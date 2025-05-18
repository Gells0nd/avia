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
require_once __DIR__ . '/config/db.php';

$pdo = new PDO(
    "mysql:host={$db['host']};dbname={$db['name']};charset=utf8mb4",
    $db['user'],
    $db['password'],
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

$dbCities = $pdo->query("SELECT * FROM popular_cities");
$cities = $dbCities->fetchAll(PDO::FETCH_ASSOC);

$dbNews = $pdo->query("SELECT * FROM news");
$news = $dbNews->fetchAll(PDO::FETCH_ASSOC);

$faqDb = $pdo->query("SELECT * FROM faq");
$faq = $faqDb->fetchAll(PDO::FETCH_ASSOC);

$dbAbout = $pdo->query("SELECT * FROM about");
$about = $dbAbout->fetchAll(PDO::FETCH_ASSOC);

$dbFlights = $pdo->query("SELECT * FROM flights");
$flights = $dbFlights->fetchAll(PDO::FETCH_ASSOC);
