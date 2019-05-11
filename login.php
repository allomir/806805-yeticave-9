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

    $formVals = $_POST;
    $params = ['email', 'password'];
    $errors = []; 

    foreach ($params as $param) {
        if (empty($formVals[$param])) {
            if ($param == 'email') {
                $errors[$param] = 'Введите e-mail';
            }
            elseif ($param == 'password') {
                $errors[$param] = 'Введите пароль';
            }
        }        
    }

    if (empty($errors['email'])) {
        if (!filter_var($formVals['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Email должен быть корректным';
        }
    }

    $saveEmail = mysqli_real_escape_string($conn, $formVals['email']);
    $user = empty($errors['email']) ? checkUserByEmail($conn, $saveEmail) : '';

    if (count($errors)) {
        $errors['password'] = 'Неверная пара пользователь/пароль';
    }

    if (empty($user) && empty($errors['email'])) {
        $errors['email'] = 'Такой пользователь не найден';
        $errors['password'] = 'Неверная пара пользователь/пароль';
    } 
        
    if (!count($errors)) {
        if (password_verify($formVals['password'], $user['password'])) {
            $_SESSION['user'] = $user;
        } else {
            $errors['password'] = 'Вы ввели неверный пароль';
        }
    } 


    /* Страница с ошибками */

    if (count($errors)) {
        $page_name = 'Вход на сайт';
        $page_content = include_template('login.php', [
            'user_name' => $user_name,
            'categories' => $categories,
            'formVals' => $formVals,
            'errors' => $errors
        ]);
    } else {
        header("Location: /?welcome=true");
        exit();
    }

} else {

    /* Страница входа после входа */

    if (isset($_SESSION['user'])) {
        $page_content = '<div class="container"><h3>Добро пожаловать, ' . $user_name . '<h3></div>';
    } else {

        /* Страница входа обычная */

        $page_content = include_template('login.php', [
            'user_name' => $user_name,
            'categories' => $categories
        ]);
    }
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