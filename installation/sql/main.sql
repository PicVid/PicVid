#
# TABELLEN DER DATENBANK LOESCHEN
#
DROP TABLE IF EXISTS `#__image`, `#__menu`, `#__news`, `#__section`, `#__user`, `#__user_group`, `#__user_session`, `#__video`;

#
# TABELLEN-STRUKTUR DER TABELLE `#__user`
#
CREATE TABLE `#__user` (
    `id` INT(10) NOT NULL AUTO_INCREMENT,
    `activation` VARCHAR(32) NOT NULL DEFAULT '1',
    `create_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
    `description` TEXT NOT NULL DEFAULT '',
    `email` VARCHAR(255) CHARSET utf8 NOT NULL DEFAULT '',
    `group` INT(10) NOT NULL DEFAULT 0,
    `lastvisit_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
    `name` VARCHAR(255) CHARSET utf8 NOT NULL DEFAULT '',
    `password` VARCHAR(32) CHARSET utf8 NOT NULL DEFAULT '',
    `publish_end_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
    `publish_start_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
    `state` TINYINT(1) NOT NULL DEFAULT 1,
    `username` VARCHAR(100) CHARSET utf8 NOT NULL DEFAULT '',
    PRIMARY KEY(`id`)
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET utf8 COLLATE = utf8_unicode_ci;

#
# TABELLEN-STRUKTUR DER TABELLE ´#__user_group´
#
CREATE TABLE `#__user_group` (
    `id` INT(10) NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(100) CHARSET utf8 NOT NULL DEFAULT '',
    PRIMARY KEY(`id`)
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET utf8 COLLATE = utf8_unicode_ci;

#
# VORDEFINIERTE DATEN IN DIE TABELLE `#__user_group` SCHREIBEN
#
INSERT INTO `#__user_group` VALUES (1, 'Registriert');
INSERT INTO `#__user_group` VALUES (2, 'Moderator');
INSERT INTO `#__user_group` VALUES (3, 'Administrator');

#
# TABELLEN-STRUKTUR DER TABELLE `#__user_session`
#
CREATE TABLE `#__user_session` (
    `id` VARCHAR(32) CHARSET utf8 NOT NULL DEFAULT '',
    `time` VARCHAR(20) CHARSET utf8 NOT NULL DEFAULT '0000-00-00 00:00:00',
    `user_id` INT(10) NOT NULL DEFAULT 0,
    `username`VARCHAR(100) CHARSET utf8 NOT NULL DEFAULT '',
    PRIMARY KEY(`id`)
) ENGINE = MyISAM CHARACTER SET utf8 COLLATE = utf8_unicode_ci;

#
# TABELLEN-STRUKTUR DER TABELLE `#__section`
#
CREATE TABLE `#__section` (
	`id` INT(10) NOT NULL AUTO_INCREMENT,
 	`admin_group` TINYINT (1) NOT NULL DEFAULT 3,
 	`category_id` TINYINT(1) NOT NULL DEFAULT 0,
	`expiry_state` TINYINT(1) NOT NULL DEFAULT 0,
	`expiry_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
	`menu_title` VARCHAR(255) CHARSET utf8 NOT NULL DEFAULT '',
	`name` VARCHAR(255) CHARSET utf8 NOT NULL DEFAULT '',
	`publish_end_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
	`publish_start_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
	`state` TINYINT(1) NOT NULL DEFAULT 1,
	`user_group` TINYINT(1) NOT NULL DEFAULT 0,
	`version` INT(3) NOT NULL DEFAULT 0,
    PRIMARY KEY(`id`)
) ENGINE = MyISAM CHARACTER SET utf8 COLLATE = utf8_unicode_ci;

#
# VORDEFINIERTE DATEN IN DIE TABELLE `#__section` SCHREIBEN
#
INSERT INTO `#__section` (`id`, `name`, `user_group`) VALUES (1, 'dashboard', 0);
INSERT INTO `#__section` (`id`, `category_id`, `menu_title`, `name`, `user_group`) VALUES (2, 2, 'Benutzerverwaltung', 'user_management', 2);
INSERT INTO `#__section` (`id`, `category_id`, `menu_title`, `name`, `user_group`) VALUES (3, 2, 'Bereiche', 'section_management', 2);
INSERT INTO `#__section` (`id`, `category_id`, `menu_title`, `name`, `user_group`) VALUES (6, 2, 'Menüverwaltung', 'menu', 2);
INSERT INTO `#__section` (`id`, `category_id`, `menu_title`, `name`, `user_group`) VALUES (4, 3, 'News', 'news', 2);
INSERT INTO `#__section` (`id`, `category_id`, `menu_title`, `name`, `user_group`) VALUES (5, 3, 'Profil', 'profile', 2);
INSERT INTO `#__section` (`id`, `category_id`, `menu_title`, `name`, `user_group`) VALUES (7, 0, 'Slideshow', 'slideshow', 0);
INSERT INTO `#__section` (`id`, `category_id`, `menu_title`, `name`, `user_group`) VALUES (8, 0, 'Videoshow', 'videoshow', 0);
INSERT INTO `#__section` (`id`, `category_id`, `menu_title`, `name`, `user_group`) VALUES (9, 0, 'Upload', 'upload', 0);
INSERT INTO `#__section` (`id`, `category_id`, `menu_title`, `name`, `user_group`) VALUES (10, 0, 'Registrierung', 'register', 0);

#
# TABELLEN-STRUKTUR DER TABELLE `#__image` ERZEUGEN.
#
CREATE TABLE `#__image` (
	`id` INT(10) NOT NULL AUTO_INCREMENT,
	`create_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
	`create_user` INT(10) NOT NULL DEFAULT 0,
	`description` TEXT CHARSET utf8 NOT NULL DEFAULT '',
	`name` VARCHAR(255) CHARSET utf8 NOT NULL DEFAULT '',
	`title` VARCHAR(255) CHARSET utf8 NOT NULL DEFAULT '',
	`type` VARCHAR(50) CHARSET utf8 NOT NULL DEFAULT '',
	PRIMARY KEY(`id`)
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET utf8 COLLATE = utf8_unicode_ci;

#
# TABELLEN-STRUKTUR DER TABELLE `#__video` ERZEUGEN.
#
CREATE TABLE `#__video` (
	`id` INT(10) NOT NULL AUTO_INCREMENT,
	`create_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
	`create_user` INT(10) NOT NULL DEFAULT 0,
	`description` TEXT CHARSET utf8 NOT NULL DEFAULT '',
    `name` VARCHAR(255) CHARSET utf8 NOT NULL DEFAULT '',
    `title` VARCHAR(255) CHARSET utf8 NOT NULL DEFAULT '',
	`type` VARCHAR(50) CHARSET utf8 NOT NULL DEFAULT '',
	PRIMARY KEY(`id`)
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET utf8 COLLATE = utf8_unicode_ci;

#
# TABELLEN-STRUKTUR DER TABELLE `#__news` ERZEUGEN.
#
CREATE TABLE `#__news` (
  `NewsID` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `NewsTitle` VARCHAR(100) CHARSET utf8 COLLATE utf8_bin NOT NULL DEFAULT '',
  `NewsDate` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `NewsContent` TEXT CHARSET utf8 COLLATE utf8_bin NOT NULL DEFAULT '',
  `NewsUserID` INT(10) NOT NULL DEFAULT 1,
  PRIMARY KEY (`NewsID`)
) ENGINE = MyISAM AUTO_INCREMENT = 7 CHARACTER SET latin1;

#
# TABELLEN-STRUKTUR DER TABELLE `#__menu`
#
CREATE TABLE `#__menu` (
	`id` INT(10) NOT NULL AUTO_INCREMENT,
	`SectionID` INT(10) NOT NULL DEFAULT 0,
	`OrderNum` INT(10) NOT NULL DEFAULT 0,
	`ShowName` VARCHAR(255) CHARSET utf8 NOT NULL DEFAULT '',
	`URL` VARCHAR(255) CHARSET utf8 NOT NULL DEFAULT '',
    `Type` TINYINT(1) NOT NULL DEFAULT 1,
    PRIMARY KEY(`id`)
) ENGINE = MyISAM CHARACTER SET utf8 COLLATE = utf8_unicode_ci;

#
# DATEN IN DIE TABELLE `#__menu`
#
INSERT INTO `#__menu` (`SectionID`, `ShowName`, `OrderNum`, `Type`) VALUES (7, 'Slideshow', 1, 0);
INSERT INTO `#__menu` (`SectionID`, `ShowName`, `OrderNum`, `Type`) VALUES (8, 'Videoshow', 2, 0);
INSERT INTO `#__menu` (`SectionID`, `ShowName`, `OrderNum`, `Type`) VALUES (9, 'Upload', 3, 0);
INSERT INTO `#__menu` (`SectionID`, `ShowName`, `OrderNum`, `Type`) VALUES (10, 'Registrierung', 4, 0);

#
# TABELLEN-STRUKTUR DER TABELLE `#__rih_profile`
#
CREATE TABLE IF NOT EXISTS `#__rich_profile` (
    `user_id` int(10) NOT NULL DEFAULT '1',
    `birthday` datetime NOT NULL DEFAULT '1930-01-01 00:00:00',
    `live_city` varchar(255) CHARSET utf8 NOT NULL,
    `live_country` varchar(255) CHARSET utf8 NOT NULL,
    `icq` int(10) DEFAULT NULL,
    `msn` varchar(255) CHARSET utf8 NOT NULL,
    `skype` varchar(255) CHARSET utf8 NOT NULL,
    `gtalk` varchar(255) CHARSET utf8 NOT NULL,
    `twitter` varchar(255) CHARSET utf8 NOT NULL,
    `facebook` varchar(255) CHARSET utf8 NOT NULL,
    `opt_birthday` int(1) NOT NULL DEFAULT '0',
    `opt_live` int(1) NOT NULL DEFAULT '0',
    `opt_icq` int(1) NOT NULL DEFAULT '0',
    `opt_msn` int(1) NOT NULL DEFAULT '0',
    `opt_skype` int(1) NOT NULL DEFAULT '0',
    `opt_gtalk` int(1) NOT NULL DEFAULT '0',
    `opt_twitter` int(1) NOT NULL DEFAULT '0',
    `opt_facebook` int(1) NOT NULL DEFAULT '0',
    `opt_mail` int(1) NOT NULL,
    PRIMARY KEY (`user_id`)
) ENGINE = MyISAM CHARACTER SET utf8 COLLATE = utf8_unicode_ci;