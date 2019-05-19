<?php

require 'inc/functions.php'; // функции
require 'inc/queries.php'; // Запросы и подключение
require 'inc/helpers.php'; // шаблонизатор

require 'inc/general.php'; // Общие сценарии всех страниц 

// параметры - название полей, и название ошибок, если поле не заполнено
$params = [
    'email' => 'Введите e-mail', 
    'password' => 'Введите пароль', 
    'name' => 'Введите имя',
    'message' => 'Напишите как с вами связаться'
];

// Особые параметры полей формы, указываются и проверяются индвивидуально
$specpars = [
    'email' => ['maxlen' => '64'], 
    'password' => ['maxlen' => '64', 'minlen' => '4'], 
    'name' => ['maxlen' => '64'], 
    'message' => ['maxlen' => '255']
];

foreach ($params as $param => $errors) {
    $formData[$param] = $_POST[$param] ?? '';
    $formErrors[$param] = '';
}

/**********************************
 * Форма отправлена 
*************************************/

if (isset($_POST['sign-up'])) { 

    foreach ($params as $param => $error) {
        // Проверка каждое поле на пусто 
        if (empty($formData[$param])) {
            $formErrors[$param] = $error;
        }
        // Максимальная длина строк
        elseif (strlen($formData[$param]) > $specpars[$param]['maxlen'] ) {
            $formErrors[$param] = 'Превышено число знаков:' . $specpars[$param]['maxlen'];
        }
        // Минимальное число знаков пароля
        elseif ($param == 'password') {
            if (strlen($formData[$param]) < 4) {
                $formErrors[$param] = 'Пароль должен быть не менее ' . $specpars[$param]['minlen'] . ' знаков';
            }
        }
    }

    // Защита email от SQL-инъекции
    $saveEmail = mysqli_real_escape_string($conn, $formData['email']);

    if(empty($formErrors['email'])) {
        // проверка валидность
        if (!filter_var($formData['email'], FILTER_VALIDATE_EMAIL)) {
            $formErrors['email'] = 'Email должен быть корректным';
        }
        // проверка на уникальность
        elseif (!empty(checkUserByEmail($conn, $saveEmail))) {
            $formErrors['email'] = 'email занят';
        }
    }

    // Результат - Cчитаем колво ошибок после нажатия кнопки и проверок
    $number_err = 0;
    foreach ($formErrors as $error) {
        if (!empty($error)) {
            $number_err++;
        }
    }
}

/******************************************
 * Переадресация 
*****************************************/

// После нажатия кнопки и колво ошибок 0 
if (isset($_POST['sign-up']) && empty($number_err)) {

    // Пароль обработать встроенной функцией password_hash 
    $passwordHash = password_hash($formData['password'], PASSWORD_DEFAULT);

    // Параметры пользователя для инсерта
    $user = [
        'email' => $formData['email'], 
        'password' => $passwordHash, 
        'name' => $formData['name'], 
        'contacts' => $formData['message'],
        'avatar_url' => '/img/user.png' // ставим по умолчанию, не требуется по заданию
        // ts_created // автозаполнение
    ];

    // Защита от SQL-инъкция - экранирование
    foreach ($user as $key => $value) {
        $saveValue = mysqli_real_escape_string($conn, $value);
        $saveUser[$key] = $saveValue;
    }

    // Запрос БД на добавление нового пользователя
    if(insertNewUser($conn, $saveUser)) {
        $last_id = mysqli_insert_id($conn);

        mysqli_close($conn); // закрыть подключение БД

        // Перенаправление на страницу входа
        header("Location: /login.php/?congratulation=true");
    }
}

mysqli_close($conn); // закрыть подключение БД

$page_content = include_template(
    'sign-up.php', [
    'categories' => $categories, 
    'formData' => $formData,
    'formErrors' => $formErrors  
    ]
);

// Подложка
$layout_content = include_template(
    'layout.php', [
    'categories' => $categories, 
    'content' => $page_content, 
    'title' => 'Регистрация',
    'page_style_main' => ''
    ]
);

print($layout_content);