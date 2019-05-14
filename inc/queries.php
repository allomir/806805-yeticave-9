<?php

/* Внутренние функции для БД */

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
        WHERE email = '$email'
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
        $user['avatar_url'] // не требуется по заданию, но не может быть пусто
        // ts_created // автозаполнение
    );

    $result = mysqli_query($conn, $sql);
    if (!$result) {
        print("Ошибка MySQL: " . mysqli_error($conn)); 
    }

    return $result; // Возвращает тру или ошибка
}

/* Запрос сделать ставку */

function insertNewBet($conn, $bet) {

    $sql = sprintf("INSERT INTO bets
    (
        item_id,
        user_id,
        bet_price
    ) 
    VALUES
    ('%s', '%s', '%s')",
        $bet['item_id'],
        $bet['user_id'],
        $bet['bet_price']
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

/* Страница Лот. Запрос показать данные одного лота по id или вернуть [] */

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

/* Запрос выбрать ставки по id лота  */

function getBetsByItemID($conn, $itemID) {
    $sql = "SELECT bets.*, users.name AS user_name FROM bets
        JOIN users ON bets.user_id = users.id
        WHERE item_id = '$itemID' 
    ";

    $result = mysqli_query($conn, $sql);
    if(!$result) {
        print('Ошибка MySQL:' . mysqli_error($conn));
    }

    $itemBets = [];
    if(mysqli_num_rows($result)) {
        $itemBets = mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    return $itemBets;
}

/* Запрос выбрать ставки по id юзера  */

function getBetsByUserID($conn, $userID) {
    $sql = "SELECT bets.*, items.name AS item_name, items.img_url, ts_end, categories.name AS category, users.contacts FROM bets
        JOIN items ON bets.item_id = items.id
        JOIN categories ON items.category_id = categories.id
        JOIN users ON items.user_id = users.id
        WHERE bets.user_id = '$userID' 
    ";

    $result = mysqli_query($conn, $sql);
    if(!$result) {
        print('Ошибка MySQL:' . mysqli_error($conn));
    }

    $Bets = [];
    if(mysqli_num_rows($result)) {
        $Bets = mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    return $Bets;
}

/* Страница категории. Запрос лотов активных в выбранной категории */

function getItemsByCategory($conn, $categoryID) {
    $sql = "SELECT items.*, categories.name AS category, COUNT(item_id) AS number_bets, MAX(bet_price) AS l_price FROM items
    JOIN categories ON items.category_id = categories.id
    LEFT JOIN bets ON items.id = bets.item_id
    WHERE categories.id = '$categoryID' AND ts_end > CURRENT_TIMESTAMP -- показывать только активные
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

/* Полнотекстовый поиск по items(name,description) */

function findItemsByFText($conn, $search, $page = 1, $limit = 6) {

    $offset = ($page - 1) * $limit;

    if ($page) {
        $sql = "SELECT items.*, categories.name AS category, COUNT(item_id) AS number_bets, MAX(bet_price) AS l_price FROM items
            JOIN categories ON items.category_id = categories.id 
            LEFT JOIN bets ON items.id = bets.item_id 
            WHERE MATCH (items.name,description) AGAINST ('$search' IN BOOLEAN MODE) 
            GROUP BY items.id DESC 
            ORDER BY ts_add DESC 
            LIMIT $limit 
            OFFSET $offset 
        ";
    }
    // Если $page = 0 - то выражение без лимита и возвращает количество строк
    else {
        $sql = "SELECT items.*, categories.name AS category, COUNT(item_id) AS number_bets, MAX(bet_price) AS l_price FROM items
            JOIN categories ON items.category_id = categories.id 
            LEFT JOIN bets ON items.id = bets.item_id 
            WHERE MATCH (items.name,description) AGAINST ('$search' IN BOOLEAN MODE) 
            GROUP BY items.id DESC 
    ";
    }

    $result = mysqli_query($conn, $sql);
    if (!$result) {
        print('Ошибка MySQL: ' . mysqli_error($conn));
    }

    if(mysqli_num_rows($result) && !empty($page)) {
        $items = mysqli_fetch_all($result, MYSQLI_ASSOC);
        $items = addPricesBets($items); // Добавление последняя цена, мин ставка
        return $items; // если $page > 0 вернется 6 строк или число строк 0
    }

    return mysqli_num_rows($result); // При $page = 0 вернет количество строк
}