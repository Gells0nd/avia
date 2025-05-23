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
    '/profile' => ['pages/profile.twig'],
    '/orders' => ['pages/orders.twig'],
    '/api/search-news' => ['handler'],
    '/api/buy-ticket' => ['handler'],
];

$currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Проверяем маршрут для отмены заказа
if (preg_match('/^\/api\/orders\/(\d+)\/cancel$/', $currentPath)) {
    require_once __DIR__ . "/handlers/cancel-order.php";
    exit;
}

// Проверяем маршрут для отдельной новости
if (preg_match('/^\/news\/(\d+)$/', $currentPath, $matches)) {
    $newsId = (int)$matches[1];
    $newsItem = null;

    // Ищем новость по ID
    foreach ($news as $item) {
        if ($item['id'] === $newsId) {
            $newsItem = $item;
            break;
        }
    }

    if ($newsItem) {
        echo $twig->render('pages/news-detail.twig', array_merge($commonData, ['news' => $newsItem]));
        exit;
    }
}

if (array_key_exists($currentPath, $routes)) {
    $route = $routes[$currentPath];

    if ($route[0] === 'handler') {
        if ($currentPath === '/api/search-news') {
            require_once __DIR__ . "/handlers/search-news.php";
        } elseif ($currentPath === '/api/buy-ticket') {
            require_once __DIR__ . "/handlers/buy-ticket.php";
        } else {
            require_once __DIR__ . "/handlers/logout.php";
        }
        exit;
    }

    // Проверяем авторизацию для защищенных маршрутов
    $protectedRoutes = ['/profile', '/orders'];
    if (in_array($currentPath, $protectedRoutes) && !isset($_SESSION['user'])) {
        header('Location: /login');
        exit;
    }

    $template = $route[0];
    $data = $route[1] ?? [];

    // Если это страница заказов, получаем заказы пользователя
    if ($currentPath === '/orders' && isset($_SESSION['user'])) {
        $pdo = new PDO(
            "mysql:host={$db['host']};dbname={$db['name']};charset=utf8mb4",
            $db['user'],
            $db['password'],
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );

        // Сначала получим структуру таблицы flights
        $stmt = $pdo->query("DESCRIBE flights");
        $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);

        // Определим правильное название поля даты
        $dateField = in_array('departure_date', $columns) ? 'departure_date' : 'depart_time';

        $stmt = $pdo->prepare("
            SELECT o.*, f.from_city as flight_from, f.to_city as flight_to, f.{$dateField} as departure_date
            FROM orders o
            JOIN flights f ON o.flight_id = f.id
            WHERE o.user_id = ?
            ORDER BY o.created_at DESC
        ");
        $stmt->execute([$_SESSION['user']['id']]);
        $data['orders'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    echo $twig->render($template, array_merge($commonData, $data));
} else {
    // 404
    echo $twig->render('pages/404.twig', $commonData);
}
