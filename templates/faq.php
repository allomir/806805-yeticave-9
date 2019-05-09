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


/* Переопределение кода ответа. Переменная передается в подложку.
- По умолчанию 200 или ничего, 
- стр не найдена 404, http_response_code(404)
- при переезде стр 302, при переадресации 301 и тд. 
*/
$response_code = ''; 

// стр. лот
$saveItemID = intval($_GET['itemID']); // Защита от SQL-инъкция (вариант 2) - приведение к числу

/*  Глобальные переменные
Список параметров 
_POST['lot-name']; 
_POST['category'];
_POST['message'];
_POST['lot-rate'];
_POST['lot-step'];
_POST['lot-date'];
_POST['add_lot']; // кнопка добавить лот
_FILES['lot-img']['name']; // Оригинальное имя файла на компьютере клиента
_FILES['lot-img']['type']; // Mime-тип файла, если браузер предоставил информацию, например "image/gif".
_FILES['lot-img']['size'] // Размер в байтах принятого файла.
_FILES['lot-img']['tmp_name'] // Временное имя, с которым принятый файл был сохранен на сервере.
*/

        // Проверка типа загружаемого файла (2 вариант)
        $finfo = finfo_open(FILEINFO_MIME_TYPE); // встроенные PHP MAGIC file по умолчанию
        $file_type = finfo_file($finfo, $imgData['tmp_name']); // определяем тип файла с именем во временной директории 
        finfo_close($finfo);

// Аналог функции mime_content_type по данным из массива

function getFileType($filename) {

    $mime_types = array(

        'txt' => 'text/plain',
        'htm' => 'text/html',
        'html' => 'text/html',
        'php' => 'text/html',
        'css' => 'text/css',
        'js' => 'application/javascript',
        'json' => 'application/json',
        'xml' => 'application/xml',
        'swf' => 'application/x-shockwave-flash',
        'flv' => 'video/x-flv',

        // images
        'png' => 'image/png',
        'jpe' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'jpg' => 'image/jpeg',
        'gif' => 'image/gif',
        'bmp' => 'image/bmp',
        'ico' => 'image/vnd.microsoft.icon',
        'tiff' => 'image/tiff',
        'tif' => 'image/tiff',
        'svg' => 'image/svg+xml',
        'svgz' => 'image/svg+xml',

        // archives
        'zip' => 'application/zip',
        'rar' => 'application/x-rar-compressed',
        'exe' => 'application/x-msdownload',
        'msi' => 'application/x-msdownload',
        'cab' => 'application/vnd.ms-cab-compressed',

        // audio/video
        'mp3' => 'audio/mpeg',
        'qt' => 'video/quicktime',
        'mov' => 'video/quicktime',

        // adobe
        'pdf' => 'application/pdf',
        'psd' => 'image/vnd.adobe.photoshop',
        'ai' => 'application/postscript',
        'eps' => 'application/postscript',
        'ps' => 'application/postscript',

        // ms office
        'doc' => 'application/msword',
        'rtf' => 'application/rtf',
        'xls' => 'application/vnd.ms-excel',
        'ppt' => 'application/vnd.ms-powerpoint',

        // open office
        'odt' => 'application/vnd.oasis.opendocument.text',
        'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
    );

    $ext = strtolower(array_pop(explode('.',$filename)));
    if (array_key_exists($ext, $mime_types)) {
        return $mime_types[$ext];
    }
    elseif (function_exists('finfo_open')) {
        $finfo = finfo_open(FILEINFO_MIME);
        $mimetype = finfo_file($finfo, $filename);
        finfo_close($finfo);
        return $mimetype;
    }
    else {
        return 'application/octet-stream';
    }
}


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