<?php

require('inc/function.php'); // функции
require('inc/queries.php'); // Запросы и подключение
require('inc/helpers.php'); // шаблонизатор
$response_code = '';

session_start();
$user = $_SESSION['user'] ?? [];

$conn = getConn(); // Подключение к БД
$categories = getCategories($conn); // Запрос Показать Таблицу Категории

/* Шаблонизация */

if (isset($_SESSION['user'])) {
    $bets = getBetsByUserID($conn, $user['id']);
    $page_name = 'Мои ставки';
    $page_content = include_template('my-bets.php', [
        'categories' => $categories, 
        'bets' => $bets
    ]);
}
else {
    $response_code = http_response_code(403);
    $page_content = '<div class="container"><h3>Ошибка доступа 403<h3></div>' ;
}

/* подложка */

$page_name = 'Вход на сайт';
$layout_content = include_template('layout.php', [
    'categories' => $categories, 
    'content' => $page_content, 
    'title' => $page_name,
    'page_style_main' => ''
]);

$response_code;
print($layout_content);