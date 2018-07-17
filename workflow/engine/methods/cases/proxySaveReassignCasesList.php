<?php
/**
 * proxySaveReassignCasesList.php
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
$aData = G::json_decode( $_POST['data'] );
$appSelectedUids = array ();
$items = explode( ",", $_POST['APP_UIDS'] );
foreach ($items as $item) {
    $dataUids = explode( "|", $item );
    $appSelectedUids[] = $dataUids[0];
}

$casesReassignedCount = 0;
$serverResponse = array ();

$oCases = new Cases();

$oAppCacheView = new AppCacheView();
$oAppDel = new AppDelegation();
$oCasesReassignList = $oAppCacheView->getToReassignListCriteria(null);
if (isset( $_POST['selected'] ) && $_POST['selected'] == 'true') {
    $oCasesReassignList->add( AppCacheViewPeer::APP_UID, $appSelectedUids, Criteria::IN );
}
// if there are no records to save return -1
if (empty( $aData )) {
    $serverResponse['TOTAL'] = - 1;
    echo G::json_encode( $serverResponse );
    die();
}

if (is_array( $aData )) {
    $currentCasesReassigned = 0;
    require_once ("classes/model/AppNotes.php");
    foreach ($aData as $data) {
        $oTmpReassignCriteria = $oCasesReassignList;
        $oTmpReassignCriteria->add( AppCacheViewPeer::APP_UID, $data->APP_UID );
        $oTmpReassignCriteria->add( AppCacheViewPeer::TAS_UID, $data->TAS_UID );
        $rs = AppCacheViewPeer::doSelectRS( $oTmpReassignCriteria );
        $rs->setFetchmode( ResultSet::FETCHMODE_ASSOC );
        $rs->next();
        $row = $rs->getRow();

        //Current users of OPEN DEL_INDEX thread
        $aCurUser = $oAppDel->getCurrentUsers($row['APP_UID'], $row['DEL_INDEX']);
        $flagReassign = true;
        if(!empty($aCurUser)){
            foreach ($aCurUser as $key => $value) {
                if($value === $data->APP_REASSIGN_USER_UID){
                    $flagReassign = false;
                }
            }
        } else {
            //DEL_INDEX is CLOSED
            throw new Exception(G::LoadTranslation('ID_REASSIGNMENT_ERROR'));
        }

        //If the currentUser is diferent to nextUser, create the thread
        if($flagReassign){
            $oCases->reassignCase( $row['APP_UID'], $row['DEL_INDEX'], ($row['USR_UID'] != '' ? $row['USR_UID'] : $_SESSION['USER_LOGGED']), $data->APP_REASSIGN_USER_UID );
        }

        $currentCasesReassigned ++;
        $casesReassignedCount ++;
        $serverResponse[] = array ('APP_REASSIGN_USER' => $data->APP_REASSIGN_USER,'APP_TITLE' => $data->APP_TITLE,'TAS_TITLE' => $data->APP_TAS_TITLE,'REASSIGNED_CASES' => $currentCasesReassigned
        );

        // Save the note reassign reason
        if (isset($data->NOTE_REASON) && $data->NOTE_REASON !== '') {
            $appNotes = new AppNotes();
            $noteContent = addslashes($data->NOTE_REASON);
            $appNotes->postNewNote($row['APP_UID'], $_SESSION['USER_LOGGED'], $noteContent, isset($data->NOTIFY_REASSIGN) ? $data->NOTIFY_REASSIGN : false);
        }
    }
} else {
    $oTmpReassignCriteria = $oCasesReassignList;
    $oTmpReassignCriteria->add( AppCacheViewPeer::TAS_UID, $aData->TAS_UID );
    $rs = AppCacheViewPeer::doSelectRS( $oTmpReassignCriteria );
    $rs->setFetchmode( ResultSet::FETCHMODE_ASSOC );
    $rs->next();
    $row = $rs->getRow();
    $currentCasesReassigned = 0;
    while (is_array( $row )) {
        $APP_UID = $row['APP_UID'];
        $aCase = $oCases->loadCaseInCurrentDelegation( $APP_UID );

        //Current users of OPEN DEL_INDEX thread
        $aCurUser = $oAppDel->getCurrentUsers($APP_UID, $aCase['DEL_INDEX']);
        $flagReassign = true;
        if(!empty($aCurUser)){
            foreach ($aCurUser as $key => $value) {
                if($value === $aData->APP_REASSIGN_USER_UID){
                    $flagReassign = false;
                }
            }
        } else {
            //DEL_INDEX is CLOSED
            throw new Exception(G::LoadTranslation('ID_REASSIGNMENT_ERROR'));
        }

        //If the currentUser is diferent to nextUser, create the thread
        if($flagReassign){
            $oCases->reassignCase( $aCase['APP_UID'], $aCase['DEL_INDEX'], ($aCase['USR_UID'] != '' ? $aCase['USR_UID'] : $_SESSION['USER_LOGGED']), $aData->APP_REASSIGN_USER_UID );
        }

        $currentCasesReassigned ++;
        $casesReassignedCount ++;
        $rs->next();
        $row = $rs->getRow();
    }
    $serverResponse[] = array ('TAS_TITLE' => $aData->APP_TAS_TITLE,'REASSIGNED_CASES' => $currentCasesReassigned);
}
$serverResponse['TOTAL'] = $casesReassignedCount;
echo G::json_encode( $serverResponse );

