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

bx_import('BxDolModule');

class HcCasesModule extends BxDolModule {

    function HcCasesModule(&$aModule) {
        parent::BxDolModule($aModule);

        $this->aForm = array(

            'form_attrs' => array(
                'name'     => 'form_cases',
                'action'   => '',
                'method'   => 'post',
            ),

            'params' => array (
                'db' => array(
                    'table' => 'hc_cases_posts',
                    'key' => 'id',
                    'submit_name' => 'submit_form',
                ),
            ),

            'inputs' => array(

                'title' => array(
                    'type' => 'text',
                    'name' => 'title',
                    'caption' => _t('_hc_cases_form_caption_title'),
                    'required' => true,
                    'checker' => array (
                        'func' => 'length',
                        'params' => array(3,255),
                        'error' => _t ('_hc_cases_form_err_title'),
                    ),
                    'db' => array (
                        'pass' => 'Xss',
                    ),
                ),

                'text' => array(
                    'type' => 'textarea',
                    'name' => 'text',
                    'caption' => _t('_hc_cases_form_caption_text'),
                    'required' => true,
                    'html' => 2,
                    'checker' => array (
                        'func' => 'length',
                        'params' => array(20,64000),
                        'error' => _t ('_hc_cases_form_err_text'),
                    ),
                    'db' => array (
                        'pass' => 'XssHtml',
                    ),
                ),

                'submit' => array (
                    'type' => 'submit',
                    'name' => 'submit_form',
                    'value' => _t('_Submit'),
                    'colspan' => true,
                ),

            ),
        );
    }

    function actionHome () {
        $this->_oTemplate->pageStart(); // all the code below will be wrapped by the user design

        $aPosts = $this->_oDb->getAllPosts (getParam('hc_cases_max_posts_to_show')); // get all posts from database
        foreach ($aPosts as $sKey => $aRow) { // add human readable values to the resulted array
            $aPosts[$sKey]['url'] = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aRow['id'];
            $aPosts[$sKey]['added'] = defineTimeInterval($aRow['added']);
            $aPosts[$sKey]['author'] = getNickName($aRow['author_id']);
        }
        $aVars = array ( // define template variables
            'bx_repeat:posts' => $aPosts,
        );
        echo $this->_oTemplate->parseHtmlByName('main', $aVars); // output posts list

        $this->_oTemplate->pageCode(_t('_hc_cases'), true); // output is completed, display all output above data wrapped by user design
    }

    function actionAdministration () {

        if (!$GLOBALS['logged']['admin']) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }

        $this->_oTemplate->pageStart();

        $iId = $this->_oDb->getSettingsCategory(); // get our setting category id
        if(empty($iId)) { // if category is not found display page not found
            echo MsgBox(_t('_sys_request_page_not_found_cpt'));
            $this->_oTemplate->pageCodeAdmin (_t('_hc_cases'));
            return;
        }

        bx_import('BxDolAdminSettings'); // import class

        $mixedResult = '';
        if(isset($_POST['save']) && isset($_POST['cat'])) { // save settings
            $oSettings = new BxDolAdminSettings($iId);
            $mixedResult = $oSettings->saveChanges($_POST);
        }

        $oSettings = new BxDolAdminSettings($iId); // get display form code
        $sResult = $oSettings->getForm();

        if($mixedResult !== true && !empty($mixedResult)) // attach any resulted messages at the form beginning
            $sResult = $mixedResult . $sResult;

        echo DesignBoxAdmin (_t('_hc_cases'), 'It works!', '', '', 11);

        $this->_oTemplate->pageCodeAdmin (_t('_hc_cases'));
    }

    function actionAdd () {

        if (!$GLOBALS['logged']['member'] && !$GLOBALS['logged']['admin']) { // check access to the page
            $this->_oTemplate->displayAccessDenied ();
            return;
        }

        $this->_oTemplate->pageStart(); // all the code below will be wrapped by the user design

        bx_import ('BxTemplFormView'); // import forms class

        $oForm = new BxTemplFormView ($this->aForm); // create foprms class

        $oForm->initChecker(); // init form checker

        if ($oForm->isSubmittedAndValid ()) { // if form is submitted and not form errors were found, save form data

            $aValsAdd = array ( // add additional fields
                'added' => time(),
                'author_id' => $_COOKIE['memberID'],
            );
            $iEntryId = $oForm->insert ($aValsAdd); // insert data to database

            if ($iEntryId) { // if post was successfully added
                $sRedirectUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $iEntryId;
                header ('Location:' . $sRedirectUrl); // redirect to created post view page
                exit;
            } else {
                MsgBox(_t('_Error Occured')); // if error occured display erroro message
            }

        } else {

            echo $oForm->getCode (); // display form, if the form is not submiyyed or data is invalid

        }

        $this->_oTemplate->pageCode(_t('_hc_cases_page_title_add'), true); // output is completed, display all output above data wrapped by user design
    }

    function actionView ($iEntryId) {

        $aEntry = $this->_oDb->getEntryById ((int)$iEntryId);
        if (!$aEntry) { // check if entry exists
            $this->_oTemplate->displayPageNotFound ();
            return;
        }

        $this->_oTemplate->pageStart(); // all the code below will be wrapped by the user design

        $aVars = array (
            'title' => $aEntry['title'],
            'text' => $aEntry['text'],
            'author' => getNickName($aEntry['author_id']),
            'added' => defineTimeInterval($aEntry['added']),
        );
        echo $this->_oTemplate->parseHtmlByName('view', $aVars); // display post

        $this->_oTemplate->pageCode($aEntry['title'], true); // output is completed, display all output above data wrapped by user design
    }
}

?>
