<?php

require('inc/function.php');
require('inc/queries.php');
require('inc/helpers.php');
$response_code = '';

session_start();

$conn = getConn();
$categories = getCategories($conn);

$search = $_GET['search'] ?? '';
$saveSearch = mysqli_real_escape_string($conn, $search);

$page = $_GET['page'] ?? 1;
$limit = $_GET['limit'] ?? 6; 
$num_items = findItemsByFText($conn, $saveSearch, 0); // вернет общее число строк
$num_pages = ceil($num_items / $limit);
$items = findItemsByFText($conn, $saveSearch, $page, $limit); // вернет 6 строк (по лимиту)

$page_name = 'Поиск информации о лотах';

$page_content = include_template('search.php', [
    'categories' => $categories,
    'items' => $items,
    'num_pages' => $num_pages,
    'search' => $search
]);

$layout_content = include_template('layout.php', [
    'categories' => $categories,
    'content' => $page_content,
    'title' => $page_name,
    'page_style_main' => ''
]);

print($layout_content);