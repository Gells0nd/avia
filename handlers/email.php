<?php

global $db;
session_start();

require_once __DIR__ . '/../config/db.php';

// Получаем email из формы
$email = trim($_POST["email"] ?? '');

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header("Location: /?subscribe_error=1");
    exit;
}

$userId = $_SESSION['user']['id'] ?? 'anonymous';

// Подключение к БД
$pdo = new PDO(
    "mysql:host={$db['host']};dbname={$db['name']};charset=utf8mb4",
    $db['user'],
    $db['password'],
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

$stmt = $pdo->prepare("INSERT INTO subscribers (user_id, email) VALUES (?, ?)");
$stmt->execute([$userId, $email]);

header("Location: /?subscribed=1");
exit;
