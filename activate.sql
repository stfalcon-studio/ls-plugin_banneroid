CREATE TABLE IF NOT EXISTS `prefix_banner` (
  `banner_id` INT(5) unsigned NOT NULL auto_increment,
  `banner_name` VARCHAR(50) DEFAULT NULL,
  `banner_url` VARCHAR(255) DEFAULT NULL,
  `banner_image` VARCHAR(255) DEFAULT NULL,
  `banner_html` LONGTEXT NOT NULL,
  `banner_lang` VARCHAR(16) DEFAULT NULL,
  `banner_type` INT(1) NOT NULL DEFAULT '1',
  `banner_start_date` DATE DEFAULT NULL,
  `banner_end_date` DATE DEFAULT NULL,
  `banner_is_active` INT(1) unsigned NOT NULL DEFAULT '1',
  `bannes_is_show` INT(1) unsigned NOT NULL DEFAULT '1',
  `banner_add_date` DATETIME DEFAULT NULL,
  `banner_edit_date` DATETIME DEFAULT NULL,
    PRIMARY KEY  (`banner_id`),
    KEY `banner_place_id` (`banner_is_active`),
    KEY `banner_name` (`banner_name`),
    KEY `banner_active` (`banner_is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=UTF8;


CREATE TABLE IF NOT EXISTS `prefix_banner_pages` (
    `place_id` INT(5) unsigned NOT NULL auto_increment,
    `place_name` VARCHAR(50) character set UTF8 DEFAULT NULL,
    `place_url` VARCHAR(255) character set UTF8 DEFAULT NULL,
    PRIMARY KEY  (`place_id`),
    KEY `place_name` (`place_name`),
    KEY `place_url` (`place_url`)
) ENGINE=InnoDB DEFAULT CHARSET=UTF8;


CREATE TABLE IF NOT EXISTS `prefix_banner_place_holders` (
    `banner_id` INT(5) unsigned NOT NULL DEFAULT '0',
    `page_id` INT(5) unsigned NOT NULL DEFAULT '0',
    `place_type` INT(1) NOT NULL DEFAULT '0',
    KEY `banner_id` (`banner_id`,`page_id`,`place_type`),
    KEY `banner_id_2` (`banner_id`),
    KEY `page_id` (`page_id`),
	CONSTRAINT `prefix_banner_place_holders_ref_banner` FOREIGN KEY (`banner_id`) REFERENCES `prefix_banner` (`banner_id`) ON DELETE CASCADE ON UPDATE CASCADE,
	CONSTRAINT `prefix_banner_place_holders_ref_page` FOREIGN KEY (`page_id`) REFERENCES `prefix_banner_pages` (`place_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=UTF8;


CREATE TABLE IF NOT EXISTS `prefix_banner_stats` (
    `stats_id` INT(11) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY ,
    `banner_id` INT(5) unsigned NOT NULL ,
    `view_count` INT(5) unsigned NOT NULL DEFAULT '0',
    `click_count` INT(5) unsigned NOT NULL DEFAULT '0',
    `stat_date` DATE NOT NULL,
	INDEX (`banner_id`),
	UNIQUE `stat_date` ( `banner_id` , `stat_date`),
	FOREIGN KEY ( `banner_id`) REFERENCES `prefix_banner` (`banner_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=UTF8;

INSERT IGNORE INTO `prefix_banner_pages` (`place_id`, `place_name`, `place_url`) VALUES
(1, 'banneroid_place_global', '%'),
(2, 'banneroid_place_blogs', '%/blog/%');