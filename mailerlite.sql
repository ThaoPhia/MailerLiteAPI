-- MySQL Workbench Synchronization
-- Generated: 2019-11-02 00:25
-- Model: New Model
-- Version: 1.0
-- Project: Name of the project
-- Author: Phia Thao

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

CREATE TABLE IF NOT EXISTS `mailerlite`.`subscriber` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_updated` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `state` ENUM('active', 'unsubscribed', 'junk', 'bounced', 'unconfirmed') NOT NULL DEFAULT 'unconfirmed',
  `email` VARCHAR(75) NOT NULL,
  `name` VARCHAR(75) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `email_UNIQUE` (`email` ASC))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE TABLE IF NOT EXISTS `mailerlite`.`subscriber_field` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `subscriber_id` INT(11) NOT NULL,
  `title` VARCHAR(45) NOT NULL,
  `type` ENUM('date', 'number', 'string', 'boolean') NOT NULL,
  `value` VARCHAR(100) NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_subscriber_field_subscriber_idx` (`subscriber_id` ASC),
  CONSTRAINT `fk_subscriber_field_subscriber`
    FOREIGN KEY (`subscriber_id`)
    REFERENCES `mailerlite`.`subscriber` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


DELIMITER $$

USE `mailerlite`$$
CREATE DEFINER = CURRENT_USER TRIGGER `subscriber_BEFORE_DELETE` BEFORE DELETE ON `subscriber` FOR EACH ROW
BEGIN
	DELETE FROM subscriber_field WHERE subscriber_id=OLD.id;
END$$


DELIMITER ;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
