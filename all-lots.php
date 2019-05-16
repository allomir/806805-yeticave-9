<?php

require('inc/functions.php'); // функции
require('inc/queries.php'); // Запросы и подключение
require('inc/helpers.php'); // шаблонизатор

require('inc/general.php'); // Общие сценарии всех страниц 

/* Страница категорий */

$items = []; // массив с данными лотов.
$checkCategory = []; // массив с данными выбранной категории.

if (isset($_GET['categoryID'])) {
    $saveCategoryID = mysqli_real_escape_string($conn, $_GET['categoryID']); // Защита от SQL-инъкция - экранирование
    // $saveCategoryID = intval($_GET['itemID']); // Защита от SQL-инъкция (вариант 2) - приведение к числу

    $items = getItemsByCategory($conn, $saveCategoryID);
    $checkCategory = checkCategoryByID($conn, $saveCategoryID); // Проверка существ. Id, массив с категорией
}

// Закрытие подключения к БД
mysqli_close($conn);

/* Шаблонизация - подключение шаблонов */

// Категория есть, но в ней отсутствуют лоты
if (empty($items) && !empty($checkCategory)) {
    $page_name = $checkCategory['name'];
    $page_content = include_template('all-lots.php', [
        'categories' => $categories,
]);
}
// В категории есть лоты
elseif (!empty($items)) {
    $page_name = $items['0']['category'];
    $page_content = include_template('all-lots.php', [
        'categories' => $categories, 
        'items' => $items
    ]);
}
// Такого id категории нет или нет параметра запроса
else {
    $page_name = '404 Страница не найдена';
    $response_code = http_response_code(404);
    $page_content = include_template('error.php', [
        'categories' => $categories,
        'page_error' => '404'
    ]);
}


/* Шаблонизация - подключение подложики */

$layout_content = include_template('layout.php', [
    'categories' => $categories, 
    'content' => $page_content, 
    'title' => $page_name,
    'page_style_main' => ''
]);

$response_code;
print($layout_content);