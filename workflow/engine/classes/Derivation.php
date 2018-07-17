<?php

class Derivation
{
    var $case;
    protected $flagControl;
    protected $flagControlMulInstance;
    protected $sys;
    protected $context;
    protected $regexpTaskTypeToInclude;
    public $node;
    public $userLogged = null;
    protected $flagUpdateList;
    protected $removeList;
    protected $aSP;
    protected $iNewDelIndex;
    protected $arraySiblings;

    public function __construct()
    {
        $this->userLogged = new Users();
        $this->setRegexpTaskTypeToInclude("GATEWAYTOGATEWAY|END-MESSAGE-EVENT|END-EMAIL-EVENT");
    }

    /**
     * @return mixed
     */
    public function getRegexpTaskTypeToInclude()
    {
        return $this->regexpTaskTypeToInclude;
    }

    /**
     * @param mixed $regexpTaskTypeToInclude
     */
    public function setRegexpTaskTypeToInclude($regexpTaskTypeToInclude)
    {
        $this->regexpTaskTypeToInclude = $regexpTaskTypeToInclude;
    }

    /**
     * prepareInformationTask
     *
     * @param array $arrayTaskData Task data (derivation)
     *
     * @return array Return array
     * @throws Exception
     */
    protected function prepareInformationTask(array $arrayTaskData)
    {
        try {
            $task = new Task();

            $arrayTaskData = G::array_merges($arrayTaskData, $task->load($arrayTaskData["TAS_UID"]));

            //2. If next case is an special case
            if ((int)($arrayTaskData["ROU_NEXT_TASK"]) < 0) {
                $arrayTaskData["NEXT_TASK"]["TAS_UID"] = (int)($arrayTaskData["ROU_NEXT_TASK"]);
                $arrayTaskData["NEXT_TASK"]["TAS_ASSIGN_TYPE"] = "nobody";
                $arrayTaskData["NEXT_TASK"]["TAS_PRIORITY_VARIABLE"] = "";
                $arrayTaskData["NEXT_TASK"]["TAS_DEF_PROC_CODE"] = "";
                $arrayTaskData["NEXT_TASK"]["TAS_PARENT"] = "";
                $arrayTaskData["NEXT_TASK"]["TAS_TRANSFER_FLY"] = "";

                switch ($arrayTaskData["ROU_NEXT_TASK"]) {
                    case -1:
                        $arrayTaskData["NEXT_TASK"]["TAS_TITLE"] = G::LoadTranslation("ID_END_OF_PROCESS");
                        break;
                    case -2:
                        $arrayTaskData["NEXT_TASK"]["TAS_TITLE"] = G::LoadTranslation("ID_TAREA_COLGANTE");
                        break;
                }

                $arrayTaskData["NEXT_TASK"]["USR_UID"] = "";
                $arrayTaskData["NEXT_TASK"]["USER_ASSIGNED"] = array("USR_UID" => "", "USR_USERNAME" => "");
            } else {
                //3. Load the task information of normal NEXT_TASK
                $arrayTaskData["NEXT_TASK"] = $task->load($arrayTaskData["ROU_NEXT_TASK"]); //print $arrayTaskData["ROU_NEXT_TASK"]." **** ".$arrayTaskData["NEXT_TASK"]["TAS_TYPE"]."<hr>";

                if ($arrayTaskData["NEXT_TASK"]["TAS_TYPE"] == "SUBPROCESS") {
                    $taskParent = $arrayTaskData["NEXT_TASK"]["TAS_UID"];

                    $criteria = new Criteria("workflow");
                    $criteria->add(SubProcessPeer::PRO_PARENT, $arrayTaskData["PRO_UID"]);
                    $criteria->add(SubProcessPeer::TAS_PARENT, $arrayTaskData["NEXT_TASK"]["TAS_UID"]);
                    $rsCriteria = SubProcessPeer::doSelectRS($criteria);
                    $rsCriteria->setFetchmode(ResultSet::FETCHMODE_ASSOC);
                    $rsCriteria->next();
                    $row = $rsCriteria->getRow();

                    $arrayTaskData["ROU_NEXT_TASK"] = $row["TAS_UID"]; //print "<hr>Life is just a lonely highway";
                    $arrayTaskData["NEXT_TASK"] = $task->load($arrayTaskData["ROU_NEXT_TASK"]); //print "<hr>Life is just a lonely highway";print"<hr>";

                    $process = new Process();
                    $row = $process->load($row["PRO_UID"]);

                    $arrayTaskData["NEXT_TASK"]["TAS_TITLE"] .= " (" . $row["PRO_TITLE"] . ")";
                    $arrayTaskData["NEXT_TASK"]["TAS_PARENT"] = $taskParent;

                    //unset($task, $process, $row, $taskParent);
                } else {
                    $arrayTaskData["NEXT_TASK"]["TAS_PARENT"] = "";
                }

                $regexpTaskTypeToExclude = "GATEWAYTOGATEWAY|END-MESSAGE-EVENT|SCRIPT-TASK|SERVICE-TASK|INTERMEDIATE-CATCH-TIMER-EVENT|INTERMEDIATE-THROW-MESSAGE-EVENT|END-EMAIL-EVENT|INTERMEDIATE-THROW-EMAIL-EVENT|INTERMEDIATE-CATCH-MESSAGE-EVENT";

                $arrayTaskData["NEXT_TASK"]["USER_ASSIGNED"] = (!preg_match("/^(?:" . $regexpTaskTypeToExclude . ")$/", $arrayTaskData["NEXT_TASK"]["TAS_TYPE"]))? $this->getNextAssignedUser($arrayTaskData) : array("USR_UID" => "", "USR_FULLNAME" => "");
            }
            //Return
            return $arrayTaskData;
        } catch (Exception $e) {
            throw $e;
        }
    }

     /**
     * prepareInformation
     *
     * @param array  $arrayData Data
     * @param string $taskUid   Unique id of Task
     *
     * @return array
     * @throws Exception
     */
    public function prepareInformation(array $arrayData, $taskUid = "")
    {
        try {
            $this->case = new Cases();
            $task = new Task();

            $arrayApplicationData = $this->case->loadCase($arrayData["APP_UID"]);

            $arrayNextTask = array();
            $arrayNextTaskDefault = array();
            $i = 0;

            $criteria = new Criteria("workflow");

            $criteria->addSelectColumn(RoutePeer::TAS_UID);
            $criteria->addSelectColumn(RoutePeer::ROU_NEXT_TASK);
            $criteria->addSelectColumn(RoutePeer::ROU_TYPE);
            $criteria->addSelectColumn(RoutePeer::ROU_DEFAULT);
            $criteria->addSelectColumn(RoutePeer::ROU_CONDITION);

            if ($taskUid != "") {
                $criteria->add(RoutePeer::TAS_UID, $taskUid, Criteria::EQUAL);
                $criteria->addAscendingOrderByColumn(RoutePeer::ROU_CASE);

                $rsCriteria = RoutePeer::doSelectRS($criteria);
            } else {
                $criteria->addJoin(AppDelegationPeer::TAS_UID, TaskPeer::TAS_UID, Criteria::LEFT_JOIN);
                $criteria->addJoin(AppDelegationPeer::TAS_UID, RoutePeer::TAS_UID, Criteria::LEFT_JOIN);
                $criteria->add(AppDelegationPeer::APP_UID, $arrayData["APP_UID"], Criteria::EQUAL);
                $criteria->add(AppDelegationPeer::DEL_INDEX, $arrayData["DEL_INDEX"], Criteria::EQUAL);
                $criteria->addAscendingOrderByColumn(RoutePeer::ROU_CASE);

                $rsCriteria = AppDelegationPeer::doSelectRS($criteria);
            }

            $rsCriteria->setFetchmode(ResultSet::FETCHMODE_ASSOC);

            $flagDefault = false;
            $aSecJoin = array();
            $count = 0;

            while ($rsCriteria->next()) {
                $arrayRouteData = G::array_merges($rsCriteria->getRow(), $arrayData);

                if ((int)($arrayRouteData["ROU_DEFAULT"]) == 1) {
                    $arrayNextTaskDefault = $arrayRouteData;
                    $flagDefault = true;
                    continue;
                }

                $flagAddDelegation = true;

                //Evaluate the condition if there are conditions defined
                if (trim($arrayRouteData["ROU_CONDITION"]) != "" && $arrayRouteData["ROU_TYPE"] != "SELECT") {
                    $pmScript = new PMScript();
                    $pmScript->setFields($arrayApplicationData["APP_DATA"]);
                    $pmScript->setScript($arrayRouteData["ROU_CONDITION"]);
                    $flagAddDelegation = $pmScript->evaluate();
                }

                if (trim($arrayRouteData['ROU_CONDITION']) == '' && $arrayRouteData['ROU_NEXT_TASK'] != '-1') {
                    $arrayTaskData = $task->load($arrayRouteData['ROU_NEXT_TASK']);

                    if ($arrayRouteData['ROU_TYPE'] != 'SEC-JOIN' && $arrayTaskData['TAS_TYPE'] == 'GATEWAYTOGATEWAY') {
                        $flagAddDelegation = true;
                    }

                    if($arrayRouteData['ROU_TYPE'] == 'SEC-JOIN'){
                       $aSecJoin[$count]['ROU_PREVIOUS_TASK'] = $arrayRouteData['ROU_NEXT_TASK'];
                       $aSecJoin[$count]['ROU_PREVIOUS_TYPE'] = 'SEC-JOIN';
                       $count++;
                    }
                }

                if ($arrayRouteData['ROU_TYPE'] == 'EVALUATE' && !empty($arrayNextTask)) {
                    $flagAddDelegation = false;
                }

                if ($flagAddDelegation &&
                    preg_match("/^(?:EVALUATE|PARALLEL-BY-EVALUATION)$/", $arrayRouteData["ROU_TYPE"]) &&
                    trim($arrayRouteData["ROU_CONDITION"]) == ""
                ) {
                    $flagAddDelegation = false;
                }

                if ($flagAddDelegation) {
                    $arrayNextTask[++$i] = $this->prepareInformationTask($arrayRouteData);
                }
            }

            if (count($arrayNextTask) == 0 && count($arrayNextTaskDefault) > 0) {
                $arrayNextTask[++$i] = $this->prepareInformationTask($arrayNextTaskDefault);
            }

            //Check Task GATEWAYTOGATEWAY, END-MESSAGE-EVENT, END-EMAIL-EVENT
            $arrayNextTaskBackup = $arrayNextTask;

            $arrayNextTask = array();
            $i = 0;
            foreach ($arrayNextTaskBackup as $value) {
                $arrayNextTaskData = $value;
                $this->node[$value['TAS_UID']]['out'][$value['ROU_NEXT_TASK']] = $value['ROU_TYPE'];
                if ($arrayNextTaskData["NEXT_TASK"]["TAS_UID"] != "-1" &&
                    preg_match("/^(?:" . $this->regexpTaskTypeToInclude . ")$/", $arrayNextTaskData["NEXT_TASK"]["TAS_TYPE"])
                ) {
                    $arrayAux = $this->prepareInformation($arrayData, $arrayNextTaskData["NEXT_TASK"]["TAS_UID"]);
                    $this->node[$value['ROU_NEXT_TASK']]['in'][$value['TAS_UID']] = $value['ROU_TYPE'];
                    foreach ($arrayAux as $value2) {
                        $key = ++$i;
                        $arrayNextTask[$key] = $value2;
                        $prefix = substr($value['ROU_NEXT_TASK'], 0, 4);
                        if($prefix!=='gtg-'){
                            $arrayNextTask[$key]['SOURCE_UID'] = $value['ROU_NEXT_TASK'];
                        }
                        foreach($aSecJoin as $rsj){
                          $arrayNextTask[$i]["NEXT_TASK"]["ROU_PREVIOUS_TASK"] = $rsj["ROU_PREVIOUS_TASK"];
                          $arrayNextTask[$i]["NEXT_TASK"]["ROU_PREVIOUS_TYPE"] = "SEC-JOIN";
                        }
                    }
                } else {
                    $regexpTaskTypeToInclude = "END-MESSAGE-EVENT|END-EMAIL-EVENT|INTERMEDIATE-THROW-EMAIL-EVENT";

                    if ($arrayNextTaskData["NEXT_TASK"]["TAS_UID"] == "-1" &&
                        preg_match("/^(?:" . $regexpTaskTypeToInclude . ")$/", $arrayNextTaskData["TAS_TYPE"])
                    ) {
                        $arrayNextTaskData["NEXT_TASK"]["TAS_UID"] = $arrayNextTaskData["TAS_UID"] . "/" . $arrayNextTaskData["NEXT_TASK"]["TAS_UID"];
                    }
                    $prefix = substr($value['ROU_NEXT_TASK'], 0, 4);
                    if($prefix!=='gtg-'){
                        $arrayNextTaskData['SOURCE_UID'] = $value['ROU_NEXT_TASK'];
                    }
                    $arrayNextTask[++$i] = $arrayNextTaskData;
                    foreach($aSecJoin as $rsj){
                        $arrayNextTask[$i]["NEXT_TASK"]["ROU_PREVIOUS_TASK"] = $rsj["ROU_PREVIOUS_TASK"];
                        $arrayNextTask[$i]["NEXT_TASK"]["ROU_PREVIOUS_TYPE"] = "SEC-JOIN";
                    }
                    //Start-Timer with Script-task
                    $criteriaE = new Criteria("workflow");
                    $criteriaE->addSelectColumn(ElementTaskRelationPeer::ELEMENT_UID);
                    $criteriaE->addJoin(BpmnEventPeer::EVN_UID, ElementTaskRelationPeer::ELEMENT_UID, Criteria::LEFT_JOIN);
                    $criteriaE->add(ElementTaskRelationPeer::TAS_UID, $arrayNextTaskData["TAS_UID"], Criteria::EQUAL);
                    $criteriaE->add(BpmnEventPeer::EVN_TYPE, 'START', Criteria::EQUAL);
                    $criteriaE->add(BpmnEventPeer::EVN_MARKER, 'TIMER', Criteria::EQUAL);
                    $rsCriteriaE = AppDelegationPeer::doSelectRS($criteriaE);
                    $rsCriteriaE->setFetchmode(ResultSet::FETCHMODE_ASSOC);
                    while ($rsCriteriaE->next()) {
                        if($arrayNextTaskData["NEXT_TASK"]["TAS_TYPE"] == "SCRIPT-TASK"){
                            if(isset($arrayNextTaskData["NEXT_TASK"]["USER_ASSIGNED"]["USR_UID"]) && $arrayNextTaskData["NEXT_TASK"]["USER_ASSIGNED"]["USR_UID"] == ""){
                                $useruid = "00000000000000000000000000000001";
                                $userFields = $this->getUsersFullNameFromArray( $useruid );
                                $arrayNextTask[$i]["NEXT_TASK"]["USER_ASSIGNED"] = $userFields;
                            }
                        }
                    }
                }
            }

            //1. There is no rule
            if (empty($arrayNextTask)) {
                $bpmn = new \ProcessMaker\Project\Bpmn();

                throw new Exception(G::LoadTranslation(
                    'ID_NO_DERIVATION_' . (($bpmn->exists($arrayApplicationData['PRO_UID']))? 'BPMN_RULE' : 'RULE')
                ));
            }

            //Return
            return $arrayNextTask;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * getRouteCondition
     *
     * @param array $aData
     * @return $routeCondition
     */
    function getRouteCondition ($aData)
    {
        $c = new Criteria( 'workflow' );
        $c->clearSelectColumns();
        $c->addSelectColumn( AppDelegationPeer::TAS_UID );
        $c->addSelectColumn( RoutePeer::ROU_CONDITION );
        $c->addSelectColumn( RoutePeer::ROU_NEXT_TASK );
        $c->addSelectColumn( RoutePeer::ROU_TYPE );
        $c->addSelectColumn( RoutePeer::ROU_OPTIONAL );
        $c->addJoin( AppDelegationPeer::TAS_UID, TaskPeer::TAS_UID, Criteria::LEFT_JOIN );
        $c->addJoin( AppDelegationPeer::TAS_UID, RoutePeer::TAS_UID, Criteria::LEFT_JOIN );
        $c->add( AppDelegationPeer::APP_UID, $aData['APP_UID'] );
        $c->add( AppDelegationPeer::DEL_INDEX, $aData['DEL_INDEX'] );
        $c->addAscendingOrderByColumn( RoutePeer::ROU_CASE );
        $rs = AppDelegationPeer::doSelectRs( $c );
        $rs->setFetchmode( ResultSet::FETCHMODE_ASSOC );
        $rs->next();
        $aDerivation = $rs->getRow();
        while (is_array( $aDerivation )) {
            return $aDerivation;
        }

    }

    function GetAppParentIndex ($aData)
    {
        //('SELECT * FROM APP_THREAD WHERE APP_UID='".$aData['APP_UID']."' AND DEL_INDEX = '".$aData['DEL_INDEX']."'");
        try {
            $aThreads = array ();
            $c = new Criteria();
            $c->addSelectColumn( AppThreadPeer::APP_THREAD_PARENT );
            $c->add( AppThreadPeer::APP_UID, $aData['APP_UID'] );
            $c->add( AppThreadPeer::DEL_INDEX, $aData['DEL_INDEX'] );
            $rs = AppThreadPeer::doSelectRs( $c );
            $rs->setFetchmode( ResultSet::FETCHMODE_ASSOC );
            $rs->next();
            $row = $rs->getRow();
            while (is_array( $row )) {
                $aThreads = $row;
                $rs->next();
                $row = $rs->getRow();
            }
            return $aThreads;
        } catch (exception $e) {
            throw ($e);
        }
    }

    /* get all users, from any task, if the task have Groups, the function expand the group
     *
     * @param   string  $sTasUid  the task uidUser
     * @param   bool    $flagIncludeAdHocUsers
     * @return  Array   $users an array with userID order by USR_UID
     */
    function getAllUsersFromAnyTask($sTasUid, $flagIncludeAdHocUsers = false)
    {
        $users = array ();
        $c = new Criteria( 'workflow' );
        $c->clearSelectColumns();
        $c->addSelectColumn( TaskUserPeer::USR_UID );
        $c->addSelectColumn( TaskUserPeer::TU_RELATION );
        $c->add( TaskUserPeer::TAS_UID, $sTasUid );

        if ($flagIncludeAdHocUsers) {
            $c->add(
                $c->getNewCriterion(TaskUserPeer::TU_TYPE, 1, Criteria::EQUAL)->addOr(
                $c->getNewCriterion(TaskUserPeer::TU_TYPE, 2, Criteria::EQUAL))
            );
        } else {
            $c->add(TaskUserPeer::TU_TYPE, 1);
        }

        $rs = TaskUserPeer::DoSelectRs( $c );
        $rs->setFetchmode( ResultSet::FETCHMODE_ASSOC );
        $rs->next();
        $row = $rs->getRow();
        while (is_array( $row )) {
            if ($row['TU_RELATION'] == '2') {
                $cGrp = new Criteria( 'workflow' );
                $cGrp->add( GroupwfPeer::GRP_STATUS, 'ACTIVE' );
                $cGrp->add( GroupUserPeer::GRP_UID, $row['USR_UID'] );
                $cGrp->addJoin( GroupUserPeer::GRP_UID, GroupwfPeer::GRP_UID, Criteria::LEFT_JOIN );
                $cGrp->addJoin( GroupUserPeer::USR_UID, UsersPeer::USR_UID, Criteria::LEFT_JOIN );
                $cGrp->add( UsersPeer::USR_STATUS, 'INACTIVE', Criteria::NOT_EQUAL );
                $rsGrp = GroupUserPeer::DoSelectRs( $cGrp );
                $rsGrp->setFetchmode( ResultSet::FETCHMODE_ASSOC );
                $rsGrp->next();
                $rowGrp = $rsGrp->getRow();
                while (is_array( $rowGrp )) {
                    $users[$rowGrp['USR_UID']] = $rowGrp['USR_UID'];
                    $rsGrp->next();
                    $rowGrp = $rsGrp->getRow();
                }
            } else {
                //filter to users that is in vacation or has an inactive estatus, and others
                $oUser = UsersPeer::retrieveByPK( $row['USR_UID'] );
                if ($oUser->getUsrStatus() == 'ACTIVE') {
                    $users[$row['USR_UID']] = $row['USR_UID'];
                } else {
                    $userUID = $this->checkReplacedByUser( $oUser );
                    if ($userUID != '') {
                        $users[$userUID] = $userUID;
                    }
                }
            }
            $rs->next();
            $row = $rs->getRow();
        }
        //to do: different types of sort
        sort( $users );

        return $users;
    }

    /* get an array of users, and returns the same arrays with User's fullname and other fields
     *
     * @param   Array   $aUsers      the task uidUser
     * @return  Array   $aUsersData  an array with with User's fullname
     */
    function getUsersFullNameFromArray ($aUsers)
    {
        $oUser = new Users();
        $aUsersData = array ();
        if (is_array( $aUsers )) {
            foreach ($aUsers as $key => $val) {
                // $userFields = $oUser->load( $val );
                $userFields = $oUser->userVacation( $val );
                $auxFields['USR_UID'] = $userFields['USR_UID'];
                $auxFields['USR_USERNAME'] = $userFields['USR_USERNAME'];
                $auxFields['USR_FIRSTNAME'] = $userFields['USR_FIRSTNAME'];
                $auxFields['USR_LASTNAME'] = $userFields['USR_LASTNAME'];
                $auxFields['USR_FULLNAME'] = $userFields['USR_LASTNAME'] . ($userFields['USR_LASTNAME'] != '' ? ', ' : '') . $userFields['USR_FIRSTNAME'];
                $auxFields['USR_EMAIL'] = $userFields['USR_EMAIL'];
                $auxFields['USR_STATUS'] = $userFields['USR_STATUS'];
                $auxFields['USR_COUNTRY'] = $userFields['USR_COUNTRY'];
                $auxFields['USR_CITY'] = $userFields['USR_CITY'];
                $auxFields['USR_LOCATION'] = $userFields['USR_LOCATION'];
                $auxFields['DEP_UID'] = $userFields['DEP_UID'];
                $auxFields['USR_HIDDEN_FIELD'] = '';
                $aUsersData[] = $auxFields;
            }
        } else {
            $oCriteria = new Criteria();
            $oCriteria->add( UsersPeer::USR_UID, $aUsers );

            if (UsersPeer::doCount( $oCriteria ) < 1) {
                return null;
            }
            $userFields = $oUser->load( $aUsers );
            $auxFields['USR_UID'] = $userFields['USR_UID'];
            $auxFields['USR_USERNAME'] = $userFields['USR_USERNAME'];
            $auxFields['USR_FIRSTNAME'] = $userFields['USR_FIRSTNAME'];
            $auxFields['USR_LASTNAME'] = $userFields['USR_LASTNAME'];
            $auxFields['USR_FULLNAME'] = $userFields['USR_LASTNAME'] . ($userFields['USR_LASTNAME'] != '' ? ', ' : '') . $userFields['USR_FIRSTNAME'];
            $auxFields['USR_EMAIL'] = $userFields['USR_EMAIL'];
            $auxFields['USR_STATUS'] = $userFields['USR_STATUS'];
            $auxFields['USR_COUNTRY'] = $userFields['USR_COUNTRY'];
            $auxFields['USR_CITY'] = $userFields['USR_CITY'];
            $auxFields['USR_LOCATION'] = $userFields['USR_LOCATION'];
            $auxFields['DEP_UID'] = $userFields['DEP_UID'];
            $aUsersData = $auxFields;
        }
        return $aUsersData;
    }

    /* get next assigned user
     *
     * @param   Array   $tasInfo
     * @return  Array   $userFields
     */
    function getNextAssignedUser ($tasInfo)
    {
        $nextAssignedTask = $tasInfo['NEXT_TASK'];
        $lastAssigned = $tasInfo['NEXT_TASK']['TAS_LAST_ASSIGNED'];
        $sTasUid = $tasInfo['NEXT_TASK']['TAS_UID'];

        $taskNext = TaskPeer::retrieveByPK($nextAssignedTask["TAS_UID"]);
        $bpmnActivityNext = BpmnActivityPeer::retrieveByPK($nextAssignedTask["TAS_UID"]);

        $flagTaskNextIsMultipleInstance = false;
        $flagTaskNextAssignTypeIsMultipleInstance = false;

        if (!is_null($taskNext) && !is_null($bpmnActivityNext)) {
            $flagTaskNextIsMultipleInstance = $bpmnActivityNext->getActType() == "TASK" && preg_match("/^(?:EMPTY|SENDTASK|RECEIVETASK|USERTASK|SERVICETASK|MANUALTASK|BUSINESSRULE)$/", $bpmnActivityNext->getActTaskType()) && $bpmnActivityNext->getActLoopType() == "PARALLEL";
            $flagTaskNextAssignTypeIsMultipleInstance = preg_match("/^(?:MULTIPLE_INSTANCE|MULTIPLE_INSTANCE_VALUE_BASED)$/", $taskNext->getTasAssignType());
        }

        $taskNextAssignType = $taskNext->getTasAssignType();
        $taskNextAssignType = ($flagTaskNextIsMultipleInstance && !$flagTaskNextAssignTypeIsMultipleInstance)? "" : $taskNextAssignType;
        $taskNextAssignType = (!$flagTaskNextIsMultipleInstance && $flagTaskNextAssignTypeIsMultipleInstance)? "" : $taskNextAssignType;

        switch ($taskNextAssignType) {
            case 'BALANCED':
                $users = $this->getAllUsersFromAnyTask( $sTasUid );
                $uidUser = "";

                if (is_array( $users ) && count( $users ) > 0) {
                    //to do apply any filter like LOCATION assignment
                    $uidUser = $users[0];
                    $i = count( $users ) - 1;
                    while ($i > 0) {
                        if ($lastAssigned < $users[$i]) {
                            $uidUser = $users[$i];
                        }
                        $i --;
                    }
                } else {
                    throw (new Exception( G::LoadTranslation( 'ID_NO_USERS' ) ));
                }
                $userFields = $this->getUsersFullNameFromArray( $uidUser );
                break;
            case 'STATIC_MI':
            case 'CANCEL_MI':
            case 'MANUAL':
                $users = $this->getAllUsersFromAnyTask( $sTasUid );
                $userFields = $this->getUsersFullNameFromArray( $users );
                break;
            case 'EVALUATE':
                $AppFields = $this->case->loadCase( $tasInfo['APP_UID'] );
                $variable = str_replace( '@@', '', $nextAssignedTask['TAS_ASSIGN_VARIABLE'] );
                if (isset( $AppFields['APP_DATA'][$variable] )) {
                    if ($AppFields['APP_DATA'][$variable] != '') {
                        $value = $this->checkReplacedByUser( $AppFields['APP_DATA'][$variable] );
                        $userFields = $this->getUsersFullNameFromArray( $value );
                        if (is_null( $userFields )) {
                            throw (new Exception( "Task doesn't have a valid user in variable $variable." ));
                        }
                    } else {
                        throw (new Exception( "Task doesn't have a valid user in variable $variable." ));
                    }
                } else {
                    throw (new Exception( "Task doesn't have a valid user in variable $variable or this variable doesn't exist." ));
                }
                break;
            case 'REPORT_TO':
                //default error user when the reportsTo is not assigned to that user
                //look for USR_REPORTS_TO to this user
                $userFields['USR_UID'] = '';
                $userFields['USR_FULLNAME'] = 'Current user does not have a valid Reports To user';
                $userFields['USR_USERNAME'] = 'Current user does not have a valid Reports To user';
                $userFields['USR_FIRSTNAME'] = '';
                $userFields['USR_LASTNAME'] = '';
                $userFields['USR_EMAIL'] = '';

                //get the report_to user & its full info
                $lastManager = $userTasInfo = $tasInfo["USER_UID"];
                do {
                    $userTasInfo = $this->getDenpendentUser($userTasInfo);
                    $useruid = $this->checkReplacedByUser($userTasInfo);
                    //When the lastManager is INACTIVE/VACATION and does not have a Replace by, the REPORT_TO is himself
                    if($lastManager === $userTasInfo){
                        $useruid = $tasInfo["USER_UID"];
                    } else {
                        $lastManager = $userTasInfo;
                    }
                } while ($useruid === '');

                if (isset( $useruid ) && $useruid != '') {
                    $userFields = $this->getUsersFullNameFromArray( $useruid );
                }
                // if there is no report_to user info, throw an exception indicating this
                if (! isset( $userFields ) || $userFields['USR_UID'] == '') {
                    throw (new Exception( G::LoadTranslation( 'ID_MSJ_REPORSTO' ) )); // "The current user does not have a valid Reports To user.  Please contact administrator.") ) ;
                }
                break;
            case 'SELF_SERVICE':
                //Check if is Self Service Value Based Assignment
                if (!empty($nextAssignedTask['TAS_GROUP_VARIABLE'])) {
                    $appFields = $this->case->loadCase($tasInfo['APP_UID']);
                    $variable = str_replace('@@', '', $nextAssignedTask['TAS_GROUP_VARIABLE']);

                    //If the variable exists will be validate the UID's
                    if (isset($appFields['APP_DATA'][$variable])) {
                        $arrVar = $appFields['APP_DATA'][$variable];
                        if (is_array($arrVar)) {
                            $statusToCheck = $arrVar;
                        } else {
                            $statusToCheck = array($arrVar);
                        }
                        $toValidate = array('ACTIVE', 'VACATION');
                        $gpr = new GroupUser();
                        if (!$gpr->groupsUsersAvailable($statusToCheck, $toValidate)) {
                            if (!($gpr->groupsUsersAvailable($statusToCheck, $toValidate, "groups"))) {
                                throw (new Exception("Task doesn't have a valid user in variable $variable or this variable doesn't exist."));
                            }
                        }
                    } else {
                        throw (new Exception("Task doesn't have a valid user in variable $variable or this variable doesn't exist."));
                    }
                }

                //look for USR_REPORTS_TO to this user
                $userFields['USR_UID'] = '';
                $userFields['USR_FULLNAME'] = '<b>' . G::LoadTranslation('ID_UNASSIGNED') . '</b>';
                $userFields['USR_USERNAME'] = '<b>' . G::LoadTranslation('ID_UNASSIGNED') . '</b>';
                $userFields['USR_FIRSTNAME'] = '';
                $userFields['USR_LASTNAME'] = '';
                $userFields['USR_EMAIL'] = '';
                break;
            case "MULTIPLE_INSTANCE":
                $userFields = $this->getUsersFullNameFromArray($this->getAllUsersFromAnyTask($nextAssignedTask["TAS_UID"]));
                if(empty($userFields)){
                    throw (new Exception( G::LoadTranslation( 'ID_NO_USERS' ) ));
                }
                break;
            case "MULTIPLE_INSTANCE_VALUE_BASED":
                $arrayApplicationData = $this->case->loadCase($tasInfo["APP_UID"]);

                $nextTaskAssignVariable = trim($nextAssignedTask["TAS_ASSIGN_VARIABLE"], " @#");

                if ($nextTaskAssignVariable != "" &&
                    isset($arrayApplicationData["APP_DATA"][$nextTaskAssignVariable]) && !empty($arrayApplicationData["APP_DATA"][$nextTaskAssignVariable]) && is_array($arrayApplicationData["APP_DATA"][$nextTaskAssignVariable])
                ) {
                    $userFields = $this->getUsersFullNameFromArray($arrayApplicationData["APP_DATA"][$nextTaskAssignVariable]);
                } else {
                    throw new Exception(G::LoadTranslation("ID_ACTIVITY_INVALID_USER_DATA_VARIABLE_FOR_MULTIPLE_INSTANCE_ACTIVITY", array(strtolower("ACT_UID"), $nextAssignedTask["TAS_UID"], $nextTaskAssignVariable)));
                }
                break;
            default:
                throw (new Exception( 'Invalid Task Assignment method for Next Task ' ));
                break;
        }
        return $userFields;
    }

    /* getDenpendentUser
     *
     * @param   string   $USR_UID
     * @return  string   $aRow['USR_REPORTS_TO']
     */
    function getDenpendentUser ($USR_UID)
    {
        $user = new \ProcessMaker\BusinessModel\User();

        $manager = $user->getUsersManager($USR_UID);

        //Return
        return ($manager !== false)? $manager : $USR_UID;
    }

    /* setTasLastAssigned
     *
     * @param   string   $tasUid
     * @param   string   $usrUid
     * @throws  Exception $e
     * @return  void
     */
    function setTasLastAssigned ($tasUid, $usrUid)
    {
        try {
            $oTask = TaskPeer::retrieveByPk( $tasUid );
            $oTask->setTasLastAssigned( $usrUid );
            $oTask->save();
        } catch (Exception $e) {
            throw ($e);
        }
    }

    /**
     * Execute Event
     *
     * @param string $dummyTaskUid                  Unique id of Element Origin      (unique id of Task) This is the nextTask
     * @param array  $applicationData               Case data
     * @param bool   $flagEventExecuteBeforeGateway Execute event before gateway
     * @param bool   $flagEventExecuteAfterGateway  Execute event after gateway
     *
     * @return void
     */
    private function executeEvent($dummyTaskUid, array $applicationData, $flagEventExecuteBeforeGateway = true, $flagEventExecuteAfterGateway = true, $elementOriUid='')
    {
        try {
            //Verify if the Project is BPMN
            $bpmn = new \ProcessMaker\Project\Bpmn();

            if (!$bpmn->exists($applicationData["PRO_UID"])) {
                return;
            }

            //Element origin and dest
            $arrayElement = array();
            $elementTaskRelation = new \ProcessMaker\BusinessModel\ElementTaskRelation();
            $arrayElementTaskRelationData = $elementTaskRelation->getElementTaskRelationWhere(
                    [
                        ElementTaskRelationPeer::PRJ_UID      => $applicationData["PRO_UID"],
                        ElementTaskRelationPeer::ELEMENT_TYPE => "bpmnEvent",
                        ElementTaskRelationPeer::TAS_UID      => $dummyTaskUid
                    ],
                    true
            );
            if(is_null($arrayElementTaskRelationData)){
                $arrayOtherElement = array();
                $arrayOtherElement = $bpmn->getElementsBetweenElementOriginAndElementDest(
                    $elementOriUid,
                    "bpmnActivity",
                    $dummyTaskUid,
                    "bpmnActivity"
                );
                $count = 0;
                foreach ($arrayOtherElement as $value) {
                    if($value[1] === 'bpmnEvent'){
                        $arrayElement[$count]["uid"]    = $value[0];
                        $arrayElement[$count++]["type"] = $value[1];
                    }
                }
            }
            if (!is_null($arrayElementTaskRelationData)) {
                $arrayElement[0]["uid"]  = $arrayElementTaskRelationData["ELEMENT_UID"];
                $arrayElement[0]["type"] = "bpmnEvent";
            }

            //Throw Events
            $messageApplication = new \ProcessMaker\BusinessModel\MessageApplication();
            $emailEvent = new \ProcessMaker\BusinessModel\EmailEvent();
            $arrayEventExecute = ["BEFORE" => $flagEventExecuteBeforeGateway, "AFTER" => $flagEventExecuteAfterGateway];
            $positionEventExecute = "BEFORE";

            $aContext = $this->context;
            $aContext['appUid'] = $applicationData["APP_UID"];
            $aContext['proUid'] = $applicationData["PRO_UID"];
            if(sizeof($arrayElement)){
                foreach ($arrayElement as $value) {
                    switch ($value['type']) {
                        case 'bpmnEvent':
                            if ($arrayEventExecute[$positionEventExecute]) {
                                $event = \BpmnEventPeer::retrieveByPK($value['uid']);

                                if (!is_null($event)) {
                                    if (preg_match("/^(?:END|INTERMEDIATE)$/", $event->getEvnType()) && $event->getEvnMarker() === 'MESSAGETHROW') {
                                        //Message-Application throw
                                        $result = $messageApplication->create($applicationData["APP_UID"], $applicationData["PRO_UID"], $value['uid'], $applicationData);

                                        $aContext['envUid'] = $value['uid'];
                                        $aContext['envType'] = $event->getEvnType();
                                        $aContext['envMarker'] = $event->getEvnMarker();
                                        $aContext['action'] = 'Message application throw';
                                        //Logger
                                        Bootstrap::registerMonolog('CaseDerivation', 200, 'Case Derivation', $aContext, $this->sysSys, 'processmaker.log');
                                    }

                                    if (preg_match("/^(?:END|INTERMEDIATE)$/", $event->getEvnType()) && $event->getEvnMarker() === 'EMAIL') {
                                        //Email-Event throw
                                        $result = $emailEvent->sendEmail($applicationData["APP_UID"], $applicationData["PRO_UID"], $value['uid'], $applicationData);

                                        $aContext['envUid'] = $value['uid'];
                                        $aContext['envType'] = $event->getEvnType();
                                        $aContext['envMarker'] = $event->getEvnMarker();
                                        $aContext['action'] = 'Email event throw';
                                        //Logger
                                        Bootstrap::registerMonolog('CaseDerivation', 200, 'Case Derivation', $aContext, $this->sysSys, 'processmaker.log');
                                    }
                                }
                            }
                            break;
                        case 'bpmnGateway':
                            $positionEventExecute = 'AFTER';
                            break;
                    }
                }
            }

        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Update counters
     *
     * @param array $arrayCurrentDelegationData
     * @param array $arrayNextDelegationData
     * @param mixed $taskNextDelegation
     * @param array $arrayApplicationData
     * @param int   $delIndexNew
     * @param mixed $aSp
     * @param bool  $removeList
     *
     * @return void
     */
    private function updateList(array $arrayCurrentDelegationData, array $arrayNextDelegationData, $taskNextDelegation, array $arrayApplicationData, $delIndexNew, $aSp, $removeList)
    {
        /*----------------------------------********---------------------------------*/
    }

    /**
     * This function prepare the information before call the derivate function
     *
     * We can route a case from differents ways from cases_Derivate and derivateCase used in PMFDerivateCase
     * before this we need to process the information
     *
     * @param array $aDataForPrepareInfo
     * @param array $tasks
     * @param string $rouType
     * @param array $aCurrentDerivation
     * @return array $arrayDerivationResult
     */
    function beforeDerivate($aDataForPrepareInfo, $tasks, $rouType, $aCurrentDerivation)
    {
        $aPInformation = $this->prepareInformation($aDataForPrepareInfo);
        $oRoute = new \ProcessMaker\Core\RoutingScreen();
        $nextTasks = $oRoute->mergeDataDerivation($tasks, $aPInformation, $rouType);

        //Get all route types
        $aRouteTypes = array();
        foreach ($aPInformation as $key => $value) {
            $aRouteTypes[$key]['ROU_NEXT_TASK'] = $value['ROU_NEXT_TASK'];
            $aRouteTypes[$key]['ROU_TYPE'] = $value['ROU_TYPE'];
        }
        $aCurrentDerivation['ROUTE_TYPES'] = $aRouteTypes;

        //Derivate the case
        $arrayDerivationResult = $this->derivate($aCurrentDerivation, $nextTasks);

        return $arrayDerivationResult;
    }

    /** Route the case
     * If need to create another thread we can execute the doDerivate
     *
     * @param array $currentDelegation
     * @param array $nextDelegations
     * @param bool $removeList
     *
     * @return void
     * @throws /Exception
     */
    function derivate(array $currentDelegation, array $nextDelegations, $removeList = true)
    {
        $this->sysSys = (defined("SYS_SYS"))? SYS_SYS : "Undefined";
        $this->context = Bootstrap::getDefaultContextLog();
        $aContext = $this->context;
        $this->removeList = $removeList;
        $arrayDerivationResult = [];

        //define this...
        if (! defined( 'TASK_FINISH_PROCESS' )) {
            define( 'TASK_FINISH_PROCESS', - 1 );
        }
        if (! defined( 'TASK_FINISH_TASK' )) {
            define( 'TASK_FINISH_TASK', - 2 );
        }

        $this->case = new cases();

        //Get data for this DEL_INDEX current
        $appFields = $this->case->loadCase( $currentDelegation['APP_UID'], $currentDelegation['DEL_INDEX'] );
        $aContext['appUid'] = $currentDelegation['APP_UID'];
        $aContext['delIndex'] = $currentDelegation['DEL_INDEX'];

        unset($appFields['APP_ROUTING_DATA']);

        //We close the current derivation, then we'll try to derivate to each defined route
        $this->case->CloseCurrentDelegation( $currentDelegation['APP_UID'], $currentDelegation['DEL_INDEX'] );

        //Get data for current delegation (current Task)
        $task = TaskPeer::retrieveByPK($currentDelegation["TAS_UID"]);
        $bpmnActivity = BpmnActivityPeer::retrieveByPK($currentDelegation["TAS_UID"]);

        $flagTaskIsMultipleInstance = false;
        $flagTaskAssignTypeIsMultipleInstance = false;

        if (!is_null($task) && !is_null($bpmnActivity)) {
            $flagTaskIsMultipleInstance = $bpmnActivity->getActType() == "TASK" && preg_match("/^(?:EMPTY|USERTASK|MANUALTASK)$/", $bpmnActivity->getActTaskType()) && $bpmnActivity->getActLoopType() == "PARALLEL";
            $flagTaskAssignTypeIsMultipleInstance = preg_match("/^(?:MULTIPLE_INSTANCE|MULTIPLE_INSTANCE_VALUE_BASED)$/", $task->getTasAssignType());
        }

        $currentDelegation["TAS_ASSIGN_TYPE"] = $task->getTasAssignType();
        $currentDelegation["TAS_MI_COMPLETE_VARIABLE"] = $task->getTasMiCompleteVariable();
        $currentDelegation["TAS_MI_INSTANCE_VARIABLE"] = $task->getTasMiInstanceVariable();

        $arrayNextDerivation = array();
        $flagFirstIteration = true;

        foreach ($nextDelegations as $nextDel) {
            //BpmnEvent - END-MESSAGE-EVENT, END-EMAIL-EVENT, INTERMEDIATE-THROW-EMAIL-EVENT
            //Check and get unique id
            if (preg_match("/^(.{32})\/(\-1)$/", $nextDel["TAS_UID"], $arrayMatch)) {
                $nextDel["TAS_UID"] = $arrayMatch[2];
                $nextDel["TAS_UID_DUMMY"] = $arrayMatch[1];
            }

            //Review if is a Subprocesses then we update the $nextDel
            if (!empty($nextDel['TAS_PARENT'])) {
                $subProcessNextDel = $this->getNextInfoSubProcess($nextDel, $appFields['PRO_UID']);
                if(count($subProcessNextDel) > 0) {
                    $nextDel = $subProcessNextDel;
                    $aSP = $this->aSP;
                } else {
                    continue;
                }
            }

            //get open threads
            $openThreads = $this->case->GetOpenThreads( $currentDelegation['APP_UID'] );
            //if we are derivating to finish process but there are no more open thread then we are finishing only the task, we are not finishing the whole process
            if (($nextDel['TAS_UID'] == TASK_FINISH_PROCESS) && (($openThreads + 1) > 1)) {
                $nextDel['TAS_UID'] = TASK_FINISH_TASK;
            }

            $taskNextDel = TaskPeer::retrieveByPK($nextDel["TAS_UID"]); //Get data for next delegation (next Task)
            $bpmnActivityNextDel = BpmnActivityPeer::retrieveByPK($nextDel["TAS_UID"]);

            $flagTaskNextDelIsMultipleInstance = false;
            $flagTaskNextDelAssignTypeIsMultipleInstance = false;

            if (!is_null($taskNextDel) && !is_null($bpmnActivityNextDel)) {
                $flagTaskNextDelIsMultipleInstance = $bpmnActivityNextDel->getActType() == "TASK" && preg_match("/^(?:EMPTY|USERTASK|MANUALTASK)$/", $bpmnActivityNextDel->getActTaskType()) && $bpmnActivityNextDel->getActLoopType() == "PARALLEL";
                $flagTaskNextDelAssignTypeIsMultipleInstance = preg_match("/^(?:MULTIPLE_INSTANCE|MULTIPLE_INSTANCE_VALUE_BASED)$/", $taskNextDel->getTasAssignType());
            }

            $this->flagUpdateList = true;

            $aContext['tasUid'] = $nextDel['TAS_UID'];
            switch ($nextDel['TAS_UID']) {
                case TASK_FINISH_PROCESS:
                    $this->finishProcess(
                        $currentDelegation,
                        $nextDel,
                        $appFields,
                        $flagFirstIteration,
                        $aContext
                    );
                    break;
                case TASK_FINISH_TASK:
                    $this->finishTask(
                        $currentDelegation,
                        $nextDel,
                        $appFields,
                        $flagFirstIteration,
                        $flagTaskAssignTypeIsMultipleInstance,
                        $aContext
                    );
                    break;
                default:
                    //Get all siblingThreads
                    $canDerivate = false;
                    $nextDel['TAS_ID'] = $taskNextDel->getTasId();

                    switch ($currentDelegation['TAS_ASSIGN_TYPE']) {
                        case 'CANCEL_MI':
                        case 'STATIC_MI':
                            $canDerivate = $this->canRouteTasAssignStaticMi($currentDelegation);
                            break;
                        default:
                            $routeType = $currentDelegation["ROU_TYPE"];
                            $routeType = ($flagTaskIsMultipleInstance && $flagTaskAssignTypeIsMultipleInstance)? "SEC-JOIN" : $routeType;
                            switch ($routeType) {
                                case "SEC-JOIN":
                                    $canDerivate = $this->canRouteTypeSecJoin(
                                        $flagTaskIsMultipleInstance,
                                        $flagTaskAssignTypeIsMultipleInstance,
                                        $currentDelegation,
                                        $appFields,
                                        $nextDel
                                    );
                                    $arraySiblings = $this->arraySiblings;
                                    break;
                                default:
                                    $canDerivate = true;
                                    //Check if the previous is a SEC-JOIN and check threads
                                    if(isset($nextDel["ROU_PREVIOUS_TYPE"])){
                                        if($nextDel["ROU_PREVIOUS_TYPE"] == "SEC-JOIN"){
                                            $arrayOpenThread = $this->case->searchOpenPreviousTasks(
                                                $nextDel["ROU_PREVIOUS_TASK"],
                                                $currentDelegation["APP_UID"]
                                            );
                                            $arraySiblings = $this->case->getOpenSiblingThreads(
                                                $nextDel["ROU_PREVIOUS_TASK"],
                                                $currentDelegation["APP_UID"],
                                                $currentDelegation["DEL_INDEX"],
                                                $currentDelegation["TAS_UID"]
                                            );
                                            if(is_array($arrayOpenThread) && is_array($arraySiblings)){
                                               $arrayOpenThread = array_merge($arrayOpenThread, $arraySiblings);
                                            }
                                            $canDerivate = empty($arrayOpenThread);
                                        }
                                    }
                                    break;
                            }
                            break;
                    }

                    if ($canDerivate) {
                        /**
                         * CREATE a new index/thread in the AppDelegation
                         */
                        $nextDel['USR_UID']  = (!isset($nextDel['USR_UID'])) ? '' : $nextDel['USR_UID'];
                        //Throw Events
                        $this->executeEvent(
                            $nextDel["TAS_UID"],
                            $appFields,
                            true,
                            true,
                            $currentDelegation["TAS_UID"]
                        );

                        //Route the case
                        $aSP = (isset($aSP))? $aSP : null;

                        $taskNextDelAssignType = ($flagTaskNextDelIsMultipleInstance && $flagTaskNextDelAssignTypeIsMultipleInstance)? $taskNextDel->getTasAssignType() : "";

                        switch ($taskNextDelAssignType) {
                            case "MULTIPLE_INSTANCE":
                            case "MULTIPLE_INSTANCE_VALUE_BASED":
                                $arrayDerivationResult = $this->routeMultipleInstance(
                                    $currentDelegation,
                                    $nextDel,
                                    $taskNextDel,
                                    $appFields,
                                    $aSP,
                                    $this->removeList
                                );
                                $iNewDelIndex = $this->iNewDelIndex;
                                 break;
                            default:
                                $iNewDelIndex = $this->doDerivation($currentDelegation, $nextDel, $appFields, $aSP);
                                //When the users route the case in the same time
                                if($iNewDelIndex !== 0){
                                    $arrayDerivationResult[] = [
                                        'DEL_INDEX' => $iNewDelIndex,
                                        'TAS_UID' => $nextDel['TAS_UID'],
                                        'USR_UID' => (isset($nextDel['USR_UID']))? $nextDel['USR_UID'] : ''
                                    ];
                                }
                                break;
                        }
                        //Execute Service Task
                        if (function_exists('executeServiceTaskByActivityUid')) {
                            $appFields["APP_DATA"] = executeServiceTaskByActivityUid($nextDel["TAS_UID"], $appFields);
                        }

                        //Execute Script-Task
                        $scriptTask = new \ProcessMaker\BusinessModel\ScriptTask();
                        $appFields["APP_DATA"] = $scriptTask->execScriptByActivityUid($nextDel["TAS_UID"], $appFields);

                        //Create record in table APP_ASSIGN_SELF_SERVICE_VALUE
                        $regexpTaskTypeToExclude = "SCRIPT-TASK|INTERMEDIATE-THROW-EMAIL-EVENT|SERVICE-TASK";
                        if (!is_null($taskNextDel) && !preg_match("/^(?:" . $regexpTaskTypeToExclude . ")$/", $taskNextDel->getTasType())) {
                            $this->createRecordAppSelfServiceValue(
                                $taskNextDel,
                                $iNewDelIndex,
                                $nextDel["TAS_UID"],
                                $appFields
                            );
                        }

                        //Elements with automatic execution
                        $aTaskTypeToInclude = array('SCRIPT-TASK', 'INTERMEDIATE-THROW-EMAIL-EVENT', 'INTERMEDIATE-THROW-MESSAGE-EVENT', 'SERVICE-TASK');
                        //Prepare information when the current is $aTaskTypeToInclude and the next element does not have user
                        if (!is_null($taskNextDel) && in_array($taskNextDel->getTasType(), $aTaskTypeToInclude)) {
                            $arrayNextDerivation[] = $this->routePrepareInformationNextTask(
                                $currentDelegation,
                                $iNewDelIndex,
                                $nextDel
                            );
                        }
                    } else {
                        /**
                         * NO CREATE a new index/thread in the AppDelegation
                         */
                        $this->doRouteWithoutThread($appFields, $currentDelegation, $nextDel, $arraySiblings, $flagTaskIsMultipleInstance, $flagTaskAssignTypeIsMultipleInstance, $flagFirstIteration);
                    }

                    break;
            }
            //$flagUpdateList is updated when is parallel
            if(!is_null($taskNextDel) && $this->flagUpdateList){
                $this->updateList(
                    $currentDelegation,
                    $nextDel,
                    $taskNextDel,
                    $appFields,
                    (isset($iNewDelIndex))? $iNewDelIndex : 0,
                    (isset($aSP))? $aSP : null,
                    $this->removeList
                );
            }

            $this->removeList = false;
            $flagFirstIteration = false;

            unset($aSP);
        }

        /* Start Block : UPDATES APPLICATION */

        //Set THE APP_STATUS
        $appFields['APP_STATUS'] = $currentDelegation['APP_STATUS'];
        /* Start Block : Count the open threads of $currentDelegation['APP_UID'] */
        $openThreads = $this->case->GetOpenThreads( $currentDelegation['APP_UID'] );

        $flagUpdateCase = false;

        //check if there is any paused thread

        $existThreadPaused = false;
        if (isset($arraySiblings['pause'])) {
            if (!empty($arraySiblings['pause'])) {
                $existThreadPaused = true;
            }
        }

        if ($openThreads == 0 && !$existThreadPaused) {
            //Close case
            $appFields["APP_STATUS"] = "COMPLETED";
            $appFields["APP_FINISH_DATE"] = "now";
            $this->verifyIsCaseChild($currentDelegation["APP_UID"], $currentDelegation["DEL_INDEX"]);
            $flagUpdateCase = true;

        }

        //The variable $iNewDelIndex will be true if we created a new index the variable
        if (isset($iNewDelIndex)) {
            $appFields["DEL_INDEX"] = $iNewDelIndex;
            $excludeTasUid = array(TASK_FINISH_PROCESS, TASK_FINISH_TASK);
            //If the last TAS_UID value is not valid we will check for the valid TAS_UID value
            if (in_array($nextDel["TAS_UID"], $excludeTasUid) && is_array($arrayDerivationResult) && isset(current($arrayDerivationResult)["TAS_UID"])) {
                $appFields["TAS_UID"] = current($arrayDerivationResult)["TAS_UID"];
            } else {
                $appFields["TAS_UID"] = $nextDel["TAS_UID"];
            }
            $flagUpdateCase = true;
        }

        if ($flagUpdateCase) {
            //Start Block : UPDATES APPLICATION
            $this->case->updateCase( $currentDelegation["APP_UID"], $appFields );
            //End Block : UPDATES APPLICATION
        }

        //Start the next derivations (Script-Task)
        if (!empty($arrayNextDerivation)) {
            foreach ($arrayNextDerivation as $value) {
                $this->derivate($value["currentDelegation"], $value["nextDelegations"]);
            }
        }

        //Return
        return $arrayDerivationResult;
    }

    /** We can create a new thread in the appDelegation
     * This function is used in derivate
     *
     * @param array $currentDelegation
     * @param array $nextDel
     * @param array $appFields
     * @param array $aSP
     *
     * @return integer $iNewDelIndex
     * @throws /Exception
     */
    function doDerivation ($currentDelegation, $nextDel, $appFields, $aSP = null)
    {
        $case = new \ProcessMaker\BusinessModel\Cases();
        $arrayApplicationData = $case->getApplicationRecordByPk($currentDelegation['APP_UID'], [], false);

        $arrayRoutingData = (!is_null($arrayApplicationData['APP_ROUTING_DATA']) && (string)($arrayApplicationData['APP_ROUTING_DATA']) != '')? unserialize($arrayApplicationData['APP_ROUTING_DATA']) : [];

        $iAppThreadIndex = $appFields['DEL_THREAD'];
        $delType = 'NORMAL';
        $sendNotifications = false;
        $sendNotificationsMobile = false;

        $appDelegation = new AppDelegation();
        $taskNextDel = TaskPeer::retrieveByPK($nextDel["TAS_UID"]);

        $arrayAppDelegationPrevious = $appDelegation->getPreviousDelegationValidTask($currentDelegation['APP_UID'], $currentDelegation['DEL_INDEX'], true);

        $taskUidOrigin = $arrayAppDelegationPrevious['TAS_UID'];
        $taskUidDest   = $taskNextDel->getTasUid();

        if (array_key_exists($taskUidOrigin . '/' . $taskUidDest, $arrayRoutingData)) {
            if(isset($arrayRoutingData[$taskUidOrigin . '/' . $taskUidDest]['USR_UID'])){
                $nextDel['USR_UID'] = $arrayRoutingData[$taskUidOrigin . '/' . $taskUidDest]['USR_UID'];
            }
            unset($arrayRoutingData[$taskUidOrigin . '/' . $taskUidDest]);
        }

        if ($taskNextDel->getTasType() == 'NORMAL' &&
            $taskNextDel->getTasAssignType() != 'SELF_SERVICE' &&
            (is_null($nextDel['USR_UID']) || $nextDel['USR_UID'] == '')
        ) {
            throw new Exception(G::LoadTranslation('ID_NO_USERS'));
        }

        if (is_numeric( $nextDel['DEL_PRIORITY'] )) {
            $nextDel['DEL_PRIORITY'] = (isset( $nextDel['DEL_PRIORITY'] ) ? ($nextDel['DEL_PRIORITY'] >= 1 && $nextDel['DEL_PRIORITY'] <= 5 ? $nextDel['DEL_PRIORITY'] : '3') : '3');
        } else {
            $nextDel['DEL_PRIORITY'] = 3;
        }

        switch ($nextDel['TAS_ASSIGN_TYPE']) {
            case 'CANCEL_MI':
            case 'STATIC_MI':
                // Create new delegation depending on the no of users in the group
                $iNewDelIndex = $this->doDerivationStaticMi(
                    $appFields,
                    $currentDelegation,
                    $nextDel,
                    $iAppThreadIndex,
                    $delType
                );
                break;
            case 'BALANCED':
                $this->setTasLastAssigned($nextDel['TAS_UID'], $nextDel['USR_UID']);
                //No Break, need no execute the default ones....
            default:
                $delPrevious = 0;
                if($this->flagControlMulInstance){
                    $criteriaMulti = new Criteria("workflow");
                    $criteriaMulti->addSelectColumn(AppDelegationPeer::DEL_PREVIOUS);
                    $criteriaMulti->add(AppDelegationPeer::TAS_UID, $currentDelegation['TAS_UID'], Criteria::EQUAL);
                    $criteriaMultiR = AppDelegationPeer::doSelectRS($criteriaMulti);
                    $criteriaMultiR->setFetchmode(ResultSet::FETCHMODE_ASSOC);
                    $criteriaMultiR->next();
                    $row = $criteriaMultiR->getRow();
                    $delPrevious = $row['DEL_PREVIOUS'];
                }
                // Create new delegation
                $iNewDelIndex = $this->case->newAppDelegation(
                    $appFields['PRO_UID'],
                    $currentDelegation['APP_UID'],
                    $nextDel['TAS_UID'],
                    $this->verifyCurrentUserInTask($nextDel, $aSP),
                    $currentDelegation['DEL_INDEX'],
                    $nextDel['DEL_PRIORITY'],
                    $delType,
                    $iAppThreadIndex,
                    $nextDel,
                    $this->flagControl,
                    $this->flagControlMulInstance,
                    $delPrevious,
                    $appFields['APP_NUMBER'],
                    $appFields['PRO_ID'],
                    $nextDel['TAS_ID']
                );
                break;
        }

        if (array_key_exists('NEXT_ROUTING', $nextDel) && is_array($nextDel['NEXT_ROUTING']) && !empty($nextDel['NEXT_ROUTING'])) {
            if (array_key_exists('TAS_UID', $nextDel['NEXT_ROUTING'])) {
                $arrayRoutingData[$currentDelegation['TAS_UID'] . '/' . $nextDel['NEXT_ROUTING']['TAS_UID']] = $nextDel['NEXT_ROUTING'];
            } else {
                foreach ($nextDel['NEXT_ROUTING'] as $value) {
                    $arrayRoutingData[$currentDelegation['TAS_UID'] . '/' . $value['TAS_UID']] = $value;
                }
            }
        }

        $application = new Application();
        $result = $application->update(['APP_UID' => $currentDelegation['APP_UID'], 'APP_ROUTING_DATA' => serialize($arrayRoutingData)]);

        //We updated the information relate to APP_THREAD
        $iAppThreadIndex = $appFields['DEL_THREAD'];
        $isUpdatedThread = false;
        if (isset($currentDelegation['ROUTE_TYPES']) && sizeof($currentDelegation['ROUTE_TYPES']) > 1) {
            //If the next is more than one thread: Parallel or other
            foreach ($currentDelegation['ROUTE_TYPES'] as $key => $value) {
                if ($value['ROU_NEXT_TASK'] === $nextDel['TAS_UID']) {
                    $isUpdatedThread = true;
                    $routeType = ($value['ROU_TYPE'] === 'EVALUATE') ? 'PARALLEL-AND-EXCLUSIVE' : $value['ROU_TYPE'];
                    $this->updateAppThread($routeType, $currentDelegation['APP_UID'], $iAppThreadIndex, $iNewDelIndex);
                }
            }
        }
        if (!$isUpdatedThread) {
            //If the next is a sequential derivation
            $this->updateAppThread($currentDelegation['ROU_TYPE'], $currentDelegation['APP_UID'], $iAppThreadIndex, $iNewDelIndex);
        }

        //if there are SubProcess to create
        if (isset($aSP)) {
            //Check if is SelfService the task in the SubProcess
            $isSelfService = (empty($aSP['USR_UID'])) ? true : false;

            //Create the new case in the sub-process
            //Set the initial date to null the time its created
            $aNewCase = $this->case->startCase( $aSP['TAS_UID'], $aSP['USR_UID'], true, $appFields, $isSelfService);

            //Load the TAS_UID related to the SubProcess
            $taskNextDel = TaskPeer::retrieveByPK($aSP["TAS_UID"]); //Sub-Process

            //Copy case variables to sub-process case
            $aFields = unserialize( $aSP['SP_VARIABLES_OUT'] );
            $aNewFields = array ();
            $aOldFields = $this->case->loadCase( $aNewCase['APPLICATION'] );

            foreach ($aFields as $sOriginField => $sTargetField) {
                $sOriginField = trim($sOriginField, " @#%?$=");
                $sTargetField = trim($sTargetField, " @#%?$=");

                $aNewFields[$sTargetField] = isset( $appFields['APP_DATA'][$sOriginField] ) ? $appFields['APP_DATA'][$sOriginField] : '';

                if (array_key_exists($sOriginField . '_label', $appFields['APP_DATA'])) {
                    $aNewFields[$sTargetField . '_label'] = $appFields['APP_DATA'][$sOriginField . '_label'];
                }
            }

            //We will to update the new case
            $aOldFields['APP_DATA'] = array_merge( $aOldFields['APP_DATA'], $aNewFields );
            $aOldFields['APP_STATUS'] = 'TO_DO';
            $this->case->updateCase(
                $aNewCase['APPLICATION'],
                $aOldFields
            );

            //Create a registry in SUB_APPLICATION table
            $aSubApplication = array (
                'APP_UID' => $aNewCase['APPLICATION'],
                'APP_PARENT' => $currentDelegation['APP_UID'],
                'DEL_INDEX_PARENT' => $iNewDelIndex,
                'DEL_THREAD_PARENT' => $iAppThreadIndex,
                'SA_STATUS' => 'ACTIVE',
                'SA_VALUES_OUT' => serialize($aNewFields),
                'SA_INIT_DATE' => date('Y-m-d H:i:s')
            );
            if ($aSP['SP_SYNCHRONOUS'] == 0) {
                $aSubApplication['SA_STATUS'] = 'FINISHED';
                $aSubApplication['SA_FINISH_DATE'] = $aSubApplication['SA_INIT_DATE'];
            }
            $oSubApplication = new SubApplication();
            $oSubApplication->create( $aSubApplication );

            //Update the AppDelegation to execute the update trigger
            $AppDelegation = AppDelegationPeer::retrieveByPK( $aNewCase['APPLICATION'], $aNewCase['INDEX'] );
            $AppDelegation->save();

            //Create record in table APP_ASSIGN_SELF_SERVICE_VALUE
            if ($taskNextDel->getTasAssignType() == "SELF_SERVICE" && trim($taskNextDel->getTasGroupVariable()) != "") {
                $nextTaskGroupVariable = trim($taskNextDel->getTasGroupVariable(), " @#");

                if (isset($aOldFields["APP_DATA"][$nextTaskGroupVariable])) {
                    $dataVariable = $aOldFields["APP_DATA"][$nextTaskGroupVariable];
                    $dataVariable = (is_array($dataVariable))? $dataVariable : trim($dataVariable);

                    if (!empty($dataVariable)) {
                        $appAssignSelfServiceValue = new AppAssignSelfServiceValue();

                        $appAssignSelfServiceValue->create($aNewCase["APPLICATION"], $aNewCase["INDEX"], array("PRO_UID" => $aNewCase["PROCESS"], "TAS_UID" => $aSP["TAS_UID"], "GRP_UID" => ""), $dataVariable);
                    }
                }
            }

            //We will to send the notifications
            $sendNotificationsMobile = $this->sendNotificationsMobile($aOldFields, $aSP, $aNewCase['INDEX']);
            $nextTaskData = $taskNextDel->toArray(BasePeer::TYPE_FIELDNAME);
            $nextTaskData['USR_UID'] = $aSP['USR_UID'];
            $sendNotifications = $this->notifyAssignedUser($appFields, $nextTaskData, $aNewCase['INDEX']);

            //If is ASYNCHRONOUS we will to route the case master
            if ($aSP['SP_SYNCHRONOUS'] == 0) {
                $this->case->setDelInitDate( $currentDelegation['APP_UID'], $iNewDelIndex );
                $aDeriveTasks = $this->prepareInformation(
                    array (
                        'USER_UID' => -1,
                        'APP_UID' => $currentDelegation['APP_UID'],
                        'DEL_INDEX' => $iNewDelIndex
                    )
                );

                if (isset($aDeriveTasks[1])) {
                    if ($aDeriveTasks[1]['ROU_TYPE'] != 'SELECT') {
                        $nextDelegations2 = array();
                        foreach ($aDeriveTasks as $aDeriveTask) {
                            $nextDelegations2[] = array(
                                'TAS_UID' => $aDeriveTask['NEXT_TASK']['TAS_UID'],
                                'USR_UID' => $aDeriveTask['NEXT_TASK']['USER_ASSIGNED']['USR_UID'],
                                'TAS_ASSIGN_TYPE' => $aDeriveTask['NEXT_TASK']['TAS_ASSIGN_TYPE'],
                                'TAS_DEF_PROC_CODE' => $aDeriveTask['NEXT_TASK']['TAS_DEF_PROC_CODE'],
                                'DEL_PRIORITY' => 3,
                                'TAS_PARENT' => $aDeriveTask['NEXT_TASK']['TAS_PARENT'],
                                'ROU_PREVIOUS_TYPE' => isset($aDeriveTask['NEXT_TASK']['ROU_PREVIOUS_TYPE']) ? $aDeriveTask['NEXT_TASK']['ROU_PREVIOUS_TYPE'] : '',
                                'ROU_PREVIOUS_TASK' => isset($aDeriveTask['NEXT_TASK']['ROU_PREVIOUS_TASK']) ? $aDeriveTask['NEXT_TASK']['ROU_PREVIOUS_TASK'] : ''
                            );
                        }
                        $currentDelegation2 = array(
                            'APP_UID' => $currentDelegation['APP_UID'],
                            'DEL_INDEX' => $iNewDelIndex,
                            'APP_STATUS' => 'TO_DO',
                            'TAS_UID' => $currentDelegation['TAS_UID'],
                            'ROU_TYPE' => $aDeriveTasks[1]['ROU_TYPE'],

                        );
                        $openThreads = 0;
                        if ($currentDelegation2['ROU_TYPE'] == 'SEC-JOIN') {
                            $openThreads = $this->case->GetOpenThreads($currentDelegation['APP_UID']);
                        }
                        if ($openThreads == 0) {
                            $this->derivate($currentDelegation2, $nextDelegations2);
                        } else {
                            $oSubApplication = new SubApplication();
                            $aSubApplication['SA_STATUS'] = 'ACTIVE';
                            $oSubApplication->update($aSubApplication);
                        }
                    }
                }
            }
        }

        if ($iNewDelIndex !== 0 && !$sendNotificationsMobile) {
            $this->sendNotificationsMobile($appFields, $nextDel, $iNewDelIndex);
        }
        if ($iNewDelIndex !== 0 && !$sendNotifications) {
            $nextTaskData = $taskNextDel->toArray(BasePeer::TYPE_FIELDNAME);
            $nextTaskData['USR_UID'] = $nextDel['USR_UID'];
            $this->notifyAssignedUser($appFields, $nextTaskData, $iNewDelIndex);
        }
        return $iNewDelIndex;
    }

    /**
     * This function returns the current user Checking cases where USR_ID exists or checking a subprocess (SYNCHRONOUS)
     * @param $nextDel
     * @param null $aSP
     * @return string
     */
    function verifyCurrentUserInTask($nextDel, $aSP = null)
    {
        if (isset($aSP) && $aSP["SP_SYNCHRONOUS"] == "1") {
            $currentUser = "";
        } else {
            $currentUser = isset($nextDel['USR_UID']) ? $nextDel['USR_UID'] : "";
        }
        return $currentUser;
    }

    /**
     * This function create, update and closed a new record related to appThread
     *
     * Related to route type we can change the records in the APP_THREAD table
     * @param  string $routeType this variable recibe information about the derivation
     * @return void
     */
    function updateAppThread($routeType, $appUid, $iAppThreadIndex, $iNewDelIndex) {
        switch ($routeType) {
            case 'PARALLEL':
            case 'PARALLEL-BY-EVALUATION':
            case 'PARALLEL-AND-EXCLUSIVE':
                $this->case->closeAppThread($appUid, $iAppThreadIndex);
                $iNewThreadIndex = $this->case->newAppThread($appUid, $iNewDelIndex, $iAppThreadIndex);
                $this->case->updateAppDelegation($appUid, $iNewDelIndex, $iNewThreadIndex);
                break;
            default:
                $this->case->updateAppThread($appUid, $iAppThreadIndex, $iNewDelIndex);
                break;
        }
    }

    /* verifyIsCaseChild
     *
     * @param   string   $sApplicationUID
     * @return  void
     */
    function verifyIsCaseChild ($sApplicationUID, $delIndex = 0)
    {
        //Obtain the related row in the table SUB_APPLICATION
        $oCriteria = new Criteria( 'workflow' );
        $oCriteria->add( SubApplicationPeer::APP_UID, $sApplicationUID );
        $oDataset = SubApplicationPeer::doSelectRS( $oCriteria );
        $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
        $oDataset->next();
        $aSA = $oDataset->getRow();
        if ($aSA) {
            //Obtain the related row in the table SUB_PROCESS
            $oCase = new Cases();
            $aParentCase = $oCase->loadCase( $aSA['APP_PARENT'], $aSA['DEL_INDEX_PARENT'] );
            $oCriteria = new Criteria( 'workflow' );
            $oCriteria->add( SubProcessPeer::PRO_PARENT, $aParentCase['PRO_UID'] );
            $oCriteria->add( SubProcessPeer::TAS_PARENT, $aParentCase['TAS_UID'] );
            $oDataset = SubProcessPeer::doSelectRS( $oCriteria );
            $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
            $oDataset->next();
            $aSP = $oDataset->getRow();
            if ($aSP['SP_SYNCHRONOUS'] == 1 || $aSA['SA_STATUS'] == "ACTIVE") {
                $appFields = $oCase->loadCase($sApplicationUID, $delIndex);
                //Copy case variables to parent case
                $aFields = unserialize($aSP['SP_VARIABLES_IN']);
                $aNewFields = array();
                foreach ($aFields as $sOriginField => $sTargetField) {
                    $sOriginField = str_replace('@', '', $sOriginField);
                    $sOriginField = str_replace('#', '', $sOriginField);
                    $sOriginField = str_replace('%', '', $sOriginField);
                    $sOriginField = str_replace('?', '', $sOriginField);
                    $sOriginField = str_replace('$', '', $sOriginField);
                    $sOriginField = str_replace('=', '', $sOriginField);
                    $sTargetField = str_replace('@', '', $sTargetField);
                    $sTargetField = str_replace('#', '', $sTargetField);
                    $sTargetField = str_replace('%', '', $sTargetField);
                    $sTargetField = str_replace('?', '', $sTargetField);
                    $sTargetField = str_replace('$', '', $sTargetField);
                    $sTargetField = str_replace('=', '', $sTargetField);
                    $aNewFields[$sTargetField] = isset($appFields['APP_DATA'][$sOriginField]) ? $appFields['APP_DATA'][$sOriginField] : '';

                    if (array_key_exists($sOriginField . '_label', $appFields['APP_DATA'])) {
                        $aNewFields[$sTargetField . '_label'] = $appFields['APP_DATA'][$sOriginField . '_label'];
                    } else {
                        if (array_key_exists($sTargetField . '_label', $aParentCase['APP_DATA'])) {
                            $aNewFields[$sTargetField . '_label'] = '';
                        }
                    }
                }
                $aParentCase['APP_DATA'] = array_merge($aParentCase['APP_DATA'], $aNewFields);
                $oCase->updateCase($aSA['APP_PARENT'], $aParentCase);
                /*----------------------------------********---------------------------------*/

                //Update table SUB_APPLICATION
                $oSubApplication = new SubApplication();
                $oSubApplication->update(array('APP_UID' => $sApplicationUID, 'APP_PARENT' => $aSA['APP_PARENT'], 'DEL_INDEX_PARENT' => $aSA['DEL_INDEX_PARENT'], 'DEL_THREAD_PARENT' => $aSA['DEL_THREAD_PARENT'], 'SA_STATUS' => 'FINISHED', 'SA_VALUES_IN' => serialize($aNewFields), 'SA_FINISH_DATE' => date('Y-m-d H:i:s')
                ));

                //Derive the parent case
                $aDeriveTasks = $this->prepareInformation(array('USER_UID' => -1, 'APP_UID' => $aSA['APP_PARENT'], 'DEL_INDEX' => $aSA['DEL_INDEX_PARENT']
                ));
                if (isset($aDeriveTasks[1])) {
                    if ($aDeriveTasks[1]['ROU_TYPE'] != 'SELECT') {
                        $nextDelegations2 = array();
                        foreach ($aDeriveTasks as $aDeriveTask) {
                            if (!isset($aDeriveTask['NEXT_TASK']['USER_ASSIGNED']['USR_UID'])) {
                                $selectedUser = $aDeriveTask['NEXT_TASK']['USER_ASSIGNED'][0];
                                unset($aDeriveTask['NEXT_TASK']['USER_ASSIGNED']);
                                $aDeriveTask['NEXT_TASK']['USER_ASSIGNED'] = $selectedUser;
                                $myLabels = array($aDeriveTask['NEXT_TASK']['TAS_TITLE'], $aParentCase['APP_NUMBER'], $selectedUser['USR_USERNAME'], $selectedUser['USR_FIRSTNAME'], $selectedUser['USR_LASTNAME']
                                );
                                if ($aDeriveTask['NEXT_TASK']['TAS_ASSIGN_TYPE'] == "MANUAL") {
                                    G::SendTemporalMessage('ID_TASK_WAS_ASSIGNED_TO_USER', 'warning', 'labels', 10, null, $myLabels);
                                }

                            }
                            $nextDelegations2[] = array(
                                'TAS_UID' => $aDeriveTask['NEXT_TASK']['TAS_UID'],
                                'USR_UID' => $aDeriveTask['NEXT_TASK']['USER_ASSIGNED']['USR_UID'],
                                'TAS_ASSIGN_TYPE' => $aDeriveTask['NEXT_TASK']['TAS_ASSIGN_TYPE'],
                                'TAS_DEF_PROC_CODE' => $aDeriveTask['NEXT_TASK']['TAS_DEF_PROC_CODE'],
                                'DEL_PRIORITY' => 3,
                                'TAS_PARENT' => $aDeriveTask['NEXT_TASK']['TAS_PARENT'],
                                'ROU_PREVIOUS_TASK' => isset($aDeriveTask['NEXT_TASK']['ROU_PREVIOUS_TASK']) ? $aDeriveTask['NEXT_TASK']['ROU_PREVIOUS_TASK'] : '',
                                'ROU_PREVIOUS_TYPE' => isset($aDeriveTask['NEXT_TASK']['ROU_PREVIOUS_TYPE']) ? $aDeriveTask['NEXT_TASK']['ROU_PREVIOUS_TYPE'] : ''
                            );
                        }
                        $currentDelegation2 = array('APP_UID' => $aSA['APP_PARENT'], 'DEL_INDEX' => $aSA['DEL_INDEX_PARENT'], 'APP_STATUS' => 'TO_DO', 'TAS_UID' => $aParentCase['TAS_UID'], 'ROU_TYPE' => $aDeriveTasks[1]['ROU_TYPE']
                        );
                        $this->derivate($currentDelegation2, $nextDelegations2);

                        if ($delIndex > 0) {
                            $flagNotification = false;
                            if ($appFields["CURRENT_USER_UID"] == '') {
                                $oCriteriaTaskDummy = new Criteria('workflow');
                                $oCriteriaTaskDummy->add(TaskPeer::PRO_UID, $appFields['PRO_UID']);
                                $oCriteriaTaskDummy->add(TaskPeer::TAS_UID, $appFields['TAS_UID']);
                                $oCriteriaTaskDummy->add(
                                    $oCriteriaTaskDummy->getNewCriterion(TaskPeer::TAS_TYPE, 'SCRIPT-TASK', Criteria::EQUAL)->addOr(
                                        $oCriteriaTaskDummy->getNewCriterion(TaskPeer::TAS_TYPE, 'INTERMEDIATE-THROW-EMAIL-EVENT', Criteria::EQUAL))
                                );
                                $oCriteriaTaskDummy->setLimit(1);
                                $oDataset = AppDelegationPeer::doSelectRS($oCriteriaTaskDummy);
                                $oDataset->setFetchmode(\ResultSet::FETCHMODE_ASSOC);
                                $oDataset->next();
                                if ($row = $oDataset->getRow()) {
                                    $flagNotification = true;
                                }
                            }
                            if (!$flagNotification) {
                                // Send notifications - Start
                                $oUser = new Users();
                                $aUser = $oUser->load($appFields["CURRENT_USER_UID"]);

                                $sFromName = $aUser["USR_FIRSTNAME"] . " " . $aUser["USR_LASTNAME"] . ($aUser["USR_EMAIL"] != "" ? " <" . $aUser["USR_EMAIL"] . ">" : "");

                                try {
                                    $oCase->sendNotifications($appFields["TAS_UID"],
                                        $nextDelegations2,
                                        $appFields["APP_DATA"],
                                        $sApplicationUID,
                                        $delIndex,
                                        $sFromName);

                                } catch (Exception $e) {
                                    G::SendTemporalMessage(G::loadTranslation("ID_NOTIFICATION_ERROR") . " - " . $e->getMessage(), "warning", "string", null, "100%");
                                }
                                // Send notifications - End
                            }
                        }
                    }
                }
            }
        }
    }

    /*  getDerivatedCases
     *  get all derivated cases and subcases from any task,
     *  this function is useful to know who users have been assigned and what task they do.
     *
     * @param   string   $sParentUid
     * @param   string   $sDelIndexParent
     * @return  array    $derivation
     *
     */
    function getDerivatedCases ($sParentUid, $sDelIndexParent)
    {
        $oCriteria = new Criteria( 'workflow' );
        $cases = array ();
        $derivation = array ();
        //get the child delegations , of parent delIndex
        $children = array ();
        $oCriteria->clearSelectColumns();
        $oCriteria->addSelectColumn( AppDelegationPeer::DEL_INDEX );
        $oCriteria->add( AppDelegationPeer::APP_UID, $sParentUid );
        $oCriteria->add( AppDelegationPeer::DEL_PREVIOUS, $sDelIndexParent );
        $oDataset = AppDelegationPeer::doSelectRS( $oCriteria );
        $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
        $oDataset->next();
        $aRow = $oDataset->getRow();
        while (is_array( $aRow )) {
            $children[] = $aRow['DEL_INDEX'];

            $oDataset->next();
            $aRow = $oDataset->getRow();
        }

        //foreach child , get the info of their derivations and subprocesses
        foreach ($children as $keyChild => $child) {
            $oCriteria = new Criteria( 'workflow' );
            $oCriteria->clearSelectColumns();
            $oCriteria->addSelectColumn( SubApplicationPeer::APP_UID );
            $oCriteria->addSelectColumn( AppDelegationPeer::APP_UID );
            $oCriteria->addSelectColumn( AppDelegationPeer::DEL_INDEX );
            $oCriteria->addSelectColumn( AppDelegationPeer::PRO_UID );
            $oCriteria->addSelectColumn( AppDelegationPeer::TAS_UID );
            $oCriteria->addSelectColumn( AppDelegationPeer::USR_UID );
            $oCriteria->addSelectColumn( UsersPeer::USR_USERNAME );
            $oCriteria->addSelectColumn( UsersPeer::USR_FIRSTNAME );
            $oCriteria->addSelectColumn( UsersPeer::USR_LASTNAME );

            $oCriteria->add( SubApplicationPeer::APP_PARENT, $sParentUid );
            $oCriteria->add( SubApplicationPeer::DEL_INDEX_PARENT, $child );
            $oCriteria->addJoin( SubApplicationPeer::APP_UID, AppDelegationPeer::APP_UID );
            $oCriteria->addJoin( AppDelegationPeer::USR_UID, UsersPeer::USR_UID );
            $oDataset = SubApplicationPeer::doSelectRS( $oCriteria );
            $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
            $oDataset->next();
            $aRow = $oDataset->getRow();
            while (is_array( $aRow )) {
                $oProcess = new Process();
                $proFields = $oProcess->load( $aRow['PRO_UID'] );
                $oCase = new Application();
                $appFields = $oCase->load( $aRow['APP_UID'] );
                $oTask = new Task();
                $tasFields = $oTask->load( $aRow['TAS_UID'] );
                $derivation[] = array ('processId' => $aRow['PRO_UID'],'processTitle' => $proFields['PRO_TITLE'],'caseId' => $aRow['APP_UID'],'caseNumber' => $appFields['APP_NUMBER'],'taskId' => $aRow['TAS_UID'],'taskTitle' => $tasFields['TAS_TITLE'],'userId' => $aRow['USR_UID'],'userName' => $aRow['USR_USERNAME'],'userFullname' => $aRow['USR_FIRSTNAME'] . ' ' . $aRow['USR_LASTNAME']
                );

                $oDataset->next();
                $aRow = $oDataset->getRow();
            }
        }

        return $derivation;
    }

    function getGrpUser ($aData)
    {
        $oTasks = new Tasks();
        $oGroups = new Groups();
        $oContent = new Content();
        $aGroup = array ();
        $aUsers = array ();
        $aGroup = $oTasks->getGroupsOfTask( $aData['ROU_NEXT_TASK'], 1 );
        $aGrpUid = $aGroup[0]['GRP_UID'];
        $sGrpName = $oContent->load( 'GRP_TITLE', '', $aGrpUid, 'en' );
        $aGrp['GRP_NAME'] = $sGrpName;
        $aGrp['GRP_UID'] = $aGrpUid;
        $aUsers = $oGroups->getUsersOfGroup( $aGroup[0]['GRP_UID'] );
        foreach ($aUsers as $aKey => $userid) {
            $aData[$aKey] = $userid;
        }
        return $aGrp;
    }

    function checkReplacedByUser ($user)
    {
        if (is_string( $user )) {
            $userInstance = UsersPeer::retrieveByPK( $user );
        } else {
            $userInstance = $user;
        }
        if (! is_object( $userInstance )) {
            throw new Exception( "The user with the UID '$user' doesn't exist." );
        }
        if ($userInstance->getUsrStatus() == 'ACTIVE') {
            return $userInstance->getUsrUid();
        } else {
            $userReplace = trim( $userInstance->getUsrReplacedBy() );
            if ($userReplace != '') {
                return $this->checkReplacedByUser( UsersPeer::retrieveByPK( $userReplace ) );
            } else {
                return '';
            }
        }
    }

    /**
     * @param $appFields
     * @param $nextDel
     * @param $iNewDelIndex
     * @return bool
     */
    private function sendNotificationsMobile($appFields, $nextDel, $iNewDelIndex)
    {
        try {
            $notificationMobile = new \ProcessMaker\BusinessModel\Light\NotificationDevice();
            if ($notificationMobile->checkMobileNotifications()) {
                $notificationMobile->routeCaseNotificationDevice($appFields, $nextDel, $iNewDelIndex);
            }
            return true;
        } catch (Exception $e) {
            \G::log(G::loadTranslation('ID_NOTIFICATION_ERROR') . '|' . $e->getMessage(), PATH_DATA, "mobile.log");
        }
    }

    /**
     * @param $appFields
     * @param $nextDel
     * @param $iNewDelIndex
     * @return bool
     */
    public function notifyAssignedUser($appFields, $nextDel, $iNewDelIndex)
    {
        try {
            if ($nextDel['TAS_RECEIVE_LAST_EMAIL'] == 'TRUE') {
                $taskData = array();
                $userLogged = $this->userLogged->load($appFields['APP_DATA']['USER_LOGGED']);
                $fromName = $userLogged['USR_FIRSTNAME'] . ' ' . $userLogged['USR_LASTNAME'];
                $sFromData = $fromName . ($userLogged['USR_EMAIL'] != '' ? ' <' . $userLogged['USR_EMAIL'] . '>' : '');
                $dataEmail = $this->case->loadDataSendEmail($nextDel, $appFields['APP_DATA'], $sFromData, 'RECEIVE');
                $dataEmail['applicationUid'] = $appFields['APP_UID'];
                $dataEmail['delIndex'] = $iNewDelIndex;
                array_push($taskData, $nextDel);
                $this->case->sendMessage($dataEmail, $appFields['APP_DATA'], $taskData);
            }
            return true;
        } catch (Exception $e) {
            \G::log(G::loadTranslation('ID_NOTIFICATION_ERROR') . '|' . $e->getMessage());
        }
    }

    /**
     * @param array $currentDelegation
     * @param array $nextDel
     * @param array $appFields
     * @param boolean $flagFirstIteration
     * @param boolean $flagTaskAssignTypeIsMultipleInstance
     * @param array $aContext
     * @return void
     */
    public function finishTask($currentDelegation, $nextDel, $appFields, $flagFirstIteration = true, $flagTaskAssignTypeIsMultipleInstance = false, $aContext = array()) {
        $iAppThreadIndex = $appFields['DEL_THREAD'];
        $this->case->closeAppThread($currentDelegation['APP_UID'], $iAppThreadIndex);
        if (isset($nextDel["TAS_UID_DUMMY"])) {
            $criteria = new Criteria("workflow");
            $criteria->addSelectColumn(RoutePeer::TAS_UID);
            $criteria->addJoin(RoutePeer::TAS_UID, AppDelegationPeer::TAS_UID);
            $criteria->add(RoutePeer::PRO_UID, $appFields['PRO_UID']);
            $criteria->add(RoutePeer::ROU_NEXT_TASK, isset($nextDel['ROU_PREVIOUS_TASK']) ? $nextDel['ROU_PREVIOUS_TASK'] : '');
            $criteria->add(RoutePeer::ROU_TYPE, isset($nextDel['ROU_PREVIOUS_TYPE']) ? $nextDel['ROU_PREVIOUS_TYPE'] : '');
            $criteria->add(AppDelegationPeer::DEL_THREAD_STATUS, 'OPEN');
            $rsCriteria = RoutePeer::doSelectRS($criteria);
            $rsCriteria->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $executeEvent = ($rsCriteria->next()) ? false : true;

            $multiInstanceCompleted = true;
            if ($flagTaskAssignTypeIsMultipleInstance) {
                $multiInstanceCompleted = $this->case->multiInstanceIsCompleted(
                    $appFields['APP_UID'],
                    $appFields['TAS_UID'],
                    $appFields['DEL_PREVIOUS']);
            }

            $taskDummy = TaskPeer::retrieveByPK($nextDel["TAS_UID_DUMMY"]);
            if (preg_match("/^(?:END-MESSAGE-EVENT|END-EMAIL-EVENT)$/", $taskDummy->getTasType())
                && $multiInstanceCompleted && $executeEvent
            ) {
                $this->executeEvent($nextDel["TAS_UID_DUMMY"], $appFields, $flagFirstIteration, true);
            }
        }
        $aContext['action'] = 'finish-task';
        //Logger
        Bootstrap::registerMonolog('CaseDerivation', 200, 'Case Derivation', $aContext, $this->sysSys, 'processmaker.log');
    }

    /**
     * Finish the process
     * Close the threads and update the status to COMPLETED
     * @param array $currentDelegation
     * @param array $nextDel
     * @param array $appFields
     * @param boolean $flagFirstIteration
     * @param array $aContext
     * @return void
     */
    public function finishProcess($currentDelegation, $nextDel, $appFields, $flagFirstIteration = true, $aContext = array()){
        /*Close all delegations of $currentDelegation['APP_UID'] */
        $this->case->closeAllDelegations( $currentDelegation['APP_UID'] );
        $this->case->closeAllThreads( $currentDelegation['APP_UID'] );
        //I think we need to change the APP_STATUS to completed,
        if (!isset($nextDel['ROU_CONDITION'])) {
            $nextDel['ROU_CONDITION'] = '';
        }
        //Execute the Intermediate Event After the End of Process
        $this->executeEvent($nextDel["TAS_UID"], $appFields, true, true);
        if (isset($nextDel["TAS_UID_DUMMY"]) ) {
            $taskDummy = TaskPeer::retrieveByPK($nextDel["TAS_UID_DUMMY"]);
            if (preg_match("/^(?:END-MESSAGE-EVENT|END-EMAIL-EVENT)$/", $taskDummy->getTasType())) {
                //Throw Events
                $this->executeEvent($nextDel["TAS_UID_DUMMY"], $appFields, $flagFirstIteration, true);
            }
        }
        $aContext['action'] = 'end-process';
        //Logger
        Bootstrap::registerMonolog('CaseDerivation', 200, 'Case Derivation', $aContext, $this->sysSys, 'processmaker.log');
    }

    /**
     * Get the next route information when is a subProcess
     * This function changes the following important variables:
     * $aSP (array) with the subProcess information
     * $newNextDel (array) the next delegation related to the subProcess
     * @param array $nextDel
     * @param array $proUid
     * @return array $newNextDel
    */
    public function getNextInfoSubProcess($nextDel, $proUid)
    {
        $newNextDel = array();
        $oCriteria = new Criteria('workflow');
        $oCriteria->add(SubProcessPeer::PRO_PARENT, $proUid);
        $oCriteria->add(SubProcessPeer::TAS_PARENT, $nextDel['TAS_PARENT']);
        $oDataset = SubProcessPeer::doSelectRS( $oCriteria );
        $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
        $oDataset->next();
        $aSP = $oDataset->getRow();
        if (is_array($aSP)) {
            $this->aSP = $aSP;
            $oTask = new Task();
            $aTaskNext = $oTask->load($nextDel['TAS_UID']);
            //When is MULTIPLE_INSTANCE catch the first user
            if($aTaskNext["TAS_ASSIGN_TYPE"] == "MULTIPLE_INSTANCE"){
                $spUserUid = $this->getAllUsersFromAnyTask($nextDel["TAS_UID"]);
                foreach($spUserUid as $row){
                    $firstUserUid = $row;
                    continue;
                }
                $this->aSP['USR_UID'] = $firstUserUid;
            }else{
                $this->aSP['USR_UID'] = $nextDel['USR_UID'];
            }
            $aTask = $oTask->load($nextDel['TAS_PARENT']);
            $newNextDel = array (
                'TAS_UID' => $aTask['TAS_UID'],
                'USR_UID' => $this->aSP['USR_UID'],
                'TAS_ASSIGN_TYPE' => $aTask['TAS_ASSIGN_TYPE'],
                'TAS_DEF_PROC_CODE' => $aTask['TAS_DEF_PROC_CODE'],
                'DEL_PRIORITY' => 3,
                'TAS_PARENT' => '',
                'ROU_PREVIOUS_TYPE' => isset($nextDel['ROU_PREVIOUS_TYPE']) ? $nextDel['ROU_PREVIOUS_TYPE'] : '',
                'ROU_PREVIOUS_TASK' => isset($nextDel['ROU_PREVIOUS_TASK']) ? $nextDel['ROU_PREVIOUS_TASK'] : ''
            );
        }

        return $newNextDel;
    }

     /**
      * @deprecated
      * Review if can route a case with the assign task STATIC_MI
      * @param array $currentDelegation
     * @return boolean $canRoute
     */
    public function canRouteTasAssignStaticMi($currentDelegation){
        $arrayOpenThread = $this->case->GetAllOpenDelegation($currentDelegation);
        $aData = $this->case->loadCase( $currentDelegation['APP_UID'] );
        if (isset( $aData['APP_DATA'][str_replace( '@@', '', $currentDelegation['TAS_MI_INSTANCE_VARIABLE'] )] )) {
            $sMIinstanceVar = $aData['APP_DATA'][str_replace( '@@', '', $currentDelegation['TAS_MI_INSTANCE_VARIABLE'] )];
        } else {
            $sMIinstanceVar = $aData['APP_DATA']['TAS_MI_INSTANCE_VARIABLE'];
        }
        if (isset( $aData['APP_DATA'][str_replace( '@@', '', $currentDelegation['TAS_MI_COMPLETE_VARIABLE'] )] )) {
            $sMIcompleteVar = $aData['APP_DATA'][str_replace( '@@', '', $currentDelegation['TAS_MI_COMPLETE_VARIABLE'] )];
        } else {
            $sMIcompleteVar = $aData['APP_DATA']['TAS_MI_COMPLETE_VARIABLE'];
        }
        $discriminateThread = $sMIinstanceVar - $sMIcompleteVar;
        // -1 because One App Delegation is closed by above Code
        if ($discriminateThread == count($arrayOpenThread)) {
            $canRoute = true;
        } else {
            $canRoute = false;
        }
        return $canRoute;
    }

    /**
     * Review if can route a case with Join we will check if review the siblings are open
     * @param boolean $flagMultipleInstance
     * @param boolean $flagTypeMultipleInstance
     * @param array $currentDelegation
     * @param array $appFields
     * @param array $nextDel
     * @return boolean $canRoute
     */
    public function canRouteTypeSecJoin($flagMultipleInstance, $flagTypeMultipleInstance, $currentDelegation, $appFields, $nextDel)
    {
        $arrayOpenThread = ($flagMultipleInstance && $flagTypeMultipleInstance)? $this->case->searchOpenPreviousTasks($currentDelegation["TAS_UID"], $currentDelegation["APP_UID"]) : array();

        if (
            $flagMultipleInstance
            && $flagTypeMultipleInstance
            && isset($nextDel["ROU_PREVIOUS_TYPE"])
            && $nextDel["ROU_PREVIOUS_TYPE"] == 'SEC-JOIN'
        ) {
            $appDelegation = new AppDelegation();
            $arraySiblings = $appDelegation->getAllTasksBeforeSecJoin(
                $nextDel["ROU_PREVIOUS_TASK"],
                $currentDelegation["APP_UID"],
                $appFields['DEL_PREVIOUS'],
                'OPEN'
            );
        } else {
            $arraySiblings = $this->case->getOpenSiblingThreads(
                $nextDel["TAS_UID"],
                $currentDelegation["APP_UID"],
                $currentDelegation["DEL_INDEX"],
                $currentDelegation["TAS_UID"]
            );
        }
        $this->arraySiblings = $arraySiblings;
        if(is_array($arrayOpenThread) && is_array($arraySiblings)){
            $arrayOpenThread = array_merge($arrayOpenThread, $arraySiblings);
        }
        $canRoute = empty($arrayOpenThread);
        if($canRoute){
            if($flagMultipleInstance && $flagTypeMultipleInstance){
                $this->flagControlMulInstance = true;
            }else{
                $this->flagControl = true;
            }
        }

        return $canRoute;
    }

    /**
     * Route a case with Multiple Instance
     * This function changes the following important variables:
     * flagUpdateList (boolean) if we will update the list tables
     * removeList (boolean) if will be remove the row in the list tables
     * iNewDelIndex (integer) new index created in the table appDelegation
     * $arrayDerivationResult (array) information related to the new index created in the table appDelegation
     * @param array $currentDelegation
     * @param array $nextDel
     * @param string $taskNextDel
     * @param array $appFields
     * @param array $aSP
     * @param boolean $removeList
     * @return array $arrayDerivationResult
     * @throws \Exception
     */
    public function routeMultipleInstance($currentDelegation, $nextDel, $taskNextDel, $appFields, $aSP, $removeList)
    {
        $arrayDerivationResult = [];
        $arrayUser = $this->getNextAssignedUser(
            array(
                "APP_UID" => $currentDelegation["APP_UID"],
                "NEXT_TASK" => $taskNextDel->toArray(BasePeer::TYPE_FIELDNAME)
            )
        );

        if (empty($arrayUser)) {
            throw new Exception(G::LoadTranslation("ID_NO_USERS"));
        }

        foreach ($arrayUser as $value2) {
            $currentDelegationAux = array_merge($currentDelegation, array("ROU_TYPE" => "PARALLEL"));
            $nextDelAux = array_merge($nextDel, array("USR_UID" => $value2["USR_UID"]));
            $iNewDelIndex = $this->doDerivation($currentDelegationAux, $nextDelAux, $appFields, $aSP);
            $this->updateList($currentDelegationAux, $nextDelAux, $taskNextDel, $appFields, $iNewDelIndex, $aSP, $removeList);
            $this->iNewDelIndex = $iNewDelIndex;
            $this->flagUpdateList = false;
            $this->removeList= false;
            $arrayDerivationResult[] = [
                'DEL_INDEX' => $iNewDelIndex,
                'TAS_UID' => $nextDelAux['TAS_UID'],
                'USR_UID' => (isset($nextDelAux['USR_UID']))? $nextDelAux['USR_UID'] : ''
            ];
        }

        return $arrayDerivationResult;
    }

    /**
     * When we route a case we will to create a record in the table APP_ASSIGN_SELF_SERVICE_VALUE if the task is SELF_SERVICE
     * @param object $taskNextDel
     * @param integer $iNewDelIndex
     * @param string $nextTasUid
     * @param array $appFields
     * @return void
     */
    public function createRecordAppSelfServiceValue($taskNextDel, $iNewDelIndex, $nextTasUid, $appFields)
    {
        if ($taskNextDel->getTasAssignType() == "SELF_SERVICE" && trim($taskNextDel->getTasGroupVariable()) != "") {
            $nextTaskGroupVariable = trim($taskNextDel->getTasGroupVariable(), " @#");

            if (isset($appFields["APP_DATA"][$nextTaskGroupVariable])) {
                $dataVariable = $appFields["APP_DATA"][$nextTaskGroupVariable];
                $dataVariable = (is_array($dataVariable))? $dataVariable : trim($dataVariable);

                if (!empty($dataVariable)) {
                    $appAssignSelfServiceValue = new AppAssignSelfServiceValue();
                    $appAssignSelfServiceValue->create(
                        $appFields["APP_UID"],
                        $iNewDelIndex,
                        array(
                            "PRO_UID" => $appFields["PRO_UID"],
                            "TAS_UID" => $nextTasUid,
                            "GRP_UID" => ""
                        ),
                        $dataVariable
                    );
                }
            }
        }
    }

    /**
     * When we route a case we will to get the information about the next task
     * This function changes the following important variables:
     * $arrayNextDerivation (array) information related to the new index created in the table appDelegation
     * @param array $currentDelegation
     * @param integer $iNewDelIndex
     * @param array $nextDel
     * @return array $arrayDerivationResult
     * @throws \Exception
     */
    public function routePrepareInformationNextTask($currentDelegation, $iNewDelIndex, $nextDel)
    {
        $nextDelegationsAux   = array();
        $taskNextDelNextDelRouType = "";
        $i = 0;
        //Get for $nextDel["TAS_UID"] your next Task
        $currentDelegationAux = array_merge(
            $currentDelegation,
            array(
                "DEL_INDEX" => $iNewDelIndex,
                "TAS_UID" => $nextDel["TAS_UID"]
            )
        );
        $arrayTaskNextDelNextDelegations = $this->prepareInformation(
            array(
                "USER_UID"  => $_SESSION["USER_LOGGED"],
                "APP_UID"   => $currentDelegation["APP_UID"],
                "DEL_INDEX" => $iNewDelIndex
            )
        );

        foreach ($arrayTaskNextDelNextDelegations as $key => $value) {
            $arrayTaskNextDelNextDel = $value;
            switch ($arrayTaskNextDelNextDel['NEXT_TASK']['TAS_ASSIGN_TYPE']) {
                case 'MANUAL':
                    $arrayTaskNextDelNextDel['NEXT_TASK']['USER_ASSIGNED']['USR_UID'] = '';
                    break;
                case 'MULTIPLE_INSTANCE':
                    if (!isset($arrayTaskNextDelNextDel['NEXT_TASK']['USER_ASSIGNED']['0']['USR_UID'])) {
                        throw new Exception(G::LoadTranslation('ID_NO_USERS'));
                    }

                    $arrayTaskNextDelNextDel['NEXT_TASK']['USER_ASSIGNED']['USR_UID'] = '';
                    break;
                case 'MULTIPLE_INSTANCE_VALUE_BASED':
                    $arrayTaskNextDelNextDel['NEXT_TASK']['USER_ASSIGNED']['USR_UID'] = '';
                    break;
                default:
                    if (!isset($arrayTaskNextDelNextDel['NEXT_TASK']['USER_ASSIGNED']['USR_UID'])) {
                        throw new Exception(G::LoadTranslation('ID_NO_USERS'));
                    }
                    break;
            }

            $taskNextDelNextDelRouType = $arrayTaskNextDelNextDel["ROU_TYPE"];
            $nextDelegationsAux[++$i] = array(
                "TAS_UID"           => $arrayTaskNextDelNextDel["NEXT_TASK"]["TAS_UID"],
                "USR_UID"           => $arrayTaskNextDelNextDel["NEXT_TASK"]["USER_ASSIGNED"]["USR_UID"],
                "TAS_ASSIGN_TYPE"   => $arrayTaskNextDelNextDel["NEXT_TASK"]["TAS_ASSIGN_TYPE"],
                "TAS_DEF_PROC_CODE" => $arrayTaskNextDelNextDel["NEXT_TASK"]["TAS_DEF_PROC_CODE"],
                "DEL_PRIORITY"      => "",
                "TAS_PARENT"        => $arrayTaskNextDelNextDel["NEXT_TASK"]["TAS_PARENT"],
                "ROU_PREVIOUS_TYPE" => isset($arrayTaskNextDelNextDel["NEXT_TASK"]["ROU_PREVIOUS_TYPE"]) ? $arrayTaskNextDelNextDel["NEXT_TASK"]["ROU_PREVIOUS_TYPE"] : '',
                "ROU_PREVIOUS_TASK" => isset($arrayTaskNextDelNextDel["NEXT_TASK"]["ROU_PREVIOUS_TASK"]) ? $arrayTaskNextDelNextDel["NEXT_TASK"]["ROU_PREVIOUS_TASK"] : ''
            );
        }

        $currentDelegationAux["ROU_TYPE"] = $taskNextDelNextDelRouType;
        $arrayNextDerivation = array(
            "currentDelegation" => $currentDelegationAux,
            "nextDelegations" => $nextDelegationsAux
        );
        return $arrayNextDerivation;
    }

    /**
     * When we route a case but we will not create a index/thread in the AppDelegation
     * We will to execute Events
     * We will to close thread when is a Gateway to Gateway
     * @param array $appFields
     * @param array $currentDelegation
     * @param array $nextDel
     * @param array $arraySiblings
     * @param boolean $flagMultipleInstance
     * @param boolean $flagTypeMultipleInstance
     * @param boolean $flagFirstIteration
     * @return void
     */
    public function doRouteWithoutThread($appFields, $currentDelegation, $nextDel, $arraySiblings = array(), $flagMultipleInstance = false, $flagTypeMultipleInstance = false, $flagFirstIteration = false)
    {
        $iAppThreadIndex = $appFields['DEL_THREAD'];
        $routeType = $currentDelegation["ROU_TYPE"];
        $routeType = ($flagMultipleInstance && $flagTypeMultipleInstance)? "SEC-JOIN" : $routeType;
        switch ($routeType) {
            case 'SEC-JOIN':
                //If the all Siblings are done execute the events
                if (sizeof($arraySiblings) === 0 && !$flagTypeMultipleInstance) {
                    //Throw Events
                    $this->executeEvent($nextDel["TAS_UID"], $appFields, $flagFirstIteration, false);
                }
                //Close thread
                $this->case->closeAppThread( $currentDelegation['APP_UID'], $iAppThreadIndex );
                break;
            default:
                if ($nextDel['ROU_PREVIOUS_TYPE'] == 'SEC-JOIN') {
                    $criteria = new Criteria('workflow');
                    $criteria->clearSelectColumns();
                    $criteria->addSelectColumn(AppThreadPeer::APP_THREAD_PARENT);
                    $criteria->add(AppThreadPeer::APP_UID, $appFields['APP_UID']);
                    $criteria->add(AppThreadPeer::APP_THREAD_STATUS, 'OPEN');
                    $criteria->add(AppThreadPeer::APP_THREAD_INDEX, $iAppThreadIndex);
                    $rsCriteria = AppThreadPeer::doSelectRS($criteria);
                    $rsCriteria->setFetchmode(ResultSet::FETCHMODE_ASSOC);
                    if ($rsCriteria->next()) {
                        $this->case->closeAppThread($currentDelegation['APP_UID'], $iAppThreadIndex);
                    }
                }
                if ($currentDelegation['TAS_ASSIGN_TYPE'] == 'STATIC_MI' || $currentDelegation['TAS_ASSIGN_TYPE'] == 'CANCEL_MI') {
                    $this->case->closeAppThread( $currentDelegation['APP_UID'], $iAppThreadIndex );
                }
                break;
        }
    }
    /**
     * @deprecated
     * We create a new index in the tables with the assign task STATIC_MI
     * @param array $appFields
     * @param array $currentDelegation
     * @param array $nextDel
     * @param integer $iAppThreadIndex
     * @param string $delType
     * @return integer $iNewDelIndex, the index created
     */
    public function doDerivationStaticMi($appFields, $currentDelegation, $nextDel, $iAppThreadIndex, $delType = 'NORMAL')
    {
        $iNewAppThreadIndex = $appFields['DEL_THREAD'];
        $this->case->closeAppThread( $currentDelegation['APP_UID'], $iAppThreadIndex );

        foreach ($nextDel['NEXT_TASK']['USER_ASSIGNED'] as $key => $aValue) {
            //Incrementing the Del_thread First so that new delegation has new del_thread
            $iNewAppThreadIndex += 1;
            //Creating new delegation according to users in group
            $iMIDelIndex = $this->case->newAppDelegation(
                $appFields['PRO_UID'],
                $currentDelegation['APP_UID'],
                $nextDel['TAS_UID'],
                (isset( $aValue['USR_UID'] ) ? $aValue['USR_UID'] : ''),
                $currentDelegation['DEL_INDEX'],
                $nextDel['DEL_PRIORITY'],
                $delType,
                $iNewAppThreadIndex,
                $nextDel,
                $appFields['APP_NUMBER'],
                $appFields['PRO_ID'],
                $nextDel['TAS_ID']
            );

            $iNewThreadIndex = $this->case->newAppThread(
                $currentDelegation['APP_UID'],
                $iMIDelIndex,
                $iAppThreadIndex
            );

            //Setting the del Index for Updating the AppThread delIndex
            if ($key == 0) {
                $iNewDelIndex = $iMIDelIndex - 1;
            }
        }
        return $iNewDelIndex;
    }

}
