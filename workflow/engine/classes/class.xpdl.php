<?php
/**
 * class.xpdl.php
 *
 * @package workflow.engine.classes
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
 */

G::LoadClass( 'processes' );
G::LoadClass( 'tasks' );
G::LoadClass( 'derivation' );
require_once 'classes/model/Users.php';
require_once 'classes/model/Configuration.php';
require_once 'classes/model/Gateway.php';
require_once 'classes/model/Event.php';

/**
 *
 * @package workflow.engine.classes
 */
class Xpdl extends processes
{

    /**
     * This public function creates a new Process, defined in the object $oData
     *
     * @param array $aData
     * @param array $tasks
     * @param string $pmFilename
     * @return void
     */
    public function createProcessFromDataXpdl ($oData, $tasks)
    {
        if (! isset( $oData->process['PRO_UID'] ) || trim( $oData->process['PRO_UID'] ) == '') {
            $oData->process['PRO_UID'] = G::generateUniqueID();
        }
        $this->removeProcessRows( $oData->process['PRO_UID'] );
        $uid = $this->createProcessRow( $oData->process );
        $this->createTaskRows( $oData->tasks );
        $newTasks = $this->verifyTasks( $uid, $tasks );
        if (! isset( $oData->routes )) {
            $oData->routes = array ();
        }
        $this->createRouteRowsXpdl( $uid, $oData->routes, $newTasks );
        $this->createLaneRows( $oData->lanes );
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
        $this->createSubProcessRowsXpdl( $uid, isset( $oData->subProcess ) ? $oData->subProcess : array (), $newTasks );
        $this->createCaseTrackerRows( isset( $oData->caseTracker ) ? $oData->caseTracker : array () );
        $this->createCaseTrackerObjectRows( isset( $oData->caseTrackerObject ) ? $oData->caseTrackerObject : array () );
        $this->createObjectPermissionsRows( isset( $oData->objectPermissions ) ? $oData->objectPermissions : array () );
        $this->createStageRows( isset( $oData->stage ) ? $oData->stage : array () );
        $this->createFieldCondition( isset( $oData->fieldCondition ) ? $oData->fieldCondition : array (), $oData->dynaforms );
        $this->createEventRows( isset( $oData->event ) ? $oData->event : array () );
        $this->createCaseSchedulerRows( isset( $oData->caseScheduler ) ? $oData->caseScheduler : array () );
    }

    /**
     * this public function creates a new Process, defined in the object $oData
     *
     * @param string $sProUid
     * @return boolean
     */
    public function updateProcessFromDataXpdl ($oData, $tasks)
    {
        if (is_array( $oData->process )) {
            $this->updateProcessRow( $oData->process );
        }
        $this->removeProcessRows( $oData->process['PRO_UID'] );
        $uid = $this->createProcessRow( $oData->process );
        $this->createTaskRows( $oData->tasks );
        $newTasks = $this->verifyTasks( $uid, $tasks );
        $this->createRouteRowsXpdl( $uid, $oData->routes, $newTasks );
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
        $this->createSubProcessRows( $oData->subProcess );
        $this->createCaseTrackerRows( $oData->caseTracker );
        $this->createCaseTrackerObjectRows( $oData->caseTrackerObject );
        $this->createObjectPermissionsRows( $oData->objectPermissions );
        $this->createStageRows( $oData->stage );
        $this->createFieldCondition( $oData->fieldCondition, $oData->dynaforms );
        $this->createEventRows( $oData->event );
        $this->createCaseSchedulerRows( $oData->caseScheduler );

    }

    /**
     * this public function creates a new Process, defined in the object $oData
     *
     * @param string $sProUid
     * @return boolean
     */
    public function createProcessFromDataPmxml ($oData)
    {
        $this->removeProcessRows( $oData->process['PRO_UID'] );
        $this->createProcessRow( $oData->process );
        $this->createDynaformRows( $oData->dynaforms );
    }

    /**
     * This public function update the dynaforms
     *
     * @param string $sProUid
     * @param array $fields
     * @return boolean
     */
    // public function updateDynaformsPmxml($uid,$fields)
    // {
    //   $oData->dynaforms = $this->getDynaformRows ( $uid);
    //   $count = sizeof($oData->dynaforms);
    //   foreach ($fields as $val => $id){
    //     $oData->dynaforms[$count]= $id;
    //     $count = $count + 1 ;
    //   }
    //   $this->createDynaformRows ($oData->dynaforms );
    // }


    /**
     * This public function create the subProcess from data
     *
     * @param array $oData
     * @param array $tasks
     * @return void
     */
    public function createSubProcessFromDataXpdl ($oData, $tasks)
    {
        $this->removeProcessRows( $oData->process['PRO_UID'] );
        $uid = $this->createProcessRow( $oData->process );
        $this->createTaskRows( $oData->tasks );
        $newTasks = $this->verifyTasks( $uid, $tasks );
        $this->createRouteRowsXpdl( $uid, $oData->routes, $newTasks );
        $this->createLaneRows( $oData->lanes );
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
        $this->createSubProcessRowsXpdl( $uid, isset( $oData->subProcess ) ? $oData->subProcess : array (), $newTasks );
        $this->createCaseTrackerRows( isset( $oData->caseTracker ) ? $oData->caseTracker : array () );
        $this->createCaseTrackerObjectRows( isset( $oData->caseTrackerObject ) ? $oData->caseTrackerObject : array () );
        $this->createObjectPermissionsRows( isset( $oData->objectPermissions ) ? $oData->objectPermissions : array () );
        $this->createStageRows( isset( $oData->stage ) ? $oData->stage : array () );
        $this->createFieldCondition( isset( $oData->fieldCondition ) ? $oData->fieldCondition : array (), $oData->dynaforms );
        $this->createEventRows( isset( $oData->event ) ? $oData->event : array () );
        $this->createCaseSchedulerRows( isset( $oData->caseScheduler ) ? $oData->caseScheduler : array () );
    }

    /**
     * This public function verify the tasks of a process that was created with the tasks they were sent to the process
     *
     * @param string $oProUid
     * @param array $fieldsTasks
     * @return array
     */
    public function verifyTasks ($sProUid, $fieldsTasks)
    {
        $process = new Process();
        $oData->tasks = $this->getTaskRows( $sProUid );
        $findTask = 0;
        $findNext = 0;
        foreach ($fieldsTasks as $taskVal => $idVal) {
            foreach ($oData->tasks as $task => $id) {
                if ($idVal['TAS_TITLE'] == $id['TAS_TITLE'] and $idVal['TAS_POSX'] == $id['TAS_POSX'] and $idVal['TAS_POSY'] == $id['TAS_POSY']) {
                    if (isset( $idVal['TAS_DESCRIPTION'] ) and isset( $id['TAS_DESCRIPTION'] )) {
                        if ($idVal['TAS_DESCRIPTION'] == $id['TAS_DESCRIPTION']) {
                            $fieldsTasks[$taskVal]['TAS_UID_DATA'] = $id['TAS_UID'];
                        }
                    }
                    $fieldsTasks[$taskVal]['TAS_UID_DATA'] = $id['TAS_UID'];
                }
            }
        }
        return $fieldsTasks;
    }

    /**
     * This public function creates the rows of the process
     *
     * @param string $sProUid
     * @param array $routes
     * @param array $fieldsTasks
     * @return void
     */
    public function createRouteRowsXpdl ($sProUid, $routes, $fieldsTasks)
    {
        $process = new Process();
        $oData->tasks = $this->getTaskRows( $sProUid );
        foreach ($routes as $taskRoute => $idRoute) {
            $findTask = 0;
            $findNext = 0;
            foreach ($oData->tasks as $task => $id) {
                if ($idRoute['TAS_UID'] == $id['TAS_UID']) {
                    $findTask = 1;
                }
                if ($idRoute['ROU_NEXT_TASK'] != '-1') {
                    if ($idRoute['ROU_NEXT_TASK'] == $id['TAS_UID']) {
                        $findNext = 1;
                    }
                } else {
                    $findNext = 1;
                }
            }
            if ($findTask == 0) {
                $id = $this->findIdTask( $idRoute['TAS_UID'], $fieldsTasks );
                $routes[$taskRoute]['TAS_UID'] = $id;
            }
            if ($findNext == 0) {
                $id = $this->findIdTask( $idRoute['ROU_NEXT_TASK'], $fieldsTasks );
                $routes[$taskRoute]['ROU_NEXT_TASK'] = $id;
            }
        }
        $this->createRouteRows( $routes );
    }

    /**
     * Create Sub Process rows from an array, removing those subprocesses with
     * the same UID.
     *
     * @param $SubProcess array
     * @return void.
     */
    public function createSubProcessRowsXpdl ($sProUid, $SubProcess, $tasks)
    {
        $process = new Process();
        $oData->tasks = $this->getTaskRows( $sProUid );
        foreach ($SubProcess as $key => $row) {
            $findTask = 0;
            foreach ($oData->tasks as $task => $id) {
                if ($row['TAS_PARENT'] == $id['TAS_UID']) {
                    $findTask = 1;
                }
            }
            if ($findTask == 0) {
                $id = $this->findIdTask( $row['TAS_PARENT'], $tasks );
                $SubProcess[$key]['TAS_PARENT'] = $id;
            }
            $oSubProcess = new SubProcess();
            if ($oSubProcess->subProcessExists( $row['SP_UID'] )) {
                $oSubProcess->remove( $row['SP_UID'] );
            }
            $res = $oSubProcess->create( $row );
        }
        return;
    }

    /**
     * this public function find the id of the task
     *
     * @param string $idTask
     * @param array $routes
     * @return array
     */
    public function findIdTask ($idTask, $routes)
    {
        foreach ($routes as $value => $id) {
            if ($id['TAS_UID'] == $idTask) {
                return $id['TAS_UID_DATA'];
            }
        }
    }

    /**
     * This public function create the file .
     *
     * xpdl from a process
     *
     * @param string $sProUid
     * @return void
     */
    public function xmdlProcess ($sProUid = '')
    {
        $oProcess = new Process();
        $oData->process = $this->getProcessRow( $sProUid, false );
        $oData->tasks = $this->getTaskRows( $sProUid );
        $oData->routes = $this->getRouteRows( $sProUid );
        $oData->lanes = $this->getLaneRows( $sProUid );
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
        $oData->objectPermissions = $this->getObjectPermissionRows( $sProUid );
        $oData->subProcess = $this->getSubProcessRow( $sProUid );
        $oData->caseTracker = $this->getCaseTrackerRow( $sProUid );
        $oData->caseTrackerObject = $this->getCaseTrackerObjectRow( $sProUid );
        $oData->stage = $this->getStageRow( $sProUid );
        $oData->fieldCondition = $this->getFieldCondition( $sProUid );
        $oData->event = $this->getEventRow( $sProUid );
        $oData->caseScheduler = $this->getCaseSchedulerRow( $sProUid );
        $path = PATH_DOCUMENT . 'output' . PATH_SEP;
        if (! is_dir( $path )) {
            G::verifyPath( $path, true );
        }
        $proTitle = (substr( G::inflect( $oData->process['PRO_TITLE'] ), 0, 245 ));
        $proTitle = preg_replace( "/[^A-Za-z0-9_]/", "", $proTitle );
        //Calculating the maximum length of file name
        $pathLength = strlen( PATH_DATA . "sites" . PATH_SEP . SYS_SYS . PATH_SEP . "files" . PATH_SEP . "output" . PATH_SEP );
        $length = strlen( $proTitle ) + $pathLength;
        if ($length >= 250) {
            $proTitle = myTruncate( $proTitle, 250 - $pathLength, '_', '' );
        }
        $index = '';
        $lastIndex = '';
        do {
            $filename = $path . $proTitle . $index . '.xpdl';
            $lastIndex = $index;
            if ($index == '') {
                $index = 1;
            } else {
                $index ++;
            }
        } while (file_exists( $filename ));
        $proTitle .= $lastIndex;
        $filenameOnly = $proTitle . '.xpdl';
        $xml = fopen( $filename . 'tpm', "wb" );
        $process = $oData->process;
        $coordinateMaximumY = 0;
        $coordinateMaximumX = 0;
        $lanes = $oData->lanes;
        foreach ($lanes as $keyLane => $valLane) {
            if ($valLane['SWI_TYPE'] == "TEXT") {
                $textLane = $valLane['SWI_TEXT'];
                $longText = strlen( $textLane );
                $height = 0;
                $width = 0;
                if ($longText < 40) {
                    $height = 20;
                    $width = ($longText * 10) + 10;
                }
                if ($longText > 40) {
                    $numberRows = $longText / 40;
                    $height = $numberRows * 20;
                    $width = 250;
                }
                $coordinateX = $valLane['SWI_X'] + $width;
                $coordinateY = $valLane['SWI_Y'] + $height;
                if ($coordinateX > $coordinateMaximumX) {
                    $coordinateMaximumX = $coordinateX;
                }
                if ($coordinateY > $coordinateMaximumY) {
                    $coordinateMaximumY = $coordinateY;
                }
            }
        }
        foreach ($oData->tasks as $keyLane => $val) {
            $coordinateX = $val['TAS_POSX'] + 160;
            $coordinateY = $val['TAS_POSY'] + 38;
            if ($coordinateX > $coordinateMaximumX) {
                $coordinateMaximumX = $coordinateX;
            }
            if ($coordinateY > $coordinateMaximumY) {
                $coordinateMaximumY = $coordinateY;
            }
        }
        $data = $this->createPool( $process, $coordinateMaximumX + 60, $coordinateMaximumY + 60 );
        fwrite( $xml, '<?xml version="1.0" encoding="utf-8"?>' );
        fwrite( $xml, $data );
        $artifacts = '<Artifacts>';
        $artifacts .= $this->createArtifacts( $lanes, '0' );
        $dataProcess = '
   <WorkflowProcesses>';
        $dataProcess .= '
      <WorkflowProcess Id= "' . $process['PRO_UID'] . '" ' . 'Name="' . $process['PRO_TITLE'] . '">
      <RedefinableHeader>
      </RedefinableHeader>
      ';
        $activitySets = '<ActivitySets>';
        $subProcess = $oData->subProcess;
        $subProcesses = $this->createSubProcessesXpdl( $subProcess, $oData->tasks );
        $activitySets .= $subProcesses['ACTIVITIES'];
        $activitySets .= '</ActivitySets>';
        $artifacts .= $subProcesses['ARTIFACTS'];
        $artifacts .= '
  </Artifacts>';
        fwrite( $xml, $artifacts );
        fwrite( $xml, $dataProcess );
        fwrite( $xml, $activitySets );
        // Here are generated activities of a file. XPDL
        // for this use the process tasks
        $tasks = $oData->tasks;
        $events = $oData->event;
        $scheduler = $oData->caseScheduler;
        $dataTasks = $this->createActivitiesXpdl( $tasks, $events, $scheduler );
        fwrite( $xml, $dataTasks['ACTIVITIES'] );
        $taskHidden = $dataTasks['TASK_HIDDEN'];
        $routes = $oData->routes;
        $dataRoutes = $this->createTransitionsXpdl( $routes, $tasks, $taskHidden );
        fwrite( $xml, $dataRoutes['ACTIVITIES'] );
        $data = '
      </Activities>';
        fwrite( $xml, $data );
        fwrite( $xml, $dataTasks['TRANSITION'] );
        fwrite( $xml, $dataRoutes['TRANSITION'] );
        $data = '
         </Transitions>
        <ExtendedAttributes />
      </WorkflowProcess>
    </WorkflowProcesses>
    <ExtendedAttributes />
</Package>';
        fwrite( $xml, $data );
        fclose( $xml );
        $filenameLink = 'processes_DownloadFileXpdl?p=' . $proTitle . '&r=' . rand( 100, 1000 );
        $result['FILENAMEXPDL'] = $proTitle . '.xpdl';
        $result['FILENAME_LINKXPDL'] = $filenameLink;
        return $result;
    }

    /**
     * This public function create the pool from the process
     *
     * @param array $process
     * @param string $coordinateMaximumX
     * @param string $coordinateMaximumY
     * @return string
     */
    public function createPool ($process, $coordinateMaximumX, $coordinateMaximumY)
    {
        $data = '';
        $data .= '
<Package xmlns="http://www.wfmc.org/2008/XPDL2.1" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" ' . 'xmlns:xsd="http://www.w3.org/2001/XMLSchema"' . ' Id = "' . $process['PRO_UID'] . '" Name ="' . $process['PRO_UID'] . '" OnlyOneProcess="false">
  <PackageHeader>
    <XPDLVersion>2.1</XPDLVersion>
    <Vendor>Process Maker</Vendor>
    <Created>' . $process['PRO_CREATE_DATE'] . '</Created>
    <Description>' . $process['PRO_DESCRIPTION'] . '</Description>
    <Documentation />
  </PackageHeader>
  <RedefinableHeader>
    <Author></Author>
    <Version />
    <Countrykey>CO</Countrykey>
  </RedefinableHeader>
  <ExternalPackages />
  <Participants />
  <Pools>
    <Pool Id="Pool' . $process['PRO_UID'] . '" BoundaryVisible="true" Name="' . $process['PRO_TITLE'] . '" Process="' . $process['PRO_UID'] . '" >
     <Lanes>
     </Lanes>
     <NodeGraphicsInfos>
       <NodeGraphicsInfo ToolId="processmaker" Height="' . $coordinateMaximumY . '" Width="' . $coordinateMaximumX . '" BorderColor="-16777216" FillColor="-1">
         <Coordinates XCoordinate="30" YCoordinate="30">
         </Coordinates>
       </NodeGraphicsInfo>
     </NodeGraphicsInfos>
  </Pool>
  </Pools>
  <MessageFlows />
  <Associations />
  ';
        return $data;
    }

    /**
     * This public function generated the Artifacts of a file.
     * XPDL
     *
     * @param array $lanes
     * @param string $id
     * @return string
     */
    public function createArtifacts ($lanes, $id)
    {
        $artifacts = '';
        $idTask = '';
        foreach ($lanes as $keyLane => $valLane) {
            if ($valLane['SWI_TYPE'] == "TEXT") {
                $idLane = $valLane['SWI_UID'];
                $textLane = $valLane['SWI_TEXT'];
                $coordinateX = $valLane['SWI_X'];
                $coordinateY = $valLane['SWI_Y'];
                $longText = strlen( $textLane );
                if ($longText < 40) {
                    $height = 20;
                    $width = ($longText * 10) + 10;
                }
                if ($longText > 40) {
                    $numberRows = $longText / 40;
                    $height = $numberRows * 20;
                    $width = 250;
                }
                if ($id != 0) {
                    $idTask = ' ActivitySetId="' . $id . '"';
                }
                $artifacts .= '
      <Artifact Id="' . $idLane . '" ArtifactType="Annotation" TextAnnotation="' . $textLane . '"' . $idTask . '>
      <NodeGraphicsInfos>
        <NodeGraphicsInfo ToolId="processmaker" Height="' . $height . '" Width="' . $width . '" BorderColor="-2763307" FillColor="-2763307">
          <Coordinates XCoordinate="' . $coordinateX . '" YCoordinate="' . $coordinateY . '" />
        </NodeGraphicsInfo>
      </NodeGraphicsInfos>
      <Documentation />
    </Artifact>';
            }
        }
        return $artifacts;
    }

    /**
     * This public function creates SubProcesses
     *
     * @param array $tasks
     * @param array $subProcess
     * @return array
     */
    public function createSubProcessesXpdl ($subProcess, $tasks)
    {
        $activitySets = '';
        $dataCreated = '';
        $artifacts = '';
        foreach ($subProcess as $key => $row) {
            if ($row['SP_UID'] != '') {
                $idTask = $row['TAS_PARENT'];
                foreach ($tasks as $id => $value) {
                    if ($value['TAS_UID'] == $idTask) {
                        $nameTask = htmlentities( $value['TAS_TITLE'] );
                    }
                }
                $activitySets .= '
        <ActivitySet Id="' . $idTask . '" Name="' . $nameTask . '">';
                if ($row['PRO_UID'] != '' && $row['PRO_UID'] != 0) {
                    $dataSubProcess = $this->serializeProcess( $row['PRO_UID'] );
                    $data = unserialize( $dataSubProcess );
                    $tasks = $data->tasks;
                    $subProcessData = $data->subProcess;
                    $subProcessTask = $data->tasks;
                    $lanes = $data->lanes;
                    $events = $data->event;
                    $scheduler = $data->caseScheduler;
                    $artifacts = $this->createArtifacts( $lanes, $idTask );
                    $dataCreated = $this->createSubProcessesXpdl( $subProcessData, $subProcessTask );
                    $dataTasks = $this->createActivitiesXpdl( $tasks, $events, $scheduler );
                    $activitySets .= $dataTasks['ACTIVITIES'];
                    $taskHidden = $dataTasks['TASK_HIDDEN'];
                    $routes = $data->routes;
                    $dataRoutes = $this->createTransitionsXpdl( $routes, $tasks, $taskHidden );
                    $activitySets .= $dataRoutes['ACTIVITIES'];
                    $activitySets .= '
      </Activities>';
                    $activitySets .= $dataTasks['TRANSITION'];
                    $activitySets .= $dataRoutes['TRANSITION'];
                    $activitySets .= '
      </Transitions>';
                } else {
                    $data = '
        <Activities />
        <Transitions />';
                }
                $activitySets .= '</ActivitySet>';
                $activitySets .= $dataCreated;
            }
        }
        $fields['ACTIVITIES'] = $activitySets;
        $fields['ARTIFACTS'] = $artifacts;
        return $fields;
    }

    /**
     * This public function creates activities from the tasks
     *
     * @param array $tasks
     * @return array
     */
    public function createActivitiesXpdl ($tasks, $events, $scheduler)
    {
        $dataTasks = '
      <Activities>';
        $transitions = '
      <Transitions>';
        $start = 0;
        $implementation = '';
        $taskHidden = array ();
        $description = '';
        foreach ($tasks as $key => $val) {
            $idTask = $val['TAS_UID'];
            $nameTask = $val['TAS_TITLE'];
            $coordinateX = $val['TAS_POSX'];
            $coordinateY = $val['TAS_POSY'];
            if ($val['TAS_TYPE'] == 'NORMAL' or $val['TAS_TYPE'] == 'SUBPROCESS') {
                if ($val['TAS_TYPE'] == 'NORMAL') {
                    $implementation = '
         <Implementation>
            <Task />
          </Implementation>';
                }
                if ($val['TAS_TYPE'] == 'SUBPROCESS') {
                    $implementation = '
         <BlockActivity ActivitySetId="' . $val['TAS_UID'] . '" />';
                }
                if (isset( $val['TAS_DESCRIPTION'] )) {
                    $description = $val['TAS_DESCRIPTION'];
                } else {
                    $description = '';
                }
                $fillColor = $val['TAS_COLOR'];
                if ($val['TAS_START'] == "TRUE") {
                    $start = 1;
                }
                if ($start == 1) {
                    $start = 0;
                    $positionX = $coordinateX + 65;
                    $positionY = $coordinateY - 45;
                    $dataTasks .= '
        <Activity Id="s' . $idTask . '">
          <Description>' . $description . '</Description>
          <Event>
            <StartEvent Trigger="None" />
          </Event>
          <Performers />
          <Documentation />
          <ExtendedAttributes />
          <NodeGraphicsInfos>
            <NodeGraphicsInfo ToolId="Processmaker"' . ' Height="30" Width="30" BorderColor="-10311914" FillColor="' . $fillColor . '">
              <Coordinates XCoordinate="' . $positionX . '" YCoordinate="' . $positionY . '" />
            </NodeGraphicsInfo>
          </NodeGraphicsInfos>
        </Activity>';
                    $transitions .= '
        <Transition Id="x' . $idTask . '" From="s' . $idTask . '" To="' . $idTask . '" Name="">
          <Condition />
          <Description />
          <ExtendedAttributes />
          <ConnectorGraphicsInfos>
            <ConnectorGraphicsInfo ToolId="Processmaker" BorderColor="0" FromPort="2" ToPort="1">
            </ConnectorGraphicsInfo>
          </ConnectorGraphicsInfos>
        </Transition>';
                }
                $dataTasks .= '
        <Activity Id="' . $idTask . '" Name="' . $nameTask . '">
          <Description>' . $description . '</Description>' . $implementation . '
          <Performers />
          <Documentation />
          <ExtendedAttributes />
          <NodeGraphicsInfos>
            <NodeGraphicsInfo ToolId="Processmaker"' . ' Height="38" Width="160" BorderColor="-16553830" FillColor="' . $fillColor . '">
              <Coordinates XCoordinate="' . $coordinateX . '" YCoordinate="' . $coordinateY . '" />
            </NodeGraphicsInfo>
          </NodeGraphicsInfos>
        </Activity>';
            } else {
                if ($val['TAS_TYPE'] == 'HIDDEN') {
                    $taskHidden[] = $tasks[$key];
                }
            }
        }

        $aux = $events;
        foreach ($events as $key => $row) {
            $aux[$key] = $row['TAS_UID'];
        }
        if (sizeof( $events ) > 0) {
            array_multisort( $aux, SORT_ASC, $events );
            unset( $aux );
        }
        $findTask = 0;
        $idTasks = array ();
        $arrayTasks = array ();
        $port = 5;
        foreach ($events as $key => $val) {
            if ($val['EVN_ACTION'] == 'SEND_MESSAGE' and $val['EVN_RELATED_TO'] == 'SINGLE') {
                $idEvent = $val['EVN_UID'];
                $idTask = $val['TAS_UID'];
                foreach ($tasks as $id => $value) {
                    if ($value['TAS_UID'] == $idTask) {
                        $coordinateX = $value['TAS_POSX'] + 19;
                        $coordinateY = $value['TAS_POSY'] + 38;
                        $coordinateXM = $coordinateX + 96;
                        $coordinateYM = $coordinateY + 65;
                    }
                }
                foreach ($arrayTasks as $id => $value) {
                    if ($idTask == $value['ID']) {
                        $coordinateX = $value['X'] + 30;
                        $coordinateY = $value['Y'];
                        $coordinateXM = $value['XM'] + 30;
                        $coordinateYM = $value['YM'];
                        if ($coordinateY < 30) {
                            $coordinateX = $value['FIRSTX'] + 30;
                            $coordinateY = $value['FIRSTY'];
                        }
                        $arrayTasks[$id]['X'] = $coordinateX;
                        $arrayTasks[$id]['Y'] = $coordinateY;
                        $arrayTasks[$id]['PORT'] = $port + 1;
                        $findTask = 1;
                        $port = $arrayTasks[$id]['PORT'];
                    }
                }

                $description = $val['EVN_DESCRIPTION'];
                $arrayTo = $val['EVN_ACTION_PARAMETERS'];
                foreach ($arrayTo as $idTo => $valueTo) {
                    $to = $valueTo;
                }
                $to = explode( '|', $to );
                $to = $to[0];
                $oConfiguration = new Configuration();
                $emailArray = $oConfiguration->load( 'Emails', '', '', '', '' );
                $arrayFrom = unserialize( $emailArray['CFG_VALUE'] );
                $passwd = $arrayFrom['MESS_PASSWORD'];
                $passwdDec = G::decrypt( $passwd, 'EMAILENCRYPT' );
                if (strpos( $passwdDec, 'hash:' ) !== false) {
                    list ($hash, $pass) = explode( ":", $passwdDec );
                    $arrayFrom['MESS_PASSWORD'] = $pass;
                }
                $from = $arrayFrom['MESS_ACCOUNT'];
                if ($to == 'ext') {
                    $oUser = new Users();
                    $aUser = $oUser->load( $_SESSION['USER_LOGGED'] );
                    $to = $aUser['USR_USERNAME'];
                }
                $dataTasks .= '
        <Activity Id="' . $idEvent . '">
          <Description />
          <Event>
            <IntermediateEvent Trigger="Timer" Target="' . $idTask . '" IsAttached="true">
              <TriggerTimer>
                <ItemElementName>TimeCycle</ItemElementName>
              </TriggerTimer>
            </IntermediateEvent>
          </Event>
          <Documentation />
          <ExtendedAttributes />
          <NodeGraphicsInfos>
            <NodeGraphicsInfo ToolId="BizAgi_Process_Modeler" Height="30" Width="30" BorderColor="-6909623" FillColor="-66833">
              <Coordinates XCoordinate="' . $coordinateX . '" YCoordinate="' . $coordinateY . '" />
            </NodeGraphicsInfo>
          </NodeGraphicsInfos>
          <IsForCompensationSpecified>false</IsForCompensationSpecified>
        </Activity>
        <Activity Id="m' . $idEvent . '" Name="' . $description . '">
          <Description>' . $description . '</Description>
          <Event>
            <IntermediateEvent Trigger="Message" Implementation="Other">
              <TriggerResultMessage CatchThrow="THROW">
              <Message Id="' . $idEvent . '" From="admin" To="' . $to . '" Name="' . $description . '">
              </Message>
              </TriggerResultMessage>
            </IntermediateEvent>
          </Event>
          <Documentation>' . $description . '</Documentation>
          <ExtendedAttributes />
          <NodeGraphicsInfos>
            <NodeGraphicsInfo ToolId="BizAgi_Process_Modeler" Height="30" Width="30" BorderColor="-6909623" FillColor="-66833">
              <Coordinates XCoordinate="' . $coordinateXM . '" YCoordinate="' . $coordinateYM . '" />
            </NodeGraphicsInfo>
          </NodeGraphicsInfos>
          <IsForCompensationSpecified>false</IsForCompensationSpecified>
        </Activity>';
                $transitions .= '
        <Transition Id="t' . $idEvent . '" From="' . $idEvent . '" To="m' . $idEvent . '" Name="">
          <Condition />
          <Description />
          <ExtendedAttributes />
          <ConnectorGraphicsInfos>
            <ConnectorGraphicsInfo ToolId="Processmaker" BorderColor="0" FromPort="' . $port . '" ToPort="1">
            </ConnectorGraphicsInfo>
          </ConnectorGraphicsInfos>
        </Transition>';
                if ($findTask == 0) {
                    $idTasks['ID'] = $idTask;
                    $idTasks['X'] = $coordinateX;
                    $idTasks['Y'] = $coordinateY;
                    $idTasks['FIRSTX'] = $coordinateX;
                    $idTasks['FIRSTY'] = $coordinateY;
                    $idTasks['XM'] = $coordinateXM;
                    $idTasks['YM'] = $coordinateYM;
                    $idTasks['PORT'] = $port;
                    $arrayTasks[] = $idTasks;
                }
            }
        }
        $idTasks = array ();
        $arrayTasks = array ();
        $findTask = 0;
        foreach ($scheduler as $key => $val) {
            if ($val['SCH_OPTION'] == 4) {
                $idSch = $val['SCH_UID'];
                $name = $val['SCH_NAME'];
                $idTask = $val['TAS_UID'];
                foreach ($tasks as $id => $value) {
                    if ($value['TAS_UID'] == $idTask) {
                        $coordinateX = $value['TAS_POSX'] - 60;
                        $coordinateY = $value['TAS_POSY'] + 5;
                    }
                }
                foreach ($arrayTasks as $id => $value) {
                    if ($idTask == $value['ID']) {
                        $coordinateX = $value['X'];
                        $coordinateY = $value['Y'] - 40;
                        if ($coordinateY < 30) {
                            $coordinateX = $value['FIRSTX'] + 50;
                            $coordinateY = $value['FIRSTY'] - 90;
                        }
                        $arrayTasks[$id]['X'] = $coordinateX;
                        $arrayTasks[$id]['Y'] = $coordinateY;
                        $findTask = 1;
                    }
                }
                $time = $val['SCH_TIME_NEXT_RUN'];
                $time = explode( ' ', $time );
                $time = $time[0];
                $time = str_replace( '-', '/', $time );
                $dataTasks .= '
        <Activity Id="' . $idSch . '" Name="' . $name . '">
          <Description />
          <Event>
            <StartEvent Trigger="Timer">
              <TriggerTimer TimeDate="' . $time . '">
                <ItemElementName>TimeCycle</ItemElementName>
              </TriggerTimer>
            </StartEvent>
          </Event>
          <Documentation />
          <ExtendedAttributes />
          <NodeGraphicsInfos>
            <NodeGraphicsInfo ToolId="BizAgi_Process_Modeler" Height="30" Width="30" BorderColor="-10311914" FillColor="-1638505">
              <Coordinates XCoordinate="' . $coordinateX . '" YCoordinate="' . $coordinateY . '" />
            </NodeGraphicsInfo>
          </NodeGraphicsInfos>
          <IsForCompensationSpecified>false</IsForCompensationSpecified>
        </Activity>';
                $transitions .= '
        <Transition Id="t' . $idSch . '" From="' . $idSch . '" To="' . $idTask . '" Name="">
          <Condition />
          <Description />
          <ExtendedAttributes />
          <ConnectorGraphicsInfos>
            <ConnectorGraphicsInfo ToolId="Processmaker" BorderColor="0" FromPort="4" ToPort="3">
            </ConnectorGraphicsInfo>
          </ConnectorGraphicsInfos>
        </Transition>';
                if ($findTask == 0) {
                    $idTasks['ID'] = $idTask;
                    $idTasks['X'] = $coordinateX;
                    $idTasks['Y'] = $coordinateY;
                    $idTasks['FIRSTX'] = $coordinateX;
                    $idTasks['FIRSTY'] = $coordinateY;
                    $arrayTasks[] = $idTasks;
                }
            }
            if ($val['SCH_OPTION'] == 1) {
                $idSch = $val['SCH_UID'];
                $name = $val['SCH_NAME'];
                $idTask = $val['TAS_UID'];
                foreach ($tasks as $id => $value) {
                    if ($value['TAS_UID'] == $idTask) {
                        $coordinateX = $value['TAS_POSX'] - 60;
                        $coordinateY = $value['TAS_POSY'] + 5;
                    }
                }
                foreach ($arrayTasks as $id => $value) {
                    if ($idTask == $value['ID']) {
                        $coordinateX = $value['X'];
                        $coordinateY = $value['Y'] - 40;
                        if ($coordinateY < 30) {
                            $coordinateX = $value['FIRSTX'] + 50;
                            $coordinateY = $value['FIRSTY'] - 90;
                        }
                        $arrayTasks[$id]['X'] = $coordinateX;
                        $arrayTasks[$id]['Y'] = $coordinateY;
                        $findTask = 1;
                    }
                }
                $dataTasks .= '
        <Activity Id="' . $idSch . '" Name="' . $name . '">
          <Description />
          <Event>
            <StartEvent Trigger="Timer">
              <TriggerTimer TimeCycle="1 DD">
                <ItemElementName>TimeCycle</ItemElementName>
              </TriggerTimer>
            </StartEvent>
          </Event>
          <Documentation />
          <ExtendedAttributes />
          <NodeGraphicsInfos>
            <NodeGraphicsInfo ToolId="BizAgi_Process_Modeler" Height="30" Width="30" BorderColor="-10311914" FillColor="-1638505">
              <Coordinates XCoordinate="' . $coordinateX . '" YCoordinate="' . $coordinateY . '" />
            </NodeGraphicsInfo>
          </NodeGraphicsInfos>
          <IsForCompensationSpecified>false</IsForCompensationSpecified>
        </Activity>';
                $transitions .= '
        <Transition Id="t' . $idSch . '" From="' . $idSch . '" To="' . $idTask . '" Name="">
          <Condition />
          <Description />
          <ExtendedAttributes />
          <ConnectorGraphicsInfos>
            <ConnectorGraphicsInfo ToolId="Processmaker" BorderColor="0" FromPort="4" ToPort="3">
            </ConnectorGraphicsInfo>
          </ConnectorGraphicsInfos>
        </Transition>';
                if ($findTask == 0) {
                    $idTasks['ID'] = $idTask;
                    $idTasks['X'] = $coordinateX;
                    $idTasks['Y'] = $coordinateY;
                    $idTasks['FIRSTX'] = $coordinateX;
                    $idTasks['FIRSTY'] = $coordinateY;
                    $arrayTasks[] = $idTasks;
                }
            }
        }
        $data = array ();
        $data['ACTIVITIES'] = $dataTasks;
        $data['TRANSITION'] = $transitions;
        $data['TASK_HIDDEN'] = $taskHidden;
        return $data;
    }

    /**
     * This public function creates transitions
     *
     * @param array $routes
     * @param array $tasks
     * @return array
     */
    public function createTransitionsXpdl ($routes, $tasks, $taskHidden)
    {
        $cont = 0;
        $dataRoutes = '';
        $endEvent = 1;
        $taskParallel = '';
        $routeParallel = '';
        $taskSecJoin = '';
        $routeSecJoin = '';
        $taskEvaluate = '';
        $routeEvaluate = '';
        $taskParallelEv = '';
        $routeParallelEv = '';
        $taskSelect = '';
        $routeSelect = '';
        $routeEnd = '';
        $arraySecJoin = array ();
        $position = 0;
        $fillColor = '';
        $transitions = '';
        $condition = '';
        $nextTask = '';
        $findFrom = 0;
        $findTo = 0;
        $routesTasks = $routes;
        foreach ($routes as $key => $row) {
            if ($row['ROU_TYPE'] == 'SEC-JOIN') {
                $arraySecJoin[$position] = array ();
                $arraySecJoin[$position] = $row;
                $position = $position + 1;
                unset( $routes[$key] );
            }
        }
        $aux = $arraySecJoin;
        foreach ($arraySecJoin as $key => $row) {
            $aux[$key] = $row['ROU_NEXT_TASK'];
        }
        if (sizeof( $arraySecJoin ) > 0) {
            array_multisort( $aux, SORT_ASC, $arraySecJoin );
            unset( $aux );
        }
        foreach ($routes as $key => $row) {
            $uid[$key] = $row['TAS_UID'];
            $case[$key] = $row['ROU_CASE'];
        }
        if (sizeof( $routes ) > 0) {
            array_multisort( $uid, SORT_ASC, $case, SORT_ASC, $routes );
        }
        $routes = array_merge( $routes, $arraySecJoin );
        $routesTasks = $routes;
        foreach ($routes as $key => $val) {
            $end = 0;
            $idRoute = $val['ROU_UID'];
            $idTask = $val['TAS_UID'];
            $nextTask = $val['ROU_NEXT_TASK'];
            $condition = htmlentities( $val['ROU_CONDITION'] );
            if ($nextTask == "-1") {
                $end = 1;
            }
            $typeRoute = $val['ROU_TYPE'];
            $route = '';
            if ($typeRoute != "SEQUENTIAL") {
                switch ($typeRoute) {
                    case 'PARALLEL':
                        $coordinateX = 0;
                        $coordinateY = 0;
                        foreach ($tasks as $taskVal => $idVal) {
                            if ($idVal['TAS_UID'] == $idTask) {
                                $coordinateX = $idVal['TAS_POSX'];
                                $coordinateY = $idVal['TAS_POSY'];
                            }
                        }
                        foreach ($taskHidden as $taskVal => $idVal) {
                            if ($idVal['TAS_UID'] == $idTask) {
                                $coordinateX = $idVal['TAS_POSX'];
                                $coordinateY = $idVal['TAS_POSY'];
                                $idRoute = $idTask;
                            }
                        }
                        $positionX = $coordinateX + 60;
                        $positionY = $coordinateY + 40;
                        if ($idTask != $taskParallel) {
                            $taskParallel = $idTask;
                            $routeParallel = $idRoute;
                            $dataRoutes .= '
        <Activity Id="' . $routeParallel . '">
          <Description />
          <Route GatewayType="AND" />
          <Documentation />
          <ExtendedAttributes />
          <NodeGraphicsInfos>
            <NodeGraphicsInfo ToolId="Processmaker" Height="30" Width="30" BorderColor="-5855715" FillColor="' . $fillColor . '">
              <Coordinates XCoordinate="' . $positionX . '" YCoordinate="' . $positionY . '" />
            </NodeGraphicsInfo>
          </NodeGraphicsInfos>
          <IsForCompensationSpecified>false</IsForCompensationSpecified>
        </Activity>';
                            if ($taskParallel != $routeParallel) {
                                $transitions .= '
        <Transition Id="' . G::generateUniqueID() . '" From="' . $taskParallel . '" To="' . $routeParallel . '" Name="">
          <Condition />
          <Description />
          <ExtendedAttributes />
          <ConnectorGraphicsInfos>
            <ConnectorGraphicsInfo ToolId="Processmaker" BorderColor="0" FromPort="2" ToPort="1">
            </ConnectorGraphicsInfo>
          </ConnectorGraphicsInfos>
        </Transition>';
                            }
                            $transitions .= '
        <Transition Id="' . G::generateUniqueID() . '" From="' . $routeParallel . '" To="' . $nextTask . '" Name="">
          <Condition />
          <Description />
          <ExtendedAttributes />
          <ConnectorGraphicsInfos>
            <ConnectorGraphicsInfo ToolId="Processmaker" BorderColor="0" FromPort="2" ToPort="1">
            </ConnectorGraphicsInfo>
          </ConnectorGraphicsInfos>
        </Transition>';
                        } else {
                            $transitions .= '
        <Transition Id="' . G::generateUniqueID() . '" From="' . $routeParallel . '" To="' . $nextTask . '" Name="">
          <Condition/>
          <Description />
          <ExtendedAttributes />
          <ConnectorGraphicsInfos>
            <ConnectorGraphicsInfo ToolId="Processmaker" BorderColor="0" FromPort="2" ToPort="1">
            </ConnectorGraphicsInfo>
          </ConnectorGraphicsInfos>
        </Transition>';
                        }
                        break;
                    case 'SEC-JOIN':
                        $coordinateX = 0;
                        $coordinateY = 0;
                        foreach ($tasks as $taskVal => $idVal) {
                            if ($idVal['TAS_UID'] == $nextTask) {
                                $coordinateX = $idVal['TAS_POSX'];
                                $coordinateY = $idVal['TAS_POSY'];
                            }
                        }
                        foreach ($taskHidden as $taskVal => $idVal) {
                            if ($idVal['TAS_UID'] == $idTask) {
                                $coordinateX = $idVal['TAS_POSX'];
                                $coordinateY = $idVal['TAS_POSY'];
                                $idRoute = $idTask;
                            }
                        }
                        $positionX = $coordinateX + 60;
                        $positionY = $coordinateY - 45;
                        if ($nextTask != $taskSecJoin) {
                            $taskSecJoin = $nextTask;
                            $routeSecJoin = $idRoute;
                            $dataRoutes .= '
        <Activity Id="' . $routeSecJoin . '">
          <Description />
          <Route GatewayType="AND" />
          <Documentation />
          <ExtendedAttributes />
          <NodeGraphicsInfos>
            <NodeGraphicsInfo ToolId="Processmaker" Height="30" Width="30" BorderColor="-5855715" FillColor="' . $fillColor . '">
              <Coordinates XCoordinate="' . $positionX . '" YCoordinate="' . $positionY . '" />
            </NodeGraphicsInfo>
          </NodeGraphicsInfos>
          <IsForCompensationSpecified>false</IsForCompensationSpecified>
        </Activity>';
                            if ($routeSecJoin != $taskSecJoin) {
                                $transitions .= '
        <Transition Id="' . G::generateUniqueID() . '" From="' . $routeSecJoin . '" To="' . $taskSecJoin . '" Name="">
          <Condition />
          <Description />
          <ExtendedAttributes />
          <ConnectorGraphicsInfos>
            <ConnectorGraphicsInfo ToolId="Processmaker" BorderColor="0" FromPort="2" ToPort="1">
            </ConnectorGraphicsInfo>
          </ConnectorGraphicsInfos>
        </Transition>';
                            }
                            $transitions .= '
        <Transition Id="' . G::generateUniqueID() . '" From="' . $idTask . '" To="' . $routeSecJoin . '" Name="">
          <Condition />
          <Description />
          <ExtendedAttributes />
          <ConnectorGraphicsInfos>
            <ConnectorGraphicsInfo ToolId="Processmaker" BorderColor="0" FromPort="2" ToPort="1">
            </ConnectorGraphicsInfo>
          </ConnectorGraphicsInfos>
        </Transition>';
                        } else {
                            $transitions .= '
        <Transition Id="' . G::generateUniqueID() . '" From="' . $idTask . '" To="' . $routeSecJoin . '" Name="">
          <Condition/>
          <Description />
          <ExtendedAttributes />
          <ConnectorGraphicsInfos>
            <ConnectorGraphicsInfo ToolId="Processmaker" BorderColor="0" FromPort="2" ToPort="1">
            </ConnectorGraphicsInfo>
          </ConnectorGraphicsInfos>
        </Transition>';
                        }
                        break;
                    case 'EVALUATE':
                        $coordinateX = 0;
                        $coordinateY = 0;
                        foreach ($tasks as $taskVal => $idVal) {
                            if ($idVal['TAS_UID'] == $idTask) {
                                $coordinateX = $idVal['TAS_POSX'];
                                $coordinateY = $idVal['TAS_POSY'];
                            }
                        }
                        foreach ($taskHidden as $taskVal => $idVal) {
                            if ($idVal['TAS_UID'] == $idTask) {
                                $coordinateX = $idVal['TAS_POSX'];
                                $coordinateY = $idVal['TAS_POSY'];
                                $idRoute = $idTask;
                            }
                        }
                        $positionX = $coordinateX + 60;
                        $positionY = $coordinateY + 40;
                        if ($idTask != $taskEvaluate) {
                            $taskEvaluate = $idTask;
                            $routeEvaluate = $idRoute;
                            if ($nextTask != "-1") {
                                $dataRoutes .= '
        <Activity Id="' . $routeEvaluate . '">
          <Description />
          <Route MarkerVisible="true"/>
          <Documentation />
          <ExtendedAttributes />
          <NodeGraphicsInfos>
            <NodeGraphicsInfo ToolId="Processmaker" Height="30" Width="30" BorderColor="-5855715" FillColor="' . $fillColor . '">
              <Coordinates XCoordinate="' . $positionX . '" YCoordinate="' . $positionY . '" />
            </NodeGraphicsInfo>
          </NodeGraphicsInfos>
          <IsForCompensationSpecified>false</IsForCompensationSpecified>
        </Activity>';
                                if ($taskEvaluate != $routeEvaluate) {
                                    $transitions .= '
        <Transition Id="' . G::generateUniqueID() . '" From="' . $taskEvaluate . '" To="' . $routeEvaluate . '" Name="">
          <Condition />
          <Description />
          <ExtendedAttributes />
          <ConnectorGraphicsInfos>
            <ConnectorGraphicsInfo ToolId="Processmaker" BorderColor="0" FromPort="2" ToPort="1">
            </ConnectorGraphicsInfo>
          </ConnectorGraphicsInfos>
        </Transition>';
                                }
                                $transitions .= '
        <Transition Id="' . G::generateUniqueID() . '" From="' . $routeEvaluate . '" To="' . $nextTask . '" Name="">
          <Condition Type="CONDITION">
            <Expression>' . $condition . '</Expression>
          </Condition>
          <Description />
          <ExtendedAttributes />
          <ConnectorGraphicsInfos>
            <ConnectorGraphicsInfo ToolId="Processmaker" BorderColor="0" FromPort="2" ToPort="1">
            </ConnectorGraphicsInfo>
          </ConnectorGraphicsInfos>
        </Transition>';
                            }
                        } else {
                            if ($nextTask != "-1") {
                                $transitions .= '
        <Transition Id="' . G::generateUniqueID() . '" From="' . $routeEvaluate . '" To="' . $nextTask . '" Name="">
          <Condition Type="CONDITION">
            <Expression>' . $condition . '</Expression>
          </Condition>
          <Description />
          <ExtendedAttributes />
          <ConnectorGraphicsInfos>
            <ConnectorGraphicsInfo ToolId="Processmaker" BorderColor="0" FromPort="2" ToPort="1">
            </ConnectorGraphicsInfo>
          </ConnectorGraphicsInfos>
        </Transition>';
                            } else {
                                $routeEnd = $routeEvaluate;
                            }
                        }
                        break;
                    case 'PARALLEL-BY-EVALUATION':
                        $coordinateX = 0;
                        $coordinateY = 0;
                        foreach ($tasks as $taskVal => $idVal) {
                            if ($idVal['TAS_UID'] == $idTask) {
                                $coordinateX = $idVal['TAS_POSX'];
                                $coordinateY = $idVal['TAS_POSY'];
                            }
                        }
                        foreach ($taskHidden as $taskVal => $idVal) {
                            if ($idVal['TAS_UID'] == $idTask) {
                                $coordinateX = $idVal['TAS_POSX'];
                                $coordinateY = $idVal['TAS_POSY'];
                                $idRoute = $idTask;
                            }
                        }
                        $positionX = $coordinateX + 60;
                        $positionY = $coordinateY + 40;
                        if ($idTask != $taskParallelEv) {
                            $taskParallelEv = $idTask;
                            $routeParallelEv = $idRoute;
                            $dataRoutes .= '
        <Activity Id="' . $routeParallelEv . '">
          <Description />
          <Route GatewayType="OR"/>
          <Documentation />
          <ExtendedAttributes />
          <NodeGraphicsInfos>
            <NodeGraphicsInfo ToolId="Processmaker" Height="30" Width="30" BorderColor="-5855715" FillColor="' . $fillColor . '">
              <Coordinates XCoordinate="' . $positionX . '" YCoordinate="' . $positionY . '" />
            </NodeGraphicsInfo>
          </NodeGraphicsInfos>
          <IsForCompensationSpecified>false</IsForCompensationSpecified>
        </Activity>';
                            if ($taskParallelEv != $routeParallelEv) {
                                $transitions .= '
        <Transition Id="' . G::generateUniqueID() . '" From="' . $taskParallelEv . '" To="' . $routeParallelEv . '" Name="">
          <Condition />
          <Description />
          <ExtendedAttributes />
          <ConnectorGraphicsInfos>
            <ConnectorGraphicsInfo ToolId="Processmaker" BorderColor="0" FromPort="2" ToPort="1">
            </ConnectorGraphicsInfo>
          </ConnectorGraphicsInfos>
        </Transition>';
                            }
                            $transitions .= '
        <Transition Id="' . G::generateUniqueID() . '" From="' . $routeParallelEv . '" To="' . $nextTask . '" Name="">
          <Condition Type="CONDITION">
            <Expression>' . $condition . '</Expression>
          </Condition>
          <Description />
          <ExtendedAttributes />
          <ConnectorGraphicsInfos>
            <ConnectorGraphicsInfo ToolId="Processmaker" BorderColor="0" FromPort="2" ToPort="1">
            </ConnectorGraphicsInfo>
          </ConnectorGraphicsInfos>
        </Transition>';
                        } else {
                            $transitions .= '
        <Transition Id="' . G::generateUniqueID() . '" From="' . $routeParallelEv . '" To="' . $nextTask . '" Name="">
          <Condition Type="CONDITION">
            <Expression>' . $condition . '</Expression>
          </Condition>
          <Description />
          <ExtendedAttributes />
          <ConnectorGraphicsInfos>
            <ConnectorGraphicsInfo ToolId="Processmaker" BorderColor="0" FromPort="2" ToPort="1">
            </ConnectorGraphicsInfo>
          </ConnectorGraphicsInfos>
        </Transition>';
                        }
                        break;
                    case 'SELECT':
                        $coordinateX = 0;
                        $coordinateY = 0;
                        foreach ($tasks as $taskVal => $idVal) {
                            if ($idVal['TAS_UID'] == $idTask) {
                                $coordinateX = $idVal['TAS_POSX'];
                                $coordinateY = $idVal['TAS_POSY'];
                            }
                        }
                        foreach ($taskHidden as $taskVal => $idVal) {
                            if ($idVal['TAS_UID'] == $idTask) {
                                $coordinateX = $idVal['TAS_POSX'];
                                $coordinateY = $idVal['TAS_POSY'];
                                $idRoute = $idTask;
                            }
                        }
                        $positionX = $coordinateX + 60;
                        $positionY = $coordinateY + 40;
                        if ($idTask != $taskSelect) {
                            $taskSelect = $idTask;
                            $routeSelect = $idRoute;
                            $dataRoutes .= '
        <Activity Id="' . $routeSelect . '">
          <Description />
          <Route GatewayType="Complex" />
          <Documentation />
          <ExtendedAttributes />
          <NodeGraphicsInfos>
            <NodeGraphicsInfo ToolId="Processmaker" Height="30" Width="30" BorderColor="-5855715" FillColor="' . $fillColor . '">
              <Coordinates XCoordinate="' . $positionX . '" YCoordinate="' . $positionY . '" />
            </NodeGraphicsInfo>
          </NodeGraphicsInfos>
          <IsForCompensationSpecified>false</IsForCompensationSpecified>
        </Activity>';
                            if ($taskSelect != $routeSelect) {
                                $transitions .= '
        <Transition Id="' . G::generateUniqueID() . '" From="' . $taskSelect . '" To="' . $routeSelect . '" Name="">
          <Condition />
          <Description />
          <ExtendedAttributes />
          <ConnectorGraphicsInfos>
            <ConnectorGraphicsInfo ToolId="Processmaker" BorderColor="0" FromPort="2" ToPort="1">
            </ConnectorGraphicsInfo>
          </ConnectorGraphicsInfos>
        </Transition>';
                            }
                            $transitions .= '
        <Transition Id="' . G::generateUniqueID() . '" From="' . $routeSelect . '" To="' . $nextTask . '" Name="">
          <Condition Type="CONDITION">
            <Expression>' . $condition . '</Expression>
          </Condition>
          <Description />
          <ExtendedAttributes />
          <ConnectorGraphicsInfos>
            <ConnectorGraphicsInfo ToolId="Processmaker" BorderColor="0" FromPort="2" ToPort="1">
            </ConnectorGraphicsInfo>
          </ConnectorGraphicsInfos>
        </Transition>';
                        } else {
                            if ($nextTask != "-1") {
                                $transitions .= '
        <Transition Id="' . G::generateUniqueID() . '" From="' . $routeSelect . '" To="' . $nextTask . '" Name="">
          <Condition Type="CONDITION">
            <Expression>' . $condition . '</Expression>
          </Condition>
          <Description />
          <ExtendedAttributes />
          <ConnectorGraphicsInfos>
            <ConnectorGraphicsInfo ToolId="Processmaker" BorderColor="0" FromPort="2" ToPort="1">
            </ConnectorGraphicsInfo>
          </ConnectorGraphicsInfos>
        </Transition>';
                            } else {
                                $routeEnd = $routeSelect;
                            }
                        }
                        break;
                    case 'DISCRIMINATOR':
                        $coordinateX = 0;
                        $coordinateY = 0;
                        $optional = $val['ROU_OPTIONAL'];
                        foreach ($tasks as $taskVal => $idVal) {
                            if ($idVal['TAS_UID'] == $nextTask) {
                                $coordinateX = $idVal['TAS_POSX'];
                                $coordinateY = $idVal['TAS_POSY'];
                            }
                        }
                        foreach ($taskHidden as $taskVal => $idVal) {
                            if ($idVal['TAS_UID'] == $idTask) {
                                $coordinateX = $idVal['TAS_POSX'];
                                $coordinateY = $idVal['TAS_POSY'];
                                $idRoute = $idTask;
                            }
                        }
                        $positionX = $coordinateX + 60;
                        $positionY = $coordinateY - 45;
                        if ($nextTask != $taskSecJoin) {
                            $taskDiscriminator = $nextTask;
                            $routeDiscriminator = $idRoute;
                            $dataRoutes .= '
        <Activity Id="' . $routeDiscriminator . '">
          <Description />
          <Route GatewayType="Complex" />
          <Documentation />
          <ExtendedAttributes>
            <ExtendedAttribute Name="option" Value="' . $optional . '" />
            <ExtendedAttribute Name="condition" Value="' . $condition . '" />
          </ExtendedAttributes>
          <NodeGraphicsInfos>
            <NodeGraphicsInfo ToolId="Processmaker" Height="30" Width="30" BorderColor="-5855715" FillColor="' . $fillColor . '">
              <Coordinates XCoordinate="' . $positionX . '" YCoordinate="' . $positionY . '" />
            </NodeGraphicsInfo>
          </NodeGraphicsInfos>
          <IsForCompensationSpecified>false</IsForCompensationSpecified>
        </Activity>';
                            if ($routeDiscriminator != $taskDiscriminator) {
                                $transitions .= '
        <Transition Id="' . G::generateUniqueID() . '" From="' . $routeDiscriminator . '" To="' . $taskDiscriminator . '" Name="">
          <Condition />
          <Description />
          <ExtendedAttributes />
          <ConnectorGraphicsInfos>
            <ConnectorGraphicsInfo ToolId="Processmaker" BorderColor="0" FromPort="2" ToPort="1">
            </ConnectorGraphicsInfo>
          </ConnectorGraphicsInfos>
        </Transition>';
                            }
                            $transitions .= '
        <Transition Id="' . G::generateUniqueID() . '" From="' . $idTask . '" To="' . $routeDiscriminator . '" Name="">
          <Condition />
          <Description />
          <ConnectorGraphicsInfos>
            <ConnectorGraphicsInfo ToolId="Processmaker" BorderColor="0" FromPort="2" ToPort="1">
            </ConnectorGraphicsInfo>
          </ConnectorGraphicsInfos>
        </Transition>';
                        } else {
                            $transitions .= '
        <Transition Id="' . G::generateUniqueID() . '" From="' . $idTask . '" To="' . $routeDiscriminator . '" Name="">
          <Condition />
          <Description />
          <ExtendedAttributes />
          <ConnectorGraphicsInfos>
            <ConnectorGraphicsInfo ToolId="Processmaker" BorderColor="0" FromPort="2" ToPort="1">
            </ConnectorGraphicsInfo>
          </ConnectorGraphicsInfos>
        </Transition>';
                        }
                        break;
                }
            }
            if ($end == 1) {
                if ($typeRoute == "SEQUENTIAL") {
                    $coordinateX = 0;
                    $coordinateY = 0;
                    foreach ($tasks as $taskVal => $idVal) {
                        if ($idVal['TAS_UID'] == $idTask) {
                            $coordinateX = $idVal['TAS_POSX'];
                            $coordinateY = $idVal['TAS_POSY'];
                        }
                    }
                    $positionX = $coordinateX + 65;
                    $positionY = $coordinateY + 40;
                    $transitions .= '
        <Transition Id="' . G::generateUniqueID() . '" From="' . $idTask . '" To="' . $idRoute . '" Name="">
          <Condition />
          <Description />
          <ExtendedAttributes />
          <ConnectorGraphicsInfos>
            <ConnectorGraphicsInfo ToolId="Processmaker" BorderColor="0" FromPort="2" ToPort="1">
            </ConnectorGraphicsInfo>
          </ConnectorGraphicsInfos>
        </Transition>';
                } else {
                    $coordinateX = 0;
                    $coordinateY = 0;
                    foreach ($tasks as $taskVal => $idVal) {
                        if ($idVal['TAS_UID'] == $idTask) {
                            $coordinateX = $idVal['TAS_POSX'];
                            $coordinateY = $idVal['TAS_POSY'];
                        }
                    }
                    $positionX = $coordinateX + 120;
                    $positionY = $coordinateY + 40;
                    $idTask = $routeEnd;
                    $transitions .= '
        <Transition Id="' . G::generateUniqueID() . '" From="' . $idTask . '" To="' . $idRoute . '" Name="">
          <Condition />
          <Description />
          <ExtendedAttributes />
          <ConnectorGraphicsInfos>
            <ConnectorGraphicsInfo ToolId="Processmaker" BorderColor="0" FromPort="4" ToPort="3">
            </ConnectorGraphicsInfo>
          </ConnectorGraphicsInfos>
        </Transition>';
                }
                $dataRoutes .= '
        <Activity Id="' . $idRoute . '">
          <Description />
          <Event>
            <EndEvent />
          </Event>
          <Documentation />
          <ExtendedAttributes />
          <NodeGraphicsInfos>
            <NodeGraphicsInfo ToolId="Processmaker" Height="30" Width="30" BorderColor="-6750208" FillColor="' . $fillColor . '">
              <Coordinates XCoordinate="' . $positionX . '" YCoordinate="' . $positionY . '" />
            </NodeGraphicsInfo>
          </NodeGraphicsInfos>
          <IsForCompensationSpecified>false</IsForCompensationSpecified>
        </Activity>';
                $end = 0;
                $endEvent = 0;
            } else {
                if ($typeRoute == "SEQUENTIAL") {
                    $transitions .= '
        <Transition Id="' . $idRoute . '" From="' . $idTask . '" To="' . $nextTask . '" Name="">
          <Condition />
          <Description />
          <ExtendedAttributes />
          <ConnectorGraphicsInfos>
            <ConnectorGraphicsInfo ToolId="Processmaker" BorderColor="0" FromPort="2" ToPort="1">
            </ConnectorGraphicsInfo>
          </ConnectorGraphicsInfos>
        </Transition>';
                }
                foreach ($tasks as $taskVal => $idVal) {
                    if ($idVal['TAS_UID'] == $idTask) {
                        $coordinateX = $idVal['TAS_POSX'] + 190;
                        $coordinateY = $idVal['TAS_POSY'] + 5;
                        if (isset( $idVal['TAS_DEF_MESSAGE'] )) {
                            if ($idVal['TAS_DEF_MESSAGE'] != '' and $idVal['TAS_DEF_MESSAGE'] != null) {
                                $idEvent = G::generateUniqueID();
                                $example = new Derivation();
                                $fieldUserTo = $example->getAllUsersFromAnyTask( $nextTask );
                                $fieldUserTo = $example->getUsersFullNameFromArray( $fieldUserTo );
                                $fieldUserFrom = $example->getAllUsersFromAnyTask( $idTask );
                                $fieldUserFrom = $example->getUsersFullNameFromArray( $fieldUserFrom );
                                /*$oUser       = new Users();
                                $aUser       = $oUser->load($_SESSION['USER_LOGGED']);
                                $from        = $aUser['USR_USERNAME'];*/
                                $description = '';
                                $userFrom = '';
                                $userTo = '';
                                if (isset( $fieldUserFrom[0]['USR_USERNAME'] )) {
                                    $userFrom = $fieldUserFrom[0]['USR_USERNAME'];
                                }
                                if (isset( $fieldUserTo[0]['USR_USERNAME'] )) {
                                    $userTo = $fieldUserTo[0]['USR_USERNAME'];
                                }
                                $dataRoutes .= '
        <Activity Id="' . $idEvent . '" Name="' . $description . '">
          <Description>' . $description . '</Description>
          <Event>
            <IntermediateEvent Trigger="Message" Implementation="Other">
              <TriggerResultMessage CatchThrow="THROW">
              <Message Id="' . $idEvent . '" From="' . $userFrom . '" To="' . $userTo . '" Name="' . $description . '">
              </Message>
              </TriggerResultMessage>
            </IntermediateEvent>
          </Event>
          <Documentation>' . $description . '</Documentation>
          <ExtendedAttributes />
          <NodeGraphicsInfos>
            <NodeGraphicsInfo ToolId="BizAgi_Process_Modeler" Height="30" Width="30" BorderColor="-6909623" FillColor="-66833">
              <Coordinates XCoordinate="' . $coordinateX . '" YCoordinate="' . $coordinateY . '" />
            </NodeGraphicsInfo>
          </NodeGraphicsInfos>
          <IsForCompensationSpecified>false</IsForCompensationSpecified>
        </Activity>';
                                $transitions .= '
        <Transition Id="t' . $idEvent . '" From="' . $idTask . '" To="' . $idEvent . '" Name="">
          <Condition />
          <Description />
          <ExtendedAttributes />
          <ConnectorGraphicsInfos>
            <ConnectorGraphicsInfo ToolId="Processmaker" BorderColor="0" FromPort="4" ToPort="3">
            </ConnectorGraphicsInfo>
          </ConnectorGraphicsInfos>
        </Transition>';
                            }
                        }
                    }
                }
            }
        }
        $data = array ();
        $data['ACTIVITIES'] = $dataRoutes;
        $data['TRANSITION'] = $transitions;
        return $data;
    }

    /**
     * Get the process Data from a filename .
     *
     * XPDL
     *
     * @param array $pmFilename
     * @return void
     */
    public function getProcessDataXpdl ($pmFilename)
    {
        if (! file_exists( $pmFilename )) {
            throw (new Exception( 'Unable to read uploaded file, please check permissions. ' ));
        }
        if (! filesize( $pmFilename ) >= 9) {
            throw (new Exception( 'Uploaded file is corrupted, please check the file before continue. ' ));
        }
        clearstatcache();
        $idProcess = 0;
        $nameProcess = '';
        $sw = 0;
        $file = new DOMDocument();
        @$file->load( $pmFilename, LIBXML_DTDLOAD );
        $root = $file->documentElement;
        $node = $root->firstChild;
        $numberTasks = 0;
        $numberLanes = 0;
        $endArray = array ();
        $posEnd = 0;
        $startArray = array ();
        $posStart = 0;
        $isEnd = 0;
        $isStart = 0;
        $posRoutes = 0;
        $sequencial = 0;
        $posT = 0;
        $output = '';
        $arrayRoutes = array ();
        $arrayLanes = array ();
        $routeTransitions = array ();
        $start = 0;
        $end = 0;
        $arrayScheduler = array ();
        $arrayMessages = array ();
        while ($node) {
            if ($node->nodeType == XML_ELEMENT_NODE) {
                $name = $node->nodeName;
                $content = $node->firstChild;
                if ($content != array ()) {
                    $output = $content->nodeValue;
                }
                if (strpos( $name, 'Pools' ) !== false) {
                    while ($content) {
                        if ($content->nodeType == XML_ELEMENT_NODE) {
                            $nameChild = $content->nodeName;
                            $contentChild = $content->firstChild;
                            if (strpos( $nameChild, 'Pool' ) !== false) {
                                $namePool = $content->getAttribute( 'Name' );
                                $idPool = str_replace( '-', '', $content->getAttribute( 'Process' ) );
                                $process['ID'] = $idPool;
                                $process['NAME'] = $namePool;
                                $process['DESCRIPTION'] = '';
                                $oData->process = $this->createProcess( $process );
                            }
                        }
                        $content = $content->nextSibling;
                    }
                }
                if (strpos( $name, 'Artifacts' ) !== false) {
                    while ($content) {
                        if ($content->nodeType == XML_ELEMENT_NODE) {
                            $nameChild = $content->nodeName;
                            $contentChild = $content->firstChild;
                            $idActivity = '';
                            if (strpos( $nameChild, 'Artifact' ) !== false) {
                                $artifactType = $content->getAttribute( 'ArtifactType' );
                                if ($artifactType == 'Annotation') {
                                    $textAnnotation = $content->getAttribute( 'TextAnnotation' );
                                    $idAnnotation = str_replace( '-', '', $content->getAttribute( 'Id' ) );
                                    $idActivity = str_replace( '-', '', $content->getAttribute( 'ActivitySetId' ) );
                                    $lanes = $this->findCoordinates( $contentChild );
                                    $lanes['TEXT'] = $textAnnotation;
                                    $lanes['ID_PROCESS'] = $idPool;
                                    $lanes['ID_LANE'] = $idAnnotation;
                                    if ($idActivity != '') {
                                        $lanes['ID_PROCESS'] = $idActivity;
                                    }
                                    $arrayLanes[$numberLanes] = $this->createLanes( $lanes );
                                    $numberLanes = $numberLanes + 1;
                                }
                            }
                        }
                        $content = $content->nextSibling;
                    }
                }
                if (strpos( $name, 'WorkflowProcesses' ) !== false) {
                    while ($content) {
                        if ($content->nodeType == XML_ELEMENT_NODE) {
                            $nameChild = $content->nodeName;
                            $contentChild = $content->firstChild;
                            if (strpos( $nameChild, 'WorkflowProcess' ) !== false) {
                                $nameWorkflow = $content->getAttribute( 'Name' );
                                $idProcess = $content->getAttribute( 'Id' );
                                $idProcess = trim( str_replace( '-', '', $idProcess ) );
                                $subProcesses = array ();
                                if ($nameWorkflow == $namePool and $idProcess == $idPool) {
                                    $idWorkflow = $idProcess;
                                    while ($contentChild) {
                                        if ($contentChild->nodeType == XML_ELEMENT_NODE) {
                                            $nameNode = $contentChild->nodeName;
                                            $contentNode = $contentChild->firstChild;
                                            if (strpos( $nameNode, 'ActivitySets' ) !== false) {
                                                $activitySet = $this->createSubProcesses( $contentNode, $arrayLanes );
                                                $subProcesses = $activitySet['SUB_PROCESS'];
                                                $arraySubProcesses = $activitySet['SUBPROCESSES'];
                                            }
                                            if (strpos( $nameNode, 'Activities' ) !== false) {
                                                $activities = $this->createActivities( $contentNode, $idProcess, $subProcesses );
                                                $oData->tasks = $activities['TASKS'];
                                                $startArray = $activities['START'];
                                                $endArray = $activities['END'];
                                                $arrayRoutes = $activities['ROUTES'];
                                                $arrayScheduler = $activities['SCHEDULER'];
                                                $arrayMessages = $activities['MESSAGES'];
                                            }

                                            if (strpos( $nameNode, 'Transitions' ) !== false) {
                                                $transitions = $this->createTransitions( $contentNode, $oData->tasks, $arrayRoutes, $endArray, $startArray, $idProcess, $arrayScheduler, $arrayMessages );
                                                $oData->routes = $transitions['ROUTES'];
                                                $oData->tasks = $transitions['TASKS'];
                                                $routeTransitions = $transitions['TRANSITIONS'];
                                                $numberRoutes = $transitions['NUMBER'];
                                                $taskHidden = $transitions['TASKHIDDEN'];
                                                $arrayScheduler = $transitions['SCHEDULER'];
                                                $arrayMessages = $transitions['MESSAGES'];
                                            }
                                        }
                                        $contentChild = $contentChild->nextSibling;
                                    }
                                }
                            }
                        }
                        $content = $content->nextSibling;
                    }
                }
            }
            $node = $node->nextSibling;
        }
        $oData->lanes = array ();
        $numberLanes = 0;
        foreach ($arrayLanes as $key => $value) {
            if ($value['PRO_UID'] == $idProcess) {
                $oData->lanes[$numberLanes] = $value;
                $numberLanes = $numberLanes + 1;
            }
        }
        $oData->inputs = array ();
        $oData->outputs = array ();
        $oData->dynaforms = array ();
        $oData->steps = array ();
        $oData->taskusers = array ();
        $oData->groupwfs = $this->getGroupwfRows( $oData->taskusers );
        $oData->steptriggers = array ();
        $oData->dbconnections = array ();
        $oData->reportTables = array ();
        $oData->reportTablesVars = array ();
        $oData->stepSupervisor = array ();
        $oData->objectPermissions = array ();
        $oData->subProcess = array ();
        $numberSubProcess = 0;
        $arraySubProcess = $subProcesses;
        $numberSubProcess = isset( $arraySubProcesses ) && is_array( $arraySubProcesses ) ? sizeof( $arraySubProcesses ) : 0;
        $numberCount = 0;
        foreach ($subProcesses as $key => $value) {
            foreach ($oData->tasks as $keyTask => $valueTask) {
                if ($value['ID_PROCESS'] == $valueTask['TAS_UID']) {
                    $fields['TASK_PARENT'] = $valueTask['TAS_UID'];
                    $idSubProcess = $valueTask['TAS_UID'];
                    $findTask = 0;
                    $newTasks = $this->getTaskRows( $idSubProcess );
                    foreach ($newTasks as $keyLane => $val) {
                        if ($val['TAS_START'] === 'TRUE' and $findTask == 0) {
                            $findTask = 1;
                            $value['TASK_START'] = $val['TAS_UID'];
                        }
                    }
                    $fields['PROCESS_PARENT'] = $idProcess;
                    $fields['TAS_UID'] = $value['TASK_START'];
                    $oData->subProcess[$numberSubProcess] = $this->createSubProcess( $fields, $arraySubProcess );
                    $numberSubProcess = $numberSubProcess + 1;
                }
            }
        }
        $oData->caseTracker = array ();
        $oData->caseTrackerObject = array ();
        $oData->stage = array ();
        $oData->fieldCondition = array ();
        $oData->event = $this->createEventMessages( $arrayMessages, $idProcess );
        $oData->triggers = array ();
        $oData->caseScheduler = $this->createScheduler( $arrayScheduler, $idProcess );
        $oData->dynaformFiles = array ();
        $numberTransitions = sizeof( $routeTransitions );
        if ($numberTransitions > 0) {
            $routesArray = $this->createGateways( $routeTransitions, $endArray, $oData->routes, $numberRoutes, $idProcess, $taskHidden );
            $oData->routes = $routesArray;
        }

        //print_r($oData);die;
        //print_r($arrayMessages);die;
        return $oData;
    }

    /**
     * This public function sort a array
     *
     * @param array $fields
     * @return array sorted
     */
    public function sortArray ($fields)
    {
        $aux = $fields;
        foreach ($fields as $key => $row) {
            $aux[$key] = $row['FROM'];
        }
        array_multisort( $aux, SORT_ASC, $fields );
        return $fields;
    }

    /**
     * This functions verify the routes and removes the routes that are repeated
     *
     * @param array $routeTransitions
     * @param array $endArray
     * @return array
     */
    public function verifyRoutes ($routeTransitions, $endArray, $taskHidden)
    {
        $findFirst = 0;
        $firstRoute = '';
        $taskTo = '';
        $taskFrom = '';
        $routeArrayT = $routeTransitions;
        $findHidden = 0;
        foreach ($routeTransitions as $valRoute => $value) {
            $findHidden = 0;
            if ($value['ROUTE'] == $firstRoute) {
                if ($value['TOORFROM'] == 'TO') {
                    $taskFrom = $value['FROM'];
                }
                if ($value['TOORFROM'] == 'FROM') {
                    $taskTo = $value['TO'];
                }
                if ($taskFrom != '') {
                    foreach ($routeArrayT as $valRoutes => $values) {
                        $isEventEnd = 0;
                        foreach ($endArray as $endBase => $valueEnd) {
                            if ($valueEnd == $values['TO']) {
                                $isEventEnd = 1;
                            }
                        }
                        if ($values['ROUTE'] == $value['ROUTE'] and $values['TO'] != $value['ROUTE'] and $isEventEnd == 0 and $findHidden == 0) {
                            $taskFrom = $values['TO'];
                        } else {
                            if ($values['ROUTE'] == $value['ROUTE'] and $values['TO'] == $value['ROUTE'] and $isEventEnd == 0) {
                                foreach ($taskHidden as $idHidden => $valueHidden) {
                                    if ($valueHidden['ID_TASK'] == $values['TO']) {
                                        $taskFrom = $valueHidden['ID_TASK'];
                                        $findHidden = 1;
                                    }
                                }
                            }
                        }
                    }
                    $routeTransitions[$valRoute]['TO'] = $taskFrom;
                    $taskFrom = '';
                }
                if ($taskTo != '') {
                    foreach ($routeArrayT as $valRoutes => $values) {
                        if ($values['ROUTE'] == $value['ROUTE'] and $values['FROM'] != $value['ROUTE'] and $findHidden == 0) {
                            $taskTo = $values['FROM'];
                        }
                    }
                    $routeTransitions[$valRoute]['FROM'] = $taskTo;
                    $taskTo = '';
                }
            } else {
                $firstRoute = $value['ROUTE'];
                $taskToE = '';
                $taskFromE = '';
                if ($value['TOORFROM'] == 'TO') {
                    $taskFromE = $value['FROM'];
                }
                if ($value['TOORFROM'] == 'FROM') {
                    $taskToE = $value['TO'];
                }
                if ($taskFromE != '') {
                    $findHidden = 0;
                    foreach ($routeArrayT as $valRoutes => $values) {
                        $isEventEnd = 0;
                        foreach ($endArray as $endBase => $valueEnd) {
                            if ($valueEnd == $values['TO']) {
                                $isEventEnd = 1;
                            }
                        }
                        if ($values['ROUTE'] == $value['ROUTE'] and $values['TO'] != $value['ROUTE'] and $isEventEnd == 0 and $findHidden == 0) {
                            $taskFromE = $values['TO'];
                        } else {
                            if ($values['ROUTE'] == $value['ROUTE'] and $values['TO'] == $value['ROUTE'] and $isEventEnd == 0) {
                                foreach ($taskHidden as $idHidden => $valueHidden) {
                                    if ($valueHidden['ID_TASK'] == $values['TO']) {
                                        $taskFromE = $valueHidden['ID_TASK'];
                                        $findHidden = 1;
                                    }
                                }
                            }
                        }
                    }
                    $routeTransitions[$valRoute]['TO'] = $taskFromE;
                    $taskFromE = '';
                }
                if ($taskToE != '') {
                    foreach ($routeArrayT as $valRoutes => $values) {
                        if ($values['ROUTE'] == $value['ROUTE'] and $values['FROM'] != $value['ROUTE'] and $findHidden == 0) {
                            $taskToE = $values['FROM'];
                        }
                    }
                    $routeTransitions[$valRoute]['FROM'] = $taskToE;
                    $taskToE = '';
                }
            }
        }
        $firstRoute = 0;
        $cont = 0;
        $routeChange = $routeTransitions;
        foreach ($routeTransitions as $valRoutes => $value) {
            $route = $value['ROUTE'];
            $type = $value['ROU_TYPE'];
            $countFrom = 0;
            $countTo = 0;
            foreach ($routeChange as $valRoutes2 => $values) {
                if ($value['ROUTE'] == $values['ROUTE'] and $values['TOORFROM'] == 'TO') {
                    $countTo = $countTo + 1;
                }
                if ($value['ROUTE'] == $values['ROUTE'] and $values['TOORFROM'] == 'FROM') {
                    $countFrom = $countFrom + 1;
                }
            }
            if ($type == 'PARALLEL') {
                if ($countTo > $countFrom) {
                    $routeTransitions[$valRoutes]['ROU_TYPE'] = 'SEC-JOIN';
                }
            }
        }
        $routeArrayT2 = $routeTransitions;
        $routeArrayT1 = $routeTransitions;
        foreach ($routeArrayT1 as $valRoutes => $value) {
            if ($firstRoute == 0) {
                $taskFirst = $value['ROUTE'];
            }
            if ($taskFirst == $value['ROUTE']) {
                if ($firstRoute == 0) {
                    foreach ($routeArrayT2 as $valRoutes2 => $values) {
                        if ($values['ROUTE'] == $taskFirst and $values['FROM'] == $value['FROM'] and $values['TO'] == $value['TO'] and $values['ID'] != $value['ID']) {
                            unset( $routeArrayT2[$valRoutes2] );
                            $firstRoute = 1;
                        }
                    }
                }
            } else {
                $firstRoute = 0;
            }
        }
        return $routeArrayT2;
    }

    /**
     * this public function creates an array for the process that will be created according to the data given in an array
     *
     * @param array $fields
     * @return array $process
     */
    public function createProcess ($fields)
    {
        $process = array ();
        $process['PRO_UID'] = $fields['ID'];
        $process['PRO_PARENT'] = $fields['ID'];
        $process['PRO_TIME'] = 1;
        $process['PRO_TIMEUNIT'] = 'DAYS';
        $process['PRO_STATUS'] = 'ACTIVE';
        $process['PRO_TYPE_DAY'] = '';
        $process['PRO_TYPE'] = 'NORMAL';
        $process['PRO_ASSIGNMENT'] = 'FALSE';
        $process['PRO_SHOW_MAP'] = 0;
        $process['PRO_SHOW_MESSAGE'] = 0;
        $process['PRO_SHOW_DELEGATE'] = 0;
        $process['PRO_SHOW_DYNAFORM'] = 0;
        $process['PRO_CATEGORY'] = '';
        $process['PRO_SUB_CATEGORY'] = '';
        $process['PRO_INDUSTRY'] = 0;
        $process['PRO_UPDATE_DATE'] = date( "D M j G:i:s T Y" );
        $process['PRO_CREATE_DATE'] = date( "D M j G:i:s T Y" );
        $process['PRO_CREATE_USER'] = 00000000000000000000000000000001;
        $process['PRO_HEIGHT'] = 5000;
        $process['PRO_WIDTH'] = 10000;
        $process['PRO_TITLE_X'] = 0;
        $process['PRO_TITLE_Y'] = 0;
        $process['PRO_DEBUG'] = 0;
        $process['PRO_TITLE'] = $fields['NAME'];
        $process['PRO_DESCRIPTION'] = $fields['DESCRIPTION'];
        return $process;
    }

    /**
     * this public function creates an array for the subProcess that will be created,according to the data given in an array
     *
     * @param array $contentNode
     * @param array $arrayLanes
     * @return array
     */
    public function createSubProcesses ($contentNode, $arrayLanes)
    {
        $activities = array ();
        $numberActivities = 0;
        $numberLanes = 0;
        $number = 0;
        $contentSubNode = $contentNode;
        $numberSub = 0;
        while ($contentSubNode) {
            if ($contentSubNode->nodeType == XML_ELEMENT_NODE) {
                $nameActivity = $contentSubNode->nodeName;
                $contentActivity = $contentSubNode->firstChild;
                if (strpos( $nameActivity, 'ActivitySet' ) !== false) {
                    $idSetActivity = str_replace( '-', '', $contentSubNode->getAttribute( 'Id' ) );
                    $arrayActivity[$number]['ID_PROCESS'] = $idSetActivity;
                    $number = $number + 1;
                }
            }
            $contentSubNode = $contentSubNode->nextSibling;
        }
        $number = 0;
        $arraySubProcesses = array ();
        while ($contentNode) {
            if ($contentNode->nodeType == XML_ELEMENT_NODE) {
                $isSubProcess = 0;
                $nameActivity = $contentNode->nodeName;
                $contentActivity = $contentNode->firstChild;
                $idSetActivity = 0;
                if (strpos( $nameActivity, 'ActivitySet' ) !== false) {
                    $idSetActivity = str_replace( '-', '', $contentNode->getAttribute( 'Id' ) );
                    if ($idSetActivity !== 0) {
                        $isSubProcess = 1;
                    }
                    $nameSetActivity = $contentNode->getAttribute( 'Name' );
                    $contentChild = $contentNode->firstChild;
                    $process['ID'] = $idSetActivity;
                    $process['NAME'] = $nameSetActivity;
                    $process['DESCRIPTION'] = '';
                    $subProcess->process = $this->createProcess( $process );
                    $activities[$number]['ID_PROCESS'] = $idSetActivity;
                    while ($contentChild) {
                        if ($contentChild->nodeType == XML_ELEMENT_NODE) {

                            $nameChild = $contentChild->nodeName;
                            $contentFirst = $contentChild->firstChild;
                            if (strpos( $nameChild, 'Activities' ) !== false) {
                                $arrayActivities = $this->createActivities( $contentFirst, $idSetActivity, $arrayActivity );
                                $subProcess->tasks = $arrayActivities['TASKS'];
                                $startArray = $arrayActivities['START'];
                                $endArray = $arrayActivities['END'];
                                $arrayRoutes = $arrayActivities['ROUTES'];
                                $arrayScheduler = $arrayActivities['SCHEDULER'];
                                $arrayMessages = $arrayActivities['MESSAGES'];
                            }

                            if (strpos( $nameChild, 'Transitions' ) !== false) {
                                $transitions = $this->createTransitions( $contentFirst, $subProcess->tasks, $arrayRoutes, $endArray, $startArray, $idSetActivity, $arrayScheduler, $arrayMessages );
                                $subProcess->routes = $transitions['ROUTES'];
                                $subProcess->tasks = $transitions['TASKS'];
                                $routeTransitions = $transitions['TRANSITIONS'];
                                $numberRoutes = $transitions['NUMBER'];
                                $arrayMessages = $transitions['MESSAGES'];
                            }
                        }
                        $contentChild = $contentChild->nextSibling;
                    }

                    $values = $subProcess;
                    if ($isSubProcess == 1) {
                        $arraySubProcesses[$numberSub]['ID_PROCESS'] = $idSetActivity;
                        $arraySubProcesses[$numberSub]['TASKS'] = $subProcess->tasks;
                        $numberSub = $numberSub + 1;
                    }
                    $activities[$number]['TASK_START'] = $startArray[0]['ID_TASK'];
                    $number = $number + 1;
                    $subProcess->lanes = array ();
                    $numberLanes = 0;
                    foreach ($arrayLanes as $key => $value) {
                        if ($value['PRO_UID'] == $idSetActivity) {
                            $subProcess->lanes[$numberLanes] = $value;
                            $numberLanes = $numberLanes + 1;
                        }
                    }
                    $subProcess->inputs = array ();
                    $subProcess->outputs = array ();
                    $subProcess->dynaforms = array ();
                    $subProcess->steps = array ();
                    $subProcess->triggers = array ();
                    $subProcess->taskusers = array ();
                    $subProcess->groupwfs = $this->getGroupwfRows( $subProcess->taskusers );
                    $subProcess->steptriggers = array ();
                    $subProcess->dbconnections = array ();
                    $subProcess->reportTables = array ();
                    $subProcess->reportTablesVars = array ();
                    $subProcess->stepSupervisor = array ();
                    $subProcess->objectPermissions = array ();
                    $subProcess->subProcess = array ();
                    $subProcess->caseTracker = array ();
                    $subProcess->caseTrackerObject = array ();
                    $subProcess->stage = array ();
                    $subProcess->fieldCondition = array ();
                    $subProcess->event = array ();
                    $subProcess->caseScheduler = array ();
                    $subProcess->dynaformFiles = array ();
                    $numberTransitions = sizeof( $routeTransitions );
                    if ($numberTransitions > 0) {
                        $routesArray = $this->createGateways( $routeTransitions, $endArray, $subProcess->routes, $numberRoutes, $idProcess, '' );
                        $subProcess->routes = $routesArray;
                    }
                    $numberSubProcess = 0;
                    foreach ($subProcess->tasks as $key => $value) {
                        if ($value['TAS_TYPE'] === 'SUBPROCESS') {
                            $fields['TASK_PARENT'] = $value['TAS_UID'];
                            $idSubProcess = $value['TAS_UID'];
                            $newTasks = $this->getTaskRows( $idSubProcess );

                            $findTask = 0;
                            foreach ($newTasks as $keyLane => $val) {
                                if ($val['TAS_START'] === 'TRUE' and $findTask == 0) {
                                    $findTask = 1;
                                    $value['TASK_START'] = $val['TAS_UID'];
                                }
                            }
                            $fields['PROCESS_PARENT'] = $idSetActivity;
                            $fields['TAS_UID'] = $value['TAS_UID'];
                            $subProcess->subProcess[$numberSubProcess] = $this->createSubProcess( $fields, $arrayActivity );
                            $numberSubProcess = $numberSubProcess + 1;
                        }
                    }
                    $this->createSubProcessFromDataXpdl( $subProcess, $subProcess->tasks );
                }
            }
            $contentNode = $contentNode->nextSibling;
        }
        $arrayActivities['SUBPROCESSES'] = $arraySubProcesses;
        $arrayActivities['SUB_PROCESS'] = $activities;
        return $arrayActivities;
    }

    /**
     * This public function creates an array for the task that will be created according to the data given in an array
     *
     * @param array $fields
     * @return array $task
     */
    public function createTask ($fields)
    {
        $task = array ();
        $task['PRO_UID'] = $fields['ID_PROCESS'];
        $task['TAS_UID'] = $fields['ID_TASK'];
        $task['TAS_TYPE'] = $fields['TASK_TYPE'];
        $task['TAS_DURATION'] = 1;
        $task['TAS_DELAY_TYPE'] = '';
        $task['TAS_TEMPORIZER'] = 0;
        $task['TAS_TYPE_DAY'] = '';
        $task['TAS_TIMEUNIT'] = 'DAYS';
        $task['TAS_ALERT'] = 'FALSE';
        $task['TAS_PRIORITY_VARIABLE'] = '@@SYS_CASE_PRIORITY';
        $task['TAS_ASSIGN_TYPE'] = 'BALANCED';
        $task['TAS_ASSIGN_VARIABLE'] = '@@SYS_NEXT_USER_TO_BE_ASSIGNED';
        $task['TAS_ASSIGN_LOCATION'] = 'FALSE';
        $task['TAS_ASSIGN_LOCATION_ADHOC'] = 'FALSE';
        $task['TAS_TRANSFER_FLY'] = 'FALSE';
        $task['TAS_LAST_ASSIGNED'] = '00000000000000000000000000000001';
        $task['TAS_USER'] = '0';
        $task['TAS_CAN_UPLOAD'] = 'FALSE';
        $task['TAS_VIEW_UPLOAD'] = 'FALSE';
        $task['TAS_VIEW_ADDITIONAL_DOCUMENTATION'] = 'FALSE';
        $task['TAS_CAN_CANCEL'] = 'FALSE';
        $task['TAS_OWNER_APP'] = 'FALSE';
        $task['STG_UID'] = '';
        $task['TAS_CAN_PAUSE'] = 'FALSE';
        $task['TAS_CAN_SEND_MESSAGE'] = 'TRUE';
        $task['TAS_CAN_DELETE_DOCS'] = 'FALSE';
        $task['TAS_SELF_SERVICE'] = 'FALSE';
        $task['TAS_START'] = 'FALSE';
        $task['TAS_TO_LAST_USER'] = 'FALSE';
        $task['TAS_SEND_LAST_EMAIL'] = 'FALSE';
        $task['TAS_DERIVATION'] = 'NORMAL';
        $task['TAS_POSX'] = $fields['X'];
        $task['TAS_POSY'] = $fields['Y'];
        $task['TAS_COLOR'] = '';
        $task['TAS_DEF_MESSAGE'] = '';
        $task['TAS_DEF_PROC_CODE'] = '';
        $task['TAS_DEF_DESCRIPTION'] = '';
        $task['TAS_TITLE'] = $fields['TAS_TITLE'];
        $task['TAS_DESCRIPTION'] = $fields['DESCRIPTION'];
        $task['TAS_DEF_TITLE'] = '';
        return $task;
    }

    public function createDataTask ($fields)
    {
        $task = array ();
        $task['PRO_UID'] = $fields['ID_PROCESS'];
        $task['TAS_UID'] = $fields['ID_TASK'];
        $task['TAS_TYPE'] = $fields['TAS_TYPE'];
        $task['TAS_DURATION'] = 1;
        $task['TAS_DELAY_TYPE'] = '';
        $task['TAS_TEMPORIZER'] = 0;
        $task['TAS_TYPE_DAY'] = '';
        $task['TAS_TIMEUNIT'] = 'DAYS';
        $task['TAS_ALERT'] = 'FALSE';
        $task['TAS_PRIORITY_VARIABLE'] = '@@SYS_CASE_PRIORITY';
        $task['TAS_ASSIGN_TYPE'] = 'BALANCED';
        $task['TAS_ASSIGN_VARIABLE'] = '@@SYS_NEXT_USER_TO_BE_ASSIGNED';
        $task['TAS_MI_INSTANCE_VARIABLE'] = '@@SYS_VAR_TOTAL_INSTANCE';
        $task['TAS_MI_COMPLETE_VARIABLE'] = '@@SYS_VAR_TOTAL_INSTANCES_COMPLETE';
        $task['TAS_ASSIGN_LOCATION'] = 'FALSE';
        $task['TAS_ASSIGN_LOCATION_ADHOC'] = 'FALSE';
        $task['TAS_TRANSFER_FLY'] = 'FALSE';
        $task['TAS_LAST_ASSIGNED'] = '00000000000000000000000000000001';
        $task['TAS_USER'] = '0';
        $task['TAS_CAN_UPLOAD'] = 'FALSE';
        $task['TAS_VIEW_UPLOAD'] = 'FALSE';
        $task['TAS_VIEW_ADDITIONAL_DOCUMENTATION'] = 'FALSE';
        $task['TAS_CAN_CANCEL'] = 'FALSE';
        $task['TAS_OWNER_APP'] = 'FALSE';
        $task['STG_UID'] = '';
        $task['TAS_CAN_PAUSE'] = 'FALSE';
        $task['TAS_CAN_SEND_MESSAGE'] = 'TRUE';
        $task['TAS_CAN_DELETE_DOCS'] = 'FALSE';
        $task['TAS_SELF_SERVICE'] = 'FALSE';
        $task['TAS_START'] = $fields['START'];
        $task['TAS_TO_LAST_USER'] = 'FALSE';
        $task['TAS_SEND_LAST_EMAIL'] = 'FALSE';
        $task['TAS_DERIVATION'] = 'NORMAL';
        $task['TAS_POSX'] = $fields['X'];
        $task['TAS_POSY'] = $fields['Y'];
        $task['TAS_COLOR'] = '';
        $task['TAS_DEF_MESSAGE'] = '';
        $task['TAS_DEF_PROC_CODE'] = '';
        $task['TAS_DEF_DESCRIPTION'] = '';
        $task['TAS_TITLE'] = $fields['TAS_TITLE'];
        $task['TAS_DESCRIPTION'] = $fields['DESCRIPTION'];
        $task['TAS_DEF_TITLE'] = '';
        return $task;
    }

    /**
     * This public function creates an array for the route that will be created according to the data given in an array
     *
     * @param array $fields
     * @return array $route
     */
    public function createRoute ($fields)
    {
        $route = array ();
        $route['ROU_UID'] = $fields['ROU_UID'];
        $route['ROU_PARENT'] = 0;
        $route['PRO_UID'] = $fields['ID_PROCESS'];
        $route['TAS_UID'] = $fields['FROM'];
        $route['ROU_NEXT_TASK'] = $fields['TO'];
        $route['ROU_CASE'] = 1;
        $route['ROU_TYPE'] = 'SEQUENTIAL';
        $route['ROU_CONDITION'] = '';
        $route['ROU_TO_LAST_USER'] = 'FALSE';
        $route['ROU_OPTIONAL'] = 'FALSE';
        $route['ROU_SEND_EMAIL'] = 'TRUE';
        $route['ROU_SOURCEANCHOR'] = 1;
        $route['ROU_TARGETANCHOR'] = 0;
        return $route;
    }

    /**
     * This public function creates an array for the subProcess that will be created according to the data given in an array
     *
     * @param array $fields
     * @return array $route
     */
    public function createSubProcess ($fields)
    {
        $subProcess = array ();
        $subProcess['SP_UID'] = $fields['TAS_UID'];
        $subProcess['PRO_UID'] = $fields['TASK_PARENT'];
        $subProcess['TAS_UID'] = $fields['TAS_UID'];
        $subProcess['PRO_PARENT'] = $fields['PROCESS_PARENT'];
        $subProcess['TAS_PARENT'] = $fields['TASK_PARENT'];
        $subProcess['SP_TYPE'] = 'SIMPLE';
        $subProcess['SP_SYNCHRONOUS'] = 0;
        $subProcess['SP_SYNCHRONOUS_TYPE'] = 'ALL';
        $subProcess['SP_SYNCHRONOUS_WAIT'] = 0;
        $subProcess['SP_VARIABLES_OUT'] = 'a:1:{s:0:"";s:0:"";}';
        $subProcess['SP_VARIABLES_IN'] = 'a:1:{s:0:"";s:0:"";}';
        $subProcess['SP_GRID_IN'] = '';
        return $subProcess;
    }

    /**
     * This public function creates an array for the routes that are not sequential that will be created according to the data given in an array
     *
     * @param array $routeTransitions
     * @param array $endArray
     * @param array $dataRoutes
     * @param array $numberRoutes
     * @param string $idProcess
     * @return array $dataRoutes
     */
    public function createGateways ($routeTransitions, $endArray, $dataRoutes, $numberRoutes, $idProcess, $taskHidden)
    {
        $valueCase = '';
        $value = 1;
        $valueC = 1;
        $aux = $routeTransitions;
        foreach ($routeTransitions as $key => $row) {
            $aux[$key] = $row['ROUTE'];
        }
        array_multisort( $aux, SORT_ASC, $routeTransitions );
        $routeArray = $this->verifyRoutes( $routeTransitions, $endArray, $taskHidden );
        $routeArray = $this->sortArray( $routeArray );
        foreach ($routeArray as $valRoutes => $value) {
            $isEventEnd = 0;
            foreach ($endArray as $endBase => $valueEnd) {
                if ($valueEnd['ID_TASK'] == $value['TO']) {
                    $isEventEnd = 1;
                }
            }
            $dataRoutes[$numberRoutes] = array ();
            $dataRoutes[$numberRoutes]['ROU_UID'] = $value['ID'];
            $dataRoutes[$numberRoutes]['ROU_PARENT'] = 0;
            $dataRoutes[$numberRoutes]['PRO_UID'] = $idProcess;
            $dataRoutes[$numberRoutes]['TAS_UID'] = $value['FROM'];

            if ($isEventEnd == 0) {
                $dataRoutes[$numberRoutes]['ROU_NEXT_TASK'] = $value['TO'];
            } else {
                $dataRoutes[$numberRoutes]['ROU_NEXT_TASK'] = '-1';
            }
            if ($valueCase == $value['FROM']) {
                $valueC = $valueC + 1;
            } else {
                $valueC = 1;
                $valueCase = $value['FROM'];
            }
            if ($valueCase == '') {
                $valueC = 1;
                $valueCase = $value['FROM'];
            }
            $dataRoutes[$numberRoutes]['ROU_CASE'] = $valueC;
            $dataRoutes[$numberRoutes]['ROU_TYPE'] = $value['ROU_TYPE'];
            if ($value['ROU_TYPE'] === 'DISCRIMINATOR') {
                $dataRoutes[$numberRoutes]['ROU_CONDITION'] = $value['CONDITION_DIS'];
                $dataRoutes[$numberRoutes]['ROU_OPTIONAL'] = $value['TYPE_DIS'];
            }
            if ($value['ROU_TYPE'] !== 'DISCRIMINATOR') {
                $dataRoutes[$numberRoutes]['ROU_CONDITION'] = $value['CONDITION'];
                $dataRoutes[$numberRoutes]['ROU_OPTIONAL'] = 'FALSE';
            }
            $dataRoutes[$numberRoutes]['ROU_TO_LAST_USER'] = 'FALSE';
            $dataRoutes[$numberRoutes]['ROU_SEND_EMAIL'] = 'TRUE';
            $dataRoutes[$numberRoutes]['ROU_SOURCEANCHOR'] = 1;
            $dataRoutes[$numberRoutes]['ROU_TARGETANCHOR'] = 0;
            $numberRoutes = $numberRoutes + 1;
        }
        return $dataRoutes;
    }

    /**
     * This public function creates an array for the lane that will be created according to the data given in an array
     *
     * @param array $lanes
     * @return array $lane
     */
    public function createLanesPM ($array, $idProcess)
    {
        $arrayLanes = array ();
        $field = array ();
        foreach ($array as $key => $value) {
            $field['ID_LANE'] = $value['0'];
            $field['ID_PROCESS'] = $idProcess;
            $field['X'] = $value['2'];
            $field['Y'] = $value['3'];
            $field['WIDTH'] = $value['4'];
            $field['HEIGHT'] = $value['5'];
            $field['TEXT'] = $value['6'];
            $arrayLanes[] = $this->createLanes( $field );
        }
        return $arrayLanes;
    }

    /**
     * This public function creates an array for the lane that will be created according to the data given in an array
     *
     * @param array $lanes
     * @return array $lane
     */
    public function createLanes ($lanes)
    {
        $lane = array ();
        $lane['SWI_UID'] = $lanes['ID_LANE'];
        $lane['PRO_UID'] = $lanes['ID_PROCESS'];
        $lane['SWI_TYPE'] = 'TEXT';
        $lane['SWI_X'] = $lanes['X'];
        $lane['SWI_Y'] = $lanes['Y'];
        $lane['SWI_TEXT'] = $lanes['TEXT'];
        $lane['SWI_WIDTH'] = $lanes['WIDTH'];
        $lane['SWI_HEIGHT'] = $lanes['HEIGHT'];
        return $lane;
    }

    /**
     * This public function creates an array for the lane that will be created according to the data given in an array
     *
     * @param array $lanes
     * @return array $lane
     */
    public function createLanesNewPM ($array)
    {
        $arrayLanes = array ();
        $field = array ();
        foreach ($array as $key => $value) {
            if ($value['SWI_TYPE'] == 'TEXT') {
                $field['0'] = $value['SWI_UID'];
                $field['1'] = $value['SWI_TEXT'];
                $field['2'] = $value['SWI_X'];
                $field['3'] = $value['SWI_Y'];
                $field['4'] = $value['SWI_WIDTH'];
                $field['5'] = $value['SWI_HEIGHT'];
                $field['6'] = $value['SWI_NEXT_UID'];
                $arrayLanes[] = $field;
            }
        }
        return $arrayLanes;
    }

    /**
     * This public function creates activities according the content of the node
     *
     * @param object $contentNode
     * @param string $idProcess
     * @param string $ActivitySet
     * @return array $result
     */
    public function createActivities ($contentNode, $idProcess, $activitySet)
    {
        $result = array ();
        $numberTasks = 0;
        $posRoutes = 0;
        $posEnd = 0;
        $posStart = 0;
        $arrayRoutes = array ();
        $endArray = array ();
        $endArray = array ();
        $startArray = array ();
        $arrayScheduler = array ();
        $scheduler = array ();
        $message = array ();
        $arrayMessages = array ();
        while ($contentNode) {
            if ($contentNode->nodeType == XML_ELEMENT_NODE) {
                $nameActivity = $contentNode->nodeName;
                $contentActivity = $contentNode->firstChild;
                if (strpos( $nameActivity, 'Activity' ) !== false) {
                    $idActivity = $contentNode->getAttribute( 'Id' );
                    $idActivity = trim( str_replace( '-', '', $idActivity ) );
                    $name = htmlentities( $contentNode->getAttribute( 'Name' ) );
                    $result = $this->createActivity( $contentActivity );
                    $result['ID_PROCESS'] = $idProcess;
                    $result['ID_TASK'] = $idActivity;
                    $result['TAS_TITLE'] = $name;
                    $result['TASK_TYPE'] = 'NORMAL';
                    foreach ($activitySet as $key => $value) {
                        if ($value['ID_PROCESS'] == $idActivity) {
                            $result['TASK_TYPE'] = 'SUBPROCESS';
                        }
                    }
                    if ($result['EVENT'] == '' and $result['ROUTE'] == '') {
                        $arrayTasks[$numberTasks] = $this->createTask( $result );
                        $numberTasks = $numberTasks + 1;
                    } else {
                        if ($result['EVENT'] != '' and $result['TRIGGER'] == 'None') {
                            if ($result['EVENT'] == 'END') {
                                $endArray[$posEnd] = $result;
                                $posEnd = $posEnd + 1;
                            }
                            if ($result['EVENT'] == 'START') {
                                $startArray[$posStart] = $result;
                                $posStart = $posStart + 1;
                            }
                        } else {
                            if ($result['TRIGGER'] == 'Timer') {
                                $scheduler['ID'] = $idActivity;
                                $scheduler['NAME'] = $name;
                                if ($result['TIME'] == '' or $result['TIME'] == null) {
                                    $scheduler['TIME'] = date( "D M j G:i:s T Y" );
                                } else {
                                    $scheduler['TIME'] = $result['TIME'];
                                }
                                $scheduler['TYPE_TIME'] = $result['TYPE_TIME'];
                                $arrayScheduler[] = $scheduler;
                            }
                            if ($result['TRIGGER'] == 'Message') {
                                $message['ID'] = $idActivity;
                                $message['NAME'] = $name;
                                $message['TYPE_MESSAGE'] = $result['MESSAGE'];
                                $arrayMessages[] = $message;
                            }
                        }
                        if ($result['ROUTE'] != '') {
                            $position = $this->findCoordinates( $contentActivity );
                            $arrayRoutes[$posRoutes]['ID'] = $idActivity;
                            $arrayRoutes[$posRoutes]['ROUTE'] = $result['ROUTE'];
                            $arrayRoutes[$posRoutes]['TYPE_DIS'] = $result['TYPE_DISCRIMINATOR'];
                            $arrayRoutes[$posRoutes]['CONDITION_DIS'] = $result['CONDITION_DISCRIMINATOR'];
                            $arrayRoutes[$posRoutes]['X'] = $position['X'];
                            $arrayRoutes[$posRoutes]['Y'] = $position['Y'];
                            $posRoutes = $posRoutes + 1;
                        }
                    }
                }
            }
            $contentNode = $contentNode->nextSibling;
        }
        $result['TASKS'] = $arrayTasks;
        $result['END'] = $endArray;
        $result['START'] = $startArray;
        $result['ROUTES'] = $arrayRoutes;
        $result['SCHEDULER'] = $arrayScheduler;
        $result['MESSAGES'] = $arrayMessages;
        return $result;
    }

    /**
     * This public function creates the scheduler
     *
     * @param array $fields
     * @return array $result
     */
    public function createEventMessages ($fields, $idProcess)
    {
        $result = array ();
        $event = array ();
        $example = new Derivation();
        foreach ($fields as $key => $value) {
            $oEvent = new Event();
            $event['EVN_UID'] = $value['ID'];
            $event['PRO_UID'] = $idProcess;
            $event['EVN_RELATED_TO'] = 'SINGLE';
            $event['TAS_UID'] = $value['ID_TASK'];
            $event['EVN_TAS_ESTIMATED_DURATION'] = '1';
            $event['EVN_WHEN_OCCURS'] = 'AFTER';
            $event['EVN_STATUS'] = 'ACTIVE';
            $event['EVN_WHEN'] = 1;
            $event['EVN_MAX_ATTEMPTS'] = 3;
            $event['EVN_ACTION'] = 'SEND_MESSAGE';
            $event['EVN_DESCRIPTION'] = $value['NAME'];
            $event['EVN_WHEN_OCCURS'] = 'AFTER_TIME';
            $event['EVN_ACTION_PARAMETERS'] = array ('SUBJECT' => 'subject','TO' => array ('0' => 'usr|-1','1' => 'ext|'
            ),'CC' => array (),'BCC' => array (),'TEMPLATE' => 'alert_message.html'
            );
            $result[] = $event;
        }
        return $result;
    }

    /**
     * This public function creates the scheduler
     *
     * @param array $fields
     * @return array $result
     */
    public function createScheduler ($fields, $idProcess)
    {
        $result = array ();
        $example = new Derivation();
        foreach ($fields as $key => $value) {
            if ($value['TYPE_TIME'] == 'TimeDate') {
                //there is a scheduler one time only
                $value['OPTION'] = 4;
                $result[] = $this->createArrayScheduler( $value, $idProcess );
            }
            if ($value['TYPE_TIME'] == 'TimeCycle') {
                //we calculated if is daily or monthly, etc
                $time = $this->calculateTimeScheduler( $value['TIME'] );
                /*$value['OPTION']= $time['OPTION'];
                $value['TIME']  = $time['TIME'];*/
                $value['OPTION'] = '1';
            }
        }
        //PRINT_r($result);
        return $result;
    }

    /**
     * This public function creates the scheduler
     *
     * @param array $fields
     * @return array $result
     */
    public function calculateTimeScheduler ($time)
    {
        $result = array ();
        // The split public function has been DEPRECATED as of PHP 5.3.0.
        // $result = split(' ',$time);
        $result = explode( ' ', $time );
        //print_r($result);
        return $result;
    }

    /**
     * This public function creates the array scheduler
     *
     * @param string $time
     * @param string $type (TimeCycle or TimeDate)
     * @return array $result
     */
    public function createArrayScheduler ($fields, $idProcess)
    {
        $result['SCH_UID'] = $fields['ID'];
        $result['SCH_DEL_USER_NAME'] = '';
        $result['SCH_DEL_USER_PASS'] = '';
        $result['SCH_DEL_USER_UID'] = '';
        $result['SCH_NAME'] = $fields['NAME'];
        $result['PRO_UID'] = $idProcess;
        $result['TAS_UID'] = $fields['ID_TASK'];
        $result['SCH_TIME_NEXT_RUN'] = $fields['TIME'];
        $result['SCH_LAST_RUN_TIME'] = null;
        $result['SCH_STATE'] = 'ACTIVE';
        $result['SCH_LAST_STATE'] = 'CREATED';
        $result['USR_UID'] = '00000000000000000000000000000001';
        $result['SCH_OPTION'] = $fields['OPTION'];
        $result['SCH_START_TIME'] = $fields['TIME'];
        $result['SCH_START_DATE'] = $fields['TIME'];
        $result['SCH_DAYS_PERFORM_TASK'] = '';
        $result['SCH_EVERY_DAYS'] = '0';
        $result['SCH_WEEK_DAYS'] = '0|0|0|0|0|0|0';
        $result['SCH_START_DAY'] = '';
        $result['SCH_MONTHS'] = '0|0|0|0|0|0|0|0|0|0|0|0';
        $result['SCH_END_DATE'] = $fields['TIME'];
        $result['SCH_REPEAT_EVERY'] = '';
        $result['SCH_REPEAT_UNTIL'] = '';
        $result['SCH_REPEAT_STOP_IF_RUNNING'] = '';
        return $result;
    }

    /**
     * This public function creates transitions according the content of the node
     *
     * @param object $contentNode
     * @param array $dataTask
     * @param array $arrayRoutes
     * @param array $endArray
     * @param array $startArray
     * @param string $idProcess
     * @return array $result
     */

    public function createTransitions ($contentNode, $dataTasks, $arrayRoutes, $endArray, $startArray, $idProcess, $schedulerArray, $messages)
    {
        $numberRoutes = 0;
        $posT = 0;
        $output = '';
        $routeTransitions = array ();
        $taskHidden = array ();
        $countHidden = 1;
        $dataRoutes = array ();
        $newFrom = 0;
        $isScheduler = 0;
        $isMessage = 0;
        while ($contentNode) {
            $isEnd = 0;
            $isStart = 0;
            $tasks = $dataTasks;
            if ($contentNode->nodeType == XML_ELEMENT_NODE) {
                $nameTransition = $contentNode->nodeName;
                $contentTransition = $contentNode->firstChild;
                if (strpos( $nameTransition, 'Transition' ) !== false) {
                    $idTransition = $contentNode->getAttribute( 'Id' );
                    $idTransition = trim( str_replace( '-', '', $idTransition ) );
                    $from = trim( str_replace( '-', '', $contentNode->getAttribute( 'From' ) ) );
                    $to = trim( str_replace( '-', '', $contentNode->getAttribute( 'To' ) ) );
                    $routes['ROU_UID'] = $idTransition;
                    $routes['ID_PROCESS'] = $idProcess;
                    $routes['FROM'] = $from;
                    $routes['TO'] = $to;
                    $isScheduler = 0;
                    $isMessage = 0;
                    foreach ($startArray as $startBase => $value) {
                        if ($value['ID_TASK'] == $from) {
                            foreach ($tasks as $tasksValue => $taskId) {
                                if ($to == $taskId['TAS_UID']) {
                                    $taskId['TAS_START'] = 'TRUE';
                                    $dataTasks[$tasksValue]['TAS_START'] = 'TRUE';
                                    $isStart = 1;
                                }
                            }
                        }
                    }
                    foreach ($schedulerArray as $startBase => $value) {
                        if ($value['ID'] == $from) {
                            $isScheduler = 1;
                            $schedulerArray[$startBase]['ID_TASK'] = $to;
                            foreach ($tasks as $tasksValue => $taskId) {
                                if ($to == $taskId['TAS_UID']) {
                                    $taskId['TAS_START'] = 'TRUE';
                                    $dataTasks[$tasksValue]['TAS_START'] = 'TRUE';
                                    $isStart = 1;
                                }
                            }
                        }
                    }
                    foreach ($messages as $startBase => $value) {
                        if ($value['ID'] == $from) {
                            $isMessage = 1;
                            $messages[$startBase]['ID_TASK'] = $to;
                        }
                        if ($value['ID'] == $to) {
                            $isMessage = 1;
                            $messages[$startBase]['ID_TASK'] = $from;
                        }
                    }
                    $sequencial = 0;
                    $findRoute = 0;
                    foreach ($arrayRoutes as $routeBase => $value) {
                        //change for task hidden
                        if ($value['ID'] == $from) {
                            if ($findRoute == 0) {
                                $typeT = 'FROM';
                                $valueRoute = $value['ID'];
                                $typeRoute = $value['ROUTE'];
                                $sequencial = 1;
                                $conditionD = $value['CONDITION_DIS'];
                                $typeD = $value['TYPE_DIS'];
                                $findRoute = 1;
                            } else {
                                $contendHidden = $contentTransition;
                                $fieldsXY = $this->findCoordinatesTransition( $to, $arrayRoutes );
                                $hidden['ID_TASK'] = G::generateUniqueID();
                                $hidden['ID_PROCESS'] = $idProcess;
                                $hidden['TAS_TITLE'] = '';
                                $hidden['TASK_TYPE'] = 'HIDDEN';
                                $hidden['DESCRIPTION'] = $countHidden;
                                $hidden['X'] = $fieldsXY['X'];
                                $hidden['Y'] = $fieldsXY['Y'];
                                $taskHidden[] = $hidden;
                                $dataTasks[] = $this->createTask( $hidden );
                                $countHidden = $countHidden + 1;
                                $routeTransitions[$posT]['ID'] = G::generateUniqueID();
                                $routeTransitions[$posT]['ROUTE'] = $from;
                                $routeTransitions[$posT]['FROM'] = $from;
                                $routeTransitions[$posT]['TO'] = $hidden['ID_TASK'];
                                $routeTransitions[$posT]['TOORFROM'] = 'FROM';
                                $routeTransitions[$posT]['ROU_TYPE'] = $value['ROUTE'];
                                $routeTransitions[$posT]['CONDITION'] = $output;
                                $routeTransitions[$posT]['CONDITION_DIS'] = $conditionD;
                                $routeTransitions[$posT]['TYPE_DIS'] = $typeD;
                                $output = '';
                                $posT = $posT + 1;
                                $from = $hidden['ID_TASK'];
                            }
                        }
                        if ($value['ID'] == $to) {
                            if ($findRoute == 0) {
                                $typeT = 'TO';
                                $valueRoute = $value['ID'];
                                $typeRoute = $value['ROUTE'];
                                $sequencial = 1;
                                $conditionD = $value['CONDITION_DIS'];
                                $typeD = $value['TYPE_DIS'];
                                $findRoute = 1;
                            } else {

                                $contendHidden = $contentTransition;
                                $fieldsXY = $this->findCoordinatesTransition( $to, $arrayRoutes );
                                $hidden['ID_TASK'] = G::generateUniqueID();
                                $hidden['ID_PROCESS'] = $idProcess;
                                $hidden['TAS_TITLE'] = '';
                                $hidden['TASK_TYPE'] = 'HIDDEN';
                                $hidden['DESCRIPTION'] = $countHidden;
                                $hidden['X'] = $fieldsXY['X'];
                                $hidden['Y'] = $fieldsXY['Y'];
                                $taskHidden[] = $hidden;
                                $dataTasks[] = $this->createTask( $hidden );
                                $countHidden = $countHidden + 1;
                                $routeTransitions[$posT]['ID'] = G::generateUniqueID();
                                $routeTransitions[$posT]['ROUTE'] = $from;
                                $routeTransitions[$posT]['FROM'] = $from;
                                $routeTransitions[$posT]['TO'] = $hidden['ID_TASK'];
                                $routeTransitions[$posT]['TOORFROM'] = 'TO';
                                $routeTransitions[$posT]['ROU_TYPE'] = $typeRoute;
                                $routeTransitions[$posT]['CONDITION'] = $output;
                                $routeTransitions[$posT]['CONDITION_DIS'] = $conditionD;
                                $routeTransitions[$posT]['TYPE_DIS'] = $typeD;
                                $output = '';
                                $posT = $posT + 1;
                                $from = $hidden['ID_TASK'];
                                $conditionD = $value['CONDITION_DIS'];
                                $typeRoute = $value['ROUTE'];
                                $typeT = 'TO';
                                $valueRoute = $value['ID'];
                                $sequencial = 1;
                                $conditionD = $value['CONDITION_DIS'];
                                $typeD = $value['TYPE_DIS'];
                            }
                        }
                    }
                    if ($sequencial == 0) {
                        foreach ($endArray as $endBase => $value) {
                            if ($value['ID_TASK'] == $to) {
                                foreach ($tasks as $tasksValue => $taskId) {
                                    if ($from == $taskId['TAS_UID']) {
                                        $routes['TO'] = '-1';
                                        $dataRoutes[$numberRoutes] = $this->createRoute( $routes );
                                        $numberRoutes = $numberRoutes + 1;
                                        $isEnd = 1;
                                    }
                                }
                            }
                        }
                    }
                    if ($sequencial == 1) {
                        while ($contentTransition) {
                            $nameCondition = $contentTransition->nodeName;
                            $contentCondition = $contentTransition->firstChild;
                            if (strpos( $nameCondition, 'Condition' ) !== false) {
                                while ($contentCondition) {
                                    $nameExpression = $contentCondition->nodeName;
                                    $contentCondition1 = '';
                                    if (strpos( $nameExpression, 'Expression' ) !== false) {
                                        $contentCondition1 = $contentCondition->firstChild;
                                        if ($contentCondition1 != array ()) {
                                            $output = $contentCondition1->nodeValue;
                                        }
                                    }
                                    $contentCondition = $contentCondition->nextSibling;
                                }
                            }
                            $contentTransition = $contentTransition->nextSibling;
                        }
                        $routeTransitions[$posT]['ID'] = $idTransition;
                        $routeTransitions[$posT]['ROUTE'] = $valueRoute;
                        $routeTransitions[$posT]['FROM'] = $from;
                        $routeTransitions[$posT]['TO'] = $to;
                        $routeTransitions[$posT]['TOORFROM'] = $typeT;
                        $routeTransitions[$posT]['ROU_TYPE'] = $typeRoute;
                        $routeTransitions[$posT]['CONDITION'] = $output;
                        $routeTransitions[$posT]['CONDITION_DIS'] = $conditionD;
                        $routeTransitions[$posT]['TYPE_DIS'] = $typeD;
                        $output = '';
                        $posT = $posT + 1;
                        $sequencial = 1;
                    }
                    if ($isEnd == 0 and $isStart == 0 and $sequencial == 0 and $isScheduler == 0 and $isMessage == 0) {
                        $dataRoutes[$numberRoutes] = $this->createRoute( $routes );
                        $numberRoutes = $numberRoutes + 1;
                    }
                }
            }
            $contentNode = $contentNode->nextSibling;
        }
        $routes = $routeTransitions;
        foreach ($routeTransitions as $key => $id) {
            $findTo = 0;
            $findFrom = 0;
            foreach ($dataTasks as $keyHidden => $value) {
                if ($id['FROM'] == $value['TAS_UID']) {
                    $findFrom = 1;
                }
                if ($id['TO'] == $value['TAS_UID']) {
                    $findTo = 1;
                }
            }
            if ($findTo == 0) {
                foreach ($routes as $keyR => $idR) {
                    if ($id['TO'] == $idR['ROUTE']) {
                        $routeTransitions[$key]['ROU_TYPE'] = $idR['ROU_TYPE'];
                        $routeTransitions[$key]['ROUTE'] = $id['TO'];
                        $routeTransitions[$key]['TOORFROM'] = 'TO';
                    }
                }
            }
            if ($findFrom == 0) {
                foreach ($routes as $keyR => $idR) {
                    if ($id['FROM'] == $idR['ROUTE']) {
                        $routeTransitions[$key]['ROU_TYPE'] = $idR['ROU_TYPE'];
                        $routeTransitions[$key]['ROUTE'] = $id['FROM'];
                        $routeTransitions[$key]['TOORFROM'] = 'FROM';
                    }
                }
            }
        }
        $result['ROUTES'] = $dataRoutes;
        $result['TRANSITIONS'] = $routeTransitions;
        $result['TASKS'] = $dataTasks;
        $result['NUMBER'] = $numberRoutes;
        $result['TASKHIDDEN'] = $taskHidden;
        $result['SCHEDULER'] = $schedulerArray;
        $result['MESSAGES'] = $messages;
        return $result;
    }

    /**
     * this public function creates a array according to the data given in a node
     *
     * @param object $contentNode
     * @return array
     */
    public function createActivity ($contentNode)
    {
        $coordinates = array ();
        $event = '';
        $typeRoute = '';
        $markerVisible = '';
        $typePM = '';
        $isRoute = 0;
        $description = '';
        $isSubProcess = 0;
        $trigger = '';
        $time = '';
        $typeTime = '';
        $isMessage = 0;
        while ($contentNode) {
            if ($contentNode->nodeType == XML_ELEMENT_NODE) {
                $nameChild = $contentNode->nodeName;
                $contentChild = $contentNode->firstChild;
                if (strpos( $nameChild, 'Description' ) !== false) {
                    $description = $contentNode->nodeValue;
                }
                if (strpos( $nameChild, 'ExtendedAttributes' ) !== false) {
                    $result = $this->createExtended( $contentChild );
                }
                if (strpos( $nameChild, 'BlockActivity' ) !== false) {
                    $isSubProcess = 1;
                    $idSubProcess = trim( str_replace( '-', '', $contentNode->getAttribute( 'ActivitySetId' ) ) );
                }
                if (strpos( $nameChild, 'Event' ) !== false) {
                    while ($contentChild) {
                        if ($contentChild->nodeType == XML_ELEMENT_NODE) {
                            $nameInfo = $contentChild->nodeName;
                            $contentInfo = $contentChild->firstChild;
                            if (strpos( $nameInfo, 'StartEvent' ) !== false) {
                                $event = 'START';
                                $trigger = $contentChild->getAttribute( 'Trigger' );
                            }
                            if (strpos( $nameInfo, 'EndEvent' ) !== false) {
                                $event = 'END';
                                $trigger = $contentChild->getAttribute( 'Trigger' );
                                if ($trigger == '') {
                                    $trigger = 'None';
                                }
                            }
                            if (strpos( $nameInfo, 'IntermediateEvent' ) !== false) {
                                $event = 'INTERMEDIATE';
                                $trigger = $contentChild->getAttribute( 'Trigger' );
                            }
                        }
                        $contentChild = $contentChild->nextSibling;
                    }
                    if ($trigger != 'None') {
                        if ($trigger == 'Timer') {
                            while ($contentInfo) {
                                $nameTrigger = $contentInfo->nodeName;
                                $contentTrigger = $contentInfo->firstChild;
                                if (strpos( $nameTrigger, 'TriggerTimer' ) !== false) {
                                    $time = $contentInfo->getAttribute( 'TimeCycle' );
                                    $typeTime = 'TimeCycle';
                                    if ($time == '') {
                                        $time = $contentInfo->getAttribute( 'TimeDate' );
                                        $typeTime = 'TimeDate';
                                    }
                                }
                                $contentInfo = $contentInfo->nextSibling;
                            }
                        }
                        if ($trigger == 'Message') {
                            $typeMessage = '';
                            $isMessage = 1;
                            while ($contentInfo) {
                                $nameTrigger = $contentInfo->nodeName;
                                $contentTrigger = $contentInfo->firstChild;
                                if (strpos( $nameTrigger, 'TriggerResultMessage' ) !== false) {
                                    $typeMessage = $contentInfo->getAttribute( 'CatchThrow' );
                                }
                                $contentInfo = $contentInfo->nextSibling;
                            }
                        }
                    }
                }

                if (strpos( $nameChild, 'Route' ) !== false) {
                    $typePM = '';
                    $typeRoute = $contentNode->getAttribute( 'GatewayType' );
                    $markerVisible = $contentNode->getAttribute( 'MarkerVisible' );
                    if ($markerVisible != '') {
                        $coordinates['ROUTE'] = $markerVisible;
                    }
                    if ($typeRoute != '') {
                        $coordinates['ROUTE'] = $typeRoute;
                    }
                    if ($typeRoute == '' and $markerVisible == '') {
                        $coordinates['ROUTE'] = 'EVALUATE';
                    }
                    switch ($coordinates['ROUTE']) {
                        case 'AND':
                            $typePM = 'PARALLEL';
                            break;
                        case 'true':
                            $typePM = 'EVALUATE';
                            break;
                        case 'EVALUATE':
                            $typePM = 'EVALUATE';
                            break;
                        case 'OR':
                            $typePM = 'PARALLEL-BY-EVALUATION';
                            break;
                        case 'Complex':
                            $typePM = 'DISCRIMINATOR';
                            break;
                    }
                    $isRoute = 1;
                }
                if (strpos( $nameChild, 'NodeGraphicsInfos' ) !== false) {
                    $coordinates = $this->findCoordinates( $contentNode );
                    $coordinates['EVENT'] = $event;
                    $coordinates['TRIGGER'] = $trigger;
                    $coordinates['TIME'] = $time;
                    $coordinates['TYPE_TIME'] = $typeTime;
                    $coordinates['DESCRIPTION'] = $description;
                    $coordinates['TYPE_DISCRIMINATOR'] = $result['TYPE'];
                    $coordinates['CONDITION_DISCRIMINATOR'] = $result['CONDITION'];
                    if ($isRoute == 1) {
                        $coordinates['ROUTE'] = $typePM;
                    } else {
                        $coordinates['ROUTE'] = '';
                    }
                    if ($isMessage == 1) {
                        $coordinates['MESSAGE'] = $typeMessage;
                    } else {
                        $coordinates['MESSAGE'] = '';
                    }
                }
            }
            $contentNode = $contentNode->nextSibling;
        }
        return $coordinates;
    }

    /**
     * This public function return the type and the condition of a discriminator
     *
     * @param object $contentNode
     * @return array
     */
    public function createExtended ($contentNode)
    {
        $result = array ();
        $result['TYPE'] = '';
        $result['CONDITION'] = '';
        $contentExtended = $contentNode;
        while ($contentExtended) {
            if ($contentExtended->nodeType == XML_ELEMENT_NODE) {
                $nameChildExtended = $contentExtended->nodeName;
                $contentChildExtended = $contentExtended->firstChild;
                if (strpos( $nameChildExtended, 'ExtendedAttribute' ) !== false) {
                    $name = $contentExtended->getAttribute( 'Name' );
                    if ($name == 'option') {
                        $result['TYPE'] = $contentExtended->getAttribute( 'Value' );
                    }
                    if ($name == 'condition') {
                        $result['CONDITION'] = $contentExtended->getAttribute( 'Value' );
                    }
                }
            }
            $contentExtended = $contentExtended->nextSibling;
        }
        return $result;
    }

    /**
     * This public function find the coordinates of a object
     *
     * @param object $contentNode
     * @return array
     */
    public function findCoordinates ($contentNode)
    {
        $coordinates = array ();
        while ($contentNode) {
            if ($contentNode->nodeType == XML_ELEMENT_NODE) {
                $nameChild = $contentNode->nodeName;
                $contentChild = $contentNode->firstChild;
                if (strpos( $nameChild, 'NodeGraphicsInfos' ) !== false) {
                    while ($contentChild) {
                        if ($contentChild->nodeType == XML_ELEMENT_NODE) {
                            $nameInfo = $contentChild->nodeName;
                            $contentInfo = $contentChild->firstChild;
                            if (strpos( $nameInfo, 'NodeGraphicsInfo' ) !== false) {
                                $coordinates['Height'] = $contentChild->getAttribute( 'Height' );
                                $coordinates['Width'] = $contentChild->getAttribute( 'Width' );
                                $coordinates['BorderColor'] = $contentChild->getAttribute( 'BorderColor' );
                                $coordinates['FillColor'] = $contentChild->getAttribute( 'FillColor' );
                                while ($contentInfo) {
                                    $nameCoordinate = $contentInfo->nodeName;
                                    $contentCoordinate = $contentInfo->firstChild;

                                    if (strpos( $nameCoordinate, 'Coordinates' ) !== false) {
                                        $coordinates['X'] = $contentInfo->getAttribute( 'XCoordinate' );
                                        $coordinates['Y'] = $contentInfo->getAttribute( 'YCoordinate' );
                                    }
                                    $contentInfo = $contentInfo->nextSibling;
                                }

                            }
                        }
                        $contentChild = $contentChild->nextSibling;
                    }
                }
            }
            $contentNode = $contentNode->nextSibling;
        }
        return $coordinates;
    }

    /**
     * This public function find the coordinates of a object
     *
     * @param string $idRoute
     * @param array $aRoutes
     * @return array
     */
    public function findCoordinatesTransition ($idRoute, $aRoutes)
    {
        $coordinates = array ();
        $size = sizeof( $aRoutes );
        $count = 0;
        $find = 0;
        while ($count < $size and $find == 0) {
            if ($aRoutes[$count]['ID'] == $idRoute) {
                $coordinates['X'] = $aRoutes[$count]['X'];
                $coordinates['Y'] = $aRoutes[$count]['Y'];
                $find = 1;
            }
            $count = $count + 1;
        }
        return $coordinates;
    }

    /**
     * This public function create the array Routes
     *
     * @param array $dataTasks
     * @param array $arrayRoutes
     * @param array $aEvents
     * @param array $aGateways
     * @param string $idProcess
     * @return array
     */
    public function createArrayRoutes ($dataTasks, $arrayRoutes, $aEvents, $aGateways, $aEnd, $idProcess)
    {
        $numberRoutes = 0;
        $posT = 0;
        $routeTransitions = array ();
        $dataRoutes = array ();
        $findRoute = 0;
        $hidden = array ();
        $taskHidden = array ();
        $countHidden = 0;
        foreach ($arrayRoutes as $idRoute => $valueRoute) {
            $idTransition = $valueRoute['0'];
            $from = $valueRoute['1'];
            $to = $valueRoute['2'];
            $toPort = $valueRoute['3'];
            $fromPort = $valueRoute['4'];
            $routes['ROU_UID'] = $idTransition;
            $routes['ID_PROCESS'] = $idProcess;
            $routes['FROM'] = $from;
            $routes['TO'] = $to;
            $sequencial = 0;
            $isEnd = 0;
            $typeRoute = '';
            $typePM = '';
            $findRoute = 0;
            $isEventIntermediate = 0;
            foreach ($aGateways as $routeBase => $value) {
                if ($value['0'] == $from) {
                    if ($findRoute == 0) {
                        $typeT = 'FROM';
                        $valueRoute = $value['0'];
                        $typeRoute = $value['1'];
                        $positionX = $value['2'];
                        $positionY = $value['3'];
                        $sequencial = 1;
                        $findRoute = 1;
                    } else {
                        $hidden['ID_TASK'] = G::generateUniqueID();
                        $hidden['ID_PROCESS'] = $idProcess;
                        $hidden['TAS_TITLE'] = '';
                        $hidden['TASK_TYPE'] = 'HIDDEN';
                        $hidden['DESCRIPTION'] = $countHidden;
                        $hidden['X'] = $positionX;
                        $hidden['Y'] = $positionY;
                        $taskHidden[] = $hidden;
                        $dataTasks[] = $this->createTask( $hidden );
                        $countHidden = $countHidden + 1;
                        $routeTransitions[$posT]['ID'] = G::generateUniqueID();
                        $routeTransitions[$posT]['ROUTE'] = $from;
                        $routeTransitions[$posT]['FROM'] = $from;
                        $routeTransitions[$posT]['TO'] = $hidden['ID_TASK'];
                        $routeTransitions[$posT]['TOORFROM'] = 'FROM';
                        $routeTransitions[$posT]['ROU_TYPE'] = $value['1'];
                        $routeTransitions[$posT]['CONDITION'] = '';
                        $routeTransitions[$posT]['CONDITION_DIS'] = '';
                        $routeTransitions[$posT]['TYPE_DIS'] = '';
                        $posT = $posT + 1;
                        $from = $hidden['ID_TASK'];
                    }
                }
                if ($value['0'] == $to) {
                    if ($findRoute == 0) {
                        $typeT = 'TO';
                        $valueRoute = $value['0'];
                        $typeRoute = $value['1'];
                        $positionX = $value['2'];
                        $positionY = $value['3'];
                        $sequencial = 1;
                        $findRoute = 1;
                    } else {
                        $hidden['ID_TASK'] = G::generateUniqueID();
                        $hidden['ID_PROCESS'] = $idProcess;
                        $hidden['TAS_TITLE'] = '';
                        $hidden['TASK_TYPE'] = 'HIDDEN';
                        $hidden['DESCRIPTION'] = $countHidden;
                        $hidden['X'] = $positionX;
                        $hidden['Y'] = $positionY;
                        $taskHidden[] = $hidden;
                        $dataTasks[] = $this->createTask( $hidden );
                        $countHidden = $countHidden + 1;
                        $routeTransitions[$posT]['ID'] = G::generateUniqueID();
                        $routeTransitions[$posT]['ROUTE'] = $from;
                        $routeTransitions[$posT]['FROM'] = $from;
                        $routeTransitions[$posT]['TO'] = $hidden['ID_TASK'];
                        $routeTransitions[$posT]['TOORFROM'] = 'TO';
                        $routeTransitions[$posT]['ROU_TYPE'] = $typeRoute;
                        $routeTransitions[$posT]['CONDITION'] = '';
                        $routeTransitions[$posT]['CONDITION_DIS'] = '';
                        $routeTransitions[$posT]['TYPE_DIS'] = '';
                        $posT = $posT + 1;
                        $from = $hidden['ID_TASK'];
                        $typeT = 'TO';
                        $valueRoute = $value['0'];
                        $typeRoute = $value['1'];
                        $sequencial = 1;
                    }
                }
            }
            if ($sequencial == 0) {
                foreach ($aEvents as $id => $value) {
                    if ($routes['TO'] == $value['0']) {
                        if ($value['1'] === 'bpmnEventEndSignal' or $value['1'] === 'bpmnEventMessageEnd') {
                            $routes['TO'] = '-1';
                            $dataRoutes[$numberRoutes] = $this->createRoute( $routes );
                            $numberRoutes = $numberRoutes + 1;
                            $isEnd = 1;
                        } else {
                            $isEventIntermediate = 1;
                        }
                    }
                }
            }
            if ($sequencial == 1) {
                $routeTransitions[$posT]['ID'] = $idTransition;
                $routeTransitions[$posT]['ROUTE'] = $valueRoute;
                $routeTransitions[$posT]['FROM'] = $from;
                $routeTransitions[$posT]['TO'] = $to;
                $routeTransitions[$posT]['TOORFROM'] = $typeT;
                $routeTransitions[$posT]['ROU_TYPE'] = $typeRoute;
                $routeTransitions[$posT]['CONDITION'] = '';
                $routeTransitions[$posT]['CONDITION_DIS'] = '';
                $routeTransitions[$posT]['TYPE_DIS'] = '';
                $posT = $posT + 1;
                $sequencial = 1;
            }
            if ($isEnd == 0 and $sequencial == 0 and $isEventIntermediate == 0) {
                $dataRoutes[$numberRoutes] = $this->createRoute( $routes );
                $numberRoutes = $numberRoutes + 1;
            }
        }
        foreach ($routeTransitions as $id => $key) {
            $typeRoute = $key['ROU_TYPE'];
            switch ($typeRoute) {
                case 'bpmnGatewayExclusiveData':
                    $typePM = 'EVALUATE';
                    break;
                case 'bpmnGatewayInclusive':
                    $typePM = 'PARALLEL-BY-EVALUATION';
                    break;
                case 'bpmnGatewayExclusiveEvent':
                    $typePM = 'EVALUATE';
                    break;
                case 'bpmnGatewayComplex':
                    $typePM = 'DISCRIMINATOR';
                    break;
                case 'bpmnGatewayParallel':
                    $typePM = 'PARALLEL';
                    break;
            }
            $routeTransitions[$id]['ROU_TYPE'] = $typePM;
        }
        $routes = $routeTransitions;
        foreach ($routeTransitions as $key => $id) {
            $findTo = 0;
            $findFrom = 0;
            foreach ($dataTasks as $keyHidden => $value) {
                if ($id['FROM'] == $value['TAS_UID']) {
                    $findFrom = 1;
                }
                if ($id['TO'] == $value['TAS_UID']) {
                    $findTo = 1;
                }
            }
            if ($findTo == 0) {
                foreach ($routes as $keyR => $idR) {
                    if ($id['TO'] == $idR['ROUTE']) {
                        $routeTransitions[$key]['ROU_TYPE'] = $idR['ROU_TYPE'];
                        $routeTransitions[$key]['ROUTE'] = $id['TO'];
                        $routeTransitions[$key]['TOORFROM'] = 'TO';
                    }
                }
            }
            if ($findFrom == 0) {
                foreach ($routes as $keyR => $idR) {
                    if ($id['FROM'] == $idR['ROUTE']) {
                        $routeTransitions[$key]['ROU_TYPE'] = $idR['ROU_TYPE'];
                        $routeTransitions[$key]['ROUTE'] = $id['FROM'];
                        $routeTransitions[$key]['TOORFROM'] = 'FROM';
                    }
                }
            }
        }
        $result['ROUTES'] = $dataRoutes;
        $result['TRANSITIONS'] = $routeTransitions;
        $result['NUMBER'] = $numberRoutes;
        $result['TASKS'] = $dataTasks;
        $result['HIDDEN'] = $taskHidden;
        return $result;
    }

    /**
     * This public function convert the array events in a array with the events end
     *
     * @param object $aEvents
     * @param string $idProcess
     * @return array
     */
    public function convertArrayEnd ($aEvents, $idProcess)
    {
        $result = array ();
        $posEnd = 1;
        $endArray = array ();
        foreach ($aEvents as $id => $value) {
            if ($value['1'] === 'bpmnEventEndSignal' or $value['1'] === 'bpmnEventMessageEnd') {
                $result['ID_PROCESS'] = $idProcess;
                $result['ID_TASK'] = $value['0'];
                $endArray[$posEnd] = $result;
                $posEnd = $posEnd + 1;
            }
        }
        return $endArray;
    }

    /**
     * This public function create transitions from the array transitions for the new processmap
     *
     * @param array $task
     * @param array $routes
     * @param array $events
     * @param array $countEvents
     * @param array $arrayRoutes
     * @param array $countRoutes
     * @return array
     */
    public function createTransitionsPM ($tasks, $routes, $events, $countEvents, $arrayRoutes, $countRoutes)
    {
        $cont = 0;
        $dataRoutes = '';
        $endEvent = 1;
        $taskParallel = '';
        $routeParallel = '';
        $taskSecJoin = '';
        $routeSecJoin = '';
        $taskDiscriminator = '';
        $taskEvaluate = '';
        $routeEvaluate = '';
        $taskParallelEv = '';
        $routeParallelEv = '';
        $taskSelect = '';
        $routeSelect = '';
        $routeEnd = '';
        $arraySecJoin = array ();
        $position = 0;
        $fillColor = '';
        $transitions = '';
        $arrayGateways = array ();
        $countG = 0;
        $gatPosX = 0;
        $gatPosY = 0;

        foreach ($routes as $key => $row) {
            if ($row['ROU_TYPE'] == 'SEC-JOIN') {
                $arraySecJoin[$position] = array ();
                $arraySecJoin[$position] = $row;
                $position = $position + 1;
                unset( $routes[$key] );
            }
        }
        $aux = $arraySecJoin;
        foreach ($arraySecJoin as $key => $row) {
            $aux[$key] = $row['ROU_NEXT_TASK'];
        }
        if (sizeof( $arraySecJoin ) > 0) {
            array_multisort( $aux, SORT_ASC, $arraySecJoin );
            unset( $aux );
        }
        foreach ($routes as $key => $row) {
            $uid[$key] = $row['TAS_UID'];
            $case[$key] = $row['ROU_CASE'];
        }
        if (sizeof( $routes ) > 0) {
            array_multisort( $uid, SORT_ASC, $case, SORT_ASC, $routes );
        }
        $routes = array_merge( $routes, $arraySecJoin );
        foreach ($routes as $key => $val) {
            $end = 0;
            $idRoute = $val['ROU_UID'];
            $idTask = $val['TAS_UID'];
            $nextTask = $val['ROU_NEXT_TASK'];
            $condition = htmlentities( $val['ROU_CONDITION'] );
            $toPort = $val['ROU_TO_PORT'];
            $fromPort = $val['ROU_FROM_PORT'];
            //If End Event
            if ($nextTask == "-1") {
                $end = 1;
            }
            $typeRoute = $val['ROU_TYPE'];
            $route = '';

            //Get GAT_UID from ROUTE table based on ROU_UID
            $oRoute = new Route();
            $aRouteDetails = $oRoute->load( $idRoute );
            $sGateUID = $aRouteDetails['GAT_UID'];

            //Get Gateway details from above GAT_UID
            $oGateway = new Gateway();
            if ($sGateUID != '') {
                $aGatewayDetails = $oGateway->load( $sGateUID );
                $gatPosX = $aGatewayDetails['GAT_X'];
                $gatPosY = $aGatewayDetails['GAT_Y'];
            }

            //if route is of SEQUENTIAL type,assign route id to GAT_UID
            if ($sGateUID == '') {
                $sGateUID = $idRoute;
            }
            if ($typeRoute != "SEQUENTIAL") {
                switch ($typeRoute) {
                    case 'PARALLEL':
                        $coordinateX = 0;
                        $coordinateY = 0;
                        foreach ($tasks as $taskVal => $idVal) {
                            if ($idVal['TAS_UID'] == $idTask) {
                                $coordinateX = $idVal['TAS_POSX'] + 60;
                                $coordinateY = $idVal['TAS_POSY'] + 60;
                                $tas_width = $idVal['TAS_WIDTH'];
                                $tas_height = $idVal['TAS_HEIGHT'];
                            }
                        }
                        $positionX = $coordinateX + 62;
                        $positionY = $coordinateY + 55;
                        if ($idTask != $taskParallel) {
                            $taskParallel = $idTask;
                            $routeParallel = $sGateUID;
                            $arrayGateways[$countG]['0'] = $sGateUID;
                            $arrayGateways[$countG]['1'] = 'bpmnGatewayParallel';
                            $arrayGateways[$countG]['2'] = $gatPosX;
                            $arrayGateways[$countG]['3'] = $gatPosY;
                            $countG = $countG + 1;
                            $arrayRoutes[$countRoutes]['0'] = G::generateUniqueID();
                            $arrayRoutes[$countRoutes]['1'] = $taskParallel;
                            $arrayRoutes[$countRoutes]['2'] = $routeParallel;
                            $arrayRoutes[$countRoutes]['3'] = '1';
                            $arrayRoutes[$countRoutes]['4'] = '2';
                            $countRoutes = $countRoutes + 1;
                            $arrayRoutes[$countRoutes]['0'] = G::generateUniqueID();
                            $arrayRoutes[$countRoutes]['1'] = $routeParallel;
                            $arrayRoutes[$countRoutes]['2'] = $nextTask;
                            $arrayRoutes[$countRoutes]['3'] = '1';
                            $arrayRoutes[$countRoutes]['4'] = '2';
                            $countRoutes = $countRoutes + 1;
                        } else {
                            $arrayRoutes[$countRoutes]['0'] = G::generateUniqueID();
                            $arrayRoutes[$countRoutes]['1'] = $routeParallel;
                            $arrayRoutes[$countRoutes]['2'] = $nextTask;
                            $arrayRoutes[$countRoutes]['3'] = '1';
                            $arrayRoutes[$countRoutes]['4'] = '2';
                            $countRoutes = $countRoutes + 1;
                        }
                        break;
                    case 'SEC-JOIN':
                        $coordinateX = 0;
                        $coordinateY = 0;
                        foreach ($tasks as $taskVal => $idVal) {
                            if ($idVal['TAS_UID'] == $nextTask) {
                                $coordinateX = $idVal['TAS_POSX'] + 60;
                                $coordinateY = $idVal['TAS_POSY'] + 60;
                                $tas_width = $idVal['TAS_WIDTH'];
                                $tas_height = $idVal['TAS_HEIGHT'];
                            }
                        }
                        $positionX = $coordinateX + 60;
                        $positionY = $coordinateY - 45;
                        if ($nextTask != $taskSecJoin) {
                            $taskSecJoin = $nextTask;
                            $routeSecJoin = $sGateUID;
                            $arrayGateways[$countG]['0'] = $sGateUID;
                            $arrayGateways[$countG]['1'] = 'bpmnGatewayParallel';
                            $arrayGateways[$countG]['2'] = $gatPosX;
                            $arrayGateways[$countG]['3'] = $gatPosY;
                            $countG = $countG + 1;
                            $arrayRoutes[$countRoutes]['0'] = G::generateUniqueID();
                            $arrayRoutes[$countRoutes]['1'] = $routeSecJoin;
                            $arrayRoutes[$countRoutes]['2'] = $taskSecJoin;
                            $arrayRoutes[$countRoutes]['3'] = '1';
                            $arrayRoutes[$countRoutes]['4'] = '2';
                            $countRoutes = $countRoutes + 1;
                            $arrayRoutes[$countRoutes]['0'] = G::generateUniqueID();
                            $arrayRoutes[$countRoutes]['1'] = $idTask;
                            $arrayRoutes[$countRoutes]['2'] = $routeSecJoin;
                            $arrayRoutes[$countRoutes]['3'] = '1';
                            $arrayRoutes[$countRoutes]['4'] = '2';
                            $countRoutes = $countRoutes + 1;
                        } else {
                            $arrayRoutes[$countRoutes]['0'] = G::generateUniqueID();
                            $arrayRoutes[$countRoutes]['1'] = $idTask;
                            $arrayRoutes[$countRoutes]['2'] = $routeSecJoin;
                            $arrayRoutes[$countRoutes]['3'] = '1';
                            $arrayRoutes[$countRoutes]['4'] = '2';
                            $countRoutes = $countRoutes + 1;
                        }
                        break;
                    case 'EVALUATE':
                        $coordinateX = 0;
                        $coordinateY = 0;
                        foreach ($tasks as $taskVal => $idVal) {
                            if ($idVal['TAS_UID'] == $idTask) {
                                $coordinateX = $idVal['TAS_POSX'] + 60;
                                $coordinateY = $idVal['TAS_POSY'] + 60;
                                $tas_width = $idVal['TAS_WIDTH'];
                                $tas_height = $idVal['TAS_HEIGHT'];
                            }
                        }
                        $positionX = $coordinateX + 62;
                        $positionY = $coordinateY + 55;
                        if ($idTask != $taskEvaluate) {
                            $taskEvaluate = $idTask;
                            $routeEvaluate = $sGateUID;
                            if ($nextTask != "-1") {
                                $arrayGateways[$countG]['0'] = $sGateUID;
                                $arrayGateways[$countG]['1'] = 'bpmnGatewayExclusiveData';
                                $arrayGateways[$countG]['2'] = $gatPosX;
                                $arrayGateways[$countG]['3'] = $gatPosY;
                                $countG = $countG + 1;
                                $arrayRoutes[$countRoutes]['0'] = G::generateUniqueID();
                                $arrayRoutes[$countRoutes]['1'] = $taskEvaluate;
                                $arrayRoutes[$countRoutes]['2'] = $routeEvaluate;
                                $arrayRoutes[$countRoutes]['3'] = '1';
                                $arrayRoutes[$countRoutes]['4'] = '2';
                                $countRoutes = $countRoutes + 1;
                                $arrayRoutes[$countRoutes]['0'] = G::generateUniqueID();
                                $arrayRoutes[$countRoutes]['1'] = $routeEvaluate;
                                $arrayRoutes[$countRoutes]['2'] = $nextTask;
                                $arrayRoutes[$countRoutes]['3'] = '1';
                                $arrayRoutes[$countRoutes]['4'] = '2';
                                $countRoutes = $countRoutes + 1;
                            }
                        } else {
                            if ($nextTask != "-1") {
                                $arrayRoutes[$countRoutes]['0'] = G::generateUniqueID();
                                $arrayRoutes[$countRoutes]['1'] = $routeEvaluate;
                                $arrayRoutes[$countRoutes]['2'] = $nextTask;
                                $arrayRoutes[$countRoutes]['3'] = '1';
                                $arrayRoutes[$countRoutes]['4'] = '2';
                                $countRoutes = $countRoutes + 1;
                            } else {
                                $routeEnd = $routeEvaluate;
                            }
                        }
                        break;
                    case 'PARALLEL-BY-EVALUATION':
                        $coordinateX = 0;
                        $coordinateY = 0;
                        foreach ($tasks as $taskVal => $idVal) {
                            if ($idVal['TAS_UID'] == $idTask) {
                                $coordinateX = $idVal['TAS_POSX'] + 60;
                                $coordinateY = $idVal['TAS_POSY'] + 60;
                                $tas_width = $idVal['TAS_WIDTH'];
                                $tas_height = $idVal['TAS_HEIGHT'];
                            }
                        }
                        $positionX = $coordinateX + 62;
                        $positionY = $coordinateY + 55;
                        if ($idTask != $taskParallelEv) {
                            $taskParallelEv = $idTask;
                            $routeParallelEv = $sGateUID;
                            $arrayGateways[$countG]['0'] = $sGateUID;
                            $arrayGateways[$countG]['1'] = 'bpmnGatewayInclusive';
                            $arrayGateways[$countG]['2'] = $gatPosX;
                            $arrayGateways[$countG]['3'] = $gatPosY;
                            $countG = $countG + 1;
                            $arrayRoutes[$countRoutes]['0'] = G::generateUniqueID();
                            $arrayRoutes[$countRoutes]['1'] = $taskParallelEv;
                            $arrayRoutes[$countRoutes]['2'] = $routeParallelEv;
                            $arrayRoutes[$countRoutes]['3'] = '1';
                            $arrayRoutes[$countRoutes]['4'] = '2';
                            $countRoutes = $countRoutes + 1;
                            $arrayRoutes[$countRoutes]['0'] = G::generateUniqueID();
                            $arrayRoutes[$countRoutes]['1'] = $routeParallelEv;
                            $arrayRoutes[$countRoutes]['2'] = $nextTask;
                            $arrayRoutes[$countRoutes]['3'] = '1';
                            $arrayRoutes[$countRoutes]['4'] = '2';
                            $countRoutes = $countRoutes + 1;
                        } else {
                            $arrayRoutes[$countRoutes]['0'] = G::generateUniqueID();
                            $arrayRoutes[$countRoutes]['1'] = $routeParallelEv;
                            $arrayRoutes[$countRoutes]['2'] = $nextTask;
                            $arrayRoutes[$countRoutes]['3'] = '1';
                            $arrayRoutes[$countRoutes]['4'] = '2';
                            $countRoutes = $countRoutes + 1;
                        }
                        break;
                    case 'SELECT':
                        $coordinateX = 0;
                        $coordinateY = 0;
                        foreach ($tasks as $taskVal => $idVal) {
                            if ($idVal['TAS_UID'] == $idTask) {
                                $coordinateX = $idVal['TAS_POSX'] + 60;
                                $coordinateY = $idVal['TAS_POSY'] + 60;
                                $tas_width = $idVal['TAS_WIDTH'];
                                $tas_height = $idVal['TAS_HEIGHT'];
                            }
                        }
                        $positionX = $coordinateX + 60;
                        $positionY = $coordinateY + 40;
                        if ($idTask != $taskSelect) {
                            $taskSelect = $idTask;
                            $routeSelect = $sGateUID;
                            $arrayGateways[$countG]['0'] = $sGateUID;
                            $arrayGateways[$countG]['1'] = 'bpmnGatewayExclusiveData';
                            $arrayGateways[$countG]['2'] = $gatPosX;
                            $arrayGateways[$countG]['3'] = $gatPosY;
                            $countG = $countG + 1;
                            $arrayRoutes[$countRoutes]['0'] = G::generateUniqueID();
                            $arrayRoutes[$countRoutes]['1'] = $taskSelect;
                            $arrayRoutes[$countRoutes]['2'] = $routeSelect;
                            $arrayRoutes[$countRoutes]['3'] = '1';
                            $arrayRoutes[$countRoutes]['4'] = '2';
                            $countRoutes = $countRoutes + 1;
                            $arrayRoutes[$countRoutes]['0'] = G::generateUniqueID();
                            $arrayRoutes[$countRoutes]['1'] = $routeSelect;
                            $arrayRoutes[$countRoutes]['2'] = $nextTask;
                            $arrayRoutes[$countRoutes]['3'] = '1';
                            $arrayRoutes[$countRoutes]['4'] = '2';
                            $countRoutes = $countRoutes + 1;
                        } else {
                            if ($nextTask != "-1") {
                                $arrayRoutes[$countRoutes]['0'] = G::generateUniqueID();
                                $arrayRoutes[$countRoutes]['1'] = $routeSelect;
                                $arrayRoutes[$countRoutes]['2'] = $nextTask;
                                $arrayRoutes[$countRoutes]['3'] = '1';
                                $arrayRoutes[$countRoutes]['4'] = '2';
                                $countRoutes = $countRoutes + 1;
                            } else {
                                $routeEnd = $routeSelect;
                            }
                        }
                        break;
                    case 'DISCRIMINATOR':
                        $coordinateX = 0;
                        $coordinateY = 0;
                        $optional = $val['ROU_OPTIONAL'];
                        foreach ($tasks as $taskVal => $idVal) {
                            if ($idVal['TAS_UID'] == $nextTask) {
                                $coordinateX = $idVal['TAS_POSX'] + 60;
                                $coordinateY = $idVal['TAS_POSY'] + 60;
                                $tas_width = $idVal['TAS_WIDTH'];
                                $tas_height = $idVal['TAS_HEIGHT'];
                            }
                        }
                        $positionX = $coordinateX + 60;
                        $positionY = $coordinateY - 45;
                        if ($nextTask != $taskDiscriminator) {
                            $taskDiscriminator = $nextTask;
                            $routeDiscriminator = $sGateUID;
                            $arrayGateways[$countG]['0'] = $sGateUID;
                            $arrayGateways[$countG]['1'] = 'bpmnGatewayComplex';
                            $arrayGateways[$countG]['2'] = $gatPosX;
                            $arrayGateways[$countG]['3'] = $gatPosY;
                            $countG = $countG + 1;
                            $arrayRoutes[$countRoutes]['0'] = G::generateUniqueID();
                            $arrayRoutes[$countRoutes]['1'] = $routeDiscriminator;
                            $arrayRoutes[$countRoutes]['2'] = $taskDiscriminator;
                            $arrayRoutes[$countRoutes]['3'] = '1';
                            $arrayRoutes[$countRoutes]['4'] = '2';
                            $countRoutes = $countRoutes + 1;
                            $arrayRoutes[$countRoutes]['0'] = G::generateUniqueID();
                            $arrayRoutes[$countRoutes]['1'] = $idTask;
                            $arrayRoutes[$countRoutes]['2'] = $routeDiscriminator;
                            $arrayRoutes[$countRoutes]['3'] = '1';
                            $arrayRoutes[$countRoutes]['4'] = '2';
                            $countRoutes = $countRoutes + 1;
                        } else {
                            $arrayRoutes[$countRoutes]['0'] = G::generateUniqueID();
                            $arrayRoutes[$countRoutes]['1'] = $idTask;
                            $arrayRoutes[$countRoutes]['2'] = $routeDiscriminator;
                            $arrayRoutes[$countRoutes]['3'] = '1';
                            $arrayRoutes[$countRoutes]['4'] = '2';
                            $countRoutes = $countRoutes + 1;
                        }
                        break;
                }
            }
            if ($end == 1) {
                if ($typeRoute == "SEQUENTIAL") {
                    $coordinateX = 0;
                    $coordinateY = 0;
                    foreach ($tasks as $taskVal => $idVal) {
                        if ($idVal['TAS_UID'] == $idTask) {
                            $coordinateX = $idVal['TAS_POSX'];
                            $coordinateY = $idVal['TAS_POSY'];
                            $tas_width = $idVal['TAS_WIDTH'];
                            $tas_height = $idVal['TAS_HEIGHT'];
                            $tas_uid = $idVal['TAS_UID'];
                        }
                    }
                    $positionX = $coordinateX + 92;
                    $positionY = $coordinateY + 40;
                    $evn_uid = $val['ROU_EVN_UID'];
                    if ($evn_uid != '') {
                        $oEvent = new Event();
                        $aEvent = $oEvent->load( $evn_uid );

                        $events[$countEvents]['0'] = $evn_uid;
                        $events[$countEvents]['1'] = $aEvent['EVN_TYPE'];
                        $events[$countEvents]['2'] = $positionX - 25;
                        $events[$countEvents]['3'] = $positionY + 35;
                        $events[$countEvents]['4'] = $tas_uid;
                        $countEvents = $countEvents + 1;

                        $arrayRoutes[$countRoutes]['0'] = G::generateUniqueID();
                        $arrayRoutes[$countRoutes]['1'] = $idTask;
                        $arrayRoutes[$countRoutes]['2'] = $evn_uid;
                        $arrayRoutes[$countRoutes]['3'] = $toPort;
                        $arrayRoutes[$countRoutes]['4'] = $fromPort;
                        $arrayRoutes[$countRoutes]['5'] = $typeRoute;
                        $countRoutes = $countRoutes + 1;
                        $end = 0;
                        $endEvent = 0;
                    }
                } else {
                    $coordinateX = 0;
                    $coordinateY = 0;
                    foreach ($tasks as $taskVal => $idVal) {
                        if ($idVal['TAS_UID'] == $idTask) {
                            $coordinateX = $idVal['TAS_POSX'];
                            $coordinateY = $idVal['TAS_POSY'];
                            $tas_width = $idVal['TAS_WIDTH'];
                            $tas_height = $idVal['TAS_HEIGHT'];
                            $tas_uid = $idVal['TAS_UID'];
                        }
                    }
                    $positionX = $coordinateX + $tas_width / 1.5 + 19;
                    $positionY = $coordinateY + $tas_height / 2;
                    $idTask = $routeEnd;
                    $arrayRoutes[$countRoutes]['0'] = G::generateUniqueID();
                    $arrayRoutes[$countRoutes]['1'] = $idTask;
                    $arrayRoutes[$countRoutes]['2'] = $idRoute;
                    $arrayRoutes[$countRoutes]['3'] = $toPort;
                    $arrayRoutes[$countRoutes]['4'] = $fromPort;
                    $arrayRoutes[$countRoutes]['5'] = $typeRoute;
                    $countRoutes = $countRoutes + 1;

                    $events[$countEvents]['0'] = $idRoute;
                    $events[$countEvents]['1'] = 'bpmnEventEmptyEnd';
                    $events[$countEvents]['2'] = $positionX - 25;
                    $events[$countEvents]['3'] = $positionY + 35;
                    $events[$countEvents]['4'] = $tas_uid;
                    $countEvents = $countEvents + 1;
                    $end = 0;
                    $endEvent = 0;
                }
            } else {
                if ($typeRoute == "SEQUENTIAL") {
                    //Will Check for Intermediate Timer
                    $evn_uid = $val['ROU_EVN_UID'];
                    if ($evn_uid != '') {
                        $coordinateX = 0;
                        $coordinateY = 0;
                        foreach ($tasks as $taskVal => $idVal) {
                            if ($idVal['TAS_UID'] == $idTask) {
                                $coordinateX = $idVal['TAS_POSX'];
                                $coordinateY = $idVal['TAS_POSY'];
                                $tas_width = $idVal['TAS_WIDTH'];
                                $tas_height = $idVal['TAS_HEIGHT'];
                            }
                        }
                        $positionX = $coordinateX + $tas_width / 1.5 + 19;
                        $positionY = $coordinateY + $tas_height / 2;

                        $oEvent = new Event();
                        $aEvent = $oEvent->load( $evn_uid );
                        $events[$countEvents]['0'] = $evn_uid;
                        $events[$countEvents]['1'] = $aEvent['EVN_TYPE'];
                        $events[$countEvents]['2'] = $aEvent['EVN_POSX'];
                        $events[$countEvents]['3'] = $aEvent['EVN_POSY'];
                        $countEvents = $countEvents + 1;

                        $arrayRoutes[$countRoutes]['0'] = G::generateUniqueID();
                        $arrayRoutes[$countRoutes]['1'] = $idTask;
                        $arrayRoutes[$countRoutes]['2'] = $evn_uid;
                        $arrayRoutes[$countRoutes]['3'] = '1';
                        $arrayRoutes[$countRoutes]['4'] = '2';
                        $countRoutes = $countRoutes + 1;
                        $arrayRoutes[$countRoutes]['0'] = G::generateUniqueID();
                        $arrayRoutes[$countRoutes]['1'] = $evn_uid;
                        $arrayRoutes[$countRoutes]['2'] = $nextTask;
                        $arrayRoutes[$countRoutes]['3'] = '1';
                        $arrayRoutes[$countRoutes]['4'] = '2';
                        $countRoutes = $countRoutes + 1;
                    } else {
                        $arrayRoutes[$countRoutes]['0'] = $idRoute;
                        $arrayRoutes[$countRoutes]['1'] = $idTask;
                        $arrayRoutes[$countRoutes]['2'] = $nextTask;
                        $arrayRoutes[$countRoutes]['3'] = $toPort;
                        $arrayRoutes[$countRoutes]['4'] = $fromPort;
                        $countRoutes = $countRoutes + 1;
                    }
                }
            }
        }
        $data = array ();
        $data['GATEWAYS'] = $arrayGateways;
        $data['TRANSITION'] = $arrayRoutes;
        $data['EVENTS'] = $events;
        return $data;
    }

    /**
     * This public function Removes duplicate values from an array bi-dimensional
     *
     * @param array $array
     * @return array
     */
    public function super_unique ($array)
    {
        $result = array_map( "unserialize", array_unique( array_map( "serialize", $array ) ) );
        foreach ($result as $key => $value) {
            if (is_array( $value )) {
                $result[$key] = $this->super_unique( $value );
            }
        }
        return $result;
    }

    /**
     * This public function create a array for open the process in the new processmap
     *
     * @param array $array
     * @return array
     */
    public function createProcessPM ($array)
    {
        $result = array ();
        $result['0'] = $array['PRO_UID'];
        $result['1'] = $array['PRO_TITLE'];
        $result['2'] = $array['PRO_DESCRIPTION'];
        return $result;
    }

    /**
     * This public function creates an array for the lane that will be created according to the data given in an array
     *
     * @param array $lanes
     * @return array $lane
     */
    public function createSubProcessesPM ($array)
    {
        $arrayLanes = array ();
        $field = array ();
        foreach ($array as $key => $value) {

            $field['0'] = $value['SWI_UID'];
            $field['1'] = $value['SWI_TEXT'];
            $field['2'] = $value['SWI_X'];
            $field['3'] = $value['SWI_Y'];
            $field['4'] = ''; //$value['SWI_WIDTH'];
            $field['5'] = ''; //$value['SWI_HEIGHT'];
        }
        return $arrayLanes;
    }

    /**
     * This public function creates an array for the lane that will be created according to the data given in an array
     *
     * @param array $lanes
     * @return array $lane
     */
    public function saveWebEntry ($array)
    {
        $file = new DOMDocument();
        foreach ($array as $key => $value) {
            $link = $value->W_LINK;
            // This split public function has been DEPRECATED as of PHP 5.3.0.
            // $link = split('>',$link);
            // $link = split('<',$link[2]);
            $link = explode( '>', $link );
            $link = explode( '<', $link[2] );
            $link = $link['0'];
            $uid = $value->W_PRO_UID;
            $name = $value->W_FILENAME;
        }
    }
}

