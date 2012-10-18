<?php

/**
 * departments_Ajax.php
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

function LookForChildren ($parent, $level, $aDepUsers)
{
    G::LoadClass( 'configuration' );
    $conf = new Configurations();
    $oDept = new Department();
    $allDepartments = $oDept->getDepartments( $parent );
    $level ++;
    $rows = Array ();
    foreach ($allDepartments as $department) {
        unset( $depto );
        $depto['DEP_TITLE'] = str_replace( array ("<",">" ), array ("&lt;","&gt;" ), $department['DEP_TITLE'] );
        $depto['DEP_STATUS'] = $department['DEP_STATUS'];
        if ($department['DEP_MANAGER_USERNAME'] != '') {
            $depto['DEP_MANAGER_NAME'] = $conf->usersNameFormat( $department['DEP_MANAGER_USERNAME'], $department['DEP_MANAGER_FIRSTNAME'], $department['DEP_MANAGER_LASTNAME'] );
        } else {
            $depto['DEP_MANAGER_NAME'] = '';
        }
        $depto['DEP_TOTAL_USERS'] = isset( $aDepUsers[$department['DEP_UID']] ) ? $aDepUsers[$department['DEP_UID']] : 0;
        $depto['DEP_UID'] = $department['DEP_UID'];
        $depto['DEP_MANAGER'] = $department['DEP_MANAGER'];
        $depto['DEP_PARENT'] = $department['DEP_PARENT'];
        if ($department['HAS_CHILDREN'] > 0) {
            $depto['children'] = LookForChildren( $department['DEP_UID'], $level, $aDepUsers );
            $depto['iconCls'] = 'ss_sprite ss_chart_organisation';
            $depto['expanded'] = true;
        } else {
            $depto['leaf'] = true;
            if ($level == 1) {
                $depto['iconCls'] = 'ss_sprite ss_chart_organisation';
            } else {
                $depto['iconCls'] = 'ss_sprite ss_plugin';
            }
        }
        $rows[] = $depto;
    }
    return $rows;
}

if (($RBAC_Response = $RBAC->userCanAccess( "PM_USERS" )) != 1) {
    return $RBAC_Response;
}
G::LoadInclude( 'ajax' );
$_POST['action'] = get_ajax_value( 'action' );

require_once 'classes/model/Department.php';

switch ($_POST['action']) {
    case 'showUsers':
        global $G_PUBLISH;
        $oDept = new Department();
        $aFields = $oDept->load( $_POST['sDptoUID'] );
        $G_PUBLISH = new Publisher();
        $G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'departments/departments_Edit', '', $aFields, '' );

        $criteria = $oDept->getUsersFromDepartment( $_POST['sDptoUID'], $aFields['DEP_MANAGER'] );

        $G_PUBLISH->AddContent( 'propeltable', 'departments/paged-table2', 'departments/departments_UsersList', $criteria, $aFields );
        //$G_PUBLISH->AddContent('propeltable', 'paged-table', 'departments/departments_UsersList', $criteria, $aFields);

        $oHeadPublisher = & headPublisher::getSingleton();
        $oHeadPublisher->addScriptCode( "groupname='{$aFields["DEPO_TITLE"]}';" );
        $oHeadPublisher->addScriptCode( "depUid='{$aFields["DEP_UID"]}';" );

        G::RenderPage( 'publish', 'raw' );
        break;
    case 'assignAllUsers':
        $aUsers = explode( ',', $_POST['aUsers'] );
        $oDept = new Department();
        $depUid = $_POST['DEP_UID'];
        $cant = $oDept->cantUsersInDepartment( $depUid );

        if ($cant == 0) {
            $manager = true;
        }

        for ($i = 0; $i < count( $aUsers ); $i ++) {
            $oDept->addUserToDepartment( $depUid, $aUsers[$i], $manager, false );
            $manager = false;
        }
        $oDept->updateDepartmentManager( $depUid );
        break;
    case 'removeUserFromDepartment':
        $oDept = new Department();
        $oDept->removeUserFromDepartment( $_POST['DEP_UID'], $_POST['USR_UID'] );
        break;
    case 'verifyDptoname':
        $_POST['sOriginalGroupname'] = get_ajax_value( 'sOriginalGroupname' );
        $_POST['sGroupname'] = get_ajax_value( 'sGroupname' );
        if ($_POST['sOriginalGroupname'] == $_POST['sGroupname']) {
            echo '0';
        } else {
            $oDpto = new Department();
            $oCriteria = $oDpto->loadByGroupname( $_POST['sGroupname'] );
            $oDataset = DepartmentPeer::doSelectRS( $oCriteria );
            $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
            $oDataset->next();
            $aRow = $oDataset->getRow();
            if (! $aRow) {
                echo '0';
            } else {
                echo '1';
            }
        }
        break;
    case 'showUnAssignedUsers':
        $_POST['UID'] = get_ajax_value( 'UID' );
        require_once ('classes/class.xmlfield_InputPM.php');

        if (($RBAC_Response = $RBAC->userCanAccess( "PM_USERS" )) != 1) {
            return $RBAC_Response;
        }
        G::LoadClass( 'departments' );
        $oDept = new Department();

        $G_PUBLISH = new Publisher();
        $G_PUBLISH->AddContent( 'propeltable', 'departments/paged-table3', 'departments/departments_AddUnAssignedUsers', $oDept->getAvailableUsersCriteria( '' ) );
        G::RenderPage( 'publish', 'raw' );
        break;
    case 'departmentList':
        global $RBAC;
        $aDEPTS = $RBAC->getAllUsersByDepartment();
        $tree_depart = LookForChildren( '', 0, $aDEPTS );
        echo G::json_encode( $tree_depart );
        break;
    case 'checkDepartmentName':
        $parent = $_REQUEST['parent'];
        $dep_name = $_REQUEST['name'];

        $oCriteria = new Criteria( 'workflow' );

        $oCriteria->clearSelectColumns();
        $oCriteria->addSelectColumn( ContentPeer::CON_CATEGORY );
        $oCriteria->addSelectColumn( ContentPeer::CON_VALUE );
        $oCriteria->addSelectColumn( DepartmentPeer::DEP_PARENT );
        $oCriteria->add( ContentPeer::CON_CATEGORY, 'DEPO_TITLE' );
        $oCriteria->addJoin( ContentPeer::CON_ID, DepartmentPeer::DEP_UID, Criteria::LEFT_JOIN );
        $oCriteria->add( ContentPeer::CON_VALUE, $dep_name );
        $oCriteria->add( ContentPeer::CON_LANG, SYS_LANG );
        $oCriteria->add( DepartmentPeer::DEP_PARENT, $parent );

        $oDataset = DepartmentPeer::doSelectRS( $oCriteria );
        $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
        $oDataset->next();
        $aRow = $oDataset->getRow();
        echo (! $aRow) ? 'true' : 'false';
        break;
    case 'checkEditDepartmentName':
        $parent = $_REQUEST['parent'];
        $dep_name = $_REQUEST['name'];
        $dep_uid = $_REQUEST['uid'];
        $oDepartment = new Department();
        $checkVal = $oDepartment->checkDepartmentName( $dep_name, $parent, $dep_uid );
        echo (! $checkVal) ? 'true' : 'false';
        break;
    case 'saveDepartment':
        $parent = $_REQUEST['parent'];
        $dep_name = $_REQUEST['name'];
        $newDepartment['DEP_PARENT'] = $parent;
        $newDepartment['DEP_TITLE'] = $dep_name;
        $oDept = new Department();
        $oDept->create( $newDepartment );
        echo '{success: true}';
        break;
    case 'usersByDepartment':
        G::LoadClass( 'configuration' );
        $sDepUid = $_REQUEST['DEP_UID'];
        $oCriteria = new Criteria( 'workflow' );
        $oCriteria->addSelectColumn( UsersPeer::USR_UID );
        $oCriteria->addSelectColumn( UsersPeer::USR_USERNAME );
        $oCriteria->addSelectColumn( UsersPeer::USR_FIRSTNAME );
        $oCriteria->addSelectColumn( UsersPeer::USR_LASTNAME );
        $oCriteria->addSelectColumn( UsersPeer::USR_REPORTS_TO );
        $oCriteria->add( UsersPeer::USR_STATUS, 'CLOSED', Criteria::NOT_EQUAL );
        $oCriteria->add( UsersPeer::DEP_UID, $sDepUid );

        $oDataset = DepartmentPeer::doSelectRS( $oCriteria );
        $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );

        $rows = Array ();
        unset( $first );
        $first['USR_UID'] = '';
        $first['USR_VALUE'] = G::LoadTranslation( 'ID_NO_MANAGER_SELECTED' );
        $rows[] = $first;

        $conf = new Configurations();

        while ($oDataset->next()) {
            $aRow = $oDataset->getRow();
            $user['USR_UID'] = $aRow['USR_UID'];
            $user['USR_VALUE'] = $conf->usersNameFormat( $aRow['USR_USERNAME'], $aRow['USR_FIRSTNAME'], $aRow['USR_LASTNAME'] );
            $rows[] = $user;
        }

        echo '{users: ' . G::json_encode( $rows ) . '}';
        break;
    case 'updateDepartment':
        try {
            $dep_name = $_REQUEST['name'];
            $dep_uid = $_REQUEST['uid'];
            $dep_manager = $_REQUEST['manager'];
            $dep_status = $_REQUEST['status'];
            $editDepartment['DEP_UID'] = $dep_uid;
            $editDepartment['DEPO_TITLE'] = $dep_name;
            $editDepartment['DEP_STATUS'] = $dep_status;
            $editDepartment['DEP_MANAGER'] = $dep_manager;
            $oDept = new Department();
            $oDept->update( $editDepartment );
            $oDept->updateDepartmentManager( $dep_uid );
            echo '{success: true}';
        } catch (exception $e) {
            echo '{success: false}';
        }
        break;
    case 'canDeleteDepartment':
        global $RBAC;
        $aDEPTS = $RBAC->getAllUsersByDepartment();
        if (isset( $aDEPTS[$_POST['dep_uid']] )) {
            echo '{success: false, users: ' . $aDEPTS[$_POST['dep_uid']] . '}';
        } else {
            echo '{success: true}';
        }
        break;
    case 'deleteDepartment':
        $DEP_UID = $_POST['DEP_UID'];
        $oDept = new Department();
        $oDept->remove( $DEP_UID );
        echo '{success: true}';
        break;
    case 'assignedUsers':
        $filter = isset( $_POST['textFilter'] ) ? $_POST['textFilter'] : '';
        $dep_uid = $_REQUEST['dUID'];
        $oDept = new Department();
        $oDept->Load( $dep_uid );
        $manager = $oDept->getDepManager();
        $oCriteria = new Criteria( 'workflow' );
        $oCriteria->addSelectColumn( UsersPeer::USR_UID );
        $oCriteria->addSelectColumn( UsersPeer::USR_USERNAME );
        $oCriteria->addSelectColumn( UsersPeer::USR_FIRSTNAME );
        $oCriteria->addSelectColumn( UsersPeer::USR_LASTNAME );
        $oCriteria->addSelectColumn( UsersPeer::USR_STATUS );
        $oCriteria->add( UsersPeer::DEP_UID, '' );
        $oCriteria->add( UsersPeer::USR_STATUS, 'CLOSED', Criteria::NOT_EQUAL );
        if ($filter != '') {
            $oCriteria->add( $oCriteria->getNewCriterion( UsersPeer::USR_USERNAME, '%' . $filter . '%', Criteria::LIKE )->addOr( $oCriteria->getNewCriterion( UsersPeer::USR_FIRSTNAME, '%' . $filter . '%', Criteria::LIKE )->addOr( $oCriteria->getNewCriterion( UsersPeer::USR_LASTNAME, '%' . $filter . '%', Criteria::LIKE ) ) ) );
        }
        $oCriteria->add( UsersPeer::DEP_UID, $dep_uid );
        $oDataset = UsersPeer::doSelectRS( $oCriteria );
        $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
        $aUsers = array ();
        while ($oDataset->next()) {
            $aUsers[] = $oDataset->getRow();
            $index = sizeof( $aUsers ) - 1;
            $aUsers[$index]['USR_SUPERVISOR'] = ($manager == $aUsers[$index]['USR_UID']) ? true : false;
        }
        echo '{users:' . G::json_encode( $aUsers ) . '}';
        break;
    case 'availableUsers':
        $filter = isset( $_POST['textFilter'] ) ? $_POST['textFilter'] : '';
        $dep_uid = $_REQUEST['dUID'];
        $oCriteria = new Criteria( 'workflow' );
        $oCriteria->addSelectColumn( UsersPeer::USR_UID );
        $oCriteria->addSelectColumn( UsersPeer::USR_USERNAME );
        $oCriteria->addSelectColumn( UsersPeer::USR_FIRSTNAME );
        $oCriteria->addSelectColumn( UsersPeer::USR_LASTNAME );
        $oCriteria->addSelectColumn( UsersPeer::USR_STATUS );
        $oCriteria->add( UsersPeer::DEP_UID, '' );
        $oCriteria->add( UsersPeer::USR_STATUS, 'CLOSED', Criteria::NOT_EQUAL );

        if ($filter != '') {
            $oCriteria->add( $oCriteria->getNewCriterion( UsersPeer::USR_USERNAME, '%' . $filter . '%', Criteria::LIKE )->addOr( $oCriteria->getNewCriterion( UsersPeer::USR_FIRSTNAME, '%' . $filter . '%', Criteria::LIKE )->addOr( $oCriteria->getNewCriterion( UsersPeer::USR_LASTNAME, '%' . $filter . '%', Criteria::LIKE ) ) ) );
        }
        $oDataset = UsersPeer::doSelectRS( $oCriteria );
        $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
        $aUsers = array ();
        while ($oDataset->next()) {
            $aUsers[] = $oDataset->getRow();
        }
        echo '{users:' . G::json_encode( $aUsers ) . '}';
        break;
    case 'assignDepartmentToUserMultiple':
        $DEP_UID = $_REQUEST['DEP_UID'];
        $uSERS = $_REQUEST['USR_UID'];
        $aUsers = explode( ',', $uSERS );
        $dep = new Department();
        $dep->Load( $DEP_UID );
        $dep_manager = $dep->getDepManager();
        $manager = ($dep_manager == '') ? true : false;
        foreach ($aUsers as $USR_UID) {
            $dep->addUserToDepartment( $DEP_UID, $USR_UID, $manager, false );
            $manager = false;
        }
        $dep->updateDepartmentManager( $DEP_UID );
        break;
    case 'deleteDepartmentToUserMultiple':
        $DEP_UID = $_REQUEST['DEP_UID'];
        $uSERS = $_REQUEST['USR_UID'];
        $aUsers = explode( ',', $uSERS );
        $dep = new Department();
        $dep->Load( $DEP_UID );
        $manager = $dep->getDepManager();
        foreach ($aUsers as $USR_UID) {
            $dep->removeUserFromDepartment( $DEP_UID, $USR_UID );
            if ($USR_UID == $manager) {
                $editDepto['DEP_UID'] = $DEP_UID;
                $editDepto['DEP_MANAGER'] = '';
                $dep->update( $editDepto );
                $dep->updateDepartmentManager( $DEP_UID );
            }
        }
        break;
    case 'updateSupervisor':
        $dep_manager = $_POST['USR_UID'];
        $dep_uid = $_POST['DEP_UID'];
        $editDepartment['DEP_UID'] = $dep_uid;
        $editDepartment['DEP_MANAGER'] = $dep_manager;
        $oDept = new Department();
        $oDept->update( $editDepartment );
        $oDept->updateDepartmentManager( $dep_uid );
        echo '{success: true}';
        break;
}

