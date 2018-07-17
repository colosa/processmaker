<?php
/**
 * processes_ImportFile.php
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

    $oProcess = new Processes();

    //  if ( isset ($_POST) ) {
    //  	krumo ( $_POST );
    //  }


    if (isset( $_POST['form']['PRO_FILENAME'] )) {
        $path = $_POST['form']['PRO_PATH'];
        $filename = $_POST['form']['PRO_FILENAME'];
        $action = $_POST['form']['GROUP_IMPORT_OPTION'];
    } else {
        //save the file, if it's not saved
        if ($_FILES['form']['error']['PROCESS_FILENAME'] == 0) {
            $filename = $_FILES['form']['name']['PROCESS_FILENAME'];
            $path = PATH_DOCUMENT . 'input' . PATH_SEP;
            $tempName = $_FILES['form']['tmp_name']['PROCESS_FILENAME'];
            $action = "none";
            G::uploadFile( $tempName, $path, $filename );
        }
    }
    //we check if the file is a pm file
    $aExtPmfile = explode( '.', $filename );
    if ($aExtPmfile[sizeof( $aExtPmfile ) - 1] != 'pm') {
        throw (new Exception( G::LoadTranslation( 'ID_NOT_PM_FILE' ) ));
    }

    $oData = $oProcess->getProcessData( $path . $filename );

    $Fields['PRO_FILENAME'] = $filename;
    $Fields['IMPORT_OPTION'] = 2;

    $sProUid = $oData->process['PRO_UID'];

    $oData->process['PRO_UID_OLD'] = $sProUid;

    if ($oProcess->processExists( $sProUid )) {
        $G_MAIN_MENU = 'processmaker';
        $G_ID_MENU_SELECTED = 'PROCESSES';
        $G_PUBLISH = new Publisher();
        $G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'processes/processes_ImportExisting', '', $Fields, 'processes_ImportExisting' );
        G::RenderPage( 'publish', 'blank' );
        die();
    }
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
    if ($groupsDuplicated > 0) {
        $Fields['PRO_FILENAME'] = $filename;
        $Fields['PRO_PATH'] = $path;
        $Fields['IMPORT_OPTION'] = 2;
        $G_MAIN_MENU = 'processmaker';
        $G_ID_MENU_SELECTED = 'PROCESSES';
        $G_PUBLISH = new Publisher();
        $G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'processes/processes_ValidatingGroups', '', $Fields, 'processes_ImportFile' );
        G::RenderPage( 'publish', 'blank' );
        die();
    }
    // end added code
    $oProcess->createProcessFromData( $oData, $path . $filename );
    G::header( 'Location: processes_Map?PRO_UID=' . $sProUid );

} catch (Exception $e) {
    $G_PUBLISH = new Publisher();
    $aMessage['MESSAGE'] = $e->getMessage();
    $G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'login/showMessage', '', $aMessage );
    G::RenderPage( 'publish', 'blank' );
}
