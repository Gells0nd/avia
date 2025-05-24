<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Включаем логирование ошибок
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error.log');

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: PUT, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . "/../data.php";

session_start();

// Логируем входящие данные
error_log("Request method: " . $_SERVER['REQUEST_METHOD']);
error_log("Session data: " . print_r($_SESSION, true));

// Проверяем авторизацию
if (!isset($_SESSION['user'])) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => 'Необходимо войти в систему'
    ]);
    exit;
}

// Получаем данные из тела запроса
$input = file_get_contents('php://input');
error_log("Raw input: " . $input);

$data = json_decode($input, true);
error_log("Decoded data: " . print_r($data, true));

if (!$data) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Неверный формат данных'
    ]);
    exit;
}

// Валидация данных
$name = trim($data['name'] ?? '');
$email = trim($data['email'] ?? '');
$password = $data['password'] ?? '';

error_log("Processed data - name: $name, email: $email, has password: " . (!empty($password) ? 'yes' : 'no'));
error_log("User ID from session: " . $_SESSION['user']['id']);

if (empty($name) || empty($email)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Имя и email обязательны для заполнения'
    ]);
    exit;
}

try {
    $pdo = new PDO(
        "mysql:host={$db['host']};dbname={$db['name']};charset=utf8mb4",
        $db['user'],
        $db['password'],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    error_log("Database connection successful");

    // Проверяем существование пользователя
    $stmt = $pdo->prepare("SELECT id FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user']['id']]);
    if (!$stmt->fetch()) {
        error_log("User not found in database: " . $_SESSION['user']['id']);
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'Пользователь не найден'
        ]);
        exit;
    }

    // Проверяем уникальность email
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
    $stmt->execute([$email, $_SESSION['user']['id']]);

    if ($stmt->fetch()) {
        error_log("Email already in use: $email");
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Этот email уже используется'
        ]);
        exit;
    }

    // Разбиваем имя на части
    $nameParts = explode(' ', $name);
    $firstname = $nameParts[0];
    $lastname = count($nameParts) > 1 ? $nameParts[1] : '';
    error_log("Split name - firstname: $firstname, lastname: $lastname");

    // Формируем SQL запрос
    $sql = "UPDATE users SET firstname = ?, lastname = ?, email = ?";
    $params = [$firstname, $lastname, $email];

    if (!empty($password)) {
        if (strlen($password) < 6) {
            error_log("Password too short");
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Пароль должен быть не менее 6 символов'
            ]);
            exit;
        }
        $sql .= ", password = ?";
        $params[] = password_hash($password, PASSWORD_DEFAULT);
    }

    $sql .= " WHERE id = ?";
    $params[] = $_SESSION['user']['id'];

    error_log("SQL query: $sql");
    error_log("Parameters: " . print_r($params, true));

    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute($params);
    error_log("Query execution result: " . ($result ? 'success' : 'failed'));

    if ($result) {
        // Обновляем данные в сессии
        $_SESSION['user']['name'] = $name;
        $_SESSION['user']['email'] = $email;
        error_log("Session updated successfully");

        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => 'Данные успешно обновлены',
            'user' => [
                'id' => $_SESSION['user']['id'],
                'name' => $name,
                'email' => $email
            ]
        ]);
    } else {
        error_log("Update failed but no exception thrown");
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Не удалось обновить данные'
        ]);
    }
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    error_log("SQL State: " . $e->getCode());
    error_log("Error Info: " . print_r($e->errorInfo, true));
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Ошибка при обновлении данных'
    ]);
} catch (Exception $e) {
    error_log("General error: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Произошла ошибка'
    ]);
}
