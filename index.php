<?php

require_once __DIR__ . "/vendor/autoload.php";

$loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/templates');
$twig = new \Twig\Environment($loader, [
    'cache' => false,
]);

$currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$cities = [
    "city1" => [
        "name" => "Сочи",
        "img" => "/static/img/city/sochi.jpeg",
        "url" => "sochi"
    ],
    "city2" => [
        "name" => "Казань",
        "img" => "/static/img/city/kazan.jpg",
        "url" => "kazan"
    ],
    "city3" => [
        "name" => "Новосибирск",
        "img" => "/static/img/city/novosibirsk.jpg",
        "url" => "novosibirsk"
    ],
    "city4" => [
        "name" => "Калининград",
        "img" => "/static/img/city/kaliningrad.jpeg",
        "url" => "kaliningrad"
    ],
];

if ($currentPath == '/') {
    echo $twig->render('pages/home.twig', ["cities" => $cities]);
} else if ($currentPath == '/about') {
    echo $twig->render('pages/about.twig', []);
} else if ($currentPath == '/contact') {
    echo $twig->render('pages/contact.twig', []);
} else if ($currentPath == '/faq') {
    echo $twig->render('pages/faq.twig', []);
} else if ($currentPath == '/news') {
    echo $twig->render('pages/news.twig', []);
} else if ($currentPath == '/rules') {
    echo $twig->render('pages/rules.twig', []);
} else if ($currentPath == '/shop') {
    echo $twig->render('pages/shop.twig', []);
} else {
    echo $twig->render('pages/404.twig', []);
}