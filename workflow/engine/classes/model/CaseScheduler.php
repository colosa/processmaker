<?php
/**
 * CaseScheduler.php
 *
 * @package workflow.engine.classes.model
 */

//require_once 'classes/model/om/BaseCaseScheduler.php';

//require_once 'classes/model/Process.php';
//require_once 'classes/model/Task.php';

/**
 * Skeleton subclass for representing a row from the 'CASE_SCHEDULER' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements. This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package workflow.engine.classes.model
 */
class CaseScheduler extends BaseCaseScheduler
{
    public function load ($SchUid)
    {
        try {
            $oRow = CaseSchedulerPeer::retrieveByPK( $SchUid );
            if (! is_null( $oRow )) {
                $aFields = $oRow->toArray( BasePeer::TYPE_FIELDNAME );
                $this->fromArray( $aFields, BasePeer::TYPE_FIELDNAME );
                $this->setNew( false );
                return $aFields;
            } else {
                throw (new Exception( "The row '" . $SchUid . "' in table CASE_SCHEDULER doesn't exist!" ));
            }
        } catch (Exception $oError) {
            throw ($oError);
        }
    }

    public function create ($aData)
    {
        $con = Propel::getConnection( CaseSchedulerPeer::DATABASE_NAME );
        try {
            if (isset($aData["SCH_OPTION"]) && (int)($aData["SCH_OPTION"]) == 4) {
                //One time only
                $aData["SCH_END_DATE"] = null;
            }

            $this->fromArray( $aData, BasePeer::TYPE_FIELDNAME );
            if ($this->validate()) {
                $result = $this->save();
            } else {
                $e = new Exception( "Failed Validation in class " . get_class( $this ) . "." );
                $e->aValidationFailures = $this->getValidationFailures();
                throw ($e);
            }
            $con->commit();

            //Add Audit Log
            $perform = $aData["SCH_OPTION"];

            switch ($aData['SCH_OPTION']) {
                case '1':
                    $perform = 'Daily';
                    break;
                case '2':
                    $perform = 'Weekly';
                    break;
                case '3':
                    $perform = 'Monthly';
                    break;
                case '4':
                    $perform = 'One time only';
                    break;
                case '5':
                    $perform = 'Every';
                    break;

            }
            G::auditLog("CreateCaseScheduler", "Scheduler Name: ".$aData['SCH_NAME'].", Task: ".$aData['TAS_UID'].", Perform this task: ".$perform.", Start Date: ".$aData['SCH_START_DATE'].", End Date: ".$aData['SCH_END_DATE'].",  Execution time  : ".$aData['SCH_START_TIME']);

            return $result;
        } catch (Exception $e) {
            $con->rollback();
            throw ($e);
        }
    }

    public function update ($fields)
    {
        $con = Propel::getConnection( CaseSchedulerPeer::DATABASE_NAME );
        try {
            if (isset($fields["SCH_OPTION"]) && (int)($fields["SCH_OPTION"]) == 4) {
                //One time only
                $fields["SCH_END_DATE"] = null;
            }

            $con->begin();
            $this->load( $fields['SCH_UID'] );
            $this->fromArray( $fields, BasePeer::TYPE_FIELDNAME );
            if ($this->validate()) {
                $result = $this->save();
                $con->commit();

                if (isset($fields['SCH_OPTION'])) {
                    //Add Audit Log
                    switch ($fields['SCH_OPTION']){
                    case '1':
                        $perform = 'Daily';
                        break;
                    case '2':
                        $perform = 'Weekly';
                        break;
                    case '3':
                        $perform = 'Monthly';
                        break;
                    case '4':
                        $perform = 'One time only';
                        break;
                    case '5':
                        $perform = 'Every';
                        break;
                    }
                    G::auditLog("UpdateCaseScheduler", "Scheduler Name: ".$fields['SCH_NAME'].", Task: ".$fields['TAS_UID'].", Perform this task: ".$perform.", Start Date: ".$fields['SCH_START_DATE'].", End Date: ".$fields['SCH_END_DATE'].",  Execution time  : ".$fields['SCH_START_TIME']);
                }

                return $result;
            } else {
                $con->rollback();
                throw (new Exception( "Failed Validation in class " . get_class( $this ) . "." ));
            }
        } catch (Exception $e) {
            $con->rollback();
            throw ($e);
        }
    }

    public function remove ($SchUid)
    {
        $con = Propel::getConnection( CaseSchedulerPeer::DATABASE_NAME );
        try {
            $oCaseScheduler = CaseSchedulerPeer::retrieveByPK( $SchUid );
            if (! is_null( $oCaseScheduler )) {
                $fields = $this->Load( $SchUid );
                $iResult = $oCaseScheduler->delete();
                $con->commit();
                //Add Audit Log
                G::auditLog("DeleteCaseScheduler", "Scheduler Name: ".$fields['SCH_NAME'].", Task: ".$fields['TAS_UID']);

                return $iResult;
            } else {
                throw (new Exception( 'This row doesn\'t exist!' ));
            }
        } catch (Exception $e) {
            $con->rollback();
            throw ($e);
        }
    }

    /*
    * change Status of any Process
    * @param string $sSchedulerUid
    * @return boolean
    */
    public function changeStatus ($sSchedulerUid = '')
    {
        $Fields = $this->Load( $sSchedulerUid );
        $Fields['SCH_LAST_STATE'] = $Fields['SCH_STATE'];
        if ($Fields['SCH_STATE'] == 'ACTIVE') {
            $Fields['SCH_STATE'] = 'INACTIVE';
        } else {
            $Fields['SCH_STATE'] = 'ACTIVE';
        }
        $this->Update( $Fields );
    }

    public function getAllCriteria ()
    {
        $c = new Criteria( 'workflow' );
        $c->clearSelectColumns();
        $c->addSelectColumn( CaseSchedulerPeer::SCH_UID );
        $c->addSelectColumn( CaseSchedulerPeer::SCH_NAME );
        $c->addSelectColumn( CaseSchedulerPeer::SCH_DEL_USER_NAME );
        $c->addSelectColumn( CaseSchedulerPeer::SCH_DEL_USER_PASS );
        $c->addSelectColumn( CaseSchedulerPeer::SCH_DEL_USER_UID );
        $c->addSelectColumn( CaseSchedulerPeer::PRO_UID );
        $c->addSelectColumn( CaseSchedulerPeer::TAS_UID );
        $c->addSelectColumn( CaseSchedulerPeer::SCH_TIME_NEXT_RUN );
        $c->addSelectColumn( CaseSchedulerPeer::SCH_LAST_RUN_TIME );
        $c->addSelectColumn( CaseSchedulerPeer::SCH_STATE );
        $c->addSelectColumn( CaseSchedulerPeer::SCH_LAST_STATE );
        $c->addSelectColumn( CaseSchedulerPeer::USR_UID );
        $c->addSelectColumn( CaseSchedulerPeer::SCH_OPTION );
        $c->addSelectColumn( CaseSchedulerPeer::SCH_START_TIME );
        $c->addSelectColumn( CaseSchedulerPeer::SCH_START_DATE );
        $c->addSelectColumn( CaseSchedulerPeer::SCH_DAYS_PERFORM_TASK );
        $c->addSelectColumn( CaseSchedulerPeer::SCH_EVERY_DAYS );
        $c->addSelectColumn( CaseSchedulerPeer::SCH_WEEK_DAYS );
        $c->addSelectColumn( CaseSchedulerPeer::SCH_START_DAY );
        $c->addSelectColumn( CaseSchedulerPeer::SCH_MONTHS );
        $c->addSelectColumn( CaseSchedulerPeer::SCH_END_DATE );
        $c->addSelectColumn( CaseSchedulerPeer::SCH_REPEAT_EVERY );
        $c->addSelectColumn( CaseSchedulerPeer::SCH_REPEAT_UNTIL );
        $c->addSelectColumn( CaseSchedulerPeer::SCH_REPEAT_STOP_IF_RUNNING );
        $c->addSelectColumn( CaseSchedulerPeer::CASE_SH_PLUGIN_UID );

        return $c;
    }

    public function getAll ()
    {
        $oCriteria = $this->getAllCriteria();
        $oDataset = CaseSchedulerPeer::doSelectRS( $oCriteria );

        $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
        $oDataset->next();
        $aRows = Array ();
        while ($aRow = $oDataset->getRow()) {
            $aRows[] = $aRow;
            $oDataset->next();
        }
        foreach ($aRows as $k => $aRow) {
            $oProcess = new Process();
            $aProcessRow = $oProcess->load( $aRow['PRO_UID'] );

            $oTask = new Task();
            $aTaskRow = $oTask->load( $aRow['TAS_UID'] );

            $aRows[$k] = array_merge( $aRow, $aProcessRow, $aTaskRow );
        }

        return $aRows;
    }

    /**
     * function getAllByProcess
     * Get All Scheduled Tasks for some process.
     *
     * @author gustavo cruz
     * @param $pro_uid process uid
     * @return $aRows a result set array
     */
    public function getAllByProcess ($pro_uid)
    {

        $oCriteria = $this->getAllCriteria();
        $oCriteria->add( CaseSchedulerPeer::PRO_UID, $pro_uid );
        $oDataset = CaseSchedulerPeer::doSelectRS( $oCriteria );

        $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
        $oDataset->next();
        $aRows = Array ();
        while ($aRow = $oDataset->getRow()) {
            $aRows[] = $aRow;
            $oDataset->next();
        }
        foreach ($aRows as $k => $aRow) {
            $oProcess = new Process();
            $aProcessRow = $oProcess->load( $aRow['PRO_UID'] );
            $oTask = new Task();
            $aTaskRow = $oTask->load( $aRow['TAS_UID'] );
            $aRows[$k] = array_merge( $aRow, $aProcessRow, $aTaskRow );
        }
        return $aRows;
    }

    public function getProcessDescription ()
    {
        $c = new Criteria( 'workflow' );
        $c->clearSelectColumns();
        $c->addSelectColumn( ProcessPeer::PRO_UID );

        $oDataset = ProcessPeer::doSelectRS( $c );
        $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
        $oDataset->next();
        $aRows = Array ();
        while ($aRow = $oDataset->getRow()) {
            $aRows[] = $aRow;
            $oDataset->next();
        }

        foreach ($aRows as $k => $aRow) {
            $oProcess = new Process();
            $aProcessRow = $oProcess->load( $aRow['PRO_UID'] );

            $aRows[$k] = array_merge( $aRow, array ('PRO_TITLE' => $aProcessRow['PRO_TITLE']
            ) );
        }
        return $aRows;

    }

    public function getTaskDescription ()
    {
        $c = new Criteria( 'workflow' );
        $c->clearSelectColumns();
        $c->addSelectColumn( TaskPeer::TAS_UID );

        $oDataset = TaskPeer::doSelectRS( $c );
        $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
        $oDataset->next();
        $aRows = Array ();
        while ($aRow = $oDataset->getRow()) {
            $aRows[] = $aRow;
            $oDataset->next();
        }
        foreach ($aRows as $k => $aRow) {

            $oTask = new Task();
            $aTaskRow = $oTask->load( $aRow['TAS_UID'] );

            $aRows[$k] = array_merge( $aRow, array ('TAS_TITLE' => $aTaskRow['TAS_TITLE']
            ), array ('PRO_UID' => $aTaskRow['PRO_UID']
            ) );
        }
        return $aRows;
    }

    public function caseSchedulerCron ($date, &$log = array(), $cron = 0)
    {
        try {
            require_once("classes" . PATH_SEP . "model" . PATH_SEP . "LogCasesScheduler.php");

            //Set variables
            $port = "";

            if (isset($_SERVER["SERVER_PORT"])) {
                $port = ($_SERVER["SERVER_PORT"] . "" != "80")? ":" . $_SERVER["SERVER_PORT"] : "";
            } else {
                if (defined("SERVER_PORT")) {
                    $port = (SERVER_PORT . "" != "80")? ":" . SERVER_PORT : "";
                }
            }

            $url = SERVER_NAME . $port . "/sys" . SYS_SYS . "/" . SYS_LANG . "/classic/services/wsdl2";
            
            $testConnection = true;
            try {
                @$client = new SoapClient("http://" . $url);
            } catch (SoapFault $fault) {
                $testConnection = false;
            }
 
            $wsdl = ($testConnection) ? "http://" . $url : "https://" . $url;

            $timeDate = strtotime($date);

            $dateHour    = date("H", $timeDate);
            $dateMinutes = date("i", $timeDate);

            $dateCurrentIni = date("Y-m-d", $timeDate) . " 00:00:00";
            $dateCurrentEnd = date("Y-m-d", $timeDate) . " 23:59:59";

            //Query
            $criteria = $this->getAllCriteria();

            $criteria->add(
                $criteria->getNewCriterion(CaseSchedulerPeer::SCH_STATE, "INACTIVE", Criteria::NOT_EQUAL)->addAnd(
                $criteria->getNewCriterion(CaseSchedulerPeer::SCH_STATE, "PROCESSED", Criteria::NOT_EQUAL))
            );
            $criteria->add(
                $criteria->getNewCriterion(CaseSchedulerPeer::SCH_TIME_NEXT_RUN, $dateCurrentIni, Criteria::GREATER_EQUAL)->addAnd(
                $criteria->getNewCriterion(CaseSchedulerPeer::SCH_TIME_NEXT_RUN, $dateCurrentEnd, Criteria::LESS_EQUAL))->addOr(
                //$criteria->getNewCriterion(CaseSchedulerPeer::SCH_OPTION, 5, Criteria::GREATER_EQUAL))->addOr(

                $criteria->getNewCriterion(CaseSchedulerPeer::SCH_TIME_NEXT_RUN, $dateCurrentIni, Criteria::LESS_THAN))
            );
            $criteria->add(
                $criteria->getNewCriterion(CaseSchedulerPeer::SCH_END_DATE, null, Criteria::EQUAL)->addOr(
                $criteria->getNewCriterion(CaseSchedulerPeer::SCH_END_DATE, $dateCurrentIni, Criteria::GREATER_EQUAL))
            );

            $rsCriteria = CaseSchedulerPeer::doSelectRS($criteria);
            $rsCriteria->setFetchmode(ResultSet::FETCHMODE_ASSOC);

            while ($rsCriteria->next()) {
                $row = $rsCriteria->getRow();

                if ($cron == 1) {
                    $arrayCron = unserialize(trim(@file_get_contents(PATH_DATA . "cron")));
                    $arrayCron["processcTimeStart"] = time();
                    @file_put_contents(PATH_DATA . "cron", serialize($arrayCron));
                }

                $caseSchedulerUid    = $row["SCH_UID"];
                $caseSchedulerOption = (int)($row["SCH_OPTION"]);
                $caseSchedulerTimeNextRun = $row["SCH_TIME_NEXT_RUN"];

                //Create the new case
                $flagNewCase = false;
                $caseSchedulerTimeNextRunNew = "";

                if (strtotime($caseSchedulerTimeNextRun) < strtotime($dateCurrentIni)) {
                    //Generate new date for old SCH_TIME_NEXT_RUN
                    $flagNewCase = true; //Create the old case
                    $caseSchedulerTimeNextRunNew = $this->getTimeNextRunByDate($row, $date, false);
                } else {
                    $caseSchedulerTimeNextRunHour    = date("H", strtotime($row["SCH_TIME_NEXT_RUN"]));
                    $caseSchedulerTimeNextRunMinutes = date("i", strtotime($row["SCH_TIME_NEXT_RUN"]));

                    if ((int)($dateHour . $dateMinutes) <= (int)($caseSchedulerTimeNextRunHour . $caseSchedulerTimeNextRunMinutes)) {
                        $flagNewCase = $caseSchedulerTimeNextRunHour == $dateHour && $caseSchedulerTimeNextRunMinutes == $dateMinutes;
                    } else {
                        $flagNewCase = true; //Create the old case
                    }
                }

                if ($flagNewCase) {
                    println("  CASE SCHEDULER: " . $row["SCH_NAME"]);
                    println("  - Connecting webservice: $wsdl");

                    $user = $row["SCH_DEL_USER_NAME"];
                    $pass = $row["SCH_DEL_USER_PASS"];
                    $processId = $row["PRO_UID"];
                    $taskId = $row["TAS_UID"];

                    $client = new SoapClient($wsdl);
                    $result = $client->__SoapCall("login",
                        array(
                            array("userid" => $user, "password" => Bootstrap::getPasswordHashType() . ":" . $pass)
                        )
                    );

                    eprintln("  - Logging as user \"$user\"...");

                    $paramsLog = array(
                        "PRO_UID"   => $processId,
                        "TAS_UID"   => $taskId,
                        "SCH_UID"   => $caseSchedulerUid,
                        "USR_NAME"  => $user,
                        "RESULT"    => "",
                        "EXEC_DATE" => date("Y-m-d"),
                        "EXEC_HOUR" => date("H:i:s"),
                        "WS_CREATE_CASE_STATUS" => "",
                        "WS_ROUTE_CASE_STATUS"  => ""
                    );

                    $paramsLogResult = "FAILED";
                    $paramsRouteLogResult = "FAILED";

                    if ($result->status_code == 0) {
                        eprintln("    OK", "green");

                        $sessionId = $result->message;

                        $params = array("sessionId" => $sessionId, "processId" => $processId, "taskId" => $taskId, "variables" => array());

                        //If this Job was was registered to be performed by a plugin
                        if (isset($row["CASE_SH_PLUGIN_UID"]) && $row["CASE_SH_PLUGIN_UID"] != "") {
                            //Check if the plugin is active
                            $pluginParts = explode("--", $row["CASE_SH_PLUGIN_UID"]);

                            if (count($pluginParts) == 2) {
                                //Plugins
                                G::LoadClass("plugin");

                                //Here we are loading all plugins registered
                                //The singleton has a list of enabled plugins
                                $sSerializedFile = PATH_DATA_SITE . "plugin.singleton";
                                $oPluginRegistry = &PMPluginRegistry::getSingleton();

                                if (file_exists($sSerializedFile)) {
                                    $oPluginRegistry->unSerializeInstance(file_get_contents($sSerializedFile));
                                }

                                $oPluginRegistry = &PMPluginRegistry::getSingleton();
                                $activePluginsForCaseScheduler = $oPluginRegistry->getCaseSchedulerPlugins();

                                foreach ($activePluginsForCaseScheduler as $key => $caseSchedulerPlugin) {
                                    if (isset($caseSchedulerPlugin->sNamespace) && $caseSchedulerPlugin->sNamespace == $pluginParts[0] && isset($caseSchedulerPlugin->sActionId) && $caseSchedulerPlugin->sActionId == $pluginParts[1]) {
                                        $caseSchedulerSelected = $caseSchedulerPlugin;
                                    }
                                }
                            }
                        }

                        //If there is a trigger that is registered to do this then transfer control
                        if (isset($caseSchedulerSelected) && is_object($caseSchedulerSelected)) {
                            eprintln("  - Transfering control to a Plugin: " . $caseSchedulerSelected->sNamespace . "/" . $caseSchedulerSelected->sActionId, "green");

                            $oData = array();
                            $oData["OBJ_SOAP"] = $client;
                            $oData["SCH_UID"] = $row["SCH_UID"];
                            $oData["params"] = $params;
                            $oData["sessionId"] = $sessionId;
                            $oData["userId"] = $user;

                            $paramsLogResultFromPlugin = $oPluginRegistry->executeMethod($caseSchedulerSelected->sNamespace, $caseSchedulerSelected->sActionExecute, $oData);
                            $paramsLog["WS_CREATE_CASE_STATUS"] = $paramsLogResultFromPlugin["WS_CREATE_CASE_STATUS"];
                            $paramsLog["WS_ROUTE_CASE_STATUS"] = $paramsLogResultFromPlugin["WS_ROUTE_CASE_STATUS"];

                            $paramsLogResult = $paramsLogResultFromPlugin["paramsLogResult"];
                            $paramsRouteLogResult = $paramsLogResultFromPlugin["paramsRouteLogResult"];
                        } else {
                            eprintln("  - Creating the new case...");

                            $paramsAux = $params;
                            $paramsAux["executeTriggers"] = 1;

                            $oPluginRegistry = &PMPluginRegistry::getSingleton();

                            if ($oPluginRegistry->existsTrigger(PM_SCHEDULER_CREATE_CASE_BEFORE)) {
                                $oPluginRegistry->executeTriggers(PM_SCHEDULER_CREATE_CASE_BEFORE, $paramsAux);
                            }

                            $result = $client->__SoapCall("NewCase", array($paramsAux));

                            if ($oPluginRegistry->existsTrigger (PM_SCHEDULER_CREATE_CASE_AFTER)) {
                                $oPluginRegistry->executeTriggers(PM_SCHEDULER_CREATE_CASE_AFTER, $result);
                            }

                            if ($result->status_code == 0) {
                                eprintln("    OK case #" . $result->caseNumber . " was created!", "green");

                                $caseId = $result->caseId;
                                $caseNumber = $result->caseNumber;
                                $log[] = $caseNumber . " was created!, ProcessID: " . $row["PRO_UID"];
                                $paramsLog["WS_CREATE_CASE_STATUS"] = "Case " . $caseNumber . " " . strip_tags($result->message);
                                $paramsLogResult = "SUCCESS";
                                $params = array("sessionId" => $sessionId, "caseId" => $caseId, "delIndex" => "1");

                                try {
                                    eprintln("  - Routing the case #$caseNumber...");

                                    $result = $client->__SoapCall("RouteCase", array($params));

                                    if ($result->status_code == 0) {
                                        $paramsLog["WS_ROUTE_CASE_STATUS"] = strip_tags($result->message);
                                        $retMsg = explode("Debug", $paramsLog["WS_ROUTE_CASE_STATUS"]);
                                        $retMsg = $retMsg[0];
                                        $paramsRouteLogResult = "SUCCESS";

                                        eprintln("    OK $retMsg", "green");
                                    } else {
                                        $paramsLog["WS_ROUTE_CASE_STATUS"] = strip_tags($result->message);
                                        $paramsRouteLogResult = "FAILED";

                                        eprintln("    Failed: " . $paramsLog["WS_ROUTE_CASE_STATUS"], "red");
                                    }
                                } catch (Exception $e) {
                                    //setExecutionResultMessage("WITH ERRORS", "error");
                                    $paramsLog["WS_ROUTE_CASE_STATUS"] = strip_tags($e->getMessage());
                                    $paramsRouteLogResult = "FAILED";

                                    eprintln("    Failed: " . strip_tags($e->getMessage()), "red");
                                }
                            } else {
                                $paramsLog["WS_CREATE_CASE_STATUS"] = strip_tags($result->message);
                                $paramsLogResult = "FAILED";

                                eprintln("    Failed: " . $paramsLog["WS_CREATE_CASE_STATUS"], "red");
                            }
                        }
                    } else {
                        //Invalid user or bad password
                        eprintln("    " . $result->message, "red");
                    }

                    if ($paramsLogResult == "SUCCESS" && $paramsRouteLogResult == "SUCCESS") {
                        $paramsLog["RESULT"] = "SUCCESS";
                    } else {
                        $paramsLog["RESULT"] = "FAILED";
                    }

                    $newCaseLog = new LogCasesScheduler();
                    $newCaseLog->saveLogParameters($paramsLog);
                    $newCaseLog->save();

                    //Update the SCH_TIME_NEXT_RUN field
                    switch ($caseSchedulerOption) {
                        case 1:
                        case 2:
                        case 3:
                            //Daily
                            //Weekly
                            //Monthly
                            if ($caseSchedulerTimeNextRunNew == "") {
                                list($value, $daysPerformTask, $weeks, $startDay, $months) = $this->getVariablesFromRecord($row);

                                $caseSchedulerTimeNextRunNew = $this->updateNextRun($caseSchedulerOption, $value, $caseSchedulerTimeNextRun, $daysPerformTask, $weeks, $startDay, $months);
                            }

                            if ($row["SCH_END_DATE"] . "" != "" && strtotime($row["SCH_END_DATE"]) < strtotime($caseSchedulerTimeNextRunNew)) {
                                $result = $this->update(array(
                                    "SCH_UID"           => $caseSchedulerUid,
                                    "SCH_LAST_STATE"    => $row["SCH_STATE"],
                                    "SCH_LAST_RUN_TIME" => $caseSchedulerTimeNextRun,
                                    "SCH_STATE"         => "PROCESSED"
                                ));
                            } else {
                                $this->updateDate($caseSchedulerUid, $caseSchedulerTimeNextRunNew, $caseSchedulerTimeNextRun);
                            }
                            break;
                        case 4:
                            //One time only
                            $result = $this->update(array(
                                "SCH_UID"           => $caseSchedulerUid,
                                "SCH_LAST_STATE"    => $row["SCH_STATE"],
                                "SCH_LAST_RUN_TIME" => $caseSchedulerTimeNextRun,
                                "SCH_STATE"         => "PROCESSED"
                            ));
                            break;
                        case 5:
                            //Every
                            if ($caseSchedulerTimeNextRunNew == "") {
                                $caseSchedulerTimeNextRunNew = date("Y-m-d H:i:s", $timeDate + round(floatval($row["SCH_REPEAT_EVERY"]) * 60 * 60));
                            }

                            $this->updateDate($caseSchedulerUid, $caseSchedulerTimeNextRunNew, $caseSchedulerTimeNextRun);
                            break;
                    }
                }
            }
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function updateDate ($sSchedulerUid = '', $sSchTimeNextRun = '', $sSchLastRunTime = '')
    {
        $Fields = $this->Load( $sSchedulerUid );
        $Fields['SCH_TIME_NEXT_RUN'] = strtotime( $sSchTimeNextRun );
        $Fields['SCH_LAST_RUN_TIME'] = strtotime( $sSchLastRunTime );
        $this->Update( $Fields );
    }

    public function updateNextRun($option, $optionMonth = "", $date = "", $daysPerformTask = "", $weeks = "", $startDay = "", $months = "", $currentDate = "", $flagNoTodayForNextRun = true)
    {
        try {
            $dateNextRun = "";

            $currentDate = trim($currentDate . " " . $date); //$date and $currentDate are the same
            $weeks  = trim($weeks, " |");
            $months = trim($months, " |");

            $arrayMonthsShort = array("Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec");
            $arrayWeekdays    = array("Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday");

            switch ((int)($option)) {
                case 1:
                    //Daily
                    $dateNextRun = date("Y-m-d H:i:s", strtotime(($flagNoTodayForNextRun)? "$currentDate +1 day" : $currentDate));
                    break;
                case 2:
                    //Weekly
                    if ($weeks != "") {
                        $weekday = (int)(date("w", strtotime($date)));
                        $weekday = ($weekday == 0)? 7 : $weekday;

                        $arrayWeekdaysData = explode("|", $weeks);

                        $firstWeekday = (int)($arrayWeekdaysData[0]);

                        $nextWeekday   = $firstWeekday;
                        $typeStatement = "this";
                        $flag = false;

                        foreach ($arrayWeekdaysData as $value) {
                            $d = (int)($value);

                            if ((!$flagNoTodayForNextRun && $d >= $weekday) || ($flagNoTodayForNextRun && $d > $weekday)) {
                                $nextWeekday = $d;
                                $flag = true;
                                break;
                            }
                        }

                        if (!$flag) {
                            $typeStatement = "next";
                        }

                        $dateNextRun = date("Y-m-d", strtotime($currentDate . " " . $typeStatement . " " . $arrayWeekdays[$nextWeekday - 1])) . " " . date("H:i:s", strtotime($date));
                    }
                    break;
                case 3:
                    //Monthly
                    if ($months != "") {
                        $year  = (int)(date("Y", strtotime($date)));
                        $month = (int)(date("m", strtotime($date)));

                        $arrayStartDay   = explode("|", $startDay);
                        $arrayMonthsData = explode("|", $months);

                        $firstMonth = (int)($arrayMonthsData[0]);

                        $nextMonth = $firstMonth;
                        $flag = false;

                        foreach ($arrayMonthsData as $value) {
                            $m = (int)($value);

                            if ((!$flagNoTodayForNextRun && $m >= $month) || ($flagNoTodayForNextRun && $m > $month)) {
                                $nextMonth = $m;
                                $flag = true;
                                break;
                            }
                        }

                        if (!$flag) {
                            $year++;
                        }

                        switch ((int)($optionMonth)) {
                            case 1:
                                $day = (int)($arrayStartDay[1]);

                                $dateNextRun = date("Y-m-d", strtotime("$year-$nextMonth-$day")) . " " . date("H:i:s", strtotime($date));
                                break;
                            case 2:
                                $arrayFormat = array(
                                    1 => "+0 week %s %s %d", //First
                                    2 => "+1 week %s %s %d", //Second
                                    3 => "+2 week %s %s %d", //Third
                                    4 => "+3 week %s %s %d", //Fourth
                                    5 => "last %s of %s %d"  //Last
                                );

                                $day = (int)($arrayStartDay[2]);

                                $dateNextRun = date("Y-m-d", strtotime(sprintf($arrayFormat[(int)($arrayStartDay[1])], $arrayWeekdays[$day - 1], $arrayMonthsShort[$nextMonth - 1], $year))) . " " . date("H:i:s", strtotime($date));
                                break;
                        }
                    }
                    break;
            }

            //Return
            return $dateNextRun;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function Exists ($sUid)
    {
        try {
            $oObj = CaseSchedulerPeer::retrieveByPk( $sUid );
            return (is_object( $oObj ) && get_class( $oObj ) == 'CaseScheduler');
        } catch (Exception $oError) {
            throw ($oError);
        }
    }

    /**
     * Get variables from a CaseScheduler record
     *
     * @param array $record Record
     *
     * return array Return an array with variables
     */
    public function getVariablesFromRecord(array $record)
    {
        try {
            $value = "";
            $daysPerformTask = "";
            $weeks = "";
            $startDay = "";
            $months = "";

            switch ((int)($record["SCH_OPTION"])) {
                case 1:
                    //Daily
                    $daysPerformTask = $record["SCH_DAYS_PERFORM_TASK"];
                    $arrayDaysPerformTask = explode("|", $daysPerformTask);
                    $value = $arrayDaysPerformTask[0];

                    if ($value != 1) {
                        $daysPerformTask = $arrayDaysPerformTask[1];
                    }
                    break;
                case 2:
                    //Weekly
                    $daysPerformTask = $record["SCH_EVERY_DAYS"];
                    $weeks = $record["SCH_WEEK_DAYS"];
                    break;
                case 3:
                    //Monthly
                    $startDay = $record["SCH_START_DAY"];
                    $months = $record["SCH_MONTHS"];
                    $arrayStartDay = explode("|", $startDay);
                    $value = $arrayStartDay[0];
                    break;
                case 4:
                    //One time only
                    break;
                case 5:
                    //Every
                    break;
            }

            //Return
            return array($value, $daysPerformTask, $weeks, $startDay, $months);
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Get the Time Next Run by Date
     *
     * @param array  $arrayCaseSchedulerData CaseScheduler Data
     * @param string $date                   Date
     * @param bool   $flagUpdateTimeNextRun  Flag
     *
     * return string Return the Time Next Run
     */
    public function getTimeNextRunByDate(array $arrayCaseSchedulerData, $date, $flagUpdateTimeNextRun = true)
    {
        try {
            $caseSchedulerOption      = (int)($arrayCaseSchedulerData["SCH_OPTION"]);
            $caseSchedulerTimeNextRun = $arrayCaseSchedulerData["SCH_TIME_NEXT_RUN"];
            $caseSchedulerStartTime   = date("H:i:s", strtotime($arrayCaseSchedulerData["SCH_START_TIME"]));

            list($value, $daysPerformTask, $weeks, $startDay, $months) = $this->getVariablesFromRecord($arrayCaseSchedulerData);

            $timeDate = strtotime($date); //Current time

            $flagTimeNextRun = true;
            $flagUpdate = false;

            switch ($caseSchedulerOption) {
                case 1:
                case 2:
                case 3:
                    //Daily
                    //Weekly
                    //Monthly
                    $caseSchedulerTimeNextRun = date("Y-m-d", strtotime($arrayCaseSchedulerData["SCH_START_DATE"])) . " " . $caseSchedulerStartTime;
                    $caseSchedulerTimeNextRun = $this->updateNextRun($caseSchedulerOption, $value, $caseSchedulerTimeNextRun, $daysPerformTask, $weeks, $startDay, $months, "", false);

                    $timeCaseSchedulerTimeNextRun = strtotime($caseSchedulerTimeNextRun);

                    if ($timeCaseSchedulerTimeNextRun > $timeDate) {
                        $flagTimeNextRun = false;
                        $flagUpdate = true;
                    }
                    break;
            }

            if ($flagTimeNextRun) {
                switch ($caseSchedulerOption) {
                    case 1:
                    case 2:
                    case 3:
                        //Daily
                        //Weekly
                        //Monthly
                        $caseSchedulerTimeNextRun = date("Y-m-d", $timeDate) . " " . $caseSchedulerStartTime;
                        $caseSchedulerTimeNextRun = $this->updateNextRun($caseSchedulerOption, $value, $caseSchedulerTimeNextRun, $daysPerformTask, $weeks, $startDay, $months, "", false);

                        $timeCaseSchedulerTimeNextRun = strtotime($caseSchedulerTimeNextRun);

                        if ($timeCaseSchedulerTimeNextRun < $timeDate) {
                            $caseSchedulerTimeNextRun = $this->updateNextRun($caseSchedulerOption, $value, $caseSchedulerTimeNextRun, $daysPerformTask, $weeks, $startDay, $months);
                        }
                        break;
                    case 4:
                        //One time only
                        $caseSchedulerTimeNextRun = date("Y-m-d", $timeDate) . " " . $caseSchedulerStartTime;

                        $timeCaseSchedulerTimeNextRun = strtotime($caseSchedulerTimeNextRun);

                        if ($timeCaseSchedulerTimeNextRun < $timeDate) {
                            $caseSchedulerTimeNextRun = $this->updateNextRun("1", "1", $caseSchedulerTimeNextRun, $daysPerformTask, $weeks, $startDay, $months);
                        }
                        break;
                    case 5:
                        //Every
                        $caseSchedulerTimeNextRun = date("Y-m-d H:i:s", $timeDate + round(floatval($arrayCaseSchedulerData["SCH_REPEAT_EVERY"]) * 60 * 60));
                        break;
                }

                $flagUpdate = true;
            }

            //Update the SCH_TIME_NEXT_RUN field
            if ($flagUpdateTimeNextRun && $flagUpdate) {
                $result = $this->update(array(
                    "SCH_UID"           => $arrayCaseSchedulerData["SCH_UID"],
                    "SCH_TIME_NEXT_RUN" => strtotime($caseSchedulerTimeNextRun)
                ));
            }

            //Return
            return $caseSchedulerTimeNextRun;
        } catch (Exception $e) {
            throw $e;
        }
    }
}

