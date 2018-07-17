<?php
/**
 * upgrade.php
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
use ProcessMaker\Exception\RBACException;

global $RBAC;
switch ($RBAC->userCanAccess('PM_USERS')) {
    case -2:
        throw new RBACException('ID_USER_HAVENT_RIGHTS_SYSTEM', -2);
        break;
    case -1:
    case -3:
        throw new RBACException('ID_USER_HAVENT_RIGHTS_PAGE', -1);
        break;
}

$REQUEST = (isset( $_GET['request'] )) ? $_GET['request'] : $_POST['request'];

switch ($REQUEST) {
    case 'newRole':
        $G_PUBLISH = new Publisher();
        $G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'roles/roles_New', '', '' );
        G::RenderPage( 'publish', 'raw' );
        break;
    case 'saveNewRole':
        $newid = G::encryptOld( $_POST['code'] . date( "d-M-Y_H:i:s" ) );
        $aData['ROL_UID'] = $newid;
        //$aData['ROL_PARENT'] = $_POST['parent'];
        $aData['ROL_SYSTEM'] = '00000000000000000000000000000002';
        $aData['ROL_CODE'] = trim( $_POST['code'] );
        $aData['ROL_NAME'] = $_POST['name'];
        $aData['ROL_CREATE_DATE'] = date( "Y-M-d H:i:s" );
        $aData['ROL_UPDATE_DATE'] = date( "Y-M-d H:i:s" );
        $aData['ROL_STATUS'] = $_POST['status'];
        $oCriteria = $RBAC->createRole( $aData );
        echo '{success: true}';
        break;
    case 'editRole':
        $ROL_UID = $_GET['ROL_UID'];
        $aFields = $RBAC->loadById( $ROL_UID );

        $G_PUBLISH = new Publisher();
        $G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'roles/roles_Edit', '', $aFields );
        G::RenderPage( 'publish', 'raw' );
        break;
    case 'updateRole':
        $aData['ROL_UID'] = $_POST['rol_uid'];
        //$aData['ROL_PARENT'] = $_POST['parent'];
        $aData['ROL_CODE'] = trim( $_POST['code'] );
        $aData['ROL_NAME'] = $_POST['name'];
        $aData['ROL_UPDATE_DATE'] = date( "Y-M-d H:i:s" );
        $aData['ROL_STATUS'] = $_POST['status'];
        $oCriteria = $RBAC->updateRole( $aData );
        echo '{success: true}';
        break;
    case 'show':
        $aRoles = $RBAC->getAllRoles();

        $fields = Array ('ROL_UID' => 'char','ROL_PARENT' => 'char','ROL_SYSTEM' => 'char','ROL_CREATE_DATE' => 'char','ROL_UPDATE_DATE' => 'char','ROL_STATUS' => 'char'
        );

        $rows = array_merge( Array ($fields
        ), $aRoles );

        global $_DBArray;
        $_DBArray['virtual_roles'] = $rows;
        $oCriteria = new Criteria( 'dbarray' );
        $oCriteria->setDBArrayTable( 'virtual_roles' );

        $G_PUBLISH = new Publisher();
        $G_PUBLISH->AddContent( 'propeltable', 'paged-table', 'roles/roles_List', $oCriteria );
        G::RenderPage( 'publish', 'raw' );
        break;
    case 'deleteRole':
        $oCriteria = $RBAC->removeRole( $_POST['ROL_UID'] );
        break;
    case 'canDeleteRole':
        if ($RBAC->numUsersWithRole( $_POST['ROL_UID'] ) == 0) {
            echo 'true';
        } else {
            echo 'false';
        }
        break;
    case 'verifyNewRole':
        $response = ($RBAC->verifyNewRole( $_POST['code'] )) ? 'true' : 'false';
        print ($response) ;
        break;
    case 'updateDataRole':
        require_once 'classes/model/om/BaseRoles.php';
        require_once 'classes/model/Content.php';
        $oCriteria = new Criteria( 'rbac' );
        $oCriteria->addSelectColumn( RolesPeer::ROL_UID );
        $oCriteria->addSelectColumn( RolesPeer::ROL_PARENT );
        $oCriteria->addSelectColumn( RolesPeer::ROL_SYSTEM );
        $oCriteria->addSelectColumn( RolesPeer::ROL_CODE );
        $oCriteria->addSelectColumn( RolesPeer::ROL_CREATE_DATE );
        $oCriteria->addSelectColumn( RolesPeer::ROL_UPDATE_DATE );
        $oCriteria->addSelectColumn( RolesPeer::ROL_STATUS );
        $oCriteria->add( RolesPeer::ROL_CODE, $_GET['code'] );

        $result = RolesPeer::doSelectRS( $oCriteria );
        $result->setFetchmode( ResultSet::FETCHMODE_ASSOC );
        $result->next();
        $row = $result->getRow();

        $oCriteria1 = new Criteria( 'workflow' );
        $oCriteria1->add( ContentPeer::CON_CATEGORY, 'ROL_NAME' );
        $oCriteria1->add( ContentPeer::CON_ID, $row['ROL_UID'] );
        $oCriteria1->add( ContentPeer::CON_LANG, SYS_LANG );
        $oDataset1 = ContentPeer::doSelectRS( $oCriteria1 );
        $oDataset1->setFetchmode( ResultSet::FETCHMODE_ASSOC );
        $oDataset1->next();
        $aRow1 = $oDataset1->getRow();
        $row['ROL_NAME'] = $aRow1['CON_VALUE'];
        $row['ROL_UPDATE_DATE'] = date( "Y-M-d H:i:s" );

        $RBAC->updateRole( $row );
        //$response = ($RBAC->verifyNewRole($_GET['code']))?'true':'false';
        break;
    case 'usersIntoRole':
        $_GET['ROL_UID'] = (isset( $_GET['ROL_UID'] )) ? $_GET['ROL_UID'] : $_POST['ROL_UID'];
        $G_PUBLISH = new Publisher();
        $G_PUBLISH->AddContent( 'view', 'roles/roles_Tree' );
        G::RenderPage( 'publish', 'raw' );
        break;
    case 'deleteUserRole':
        $USR_UID = $_POST['USR_UID'];
        $ROL_UID = $_POST['ROL_UID'];
        $RBAC->deleteUserRole( $ROL_UID, $USR_UID );

        $_GET['ROL_UID'] = $ROL_UID;
        $G_PUBLISH = new Publisher();
        $G_PUBLISH->AddContent( 'view', 'roles/roles_Tree' );
        G::RenderPage( 'publish', 'raw' );
        break;
    case 'showUsers':
        $ROL_UID = $_POST['ROL_UID'];
        $_GET['ROL_UID'] = $ROL_UID;
        $G_PUBLISH = new Publisher();
        $G_PUBLISH->AddContent( 'view', 'roles/roles_AssignRole' );
        G::RenderPage( 'publish', 'raw' );
        break;
    case 'showPermissions':
        $ROL_UID = $_POST['ROL_UID'];
        $_GET['ROL_UID'] = $ROL_UID;
        $G_PUBLISH = new Publisher();
        $G_PUBLISH->AddContent( 'view', 'roles/roles_AssignPermissions' );
        G::RenderPage( 'publish', 'raw' );
        break;
    case 'assignUserToRole':
        $ROL_UID = $_POST['ROL_UID'];
        $aUserIuds = explode( ",", $_POST['aUsers'] );
        foreach ($aUserIuds as $key => $val) {
            $sData['USR_UID'] = $val;
            $sData['ROL_UID'] = $ROL_UID;
            if ($sData['USR_UID'] == '00000000000000000000000000000001') {
                if ($sData['ROL_UID'] != 'PROCESSMAKER_ADMIN') {
                    $response = new stdclass();
					$response->userRole = true;
					echo G::json_encode($response);
                    break;
                }
            }
            $RBAC->assignUserToRole( $sData );
        }

        //    	$_GET['ROL_UID'] = $ROL_UID;
        //      $G_PUBLISH = new Publisher;
        //      $G_PUBLISH->AddContent('view', 'roles/roles_Tree' );
        //      G::RenderPage('publish', 'raw');
        break;
    case 'assignPermissionToRole':
        $USR_UID = $_POST['PER_UID'];
        $ROL_UID = $_POST['ROL_UID'];
        $sData['PER_UID'] = $USR_UID;
        $sData['ROL_UID'] = $ROL_UID;
        $RBAC->assignPermissionRole( $sData );

        //    	$_GET['ROL_UID'] = $ROL_UID;
        //		$G_PUBLISH = new Publisher;
        //		$G_PUBLISH->AddContent('view', 'roles/roles_permissionsTree' );
        //		G::RenderPage('publish', 'raw');
        break;
    case 'viewPermitions':
        $_GET['ROL_UID'] = (isset( $_GET['ROL_UID'] )) ? $_GET['ROL_UID'] : $_POST['ROL_UID'];
        $G_PUBLISH = new Publisher();
        $G_PUBLISH->AddContent( 'view', 'roles/roles_permissionsTree' );
        G::RenderPage( 'publish', 'raw' );
        break;
    case 'deletePermissionRole':
        $PER_UID = $_POST['PER_UID'];
        $ROL_UID = $_POST['ROL_UID'];
        $RBAC->deletePermissionRole( $ROL_UID, $PER_UID );

        $_GET['ROL_UID'] = $ROL_UID;
        $G_PUBLISH = new Publisher();
        $G_PUBLISH->AddContent( 'view', 'roles/roles_permissionsTree' );
        G::RenderPage( 'publish', 'raw' );
        break;
    case 'assignPermissionToRoleMultiple':
        $USR_UID = $_POST['PER_UID'];
        $ROL_UID = $_POST['ROL_UID'];
        $arrPer = explode( ',', $USR_UID );
        foreach ($arrPer as $PER_UID) {
            unset( $sData );
            $sData['PER_UID'] = $PER_UID;
            $sData['ROL_UID'] = $ROL_UID;
            $RBAC->assignPermissionRole( $sData );
        }
        break;
    case 'deletePermissionToRoleMultiple':
        $USR_UID = $_POST['PER_UID'];
        $ROL_UID = $_POST['ROL_UID'];
        $arrPer = explode( ',', $USR_UID );
        foreach ($arrPer as $PER_UID) {
            $RBAC->deletePermissionRole( $ROL_UID, $PER_UID );
        }
        break;
    case 'deleteUserRoleMultiple':
        $USR_UID = $_POST['USR_UID'];
        $ROL_UID = $_POST['ROL_UID'];
        $arrUsers = explode( ',', $USR_UID );
        foreach ($arrUsers as $aUID) {
            $RBAC->deleteUserRole( $ROL_UID, $aUID );
            if ($aUID == '00000000000000000000000000000001') {
                $sData['USR_UID'] = $aUID;
                $sData['ROL_UID'] = '00000000000000000000000000000002';
                $RBAC->assignUserToRole( $sData );
            }
        }
        break;
    case 'rolesList':
        $co = new Configurations();
        $config = $co->getConfiguration( 'rolesList', 'pageSize', '', $_SESSION['USER_LOGGED'] );
        $limit_size = isset( $config['pageSize'] ) ? $config['pageSize'] : 20;

        $start = isset( $_POST['start'] ) ? $_POST['start'] : 0;
        $limit = isset( $_POST['limit'] ) ? $_POST['limit'] : $limit_size;
        $filter = isset( $_REQUEST['textFilter'] ) ? $_REQUEST['textFilter'] : '';

        global $RBAC;
        $Criterias = $RBAC->getAllRolesFilter( $start, $limit, $filter );

        $rs = RolesPeer::DoSelectRs( $Criterias['LIST'] );
        $rs->setFetchmode( ResultSet::FETCHMODE_ASSOC );

        $content = new Content();
        $rNames = $content->getAllContentsByRole();
        $aUsers = $RBAC->getAllUsersByRole();
        $aRows = Array ();
        while ($rs->next()) {
            $aRows[] = $rs->getRow();
            $index = sizeof( $aRows ) - 1;
            $roleUid = $aRows[$index]['ROL_UID'];
            if (!isset($rNames[$roleUid])) {
                $rol = new Roles();
                $row = $rol->load($roleUid);
                $rolname = $row['ROL_NAME'];
            } else {
                $rolname = $rNames[$roleUid];
            }
            $aRows[$index]['ROL_NAME'] = $rolname;
            $aRows[$index]['TOTAL_USERS'] = isset( $aUsers[$roleUid] ) ? $aUsers[$roleUid] : 0;
        }

        $oData = RolesPeer::doSelectRS( $Criterias['COUNTER'] );
        $oData->setFetchmode( ResultSet::FETCHMODE_ASSOC );
        $oData->next();
        $row = $oData->getRow();
        $total_roles = $row['CNT'];

        echo '{roles: ' . G::json_encode( $aRows ) . ', total_roles: ' . $total_roles . '}';
        break;
    case 'updatePageSize':
        $c = new Configurations();
        $arr['pageSize'] = $_REQUEST['size'];
        $arr['dateSave'] = date( 'Y-m-d H:i:s' );
        $config = Array ();
        $config[] = $arr;
        $c->aConfig = $config;
        $c->saveConfig( 'rolesList', 'pageSize', '', $_SESSION['USER_LOGGED'] );
        echo '{success: true}';
        break;
    case 'checkRoleCode':
        $rol_code = $_POST['ROL_CODE'];
        $rol_uid = isset( $_POST['ROL_UID'] ) ? $_POST['ROL_UID'] : '';
        $oCriteria = new Criteria( 'rbac' );
        $oCriteria->addSelectColumn( RolesPeer::ROL_UID );
        $oCriteria->add( RolesPeer::ROL_CODE, $rol_code );
        if ($rol_uid != '') {
            $oCriteria->add( RolesPeer::ROL_UID, $rol_uid, Criteria::NOT_EQUAL );
        }
        $oDataset = RolesPeer::doSelectRS( $oCriteria );
        $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
        if ($oDataset->next()) {
            $response = 'false';
        } else {
            $response = 'true';
        }
        echo '{success:' . $response . '}';
        break;
    case 'updatePermissionContent':
        /*
        $per_code = $_POST['PER_NAME'];
        $per_uid = isset( $_POST['PER_UID'] ) ? $_POST['PER_UID'] : '';
        require_once 'classes/model/Content.php';
        $oCriteria = new Criteria( 'workflow' );
        $oCriteria->add( ContentPeer::CON_CATEGORY, 'PER_NAME' );
        $oCriteria->add( ContentPeer::CON_ID, $per_uid );
        $oCriteria->add( ContentPeer::CON_VALUE, $per_code );
        $oDataset = ContentPeer::doSelectRS( $oCriteria );
        */
        break;
    default:
        echo 'default';
}

