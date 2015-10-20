<?php
namespace ProcessMaker\BusinessModel;

use \G;

class Variable
{
    /**
     * Create Variable for a Process
     *
     * @param string $processUid Unique id of Process
     * @param array  $arrayData  Data
     *
     * return array Return data of the new Variable created
     */
    public function create($processUid, array $arrayData)
    {
        try {
            //Verify data
            Validator::proUid($processUid, '$prj_uid');

            $arrayData = array_change_key_case($arrayData, CASE_UPPER);

            $this->existsName($processUid, $arrayData["VAR_NAME"], "");

            $this->throwExceptionFieldDefinition($arrayData);

            //Create
            $cnn = \Propel::getConnection("workflow");
            try {
                $variable = new \ProcessVariables();

                $sPkProcessVariables = \ProcessMaker\Util\Common::generateUID();

                $variable->setVarUid($sPkProcessVariables);
                $variable->setPrjUid($processUid);

                if ($variable->validate()) {
                    $cnn->begin();

                    if (isset($arrayData["VAR_NAME"])) {
                        $variable->setVarName($arrayData["VAR_NAME"]);
                    } else {
                        throw new \Exception(\G::LoadTranslation("ID_CAN_NOT_BE_NULL", array('$var_name' )));
                    }
                    if (isset($arrayData["VAR_FIELD_TYPE"])) {
                        $variable->setVarFieldType($arrayData["VAR_FIELD_TYPE"]);
                    } else {
                        throw new \Exception(\G::LoadTranslation("ID_CAN_NOT_BE_NULL", array('$var_field_type' )));
                    }
                    if (isset($arrayData["VAR_FIELD_SIZE"])) {
                        $variable->setVarFieldSize($arrayData["VAR_FIELD_SIZE"]);
                    }
                    if (isset($arrayData["VAR_LABEL"])) {
                        $variable->setVarLabel($arrayData["VAR_LABEL"]);
                    } else {
                        throw new \Exception(\G::LoadTranslation("ID_CAN_NOT_BE_NULL", array('$var_label' )));
                    }
                    if (isset($arrayData["VAR_DBCONNECTION"])) {
                        $variable->setVarDbconnection($arrayData["VAR_DBCONNECTION"]);
                    } else {
                        $variable->setVarDbconnection("");
                    }
                    if (isset($arrayData["VAR_SQL"])) {
                        $variable->setVarSql($arrayData["VAR_SQL"]);
                    } else {
                        $variable->setVarSql("");
                    }
                    if (isset($arrayData["VAR_NULL"])) {
                        $variable->setVarNull($arrayData["VAR_NULL"]);
                    } else {
                        $variable->setVarNull(0);
                    }
                    if (isset($arrayData["VAR_DEFAULT"])) {
                        $variable->setVarDefault($arrayData["VAR_DEFAULT"]);
                    }
                    if (isset($arrayData["VAR_ACCEPTED_VALUES"])) {
                        $encodeAcceptedValues = \G::json_encode($arrayData["VAR_ACCEPTED_VALUES"]);
                        $variable->setVarAcceptedValues($encodeAcceptedValues);
                    }
                    if (isset($arrayData["INP_DOC_UID"])) {
                        $variable->setInpDocUid($arrayData["INP_DOC_UID"]);
                    }
                    $variable->save();
                    $cnn->commit();
                } else {

                    $msg = "";

                    foreach ($variable->getValidationFailures() as $validationFailure) {
                        $msg = $msg . (($msg != "")? "\n" : "") . $validationFailure->getMessage();
                    }

                    throw new \Exception(\G::LoadTranslation("ID_RECORD_CANNOT_BE_CREATED") . "\n" . $msg);
                }

            } catch (\Exception $e) {
                $cnn->rollback();

                throw $e;
            }

            //Return
            $variable = $this->getVariable($processUid, $sPkProcessVariables);

            return $variable;

        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Update Variable
     *
     * @param string $processUid Unique id of Process
     * @param string $variableUid Unique id of Variable
     * @param array  $arrayData   Data
     *
     * return array Return data of the Variable updated
     */
    public function update($processUid, $variableUid, $arrayData)
    {
        try {
            //Verify data
            Validator::proUid($processUid, '$prj_uid');
            $arrayData = array_change_key_case($arrayData, CASE_UPPER);

            $this->throwExceptionFieldDefinition($arrayData);

            //Update
            $cnn = \Propel::getConnection("workflow");
            try {
                $variable = \ProcessVariablesPeer::retrieveByPK($variableUid);
                $dbConnection = \DbSourcePeer::retrieveByPK($variable->getVarDbconnection(), $variable->getPrjUid());

                $oldVariable = array(
                    "VAR_NAME" => $variable->getVarName(),
                    "VAR_FIELD_TYPE" => $variable->getVarFieldType(),
                    "VAR_DBCONNECTION" => $variable->getVarDbconnection(),
                    "VAR_DBCONNECTION_LABEL" => $dbConnection !== null ? '[' . $dbConnection->getDbsServer() . ':' . $dbConnection->getDbsPort() . '] ' . $dbConnection->getDbsType() . ': ' . $dbConnection->getDbsDatabaseName() : 'PM Database',
                    "VAR_SQL" => $variable->getVarSql(),
                    "VAR_ACCEPTED_VALUES" => $variable->getVarAcceptedValues()
                );
                if ($variable->validate()) {
                    $cnn->begin();
                    if (isset($arrayData["VAR_NAME"])) {
                        $this->existsName($processUid, $arrayData["VAR_NAME"], $variableUid);

                        $variable->setVarName($arrayData["VAR_NAME"]);
                    }
                    if (isset($arrayData["VAR_FIELD_TYPE"])) {
                        $variable->setVarFieldType($arrayData["VAR_FIELD_TYPE"]);
                    }
                    if (isset($arrayData["VAR_FIELD_SIZE"])) {
                        $variable->setVarFieldSize($arrayData["VAR_FIELD_SIZE"]);
                    }
                    if (isset($arrayData["VAR_LABEL"])) {
                        $variable->setVarLabel($arrayData["VAR_LABEL"]);
                    }
                    if (isset($arrayData["VAR_DBCONNECTION"])) {
                        $variable->setVarDbconnection($arrayData["VAR_DBCONNECTION"]);
                    }
                    if (isset($arrayData["VAR_SQL"])) {
                        $variable->setVarSql($arrayData["VAR_SQL"]);
                    }
                    if (isset($arrayData["VAR_NULL"])) {
                        $variable->setVarNull($arrayData["VAR_NULL"]);
                    }
                    if (isset($arrayData["VAR_DEFAULT"])) {
                        $variable->setVarDefault($arrayData["VAR_DEFAULT"]);
                    }
                    if (isset($arrayData["VAR_ACCEPTED_VALUES"])) {
                        $encodeAcceptedValues = \G::json_encode($arrayData["VAR_ACCEPTED_VALUES"]);
                        $variable->setVarAcceptedValues($encodeAcceptedValues);
                    }
                    if (isset($arrayData["INP_DOC_UID"])) {
                        $variable->setInpDocUid($arrayData["INP_DOC_UID"]);
                    }
                    $variable->save();
                    $cnn->commit();
                    //update dynaforms
                    $dbConnection = \DbSourcePeer::retrieveByPK($variable->getVarDbconnection(), $variable->getPrjUid());
                    $newVariable = array(
                        "VAR_NAME" => $variable->getVarName(),
                        "VAR_FIELD_TYPE" => $variable->getVarFieldType(),
                        "VAR_DBCONNECTION" => $variable->getVarDbconnection(),
                        "VAR_DBCONNECTION_LABEL" => $dbConnection !== null ? '[' . $dbConnection->getDbsServer() . ':' . $dbConnection->getDbsPort() . '] ' . $dbConnection->getDbsType() . ': ' . $dbConnection->getDbsDatabaseName() : 'PM Database',
                        "VAR_SQL" => $variable->getVarSql(),
                        "VAR_ACCEPTED_VALUES" => $variable->getVarAcceptedValues()
                    );
                    \G::LoadClass('pmDynaform');
                    $pmDynaform = new \pmDynaform();
                    $pmDynaform->synchronizeVariable($processUid, $newVariable, $oldVariable);
                } else {

                    $msg = "";

                    foreach ($variable->getValidationFailures() as $validationFailure) {
                        $msg = $msg . (($msg != "")? "\n" : "") . $validationFailure->getMessage();
                    }

                    throw new \Exception(\G::LoadTranslation("ID_RECORD_CANNOT_BE_CREATED") . "\n" . $msg);
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
     * Delete Variable
     *
     * @param string $processUid Unique id of Process
     * @param string $variableUid Unique id of Variable
     *
     * return void
     */
    public function delete($processUid, $variableUid)
    {
        try {
            //Verify data
            Validator::proUid($processUid, '$prj_uid');

            $this->throwExceptionIfNotExistsVariable($variableUid);

            $variable = $this->getVariable($processUid, $variableUid);
            \G::LoadClass('pmDynaform');
            $pmDynaform = new \pmDynaform();
            $isUsed = $pmDynaform->isUsed($processUid, $variable);
            if ($isUsed !== false) {
                $titleDynaform=$pmDynaform->getDynaformTitle($isUsed);
                throw new \Exception(\G::LoadTranslation("ID_VARIABLE_IN_USE", array($titleDynaform)));
            }
            //Delete
            $criteria = new \Criteria("workflow");

            $criteria->add(\ProcessVariablesPeer::VAR_UID, $variableUid);

            \ProcessVariablesPeer::doDelete($criteria);

        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get data of a Variable
     * @param string $processUid Unique id of Process
     * @param string $variableUid Unique id of Variable
     *
     * return array Return an array with data of a Variable
     */
    public function getVariable($processUid, $variableUid)
    {
        try {
            //Verify data
            Validator::proUid($processUid, '$prj_uid');

            $this->throwExceptionIfNotExistsVariable($variableUid);

            //Get data
            $criteria = new \Criteria("workflow");

            $criteria->addSelectColumn(\ProcessVariablesPeer::VAR_UID);
            $criteria->addSelectColumn(\ProcessVariablesPeer::PRJ_UID);
            $criteria->addSelectColumn(\ProcessVariablesPeer::VAR_NAME);
            $criteria->addSelectColumn(\ProcessVariablesPeer::VAR_FIELD_TYPE);
            $criteria->addSelectColumn(\ProcessVariablesPeer::VAR_FIELD_SIZE);
            $criteria->addSelectColumn(\ProcessVariablesPeer::VAR_LABEL);
            $criteria->addSelectColumn(\ProcessVariablesPeer::VAR_DBCONNECTION);
            $criteria->addSelectColumn(\ProcessVariablesPeer::VAR_SQL);
            $criteria->addSelectColumn(\ProcessVariablesPeer::VAR_NULL);
            $criteria->addSelectColumn(\ProcessVariablesPeer::VAR_DEFAULT);
            $criteria->addSelectColumn(\ProcessVariablesPeer::VAR_ACCEPTED_VALUES);
            $criteria->addSelectColumn(\ProcessVariablesPeer::INP_DOC_UID);
            $criteria->addSelectColumn(\DbSourcePeer::DBS_SERVER);
            $criteria->addSelectColumn(\DbSourcePeer::DBS_PORT);
            $criteria->addSelectColumn(\DbSourcePeer::DBS_DATABASE_NAME);
            $criteria->addSelectColumn(\DbSourcePeer::DBS_TYPE);

            $criteria->add(\ProcessVariablesPeer::PRJ_UID, $processUid, \Criteria::EQUAL);
            $criteria->add(\ProcessVariablesPeer::VAR_UID, $variableUid, \Criteria::EQUAL);
            $criteria->addJoin(\ProcessVariablesPeer::VAR_DBCONNECTION, \DbSourcePeer::DBS_UID, \Criteria::LEFT_JOIN);

            $rsCriteria = \ProcessVariablesPeer::doSelectRS($criteria);

            $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

            $rsCriteria->next();
            $arrayVariables = array();

            while ($aRow = $rsCriteria->getRow()) {
                
                $VAR_ACCEPTED_VALUES = \G::json_decode($aRow['VAR_ACCEPTED_VALUES'], true);
                if(sizeof($VAR_ACCEPTED_VALUES)) {
                    $encodeAcceptedValues = preg_replace("/\\\\u([a-f0-9]{4})/e", "iconv('UCS-4LE','UTF-8',pack('V', hexdec('U$1')))", \G::json_encode($VAR_ACCEPTED_VALUES));
                } else {
                    $encodeAcceptedValues = $aRow['VAR_ACCEPTED_VALUES'];
                }
                
                $arrayVariables = array('var_uid' => $aRow['VAR_UID'],
                    'prj_uid' => $aRow['PRJ_UID'],
                    'var_name' => $aRow['VAR_NAME'],
                    'var_field_type' => $aRow['VAR_FIELD_TYPE'],
                    'var_field_size' => (int)$aRow['VAR_FIELD_SIZE'],
                    'var_label' => $aRow['VAR_LABEL'],
                    'var_dbconnection' => $aRow['VAR_DBCONNECTION'] === 'none' ? 'workflow' : $aRow['VAR_DBCONNECTION'],
                    'var_dbconnection_label' => $aRow['DBS_SERVER'] !== null ? '[' . $aRow['DBS_SERVER'] . ':' . $aRow['DBS_PORT'] . '] ' . $aRow['DBS_TYPE'] . ': ' . $aRow['DBS_DATABASE_NAME'] : 'PM Database',
                    'var_sql' => $aRow['VAR_SQL'],
                    'var_null' => (int)$aRow['VAR_NULL'],
                    'var_default' => $aRow['VAR_DEFAULT'],
                    'var_accepted_values' => $encodeAcceptedValues,
                    'inp_doc_uid' => $aRow['INP_DOC_UID']);
                $rsCriteria->next();
            }
            //Return
            return $arrayVariables;

        } catch (\Exception $e) {
             throw $e;
        }
    }


    /**
     * Get data of Variables
     *
     * @param string $processUid Unique id of Process
     *
     * return array Return an array with data of a DynaForm
     */
    public function getVariables($processUid)
    {
        try {
            //Verify data
            Validator::proUid($processUid, '$prj_uid');

            //Get data
            $criteria = new \Criteria("workflow");

            $criteria->addSelectColumn(\ProcessVariablesPeer::VAR_UID);
            $criteria->addSelectColumn(\ProcessVariablesPeer::PRJ_UID);
            $criteria->addSelectColumn(\ProcessVariablesPeer::VAR_NAME);
            $criteria->addSelectColumn(\ProcessVariablesPeer::VAR_FIELD_TYPE);
            $criteria->addSelectColumn(\ProcessVariablesPeer::VAR_FIELD_SIZE);
            $criteria->addSelectColumn(\ProcessVariablesPeer::VAR_LABEL);
            $criteria->addSelectColumn(\ProcessVariablesPeer::VAR_DBCONNECTION);
            $criteria->addSelectColumn(\ProcessVariablesPeer::VAR_SQL);
            $criteria->addSelectColumn(\ProcessVariablesPeer::VAR_NULL);
            $criteria->addSelectColumn(\ProcessVariablesPeer::VAR_DEFAULT);
            $criteria->addSelectColumn(\ProcessVariablesPeer::VAR_ACCEPTED_VALUES);
            $criteria->addSelectColumn(\ProcessVariablesPeer::INP_DOC_UID);
            $criteria->addSelectColumn(\DbSourcePeer::DBS_SERVER);
            $criteria->addSelectColumn(\DbSourcePeer::DBS_PORT);
            $criteria->addSelectColumn(\DbSourcePeer::DBS_DATABASE_NAME);
            $criteria->addSelectColumn(\DbSourcePeer::DBS_TYPE);

            $criteria->add(\ProcessVariablesPeer::PRJ_UID, $processUid, \Criteria::EQUAL);
            $criteria->addJoin(\ProcessVariablesPeer::VAR_DBCONNECTION, \DbSourcePeer::DBS_UID . " AND " . \DbSourcePeer::PRO_UID . " = '" . $processUid . "'", \Criteria::LEFT_JOIN);

            $rsCriteria = \ProcessVariablesPeer::doSelectRS($criteria);

            $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

            $rsCriteria->next();
            $arrayVariables = array();

            while ($aRow = $rsCriteria->getRow()) {
                
                $VAR_ACCEPTED_VALUES = \G::json_decode($aRow['VAR_ACCEPTED_VALUES'], true);
                if(sizeof($VAR_ACCEPTED_VALUES)) {
                    $encodeAcceptedValues = preg_replace("/\\\\u([a-f0-9]{4})/e", "iconv('UCS-4LE','UTF-8',pack('V', hexdec('U$1')))", \G::json_encode($VAR_ACCEPTED_VALUES));
                } else {
                    $encodeAcceptedValues = $aRow['VAR_ACCEPTED_VALUES'];
                }
                
                $arrayVariables[] = array('var_uid' => $aRow['VAR_UID'],
                    'prj_uid' => $aRow['PRJ_UID'],
                    'var_name' => $aRow['VAR_NAME'],
                    'var_field_type' => $aRow['VAR_FIELD_TYPE'],
                    'var_field_size' => (int)$aRow['VAR_FIELD_SIZE'],
                    'var_label' => $aRow['VAR_LABEL'],
                    'var_dbconnection' => $aRow['VAR_DBCONNECTION'] === 'none' ? 'workflow' : $aRow['VAR_DBCONNECTION'],
                    'var_dbconnection_label' => $aRow['DBS_SERVER'] !== null ? '[' . $aRow['DBS_SERVER'] . ':' . $aRow['DBS_PORT'] . '] ' . $aRow['DBS_TYPE'] . ': ' . $aRow['DBS_DATABASE_NAME'] : 'PM Database',
                    'var_sql' => $aRow['VAR_SQL'],
                    'var_null' => (int)$aRow['VAR_NULL'],
                    'var_default' => $aRow['VAR_DEFAULT'],
                    'var_accepted_values' => $encodeAcceptedValues,
                    'inp_doc_uid' => $aRow['INP_DOC_UID']);
                $rsCriteria->next();
            }
            //Return
            return $arrayVariables;

        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Verify field definition
     *
     * @param array $aData Unique id of Variable to exclude
     *
     */
    public function throwExceptionFieldDefinition($aData)
    {
        try {
            if (isset($aData["VAR_NAME"])) {
                Validator::isString($aData['VAR_NAME'], '$var_name');
                Validator::isNotEmpty($aData['VAR_NAME'], '$var_name');
            }
            if (isset($aData["VAR_FIELD_TYPE"])) {
                Validator::isString($aData['VAR_FIELD_TYPE'], '$var_field_type');
                Validator::isNotEmpty($aData['VAR_FIELD_TYPE'], '$var_field_type');
                /*if ($aData["VAR_FIELD_TYPE"] != 'string' && $aData["VAR_FIELD_TYPE"] != 'integer' && $aData["VAR_FIELD_TYPE"] != 'boolean' && $aData["VAR_FIELD_TYPE"] != 'float' &&
                    $aData["VAR_FIELD_TYPE"] != 'datetime' && $aData["VAR_FIELD_TYPE"] != 'date_of_birth' && $aData["VAR_FIELD_TYPE"] != 'date') {
                    throw new \Exception(\G::LoadTranslation("ID_INVALID_VALUE_FOR", array('$var_field_type')));
                }*/
            }
            if (isset($aData["VAR_FIELD_SIZE"])) {
                Validator::isInteger($aData["VAR_FIELD_SIZE"], '$var_field_size');
            }
            if (isset($aData["VAR_LABEL"])) {
                Validator::isString($aData['VAR_LABEL'], '$var_label');
                Validator::isNotEmpty($aData['VAR_LABEL'], '$var_label');
            }
            if (isset($aData["VAR_DBCONNECTION"])) {
                Validator::isString($aData['VAR_DBCONNECTION'], '$var_dbconnection');
            }
            if (isset($aData["VAR_SQL"])) {
                Validator::isString($aData['VAR_SQL'], '$var_sql');
            }
            if (isset($aData["VAR_NULL"])) {
                Validator::isInteger($aData['VAR_NULL'], '$var_null');
                if ($aData["VAR_NULL"] != 0 && $aData["VAR_NULL"] !=1 ) {
                    throw new \Exception(\G::LoadTranslation("ID_INVALID_VALUE_ONLY_ACCEPTS_VALUES", array('$var_null','0, 1' )));
                }
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Verify if exists the name of a variable
     *
     * @param string $processUid         Unique id of Process
     * @param string $variableName       Name
     *
     */
    public function existsName($processUid, $variableName, $variableUidToExclude = "")
    {
        try {
            $criteria = new \Criteria("workflow");

            $criteria->addSelectColumn(\ProcessVariablesPeer::VAR_UID);
            $criteria->addSelectColumn(\ProcessVariablesPeer::VAR_NAME);

            if ($variableUidToExclude != "") {
                $criteria->add(\ProcessVariablesPeer::VAR_UID, $variableUidToExclude, \Criteria::NOT_EQUAL);
            }

            $criteria->add(\ProcessVariablesPeer::VAR_NAME, $variableName, \Criteria::EQUAL);
            $criteria->add(\ProcessVariablesPeer::PRJ_UID, $processUid, \Criteria::EQUAL);
            $rsCriteria = \ProcessVariablesPeer::doSelectRS($criteria);

            $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

            while ($rsCriteria->next()) {
                $row = $rsCriteria->getRow();

                if ($variableName == $row["VAR_NAME"]) {
                    throw new \Exception(\G::LoadTranslation("DYNAFIELD_ALREADY_EXIST"));
                }
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get required variables in the SQL
     *
     * @param string $sql SQL
     *
     * return array Return an array with required variables in the SQL
     */
    public function sqlGetRequiredVariables($sql)
    {
        try {
            $arrayVariableRequired = array();

            preg_match_all("/@[@%#\?\x24\=]([A-Za-z_]\w*)/", $sql, $arrayMatch, PREG_SET_ORDER);

            foreach ($arrayMatch as $value) {
                $arrayVariableRequired[] = $value[1];
            }

            return $arrayVariableRequired;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Verify if some required variable in the SQL is missing in the variables
     *
     * @param string $variableName  Variable name
     * @param string $variableSql   SQL
     * @param array  $arrayVariable The variables
     *
     * return void Throw exception if some required variable in the SQL is missing in the variables
     */
    public function throwExceptionIfSomeRequiredVariableSqlIsMissingInVariables($variableName, $variableSql, array $arrayVariable)
    {
        try {
            $arrayResult = array_diff(array_unique($this->sqlGetRequiredVariables($variableSql)), array_keys($arrayVariable));

            if (count($arrayResult) > 0) {
                throw new \Exception(\G::LoadTranslation("ID_PROCESS_VARIABLE_REQUIRED_VARIABLES_FOR_QUERY", array($variableName, implode(", ", $arrayResult))));
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get all records by execute SQL
     *
     * @param string $processUid    Unique id of Process
     * @param string $variableName  Variable name
     * @param array  $arrayVariable The variables
     *
     * return array Return an array with all records
     */
    public function executeSql($processUid, $variableName, array $arrayVariable = array())
    {
        try {
            $arrayRecord = array();

            //Verify data
            $process = new \ProcessMaker\BusinessModel\Process();

            $process->throwExceptionIfNotExistsProcess($processUid, strtolower("PRJ_UID"));

            //Set data
            \G::LoadClass('pmDynaform');
            $pmDynaform = new \pmDynaform();
            $field = $pmDynaform->searchField($arrayVariable["dyn_uid"], $arrayVariable["field_id"]);
            $variableDbConnectionUid = $field !== null ? $field->dbConnection : "";
            $variableSql = $field !== null ? $field->sql : "";

            //Get data
            $_SESSION["PROCESS"] = $processUid;

            $cnn = \Propel::getConnection(($variableDbConnectionUid . "" != "")? $variableDbConnectionUid : "workflow");
            $stmt = $cnn->createStatement();

            $replaceFields = G::replaceDataField($variableSql, $arrayVariable);

            $rs = $stmt->executeQuery($replaceFields, \ResultSet::FETCHMODE_NUM);

            while ($rs->next()) {
                $row = $rs->getRow();

                $arrayRecord[] = array(
                    strtolower("VALUE") => $row[0],
                    strtolower("TEXT") => isset($row[1]) ? $row[1] : $row[0]
                );
            }

            //Return
            return $arrayRecord;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Verify if does not exist the variable in table PROCESS_VARIABLES
     *
     * @param string $variableUid           Unique id of variable
     *
     * return void Throw exception if does not exist the variable in table PROCESS_VARIABLES
     */
    public function throwExceptionIfNotExistsVariable($variableUid)
    {
        try {
            $obj = \ProcessVariablesPeer::retrieveByPK($variableUid);

            if (is_null($obj)) {
                throw new \Exception('var_uid: '.$variableUid. ' '.\G::LoadTranslation("ID_DOES_NOT_EXIST"));
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Verify if the variable is being used in a Dynaform
     *
     * @param string $processUid       Unique id of Process
     * @param string $variableUid       Unique id of Variable
     *
     */
    public function verifyUse($processUid, $variableUid)
    {
        try {

            $criteria = new \Criteria("workflow");
            $criteria->addSelectColumn(\DynaformPeer::DYN_CONTENT);
            $criteria->addSelectColumn(\DynaformPeer::DYN_UID);
            $criteria->add(\DynaformPeer::PRO_UID, $processUid, \Criteria::EQUAL);
            $rsCriteria = \DynaformPeer::doSelectRS($criteria);
            $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

            while ($rsCriteria->next()) {

                $row = $rsCriteria->getRow();

                $contentDecode = \G::json_decode($row["DYN_CONTENT"], true);
                $content = $contentDecode['items'][0]['items'];
                if (is_array($content)) {
                    foreach ($content as $key => $value) {
                        if (isset($value[0]["variable"])) {
                            $criteria = new \Criteria("workflow");
                            $criteria->addSelectColumn(\ProcessVariablesPeer::VAR_NAME);
                            $criteria->add(\ProcessVariablesPeer::PRJ_UID, $processUid, \Criteria::EQUAL);
                            $criteria->add(\ProcessVariablesPeer::VAR_NAME, $value[0]["variable"], \Criteria::EQUAL);
                            $criteria->add(\ProcessVariablesPeer::VAR_UID, $variableUid, \Criteria::EQUAL);
                            $rsCriteria = \ProcessVariablesPeer::doSelectRS($criteria);
                            $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);
                            $rsCriteria->next();

                            if ($rsCriteria->getRow()) {
                                throw new \Exception(\G::LoadTranslation("ID_VARIABLE_IN_USE", array($variableUid, $row["DYN_UID"])));
                            }
                        }
                    }
                }
            }

        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get all records by execute SQL suggest
     *
     * @param string $processUid    Unique id of Process
     * @param string $variableName  Variable name
     * @param array  $arrayVariable The variables
     *
     * return array Return an array with all records
     */
    public function executeSqlSuggest($processUid, $variableName, array $arrayVariable = array())
    {
        try {
            $arrayRecord = array();

            //Verify data
            $process = new \ProcessMaker\BusinessModel\Process();

            $process->throwExceptionIfNotExistsProcess($processUid, strtolower("PRJ_UID"));

            //Set data
            $variableDbConnectionUid = "";
            $variableSql = "";
            $sqlLimit = "";
            $variableSqlLimit = "";
            $sqlConditionLike = "";

            $criteria = new \Criteria("workflow");

            $criteria->addSelectColumn(\ProcessVariablesPeer::VAR_DBCONNECTION);
            $criteria->addSelectColumn(\ProcessVariablesPeer::VAR_SQL);
            $criteria->add(\ProcessVariablesPeer::PRJ_UID, $processUid, \Criteria::EQUAL);
            $criteria->add(\ProcessVariablesPeer::VAR_NAME, $variableName, \Criteria::EQUAL);

            $rsCriteria = \ProcessVariablesPeer::doSelectRS($criteria);

            $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);
            if ($rsCriteria->next()) {
                $row = $rsCriteria->getRow();

                $variableDbConnectionUid = $row["VAR_DBCONNECTION"];
                $variableSql = $row["VAR_SQL"];
            } else {
                throw new \Exception(\G::LoadTranslation("ID_PROCESS_VARIABLE_DOES_NOT_EXIST", array(strtolower("VAR_NAME"), $variableName)));
            }

            //Verify data
            $this->throwExceptionIfSomeRequiredVariableSqlIsMissingInVariables($variableName, $variableSql, $arrayVariable);

            //Get data
            $_SESSION["PROCESS"] = $processUid;

            foreach ($arrayVariable as $keyRequest => $valueRequest) {
                $keyRequest = strtoupper($keyRequest);

                if ($keyRequest == 'LIMIT') {
                    if (strpos($variableSql, 'LIMIT')) {
                        $variableSqlLimit = explode("LIMIT", $variableSql);
                        $sqlLimit = " LIMIT " . $variableSqlLimit[1];
                        $variableSql = $variableSqlLimit[0];
                    } else {
                        $sqlLimit = " LIMIT ". 0 . ", " . $valueRequest;
                    }
                } else {
                    if (strpos($variableSql, 'WHERE')) {
                        $sqlConditionLike = " AND " . $keyRequest . " LIKE '%" . $valueRequest . "%'";
                    } else {
                        $sqlConditionLike = " WHERE " . $keyRequest . " LIKE '%" . $valueRequest . "%'";
                    }
                }
            }

            $sqlQuery = $variableSql . $sqlConditionLike . $sqlLimit;

            $cnn = \Propel::getConnection(($variableDbConnectionUid . "" != "")? $variableDbConnectionUid : "workflow");
            $stmt = $cnn->createStatement();
            $replaceFields = G::replaceDataField($sqlQuery, $arrayVariable);

            $rs = $stmt->executeQuery($replaceFields, \ResultSet::FETCHMODE_NUM);

            while ($rs->next()) {
                $row = $rs->getRow();

                $arrayRecord[] = array(
                    strtolower("VALUE") => $row[0],
                    strtolower("TEXT")  => $row[1]
                );
            }

            //Return
            return $arrayRecord;
        } catch (\Exception $e) {
            throw $e;
        }
    }
}