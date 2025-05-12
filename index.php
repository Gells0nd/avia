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

$news = [
    [
        'title' => 'Весенняя распродажа: билеты со скидкой до 50%',
        'date' => '2025-05-01',
        'content' => 'Мы запускаем весеннюю распродажу! Специальные предложения на все рейсы до конца мая.',
    ],
    [
        'title' => 'Новый рейс: Казань – Тбилиси уже с июня!',
        'date' => '2025-05-10',
        'content' => 'С радостью сообщаем, что с июня запускаем новый рейс из Казани в Тбилиси. Билеты уже в продаже.',
    ],
    [
        'title' => 'Бесплатный багаж в июле на всех рейсах внутри РФ',
        'date' => '2025-05-15',
        'content' => 'Все билеты на рейсы внутри России в июле будут включать бесплатный багаж до 20 кг.',
    ],
];

$faq = [
    [
        'question' => 'Как изменить или отменить бронирование?',
        'answer' => 'Вы можете отменить или изменить свое бронирование в личном кабинете или через службу поддержки.',
    ],
    [
        'question' => 'Можно ли провозить животных?',
        'answer' => 'Да, мы разрешаем провозить домашних животных. Ознакомьтесь с правилами перевозки на странице "Правила авиаперевозок".',
    ],
    [
        'question' => 'Какие способы оплаты доступны?',
        'answer' => 'Мы принимаем все основные банковские карты, а также оплату через электронные кошельки и мобильные приложения.',
    ],
];

$rules = [
    'title' => 'Правила авиаперевозок',
    'content' => 'Наши правила перевозки включают информацию о багажных нормах, возврате билетов, перевозке животных, правилах безопасности и многих других аспектах, которые делают ваш полет комфортным и безопасным.',
];

$about = [
    'title' => 'О компании Avia',
    'content' => 'Avia — крупнейшая российская авиакомпания, работающая на более чем 500 направлениях по России, СНГ и Европе. Мы предлагаем самые выгодные тарифы, высокое качество обслуживания и гибкие условия для наших пассажиров.',
];

$flights = [
    [
        'flight_number' => 'SU1234',
        'from_city' => 'Москва',
        'to_city' => 'Сочи',
        'price' => 2990,
        'depart_time' => '2025-06-10 10:00',
        'arrive_time' => '2025-06-10 12:30',
        'aircraft' => 'Airbus A320',
        'duration' => '2 ч 30 мин',
        'places_left' => 30,
        'id' => 1,
    ],
    [
        'flight_number' => 'SU2345',
        'from_city' => 'Москва',
        'to_city' => 'Казань',
        'price' => 3490,
        'depart_time' => '2025-06-12 14:00',
        'arrive_time' => '2025-06-12 16:00',
        'aircraft' => 'Boeing 737',
        'duration' => '2 ч 00 мин',
        'places_left' => 25,
        'id' => 2,
    ],
    [
        'flight_number' => 'MA2321',
        'from_city' => 'Москва',
        'to_city' => 'Калифорния',
        'price' => 72999,
        'depart_time' => '2025-06-12 14:00',
        'arrive_time' => '2025-06-12 16:00',
        'aircraft' => 'Boeing 737',
        'duration' => '9 ч 30 мин',
        'places_left' => 13,
        'id' => 3,
    ],
];

if ($currentPath == '/') {
    echo $twig->render('pages/home.twig', ["cities" => $cities]);
} else if ($currentPath == '/about') {
    echo $twig->render('pages/about.twig', $about);
} else if ($currentPath == '/contact') {
    echo $twig->render('pages/contact.twig', []);
} else if ($currentPath == '/faq') {
    echo $twig->render('pages/faq.twig', ['faq' => $faq]);
} else if ($currentPath == '/news') {
    echo $twig->render('pages/news.twig', ['news' => $news]);
} else if ($currentPath == '/rules') {
    echo $twig->render('pages/rules.twig', $rules);
} else if ($currentPath == '/shop') {
    echo $twig->render('pages/shop.twig', ['flights' => $flights]);
} else if ($currentPath == '/register') {
    echo $twig->render('auth/register.twig');
} else if ($currentPath == '/login') {
    echo $twig->render('auth/login.twig');
} else {
    echo $twig->render('pages/404.twig', []);
}
