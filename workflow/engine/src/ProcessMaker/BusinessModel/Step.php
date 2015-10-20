<?php
namespace ProcessMaker\BusinessModel;

class Step
{
    private $formatFieldNameInUppercase = true;
    private $arrayParamException = array(
        "stepUid"       => "STEP_UID",
        "taskUid"       => "TAS_UID",
        "processUid"    => "PRO_UID",
        "stepTypeObj"   => "STEP_TYPE_OBJ",
        "stepUidObj"    => "STEP_UID_OBJ",
        "stepCondition" => "STEP_CONDITION",
        "stepPosition"  => "STEP_POSITION",
        "stepMode"      => "STEP_MODE"
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
     * Verify if exists the record in table STEP
     *
     * @param string $taskUid        Unique id of Task
     * @param string $type           Type of Step (DYNAFORM, INPUT_DOCUMENT, OUTPUT_DOCUMENT)
     * @param string $objectUid      Unique id of Object
     * @param int    $position       Position
     * @param string $stepUidExclude Unique id of Step to exclude
     *
     * return bool Return true if exists the record in table STEP, false otherwise
     */
    public function existsRecord($taskUid, $type, $objectUid, $position = 0, $stepUidExclude = "")
    {
        try {
            $criteria = new \Criteria("workflow");

            $criteria->addSelectColumn(\StepPeer::STEP_UID);
            $criteria->add(\StepPeer::TAS_UID, $taskUid, \Criteria::EQUAL);

            if ($stepUidExclude != "") {
                $criteria->add(\StepPeer::STEP_UID, $stepUidExclude, \Criteria::NOT_EQUAL);
            }

            if ($type != "") {
                $criteria->add(\StepPeer::STEP_TYPE_OBJ, $type, \Criteria::EQUAL);
            }

            if ($objectUid != "") {
                $criteria->add(\StepPeer::STEP_UID_OBJ, $objectUid, \Criteria::EQUAL);
            }

            if ($position > 0) {
                $criteria->add(\StepPeer::STEP_POSITION, $position, \Criteria::EQUAL);
            }

            $rsCriteria = \StepPeer::doSelectRS($criteria);
            $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

            if ($rsCriteria->next()) {
                return true;
            } else {
                return false;
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Verify if exists the "Object UID" in the corresponding table
     *
     * @param string $type                  Type of Step (DYNAFORM, INPUT_DOCUMENT, OUTPUT_DOCUMENT)
     * @param string $objectUid             Unique id of Object
     * @param string $fieldNameForException Field name for the exception
     *
     * return strin Return empty string if $objectUid exists in the corresponding table, return string with data if $objectUid doesn't exist
     */
    public function existsObjectUid($type, $objectUid, $fieldNameForException)
    {
        try {
            $msg = "";

            switch ($type) {
                case "DYNAFORM":
                    $dynaform = new \Dynaform();

                    if (!$dynaform->dynaformExists($objectUid)) {
                        $msg = \G::LoadTranslation("ID_DYNAFORM_DOES_NOT_EXIST", array($fieldNameForException, $objectUid));
                    }
                    break;
                case "INPUT_DOCUMENT":
                    $inputdoc = new \InputDocument();

                    if (!$inputdoc->InputExists($objectUid)) {
                        $msg = \G::LoadTranslation("ID_INPUT_DOCUMENT_DOES_NOT_EXIST", array($fieldNameForException, $objectUid));
                    }
                    break;
                case "OUTPUT_DOCUMENT":
                    $outputdoc = new \OutputDocument();

                    if (!$outputdoc->OutputExists($objectUid)) {
                        $msg = \G::LoadTranslation("ID_OUTPUT_DOCUMENT_DOES_NOT_EXIST", array($fieldNameForException, $objectUid));
                    }
                    break;
            }

            return $msg;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Verify if Type Object has invalid value
     *
     * @param string $stepTypeObj Type Object
     *
     * return void Throw exception if Type Object has invalid value
     */
    public function throwExceptionIfHaveInvalidValueInTypeObj($stepTypeObj)
    {
        $arrayDefaultValues = array("DYNAFORM", "INPUT_DOCUMENT", "OUTPUT_DOCUMENT", "EXTERNAL");

        if (!in_array($stepTypeObj, $arrayDefaultValues)) {
            $field = $this->arrayParamException["stepTypeObj"];

            throw new \Exception(\G::LoadTranslation("ID_INVALID_VALUE_ONLY_ACCEPTS_VALUES", array($field, implode("|", $arrayDefaultValues))));
        }
    }

    /**
     * Verify if Mode has invalid value
     *
     * @param string $stepMode Mode
     *
     * return void Throw exception if Mode has invalid value
     */
    public function throwExceptionIfHaveInvalidValueInMode($stepMode)
    {
        $arrayDefaultValues = array("EDIT", "VIEW");

        if (!in_array($stepMode, $arrayDefaultValues)) {
            $field = $this->arrayParamException["stepMode"];

            throw new \Exception(\G::LoadTranslation("ID_INVALID_VALUE_ONLY_ACCEPTS_VALUES", array($field, implode("|", $arrayDefaultValues))));
        }
    }

    /**
     * Verify if doesn't exist the Step in table STEP
     *
     * @param string $stepUid Unique id of Step
     *
     * return void Throw exception if doesn't exist the Step in table STEP
     */
    public function throwExceptionIfNotExistsStep($stepUid)
    {
        $step = new \Step();

        if (!$step->StepExists($stepUid)) {
            throw new \Exception(\G::LoadTranslation("ID_STEP_DOES_NOT_EXIST", array($this->arrayParamException["stepUid"], $stepUid)));
        }
    }

    /**
     * Verify if doesn't exist the Process in table PROCESS
     *
     * @param string $processUid Unique id of Process
     *
     * return void Throw exception if doesn't exist the Process in table PROCESS
     */
    public function throwExceptionIfNotExistsProcess($processUid)
    {
        $process = new \Process();

        if (!$process->exists($processUid)) {
            throw new \Exception(\G::LoadTranslation("ID_PROJECT_DOES_NOT_EXIST", array($this->arrayParamException["processUid"], $processUid)));
        }
    }

    /**
     * Create Step for a Task
     *
     * @param string $taskUid    Unique id of Task
     * @param string $processUid Unique id of Process
     * @param array  $arrayData  Data
     *
     * return array Return data of the new Step created
     */
    public function create($taskUid, $processUid, $arrayData)
    {
        try {
            $arrayData = array_change_key_case($arrayData, CASE_UPPER);

            unset($arrayData["STEP_UID"]);

            //Verify data
            $task = new \ProcessMaker\BusinessModel\Task();

            $this->throwExceptionIfNotExistsProcess($processUid);

            $task->throwExceptionIfNotExistsTask($processUid, $taskUid, $this->arrayParamException["taskUid"]);

            if (!isset($arrayData["STEP_TYPE_OBJ"])) {
                throw new \Exception(\G::LoadTranslation("ID_UNDEFINED_VALUE_IS_REQUIRED", array($this->arrayParamException["stepTypeObj"])));
            }

            $arrayData["STEP_TYPE_OBJ"] = trim($arrayData["STEP_TYPE_OBJ"]);

            if ($arrayData["STEP_TYPE_OBJ"] == "") {
                throw new \Exception(\G::LoadTranslation("ID_INVALID_VALUE_CAN_NOT_BE_EMPTY", array($this->arrayParamException["stepTypeObj"])));
            }

            if (!isset($arrayData["STEP_UID_OBJ"])) {
                throw new \Exception(\G::LoadTranslation("ID_UNDEFINED_VALUE_IS_REQUIRED", array($this->arrayParamException["stepUidObj"])));
            }

            $arrayData["STEP_UID_OBJ"] = trim($arrayData["STEP_UID_OBJ"]);

            if ($arrayData["STEP_UID_OBJ"] == "") {
                throw new \Exception(\G::LoadTranslation("ID_INVALID_VALUE_CAN_NOT_BE_EMPTY", array($this->arrayParamException["stepUidObj"])));
            }

            if (!isset($arrayData["STEP_MODE"])) {
                throw new \Exception(\G::LoadTranslation("ID_UNDEFINED_VALUE_IS_REQUIRED", array($this->arrayParamException["stepMode"])));
            }

            $arrayData["STEP_MODE"] = trim($arrayData["STEP_MODE"]);

            if ($arrayData["STEP_MODE"] == "") {
                throw new \Exception(\G::LoadTranslation("ID_INVALID_VALUE_CAN_NOT_BE_EMPTY", array($this->arrayParamException["stepMode"])));
            }

            $this->throwExceptionIfHaveInvalidValueInTypeObj($arrayData["STEP_TYPE_OBJ"]);

            $this->throwExceptionIfHaveInvalidValueInMode($arrayData["STEP_MODE"]);

            $msg = $this->existsObjectUid($arrayData["STEP_TYPE_OBJ"], $arrayData["STEP_UID_OBJ"], $this->arrayParamException["stepUidObj"]);

            if ($msg != "") {
                throw new \Exception($msg);
            }

            if ($this->existsRecord($taskUid, $arrayData["STEP_TYPE_OBJ"], $arrayData["STEP_UID_OBJ"])) {
                throw new \Exception(\G::LoadTranslation("ID_RECORD_EXISTS_IN_TABLE", array($taskUid . ", " . $arrayData["STEP_TYPE_OBJ"] . ", " . $arrayData["STEP_UID_OBJ"], "STEP")));
            }

            //Create
            $step = new \Step();

            $stepUid = $step->create(array(
                "PRO_UID" => $processUid,
                "TAS_UID" => $taskUid,
                "STEP_POSITION" => $step->getNextPosition($taskUid)
            ));

            if (!isset($arrayData["STEP_POSITION"]) || $arrayData["STEP_POSITION"] == "") {
                unset($arrayData["STEP_POSITION"]);
            }

            $arrayData = $this->update($stepUid, $arrayData);

            //Return
            unset($arrayData["STEP_UID"]);

            $arrayData = array_merge(array("STEP_UID" => $stepUid), $arrayData);

            if (!$this->formatFieldNameInUppercase) {
                $arrayData = array_change_key_case($arrayData, CASE_LOWER);
            }

            return $arrayData;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Update Step of a Task
     *
     * @param string $stepUid   Unique id of Step
     * @param array  $arrayData Data
     *
     * return array Return data of the Step updated
     */
    public function update($stepUid, $arrayData)
    {
        try {
            $arrayData = array_change_key_case($arrayData, CASE_UPPER);

            //Verify data
            $this->throwExceptionIfNotExistsStep($stepUid);

            //Load Step
            $step = new \Step();
            $arrayStepData = $step->load($stepUid);

            $taskUid = $arrayStepData["TAS_UID"];
            $proUid = $arrayStepData["PRO_UID"];

            //Verify data
            if (isset($arrayData["STEP_TYPE_OBJ"]) && !isset($arrayData["STEP_UID_OBJ"])) {
                throw new \Exception(\G::LoadTranslation("ID_UNDEFINED_VALUE_IS_REQUIRED", array($this->arrayParamException["stepUidObj"])));
            }

            if (!isset($arrayData["STEP_TYPE_OBJ"]) && isset($arrayData["STEP_UID_OBJ"])) {
                throw new \Exception(\G::LoadTranslation("ID_UNDEFINED_VALUE_IS_REQUIRED", array($this->arrayParamException["stepTypeObj"])));
            }

            if (isset($arrayData["STEP_TYPE_OBJ"])) {
                $arrayData["STEP_TYPE_OBJ"] = trim($arrayData["STEP_TYPE_OBJ"]);

                if ($arrayData["STEP_TYPE_OBJ"] == "") {
                    throw new \Exception(\G::LoadTranslation("ID_INVALID_VALUE_CAN_NOT_BE_EMPTY", array($this->arrayParamException["stepTypeObj"])));
                }
            }

            if (isset($arrayData["STEP_UID_OBJ"])) {
                $arrayData["STEP_UID_OBJ"] = trim($arrayData["STEP_UID_OBJ"]);

                if ($arrayData["STEP_UID_OBJ"] == "") {
                    throw new \Exception(\G::LoadTranslation("ID_INVALID_VALUE_CAN_NOT_BE_EMPTY", array($this->arrayParamException["stepUidObj"])));
                }
            }

            if (isset($arrayData["STEP_MODE"])) {
                $arrayData["STEP_MODE"] = trim($arrayData["STEP_MODE"]);

                if ($arrayData["STEP_MODE"] == "") {
                    throw new \Exception(\G::LoadTranslation("ID_INVALID_VALUE_CAN_NOT_BE_EMPTY", array($this->arrayParamException["stepMode"])));
                }
            }

            if (isset($arrayData["STEP_TYPE_OBJ"])) {
                $this->throwExceptionIfHaveInvalidValueInTypeObj($arrayData["STEP_TYPE_OBJ"]);
            }

            if (isset($arrayData["STEP_MODE"])) {
                $this->throwExceptionIfHaveInvalidValueInMode($arrayData["STEP_MODE"]);
            }

            if (isset($arrayData["STEP_TYPE_OBJ"]) && isset($arrayData["STEP_UID_OBJ"])) {
                $msg = $this->existsObjectUid($arrayData["STEP_TYPE_OBJ"], $arrayData["STEP_UID_OBJ"], $this->arrayParamException["stepUidObj"]);

                if ($msg != "") {
                    throw new \Exception($msg);
                }

                if ($this->existsRecord($taskUid, $arrayData["STEP_TYPE_OBJ"], $arrayData["STEP_UID_OBJ"], 0, $stepUid)) {
                    throw new \Exception(\G::LoadTranslation("ID_RECORD_EXISTS_IN_TABLE", array($taskUid . ", " . $arrayData["STEP_TYPE_OBJ"] . ", " . $arrayData["STEP_UID_OBJ"], "STEP")));
                }
            }

            //Update
            $step = new \Step();

            $arrayData["STEP_UID"] = $stepUid;
            $tempPosition = (isset($arrayData["STEP_POSITION"])) ? $arrayData["STEP_POSITION"] : $arrayStepData["STEP_POSITION"];
            $arrayData["STEP_POSITION"] = $arrayStepData["STEP_POSITION"];
            $result = $step->update($arrayData);

            if (isset($tempPosition) && ($tempPosition != $arrayStepData["STEP_POSITION"])) {
                $this->moveSteps($proUid, $taskUid, $stepUid, $tempPosition);
            }

            //Return
            unset($arrayData["STEP_UID"]);
            $arrayData["STEP_POSITION"] = $tempPosition;

            if (!$this->formatFieldNameInUppercase) {
                $arrayData = array_change_key_case($arrayData, CASE_LOWER);
            }

            return $arrayData;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Delete Step of a Task
     *
     * @param string $stepUid Unique id of Step
     *
     * return void
     */
    public function delete($stepUid)
    {
        try {

            //Verify data
            $this->throwExceptionIfNotExistsStep($stepUid);

            //Get position
            $criteria = new \Criteria("workflow");

            $criteria->add(\StepPeer::STEP_UID, $stepUid, \Criteria::EQUAL);

            $rsCriteria = \StepPeer::doSelectRS($criteria);
            $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

            $rsCriteria->next();

            $row = $rsCriteria->getRow();

            $position = (int)($row["STEP_POSITION"]);

            //Delete
            $step = new \Step();

            $step->reOrder($stepUid, $position);
            $step->remove($stepUid);
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
            $task = new \ProcessMaker\BusinessModel\Task();

            $task->throwExceptionIfNotExistsTask("", $taskUid, $this->arrayParamException["taskUid"]);

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
     * Get data of a Step
     *
     * @param string $stepUid Unique id of Step
     *
     * return array Return an array with data of a Step
     */
    public function getStep($stepUid)
    {
        try {
            $arrayStep = array();

            //Verify data
            $this->throwExceptionIfNotExistsStep($stepUid);

            //Get data
            //Call plugin
            $pluginRegistry = &\PMPluginRegistry::getSingleton();
            $externalSteps = $pluginRegistry->getSteps();

            $criteria = new \Criteria("workflow");

            $criteria->add(\StepPeer::STEP_UID, $stepUid, \Criteria::EQUAL);

            $rsCriteria = \StepPeer::doSelectRS($criteria);
            $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

            $rsCriteria->next();

            $row = $rsCriteria->getRow();

            $titleObj = "";
            $descriptionObj = "";

            switch ($row["STEP_TYPE_OBJ"]) {
                case "DYNAFORM":
                    $dynaform = new \Dynaform();
                    $arrayData = $dynaform->load($row["STEP_UID_OBJ"]);

                    $titleObj = $arrayData["DYN_TITLE"];
                    $descriptionObj = $arrayData["DYN_DESCRIPTION"];
                    break;
                case "INPUT_DOCUMENT":
                    $inputDocument = new \InputDocument();
                    $arrayData = $inputDocument->getByUid($row["STEP_UID_OBJ"]);

                    if ($arrayData === false) {
                        return $arrayStep;
                    }

                    $titleObj = $arrayData["INP_DOC_TITLE"];
                    $descriptionObj = $arrayData["INP_DOC_DESCRIPTION"];
                    break;
                case "OUTPUT_DOCUMENT":
                    $outputDocument = new \OutputDocument();
                    $arrayData = $outputDocument->getByUid($row["STEP_UID_OBJ"]);

                    if ($arrayData === false) {
                        return $arrayStep;
                    }

                    $titleObj = $arrayData["OUT_DOC_TITLE"];
                    $descriptionObj = $arrayData["OUT_DOC_DESCRIPTION"];
                    break;
                case "EXTERNAL":
                    $titleObj = "unknown " . $row["STEP_UID"];

                    if (is_array($externalSteps) && count($externalSteps) > 0) {
                        foreach ($externalSteps as $key => $value) {
                            if ($value->sStepId == $row["STEP_UID_OBJ"]) {
                                $titleObj = $value->sStepTitle;
                            }
                        }
                    }
                    break;
            }

            //Return
            $arrayStep = array(
                $this->getFieldNameByFormatFieldName("STEP_UID")        => $stepUid,
                $this->getFieldNameByFormatFieldName("STEP_TYPE_OBJ")   => $row["STEP_TYPE_OBJ"],
                $this->getFieldNameByFormatFieldName("STEP_UID_OBJ")    => $row["STEP_UID_OBJ"],
                $this->getFieldNameByFormatFieldName("STEP_CONDITION")  => $row["STEP_CONDITION"],
                $this->getFieldNameByFormatFieldName("STEP_POSITION")   => (int)($row["STEP_POSITION"]),
                $this->getFieldNameByFormatFieldName("STEP_MODE")       => $row["STEP_MODE"],
                $this->getFieldNameByFormatFieldName("OBJ_TITLE")       => $titleObj,
                $this->getFieldNameByFormatFieldName("OBJ_DESCRIPTION") => $descriptionObj
            );

            return $arrayStep;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get available Triggers of a Step
     *
     * @param string $stepUid Unique id of Step
     * @param string $type    Type (BEFORE, AFTER, BEFORE_ASSIGNMENT, BEFORE_ROUTING, AFTER_ROUTING)
     * @param string $taskUid Unique id of Task
     *
     * return array Return an array with the Triggers available of a Step
     */
    public function getAvailableTriggers($stepUid, $type, $taskUid = "")
    {
        try {
            $arrayAvailableTrigger = array();

            //Verify data
            if ($stepUid != "") {
                $this->throwExceptionIfNotExistsStep($stepUid);
            }

            if ($stepUid == "") {
                $task = new \ProcessMaker\BusinessModel\Task();

                $task->throwExceptionIfNotExistsTask("", $taskUid, $this->arrayParamException["taskUid"]);
            }

            //Get data
            $trigger = new \ProcessMaker\BusinessModel\Trigger();

            $flagStepAssignTask = 0;

            if ($stepUid != "") {
                //Load Step
                $step = new \Step();

                $arrayStepData = $step->load($stepUid);

                $processUid = $arrayStepData["PRO_UID"];
            } else {
                //Load Task
                $task = new \Task();

                $arrayTaskData = $task->load($taskUid);

                $processUid = $arrayTaskData["PRO_UID"];

                //Set variables
                $flagStepAssignTask = 1;

                switch ($type) {
                    case "BEFORE_ASSIGNMENT":
                        $stepUid = "-1";
                        $type = "BEFORE";
                        break;
                    case "BEFORE_ROUTING":
                        $stepUid = "-2";
                        $type = "BEFORE";
                        break;
                    case "AFTER_ROUTING":
                        $stepUid = "-2";
                        $type = "AFTER";
                        break;
                }
            }

            //Get data
            //Get Uids
            $arrayUid = array();

            $criteria = new \Criteria("workflow");

            $criteria->addSelectColumn(\StepTriggerPeer::TRI_UID);
            $criteria->add(\StepTriggerPeer::STEP_UID, $stepUid, \Criteria::EQUAL);

            if ($flagStepAssignTask == 1) {
                $criteria->add(\StepTriggerPeer::TAS_UID, $taskUid, \Criteria::EQUAL);
            }

            $criteria->add(\StepTriggerPeer::ST_TYPE, $type, \Criteria::EQUAL);

            $rsCriteria = \StepTriggerPeer::doSelectRS($criteria);
            $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

            while ($rsCriteria->next()) {
                $row = $rsCriteria->getRow();

                $arrayUid[] = $row["TRI_UID"];
            }

            //Criteria
            $criteria = $trigger->getTriggerCriteria();

            $criteria->add(\TriggersPeer::TRI_UID, $arrayUid, \Criteria::NOT_IN);
            $criteria->add(\TriggersPeer::PRO_UID, $processUid, \Criteria::EQUAL);
            $criteria->addAscendingOrderByColumn("TRI_TITLE");

            $rsCriteria = \TriggersPeer::doSelectRS($criteria);
            $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

            while ($rsCriteria->next()) {
                $row = $rsCriteria->getRow();

                $arrayAvailableTrigger[] = array(
                    $this->getFieldNameByFormatFieldName("TRI_UID")         => $row["TRI_UID"],
                    $this->getFieldNameByFormatFieldName("TRI_TITLE")       => $row["TRI_TITLE"],
                    $this->getFieldNameByFormatFieldName("TRI_DESCRIPTION") => $row["TRI_DESCRIPTION"],
                    $this->getFieldNameByFormatFieldName("TRI_TYPE")        => $row["TRI_TYPE"],
                    $this->getFieldNameByFormatFieldName("TRI_WEBBOT")      => $row["TRI_WEBBOT"],
                    $this->getFieldNameByFormatFieldName("TRI_PARAM")       => $row["TRI_PARAM"]
                );
            }

            //Return
            return $arrayAvailableTrigger;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get all Triggers of a Step
     *
     * @param string $stepUid Unique id of Step
     * @param string $taskUid Unique id of Task
     *
     * return array Return an array with all Triggers of a Step
     */
    public function getTriggers($stepUid, $taskUid = "")
    {
        try {
            $arrayTrigger = array();

            //Verify data
            if ($stepUid != "") {
                $this->throwExceptionIfNotExistsStep($stepUid);
            }

            if ($stepUid == "") {
                $task = new \ProcessMaker\BusinessModel\Task();

                $task->throwExceptionIfNotExistsTask("", $taskUid, $this->arrayParamException["taskUid"]);
            }

            //Get data
            $bmTrigger = new \ProcessMaker\BusinessModel\Trigger();
            $bmStepTrigger = new \ProcessMaker\BusinessModel\Step\Trigger();

            $stepTrigger = new \StepTrigger();

            if ($stepUid != "") {
                //Load Step
                $step = new \Step();

                $arrayStepData = $step->load($stepUid);

                $taskUid = $arrayStepData["TAS_UID"];
            }

            $arrayTriggerType1 = array(
                "BEFORE" => "BEFORE",
                "AFTER"  => "AFTER"
            );

            $arrayTriggerType2 = array(
                "BEFORE_ASSIGNMENT" => "BEFORE",
                "BEFORE_ROUTING"    => "BEFORE",
                "AFTER_ROUTING"     => "AFTER"
            );

            $arrayTriggerType = ($stepUid != "")? $arrayTriggerType1 : $arrayTriggerType2;

            foreach ($arrayTriggerType as $index => $value) {
                $triggerType = $index;
                $type = $value;

                $flagStepAssignTask = 0;

                switch ($triggerType) {
                    case "BEFORE_ASSIGNMENT":
                        $stepUid = "-1";
                        $flagStepAssignTask = 1;
                        break;
                    case "BEFORE_ROUTING":
                        $stepUid = "-2";
                        $flagStepAssignTask = 1;
                        break;
                    case "AFTER_ROUTING":
                        $stepUid = "-2";
                        $flagStepAssignTask = 1;
                        break;
                }

                $stepTrigger->orderPosition($stepUid, $taskUid, $type);

                //Criteria
                $criteria = $bmTrigger->getTriggerCriteria();

                $criteria->addSelectColumn(\StepTriggerPeer::ST_TYPE);
                $criteria->addSelectColumn(\StepTriggerPeer::ST_CONDITION);
                $criteria->addSelectColumn(\StepTriggerPeer::ST_POSITION);
                $criteria->addJoin(\StepTriggerPeer::TRI_UID, \TriggersPeer::TRI_UID, \Criteria::LEFT_JOIN);
                $criteria->add(\StepTriggerPeer::STEP_UID, $stepUid, \Criteria::EQUAL);
                $criteria->add(\StepTriggerPeer::TAS_UID, $taskUid, \Criteria::EQUAL);
                $criteria->add(\StepTriggerPeer::ST_TYPE, $type, \Criteria::EQUAL);
                $criteria->addAscendingOrderByColumn(\StepTriggerPeer::ST_POSITION);

                $rsCriteria = \StepTriggerPeer::doSelectRS($criteria);
                $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

                while ($rsCriteria->next()) {
                    $row = $rsCriteria->getRow();

                    if ($flagStepAssignTask == 1) {
                        $row["ST_TYPE"] = $triggerType;
                    }

                    $arrayTrigger[] = $bmStepTrigger->getTriggerDataFromRecord($row);
                }
            }

            return $arrayTrigger;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Validate Process Uid
     * @var string $pro_uid. Uid for Process
     * @var string $tas_uid. Uid for Task
     * @var string $step_uid. Uid for Step
     * @var string $step_pos. Position for Step
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @return void
     */
    public function moveSteps($pro_uid, $tas_uid, $step_uid, $step_pos)
    {
        $this->setFormatFieldNameInUppercase(false);
        $this->setArrayParamException(array("taskUid" => "act_uid"));
        $aSteps = $this->getSteps($tas_uid);

        foreach ($aSteps as $dataStep) {
            if ($dataStep['step_uid'] == $step_uid) {
                $prStepPos = (int)$dataStep['step_position'];
            }
        }
        $seStepPos = $step_pos;

        //Principal Step is up
        if ($prStepPos == $seStepPos) {
            return true;
        } elseif ($prStepPos < $seStepPos) {
            $modPos = 'UP';
            $newPos = $seStepPos;
            $iniPos = $prStepPos+1;
            $finPos = $seStepPos;
        } else {
            $modPos = 'DOWN';
            $newPos = $seStepPos;
            $iniPos = $seStepPos;
            $finPos = $prStepPos-1;
        }

        $range = range($iniPos, $finPos);
        foreach ($aSteps as $dataStep) {
            if ((in_array($dataStep['step_position'], $range)) && ($dataStep['step_uid'] != $step_uid)) {
                $stepChangeIds[] = $dataStep['step_uid'];
                $stepChangePos[] = $dataStep['step_position'];
            }
        }

        foreach ($stepChangeIds as $key => $value) {
            if ($modPos == 'UP') {
                $tempPos = ((int)$stepChangePos[$key])-1;
                $this->changePosStep($value, $tempPos);
            } else {
                $tempPos = ((int)$stepChangePos[$key])+1;
                $this->changePosStep($value, $tempPos);
            }
        }
        $this->changePosStep($step_uid, $newPos);
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
    public function changePosStep ($step_uid, $pos)
    {
        $data = array(
            'STEP_UID' => $step_uid,
            'STEP_POSITION' => $pos
        );
        $oStep = new \Step();
        $oStep->update($data);
    }

    /**
     * Create Steps for a Task
     *
     * @param string $taskUid    Unique id of Task
     * @param string $processUid Unique id of Process
     * @param array  $arrayData  Data
     *
     * return array Return data of the new Steps created
     */
    public function createAll($taskUid, $processUid, $arrayData)
    {
        try {
            $stepTrigger = new \ProcessMaker\BusinessModel\Step\Trigger();

            $arrayData = array_change_key_case($arrayData, CASE_UPPER);

            unset($arrayData["STEP_UID"]);

            //Verify data
            $task = new \ProcessMaker\BusinessModel\Task();

            $this->throwExceptionIfNotExistsProcess($processUid);

            $task->throwExceptionIfNotExistsTask($processUid, $taskUid, $this->arrayParamException["taskUid"]);

            if (!isset($arrayData["STEP_TYPE_OBJ"])) {
                throw new \Exception(\G::LoadTranslation("ID_UNDEFINED_VALUE_IS_REQUIRED", array($this->arrayParamException["stepTypeObj"])));
            }

            $arrayData["STEP_TYPE_OBJ"] = trim($arrayData["STEP_TYPE_OBJ"]);

            if ($arrayData["STEP_TYPE_OBJ"] == "") {
                throw new \Exception(\G::LoadTranslation("ID_INVALID_VALUE_CAN_NOT_BE_EMPTY", array($this->arrayParamException["stepTypeObj"])));
            }

            if (!isset($arrayData["STEP_UID_OBJ"])) {
                throw new \Exception(\G::LoadTranslation("ID_UNDEFINED_VALUE_IS_REQUIRED", array($this->arrayParamException["stepUidObj"])));
            }

            $arrayData["STEP_UID_OBJ"] = trim($arrayData["STEP_UID_OBJ"]);

            if ($arrayData["STEP_UID_OBJ"] == "") {
                throw new \Exception(\G::LoadTranslation("ID_INVALID_VALUE_CAN_NOT_BE_EMPTY", array($this->arrayParamException["stepUidObj"])));
            }

            if (!isset($arrayData["STEP_MODE"])) {
                throw new \Exception(\G::LoadTranslation("ID_UNDEFINED_VALUE_IS_REQUIRED", array($this->arrayParamException["stepMode"])));
            }

            $arrayData["STEP_MODE"] = trim($arrayData["STEP_MODE"]);

            if ($arrayData["STEP_MODE"] == "") {
                throw new \Exception(\G::LoadTranslation("ID_INVALID_VALUE_CAN_NOT_BE_EMPTY", array($this->arrayParamException["stepMode"])));
            }

            $this->throwExceptionIfHaveInvalidValueInTypeObj($arrayData["STEP_TYPE_OBJ"]);

            $this->throwExceptionIfHaveInvalidValueInMode($arrayData["STEP_MODE"]);

            $msg = $this->existsObjectUid($arrayData["STEP_TYPE_OBJ"], $arrayData["STEP_UID_OBJ"], $this->arrayParamException["stepUidObj"]);

            if ($msg != "") {
                throw new \Exception($msg);
            }

            if ($this->existsRecord($taskUid, $arrayData["STEP_TYPE_OBJ"], $arrayData["STEP_UID_OBJ"])) {
                throw new \Exception(\G::LoadTranslation("ID_RECORD_EXISTS_IN_TABLE", array($taskUid . ", " . $arrayData["STEP_TYPE_OBJ"] . ", " . $arrayData["STEP_UID_OBJ"], "STEP")));
            }

            //Create
            $step = new \Step();

            $stepUid = $step->create(array(
                "PRO_UID" => $processUid,
                "TAS_UID" => $taskUid,
                "STEP_POSITION" => $arrayData["STEP_POSITION"]
            ));

            if (!isset($arrayData["STEP_POSITION"]) || $arrayData["STEP_POSITION"] == "") {
                unset($arrayData["STEP_POSITION"]);
            }

            $arrayData = $this->updateAll($stepUid, $arrayData);

            //Return
            unset($arrayData["STEP_UID"]);

            $arrayData = array_merge(array("STEP_UID" => $stepUid), $arrayData);

            if (!$this->formatFieldNameInUppercase) {
                $arrayData = array_change_key_case($arrayData, CASE_LOWER);
            }

            return $arrayData;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Update Steps of a Task
     *
     * @param string $stepUid   Unique id of Step
     * @param array  $arrayData Data
     *
     * return array Return data of the Steps updated
     */
    public function updateAll($stepUid, $arrayData)
    {
        try {
            $arrayData = array_change_key_case($arrayData, CASE_UPPER);

            //Verify data
            $this->throwExceptionIfNotExistsStep($stepUid);

            //Load Step
            $step = new \Step();
            $arrayStepData = $step->load($stepUid);

            $taskUid = $arrayStepData["TAS_UID"];
            $proUid = $arrayStepData["PRO_UID"];

            //Verify data
            if (isset($arrayData["STEP_TYPE_OBJ"]) && !isset($arrayData["STEP_UID_OBJ"])) {
                throw new \Exception(\G::LoadTranslation("ID_UNDEFINED_VALUE_IS_REQUIRED", array($this->arrayParamException["stepUidObj"])));
            }

            if (!isset($arrayData["STEP_TYPE_OBJ"]) && isset($arrayData["STEP_UID_OBJ"])) {
                throw new \Exception(\G::LoadTranslation("ID_UNDEFINED_VALUE_IS_REQUIRED", array($this->arrayParamException["stepTypeObj"])));
            }

            if (isset($arrayData["STEP_TYPE_OBJ"])) {
                $arrayData["STEP_TYPE_OBJ"] = trim($arrayData["STEP_TYPE_OBJ"]);

                if ($arrayData["STEP_TYPE_OBJ"] == "") {
                    throw new \Exception(\G::LoadTranslation("ID_INVALID_VALUE_CAN_NOT_BE_EMPTY", array($this->arrayParamException["stepTypeObj"])));
                }
            }

            if (isset($arrayData["STEP_UID_OBJ"])) {
                $arrayData["STEP_UID_OBJ"] = trim($arrayData["STEP_UID_OBJ"]);

                if ($arrayData["STEP_UID_OBJ"] == "") {
                    throw new \Exception(\G::LoadTranslation("ID_INVALID_VALUE_CAN_NOT_BE_EMPTY", array($this->arrayParamException["stepUidObj"])));
                }
            }

            if (isset($arrayData["STEP_MODE"])) {
                $arrayData["STEP_MODE"] = trim($arrayData["STEP_MODE"]);

                if ($arrayData["STEP_MODE"] == "") {
                    throw new \Exception(\G::LoadTranslation("ID_INVALID_VALUE_CAN_NOT_BE_EMPTY", array($this->arrayParamException["stepMode"])));
                }
            }

            if (isset($arrayData["STEP_TYPE_OBJ"])) {
                $this->throwExceptionIfHaveInvalidValueInTypeObj($arrayData["STEP_TYPE_OBJ"]);
            }

            if (isset($arrayData["STEP_MODE"])) {
                $this->throwExceptionIfHaveInvalidValueInMode($arrayData["STEP_MODE"]);
            }

            if (isset($arrayData["STEP_TYPE_OBJ"]) && isset($arrayData["STEP_UID_OBJ"])) {
                $msg = $this->existsObjectUid($arrayData["STEP_TYPE_OBJ"], $arrayData["STEP_UID_OBJ"], $this->arrayParamException["stepUidObj"]);

                if ($msg != "") {
                    throw new \Exception($msg);
                }

                if ($this->existsRecord($taskUid, $arrayData["STEP_TYPE_OBJ"], $arrayData["STEP_UID_OBJ"], 0, $stepUid)) {
                    throw new \Exception(\G::LoadTranslation("ID_RECORD_EXISTS_IN_TABLE", array($taskUid . ", " . $arrayData["STEP_TYPE_OBJ"] . ", " . $arrayData["STEP_UID_OBJ"], "STEP")));
                }
            }

            //Update
            $step = new \Step();

            $arrayData["STEP_UID"] = $stepUid;
            $result = $step->update($arrayData);

            //Return
            unset($arrayData["STEP_UID"]);

            if (!$this->formatFieldNameInUppercase) {
                $arrayData = array_change_key_case($arrayData, CASE_LOWER);
            }

            return $arrayData;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Delete Steps and triggers of a Task
     *
     * @param string $stepUid Unique id of Step
     * @param string $taskUid Unique id of Step
     *
     * return void
     */
    public function deleteAll($taskUid)
    {
        try {
            $step = new \Step();
            $stepTrigger = new \ProcessMaker\BusinessModel\Step\Trigger();

            $criteriaTrigger = new \Criteria("workflow");
            $criteriaTrigger->addSelectColumn(\StepTriggerPeer::STEP_UID);
            $criteriaTrigger->addSelectColumn(\StepTriggerPeer::ST_TYPE);
            $criteriaTrigger->addSelectColumn(\StepTriggerPeer::TRI_UID);
            $criteriaTrigger->add(\StepTriggerPeer::TAS_UID, $taskUid, \Criteria::EQUAL);
            $rsCriteriaTrigger = \StepTriggerPeer::doSelectRS($criteriaTrigger);
            $rsCriteriaTrigger->setFetchmode(\ResultSet::FETCHMODE_ASSOC);
            $rsCriteriaTrigger->next();

            while ($aRowTrigger = $rsCriteriaTrigger->getRow()) {

                $stepTrigger->delete($aRowTrigger['STEP_UID'], $aRowTrigger['ST_TYPE'], $taskUid, $aRowTrigger['TRI_UID']);
                $rsCriteriaTrigger->next();
            }

            $criteria = new \Criteria("workflow");
            $criteria->addSelectColumn(\StepPeer::STEP_UID);
            $criteria->add(\StepPeer::TAS_UID, $taskUid, \Criteria::EQUAL);
            $rsCriteria = \StepPeer::doSelectRS($criteria);
            $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);
            $rsCriteria->next();

            while ($aRow = $rsCriteria->getRow()) {
                $step->remove($aRow['STEP_UID']);
                $rsCriteria->next();
            }

        } catch (\Exception $e) {
            throw $e;
        }
    }
}

