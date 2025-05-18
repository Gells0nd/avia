<?php

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
