<?php
namespace ProcessMaker\Project\Adapter;

use ProcessMaker\Project;
use ProcessMaker\Util;

/**
 * Class BpmnWorkflow
 *
 * @package ProcessMaker\Project\Adapter
 * @author Erik Amaru Ortiz <aortiz.erik@gmail.com, erik@colosa.com>
 */
class BpmnWorkflow extends Project\Bpmn
{
    /**
     * @var \ProcessMaker\Project\Workflow
     */
    protected $wp;

    const BPMN_GATEWAY_COMPLEX    = "COMPLEX";
    const BPMN_GATEWAY_PARALLEL   = "PARALLEL";
    const BPMN_GATEWAY_INCLUSIVE  = "INCLUSIVE";
    const BPMN_GATEWAY_EXCLUSIVE  = "EXCLUSIVE";
    const BPMN_GATEWAY_EVENTBASED = "EVENTBASED";

    private $arrayTaskAttribute = array(
        "gateway-to-gateway"               => array("type" => "GATEWAYTOGATEWAY",                 "prefix" => "gtg-"),
        "end-message-event"                => array("type" => "END-MESSAGE-EVENT",                "prefix" => "eme-"),
        "start-message-event"              => array("type" => "START-MESSAGE-EVENT",              "prefix" => "sme-"),
        "intermediate-throw-message-event" => array("type" => "INTERMEDIATE-THROW-MESSAGE-EVENT", "prefix" => "itme-"),
        "intermediate-catch-message-event" => array("type" => "INTERMEDIATE-CATCH-MESSAGE-EVENT", "prefix" => "icme-"),
        "start-timer-event"                => array("type" => "START-TIMER-EVENT",                "prefix" => "ste-"),
        "intermediate-catch-timer-event"   => array("type" => "INTERMEDIATE-CATCH-TIMER-EVENT",   "prefix" => "icte-"),
        "end-email-event"                  => array("type" => "END-EMAIL-EVENT",                  "prefix" => "eee-")
    );

    private $arrayElementTaskRelation = array();

    /**
     * OVERRIDES
     */

    public static function load($prjUid)
    {

        $parent = parent::load($prjUid);
        //return new BpmnWorkflow();

        $me = new BpmnWorkflow();

        $me->project = $parent->project;
        $me->prjUid = $parent->project->getPrjUid();
        $me->wp = Project\Workflow::load($me->prjUid);

        return $me;
    }

    public function create($data)
    {
        try {
            parent::create($data);
        } catch (\Exception $e) {
            throw new \RuntimeException(sprintf("Can't create Bpmn Project." . PHP_EOL . $e->getMessage()));
        }

        try {
            $wpData = array();
            $wpData["PRO_UID"] = $this->getUid();

            if (array_key_exists("PRJ_NAME", $data)) {
                $wpData["PRO_TITLE"] = $data["PRJ_NAME"];
            }
            if (array_key_exists("PRJ_DESCRIPTION", $data)) {
                $wpData["PRO_DESCRIPTION"] = $data["PRJ_DESCRIPTION"];
            }
            if (array_key_exists("PRJ_AUTHOR", $data)) {
                $wpData["PRO_CREATE_USER"] = $data["PRJ_AUTHOR"];
            }

            $this->wp = new Project\Workflow();
            $this->wp->create($wpData);

            //Add Audit Log
            $ogetProcess = new \Process();
            $getprocess=$ogetProcess->load($this->getUid());
            $nameProcess=$getprocess['PRO_TITLE'];
            \G::auditLog("ImportProcess", 'PMX File Imported '.$nameProcess. ' ('.$this->getUid().')');

        } catch (\Exception $e) {
            $prjUid = $this->getUid();
            //$this->remove();
            $bpmnProject = Project\Bpmn::load($prjUid);
            $bpmnProject->remove();

            throw new \RuntimeException(sprintf(
                "Can't create Bpmn Project with prj_uid: %s, workflow creation fails." . PHP_EOL . $e->getMessage()
                , $prjUid
            ));
        }
    }

    public function update($data)
    {
        parent::update($data);

        $arrayData = array();

        if (isset($data["PRJ_UID"])) {
            $arrayData["PRO_UID"] = $data["PRJ_UID"];
        }

        if (isset($data["PRJ_NAME"])) {
            $arrayData["PRO_TITLE"] = $data["PRJ_NAME"];
        }

        if (isset($data["PRJ_DESCRIPTION"])) {
            $arrayData["PRO_DESCRIPTION"] = $data["PRJ_DESCRIPTION"];
        }

        if (isset($data["PRJ_STATUS"])) {
            $arrayData["PRO_STATUS"] = $data["PRJ_STATUS"];
        }

        $arrayData["PRO_UPDATE_DATE"] = date("Y-m-d H:i:s");

        $this->wp->update($arrayData);
    }

    public static function getList($start = null, $limit = null, $filter = "", $changeCaseTo = CASE_UPPER)
    {
        $bpmnProjects = parent::getList($start, $limit, $filter);
        $workflowProjects = Project\Workflow::getList($start, $limit, $filter);
        $bpmnProjectsUid = array();
        $bpmnProjectsList = array();
        $list = array();

        foreach ($bpmnProjects as $bpmnProject) {
            $bpmnProjectsList[$bpmnProject["PRJ_UID"]] = $bpmnProject;
        }

        $bpmnProjectsUid = array_keys($bpmnProjectsList);

        foreach ($workflowProjects as $workflowProject) {
            $data = array();

            $data["PRJ_UID"] = $workflowProject["PRO_UID"];
            $data["PRJ_NAME"] = $workflowProject["PRO_TITLE"];
            $data["PRJ_DESCRIPTION"] = $workflowProject["PRO_DESCRIPTION"];
            $data["PRJ_CATEGORY"] = $workflowProject["PRO_CATEGORY"];

            if (in_array($workflowProject["PRO_UID"], $bpmnProjectsUid)) {
                $data["PRJ_TYPE"] = "bpmn";
                $data["PRJ_CREATE_DATE"] = $bpmnProjectsList[$workflowProject["PRO_UID"]]["PRJ_CREATE_DATE"];
                $data["PRJ_UPDATE_DATE"] = $bpmnProjectsList[$workflowProject["PRO_UID"]]["PRJ_UPDATE_DATE"];
            } else {
                $data["PRJ_TYPE"] = "classic";
                $data["PRJ_CREATE_DATE"] = $workflowProject["PRO_CREATE_DATE"];
                $data["PRJ_UPDATE_DATE"] = $workflowProject["PRO_UPDATE_DATE"];
            }

            $data["PRJ_STATUS"] = $workflowProject["PRO_STATUS"];

            if ($changeCaseTo != CASE_UPPER) {
                $data = array_change_key_case($data, $changeCaseTo);
            }

            $list[] = $data;
        }

        return $list;
    }

    public function addActivity($data)
    {
        $taskData = array();

        $actUid = parent::addActivity($data);
        $taskData["TAS_UID"] = $actUid;

        if (array_key_exists("ACT_NAME", $data)) {
            $taskData["TAS_TITLE"] = $data["ACT_NAME"];
        }
        if (array_key_exists("ACT_NAME", $data)) {
            $taskData["TAS_POSX"] = $data["BOU_X"];
        }
        if (array_key_exists("ACT_NAME", $data)) {
            $taskData["TAS_POSY"] = $data["BOU_Y"];
        }
        if (array_key_exists("ACT_TYPE", $data)) {
            if ($data["ACT_TYPE"] == "SUB_PROCESS") {
                $taskData["TAS_TYPE"] = "SUBPROCESS";
            } else {
                $taskData["TAS_TYPE"] = "NORMAL";
            }
        }

        $activityCurrent = \BpmnActivityPeer::retrieveByPK($actUid);

        if ($activityCurrent->getActType() == "TASK" && $activityCurrent->getActTaskType() == "SCRIPTTASK") {
            $taskData["TAS_TYPE"] = "SCRIPT-TASK";
            $taskData["TAS_ASSIGN_TYPE"] = "BALANCED";
        }

        $this->wp->addTask($taskData);

        return $actUid;
    }

    public function updateActivity($actUid, $data)
    {
        //Update Activity
        $activityBefore = \BpmnActivityPeer::retrieveByPK($actUid);

        parent::updateActivity($actUid, $data);

        $activityCurrent = \BpmnActivityPeer::retrieveByPK($actUid);

        //Update Task
        $taskData = array();

        if (array_key_exists("ACT_NAME", $data)) {
            $taskData["TAS_TITLE"] = $data["ACT_NAME"];
        }
        if (array_key_exists("ACT_NAME", $data)) {
            $taskData["TAS_POSX"] = $data["BOU_X"];
        }
        if (array_key_exists("ACT_NAME", $data)) {
            $taskData["TAS_POSY"] = $data["BOU_Y"];
        }

        if ($activityBefore->getActTaskType() != "SCRIPTTASK" && $activityCurrent->getActTaskType() == "SCRIPTTASK") {
            $taskData["TAS_TYPE"] = "SCRIPT-TASK";
            $taskData["TAS_ASSIGN_TYPE"] = "BALANCED";
        }

        if ($activityBefore->getActTaskType() == "SCRIPTTASK" && $activityCurrent->getActTaskType() != "SCRIPTTASK") {
            $taskData["TAS_TYPE"] = "NORMAL";
            $taskData["TAS_ASSIGN_TYPE"] = "BALANCED";

            $scriptTask = new \ProcessMaker\BusinessModel\ScriptTask();

            $scriptTask->deleteWhere(array(
                \ScriptTaskPeer::PRJ_UID => $activityCurrent->getPrjUid(),
                \ScriptTaskPeer::ACT_UID => $activityCurrent->getActUid()
            ));
        }

        $this->wp->updateTask($actUid, $taskData);
    }

    public function removeActivity($actUid)
    {
        $activity = \BpmnActivityPeer::retrieveByPK($actUid);

        parent::removeActivity($actUid);
        $this->wp->removeTask($actUid);

        //Delete Script-Task
        $scriptTask = new \ProcessMaker\BusinessModel\ScriptTask();

        $scriptTask->deleteWhere(array(
            \ScriptTaskPeer::PRJ_UID => $activity->getPrjUid(),
            \ScriptTaskPeer::ACT_UID => $activity->getActUid()
        ));
    }

    public function removeElementTaskRelation($elementUid, $elementType)
    {
        try {
            $elementTaskRelation = new \ProcessMaker\BusinessModel\ElementTaskRelation();

            $arrayElementTaskRelationData = $elementTaskRelation->getElementTaskRelationWhere(
                array(
                    \ElementTaskRelationPeer::PRJ_UID      => $this->wp->getUid(),
                    \ElementTaskRelationPeer::ELEMENT_UID  => $elementUid,
                    \ElementTaskRelationPeer::ELEMENT_TYPE => $elementType
                ),
                true
            );

            if (!is_null($arrayElementTaskRelationData)) {
                //Task - Delete
                $arrayTaskData = $this->wp->getTask($arrayElementTaskRelationData["TAS_UID"]);

                if (!is_null($arrayTaskData)) {
                    $this->wp->removeTask($arrayElementTaskRelationData["TAS_UID"]);
                }

                //Element-Task-Relation - Delete
                $elementTaskRelation->deleteWhere(array(\ElementTaskRelationPeer::ETR_UID => $arrayElementTaskRelationData["ETR_UID"]));

                //Array - Delete element
                unset($this->arrayElementTaskRelation[$elementUid]);
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function removeGateway($gatewayUid)
    {
        try {
            //Element-Task-Relation - Delete
            $this->removeElementTaskRelation($gatewayUid, "bpmnGateway");

            parent::removeGateway($gatewayUid);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function addFlow($data)
    {
        $floUid = parent::addFlow($data);

        // to add start event->activity  as initial or end task
        switch ($data["FLO_ELEMENT_ORIGIN_TYPE"]) {
            case "bpmnEvent":
                switch ($data["FLO_ELEMENT_DEST_TYPE"]) {
                    case "bpmnActivity":
                        $event = \BpmnEventPeer::retrieveByPK($data["FLO_ELEMENT_ORIGIN"]);

                        //Setting as start Task
                        if (!is_null($event) && $event->getEvnType() == "START" && $event->getEvnMarker() == "EMPTY") {
                            $this->wp->setStartTask($data["FLO_ELEMENT_DEST"]);
                        }

                        //$this->updateEventStartObjects($data["FLO_ELEMENT_ORIGIN"], $data["FLO_ELEMENT_DEST"]);

                        //WebEntry-Event - Update
                        $this->updateWebEntryEventByEvent($data["FLO_ELEMENT_ORIGIN"], array("ACT_UID" => $data["FLO_ELEMENT_DEST"]));
                        break;
                    case "bpmnEvent":
                        $messageEventRelationUid = $this->createMessageEventRelationByBpmnFlow(\BpmnFlowPeer::retrieveByPK($floUid));
                        break;
                }
                break;
            case "bpmnActivity":
                switch ($data["FLO_ELEMENT_DEST_TYPE"]) {
                    case "bpmnEvent":
                        $event = \BpmnEventPeer::retrieveByPK($data["FLO_ELEMENT_DEST"]);

                        //Setting as end Task
                        if (!is_null($event) && $event->getEvnType() == "END" && $event->getEvnMarker() == "EMPTY") {
                            $this->wp->setEndTask($data["FLO_ELEMENT_ORIGIN"]);
                        }
                        break;
                }
                break;
        }

        return $floUid;
    }

    public function updateFlow($floUid, $data, $flows)
    {
        $flowBefore = \BpmnFlowPeer::retrieveByPK($floUid);

        parent::updateFlow($floUid, $data);

        $flowCurrent = \BpmnFlowPeer::retrieveByPK($floUid);

        //Verify case: Event1(start) -> Activity1 -----Update-to----> Event1(start) -> Activity2
        if ($flowBefore->getFloElementOriginType() == "bpmnEvent" && $flowBefore->getFloElementDestType() == "bpmnActivity" &&
            $flowCurrent->getFloElementOriginType() == "bpmnEvent" && $flowCurrent->getFloElementDestType() == "bpmnActivity" &&
            $flowBefore->getFloElementOrigin() == $flowCurrent->getFloElementOrigin() &&
            $flowBefore->getFloElementDest() != $flowCurrent->getFloElementDest()
        ) {
            $event = \BpmnEventPeer::retrieveByPK($flowBefore->getFloElementOrigin());

            if (!is_null($event) && $event->getEvnType() == "START" && $event->getEvnMarker() == "EMPTY") {
                //Remove as start Task
                $this->wp->setStartTask($flowBefore->getFloElementDest(), false);

                //Setting as start Task
                $this->wp->setStartTask($flowCurrent->getFloElementDest());

                //$this->updateEventStartObjects($flowCurrent->getFloElementOrigin(), $flowCurrent->getFloElementDest());

                //WebEntry-Event - Update
                $this->updateWebEntryEventByEvent($flowCurrent->getFloElementOrigin(), array("ACT_UID" => $flowCurrent->getFloElementDest()));
            }
        }

        //Verify case: Activity1 -> Event1(end) -----Update-to----> Activity2 -> Event1(end)
        if ($flowBefore->getFloElementOriginType() == "bpmnActivity" && $flowBefore->getFloElementDestType() == "bpmnEvent" &&
            $flowCurrent->getFloElementOriginType() == "bpmnActivity" && $flowCurrent->getFloElementDestType() == "bpmnEvent" &&
            $flowBefore->getFloElementOrigin() != $flowCurrent->getFloElementOrigin() &&
            $flowBefore->getFloElementDest() == $flowCurrent->getFloElementDest()
        ) {
            $event = \BpmnEventPeer::retrieveByPK($flowBefore->getFloElementDest());

            if (!is_null($event) && $event->getEvnType() == "END" && $event->getEvnMarker() == "EMPTY") {
                //Remove as end Task
                $this->wp->setEndTask($flowBefore->getFloElementOrigin(), false);

                //Setting as end Task
                $this->wp->setEndTask($flowCurrent->getFloElementOrigin());
            }
        }

        //Verify case: Activity1 -> Event1(end) -----Update-to----> Activity1 -> Activity2
        if ($flowBefore->getFloElementOriginType() == "bpmnActivity" && $flowBefore->getFloElementDestType() == "bpmnEvent" &&
            $flowCurrent->getFloElementOriginType() == "bpmnActivity" && $flowCurrent->getFloElementDestType() == "bpmnActivity" &&
            $flowBefore->getFloElementOrigin() == $flowCurrent->getFloElementOrigin()
        ) {
            $event = \BpmnEventPeer::retrieveByPK($flowBefore->getFloElementDest());

            if (!is_null($event) && $event->getEvnType() == "END" && $event->getEvnMarker() == "EMPTY") {
                //Remove as end Task
                $this->wp->setEndTask($flowBefore->getFloElementOrigin(), false);
            }
        }

        //Verify case: Activity1 -> Activity2 -----Update-to----> Activity1 -> Activity3
        if ($flowBefore->getFloElementOriginType() == "bpmnActivity" && $flowBefore->getFloElementDestType() == "bpmnActivity" &&
            $flowCurrent->getFloElementOriginType() == "bpmnActivity" && $flowCurrent->getFloElementDestType() == "bpmnActivity" &&
            $flowBefore->getFloElementOrigin() == $flowCurrent->getFloElementOrigin() &&
            $flowBefore->getFloElementDest() != $flowCurrent->getFloElementDest()
        ) {
            $this->wp->removeRouteFromTo($flowBefore->getFloElementOrigin(), $flowBefore->getFloElementDest());
        }

        //Verify case: Event1(message) -> Event2(message) -----Update-to----> Event(message) -> Event(message)
        if ($flowBefore->getFloType() == "MESSAGE" &&
            $flowBefore->getFloElementOriginType() == "bpmnEvent" && $flowBefore->getFloElementDestType() == "bpmnEvent"
        ) {
            //Delete Message-Event-Relation
            $messageEventRelation = new \ProcessMaker\BusinessModel\MessageEventRelation();

            $messageEventRelation->deleteWhere(array(
                \MessageEventRelationPeer::PRJ_UID       => $flowBefore->getPrjUid(),
                \MessageEventRelationPeer::EVN_UID_THROW => $flowBefore->getFloElementOrigin(),
                \MessageEventRelationPeer::EVN_UID_CATCH => $flowBefore->getFloElementDest()
            ));

            //Create Message-Event-Relation
            if ($flowCurrent->getFloType() == "MESSAGE" &&
                $flowCurrent->getFloElementOriginType() == "bpmnEvent" && $flowCurrent->getFloElementDestType() == "bpmnEvent"
            ) {
                $messageEventRelationUid = $this->createMessageEventRelationByBpmnFlow($flowCurrent);
            }
        }
    }

    public function removeFlow($floUid)
    {
        $flow = \BpmnFlowPeer::retrieveByPK($floUid);
        parent::removeFlow($floUid);

        // verify case: event(start) -> activity
        // => find the corresponding task and unset it as start task
        if ($flow->getFloElementOriginType() == "bpmnEvent" &&
            $flow->getFloElementDestType() == "bpmnActivity"
        ) {
            $bpmnFlow = \BpmnFlow::findOneBy(array(
                \BpmnFlowPeer::FLO_ELEMENT_ORIGIN      => $flow->getFloElementOrigin(),
                \BpmnFlowPeer::FLO_ELEMENT_ORIGIN_TYPE => "bpmnEvent",
                \BpmnFlowPeer::FLO_ELEMENT_DEST        => $flow->getFloElementDest(),
                \BpmnFlowPeer::FLO_ELEMENT_DEST_TYPE   => "bpmnActivity"
            ));

            if (is_null($bpmnFlow)) {
                $event = \BpmnEventPeer::retrieveByPK($flow->getFloElementOrigin());

                if (!is_null($event) && $event->getEvnType() == "START" && $event->getEvnMarker() == "EMPTY") {
                    $activity = \BpmnActivityPeer::retrieveByPK($flow->getFloElementDest());

                    //Remove as start Task
                    if (!is_null($activity)) {
                        $this->wp->setStartTask($activity->getActUid(), false);
                    }
                }
            }

            //$this->updateEventStartObjects($flow->getFloElementOrigin(), "");

            //WebEntry-Event - Update
            if (is_null($bpmnFlow)) {
                $this->updateWebEntryEventByEvent($flow->getFloElementOrigin(), array("WEE_STATUS" => "DISABLED"));
            }
        } elseif ($flow->getFloElementOriginType() == "bpmnActivity" &&
            $flow->getFloElementDestType() == "bpmnEvent") {
            // verify case: activity -> event(end)
            // => find the corresponding task and unset it as start task
            $event = \BpmnEventPeer::retrieveByPK($flow->getFloElementDest());

            if (!is_null($event) && $event->getEvnType() == "END" && $event->getEvnMarker() == "EMPTY") {
                $activity = \BpmnActivityPeer::retrieveByPK($flow->getFloElementOrigin());

                //Remove as end Task
                if (! is_null($activity)) {
                    $this->wp->setEndTask($activity->getActUid(), false);
                }
            }
        } else {
            switch ($flow->getFloElementOriginType()) {
                case "bpmnActivity":
                    switch ($flow->getFloElementDestType()) {
                        //Activity1 -> Activity2
                        case "bpmnActivity":
                            $this->wp->removeRouteFromTo($flow->getFloElementOrigin(), $flow->getFloElementDest());
                            break;
                    }
                    break;
                case "bpmnEvent":
                    switch ($flow->getFloElementDestType()) {
                        //Event1 -> Event2
                        case "bpmnEvent":
                            if ($flow->getFloType() == "MESSAGE") {
                                //Delete Message-Event-Relation
                                $messageEventRelation = new \ProcessMaker\BusinessModel\MessageEventRelation();

                                $messageEventRelation->deleteWhere(array(
                                    \MessageEventRelationPeer::PRJ_UID       => $flow->getPrjUid(),
                                    \MessageEventRelationPeer::EVN_UID_THROW => $flow->getFloElementOrigin(),
                                    \MessageEventRelationPeer::EVN_UID_CATCH => $flow->getFloElementDest()
                                ));
                            }
                            break;
                    }
                    break;
            }
        }

        // TODO Complete for other routes, activity->activity, activity->gateway and viceversa
    }

    public function updateEventActivityDefinition(\BpmnEvent $bpmnEvent, $flagStartTask)
    {
        try {
            if ($bpmnEvent->getEvnType() == "START") {
                //Flows
                $arrayFlow = \BpmnFlow::findAllBy(array(
                    \BpmnFlowPeer::FLO_TYPE                => array("MESSAGE", \Criteria::NOT_EQUAL),
                    \BpmnFlowPeer::FLO_ELEMENT_ORIGIN      => $bpmnEvent->getEvnUid(),
                    \BpmnFlowPeer::FLO_ELEMENT_ORIGIN_TYPE => "bpmnEvent"
                ));

                foreach ($arrayFlow as $value) {
                    $arrayFlowData = $value->toArray();

                    switch ($arrayFlowData["FLO_ELEMENT_DEST_TYPE"]) {
                        case "bpmnActivity":
                            //Setting as start Task
                            //or
                            //Remove as start Task
                            $bwp = new self;
                            if ($bwp->getActivity($arrayFlowData["FLO_ELEMENT_DEST"])) {
                                $this->wp->setStartTask($arrayFlowData["FLO_ELEMENT_DEST"], $flagStartTask);
                            }
                            break;
                    }
                }
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function removeEventDefinition(\BpmnEvent $bpmnEvent)
    {
        try {
            //WebEntry-Event - Delete
            if ($bpmnEvent->getEvnType() == "START" && $bpmnEvent->getEvnMarker() == "EMPTY") {
                $webEntryEvent = new \ProcessMaker\BusinessModel\WebEntryEvent();

                if ($webEntryEvent->existsEvent($bpmnEvent->getPrjUid(), $bpmnEvent->getEvnUid())) {
                    $arrayWebEntryEventData = $webEntryEvent->getWebEntryEventByEvent($bpmnEvent->getPrjUid(), $bpmnEvent->getEvnUid(), true);

                    $webEntryEvent->delete($arrayWebEntryEventData["WEE_UID"]);
                }
            }

            //Message-Event-Definition - Delete
            $arrayEventType   = array("START", "END", "INTERMEDIATE");
            $arrayEventMarker = array("MESSAGETHROW", "MESSAGECATCH");

            if (in_array($bpmnEvent->getEvnType(), $arrayEventType) && in_array($bpmnEvent->getEvnMarker(), $arrayEventMarker)) {
                $messageEventDefinition = new \ProcessMaker\BusinessModel\MessageEventDefinition();

                if ($messageEventDefinition->existsEvent($bpmnEvent->getPrjUid(), $bpmnEvent->getEvnUid())) {
                    $arrayMessageEventDefinitionData = $messageEventDefinition->getMessageEventDefinitionByEvent($bpmnEvent->getPrjUid(), $bpmnEvent->getEvnUid(), true);

                    $messageEventDefinition->delete($arrayMessageEventDefinitionData["MSGED_UID"]);
                }
            }

            //Timer-Event - Delete
            $arrayEventType   = array("START", "INTERMEDIATE");
            $arrayEventMarker = array("TIMER");

            if (in_array($bpmnEvent->getEvnType(), $arrayEventType) && in_array($bpmnEvent->getEvnMarker(), $arrayEventMarker)) {
                $timerEvent = new \ProcessMaker\BusinessModel\TimerEvent();

                $timerEvent->deleteWhere(array(
                    \TimerEventPeer::PRJ_UID => array($bpmnEvent->getPrjUid(), \Criteria::EQUAL),
                    \TimerEventPeer::EVN_UID => array($bpmnEvent->getEvnUid(), \Criteria::EQUAL)
                ));
            }

            //Email-Event - Delete
            $arrayEventType   = array("END", "INTERMEDIATE");
            $arrayEventMarker = array("EMAIL");

            if (in_array($bpmnEvent->getEvnType(), $arrayEventType) && in_array($bpmnEvent->getEvnMarker(), $arrayEventMarker)) {
                $emailEvent = new \ProcessMaker\BusinessModel\EmailEvent();

                if ($emailEvent->existsEvent($bpmnEvent->getPrjUid(), $bpmnEvent->getEvnUid())) {
                    $arrayEmailEventData = $emailEvent->getEmailEventData($bpmnEvent->getPrjUid(), $bpmnEvent->getEvnUid());
                    $arrayEmailEventData = array_change_key_case($arrayEmailEventData, CASE_UPPER);
                    $emailEvent->delete($bpmnEvent->getPrjUid(), $arrayEmailEventData["EMAIL_EVENT_UID"], true);
                }
            }

            //Element-Task-Relation - Delete
            $this->removeElementTaskRelation($bpmnEvent->getEvnUid(), "bpmnEvent");
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function addEvent($data)
    {
        if (! array_key_exists("EVN_TYPE", $data)) {
            throw new \RuntimeException("Required param \"EVN_TYPE\" is missing.");
        }

        $eventUid = parent::addEvent($data);
        $event = \BpmnEventPeer::retrieveByPK($eventUid);

        //// create case scheduler
        //if ($event && $event->getEvnMarker() == "TIMER" && $event->getEvnType() == "START") {
        //    $this->wp->addCaseScheduler($eventUid);
        //}
        //
        //// create web entry
        //if ($event && $event->getEvnMarker() == "MESSAGE" && $event->getEvnType() == "START") {
        //    $this->wp->addWebEntry($eventUid);
        //}

        return $eventUid;
    }

    public function updateEvent($eventUid, array $arrayEventData)
    {
        try {
            $bpmnEvent = \BpmnEventPeer::retrieveByPK($eventUid);

            if ((isset($arrayEventData["EVN_TYPE"]) && $arrayEventData["EVN_TYPE"] != $bpmnEvent->getEvnType()) ||
                (isset($arrayEventData["EVN_MARKER"]) && $arrayEventData["EVN_MARKER"] != $bpmnEvent->getEvnMarker())
            ) {
                $this->updateEventActivityDefinition($bpmnEvent, false);
                $this->removeEventDefinition($bpmnEvent);
            }

            parent::updateEvent($eventUid, $arrayEventData);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function removeEvent($eventUid)
    {
        try {
            $bpmnEvent = \BpmnEventPeer::retrieveByPK($eventUid);

            $this->updateEventActivityDefinition($bpmnEvent, false);
            $this->removeEventDefinition($bpmnEvent);

            parent::removeEvent($eventUid);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /*
    public function updateEventStartObjects($eventUid, $taskUid)
    {
        $event = \BpmnEventPeer::retrieveByPK($eventUid);

        //Case-Scheduler - Update
        if (!is_null($event) && $event->getEvnType() == "START" && $event->getEvnMarker() == "TIMER") {
            $caseScheduler = new \CaseScheduler();

            if ($caseScheduler->Exists($eventUid)) {
                $this->wp->updateCaseScheduler($eventUid, array("TAS_UID" => $taskUid));
            }
        }

        //Update web entry
        //if (!is_null($event) && $event->getEvnType() == "START" && $event->getEvnMarker() == "MESSAGE") {
        //    $this->wp->updateWebEntry($eventUid, array("TAS_UID" => $taskUid));
        //}
    }
    */

    public function createTaskByElement($elementUid, $elementType, $key)
    {
        try {
            if (isset($this->arrayElementTaskRelation[$elementUid])) {
                $taskUid = $this->arrayElementTaskRelation[$elementUid];
            } else {
                $taskPosX = 0;
                $taskPosY = 0;

                $flow = \BpmnFlow::findOneBy(array(
                    \BpmnFlowPeer::FLO_ELEMENT_ORIGIN      => $elementUid,
                    \BpmnFlowPeer::FLO_ELEMENT_ORIGIN_TYPE => $elementType
                ));

                if (!is_null($flow)) {
                    $arrayFlowData = $flow->toArray();

                    $taskPosX = (int)($arrayFlowData["FLO_X1"]);
                    $taskPosY = (int)($arrayFlowData["FLO_Y1"]);
                } else {
                    $flow = \BpmnFlow::findOneBy(array(
                        \BpmnFlowPeer::FLO_ELEMENT_DEST      => $elementUid,
                        \BpmnFlowPeer::FLO_ELEMENT_DEST_TYPE => $elementType
                    ));

                    if (!is_null($flow)) {
                        $arrayFlowData = $flow->toArray();

                        $taskPosX = (int)($arrayFlowData["FLO_X2"]);
                        $taskPosY = (int)($arrayFlowData["FLO_Y2"]);
                    }
                }

                $prefix   = $this->arrayTaskAttribute[$key]["prefix"];
                $taskType = $this->arrayTaskAttribute[$key]["type"];

                $taskUid = $this->wp->addTask(array(
                    "TAS_UID"   => $prefix . substr(Util\Common::generateUID(), (32 - strlen($prefix)) * -1),
                    "TAS_TYPE"  => $taskType,
                    "TAS_TITLE" => $taskType,
                    "TAS_POSX"  => $taskPosX,
                    "TAS_POSY"  => $taskPosY
                ));

                if ($elementType == "bpmnEvent" &&
                    in_array($key, array("end-message-event", "start-message-event", "intermediate-catch-message-event"))
                ) {
                    if (in_array($key, array("start-message-event", "intermediate-catch-message-event"))) {
                        //Task - User
                        //Assign to admin
                        $task = new \Tasks();

                        $result = $task->assignUser($taskUid, "00000000000000000000000000000001", 1);
                    }
                }

                //Element-Task-Relation - Create
                $elementTaskRelation = new \ProcessMaker\BusinessModel\ElementTaskRelation();

                $arrayResult = $elementTaskRelation->create(
                    $this->wp->getUid(),
                    array(
                        "ELEMENT_UID"  => $elementUid,
                        "ELEMENT_TYPE" => $elementType,
                        "TAS_UID"      => $taskUid
                    )
                );

                //Array - Add element
                $this->arrayElementTaskRelation[$elementUid] = $taskUid;
            }

            //Return
            return $taskUid;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function mapBpmnGatewayToWorkflowRoutes($activityUid, $gatewayUid)
    {
        try {
            $arrayGatewayData = \BpmnGateway::findOneBy(\BpmnGatewayPeer::GAT_UID, $gatewayUid)->toArray();

            switch ($arrayGatewayData["GAT_TYPE"]) {
                //case "SELECTION":
                case self::BPMN_GATEWAY_COMPLEX:
                    $routeType = "SELECT";
                    break;
                //case "EVALUATION":
                case self::BPMN_GATEWAY_EXCLUSIVE:
                    $routeType = "EVALUATE";
                    break;
                //case "PARALLEL":
                case self::BPMN_GATEWAY_PARALLEL:
                    if ($arrayGatewayData["GAT_DIRECTION"] == "DIVERGING") {
                        $routeType = "PARALLEL";
                    } else {
                        if ($arrayGatewayData["GAT_DIRECTION"] == "CONVERGING") {
                            $routeType = "SEC-JOIN";
                        } else {
                            throw new \LogicException(
                                "Invalid Gateway direction, accepted values: [DIVERGING|CONVERGING], given: " . $arrayGatewayData["GAT_DIRECTION"]
                            );
                        }
                    }
                    break;
                //case "PARALLEL_EVALUATION":
                case self::BPMN_GATEWAY_INCLUSIVE:
                    if ($arrayGatewayData["GAT_DIRECTION"] == "DIVERGING") {
                        $routeType = "PARALLEL-BY-EVALUATION";
                    } else {
                        if ($arrayGatewayData["GAT_DIRECTION"] == "CONVERGING") {
                            $routeType = "SEC-JOIN";
                        } else {
                            throw new \LogicException(
                                "Invalid Gateway direction, accepted values: [DIVERGING|CONVERGING], given: " . $arrayGatewayData["GAT_DIRECTION"]
                            );
                        }
                    }
                    break;
                //case "TO_DO":
                case self::BPMN_GATEWAY_EVENTBASED:
                    $routeType = "EVALUATE";
                    break;
                //default
                default:
                    throw new \LogicException("Unsupported Gateway type: " . $arrayGatewayData["GAT_TYPE"]);
                    break;
            }

            //Flows
            $arrayFlow = \BpmnFlow::findAllBy(array(
                \BpmnFlowPeer::FLO_TYPE                => array("MESSAGE", \Criteria::NOT_EQUAL),
                \BpmnFlowPeer::FLO_ELEMENT_ORIGIN      => $gatewayUid,
                \BpmnFlowPeer::FLO_ELEMENT_ORIGIN_TYPE => "bpmnGateway"
            ));

            //if ($arrayFlow > 0) {
            //    $this->wp->resetTaskRoutes($activityUid);
            //}

            foreach ($arrayFlow as $value) {
                $arrayFlowData = $value->toArray();

                $routeDefault   = (array_key_exists("FLO_TYPE", $arrayFlowData) && $arrayFlowData["FLO_TYPE"] == "DEFAULT")? 1 : 0;
                $routeCondition = (array_key_exists("FLO_CONDITION", $arrayFlowData))? $arrayFlowData["FLO_CONDITION"] : "";

                switch ($arrayFlowData["FLO_ELEMENT_DEST_TYPE"]) {
                    case "bpmnActivity":
                        //Gateway ----> Activity
                        $result = $this->wp->addRoute($activityUid, $arrayFlowData["FLO_ELEMENT_DEST"], $routeType, $routeCondition, $routeDefault);
                        break;
                    case "bpmnGateway":
                        //Gateway ----> Gateway
                        $taskUid = $this->createTaskByElement(
                            $gatewayUid,
                            "bpmnGateway",
                            "gateway-to-gateway"
                        );

                        $result = $this->wp->addRoute($activityUid, $taskUid, $routeType, $routeCondition, $routeDefault);

                        $this->mapBpmnGatewayToWorkflowRoutes($taskUid, $arrayFlowData["FLO_ELEMENT_DEST"]);
                        break;
                    case "bpmnEvent":
                        //Gateway ----> Event
                        $event = \BpmnEventPeer::retrieveByPK($arrayFlowData["FLO_ELEMENT_DEST"]);

                        if (!is_null($event)) {
                            switch ($event->getEvnType()) {
                                case "START":
                                    throw new \LogicException("Incorrect design" . PHP_EOL . "Given: bpmnGateway -> " . $arrayFlowData["FLO_ELEMENT_DEST_TYPE"]);
                                    break;
                                case "END":
                                    //$event->getEvnMarker(): EMPTY or MESSAGETHROW
                                    switch ($event->getEvnMarker()) {
                                        case "MESSAGETHROW":
                                            $taskUid = $this->createTaskByElement(
                                                $event->getEvnUid(),
                                                "bpmnEvent",
                                                "end-message-event"
                                            );

                                            $result = $this->wp->addRoute($activityUid, $taskUid, $routeType, $routeCondition, $routeDefault);
                                            $result = $this->wp->addRoute($taskUid, -1, "SEQUENTIAL");
                                            break;
                                        case "EMAIL":
                                            $taskUid = $this->createTaskByElement(
                                                $event->getEvnUid(),
                                                "bpmnEvent",
                                                "end-email-event"
                                            );

                                            $result = $this->wp->addRoute($activityUid, $taskUid, $routeType, $routeCondition, $routeDefault);
                                            $result = $this->wp->addRoute($taskUid, -1, "SEQUENTIAL");
                                            break;
                                        default:
                                            //EMPTY //and others types
                                            $result = $this->wp->addRoute($activityUid, -1, $routeType, $routeCondition, $routeDefault);
                                            break;
                                    }
                                    break;
                                default:
                                    //INTERMEDIATE //and others types
                                    $this->mapBpmnEventToWorkflowRoutes($activityUid, $arrayFlowData["FLO_ELEMENT_DEST"], $routeType, $routeCondition, $routeDefault);
                                    break;
                            }
                        }
                        break;
                    default:
                        //For ProcessMaker is only allowed flows between: "gateway -> activity", "gateway -> gateway", "gateway -> event"
                        //any another flow is considered invalid
                        throw new \LogicException(
                            "For ProcessMaker is only allowed flows between: \"gateway -> activity\", \"gateway -> gateway\", \"gateway -> event\"" . PHP_EOL .
                            "Given: bpmnGateway -> " . $arrayFlowData["FLO_ELEMENT_DEST_TYPE"]
                        );
                }
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function mapBpmnEventToWorkflowRoutes($activityUid, $eventUid, $routeType = "SEQUENTIAL", $routeCondition = "", $routeDefault = 0)
    {
        try {
            $arrayEventData = \BpmnEvent::findOneBy(\BpmnEventPeer::EVN_UID, $eventUid)->toArray();

            if (!is_null($arrayEventData)) {
                $arrayEventType   = array("INTERMEDIATE");
                $arrayEventMarker = array("MESSAGECATCH", "TIMER");

                if (in_array($arrayEventData["EVN_TYPE"], $arrayEventType) && in_array($arrayEventData["EVN_MARKER"], $arrayEventMarker)) {
                    $arrayKey = array(
                        "MESSAGECATCH" => "intermediate-catch-message-event",
                        "TIMER"        => "intermediate-catch-timer-event"
                    );

                    $taskUid = $this->createTaskByElement(
                        $eventUid,
                        "bpmnEvent",
                        $arrayKey[$arrayEventData["EVN_MARKER"]]
                    );

                    $result = $this->wp->addRoute($activityUid, $taskUid, $routeType, $routeCondition, $routeDefault);

                    $activityUid = $taskUid;

                    $routeType = "SEQUENTIAL";
                    $routeCondition = "";
                    $routeDefault = 0;
                }

                //Flows
                $arrayFlow = \BpmnFlow::findAllBy(array(
                    \BpmnFlowPeer::FLO_TYPE                => array("MESSAGE", \Criteria::NOT_EQUAL),
                    \BpmnFlowPeer::FLO_ELEMENT_ORIGIN      => $eventUid,
                    \BpmnFlowPeer::FLO_ELEMENT_ORIGIN_TYPE => "bpmnEvent"
                ));

                foreach ($arrayFlow as $value) {
                    $arrayFlowData = $value->toArray();

                    switch ($arrayFlowData["FLO_ELEMENT_DEST_TYPE"]) {
                        case "bpmnActivity":
                            //Event ----> Activity
                            $result = $this->wp->addRoute($activityUid, $arrayFlowData["FLO_ELEMENT_DEST"], $routeType, $routeCondition, $routeDefault);
                            break;
                        case "bpmnGateway":
                            //Event ----> Gateway
                            $this->mapBpmnGatewayToWorkflowRoutes($activityUid, $arrayFlowData["FLO_ELEMENT_DEST"]);
                            break;
                        case "bpmnEvent":
                            //Event ----> Event
                            $event = \BpmnEventPeer::retrieveByPK($arrayFlowData["FLO_ELEMENT_DEST"]);

                            if (!is_null($event)) {
                                switch ($event->getEvnType()) {
                                    case "START":
                                        throw new \LogicException("Incorrect design" . PHP_EOL . "Given: bpmnEvent -> " . $arrayFlowData["FLO_ELEMENT_DEST_TYPE"]);
                                        break;
                                    case "END":
                                        //$event->getEvnMarker(): EMPTY or MESSAGETHROW
                                        switch ($event->getEvnMarker()) {
                                            case "MESSAGETHROW":
                                                $taskUid = $this->createTaskByElement(
                                                    $event->getEvnUid(),
                                                    "bpmnEvent",
                                                    "end-message-event"
                                                );

                                                $result = $this->wp->addRoute($activityUid, $taskUid, $routeType, $routeCondition, $routeDefault);
                                                $result = $this->wp->addRoute($taskUid, -1, "SEQUENTIAL");
                                                break;
                                            case "EMAIL":
                                                $taskUid = $this->createTaskByElement(
                                                    $event->getEvnUid(),
                                                    "bpmnEvent",
                                                    "end-email-event"
                                                );

                                                $result = $this->wp->addRoute($activityUid, $taskUid, $routeType, $routeCondition, $routeDefault);
                                                $result = $this->wp->addRoute($taskUid, -1, "SEQUENTIAL");
                                                break;
                                            default:
                                                //EMPTY //and others types
                                                $result = $this->wp->addRoute($activityUid, -1, $routeType, $routeCondition, $routeDefault);
                                                break;
                                        }
                                        break;
                                    default:
                                        //INTERMEDIATE //and others types
                                        $this->mapBpmnEventToWorkflowRoutes($activityUid, $arrayFlowData["FLO_ELEMENT_DEST"], $routeType, $routeCondition, $routeDefault);
                                        break;
                                }
                            }
                            break;
                        default:
                            //For ProcessMaker is only allowed flows between: "event -> activity", "event -> gateway", "event -> event"
                            //any another flow is considered invalid
                            throw new \LogicException(
                                "For ProcessMaker is only allowed flows between: \"event -> activity\", \"event -> gateway\", \"event -> event\"" . PHP_EOL .
                                "Given: bpmnEvent -> " . $arrayFlowData["FLO_ELEMENT_DEST_TYPE"]
                            );
                    }
                }
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function mapBpmnFlowsToWorkflowRoutes()
    {
        //Task dummies //Delete Task Routes
        foreach ($this->arrayElementTaskRelation as $value) {
            $this->wp->resetTaskRoutes($value);
        }

        //Activities
        foreach ($this->getActivities() as $value) {
            $activity = $value;

            //Delete Task Routes
            $this->wp->resetTaskRoutes($activity["ACT_UID"]);

            //Flows
            $arrayFlow = \BpmnFlow::findAllBy(array(
                \BpmnFlowPeer::FLO_TYPE                => array("MESSAGE", \Criteria::NOT_EQUAL),
                \BpmnFlowPeer::FLO_ELEMENT_ORIGIN      => $activity["ACT_UID"],
                \BpmnFlowPeer::FLO_ELEMENT_ORIGIN_TYPE => "bpmnActivity"
            ));

            foreach ($arrayFlow as $value2) {
                $flow = $value2;

                switch ($flow->getFloElementDestType()) {
                    case "bpmnActivity":
                        //Activity -> Activity
                        $this->wp->addRoute($activity["ACT_UID"], $flow->getFloElementDest(), "SEQUENTIAL");
                        break;
                    case "bpmnGateway":
                        //Activity -> Gateway
                        //We must find the related flows: gateway -> <object>
                        $this->mapBpmnGatewayToWorkflowRoutes($activity["ACT_UID"], $flow->getFloElementDest());
                        break;
                    case "bpmnEvent":
                        //Activity -> Event
                        $event = \BpmnEventPeer::retrieveByPK($flow->getFloElementDest());

                        if (!is_null($event)) {
                            switch ($event->getEvnType()) {
                                case "START":
                                    throw new \LogicException("Incorrect design" . PHP_EOL . "Given: bpmnActivity -> " . $flow->getFloElementDestType());
                                    break;
                                case "END":
                                    //$event->getEvnMarker(): EMPTY or MESSAGETHROW
                                    switch ($event->getEvnMarker()) {
                                        case "MESSAGETHROW":
                                            $taskUid = $this->createTaskByElement(
                                                $event->getEvnUid(),
                                                "bpmnEvent",
                                                "end-message-event"
                                            );

                                            $result = $this->wp->addRoute($activity["ACT_UID"], $taskUid, "SEQUENTIAL");
                                            $result = $this->wp->addRoute($taskUid, -1, "SEQUENTIAL");
                                            break;
                                        case "EMAIL":
                                            $taskUid = $this->createTaskByElement(
                                                $event->getEvnUid(),
                                                "bpmnEvent",
                                                "end-email-event"
                                            );

                                            $result = $this->wp->addRoute($activity["ACT_UID"], $taskUid, "SEQUENTIAL");
                                            $result = $this->wp->addRoute($taskUid, -1, "SEQUENTIAL");
                                            break;
                                        default:
                                            //EMPTY //This it's already implemented
                                            //and others types
                                            $result = $this->wp->addRoute($activity["ACT_UID"], -1, "SEQUENTIAL");
                                            break;
                                    }
                                    break;
                                default:
                                    //INTERMEDIATE //and others types
                                    $this->mapBpmnEventToWorkflowRoutes($activity["ACT_UID"], $flow->getFloElementDest());
                                    break;
                            }
                        }
                        break;
                }
            }
        }

        //Events
        foreach ($this->getEvents() as $value) {
            $event = $value;

            switch ($event["EVN_TYPE"]) {
                case "START":
                    switch ($event["EVN_MARKER"]) {
                        case "MESSAGECATCH":
                            $taskUid = $this->createTaskByElement(
                                $event["EVN_UID"],
                                "bpmnEvent",
                                "start-message-event"
                            );

                            $this->wp->setStartTask($taskUid);

                            $this->mapBpmnEventToWorkflowRoutes($taskUid, $event["EVN_UID"]);
                            break;
                        case "TIMER":
                            $taskUid = $this->createTaskByElement(
                                $event["EVN_UID"],
                                "bpmnEvent",
                                "start-timer-event"
                            );

                            $this->wp->setStartTask($taskUid);

                            $this->mapBpmnEventToWorkflowRoutes($taskUid, $event["EVN_UID"]);
                            break;
                        case "EMPTY":
                            $this->updateEventActivityDefinition(\BpmnEventPeer::retrieveByPK($event["EVN_UID"]), true);
                            break;
                    }
                    break;
                //case "END":
                //    break;
                //case "INTERMEDIATE":
                //    break;
            }
        }
    }

    public function remove($flagForceRemoveProject = false, $flagRemoveCases = true)
    {
        parent::remove($flagForceRemoveProject);
        $this->wp->remove($flagRemoveCases);
    }

    public static function createFromStruct(array $projectData, $generateUid = true)
    {
        $projectData["prj_name"] = trim($projectData["prj_name"]);
        if ($projectData["prj_name"] == '') {
            throw new \Exception("`prj_name` is required but it is empty.");
        }
        if (\Process::existsByProTitle($projectData["prj_name"])) {
            throw new \Exception("Project with name: {$projectData["prj_name"]}, already exists.");
        }
        $activities = isset($projectData['diagrams']['0']['activities'])?$projectData['diagrams']['0']['activities']:array();
        foreach ($activities as $value) {
            if (empty($value['act_type'])) {
                throw new \Exception("For activity: {$value['act_uid']} `act_type` is required but missing.");
            }
        }
        $events = isset($projectData['diagrams']['0']['events'])?$projectData['diagrams']['0']['events']:array();
        foreach ($events as $value) {
            if (empty($value['evn_type'])) {
                throw new \Exception("For event: {$value['evn_uid']} `evn_type` is required but missing.");
            }
            if (empty($value['evn_marker'])) {
                throw new \Exception("For event: {$value['evn_uid']} `evn_marker` is required but missing.");
            }
        }
        $bwp = new self;
        $result = array();
        $data = array();

        if ($generateUid) {
            $result[0]["old_uid"] = isset($projectData["prj_uid"]) ? $projectData["prj_uid"] : "";
            $projectData["prj_uid"] = Util\Common::generateUID();
            $result[0]["new_uid"] = $projectData["prj_uid"];
            $result[0]["object"] = "project";
        }

        $data["PRJ_UID"] = $projectData["prj_uid"];
        $data["PRJ_AUTHOR"] = $projectData["prj_author"];

        $bwp->create($data);

        $diagramData = $processData = array();

        if (array_key_exists("diagrams", $projectData) && is_array($projectData["diagrams"]) && count($projectData["diagrams"]) > 0) {
            $diagramData = array_change_key_case($projectData["diagrams"][0], CASE_UPPER);

            if ($generateUid) {
                $result[1]["old_uid"] = $diagramData["DIA_UID"];
                $diagramData["DIA_UID"] = Util\Common::generateUID();
                $result[1]["new_uid"] = $diagramData["DIA_UID"];
                $result[1]["object"] = "diagram";
            }
        }

        $bwp->addDiagram($diagramData);

        if (array_key_exists("process", $projectData) && is_array($projectData["process"])) {
            $processData = array_change_key_case($projectData["process"], CASE_UPPER);
            if ($generateUid) {
                $result[2]["old_uid"] = $processData["PRO_UID"];
                $processData["PRO_UID"] = Util\Common::generateUID();
                $result[2]["new_uid"] = $processData["PRO_UID"];
                $result[2]["object"] = "process";
            }
        }

        $bwp->addProcess($processData);

        $mappedUid = array_merge($result, self::updateFromStruct($bwp->prjUid, $projectData, $generateUid, true));

        return $generateUid ? $mappedUid : $bwp->getUid();
    }

    /**
     * Compose and return a Project struct
     *
     * Example struct return:
     *  array(
     *    "prj_uid" => "25111170353317e324d6e23073851309",
     *    "prj_name" => "example project",
     *    "prj_description" => "project desc.",
     *    ...
     *    "diagrams" => array(
     *      array(
     *        "dia_uid" => "94208559153317e325f1c24068030751",
     *        "dia_name" => "Example Diagram",
     *        ...
     *        "activities" => array(...),
     *        "events" => array(...),
     *        "gateways" => array(...),
     *        "flows" => array(...),
     *        "artifacts" => array(...),
     *        "laneset" => array(...),
     *        "lanes" => array(...)
     *      )
     *    )
     *  )
     *
     * @param $prjUid
     * @return array
     */
    public static function getStruct($prjUid)
    {
        $bwp = BpmnWorkflow::load($prjUid);

        $project = array_change_key_case($bwp->getProject(), CASE_LOWER);
        $diagram = $bwp->getDiagram();
        $process = $bwp->getProcess();
        $diagram["pro_uid"] = $process["PRO_UID"];

        $configList = array("changeCaseTo" => CASE_LOWER);

        if (! is_null($diagram)) {
            $diagram = array_change_key_case($diagram, CASE_LOWER);
            $diagram["activities"] = $bwp->getActivities($configList);
            $diagram["events"] = $bwp->getEvents($configList);
            $diagram["gateways"] = $bwp->getGateways($configList);
            $diagram["flows"] = $bwp->getFlows($configList);
            $diagram["artifacts"] = $bwp->getArtifacts($configList);
            $diagram["laneset"] = $bwp->getLanesets($configList);
            $diagram["lanes"] = $bwp->getLanes($configList);
            $diagram["data"] = $bwp->getDataCollection($configList);
            $diagram["participants"] = $bwp->getParticipants($configList);
            $project["diagrams"][] = $diagram;
        }
        if (file_exists(PATH_DOCUMENT . $project["prj_uid"])) {
            $project["prj_bpmn_file_upload"] = file_get_contents(PATH_DOCUMENT . $project["prj_uid"]);
            unlink(PATH_DOCUMENT . $project["prj_uid"]);
        }

        return $project;
    }

    public function updateBoundByArrayUid(array $arrayObjectData, array $arrayUid)
    {
        try {
            unset($arrayObjectData["BOU_UID"]);

            if ($arrayObjectData["BOU_CONTAINER"] == "bpmnPool" ||
                $arrayObjectData["BOU_CONTAINER"] == "bpmnLane" ||
                $arrayObjectData["BOU_CONTAINER"] == "bpmnActivity"
            ) {
                foreach ($arrayUid as $value) {
                    if ($arrayObjectData["BOU_ELEMENT"] == $value["old_uid"]) {
                        $arrayObjectData["BOU_ELEMENT"] = $value["new_uid"];
                    }
                }
            }

            //Return
            return $arrayObjectData;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Update project from a struct defined.
     *
     * This function make add, update or delete of elements of a project
     * Actions is based on a diff from project save in Db and the given structure as param, by the following criteria.
     *
     * 1. Elements that are on the struct, but they are not in the Db will be created on Db
     * 2. Elements that are on the struct and they are found in db, will be compared, if they have been modified then will be updated on Db
     * 3. Elements found in Db but they are not present on the struct will be considered deleted, so they will be deleted from Db.
     *
     * Example Struct:
     *  array(
     *    "prj_uid" => "25111170353317e324d6e23073851309",
     *    "prj_name" => "example project",
     *    "prj_description" => "project desc.",
     *    ...
     *    "diagrams" => array(
     *      array(
     *        "dia_uid" => "94208559153317e325f1c24068030751",
     *        "dia_name" => "Example Diagram",
     *        ...
     *        "activities" => array(...),
     *        "events" => array(...),
     *        "gateways" => array(...),
     *        "flows" => array(...),
     *        "artifacts" => array(...),
     *        "laneset" => array(...),
     *        "lanes" => array(...)
     *      )
     *    )
     *  )
     *
     * Notes:
     *   1. All elements keys are in lowercase
     *   2. the "diagrams" element is an array of arrays
     *
     * @param $prjUid
     * @param $projectData
     * @return array
     */
    public static function updateFromStruct($prjUid, $projectData, $generateUid = true, $forceInsert = false)
    {
        $diagram = isset($projectData["diagrams"]) && isset($projectData["diagrams"][0]) ? $projectData["diagrams"][0] : array();
        $diagram["activities"] = isset($diagram["activities"])? $diagram["activities"]: array();
        $diagram["artifacts"]  = isset($diagram["artifacts"])? $diagram["artifacts"]: array();
        $diagram["gateways"] = isset($diagram["gateways"])? $diagram["gateways"]: array();
        $diagram["events"] = isset($diagram["events"])? $diagram["events"]: array();
        $diagram["data"] = isset($diagram["data"])? $diagram["data"]: array();
        $diagram["participants"] = isset($diagram["participants"])? $diagram["participants"]: array();
        $diagram["laneset"] = isset($diagram["laneset"])? $diagram["laneset"]: array();
        $diagram["lanes"] = isset($diagram["lanes"])? $diagram["lanes"]: array();

        $result = array();

        $projectData['prj_uid'] = $prjUid;

        $bwp = BpmnWorkflow::load($prjUid);

        $projectRecord = array_change_key_case($projectData, CASE_UPPER);

        $bwp->update($projectRecord);

        //Array - Set empty
        $bwp->arrayElementTaskRelation = array();

        //Element-Task-Relation - Get all records
        $criteria = new \Criteria("workflow");

        $criteria->addSelectColumn(\ElementTaskRelationPeer::ELEMENT_UID);
        $criteria->addSelectColumn(\ElementTaskRelationPeer::TAS_UID);

        $criteria->add(\ElementTaskRelationPeer::PRJ_UID, $bwp->wp->getUid(), \Criteria::EQUAL);

        $rsCriteria = \ElementTaskRelationPeer::doSelectRS($criteria);
        $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

        while ($rsCriteria->next()) {
            $row = $rsCriteria->getRow();

            //Array - Add element
            $bwp->arrayElementTaskRelation[$row["ELEMENT_UID"]] = $row["TAS_UID"];
        }

        /*
         * Diagram's Laneset Handling
         */
        $whiteList = array();
        foreach ($diagram["laneset"] as $i => $lanesetData) {
            $lanesetData = array_change_key_case($lanesetData, CASE_UPPER);

            $dataObject = $bwp->getLaneset($lanesetData["LNS_UID"]);

            if ($forceInsert || is_null($dataObject)) {
                if ($generateUid) {
                    //Generate and update UID
                    unset($lanesetData["BOU_UID"]);

                    $uidOld = $lanesetData["LNS_UID"];
                    $lanesetData["LNS_UID"] = Util\Common::generateUID();

                    $result[] = array(
                        "object"  => "laneset",
                        "old_uid" => $uidOld,
                        "new_uid" => $lanesetData["LNS_UID"]
                    );
                }
                $bwp->addLaneset($lanesetData);
            } elseif (! $bwp->isEquals($dataObject, $lanesetData)) {
                $bwp->updateLaneset($lanesetData["LNS_UID"], $lanesetData);
            } else {
                Util\Logger::log("Update Laneset ({$lanesetData["LNS_UID"]}) Skipped - No changes required");
            }

            $diagram["laneset"][$i] = $lanesetData;
            $whiteList[] = $lanesetData["LNS_UID"];
        }

        $dataCollection = $bwp->getLanesets();

        // looking for removed elements
        foreach ($dataCollection as $lanesetData) {
            if (! in_array($lanesetData["LNS_UID"], $whiteList)) {
                // If it is not in the white list, then remove them
                $bwp->removeLaneset($lanesetData["LNS_UID"]);
            }
        }

        /*
         * Diagram's Lane Handling
         */
        $whiteList = array();
        foreach ($diagram["lanes"] as $i => $laneData) {
            $laneData = array_change_key_case($laneData, CASE_UPPER);

            //Update UIDs
            foreach ($result as $value) {
                if ($laneData["LNS_UID"] == $value["old_uid"]) {
                    $laneData["LNS_UID"] = $value["new_uid"];
                }
            }

            $dataObject = $bwp->getLane($laneData["LAN_UID"]);

            if ($forceInsert || is_null($dataObject)) {
                if ($generateUid) {
                    //Generate and update UID
                    unset($laneData["BOU_UID"]);

                    $uidOld = $laneData["LAN_UID"];
                    $laneData["LAN_UID"] = Util\Common::generateUID();

                    $result[] = array(
                        "object"  => "lane",
                        "old_uid" => $uidOld,
                        "new_uid" => $laneData["LAN_UID"]
                    );
                }
                $bwp->addLane($laneData);
            } elseif (! $bwp->isEquals($dataObject, $laneData)) {
                $bwp->updateLane($laneData["LAN_UID"], $laneData);
            } else {
                Util\Logger::log("Update Lane ({$laneData["LAN_UID"]}) Skipped - No changes required");
            }

            $diagram["lanes"][$i] = $laneData;
            $whiteList[] = $laneData["LAN_UID"];
        }

        $dataCollection = $bwp->getLanes();

        // looking for removed elements
        foreach ($dataCollection as $laneData) {
            if (! in_array($laneData["LAN_UID"], $whiteList)) {
                // If it is not in the white list, then remove them
                $bwp->removeLane($laneData["LAN_UID"]);
            }
        }

        /*
         * Diagram's Activities Handling
         */
        $whiteList = array();
        foreach ($diagram["activities"] as $i => $activityData) {
            $activityData = array_change_key_case($activityData, CASE_UPPER);
            unset($activityData["_EXTENDED"], $activityData["BOU_ELEMENT_ID"]);
            $activityData = Util\ArrayUtil::boolToIntValues($activityData);

            $activity = $bwp->getActivity($activityData["ACT_UID"]);

            if ($forceInsert || is_null($activity)) {
                if ($generateUid) {
                    //Generate and update UID
                    $activityData = $bwp->updateBoundByArrayUid($activityData, $result);

                    $uidOld = $activityData["ACT_UID"];
                    $activityData["ACT_UID"] = Util\Common::generateUID();

                    $result[] = array(
                        "object"  => "activity",
                        "old_uid" => $uidOld,
                        "new_uid" => $activityData["ACT_UID"]
                    );
                }

                $bwp->addActivity($activityData);
            } elseif (! $bwp->isEquals($activity, $activityData)) {
                $bwp->updateActivity($activityData["ACT_UID"], $activityData);
            } else {
                Util\Logger::log("Update Activity ({$activityData["ACT_UID"]}) Skipped - No changes required");
            }

            $diagram["activities"][$i] = $activityData;
            $whiteList[] = $activityData["ACT_UID"];

        }

        $activities = $bwp->getActivities();

        // looking for removed elements
        foreach ($activities as $activityData) {
            if (! in_array($activityData["ACT_UID"], $whiteList)) {
                $bwp->removeActivity($activityData["ACT_UID"]);
            }
        }

        /*
         * Diagram's Artifacts Handling
         */
        $whiteList = array();
        foreach ($diagram["artifacts"] as $i => $artifactData) {
            $artifactData = array_change_key_case($artifactData, CASE_UPPER);
            unset($artifactData["_EXTENDED"]);

            $artifact = $bwp->getArtifact($artifactData["ART_UID"]);

            if ($forceInsert || is_null($artifact)) {
                if ($generateUid) {
                    //Generate and update UID
                    $artifactData = $bwp->updateBoundByArrayUid($artifactData, $result);

                    $uidOld = $artifactData["ART_UID"];
                    $artifactData["ART_UID"] = Util\Common::generateUID();

                    $result[] = array(
                        "object"  => "artifact",
                        "old_uid" => $uidOld,
                        "new_uid" => $artifactData["ART_UID"]
                    );
                }

                $bwp->addArtifact($artifactData);
            } elseif (! $bwp->isEquals($artifact, $artifactData)) {
                $bwp->updateArtifact($artifactData["ART_UID"], $artifactData);
            } else {
                Util\Logger::log("Update Artifact ({$artifactData["ART_UID"]}) Skipped - No changes required");
            }

            $diagram["artifacts"][$i] = $artifactData;
            $whiteList[] = $artifactData["ART_UID"];
        }

        $artifacts = $bwp->getArtifacts();
        // looking for removed elements
        foreach ($artifacts as $artifactData) {
            if (! in_array($artifactData["ART_UID"], $whiteList)) {
                $bwp->removeArtifact($artifactData["ART_UID"]);
            }
        }

        /*
         * Diagram's Gateways Handling
         */
        $arrayGatewayUid = array();
        $arrayGatewayParallelUid = array();
        $arrayGatewayUidToCheckConverging = array(); //PARALLEL, INCLUSIVE

        $whiteList = array();

        foreach ($diagram["gateways"] as $i => $gatewayData) {
            $gatewayData = array_change_key_case($gatewayData, CASE_UPPER);
            unset($gatewayData["_EXTENDED"]);

            $flagAddOrUpdate = false;

            $gateway = $bwp->getGateway($gatewayData["GAT_UID"]);

            if ($forceInsert || is_null($gateway)) {
                if ($generateUid) {
                    //Generate and update UID
                    $gatewayData = $bwp->updateBoundByArrayUid($gatewayData, $result);

                    $uidOld = $gatewayData["GAT_UID"];
                    $gatewayData["GAT_UID"] = Util\Common::generateUID();

                    $result[] = array(
                        "object"  => "gateway",
                        "old_uid" => $uidOld,
                        "new_uid" => $gatewayData["GAT_UID"]
                    );
                }

                $bwp->addGateway($gatewayData);

                $flagAddOrUpdate = true;
            } elseif (! $bwp->isEquals($gateway, $gatewayData)) {
                $bwp->updateGateway($gatewayData["GAT_UID"], $gatewayData);

                $flagAddOrUpdate = true;
            } else {
                Util\Logger::log("Update Gateway ({$gatewayData["GAT_UID"]}) Skipped - No changes required");
            }

            if ($flagAddOrUpdate) {
                $arrayGatewayData = $bwp->getGateway($gatewayData["GAT_UID"]);

                switch ($arrayGatewayData["GAT_TYPE"]) {
                    case self::BPMN_GATEWAY_PARALLEL:
                        $arrayGatewayParallelUid[] = $gatewayData["GAT_UID"];
                        $arrayGatewayUidToCheckConverging[] = $gatewayData["GAT_UID"];
                        break;
                    case self::BPMN_GATEWAY_INCLUSIVE:
                        $arrayGatewayUidToCheckConverging[] = $gatewayData["GAT_UID"];
                        break;
                }
            }

            $arrayGatewayUid[$gatewayData["GAT_UID"]] = 1;

            $diagram["gateways"][$i] = $gatewayData;
            $whiteList[] = $gatewayData["GAT_UID"];
        }

        $gateways = $bwp->getGateways();

        // looking for removed elements
        foreach ($gateways as $gatewayData) {
            if (! in_array($gatewayData["GAT_UID"], $whiteList)) {
                $bwp->removeGateway($gatewayData["GAT_UID"]);
            }
        }

        /*
         * Diagram's Events Handling
         */
        $whiteList = array();
        foreach ($diagram["events"] as $i => $eventData) {
            $eventData = array_change_key_case($eventData, CASE_UPPER);

            unset($eventData["_EXTENDED"]);

            if (array_key_exists("EVN_CANCEL_ACTIVITY", $eventData)) {
                $eventData["EVN_CANCEL_ACTIVITY"] = $eventData["EVN_CANCEL_ACTIVITY"] ? 1 : 0;
            }

            if (array_key_exists("EVN_WAIT_FOR_COMPLETION", $eventData)) {
                $eventData["EVN_WAIT_FOR_COMPLETION"] = $eventData["EVN_WAIT_FOR_COMPLETION"] ? 1 : 0;
            }

            $event = $bwp->getEvent($eventData["EVN_UID"]);

            if ($forceInsert || is_null($event)) {
                if ($generateUid) {
                    //Generate and update UID
                    $eventData = $bwp->updateBoundByArrayUid($eventData, $result);

                    $uidOld = $eventData["EVN_UID"];
                    $eventData["EVN_UID"] = Util\Common::generateUID();

                    $result[] = array(
                        "object"  => "event",
                        "old_uid" => $uidOld,
                        "new_uid" => $eventData["EVN_UID"]
                    );
                }

                $bwp->addEvent($eventData);
            } elseif (! $bwp->isEquals($event, $eventData)) {
                $bwp->updateEvent($eventData["EVN_UID"], $eventData);
            } else {
                Util\Logger::log("Update Event ({$eventData["EVN_UID"]}) Skipped - No changes required");
            }

            $diagram["events"][$i] = $eventData;
            $whiteList[] = $eventData["EVN_UID"];
        }

        $events = $bwp->getEvents();

        // looking for removed elements
        foreach ($events as $eventData) {
            if (! in_array($eventData["EVN_UID"], $whiteList)) {
                // If it is not in the white list, then remove them
                $bwp->removeEvent($eventData["EVN_UID"]);
            }
        }

        /*
         * Diagram's Data Handling
         */
        $whiteList = array();
        foreach ($diagram["data"] as $i => $dataObjectData) {
            $dataObjectData = array_change_key_case($dataObjectData, CASE_UPPER);
            unset($dataObjectData["_EXTENDED"]);

            $dataObject = $bwp->getData($dataObjectData["DAT_UID"]);

            if ($forceInsert || is_null($dataObject)) {
                if ($generateUid) {
                    //Generate and update UID
                    $dataObjectData = $bwp->updateBoundByArrayUid($dataObjectData, $result);

                    $uidOld = $dataObjectData["DAT_UID"];
                    $dataObjectData["DAT_UID"] = Util\Common::generateUID();

                    $result[] = array(
                        "object"  => "data",
                        "old_uid" => $uidOld,
                        "new_uid" => $dataObjectData["DAT_UID"]
                    );
                }

                $bwp->addData($dataObjectData);
            } elseif (! $bwp->isEquals($dataObject, $dataObjectData)) {
                $bwp->updateData($dataObjectData["DAT_UID"], $dataObjectData);
            } else {
                Util\Logger::log("Update Data ({$dataObjectData["DAT_UID"]}) Skipped - No changes required");
            }

            $diagram["data"][$i] = $dataObjectData;
            $whiteList[] = $dataObjectData["DAT_UID"];
        }

        $dataCollection = $bwp->getDataCollection();

        // looking for removed elements
        foreach ($dataCollection as $dataObjectData) {
            if (! in_array($dataObjectData["DAT_UID"], $whiteList)) {
                // If it is not in the white list, then remove them
                $bwp->removeData($dataObjectData["DAT_UID"]);
            }
        }

        /*
         * Diagram's Participant Handling
         */
        $whiteList = array();
        foreach ($diagram["participants"] as $i => $participantData) {
            $participantData = array_change_key_case($participantData, CASE_UPPER);
            unset($participantData["_EXTENDED"]);

            $participant = $bwp->getParticipant($participantData["PAR_UID"]);

            if ($forceInsert || is_null($participant)) {
                if ($generateUid) {
                    //Generate and update UID
                    $participantData = $bwp->updateBoundByArrayUid($participantData, $result);

                    $uidOld = $participantData["PAR_UID"];
                    $participantData["PAR_UID"] = Util\Common::generateUID();

                    $result[] = array(
                        "object"  => "participant",
                        "old_uid" => $uidOld,
                        "new_uid" => $participantData["PAR_UID"]
                    );
                }

                $bwp->addParticipant($participantData);
            } elseif (! $bwp->isEquals($participant, $participantData)) {
                $bwp->updateParticipant($participantData["PAR_UID"], $participantData);
            } else {
                Util\Logger::log("Update Participant ({$participantData["PAR_UID"]}) Skipped - No changes required");
            }

            $diagram["participants"][$i] = $participantData;
            $whiteList[] = $participantData["PAR_UID"];
        }

        $dataCollection = $bwp->getParticipants();

        // looking for removed elements
        foreach ($dataCollection as $participantData) {
            if (! in_array($participantData["PAR_UID"], $whiteList)) {
                // If it is not in the white list, then remove them
                $bwp->removeParticipant($participantData["PAR_UID"]);
            }
        }

        /*
         * Diagram's Flows Handling
         */
        $arrayGatewayGatDefaultFlow = array();

        $whiteList = array();

        $diagramFlows = isset($diagram["flows"])?$diagram["flows"]:array();
        foreach ($diagramFlows as $i => $flowData) {
            $flowData = array_change_key_case($flowData, CASE_UPPER);

            // if it is a new flow record
            if ($forceInsert || ($generateUid && !\BpmnFlow::exists($flowData["FLO_UID"]))) {
                $uidOld = $flowData["FLO_UID"];
                $flowData["FLO_UID"] = Util\Common::generateUID();

                $result[] = array(
                    "object" => "flow",
                    "old_uid" => $uidOld,
                    "new_uid" => $flowData["FLO_UID"]
                );

                $mappedUid = self::mapUid($flowData["FLO_ELEMENT_ORIGIN"], $result);

                if ($mappedUid !== false) {
                    $flowData["FLO_ELEMENT_ORIGIN"] = $mappedUid;
                }

                $mappedUid = self::mapUid($flowData["FLO_ELEMENT_DEST"], $result);

                if ($mappedUid !== false) {
                    $flowData["FLO_ELEMENT_DEST"] = $mappedUid;
                }
            }

            //Update UIDs
            foreach ($result as $value) {
                if ($flowData["FLO_ELEMENT_ORIGIN"] == $value["old_uid"]) {
                    $flowData["FLO_ELEMENT_ORIGIN"] = $value["new_uid"];
                }

                if ($flowData["FLO_ELEMENT_DEST"] == $value["old_uid"]) {
                    $flowData["FLO_ELEMENT_DEST"] = $value["new_uid"];
                }
            }

            //Update condition
            if ($flowData["FLO_ELEMENT_ORIGIN_TYPE"] == "bpmnGateway" && in_array($flowData["FLO_ELEMENT_ORIGIN"], $arrayGatewayParallelUid)) {
                $flowData["FLO_CONDITION"] = "";
            }

            //Add element to array Gateway default flow
            if ($flowData["FLO_TYPE"] == "DEFAULT" && isset($arrayGatewayUid[$flowData["FLO_ELEMENT_ORIGIN"]])) {
                $arrayGatewayGatDefaultFlow[$flowData["FLO_ELEMENT_ORIGIN"]] = $flowData["FLO_UID"];
            }

            $diagram["flows"][$i] = $flowData;
            $whiteList[] = $flowData["FLO_UID"];
        }

        $diagramFlows = isset($diagram["flows"])?$diagram["flows"]:array();
        foreach ($diagramFlows as $flowData) {
            $flow = $bwp->getFlow($flowData["FLO_UID"]);
            if ($forceInsert || is_null($flow)) {
                $bwp->addFlow($flowData);
            } elseif (! $bwp->isEquals($flow, $flowData)) {
                $bwp->updateFlow($flowData["FLO_UID"], $flowData, $diagram["flows"]);
            } else {
                Util\Logger::log("Update Flow ({$flowData["FLO_UID"]}) Skipped - No changes required");
            }
        }

        $flows = $bwp->getFlows();

        // looking for removed elements
        foreach ($flows as $flowData) {
            if (! in_array($flowData["FLO_UID"], $whiteList)) {
                $bwp->removeFlow($flowData["FLO_UID"]);
            }
        }

        //Update BPMN_GATEWAY.GAT_DIRECTION
        foreach ($arrayGatewayUidToCheckConverging as $value) {
            $arrayGatewayData = $bwp->getGateway($value);

            if (!is_null($arrayGatewayData)) {
                $arrayFlow = \BpmnFlow::findAllBy(array(
                    \BpmnFlowPeer::FLO_TYPE              => array("MESSAGE", \Criteria::NOT_EQUAL),
                    \BpmnFlowPeer::FLO_ELEMENT_DEST      => $arrayGatewayData["GAT_UID"],
                    \BpmnFlowPeer::FLO_ELEMENT_DEST_TYPE => "bpmnGateway"
                ));

                if (count($arrayFlow) > 1) {
                    $bwp->updateGateway($arrayGatewayData["GAT_UID"], array("GAT_DIRECTION" => "CONVERGING"));
                }
            }
        }

        //Update BPMN_GATEWAY.GAT_DEFAULT_FLOW
        foreach ($arrayGatewayGatDefaultFlow as $key => $value) {
            $bwp->updateGateway($key, array("GAT_DEFAULT_FLOW" => $value));
        }

        //Map Bpmn-Flows to Workflow-Routes
        $bwp->mapBpmnFlowsToWorkflowRoutes();

        //Return
        return $result;
    }

    protected static function mapUid($oldUid, $list)
    {
        foreach ($list as $item) {
            if ($item["old_uid"] == $oldUid) {
                return $item["new_uid"];
            }
        }

        return false;
    }

    public function setDisabled($value = true)
    {
        parent::setDisabled($value);
        $this->wp->setDisabled($value);
    }

    public function updateWebEntryEventByEvent($eventUid, array $arrayData)
    {
        try {
            $bpmnEvent = \BpmnEventPeer::retrieveByPK($eventUid);

            if (!is_null($bpmnEvent) && $bpmnEvent->getEvnType() == "START" && $bpmnEvent->getEvnMarker() == "EMPTY") {
                $webEntryEvent = new \ProcessMaker\BusinessModel\WebEntryEvent();

                if ($webEntryEvent->existsEvent($bpmnEvent->getPrjUid(), $bpmnEvent->getEvnUid())) {
                    $arrayWebEntryEventData = $webEntryEvent->getWebEntryEventByEvent($bpmnEvent->getPrjUid(), $bpmnEvent->getEvnUid(), true);

                    $bpmn = \ProcessMaker\Project\Bpmn::load($bpmnEvent->getPrjUid());
                    $bpmnProject = $bpmn->getProject("object");

                    $arrayResult = $webEntryEvent->update($arrayWebEntryEventData["WEE_UID"], $bpmnProject->getPrjAuthor(), $arrayData);
                }
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function createMessageEventRelationByBpmnFlow(\BpmnFlow $bpmnFlow)
    {
        try {
            $messageEventRelation = new \ProcessMaker\BusinessModel\MessageEventRelation();

            $messageEventRelationUid = "";

            if ($bpmnFlow->getFloType() == "MESSAGE" &&
                $bpmnFlow->getFloElementOriginType() == "bpmnEvent" && $bpmnFlow->getFloElementDestType() == "bpmnEvent" &&
                !$messageEventRelation->existsEventRelation($bpmnFlow->getPrjUid(), $bpmnFlow->getFloElementOrigin(), $bpmnFlow->getFloElementDest())
            ) {
                $arrayResult = $messageEventRelation->create(
                    $bpmnFlow->getPrjUid(),
                    array(
                        "EVN_UID_THROW" => $bpmnFlow->getFloElementOrigin(),
                        "EVN_UID_CATCH" => $bpmnFlow->getFloElementDest()
                    )
                );

                $messageEventRelationUid = $arrayResult["MSGER_UID"];
            }

            //Return
            return $messageEventRelationUid;
        } catch (\Exception $e) {
            throw $e;
        }
    }
}

