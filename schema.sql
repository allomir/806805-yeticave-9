CREATE DATABASE yeticave 
    DEFAULT CHARACTER SET utf8 
    DEFAULT COLLATE utf8_general_ci;

USE yeticave;
	
CREATE TABLE categories (
  id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  category_id INT UNSIGNED NOT NULL UNIQUE,
  title CHAR(64) NOT NULL UNIQUE
);

CREATE TABLE items (
  item_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  category_id INT UNSIGNED NOT NULL,
  user_id INT UNSIGNED NOT NULL,
  item_name CHAR(255) NOT NULL,
  description TEXT(1024),
  img_url CHAR(255),
  price DECIMAL,
  bet_step DECIMAL,
  ts_add TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  ts_end TIMESTAMP,
  dt_add DATETIME DEFAULT CURRENT_TIMESTAMP,
  dt_end DATETIME
);

CREATE TABLE users (
  id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NOT NULL UNIQUE,
  email CHAR(64) NOT NULL UNIQUE,
  password CHAR(64) NOT NULL,
  user_name CHAR(64),
  user_cont CHAR(255),
  avatar_url CHAR(255),
  ts_user TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  dt_user DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE bets (
  bet_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NOT NULL,
  item_id INT UNSIGNED NOT NULL,
  bet_price DECIMAL NOT NULL,
  ts_bet TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  dt_bet DATETIME DEFAULT CURRENT_TIMESTAMP
);