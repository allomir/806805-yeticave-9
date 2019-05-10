<?php

require('inc/function.php'); // функции
require('inc/queries.php'); // Запросы и подключение
require('inc/helpers.php'); // шаблонизатор
$response_code = '';

session_start();
$user_name = isset($_SESSION['user']) ? $_SESSION['user']['name'] : 0;

$conn = getConn(); // Подключение к БД
$categories = getCategories($conn); // Запрос Показать Таблицу Категории

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $valForms = $_POST;
    $params = ['email', 'password'];
    $errors = []; 

    foreach ($params as $param) {
        if (empty($valForms[$param])) {
            if ($param == 'email') {
                $errors[$param] = 'Введите e-mail';
            }
            elseif ($param == 'password') {
                $errors[$param] = 'Введите пароль';
            }
        }        
    }

    $saveEmail = mysqli_real_escape_string($conn, $valForms['email']);
    $user = checkUserByEmail($conn, $saveEmail);

    if (!count($errors) && $user) {
        if (password_verify($valForms['password'], $user['password'])) {
            $_SESSION['user'] = $user;
        } else {
            $errors['password'] = 'Вы ввели неверный пароль';
        }
    } else {
        $errors['email'] = 'Такой пользователь не найден';
    }

    if (count($errors)) {
        $page_name = 'Вход на сайт';
        $page_content = include_template('login.php', [
            'user_name' => $user_name,
            'categories' => $categories,
            'valForms' => $valForms,
            'errors' => $errors
        ]);
    } else {
        header("Location: /?welcome=true");
        exit();
    }

} else {

    /* Страница входа */

    $page_content = include_template('login.php', [
        'user_name' => $user_name,
        'categories' => $categories
    ]);
}

    /* подложка */

$page_name = 'Вход на сайт';
$layout_content = include_template('layout.php', [
    'user_name' => $user_name,
    'categories' => $categories, 
    'content' => $page_content, 
    'title' => $page_name,
    'page_style_main' => ''
]);

print($layout_content);