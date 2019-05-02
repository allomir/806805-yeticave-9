<?php

// faq.php - удаленные промежуточные данные заданий.
require('function.php'); // функции
require('helpers.php'); // шаблонизатор

/* Извлечение лотов из таблицы */
// запрос значений для лотов, активных (не закрытый) без защиты от sql-инъекции, тк нет переменных

$sql = "SELECT items.*, title AS category, symbol FROM items 
    JOIN categories ON items.category = categories.id
    WHERE items.ts_end > CURRENT_TIMESTAMP /* проверяем что лот не закрыт */
    ORDER BY ts_add DESC 
    LIMIT 9
"; 

$result = mysqli_query($conn, $sql);
if (!$result) {
    $error = mysqli_error($conn);
    print("Ошибка MySQL: " . $error);
}

$items = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Закрытие подключения к БД
mysqli_close($conn);

$page_content = include_template('index.php', [
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
