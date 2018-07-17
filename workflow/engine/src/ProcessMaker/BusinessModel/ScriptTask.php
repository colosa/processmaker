<?php
namespace ProcessMaker\BusinessModel;
use G;

class ScriptTask
{
    private $arrayFieldDefinition = array(
        "SCRTAS_UID"      => array("type" => "string", "required" => false, "empty" => false, "defaultValues" => array(),          "fieldNameAux" => "scrtasUid"),
        "PRJ_UID"         => array("type" => "string", "required" => false, "empty" => false, "defaultValues" => array(),          "fieldNameAux" => "projectUid"),
        "ACT_UID"         => array("type" => "string", "required" => true,  "empty" => false, "defaultValues" => array(),          "fieldNameAux" => "actUid"),
        "SCRTAS_OBJ_TYPE" => array("type" => "string", "required" => true,  "empty" => false, "defaultValues" => array("TRIGGER"), "fieldNameAux" => "scrtasObjType"),
        "SCRTAS_OBJ_UID"  => array("type" => "string", "required" => false, "empty" => false, "defaultValues" => array(),          "fieldNameAux" => "scrtasObjUid")
    );

    private $formatFieldNameInUppercase = true;

    private $arrayFieldNameForException = array();

    /**
     * Constructor of the class
     *
     * return void
     */
    public function __construct()
    {
        try {
            foreach ($this->arrayFieldDefinition as $key => $value) {
                $this->arrayFieldNameForException[$value["fieldNameAux"]] = $key;
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

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

            $this->setArrayFieldNameForException($this->arrayFieldNameForException);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Set exception messages for fields
     *
     * @param array $arrayData Data with the fields
     *
     * return void
     */
    public function setArrayFieldNameForException(array $arrayData)
    {
        try {
            foreach ($arrayData as $key => $value) {
                $this->arrayFieldNameForException[$key] = $this->getFieldNameByFormatFieldName($value);
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
            //Return
            return ($this->formatFieldNameInUppercase)? strtoupper($fieldName) : strtolower($fieldName);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Verify if exists the Script-Task
     *
     * @param string $scriptTaskUid Unique id of Script-Task
     *
     * return bool Return true if exists the Script-Task, false otherwise
     */
    public function exists($scriptTaskUid)
    {
        try {
            $obj = \ScriptTaskPeer::retrieveByPK($scriptTaskUid);

            //Return
            return (!is_null($obj))? true : false;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Verify if does not exists the Script-Task
     *
     * @param string $scriptTaskUid         Unique id of Script-Task
     * @param string $fieldNameForException Field name for the exception
     *
     * return void Throw exception if does not exists the Script-Task
     */
    public function throwExceptionIfNotExistsScriptTask($scriptTaskUid, $fieldNameForException)
    {
        try {
            if (!$this->exists($scriptTaskUid)) {
                throw new \Exception(\G::LoadTranslation("ID_SCRIPT_TASK_DOES_NOT_EXIST", array($fieldNameForException, $scriptTaskUid)));
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Validate the data if they are invalid (INSERT and UPDATE)
     *
     * @param string $scriptTaskUid Unique id of Script-Task
     * @param string $projectUid    Unique id of Project
     * @param array  $arrayData     Data
     *
     * return void Throw exception if data has an invalid value
     */
    public function throwExceptionIfDataIsInvalid($scriptTaskUid, $projectUid, array $arrayData)
    {
        try {
            //Set variables
            $arrayScriptTaskData = ($scriptTaskUid == "")? array() : $this->getScriptTask($scriptTaskUid, true);
            $flagInsert = ($scriptTaskUid == "")? true : false;

            $arrayFinalData = array_merge($arrayScriptTaskData, $arrayData);

            //Verify data - Field definition
            $process = new \ProcessMaker\BusinessModel\Process();

            $process->throwExceptionIfDataNotMetFieldDefinition($arrayData, $this->arrayFieldDefinition, $this->arrayFieldNameForException, $flagInsert);

            //---
            if (isset($arrayData["ACT_UID"])) {
                $obj = \BpmnActivityPeer::retrieveByPK($arrayData["ACT_UID"]);

                if (is_null($obj)) {
                    throw new \Exception(\G::LoadTranslation("ID_SCRIPT_TASK_DOES_NOT_ACTIVITY", array($this->arrayFieldNameForException["actUid"], $arrayData["ACT_UID"])));
                }
            }

            //---
            if (isset($arrayData["SCRTAS_OBJ_UID"])) {
                $obj = \TriggersPeer::retrieveByPK($arrayData["SCRTAS_OBJ_UID"]);

                if (is_null($obj)) {
                    throw new \Exception(\G::LoadTranslation("ID_SCRIPT_TASK_DOES_NOT_TRIGGER", array($this->arrayFieldNameForException["scrtasObjUid"], $arrayData["SCRTAS_OBJ_UID"])));
                }
            }

            //---
            if (isset($arrayData["ACT_UID"])) {
                $criteria = new \Criteria("workflow");

                $criteria->addSelectColumn(\BpmnActivityPeer::ACT_UID);
                $criteria->add(\BpmnActivityPeer::ACT_UID, $arrayData["ACT_UID"], \Criteria::EQUAL);
                $criteria->add(\BpmnActivityPeer::PRJ_UID, $projectUid, \Criteria::EQUAL);

                $rsCriteria = \BpmnActivityPeer::doSelectRS($criteria);
                $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

                if (!$rsCriteria->next()) {
                    throw new \Exception(\G::LoadTranslation("ID_SCRIPT_TASK_ACTIVITY_NOT_BELONG_TO_PROJECT", array($arrayData["ACT_UID"], $projectUid)));
                }
            }

            //---
            if (isset($arrayData["SCRTAS_OBJ_UID"])) {
                $criteria = new \Criteria("workflow");

                $criteria->addSelectColumn(\TriggersPeer::TRI_UID);
                $criteria->add(\TriggersPeer::TRI_UID, $arrayData["SCRTAS_OBJ_UID"], \Criteria::EQUAL);
                $criteria->add(\TriggersPeer::PRO_UID, $projectUid, \Criteria::EQUAL);

                $rsCriteria = \TriggersPeer::doSelectRS($criteria);
                $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

                if (!$rsCriteria->next()) {
                    throw new \Exception(\G::LoadTranslation("ID_SCRIPT_TASK_TRIGGER_NOT_BELONG_TO_PROJECT", array($arrayData["SCRTAS_OBJ_UID"], $projectUid)));
                }
            }

            //---
            $obj = \BpmnActivityPeer::retrieveByPK($arrayFinalData["ACT_UID"]);

            if ($obj->getActTaskType() != "SCRIPTTASK") {
               throw new \Exception(\G::LoadTranslation("ID_SCRIPT_TASK_TYPE_ACTIVITY_NOT_IS_SCRIPTTASK", array($this->arrayFieldNameForException["actUid"], $arrayData["ACT_UID"])));
            }

            //Activity - Already registered
            $criteria = new \Criteria('workflow');
            $criteria->addSelectColumn(\ScriptTaskPeer::SCRTAS_UID);

            if ($scriptTaskUid != '') {
                $criteria->add(\ScriptTaskPeer::SCRTAS_UID, $scriptTaskUid, \Criteria::NOT_EQUAL);
            }

            $criteria->add(\ScriptTaskPeer::PRJ_UID, $projectUid, \Criteria::EQUAL);
            $criteria->add(\ScriptTaskPeer::ACT_UID, $arrayFinalData['ACT_UID'], \Criteria::EQUAL);

            $rsCriteria = \ScriptTaskPeer::doSelectRS($criteria);

            if ($rsCriteria->next()) {
                throw new \Exception(\G::LoadTranslation(
                    'ID_SCRIPT_TASK_ACTIVITY_ALREADY_REGISTERED',
                    [$this->arrayFieldNameForException['actUid'], $arrayFinalData['ACT_UID']]
                ));
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Create Script-Task for a Project
     *
     * @param string $projectUid Unique id of Project
     * @param array  $arrayData  Data
     *
     * return array Return data of the new Script-Task created
     */
    public function create($projectUid, array $arrayData)
    {
        try {
            //Verify data
            $process = new \ProcessMaker\BusinessModel\Process();
            $validator = new \ProcessMaker\BusinessModel\Validator();

            $validator->throwExceptionIfDataIsNotArray($arrayData, "\$arrayData");
            $validator->throwExceptionIfDataIsEmpty($arrayData, "\$arrayData");

            //Set data
            $arrayData = array_change_key_case($arrayData, CASE_UPPER);

            unset($arrayData["SCRTAS_UID"]);
            unset($arrayData["PRJ_UID"]);

            //Verify data
            $process->throwExceptionIfNotExistsProcess($projectUid, $this->arrayFieldNameForException["projectUid"]);

            $this->throwExceptionIfDataIsInvalid("", $projectUid, $arrayData);

            //Create
            $cnn = \Propel::getConnection("workflow");

            try {
                $scriptTask = new \ScriptTask();

                $scriptTask->fromArray($arrayData, \BasePeer::TYPE_FIELDNAME);

                $scriptTaskUid = \ProcessMaker\Util\Common::generateUID();

                $scriptTask->setScrtasUid($scriptTaskUid);
                $scriptTask->setPrjUid($projectUid);

                if ($scriptTask->validate()) {
                    $cnn->begin();

                    $result = $scriptTask->save();

                    $cnn->commit();

                    //Return
                    return $this->getScriptTask($scriptTaskUid);
                } else {
                    $msg = "";

                    foreach ($scriptTask->getValidationFailures() as $validationFailure) {
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
     * Update Script-Task
     *
     * @param string $scriptTaskUid Unique id of Script-Task
     * @param array  $arrayData     Data
     *
     * return array Return data of the Script-Task updated
     */
    public function update($scriptTaskUid, array $arrayData)
    {
        try {
            //Verify data
            $process = new \ProcessMaker\BusinessModel\Process();
            $validator = new \ProcessMaker\BusinessModel\Validator();

            $validator->throwExceptionIfDataIsNotArray($arrayData, "\$arrayData");
            $validator->throwExceptionIfDataIsEmpty($arrayData, "\$arrayData");

            //Set data
            $arrayData = array_change_key_case($arrayData, CASE_UPPER);
            $arrayDataBackup = $arrayData;

            unset($arrayData["SCRTAS_UID"]);
            unset($arrayData["PRJ_UID"]);

            //Set variables
            $arrayScriptTaskData = $this->getScriptTask($scriptTaskUid, true);

            //Verify data
            $this->throwExceptionIfNotExistsScriptTask($scriptTaskUid, $this->arrayFieldNameForException["scrtasUid"]);

            $this->throwExceptionIfDataIsInvalid($scriptTaskUid, $arrayScriptTaskData["PRJ_UID"], $arrayData);

            //Update
            $cnn = \Propel::getConnection("workflow");

            try {
                $scriptTask = \ScriptTaskPeer::retrieveByPK($scriptTaskUid);

                $scriptTask->fromArray($arrayData, \BasePeer::TYPE_FIELDNAME);

                if ($scriptTask->validate()) {
                    $cnn->begin();

                    $result = $scriptTask->save();

                    $cnn->commit();

                    //Return
                    $arrayData = $arrayDataBackup;

                    if (!$this->formatFieldNameInUppercase) {
                        $arrayData = array_change_key_case($arrayData, CASE_LOWER);
                    }

                    return $arrayData;
                } else {
                    $msg = "";

                    foreach ($scriptTask->getValidationFailures() as $validationFailure) {
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
     * Delete Script-Task
     *
     * @param string $scriptTaskUid Unique id of Script-Task
     *
     * return void
     */
    public function delete($scriptTaskUid)
    {
        try {
            //Verify data
            $this->throwExceptionIfNotExistsScriptTask($scriptTaskUid, $this->arrayFieldNameForException["scrtasUid"]);

            //Delete
            $criteria = new \Criteria("workflow");

            $criteria->add(\ScriptTaskPeer::SCRTAS_UID, $scriptTaskUid, \Criteria::EQUAL);

            $result = \ScriptTaskPeer::doDelete($criteria);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Delete Script-Task
     *
     * @param array $arrayCondition Conditions
     *
     * return void
     */
    public function deleteWhere(array $arrayCondition)
    {
        try {
            //Delete
            $criteria = new \Criteria("workflow");

            foreach ($arrayCondition as $key => $value) {
                if (is_array($value)) {
                    $criteria->add($key, $value[0], $value[1]);
                } else {
                    $criteria->add($key, $value, \Criteria::EQUAL);
                }
            }

            $result = \ScriptTaskPeer::doDelete($criteria);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get criteria for Script-Task
     *
     * return object
     */
    public function getScriptTaskCriteria()
    {
        try {
            $criteria = new \Criteria("workflow");

            $criteria->addSelectColumn(\ScriptTaskPeer::SCRTAS_UID);
            $criteria->addSelectColumn(\ScriptTaskPeer::PRJ_UID);
            $criteria->addSelectColumn(\ScriptTaskPeer::ACT_UID);
            $criteria->addSelectColumn(\ScriptTaskPeer::SCRTAS_OBJ_TYPE);
            $criteria->addSelectColumn(\ScriptTaskPeer::SCRTAS_OBJ_UID);

            return $criteria;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get data of a Script-Task from a record
     *
     * @param array $record Record
     *
     * return array Return an array with data Script-Task
     */
    public function getScriptTaskDataFromRecord(array $record)
    {
        try {
            return array(
                $this->getFieldNameByFormatFieldName("SCRTAS_UID") => $record["SCRTAS_UID"],
                $this->getFieldNameByFormatFieldName("PRJ_UID") => $record["PRJ_UID"],
                $this->getFieldNameByFormatFieldName("ACT_UID") => $record["ACT_UID"],
                $this->getFieldNameByFormatFieldName("SCRTAS_OBJ_TYPE") => $record["SCRTAS_OBJ_TYPE"],
                $this->getFieldNameByFormatFieldName("SCRTAS_OBJ_UID") => $record["SCRTAS_OBJ_UID"]
            );
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get data of a Script-Task
     *
     * @param string $scriptTaskUid Unique id of Script-Task
     * @param bool   $flagGetRecord Value that set the getting
     *
     * return array Return an array with data of a Script-Task
     */
    public function getScriptTask($scriptTaskUid, $flagGetRecord = false)
    {
        try {
            //Verify data
            $this->throwExceptionIfNotExistsScriptTask($scriptTaskUid, $this->arrayFieldNameForException["scrtasUid"]);

            //Get data
            $criteria = $this->getScriptTaskCriteria();

            $criteria->add(\ScriptTaskPeer::SCRTAS_UID, $scriptTaskUid, \Criteria::EQUAL);

            $rsCriteria = \ScriptTaskPeer::doSelectRS($criteria);
            $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

            $result = $rsCriteria->next();

            $row = $rsCriteria->getRow();

            //Return
            return (!$flagGetRecord)? $this->getScriptTaskDataFromRecord($row) : $row;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get all Script-Tasks
     *
     * @param string $scriptTaskDefUid Unique id of Project
     *
     * return array Return an array with all Script-Tasks
     */
    public function getScriptTasks($projectUid)
    {
        try {
            $arrayScriptTask = array();

            //Verify data
            $process = new \ProcessMaker\BusinessModel\Process();

            $process->throwExceptionIfNotExistsProcess($projectUid, $this->arrayFieldNameForException["projectUid"]);

            //Get data
            $criteria = $this->getScriptTaskCriteria();

            $criteria->add(\ScriptTaskPeer::PRJ_UID, $projectUid, \Criteria::EQUAL);

            $rsCriteria = \ScriptTaskPeer::doSelectRS($criteria);
            $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

            while ($rsCriteria->next()) {
                $row = $rsCriteria->getRow();

                $arrayScriptTask[] = $this->getScriptTaskDataFromRecord($row);
            }

            //Return
            return $arrayScriptTask;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get data of a Script-Task by unique id of Activity
     *
     * @param string $projectUid  Unique id of Project
     * @param string $activityUid Unique id of Event
     *
     * return array Return an array with data of a Script-Task by unique id of Activity
     */
    public function getScriptTaskByActivity($projectUid, $activityUid)
    {
        try {
            //Verify data
            $process = new \ProcessMaker\BusinessModel\Process();

            $process->throwExceptionIfNotExistsProcess($projectUid, $this->arrayFieldNameForException["projectUid"]);

            //---
            $obj = \BpmnActivityPeer::retrieveByPK($activityUid);

            if (is_null($obj)) {
                throw new \Exception(\G::LoadTranslation("ID_SCRIPT_TASK_DOES_NOT_ACTIVITY", array($this->arrayFieldNameForException["actUid"], $activityUid)));
            }

            //---
            $criteria = new \Criteria("workflow");

            $criteria->addSelectColumn(\BpmnActivityPeer::ACT_UID);
            $criteria->add(\BpmnActivityPeer::PRJ_UID, $projectUid, \Criteria::EQUAL);
            $criteria->add(\BpmnActivityPeer::ACT_UID, $activityUid, \Criteria::EQUAL);

            $rsCriteria = \BpmnActivityPeer::doSelectRS($criteria);
            $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

            if (!$rsCriteria->next()) {
                throw new \Exception(\G::LoadTranslation("ID_SCRIPT_TASK_ACTIVITY_NOT_BELONG_TO_PROJECT", array($arrayData["ACT_UID"], $projectUid)));
            }

            //---
            $criteria = $this->getScriptTaskCriteria();

            $criteria->add(\ScriptTaskPeer::PRJ_UID, $projectUid, \Criteria::EQUAL);
            $criteria->add(\ScriptTaskPeer::ACT_UID, $activityUid, \Criteria::EQUAL);

            $rsCriteria = \ScriptTaskPeer::doSelectRS($criteria);
            $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

            //Return
            return ($rsCriteria->next())? $this->getScriptTaskDataFromRecord($rsCriteria->getRow()) : array();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Execute Script
     *
     * @param string $activityUid          Unique id of Event
     * @param array  $arrayApplicationData Case data
     *
     * @return array
     */
    public function execScriptByActivityUid($activityUid, array $arrayApplicationData)
    {
        try {
            $task = \TaskPeer::retrieveByPK($activityUid);

            if (!is_null($task) && $task->getTasType() == "SCRIPT-TASK") {
                $criteria = new \Criteria("workflow");

                $criteria->addSelectColumn(\ScriptTaskPeer::SCRTAS_OBJ_UID);

                $criteria->add(\ScriptTaskPeer::ACT_UID, $activityUid, \Criteria::EQUAL);

                $rsCriteria = \ScriptTaskPeer::doSelectRS($criteria);
                $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

                if ($rsCriteria->next()) {
                    $row = $rsCriteria->getRow();

                    $scriptTasObjUid = $row["SCRTAS_OBJ_UID"];

                    $trigger = \TriggersPeer::retrieveByPK($scriptTasObjUid);

                    if (!is_null($trigger)) {
                        //Some Pmf functions uses this global variable $oPMScript for review the aFields defined
                        global $oPMScript;
                        $oPMScript = new \PMScript();
                        $oPMScript->setDataTrigger($trigger->toArray(\BasePeer::TYPE_FIELDNAME));
                        $oPMScript->setFields($arrayApplicationData["APP_DATA"]);
                        $oPMScript->setScript($trigger->getTriWebbot());
                        $oPMScript->execute();

                        if (isset($oPMScript->aFields["__ERROR__"]))  {
                            G::log("Case Uid: " . $arrayApplicationData["APP_UID"] . ", Error: " . $oPMScript->aFields["__ERROR__"], PATH_DATA, "ScriptTask.log");
                        }

                        $arrayApplicationData["APP_DATA"] = $oPMScript->aFields;

                        $case = new \Cases();

                        $result = $case->updateCase($arrayApplicationData["APP_UID"], $arrayApplicationData);
                    }
                }
            }

            //Return
            return $arrayApplicationData["APP_DATA"];
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
