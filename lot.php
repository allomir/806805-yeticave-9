<?php

require('inc/function.php'); // функции
require('inc/queries.php'); // Запросы и подключение
require('helpers.php'); // шаблонизатор

$conn = getConn(); // Подключение к БД
$categories = getCategories($conn); // Запрос Показать Таблицу Категории

/* Страница лота. Получение id лота из параметра запроса GET */

if (isset($_GET['itemID'])) {
    $saveItemID = mysqli_real_escape_string($conn, $_GET['itemID']); // Защита от SQL-инъкция - экранирование
    // $safe_item_id = intval($_GET['itemID']); // Защита от SQL-инъкция (вариант 2) - приведение к числу

    $item = getItemsByID($conn, $saveItemID); // Запрос элемента из БД таблицы по id, массив или 0 
}

// Закрытие подключения к БД
mysqli_close($conn);

/* Шаблонизация - подключение шаблонов */
    
if(!$item OR !isset($_GET['itemID'])){
    $page_name = '404 Страница не найдена';
    $response_code = http_response_code(404);
    $page_content = include_template('error.php', [
            'categories' => $categories
    ]);
}
else {
    $page_name = $item['name'];
    $page_content = include_template('lot.php', [
            'categories' => $categories, 
            'item' => $item
    ]);
}

/* Шаблонизация - подключение подложики */

$layout_content = include_template('layout.php', [
    'is_auth' => $is_auth,
    'categories' => $categories, 
    'content' => $page_content, 
    'user_name' => $user_name, 
    'title' => $page_name,
    'response_code' => $response_code
]);

print($layout_content);
