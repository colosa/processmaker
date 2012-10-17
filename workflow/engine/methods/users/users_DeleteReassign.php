<?php
/**
 * cases_ReassignByUser.php
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
    global $RBAC;
    switch ($RBAC->userCanAccess( 'PM_REASSIGNCASE' )) {
        case - 2:
            G::SendTemporalMessage( 'ID_USER_HAVENT_RIGHTS_SYSTEM', 'error', 'labels' );
            G::header( 'location: ../login/login' );
            die();
            break;
        case - 1:
            G::SendTemporalMessage( 'ID_USER_HAVENT_RIGHTS_PAGE', 'error', 'labels' );
            G::header( 'location: ../login/login' );
            die();
            break;
    }

    if (! isset( $_GET['USR_UID'] )) {
        $_GET['USR_UID'] = '';
    }

    $G_MAIN_MENU = 'processmaker';
    $G_SUB_MENU = 'users';
    $G_ID_MENU_SELECTED = 'USERS';
    $G_ID_SUB_MENU_SELECTED = 'USERS';
    $G_PUBLISH = new Publisher();

    if ($_GET['USR_UID'] != '') {
        $c = 0;
        $oTemplatePower = new TemplatePower( PATH_TPL . 'users/users_DeleteReassign.html' );
        $oTemplatePower->prepare();
        G::LoadClass( 'tasks' );
        G::LoadClass( 'groups' );
        $oTasks = new Tasks();
        $oGroups = new Groups();
        $oUser = new Users();
        G::LoadClass( 'case' );
        $oCases = new Cases();
        $USR_UID = $_GET['USR_UID'];
        list ($oCriteriaToDo, $sXMLFile) = $oCases->getConditionCasesList( 'to_do', $_GET['USR_UID'] );
        list ($oCriteriaDraft, $sXMLFile) = $oCases->getConditionCasesList( 'draft', $_GET['USR_UID'] );

        if (ApplicationPeer::doCount( $oCriteriaToDo ) == 0 && ApplicationPeer::doCount( $oCriteriaDraft ) == 0)
            ;
        G::header( 'location: users_Delete?USR_UID=' . $USR_UID );

        $oDataset = ApplicationPeer::doSelectRS( $oCriteriaToDo );
        $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
        $oDataset->next();
        while ($aRow = $oDataset->getRow()) {
            $c ++;
            $oTemplatePower->newBlock( 'cases' );
            $aKeys = array_keys( $aRow );
            foreach ($aKeys as $sKey) {
                $oTemplatePower->assign( $sKey, $aRow[$sKey] );
            }
            $aUsers = array ($_GET['USR_UID']
            );
            $aAux1 = $oTasks->getGroupsOfTask( $aRow['TAS_UID'], 1 );
            foreach ($aAux1 as $aGroup) {
                $aAux2 = $oGroups->getUsersOfGroup( $aGroup['GRP_UID'] );
                foreach ($aAux2 as $aUser) {
                    if (! in_array( $aUser['USR_UID'], $aUsers )) {
                        $aUsers[] = $aUser['USR_UID'];
                        $aData = $oUser->load( $aUser['USR_UID'] );
                        $oTemplatePower->newBlock( 'users' );
                        $oTemplatePower->assign( 'USR_UID', $aUser['USR_UID'] );
                        $oTemplatePower->assign( 'USR_FULLNAME', $aData['USR_FIRSTNAME'] . ' ' . $aData['USR_LASTNAME'] . ' (' . $aData['USR_USERNAME'] . ')' );
                    }
                }
            }
            $aAux1 = $oTasks->getUsersOfTask( $aRow['TAS_UID'], 1 );
            foreach ($aAux1 as $aUser) {
                if (! in_array( $aUser['USR_UID'], $aUsers )) {
                    $aUsers[] = $aUser['USR_UID'];
                    $aData = $oUser->load( $aUser['USR_UID'] );
                    $oTemplatePower->newBlock( 'users' );
                    $oTemplatePower->assign( 'USR_UID', $aUser['USR_UID'] );
                    $oTemplatePower->assign( 'USR_FULLNAME', $aData['USR_FIRSTNAME'] . ' ' . $aData['USR_LASTNAME'] . ' (' . $aData['USR_USERNAME'] . ')' );
                }
            }
            $oTemplatePower->gotoBlock( 'cases' );
            $oTemplatePower->assign( 'ID_STATUS', G::LoadTranslation( 'ID_TO_DO' ) );
            $oTemplatePower->assign( 'ID_NO_REASSIGN', G::LoadTranslation( 'ID_NO_REASSIGN' ) );
            $oDataset->next();
        }
        $oDataset = ApplicationPeer::doSelectRS( $oCriteriaDraft );
        $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
        $oDataset->next();
        while ($aRow = $oDataset->getRow()) {
            $c ++;
            $oTemplatePower->newBlock( 'cases' );
            $aKeys = array_keys( $aRow );
            foreach ($aKeys as $sKey) {
                $oTemplatePower->assign( $sKey, $aRow[$sKey] );
            }
            $aUsers = array ($_GET['USR_UID']
            );
            $aAux1 = $oTasks->getGroupsOfTask( $aRow['TAS_UID'], 1 );
            foreach ($aAux1 as $aGroup) {
                $aAux2 = $oGroups->getUsersOfGroup( $aGroup['GRP_UID'] );
                foreach ($aAux2 as $aUser) {
                    if (! in_array( $aUser['USR_UID'], $aUsers )) {
                        $aUsers[] = $aUser['USR_UID'];
                        $aData = $oUser->load( $aUser['USR_UID'] );
                        $oTemplatePower->newBlock( 'users' );
                        $oTemplatePower->assign( 'USR_UID', $aUser['USR_UID'] );
                        $oTemplatePower->assign( 'USR_FULLNAME', $aData['USR_FIRSTNAME'] . ' ' . $aData['USR_LASTNAME'] . ' (' . $aData['USR_USERNAME'] . ')' );
                    }
                }
            }
            $aAux1 = $oTasks->getUsersOfTask( $aRow['TAS_UID'], 1 );
            foreach ($aAux1 as $aUser) {
                if (! in_array( $aUser['USR_UID'], $aUsers )) {
                    $aUsers[] = $aUser['USR_UID'];
                    $aData = $oUser->load( $aUser['USR_UID'] );
                    $oTemplatePower->newBlock( 'users' );
                    $oTemplatePower->assign( 'USR_UID', $aUser['USR_UID'] );
                    $oTemplatePower->assign( 'USR_FULLNAME', $aData['USR_FIRSTNAME'] . ' ' . $aData['USR_LASTNAME'] . ' (' . $aData['USR_USERNAME'] . ')' );
                }
            }
            $oTemplatePower->gotoBlock( 'cases' );
            $oTemplatePower->assign( 'ID_STATUS', G::LoadTranslation( 'ID_DRAFT' ) );
            $oTemplatePower->assign( 'ID_NO_REASSIGN', G::LoadTranslation( 'ID_NO_REASSIGN' ) );
            $oDataset->next();
        }
        $oTemplatePower->gotoBlock( '_ROOT' );
        $oTemplatePower->assign( 'ID_NUMBER', '#' );
        $oTemplatePower->assign( 'ID_CASE', G::LoadTranslation( 'ID_CASE' ) );
        $oTemplatePower->assign( 'ID_TASK', G::LoadTranslation( 'ID_TASK' ) );
        $oTemplatePower->assign( 'ID_PROCESS', G::LoadTranslation( 'ID_PROCESS' ) );
        $oTemplatePower->assign( 'ID_STATUS', G::LoadTranslation( 'ID_STATUS' ) );
        $oTemplatePower->assign( 'ID_REASSIGN_TO', G::LoadTranslation( 'ID_REASSIGN_TO' ) );
        $oTemplatePower->assign( 'ID_REASSIGN', G::LoadTranslation( 'ID_REASSIGN' ) );
        $oTemplatePower->assign( 'USR_UID', $_GET['USR_UID'] );
        $oTemplatePower->assign( 'CONT', $c );
        $G_PUBLISH->AddContent( 'template', '', '', '', $oTemplatePower );
    }
    G::RenderPage( 'publish' );
} catch (Exception $oException) {
    die( $oException->getMessage() );
}

