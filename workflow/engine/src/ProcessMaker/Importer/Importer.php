<?php
namespace ProcessMaker\Importer;

use ProcessMaker\Util;
use ProcessMaker\Project;
use ProcessMaker\Project\Adapter;

abstract class Importer
{
    protected $data = array();
    protected $importData = array();
    protected $filename = "";
    protected $saveDir = "";
    protected $metadata = array();

    const IMPORT_OPTION_OVERWRITE = "project.import.override";
    const IMPORT_OPTION_DISABLE_AND_CREATE_NEW = "project.import.disable_and_create_new";
    const IMPORT_OPTION_KEEP_WITHOUT_CHANGING_AND_CREATE_NEW = "project.import.keep_without_changing_and_create_new";
    const IMPORT_OPTION_CREATE_NEW = "project.import.create_new";

    const GROUP_IMPORT_OPTION_RENAME = "group.import.rename";
    const GROUP_IMPORT_OPTION_MERGE_PREEXISTENT = "group.import.merge.preexistent";
    const GROUP_IMPORT_OPTION_CREATE_NEW = "group.import.create_new";

    const IMPORT_STAT_SUCCESS = 100;               //Success, Project imported successfully.
    const IMPORT_STAT_TARGET_ALREADY_EXISTS = 101; //Error, Target Project already exists.
    const IMPORT_STAT_INVALID_SOURCE_FILE = 102;   //Error, Invalid file type or the file have corrupt data.
    const IMPORT_STAT_GROUP_ALREADY_EXISTS = 105;  //Error, Group already exists.

    public abstract function load($filename = null);

    /**
     * Verify if exists reserved words SQL
     *
     * @param object $data Data
     *
     * return void Throw exception if exists reserved words SQL
     */
    public function throwExceptionIfExistsReservedWordsSql($data)
    {
        $arrayReservedWordsSql = \G::reservedWordsSql();

        $arrayAux = array();

        foreach ($data->reportTables as $key => $value) {
            $record = $value;

            if (in_array(strtoupper($record["REP_TAB_NAME"]), $arrayReservedWordsSql)) {
                $arrayAux[] = $record["REP_TAB_NAME"];
            }
        }

        if (count($arrayAux) > 0) {
            throw new \Exception(\G::LoadTranslation("ID_PMTABLE_INVALID_NAME", array(implode(", ", $arrayAux))));
        }

        $arrayAux = array();

        foreach ($data->reportTablesVars as $key => $value) {
            $record = $value;

            if (in_array(strtoupper($record["REP_VAR_NAME"]), $arrayReservedWordsSql)) {
                $arrayAux[] = $record["REP_VAR_NAME"];
            }
        }

        if (count($arrayAux) > 0) {
            throw new \Exception(\G::LoadTranslation("ID_PMTABLE_INVALID_FIELD_NAME", array(implode(", ", $arrayAux))));
        }
    }

    public function import($option = self::IMPORT_OPTION_CREATE_NEW, $optionGroup = self::GROUP_IMPORT_OPTION_CREATE_NEW)
    {
        $this->prepare();

        //Verify data
        switch ($option) {
            case self::IMPORT_OPTION_CREATE_NEW:
                if ($this->targetExists()) {
                    throw new \Exception(
                        \G::LoadTranslation(
                            "ID_IMPORTER_PROJECT_ALREADY_EXISTS_SET_ACTION_TO_CONTINUE",
                            array(implode(
                                "|",
                                array(
                                    self::IMPORT_OPTION_CREATE_NEW,
                                    self::IMPORT_OPTION_OVERWRITE,
                                    self::IMPORT_OPTION_DISABLE_AND_CREATE_NEW,
                                    self::IMPORT_OPTION_KEEP_WITHOUT_CHANGING_AND_CREATE_NEW
                                )
                            ))
                        ),
                        self::IMPORT_STAT_TARGET_ALREADY_EXISTS
                    );
                }
                break;
            case self::IMPORT_OPTION_OVERWRITE:
                break;
            case self::IMPORT_OPTION_DISABLE_AND_CREATE_NEW:
                break;
            case self::IMPORT_OPTION_KEEP_WITHOUT_CHANGING_AND_CREATE_NEW:
                break;
        }

        $processes = new \Processes();

        switch ($optionGroup) {
            case self::GROUP_IMPORT_OPTION_CREATE_NEW:
                $arrayAux = $processes->checkExistingGroups($this->importData["tables"]["workflow"]["groupwfs"]);

                if (is_array($arrayAux) && count($arrayAux) > 0) {
                    throw new \Exception(
                        \G::LoadTranslation(
                            "ID_IMPORTER_GROUP_ALREADY_EXISTS_SET_ACTION_TO_CONTINUE",
                            array(implode(
                                "|",
                                array(
                                    self::GROUP_IMPORT_OPTION_CREATE_NEW,
                                    self::GROUP_IMPORT_OPTION_RENAME,
                                    self::GROUP_IMPORT_OPTION_MERGE_PREEXISTENT
                                )
                            ))
                        ),
                        self::IMPORT_STAT_GROUP_ALREADY_EXISTS
                    );
                }
                break;
            case self::GROUP_IMPORT_OPTION_RENAME:
                $arrayAux = $processes->renameExistingGroups($this->importData["tables"]["workflow"]["groupwfs"]);

                if (is_array($arrayAux) && count($arrayAux) > 0) {
                    $this->importData["tables"]["workflow"]["groupwfs"] = $arrayAux;
                }
                break;
            case self::GROUP_IMPORT_OPTION_MERGE_PREEXISTENT:
                $this->importData["tables"]["workflow"] = (array)($processes->groupwfsUpdateUidByDatabase((object)($this->importData["tables"]["workflow"])));
                break;
        }

        //Import
        $name = $this->importData["tables"]["bpmn"]["project"][0]["prj_name"];

        switch ($option) {
            case self::IMPORT_OPTION_CREATE_NEW:
                //Shouldn't generate new UID for all objects
                $generateUid = false;
                break;
            case self::IMPORT_OPTION_OVERWRITE:
                //Shouldn't generate new UID for all objects
                $this->removeProject();

                $generateUid = false;
                break;
            case self::IMPORT_OPTION_DISABLE_AND_CREATE_NEW:
                //Should generate new UID for all objects
                $this->disableProject();

                $name = "New - " . $name . " - " . date("M d, H:i");

                $generateUid = true;
                break;
            case self::IMPORT_OPTION_KEEP_WITHOUT_CHANGING_AND_CREATE_NEW:
                //Should generate new UID for all objects
                $name = \G::LoadTranslation("ID_COPY_OF") . " - " . $name . " - " . date("M d, H:i");

                $generateUid = true;
                break;
        }

        $this->importData["tables"]["bpmn"]["project"][0]["prj_name"] = $name;
        $this->importData["tables"]["bpmn"]["diagram"][0]["dia_name"] = $name;
        $this->importData["tables"]["bpmn"]["process"][0]["pro_name"] = $name;
        $this->importData["tables"]["workflow"]["process"][0]["PRO_TITLE"] = $name;

        if ($this->importData["tables"]["workflow"]["process"][0]["PRO_UPDATE_DATE"] . "" == "") {
            $this->importData["tables"]["workflow"]["process"][0]["PRO_UPDATE_DATE"] = null;
        }

        $this->importData["tables"]["workflow"]["process"] = $this->importData["tables"]["workflow"]["process"][0];

        //Import
        $result = $this->doImport($generateUid);

        //Return
        return $result;
    }

    /**
     * Prepare for import, it makes all validations needed
     * @return int
     * @throws \Exception
     */
    public function prepare()
    {
        if ($this->validateSource() === false) {
            throw new \Exception(
                \G::LoadTranslation("ID_IMPORTER_ERROR_FILE_INVALID_TYPE_OR_CORRUPT_DATA"),
                self::IMPORT_STAT_INVALID_SOURCE_FILE
            );
        }

        $this->importData = $this->load();

        $this->validateImportData();
    }

    public function setData($key, $value)
    {
        $this->data[$key] = $value;
    }

    /**
     * Validates the source file
     * @return mixed
     */
    public function validateSource()
    {
        return true;
    }

    public function validateImportData()
    {
        if (! isset($this->importData["tables"]["bpmn"])) {
            throw new \Exception(\G::LoadTranslation("ID_IMPORTER_BPMN_DEFINITION_IS_MISSING"));
        }
        if (! isset($this->importData["tables"]["bpmn"]["project"]) || count($this->importData["tables"]["bpmn"]["project"]) !== 1) {
            throw new \Exception(\G::LoadTranslation("ID_IMPORTER_BPMN_PROJECT_TABLE_DEFINITION_IS_MISSING"));
        }

        $this->throwExceptionIfExistsReservedWordsSql((object)($this->importData["tables"]["workflow"]));

        return true;
    }

    /**
     * Verify if the project already exists
     * @return mixed
     */
    public function targetExists()
    {
        $prjUid = $this->importData["tables"]["bpmn"]["project"][0]["prj_uid"];

        $bpmnProject = \BpmnProjectPeer::retrieveByPK($prjUid);

        return is_object($bpmnProject);
    }

    public function updateProject()
    {

    }

    public function disableProject()
    {
        $project = \ProcessMaker\Project\Adapter\BpmnWorkflow::load($this->metadata["uid"]);
        $project->setDisabled();
    }

    public function removeProject()
    {
        $project = \ProcessMaker\Project\Adapter\BpmnWorkflow::load($this->metadata["uid"]);
        $project->remove(true, false);
    }

    /**
     * Sets the temporal file save directory
     * @param $dirName
     */
    public function setSaveDir($dirName)
    {
        $this->saveDir = rtrim($dirName, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
    }

    /**
     * Gets the temporal file save directory
     * @return string
     */
    public function getSaveDir()
    {
        if (empty($this->saveDir)) {
            $this->saveDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR;
        }

        return $this->saveDir;
    }

    /**
     * Sets the temporal source file
     * @param $filename
     */
    public function setSourceFile($filename)
    {
        $this->filename = $filename;
    }

    /**
     * Set source from Global Http Request resource
     * @param $varName
     * @throws \Exception
     */
    public function setSourceFromGlobals($varName)
    {
        if (! array_key_exists($varName, $_FILES)) {
            throw new \Exception(\G::LoadTranslation("ID_IMPORTER_COULD_NOT_FIND_SPECIFIED_SOURCE_IN_PHP_GLOBALS", array($varName)));
        }

        $data = $_FILES[$varName];

        if ($data["error"] != 0) {
            throw new \Exception(\G::LoadTranslation("ID_IMPORTER_ERROR_WHILE_UPLOADING_FILE", array($data["error"])));
        }

        if (! is_dir($this->getSaveDir())) {
            Util\Common::mk_dir($this->getSaveDir());
        }

        $this->filename = $this->getSaveDir() . $data["name"];

        $oldUmask = umask(0);
        move_uploaded_file($data["tmp_name"], $this->filename);
        @chmod($this->filename, 0755);
        umask($oldUmask);
    }

    protected function importBpmnTables(array $tables, $generateUid = false)
    {
        // Build BPMN project struct
        $project = $tables["project"][0];
        $diagram = $tables["diagram"][0];
        $diagram["activities"] = $tables["activity"];
        $diagram["artifacts"] = (isset($tables["artifact"]))? $tables["artifact"] : array();
        $diagram["events"] = $tables["event"];
        $diagram["flows"] = $tables["flow"];
        $diagram["gateways"] = $tables["gateway"];
        $diagram["data"] = (isset($tables["data"]))? $tables["data"] : array();
        $diagram["participants"] = (isset($tables["participant"]))? $tables["participant"] : array();
        $diagram["laneset"] = (isset($tables["laneset"]))? $tables["laneset"] : array();
        $diagram["lanes"] = (isset($tables["lane"]))? $tables["lane"] : array();
        $project["diagrams"] = array($diagram);
        $project["prj_author"] = isset($this->data["usr_uid"])? $this->data["usr_uid"]: "00000000000000000000000000000001";
        $project["process"] = $tables["process"][0];

        return Adapter\BpmnWorkflow::createFromStruct($project, $generateUid);
    }

    protected function importWfTables(array $tables)
    {
        $workflow = new \ProcessMaker\Project\Workflow();

        $workflow->createDataByArrayData($tables);
    }

    protected function importWfFiles(array $workflowFiles)
    {
        $workflow = new \ProcessMaker\Project\Workflow();

        $workflow->createDataFileByArrayFile($workflowFiles);
    }

    public function doImport($generateUid = true)
    {
        $arrayBpmnTables = $this->importData["tables"]["bpmn"];
        $arrayWorkflowTables = $this->importData["tables"]["workflow"];
        $arrayWorkflowFiles = $this->importData["files"]["workflow"];

        //Import BPMN tables
        $result = $this->importBpmnTables($arrayBpmnTables, $generateUid);

        $projectUidOld = $arrayBpmnTables["project"][0]["prj_uid"];
        $projectUid = ($generateUid)? $result[0]["new_uid"] : $result;

        //Import workflow tables
        if ($generateUid) {
            $result[0]["object"]  = "project";
            $result[0]["old_uid"] = $projectUidOld;
            $result[0]["new_uid"] = $projectUid;

            $workflow = new \ProcessMaker\Project\Workflow();

            list($arrayWorkflowTables, $arrayWorkflowFiles) = $workflow->updateDataUidByArrayUid($arrayWorkflowTables, $arrayWorkflowFiles, $result);
        }

        $this->importWfTables($arrayWorkflowTables);

        //Import workflow files
        $this->importWfFiles($arrayWorkflowFiles);

        //Update
        $workflow = \ProcessMaker\Project\Workflow::load($projectUid);

        foreach ($arrayWorkflowTables["tasks"] as $key => $value) {
            $arrayTaskData = $value;

            if (!in_array($arrayTaskData["TAS_TYPE"], array("GATEWAYTOGATEWAY", "WEBENTRYEVENT", "END-MESSAGE-EVENT", "START-MESSAGE-EVENT", "INTERMEDIATE-THROW-MESSAGE-EVENT", "INTERMEDIATE-CATCH-MESSAGE-EVENT", "START-TIMER-EVENT", "INTERMEDIATE-CATCH-TIMER-EVENT", "END-EMAIL-EVENT", "INTERMEDIATE-EMAIL-EVENT"))) {
                $result = $workflow->updateTask($arrayTaskData["TAS_UID"], $arrayTaskData);
            }
        }

        unset($arrayWorkflowTables["process"]["PRO_CREATE_USER"]);
        unset($arrayWorkflowTables["process"]["PRO_CREATE_DATE"]);
        unset($arrayWorkflowTables["process"]["PRO_UPDATE_DATE"]);
        unset($arrayWorkflowTables["process"]["PRO_CATEGORY"]);
        unset($arrayWorkflowTables["process"]["PRO_CATEGORY_LABEL"]);
        
        $workflow->update($arrayWorkflowTables["process"]);

        //Process-Files upgrade
        $filesManager = new \ProcessMaker\BusinessModel\FilesManager();

        $filesManager->processFilesUpgrade($projectUid);

        //Return
        return $projectUid;
    }

    /**
     * Imports a Project sent through the POST method ($_FILES)
     *
     * @param array  $arrayData      Data
     * @param string $option         Option for Project ("CREATE", "OVERWRITE", "DISABLE", "KEEP")
     * @param string $optionGroup    Option for Group ("CREATE", "RENAME", "MERGE")
     * @param array  $arrayFieldName The field's names
     *
     * return array Returns the data sent and the unique id of Project
     */
    public function importPostFile(array $arrayData, $option = "CREATE", $optionGroup = "CREATE", array $arrayFieldName = array())
    {
        try {
            //Set data
            $arrayFieldName["projectFile"] = (isset($arrayFieldName["projectFile"]))? $arrayFieldName["projectFile"] : "PROJECT_FILE";
            $arrayFieldName["option"] = (isset($arrayFieldName["option"]))? $arrayFieldName["option"] : "OPTION";
            $arrayFieldName["optionGroup"] = (isset($arrayFieldName["optionGroup"]))? $arrayFieldName["optionGroup"] : "OPTION_GROUP";

            $arrayFieldDefinition = array(
                $arrayFieldName["projectFile"] => array("type" => "string", "required" => true, "empty" => false, "defaultValues" => array(), "fieldNameAux" => "projectFile")
            );

            $arrayFieldNameForException = $arrayFieldName;

            if (isset($_FILES[$arrayFieldName["projectFile"]])) {
                $_FILES["filePmx"] = $_FILES[$arrayFieldName["projectFile"]];
            }

            if (isset($arrayData[$arrayFieldName["projectFile"]]) &&
                isset($arrayData[$arrayFieldName["projectFile"]]["name"]) &&
                is_array($arrayData[$arrayFieldName["projectFile"]])
            ) {
                $arrayData[$arrayFieldName["projectFile"]] = $arrayData[$arrayFieldName["projectFile"]]["name"];
            }

            $optionCaseUpper = (strtoupper($option) == $option)? true : false;
            $option = strtoupper($option);

            $optionGroupCaseUpper = (strtoupper($optionGroup) == $optionGroup)? true : false;
            $optionGroup = strtoupper($optionGroup);

            //Verify data
            $process = new \ProcessMaker\BusinessModel\Process();
            $validator = new \ProcessMaker\BusinessModel\Validator();

            $validator->throwExceptionIfDataIsEmpty($arrayData, "\$arrayData");

            $process->throwExceptionIfDataNotMetFieldDefinition($arrayData, $arrayFieldDefinition, $arrayFieldNameForException, true);

            $arrayOptionDefaultValues = array("CREATE", "OVERWRITE", "DISABLE", "KEEP");
            $arrayOptionGroupDefaultValues = array("CREATE", "RENAME", "MERGE");

            $arrayAux = array(
                array($option,      $arrayOptionDefaultValues,      $arrayFieldNameForException["option"],      $optionCaseUpper),
                array($optionGroup, $arrayOptionGroupDefaultValues, $arrayFieldNameForException["optionGroup"], $optionGroupCaseUpper)
            );

            foreach ($arrayAux as $value) {
                $opt = $value[0];
                $arrayDefaultValues = $value[1];
                $fieldNameForException = $value[2];
                $caseUpper = $value[3];

                if ($opt != "") {
                    if (!in_array($opt, $arrayDefaultValues, true)) {
                        $strdv = implode("|", $arrayDefaultValues);

                        throw new \Exception(
                            \G::LoadTranslation(
                                "ID_INVALID_VALUE_ONLY_ACCEPTS_VALUES",
                                array($fieldNameForException, ($caseUpper)? $strdv : strtolower($strdv))
                            )
                        );
                    }
                }
            }

            if ((isset($_FILES["filePmx"]) && pathinfo($_FILES["filePmx"]["name"], PATHINFO_EXTENSION) != "pmx") ||
                (isset($arrayData[$arrayFieldName["projectFile"]]) && pathinfo($arrayData[$arrayFieldName["projectFile"]], PATHINFO_EXTENSION) != "pmx")
            ) {
                throw new \Exception(\G::LoadTranslation("ID_IMPORTER_FILE_EXTENSION_IS_NOT_PMX"));
            }

            //Set variables
            $arrayAux = array(
                (($option != "")? "CREATE" : "") => self::IMPORT_OPTION_CREATE_NEW,
                "OVERWRITE" => self::IMPORT_OPTION_OVERWRITE,
                "DISABLE"   => self::IMPORT_OPTION_DISABLE_AND_CREATE_NEW,
                "KEEP"      => self::IMPORT_OPTION_KEEP_WITHOUT_CHANGING_AND_CREATE_NEW
            );

            $option = $arrayAux[$option];

            $arrayAux = array(
                (($optionGroup != "")? "CREATE" : "") => self::GROUP_IMPORT_OPTION_CREATE_NEW,
                "RENAME" => self::GROUP_IMPORT_OPTION_RENAME,
                "MERGE"  => self::GROUP_IMPORT_OPTION_MERGE_PREEXISTENT
            );

            $optionGroup = $arrayAux[$optionGroup];

            if (isset($_FILES["filePmx"])) {
                $this->setSourceFromGlobals("filePmx");
            } else {
                $filePmx = rtrim($this->getSaveDir(), PATH_SEP) . PATH_SEP . $arrayData[$arrayFieldName["projectFile"]];

                if (isset($arrayData[$arrayFieldName["projectFile"]]) && file_exists($filePmx)) {
                    $this->setSourceFile($filePmx);
                } else {
                    throw new \Exception(\G::LoadTranslation("ID_IMPORTER_FILE_DOES_NOT_EXIST", array($arrayFieldNameForException["projectFile"], $arrayData[$arrayFieldName["projectFile"]])));
                }
            }

            //Import
            try {
                $projectUid = $this->import($option, $optionGroup);

                $arrayData = array_merge(array("PRJ_UID" => $projectUid), $arrayData);
            } catch (\Exception $e) {
                $strOpt  = implode("|", $arrayOptionDefaultValues);
                $strOptg = implode("|", $arrayOptionGroupDefaultValues);

                $strOpt = ($optionCaseUpper)? $strOpt : strtolower($strOpt);
                $strOptg = ($optionGroupCaseUpper)? $strOptg : strtolower($strOptg);

                $msg = str_replace(
                    array(
                        self::IMPORT_OPTION_CREATE_NEW,
                        self::IMPORT_OPTION_OVERWRITE,
                        self::IMPORT_OPTION_DISABLE_AND_CREATE_NEW,
                        self::IMPORT_OPTION_KEEP_WITHOUT_CHANGING_AND_CREATE_NEW,

                        self::GROUP_IMPORT_OPTION_CREATE_NEW,
                        self::GROUP_IMPORT_OPTION_RENAME,
                        self::GROUP_IMPORT_OPTION_MERGE_PREEXISTENT
                    ),
                    array_merge(explode("|", $strOpt), explode("|", $strOptg)),
                    $e->getMessage()
                );

                throw new \Exception($msg);
            }

            //Return
            if ($arrayFieldName["projectFile"] != strtoupper($arrayFieldName["projectFile"])) {
                $arrayData = array_change_key_case($arrayData, CASE_LOWER);
            }

            return $arrayData;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function saveAs($prj_uid, $prj_name, $prj_description, $prj_category)
    {
        try {
            $exporter = new \ProcessMaker\Exporter\XmlExporter($prj_uid);
            $getProjectName = $exporter->truncateName($exporter->getProjectName(), false);

            $outputDir = PATH_DATA . "sites" . PATH_SEP . SYS_SYS . PATH_SEP . "files" . PATH_SEP . "output" . PATH_SEP;
            $version = \ProcessMaker\Util\Common::getLastVersion($outputDir . $getProjectName . "-*.pmx") + 1;
            $outputFilename = $outputDir . sprintf("%s-%s.%s", str_replace(" ", "_", $getProjectName), $version, "pmx");

            $exporter->setMetadata("export_version", $version);
            $outputFilename = $outputDir . $exporter->saveExport($outputFilename);

            $httpStream = new \ProcessMaker\Util\IO\HttpStream();
            $fileExtension = pathinfo($outputFilename, PATHINFO_EXTENSION);

            $this->setSourceFile($outputFilename);
            $this->prepare();
            $this->importData["tables"]["bpmn"]["project"][0]["prj_name"] = $prj_name;
            $this->importData["tables"]["bpmn"]["project"][0]["prj_description"] = $prj_description;
            $this->importData["tables"]["bpmn"]["diagram"][0]["dia_name"] = $prj_name;
            $this->importData["tables"]["bpmn"]["process"][0]["pro_name"] = $prj_name;
            $this->importData["tables"]["workflow"]["process"][0]["PRO_TITLE"] = $prj_name;
            $this->importData["tables"]["workflow"]["process"][0]["PRO_DESCRIPTION"] = $prj_description;
            $this->importData["tables"]["workflow"]["process"][0]["PRO_CATEGORY"] = $prj_category;
            $this->importData["tables"]["workflow"]["process"][0]["PRO_CATEGORY_LABEL"] = null;
            $this->importData["tables"]["workflow"]["process"][0]["PRO_UPDATE_DATE"] = null;
            $this->importData["tables"]["workflow"]["process"] = $this->importData["tables"]["workflow"]["process"][0];
            return array("prj_uid" => $this->doImport());
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}

