<?php

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
    if (isset($_POST['form'])) {
        $_POST = $_POST['form'];
    }
    if (isset($_REQUEST['function'])) {
        $value = get_ajax_value('function');
    } else {
        $value = get_ajax_value('functions');
    }

    $RBAC->allows(basename(__FILE__), $value);
    switch ($value) {
        case 'availableUsers':
            //Classic process: list of users to assign in the task
            $oProcessMap = new ProcessMap();
            global $G_PUBLISH;
            $G_PUBLISH = new Publisher();
            $G_PUBLISH->AddContent('propeltable', 'paged-table', 'users/users_AvailableUsers', $oProcessMap->getAvailableUsersCriteria($_GET['sTask'], $_GET['iType']));
            G::RenderPage('publish', 'raw');
            break;
        case 'assign':
            //Classic process: assign users and groups in the task
            $oTasks = new Tasks();
            switch ((int) $_POST['TU_RELATION']) {
                case 1:
                    $resh = htmlentities($oTasks->assignUser($_POST['TAS_UID'], $_POST['USR_UID'], $_POST['TU_TYPE']), ENT_QUOTES | ENT_HTML5, 'UTF-8');
                    G::outRes($resh);
                    G::auditlog("AssignUserTask","Assign a User to a Task -> ".$_POST['TAS_UID'].' User UID -> '.$_POST['USR_UID']);
                    break;
                case 2:
                    $resh = htmlentities($oTasks->assignGroup($_POST['TAS_UID'], $_POST['USR_UID'], $_POST['TU_TYPE']), ENT_QUOTES | ENT_HTML5, 'UTF-8');
                    G::outRes($resh);
                    G::auditlog("AssignGroupTask","Assign a Group to a Task -> ".$_POST['TAS_UID'].' User UID -> '.$_POST['USR_UID']);
                    break;
            }
            break;
        case 'ofToAssign':
            //Classic process: remove users and groups related a task
            $oTasks = new Tasks();
            switch ((int) $_POST['TU_RELATION']) {
                case 1:
                    echo $oTasks->ofToAssignUser($_POST['TAS_UID'], $_POST['USR_UID'], $_POST['TU_TYPE']);
                    G::auditlog("DeleteUserTask"," Delete a User from a Task -> ".$_POST['TAS_UID'].' User UID -> '.$_POST['USR_UID']);
                    break;
                case 2:
                    echo $oTasks->ofToAssignGroup($_POST['TAS_UID'], $_POST['USR_UID'], $_POST['TU_TYPE']);
                    G::auditlog("DeleteGroupTask","Delete a Group from a Task -> ".$_POST['TAS_UID'].' User UID -> '.$_POST['USR_UID']);
                    break;
            }
            break;
        case 'changeView':
            //Classic process: set variable for users and groups Ad hoc
            $_SESSION['iType'] = $_POST['TU_TYPE'];
            break;
        case 'usersGroup':
            //Classic process: list of users in a group related a task
            $oGroup = new Groups();
            $aGroup = $oGroup->getUsersOfGroup($_POST['GRP_UID']);
            foreach ($aGroup as $iIndex => $aValues) {
                echo $aValues['USR_FIRSTNAME'] . ' ' . $aValues['USR_LASTNAME'] . '<br>';
            }
            break;
        case 'canDeleteUser':
            //Check before delete a user
            $oProcessMap = new Cases();
            $userUid = $_POST['uUID'];
            $total = 0;
            $history = 0;
            $c = $oProcessMap->getCriteriaUsersCases('TO_DO', $userUid);
            $total += ApplicationPeer::doCount($c);
            $c = $oProcessMap->getCriteriaUsersCases('DRAFT', $userUid);
            $total += ApplicationPeer::doCount($c);
            $c = $oProcessMap->getCriteriaUsersCases('COMPLETED', $userUid);
            $history += ApplicationPeer::doCount($c);
            $c = $oProcessMap->getCriteriaUsersCases('CANCELLED', $userUid);
            $history += ApplicationPeer::doCount($c);
            //Check if the user is configured in Web Entry
            if ($total === 0) {
                $webEntry = new \ProcessMaker\BusinessModel\WebEntryEvent();
                $total = $webEntry->getWebEntryRelatedToUser($userUid);
            }

            //check user guest
            if (RBAC::isGuestUserUid($userUid)) {
                $total++;
            }

            $response = '{success: true, candelete: ';
            $response .= ($total > 0) ? 'false' : 'true';
            $response .= ', hashistory: ';
            $response .= ($history > 0) ? 'true' : 'false';
            $response .= '}';
            echo $response;
            break;
        case 'deleteUser':
            $usrUid = $_POST['USR_UID'];
            //Check if the user was defined in a process permissions
            $oObjectPermission = new ObjectPermission();
            $aProcess = $oObjectPermission->objectPermissionPerUser($usrUid, 1);
            if (count($aProcess) > 0) {
                echo G::json_encode(array(
                    "status" => 'ERROR',
                    "message" => G::LoadTranslation('ID_USER_CANT_BE_DELETED_FOR_THE_PROCESS', array('processTitle' => isset($aProcess["PRO_TITLE"]) ? $aProcess["PRO_TITLE"] : $aProcess['PRO_UID']))
                ));
                break;
            }

            //Remove from tasks
            $oTasks = new Tasks();
            $oTasks->ofToAssignUserOfAllTasks($usrUid);

            //Remove from groups
            $oGroups = new Groups();
            $oGroups->removeUserOfAllGroups($usrUid);

            //Update the table Users
            $RBAC->changeUserStatus($usrUid, 'CLOSED');
            $RBAC->updateUser(array('USR_UID' => $usrUid,'USR_USERNAME' => ''), '');
            $oUser = new Users();
            $aFields = $oUser->load($usrUid);
            $aFields['USR_STATUS'] = 'CLOSED';
            $userName = $aFields['USR_USERNAME'];
            $aFields['USR_USERNAME'] = '';
            $oUser->update($aFields);

            //Delete Dashboard
            $criteria = new Criteria( 'workflow' );
            $criteria->add( DashletInstancePeer::DAS_INS_OWNER_UID, $usrUid );
            $criteria->add( DashletInstancePeer::DAS_INS_OWNER_TYPE , 'USER');
            DashletInstancePeer::doDelete( $criteria );

            //Delete users as supervisor
            $criteria = new Criteria("workflow");
            $criteria->add(ProcessUserPeer::USR_UID, $usrUid, Criteria::EQUAL);
            $criteria->add(ProcessUserPeer::PU_TYPE, "SUPERVISOR", Criteria::EQUAL);
            ProcessUserPeer::doDelete($criteria);
            G::auditLog("DeleteUser", "User Name: ". $userName." User ID: (".$usrUid.") ");
            break;
        case 'changeUserStatus':
            //When the user change the status: ACTIVE, INACTIVE, VACATION
            $response = new stdclass();
            if (isset($_REQUEST['USR_UID']) && isset($_REQUEST['NEW_USR_STATUS'])) {
                $RBAC->changeUserStatus($_REQUEST['USR_UID'], ($_REQUEST['NEW_USR_STATUS'] == 'ACTIVE' ? 1 : 0));
                $userInstance = new Users();
                $userData = $userInstance->load($_REQUEST['USR_UID']);
                $userData['USR_STATUS'] = $_REQUEST['NEW_USR_STATUS'];
                $userInstance->update($userData);

                $msg = $_REQUEST['NEW_USR_STATUS'] == 'ACTIVE'? "EnableUser" : "DisableUser";
                G::auditLog($msg, "User Name: ".$userData['USR_USERNAME']." User ID: (".$userData['USR_UID'].") ");
                $response->status = 'OK';
            } else {
                $response->status = 'ERROR';
                $response->message = 'USR_UID and NEW_USR_STATUS parameters are required.';
            }
            die(G::json_encode($response));
            break;
        case 'availableGroups':
            //Get the available groups for assign to user
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
            //Get the groups related to user
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
            //Assign user in a group
            $USR_UID = $_POST['USR_UID'];
            $gUIDs = explode(',', $_POST['GRP_UID']);
            $oGroup = new Groups();
            foreach ($gUIDs as $GRP_UID) {
                $oGroup->addUserToGroup($GRP_UID, $USR_UID);
            }
            break;
        case 'deleteGroupsToUserMultiple':
            //Remove a user from a group
            $USR_UID = $_POST['USR_UID'];
            $gUIDs = explode(',', $_POST['GRP_UID']);
            $oGroup = new Groups();
            foreach ($gUIDs as $GRP_UID) {
                $oGroup->removeUserOfGroup($GRP_UID, $USR_UID);
            }
            break;
        case 'authSources':
            //Get the authentication information
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
            $started['AUTH_SOURCE_SHOW'] = 'ProcessMaker (MYSQL)';
            $arr[] = $started;
            while ($objects->next()) {
                $row = $objects->getRow();
                $aux = Array();
                $aux['AUTH_SOURCE_UID'] = $row['AUTH_SOURCE_UID'];
                $aux['AUTH_SOURCE_SHOW'] = $row['AUTH_SOURCE_NAME'] . ' (' . $row['AUTH_SOURCE_PROVIDER'] . ')';
                $arr[] = $aux;
            }
            echo '{sources: ' . G::json_encode($arr) . '}';
            break;
        case 'loadAuthSourceByUID':
            //Get the authentication source assignment
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
            //Update the information related to user's autentication
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
                $aData['USR_AUTH_USER_DN'] = $auth_dn;
            }
            $RBAC->updateUser($aData);
            G::auditLog(
                "AssignAuthenticationSource",
                "User Name: ".$aData['USR_USERNAME'].' User ID: ('.$aData['USR_UID'].') assign to '.$aData['USR_AUTH_TYPE']
            );
            echo '{success: true}';
            break;
        case 'usersList':
            //Get the list of users
            //Read the configurations related to enviroments
            $co = new Configurations();
            $config = $co->getConfiguration('usersList', 'pageSize', '', $_SESSION['USER_LOGGED']);
            $limit_size = isset($config['pageSize']) ? $config['pageSize'] : 20;
            $limit = isset($_REQUEST['limit']) ? $_REQUEST['limit'] : $limit_size;
            $start = isset($_REQUEST['start']) ? $_REQUEST['start'] : 0;
            $filter = isset($_REQUEST['textFilter']) ? $_REQUEST['textFilter'] : '';
            $authSource = isset($_REQUEST['auths']) ? $_REQUEST['auths'] : '';
            $sort = isset($_REQUEST['sort']) ? $_REQUEST['sort'] : '';
            $dir = isset($_REQUEST['dir']) ? $_REQUEST['dir'] : 'ASC';

            //Get all list of users with the additional information related to department, role, authentication, cases
            $oUser = new \ProcessMaker\BusinessModel\User();
            $oDatasetUsers = $oUser->getAllUsersWithAuthSource($authSource, $filter, $sort, $start, $limit, $dir);
            $rows = $oUser->getAdditionalInfoFromUsers($oDatasetUsers["data"]);
            echo '{users: ' . G::json_encode($rows['data']) . ', total_users: ' . $oDatasetUsers["totalRows"] . '}';
            break;
        case 'updatePageSize':
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
            //Get all information for the summary
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
                $arrayConfFormat = $c->getFormats();

                $replaced_by = G::getFormatUserList($arrayConfFormat['format'], $u);
            } else {
                $replaced_by = '';
            }
            $misc = Array();
            $misc['DEP_TITLE'] = $dep_name;
            $misc['REPLACED_NAME'] = $replaced_by;
            echo '{success: true, userdata: ' . G::json_encode($data) . ', cases: ' . G::json_encode($aCount) . ', misc: ' . G::json_encode($misc) . '}';
            break;

        case "verifyIfUserAssignedAsSupervisor":
            //Before delete we check if is supervisor
            $supervisor = new \ProcessMaker\BusinessModel\ProcessSupervisor();
            $isSupervisor = $supervisor->isUserSupervisor($_POST["supervisorUserUid"]);
            $supervisorUserUid = $_POST["supervisorUserUid"];
            $message = 'OK';
            if ($isSupervisor) {
                $message = 'ERROR';
            }
            $response = array();
            $response["result"] = $message;
            echo G::json_encode($response);
            break;
    }
} catch (Exception $oException) {
    $token = strtotime("now");
    PMException::registerErrorLog($oException, $token);
    G::outRes( G::LoadTranslation("ID_EXCEPTION_LOG_INTERFAZ", array($token)) );
    die;
}
