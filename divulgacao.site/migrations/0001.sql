DROP DATABASE showcase;
CREATE DATABASE showcase;
USE showcase;



-- @author Mateus Felipe - mateus_f97@hotmail.com
-- This table was created to save the access and the main data for a user


CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(256) NOT NULL UNIQUE,
  `access_token` varchar(256) NOT NULL,
  `password` varchar(500) NOT NULL,
  `cpf` varchar(11) NOT NULL UNIQUE,
  `first_access` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_modification` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE  = InnoDB;

CREATE INDEX user_cpf ON user(cpf);
CREATE INDEX user_email ON user(email);
CREATE INDEX user_password ON user(password);


-- @author Mateus Felipe - mateus_f97@hotmail.com
-- This table was created to save products

CREATE TABLE IF NOT EXISTS `product` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `url` TEXT,
  `image_url` TEXT,
  `title` VARCHAR(128),
  `description` VARCHAR(1024),
  `price` VARCHAR(128),
  `parcel` VARCHAR(128),
  `category_books` TINYINT(1) DEFAULT 0,
  `category_courses` TINYINT(1) DEFAULT 0,
  `category_subscriptions` TINYINT(1) DEFAULT 0,
  `category_free` TINYINT(1) DEFAULT 0,
  `is_important`tinyint(1) DEFAULT 0,
  `clicks` INT(11) DEFAULT 0,
  `last_modification` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE  = InnoDB;

CREATE INDEX product_title ON product(title);
CREATE INDEX product_description ON product(description);
CREATE INDEX product_is_important ON product(is_important);
