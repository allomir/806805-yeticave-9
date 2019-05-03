CREATE DATABASE yeticave 
    DEFAULT CHARACTER SET utf8 
    DEFAULT COLLATE utf8_general_ci;

USE yeticave;
	
CREATE TABLE categories (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  symbol CHAR(64) NOT NULL UNIQUE,
  name CHAR(64) NOT NULL UNIQUE
);

CREATE TABLE items (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  category_id CHAR(64) NOT NULL,
  user_id INT UNSIGNED NOT NULL,
  name CHAR(255) NOT NULL,
  description TEXT(1024) NOT NULL,
  img_url CHAR(255) NOT NULL,
  price INT NOT NULL,
  step INT UNSIGNED NOT NULL,
  ts_add TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  ts_end TIMESTAMP NOT NULL
);

CREATE TABLE users (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  email CHAR(64) NOT NULL UNIQUE,
  password CHAR(64) NOT NULL,
  name CHAR(64) NOT NULL,
  contacts CHAR(255) NOT NULL,
  avatar_url CHAR(255) NOT NULL,
  ts_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE bets (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  item_id INT UNSIGNED NOT NULL,
  user_id INT UNSIGNED NOT NULL,
  winner_id INT UNSIGNED,
  bet_price INT UNSIGNED NOT NULL,
  ts_betted TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE UNIQUE INDEX ctg ON categories(name);
CREATE UNIQUE INDEX eml ON users(email);
CREATE INDEX usr ON users(name);
CREATE INDEX itm ON items(name);
CREATE INDEX f_prc ON items(price);
CREATE INDEX wnr ON bets(winner_id);
CREATE INDEX b_ts ON bets(ts_betted);
CREATE INDEX b_prc ON bets(bet_price);

