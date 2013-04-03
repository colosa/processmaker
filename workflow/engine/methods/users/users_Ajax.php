<?php

/**
 * users_Ajax.php
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
    switch ($RBAC->userCanAccess('PM_LOGIN')) {
        case - 2:
            G::SendTemporalMessage('ID_USER_HAVENT_RIGHTS_SYSTEM', 'error', 'labels');
            G::header('location: ../login/login');
            die();
            break;
        case - 1:
            G::SendTemporalMessage('ID_USER_HAVENT_RIGHTS_PAGE', 'error', 'labels');
            G::header('location: ../login/login');
            die();
            break;
    }
    G::LoadInclude('ajax');
    if (isset($_POST['form'])) {
        $_POST = $_POST['form'];
    }
    if (isset($_REQUEST['function'])) {
        //$value= $_POST['function'];
        $value = get_ajax_value('function');
    } else {
        //$value= $_POST['functions'];
        $value = get_ajax_value('functions');
    }
    switch ($value) {
        case 'verifyUsername':
            //print_r($_POST); die;
            $_POST['sOriginalUsername'] = get_ajax_value('sOriginalUsername');
            $_POST['sUsername'] = get_ajax_value('sUsername');
            if ($_POST['sOriginalUsername'] == $_POST['sUsername']) {
                echo '0';
            } else {
                require_once 'classes/model/Users.php';
                G::LoadClass('Users');
                $oUser = new Users();
                $oCriteria = $oUser->loadByUsername($_POST['sUsername']);
                $oDataset = UsersPeer::doSelectRS($oCriteria);
                $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
                $oDataset->next();
                $aRow = $oDataset->getRow();
                //print_r($aRow); die;
                //if (!$aRow)
                if (!is_array($aRow)) {
                    echo '0';
                } else {
                    echo '1';
                }
            }
            break;
        case 'availableUsers':
            G::LoadClass('processMap');
            $oProcessMap = new ProcessMap();
            global $G_PUBLISH;
            $G_PUBLISH = new Publisher();
            $G_PUBLISH->AddContent('propeltable', 'paged-table', 'users/users_AvailableUsers', $oProcessMap->getAvailableUsersCriteria($_GET['sTask'], $_GET['iType']));
            G::RenderPage('publish', 'raw');
            break;
        case 'assign':
            G::LoadClass('tasks');
            $oTasks = new Tasks();
            switch ((int) $_POST['TU_RELATION']) {
                case 1:
                    echo $oTasks->assignUser($_POST['TAS_UID'], $_POST['USR_UID'], $_POST['TU_TYPE']);
                    break;
                case 2:
                    echo $oTasks->assignGroup($_POST['TAS_UID'], $_POST['USR_UID'], $_POST['TU_TYPE']);
                    break;
            }
            break;
        case 'ofToAssign':
            G::LoadClass('tasks');
            $oTasks = new Tasks();
            switch ((int) $_POST['TU_RELATION']) {
                case 1:
                    echo $oTasks->ofToAssignUser($_POST['TAS_UID'], $_POST['USR_UID'], $_POST['TU_TYPE']);
                    break;
                case 2:
                    echo $oTasks->ofToAssignGroup($_POST['TAS_UID'], $_POST['USR_UID'], $_POST['TU_TYPE']);
                    break;
            }
            break;
        case 'changeView':
            $_SESSION['iType'] = $_POST['TU_TYPE'];
            break;
        case 'deleteGroup':
            G::LoadClass('groups');
            $oGroup = new Groups();
            $oGroup->removeUserOfGroup($_POST['GRP_UID'], $_POST['USR_UID']);
            $_GET['sUserUID'] = $_POST['USR_UID'];
            $G_PUBLISH = new Publisher();
            $G_PUBLISH->AddContent('view', 'users/users_Tree');
            G::RenderPage('publish', 'raw');
            break;
        case 'showUserGroupInterface':
            $_GET['sUserUID'] = $_POST['sUserUID'];
            $G_PUBLISH = new Publisher();
            $G_PUBLISH->AddContent('view', 'users/users_AssignGroup');
            G::RenderPage('publish', 'raw');
            break;
        case 'showUserGroups':
            $_GET['sUserUID'] = $_POST['sUserUID'];
            $G_PUBLISH = new Publisher();
            $G_PUBLISH->AddContent('view', 'users/users_Tree');
            G::RenderPage('publish', 'raw');
            break;
        case 'assignUserToGroup':
            G::LoadClass('groups');
            $oGroup = new Groups();
            $oGroup->addUserToGroup($_POST['GRP_UID'], $_POST['USR_UID']);
            echo '<div align="center"><h2><font color="blue">' . G::LoadTranslation('ID_MSG_ASSIGN_DONE') . '</font></h2></div>';
            break;
        case 'usersGroup':
            G::LoadClass('groups');
            $oGroup = new Groups();
            $aGroup = $oGroup->getUsersOfGroup($_POST['GRP_UID']);
            foreach ($aGroup as $iIndex => $aValues) {
                echo $aValues['USR_FIRSTNAME'] . ' ' . $aValues['USR_LASTNAME'] . '<br>';
            }
            break;

        //This case is used to check if any of the user group has as role 'PROCESSMAKER_ADMIN',
        case 'usersAdminGroupExtJS':
            G::LoadClass('groups');
            $oGroup = new Groups();
            $aGroup = $oGroup->getUsersOfGroup($_POST['GRP_UID']);
            $responseUser = 'false';
            $usersAdmin = '';
            foreach ($aGroup as $iIndex => $aValues) {
                if ($aValues['USR_ROLE'] == 'PROCESSMAKER_ADMIN') {
                    $responseUser = 'true';
                    $usersAdmin .= $aValues['USR_FIRSTNAME'] . ' ' . $aValues['USR_LASTNAME'] . ', ';
                }
            }
            $usersAdmin = substr($usersAdmin, 0, - 2);

            $result = new stdClass();
            $result->reponse = $responseUser;
            $result->users = $usersAdmin;

            echo G::json_encode($result);
            break;
        case 'canDeleteUser':
            G::LoadClass('case');
            $oProcessMap = new Cases();
            $USR_UID = $_POST['uUID'];
            $total = 0;
            $history = 0;
            $c = $oProcessMap->getCriteriaUsersCases('TO_DO', $USR_UID);
            $total += ApplicationPeer::doCount($c);
            $c = $oProcessMap->getCriteriaUsersCases('DRAFT', $USR_UID);
            $total += ApplicationPeer::doCount($c);
            $c = $oProcessMap->getCriteriaUsersCases('COMPLETED', $USR_UID);
            $history += ApplicationPeer::doCount($c);
            $c = $oProcessMap->getCriteriaUsersCases('CANCELLED', $USR_UID);
            $history += ApplicationPeer::doCount($c);
            $response = '{success: true, candelete: ';
            $response .= ($total > 0) ? 'false' : 'true';
            $response .= ', hashistory: ';
            $response .= ($history > 0) ? 'true' : 'false';
            $response .= '}';
            echo $response;
            break;
        case 'deleteUser':
            $UID = $_POST['USR_UID'];
            G::LoadClass('tasks');
            $oTasks = new Tasks();
            $oTasks->ofToAssignUserOfAllTasks($UID);
            G::LoadClass('groups');
            $oGroups = new Groups();
            $oGroups->removeUserOfAllGroups($UID);
            $RBAC->changeUserStatus($UID, 'CLOSED');
            $_GET['USR_USERNAME'] = '';
            $RBAC->updateUser(array('USR_UID' => $UID, 'USR_USERNAME' => $_GET['USR_USERNAME']
                    ), '');
            require_once 'classes/model/Users.php';
            $oUser = new Users();
            $aFields = $oUser->load($UID);
            $aFields['USR_STATUS'] = 'CLOSED';
            $aFields['USR_USERNAME'] = '';
            $oUser->update($aFields);

            //Delete Dashboard
            require_once 'classes/model/DashletInstance.php';
            $criteria = new Criteria( 'workflow' );
            $criteria->add( DashletInstancePeer::DAS_INS_OWNER_UID, $UID );
            $criteria->add( DashletInstancePeer::DAS_INS_OWNER_TYPE , 'USER');
            DashletInstancePeer::doDelete( $criteria );
            break;
        case 'changeUserStatus':
            $response = new stdclass();
            if (isset($_REQUEST['USR_UID']) && isset($_REQUEST['NEW_USR_STATUS'])) {
                $RBAC->changeUserStatus($_REQUEST['USR_UID'], ($_REQUEST['NEW_USR_STATUS'] == 'ACTIVE' ? 1 : 0));
                require_once 'classes/model/Users.php';
                $userInstance = new Users();
                $userData = $userInstance->load($_REQUEST['USR_UID']);
                $userData['USR_STATUS'] = $_REQUEST['NEW_USR_STATUS'];
                $userInstance->update($userData);
                $response->status = 'OK';
            } else {
                $response->status = 'ERROR';
                $response->message = 'USR_UID and NEW_USR_STATUS parameters are required.';
            }
            die(G::json_encode($response));
            break;
        case 'availableGroups':
            G::LoadClass('groups');
            $filter = (isset($_POST['textFilter'])) ? $_POST['textFilter'] : '';
            $groups = new Groups();
            $criteria = $groups->getAvailableGroupsCriteria($_REQUEST['uUID'], $filter);
            $objects = GroupwfPeer::doSelectRS($criteria);
            $objects->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $arr = Array();
            while ($objects->next()) {
                $arr[] = $objects->getRow();
            }
            echo '{groups: ' . G::json_encode($arr) . '}';
            break;
        case 'assignedGroups':
            G::LoadClass('groups');
            $filter = (isset($_POST['textFilter'])) ? $_POST['textFilter'] : '';
            $groups = new Groups();
            $criteria = $groups->getAssignedGroupsCriteria($_REQUEST['uUID'], $filter);
            $objects = GroupwfPeer::doSelectRS($criteria);
            $objects->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $arr = Array();
            while ($objects->next()) {
                $arr[] = $objects->getRow();
            }
            echo '{groups: ' . G::json_encode($arr) . '}';
            break;
        case 'assignGroupsToUserMultiple':
            $USR_UID = $_POST['USR_UID'];
            $gUIDs = explode(',', $_POST['GRP_UID']);
            G::LoadClass('groups');
            $oGroup = new Groups();
            foreach ($gUIDs as $GRP_UID) {
                $oGroup->addUserToGroup($GRP_UID, $USR_UID);
            }
            break;
        case 'deleteGroupsToUserMultiple':
            $USR_UID = $_POST['USR_UID'];
            $gUIDs = explode(',', $_POST['GRP_UID']);
            G::LoadClass('groups');
            $oGroup = new Groups();
            foreach ($gUIDs as $GRP_UID) {
                $oGroup->removeUserOfGroup($GRP_UID, $USR_UID);
            }
            break;
        case 'authSources':
            $criteria = $RBAC->getAllAuthSources();
            $objects = AuthenticationSourcePeer::doSelectRS($criteria);
            $objects->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $arr = Array();
            if (isset($_REQUEST['cmb'])) {
                if ($_REQUEST['cmb'] == 'yes') {
                    $started = Array();
                    $started['AUTH_SOURCE_UID'] = '';
                    $started['AUTH_SOURCE_SHOW'] = G::LoadTranslation('ID_ALL');
                    $arr[] = $started;
                }
            }
            $started = Array();
            $started['AUTH_SOURCE_UID'] = '00000000000000000000000000000000';
            //$started['AUTH_SOURCE_NAME'] = 'ProcessMaker';
            //$started['AUTH_SOURCE_TYPE'] = 'MYSQL';
            $started['AUTH_SOURCE_SHOW'] = 'ProcessMaker (MYSQL)';
            $arr[] = $started;
            while ($objects->next()) {
                $row = $objects->getRow();
                $aux = Array();
                $aux['AUTH_SOURCE_UID'] = $row['AUTH_SOURCE_UID'];
                //$aux['AUTH_SOURCE_NAME'] =  $row['AUTH_SOURCE_NAME'];
                //$aux['AUTH_SOURCE_TYPE'] =  $row['AUTH_SOURCE_TYPE'];
                $aux['AUTH_SOURCE_SHOW'] = $row['AUTH_SOURCE_NAME'] . ' (' . $row['AUTH_SOURCE_PROVIDER'] . ')';
                $arr[] = $aux;
            }
            echo '{sources: ' . G::json_encode($arr) . '}';
            break;
        case 'loadAuthSourceByUID':
            require_once 'classes/model/Users.php';
            $oCriteria = $RBAC->load($_POST['uUID']);
            $UID_AUTH = $oCriteria['UID_AUTH_SOURCE'];
            if (($UID_AUTH != '00000000000000000000000000000000') && ($UID_AUTH != '')) {
                $aux = $RBAC->getAuthSource($UID_AUTH);
                $arr = Array();
                $arr['AUTH_SOURCE_NAME'] = $aux['AUTH_SOURCE_NAME'] . ' (' . $aux['AUTH_SOURCE_PROVIDER'] . ')';
                $arr['AUTH_SOURCE_PROVIDER'] = $aux['AUTH_SOURCE_PROVIDER'];
                $aFields = $arr;
            } else {
                $arr = Array();
                $arr['AUTH_SOURCE_NAME'] = 'ProcessMaker (MYSQL)';
                $arr['AUTH_SOURCE_PROVIDER'] = 'MYSQL';
                $aFields = $arr;
            }
            $res = Array();
            $res['data'] = $oCriteria;
            $res['auth'] = $aFields;
            echo G::json_encode($res);
            break;
        case 'updateAuthServices':
            $aData = $RBAC->load($_POST['usr_uid']);
            unset($aData['USR_ROLE']);
            $auth_uid = $_POST['auth_source'];
            $auth_uid2 = $_POST['auth_source_uid'];
            if ($auth_uid == $auth_uid2) {
                $auth_uid = $aData['UID_AUTH_SOURCE'];
            }
            if (($auth_uid == '00000000000000000000000000000000') || ($auth_uid == '')) {
                $aData['USR_AUTH_TYPE'] = 'MYSQL';
                $aData['UID_AUTH_SOURCE'] = '';
            } else {
                $aFields = $RBAC->getAuthSource($auth_uid);
                $aData['USR_AUTH_TYPE'] = $aFields['AUTH_SOURCE_PROVIDER'];
                $aData['UID_AUTH_SOURCE'] = $auth_uid;
            }
            if (isset($_POST['auth_dn'])) {
                $auth_dn = $_POST['auth_dn'];
            } else {
                $auth_dn = "";
            }
            $aData['USR_AUTH_USER_DN'] = $auth_dn;
            $RBAC->updateUser($aData);
            echo '{success: true}';
            break;
        case 'usersList':
            require_once 'classes/model/Users.php';
            require_once 'classes/model/LoginLog.php';
            require_once 'classes/model/Department.php';
            require_once 'classes/model/AppCacheView.php';
            require_once PATH_RBAC . 'model/Roles.php';
            global $RBAC;
            G::LoadClass('configuration');
            $co = new Configurations();
            $config = $co->getConfiguration('usersList', 'pageSize', '', $_SESSION['USER_LOGGED']);
            $limit_size = isset($config['pageSize']) ? $config['pageSize'] : 20;
            $start = isset($_REQUEST['start']) ? $_REQUEST['start'] : 0;
            $limit = isset($_REQUEST['limit']) ? $_REQUEST['limit'] : $limit_size;
            $filter = isset($_REQUEST['textFilter']) ? $_REQUEST['textFilter'] : '';
            $auths = isset($_REQUEST['auths']) ? $_REQUEST['auths'] : '';
            $sort = isset($_REQUEST['sort']) ? $_REQUEST['sort'] : '';
            $dir = isset($_REQUEST['dir']) ? $_REQUEST['dir'] : 'ASC';
            $aUsers = Array();
            if ($auths != '') {
                $aUsers = $RBAC->getListUsersByAuthSource($auths);
            }
            $oCriteria = new Criteria('workflow');
            $oCriteria->addSelectColumn('COUNT(*) AS CNT');
            if ($filter != '') {
                $cc = $oCriteria->getNewCriterion(UsersPeer::USR_USERNAME, '%' . $filter . '%', Criteria::LIKE)->addOr($oCriteria->getNewCriterion(UsersPeer::USR_FIRSTNAME, '%' . $filter . '%', Criteria::LIKE)->addOr($oCriteria->getNewCriterion(UsersPeer::USR_LASTNAME, '%' . $filter . '%', Criteria::LIKE)->addOr($oCriteria->getNewCriterion(UsersPeer::USR_EMAIL, '%' . $filter . '%', Criteria::LIKE))));
                $oCriteria->add($cc);
            }
            $oCriteria->add(UsersPeer::USR_STATUS, array('CLOSED'), Criteria::NOT_IN);
            if ($auths != '') {
                $totalRows = sizeof($aUsers);
            } else {
                $oDataset = UsersPeer::DoSelectRs($oCriteria);
                $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
                $oDataset->next();
                $row = $oDataset->getRow();
                $totalRows = $row['CNT'];
            }
            $oCriteria->clearSelectColumns();
            $oCriteria->addSelectColumn(UsersPeer::USR_UID);
            $oCriteria->addSelectColumn(UsersPeer::USR_USERNAME);
            $oCriteria->addSelectColumn(UsersPeer::USR_FIRSTNAME);
            $oCriteria->addSelectColumn(UsersPeer::USR_LASTNAME);
            $oCriteria->addSelectColumn(UsersPeer::USR_EMAIL);
            $oCriteria->addSelectColumn(UsersPeer::USR_ROLE);
            $oCriteria->addSelectColumn(UsersPeer::USR_DUE_DATE);
            $oCriteria->addSelectColumn(UsersPeer::USR_STATUS);
            $oCriteria->addSelectColumn(UsersPeer::USR_UX);
            $oCriteria->addSelectColumn(UsersPeer::DEP_UID);
            $oCriteria->addAsColumn('LAST_LOGIN', 0);
            $oCriteria->addAsColumn('DEP_TITLE', 0);
            $oCriteria->addAsColumn('TOTAL_CASES', 0);
            $oCriteria->addAsColumn('DUE_DATE_OK', 1);
            $sep = "'";
            $oCriteria->add(UsersPeer::USR_STATUS, array('CLOSED'), Criteria::NOT_IN);
            if ($filter != '') {
                $cc = $oCriteria->getNewCriterion(UsersPeer::USR_USERNAME, '%' . $filter . '%', Criteria::LIKE)->addOr($oCriteria->getNewCriterion(UsersPeer::USR_FIRSTNAME, '%' . $filter . '%', Criteria::LIKE)->addOr($oCriteria->getNewCriterion(UsersPeer::USR_LASTNAME, '%' . $filter . '%', Criteria::LIKE)->addOr($oCriteria->getNewCriterion(UsersPeer::USR_EMAIL, '%' . $filter . '%', Criteria::LIKE))));
                $oCriteria->add($cc);
            }
            if (sizeof($aUsers) > 0) {
                $oCriteria->add(UsersPeer::USR_UID, $aUsers, Criteria::IN);
            } elseif ($totalRows == 0 && $auths != '') {
                $oCriteria->add(UsersPeer::USR_UID, '', Criteria::IN);
            }
            if ($sort != '') {
                if ($dir == 'ASC') {
                    $oCriteria->addAscendingOrderByColumn($sort);
                } else {
                    $oCriteria->addDescendingOrderByColumn($sort);
                }
            }
            $oCriteria->setOffset($start);
            $oCriteria->setLimit($limit);
            $oDataset = UsersPeer::DoSelectRs($oCriteria);
            $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);

            $Login = new LoginLog();
            $aLogin = $Login->getLastLoginAllUsers();
            $Cases = new AppCacheView();
            $aCases = $Cases->getTotalCasesByAllUsers();
            $Department = new Department();
            $aDepart = $Department->getAllDepartmentsByUser();
            $aAuthSources = $RBAC->getAllAuthSourcesByUser();

            require_once PATH_CONTROLLERS . 'adminProxy.php';
            $uxList = adminProxy::getUxTypesList();

            $oRoles = new Roles();
            $rows = Array();
            while ($oDataset->next()) {
                $row = $oDataset->getRow();
                $uRole = $oRoles->loadByCode($row['USR_ROLE']);
                $row['USR_ROLE'] = isset($uRole['ROL_NAME']) ? ($uRole['ROL_NAME'] != '' ? $uRole['ROL_NAME'] : $uRole['USR_ROLE']) : $uRole['USR_ROLE'];
                $row['DUE_DATE_OK'] = (date('Y-m-d') > date('Y-m-d', strtotime($row['USR_DUE_DATE']))) ? 0 : 1;
                $row['LAST_LOGIN'] = isset($aLogin[$row['USR_UID']]) ? $aLogin[$row['USR_UID']] : '';
                $row['TOTAL_CASES'] = isset($aCases[$row['USR_UID']]) ? $aCases[$row['USR_UID']] : 0;
                $row['DEP_TITLE'] = isset($aDepart[$row['USR_UID']]) ? $aDepart[$row['USR_UID']] : '';
                $row['USR_UX'] = isset($uxList[$row['USR_UX']]) ? $uxList[$row['USR_UX']] : $uxList['NORMAL'];
                $row['USR_AUTH_SOURCE'] = isset($aAuthSources[$row['USR_UID']]) ? $aAuthSources[$row['USR_UID']] : 'ProcessMaker (MYSQL)';

                $rows[] = $row;
            }
            echo '{users: ' . G::json_encode($rows) . ', total_users: ' . $totalRows . '}';
            break;
        case 'updatePageSize':
            G::LoadClass('configuration');
            $c = new Configurations();
            $arr['pageSize'] = $_REQUEST['size'];
            $arr['dateSave'] = date('Y-m-d H:i:s');
            $config = Array();
            $config[] = $arr;
            $c->aConfig = $config;
            $c->saveConfig('usersList', 'pageSize', '', $_SESSION['USER_LOGGED']);
            echo '{success: true}';
            break;
        case 'summaryUserData':
            require_once 'classes/model/Users.php';
            require_once 'classes/model/Department.php';
            require_once 'classes/model/AppCacheView.php';
            G::LoadClass('configuration');
            $oUser = new Users();
            $data = $oUser->loadDetailed($_REQUEST['USR_UID']);
            $data['USR_STATUS'] = G::LoadTranslation('ID_' . $data['USR_STATUS']);
            $oAppCache = new AppCacheView();
            $aTypes = Array();
            $aTypes['to_do'] = 'CASES_INBOX';
            $aTypes['draft'] = 'CASES_DRAFT';
            $aTypes['cancelled'] = 'CASES_CANCELLED';
            $aTypes['sent'] = 'CASES_SENT';
            $aTypes['paused'] = 'CASES_PAUSED';
            $aTypes['completed'] = 'CASES_COMPLETED';
            $aTypes['selfservice'] = 'CASES_SELFSERVICE';
            $aCount = $oAppCache->getAllCounters(array_keys($aTypes), $_REQUEST['USR_UID']);
            $dep = new Department();
            if ($dep->existsDepartment($data['DEP_UID'])) {
                $dep->Load($data['DEP_UID']);
                $dep_name = $dep->getDepTitle();
            } else {
                $dep_name = '';
            }
            if ($data['USR_REPLACED_BY'] != '') {
                $user = new Users();
                $u = $user->load($data['USR_REPLACED_BY']);
                $c = new Configurations();
                $replaced_by = $c->usersNameFormat($u['USR_USERNAME'], $u['USR_FIRSTNAME'], $u['USR_LASTNAME']);
            } else {
                $replaced_by = '';
            }
            $misc = Array();
            $misc['DEP_TITLE'] = $dep_name;
            $misc['REPLACED_NAME'] = $replaced_by;
            echo '{success: true, userdata: ' . G::json_encode($data) . ', cases: ' . G::json_encode($aCount) . ', misc: ' . G::json_encode($misc) . '}';
            break;
    }
} catch (Exception $oException) {
    die($oException->getMessage());
}

