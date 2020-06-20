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
