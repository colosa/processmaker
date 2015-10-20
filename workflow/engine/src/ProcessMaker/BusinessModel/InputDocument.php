<?php
namespace ProcessMaker\BusinessModel;

class InputDocument
{
    private $arrayFieldDefinition = array(
        "INP_DOC_UID"              => array("type" => "string", "required" => false, "empty" => false, "defaultValues" => array(),                                "fieldNameAux" => "inputDocumentUid"),

        "INP_DOC_TITLE"            => array("type" => "string", "required" => true,  "empty" => false, "defaultValues" => array(),                                "fieldNameAux" => "inputDocumentTitle"),
        "INP_DOC_DESCRIPTION"      => array("type" => "string", "required" => false, "empty" => true,  "defaultValues" => array(),                                "fieldNameAux" => "inputDocumentDescription"),
        "INP_DOC_FORM_NEEDED"      => array("type" => "string", "required" => false, "empty" => false, "defaultValues" => array("VIRTUAL", "REAL", "VREAL"),      "fieldNameAux" => "inputDocumentFormNeeded"),
        "INP_DOC_ORIGINAL"         => array("type" => "string", "required" => false, "empty" => false, "defaultValues" => array("ORIGINAL", "COPY", "COPYLEGAL"), "fieldNameAux" => "inputDocumentOriginal"),
        "INP_DOC_PUBLISHED"        => array("type" => "string", "required" => false, "empty" => false, "defaultValues" => array("PRIVATE", "PUBLIC"),             "fieldNameAux" => "inputDocumentPublished"),
        "INP_DOC_VERSIONING"       => array("type" => "int",    "required" => false, "empty" => false, "defaultValues" => array(0, 1),                            "fieldNameAux" => "inputDocumentVersioning"),
        "INP_DOC_DESTINATION_PATH" => array("type" => "string", "required" => false, "empty" => true,  "defaultValues" => array(),                                "fieldNameAux" => "inputDocumentDestinationPath"),
        "INP_DOC_TAGS"             => array("type" => "string", "required" => false, "empty" => true,  "defaultValues" => array(),                                "fieldNameAux" => "inputDocumentTags"),		
        "INP_DOC_TYPE_FILE"             => array("type" => "string", "required" => true, "empty" => false,  "defaultValues" => array(),                                "fieldNameAux" => "inputDocumentTypeFile"),
        "INP_DOC_MAX_FILESIZE"             => array("type" => "int", "required" => true, "empty" => false,  "defaultValues" => array(),                                "fieldNameAux" => "inputDocumentMaxFilesize"),
        "INP_DOC_MAX_FILESIZE_UNIT"             => array("type" => "string", "required" => true, "empty" => false,  "defaultValues" => array(),                                "fieldNameAux" => "inputDocumentMaxFilesizeUnit")
    );

    private $formatFieldNameInUppercase = true;

    private $arrayFieldNameForException = array(
        "processUid" => "PRO_UID"
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
     * Verify if exists the title of a InputDocument
     *
     * @param string $processUid              Unique id of Process
     * @param string $inputDocumentTitle      Title
     * @param string $inputDocumentUidExclude Unique id of InputDocument to exclude
     *
     * return bool Return true if exists the title of a InputDocument, false otherwise
     */
    public function existsTitle($processUid, $inputDocumentTitle, $inputDocumentUidExclude = "")
    {
        try {
            $delimiter = \DBAdapter::getStringDelimiter();

            $criteria = new \Criteria("workflow");

            $criteria->addSelectColumn(\InputDocumentPeer::INP_DOC_UID);

            $criteria->addAlias("CT", \ContentPeer::TABLE_NAME);

            $arrayCondition = array();
            $arrayCondition[] = array(\InputDocumentPeer::INP_DOC_UID, "CT.CON_ID", \Criteria::EQUAL);
            $arrayCondition[] = array("CT.CON_CATEGORY", $delimiter . "INP_DOC_TITLE" . $delimiter, \Criteria::EQUAL);
            $arrayCondition[] = array("CT.CON_LANG", $delimiter . SYS_LANG . $delimiter, \Criteria::EQUAL);
            $criteria->addJoinMC($arrayCondition, \Criteria::LEFT_JOIN);

            $criteria->add(\InputDocumentPeer::PRO_UID, $processUid, \Criteria::EQUAL);

            if ($inputDocumentUidExclude != "") {
                $criteria->add(\InputDocumentPeer::INP_DOC_UID, $inputDocumentUidExclude, \Criteria::NOT_EQUAL);
            }

            $criteria->add("CT.CON_VALUE", $inputDocumentTitle, \Criteria::EQUAL);

            $rsCriteria = \InputDocumentPeer::doSelectRS($criteria);

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
     * Verify if the InputDocument it's assigned in other objects
     *
     * @param string $inputDocumentUid Unique id of InputDocument
     *
     * return array Return array (true if it's assigned or false otherwise and data)
     */
    public function itsAssignedInOtherObjects($inputDocumentUid)
    {
        try {
            $flagAssigned = false;
            $arrayData = array();

            //Step
            $criteria = new \Criteria("workflow");

            $criteria->addSelectColumn(\StepPeer::STEP_UID);
            $criteria->add(\StepPeer::STEP_TYPE_OBJ, "INPUT_DOCUMENT", \Criteria::EQUAL);
            $criteria->add(\StepPeer::STEP_UID_OBJ, $inputDocumentUid, \Criteria::EQUAL);

            $rsCriteria = \StepPeer::doSelectRS($criteria);

            if ($rsCriteria->next()) {
                $flagAssigned = true;
                $arrayData[] = \G::LoadTranslation("ID_STEPS");
            }

            //StepSupervisor
            $criteria = new \Criteria("workflow");

            $criteria->addSelectColumn(\StepSupervisorPeer::STEP_UID);
            $criteria->add(\StepSupervisorPeer::STEP_TYPE_OBJ, "INPUT_DOCUMENT", \Criteria::EQUAL);
            $criteria->add(\StepSupervisorPeer::STEP_UID_OBJ, $inputDocumentUid, \Criteria::EQUAL);

            $rsCriteria = \StepSupervisorPeer::doSelectRS($criteria);

            if ($rsCriteria->next()) {
                $flagAssigned = true;
                $arrayData[] = \G::LoadTranslation("ID_CASES_MENU_ADMIN");
            }

            //ObjectPermission
            $criteria = new \Criteria("workflow");

            $criteria->addSelectColumn(\ObjectPermissionPeer::OP_UID);
            $criteria->add(\ObjectPermissionPeer::OP_OBJ_TYPE, "INPUT", \Criteria::EQUAL);
            $criteria->add(\ObjectPermissionPeer::OP_OBJ_UID, $inputDocumentUid, \Criteria::EQUAL);

            $rsCriteria = \ObjectPermissionPeer::doSelectRS($criteria);

            if ($rsCriteria->next()) {
                $flagAssigned = true;
                $arrayData[] = \G::LoadTranslation("ID_PROCESS_PERMISSIONS");
            }

            //Return
            return array($flagAssigned, $arrayData);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Verify if doesn't exists the InputDocument in table INPUT_DOCUMENT
     *
     * @param string $inputDocumentUid      Unique id of InputDocument
     * @param string $processUid            Unique id of Process
     * @param string $fieldNameForException Field name for the exception
     *
     * return void Throw exception if doesn't exists the InputDocument in table INPUT_DOCUMENT
     */
    public function throwExceptionIfNotExistsInputDocument($inputDocumentUid, $processUid, $fieldNameForException)
    {
        try {
            $criteria = new \Criteria("workflow");

            $criteria->addSelectColumn(\InputDocumentPeer::INP_DOC_UID);

            if ($processUid != "") {
                $criteria->add(\InputDocumentPeer::PRO_UID, $processUid, \Criteria::EQUAL);
            }

            $criteria->add(\InputDocumentPeer::INP_DOC_UID, $inputDocumentUid, \Criteria::EQUAL);

            $rsCriteria = \InputDocumentPeer::doSelectRS($criteria);

            if (!$rsCriteria->next()) {
                throw new \Exception(\G::LoadTranslation("ID_INPUT_DOCUMENT_DOES_NOT_EXIST", array($fieldNameForException, $inputDocumentUid)));
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Verify if exists the title of a InputDocument
     *
     * @param string $processUid              Unique id of Process
     * @param string $inputDocumentTitle      Title
     * @param string $fieldNameForException   Field name for the exception
     * @param string $inputDocumentUidExclude Unique id of InputDocument to exclude
     *
     * return void Throw exception if exists the title of a InputDocument
     */
    public function throwExceptionIfExistsTitle($processUid, $inputDocumentTitle, $fieldNameForException, $inputDocumentUidExclude = "")
    {
        try {
            if ($this->existsTitle($processUid, $inputDocumentTitle, $inputDocumentUidExclude)) {
                throw new \Exception(\G::LoadTranslation("ID_INPUT_DOCUMENT_TITLE_ALREADY_EXISTS", array($fieldNameForException, $inputDocumentTitle)));
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Verify if the InputDocument it's assigned in other objects
     *
     * @param string $inputDocumentUid      Unique id of InputDocument
     * @param string $fieldNameForException Field name for the exception
     *
     * return void Throw exception if the InputDocument it's assigned in other objects
     */
    public function throwExceptionIfItsAssignedInOtherObjects($inputDocumentUid, $fieldNameForException)
    {
        try {
            list($flagAssigned, $arrayData) = $this->itsAssignedInOtherObjects($inputDocumentUid);

            if ($flagAssigned) {
                throw new \Exception(\G::LoadTranslation("ID_INPUT_DOCUMENT_ITS_ASSIGNED", array($fieldNameForException, $inputDocumentUid, implode(", ", $arrayData))));
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Create InputDocument for a Process
     *
     * @param string $processUid Unique id of Process
     * @param array  $arrayData  Data
     *
     * return array Return data of the new InputDocument created
     */
    public function create($processUid, $arrayData)
    {
        try {
            $arrayData = array_change_key_case($arrayData, CASE_UPPER);

            unset($arrayData["INP_DOC_UID"]);

            //Verify data
            $process = new \ProcessMaker\BusinessModel\Process();

            $process->throwExceptionIfNotExistsProcess($processUid, $this->arrayFieldNameForException["processUid"]);

            $process->throwExceptionIfDataNotMetFieldDefinition($arrayData, $this->arrayFieldDefinition, $this->arrayFieldNameForException, true);

            $this->throwExceptionIfExistsTitle($processUid, $arrayData["INP_DOC_TITLE"], $this->arrayFieldNameForException["inputDocumentTitle"]);

            //Flags
            $flagDataDestinationPath = (isset($arrayData["INP_DOC_DESTINATION_PATH"]))? 1 : 0;
            $flagDataTags = (isset($arrayData["INP_DOC_TAGS"]))? 1 : 0;

            //Create
            $inputDocument = new \InputDocument();

            $arrayData["PRO_UID"] = $processUid;

            $arrayData["INP_DOC_DESTINATION_PATH"] = ($flagDataDestinationPath == 1)? $arrayData["INP_DOC_DESTINATION_PATH"] : "";
            $arrayData["INP_DOC_TAGS"] = ($flagDataTags == 1)? $arrayData["INP_DOC_TAGS"] : "";

            $inputDocumentUid = $inputDocument->create($arrayData);

            //Return
            unset($arrayData["PRO_UID"]);

            if ($flagDataDestinationPath == 0) {
                unset($arrayData["INP_DOC_DESTINATION_PATH"]);
            }

            if ($flagDataTags == 0) {
                unset($arrayData["INP_DOC_TAGS"]);
            }

            $arrayData = array_merge(array("INP_DOC_UID" => $inputDocumentUid), $arrayData);

            if (!$this->formatFieldNameInUppercase) {
                $arrayData = array_change_key_case($arrayData, CASE_LOWER);
            }

            return $arrayData;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Update InputDocument
     *
     * @param string $inputDocumentUid Unique id of InputDocument
     * @param array  $arrayData        Data
     *
     * return array Return data of the InputDocument updated
     */
    public function update($inputDocumentUid, $arrayData)
    {
        try {
            $arrayData = array_change_key_case($arrayData, CASE_UPPER);

            //Verify data
            $this->throwExceptionIfNotExistsInputDocument($inputDocumentUid, "", $this->arrayFieldNameForException["inputDocumentUid"]);

            //Load InputDocument
            $inputDocument = new \InputDocument();

            $arrayInputDocumentData = $inputDocument->load($inputDocumentUid);

            $processUid = $arrayInputDocumentData["PRO_UID"];

            //Verify data
            $process = new \ProcessMaker\BusinessModel\Process();

            $process->throwExceptionIfDataNotMetFieldDefinition($arrayData, $this->arrayFieldDefinition, $this->arrayFieldNameForException, false);

            if (isset($arrayData["INP_DOC_TITLE"])) {
                $this->throwExceptionIfExistsTitle($processUid, $arrayData["INP_DOC_TITLE"], $this->arrayFieldNameForException["inputDocumentTitle"], $inputDocumentUid);
            }

            //Update
            $arrayData["INP_DOC_UID"] = $inputDocumentUid;

            $result = $inputDocument->update($arrayData);
            
            \G::LoadClass('pmDynaform');
            $pmDynaform = new \pmDynaform();
            $pmDynaform->synchronizeInputDocument($processUid, $arrayData);

            //Return
            unset($arrayData["INP_DOC_UID"]);

            if (!$this->formatFieldNameInUppercase) {
                $arrayData = array_change_key_case($arrayData, CASE_LOWER);
            }

            return $arrayData;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Delete InputDocument
     *
     * @param string $inputDocumentUid Unique id of InputDocument
     *
     * return void
     */
    public function delete($inputDocumentUid)
    {
        try {
            //Verify data
            $this->throwExceptionIfNotExistsInputDocument($inputDocumentUid, "", $this->arrayFieldNameForException["inputDocumentUid"]);

            $this->throwExceptionIfItsAssignedInOtherObjects($inputDocumentUid, $this->arrayFieldNameForException["inputDocumentUid"]);

            //Delete
            //StepSupervisor
            $stepSupervisor = new \StepSupervisor();

            $arrayData = $stepSupervisor->loadInfo($inputDocumentUid);
            $result = $stepSupervisor->remove($arrayData["STEP_UID"]);

            //ObjectPermission
            $objectPermission = new \ObjectPermission();

            $arrayData = $objectPermission->loadInfo($inputDocumentUid);

            if (is_array($arrayData)) {
                $result = $objectPermission->remove($arrayData["OP_UID"]);
            }

            //InputDocument
            $inputDocument = new \InputDocument();

            $result = $inputDocument->remove($inputDocumentUid);

            //Step
            $step = new \Step();

            $step->removeStep("INPUT_DOCUMENT", $inputDocumentUid);

            //ObjectPermission
            $objectPermission = new \ObjectPermission();

            $objectPermission->removeByObject("INPUT", $inputDocumentUid);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get criteria for InputDocument
     *
     * return object
     */
    public function getInputDocumentCriteria()
    {
        try {
            $delimiter = \DBAdapter::getStringDelimiter();

            $criteria = new \Criteria("workflow");

            $criteria->addSelectColumn(\InputDocumentPeer::INP_DOC_UID);
            $criteria->addAsColumn("INP_DOC_TITLE", "CT.CON_VALUE");
            $criteria->addAsColumn("INP_DOC_DESCRIPTION", "CD.CON_VALUE");
            $criteria->addSelectColumn(\InputDocumentPeer::INP_DOC_FORM_NEEDED);
            $criteria->addSelectColumn(\InputDocumentPeer::INP_DOC_ORIGINAL);
            $criteria->addSelectColumn(\InputDocumentPeer::INP_DOC_PUBLISHED);
            $criteria->addSelectColumn(\InputDocumentPeer::INP_DOC_VERSIONING);
            $criteria->addSelectColumn(\InputDocumentPeer::INP_DOC_DESTINATION_PATH);
            $criteria->addSelectColumn(\InputDocumentPeer::INP_DOC_TAGS);
            $criteria->addSelectColumn(\InputDocumentPeer::INP_DOC_TYPE_FILE);
            $criteria->addSelectColumn(\InputDocumentPeer::INP_DOC_MAX_FILESIZE);
            $criteria->addSelectColumn(\InputDocumentPeer::INP_DOC_MAX_FILESIZE_UNIT);

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

            return $criteria;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get data of an InputDocument from a record
     *
     * @param array $record Record
     *
     * return array Return an array with data InputDocument
     */
    public function getInputDocumentDataFromRecord($record)
    {
        try {
            if ($record["INP_DOC_TITLE"] . "" == "") {
                //Load InputDocument
                $inputDocument = new \InputDocument();

                $arrayInputDocumentData = $inputDocument->load($record["INP_DOC_UID"]);

                //There is no transaltion for this Document name, try to get/regenerate the label
                $record["INP_DOC_TITLE"] = $arrayInputDocumentData["INP_DOC_TITLE"];
                $record["INP_DOC_DESCRIPTION"] = $arrayInputDocumentData["INP_DOC_DESCRIPTION"];
            }

            return array(
                $this->getFieldNameByFormatFieldName("INP_DOC_UID")              => $record["INP_DOC_UID"],
                $this->getFieldNameByFormatFieldName("INP_DOC_TITLE")            => $record["INP_DOC_TITLE"],
                $this->getFieldNameByFormatFieldName("INP_DOC_DESCRIPTION")      => $record["INP_DOC_DESCRIPTION"] . "",
                $this->getFieldNameByFormatFieldName("INP_DOC_FORM_NEEDED")      => $record["INP_DOC_FORM_NEEDED"] . "",
                $this->getFieldNameByFormatFieldName("INP_DOC_ORIGINAL")         => $record["INP_DOC_ORIGINAL"] . "",
                $this->getFieldNameByFormatFieldName("INP_DOC_PUBLISHED")        => $record["INP_DOC_PUBLISHED"] . "",
                $this->getFieldNameByFormatFieldName("INP_DOC_VERSIONING")       => (int)($record["INP_DOC_VERSIONING"]),
                $this->getFieldNameByFormatFieldName("INP_DOC_DESTINATION_PATH") => $record["INP_DOC_DESTINATION_PATH"] . "",
                $this->getFieldNameByFormatFieldName("INP_DOC_TAGS")             => $record["INP_DOC_TAGS"] . "",
                $this->getFieldNameByFormatFieldName("INP_DOC_TYPE_FILE")             => $record["INP_DOC_TYPE_FILE"] . "",
                $this->getFieldNameByFormatFieldName("INP_DOC_MAX_FILESIZE")             => (int)($record["INP_DOC_MAX_FILESIZE"]),
                $this->getFieldNameByFormatFieldName("INP_DOC_MAX_FILESIZE_UNIT")             => $record["INP_DOC_MAX_FILESIZE_UNIT"] . ""
            );
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get data of an InputDocument
     *
     * @param string $inputDocumentUid Unique id of InputDocument
     *
     * return array Return an array with data of an InputDocument
     */
    public function getInputDocument($inputDocumentUid)
    {
        try {
            //Verify data
            $this->throwExceptionIfNotExistsInputDocument($inputDocumentUid, "", $this->arrayFieldNameForException["inputDocumentUid"]);

            //Get data
            $criteria = $this->getInputDocumentCriteria();

            $criteria->add(\InputDocumentPeer::INP_DOC_UID, $inputDocumentUid, \Criteria::EQUAL);

            $rsCriteria = \InputDocumentPeer::doSelectRS($criteria);
            $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

            $rsCriteria->next();

            $row = $rsCriteria->getRow();

            return $this->getInputDocumentDataFromRecord($row);
        } catch (\Exception $e) {
            throw $e;
        }
    }
}

