<?php

require('inc/function.php'); // функции
require('inc/queries.php'); // Запросы и подключение
require('inc/helpers.php'); // шаблонизатор
$response_code = '';

session_start();
$user_name = isset($_SESSION['user']) ? $_SESSION['user']['name'] : 0;

$conn = getConn(); // Подключение к БД
$categories = getCategories($conn); // Запрос Показать Таблицу Категории

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
$specpars = [
    'lot-name' => ['maxlen' => '255'], 
    'message' => ['maxlen' => '1024'], 
    'lot-rate' => ['maxlen' => '7'], 
    'lot-step' => ['maxlen' => '7'], 
    'lot-date' => ['mindate' => strtotime('tomorrow + 1 days')] // lot-date
];

// Загрузка данных полей из глобальной переменной POST, если нет данных то пусто 
foreach ($params as $param => $error) {
    $formErrors[$param] = ''; // Массив для хранения названия ошибки или пусто, вначале всегда пусто, используется для сообщий в верстке
    $formData[$param] = $_POST[$param] ?? ''; // Массив для получения данных из форм, используется для автозаполнения в верстке 
}
If ($formData['category'] == 'Выберите категорию') {$formData['category'] = '';} // Исключение - параметр приравниваем к пусто

// Параметры файла-изображения
$imgData = $_FILES['lot-img'] ?? []; // Сокращенная запись isset else
$imgData['img_err'] = ''; // хранилище ошибок для поля загрузить файл
$imgData['img_url'] = $_POST['img-url'] ?? ''; // url изображения - значение определяется после загрузки, передается в POST, используется при автозаполнение
$imgData['img_post'] = ''; // автозаполнение и файл загружен, если новый файл не выбран, добавляет скрытое поле POST с URL прежнего файла
$imgData['maxlen'] = '64'; // Ограничим название файла до 64
$imgData['accept_type'] = ['image/gif', 'image/jpeg', 'image/png']; // особый параметр файла - типы файла изображений
$imgData['maxsize'] = 1048576; // особый параметр - максимальный размер в кб (подсчет в Мб в сообщении)

$item = []; // Параметры лота для инсерта
$number_err = 0; // Колво ошибок
/********************************** Форма отправлена *************************************/


// Проверка - событие нажатие кнопки
if (isset($_POST['add_lot'])) {

    /* 1 часть. Заполнение массива ошибок полей, если поле пусто */

    foreach ($params as $param => $error) {
        if (empty($formData[$param])) {
            $formErrors[$param] = $error;
        }
        
        // Если поле не пусто, проверяем особые условия для каждого поля, переполнение, число
        elseif ($param == 'lot-name' OR $param == 'message') {
            if (strlen($formData[$param]) > $specpars[$param]['maxlen'] ) {
                    $formErrors[$param] = 'Превышено число знаков' . $specpars[$param]['maxlen'];
            }
        }
        elseif ($param == 'lot-rate' OR $param == 'lot-step') {
            // Проверка число или строка с числом (как поле ввода в форме, которое всегда является строкой), используйте is_numeric()!!!
            if (!is_numeric($formData[$param])) {
                    $formErrors[$param] = 'Введите число';
            }
            elseif (strlen($formData[$param]) > $specpars[$param]['maxlen'] ) {
                $formErrors[$param] = 'Превышено значение';
            }
            elseif (!is_int($formData[$param] * 1)) {
                    $formErrors[$param] = 'Введите целое число'; 
            }
            elseif ($formData[$param] <= 0 ) {
                    $formErrors[$param] = 'Введите положительное число';
            }
            elseif (strpos($formData[$param], '0') === 0) {
                    $formErrors[$param] = 'Слишком много нулей :)';
            }
        }
    }

    /* 2 часть. Валидация времени, функция из helpers */

    if (empty($formErrors['lot-date'])) {
        $result_date = is_date_valid($formData['lot-date']);
        if (!is_date_valid($formData['lot-date'])) {
            $formErrors['lot-date'] = 'Время должно быть корректное ГГГГ-ММ-ДД';
        }
        elseif (strtotime($formData['lot-date']) < $specpars['lot-date']['mindate']) {
            $formErrors['lot-date'] = 'Минимальная дата завершения: ' . date('Y-m-d', $specpars['lot-date']['mindate']);
        }
    }

    /* 3 часть. Проверка файла и валидация */

    // Файл не выбран - если имя файла пусто 
    if (empty($imgData['name'])) {
        // Автозаполнение. Использовать файл из предыдущей загрузки, если файл загружался - есть URL из POST 
        if (!empty($imgData['img_url'])) {
            // Скрытое поле POST с URL файла и сообщение об успешной загрузке файла в предыдущий раз
            $imgData['img_post'] = ' Файл загружен! <input type="hidden" name="img-url" value="' . $imgData['img_url'] . '">';
        }
        // Файл не выбран и не загружался
        else {
            $imgData['img_err'] = ' Выберите файл';
        }
    }
    // Файл выбран новый или тотже, старый файл удаляется (удаление не требуется). Выполняется валидация файла - тип и макс. размер
    else {
        // Проверка формата загружаемого файла
        $file_type = mime_content_type($imgData['tmp_name']); // MIME-тип файла, из встроенного файла magic.mime.
        $correct_type = 0;
        foreach ($imgData['accept_type'] AS $accept_type) {
            if ($accept_type == $file_type) {
                $correct_type++; 
            }
        }
        // Если совпадений нет
        if (empty($correct_type)) {
            $imgData['img_err'] = ' Формат файла должен быть: gif, jpg, png';
        }
        // Проверка длины имени файла
        elseif (strlen($imgData['name']) > $imgData['maxlen']) {
            $imgData['img_err'] = ' Превышено число знаков' . $imgData['maxlen'];
        }
        // Проверка размера файла
        elseif ($imgData['size'] > $imgData['maxsize']) {
            $imgData['img_err'] = ' Размер файла не более: ' . $imgData['maxsize'] / 1048576 . 'Мб';
        }
    }

    /* 4 часть. Загрузка файла в директорию, если ошибок не найдено */

    // Если выбран файл и если ошибок не найдено
    if (!empty($imgData['name']) && empty($imgData['img_err'])) {
        $img_path = __DIR__ . '/uploads/';
        $imgData['img_url'] = '/uploads/' . $imgData['name'];

        // Проверка что файл загружен и перемещен на сервер без ошибок, те он там есть
        // * Примечание Если файл уже существует, он будет перезаписан функцией move_uploaded_file
        if(move_uploaded_file($imgData['tmp_name'], $img_path . $imgData['name'])) {

            // Скрытое поле POST с URL файла и сообщение об успешной загрузке файла
            $imgData['img_post'] = ' Файл загружен! <input type="hidden" name="img-url" value="' . $imgData['img_url'] . '">';
        }
        else { 
            $imgData['img_err'] = "Ошибка загрузки файла! ";
        }
    }

    /* 5 часть. Колво ошибок */

    // Добавляем в массив ошибок полей формы ошибки файла и загрузки файла
    $formErrors['lot-img'] = $imgData['img_err'];
    // Результат - Cчитаем колво ошибок после нажатия кнопки и проверок
    $number_err = 0; 
    foreach ($formErrors as $value) {
        if (!empty($value)) {
            $number_err++;
        }
    }
}

/****************************************** Переадресация *****************************************/

// После нажатия кнопки и Колво ошибок 0 
if (isset($_POST['add_lot']) && $number_err == 0) {

    // Параметры лота
    $item = [
        'category_id' => $formData['category'],
        'user_id' => $user_id,
        'name' => $formData['lot-name'],
        'description' => $formData['message'],
        'img_url' => $imgData['img_url'],
        'price' => $formData['lot-rate'],
        'step' => $formData['lot-step'],
        //'ts_add' => strtotime('now + 1 hour'), // ошибка - Incorrect datetime value: '1557159721' 
        'ts_end' => $formData['lot-date']
    ];

    // Защита от SQL-инъкция - экранирование
    foreach ($item as $key => $value) {
        $saveValue = mysqli_real_escape_string($conn, $value); 
        $saveItem[$key] = $saveValue;
    }

    // Функция добавить лот со всеми значениями
    if (insertNewItem($conn, $saveItem)) {
        
        // Запрос последнего добавленного ID
        $last_id = mysqli_insert_id($conn);
        mysqli_close($conn);

        // Перенаправление на страницу добавленного лота
        header("Location: lot.php?success=true&itemID=" . $last_id);
    }
}

mysqli_close($conn);

/* Шаблонизатор */

$page_name = 'Добавление лота';

if (isset($_SESSION['user'])) {
    $page_content = include_template('add-lot.php', [
        'categories' => $categories, 
        'formData' => $formData,
        'formErrors' => $formErrors,
        'imgData' => $imgData
        
    ]);
} else {
    $response_code = http_response_code(403);
    $page_content = 'Для просмотра Зарегистрируйтесь' ;
}



$layout_content = include_template('layout.php', [
    'user_name' => $user_name, 
    'categories' => $categories, 
    'content' => $page_content, 
    'title' => $page_name,
    'page_style_main' => ''
]);

$response_code;
print($layout_content);