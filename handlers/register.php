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

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = trim($_POST['first_name'] ?? '');
    $lastName = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if ($password !== $confirmPassword) {
        $errors[] = "Пароли не совпадают.";
    }

    $pdo = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUser, $dbPassword);
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        $errors[] = "Пользователь с такой почтой уже существует";
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("INSERT INTO users (firstname, lastname, email, password, role) VALUES (?, ?, ?, ?, ?)");
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt->execute([$firstName, $lastName, $email, $hashedPassword, 'user']);

        header("Location: /login");
        exit;
    }
}

echo $twig->render('auth/register.twig', [
    'errors' => $errors
]);
