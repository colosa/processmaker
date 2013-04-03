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

$aFields['PRO_UID'] = $_GET['PRO_UID'];
$aFields['TRI_TYPE'] = 'SCRIPT';
if (isset( $_GET['TRI_UID'] ) && ($_GET['TRI_UID'] != "")) {
    $oTrigger = new Triggers();
    $aFields = $oTrigger->load( $_GET['TRI_UID'] );
}
$xmlform = 'triggers/triggersCustom';

G::LoadClass( 'xmlfield_InputPM' );
$G_PUBLISH = new Publisher();
$G_PUBLISH->AddContent( 'xmlform', 'xmlform', $xmlform, '', $aFields, '../triggers/triggers_Save' );
$oHeadPublisher =& headPublisher::getSingleton();
//$oHeadPublisher->addScriptFile('/js/codemirror/js/codemirror.js', 1);
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
G::RenderPage( 'publish', 'raw' );

