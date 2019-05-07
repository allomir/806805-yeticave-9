<?php

// faq.php - удаленные промежуточные данные заданий.
$is_auth = 1;
$user_name = 'Михаил Лебедев';

/* Переопределение кода ответа. Переменная передается в подложку.
- По умолчанию 200 или ничего, 
- стр не найдена 404, http_response_code(404)
- при переезде стр 302, при переадресации 301 и тд. 
*/
$response_code = ''; 

function deffXSS($value) {
    return htmlspecialchars($value, ENT_QUOTES,'cp1251');
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

/* Функция - Вставить класс ошибки, стр добавление лота */

function insErrStyle($errors) {
    // Виды стилей при ошибках заполнения
    $result = '';
    $formErrStyle = ['form--invalid', 'form__item--invalid']; // типы CLASS
    if (isset($_POST['add_lot'])) {
        if (is_string($errors)) {
            if (!empty($errors)) {
                $result =  $formErrStyle[1];
            }
        }
        else {
            $number_err = 0;
            foreach ($errors as $value) {
                if (!empty($value)) {
                    $number_err++;
                }
            }
            if ($number_err) {
                $result = $formErrStyle[0];
            }
        }
    }
    return $result;
}
