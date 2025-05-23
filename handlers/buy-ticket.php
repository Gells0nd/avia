<?php
// Отключаем вывод ошибок
error_reporting(0);
ini_set('display_errors', 0);

// Устанавливаем заголовки
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . "/../data.php";

session_start();

// Простая проверка авторизации
if (!isset($_SESSION['user'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Для покупки билета необходимо войти в систему'
    ]);
    exit;
}

// Получаем данные из формы
$flightId = $_POST['flight_id'] ?? null;
$passengerName = $_POST['passenger_name'] ?? null;

// Простая валидация
if (!$flightId || !$passengerName) {
    echo json_encode([
        'success' => false,
        'message' => 'Заполните все поля'
    ]);
    exit;
}

try {
    // Подключаемся к БД
    $pdo = new PDO(
        "mysql:host={$db['host']};dbname={$db['name']};charset=utf8mb4",
        $db['user'],
        $db['password']
    );

    // Создаем заказ
    $stmt = $pdo->prepare("
        INSERT INTO orders (user_id, flight_id, passenger_name, price, status, created_at)
        SELECT ?, ?, ?, price, 'active', NOW()
        FROM flights WHERE id = ?
    ");

    $stmt->execute([
        $_SESSION['user']['id'],
        $flightId,
        $passengerName,
        $flightId
    ]);

    // Уменьшаем количество мест
    $stmt = $pdo->prepare("UPDATE flights SET places_left = places_left - 1 WHERE id = ?");
    $stmt->execute([$flightId]);

    echo json_encode([
        'success' => true,
        'message' => 'Билет успешно куплен'
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Ошибка при покупке билета'
    ]);
}
