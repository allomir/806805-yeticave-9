<?php

// faq.php - удаленные промежуточные данные заданий.

function deffXSS($value)
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8', true);
}

// Определение окончания

function getEndingWord($number, $word = 'ставка')
{
    $list = [
        'ставка' => ['ставка', 'ставки', 'ставок'],
        'час' => ['час', 'часа', 'часов'],
        'минута' => ['минута', 'минуты', 'минут']
    ];

    $mod10 = $number % 10;
    $mod100 = $number % 100;
            
    if ($mod100 >= 11 && $mod100 <= 20) {
        $wordEnd = $list[$word][2];
    }
    elseif ($mod10 > 5) {
        $wordEnd = $list[$word][2];
    }
    elseif ($mod10 == 1) {
        $wordEnd = $list[$word][0]; 
    }
    elseif ($mod10 >= 2 && $mod10 <= 4) {
        $wordEnd = $list[$word][1];
    }
    else {$wordEnd = $list[$word][2];
    }

    return $wordEnd;
}

/* функция формат цены */

function makePriceFormat($price)
{
    $priceFormat = ceil($price); // Округление и значение поумолчанию если < 1000
    if ($priceFormat >= 1000) {
        $priceFormat = number_format($price, $decimals = 0, ".", " ");
    }
    return $priceFormat;
}

/* функция таймер */

function makeTimer($TS_end)
{
    date_default_timezone_set("Europe/Moscow");
    $TS_diff = strtotime($TS_end) - time(); // Осталось до конца ставки
    $timer = '00:00';
    $timer_style = '';

    // Создаем таймер День : Час : Мин
    $days = floor($TS_diff / 86400);
    $hours = floor(($TS_diff % 86400) / 3600); 
    $minutes = floor(($TS_diff % 3600) / 60);

    if ($hours < 10) {
        $hours = '0' . $hours;
    }
    
    if ($minutes < 10) {
        $minutes = '0' . $minutes;
    }
    
    if ($TS_diff <= 3600) {
        $timer_style = 'timer--finishing';
    }

    if ($TS_diff > 86400) {
        $timer =  $days . ":" . $hours . ":" . $minutes;
    }
    elseif ($TS_diff > 0) {
        $timer =  $hours . ":" . $minutes;
    }

    return ['DDHHMM' => $timer, 'style' => $timer_style];
}

/* Время ставки */

function makeBacktime($value)
{
    date_default_timezone_set("Europe/Moscow");
    $TS_diff = time()- strtotime($value);
    
    // Осталось часов
    $hour = floor($TS_diff / 3600);
    $minute = floor(($TS_diff % 3600) / 60);

    if ($hour > 24) {
        $backTime = date('y.m.d \в H:i', strtotime($value));
    }
    elseif ($hour >= 1) {
        $backTime = $hour . ' ' . getEndingWord($hour, 'час') . ' назад';
    }
    elseif ($TS_diff < 120) {
        $backTime = 'минуту назад';
    }
    else {
        $backTime = $minute . ' ' . getEndingWord($minute, 'минута') . ' назад';
    }

    return $backTime;
}

/* Функция - Вставить класс ошибки, стр добавление лота */

function addErrorStyle($errors)
{
    // Виды стилей CLASS при заполнении полей или формы
    $formErrStyle = ['form--invalid', 'form__item--invalid']; 
 
    if (is_string($errors)) {
        if (!empty($errors)) {
            return $formErrStyle[1];
        }
    } else {
        // Если массив
        $number_err = 0;
        foreach ($errors as $error) {
            if (!empty($error)) {
                $number_err++;
            }
        }
        if ($number_err) {
            return $formErrStyle[0];
        }
    }
    
    return null;
}
