-- settings
SET @iCategId = (SELECT `ID` FROM `sys_options_cats` WHERE `name` = 'HC Cases' LIMIT 1);
DELETE FROM `sys_options` WHERE `kateg` = @iCategId;
DELETE FROM `sys_options_cats` WHERE `ID` = @iCategId;
DELETE FROM `sys_options` WHERE `Name` = 'hc_cases_permalinks';

-- permalinks
DELETE FROM `sys_permalinks` WHERE `standard` = 'modules/?r=cases/';

-- admin menu
DELETE FROM `sys_menu_admin` WHERE `name` = 'hc_cases';

DROP TABLE `me_blgg_posts`;