<?php

require 'inc/functions.php'; // функции и шаблонизатор
require 'inc/queries.php'; // Запросы и подключение
require 'inc/general.php'; // Общие сценарии всех страниц 

$user = $_SESSION['user'] ?? [];

$conn = getConn(); // Подключение к БД
$categories = getCategories($conn); // Запрос Показать Таблицу Категории

// Страница лота. Получение id лота из параметра запроса GET 
if (isset($_GET['item_id'])) {
    $save_item_id = mysqli_real_escape_string($conn, $_GET['item_id']); // Защита от SQL-инъкция - экранирование
    $item = getItemByID($conn, $save_item_id); // Запрос элемента из БД таблицы по id, массив или [] 
    $item_bets = !empty($item) ? getBetsByItemID($conn, $item['id'], 'DESC') : []; // Запрос показать ставки лота по его id, массив или [] 
    $last_item_bet = !empty($item_bets) ? getBetsByItemID($conn, $item['id'], 'DESC')[0] : []; // Последняя ставка при сортировке DESC
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $param = 'cost';
    $form_value[$param] = trim($_POST[$param]);
    $form_error = [];
    $options = ['max_length' => 7, 'min_bet' => $item['min_bet']];

    if (empty($form_value[$param])) {
        $form_error[$param] = 'Введите ставку';
    } elseif (!is_numeric($form_value[$param])) {
        $form_error[$param] = 'Введите число';
    } elseif (strlen($form_value[$param]) > $options['max_length'] ) {
        $form_error[$param] = 'Превышено значение';
    } elseif (!is_int($form_value[$param] * 1)) {
        $form_error[$param] = 'Введите целое число'; 
    } elseif ($form_value[$param] < $options['min_bet'] ) {
        $form_error[$param] = 'Минимальная ставка ' . $options['min_bet'];
    } elseif (strpos($form_value[$param], '0') === 0) {
        $form_error[$param] = 'Слишком много нулей :)';
    }

    // Pапрет делать ставку повторно, если ставка юзера последняя
    if (!empty($item_bets) && $last_item_bet['user_id'] === $user['id']) {
        $form_error[$param] = 'Ваша ставка последняя';
    }

    if(empty($form_error)) {
        $bet = [
            'item_id' => $item['id'],
            'user_id' => $user['id'],
            'bet_price' => mysqli_real_escape_string($conn, $form_value['cost'])
        ];

        if(insertNewBet($conn, $bet)) {
            header("Location: /lot.php?bet_success&item_id=" . $item['id']);
            exit();
        }
    }
}

// Лот пуст (лота с таким id нет) или id лота нет (втч параметра запроса нет)
if(empty($item) OR empty($_GET['item_id'])) {
    $page_name = '404 Страница не найдена';
    $response_code = http_response_code(404);
    $page_content = include_template(
        'error.php', 
        [
        'categories' => $categories,
        'page_error' => '404'
        ]
    );
} elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Если отправлена ставка, страница с ошибками и данынми
    $page_name = $item['name'];
    $page_content = include_template(
        'lot.php', 
        [
        'categories' => $categories, 
        'item' => $item,
        'item_bets' => $item_bets,
        'form_value' => $form_value,
        'form_error' => $form_error
        ]
    );
} else {
    // По умолчанию - Id Лот существует и лот не пуст
    $page_name = $item['name'];
    $page_content = include_template(
        'lot.php', 
        [
        'categories' => $categories, 
        'item' => $item,
        'item_bets' => $item_bets
        ]
    );
}

mysqli_close($conn); // Закрытие подключения к БД

// Подложка
$layout_content = include_template(
    'layout.php', 
    [
    'categories' => $categories, 
    'content' => $page_content, 
    'title' => $page_name
    ]
);

$response_code;
print($layout_content);
