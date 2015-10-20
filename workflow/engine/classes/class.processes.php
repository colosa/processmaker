<?php

/**

 * class.processes.php

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



//require_once 'classes/model/Content.php';

//require_once 'classes/model/Process.php';

//require_once 'classes/model/Task.php';

//require_once 'classes/model/Route.php';

//require_once 'classes/model/SwimlanesElements.php';

//require_once 'classes/model/InputDocument.php';

//require_once 'classes/model/ObjectPermission.php';

//require_once 'classes/model/OutputDocument.php';

//require_once 'classes/model/Step.php';

//require_once 'classes/model/StepTrigger.php';

//require_once 'classes/model/Dynaform.php';

//require_once 'classes/model/Triggers.php';

//require_once 'classes/model/Groupwf.php';

//require_once 'classes/model/ReportTable.php';

//require_once 'classes/model/ReportVar.php';

//require_once 'classes/model/DbSource.php';

//require_once 'classes/model/StepSupervisor.php';

//require_once 'classes/model/SubProcess.php';

//require_once 'classes/model/CaseTracker.php';

//require_once 'classes/model/CaseTrackerObject.php';

//require_once 'classes/model/Stage.php';

//require_once 'classes/model/TaskUser.php';

//require_once 'classes/model/FieldCondition.php';

//require_once 'classes/model/Event.php';

//require_once 'classes/model/CaseScheduler.php';

//require_once 'classes/model/ProcessCategory.php';

//require_once 'classes/model/Configuration.php';



//G::LoadClass( 'tasks' );

//G::LoadClass( 'reportTables' );

//G::LoadClass( 'processMap' );

//G::LoadThirdParty( 'pear/json', 'class.json' );



class Processes

{



    /**

     * change Status of any Process

     *

     * @param string $sProUid

     * @return boolean

     * @package workflow.engine.ProcessMaker

     */

    public function changeStatus ($sProUid = '')

    {

        $oProcess = new Process();

        $Fields = $oProcess->Load( $sProUid );

        $proFields['PRO_UID'] = $sProUid;

        if ($Fields['PRO_STATUS'] == 'ACTIVE') {

            $proFields['PRO_STATUS'] = 'INACTIVE';

        } else {

            $proFields['PRO_STATUS'] = 'ACTIVE';

        }

        $oProcess->Update( $proFields );

    }



    /**

     * change debug mode of any Process

     *

     * @param string $sProUid

     * @return boolean

     * @package workflow.engine.ProcessMaker

     */

    public function changeDebugMode ($sProUid = '')

    {

        $oProcess = new Process();

        $Fields = $oProcess->Load( $sProUid );

        $proFields['PRO_UID'] = $sProUid;

        if ($Fields['PRO_DEBUG'] == '1') {

            $proFields['PRO_DEBUG'] = '0';

        } else {

            $proFields['PRO_DEBUG'] = '1';

        }

        $oProcess->Update( $proFields );

    }



    /**

     * changes in DB the parent GUID

     *

     * @param $sProUid process uid

     * @param $sParentUid process parent uid

     * @return $sProUid

     */

    public function changeProcessParent ($sProUid, $sParentUid)

    {

        $oProcess = new Process();

        $Fields = $oProcess->Load( $sProUid );

        $proFields['PRO_UID'] = $sProUid;

        $Fields['PRO_PARENT'] == $sParentUid;

        $oProcess->Update( $proFields );

    }



    /**

     * verify if the process $sProUid exists

     *

     * @param string $sProUid

     * @return boolean

     */

    public function processExists ($sProUid = '')

    {

        $oProcess = new Process();

        return $oProcess->processExists( $sProUid );

    }



    /**

     * get an unused process GUID

     *

     * @return $sProUid

     */

    public function getUnusedProcessGUID ()

    {

        do {

            $sNewProUid = G::generateUniqueID();

        } while ($this->processExists( $sNewProUid ));

        return $sNewProUid;

    }



    /**

     * verify if the task $sTasUid exists

     *

     * @param string $sTasUid

     * @return boolean

     */

    public function taskExists ($sTasUid = '')

    {

        $oTask = new Task();

        return $oTask->taskExists( $sTasUid );

    }



    /**

     * get an unused task GUID

     *

     * @return $sTasUid

     */

    public function getUnusedTaskGUID ()

    {

        do {

            $sNewTasUid = G::generateUniqueID();

        } while ($this->taskExists( $sNewTasUid ));

        return $sNewTasUid;

    }



    /**

     * verify if the dynaform $sDynUid exists

     *

     * @param string $sDynUid

     * @return boolean

     */

    public function dynaformExists ($sDynUid = '')

    {

        $oDynaform = new Dynaform();

        return $oDynaform->dynaformExists( $sDynUid );

    }



    /**

     * verify if the object exists

     *

     * @param string $sUid

     * @return boolean

     */

    public function inputExists ($sUid = '')

    {

        $oInput = new InputDocument();

        return $oInput->inputExists( $sUid );

    }



    /**

     * verify if the object exists

     *

     * @param string $sUid

     * @return boolean

     */

    public function outputExists ($sUid = '')

    {

        $oOutput = new OutputDocument();

        return $oOutput->outputExists( $sUid );

    }



    /**

     * verify if the object exists

     *

     * @param string $sUid

     * @return boolean

     */

    public function processVariableExists ($sUid = '')

    {

        $oProcessVariable = new ProcessVariables();

        return $oProcessVariable->ProcessVariableExists( $sUid );

    }



    /**

     * verify if the object exists

     *

     * @param string $sUid

     * @return boolean

     */

    public function triggerExists ($sUid = '')

    {

        $oTrigger = new Triggers();

        return $oTrigger->triggerExists( $sUid );

    }



    /**

     * verify if the object exists

     *

     * @param string $sUid

     * @return boolean

     */

    public function SubProcessExists ($sUid = '')

    {

        $oSubProcess = new SubProcess();

        return $oSubProcess->subProcessExists( $sUid );

    }



    /**

     * verify if a caseTrackerObject object exists

     *

     * @param string $sUid

     * @return boolean

     */

    public function caseTrackerObjectExists ($sUid = '')

    {

        $oCaseTrackerObject = new CaseTrackerObject();

        return $oCaseTrackerObject->caseTrackerObjectExists( $sUid );

    }



    /**

     * verify if a caseTracker Object exists

     *

     * @param string $sUid

     * @return boolean

     */

    public function caseTrackerExists ($sUid = '')

    {

        $oCaseTracker = new CaseTracker();

        return $oCaseTracker->caseTrackerExists( $sUid );

    }



    /**

     * verify if a dbconnection exists

     *

     * @param string $sUid

     * @return boolean

     */

    public function dbConnectionExists ($sUid = '')

    {

        $oDBSource = new DbSource();

        return $oDBSource->Exists( $sUid );

    }



    /**

     * verify if a objectPermission exists

     *

     * @param string $sUid

     * @return boolean

     */

    public function objectPermissionExists ($sUid = '')

    {

        $oObjectPermission = new ObjectPermission();

        return $oObjectPermission->Exists( $sUid );

    }



    /**

     * verify if a route exists

     *

     * @param string $sUid

     * @return boolean

     */

    public function routeExists ($sUid = '')

    {

        $oRoute = new Route();

        return $oRoute->routeExists( $sUid );

    }



    /**

     * verify if a stage exists

     *

     * @param string $sUid

     * @return boolean

     */

    public function stageExists ($sUid = '')

    {

        $oStage = new Stage();

        return $oStage->Exists( $sUid );

    }



    /**

     * verify if a swimlane exists

     *

     * @param string $sUid

     * @return boolean

     */

    public function slExists ($sUid = '')

    {

        $oSL = new SwimlanesElements();

        return $oSL->swimlanesElementsExists( $sUid );

    }



    /**

     * verify if a reportTable exists

     *

     * @param string $sUid

     * @return boolean

     */

    public function reportTableExists ($sUid = '')

    {

        $oReportTable = new ReportTable();

        return $oReportTable->reportTableExists( $sUid );

    }



    /**

     * verify if a reportVar exists

     *

     * @param string $sUid

     * @return boolean

     */

    public function reportVarExists ($sUid = '')

    {

        $oReportVar = new ReportVar();

        return $oReportVar->reportVarExists( $sUid );

    }



    /**

     * verify if a caseTrackerObject exists

     *

     * @param string $sUid

     * @return boolean

     */

    public function fieldsConditionsExists ($sUid = '')

    {

        $oFieldCondition = new FieldCondition();

        return $oFieldCondition->Exists( $sUid );

    }



    /**

     * verify if an event exists

     *

     * @param string $sUid

     * @return boolean

     */

    public function eventExists ($sUid = '')

    {

        $oEvent = new Event();

        return $oEvent->Exists( $sUid );

    }



    /**

     * verify if a caseScheduler exists

     *

     * @param string $sUid

     * @return boolean

     */

    public function caseSchedulerExists ($sUid = '')

    {

        $oCaseScheduler = new CaseScheduler();

        return $oCaseScheduler->Exists( $sUid );

    }



    /**

     * Verify if exists the "Process User" in table PROCESS_USER

     *

     * @param string $processUserUid Unique id of "Process User"

     *

     * return bool Return true if exists the "Process User" in table PROCESS_USER, false otherwise

     */

    public function processUserExists($processUserUid)

    {

        try {

            $processUser = new ProcessUser();



            return $processUser->Exists($processUserUid);

        } catch (Exception $e) {

            throw $e;

        }

    }



    /**

     * get an unused input GUID

     *

     * @return $sProUid

     */

    public function getUnusedInputGUID ()

    {

        do {

            $sNewUid = G::generateUniqueID();

        } while ($this->inputExists( $sNewUid ));

        return $sNewUid;

    }



    /**

     * get an unused output GUID

     *

     * @return $sProUid

     */

    public function getUnusedOutputGUID ()

    {

        do {

            $sNewUid = G::generateUniqueID();

        } while ($this->outputExists( $sNewUid ));

        return $sNewUid;

    }



    /**

     * get an unused trigger GUID

     *

     * @return $sProUid

     */

    public function getUnusedTriggerGUID ()

    {

        do {

            $sNewUid = G::generateUniqueID();

        } while ($this->triggerExists( $sNewUid ));

        return $sNewUid;

    }



    /**

     * get an unused trigger GUID

     *

     * @return $sProUid

     */

    public function getUnusedSubProcessGUID ()

    {

        do {

            $sNewUid = G::generateUniqueID();

        } while ($this->subProcessExists( $sNewUid ));

        return $sNewUid;

    }



    /**

     * get a Unused CaseTrackerObject GUID

     *

     * @return $sNewUid a new generated Uid

     */

    public function getUnusedCaseTrackerObjectGUID ()

    {

        do {

            $sNewUid = G::generateUniqueID();

        } while ($this->caseTrackerObjectExists( $sNewUid ));

        return $sNewUid;

    }



    /**

     * get a Unused Database Source GUID

     *

     * @return $sNewUid a new generated Uid

     */

    public function getUnusedDBSourceGUID ()

    {

        do {

            $sNewUid = G::generateUniqueID();

        } while ($this->dbConnectionExists( $sNewUid ));

        return $sNewUid;

    }



    /**

     * get a Unused Object Permission GUID

     *

     * @return $sNewUid a new generated Uid

     */

    public function getUnusedObjectPermissionGUID ()

    {

        do {

            $sNewUid = G::generateUniqueID();

        } while ($this->objectPermissionExists( $sNewUid ));

        return $sNewUid;

    }



    /**

     * get a Unused Route GUID

     *

     * @return $sNewUid a new generated Uid

     */

    public function getUnusedRouteGUID ()

    {

        do {

            $sNewUid = G::generateUniqueID();

        } while ($this->routeExists( $sNewUid ));

        return $sNewUid;

    }



    /**

     * get a Unused Stage GUID

     *

     * @return $sNewUid a new generated Uid

     */

    public function getUnusedStageGUID ()

    {

        do {

            $sNewUid = G::generateUniqueID();

        } while ($this->stageExists( $sNewUid ));

        return $sNewUid;

    }



    /**

     * get a Unused SL GUID

     *

     * @return $sNewUid a new generated Uid

     */

    public function getUnusedSLGUID ()

    {

        do {

            $sNewUid = G::generateUniqueID();

        } while ($this->slExists( $sNewUid ));

        return $sNewUid;

    }



    /**

     * get a Unused Report Table GUID

     *

     * @return $sNewUid a new generated Uid

     */

    public function getUnusedRTGUID ()

    {

        do {

            $sNewUid = G::generateUniqueID();

        } while ($this->reportTableExists( $sNewUid ));

        return $sNewUid;

    }



    /**

     * get a Unused Report Var GUID

     *

     * @return $sNewUid a new generated Uid

     */

    public function getUnusedRTVGUID ()

    {

        do {

            $sNewUid = G::generateUniqueID();

        } while ($this->reportVarExists( $sNewUid ));

        return $sNewUid;

    }



    /**

     * verify if the object exists

     *

     * @param string $sUid

     * @return boolean

     */

    public function stepExists ($sUid = '')

    {

        $oStep = new Step();

        return $oStep->stepExists( $sUid );

    }



    /**

     * get an unused step GUID

     *

     * @return $sUid

     */

    public function getUnusedStepGUID ()

    {

        do {

            $sNewUid = G::generateUniqueID();

        } while ($this->stepExists( $sNewUid ));

        return $sNewUid;

    }



    /*

     * get an unused Dynaform GUID

     * @return $sDynUid

     */

    public function getUnusedDynaformGUID ()

    {

        do {

            $sNewUid = G::generateUniqueID();

        } while ($this->dynaformExists( $sNewUid ));

        return $sNewUid;

    }



    /**

     * get a Unused Field Condition GUID

     *

     * @return $sNewUid a new generated Uid

     */

    public function getUnusedFieldConditionGUID ()

    {

        do {

            $sNewUid = G::generateUniqueID();

        } while ($this->fieldsConditionsExists( $sNewUid ));

        return $sNewUid;

    }



    /**

     * get a Unused Event GUID

     *

     * @return $sNewUid a new generated Uid

     */

    public function getUnusedEventGUID ()

    {

        do {

            $sNewUid = G::generateUniqueID();

        } while ($this->eventExists( $sNewUid ));

        return $sNewUid;

    }



    /**

     * get a Unused Case Scheduler GUID

     *

     * @return $sNewUid a new generated Uid

     */

    public function getUnusedCaseSchedulerGUID ()

    {

        do {

            $sNewUid = G::generateUniqueID();

        } while ($this->caseSchedulerExists( $sNewUid ));

        return $sNewUid;

    }



    /**

     * Get an unused "Process User" unique id

     *

     * return string Return a new generated unique id

     */

    public function getUnusedProcessUserUid()

    {

        try {

            do {

                $newUid = G::generateUniqueID();

            } while ($this->processUserExists($newUid));



            return $newUid;

        } catch (Exception $e) {

            throw $e;

        }

    }



    /**

     * get an unused process variables GUID

     *

     * @return $sProUid

     */

    public function getUnusedProcessVariableGUID ()

    {

        do {

            $sNewUid = G::generateUniqueID();

        } while ($this->processVariableExists( $sNewUid ));

        return $sNewUid;

    }



    /**

     * Get an unused unique id for Message-Type

     *

     * @return string $uid

     */

    public function getUnusedMessageTypeUid()

    {

        try {

            $messageType = new \ProcessMaker\BusinessModel\MessageType();



            do {

                $newUid = \ProcessMaker\Util\Common::generateUID();

            } while ($messageType->exists($newUid));



            return $newUid;

        } catch (Exception $e) {

            throw $e;

        }



    }



    /**

     * Get an unused unique id for Message-Type-Variable

     *

     * @return string $uid

     */

    public function getUnusedMessageTypeVariableUid()

    {

        try {

            $variable = new \ProcessMaker\BusinessModel\MessageType\Variable();



            do {

                $newUid = \ProcessMaker\Util\Common::generateUID();

            } while ($variable->exists($newUid));



            return $newUid;

        } catch (Exception $e) {

            throw $e;

        }



    }



    /**

     * change the GUID for a serialized process

     *

     * @param string $sProUid

     * @return boolean

     */

    public function setProcessGUID (&$oData, $sNewProUid)

    {

        $sProUid = $oData->process['PRO_UID'];

        $oData->process['PRO_UID'] = $sNewProUid;



        if (isset( $oData->tasks ) && is_array( $oData->tasks )) {

            foreach ($oData->tasks as $key => $val) {

                $oData->tasks[$key]['PRO_UID'] = $sNewProUid;

            }

        }



        if (isset( $oData->routes ) && is_array( $oData->routes )) {

            foreach ($oData->routes as $key => $val) {

                $oData->routes[$key]['PRO_UID'] = $sNewProUid;

            }

        }



        if (isset( $oData->lanes ) && is_array( $oData->lanes )) {

            foreach ($oData->lanes as $key => $val) {

                $oData->lanes[$key]['PRO_UID'] = $sNewProUid;

            }

        }



        if (isset( $oData->inputs ) && is_array( $oData->inputs )) {

            foreach ($oData->inputs as $key => $val) {

                $oData->inputs[$key]['PRO_UID'] = $sNewProUid;

            }

        }



        if (isset( $oData->outputs ) && is_array( $oData->outputs )) {

            foreach ($oData->outputs as $key => $val) {

                $oData->outputs[$key]['PRO_UID'] = $sNewProUid;

            }

        }



        if (isset( $oData->steps ) && is_array( $oData->steps )) {

            foreach ($oData->steps as $key => $val) {

                $oData->steps[$key]['PRO_UID'] = $sNewProUid;

            }

        }



        if (isset( $oData->dynaforms ) && is_array( $oData->dynaforms )) {

            foreach ($oData->dynaforms as $key => $val) {

                $oData->dynaforms[$key]['PRO_UID'] = $sNewProUid;

            }

        }



        if (isset( $oData->triggers ) && is_array( $oData->triggers )) {

            foreach ($oData->triggers as $key => $val) {

                $oData->triggers[$key]['PRO_UID'] = $sNewProUid;

            }

        }



        if (isset( $oData->reportTables ) && is_array( $oData->reportTables )) {

            foreach ($oData->reportTables as $key => $val) {

                $oData->reportTables[$key]['PRO_UID'] = $sNewProUid;

            }

        }



        if (isset( $oData->reportTablesVars ) && is_array( $oData->reportTablesVars )) {

            foreach ($oData->reportTablesVars as $key => $val) {

                $oData->reportTablesVars[$key]['PRO_UID'] = $sNewProUid;

            }

        }



        if (isset( $oData->dbconnections ) && is_array( $oData->dbconnections )) {

            foreach ($oData->dbconnections as $key => $val) {

                $oData->dbconnections[$key]['PRO_UID'] = $sNewProUid;

            }

        }



        if (isset( $oData->stepSupervisor ) && is_array( $oData->stepSupervisor )) {

            foreach ($oData->stepSupervisor as $key => $val) {

                $oData->stepSupervisor[$key]['PRO_UID'] = $sNewProUid;

            }

        }



        if (isset( $oData->objectPermissions ) && is_array( $oData->objectPermissions )) {

            foreach ($oData->objectPermissions as $key => $val) {

                $oData->objectPermissions[$key]['PRO_UID'] = $sNewProUid;

            }

        }



        if (isset( $oData->caseTracker ) && is_array( $oData->caseTracker )) {

            foreach ($oData->caseTracker as $key => $val) {

                $oData->caseTracker[$key]['PRO_UID'] = $sNewProUid;

            }

        }



        if (isset( $oData->caseTrackerObject ) && is_array( $oData->caseTrackerObject )) {

            foreach ($oData->caseTrackerObject as $key => $val) {

                $oData->caseTrackerObject[$key]['PRO_UID'] = $sNewProUid;

            }

        }



        if (isset( $oData->stage ) && is_array( $oData->stage )) {

            foreach ($oData->stage as $key => $val) {

                $oData->stage[$key]['PRO_UID'] = $sNewProUid;

            }

        }



        if (isset( $oData->subProcess ) && is_array( $oData->subProcess )) {

            foreach ($oData->subProcess as $key => $val) {

                $oData->subProcess[$key]['PRO_PARENT'] = $sNewProUid;

            }

        }



        if (isset( $oData->event ) && is_array( $oData->event )) {

            foreach ($oData->event as $key => $val) {

                $oData->event[$key]['PRO_UID'] = $sNewProUid;

            }

        }



        if (isset( $oData->caseScheduler ) && is_array( $oData->caseScheduler )) {

            foreach ($oData->caseScheduler as $key => $val) {

                $oData->caseScheduler[$key]['PRO_UID'] = $sNewProUid;

            }

        }



        if (isset($oData->processUser)) {

            foreach ($oData->processUser as $key => $value) {

                $oData->processUser[$key]["PRO_UID"] = $sNewProUid;

            }

        }



        if (isset($oData->processVariables)) {

            foreach ($oData->processVariables as $key => $value) {

                $oData->processVariables[$key]["PRJ_UID"] = $sNewProUid;

            }

        }



        if (isset($oData->messageType)) {

            foreach ($oData->messageType as $key => $value) {

                $oData->messageType[$key]["PRJ_UID"] = $sNewProUid;

            }

        }



        if (isset($oData->emailEvent)) {

            foreach ($oData->emailEvent as $key => $value) {

                $oData->emailEvent[$key]["PRJ_UID"] = $sNewProUid;

            }

        }



        if (isset($oData->filesManager)) {

            foreach ($oData->filesManager as $key => $value) {

                $oData->filesManager[$key]["PRO_UID"] = $sNewProUid;

            }

        }

        

        if (isset($oData->abeConfiguration)) {

            foreach ($oData->abeConfiguration as $key => $value) {

                $oData->abeConfiguration[$key]["PRO_UID"] = $sNewProUid;

            }

        }



        return true;

    }



    /**

     * change the GUID Parent for a serialized process, only in serialized data

     *

     * @param string $sProUid

     * @return boolean

     */

    public function setProcessParent (&$oData, $sParentUid)

    {

        $oData->process['PRO_PARENT'] = $sParentUid;

        $oData->process['PRO_CREATE_DATE'] = date( 'Y-m-d H:i:s' );

        $oData->process['PRO_UPDATE_DATE'] = date( 'Y-m-d H:i:s' );

        return true;

    }



    /**

     * change and Renew all Task GUID, because the process needs to have a new set of tasks

     *

     * @param string $oData

     * @return boolean

     */

    public function renewAllTaskGuid (&$oData)

    {

        $map = array ();

        foreach ($oData->tasks as $key => $val) {

            if (!isset($val["TAS_UID_OLD"])) {

                $uidNew = $this->getUnusedTaskGUID();

                $map[$val["TAS_UID"]] = $uidNew;



                $oData->tasks[$key]["TAS_UID"] = $uidNew;

            } else {

                $map[$val["TAS_UID_OLD"]] = $val["TAS_UID"];

            }

        }



        $oData->uid["TASK"] = $map;



        if (isset( $oData->routes ) && is_array( $oData->routes )) {

            foreach ($oData->routes as $key => $value) {

                $record = $value;



                if (isset($map[$record["TAS_UID"]])) {

                    $newUid = $map[$record["TAS_UID"]];



                    $oData->routes[$key]["TAS_UID"] = $newUid;



                    if (strlen($record["ROU_NEXT_TASK"]) > 0 && $record["ROU_NEXT_TASK"] > 0) {

                        $newUid = $map[$record["ROU_NEXT_TASK"]];

                        $oData->routes[$key]["ROU_NEXT_TASK"] = $newUid;

                    }

                }

            }

        }



        if (isset( $oData->steps ) && is_array( $oData->steps )) {

            foreach ($oData->steps as $key => $value) {

                $record = $value;



                if (isset($map[$record["TAS_UID"]])) {

                    $newUid = $map[$record["TAS_UID"]];



                    $oData->steps[$key]["TAS_UID"] = $newUid;

                }

            }

        }



        if (isset( $oData->steptriggers ) && is_array( $oData->steptriggers )) {

            foreach ($oData->steptriggers as $key => $val) {

                $newGuid = $map[$val['TAS_UID']];

                $oData->steptriggers[$key]['TAS_UID'] = $newGuid;

            }

        }



        if (isset( $oData->taskusers ) && is_array( $oData->taskusers )) {

            foreach ($oData->taskusers as $key => $val) {

                $newGuid = $map[$val['TAS_UID']];

                $oData->taskusers[$key]['TAS_UID'] = $newGuid;

            }

        }



        if (isset( $oData->subProcess ) && is_array( $oData->subProcess )) {

            foreach ($oData->subProcess as $key => $val) {

                $newGuid = $map[$val['TAS_PARENT']];

                $oData->subProcess[$key]['TAS_PARENT'] = $newGuid;

                if (isset( $map[$val['TAS_UID']] )) {

                    $newGuid = $map[$val['TAS_UID']];

                    $oData->subProcess[$key]['TAS_UID'] = $newGuid;

                }

            }

        }



        if (isset( $oData->objectPermissions ) && is_array( $oData->objectPermissions )) {

            foreach ($oData->objectPermissions as $key => $val) {

                if (isset( $map[$val['TAS_UID']] )) {

                    $newGuid = $map[$val['TAS_UID']];

                    $oData->objectPermissions[$key]['TAS_UID'] = $newGuid;

                }

            }

        }



        // New process bpmn

        if (isset( $oData->event ) && is_array( $oData->event )) {

            foreach ($oData->event as $key => $val) {

                if (isset( $val['EVN_TAS_UID_FROM'] ) && isset( $map[$val['EVN_TAS_UID_FROM']] )) {

                    $newGuid = $map[$val['EVN_TAS_UID_FROM']];

                    $oData->event[$key]['EVN_TAS_UID_FROM'] = $newGuid;

                }

            }

        }



        if (isset( $oData->caseScheduler ) && is_array( $oData->caseScheduler )) {

            foreach ($oData->caseScheduler as $key => $val) {

                if (isset( $map[$val['TAS_UID']] )) {

                    $newGuid = $map[$val['TAS_UID']];

                    $oData->caseScheduler[$key]['TAS_UID'] = $newGuid;

                }

            }

        }



        if (isset($oData->taskExtraProperties)) {

            foreach ($oData->taskExtraProperties as $key => $value) {

                $record = $value;



                if (isset($map[$record["OBJ_UID"]])) {

                    $newUid = $map[$record["OBJ_UID"]];



                    $oData->taskExtraProperties[$key]["OBJ_UID"] = $newUid;

                }

            }

        }



        if (isset($oData->webEntry)) {

            foreach ($oData->webEntry as $key => $value) {

                $record = $value;



                if (isset($map[$record["TAS_UID"]])) {

                    $newUid = $map[$record["TAS_UID"]];



                    $oData->webEntry[$key]["TAS_UID"] = $newUid;

                }

            }

        }



        if (isset($oData->webEntryEvent)) {

            foreach ($oData->webEntryEvent as $key => $value) {

                $record = $value;



                if (isset($map[$record["ACT_UID"]])) {

                    $newUid = $map[$record["ACT_UID"]];



                    $oData->webEntryEvent[$key]["ACT_UID"] = $newUid;

                }

            }

        }

        

        if (isset( $oData->abeConfiguration ) && is_array( $oData->abeConfiguration )) {

            foreach ($oData->abeConfiguration as $key => $value) {

                $record = $value;

                if (isset($map[$record["TAS_UID"]])) {

                    $newUid = $map[$record["TAS_UID"]];

                    $oData->abeConfiguration[$key]["TAS_UID"] = $newUid;

                }

            }

        }

    }



    /**

     * change and Renew all Dynaform GUID, because the process needs to have a new set of dynaforms

     *

     * @param string $oData

     * @return boolean

     */

    public function renewAllDynaformGuid (&$oData)

    {

        $map = array ();

        foreach ($oData->dynaforms as $key => $val) {

            $newGuid = $this->getUnusedDynaformGUID();

            $map[$val['DYN_UID']] = $newGuid;

            $oData->dynaforms[$key]['DYN_UID'] = $newGuid;

        }



        $oData->uid["DYNAFORM"] = $map;



        if (isset( $oData->process['PRO_DYNAFORMS'] ) && ! is_array( $oData->process['PRO_DYNAFORMS'] )) {

            $oData->process['PRO_DYNAFORMS'] = @unserialize( $oData->process['PRO_DYNAFORMS'] );

        }



        if (! isset( $oData->process['PRO_DYNAFORMS']['PROCESS'] )) {

            $oData->process['PRO_DYNAFORMS']['PROCESS'] = '';

        }



        if ($oData->process['PRO_DYNAFORMS']['PROCESS'] != '') {

            $oData->process['PRO_DYNAFORMS']['PROCESS'] = $map[$oData->process['PRO_DYNAFORMS']['PROCESS']];

        }



        foreach ($oData->steps as $key => $val) {

            if ($val['STEP_TYPE_OBJ'] == 'DYNAFORM') {

                $newGuid = $map[$val['STEP_UID_OBJ']];

                $oData->steps[$key]['STEP_UID_OBJ'] = $newGuid;

            }

        }



        if (isset( $oData->caseTrackerObject ) && is_array( $oData->caseTrackerObject )) {

            foreach ($oData->caseTrackerObject as $key => $val) {

                if ($val['CTO_TYPE_OBJ'] == 'DYNAFORM') {

                    $newGuid = $map[$val['CTO_UID_OBJ']];

                    $oData->steps[$key]['CTO_UID_OBJ'] = $newGuid;

                }

            }

        }

        if (isset( $oData->objectPermissions ) && is_array( $oData->objectPermissions )) {

            foreach ($oData->objectPermissions as $key => $val) {

                if ($val['OP_OBJ_TYPE'] == 'DYNAFORM') {

                    if (isset( $map[$val['OP_OBJ_UID']] )) {

                        $newGuid = $map[$val['OP_OBJ_UID']];

                        $oData->objectPermissions[$key]['OP_OBJ_UID'] = $newGuid;

                    }

                }

            }

        }

        if (isset( $oData->stepSupervisor ) && is_array( $oData->stepSupervisor )) {

            foreach ($oData->stepSupervisor as $key => $val) {

                if ($val['STEP_TYPE_OBJ'] == 'DYNAFORM') {

                    $newGuid = $map[$val['STEP_UID_OBJ']];

                    $oData->stepSupervisor[$key]['STEP_UID_OBJ'] = $newGuid;

                }

            }



            if (isset($oData->dynaformFiles)) {

                foreach ($oData->dynaformFiles as $key => $value) {

                    $newGuid = $map[$key];

                    $oData->dynaformFiles[$key] = $newGuid;

                }

            }

        }

        if (isset( $oData->gridFiles )) {

            foreach ($oData->gridFiles as $key => $val) {

                $newGuid = $map[$key];

                $oData->gridFiles[$key] = $newGuid;

            }

        }

        if (isset( $oData->fieldCondition ) && is_array( $oData->fieldCondition )) {

            foreach ($oData->fieldCondition as $key => $val) {

                $newGuid = $map[$val['FCD_DYN_UID']];

                $oData->fieldCondition[$key]['FCD_DYN_UID'] = $newGuid;

            }

        }



        if (isset($oData->webEntry)) {

            foreach ($oData->webEntry as $key => $value) {

                $record = $value;



                if (isset($map[$record["DYN_UID"]])) {

                    $newUid = $map[$record["DYN_UID"]];



                    $oData->webEntry[$key]["DYN_UID"] = $newUid;

                }

            }

        }



        if (isset($oData->webEntryEvent)) {

            foreach ($oData->webEntryEvent as $key => $value) {

                $record = $value;



                if (isset($map[$record["DYN_UID"]])) {

                    $newUid = $map[$record["DYN_UID"]];



                    $oData->webEntryEvent[$key]["DYN_UID"] = $newUid;

                }

            }

        }

        

        if (isset( $oData->abeConfiguration ) && is_array( $oData->abeConfiguration )) {

            foreach ($oData->abeConfiguration as $key => $value) {

                $record = $value;

                if (isset($map[$record["DYN_UID"]])) {

                    $newUid = $map[$record["DYN_UID"]];

                    $oData->abeConfiguration[$key]["DYN_UID"] = $newUid;

                }

            }

        }

    }



    /**

     * get a Process with a search based in the process Uid

     *

     * @param $sProUid string process Uid

     * @return $oProcess Process object

     */

    public function getProcessRow ($sProUid, $getAllLang = false)

    {

        $oProcess = new Process();

        return $oProcess->Load( $sProUid, $getAllLang );

    }



    /**

     * creates a process new process if a process exists with the same uid of the

     * $row['PRO_UID'] parameter then deletes it from the database and creates

     * a new one based on the $row parameter

     *

     * @param $row array parameter with the process data

     * @return $oProcess Process object

     */

    public function createProcessRow ($row)

    {

        $oProcess = new Process();

        if ($oProcess->processExists( $row['PRO_UID'] )) {

            $oProcess->remove( $row['PRO_UID'] );

        }

        return $oProcess->createRow( $row );

    }



    /**

     * Update a Process register in DB, if the process doesn't exist with the same

     * uid of the $row['PRO_UID'] parameter the function creates a new one based

     * on the $row parameter data.

     *

     * @param $row array parameter with the process data

     * @return $oProcess Process object

     */

    public function updateProcessRow ($row)

    {

        $oProcess = new Process();

        if ($oProcess->processExists( $row['PRO_UID'] )) {

            $oProcess->update( $row );

        } else {

            $oProcess->create( $row );

        }

    }



    /**

     * Gets the subprocess data from a process and returns it in an array.

     *

     * @param $sProUid string for the process Uid

     * @return $aSubProcess array

     */

    public function getSubProcessRow ($sProUid)

    {

        try {

            $aSubProcess = array ();

            $oCriteria = new Criteria( 'workflow' );

            $oCriteria->add( SubProcessPeer::PRO_PARENT, $sProUid );

            $oDataset = SubProcessPeer::doSelectRS( $oCriteria );

            $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );

            $oDataset->next();

            while ($aRow = $oDataset->getRow()) {

                $aSubProcess[] = $aRow;

                $oDataset->next();

            }

            return $aSubProcess;

        } catch (Exception $oError) {

            throw ($oError);

        }

    }



    /**

     * Gets a case Tracker Row from a process and returns it in an array.

     *

     * @param $sProUid string for the process Uid

     * @return $aCaseTracker array

     */



    public function getCaseTrackerRow ($sProUid)

    {

        try {

            $aCaseTracker = array ();

            $oCriteria = new Criteria( 'workflow' );

            $oCriteria->add( CaseTrackerPeer::PRO_UID, $sProUid );

            $oDataset = CaseTrackerPeer::doSelectRS( $oCriteria );

            $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );

            $oDataset->next();

            while ($aRow = $oDataset->getRow()) {

                $aCaseTracker[] = $aRow;

                $oDataset->next();

            }

            return $aCaseTracker;

        } catch (Exception $oError) {

            throw ($oError);

        }

    }



    /**

     * Gets a case TrackerObject Row from a process and returns it in an array.

     *

     * @param $sProUid string for the process Uid

     * @return $aCaseTracker array

     */

    public function getCaseTrackerObjectRow ($sProUid)

    {

        try {

            $aCaseTrackerObject = array ();

            $oCriteria = new Criteria( 'workflow' );

            $oCriteria->add( CaseTrackerObjectPeer::PRO_UID, $sProUid );

            $oDataset = CaseTrackerObjectPeer::doSelectRS( $oCriteria );

            $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );

            $oDataset->next();

            while ($aRow = $oDataset->getRow()) {

                $aCaseTrackerObject[] = $aRow;

                $oDataset->next();

            }

            return $aCaseTrackerObject;

        } catch (Exception $oError) {

            throw ($oError);

        }

    }



    /**

     * Gets a Stage Row from a process and returns it in an array.

     *

     * @param $sProUid string for the process Uid

     * @return $aStage array

     */

    public function getStageRow ($sProUid)

    {

        try {

            $aStage = array ();

            $oCriteria = new Criteria( 'workflow' );

            $oCriteria->add( StagePeer::PRO_UID, $sProUid );

            $oDataset = StagePeer::doSelectRS( $oCriteria );

            $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );

            $oDataset->next();

            while ($aRow = $oDataset->getRow()) {

                $oStage = new Stage();

                $aStage[] = $oStage->load( $aRow['STG_UID'] );

                $oDataset->next();

            }

            return $aStage;

        } catch (Exception $oError) {

            throw ($oError);

        }

    }



    /**

     * Gets the Field Conditions from a process and returns those in an array.

     *

     * @param $sProUid string for the process Uid

     * @return $aFieldCondition array

     */



    public function getFieldCondition ($sProUid)

    {

        try {

            $aFieldCondition = array ();

            $oCriteria = new Criteria( 'workflow' );

            $oCriteria->add( DynaformPeer::PRO_UID, $sProUid );

            $oCriteria->addJoin( DynaformPeer::DYN_UID, FieldConditionPeer::FCD_DYN_UID );

            $oDataset = FieldConditionPeer::doSelectRS( $oCriteria );

            $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );

            $oDataset->next();

            while ($aRow = $oDataset->getRow()) {

                $aFieldCondition[] = $aRow;

                $oDataset->next();

            }

            return $aFieldCondition;

        } catch (Exception $oError) {

            throw ($oError);

        }

    }



    /**

     * Gets the Event rows from a process and returns those in an array.

     *

     * @param $sProUid string for the process Uid

     * @return $aEvent array

     */

    public function getEventRow ($sProUid)

    {

        try {

            $aEvent = array ();

            $oCriteria = new Criteria( 'workflow' );



            $oCriteria->add( EventPeer::PRO_UID, $sProUid );

            $oDataset = EventPeer::doSelectRS( $oCriteria );

            $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );

            $oDataset->next();

            while ($aRow = $oDataset->getRow()) {

                $oEvent = new Event();

                $aEvent[] = $oEvent->load( $aRow['EVN_UID'] );

                $oDataset->next();

            }

            return $aEvent;

        } catch (Exception $oError) {

            throw ($oError);

        }

    }



    /**

     * Gets the Cases Scheduler rows from a process and returns those in an array.

     *

     * @param $sProUid string for the process Uid

     * @return $aCaseScheduler array

     */

    public function getCaseSchedulerRow ($sProUid)

    {

        try {

            $aCaseScheduler = array ();

            $oCriteria = new Criteria( 'workflow' );



            $oCriteria->add( CaseSchedulerPeer::PRO_UID, $sProUid );

            $oDataset = CaseSchedulerPeer::doSelectRS( $oCriteria );

            $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );

            $oDataset->next();

            while ($aRow = $oDataset->getRow()) {

                $oCaseScheduler = new CaseScheduler();

                $aCaseScheduler[] = $oCaseScheduler->load( $aRow['SCH_UID'] );

                $oDataset->next();

            }

            return $aCaseScheduler;

        } catch (Exception $oError) {

            throw ($oError);

        }

    }



    /**

     * Gets processCategory record, if the process had one

     *

     * @param $sProUid string for the process Uid

     * @return $processCategory array

     */

    public function getProcessCategoryRow ($sProUid)

    {

        $process = ProcessPeer::retrieveByPK( $sProUid );



        if ($process->getProCategory() == '') {

            return null;

        }



        $oCriteria = new Criteria( 'workflow' );

        $oCriteria->add( ProcessCategoryPeer::CATEGORY_UID, $process->getProCategory() );

        $oDataset = ProcessCategoryPeer::doSelectRS( $oCriteria );

        $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );

        $oDataset->next();



        return $oDataset->getRow();

    }



    /**

     * Get all Swimlanes Elements for any Process

     *

     * @param string $sProUid

     * @return array

     */

    public function getAllLanes ($sProUid)

    {

        try {

            $aLanes = array ();

            $oCriteria = new Criteria( 'workflow' );

            $oCriteria->add( SwimlanesElementsPeer::PRO_UID, $sProUid );

            $oDataset = SwimlanesElementsPeer::doSelectRS( $oCriteria );

            $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );

            $oDataset->next();

            while ($aRow = $oDataset->getRow()) {

                $oSwim = new SwimlanesElements();

                $aLanes[] = $oSwim->Load( $aRow['SWI_UID'] );

                $oDataset->next();

            }

            return $aLanes;

        } catch (Exception $oError) {

            throw ($oError);

        }

    }



    /**

     * Get Task Rows from a process and returns those in an array.

     *

     * @param $sProUid string for the process Uid

     * @return $oTask array

     */

    public function getTaskRows ($sProUid)

    {

        $oTask = new Tasks();

        return $oTask->getAllTasks( $sProUid );

    }



    /**

     * Create Task Rows from a $aTasks array data and returns those in an array.

     *

     * @param $aTasks array

     * @return $oTask array

     */

    public function createTaskRows ($aTasks)

    {

        $oTask = new Tasks();

        return $oTask->createTaskRows( $aTasks );

    }



    /**

     * Update Task Rows from a $aTasks array data and returns those in an array.

     *

     * @param $aTasks array

     * @return $oTask array

     */

    public function updateTaskRows ($aTasks)

    {

        $oTask = new Tasks();

        return $oTask->updateTaskRows( $aTasks );

    }



    /**

     * Gets all Route rows from a Process and returns those in an array.

     *

     * @param $sProUid string for the process Uid

     * @return $oTask Tasks array

     */

    public function getRouteRows ($sProUid)

    {

        $oTask = new Tasks();

        return $oTask->getAllRoutes( $sProUid );

    }



    /**

     * Create Route Rows from a $aRoutes array data and returns those in an array.

     *

     * @param $aRoutes array

     * @return $oTask Tasks array

     */

    public function createRouteRows ($aRoutes)

    {

        $oTask = new Tasks();

        return $oTask->createRouteRows( $aRoutes );

    }



    /**

     * Update Route Rows from a $aRoutes array data and returns those in an array.

     *

     * @param $aRoutes array

     * @return $oTask Tasks array

     */

    public function updateRouteRows ($aRoutes)

    {

        $oTask = new Tasks();

        return $oTask->updateRouteRows( $aRoutes );

    }



    /**

     * Get Lane Rows from a Process and returns those in an array.

     *

     * @param $sProUid string for the process Uid

     * @return array

     */

    public function getLaneRows ($sProUid)

    {

        return $this->getAllLanes( $sProUid );

    }



    /**

     * Get Gateway Rows from a process and returns those in an array.

     *

     * @param $sProUid string for the process Uid

     * @return $oTask array

     */

    public function getGatewayRows ($sProUid)

    {

        $oTask = new Tasks();

        return $oTask->getAllGateways( $sProUid );

    }



    /**

     * Create Gateway Rows from a $aGateways array data and returns those in an array.

     *

     * @param $aGateways array

     * @return $oGateway array

     */

    public function createGatewayRows ($aGateways)

    {

        $oTask = new Tasks();

        return $oTask->createGatewayRows( $aGateways );

    }



    /**

     * Create Lane Rows from a $aLanes array data and returns those in an array.

     *

     * @param $aLanes array.

     * @return void

     */

    public function createLaneRows ($aLanes)

    {

        foreach ($aLanes as $key => $row) {

            $oLane = new SwimlanesElements();

            if ($oLane->swimlanesElementsExists( $row['SWI_UID'] )) {

                $oLane->remove( $row['SWI_UID'] );

            }

            $res = $oLane->create( $row );

        }

        return;

    }



    /**

     * Create Sub Process rows from an array, removing those subprocesses with

     * the same UID.

     *

     * @param $SubProcess array

     * @return void.

     */

    public function createSubProcessRows ($SubProcess)

    {

        foreach ($SubProcess as $key => $row) {

            $oSubProcess = new SubProcess();



            //if ($oSubProcess->subProcessExists( $row['SP_UID'] )) {

            //    $oSubProcess->remove( $row['SP_UID'] );

            //}



            //Delete

            $criteria = new Criteria("workflow");



            $criteria->add(SubProcessPeer::PRO_PARENT, $row["PRO_PARENT"], Criteria::EQUAL);

            $criteria->add(SubProcessPeer::TAS_PARENT, $row["TAS_PARENT"], Criteria::EQUAL);



            $result = SubProcessPeer::doDelete($criteria);



            //Create

            $res = $oSubProcess->create( $row );

        }

        return;

    }



    /**

     * Create Case Tracker rows from an array, removing those Trackers with

     * the same UID.

     *

     * @param $CaseTracker array.

     * @return void

     */

    public function createCaseTrackerRows ($CaseTracker)

    {

        if (is_array( $CaseTracker )) {

            foreach ($CaseTracker as $key => $row) {

                $oCaseTracker = new CaseTracker();

                if ($oCaseTracker->caseTrackerExists( $row['PRO_UID'] )) {

                    $oCaseTracker->remove( $row['PRO_UID'] );

                }

                $res = $oCaseTracker->create( $row );

            }

        }

        return;

    }



    /**

     * Create Case Tracker Objects rows from an array, removing those Objects

     * with the same UID, and recreaiting those from the array data.

     *

     * @param $CaseTrackerObject array.

     * @return void

     */

    public function createCaseTrackerObjectRows ($CaseTrackerObject)

    {

        foreach ($CaseTrackerObject as $key => $row) {

            $oCaseTrackerObject = new CaseTrackerObject();

            if ($oCaseTrackerObject->caseTrackerObjectExists( $row['CTO_UID'] )) {

                $oCaseTrackerObject->remove( $row['CTO_UID'] );

            }

            $res = $oCaseTrackerObject->create( $row );

        }

        return;

    }



    /**

     * Create Object Permissions rows from an array, removing those Objects

     * with the same UID, and recreaiting the records from the array data.

     *

     * @param $sProUid string for the process Uid.

     * @return void

     */

    public function createObjectPermissionsRows ($ObjectPermissions)

    {

        foreach ($ObjectPermissions as $key => $row) {

            $oObjectPermissions = new ObjectPermission();

            if ($oObjectPermissions->Exists( $row['OP_UID'] )) {

                $oObjectPermissions->remove( $row['OP_UID'] );

            }

            $res = $oObjectPermissions->create( $row );

        }

        return;

    }



    /**

     * Create Stage rows from an array, removing those Objects

     * with the same UID, and recreaiting the records from the array data.

     *

     * @param $Stage array.

     * @return void

     */

    public function createStageRows ($Stage)

    {

        foreach ($Stage as $key => $row) {

            $oStage = new Stage();

            if ($oStage->Exists( $row['STG_UID'] )) {

                $oStage->remove( $row['STG_UID'] );

            }

            $res = $oStage->create( $row );

        }

        return;

    }



    /**

     * Remove All Fields Conditions from an array of Field Conditions and Dynaforms,

     * from the arrays data.

     *

     * @param $aDynaform array

     * @return void

     */

    public function removeAllFieldCondition ($aDynaform)

    {

        foreach ($aDynaform as $key => $row) {

            $oCriteria = new Criteria();

            $oCriteria->add( FieldConditionPeer::FCD_DYN_UID, $row['DYN_UID'] );

            FieldConditionPeer::doDelete( $oCriteria );

        }

    }



    /**

     * Create Field Conditions from an array of Field Conditions and Dynaforms,

     * removing those Objects with the same UID, and recreaiting the records

     * from the arrays data.

     *

     * @param $aFieldCondition array.

     * @param $aDynaform array.

     * @return void

     */

    public function createFieldCondition ($aFieldCondition, $aDynaform)

    {

        if (is_array( $aFieldCondition )) {

            foreach ($aFieldCondition as $key => $row) {

                $oFieldCondition = new FieldCondition();

                if ($oFieldCondition->fieldConditionExists( $row['FCD_UID'], $aDynaform )) {

                    $oFieldCondition->remove( $row['FCD_UID'] );

                }

                $res = $oFieldCondition->create( $row );

            }

        }

        return;

    }



    /**

     * Create Event rows from an array, removing those Objects

     * with the same UID, and recreaiting the records from the array data.

     *

     * @param $Event array.

     * @return void

     */

    public function createEventRows ($Event)

    {

        foreach ($Event as $key => $row) {

            $oEvent = new Event();

            if ($oEvent->Exists( $row['EVN_UID'] )) {

                $oEvent->remove( $row['EVN_UID'] );

            }

            $res = $oEvent->create( $row );

        }

        return;

    }



    /**

     * Create Case Scheduler Rows from an array, removing those Objects

     * with the same UID, and recreaiting the records from the array data.

     *

     * @param $CaseScheduler array.

     * @return void

     */

    public function createCaseSchedulerRows ($CaseScheduler)

    {

        foreach ($CaseScheduler as $key => $row) {

            $oCaseScheduler = new CaseScheduler();

            if ($oCaseScheduler->Exists( $row['SCH_UID'] )) {

                $oCaseScheduler->remove( $row['SCH_UID'] );

            }

            $res = $oCaseScheduler->create( $row );

        }

        return;

    }



    /**

     * Create ProcessCategory record

     *

     * @param $ProcessCategory array.

     * @return void

     */

    public function createProcessCategoryRow ($row)

    {

        if ($row && is_array( $row ) && isset( $row['CATEGORY_UID'] )) {

            $record = ProcessCategoryPeer::retrieveByPK( $row['CATEGORY_UID'] );

            // create only if the category doesn't exists

            if (! $record) {

                $processCategory = new ProcessCategory();

                $processCategory->fromArray( $row, BasePeer::TYPE_FIELDNAME );

                $processCategory->save();

            }

        }

    }



    /**

     * Create "Process User" records

     *

     * @param array $arrayData Data to create

     *

     * return void

     */

    public function createProcessUser(array $arrayData)

    {

        try {

            $processUser = new ProcessUser();



            foreach ($arrayData as $value) {

                $record = $value;



                if ($processUser->Exists($record["PU_UID"])) {

                    $result = $processUser->remove($record["PU_UID"]);

                }



                $result = $processUser->create($record);

            }

        } catch (Exception $e) {

            throw $e;

        }

    }



    /**

     * Create "Process Variables" records

     *

     * @param array $arrayData Data to create

     *

     * return void

     */

    public function createProcessVariables(array $arrayData)

    {

        try {

            foreach ($arrayData as $value) {

                $processVariables = new ProcessVariables();

                $record = $value;



                if ($processVariables->Exists($record["VAR_UID"])) {

                    $result = $processVariables->remove($record["VAR_UID"]);

                }

                $result = $processVariables->create($record);

            }

        } catch (Exception $e) {

            throw $e;

        }

    }





    /**

     * Gets Input Documents Rows from aProcess.

     *

     * @param $sProUid string.

     * @return void

     */

    public function getInputRows ($sProUid)

    {

        try {

            $aInput = array ();

            $oCriteria = new Criteria( 'workflow' );

            $oCriteria->add( InputDocumentPeer::PRO_UID, $sProUid );

            $oDataset = InputDocumentPeer::doSelectRS( $oCriteria );

            $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );

            $oDataset->next();

            while ($aRow = $oDataset->getRow()) {

                $oInput = new InputDocument();

                $aInput[] = $oInput->Load( $aRow['INP_DOC_UID'] );

                $oDataset->next();

            }

            return $aInput;

        } catch (Exception $oError) {

            throw ($oError);

        }

    }



    /**

     * Create Input Documents Rows from an array, removing those Objects

     * with the same UID, and recreaiting the records from the array data.

     *

     * @param $aInput array.

     * @return void

     */

    public function createInputRows ($aInput)

    {

        foreach ($aInput as $key => $row) {

            $oInput = new InputDocument();

            //unset ($row['TAS_UID']);

            if ($oInput->InputExists( $row['INP_DOC_UID'] )) {

                $oInput->remove( $row['INP_DOC_UID'] );

            }

            $res = $oInput->create( $row );

        }

        return;

    }



    /**

     * change and Renew all Input GUID, because the process needs to have a new set of Inputs

     *

     * @param string $oData

     * @return boolean

     */

    public function renewAllInputGuid (&$oData)

    {

        $map = array ();

        foreach ($oData->inputs as $key => $val) {

            $newGuid = $this->getUnusedInputGUID();

            $map[$val['INP_DOC_UID']] = $newGuid;

            $oData->inputFiles[$oData->inputs[$key]['INP_DOC_UID']] = $newGuid;

            $oData->inputs[$key]['INP_DOC_UID'] = $newGuid;

        }

        if (!isset($oData->inputFiles)) {

        	$oData->inputFiles = array();

        }

        foreach ($oData->steps as $key => $val) {

            if (isset( $val['STEP_TYPE_OBJ'] )) {

                if ($val['STEP_TYPE_OBJ'] == 'INPUT_DOCUMENT') {

                    $newGuid = $map[$val['STEP_UID_OBJ']];

                    $oData->steps[$key]['STEP_UID_OBJ'] = $newGuid;

                }

            }

        }

        if (isset( $oData->caseTrackerObject ) && is_array( $oData->caseTrackerObject )) {

            foreach ($oData->caseTrackerObject as $key => $val) {

                if ($val['CTO_TYPE_OBJ'] == 'INPUT_DOCUMENT') {

                    $newGuid = $map[$val['CTO_UID_OBJ']];

                    $oData->steps[$key]['CTO_UID_OBJ'] = $newGuid;

                }

            }

        }

        if (isset( $oData->objectPermissions ) && is_array( $oData->objectPermissions )) {

            foreach ($oData->objectPermissions as $key => $val) {

                if ($val['OP_OBJ_TYPE'] == 'INPUT_DOCUMENT') {

                    if (isset( $map[$val['OP_OBJ_UID']] )) {

                        $newGuid = $map[$val['OP_OBJ_UID']];

                        $oData->objectPermissions[$key]['OP_OBJ_UID'] = $newGuid;

                    }

                }

            }

        }

        if (isset( $oData->stepSupervisor ) && is_array( $oData->stepSupervisor )) {

            foreach ($oData->stepSupervisor as $key => $val) {

                if ($val['STEP_TYPE_OBJ'] == 'INPUT_DOCUMENT') {

                    $newGuid = $map[$val['STEP_UID_OBJ']];

                    $oData->stepSupervisor[$key]['STEP_UID_OBJ'] = $newGuid;

                }

            }

        }

    }



    /**

     * Gets the Output Documents Rows from a Process.

     *

     * @param $sProUid string.

     * @return $aOutput array

     */

    public function getOutputRows ($sProUid)

    {

        try {

            $aOutput = array ();

            $oCriteria = new Criteria( 'workflow' );

            $oCriteria->add( OutputDocumentPeer::PRO_UID, $sProUid );

            $oDataset = OutputDocumentPeer::doSelectRS( $oCriteria );

            $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );

            $oDataset->next();

            while ($aRow = $oDataset->getRow()) {

                $oOutput = new OutputDocument();

                $aOutput[] = $oOutput->Load( $aRow['OUT_DOC_UID'] );

                $oDataset->next();

            }

            return $aOutput;

        } catch (Exception $oError) {

            throw ($oError);

        }

    }



    /**

     * Create Input Documents Rows from an array, removing those Objects

     * with the same UID, and recreaiting the records from the array data.

     *

     * @param $aOutput array.

     * @return void

     */

    public function createOutputRows ($aOutput)

    {

        foreach ($aOutput as $key => $row) {

            $oOutput = new OutputDocument();

            //unset ($row['TAS_UID']);

            if ($oOutput->OutputExists( $row['OUT_DOC_UID'] )) {

                $oOutput->remove( $row['OUT_DOC_UID'] );

            }

            $res = $oOutput->create( $row );

        }

        return;

    }



    /**

     * change and Renew all Output GUID, because the process needs to have a new set of Outputs

     *

     * @param string $oData

     * @return boolean

     */

    public function renewAllOutputGuid (&$oData)

    {

        $map = array ();

        foreach ($oData->outputs as $key => $val) {

            $newGuid = $this->getUnusedOutputGUID();

            $map[$val['OUT_DOC_UID']] = $newGuid;

            $oData->outputs[$key]['OUT_DOC_UID'] = $newGuid;

        }



        $oData->uid["OUTPUT_DOCUMENT"] = $map;



        foreach ($oData->steps as $key => $val) {

            if (isset( $val['STEP_TYPE_OBJ'] )) {

                if ($val['STEP_TYPE_OBJ'] == 'OUTPUT_DOCUMENT') {

                    $newGuid = $map[$val['STEP_UID_OBJ']];

                    $oData->steps[$key]['STEP_UID_OBJ'] = $newGuid;

                }

            }

        }

        foreach ($oData->caseTrackerObject as $key => $val) {

            if ($val['CTO_TYPE_OBJ'] == 'OUTPUT_DOCUMENT') {

                $newGuid = $map[$val['CTO_UID_OBJ']];

                $oData->steps[$key]['CTO_UID_OBJ'] = $newGuid;

            }

        }

        foreach ($oData->objectPermissions as $key => $val) {

            if ($val['OP_OBJ_TYPE'] == 'OUTPUT_DOCUMENT') {

                $newGuid = $map[$val['OP_OBJ_UID']];

                $oData->objectPermissions[$key]['OP_OBJ_UID'] = $newGuid;

            }

        }

        foreach ($oData->stepSupervisor as $key => $val) {

            if ($val['STEP_TYPE_OBJ'] == 'OUTPUT_DOCUMENT') {

                $newGuid = $map[$val['STEP_UID_OBJ']];

                $oData->stepSupervisor[$key]['STEP_UID_OBJ'] = $newGuid;

            }

        }

    }



    /**

     * change and Renew all Trigger GUID, because the process needs to have a new set of Triggers

     *

     * @param string $oData

     * @return boolean

     */

    public function renewAllTriggerGuid (&$oData)

    {

        $map = array ();

        foreach ($oData->triggers as $key => $val) {

            $newGuid = $this->getUnusedTriggerGUID();

            $map[$val['TRI_UID']] = $newGuid;

            $oData->triggers[$key]['TRI_UID'] = $newGuid;

        }



        $oData->uid["TRIGGER"] = $map;



        foreach ($oData->steptriggers as $key => $val) {

            if (isset( $map[$val['TRI_UID']] )) {

                $newGuid = $map[$val['TRI_UID']];

                $oData->steptriggers[$key]['TRI_UID'] = $newGuid;

            } else {

                $oData->steptriggers[$key]['TRI_UID'] = $this->getUnusedTriggerGUID();

            }

        }



        foreach (array("PRO_TRI_DELETED", "PRO_TRI_CANCELED", "PRO_TRI_PAUSED", "PRO_TRI_REASSIGNED") as $value) {

            $key = $value;



            if (isset($map[$oData->process[$key]])) {

                $oData->process[$key] = $map[$oData->process[$key]];

            }

        }



        //Script-Task

        if (isset($oData->scriptTask)) {

            foreach ($oData->scriptTask as $key => $value) {

                $record = $value;



                if (isset($map[$record["SCRTAS_OBJ_UID"]])) {

                    $newUid = $map[$record["SCRTAS_OBJ_UID"]];



                    $oData->scriptTask[$key]["SCRTAS_OBJ_UID"] = $newUid;

                }

            }

        }

    }



    /**

     * Renew all the GUID's for Subprocesses

     *

     * @param $oData array.

     * @return void

     */

    public function renewAllSubProcessGuid (&$oData)

    {

        $map = array ();

        foreach ($oData->subProcess as $key => $val) {

            $newGuid = $this->getUnusedSubProcessGUID();

            $map[$val['SP_UID']] = $newGuid;

            $oData->subProcess[$key]['SP_UID'] = $newGuid;

        }



        $oData->uid["SUB_PROCESS"] = $map;

    }



    /**

     * Renew all the GUID's for Case Tracker Objects

     *

     * @param $oData array.

     * @return void

     */

    public function renewAllCaseTrackerObjectGuid (&$oData)

    {

        $map = array ();

        foreach ($oData->caseTrackerObject as $key => $val) {

            $newGuid = $this->getUnusedCaseTrackerObjectGUID();

            $map[$val['CTO_UID']] = $newGuid;

            $oData->caseTrackerObject[$key]['CTO_UID'] = $newGuid;

        }



        $oData->uid["CASE_TRACKER_OBJECT"] = $map;

    }



    /**

     * Renew all the GUID's for DB Sources

     *

     * @param $oData array.

     * @return void

     */

    public function renewAllDBSourceGuid (&$oData)

    {

        $map = array ();

        $aSqlConnections = array ();

        foreach ($oData->dbconnections as $key => $val) {

            $newGuid = $val['DBS_UID']; ///--  $this->getUnusedDBSourceGUID();

            $map[$val['DBS_UID']] = $newGuid;

            $oData->dbconnections[$key]['DBS_UID'] = $newGuid;

        }



        $oData->uid["DB_SOURCE"] = $map;



        $oData->sqlConnections = $map;

    }



    /**

     * Renew all the GUID's for Object Permissions

     *

     * @param $oData array.

     * @return void

     */

    public function renewAllObjectPermissionGuid (&$oData)

    {

        $map = array ();

        foreach ($oData->objectPermissions as $key => $val) {

            $newGuid = $this->getUnusedObjectPermissionGUID();

            $map[$val['OP_UID']] = $newGuid;

            $oData->objectPermissions[$key]['OP_UID'] = $newGuid;

        }



        $oData->uid["OBJECT_PERMISSION"] = $map;

    }



    /**

     * Renew all the GUID's for Routes Objects

     *

     * @param $oData array.

     * @return void

     */

    public function renewAllRouteGuid (&$oData)

    {

        $map = array ();

        if (isset( $oData->routes ) && is_array( $oData->routes )) {

            foreach ($oData->routes as $key => $val) {

                $newGuid = $this->getUnusedRouteGUID();

                $map[$val['ROU_UID']] = $newGuid;

                $oData->routes[$key]['ROU_UID'] = $newGuid;

            }

        }



        $oData->uid["ROUTE"] = $map;

    }



    /**

     * Renew all the GUID's for Stage Objects

     *

     * @param $oData array.

     * @return void

     */

    public function renewAllStageGuid (&$oData)

    {

        $map = array ();

        foreach ($oData->stage as $key => $val) {

            $newGuid = $this->getUnusedStageGUID();

            $map[$val['STG_UID']] = $newGuid;

            $oData->stage[$key]['STG_UID'] = $newGuid;

        }



        $oData->uid["STAGE"] = $map;



        foreach ($oData->tasks as $key => $val) {

            if (isset( $map[$val['STG_UID']] )) {

                $newGuid = $map[$val['STG_UID']];

                $oData->tasks[$key]['STG_UID'] = $newGuid;

            }

        }

    }



    /**

     * Renew all the GUID's for Swimlanes Elements Objects

     *

     * @param $oData array.

     * @return void

     */

    public function renewAllSwimlanesElementsGuid (&$oData)

    {

        $map = array ();

        foreach ($oData->lanes as $key => $val) {

            $newGuid = $this->getUnusedSLGUID();

            $map[$val['SWI_UID']] = $newGuid;

            $oData->lanes[$key]['SWI_UID'] = $newGuid;

        }



        $oData->uid["SWIMLANE_ELEMENT"] = $map;

    }



    /**

     * Renew the GUID's for all the Report Tables Objects

     *

     * @param $oData array.

     * @return void

     */

    public function renewAllReportTableGuid (&$oData)

    {

        $map = array ();

        foreach ($oData->reportTables as $key => $val) {

            $newGuid = $this->getUnusedRTGUID();

            $map[$val['REP_TAB_UID']] = $newGuid;

            $oData->reportTables[$key]['REP_TAB_UID'] = $newGuid;

        }



        $oData->uid["REPORT_TABLE"] = $map;



        foreach ($oData->reportTablesVars as $key => $val) {

            if (isset( $map[$val['REP_TAB_UID']] )) {

                /*TODO: Why this can be not defined?? The scenario was when

                 * imported an existing process but as a new one

                 */

                $newGuid = $map[$val['REP_TAB_UID']];

                $oData->reportTablesVars[$key]['REP_TAB_UID'] = $newGuid;

            }

        }

    }



    /**

     * Renew all the GUID's for All The Report Vars Objects

     *

     * @param $oData array.

     * @return void

     */

    public function renewAllReportVarGuid (&$oData)

    {

        $map = array ();

        foreach ($oData->reportTablesVars as $key => $val) {

            $newGuid = $this->getUnusedRTVGUID();

            $map[$val['REP_VAR_UID']] = $newGuid;

            $oData->reportTablesVars[$key]['REP_VAR_UID'] = $newGuid;

        }



        $oData->uid["REPORT_VAR"] = $map;

    }



    /**

     * Renew the GUID's for all the Field Conditions Objects

     *

     * @param $oData array.

     * @return void

     */

    public function renewAllFieldCondition (&$oData)

    {

        $map = array ();

        foreach ($oData->fieldCondition as $key => $val) {

            $newGuid = $this->getUnusedFieldConditionGUID();

            $map[$val['FCD_UID']] = $newGuid;

            $oData->fieldCondition[$key]['FCD_UID'] = $newGuid;

        }



        $oData->uid["FIELD_CONDITION"] = $map;

    }



    /**

     * Renew the GUID's for all the Events Objects

     *

     * @param $oData array.

     * @return void

     */

    public function renewAllEvent (&$oData)

    {

        $map = array ();

        foreach ($oData->event as $key => $val) {

            $newGuid = $this->getUnusedEventGUID();

            $map[$val['EVN_UID']] = $newGuid;

            $oData->event[$key]['EVN_UID'] = $newGuid;

        }



        $oData->uid["EVENT"] = $map;

    }



    /**

     * Renew the GUID's for all Case Scheduler Objects

     *

     * @param $oData array.

     * @return void

     */

    public function renewAllCaseScheduler (&$oData)

    {

        $map = array ();

        foreach ($oData->caseScheduler as $key => $val) {

            $newGuid = $this->getUnusedCaseSchedulerGUID();

            $map[$val['SCH_UID']] = $newGuid;

            $oData->caseScheduler[$key]['SCH_UID'] = $newGuid;

        }



        $oData->uid["CASE_SCHEDULER"] = $map;

    }



    /**

     * Renew all the unique id for "Process User"

     *

     * @param $data Object with the data

     *

     * return void

     */

    public function renewAllProcessUserUid(&$data)

    {

        try {

            if (isset($data->processUser)) {

                $map = array();



                foreach ($data->processUser as $key => $value) {

                    $record = $value;



                    $newUid = $this->getUnusedProcessUserUid();

                    $map[$record["PU_UID"]] = $newUid;

                    $data->processUser[$key]["PU_UID"] = $newUid;

                }



                $data->uid["PROCESS_USER"] = $map;

            }

        } catch (Exception $e) {

            throw $e;

        }

    }



    /**

     * Renew all the unique id for "Process User"

     *

     * @param $data Object with the data

     *

     * return void

     */

    public function renewAllProcessVariableUid(&$data)

    {

        try {

            if (isset($data->processVariables)) {

                $map = array();

                foreach ($data->processVariables as $key => $val) {

                    if (isset($val['VAR_UID'])) {

                        $newGuid = $this->getUnusedProcessVariableGUID();

                        $map[$val['VAR_UID']] = $newGuid;

                        $data->processVariables[$key]['VAR_UID'] = $newGuid;

                    }

                }

            }



        } catch (Exception $e) {

            throw $e;

        }

    }



    /**

     * Renew all the unique id for Message-Type

     *

     * @param object $data Object with the data

     *

     * return void

     */

    public function renewAllMessageTypeUid(&$data)

    {

        try {

            $map = array();



            if (isset($data->messageType)) {

                foreach ($data->messageType as $key => $value) {

                    $record = $value;



                    if (isset($record["MSGT_UID"])) {

                        $newUid = $this->getUnusedMessageTypeUid();



                        $map[$record["MSGT_UID"]] = $newUid;

                        $data->messageType[$key]["MSGT_UID"] = $newUid;

                    }

                }

            }



            $data->uid["MESSAGE_TYPE"] = $map;



            if (isset($data->messageTypeVariable)) {

                foreach ($data->messageTypeVariable as $key => $value) {

                    $record = $value;



                    if (isset($map[$record["MSGT_UID"]])) {

                        $newUid = $map[$record["MSGT_UID"]];



                        $data->messageTypeVariable[$key]["MSGT_UID"] = $newUid;

                    }

                }

            }



            //Message-Envent-Definition

            if (isset($data->messageEventDefinition)) {

                foreach ($data->messageEventDefinition as $key => $value) {

                    $record = $value;



                    if (isset($map[$record["MSGT_UID"]])) {

                        $newUid = $map[$record["MSGT_UID"]];



                        $data->messageEventDefinition[$key]["MSGT_UID"] = $newUid;

                    }

                }

            }

        } catch (Exception $e) {

            throw $e;

        }

    }



    /**

     * Renew all the unique id for Message-Type-Variable

     *

     * @param object $data Object with the data

     *

     * return void

     */

    public function renewAllMessageTypeVariableUid(&$data)

    {

        try {

            $map = array();



            if (isset($data->messageTypeVariable)) {

                foreach ($data->messageTypeVariable as $key => $value) {

                    $record = $value;



                    if (isset($record["MSGTV_UID"])) {

                        $newUid = $this->getUnusedMessageTypeVariableUid();



                        $map[$record["MSGTV_UID"]] = $newUid;

                        $data->messageTypeVariable[$key]["MSGTV_UID"] = $newUid;

                    }

                }

            }



            $data->uid["MESSAGE_TYPE_VARIABLE"] = $map;

        } catch (Exception $e) {

            throw $e;

        }

    }



    /**

     * Renew the GUID's for all the Uids for all the elements

     *

     * @param $oData array.

     * @return void

     */

    public function renewAll (&$oData)

    {

        $oData->uid = array();



        $this->renewAllTaskGuid( $oData );

        $this->renewAllDynaformGuid( $oData );

        $this->renewAllInputGuid( $oData );

        $this->renewAllOutputGuid( $oData );

        $this->renewAllStepGuid( $oData );

        $this->renewAllTriggerGuid( $oData );

        $this->renewAllSubProcessGuid( $oData );

        $this->renewAllCaseTrackerObjectGuid( $oData );

        $this->renewAllDBSourceGuid( $oData );

        $this->renewAllObjectPermissionGuid( $oData );

        $this->renewAllRouteGuid( $oData );

        $this->renewAllStageGuid( $oData );

        $this->renewAllSwimlanesElementsGuid( $oData );

        $this->renewAllReportTableGuid( $oData );

        $this->renewAllReportVarGuid( $oData );

        $this->renewAllFieldCondition( $oData );

        $this->renewAllEvent( $oData );

        $this->renewAllCaseScheduler( $oData );

        $this->renewAllProcessUserUid($oData);

        $this->renewAllProcessVariableUid($oData);

        $this->renewAllMessageTypeUid($oData);

        $this->renewAllMessageTypeVariableUid($oData);

    }



    /**

     * Get Step Rows from a Process

     *

     * @param $sProUid array.

     * @return array $aStep.

     */

    public function getStepRows ($sProUid)

    {

        try {

            $aStep = array ();

            $oCriteria = new Criteria( 'workflow' );

            $oCriteria->add( StepPeer::PRO_UID, $sProUid );

            $oDataset = StepPeer::doSelectRS( $oCriteria );

            $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );

            $oDataset->next();

            while ($aRow = $oDataset->getRow()) {

                $oStep = new Step();

                $aStep[] = $oStep->Load( $aRow['STEP_UID'] );

                $oDataset->next();

            }

            return $aStep;

        } catch (Exception $oError) {

            throw ($oError);

        }

    }



    /**

     * Create Step Rows from a Process

     *

     * @param $aStep array.

     * @return void.

     */

    public function createStepRows ($aStep)

    {

        foreach ($aStep as $key => $row) {

            $oStep = new Step();

            if (isset( $row['STEP_UID'] )) {

                if ($oStep->StepExists( $row['STEP_UID'] )) {

                    $oStep->remove( $row['STEP_UID'] );

                }

                $res = $oStep->create( $row );

            }

        }

        return;

    }



    /**

     * Create Step Supervisor Rows for a Process from an array of data

     *

     * @param $aStepSupervisor array.

     * @return void.

     */

    public function createStepSupervisorRows ($aStepSupervisor)

    {

        foreach ($aStepSupervisor as $key => $row) {

            $oStepSupervisor = new StepSupervisor();

            if ($oStepSupervisor->Exists( $row['STEP_UID'] )) {

                $oStepSupervisor->remove( $row['STEP_UID'] );

            }

            $oStepSupervisor->create( $row );

        }

    } #@!Neyek





    /**

     * change and Renew all Step GUID, because the process needs to have a new set of Steps

     *

     * @param string $oData

     * @return boolean

     */

    public function renewAllStepGuid (&$oData)

    {

        $map = array ();

        foreach ($oData->steps as $key => $val) {

            if (isset( $val['STEP_UID'] )) {

                $newGuid = $this->getUnusedStepGUID();

                $map[$val['STEP_UID']] = $newGuid;

                $oData->steps[$key]['STEP_UID'] = $newGuid;

            }

        }



        $oData->uid["STEP"] = $map;



        foreach ($oData->steptriggers as $key => $val) {

            if ($val['STEP_UID'] > 0) {

                if (isset( $map[$val['STEP_UID']] )) {

                    $newGuid = $map[$val['STEP_UID']];

                    $oData->steptriggers[$key]['STEP_UID'] = $newGuid;

                } else {

                    $oData->steptriggers[$key]['STEP_UID'] = $this->getUnusedStepGUID();

                }

            }

        }

        foreach ($oData->stepSupervisor as $key => $val) {

            if ($val['STEP_UID'] > 0) {

                if (isset( $map[$val['STEP_UID']] )) {

                    $newGuid = $map[$val['STEP_UID']];

                    $oData->stepSupervisor[$key]['STEP_UID'] = $newGuid;

                } else {

                    $oData->stepSupervisor[$key]['STEP_UID'] = $this->getUnusedStepGUID();

                }

            }

        }

    }



    /**

     * Get Dynaform Rows from a Process

     *

     * @param string $sProUid

     * @return $aDynaform array

     */

    public function getDynaformRows ($sProUid)

    {

        try {

            $aDynaform = array ();

            $oCriteria = new Criteria( 'workflow' );

            $oCriteria->add( DynaformPeer::PRO_UID, $sProUid );

            $oDataset = DynaformPeer::doSelectRS( $oCriteria );

            $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );

            $oDataset->next();

            while ($aRow = $oDataset->getRow()) {

                $oDynaform = new Dynaform();

                $aDynaform[] = $oDynaform->Load( $aRow['DYN_UID'] );

                $oDataset->next();

            }

            return $aDynaform;

        } catch (Exception $oError) {

            throw ($oError);

        }

    }



    /**

     * Get Object Permission Rows from a Process

     *

     * @param string $sProUid

     * @return $aDynaform array

     */

    public function getObjectPermissionRows ($sProUid, &$oData)

    {

        // by erik

        try {

            $oPermissions = array ();

            $oCriteria = new Criteria( 'workflow' );

            $oCriteria->add( ObjectPermissionPeer::PRO_UID, $sProUid );

            $oCriteria->add( ObjectPermissionPeer::OP_USER_RELATION, 2 );

            $oDataset = ObjectPermissionPeer::doSelectRS( $oCriteria );

            $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );

            $oDataset->next();

            while ($aRow = $oDataset->getRow()) {

                $o = new ObjectPermission();

                $oPermissions[] = $o->Load( $aRow['OP_UID'] );



                $oGroupwf = new Groupwf();

                $oData->groupwfs[] = $oGroupwf->Load( $aRow['USR_UID'] );

                $oDataset->next();

            }



            return $oPermissions;

        } catch (Exception $oError) {

            throw ($oError);

        }

    }

    #@!neyek



    /**

     * Get Object Permission Rows from a Process

     *

     * @param string $sProUid

     * @return $aDynaform array

     */

    //Deprecated

    public function getGroupwfSupervisor ($sProUid, &$oData)

    {

        try {

            $oCriteria = new Criteria( 'workflow' );

            $oCriteria->add(ProcessUserPeer::PRO_UID,  $sProUid );

            $oCriteria->add(ProcessUserPeer::PU_TYPE,  'GROUP_SUPERVISOR' );

            $oDataset = ProcessUserPeer::doSelectRS( $oCriteria );

            $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );

            $oDataset->next();

            while ($aRow = $oDataset->getRow()) {

                $oGroupwf = new Groupwf();

                $oData->groupwfs[] = $oGroupwf->Load( $aRow['USR_UID'] );

                $oDataset->next();

            }

            return true;

        } catch (Exception $oError) {

            throw ($oError);

        }

    }



    /**

     * Create Dynaform Rows for a Process form an array

     *

     * @param array $aDynaform

     * @return void

     */

    public function createDynaformRows ($aDynaform)

    {

        foreach ($aDynaform as $key => $row) {

            $oDynaform = new Dynaform();

            //unset ($row['TAS_UID']);

            if ($oDynaform->exists( $row['DYN_UID'] )) {

                $oDynaform->remove( $row['DYN_UID'] );

            }

            $res = $oDynaform->create( $row );

        }

        return;

    }

    #@!neyek





    /**

     * Create Step Trigger Rows for a Process form an array

     *

     * @param array $aStepTrigger

     * @return void

     */

    public function createStepTriggerRows ($aStepTrigger)

    {

        foreach ($aStepTrigger as $key => $row) {

            $oStepTrigger = new StepTrigger();

            //unset ($row['TAS_UID']);

            if ($oStepTrigger->stepTriggerExists( $row['STEP_UID'], $row['TAS_UID'], $row['TRI_UID'], $row['ST_TYPE'] )) {

                $oStepTrigger->remove( $row['STEP_UID'], $row['TAS_UID'], $row['TRI_UID'], $row['ST_TYPE'] );

            }

            $res = $oStepTrigger->createRow( $row );

        }

        return;

    }



    /**

     * Get Step Trigger Rows for a Process form an array

     *

     * @param array $aTask

     * @return array $aStepTrigger

     */

    public function getStepTriggerRows ($aTask)

    {

        try {

            $aInTasks = array ();

            foreach ($aTask as $key => $val) {

                $aInTasks[] = $val['TAS_UID'];

            }



            $aTrigger = array ();

            $oCriteria = new Criteria( 'workflow' );

            $oCriteria->add( StepTriggerPeer::TAS_UID, $aInTasks, Criteria::IN );

            $oDataset = StepTriggerPeer::doSelectRS( $oCriteria );

            $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );

            $oDataset->next();

            $aStepTrigger = array ();

            while ($aRow = $oDataset->getRow()) {

                $aStepTrigger[] = $aRow;

                $oDataset->next();

            }

            return $aStepTrigger;

        } catch (Exception $oError) {

            throw ($oError);

        }

    }



    /**

     * Get Step Trigger Rows for a Process form an array

     *

     * @param array $aTask

     * @return array $aStepTrigger

     */

    public function getTriggerRows ($sProUid)

    {

        try {

            $aTrigger = array ();

            $oCriteria = new Criteria( 'workflow' );

            $oCriteria->add( TriggersPeer::PRO_UID, $sProUid );

            $oDataset = TriggersPeer::doSelectRS( $oCriteria );

            $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );

            $oDataset->next();

            while ($aRow = $oDataset->getRow()) {

                $oTrigger = new Triggers();

                $aTrigger[] = $oTrigger->Load( $aRow['TRI_UID'] );

                $oDataset->next();

            }

            return $aTrigger;

        } catch (Exception $oError) {

            throw ($oError);

        }

    }



    /**

     * Create Step Trigger Rows for a Process form an array

     *

     * @param array $aTrigger

     * @return void

     */

    public function createTriggerRows ($aTrigger)

    {

        foreach ($aTrigger as $key => $row) {

            $oTrigger = new Triggers();

            //unset ($row['TAS_UID']);

            if ($oTrigger->TriggerExists( $row['TRI_UID'] )) {

                $oTrigger->remove( $row['TRI_UID'] );

            }

            $res = $oTrigger->create( $row );

        }

        return;

    }



    /**

     * Get Groupwf Rows for a Process form an array

     *

     * @param array $aGroups

     * @return array $aGroupwf

     */

    public function getGroupwfRows ($aGroups)

    {

        try {

            $aInGroups = array ();

            foreach ($aGroups as $key => $val) {

                $aInGroups[] = $val['USR_UID'];

            }



            $aGroupwf = array ();

            $oCriteria = new Criteria( 'workflow' );

            $oCriteria->add( GroupwfPeer::GRP_UID, $aInGroups, Criteria::IN );

            $oDataset = GroupwfPeer::doSelectRS( $oCriteria );

            $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );

            $oDataset->next();

            while ($aRow = $oDataset->getRow()) {

                $oGroupwf = new Groupwf();

                $aGroupwf[] = $oGroupwf->Load( $aRow['GRP_UID'] );

                $oDataset->next();

            }



            return $aGroupwf;

        } catch (Exception $oError) {

            throw ($oError);

        }

    }



    /**

     * Get DB Connections Rows for a Process

     *

     * @param array $sProUid

     * @return array $aConnections

     */

    public function getDBConnectionsRows ($sProUid)

    {

        try {

            $aConnections = array ();

            $oCriteria = new Criteria( 'workflow' );

            $oCriteria->add( DbSourcePeer::PRO_UID, $sProUid );

            $oDataset = DbSourcePeer::doSelectRS( $oCriteria );

            $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );

            $oDataset->next();

            while ($aRow = $oDataset->getRow()) {

                $oConnection = new DbSource();

                $aConnections[] = $oConnection->Load( $aRow['DBS_UID'], $aRow['PRO_UID'] );

                $oDataset->next();

            }

            return $aConnections;

        } catch (Exception $oError) {

            throw $oError;

        }

    }



    /**

     * Get Step Supervisor Rows for a Process form an array

     *

     * @param array $sProUid

     * @return array $aStepSup

     */

    public function getStepSupervisorRows ($sProUid)

    {

        try {

            $aConnections = array ();

            $oCriteria = new Criteria( 'workflow' );

            $oCriteria->add( StepSupervisorPeer::PRO_UID, $sProUid );

            $oDataset = StepSupervisorPeer::doSelectRS( $oCriteria );

            $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );

            $oDataset->next();

            $aStepSup = array ();

            while ($aRow = $oDataset->getRow()) {

                $aStepSup[] = $aRow;

                $oDataset->next();

            }

            return $aStepSup;

        } catch (Exception $oError) {

            throw $oError;

        }

    }



    /**

     * Get Report Tables Rows for a Process form an array

     *

     * @param array $aTask

     * @return array $aReps

     */

    public function getReportTablesRows ($sProUid)

    {

        try {

            $aReps = array ();

            $oCriteria = new Criteria( 'workflow' );

            $oCriteria->add( ReportTablePeer::PRO_UID, $sProUid );

            $oDataset = ReportTablePeer::doSelectRS( $oCriteria );

            $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );

            $oDataset->next();

            while ($aRow = $oDataset->getRow()) {

                $oRep = new ReportTable();

                $aReps[] = $oRep->load( $aRow['REP_TAB_UID'] );

                $oDataset->next();

            }

            return $aReps;

        } catch (Exception $oError) {

            throw $oError;

        }

    }



    /**

     * Get Report Tables Vars Rows for a Process

     *

     * @param string $sProUid

     * @return array $aRepVars

     */

    public function getReportTablesVarsRows ($sProUid)

    {

        try {

            $aRepVars = array ();

            $oCriteria = new Criteria( 'workflow' );

            $oCriteria->add( ReportVarPeer::PRO_UID, $sProUid );

            $oDataset = ReportVarPeer::doSelectRS( $oCriteria );

            $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );

            $oDataset->next();

            while ($aRow = $oDataset->getRow()) {

                $oRepVar = new ReportVar();

                $aRepVars[] = $oRepVar->load( $aRow['REP_VAR_UID'] );

                $oDataset->next();

            }

            return $aRepVars;

        } catch (Exception $oError) {

            throw $oError;

        }

    }



    /**

     * Get Task User Rows for a Process

     *

     * @param array $aTask

     * @return array $aStepTrigger

     */

    public function getTaskUserRows ($aTask)

    {

        try {

            $aInTasks = array ();

            foreach ($aTask as $key => $val) {

                $aInTasks[] = $val['TAS_UID'];

            }



            $aTaskUser = array ();

            $oCriteria = new Criteria( 'workflow' );

            $oCriteria->add( TaskUserPeer::TAS_UID, $aInTasks, Criteria::IN );

            $oCriteria->add( TaskUserPeer::TU_RELATION, 2 );

            $oDataset = TaskUserPeer::doSelectRS( $oCriteria );

            $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );

            $oDataset->next();

            while ($aRow = $oDataset->getRow()) {

                $oCriteria2 = new Criteria( 'workflow' );

                $oCriteria2->clearSelectColumns();

                $oCriteria2->addSelectColumn( 'COUNT(*)' );

                $oCriteria2->add( GroupwfPeer::GRP_UID, $aRow['USR_UID'] );

                $oCriteria2->add( GroupwfPeer::GRP_STATUS, 'ACTIVE' );

                $oDataset2 = GroupwfPeer::doSelectRS( $oCriteria2 );

                //$oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);

                $oDataset2->next();

                $aRow2 = $oDataset2->getRow();

                $bActiveGroup = $aRow2[0];

                if ($bActiveGroup == 1) {

                    $aTaskUser[] = $aRow;

                }

                $oDataset->next();

            }

            return $aTaskUser;

        } catch (Exception $oError) {

            throw ($oError);

        }

    }



    /**

     * Get "Process Variables" records of a Process Variables

     *

     * @param string $processUid Unique id of Process

     *

     * return array Return an array with all "Process Variables"

     */

    public function getProcessVariables ($sProUid)

    {

        try {

            $aVars = array ();

            $oCriteria = new Criteria( 'workflow' );

            $oCriteria->add( ProcessVariablesPeer::PRJ_UID, $sProUid );

            $oDataset = ProcessVariablesPeer::doSelectRS( $oCriteria );

            $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );

            $oDataset->next();

            while ($aRow = $oDataset->getRow()) {

                $aVars[] = $aRow;

                $oDataset->next();

            }

            return $aVars;

        } catch (Exception $oError) {

            throw $oError;

        }

    }



    /**

     * Get "Process User" (Groups) records of a Process

     *

     * @param string $processUid Unique id of Process

     *

     * return array Return an array with all "Process User" (Groups)

     */

    public function getProcessUser($processUid)

    {

        try {

            $arrayProcessUser = array();



            //Get data

            $criteria = new Criteria("workflow");



            $criteria->add(ProcessUserPeer::PRO_UID, $processUid, Criteria::EQUAL);

            $criteria->add(ProcessUserPeer::PU_TYPE, "GROUP_SUPERVISOR", Criteria::EQUAL);



            $rsCriteria = ProcessUserPeer::doSelectRS($criteria);

            $rsCriteria->setFetchmode(ResultSet::FETCHMODE_ASSOC);



            while ($rsCriteria->next()) {

                $row = $rsCriteria->getRow();



                //Verify group status

                $criteria2 = new Criteria("workflow");



                $criteria2->add(GroupwfPeer::GRP_UID, $row["USR_UID"], Criteria::EQUAL);

                $criteria2->add(GroupwfPeer::GRP_STATUS, "ACTIVE", Criteria::EQUAL);



                $rsCriteria2 = GroupwfPeer::doSelectRS($criteria2);



                if ($rsCriteria2->next()) {

                    $arrayProcessUser[] = $row;

                }

            }



            //Return

            return $arrayProcessUser;

        } catch (Exception $e) {

            throw $e;

        }

    }



    /**

     * Get all WebEntry records of a Process

     *

     * @param string $processUid Unique id of Process

     *

     * return array Return an array with all WebEntry records of a Process

     */

    public function getWebEntries($processUid)

    {

        try {

            $arrayWebEntry = array();



            $webEntry = new \ProcessMaker\BusinessModel\WebEntry();



            //Get UIDs to exclude

            $arrayWebEntryUidToExclude = array();



            $criteria = new Criteria("workflow");



            $criteria->setDistinct();

            $criteria->addSelectColumn(WebEntryEventPeer::WEE_WE_UID);

            $criteria->add(WebEntryEventPeer::PRJ_UID, $processUid, Criteria::EQUAL);



            $rsCriteria = WebEntryEventPeer::doSelectRS($criteria);

            $rsCriteria->setFetchmode(ResultSet::FETCHMODE_ASSOC);



            while ($rsCriteria->next()) {

                $row = $rsCriteria->getRow();



                $arrayWebEntryUidToExclude[] = $row["WEE_WE_UID"];

            }



            //Get data

            $criteria = new Criteria("workflow");



            $criteria->addSelectColumn(WebEntryPeer::WE_UID);

            $criteria->add(WebEntryPeer::PRO_UID, $processUid, Criteria::EQUAL);

            $criteria->add(WebEntryPeer::WE_UID, $arrayWebEntryUidToExclude, Criteria::NOT_IN);



            $rsCriteria = WebEntryPeer::doSelectRS($criteria);

            $rsCriteria->setFetchmode(ResultSet::FETCHMODE_ASSOC);



            while ($rsCriteria->next()) {

                $row = $rsCriteria->getRow();



                $arrayWebEntry[] = $webEntry->getWebEntry($row["WE_UID"], true);

            }



            //Return

            return $arrayWebEntry;

        } catch (Exception $e) {

            throw $e;

        }

    }



    /**

     * Get all WebEntry-Event records of a Process

     *

     * @param string $processUid Unique id of Process

     *

     * return array Return an array with all WebEntry-Event records of a Process

     */

    public function getWebEntryEvents($processUid)

    {

        try {

            $arrayWebEntryEvent = array();



            $webEntryEvent = new \ProcessMaker\BusinessModel\WebEntryEvent();



            //Get data

            $criteria = new Criteria("workflow");



            $criteria->addSelectColumn(WebEntryEventPeer::WEE_UID);

            $criteria->add(WebEntryEventPeer::PRJ_UID, $processUid, Criteria::EQUAL);



            $rsCriteria = WebEntryEventPeer::doSelectRS($criteria);

            $rsCriteria->setFetchmode(ResultSet::FETCHMODE_ASSOC);



            while ($rsCriteria->next()) {

                $row = $rsCriteria->getRow();



                $arrayWebEntryEvent[] = $webEntryEvent->getWebEntryEvent($row["WEE_UID"], true);

            }



            //Return

            return $arrayWebEntryEvent;

        } catch (Exception $e) {

            throw $e;

        }

    }



    public function getMessageTypes($processUid)

    {

        try {

            $arrayMessageType = array();



            $messageType = new \ProcessMaker\BusinessModel\MessageType();



            //Get data

            $criteria = new Criteria("workflow");



            $criteria->addSelectColumn(MessageTypePeer::MSGT_UID);

            $criteria->add(MessageTypePeer::PRJ_UID, $processUid, Criteria::EQUAL);



            $rsCriteria = MessageTypePeer::doSelectRS($criteria);

            $rsCriteria->setFetchmode(ResultSet::FETCHMODE_ASSOC);



            while ($rsCriteria->next()) {

                $row = $rsCriteria->getRow();



                $arrayAux = $messageType->getMessageType($row["MSGT_UID"], true);



                unset($arrayAux["MSGT_VARIABLES"]);



                $arrayMessageType[] = $arrayAux;

            }



            //Return

            return $arrayMessageType;

        } catch (Exception $e) {

            throw $e;

        }

    }



    public function getMessageTypeVariables($processUid)

    {

        try {

            $arrayVariable = array();



            $variable = new \ProcessMaker\BusinessModel\MessageType\Variable();



            //Get data

            $criteria = new Criteria("workflow");



            $criteria->addSelectColumn(MessageTypeVariablePeer::MSGTV_UID);



            $criteria->addJoin(MessageTypePeer::MSGT_UID, MessageTypeVariablePeer::MSGT_UID, Criteria::LEFT_JOIN);



            $criteria->add(MessageTypePeer::PRJ_UID, $processUid, Criteria::EQUAL);



            $rsCriteria = MessageTypeVariablePeer::doSelectRS($criteria);

            $rsCriteria->setFetchmode(ResultSet::FETCHMODE_ASSOC);



            while ($rsCriteria->next()) {

                $row = $rsCriteria->getRow();



                $arrayVariable[] = $variable->getMessageTypeVariable($row["MSGTV_UID"], true);

            }



            //Return

            return $arrayVariable;

        } catch (Exception $e) {

            throw $e;

        }

    }



    public function getMessageEventDefinitions($processUid)

    {

        try {

            $arrayMessageEventDefinition = array();



            $messageEventDefinition = new \ProcessMaker\BusinessModel\MessageEventDefinition();



            //Get data

            $criteria = new Criteria("workflow");



            $criteria->addSelectColumn(MessageEventDefinitionPeer::MSGED_UID);

            $criteria->add(MessageEventDefinitionPeer::PRJ_UID, $processUid, Criteria::EQUAL);



            $rsCriteria = MessageEventDefinitionPeer::doSelectRS($criteria);

            $rsCriteria->setFetchmode(ResultSet::FETCHMODE_ASSOC);



            while ($rsCriteria->next()) {

                $row = $rsCriteria->getRow();



                $arrayData = $messageEventDefinition->getMessageEventDefinition($row["MSGED_UID"], true);



                $arrayData["MSGED_VARIABLES"] = serialize($arrayData["MSGED_VARIABLES"]);



                $arrayMessageEventDefinition[] = $arrayData;

            }



            //Return

            return $arrayMessageEventDefinition;

        } catch (Exception $e) {

            throw $e;

        }

    }



    public function getScriptTasks($processUid)

    {

        try {

            $arrayScriptTask = array();



            $scriptTask = new \ProcessMaker\BusinessModel\ScriptTask();



            //Get data

            $criteria = $scriptTask->getScriptTaskCriteria();



            $criteria->add(\ScriptTaskPeer::PRJ_UID, $processUid, \Criteria::EQUAL);



            $rsCriteria = \ScriptTaskPeer::doSelectRS($criteria);

            $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);



            while ($rsCriteria->next()) {

                $row = $rsCriteria->getRow();



                $arrayScriptTask[] = $row;

            }



            //Return

            return $arrayScriptTask;

        } catch (Exception $e) {

            throw $e;

        }

    }



    public function getTimerEvents($processUid)

    {

        try {

            $arrayTimerEvent = array();



            $timerEvent = new \ProcessMaker\BusinessModel\TimerEvent();



            //Get data

            $criteria = $timerEvent->getTimerEventCriteria();



            $criteria->add(\TimerEventPeer::PRJ_UID, $processUid, \Criteria::EQUAL);



            $rsCriteria = \TimerEventPeer::doSelectRS($criteria);

            $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);



            while ($rsCriteria->next()) {

                $row = $rsCriteria->getRow();



                $arrayTimerEvent[] = $row;

            }



            //Return

            return $arrayTimerEvent;

        } catch (Exception $e) {

            throw $e;

        }

    }



    public function getEmailEvent($processUid)

    {

        try {

            $arrayEmailEvent = array();



            $emailEvent = new \ProcessMaker\BusinessModel\EmailEvent();

            $criteria = $emailEvent->getEmailEventCriteria();



            //Get data

            $criteria->add(EmailEventPeer::PRJ_UID, $processUid, Criteria::EQUAL);

            $rsCriteria = EmailEventPeer::doSelectRS($criteria);

            $rsCriteria->setFetchmode(ResultSet::FETCHMODE_ASSOC);

            $rsCriteria->next();

            while ($aRow = $rsCriteria->getRow()) {

                $arrayEmailEvent[] = $aRow;

                $rsCriteria->next();

            }

            //Return

            return $arrayEmailEvent;

        } catch (Exception $e) {

            throw $e;

        }

    }

    

    public function getActionsByEmail($processUid)

    {

        try {

            $arrayActionsByEmail = array();

            //Get data

            $criteria = new \Criteria("workflow");

            $criteria->addSelectColumn(\AbeConfigurationPeer::ABE_UID);

            $criteria->addSelectColumn(\AbeConfigurationPeer::PRO_UID);

            $criteria->addSelectColumn(\AbeConfigurationPeer::TAS_UID);

            $criteria->addSelectColumn(\AbeConfigurationPeer::ABE_TYPE);

            $criteria->addSelectColumn(\AbeConfigurationPeer::ABE_TEMPLATE);

            $criteria->addSelectColumn(\AbeConfigurationPeer::ABE_DYN_TYPE);

            $criteria->addSelectColumn(\AbeConfigurationPeer::DYN_UID);

            $criteria->addSelectColumn(\AbeConfigurationPeer::ABE_EMAIL_FIELD);

            $criteria->addSelectColumn(\AbeConfigurationPeer::ABE_ACTION_FIELD);

            $criteria->addSelectColumn(\AbeConfigurationPeer::ABE_CASE_NOTE_IN_RESPONSE);

            $criteria->addSelectColumn(\AbeConfigurationPeer::ABE_CREATE_DATE);

            $criteria->addSelectColumn(\AbeConfigurationPeer::ABE_UPDATE_DATE);

            $criteria->addSelectColumn(\AbeConfigurationPeer::ABE_SUBJECT_FIELD);

            $criteria->add(AbeConfigurationPeer::PRO_UID, $processUid, Criteria::EQUAL);

            $rsCriteria = AbeConfigurationPeer::doSelectRS($criteria);

            $rsCriteria->setFetchmode(ResultSet::FETCHMODE_ASSOC);

            $rsCriteria->next();

            while ($aRow = $rsCriteria->getRow()) {

                $arrayActionsByEmail[] = $aRow;

                $rsCriteria->next();

            }

            //Return

            return $arrayActionsByEmail;

        } catch (Exception $e) {

            throw $e;

        }

    }



    public function getFilesManager($processUid)

    {

        try {

            $arrayFilesManager = array();

            //Get data

            $criteria = new \Criteria("workflow");

            $criteria->addSelectColumn(\ProcessFilesPeer::PRF_UID);

            $criteria->addSelectColumn(\ProcessFilesPeer::PRO_UID);

            $criteria->addSelectColumn(\ProcessFilesPeer::USR_UID);

            $criteria->addSelectColumn(\ProcessFilesPeer::PRF_UPDATE_USR_UID);

            $criteria->addSelectColumn(\ProcessFilesPeer::PRF_PATH);

            $criteria->addSelectColumn(\ProcessFilesPeer::PRF_TYPE);

            $criteria->addSelectColumn(\ProcessFilesPeer::PRF_EDITABLE);

            $criteria->addSelectColumn(\ProcessFilesPeer::PRF_CREATE_DATE);

            $criteria->addSelectColumn(\ProcessFilesPeer::PRF_UPDATE_DATE);

            $criteria->add(ProcessFilesPeer::PRO_UID, $processUid, Criteria::EQUAL);

            $rsCriteria = ProcessFilesPeer::doSelectRS($criteria);

            $rsCriteria->setFetchmode(ResultSet::FETCHMODE_ASSOC);

            $rsCriteria->next();

            while ($aRow = $rsCriteria->getRow()) {

                $arrayFilesManager[] = $aRow;

                $rsCriteria->next();

            }

            //Return

            return $arrayFilesManager;

        } catch (Exception $e) {

            throw $e;

        }

    }



    /**

     * Get Task User Rows from an array of data

     *

     * @param array $aTaskUser

     * @return array $aStepTrigger

     */

    public function createTaskUserRows ($aTaskUser)

    {

        if (is_array($aTaskUser)) {

            foreach ($aTaskUser as $key => $row) {

                $oTaskUser = new TaskUser();

                if ($oTaskUser->TaskUserExists( $row['TAS_UID'], $row['USR_UID'], $row['TU_TYPE'], $row['TU_RELATION'] )) {

                    $oTaskUser->remove( $row['TAS_UID'], $row['USR_UID'], $row['TU_TYPE'], $row['TU_RELATION'] );

                }

                $res = $oTaskUser->create( $row );

            }

        }

        return;

    }



    /**

     * Get Task User Rows from an array of data

     *

     * @param array $aTaskUser

     * @return array $aStepTrigger

     */

    public function createGroupRow ($aGroupwf)

    {

        foreach ($aGroupwf as $key => $row) {

            $oGroupwf = new Groupwf();

            if ($oGroupwf->GroupwfExists( $row['GRP_UID'] )) {

                $oGroupwf->remove( $row['GRP_UID'] );

            }

            $res = $oGroupwf->create( $row );

        }

    }



    /**

     * Create DB Connections rows from an array of data

     *

     * @param array $aConnections

     * @return void

     */

    public function createDBConnectionsRows ($aConnections)

    {

        foreach ($aConnections as $sKey => $aRow) {

            $oConnection = new DbSource();

            if ($oConnection->Exists( $aRow['DBS_UID'], $aRow['PRO_UID'] )) {

                $oConnection->remove( $aRow['DBS_UID'], $aRow['PRO_UID'] );

            }

            $oConnection->create( $aRow );



            // Update information in the table of contents

            $oContent = new Content();

            $ConCategory = 'DBS_DESCRIPTION';

            $ConParent = '';

            $ConId = $aRow['DBS_UID'];

            $ConLang = SYS_LANG;

            if ($oContent->Exists( $ConCategory, $ConParent, $ConId, $ConLang )) {

                $oContent->removeContent( $ConCategory, $ConParent, $ConId );

            }

            $oContent->addContent( $ConCategory, $ConParent, $ConId, $ConLang, $aRow['DBS_DESCRIPTION'] );

        }

    } #@!neyek





    /**

     * Create Report Tables from an array of data

     *

     * @param array $aReportTables

     * @param array $aReportTablesVars

     * @return void

     */

    public function createReportTables ($aReportTables, $aReportTablesVars)

    {

        $this->createReportTablesVars( $aReportTablesVars );

        $oReportTables = new ReportTables();

        foreach ($aReportTables as $sKey => $aRow) {

            $bExists = true;

            $sTable = $aRow['REP_TAB_NAME'];

            $iCounter = 1;

            while ($bExists) {

                $oCriteria = new Criteria( 'workflow' );

                $oCriteria->add( ReportTablePeer::REP_TAB_NAME, $sTable );

                $oDataset = ReportTablePeer::doSelectRS( $oCriteria );

                $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );

                $oDataset->next();

                $bExists = ($aRow2 = $oDataset->getRow());

                if ($bExists) {

                    $sTable = $aRow['REP_TAB_NAME'] . '_' . $iCounter;

                    $iCounter ++;

                } else {

                    $aRow['REP_TAB_NAME'] = $sTable;

                }

            }

            $aFields = $oReportTables->getTableVars( $aRow['REP_TAB_UID'], true );

            $oReportTables->createTable( $aRow['REP_TAB_NAME'], $aRow['REP_TAB_CONNECTION'], $aRow['REP_TAB_TYPE'], $aFields );

            $oReportTables->populateTable( $aRow['REP_TAB_NAME'], $aRow['REP_TAB_CONNECTION'], $aRow['REP_TAB_TYPE'], $aFields, $aRow['PRO_UID'], $aRow['REP_TAB_GRID'] );

            $aReportTables[$sKey]['REP_TAB_NAME'] = $aRow['REP_TAB_NAME'];

            $oRep = new ReportTable();

            if ($oRep->reportTableExists( $aRow['REP_TAB_UID'] )) {

                $oRep->remove( $aRow['REP_TAB_UID'] );

            }

            $oRep->create( $aRow );

        }

    }

    #@!neyek





    /**

     * Update Report Tables from an array of data

     *

     * @param array $aReportTables

     * @param array $aReportTablesVars

     * @return void

     */

    public function updateReportTables ($aReportTables, $aReportTablesVars)

    {

        $this->cleanupReportTablesReferences( $aReportTables );

        $this->createReportTables( $aReportTables, $aReportTablesVars );

    } #@!neyek





    /**

     * Create Report Tables Vars from an array of data

     *

     * @param array $aReportTablesVars

     * @return void

     */

    public function createReportTablesVars ($aReportTablesVars)

    {

        foreach ($aReportTablesVars as $sKey => $aRow) {

            $oRep = new ReportVar();

            if ($oRep->reportVarExists( $aRow['REP_VAR_UID'] )) {

                $oRep->remove( $aRow['REP_VAR_UID'] );

            }

            $oRep->create( $aRow );

        }

    } #@!neyek



    /**

     * Create WebEntry records

     *

     * @param string $processUid     Unique id of Process

     * @param string $userUidCreator Unique id of creator User

     * @param array  $arrayData      Data

     *

     * return void

     */

    public function createWebEntry($processUid, $userUidCreator, array $arrayData)

    {

        try {

            $webEntry = new \ProcessMaker\BusinessModel\WebEntry();



            foreach ($arrayData as $value) {

                $record = $value;



                $arrayWebEntryData = $webEntry->create($processUid, $userUidCreator, $record);

            }

        } catch (Exception $e) {

            throw $e;

        }

    }



    /**

     * Create WebEntry-Event records

     *

     * @param string $processUid     Unique id of Process

     * @param string $userUidCreator Unique id of creator User

     * @param array  $arrayData      Data

     *

     * return void

     */

    public function createWebEntryEvent($processUid, $userUidCreator, array $arrayData)

    {

        try {

            $webEntryEvent = new \ProcessMaker\BusinessModel\WebEntryEvent();



            foreach ($arrayData as $value) {

                $record = $value;



                $arrayWebEntryEventData = $webEntryEvent->create($processUid, $userUidCreator, $record);

            }

        } catch (Exception $e) {

            throw $e;

        }

    }



    /**

     * Create Message-Type records

     *

     * @param array $arrayData Data

     *

     * return void

     */

    public function createMessageType(array $arrayData)

    {

        try {

            $messageType = new \ProcessMaker\BusinessModel\MessageType();



            foreach ($arrayData as $value) {

                $record = $value;



                if ($messageType->exists($record["MSGT_UID"])) {

                    $messageType->delete($record["MSGT_UID"]);

                }



                $result = $messageType->singleCreate($record);

            }

        } catch (Exception $e) {

            throw $e;

        }

    }



    /**

     * Create Message-Type-Variable records

     *

     * @param array $arrayData Data

     *

     * return void

     */

    public function createMessageTypeVariable(array $arrayData)

    {

        try {

            $variable = new \ProcessMaker\BusinessModel\MessageType\Variable();





            foreach ($arrayData as $value) {

                $record = $value;



                if ($variable->exists($record["MSGTV_UID"])) {

                    $variable->delete($record["MSGTV_UID"]);

                }



                $result = $variable->singleCreate($record);

            }

        } catch (Exception $e) {

            throw $e;

        }

    }



    /**

     * Create Message-Event-Definition records

     *

     * @param string $processUid Unique id of Process

     * @param array  $arrayData  Data

     *

     * return void

     */

    public function createMessageEventDefinition($processUid, array $arrayData)

    {

        try {

            $messageEventDefinition = new \ProcessMaker\BusinessModel\MessageEventDefinition();



            foreach ($arrayData as $value) {

                $record = $value;



                $record["MSGED_VARIABLES"] = unserialize($record["MSGED_VARIABLES"]);



                $arrayMessageEventDefinitionData = $messageEventDefinition->create($processUid, $record, false);

            }

        } catch (Exception $e) {

            throw $e;

        }

    }



    /**

     * Create Script-Task records

     *

     * @param string $processUid Unique id of Process

     * @param array  $arrayData  Data

     *

     * return void

     */

    public function createScriptTask($processUid, array $arrayData)

    {

        try {

            $scriptTask = new \ProcessMaker\BusinessModel\ScriptTask();



            foreach ($arrayData as $value) {

                $record = $value;



                $result = $scriptTask->create($processUid, $record);

            }

        } catch (Exception $e) {

            throw $e;

        }

    }



    /**

     * Create Timer-Event records

     *

     * @param string $processUid Unique id of Process

     * @param array  $arrayData  Data

     *

     * return void

     */

    public function createTimerEvent($processUid, array $arrayData)

    {

        try {

            $timerEvent = new \ProcessMaker\BusinessModel\TimerEvent();



            foreach ($arrayData as $value) {

                $record = $value;



                $result = $timerEvent->singleCreate($processUid, $record);

            }

        } catch (Exception $e) {

            throw $e;

        }

    }



    /**

     * Create Email-Event records

     *

     * @param string $processUid Unique id of Process

     * @param array  $arrayData  Data

     *

     * return void

     */

    public function createEmailEvent($processUid, array $arrayData)

    {

        try {

            $emailEvent = new \ProcessMaker\BusinessModel\EmailEvent();



            foreach ($arrayData as $value) {

                $emailEventData = $emailEvent->save($processUid, $value);

            }

        } catch (Exception $e) {

            throw $e;

        }

    }

    

    /**

     * Create Actions by email records

     *

     * @param string $processUid Unique id of Process

     * @param array  $arrayData  Data

     *

     * return void

     */

    public function createActionsByEmail($processUid, array $arrayData)

    {

        try {

            require_once 'classes/model/AbeConfiguration.php';

            $abeConfigurationInstance = new AbeConfiguration();

            foreach ($arrayData as $value) {

                $value['ABE_UID'] = "";

                $abeConfigurationInstance->createOrUpdate($value);

            }

        } catch (Exception $e) {

            throw $e;

        }

    }



    /**

     * Create Files Manager records

     *

     * @param string $processUid Unique id of Process

     * @param array  $arrayData  Data

     *

     * return void

     */

    public function createFilesManager($processUid, array $arrayData)

    {

        try {

            $filesManager = new \ProcessMaker\BusinessModel\FilesManager();



            foreach ($arrayData as $value) {

                $filesManager->addProcessFilesManagerInDb($value);

            }

        } catch (Exception $e) {

            throw $e;

        }

    }



    /**

     * Cleanup Report Tables References from an array of data

     *

     * @param array $aReportTables

     * @return void

     */

    public function cleanupReportTablesReferences ($aReportTables)

    {

        foreach ($aReportTables as $sKey => $aRow) {

            $oReportTables = new ReportTables();

            $oReportTables->deleteReportTable( $aRow['REP_TAB_UID'] );

            $oReportTables->deleteAllReportVars( $aRow['REP_TAB_UID'] );

            $oReportTables->dropTable( $aRow['REP_TAB_NAME'] );

        }

    } #@!neyek



    /**

     * Merge groupwfs data

     *

     * @param array  $arrayGroupwfsData            Data groupwfs

     * @param array  $arrayData                    Data for merge

     * @param string $groupUidFieldNameInArrayData Field name of unique id

     *

     * return array Return an array with all groupwfs data

     */

    public function groupwfsMerge(array $arrayGroupwfsData, array $arrayData, $groupUidFieldNameInArrayData = "GRP_UID")

    {

        try {

            $arrayUid = array();



            foreach ($arrayGroupwfsData as $value) {

                $record = $value;



                $arrayUid[] = $record["GRP_UID"];

            }



            //Merge

            $groupwf = new Groupwf();



            foreach ($arrayData as $value) {

                $record = $value;



                if (isset($record[$groupUidFieldNameInArrayData]) && !in_array($record[$groupUidFieldNameInArrayData], $arrayUid)) {

                    $arrayGroupwfsData[] = $groupwf->Load($record[$groupUidFieldNameInArrayData]);

                }

            }



            //Return

            return $arrayGroupwfsData;

        } catch (Exception $e) {

            throw $e;

        }

    }



    /**

     * Update unique ids in groupwfs data by database

     *

     * @param object $data Data

     *

     * return object Return data

     */

    public function groupwfsUpdateUidByDatabase($data)

    {

        try {

            //Get Groupwf of database

            $arrayGroupwf = array();



            $criteria = new Criteria("workflow");



            $criteria->addSelectColumn(GroupwfPeer::GRP_UID);

            $criteria->addSelectColumn(ContentPeer::CON_VALUE);

            $criteria->addJoin(GroupwfPeer::GRP_UID, ContentPeer::CON_ID, Criteria::LEFT_JOIN);

            $criteria->add(ContentPeer::CON_CATEGORY, "GRP_TITLE", Criteria::EQUAL);

            $criteria->add(ContentPeer::CON_LANG, SYS_LANG, Criteria::EQUAL);



            $rsCriteria = GroupwfPeer::doSelectRS($criteria);

            $rsCriteria->setFetchmode(ResultSet::FETCHMODE_ASSOC);



            while ($rsCriteria->next()) {

                $row = $rsCriteria->getRow();



                $arrayGroupwf[] = $row;

            }



            //Check if any group name exists in database

            $arrayUid = array();



            foreach ($data->groupwfs as $key => $value) {

                $groupwfsRecord = $value;



                foreach ($arrayGroupwf as $key2 => $value2) {

                    $groupwfRecord = $value2;



                    if ($groupwfRecord["CON_VALUE"] == $groupwfsRecord["GRP_TITLE"] && $groupwfRecord["GRP_UID"] != $groupwfsRecord["GRP_UID"]) {

                        //Update unique id

                        $uidOld = $data->groupwfs[$key]["GRP_UID"];



                        $data->groupwfs[$key]["GRP_UID"] = $groupwfRecord["GRP_UID"];

                        $arrayUid[$uidOld] = $groupwfRecord["GRP_UID"];

                        break;

                    }

                }

            }



            //Update in $data

            if (count($arrayUid) > 0) {

                foreach ($data->taskusers as $key => $value) {

                    $record = $value;



                    if (isset($arrayUid[$record["USR_UID"]])) {

                        $data->taskusers[$key]["USR_UID"]= $arrayUid[$record["USR_UID"]];

                    }

                }



                foreach ($data->objectPermissions as $key => $value) {

                    $record = $value;



                    if (isset($arrayUid[$record["USR_UID"]])) {

                        $data->objectPermissions[$key]["USR_UID"]= $arrayUid[$record["USR_UID"]];

                    }

                }



                if (isset($data->processUser)) {

                    foreach ($data->processUser as $key => $value) {

                        $record = $value;



                        if (isset($arrayUid[$record["USR_UID"]])) {

                            $data->processUser[$key]["USR_UID"]= $arrayUid[$record["USR_UID"]];

                        }

                    }

                }

            }



            //Return

            return $data;

        } catch (Exception $e) {

            throw $e;

        }

    }



    /**

     * change Status of any Process

     *

     * @param string $sProUid

     * @return boolean

     */

    public function serializeProcess ($sProUid = '')

    {

        return serialize($this->getWorkflowData($sProUid));

    }



    public function getWorkflowData($sProUid = '')

    {

        $oProcess = new Process();

        $oData = new StdClass();

        $oData->process = $this->getProcessRow( $sProUid, false );

        $oData->tasks = $this->getTaskRows( $sProUid );

        $oData->routes = $this->getRouteRows( $sProUid );

        $oData->lanes = $this->getLaneRows( $sProUid );

        $oData->gateways = $this->getGatewayRows( $sProUid );

        $oData->inputs = $this->getInputRows( $sProUid );

        $oData->outputs = $this->getOutputRows( $sProUid );

        $oData->dynaforms = $this->getDynaformRows( $sProUid );

        $oData->steps = $this->getStepRows( $sProUid );

        $oData->triggers = $this->getTriggerRows( $sProUid );

        $oData->taskusers = $this->getTaskUserRows( $oData->tasks );

        $oData->groupwfs = $this->getGroupwfRows( $oData->taskusers );

        $oData->steptriggers = $this->getStepTriggerRows( $oData->tasks );

        $oData->dbconnections = $this->getDBConnectionsRows( $sProUid );

        $oData->reportTables = $this->getReportTablesRows( $sProUid );

        $oData->reportTablesVars = $this->getReportTablesVarsRows( $sProUid );

        $oData->stepSupervisor = $this->getStepSupervisorRows( $sProUid );

        $oData->objectPermissions = $this->getObjectPermissionRows( $sProUid, $oData);

        $oData->subProcess = $this->getSubProcessRow( $sProUid );

        $oData->caseTracker = $this->getCaseTrackerRow( $sProUid );

        $oData->caseTrackerObject = $this->getCaseTrackerObjectRow( $sProUid );

        $oData->stage = $this->getStageRow( $sProUid );

        $oData->fieldCondition = $this->getFieldCondition( $sProUid );

        $oData->event = $this->getEventRow( $sProUid );

        $oData->caseScheduler = $this->getCaseSchedulerRow( $sProUid );

        $oData->processCategory = $this->getProcessCategoryRow( $sProUid );

        $oData->taskExtraProperties = $this->getTaskExtraPropertiesRows( $sProUid );

        $oData->processUser = $this->getProcessUser($sProUid);

        $oData->processVariables = $this->getProcessVariables($sProUid);

        $oData->webEntry      = $this->getWebEntries($sProUid);

        $oData->webEntryEvent = $this->getWebEntryEvents($sProUid);

        $oData->messageType = $this->getMessageTypes($sProUid);

        $oData->messageTypeVariable = $this->getMessageTypeVariables($sProUid);

        $oData->messageEventDefinition = $this->getMessageEventDefinitions($sProUid);

        $oData->scriptTask = $this->getScriptTasks($sProUid);

        $oData->timerEvent = $this->getTimerEvents($sProUid);

        $oData->emailEvent = $this->getEmailEvent($sProUid);

        $oData->filesManager = $this->getFilesManager($sProUid);

        $oData->abeConfiguration = $this->getActionsByEmail($sProUid);

        $oData->groupwfs = $this->groupwfsMerge($oData->groupwfs, $oData->processUser, "USR_UID");

        $oData->process["PRO_TYPE_PROCESS"] = "PUBLIC";



        //Return

        return $oData;

    }



    /**

     * Save a Serialized Process from an object

     *

     * @param array $oData

     * @return $result an array

     */

    public function saveSerializedProcess ($oData)

    {

        //$oJSON = new Services_JSON();

        //$data = $oJSON->decode($oData);

        //$sProUid = $data->process->PRO_UID;

        $data = unserialize( $oData );

        $sProUid = $data->process['PRO_UID'];

        $path = PATH_DOCUMENT . 'output' . PATH_SEP;



        if (! is_dir( $path )) {

            G::verifyPath( $path, true );

        }



        $proTitle = (substr( G::inflect( $data->process['PRO_TITLE'] ), 0, 245 ));

        $proTitle = preg_replace( "/[^A-Za-z0-9_]/", "", $proTitle );

        //Calculating the maximum length of file name

        $pathLength = strlen( PATH_DATA . "sites" . PATH_SEP . SYS_SYS . PATH_SEP . "files" . PATH_SEP . "output" . PATH_SEP );

        $length = strlen( $proTitle ) + $pathLength;

        $limit = 200;

        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {

            $limit = 150;

        }

        if ($length >= $limit) {

            $proTitle = $this->truncateName($proTitle);

        }

        $index = '';



        $lastIndex = '';



        do {

            $filename = $path . $proTitle . $index . '.pm';

            $lastIndex = $index;



            if ($index == '') {

                $index = 1;

            } else {

                $index ++;

            }

        } while (file_exists( $filename ));



        $proTitle .= $lastIndex;



        $filenameOnly = $proTitle . '.pm';



        $fp = fopen( $filename . 'tpm', "wb" );



        $fsData = sprintf( "%09d", strlen( $oData ) );

        $bytesSaved = fwrite( $fp, $fsData ); //writing the size of $oData

        $bytesSaved += fwrite( $fp, $oData ); //writing the $oData





        foreach ($data->dynaforms as $key => $val) {

            $sFileName = PATH_DYNAFORM . $val['DYN_FILENAME'] . '.xml';

            if (file_exists( $sFileName )) {

                $xmlGuid = $val['DYN_UID'];

                $fsXmlGuid = sprintf( "%09d", strlen( $xmlGuid ) );

                $bytesSaved += fwrite( $fp, $fsXmlGuid ); //writing the size of xml file

                $bytesSaved += fwrite( $fp, $xmlGuid ); //writing the xmlfile





                $xmlContent = file_get_contents( $sFileName );

                $fsXmlContent = sprintf( "%09d", strlen( $xmlContent ) );

                $bytesSaved += fwrite( $fp, $fsXmlContent ); //writing the size of xml file

                $bytesSaved += fwrite( $fp, $xmlContent ); //writing the xmlfile

            }



            $sFileName2 = PATH_DYNAFORM . $val['DYN_FILENAME'] . '.html';

            if (file_exists( $sFileName2 )) {

                $htmlGuid = $val['DYN_UID'];

                $fsHtmlGuid = sprintf( "%09d", strlen( $htmlGuid ) );

                $bytesSaved += fwrite( $fp, $fsHtmlGuid ); //writing size dynaform id

                $bytesSaved += fwrite( $fp, $htmlGuid ); //writing dynaform id





                $htmlContent = file_get_contents( $sFileName2 );

                $fsHtmlContent = sprintf( "%09d", strlen( $htmlContent ) );

                $bytesSaved += fwrite( $fp, $fsHtmlContent ); //writing the size of xml file

                $bytesSaved += fwrite( $fp, $htmlContent ); //writing the htmlfile

            }

        }

        /**

         * By <erik@colosa.com>

         * here we should work for the new functionalities

         * we have a many files for attach into this file

         *

         * here we go with the anothers files ;)

         */

        //before to do something we write a header into pm file for to do a differentiation between document types





        //create the store object

        //$file_objects = new ObjectCellection();





        // for mailtemplates files

        $MAILS_ROOT_PATH = PATH_DATA . 'sites' . PATH_SEP . SYS_SYS . PATH_SEP . 'mailTemplates' . PATH_SEP . $data->process['PRO_UID'];



        $isMailTempSent = false;

        $isPublicSent = false;

        //if this process have any mailfile

        if (is_dir( $MAILS_ROOT_PATH )) {



            //get mail files list from this directory

            $file_list = scandir( $MAILS_ROOT_PATH );



            foreach ($file_list as $filename) {

                // verify if this filename is a valid file, because it could be . or .. on *nix systems

                if ($filename != '.' && $filename != '..') {

                    if (@is_readable( $MAILS_ROOT_PATH . PATH_SEP . $filename )) {

                        $sFileName = $MAILS_ROOT_PATH . PATH_SEP . $filename;

                        if (file_exists( $sFileName )) {

                            if (! $isMailTempSent) {

                                $bytesSaved += fwrite( $fp, 'MAILTEMPL' );

                                $isMailTempSent = true;

                            }

                            //$htmlGuid    = $val['DYN_UID'];

                            $fsFileName = sprintf( "%09d", strlen( $filename ) );

                            $bytesSaved += fwrite( $fp, $fsFileName ); //writing the fileName size

                            $bytesSaved += fwrite( $fp, $filename ); //writing the fileName size





                            $fileContent = file_get_contents( $sFileName );

                            $fsFileContent = sprintf( "%09d", strlen( $fileContent ) );

                            $bytesSaved += fwrite( $fp, $fsFileContent ); //writing the size of xml file

                            $bytesSaved += fwrite( $fp, $fileContent ); //writing the htmlfile

                        }

                    }

                }

            }

        }



        // for public files

        $PUBLIC_ROOT_PATH = PATH_DATA . 'sites' . PATH_SEP . SYS_SYS . PATH_SEP . 'public' . PATH_SEP . $data->process['PRO_UID'];



        //Get WebEntry file names

        $arrayWebEntryFile = array();



        if (is_dir($PUBLIC_ROOT_PATH)) {

            if ($dirh = opendir($PUBLIC_ROOT_PATH)) {

                while (($file = readdir($dirh)) !== false) {

                    if (preg_match("/^(.+)Post\.php$/", $file, $arrayMatch)) {

                        $arrayWebEntryFile[] = $arrayMatch[1] . ".php";

                        $arrayWebEntryFile[] = $arrayMatch[1] . "Post.php";

                    }

                }



                closedir($dirh);

            }

        }



        //if this process have any mailfile

        if (is_dir( $PUBLIC_ROOT_PATH )) {

            //get mail files list from this directory

            $file_list = scandir( $PUBLIC_ROOT_PATH );

            foreach ($file_list as $filename) {

                // verify if this filename is a valid file, because it could be . or .. on *nix systems

                if ($filename != '.' && $filename != '..') {

                    if (in_array($filename, $arrayWebEntryFile)) {

                        continue;

                    }



                    if (@is_readable( $PUBLIC_ROOT_PATH . PATH_SEP . $filename )) {

                        $sFileName = $PUBLIC_ROOT_PATH . PATH_SEP . $filename;

                        if (file_exists( $sFileName )) {

                            if (! $isPublicSent) {

                                $bytesSaved += fwrite( $fp, 'PUBLIC   ' );

                                $isPublicSent = true;

                            }

                            //$htmlGuid    = $val['DYN_UID'];

                            $fsFileName = sprintf( "%09d", strlen( $filename ) );

                            $bytesSaved += fwrite( $fp, $fsFileName );

                            //writing the fileName size

                            $bytesSaved += fwrite( $fp, $filename );

                            //writing the fileName size

                            $fileContent = file_get_contents( $sFileName );

                            $fsFileContent = sprintf( "%09d", strlen( $fileContent ) );

                            $bytesSaved += fwrite( $fp, $fsFileContent );

                            //writing the size of xml file

                            $bytesSaved += fwrite( $fp, $fileContent );

                            //writing the htmlfile

                        }

                    }

                }

            }

        }



        /*

        // for public files

        $PUBLIC_ROOT_PATH = PATH_DATA.'sites'.PATH_SEP.SYS_SYS.PATH_SEP.'public'.PATH_SEP.$data->process['PRO_UID'];

        //if this process have any mailfile

        if ( is_dir( $PUBLIC_ROOT_PATH ) ) {

            //get mail files list from this directory

            $files_list = scandir($PUBLIC_ROOT_PATH);

            foreach ($file_list as $filename) {

              // verify if this filename is a valid file, beacuse it could be . or .. on *nix systems

                if($filename != '.' && $filename != '..'){

                    if (@is_readable($PUBLIC_ROOT_PATH.PATH_SEP.$nombre_archivo)) {

                        $tmp = explode('.', $filename);

                        $ext = $tmp[1];

                        $ext_fp = fopen($PUBLIC_ROOT_PATH.PATH_SEP.$nombre_archivo, 'r');

                        $file_data = fread($ext_fp, filesize($PUBLIC_ROOT_PATH.PATH_SEP.$nombre_archivo));

                        fclose($ext_fp);

                        $file_objects->add($filename, $ext, $file_data,'public');

                    }

                }

            }

        }



        //So,. we write the store object into pm export file

        $extended_data = serialize($file_objects);

        $bytesSaved += fwrite( $fp, $extended_data );

        */

        /* under here, I've not modified those lines */

        fclose( $fp );

        //$bytesSaved = file_put_contents  ( $filename  , $oData  );

        $filenameLink = 'processes_DownloadFile?p=' . $proTitle . '&r=' . rand( 100, 1000 );

        $result['PRO_UID'] = $data->process['PRO_UID'];

        $result['PRO_TITLE'] = $data->process['PRO_TITLE'];

        $result['PRO_DESCRIPTION'] = $data->process['PRO_DESCRIPTION'];

        $result['SIZE'] = $bytesSaved;

        $result['FILENAME'] = $filenameOnly;

        $result['FILENAME_LINK'] = $filenameLink;

        return $result;

    }



    /**

     * Get the process Data form a filename

     *

     * @param array $pmFilename

     * @return void

     */

    public function getProcessData ($pmFilename)

    {

        $oProcess = new Process();

        if (! file_exists( $pmFilename )) {

            throw (new Exception( 'Unable to read uploaded file, please check permissions. ' ));

        }

        if (! filesize( $pmFilename ) >= 9) {

            throw (new Exception( 'Uploaded file is corrupted, please check the file before continuing. ' ));

        }

        clearstatcache();

        $fp = fopen( $pmFilename, "rb" );

        $fsData = intval( fread( $fp, 9 ) ); //reading the size of $oData

        $contents = '';

        $contents = @fread( $fp, $fsData ); //reading string $oData





        if ($contents != '') {

            $oData = unserialize( $contents );

            if ($oData === false) {

                throw new Exception( "Process file is not valid" );

            }

            foreach ($oData->dynaforms as $key => $value) {

                if ($value['DYN_TYPE'] == 'grid') {

                    $oData->gridFiles[$value['DYN_UID']] = $value['DYN_UID'];

                }

            }



            $oData->dynaformFiles = array ();

            $sIdentifier = 0;

            while (! feof( $fp ) && is_numeric( $sIdentifier )) {

                $sIdentifier = fread( $fp, 9 ); //reading the block identifier

                if (is_numeric( $sIdentifier )) {

                    $fsXmlGuid = intval( $sIdentifier ); //reading the size of $filename

                    if ($fsXmlGuid > 0) {

                        $XmlGuid = fread( $fp, $fsXmlGuid ); //reading string $XmlGuid

                    }



                    $fsXmlContent = intval( fread( $fp, 9 ) ); //reading the size of $XmlContent

                    if ($fsXmlContent > 0) {

                        $oData->dynaformFiles[$XmlGuid] = $XmlGuid;

                        $XmlContent = fread( $fp, $fsXmlContent ); //reading string $XmlContent

                        unset( $XmlContent );

                    }

                }

            }

        } else {

            $oData = null;

        }

        fclose( $fp );

        return $oData;

    }



    // import process related functions





    /**

     * function checkExistingGroups

     * checkExistingGroups check if any of the groups listed in the parameter

     * array exist and wich are those, that is the result $sFilteredGroups array.

     *

     * @author gustavo cruz gustavo-at-colosa.com

     * @param $sGroupList array of a group list

     * @return $existingGroupList array of existing groups or null

     */

    public function checkExistingGroups ($sGroupList)

    {

        $aGroupwf = array ();

        $oCriteria = new Criteria( 'workflow' );

        $oCriteria->addSelectColumn( GroupwfPeer::GRP_UID );

        $oCriteria->addSelectColumn( ContentPeer::CON_ID );

        $oCriteria->addSelectColumn( ContentPeer::CON_VALUE );

        $oCriteria->add( ContentPeer::CON_CATEGORY, 'GRP_TITLE' );

        $oCriteria->add(ContentPeer::CON_LANG, SYS_LANG);

        $oCriteria->addJoin( ContentPeer::CON_ID, GroupwfPeer::GRP_UID );

        $oDataset = ContentPeer::doSelectRS( $oCriteria );

        $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );

        $oDataset->next();

        while ($aRow = $oDataset->getRow()) {

            $aGroupwf[] = $aRow;

            $oDataset->next();

        }

        //check if any group name exists in the dbase

        if (is_array( $sGroupList )) {

            foreach ($aGroupwf as $groupBase) {

                foreach ($sGroupList as $group) {

                    if ($groupBase['CON_VALUE'] == $group['GRP_TITLE'] && $groupBase['CON_ID'] != $group['GRP_UID']) {

                        $existingGroupList[] = $group;

                    }

                }

            }

        }

        //return $sGroupList;

        if (isset( $existingGroupList )) {

            return $existingGroupList;

        } else {

            return null;

        }

    }



    /**

     * function renameExistingGroups

     * renameExistingGroups check if any of the groups listed in the parameter

     * array exist and wich are those, then rename the file adding a number

     * suffix to the title atribute of each element of the $renamedGroupList array.

     *

     * @author gustavo cruz gustavo-at-colosa.com

     * @param $sGroupList array of a group list

     * @return $renamedGroupList array of existing groups

     */



    public function renameExistingGroups ($sGroupList)

    {

        $checkedGroup = $this->checkExistingGroups( $sGroupList );

        foreach ($sGroupList as $groupBase) {

            foreach ($checkedGroup as $group) {

                if ($groupBase['GRP_TITLE'] == $group['GRP_TITLE']) {

                    $index = substr( $groupBase['GRP_TITLE'], - 1, 0 );

                    if (is_int( $index )) {

                        $index ++;

                    } else {

                        $index = 1;

                    }

                    $groupBase['GRP_TITLE'] = $groupBase['GRP_TITLE'] . $index;

                }



            }

            $renamedGroupList[] = $groupBase;

        }



        if (isset( $renamedGroupList )) {

            return $renamedGroupList;

        } else {

            return null;

        }

    }



    /**

     * function mergeExistingGroups

     * mergeExistingGroups check if any of the groups listed in the parameter

     * array exist and wich are those, then replaces the id of the elements in

     * in the $mergedGroupList array.

     *

     * @author gustavo cruz gustavo-at-colosa.com

     * @param $sGroupList array of a group list

     * @return $mergedGroupList array of existing groups

     */

    //Deprecated

    public function mergeExistingGroups ($sGroupList)

    {

        $oCriteria = new Criteria( 'workflow' );

        $oCriteria->addSelectColumn( GroupwfPeer::GRP_UID );

        $oCriteria->addSelectColumn( ContentPeer::CON_ID );

        $oCriteria->addSelectColumn( ContentPeer::CON_VALUE );

        $oCriteria->add( ContentPeer::CON_CATEGORY, 'GRP_TITLE' );

        $oCriteria->add( ContentPeer::CON_LANG, 'en' );

        $oCriteria->addJoin( ContentPeer::CON_ID, GroupwfPeer::GRP_UID );

        $oDataset = ContentPeer::doSelectRS( $oCriteria );

        $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );

        $oDataset->next();

        while ($aRow = $oDataset->getRow()) {

            $aGroupwf[] = $aRow;

            $oDataset->next();

        }

        //check if any group name exists in the dbase

        foreach ($sGroupList as $group) {

            $merged = false;

            foreach ($aGroupwf as $groupBase) {

                if ($groupBase['CON_VALUE'] == $group['GRP_TITLE'] && $groupBase['CON_ID'] != $group['GRP_UID']) {

                    $group['GRP_UID'] = $groupBase['CON_ID'];

                    $mergedGroupList[] = $group;

                    $merged = true;

                }

            }



            if (! $merged) {

                $mergedGroupList[] = $group;

            }

        }



        if (isset( $mergedGroupList )) {

            return $mergedGroupList;

        } else {

            return null;

        }

    }



    /**

     * function mergeExistingUsers

     * mergeExistingGroups check if any of the groups listed in the parameter

     * array exist and wich are those, then replaces the id of the elements in

     * in the $mergedGroupList array.

     *

     * @author gustavo cruz gustavo-at-colosa.com

     * @param $sBaseGroupList array of a group list with the original group list

     * @param $sGroupList array of a group list with the merged group list

     * @param $sTaskUserList array of the task user list, it contents the link between

     * the task and the group list

     * @return $mergedTaskUserList array of the merged task user list

     */

    //Deprecated

    public function mergeExistingUsers ($sBaseGroupList, $sGroupList, $sTaskUserList)

    {

        foreach ($sTaskUserList as $taskuser) {

            $merged = false;

            foreach ($sBaseGroupList as $groupBase) {

                foreach ($sGroupList as $group) {

                    // check if the group has been merged

                    if ($groupBase['GRP_TITLE'] == $group['GRP_TITLE'] && $groupBase['GRP_UID'] != $group['GRP_UID'] && $groupBase['GRP_UID'] == $taskuser['USR_UID']) {

                        // merging the user id to match the merged group

                        $taskuser['USR_UID'] = $group['GRP_UID'];

                        $mergedTaskUserList[] = $taskuser;

                        $merged = true;

                    }

                }

            }

            //if hasn't been merged set the default value

            if (! $merged) {

                $mergedTaskUserList[] = $taskuser;

            }

        }

        if (isset( $mergedTaskUserList )) {

            return $mergedTaskUserList;

        } else {

            return null;

        }

    }



    // end of import process related functions





    /**

     * disable all previous process with the parent $sProUid

     *

     * @param $sProUid process uid

     * @return void

     */

    public function disablePreviousProcesses ($sProUid)

    {

        //change status of process

        $oCriteria = new Criteria( 'workflow' );

        $oCriteria->add( ProcessPeer::PRO_PARENT, $sProUid );

        $oDataset = ProcessPeer::doSelectRS( $oCriteria );

        $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );

        $oDataset->next();

        $oProcess = new Process();

        while ($aRow = $oDataset->getRow()) {

            $aRow['PRO_STATUS'] = 'DISABLED';

            $aRow['PRO_UPDATE_DATE'] = 'now';

            $oProcess->update( $aRow );

            $oDataset->next();

        }

    }



    /**

     * create the files from a .

     *

     *

     * pm file

     *

     * @param $oData process data

     * @param $pmFilename process file name

     * @return boolean true

     */

    public function createFiles ($oData, $pmFilename)

    {

        if (! file_exists( $pmFilename )) {

            throw (new Exception( 'Unable to read uploaded .pm file, please check permissions. ' ));

        }

        if (! filesize( $pmFilename ) >= 9) {

            throw (new Exception( 'Uploaded .pm file is corrupted, please check the file before continue. ' ));

        }

        $fp = fopen( $pmFilename, "rb" );

        $fsData = intval( fread( $fp, 9 ) ); //reading the size of $oData

        $contents = fread( $fp, $fsData ); //reading string $oData





        $path = PATH_DYNAFORM . $oData->process['PRO_UID'] . PATH_SEP;

        if (! is_dir( $path )) {

            G::verifyPath( $path, true );

        }



        $sIdentifier = 1;

        while (! feof( $fp ) && is_numeric( $sIdentifier )) {

            $sIdentifier = fread( $fp, 9 ); //reading the size of $filename

            if (is_numeric( $sIdentifier )) {

                $fsXmlGuid = intval( $sIdentifier ); //reading the size of $filename

                if ($fsXmlGuid > 0) {

                    $XmlGuid = fread( $fp, $fsXmlGuid ); //reading string $XmlGuid

                }

                $fsXmlContent = intval( fread( $fp, 9 ) ); //reading the size of $XmlContent

                if ($fsXmlContent > 0) {

                    $newXmlGuid = $oData->dynaformFiles[$XmlGuid];

                    if (isset( $oData->process['PRO_UID_OLD'] )) {

                        $XmlContent = fread( $fp, $fsXmlContent ); //reading string $XmlContent

                        $XmlContent = str_replace( $oData->process['PRO_UID_OLD'], $oData->process['PRO_UID'], $XmlContent );

                        $XmlContent = str_replace( $XmlGuid, $newXmlGuid, $XmlContent );



                        if (isset( $oData->inputFiles )) {

                            foreach($oData->inputFiles as $input => $valInput){

                                $oldInput = $input;

                                $newInput = $oData->inputFiles[$oldInput];

                                $XmlContent = str_replace( $oldInput, $newInput, $XmlContent );

                            }

                        }



                        //foreach

                        if (isset( $oData->gridFiles )) {

                            if (is_array( $oData->gridFiles )) {

                                foreach ($oData->gridFiles as $key => $value) {

                                    $XmlContent = str_replace( $key, $value, $XmlContent );

                                }

                            }

                        }



                        if (isset( $oData->sqlConnections )) {

                            foreach ($oData->sqlConnections as $key => $value) {

                                $XmlContent = str_replace( $key, $value, $XmlContent );

                            }



                        }



                        #here we verify if is adynaform or a html

                        $aAux = explode( ' ', $XmlContent );

                        $ext = (strpos( $aAux[0], '<?xml' ) !== false ? '.xml' : '.html');

                        $sFileName = $path . $newXmlGuid . $ext;

                        $bytesSaved = @file_put_contents( $sFileName, $XmlContent );

                        //if ( $bytesSaved != $fsXmlContent ) throw ( new Exception ('Error writing dynaform file in directory : ' . $path ) );

                    }

                }

            }

        }



        //now mailTemplates and public files

        $pathPublic = PATH_DATA_SITE . 'public' . PATH_SEP . $oData->process['PRO_UID'] . PATH_SEP;

        $pathMailTem = PATH_DATA_SITE . 'mailTemplates' . PATH_SEP . $oData->process['PRO_UID'] . PATH_SEP;

        G::mk_dir( $pathPublic );

        G::mk_dir( $pathMailTem );



        if ($sIdentifier == 'MAILTEMPL') {

            $sIdentifier = 1;

            while (! feof( $fp ) && is_numeric( $sIdentifier )) {

                $sIdentifier = fread( $fp, 9 );  //reading the size of $filename

                if (is_numeric( $sIdentifier )) {

                    $fsFileName = intval( $sIdentifier ); //reading the size of $filename

                    if ($fsFileName > 0) {

                        $sFileName = fread( $fp, $fsFileName ); //reading filename string

                    }

                    $fsContent = intval( fread ( $fp, 9)) or 0; //reading the size of $Content

                    if ($fsContent > 0) {

                        $fileContent = fread( $fp, $fsContent ); //reading string $XmlContent

                        $newFileName = $pathMailTem . $sFileName;

                        $bytesSaved = @file_put_contents( $newFileName, $fileContent );

                        if ($bytesSaved != $fsContent) {

                            throw (new Exception( 'Error writing MailTemplate file in directory : ' . $pathMailTem ));

                        }

                    }

                }

            }

        }



        if (trim( $sIdentifier ) == 'PUBLIC') {

            //Get WebEntry file names

            $arrayWebEntryFile = array();



            $fh = fopen($pmFilename, "rb");

            $contents = fread($fh, intval(fread($fh, 9))); //Reading string $oData



            while (!feof($fh)) {

                $fsFileName = intval(fread($fh, 9)); //Reading the size of $filename



                if ($fsFileName > 0) {

                    $sFileName = fread($fh, $fsFileName); //Reading filename string



                    if (preg_match("/^(.+)Post\.php$/", $sFileName, $arrayMatch)) {

                        $arrayWebEntryFile[] = $arrayMatch[1] . ".php";

                        $arrayWebEntryFile[] = $arrayMatch[1] . "Post.php";

                    }

                }

            }



            fclose($fh);



            //Public files

            $sIdentifier = 1;

            while (! feof( $fp ) && is_numeric( $sIdentifier )) {

                $sIdentifier = fread( $fp, 9 ); //reading the size of $filename

                if (is_numeric( $sIdentifier )) {

                    $fsFileName = intval( $sIdentifier ); //reading the size of $filename

                    if ($fsFileName > 0) {

                        $sFileName = fread( $fp, $fsFileName ); //reading filename string

                    }

                    $fsContent = intval( fread ( $fp, 9)) or 0; //reading the size of $Content

                    if ($fsContent > 0) {

                        $fileContent = fread( $fp, $fsContent ); //reading string $XmlContent

                        $newFileName = $pathPublic . $sFileName;



                        if (in_array($sFileName, $arrayWebEntryFile)) {

                            continue;

                        }



                        $bytesSaved = @file_put_contents( $newFileName, $fileContent );

                        if ($bytesSaved != $fsContent) {

                            throw (new Exception( 'Error writing Public file in directory : ' . $pathPublic ));

                        }

                    }

                }

            }

        }



        fclose( $fp );



        return true;



    }



    /**

     * The current method is for filter every row that exist in

     * the Configuration table

     *

     * @param array $aTaskExtraProperties

     * @return void

     */

    public function createTaskExtraPropertiesRows ($aTaskExtraProperties)

    {

        if (count($aTaskExtraProperties) > 0) {

            foreach ($aTaskExtraProperties as $key => $row) {

                $oConfig = new Configuration();



                if ($oConfig->exists( $row['CFG_UID'], $row['OBJ_UID'], $row['PRO_UID'], $row['USR_UID'], $row['APP_UID']) ) {

                    $oConfig->remove( $row['CFG_UID'], $row['OBJ_UID'], $row['PRO_UID'], $row['USR_UID'], $row['APP_UID'] );

                    $oConfig->setDeleted(false);

                }

                $res = $oConfig->create( $row );

                $oConfig->setNew(true);



                if (method_exists($oConfig, "setProperties")) {

                    $oConfig->setProperties();

                }

            }

        }

        return;

    }



    /**

     * this function remove all Process except the PROCESS ROW

     *

     * @param string $sProUid

     * @return boolean

     */

    public function removeProcessRows ($sProUid)

    {

        try {

            //Instance all classes necesaries

            $oProcess = new Process();

            $oDynaform = new Dynaform();

            $oInputDocument = new InputDocument();

            $oOutputDocument = new OutputDocument();

            $oTrigger = new Triggers();

            $oStepTrigger = new StepTrigger();

            $oRoute = new Route();

            $oStep = new Step();

            $oSubProcess = new SubProcess();

            $oCaseTracker = new CaseTracker();

            $oCaseTrackerObject = new CaseTrackerObject();

            $oObjectPermission = new ObjectPermission();

            $oSwimlaneElement = new SwimlanesElements();

            $oConnection = new DbSource();

            $oStage = new Stage();

            $oEvent = new Event();

            $oCaseScheduler = new CaseScheduler();

            $oConfig = new Configuration();



            //Delete the tasks of process

            $oCriteria = new Criteria( 'workflow' );

            $oCriteria->add( TaskPeer::PRO_UID, $sProUid );

            $oDataset = TaskPeer::doSelectRS( $oCriteria );

            $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );

            $oDataset->next();

            $oTask = new Task();

            while ($aRow = $oDataset->getRow()) {

                $oCriteria = new Criteria( 'workflow' );

                $oCriteria->add( StepTriggerPeer::TAS_UID, $aRow['TAS_UID'] );

                StepTriggerPeer::doDelete( $oCriteria );

                if ($oTask->taskExists( $aRow['TAS_UID'] )) {

                    $oTask->remove( $aRow['TAS_UID'] );

                }

                $oDataset->next();

            }



            //Delete the dynaforms of process

            $oCriteria = new Criteria( 'workflow' );

            $oCriteria->add( DynaformPeer::PRO_UID, $sProUid );

            $oDataset = DynaformPeer::doSelectRS( $oCriteria );

            $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );

            $oDataset->next();

            while ($aRow = $oDataset->getRow()) {

                $sWildcard = PATH_DYNAFORM . $aRow['PRO_UID'] . PATH_SEP . $aRow['DYN_UID'] . '_tmp*';

                foreach (glob( $sWildcard ) as $fn) {

                    @unlink( $fn );

                }

                $sWildcard = PATH_DYNAFORM . $aRow['PRO_UID'] . PATH_SEP . $aRow['DYN_UID'] . '.*';

                foreach (glob( $sWildcard ) as $fn) {

                    @unlink( $fn );

                }

                if ($oDynaform->dynaformExists( $aRow['DYN_UID'] )) {

                    $oDynaform->remove( $aRow['DYN_UID'] );

                }

                $oDataset->next();

            }



            //Delete the input documents of process

            $oCriteria = new Criteria( 'workflow' );

            $oCriteria->add( InputDocumentPeer::PRO_UID, $sProUid );

            $oDataset = InputDocumentPeer::doSelectRS( $oCriteria );

            $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );

            $oDataset->next();

            while ($aRow = $oDataset->getRow()) {

                if ($oInputDocument->InputExists( $aRow['INP_DOC_UID'] )) {

                    $oInputDocument->remove( $aRow['INP_DOC_UID'] );

                }

                $oDataset->next();

            }



            //Delete the output documents of process

            $oCriteria = new Criteria( 'workflow' );

            $oCriteria->add( OutputDocumentPeer::PRO_UID, $sProUid );

            $oDataset = OutputDocumentPeer::doSelectRS( $oCriteria );

            $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );

            $oDataset->next();

            while ($aRow = $oDataset->getRow()) {

                if ($oOutputDocument->OutputExists( $aRow['OUT_DOC_UID'] )) {

                    $oOutputDocument->remove( $aRow['OUT_DOC_UID'] );

                }

                $oDataset->next();

            }



            //Delete the steps

            $oCriteria = new Criteria( 'workflow' );

            $oCriteria->add( StepPeer::PRO_UID, $sProUid );

            $oDataset = StepPeer::doSelectRS( $oCriteria );

            $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );

            $oDataset->next();

            while ($aRow = $oDataset->getRow()) {

                //Delete the steptrigger of process

                /*$oCriteria = new Criteria('workflow');

                  $oCriteria->add(StepTriggerPeer::STEP_UID, $aRow['STEP_UID']);

                  $oDataseti = StepTriggerPeer::doSelectRS($oCriteria);

                  $oDataseti->setFetchmode(ResultSet::FETCHMODE_ASSOC);

                  $oDataseti->next();

                  while ($aRowi = $oDataseti->getRow()) {

                  if ($oStepTrigger->stepTriggerExists($aRowi['STEP_UID'], $aRowi['TAS_UID'], $aRowi['TRI_UID'], $aRowi['ST_TYPE']))

                  $oStepTrigger->remove($aRowi['STEP_UID'], $aRowi['TAS_UID'], $aRowi['TRI_UID'], $aRowi['ST_TYPE']);

                  $oDataseti->next();

                  }*/

                $oStep->remove( $aRow['STEP_UID'] );

                $oDataset->next();

            }



            //Delete the StepSupervisor

            $oCriteria = new Criteria( 'workflow' );

            $oCriteria->add( StepSupervisorPeer::PRO_UID, $sProUid );

            $oDataset = StepSupervisorPeer::doSelectRS( $oCriteria );

            $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );

            $oDataset->next();

            while ($aRow = $oDataset->getRow()) {

                if ($oStep->StepExists( $aRow['STEP_UID'] )) {

                    $oStep->remove( $aRow['STEP_UID'] );

                }

                $oDataset->next();

            }



            //Delete the triggers of process

            $oCriteria = new Criteria( 'workflow' );

            $oCriteria->add( TriggersPeer::PRO_UID, $sProUid );

            $oDataset = TriggersPeer::doSelectRS( $oCriteria );

            $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );

            $oDataset->next();

            while ($aRow = $oDataset->getRow()) {

                if ($oTrigger->TriggerExists( $aRow['TRI_UID'] )) {

                    $oTrigger->remove( $aRow['TRI_UID'] );

                }

                $oDataset->next();

            }

            //Delete the routes of process

            $oCriteria = new Criteria( 'workflow' );

            $oCriteria->add( RoutePeer::PRO_UID, $sProUid );

            $oDataset = RoutePeer::doSelectRS( $oCriteria );

            $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );

            $oDataset->next();

            while ($aRow = $oDataset->getRow()) {

                if ($oRoute->routeExists( $aRow['ROU_UID'] )) {

                    $oRoute->remove( $aRow['ROU_UID'] );

                }

                $oDataset->next();

            }

            //Delete the swimlanes elements of process

            $oCriteria = new Criteria( 'workflow' );

            $oCriteria->add( SwimlanesElementsPeer::PRO_UID, $sProUid );

            $oDataset = SwimlanesElementsPeer::doSelectRS( $oCriteria );

            $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );

            $oDataset->next();

            while ($aRow = $oDataset->getRow()) {

                if ($oSwimlaneElement->swimlanesElementsExists( $aRow['SWI_UID'] )) {

                    $oSwimlaneElement->remove( $aRow['SWI_UID'] );

                }

                $oDataset->next();

            }



            //Delete the DB connections of process

            $oCriteria = new Criteria( 'workflow' );

            $oCriteria->add( DbSourcePeer::PRO_UID, $sProUid );

            $oDataset = DbSourcePeer::doSelectRS( $oCriteria );

            $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );

            $oDataset->next();

            while ($aRow = $oDataset->getRow()) {

                if ($oConnection->Exists( $aRow['DBS_UID'], $aRow['PRO_UID'] )) {

                    $oConnection->remove( $aRow['DBS_UID'], $aRow['PRO_UID'] );

                }

                $oDataset->next();

            }



            //Delete the sub process of process

            $oCriteria = new Criteria( 'workflow' );

            $oCriteria->add( SubProcessPeer::PRO_PARENT, $sProUid );

            $oDataset = SubProcessPeer::doSelectRS( $oCriteria );

            $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );

            $oDataset->next();

            while ($aRow = $oDataset->getRow()) {

                if ($oSubProcess->subProcessExists( $aRow['SP_UID'] )) {

                    $oSubProcess->remove( $aRow['SP_UID'] );

                }

                $oDataset->next();

            }



            //Delete the caseTracker of process

            $oCriteria = new Criteria( 'workflow' );

            $oCriteria->add( CaseTrackerPeer::PRO_UID, $sProUid );

            $oDataset = CaseTrackerPeer::doSelectRS( $oCriteria );

            $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );

            $oDataset->next();

            while ($aRow = $oDataset->getRow()) {

                if ($oCaseTracker->caseTrackerExists( $aRow['PRO_UID'] )) {

                    $oCaseTracker->remove( $aRow['PRO_UID'] );

                }

                $oDataset->next();

            }



            //Delete the caseTrackerObject of process

            $oCriteria = new Criteria( 'workflow' );

            $oCriteria->add( CaseTrackerObjectPeer::PRO_UID, $sProUid );

            $oDataset = CaseTrackerObjectPeer::doSelectRS( $oCriteria );

            $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );

            $oDataset->next();

            while ($aRow = $oDataset->getRow()) {

                if ($oCaseTrackerObject->caseTrackerObjectExists( $aRow['CTO_UID'] )) {

                    $oCaseTrackerObject->remove( $aRow['CTO_UID'] );

                }

                $oDataset->next();

            }



            //Delete the ObjectPermission of process

            $oCriteria = new Criteria( 'workflow' );

            $oCriteria->add( ObjectPermissionPeer::PRO_UID, $sProUid );

            $oDataset = ObjectPermissionPeer::doSelectRS( $oCriteria );

            $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );

            $oDataset->next();

            while ($aRow = $oDataset->getRow()) {

                if ($oObjectPermission->Exists( $aRow['OP_UID'] )) {

                    $oObjectPermission->remove( $aRow['OP_UID'] );

                }

                $oDataset->next();

            }



            //Delete the Stage of process

            $oCriteria = new Criteria( 'workflow' );

            $oCriteria->add( StagePeer::PRO_UID, $sProUid );

            $oDataset = StagePeer::doSelectRS( $oCriteria );

            $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );

            $oDataset->next();

            while ($aRow = $oDataset->getRow()) {

                if ($oStage->Exists( $aRow['STG_UID'] )) {

                    $oStage->remove( $aRow['STG_UID'] );

                }

                $oDataset->next();

            }



            //Delete the Event of process

            $oCriteria = new Criteria( 'workflow' );

            $oCriteria->add( EventPeer::PRO_UID, $sProUid );

            $oDataset = EventPeer::doSelectRS( $oCriteria );

            $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );

            $oDataset->next();

            while ($aRow = $oDataset->getRow()) {

                if ($oEvent->Exists( $aRow['EVN_UID'] )) {

                    $oEvent->remove( $aRow['EVN_UID'] );

                }

                $oDataset->next();

                if ($oEvent->existsByTaskUidFrom( $aRow['TAS_UID'] )) {

                    $aRowEvent = $oEvent->getRowByTaskUidFrom( $aRow['TAS_UID'] );

                    $oEvent->remove( $aRowEvent['EVN_UID'] );

                }

                $oDataset->next();

            }



            //Delete the CaseScheduler of process

            $oCriteria = new Criteria( 'workflow' );

            $oCriteria->add( CaseSchedulerPeer::PRO_UID, $sProUid );

            $oDataset = CaseSchedulerPeer::doSelectRS( $oCriteria );

            $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );

            $oDataset->next();

            while ($aRow = $oDataset->getRow()) {

                if ($oCaseScheduler->Exists( $aRow['SCH_UID'] )) {

                    $oCaseScheduler->remove( $aRow['SCH_UID'] );

                }

                $oDataset->next();

            }



            //Delete the TaskExtraProperties of the process

            $oCriteria = new Criteria( 'workflow' );

            $oCriteria->addSelectColumn( ConfigurationPeer::CFG_UID );

            $oCriteria->addSelectColumn( ConfigurationPeer::OBJ_UID );

            $oCriteria->addSelectColumn( ConfigurationPeer::CFG_VALUE );

            $oCriteria->addSelectColumn( TaskPeer::PRO_UID );

            $oCriteria->addSelectColumn( ConfigurationPeer::USR_UID );

            $oCriteria->addSelectColumn( ConfigurationPeer::APP_UID );

            $oCriteria->add( TaskPeer::PRO_UID, $sProUid );

            $oCriteria->add( ConfigurationPeer::CFG_UID, 'TAS_EXTRA_PROPERTIES' );

            $oCriteria->addJoin( ConfigurationPeer::OBJ_UID, TaskPeer::TAS_UID );

            $oDataset = ConfigurationPeer::doSelectRS( $oCriteria );

            $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );

            $oDataset->next();

            while ($aRow = $oDataset->getRow()) {

                if ($oConfig->exists($aRow['CFG_UID'], $aRow['OBJ_UID'], $aRow['PRO_UID'], $aRow['USR_UID'], $aRow['APP_UID'])) {

                    $oConfig->remove( $aRow['CFG_UID'], $aRow['OBJ_UID'], $aRow['PRO_UID'], $aRow['USR_UID'], $aRow['APP_UID'] );

                }

                $oDataset->next();

            }



            return true;

        } catch (Exception $oError) {

            throw ($oError);

        }

    }



    /**

     * this function creates a new Process, defined in the object $oData

     *

     * @param string $sProUid

     * @return boolean

     */

    public function createProcessFromData ($oData, $pmFilename)

    {

        $this->removeProcessRows( $oData->process['PRO_UID'] );



        // (*) Creating process dependencies

        // creating the process category

        $this->createProcessCategoryRow( isset( $oData->processCategory ) ? $oData->processCategory : null );



        // create the process

        $this->createProcessRow($oData->process);



        $this->createTaskRows( $oData->tasks );

        //it was commented becuase it seems to be working fine

        //$this->createEventRows(isset($oData->event) ? $oData->event : array());





        $aRoutesUID = $this->createRouteRows( $oData->routes );



        $this->createProcessPropertiesFromData($oData);



//        $this->createLaneRows( $oData->lanes );

//

//

//        if (isset( $oData->gateways )) {

//            $this->createGatewayRows( $oData->gateways );

//        }

//        $this->createDynaformRows( $oData->dynaforms );

//        $this->createInputRows( $oData->inputs );

//        $this->createOutputRows( $oData->outputs );

//        $this->createStepRows( $oData->steps );

//        $this->createStepSupervisorRows( isset( $oData->stepSupervisor ) ? $oData->stepSupervisor : array () );

//        $this->createTriggerRows( $oData->triggers );

//        $this->createStepTriggerRows( $oData->steptriggers );

//        $this->createTaskUserRows( $oData->taskusers );

//        $this->createGroupRow( $oData->groupwfs );

//        $this->createDBConnectionsRows( isset( $oData->dbconnections ) ? $oData->dbconnections : array () );

//        $this->createReportTables( isset( $oData->reportTables ) ? $oData->reportTables : array (), isset( $oData->reportTablesVars ) ? $oData->reportTablesVars : array () );

//        $this->createSubProcessRows( isset( $oData->subProcess ) ? $oData->subProcess : array () );

//        $this->createCaseTrackerRows( isset( $oData->caseTracker ) ? $oData->caseTracker : array () );

//        $this->createCaseTrackerObjectRows( isset( $oData->caseTrackerObject ) ? $oData->caseTrackerObject : array () );

//        $this->createObjectPermissionsRows( isset( $oData->objectPermissions ) ? $oData->objectPermissions : array () );

//        $this->createStageRows( isset( $oData->stage ) ? $oData->stage : array () );

//

//        $this->createFieldCondition( isset( $oData->fieldCondition ) ? $oData->fieldCondition : array (), $oData->dynaforms );

//

//        // Create before to createRouteRows for avoid duplicates

//        $this->createEventRows( isset( $oData->event ) ? $oData->event : array () );

//

//        $this->createCaseSchedulerRows( isset( $oData->caseScheduler ) ? $oData->caseScheduler : array () );

//

//        //Create data related to Configuration table

//        $this->createTaskExtraPropertiesRows( isset( $oData->taskExtraProperties ) ? $oData->taskExtraProperties : array () );



        // and finally create the files, dynaforms (xml and html), emailTemplates and Public files

        $this->createFiles( $oData, $pmFilename );

    }



    public function createProcessPropertiesFromData ($oData)

    {

        $arrayProcessData = $oData->process;



        // (*) Creating process dependencies

        // creating the process category

        $this->createProcessCategoryRow( isset( $oData->processCategory ) ? $oData->processCategory : null );



        $this->createLaneRows( $oData->lanes );





        if (isset( $oData->gateways )) {

            $this->createGatewayRows( $oData->gateways );

        }

        $this->createDynaformRows( $oData->dynaforms );

        $this->createInputRows( $oData->inputs );

        $this->createOutputRows( $oData->outputs );

        $this->createStepRows( $oData->steps );

        $this->createStepSupervisorRows( isset( $oData->stepSupervisor ) ? $oData->stepSupervisor : array () );

        $this->createTriggerRows( $oData->triggers );

        $this->createStepTriggerRows( $oData->steptriggers );

        $this->createTaskUserRows( $oData->taskusers );

        $this->createGroupRow( $oData->groupwfs );

        $this->createDBConnectionsRows( isset( $oData->dbconnections ) ? $oData->dbconnections : array () );

        $this->createReportTables( isset( $oData->reportTables ) ? $oData->reportTables : array (), isset( $oData->reportTablesVars ) ? $oData->reportTablesVars : array () );

        $this->createSubProcessRows( isset( $oData->subProcess ) ? $oData->subProcess : array () );

        $this->createCaseTrackerRows( isset( $oData->caseTracker ) ? $oData->caseTracker : array () );

        $this->createCaseTrackerObjectRows( isset( $oData->caseTrackerObject ) ? $oData->caseTrackerObject : array () );

        $this->createObjectPermissionsRows( isset( $oData->objectPermissions ) ? $oData->objectPermissions : array () );

        $this->createStageRows( isset( $oData->stage ) ? $oData->stage : array () );



        $this->createFieldCondition( isset( $oData->fieldCondition ) ? $oData->fieldCondition : array (), $oData->dynaforms );



        // Create before to createRouteRows for avoid duplicates

        $this->createEventRows( isset( $oData->event ) ? $oData->event : array () );



        $this->createCaseSchedulerRows( isset( $oData->caseScheduler ) ? $oData->caseScheduler : array () );



        //Create data related to Configuration table

        $this->createTaskExtraPropertiesRows( isset( $oData->taskExtraProperties ) ? $oData->taskExtraProperties : array () );



        $this->createProcessUser((isset($oData->processUser))? $oData->processUser : array());

        $this->createProcessVariables((isset($oData->processVariables))? $oData->processVariables : array());

        $this->createWebEntry($arrayProcessData["PRO_UID"], $arrayProcessData["PRO_CREATE_USER"], (isset($oData->webEntry))? $oData->webEntry : array());

        $this->createWebEntryEvent($arrayProcessData["PRO_UID"], $arrayProcessData["PRO_CREATE_USER"], (isset($oData->webEntryEvent))? $oData->webEntryEvent : array());

        $this->createMessageType((isset($oData->messageType))? $oData->messageType : array());

        $this->createMessageTypeVariable((isset($oData->messageTypeVariable))? $oData->messageTypeVariable : array());

        $this->createMessageEventDefinition($arrayProcessData["PRO_UID"], (isset($oData->messageEventDefinition))? $oData->messageEventDefinition : array());

        $this->createScriptTask($arrayProcessData["PRO_UID"], (isset($oData->scriptTask))? $oData->scriptTask : array());

        $this->createTimerEvent($arrayProcessData["PRO_UID"], (isset($oData->timerEvent))? $oData->timerEvent : array());

        $this->createEmailEvent($arrayProcessData["PRO_UID"], (isset($oData->emailEvent))? $oData->emailEvent : array());

        $this->createActionsByEmail($arrayProcessData["PRO_UID"], (isset($oData->abeConfiguration))? $oData->abeConfiguration : array());

        $this->createFilesManager($arrayProcessData["PRO_UID"], (isset($oData->filesManager))? $oData->filesManager : array());

    }



    /**

     * this function creates a new Process, defined in the object $oData

     *

     * @param string $sProUid

     * @return boolean

     */

    public function updateProcessFromData ($oData, $pmFilename)

    {

        $this->updateProcessRow( $oData->process );

        $this->removeProcessRows( $oData->process['PRO_UID'] );

        $this->removeAllFieldCondition($oData->dynaforms );

        $this->createTaskRows( $oData->tasks );

        $this->createRouteRows( $oData->routes );

        $this->createLaneRows( $oData->lanes );

        $this->createDynaformRows( $oData->dynaforms );

        $this->createInputRows( $oData->inputs );

        $this->createOutputRows( $oData->outputs );

        $this->createStepRows( $oData->steps );

        $this->createStepSupervisorRows( $oData->stepSupervisor );

        $this->createTriggerRows( $oData->triggers );

        $this->createStepTriggerRows( $oData->steptriggers );

        $this->createTaskUserRows( $oData->taskusers );

        $this->createGroupRow( $oData->groupwfs );

        $this->createDBConnectionsRows( $oData->dbconnections );

        $this->updateReportTables( $oData->reportTables, $oData->reportTablesVars );

        $this->createFiles( $oData, $pmFilename );

        $this->createSubProcessRows( $oData->subProcess );

        $this->createCaseTrackerRows( $oData->caseTracker );

        $this->createCaseTrackerObjectRows( $oData->caseTrackerObject );

        $this->createObjectPermissionsRows( $oData->objectPermissions );

        $this->createStageRows( $oData->stage );

        $this->createFieldCondition( $oData->fieldCondition, $oData->dynaforms );

        $this->createEventRows( $oData->event );

        $this->createCaseSchedulerRows( $oData->caseScheduler );

        $this->createProcessCategoryRow( isset( $oData->processCategory ) ? $oData->processCategory : null );

        $this->createTaskExtraPropertiesRows( isset( $oData->taskExtraProperties ) ? $oData->taskExtraProperties : array () );

    }



    /**

     * get the starting task for a user but from a Tasks object

     *

     * @param $sProUid process uid

     * @param $sUserUid user uid

     * @return an array of tasks

     */

    public function getStartingTaskForUser ($sProUid, $sUsrUid)

    {

        $oTask = new Tasks();

        return $oTask->getStartingTaskForUser( $sProUid, $sUsrUid );

    }



    /**

     * ***********************************************

     * functions to enable open ProcessMaker Library

     * ***********************************************

     */

    /**

     * Open a WebService connection

     *

     * @param $user username for pm

     * @param $pass password for the user

     * @return 1 integer.

     */

    public function ws_open ($user, $pass)

    {

        global $sessionId;

        global $client;

        $endpoint = PML_WSDL_URL;

        $sessionId = '';

        $proxy = array ();

        $sysConf = System::getSystemConfiguration();

        if ($sysConf['proxy_host'] != '') {

            $proxy['proxy_host'] = $sysConf['proxy_host'];

            if ($sysConf['proxy_port'] != '') {

                $proxy['proxy_port'] = $sysConf['proxy_port'];

            }

            if ($sysConf['proxy_user'] != '') {

                $proxy['proxy_login'] = $sysConf['proxy_user'];

            }

            if ($sysConf['proxy_pass'] != '') {

                $proxy['proxy_password'] = $sysConf['proxy_pass'];

            }

        }

        $client = new SoapClient( $endpoint, $proxy );

        $params = array ('userid' => $user,'password' => $pass

        );

        $result = $client->__SoapCall( 'login', array ($params

        ) );

        if ($result->status_code == 0) {

            $sessionId = $result->message;

            return 1;

        }

        throw (new Exception( $result->message ));

        return 1;

    }



    /**

     * Open a WebService public connection

     *

     * @param $user username for pm

     * @param $pass password for the user

     * @return 1 integer.

     */

    public function ws_open_public ()

    {

        global $sessionId;

        global $client;

        $endpoint = PML_WSDL_URL;

        $sessionId = '';

        ini_set( "soap.wsdl_cache_enabled", "0" ); // enabling WSDL cache

        try {

            $proxy = array ();

            $sysConf = System::getSystemConfiguration();

            if ($sysConf['proxy_host'] != '') {

                $proxy['proxy_host'] = $sysConf['proxy_host'];

                if ($sysConf['proxy_port'] != '') {

                    $proxy['proxy_port'] = $sysConf['proxy_port'];

                }

                if ($sysConf['proxy_user'] != '') {

                    $proxy['proxy_login'] = $sysConf['proxy_user'];

                }

                if ($sysConf['proxy_pass'] != '') {

                    $proxy['proxy_password'] = $sysConf['proxy_pass'];

                }

            }

            $client = @new SoapClient( $endpoint, $proxy );

        } catch (Exception $e) {

            throw (new Exception( $e->getMessage() ));

        }

        return 1;

    }



    /**

     * Consume the processList WebService

     *

     * @return $result process list.

     */

    public function ws_processList ()

    {

        global $sessionId;

        global $client;



        $endpoint = PML_WSDL_URL;

        $proxy = array ();

        $sysConf = System::getSystemConfiguration();



        if ($sysConf['proxy_host'] != '') {

            $proxy['proxy_host'] = $sysConf['proxy_host'];

            if ($sysConf['proxy_port'] != '') {

                $proxy['proxy_port'] = $sysConf['proxy_port'];

            }

            if ($sysConf['proxy_user'] != '') {

                $proxy['proxy_login'] = $sysConf['proxy_user'];

            }

            if ($sysConf['proxy_pass'] != '') {

                $proxy['proxy_password'] = $sysConf['proxy_pass'];

            }

        }



        $client = new SoapClient( $endpoint, $proxy );

        $sessionId = '';

        $params = array ('sessionId' => $sessionId

        );

        $result = $client->__SoapCall( 'processList', array ($params

        ) );

        if ($result->status_code == 0) {

            return $result;

        }

        throw (new Exception( $result->message ));

    }



    /**

     * download a File

     *

     * @param $file file to download

     * @param $local_path path of the file

     * @param $newfilename

     * @return $errorMsg process list.

     */

    public function downloadFile ($file, $local_path, $newfilename)

    {

        $err_msg = '';

        $out = fopen( $local_path . $newfilename, 'wb' );

        if ($out == false) {

            throw (new Exception( "File $newfilename not opened" ));

        }



        if (! function_exists( 'curl_init' )) {

            G::SendTemporalMessage( 'ID_CURLFUN_ISUNDEFINED', "warning", 'LABEL', '', '100%', '' );

            G::header( 'location: ../processes/processes_Library' );

            die();

        }

        $ch = curl_init();



        curl_setopt( $ch, CURLOPT_FILE, $out );

        curl_setopt( $ch, CURLOPT_HEADER, 0 );

        curl_setopt( $ch, CURLOPT_URL, $file );



        curl_exec( $ch );

        $errorMsg = curl_error( $ch );

        fclose( $out );



        curl_close( $ch );

        return $errorMsg;



    } //end function





    /**

     * get the process Data from a process

     *

     * @param $proId process Uid

     * @return $result

     */

    public function ws_processGetData ($proId)

    {

        global $sessionId;

        global $client;



        $endpoint = PML_WSDL_URL;

        $proxy = array ();

        $sysConf = System::getSystemConfiguration();

        if ($sysConf['proxy_host'] != '') {

            $proxy['proxy_host'] = $sysConf['proxy_host'];

            if ($sysConf['proxy_port'] != '') {

                $proxy['proxy_port'] = $sysConf['proxy_port'];

            }

            if ($sysConf['proxy_user'] != '') {

                $proxy['proxy_login'] = $sysConf['proxy_user'];

            }

            if ($sysConf['proxy_pass'] != '') {

                $proxy['proxy_password'] = $sysConf['proxy_pass'];

            }

        }

        $client = new SoapClient( $endpoint, $proxy );



        $sessionId = '';

        $params = array ('sessionId' => $sessionId,'processId' => $proId

        );

        $result = $client->__SoapCall( 'processGetData', array ($params

        ) );

        if ($result->status_code == 0) {

            return $result;

        }

        throw (new Exception( $result->message ));

    }



    /**

     * parse an array of Items

     *

     * @param $proId process Uid

     * @return $result

     */

    public function parseItemArray ($array)

    {

        if (! isset( $array->item ) && ! is_array( $array )) {

            return null;

        }



        $result = array ();

        if (isset( $array->item )) {

            foreach ($array->item as $key => $value) {

                $result[$value->key] = $value->value;

            }

        } else {

            foreach ($array as $key => $value) {

                $result[$value->key] = $value->value;

            }

        }

        return $result;

    }



    public function getProcessFiles ($proUid, $type)

    {

        $filesList = array ();



        switch ($type) {

            case "mail":

            case "email":

                $basePath = PATH_DATA_MAILTEMPLATES;

                break;

            case "public":

                $basePath = PATH_DATA_PUBLIC;

                break;

            default:

                throw new Exception( "Unknow Process Files Type \"$type\"." );

                break;

        }



        $dir = $basePath . $proUid . PATH_SEP;



        G::verifyPath( $dir, true ); //Create if it does not exist





        //Creating the default template (if not exists)

        if (! file_exists( $dir . "alert_message.html" )) {

            @copy( PATH_TPL . "mails" . PATH_SEP . "alert_message.html", $dir . "alert_message.html" );

        }



        if ( (!file_exists( $dir . "unassignedMessage.html")) && file_exists($dir . G::LoadTranslation('ID_UNASSIGNED_MESSAGE'))) {

            if (defined('PARTNER_FLAG')) {

                @copy( PATH_TPL . "mails" . PATH_SEP . "unassignedMessagePartner.html", $dir . G::LoadTranslation('ID_UNASSIGNED_MESSAGE'));

            } else {

                @copy( PATH_TPL . "mails" . PATH_SEP . "unassignedMessage.html", $dir . G::LoadTranslation('ID_UNASSIGNED_MESSAGE'));

            }

        }



        $files = glob( $dir . "*.*" );



        foreach ($files as $file) {

            $fileName = basename( $file );



            if ($fileName != "alert_message.html" && $fileName != G::LoadTranslation('ID_UNASSIGNED_MESSAGE')) {

                $filesList[] = array ("filepath" => $file,"filename" => $fileName);

            }

        }

        return $filesList;

    }



    /**

    * get rows related to Task extra properties of the process seleceted

    *

    * @param $proId process Uid

    * @return $result

    */

    public function getTaskExtraPropertiesRows($proId)

    {

        try {



            $oCriteria = new Criteria('workflow');

            $oCriteria->addSelectColumn( ConfigurationPeer::CFG_UID );

            $oCriteria->addSelectColumn( ConfigurationPeer::OBJ_UID );

            $oCriteria->addSelectColumn( ConfigurationPeer::CFG_VALUE );

            $oCriteria->addSelectColumn( ConfigurationPeer::PRO_UID );

            $oCriteria->addSelectColumn( ConfigurationPeer::USR_UID );

            $oCriteria->addSelectColumn( ConfigurationPeer::APP_UID );

            $oCriteria->add( TaskPeer::PRO_UID, $proId );

            $oCriteria->add( ConfigurationPeer::CFG_UID, 'TAS_EXTRA_PROPERTIES' );

            $oCriteria->addJoin( ConfigurationPeer::OBJ_UID, TaskPeer::TAS_UID );

            $oDataset = ConfigurationPeer::doSelectRS( $oCriteria );

            $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );

            $oDataset->next();



            $aConfRows = array();

            while ($aRow = $oDataset->getRow()) {

                $aConfRows[] = $aRow;

                $oDataset->next();

            }



            return $aConfRows;



        } catch (Exception $oError) {

            throw ($oError);

        }

    }



    /**

     * Get disabled code

     *

     * @param string $processUid Unique id of Process

     *

     * return array Return array with disabled code found, array empty otherwise

     */

    public function getDisabledCode($processUid = "")

    {

        try {

            /*----------------------------------********---------------------------------*/



            $arrayDisabledCode = array();



            /*----------------------------------********---------------------------------*/



            //Return

            return $arrayDisabledCode;

        } catch (Exception $e) {

            throw $e;

        }

    }



    public function truncateName($proTitle)

    {

        $proTitle = str_replace(".","_",$proTitle);

        $limit = 200;

        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {

            $limit = 150;

        }

        $excess = strlen($proTitle) - $limit;

        $proTitle = substr($proTitle,0,strlen($proTitle)-$excess);

        return $proTitle;

    }

}

//end class processes





/**

 * Object Document class

 *

 * @package workflow.engine.ProcessMaker

 */

class ObjectDocument

{

    public $type;

    public $name;

    public $data;

    public $origin;



    /**

     * Constructor

     */

    public function __construct ()

    {

        $this->type = '';

        $this->name = '';

        $this->data = '';

        $this->origin = '';

    }

}



/**

 * ObjectDocument Collection

 *

 * @package workflow.engine.ProcessMaker

 */

class ObjectCellection

{

    public $num;

    public $swapc;

    public $objects;



    /**

     * Constructor

     */

    public function __construct ()

    {

        $this->objects = Array ();

        $this->num = 0;

        $this->swapc = $this->num;

        array_push( $this->objects, 'void' );

    }



    /**

     * add in the collecetion a new object Document

     *

     * @param $name name object document

     * @param $type type object document

     * @param $data data object document

     * @param $origin origin object document

     * @return void

     */

    public function add ($name, $type, $data, $origin)

    {

        $o = new ObjectDocument();

        $o->name = $name;

        $o->type = $type;

        $o->data = $data;

        $o->origin = $origin;



        $this->num ++;

        array_push( $this->objects, $o );

        $this->swapc = $this->num;

    }



    /**

     * get the collection of ObjectDocument

     *

     * @param $name name object document

     * @param $type type object document

     * @param $data data object document

     * @param $origin origin object document

     * @return void

     */

    public function get ()

    {

        if ($this->swapc > 0) {

            $e = $this->objects[$this->swapc];

            $this->swapc --;

            return $e;

        } else {

            $this->swapc = $this->num;

            return false;

        }

    }

}


