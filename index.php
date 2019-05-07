<?php

require('inc/function.php'); // функции
require('inc/queries.php'); // Запросы и подключение
require('helpers.php'); // шаблонизатор

$conn = getConn(); // Подключение к БД
$categories = getCategories($conn); // Запрос Показать Таблицу Категории
$items = getItems($conn); // Главная стр показать активные лоты, до 9 шт
mysqli_close($conn);

/* Шаблонизация */

$page_name = 'Главная';

$page_content = include_template('index.php', [
    'categories' => $categories, 
    'items' => $items
]);

$layout_content = include_template('layout.php', [
    'is_auth' => $is_auth,
    'user_name' => $user_name, 
    'categories' => $categories, 
    'content' => $page_content, 
    'title' => $page_name,
    'response_code' => $response_code,
    'page_style_main' => 'container'

]);

print($layout_content);
