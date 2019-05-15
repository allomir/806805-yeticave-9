<?php

require('inc/functions.php'); // функции
require('inc/queries.php'); // Запросы и подключение
require('inc/helpers.php'); // шаблонизатор
$response_code = '';

session_start();

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
    'categories' => $categories, 
    'content' => $page_content, 
    'title' => $page_name,
    'page_style_main' => 'container'

]);

print($layout_content);
