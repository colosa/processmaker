<?php
namespace ProcessMaker\BusinessModel;

use G;
use Criteria;

class Process
{
    private $arrayFieldDefinition = array(
        "PRO_UID"                   => array("type" => "string",   "required" => false, "empty" => false, "defaultValues" => array(),  "fieldNameAux" => "processUid"),

        "PRO_TITLE"                 => array("type" => "string",   "required" => true,  "empty" => false, "defaultValues" => array(),  "fieldNameAux" => "processTitle"),
        "PRO_DESCRIPTION"           => array("type" => "string",   "required" => false, "empty" => true,  "defaultValues" => array(),  "fieldNameAux" => "processDescription"),
        "PRO_PARENT"                => array("type" => "string",   "required" => true,  "empty" => false, "defaultValues" => array(),  "fieldNameAux" => "processParent"),
        "PRO_TIME"                  => array("type" => "int",      "required" => false, "empty" => false, "defaultValues" => array(1), "fieldNameAux" => "processTime"),
        "PRO_TIMEUNIT"              => array("type" => "string",   "required" => false, "empty" => false, "defaultValues" => array("DAYS"),               "fieldNameAux" => "processTimeunit"),
        "PRO_STATUS"                => array("type" => "string",   "required" => true,  "empty" => false, "defaultValues" => array("ACTIVE", "INACTIVE"), "fieldNameAux" => "processStatus"),
        "PRO_TYPE_DAY"              => array("type" => "string",   "required" => false, "empty" => true,  "defaultValues" => array(),                     "fieldNameAux" => "processTypeDay"),
        "PRO_TYPE"                  => array("type" => "string",   "required" => false, "empty" => false, "defaultValues" => array("NORMAL"),             "fieldNameAux" => "processType"),
        "PRO_ASSIGNMENT"            => array("type" => "int",      "required" => false, "empty" => false, "defaultValues" => array(0, 1), "fieldNameAux" => "processAssignment"),
        "PRO_SHOW_MAP"              => array("type" => "int",      "required" => false, "empty" => false, "defaultValues" => array(0, 1), "fieldNameAux" => "processShowMap"),
        "PRO_SHOW_MESSAGE"          => array("type" => "int",      "required" => false, "empty" => false, "defaultValues" => array(0, 1), "fieldNameAux" => "processShowMessage"),
        "PRO_SUBPROCESS"            => array("type" => "int",      "required" => false, "empty" => false, "defaultValues" => array(0, 1), "fieldNameAux" => "processSubprocess"),
        "PRO_TRI_DELETED"           => array("type" => "string",   "required" => false, "empty" => true,  "defaultValues" => array(),     "fieldNameAux" => "processTriDeleted"),
        "PRO_TRI_CANCELED"          => array("type" => "string",   "required" => false, "empty" => true,  "defaultValues" => array(),     "fieldNameAux" => "processTriCanceled"),
        "PRO_TRI_PAUSED"            => array("type" => "string",   "required" => false, "empty" => true,  "defaultValues" => array(),     "fieldNameAux" => "processTriPaused"),
        "PRO_TRI_REASSIGNED"        => array("type" => "string",   "required" => false, "empty" => true,  "defaultValues" => array(),     "fieldNameAux" => "processTriReassigned"),
        "PRO_SHOW_DELEGATE"         => array("type" => "int",      "required" => false, "empty" => false, "defaultValues" => array(0, 1), "fieldNameAux" => "processShowDelegate"),
        "PRO_SHOW_DYNAFORM"         => array("type" => "int",      "required" => false, "empty" => false, "defaultValues" => array(0, 1), "fieldNameAux" => "processShowDynaform"),
        "PRO_CATEGORY"              => array("type" => "string",   "required" => false, "empty" => true,  "defaultValues" => array(),     "fieldNameAux" => "processCategory"),
        "PRO_SUB_CATEGORY"          => array("type" => "string",   "required" => false, "empty" => true,  "defaultValues" => array(),     "fieldNameAux" => "processSubCategory"),
        "PRO_INDUSTRY"              => array("type" => "int",      "required" => false, "empty" => false, "defaultValues" => array(0),    "fieldNameAux" => "processIndustry"),
        "PRO_UPDATE_DATE"           => array("type" => "datetime", "required" => false, "empty" => true,  "defaultValues" => array(),     "fieldNameAux" => "processUpdateDate"),
        "PRO_CREATE_DATE"           => array("type" => "datetime", "required" => false, "empty" => true,  "defaultValues" => array(),     "fieldNameAux" => "processCreateDate"),
        "PRO_CREATE_USER"           => array("type" => "string",   "required" => true,  "empty" => true,  "defaultValues" => array(),     "fieldNameAux" => "processCreateUser"),
        "PRO_DEBUG"                 => array("type" => "int",      "required" => false, "empty" => false, "defaultValues" => array(0, 1), "fieldNameAux" => "processDebug"),
        "PRO_DERIVATION_SCREEN_TPL" => array("type" => "string",   "required" => false, "empty" => true,  "defaultValues" => array(),     "fieldNameAux" => "processDerivationScreenTpl"),
        "PRO_SUMMARY_DYNAFORM"      => array("type" => "string",   "required" => false, "empty" => true,  "defaultValues" => array(),     "fieldNameAux" => "processSummaryDynaform"),
        "PRO_CALENDAR"              => array("type" => "string",   "required" => false, "empty" => true,  "defaultValues" => array(),     "fieldNameAux" => "processCalendar")
    );

    private $formatFieldNameInUppercase = true;

    private $arrayFieldNameForException = array(
        "gridUid" => "GRID_UID"
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
     * Set exception messages for fields
     *
     * @param array $arrayData Data with the fields
     *
     * return void
     */
    public function setArrayFieldNameForException($arrayData)
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
     * Verify if exists the title of a Process
     *
     * @param string $processTitle      Title
     * @param string $processUidExclude Unique id of Process to exclude
     *
     * return bool Return true if exists the title of a Process, false otherwise
     */
    public function existsTitle($processTitle, $processUidExclude = "")
    {
        try {
            $delimiter = \DBAdapter::getStringDelimiter();

            $criteria = new \Criteria("workflow");

            $criteria->addSelectColumn(\ProcessPeer::PRO_UID);

            $criteria->addAlias("CT", \ContentPeer::TABLE_NAME);

            $arrayCondition = array();
            $arrayCondition[] = array(\ProcessPeer::PRO_UID, "CT.CON_ID", \Criteria::EQUAL);
            $arrayCondition[] = array("CT.CON_CATEGORY", $delimiter . "PRO_TITLE" . $delimiter, \Criteria::EQUAL);
            $arrayCondition[] = array("CT.CON_LANG", $delimiter . SYS_LANG . $delimiter, \Criteria::EQUAL);
            $criteria->addJoinMC($arrayCondition, \Criteria::LEFT_JOIN);

            if ($processUidExclude != "") {
                $criteria->add(\ProcessPeer::PRO_UID, $processUidExclude, \Criteria::NOT_EQUAL);
            }

            $criteria->add("CT.CON_VALUE", $processTitle, \Criteria::EQUAL);

            $rsCriteria = \ProcessPeer::doSelectRS($criteria);

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
     * Validate data by field definition
     *
     * @param array $arrayData                  Data
     * @param array $arrayFieldDefinition       Definition of fields
     * @param array $arrayFieldNameForException Fields for exception messages
     * @param bool  $flagValidateRequired       Validate required fields
     *
     * return void Throw exception if data has an invalid value
     */
    public function throwExceptionIfDataNotMetFieldDefinition($arrayData, $arrayFieldDefinition, $arrayFieldNameForException, $flagValidateRequired = true)
    {
        try {

            \G::LoadSystem('inputfilter');
            $filter = new \InputFilter();

            if ($flagValidateRequired) {
                foreach ($arrayFieldDefinition as $key => $value) {
                    $fieldName = $key;

                    $fieldNameAux = (isset($arrayFieldNameForException[$arrayFieldDefinition[$fieldName]["fieldNameAux"]]))? $arrayFieldNameForException[$arrayFieldDefinition[$fieldName]["fieldNameAux"]] : "";

                    if ($arrayFieldDefinition[$fieldName]["required"] && !isset($arrayData[$fieldName])) {
                        throw new \Exception(\G::LoadTranslation("ID_UNDEFINED_VALUE_IS_REQUIRED", array($fieldNameAux)));
                    }
                }
            }

            $arrayType1 = array("int", "integer", "float", "real", "double", "bool", "boolean", "string", "date", "hour", "datetime");
            $arrayType2 = array("array", "object");

            foreach ($arrayData as $key => $value) {
                $fieldName = $key;
                $fieldValue = $value;


                if (isset($arrayFieldDefinition[$fieldName])) {
                    $fieldNameAux = (isset($arrayFieldNameForException[$arrayFieldDefinition[$fieldName]["fieldNameAux"]]))? $arrayFieldNameForException[$arrayFieldDefinition[$fieldName]["fieldNameAux"]] : "";

                    $arrayFieldDefinition[$fieldName]["type"] = strtolower($arrayFieldDefinition[$fieldName]["type"]);

                    $optionType = 0;
                    $optionType = ($optionType == 0 && in_array($arrayFieldDefinition[$fieldName]["type"], $arrayType1))? 1 : $optionType;
                    $optionType = ($optionType == 0 && in_array($arrayFieldDefinition[$fieldName]["type"], $arrayType2))? 2 : $optionType;

                    switch ($optionType) {
                        case 1:
                            //empty
                            if (!$arrayFieldDefinition[$fieldName]["empty"] && trim($fieldValue) . "" == "") {
                                throw new \Exception(\G::LoadTranslation("ID_INVALID_VALUE_CAN_NOT_BE_EMPTY", array($fieldNameAux)));
                            }

                            //defaultValues
                            if (count($arrayFieldDefinition[$fieldName]["defaultValues"]) > 0 && !in_array($fieldValue, $arrayFieldDefinition[$fieldName]["defaultValues"], true)) {
                                throw new \Exception(\G::LoadTranslation("ID_INVALID_VALUE_ONLY_ACCEPTS_VALUES", array($fieldNameAux, implode("|", $arrayFieldDefinition[$fieldName]["defaultValues"]))));
                            }

                            //type
                            $fieldValue = (!is_array($fieldValue)) ? $fieldValue : '';
                            if ($arrayFieldDefinition[$fieldName]["empty"] && $fieldValue . "" == "") {
                                //
                            } else {
                                $regexpDate = "[1-9]\d{3}\-(?:0[1-9]|1[012])\-(?:[0][1-9]|[12][0-9]|3[01])";
                                $regexpHour = "(?:[0-1]\d|2[0-3])\:(?:[0-5]\d)(?:\:[0-5]\d)?";
                                $regexpDatetime = $regexpDate . "\s" . $regexpHour;

                                switch ($arrayFieldDefinition[$fieldName]["type"]) {
                                    case "date":
                                        if (!preg_match("/^" . $regexpDate . "$/", $fieldValue)) {
                                            throw new \Exception(\G::LoadTranslation("ID_INVALID_VALUE", array($fieldNameAux)));
                                        }
                                        break;
                                    case "hour":
                                        if (!preg_match("/^" . $regexpHour . "$/", $fieldValue)) {
                                            throw new \Exception(\G::LoadTranslation("ID_INVALID_VALUE", array($fieldNameAux)));
                                        }
                                        break;
                                    case "datetime":
                                        if (!preg_match("/^" . $regexpDatetime . "$/", $fieldValue)) {
                                            throw new \Exception(\G::LoadTranslation("ID_INVALID_VALUE", array($fieldNameAux)));
                                        }
                                        break;
                                }
                            }
                            break;
                        case 2:
                            switch ($arrayFieldDefinition[$fieldName]["type"]) {
                                case "array":
                                    $regexpArray1 = "\s*array\s*\(";
                                    $regexpArray2 = "\)\s*";

                                    //type
                                    if (!is_array($fieldValue)) {
                                        if ($fieldValue != "" && !preg_match("/^" . $regexpArray1 . ".*" . $regexpArray2 . "$/", $fieldValue)) {
                                            throw new \Exception(\G::LoadTranslation("ID_INVALID_VALUE_THIS_MUST_BE_ARRAY", array($fieldNameAux)));
                                        }
                                    }

                                    //empty
                                    if (!$arrayFieldDefinition[$fieldName]["empty"]) {
                                        $arrayAux = array();

                                        if (is_array($fieldValue)) {
                                            $arrayAux = $fieldValue;
                                        }

                                        if (is_string($fieldValue) && trim($fieldValue) . "" != "") {
                                            //eval("\$arrayAux = $fieldValue;");

                                            if (preg_match("/^" . $regexpArray1 . "(.*)" . $regexpArray2 . "$/", $fieldValue, $arrayMatch)) {
                                                if (trim($arrayMatch[1], " ,") != "") {
                                                    $arrayAux = array(0);
                                                }
                                            }
                                        }

                                        if (count($arrayAux) == 0) {
                                            throw new \Exception(\G::LoadTranslation("ID_INVALID_VALUE_CAN_NOT_BE_EMPTY", array($fieldNameAux)));
                                        }
                                    }

                                    //defaultValues
                                    if (count($arrayFieldDefinition[$fieldName]["defaultValues"]) > 0) {
                                        $arrayAux = array();

                                        if (is_array($fieldValue)) {
                                            $arrayAux = $fieldValue;
                                        }

                                        if (is_string($fieldValue) && trim($fieldValue) . "" != "") {
                                            $fieldValue = $filter->validateInput($fieldValue);
                                            eval("\$arrayAux = $fieldValue;");
                                        }

                                        foreach ($arrayAux as $value) {
                                            if (!in_array($value, $arrayFieldDefinition[$fieldName]["defaultValues"], true)) {
                                                throw new \Exception(\G::LoadTranslation("ID_INVALID_VALUE_ONLY_ACCEPTS_VALUES", array($fieldNameAux, implode("|", $arrayFieldDefinition[$fieldName]["defaultValues"]))));
                                            }
                                        }
                                    }
                                    break;
                            }
                            break;
                    }
                }
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Validate pager data
     *
     * @param array $arrayData                  Data
     * @param array $arrayFieldNameForException Fields for exception messages
     *
     * return void Throw exception if pager data has an invalid value
     */
    public function throwExceptionIfDataNotMetPagerVarDefinition($arrayData, $arrayFieldNameForException)
    {
        try {
            foreach ($arrayData as $key => $value) {
                $nameForException = (isset($arrayFieldNameForException[$key]))? $arrayFieldNameForException[$key] : "";

                if (!is_null($value) && ($value . "" == "" || !preg_match("/^(?:\+|\-)?(?:0|[1-9]\d*)$/", $value . "") || (int)($value) < 0)) {
                    throw new \Exception(\G::LoadTranslation("ID_INVALID_VALUE_EXPECTING_POSITIVE_INTEGER", array($nameForException)));
                }
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Verify if doesn't exists the Process in table PROCESS
     *
     * @param string $processUid            Unique id of Process
     * @param string $fieldNameForException Field name for the exception
     *
     * return void Throw exception if doesn't exists the Process in table PROCESS
     */
    public function throwExceptionIfNotExistsProcess($processUid, $fieldNameForException)
    {
        try {
            $process = new \Process();

            if (!$process->processExists($processUid)) {
                throw new \Exception(\G::LoadTranslation("ID_PROJECT_DOES_NOT_EXIST", array($fieldNameForException, $processUid)));
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Verify if doesn't exists the User in table USERS
     *
     * @param string $userUid               Unique id of User
     * @param string $fieldNameForException Field name for the exception
     *
     * return void Throw exception if doesn't exists the User in table USERS
     */
    public function throwExceptionIfNotExistsUser($userUid, $fieldNameForException)
    {
        try {
            $user = new \Users();

            if (!$user->userExists($userUid)) {
                throw new \Exception(\G::LoadTranslation("ID_USER_DOES_NOT_EXIST", array($fieldNameForException, $userUid)));
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Verify if exists the title of a Process
     *
     * @param string $processTitle          Title
     * @param string $fieldNameForException Field name for the exception
     * @param string $processUidExclude     Unique id of Process to exclude
     *
     * return void Throw exception if exists the title of a Process
     */
    public function throwExceptionIfExistsTitle($processTitle, $fieldNameForException, $processUidExclude = "")
    {
        try {
            if ($this->existsTitle($processTitle, $processUidExclude)) {
                throw new \Exception(\G::LoadTranslation("ID_PROJECT_TITLE_ALREADY_EXISTS", array($fieldNameForException, $processTitle)));
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Verify if doesn't exists the Process Category in table PROCESS_CATEGORY
     *
     * @param string $processCategoryUid    Unique id of Process Category
     * @param string $fieldNameForException Field name for the exception
     *
     * return void Throw exception if doesn't exists the Process Category in table PROCESS_CATEGORY
     */
    public function throwExceptionIfNotExistsProcessCategory($processCategoryUid, $fieldNameForException)
    {
        try {
            $obj = \ProcessCategoryPeer::retrieveByPK($processCategoryUid);

            if (!(is_object($obj) && get_class($obj) == "ProcessCategory")) {
                throw new \Exception(\G::LoadTranslation("ID_PROJECT_CATEGORY_DOES_NOT_EXIST", array($fieldNameForException, $processCategoryUid)));
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Verify if doesn't exists the PM Table in table ADDITIONAL_TABLES
     *
     * @param string $additionalTableUid    Unique id of PM Table
     * @param string $fieldNameForException Field name for the exception
     *
     * return void Throw exception if doesn't exists the PM Table in table ADDITIONAL_TABLES
     */
    public function throwExceptionIfNotExistsPmTable($additionalTableUid, $fieldNameForException)
    {
        try {
            $obj = \AdditionalTablesPeer::retrieveByPK($additionalTableUid);

            if (!(is_object($obj) && get_class($obj) == "AdditionalTables")) {
                throw new \Exception(\G::LoadTranslation("ID_PMTABLE_DOES_NOT_EXIST", array($fieldNameForException, $additionalTableUid)));
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Verify if doesn't exists the Template in Routing Screen Template
     *
     * @param string $processUid            Unique id of Process
     * @param string $fileName              Name template
     * @param string $fieldNameForException Field name for the exception
     *
     * return void Throw exception if doesn't exists the Template in Routing Screen Template
     */
    public function throwExceptionIfNotExistsRoutingScreenTemplate($processUid, $fileName, $fieldNameForException)
    {
        try {
            \G::LoadClass("processes");

            $arrayFile = \Processes::getProcessFiles($processUid, "mail");
            $flag = 0;

            foreach ($arrayFile as $f) {
                if ($f["filename"] == $fileName) {
                    $flag = 1;
                    break;
                }
            }

            if ($flag == 0) {
                throw new \Exception(\G::LoadTranslation("ID_ROUTING_SCREEN_TEMPLATE_DOES_NOT_EXIST", array($fieldNameForException, $fileName)));
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Verify if does not exist the Permission in table PERMISSIONS (Database RBAC)
     *
     * @param string $permissionUid         Unique id of Permission
     * @param string $fieldNameForException Field name for the exception
     *
     * return void Throw exception if does not exist the Permission in table PERMISSIONS
     */
    public function throwExceptionIfNotExistsPermission($permissionUid, $fieldNameForException)
    {
        try {
            $obj = \PermissionsPeer::retrieveByPK($permissionUid);

            if (is_null($obj)) {
                throw new \Exception(\G::LoadTranslation("ID_PERMISSION_DOES_NOT_EXIST", array($fieldNameForException, $permissionUid)));
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Update Process
     *
     * @param string $processUid Unique id of Process
     * @param array  $arrayData  Data
     *
     * return array Return data of the Process updated
     */
    public function update($processUid, $arrayData)
    {
        try {
            $arrayData = array_change_key_case($arrayData, CASE_UPPER);

            //Verify data
            $this->throwExceptionIfNotExistsProcess($processUid, $this->arrayFieldNameForException["processUid"]);

            $this->throwExceptionIfDataNotMetFieldDefinition($arrayData, $this->arrayFieldDefinition, $this->arrayFieldNameForException, false);

            if (isset($arrayData["PRO_TITLE"])) {
                $this->throwExceptionIfExistsTitle($arrayData["PRO_TITLE"], $this->arrayFieldNameForException["processTitle"], $processUid);
            }

            if (isset($arrayData["PRO_CALENDAR"]) && $arrayData["PRO_CALENDAR"] . "" != "") {
                $calendar = new \ProcessMaker\BusinessModel\Calendar();

                $calendar->throwExceptionIfNotExistsCalendar($arrayData["PRO_CALENDAR"], $this->arrayFieldNameForException["processCalendar"]);
            }

            if (isset($arrayData["PRO_CATEGORY"]) && $arrayData["PRO_CATEGORY"] . "" != "") {
                $this->throwExceptionIfNotExistsProcessCategory($arrayData["PRO_CATEGORY"], $this->arrayFieldNameForException["processCategory"]);
            }

            if (isset($arrayData["PRO_SUMMARY_DYNAFORM"]) && $arrayData["PRO_SUMMARY_DYNAFORM"] . "" != "") {
                $dynaForm = new \ProcessMaker\BusinessModel\DynaForm();

                $dynaForm->throwExceptionIfNotExistsDynaForm($arrayData["PRO_SUMMARY_DYNAFORM"], $processUid, $this->arrayFieldNameForException["processSummaryDynaform"]);
            }

            if (isset($arrayData["PRO_DERIVATION_SCREEN_TPL"]) && $arrayData["PRO_DERIVATION_SCREEN_TPL"] . "" != "") {
                $this->throwExceptionIfNotExistsRoutingScreenTemplate($processUid, $arrayData["PRO_DERIVATION_SCREEN_TPL"], $this->arrayFieldNameForException["processDerivationScreenTpl"]);
            }

            $trigger = new \ProcessMaker\BusinessModel\Trigger();

            if (isset($arrayData["PRO_TRI_DELETED"]) && $arrayData["PRO_TRI_DELETED"] . "" != "") {
                $trigger->throwExceptionIfNotExistsTrigger($arrayData["PRO_TRI_DELETED"], $processUid, $this->arrayFieldNameForException["processTriDeleted"]);
            }

            if (isset($arrayData["PRO_TRI_CANCELED"]) && $arrayData["PRO_TRI_CANCELED"] . "" != "") {
                $trigger->throwExceptionIfNotExistsTrigger($arrayData["PRO_TRI_CANCELED"], $processUid, $this->arrayFieldNameForException["processTriCanceled"]);
            }

            if (isset($arrayData["PRO_TRI_PAUSED"]) && $arrayData["PRO_TRI_PAUSED"] . "" != "") {
                $trigger->throwExceptionIfNotExistsTrigger($arrayData["PRO_TRI_PAUSED"], $processUid, $this->arrayFieldNameForException["processTriPaused"]);
            }

            if (isset($arrayData["PRO_TRI_REASSIGNED"]) && $arrayData["PRO_TRI_REASSIGNED"] . "" != "") {
                $trigger->throwExceptionIfNotExistsTrigger($arrayData["PRO_TRI_REASSIGNED"], $processUid, $this->arrayFieldNameForException["processTriReassigned"]);
            }

            if (isset($arrayData["PRO_PARENT"])) {
                $this->throwExceptionIfNotExistsProcess($arrayData["PRO_PARENT"], $this->arrayFieldNameForException["processParent"]);
            }

            if (isset($arrayData["PRO_CREATE_USER"]) && $arrayData["PRO_CREATE_USER"] . "" != "") {
                $this->throwExceptionIfNotExistsUser($arrayData["PRO_CREATE_USER"], $this->arrayFieldNameForException["processCreateUser"]);
            }

            //Update
            $process = new \Process();

            $arrayDataBackup = $arrayData;

            $arrayData["PRO_UID"] = $processUid;

            if (isset($arrayData["PRO_ASSIGNMENT"])) {
                $arrayData["PRO_ASSIGNMENT"] = ($arrayData["PRO_ASSIGNMENT"] == 1)? "TRUE" : "FALSE";
            }

            $arrayData["PRO_DYNAFORMS"] = array();
            $arrayData["PRO_DYNAFORMS"]["PROCESS"] = (isset($arrayData["PRO_SUMMARY_DYNAFORM"]))? $arrayData["PRO_SUMMARY_DYNAFORM"] : "";

            unset($arrayData["PRO_SUMMARY_DYNAFORM"]);

            $result = $process->update($arrayData);

            if (isset($arrayData["PRO_CALENDAR"])) {
                $calendar = new \Calendar();

                $calendar->assignCalendarTo($processUid, $arrayData["PRO_CALENDAR"], "PROCESS"); //Save Calendar ID for this process
            }

            $arrayData = $arrayDataBackup;

            //Return
            if (!$this->formatFieldNameInUppercase) {
                $arrayData = array_change_key_case($arrayData, CASE_LOWER);
            }

            return $arrayData;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get data of a Process
     *
     * @param string $processUid Unique id of Process
     *
     * return array Return an array with data of a Process
     */
    public function getProcess($processUid)
    {
        try {
            //Verify data
            $this->throwExceptionIfNotExistsProcess($processUid, $this->arrayFieldNameForException["processUid"]);

            //Get data
            //Load Process
            $process = new \Process();
            $calendar = new \Calendar();

            $arrayProcessData = $process->load($processUid);
            $arrayCalendarInfo = $calendar->getCalendarFor($processUid, $processUid, $processUid);

            $arrayProcessData["PRO_ASSIGNMENT"] = ($arrayProcessData["PRO_ASSIGNMENT"] == "TRUE")? 1 : 0;
            $arrayProcessData["PRO_SUMMARY_DYNAFORM"] = (isset($arrayProcessData["PRO_DYNAFORMS"]["PROCESS"])? $arrayProcessData["PRO_DYNAFORMS"]["PROCESS"] : "");

            //If the function returns a DEFAULT calendar it means that this object doesn't have assigned any calendar
            $arrayProcessData["PRO_CALENDAR"] = ($arrayCalendarInfo["CALENDAR_APPLIED"] != "DEFAULT")? $arrayCalendarInfo["CALENDAR_UID"] : "";

            //Return
            unset($arrayProcessData["PRO_DYNAFORMS"]);
            unset($arrayProcessData["PRO_WIDTH"]);
            unset($arrayProcessData["PRO_HEIGHT"]);
            unset($arrayProcessData["PRO_TITLE_X"]);
            unset($arrayProcessData["PRO_TITLE_Y"]);
            unset($arrayProcessData["PRO_CATEGORY_LABEL"]);

            $processTitle = $arrayProcessData["PRO_TITLE"];
            $processDescription = $arrayProcessData["PRO_DESCRIPTION"];

            unset($arrayProcessData["PRO_UID"]);
            unset($arrayProcessData["PRO_TITLE"]);
            unset($arrayProcessData["PRO_DESCRIPTION"]);

            $arrayProcessData = array_merge(array("PRO_UID" => $processUid, "PRO_TITLE" => $processTitle, "PRO_DESCRIPTION" => $processDescription), $arrayProcessData);

            if (!$this->formatFieldNameInUppercase) {
                $arrayProcessData = array_change_key_case($arrayProcessData, CASE_LOWER);
            }

            return $arrayProcessData;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Create Route
     *
     * @param string $processUid
     * @param string $taskUid
     * @param string $nextTaskUid
     * @param string $type
     * @param bool   $delete
     *
     * return string Return UID of new Route
     *
     * @access public
     */
    public function defineRoute($processUid, $taskUid, $nextTaskUid, $type, $delete = false)
    {
        //Copy of processmaker/workflow/engine/methods/processes/processes_Ajax.php //case 'saveNewPattern':

        $processMap = new \processMap();

        if ($type != "SEQUENTIAL" && $type != "SEC-JOIN" && $type != "DISCRIMINATOR") {
            if ($processMap->getNumberOfRoutes($processUid, $taskUid, $nextTaskUid, $type) > 0) {
                //die();
                throw (new \Exception());
            }

            //unset($aRow);
        }

        if ($delete || $type == "SEQUENTIAL" || $type == "SEC-JOIN" || $type == "DISCRIMINATOR") {
            //\G::LoadClass("tasks");

            $tasks = new \Tasks();

            $tasks->deleteAllRoutesOfTask($processUid, $taskUid);
            $tasks->deleteAllGatewayOfTask($processUid, $taskUid);
        }

        return $processMap->saveNewPattern($processUid, $taskUid, $nextTaskUid, $type, $delete);
    }

    /**
     * Create/Update Process
     *
     * @param string $option
     * @param array  $arrayDefineProcessData
     *
     * return array  Return data array with new UID for each element
     *
     * @access public
     */
    public function defineProcess($option, $arrayDefineProcessData)
    {
        if (!isset($arrayDefineProcessData["process"]) || count($arrayDefineProcessData["process"]) == 0) {
            throw (new \Exception("Process data do not exist"));
        }

        //Process
        $process = new \Process();

        $arrayProcessData = $arrayDefineProcessData["process"];

        unset($arrayProcessData["tasks"]);
        unset($arrayProcessData["routes"]);

        switch ($option) {
            case "CREATE":
                if (!isset($arrayProcessData["USR_UID"]) || trim($arrayProcessData["USR_UID"]) == "") {
                    throw (new \Exception("User data do not exist"));
                }

                if (!isset($arrayProcessData["PRO_TITLE"]) || trim($arrayProcessData["PRO_TITLE"]) == "") {
                    throw (new \Exception("Process title data do not exist"));
                }

                if (!isset($arrayProcessData["PRO_DESCRIPTION"])) {
                    throw (new \Exception("Process description data do not exist"));
                }

                if (!isset($arrayProcessData["PRO_CATEGORY"])) {
                    throw (new \Exception("Process category data do not exist"));
                }
                break;
            case "UPDATE":
                break;
        }

        if (isset($arrayProcessData["PRO_TITLE"])) {
            $arrayProcessData["PRO_TITLE"] = trim($arrayProcessData["PRO_TITLE"]);
        }

        if (isset($arrayProcessData["PRO_DESCRIPTION"])) {
            $arrayProcessData["PRO_DESCRIPTION"] = trim($arrayProcessData["PRO_DESCRIPTION"]);
        }

        if (isset($arrayProcessData["PRO_TITLE"]) && $process->existsByProTitle($arrayProcessData["PRO_TITLE"])) {
            throw (new \Exception(\G::LoadTranslation("ID_PROCESSTITLE_ALREADY_EXISTS", SYS_LANG, array("PRO_TITLE" => $arrayProcessData["PRO_TITLE"]))));
        }

        $arrayProcessData["PRO_DYNAFORMS"] = array ();
        $arrayProcessData["PRO_DYNAFORMS"]["PROCESS"] = (isset($arrayProcessData["PRO_SUMMARY_DYNAFORM"]))? $arrayProcessData["PRO_SUMMARY_DYNAFORM"] : "";

        unset($arrayProcessData["PRO_SUMMARY_DYNAFORM"]);

        switch ($option) {
            case "CREATE":
                $processUid = $process->create($arrayProcessData, false);

                //Call plugins
                //$arrayData = array(
                //    "PRO_UID"      => $processUid,
                //    "PRO_TEMPLATE" => (isset($arrayProcessData["PRO_TEMPLATE"]) && $arrayProcessData["PRO_TEMPLATE"] != "")? $arrayProcessData["PRO_TEMPLATE"] : "",
                //    "PROCESSMAP"   => $this //?
                //);
                //
                //$oPluginRegistry = &PMPluginRegistry::getSingleton();
                //$oPluginRegistry->executeTriggers(PM_NEW_PROCESS_SAVE, $arrayData);
                break;
            case "UPDATE":
                $result = $process->update($arrayProcessData);

                $processUid = $arrayProcessData["PRO_UID"];
                break;
        }

        //Process - Save Calendar ID for this process
        if (isset($arrayProcessData["PRO_CALENDAR"]) && $arrayProcessData["PRO_CALENDAR"] != "") {
            $calendar = new \Calendar();
            $calendar->assignCalendarTo($processUid, $arrayProcessData["PRO_CALENDAR"], "PROCESS");
        }

        $uidAux = $arrayDefineProcessData["process"]["PRO_UID"];
        $arrayDefineProcessData["process"]["PRO_UID"] = $processUid;
        $arrayDefineProcessData["process"]["PRO_UID_OLD"] = $uidAux;

        //Tasks
        if (isset($arrayDefineProcessData["process"]["tasks"]) && count($arrayDefineProcessData["process"]["tasks"]) > 0) {
            $arrayTaskData = $arrayDefineProcessData["process"]["tasks"];

            foreach ($arrayTaskData as $index => $value) {
                $t = $value;
                $t["PRO_UID"] = $processUid;

                $arrayData = $t;

                $action = $arrayData["_action"];

                unset($arrayData["_action"]);

                switch ($action) {
                    case "CREATE":
                        //Create task
                        $arrayDataAux = array(
                            "TAS_UID"   => $arrayData["TAS_UID"],
                            "PRO_UID"   => $arrayData["PRO_UID"],
                            "TAS_TITLE" => $arrayData["TAS_TITLE"],
                            "TAS_DESCRIPTION" => $arrayData["TAS_DESCRIPTION"],
                            "TAS_POSX"  => $arrayData["TAS_POSX"],
                            "TAS_POSY"  => $arrayData["TAS_POSY"],
                            "TAS_START" => $arrayData["TAS_START"]
                        );

                        $task = new \Task();

                        $taskUid = $task->create($arrayDataAux, false);

                        $uidAux = $arrayDefineProcessData["process"]["tasks"][$index]["TAS_UID"];
                        $arrayDefineProcessData["process"]["tasks"][$index]["TAS_UID"] = $taskUid;
                        $arrayDefineProcessData["process"]["tasks"][$index]["TAS_UID_OLD"] = $uidAux;

                        //Update task properties
                        $task2 = new \ProcessMaker\BusinessModel\Task();

                        $arrayResult = $task2->updateProperties($taskUid, $processUid, $arrayData);

                        //Update array routes
                        if (isset($arrayDefineProcessData["process"]["routes"]) && count($arrayDefineProcessData["process"]["routes"]) > 0) {
                            $arrayDefineProcessData["process"]["routes"] = $this->routeUpdateTaskUidInArray($arrayDefineProcessData["process"]["routes"], $taskUid, $t["TAS_UID"]);
                        }
                        break;
                    case "UPDATE":
                        //Update task
                        $task = new \Task();

                        $result = $task->update($arrayData);

                        //Update task properties
                        $task2 = new \ProcessMaker\BusinessModel\Task();

                        $arrayResult = $task2->updateProperties($arrayData["TAS_UID"], $processUid, $arrayData);
                        break;
                    case "DELETE":
                        $tasks = new \Tasks();

                        $tasks->deleteTask($arrayData["TAS_UID"]);
                        break;
                }
            }
        }

        //Routes
        if (isset($arrayDefineProcessData["process"]["routes"]) && count($arrayDefineProcessData["process"]["routes"]) > 0) {
            $arrayRouteData = $arrayDefineProcessData["process"]["routes"];

            foreach ($arrayRouteData as $index => $value) {
                $r = $value;

                $routeUid = $this->defineRoute( //***** New method
                    $processUid,
                    $r["TAS_UID"],
                    $r["ROU_NEXT_TASK"],
                    $r["ROU_TYPE"],
                    false
                );

                $uidAux = $arrayDefineProcessData["process"]["routes"][$index]["ROU_UID"];
                $arrayDefineProcessData["process"]["routes"][$index]["ROU_UID"] = $routeUid;
                $arrayDefineProcessData["process"]["routes"][$index]["ROU_UID_OLD"] = $uidAux;
            }
        }

        return $arrayDefineProcessData;
    }

    /**
     * Update UID in array
     *
     * @param array  $arrayData
     * @param string $taskUid
     * @param string $taskUidOld
     *
     * return array  Return data array with UID updated
     *
     * @access public
     */
    public function routeUpdateTaskUidInArray($arrayData, $taskUid, $taskUidOld)
    {
        foreach ($arrayData as $index => $value) {
            $r = $value;

            if ($r["TAS_UID"] == $taskUidOld) {
                $arrayData[$index]["TAS_UID"] = $taskUid;
            }

            if ($r["ROU_NEXT_TASK"] == $taskUidOld) {
                $arrayData[$index]["ROU_NEXT_TASK"] = $taskUid;
            }
        }

        return $arrayData;
    }

    /**
     * Create Process
     *
     * @param string $userUid
     * @param array  $arrayDefineProcessData
     *
     * return array  Return data array with new UID for each element
     *
     * @access public
     */
    public function createProcess($userUid, $arrayDefineProcessData)
    {
        $arrayDefineProcessData["process"]["USR_UID"] = $userUid;

        return $this->defineProcess("CREATE", $arrayDefineProcessData);
    }

    /**
     * Load all Process
     *
     * @param array $arrayFilterData
     * @param int   $start
     * @param int   $limit
     *
     * return array Return data array with the Process
     *
     * @access public
     */
    public function loadAllProcess($arrayFilterData = array(), $start = 0, $limit = 25)
    {
        //Copy of processmaker/workflow/engine/methods/processes/processesList.php

        $process = new \Process();

        $memcache = &\PMmemcached::getSingleton(SYS_SYS);

        $memkey = "no memcache";
        $memcacheUsed = "not used";
        $totalCount = 0;

        if (isset($arrayFilterData["category"]) && $arrayFilterData["category"] !== "<reset>") {
            if (isset($arrayFilterData["processName"])) {
                $proData = $process->getAllProcesses($start, $limit, $arrayFilterData["category"], $arrayFilterData["processName"]);
            } else {
                $proData = $process->getAllProcesses($start, $limit, $arrayFilterData["category"]);
            }
        } else {
            if (isset($arrayFilterData["processName"])) {
                $memkey = "processList-" . $start . "-" . $limit . "-" . $arrayFilterData["processName"];
                $memcacheUsed = "yes";

                if (($proData = $memcache->get($memkey)) === false) {
                    $proData = $process->getAllProcesses($start, $limit, null, $arrayFilterData["processName"]);
                    $memcache->set($memkey, $proData, \PMmemcached::ONE_HOUR);
                    $memcacheUsed = "no";
                }
            } else {
                $memkey = "processList-allProcesses-" . $start . "-" . $limit;
                $memkeyTotal = $memkey . "-total";
                $memcacheUsed = "yes";

                if (($proData = $memcache->get($memkey)) === false || ($totalCount = $memcache->get($memkeyTotal)) === false) {
                    $proData = $process->getAllProcesses($start, $limit);
                    $totalCount = $process->getAllProcessesCount();
                    $memcache->set($memkey, $proData, \PMmemcached::ONE_HOUR);
                    $memcache->set($memkeyTotal, $totalCount, \PMmemcached::ONE_HOUR);
                    $memcacheUsed = "no";
                }
            }
        }

        $arrayData = array(
            "memkey"     => $memkey,
            "memcache"   => $memcacheUsed,
            "data"       => $proData,
            "totalCount" => $totalCount
        );

        return $arrayData;
    }

    /**
     * Load data of the Process
     *
     * @param string $processUid
     *
     * return array  Return data array with data of the Process (attributes of the process, tasks and routes)
     *
     * @access public
     */
    public function loadProcess($processUid)
    {
        $arrayDefineProcessData = array();

        //Process
        $process = new \Process();

        $arrayProcessData = $process->load($processUid);

        $arrayDefineProcessData["process"] = array(
            "PRO_UID"   => $processUid,
            "PRO_TITLE" => $arrayProcessData["PRO_TITLE"],
            "PRO_DESCRIPTION" => $arrayProcessData["PRO_DESCRIPTION"],
            "PRO_CATEGORY"    => $arrayProcessData["PRO_CATEGORY"]
        );

        //Load data
        $processMap = new \processMap();

        $arrayData = (array)(\Bootstrap::json_decode($processMap->load($processUid)));

        //Tasks & Routes
        $arrayDefineProcessData["process"]["tasks"]  = array();
        $arrayDefineProcessData["process"]["routes"] = array();

        if (isset($arrayData["task"]) && count($arrayData["task"]) > 0) {
            foreach ($arrayData["task"] as $indext => $valuet) {
                $t = (array)($valuet);

                $taskUid = $t["uid"];

                //Load task data
                $task = new \Task();

                $arrayTaskData = $task->load($taskUid);

                //Set task
                $arrayDefineProcessData["process"]["tasks"][] = array(
                    "TAS_UID"   => $taskUid,
                    "TAS_TITLE" => $arrayTaskData["TAS_TITLE"],
                    "TAS_DESCRIPTION" => $arrayTaskData["TAS_DESCRIPTION"],
                    "TAS_POSX"  => $arrayTaskData["TAS_POSX"],
                    "TAS_POSY"  => $arrayTaskData["TAS_POSY"],
                    "TAS_START" => $arrayTaskData["TAS_START"]
                );

                //Routes
                if (isset($t["derivation"])) {
                    $t["derivation"] = (array)($t["derivation"]);

                    $type = "";

                    switch ($t["derivation"]["type"]) {
                        case 0:
                            $type = "SEQUENTIAL";
                            break;
                        case 1:
                            $type = "SELECT";
                            break;
                        case 2:
                            $type = "EVALUATE";
                            break;
                        case 3:
                            $type = "PARALLEL";
                            break;
                        case 4:
                            $type = "PARALLEL-BY-EVALUATION";
                            break;
                        case 5:
                            $type = "SEC-JOIN";
                            break;
                        case 8:
                            $type = "DISCRIMINATOR";
                            break;
                    }

                    foreach ($t["derivation"]["to"] as $indexr => $valuer) {
                        $r = (array)($valuer);

                        //Criteria
                        $criteria = new \Criteria("workflow");

                        $criteria->addSelectColumn(\RoutePeer::ROU_UID);
                        $criteria->add(\RoutePeer::PRO_UID, $processUid, \Criteria::EQUAL);
                        $criteria->add(\RoutePeer::TAS_UID, $taskUid, \Criteria::EQUAL);
                        $criteria->add(\RoutePeer::ROU_NEXT_TASK, $r["task"], \Criteria::EQUAL);

                        $rsCriteria = \RoutePeer::doSelectRS($criteria);
                        $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

                        $rsCriteria->next();

                        $row = $rsCriteria->getRow();

                        $routeUid = $row["ROU_UID"];

                        //Set route
                        $arrayDefineProcessData["process"]["routes"][] = array(
                            "ROU_UID" => $routeUid,
                            "TAS_UID" => $taskUid,
                            "ROU_NEXT_TASK" => $r["task"],
                            "ROU_TYPE" => $type
                        );
                    }
                }
            }
        }

        return $arrayDefineProcessData;
    }

    /**
     * Update Process
     *
     * @param string $processUid
     * @param string $userUid
     * @param array  $arrayDefineProcessData
     *
     * return array
     *
     * @access public
     */
    public function updateProcess($processUid, $userUid, $arrayDefineProcessData)
    {
        $arrayDefineProcessData["process"]["PRO_UID"] = $processUid;
        $arrayDefineProcessData["process"]["USR_UID"] = $userUid;

        return $this->defineProcess("UPDATE", $arrayDefineProcessData);
    }

    /**
     * Delete Process
     *
     * @param string $processUid
     * @param bool   $checkCases
     *
     * return bool   Return true, if is succesfully
     *
     * @access public

    DEPRECATED
    public function deleteProcess($processUid, $checkCases = true)
    {
        if ($checkCases) {
            $process = new \Process();

            $arrayCases = $process->getCasesCountInAllProcesses($processUid);

            $sum = 0;

            if (isset($arrayCases[$processUid]) && count($arrayCases[$processUid]) > 0) {
                foreach ($arrayCases[$processUid] as $value) {
                    $sum = $sum + $value;
                }
            }

            if ($sum > 0) {
                throw (new \Exception("You can't delete the process, because it has $sum cases"));
            }
        }

        $processMap = new \processMap();

        return $processMap->deleteProcess($processUid);

    }*/

    public function deleteProcess($sProcessUID)
    {
        try {
            G::LoadClass('case');
            G::LoadClass('reportTables');
            //Instance all classes necesaries
            $oProcess = new Process();
            $oDynaform = new Dynaform();
            $oInputDocument = new InputDocument();
            $oOutputDocument = new OutputDocument();
            $oTrigger = new Triggers();
            $oRoute = new Route();
            $oGateway = new Gateway();
            $oEvent = new Event();
            $oSwimlaneElement = new SwimlanesElements();
            $oConfiguration = new Configuration();
            $oDbSource = new DbSource();
            $oReportTable = new ReportTables();
            $oCaseTracker = new CaseTracker();
            $oCaseTrackerObject = new CaseTrackerObject();
            //Delete the applications of process
            $oCriteria = new Criteria('workflow');
            $oCriteria->add(ApplicationPeer::PRO_UID, $sProcessUID);
            $oDataset = ApplicationPeer::doSelectRS($oCriteria);
            $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $oDataset->next();
            $oCase = new Cases();
            while ($aRow = $oDataset->getRow()) {
                $oCase->removeCase($aRow['APP_UID']);
                $oDataset->next();
            }
            //Delete the tasks of process
            $oCriteria = new Criteria('workflow');
            $oCriteria->add(TaskPeer::PRO_UID, $sProcessUID);
            $oDataset = TaskPeer::doSelectRS($oCriteria);
            $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $oDataset->next();
            while ($aRow = $oDataset->getRow()) {
                $this->deleteTask($aRow['TAS_UID']);
                $oDataset->next();
            }
            //Delete the dynaforms of process
            $oCriteria = new Criteria('workflow');
            $oCriteria->add(DynaformPeer::PRO_UID, $sProcessUID);
            $oDataset = DynaformPeer::doSelectRS($oCriteria);
            $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $oDataset->next();
            while ($aRow = $oDataset->getRow()) {
                $oDynaform->remove($aRow['DYN_UID']);
                $oDataset->next();
            }
            //Delete the input documents of process
            $oCriteria = new Criteria('workflow');
            $oCriteria->add(InputDocumentPeer::PRO_UID, $sProcessUID);
            $oDataset = InputDocumentPeer::doSelectRS($oCriteria);
            $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $oDataset->next();
            while ($aRow = $oDataset->getRow()) {
                $oInputDocument->remove($aRow['INP_DOC_UID']);
                $oDataset->next();
            }
            //Delete the output documents of process
            $oCriteria = new Criteria('workflow');
            $oCriteria->add(OutputDocumentPeer::PRO_UID, $sProcessUID);
            $oDataset = OutputDocumentPeer::doSelectRS($oCriteria);
            $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $oDataset->next();
            while ($aRow = $oDataset->getRow()) {
                $oOutputDocument->remove($aRow['OUT_DOC_UID']);
                $oDataset->next();
            }

            //Delete the triggers of process
            $oCriteria = new Criteria('workflow');
            $oCriteria->add(TriggersPeer::PRO_UID, $sProcessUID);
            $oDataset = TriggersPeer::doSelectRS($oCriteria);
            $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $oDataset->next();
            while ($aRow = $oDataset->getRow()) {
                $oTrigger->remove($aRow['TRI_UID']);
                $oDataset->next();
            }

            //Delete the routes of process
            $oCriteria = new Criteria('workflow');
            $oCriteria->add(RoutePeer::PRO_UID, $sProcessUID);
            $oDataset = RoutePeer::doSelectRS($oCriteria);
            $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $oDataset->next();
            while ($aRow = $oDataset->getRow()) {
                $oRoute->remove($aRow['ROU_UID']);
                $oDataset->next();
            }

            //Delete the gateways of process
            $oCriteria = new Criteria('workflow');
            $oCriteria->add(GatewayPeer::PRO_UID, $sProcessUID);
            $oDataset = GatewayPeer::doSelectRS($oCriteria);
            $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $oDataset->next();
            while ($aRow = $oDataset->getRow()) {
                $oGateway->remove($aRow['GAT_UID']);
                $oDataset->next();
            }

            //Delete the Event of process
            $oCriteria = new Criteria('workflow');
            $oCriteria->add(EventPeer::PRO_UID, $sProcessUID);
            $oDataset = EventPeer::doSelectRS($oCriteria);
            $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $oDataset->next();
            while ($aRow = $oDataset->getRow()) {
                $oEvent->remove($aRow['EVN_UID']);
                $oDataset->next();
            }

            //Delete the swimlanes elements of process
            $oCriteria = new Criteria('workflow');
            $oCriteria->add(SwimlanesElementsPeer::PRO_UID, $sProcessUID);
            $oDataset = SwimlanesElementsPeer::doSelectRS($oCriteria);
            $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $oDataset->next();
            while ($aRow = $oDataset->getRow()) {
                $oSwimlaneElement->remove($aRow['SWI_UID']);
                $oDataset->next();
            }
            //Delete the configurations of process
            $oCriteria = new Criteria('workflow');
            $oCriteria->add(ConfigurationPeer::PRO_UID, $sProcessUID);
            $oDataset = ConfigurationPeer::doSelectRS($oCriteria);
            $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $oDataset->next();
            while ($aRow = $oDataset->getRow()) {
                $oConfiguration->remove($aRow['CFG_UID'], $aRow['OBJ_UID'], $aRow['PRO_UID'], $aRow['USR_UID'], $aRow['APP_UID']);
                $oDataset->next();
            }
            //Delete the DB sources of process
            $oCriteria = new Criteria('workflow');
            $oCriteria->add(DbSourcePeer::PRO_UID, $sProcessUID);
            $oDataset = DbSourcePeer::doSelectRS($oCriteria);
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
            $oCriteria->add(ProcessUserPeer::PRO_UID, $sProcessUID);
            ProcessUserPeer::doDelete($oCriteria);
            //Delete the object permissions
            $oCriteria = new Criteria('workflow');
            $oCriteria->add(ObjectPermissionPeer::PRO_UID, $sProcessUID);
            ObjectPermissionPeer::doDelete($oCriteria);
            //Delete the step supervisors
            $oCriteria = new Criteria('workflow');
            $oCriteria->add(StepSupervisorPeer::PRO_UID, $sProcessUID);
            StepSupervisorPeer::doDelete($oCriteria);
            //Delete the report tables
            $oCriteria = new Criteria('workflow');
            $oCriteria->add(ReportTablePeer::PRO_UID, $sProcessUID);
            $oDataset = ReportTablePeer::doSelectRS($oCriteria);
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
            $oCriteria->add(CaseTrackerObjectPeer::PRO_UID, $sProcessUID);
            ProcessUserPeer::doDelete($oCriteria);
            //Delete the process
            try {
                $oProcess->remove($sProcessUID);
            } catch (Exception $oError) {
                throw ($oError);
            }
            return true;
        } catch (Exception $oError) {
            throw ($oError);
        }
    }

    /**
     * Get all DynaForms of a Process
     *
     * @param string $processUid Unique id of Process
     *
     * return array Return an array with all DynaForms of a Process
     */
    public function getDynaForms($processUid)
    {
        try {
            $arrayDynaForm = array();

            //Verify data
            $this->throwExceptionIfNotExistsProcess($processUid, $this->arrayFieldNameForException["processUid"]);

            //Get data
            $dynaForm = new \ProcessMaker\BusinessModel\DynaForm();
            $dynaForm->setFormatFieldNameInUppercase($this->formatFieldNameInUppercase);
            $dynaForm->setArrayFieldNameForException($this->arrayFieldNameForException);

            $criteria = $dynaForm->getDynaFormCriteria();

            $criteria->add(\DynaformPeer::PRO_UID, $processUid, \Criteria::EQUAL);
            $criteria->addAscendingOrderByColumn("DYN_TITLE");

            $rsCriteria = \DynaformPeer::doSelectRS($criteria);
            $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

            while ($rsCriteria->next()) {
                $row = $rsCriteria->getRow();

                $arrayDynaForm[] = $dynaForm->getDynaFormDataFromRecord($row);
            }

            //Return
            return $arrayDynaForm;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get all InputDocuments of a Process
     *
     * @param string $processUid Unique id of Process
     *
     * return array Return an array with all InputDocuments of a Process
     */
    public function getInputDocuments($processUid)
    {
        try {
            $arrayInputDocument = array();

            //Verify data
            $this->throwExceptionIfNotExistsProcess($processUid, $this->arrayFieldNameForException["processUid"]);

            //Get data
            $inputDocument = new \ProcessMaker\BusinessModel\InputDocument();
            $inputDocument->setFormatFieldNameInUppercase($this->formatFieldNameInUppercase);
            $inputDocument->setArrayFieldNameForException($this->arrayFieldNameForException);

            $criteria = $inputDocument->getInputDocumentCriteria();

            $criteria->add(\InputDocumentPeer::PRO_UID, $processUid, \Criteria::EQUAL);
            $criteria->addAscendingOrderByColumn("INP_DOC_TITLE");

            $rsCriteria = \InputDocumentPeer::doSelectRS($criteria);
            $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

            while ($rsCriteria->next()) {
                $row = $rsCriteria->getRow();

                $arrayInputDocument[] = $inputDocument->getInputDocumentDataFromRecord($row);
            }

            //Return
            return $arrayInputDocument;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get field names which are primary key in a PM Table
     *
     * @param string $additionalTableUid    Unique id of PM Table
     * @param string $fieldNameForException Field name for the exception
     *
     * return array Return data with the primary keys
     */
    public function getPmTablePrimaryKeyFields($additionalTableUid, $fieldNameForException)
    {
        try {
            $arrayFieldPk = array();

            //Verify data
            $this->throwExceptionIfNotExistsPmTable($additionalTableUid, $fieldNameForException);

            //Get data
            //Load AdditionalTable
            $additionalTable = new \AdditionalTables();

            $arrayAdditionalTableData = $additionalTable->load($additionalTableUid, true);

            foreach ($arrayAdditionalTableData["FIELDS"] as $key => $value) {
                if ($value["FLD_KEY"] == 1) {
                    //Primary Key
                    $arrayFieldPk[] = $value["FLD_NAME"];
                }
            }

            //Return
            return $arrayFieldPk;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get data of a Variable from a record
     *
     * @param array $record Record
     *
     * return array Return an array with data Variable
     */
    public function getVariableDataFromRecord($record)
    {
        try {
            return array(
                $this->getFieldNameByFormatFieldName("VAR_NAME")  => trim($record["name"]),
                $this->getFieldNameByFormatFieldName("VAR_LABEL") => trim($record["label"]),
                $this->getFieldNameByFormatFieldName("VAR_TYPE")  => trim($record["type"]),
                $this->getFieldNameByFormatFieldName("VAR_SOURCE")  => trim($record["source"])
            );
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get Variables of a Process/Grid
     *
     * @param string $option     Option (GRID, GRIDVARS, ALL)
     * @param string $processUid Unique id of Process
     * @param string $gridUid    Unique id of Grid (DynaForm)
     *
     * return array Return an array with Variables of a Process/Grid
     */
    public function getVariables($option, $processUid, $gridUid = "")
    {
        try {
            $arrayVariable = array();

            //Verify data
            $this->throwExceptionIfNotExistsProcess($processUid, $this->arrayFieldNameForException["processUid"]);

            //Get data
            switch ($option) {
                case "GRID":
                    $arrayVar = self::getGridsVars($processUid);

                    foreach ($arrayVar as $key => $value) {
                        $arrayVariableAux = $this->getVariableDataFromRecord(array("name" => $value["sName"], "label" => "[ " . \G::LoadTranslation("ID_GRID") . " ]", "type" => "grid"));

                        $arrayVariable[] = array_merge($arrayVariableAux, array($this->getFieldNameByFormatFieldName("GRID_UID") => $value["sXmlForm"]));
                    }
                    break;
                case "GRIDVARS":
                    //Verify data
                    $dynaForm = new \ProcessMaker\BusinessModel\DynaForm();

                    $dynaForm->throwExceptionIfNotExistsDynaForm($gridUid, $processUid, $this->arrayFieldNameForException["gridUid"]);
                    $dynaForm->throwExceptionIfNotIsGridDynaForm($gridUid, $this->arrayFieldNameForException["gridUid"]);

                    //Get data
                    $fields = self::getVarsGrid($processUid, $gridUid);
                    foreach ($fields as $field) {
                        $arrayVariable[] = $this->getVariableDataFromRecord(array("name" => $field["sName"], "label" => $field["sLabel"], "type" => $field["sType"]));
                    }

                    break;
                default:
                    //ALL
                    $arrayVar = self::getDynaformsVars($processUid);

                    foreach ($arrayVar as $key => $value) {
                        $arrayVariable[] = $this->getVariableDataFromRecord(array("name" => $value["sName"], "label" => $value["sLabel"], "type" => $value["sType"], "source" => $value["sUid"]));
                    }

                    $arrayHtmlVariable = self::getHtmlFormVars($processUid);
                    $arrayVariable = array_merge($arrayVariable, $arrayHtmlVariable);

                    break;
            }

            //Return
            return $arrayVariable;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get all Libraries
     *
     * @param string $processUid Unique id of Process
     *
     * return array Return an array with all Libraries
     */
    public function getLibraries($processUid)
    {
        try {
            $arrayLibrary = array();

            //Verify data
            $this->throwExceptionIfNotExistsProcess($processUid, $this->arrayFieldNameForException["processUid"]);

            //Get data
            \G::LoadClass("triggerLibrary");

            $triggerWizard = new \ProcessMaker\BusinessModel\TriggerWizard();
            $triggerWizard->setFormatFieldNameInUppercase($this->formatFieldNameInUppercase);
            $triggerWizard->setArrayFieldNameForException($this->arrayFieldNameForException);

            $triggerLibrary = \triggerLibrary::getSingleton();
            $library = $triggerLibrary->getRegisteredClasses();

            ksort($library);

            foreach ($library as $key => $value) {
                $libraryName = (preg_match("/^class\.?(.*)\.pmFunctions\.php$/", $key, $arrayMatch))? ((isset($arrayMatch[1]) && $arrayMatch[1] != "")? $arrayMatch[1] : "pmFunctions") : $key;

                $arrayLibrary[] = $triggerWizard->getLibrary($libraryName);
            }

            //Return
            return $arrayLibrary;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Function getDynaformsVars
     *
     * @access public
     * @param eter string $sProcessUID
     * @param eter boolean $bSystemVars
     * @return array
     */
    public static function getDynaformsVars ($sProcessUID, $bSystemVars = true, $bIncMulSelFields = 0)
    {
        $aFields = array ();
        $aFieldsNames = array ();
        if ($bSystemVars) {
            $aAux = G::getSystemConstants();
            foreach ($aAux as $sName => $sValue) {
                $aFields[] = array ('sName' => $sName,'sType' => 'system','sLabel' => G::LoadTranslation('ID_TINY_SYSTEM_VARIABLE'), 'sUid' => '');
            }
            //we're adding the ping variable to the system list
            $aFields[] = array ('sName' => 'PIN','sType' => 'system','sLabel' => G::LoadTranslation('ID_TINY_SYSTEM_VARIABLE'), 'sUid' => '');
        }

        $aInvalidTypes = array("title", "subtitle", "file", "button", "reset", "submit", "javascript");
        $aMultipleSelectionFields = array("listbox", "checkgroup", "grid");

        if ($bIncMulSelFields != 0) {
            $aInvalidTypes = array_merge( $aInvalidTypes, $aMultipleSelectionFields );
        }

        $oCriteria = new Criteria( 'workflow' );
        $oCriteria->addSelectColumn( \DynaformPeer::DYN_FILENAME );
        $oCriteria->addSelectColumn( \DynaformPeer::DYN_UID );
        $oCriteria->add( \DynaformPeer::PRO_UID, $sProcessUID );
        $oCriteria->add( \DynaformPeer::DYN_TYPE, 'xmlform' );
        $oDataset = \DynaformPeer::doSelectRS( $oCriteria );
        $oDataset->setFetchmode( \ResultSet::FETCHMODE_ASSOC );
        $oDataset->next();

        while ($aRow = $oDataset->getRow()) {
            if (is_file(PATH_DYNAFORM . $aRow['DYN_FILENAME'] . ".xml")) {
                $dyn = new \dynaFormHandler(PATH_DYNAFORM . $aRow['DYN_FILENAME'] . ".xml");

                if ($dyn->getHeaderAttribute("type") !== "xmlform" && $dyn->getHeaderAttribute("type") !== "") {
                    // skip it, if that is not a xmlform
                    $oDataset->next();
                    continue;
                }

                $fields = $dyn->getFields();

                foreach ($fields as $field) {
                    $label = "";
                    if ($field->hasChildNodes()) {
                        $child = $field->getElementsByTagName(SYS_LANG)->length ? $field->getElementsByTagName(SYS_LANG): $field->getElementsByTagName("en");
                        $label = $child->item(0) ? $child->item(0)->textContent : "";
                    }

                    if (! in_array($field->getAttribute("type"), $aInvalidTypes) && ! in_array($field->tagName, $aFieldsNames)) {
                        $aFieldsNames[] = $field->tagName;
                        $aFields[] = array (
                            'sName' => $field->tagName,
                            'sType' => $field->getAttribute("type"),
                            'sLabel' => ($field->getAttribute("type") != 'grid' ? $label : '[ ' . G::LoadTranslation('ID_GRID') . ' ]'),
                            'sUid' => $aRow['DYN_UID']);
                    }
                }
            }

            $oDataset->next();
        }
        return $aFields;
    }

    /**
     * Function getGridsVars
     *
     * @access public
     * @param eter string $sProcessUID
     * @return array
     */
    public static function getGridsVars ($sProcessUID)
    {
        $aFields = array ();
        $aFieldsNames = array ();

        $oCriteria = new Criteria( 'workflow' );
        $oCriteria->addSelectColumn( \DynaformPeer::DYN_FILENAME );
        $oCriteria->add( \DynaformPeer::PRO_UID, $sProcessUID );
        $oDataset = \DynaformPeer::doSelectRS( $oCriteria );
        $oDataset->setFetchmode( \ResultSet::FETCHMODE_ASSOC );
        $oDataset->next();
        while ($aRow = $oDataset->getRow()) {
            if (is_file(PATH_DYNAFORM . $aRow['DYN_FILENAME'] . ".xml")) {
                $dyn = new \dynaFormHandler(PATH_DYNAFORM . $aRow['DYN_FILENAME'] . ".xml");

                if ($dyn->getHeaderAttribute("type") === "xmlform") {
                    // skip it, if that is not a xmlform
                    $oDataset->next();
                    continue;
                }

                $fields = $dyn->getFields();

                foreach ($fields as $field) {
                    if ($field->getAttribute("type") !== "grid") {
                        continue;
                    }
                    if (! in_array($field->tagName, $aFieldsNames)) {
                        $aFieldsNames[] = $field->tagName;
                        $aFields[] = array (
                            'sName' => $field->tagName,
                            'sXmlForm' => $aRow['DYN_FILENAME']
                        );
                    }
                }
            }

            $oDataset->next();
        }
        return $aFields;
    }

    /**
     * Function getVarsGrid returns all variables of Grid
     *
     * @access public
     * @param string proUid process ID
     * @param string dynUid dynaform ID
     * @return array
     */
    public static function getVarsGrid ($proUid, $dynUid)
    {
        $dynaformFields = array ();
        $aFieldsNames = array();
        $aFields = array();
        $aInvalidTypes = array("title", "subtitle", "file", "button", "reset", "submit", "javascript");
        $aMultipleSelectionFields = array("listbox", "checkgroup", "grid");

        if (is_file( PATH_DATA . '/sites/'. SYS_SYS .'/xmlForms/'. $proUid .'/'.$dynUid. '.xml' ) && filesize( PATH_DATA . '/sites/'. SYS_SYS .'/xmlForms/'. $proUid .'/'. $dynUid .'.xml' ) > 0) {
            $dyn = new \dynaFormHandler( PATH_DATA . '/sites/'. SYS_SYS .'/xmlForms/' .$proUid. '/' . $dynUid .'.xml' );
            $dynaformFields[] = $dyn->getFields();

            $fields = $dyn->getFields();

            foreach ($fields as $field) {
                if ($field->getAttribute("type") !== "grid") {
                    continue;
                }
                if (! in_array($field->getAttribute("type"), $aInvalidTypes) && ! in_array($field->tagName, $aFieldsNames)) {
                    $aFieldsNames[] = $field->tagName;
                    $aFields[] = array (
                        'sName' => $field->tagName,
                        'sType' => $field->getAttribute("type"),
                        'sLabel' => ($field->getAttribute("type") != 'grid' ? $label : '[ ' . G::LoadTranslation('ID_GRID') . ' ]')
                    );
                }
            }
        }

        return $aFields;
    }

    /**
     * Function getHtmlFormVars
     *
     * @access public
     * @param string $sProcessUID
     * @return array
     */
    public static function getHtmlFormVars ($sProcessUID)
    {
        //Get data
        $criteria = new \Criteria("workflow");

        $criteria->addSelectColumn(\ProcessVariablesPeer::VAR_UID);
        $criteria->addSelectColumn(\ProcessVariablesPeer::VAR_NAME);
        $criteria->addSelectColumn(\ProcessVariablesPeer::VAR_LABEL);
        $criteria->addSelectColumn(\ProcessVariablesPeer::VAR_FIELD_TYPE);

        $criteria->add(\ProcessVariablesPeer::PRJ_UID, $sProcessUID, \Criteria::EQUAL);

        $rsCriteria = \ProcessVariablesPeer::doSelectRS($criteria);

        $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

        $rsCriteria->next();
        $arrayVariables = array();

        while ($aRow = $rsCriteria->getRow()) {
            $arrayVariables[] = array(  'var_name' => $aRow['VAR_NAME'],
                                        'var_label' => $aRow['VAR_LABEL'],
                                        'var_type' => $aRow['VAR_FIELD_TYPE'],
                                        'var_source' => $aRow['VAR_UID']);
            $rsCriteria->next();
        }
        //Return
        return $arrayVariables;

    }

}

