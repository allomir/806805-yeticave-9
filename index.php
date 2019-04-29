<?php

$is_auth = rand(0, 1);
$user_name = 'Михаил Лебедев';

/* Подключение к БД и запросы */

$conn = mysqli_connect("localhost", "root", "", "yeticave");
mysqli_set_charset($conn, "utf8"); // первым делом кодировка

if ($conn == false) {
    print("Ошибка: Невозможно подключиться к MySQL " . mysqli_connect_error());
}

/* 1часть. Извлечение категорий из таблицы */

// запрос значений таблицы categories без защиты от sql-инъекции, тк нет переменных
$sql = 'SELECT * FROM categories'; 
$result = mysqli_query($conn, $sql);
if (!$result) {
    $error = mysqli_error($conn);
    print("Ошибка MySQL: " . $error);
}

// передача значений в ассоциативный массив с категориями
$rows = mysqli_fetch_all($result, MYSQLI_ASSOC);  
foreach ($rows as $category_row) {
    $categories[$category_row[symbol]] = $category_row[title]; 
}

/* 2часть. Извлечение лотов из таблицы */

// запрос значений для лотов, активных (не закрытый) без защиты от sql-инъекции, тк нет переменных
$sql = 'SELECT items.*, title AS category, symbol FROM items 
    JOIN categories ON items.category = categories.id
    WHERE items.ts_end > CURRENT_TIMESTAMP /* проверяем что лот не закрыт */
    ORDER BY ts_add DESC 
'; 

$result = mysqli_query($conn, $sql);
if (!$result) {
    $error = mysqli_error($conn);
    print("Ошибка MySQL: " . $error);
}

// передача значений в двумерный массив с лотами
$items = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Закрытие подключения к БД
mysqli_close($conn);

/*  3часть. Группировка по лотам, поиск количесва ставок и макс. (последней) цены лота*/

function getLastPrice ($itemID, $price) {

    // Внутри функции новое подключение, наружное не видет
    $conn = mysqli_connect("localhost", "root", "", "yeticave");
    mysqli_set_charset($conn, "utf8"); // первым делом кодировка
    
    if ($conn == false) {
        print("Ошибка: Невозможно подключиться к MySQL " . mysqli_connect_error());
    }

    // запрос группировка ставок по лотам, активных лотов (не закрытый) без защиты от sql-инъекции, тк нет переменных
    $sql = "SELECT item_id, COUNT(item_id) AS number_bets, MAX(bet_price) AS last_price FROM bets 
        WHERE winner_id IS NULL AND item_id = '$itemID' /* проверяем что лот не закрыт, те нет победителя и врямя не вышло. Если никто не сделал ставку лота нет в таблице */
        GROUP BY item_id DESC 
    "; 

    $result = mysqli_query($conn, $sql);
    if (!$result) {
        $error = mysqli_error($conn);
        print("Ошибка MySQL: " . $error);
    }
    
    // передача значений в ассоциативный массив с количеством ставок и макс ценой
    $bet = mysqli_fetch_assoc($result);
    $bet[number_bets] .= ' ставок';
    if (mysqli_num_rows($result) == 0) {
        $bet[last_price] = $price;
        $bet[number_bets] = 'Стартовая цена';
    }
    return $bet;
}

/* заменены на данные из таблиц БД по аналогии
$categories = ['Доски и лыжи', 'Крепления', 'Ботинки', 'Одежда', 'Инструменты', 'Разное'];
$items = [
    [
        'name' => '2014 Rossignol District Snowboard',
        'category' => 'Доски и лыжи',
        'price' => '10999',
        'imgURL' => 'img/lot-1.jpg'
    ],
    [
        'name' => 'DC Ply Mens 2016/2017 Snowboard',
        'category' => 'Доски и лыжи',
        'price' => '159999',
        'imgURL' => 'img/lot-2.jpg'
    ],
    [
        'name' => 'Крепления Union Contact Pro 2015 года размер L/XL',
        'category' => 'Крепления',
        'price' => '8000',
        'imgURL' => 'img/lot-3.jpg'
    ],
    [
        'name' => 'Ботинки для сноуборда DC Mutiny Charocal',
        'category' => 'Ботинки',
        'price' => '10999',
        'imgURL' => 'img/lot-4.jpg'
    ],
    [
        'name' => 'Куртка для сноуборда DC Mutiny Charocal',
        'category' => 'Одежда',
        'price' => '7500',
        'imgURL' => 'img/lot-5.jpg'
    ],
    [
        'name' => 'Маска Oakley Canopy',
        'category' => 'Разное',
        'price' => '5400',
        'imgURL' => 'img/lot-6.jpg'
    ]
];
*/

function makePriceFormat($price) {
    $priceFormat = ceil($price); // Округление и значение поумолчанию если < 1000

        if ($priceFormat >= 1000) {
            $priceFormat = number_format($price, $decimals = 0, ".", " ");
        }
        
    return $priceFormat . '<b class="rub">р</b>';
}

function makeTimer($TS_end) {
    date_default_timezone_set("Europe/Moscow");
    $TS_diff = strtotime($TS_end) - time(); // Осталось до конца ставки
    $timer_style = '';

    // Создаем таймер День : Час : Мин
    if ($TS_diff > 0) {
        $days = floor($TS_diff / 86400);
        $hours = floor(($TS_diff % 86400) / 3600);
        $minutes = floor(($TS_diff % 3600) / 60);
        $timer = $days . ":" . $hours . ":" . $minutes;
        
        if ($TS_diff <= 3600) {
            $timer_style = 'timer--finishing';
        }
    }
    else {
        $timer = '00:00';
    } 

    return $timer = [$timer, $timer_style];
}

require('helpers.php');

$page_content = include_template('index.php', [
    'categories' => $categories, 
    'items' => $items,
    'timer' => $timer
]);

$layout_content = include_template('layout.php', [
    'is_auth' => $is_auth,
    'categories' => $categories, 
    'content' => $page_content, 
    'user_name' => $user_name, 
    'title' => 'Главная'
]);

print($layout_content);
