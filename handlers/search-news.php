<?php
require_once __DIR__ . "/../vendor/autoload.php";
require_once __DIR__ . "/../data.php";

header('Content-Type: application/json');

$query = $_GET['query'] ?? '';
$query = trim($query);

if (empty($query)) {
    echo json_encode(['news' => $news]);
    exit;
}

$filteredNews = array_filter($news, function ($item) use ($query) {
    $searchText = mb_strtolower($query);
    $title = mb_strtolower($item['title']);
    $content = mb_strtolower($item['content']);

    return strpos($title, $searchText) !== false ||
        strpos($content, $searchText) !== false;
});

echo json_encode(['news' => array_values($filteredNews)]);
