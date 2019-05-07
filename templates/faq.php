<?php

/* Заметки */

// печать ошибок в добавлении лота
$i = 0;
  foreach ($formErrors as $value) {
    if (!empty($value)) {
      $i++;
      print($i . '. ');
      print($value . '<br>');
    }
  }
if(empty($imgData['img_url'])) {print(++$i . '. файл не загружен <br>');}
print('<br>');


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

// 1 способ

$sql = "INSERT INTO items SET 
category_id = '" . $item['category_id'] . "', 
user_id = '" . $item['user_id'] . "', 
name = '" . $item['name'] . "',
description = '" . $item['description'] . "',
img_url = '" . $item['img_url'] . "',
price = '" . $item['price'] . "',
step = '" . $item['step'] . "',
ts_add = '" . $item['step'] . "',
ts_end = '" . $item['ts_end'] . "'
";

// 3 способ

$sql = sprintf("INSERT INTO items 
(
category_id, 
user_id, 
name,
description,
img_url,
price,
step,
ts_add,
ts_end
)
VALUES
('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')",  
$item['category_id'],
$item['user_id'],
$item['name'],
$item['description'],
$item['img_url'],
$item['price'],
$item['step'],
$item['ts_add'],
$item['ts_end']
);