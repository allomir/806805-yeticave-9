<?php

require 'inc/functions.php'; // функции и шаблонизатор
require 'inc/queries.php'; // Запросы и подключение
require 'inc/general.php'; // Общие сценарии всех страниц 

/* Страница категорий */

if (isset($_GET['category_id'])) {
    $save_category_id = mysqli_real_escape_string($conn, $_GET['category_id']); // Защита от SQL-инъкция - экранирование
    $page = $_GET['page'] ?? 1;
    $limit = $_GET['limit'] ?? 9; 
    $num_items = count(getItemsByCategory($conn, $save_category_id)); // вернет общий массив если не указывать 3й(лимит) и 4й(стр) параметры или []
    $num_pages = ceil($num_items / $limit);
    $items = getItemsByCategory($conn, $save_category_id, $limit, $page); // вернет массив 9 строк (лимит) или [] 
    $check_category = checkCategoryByID($conn, $save_category_id); // Проверка существ. id, массив с категорией
}

// Закрытие подключения к БД
mysqli_close($conn);

/* Шаблонизация - подключение шаблонов */

// Категория есть, но в ней отсутствуют лоты
if (empty($items) && !empty($check_category)) {
    $page_name = $category_name = $check_category['name'];
    $page_content = include_template(
        'all-lots.php', 
        [
        'categories' => $categories,
        'category_name' => $category_name,
        'num_pages' => $num_pages
        ]
    );
}
// В категории есть лоты
elseif (!empty($items)) {
    $page_name = $items['0']['category'];
    $page_content = include_template(
        'all-lots.php', 
        [
        'categories' => $categories, 
        'items' => $items,
        'num_pages' => $num_pages
        ]
    );
}
// Такого id категории нет или нет параметра запроса
else {
    $page_name = '404 Страница не найдена';
    $response_code = http_response_code(404);
    $page_content = include_template(
        'error.php', 
        [
        'categories' => $categories,
        'page_error' => '404'
        ]
    );
}

// Подложка
$layout_content = include_template(
    'layout.php', 
    [
    'categories' => $categories, 
    'content' => $page_content, 
    'title' => $page_name
    ]
);

$response_code;
print($layout_content);