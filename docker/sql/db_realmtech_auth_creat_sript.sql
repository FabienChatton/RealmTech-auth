-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- -----------------------------------------------------
-- Schema chattonf01_db_realmtech_auth
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema chattonf01_db_realmtech_auth
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `chattonf01_db_realmtech_auth` ;
USE `chattonf01_db_realmtech_auth` ;

-- -----------------------------------------------------
-- Table `chattonf01_db_realmtech_auth`.`T_User`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `chattonf01_db_realmtech_auth`.`T_User` (
  `PK_User` INT NOT NULL AUTO_INCREMENT,
  `uuid` VARCHAR(36) NOT NULL,
  `username` VARCHAR(15) NOT NULL,
  `password` VARCHAR(100) NOT NULL,
  `email` VARCHAR(45) NULL,
  `first_join` TIMESTAMP NOT NULL,
  PRIMARY KEY (`PK_User`),
  UNIQUE INDEX `uuid_UNIQUE` (`uuid` ASC) VISIBLE,
  UNIQUE INDEX `username_UNIQUE` (`username` ASC) VISIBLE,
  UNIQUE INDEX `email_UNIQUE` (`email` ASC) VISIBLE)
ENGINE = InnoDB;

USE `chattonf01_db_realmtech_auth`;

DELIMITER $$
USE `chattonf01_db_realmtech_auth`$$
CREATE DEFINER = CURRENT_USER TRIGGER `chattonf01_db_realmtech_auth`.`T_User_BEFORE_INSERT` BEFORE INSERT ON `T_User` FOR EACH ROW
BEGIN
SET NEW.uuid = uuid();
SET NEW.first_join = now();
END$$


DELIMITER ;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
