<?php

require 'inc/functions.php'; // функции и шаблонизатор
require 'inc/queries.php'; // Запросы и подключение
require 'inc/general.php'; // Общие сценарии всех страниц 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $params = ['email', 'password'];
    $form_values = $_POST;
    $form_errors = []; 

    foreach ($params as $param) {
        if (empty($form_values[$param])) {
            if ($param === 'email') {
                $form_errors[$param] = 'Введите e-mail';
            } elseif ($param === 'password') {
                $form_errors[$param] = 'Введите пароль';
            }
        }        
    }

    if (empty($form_errors['email'])) {
        if (!filter_var($form_values['email'], FILTER_VALIDATE_EMAIL)) {
            $form_errors['email'] = 'Email должен быть корректным';
        }
    }

    $save_email = mysqli_real_escape_string($conn, $form_values['email']);
    $user = empty($form_errors['email']) ? checkUserByEmail($conn, $save_email) : '';

    if (empty($user) && empty($form_errors['email'])) {
        $form_errors['email'] = 'Такой пользователь не найден';
    } 
    
    if (empty($form_errors['password']) && !empty($form_errors['email'])) {
        $form_errors['password'] = 'Неверная пара пользователь/пароль';
    }

    if (!count($form_errors)) {
        if (password_verify($form_values['password'], $user['password'])) {
            $_SESSION['user'] = $user;
        } else {
            $form_errors['password'] = 'Вы ввели неверный пароль';
        }
    } 

    // Страница после отправки формы
    if (count($form_errors)) {
        $page_content = include_template(
            'login.php', 
            [
            'categories' => $categories,
            'form_values' => $form_values,
            'form_errors' => $form_errors
            ]
        );
    } else {
        header("Location: /?welcome=true");
        exit();
    }

} else {

    // Страница входа после входа
    if (isset($_SESSION['user'])) {
        $page_content = include_template(
            'error.php', 
            [
            'categories' => $categories,
            'page_error' => 'login'
            ]
        );
    } else {
        // Страница входа обычная 
        $page_content = include_template(
            'login.php', 
            [
            'categories' => $categories
            ]
        );
    }
}

// Подложка
$layout_content = include_template(
    'layout.php', 
    [
    'categories' => $categories, 
    'content' => $page_content, 
    'title' => 'Вход на сайт'
    ]
);

print($layout_content);