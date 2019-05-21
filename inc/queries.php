<?php

/** 
 * #1 Общее подключение к БД
 * Примечание: mysqli_set_charset($conn, "utf8");
 * @return link соединение с БД
 */
function getConn()
{
    $conn = mysqli_connect("localhost", "root", "", "yeticave");
    mysqli_set_charset($conn, "utf8"); 

    if (!$conn) {
        print("Ошибка: Невозможно подключиться к MySQL " . mysqli_connect_error());
    }
    
    return $conn;
}

/** 
 * #2 Все категории - поля таблицы categories
 * @param link   $conn соединение с БД
 * @return array двууровневый массив с полями из таблицы БД или []
 */
function getCategories($conn)
{
    $sql = 'SELECT * FROM categories';

    $result = mysqli_query($conn, $sql);
    if (!$result) {
        print("Ошибка MySQL: " . mysqli_error($conn));
    }

    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

/** 
 * #3 Проверка существования категории по id - поля таблицы categories
 * @param link   $conn соединение с БД
 * @param int    $category_id уникальный ключ (id) категории в виде числа
 * @return array ассоциат массив с полями из таблицы БД или []
 */
function checkCategoryByID($conn, $category_id)
{
    $sql = "SELECT * FROM categories
        WHERE categories.id = '$category_id'
    ";

    $result = mysqli_query($conn, $sql);
    if (!$result) {
        print("Ошибка MySQL: " . mysqli_error($conn));
    }

    return mysqli_fetch_assoc($result);
}

/** 
 * #4 Проверка существования адреса эл. почты - поля таблицы users
 * @param link   $conn соединение с БД
 * @param string $email валидный адрес эл. почты в виде строки
 * @return array ассоциат массив с полями из таблицы БД или []
 */
function checkUserByEmail($conn, $email)
{
    $sql = "SELECT * FROM users 
        WHERE email = '$email'
    ";

    $result = mysqli_query($conn, $sql);
    if (!$result) {
        print("Ошибка MySQL: " . mysqli_error($conn));
    }
    
    return mysqli_fetch_assoc($result);
}

/** 
 * #5 Добавить в таблицу items новый лот и его данные
 * @param link  $conn соединение с БД
 * @param array $item поля таблицы и их значения в виде ассоциат. массива
 * @return boolean true если записи успешно добавлена 
 */
function insertNewItem($conn, $item)
{
    $sql = sprintf(
        "INSERT INTO items 
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

/** 
 * #6 Добавить в таблицу users нового пользователя и его данные
 * @param link  $conn соединение с БД
 * @param array $user поля таблицы и их значения в виде ассоциат. массива
 * @return boolean true если записи успешно добавлена 
 */
function insertNewUser($conn, $user)
{
    $sql = sprintf(
        "INSERT INTO users 
            (
            email, 
            password, 
            name, 
            contacts,
            avatar_url -- но не может быть пусто
            -- ts_created -- автозаполнение
            )
        VALUES
            ('%s', '%s', '%s', '%s', '%s')",
        $user['email'],
        $user['password'],
        $user['name'],
        $user['contacts'],
        $user['avatar_url'] // не может быть пусто
        // ts_created // автозаполнение
    );

    $result = mysqli_query($conn, $sql);
    if (!$result) {
        print("Ошибка MySQL: " . mysqli_error($conn));
    }

    return $result; // Возвращает тру или ошибка
}

/** 
 * #7 Добавить в таблицу bets ставки и ее данные
 * @param link  $conn соединение с БД
 * @param array $bet поля таблицы и их значения в виде ассоциат. массива
 * @return boolean true если запись успешно добавлена 
 */
function insertNewBet($conn, $bet)
{
    $sql = sprintf(
        "INSERT INTO bets
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


/** 
 * #8 Активные лоты из таблицы items на главной стр., последние 9шт, категорию из categories, а также колво ставок из bets и макс. цену из bets каждого лота
 * Примечание: используется ф. addPricesBets, определяет последнюю цену и считает размер мин. ставки
 * 
 * @param link  $conn соединение с БД
 * @return array двууровневый массив с полями из таблицы БД или []
 */
function getItems($conn)
{
    $sql = "SELECT items.*, categories.name AS category, COUNT(item_id) AS number_bets, MAX(bet_price) AS last_price FROM items
        JOIN categories ON items.category_id = categories.id
        LEFT JOIN bets ON items.id = bets.item_id
        WHERE ts_end > CURRENT_TIMESTAMP -- показывать только активные
        GROUP BY items.id 
        ORDER BY ts_add DESC 
        LIMIT 9
    ";

    $result = mysqli_query($conn, $sql);
    if (!$result) {
        print("Ошибка MySQL: " . mysqli_error($conn));
    }

    $items = [];
    if (mysqli_num_rows($result)) {
        $items = mysqli_fetch_all($result, MYSQLI_ASSOC);
        $items = addPricesBets($items); // Добавление последняя цена, мин ставка
    }

    return  $items;
}

/** 
 * #9 Данные одного лота из таблицы items по его id на стр. Лот , категорию из categories, колво ставок из bets и макс. цену из bets каждого лота
 * Примечание: внутри функции определяется последня цена и считается размер мин. ставки, значения передаются в массив
 * 
 * @param link  $conn соединение с БД
 * @param int  $item_id значение id лота из таблицы items в виде числа
 * @return array $item ассоциат массив с полями из таблицы БД или []
 */
function getItemByID($conn, $item_id)
{
    $sql = "SELECT items.*, categories.name AS category, COUNT(item_id) AS number_bets, MAX(bet_price) AS last_price FROM items
        JOIN categories ON items.category_id = categories.id
        LEFT JOIN bets ON items.id = bets.item_id
        WHERE items.id = '$item_id' -- AND ts_end > CURRENT_TIMESTAMP -- показывать только активные
        GROUP BY items.id DESC
    ";

    $result = mysqli_query($conn, $sql);
    if (!$result) {
        print("Ошибка MySQL: " . mysqli_error($conn));
    }

    $item = [];
    if (mysqli_num_rows($result)) {
        $item = mysqli_fetch_assoc($result); // Ассоциативный массив
        if (!$item['last_price']) {
            $item['last_price'] = $item['price']; // Последняя ставка или стартовая цена
        }
        $item['min_bet'] = $item['last_price'] + $item['step']; // Добавление поля - Мин ставка
    }

    return $item;
}

/** 
 * #10 Ставки одного лота по его id на стр. Лот, имя пользователя по его id из bets и пользователей, сделавших ставку из users
 * @param link   $conn соединение с БД
 * @param int    $item_id уникальный ключ (id) лота в виде числа
 * @param string $order принимает значения DESC или ASC, влияет на определение пользователя с последней ставкой
 * @return array $itemBets двууровневый массив с полями из таблицы БД или []
 */
function getBetsByItemID($conn, $item_id, $order)
{
    $sql = "SELECT bets.*, users.name AS user_name FROM bets
        JOIN users ON bets.user_id = users.id
        WHERE item_id = '$item_id' 
        ORDER BY ts_betted $order
    ";

    $result = mysqli_query($conn, $sql);
    if (!$result) {
        print('Ошибка MySQL:' . mysqli_error($conn));
    }

    $itemBets = [];
    if (mysqli_num_rows($result)) {
        $itemBets = mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    return $itemBets;
}

/** 
 * #11 Ставки одного пользователя по его id на стр. Мои ставки из bets, название и изображение лота из items, категория лота из categories, контакты владельца лота из users
 * @param link   $conn соединение с БД
 * @param int    $item_id уникальный ключ (id) пользователя после аутентификациии в виде числа
 * @return array $itemBets двууровневый массив с полями из таблицы БД или []
 */
function getBetsByUserID($conn, $user_id)
{
    $sql = "SELECT bets.*, items.name AS item_name, items.img_url, ts_end, categories.name AS category, users.contacts FROM bets
        JOIN items ON bets.item_id = items.id
        JOIN categories ON items.category_id = categories.id
        JOIN users ON items.user_id = users.id
        WHERE bets.user_id = '$user_id'
        ORDER BY ts_betted DESC
    ";

    $result = mysqli_query($conn, $sql);
    if (!$result) {
        print('Ошибка MySQL:' . mysqli_error($conn));
    }

    $bets = [];
    if (mysqli_num_rows($result)) {
        $bets = mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    return $bets;
}


/** 
 * #12 Все активные лоты в выбранной категории по id категории из таблицы items, категорию из categories, колво ставок и макс. цену из bets каждого лота
 * Примечание: аналогичен полнотекстовому поиску, кроме условия, должен содержать limit и page (не требуется по заданию).
 * 
 * @param link   $conn соединение с БД
 * @param int    $category_id уникальный ключ (id) категории в виде числа
 * @return array $items двууровневый массив с полями из таблицы БД или []
 */
function getItemsByCategory($conn, $category_id)
{
    $sql = "SELECT items.*, categories.name AS category, COUNT(item_id) AS number_bets, MAX(bet_price) AS last_price FROM items
        JOIN categories ON items.category_id = categories.id
        LEFT JOIN bets ON items.id = bets.item_id
        WHERE categories.id = '$category_id' AND ts_end > CURRENT_TIMESTAMP -- показывать только активные
        GROUP BY items.id 
        ORDER BY ts_add DESC
    ";

    $result = mysqli_query($conn, $sql);
    if (!$result) {
        print("Ошибка MySQL: " . mysqli_error($conn));
    }

    $items = [];
    if (mysqli_num_rows($result)) {
        $items = mysqli_fetch_all($result, MYSQLI_ASSOC);
        $items = addPricesBets($items); // Добавление последняя цена, мин ставка
    }

    return $items;
}

/** 
 * #13 Полнотекстовый поиск по полям items(name,description) в таблице items
 * 
 * Ограничения:
 * Если $limit = 0 или не задан то возвращает общее число строк (по умолчанию)
 * Если $limit = число то возвращает число строк 
 * $page - определяет значение оффсета, должно быть больше или равно 1 (по умолчанию)
 * 
 * @param mysqli $conn соединение с БД
 * @param string $search слова из строки поиска в виде строки 
 * @param int $limit макс колво показываемых лотов на одной странице
 * @param int $page номер страницы, используется для определения offset
 * @return array двууровневый массив с полями из таблицы БД или []
 */
function findItemsByFText($conn, $search, $limit = 0, $page = 1)
{
    $offset = ($page - 1) * $limit;

    $sql = "SELECT i.id";
    if ($limit) {
        $sql .= ", i.name, img_url, ts_end, i.step, i.price, categories.name AS category,"
            . " COUNT(item_id) AS number_bets, MAX(bet_price) AS last_price";
    }
    $sql .= " FROM items i";
    if ($limit) {
        $sql .= " JOIN categories ON i.category_id = categories.id"
            . " LEFT JOIN bets ON i.id = bets.item_id ";
    }
    $sql .= " WHERE MATCH (i.name, description) AGAINST ('$search' IN BOOLEAN MODE)";
    if ($limit) {
        $sql .= " GROUP BY i.id"
            . " ORDER BY ts_add DESC "
            . " LIMIT $limit"
            . " OFFSET $offset";
    }

    $result = mysqli_query($conn, $sql);
    if (!$result) {
        print('Ошибка MySQL: ' . mysqli_error($conn));
    }

    $items = [];
    if (mysqli_num_rows($result)) {
        $items = mysqli_fetch_all($result, MYSQLI_ASSOC);
        $items = !empty($limit) ? addPricesBets($items) : $items; // Добавление последняя цена, мин ставка
    }

    return $items;
}

/** 
 * # Последняя цена, мин ставки, колво ставок. Перезапись и добавление 3х параметров для карточек лотов
 * Примечание: используется ф. getEndingWord для поля колво ставок - number_bets,  формат '3 ставки', если число ставок (или макс. цена) не пусто
 * 
 * @param array $items двумерный массив с полями 'last_price', 'number_bets', добавляется поле 'min_bet'
 * @return array двууровневый массив с полями из таблицы БД или []
 */
function addPricesBets($items)
{
    foreach ($items as $key => $item) {

        if (!$item['last_price']) {
            $item['last_price'] = $item['price']; // Последняя ставка или стартовая цена
            $item['number_bets'] = 'Стартовая цена';
        } else {
            // Определение окончания и запись в массив (по умолчанию - ставка)
            $word = getEndingWord($item['number_bets']);
            $item['number_bets'] .= " $word";
        }

        $item['min_bet'] = $item['last_price'] + $item['step']; // Добавление поля - Мин ставка
        $items[$key] = $item; // Перезаписать строку в массиве
    }

    return $items;
}