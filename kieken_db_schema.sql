-- -----------------------------------------------------
-- Table `kieken_pictures`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `kieken_pictures` ;
CREATE  TABLE IF NOT EXISTS `kieken_pictures` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `title` VARCHAR(255) NULL ,
  `description` TEXT NULL ,
  `license` SMALLINT NULL ,
  `user_id` INT(11) NULL ,
  `slug` VARCHAR(255) NOT NULL ,
  `status` TINYINT(1) NOT NULL DEFAULT 0 ,
  `updated` DATETIME NOT NULL ,
  `created` DATETIME NOT NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `slug_UNIQUE` (`slug` ASC) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;

-- -----------------------------------------------------
-- Table `kieken_albums`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `kieken_albums` ;
CREATE  TABLE IF NOT EXISTS `kieken_albums` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `parent_id` INT(11) NULL ,
  `title` VARCHAR(255) NULL ,
  `excerpt` TINYTEXT NULL ,
  `description` TEXT NULL ,
  `thumbnail_picture_id` INT(11) NULL ,
  `user_id` INT(11) NULL ,
  `slug` VARCHAR(255) NOT NULL ,
  `lft` INT(11) NULL ,
  `rght` INT(11) NULL ,
  `status` TINYINT(1) NOT NULL DEFAULT 0 ,
  `updated` DATETIME NOT NULL ,
  `created` DATETIME NOT NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `slug_UNIQUE` (`slug` ASC) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;

-- -----------------------------------------------------
-- Table `kieken_albums_pictures`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `kieken_albums_pictures` ;
CREATE  TABLE IF NOT EXISTS `kieken_albums_pictures` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `album_id` INT(11) NOT NULL ,
  `picture_id` INT(11) NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;

-- -----------------------------------------------------
-- Table `kieken_files`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `kieken_files` ;
CREATE  TABLE IF NOT EXISTS `kieken_files` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `picture_id` INT(11) NOT NULL ,
  `width` INT(6) NOT NULL ,
  `height` INT(6) NOT NULL ,
  `filename` VARCHAR(100) NULL ,
  `filetype` VARCHAR(20) NOT NULL ,
  `updated` DATETIME NOT NULL ,
  `created` DATETIME NOT NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `filename_UNIQUE` (`filename` ASC) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;