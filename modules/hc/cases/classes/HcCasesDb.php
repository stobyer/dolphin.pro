<?php
/***************************************************************************
*                            Dolphin Smart Community Builder
*                              -------------------
*     begin                : Mon Mar 23 2006
*     copyright            : (C) 2007 BoonEx Group
*     website              : http://www.boonex.com
* This file is part of Dolphin - Smart Community Builder
*
* Dolphin is free software; you can redistribute it and/or modify it under
* the terms of the GNU General Public License as published by the
* Free Software Foundation; either version 2 of the
* License, or  any later version.
*
* Dolphin is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
* without even the implied warranty of  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
* See the GNU General Public License for more details.
* You should have received a copy of the GNU General Public License along with Dolphin,
* see license.txt file; if not, write to marketing@boonex.com
***************************************************************************/

bx_import('BxDolModuleDb');

class HcCasesDb extends BxDolModuleDb {

	function HcCasesDb(&$oConfig) {
		parent::BxDolModuleDb();
        $this->_sPrefix = $oConfig->getDbPrefix();
    }

    function getSettingsCategory() {
        return $this->getOne("SELECT * FROM `sys_options_cats` WHERE `name` = 'HC Cases' ORDER BY ID DESC LIMIT 1");
    }

    function getEntryById ($iEntryId) {
        return $this->getRow("SELECT * FROM `" . $this->_sPrefix . "posts` WHERE `id` = '$iEntryId'");
    }

    function getAllPosts ($iLimit) {
        return $this->getAll("SELECT *, LEFT(`text`, 256) AS `text` FROM `" . $this->_sPrefix . "posts` ORDER BY `added` DESC LIMIT $iLimit");
    }
}

?>
