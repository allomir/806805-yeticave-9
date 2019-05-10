<?php

require('inc/function.php'); // функции
require('inc/queries.php'); // Запросы и подключение
require('inc/helpers.php'); // шаблонизатор
$response_code = '';

session_start();
$user_name = isset($_SESSION['user']) ? $_SESSION['user']['name'] : 0;

$conn = getConn(); // Подключение к БД
$categories = getCategories($conn); // Запрос Показать Таблицу Категории

/* Страница лота. Получение id лота из параметра запроса GET */

$item = []; // массив с данными лота.

if (isset($_GET['itemID'])) {
    $saveItemID = mysqli_real_escape_string($conn, $_GET['itemID']); // Защита от SQL-инъкция - экранирование
    $item = getItemByID($conn, $saveItemID); // Запрос элемента из БД таблицы по id, массив или 0 
}

// Закрытие подключения к БД
mysqli_close($conn);

/* Шаблонизация - подключение шаблонов */

// Лот пуст (лота с таким id нет) или id лота нет (параметра запроса нет)
if(empty($item) OR empty($_GET['itemID'])){
    $page_name = '404 Страница не найдена';
    $response_code = http_response_code(404);
    $page_content = include_template('error.php', [
            'categories' => $categories
    ]);
}
// Id Лота существует и лот не пуст
else {
    $page_name = $item['name'];
    $page_content = include_template('lot.php', [
            'categories' => $categories, 
            'item' => $item
    ]);
}

/* Шаблонизация - подключение подложики */

$layout_content = include_template('layout.php', [
    'user_name' => $user_name, 
    'categories' => $categories, 
    'content' => $page_content, 
    'title' => $page_name,
    'page_style_main' => ''
]);

$response_code;
print($layout_content);
