<?php
/**
 * groups_Ajax.php
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
if (($RBAC_Response = $RBAC->userCanAccess( "PM_USERS" )) != 1) {
    return $RBAC_Response;
}
G::LoadInclude( 'ajax' );
$_POST['action'] = get_ajax_value( 'action' );

switch ($_POST['action']) {
    case 'showUsers':
        G::LoadClass( 'groups' );
        $oGroups = new Groups();
        $oGroup = new Groupwf();
        $aFields = $oGroup->load( $_POST['sGroupUID'] );
        global $G_PUBLISH;
        $G_PUBLISH = new Publisher();
        //$G_PUBLISH->AddContent('xmlform', 'xmlform', 'groups/groups_UsersListTitle', '', array('GRP_NAME' => $aFields['GRP_TITLE']));
        $G_PUBLISH->AddContent( 'propeltable', 'groups/paged-table2', 'groups/groups_UsersList', $oGroups->getUsersGroupCriteria( $_POST['sGroupUID'] ), array ('GRP_UID' => $_POST['sGroupUID'],'GRP_NAME' => $aFields['GRP_TITLE']) );
        $oHeadPublisher = & headPublisher::getSingleton();
        $oHeadPublisher->addScriptCode( "groupname=\"{$aFields["GRP_TITLE"]}\";" );
        G::RenderPage( 'publish', 'raw' );
        break;
    case 'assignUser':
        G::LoadClass( 'groups' );
        $oGroup = new Groups();
        $oGroup->addUserToGroup( $_POST['GRP_UID'], $_POST['USR_UID'] );
        break;
    case 'assignAllUsers':
        G::LoadClass( 'groups' );
        $oGroup = new Groups();
        $aUsers = explode( ',', $_POST['aUsers'] );
        for ($i = 0; $i < count( $aUsers ); $i ++) {
            $oGroup->addUserToGroup( $_POST['GRP_UID'], $aUsers[$i] );
        }
        break;
    case 'ofToAssignUser':
        G::LoadClass( 'groups' );
        $oGroup = new Groups();
        $oGroup->removeUserOfGroup( $_POST['GRP_UID'], $_POST['USR_UID'] );
        break;
    case 'verifyGroupname':
        $_POST['sOriginalGroupname'] = get_ajax_value( 'sOriginalGroupname' );
        $_POST['sGroupname'] = get_ajax_value( 'sGroupname' );
        if ($_POST['sOriginalGroupname'] == $_POST['sGroupname']) {
            echo '0';
        } else {
            require_once 'classes/model/Groupwf.php';
            G::LoadClass( 'Groupswf' );
            $oGroup = new Groupwf();
            $oCriteria = $oGroup->loadByGroupname( $_POST['sGroupname'] );
            $oDataset = GroupwfPeer::doSelectRS( $oCriteria );
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
    case 'groupsList':
        require_once 'classes/model/Groupwf.php';
        require_once 'classes/model/TaskUser.php';
        require_once 'classes/model/GroupUser.php';
        G::LoadClass( 'configuration' );
        $co = new Configurations();
        $config = $co->getConfiguration( 'groupList', 'pageSize', '', $_SESSION['USER_LOGGED'] );
        $env = $co->getConfiguration( 'ENVIRONMENT_SETTINGS', '' );
        $limit_size = isset( $config['pageSize'] ) ? $config['pageSize'] : 20;
        $start = isset( $_REQUEST['start'] ) ? $_REQUEST['start'] : 0;
        $limit = isset( $_REQUEST['limit'] ) ? $_REQUEST['limit'] : $limit_size;
        $filter = isset( $_REQUEST['textFilter'] ) ? $_REQUEST['textFilter'] : '';

        global $RBAC;
        if ($limit == $start) {
            $limit = $limit + $limit;
        }
        $tasks = new TaskUser();
        $aTask = $tasks->getCountAllTaksByGroups();

        $members = new GroupUser();
        $aMembers = $members->getCountAllUsersByGroup();

        require_once PATH_CONTROLLERS . 'adminProxy.php';
        $uxList = adminProxy::getUxTypesList();

        $groups = new Groupwf();
        $data = $groups->getAllGroup( $start, $limit, $filter );
        $result = $data['rows'];

        $totalRows = 0;
        $arrData = array ();
        foreach ($result as $results) {
            $totalRows ++;
            $results['CON_VALUE'] = str_replace( array ("<",">"
            ), array ("&lt;","&gt;"
            ), $results['GRP_TITLE'] );
            $results['GRP_TASKS'] = isset( $aTask[$results['GRP_UID']] ) ? $aTask[$results['GRP_UID']] : 0;
            $results['GRP_USERS'] = isset( $aMembers[$results['GRP_UID']] ) ? $aMembers[$results['GRP_UID']] : 0;
            $arrData[] = $results;
        }

        $result = new StdClass();
        $result->success = true;
        $result->groups = $arrData;
        $result->total_groups = $data['totalCount'];

        echo G::json_encode( $result );
        break;
    case 'exitsGroupName':
        require_once 'classes/model/Groupwf.php';
        G::LoadClass( 'Groupswf' );
        $oGroup = new Groupwf();
        $oCriteria = $oGroup->loadByGroupname( $_POST['GRP_NAME'] );
        $oDataset = GroupwfPeer::doSelectRS( $oCriteria );
        $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
        $oDataset->next();
        $aRow = $oDataset->getRow();
        $response = ($aRow) ? 'true' : 'false';
        echo $response;
        break;
    case 'saveNewGroup':
        G::LoadClass( 'groups' );
        $newGroup['GRP_UID'] = '';
        $newGroup['GRP_STATUS'] = G::toUpper( $_POST['status'] );
        $newGroup['GRP_TITLE'] = trim( $_POST['name'] );
        unset( $newGroup['GRP_UID'] );
        $group = new Groupwf();
        $group->create( $newGroup );
        echo '{success: true}';
        break;
    case 'saveEditGroup':
        G::LoadClass( 'groups' );
        $editGroup['GRP_UID'] = $_POST['grp_uid'];
        $editGroup['GRP_STATUS'] = G::toUpper( $_POST['status'] );
        $editGroup['GRP_TITLE'] = trim( $_POST['name'] );
        $group = new Groupwf();
        $group->update( $editGroup );
        echo '{success: true}';
        break;
    case 'deleteGroup':
        G::LoadClass( 'groups' );
        $group = new Groupwf();
        if (! isset( $_POST['GRP_UID'] )) {
            return;
        }
        $group->remove( urldecode( $_POST['GRP_UID'] ) );
        require_once 'classes/model/TaskUser.php';
        $oProcess = new TaskUser();
        $oCriteria = new Criteria( 'workflow' );
        $oCriteria->add( TaskUserPeer::USR_UID, $_POST['GRP_UID'] );
        TaskUserPeer::doDelete( $oCriteria );

        //Delete permissions
        require_once 'classes/model/ObjectPermission.php';
        $criteria = new Criteria( 'workflow' );
        $criteria->add(ObjectPermissionPeer::USR_UID, $_POST['GRP_UID']);
        $dataset = ObjectPermissionPeer::doSelectRS($criteria);
        $dataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $dataset->next();
        $oOP = new ObjectPermission();
        while ($row = $dataset->getRow()) {
            $oOP = ObjectPermissionPeer::retrieveByPK( $row['OP_UID'] );
            $oOP->delete();
            $dataset->next();
        }
        echo '{success: true}';
        break;
    case 'assignedMembers':
        require_once 'classes/model/Users.php';
        require_once 'classes/model/GroupUser.php';

        G::LoadClass( 'configuration' );
        $co = new Configurations();
        $config = $co->getConfiguration( 'groupList', 'pageSize', '', $_SESSION['USER_LOGGED'] );
        $env = $co->getConfiguration( 'ENVIRONMENT_SETTINGS', '' );
        $limit_size = isset( $config['pageSize'] ) ? $config['pageSize'] : 20;
        $start = isset( $_REQUEST['start'] ) ? $_REQUEST['start'] : 0;
        $limit = isset( $_REQUEST['limit'] ) ? $_REQUEST['limit'] : $limit_size;
        $filter = isset( $_REQUEST['textFilter'] ) ? $_REQUEST['textFilter'] : '';

        $sGroupUID = $_REQUEST['gUID'];

        $aUsers = Array ();
        $oCriteria = new Criteria( 'workflow' );
        $oCriteria->addSelectColumn( 'COUNT(*) AS CNT' );
        $oCriteria->addJoin( GroupUserPeer::USR_UID, UsersPeer::USR_UID, Criteria::LEFT_JOIN );
        $oCriteria->add( GroupUserPeer::GRP_UID, $sGroupUID );
        $oCriteria->add( UsersPeer::USR_STATUS, 'CLOSED', Criteria::NOT_EQUAL );
        $filter = (isset( $_POST['textFilter'] )) ? $_POST['textFilter'] : '';
        if ($filter != '') {
            $oCriteria->add( $oCriteria->getNewCriterion( UsersPeer::USR_USERNAME, '%' . $filter . '%', Criteria::LIKE )->addOr( $oCriteria->getNewCriterion( UsersPeer::USR_FIRSTNAME, '%' . $filter . '%', Criteria::LIKE )->addOr( $oCriteria->getNewCriterion( UsersPeer::USR_LASTNAME, '%' . $filter . '%', Criteria::LIKE ) ) ) );
        }
        $oDataset = UsersPeer::DoSelectRs( $oCriteria );
        $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
        $oDataset->next();
        $row = $oDataset->getRow();
        $totalRows = $row['CNT'];

        $oCriteria = new Criteria( 'workflow' );
        $oCriteria->addSelectColumn( GroupUserPeer::GRP_UID );
        $oCriteria->addSelectColumn( UsersPeer::USR_UID );
        $oCriteria->addSelectColumn( UsersPeer::USR_USERNAME );
        $oCriteria->addSelectColumn( UsersPeer::USR_FIRSTNAME );
        $oCriteria->addSelectColumn( UsersPeer::USR_LASTNAME );
        $oCriteria->addSelectColumn( UsersPeer::USR_EMAIL );
        $oCriteria->addSelectColumn( UsersPeer::USR_STATUS );
        $oCriteria->addJoin( GroupUserPeer::USR_UID, UsersPeer::USR_UID, Criteria::LEFT_JOIN );
        $oCriteria->add( GroupUserPeer::GRP_UID, $sGroupUID );
        $oCriteria->add( UsersPeer::USR_STATUS, 'CLOSED', Criteria::NOT_EQUAL );
        $filter = (isset( $_POST['textFilter'] )) ? $_POST['textFilter'] : '';
        if ($filter != '') {
            $oCriteria->add( $oCriteria->getNewCriterion( UsersPeer::USR_USERNAME, '%' . $filter . '%', Criteria::LIKE )->addOr( $oCriteria->getNewCriterion( UsersPeer::USR_FIRSTNAME, '%' . $filter . '%', Criteria::LIKE )->addOr( $oCriteria->getNewCriterion( UsersPeer::USR_LASTNAME, '%' . $filter . '%', Criteria::LIKE ) ) ) );
        }
        $oCriteria->setOffset( $start );
        $oCriteria->setLimit( $limit );

        $oDataset = UsersPeer::doSelectRS( $oCriteria );
        $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
        $arrData = Array ();
        while ($oDataset->next()) {
            $arrData[] = $oDataset->getRow();
        }
        echo '{success: true, members: ' . G::json_encode( $arrData ) . ', total_users: ' . $totalRows . '}';
        break;
    case 'availableMembers':
        require_once 'classes/model/Users.php';
        require_once 'classes/model/GroupUser.php';

        G::LoadClass( 'configuration' );
        $co = new Configurations();
        $config = $co->getConfiguration( 'groupList', 'pageSize', '', $_SESSION['USER_LOGGED'] );
        $env = $co->getConfiguration( 'ENVIRONMENT_SETTINGS', '' );
        $limit_size = isset( $config['pageSize'] ) ? $config['pageSize'] : 20;
        $start = isset( $_REQUEST['start'] ) ? $_REQUEST['start'] : 0;
        $limit = isset( $_REQUEST['limit'] ) ? $_REQUEST['limit'] : $limit_size;
        $filter = isset( $_REQUEST['textFilter'] ) ? $_REQUEST['textFilter'] : '';

        $sGroupUID = $_REQUEST['gUID'];
        $oCriteria = new Criteria( 'workflow' );
        $oCriteria->addSelectColumn( GroupUserPeer::GRP_UID );
        $oCriteria->addSelectColumn( UsersPeer::USR_UID );
        $oCriteria->addJoin( GroupUserPeer::USR_UID, UsersPeer::USR_UID, Criteria::LEFT_JOIN );
        $oCriteria->add( GroupUserPeer::GRP_UID, $sGroupUID );
        $oCriteria->add( UsersPeer::USR_STATUS, 'CLOSED', Criteria::NOT_EQUAL );
        $oDataset = UsersPeer::doSelectRS( $oCriteria );
        $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
        $oDataset->next();
        $uUIDs = array ();
        while ($aRow = $oDataset->getRow()) {
            $uUIDs[] = $aRow['USR_UID'];
            $oDataset->next();
        }

        $aUsers = Array ();
        $oCriteria = new Criteria( 'workflow' );
        $oCriteria->addSelectColumn( 'COUNT(*) AS CNT' );
        $oCriteria->add( UsersPeer::USR_STATUS, 'CLOSED', Criteria::NOT_EQUAL );
        $oCriteria->add( UsersPeer::USR_UID, $uUIDs, Criteria::NOT_IN );
        $filter = (isset( $_POST['textFilter'] )) ? $_POST['textFilter'] : '';
        if ($filter != '') {
            $oCriteria->add( $oCriteria->getNewCriterion( UsersPeer::USR_USERNAME, '%' . $filter . '%', Criteria::LIKE )->addOr( $oCriteria->getNewCriterion( UsersPeer::USR_FIRSTNAME, '%' . $filter . '%', Criteria::LIKE )->addOr( $oCriteria->getNewCriterion( UsersPeer::USR_LASTNAME, '%' . $filter . '%', Criteria::LIKE ) ) ) );
        }
        $oDataset = UsersPeer::DoSelectRs( $oCriteria );
        $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
        $oDataset->next();
        $row = $oDataset->getRow();
        $totalRows = $row['CNT'];

        $oCriteria = new Criteria( 'workflow' );
        $oCriteria->addSelectColumn( UsersPeer::USR_UID );
        $oCriteria->addSelectColumn( UsersPeer::USR_USERNAME );
        $oCriteria->addSelectColumn( UsersPeer::USR_FIRSTNAME );
        $oCriteria->addSelectColumn( UsersPeer::USR_LASTNAME );
        $oCriteria->addSelectColumn( UsersPeer::USR_EMAIL );
        $oCriteria->addSelectColumn( UsersPeer::USR_STATUS );
        $oCriteria->add( UsersPeer::USR_STATUS, 'CLOSED', Criteria::NOT_EQUAL );
        $oCriteria->add( UsersPeer::USR_UID, $uUIDs, Criteria::NOT_IN );
        $filter = (isset( $_POST['textFilter'] )) ? $_POST['textFilter'] : '';
        if ($filter != '') {
            $oCriteria->add( $oCriteria->getNewCriterion( UsersPeer::USR_USERNAME, '%' . $filter . '%', Criteria::LIKE )->addOr( $oCriteria->getNewCriterion( UsersPeer::USR_FIRSTNAME, '%' . $filter . '%', Criteria::LIKE )->addOr( $oCriteria->getNewCriterion( UsersPeer::USR_LASTNAME, '%' . $filter . '%', Criteria::LIKE ) ) ) );
        }
        $oCriteria->setOffset( $start );
        $oCriteria->setLimit( $limit );
        $oDataset = UsersPeer::doSelectRS( $oCriteria );
        $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
        $arrData = Array ();
        while ($oDataset->next()) {
            $arrData[] = $oDataset->getRow();
        }
        echo '{success: true, members: ' . G::json_encode( $arrData ) . ', total_users: ' . $totalRows . '}';
        break;
    case 'assignUsersToGroupsMultiple':
        $GRP_UID = $_POST['GRP_UID'];
        $uUIDs = explode( ',', $_POST['USR_UID'] );
        G::LoadClass( 'groups' );
        $oGroup = new Groups();
        foreach ($uUIDs as $USR_UID) {
            $oGroup->addUserToGroup( $GRP_UID, $USR_UID );
        }
        break;
    case 'deleteUsersToGroupsMultiple':
        $GRP_UID = $_POST['GRP_UID'];
        $uUIDs = explode( ',', $_POST['USR_UID'] );
        G::LoadClass( 'groups' );
        $oGroup = new Groups();
        foreach ($uUIDs as $USR_UID) {
            $oGroup->removeUserOfGroup( $GRP_UID, $USR_UID );
        }
        break;
    case 'updatePageSize':
        G::LoadClass( 'configuration' );
        $c = new Configurations();
        $arr['pageSize'] = $_REQUEST['size'];
        $arr['dateSave'] = date( 'Y-m-d H:i:s' );
        $config = Array ();
        $config[] = $arr;
        $c->aConfig = $config;
        $c->saveConfig( 'groupList', 'pageSize', '', $_SESSION['USER_LOGGED'] );
        echo '{success: true}';
        break;
}

