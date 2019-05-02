<?php

$is_auth = rand(0, 1);
$user_name = 'Михаил Лебедев';
require('function.php'); // функции
require('helpers.php'); // шаблонизатор

/* Подключение к БД */


$conn = mysqli_connect("localhost", "root", "", "yeticave");
// mysqli_set_charset($conn, "utf8"); // первым делом кодировка

if ($conn == false) {
    print("Ошибка: Невозможно подключиться к MySQL " . mysqli_connect_error());
}

/* 1часть. Запрос категорий из БД таблицы */

$sql = 'SELECT symbol, title FROM categories'; 
$result = mysqli_query($conn, $sql);
if (!$result) {
    $error = mysqli_error($conn);
    print("Ошибка MySQL: " . $error);
}

$categories = mysqli_fetch_all($result, MYSQLI_ASSOC);  

/* 2часть. Получение элементов по id из параметра запроса */

if (isset($_GET['itemID'])) {
    // $safe_item_id = intval($_GET['itemID']); // Защита от SQL-инъкция, приведение к числу
    $safe_item_id = mysqli_real_escape_string($conn, $_GET['itemID']); // Защита от SQL-инъкция, экранирование

    /* 3часть. Запрос элемента из БД таблицы по id */

    $sql = "SELECT items.*, title FROM items
        JOIN categories ON items.category = categories.id
        WHERE items.id = '$safe_item_id'
    ";

    $result = mysqli_query($conn, $sql);

    if (!$result) {
        $page_name = 'Ошибка MySQL';
        $error = "Ошибка MySQL: " . mysqli_error($conn);
        $page_content = include_template('error.php', [
            'error' => $error
        ]);
    }
    elseif(!mysqli_num_rows($result)){
        $page_name = '404 Страница не найдена';
        $error = '<h2>404 Страница не найдена</h2>
    <p>Данной страницы не существует на сайте.</p>';
        $page_content = include_template('error.php', [
            'error' => $error
        ]);
    }
    else {
        $item = mysqli_fetch_assoc($result);
        $page_name = $item['name'];
        $page_content = include_template('lot.php', [
            'categories' => $categories, 
            'item' => $item
        ]);
    }
}
else {
    $page_name = '404 Страница не найдена';
    $error = '<h2>404 Страница не найдена</h2>
    <p>Данной страницы не существует на сайте.</p>';
    $page_content = include_template('error.php', [
    'error' => $error
    ]);
}

mysqli_close($conn);

/* Шаблонизация - подключение подложики */

$layout_content = include_template('layout.php', [
    'is_auth' => $is_auth,
    'categories' => $categories, 
    'content' => $page_content, 
    'user_name' => $user_name, 
    'title' => $page_name
]);

print($layout_content);
