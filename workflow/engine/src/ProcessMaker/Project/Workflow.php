<?php
namespace ProcessMaker\Project;

use \Criteria;
use \ResultSet;

use \Process;
use \Tasks;
use \Task;
use \Route;
use \RoutePeer;

use ProcessMaker\Util\Common;
use ProcessMaker\Exception;
use ProcessMaker\Util;

/**
 * Class Workflow
 *
 * @package ProcessMaker\Project
 * @author Erik Amaru Ortiz <aortiz.erik@gmail.com, erik@colosa.com>
 */
class Workflow extends Handler
{
    protected $process;
    protected $proUid;

    protected $tasks = array();
    protected $routes = array();


    public function __construct($data = null)
    {
        if (! is_null($data)) {
            $this->create($data);
        }
    }

    public static function load($proUid)
    {
        $me = new self();

        try {
            $process = new Process();
            $processData = $process->load($proUid);
        } catch (\Exception $e) {
            if (is_null(\ProcessPeer::retrieveByPK($proUid))) {
                throw new Exception\ProjectNotFound($me, $proUid);
            } else {
                throw $e;
            }
        }

        $me->process = $processData;
        $me->proUid = $processData["PRO_UID"];

        return $me;
    }

    public function create($data)
    {
        // setting defaults
        $data['PRO_UID'] = array_key_exists('PRO_UID', $data) ? $data['PRO_UID'] : Common::generateUID();
        $data['USR_UID'] = array_key_exists('PRO_CREATE_USER', $data) ? $data['PRO_CREATE_USER'] : null;
        $data['PRO_TITLE'] = array_key_exists('PRO_TITLE', $data) ? trim($data['PRO_TITLE']) : "";
        $data['PRO_CATEGORY'] = array_key_exists('PRO_CATEGORY', $data) ? $data['PRO_CATEGORY'] : "";

        try {
            self::log("Create Process with data:", $data);

            //validate if process with specified name already exists
            if (Process::existsByProTitle($data["PRO_TITLE"])) {
                throw new Exception\ProjectAlreadyExists($this, $data["PRO_TITLE"]);
            }

            // Create project
            $process = new Process();
            $this->proUid = $process->create($data, false);

            // Call Plugins
            $pluginData['PRO_UID'] = $this->proUid;
            $pluginData['PRO_TEMPLATE'] = empty($data["PRO_TEMPLATE"]) ? "" : $data["PRO_TEMPLATE"];
            $pluginData['PROCESSMAP'] = null;

            $pluginRegistry = \PMPluginRegistry::getSingleton();
            $pluginRegistry->executeTriggers(PM_NEW_PROCESS_SAVE, $pluginData);

            // Save Calendar ID for this process
            if (! empty($data["PRO_CALENDAR"])) {
                //G::LoadClass( "calendar" );
                $calendar = new \Calendar();
                $calendar->assignCalendarTo($this->proUid, $data["PRO_CALENDAR"], 'PROCESS');
            }

            self::log("Create Process Success!");
        } catch (\Exception $e) {
            self::log("Exception: ", $e->getMessage(), "Trace: ", $e->getTraceAsString());
            throw $e;
        }
    }

    public function update($data)
    {
        $process = new Process();
        $data["PRO_UID"] = $this->getUid();
        $process->update($data);
    }

    public function remove($flagRemoveCases = true)
    {
        try {
            self::log("Remove Process with uid: {$this->proUid}");
            $this->deleteProcess($this->proUid, $flagRemoveCases);
            self::log("Remove Process Success!");
        } catch (\Exception $e) {
            self::log("Exception: ", $e->getMessage(), "Trace: ", $e->getTraceAsString());
            throw $e;
        }
    }

    public static function removeIfExists($proUid)
    {
        $process = \ProcessPeer::retrieveByPK($proUid);

        if ($process) {
            $me = new self();
            $me->proUid = $process->getProUid();
            $me->remove();
        }
    }

    public static function getList($start = null, $limit = null, $filter = "", $changeCaseTo = CASE_UPPER)
    {
        //return Project::getAll($start, $limit, $filter, $changeCaseTo);
        $process = new Process();
        $processes = $process->getAllProcesses($start, $limit);
        //$processes = $process->getAll();

        if ($changeCaseTo != CASE_UPPER) {
            foreach ($processes as $i => $processRow) {
                $processes[$i] = array_change_key_case($processRow, $changeCaseTo);
            }
        }

        return $processes;
    }

    public function getUid()
    {
        if (empty($this->proUid)) {
            throw new \RuntimeException("Error: There is not an initialized project.");
        }

        return $this->proUid;
    }

    public function getProcess()
    {
        if (empty($this->proUid)) {
            throw new \Exception("Error: There is not an initialized project.");
        }

        $process = new Process();

        return $process->load($this->proUid);
    }

    /*
     * Projects elements handlers
     */

    public function addTask($taskData)
    {
        // Setting defaults
        $taskData['TAS_UID'] = array_key_exists('TAS_UID', $taskData) ? $taskData['TAS_UID'] : Common::generateUID();
        $taskData['PRO_UID'] = $this->proUid;

        try {
            self::log("Add Task with data: ", $taskData);
            $task = new Task();
            $tasUid = $task->create($taskData, false);
            self::log("Add Task Success!");

            // SubProcess Handling
            if ($task->getTasType() == "SUBPROCESS") {
                $this->addSubProcess($this->proUid, $tasUid);
            }

        } catch (\Exception $e) {
            self::log("Exception: ", $e->getMessage(), "Trace: ", $e->getTraceAsString());
            throw $e;
        }

        return $tasUid;
    }

    public function updateTask($tasUid, $taskData)
    {
        try {
            self::log("Update Task: $tasUid", "With data: ", $taskData);
            $task = new Task();
            $taskData['TAS_UID'] = $tasUid;
            $result = $task->update($taskData);
            self::log("Update Task Success!");
        } catch (\Exception $e) {
            self::log("Exception: ", $e->getMessage(), "Trace: ", $e->getTraceAsString());
            throw $e;
        }

        return $result;
    }

    public function removeTask($tasUid)
    {
        try {
            self::log("Remove Task: $tasUid");
            $task = \TaskPeer::retrieveByPK($tasUid);
            $tasType = $task->getTasType();

            $task = new Tasks();
            $task->deleteTask($tasUid);
            self::log("Remove Task Success!");

            if ($tasType == "SUBPROCESS") {
                $this->removeSupProcess($this->proUid, $tasUid);
            }

        } catch (\Exception $e) {
            self::log("Exception: ", $e->getMessage(), "Trace: ", $e->getTraceAsString());
            throw $e;
        }
    }

    public function getTask($tasUid)
    {
        try {
            $task = new Task();
            $taskData = $task->load($tasUid);
        } catch (\Exception $e){
            $taskData = null;
        }

        return $taskData;
    }


    /**
     * @return array()
     */
    public function getTasks()
    {
        if (empty($this->proUid)) {
            return array();
        }

        $tasks = new Tasks();

        return $tasks->getAllTasks($this->proUid);
    }

    public function addSubProcess($proUid = '', $tasUid)//$iX = 0, $iY = 0)
    {
        try {
            $subProcess = new \SubProcess();
            $data = array(
                'SP_UID' => Util\Common::generateUID(),
                'PRO_UID' => 0,
                'TAS_UID' => 0,
                'PRO_PARENT' => $proUid,
                'TAS_PARENT' => $tasUid,
                'SP_TYPE' => 'SIMPLE',
                'SP_SYNCHRONOUS' => 0,
                'SP_SYNCHRONOUS_TYPE' => 'ALL',
                'SP_SYNCHRONOUS_WAIT' => 0,
                'SP_VARIABLES_OUT' => '',
                'SP_VARIABLES_IN' => '',
                'SP_GRID_IN' => ''
            );

            self::log("Adding SubProcess with data: ", $data);
            $spUid = $subProcess->create($data);
            self::log("Adding SubProcess success!, created sp_uid: ", $spUid);

            return $spUid;
        } catch (\Exception $oError) {
            throw ($oError);
        }
    }

    public function removeSupProcess($proUid, $tasUid)
    {
        try {
            $subProcess = \SubProcess::findByParents($proUid, $tasUid);
            self::log("Remove SupProcess: ".$subProcess->getSpUid());
            $subProcess->delete();
            self::log("Remove SupProcess Success!");
        } catch (\Exception $e) {
            self::log("Exception: ", $e->getMessage(), "Trace: ", $e->getTraceAsString());
            throw $e;
        }
    }

    public function updateSubProcess()
    {

    }

    /**
     * @param string $tasUid
     * @param bool $value
     */
    public function setStartTask($tasUid, $value = true)
    {
        $value = $value ? "TRUE" : "FALSE";

        self::log("Setting Start Task with Uid: $tasUid: $value");
        $task = \TaskPeer::retrieveByPK($tasUid);
        $task->setTasStart($value);
        $task->save();
        self::log("Setting Start Task -> $value, Success!");
    }

    /**
     * @param string $tasUid
     * @param bool $value
     */
    public function setEndTask($tasUid, $value = true)
    {
        self::log("Setting End Task with Uid: $tasUid: " . ($value ? "TRUE" : "FALSE"));
        if ($value) {
            $this->addSequentialRoute($tasUid, "-1");
        } else {
            $route = \Route::findOneBy(array(
                \RoutePeer::TAS_UID => $tasUid,
                \RoutePeer::ROU_NEXT_TASK => "-1"
            ));

            if (! is_null($route)) {
                $this->removeRoute($route->getRouUid());
            }
        }
        self::log("Setting End Task -> ".($value ? "TRUE" : "FALSE").", Success!");
    }

    public function addSequentialRoute($fromTasUid, $toTasUid, $delete = null)
    {
        $this->addRoute($fromTasUid, $toTasUid, "SEQUENTIAL", $delete);
    }

    public function addSelectRoute($fromTasUid, array $toTasks, $delete = null)
    {
        foreach ($toTasks as $toTasUid) {
            $this->addRoute($fromTasUid, $toTasUid, "SELECT", $delete);
        }
    }

    /**
     * This method add a new or update a Route record
     *
     * @param $fromTasUid
     * @param $toTasUid
     * @param $type
     * @param string $condition
     * @return string
     * @throws \Exception
     */
    public function addRoute($fromTasUid, $toTasUid, $type, $condition = "", $default = 0)
    {
        try {
            $validTypes = array("SEQUENTIAL", "SELECT", "EVALUATE", "PARALLEL", "PARALLEL-BY-EVALUATION", "SEC-JOIN", "DISCRIMINATOR");

            if (! in_array($type, $validTypes)) {
                throw new \Exception("Invalid Route type, given: $type, expected: [".implode(",", $validTypes)."]");
            }

            if ($type != 'SEQUENTIAL' && $type != 'SEC-JOIN' && $type != 'DISCRIMINATOR') {
                //if ($this->getNumberOfRoutes($this->proUid, $fromTasUid, $toTasUid, $type) > 0) {
                    //throw new \LogicException("Unexpected behaviour");
                //}
            }

            //if ($type == 'SEQUENTIAL' || $type == 'SEC-JOIN' || $type == 'DISCRIMINATOR') {
                //$oTasks = new Tasks();
                //$oTasks->deleteAllRoutesOfTask($this->proUid, $fromTasUid);
            //}

            $route = \Route::findOneBy(array(
                \RoutePeer::TAS_UID => $fromTasUid,
                \RoutePeer::ROU_NEXT_TASK => $toTasUid
            ));

            if (is_null($route)) {
                $result = $this->saveNewPattern($this->proUid, $fromTasUid, $toTasUid, $type, $condition, $default);
            } else {
                $result = $this->updateRoute($route->getRouUid(), array(
                    "TAS_UID" => $fromTasUid,
                    "ROU_NEXT_TASK" => $toTasUid,
                    "ROU_TYPE" => $type,
                    "ROU_DEFAULT"   => $default,
                    "ROU_CONDITION" => $condition
                ));
            }

            return $result;
        } catch (\Exception $e) {
            self::log("Exception: ", $e->getMessage(), "Trace: ", $e->getTraceAsString());
            throw $e;
        }
    }

    public function resetTaskRoutes($actUid)
    {
        $oTasks = new Tasks();
        $oTasks->deleteAllRoutesOfTask($this->proUid, $actUid);
    }

    public function updateRoute($rouUid, $routeData)
    {
        $routeData['ROU_UID'] = $rouUid;

        try {
            self::log("Update Route: $rouUid with data:", $routeData);
            $route = new Route();
            $result = $route->update($routeData);
            self::log("Update Route Success!");

            return $result;
        } catch (\Exception $e) {
            self::log("Exception: ", $e->getMessage(), "Trace: ", $e->getTraceAsString());
            throw $e;
        }
    }

    public function removeRoute($rouUid)
    {
        try {
            self::log("Remove Route: $rouUid");
            $route = new Route();
            $result = $route->remove($rouUid);
            self::log("Remove Route Success!");

            return $result;
        } catch (\Exception $e) {
            self::log("Exception: ", $e->getMessage(), "Trace: ", $e->getTraceAsString());
            throw $e;
        }
    }

    public function removeRouteFromTo($fromTasUid, $toTasUid)
    {
        try {
            self::log("Remove Route from $fromTasUid -> to $toTasUid");

            $route = Route::findOneBy(array(
                RoutePeer::TAS_UID => $fromTasUid,
                RoutePeer::ROU_NEXT_TASK => $toTasUid
            ));

            if ($route != null) {
                $route->delete();
            }

            self::log("Remove Route Success!");
        } catch (\Exception $e) {
            self::log("Exception: ", $e->getMessage(), "Trace: ", $e->getTraceAsString());
            throw $e;
        }
    }

    public function getRoute($rouUid)
    {
        $route = new Route();

        return $route->load($rouUid);
    }

    public function getRoutes($start = null, $limit = null, $filter = '', $changeCaseTo = CASE_UPPER)
    {
        return Route::getAll($this->getUid(), $start, $limit, $filter, $changeCaseTo);
    }


    /****************************************************************************************************
     * Migrated Methods from class.processMap.php class                                                 *
     ****************************************************************************************************/

    private function getNumberOfRoutes($sProcessUID = '', $sTaskUID = '', $sNextTask = '', $sType = '')
    {
        try {
            $oCriteria = new Criteria('workflow');
            $oCriteria->addSelectColumn('COUNT(*) AS ROUTE_NUMBER');
            $oCriteria->add(RoutePeer::PRO_UID, $sProcessUID);
            $oCriteria->add(RoutePeer::TAS_UID, $sTaskUID);
            $oCriteria->add(RoutePeer::ROU_NEXT_TASK, $sNextTask);
            $oCriteria->add(RoutePeer::ROU_TYPE, $sType);
            $oDataset = RoutePeer::doSelectRS($oCriteria);
            $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $oDataset->next();
            $aRow = $oDataset->getRow();

            return (int) $aRow['ROUTE_NUMBER'];
        } catch (Exception $oError) {
            throw ($oError);
        }
    }

    private function saveNewPattern($sProcessUID = "", $sTaskUID = "", $sNextTask = "", $sType = "", $condition = "", $default = 0)
    {
        try {
            self::log("Add Route from task: $sTaskUID -> to task: $sNextTask ($sType)");

            $oCriteria = new Criteria('workflow');
            $oCriteria->addSelectColumn('COUNT(*) AS ROUTE_NUMBER');
            //$oCriteria->addSelectColumn('GAT_UID AS GATEWAY_UID');
            $oCriteria->add(RoutePeer::PRO_UID, $sProcessUID);
            $oCriteria->add(RoutePeer::TAS_UID, $sTaskUID);
            $oCriteria->add(RoutePeer::ROU_TYPE, $sType);

            $oDataset = RoutePeer::doSelectRS($oCriteria);
            $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $oDataset->next();
            $aRow = $oDataset->getRow();

            $aFields['PRO_UID'] = $sProcessUID;
            $aFields['TAS_UID'] = $sTaskUID;
            $aFields['ROU_NEXT_TASK'] = $sNextTask;
            $aFields["ROU_CASE"] = (int)($aRow["ROUTE_NUMBER"]) + 1;
            $aFields["ROU_TYPE"] = $sType;
            $aFields["ROU_DEFAULT"] = $default;

            if(! empty($condition)) {
                $aFields['ROU_CONDITION'] = $condition;
            }

            //$sGatewayUID = $aRow['GATEWAY_UID'];

            //if ($sDelete && $sGatewayUID != '') {
            //    $oGateway = new Gateway();
            //   $oGateway->remove($sGatewayUID);
            //}
            //Getting Gateway UID after saving gateway
            //if($sType != 'SEQUENTIAL' && $sGatewayUID == '' && $sDelete == '1')

            /*??? Maybe this is deprecated
            if ($sType != 'SEQUENTIAL') {
                $oProcessMap = new processMap();
                $sGatewayUID = $this->saveNewGateway($sProcessUID, $sTaskUID, $sNextTask);
            }*/

            //$aFields['GAT_UID'] = (isset($sGatewayUID)) ? $sGatewayUID : '';

            $oRoute = new Route();
            $result = $oRoute->create($aFields);
            self::log("Add Route Success! - ROU_UID: ", $result);

            return $result;
        } catch (Exception $oError) {
            throw ($oError);
        }
    }

    public function deleteProcess($sProcessUID, $flagRemoveCases = true)
    {
        try {
            //G::LoadClass('case');
            //G::LoadClass('reportTables');

            //Instance all classes necesaries
            $oProcess = new \Process();
            $oDynaform = new \Dynaform();
            $oInputDocument = new \InputDocument();
            $oOutputDocument = new \OutputDocument();
            $oTrigger = new \Triggers();
            $oRoute = new \Route();
            $oGateway = new \Gateway();
            $oEvent = new \Event();
            $oSwimlaneElement = new \SwimlanesElements();
            $oConfiguration = new \Configuration();
            $oDbSource = new \DbSource();
            $oReportTable = new \ReportTables();
            $oCaseTracker = new \CaseTracker();
            $oCaseTrackerObject = new \CaseTrackerObject();

            //Update PROCESS_FILES_CHECKED
            $configuration = \ConfigurationPeer::retrieveByPK("PROCESS_FILES_CHECKED", "", "", "", "");

            if (!is_null($configuration)) {
                $arrayProjectUid = unserialize($configuration->getCfgValue());

                unset($arrayProjectUid[$sProcessUID]);

                $conf = new \Configuration();

                $result = $conf->update(array(
                    "CFG_UID"   => "PROCESS_FILES_CHECKED",
                    "OBJ_UID"   => "",
                    "CFG_VALUE" => serialize($arrayProjectUid),
                    "PRO_UID"   => "",
                    "USR_UID"   => "",
                    "APP_UID"   => ""
                ));
            }

            //Delete the applications of process
            if ($flagRemoveCases) {
                $case = new \Cases();

                $criteria = new \Criteria("workflow");

                $criteria->addSelectColumn(\ApplicationPeer::APP_UID);
                $criteria->add(\ApplicationPeer::PRO_UID, $sProcessUID, \Criteria::EQUAL);

                $rsCriteria = \ApplicationPeer::doSelectRS($criteria);
                $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

                while ($rsCriteria->next()) {
                    $row = $rsCriteria->getRow();

                    $result = $case->removeCase($row["APP_UID"]);
                }
            }

            //Delete the tasks of process
            $oCriteria = new Criteria('workflow');
            $oCriteria->add(\TaskPeer::PRO_UID, $sProcessUID);
            $oDataset = \TaskPeer::doSelectRS($oCriteria);
            $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $oDataset->next();
            while ($aRow = $oDataset->getRow()) {
                //$this->deleteTask($aRow['TAS_UID']);
                $oTasks = new \Tasks();
                $oTasks->deleteTask($aRow['TAS_UID']);

                $oDataset->next();
            }
            //Delete the dynaforms of process
            $oCriteria = new Criteria('workflow');
            $oCriteria->add(\DynaformPeer::PRO_UID, $sProcessUID);
            $oDataset = \DynaformPeer::doSelectRS($oCriteria);
            $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $oDataset->next();
            while ($aRow = $oDataset->getRow()) {
                $oDynaform->remove($aRow['DYN_UID']);
                $oDataset->next();
            }
            //Delete the input documents of process
            $oCriteria = new Criteria('workflow');
            $oCriteria->add(\InputDocumentPeer::PRO_UID, $sProcessUID);
            $oDataset = \InputDocumentPeer::doSelectRS($oCriteria);
            $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $oDataset->next();
            while ($aRow = $oDataset->getRow()) {
                $oInputDocument->remove($aRow['INP_DOC_UID']);
                $oDataset->next();
            }
            //Delete the output documents of process
            $oCriteria = new Criteria('workflow');
            $oCriteria->add(\OutputDocumentPeer::PRO_UID, $sProcessUID);
            $oDataset = \OutputDocumentPeer::doSelectRS($oCriteria);
            $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $oDataset->next();
            while ($aRow = $oDataset->getRow()) {
                $oOutputDocument->remove($aRow['OUT_DOC_UID']);
                $oDataset->next();
            }

            //Delete the triggers of process
            $oCriteria = new Criteria('workflow');
            $oCriteria->add(\TriggersPeer::PRO_UID, $sProcessUID);
            $oDataset = \TriggersPeer::doSelectRS($oCriteria);
            $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $oDataset->next();
            while ($aRow = $oDataset->getRow()) {
                $oTrigger->remove($aRow['TRI_UID']);
                $oDataset->next();
            }

            //Delete the routes of process
            $oCriteria = new Criteria('workflow');
            $oCriteria->add(\RoutePeer::PRO_UID, $sProcessUID);
            $oDataset = \RoutePeer::doSelectRS($oCriteria);
            $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $oDataset->next();
            while ($aRow = $oDataset->getRow()) {
                $oRoute->remove($aRow['ROU_UID']);
                $oDataset->next();
            }

            //Delete the gateways of process
            $oCriteria = new Criteria('workflow');
            $oCriteria->add(\GatewayPeer::PRO_UID, $sProcessUID);
            $oDataset = \GatewayPeer::doSelectRS($oCriteria);
            $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $oDataset->next();
            while ($aRow = $oDataset->getRow()) {
                $oGateway->remove($aRow['GAT_UID']);
                $oDataset->next();
            }

            //Delete the Event of process
            $oCriteria = new Criteria('workflow');
            $oCriteria->add(\EventPeer::PRO_UID, $sProcessUID);
            $oDataset = \EventPeer::doSelectRS($oCriteria);
            $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $oDataset->next();
            while ($aRow = $oDataset->getRow()) {
                $oEvent->remove($aRow['EVN_UID']);
                $oDataset->next();
            }

            //Delete the swimlanes elements of process
            $oCriteria = new Criteria('workflow');
            $oCriteria->add(\SwimlanesElementsPeer::PRO_UID, $sProcessUID);
            $oDataset = \SwimlanesElementsPeer::doSelectRS($oCriteria);
            $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $oDataset->next();
            while ($aRow = $oDataset->getRow()) {
                $oSwimlaneElement->remove($aRow['SWI_UID']);
                $oDataset->next();
            }
            //Delete the configurations of process
            $oCriteria = new Criteria('workflow');
            $oCriteria->add(\ConfigurationPeer::PRO_UID, $sProcessUID);
            $oDataset = \ConfigurationPeer::doSelectRS($oCriteria);
            $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $oDataset->next();
            while ($aRow = $oDataset->getRow()) {
                $oConfiguration->remove($aRow['CFG_UID'], $aRow['OBJ_UID'], $aRow['PRO_UID'], $aRow['USR_UID'], $aRow['APP_UID']);
                $oDataset->next();
            }
            //Delete the DB sources of process
            $oCriteria = new Criteria('workflow');
            $oCriteria->add(\DbSourcePeer::PRO_UID, $sProcessUID);
            $oDataset = \DbSourcePeer::doSelectRS($oCriteria);
            $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $oDataset->next();
            while ($aRow = $oDataset->getRow()) {

                /**
                 * note added by gustavo cruz gustavo-at-colosa-dot-com 27-01-2010
                 * in order to solve the bug 0004389, we use the validation function Exists
                 * inside the remove function in order to verify if the DbSource record
                 * exists in the Database, however there is a strange behavior within the
                 * propel engine, when the first record is erased somehow the "_deleted"
                 * attribute of the next row is set to true, so when propel tries to erase
                 * it, obviously it can't and trows an error. With the "Exist" function
                 * we ensure that if there is the record in the database, the _delete attribute must be false.
                 *
                 * note added by gustavo cruz gustavo-at-colosa-dot-com 28-01-2010
                 * I have just identified the source of the issue, when is created a $oDbSource DbSource object
                 * it's used whenever a record is erased or removed in the db, however the problem
                 * it's that the same object is used every time, and the delete method invoked
                 * sets the _deleted attribute to true when its called, of course as we use
                 * the same object, the first time works fine but trowns an error with the
                 * next record, cos it's the same object and the delete method checks if the _deleted
                 * attribute it's true or false, the attrib _deleted is setted to true the
                 * first time and later is never changed, the issue seems to be part of
                 * every remove function in the model classes, not only DbSource
                 * i recommend that a more general solution must be achieved to resolve
                 * this issue in every model class, to prevent future problems.
                 */
                $oDbSource->remove($aRow['DBS_UID'], $sProcessUID);
                $oDataset->next();
            }
            //Delete the supervisors
            $oCriteria = new Criteria('workflow');
            $oCriteria->add(\ProcessUserPeer::PRO_UID, $sProcessUID);
            \ProcessUserPeer::doDelete($oCriteria);
            //Delete the object permissions
            $oCriteria = new Criteria('workflow');
            $oCriteria->add(\ObjectPermissionPeer::PRO_UID, $sProcessUID);
            \ObjectPermissionPeer::doDelete($oCriteria);
            //Delete the step supervisors
            $oCriteria = new Criteria('workflow');
            $oCriteria->add(\StepSupervisorPeer::PRO_UID, $sProcessUID);
            \StepSupervisorPeer::doDelete($oCriteria);
            //Delete the report tables
            $oCriteria = new Criteria('workflow');
            $oCriteria->add(\ReportTablePeer::PRO_UID, $sProcessUID);
            $oDataset = \ReportTablePeer::doSelectRS($oCriteria);
            $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $oDataset->next();
            while ($aRow = $oDataset->getRow()) {
                $oReportTable->deleteReportTable($aRow['REP_TAB_UID']);
                $oDataset->next();
            }
            //Delete case tracker configuration
            $oCaseTracker->remove($sProcessUID);
            //Delete case tracker objects
            $oCriteria = new Criteria('workflow');
            $oCriteria->add(\CaseTrackerObjectPeer::PRO_UID, $sProcessUID);
            \ProcessUserPeer::doDelete($oCriteria);

            //Delete SubProcess
            $criteria = new \Criteria("workflow");

            $criteria->add(\SubProcessPeer::PRO_PARENT, $sProcessUID, \Criteria::EQUAL);

            $result = \SubProcessPeer::doDelete($criteria);

            //Delete WebEntries
            $webEntry = new \ProcessMaker\BusinessModel\WebEntry();

            $criteria = new \Criteria("workflow");

            $criteria->addSelectColumn(\WebEntryPeer::WE_UID);
            $criteria->add(\WebEntryPeer::PRO_UID, $sProcessUID, \Criteria::EQUAL);

            $rsCriteria = \WebEntryPeer::doSelectRS($criteria);
            $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

            while ($rsCriteria->next()) {
                $row = $rsCriteria->getRow();

                $webEntry->delete($row["WE_UID"]);
            }

            //Delete WebEntry-Events
            $webEntryEvent = new \ProcessMaker\BusinessModel\WebEntryEvent();

            $criteria = new \Criteria("workflow");

            $criteria->addSelectColumn(\WebEntryEventPeer::WEE_UID);
            $criteria->add(\WebEntryEventPeer::PRJ_UID, $sProcessUID, \Criteria::EQUAL);

            $rsCriteria = \WebEntryEventPeer::doSelectRS($criteria);
            $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

            while ($rsCriteria->next()) {
                $row = $rsCriteria->getRow();

                $webEntryEvent->delete($row["WEE_UID"]);
            }

            //Delete MessageTypes
            $messageType = new \ProcessMaker\BusinessModel\MessageType();

            $criteria = new \Criteria("workflow");

            $criteria->addSelectColumn(\MessageTypePeer::MSGT_UID);
            $criteria->add(\MessageTypePeer::PRJ_UID, $sProcessUID, \Criteria::EQUAL);

            $rsCriteria = \MessageTypePeer::doSelectRS($criteria);
            $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

            while ($rsCriteria->next()) {
                $row = $rsCriteria->getRow();

                $messageType->delete($row["MSGT_UID"]);
            }

            //Delete Message-Event-Relation
            $messageEventRelation = new \ProcessMaker\BusinessModel\MessageEventRelation();

            $messageEventRelation->deleteWhere(array(\MessageEventRelationPeer::PRJ_UID => $sProcessUID));

            //Delete Message-Event-Task-Relation
            $elementTaskRelation = new \ProcessMaker\BusinessModel\ElementTaskRelation();

            $elementTaskRelation->deleteWhere(array(\ElementTaskRelationPeer::PRJ_UID => $sProcessUID));

            //Delete Message-Event-Definition
            $messageEventDefinition = new \ProcessMaker\BusinessModel\MessageEventDefinition();

            $criteria = new \Criteria("workflow");

            $criteria->addSelectColumn(\MessageEventDefinitionPeer::MSGED_UID);
            $criteria->add(\MessageEventDefinitionPeer::PRJ_UID, $sProcessUID, \Criteria::EQUAL);

            $rsCriteria = \MessageEventDefinitionPeer::doSelectRS($criteria);
            $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

            while ($rsCriteria->next()) {
                $row = $rsCriteria->getRow();

                $messageEventDefinition->delete($row["MSGED_UID"]);
            }

            //Delete Script-Task
            $scriptTask = new \ProcessMaker\BusinessModel\ScriptTask();

            $scriptTask->deleteWhere(array(\ScriptTaskPeer::PRJ_UID => array($sProcessUID, \Criteria::EQUAL)));

            //Delete Timer-Event
            $timerEvent = new \ProcessMaker\BusinessModel\TimerEvent();

            $timerEvent->deleteWhere(array(\TimerEventPeer::PRJ_UID => array($sProcessUID, \Criteria::EQUAL)));

            //Delete Email-Event
            $emailEvent = new \ProcessMaker\BusinessModel\EmailEvent();
            $criteria = new \Criteria("workflow");
            $criteria->addSelectColumn(\EmailEventPeer::EMAIL_EVENT_UID);
            $criteria->add(\EmailEventPeer::PRJ_UID, $sProcessUID, \Criteria::EQUAL);
            $rsCriteria = \EmailEventPeer::doSelectRS($criteria);
            $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

            while ($rsCriteria->next()) {
                $row = $rsCriteria->getRow();
                $emailEvent->delete($sProcessUID,$row["EMAIL_EVENT_UID"],false);
            }

            //Delete files Manager
            $filesManager = new \ProcessMaker\BusinessModel\FilesManager();
            $criteria = new \Criteria("workflow");
            $criteria->addSelectColumn(\ProcessFilesPeer::PRF_UID);
            $criteria->add(\ProcessFilesPeer::PRO_UID, $sProcessUID, \Criteria::EQUAL);
            $rsCriteria = \ProcessFilesPeer::doSelectRS($criteria);
            $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

            while ($rsCriteria->next()) {
                $row = $rsCriteria->getRow();
                $filesManager->deleteProcessFilesManager($sProcessUID, $row["PRF_UID"]);
            }
            
            //Delete the actions by email
            $oCriteria = new Criteria('workflow');
            $oCriteria->add(\AbeConfigurationPeer::PRO_UID, $sProcessUID);
            \AbeConfigurationPeer::doDelete($oCriteria);

            //Delete the process
            try {
                $oProcess->remove($sProcessUID);
            } catch (\Exception $oError) {
                throw ($oError);
            }
            return true;
        } catch (\Exception $oError) {
            throw ($oError);
        }
    }

    public function setDisabled($value = true)
    {
        $status = $value ? "DISABLED" : "ACTIVE";
        $this->update(array("PRO_STATUS" => $status));
    }

    public function addCaseScheduler($schUid)
    {
        try {
            $caseScheduler = new \CaseScheduler();
            $data = array(
                'SCH_NAME'=>'',
                'SCH_DEL_USER_NAME'=>'',
                'SCH_DEL_USER_UID'=>'',
                'PRO_UID'=>$this->proUid,
                'TAS_UID'=>'',
                'SCH_TIME_NEXT_RUN'=>date('Y-m-d H:i:s'),
                'SCH_LAST_RUN_TIME'=>NULL,
                'SCH_STATE'=>'',
                'SCH_LAST_STATE'=>'',
                'USR_UID'=>'',
                'SCH_OPTION'=>'',
                'SCH_START_TIME'=>date('Y-m-d H:i:s'),
                'SCH_START_DATE'=>date('Y-m-d H:i:s'),
                'SCH_DAYS_PERFORM_TASK'=>'',
                'SCH_EVERY_DAYS'=>NULL,
                'SCH_WEEK_DAYS'=>'',
                'SCH_START_DAY'=>'',
                'SCH_START_DAY_OPT_1'=>'',
                'SCH_START_DAY_OPT_2'=>'',
                'SCH_MONTHS'=>'',
                'SCH_END_DATE'=>date('Y-m-d H:i:s'),
                'SCH_REPEAT_EVERY'=>'',
                'SCH_REPEAT_STOP_IF_RUNNING'=>'',
                'CASE_SH_PLUGIN_UID'=>NULL,
                'SCH_DEL_USER_PASS'=>'',
                'SCH_UID'=>$schUid,
                'SCH_REPEAT_UNTIL'=>''
            );

            self::log("Adding Case Scheduler with data: ", $data);
            $caseSchedulerUid = $caseScheduler->create($data);
            self::log("Adding Case Scheduler success!, created case Scheduler id: ", $caseSchedulerUid);

            return $caseSchedulerUid;
        } catch (\Exception $oError) {
            throw ($oError);
        }
    }

    public function removeCaseScheduler($schUid)
    {
        try {
            $caseScheduler = new \CaseScheduler();
            self::log("Remove Case Scheduler: ".$schUid);
            $caseScheduler->remove($schUid);
            self::log("Remove Case Scheduler Success!");
        } catch (\Exception $e) {
            self::log("Exception: ", $e->getMessage(), "Trace: ", $e->getTraceAsString());
            throw $e;
        }
    }

    public function updateCaseScheduler($schUid, $data)
    {
        try {
            $data = array_merge(array("SCH_UID" => $schUid), $data);
            $caseScheduler = new \CaseScheduler();
            $caseScheduler->update($data);
            self::log("Update Case Scheduler Success!");
        } catch (\Exception $e) {
            self::log("Exception: ", $e->getMessage(), "Trace: ", $e->getTraceAsString());
            throw $e;
        }
    }

    public function addWebEntry($weUid)
    {
        try {
            $webEntry = new \WebEntry();
            $webEntryUid = $weUid;
            $webEntry->setWeUid($webEntryUid);
            $webEntry->setProUid($this->proUid);
            $webEntry->setWeMethod('');
            $webEntry->setWeCreateDate(date('Y-m-d H:i:s'));
            $webEntry->save();

            //Return
            self::log("Adding Web Entry success!, created Web Entry id: ", $webEntryUid);
            return $webEntryUid;
        } catch (\Exception $oError) {
            throw ($oError);
        }
    }

    public function removeWebEntry($weUid)
    {
        try {
            $webEntry = new \ProcessMaker\BusinessModel\WebEntry();
            self::log("Remove Web Entry: ".$weUid);
            $webEntry->delete($weUid);
            self::log("Remove Web Entry Success!");
        } catch (\Exception $e) {
            self::log("Exception: ", $e->getMessage(), "Trace: ", $e->getTraceAsString());
            throw $e;
        }
    }

    public function updateWebEntry($webEntryUid, $data)
    {
        try {
            $webEntry = \WebEntryPeer::retrieveByPK($webEntryUid);
            $webEntry->fromArray($data, \BasePeer::TYPE_FIELDNAME);
            $webEntry->save();
            self::log("Update Web Entry Success!");
        } catch (\Exception $e) {
            self::log("Exception: ", $e->getMessage(), "Trace: ", $e->getTraceAsString());
            throw $e;
        }

    }

    public function addLine($position, $direction = "HORIZONTAL")
    {
        try {
            self::log("Add Line with data: position $position, direction $direction");

            $swimlaneElement = new \SwimlanesElements();

            $swiUid = $swimlaneElement->create(array(
                "PRO_UID"  => $this->proUid,
                "SWI_TYPE" => "LINE",
                "SWI_X"    => ($direction == "HORIZONTAL")? 0 : $position,
                "SWI_Y"    => ($direction == "HORIZONTAL")? $position : 0
            ));

            self::log("Add Line Success!");

            //Return
            return $swiUid;
        } catch (\Exception $e) {
            self::log("Exception: ", $e->getMessage(), "Trace: ", $e->getTraceAsString());

            throw $e;
        }
    }

    public function addText($text, $x, $y)
    {
        try {
            self::log("Add Text with data: text \"$text\"");

            $swimlaneElement = new \SwimlanesElements();

            $swiUid = $swimlaneElement->create(array(
                "PRO_UID"  => $this->proUid,
                "SWI_TYPE" => "TEXT",
                "SWI_TEXT" => $text,
                "SWI_X"    => $x,
                "SWI_Y"    => $y
            ));

            self::log("Add Text Success!");

            //Return
            return $swiUid;
        } catch (\Exception $e) {
            self::log("Exception: ", $e->getMessage(), "Trace: ", $e->getTraceAsString());

            throw $e;
        }
    }

    public function createDataByArrayData(array $arrayData)
    {
        try {
            $processes = new \Processes();

            $processes->createProcessPropertiesFromData((object)($arrayData));
        } catch (\Exception $e) {
            self::log("Exception: ", $e->getMessage(), "Trace: ", $e->getTraceAsString());

            throw $e;
        }
    }

    public function createDataFileByArrayFile(array $arrayFile)
    {
        try {
            foreach ($arrayFile as $target => $files) {
                switch (strtoupper($target)) {
                    case "DYNAFORMS":
                        $basePath = PATH_DYNAFORM;
                        break;
                    case "PUBLIC":
                        $basePath = PATH_DATA . "sites" . PATH_SEP . SYS_SYS . PATH_SEP . "public" . PATH_SEP;
                        break;
                    case "TEMPLATES":
                        $basePath = PATH_DATA . "sites" . PATH_SEP . SYS_SYS . PATH_SEP . "mailTemplates" . PATH_SEP;
                        break;
                    default:
                        $basePath = "";
                }

                if (empty($basePath)) {
                    continue;
                }

                foreach ($files as $file) {
                    $filename = $basePath . ((isset($file["file_path"]))? $file["file_path"] : $file["filepath"]);
                    $path = dirname($filename);

                    if (!is_dir($path)) {
                        Util\Common::mk_dir($path, 0775);
                    }

                    file_put_contents($filename, $file["file_content"]);
                    chmod($filename, 0775);
                }
            }
        } catch (\Exception $e) {
            self::log("Exception: ", $e->getMessage(), "Trace: ", $e->getTraceAsString());

            throw $e;
        }
    }

    public function getData($processUid)
    {
        try {
            $process = new \Processes();

            //Get data
            $workflowData = (array)($process->getWorkflowData($processUid));
            $workflowData["process"]["PRO_DYNAFORMS"] = (empty($workflowData["process"]["PRO_DYNAFORMS"]))? "" : serialize($workflowData["process"]["PRO_DYNAFORMS"]);

            $workflowData["process"] = array($workflowData["process"]);
            $workflowData["processCategory"] = (empty($workflowData["processCategory"]))? array() : array($workflowData["processCategory"]);

            //Get files
            $workflowFile = array();

            //Getting DynaForms
            foreach ($workflowData["dynaforms"] as $dynaform) {
                $dynFile = PATH_DYNAFORM . $dynaform["DYN_FILENAME"] . ".xml";

                $workflowFile["DYNAFORMS"][] = array(
                    "filename" => $dynaform["DYN_TITLE"],
                    "filepath" => $dynaform["DYN_FILENAME"] . ".xml",
                    "file_content" => file_get_contents($dynFile)
                );

                $htmlFile = PATH_DYNAFORM . $dynaform["DYN_FILENAME"] . ".html";

                if (file_exists($htmlFile)) {
                    $workflowFile["DYNAFORMS"][] = array(
                        "filename" => $dynaform["DYN_FILENAME"] . ".html",
                        "filepath" => $dynaform["DYN_FILENAME"] . ".html",
                        "file_content" => file_get_contents($htmlFile)
                    );
                }
            }

            //Get public files to exclude
            $arrayPublicFileToExclude = array("wsClient.php");

            //WebEntry
            $criteria = new \Criteria("workflow");

            $criteria->addSelectColumn(\WebEntryPeer::WE_DATA);
            $criteria->add(\WebEntryPeer::PRO_UID, $processUid, \Criteria::EQUAL);
            $criteria->add(\WebEntryPeer::WE_METHOD, "WS", \Criteria::EQUAL);

            $rsCriteria = \WebEntryPeer::doSelectRS($criteria);
            $rsCriteria->setFetchmode(ResultSet::FETCHMODE_ASSOC);

            while ($rsCriteria->next()) {
                $row = $rsCriteria->getRow();

                $arrayPublicFileToExclude[] = $row["WE_DATA"];
                $arrayPublicFileToExclude[] = preg_replace("/^(.+)\.php$/", "$1Post.php", $row["WE_DATA"]);
            }

            //Get templates and public files
            $workspaceTargetDirs = array("TEMPLATES" => "mailTemplates", "PUBLIC" => "public");
            $workspaceDir = PATH_DATA . "sites" . PATH_SEP . SYS_SYS . PATH_SEP;

            foreach ($workspaceTargetDirs as $target => $workspaceTargetDir) {
                $templatesDir = $workspaceDir . $workspaceTargetDir . PATH_SEP . $processUid;
                $templatesFiles = Util\Common::rglob("$templatesDir/*", 0, true);

                foreach ($templatesFiles as $templatesFile) {
                    if (is_dir($templatesFile)) {
                        continue;
                    }

                    $filename = basename($templatesFile);

                    if ($target == "PUBLIC" && in_array($filename, $arrayPublicFileToExclude)) {
                        continue;
                    }

                    $workflowFile[$target][] = array(
                        "filename" => $filename,
                        "filepath" => $processUid . PATH_SEP . $filename,
                        "file_content" => file_get_contents($templatesFile)
                    );
                }
            }

            //Return
            self::log("Getting Workflow data Success!");

            return array($workflowData, $workflowFile);
        } catch (\Exception $e) {
            self::log("Exception: ", $e->getMessage(), "Trace: ", $e->getTraceAsString());

            throw $e;
        }
    }

    public function updateDataUidByArrayUid(array $arrayWorkflowData, array $arrayWorkflowFile, array $arrayUid)
    {
        try {
            $processUidOld = $arrayUid[0]["old_uid"];
            $processUid = $arrayUid[0]["new_uid"];

            //Update Table.Field
            $arrayUpdateTableField = array(
                "tasks"                  => array("fieldname" => "TAS_UID", "oldFieldname" => "TAS_UID_OLD"), //Update TASK.TAS_UID
                "webEntryEvent"          => array("fieldname" => "EVN_UID", "oldFieldname" => "EVN_UID_OLD"), //Update WEB_ENTRY_EVENT.EVN_UID
                "messageEventDefinition" => array("fieldname" => "EVN_UID", "oldFieldname" => "EVN_UID_OLD"), //Update MESSAGE_EVENT_DEFINITION.EVN_UID
                "scriptTask"             => array("fieldname" => "ACT_UID", "oldFieldname" => "ACT_UID_OLD"), //Update SCRIPT_TASK.ACT_UID
                "timerEvent"             => array("fieldname" => "EVN_UID", "oldFieldname" => "EVN_UID_OLD"), //Update TIMER_EVENT.EVN_UID
                "emailEvent"             => array("fieldname" => "EVN_UID", "oldFieldname" => "EVN_UID_OLD"), //Update EMAIL_EVENT.EVN_UID
                "abeConfiguration"       => array("fieldname" => "TAS_UID", "oldFieldname" => "TAS_UID_OLD")  //Update ABE_CONFIGURATION.TAS_UID
            );

            foreach ($arrayUpdateTableField as $key => $value) {
                $table = $key;
                $fieldname = $value["fieldname"];
                $oldFieldname = $value["oldFieldname"];

                if (isset($arrayWorkflowData[$table])) {
                    foreach ($arrayWorkflowData[$table] as $key2 => $value2) {
                        $uid = $arrayWorkflowData[$table][$key2][$fieldname];

                        foreach ($arrayUid as $value3) {
                            $arrayItem = $value3;

                            if ($arrayItem["old_uid"] == $uid) {
                                $arrayWorkflowData[$table][$key2][$fieldname]    = $arrayItem["new_uid"];
                                $arrayWorkflowData[$table][$key2][$oldFieldname] = $uid;
                                break;
                            }
                        }
                    }
                }
            }

            //Workflow tables
            $workflowData = (object)($arrayWorkflowData);

            $processes = new \Processes();
            $processes->setProcessGUID($workflowData, $processUid);
            $processes->renewAll($workflowData);

            $arrayWorkflowData = (array)($workflowData);

            //Workflow files
            foreach ($arrayWorkflowFile as $key => $value) {
                $arrayFile = $value;

                foreach ($arrayFile as $key2 => $value2) {
                    $file = $value2;

                    $arrayWorkflowFile[$key][$key2]["file_path"] = str_replace($processUidOld, $processUid, (isset($file["file_path"]))? $file["file_path"] : $file["filepath"]);
                    $arrayWorkflowFile[$key][$key2]["file_content"] = str_replace($processUidOld, $processUid, $file["file_content"]);
                }
            }

            if (isset($arrayWorkflowData["uid"])) {
                foreach ($arrayWorkflowData["uid"] as $key => $value) {
                    $arrayT = $value;

                    foreach ($arrayT as $key2 => $value2) {
                        $uidOld = $key2;
                        $uid = $value2;

                        foreach ($arrayWorkflowFile as $key3 => $value3) {
                            $arrayFile = $value3;

                            foreach ($arrayFile as $key4 => $value4) {
                                $file = $value4;

                                $arrayWorkflowFile[$key3][$key4]["file_path"] = str_replace($uidOld, $uid, $file["file_path"]);
                                $arrayWorkflowFile[$key3][$key4]["file_content"] = str_replace($uidOld, $uid, $file["file_content"]);
                            }
                        }
                    }
                }
            }

            //Return
            return array($arrayWorkflowData, $arrayWorkflowFile);
        } catch (\Exception $e) {
            self::log("Exception: ", $e->getMessage(), "Trace: ", $e->getTraceAsString());

            throw $e;
        }
    }
}

