<?php

$is_auth = rand(0, 1);
$user_name = 'Михаил Лебедев';

/* Переопределение кода ответа. Переменная передается в подложку.
- По умолчанию 200 или ничего, 
- стр не найдена 404, http_response_code(404)
- при переезде стр 302, при переадресации 301 и тд. 
*/
$response_code = ''; 

/* Горизонтальное простое меню - для всех страниц кроме главной */

function makeMainMenuSimple($categories) {
    $MenuSimple = '';
    foreach ($categories as $category) {
        $MenuSimple .= '<li class="nav__item"><a href="all-lots.php">' . htmlspecialchars($category["title"]) . '</a></li>';
    }
return $MenuSimple;
}

/* функция формат цены */

function makePriceFormat($price) {
    $priceFormat = ceil($price); // Округление и значение поумолчанию если < 1000
        if ($priceFormat >= 1000) {
            $priceFormat = number_format($price, $decimals = 0, ".", " ");
        }
    return $priceFormat;
}

/* функция таймер */

function makeTimer($TS_end) {
    date_default_timezone_set("Europe/Moscow");
    $TS_diff = strtotime($TS_end) - time(); // Осталось до конца ставки
    $timer_style = '';

    // Создаем таймер День : Час : Мин
    if ($TS_diff > 0) {
        /* Дней осталось
        $days = floor($TS_diff / 86400);
        $hours = floor(($TS_diff % 86400) / 3600);*/

        $hours = floor($TS_diff / 3600);
        if ($hours >= 99) {$hours = '99';}
        elseif($hours < 10) {$hours = '0' . $hours;}

        $minutes = floor(($TS_diff % 3600) / 60);
        if ($hours >= 99) {$minutes = '00';}
        elseif($hours < 99 && $minutes < 10) {$minutes = '0' . $minutes;}

        $timer = /* $days . ":" . */$hours . ":" . $minutes;
        
        if ($TS_diff <= 3600) {
            $timer_style = 'timer--finishing';
        }
    }
    else {
        $timer = '00:00';
    } 
    return $timer = ['DDHHMM' => $timer, 'style' => $timer_style];
}


/* функция последняя цена и колво ставок */

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
    if (mysqli_num_rows($result)) {
        $bet = mysqli_fetch_assoc($result);
        $bet['number_bets'] .= ' ставок';
    }
    else {
        $bet['last_price'] = $price;
        $bet['number_bets'] = 'Стартовая цена';
    }

    return $bet;
}

/* Минимальная ставка  */

function getMinBet($lastPrice, $betStep) {
    $lastPrice = intval($lastPrice);
    $betStep = intval($betStep);

    return $lastPrice + $betStep;
}