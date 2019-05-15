<?php

require('inc/functions.php'); // функции
require('inc/queries.php'); // Запросы и подключение
require('inc/helpers.php'); // шаблонизатор

require('inc/general.php'); // Общие сценарии всех страниц 

require('inc/getwinner.php'); // специал. сценарий главной стр. - Определение победителя
require('vendor/autoload.php'); // composer - подключение SwiftMailer

/* Главная страница */

$items = getItems($conn); // #1 Запрос - показать активные лоты, 9 шт
mysqli_close($conn); // закрыть подключение БД

$page_content = include_template('index.php', [
    'categories' => $categories, 
    'items' => $items
]);

$layout_content = include_template('layout.php', [
    'categories' => $categories, 
    'content' => $page_content, 
    'title' => 'Главная',
    'page_style_main' => 'container'

]);

print($layout_content);
