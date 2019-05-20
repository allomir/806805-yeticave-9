<?php

/**
 * Подключает шаблон, передает туда данные и возвращает итоговый HTML контент
 *
 * @param  string $name Путь к файлу шаблона относительно папки templates
 * @param  array  $data Ассоциативный массив с данными для шаблона
 * @return string Итоговый HTML
 */
function include_template($name, array $data = [])
{
    $name = 'templates/' . $name;
    $result = '';

    if (!is_readable($name)) {
        return $result;
    }

    ob_start();
    extract($data);
    include $name;

    $result = ob_get_clean();

    return $result;
}

/**
 * Возвращает корректную форму множественного числа
 * Ограничения: только для целых чисел
 * Все формы слова задаются в массиве внутри функции, ключом является передаваемое слово
 * Если слово отсутствует во внутреннем массиве, возвращается само слово
 *
 * Пример использования:
 * $remaining_minutes = 5;
 * echo "Я поставил таймер на {$remaining_minutes} " .
 *     getEndingWord($remaining_minutes, 'минута');
 * Результат: "Я поставил таймер на 5 минут"
 *
 * @param int    $number Число, по которому вычисляем  форму множественного числа
 * @param string $word Форма единственного числа: яблоко, час, минута
 *
 * @return string Рассчитанная форма множественнго числа или слово, если слово отсутствует во внутреннем массиве
 */
function getEndingWord($number, $word = 'ставка')
{
    $list = [
        'ставка' => ['ставка', 'ставки', 'ставок'],
        'час' => ['час', 'часа', 'часов'],
        'минута' => ['минута', 'минуты', 'минут']
    ];

    if(empty($list[$word])) {
        return $word;
    }

    $mod10 = $number % 10;
    $mod100 = $number % 100;
            
    if ($mod100 >= 11 && $mod100 <= 20) {
        $wordEnd = $list[$word][2];
    }
    elseif ($mod10 > 5) {
        $wordEnd = $list[$word][2];
    }
    elseif ($mod10 === 1) {
        $wordEnd = $list[$word][0]; 
    }
    elseif ($mod10 >= 2 && $mod10 <= 4) {
        $wordEnd = $list[$word][1];
    }
    else {$wordEnd = $list[$word][2];
    }

    return $wordEnd;
}

/**
 * Проверяет переданную дату на соответствие формату 'ГГГГ-ММ-ДД'
 *
 * Примеры использования:
 * is_date_valid('2019-01-01'); // true
 * is_date_valid('2016-02-29'); // true
 * is_date_valid('2019-04-31'); // false
 * is_date_valid('10.10.2010'); // false
 * is_date_valid('10/10/2010'); // false
 *
 * @param string $date Дата в виде строки
 *
 * @return bool true при совпадении с форматом 'ГГГГ-ММ-ДД', иначе false
 */
function is_date_valid(string $date) : bool
{
    $format_to_check = 'Y-m-d';
    $dateTimeObj = date_create_from_format($format_to_check, $date);

    return $dateTimeObj !== false && array_sum(date_get_last_errors()) === 0;
}

/**
 * Форматирует переданную цену в формат с разделителем в виде пробела '1 000 000'
 * Ограничения: >= 1000 (если цена больше или равна 1000)
 * 
 * Пример:
 * makePriceFormat('10000'); // 10 000
 * makePriceFormat('1000'); // 1 000
 * makePriceFormat('900'); // 900
 * 
 * @param  string $price цена в виде строки
 * @return string $priceFormat цена в виде строки с разделителем
 */

function makePriceFormat($price)
{
    $priceFormat = ceil($price); 
    if ($priceFormat >= 1000) {
        $priceFormat = number_format($price, $decimals = 0, ".", " ");
    }
    return $priceFormat;
}

/**
 * Показывает время, оставшееся до конца торгов в формате 'DD:HH:MM' или 'HH:MM'
 * и передает название класса, если осталось меншье 1 часа
 * 
 * Ограничения: 
 * если осталось более 99 дней, показывает 99:00:00
 * если осталось более 1 дня, показывает в формате 'DD:HH:MM'
 * если осталось более 0, показывает в формате 'HH:MM'
 * если время вышло показывает 00:00 (по умолчанию)
 * если осталось меньше 1 часа, то передает специальный класс
 * 
 * Пример:
 * time() // 2019-06-10 00:00:00
 * makeTimer('2019-06-09 12:00:00'); // 12:00
 * makeTimer('2019-06-05 12:00:00'); // 04:12:00
 * 
 * @param  string $date время окончания торгов
 * @return array ассоциат массив - ключ 'DDHHMM' => время в виде строки, ключ 'style' => название класса в виде строки 
 */
function makeTimer($date)
{
    date_default_timezone_set("Europe/Moscow");
    $TS_diff = strtotime($date) - time(); // Осталось до конца ставки
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

    if ($TS_diff > 86400 * 99) {
        $timer =  '99:00:00';
    }
    elseif ($TS_diff > 86400) {
        $timer =  $days . ':' . $hours . ':' . $minutes;
    }
    elseif ($TS_diff > 0) {
        $timer =  $hours . ':' . $minutes;
    }

    return ['DDHHMM' => $timer, 'style' => $timer_style];
}

/**
 * Показывает время, прощедшее с момента ставки в форматах 'минуту назад', 30 минут назад', '5 часов назад'
 * или время когда сделана ставка в формате '19.06.10 в 12:00'
 * 
 * Ограничения: 
 * если ставка сделана до 1 часа назад, формат '59 минт назад'
 * если ставка сделана до 24 часов назад, формат '23 часа назад'
 * если ставка сделана более 24 часа назад, формат '19.06.10 в 12:00'
 * 
 * Пример:
 * time() // 2019-10-06 00:00:00
 * * showBetTime('2019-09-06 23:59:00'); // минуту назад
 * showBetTime('2019-09-06 23:30:00'); // 30 минут назад
 * showBetTime('2019-09-06 12:00:00'); // 12 часов назад
 * showBetTime('2019-05-06 12:00:00'); // 19.05.06 в 12:00
 * 
 * @param  string $date время момента ставки в виде строки
 * @return string прошло времени после ставки или время ставки в формате в виде строки 
 */
function showBetTime($date)
{
    date_default_timezone_set("Europe/Moscow");
    $TS_diff = time()- strtotime($date);
    
    // Осталось часов
    $hour = floor($TS_diff / 3600);
    $minute = floor(($TS_diff % 3600) / 60);

    if ($hour > 24) {
        $bet_time = date('y.m.d \в H:i', strtotime($date));
    }
    elseif ($hour >= 1) {
        $bet_time = $hour . ' ' . getEndingWord($hour, 'час') . ' назад';
    }
    elseif ($TS_diff < 120) {
        $bet_time = 'минуту назад';
    }
    else {
        $bet_time = $minute . ' ' . getEndingWord($minute, 'минута') . ' назад';
    }

    return $bet_time;
}
