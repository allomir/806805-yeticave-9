<?php

require 'inc/functions.php'; // функции и шаблонизатор
require 'inc/queries.php'; // Запросы и подключение
require 'inc/general.php'; // Общие сценарии всех страниц 

// параметры - название полей, и название ошибок, если поле не заполнено
$params = [
    'email' => 'Введите e-mail', 
    'password' => 'Введите пароль', 
    'name' => 'Введите имя',
    'message' => 'Напишите как с вами связаться'
];

// Особые параметры полей формы, указываются и проверяются индвивидуально
$options = [
    'email' => ['max_length' => '64'], 
    'password' => ['max_length' => '64', 'min_length' => '4'], 
    'name' => ['max_length' => '64'], 
    'message' => ['max_length' => '255']
];

foreach ($params as $param => $errors) {
    $form_values[$param] = $_POST[$param] ?? '';
    $form_errors[$param] = '';
}

/* Форма отправлена - нажатие кнопки */

if (isset($_POST['sign-up'])) { 

    foreach ($params as $param => $error) {
        // Проверка каждое поле на пусто 
        if (empty($form_values[$param])) {
            $form_errors[$param] = $error;
        } elseif (strlen($form_values[$param]) > $options[$param]['max_length'] ) {
            // Максимальная длина строк
            $form_errors[$param] = 'Превышено число знаков: ' . $options[$param]['max_length'];
        } elseif ($param == 'password') {
            // Минимальное число знаков пароля
            if (strlen($form_values[$param]) < $options[$param]['min_length']) {
                $form_errors[$param] = 'Пароль должен быть не менее ' . $options[$param]['min_length'] . ' знаков';
            }
        }
    }

    // Защита email от SQL-инъекции
    $save_email = mysqli_real_escape_string($conn, $form_values['email']);

    if(empty($form_errors['email'])) {
        // проверка валидность
        if (!filter_var($form_values['email'], FILTER_VALIDATE_EMAIL)) {
            $form_errors['email'] = 'Email должен быть корректным';
        }
        // проверка на уникальность
        elseif (!empty(checkUserByEmail($conn, $save_email))) {
            $form_errors['email'] = 'email занят';
        }
    }

    // Результат - Cчитаем колво ошибок после нажатия кнопки и проверок
    $num_errors = 0;
    foreach ($form_errors as $value) {
        $num_errors = empty($value) ? $num_errors : ++$num_errors; 
    }
}

/* Переадресация */

// После нажатия кнопки и если колво ошибок 0 
if (isset($_POST['sign-up']) && empty($num_errors)) {
    $passwordHash = password_hash($form_values['password'], PASSWORD_DEFAULT); // Пароль, функция password_hash 

    // Параметры пользователя для инсерта
    $user = [
        'email' => $form_values['email'], 
        'password' => $passwordHash, 
        'name' => $form_values['name'], 
        'contacts' => $form_values['message'],
        'avatar_url' => '/img/user.png' // ставим по умолчанию, не требуется по заданию
        // ts_created // автозаполнение
    ];

    // Защита от SQL-инъкция - экранирование
    foreach ($user as $key => $value) {
        $save_value = mysqli_real_escape_string($conn, $value);
        $save_user[$key] = $save_value;
    }

    // Запрос БД на добавление нового пользователя
    if(insertNewUser($conn, $save_user)) {
        mysqli_close($conn); // закрыть подключение БД
        header("Location: /login.php/?congratulation=true"); // Перенаправление на страницу входа
        exit();
    }
}

mysqli_close($conn); // закрыть подключение БД

$page_content = include_template(
    'sign-up.php', 
    [
    'categories' => $categories, 
    'form_values' => $form_values,
    'form_errors' => $form_errors  
    ]
);

// Подложка
$layout_content = include_template(
    'layout.php', 
    [
    'categories' => $categories, 
    'content' => $page_content, 
    'title' => 'Регистрация'
    ]
);

print($layout_content);