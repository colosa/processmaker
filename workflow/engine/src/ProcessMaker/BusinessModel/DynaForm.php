<?php
namespace ProcessMaker\BusinessModel;
use DynaformHandler;

class DynaForm
{
    private $arrayFieldDefinition = array(
        "DYN_UID"         => array("type" => "string", "required" => false, "empty" => false, "defaultValues" => array(),                  "fieldNameAux" => "dynaFormUid"),

        "DYN_TITLE"       => array("type" => "string", "required" => true,  "empty" => false, "defaultValues" => array(),                  "fieldNameAux" => "dynaFormTitle"),
        "DYN_DESCRIPTION" => array("type" => "string", "required" => false, "empty" => true,  "defaultValues" => array(),                  "fieldNameAux" => "dynaFormDescription"),
        "DYN_TYPE"        => array("type" => "string", "required" => true,  "empty" => false, "defaultValues" => array("xmlform", "grid"), "fieldNameAux" => "dynaFormType"),
        "DYN_CONTENT"     => array("type" => "string", "required" => false, "empty" => true,  "defaultValues" => array(),                  "fieldNameAux" => "dynaFormContent"),
        "DYN_VERSION"     => array("type" => "int",    "required" => false,  "empty" => true, "defaultValues" => array(1 ,2),              "fieldNameAux" => "dynaFormVersion")
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
     * Verify if exists the title of a DynaForm
     *
     * @param string $processUid         Unique id of Process
     * @param string $dynaFormTitle      Title
     * @param string $dynaFormUidExclude Unique id of DynaForm to exclude
     *
     * return bool Return true if exists the title of a DynaForm, false otherwise
     */
    public function existsTitle($processUid, $dynaFormTitle, $dynaFormUidExclude = "")
    {
        try {
            $criteria = new \Criteria("workflow");
            $criteria->addSelectColumn(\DynaformPeer::DYN_UID);
            $criteria->addSelectColumn(\DynaformPeer::DYN_TITLE);
            $criteria->add(\DynaformPeer::PRO_UID, $processUid, \Criteria::EQUAL);

            if ($dynaFormUidExclude != "") {
                $criteria->add(\DynaformPeer::DYN_UID, $dynaFormUidExclude, \Criteria::NOT_EQUAL);
            }

            $criteria->add(\DynaformPeer::DYN_TITLE, $dynaFormTitle, \Criteria::EQUAL);

            $rsCriteria = \DynaformPeer::doSelectRS($criteria);

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
     * Verify if a DynaForm is assigned some Steps
     *
     * @param string $dynaFormUid Unique id of DynaForm
     * @param string $processUid  Unique id of Process
     *
     * return bool Return true if a DynaForm is assigned some Steps, false otherwise
     */
    public function dynaFormDepends($dynUid, $proUid)
    {
        $oCriteria = new \Criteria();
        $oCriteria->addSelectColumn( \DynaformPeer::DYN_TYPE );
        $oCriteria->add( \DynaformPeer::DYN_UID, $dynUid );
        $oCriteria->add( \DynaformPeer::PRO_UID, $proUid );
        $oDataset = \DynaformPeer::doSelectRS( $oCriteria );
        $oDataset->setFetchmode( \ResultSet::FETCHMODE_ASSOC );
        $oDataset->next();
        $dataDyna = $oDataset->getRow();

        if ($dataDyna['DYN_TYPE'] == 'grid') {
            $formsDepend = array();


            $oCriteria = new \Criteria( 'workflow' );
            $oCriteria->addSelectColumn( \DynaformPeer::DYN_UID );
            $oCriteria->addSelectColumn( \DynaformPeer::DYN_TITLE );
            $oCriteria->add( \DynaformPeer::PRO_UID, $proUid );
            $oCriteria->add( \DynaformPeer::DYN_TYPE, "xmlform" );
            $oDataset = \DynaformPeer::doSelectRS( $oCriteria );
            $oDataset->setFetchmode( \ResultSet::FETCHMODE_ASSOC );

            while ($oDataset->next()) {
                $dataForms = $oDataset->getRow();
                $dynHandler = new DynaformHandler(PATH_DYNAFORM . $proUid . PATH_SEP . $dataForms["DYN_UID"] . ".xml");
                $dynFields = $dynHandler->getFields();
                foreach ($dynFields as $field) {
                    $sType = \Step::getAttribute( $field, 'type' );
                    if ($sType == 'grid') {
                        $sxmlgrid = \Step::getAttribute( $field, 'xmlgrid' );
                        $aGridInfo = explode( "/", $sxmlgrid );
                        if ($aGridInfo[0] == $proUid && $aGridInfo[1] == $dynUid) {
                            $formsDepend[] = $dataForms["DYN_TITLE"];
                        }
                    }
                }
            }
            if (!empty($formsDepend)) {
                $message = "You can not delete the grid '$dynUid', because it is in the ";
                $message .= (count($formsDepend) == 1) ? 'form' : 'forms';
                $message .= ': ' . implode(', ', $formsDepend);
                return $message;
            }
        } else {
            $flagDepend = false;
            $stepsDepends = \Step::verifyDynaformAssigStep($dynUid, $proUid);

            $messageSteps = '(0) Depends in steps';
            if (!empty($stepsDepends)) {
                $flagDepend = true;
                $countSteps = count($stepsDepends);
                $messTemp = '';
                foreach ($stepsDepends as $value) {
                    $messTemp .= ", the task '" . $value['CON_VALUE'] . "' position " . $value['STEP_POSITION'];
                }
                $messageSteps = "($countSteps) Depends in steps in" . $messTemp;
            }

            $stepSupervisorsDepends = \StepSupervisor::verifyDynaformAssigStepSupervisor($dynUid, $proUid);
            $messageStepsSupervisors = '(0) Depends in steps supervisor';
            if (!empty($stepSupervisorsDepends)) {
                $flagDepend = true;
                $countSteps = count($stepSupervisorsDepends);
                $messageStepsSupervisors = "($countSteps) Depends in steps supervisor";
            }

            $objectPermissionDepends = \ObjectPermission::verifyDynaformAssigObjectPermission($dynUid, $proUid);
            $messageObjectPermission = '(0) Depends in permissions';
            if (!empty($objectPermissionDepends)) {
                $flagDepend = true;
                $countSteps = count($objectPermissionDepends);
                $messageObjectPermission = "($countSteps) Depends in permissions";
            }

            $caseTrackerDepends = \CaseTrackerObject::verifyDynaformAssigCaseTracker($dynUid, $proUid);
            $messageCaseTracker = '(0) Depends in case traker';
            if (!empty($caseTrackerDepends)) {
                $flagDepend = true;
                $countSteps = count($caseTrackerDepends);
                $messageCaseTracker = "($countSteps) Depends in case traker";
            }

            $dynaformDepends = \Dynaform::verifyDynaformAssignDynaform($dynUid, $proUid);
            $messageDynaform = '(0) Depends in case traker';
            if (!empty($dynaformDepends)) {
                $flagDepend = true;
                $countSteps = count($dynaformDepends);
                $messageDynaform = "($countSteps) Depends in dynaform";
            }

            if ($flagDepend) {
                $message = "You can not delete the dynaform '$dynUid', because it has the following dependencies: \n\n";
                $message .= $messageSteps . ".\n" . $messageStepsSupervisors . ".\n";
                $message .= $messageObjectPermission . ".\n" . $messageCaseTracker . "\n";
                $message .= $messageDynaform;
                return $message;
            }
            return '';
        }
    }

    /**
     * Verify if a DynaForm has relation with a Step Supervisor
     *
     * @param string $dynaFormUid Unique id of DynaForm
     *
     * return bool Return true if a DynaForm has relation with a Step Supervisor, false otherwise
     */
    public function dynaFormRelationStepSupervisor($dynaFormUid)
    {
        try {
            $stepSupervisor = new \StepSupervisor();
            $arrayData = $stepSupervisor->loadInfo($dynaFormUid);
            if (is_array($arrayData)) {
                return true;
            } else {
                return false;
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Throw the exception "The DynaForm doesn't exist"
     *
     * @param string $dynaFormUid           Unique id of DynaForm
     * @param string $fieldNameForException Field name for the exception
     *
     * @return void
     */
    private function throwExceptionDynaFormDoesNotExist($dynaFormUid, $fieldNameForException)
    {
        throw new \Exception(\G::LoadTranslation(
            'ID_DYNAFORM_DOES_NOT_EXIST', [$fieldNameForException, $dynaFormUid]
        ));
    }

    /**
     * Verify if doesn't exists the DynaForm in table DYNAFORM
     *
     * @param string $dynaFormUid           Unique id of DynaForm
     * @param string $processUid            Unique id of Process
     * @param string $fieldNameForException Field name for the exception
     *
     * return void Throw exception if doesn't exists the DynaForm in table DYNAFORM
     */
    public function throwExceptionIfNotExistsDynaForm($dynaFormUid, $processUid, $fieldNameForException)
    {
        try {
            $criteria = new \Criteria("workflow");

            $criteria->addSelectColumn(\DynaformPeer::DYN_UID);

            if ($processUid != "") {
                $criteria->add(\DynaformPeer::PRO_UID, $processUid, \Criteria::EQUAL);
            }

            $criteria->add(\DynaformPeer::DYN_UID, $dynaFormUid, \Criteria::EQUAL);

            $rsCriteria = \DynaformPeer::doSelectRS($criteria);

            if (!$rsCriteria->next()) {
                $this->throwExceptionDynaFormDoesNotExist($dynaFormUid, $fieldNameForException);
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Verify if exists the title of a DynaForm
     *
     * @param string $processUid            Unique id of Process
     * @param string $dynaFormTitle         Title
     * @param string $fieldNameForException Field name for the exception
     * @param string $dynaFormUidExclude    Unique id of DynaForm to exclude
     *
     * return void Throw exception if exists the title of a DynaForm
     */
    public function throwExceptionIfExistsTitle($processUid, $dynaFormTitle, $fieldNameForException, $dynaFormUidExclude = "")
    {
        try {
            if ($this->existsTitle($processUid, $dynaFormTitle, $dynaFormUidExclude)) {
                throw new \Exception(\G::LoadTranslation("ID_DYNAFORM_TITLE_ALREADY_EXISTS", array($fieldNameForException, $dynaFormTitle)));
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Verify if is not grid DynaForm
     *
     * @param string $dynaFormUid           Unique id of DynaForm
     * @param string $fieldNameForException Field name for the exception
     *
     * return void Throw exception if is not grid DynaForm
     */
    public function throwExceptionIfNotIsGridDynaForm($dynaFormUid, $fieldNameForException)
    {
        try {
            //Load DynaForm
            $dynaForm = new \Dynaform();

            $arrayDynaFormData = $dynaForm->Load($dynaFormUid);

            if ($arrayDynaFormData["DYN_TYPE"] != "grid") {
                throw new \Exception(\G::LoadTranslation("ID_DYNAFORM_IS_NOT_GRID", array($fieldNameForException, $dynaFormUid)));
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get DynaForm record
     *
     * @param string $dynaFormUid                   Unique id of DynaForm
     * @param array  $arrayVariableNameForException Variable name for exception
     * @param bool   $throwException Flag to throw the exception if the main parameters are invalid or do not exist
     *                               (TRUE: throw the exception; FALSE: returns FALSE)
     *
     * @return array Returns an array with DynaForm record, ThrowTheException/FALSE otherwise
     */
    public function getDynaFormRecordByPk($dynaFormUid, array $arrayVariableNameForException, $throwException = true)
    {
        try {
            $obj = \DynaformPeer::retrieveByPK($dynaFormUid);

            if (is_null($obj)) {
                if ($throwException) {
                    $this->throwExceptionDynaFormDoesNotExist(
                        $dynaFormUid, $arrayVariableNameForException['$dynaFormUid']
                    );
                } else {
                    return false;
                }
            }

            //Return
            return $obj->toArray(\BasePeer::TYPE_FIELDNAME);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Create DynaForm for a Process
     *
     * @param string $processUid Unique id of Process
     * @param array  $arrayData  Data
     *
     * return array Return data of the new DynaForm created
     */
    public function create($processUid, $arrayData)
    {
        try {
            $arrayData = array_change_key_case($arrayData, CASE_UPPER);

            unset($arrayData["DYN_UID"]);
            unset($arrayData["COPY_IMPORT"]);
            unset($arrayData["PMTABLE"]);

            //Verify data
            $process = new \ProcessMaker\BusinessModel\Process();

            $process->throwExceptionIfNotExistsProcess($processUid, $this->arrayFieldNameForException["processUid"]);

            $process->throwExceptionIfDataNotMetFieldDefinition($arrayData, $this->arrayFieldDefinition, $this->arrayFieldNameForException, true);

            $this->throwExceptionIfExistsTitle($processUid, $arrayData["DYN_TITLE"], $this->arrayFieldNameForException["dynaFormTitle"]);

            //Create
            $dynaForm = new \Dynaform();

            $arrayData["PRO_UID"] = $processUid;

            $dynaFormUid = $dynaForm->create($arrayData);

            //Return
            unset($arrayData["PRO_UID"]);

            $arrayData = array_merge(array("DYN_UID" => $dynaFormUid), $arrayData);

            if (!$this->formatFieldNameInUppercase) {
                $arrayData = array_change_key_case($arrayData, CASE_LOWER);
            }

            return $arrayData;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Update DynaForm
     *
     * @param string $dynaFormUid Unique id of DynaForm
     * @param array  $arrayData   Data
     *
     * return array Return data of the DynaForm updated
     */
    public function update($dynaFormUid, $arrayData)
    {
        try {
            $arrayData = array_change_key_case($arrayData, CASE_UPPER);

            //Verify data
            $this->throwExceptionIfNotExistsDynaForm($dynaFormUid, "", $this->arrayFieldNameForException["dynaFormUid"]);

            //Load DynaForm
            $dynaForm = new \Dynaform();

            $arrayDynaFormData = $dynaForm->Load($dynaFormUid);

            $processUid = $arrayDynaFormData["PRO_UID"];

            //Verify data
            $process = new \ProcessMaker\BusinessModel\Process();

            $process->throwExceptionIfDataNotMetFieldDefinition($arrayData, $this->arrayFieldDefinition, $this->arrayFieldNameForException, false);

            if (isset($arrayData["DYN_TITLE"])) {
                $this->throwExceptionIfExistsTitle($processUid, $arrayData["DYN_TITLE"], $this->arrayFieldNameForException["dynaFormTitle"], $dynaFormUid);
            }

            //Update
            $arrayData["DYN_UID"] = $dynaFormUid;

            $result = $dynaForm->update($arrayData);

            //Return
            unset($arrayData["DYN_UID"]);

            if (!$this->formatFieldNameInUppercase) {
                $arrayData = array_change_key_case($arrayData, CASE_LOWER);
            }

            return $arrayData;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Delete DynaForm
     *
     * @param string $dynaFormUid Unique id of DynaForm
     *
     * return void
     */
    public function delete($dynaFormUid)
    {
        try {
            //Verify data
            $this->throwExceptionIfNotExistsDynaForm($dynaFormUid, "", $this->arrayFieldNameForException["dynaFormUid"]);

            //Load DynaForm
            $dynaForm = new \Dynaform();

            $arrayDynaFormData = $dynaForm->Load($dynaFormUid);

            $processUid = $arrayDynaFormData["PRO_UID"];

            //Verify dependences dynaforms
            $resultDepends = $this->dynaFormDepends($dynaFormUid, $processUid);

            if ($resultDepends != "") {
                throw new \Exception($resultDepends);
            }

            //Delete
            //In table DYNAFORM
            $result = $dynaForm->remove($dynaFormUid);

            //In table STEP
            $step = new \Step();
            $step->removeStep("DYNAFORM", $dynaFormUid);

            //In table OBJECT_PERMISSION
            $objPermission = new \ObjectPermission();
            $objPermission->removeByObject("DYNAFORM", $dynaFormUid);

            //In table STEP_SUPERVISOR
            $stepSupervisor = new \StepSupervisor();
            $stepSupervisor->removeByObject("DYNAFORM", $dynaFormUid);

            //In table CASE_TRACKER_OBJECT
            $caseTrackerObject = new \CaseTrackerObject();
            $caseTrackerObject->removeByObject("DYNAFORM", $dynaFormUid);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Copy/Import a DynaForm
     *
     * @param string $processUid Unique id of Process
     * @param array  $arrayData  Data
     *
     * return array Return data of the new DynaForm created
     */
    public function copyImport($processUid, $arrayData)
    {
        try {
            $arrayData = \G::array_change_key_case2($arrayData, CASE_UPPER);

            unset($arrayData["DYN_UID"]);
            unset($arrayData["PMTABLE"]);

            //Verify data
            $process = new \ProcessMaker\BusinessModel\Process();

            $process->throwExceptionIfNotExistsProcess($processUid, $this->arrayFieldNameForException["processUid"]);

            $process->throwExceptionIfDataNotMetFieldDefinition($arrayData, $this->arrayFieldDefinition, $this->arrayFieldNameForException, true);

            if (!isset($arrayData["COPY_IMPORT"])) {
                throw new \Exception(\G::LoadTranslation("ID_UNDEFINED_VALUE_IS_REQUIRED", array($this->getFieldNameByFormatFieldName("COPY_IMPORT"))));
            }

            if (!isset($arrayData["COPY_IMPORT"]["PRJ_UID"])) {
                throw new \Exception(\G::LoadTranslation("ID_UNDEFINED_VALUE_IS_REQUIRED", array($this->getFieldNameByFormatFieldName("COPY_IMPORT.PRJ_UID"))));
            }

            $arrayData["COPY_IMPORT"]["PRJ_UID"] = trim($arrayData["COPY_IMPORT"]["PRJ_UID"]);

            if ($arrayData["COPY_IMPORT"]["PRJ_UID"] == "") {
                throw new \Exception(\G::LoadTranslation("ID_INVALID_VALUE_CAN_NOT_BE_EMPTY", array($this->getFieldNameByFormatFieldName("COPY_IMPORT.PRJ_UID"))));
            }

            if (!isset($arrayData["COPY_IMPORT"]["DYN_UID"])) {
                throw new \Exception(\G::LoadTranslation("ID_UNDEFINED_VALUE_IS_REQUIRED", array($this->getFieldNameByFormatFieldName("COPY_IMPORT.DYN_UID"))));
            }

            $arrayData["COPY_IMPORT"]["DYN_UID"] = trim($arrayData["COPY_IMPORT"]["DYN_UID"]);

            if ($arrayData["COPY_IMPORT"]["DYN_UID"] == "") {
                throw new \Exception(\G::LoadTranslation("ID_INVALID_VALUE_CAN_NOT_BE_EMPTY", array($this->getFieldNameByFormatFieldName("COPY_IMPORT.DYN_UID"))));
            }

            $this->throwExceptionIfExistsTitle($processUid, $arrayData["DYN_TITLE"], $this->arrayFieldNameForException["dynaFormTitle"]);

            //Copy/Import Uids
            $processUidCopyImport  = $arrayData["COPY_IMPORT"]["PRJ_UID"];
            $dynaFormUidCopyImport = $arrayData["COPY_IMPORT"]["DYN_UID"];

            //Verify data
            $process->throwExceptionIfNotExistsProcess($processUidCopyImport, $this->getFieldNameByFormatFieldName("COPY_IMPORT.PRJ_UID"));

            $this->throwExceptionIfNotExistsDynaForm($dynaFormUidCopyImport, $processUidCopyImport, $this->getFieldNameByFormatFieldName("COPY_IMPORT.DYN_UID"));

            //Copy/Import

            //Copy content if version is 2
            if ($arrayData["DYN_VERSION"] === 2) {
                $dynaFormOld = new \Dynaform();

                $arrayDynaFormData = $dynaFormOld->Load($dynaFormUidCopyImport);

                $arrayData["DYN_CONTENT"] = $arrayDynaFormData["DYN_CONTENT"];
            }

            //Create
            $arrayData = $this->create($processUid, $arrayData);

            $dynaFormUid = $arrayData[$this->getFieldNameByFormatFieldName("DYN_UID")];

            //Copy files of the DynaForm
            $umaskOld = umask(0);

            $fileXml = PATH_DYNAFORM . $processUidCopyImport . PATH_SEP . $dynaFormUidCopyImport . ".xml";

            if (file_exists($fileXml)) {
                $fileXmlCopy = PATH_DYNAFORM . $processUid . PATH_SEP . $dynaFormUid . ".xml";

                $fhXml = fopen($fileXml, "r");
                $fhXmlCopy = fopen($fileXmlCopy, "w");

                while (!feof($fhXml)) {
                    $strLine = fgets($fhXml, 4096);
                    $strLine = str_replace($processUidCopyImport . "/" . $dynaFormUidCopyImport, $processUid . "/" . $dynaFormUid, $strLine);

                    //DynaForm Grid
                    preg_match_all("/<.*type\s*=\s*[\"\']grid[\"\'].*xmlgrid\s*=\s*[\"\']\w{32}\/(\w{32})[\"\'].*\/>/", $strLine, $arrayMatch, PREG_SET_ORDER);

                    foreach ($arrayMatch as $value) {
                        $dynaFormGridUidCopyImport = $value[1];

                        //Get data
                        $dynaForm = new \Dynaform();
                        $row = $dynaForm->Load($dynaFormGridUidCopyImport);
                        $dynGrdTitleCopyImport = $row["DYN_TITLE"];
                        $dynGrdDescriptionCopyImport = $row["DYN_DESCRIPTION"];

                        //Create Grid
                        $dynaFormGrid = new \Dynaform();

                        $arrayDataAux = array(
                            "PRO_UID"   => $processUid,
                            "DYN_TITLE" => $dynGrdTitleCopyImport . (($this->existsTitle($processUid, $dynGrdTitleCopyImport))? " (" . $arrayData["DYN_TITLE"] . ")" : ""),
                            "DYN_DESCRIPTION" => $dynGrdDescriptionCopyImport,
                            "DYN_TYPE" => "grid"
                        );

                        $dynaFormGridUid = $dynaFormGrid->create($arrayDataAux);

                        //Copy files of the DynaForm Grid
                        $fileGridXml = PATH_DYNAFORM . $processUidCopyImport . PATH_SEP . $dynaFormGridUidCopyImport . ".xml";

                        if (file_exists($fileGridXml)) {
                            $fileGridXmlCopy = PATH_DYNAFORM . $processUid . PATH_SEP . $dynaFormGridUid . ".xml";

                            $fhGridXml = fopen($fileGridXml, "r");
                            $fhGridXmlCopy = fopen($fileGridXmlCopy, "w");

                            while (!feof($fhGridXml)) {
                                $strLineAux = fgets($fhGridXml, 4096);
                                $strLineAux = str_replace($processUidCopyImport . "/" . $dynaFormGridUidCopyImport, $processUid . "/" . $dynaFormGridUid, $strLineAux);

                                fwrite($fhGridXmlCopy, $strLineAux);
                            }

                            fclose($fhGridXmlCopy);
                            fclose($fhGridXml);

                            chmod($fileGridXmlCopy, 0777);
                        }

                        $fileGridHtml = PATH_DYNAFORM . $processUidCopyImport . PATH_SEP . $dynaFormGridUidCopyImport . ".html";

                        if (file_exists($fileGridHtml)) {
                            $fileGridHtmlCopy = PATH_DYNAFORM . $processUid . PATH_SEP . $dynaFormGridUid . ".html";

                            copy($fileGridHtml, $fileGridHtmlCopy);

                            chmod($fileGridHtmlCopy, 0777);
                        }

                        $strLine = str_replace($processUidCopyImport . "/" . $dynaFormGridUidCopyImport, $processUid . "/" . $dynaFormGridUid, $strLine);
                    }

                    fwrite($fhXmlCopy, $strLine);
                }

                fclose($fhXmlCopy);
                fclose($fhXml);

                chmod($fileXmlCopy, 0777);
            }

            $fileHtml = PATH_DYNAFORM . $processUidCopyImport . PATH_SEP . $dynaFormUidCopyImport . ".html";

            if (file_exists($fileHtml)) {
                $fileHtmlCopy = PATH_DYNAFORM . $processUid . PATH_SEP . $dynaFormUid . ".html";

                copy($fileHtml, $fileHtmlCopy);

                chmod($fileHtmlCopy, 0777);
            }

            //Copy if there are conditions attached to the DynaForm
            $fieldCondition = new \FieldCondition();

            $arrayCondition = $fieldCondition->getAllByDynUid($dynaFormUidCopyImport);

            foreach ($arrayCondition as $condition) {
                $condition["FCD_UID"] = "";
                $condition["FCD_DYN_UID"] = $dynaFormUid;

                $fieldCondition->quickSave($condition);
            }

            umask($umaskOld);

            //Return
            return $arrayData;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Create a Dynaform based on a PMTable
     *
     * @param string $processUid Unique id of Process
     * @param array  $arrayData  Data
     *
     * return array Return data of the new DynaForm created
     */
    public function createBasedPmTable($processUid, $arrayData)
    {
        try {
            $arrayData = \G::array_change_key_case2($arrayData, CASE_UPPER);

            unset($arrayData["DYN_UID"]);
            unset($arrayData["COPY_IMPORT"]);

            //Verify data
            $process = new \ProcessMaker\BusinessModel\Process();

            $process->throwExceptionIfNotExistsProcess($processUid, $this->arrayFieldNameForException["processUid"]);

            $process->throwExceptionIfDataNotMetFieldDefinition($arrayData, $this->arrayFieldDefinition, $this->arrayFieldNameForException, true);

            if ($arrayData["DYN_TYPE"] == "grid") {
                throw new \Exception(\G::LoadTranslation("ID_INVALID_VALUE_ONLY_ACCEPTS_VALUES", array($this->arrayFieldNameForException["dynaFormType"], "xmlform")));
            }

            if (!isset($arrayData["PMTABLE"])) {
                throw new \Exception(\G::LoadTranslation("ID_UNDEFINED_VALUE_IS_REQUIRED", array($this->getFieldNameByFormatFieldName("PMTABLE"))));
            }

            if (!isset($arrayData["PMTABLE"]["TAB_UID"])) {
                throw new \Exception(\G::LoadTranslation("ID_UNDEFINED_VALUE_IS_REQUIRED", array($this->getFieldNameByFormatFieldName("PMTABLE.TAB_UID"))));
            }

            $arrayData["PMTABLE"]["TAB_UID"] = trim($arrayData["PMTABLE"]["TAB_UID"]);

            if ($arrayData["PMTABLE"]["TAB_UID"] == "") {
                throw new \Exception(\G::LoadTranslation("ID_INVALID_VALUE_CAN_NOT_BE_EMPTY", array($this->getFieldNameByFormatFieldName("PMTABLE.TAB_UID"))));
            }

            if (!isset($arrayData["PMTABLE"]["FIELDS"])) {
                throw new \Exception(\G::LoadTranslation("ID_UNDEFINED_VALUE_IS_REQUIRED", array($this->getFieldNameByFormatFieldName("PMTABLE.FIELDS"))));
            }

            if (count($arrayData["PMTABLE"]["FIELDS"]) == 0) {
                throw new \Exception(\G::LoadTranslation("ID_INVALID_VALUE_CAN_NOT_BE_EMPTY", array($this->getFieldNameByFormatFieldName("PMTABLE.FIELDS"))));
            }

            $this->throwExceptionIfExistsTitle($processUid, $arrayData["DYN_TITLE"], $this->arrayFieldNameForException["dynaFormTitle"]);

            $process->throwExceptionIfNotExistsPmTable($arrayData["PMTABLE"]["TAB_UID"], $this->getFieldNameByFormatFieldName("PMTABLE.TAB_UID"));

            //Validate PMTABLE.FIELDS
            //Valid Keys
            $flagValidFieldKey = 1;

            foreach ($arrayData["PMTABLE"]["FIELDS"] as $key => $value) {
                if (!isset($value["FLD_NAME"]) || !isset($value["PRO_VARIABLE"])) {
                    $flagValidFieldKey = 0;
                    break;
                }
            }

            if ($flagValidFieldKey == 0) {
                throw new \Exception(\G::LoadTranslation("ID_ATTRIBUTE_HAS_INVALID_ELEMENT_KEY", array($this->getFieldNameByFormatFieldName("PMTABLE.FIELDS"))));
            }

            //Is Primary Key
            $arrayFieldPk = $process->getPmTablePrimaryKeyFields($arrayData["PMTABLE"]["TAB_UID"], $this->getFieldNameByFormatFieldName("PMTABLE.TAB_UID"));
            $flagValidFieldPk = 1;
            $invalidFieldPk = "";

            $arrayFieldPkAux = array();

            foreach ($arrayData["PMTABLE"]["FIELDS"] as $key => $value) {
                $arrayFieldPkAux[] = $value["FLD_NAME"];

                if (!in_array($value["FLD_NAME"], $arrayFieldPk)) {
                    $flagValidFieldPk = 0;
                    $invalidFieldPk = $value["FLD_NAME"];
                    break;
                }
            }

            if ($flagValidFieldPk == 0) {
                throw new \Exception(\G::LoadTranslation("ID_PMTABLE_FIELD_IS_NOT_PRIMARY_KEY", array($this->getFieldNameByFormatFieldName("PMTABLE.FIELDS.FLD_NAME"), $invalidFieldPk)));
            }

            //All Primary Keys
            $flagAllFieldPk = 1;
            $missingFieldPk = "";

            foreach ($arrayFieldPk as $key => $value) {
                if (!in_array($value, $arrayFieldPkAux)) {
                    $flagAllFieldPk = 0;
                    $missingFieldPk = $value;
                    break;
                }
            }

            if ($flagAllFieldPk == 0) {
                throw new \Exception(\G::LoadTranslation("ID_PMTABLE_PRIMARY_KEY_FIELD_IS_MISSING_IN_ATTRIBUTE", array($missingFieldPk, $this->getFieldNameByFormatFieldName("PMTABLE.FIELDS"))));
            }

            //Total of Primary Keys
            $n1 = count($arrayFieldPk);
            $n2 = count($arrayFieldPkAux);

            if ($n1 != $n2) {
                throw new \Exception(\G::LoadTranslation("ID_PMTABLE_TOTAL_PRIMARY_KEY_FIELDS_IS_NOT_EQUAL_IN_ATTRIBUTE", array($n1, $this->getFieldNameByFormatFieldName("PMTABLE.FIELDS"), $n2)));
            }

            //Set data
            $tableUid    = $arrayData["PMTABLE"]["TAB_UID"];
            $arrayFields = $arrayData["PMTABLE"]["FIELDS"];

            unset($arrayData["PMTABLE"]);

            //Create
            $dynaForm = new \Dynaform();

            $arrayData["PRO_UID"] = $processUid;
            $arrayData["DYN_TYPE"] = "xmlform";
            $arrayData["FIELDS"] = $arrayFields;

            $dynaForm->createFromPMTable($arrayData, $tableUid);

            $dynaFormUid = $dynaForm->getDynUid();

            //Return
            unset($arrayData["PRO_UID"]);
            unset($arrayData["FIELDS"]);

            $arrayData = array_merge(array("DYN_UID" => $dynaFormUid), $arrayData);

            if (!$this->formatFieldNameInUppercase) {
                $arrayData = array_change_key_case($arrayData, CASE_LOWER);
            }

            return $arrayData;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Defines the method for create a DynaForm
     *
     * @param string $processUid Unique id of Process
     * @param array  $arrayData  Data
     *
     * return array Return data of the new DynaForm created
     */
    public function executeCreate($processUid, $arrayData)
    {
        try {
            $arrayData = array_change_key_case($arrayData, CASE_UPPER);

            $option = "NORMAL";

            //Validate data
            $count = 0;
            $msgMethod = "";

            if (isset($arrayData["COPY_IMPORT"])) {
                $count = $count + 1;
                $msgMethod = $msgMethod . (($msgMethod != "")? ", " : "") . "COPY_IMPORT";

                $option = "COPY_IMPORT";
            }

            if (isset($arrayData["PMTABLE"])) {
                $count = $count + 1;
                $msgMethod = $msgMethod . (($msgMethod != "")? ", " : "") . "PMTABLE";

                $option = "PMTABLE";
            }

            if ($count <= 1) {
                $arrayDataAux = array();

                switch ($option) {
                    case "COPY_IMPORT":
                        $arrayDataAux = $this->copyImport($processUid, $arrayData);
                        break;
                    case "PMTABLE":
                        $arrayDataAux = $this->createBasedPmTable($processUid, $arrayData);
                        break;
                    default:
                        //NORMAL
                        $arrayDataAux = $this->create($processUid, $arrayData);
                        break;
                }

                //Return
                return $arrayDataAux;
            } else {
                throw new \Exception(\G::LoadTranslation("ID_DYNAFORM_IT_IS_TRYING_CREATE_BY_SEVERAL_METHODS", array($this->getFieldNameByFormatFieldName($msgMethod))));
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get criteria for DynaForm
     *
     * return object
     */
    public function getDynaFormCriteria()
    {
        try {
            $criteria = new \Criteria("workflow");
            $criteria->addSelectColumn(\DynaformPeer::DYN_UID);
            $criteria->addSelectColumn(\DynaformPeer::DYN_TITLE);
            $criteria->addSelectColumn(\DynaformPeer::DYN_DESCRIPTION);
            $criteria->addSelectColumn(\DynaformPeer::DYN_TYPE);
            $criteria->addSelectColumn(\DynaformPeer::DYN_CONTENT);
            $criteria->addSelectColumn(\DynaformPeer::DYN_VERSION);
            $criteria->addSelectColumn(\DynaformPeer::DYN_UPDATE_DATE);
            return $criteria;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get data of a DynaForm from a record
     *
     * @param array $record Record
     *
     * return array Return an array with data DynaForm
     */
    public function getDynaFormDataFromRecord($record)
    {
        try {
            if ($record["DYN_VERSION"] == 0) {
                $record["DYN_VERSION"] = 1;
            }

            $record["DYN_CONTENT"] = preg_replace("/\\\\u([a-f0-9]{4})/e", "iconv('UCS-4LE','UTF-8',pack('V', hexdec('U$1')))", $record["DYN_CONTENT"]);

            return array(
                $this->getFieldNameByFormatFieldName("DYN_UID")         => $record["DYN_UID"],
                $this->getFieldNameByFormatFieldName("DYN_TITLE")       => $record["DYN_TITLE"],
                $this->getFieldNameByFormatFieldName("DYN_DESCRIPTION") => $record["DYN_DESCRIPTION"] . "",
                $this->getFieldNameByFormatFieldName("DYN_TYPE")        => $record["DYN_TYPE"] . "",
                $this->getFieldNameByFormatFieldName("DYN_CONTENT")     => $record["DYN_CONTENT"] . "",
                $this->getFieldNameByFormatFieldName("DYN_VERSION")     => (int)($record["DYN_VERSION"]),
                $this->getFieldNameByFormatFieldName("DYN_UPDATE_DATE")     => $record["DYN_UPDATE_DATE"]
            );
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get data of a DynaForm
     *
     * @param string $dynaFormUid Unique id of DynaForm
     *
     * return array Return an array with data of a DynaForm
     */
    public function getDynaForm($dynaFormUid)
    {
        try {
            //Verify data
            $this->throwExceptionIfNotExistsDynaForm($dynaFormUid, "", $this->arrayFieldNameForException["dynaFormUid"]);

            //Get data
            $criteria = $this->getDynaFormCriteria();

            $criteria->add(\DynaformPeer::DYN_UID, $dynaFormUid, \Criteria::EQUAL);

            $rsCriteria = \DynaformPeer::doSelectRS($criteria);
            $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

            $rsCriteria->next();

            $row = $rsCriteria->getRow();

            //Return
            return $this->getDynaFormDataFromRecord($row);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get data of a DynaForm
     *
     * @param string $projectUid Unique id of Project
     * @param string $dynaFormUid Unique id of DynaForm
     *
     * return array Return an array with data of a DynaForm
     */
    public function getDynaFormFields($projectUid, $dynaFormUid)
    {
        try {
            $arrayVariables = array();
            $arrayVariablesDef = array();
            //Verify data
            Validator::proUid($projectUid, '$prj_uid');
            $this->throwExceptionIfNotExistsDynaForm($dynaFormUid, "", $this->arrayFieldNameForException["dynaFormUid"]);

            $criteria = new \Criteria("workflow");
            $criteria->addSelectColumn(\DynaformPeer::DYN_CONTENT);
            $criteria->add(\DynaformPeer::DYN_UID, $dynaFormUid, \Criteria::EQUAL);
            $rsCriteria = \DynaformPeer::doSelectRS($criteria);
            $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);
            $rsCriteria->next();

            $aRow = $rsCriteria->getRow();
            $contentDecode = json_decode($aRow['DYN_CONTENT'],true);

            $content = $contentDecode['items'][0]['items'];

            foreach ($content as $key => $value) {

                $valueType = (isset($value[0]["valueType"])) ? $value[0]["valueType"]:null;
                $maxLength = (isset($value[0]["maxLength"])) ? $value[0]["maxLength"]:null;
                $label = (isset($value[0]["label"])) ? $value[0]["label"]:null;
                $defaultValue = (isset($value[0]["defaultValue"])) ? $value[0]["defaultValue"]:null;
                $required = (isset($value[0]["required"])) ? $value[0]["required"]:null;
                $dbConnection = (isset($value[0]["dbConnection"])) ? $value[0]["dbConnection"]:null;
                $sql = (isset($value[0]["sql"])) ? $value[0]["sql"]:null;
                $options = (isset($value[0]["options"])) ? $value[0]["options"]:null;

                if (isset($value[0]["variable"])) {
                    $variable = $value[0]["variable"];

                    $criteria = new \Criteria("workflow");
                    $criteria->addSelectColumn(\ProcessVariablesPeer::VAR_NAME);
                    $criteria->addSelectColumn(\ProcessVariablesPeer::VAR_FIELD_TYPE);
                    $criteria->addSelectColumn(\ProcessVariablesPeer::VAR_FIELD_SIZE);
                    $criteria->addSelectColumn(\ProcessVariablesPeer::VAR_LABEL);
                    $criteria->addSelectColumn(\ProcessVariablesPeer::VAR_DBCONNECTION);
                    $criteria->addSelectColumn(\ProcessVariablesPeer::VAR_SQL);
                    $criteria->addSelectColumn(\ProcessVariablesPeer::VAR_NULL);
                    $criteria->addSelectColumn(\ProcessVariablesPeer::VAR_DEFAULT);
                    $criteria->addSelectColumn(\ProcessVariablesPeer::VAR_ACCEPTED_VALUES);
                    $criteria->add(\ProcessVariablesPeer::PRJ_UID, $projectUid, \Criteria::EQUAL);
                    $criteria->add(\ProcessVariablesPeer::VAR_NAME, $variable, \Criteria::EQUAL);
                    $rsCriteria = \ProcessVariablesPeer::doSelectRS($criteria);
                    $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);
                    $rsCriteria->next();

                    while ($aRow = $rsCriteria->getRow()) {

                        $valueTypeMerged = ($valueType == null && $valueType == '') ? $aRow['VAR_FIELD_TYPE'] : $valueType;
                        $maxLengthMerged = ($maxLength == null && $maxLength == '') ? (int)$aRow['VAR_FIELD_SIZE'] : $maxLength;
                        $labelMerged = ($label == null && $label == '') ? $aRow['VAR_LABEL'] : $label;
                        $defaultValueMerged = ($defaultValue == null && $defaultValue == '') ? $aRow['VAR_DEFAULT'] : $defaultValue;
                        $requiredMerged =  ($required == null && $required == '') ? ($aRow['VAR_NULL']==1) ? false: true : $required;
                        $dbConnectionMerged = ($dbConnection == null && $dbConnection == '') ? $aRow['VAR_DBCONNECTION'] : $dbConnection;
                        $sqlMerged = ($sql == null && $sql == '') ? $aRow['VAR_SQL'] : $sql;
                        $optionsMerged = ($options == null && $options == '') ? $aRow['VAR_ACCEPTED_VALUES'] : $options;

                        $aVariables = array('valueType' => $valueTypeMerged,
                                            'maxLength' => $maxLengthMerged,
                                            'label' => $labelMerged,
                                            'defaultValue' => $defaultValueMerged,
                                            'required' => $requiredMerged,
                                            'dbConnection' => $dbConnectionMerged,
                                            'sql' => $sqlMerged,
                                            'options' => $optionsMerged);

                        //fields properties
                        if (isset($value[0]["pickType"])) {
                            $aVariables = array_merge(array('pickType' => $value[0]["pickType"]), $aVariables);
                        }
                        if (isset($value[0]["placeHolder"])) {
                            $aVariables = array_merge(array('placeHolder' => $value[0]["placeHolder"]), $aVariables);
                        }
                        if (isset($value[0]["dependentsField"])) {
                            $aVariables = array_merge(array('dependentsField' => $value[0]["dependentsField"]), $aVariables);
                        }
                        if (isset($value[0]["hint"])) {
                            $aVariables = array_merge(array('hint' => $value[0]["hint"]), $aVariables);
                        }
                        if (isset($value[0]["readonly"])) {
                            $aVariables = array_merge(array('readonly' => $value[0]["readonly"]), $aVariables);
                        }
                        if (isset($value[0]["colSpan"])) {
                            $aVariables = array_merge(array('colSpan' => $value[0]["colSpan"]), $aVariables);
                        }
                        if (isset($value[0]["type"])) {
                            $aVariables = array_merge(array('type' => $value[0]["type"]), $aVariables);
                        }
                        if (isset($value[0]["name"])) {
                            $aVariables = array_merge(array('name' => $value[0]["name"]), $aVariables);
                        }
                        $aVariables = array_merge(array('variable' => $variable), $aVariables);

                        $arrayVariables[] = $aVariables;
                        $rsCriteria->next();
                    }

                } else {
                    $arrayVariablesDef[] = $value[0];
                }
            }
            $arrayVariables = array_merge($arrayVariables, $arrayVariablesDef);
            //Return
            return $arrayVariables;

        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get data of a DynaForm History
     *
     * @param string $dynaFormUid Unique id of DynaForm
     *
     * return array Return an array with data of a DynaForm History
     */
    public function getDynaFormHistory($prj_uid, $dynaFormUid, array $arrayData = array())
    {
        try {
            $filter = "";
            if (isset($arrayData["filter"])) {
                $filter = $arrayData["filter"];
            }
            $start = 0;
            if (isset($arrayData["start"])) {
                $start = $arrayData["start"];
            }
            $limit = 50;
            if (isset($arrayData["limit"])) {
                $limit = $arrayData["limit"];
            }
            $this->throwExceptionIfNotExistsDynaForm($dynaFormUid, "", $this->arrayFieldNameForException["dynaFormUid"]);

            $criteria = new \Criteria("workflow");

            $subcriteria = $criteria->getNewCriterion(\AppHistoryPeer::HISTORY_DATE, "%" . $filter . "%", \Criteria::LIKE);

            $criteria->addSelectColumn(\AppHistoryPeer::DYN_UID);
            $criteria->addSelectColumn(\AppHistoryPeer::HISTORY_DATA);
            $criteria->addSelectColumn(\AppHistoryPeer::HISTORY_DATE);
            $criteria->addAnd(\AppHistoryPeer::DYN_UID, $dynaFormUid, \Criteria::EQUAL);
            $criteria->addAnd(\AppHistoryPeer::OBJ_TYPE, "DYNAFORM", \Criteria::EQUAL);
            $criteria->addAnd(\AppHistoryPeer::HISTORY_DATA, "%DYN_CONTENT_HISTORY%", \Criteria::LIKE);
            $criteria->addAnd($subcriteria);

            $criteria->addDescendingOrderByColumn(\AppHistoryPeer::HISTORY_DATE);
            $criteria->setOffset($start);
            $criteria->setLimit($limit);

            $rsCriteria = \AppHistoryPeer::doSelectRS($criteria);
            $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);
            $data = array();
            while ($rsCriteria->next()) {
                $row = $rsCriteria->getRow();
                $d = @unserialize($row["HISTORY_DATA"]);
                $jsonData = "";
                if (isset($d["DYN_CONTENT_HISTORY"])) {
                    $decode = base64_decode($d["DYN_CONTENT_HISTORY"], true);
                    if ($decode !== false) {
                        $jsonData = $decode;
                    }
                }
                $data[] = array(
                    "history_date" => $row["HISTORY_DATE"],
                    "dyn_uid" => $row["DYN_UID"],
                    "dyn_content_history" => $jsonData
                );
            }
            return $data;
        } catch (\Exception $e) {
            throw $e;
        }
    }

}
