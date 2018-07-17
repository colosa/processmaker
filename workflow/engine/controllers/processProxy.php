<?php

class ProcessProxy extends HttpProxyController
{

    public function call ($name)
    {
        global $RBAC;
        $RBAC->allows(basename(__FILE__), $name);
        parent::call($name);
    }
    /**
     * get Process Categories List with defailt value (empty option) and -All- aoption
     */
    public function categoriesList ()
    {
        $data = $this->getCategoriesList();
        $defaultOption[] = Array ('CATEGORY_UID' => '<reset>','CATEGORY_NAME' => G::LoadTranslation( 'ID_ALL' ));

        return array_merge( $defaultOption, $data );
    }

    /**
     * Get Process categories list with defailt value (empty option)
     *
     * @return array
     */
    public function getCategoriesList ()
    {
        require_once "classes/model/ProcessCategory.php";

        $processCategory = new ProcessCategory();
        $defaultOption = Array ();
        $defaultOption[] = Array ('CATEGORY_UID' => '','CATEGORY_NAME' => G::LoadTranslation( 'ID_PROCESS_NO_CATEGORY' ));

        return array_merge( $defaultOption, $processCategory->getAll( 'array' ) );
    }

    /**
     * Save new process
     *
     * @param object $httpData
     */
    public function saveProcess ($httpData)
    {
        $projectType = empty($_GET['type']) ? 'classicProject' : $_GET['type'];

        try {
            $data = (array) $httpData;
            $data['PRO_CREATE_USER'] = $_SESSION['USER_LOGGED'];

            if ($projectType == "classicProject") {
                $project = new ProcessMaker\Project\Workflow($data);
            } else {
                $project = new ProcessMaker\Project\Adapter\WorkflowBpmn($data);
            }

            $sProUid = $project->getUid();

            $this->success = true;
            $this->PRO_UID = $sProUid;
            $this->msg = G::LoadTranslation( 'ID_CREATE_PROCESS_SUCCESS' );
        } catch (Exception $e) {
            $this->success = false;
            $this->msg = $e->getMessage();
            $this->trace = $e->getTraceAsString();
        }
    }

    /**
     * Change process status
     */
    public function changeStatus ()
    {
        $ids = explode( ',', $_REQUEST['UIDS'] );

        $oProcess = new Processes();
        if (count( $ids ) > 0) {
            foreach ($ids as $id) {
                $oProcess->changeStatus( htmlspecialchars($id) );
            }
        }
    }

    /**
     * Change process debug mode
     */
    public function changeDebugMode ()
    {
        $ids = explode( ',', $_REQUEST['UIDS'] );

        $oProcess = new Processes();
        if (count( $ids ) > 0) {
            foreach ($ids as $id) {
                $oProcess->changeDebugMode( $id );
            }
        }
    }

    /**
     * Get users list
     *
     * @param $params httpdata object
     */
    public function getUsers ($params)
    {
        require_once 'classes/model/Users.php';
        $search = isset( $params->search ) ? $params->search : null;
        return Users::getAll( $params->start, $params->limit, $search );
    }

    /**
     * Get groups list
     *
     * @param $params httpdata object
     */
    public function getGroups ($params)
    {
        require_once 'classes/model/Groupwf.php';
        $search = isset( $params->search ) ? $params->search : null;
        return Groupwf::getAll( $params->start, $params->limit, $search );
    }

    /**
     * Assign actors to task
     *
     * @param unknown_type $param
     */
    public function assignActorsTask ($param)
    {
        require_once 'classes/model/TaskUser.php';
        require_once 'classes/model/Task.php';
        $oTaskUser = new TaskUser();
        $UIDS = explode( ',', $param->UIDS );
        $TU_TYPE = $param->TU_TYPE;
        $TAS_UID = $param->TAS_UID;

        foreach ($UIDS as $UID) {
            if ($param->TU_RELATION == '1') {
                $res[] = $oTaskUser->create( array ('TAS_UID' => $TAS_UID,'USR_UID' => $UID,'TU_TYPE' => $TU_TYPE,'TU_RELATION' => 1) );
            } else {
                $res[] = $oTaskUser->create( array ('TAS_UID' => $TAS_UID,'USR_UID' => $UID,'TU_TYPE' => $TU_TYPE,'TU_RELATION' => 2) );
            }
        }
        $task = TaskPeer::retrieveByPk( $TAS_UID );

        $this->success = true;
        if (! in_array( '-1', $res )) {
            if (count( $UIDS ) == 1) {
                $this->msg = G::LoadTranslation( 'ID_ACTOR_ASSIGNED_SUCESSFULLY', SYS_LANG, Array ('tas_title' => $task->getTasTitle()) );
            } else {
                $this->msg = G::LoadTranslation( 'ID_ACTORS_ASSIGNED_SUCESSFULLY', SYS_LANG, Array (count( $UIDS ),$task->getTasTitle()) );
            }
        } else {
            if (count( $UIDS ) == 1) {
                $this->msg = G::LoadTranslation( 'ID_ACTOR_ALREADY_ASSIGNED', SYS_LANG, Array ($task->getTasTitle()) );
            } else {
                $this->msg = G::LoadTranslation( 'ID_SOME_ACTORS_ALREADY_ASSIGNED', SYS_LANG, Array ($task->getTasTitle()) );
            }
        }
    }

    /**
     * Remove Actors from Task
     *
     * @param $param
     */
    public function removeActorsTask ($param)
    {
        require_once 'classes/model/TaskUser.php';
        $oTaskUser = new TaskUser();
        $USR_UIDS = explode( ',', $param->USR_UID );
        $TU_RELATIONS = explode( ',', $param->TU_RELATION );
        $TU_TYPE = $param->TU_TYPE;

        foreach ($USR_UIDS as $i => $USR_UID) {
            if ($TU_RELATIONS[$i] == 1) {
                $oTaskUser->remove( $param->TAS_UID, $USR_UID, $TU_TYPE, 1 );
            } else {
                $oTaskUser->remove( $param->TAS_UID, $USR_UID, $TU_TYPE, 2 );
            }
        }

        $this->success = true;
        $this->msg = '';
    }

    /**
     * Get actors asssigned to task
     *
     * @param object $httpData{tas_uid, tu_type},
     */
    public function getActorsTask ($httpData)
    {
        require_once 'classes/model/TaskUser.php';
        $usersTaskList = Array ();
        $task = new TaskUser();
        $usersTask = $task->getUsersTask( $httpData->tas_uid, $httpData->tu_type );

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

        $this->data = $usersTaskList;
        $this->totalCount = $usersTask->totalCount;
    }

    /**
     * Get Process details
     *
     * @param object $httpData{PRO_UID}
     * @return array
     */
    public function getProcessDetail ($httpData)
    {
        $tasks = new Tasks();
        $PRO_UID = $httpData->PRO_UID;
        $process = ProcessPeer::retrieveByPk( $PRO_UID );
        $tasksList = $tasks->getAllTasks( $PRO_UID );

        $rootNode->id = $process->getProUid();
        $rootNode->type = 'process';
        $rootNode->typeLabel = G::LoadTranslation( 'ID_PROCESS' );
        $rootNode->text = $process->getProTitle();
        $rootNode->leaf = count( $tasksList ) > 0 ? false : true;
        $rootNode->iconCls = 'ss_sprite ss_application';
        $rootNode->expanded = true;
        foreach ($tasksList as $task) {
            $node = new stdClass();
            $node->id = $task['TAS_UID'];
            $node->type = 'task';
            $node->typeLabel = G::LoadTranslation( 'ID_TASK' );
            $node->text = $task['TAS_TITLE'];
            $node->iconCls = 'ss_sprite ss_layout';
            $node->leaf = true;
            $rootNode->children[] = $node;
        }

        $treeDetail[] = $rootNode;

        return $treeDetail;
    }

    /**
     * Get process and task properties
     *
     * @param object $httpData{type, UID}
     */
    public function getProperties ($httpData)
    {
        switch ($httpData->type) {
            case 'process':

                $oProcessMap = new ProcessMap( new DBConnection() );
                $process = $oProcessMap->editProcessNew( $httpData->UID );
                $category = ProcessCategoryPeer::retrieveByPk( $process['PRO_CATEGORY'] );
                $categoryName = is_object( $category ) ? $category->getCategoryName() : '';
                $calendar = CalendarDefinitionPeer::retrieveByPk( $process['PRO_CALENDAR'] );
                $calendarName = is_object( $calendar ) ? $calendar->getCalendarName() : '';

                $properties['Title'] = $process['PRO_TITLE'];
                $properties['Description'] = $process['PRO_DESCRIPTION'];
                $properties['Calendar'] = $calendarName;
                $properties['Category'] = $categoryName;
                $properties['Debug'] = $process['PRO_DEBUG'] == '1' ? true : false;

                $this->sucess = true;
                $this->prop = $properties;
                break;
            case 'task':
                require_once 'classes/model/Task.php';
                $task = new Task();
                $taskData = $task->load( $httpData->UID );

                $properties['Title'] = $taskData['TAS_TITLE'];
                $properties['Description'] = $taskData['TAS_DESCRIPTION'];
                $properties['Variable for case priority'] = $taskData['TAS_PRIORITY_VARIABLE'];
                $properties['Starting Task'] = $taskData['TAS_START'] == 'TRUE' ? true : false;

                $this->sucess = true;
                $this->prop = $properties;
                break;
        }
    }

    /**
     * Save process and task propeties
     *
     * @param object $httpData{UID, type, property, value}
     */
    public function saveProperties ($httpData)
    {
        switch ($httpData->type) {
            case 'process':

                $oProcessMap = new ProcessMap();
                $process['PRO_UID'] = $httpData->UID;

                switch ($httpData->property) {
                    case 'Title':
                        $fieldName = 'PRO_TITLE';
                        break;
                    case 'Description':
                        $fieldName = 'PRO_DESCRIPTION';
                        break;
                    case 'Debug':
                        $fieldName = 'PRO_DEBUG';
                        $httpData->value = $httpData->value == 'true' ? '1' : '0';
                        break;
                    case 'Category':
                        $fieldName = 'PRO_CATEGORY';
                        $category = ProcessCategory::loadByCategoryName( $httpData->value );
                        $httpData->value = $category['CATEGORY_UID'];
                        break;
                    case 'Calendar':
                        $fieldName = 'PRO_CALENDAR';
                        $calendar = CalendarDefinition::loadByCalendarName( $httpData->value );

                        $calendarObj = new Calendar();
                        $calendarObj->assignCalendarTo( $process['PRO_UID'], $calendar['CALENDAR_UID'], 'PROCESS' );
                        break;
                }

                if ($fieldName != 'PRO_CALENDAR') {
                    $process[$fieldName] = $httpData->value;
                    $oProcessMap->updateProcess( $process );
                }
                break;
            case 'task':
                require_once 'classes/model/Task.php';
                $oTask = new Task();
                $task['TAS_UID'] = $httpData->UID;

                switch ($httpData->property) {
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
                        $httpData->value = strtoupper( $httpData->value );
                        break;
                }
                $task[$fieldName] = $httpData->value;

                $oTask->update( $task );

                break;
        }

        $this->sucess = true;
    }

    /**
     * get calendar list
     */
    public function getCaledarList ()
    {
        $calendar = new CalendarDefinition();
        $calendarObj = $calendar->getCalendarList( true, true );
        $calendarObj['array'][0] = Array ('CALENDAR_UID' => '','CALENDAR_NAME' => ''
        );

        $this->rows = $calendarObj['array'];
    }

    /**
     * Get PM Variables
     *
     * @param $param{PRO_UID}
     */
    public function getPMVariables ($param)
    {
        $oProcessMap = new ProcessMap( new DBConnection() );
        $rows = getDynaformsVars( $param->PRO_UID );
        foreach ($rows as $i => $var) {
            $rows[$i]['sName'] = "@@{$var['sName']}";
        }
        $this->rows = $rows;
    }

    /**
     * Generate BPMN (New record is generated)
     *
     * return void
     */
    public function generateBpmn()
    {
        try {
            $processUid = $_POST["processUid"];

            $workflowBpmn = new \ProcessMaker\Project\Adapter\WorkflowBpmn();

            $projectUid = $workflowBpmn->generateBpmn($processUid, "processUid", $_SESSION["USER_LOGGED"]);

            $this->status = "OK";
            $this->projectUid = $projectUid;
        } catch (Exception $e) {
            $this->status  = "ERROR";
            $this->message = $e->getMessage();
        }
    }
}

