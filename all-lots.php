<?php

require('inc/function.php'); // функции
require('inc/queries.php'); // Запросы и подключение
require('helpers.php'); // шаблонизатор

$conn = getConn(); // Подключение к БД
$categories = getCategories($conn); // Запрос Показать Таблицу Категории

/* Страница категорий. Получение id категории */

if (isset($_GET['categoryID'])) {
    $saveCategoryID = mysqli_real_escape_string($conn, $_GET['categoryID']); // Защита от SQL-инъкция - экранирование
    // $saveCategoryID = intval($_GET['itemID']); // Защита от SQL-инъкция (вариант 2) - приведение к числу

    $items = getItemsByCategory($conn, $saveCategoryID);
    $checkCategory = checkCategoryByID($conn, $saveCategoryID); // Проверка существ. Id, массив с категорией
}

// Закрытие подключения к БД
mysqli_close($conn);

/* Шаблонизация - подключение шаблонов */

if (!$items && $checkCategory) {
    $page_name = $checkCategory['name'];
    $page_content = include_template('all-lots.php', [
        'categories' => $categories,
        'items' => $items,
        'page_name' => $page_name
]);
}
elseif ($items) {
    $page_name = $items['0']['category'];
    $page_content = include_template('all-lots.php', [
        'categories' => $categories, 
        'items' => $items,
        'page_name' => $page_name
    ]);
}
else {
    $page_name = '404 Страница не найдена';
    $response_code = http_response_code(404);
    $page_content = include_template('error.php', [
            'categories' => $categories
    ]);
}


/* Шаблонизация - подключение подложики */

$layout_content = include_template('layout.php', [
    'is_auth' => $is_auth,
    'user_name' => $user_name, 
    'categories' => $categories, 
    'content' => $page_content, 
    'title' => $page_name,
    'response_code' => $response_code
]);

print($layout_content);