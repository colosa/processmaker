<?php
namespace ProcessMaker\BusinessModel;

class MessageApplication
{
    private $arrayFieldNameForException = array(
        "start" => "START",
        "limit" => "LIMIT"
    );

    private $frontEnd = false;

    /**
     * Verify if exists the Message-Application
     *
     * @param string $messageApplicationUid Unique id of Message-Application
     *
     * return bool Return true if exists the Message-Application, false otherwise
     */
    public function exists($messageApplicationUid)
    {
        try {
            $obj = \MessageApplicationPeer::retrieveByPK($messageApplicationUid);

            return (!is_null($obj))? true : false;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Create Message-Application for the Case
     *
     * @param string $applicationUid       Unique id of Case
     * @param string $projectUid           Unique id of Project
     * @param string $eventUidThrow        Unique id of Event (throw)
     * @param array  $arrayApplicationData Case data
     *
     * return bool Return true if been created, false otherwise
     */
    public function create($applicationUid, $projectUid, $eventUidThrow, array $arrayApplicationData)
    {
        try {
            $flagCreate = true;

            //Set data
            //Message-Event-Relation - Get unique id of Event (catch)
            $messageEventRelation = new \ProcessMaker\BusinessModel\MessageEventRelation();

            $arrayMessageEventRelationData = $messageEventRelation->getMessageEventRelationWhere(
                array(
                    \MessageEventRelationPeer::PRJ_UID       => $projectUid,
                    \MessageEventRelationPeer::EVN_UID_THROW => $eventUidThrow
                ),
                true
            );

            if (!is_null($arrayMessageEventRelationData)) {
                $eventUidCatch = $arrayMessageEventRelationData["EVN_UID_CATCH"];
            } else {
                $flagCreate = false;
            }

            //Message-Application - Get data ($eventUidThrow)
            $messageEventDefinition = new \ProcessMaker\BusinessModel\MessageEventDefinition();

            if ($messageEventDefinition->existsEvent($projectUid, $eventUidThrow)) {
                $arrayMessageEventDefinitionData = $messageEventDefinition->getMessageEventDefinitionByEvent($projectUid, $eventUidThrow, true);

                $arrayMessageApplicationVariables = $arrayMessageEventDefinitionData["MSGED_VARIABLES"];
                $messageApplicationCorrelation = \G::replaceDataField($arrayMessageEventDefinitionData["MSGED_CORRELATION"], $arrayApplicationData["APP_DATA"]);

                foreach ($arrayMessageApplicationVariables as $key => $value) {
                    $arrayMessageApplicationVariables[$key] = \G::replaceDataField($value, $arrayApplicationData["APP_DATA"]);
                }
            } else {
                $flagCreate = false;
            }

            if (!$flagCreate) {
                //Return
                return false;
            }

            //Create
            $cnn = \Propel::getConnection("workflow");

            try {
                $messageApplication = new \MessageApplication();

                $messageApplicationUid = \ProcessMaker\Util\Common::generateUID();

                $messageApplication->setMsgappUid($messageApplicationUid);
                $messageApplication->setAppUid($applicationUid);
                $messageApplication->setPrjUid($projectUid);
                $messageApplication->setEvnUidThrow($eventUidThrow);
                $messageApplication->setEvnUidCatch($eventUidCatch);
                $messageApplication->setMsgappVariables(serialize($arrayMessageApplicationVariables));
                $messageApplication->setMsgappCorrelation($messageApplicationCorrelation);
                $messageApplication->setMsgappThrowDate("now");

                if ($messageApplication->validate()) {
                    $cnn->begin();

                    $result = $messageApplication->save();

                    $cnn->commit();

                    //Return
                    return true;
                } else {
                    $msg = "";

                    foreach ($messageApplication->getValidationFailures() as $validationFailure) {
                        $msg = $msg . (($msg != "")? "\n" : "") . $validationFailure->getMessage();
                    }

                    throw new \Exception(\G::LoadTranslation("ID_RECORD_CANNOT_BE_CREATED") . (($msg != "")? "\n" . $msg : ""));
                }
            } catch (\Exception $e) {
                $cnn->rollback();

                throw $e;
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Update Message-Application
     *
     * @param string $messageApplicationUid Unique id of Message-Application
     * @param array  $arrayData             Data
     *
     * return bool Return true if been updated, false otherwise
     */
    public function update($messageApplicationUid, array $arrayData)
    {
        try {
            //Verify data
            if (!$this->exists($messageApplicationUid)) {
                //Return
                return false;
            }

            //Update
            $cnn = \Propel::getConnection("workflow");

            try {
                $messageApplication = \MessageApplicationPeer::retrieveByPK($messageApplicationUid);

                $messageApplication->fromArray($arrayData, \BasePeer::TYPE_FIELDNAME);

                $messageApplication->setMsgappCatchDate("now");

                if ($messageApplication->validate()) {
                    $cnn->begin();

                    $result = $messageApplication->save();

                    $cnn->commit();

                    //Return
                    return true;
                } else {
                    $msg = "";

                    foreach ($messageApplication->getValidationFailures() as $validationFailure) {
                        $msg = $msg . (($msg != "")? "\n" : "") . $validationFailure->getMessage();
                    }

                    throw new \Exception(\G::LoadTranslation("ID_REGISTRY_CANNOT_BE_UPDATED") . (($msg != "")? "\n" . $msg : ""));
                }
            } catch (\Exception $e) {
                $cnn->rollback();

                throw $e;
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get criteria for Message-Application
     *
     * return object
     */
    public function getMessageApplicationCriteria()
    {
        try {
            $criteria = new \Criteria("workflow");

            $criteria->addSelectColumn(\MessageApplicationPeer::MSGAPP_UID);
            $criteria->addSelectColumn(\MessageApplicationPeer::APP_UID);
            $criteria->addSelectColumn(\MessageApplicationPeer::PRJ_UID);
            $criteria->addSelectColumn(\MessageApplicationPeer::EVN_UID_THROW);
            $criteria->addSelectColumn(\MessageApplicationPeer::EVN_UID_CATCH);
            $criteria->addSelectColumn(\MessageApplicationPeer::MSGAPP_VARIABLES);
            $criteria->addSelectColumn(\MessageApplicationPeer::MSGAPP_CORRELATION);
            $criteria->addSelectColumn(\MessageApplicationPeer::MSGAPP_THROW_DATE);
            $criteria->addSelectColumn(\MessageApplicationPeer::MSGAPP_CATCH_DATE);
            $criteria->addSelectColumn(\MessageApplicationPeer::MSGAPP_STATUS);

            return $criteria;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Merge and get variables
     *
     * @param array $arrayVariableName  Variables
     * @param array $arrayVariableValue Values
     *
     * return array Return an array
     */
    public function mergeVariables(array $arrayVariableName, array $arrayVariableValue)
    {
        try {
            $arrayVariable = array();

            foreach ($arrayVariableName as $key => $value) {
                if (preg_match("/^@[@%#\?\x24\=]([A-Za-z_]\w*)$/", $value, $arrayMatch) && isset($arrayVariableValue[$key])) {
                    $arrayVariable[$arrayMatch[1]] = $arrayVariableValue[$key];
                }
            }

            //Return
            return $arrayVariable;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get all Message-Applications
     *
     * @param array  $arrayFilterData Data of the filters
     * @param string $sortField       Field name to sort
     * @param string $sortDir         Direction of sorting (ASC, DESC)
     * @param int    $start           Start
     * @param int    $limit           Limit
     *
     * return array Return an array with all Message-Applications
     */
    public function getMessageApplications($arrayFilterData = null, $sortField = null, $sortDir = null, $start = null, $limit = null)
    {
        try {
            $arrayMessageApplication = array();

            //Verify data
            $process = new \ProcessMaker\BusinessModel\Process();

            $process->throwExceptionIfDataNotMetPagerVarDefinition(array("start" => $start, "limit" => $limit), $this->arrayFieldNameForException);

            //Get data
            if (!is_null($limit) && $limit . "" == "0") {
                return $arrayMessageApplication;
            }

            //SQL
            $criteria = $this->getMessageApplicationCriteria();

            $criteria->addSelectColumn(\BpmnEventPeer::EVN_UID);
            $criteria->addSelectColumn(\BpmnEventPeer::EVN_TYPE);
            $criteria->addSelectColumn(\BpmnEventPeer::EVN_MARKER);
            $criteria->addSelectColumn(\BpmnEventPeer::EVN_NAME);
            $criteria->addSelectColumn(\MessageEventDefinitionPeer::MSGED_USR_UID);
            $criteria->addSelectColumn(\MessageEventDefinitionPeer::MSGED_VARIABLES);
            $criteria->addSelectColumn(\MessageEventDefinitionPeer::MSGED_CORRELATION);
            $criteria->addSelectColumn(\ElementTaskRelationPeer::TAS_UID);

            $arrayEventType   = array("START", "INTERMEDIATE");
            $arrayEventMarker = array("MESSAGECATCH");

            $criteria->addJoin(\MessageApplicationPeer::EVN_UID_CATCH, \BpmnEventPeer::EVN_UID, \Criteria::INNER_JOIN);
            $criteria->add(\BpmnEventPeer::EVN_TYPE, $arrayEventType, \Criteria::IN);
            $criteria->add(\BpmnEventPeer::EVN_MARKER, $arrayEventMarker, \Criteria::IN);

            $criteria->addJoin(\MessageApplicationPeer::EVN_UID_CATCH, \MessageEventDefinitionPeer::EVN_UID, \Criteria::INNER_JOIN);

            $criteria->addJoin(\MessageApplicationPeer::EVN_UID_CATCH, \ElementTaskRelationPeer::ELEMENT_UID, \Criteria::INNER_JOIN);
            $criteria->add(\ElementTaskRelationPeer::ELEMENT_TYPE, "bpmnEvent", \Criteria::EQUAL);

            if (!is_null($arrayFilterData) && is_array($arrayFilterData) && isset($arrayFilterData["messageApplicationStatus"]) && trim($arrayFilterData["messageApplicationStatus"]) != "") {
                $criteria->add(\MessageApplicationPeer::MSGAPP_STATUS, $arrayFilterData["messageApplicationStatus"], \Criteria::EQUAL);
            }

            //Number records total
            $criteriaCount = clone $criteria;

            $criteriaCount->clearSelectColumns();
            $criteriaCount->addSelectColumn("COUNT(" . \MessageApplicationPeer::MSGAPP_UID . ") AS NUM_REC");

            $rsCriteriaCount = \MessageApplicationPeer::doSelectRS($criteriaCount);
            $rsCriteriaCount->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

            $rsCriteriaCount->next();
            $row = $rsCriteriaCount->getRow();

            $numRecTotal = $row["NUM_REC"];

            //SQL
            if (!is_null($sortField) && trim($sortField) != "") {
                $sortField = strtoupper($sortField);

                if (in_array($sortField, array("MSGAPP_THROW_DATE", "MSGAPP_CATCH_DATE", "MSGAPP_STATUS"))) {
                    $sortField = \MessageApplicationPeer::TABLE_NAME . "." . $sortField;
                } else {
                    $sortField = \MessageApplicationPeer::MSGAPP_THROW_DATE;
                }
            } else {
                $sortField = \MessageApplicationPeer::MSGAPP_THROW_DATE;
            }

            if (!is_null($sortDir) && trim($sortDir) != "" && strtoupper($sortDir) == "DESC") {
                $criteria->addDescendingOrderByColumn($sortField);
            } else {
                $criteria->addAscendingOrderByColumn($sortField);
            }

            if (!is_null($start)) {
                $criteria->setOffset((int)($start));
            }

            if (!is_null($limit)) {
                $criteria->setLimit((int)($limit));
            }

            $rsCriteria = \MessageApplicationPeer::doSelectRS($criteria);
            $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

            while ($rsCriteria->next()) {
                $row = $rsCriteria->getRow();

                $row["MSGAPP_VARIABLES"] = unserialize($row["MSGAPP_VARIABLES"]);
                $row["MSGED_VARIABLES"]  = unserialize($row["MSGED_VARIABLES"]);

                $arrayMessageApplication[] = $row;
            }

            //Return
            return array(
                "total"  => $numRecTotal,
                "start"  => (int)((!is_null($start))? $start : 0),
                "limit"  => (int)((!is_null($limit))? $limit : 0),
                "filter" => (!is_null($arrayFilterData) && is_array($arrayFilterData) && isset($arrayFilterData["messageApplicationStatus"]))? $arrayFilterData["messageApplicationStatus"] : "",
                "data"   => $arrayMessageApplication
            );
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Catch Message-Events for the Cases
     *
     * @param bool $frontEnd Flag to represent progress bar
     *
     * @throws \Exception
     * @return void
     */
    public function catchMessageEvent($frontEnd = false)
    {
        try {

            //Set variables
            $ws = new \WsBase();
            $case = new \Cases();
            $common = new \ProcessMaker\Util\Common();
            $sysSys = (defined("SYS_SYS"))? SYS_SYS : "Undefined";

            $common->setFrontEnd($frontEnd);

            //Get data
            $totalMessageEvent = 0;

            $counterStartMessageEvent = 0;
            $counterIntermediateCatchMessageEvent = 0;
            $counter = 0;

            $start = 0;
            $limit = 1000;

            $flagFirstTime = false;

            $common->frontEndShow("START");

            do {
                $flagNextRecords = false;

                $arrayMessageApplicationUnread = $this->getMessageApplications(array("messageApplicationStatus" => "UNREAD"), null, null, $start, $limit);

                if (!$flagFirstTime) {
                    $totalMessageEvent = $arrayMessageApplicationUnread["total"];

                    $flagFirstTime = true;
                }

                foreach ($arrayMessageApplicationUnread["data"] as $value) {
                    $start++;
                    if ($counter + 1 > $totalMessageEvent) {
                        $flagNextRecords = false;
                        break;
                    }

                    $arrayMessageApplicationData = $value;

                    $processUid = $arrayMessageApplicationData["PRJ_UID"];
                    $taskUid = $arrayMessageApplicationData["TAS_UID"];

                    $messageApplicationUid         = $arrayMessageApplicationData["MSGAPP_UID"];
                    $messageApplicationCorrelation = $arrayMessageApplicationData["MSGAPP_CORRELATION"];

                    $messageEventDefinitionUserUid     = $arrayMessageApplicationData["MSGED_USR_UID"];
                    $messageEventDefinitionCorrelation = $arrayMessageApplicationData["MSGED_CORRELATION"];

                    $arrayVariable = $this->mergeVariables($arrayMessageApplicationData["MSGED_VARIABLES"], $arrayMessageApplicationData["MSGAPP_VARIABLES"]);

                    $flagCatched = false;

                    switch ($arrayMessageApplicationData["EVN_TYPE"]) {
                        case "START":
                            if ($messageEventDefinitionCorrelation == $messageApplicationCorrelation && $messageEventDefinitionUserUid != "") {
                                //Start and derivate new Case
                                $result = $ws->newCase($processUid, $messageEventDefinitionUserUid, $taskUid, $arrayVariable);

                                $arrayResult = \G::json_decode(\G::json_encode($result), true);
                                $appNumber = !empty($arrayResult["caseNumber"]) ? $arrayResult["caseNumber"] : '';

                                if ($arrayResult["status_code"] == 0) {
                                    $applicationUid = $arrayResult["caseId"];
                                    $appUid    = $arrayResult["caseId"];
                                    $aInfo = array(
                                        'ip'       => \G::getIpAddress()
                                        ,'action'   => 'CREATED-NEW-CASE'
                                        ,'workspace'=> $sysSys
                                        ,'usrUid'   => $messageEventDefinitionUserUid
                                        ,'proUid'   => $processUid
                                        ,'tasUid'   => $taskUid
                                        ,'appUid'   => $appUid
                                        ,'appNumber'=> $appNumber
                                        ,'evnUid'   => $value['EVN_UID']
                                        ,'evnName'  => $value['EVN_NAME']
                                    );
                                    $this->syslog(
                                        200
                                        ,"Case #$appNumber created"
                                        ,'CREATED-NEW-CASE'
                                        ,$aInfo
                                    );

                                    $result = $ws->derivateCase($messageEventDefinitionUserUid, $applicationUid, 1);
                                    $arrayResult = \G::json_decode(\G::json_encode($result), true);
                                    if ($arrayResult["status_code"] == 0) {
                                        $aInfo = array(
                                            'ip'       => \G::getIpAddress()
                                            ,'action'   => 'ROUTED-NEW-CASE'
                                            ,'workspace'=> $sysSys
                                            ,'usrUid'   => $messageEventDefinitionUserUid
                                            ,'proUid'   => $processUid
                                            ,'tasUid'   => $taskUid
                                            ,'appUid'   => $appUid
                                            ,'appNumber'=> $appNumber
                                            ,'delIndex' => '1'
                                            ,'evnUid'   => $value['EVN_UID']
                                            ,'evnName'  => $value['EVN_NAME']
                                        );
                                        $this->syslog(
                                            200
                                            ,"Case #$appNumber routed"
                                            ,'ROUTED-NEW-CASE'
                                            ,$aInfo
                                        );
                                    } else {
                                        $aInfo = array(
                                            'ip'       => \G::getIpAddress()
                                            ,'action'   => 'ROUTED-NEW-CASE'
                                            ,'workspace'=> $sysSys
                                            ,'usrUid'   => $messageEventDefinitionUserUid
                                            ,'proUid'   => $processUid
                                            ,'tasUid'   => $taskUid
                                            ,'appUid'   => $appUid
                                            ,'appNumber'=> $appNumber
                                            ,'evnUid'   => $value['EVN_UID']
                                            ,'evnName'  => $value['EVN_NAME']
                                        );
                                        $this->syslog(
                                            500
                                            ,"Failed case #$appNumber. " . $arrayResult["message"]
                                            ,'ROUTED-NEW-CASE'
                                            ,$aInfo
                                        );
                                    }

                                    $flagCatched = true;

                                    //Counter
                                    $counterStartMessageEvent++;
                                } else {
                                    $aInfo = array(
                                        'ip'       => \G::getIpAddress()
                                        ,'action'   => 'CREATED-NEW-CASE'
                                        ,'workspace'=> $sysSys
                                        ,'usrUid'   => $messageEventDefinitionUserUid
                                        ,'proUid'   => $processUid
                                        ,'tasUid'   => $taskUid
                                        ,'evnUid'   => $value['EVN_UID']
                                        ,'evnName'  => $value['EVN_NAME']
                                    );
                                    $this->syslog(
                                        500
                                        ,"Failed case #$appNumber. " . $arrayResult["message"]
                                        ,'CREATED-NEW-CASE'
                                        ,$aInfo
                                    );
                                }
                            }
                            break;
                        case "INTERMEDIATE":
                            $criteria = new \Criteria("workflow");

                            $criteria->addSelectColumn(\AppDelegationPeer::APP_UID);
                            $criteria->addSelectColumn(\AppDelegationPeer::DEL_INDEX);
                            $criteria->addSelectColumn(\AppDelegationPeer::USR_UID);

                            $criteria->add(\AppDelegationPeer::PRO_UID, $processUid, \Criteria::EQUAL);
                            $criteria->add(\AppDelegationPeer::TAS_UID, $taskUid, \Criteria::EQUAL);
                            $criteria->add(\AppDelegationPeer::DEL_THREAD_STATUS, "OPEN", \Criteria::EQUAL);
                            $criteria->add(\AppDelegationPeer::DEL_FINISH_DATE, null, \Criteria::ISNULL);

                            $rsCriteria = \AppDelegationPeer::doSelectRS($criteria);
                            $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

                            while ($rsCriteria->next()) {
                                $row = $rsCriteria->getRow();

                                $applicationUid = $row["APP_UID"];
                                $delIndex = $row["DEL_INDEX"];
                                $userUid = $row["USR_UID"];

                                $arrayApplicationData = $case->loadCase($applicationUid);

                                if (\G::replaceDataField($messageEventDefinitionCorrelation, $arrayApplicationData["APP_DATA"]) == $messageApplicationCorrelation) {
                                    //"Unpause" and derivate Case
                                    $arrayApplicationData["APP_DATA"] = array_merge($arrayApplicationData["APP_DATA"], $arrayVariable);

                                    $arrayResult = $case->updateCase($applicationUid, $arrayApplicationData);
                                    $appNumber = isset($arrayApplicationData["APP_DATA"]["APP_NUMBER"]) ? $arrayApplicationData["APP_DATA"]["APP_NUMBER"] : '';

                                    $result = $ws->derivateCase($userUid, $applicationUid, $delIndex);
                                    $arrayResult = \G::json_decode(\G::json_encode($result), true);
                                    if ($arrayResult["status_code"] == 0) {
                                        $aInfo = array(
                                            'ip'       => \G::getIpAddress()
                                            ,'action'   => 'ROUTED-NEW-CASE'
                                            ,'workspace'=> $sysSys
                                            ,'usrUid'   => $userUid
                                            ,'proUid'   => $processUid
                                            ,'tasUid'   => $taskUid
                                            ,'appUid'   => $applicationUid
                                            ,'appNumber'=> $appNumber
                                            ,'delIndex' => $delIndex
                                            ,'evnUid'   => $value['EVN_UID']
                                            ,'evnName'  => $value['EVN_NAME']
                                        );
                                        $this->syslog(
                                            200
                                            ,"Case #$appNumber routed "
                                            ,'ROUTED-NEW-CASE'
                                            ,$aInfo
                                        );
                                    } else {
                                        $aInfo = array(
                                            'ip'       => \G::getIpAddress()
                                            ,'action'   => 'ROUTED-NEW-CASE'
                                            ,'workspace'=> $sysSys
                                            ,'usrUid'   => $userUid
                                            ,'proUid'   => $processUid
                                            ,'tasUid'   => $taskUid
                                            ,'appUid'   => $applicationUid
                                            ,'appNumber'=> $appNumber
                                            ,'delIndex' => $delIndex
                                            ,'evnUid'   => $value['EVN_UID']
                                            ,'evnName'  => $value['EVN_NAME']
                                        );
                                        $this->syslog(
                                        500
                                        ,"Failed case #$appNumber. " . $arrayResult["message"]
                                        ,'ROUTED-NEW-CASE'
                                        ,$aInfo
                                    );
                                    }

                                    $flagCatched = true;
                                    break;
                                }
                            }

                            //Counter
                            if ($flagCatched) {
                                $counterIntermediateCatchMessageEvent++;
                                $start--;
                            }
                            break;
                    }

                    //Message-Application catch
                    if ($flagCatched) {
                        $result = $this->update($messageApplicationUid, array("MSGAPP_STATUS" => "READ"));
                    }

                    $counter++;

                    //Progress bar
                    $common->frontEndShow("BAR", "Message-Events (unread): " . $counter . "/" . $totalMessageEvent . " " . $common->progressBar($totalMessageEvent, $counter));

                    $flagNextRecords = true;
                }
            } while ($flagNextRecords);

            $common->frontEndShow("TEXT", "Total Message-Events unread: " . $totalMessageEvent);
            $common->frontEndShow("TEXT", "Total cases started: " . $counterStartMessageEvent);
            $common->frontEndShow("TEXT", "Total cases continued: " . $counterIntermediateCatchMessageEvent);
            $common->frontEndShow("TEXT", "Total Message-Events pending: " . ($totalMessageEvent - ($counterStartMessageEvent + $counterIntermediateCatchMessageEvent)));
            $this->syslog(
                200
                ,'Total Message-Events unread '. $totalMessageEvent
                ,'RESUME'//Action
            );
            $this->syslog(
                200
                ,'Total cases started '. $counterStartMessageEvent
                ,'RESUME'//Action
            );
            $this->syslog(
                200
                ,'Total cases continued '. $counterIntermediateCatchMessageEvent
                ,'RESUME'//Action
            );
            $this->syslog(
                200
                ,'Total Message-Events pending '. ($totalMessageEvent - ($counterStartMessageEvent + $counterIntermediateCatchMessageEvent))
                ,'RESUME'//Action
            );

            $common->frontEndShow("END");
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * The Syslog register the information in Monolog Class
     *
     * @param int $level DEBUG=100 INFO=200 NOTICE=250 WARNING=300 ERROR=400 CRITICAL=500
     * @param string $message
     * @param string $ipClient for Context information
     * @param string $action for Context information
     * @param string $timeZone for Context information
     * @param string $workspace for Context information
     * @param string $usrUid for Context information
     * @param string $proUid for Context information
     * @param string $tasUid for Context information
     * @param string $appUid for Context information
     * @param string $delIndex for Context information
     * @param string $stepUid for Context information
     * @param string $triUid for Context information
     * @param string $outDocUid for Context information
     * @param string $inpDocUid for Context information
     * @param string $url for Context information
     *
     * return void
     */
    private function syslog(
        $level,
        $message,
        $action='',
        $aContext = array()
    )
    {
        try {
            \Bootstrap::registerMonolog('MessageEventCron', $level, $message, $aContext, SYS_SYS, 'processmaker.log');
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
