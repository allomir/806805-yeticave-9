<?php

require('inc/functions.php'); // функции
require('inc/queries.php'); // Запросы и подключение
require('inc/helpers.php'); // шаблонизатор

require('inc/general.php'); // Общие сценарии всех страниц 

$user = $_SESSION['user'] ?? [];

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