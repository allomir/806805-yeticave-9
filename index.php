<?php

require 'inc/functions.php'; // функции и шаблонизатор
require 'inc/queries.php'; // Запросы и подключение

require 'inc/general.php'; // Общие сценарии всех страниц

require 'vendor/autoload.php'; // composer - подключение SwiftMailer

/* Главная страница */

$items = getItems($conn); // #1 Запрос - показать активные лоты, 9 шт

$page_content = include_template(
    'index.php', [
    'categories' => $categories,
    'items' => $items
    ]
);

$layout_content = include_template(
    'layout.php', [
    'categories' => $categories,
    'content' => $page_content,
    'title' => 'Главная',
    'page_style_main' => 'container'
    ]
);

print($layout_content);

require 'inc/getwinner.php'; // специал. сценарий главной стр. - Определение победителя
