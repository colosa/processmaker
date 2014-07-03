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
     * prepareInformation
     *
     * @param array $aData
     * @return $taskInfo
     */
    function prepareInformation ($aData)
    {
        $oTask = new Task();
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
        $c->addJoin( AppDelegationPeer::TAS_UID, TaskPeer::TAS_UID, Criteria::LEFT_JOIN );
        $c->addJoin( AppDelegationPeer::TAS_UID, RoutePeer::TAS_UID, Criteria::LEFT_JOIN );
        $c->add( AppDelegationPeer::APP_UID, $aData['APP_UID'] );
        $c->add( AppDelegationPeer::DEL_INDEX, $aData['DEL_INDEX'] );
        $c->addAscendingOrderByColumn( RoutePeer::ROU_CASE );
        $rs = AppDelegationPeer::doSelectRs( $c );
        $rs->setFetchmode( ResultSet::FETCHMODE_ASSOC );
        $rs->next();
        $aDerivation = $rs->getRow();
        $i = 0;
        $taskInfo = array ();

        $oUser = new Users();
        if (!class_exists('Cases')) {
            G::LoadClass('case');
        }
        $this->case = new Cases();
        // 1. there is no rule
        if (is_null( $aDerivation['ROU_NEXT_TASK'] )) {
            throw (new Exception( G::LoadTranslation( 'ID_NO_DERIVATION_RULE' ) ));
        }

        while (is_array( $aDerivation )) {
            $oTask = new Task();
            $aDerivation = G::array_merges( $aDerivation, $aData );
            $bContinue = true;

            //evaluate the condition if there are conditions defined.
            if (isset( $aDerivation['ROU_CONDITION'] ) && trim( $aDerivation['ROU_CONDITION'] ) != '' && ($aDerivation['ROU_TYPE'] != 'SELECT' || $aDerivation['ROU_TYPE'] == 'PARALLEL-BY-EVALUATION')) {
                $AppFields = $this->case->loadCase( $aData['APP_UID'] );
                G::LoadClass( 'pmScript' );
                $oPMScript = new PMScript();
                $oPMScript->setFields( $AppFields['APP_DATA'] );
                $oPMScript->setScript( $aDerivation['ROU_CONDITION'] );
                $bContinue = $oPMScript->evaluate();
            }

            if ($aDerivation['ROU_TYPE'] == 'EVALUATE') {
                if (count( $taskInfo ) >= 1) {
                    $bContinue = false;
                }
            }

            if ($bContinue) {
                $i ++;
                $TaskFields = $oTask->load( $aDerivation['TAS_UID'] );

                $aDerivation = G::array_merges( $aDerivation, $TaskFields );

                //2. if next case is an special case
                if ((int) $aDerivation['ROU_NEXT_TASK'] < 0) {
                    $aDerivation['NEXT_TASK']['TAS_UID'] = (int) $aDerivation['ROU_NEXT_TASK'];
                    $aDerivation['NEXT_TASK']['TAS_ASSIGN_TYPE'] = 'nobody';
                    $aDerivation['NEXT_TASK']['TAS_PRIORITY_VARIABLE'] = '';
                    $aDerivation['NEXT_TASK']['TAS_DEF_PROC_CODE'] = '';
                    $aDerivation['NEXT_TASK']['TAS_PARENT'] = '';
                    $aDerivation['NEXT_TASK']['TAS_TRANSFER_FLY'] = '';

                    switch ($aDerivation['ROU_NEXT_TASK']) {
                        case - 1:
                            $aDerivation['NEXT_TASK']['TAS_TITLE'] = G::LoadTranslation( 'ID_END_OF_PROCESS' );
                            break;
                        case - 2:
                            $aDerivation['NEXT_TASK']['TAS_TITLE'] = G::LoadTranslation( 'ID_TAREA_COLGANTE' );
                            break;
                    }
                    $aDerivation['NEXT_TASK']['USR_UID'] = '';
                    $aDerivation['NEXT_TASK']['USER_ASSIGNED'] = array('USR_UID' => '');
                } else {
                    //3. load the task information of normal NEXT_TASK
                    $aDerivation['NEXT_TASK'] = $oTask->load( $aDerivation['ROU_NEXT_TASK'] ); //print $aDerivation['ROU_NEXT_TASK']." **** ".$aDerivation['NEXT_TASK']['TAS_TYPE']."<hr>";


                    if ($aDerivation['NEXT_TASK']['TAS_TYPE'] === 'SUBPROCESS') {
                        $oCriteria = new Criteria( 'workflow' );
                        $oCriteria->add( SubProcessPeer::PRO_PARENT, $aDerivation['PRO_UID'] );
                        $oCriteria->add( SubProcessPeer::TAS_PARENT, $aDerivation['NEXT_TASK']['TAS_UID'] );
                        $oDataset = SubProcessPeer::doSelectRS( $oCriteria );
                        $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
                        $oDataset->next();
                        $aRow = $oDataset->getRow();
                        $sTaskParent = $aDerivation['NEXT_TASK']['TAS_UID'];
                        $aDerivation['ROU_NEXT_TASK'] = $aRow['TAS_UID']; //print "<hr>Life is just a lonely highway";
                        $aDerivation['NEXT_TASK'] = $oTask->load( $aDerivation['ROU_NEXT_TASK'] ); //print "<hr>Life is just a lonely highway";print"<hr>";
                        $oProcess = new Process();
                        $aRow = $oProcess->load( $aRow['PRO_UID'] );
                        $aDerivation['NEXT_TASK']['TAS_TITLE'] .= ' (' . $aRow['PRO_TITLE'] . ')';
                        $aDerivation['NEXT_TASK']['TAS_PARENT'] = $sTaskParent;
                        unset( $oTask, $oProcess, $aRow, $sTaskParent );
                    } else {
                        $aDerivation['NEXT_TASK']['TAS_PARENT'] = '';
                    }
                    $aDerivation['NEXT_TASK']['USER_ASSIGNED'] = $this->getNextAssignedUser( $aDerivation );
                }

                $taskInfo[$i] = $aDerivation;
            }
            $rs->next();
            $aDerivation = $rs->getRow();
        }
        return $taskInfo;
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
     * @return  Array   $users an array with userID order by USR_UID
     */
    function getAllUsersFromAnyTask ($sTasUid)
    {
        $users = array ();
        $c = new Criteria( 'workflow' );
        $c->clearSelectColumns();
        $c->addSelectColumn( TaskUserPeer::USR_UID );
        $c->addSelectColumn( TaskUserPeer::TU_RELATION );
        $c->add( TaskUserPeer::TAS_UID, $sTasUid );
        $c->add( TaskUserPeer::TU_TYPE, 1 );
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
        $oUser = new Users();
        $nextAssignedTask = $tasInfo['NEXT_TASK'];
        $lastAssigned = $tasInfo['NEXT_TASK']['TAS_LAST_ASSIGNED'];
        $sTasUid = $tasInfo['NEXT_TASK']['TAS_UID'];
        // to do: we can increase the LOCATION by COUNTRY, STATE and LOCATION
        /* Verify if the next Task is set with the option "TAS_ASSIGN_LOCATION == TRUE" */
        $assignLocation = '';
        if ($tasInfo['NEXT_TASK']['TAS_ASSIGN_LOCATION'] == 'TRUE') {
            $oUser->load( $tasInfo['USER_UID'] );
            krumo( $oUser->getUsrLocation() );
            //to do: assign for location
            //$assignLocation = " AND USR_LOCATION = " . $oUser->Fields['USR_LOCATION'];
        }
        /* End - Verify if the next Task is set with the option "TAS_ASSIGN_LOCATION == TRUE" */

        $uidUser = '';
        switch ($nextAssignedTask['TAS_ASSIGN_TYPE']) {
            case 'BALANCED':
                $users = $this->getAllUsersFromAnyTask( $sTasUid );
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
                $useruid = $this->checkReplacedByUser( $this->getDenpendentUser( $tasInfo['USER_UID'] ) );

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
            default:
                throw (new Exception( 'Invalid Task Assignment method for Next Task ' ));
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

    /* derivate
     *
     * @param   array   $currentDelegation
     * @param   array   $nextDelegations
     * @return  void
     */
    function derivate ($currentDelegation = array(), $nextDelegations = array())
    {
        //define this...
        if (! defined( 'TASK_FINISH_PROCESS' )) {
            define( 'TASK_FINISH_PROCESS', - 1 );
        }
        if (! defined( 'TASK_FINISH_TASK' )) {
            define( 'TASK_FINISH_TASK', - 2 );
        }

        $this->case = new cases();

        //first, we close the current derivation, then we'll try to derivate to each defined route
        $appFields = $this->case->loadCase( $currentDelegation['APP_UID'], $currentDelegation['DEL_INDEX'] );
        $this->case->CloseCurrentDelegation( $currentDelegation['APP_UID'], $currentDelegation['DEL_INDEX'] );

        //Count how many tasks should be derivated.
        //$countNextTask = count($nextDelegations);
        foreach ($nextDelegations as $nextDel) {
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
            //get TAS_ASSIGN_TYPE for current Delegation
            $oTask = new Task();
            $aTask = $oTask->load( $currentDelegation['TAS_UID'] );
            $currentDelegation['TAS_ASSIGN_TYPE'] = $aTask['TAS_ASSIGN_TYPE'];
            $currentDelegation['TAS_MI_COMPLETE_VARIABLE'] = $aTask['TAS_MI_COMPLETE_VARIABLE'];
            $currentDelegation['TAS_MI_INSTANCE_VARIABLE'] = $aTask['TAS_MI_INSTANCE_VARIABLE'];

            //get open threads
            $openThreads = $this->case->GetOpenThreads( $currentDelegation['APP_UID'] );
            //if we are derivating to finish process but there are no more open thread then we are finishing only the task, we are not finishing the whole process
            if (($nextDel['TAS_UID'] == TASK_FINISH_PROCESS) && (($openThreads + 1) > 1)) {
                $nextDel['TAS_UID'] = TASK_FINISH_TASK;
            }
            switch ($nextDel['TAS_UID']) {
                case TASK_FINISH_PROCESS:
                    /*Close all delegations of $currentDelegation['APP_UID'] */
                    $this->case->closeAllDelegations( $currentDelegation['APP_UID'] );
                    $this->case->closeAllThreads( $currentDelegation['APP_UID'] );
                    //I think we need to change the APP_STATUS to completed,
                    break;
                case TASK_FINISH_TASK:
                    $iAppThreadIndex = $appFields['DEL_THREAD'];
                    $this->case->closeAppThread( $currentDelegation['APP_UID'], $iAppThreadIndex );
                    break;
                default:
                    // get all siblingThreads
                    //if($currentDelegation['TAS_ASSIGN_TYPE'] == 'STATIC_MI')
                    switch ($currentDelegation['TAS_ASSIGN_TYPE']) {
                        case 'CANCEL_MI':
                        case 'STATIC_MI':
                            $siblingThreads = $this->case->GetAllOpenDelegation( $currentDelegation );
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
                            if ($discriminateThread == count( $siblingThreads )) {
                                $canDerivate = true;
                            } else {
                                $canDerivate = false;
                            }
                            break;
                        default:
                            if ($currentDelegation['ROU_TYPE'] == 'SEC-JOIN') {
                                $siblingThreads = $this->case->getOpenSiblingThreads( $nextDel['TAS_UID'], $currentDelegation['APP_UID'], $currentDelegation['DEL_INDEX'], $currentDelegation['TAS_UID'], $currentDelegation['ROU_TYPE'] );
                                $canDerivate = count( $siblingThreads ) == 0;
                            } elseif ($currentDelegation['ROU_TYPE'] == 'DISCRIMINATOR') {
                                //First get the total threads of Next Task where route type='Discriminator'
                                $siblingThreads = $this->case->getOpenSiblingThreads( $nextDel['TAS_UID'], $currentDelegation['APP_UID'], $currentDelegation['DEL_INDEX'], $currentDelegation['TAS_UID'], $currentDelegation['ROU_TYPE'] );
                                $siblingThreadsCount = count( $siblingThreads );
                                $discriminateThread = $currentDelegation['ROU_CONDITION'];
                                //$checkThread = count($totalThreads) - $cond;
                                if ($discriminateThread == $siblingThreadsCount) {
                                    $canDerivate = true;
                                } else {
                                    $canDerivate = false;
                                }
                            } else {
                                $canDerivate = true;
                            }
                    } //end switch


                    if ($canDerivate) {
                        $aSP = isset( $aSP ) ? $aSP : null;
                        $iNewDelIndex = $this->doDerivation( $currentDelegation, $nextDel, $appFields, $aSP );
                    } else {
                        //when the task doesnt generate a new AppDelegation
                        $iAppThreadIndex = $appFields['DEL_THREAD'];
                        switch ($currentDelegation['ROU_TYPE']) {
                            case 'DISCRIMINATOR':
                            case 'SEC-JOIN':
                                $this->case->closeAppThread( $currentDelegation['APP_UID'], $iAppThreadIndex );
                                break;
                            default:
                                if ($currentDelegation['TAS_ASSIGN_TYPE'] == 'STATIC_MI' || $currentDelegation['TAS_ASSIGN_TYPE'] == 'CANCEL_MI') {
                                    $this->case->closeAppThread( $currentDelegation['APP_UID'], $iAppThreadIndex );
                                }
                        } //switch
                    }
            }
            //SETS THE APP_PROC_CODE
            //if (isset($nextDel['TAS_DEF_PROC_CODE']))
            //$appFields['APP_PROC_CODE'] = $nextDel['TAS_DEF_PROC_CODE'];
            unset( $aSP );

        } //end foreach


        /* Start Block : UPDATES APPLICATION */

        //Set THE APP_STATUS
        $appFields['APP_STATUS'] = $currentDelegation['APP_STATUS'];
        /* Start Block : Count the open threads of $currentDelegation['APP_UID'] */
        $openThreads = $this->case->GetOpenThreads( $currentDelegation['APP_UID'] );

        ///////
        $sw = 0;

        if ($openThreads == 0) {
            //Close case
            $appFields["APP_STATUS"] = "COMPLETED";
            $appFields["APP_FINISH_DATE"] = "now";
            $this->verifyIsCaseChild($currentDelegation["APP_UID"], $currentDelegation["DEL_INDEX"]);

            $sw = 1;
        }

        if (isset( $iNewDelIndex )) {
            $appFields["DEL_INDEX"] = $iNewDelIndex;
            $appFields["TAS_UID"] = $nextDel["TAS_UID"];

            $sw = 1;
        }

        if ($sw == 1) {
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
                //print " this->case->updateAppDelegation ( " . $currentDelegation['APP_UID'] .", " . $iNewDelIndex ." , " .  $iNewThreadIndex . " )<br>";
                break;
            case 'DISCRIMINATOR':
                if ($currentDelegation['ROU_OPTIONAL'] == 'TRUE') {
                    $this->case->discriminateCases( $currentDelegation );
                } //No Break, executing Default Condition
            default:
                switch ($currentDelegation['TAS_ASSIGN_TYPE']) {
                    case 'CANCEL_MI':
                        $this->case->discriminateCases( $currentDelegation );
                } //No Break, executing updateAppThread
                $this->case->updateAppThread( $currentDelegation['APP_UID'], $iAppThreadIndex, $iNewDelIndex );

        } //en switch


        //if there are subprocess to create
        if (isset( $aSP )) {
            //Create the new case in the sub-process
            // set the initial date to null the time its created
            $aNewCase = $this->case->startCase( $aSP['TAS_UID'], $aSP['USR_UID'], true );
            //Copy case variables to sub-process case
            $aFields = unserialize( $aSP['SP_VARIABLES_OUT'] );
            $aNewFields = array ();
            $aOldFields = $this->case->loadCase( $aNewCase['APPLICATION'] );

            foreach ($aFields as $sOriginField => $sTargetField) {
                $sOriginField = str_replace( '@', '', $sOriginField );
                $sOriginField = str_replace( '#', '', $sOriginField );
                $sTargetField = str_replace( '@', '', $sTargetField );
                $sTargetField = str_replace( '#', '', $sTargetField );
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
                    $sTargetField = str_replace( '@', '', $sTargetField );
                    $sTargetField = str_replace( '#', '', $sTargetField );
                    $aNewFields[$sTargetField] = isset( $appFields['APP_DATA'][$sOriginField] ) ? $appFields['APP_DATA'][$sOriginField] : '';
                }
                $aParentCase['APP_DATA'] = array_merge( $aParentCase['APP_DATA'], $aNewFields );
                $oCase->updateCase( $aSA['APP_PARENT'], $aParentCase );
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

                        // Send notifications - Start
                        $oUser = new Users();

                        $aUser = $oUser->load($appFields["APP_DATA"]["USER_LOGGED"]);
                        $sFromName = $aUser["USR_FIRSTNAME"] . " " . $aUser["USR_LASTNAME"] . ($aUser["USR_EMAIL"] != "" ? " <" . $aUser["USR_EMAIL"] . ">" : "");

                        try {
                            $oCase->sendNotifications($appFields["APP_DATA"]["TASK"],
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

