<?php
/**
 * class.tasks.php
 *
 * @package workflow.engine.ProcessMaker
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

require_once 'classes/model/GroupUser.php';
require_once 'classes/model/Groupwf.php';
require_once 'classes/model/ObjectPermission.php';
require_once 'classes/model/Process.php';
require_once 'classes/model/Route.php';
require_once 'classes/model/Event.php';
require_once 'classes/model/Step.php';
require_once 'classes/model/StepTrigger.php';
require_once 'classes/model/Task.php';
require_once 'classes/model/TaskUser.php';
require_once 'classes/model/Users.php';
require_once 'classes/model/Gateway.php';

/**
 * Tasks - Tasks class
 *
 * @package workflow.engine.ProcessMaker
 * @author Julio Cesar Laura Avenda�o
 * @copyright 2007 COLOSA
 */

class Tasks
{

    /**
     * Get the assigned groups of a task
     *
     * @param string $sTaskUID
     * @param integer $iType
     * @return array
     */
    public function getGroupsOfTask ($sTaskUID, $iType)
    {
        try {
            $aGroups = array ();
            $oCriteria = new Criteria( 'workflow' );
            $oCriteria->addJoin( GroupwfPeer::GRP_UID, TaskUserPeer::USR_UID, Criteria::LEFT_JOIN );
            $oCriteria->add( TaskUserPeer::TAS_UID, $sTaskUID );
            $oCriteria->add( TaskUserPeer::TU_TYPE, $iType );
            $oCriteria->add( TaskUserPeer::TU_RELATION, 2 );
            $oCriteria->add( GroupwfPeer::GRP_STATUS, 'ACTIVE' );
            $oDataset = GroupwfPeer::doSelectRS( $oCriteria );
            $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
            $oDataset->next();
            while ($aRow = $oDataset->getRow()) {
                $aGroups[] = $aRow;
                $oDataset->next();
            }
            return $aGroups;
        } catch (Exception $oError) {
            throw ($oError);
        }
    }

    /**
     * Get all tasks for any Process
     *
     * @param string $sProUid
     * @return array
     */
    public function getAllTasks ($sProUid)
    {
        try {
            $aTasks = array ();
            $sDelimiter = DBAdapter::getStringDelimiter();
            $oCriteria = new Criteria( 'workflow' );
            $oCriteria->add( TaskPeer::PRO_UID, $sProUid );
            $aConditions = array ();
            $aConditions[] = array (TaskPeer::TAS_UID,ContentPeer::CON_ID
            );
            $aConditions[] = array (ContentPeer::CON_CATEGORY,$sDelimiter . 'TAS_TITLE' . $sDelimiter
            );
            $aConditions[] = array (ContentPeer::CON_LANG,$sDelimiter . SYS_LANG . $sDelimiter
            );
            $oCriteria->addJoinMC( $aConditions, Criteria::LEFT_JOIN );
            $oCriteria->addAscendingOrderByColumn( ContentPeer::CON_VALUE );
            $oDataset = TaskPeer::doSelectRS( $oCriteria );
            $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
            $oDataset->next();
            while ($aRow = $oDataset->getRow()) {
                $oTask = new Task();
                $aTasks[] = $oTask->Load( $aRow['TAS_UID'] );
                $oDataset->next();
            }
            return $aTasks;
        } catch (Exception $oError) {
            throw ($oError);
        }
    }

    /**
     * creates row tasks from an Task Array
     *
     * @param string $aTasks
     * @return array
     */
    public function createTaskRows ($aTask)
    {
        foreach ($aTask as $key => $row) {
            $oTask = new Task();
            if ($oTask->taskExists( $row['TAS_UID'] ))
                $oTask->remove( $row['TAS_UID'] );
            $res = $oTask->createRow( $row );
        }
        return;
    }

    /**
     * updates row tasks from an Task Array
     *
     * @param string $aTasks
     * @return array
     */
    public function updateTaskRows ($aTask)
    {
        foreach ($aTask as $key => $row) {
            $oTask = new Task();
            if ($oTask->taskExists( $row['TAS_UID'] ))
                $oTask->remove( $row['TAS_UID'] );
            else
                $res = $oTask->update( $row );
        }
        return;
    }

    /**
     * Get all Routes for any Process
     *
     * @param string $sProUid
     * @return array
     */
    public function getAllRoutes ($sProUid)
    {
        try {
            $aRoutes = array ();
            $oCriteria = new Criteria( 'workflow' );
            $oCriteria->add( RoutePeer::PRO_UID, $sProUid );
            $oDataset = RoutePeer::doSelectRS( $oCriteria );
            $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
            $oDataset->next();
            while ($aRow = $oDataset->getRow()) {
                $aRoutes[] = $aRow;
                $oDataset->next();
            }
            return $aRoutes;
        } catch (Exception $oError) {
            throw ($oError);
        }
    }

    /**
     * creates row tasks from an Route Array
     *
     * @param string $aTasks
     * @return array
     */
    public function createRouteRows ($aRoutes)
    {
        $routeID = array ();
        $aField = array ();
        $taskParallel = '';
        $taskSecJoin = '';
        $taskEvaluate = '';
        $taskParallelEv = '';
        $taskSelect = '';
        $taskDiscriminator = '';
        foreach ($aRoutes as $key => $row) {
            $sRouteType = $row['ROU_TYPE'];
            $oRoute = new Route();
            $oProcessMap = new processMap();
            $oTask = new Task();
            $oEvent = new Event();
            //unset ($row['ROU_UID']);


            //Saving Gateway into the GATEWAY table
            $idTask = $row['TAS_UID'];
            $nextTask = $row['ROU_NEXT_TASK'];
            if ($nextTask == "-1") {
                $end = 1;
            }
            if ($sRouteType != 'SEQUENTIAL') {
                switch ($sRouteType) {
                    case 'PARALLEL':
                        if ($idTask != $taskParallel) {
                            $taskParallel = $idTask;

                            $sGatewayUID = $oProcessMap->saveNewGateway( $row['PRO_UID'], $row['TAS_UID'], $row['ROU_NEXT_TASK'] );
                        }
                        break;
                    case 'SEC-JOIN':
                        if ($nextTask != $taskSecJoin) {
                            $taskSecJoin = $nextTask;
                            $sGatewayUID = $oProcessMap->saveNewGateway( $row['PRO_UID'], $row['TAS_UID'], $row['ROU_NEXT_TASK'] );
                        }
                        break;
                    case 'EVALUATE':
                        if ($idTask != $taskEvaluate) {
                            $taskEvaluate = $idTask;
                            $sGatewayUID = $oProcessMap->saveNewGateway( $row['PRO_UID'], $row['TAS_UID'], $row['ROU_NEXT_TASK'] );
                        }
                        break;
                    case 'PARALLEL-BY-EVALUATION':
                        if ($idTask != $taskParallelEv) {
                            $taskParallelEv = $idTask;
                            $sGatewayUID = $oProcessMap->saveNewGateway( $row['PRO_UID'], $row['TAS_UID'], $row['ROU_NEXT_TASK'] );
                        }
                        break;
                    case 'SELECT':
                        if ($idTask != $taskSelect) {
                            $taskSelect = $idTask;
                            $sGatewayUID = $oProcessMap->saveNewGateway( $row['PRO_UID'], $row['TAS_UID'], $row['ROU_NEXT_TASK'] );
                        }
                        break;
                    case 'DISCRIMINATOR':
                        if ($nextTask != $taskDiscriminator) {
                            $taskDiscriminator = $nextTask;
                            $sGatewayUID = $oProcessMap->saveNewGateway( $row['PRO_UID'], $row['TAS_UID'], $row['ROU_NEXT_TASK'] );
                        }
                        break;
                }
                $row['GAT_UID'] = $sGatewayUID;
            }

            if ($oRoute->routeExists( $row['ROU_UID'] ))
                $oRoute->remove( $row['ROU_UID'] );

            $routeID = $oRoute->create( $row );

            //saving end event while import old processes
            if (isset( $end ) && $end == 1) {
                if (! $oEvent->existsByTaskUidFrom( $idTask )) {
                    if ($sRouteType == "SEQUENTIAL") {
                        $aTaskDetails = $oTask->load( $idTask );
                        $positionX = $aTaskDetails['TAS_POSX'] + $aTaskDetails['TAS_WIDTH'] / 2;
                        $positionY = $aTaskDetails['TAS_POSY'] + $aTaskDetails['TAS_HEIGHT'] + 10;

                        $aData['PRO_UID'] = $row['PRO_UID'];
                        $aData['EVN_TYPE'] = 'bpmnEventEmptyEnd';
                        $aData['EVN_POSX'] = $positionX;
                        $aData['EVN_POSY'] = $positionY;
                        $aData['EVN_TAS_UID_FROM'] = $idTask;
                        $aData['EVN_STATUS'] = 'ACTIVE';
                        $aData['EVN_RELATED_TO'] = 'MULTIPLE';
                        $aData['EVN_WHEN'] = '1';
                        $aData['EVN_ACTION'] = '';
                        $sEvn_uid = $oEvent->create( $aData );

                        $aField['ROU_UID'] = $routeID;
                        $aField['ROU_EVN_UID'] = $sEvn_uid;
                        $oRoute->update( $aField );
                        $end = 0;
                    }
                }
            }

        }
        return;
    }

    /**
     * updates row tasks from an Route Array
     *
     * @param string $aTasks
     * @return array
     */
    public function updateRouteRows ($aRoutes)
    {
        foreach ($aRoutes as $key => $row) {
            $oRoute = new Route();
            //krumo ($row);
            if (is_array( $oRoute->load( $row['ROU_UID'] ) ))
                $oRoute->remove( $row['ROU_UID'] );
            else
                $res = $oRoute->update( $row );
        }
        return;
    }

    /**
     * Get the assigned users of a task
     *
     * @param string $sTaskUID
     * @param integer $iType
     * @return array
     */
    public function getUsersOfTask ($sTaskUID, $iType)
    {
        try {
            $aUsers = array ();
            $oCriteria = new Criteria( 'workflow' );
            $oCriteria->addJoin( UsersPeer::USR_UID, TaskUserPeer::USR_UID, Criteria::LEFT_JOIN );
            $oCriteria->add( TaskUserPeer::TAS_UID, $sTaskUID );
            $oCriteria->add( TaskUserPeer::TU_TYPE, $iType );
            $oCriteria->add( TaskUserPeer::TU_RELATION, 1 );
            $oDataset = UsersPeer::doSelectRS( $oCriteria );
            $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
            $oDataset->next();
            while ($aRow = $oDataset->getRow()) {
                $aUsers[] = $aRow;
                $oDataset->next();
            }
            return $aUsers;
        } catch (Exception $oError) {
            throw ($oError);
        }
    }

    /**
     * Delete a task
     *
     * @param string $sTaskUID
     * @return void
     */
    function deleteTask ($sTaskUID = '')
    {
        try {
            //Instance classes
            $oTask = new Task();
            $oTasks = new Tasks();
            $oTaskUser = new TaskUser();
            $oStep = new Step();
            $oStepTrigger = new StepTrigger();
            //Get task information
            $aFields = $oTask->load( $sTaskUID );
            //Delete routes
            $oTasks->deleteAllRoutesOfTask( $aFields['PRO_UID'], $sTaskUID, true );
            //Delete gateways
            $oTasks->deleteAllGatewayOfTask( $aFields['PRO_UID'], $sTaskUID, true );
            //Delete the users assigned to task
            $oCriteria = new Criteria( 'workflow' );
            $oCriteria->add( TaskUserPeer::TAS_UID, $sTaskUID );
            $oDataset1 = TaskUserPeer::doSelectRS( $oCriteria );
            $oDataset1->setFetchmode( ResultSet::FETCHMODE_ASSOC );
            $oDataset1->next();
            while ($aRow1 = $oDataset1->getRow()) {
                $oTaskUser->remove( $aRow1['TAS_UID'], $aRow1['USR_UID'], $aRow1['TU_TYPE'], $aRow1['TU_RELATION'] );
                $oDataset1->next();
            }
            //Delete the steps of task
            $oCriteria = new Criteria( 'workflow' );
            $oCriteria->add( StepPeer::TAS_UID, $sTaskUID );
            $oDataset1 = StepPeer::doSelectRS( $oCriteria );
            $oDataset1->setFetchmode( ResultSet::FETCHMODE_ASSOC );
            $oDataset1->next();
            while ($aRow1 = $oDataset1->getRow()) {
                //Delete the triggers assigned to step
                /*$oCriteria = new Criteria('workflow');
        $oCriteria->add(StepTriggerPeer::STEP_UID, $aRow1['STEP_UID']);
        $oDataset2 = StepTriggerPeer::doSelectRS($oCriteria);
        $oDataset2->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $oDataset2->next();
        while ($aRow2 = $oDataset2->getRow()) {
          $oStepTrigger->remove($aRow2['STEP_UID'], $aRow2['TAS_UID'], $aRow2['TRI_UID'], $aRow2['ST_TYPE']);
          $oDataset2->next();
        }*/
                $oStep->remove( $aRow1['STEP_UID'] );
                $oDataset1->next();
            }
            //Delete step triggers
            $oCriteria = new Criteria( 'workflow' );
            $oCriteria->add( StepTriggerPeer::TAS_UID, $sTaskUID );
            StepTriggerPeer::doDelete( $oCriteria );
            //Delete permissions
            $oCriteria = new Criteria( 'workflow' );
            $oCriteria->add( ObjectPermissionPeer::TAS_UID, $sTaskUID );
            ObjectPermissionPeer::doDelete( $oCriteria );
            $oCriteria = new Criteria( 'workflow' );
            $oCriteria->add( ObjectPermissionPeer::OP_TASK_SOURCE, $sTaskUID );
            ObjectPermissionPeer::doDelete( $oCriteria );
            //Delete task
            $oTask->remove( $sTaskUID );
            //Delete cases schedulers added by krlos
            $oCriteria = new Criteria( 'workflow' );
            $oCriteria->add( CaseSchedulerPeer::TAS_UID, $sTaskUID );
            CaseSchedulerPeer::doDelete( $oCriteria );
        } catch (Exception $oError) {
            throw ($oError);
        }
    }

    /**
     * Delete all routes from a task
     *
     * @param string $sProcessUID
     * @param string $sTaskUID
     * @return boolean
     */
    public function deleteAllRoutesOfTask ($sProcessUID = '', $sTaskUID = '', $bAll = false)
    {
        try {
            $oProcess = new Process();
            $aFields = $oProcess->load( $sProcessUID );
            $oTask = new Task();
            $aFields = $oTask->load( $sTaskUID );
            $oCriteria = new Criteria( 'workflow' );
            $oCriteria->add( RoutePeer::PRO_UID, $sProcessUID );
            $oCriteria->add( RoutePeer::TAS_UID, $sTaskUID );
            RoutePeer::doDelete( $oCriteria );
            if ($bAll) {
                $oCriteria = new Criteria( 'workflow' );
                $oCriteria->add( RoutePeer::PRO_UID, $sProcessUID );
                $oCriteria->add( RoutePeer::ROU_NEXT_TASK, $sTaskUID );
                RoutePeer::doDelete( $oCriteria );
            }
            return true;
        } catch (Exception $oError) {
            throw ($oError);
        }
    }

    /**
     * Get all gateways for any Process
     *
     * @param string $sProUid
     * @return array
     */
    public function getAllGateways ($sProUid)
    {
        try {
            $aGateways = array ();
            $oCriteria = new Criteria( 'workflow' );
            $oCriteria->add( GatewayPeer::PRO_UID, $sProUid );
            $oDataset = GatewayPeer::doSelectRS( $oCriteria );
            $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
            $oDataset->next();
            while ($aRow = $oDataset->getRow()) {
                $oGateway = new Gateway();
                $aGateways[] = $oGateway->load( $aRow['GAT_UID'] );
                $oDataset->next();
            }
            return $aGateways;
        } catch (Exception $oError) {
            throw ($oError);
        }
    }

    /**
     * creates row tasks from an Task Array
     *
     * @param string $aTasks
     * @return array
     */
    public function createGatewayRows ($aGateway)
    {
        foreach ($aGateway as $key => $row) {
            $oGateway = new Gateway();
            if ($oGateway->gatewayExists( $row['GAT_UID'] ))
                $oGateway->remove( $row['GAT_UID'] );

            if ($row['TAS_UID'] != '' && $row['GAT_NEXT_TASK'] != '')
                continue;
            else
                $res = $oGateway->createRow( $row );
        }
        return;
    }

    /**
     * Delete all routes from a task
     *
     * @param string $sProcessUID
     * @param string $sTaskUID
     * @return boolean
     */
    public function deleteAllGatewayOfTask ($sProcessUID = '', $sTaskUID = '', $bAll = false)
    {
        try {
            $oProcess = new Process();
            $aFields = $oProcess->load( $sProcessUID );
            $oTask = new Task();
            $aFields = $oTask->load( $sTaskUID );
            $oCriteria = new Criteria( 'workflow' );
            $oCriteria->add( GatewayPeer::PRO_UID, $sProcessUID );
            $oCriteria->add( GatewayPeer::TAS_UID, $sTaskUID );
            GatewayPeer::doDelete( $oCriteria );
            if ($bAll) {
                $oCriteria = new Criteria( 'workflow' );
                $oCriteria->add( GatewayPeer::PRO_UID, $sProcessUID );
                $oCriteria->add( GatewayPeer::GAT_NEXT_TASK, $sTaskUID );
                GatewayPeer::doDelete( $oCriteria );
            }
            return true;
        } catch (Exception $oError) {
            throw ($oError);
        }
    }

    /**
     * Assign a user to task
     *
     * @param string $sTaskUID
     * @param string $sUserUID
     * @param string $iType
     * @return integer
     */
    public function assignUser ($sTaskUID = '', $sUserUID = '', $iType = '')
    {
        try {
            $oTaskUser = new TaskUser();
            return $oTaskUser->create( array ('TAS_UID' => $sTaskUID,'USR_UID' => $sUserUID,'TU_TYPE' => $iType,'TU_RELATION' => 1
            ) );
        } catch (Exception $oError) {
            throw ($oError);
        }
    }

    /**
     * Assign a group to task
     *
     * @param string $sTaskUID
     * @param string $sGroupUID
     * @param string $iType
     * @return integer
     */
    public function assignGroup ($sTaskUID = '', $sGroupUID = '', $iType = '')
    {
        try {
            $oTaskUser = new TaskUser();
            /*$oCriteria = new Criteria('workflow');
      $oCriteria->add(GroupUserPeer::GRP_UID, $sGroupUID);
      $oDataset = GroupUserPeer::doSelectRS($oCriteria);
      $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
      $oDataset->next();
      while ($aGroupUser = $oDataset->getRow()) {
        $oCriteria = new Criteria('workflow');
        $oCriteria->add(TaskUserPeer::TAS_UID, $sTaskUID);
        $oCriteria->add(TaskUserPeer::USR_UID, $aGroupUser['USR_UID']);
        $oDataset2 = TaskUserPeer::doSelectRS($oCriteria);
        $oDataset2->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $oDataset2->next();
        $aRow = $oDataset2->getRow();
        if (!is_array($aRow)) {
          $this->assignUser($sTaskUID, $aGroupUser['USR_UID'], $iType);
        }
        $oDataset->next();
      }*/
            return $oTaskUser->create( array ('TAS_UID' => $sTaskUID,'USR_UID' => $sGroupUID,'TU_TYPE' => $iType,'TU_RELATION' => 2
            ) );
        } catch (Exception $oError) {
            throw ($oError);
        }
    }

    /**
     * of the assign user of all the tasks
     *
     * @param string $sUserUID
     * @return void
     */
    public function ofToAssignUserOfAllTasks ($sUserUID = '')
    {
        try {
            $oCriteria = new Criteria( 'workflow' );
            $oCriteria->add( TaskUserPeer::USR_UID, $sUserUID );
            TaskUserPeer::doDelete( $oCriteria );
        } catch (Exception $oError) {
            throw ($oError);
        }
    }

    /**
     * Of to assign a user from a task
     *
     * @param string $sTaskUID
     * @param string $sUserUID
     * @param integer $iType
     * @return boolean
     */
    public function ofToAssignUser ($sTaskUID = '', $sUserUID = '', $iType = 0)
    {
        try {
            $oTaskUser = new TaskUser();
            $oTaskUser->remove( $sTaskUID, $sUserUID, $iType, 1 );
            return true;
        } catch (Exception $oError) {
            throw ($oError);
        }
    }

    /**
     * Of to assign a group from a task
     *
     * @param string $sTaskUID
     * @param string $sGroupUID
     * @param integer $iType
     * @return boolean
     */
    public function ofToAssignGroup ($sTaskUID = '', $sGroupUID = '', $iType = 0)
    {
        try {
            $oTaskUser = new TaskUser();
            return $oTaskUser->remove( $sTaskUID, $sGroupUID, $iType, 2 );
        } catch (Exception $oError) {
            throw ($oError);
        }
    }

    /**
     * Get the assigned steps of a task
     *
     * @param string $sTaskUID
     * @return array
     */
    public function getStepsOfTask ($sTaskUID)
    {
        try {
            $aSteps = array ();
            $oCriteria = new Criteria( 'workflow' );
            $oCriteria->add( StepPeer::TAS_UID, $sTaskUID );
            $oCriteria->addAscendingOrderByColumn( StepPeer::STEP_POSITION );
            $oDataset = StepPeer::doSelectRS( $oCriteria );
            $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
            $oDataset->next();
            while ($aRow = $oDataset->getRow()) {
                $aSteps[] = $aRow;
                $oDataset->next();
            }
            return $aSteps;
        } catch (Exception $oError) {
            throw ($oError);
        }
    }

    /**
     * Return if exists building elements to add steps
     *
     * @param string $sProcessUID
     * @return boolean
     */
    public function existsBuildingElements ($sProcessUID)
    {
        try {
            $oCriteria = new Criteria( 'workflow' );
            //$oCriteria->add(StepPeer::PRO_UID, $sProcessUID);
            //$oDataset = StepPeer::doSelectRS($oCriteria);
            //$oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            //$oDataset->next();
            return true;
        } catch (Exception $oError) {
            throw ($oError);
        }
    }

    /**
     * Get all tasks for any Process
     *
     * @param string $sProUid
     * @return array
     */
    public function getStartingTaskForUser ($sProUid, $sUsrUid)
    {
        try {
            $aTasks = array ();
            $oCriteria = new Criteria( 'workflow' );
            $oCriteria->add( TaskPeer::PRO_UID, $sProUid );
            //$oCriteria->add(TaskPeer::TAS_USER,    $sUsrUid);
            $oCriteria->add( TaskPeer::TAS_START, 'TRUE' );
            $oDataset = TaskPeer::doSelectRS( $oCriteria );
            $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
            $oDataset->next();
            while ($aRow = $oDataset->getRow()) {
                $oTask = new Task();
                $aTasks[] = $oTask->Load( $aRow['TAS_UID'] );
                $oDataset->next();
            }
            return $aTasks;
        } catch (Exception $oError) {
            throw ($oError);
        }
    }

    /**
     * Verify the user assig in any task
     *
     * @param string $sTaskUID
     * @return array
     */
    public function assignUsertoTask ($sTaskUID)
    {
        try {
            $oCriteria = new Criteria( 'workflow' );
            $oCriteria->addSelectColumn( TaskUserPeer::USR_UID );
            $oCriteria->add( TaskUserPeer::TAS_UID, $sTaskUID );
            $oDataset = TaskUserPeer::doSelectRS( $oCriteria );
            $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
            $oDataset->next();
            $aRow = $oDataset->getRow();
            if (is_array( $aRow ))
                return 1;
            else
                return 0;
        } catch (Exception $oError) {
            throw ($oError);
        }
    }

    /**
     * Verify the user assig in task
     *
     * @param string $sUsrUid, $sTaskUID
     * @return array
     */
    public function verifyUsertoTask ($sUsrUid, $sTaskUID)
    {
        try {
            $oCriteria = new Criteria( 'workflow' );
            $oCriteria->addSelectColumn( TaskUserPeer::USR_UID );
            $oCriteria->addSelectColumn( TaskUserPeer::TAS_UID );
            $oCriteria->addSelectColumn( TaskUserPeer::TU_RELATION );
            $oCriteria->add( TaskUserPeer::TAS_UID, $sTaskUID );
            $oCriteria->add( TaskUserPeer::USR_UID, $sUsrUid );
            $oDataset = TaskUserPeer::doSelectRS( $oCriteria );
            $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
            $oDataset->next();
            $aRow = $oDataset->getRow();
            if (is_array( $aRow ))
                return $aRow;
            else
                return $aRow;
        } catch (Exception $oError) {
            throw ($oError);
        }
    }

    /**
     * Get tasks that the usser is assigned
     *
     * @param string $sUsrUID
     * @return array
     */
    public function getTasksThatUserIsAssigned ($sUserUID)
    {
        try {
            $aTasks = array ();
            $oCriteria = new Criteria( 'workflow' );
            $oCriteria->add( TaskUserPeer::USR_UID, $sUserUID );
            $oCriteria->add( TaskUserPeer::TU_RELATION, 1 );
            $oDataset = TaskUserPeer::doSelectRS( $oCriteria );
            $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
            $oDataset->next();
            while ($aRow = $oDataset->getRow()) {
                $aTasks[] = $aRow['TAS_UID'];
                $oDataset->next();
            }
            $aGroups = array ();
            $oCriteria = new Criteria();
            $oCriteria->add( GroupwfPeer::GRP_UID, '', Criteria::NOT_EQUAL );
            $oCriteria->add( GroupUserPeer::USR_UID, $sUserUID );
            $oCriteria->add( GroupwfPeer::GRP_STATUS, 'ACTIVE' );
            $oCriteria->addJoin( GroupUserPeer::GRP_UID, GroupwfPeer::GRP_UID, Criteria::LEFT_JOIN );
            $oDataset = GroupwfPeer::doSelectRS( $oCriteria );
            $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
            $oDataset->next();
            while ($aRow = $oDataset->getRow()) {
                $aGroups[] = $aRow['GRP_UID'];
                $oDataset->next();
            }
            $oCriteria = new Criteria( 'workflow' );
            $oCriteria->add( TaskUserPeer::USR_UID, $aGroups, Criteria::IN );
            $oCriteria->add( TaskUserPeer::TU_RELATION, 2 );
            $oDataset = TaskUserPeer::doSelectRS( $oCriteria );
            $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
            $oDataset->next();
            while ($aRow = $oDataset->getRow()) {
                if (! in_array( $aRow['TAS_UID'], $aTasks )) {
                    $aTasks[] = $aRow['TAS_UID'];
                }
                $oDataset->next();
            }
            return $aTasks;
        } catch (Exception $oError) {
            throw ($oError);
        }
    }

    /**
     * Get Routes for any Process and any Task
     *
     * @param string $sProUid, $sTaskUid
     * @return array by Everth
     */
    public function getRoute ($sProUid, $sTaskUid)
    {
        try {
            $aRoutes = array ();
            $oCriteria = new Criteria( 'workflow' );
            $oCriteria->add( RoutePeer::PRO_UID, $sProUid );
            $oCriteria->add( RoutePeer::TAS_UID, $sTaskUid );
            $oDataset = RoutePeer::doSelectRS( $oCriteria );
            $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
            $oDataset->next();
            while ($aRow = $oDataset->getRow()) {
                $aRoutes[] = $aRow;
                $oDataset->next();
            }

            return $aRoutes;
        } catch (Exception $oError) {
            throw ($oError);
        }
    }

    /**
     * Get Routes for any Process,route type,route next task
     *
     * @param string $sProUid, $sTaskUid
     * @return array by Girish
     */
    public function getRouteByType ($sProUid, $sRouteNextTaskUid, $sRouteType)
    {
        try {
            $aRoutes = array ();
            $oCriteria = new Criteria( 'workflow' );
            $oCriteria->add( RoutePeer::PRO_UID, $sProUid );
            $oCriteria->add( RoutePeer::ROU_NEXT_TASK, $sRouteNextTaskUid );
            $oCriteria->add( RoutePeer::ROU_TYPE, $sRouteType );
            $oDataset = RoutePeer::doSelectRS( $oCriteria );
            $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
            $oDataset->next();
            while ($aRow = $oDataset->getRow()) {
                $aRoutes[] = $aRow;
                $oDataset->next();
            }

            return $aRoutes;
        } catch (Exception $oError) {
            throw ($oError);
        }
    }

}
?>
