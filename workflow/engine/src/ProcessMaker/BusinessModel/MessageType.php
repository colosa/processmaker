<?php
namespace ProcessMaker\BusinessModel;

class MessageType
{
    private $arrayFieldDefinition = array(
        "MSGT_UID"       => array("type" => "string", "required" => false, "empty" => false, "defaultValues" => array(), "fieldNameAux" => "messageTypeUid"),
        "PRJ_UID"        => array("type" => "string", "required" => false, "empty" => false, "defaultValues" => array(), "fieldNameAux" => "projectUid"),
        "MSGT_NAME"      => array("type" => "string", "required" => true,  "empty" => false, "defaultValues" => array(), "fieldNameAux" => "messageTypeName"),
        "MSGT_VARIABLES" => array("type" => "array",  "required" => false, "empty" => true,  "defaultValues" => array(), "fieldNameAux" => "messageTypeVariables")
    );

    private $formatFieldNameInUppercase = true;

    private $arrayFieldNameForException = array(
        "start"  => "START",
        "limit"  => "LIMIT"
    );

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
     * Set exception messageTypes for fields
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
            return ($this->formatFieldNameInUppercase)? strtoupper($fieldName) : strtolower($fieldName);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Verify if exists the Message-Type
     *
     * @param string $messageTypeUid Unique id of Message-Type
     *
     * return bool Return true if exists the Message-Type, false otherwise
     */
    public function exists($messageTypeUid)
    {
        try {
            $obj = \MessageTypePeer::retrieveByPK($messageTypeUid);

            return (!is_null($obj))? true : false;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Verify if exists the Name of a Message-Type
     *
     * @param string $projectUid              Unique id of Project
     * @param string $messageTypeName         Name
     * @param string $messageTypeUidToExclude Unique id of Message to exclude
     *
     * return bool Return true if exists the Name of a Message-Type, false otherwise
     */
    public function existsName($projectUid, $messageTypeName, $messageTypeUidToExclude = "")
    {
        try {
            $criteria = $this->getMessageTypeCriteria();

            if ($messageTypeUidToExclude != "") {
                $criteria->add(\MessageTypePeer::MSGT_UID, $messageTypeUidToExclude, \Criteria::NOT_EQUAL);
            }

            $criteria->add(\MessageTypePeer::PRJ_UID, $projectUid, \Criteria::EQUAL);
            $criteria->add(\MessageTypePeer::MSGT_NAME, $messageTypeName, \Criteria::EQUAL);

            //QUERY
            $rsCriteria = \MessageTypePeer::doSelectRS($criteria);

            return ($rsCriteria->next())? true : false;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function throwExceptionCheckIfThereIsRepeatedVariableName(array $arrayDataVariables)
    {
        try {
            $i = 0;
            $arrayDataVarAux = $arrayDataVariables;

            while ($i <= count($arrayDataVariables) - 1) {
                if (array_key_exists("MSGTV_NAME", $arrayDataVariables[$i])) {
                    $msgtvNameAux = $arrayDataVariables[$i]["MSGTV_NAME"];
                    $counter = 0;

                    foreach ($arrayDataVarAux as $key => $value) {
                        if ($value["MSGTV_NAME"] == $msgtvNameAux) {
                            $counter = $counter + 1;
                        }
                    }

                    if ($counter > 1) {
                        throw new \Exception(\G::LoadTranslation("ID_MESSAGE_TYPE_NAME_VARIABLE_EXISTS", array($value["MSGTV_NAME"])));
                    }
                }

                $i = $i + 1;
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Verify if exists the Name of a Message-Type
     *
     * @param string $projectUid              Unique id of Project
     * @param string $messageTypeName         Name
     * @param string $fieldNameForException   Field name for the exception
     * @param string $messageTypeUidToExclude Unique id of Message-Type to exclude
     *
     * return void Throw exception if exists the title of a Message-Type
     */
    public function throwExceptionIfExistsName($projectUid, $messageTypeName, $fieldNameForException, $messageTypeUidToExclude = "")
    {
        try {
            if ($this->existsName($projectUid, $messageTypeName, $messageTypeUidToExclude)) {
                throw new \Exception(\G::LoadTranslation("ID_MESSAGE_TYPE_NAME_ALREADY_EXISTS", array($fieldNameForException, $messageTypeName)));
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Validate the data if they are invalid (INSERT and UPDATE)
     *
     * @param string $messageTypeUid Unique id of Message-Type
     * @param string $projectUid     Unique id of Project
     * @param array  $arrayData      Data
     *
     * return void Throw exception if data has an invalid value
     */
    public function throwExceptionIfDataIsInvalid($messageTypeUid, $projectUid, array $arrayData)
    {
        try {
            //Set variables
            $arrayMessageTypeData = ($messageTypeUid == "")? array() : $this->getMessageType($messageTypeUid, true);
            $flagInsert           = ($messageTypeUid == "")? true : false;

            $arrayFinalData = array_merge($arrayMessageTypeData, $arrayData);

            //Verify data - Field definition
            $process = new \ProcessMaker\BusinessModel\Process();

            $process->throwExceptionIfDataNotMetFieldDefinition($arrayData, $this->arrayFieldDefinition, $this->arrayFieldNameForException, $flagInsert);

            //Verify data
            if (isset($arrayData["MSGT_NAME"])) {
                $this->throwExceptionIfExistsName($projectUid, $arrayData["MSGT_NAME"], $this->arrayFieldNameForException["messageTypeName"], $messageTypeUid);
            }

            if (isset($arrayData["MSGT_VARIABLES"]) && count($arrayData["MSGT_VARIABLES"]) > 0) {
                $this->throwExceptionCheckIfThereIsRepeatedVariableName($arrayData["MSGT_VARIABLES"]);
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Verify if does not exist the Message-Type
     *
     * @param string $messageTypeUid        Unique id of Message-Type
     * @param string $fieldNameForException Field name for the exception
     *
     * return void Throw exception if does not exist the Message-Type
     */
    public function throwExceptionIfNotExistsMessageType($messageTypeUid, $fieldNameForException)
    {
        try {
            $obj = \MessageTypePeer::retrieveByPK($messageTypeUid);

            if (!$this->exists($messageTypeUid)) {
                throw new \Exception(\G::LoadTranslation("ID_MESSAGE_TYPE_DOES_NOT_EXIST", array($fieldNameForException, $messageTypeUid)));
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Create Message-Type
     *
     * @param string $projectUid Unique id of Project
     * @param array  $arrayData  Data
     *
     * return array Return data of the new Message-Type created
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

            if (isset($arrayData["MSGT_VARIABLES"]) && is_array($arrayData["MSGT_VARIABLES"])) {
                foreach ($arrayData["MSGT_VARIABLES"] as $key => $value) {
                    $arrayData["MSGT_VARIABLES"][$key] = array_change_key_case($value, CASE_UPPER);
                }
            }

            unset($arrayData["MSGT_UID"]);
            unset($arrayData["PRJ_UID"]);

            $process->throwExceptionIfNotExistsProcess($projectUid, $this->arrayFieldNameForException["projectUid"]);

            $this->throwExceptionIfDataIsInvalid("", $projectUid, $arrayData);

            //Create
            $cnn = \Propel::getConnection("workflow");

            try {
                $messageType = new \MessageType();

                $messageType->fromArray($arrayData, \BasePeer::TYPE_FIELDNAME);

                $messageTypeUid = \ProcessMaker\Util\Common::generateUID();

                $messageType->setMsgtUid($messageTypeUid);
                $messageType->setPrjUid($projectUid);

                if ($messageType->validate()) {
                    $cnn->begin();

                    $result = $messageType->save();

                    $cnn->commit();

                    if (isset($arrayData["MSGT_VARIABLES"]) && count($arrayData["MSGT_VARIABLES"]) > 0) {
                        $variable = new \ProcessMaker\BusinessModel\MessageType\Variable();
                        $variable->setFormatFieldNameInUppercase($this->formatFieldNameInUppercase);

                        foreach ($arrayData["MSGT_VARIABLES"] as $key => $value) {
                            $arrayVariable = $value;

                            $arrayResult = $variable->create($messageTypeUid, $arrayVariable);
                        }
                    }

                    //Return
                    return $this->getMessageType($messageTypeUid);
                } else {
                    $msg = "";

                    foreach ($messageType->getValidationFailures() as $validationFailure) {
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
     * Single create Message-Type
     *
     * @param array $arrayData Data
     *
     * return int Return integer
     */
    public function singleCreate(array $arrayData)
    {
        try {
            $cnn = \Propel::getConnection("workflow");

            try {
                $messageType = new \MessageType();

                $messageType->fromArray($arrayData, \BasePeer::TYPE_FIELDNAME);

                if ($messageType->validate()) {
                    $cnn->begin();

                    $result = $messageType->save();

                    $cnn->commit();

                    //Return
                    return $result;
                } else {
                    $msg = "";

                    foreach ($messageType->getValidationFailures() as $validationFailure) {
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
     * Update Message-Type
     *
     * @param string $messageTypeUid Unique id of Message-Type
     * @param array  $arrayData      Data
     *
     * return array Return data of the Message-Type updated
     */
    public function update($messageTypeUid, $arrayData)
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

            unset($arrayData["MSGT_UID"]);
            unset($arrayData["PRJ_UID"]);

            if (isset($arrayData["MSGT_VARIABLES"]) && is_array($arrayData["MSGT_VARIABLES"])) {
                foreach ($arrayData["MSGT_VARIABLES"] as $key => $value) {
                    $arrayData["MSGT_VARIABLES"][$key] = array_change_key_case($value, CASE_UPPER);
                }
            }

            //Set variables
            $arrayMessageTypeData = $this->getMessageType($messageTypeUid, true);

            //Verify data
            $this->throwExceptionIfNotExistsMessageType($messageTypeUid, $this->arrayFieldNameForException["messageTypeUid"]);

            $this->throwExceptionIfDataIsInvalid($messageTypeUid, $arrayMessageTypeData["PRJ_UID"], $arrayData);

            //Update
            $cnn = \Propel::getConnection("workflow");

            try {
                $messageType = \MessageTypePeer::retrieveByPK($messageTypeUid);
                $messageType->fromArray($arrayData, \BasePeer::TYPE_FIELDNAME);

                if ($messageType->validate()) {
                    $cnn->begin();

                    $result = $messageType->save();

                    $cnn->commit();

                    //----- *****
                    $variable = new \ProcessMaker\BusinessModel\MessageType\Variable();
                    $variable->setFormatFieldNameInUppercase($this->formatFieldNameInUppercase);

                    $criteria = new \Criteria("workflow");

                    $criteria->add(\MessageTypeVariablePeer::MSGT_UID, $messageTypeUid, \Criteria::EQUAL);

                    \MessageTypeVariablePeer::doDelete($criteria);

                    if (isset($arrayData["MSGT_VARIABLES"]) && count($arrayData["MSGT_VARIABLES"]) > 0) {
                        $variable = new \ProcessMaker\BusinessModel\MessageType\Variable();
                        $variable->setFormatFieldNameInUppercase($this->formatFieldNameInUppercase);

                        foreach ($arrayData["MSGT_VARIABLES"] as $key => $value) {
                            $arrayVariable = $value;

                            $arrayResult = $variable->create($messageTypeUid, $arrayVariable);
                        }
                    }
                    //----- *****

                    //Return
                    $arrayData = $arrayDataBackup;

                    if (!$this->formatFieldNameInUppercase) {
                        $arrayData = array_change_key_case($arrayData, CASE_LOWER);
                    }

                    return $arrayData;
                } else {
                    $msg = "";

                    foreach ($messageType->getValidationFailures() as $validationFailure) {
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
     * Delete Message-Type
     *
     * @param string $messageTypeUid Unique id of Message-Type
     *
     * return void
     */
    public function delete($messageTypeUid)
    {
        try {
            $this->throwExceptionIfNotExistsMessageType($messageTypeUid, $this->arrayFieldNameForException["messageTypeUid"]);

            //Delete Message-Type-Variable
            $criteria = new \Criteria("workflow");

            $criteria->add(\MessageTypeVariablePeer::MSGT_UID, $messageTypeUid, \Criteria::EQUAL);

            \MessageTypeVariablePeer::doDelete($criteria);

            //Delete Message-Type
            $criteria = $this->getMessageTypeCriteria();

            $criteria->add(\MessageTypePeer::MSGT_UID, $messageTypeUid, \Criteria::EQUAL);

            \MessageTypePeer::doDelete($criteria);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get criteria for Message-Type
     *
     * return object
     */
    public function getMessageTypeCriteria()
    {
        try {
            $criteria = new \Criteria("workflow");

            $criteria->addSelectColumn(\MessageTypePeer::MSGT_UID);
            $criteria->addSelectColumn(\MessageTypePeer::PRJ_UID);
            $criteria->addSelectColumn(\MessageTypePeer::MSGT_NAME);

            return $criteria;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get data of a from a record
     *
     * @param array $record Record
     *
     * return array Return an array with data Message-Type
     */
    public function getMessageTypeDataFromRecord(array $record)
    {
        try {
            return array(
                $this->getFieldNameByFormatFieldName("MSGT_UID")       => $record["MSGT_UID"],
                $this->getFieldNameByFormatFieldName("MSGT_NAME")      => $record["MSGT_NAME"],
                $this->getFieldNameByFormatFieldName("MSGT_VARIABLES") => $record["MSGT_VARIABLES"]
            );
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get all Message-Type
     *
     * @param string $projectUid      {@min 32}{@max 32}
     * @param array  $arrayFilterData Data of the filters
     * @param string $sortField       Field name to sort
     * @param string $sortDir         Direction of sorting (ASC, DESC)
     * @param int    $start           Start
     * @param int    $limit           Limit
     *
     * return array Return an array with all Message
     */
    public function getMessageTypes($projectUid, $arrayFilterData = null, $sortField = null, $sortDir = null, $start = null, $limit = null)
    {
        try {
            $arrayMessage = array();

            //Verify data
            $process = new \ProcessMaker\BusinessModel\Process();

            $process->throwExceptionIfNotExistsProcess($projectUid, $this->arrayFieldNameForException["projectUid"]);

            $process->throwExceptionIfDataNotMetPagerVarDefinition(array("start" => $start, "limit" => $limit), $this->arrayFieldNameForException);

            //Get data
            if (!is_null($limit) && $limit . "" == "0") {
                return $arrayMessage;
            }

            //SQL
            $criteria = $this->getMessageTypeCriteria();
            $criteria->add(\MessageTypePeer::PRJ_UID, $projectUid, \Criteria::EQUAL);

            if (!is_null($arrayFilterData) && is_array($arrayFilterData) && isset($arrayFilterData["filter"]) && trim($arrayFilterData["filter"]) != "") {
                $criteria->add(
                    $criteria->getNewCriterion(\MessageTypePeer::MSGT_NAME, "%" . $arrayFilterData["filter"] . "%", \Criteria::LIKE)
                );
            }

            //Number records total
            $criteriaCount = clone $criteria;

            $criteriaCount->clearSelectColumns();
            $criteriaCount->addSelectColumn("COUNT(" . \MessageTypePeer::MSGT_UID . ") AS NUM_REC");

            $rsCriteriaCount = \MessageTypePeer::doSelectRS($criteriaCount);
            $rsCriteriaCount->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

            $rsCriteriaCount->next();
            $row = $rsCriteriaCount->getRow();

            $numRecTotal = $row["NUM_REC"];

            //SQL
            if (!is_null($sortField) && trim($sortField) != "") {
                $sortField = strtoupper($sortField);

                if (in_array($sortField, array("MSGT_NAME"))) {
                    $sortField = \MessageTypePeer::TABLE_NAME . "." . $sortField;
                } else {
                    $sortField = \MessageTypePeer::MSGT_NAME;
                }
            } else {
                $sortField = \MessageTypePeer::MSGT_NAME;
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

            $rsCriteria = \MessageTypePeer::doSelectRS($criteria);
            $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

            while ($rsCriteria->next()) {
                $row = $rsCriteria->getRow();

                $arrayVariable = array();

                $variable = new \ProcessMaker\BusinessModel\MessageType\Variable();
                $variable->setFormatFieldNameInUppercase($this->formatFieldNameInUppercase);

                $criteriaMessageTypeVariable = $variable->getMessageTypeVariableCriteria();
                $criteriaMessageTypeVariable->add(\MessageTypeVariablePeer::MSGT_UID, $row["MSGT_UID"], \Criteria::EQUAL);
                $criteriaMessageTypeVariable->addAscendingOrderByColumn(\MessageTypeVariablePeer::MSGTV_NAME);

                $rsCriteriaMessageTypeVariable = \MessageTypeVariablePeer::doSelectRS($criteriaMessageTypeVariable);
                $rsCriteriaMessageTypeVariable->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

                while ($rsCriteriaMessageTypeVariable->next()) {
                    $row2 = $rsCriteriaMessageTypeVariable->getRow();

                    $arrayVariable[] = $variable->getMessageTypeVariableDataFromRecord($row2, false);
                }

                $row["MSGT_VARIABLES"] = $arrayVariable;

                $arrayMessage[] = $this->getMessageTypeDataFromRecord($row);
            }

            //Return
            return array(
                "total"  => $numRecTotal,
                "start"  => (int)((!is_null($start))? $start : 0),
                "limit"  => (int)((!is_null($limit))? $limit : 0),
                "filter" => (!is_null($arrayFilterData) && is_array($arrayFilterData) && isset($arrayFilterData["filter"]))? $arrayFilterData["filter"] : "",
                "data"   => $arrayMessage
            );
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get data of a Message-Type
     *
     * @param string $messageTypeUid Unique id of Message-Type
     * @param bool   $flagGetRecord  Value that set the getting
     *
     * return array Return an array with data of a Message-Type
     */
    public function getMessageType($messageTypeUid, $flagGetRecord = false)
    {
        try {
            //Verify data
            $this->throwExceptionIfNotExistsMessageType($messageTypeUid, $this->arrayFieldNameForException["messageTypeUid"]);

            //Get data
            //SQL
            $criteria = $this->getMessageTypeCriteria();

            $criteria->add(\MessageTypePeer::MSGT_UID, $messageTypeUid, \Criteria::EQUAL);

            $rsCriteria = \MessageTypePeer::doSelectRS($criteria);
            $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

            $rsCriteria->next();

            $row = $rsCriteria->getRow();

            //Variable
            $arrayVariable = array();

            $variable = new \ProcessMaker\BusinessModel\MessageType\Variable();
            $variable->setFormatFieldNameInUppercase($this->formatFieldNameInUppercase);

            $criteriaMessageTypeVariable = $variable->getMessageTypeVariableCriteria();
            $criteriaMessageTypeVariable->add(\MessageTypeVariablePeer::MSGT_UID, $messageTypeUid, \Criteria::EQUAL);

            $rsCriteriaMessageTypeVariable = \MessageTypeVariablePeer::doSelectRS($criteriaMessageTypeVariable);
            $rsCriteriaMessageTypeVariable->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

            while ($rsCriteriaMessageTypeVariable->next()) {
                $row2 = $rsCriteriaMessageTypeVariable->getRow();

                if (!$flagGetRecord) {
                    $arrayVariable[] = $variable->getMessageTypeVariableDataFromRecord($row2, false);
                } else {
                    unset($row2["MSGTV_UID"]);

                    $arrayVariable[] = $row2;
                }
            }

            $row["MSGT_VARIABLES"] = $arrayVariable;

            //Return
            return (!$flagGetRecord)? $this->getMessageTypeDataFromRecord($row) : $row;
        } catch (\Exception $e) {
            throw $e;
        }
    }
}

