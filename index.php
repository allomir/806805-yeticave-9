<?php

// faq.php - удаленные промежуточные данные заданий.
require('inc/function.php'); // функции
require('helpers.php'); // шаблонизатор

// Подключение к БД
$conn = getConn();
if (!$conn) {
    $page_name = 'Ошибка MySQL';
    $error = "Ошибка: Невозможно подключиться к MySQL " . mysqli_connect_error();
    $page_content = include_template('error.php', [
        'error' => $error
    ]);
}

// Запрос Показать Таблицу Категории
$result = getCategories($conn);
if ($result) {
    $categories = mysqli_fetch_all($result, MYSQLI_ASSOC);
}
else {
    $page_name = 'Ошибка MySQL';
    $error = "Ошибка MySQL: " . mysqli_error($conn);
    $page_content = include_template('error.php', [
        'error' => $error
    ]);
}

// Главная стр - Запрос показать активные лоты (врямя окончания не вышло), сортировать от последнего добавленного

$sql = "SELECT items.*, categories.name AS category, symbol FROM items 
    JOIN categories ON items.category_id = categories.id
    WHERE items.ts_end > CURRENT_TIMESTAMP 
    ORDER BY ts_add DESC 
    LIMIT 9
"; 

$result = mysqli_query($conn, $sql);
if ($result) {
    $page_name = 'Главная';
    $items = mysqli_fetch_all($result, MYSQLI_ASSOC);
    $page_content = include_template('index.php', [
        'categories' => $categories, 
        'items' => $items
    ]);
}
else {
    $page_name = 'Ошибка MySQL';
    $error = "Ошибка MySQL: " . mysqli_error($conn);
    $page_content = include_template('error.php', [
        'error' => $error
    ]);
}

// Закрытие подключения к БД
mysqli_close($conn);

// Подложка

$layout_content = include_template('layout.php', [
    'is_auth' => $is_auth,
    'categories' => $categories, 
    'content' => $page_content, 
    'user_name' => $user_name, 
    'title' => $page_name,
    'response_code' => $response_code
]);

print($layout_content);
