<?php

require('inc/function.php'); // функции
require('helpers.php'); // шаблонизатор

$conn = getConn();
$categories = getCategories($conn);

/* Получение элементов по id из параметра запроса */

if (isset($_GET['itemID'])) {
    // $safe_item_id = intval($_GET['itemID']); // Защита от SQL-инъкция, приведение к числу
    $safe_item_id = mysqli_real_escape_string($conn, $_GET['itemID']); // Защита от SQL-инъкция, экранирование

    /* Запрос элемента из БД таблицы по id */

    $sql = "SELECT items.*, categories.name AS category FROM items
        JOIN categories ON items.category_id = categories.id
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
        $response_code = http_response_code(404);
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
    $response_code = http_response_code(404);
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
    'title' => $page_name,
    'response_code' => $response_code
]);

print($layout_content);
