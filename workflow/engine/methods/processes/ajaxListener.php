<?php

/**
 * processes/ajaxListener.php Ajax Listener for Cases rpc requests
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

use ProcessMaker\Plugins\PluginRegistry;

/**
 *
 * @author Erik Amaru Ortiz <erik@colosa.com>
 * @date Jan 10th, 2010
 */

/**
 * verify user authentication, case tracker.
 */
if (!isset($_SESSION['PIN'])) {
    global $RBAC;
    switch ($RBAC->userCanAccess('PM_LOGIN')) {
        case -2:
            G::SendTemporalMessage('ID_USER_HAVENT_RIGHTS_SYSTEM', 'error', 'labels');
            G::header('location: ../login/login');
            die();
            break;
        case -1:
            G::SendTemporalMessage('ID_USER_HAVENT_RIGHTS_PAGE', 'error', 'labels');
            G::header('location: ../login/login');
            die();
            break;
    }
}

$action = $_REQUEST['action'];
unset($_REQUEST['action']);

$ajax = new Ajax();
$ajax->$action($_REQUEST);

class Ajax
{

    public function categoriesList()
    {
        require_once "classes/model/ProcessCategory.php";

        $processCategory = new ProcessCategory();
        $defaultOption = Array();
        $defaultOption[] = Array('CATEGORY_UID' => '<reset>', 'CATEGORY_NAME' => G::LoadTranslation('ID_ALL'));
        $defaultOption[] = Array('CATEGORY_UID' => '', 'CATEGORY_NAME' => G::LoadTranslation('ID_PROCESS_NO_CATEGORY'));

        $response->rows = array_merge($defaultOption, $processCategory->getAll('array'));

        echo G::json_encode($response);
    }

    public function processCategories()
    {
        require_once "classes/model/ProcessCategory.php";

        $processCategory = new ProcessCategory();
        $defaultOption = Array();
        $defaultOption[] = Array('CATEGORY_UID' => '', 'CATEGORY_NAME' => G::LoadTranslation('ID_PROCESS_NO_CATEGORY'));

        $response->rows = array_merge($defaultOption, $processCategory->getAll('array'));

        echo G::json_encode($response);
    }

    public function saveProcess()
    {
        try {
            $oProcessMap = new ProcessMap();

            if (!isset($_POST['PRO_UID'])) {

                if (Process::existsByProTitle($_POST['PRO_TITLE'])) {
                    $result = array(
                        'success' => false,
                        'msg'     => 'Process Save Error',
                        'errors'  => array('PRO_TITLE' => G::LoadTranslation('ID_PROCESSTITLE_ALREADY_EXISTS', SYS_LANG, $_POST))
                    );
                    print G::json_encode($result);
                    exit(0);
                }

                $processData['USR_UID'] = $_SESSION['USER_LOGGED'];
                $processData['PRO_TITLE'] = $_POST['PRO_TITLE'];
                $processData['PRO_DESCRIPTION'] = $_POST['PRO_DESCRIPTION'];
                $processData['PRO_CATEGORY'] = $_POST['PRO_CATEGORY'];

                $sProUid = $oProcessMap->createProcess($processData);

                //call plugins
                $oData['PRO_UID'] = $sProUid;
                $oData['PRO_TEMPLATE'] = (isset($_POST['PRO_TEMPLATE']) && $_POST['PRO_TEMPLATE'] != '') ? $_POST['form']['PRO_TEMPLATE'] : '';
                $oData['PROCESSMAP'] = $oProcessMap;

                $oPluginRegistry = PluginRegistry::loadSingleton();
                $oPluginRegistry->executeTriggers(PM_NEW_PROCESS_SAVE, $oData);
            } else {
                //$oProcessMap->updateProcess($_POST['form']);
                $sProUid = $_POST['PRO_UID'];
            }

            //Save Calendar ID for this process
            if (isset($_POST['PRO_CALENDAR'])) {
                $calendarObj = new Calendar();
                $calendarObj->assignCalendarTo($sProUid, $_POST['PRO_CALENDAR'], 'PROCESS');
            }

            $result->success = true;
            $result->PRO_UID = $sProUid;
            $result->msg = G::LoadTranslation('ID_CREATE_PROCESS_SUCCESS');
        } catch (Exception $e) {
            $result->success = false;
            $result->msg = $e->getMessage();
        }

        print G::json_encode($result);
    }

    public function changeStatus()
    {
        $ids = explode(',', $_REQUEST['UIDS']);

        $oProcess = new Processes();
        if (count($ids) > 0) {
            foreach ($ids as $id) {
                $oProcess->changeStatus($id);
            }
        }
    }

    public function changeDebugMode()
    {
        $ids = explode(',', $_REQUEST['UIDS']);

        $oProcess = new Processes();
        if (count($ids) > 0) {
            foreach ($ids as $id) {
                $oProcess->changeDebugMode($id);
            }
        }
    }

    public function getUsers($params)
    {
        $conf = new Configurations();

        $search = isset($params['search']) ? $params['search'] : null;
        $users = Users::getAll($params['start'], $params['limit'], $search);

        foreach ($users->data as $i => $user) {
            $users->data[$i]['USER'] = $conf->getEnvSetting('format', Array('userName' => $user['USR_USERNAME'], 'firstName' => $user['USR_FIRSTNAME'], 'lastName' => $user['USR_LASTNAME']));
        }
        print G::json_encode($users);
    }

    public function getGroups($params)
    {
        require_once 'classes/model/Groupwf.php';
        $search = isset($params['search']) ? $params['search'] : null;
        $groups = Groupwf::getAll($params['start'], $params['limit'], $search);

        print G::json_encode($groups);
    }

    public function assignUsersTask($param)
    {
        try {
            require_once 'classes/model/TaskUser.php';
            require_once 'classes/model/Task.php';
            $oTaskUser = new TaskUser();
            $UIDS = explode(',', $param['UIDS']);
            $TU_TYPE = 1;

            foreach ($UIDS as $UID) {
                if ($_POST['TU_RELATION'] == 1) {
                    $oTaskUser->create(array('TAS_UID' => $param['TAS_UID'], 'USR_UID' => $UID, 'TU_TYPE' => $TU_TYPE, 'TU_RELATION' => 1));
                } else {
                    $oTaskUser->create(array('TAS_UID' => $param['TAS_UID'], 'USR_UID' => $UID, 'TU_TYPE' => $TU_TYPE, 'TU_RELATION' => 2));
                }
            }
            $task = TaskPeer::retrieveByPk($param['TAS_UID']);

            $result->success = true;
            if (count($UIDS) > 1) {
                $result->msg = G::LoadTranslation('ID_ACTORS_ASSIGNED_SUCESSFULLY', SYS_LANG, Array(count($UIDS), $task->getTasTitle()));
            } else {
                $result->msg = G::LoadTranslation('ID_ACTOR_ASSIGNED_SUCESSFULLY', SYS_LANG, Array('tas_title' => $task->getTasTitle()));
            }
        } catch (Exception $e) {
            $result->success = false;
            $result->msg = $e->getMessage();
        }

        print G::json_encode($result);
    }

    public function removeUsersTask($param)
    {
        try {
            require_once 'classes/model/TaskUser.php';
            $oTaskUser = new TaskUser();
            $USR_UIDS = explode(',', $param['USR_UID']);
            $TU_RELATIONS = explode(',', $param['TU_RELATION']);
            $TU_TYPE = 1;

            foreach ($USR_UIDS as $i => $USR_UID) {
                if ($TU_RELATIONS[$i] == 1) {

                    $oTaskUser->remove($param['TAS_UID'], $USR_UID, $TU_TYPE, 1);
                } else {
                    $oTaskUser->remove($param['TAS_UID'], $USR_UID, $TU_TYPE, 2);
                }
            }

            $result->success = true;
            $result->msg = '';
        } catch (Exception $e) {
            $result->success = false;
            $result->msg = $e->getMessage();
        }

        print G::json_encode($result);
    }

    public function getUsersTask($param)
    {
        $usersTaskList = Array();
        $task = new TaskUser();
        $conf = new Configurations();
        $TU_TYPE = 1;

        $usersTask = $task->getUsersTask($param['TAS_UID'], $TU_TYPE);

        foreach ($usersTask->data as $userTask) {
            $usersTaskListItem['TAS_UID'] = $userTask['TAS_UID'];
            if ($userTask['TU_RELATION'] == 1) {
                $usersTaskListItem['USR_USERNAME'] = $userTask['USR_USERNAME'];
                $usersTaskListItem['USR_FIRSTNAME'] = $userTask['USR_FIRSTNAME'];
                $usersTaskListItem['USR_LASTNAME'] = $userTask['USR_LASTNAME'];
            } else {
                $usersTaskListItem['NAME'] = $userTask['GRP_TITLE'];
            }

            $usersTaskListItem['TU_RELATION'] = $userTask['TU_RELATION'];
            $usersTaskListItem['USR_UID'] = $userTask['USR_UID'];

            $usersTaskList[] = $usersTaskListItem;
        }

        $result->data = $usersTaskList;
        $result->totalCount = $usersTask->totalCount;

        print G::json_encode($result);
    }

    public function getProcessDetail($param)
    {
        $PRO_UID = $param['PRO_UID'];

        $tasks = new Tasks();
        $process = ProcessPeer::retrieveByPk($PRO_UID);

        $tasksList = $tasks->getAllTasks($PRO_UID);

        $rootNode->id = $process->getProUid();
        $rootNode->type = 'process';
        $rootNode->typeLabel = G::LoadTranslation('ID_PROCESS');
        $rootNode->text = $process->getProTitle();
        $rootNode->leaf = count($tasksList) > 0 ? false : true;
        $rootNode->iconCls = 'ss_sprite ss_application';
        $rootNode->expanded = true;
        foreach ($tasksList as $task) {
            $node = new stdClass();
            $node->id = $task['TAS_UID'];
            $node->type = 'task';
            $node->typeLabel = G::LoadTranslation('ID_TASK');
            $node->text = $task['TAS_TITLE'];
            $node->iconCls = 'ss_sprite ss_layout';
            $node->leaf = true;
            $rootNode->children[] = $node;
        }

        $treeDetail[] = $rootNode;
        print G::json_encode($treeDetail);
    }

    public function getProperties($param)
    {
        switch ($param['type']) {
            case 'process':
                $oProcessMap = new ProcessMap(new DBConnection());
                $process = $oProcessMap->editProcessNew($param['UID']);
                $category = ProcessCategoryPeer::retrieveByPk($process['PRO_CATEGORY']);
                $categoryName = is_object($category) ? $category->getCategoryName() : '';
                $calendar = CalendarDefinitionPeer::retrieveByPk($process['PRO_CALENDAR']);
                $calendarName = is_object($calendar) ? $calendar->getCalendarName() : '';

                $properties['Title'] = $process['PRO_TITLE'];
                $properties['Description'] = $process['PRO_DESCRIPTION'];
                $properties['Calendar'] = $calendarName;
                $properties['Category'] = $categoryName;
                $properties['Debug'] = $process['PRO_DEBUG'] == '1' ? true : false;

                $result->sucess = true;
                $result->prop = $properties;
                break;
            case 'task':
                require_once 'classes/model/Task.php';
                $task = new Task();
                $taskData = $task->load($param['UID']);

                $properties['Title'] = $taskData['TAS_TITLE'];
                $properties['Description'] = $taskData['TAS_DESCRIPTION'];
                $properties['Variable for case priority'] = $taskData['TAS_PRIORITY_VARIABLE'];
                $properties['Starting Task'] = $taskData['TAS_START'] == 'TRUE' ? true : false;

                $result->sucess = true;
                $result->prop = $properties;

                break;
        }

        print G::json_encode($result);
    }

    public function saveProperties($param)
    {
        try {
            $result->sucess = true;
            $result->msg = '';

            switch ($param['type']) {
                case 'process':
                    $oProcessMap = new ProcessMap();
                    $process['PRO_UID'] = $param['UID'];

                    switch ($param['property']) {
                        case 'Title':
                            $fieldName = 'PRO_TITLE';
                            break;
                        case 'Description':
                            $fieldName = 'PRO_DESCRIPTION';
                            break;
                        case 'Debug':
                            $fieldName = 'PRO_DEBUG';
                            $param['value'] = $param['value'] == 'true' ? '1' : '0';
                            break;
                        case 'Category':
                            $fieldName = 'PRO_CATEGORY';
                            $category = ProcessCategory::loadByCategoryName($param['value']);
                            $param['value'] = $category[0]['CATEGORY_UID'];
                            break;
                        case 'Calendar':
                            $fieldName = 'PRO_CALENDAR';
                            $calendar = CalendarDefinition::loadByCalendarName($param['value']);

                            $calendarObj = new Calendar();
                            $calendarObj->assignCalendarTo($process['PRO_UID'], $calendar['CALENDAR_UID'], 'PROCESS');
                            break;
                    }

                    if ($fieldName != 'PRO_CALENDAR') {
                        $process[$fieldName] = $param['value'];
                        $oProcessMap->updateProcess($process);
                    }
                    break;
                case 'task':
                    require_once 'classes/model/Task.php';
                    $oTask = new Task();
                    $task['TAS_UID'] = $param['UID'];

                    switch ($param['property']) {
                        case 'Title':
                            $fieldName = 'TAS_TITLE';
                            break;
                        case 'Description':
                            $fieldName = 'TAS_DESCRIPTION';
                            break;
                        case 'Variable for case priority':
                            $fieldName = 'TAS_PRIORITY_VARIABLE';
                            break;
                        case 'Starting Task':
                            $fieldName = 'TAS_START';
                            $param['value'] = strtoupper($param['value']);
                            break;
                    }
                    $task[$fieldName] = $param['value'];
                    print_r($task);
                    $oTask->update($task);

                    break;
            }
        } catch (Exception $e) {
            $result->sucess = false;
            $result->msg = $e->getMessage();
        }

        print G::json_encode($result);
    }

    public function getCategoriesList()
    {
        require_once "classes/model/ProcessCategory.php";

        $processCategory = new ProcessCategory();
        $defaultOption = Array();
        $defaultOption[] = Array('CATEGORY_UID' => '', 'CATEGORY_NAME' => '');

        $response->rows = array_merge($defaultOption, $processCategory->getAll('array'));

        print G::json_encode($response);
    }

    public function getCaledarList()
    {
        $calendar = new CalendarDefinition();
        $calendarObj = $calendar->getCalendarList(true, true);
        $calendarObj['array'][0] = Array('CALENDAR_UID' => '', 'CALENDAR_NAME' => '');

        $response->rows = $calendarObj['array'];

        print G::json_encode($response);
    }

    public function getPMVariables($param)
    {
        $oProcessMap = new ProcessMap(new DBConnection());
        $response->rows = getDynaformsVars($param['PRO_UID']);
        foreach ($response->rows as $i => $var) {
            $response->rows[$i]['sName'] = "@@{$var['sName']}";
        }
        print G::json_encode($response);
    }
}

