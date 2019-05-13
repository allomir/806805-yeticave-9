<?php

require('inc/function.php'); // функции
require('inc/queries.php'); // Запросы и подключение
require('inc/helpers.php'); // шаблонизатор
$response_code = '';

session_start();

$conn = getConn(); // Подключение к БД
$categories = getCategories($conn); // Запрос Показать Таблицу Категории

/* Страница лота. Получение id лота из параметра запроса GET */

$item = []; // массив с данными лота.

if (isset($_GET['itemID'])) {
    $saveItemID = mysqli_real_escape_string($conn, $_GET['itemID']); // Защита от SQL-инъкция - экранирование
    $item = getItemByID($conn, $saveItemID); // Запрос элемента из БД таблицы по id, массив или [] 
    $itemBets = !empty($item) ? getBetsByItemID($conn, $item['id']) : []; // Запрос показать ставки лота по его id, массив или [] 
    print_r($item);
}

// Закрытие подключения к БД
mysqli_close($conn);


if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $param = 'cost';
    $formVal = $_POST;
    $formErr = [$param => ''];
    $specpar = ['maxlen' => 7, 'minbet' => $item['min_bet']];


    if (empty($formVal[$param])) {
        $formErr[$param] = 'Введите ставку';
    }
    elseif (!is_numeric($formVal[$param])) {
        $formErr[$param] = 'Введите число';
    }
    elseif (strlen($formVal[$param]) > $specpar['maxlen'] ) {
        $formErr[$param] = 'Превышено значение';
    }
    elseif (!is_int($formVal[$param] * 1)) {
        $formErr[$param] = 'Введите целое число'; 
    }
    elseif ($formVal[$param] < $specpar['minbet'] ) {
        $formErr[$param] = 'Введите число больше' . $specpar['minbet'];
    }
    elseif (strpos($formVal[$param], '0') === 0) {
        $formErr[$param] = 'Слишком много нулей :)';
    }

}

/* Шаблонизация - подключение шаблонов */

// Лот пуст (лота с таким id нет) или id лота нет (параметра запроса нет)
if(empty($item) OR empty($_GET['itemID'])){
    $page_name = '404 Страница не найдена';
    $response_code = http_response_code(404);
    $page_content = include_template('error.php', [
        'categories' => $categories
    ]);
}
// Если отправлена ставка
elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $page_name = $item['name'];
    $page_content = include_template('lot.php', [
        'categories' => $categories, 
        'item' => $item,
        'itemBets' => $itemBets,
        'formVal' => $formVal,
        'formErr' => $formErr
    ]);
}
// Id Лота, существует и лот не пуст
else {
    $page_name = $item['name'];
    $page_content = include_template('lot.php', [
        'categories' => $categories, 
        'item' => $item,
        'itemBets' => $itemBets
    ]);
}
/* Форма добавления ставки */


/* Шаблонизация - подключение подложики */

$layout_content = include_template('layout.php', [
    'categories' => $categories, 
    'content' => $page_content, 
    'title' => $page_name,
    'page_style_main' => ''
]);

$response_code;
print($layout_content);
