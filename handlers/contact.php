<?php

/*
 * Молитва PHP-разработчика
 * Да не падет сайт мой в 500 й ошибке,
 * Да не сокрушит его SQL-инъекция лютая,
 * Да минует его злой баг в полночь,
 * Да не будет белого экрана смерти.
 * 
 * Ежеси на небеси, сохрани код мой от кривых рук,
 * Осени его благодатью try-catch,
 * Укрепи его святыми header'ами,
 * И да не сломается он при апдейте.
 * 
 * Аминь.
 */

global $db;
session_start();

require_once __DIR__ . '/../config/db.php';

$userId = $_SESSION['user']['id'] ?? 'anonymous';
$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$theme = trim($_POST['theme'] ?? '');
$message = trim($_POST['message'] ?? '');

$pdo = new PDO(
    "mysql:host={$db['host']};dbname={$db['name']};charset=utf8mb4",
    $db['user'],
    $db['password'],
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

$stmt = $pdo->prepare("INSERT INTO messages (user_id, name, email, theme, message) VALUES (?, ?, ?, ?, ?)");
$stmt->execute([$userId, $name, $email, $theme, $message]);

header("Location: /contact");
exit;