<?php
namespace ProcessMaker\Project;

use BasePeer;
use BpmnActivity as Activity;
use BpmnArtifact as Artifact;
use BpmnActivityPeer as ActivityPeer;
use BpmnArtifactPeer as ArtifactPeer;
use BpmnBound as Bound;
use BpmnBoundPeer as BoundPeer;
use BpmnDiagram as Diagram;
use BpmnDiagramPeer as DiagramPeer;
use BpmnEvent as Event;
use BpmnEventPeer as EventPeer;
use BpmnFlow as Flow;
use BpmnFlowPeer as FlowPeer;
use BpmnGateway as Gateway;
use BpmnGatewayPeer as GatewayPeer;
use BpmnLaneset as Laneset;
use BpmnLanesetPeer as LanesetPeer;
use BpmnLane as Lane;
use BpmnLanePeer as LanePeer;
use BpmnParticipant as Participant;
use BpmnParticipantPeer as ParticipantPeer;
use BpmnProject as Project;
use BpmnProcess as Process;
use BpmnProjectPeer as ProjectPeer;
use BpmnProcessPeer as ProcessPeer;
use Criteria as Criteria;
use Exception;
use G;
use ResultSet as ResultSet;
use ProcessMaker\Util\Common;
use ProcessMaker\Exception\ProjectNotFound;
use ProcessMaker\Project\Adapter\BpmnWorkflow;
use Bootstrap;

/**
 * Class Bpmn
 *
 * @package ProcessMaker\Project
 * @author Erik Amaru Ortiz <aortiz.erik@gmail.com, erik@colosa.com>
 */
class Bpmn extends Handler
{
    /**
     * @var \BpmnProject
     */
    protected $project;

    protected $prjUid;

    /**
     * @var \BpmnProcess
     */
    protected $process;

    /**
     * @var \BpmnDiagram
     */
    protected $diagram;

    protected static $excludeFields = array(
        "activity" => array(
            "PRJ_UID",
            "PRO_UID",
            "BOU_ELEMENT_TYPE",
            "BOU_REL_POSITION",
            "BOU_SIZE_IDENTICAL",
            "DIA_UID",
            "BOU_UID",
            "ELEMENT_UID"
        ),
        "event" => array(
            "PRJ_UID",
            "PRO_UID",
            "BOU_ELEMENT_TYPE",
            "BOU_REL_POSITION",
            "BOU_SIZE_IDENTICAL",
            "DIA_UID",
            "BOU_UID",
            "ELEMENT_UID",
            "EVN_ATTACHED_TO",
            "EVN_CONDITION"
        ),
        "gateway" => array(
            "BOU_ELEMENT_TYPE",
            "BOU_REL_POSITION",
            "BOU_SIZE_IDENTICAL",
            "BOU_UID",
            "DIA_UID",
            "ELEMENT_UID",
            "PRJ_UID",
            "PRO_UID"
        ),
        "artifact" => array(
            "PRJ_UID",
            "PRO_UID",
            "BOU_ELEMENT_TYPE",
            "BOU_REL_POSITION",
            "BOU_SIZE_IDENTICAL",
            "DIA_UID",
            "BOU_UID",
            "ELEMENT_UID"
        ),
        "flow" => array(
            "PRJ_UID",
            "DIA_UID",
            "FLO_ELEMENT_DEST_PORT",
            "FLO_ELEMENT_ORIGIN_PORT"
        ),
        "data" => array(
            "PRJ_UID"
        ),
        "participant" => array(
            "PRJ_UID"
        ),
        "laneset" => array(
            "BOU_ELEMENT_TYPE",
            "BOU_SIZE_IDENTICAL",
            "BOU_UID"
        ),
        "lane" => array(
            "BOU_ELEMENT_TYPE",
            "BOU_SIZE_IDENTICAL",
            "BOU_UID"
        )
    );

    private $arrayElementOriginChecked = array();
    protected $contextLog = array();

    public function __construct($data = null)
    {
        if (! is_null($data)) {
            $this->create($data);
        }
        //Define the variables for the logging
        $info = array(
            'ip' => G::getIpAddress(),
            'workspace' => (defined("SYS_SYS"))? SYS_SYS : "Workspace undefined"
        );
        $this->setContextLog($info);
    }

    /**
     * Get the $contextLog value.
     *
     * @return string
     */
    public function getContextLog()
    {
        return $this->contextLog;
    }

    /**
     * Set the value of $contextLog.
     *
     * @param array $k
     * @return void
     */
    public function setContextLog($k)
    {
        $this->contextLog = array_merge($this->contextLog, $k);
    }

    /**
     * Bulk actions
     * We will have actions in many projects
     *
     * @param array $data
     * @return array $result, related to actions send
     */
    public static function doBulk($data)
    {
        //We will review the data format
        if (!is_array($data)) {
            $isJson = is_string($data) && is_array(G::json_decode($data, true));
            if ($isJson) {
                $data = G::json_decode($data, true);
            } else {
                return;
            }
        }
        //We get the action and execute
        $response = array();
        if (isset($data['data'])) {
            foreach ($data['data'] as $key => $val) {
                if (isset($val['action'])) {
                    switch ($val['action']) {
                        case 'delete':
                            $response[] = array(
                                'action' => $val['action'],
                                'data'=> self::doBulkDelete($val['data'])
                            );
                            break;
                        default:
                            $response[] = array(
                                'action' => $val['action'],
                                'title' => 'Unknown action',
                                'status' => '400',
                                'detail' => "Unknown action.",
                                'result' => false
                            );
                        //todo, we can add more bulk actions
                    }
                }
            }
        }
        $result['data'] = $response;

        return $result;
    }

    /**
     * Bulk delete
     * We will delete many projects in bulk
     *
     * @param array $data, array of projectUid
     * @return array $response, information about the action with the projectUid
    */
    public static function doBulkDelete($data)
    {
        //We reviewed the action in all projectUid
        $response = array();
        foreach ($data as $key => $val) {
            //Review if the type is "prj_uid"
            if (isset($val['prj_uid']) && !empty($val['prj_uid']) ) {
                //The project exist?
                if (!Bpmn::exists($val['prj_uid'])) {
                    $response[] = array(
                        'type' => $val['type'],
                        'prj_uid' => $val['prj_uid'],
                        'title' => 'Not found',
                        'token' => strtotime("now"),
                        'status' => '404',
                        'detail' => "The row {$val['prj_uid']} in table Process doesn't exist!.",
                        'result' => false
                    );
                    continue;
                }
                //The project has cases?
                $oBpmnWf = BpmnWorkflow::load($val['prj_uid']);
                if (!$oBpmnWf->canRemove()) {
                    $response[] = array(
                        'type' => $val['type'],
                        'prj_uid' => $val['prj_uid'],
                        'title' => 'Unable to delete project',
                        'token' => strtotime("now"),
                        'status' => '403',
                        'detail' => "Project with prj_uid: {$val['prj_uid']} can not be deleted, it has started cases.",
                        'result' => false

                    );
                    continue;
                }
                //We will to remove
                $oBpmnWf = BpmnWorkflow::load($val['prj_uid']);
                $oBpmnWf->remove();
                $response[] = array(
                    'type' => $val['type'],
                    'prj_uid' => $val['prj_uid'],
                    'status' => '200',
                    'result' => true
                );
            } else {
                //Is not defined the "prj_uid"
                $response[] = array(
                    'type' => $val['type'],
                    'title' => 'Unknown field',
                    'token' => strtotime("now"),
                    'status' => '400',
                    'detail' => "Unknown field.",
                    'result' => false

                );
            }
        }
        $me = new self();
        $me->setContextLog($response);
        $me->syslog(
            'DoBulkDelete',
            200,
            'Do bulk delete',
            $me->getContextLog()
        );

        return $response;
    }

    public function exists($projectUid)
    {
        try {
            $obj = ProjectPeer::retrieveByPK($projectUid);

            return (!is_null($obj))? true : false;
        } catch (\Exception $e) {
            self::log("Exception: ", $e->getMessage(), "Trace: ", $e->getTraceAsString());

            throw $e;
        }
    }

    public static function load($prjUid)
    {
        $me = new self();
        $project = ProjectPeer::retrieveByPK($prjUid);

        if (!is_object($project)) {
            throw new ProjectNotFound($me, $prjUid);
        }

        $me->project = $project;
        $me->prjUid = $me->project->getPrjUid();

        return $me;
    }

    /**
     * @param array| $data array attributes to create and initialize a BpmnProject
     */
    public function create($data)
    {
        // setting defaults
        $data['PRJ_UID'] = array_key_exists('PRJ_UID', $data) ? $data['PRJ_UID'] : Common::generateUID();

        self::log("Create Project with data: ", $data);
        $this->project = new Project();
        $this->project->fromArray($data, BasePeer::TYPE_FIELDNAME);
        $this->project->setPrjCreateDate(date("Y-m-d H:i:s"));
        $this->project->save();

        $this->prjUid = $this->project->getPrjUid();
        self::log("Create Project Success!");
    }

    public function update($data)
    {
        if (isset($data["PRJ_NAME"])) {
            $process = new \ProcessMaker\BusinessModel\Process();

            $process->throwExceptionIfExistsTitle($data["PRJ_NAME"], strtolower("PRJ_NAME"), $this->prjUid);
        }

        if (array_key_exists("PRJ_CREATE_DATE", $data) && empty($data["PRJ_CREATE_DATE"])) {
            unset($data["PRJ_UPDATE_DATE"]);
        }

        if (array_key_exists("PRJ_UPDATE_DATE", $data)) {
            unset($data["PRJ_UPDATE_DATE"]);
        }

        $this->project->fromArray($data, BasePeer::TYPE_FIELDNAME);
        $this->project->setPrjUpdateDate(date("Y-m-d H:i:s"));
        $this->project->save();

        if (isset($data["PRJ_NAME"])) {
            $this->updateDiagram(array("DIA_NAME" => $data["PRJ_NAME"]));
        }
    }

    public function remove($force = false)
    {
        /*
         * 1. Remove Diagram related objects
         * 2. Remove Project related objects
         */

        if (! $force && ! $this->canRemove()) {
            throw new \Exception("Project with prj_uid: {$this->getUid()} can not be deleted, it has started cases.");
        }

        self::log("Remove Project With Uid: {$this->prjUid}");
        foreach ($this->getEvents() as $event) {
            $this->removeEvent($event["EVN_UID"]);
        }
        foreach ($this->getActivities() as $activity) {
            $this->removeActivity($activity["ACT_UID"]);
        }
        foreach ($this->getGateways() as $gateway) {
            $this->removeGateway($gateway["GAT_UID"]);
        }
        foreach ($this->getFlows() as $flow) {
            $this->removeFlow($flow["FLO_UID"]);
        }
        foreach ($this->getArtifacts() as $artifacts) {
            $this->removeArtifact($artifacts["ART_UID"]);
        }
        foreach ($this->getDataCollection() as $bpmnData) {
            $this->removeData($bpmnData["DAT_UID"]);
        }
        foreach ($this->getParticipants() as $participant) {
            $this->removeParticipant($participant["PAR_UID"]);
        }
        foreach ($this->getLanes() as $lane) {
            $this->removeLane($lane["LAN_UID"]);
        }
        foreach ($this->getLanesets() as $laneset) {
            $this->removeLaneset($laneset["LNS_UID"]);
        }
        if ($process = $this->getProcess("object")) {
            $process->delete();
        }
        if ($diagram = $this->getDiagram("object")) {
            $diagram->delete();
        }
        if ($project = $this->getProject("object")) {
            $project->delete();
        }
        self::log("Remove Project Success!");
    }

    public static function removeIfExists($prjUid)
    {
        $project = ProjectPeer::retrieveByPK($prjUid);

        if ($project) {
            $me = new self();
            $me->prjUid = $project->getPrjUid();
            $me->project = $project;
            $me->remove();
        }
    }

    public static function getList($start = null, $limit = null, $filter = "", $changeCaseTo = CASE_UPPER)
    {
        return Project::getAll($start, $limit, $filter, $changeCaseTo);
    }

    public function getUid()
    {
        if (empty($this->project)) {
            throw new \RuntimeException("Error: There is not an initialized project.");
        }

        return $this->prjUid;
    }

    /**
     * @param string $retType
     * @return array|Project
     * @throws \RuntimeException
     */
    public function getProject($retType = "array")
    {
        if (empty($this->project)) {
            throw new \RuntimeException("Error: There is not an initialized project.");
        }

        return $retType == "array" ? $this->project->toArray() : $this->project;
    }

    public function canRemove()
    {
        $totalCases = \Process::getCasesCountForProcess($this->prjUid);
        if ($totalCases == 0) {
            return true;
        } else {
            return false;
        }
    }

    /*
     * Projects elements handlers
     */

    public function addDiagram($data = array())
    {
        if (empty($this->project)) {
            throw new \Exception("Error: There is not an initialized project.");
        }

        // setting defaults
        $data['DIA_UID'] = array_key_exists('DIA_UID', $data) ? $data['DIA_UID'] : Common::generateUID();
        $data['DIA_NAME'] = array_key_exists('DIA_NAME', $data) ? $data['DIA_NAME'] : $this->project->getPrjName();

        $this->diagram = new Diagram();
        $this->diagram->fromArray($data, BasePeer::TYPE_FIELDNAME);
        $this->diagram->setPrjUid($this->project->getPrjUid());
        $this->diagram->save();
    }

    public function updateDiagram($data)
    {
        if (empty($this->project)) {
            throw new \Exception("Error: There is not an initialized project.");
        }
        if (! is_object($this->diagram)) {
            $this->getDiagram();
        }

        $this->diagram->fromArray($data, BasePeer::TYPE_FIELDNAME);
        $this->diagram->save();
    }

    public function getDiagram($retType = "array")
    {
        if (empty($this->diagram)) {
            $diagrams = Diagram::findAllByProUid($this->getUid());

            if (! empty($diagrams)) {
                //NOTICE for ProcessMaker we're just handling a "one to one" relationship between project and process
                $this->diagram = $diagrams[0];
            }
        }

        return ($retType == "array" && is_object($this->diagram)) ? $this->diagram->toArray() : $this->diagram;
    }

    public function addProcess($data = array())
    {
        if (empty($this->diagram)) {
            throw new \Exception("Error: There is not an initialized diagram.");
        }

        // setting defaults
        $data['PRO_UID'] = array_key_exists('PRO_UID', $data) ? $data['PRO_UID'] : Common::generateUID();;
        $data['PRO_NAME'] = array_key_exists('PRO_NAME', $data) ? $data['PRO_NAME'] : $this->diagram->getDiaName();

        $this->process = new Process();
        $this->process->fromArray($data, BasePeer::TYPE_FIELDNAME);
        $this->process->setPrjUid($this->project->getPrjUid());
        $this->process->setDiaUid($this->getDiagram("object")->getDiaUid());
        $this->process->save();
    }

    public function getProcess($retType = "array")
    {
        if (empty($this->process)) {
            $processes = Process::findAllByProUid($this->getUid());

            if (! empty($processes)) {
                //NOTICE for ProcessMaker we're just handling a "one to one" relationship between project and process
                $this->process = $processes[0];
            }
        }

        return $retType == "array" ? $this->process->toArray() : $this->process;
    }

    public function addActivity($data)
    {
        if (! ($process = $this->getProcess("object"))) {
            throw new \Exception(sprintf("Error: There is not an initialized diagram for Project with prj_uid: %s.", $this->getUid()));
        }

        // setting defaults
        $processUid = $process->getProUid();

        $data["ACT_UID"] = (array_key_exists("ACT_UID", $data))? $data["ACT_UID"] : Common::generateUID();
        $data["PRO_UID"] = $processUid;

        if (isset($data["ACT_LOOP_TYPE"]) && $data["ACT_LOOP_TYPE"] == "NONE") {
            $data["ACT_LOOP_TYPE"] = "EMPTY";
        }

        try {
            self::log("Add Activity with data: ", $data);

            $activity = new Activity();
            $activity->fromArray($data);
            $activity->setPrjUid($this->getUid());
            $activity->setProUid($processUid);
            $activity->save();

            self::log("Add Activity Success!");
        } catch (\Exception $e) {
            self::log("Exception: ", $e->getMessage(), "Trace: ", $e->getTraceAsString());
            throw $e;
        }

        return $activity->getActUid();
    }

    public function getActivity($actUid, $retType = 'array')
    {
        $activity = ActivityPeer::retrieveByPK($actUid);

        if ($retType != "object" && ! empty($activity)) {
            $activity = $activity->toArray();
            $activity = self::filterArrayKeys($activity, self::$excludeFields["activity"]);
        }

        return $activity;
    }

    public function getActivities($start = null, $limit = null, $filter = '', $changeCaseTo = CASE_UPPER)
    {
        if (is_array($start)) {
            extract($start);
        }

        $filter = $changeCaseTo != CASE_UPPER ? array_map("strtolower", self::$excludeFields["activity"]) : self::$excludeFields["activity"];

        return self::filterCollectionArrayKeys(
            Activity::getAll($this->getUid(), $start, $limit, $filter, $changeCaseTo),
            $filter
        );
    }

    public function updateActivity($actUid, $data)
    {
        try {
            if (isset($data["ACT_LOOP_TYPE"]) && $data["ACT_LOOP_TYPE"] == "NONE") {
                $data["ACT_LOOP_TYPE"] = "EMPTY";
            }

            self::log("Update Activity: $actUid, with data: ", $data);

            $activity = ActivityPeer::retrieveByPk($actUid);
            $activity->fromArray($data);
            $activity->save();

            self::log("Update Activity Success!");
        } catch (\Exception $e) {
            self::log("Exception: ", $e->getMessage(), "Trace: ", $e->getTraceAsString());
            throw $e;
        }
    }

    public function removeActivity($actUid)
    {
        try {
            self::log("Remove Activity: $actUid");

            $activity = ActivityPeer::retrieveByPK($actUid);
            if (isset($activity)) {
                $activity->delete();
                Flow::removeAllRelated($actUid);
            } else {
                throw new Exception(G::LoadTranslation("ID_ACTIVITY_DOES_NOT_EXIST", array("act_uid", $actUid)));
            }
            self::log("Remove Activity Success!");
        } catch (\Exception $e) {
            self::log("Exception: ", $e->getMessage(), "Trace: ", $e->getTraceAsString());
            throw $e;
        }
    }

    public function activityExists($actUid)
    {
        return \BpmnActivity::exists($actUid);
    }

    public function addEvent($data)
    {
        // setting defaults
        $processUid = $this->getProcess("object")->getProUid();

        $data['EVN_UID'] = array_key_exists('EVN_UID', $data) ? $data['EVN_UID'] : Common::generateUID();
        $data["PRO_UID"] = $processUid;

        try {
            self::log("Add Event with data: ", $data);

            $event = new Event();
            $event->fromArray($data);
            $event->setPrjUid($this->project->getPrjUid());
            $event->setProUid($processUid);
            $event->save();

            self::log("Add Event Success!");

            return $event->getEvnUid();
        } catch (\Exception $e) {
            self::log("Exception: ", $e->getMessage(), "Trace: ", $e->getTraceAsString());
            throw $e;
        }
    }

    public function getEvent($evnUid, $retType = 'array')
    {
        $event = EventPeer::retrieveByPK($evnUid);

        if ($retType != "object" && ! empty($event)) {
            $event = $event->toArray();
            $event = self::filterArrayKeys($event, self::$excludeFields["event"]);
        }

        return $event;
    }

    public function getEvents($start = null, $limit = null, $filter = '', $changeCaseTo = CASE_UPPER)
    {
        if (is_array($start)) {
            extract($start);
        }

        //return Event::getAll($this->project->getPrjUid(), null, null, '', $changeCaseTo);

        $filter = $changeCaseTo != CASE_UPPER ? array_map("strtolower", self::$excludeFields["event"]) : self::$excludeFields["event"];

        return self::filterCollectionArrayKeys(
            Event::getAll($this->getUid(), $start, $limit, $filter, $changeCaseTo),
            $filter
        );
    }

    public function updateEvent($evnUid, $data)
    {
        /*if (array_key_exists("EVN_CANCEL_ACTIVITY", $data)) {
            $data["EVN_CANCEL_ACTIVITY"] = $data["EVN_CANCEL_ACTIVITY"] ? 1 : 0;
        }

        if (array_key_exists("EVN_WAIT_FOR_COMPLETION", $data)) {
            $data["EVN_WAIT_FOR_COMPLETION"] = $data["EVN_WAIT_FOR_COMPLETION"] ? 1 : 0;
        }*/

        try {
            self::log("Update Event: $evnUid", "With data: ", $data);

            $event = EventPeer::retrieveByPk($evnUid);
            $event->fromArray($data);
            $event->save();

            self::log("Update Event Success!");
        } catch (\Exception $e) {
            self::log("Exception: ", $e->getMessage(), "Trace: ", $e->getTraceAsString());
            throw $e;
        }
    }

    public function removeEvent($evnUid)
    {
        try {
            self::log("Remove Event: $evnUid");
            $event = EventPeer::retrieveByPK($evnUid);

            $event->delete();

            self::log("Remove Event Success!");
        } catch (\Exception $e) {
            self::log("Exception: ", $e->getMessage(), "Trace: ", $e->getTraceAsString());
            throw $e;
        }
    }

    public function addGateway($data)
    {
        // setting defaults
        $processUid = $this->getProcess("object")->getProUid();

        $data['GAT_UID'] = array_key_exists('GAT_UID', $data) ? $data['GAT_UID'] : Common::generateUID();
        $data["PRO_UID"] = $processUid;

        try {
            self::log("Add Gateway with data: ", $data);
            $gateway = new Gateway();
            $gateway->fromArray($data);
            $gateway->setPrjUid($this->getUid());
            $gateway->setProUid($processUid);
            $gateway->save();
            self::log("Add Gateway Success!");
        } catch (\Exception $e) {
            self::log("Exception: ", $e->getMessage(), "Trace: ", $e->getTraceAsString());
            throw $e;
        }

        return $gateway->getGatUid();
    }

    public function updateGateway($gatUid, $data)
    {
        try {
            self::log("Update Gateway: $gatUid", "With data: ", $data);

            $gateway = GatewayPeer::retrieveByPk($gatUid);

            $gateway->fromArray($data);
            $gateway->save();

            self::log("Update Gateway Success!");
        } catch (\Exception $e) {
            self::log("Exception: ", $e->getMessage(), "Trace: ", $e->getTraceAsString());
            throw $e;
        }
    }

    public function getGateway($gatUid, $retType = 'array')
    {
        $gateway = GatewayPeer::retrieveByPK($gatUid);

        if ($retType != "object" && ! empty($gateway)) {
            $gateway = $gateway->toArray();
            $gateway = self::filterArrayKeys($gateway, self::$excludeFields["gateway"]);
        }

        return $gateway;
    }

    public function getGateway2($gatewayUid)
    {
        try {
            $criteria = new Criteria("workflow");

            $criteria->addSelectColumn(GatewayPeer::TABLE_NAME . ".*");
            $criteria->addSelectColumn(BoundPeer::TABLE_NAME . ".*");
            $criteria->addJoin(GatewayPeer::GAT_UID, BoundPeer::ELEMENT_UID, Criteria::LEFT_JOIN);
            $criteria->add(GatewayPeer::GAT_UID, $gatewayUid, Criteria::EQUAL);

            $rsCriteria = GatewayPeer::doSelectRS($criteria);
            $rsCriteria->setFetchmode(ResultSet::FETCHMODE_ASSOC);

            if ($rsCriteria->next()) {
                //Return
                return $rsCriteria->getRow();
            }

            //Return
            return false;
        } catch (\Exception $e) {
            self::log("Exception: ", $e->getMessage(), "Trace: ", $e->getTraceAsString());

            throw $e;
        }
    }

    public function getGateways($start = null, $limit = null, $filter = '', $changeCaseTo = CASE_UPPER)
    {
        if (is_array($start)) {
            extract($start);
        }

        //return  Gateway::getAll($this->getUid(), $start, $limit, $filter, $changeCaseTo);
        $filter = $changeCaseTo != CASE_UPPER ? array_map("strtolower", self::$excludeFields["gateway"]) : self::$excludeFields["gateway"];

        return self::filterCollectionArrayKeys(
            Gateway::getAll($this->getUid(), $start, $limit, $filter, $changeCaseTo),
            $filter
        );
    }

    public function removeGateway($gatUid)
    {
        try {
            self::log("Remove Gateway: $gatUid");

            $gateway = GatewayPeer::retrieveByPK($gatUid);
            $gateway->delete();

            // remove related object (flows)
            Flow::removeAllRelated($gatUid);

            self::log("Remove Gateway Success!");
        } catch (\Exception $e) {
            self::log("Exception: ", $e->getMessage(), "Trace: ", $e->getTraceAsString());
            throw $e;
        }
    }

    public function throwExceptionFlowIfIsAnInvalidMessageFlow(array $bpmnFlow)
    {
        try {
            if ($bpmnFlow["FLO_TYPE"] == "MESSAGE" &&
                $bpmnFlow["FLO_ELEMENT_ORIGIN_TYPE"] == "bpmnEvent" && $bpmnFlow["FLO_ELEMENT_DEST_TYPE"] == "bpmnEvent"
            ) {
                $flagValid = true;

                $arrayEventType = array("START", "END", "INTERMEDIATE");

                $arrayAux = array(
                    array("eventUid" => $bpmnFlow["FLO_ELEMENT_ORIGIN"], "eventMarker" => array("MESSAGETHROW",
                        "EMAIL")),
                    array("eventUid" => $bpmnFlow["FLO_ELEMENT_DEST"],   "eventMarker" => array("MESSAGECATCH"))
                );

                foreach ($arrayAux as $value) {
                    $criteria = new \Criteria("workflow");

                    $criteria->addSelectColumn(\BpmnEventPeer::EVN_UID);
                    $criteria->add(\BpmnEventPeer::EVN_UID, $value["eventUid"], \Criteria::EQUAL);
                    $criteria->add(\BpmnEventPeer::EVN_TYPE, $arrayEventType, \Criteria::IN);
                    $criteria->add(\BpmnEventPeer::EVN_MARKER, $value["eventMarker"], \Criteria::IN);

                    $rsCriteria = \BpmnEventPeer::doSelectRS($criteria);

                    if (!$rsCriteria->next()) {
                        $flagValid = false;
                        break;
                    }
                }

                if (!$flagValid) {
                    throw new \RuntimeException("Invalid Message Flow.");
                }
            }
        } catch (\Exception $e) {
            self::log("Exception: ", $e->getMessage(), "Trace: ", $e->getTraceAsString());

            throw $e;
        }
    }

    public function addFlow($data)
    {
        self::log("Add Flow with data: ", $data);

        // setting defaults
        $data['FLO_UID'] = array_key_exists('FLO_UID', $data) ? $data['FLO_UID'] : Common::generateUID();

        if (array_key_exists('FLO_STATE', $data)) {
            $data['FLO_STATE'] = is_array($data['FLO_STATE']) ? json_encode($data['FLO_STATE']) : $data['FLO_STATE'];
        }

        try {
            switch ($data["FLO_ELEMENT_ORIGIN_TYPE"]) {
                case "bpmnActivity": $class = "BpmnActivity"; break;
                case "bpmnGateway": $class = "BpmnGateway"; break;
                case "bpmnEvent": $class = "BpmnEvent"; break;
                case "bpmnArtifact": $class = "BpmnArtifact"; break;
                case "bpmnData": $class = "BpmnData"; break;
                case "bpmnParticipant": $class = "BpmnParticipant"; break;
                case "bpmnLaneset": $class = "BpmnLaneset"; break;
                case "bpmnLane": $class = "BpmnLane"; break;
                default:
                    throw new \RuntimeException(sprintf("Invalid Object type, accepted types: [%s|%s|%s|%s], given %s.",
                        "BpmnActivity", "BpmnBpmnGateway", "BpmnEvent", "bpmnArtifact", $data["FLO_ELEMENT_ORIGIN_TYPE"]
                    ));
            }

            // Validate origin object exists
            if (! $class::exists($data["FLO_ELEMENT_ORIGIN"])) {
                throw new \RuntimeException(sprintf("Reference not found, the %s with UID: %s, does not exist!",
                    ucfirst($data["FLO_ELEMENT_ORIGIN_TYPE"]), $data["FLO_ELEMENT_ORIGIN"]
                ));
            }

            switch ($data["FLO_ELEMENT_DEST_TYPE"]) {
                case "bpmnActivity": $class = "BpmnActivity"; break;
                case "bpmnGateway": $class = "BpmnGateway"; break;
                case "bpmnEvent": $class = "BpmnEvent"; break;
                case "bpmnArtifact": $class = "BpmnArtifact"; break;
                case "bpmnData": $class = "BpmnData"; break;
                case "bpmnParticipant": $class = "BpmnParticipant"; break;
                case "bpmnLaneset": $class = "BpmnLaneset"; break;
                case "bpmnLane": $class = "BpmnLane"; break;
                default:
                    throw new \RuntimeException(sprintf("Invalid Object type, accepted types: [%s|%s|%s|%s], given %s.",
                        "BpmnActivity", "BpmnBpmnGateway", "BpmnEvent", "bpmnArtifact", $data["FLO_ELEMENT_DEST_TYPE"]
                    ));
            }

            // Validate origin object exists
            if (! $class::exists($data["FLO_ELEMENT_DEST"])) {
                throw new \RuntimeException(sprintf("Reference not found, the %s with UID: %s, does not exist!",
                    ucfirst($data["FLO_ELEMENT_DEST_TYPE"]), $data["FLO_ELEMENT_DEST"]
                ));
            }

            //Check and validate Message Flow
            $this->throwExceptionFlowIfIsAnInvalidMessageFlow($data);

            //Validating FLO_CONDITION value
            if (array_key_exists('FLO_CONDITION', $data) && is_null($data['FLO_CONDITION'])) {
                $data['FLO_CONDITION'] = '';
            }

            //Create
            $flow = new Flow();
            $flow->fromArray($data, BasePeer::TYPE_FIELDNAME);
            $flow->setPrjUid($this->getUid());
            $flow->setDiaUid($this->getDiagram("object")->getDiaUid());
            $flow->setFloPosition($this->getFlowNextPosition($data["FLO_UID"], $data["FLO_TYPE"], $data["FLO_ELEMENT_ORIGIN"]));
            $flow->save();

            self::log("Add Flow Success!");

            return $flow->getFloUid();
        } catch (\Exception $e) {
            self::log("Exception: ", $e->getMessage(), "Trace: ", $e->getTraceAsString());

            throw $e;
        }
    }

    public function updateFlow($floUid, $data)
    {
        self::log("Update Flow: $floUid", "With data: ", $data);

        // setting defaults
        if (array_key_exists('FLO_STATE', $data)) {
            $data['FLO_STATE'] = is_array($data['FLO_STATE']) ? json_encode($data['FLO_STATE']) : $data['FLO_STATE'];
        }

        try {
            //Check and validate Message Flow
            $this->throwExceptionFlowIfIsAnInvalidMessageFlow($data);

            //Validating FLO_CONDITION value
            if (array_key_exists('FLO_CONDITION', $data) && is_null($data['FLO_CONDITION'])) {
                $data['FLO_CONDITION'] = '';
            }

            //Update
            $flow = FlowPeer::retrieveByPk($floUid);
            $flow->fromArray($data);
            $flow->save();

            self::log("Update Flow Success!");
        } catch (\Exception $e) {
            self::log("Exception: ", $e->getMessage(), "Trace: ", $e->getTraceAsString());
            throw $e;
        }
    }

    public function getFlow($floUid, $retType = 'array')
    {
        $flow = FlowPeer::retrieveByPK($floUid);

        if ($retType != "object" && ! empty($flow)) {
            $flow = $flow->toArray();
            $flow = self::filterArrayKeys($flow, self::$excludeFields["flow"]);
        }

        return $flow;
    }

    public function getFlows($start = null, $limit = null, $filter = '', $changeCaseTo = CASE_UPPER)
    {
        if (is_array($start)) {
            extract($start);
        }

        $filter = $changeCaseTo != CASE_UPPER ? array_map("strtolower", self::$excludeFields["flow"]) : self::$excludeFields["flow"];

        return self::filterCollectionArrayKeys(
            Flow::getAll($this->getUid(), $start, $limit, $filter, $changeCaseTo),
            $filter
        );
    }

    public function removeFlow($floUid)
    {
        try {
            self::log("Remove Flow: $floUid");

            $flow = FlowPeer::retrieveByPK($floUid);
            $this->reOrderFlowPosition($flow->getFloElementOrigin(), $flow->getFloPosition());

            $flow->delete();

            self::log("Remove Flow Success!");
        } catch (\Exception $e) {
            self::log("Exception: ", $e->getMessage(), "Trace: ", $e->getTraceAsString());
            throw $e;
        }
    }

    public function flowExists($floUid)
    {
        return \BpmnFlow::exists($floUid);
    }

    public function addArtifact($data)
    {
        // setting defaults
        $processUid = $this->getProcess("object")->getProUid();

        $data['ART_UID'] = array_key_exists('ART_UID', $data) ? $data['ART_UID'] : Common::generateUID();
        $data["PRO_UID"] = $processUid;

        try {
            self::log("Add Artifact with data: ", $data);
            $artifact = new Artifact();
            $artifact->fromArray($data, BasePeer::TYPE_FIELDNAME);
            $artifact->setPrjUid($this->getUid());
            $artifact->setProUid($this->getProcess("object")->getProUid());
            $artifact->save();
            self::log("Add Artifact Success!");
        } catch (\Exception $e) {
            self::log("Exception: ", $e->getMessage(), "Trace: ", $e->getTraceAsString());
            throw $e;
        }

        return $artifact->getArtUid();
    }

    public function updateArtifact($artUid, $data)
    {
        try {
            self::log("Update Artifact: $artUid", "With data: ", $data);

            $artifact = ArtifactPeer::retrieveByPk($artUid);

            $artifact->fromArray($data);
            $artifact->save();

            self::log("Update Artifact Success!");
        } catch (\Exception $e) {
            self::log("Exception: ", $e->getMessage(), "Trace: ", $e->getTraceAsString());
            throw $e;
        }
    }

    public function getArtifact($artUid, $retType = 'array')
    {
        $artifact = ArtifactPeer::retrieveByPK($artUid);

        if ($retType != "object" && ! empty($artifact)) {
            $artifact = $artifact->toArray();
            $artifact = self::filterArrayKeys($artifact, self::$excludeFields["artifact"]);
        }

        return $artifact;
    }

    public function getArtifacts($start = null, $limit = null, $filter = '', $changeCaseTo = CASE_UPPER)
    {
        if (is_array($start)) {
            extract($start);
        }

        $filter = $changeCaseTo != CASE_UPPER ? array_map("strtolower", self::$excludeFields["artifact"]) : self::$excludeFields["artifact"];

        return self::filterCollectionArrayKeys(
            Artifact::getAll($this->getUid(), $start, $limit, $filter, $changeCaseTo),
            $filter
        );
    }

    public function removeArtifact($artUid)
    {
        try {
            self::log("Remove Artifact: $artUid");

            $artifact = ArtifactPeer::retrieveByPK($artUid);
            $artifact->delete();

            // remove related object (flows)
            Flow::removeAllRelated($artUid);

            self::log("Remove Artifact Success!");
        } catch (\Exception $e) {
            self::log("Exception: ", $e->getMessage(), "Trace: ", $e->getTraceAsString());
            throw $e;
        }
    }

    public function addData($data)
    {
        // setting defaults
        $processUid = $this->getProcess("object")->getProUid();

        $data['DATA_UID'] = array_key_exists('DAT_UID', $data) ? $data['DAT_UID'] : Common::generateUID();
        $data["PRO_UID"] = $processUid;

        try {
            self::log("Add BpmnData with data: ", $data);
            $bpmnData = new \BpmnData();
            $bpmnData->fromArray($data, BasePeer::TYPE_FIELDNAME);
            $bpmnData->setPrjUid($this->getUid());
            $bpmnData->setProUid($this->getProcess("object")->getProUid());
            $bpmnData->save();
            self::log("Add BpmnData Success!");
        } catch (\Exception $e) {
            self::log("Exception: ", $e->getMessage(), "Trace: ", $e->getTraceAsString());
            throw $e;
        }

        return $bpmnData->getDatUid();
    }

    public function updateData($datUid, $data)
    {
        try {
            self::log("Update BpmnData: $datUid", "With data: ", $data);

            $bpmnData = \BpmnDataPeer::retrieveByPk($datUid);

            $bpmnData->fromArray($data);
            $bpmnData->save();

            self::log("Update BpmnData Success!");
        } catch (\Exception $e) {
            self::log("Exception: ", $e->getMessage(), "Trace: ", $e->getTraceAsString());
            throw $e;
        }
    }

    public function getData($datUid, $retType = 'array')
    {
        $bpmnData = \BpmnDataPeer::retrieveByPK($datUid);

        if ($retType != "object" && ! empty($bpmnData)) {
            $bpmnData = $bpmnData->toArray();
            $bpmnData = self::filterArrayKeys($bpmnData, self::$excludeFields["data"]);
        }

        return $bpmnData;
    }

    public function getDataCollection($start = null, $limit = null, $filter = '', $changeCaseTo = CASE_UPPER)
    {
        if (is_array($start)) {
            extract($start);
        }

        $filter = $changeCaseTo != CASE_UPPER ? array_map("strtolower", self::$excludeFields["data"]) : self::$excludeFields["data"];

        return self::filterCollectionArrayKeys(
            \BpmnData::getAll($this->getUid(), $start, $limit, $filter, $changeCaseTo),
            $filter
        );
    }

    public function removeData($datUid)
    {
        try {
            self::log("Remove BpmnData: $datUid");

            $bpmnData = \BpmnDataPeer::retrieveByPK($datUid);
            $bpmnData->delete();

            // remove related object (flows)
            Flow::removeAllRelated($datUid);

            self::log("Remove BpmnData Success!");
        } catch (\Exception $e) {
            self::log("Exception: ", $e->getMessage(), "Trace: ", $e->getTraceAsString());
            throw $e;
        }
    }

    public function addParticipant($data)
    {
        // setting defaults
        $processUid = $this->getProcess("object")->getProUid();

        $data['PAR_UID'] = array_key_exists('PAR_UID', $data) ? $data['PAR_UID'] : Common::generateUID();
        $data["PRO_UID"] = $processUid;

        try {
            self::log("Add Participant with data: ", $data);
            $participant = new Participant();
            $participant->fromArray($data, BasePeer::TYPE_FIELDNAME);
            $participant->setPrjUid($this->getUid());
            $participant->setProUid($this->getProcess("object")->getProUid());
            $participant->save();
            self::log("Add Participant Success!");
        } catch (\Exception $e) {
            self::log("Exception: ", $e->getMessage(), "Trace: ", $e->getTraceAsString());
            throw $e;
        }

        return $participant->getParUid();
    }

    public function updateParticipant($parUid, $data)
    {
        try {
            self::log("Update Participant: $parUid", "With data: ", $data);

            $participant = ParticipantPeer::retrieveByPk($parUid);

            $participant->fromArray($data);
            $participant->save();

            self::log("Update Participant Success!");
        } catch (\Exception $e) {
            self::log("Exception: ", $e->getMessage(), "Trace: ", $e->getTraceAsString());
            throw $e;
        }
    }

    public function getParticipant($parUid, $retType = 'array')
    {
        $participant = ParticipantPeer::retrieveByPK($parUid);

        if ($retType != "object" && ! empty($participant)) {
            $participant = $participant->toArray();
            $participant = self::filterArrayKeys($participant, self::$excludeFields["participant"]);
        }

        return $participant;
    }

    public function getParticipants($start = null, $limit = null, $filter = '', $changeCaseTo = CASE_UPPER)
    {
        if (is_array($start)) {
            extract($start);
        }

        $filter = $changeCaseTo != CASE_UPPER ? array_map("strtolower", self::$excludeFields["participant"]) : self::$excludeFields["participant"];

        return self::filterCollectionArrayKeys(
            Participant::getAll($this->getUid(), $start, $limit, $filter, $changeCaseTo),
            $filter
        );
    }

    public function removeParticipant($parUid)
    {
        try {
            self::log("Remove Participant: $parUid");

            $participant = ParticipantPeer::retrieveByPK($parUid);
            $participant->delete();

            // remove related object (flows)
            Flow::removeAllRelated($parUid);

            self::log("Remove Participant Success!");
        } catch (\Exception $e) {
            self::log("Exception: ", $e->getMessage(), "Trace: ", $e->getTraceAsString());
            throw $e;
        }
    }

    public function addLane($data)
    {
        // setting defaults
        $processUid = $this->getProcess("object")->getProUid();

        $data['LAN_UID'] = array_key_exists('LAN_UID', $data) ? $data['LAN_UID'] : Common::generateUID();
        $data["PRO_UID"] = $processUid;

        try {
            self::log("Add Lane with data: ", $data);
            $lane = new Lane();
            $lane->fromArray($data, BasePeer::TYPE_FIELDNAME);
            $lane->setPrjUid($this->getUid());
            //$lane->setProUid($this->getProcess("object")->getProUid());
            $lane->save();
            self::log("Add Lane Success!");
        } catch (\Exception $e) {
            self::log("Exception: ", $e->getMessage(), "Trace: ", $e->getTraceAsString());
            throw $e;
        }

        return $lane->getLanUid();
    }

    public function getLane($lanUid, $retType = 'array')
    {
        $lane = LanePeer::retrieveByPK($lanUid);

        if ($retType != "object" && ! empty($lane)) {
            $lane = $lane->toArray();
            $lane = self::filterArrayKeys($lane, self::$excludeFields["lane"]);
        }

        return $lane;
    }

    public function getLanes($start = null, $limit = null, $filter = '', $changeCaseTo = CASE_UPPER)
    {
        if (is_array($start)) {
            extract($start);
        }

        $filter = $changeCaseTo != CASE_UPPER ? array_map("strtolower", self::$excludeFields["lane"]) : self::$excludeFields["lane"];

        return self::filterCollectionArrayKeys(
            Lane::getAll($this->getUid(), $start, $limit, $filter, $changeCaseTo),
            $filter
        );
    }

    public function removeLane($lanUid)
    {
        try {
            self::log("Remove Lane: $lanUid");

            $lane = LanePeer::retrieveByPK($lanUid);
            $lane->delete();

            // remove related object (flows)
            Flow::removeAllRelated($lanUid);

            self::log("Remove Lane Success!");
        } catch (\Exception $e) {
            self::log("Exception: ", $e->getMessage(), "Trace: ", $e->getTraceAsString());
            throw $e;
        }
    }

    public function updateLane($lanUid, $data)
    {
        try {
            self::log("Update Lane: $lanUid", "With data: ", $data);
            $lane = LanePeer::retrieveByPk($lanUid);

            $lane->fromArray($data);
            $lane->save();

            self::log("Update Lane Success!");
        } catch (\Exception $e) {
            self::log("Exception: ", $e->getMessage(), "Trace: ", $e->getTraceAsString());
            throw $e;
        }
    }

    public function addLaneset($data)
    {
        // setting defaults
        $processUid = $this->getProcess("object")->getProUid();
        $data['LNS_UID'] = array_key_exists('LNS_UID', $data) ? $data['LNS_UID'] : Common::generateUID();
        $data["PRO_UID"] = $processUid;

        try {
            self::log("Add Laneset with data: ", $data);
            $laneset = new Laneset();
            $laneset->fromArray($data, BasePeer::TYPE_FIELDNAME);
            $laneset->setPrjUid($this->getUid());
            $laneset->setProUid($this->getProcess("object")->getProUid());
            $laneset->save();
            self::log("Add Laneset Success!");
        } catch (\Exception $e) {
            self::log("Exception: ", $e->getMessage(), "Trace: ", $e->getTraceAsString());
            throw $e;
        }

        return $laneset->getLnsUid();
    }

    public function getLaneset($lnsUid, $retType = 'array')
    {
        $laneset = LanesetPeer::retrieveByPK($lnsUid);

        if ($retType != "object" && ! empty($laneset)) {
            $laneset = $laneset->toArray();
            $laneset = self::filterArrayKeys($laneset, self::$excludeFields["laneset"]);
        }

        return $laneset;
    }

    public function getLanesets($start = null, $limit = null, $filter = '', $changeCaseTo = CASE_UPPER)
    {
        if (is_array($start)) {
            extract($start);
        }
        $filter = $changeCaseTo != CASE_UPPER ? array_map("strtolower", self::$excludeFields["laneset"]) : self::$excludeFields["laneset"];
        return self::filterCollectionArrayKeys(
            Laneset::getAll($this->getUid(), $start, $limit, $filter, $changeCaseTo),
            $filter
        );
    }

    public function removeLaneset($lnsUid)
    {
        try {
            self::log("Remove Laneset: $lnsUid");

            $laneset = LanesetPeer::retrieveByPK($lnsUid);
            $laneset->delete();

            // remove related object (flows)
            Flow::removeAllRelated($lnsUid);

            self::log("Remove Laneset Success!");
        } catch (\Exception $e) {
            self::log("Exception: ", $e->getMessage(), "Trace: ", $e->getTraceAsString());
            throw $e;
        }
    }

    public function updateLaneset($lnsUid, $data)
    {
        try {
            self::log("Update Laneset: $lnsUid", "With data: ", $data);

            $laneset = LanesetPeer::retrieveByPk($lnsUid);

            $laneset->fromArray($data);
            $laneset->save();

            self::log("Update Laneset Success!");
        } catch (\Exception $e) {
            self::log("Exception: ", $e->getMessage(), "Trace: ", $e->getTraceAsString());
            throw $e;
        }
    }

    public function isModified($element, $uid, $newData)
    {
        $data = array();

        switch ($element) {
            case "activity": $data = $this->getActivity($uid); break;
            case "gateway":  $data = $this->getGateway($uid); break;
            case "event":    $data = $this->getEvent($uid); break;
            case "flow":     $data = $this->getFlow($uid); break;
        }
        //self::log("saved data: ", $data, "new data: ", $newData);
        //self::log("checksum saved data: ", self::getChecksum($data), "checksum new data: ", self::getChecksum($newData));
        return (self::getChecksum($data) !== self::getChecksum($newData));
    }

    public function setDisabled($value = true)
    {
        $status = $value ? "DISABLED" : "ACTIVE";
        $this->update(array("PRJ_STATUS" => $status));
    }

    public function getGatewayByDirectionActivityAndFlow($gatewayDirection, $activityUid)
    {
        try {
            $criteria = new Criteria("workflow");

            if ($gatewayDirection == "DIVERGING") {
                $criteria->addSelectColumn(FlowPeer::FLO_ELEMENT_DEST . " AS GAT_UID");

                $criteria->add(FlowPeer::FLO_ELEMENT_ORIGIN, $activityUid, Criteria::EQUAL);
                $criteria->add(FlowPeer::FLO_ELEMENT_ORIGIN_TYPE, "bpmnActivity", Criteria::EQUAL);
                $criteria->add(FlowPeer::FLO_ELEMENT_DEST_TYPE, "bpmnGateway", Criteria::EQUAL);
            } else {
                //CONVERGING
                $criteria->addSelectColumn(FlowPeer::FLO_ELEMENT_ORIGIN . " AS GAT_UID");

                $criteria->add(FlowPeer::FLO_ELEMENT_ORIGIN_TYPE, "bpmnGateway", Criteria::EQUAL);
                $criteria->add(FlowPeer::FLO_ELEMENT_DEST, $activityUid, Criteria::EQUAL);
                $criteria->add(FlowPeer::FLO_ELEMENT_DEST_TYPE, "bpmnActivity", Criteria::EQUAL);
            }

            $criteria->add(FlowPeer::PRJ_UID, $this->prjUid, Criteria::EQUAL);
            $criteria->add(FlowPeer::FLO_TYPE, "SEQUENCE", Criteria::EQUAL);

            $rsCriteria = FlowPeer::doSelectRS($criteria);
            $rsCriteria->setFetchmode(ResultSet::FETCHMODE_ASSOC);

            $gatewayUid = "";

            if ($rsCriteria->next()) {
                $row = $rsCriteria->getRow();

                $gatewayUid = $row["GAT_UID"];
            }

            //Return
            return $this->getGateway2($gatewayUid);
        } catch (\Exception $e) {
            self::log("Exception: ", $e->getMessage(), "Trace: ", $e->getTraceAsString());

            throw $e;
        }
    }

    public function getFlowNextPosition ($sFloUid, $sFloType, $sFloElementOrigin)
    {
        try {

            $oCriteria = new Criteria('workflow');
            $oCriteria->addSelectColumn( '(COUNT(*) + 1) AS FLOW_POS' );
            $oCriteria->add(\BpmnFlowPeer::PRJ_UID, $this->getUid());
            $oCriteria->add(\BpmnFlowPeer::DIA_UID, $this->getDiagram("object")->getDiaUid());
            $oCriteria->add(\BpmnFlowPeer::FLO_UID, $sFloUid, \Criteria::NOT_EQUAL);
            $oCriteria->add(\BpmnFlowPeer::FLO_TYPE, $sFloType);
            $oCriteria->add(\BpmnFlowPeer::FLO_ELEMENT_ORIGIN, $sFloElementOrigin);
            $oDataset = \BpmnFlowPeer::doSelectRS($oCriteria);
            $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $oDataset->next();
            $aRow = $oDataset->getRow();
            return (int)($aRow["FLOW_POS"]);

        } catch (Exception $oException) {
            throw $oException;
        }
    }

    public function reOrderFlowPosition ($sFloOrigin, $iPosition)
    {
        try {
            $con = \Propel::getConnection('workflow');
            $oCriteria = new Criteria( 'workflow' );
            $oCriteria->add( \BpmnFlowPeer::FLO_ELEMENT_ORIGIN, $sFloOrigin );
            $oCriteria->add( \BpmnFlowPeer::FLO_POSITION, $iPosition, '>' );
            $oDataset = \BpmnFlowPeer::doSelectRS( $oCriteria );
            $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
            while ($oDataset->next()) {
                $aRow = $oDataset->getRow();
                $newPosition = ((int)$aRow['FLO_POSITION'])-1;
                $oCriteriaTemp = new Criteria( 'workflow' );
                $oCriteriaTemp->add( \BpmnFlowPeer::FLO_UID, $aRow['FLO_UID'] );
                $oCriteria2 = new Criteria('workflow');
                $oCriteria2->add(\BpmnFlowPeer::FLO_POSITION, $newPosition);
                BasePeer::doUpdate($oCriteriaTemp, $oCriteria2, $con);
            }
        } catch (Exception $oException) {
            throw $oException;
        }
    }

    private function __getElementsBetweenElementOriginAndElementDest(
        $elementOriginUid,
        $elementOriginType,
        $elementDestUid,
        $elementDestType,
        $index
    ) {
        try {
            if (isset($this->arrayElementOriginChecked[$elementOriginUid]) && $this->arrayElementOriginChecked[$elementOriginUid] == $elementOriginType) {
                //Return
                return [];
            }

            $this->arrayElementOriginChecked[$elementOriginUid] = $elementOriginType;

            if ($index > 0 && $elementOriginType == $elementDestType) {
                if ($elementOriginUid == $elementDestUid) {
                    $arrayEvent = [];

                    array_unshift($arrayEvent, [$elementDestUid, $elementDestType]);

                    //Return
                    return $arrayEvent;
                }

                //Return
                return [];
            } else {
                //Flows
                $arrayFlow = \BpmnFlow::findAllBy([
                    \BpmnFlowPeer::FLO_TYPE                => ["MESSAGE", \Criteria::NOT_EQUAL],
                    \BpmnFlowPeer::FLO_ELEMENT_ORIGIN      => $elementOriginUid,
                    \BpmnFlowPeer::FLO_ELEMENT_ORIGIN_TYPE => $elementOriginType
                ]);

                foreach ($arrayFlow as $value) {
                    $arrayFlowData = $value->toArray();

                    $arrayEvent = $this->__getElementsBetweenElementOriginAndElementDest(
                        $arrayFlowData["FLO_ELEMENT_DEST"],
                        $arrayFlowData["FLO_ELEMENT_DEST_TYPE"],
                        $elementDestUid,
                        $elementDestType,
                        $index + 1
                    );

                    if (!empty($arrayEvent)) {
                        array_unshift($arrayEvent, [$elementOriginUid, $elementOriginType]);

                        //Return
                        return $arrayEvent;
                    }
                }

                //Return
                return [];
            }
        } catch (\Exception $e) {
            self::log("Exception: ", $e->getMessage(), "Trace: ", $e->getTraceAsString());

            throw $e;
        }
    }

    public function getElementsBetweenElementOriginAndElementDest(
        $elementOriginUid,
        $elementOriginType,
        $elementDestUid,
        $elementDestType
    ) {
        try {
            $this->arrayElementOriginChecked = [];

            //Return
            return call_user_func_array([$this, "__getElementsBetweenElementOriginAndElementDest"], array_merge(func_get_args(), [0]));
        } catch (\Exception $e) {
            self::log("Exception: ", $e->getMessage(), "Trace: ", $e->getTraceAsString());

            throw $e;
        }
    }

    /**
     * Logging information related to project
     * When the user doDeleteBulk
     *
     * @param string $channel
     * @param string $level
     * @param string $message
     * @param array $context
     *
     * @return void
     * @throws Exception
     */
    private function syslog(
        $channel,
        $level,
        $message,
        $context = array()
    )
    {
        try {
            Bootstrap::registerMonolog($channel, $level, $message, $context, $context['workspace'], 'processmaker.log');
        } catch (Exception $e) {
            throw $e;
        }
    }
}

