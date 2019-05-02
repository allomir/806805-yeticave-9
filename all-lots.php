<?php

// faq.php - удаленные промежуточные данные заданий.
require('function.php'); // функции
require('helpers.php'); // шаблонизатор

/* Подключение к БД и запросы */

$conn = mysqli_connect("localhost", "root", "", "yeticave");
mysqli_set_charset($conn, "utf8"); // первым делом кодировка

if ($conn == false) {
    print("Ошибка: Невозможно подключиться к MySQL " . mysqli_connect_error());
}

/* 1часть. Извлечение категорий из таблицы без защиты от sql-инъекции*/

$sql = 'SELECT * FROM categories'; 
$result = mysqli_query($conn, $sql);
if (!$result) {
    $error = mysqli_error($conn);
    print("Ошибка MySQL: " . $error);
}

$categories = mysqli_fetch_all($result, MYSQLI_ASSOC); 

/* 2часть. Извлечение лотов из таблицы */
// запрос значений для лотов, активных (не закрытый), в выбранной категории без защиты от sql-инъекции, тк нет переменных

$sql = "SELECT items.*, title AS category, symbol FROM items 
    JOIN categories ON items.category = categories.id
    WHERE items.ts_end > CURRENT_TIMESTAMP 
    AND  categories.title = 'Доски и лыжи'
    ORDER BY ts_add DESC 
"; 

$result = mysqli_query($conn, $sql);
if (!$result) {
    $error = mysqli_error($conn);
    print("Ошибка MySQL: " . $error);
}

$items = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Закрытие подключения к БД
mysqli_close($conn);

$page_content = include_template('all-lots.php', [
    'categories' => $categories, 
    'items' => $items
]);

$layout_content = include_template('layout.php', [
    'is_auth' => $is_auth,
    'categories' => $categories, 
    'content' => $page_content, 
    'user_name' => $user_name, 
    'title' => 'Главная',
    'response_code' => $response_code
]);

print($layout_content);
