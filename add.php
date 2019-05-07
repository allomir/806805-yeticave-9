<?php

require('inc/function.php'); // функции
require('inc/queries.php'); // Запросы и подключение
require('helpers.php'); // шаблонизатор

$conn = getConn(); // Подключение к БД
$categories = getCategories($conn); // Запрос Показать Таблицу Категории

/*  Глобальные переменные
Список параметров 
_POST['lot-name']; 
_POST['category'];
_POST['message'];
_POST['lot-rate'];
_POST['lot-step'];
_POST['lot-date'];
_POST['add_lot']; // кнопка добавить лот
_FILES['lot-img']['name']; // Оригинальное имя файла на компьютере клиента
_FILES['lot-img']['type']; // Mime-тип файла, если браузер предоставил информацию, например "image/gif".
_FILES['lot-img']['size'] // Размер в байтах принятого файла.
_FILES['lot-img']['tmp_name'] // Временное имя, с которым принятый файл был сохранен на сервере.
*/

// параметры - название полей, кроме изображения, и название ошибок, если поле не заполнено
$params = [
    'lot-name' => 'Введите наименование лота', 
    'category' => 'Выберите категорию', 
    'message' => 'Напишите описание лота',
    'lot-rate' => 'Введите начальную цену',
    'lot-step' => 'Введите шаг ставки',
    'lot-date' => 'Введите дату завершения торгов'
];

// Особые параметры полей, указываются и проверяются индвивидуально
$AAA = ['maxlen' => '255']; // lot-name
$CCC = ['maxlen' => '1024']; // message
$DDD = ['maxlen' => '7']; // lot-rate
$EEE = ['maxlen' => '7']; // lot-step
$FFF = ['mintime' => strtotime('tomorrow + 1 days')]; // lot-date

// Загрузка данных полей из глобальной переменной POST, если нет данных то пусто 
foreach ($params as $param => $error) {
    $formErrors[$param] = ''; // Массив для хранения названия ошибки или пусто, вначале всегда пусто, используется для сообщий в верстке
    $formData[$param] = $_POST[$param] ?? ''; // Массив для получения данных из форм, используется для автозаполнения в верстке 
}

If ($formData['category'] == 'Выберите категорию') {$formData['category'] = '';} // Исключение - параметр приравниваем к пусто

// $formData['lot-date'] = '2019-05-10'; // пример даты

// Параметры файла-изображения

$imgData = $_FILES['lot-img'] ?? []; 
$imgData['mess_err'] = ''; // хранилище ошибок и сообщений для поля загрузить файл
$imgData['maxlen'] = '64'; // Ограничим название файла до 64
$imgData['accept_type'] = ['image/gif', 'image/jpeg', 'image/png']; // типы файла изображений
$imgData['max_size'] = 1048576; // максимальный размер в кб
// url изображения - значение устанавливается после успешной загрузки, и передается в POST
$imgData['img_url'] = $img_url = ''; //копия для передачи в массиве


/********************************** Форма отправлена *************************************/


// Проверка - событие нажатие кнопки
if (isset($_POST['add_lot']) ) {


    /* 1часть. Заполнение массива ошибок, если поле пусто */
    foreach ($params as $param => $error) {
        if (empty($formData[$param])) {
            $formErrors[$param] = $error;
        }
        // Если поле не пусто, проверяем особые условия для каждого поля, переполнение, число
        else{
            if ($param == 'lot-name') {
                if (strlen($formData[$param]) > $AAA['maxlen'] ) {
                    $formErrors[$param] = 'Превышена максимальная длина' . $AAA['maxlen'];
                }
            }
            elseif ($param == 'message') {
                if (strlen($formData[$param]) > $CCC['maxlen'] ) {
                    $formErrors[$param] = 'Превышена максимальная длина' . $CCC['maxlen'];
                }
            }
            elseif ($param == 'lot-rate') {
                if (!is_numeric($formData[$param])) {
                    $formErrors[$param] = 'Цена должна быть числом';
                }
                elseif (strlen($formData[$param]) > $DDD['maxlen'] ) {
                    $formErrors[$param] = 'Превышено значение числа';
                }
                elseif ($formData[$param] < 0) {
                    $formErrors[$param] = 'Введите положительное число';
                }
            }
            elseif ($param == 'lot-step') {
                if (!is_numeric($formData[$param])) {
                    $formErrors[$param] = 'Шаг ставки должен быть числом';
                }
                elseif (strlen($formData[$param]) > $EEE['maxlen'] ) {
                    $formErrors[$param] = 'Превышено значение числа';
                }
                elseif ($formData[$param] < 0) {
                    $formErrors[$param] = 'Введите положительное число';
                }
            }
        }
    }

    /* 2часть. Валидация времени, функция из helpers */

    if (!empty($formData['lot-date'])) {
        $result_date = is_date_valid($formData['lot-date']);
        if (!is_date_valid($formData['lot-date'])) {
            $formErrors['lot-date'] = 'Время должно быть корректное ГГГГ-ММ-ДД';
        }
        elseif (strtotime($formData['lot-date']) < $FFF['mintime']) {
            $formErrors['lot-date'] = 'Минимальное время завершения: ' . date('Y-m-d', $FFF['mintime']);
        }
    }

    /* 3часть. Проверка файла и загрузка в директорию */

    // Если имя файла нет - файл не выбран или был загружен до этого, но форма заполнена с ошибкой
    if (empty($imgData['name'])) {
        // Проверка - если файл не выбран, используется файл из предыдущей загрузки 
        if (isset($_POST['img-url'])) {
            // Скрытое поле POST с URL файла и сообщение об успешной загрузке файла в предыдущий раз
            $imgData['mess_err'] = '<input type="hidden" name="img-url" value="' . $_POST['img-url'] . '">';
            $imgData['img_url'] = $img_url = $_POST['img-url'];
        }
        else {
            $imgData['mess_err'] = ' Выберите файл';
        }
    }
    // Если выбран новый файл, старый файл удаляется (удаление не сделано). Выполняется валидация файла - тип и макс. размер
    else {
        
        if (strlen($imgData['name']) > $imgData['maxlen']) {
            $imgData['mess_err'] = ' Название файла не более' . $imgData['maxlen'];
        }
        elseif ($imgData['size'] > $imgData['max_size']) {
            $imgData['mess_err'] = ' Размер файла не более: ' . $imgData['max_size'] / 1048576 . 'Мб';
        }

        // Сравниваем исходные параметры с типом загружаемого файла
        $finfo = finfo_open(FILEINFO_MIME_TYPE); // встроенные PHP MAGIC file по умолчанию
        // $file_type = finfo_file($finfo, $imgData['tmp_name']); // определяем тип файла с именем во временной директории (2 вариант)
        $file_type = mime_content_type($imgData['tmp_name']); // MIME-тип файла, используя для определения информацию из файла magic.mime.
        finfo_close($finfo);

        $result_type = 0;
        foreach ($imgData['accept_type'] AS $accept_type) {
            if ($accept_type == $file_type) {
                $result_type++; 
            }
        }
        if (!$result_type) {
            $imgData['mess_err'] = ' Формат файла должен быть: gif, jpg, png';
        }

        // Если ошибок не найдено, Перемещение файла в директорию, даем сообщение файл загружен - URL файла сохраняется скрытым полем в POST.
        if (empty($imgData['mess_err'])) {
            $img_path = __DIR__ . '/uploads/';
            $imgData['img_url'] = $img_url = '/uploads/' . $imgData['name'];

            // Проверка что файл загружен и перемещен на сервер без ошибок, те он там есть
            // Примечание Если файл уже существует, он будет перезаписан функцией move_uploaded_file
            if(move_uploaded_file($imgData['tmp_name'], $img_path . $imgData['name'])) {
                
                // Скрытое поле POST с URL файла и сообщение об успешной загрузке файла
                $imgData['mess_err'] = '<input type="hidden" name="img-url" value="' . $img_url . '">';
            }
            else { 
                $imgData['mess_err'] = "<h3>Ошибка! Не удалось загрузить файл на сервер!</h3>";
            }
        }
    }

    /****************************************** Переадресация ***************************************/

    // Условие все поля заполнены правильно - $formError пусто и $img_url содержит URL 
    $number_err = 0;
    foreach ($formErrors as $value) {
        if (!empty($value)) {
            $number_err++;
        }
    }
    if (!$number_err && !empty($img_url)) {

        // Параметры лота, юзер вводится вручную на данном этапе
        $item_params = $arr = [
            'category_id' => $formData['category'],
            'user_id' => '1',
            'name' => $formData['lot-name'],
            'description' => $formData['message'],
            'img_url' => $img_url,
            'price' => $formData['lot-rate'],
            'step' => $formData['lot-step'],
            //'ts_add' => strtotime('now + 1 hour'), // ошибка - Incorrect datetime value: '1557159721' 
            'ts_end' => $formData['lot-date']
        ];

        // Защита от SQL-инъкция - экранирование
        foreach ($arr as $key => $value) {
            $saveValue = mysqli_real_escape_string($conn, $value); 
            $saveArr[$key] = $saveValue;
        }

        // Функция добавить лот со всеми значениями
        if (addItem($conn, $saveArr)) {
            $last_id = mysqli_insert_id($conn);

            // Перенаправление
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                header("Location: /lot.php?success=true&itemID=" . $last_id);
            }
                
        }

    }
}

/* Шаблонизатор */

$page_name = 'Добавление лота';

$page_content = include_template('add-lot.php', [
    'categories' => $categories, 
    'formData' => $formData,
    'formErrors' => $formErrors,
    'imgData' => $imgData
    
]);

$layout_content = include_template('layout.php', [
    'is_auth' => $is_auth,
    'user_name' => $user_name, 
    'categories' => $categories, 
    'content' => $page_content, 
    'title' => $page_name,
    'response_code' => $response_code
]);

print($layout_content);