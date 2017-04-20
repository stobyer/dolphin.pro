-- settings
SET @iMaxOrder = (SELECT `menu_order` + 1 FROM `sys_options_cats` ORDER BY `menu_order` DESC LIMIT 1);
INSERT INTO `sys_options_cats` (`name`, `menu_order`) VALUES ('HC Cases', @iMaxOrder);
SET @iCategId = (SELECT LAST_INSERT_ID());
INSERT INTO `sys_options` (`Name`, `VALUE`, `kateg`, `desc`, `Type`, `check`, `err_text`, `order_in_kateg`, `AvailableValues`) VALUES
  ('hc_cases_permalinks', 'on', 26, 'Enable friendly permalinks in HC Cases', 'checkbox', '', '', '0', ''),
  ('hc_cases_date_format', 'Y-m-d H:i', @iCategId, 'Format for server date/time', 'digit', '', '', '1', ''),
  ('hc_cases_enable_js_date', 'on', @iCategId, 'Show user time', 'chheckbox', '', '', '2', '');

-- permalinks
INSERT INTO `sys_permalinks` VALUES (NULL, 'modules/?r=cases/', 'm/cases/', 'hc_cases_permalinks');

-- admin menu
SET @iMax = (SELECT MAX(`order`) FROM `sys_menu_admin` WHERE `parent_id` = '2');
INSERT IGNORE INTO `sys_menu_admin` (`parent_id`, `name`, `title`, `url`, `description`, `icon`, `order`) VALUES
  (2, 'hc_cases', '_hc_cases', '{siteUrl}modules/?r=cases/administration/', 'HC Cases by HC', 'file', @iMax+1);


CREATE TABLE `hc_cases_posts` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
  `title` VARCHAR( 255 ) NOT NULL ,
  `text` TEXT NOT NULL ,
  `author_id` INT UNSIGNED NOT NULL ,
  `added` INT UNSIGNED NOT NULL ,
  INDEX ( `author_id` )
);