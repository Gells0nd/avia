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
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/db.php';

$dbHost = $db['host'];
$dbUser = $db['user'];
$dbPassword = $db['password'];
$dbName = $db['name'];

$loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/../templates');
$twig = new \Twig\Environment($loader);

session_start();

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Проверка в базе
    $pdo = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUser, $dbPassword);
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        // Авторизация успешна
        $_SESSION['user'] = [
            'id' => $user['id'],
            'email' => $user['email'],
            'role' => $user['role'],
            'name' => $user['firstname'] . ' ' . $user['lastname'],
        ];
        header("Location: /"); // редирект на главную или профиль
        exit;
    } else {
        $error = "Неверный email или пароль.";
    }
}

echo $twig->render('auth/login.twig', [
    'error' => $error
]);
