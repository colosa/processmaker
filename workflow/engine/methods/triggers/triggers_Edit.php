<?php
/**
 * triggers_Edit.php
 *
 * ProcessMaker Open Source Edition
 * Copyright (C) 2004 - 2008 Colosa Inc.23
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 * For more information, contact Colosa Inc, 2566 Le Jeune Rd.,
 * Coral Gables, FL, 33134, USA, or email info@colosa.com.
 */
if (($RBAC_Response = $RBAC->userCanAccess( "PM_FACTORY" )) != 1) {
    return $RBAC_Response;
}
require_once ('classes/model/Triggers.php');

if (isset( $_GET['TRI_UID'] )) {
    $oTrigger = new Triggers();
    // check if its necessary bypass the wizard editor
    if (isset( $_GET['BYPASS'] ) && $_GET['BYPASS'] == '1') {
        $editWizardSource = true;
    } else {
        $editWizardSource = false;
    }
    $aFields = $oTrigger->load( $_GET['TRI_UID'] );
    $aTriggerData = unserialize( $aFields['TRI_PARAM'] );
    // if trigger has been created with the wizard the TRI_PARAM field cant be empty
    if ($aFields['TRI_PARAM'] != '' && ! $editWizardSource) {
        $aTriggerData = unserialize( $aFields['TRI_PARAM'] );
        // if the trigger has been modified manually, it cant be edited with the wizard.
        if (md5( $aFields['TRI_WEBBOT'] ) == $aTriggerData['hash']) {
            $triUid = $_GET['TRI_UID'];
            $_GET = $aTriggerData['params'];
            $_GET['TRI_UID'] = $triUid;
            require_once ('triggers_EditWizard.php');
            die();
        } else {
            // custom trigger edit
            $xmlform = 'triggers/triggers_Edit';
            $xmlform_action = '../triggers/triggers_Save';
        }
    } else {
        // custom trigger edit
        $xmlform = 'triggers/triggers_Edit';
        $xmlform_action = '../triggers/triggers_Save';
    }
} else {
    //if its a new trigger
    $aFields['PRO_UID'] = $_GET['PRO_UID'];
    $aFields['TRI_TYPE'] = 'SCRIPT';
    $xmlform = 'triggers/triggersProperties';
    $xmlform_action = '../triggers/triggers_Save';
}
G::LoadClass( 'xmlfield_InputPM' );
$G_PUBLISH = new Publisher();
$G_PUBLISH->AddContent( 'xmlform', 'xmlform', $xmlform, '', $aFields, $xmlform_action );
$oHeadPublisher =& headPublisher::getSingleton();
//$oHeadPublisher->addScriptFile('/js/codemirror/js/codemirror.js', 1);
$oHeadPublisher->addCssFile('/js/codemirror/lib/codemirror.css', 1);
$oHeadPublisher->addCssFile('/js/codemirror/addon/hint/show-hint.css', 1);
$oHeadPublisher->addScriptFile('/js/codemirror/lib/codemirror.js', 1);
$oHeadPublisher->addScriptFile("/js/codemirror/addon/edit/matchbrackets.js",1);
$oHeadPublisher->addScriptFile("/js/codemirror/mode/htmlmixed/htmlmixed.js",1);
$oHeadPublisher->addScriptFile("/js/codemirror/mode/xml/xml.js",1);
$oHeadPublisher->addScriptFile("/js/codemirror/mode/javascript/javascript.js",1);
$oHeadPublisher->addScriptFile("/js/codemirror/mode/css/css.js",1);
$oHeadPublisher->addScriptFile("/js/codemirror/mode/clike/clike.js",1);
$oHeadPublisher->addScriptFile("/js/codemirror/addon/hint/show-hint.js",1);
$oHeadPublisher->addScriptFile("/js/codemirror/addon/hint/php-hint.js",1);
$oHeadPublisher->addScriptFile("/js/codemirror/mode/php/php.js",1);

//Hack: CodeMirror needed to run Internet Explorer
$ie = (strrpos($_SERVER['HTTP_USER_AGENT'], "MSIE") === false ) ? false : true;
if ($ie) {
    echo "<!DOCTYPE html>\n";
}

G::RenderPage( 'publish', 'blank' );

