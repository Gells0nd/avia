<?php

global $cities, $news, $faq, $about, $flights;
require_once __DIR__ . "/vendor/autoload.php";
require_once "data.php";

session_start();

$loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/templates');
$twig = new \Twig\Environment($loader, ['cache' => false]);

$commonData = [
    'user' => $_SESSION['user'] ?? null,
];

// Карта маршрутов: путь => [шаблон, дополнительные данные]
$routes = [
    '/' => ['pages/home.twig', ['cities' => $cities]],
    '/about' => ['pages/about.twig', ['about' => $about]],
    '/contact' => ['pages/contact.twig'],
    '/faq' => ['pages/faq.twig', ['faq' => $faq]],
    '/news' => ['pages/news.twig', ['news' => $news]],
    '/rules' => ['pages/rules.twig'],
    '/shop' => ['pages/shop.twig', ['flights' => $flights]],
    '/register' => ['auth/register.twig'],
    '/login' => ['auth/login.twig'],
    '/logout' => ['handler'],
];

$currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if (array_key_exists($currentPath, $routes)) {
    $route = $routes[$currentPath];

    if ($route[0] === 'handler') {
        require_once __DIR__ . "/handlers/logout.php";
        exit;
    }

    $template = $route[0];
    $data = $route[1] ?? [];

    echo $twig->render($template, array_merge($commonData, $data));
} else {
    // 404
    echo $twig->render('pages/404.twig', $commonData);
}
