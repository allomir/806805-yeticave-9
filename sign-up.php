<?php

require('inc/function.php'); // функции, response_code, is_auth
require('inc/queries.php'); // Запросы и подключение
require('helpers.php'); // шаблонизатор

$conn = getConn(); // Подключение к БД
$categories = getCategories($conn); // Запрос Показать Таблицу Категории

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

$number_err = 0;

/********************************** Форма отправлена *************************************/

// Событие нажатие кнопки
if (isset($_POST['sign-up'])) { 

    /* 1часть. Проверки полей */

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

    /* 2часть. Проверки поля email */

    if(empty($formErrors['email'])) {
        // проверка валидность
        if (!filter_var($formData['email'], FILTER_VALIDATE_EMAIL)) {
            $formErrors['email'] = 'Email должен быть корректным';
        }
        // проверка на уникальность
        elseif (!empty(checkUserByEmail($conn, $formData['email']))) {
            $formErrors['email'] = 'email занят';
        }
    }

    /* 3 часть. Поле пароль обработать встроенной функцией password_hash */
    if (empty($formErrors['password'])) {
        $passwordHash = password_hash($formData['password'], PASSWORD_DEFAULT);
    }
    
    /* 4 часть. Колво ошибок */

    // Результат - Cчитаем колво ошибок после нажатия кнопки и проверок

    foreach ($formErrors as $error) {
        if (!empty($error)) {
            $number_err++;
        }
    }
}

/****************************************** Переадресация *****************************************/

// После нажатия кнопки и Колво ошибок 0 
if (isset($_POST['sign-up']) && $number_err === 0) {

    // Параметры пользователя для инсерта
    $user = [
        'email' => $formData['email'], 
        'password' => $passwordHash, 
        'name' => $formData['name'], 
        'contacts' => $formData['message']
        // avatar_url, // не требуется по заданию
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

        mysqli_close($conn);

        // Перенаправление на страницу входа
        header("Location: login.php/?success=true");
    }
}

/* Шаблонизатор */

$page_name = 'Регистрация';

$page_content = include_template('sign-up.php', [
    'categories' => $categories, 
    'formData' => $formData,
    'formErrors' => $formErrors  
]);

$layout_content = include_template('layout.php', [
    'is_auth' => $is_auth,
    'user_name' => $user_name, 
    'categories' => $categories, 
    'content' => $page_content, 
    'title' => $page_name,
    'page_style_main' => ''
]);

print($layout_content);