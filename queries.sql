/* 1часть. Наполнение таблиц информацией  */

USE yeticave;

-- нестандартный однострочный ввод значений в поля, подобно UPDATE

INSERT INTO categories SET         
    symbol = 'boards', 
    title = 'Доски и лыжи'
;

-- стандартный ввод мнострочный, остальные значения 5шт

INSERT INTO categories              
    (symbol, title)
VALUES
    ('attachment', 'Крепления'),
    ('boots','Ботинки'),
    ('clothing','Одежда'),
    ('tools','Инструменты'),
    ('other','Разное')
;

INSERT INTO users
    (email, password, name, contacts, avatar_url, ts_created)
VALUES
    ('elon@gmail.com', 'spacex', 'Elon Musk', 'USA, California DC, Gigafactory-1', 'fotox00777.png', DEFAULT),
    ('bill@hotmail.ru', 'microsoft', 'Bill Gates', 'Paolo Alto', 'foto01010.jpg', DEFAULT)
;

INSERT INTO items
    (
        category, 
        user_id, 
        name, 
        description, 
        img_url, 
        price, 
        bet_step, 
        ts_add, -- DEFAULT
        ts_end
    )
VALUES
    (
        '1', 
        '1', 
        '2014 Rossignol District Snowboard', 
        'Легкий маневренный сноуборд, готовый дать жару в любом парке, растопив снег мощным 
        щелчкоми четкими дугами. Стекловолокно Bi-Ax, уложенное в двух направлениях, 
        наделяет этот снаряд отличной гибкостью и отзывчивостью, а симметричная геометрия в 
        сочетании с классическим прогибом кэмбер позволит уверенно держать высокие скорости. 
        А если к концу катального дня сил совсем не останется, просто посмотрите на Вашу доску и 
        улыбнитесь, крутая графика от Шона Кливера еще никого не оставляла равнодушным.',
        'lot-1.jpg', 
        '10999', 
        '1000', 
        DEFAULT, 
        '2019-05-01'
    ),
    (
        '1', 
        '2', 
        'DC Ply Mens 2016/2017 Snowboard', 
        'Легкий маневренный сноуборд, готовый дать жару в любом парке, растопив снег мощным ... ', 
        'lot-2.jpg', 
        '159999', 
        '10000', 
        DEFAULT, 
        '2019-05-01'
    ),
    (
        '2', 
        '1', 
        'Крепления Union Contact Pro 2015 года размер L/XL', 
        'Легкий маневренный сноуборд, готовый дать жару в любом парке, растопив снег мощным ... ', 
        'lot-3.jpg', 
        '8000', 
        '500', 
        DEFAULT, 
        '2019-05-01'
    ),
    (
        '3', 
        '2', 
        'Ботинки для сноуборда DC Mutiny Charocal', 
        'Легкий маневренный сноуборд, готовый дать жару в любом парке, растопив снег мощным ... ', 
        'lot-4.jpg', 
        '10999', 
        '2000', 
        DEFAULT, 
        '2019-05-01'
    ),
    (
        '4', 
        '1', 
        'Куртка для сноуборда DC Mutiny Charocal', 
        'Легкий маневренный сноуборд, готовый дать жару в любом парке, растопив снег мощным ... ', 
        'lot-5.jpg', 
        '7500', 
        '500', 
        DEFAULT, 
        '2019-05-01'
    ),
    (
        '6', 
        '1', 
        'Маска Oakley Canopy', 
        'Легкий маневренный сноуборд, готовый дать жару в любом парке, растопив снег мощным ... ', 
        'lot-6.jpg', 
        '5400', 
        '500', 
        DEFAULT, 
        '2019-05-01'
);

INSERT INTO bets 
    (
        item_id,
        user_id,
        winner_id,
        bet_price,
        ts_betted -- DEFAULT
    )
VALUES
    (           -- Ставка 2го пользователя на 5й лот +500 = 8000, 5й лот принадлежит 1 юзеру.
        '5', 
        '2', 
        DEFAULT, -- заполнение NUll, вариант '' выдает ошибку 
        '8000', 
        DEFAULT
    ),
    (           -- Ставка 1го пользователя на 2й лот +10000 = 169999, 2й лот принадлежит 2 юзеру.
        '2', 
        '1', 
        DEFAULT, -- заполнение NUll, вариант '', выдает ошибку
        '169999', 
        DEFAULT
);

/* 2часть. Чтение из таблиц, запросы*/

-- 1 - получить все категории

SELECT * FROM categories; 

-- 2 - получить самые новые, открытые лоты. Каждый лот должен включать название, стартовую цену, ссылку на изображение, цену, название категории

USE yeticave;

SELECT name, price, img_url, bet_price, title FROM items 
JOIN bets ON items.id = bets.item_id  
JOIN categories ON items.category = categories.id 
WHERE bets.winner_id IS NULL 
ORDER BY ts_betted DESC  
LIMIT 2;

-- 3 - показать лот по его id. Получите также название категории, к которой принадлежит лот; 

SELECT items.*, title FROM items  
JOIN categories ON items.category = categories.id 
WHERE items.id = 3; 

-- обновить название лота по его идентификатору 

UPDATE items SET name = 'NEW! Крепления Union Contact Pro 2015 года размер L/XL' WHERE id = 3;

-- 4 (1 вариант) получить список самых свежих ставок для лота по его идентификатору 

SELECT items.id, name, bet_price FROM items
JOIN bets ON items.id = bets.item_id
WHERE items.id = 1 AND ts_betted BETWEEN '2019-04-26' AND CURRENT_TIMESTAMP
ORDER BY ts_betted DESC;

-- 4 (2 вариант) получить список самых свежих ставок для лота по его идентификатору 

SELECT items.id, name, bet_price FROM items
JOIN bets ON items.id = bets.item_id
WHERE ts_betted > '2019-04-26' AND items.id = 1
ORDER BY ts_betted DESC;
