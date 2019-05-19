<?php

require 'inc/functions.php'; // функции и шаблонизатор
require 'inc/queries.php'; // Запросы и подключение
require 'inc/general.php'; // Общие сценарии всех страниц 

if (isset($_GET['search'])) {

    $search = trim($_GET['search']);
    $save_search = mysqli_real_escape_string($conn, $search);
    $page = $_GET['page'] ?? 1;
    $limit = $_GET['limit'] ?? 6; 
    $num_items = findItemsByFText($conn, $save_search, 0); // вернет общее число строк если указать 3-й параметр = 0
    $num_pages = ceil($num_items / $limit);
    $items = findItemsByFText($conn, $save_search, $page, $limit); // вернет 6 строк (лимит) или [] 

    $page_content = include_template(
        'search.php', 
        [
        'categories' => $categories,
        'items' => $items,
        'num_pages' => $num_pages,
        'search' => $search
        ]
    );
}
else {
    $page_content = include_template(
        'search.php', 
        [
        'categories' => $categories
        ]
    );
}

// Подложка
$layout_content = include_template(
    'layout.php', 
    [
    'categories' => $categories,
    'content' => $page_content,
    'title' => 'Поиск информации о лотах'
    ]
);

print($layout_content);