<?php

require('inc/functions.php'); // функции
require('inc/queries.php'); // Запросы и подключение
require('inc/helpers.php'); // шаблонизатор

require('inc/general.php'); // Общие сценарии всех страниц 

$user = $_SESSION['user'] ?? []; // Аунтификация пользователя

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
    $page_content = include_template('error.php', [
        'categories' => $categories,
        'page_error' => '403'
    ]);
}

// Подложка
$layout_content = include_template('layout.php', [
    'categories' => $categories, 
    'content' => $page_content, 
    'title' => 'Вход на сайт',
    'page_style_main' => ''
]);

$response_code;
print($layout_content);