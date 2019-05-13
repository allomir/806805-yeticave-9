/* 1часть. Наполнение таблиц информацией  */

USE yeticave;

INSERT INTO categories              
    (symbol, name)
VALUES
    ('boards','Доски и лыжи'),
    ('attachment','Крепления'),
    ('boots','Ботинки'),
    ('clothing','Одежда'),
    ('tools','Инструменты'),
    ('other','Разное')
;

INSERT INTO users
    (email, password, name, contacts, avatar_url, ts_created)
VALUES 
    ('elon@gmail.com','$2y$10$JM0EyD.1YIp0eykVXI.vZ.vZxMO1MttUUY56a7rqJwufdMP0WVMuS','Elon Musk','USA, California DC, Gigafactory-1','fotox00777.png',DEFAULT),
    ('bill@hotmail.com','$2y$10$JM0EyD.1YIp0eykVXI.vZ.vZxMO1MttUUY56a7rqJwufdMP0WVMuS','Bill Gates','Paolo Alto, USA','foto01010.jpg',DEFAULT),
    ('rasmus@yahoo.com','$2y$10$JM0EyD.1YIp0eykVXI.vZ.vZxMO1MttUUY56a7rqJwufdMP0WVMuS','Rasmus Lerdorf','Canada','foto999.png',DEFAULT)
;

INSERT INTO items
    (
        user_id,
        category_id,  
        name, 
        description, 
        img_url, 
        price, 
        step, 
        ts_add, -- DEFAULT
        ts_end
    )
VALUES 
    (
        '1',
        1,
        '2014 Rossignol District Snowboard',
        'Легкий маневренный сноуборд, готовый дать жару в любом парке, растопив снег мощным щелчком и четкими дугами. Стекловолокно Bi-Ax, уложенное в двух направлениях, наделяет этот снаряд отличной гибкостью и отзывчивостью, а симметричная геометрия в сочетании с классическим прогибом кэмбер позволит уверенно держать высокие скорости. А если к концу катального дня сил совсем не останется, просто посмотрите на Вашу доску и улыбнитесь, крутая графика от Шона Кливера еще никого не оставляла равнодушным.',
        'img/lot-1.jpg',
        10999,
        1000,
        DEFAULT,
        '2019-05-14 21:00:00'
    ),
    (
        '2',
        1,
        'DC Ply Mens 2016/2017 Snowboard',
        'Легкий маневренный сноуборд, готовый дать жару в любом парке, растопив снег мощным щелчком и четкими дугами. Стекловолокно Bi-Ax, уложенное в двух направлениях, наделяет этот снаряд отличной гибкостью и отзывчивостью, а симметричная геометрия в сочетании с классическим прогибом кэмбер позволит уверенно держать высокие скорости. А если к концу катального дня сил совсем не останется, просто посмотрите на Вашу доску и улыбнитесь, крутая графика от Шона Кливера еще никого не оставляла равнодушным.',
        'img/lot-2.jpg',
        159999,
        10000,
        DEFAULT,
        '2019-05-15 17:30:00'
    ),
    (
        '3',
        2,
        'NEW! Крепления Union Contact Pro 2015 года размер L/XL',
        'Легкий маневренный сноуборд, готовый дать жару в любом парке, растопив снег мощным щелчком и четкими дугами. Стекловолокно Bi-Ax, уложенное в двух направлениях, наделяет этот снаряд отличной гибкостью и отзывчивостью, а симметричная геометрия в сочетании с классическим прогибом кэмбер позволит уверенно держать высокие скорости. А если к концу катального дня сил совсем не останется, просто посмотрите на Вашу доску и улыбнитесь, крутая графика от Шона Кливера еще никого не оставляла равнодушным.',
        'img/lot-3.jpg',
        8000,
        500,
        DEFAULT,
        '2019-05-16 18:00:00'
    ),
    (
        '1',
        3,
        'Ботинки для сноуборда DC Mutiny Charocal',
        'Легкий маневренный сноуборд, готовый дать жару в любом парке, растопив снег мощным щелчком и четкими дугами. Стекловолокно Bi-Ax, уложенное в двух направлениях, наделяет этот снаряд отличной гибкостью и отзывчивостью, а симметричная геометрия в сочетании с классическим прогибом кэмбер позволит уверенно держать высокие скорости. А если к концу катального дня сил совсем не останется, просто посмотрите на Вашу доску и улыбнитесь, крутая графика от Шона Кливера еще никого не оставляла равнодушным.',
        'img/lot-4.jpg',
        10999,
        2000,
        DEFAULT,
        '2019-05-17 21:00:00'
    ),
    (
        '2',
        4,
        'Куртка для сноуборда DC Mutiny Charocal',
        'Легкий маневренный сноуборд, готовый дать жару в любом парке, растопив снег мощным щелчком и четкими дугами. Стекловолокно Bi-Ax, уложенное в двух направлениях, наделяет этот снаряд отличной гибкостью и отзывчивостью, а симметричная геометрия в сочетании с классическим прогибом кэмбер позволит уверенно держать высокие скорости. А если к концу катального дня сил совсем не останется, просто посмотрите на Вашу доску и улыбнитесь, крутая графика от Шона Кливера еще никого не оставляла равнодушным.',
        'img/lot-5.jpg',
        7500,
        500,
        DEFAULT,
        '2019-05-18 12:00:00'
    ),
    (
        '3',
        6,
        'Маска Oakley Canopy',
        'Легкий маневренный сноуборд, готовый дать жару в любом парке, растопив снег мощным щелчком и четкими дугами. Стекловолокно Bi-Ax, уложенное в двух направлениях, наделяет этот снаряд отличной гибкостью и отзывчивостью, а симметричная геометрия в сочетании с классическим прогибом кэмбер позволит уверенно держать высокие скорости. А если к концу катального дня сил совсем не останется, просто посмотрите на Вашу доску и улыбнитесь, крутая графика от Шона Кливера еще никого не оставляла равнодушным.',
        'img/lot-6.jpg',
        5400,
        500,
        DEFAULT,
        '2019-05-19 21:00:00'
    ),

    (
        '1',
        1,
        'Лыжи FISCHER RC4 Race Jr. + FJ4 AC SLR',
        'Легкий маневренный сноуборд, готовый дать жару в любом парке, растопив снег мощным щелчком и четкими дугами. Стекловолокно Bi-Ax, уложенное в двух направлениях, наделяет этот снаряд отличной гибкостью и отзывчивостью, а симметричная геометрия в сочетании с классическим прогибом кэмбер позволит уверенно держать высокие скорости. А если к концу катального дня сил совсем не останется, просто посмотрите на Вашу доску и улыбнитесь, крутая графика от Шона Кливера еще никого не оставляла равнодушным.',
        'img/lot-7.jpg',
        7700,
        1000,
        DEFAULT,
        '2019-05-20 21:00:00'
    ),
    (
        '2',
        6,
        'Шлем горнолыжный SALOMON Mirage',
        'Легкий маневренный сноуборд, готовый дать жару в любом парке, растопив снег мощным щелчком и четкими дугами. Стекловолокно Bi-Ax, уложенное в двух направлениях, наделяет этот снаряд отличной гибкостью и отзывчивостью, а симметричная геометрия в сочетании с классическим прогибом кэмбер позволит уверенно держать высокие скорости. А если к концу катального дня сил совсем не останется, просто посмотрите на Вашу доску и улыбнитесь, крутая графика от Шона Кливера еще никого не оставляла равнодушным.',
        'img/lot-8.jpg',
        15900,
        2000,
        DEFAULT,
        '2019-05-21 21:00:00'
    ),
    (
        '3',
        3,
        'Ботинки горнолыжные ROXA Element 90',
        'Легкий маневренный сноуборд, готовый дать жару в любом парке, растопив снег мощным щелчком и четкими дугами. Стекловолокно Bi-Ax, уложенное в двух направлениях, наделяет этот снаряд отличной гибкостью и отзывчивостью, а симметричная геометрия в сочетании с классическим прогибом кэмбер позволит уверенно держать высокие скорости. А если к концу катального дня сил совсем не останется, просто посмотрите на Вашу доску и улыбнитесь, крутая графика от Шона Кливера еще никого не оставляла равнодушным.',
        'img/lot-9.jpg',
        9999,
        1000,
        DEFAULT,
        '2019-05-22 17:30:00'
    ),
    (
        '1',
        6,
        'Рюкзак горнолыжный SALOMON Original Gear Backpack Barbados',
        'Легкий маневренный сноуборд, готовый дать жару в любом парке, растопив снег мощным щелчком и четкими дугами. Стекловолокно Bi-Ax, уложенное в двух направлениях, наделяет этот снаряд отличной гибкостью и отзывчивостью, а симметричная геометрия в сочетании с классическим прогибом кэмбер позволит уверенно держать высокие скорости. А если к концу катального дня сил совсем не останется, просто посмотрите на Вашу доску и улыбнитесь, крутая графика от Шона Кливера еще никого не оставляла равнодушным.',
        'img/lot-10.jpg',
        3000,
        500,
        DEFAULT,
        '2019-05-23 17:30:00'
    ),
    (
        '2',
        5,
        'Стяжка для горных лыж SWIX R0391',
        'Легкий маневренный сноуборд, готовый дать жару в любом парке, растопив снег мощным щелчком и четкими дугами. Стекловолокно Bi-Ax, уложенное в двух направлениях, наделяет этот снаряд отличной гибкостью и отзывчивостью, а симметричная геометрия в сочетании с классическим прогибом кэмбер позволит уверенно держать высокие скорости. А если к концу катального дня сил совсем не останется, просто посмотрите на Вашу доску и улыбнитесь, крутая графика от Шона Кливера еще никого не оставляла равнодушным.',
        'img/lot-11.jpg',
        500,
        69,
        DEFAULT,
        '2019-05-24 17:30:00'
    ),
    (
        '3',
        5,
        'Набор мазей держания с пробкой SWIX P0020G',
        'Легкий маневренный сноуборд, готовый дать жару в любом парке, растопив снег мощным щелчком и четкими дугами. Стекловолокно Bi-Ax, уложенное в двух направлениях, наделяет этот снаряд отличной гибкостью и отзывчивостью, а симметричная геометрия в сочетании с классическим прогибом кэмбер позволит уверенно держать высокие скорости. А если к концу катального дня сил совсем не останется, просто посмотрите на Вашу доску и улыбнитесь, крутая графика от Шона Кливера еще никого не оставляла равнодушным.',
        'img/lot-12.jpg',
        1500,
        200,
        DEFAULT,
        '2019-05-25 17:30:00'
    ),
        (
        '1',
        5,
        'Утюг для беговых лыж SWIX T73 Performance Digital 220V',
        'Легкий маневренный сноуборд, готовый дать жару в любом парке, растопив снег мощным щелчком и четкими дугами. Стекловолокно Bi-Ax, уложенное в двух направлениях, наделяет этот снаряд отличной гибкостью и отзывчивостью, а симметричная геометрия в сочетании с классическим прогибом кэмбер позволит уверенно держать высокие скорости. А если к концу катального дня сил совсем не останется, просто посмотрите на Вашу доску и улыбнитесь, крутая графика от Шона Кливера еще никого не оставляла равнодушным.',
        'img/lot-13.jpg',
        5000,
        590,
        DEFAULT,
        '2019-05-26 17:30:00'
    ),
    (
        '2',
        2,
        'Крепления горнолыжные HEAD Freeflex Evo 16X RD',
        'Легкий маневренный сноуборд, готовый дать жару в любом парке, растопив снег мощным щелчком и четкими дугами. Стекловолокно Bi-Ax, уложенное в двух направлениях, наделяет этот снаряд отличной гибкостью и отзывчивостью, а симметричная геометрия в сочетании с классическим прогибом кэмбер позволит уверенно держать высокие скорости. А если к концу катального дня сил совсем не останется, просто посмотрите на Вашу доску и улыбнитесь, крутая графика от Шона Кливера еще никого не оставляла равнодушным.',
        'img/lot-14.jpg',
        16500,
        2500,
        DEFAULT,
        '2019-05-27 17:30:00'
    ),
    (
        '3',
        4,
        'Штаны горнолыжные HELLY HANSEN W Legendary Pant Melt Down',
        'Легкий маневренный сноуборд, готовый дать жару в любом парке, растопив снег мощным щелчком и четкими дугами. Стекловолокно Bi-Ax, уложенное в двух направлениях, наделяет этот снаряд отличной гибкостью и отзывчивостью, а симметричная геометрия в сочетании с классическим прогибом кэмбер позволит уверенно держать высокие скорости. А если к концу катального дня сил совсем не останется, просто посмотрите на Вашу доску и улыбнитесь, крутая графика от Шона Кливера еще никого не оставляла равнодушным.',
        'img/lot-15.jpg',
        7700,
        770,
        DEFAULT,
        '2019-05-28 13:30:00'
    );


INSERT INTO bets 
    (
        item_id,
        user_id,
        winner_id,  -- DEFAULT NULL
        bet_price,
        ts_betted -- DEFAULT
    )
VALUES 
    (5,2,DEFAULT,8000,DEFAULT),
    (2,1,DEFAULT,169999,DEFAULT),
    (1,2,DEFAULT,11999,DEFAULT),
    (4,1,DEFAULT,12999,DEFAULT),
    (1,3,DEFAULT,12999,DEFAULT)
;
