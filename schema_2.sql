CREATE SCHEMA `yeticave` DEFAULT CHARACTER SET utf8 ;

CREATE TABLE `yeticave`.`categories` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `category_id` INT UNSIGNED NOT NULL,
  `title` CHAR(64) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `category_id_UNIQUE` (`category_id` ASC),
  UNIQUE INDEX `title_UNIQUE` (`title` ASC));

CREATE TABLE `yeticave`.`items` (
  `item_id` INT NOT NULL AUTO_INCREMENT,
  `category_id` INT UNSIGNED NOT NULL,
  `user_id` INT UNSIGNED NOT NULL,
  `item_name` CHAR(255) NOT NULL,
  `description` TEXT(1024) NULL,
  `img_url` CHAR(255) NULL,
  `price` DECIMAL NULL,
  `bet_step` DECIMAL NULL,
  `ts_add` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `ts_end` TIMESTAMP NULL,
  `dt_add` DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
  `dt_end` DATETIME NULL,
  PRIMARY KEY (`item_id`));

CREATE TABLE `yeticave`.`users` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NOT NULL,
  `email` CHAR(64) NOT NULL,
  `password` CHAR(64) NOT NULL,
  `user_name` CHAR(64) NULL,
  `user_cont` CHAR(255) NULL,
  `avatar_url` CHAR(255) NULL,
  `ts_user` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `dt_user` DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `user_id_UNIQUE` (`user_id` ASC),
  UNIQUE INDEX `email_UNIQUE` (`email` ASC));

CREATE TABLE `yeticave`.`bets` (
  `bet_id` INT NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NOT NULL,
  `item_id` INT UNSIGNED NOT NULL,
  `bet_price` DECIMAL NOT NULL,
  `ts_bet` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `dt_bet` DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`bet_id`));