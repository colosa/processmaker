<?php
/**
 * processes_ImportFileExisting.php
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

try {
    //load the variables
    G::LoadClass( 'processes' );
    $oProcess = new Processes();

    if (! isset( $_POST['form']['IMPORT_OPTION'] )) {
        throw (new Exception( 'Please select an option before to continue' ));
    }

    if (! isset( $_POST['form']['GROUP_IMPORT_OPTION'] )) {
        $action = "none";
    } else {
        $action = $_POST['form']['GROUP_IMPORT_OPTION'];
    }

    $option = $_POST['form']['IMPORT_OPTION'];
    $filename = $_POST['form']['PRO_FILENAME'];
    $ObjUid = $_POST['form']['OBJ_UID'];

    $path = PATH_DOCUMENT . 'input' . PATH_SEP;
    $oData = $oProcess->getProcessData( $path . $filename );

    $Fields['PRO_FILENAME'] = $filename;
    $sProUid = $oData->process['PRO_UID'];

    $oData->process['PRO_UID_OLD'] = $sProUid;

    // code added by gustavo cruz gustavo-at-colosa-dot-com
    // evaluate actions or import options
    switch ($action) {
        case "none":
            $groupsDuplicated = $oProcess->checkExistingGroups( $oData->groupwfs );
            break;
        case "rename":
            $oData->groupwfs = $oProcess->renameExistingGroups( $oData->groupwfs );
            $groupsDuplicated = $oProcess->checkExistingGroups( $oData->groupwfs );
            break;
        case "merge":
            $oBaseGroup = $oData->groupwfs;
            $oNewGroup = $oProcess->mergeExistingGroups( $oData->groupwfs );
            $oData->groupwfs = $oNewGroup;
            $oData->taskusers = $oProcess->mergeExistingUsers( $oBaseGroup, $oNewGroup, $oData->taskusers );

            break;
        default:
            $groupsDuplicated = $oProcess->checkExistingGroups( $oData->groupwfs );
            break;
    }

    // if there are duplicated groups render the group importing options
    if ((isset( $groupsDuplicated )) && ($groupsDuplicated > 0)) {
        $Fields['PRO_FILENAME'] = $filename;
        $Fields['PRO_PATH'] = $path;
        $Fields['IMPORT_OPTION'] = $option;
        $Fields['OBJ_UID'] = $ObjUid;
        $G_MAIN_MENU = 'processmaker';
        $G_ID_MENU_SELECTED = 'PROCESSES';
        $G_PUBLISH = new Publisher();
        $G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'processes/processes_ValidatingGroups', '', $Fields, 'processes_ImportExisting' );
        G::RenderPage( 'publish', 'blank' );
        die();
    }
    //end added code


    //Update the current Process, overwriting all tasks and steps
    if ($option == 1) {
        $oProcess->updateProcessFromData( $oData, $path . $filename );
        if (file_exists( PATH_OUTTRUNK . 'compiled' . PATH_SEP . 'xmlform' . PATH_SEP . $sProUid )) {
            $oDirectory = dir( PATH_OUTTRUNK . 'compiled' . PATH_SEP . 'xmlform' . PATH_SEP . $sProUid );
            while ($sObjectName = $oDirectory->read()) {
                if (($sObjectName != '.') && ($sObjectName != '..')) {
                    unlink( PATH_OUTTRUNK . 'compiled' . PATH_SEP . 'xmlform' . PATH_SEP . $sProUid . PATH_SEP . $sObjectName );
                }
            }
            $oDirectory->close();
        }
        $sNewProUid = $sProUid;
    }

    //Disable current Process and create a new version of the Process
    if ($option == 2) {
        $oProcess->disablePreviousProcesses( $sProUid );
        $sNewProUid = $oProcess->getUnusedProcessGUID();
        $oProcess->setProcessGuid( $oData, $sNewProUid );
        $oProcess->setProcessParent( $oData, $sProUid );
        $oData->process['PRO_TITLE'] = "New - " . $oData->process['PRO_TITLE'] . ' - ' . date( 'M d, H:i' );
        $oProcess->renewAll( $oData );
        $oProcess->createProcessFromData( $oData, $path . $filename );
    }

    //Create a completely new Process without change the current Process
    if ($option == 3) {
        //krumo ($oData); die;
        $sNewProUid = $oProcess->getUnusedProcessGUID();
        $oProcess->setProcessGuid( $oData, $sNewProUid );
        $oData->process['PRO_TITLE'] = "Copy of  - " . $oData->process['PRO_TITLE'] . ' - ' . date( 'M d, H:i' );
        $oProcess->renewAll( $oData );
        $oProcess->createProcessFromData( $oData, $path . $filename );
    }
    G::header( 'Location: processes_Map?PRO_UID=' . $sNewProUid );
} catch (Exception $e) {
    $G_PUBLISH = new Publisher();
    $aMessage['MESSAGE'] = $e->getMessage();
    $G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'login/showMessage', '', $aMessage );
    G::RenderPage( 'publish', 'blank' );
}
