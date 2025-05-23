<?php
require_once __DIR__ . "/db.php";

try {
    $pdo = new PDO(
        "mysql:host={$db['host']};dbname={$db['name']};charset=utf8mb4",
        $db['user'],
        $db['password'],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    // Читаем SQL файл
    $sql = file_get_contents(__DIR__ . '/database.sql');

    // Выполняем SQL
    $pdo->exec($sql);

    echo "База данных успешно инициализирована\n";
} catch (PDOException $e) {
    die("Ошибка инициализации базы данных: " . $e->getMessage() . "\n");
}
