<?php

namespace ProcessMaker\BusinessModel;



use \G;

use \ProcessMaker\Util;



class Task

{

    private $formatFieldNameInUppercase = true;

    private $arrayParamException = array(

        "taskUid" => "TAS_UID"

    );



    /**

     * Set the format of the fields name (uppercase, lowercase)

     *

     * @param bool $flag Value that set the format

     *

     * return void

     */

    public function setFormatFieldNameInUppercase($flag)

    {

        try {

            $this->formatFieldNameInUppercase = $flag;



            $this->setArrayParamException($this->arrayParamException);

        } catch (\Exception $e) {

            throw $e;

        }

    }



    /**

     * Set exception messages for parameters

     *

     * @param array $arrayData Data with the params

     *

     * return void

     */

    public function setArrayParamException($arrayData)

    {

        try {

            foreach ($arrayData as $key => $value) {

                $this->arrayParamException[$key] = $this->getFieldNameByFormatFieldName($value);

            }

        } catch (\Exception $e) {

            throw $e;

        }

    }



    /**

     * Get the name of the field according to the format

     *

     * @param string $fieldName Field name

     *

     * return string Return the field name according the format

     */

    public function getFieldNameByFormatFieldName($fieldName)

    {

        try {

            return ($this->formatFieldNameInUppercase)? strtoupper($fieldName) : strtolower($fieldName);

        } catch (\Exception $e) {

            throw $e;

        }

    }



    /**

     * Verify if doesn't exist the Task in table TASK

     *

     * @param string $taskUid Unique id of Task

     *

     * return void Throw exception if doesn't exist the Task in table TASK

     */

    public function throwExceptionIfNotExistsTask($taskUid)

    {

        $task = new \Task();



        if (!$task->taskExists($taskUid)) {

            throw new \Exception(\G::LoadTranslation("ID_ACTIVITY_DOES_NOT_EXIST", array($this->arrayParamException["taskUid"], $taskUid)));

        }

    }



    /**

     * Get all properties of an Task

     * @var string $prj_uid. Uid for Process

     * @var string $act_uid. Uid for Activity

     * @var boolean $keyCaseToLower. Flag for case lower

     *

     * return object

     */

    public function getProperties($prj_uid, $act_uid, $keyCaseToLower = false, $groupData = true)

    {

        try {

            $prj_uid = $this->validateProUid($prj_uid);

            $taskUid = $this->validateActUid($act_uid);



            //G::LoadClass("configuration");

            require_once (PATH_TRUNK . "workflow" . PATH_SEP . "engine" . PATH_SEP . "classes" . PATH_SEP . "class.configuration.php");



            $task = new \Task();

            $arrayDataAux = $task->load($taskUid);



            //$arrayDataAux["INDEX"] = 0;

            //$arrayDataAux["IFORM"] = 1;

            //$arrayDataAux["LANG"] = SYS_LANG;



            //Assignment rules

            if ($arrayDataAux["TAS_ASSIGN_TYPE"] == "SELF_SERVICE") {

                $arrayDataAux["TAS_ASSIGN_TYPE"] = (!empty($arrayDataAux["TAS_GROUP_VARIABLE"])) ? "SELF_SERVICE_EVALUATE" : $arrayDataAux["TAS_ASSIGN_TYPE"];

            }



            //Timing control

            //Load Calendar Information

            $calendar = new \Calendar();



            $calendarInfo = $calendar->getCalendarFor("", "", $taskUid);



            //If the function returns a DEFAULT calendar it means that this object doesn"t have assigned any calendar

            $arrayDataAux["TAS_CALENDAR"] = ($calendarInfo["CALENDAR_APPLIED"] != "DEFAULT")? $calendarInfo["CALENDAR_UID"] : "";



            //Notifications

            $conf = new \Configurations();

            $conf->loadConfig($x, "TAS_EXTRA_PROPERTIES", $taskUid, "", "");



            $arrayDataAux["TAS_DEF_MESSAGE_TYPE"] = "text";

            $arrayDataAux["TAS_DEF_MESSAGE_TEMPLATE"] = "alert_message.html";

            if (isset($conf->aConfig["TAS_DEF_MESSAGE_TYPE"]) && isset($conf->aConfig["TAS_DEF_MESSAGE_TYPE"])) {

                $arrayDataAux["TAS_DEF_MESSAGE_TYPE"] = $conf->aConfig["TAS_DEF_MESSAGE_TYPE"];

                $arrayDataAux["TAS_DEF_MESSAGE_TEMPLATE"] = $conf->aConfig["TAS_DEF_MESSAGE_TEMPLATE"];

            }



            //Set data

            $arrayData = array();

            $keyCase = ($keyCaseToLower) ? CASE_LOWER : CASE_UPPER;



            if (!$groupData) {

                $arrayData = array_change_key_case($arrayDataAux, $keyCase);

                return $arrayData;

            }



            //Definition

            $arrayData["DEFINITION"] = array_change_key_case(

                array(

                    "TAS_PRIORITY_VARIABLE"     => $arrayDataAux["TAS_PRIORITY_VARIABLE"],

                    "TAS_DERIVATION_SCREEN_TPL" => $arrayDataAux["TAS_DERIVATION_SCREEN_TPL"]

                ),

                $keyCase

            );



            //Assignment Rules

            $arrayData["ASSIGNMENT_RULES"] = array_change_key_case(

                array(

                    "TAS_ASSIGN_TYPE"     => $arrayDataAux["TAS_ASSIGN_TYPE"],

                    "TAS_ASSIGN_VARIABLE" => $arrayDataAux["TAS_ASSIGN_VARIABLE"],

                    "TAS_GROUP_VARIABLE"  => $arrayDataAux["TAS_GROUP_VARIABLE"],

                    "TAS_SELFSERVICE_TIMEOUT" => $arrayDataAux["TAS_SELFSERVICE_TIMEOUT"],

                    "TAS_SELFSERVICE_TIME"    => $arrayDataAux["TAS_SELFSERVICE_TIME"],

                    "TAS_SELFSERVICE_TIME_UNIT"   => $arrayDataAux["TAS_SELFSERVICE_TIME_UNIT"],

                    "TAS_SELFSERVICE_TRIGGER_UID" => $arrayDataAux["TAS_SELFSERVICE_TRIGGER_UID"],

                    "TAS_SELFSERVICE_EXECUTION"   => $arrayDataAux["TAS_SELFSERVICE_EXECUTION"]

                ),

                $keyCase

            );



            //Timing control

            $arrayData["TIMING_CONTROL"] = array_change_key_case(

                array(

                    "TAS_TRANSFER_FLY" => $arrayDataAux["TAS_TRANSFER_FLY"],

                    "TAS_DURATION"     => $arrayDataAux["TAS_DURATION"],

                    /*----------------------------------********---------------------------------*/

                    "TAS_TIMEUNIT"     => $arrayDataAux["TAS_TIMEUNIT"],

                    "TAS_TYPE_DAY"     => $arrayDataAux["TAS_TYPE_DAY"],

                    "TAS_CALENDAR"     => $arrayDataAux["TAS_CALENDAR"]

                ),

                $keyCase

            );



            //Permissions

            $arrayData["PERMISSIONS"] = array_change_key_case(

                array(

                    "TAS_TYPE" => $arrayDataAux["TAS_TYPE"]

                ),

                $keyCase

            );



            //Case Labels

            $arrayData["CASE_LABELS"] = array_change_key_case(

                array(

                    "TAS_DEF_TITLE"       => $arrayDataAux["TAS_DEF_TITLE"],

                    "TAS_DEF_DESCRIPTION" => $arrayDataAux["TAS_DEF_DESCRIPTION"]

                ),

                $keyCase

            );



            //Notifications

            $arrayData["NOTIFICATIONS"] = array_change_key_case(

                array(

                    "SEND_EMAIL"               => $arrayDataAux["TAS_SEND_LAST_EMAIL"],

                    "TAS_DEF_SUBJECT_MESSAGE"  => $arrayDataAux["TAS_DEF_SUBJECT_MESSAGE"],

                    "TAS_DEF_MESSAGE_TYPE"     => $arrayDataAux["TAS_DEF_MESSAGE_TYPE"],

                    "TAS_DEF_MESSAGE"          => $arrayDataAux["TAS_DEF_MESSAGE"],

                    "TAS_DEF_MESSAGE_TEMPLATE" => $arrayDataAux["TAS_DEF_MESSAGE_TEMPLATE"]

                ),

                $keyCase

            );



            $arrayData = array_change_key_case($arrayData, $keyCase);



            return $arrayData;

        } catch (Exception $e) {

            throw $e;

        }

    }



    /**

     * Update properties of an Task

     * @var string $prj_uid. Uid for Process

     * @var string $act_uid. Uid for Activity

     * @var array $arrayProperty. Data for properties of Activity

     *

     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>

     * @copyright Colosa - Bolivia

     *

     * return object

     */

    public function updateProperties($prj_uid, $act_uid, $arrayProperty)

    {

        //Copy of processmaker/workflow/engine/methods/tasks/tasks_Ajax.php //case "saveTaskData":

        try {

            if (isset($arrayProperty['properties'])) {

                $arrayProperty = array_change_key_case($arrayProperty['properties'], CASE_UPPER);

            }

            $prj_uid = $this->validateProUid($prj_uid);

            $act_uid = $this->validateActUid($act_uid);

            $arrayProperty["TAS_UID"] = $act_uid;

            $arrayProperty["PRO_UID"] = $prj_uid;



            $task = new \Task();

            $aTaskInfo = $task->load($arrayProperty["TAS_UID"]);

            $bpmnActivity = \BpmnActivityPeer::retrieveByPK($act_uid);



            $arrayResult = array();

            if ($arrayProperty["TAS_SELFSERVICE_TIMEOUT"] == "1") {

                if (!is_numeric($arrayProperty["TAS_SELFSERVICE_TIME"]) || $arrayProperty["TAS_SELFSERVICE_TIME"]=='') {

                    throw (new \Exception("Invalid value specified for 'tas_selfservice_time'"));

                }

            }



            foreach ($arrayProperty as $k => $v) {

                $arrayProperty[$k] = str_replace("@amp@", "&", $v);

            }



            if (isset($arrayProperty["TAS_SEND_LAST_EMAIL"])) {

                $arrayProperty["TAS_SEND_LAST_EMAIL"] = ($arrayProperty["TAS_SEND_LAST_EMAIL"] == "TRUE")? "TRUE" : "FALSE";

            } else {

                if (isset($arrayProperty["SEND_EMAIL"])) {

                    $arrayProperty["TAS_SEND_LAST_EMAIL"] = ($arrayProperty["SEND_EMAIL"] == "TRUE")? "TRUE" : "FALSE";

                } else {

                    $arrayProperty["TAS_SEND_LAST_EMAIL"] = (is_null($aTaskInfo["TAS_SEND_LAST_EMAIL"]))? "FALSE" : $aTaskInfo["TAS_SEND_LAST_EMAIL"];

                }

            }



            //Validating TAS_ASSIGN_VARIABLE value

            if (!isset($arrayProperty["TAS_ASSIGN_TYPE"])) {

                $derivateType = $task->kgetassigType($arrayProperty["PRO_UID"], $arrayProperty["TAS_UID"]);



                if (is_null($derivateType)) {

                    $arrayProperty["TAS_ASSIGN_TYPE"] = "BALANCED";

                } else {

                    $arrayProperty["TAS_ASSIGN_TYPE"] = $derivateType["TAS_ASSIGN_TYPE"];

                }

            }



            $flagTaskIsMultipleInstance = $bpmnActivity->getActType() == "TASK" && preg_match("/^(?:EMPTY|USERTASK|MANUALTASK)$/", $bpmnActivity->getActTaskType()) && $bpmnActivity->getActLoopType() == "PARALLEL";

            $flagTaskAssignTypeIsMultipleInstance = preg_match("/^(?:MULTIPLE_INSTANCE|MULTIPLE_INSTANCE_VALUE_BASED)$/", $arrayProperty["TAS_ASSIGN_TYPE"]);



            if ($flagTaskIsMultipleInstance && !$flagTaskAssignTypeIsMultipleInstance) {

                throw new \Exception(\G::LoadTranslation("ID_ACTIVITY_INVALID_ASSIGNMENT_METHOD_FOR_MULTIPLE_INSTANCE_ACTIVITY", array(strtolower("ACT_UID"), $act_uid)));

            }



            if (!$flagTaskIsMultipleInstance && $flagTaskAssignTypeIsMultipleInstance) {

                throw new \Exception(\G::LoadTranslation("ID_ACTIVITY_INVALID_ASSIGNMENT_METHOD_FOR_ACTIVITY", array(strtolower("ACT_UID"), $act_uid)));

            }



            switch ($arrayProperty["TAS_ASSIGN_TYPE"]) {

                case 'BALANCED':

                case 'MANUAL':

                case 'REPORT_TO':

                    $this->unsetVar($arrayProperty, "TAS_ASSIGN_VARIABLE");

                    $this->unsetVar($arrayProperty, "TAS_GROUP_VARIABLE");

                    $this->unsetVar($arrayProperty, "TAS_SELFSERVICE_TIMEOUT");

                    $this->unsetVar($arrayProperty, "TAS_SELFSERVICE_TIME");

                    $this->unsetVar($arrayProperty, "TAS_SELFSERVICE_TIME_UNIT");

                    $this->unsetVar($arrayProperty, "TAS_SELFSERVICE_TRIGGER_UID");

                    $this->unsetVar($arrayProperty, "TAS_SELFSERVICE_EXECUTION");

                    break;

                case 'EVALUATE':

                    if (empty($arrayProperty["TAS_ASSIGN_VARIABLE"])) {

                        throw (new \Exception("Invalid value specified for 'tas_assign_variable'"));

                    }

                    $this->unsetVar($arrayProperty, "TAS_GROUP_VARIABLE");

                    $this->unsetVar($arrayProperty, "TAS_SELFSERVICE_TIMEOUT");

                    $this->unsetVar($arrayProperty, "TAS_SELFSERVICE_TIME");

                    $this->unsetVar($arrayProperty, "TAS_SELFSERVICE_TIME_UNIT");

                    $this->unsetVar($arrayProperty, "TAS_SELFSERVICE_TRIGGER_UID");

                    $this->unsetVar($arrayProperty, "TAS_SELFSERVICE_EXECUTION");

                    break;

                case 'SELF_SERVICE':

                case 'SELF_SERVICE_EVALUATE':

                    if ($arrayProperty["TAS_ASSIGN_TYPE"] == "SELF_SERVICE_EVALUATE") {

                        if (empty($arrayProperty["TAS_GROUP_VARIABLE"])) {

                            throw (new \Exception("Invalid value specified for 'tas_group_variable'"));

                        }

                    } else {

                        $arrayProperty["TAS_GROUP_VARIABLE"] = '';

                    }

                    $arrayProperty["TAS_ASSIGN_TYPE"] = "SELF_SERVICE";

                    if (!($arrayProperty["TAS_SELFSERVICE_TIMEOUT"] == 0 || $arrayProperty["TAS_SELFSERVICE_TIMEOUT"] == 1)) {

                        throw (new \Exception("Invalid value specified for 'tas_selfservice_timeout'"));

                    }



                    if ($arrayProperty["TAS_SELFSERVICE_TIMEOUT"] == "1") {

                        if (empty($arrayProperty["TAS_SELFSERVICE_TIME"])) {

                            throw (new \Exception("Invalid value specified for 'tas_assign_variable'"));

                        }

                        if (empty($arrayProperty["TAS_SELFSERVICE_TIME_UNIT"])) {

                            throw (new \Exception("Invalid value specified for 'tas_selfservice_time_unit'"));

                        }

                        if (empty($arrayProperty["TAS_SELFSERVICE_TRIGGER_UID"])) {

                            throw (new \Exception("Invalid value specified for 'tas_selfservice_trigger_uid'"));

                        }

                        if (empty($arrayProperty["TAS_SELFSERVICE_EXECUTION"])) {

                            throw (new \Exception("Invalid value specified for 'tas_selfservice_execution'"));

                        }

                    } else {

                        $this->unsetVar($arrayProperty, "TAS_SELFSERVICE_TIME");

                        $this->unsetVar($arrayProperty, "TAS_SELFSERVICE_TIME_UNIT");

                        $this->unsetVar($arrayProperty, "TAS_SELFSERVICE_TRIGGER_UID");

                        $this->unsetVar($arrayProperty, "TAS_SELFSERVICE_EXECUTION");

                    }

                    break;

                case "MULTIPLE_INSTANCE_VALUE_BASED":

                    if (trim($arrayProperty["TAS_ASSIGN_VARIABLE"]) == "") {

                        throw new \Exception(\G::LoadTranslation("ID_INVALID_VALUE_CAN_NOT_BE_EMPTY", array(strtolower("TAS_ASSIGN_VARIABLE"))));

                    }

                    break;

            }



            //Validating TAS_TRANSFER_FLY value

            if ($arrayProperty["TAS_TRANSFER_FLY"] == "FALSE") {

                if (!isset($arrayProperty["TAS_DURATION"])) {

                    throw (new \Exception("Invalid value specified for 'tas_duration'"));

                }

                $valuesTimeUnit = array('DAYS','HOURS','MINUTES');

                if ((!isset($arrayProperty["TAS_TIMEUNIT"])) ||

                    (!in_array($arrayProperty["TAS_TIMEUNIT"], $valuesTimeUnit))) {

                    throw (new \Exception("Invalid value specified for 'tas_timeunit'"));

                }

                $valuesTypeDay = array('1','2','');

                if ((!isset($arrayProperty["TAS_TYPE_DAY"])) ||

                    (!in_array($arrayProperty["TAS_TYPE_DAY"], $valuesTypeDay))) {

                    throw (new \Exception("Invalid value specified for 'tas_type_day'"));

                }

                if (!isset($arrayProperty["TAS_CALENDAR"])) {

                    throw (new \Exception("Invalid value specified for 'tas_calendar'"));

                }

            } else {

                $this->unsetVar($arrayProperty, "TAS_DURATION");

                $this->unsetVar($arrayProperty, "TAS_TIMEUNIT");

                $this->unsetVar($arrayProperty, "TAS_TYPE_DAY");

                $this->unsetVar($arrayProperty, "TAS_CALENDAR");

            }



            if ($arrayProperty["TAS_SEND_LAST_EMAIL"] == "TRUE") {

                if (empty($arrayProperty["TAS_DEF_SUBJECT_MESSAGE"])) {

                    throw (new \Exception("Invalid value specified for 'tas_def_subject_message'"));

                }

                $valuesDefMessageType = array('template','text');

                if ((!isset($arrayProperty["TAS_DEF_MESSAGE_TYPE"])) ||

                    (!in_array($arrayProperty["TAS_DEF_MESSAGE_TYPE"], $valuesDefMessageType))) {

                    throw (new \Exception("Invalid value specified for 'tas_def_message_type'"));

                }

                if ($arrayProperty["TAS_DEF_MESSAGE_TYPE"] == 'template') {

                    if (empty($arrayProperty["TAS_DEF_MESSAGE_TEMPLATE"])) {

                        throw (new \Exception("Invalid value specified for 'tas_def_message_template'"));

                    }

                    $this->unsetVar($arrayProperty, "TAS_DEF_MESSAGE");

                } else {

                    if (empty($arrayProperty["TAS_DEF_MESSAGE"])) {

                        throw (new \Exception("Invalid value specified for 'tas_def_message'"));

                    }

                    $this->unsetVar($arrayProperty, "TAS_DEF_MESSAGE_TEMPLATE");

                }

                //Additional configuration

                if (isset($arrayProperty["TAS_DEF_MESSAGE_TYPE"])) {

                    \G::LoadClass("configuration");

                    $oConf = new \Configurations();

                    if (!isset($arrayProperty["TAS_DEF_MESSAGE_TEMPLATE"])) {

                        $arrayProperty["TAS_DEF_MESSAGE_TEMPLATE"] = "alert_message.html";

                    }

                    $oConf->aConfig = array("TAS_DEF_MESSAGE_TYPE" => $arrayProperty["TAS_DEF_MESSAGE_TYPE"], "TAS_DEF_MESSAGE_TEMPLATE" => $arrayProperty["TAS_DEF_MESSAGE_TEMPLATE"]);

                    $oConf->saveConfig("TAS_EXTRA_PROPERTIES", $arrayProperty["TAS_UID"], "", "");

                }

            } else {

                $this->unsetVar($arrayProperty, "TAS_DEF_SUBJECT_MESSAGE");

                $this->unsetVar($arrayProperty, "TAS_DEF_MESSAGE_TYPE");

                $this->unsetVar($arrayProperty, "TAS_DEF_MESSAGE");

                $this->unsetVar($arrayProperty, "TAS_DEF_MESSAGE_TEMPLATE");

            }



            $result = $task->update($arrayProperty);

            if (!empty($arrayProperty['CONSOLIDATE_DATA'])) {

                if (!empty($arrayProperty['CONSOLIDATE_DATA']['consolidated_dynaform'])) {

                    G::LoadClass("consolidatedCases");

                    $consolidated = new \ConsolidatedCases();

                    $dataConso = array(

                        'con_status'    => $arrayProperty['CONSOLIDATE_DATA']['consolidated_enable'],

                        'tas_uid'       => $arrayProperty['TAS_UID'],

                        'dyn_uid'       => $arrayProperty['CONSOLIDATE_DATA']['consolidated_dynaform'],

                        'pro_uid'       => $arrayProperty['PRO_UID'],

                        'rep_uid'       => $arrayProperty['CONSOLIDATE_DATA']['consolidated_report_table'],

                        'table_name'    => $arrayProperty['CONSOLIDATE_DATA']['consolidated_table'],

                        'title'         => $arrayProperty['CONSOLIDATE_DATA']['consolidated_title']

                    );

                    $consolidated->saveConsolidated($dataConso);

                }

            }

            $arrayResult["status"] = "OK";



            if ($result == 3) {

                $arrayResult["status"] = "CRONCL";

            }

            return $arrayResult;

        } catch (Exception $e) {

            throw $e;

        }

    }



    /**

     * Delete Activity

     * @var string $prj_uid. Uid for Process

     * @var string $act_uid. Uid for Activity

     *

     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>

     * @copyright Colosa - Bolivia

     *

     * return object

     */

    public function deleteTask($prj_uid, $act_uid)

    {

        try {

            $prj_uid = $this->validateProUid($prj_uid);

            $act_uid = $this->validateActUid($act_uid);



            G::LoadClass('tasks');

            $tasks = new \Tasks();

            $tasks->deleteTask($act_uid);

        } catch (Exception $e) {

            throw $e;

        }

    }



    /**

     * Get available Steps of a Task

     *

     * @param string $taskUid Unique id of Task

     *

     * return array Return an array with the Steps available of a Task

     */

    public function getAvailableSteps($taskUid)

    {

        try {

            $arrayAvailableStep = array();



            //Verify data

            $this->throwExceptionIfNotExistsTask($taskUid);



            //Load Task

            $task = new \Task();



            $arrayTaskData = $task->load($taskUid);



            $processUid = $arrayTaskData["PRO_UID"];



            //Get data

            //Get Uids

            $arrayUid = array();



            $task = new \Tasks();

            $arrayStep = $task->getStepsOfTask($taskUid);



            foreach ($arrayStep as $step) {

                $arrayUid[] = $step["STEP_UID_OBJ"];

            }



            //Array DB

            $arraydbStep = array();

            $delimiter = \DBAdapter::getStringDelimiter();



            //DynaForms

            $criteria = new \Criteria("workflow");



            $criteria->addSelectColumn(\DynaformPeer::DYN_UID);

            $criteria->addAsColumn("DYN_TITLE", "CT.CON_VALUE");

            $criteria->addAsColumn("DYN_DESCRIPTION", "CD.CON_VALUE");



            $criteria->addAlias("CT", \ContentPeer::TABLE_NAME);

            $criteria->addAlias("CD", \ContentPeer::TABLE_NAME);



            $arrayCondition = array();

            $arrayCondition[] = array(\DynaformPeer::DYN_UID, "CT.CON_ID", \Criteria::EQUAL);

            $arrayCondition[] = array("CT.CON_CATEGORY", $delimiter . "DYN_TITLE" . $delimiter, \Criteria::EQUAL);

            $arrayCondition[] = array("CT.CON_LANG", $delimiter . SYS_LANG . $delimiter, \Criteria::EQUAL);

            $criteria->addJoinMC($arrayCondition, \Criteria::LEFT_JOIN);



            $arrayCondition = array();

            $arrayCondition[] = array(\DynaformPeer::DYN_UID, "CD.CON_ID", \Criteria::EQUAL);

            $arrayCondition[] = array("CD.CON_CATEGORY", $delimiter . "DYN_DESCRIPTION" . $delimiter, \Criteria::EQUAL);

            $arrayCondition[] = array("CD.CON_LANG", $delimiter . SYS_LANG . $delimiter, \Criteria::EQUAL);

            $criteria->addJoinMC($arrayCondition, \Criteria::LEFT_JOIN);



            $criteria->add(\DynaformPeer::PRO_UID, $processUid, \Criteria::EQUAL);

            $criteria->add(\DynaformPeer::DYN_UID, $arrayUid, \Criteria::NOT_IN);

            $criteria->add(\DynaformPeer::DYN_TYPE, "xmlform", \Criteria::EQUAL);



            $rsCriteria = \DynaformPeer::doSelectRS($criteria);

            $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);



            while ($rsCriteria->next()) {

                $row = $rsCriteria->getRow();



                if ($row["DYN_TITLE"] . "" == "") {

                    //There is no transaltion for this Document name, try to get/regenerate the label

                    $row["DYN_TITLE"] = \Content::Load("DYN_TITLE", "", $row["DYN_UID"], SYS_LANG);

                }



                $arraydbStep[] = array(

                    $this->getFieldNameByFormatFieldName("OBJ_UID")         => $row["DYN_UID"],

                    $this->getFieldNameByFormatFieldName("OBJ_TITLE")       => $row["DYN_TITLE"],

                    $this->getFieldNameByFormatFieldName("OBJ_DESCRIPTION") => $row["DYN_DESCRIPTION"],

                    $this->getFieldNameByFormatFieldName("OBJ_TYPE")        => "DYNAFORM"

                );

            }



            //InputDocuments

            $criteria = new \Criteria("workflow");



            $criteria->addSelectColumn(\InputDocumentPeer::INP_DOC_UID);

            $criteria->addAsColumn("INP_DOC_TITLE", "CT.CON_VALUE");

            $criteria->addAsColumn("INP_DOC_DESCRIPTION", "CD.CON_VALUE");



            $criteria->addAlias("CT", \ContentPeer::TABLE_NAME);

            $criteria->addAlias("CD", \ContentPeer::TABLE_NAME);



            $arrayCondition = array();

            $arrayCondition[] = array(\InputDocumentPeer::INP_DOC_UID, "CT.CON_ID", \Criteria::EQUAL);

            $arrayCondition[] = array("CT.CON_CATEGORY", $delimiter . "INP_DOC_TITLE" . $delimiter, \Criteria::EQUAL);

            $arrayCondition[] = array("CT.CON_LANG", $delimiter . SYS_LANG . $delimiter, \Criteria::EQUAL);

            $criteria->addJoinMC($arrayCondition, \Criteria::LEFT_JOIN);



            $arrayCondition = array();

            $arrayCondition[] = array(\InputDocumentPeer::INP_DOC_UID, "CD.CON_ID", \Criteria::EQUAL);

            $arrayCondition[] = array("CD.CON_CATEGORY", $delimiter . "INP_DOC_DESCRIPTION" . $delimiter, \Criteria::EQUAL);

            $arrayCondition[] = array("CD.CON_LANG", $delimiter . SYS_LANG . $delimiter, \Criteria::EQUAL);

            $criteria->addJoinMC($arrayCondition, \Criteria::LEFT_JOIN);



            $criteria->add(\InputDocumentPeer::PRO_UID, $processUid, \Criteria::EQUAL);

            $criteria->add(\InputDocumentPeer::INP_DOC_UID, $arrayUid, \Criteria::NOT_IN);



            $rsCriteria = \InputDocumentPeer::doSelectRS($criteria);

            $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);



            while ($rsCriteria->next()) {

                $row = $rsCriteria->getRow();



                if ($row["INP_DOC_TITLE"] . "" == "") {

                    //There is no transaltion for this Document name, try to get/regenerate the label

                    $row["INP_DOC_TITLE"] = \Content::Load("INP_DOC_TITLE", "", $row["INP_DOC_UID"], SYS_LANG);

                }



                $arraydbStep[] = array(

                    $this->getFieldNameByFormatFieldName("OBJ_UID")         => $row["INP_DOC_UID"],

                    $this->getFieldNameByFormatFieldName("OBJ_TITLE")       => $row["INP_DOC_TITLE"],

                    $this->getFieldNameByFormatFieldName("OBJ_DESCRIPTION") => $row["INP_DOC_DESCRIPTION"],

                    $this->getFieldNameByFormatFieldName("OBJ_TYPE")        => "INPUT_DOCUMENT"

                );

            }



            //OutputDocuments

            $criteria = new \Criteria("workflow");



            $criteria->addSelectColumn(\OutputDocumentPeer::OUT_DOC_UID);

            $criteria->addAsColumn("OUT_DOC_TITLE", "CT.CON_VALUE");

            $criteria->addAsColumn("OUT_DOC_DESCRIPTION", "CD.CON_VALUE");



            $criteria->addAlias("CT", \ContentPeer::TABLE_NAME);

            $criteria->addAlias("CD", \ContentPeer::TABLE_NAME);



            $arrayCondition = array();

            $arrayCondition[] = array(\OutputDocumentPeer::OUT_DOC_UID, "CT.CON_ID", \Criteria::EQUAL);

            $arrayCondition[] = array("CT.CON_CATEGORY", $delimiter . "OUT_DOC_TITLE" . $delimiter, \Criteria::EQUAL);

            $arrayCondition[] = array("CT.CON_LANG", $delimiter . SYS_LANG . $delimiter, \Criteria::EQUAL);

            $criteria->addJoinMC($arrayCondition, \Criteria::LEFT_JOIN);



            $arrayCondition = array();

            $arrayCondition[] = array(\OutputDocumentPeer::OUT_DOC_UID, "CD.CON_ID", \Criteria::EQUAL);

            $arrayCondition[] = array("CD.CON_CATEGORY", $delimiter . "OUT_DOC_DESCRIPTION" . $delimiter, \Criteria::EQUAL);

            $arrayCondition[] = array("CD.CON_LANG", $delimiter . SYS_LANG . $delimiter, \Criteria::EQUAL);

            $criteria->addJoinMC($arrayCondition, \Criteria::LEFT_JOIN);



            $criteria->add(\OutputDocumentPeer::PRO_UID, $processUid, \Criteria::EQUAL);

            $criteria->add(\OutputDocumentPeer::OUT_DOC_UID, $arrayUid, \Criteria::NOT_IN);



            $rsCriteria = \OutputDocumentPeer::doSelectRS($criteria);

            $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);



            while ($rsCriteria->next()) {

                $row = $rsCriteria->getRow();



                if ($row["OUT_DOC_TITLE"] . "" == "") {

                    //There is no transaltion for this Document name, try to get/regenerate the label

                    $row["OUT_DOC_TITLE"] = \Content::Load("OUT_DOC_TITLE", "", $row["OUT_DOC_UID"], SYS_LANG);

                }



                $arraydbStep[] = array(

                    $this->getFieldNameByFormatFieldName("OBJ_UID")         => $row["OUT_DOC_UID"],

                    $this->getFieldNameByFormatFieldName("OBJ_TITLE")       => $row["OUT_DOC_TITLE"],

                    $this->getFieldNameByFormatFieldName("OBJ_DESCRIPTION") => $row["OUT_DOC_DESCRIPTION"],

                    $this->getFieldNameByFormatFieldName("OBJ_TYPE")        => "OUTPUT_DOCUMENT"

                );

            }



            //Call plugin

            $pluginRegistry = &\PMPluginRegistry::getSingleton();

            $externalSteps = $pluginRegistry->getSteps();



            if (is_array($externalSteps) && count($externalSteps) > 0) {

                foreach ($externalSteps as $key => $value) {

                    $arraydbStep[] = array(

                        $this->getFieldNameByFormatFieldName("OBJ_UID")         => $value->sStepId,

                        $this->getFieldNameByFormatFieldName("OBJ_TITLE")       => $value->sStepTitle,

                        $this->getFieldNameByFormatFieldName("OBJ_DESCRIPTION") => "",

                        $this->getFieldNameByFormatFieldName("OBJ_TYPE")        => "EXTERNAL"

                    );

                }

            }



            if (! empty($arraydbStep)) {

                $arraydbStep = Util\ArrayUtil::sort(

                    $arraydbStep,

                    array($this->getFieldNameByFormatFieldName("OBJ_TYPE"), $this->getFieldNameByFormatFieldName("OBJ_TITLE")),

                    SORT_ASC

                );

            }



            return $arraydbStep;

        } catch (\Exception $e) {

            throw $e;

        }

    }



    /**

     * Get all Steps of a Task

     *

     * @param string $taskUid Unique id of Task

     *

     * return array Return an array with all Steps of a Task

     */

    public function getSteps($taskUid)

    {

        try {

            $arrayStep = array();



            $step = new \ProcessMaker\BusinessModel\Step();

            $step->setFormatFieldNameInUppercase($this->formatFieldNameInUppercase);

            $step->setArrayParamException($this->arrayParamException);



            //Verify data

            $this->throwExceptionIfNotExistsTask($taskUid);



            //Get data

            $criteria = new \Criteria("workflow");



            $criteria->add(\StepPeer::TAS_UID, $taskUid, \Criteria::EQUAL);

            $criteria->addAscendingOrderByColumn(\StepPeer::STEP_POSITION);



            $rsCriteria = \StepPeer::doSelectRS($criteria);

            $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);



            while ($rsCriteria->next()) {

                $row = $rsCriteria->getRow();



                $arrayData = $step->getStep($row["STEP_UID"]);



                if (count($arrayData) > 0) {

                    $arrayStep[] = $arrayData;

                }

            }



            //Return

            return $arrayStep;

        } catch (\Exception $e) {

            throw $e;

        }

    }



    /**

     * Get all users of the Task

     *

     * @param string $taskUid

     * @param int    $taskUserType

     * @param bool   $keyCaseToLower

     *

     * return array

     *

     * @access public

     */

    public function getUsers($taskUid, $taskUserType, $keyCaseToLower = false)

    {

        try {

            //G::LoadClass("BasePeer");

            require_once (PATH_TRUNK . "workflow" . PATH_SEP . "engine" . PATH_SEP . "classes" . PATH_SEP . "class.BasePeer.php");



            $arrayData = array();

            $keyCase = ($keyCaseToLower)? CASE_LOWER : CASE_UPPER;



            //Criteria

            $processMap = new \ProcessMap();



            $criteria = $processMap->getTaskUsersCriteria($taskUid, $taskUserType);



            if ($criteria->getDbName() == "dbarray") {

                $rsCriteria = \ArrayBasePeer::doSelectRS($criteria);

            } else {

                $rsCriteria = \GulliverBasePeer::doSelectRS($criteria);

            }



            $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);



            while ($rsCriteria->next()) {

                $row = $rsCriteria->getRow();



                $arrayData[] = array_change_key_case($row, $keyCase);

            }



            return $arrayData;

        } catch (\Exception $e) {

            throw $e;

        }



    }



    /**

     * Return a assignee list of an activity

     *

     * @param string $sProcessUID {@min 32} {@max 32}

     * @param string $sTaskUID {@min 32} {@max 32}

     * @param string $filter

     * @param int    $start

     * @param int    $limit

     * @param string $type

     *

     * return array

     *

     * @access public

     */

    public function getTaskAssignees($sProcessUID, $sTaskUID, $filter, $start, $limit, $type)

    {

        try {

            require_once (PATH_RBAC_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "RbacUsers.php");

            require_once (PATH_TRUNK . "workflow" . PATH_SEP . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "TaskUser.php");

            require_once (PATH_TRUNK . "workflow" . PATH_SEP . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "GroupUser.php");

            Validator::proUid($sProcessUID, '$prj_uid');

            $this->validateActUid($sTaskUID);

            $aUsers = array();

            $sDelimiter = \DBAdapter::getStringDelimiter();

            $oCriteria = new \Criteria('workflow');

            $oCriteria->addAsColumn('GRP_TITLE', 'C.CON_VALUE');

            $oCriteria->addSelectColumn(\TaskUserPeer::TAS_UID);

            $oCriteria->addSelectColumn(\TaskUserPeer::USR_UID);

            $oCriteria->addSelectColumn(\TaskUserPeer::TU_TYPE);

            $oCriteria->addSelectColumn(\TaskUserPeer::TU_RELATION);

            $oCriteria->addAlias('C', 'CONTENT');

            $aConditions = array();

            $aConditions[] = array(\TaskUserPeer::USR_UID, 'C.CON_ID' );

            $aConditions[] = array('C.CON_CATEGORY', $sDelimiter . 'GRP_TITLE' . $sDelimiter );

            $aConditions[] = array('C.CON_LANG', $sDelimiter . SYS_LANG . $sDelimiter );

            $oCriteria->addJoinMC($aConditions, \Criteria::LEFT_JOIN);

            $oCriteria->add(\TaskUserPeer::TAS_UID, $sTaskUID);

            $oCriteria->add(\TaskUserPeer::TU_TYPE, 1);

            $oCriteria->add(\TaskUserPeer::TU_RELATION, 2);

            $oDataset = \TaskUserPeer::doSelectRS($oCriteria);

            $oDataset->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

            $oDataset->next();

            $c = 0;

            $oTasks = new \Tasks();

            $aAux = $oTasks->getGroupsOfTask($sTaskUID, 1);

            $aUIDS1 = array();

            foreach ($aAux as $aGroup) {

                $aUIDS1[] = $aGroup['GRP_UID'];

            }

            $criteria = new \Criteria( 'workflow' );

            $criteria->addSelectColumn( \GroupwfPeer::GRP_UID );

            $criteria->addSelectColumn( \GroupwfPeer::GRP_STATUS );

            $criteria->addSelectColumn( \GroupwfPeer::GRP_UX );

            $criteria->addAsColumn( 'GRP_TITLE', \ContentPeer::CON_VALUE );

            $criteria->addJoin( \GroupwfPeer::GRP_UID, \ContentPeer::CON_ID, \Criteria::LEFT_JOIN );

            $criteria->add( \ContentPeer::CON_CATEGORY, 'GRP_TITLE' );

            $criteria->add( \ContentPeer::CON_LANG, SYS_LANG );

            $criteria->addAscendingOrderByColumn( \ContentPeer::CON_VALUE );

            if ($filter != '') {

                $criteria->add( \ContentPeer::CON_VALUE, '%' . $filter . '%', \Criteria::LIKE );

            }

            $oDataset = \GroupwfPeer::doSelectRS( $criteria );

            $oDataset->setFetchmode( \ResultSet::FETCHMODE_ASSOC );

            $groups = array ();

            while ($oDataset->next()) {

                $groups[] = $oDataset->getRow();

            }

            $result = array ('rows' => $groups);

            foreach ($result['rows'] as $results) {

                if (in_array($results['GRP_UID'], $aUIDS1)) {

                    $c++;

                    $oCriteria = new \Criteria('workflow');

                    $oCriteria->addSelectColumn('COUNT(*) AS MEMBERS_NUMBER');

                    $oCriteria->add(\GroupUserPeer::GRP_UID, $results['GRP_UID']);

                    $oDataset2 = \GroupUserPeer::doSelectRS($oCriteria);

                    $oDataset2->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

                    $oDataset2->next();

                    $aRow2 = $oDataset2->getRow();

                    if ($type == '' || $type == 'group') {

                        $aUsers[] = array('aas_uid' => $results['GRP_UID'],

                                          'aas_name' => (!isset($aRow2['GROUP_INACTIVE']) ? $results['GRP_TITLE'] .

                                          ' (' . $aRow2['MEMBERS_NUMBER'] . ' ' .

                                          ((int) $aRow2['MEMBERS_NUMBER'] == 1 ? \G::LoadTranslation('ID_USER') : \G::LoadTranslation('ID_USERS')).

                                          ')' . '' : $results['GRP_TITLE'] . ' ' . $aRow2['GROUP_INACTIVE']),

                                          'aas_lastname' => "",

                                          'aas_username' => "",

                                          'aas_type' => "group" );

                    }

                }

            }

            $oCriteria = new \Criteria('workflow');

            $oCriteria->addSelectColumn(\UsersPeer::USR_FIRSTNAME);

            $oCriteria->addSelectColumn(\UsersPeer::USR_LASTNAME);

            $oCriteria->addSelectColumn(\UsersPeer::USR_USERNAME);

            $oCriteria->addSelectColumn(\UsersPeer::USR_EMAIL);

            if ($filter != '') {

                $oCriteria->add( $oCriteria->getNewCriterion( \UsersPeer::USR_USERNAME, "%$filter%", \Criteria::LIKE )->addOr( $oCriteria->getNewCriterion( \UsersPeer::USR_FIRSTNAME, "%$filter%", \Criteria::LIKE ) )->addOr( $oCriteria->getNewCriterion( \UsersPeer::USR_LASTNAME, "%$filter%", \Criteria::LIKE ) ) );

            }

            $oCriteria->addSelectColumn(\TaskUserPeer::TAS_UID);

            $oCriteria->addSelectColumn(\TaskUserPeer::USR_UID);

            $oCriteria->addSelectColumn(\TaskUserPeer::TU_TYPE);

            $oCriteria->addSelectColumn(\TaskUserPeer::TU_RELATION);

            $oCriteria->addJoin(\TaskUserPeer::USR_UID, \UsersPeer::USR_UID, \Criteria::LEFT_JOIN);

            $oCriteria->add(\TaskUserPeer::TAS_UID, $sTaskUID);

            $oCriteria->add(\TaskUserPeer::TU_TYPE, 1);

            $oCriteria->add(\TaskUserPeer::TU_RELATION, 1);

            $oDataset = \TaskUserPeer::doSelectRS($oCriteria);

            $oDataset->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

            $oDataset->next();

            while ($aRow = $oDataset->getRow()) {

                if ($type == '' || $type == 'user') {

                    $aUsers[] = array('aas_uid' => $aRow['USR_UID'],

                                      'aas_name' => $aRow['USR_FIRSTNAME'],

                                      'aas_lastname' => $aRow['USR_LASTNAME'],

                                      'aas_username' => $aRow['USR_USERNAME'],

                                      'aas_type' => "user" );

                }

                $oDataset->next();

            }

            if ($start) {

                if ($start < 0) {

                    throw (new \Exception( 'Invalid value specified for start.'));

                }

            } else {

                $start = 0;

            }

            if (isset($limit)) {

                if ($limit < 0) {

                    throw (new \Exception( 'Invalid value specified for limit.'));

                } else {

                    if ($limit == 0) {

                        return array();

                    }

                }

            } else {

                $limit = count($aUsers) + 1;

            }

            $aUsers = $this->arrayPagination($aUsers, $start, $limit);

            return $aUsers;

        } catch (\Exception $e) {

            throw $e;

        }

    }



    /**

     * Return the available users and users groups to assigned to an activity

     *

     * @param string $sProcessUID {@min 32} {@max 32}

     * @param string $sTaskUID {@min 32} {@max 32}

     * @param string $filter

     * @param int    $start

     * @param int    $limit

     * @param string $type

     *

     * return array

     *

     * @access public

     */

    public function getTaskAvailableAssignee($sProcessUID, $sTaskUID, $filter, $start, $limit, $type)

    {

        try {

            require_once (PATH_RBAC_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "RbacUsers.php");

            require_once (PATH_TRUNK . "workflow" . PATH_SEP . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "TaskUser.php");

            require_once (PATH_TRUNK . "workflow" . PATH_SEP . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "GroupUser.php");

            Validator::proUid($sProcessUID, '$prj_uid');

            $this->validateActUid($sTaskUID);

            $iType = 1;

            $oTasks = new \Tasks();

            $aAux = $oTasks->getGroupsOfTask($sTaskUID, $iType);

            $aUIDS1 = array();

            $aUIDS2 = array();

            foreach ($aAux as $aGroup) {

                $aUIDS1[] = $aGroup['GRP_UID'];

            }

            $aAux = $oTasks->getUsersOfTask($sTaskUID, $iType);

            foreach ($aAux as $aUser) {

                $aUIDS2[] = $aUser['USR_UID'];

            }

            $aUsers = array();

            $c = 0;

            $oTasks = new \Tasks();

            $aAux = $oTasks->getGroupsOfTask($sTaskUID, 1);

            $aUIDS1 = array();

            foreach ($aAux as $aGroup) {

                $aUIDS1[] = $aGroup['GRP_UID'];

            }

            $criteria = new \Criteria( 'workflow' );

            $criteria->addSelectColumn( \GroupwfPeer::GRP_UID );

            $criteria->addSelectColumn( \GroupwfPeer::GRP_STATUS );

            $criteria->addSelectColumn( \GroupwfPeer::GRP_UX );

            $criteria->addAsColumn( 'GRP_TITLE', \ContentPeer::CON_VALUE );

            $criteria->addJoin( \GroupwfPeer::GRP_UID, \ContentPeer::CON_ID, \Criteria::LEFT_JOIN );

            $criteria->add(\GroupwfPeer::GRP_STATUS, "ACTIVE", \Criteria::EQUAL);

            $criteria->add( \ContentPeer::CON_CATEGORY, 'GRP_TITLE' );

            $criteria->add( \ContentPeer::CON_LANG, SYS_LANG );

            $criteria->addAscendingOrderByColumn( \ContentPeer::CON_VALUE );

            if ($filter != '') {

                $criteria->add( \ContentPeer::CON_VALUE, '%' . $filter . '%', \Criteria::LIKE );

            }

            $oDataset = \GroupwfPeer::doSelectRS( $criteria );

            $oDataset->setFetchmode( \ResultSet::FETCHMODE_ASSOC );

            $groups = array ();

            while ($oDataset->next()) {

                $groups[] = $oDataset->getRow();

            }

            $result = array ('rows' => $groups);

            foreach ($result['rows'] as $results) {

                if (! in_array($results['GRP_UID'], $aUIDS1)) {

                    $c++;

                    $oCriteria = new \Criteria('workflow');

                    $oCriteria->addSelectColumn('COUNT(*) AS MEMBERS_NUMBER');

                    $oCriteria->add(\GroupUserPeer::GRP_UID, $results['GRP_UID']);

                    $oDataset2 = \GroupUserPeer::doSelectRS($oCriteria);

                    $oDataset2->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

                    $oDataset2->next();

                    $aRow2 = $oDataset2->getRow();

                    if ($type == '' || $type == 'group') {

                        $aUsers[] = array('aas_uid' => $results['GRP_UID'],

                                          'aas_name' => (!isset($aRow2['GROUP_INACTIVE']) ? $results['GRP_TITLE'] .

                                          ' (' . $aRow2['MEMBERS_NUMBER'] . ' ' .

                                          ((int) $aRow2['MEMBERS_NUMBER'] == 1 ? \G::LoadTranslation('ID_USER') : \G::LoadTranslation('ID_USERS')).

                                          ')' . '' : $results['GRP_TITLE'] . ' ' . $aRow2['GROUP_INACTIVE']),

                                          'aas_lastname' => "",

                                          'aas_username' => "",

                                          'aas_type' => "group" );

                    }

                }

            }

            $oCriteria = new \Criteria('workflow');

            $oCriteria->addSelectColumn(\UsersPeer::USR_UID);

            $oCriteria->addSelectColumn(\UsersPeer::USR_USERNAME);

            $oCriteria->addSelectColumn(\UsersPeer::USR_FIRSTNAME);

            $oCriteria->addSelectColumn(\UsersPeer::USR_LASTNAME);

            $oCriteria->addSelectColumn(\UsersPeer::USR_EMAIL);

            if ($filter != '') {

                 $oCriteria->add( $oCriteria->getNewCriterion( \UsersPeer::USR_USERNAME, "%$filter%", \Criteria::LIKE )->addOr( $oCriteria->getNewCriterion( \UsersPeer::USR_FIRSTNAME, "%$filter%", \Criteria::LIKE ) )->addOr( $oCriteria->getNewCriterion( \UsersPeer::USR_LASTNAME, "%$filter%", \Criteria::LIKE ) ) );

            }

            $oCriteria->add(\UsersPeer::USR_STATUS, 'ACTIVE');

            $oCriteria->add(\UsersPeer::USR_UID, $aUIDS2, \Criteria::NOT_IN);

            $oDataset = \UsersPeer::doSelectRS($oCriteria);

            $oDataset->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

            $oDataset->next();

            while ($aRow = $oDataset->getRow()) {

                if ($type == '' || $type == 'user') {

                    $aUsers[] = array('aas_uid' => $aRow['USR_UID'],

                                      'aas_name' => $aRow['USR_FIRSTNAME'],

                                      'aas_lastname' => $aRow['USR_LASTNAME'],

                                      'aas_username' => $aRow['USR_USERNAME'],

                                      'aas_type' => "user" );

                }

                $oDataset->next();

            }

            if ($start) {

                if ($start < 0) {

                    throw new \Exception(\G::LoadTranslation("ID_INVALID_START"));

                }

            } else {

                $start = 0;

            }

            if (isset($limit)) {

                if ($limit < 0) {

                    throw new \Exception(\G::LoadTranslation("ID_INVALID_LIMIT"));

                } else {

                    if ($limit == 0) {

                        return array();

                    }

                }

            } else {

                $limit = count($aUsers) + 1;

            }

            $aUsers = $this->arrayPagination($aUsers, $start, $limit);

            return $aUsers;

        } catch (\Exception $e) {

            throw $e;

        }

    }



    /**

     * Return a single user or group assigned to an activity

     *

     * @param string $sProcessUID {@min 32} {@max 32}

     * @param string $sTaskUID {@min 32} {@max 32}

     * @param string $sAssigneeUID {@min 32} {@max 32}

     *

     * return array

     *

     * @access public

     */

    public function getTaskAssignee($sProcessUID, $sTaskUID, $sAssigneeUID)

    {

        try {

            Validator::proUid($sProcessUID, '$prj_uid');

            $this->validateActUid($sTaskUID);

            $iType = 1;

            $aUsers = array();

            $sDelimiter = \DBAdapter::getStringDelimiter();

            $oCriteria = new \Criteria('workflow');

            $oCriteria->addAsColumn('GRP_TITLE', 'C.CON_VALUE');

            $oCriteria->addSelectColumn(\TaskUserPeer::TAS_UID);

            $oCriteria->addSelectColumn(\TaskUserPeer::USR_UID);

            $oCriteria->addSelectColumn(\TaskUserPeer::TU_TYPE);

            $oCriteria->addSelectColumn(\TaskUserPeer::TU_RELATION);

            $oCriteria->addAlias('C', 'CONTENT');

            $aConditions = array();

            $aConditions[] = array(\TaskUserPeer::USR_UID, 'C.CON_ID' );

            $aConditions[] = array('C.CON_CATEGORY', $sDelimiter . 'GRP_TITLE' . $sDelimiter );

            $aConditions[] = array('C.CON_LANG', $sDelimiter . SYS_LANG . $sDelimiter );

            $oCriteria->addJoinMC($aConditions, \Criteria::LEFT_JOIN);

            $oCriteria->add(\TaskUserPeer::USR_UID, $sAssigneeUID);

            $oCriteria->add(\TaskUserPeer::TAS_UID, $sTaskUID);

            $oCriteria->add(\TaskUserPeer::TU_TYPE, $iType);

            $oCriteria->add(\TaskUserPeer::TU_RELATION, 2);

            $oDataset = \TaskUserPeer::doSelectRS($oCriteria);

            $oDataset->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

            $oDataset->next();

            $c = 0;

            $oTasks = new \Tasks();

            $aAux = $oTasks->getGroupsOfTask($sTaskUID, 1);

            $aUIDS1 = array();

            foreach ($aAux as $aGroup) {

                $aUIDS1[] = $aGroup['GRP_UID'];

            }

            $criteria = new \Criteria( 'workflow' );

            $criteria->addSelectColumn( \GroupwfPeer::GRP_UID );

            $criteria->addSelectColumn( \GroupwfPeer::GRP_STATUS );

            $criteria->addSelectColumn( \GroupwfPeer::GRP_UX );

            $criteria->addAsColumn( 'GRP_TITLE', \ContentPeer::CON_VALUE );

            $criteria->addJoin( \GroupwfPeer::GRP_UID, \ContentPeer::CON_ID, \Criteria::LEFT_JOIN );

            $criteria->add( \ContentPeer::CON_CATEGORY, 'GRP_TITLE' );

            $criteria->add( \ContentPeer::CON_LANG, SYS_LANG );

            $criteria->add( \GroupwfPeer::GRP_UID, $sAssigneeUID);

            $criteria->addAscendingOrderByColumn( \ContentPeer::CON_VALUE );

            $oDataset = \GroupwfPeer::doSelectRS( $criteria );

            $oDataset->setFetchmode( \ResultSet::FETCHMODE_ASSOC );

            $groups = array ();

            while ($oDataset->next()) {

                $groups[] = $oDataset->getRow();

            }

            $result = array ('rows' => $groups);

            foreach ($result['rows'] as $results) {

                if (in_array($results['GRP_UID'], $aUIDS1)) {

                    $c++;

                    $oCriteria = new \Criteria('workflow');

                    $oCriteria->addSelectColumn('COUNT(*) AS MEMBERS_NUMBER');

                    $oCriteria->add(\GroupUserPeer::GRP_UID, $results['GRP_UID']);

                    $oDataset2 = \GroupUserPeer::doSelectRS($oCriteria);

                    $oDataset2->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

                    $oDataset2->next();

                    $aRow2 = $oDataset2->getRow();

                    $aUsers = array('aas_uid' => $results['GRP_UID'],

                                    'aas_name' => (!isset($aRow2['GROUP_INACTIVE']) ? $results['GRP_TITLE'] .

                                    ' (' . $aRow2['MEMBERS_NUMBER'] . ' ' .

                                    ((int) $aRow2['MEMBERS_NUMBER'] == 1 ? \G::LoadTranslation('ID_USER') : \G::LoadTranslation('ID_USERS')).

                                    ')' . '' : $results['GRP_TITLE'] . ' ' . $aRow2['GROUP_INACTIVE']),

                                    'aas_lastname' => "",

                                    'aas_username' => "",

                                    'aas_type' => "group" );

                }

            }

            $oCriteria = new \Criteria('workflow');

            $oCriteria->addSelectColumn(\UsersPeer::USR_FIRSTNAME);

            $oCriteria->addSelectColumn(\UsersPeer::USR_LASTNAME);

            $oCriteria->addSelectColumn(\UsersPeer::USR_USERNAME);

            $oCriteria->addSelectColumn(\UsersPeer::USR_EMAIL);

            $oCriteria->addSelectColumn(\TaskUserPeer::TAS_UID);

            $oCriteria->addSelectColumn(\TaskUserPeer::USR_UID);

            $oCriteria->addSelectColumn(\TaskUserPeer::TU_TYPE);

            $oCriteria->addSelectColumn(\TaskUserPeer::TU_RELATION);

            $oCriteria->addJoin(\TaskUserPeer::USR_UID, \UsersPeer::USR_UID, \Criteria::LEFT_JOIN);

            $oCriteria->add(\TaskUserPeer::USR_UID, $sAssigneeUID);

            $oCriteria->add(\TaskUserPeer::TAS_UID, $sTaskUID);

            $oCriteria->add(\TaskUserPeer::TU_TYPE, $iType);

            $oCriteria->add(\TaskUserPeer::TU_RELATION, 1);

            $oDataset = \TaskUserPeer::doSelectRS($oCriteria);

            $oDataset->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

            $oDataset->next();

            while ($aRow = $oDataset->getRow()) {

                $aUsers = array('aas_uid' => $aRow['USR_UID'],

                                'aas_name' => $aRow['USR_FIRSTNAME'],

                                'aas_lastname' => $aRow['USR_LASTNAME'],

                                'aas_username' => $aRow['USR_USERNAME'],

                                'aas_type' => "user" );

                $oDataset->next();

            }

            if (empty($aUsers)) {

                throw new \Exception(\G::LoadTranslation("ID_RECORD_NOT_FOUND", array($sAssigneeUID)));

            } else {

                return $aUsers;

            }

        } catch (\Exception $e) {

            throw $e;

        }

    }



    /**

     * Assign a user or group to an activity

     *

     * @param string $sProcessUID {@min 32} {@max 32}

     * @param string $sTaskUID {@min 32} {@max 32}

     * @param string $sAssigneeUID {@min 32} {@max 32}

     * @param string $assType {@choice user,group}

     *

     * return array

     *

     * @access public

     */

    public function addTaskAssignee($sProcessUID, $sTaskUID, $sAssigneeUID, $assType)

    {

        try {

            Validator::proUid($sProcessUID, '$prj_uid');

            $this->validateActUid($sTaskUID);

            $iType = 1;

            $iRelation = '';

            $oCriteria = new \Criteria('workflow');

            $oCriteria->addSelectColumn( \TaskUserPeer::TU_RELATION );

            $oCriteria->add(\TaskUserPeer::USR_UID, $sAssigneeUID );

            $oCriteria->add(\TaskUserPeer::TAS_UID, $sTaskUID );

            $oCriteria->add(\TaskUserPeer::TU_TYPE, $iType );

            $oTaskUser = \TaskUserPeer::doSelectRS( $oCriteria );

            $oTaskUser->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

            while ($oTaskUser->next()) {

                $aRow = $oTaskUser->getRow();

                $iRelation = $aRow['TU_RELATION'];

            }

            $oTaskUser = \TaskUserPeer::retrieveByPK( $sTaskUID, $sAssigneeUID, $iType, $iRelation );

            if (! is_null( $oTaskUser )) {

                throw new \Exception(\G::LoadTranslation("ID_ALREADY_ASSIGNED", array($sAssigneeUID, $sTaskUID)));

            } else {

                $oTypeAssigneeG = \GroupwfPeer::retrieveByPK( $sAssigneeUID );

                $oTypeAssigneeU = \UsersPeer::retrieveByPK( $sAssigneeUID );

                if (is_null( $oTypeAssigneeG ) && is_null( $oTypeAssigneeU ) ) {

                    throw new \Exception(\G::LoadTranslation("ID_DOES_NOT_CORRESPOND", array($sAssigneeUID, $assType)));

                }

                if (is_null( $oTypeAssigneeG ) && ! is_null( $oTypeAssigneeU) ) {

                    $type = "user";

                    if ( $type != $assType ) {

                        throw new \Exception(\G::LoadTranslation("ID_DOES_NOT_CORRESPOND", array($sAssigneeUID, $assType)));

                    }

                }

                if (! is_null( $oTypeAssigneeG ) && is_null( $oTypeAssigneeU ) ) {

                    $type = "group";

                    if ( $type != $assType ) {

                        throw new \Exception(\G::LoadTranslation("ID_DOES_NOT_CORRESPOND", array($sAssigneeUID, $assType)));

                    }

                }

                $oTaskUser = new \TaskUser();

                if ( $assType == "user" ) {

                    $oTaskUser->create(array('TAS_UID' => $sTaskUID,

                                             'USR_UID' => $sAssigneeUID,

                                             'TU_TYPE' => $iType,

                                             'TU_RELATION' => 1));

                } else {

                    $oTaskUser->create(array('TAS_UID' => $sTaskUID,

                                             'USR_UID' => $sAssigneeUID,

                                             'TU_TYPE' => $iType,

                                             'TU_RELATION' => 2));

                }

            }

        } catch ( \Exception $e ) {

            throw $e;

        }

    }



    /**

     * Remove a assignee of an activity

     *

     * @param string $sProcessUID {@min 32} {@max 32}

     * @param string $sTaskUID {@min 32} {@max 32}

     * @param string $sAssigneeUID {@min 32} {@max 32}

     *

     * @access public

     */

    public function removeTaskAssignee($sProcessUID, $sTaskUID, $sAssigneeUID)

    {

        try {

            Validator::proUid($sProcessUID, '$prj_uid');

            $this->validateActUid($sTaskUID);

            $iType = 1;

            $iRelation = '';

            $oCriteria = new \Criteria('workflow');

            $oCriteria->addSelectColumn( \TaskUserPeer::TU_RELATION );

            $oCriteria->add(\TaskUserPeer::USR_UID, $sAssigneeUID);

            $oCriteria->add(\TaskUserPeer::TAS_UID, $sTaskUID);

            $oCriteria->add(\TaskUserPeer::TU_TYPE, $iType);

            $oTaskUser = \TaskUserPeer::doSelectRS($oCriteria);

            $oTaskUser->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

            while ($oTaskUser->next()) {

                $aRow = $oTaskUser->getRow();

                $iRelation = $aRow['TU_RELATION'];

            }

            $oTaskUser = \TaskUserPeer::retrieveByPK($sTaskUID, $sAssigneeUID, $iType, $iRelation);

            if (! is_null( $oTaskUser )) {

                \TaskUserPeer::doDelete($oCriteria);

            } else {

                throw new \Exception(\G::LoadTranslation("ID_ROW_DOES_NOT_EXIST"));

            }

        } catch (\Exception $e) {

            throw $e;

        }

    }



    /**

     * Return a adhoc assignee list of an activity

     *

     * @param string $sProcessUID {@min 32} {@max 32}

     * @param string $sTaskUID {@min 32} {@max 32}

     * @param string $filter

     * @param int    $start

     * @param int    $limit

     * @param string $type

     *

     * return array

     *

     * @access public

     */

    public function getTaskAdhocAssignees($sProcessUID, $sTaskUID, $filter, $start, $limit, $type)

    {

        try {

            require_once (PATH_RBAC_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "RbacUsers.php");

            require_once (PATH_TRUNK . "workflow" . PATH_SEP . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "TaskUser.php");

            require_once (PATH_TRUNK . "workflow" . PATH_SEP . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "GroupUser.php");

            Validator::proUid($sProcessUID, '$prj_uid');

            $this->validateActUid($sTaskUID);

            $aUsers = array();

            $sDelimiter = \DBAdapter::getStringDelimiter();

            $oCriteria = new \Criteria('workflow');

            $oCriteria->addAsColumn('GRP_TITLE', 'C.CON_VALUE');

            $oCriteria->addSelectColumn(\TaskUserPeer::TAS_UID);

            $oCriteria->addSelectColumn(\TaskUserPeer::USR_UID);

            $oCriteria->addSelectColumn(\TaskUserPeer::TU_TYPE);

            $oCriteria->addSelectColumn(\TaskUserPeer::TU_RELATION);

            $oCriteria->addAlias('C', 'CONTENT');

            $aConditions = array();

            $aConditions[] = array(\TaskUserPeer::USR_UID, 'C.CON_ID' );

            $aConditions[] = array('C.CON_CATEGORY', $sDelimiter . 'GRP_TITLE' . $sDelimiter );

            $aConditions[] = array('C.CON_LANG', $sDelimiter . SYS_LANG . $sDelimiter );

            $oCriteria->addJoinMC($aConditions, \Criteria::LEFT_JOIN);

            $oCriteria->add(\TaskUserPeer::TAS_UID, $sTaskUID);

            $oCriteria->add(\TaskUserPeer::TU_TYPE, 2);

            $oCriteria->add(\TaskUserPeer::TU_RELATION, 2);

            $oDataset = \TaskUserPeer::doSelectRS($oCriteria);

            $oDataset->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

            $oDataset->next();

            $c = 0;

            $oTasks = new \Tasks();

            $aAux = $oTasks->getGroupsOfTask($sTaskUID, 2);

            $aUIDS1 = array();

            foreach ($aAux as $aGroup) {

                $aUIDS1[] = $aGroup['GRP_UID'];

            }

            $criteria = new \Criteria( 'workflow' );

            $criteria->addSelectColumn( \GroupwfPeer::GRP_UID );

            $criteria->addSelectColumn( \GroupwfPeer::GRP_STATUS );

            $criteria->addSelectColumn( \GroupwfPeer::GRP_UX );

            $criteria->addAsColumn( 'GRP_TITLE', \ContentPeer::CON_VALUE );

            $criteria->addJoin( \GroupwfPeer::GRP_UID, \ContentPeer::CON_ID, \Criteria::LEFT_JOIN );

            $criteria->add( \ContentPeer::CON_CATEGORY, 'GRP_TITLE' );

            $criteria->add( \ContentPeer::CON_LANG, SYS_LANG );

            $criteria->addAscendingOrderByColumn( \ContentPeer::CON_VALUE );

            if ($filter != '') {

                $criteria->add( \ContentPeer::CON_VALUE, '%' . $filter . '%', \Criteria::LIKE );

            }

            $oDataset = \GroupwfPeer::doSelectRS( $criteria );

            $oDataset->setFetchmode( \ResultSet::FETCHMODE_ASSOC );

            $groups = array ();

            while ($oDataset->next()) {

                $groups[] = $oDataset->getRow();

            }

            $result = array ('rows' => $groups);

            foreach ($result['rows'] as $results) {

                if (in_array($results['GRP_UID'], $aUIDS1)) {

                    $c++;

                    $oCriteria = new \Criteria('workflow');

                    $oCriteria->addSelectColumn('COUNT(*) AS MEMBERS_NUMBER');

                    $oCriteria->add(\GroupUserPeer::GRP_UID, $results['GRP_UID']);

                    $oDataset2 = \GroupUserPeer::doSelectRS($oCriteria);

                    $oDataset2->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

                    $oDataset2->next();

                    $aRow2 = $oDataset2->getRow();

                    if ($type == '' || $type == 'group') {

                        $aUsers[] = array('ada_uid' => $results['GRP_UID'],

                                          'ada_name' => (!isset($aRow2['GROUP_INACTIVE']) ? $results['GRP_TITLE'] .

                                          ' (' . $aRow2['MEMBERS_NUMBER'] . ' ' .

                                          ((int) $aRow2['MEMBERS_NUMBER'] == 1 ? \G::LoadTranslation('ID_USER') : \G::LoadTranslation('ID_USERS')).

                                          ')' . '' : $results['GRP_TITLE'] . ' ' . $aRow2['GROUP_INACTIVE']),

                                          'ada_lastname' => "",

                                          'ada_username' => "",

                                          'ada_type' => "group" );

                    }

                }

            }

            $oCriteria = new \Criteria('workflow');

            $oCriteria->addSelectColumn(\UsersPeer::USR_FIRSTNAME);

            $oCriteria->addSelectColumn(\UsersPeer::USR_LASTNAME);

            $oCriteria->addSelectColumn(\UsersPeer::USR_USERNAME);

            $oCriteria->addSelectColumn(\UsersPeer::USR_EMAIL);

            if ($filter != '') {

                 $oCriteria->add( $oCriteria->getNewCriterion( \UsersPeer::USR_USERNAME, "%$filter%", \Criteria::LIKE )->addOr( $oCriteria->getNewCriterion( \UsersPeer::USR_FIRSTNAME, "%$filter%", \Criteria::LIKE ) )->addOr( $oCriteria->getNewCriterion( \UsersPeer::USR_LASTNAME, "%$filter%", \Criteria::LIKE ) ) );

            }

            $oCriteria->addSelectColumn(\TaskUserPeer::TAS_UID);

            $oCriteria->addSelectColumn(\TaskUserPeer::USR_UID);

            $oCriteria->addSelectColumn(\TaskUserPeer::TU_TYPE);

            $oCriteria->addSelectColumn(\TaskUserPeer::TU_RELATION);

            $oCriteria->addJoin(\TaskUserPeer::USR_UID, \UsersPeer::USR_UID, \Criteria::LEFT_JOIN);

            $oCriteria->add(\TaskUserPeer::TAS_UID, $sTaskUID);

            $oCriteria->add(\TaskUserPeer::TU_TYPE, 2);

            $oCriteria->add(\TaskUserPeer::TU_RELATION, 1);

            $oDataset = \TaskUserPeer::doSelectRS($oCriteria);

            $oDataset->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

            $oDataset->next();

            while ($aRow = $oDataset->getRow()) {

                if ($type == '' || $type == 'user') {

                    $aUsers[] = array('ada_uid' => $aRow['USR_UID'],

                                      'ada_name' => $aRow['USR_FIRSTNAME'],

                                      'ada_lastname' => $aRow['USR_LASTNAME'],

                                      'ada_username' => $aRow['USR_USERNAME'],

                                      'ada_type' => "user" );

                }

                $oDataset->next();

            }

            if ($start) {

                if ($start < 0) {

                    throw new \Exception(\G::LoadTranslation("ID_INVALID_START"));

                }

            } else {

                $start = 0;

            }

            if (isset($limit)) {

                if ($limit < 0) {

                    throw new \Exception(\G::LoadTranslation("ID_INVALID_LIMIT"));

                } else {

                    if ($limit == 0) {

                        return array();

                    }

                }

            } else {

                $limit = count($aUsers) + 1;

            }

            $aUsers = $this->arrayPagination($aUsers, $start, $limit);

            return $aUsers;

        } catch (\Exception $e) {

            throw $e;

        }

    }



    /**

     * Return the available adhoc users and users groups to assigned to an activity

     *

     * @param string $sProcessUID {@min 32} {@max 32}

     * @param string $sTaskUID {@min 32} {@max 32}

     * @param string $filter

     * @param int    $start

     * @param int    $limit

     * @param string $type

     *

     * return array

     *

     * @access public

     */

    public function getTaskAvailableAdhocAssignee($sProcessUID, $sTaskUID, $filter, $start, $limit, $type)

    {

        try {

            require_once (PATH_RBAC_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "RbacUsers.php");

            require_once (PATH_TRUNK . "workflow" . PATH_SEP . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "TaskUser.php");

            require_once (PATH_TRUNK . "workflow" . PATH_SEP . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "GroupUser.php");

            Validator::proUid($sProcessUID, '$prj_uid');

            $this->validateActUid($sTaskUID);

            $iType = 2;

            $oTasks = new \Tasks();

            $aAux = $oTasks->getGroupsOfTask($sTaskUID, $iType);

            $aUIDS1 = array();

            $aUIDS2 = array();

            foreach ($aAux as $aGroup) {

                $aUIDS1[] = $aGroup['GRP_UID'];

            }

            $aAux = $oTasks->getUsersOfTask($sTaskUID, $iType);

            foreach ($aAux as $aUser) {

                $aUIDS2[] = $aUser['USR_UID'];

            }

            $aUsers = array();

            $c = 0;

            $oTasks = new \Tasks();

            $aAux = $oTasks->getGroupsOfTask($sTaskUID, 2);

            $aUIDS1 = array();

            foreach ($aAux as $aGroup) {

                $aUIDS1[] = $aGroup['GRP_UID'];

            }

            $criteria = new \Criteria( 'workflow' );

            $criteria->addSelectColumn( \GroupwfPeer::GRP_UID );

            $criteria->addSelectColumn( \GroupwfPeer::GRP_STATUS );

            $criteria->addSelectColumn( \GroupwfPeer::GRP_UX );

            $criteria->add(\GroupwfPeer::GRP_STATUS, "ACTIVE", \Criteria::EQUAL);

            $criteria->addAsColumn( 'GRP_TITLE', \ContentPeer::CON_VALUE );

            $criteria->addJoin( \GroupwfPeer::GRP_UID, \ContentPeer::CON_ID, \Criteria::LEFT_JOIN );

            $criteria->add( \ContentPeer::CON_CATEGORY, 'GRP_TITLE' );

            $criteria->add( \ContentPeer::CON_LANG, SYS_LANG );

            $criteria->addAscendingOrderByColumn( \ContentPeer::CON_VALUE );

            if ($filter != '') {

                $criteria->add( \ContentPeer::CON_VALUE, '%' . $filter . '%', \Criteria::LIKE );

            }

            $oDataset = \GroupwfPeer::doSelectRS( $criteria );

            $oDataset->setFetchmode( \ResultSet::FETCHMODE_ASSOC );

            $groups = array ();

            while ($oDataset->next()) {

                $groups[] = $oDataset->getRow();

            }

            $result = array ('rows' => $groups);

            foreach ($result['rows'] as $results) {

                if (! in_array($results['GRP_UID'], $aUIDS1)) {

                    $c++;

                    $oCriteria = new \Criteria('workflow');

                    $oCriteria->addSelectColumn('COUNT(*) AS MEMBERS_NUMBER');

                    $oCriteria->add(\GroupUserPeer::GRP_UID, $results['GRP_UID']);

                    $oDataset2 = \GroupUserPeer::doSelectRS($oCriteria);

                    $oDataset2->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

                    $oDataset2->next();

                    $aRow2 = $oDataset2->getRow();

                    if ($type == '' || $type == 'group') {

                        $aUsers[] = array('ada_uid' => $results['GRP_UID'],

                                          'ada_name' => (!isset($aRow2['GROUP_INACTIVE']) ? $results['GRP_TITLE'] .

                                          ' (' . $aRow2['MEMBERS_NUMBER'] . ' ' .

                                          ((int) $aRow2['MEMBERS_NUMBER'] == 1 ? \G::LoadTranslation('ID_USER') : \G::LoadTranslation('ID_USERS')).

                                          ')' . '' : $results['GRP_TITLE'] . ' ' . $aRow2['GROUP_INACTIVE']),

                                          'ada_lastname' => "",

                                          'ada_username' => "",

                                          'ada_type' => "group" );

                    }

                }

            }

            $oCriteria = new \Criteria('workflow');

            $oCriteria->addSelectColumn(\UsersPeer::USR_UID);

            $oCriteria->addSelectColumn(\UsersPeer::USR_USERNAME);

            $oCriteria->addSelectColumn(\UsersPeer::USR_FIRSTNAME);

            $oCriteria->addSelectColumn(\UsersPeer::USR_LASTNAME);

            $oCriteria->addSelectColumn(\UsersPeer::USR_EMAIL);

            if ($filter != '') {

                 $oCriteria->add( $oCriteria->getNewCriterion( \UsersPeer::USR_USERNAME, "%$filter%", \Criteria::LIKE )->addOr( $oCriteria->getNewCriterion( \UsersPeer::USR_FIRSTNAME, "%$filter%", \Criteria::LIKE ) )->addOr( $oCriteria->getNewCriterion( \UsersPeer::USR_LASTNAME, "%$filter%", \Criteria::LIKE ) ) );

            }

            $oCriteria->add(\UsersPeer::USR_STATUS, 'ACTIVE');

            $oCriteria->add(\UsersPeer::USR_UID, $aUIDS2, \Criteria::NOT_IN);

            $oDataset = \UsersPeer::doSelectRS($oCriteria);

            $oDataset->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

            $oDataset->next();

            while ($aRow = $oDataset->getRow()) {

                if ($type == '' || $type == 'user') {

                    $aUsers[] = array('ada_uid' => $aRow['USR_UID'],

                                      'ada_name' => $aRow['USR_FIRSTNAME'],

                                      'ada_lastname' => $aRow['USR_LASTNAME'],

                                      'ada_username' => $aRow['USR_USERNAME'],

                                      'ada_type' => "user" );

                }

                $oDataset->next();

            }

            if ($start) {

                if ($start < 0) {

                    throw new \Exception(\G::LoadTranslation("ID_INVALID_START"));

                }

            } else {

                $start = 0;

            }

            if (isset($limit)) {

                if ($limit < 0) {

                    throw new \Exception(\G::LoadTranslation("ID_INVALID_LIMIT"));

                } else {

                    if ($limit == 0) {

                        return array();

                    }

                }

            } else {

                $limit = count($aUsers) + 1;

            }

            $aUsers = $this->arrayPagination($aUsers, $start, $limit);

            return $aUsers;

        } catch (\Exception $e) {

            throw $e;

        }

    }



    /**

     * Return a single Adhoc user or group assigned to an activity

     *

     * @param string $sProcessUID {@min 32} {@max 32}

     * @param string $sTaskUID {@min 32} {@max 32}

     * @param string $sAssigneeUID {@min 32} {@max 32}

     *

     * return array

     *

     * @access public

     */

    public function getTaskAdhocAssignee($sProcessUID, $sTaskUID, $sAssigneeUID)

    {

        try {

            Validator::proUid($sProcessUID, '$prj_uid');

            $this->validateActUid($sTaskUID);

            $iType = 2;

            $aUsers = array();

            $sDelimiter = \DBAdapter::getStringDelimiter();

            $oCriteria = new \Criteria('workflow'   );

            $oCriteria->addAsColumn('GRP_TITLE', 'C.CON_VALUE');

            $oCriteria->addSelectColumn(\TaskUserPeer::TAS_UID);

            $oCriteria->addSelectColumn(\TaskUserPeer::USR_UID);

            $oCriteria->addSelectColumn(\TaskUserPeer::TU_TYPE);

            $oCriteria->addSelectColumn(\TaskUserPeer::TU_RELATION);

            $oCriteria->addAlias('C', 'CONTENT');

            $aConditions = array();

            $aConditions[] = array(\TaskUserPeer::USR_UID, 'C.CON_ID' );

            $aConditions[] = array('C.CON_CATEGORY', $sDelimiter . 'GRP_TITLE' . $sDelimiter );

            $aConditions[] = array('C.CON_LANG', $sDelimiter . SYS_LANG . $sDelimiter );

            $oCriteria->addJoinMC($aConditions, \Criteria::LEFT_JOIN);

            $oCriteria->add(\TaskUserPeer::USR_UID, $sAssigneeUID);

            $oCriteria->add(\TaskUserPeer::TAS_UID, $sTaskUID);

            $oCriteria->add(\TaskUserPeer::TU_TYPE, $iType);

            $oCriteria->add(\TaskUserPeer::TU_RELATION, 2);

            $oDataset = \TaskUserPeer::doSelectRS($oCriteria);

            $oDataset->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

            $oDataset->next();

            $c = 0;

            $oTasks = new \Tasks();

            $aAux = $oTasks->getGroupsOfTask($sTaskUID, 2);

            $aUIDS1 = array();

            foreach ($aAux as $aGroup) {

                $aUIDS1[] = $aGroup['GRP_UID'];

            }

            $criteria = new \Criteria( 'workflow' );

            $criteria->addSelectColumn( \GroupwfPeer::GRP_UID );

            $criteria->addSelectColumn( \GroupwfPeer::GRP_STATUS );

            $criteria->addSelectColumn( \GroupwfPeer::GRP_UX );

            $criteria->addAsColumn( 'GRP_TITLE', \ContentPeer::CON_VALUE );

            $criteria->addJoin( \GroupwfPeer::GRP_UID, \ContentPeer::CON_ID, \Criteria::LEFT_JOIN );

            $criteria->add( \ContentPeer::CON_CATEGORY, 'GRP_TITLE' );

            $criteria->add( \ContentPeer::CON_LANG, SYS_LANG );

            $criteria->add( \GroupwfPeer::GRP_UID, $sAssigneeUID);

            $criteria->addAscendingOrderByColumn( \ContentPeer::CON_VALUE );

            $oDataset = \GroupwfPeer::doSelectRS( $criteria );

            $oDataset->setFetchmode( \ResultSet::FETCHMODE_ASSOC );

            $groups = array ();

            while ($oDataset->next()) {

                $groups[] = $oDataset->getRow();

            }

            $result = array ('rows' => $groups);

            foreach ($result['rows'] as $results) {

                if (in_array($results['GRP_UID'], $aUIDS1)) {

                    $c++;

                    $oCriteria = new \Criteria('workflow');

                    $oCriteria->addSelectColumn('COUNT(*) AS MEMBERS_NUMBER');

                    $oCriteria->add(\GroupUserPeer::GRP_UID, $results['GRP_UID']);

                    $oDataset2 = \GroupUserPeer::doSelectRS($oCriteria);

                    $oDataset2->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

                    $oDataset2->next();

                    $aRow2 = $oDataset2->getRow();

                    $aUsers[] = array('ada_uid' => $results['GRP_UID'],

                                      'ada_name' => (!isset($aRow2['GROUP_INACTIVE']) ? $results['GRP_TITLE'] .

                                      ' (' . $aRow2['MEMBERS_NUMBER'] . ' ' .

                                      ((int) $aRow2['MEMBERS_NUMBER'] == 1 ? \G::LoadTranslation('ID_USER') : \G::LoadTranslation('ID_USERS')).

                                      ')' . '' : $results['GRP_TITLE'] . ' ' . $aRow2['GROUP_INACTIVE']),

                                      'ada_lastname' => "",

                                      'ada_username' => "",

                                      'ada_type' => "group" );

                }

            }

            $oCriteria = new \Criteria('workflow');

            $oCriteria->addSelectColumn(\UsersPeer::USR_FIRSTNAME);

            $oCriteria->addSelectColumn(\UsersPeer::USR_LASTNAME);

            $oCriteria->addSelectColumn(\UsersPeer::USR_USERNAME);

            $oCriteria->addSelectColumn(\UsersPeer::USR_EMAIL);

            $oCriteria->addSelectColumn(\TaskUserPeer::TAS_UID);

            $oCriteria->addSelectColumn(\TaskUserPeer::USR_UID);

            $oCriteria->addSelectColumn(\TaskUserPeer::TU_TYPE);

            $oCriteria->addSelectColumn(\TaskUserPeer::TU_RELATION);

            $oCriteria->addJoin(\TaskUserPeer::USR_UID, \UsersPeer::USR_UID, \Criteria::LEFT_JOIN);

            $oCriteria->add(\TaskUserPeer::USR_UID, $sAssigneeUID);

            $oCriteria->add(\TaskUserPeer::TAS_UID, $sTaskUID);

            $oCriteria->add(\TaskUserPeer::TU_TYPE, $iType);

            $oCriteria->add(\TaskUserPeer::TU_RELATION, 1);

            $oDataset = \TaskUserPeer::doSelectRS($oCriteria);

            $oDataset->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

            $oDataset->next();

            while ($aRow = $oDataset->getRow()) {

                $aUsers = array('ada_uid' => $aRow['USR_UID'],

                                'ada_name' => $aRow['USR_FIRSTNAME'],

                                'ada_lastname' => $aRow['USR_LASTNAME'],

                                'ada_username' => $aRow['USR_USERNAME'],

                                'ada_type' => "user" );

                $oDataset->next();

            }

            if (empty($aUsers)) {

                throw new \Exception(\G::LoadTranslation("ID_RECORD_NOT_FOUND", array($sAssigneeUID)));

            } else {

                return $aUsers;

            }

        } catch (\Exception $e) {

            throw $e;

        }

    }



    /**

     * Assign a Adhoc user or group to an activity

     *

     * @param string $sProcessUID {@min 32} {@max 32}

     * @param string $sTaskUID {@min 32} {@max 32}

     * @param string $sAssigneeUID {@min 32} {@max 32}

     * @param string $assType {@choice user,group}

     *

     * return array

     *

     * @access public

     */

    public function addTaskAdhocAssignee($sProcessUID, $sTaskUID, $sAssigneeUID, $assType)

    {

        try {

            Validator::proUid($sProcessUID, '$prj_uid');

            $this->validateActUid($sTaskUID);

            $iType = 2;

            $iRelation = '';

            $oCriteria = new \Criteria('workflow');

            $oCriteria->addSelectColumn( \TaskUserPeer::TU_RELATION );

            $oCriteria->add(\TaskUserPeer::USR_UID, $sAssigneeUID );

            $oCriteria->add(\TaskUserPeer::TAS_UID, $sTaskUID );

            $oCriteria->add(\TaskUserPeer::TU_TYPE, $iType );

            $oTaskUser = \TaskUserPeer::doSelectRS( $oCriteria );

            $oTaskUser->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

            while ($oTaskUser->next()) {

                $aRow = $oTaskUser->getRow();

                $iRelation = $aRow['TU_RELATION'];

            }

            $oTaskUser = \TaskUserPeer::retrieveByPK( $sTaskUID, $sAssigneeUID, $iType, $iRelation );

            if (! is_null( $oTaskUser )) {

                throw new \Exception(\G::LoadTranslation("ID_ALREADY_ASSIGNED", array($sAssigneeUID, $sTaskUID)));

            } else {

                $oTypeAssigneeG = \GroupwfPeer::retrieveByPK( $sAssigneeUID );

                $oTypeAssigneeU = \UsersPeer::retrieveByPK( $sAssigneeUID );

                if (is_null( $oTypeAssigneeG ) && is_null( $oTypeAssigneeU ) ) {

                    throw new \Exception(\G::LoadTranslation("ID_DOES_NOT_CORRESPOND", array($sAssigneeUID, $assType)));

                }

                if (is_null( $oTypeAssigneeG ) && ! is_null( $oTypeAssigneeU) ) {

                    $type = "user";

                    if ( $type != $assType ) {

                        throw new \Exception(\G::LoadTranslation("ID_DOES_NOT_CORRESPOND", array($sAssigneeUID, $assType)));

                    }

                }

                if (! is_null( $oTypeAssigneeG ) && is_null( $oTypeAssigneeU ) ) {

                    $type = "group";

                    if ( $type != $assType ) {

                        throw new \Exception(\G::LoadTranslation("ID_DOES_NOT_CORRESPOND", array($sAssigneeUID, $assType)));

                    }

                }

                $oTaskUser = new \TaskUser();

                if ( $assType == "user" ) {

                    $oTaskUser->create(array('TAS_UID' => $sTaskUID,

                                             'USR_UID' => $sAssigneeUID,

                                             'TU_TYPE' => $iType,

                                             'TU_RELATION' => 1));

                } else {

                    $oTaskUser->create(array('TAS_UID' => $sTaskUID,

                                             'USR_UID' => $sAssigneeUID,

                                             'TU_TYPE' => $iType,

                                             'TU_RELATION' => 2));

                }



                $task = new \Task();



                $result = $task->update(array("TAS_UID" => $sTaskUID, "TAS_TYPE" => "ADHOC"));

            }

        } catch ( \Exception $e ) {

            throw $e;

        }

    }



    /**

     * Remove a Adhoc assignee of an activity

     *

     * @param string $sProcessUID {@min 32} {@max 32}

     * @param string $sTaskUID {@min 32} {@max 32}

     * @param string $sAssigneeUID {@min 32} {@max 32}

     *

     * @access public

     */

    public function removeTaskAdhocAssignee($sProcessUID, $sTaskUID, $sAssigneeUID)

    {

        try {

            Validator::proUid($sProcessUID, '$prj_uid');

            $this->validateActUid($sTaskUID);

            $iType = 2;

            $iRelation = '';

            $oCriteria = new \Criteria('workflow');

            $oCriteria->addSelectColumn( \TaskUserPeer::TU_RELATION );

            $oCriteria->add(\TaskUserPeer::USR_UID, $sAssigneeUID);

            $oCriteria->add(\TaskUserPeer::TAS_UID, $sTaskUID);

            $oCriteria->add(\TaskUserPeer::TU_TYPE, $iType);

            $oTaskUser = \TaskUserPeer::doSelectRS($oCriteria);

            $oTaskUser->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

            while ($oTaskUser->next()) {

                $aRow = $oTaskUser->getRow();

                $iRelation = $aRow['TU_RELATION'];

            }

            $oTaskUser = \TaskUserPeer::retrieveByPK($sTaskUID, $sAssigneeUID, $iType, $iRelation);

            if (! is_null( $oTaskUser )) {

                \TaskUserPeer::doDelete($oCriteria);



                $arrayTaskAdhocAssignees = $this->getTaskAdhocAssignees($sProcessUID, $sTaskUID, "", null, null, "");



                if (empty($arrayTaskAdhocAssignees)) {

                    $task = new \Task();



                    $result = $task->update(array("TAS_UID" => $sTaskUID, "TAS_TYPE" => "NORMAL"));

                }

            } else {

                throw new \Exception(\G::LoadTranslation("ID_ROW_DOES_NOT_EXIST"));

            }

        } catch (\Exception $e) {

            throw $e;

        }

    }



    /**

     * Validate Process Uid

     * @var string $pro_uid. Uid for process

     *

     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>

     * @copyright Colosa - Bolivia

     *

     * @return string

     */

    public function validateProUid ($pro_uid)

    {

        $pro_uid = trim($pro_uid);

        if ($pro_uid == '') {

            throw (new \Exception("The project with prj_uid: '', does not exist."));

        }

        $oProcess = new \Process();

        if (!($oProcess->processExists($pro_uid))) {

            throw (new \Exception("The project with prj_uid: '$pro_uid', does not exist."));

        }

        return $pro_uid;

    }



    /**

     * Validate Task Uid

     * @var string $act_uid. Uid for task

     *

     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>

     * @copyright Colosa - Bolivia

     *

     * @return string

     */

    public function validateActUid($act_uid)

    {

        $act_uid = trim($act_uid);

        if ($act_uid == '') {

            throw (new \Exception("The activity with act_uid: '', does not exist."));

        }

        $oTask = new \Task();

        if (!($oTask->taskExists($act_uid))) {

            throw (new \Exception("The activity with act_uid: '$act_uid', does not exist."));

        }

        return $act_uid;

    }



    /**

     * @var array $display_array. array of groups and users

     * @var int $page. start

     * @var int $show_per_page. limit

     *

     * @return array

     */

    public function arrayPagination($display_array, $page, $show_per_page)

    {

        $page = $page + 1;

        $show_per_page = $show_per_page -1;

        $start = ($page - 1) * ($show_per_page + 1);

        $offset = $show_per_page + 1;

        $outArray = array_slice($display_array, $start, $offset);

        return $outArray;

    }



    /**

     * Unset variable for array

     * @var array $array. Array base

     * @var string $variable. name for variable

     *

     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>

     * @copyright Colosa - Bolivia

     *

     * @return string

     */

    public function unsetVar(&$array, $variable)

    {

        if (isset($array[$variable])) {

            unset($array[$variable]);

        }

    }



    /**

     * Return a list of assignees of an activity

     *

     * @param string $sProcessUID {@min 32} {@max 32}

     * @param string $sTaskUID {@min 32} {@max 32}

     * @param string $filter

     * @param int    $start

     * @param int    $limit

     * @param string $type

     *

     * return array

     *

     * @access public

     */

    public function getTaskAssigneesAll($sProcessUID, $sTaskUID, $filter, $start, $limit, $type)

    {

        try {

            Validator::proUid($sProcessUID, '$prj_uid');

            $this->validateActUid($sTaskUID);

            $aUsers = array();

            $oTasks = new \Tasks();

            $aAux = $oTasks->getGroupsOfTask($sTaskUID, 1);

            $aGroupUids = array();

            foreach ($aAux as $aGroup) {

                $aGroupUids[] = $aGroup['GRP_UID'];

            }

            foreach ($aGroupUids as $results) {

                $oCriteria = new \Criteria('workflow');

                $oCriteria->addSelectColumn('USR_UID');

                $oCriteria->add(\GroupUserPeer::GRP_UID, $results);

                $oGroupDataset = \GroupUserPeer::doSelectRS($oCriteria);

                $oGroupDataset->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

                while ($oGroupDataset->next()) {

                    $aGroupRow = $oGroupDataset->getRow();

                    $oGroupCriteria = new \Criteria('workflow');

                    $oGroupCriteria->addSelectColumn(\UsersPeer::USR_UID);

                    $oGroupCriteria->addSelectColumn(\UsersPeer::USR_FIRSTNAME);

                    $oGroupCriteria->addSelectColumn(\UsersPeer::USR_LASTNAME);

                    $oGroupCriteria->addSelectColumn(\UsersPeer::USR_USERNAME);

                    if ($filter != '') {

                        $oGroupCriteria->add($oGroupCriteria->getNewCriterion(\UsersPeer::USR_USERNAME, "%$filter%", \Criteria::LIKE)

                                             ->addOr($oGroupCriteria->getNewCriterion(\UsersPeer::USR_FIRSTNAME, "%$filter%", \Criteria::LIKE))

                                             ->addOr($oGroupCriteria->getNewCriterion(\UsersPeer::USR_LASTNAME, "%$filter%", \Criteria::LIKE)));

                    }

                    $oGroupCriteria->add(\UsersPeer::USR_UID, $aGroupRow["USR_UID"]);

                    $oUserDataset = \UsersPeer::doSelectRS($oGroupCriteria);

                    $oUserDataset->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

                    $oUserDataset->next();

                    while ($aUserRow = $oUserDataset->getRow()) {

                         $aUsers[] = array('aas_uid' => $aUserRow['USR_UID'],

                                           'aas_name' => $aUserRow['USR_FIRSTNAME'],

                                           'aas_lastname' => $aUserRow['USR_LASTNAME'],

                                           'aas_username' => $aUserRow['USR_USERNAME'],

                                           'aas_type' => "user" );

                         $oUserDataset->next();

                    }

                }

            }

            $oCriteria = new \Criteria('workflow');

            $oCriteria->addSelectColumn(\UsersPeer::USR_UID);

            $oCriteria->addSelectColumn(\UsersPeer::USR_FIRSTNAME);

            $oCriteria->addSelectColumn(\UsersPeer::USR_LASTNAME);

            $oCriteria->addSelectColumn(\UsersPeer::USR_USERNAME);

            if ($filter != '') {

                $oCriteria->add($oCriteria->getNewCriterion(\UsersPeer::USR_USERNAME, "%$filter%", \Criteria::LIKE)

                                ->addOr($oCriteria->getNewCriterion(\UsersPeer::USR_FIRSTNAME, "%$filter%", \Criteria::LIKE))

                                ->addOr($oCriteria->getNewCriterion(\UsersPeer::USR_LASTNAME, "%$filter%", \Criteria::LIKE )));

            }

            $oCriteria->addJoin(\TaskUserPeer::USR_UID, \UsersPeer::USR_UID, \Criteria::LEFT_JOIN);

            $oCriteria->add(\TaskUserPeer::TAS_UID, $sTaskUID);

            $oCriteria->add(\TaskUserPeer::TU_TYPE, 1);

            $oCriteria->add(\TaskUserPeer::TU_RELATION, 1);

            $oDataset = \TaskUserPeer::doSelectRS($oCriteria);

            $oDataset->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

            $oDataset->next();

            while ($aRow = $oDataset->getRow()) {

                if ($type == '' || $type == 'user') {

                    $aUsers[] = array('aas_uid' => $aRow['USR_UID'],

                                      'aas_name' => $aRow['USR_FIRSTNAME'],

                                      'aas_lastname' => $aRow['USR_LASTNAME'],

                                      'aas_username' => $aRow['USR_USERNAME'],

                                      'aas_type' => "user" );

                }

                $oDataset->next();

            }

            $aUsersGroups = array();

            $exclude = array("");

            for ($i = 0; $i<=count($aUsers)-1; $i++) {

                if (!in_array(trim($aUsers[$i]["aas_uid"]) ,$exclude)) {

                    $aUsersGroups[] = $aUsers[$i];

                    $exclude[] = trim($aUsers[$i]["aas_uid"]);

                }

            }

            if ($start) {

                if ($start < 0) {

                    throw new \Exception(\G::LoadTranslation("ID_INVALID_START"));

                }

            } else {

                $start = 0;

            }

            if (isset($limit)) {

                if ($limit < 0) {

                    throw new \Exception(\G::LoadTranslation("ID_INVALID_LIMIT"));

                } else {

                    if ($limit == 0) {

                        return array();

                    }

                }

            } else {

                $limit = count($aUsersGroups) + 1;

            }

            $aUsersGroups = $this->arrayPagination($aUsersGroups, $start, $limit);

            return $aUsersGroups;

        } catch (\Exception $e) {

            throw $e;

        }

    }



    /**

     * Return a list of adhoc assignees of an activity

     *

     * @param string $sProcessUID {@min 32} {@max 32}

     * @param string $sTaskUID {@min 32} {@max 32}

     * @param string $filter

     * @param int    $start

     * @param int    $limit

     * @param string $type

     *

     * return array

     *

     * @access public

     */

    public function getTaskAdhocAssigneesAll($sProcessUID, $sTaskUID, $filter, $start, $limit, $type)

    {

        try {

            Validator::proUid($sProcessUID, '$prj_uid');

            $this->validateActUid($sTaskUID);

            $aUsers = array();

            $oTasks = new \Tasks();

            $aAux = $oTasks->getGroupsOfTask($sTaskUID, 2);

            $aGroupUids = array();

            foreach ($aAux as $aGroup) {

                $aGroupUids[] = $aGroup['GRP_UID'];

            }

            foreach ($aGroupUids as $results) {

                $oCriteria = new \Criteria('workflow');

                $oCriteria->addSelectColumn('USR_UID');

                $oCriteria->add(\GroupUserPeer::GRP_UID, $results);

                $oGroupDataset = \GroupUserPeer::doSelectRS($oCriteria);

                $oGroupDataset->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

                while ($oGroupDataset->next()) {

                    $aGroupRow = $oGroupDataset->getRow();

                    $oGroupCriteria = new \Criteria('workflow');

                    $oGroupCriteria->addSelectColumn(\UsersPeer::USR_UID);

                    $oGroupCriteria->addSelectColumn(\UsersPeer::USR_FIRSTNAME);

                    $oGroupCriteria->addSelectColumn(\UsersPeer::USR_LASTNAME);

                    $oGroupCriteria->addSelectColumn(\UsersPeer::USR_USERNAME);

                    if ($filter != '') {

                        $oGroupCriteria->add($oGroupCriteria->getNewCriterion(\UsersPeer::USR_USERNAME, "%$filter%", \Criteria::LIKE)

                                             ->addOr($oGroupCriteria->getNewCriterion(\UsersPeer::USR_FIRSTNAME, "%$filter%", \Criteria::LIKE))

                                             ->addOr($oGroupCriteria->getNewCriterion(\UsersPeer::USR_LASTNAME, "%$filter%", \Criteria::LIKE)));

                    }

                    $oGroupCriteria->add(\UsersPeer::USR_UID, $aGroupRow["USR_UID"]);

                    $oUserDataset = \UsersPeer::doSelectRS($oGroupCriteria);

                    $oUserDataset->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

                    $oUserDataset->next();

                    while ($aUserRow = $oUserDataset->getRow()) {

                        $aUsers[] = array('aas_uid' => $aUserRow['USR_UID'],

                                          'aas_name' => $aUserRow['USR_FIRSTNAME'],

                                          'aas_lastname' => $aUserRow['USR_LASTNAME'],

                                          'aas_username' => $aUserRow['USR_USERNAME'],

                                          'aas_type' => "user" );

                        $oUserDataset->next();

                    }

                }

            }

            $oCriteria = new \Criteria('workflow');

            $oCriteria->addSelectColumn(\UsersPeer::USR_UID);

            $oCriteria->addSelectColumn(\UsersPeer::USR_FIRSTNAME);

            $oCriteria->addSelectColumn(\UsersPeer::USR_LASTNAME);

            $oCriteria->addSelectColumn(\UsersPeer::USR_USERNAME);

            if ($filter != '') {

                $oCriteria->add($oCriteria->getNewCriterion(\UsersPeer::USR_USERNAME, "%$filter%", \Criteria::LIKE)

                                ->addOr($oCriteria->getNewCriterion(\UsersPeer::USR_FIRSTNAME, "%$filter%", \Criteria::LIKE))

                                ->addOr($oCriteria->getNewCriterion(\UsersPeer::USR_LASTNAME, "%$filter%", \Criteria::LIKE )));

            }

            $oCriteria->addJoin(\TaskUserPeer::USR_UID, \UsersPeer::USR_UID, \Criteria::LEFT_JOIN);

            $oCriteria->add(\TaskUserPeer::TAS_UID, $sTaskUID);

            $oCriteria->add(\TaskUserPeer::TU_TYPE, 2);

            $oCriteria->add(\TaskUserPeer::TU_RELATION, 1);

            $oDataset = \TaskUserPeer::doSelectRS($oCriteria);

            $oDataset->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

            $oDataset->next();

            while ($aRow = $oDataset->getRow()) {

                if ($type == '' || $type == 'user') {

                    $aUsers[] = array('aas_uid' => $aRow['USR_UID'],

                                      'aas_name' => $aRow['USR_FIRSTNAME'],

                                      'aas_lastname' => $aRow['USR_LASTNAME'],

                                      'aas_username' => $aRow['USR_USERNAME'],

                                      'aas_type' => "user" );

                }

                $oDataset->next();

            }

            $aUsersGroups = array();

            $exclude = array("");

            for ($i = 0; $i<=count($aUsers)-1; $i++) {

                if (!in_array(trim($aUsers[$i]["aas_uid"]) ,$exclude)) {

                    $aUsersGroups[] = $aUsers[$i];

                    $exclude[] = trim($aUsers[$i]["aas_uid"]);

                }

            }

            if ($start) {

                if ($start < 0) {

                    throw new \Exception(\G::LoadTranslation("ID_INVALID_START"));

                }

            } else {

                $start = 0;

            }

            if (isset($limit)) {

                if ($limit < 0) {

                    throw new \Exception(\G::LoadTranslation("ID_INVALID_LIMIT"));

                } else {

                    if ($limit == 0) {

                        return array();

                    }

                }

            } else {

                $limit = count($aUsersGroups) + 1;

            }

            $aUsersGroups = $this->arrayPagination($aUsersGroups, $start, $limit);

            return $aUsersGroups;

        } catch (\Exception $e) {

            throw $e;

        }

    }

}


