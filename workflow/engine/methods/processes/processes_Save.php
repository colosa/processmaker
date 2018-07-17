<?php
/**
 * processes_Save.php
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

/*
 * This is a ajax response file
 *
 */



use ProcessMaker\Plugins\PluginRegistry;

$function = isset( $_POST['function'] ) ? $_POST['function'] : '';
$infoProcess = new Process();
$resultProcessOld = $infoProcess->load($_POST['form']['PRO_UID']);
unset($resultProcessOld['PRO_DYNAFORMS']);

switch ($function) {
    case 'lookForNameProcess':
        require_once 'classes/model/Content.php';
        $snameProcess = urldecode( $_POST['NAMEPROCESS'] );
        $oCriteria = new Criteria( 'workflow' );
        $oCriteria->addSelectColumn( 'COUNT(*) AS PROCESS' );
        $oCriteria->add(ProcessPeer::PRO_TITLE, $snameProcess);
        $oDataset = ContentPeer::doSelectRS( $oCriteria );
        $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
        $oDataset->next();
        $aRow = $oDataset->getRow();
        print ($aRow['PROCESS'] ? true : false) ;
        break;

    default:
        if (isset( $_GET['PRO_UID'] )) {
            $_POST['form']['PRO_UID'] = $_GET['PRO_UID'];
        }

        $_POST['form']['PRO_TITLE'] = trim( $_POST['form']['PRO_TITLE'] );

        $oProcessMap = new ProcessMap();
        if (! isset( $_POST['form']['PRO_UID'] )) {
            $_POST['form']['USR_UID'] = $_SESSION['USER_LOGGED'];
            //$oJSON = new Services_JSON();
            require_once 'classes/model/Task.php';

            $sProUid = $oProcessMap->createProcess( $_POST['form'] );

            //call plugins
            $oData['PRO_UID'] = $sProUid;
            $oData['PRO_TEMPLATE'] = (isset( $_POST['form']['PRO_TEMPLATE'] ) && $_POST['form']['PRO_TEMPLATE'] != '') ? $_POST['form']['PRO_TEMPLATE'] : '';
            $oData['PROCESSMAP'] = $oProcessMap;

            $oPluginRegistry = PluginRegistry::loadSingleton();
            $oPluginRegistry->executeTriggers( PM_NEW_PROCESS_SAVE, $oData );

            G::header( 'location: processes_Map?PRO_UID=' . $sProUid );
            die();
        } else {
            $_POST['form']['PRO_DYNAFORMS'] = array ();
            $_POST['form']['PRO_DYNAFORMS']['PROCESS'] = isset( $_POST['form']['PRO_SUMMARY_DYNAFORM'] ) ? $_POST['form']['PRO_SUMMARY_DYNAFORM'] : '';
            unset( $_POST['form']['PRO_SUMMARY_DYNAFORM'] );
            $oProcessMap->updateProcess( $_POST['form'] );
            $sProUid = $_POST['form']['PRO_UID'];
        }

        //Save Calendar ID for this process

        $calendarObj = new Calendar();
        $calendarObj->assignCalendarTo( $sProUid, $_POST['form']['PRO_CALENDAR'], 'PROCESS' );

        if ($_POST['form']['THETYPE'] == '') {
            G::header( 'location: main' );
        }
        break;

}

$resultProcessNew = $infoProcess->load($_POST['form']['PRO_UID']);
unset($resultProcessNew['PRO_DYNAFORMS']);

$newFields = array_diff_assoc($resultProcessNew,$resultProcessOld);
$fields = array();

if(array_key_exists('PRO_TITLE', $newFields)) {
    $fields[] = G::LoadTranslation('ID_TITLE');
}
if(array_key_exists('PRO_DESCRIPTION', $newFields)) {
    $fields[] = G::LoadTranslation('ID_DESCRIPTION');
}
if(array_key_exists('PRO_CALENDAR', $newFields)) {
    $fields[] = G::LoadTranslation('ID_CALENDAR');
}
if(array_key_exists('PRO_CATEGORY', $newFields)) {
    $fields[] = "Process Category";
}
if(array_key_exists('PRO_SUMMARY_DYNAFORM', $newFields)) {
    $fields[] = "Dynaform to show a case summary";
}
if(array_key_exists('PRO_DERIVATION_SCREEN_TPL', $newFields)) {
    $fields[] = "Routing Screen Template";
}
if(array_key_exists('PRO_DEBUG', $newFields)) {
    $fields[] = G::LoadTranslation('ID_PRO_DEBUG');
}
if(array_key_exists('PRO_SHOW_MESSAGE', $newFields)) {
    $fields[] = "Hide the case number and the case title in the steps";
}
if(array_key_exists('PRO_SUBPROCESS', $newFields)) {
    $fields[] = "This a sub process";
}
if(array_key_exists('PRO_TRI_DELETED', $newFields)) {
    $fields[] = "Execute a trigger when a case is deleted";
}
if(array_key_exists('PRO_TRI_CANCELED', $newFields)) {
    $fields[] = "Execute a trigger when a case is canceled";
}
if(array_key_exists('PRO_TRI_PAUSED', $newFields)) {
    $fields[] = "Execute a trigger when a case is paused";
}
if(array_key_exists('PRO_TRI_REASSIGNED', $newFields)) {
    $fields[] = "Execute a trigger when a case is reassigned";
}
if(array_key_exists('PRO_TRI_UNPAUSED', $newFields)) {
    $fields[] = "Execute a trigger when a case is unpaused";
}
if(array_key_exists('PRO_TYPE_PROCESS', $newFields)) {
    $fields[] = "Type of process (only owners can edit private processes)";
}
G::auditLog('EditProcess','Edit fields ('.implode(', ',$fields).') in process "'.$_POST['form']['PRO_TITLE'].'"');

if(isset($_POST['form']['PRO_UID']) && !empty($_POST['form']['PRO_UID'])) {
    $valuesProcess['PRO_UID'] = $_POST['form']['PRO_UID'];
    $valuesProcess['PRO_UPDATE_DATE'] = date("Y-m-d H:i:s");

    $infoProcess = new Processes();
    $resultProcess = $infoProcess->updateProcessRow($valuesProcess);
}