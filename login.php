<?php

require('inc/function.php'); // функции, response_code, is_auth
require('inc/queries.php'); // Запросы и подключение
require('helpers.php'); // шаблонизатор

$conn = getConn(); // Подключение к БД
$categories = getCategories($conn); // Запрос Показать Таблицу Категории

/* Шаблонизатор */

$page_name = 'Вход на сайт';

$page_content = include_template('login.php', [
    'categories' => $categories
]);

$layout_content = include_template('layout.php', [
    'is_auth' => $is_auth,
    'user_name' => $user_name, 
    'categories' => $categories, 
    'content' => $page_content, 
    'title' => $page_name,
    'page_style_main' => ''
]);

print($layout_content);