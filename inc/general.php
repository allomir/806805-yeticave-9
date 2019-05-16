<?php

session_start();

$conn = getConn(); // Подключение к БД
$categories = getCategories($conn); // Запрос Показать Таблицу Категории

$response_code = ''; // кода ответа страницы по умолчанию 200 или пусто