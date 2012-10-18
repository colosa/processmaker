<?php
/**
 * events_EditAction.php
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
global $RBAC;
global $_DBArray;

if ($RBAC->userCanAccess( 'PM_SETUP' ) != 1) {
    G::SendTemporalMessage( 'ID_USER_HAVENT_RIGHTS_PAGE', 'error', 'labels' );
    G::header( 'location: ../login/login' );
    die();
}
if (isset( $_SESSION['EVN_UID'] )) {
    $evnUid = $_SESSION['EVN_UID'];
    unset( $_SESSION['EVN_UID'] );
} else {
    $evnUid = $_GET['EVN_UID'];
}

require_once 'classes/model/Event.php';
require_once 'classes/model/Triggers.php';
$oEvent = new Event();
$oTrigger = new Triggers();
$aFields = $oEvent->load( $evnUid );
$parameters = unserialize( $oEvent->getEvnActionParameters() );
//g::pr($parameters); die;
$aTrigger = $oTrigger->load( $aFields['TRI_UID'] );

$hash = md5( $oTrigger->getTriWebbot() );
//var_dump($hash,$parameters->hash);die;
//if the hash is different, the script was edited , so we will show the trigger editor.
if ((isset( $parameters->hash ) && $hash != $parameters->hash) || $aFields['EVN_ACTION'] == 'EXECUTE_TRIGGER' || $aFields['EVN_ACTION'] == 'EXECUTE_CONDITIONAL_TRIGGER') {
    $oTriggerParams = unserialize( $aTrigger['TRI_PARAM'] );
    // check again a hash, this time to check the trigger itself integrity
    if ($oTriggerParams['hash'] != $hash) {
        // if has changed edit manually
        G::LoadClass( 'xmlfield_InputPM' );
        $G_PUBLISH = new Publisher();
        $G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'triggers/triggersNarrowEdit', '', $aTrigger, '../events/triggersSave' );
        G::RenderPage( 'publish', 'raw' );
        die();
    } else {
        // if not launch the wizard view.
        $triUid = $aFields['TRI_UID'];
        $_GET = $oTriggerParams['params'];
        $_GET['TRI_UID'] = $triUid;
        require_once (PATH_METHODS . 'triggers/triggers_EditWizard.php');
        die();
    }
}

$aFields['EVN_MESSAGE_SUBJECT'] = (isset( $parameters->SUBJECT ) ? $parameters->SUBJECT : '');

if (isset( $parameters->TO )) {
    $paramTO[] = Array ('id' => 'char','name' => 'char' );

    //echo '<pre>';print_r($parameters->TO);
    foreach ($parameters->TO as $item) {
        $row = explode( '|', $item );
        switch ($row[0]) {
            case 'usr':
                require_once ('classes/model/Users.php');
                $user = new Users();
                if ($row[1] == '-1') {
                    $value = '(Current Task User)';
                } else {
                    $rec = $user->load( $row[1] );
                    $value = $rec['USR_FIRSTNAME'] . ' ' . $rec['USR_LASTNAME'];
                }
                break;
            case 'grp':
                G::LoadClass( 'groups' );
                $group = new Groups();
                $rec = $group->load( $row[1] );
                $value = strip_tags( $rec->getGrpTitle() );
                break;
            case 'ext':
                $value = htmlentities( $row[1] );
                break;
            case 'dyn':
                $value = htmlentities( '@#' . $row[1] );
                break;
            default:
                echo '->' . $row[0];
        }
        $paramTO[] = Array ('id' => replaceQuotes( $item ),'name' => $value
        );
    }
} else {
    $paramTO[] = Array ('id' => 'char','name' => 'char'
    );
    $paramTO[] = Array ('id' => 'usr|-1','name' => '(Current Task User)'
    );
}
$_DBArray['eventomsgto'] = $paramTO;

if (isset( $parameters->CC )) {
    $paramCC[] = Array ('id' => 'char','name' => 'char' );
    foreach ($parameters->CC as $item) {
        $row = explode( '|', $item );
        switch ($row[0]) {
            case 'usr':
                require_once ('classes/model/Users.php');
                $user = new Users();

                if ($row[1] == '-1') {
                    $value = '(Current Task User)';
                } else {
                    $rec = $user->load( $row[1] );
                    $value = $rec['USR_FIRSTNAME'] . ' ' . $rec['USR_LASTNAME'];
                }
                break;
            case 'grp':
                G::LoadClass( 'groups' );
                $group = new Groups();
                $rec = $group->load( $row[1] );
                $value = strip_tags( $rec->getGrpTitle() );
                break;
            case 'ext':
                $value = htmlentities( $row[1] );
                break;
            case 'dyn':
                $value = htmlentities( '@#' . $row[1] );
                break;
        }
        $paramCC[] = Array ('id' => replaceQuotes( $item ),'name' => $value
        );
    }

    $_DBArray['eventomsgcc'] = $paramCC;

} else {
    $_DBArray['eventomsgcc'] = Array ();
}

if (isset( $parameters->BCC )) {
    $paramBCC[] = Array ('id' => 'char','name' => 'char' );
    foreach ($parameters->BCC as $item) {
        $row = explode( '|', $item );
        switch ($row[0]) {
            case 'usr':
                require_once ('classes/model/Users.php');
                $user = new Users();

                if ($row[1] == '-1') {
                    $value = '(Current Task User)';
                } else {
                    $rec = $user->load( $row[1] );
                    $value = $rec['USR_FIRSTNAME'] . ' ' . $rec['USR_LASTNAME'];
                }
                break;
            case 'grp':
                G::LoadClass( 'groups' );
                $group = new Groups();
                $rec = $group->load( $row[1] );
                $value = strip_tags( $rec->getGrpTitle() );
                break;
            case 'ext':
                $value = htmlentities( $row[1] );
                break;
            case 'dyn':
                $value = htmlentities( '@#' . $row[1] );
                break;
        }
        $paramBCC[] = Array ('id' => replaceQuotes( $item ),'name' => $value);
    }

    $_DBArray['eventomsgbcc'] = $paramBCC;

} else {
    $_DBArray['eventomsgbcc'] = Array ();
}
$aFields['EVN_MESSAGE_TO_TO'] = $paramTO;
$aFields['EVN_MESSAGE_TO_CC'] = (isset( $parameters->CC ) ? $paramCC : '');
$aFields['EVN_MESSAGE_TO_BCC'] = (isset( $parameters->BCC ) ? $paramBCC : '');
$aFields['EVN_MESSAGE_TEMPLATE'] = (isset( $parameters->TEMPLATE ) ? $parameters->TEMPLATE : '');

$aTemplates = array ();
$aTemplates[] = array ('TEMPLATE1' => 'char','TEMPLATE2' => 'char');
$sDirectory = PATH_DATA_MAILTEMPLATES . $aFields['PRO_UID'] . PATH_SEP;
G::verifyPath( $sDirectory, true );
if (! file_exists( $sDirectory . 'alert_message.html' )) {
    @copy( PATH_TPL . 'mails' . PATH_SEP . 'alert_message.html', $sDirectory . 'alert_message.html' );
}
$oDirectory = dir( $sDirectory );
while ($sObject = $oDirectory->read()) {
    if (($sObject !== '.') && ($sObject !== '..') && ($sObject !== 'alert_message.html')) {
        $aTemplates[] = array ('TEMPLATE1' => $sObject,'TEMPLATE2' => $sObject);
    }
}
$_DBArray['templates'] = $aTemplates;

$aTriggers[] = array ('TRI_UID' => 'char','TRI_TITLE' => 'char');
G::LoadClass( 'processMap' );
$oProcessMap = new ProcessMap();
$oDataset = TriggersPeer::doSelectRS( $oProcessMap->getTriggersCriteria( $aFields['PRO_UID'] ) );
$oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
$oDataset->next();
while ($aRow = $oDataset->getRow()) {
    $aTriggers[] = array ('TRI_UID' => $aRow['TRI_UID'],'TRI_TITLE' => $aRow['TRI_TITLE'] );
    $oDataset->next();
}
$_DBArray['triggers'] = $aTriggers;

$_SESSION['_DBArray'] = $_DBArray;

$G_PUBLISH = new Publisher();
$G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'events/eventsEditAction', '', $aFields, '../events/eventsSave' );
//$G_PUBLISH->AddContent('xmlform', 'xmlform', 'events/eventsEditAction', '', $aFields, '../events/eventsSave');
G::RenderPage( 'publish', 'raw' );

function replaceQuotes ($aData)
{
    return str_replace( '"', '&quote;', $aData );
}

