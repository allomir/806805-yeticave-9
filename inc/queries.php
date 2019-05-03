<?php

/* Общее подключение к БД */

function getConn() {
    $conn = mysqli_connect("localhost", "root", "", "yeticave");
    mysqli_set_charset($conn, "utf8"); // первым делом кодировка
    if (!$conn) {
        print("Ошибка: Невозможно подключиться к MySQL " . mysqli_connect_error()); 
        // die('Ошибка при подключении: ' . mysqli_connect_error()); // вариант 2.
    }
    return $conn;
}

/* Общий запрос категорий из БД таблицы без защиты от sql-инъекции, тк нет переменных */

function getCategories($conn) {
    $sql = 'SELECT symbol, name FROM categories'; 
    $result = mysqli_query($conn, $sql);
    if (!$result) {
        print("Ошибка MySQL: " . mysqli_error($conn)); 
    }
    return mysqli_fetch_all($result, MYSQLI_ASSOC); 
}

// Главная стр. Запрос показать активные лоты (врямя окончания не вышло), сортировать от последнего добавленного, не более 9

function getItems($conn) {
    $sql = "SELECT items.*, categories.name AS category, symbol FROM items 
        JOIN categories ON items.category_id = categories.id
        WHERE ts_end > CURRENT_TIMESTAMP 
        ORDER BY ts_add DESC 
        LIMIT 9
    "; 
    $result = mysqli_query($conn, $sql);
    if (!$result) {
        print("Ошибка MySQL: " . mysqli_error($conn)); 
    }
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

// Страница Лот. Запрос показать данные одного лота по id или вернуть 0

function getItemsByID($conn, $itemID) {
    $sql = "SELECT items.*, categories.name AS category FROM items
        JOIN categories ON items.category_id = categories.id
        WHERE items.id = '$itemID' AND ts_end > CURRENT_TIMESTAMP 
    ";
    $result = mysqli_query($conn, $sql);
    if (!$result) {
        print("Ошибка MySQL: " . mysqli_error($conn)); 
    }
    if(mysqli_num_rows($result)) {
        $item = mysqli_fetch_assoc($result); // Ассоциативный массив 
    } else {$item = 0;} // Число

    return $item;
}