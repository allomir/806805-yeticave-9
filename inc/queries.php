<?php

/* Внутренние функции для БД */

// Определение окончания

function getEndingWord($number) {
    
    $mod10 = $number % 10;
    $mod100 = $number % 100;
            
    if ($mod100 >= 11 && $mod100 <= 20) {
        $word = 'ставок';
    }
    elseif ($mod10 > 5) {
        $word = 'ставок';
    }
    elseif ($mod10 == 1) {
        $word = 'ставка'; 
    }
    elseif ($mod10 >= 2 && $mod10 <= 4){
        $word = 'ставки';
    }
    else {$word = 'ставок';}

    return $word;
}

// Определение проследней цены, добавление в массив мин ставки

function addPricesBets($items) {
    foreach ($items as $key => $item) {
        if(!$item['l_price']) {
            $item['l_price'] = $item['price']; // Последняя ставка или стартовая цена
            $item['number_bets'] = 'Стартовая цена';
        }
        else {
            // Определение окончания и запись в массив
            $word = getEndingWord($item['number_bets']);
            $item['number_bets'] .= " $word";
        }
        $item['min_bet'] = $item['l_price'] + $item['step']; // Добавление поля - Мин ставка
        $items[$key] = $item; // Перезаписать строку в массиве
    }
    return $items;
}

/* Общее подключение к БД */

function getConn() {
    $conn = mysqli_connect("localhost", "root", "", "yeticave");
    mysqli_set_charset($conn, "utf8"); // первым делом кодировка
    if (!$conn) {
        print("Ошибка: Невозможно подключиться к MySQL " . mysqli_connect_error()); 
        // die('Ошибка при подключении: ' . mysqli_connect_error()); // вариант 2.
    }
    return $conn;
}

/* Общий запрос категорий из БД таблицы без защиты от sql-инъекции, тк нет переменных */

function getCategories($conn) {
    $sql = 'SELECT * FROM categories'; 
    $result = mysqli_query($conn, $sql);
    if (!$result) {
        print("Ошибка MySQL: " . mysqli_error($conn)); 
    }
    return mysqli_fetch_all($result, MYSQLI_ASSOC); 
}

/* Проверка существования категории */

function checkCategoryByID($conn, $categoryID) {
    $sql = "SELECT * FROM categories
        WHERE categories.id = '$categoryID'
    "; 
    $result = mysqli_query($conn, $sql);
    if (!$result) {
        print("Ошибка MySQL: " . mysqli_error($conn)); 
    }
    return mysqli_fetch_assoc($result);
}

/* Проверка существования email */

function checkUserByEmail($conn, $email) {
    $sql = "SELECT * FROM users 
        WHERE email='$email'
    ";
    $result = mysqli_query($conn, $sql);
    if (!$result) {
        print("Ошибка MySQL: " . mysqli_error($conn)); 
    }
    return mysqli_fetch_assoc($result);
}

/* Запрос добавить новый лот */

function insertNewItem($conn, $item) {

    $sql = sprintf("INSERT INTO items 
    (
    category_id, 
    user_id, 
    name,
    description,
    img_url,
    price,
    step,
    -- ts_add, -- автозаполнение
    ts_end
    )
    VALUES
    ('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')",  
    $item['category_id'],
    $item['user_id'],
    $item['name'],
    $item['description'],
    $item['img_url'],
    $item['price'],
    $item['step'],
    // $item['ts_add'],
    $item['ts_end']
    );

    $result = mysqli_query($conn, $sql);
    if (!$result) {
        print("Ошибка MySQL: " . mysqli_error($conn)); 
    }

    return $result; // Возвращает тру или ошибка
}

/* Запрос добавить нового пользователя */

function insertNewUser($conn, $user) {
echo 22222;
    $sql = sprintf("INSERT INTO users 
    (
        email, 
        password, 
        name, 
        contacts,
        avatar_url -- не требуется по заданию, но не может быть пусто
        -- ts_created -- автозаполнение
    )
    VALUES
    ('%s', '%s', '%s', '%s', '%s')",  
        $user['email'], 
        $user['password'], 
        $user['name'], 
        $user['contacts'],
        '/img/user.png' // не требуется по заданию, но не может быть пусто
        // ts_created // автозаполнение
    );

    $result = mysqli_query($conn, $sql);
    if (!$result) {
        print("Ошибка MySQL: " . mysqli_error($conn)); 
    }

    return $result; // Возвращает тру или ошибка
}

/* Главная стр. Запрос показать активные лоты (врямя окончания не вышло), сортировать от последнего добавленного, не более 9 */

function getItems($conn) {
    $sql = "SELECT items.*, categories.name AS category, COUNT(item_id) AS number_bets, MAX(bet_price) AS l_price FROM items
        JOIN categories ON items.category_id = categories.id
        LEFT JOIN bets ON items.id = bets.item_id
        WHERE ts_end > CURRENT_TIMESTAMP -- показывать только активные
        GROUP BY items.id DESC
        ORDER BY ts_add DESC 
        LIMIT 9
    "; 
    $result = mysqli_query($conn, $sql);
    if (!$result) {
        print("Ошибка MySQL: " . mysqli_error($conn)); 
    }

    $items = [];
    if(mysqli_num_rows($result)) {
        $items = mysqli_fetch_all($result, MYSQLI_ASSOC);
        $items = addPricesBets($items); // Добавление последняя цена, мин ставка
    }
    return  $items;
}

/* Страница Лот. Запрос показать данные одного лота по id или вернуть 0 */

function getItemByID($conn, $itemID) {
    $sql = "SELECT items.*, categories.name AS category, COUNT(item_id) AS number_bets, MAX(bet_price) AS l_price FROM items
        JOIN categories ON items.category_id = categories.id
        LEFT JOIN bets ON items.id = bets.item_id
        WHERE items.id = '$itemID' -- AND ts_end > CURRENT_TIMESTAMP -- показывать только активные
        GROUP BY items.id DESC
    ";
    $result = mysqli_query($conn, $sql);
    if (!$result) {
        print("Ошибка MySQL: " . mysqli_error($conn)); 
    }

    $item = []; 
    if(mysqli_num_rows($result)) {
        $item = mysqli_fetch_assoc($result); // Ассоциативный массив 
        if(!$item['l_price']) {
            $item['l_price'] = $item['price']; // Последняя ставка или стартовая цена
        }
        $item['min_bet'] = $item['l_price'] + $item['step']; // Добавление поля - Мин ставка
    } 
    return $item;
}

/* Страница категории. Запрос лотов активных в выбранной категории */

function getItemsByCategory($conn, $categoryID) {
    $sql = "SELECT items.*, categories.name AS category, COUNT(item_id) AS number_bets, MAX(bet_price) AS l_price FROM items
    JOIN categories ON items.category_id = categories.id
    LEFT JOIN bets ON items.id = bets.item_id
    WHERE categories.id = '$categoryID' -- AND ts_end > CURRENT_TIMESTAMP -- показывать только активные
    GROUP BY items.id DESC
    ORDER BY ts_add DESC
    "; 
    $result = mysqli_query($conn, $sql);
    if (!$result) {
        print("Ошибка MySQL: " . mysqli_error($conn)); 
    }

    $items = [];
    if(mysqli_num_rows($result)) {
        $items = mysqli_fetch_all($result, MYSQLI_ASSOC);
        $items = addPricesBets($items); // Добавление последняя цена, мин ставка
    } 
    return $items;
}
