<?php

/**

 * class.derivation.php

 *

 * @package workflow.engine.ProcessMaker

 *

 * ProcessMaker Open Source Edition

 * Copyright (C) 2004 - 2011 Colosa Inc.

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

 *

 */

require_once ("classes/model/Task.php");

require_once ("classes/model/Process.php");

require_once ("classes/model/Step.php");

require_once ("classes/model/Application.php");

require_once ('classes/model/Groupwf.php');

require_once ("classes/model/GroupUser.php");

require_once ("classes/model/AppDelegation.php");

require_once ("classes/model/Route.php");

require_once ('classes/model/SubApplication.php');

require_once ('classes/model/SubProcess.php');

require_once ("classes/model/Users.php");



G::LoadClass( "plugin" );



/**

 * derivation - derivation class

 *

 * @package workflow.engine.ProcessMaker

 *

 */



class Derivation

{

    var $case;



    /**

     * prepareInformationTask

     *

     * @param array $arrayTaskData Task data (derivation)

     *

     * return array Return array

     */

    private function prepareInformationTask(array $arrayTaskData)

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



                $regexpTaskTypeToExclude = "GATEWAYTOGATEWAY|END-MESSAGE-EVENT|SCRIPT-TASK|INTERMEDIATE-CATCH-TIMER-EVENT|END-EMAIL-EVENT";



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

     * return array Return array

     */

    public function prepareInformation(array $arrayData, $taskUid = "")

    {

        try {

            if (!class_exists("Cases")) {

                G::LoadClass("case");

            }



            $this->case = new Cases();

            $task = new Task();



            $arrayApplicationData = $this->case->loadCase($arrayData["APP_UID"]);



            $arrayNextTask = array();

            $arrayNextTaskDefault = array();

            $i = 0;



            //SELECT *

            //FROM APP_DELEGATION AS A

            //LEFT JOIN TASK AS T ON(T.TAS_UID = A.TAS_UID)

            //LEFT JOIN ROUTE AS R ON(R.TAS_UID = A.TAS_UID)

            //WHERE

            //APP_UID = '$arrayData["APP_UID"]'

            //AND DEL_INDEX = '$arrayData["DEL_INDEX"]'



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

                    G::LoadClass("pmScript");



                    $pmScript = new PMScript();

                    $pmScript->setFields($arrayApplicationData["APP_DATA"]);

                    $pmScript->setScript($arrayRouteData["ROU_CONDITION"]);

                    $flagAddDelegation = $pmScript->evaluate();

                }



                if (trim($arrayRouteData["ROU_CONDITION"]) == "" && $arrayRouteData["ROU_NEXT_TASK"] != "-1") {

                    $arrayTaskData = $task->load($arrayRouteData["ROU_NEXT_TASK"]);



                    if ($arrayTaskData["TAS_TYPE"] == "GATEWAYTOGATEWAY") {

                        $flagAddDelegation = false;

                    }

                }



                if ($arrayRouteData["ROU_TYPE"] == "EVALUATE" && !empty($arrayNextTask)) {

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



                $regexpTaskTypeToInclude = "GATEWAYTOGATEWAY|END-MESSAGE-EVENT|END-EMAIL-EVENT";



                if ($arrayNextTaskData["NEXT_TASK"]["TAS_UID"] != "-1" &&

                    preg_match("/^(?:" . $regexpTaskTypeToInclude . ")$/", $arrayNextTaskData["NEXT_TASK"]["TAS_TYPE"])

                ) {

                    $arrayAux = $this->prepareInformation($arrayData, $arrayNextTaskData["NEXT_TASK"]["TAS_UID"]);



                    foreach ($arrayAux as $value2) {

                        $arrayNextTask[++$i] = $value2;

                    }

                } else {

                    $regexpTaskTypeToInclude = "END-MESSAGE-EVENT|END-EMAIL-EVENT";



                    if ($arrayNextTaskData["NEXT_TASK"]["TAS_UID"] == "-1" &&

                        preg_match("/^(?:" . $regexpTaskTypeToInclude . ")$/", $arrayNextTaskData["TAS_TYPE"])

                    ) {

                        $arrayNextTaskData["NEXT_TASK"]["TAS_UID"] = $arrayNextTaskData["TAS_UID"] . "/" . $arrayNextTaskData["NEXT_TASK"]["TAS_UID"];

                    }



                    $arrayNextTask[++$i] = $arrayNextTaskData;

                }

            }



            //1. There is no rule

            if (empty($arrayNextTask)) {

              $oProcess = new Process();

              $oProcessFieds = $oProcess->Load( $_SESSION['PROCESS'] );

              if(isset($oProcessFieds['PRO_BPMN']) && $oProcessFieds['PRO_BPMN'] == 1){

                throw new Exception(G::LoadTranslation("ID_NO_DERIVATION_BPMN_RULE"));

              }else{

                throw new Exception(G::LoadTranslation("ID_NO_DERIVATION_RULE"));

              }

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

        //SELECT *

        //FROM APP_DELEGATION AS A

        //LEFT JOIN TASK AS T ON(T.TAS_UID = A.TAS_UID)

        //LEFT JOIN ROUTE AS R ON(R.TAS_UID = A.TAS_UID)

        //WHERE

        //APP_UID = '$aData['APP_UID']'

        //AND DEL_INDEX = '$aData['DEL_INDEX']'

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

        //$oUser = new Users();

        $nextAssignedTask = $tasInfo['NEXT_TASK'];

        $lastAssigned = $tasInfo['NEXT_TASK']['TAS_LAST_ASSIGNED'];

        $sTasUid = $tasInfo['NEXT_TASK']['TAS_UID'];



        //// to do: we can increase the LOCATION by COUNTRY, STATE and LOCATION

        ///* Verify if the next Task is set with the option "TAS_ASSIGN_LOCATION == TRUE" */

        //$assignLocation = '';

        //if ($tasInfo['NEXT_TASK']['TAS_ASSIGN_LOCATION'] == 'TRUE') {

        //    $oUser->load( $tasInfo['USER_UID'] );

        //    krumo( $oUser->getUsrLocation() );

        //    //to do: assign for location

        //    //$assignLocation = " AND USR_LOCATION = " . $oUser->Fields['USR_LOCATION'];

        //}

        ///* End - Verify if the next Task is set with the option "TAS_ASSIGN_LOCATION == TRUE" */



        $taskNext = TaskPeer::retrieveByPK($nextAssignedTask["TAS_UID"]);

        $bpmnActivityNext = BpmnActivityPeer::retrieveByPK($nextAssignedTask["TAS_UID"]);



        $flagTaskNextIsMultipleInstance = false;

        $flagTaskNextAssignTypeIsMultipleInstance = false;



        if (!is_null($taskNext) && !is_null($bpmnActivityNext)) {

            $flagTaskNextIsMultipleInstance = $bpmnActivityNext->getActType() == "TASK" && preg_match("/^(?:EMPTY|USERTASK|MANUALTASK)$/", $bpmnActivityNext->getActTaskType()) && $bpmnActivityNext->getActLoopType() == "PARALLEL";

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

                $useruid = ($tasInfo["USER_UID"] != "")? $this->checkReplacedByUser($this->getDenpendentUser($tasInfo["USER_UID"])) : "";



                if (isset( $useruid ) && $useruid != '') {

                    $userFields = $this->getUsersFullNameFromArray( $useruid );

                }



                // if there is no report_to user info, throw an exception indicating this

                if (! isset( $userFields ) || $userFields['USR_UID'] == '') {

                    throw (new Exception( G::LoadTranslation( 'ID_MSJ_REPORSTO' ) )); // "The current user does not have a valid Reports To user.  Please contact administrator.") ) ;

                }

                break;

            case 'SELF_SERVICE':

                //look for USR_REPORTS_TO to this user

                $userFields['USR_UID'] = '';

                $userFields['USR_FULLNAME'] = '<b>' . G::LoadTranslation( 'ID_UNASSIGNED' ) . '</b>';

                $userFields['USR_USERNAME'] = '<b>' . G::LoadTranslation( 'ID_UNASSIGNED' ) . '</b>';

                $userFields['USR_FIRSTNAME'] = '';

                $userFields['USR_LASTNAME'] = '';

                $userFields['USR_EMAIL'] = '';

                break;

            case "MULTIPLE_INSTANCE":

                $userFields = $this->getUsersFullNameFromArray($this->getAllUsersFromAnyTask($nextAssignedTask["TAS_UID"]));

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

        //Here the uid to next user

        $oC = new Criteria();

        $oC->addSelectColumn( UsersPeer::USR_REPORTS_TO );

        $oC->add( UsersPeer::USR_UID, $USR_UID );

        $oDataset = UsersPeer::doSelectRS( $oC );

        $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );

        $oDataset->next();

        $aRow = $oDataset->getRow();

        return $aRow['USR_REPORTS_TO'] != '' ? $aRow['USR_REPORTS_TO'] : $USR_UID;

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

     * return void

     */

    private function derivateUpdateCounters(array $arrayCurrentDelegationData, array $arrayNextDelegationData, $taskNextDelegation, array $arrayApplicationData, $delIndexNew, $aSp, $removeList)

    {

        /*----------------------------------********---------------------------------*/

    }



    /* derivate

     *

     * @param   array   $currentDelegation

     * @param   array   $nextDelegations

     * @return  void

     */

    function derivate($currentDelegation = array(), $nextDelegations = array(), $removeList = true)

    {

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



        //Count how many tasks should be derivated.

        //$countNextTask = count($nextDelegations);

        //$removeList = true;



        foreach ($nextDelegations as $nextDel) {

            //BpmnEvent - END-MESSAGE-EVENT, END-EMAIL-EVENT

            //Check and get unique id

            if (preg_match("/^(.{32})\/(\-1)$/", $nextDel["TAS_UID"], $arrayMatch)) {

                $nextDel["TAS_UID"] = $arrayMatch[2];

                $nextDel["TAS_UID_DUMMY"] = $arrayMatch[1];

            }



            //subprocesses??

            if ($nextDel['TAS_PARENT'] != '') {

                $oCriteria = new Criteria( 'workflow' );

                $oCriteria->add( SubProcessPeer::PRO_PARENT, $appFields['PRO_UID'] );

                $oCriteria->add( SubProcessPeer::TAS_PARENT, $nextDel['TAS_PARENT'] );

                if (SubProcessPeer::doCount( $oCriteria ) > 0) {

                    $oDataset = SubProcessPeer::doSelectRS( $oCriteria );

                    $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );

                    $oDataset->next();

                    $aSP = $oDataset->getRow();

                    $aSP['USR_UID'] = $nextDel['USR_UID'];

                    $oTask = new Task();

                    $aTask = $oTask->load( $nextDel['TAS_PARENT'] );

                    $nextDel = array ('TAS_UID' => $aTask['TAS_UID'],'USR_UID' => $aSP['USR_UID'],'TAS_ASSIGN_TYPE' => $aTask['TAS_ASSIGN_TYPE'],'TAS_DEF_PROC_CODE' => $aTask['TAS_DEF_PROC_CODE'],'DEL_PRIORITY' => 3,'TAS_PARENT' => ''

                    );

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



            $flagUpdateCounters = true;



            switch ($nextDel['TAS_UID']) {

                case TASK_FINISH_PROCESS:

                    /*Close all delegations of $currentDelegation['APP_UID'] */

                    $this->case->closeAllDelegations( $currentDelegation['APP_UID'] );

                    $this->case->closeAllThreads( $currentDelegation['APP_UID'] );

                    //I think we need to change the APP_STATUS to completed,



                    //BpmnEvent - END-MESSAGE-EVENT, END-EMAIL-EVENT

                    if (isset($nextDel["TAS_UID_DUMMY"])) {

                        $taskDummy = TaskPeer::retrieveByPK($nextDel["TAS_UID_DUMMY"]);



                        switch ($taskDummy->getTasType()) {

                            case "END-MESSAGE-EVENT":

                                //Throw Message-Events - BpmnEvent - END-MESSAGE-EVENT

                                $case = new \ProcessMaker\BusinessModel\Cases();



                                $case->throwMessageEventBetweenElementOriginAndElementDest($currentDelegation["TAS_UID"], $nextDel["TAS_UID_DUMMY"], $appFields);

                                break;

                            case "END-EMAIL-EVENT":

                                //Email Event

                                $emailEvent = new \ProcessMaker\BusinessModel\EmailEvent();



                                $emailEvent->emailEventBetweenElementOriginAndElementDest($currentDelegation["TAS_UID"], $nextDel["TAS_UID_DUMMY"], $appFields);

                                break;

                        }

                    }

                    break;

                case TASK_FINISH_TASK:

                    $iAppThreadIndex = $appFields['DEL_THREAD'];

                    $this->case->closeAppThread( $currentDelegation['APP_UID'], $iAppThreadIndex );

                    break;

                default:

                    //Get all siblingThreads

                    $canDerivate = false;



                    switch ($currentDelegation['TAS_ASSIGN_TYPE']) {

                        case 'CANCEL_MI':

                        case 'STATIC_MI':

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

                                $canDerivate = true;

                            } else {

                                $canDerivate = false;

                            }

                            break;

                        default:

                            $routeType = $currentDelegation["ROU_TYPE"];

                            $routeType = ($flagTaskIsMultipleInstance && $flagTaskAssignTypeIsMultipleInstance)? "SEC-JOIN" : $routeType;



                            switch ($routeType) {

                                case "SEC-JOIN":

                                    $arrayOpenThread = ($flagTaskIsMultipleInstance && $flagTaskAssignTypeIsMultipleInstance)? $this->case->searchOpenPreviousTasks($currentDelegation["TAS_UID"], $currentDelegation["APP_UID"]) : array();

                                    $arrayOpenThread = array_merge($arrayOpenThread, $this->case->getOpenSiblingThreads($nextDel["TAS_UID"], $currentDelegation["APP_UID"], $currentDelegation["DEL_INDEX"], $currentDelegation["TAS_UID"]));



                                    $canDerivate = empty($arrayOpenThread);

                                    break;

                                default:

                                    $canDerivate = true;

                                    break;

                            }

                            break;

                    }



                    if ($canDerivate) {

                        //Throw Message-Events

                        $case = new \ProcessMaker\BusinessModel\Cases();



                        $case->throwMessageEventBetweenElementOriginAndElementDest($currentDelegation["TAS_UID"], $nextDel["TAS_UID"], $appFields);



                        //Throw Email-Events

                        $emailEvent = new \ProcessMaker\BusinessModel\EmailEvent();



                        $emailEvent->emailEventBetweenElementOriginAndElementDest($currentDelegation["TAS_UID"], $nextDel["TAS_UID"], $appFields);



                        //Derivate

                        $aSP = (isset($aSP))? $aSP : null;



                        $taskNextDelAssignType = ($flagTaskNextDelIsMultipleInstance && $flagTaskNextDelAssignTypeIsMultipleInstance)? $taskNextDel->getTasAssignType() : "";



                        switch ($taskNextDelAssignType) {

                            case "MULTIPLE_INSTANCE":

                            case "MULTIPLE_INSTANCE_VALUE_BASED":

                                $arrayUser = $this->getNextAssignedUser(array("APP_UID" => $currentDelegation["APP_UID"], "NEXT_TASK" => $taskNextDel->toArray(BasePeer::TYPE_FIELDNAME)));



                                if (empty($arrayUser)) {

                                    throw new Exception(G::LoadTranslation("ID_NO_USERS"));

                                }



                                foreach ($arrayUser as $value2) {

                                    $currentDelegationAux = array_merge($currentDelegation, array("ROU_TYPE" => "PARALLEL"));

                                    $nextDelAux = array_merge($nextDel, array("USR_UID" => $value2["USR_UID"]));



                                    $iNewDelIndex = $this->doDerivation($currentDelegationAux, $nextDelAux, $appFields, $aSP);



                                    $this->derivateUpdateCounters($currentDelegationAux, $nextDelAux, $taskNextDel, $appFields, $iNewDelIndex, $aSP, $removeList);



                                    $flagUpdateCounters = false;

                                    $removeList = false;

                                }

                                break;

                            default:

                                $iNewDelIndex = $this->doDerivation($currentDelegation, $nextDel, $appFields, $aSP);

                                break;

                        }



                        //Execute Script-Task

                        $scriptTask = new \ProcessMaker\BusinessModel\ScriptTask();



                        $appFields["APP_DATA"] = $scriptTask->execScriptByActivityUid($nextDel["TAS_UID"], $appFields);



                        //Create record in table APP_ASSIGN_SELF_SERVICE_VALUE

                        $regexpTaskTypeToExclude = "SCRIPT-TASK";



                        if (!is_null($taskNextDel) && !preg_match("/^(?:" . $regexpTaskTypeToExclude . ")$/", $taskNextDel->getTasType())) {

                            if ($taskNextDel->getTasAssignType() == "SELF_SERVICE" && trim($taskNextDel->getTasGroupVariable()) != "") {

                                $nextTaskGroupVariable = trim($taskNextDel->getTasGroupVariable(), " @#");



                                if (isset($appFields["APP_DATA"][$nextTaskGroupVariable])) {

                                    $dataVariable = $appFields["APP_DATA"][$nextTaskGroupVariable];

                                    $dataVariable = (is_array($dataVariable))? $dataVariable : trim($dataVariable);



                                    if (!empty($dataVariable)) {

                                        $appAssignSelfServiceValue = new AppAssignSelfServiceValue();



                                        $appAssignSelfServiceValue->create($appFields["APP_UID"], $iNewDelIndex, array("PRO_UID" => $appFields["PRO_UID"], "TAS_UID" => $nextDel["TAS_UID"], "GRP_UID" => serialize($dataVariable)));

                                    }

                                }

                            }

                        }



                        //Check if $taskNextDel is Script-Task

                        if (!is_null($taskNextDel) && $taskNextDel->getTasType() == "SCRIPT-TASK") {

                            $this->case->CloseCurrentDelegation($currentDelegation["APP_UID"], $iNewDelIndex);



                            //Get for $nextDel["TAS_UID"] your next Task

                            $taskNextDelNextDelegations = $this->prepareInformation(array(

                                "USER_UID"  => $_SESSION["USER_LOGGED"],

                                "APP_UID"   => $_SESSION["APPLICATION"],

                                "DEL_INDEX" => $iNewDelIndex

                            ));



                            //New next delegation

                            $newNextDelegation = array();



                            $newNextDelegation[1] = array(

                                "TAS_UID"           => $taskNextDelNextDelegations[1]["NEXT_TASK"]["TAS_UID"],

                                "USR_UID"           => $taskNextDelNextDelegations[1]["NEXT_TASK"]["USER_ASSIGNED"]["USR_UID"],

                                "TAS_ASSIGN_TYPE"   => $taskNextDelNextDelegations[1]["NEXT_TASK"]["TAS_ASSIGN_TYPE"],

                                "TAS_DEF_PROC_CODE" => "",

                                "DEL_PRIORITY"      => "",

                                "TAS_PARENT"        => ""

                            );



                            $this->derivate($currentDelegation, $newNextDelegation, $removeList);

                        }

                    } else {

                        //when the task doesnt generate a new AppDelegation

                        $iAppThreadIndex = $appFields['DEL_THREAD'];



                        $routeType = $currentDelegation["ROU_TYPE"];

                        $routeType = ($flagTaskIsMultipleInstance && $flagTaskAssignTypeIsMultipleInstance)? "SEC-JOIN" : $routeType;



                        switch ($routeType) {

                            case 'SEC-JOIN':

                                $this->case->closeAppThread( $currentDelegation['APP_UID'], $iAppThreadIndex );

                                break;

                            default:

                                if ($currentDelegation['TAS_ASSIGN_TYPE'] == 'STATIC_MI' || $currentDelegation['TAS_ASSIGN_TYPE'] == 'CANCEL_MI') {

                                    $this->case->closeAppThread( $currentDelegation['APP_UID'], $iAppThreadIndex );

                                }

                                break;

                        }

                    }

                    break;

            }



            if ($flagUpdateCounters) {

                $this->derivateUpdateCounters($currentDelegation, $nextDel, $taskNextDel, $appFields, (isset($iNewDelIndex))? $iNewDelIndex : 0, (isset($aSP))? $aSP : null, $removeList);

            }



            $removeList = false;



            unset($aSP);

        }



        /* Start Block : UPDATES APPLICATION */



        //Set THE APP_STATUS

        $appFields['APP_STATUS'] = $currentDelegation['APP_STATUS'];

        /* Start Block : Count the open threads of $currentDelegation['APP_UID'] */

        $openThreads = $this->case->GetOpenThreads( $currentDelegation['APP_UID'] );



        ///////

        $flag = false;



        if ($openThreads == 0) {

            //Close case

            $appFields["APP_STATUS"] = "COMPLETED";

            $appFields["APP_FINISH_DATE"] = "now";

            $this->verifyIsCaseChild($currentDelegation["APP_UID"], $currentDelegation["DEL_INDEX"]);



            $flag = true;

        }



        if (isset( $iNewDelIndex )) {

            $appFields["DEL_INDEX"] = $iNewDelIndex;

            $appFields["TAS_UID"] = $nextDel["TAS_UID"];



            $flag = true;

        }



        if ($flag) {

            //Start Block : UPDATES APPLICATION

            $this->case->updateCase( $currentDelegation["APP_UID"], $appFields );

            //End Block : UPDATES APPLICATION

        }

    }



    function doDerivation ($currentDelegation, $nextDel, $appFields, $aSP = null)

    {

        $iAppThreadIndex = $appFields['DEL_THREAD'];

        $delType = 'NORMAL';



        if (is_numeric( $nextDel['DEL_PRIORITY'] )) {

            $nextDel['DEL_PRIORITY'] = (isset( $nextDel['DEL_PRIORITY'] ) ? ($nextDel['DEL_PRIORITY'] >= 1 && $nextDel['DEL_PRIORITY'] <= 5 ? $nextDel['DEL_PRIORITY'] : '3') : '3');

        } else {

            $nextDel['DEL_PRIORITY'] = 3;

        }

        switch ($nextDel['TAS_ASSIGN_TYPE']) {

            case 'CANCEL_MI':

            case 'STATIC_MI':

                // Create new delegation depending on the no of users in the group

                $iNewAppThreadIndex = $appFields['DEL_THREAD'];

                $this->case->closeAppThread( $currentDelegation['APP_UID'], $iAppThreadIndex );



                foreach ($nextDel['NEXT_TASK']['USER_ASSIGNED'] as $key => $aValue) {

                    //Incrementing the Del_thread First so that new delegation has new del_thread

                    $iNewAppThreadIndex += 1;

                    //Creating new delegation according to users in group

                    $iMIDelIndex = $this->case->newAppDelegation( $appFields['PRO_UID'], $currentDelegation['APP_UID'], $nextDel['TAS_UID'], (isset( $aValue['USR_UID'] ) ? $aValue['USR_UID'] : ''), $currentDelegation['DEL_INDEX'], $nextDel['DEL_PRIORITY'], $delType, $iNewAppThreadIndex, $nextDel );



                    $iNewThreadIndex = $this->case->newAppThread( $currentDelegation['APP_UID'], $iMIDelIndex, $iAppThreadIndex );



                    //Setting the del Index for Updating the AppThread delIndex

                    if ($key == 0) {

                        $iNewDelIndex = $iMIDelIndex - 1;

                    }

                } //end foreach

                break;

            case 'BALANCED':

                $this->setTasLastAssigned( $nextDel['TAS_UID'], $nextDel['USR_UID'] );

                //No Break, need no execute the default ones....

            default:

                // Create new delegation

                $iNewDelIndex = $this->case->newAppDelegation( $appFields['PRO_UID'], $currentDelegation['APP_UID'], $nextDel['TAS_UID'], (isset( $nextDel['USR_UID'] ) ? $nextDel['USR_UID'] : ''), $currentDelegation['DEL_INDEX'], $nextDel['DEL_PRIORITY'], $delType, $iAppThreadIndex, $nextDel );

                break;

        }



        $iAppThreadIndex = $appFields['DEL_THREAD'];



        switch ($currentDelegation['ROU_TYPE']) {

            case 'PARALLEL':

            case 'PARALLEL-BY-EVALUATION':

                $this->case->closeAppThread( $currentDelegation['APP_UID'], $iAppThreadIndex );

                $iNewThreadIndex = $this->case->newAppThread( $currentDelegation['APP_UID'], $iNewDelIndex, $iAppThreadIndex );

                $this->case->updateAppDelegation( $currentDelegation['APP_UID'], $iNewDelIndex, $iNewThreadIndex );

                break;

            default:

                $this->case->updateAppThread( $currentDelegation['APP_UID'], $iAppThreadIndex, $iNewDelIndex );

                break;

        } //en switch



        //if there are subprocess to create

        if (isset( $aSP )) {

            //Create the new case in the sub-process

            // set the initial date to null the time its created

            $aNewCase = $this->case->startCase( $aSP['TAS_UID'], $aSP['USR_UID'], true, $appFields);



            $taskNextDel = TaskPeer::retrieveByPK($aSP["TAS_UID"]); //Sub-Process



            //Copy case variables to sub-process case

            $aFields = unserialize( $aSP['SP_VARIABLES_OUT'] );

            $aNewFields = array ();

            $aOldFields = $this->case->loadCase( $aNewCase['APPLICATION'] );



            foreach ($aFields as $sOriginField => $sTargetField) {

                $sOriginField = trim($sOriginField, " @#%?$=");

                $sTargetField = trim($sTargetField, " @#%?$=");



                $aNewFields[$sTargetField] = isset( $appFields['APP_DATA'][$sOriginField] ) ? $appFields['APP_DATA'][$sOriginField] : '';

            }



            $aOldFields['APP_DATA'] = array_merge( $aOldFields['APP_DATA'], $aNewFields );

            $aOldFields['APP_STATUS'] = 'TO_DO';



            $this->case->updateCase( $aNewCase['APPLICATION'], $aOldFields );



            //Create a registry in SUB_APPLICATION table

            $aSubApplication = array ('APP_UID' => $aNewCase['APPLICATION'],'APP_PARENT' => $currentDelegation['APP_UID'],'DEL_INDEX_PARENT' => $iNewDelIndex,'DEL_THREAD_PARENT' => $iAppThreadIndex,'SA_STATUS' => 'ACTIVE','SA_VALUES_OUT' => serialize( $aNewFields ),'SA_INIT_DATE' => date( 'Y-m-d H:i:s' )

            );



            if ($aSP['SP_SYNCHRONOUS'] == 0) {

                $aSubApplication['SA_STATUS'] = 'FINISHED';

                $aSubApplication['SA_FINISH_DATE'] = $aSubApplication['SA_INIT_DATE'];

            }



            $oSubApplication = new SubApplication();

            $oSubApplication->create( $aSubApplication );

            //Update the AppDelegation to execute the update trigger

            $AppDelegation = AppDelegationPeer::retrieveByPK( $aNewCase['APPLICATION'], $aNewCase['INDEX'] );



            // note added by krlos pacha carlos[at]colosa[dot]com

            // the following line of code was commented because it is related to the 6878 bug

            //$AppDelegation->setDelInitDate("+1 second");



            $AppDelegation->save();



            //Create record in table APP_ASSIGN_SELF_SERVICE_VALUE

            if ($taskNextDel->getTasAssignType() == "SELF_SERVICE" && trim($taskNextDel->getTasGroupVariable()) != "") {

                $nextTaskGroupVariable = trim($taskNextDel->getTasGroupVariable(), " @#");



                if (isset($aOldFields["APP_DATA"][$nextTaskGroupVariable])) {

                    $dataVariable = $aOldFields["APP_DATA"][$nextTaskGroupVariable];

                    $dataVariable = (is_array($dataVariable))? $dataVariable : trim($dataVariable);



                    if (!empty($dataVariable)) {

                        $appAssignSelfServiceValue = new AppAssignSelfServiceValue();



                        $appAssignSelfServiceValue->create($aNewCase["APPLICATION"], $aNewCase["INDEX"], array("PRO_UID" => $aNewCase["PROCESS"], "TAS_UID" => $aSP["TAS_UID"], "GRP_UID" => serialize($dataVariable)));

                    }

                }

            }



            //If not is SYNCHRONOUS derivate one more time

            if ($aSP['SP_SYNCHRONOUS'] == 0) {

                $this->case->setDelInitDate( $currentDelegation['APP_UID'], $iNewDelIndex );

                $aDeriveTasks = $this->prepareInformation( array ('USER_UID' => -1,'APP_UID' => $currentDelegation['APP_UID'],'DEL_INDEX' => $iNewDelIndex

                ) );



                if (isset( $aDeriveTasks[1] )) {

                    if ($aDeriveTasks[1]['ROU_TYPE'] != 'SELECT') {

                        $nextDelegations2 = array ();

                        foreach ($aDeriveTasks as $aDeriveTask) {

                            $nextDelegations2[] = array ('TAS_UID' => $aDeriveTask['NEXT_TASK']['TAS_UID'],'USR_UID' => $aDeriveTask['NEXT_TASK']['USER_ASSIGNED']['USR_UID'],'TAS_ASSIGN_TYPE' => $aDeriveTask['NEXT_TASK']['TAS_ASSIGN_TYPE'],'TAS_DEF_PROC_CODE' => $aDeriveTask['NEXT_TASK']['TAS_DEF_PROC_CODE'],'DEL_PRIORITY' => 3,'TAS_PARENT' => $aDeriveTask['NEXT_TASK']['TAS_PARENT']

                            );

                        }

                        $currentDelegation2 = array ('APP_UID' => $currentDelegation['APP_UID'],'DEL_INDEX' => $iNewDelIndex,'APP_STATUS' => 'TO_DO','TAS_UID' => $currentDelegation['TAS_UID'],'ROU_TYPE' => $aDeriveTasks[1]['ROU_TYPE']

                        );

                        $this->derivate( $currentDelegation2, $nextDelegations2 );

                    }

                }

            }

        } //end switch

        return $iNewDelIndex;

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

            if ($aSP['SP_SYNCHRONOUS'] == 1) {

                $appFields = $oCase->loadCase($sApplicationUID, $delIndex);

                //Copy case variables to parent case

                $aFields = unserialize( $aSP['SP_VARIABLES_IN'] );

                $aNewFields = array ();

                foreach ($aFields as $sOriginField => $sTargetField) {

                    $sOriginField = str_replace( '@', '', $sOriginField );

                    $sOriginField = str_replace( '#', '', $sOriginField );

                    $sOriginField = str_replace( '%', '', $sOriginField );

                    $sOriginField = str_replace( '?', '', $sOriginField );

                    $sOriginField = str_replace( '$', '', $sOriginField );

                    $sOriginField = str_replace( '=', '', $sOriginField );

                    $sTargetField = str_replace( '@', '', $sTargetField );

                    $sTargetField = str_replace( '#', '', $sTargetField );

                    $sTargetField = str_replace( '%', '', $sTargetField );

                    $sTargetField = str_replace( '?', '', $sTargetField );

                    $sTargetField = str_replace( '$', '', $sTargetField );

                    $sTargetField = str_replace( '=', '', $sTargetField );

                    $aNewFields[$sTargetField] = isset( $appFields['APP_DATA'][$sOriginField] ) ? $appFields['APP_DATA'][$sOriginField] : '';

                    if(isset($aParentCase['APP_DATA'][$sTargetField.'_label'])){

                        $aNewFields[$sTargetField.'_label'] = isset( $appFields['APP_DATA'][$sOriginField.'_label'] ) ? $appFields['APP_DATA'][$sOriginField.'_label'] : '';

                    }

                }

                $aParentCase['APP_DATA'] = array_merge( $aParentCase['APP_DATA'], $aNewFields );

                $oCase->updateCase( $aSA['APP_PARENT'], $aParentCase );

                /*----------------------------------********---------------------------------*/



                //Update table SUB_APPLICATION

                $oSubApplication = new SubApplication();

                $oSubApplication->update( array ('APP_UID' => $sApplicationUID,'APP_PARENT' => $aSA['APP_PARENT'],'DEL_INDEX_PARENT' => $aSA['DEL_INDEX_PARENT'],'DEL_THREAD_PARENT' => $aSA['DEL_THREAD_PARENT'],'SA_STATUS' => 'FINISHED','SA_VALUES_IN' => serialize( $aNewFields ),'SA_FINISH_DATE' => date( 'Y-m-d H:i:s' )

                ) );



                //Derive the parent case

                $aDeriveTasks = $this->prepareInformation( array ('USER_UID' => - 1,'APP_UID' => $aSA['APP_PARENT'],'DEL_INDEX' => $aSA['DEL_INDEX_PARENT']

                ) );

                if (isset( $aDeriveTasks[1] )) {

                    if ($aDeriveTasks[1]['ROU_TYPE'] != 'SELECT') {

                        $nextDelegations2 = array ();

                        foreach ($aDeriveTasks as $aDeriveTask) {

                            if (! isset( $aDeriveTask['NEXT_TASK']['USER_ASSIGNED']['USR_UID'] )) {

                                $selectedUser = $aDeriveTask['NEXT_TASK']['USER_ASSIGNED'][0];

                                unset( $aDeriveTask['NEXT_TASK']['USER_ASSIGNED'] );

                                $aDeriveTask['NEXT_TASK']['USER_ASSIGNED'] = $selectedUser;

                                $myLabels = array ($aDeriveTask['NEXT_TASK']['TAS_TITLE'],$aParentCase['APP_NUMBER'],$selectedUser['USR_USERNAME'],$selectedUser['USR_FIRSTNAME'],$selectedUser['USR_LASTNAME']

                                );

                                G::SendTemporalMessage( 'ID_TASK_WAS_ASSIGNED_TO_USER', 'warning', 'labels', 10, null, $myLabels );



                            }

                            $nextDelegations2[] = array ('TAS_UID' => $aDeriveTask['NEXT_TASK']['TAS_UID'],'USR_UID' => $aDeriveTask['NEXT_TASK']['USER_ASSIGNED']['USR_UID'],'TAS_ASSIGN_TYPE' => $aDeriveTask['NEXT_TASK']['TAS_ASSIGN_TYPE'],'TAS_DEF_PROC_CODE' => $aDeriveTask['NEXT_TASK']['TAS_DEF_PROC_CODE'],'DEL_PRIORITY' => 3,'TAS_PARENT' => $aDeriveTask['NEXT_TASK']['TAS_PARENT']

                            );

                        }

                        $currentDelegation2 = array ('APP_UID' => $aSA['APP_PARENT'],'DEL_INDEX' => $aSA['DEL_INDEX_PARENT'],'APP_STATUS' => 'TO_DO','TAS_UID' => $aParentCase['TAS_UID'],'ROU_TYPE' => $aDeriveTasks[1]['ROU_TYPE']

                        );

                        $this->derivate( $currentDelegation2, $nextDelegations2 );



                        if($delIndex > 0 ) {

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

        G::LoadClass( 'groups' );

        G::LoadClass( 'tasks' );

        require_once 'classes/model/Content.php';

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

}


