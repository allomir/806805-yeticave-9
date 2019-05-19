<?php

require 'inc/functions.php'; // функции и шаблонизатор
require 'inc/queries.php'; // Запросы и подключение
require 'inc/general.php'; // Общие сценарии всех страниц

$user = $_SESSION['user'] ?? []; // Аунтификация пользователя

// параметры - название полей, кроме изображения, и название ошибок, если поле не заполнено
$params = [
    'lot-name' => 'Введите наименование лота',
    'category' => 'Выберите категорию',
    'message' => 'Напишите описание лота',
    'lot-rate' => 'Введите начальную цену',
    'lot-step' => 'Введите шаг ставки',
    'lot-date' => 'Введите дату завершения торгов'
];

// Особые параметры полей формы кроме файла, указываются и проверяются индвивидуально
$options = [
    'lot-name' => ['max_length' => '255'],
    'message' => ['max_length' => '1024'],
    'lot-rate' => ['max_length' => '7'],
    'lot-step' => ['max_length' => '7'],
    'lot-date' => ['min_date' => strtotime('tomorrow + 1 days')] // lot-date
];

// Загрузка данных полей из глобальной переменной POST, если нет данных то пусто
foreach ($params as $param => $error) {
    $form_errors[$param] = ''; // Массив для хранения названия ошибки или пусто, вначале всегда пусто, используется для сообщий в верстке
    $form_values[$param] = isset($_POST[$param]) ? trim($_POST[$param]) : ''; // Массив для получения данных из форм, используется для автозаполнения в верстке
}

// Исключение - параметр приравниваем к пусто
if ($form_values['category'] == 'Выберите категорию') {
    $form_values['category'] = '';
}

// Параметры файла-изображения
$img_values = $_FILES['lot-img'] ?? []; // Сокращенная запись isset else
$img_values['img_errors'] = ''; // хранилище ошибок для поля загрузить файл
$img_values['img_url'] = $_POST['img-url'] ?? ''; // url изображения - значение определяется после загрузки, передается в POST, используется при автозаполнение
$img_values['img_loaded'] = ''; // файл загружен, если новый файл не выбран, добавляет скрытое поле POST с URL прежнего файла
$img_values['max_length'] = '64'; // Ограничим название файла до 64
$img_values['accept_type'] = ['image/gif', 'image/jpeg', 'image/png']; // особый параметр файла - типы файла изображений
$img_values['max_size'] = 1048576; // особый параметр - максимальный размер в кб (подсчет в Мб в сообщении)

$item = []; // Параметры лота, взятые из формы для запроса insert
$num_errors = 0; // Колво ошибок

/* Форма отправлена - событие нажатие кнопки */

if (isset($_POST['add-lot'])) {

    // 1 часть поля. Заполнение массива ошибок полей, если поле пусто
    foreach ($params as $param => $error) {
        if (empty($form_values[$param])) {
            $form_errors[$param] = $error;
        } elseif ($param == 'lot-name' or $param == 'message') {
            // Если поле не пусто, проверяем особые условия для каждого поля, переполнение, число
            if (strlen($form_values[$param]) > $options[$param]['max_length']) {
                $form_errors[$param] = 'Превышено число знаков' . $options[$param]['max_length'];
            } 
        } elseif ($param == 'lot-rate' or $param == 'lot-step') {
            // Проверка число или строка с числом (как поле ввода в форме, которое всегда является строкой), используйте is_numeric()!!!
            if (!is_numeric($form_values[$param])) {
                $form_errors[$param] = 'Введите число';
            } elseif (strlen($form_values[$param]) > $options[$param]['max_length']) {
                $form_errors[$param] = 'Превышено значение';
            } elseif (!is_int($form_values[$param] * 1)) {
                $form_errors[$param] = 'Введите целое число';
            } elseif ($form_values[$param] * 1 <= 0) {
                $form_errors[$param] = 'Введите положительное число';
            } elseif (strpos($form_values[$param], '0') === 0) {
                $form_errors[$param] = 'Слишком много нулей :)';
            }
        }
    }

    // Валидация времени
    if (empty($form_errors['lot-date'])) {
        $result_date = is_date_valid($form_values['lot-date']);
        if (!is_date_valid($form_values['lot-date'])) {
            $form_errors['lot-date'] = 'Время должно быть корректное ГГГГ-ММ-ДД';
        } elseif (strtotime($form_values['lot-date']) < $options['lot-date']['min_date']) {
            $form_errors['lot-date'] = 'Минимальная дата завершения: ' . date('Y-m-d', $options['lot-date']['min_date']);
        }
    }

    // 2 часть файл-изображение. Проверка имя файла пусто, значит файл не выбран
    if (empty($img_values['name'])) {
        if (!empty($img_values['img_url'])) {
            // Сообщение файл загружен. Автозаполнение (последний URL файла) - добавление скрытое поле POST с URL файла 
            $img_values['img_loaded'] = ' Файл загружен! <input type="hidden" name="img-url" value="' . $img_values['img_url'] . '">';
        } else {
            // Файл не выбран и не загружался
            $img_values['img_errors'] = ' Выберите файл';
        }
    } else {
        // Файл выбран новый или тот же. Проверка формата загружаемого файла
        $file_type = mime_content_type($img_values['tmp_name']); // MIME-тип файла, из встроенного файла magic.mime.
        if (!in_array($file_type, $img_values['accept_type'])) {
            $img_values['img_errors'] = ' Формат файла должен быть: gif, jpg, png';
        } elseif (strlen($img_values['name']) > $img_values['max_length']) {
            // Проверка длины имени файла
            $img_values['img_errors'] = ' Превышено число знаков' . $img_values['max_length'];
        } elseif ($img_values['size'] > $img_values['max_size']) {
            // Проверка размера файла
            $img_values['img_errors'] = ' Размер файла не более: ' . $img_values['max_size'] / 1048576 . 'Мб';
        }
    }

    // Загрузка файла в директорию, если выбран файл и если ошибок не найдено
    if (!empty($img_values['name']) && empty($img_values['img_errors'])) {
        $img_path = __DIR__ . '/uploads/';
        $img_values['img_url'] = '/uploads/' . $img_values['name'];

        // Проверка что файл загружен и перемещен на сервер без ошибок. Если файл уже существует, он будет перезаписан функцией move_uploaded_file
        if (move_uploaded_file($img_values['tmp_name'], $img_path . $img_values['name'])) {
            // Скрытое поле POST с URL файла и сообщение об успешной загрузке файла
            $img_values['img_loaded'] = ' Файл загружен! <input type="hidden" name="img-url" value="' . $img_values['img_url'] . '">';
        } else {
            $img_values['img_errors'] = "Ошибка загрузки файла! ";
        }
    }

    // Добавляем в массив ошибок полей ошибки файла
    $form_errors['lot-img'] = $img_values['img_errors'];
    // Результат - Cчитаем колво ошибок после нажатия кнопки и проверок
    $num_errors = 0;
    foreach ($form_errors as $value) {
        $num_errors = empty($value) ? $num_errors : ++$num_errors; 
    }
}

/* Переадресация */

// После нажатия кнопки и если колво ошибок 0
if (isset($_POST['add-lot']) && $num_errors == 0) {

    // Параметры лота
    $item = [
        'category_id' => $form_values['category'],
        'user_id' => $user['id'],
        'name' => $form_values['lot-name'],
        'description' => $form_values['message'],
        'img_url' => $img_values['img_url'],
        'price' => $form_values['lot-rate'],
        'step' => $form_values['lot-step'],
        //'ts_add' => strtotime('now + 1 hour'), // автозаполнение
        'ts_end' => $form_values['lot-date']
    ];

    // Защита от SQL-инъкция - экранирование
    foreach ($item as $key => $value) {
        $save_value = mysqli_real_escape_string($conn, $value);
        $save_item[$key] = $save_value;
    }

    // Функция добавить лот со всеми значениями
    if (insertNewItem($conn, $save_item)) {
        $last_id = mysqli_insert_id($conn); // Запрос последнего добавленного ID
        header("Location: lot.php?lot_success=true&item_id=" . $last_id); // Перенаправление на страницу добавленного лота
        exit();
    }
}

mysqli_close($conn);

// Страница для зарегистрированных или ошибка доступа
if (isset($_SESSION['user'])) {
    $page_content = include_template(
        'add-lot.php', 
        [
        'categories' => $categories,
        'form_values' => $form_values,
        'form_errors' => $form_errors,
        'img_values' => $img_values
        ]
    );
} else {
    $response_code = http_response_code(403);
    $page_content = include_template(
        'error.php', 
        [
        'categories' => $categories,
        'page_error' => '403'
        ]
    );
}

// Подложка
$layout_content = include_template(
    'layout.php',
    [
    'categories' => $categories,
    'content' => $page_content,
    'title' => 'Добавление лота'
    ]
);

$response_code;
print($layout_content);
