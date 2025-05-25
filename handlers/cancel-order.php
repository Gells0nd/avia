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

// Проверяем авторизацию
if (!isset($_SESSION['user'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Необходимо войти в систему'
    ]);
    exit;
}

// Получаем ID заказа из URL
$uri = $_SERVER['REQUEST_URI'];
preg_match('/\/api\/orders\/(\d+)\/cancel/', $uri, $matches);
$orderId = $matches[1] ?? null;

if (!$orderId) {
    echo json_encode([
        'success' => false,
        'message' => 'Неверный ID заказа'
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

    // Отменяем заказ
    $stmt = $pdo->prepare("
        UPDATE orders 
        SET status = 'cancelled' 
        WHERE id = ? AND user_id = ?
    ");

    $stmt->execute([$orderId, $_SESSION['user']['id']]);

    // Возвращаем место в рейс
    $stmt = $pdo->prepare("
        UPDATE flights f
        JOIN orders o ON f.id = o.flight_id
        SET f.places_left = f.places_left + 1
        WHERE o.id = ? AND o.user_id = ?
    ");

    $stmt->execute([$orderId, $_SESSION['user']['id']]);

    echo json_encode([
        'success' => true,
        'message' => 'Заказ успешно отменен'
    ]);

    header('Location: /orders');
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Ошибка при отмене заказа'
    ]);

    header('Location: /orders');
}
