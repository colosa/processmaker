<?php
namespace ProcessMaker\BusinessModel;

class WebEntry
{
    private $arrayFieldDefinition = array(
        "WE_UID"                   => array("type" => "string", "required" => false, "empty" => false, "defaultValues" => array(),             "fieldNameAux" => "webEntryUid"),

        "TAS_UID"                  => array("type" => "string", "required" => true,  "empty" => false, "defaultValues" => array(),             "fieldNameAux" => "taskUid"),
        "DYN_UID"                  => array("type" => "string", "required" => true,  "empty" => false, "defaultValues" => array(),             "fieldNameAux" => "dynaFormUid"),
        "USR_UID"                  => array("type" => "string", "required" => false, "empty" => false, "defaultValues" => array(),             "fieldNameAux" => "userUid"),
        "WE_TITLE"                 => array("type" => "string", "required" => true,  "empty" => false, "defaultValues" => array(),             "fieldNameAux" => "webEntryTitle"),
        "WE_DESCRIPTION"           => array("type" => "string", "required" => false, "empty" => true,  "defaultValues" => array(),             "fieldNameAux" => "webEntryDescription"),
        "WE_METHOD"                => array("type" => "string", "required" => true,  "empty" => false, "defaultValues" => array("WS", "HTML"), "fieldNameAux" => "webEntryMethod"),
        "WE_INPUT_DOCUMENT_ACCESS" => array("type" => "int",    "required" => true,  "empty" => false, "defaultValues" => array(0, 1),         "fieldNameAux" => "webEntryInputDocumentAccess")
    );

    private $arrayUserFieldDefinition = array(
        "USR_UID" => array("type" => "string", "required" => true, "empty" => false, "defaultValues" => array(), "fieldNameAux" => "userUid")
    );

    private $formatFieldNameInUppercase = true;

    private $arrayFieldNameForException = array(
        "processUid" => "PRO_UID",
        "userUid"    => "USR_UID"
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
     * Sanitizes a filename
     *
     * @param string $name Filename
     *
     * return string Return the filename sanitizes
     */
    public function sanitizeFilename($name)
    {
        $name = trim($name);

        $arraySpecialCharSearch  = array("/", "\\", " ");
        $arraySpecialCharReplace = array("_", "_",  "_");

        $newName = str_replace($arraySpecialCharSearch, $arraySpecialCharReplace, $name);

        $arraySpecialCharSearch  = array("/[\!-\)\:-\@]/", "/[\{\}\[\]\|\¿\?\+\*]/");
        $arraySpecialCharReplace = array("",               "");

        $newName = preg_replace($arraySpecialCharSearch, $arraySpecialCharReplace, $newName);

        return $newName;
    }

    /**
     * Verify if exists the Web Entry
     *
     * @param string $webEntryUid Unique id of Web Entry
     *
     * return bool Return true if exists the Web Entry, false otherwise
     */
    public function exists($webEntryUid)
    {
        try {
            $obj = \WebEntryPeer::retrieveByPK($webEntryUid);

            return (!is_null($obj))? true : false;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Verify if exists the title of a Web Entry
     *
     * @param string $processUid         Unique id of Process
     * @param string $webEntryTitle      Title
     * @param string $webEntryUidExclude Unique id of Web Entry to exclude
     *
     * return bool Return true if exists the title of a Web Entry, false otherwise
     */
    public function existsTitle($processUid, $webEntryTitle, $webEntryUidExclude = "")
    {
        try {
            $delimiter = \DBAdapter::getStringDelimiter();

            $criteria = new \Criteria("workflow");

            $criteria->addSelectColumn(\WebEntryPeer::WE_UID);

            $criteria->addAlias("CT", \ContentPeer::TABLE_NAME);

            $arrayCondition = array();
            $arrayCondition[] = array(\WebEntryPeer::WE_UID, "CT.CON_ID", \Criteria::EQUAL);
            $arrayCondition[] = array("CT.CON_CATEGORY", $delimiter . "WE_TITLE" . $delimiter, \Criteria::EQUAL);
            $arrayCondition[] = array("CT.CON_LANG", $delimiter . SYS_LANG . $delimiter, \Criteria::EQUAL);
            $criteria->addJoinMC($arrayCondition, \Criteria::LEFT_JOIN);

            $criteria->add(\WebEntryPeer::PRO_UID, $processUid, \Criteria::EQUAL);

            if ($webEntryUidExclude != "") {
                $criteria->add(\WebEntryPeer::WE_UID, $webEntryUidExclude, \Criteria::NOT_EQUAL);
            }

            $criteria->add("CT.CON_VALUE", $webEntryTitle, \Criteria::EQUAL);

            $rsCriteria = \WebEntryPeer::doSelectRS($criteria);

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
     * Verify if does not exists the Web Entry
     *
     * @param string $webEntryUid           Unique id of Web Entry
     * @param string $fieldNameForException Field name for the exception
     *
     * return void Throw exception if does not exists the Web Entry
     */
    public function throwExceptionIfNotExistsWebEntry($webEntryUid, $fieldNameForException)
    {
        try {
            if (!$this->exists($webEntryUid)) {
                throw new \Exception(\G::LoadTranslation("ID_WEB_ENTRY_DOES_NOT_EXIST", array($fieldNameForException, $webEntryUid)));
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Verify if exists the title of a Web Entry
     *
     * @param string $processUid            Unique id of Process
     * @param string $webEntryTitle         Title
     * @param string $fieldNameForException Field name for the exception
     * @param string $webEntryUidExclude    Unique id of Web Entry to exclude
     *
     * return void Throw exception if exists the title of a Web Entry
     */
    public function throwExceptionIfExistsTitle($processUid, $webEntryTitle, $fieldNameForException, $webEntryUidExclude = "")
    {
        try {
            if ($this->existsTitle($processUid, $webEntryTitle, $webEntryUidExclude)) {
                throw new \Exception(\G::LoadTranslation("ID_WEB_ENTRY_TITLE_ALREADY_EXISTS", array($fieldNameForException, $webEntryTitle)));
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Validate the data if they are invalid (INSERT and UPDATE)
     *
     * @param string $webEntryUid Unique id of Web Entry
     * @param string $processUid  Unique id of Process
     * @param array  $arrayData   Data
     *
     * return void Throw exception if data has an invalid value
     */
    public function throwExceptionIfDataIsInvalid($webEntryUid, $processUid, array $arrayData)
    {
        try {
            //Set variables
            $arrayWebEntryData = ($webEntryUid == "")? array() : $this->getWebEntry($webEntryUid, true);
            $flagInsert        = ($webEntryUid == "")? true : false;

            $arrayDataMain = array_merge($arrayWebEntryData, $arrayData);

            //Verify data - Field definition
            $process = new \ProcessMaker\BusinessModel\Process();

            $process->throwExceptionIfDataNotMetFieldDefinition($arrayData, $this->arrayFieldDefinition, $this->arrayFieldNameForException, $flagInsert);

            if ($arrayDataMain["WE_METHOD"] == "WS") {
                $process->throwExceptionIfDataNotMetFieldDefinition($arrayData, $this->arrayUserFieldDefinition, $this->arrayFieldNameForException, $flagInsert);
            }

            //Verify data
            if (isset($arrayData["WE_TITLE"])) {
                $this->throwExceptionIfExistsTitle($processUid, $arrayData["WE_TITLE"], $this->arrayFieldNameForException["webEntryTitle"], $webEntryUid);
            }

            if (isset($arrayData["TAS_UID"])) {
                $task = new \ProcessMaker\BusinessModel\Task();

                $task->throwExceptionIfNotExistsTask($processUid, $arrayData["TAS_UID"], $this->arrayFieldNameForException["taskUid"]);
            }

            if (isset($arrayData["DYN_UID"])) {
                $dynaForm = new \ProcessMaker\BusinessModel\DynaForm();

                $dynaForm->throwExceptionIfNotExistsDynaForm($arrayData["DYN_UID"], $processUid, $this->arrayFieldNameForException["dynaFormUid"]);
            }

            if ($arrayDataMain["WE_METHOD"] == "WS" && isset($arrayData["USR_UID"])) {
                $process->throwExceptionIfNotExistsUser($arrayData["USR_UID"], $this->arrayFieldNameForException["userUid"]);
            }

            $task = new \Task();

            $arrayTaskData = $task->load($arrayDataMain["TAS_UID"]);

            if (isset($arrayData["TAS_UID"])) {
                if ($arrayTaskData["TAS_START"] == "FALSE") {
                    throw new \Exception(\G::LoadTranslation("ID_ACTIVITY_IS_NOT_INITIAL_ACTIVITY", array($arrayTaskData["TAS_TITLE"])));
                }

                if ($arrayTaskData["TAS_ASSIGN_TYPE"] != "BALANCED") {
                    throw new \Exception(\G::LoadTranslation("ID_WEB_ENTRY_ACTIVITY_DOES_NOT_HAVE_VALID_ASSIGNMENT_TYPE", array($arrayTaskData["TAS_TITLE"])));
                }
            }

            if ($arrayDataMain["WE_METHOD"] == "WS" && isset($arrayData["TAS_UID"])) {
                $task = new \Tasks();

                if ($task->assignUsertoTask($arrayData["TAS_UID"]) == 0) {
                    throw new \Exception(\G::LoadTranslation("ID_ACTIVITY_DOES_NOT_HAVE_USERS", array($arrayTaskData["TAS_TITLE"])));
                }
            }

            if (isset($arrayData["DYN_UID"])) {
                $dynaForm = new \Dynaform();

                $arrayDynaFormData = $dynaForm->Load($arrayData["DYN_UID"]);

                $step = new \ProcessMaker\BusinessModel\Step();

                if (!$step->existsRecord($arrayDataMain["TAS_UID"], "DYNAFORM", $arrayData["DYN_UID"])) {
                    throw new \Exception(\G::LoadTranslation("ID_DYNAFORM_IS_NOT_ASSIGNED_TO_ACTIVITY", array($arrayDynaFormData["DYN_TITLE"], $arrayTaskData["TAS_TITLE"])));
                }
            }

            if ($arrayDataMain["WE_METHOD"] == "WS" && isset($arrayData["USR_UID"])) {
                $user = new \Users();

                $arrayUserData = $user->load($arrayData["USR_UID"]);

                //Verify if User is assigned to Task
                $projectUser = new \ProcessMaker\BusinessModel\ProjectUser();

                if (!$projectUser->userIsAssignedToTask($arrayData["USR_UID"], $arrayDataMain["TAS_UID"])) {
                    throw new \Exception(\G::LoadTranslation("ID_USER_DOES_NOT_HAVE_ACTIVITY_ASSIGNED", array($arrayUserData["USR_USERNAME"], $arrayTaskData["TAS_TITLE"])));
                }
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Set value in WE_DATA
     *
     * @param string $webEntryUid Unique id of Web Entry
     *
     * return void
     */
    public function setWeData($webEntryUid)
    {
        try {
            //Verify data
            $this->throwExceptionIfNotExistsWebEntry($webEntryUid, $this->arrayFieldNameForException["webEntryUid"]);

            //Set variables
            $arrayWebEntryData = $this->getWebEntry($webEntryUid, true);

            $processUid  = $arrayWebEntryData["PRO_UID"];
            $taskUid     = $arrayWebEntryData["TAS_UID"];
            $dynaFormUid = $arrayWebEntryData["DYN_UID"];
            $webEntryMethod = $arrayWebEntryData["WE_METHOD"];
            $webEntryInputDocumentAccess = $arrayWebEntryData["WE_INPUT_DOCUMENT_ACCESS"];
            $webEntryData = "";

            $wsRoundRobin = 0; //0, 1 //0 - Cyclical Assignment

            $pathDataPublicProcess = PATH_DATA_PUBLIC . $processUid;

            //Delete previous files
            if (trim($arrayWebEntryData["WE_DATA"]) != "") {
                $fileName = str_replace(".php", "", trim($arrayWebEntryData["WE_DATA"]));
                $file = $pathDataPublicProcess . PATH_SEP . $fileName . ".php";

                if (is_file($file) && file_exists($file)) {
                    unlink($file);
                    unlink($pathDataPublicProcess . PATH_SEP . $fileName . "Post.php");
                }
            }

            //Create files
            \G::mk_dir($pathDataPublicProcess, 0777);

            $http = (\G::is_https())? "https://" : "http://";

            switch ($webEntryMethod) {
                case "WS":
                    require_once(PATH_RBAC . "model" . PATH_SEP . "RbacUsers.php");

                    $user = new \RbacUsers();

                    $arrayUserData = $user->load($arrayWebEntryData["USR_UID"]);

                    $usrUsername = $arrayUserData["USR_USERNAME"];
                    $usrPassword = $arrayUserData["USR_PASSWORD"];

                    $dynaForm = new \Dynaform();

                    $arrayDynaFormData = $dynaForm->Load($arrayWebEntryData["DYN_UID"]);

                    //Creating sys.info;
                    $sitePublicPath = "";

                    if (file_exists($sitePublicPath . "")) {
                    }

                    //Creating the first file
                    $weTitle = $this->sanitizeFilename($arrayWebEntryData["WE_TITLE"]);
                    $fileName = $weTitle;

                    $fileContent = "<?php\n";
                    $fileContent .= "global \$_DBArray;\n";
                    $fileContent .= "if (!isset(\$_DBArray)) {\n";
                    $fileContent .= "  \$_DBArray = array();\n";
                    $fileContent .= "}\n";
                    $fileContent .= "\$_SESSION[\"PROCESS\"] = \"" . $processUid . "\";\n";
                    $fileContent .= "\$_SESSION[\"CURRENT_DYN_UID\"] = \"" . $dynaFormUid . "\";\n";
                    $fileContent .= "\$G_PUBLISH = new Publisher();\n";

                    $fileContent .= "G::LoadClass(\"pmDynaform\");\n";
                    $fileContent .= "\$a = new pmDynaform(array(\"CURRENT_DYNAFORM\" => \"" . $arrayWebEntryData["DYN_UID"] . "\"));\n";
                    $fileContent .= "if (\$a->isResponsive()) {";
                    $fileContent .= "  \$a->printWebEntry(\"" . $fileName . "Post.php\");";
                    $fileContent .= "} else {";
                    $fileContent .= "  \$G_PUBLISH->AddContent(\"dynaform\", \"xmlform\", \"" . $processUid . PATH_SEP . $dynaFormUid . "\", \"\", array(), \"" . $fileName . "Post.php\");\n";
                    $fileContent .= "  G::RenderPage(\"publish\", \"blank\");";
                    $fileContent .= "}";

                    file_put_contents($pathDataPublicProcess . PATH_SEP . $fileName . ".php", $fileContent);

                    //Creating the second file, the  post file who receive the post form.
                    $pluginTpl = PATH_TPL . "processes" . PATH_SEP . "webentryPost.tpl";

                    $template = new \TemplatePower($pluginTpl);
                    $template->prepare();

                    $template->assign("wsdlUrl", $http . $_SERVER["HTTP_HOST"] . "/sys" . SYS_SYS . "/" . SYS_LANG . "/" . SYS_SKIN . "/services/wsdl2");
                    $template->assign("wsUploadUrl", $http . $_SERVER["HTTP_HOST"] . "/sys" . SYS_SYS . "/" . SYS_LANG . "/" . SYS_SKIN . "/services/upload");
                    $template->assign("processUid", $processUid);
                    $template->assign("dynaformUid", $dynaFormUid);
                    $template->assign("taskUid", $taskUid);
                    $template->assign("wsUser", $usrUsername);
                    $template->assign("wsPass", \Bootstrap::getPasswordHashType() . ':' . $usrPassword);
                    $template->assign("wsRoundRobin", $wsRoundRobin);

                    if ($webEntryInputDocumentAccess == 0) {
                        //Restricted to process permissions
                        $template->assign("USR_VAR", "\$cInfo = ws_getCaseInfo(\$caseId);\n\t  \$USR_UID = \$cInfo->currentUsers->userId;");
                    } else {
                        //No Restriction
                        $template->assign("USR_VAR", "\$USR_UID = -1;");
                    }

                    $template->assign("dynaform", $arrayDynaFormData["DYN_TITLE"]);
                    $template->assign("timestamp", date("l jS \of F Y h:i:s A"));
                    $template->assign("ws", SYS_SYS);
                    $template->assign("version", \System::getVersion());

                    $fileName = $pathDataPublicProcess . PATH_SEP . $weTitle . "Post.php";

                    file_put_contents($fileName, $template->getOutputContent());

                    //Creating the third file, only if this wsClient.php file doesn't exist.
                    $fileName = $pathDataPublicProcess . PATH_SEP . "wsClient.php";
                    $pluginTpl = PATH_CORE . "templates" . PATH_SEP . "processes" . PATH_SEP . "wsClient.php";

                    if (file_exists($fileName)) {
                        if (filesize($fileName) != filesize($pluginTpl)) {
                            copy($fileName, $pathDataPublicProcess . PATH_SEP . "wsClient.php.bak");
                            unlink($fileName);

                            $template = new \TemplatePower($pluginTpl);
                            $template->prepare();

                            file_put_contents($fileName, $template->getOutputContent());
                        }
                    } else {
                        $template = new \TemplatePower($pluginTpl);
                        $template->prepare();

                        file_put_contents($fileName, $template->getOutputContent());
                    }

                    //Event
                    $task = new \Task();

                    $arrayTaskData = $task->load($arrayWebEntryData["TAS_UID"]);

                    $weEventUid = $task->getStartingEvent();

                    if ($weEventUid != "") {
                        $event = new \Event();

                        $arrayEventData = array();

                        $arrayEventData["EVN_UID"] = $weEventUid;
                        $arrayEventData["EVN_RELATED_TO"] = "MULTIPLE";
                        $arrayEventData["EVN_ACTION"] = $dynaFormUid;
                        $arrayEventData["EVN_CONDITIONS"] = $usrUsername;

                        $result = $event->update($arrayEventData);
                    }

                    //WE_DATA
                    $webEntryData = $weTitle . ".php";
                    break;
                case "HTML":
                    global $G_FORM;

                    if (! class_exists("Smarty")) {
                        $loader = \Maveriks\Util\ClassLoader::getInstance();
                        $loader->addClass("Smarty", PATH_THIRDPARTY . "smarty" . PATH_SEP . "libs" . PATH_SEP . "Smarty.class.php");
                    }

                    $G_FORM = new \Form($processUid . "/" . $dynaFormUid, PATH_DYNAFORM, SYS_LANG, false);
                    $G_FORM->action = $http . $_SERVER["HTTP_HOST"] . "/sys" . SYS_SYS . "/" . SYS_LANG . "/" . SYS_SKIN . "/services/cases_StartExternal.php";

                    $scriptCode = "";
                    $scriptCode = $G_FORM->render(PATH_TPL . "xmlform" . ".html", $scriptCode);
                    $scriptCode = str_replace("/controls/", $http . $_SERVER["HTTP_HOST"] . "/controls/", $scriptCode);
                    $scriptCode = str_replace("/js/maborak/core/images/", $http . $_SERVER["HTTP_HOST"] . "/js/maborak/core/images/", $scriptCode);

                    //Render the template
                    $pluginTpl = PATH_TPL . "processes" . PATH_SEP . "webentry.tpl";

                    $template = new \TemplatePower($pluginTpl);
                    $template->prepare();

                    $step = new \Step();
                    $sUidGrids = $step->lookingforUidGrids($processUid, $dynaFormUid);

                    $template->assign("URL_MABORAK_JS", \G::browserCacheFilesUrl("/js/maborak/core/maborak.js"));
                    $template->assign("URL_TRANSLATION_ENV_JS", \G::browserCacheFilesUrl("/jscore/labels/" . SYS_LANG . ".js"));
                    $template->assign("siteUrl", $http . $_SERVER["HTTP_HOST"]);
                    $template->assign("sysSys", SYS_SYS);
                    $template->assign("sysLang", SYS_LANG);
                    $template->assign("sysSkin", SYS_SKIN);
                    $template->assign("processUid", $processUid);
                    $template->assign("dynaformUid", $dynaFormUid);
                    $template->assign("taskUid", $taskUid);
                    $template->assign("dynFileName", $processUid . "/" . $dynaFormUid);
                    $template->assign("formId", $G_FORM->id);
                    $template->assign("scriptCode", $scriptCode);

                    if (sizeof($sUidGrids) > 0) {
                        foreach ($sUidGrids as $k => $v) {
                            $template->newBlock("grid_uids");
                            $template->assign("siteUrl", $http . $_SERVER["HTTP_HOST"]);
                            $template->assign("gridFileName", $processUid . "/" . $v);
                        }
                    }

                    //WE_DATA
                    $html = str_replace("</body>", "</form></body>", str_replace("</form>", "", $template->getOutputContent()));

                    $webEntryData = $html;
                    break;
            }

            //Update
            //Update where
            $criteriaWhere = new \Criteria("workflow");
            $criteriaWhere->add(\WebEntryPeer::WE_UID, $webEntryUid);

            //Update set
            $criteriaSet = new \Criteria("workflow");
            $criteriaSet->add(\WebEntryPeer::WE_DATA, $webEntryData);

            \BasePeer::doUpdate($criteriaWhere, $criteriaSet, \Propel::getConnection("workflow"));
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Create Web Entry for a Process
     *
     * @param string $processUid     Unique id of Process
     * @param string $userUidCreator Unique id of creator User
     * @param array  $arrayData      Data
     *
     * return array Return data of the new Web Entry created
     */
    public function create($processUid, $userUidCreator, array $arrayData)
    {
        try {
            //Verify data
            $process = new \ProcessMaker\BusinessModel\Process();
            $validator = new \ProcessMaker\BusinessModel\Validator();

            $validator->throwExceptionIfDataIsNotArray($arrayData, "\$arrayData");
            $validator->throwExceptionIfDataIsEmpty($arrayData, "\$arrayData");

            //Set data
            $arrayData = array_change_key_case($arrayData, CASE_UPPER);

            unset($arrayData["WE_UID"]);
            unset($arrayData["WE_DATA"]);

            //Verify data
            $process->throwExceptionIfNotExistsProcess($processUid, $this->arrayFieldNameForException["processUid"]);

            $this->throwExceptionIfDataIsInvalid("", $processUid, $arrayData);

            //Create
            $cnn = \Propel::getConnection("workflow");

            try {
                $webEntry = new \WebEntry();

                $webEntry->fromArray($arrayData, \BasePeer::TYPE_FIELDNAME);

                $webEntryUid = \ProcessMaker\Util\Common::generateUID();

                $webEntry->setWeUid($webEntryUid);
                $webEntry->setProUid($processUid);
                $webEntry->setWeCreateUsrUid($userUidCreator);
                $webEntry->setWeCreateDate("now");

                if ($webEntry->validate()) {
                    $cnn->begin();

                    $result = $webEntry->save();

                    $cnn->commit();

                    //Set WE_TITLE
                    if (isset($arrayData["WE_TITLE"])) {
                        $result = \Content::addContent("WE_TITLE", "", $webEntryUid, SYS_LANG, $arrayData["WE_TITLE"]);
                    }

                    if (isset($arrayData["WE_DESCRIPTION"])) {
                        $result = \Content::addContent("WE_DESCRIPTION", "", $webEntryUid, SYS_LANG, $arrayData["WE_DESCRIPTION"]);
                    }

                    //Set WE_DATA
                    $this->setWeData($webEntryUid);

                    //Return
                    return $this->getWebEntry($webEntryUid);
                } else {
                    $msg = "";

                    foreach ($webEntry->getValidationFailures() as $validationFailure) {
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
     * Update Web Entry
     *
     * @param string $webEntryUid    Unique id of Web Entry
     * @param string $userUidUpdater Unique id of updater User
     * @param array  $arrayData      Data
     *
     * return array Return data of the Web Entry updated
     */
    public function update($webEntryUid, $userUidUpdater, array $arrayData)
    {
        try {
            //Verify data
            $process = new \ProcessMaker\BusinessModel\Process();
            $validator = new \ProcessMaker\BusinessModel\Validator();

            $validator->throwExceptionIfDataIsNotArray($arrayData, "\$arrayData");
            $validator->throwExceptionIfDataIsEmpty($arrayData, "\$arrayData");

            //Set data
            $arrayData = array_change_key_case($arrayData, CASE_UPPER);

            //Set variables
            $arrayWebEntryData = $this->getWebEntry($webEntryUid, true);

            //Verify data
            $this->throwExceptionIfNotExistsWebEntry($webEntryUid, $this->arrayFieldNameForException["webEntryUid"]);

            $this->throwExceptionIfDataIsInvalid($webEntryUid, $arrayWebEntryData["PRO_UID"], $arrayData);

            //Update
            $cnn = \Propel::getConnection("workflow");

            try {
                $webEntry = \WebEntryPeer::retrieveByPK($webEntryUid);

                $webEntry->fromArray($arrayData, \BasePeer::TYPE_FIELDNAME);

                $webEntry->setWeUpdateUsrUid($userUidUpdater);
                $webEntry->setWeUpdateDate("now");

                if ($webEntry->validate()) {
                    $cnn->begin();

                    $result = $webEntry->save();

                    $cnn->commit();

                    //Set WE_TITLE
                    if (isset($arrayData["WE_TITLE"])) {
                        $result = \Content::addContent("WE_TITLE", "", $webEntryUid, SYS_LANG, $arrayData["WE_TITLE"]);
                    }

                    if (isset($arrayData["WE_DESCRIPTION"])) {
                        $result = \Content::addContent("WE_DESCRIPTION", "", $webEntryUid, SYS_LANG, $arrayData["WE_DESCRIPTION"]);
                    }

                    //Set WE_DATA
                    $this->setWeData($webEntryUid);

                    //Return
                    if (!$this->formatFieldNameInUppercase) {
                        $arrayData = array_change_key_case($arrayData, CASE_LOWER);
                    }

                    return $arrayData;
                } else {
                    $msg = "";

                    foreach ($webEntry->getValidationFailures() as $validationFailure) {
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
     * Delete Web Entry
     *
     * @param string $webEntryUid Unique id of Web Entry
     *
     * return void
     */
    public function delete($webEntryUid)
    {
        try {
            //Verify data
            $this->throwExceptionIfNotExistsWebEntry($webEntryUid, $this->arrayFieldNameForException["webEntryUid"]);

            //Set variables
            $arrayWebEntryData = $this->getWebEntry($webEntryUid, true);

            //Delete content
            \Content::removeContent("WE_TITLE", "", $webEntryUid);
            \Content::removeContent("WE_DESCRIPTION", "", $webEntryUid);

            //Delete web entry
            $criteria = new \Criteria("workflow");

            $criteria->add(\WebEntryPeer::WE_UID, $webEntryUid);

            $result = \WebEntryPeer::doDelete($criteria);

            //Delete files
            if ($arrayWebEntryData["WE_METHOD"] == "WS") {
                $pathDataPublicProcess = PATH_DATA_PUBLIC . $arrayWebEntryData["PRO_UID"];

                $fileName = str_replace(".php", "", trim($arrayWebEntryData["WE_DATA"]));
                $file = $pathDataPublicProcess . PATH_SEP . $fileName . ".php";

                if (is_file($file) && file_exists($file)) {
                    unlink($file);
                    unlink($pathDataPublicProcess . PATH_SEP . $fileName . "Post.php");
                }
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get criteria for Web Entry
     *
     * return object
     */
    public function getWebEntryCriteria()
    {
        try {
            $delimiter = \DBAdapter::getStringDelimiter();

            $criteria = new \Criteria("workflow");

            $criteria->addSelectColumn(\WebEntryPeer::WE_UID);
            $criteria->addSelectColumn(\WebEntryPeer::PRO_UID);
            $criteria->addSelectColumn(\WebEntryPeer::TAS_UID);
            $criteria->addSelectColumn(\WebEntryPeer::DYN_UID);
            $criteria->addSelectColumn(\WebEntryPeer::USR_UID);
            $criteria->addAsColumn("WE_TITLE", "CT.CON_VALUE");
            $criteria->addAsColumn("WE_DESCRIPTION", "CD.CON_VALUE");
            $criteria->addSelectColumn(\WebEntryPeer::WE_METHOD);
            $criteria->addSelectColumn(\WebEntryPeer::WE_INPUT_DOCUMENT_ACCESS);
            $criteria->addSelectColumn(\WebEntryPeer::WE_DATA);
            $criteria->addSelectColumn(\WebEntryPeer::WE_CREATE_USR_UID);
            $criteria->addSelectColumn(\WebEntryPeer::WE_UPDATE_USR_UID);
            $criteria->addSelectColumn(\WebEntryPeer::WE_CREATE_DATE);
            $criteria->addSelectColumn(\WebEntryPeer::WE_UPDATE_DATE);

            $criteria->addAlias("CT", \ContentPeer::TABLE_NAME);
            $criteria->addAlias("CD", \ContentPeer::TABLE_NAME);

            $arrayCondition = array();
            $arrayCondition[] = array(\WebEntryPeer::WE_UID, "CT.CON_ID", \Criteria::EQUAL);
            $arrayCondition[] = array("CT.CON_CATEGORY", $delimiter . "WE_TITLE" . $delimiter, \Criteria::EQUAL);
            $arrayCondition[] = array("CT.CON_LANG", $delimiter . SYS_LANG . $delimiter, \Criteria::EQUAL);
            $criteria->addJoinMC($arrayCondition, \Criteria::LEFT_JOIN);

            $arrayCondition = array();
            $arrayCondition[] = array(\WebEntryPeer::WE_UID, "CD.CON_ID", \Criteria::EQUAL);
            $arrayCondition[] = array("CD.CON_CATEGORY", $delimiter . "WE_DESCRIPTION" . $delimiter, \Criteria::EQUAL);
            $arrayCondition[] = array("CD.CON_LANG", $delimiter . SYS_LANG . $delimiter, \Criteria::EQUAL);
            $criteria->addJoinMC($arrayCondition, \Criteria::LEFT_JOIN);

            return $criteria;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get data of a Web Entry from a record
     *
     * @param array $record Record
     *
     * return array Return an array with data Web Entry
     */
    public function getWebEntryDataFromRecord(array $record)
    {
        try {
            if ($record["WE_METHOD"] == "WS") {
                $http = (\G::is_https())? "https://" : "http://";
                $url = $http . $_SERVER["HTTP_HOST"] . "/sys" . SYS_SYS . "/" . SYS_LANG . "/" . SYS_SKIN . "/" . $record["PRO_UID"];

                $record["WE_DATA"] = $url . "/" . $record["WE_DATA"];
            }

            $conf = new \Configurations();
            $confEnvSetting = $conf->getFormats();

            $dateTime = new \DateTime($record["WE_CREATE_DATE"]);
            $webEntryCreateDate = $dateTime->format($confEnvSetting["dateFormat"]);

            $webEntryUpdateDate = "";

            if (!empty($record["WE_UPDATE_DATE"])) {
                $dateTime = new \DateTime($record["WE_UPDATE_DATE"]);
                $webEntryUpdateDate = $dateTime->format($confEnvSetting["dateFormat"]);
            }

            return array(
                $this->getFieldNameByFormatFieldName("WE_UID")                   => $record["WE_UID"],
                $this->getFieldNameByFormatFieldName("TAS_UID")                  => $record["TAS_UID"],
                $this->getFieldNameByFormatFieldName("DYN_UID")                  => $record["DYN_UID"],
                $this->getFieldNameByFormatFieldName("USR_UID")                  => $record["USR_UID"] . "",
                $this->getFieldNameByFormatFieldName("WE_TITLE")                 => $record["WE_TITLE"] . "",
                $this->getFieldNameByFormatFieldName("WE_DESCRIPTION")           => $record["WE_DESCRIPTION"] . "",
                $this->getFieldNameByFormatFieldName("WE_METHOD")                => $record["WE_METHOD"],
                $this->getFieldNameByFormatFieldName("WE_INPUT_DOCUMENT_ACCESS") => (int)($record["WE_INPUT_DOCUMENT_ACCESS"]),
                $this->getFieldNameByFormatFieldName("WE_DATA")                  => $record["WE_DATA"],
                $this->getFieldNameByFormatFieldName("WE_CREATE_USR_UID")        => $record["WE_CREATE_USR_UID"],
                $this->getFieldNameByFormatFieldName("WE_UPDATE_USR_UID")        => $record["WE_UPDATE_USR_UID"] . "",
                $this->getFieldNameByFormatFieldName("WE_CREATE_DATE")           => $webEntryCreateDate,
                $this->getFieldNameByFormatFieldName("WE_UPDATE_DATE")           => $webEntryUpdateDate
            );
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get all Web Entries
     *
     * @param string $processUid Unique id of Process
     *
     * return array Return an array with all Web Entries
     */
    public function getWebEntries($processUid)
    {
        try {
            $arrayWebEntry = array();

            //Verify data
            $process = new \ProcessMaker\BusinessModel\Process();

            $process->throwExceptionIfNotExistsProcess($processUid, $this->arrayFieldNameForException["processUid"]);

            //Get data
            $criteria = $this->getWebEntryCriteria();

            $criteria->add(\WebEntryPeer::PRO_UID, $processUid, \Criteria::EQUAL);
            $criteria->addAscendingOrderByColumn("WE_TITLE");

            $rsCriteria = \WebEntryPeer::doSelectRS($criteria);
            $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

            while ($rsCriteria->next()) {
                $row = $rsCriteria->getRow();

                $arrayWebEntry[] = $this->getWebEntryDataFromRecord($row);
            }

            //Return
            return $arrayWebEntry;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get data of a Web Entry
     *
     * @param string $webEntryUid   Unique id of Web Entry
     * @param bool   $flagGetRecord Value that set the getting
     *
     * return array Return an array with data of a Web Entry
     */
    public function getWebEntry($webEntryUid, $flagGetRecord = false)
    {
        try {
            //Verify data
            $this->throwExceptionIfNotExistsWebEntry($webEntryUid, $this->arrayFieldNameForException["webEntryUid"]);

            //Get data
            //SQL
            $criteria = $this->getWebEntryCriteria();

            $criteria->add(\WebEntryPeer::WE_UID, $webEntryUid, \Criteria::EQUAL);

            $rsCriteria = \WebEntryPeer::doSelectRS($criteria);
            $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

            $rsCriteria->next();

            $row = $rsCriteria->getRow();

            //Return
            return (!$flagGetRecord)? $this->getWebEntryDataFromRecord($row) : $row;
        } catch (\Exception $e) {
            throw $e;
        }
    }
}

