<?php

namespace ProcessMaker\BusinessModel;

use ProcessMaker\BusinessModel\Lists;
use G;
use Criteria;
use UsersPeer;
use AppDelegationPeer;
use AppDelayPeer;
use ProcessMaker\Core\System;
use ProcessMaker\Util\DateTime;
use PmLicenseManager;
use Bootstrap;
use ProcessPeer;
use BpmnProjectPeer;
use Propel;
use ResultSet;
use Process;
use Cases;
use ProcessMaker\BusinessModel\Task as BusinessModelTask;
use ProcessMaker\Services\Api\Project\Activity\Step as ActivityStep;
use Exception;
use AppCacheView;
use database;
use TaskPeer;
use StepPeer;
use GulliverBasePeer;
use AppDocument;
use Users;
use ProcessMaker\BusinessModel\User as BusinessModelUser;
use ProcessMaker\BusinessModel\Cases as BusinessModelCases;
use ProcessMaker\Core\RoutingScreen;
use Configurations;
use InputDocument;
use AppFolder;
use PMmemcached;
use RBAC;

class Light
{

    /**
     * Method get list start case
     *
     * @param $userId User id
     * @return array
     * @throws Exception
     */
    public function getProcessListStartCase($userId)
    {
        $response = null;
        try {
            // getting bpmn projects
            $c = new Criteria('workflow');
            $c->addSelectColumn(BpmnProjectPeer::PRJ_UID);
            $ds = ProcessPeer::doSelectRS($c, Propel::getDbConnection('workflow_ro'));
            $ds->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $bpmnProjects = array();

            while ($ds->next()) {
                $row = $ds->getRow();
                $bpmnProjects[] = $row['PRJ_UID'];
            }

            $oProcess = new Process();
            $oCase = new Cases();

            //Get ProcessStatistics Info
            $start = 0;
            $limit = '';
            $proData = $oProcess->getAllProcesses($start, $limit, null, null, false, true);

            $processListInitial = $oCase->getStartCasesPerType($userId, 'category');

            $processList = array();
            foreach ($processListInitial as $key => $procInfo) {
                if (isset($procInfo['pro_uid'])) {
                    if (trim($procInfo['cat']) == "") {
                        $procInfo['cat'] = "_OTHER_";
                    }
                    $processList[$procInfo['catname']][$procInfo['value']] = $procInfo;
                }
            }

            ksort($processList);
            foreach ($processList as $key => $processInfo) {
                ksort($processList[$key]);
            }

            foreach ($proData as $key => $proInfo) {
                $proData[$proInfo['PRO_UID']] = $proInfo;
            }

            $task = new BusinessModelTask();
            $task->setFormatFieldNameInUppercase(false);
            $task->setArrayParamException(array("taskUid" => "act_uid", "stepUid" => "step_uid"));

            $step = new ActivityStep();
            $response = array();
            foreach ($processList as $key => $processInfo) {
                $tempTreeChildren = array();
                foreach ($processList[$key] as $keyChild => $processInfoChild) {
                    if (in_array($processInfoChild['pro_uid'], $bpmnProjects)) {
                        $tempTreeChild['text'] = $keyChild; //ellipsis ( $keyChild, 50 );
                        $tempTreeChild['processId'] = $processInfoChild['pro_uid'];
                        $tempTreeChild['taskId'] = $processInfoChild['uid'];
                        list($tempTreeChild['offlineEnabled'], $tempTreeChild['autoRoot']) = $task->getColumnValues($processInfoChild['pro_uid'],
                            $processInfoChild['uid'], array('TAS_OFFLINE', 'TAS_AUTO_ROOT'));
                        //Add process category
                        $tempTreeChild['categoryName'] = $processInfoChild['catname'];
                        $tempTreeChild['categoryId'] = $processInfoChild['cat'];
                        $forms = $task->getSteps($processInfoChild['uid']);
                        $newForm = array();
                        $c = 0;
                        foreach ($forms as $k => $form) {
                            if ($form['step_type_obj'] == "DYNAFORM") {
                                $dynaForm = \DynaformPeer::retrieveByPK($form['step_uid_obj']);

                                $newForm[$c]['formId'] = $form['step_uid_obj'];
                                $newForm[$c]['formUpdateDate'] = DateTime::convertUtcToIso8601($dynaForm->getDynUpdateDate());
                                $newForm[$c]['index'] = $c + 1;
                                $newForm[$c]['title'] = $form['obj_title'];
                                $newForm[$c]['description'] = $form['obj_description'];
                                $newForm[$c]['stepId'] = $form["step_uid"];
                                $newForm[$c]['stepUidObj'] = $form["step_uid_obj"];
                                $newForm[$c]['stepMode'] = $form['step_mode'];
                                $newForm[$c]['stepCondition'] = $form['step_condition'];
                                $newForm[$c]['stepPosition'] = $form['step_position'];
                                $trigger = $this->statusTriggers($step->doGetActivityStepTriggers($form["step_uid"],
                                    $tempTreeChild['taskId'], $tempTreeChild['processId']));
                                $newForm[$c]["triggers"] = $trigger;
                                $c++;
                            }
                        }
                        $tempTreeChild['forms'] = $newForm;
                        if (isset($proData[$processInfoChild['pro_uid']])) {
                            $tempTreeChildren[] = $tempTreeChild;
                        }
                    }
                }
                $response = array_merge($response, $tempTreeChildren);
            }
        } catch (Exception $e) {
            throw $e;
        }

        return $response;
    }

    /**
     * Get status trigger case
     * @param $triggers
     * @return array
     */
    public function statusTriggers($triggers)
    {
        $return = array("before" => false, "after" => false);
        foreach ($triggers as $trigger) {
            if ($trigger['st_type'] == "BEFORE") {
                $return["before"] = true;
            }
            if ($trigger['st_type'] == "AFTER") {
                $return["after"] = true;
            }
        }

        return $return;
    }

    /**
     * Get counters each type of list
     * @param $userId
     * @return array
     * @throws Exception
     */
    public function getCounterCase($userId)
    {
        try {
            $userUid = (isset($userId) && $userId != '') ? $userId : null;
            $oAppCache = new AppCacheView();

            $aTypes = Array();
            $aTypes['to_do'] = 'toDo';
            $aTypes['draft'] = 'draft';
            $aTypes['cancelled'] = 'cancelled';
            $aTypes['sent'] = 'participated';
            $aTypes['paused'] = 'paused';
            $aTypes['completed'] = 'completed';
            $aTypes['selfservice'] = 'unassigned';

            $aCount = $oAppCache->getAllCounters(array_keys($aTypes), $userUid);

            $response = Array();
            foreach ($aCount as $type => $count) {
                $response[$aTypes[$type]] = $count;
            }
        } catch (Exception $e) {
            throw $e;
        }

        return $response;
    }

    /**
     * @param $sAppUid
     * @return Criteria
     */
    public function getTransferHistoryCriteria($sAppUid)
    {
        $c = new Criteria('workflow');
        $c->addSelectColumn(UsersPeer::USR_FIRSTNAME);
        $c->addSelectColumn(UsersPeer::USR_LASTNAME);
        $c->addSelectColumn(AppDelegationPeer::DEL_DELEGATE_DATE);
        $c->addSelectColumn(AppDelegationPeer::PRO_UID);
        $c->addSelectColumn(AppDelegationPeer::TAS_UID);
        $c->addSelectColumn(AppDelegationPeer::APP_UID);
        $c->addSelectColumn(AppDelegationPeer::DEL_INDEX);
        ///-- $c->addAsColumn('USR_NAME', "CONCAT(USR_LASTNAME, ' ', USR_FIRSTNAME)");
        $sDataBase = 'database_' . strtolower(DB_ADAPTER);
        if (G::LoadSystemExist($sDataBase)) {

            $oDataBase = new database();
            $c->addAsColumn('USR_NAME', $oDataBase->concatString("USR_LASTNAME", "' '", "USR_FIRSTNAME"));
            $c->addAsColumn(
                'DEL_FINISH_DATE',
                $oDataBase->getCaseWhen("DEL_FINISH_DATE IS NULL", "'-'", AppDelegationPeer::DEL_FINISH_DATE)
            );
            $c->addAsColumn(
                'APP_TYPE', $oDataBase->getCaseWhen("DEL_FINISH_DATE IS NULL", "'IN_PROGRESS'", AppDelayPeer::APP_TYPE)
            );
        }
        $c->addSelectColumn(AppDelegationPeer::DEL_INIT_DATE);
        $c->addSelectColumn(AppDelayPeer::APP_ENABLE_ACTION_DATE);
        $c->addSelectColumn(AppDelayPeer::APP_DISABLE_ACTION_DATE);
        $c->addSelectColumn(TaskPeer::TAS_TITLE);
        //APP_DELEGATION LEFT JOIN USERS
        $c->addJoin(AppDelegationPeer::USR_UID, UsersPeer::USR_UID, Criteria::LEFT_JOIN);

        $del = \DBAdapter::getStringDelimiter();
        $app = array();
        $app[] = array(AppDelegationPeer::DEL_INDEX, AppDelayPeer::APP_DEL_INDEX);
        $app[] = array(AppDelegationPeer::APP_UID, AppDelayPeer::APP_UID);
        $c->addJoinMC($app, Criteria::LEFT_JOIN);

        //LEFT JOIN TASK TAS_TITLE
        $c->addJoin(AppDelegationPeer::TAS_UID, TaskPeer::TAS_UID, Criteria::LEFT_JOIN);

        //WHERE
        $c->add(AppDelegationPeer::APP_UID, $sAppUid);

        //ORDER BY
        $c->clearOrderByColumns();
        $c->addAscendingOrderByColumn(AppDelegationPeer::DEL_DELEGATE_DATE);

        return $c;
    }

    /**
     * GET history of case
     *
     * @param $app_uid
     * @return array
     * @throws Exception
     */
    public function getCasesListHistory($app_uid)
    {

        //global $G_PUBLISH;
        $c = $this->getTransferHistoryCriteria($app_uid);
        $aProcesses = Array();

        $rs = GulliverBasePeer::doSelectRs($c);
        $rs->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $rs->next();
        for ($j = 0; $j < $rs->getRecordCount(); $j++) {
            $result = $rs->getRow();
            $result["ID_HISTORY"] = $result["PRO_UID"] . '_' . $result["APP_UID"] . '_' . $result["TAS_UID"];
            $aProcesses[] = $result;
            $rs->next();
            $processUid = $result["PRO_UID"];
        }

        $process = new Process();

        $arrayProcessData = $process->load($processUid);

        $result = array();
        $result["processName"] = $arrayProcessData["PRO_TITLE"];
        $result['flow'] = $aProcesses;

        return $result;
    }

    /**
     * starting one case
     *
     * @param string $userId
     * @param string $proUid
     * @param string $taskUid
     * @return array
     * @throws Exception
     */
    public function startCase($userId = '', $proUid = '', $taskUid = '')
    {
        try {
            $oCase = new Cases();

            $aData = $oCase->startCase($taskUid, $userId);

            $user = new BusinessModelUser();
            $arrayUserData = $user->getUserRecordByPk($userId, ['$userUid' => '$userId']);

            $_SESSION['APPLICATION'] = $aData['APPLICATION'];
            $_SESSION['INDEX'] = $aData['INDEX'];
            $_SESSION['PROCESS'] = $aData['PROCESS'];
            $_SESSION['TASK'] = $taskUid;
            $_SESSION["USER_LOGGED"] = $userId;
            $_SESSION['USR_USERNAME'] = $arrayUserData['USR_USERNAME'];

            $aFields = $oCase->loadCase($aData['APPLICATION'], $aData['INDEX']);
            $oCase->updateCase($aData['APPLICATION'], $aFields);

            $response = array();
            $response['caseId'] = $aData['APPLICATION'];
            $response['caseIndex'] = $aData['INDEX'];
            $response['caseNumber'] = $aData['CASE_NUMBER'];

            //Log
            Bootstrap::registerMonolog('MobileCreateCase', 200, "Create case",
                ['application_uid' => $aData['APPLICATION'], 'usr_uid' => $userId], SYS_SYS, 'processmaker.log');
        } catch (Exception $e) {
            $response['status'] = 'failure';
            $response['message'] = $e->getMessage();
        }

        return $response;
    }

    public function lookinginforContentProcess($sproUid)
    {
        $oContent = new \Content();
        ///we are looking for a pro title for this process $sproUid
        $oCriteria = new Criteria('workflow');
        $oCriteria->add(ProcessPeer::PRO_UID, $sproUid);
        $oDataset = ProcessPeer::doSelectRS($oCriteria);
        $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $oDataset->next();
        $aRow = $oDataset->getRow();
        if (!is_array($aRow)) {
            $oC = new Criteria('workflow');
            $oC->addSelectColumn(TaskPeer::TAS_UID);
            $oC->addSelectColumn(TaskPeer::TAS_TITLE);
            $oC->add(TaskPeer::PRO_UID, $sproUid);
            $oDataset1 = TaskPeer::doSelectRS($oC);
            $oDataset1->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            while ($oDataset1->next()) {
                $aRow1 = $oDataset1->getRow();
                \Content::insertContent('TAS_TITLE', '', $aRow1['TAS_UID'], 'en', $aRow1['TAS_TITLE']);
            }
            $oC2 = new Criteria('workflow');
            $oC2->addSelectColumn(ProcessPeer::PRO_UID);
            $oC2->addSelectColumn(ProcessPeer::PRO_TITLE);
            $oC2->add(ProcessPeer::PRO_UID, $sproUid);
            $oDataset3 = ProcessPeer::doSelectRS($oC2);
            $oDataset3->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $oDataset3->next();
            $aRow3 = $oDataset3->getRow();
            \Content::insertContent('PRO_TITLE', '', $aRow3['PRO_UID'], 'en', $aRow3['PRO_TITLE']);
        }

        return 1;

    }

    /**
     * Execute Trigger case
     *
     */
    public function doExecuteTriggerCase($usr_uid, $prj_uid, $act_uid, $cas_uid, $step_uid, $type, $del_index = null)
    {
        $userData = $this->getUserData($usr_uid);
        $c = new Criteria();
        $c->clearSelectColumns();
        $c->addSelectColumn(StepPeer::STEP_UID);
        $c->addSelectColumn(StepPeer::STEP_UID_OBJ);
        $c->add(StepPeer::TAS_UID, $act_uid);
        $c->add(StepPeer::STEP_TYPE_OBJ, 'DYNAFORM');
        $c->add(StepPeer::STEP_UID, $step_uid);
        $rs = StepPeer::doSelectRS($c);
        $rs->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $rs->next();
        $row = $rs->getRow();
        $step_uid_obj = $row['STEP_UID_OBJ'];

        $oCase = new Cases();
        $Fields = $oCase->loadCase($cas_uid);
        $_SESSION["APPLICATION"] = $cas_uid;
        $_SESSION["PROCESS"] = $prj_uid;
        $_SESSION["TASK"] = $act_uid;
        $_SESSION["USER_LOGGED"] = $usr_uid;
        $_SESSION["USR_USERNAME"] = $userData['firstName'];
        $_SESSION["INDEX"] = $Fields["DEL_INDEX"] = $del_index !== null ? $del_index : \AppDelegation::getCurrentIndex($cas_uid);
        $Fields['APP_DATA'] = array_merge($Fields['APP_DATA'], G::getSystemConstants());
        $triggers = $oCase->loadTriggers($act_uid, 'DYNAFORM', $step_uid_obj, strtoupper($type));
        if ($triggers) {
            $Fields['APP_DATA'] = $oCase->ExecuteTriggers($act_uid, 'DYNAFORM', $step_uid_obj, strtoupper($type),
                $Fields['APP_DATA']);
        }
        $Fields['TAS_UID'] = $act_uid;
        $Fields['CURRENT_DYNAFORM'] = $step_uid_obj;
        $Fields['USER_UID'] = $usr_uid;
        $Fields['PRO_UID'] = $prj_uid;
        $oCase->updateCase($cas_uid, $Fields);
        $response = array('status' => 'ok');

        return $response;
    }

    /**
     * Return Informaction User for derivate
     * assignment Users
     *
     * return array Return an array with Task Case
     */
    public function getPrepareInformation($usr_uid, $tas_uid, $app_uid, $del_index = null)
    {
        try {
            $oCase = new Cases();
            $Fields = $oCase->loadCase($app_uid);
            $_SESSION["APPLICATION"] = $app_uid;
            $_SESSION["PROCESS"] = $Fields['PRO_UID'];
            $_SESSION["TASK"] = $tas_uid;
            $_SESSION["INDEX"] = $del_index;
            $_SESSION["USER_LOGGED"] = $usr_uid;
            $_SESSION["USR_USERNAME"] = isset($Fields['APP_DATA']['USR_USERNAME']) ? $Fields['APP_DATA']['USR_USERNAME'] : '';

            $triggers = $oCase->loadTriggers($tas_uid, 'ASSIGN_TASK', '-1', 'BEFORE');
            if (isset($triggers)) {
                $Fields['APP_DATA'] = array_merge($Fields['APP_DATA'], G::getSystemConstants());
                $Fields['APP_DATA'] = $oCase->ExecuteTriggers($tas_uid, 'DYNAFORM', '-1', 'BEFORE',
                    $Fields['APP_DATA']);
                $oCase->updateCase($app_uid, $Fields);
            }
            $oDerivation = new \Derivation();
            $aData = array();
            $aData['APP_UID'] = $app_uid;
            $aData['DEL_INDEX'] = $del_index;
            $aData['USER_UID'] = $usr_uid;
            $oRoute = new RoutingScreen();
            $derive = $oRoute->prepareRoutingScreen($aData);
            $response = array();
            foreach ($derive as $sKey => &$aValues) {
                $sPriority = ''; //set priority value
                if ($derive[$sKey]['NEXT_TASK']['TAS_PRIORITY_VARIABLE'] != '') {
                    //TO DO: review this type of assignment
                    if (isset($aData['APP_DATA'][str_replace('@@', '',
                            $derive[$sKey]['NEXT_TASK']['TAS_PRIORITY_VARIABLE'])])) {
                        $sPriority = $aData['APP_DATA'][str_replace('@@', '',
                            $derive[$sKey]['NEXT_TASK']['TAS_PRIORITY_VARIABLE'])];
                    }
                } //set priority value

                $taskType = (isset($aValues["NEXT_TASK"]["TAS_TYPE"])) ? $aValues["NEXT_TASK"]["TAS_TYPE"] : false;
                $taskMessage = "";
                switch ($taskType) {
                    case "SCRIPT-TASK":
                        $taskMessage = G::LoadTranslation("ID_ROUTE_TO_TASK_SCRIPT_TASK");
                        break;
                    case "INTERMEDIATE-CATCH-TIMER-EVENT":
                        $taskMessage = G::LoadTranslation("ID_ROUTE_TO_TASK_INTERMEDIATE_CATCH_TIMER_EVENT");
                        break;
                }

                switch ($aValues['NEXT_TASK']['TAS_ASSIGN_TYPE']) {
                    case 'EVALUATE':
                    case 'REPORT_TO':
                    case 'BALANCED':
                    case 'SELF_SERVICE':
                        $taskAss = array();
                        $taskAss['taskId'] = $aValues['NEXT_TASK']['TAS_UID'];
                        $taskAss['taskName'] = $aValues['NEXT_TASK']['TAS_TITLE'];
                        $taskAss['taskAssignType'] = $aValues['NEXT_TASK']['TAS_ASSIGN_TYPE'];
                        $taskAss['taskDefProcCode'] = $aValues['NEXT_TASK']['TAS_DEF_PROC_CODE'];
                        $taskAss['delPriority'] = isset($aValues['NEXT_TASK']['DEL_PRIORITY']) ? $aValues['NEXT_TASK']['DEL_PRIORITY'] : "";
                        $taskAss['taskParent'] = $aValues['NEXT_TASK']['TAS_PARENT'];
                        $taskAss['taskMessage'] = $taskType ? $taskMessage : "";
                        $taskAss['sourceUid'] = $aValues['SOURCE_UID'];
                        $users = array();
                        $users['userId'] = $derive[$sKey]['NEXT_TASK']['USER_ASSIGNED']['USR_UID'];
                        $users['userFullName'] = strip_tags($derive[$sKey]['NEXT_TASK']['USER_ASSIGNED']['USR_FULLNAME']);
                        $taskAss['users'][] = $users;
                        $response[] = $taskAss;
                        break;
                    case 'MANUAL':
                    case "MULTIPLE_INSTANCE":
                    case "MULTIPLE_INSTANCE_VALUE_BASED":
                        $manual = array();
                        $manual['taskId'] = $aValues['NEXT_TASK']['TAS_UID'];
                        $manual['taskName'] = $aValues['NEXT_TASK']['TAS_TITLE'];
                        $manual['taskAssignType'] = $aValues['NEXT_TASK']['TAS_ASSIGN_TYPE'];
                        $manual['taskDefProcCode'] = $aValues['NEXT_TASK']['TAS_DEF_PROC_CODE'];
                        $manual['delPriority'] = isset($aValues['NEXT_TASK']['DEL_PRIORITY']) ? $aValues['NEXT_TASK']['DEL_PRIORITY'] : "";
                        $manual['taskParent'] = $aValues['NEXT_TASK']['TAS_PARENT'];
                        $manual['taskMessage'] = $taskType ? $taskMessage : "";
                        $manual['sourceUid'] = $aValues['SOURCE_UID'];
                        $Aux = array();
                        foreach ($aValues['NEXT_TASK']['USER_ASSIGNED'] as $aUser) {
                            $Aux[$aUser['USR_UID']] = $aUser['USR_FULLNAME'];
                        }
                        asort($Aux);
                        $users = array();
                        foreach ($Aux as $id => $fullname) {
                            $user['userId'] = $id;
                            $user['userFullName'] = $fullname;
                            $users[] = $user;
                        }
                        $manual['users'] = $users;
                        $response[] = $manual;
                        break;
                    case '': //when this task is the Finish process
                    case 'nobody':
                        $userFields = $oDerivation->getUsersFullNameFromArray($derive[$sKey]['USER_UID']);
                        $taskAss = array();
                        $taskAss['routeFinishFlag'] = true;
                        $user['userId'] = $derive[$sKey]['USER_UID'];
                        $user['userFullName'] = $userFields['USR_FULLNAME'];
                        $taskAss['users'][] = $user;
                        $response[] = $taskAss;
                        break;
                }
            }

            if (empty($response)) {
                throw new Exception(G::LoadTranslation("ID_NO_DERIVATION_RULE"));
            }
        } catch (Exception $e) {
            throw $e;
        }

        return $response;
    }

    /**
     * Route Case
     *
     * @param string $applicationUid Unique id of Case
     * @param string $userUid Unique id of User
     * @param string $delIndex
     * @param array $tasks
     * @param string $bExecuteTriggersBeforeAssignment
     *
     * return array Return an array with Task Case
     */
    public function updateRouteCase($applicationUid, $userUid, $delIndex, $tasks)
    {
        try {
            if (!$delIndex) {
                $delIndex = \AppDelegation::getCurrentIndex($applicationUid);
            }

            $ws = new \WsBase();
            $fields = $ws->derivateCase($userUid, $applicationUid, $delIndex, $bExecuteTriggersBeforeAssignment = false,
                $tasks);
            $array = json_decode(json_encode($fields), true);
            $array['message'] = trim(strip_tags($array['message']));
            if ($array ["status_code"] != 0) {
                throw (new Exception($array ["message"]));
            } else {
                unset($array['status_code']);
                unset($array['message']);
                unset($array['timestamp']);
            }

            //Log
            Bootstrap::registerMonolog('MobileRouteCase', 200, 'Route case',
                ['application_uid' => $applicationUid, 'usr_uid' => $userUid], SYS_SYS, 'processmaker.log');
        } catch (Exception $e) {
            throw $e;
        }

        return $fields;
    }

    /**
     * Get user Data
     *
     * @param string $applicationUid Unique id of Case
     * @param string $userUid Unique id of User
     * @param string $delIndex
     * @param string $bExecuteTriggersBeforeAssignment
     *
     * return array Return an array with Task Case
     */
    public function getUserData($userUid)
    {
        try {
            $direction = PATH_IMAGES_ENVIRONMENT_USERS . $userUid . ".gif";
            if (!file_exists($direction)) {
                $direction = PATH_HOME . 'public_html/images/user.gif';
            }
            $gestor = fopen($direction, "r");
            $contenido = fread($gestor, filesize($direction));
            fclose($gestor);
            $oUser = new \Users();
            $aUserLog = $oUser->loadDetailed($userUid);
            $response['userId'] = $aUserLog['USR_UID'];
            $response['userName'] = $aUserLog['USR_USERNAME'];
            $response['firstName'] = $aUserLog['USR_FIRSTNAME'];
            $response['lastName'] = $aUserLog['USR_LASTNAME'];
            $response['fullName'] = $aUserLog['USR_FULLNAME'];
            $response['email'] = $aUserLog['USR_EMAIL'];
            $response['userRole'] = $aUserLog['USR_ROLE_NAME'];
            $response['userPhone'] = $aUserLog['USR_PHONE'];
            $response['updateDate'] = $aUserLog['USR_UPDATE_DATE'];
            $response['userPhoto'] = base64_encode($contenido);
        } catch (Exception $e) {
            throw $e;
        }

        return $response;
    }

    /**
     * Download files and resize dimensions in file type image
     * if not type image return content file
     *
     * return array Return an array with Task Case
     */
    public function downloadFile($app_uid, $request_data)
    {
        try {
            $oAppDocument = new AppDocument();
            $arrayFiles = array();
            foreach ($request_data as $key => $fileData) {
                if (!isset($fileData['version'])) {
                    //Load last version of the document
                    $docVersion = $oAppDocument->getLastAppDocVersion($fileData['fileId']);
                } else {
                    $docVersion = $fileData['version'];
                }
                $oAppDocument->Fields = $oAppDocument->load($fileData['fileId'], $docVersion);

                $sAppDocUid = $oAppDocument->getAppDocUid();
                $iDocVersion = $oAppDocument->getDocVersion();
                $info = pathinfo($oAppDocument->getAppDocFilename());
                $ext = (isset($info['extension']) ? $info['extension'] : '');//BUG fix: must handle files without any extension

                //$app_uid = G::getPathFromUID($oAppDocument->Fields['APP_UID']);
                $file = G::getPathFromFileUID($oAppDocument->Fields['APP_UID'], $sAppDocUid);

                $realPath = PATH_DOCUMENT . G::getPathFromUID($app_uid) . '/' . $file[0] . $file[1] . '_' . $iDocVersion . '.' . $ext;
                $realPath1 = PATH_DOCUMENT . G::getPathFromUID($app_uid) . '/' . $file[0] . $file[1] . '.' . $ext;

                $width = isset($fileData['width']) ? $fileData['width'] : null;
                $height = isset($fileData['height']) ? $fileData['height'] : null;
                if (file_exists($realPath)) {
                    switch ($ext) {
                        case 'jpg':
                        case 'jpeg':
                        case 'gif':
                        case 'png':
                            $arrayFiles[$key]['fileId'] = $fileData['fileId'];
                            $arrayFiles[$key]['fileContent'] = base64_encode($this->imagesThumbnails($realPath, $ext,
                                $width, $height));
                            break;
                        default:
                            $fileTmp = fopen($realPath, "r");
                            $content = fread($fileTmp, filesize($realPath));
                            $arrayFiles[$key]['fileId'] = $fileData['fileId'];
                            $arrayFiles[$key]['fileContent'] = base64_encode($content);
                            fclose($fileTmp);
                            break;
                    }
                } elseif (file_exists($realPath1)) {
                    switch ($ext) {
                        case 'jpg':
                        case 'jpeg':
                        case 'gif':
                        case 'png':
                            $arrayFiles[$key]['fileId'] = $fileData['fileId'];
                            $arrayFiles[$key]['fileContent'] = $this->imagesThumbnails($realPath1, $ext, $width,
                                $height);
                            break;
                        default:
                            $fileTmp = fopen($realPath, "r");
                            $content = fread($fileTmp, filesize($realPath));
                            $arrayFiles[$key]['fileId'] = $fileData['fileId'];
                            $arrayFiles[$key]['fileContent'] = base64_encode($content);
                            fclose($fileTmp);
                            break;
                    }
                }
            }
        } catch (Exception $e) {
            throw $e;
        }

        return $arrayFiles;
    }

    /**
     * resize image if send width or height
     *
     * @param $path
     * @param $extensions
     * @param null $newWidth
     * @param null $newHeight
     * @return string
     */
    public function imagesThumbnails($path, $extensions, $newWidth = null, $newHeight = null)
    {
        switch ($extensions) {
            case 'jpg':
            case 'jpeg':
                ini_set('gd.jpeg_ignore_warning', 1);
                error_reporting(0);
                $imgTmp = @imagecreatefromjpeg($path);
                break;
            case 'gif':
                $imgTmp = imagecreatefromgif($path);
                break;
            case 'png':
                $imgTmp = imagecreatefrompng($path);
                break;
        }

        $width = imagesx($imgTmp);
        $height = imagesy($imgTmp);

        $ratio = $width / $height;

        $isThumbnails = false;
        if (isset($newWidth) && !isset($newHeight)) {
            $newwidth = $newWidth;
            $newheight = round($newwidth / $ratio);
            $isThumbnails = true;
        } elseif (!isset($newWidth) && isset($newHeight)) {
            $newheight = $newHeight;
            $newwidth = round($newheight / $ratio);
            $isThumbnails = true;
        } elseif (isset($newWidth) && isset($newHeight)) {
            $newwidth = $newWidth;
            $newheight = $newHeight;
            $isThumbnails = true;
        }

        $thumb = $imgTmp;
        if ($isThumbnails) {
            $thumb = imagecreatetruecolor($newwidth, $newheight);
            imagecopyresampled($thumb, $imgTmp, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
        }
        ob_start();
        switch ($extensions) {
            case 'jpg':
            case 'jpeg':
                imagejpeg($thumb);
                break;
            case 'gif':
                imagegif($thumb);
                break;
            case 'png':
                imagepng($thumb);
                break;
        }
        $image = ob_get_clean();
        imagedestroy($thumb);

        return $image;
    }

    public function logout($oauthAccessTokenId, $refresh)
    {
        $aFields = array();

        if (!isset($_GET['u'])) {
            $aFields['URL'] = '';
        } else {
            $aFields['URL'] = htmlspecialchars(addslashes(stripslashes(strip_tags(trim(urldecode($_GET['u']))))));
        }

        if (!isset($_SESSION['G_MESSAGE'])) {
            $_SESSION['G_MESSAGE'] = '';
        }

        if (!isset($_SESSION['G_MESSAGE_TYPE'])) {
            $_SESSION['G_MESSAGE_TYPE'] = '';
        }

        $msg = $_SESSION['G_MESSAGE'];
        $msgType = $_SESSION['G_MESSAGE_TYPE'];

        if (!isset($_SESSION['FAILED_LOGINS'])) {
            $_SESSION['FAILED_LOGINS'] = 0;
            $_SESSION["USERNAME_PREVIOUS1"] = "";
            $_SESSION["USERNAME_PREVIOUS2"] = "";
        }

        $sFailedLogins = $_SESSION['FAILED_LOGINS'];
        $usernamePrevious1 = $_SESSION["USERNAME_PREVIOUS1"];
        $usernamePrevious2 = $_SESSION["USERNAME_PREVIOUS2"];

        $aFields['LOGIN_VERIFY_MSG'] = G::loadTranslation('LOGIN_VERIFY_MSG');

        //start new session
        @session_destroy();
        session_start();
        session_regenerate_id();

        setcookie("workspaceSkin", SYS_SKIN, time() + (24 * 60 * 60), "/sys" . SYS_SYS, null, false, true);

        if (strlen($msg) > 0) {
            $_SESSION['G_MESSAGE'] = $msg;
        }
        if (strlen($msgType) > 0) {
            $_SESSION['G_MESSAGE_TYPE'] = $msgType;
        }

        $_SESSION['FAILED_LOGINS'] = $sFailedLogins;
        $_SESSION["USERNAME_PREVIOUS1"] = $usernamePrevious1;
        $_SESSION["USERNAME_PREVIOUS2"] = $usernamePrevious2;

        /*----------------------------------********---------------------------------*/

        try {
            $oatoken = new \OauthAccessTokens();
            $result = $oatoken->remove($oauthAccessTokenId);

            $response["status"] = "OK";
        } catch (Exception $e) {
            $response["status"] = "ERROR";
            $response["message"] = $e->getMessage();
        }

        return $response;

    }

    /**
     * Get information for status paused and participated or other status
     *
     * @param $userUid
     * @param $type
     * @param $app_uid
     * @throws Exception
     */
    public function getInformation($userUid, $type, $app_uid)
    {
        $response = array();
        switch ($type) {
            case 'unassigned':
            case 'paused':
            case 'participated':
                $oCase = new Cases();
                $iDelIndex = $oCase->getCurrentDelegationCase($app_uid);
                $aFields = $oCase->loadCase($app_uid, $iDelIndex);
                $response = $this->getInfoResume($userUid, $aFields, $type);
                break;
            case 'lastopenindex':
                //Get the last participate from a user
                $oNewCase = new BusinessModelCases();
                $iDelIndex = $oNewCase->getLastParticipatedByUser($app_uid, $userUid, 'OPEN');
                $oCase = new Cases();
                $aFields = $oCase->loadCase($app_uid, $iDelIndex);
                $aFields['DEL_INDEX'] = $iDelIndex === 0 ? '' : $iDelIndex;
                $aFields['USR_UID'] = $userUid;
                $response = $this->getInfoResume($userUid, $aFields, $type);
                break;
        }

        return $response;
    }

    /**
     * view in html response for status
     *
     * @param $userUid
     * @param $Fields
     * @param $type
     * @throws Exception
     */
    public function getInfoResume($userUid, $Fields, $type)
    {
        /* Prepare page before to show */
        $objProc = new Process();
        $aProc = $objProc->load($Fields['PRO_UID']);
        $Fields['PRO_TITLE'] = $aProc['PRO_TITLE'];

        $objTask = new \Task();

        if (isset($_SESSION['ACTION']) && ($_SESSION['ACTION'] == 'jump')) {
            $task = explode('|', $Fields['TAS_UID']);
            $Fields['TAS_TITLE'] = '';

            for ($i = 0; $i < sizeof($task) - 1; $i++) {
                $aTask = $objTask->load($task[$i]);
                $Fields['TAS_TITLE'][] = $aTask['TAS_TITLE'];
            }

            $Fields['TAS_TITLE'] = implode(" - ", array_values($Fields['TAS_TITLE']));
        } elseif (isset($Fields['TAS_UID']) && !empty($Fields['TAS_UID'])) {
            $aTask = $objTask->load($Fields['TAS_UID']);
            $Fields['TAS_TITLE'] = $aTask['TAS_TITLE'];
        }

        return $Fields;
    }

    /**
     * First step for upload file
     * create uid app_document for upload file
     *
     * @param string $userUid
     * @param string $appUid
     * @param array $requestData
     * @return array $response
     * @throws Exception
     */
    public function postUidUploadFiles($userUid, $appUid, $requestData)
    {
        $response = array();
        if (is_array($requestData)) {
            $config = new Configurations();
            $confEnvSetting = $config->getFormats();
            $user = new Users();
            foreach ($requestData as $k => $file) {
                $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                if (Bootstrap::getDisablePhpUploadExecution() === 1 && $ext === 'php') {
                    $message = G::LoadTranslation('THE_UPLOAD_OF_PHP_FILES_WAS_DISABLED');
                    Bootstrap::registerMonologPhpUploadExecution('phpUpload', 550, $message, $file['name']);
                    $response[$k]['error'] = array(
                        "code" => "400",
                        "message" => $message
                    );
                    continue;
                }
                $cases = new Cases();
                $delIndex = $cases->getCurrentDelegation($appUid, $userUid);
                $docUid = !empty($file['docUid']) ? $file['docUid'] : -1;
                $folderId = '';
                if ($docUid !== -1) {
                    $inputDocument = new InputDocument();
                    $aInputDocumentData = $inputDocument->load($docUid);
                    $appFolder = new AppFolder();
                    $folderId = $appFolder->createFromPath($aInputDocumentData["INP_DOC_DESTINATION_PATH"], $appUid);
                }
                $appDocType = !empty($file['appDocType']) ? $file['appDocType'] : "ATTACHED";
                $fieldName = !empty($file['fieldName']) ? $file['fieldName'] : null;
                $fieldsInput = array(
                    "APP_UID" => $appUid,
                    "DEL_INDEX" => $delIndex,
                    "USR_UID" => $userUid,
                    "DOC_UID" => $docUid,
                    "APP_DOC_TYPE" => $appDocType,
                    "APP_DOC_CREATE_DATE" => date("Y-m-d H:i:s"),
                    "APP_DOC_COMMENT" => "",
                    "APP_DOC_TITLE" => "",
                    "APP_DOC_FILENAME" => $file['name'],
                    "APP_DOC_FIELDNAME" => $fieldName,
                    "FOLDER_UID" => $folderId
                );
                //We will to create a new version related to the appDocUid
                if (isset($file['appDocUid'])) {
                    $fieldsInput['APP_DOC_UID'] = $file['appDocUid'];
                }
                $appDocument = new AppDocument();
                $appDocument->create($fieldsInput);
                //todo, we need to  uniform the response format with camelCase
                $response[$k]['appDocUid'] = $appDocUid = $appDocument->getAppDocUid();
                $response[$k]['docVersion'] = $docVersion = $appDocument->getDocVersion();
                $response[$k]['appDocFilename'] = $appDocument->getAppDocFilename();
                $response[$k]['appDocCreateDate'] = $appDocument->getAppDocCreateDate();
                $response[$k]['appDocType'] = $appDocument->getAppDocType();
                $response[$k]['appDocIndex'] = $appDocument->getAppDocIndex();
                //todo, we use this *** in others endpoint for mark that user not exist, but we need to change
                $userInfo = '***';
                if ($userUid !== '-1') {
                    $arrayUserData = $user->load($userUid);
                    $userInfo = $config->usersNameFormatBySetParameters($confEnvSetting["format"],
                        $arrayUserData["USR_USERNAME"], $arrayUserData["USR_FIRSTNAME"],
                        $arrayUserData["USR_LASTNAME"]);
                }
                $response[$k]['appDocCreateUser'] = $userInfo;
            }
        }

        return $response;
    }

    /**
     * second step for upload file
     * upload file in foler app_uid
     *
     * @param $userUid
     * @param $Fields
     * @param $type
     * @throws Exception
     */
    public function documentUploadFiles($userUid, $app_uid, $app_doc_uid, $request_data)
    {
        $response = array("status" => "fail");
        if (isset($_FILES["form"]["name"]) && count($_FILES["form"]["name"]) > 0) {
            $arrayField = array();
            $arrayFileName = array();
            $arrayFileTmpName = array();
            $arrayFileError = array();
            $i = 0;

            foreach ($_FILES["form"]["name"] as $fieldIndex => $fieldValue) {
                if (is_array($fieldValue)) {
                    foreach ($fieldValue as $index => $value) {
                        if (is_array($value)) {
                            foreach ($value as $grdFieldIndex => $grdFieldValue) {
                                $arrayField[$i]["grdName"] = $fieldIndex;
                                $arrayField[$i]["grdFieldName"] = $grdFieldIndex;
                                $arrayField[$i]["index"] = $index;

                                $arrayFileName[$i] = $_FILES["form"]["name"][$fieldIndex][$index][$grdFieldIndex];
                                $arrayFileTmpName[$i] = $_FILES["form"]["tmp_name"][$fieldIndex][$index][$grdFieldIndex];
                                $arrayFileError[$i] = $_FILES["form"]["error"][$fieldIndex][$index][$grdFieldIndex];
                                $i = $i + 1;
                            }
                        }
                    }
                } else {
                    $arrayField[$i] = $fieldIndex;

                    $arrayFileName[$i] = $_FILES["form"]["name"][$fieldIndex];
                    $arrayFileTmpName[$i] = $_FILES["form"]["tmp_name"][$fieldIndex];
                    $arrayFileError[$i] = $_FILES["form"]["error"][$fieldIndex];
                    $i = $i + 1;
                }
            }
            if (count($arrayField) > 0) {
                for ($i = 0; $i <= count($arrayField) - 1; $i++) {
                    if ($arrayFileError[$i] == 0) {
                        $indocUid = null;
                        $fieldName = null;
                        $fileSizeByField = 0;

                        $oAppDocument = new AppDocument();
                        $aAux = $oAppDocument->load($app_doc_uid);

                        $iDocVersion = $oAppDocument->getDocVersion();
                        $sAppDocUid = $oAppDocument->getAppDocUid();
                        $aInfo = pathinfo($oAppDocument->getAppDocFilename());
                        $sExtension = ((isset($aInfo["extension"])) ? $aInfo["extension"] : "");
                        $pathUID = G::getPathFromUID($app_uid);
                        $sPathName = PATH_DOCUMENT . $pathUID . PATH_SEP;
                        $sFileName = $sAppDocUid . "_" . $iDocVersion . "." . $sExtension;
                        G::uploadFile($arrayFileTmpName[$i], $sPathName, $sFileName);
                        $response = array("status" => "ok");
                    }
                }
            }
        }

        return $response;
    }

    /**
     * claim case
     *
     * @param $userUid
     * @param $Fields
     * @param $type
     * @throws Exception
     */
    public function claimCaseUser($userUid, $sAppUid)
    {
        $response = array("status" => "fail");
        $oCase = new Cases();
        $iDelIndex = $oCase->getCurrentDelegation($sAppUid, '', true);

        $oAppDelegation = new \AppDelegation();
        $aDelegation = $oAppDelegation->load($sAppUid, $iDelIndex);

        //if there are no user in the delegation row, this case is still in selfservice
        if ($aDelegation['USR_UID'] == "") {
            $oCase->setCatchUser($sAppUid, $iDelIndex, $userUid);
            $response = array("status" => "ok");
        } else {
            //G::SendMessageText( G::LoadTranslation( 'ID_CASE_ALREADY_DERIVATED' ), 'error' );
        }

        return $response;
    }

    /**
     * GET return array category
     *
     * @return array
     */
    public function getCategoryList()
    {
        $category = array();
        $category[] = array("", G::LoadTranslation("ID_ALL_CATEGORIES"));

        $criteria = new Criteria('workflow');
        $criteria->addSelectColumn(ProcessCategoryPeer::CATEGORY_UID);
        $criteria->addSelectColumn(ProcessCategoryPeer::CATEGORY_NAME);
        $criteria->addAscendingOrderByColumn(ProcessCategoryPeer::CATEGORY_NAME);

        $dataset = ProcessCategoryPeer::doSelectRS($criteria);
        $dataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $dataset->next();

        while ($row = $dataset->getRow()) {
            $category[] = array($row['CATEGORY_UID'], $row['CATEGORY_NAME']);
            $dataset->next();
        }

        return $category;
    }

    /**
     * @param $action
     * @param $categoryUid
     * @param $userUid
     * @return array
     * @throws PropelException
     */
    public function getProcessList($action, $categoryUid, $userUid)
    {
        //$action = isset( $_REQUEST['action'] ) ? $_REQUEST['action'] : null;
        //$categoryUid = isset( $_REQUEST['CATEGORY_UID'] ) ? $_REQUEST['CATEGORY_UID'] : null;
        //$userUid = (isset( $_SESSION['USER_LOGGED'] ) && $_SESSION['USER_LOGGED'] != '') ? $_SESSION['USER_LOGGED'] : null;

        // global $oAppCache;
        $oAppCache = new AppCacheView();
        $processes = array();
        $processes[] = array('', G::LoadTranslation('ID_ALL_PROCESS'));

        //get the list based in the action provided
        switch ($action) {
            case 'draft':
                $cProcess = $oAppCache->getDraftListCriteria($userUid); //fast enough
                break;
            case 'sent':
                $cProcess = $oAppCache->getSentListProcessCriteria($userUid); // fast enough
                break;
            case 'simple_search':
            case 'search':
                //in search action, the query to obtain all process is too slow, so we need to query directly to
                //process and content tables, and for that reason we need the current language in AppCacheView.

                $oConf = new Configurations();
                $oConf->loadConfig($x, 'APP_CACHE_VIEW_ENGINE', '', '', '', '');
                $appCacheViewEngine = $oConf->aConfig;
                $lang = isset($appCacheViewEngine['LANG']) ? $appCacheViewEngine['LANG'] : 'en';

                $cProcess = new Criteria('workflow');
                $cProcess->clearSelectColumns();
                $cProcess->addSelectColumn(ProcessPeer::PRO_UID);
                $cProcess->addSelectColumn(ProcessPeer::PRO_TITLE);
                if ($categoryUid) {
                    $cProcess->add(ProcessPeer::PRO_CATEGORY, $categoryUid);
                }
                $cProcess->add(ProcessPeer::PRO_STATUS, 'ACTIVE');
                $cProcess->addAscendingOrderByColumn(ProcessPeer::PRO_TITLE);

                $oDataset = ProcessPeer::doSelectRS($cProcess);
                $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
                $oDataset->next();

                while ($aRow = $oDataset->getRow()) {
                    $processes[] = array($aRow['PRO_UID'], $aRow['PRO_TITLE']);
                    $oDataset->next();
                }

                return print G::json_encode($processes);
                break;
            case 'unassigned':
                $cProcess = $oAppCache->getUnassignedListCriteria($userUid);
                break;
            case 'paused':
                $cProcess = $oAppCache->getPausedListCriteria($userUid);
                break;
            case 'to_revise':
                $cProcess = $oAppCache->getToReviseListCriteria($userUid);
                break;
            case 'to_reassign':
                $cProcess = $oAppCache->getToReassignListCriteria($userUid);
                break;
            case 'gral':
                $cProcess = $oAppCache->getGeneralListCriteria();
                break;
            case 'todo':
            default:
                $cProcess = $oAppCache->getToDoListCriteria($userUid); //fast enough
                break;
        }
        //get the processes for this user in this action
        $cProcess->clearSelectColumns();
        $cProcess->addSelectColumn(AppCacheViewPeer::PRO_UID);
        $cProcess->addSelectColumn(AppCacheViewPeer::APP_PRO_TITLE);
        $cProcess->setDistinct(AppCacheViewPeer::PRO_UID);
        if ($categoryUid) {
            require_once 'classes/model/Process.php';
            $cProcess->addAlias('CP', 'PROCESS');
            $cProcess->add('CP.PRO_CATEGORY', $categoryUid, Criteria::EQUAL);
            $cProcess->addJoin(AppCacheViewPeer::PRO_UID, 'CP.PRO_UID', Criteria::LEFT_JOIN);
            $cProcess->addAsColumn('CATEGORY_UID', 'CP.PRO_CATEGORY');
        }

        $cProcess->addAscendingOrderByColumn(AppCacheViewPeer::APP_PRO_TITLE);

        $oDataset = AppCacheViewPeer::doSelectRS($cProcess, Propel::getDbConnection('workflow_ro'));
        $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $oDataset->next();

        while ($aRow = $oDataset->getRow()) {
            $processes[] = array(
                $aRow['PRO_UID'],
                $aRow['APP_PRO_TITLE']
            );
            $oDataset->next();
        }

        return $processes;
    }

    /**
     * lista de usuarios a reasignar
     */
    public function getUsersToReassign($usr_uid, $task_uid)
    {

        $memcache = PMmemcached::getSingleton(SYS_SYS);
        $RBAC = RBAC::getSingleton(PATH_DATA, session_id());
        $RBAC->sSystem = 'PROCESSMAKER';
        $RBAC->initRBAC();
        $memKey = 'rbacSession' . session_id();
        if (($RBAC->aUserInfo = $memcache->get($memKey)) === false) {
            $RBAC->loadUserRolePermission($RBAC->sSystem, $usr_uid);
            $memcache->set($memKey, $RBAC->aUserInfo, PMmemcached::EIGHT_HOURS);
        }
        $GLOBALS['RBAC'] = $RBAC;

        $task = new \Task();
        $tasks = $task->load($task_uid);
        $case = new Cases();
        $result = new \stdclass();
        $result->data = $case->getUsersToReassign($task_uid, $usr_uid, $tasks['PRO_UID']);

        return $result;
    }

    /**
     *
     */
    public function reassignCase($usr_uid, $app_uid, $TO_USR_UID)
    {
        $cases = new Cases();
        $user = new \Users();
        $app = new \Application();
        $result = new \stdclass();

        try {
            $iDelIndex = $cases->getCurrentDelegation($app_uid, $usr_uid);
            $cases->reassignCase($app_uid, $iDelIndex, $usr_uid, $TO_USR_UID);
            $caseData = $app->load($app_uid);
            $userData = $user->load($TO_USR_UID);
            $data['APP_NUMBER'] = $caseData['APP_NUMBER'];
            $data['USER'] = $userData['USR_LASTNAME'] . ' ' . $userData['USR_FIRSTNAME']; //TODO change with the farmated username from environment conf
            $result->status = 0;
            $result->msg = G::LoadTranslation('ID_REASSIGNMENT_SUCCESS', SYS_LANG, $data);
        } catch (Exception $e) {
            $result->status = 1;
            $result->msg = $e->getMessage();
        }

        return $result;
    }

    /**
     *
     */
    public function pauseCase($usr_uid, $app_uid, $request_data)
    {
        $result = new \stdclass();

        try {
            $oCase = new Cases();
            $iDelIndex = $oCase->getCurrentDelegation($app_uid, $usr_uid);
            // Save the note pause reason
            if ($request_data['noteContent'] != '') {
                $request_data['noteContent'] = G::LoadTranslation('ID_CASE_PAUSE_LABEL_NOTE') . ' ' . $request_data['noteContent'];
                $appNotes = new \AppNotes();
                $noteContent = addslashes($request_data['noteContent']);
                $appNotes->postNewNote($app_uid, $usr_uid, $noteContent, $request_data['notifyUser']);
            }
            // End save

            $oCase->pauseCase($app_uid, $iDelIndex, $usr_uid, $request_data['unpauseDate']);
            $app = new \Application();
            $caseData = $app->load($app_uid);
            $data['APP_NUMBER'] = $caseData['APP_NUMBER'];
            $data['UNPAUSE_DATE'] = $request_data['unpauseDate'];

            $result->success = true;
            $result->msg = G::LoadTranslation('ID_CASE_PAUSED_SUCCESSFULLY', SYS_LANG, $data);
        } catch (Exception $e) {
            throw $e;
        }

        return $result;
    }

    /**
     * Get configuration
     * @return mixed
     */
    public function getConfiguration($params)
    {
        $sysConf = Bootstrap::getSystemConfiguration('', '', SYS_SYS);
        $multiTimeZone = false;
        //Set Time Zone
        /*----------------------------------********---------------------------------*/
        $tz = isset($_SESSION['USR_TIME_ZONE']) ? $_SESSION['USR_TIME_ZONE'] : $sysConf['time_zone'];
        $offset = timezone_offset_get(new \DateTimeZone($tz), new \DateTime());
        $response['timeZone'] = sprintf("GMT%s%02d:%02d", ($offset >= 0) ? '+' : '-', abs($offset / 3600),
            abs(($offset % 3600) / 60));
        $response['multiTimeZone'] = $multiTimeZone;
        $fields = System::getSysInfo();
        $response['version'] = $fields['PM_VERSION'];

        $buildType = 'Community';
        /*----------------------------------********---------------------------------*/
        $response['buildType'] = $buildType;

        $conf = new Configurations();
        $confEnvironment = $conf->getFormats();

        $response['environment'] = array();
        if (is_array($confEnvironment)) {
            $response['environment']['format'] = isset($confEnvironment['format']) ? $confEnvironment['format'] : '';
            $response['environment']['dateFormat'] = isset($confEnvironment['dateFormat']) ? $confEnvironment['dateFormat'] : '';
            $response['environment']['casesListDateFormat'] = isset($confEnvironment['casesListDateFormat']) ? $confEnvironment['casesListDateFormat'] : '';
        }

        $Translations = new \Translation;
        $translationsTable = $Translations->getTranslationEnvironments();
        $languagesList = array();

        foreach ($translationsTable as $locale) {
            $LANG_ID = $locale['LOCALE'];
            if ($locale['COUNTRY'] != '.') {
                $LANG_NAME = $locale['LANGUAGE'] . ' (' . (ucwords(strtolower($locale['COUNTRY']))) . ')';
            } else {
                $LANG_NAME = $locale['LANGUAGE'];
            }
            $languages["L10n"] = $LANG_ID;
            $languages["label"] = $LANG_NAME;
            $languagesList[] = $languages;
        }
        $response['listLanguage'] = $languagesList;
        if (isset($params['fileLimit']) && $params['fileLimit']) {
            $postMaxSize = $this->return_bytes(ini_get('post_max_size'));
            $uploadMaxFileSize = $this->return_bytes(ini_get('upload_max_filesize'));
            if ($postMaxSize < $uploadMaxFileSize) {
                $uploadMaxFileSize = $postMaxSize;
            }
            $response['fileLimit'] = $uploadMaxFileSize;
        }
        if (isset($params['tz']) && $params['tz']) {
            $response['tz'] = isset($_SESSION['USR_TIME_ZONE']) ? $_SESSION['USR_TIME_ZONE'] : $sysConf['time_zone'];
        }

        return $response;
    }

    public function return_bytes($size_str)
    {
        switch (substr($size_str, -1)) {
            case 'M':
            case 'm':
                return (int)$size_str * 1048576;
            case 'K':
            case 'k':
                return (int)$size_str * 1024;
            case 'G':
            case 'g':
                return (int)$size_str * 1073741824;
            default:
                return $size_str;
        }
    }

    public function getInformationDerivatedCase($app_uid, $del_index)
    {
        $oCriteria = new Criteria('workflow');
        $children = array();
        $oCriteria->clearSelectColumns();
        $oCriteria->addSelectColumn(AppDelegationPeer::DEL_INDEX);
        $oCriteria->addSelectColumn(AppDelegationPeer::PRO_UID);
        $oCriteria->addSelectColumn(AppDelegationPeer::TAS_UID);
        $oCriteria->addSelectColumn(AppDelegationPeer::USR_UID);
        $oCriteria->add(AppDelegationPeer::APP_UID, $app_uid);
        $oCriteria->add(AppDelegationPeer::DEL_PREVIOUS, $del_index);
        $oDataset = AppDelegationPeer::doSelectRS($oCriteria);
        $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $oDataset->next();
        while ($row = $oDataset->getRow()) {
            $children[] = $row;
            $oDataset->next();
        }

        return $children;
    }

    /**
     * This function check if the $data are in the corresponding cases list
     * @param string $userUid
     * @param array $data
     * @param string $listName
     * @param string $action
     * @return array $response
     */
    public function getListCheck($userUid, $data, $listName = 'inbox', $action = 'todo')
    {
        $casesToCheck = [];
        foreach ($data as $key => $val) {
            array_push($casesToCheck, $val['caseId']);
        }
        $dataList = [];
        $dataList['appUidCheck'] = $casesToCheck;
        $dataList['userId'] = $userUid;
        $dataList['action'] = $action;
            /*----------------------------------********---------------------------------*/
            $case = new Cases();
            $response = $case->getList($dataList);

        /*----------------------------------********---------------------------------*/
        $result = [];
        foreach ($data as $key => $val) {
            $flagRemoved = true;
            foreach ($response['data'] as $row) {
                $row = array_change_key_case($row,CASE_UPPER);
                if (isset($row['APP_UID']) && isset($row['DEL_INDEX'])) {
                    if ($val['caseId'] === $row['APP_UID'] && $val['delIndex'] === $row['DEL_INDEX'] ) {
                        $flagRemoved = false;
                        continue;
                    }
                }

            }
            if ($flagRemoved) {
                $result[] = [
                    'caseId' => $val['caseId'],
                    'delIndex' => $val['delIndex']
                ];
            }
        }

        return $result;
    }
}
