<?php

require('inc/function.php'); // функции
require('helpers.php'); // шаблонизатор

$conn = getConn(); // Подключение к БД
$categories = getCategories($conn); // Запрос Показать Таблицу Категории

// Главная стр - Запрос показать активные лоты (врямя окончания не вышло), сортировать от последнего добавленного

$sql = "SELECT items.*, categories.name AS category, symbol FROM items 
    JOIN categories ON items.category_id = categories.id
    WHERE items.ts_end > CURRENT_TIMESTAMP 
    ORDER BY ts_add DESC 
    LIMIT 9
"; 

$result = mysqli_query($conn, $sql);
if (!$result) {
    print("Ошибка MySQL: " . mysqli_error($conn)); 
}
$items = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Закрытие подключения к БД
mysqli_close($conn);

/* Шаблонизация - подключение подложики */

$page_name = 'Главная';

$page_content = include_template('index.php', [
    'categories' => $categories, 
    'items' => $items
]);

$layout_content = include_template('layout.php', [
    'is_auth' => $is_auth,
    'categories' => $categories, 
    'content' => $page_content, 
    'user_name' => $user_name, 
    'title' => $page_name,
    'response_code' => $response_code
]);

print($layout_content);
