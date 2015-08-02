SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

CREATE SCHEMA IF NOT EXISTS `cms` DEFAULT CHARACTER SET utf8 ;
USE `cms` ;

-- -----------------------------------------------------
-- Table `cms`.`media`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `cms`.`media` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `slug` VARCHAR(16) NOT NULL ,
  `title` VARCHAR(32) NOT NULL ,
  `created_at` DATETIME NULL DEFAULT NULL ,
  `updated_at` DATETIME NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `slug` (`slug` ASC) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `cms`.`media_items`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `cms`.`media_items` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `media_id` INT(10) UNSIGNED NOT NULL ,
  `sort_id` TINYINT(3) UNSIGNED NOT NULL ,
  `url` TEXT NULL DEFAULT NULL ,
  `title` VARCHAR(140) NULL DEFAULT NULL ,
  `caption` TEXT NULL DEFAULT NULL ,
  `created_at` DATETIME NULL DEFAULT NULL ,
  `updated_at` DATETIME NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `media_id` (`media_id` ASC) ,
  CONSTRAINT `media_items_ibfk_1`
    FOREIGN KEY (`media_id` )
    REFERENCES `cms`.`media` (`id` )
    ON DELETE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `cms`.`menus`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `cms`.`menus` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `slug` VARCHAR(16) NOT NULL ,
  `title` VARCHAR(16) NOT NULL ,
  `created_at` DATETIME NULL DEFAULT NULL ,
  `updated_at` DATETIME NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `slug` (`slug` ASC) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `cms`.`pages`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `cms`.`pages` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `author_id` INT(10) UNSIGNED NOT NULL ,
  `status` ENUM('drafted','published','deleted','hidden') NOT NULL DEFAULT 'drafted' ,
  `slug` VARCHAR(140) NOT NULL ,
  `title` VARCHAR(140) NOT NULL ,
  `content` MEDIUMTEXT NOT NULL ,
  `created_at` DATETIME NULL DEFAULT NULL ,
  `updated_at` DATETIME NULL DEFAULT NULL ,
  `deleted_at` DATETIME NULL DEFAULT NULL ,
  `publish_at` DATETIME NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `author_id` (`author_id` ASC) ,
  INDEX `status` (`status` ASC, `publish_at` ASC) ,
  INDEX `slug` (`slug` ASC, `status` ASC) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `cms`.`menus_items`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `cms`.`menus_items` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `menu_id` INT(10) UNSIGNED NOT NULL ,
  `sort_id` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0' ,
  `page_id` INT(10) UNSIGNED NULL DEFAULT NULL ,
  `url` TEXT NULL DEFAULT NULL ,
  `title` VARCHAR(16) NULL DEFAULT NULL ,
  `created_at` DATETIME NULL DEFAULT NULL ,
  `updated_at` DATETIME NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `menu_id` (`menu_id` ASC) ,
  INDEX `menus_items_ibfk_2` (`page_id` ASC) ,
  CONSTRAINT `menus_items_ibfk_1`
    FOREIGN KEY (`menu_id` )
    REFERENCES `cms`.`menus` (`id` )
    ON DELETE CASCADE,
  CONSTRAINT `menus_items_ibfk_2`
    FOREIGN KEY (`page_id` )
    REFERENCES `cms`.`pages` (`id` )
    ON DELETE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `cms`.`roles`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `cms`.`roles` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(32) NOT NULL ,
  `description` VARCHAR(255) NOT NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `uniq_name` (`name` ASC) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `cms`.`users`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `cms`.`users` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `email` VARCHAR(254) NOT NULL ,
  `username` VARCHAR(32) NOT NULL DEFAULT '' ,
  `password` VARCHAR(64) NOT NULL ,
  `logins` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
  `last_login` INT(10) UNSIGNED NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `uniq_username` (`username` ASC) ,
  UNIQUE INDEX `uniq_email` (`email` ASC) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `cms`.`roles_users`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `cms`.`roles_users` (
  `user_id` INT(10) UNSIGNED NOT NULL ,
  `role_id` INT(10) UNSIGNED NOT NULL ,
  PRIMARY KEY (`user_id`, `role_id`) ,
  INDEX `fk_role_id` (`role_id` ASC) ,
  CONSTRAINT `roles_users_ibfk_1`
    FOREIGN KEY (`user_id` )
    REFERENCES `cms`.`users` (`id` )
    ON DELETE CASCADE,
  CONSTRAINT `roles_users_ibfk_2`
    FOREIGN KEY (`role_id` )
    REFERENCES `cms`.`roles` (`id` )
    ON DELETE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `cms`.`user_tokens`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `cms`.`user_tokens` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `user_id` INT(11) UNSIGNED NOT NULL ,
  `user_agent` VARCHAR(40) NOT NULL ,
  `token` VARCHAR(40) NOT NULL ,
  `created` INT(10) UNSIGNED NOT NULL ,
  `expires` INT(10) UNSIGNED NOT NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `uniq_token` (`token` ASC) ,
  INDEX `fk_user_id` (`user_id` ASC) ,
  INDEX `expires` (`expires` ASC) ,
  CONSTRAINT `user_tokens_ibfk_1`
    FOREIGN KEY (`user_id` )
    REFERENCES `cms`.`users` (`id` )
    ON DELETE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

USE `cms` ;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
