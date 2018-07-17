<?php

namespace ProcessMaker\BusinessModel;

use WebEntryEventPeer;
use ProcessPeer;
use Criteria;
use WebEntryPeer;
use Exception;
use G;
use BpmnFlowPeer;
use ProcessMaker\BusinessModel\Process as BusinessModelProcess;
use ProcessMaker\BusinessModel\Validator as BusinessModelValidator;
use ProcessMaker\Project\Workflow;
use WebEntryEvent as ModelWebEntryEvent;
use ProcessMaker\Util\Common;
use Task as ModelTask;
use Propel;
use BasePeer;
use Content;
use Tasks;
use Step;
use TaskPeer;
use StepPeer;
use ResultSet;
use TaskUser;
use TaskUserPeer;


class WebEntryEvent
{
    private $arrayFieldDefinition = array(
        "WEE_UID" => array(
            "type" => "string",
            "required" => false,
            "empty" => false,
            "defaultValues" => array(),
            "fieldNameAux" => "webEntryEventUid"
        ),

        "EVN_UID" => array(
            "type" => "string",
            "required" => true,
            "empty" => false,
            "defaultValues" => array(),
            "fieldNameAux" => "eventUid"
        ),
        "ACT_UID" => array(
            "type" => "string",
            "required" => true,
            "empty" => false,
            "defaultValues" => array(),
            "fieldNameAux" => "activityUid"
        ),
        "DYN_UID" => array(
            "type" => "string",
            "required" => false,
            "empty" => true,
            "defaultValues" => array(),
            "fieldNameAux" => "dynaFormUid"
        ),
        "USR_UID" => array(
            "type" => "string",
            "required" => false,
            "empty" => true,
            "defaultValues" => array(),
            "fieldNameAux" => "userUid"
        ),

        "WEE_TITLE" => array(
            "type" => "string",
            "required" => false,
            "empty" => true,
            "defaultValues" => array(),
            "fieldNameAux" => "webEntryEventTitle"
        ),
        "WEE_DESCRIPTION" => array(
            "type" => "string",
            "required" => false,
            "empty" => true,
            "defaultValues" => array(),
            "fieldNameAux" => "webEntryEventDescription"
        ),
        "WEE_STATUS" => array(
            "type" => "string",
            "required" => false,
            "empty" => false,
            "defaultValues" => array("ENABLED", "DISABLED"),
            "fieldNameAux" => "webEntryEventStatus"
        ),
        "WE_LINK_SKIN" => array(
            "type" => "string",
            "required" => false,
            "empty" => true,
            "defaultValues" => array(),
            "fieldNameAux" => "webEntryEventSkin"
        ),
        "WE_LINK_LANGUAGE" => array(
            "type" => "string",
            "required" => false,
            "empty" => true,
            "defaultValues" => array(),
            "fieldNameAux" => "webEntryEventLanguage"
        ),
        "WE_LINK_DOMAIN" => array(
            "type" => "string",
            "required" => false,
            "empty" => true,
            "defaultValues" => array(),
            "fieldNameAux" => "webEntryEventDomain"
        ),
    );

    private $formatFieldNameInUppercase = true;

    private $arrayFieldNameForException = array(
        "projectUid" => "PRJ_UID"
    );

    private $webEntryEventWebEntryUid = "";
    private $webEntryEventWebEntryTaskUid = "";

    private $webEntry;

    /**
     * Constructor of the class
     *
     * return void
     */
    public function __construct()
    {
        try {
            $this->webEntry = new \ProcessMaker\BusinessModel\WebEntry();

            foreach ($this->arrayFieldDefinition as $key => $value) {
                $this->arrayFieldNameForException[$value["fieldNameAux"]] = $key;
            }
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Set the format of the fields name (uppercase, lowercase)
     *
     * @param bool $flag Value that set the format
     *
     * @return void
     * @throws Exception
     */
    public function setFormatFieldNameInUppercase($flag)
    {
        try {
            $this->webEntry->setFormatFieldNameInUppercase($flag);

            $this->formatFieldNameInUppercase = $flag;

            $this->setArrayFieldNameForException($this->arrayFieldNameForException);
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Set exception messages for fields
     *
     * @param array $arrayData Data with the fields
     *
     * @return void
     * @throws Exception
     */
    public function setArrayFieldNameForException(array $arrayData)
    {
        try {
            $this->webEntry->setArrayFieldNameForException($arrayData);

            foreach ($arrayData as $key => $value) {
                $this->arrayFieldNameForException[$key] = $this->getFieldNameByFormatFieldName($value);
            }
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Get the name of the field according to the format
     *
     * @param string $fieldName Field name
     *
     * @return string Return the field name according the format
     * @throws Exception
     */
    public function getFieldNameByFormatFieldName($fieldName)
    {
        try {
            return ($this->formatFieldNameInUppercase) ? strtoupper($fieldName) : strtolower($fieldName);
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Verify if exists the WebEntry-Event
     *
     * @param string $webEntryEventUid Unique id of WebEntry-Event
     *
     * @return bool Return true if exists the WebEntry-Event, false otherwise
     * @throws Exception
     */
    public function exists($webEntryEventUid)
    {
        try {
            $obj = WebEntryEventPeer::retrieveByPK($webEntryEventUid);

            return (!is_null($obj)) ? true : false;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Verify if exists the Event of a WebEntry-Event
     *
     * @param string $projectUid Unique id of Project
     * @param string $eventUid Unique id of Event
     * @param string $webEntryEventUidToExclude Unique id of WebEntry-Event to exclude
     *
     * @return bool Return true if exists the Event of a WebEntry-Event, false otherwise
     * @throws Exception
     */
    public function existsEvent($projectUid, $eventUid, $webEntryEventUidToExclude = "")
    {
        try {
            $criteria = new Criteria("workflow");

            $criteria->addSelectColumn(WebEntryEventPeer::WEE_UID);
            $criteria->add(WebEntryEventPeer::PRJ_UID, $projectUid, Criteria::EQUAL);

            if ($webEntryEventUidToExclude != "") {
                $criteria->add(WebEntryEventPeer::WEE_UID, $webEntryEventUidToExclude, Criteria::NOT_EQUAL);
            }

            $criteria->add(WebEntryEventPeer::EVN_UID, $eventUid, Criteria::EQUAL);

            $rsCriteria = WebEntryEventPeer::doSelectRS($criteria);

            return ($rsCriteria->next()) ? true : false;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Verify if exists the title of a WebEntry-Event
     *
     * @param string $projectUid Unique id of Project
     * @param string $webEntryEventTitle Title
     * @param string $webEntryEventUidToExclude Unique id of WebEntry-Event to exclude
     *
     * @return bool Return true if exists the title of a WebEntry-Event, false otherwise
     * @throws Exception
     */
    public function existsTitle($projectUid, $webEntryEventTitle, $webEntryEventUidToExclude = "")
    {
        try {
            $criteria = new Criteria("workflow");

            $criteria->addSelectColumn(WebEntryEventPeer::WEE_UID);
            $criteria->add(WebEntryEventPeer::PRJ_UID, $projectUid, Criteria::EQUAL);

            if ($webEntryEventUidToExclude != "") {
                $criteria->add(WebEntryEventPeer::WEE_UID, $webEntryEventUidToExclude, Criteria::NOT_EQUAL);
            }
            $criteria->add(WebEntryEventPeer::WEE_TITLE, $webEntryEventTitle);
            $rsCriteria = WebEntryEventPeer::doSelectRS($criteria);

            return ($rsCriteria->next()) ? true : false;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Verify if does not exists the WebEntry-Event
     *
     * @param string $webEntryEventUid Unique id of WebEntry-Event
     * @param string $fieldNameForException Field name for the exception
     *
     * @return void Throw exception if does not exists the WebEntry-Event
     * @throws Exception
     */
    public function throwExceptionIfNotExistsWebEntryEvent($webEntryEventUid, $fieldNameForException)
    {
        try {
            if (!$this->exists($webEntryEventUid)) {
                throw new Exception(G::LoadTranslation("ID_WEB_ENTRY_EVENT_DOES_NOT_EXIST",
                    array($fieldNameForException, $webEntryEventUid)));
            }
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Verify if is registered the Event
     *
     * @param string $projectUid Unique id of Project
     * @param string $eventUid Unique id of Event
     * @param string $fieldNameForException Field name for the exception
     * @param string $webEntryEventUidToExclude Unique id of WebEntry-Event to exclude
     *
     * @return void Throw exception if is registered the Event
     * @throws Exception
     */
    public function throwExceptionIfEventIsRegistered(
        $projectUid,
        $eventUid,
        $fieldNameForException,
        $webEntryEventUidToExclude = ""
    ) {
        try {
            if ($this->existsEvent($projectUid, $eventUid, $webEntryEventUidToExclude)) {
                throw new Exception(G::LoadTranslation("ID_WEB_ENTRY_EVENT_ALREADY_REGISTERED",
                    array($fieldNameForException, $eventUid)));
            }
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Verify if exists the title of a WebEntry-Event
     *
     * @param string $projectUid Unique id of Project
     * @param string $webEntryEventTitle Title
     * @param string $fieldNameForException Field name for the exception
     * @param string $webEntryEventUidToExclude Unique id of WebEntry-Event to exclude
     *
     * @return void Throw exception if exists the title of a WebEntry-Event
     * @throws Exception
     */
    public function throwExceptionIfExistsTitle(
        $projectUid,
        $webEntryEventTitle,
        $fieldNameForException,
        $webEntryEventUidToExclude = ""
    ) {
        try {
            if ($this->existsTitle($projectUid, $webEntryEventTitle, $webEntryEventUidToExclude)) {
                throw new Exception(G::LoadTranslation("ID_WEB_ENTRY_EVENT_TITLE_ALREADY_EXISTS",
                    array($fieldNameForException, $webEntryEventTitle)));
            }
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Validate the data if they are invalid (INSERT and UPDATE)
     *
     * @param string $webEntryEventUid Unique id of WebEntry-Event
     * @param string $projectUid Unique id of Project
     * @param array $arrayData Data
     *
     * @return void Throw exception if data has an invalid value
     * @throws Exception
     */
    public function throwExceptionIfDataIsInvalid($webEntryEventUid, $projectUid, array $arrayData)
    {
        try {
            //Set variables
            $arrayWebEntryEventData = ($webEntryEventUid == "") ? array() : $this->getWebEntryEvent($webEntryEventUid,
                true);
            $flagInsert = ($webEntryEventUid == "") ? true : false;

            $arrayFinalData = array_merge($arrayWebEntryEventData, $arrayData);

            //Define the required dependent fields:
            if (!isset($arrayData['WE_TYPE']) || $arrayData['WE_TYPE']
                == 'SINGLE') {
                $this->arrayFieldDefinition['DYN_UID']['required'] = true;
            }
            if (isset($arrayData['WE_LINK_GENERATION']) && $arrayData['WE_LINK_GENERATION']
                == 'ADVANCED') {
                $this->arrayFieldDefinition['WE_LINK_SKIN']['required'] = true;
                $this->arrayFieldDefinition['WE_LINK_LANGUAGE']['required'] = true;
                $this->arrayFieldDefinition['WE_LINK_DOMAIN']['required'] = true;
                $this->arrayFieldDefinition['WE_LINK_SKIN']['empty'] = false;
                $this->arrayFieldDefinition['WE_LINK_LANGUAGE']['empty'] = false;
                $this->arrayFieldDefinition['WE_LINK_DOMAIN']['empty'] = false;
                $modelSkins = new \ProcessMaker\BusinessModel\Skins();
                $skins = [];
                foreach ($modelSkins->getSkins() as $mSkin) {
                    $skins[] = $mSkin['SKIN_FOLDER_ID'];
                }
                $this->arrayFieldDefinition['WE_LINK_SKIN']['defaultValues'] = $skins;
                $modelLanguages = new \ProcessMaker\BusinessModel\Language();
                $languages = [];
                foreach ($modelLanguages->getLanguageList() as $mLang) {
                    $languages[] = $mLang['LANG_ID'];
                }
                $this->arrayFieldDefinition['WE_LINK_LANGUAGE']['defaultValues'] = $languages;
            }

            $process = new BusinessModelProcess();
            $process->throwExceptionIfDataNotMetFieldDefinition($arrayData, $this->arrayFieldDefinition,
                $this->arrayFieldNameForException, $flagInsert);

            //Verify data
            if (isset($arrayData["EVN_UID"])) {
                $this->throwExceptionIfEventIsRegistered($projectUid, $arrayData["EVN_UID"],
                    $this->arrayFieldNameForException["eventUid"], $webEntryEventUid);
            }

            if (isset($arrayData["EVN_UID"])) {
                $obj = \BpmnEventPeer::retrieveByPK($arrayData["EVN_UID"]);

                if (is_null($obj)) {
                    throw new Exception(G::LoadTranslation("ID_EVENT_NOT_EXIST",
                        array($this->arrayFieldNameForException["eventUid"], $arrayData["EVN_UID"])));
                }

                if (($obj->getEvnType() != "START") || ($obj->getEvnType() == "START" && $obj->getEvnMarker() != "EMPTY")) {
                    throw new Exception(G::LoadTranslation("ID_EVENT_NOT_IS_START_EVENT",
                        array($this->arrayFieldNameForException["eventUid"], $arrayData["EVN_UID"])));
                }
            }

            if (isset($arrayData["WEE_TITLE"])) {
                $this->throwExceptionIfExistsTitle($projectUid, $arrayData["WEE_TITLE"],
                    $this->arrayFieldNameForException["webEntryEventTitle"], $webEntryEventUid);
            }

            if (isset($arrayData["ACT_UID"])) {
                $bpmn = new \ProcessMaker\Project\Bpmn();

                if (!$bpmn->activityExists($arrayData["ACT_UID"])) {
                    throw new Exception(G::LoadTranslation("ID_ACTIVITY_DOES_NOT_EXIST",
                        array($this->arrayFieldNameForException["activityUid"], $arrayData["ACT_UID"])));
                }
            }

            if (isset($arrayData["EVN_UID"]) || isset($arrayData["ACT_UID"])) {
                $criteria = new Criteria("workflow");

                $criteria->addSelectColumn(BpmnFlowPeer::FLO_UID);
                $criteria->add(BpmnFlowPeer::FLO_ELEMENT_ORIGIN, $arrayFinalData["EVN_UID"], Criteria::EQUAL);
                $criteria->add(BpmnFlowPeer::FLO_ELEMENT_ORIGIN_TYPE, "bpmnEvent", Criteria::EQUAL);
                $criteria->add(BpmnFlowPeer::FLO_ELEMENT_DEST, $arrayFinalData["ACT_UID"], Criteria::EQUAL);
                $criteria->add(BpmnFlowPeer::FLO_ELEMENT_DEST_TYPE, "bpmnActivity", Criteria::EQUAL);

                $rsCriteria = BpmnFlowPeer::doSelectRS($criteria);

                if (!$rsCriteria->next()) {
                    throw new Exception(G::LoadTranslation("ID_WEB_ENTRY_EVENT_FLOW_EVENT_TO_ACTIVITY_DOES_NOT_EXIST"));
                }
            }

            if (!empty($arrayData["DYN_UID"])) {
                $dynaForm = new \ProcessMaker\BusinessModel\DynaForm();

                $dynaForm->throwExceptionIfNotExistsDynaForm($arrayData["DYN_UID"], $projectUid,
                    $this->arrayFieldNameForException["dynaFormUid"]);
            }

            if (!empty($arrayData["USR_UID"])) {
                $process->throwExceptionIfNotExistsUser($arrayData["USR_UID"],
                    $this->arrayFieldNameForException["userUid"]);
            }

            if ((empty($arrayData["WE_TYPE"]) || $arrayData["WE_TYPE"] === "SINGLE") && empty($arrayData["DYN_UID"])) {
                throw new Exception(G::LoadTranslation("ID_SELECT_DYNAFORM_USE_IN_CASE"));
            }

            if (isset($arrayData["WE_CALLBACK"]) && $arrayData["WE_CALLBACK"] === "CUSTOM" && empty($arrayData["WE_CALLBACK_URL"])) {
                throw new Exception(G::LoadTranslation("ID_ENTER_VALID_URL"));
            }
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Return an UID for the task related to the WebEntryEvent.
     * TAS_UID is based on the EVN_UID to maintain the steps assignation
     * during the import process
     *
     */
    public static function getTaskUidFromEvnUid($eventUid)
    {
        $prefix = "wee-";

        return $prefix . substr($eventUid, (32 - strlen($prefix)) * -1);
    }

    /**
     * Create WebEntry
     *
     * @param string $projectUid Unique id of Project
     * @param string $eventUid Unique id of Event
     * @param string $activityUid Unique id of Activity
     * @param string $dynaFormUid WebEntry, unique id of DynaForm
     * @param string $userUid WebEntry, unique id of User
     * @param string $title WebEntry, title
     * @param string $description WebEntry, description
     * @param string $userUidCreator WebEntry, unique id of creator User
     *
     * @return void
     * @throws Exception
     */
    public function createWebEntry(
        $projectUid,
        $eventUid,
        $activityUid,
        $dynaFormUid,
        $userUid,
        $title,
        $description,
        $userUidCreator,
        $arrayData = []
    ) {
        try {
            $bpmn = new \ProcessMaker\Project\Bpmn();

            $arrayEventData = $bpmn->getEvent($eventUid);

            //Task
            $task = new ModelTask();

            $tasUid = static::getTaskUidFromEvnUid($eventUid);

            if (TaskPeer::retrieveByPK($tasUid)) {
                $this->webEntryEventWebEntryTaskUid = $tasUid;
            } else {
                $this->webEntryEventWebEntryTaskUid = $task->create(
                    array(
                        "TAS_UID" => $tasUid,
                        "PRO_UID" => $projectUid,
                        "TAS_TYPE" => "WEBENTRYEVENT",
                        "TAS_TITLE" => "WEBENTRYEVENT",
                        "TAS_START" => "TRUE",
                        "TAS_POSX" => (int)($arrayEventData["BOU_X"]),
                        "TAS_POSY" => (int)($arrayEventData["BOU_Y"])
                    ),
                    false
                );

                if (!isset($arrayData['WE_TYPE']) || $arrayData['WE_TYPE'] === 'SINGLE') {
                    //Task - Step
                    $step = new Step();

                    $stepUid = $step->create(array(
                        "PRO_UID" => $projectUid,
                        "TAS_UID" => $this->webEntryEventWebEntryTaskUid
                    ));
                    if (!empty($dynaFormUid)) {
                        $result = $step->update(array(
                            "STEP_UID" => $stepUid,
                            "STEP_TYPE_OBJ" => "DYNAFORM",
                            "STEP_UID_OBJ" => $dynaFormUid,
                            "STEP_POSITION" => 1,
                            "STEP_MODE" => "EDIT"
                        ));
                    }
                }

                //Task - User
                $task = new Tasks();
                if (!(isset($arrayData['WE_AUTHENTICATION']) && $arrayData['WE_AUTHENTICATION'] === 'LOGIN_REQUIRED')) {
                    $task->assignUser($this->webEntryEventWebEntryTaskUid, $userUid, 1);
                }

                //Route
                $workflow = Workflow::load($projectUid);

                $result = $workflow->addRoute($this->webEntryEventWebEntryTaskUid, $activityUid, "SEQUENTIAL");

            }
            //WebEntry
            if (isset($arrayData['WE_LINK_GENERATION']) && $arrayData['WE_LINK_GENERATION'] === 'ADVANCED') {
                $arrayData['WE_DATA'] = isset($arrayData['WEE_URL']) ? $arrayData['WEE_URL'] : null;
            }
            $data0 = [];
            foreach ($arrayData as $k => $v) {
                $exists = array_search($k, [
                    'WE_DATA',
                    'WE_TYPE',
                    'WE_CUSTOM_TITLE',
                    'WE_AUTHENTICATION',
                    'WE_HIDE_INFORMATION_BAR',
                    'WE_CALLBACK',
                    'WE_CALLBACK_URL',
                    'WE_LINK_GENERATION',
                    'WE_LINK_SKIN',
                    'WE_LINK_LANGUAGE',
                    'WE_LINK_DOMAIN',
                    'WE_SHOW_IN_NEW_CASE',
                ]);
                if ($exists !== false) {
                    $data0[$k] = $v;
                }
            }
            $data = array_merge($data0, array(
                "TAS_UID" => $this->webEntryEventWebEntryTaskUid,
                "DYN_UID" => $dynaFormUid,
                "USR_UID" => $userUid,
                "WE_TITLE" => $title,
                "WE_DESCRIPTION" => $description,
                "WE_METHOD" => "WS",
                "WE_INPUT_DOCUMENT_ACCESS" => 1
            ));
            $arrayWebEntryData = $this->webEntry->create(
                $projectUid,
                $userUidCreator,
                $data
            );

            $this->webEntryEventWebEntryUid = $arrayWebEntryData[$this->getFieldNameByFormatFieldName("WE_UID")];
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Delete WebEntry
     *
     * @param string $webEntryUid Unique id of WebEntry
     * @param string $webEntryTaskUid WebEntry, unique id of Task
     *
     * @return void
     * @throws Exception
     */
    public function deleteWebEntry($webEntryUid, $webEntryTaskUid)
    {
        try {
            if ($webEntryTaskUid != "") {
                $obj = TaskPeer::retrieveByPK($webEntryTaskUid);

                if (!is_null($obj)) {
                    $task = new Tasks();

                    $task->deleteTask($webEntryTaskUid);
                }
            }

            if ($webEntryUid != "") {
                $obj = WebEntryPeer::retrieveByPK($webEntryUid);

                if (!is_null($obj)) {
                    $this->webEntry->delete($webEntryUid);
                }
            }
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Create WebEntry-Event for a Project
     *
     * @param string $projectUid Unique id of Project
     * @param string $userUidCreator Unique id of creator User
     * @param array $arrayData Data
     *
     * @return array Return data of the new WebEntry-Event created
     * @throws Exception
     */
    public function create($projectUid, $userUidCreator, array $arrayData)
    {
        try {
            //Verify data
            $process = new BusinessModelProcess();
            $validator = new BusinessModelValidator();

            $validator->throwExceptionIfDataIsNotArray($arrayData, "\$arrayData");
            $validator->throwExceptionIfDataIsEmpty($arrayData, "\$arrayData");

            //Set data
            $arrayData = array_change_key_case($arrayData, CASE_UPPER);

            unset($arrayData["WEE_UID"]);
            unset($arrayData["PRJ_UID"]);
            unset($arrayData["WEE_WE_UID"]);
            unset($arrayData["WEE_WE_TAS_UID"]);
            if (empty($arrayData["WE_LINK_SKIN"])) {
                unset($arrayData["WE_LINK_SKIN"]);
            }
            if (empty($arrayData["WE_LINK_LANGUAGE"])) {
                unset($arrayData["WE_LINK_LANGUAGE"]);
            }

            if (!isset($arrayData["WEE_DESCRIPTION"])) {
                $arrayData["WEE_DESCRIPTION"] = "";
            }

            if (!isset($arrayData["WEE_STATUS"])) {
                $arrayData["WEE_STATUS"] = "ENABLED";
            }

            if (!array_key_exists('USR_UID', $arrayData)) {
                $arrayData['USR_UID'] = null;
            }

            if (!isset($arrayData["WEE_TITLE"])) {
                $arrayData["WEE_TITLE"] = null;
            }

            //Verify data related to the process
            $process->throwExceptionIfNotExistsProcess($projectUid, $this->arrayFieldNameForException["projectUid"]);
            //Define if the webEntry need to use the guest user
            $weUserUid = isset($arrayData["USR_UID"]) ? $arrayData["USR_UID"] : '';
            $weAuthentication = isset($arrayData["WE_AUTHENTICATION"]) ? $arrayData["WE_AUTHENTICATION"] : '';
            $arrayData["USR_UID"] = $this->getWebEntryUser($weAuthentication, $weUserUid);
            //Verify data with the required fields
            $this->throwExceptionIfDataIsInvalid("", $projectUid, $arrayData);

            $this->webEntryEventWebEntryUid = "";
            $this->webEntryEventWebEntryTaskUid = "";
            //Create the connection
            $cnn = Propel::getConnection("workflow");
            try {
                //WebEntry
                $this->createWebEntry(
                    $projectUid,
                    $arrayData["EVN_UID"],
                    $arrayData["ACT_UID"],
                    empty($arrayData["DYN_UID"]) ? null : $arrayData["DYN_UID"],
                    $arrayData["USR_UID"],
                    $arrayData["WEE_TITLE"],
                    $arrayData["WEE_DESCRIPTION"],
                    $userUidCreator,
                    $arrayData
                );

                //WebEntry-Event
                $webEntryEvent = new ModelWebEntryEvent();

                $webEntryEvent->fromArray($arrayData, BasePeer::TYPE_FIELDNAME);

                $webEntryEventUid = Common::generateUID();

                $webEntryEvent->setWeeUid($webEntryEventUid);
                $webEntryEvent->setPrjUid($projectUid);
                $webEntryEvent->setWeeWeUid($this->webEntryEventWebEntryUid);
                $webEntryEvent->setWeeWeTasUid($this->webEntryEventWebEntryTaskUid);

                if ($webEntryEvent->validate()) {
                    $cnn->begin();

                    $result = $webEntryEvent->save();

                    $cnn->commit();

                    //Set WEE_TITLE
                    if (isset($arrayData["WEE_TITLE"])) {
                        $result = Content::addContent("WEE_TITLE", "", $webEntryEventUid, SYS_LANG,
                            $arrayData["WEE_TITLE"]);
                    }

                    //Set WEE_DESCRIPTION
                    if (isset($arrayData["WEE_DESCRIPTION"])) {
                        $result = Content::addContent("WEE_DESCRIPTION", "", $webEntryEventUid, SYS_LANG,
                            $arrayData["WEE_DESCRIPTION"]);
                    }

                    //Return
                    return $this->getWebEntryEvent($webEntryEventUid);
                } else {
                    $msg = "";

                    foreach ($webEntryEvent->getValidationFailures() as $validationFailure) {
                        $msg = $msg . (($msg != "") ? "\n" : "") . $validationFailure->getMessage();
                    }

                    throw new Exception(G::LoadTranslation("ID_RECORD_CANNOT_BE_CREATED") . (($msg != "") ? "\n" . $msg : ""));
                }
            } catch (Exception $e) {
                $cnn->rollback();

                $this->deleteWebEntry($this->webEntryEventWebEntryUid, $this->webEntryEventWebEntryTaskUid);

                throw $e;
            }
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Update WebEntry-Event
     *
     * @param string $webEntryEventUid Unique id of WebEntry-Event
     * @param string $userUidUpdater Unique id of updater User
     * @param array $arrayData Data
     *
     * @return array Return data of the WebEntry-Event updated
     * @throws Exception
     */
    public function update($webEntryEventUid, $userUidUpdater, array $arrayData, $updateUser = true)
    {
        try {
            //Verify data
            $process = new BusinessModelProcess();
            $validator = new BusinessModelValidator();

            $validator->throwExceptionIfDataIsNotArray($arrayData, "\$arrayData");
            $validator->throwExceptionIfDataIsEmpty($arrayData, "\$arrayData");

            //Set data
            $arrayData = array_change_key_case($arrayData, CASE_UPPER);
            $arrayDataBackup = $arrayData;

            unset($arrayData["WEE_UID"]);
            unset($arrayData["PRJ_UID"]);
            unset($arrayData["WEE_WE_UID"]);
            unset($arrayData["WEE_WE_TAS_UID"]);

            //Set variables
            $arrayWebEntryEventData = $this->getWebEntryEvent($webEntryEventUid, true);

            $arrayFinalData = array_merge($arrayWebEntryEventData, $arrayData);

            //Verify data related to the process
            $this->throwExceptionIfNotExistsWebEntryEvent($webEntryEventUid,
                $this->arrayFieldNameForException["webEntryEventUid"]);
            //Define if the webEntry need to use the guest user
            $weUserUid = isset($arrayData["USR_UID"]) ? $arrayData["USR_UID"] : '';
            $weAuthentication = isset($arrayData["WE_AUTHENTICATION"]) ? $arrayData["WE_AUTHENTICATION"] : '';
            if ($updateUser) {
                $arrayData["USR_UID"] = $this->getWebEntryUser($weAuthentication, $weUserUid);
            }
            //Verify data with the required fields
            $this->throwExceptionIfDataIsInvalid($webEntryEventUid, $arrayWebEntryEventData["PRJ_UID"], $arrayData);

            //Update
            $cnn = Propel::getConnection("workflow");

            $this->webEntryEventWebEntryUid = "";
            $this->webEntryEventWebEntryTaskUid = "";

            try {
                //WebEntry
                if ($arrayWebEntryEventData["WEE_WE_UID"] != "") {
                    $task = new Tasks();

                    //Task - Step for WE_TYPE=SINGLE
                    $weType = !empty($arrayData["WE_TYPE"]) ? $arrayData["WE_TYPE"] : $arrayWebEntryEventData["WE_TYPE"];
                    if (isset($arrayData["DYN_UID"]) && $arrayData["DYN_UID"] !== $arrayWebEntryEventData["DYN_UID"] && $weType === 'SINGLE') {
                        //Delete
                        $step = new Step();

                        $criteria = new Criteria("workflow");

                        $criteria->add(StepPeer::TAS_UID, $arrayWebEntryEventData["WEE_WE_TAS_UID"]);

                        $rsCriteria = StepPeer::doSelectRS($criteria);
                        $rsCriteria->setFetchmode(ResultSet::FETCHMODE_ASSOC);

                        while ($rsCriteria->next()) {
                            $row = $rsCriteria->getRow();

                            $result = $step->remove($row["STEP_UID"]);
                        }

                        //Add
                        $step = new Step();

                        $stepUid = $step->create(array(
                            "PRO_UID" => $arrayWebEntryEventData["PRJ_UID"],
                            "TAS_UID" => $arrayWebEntryEventData["WEE_WE_TAS_UID"]
                        ));
                        $result = $step->update(array(
                            "STEP_UID" => $stepUid,
                            "STEP_TYPE_OBJ" => "DYNAFORM",
                            "STEP_UID_OBJ" => $arrayData["DYN_UID"],
                            "STEP_POSITION" => 1,
                            "STEP_MODE" => "EDIT"
                        ));
                    }

                    //Task - User
                    $proUser = new ProjectUser();
                    $newUser = !empty($arrayData["USR_UID"]) ? $arrayData["USR_UID"] : "";
                    $oldUser = $arrayWebEntryEventData["USR_UID"];
                    $isAssigned = $proUser->userIsAssignedToTask($newUser, $arrayWebEntryEventData["WEE_WE_TAS_UID"]);
                    $shouldUpdate = !empty($newUser) && ($newUser !== $oldUser || !$isAssigned);
                    if ($shouldUpdate) {
                        //Unassign
                        $taskUser = new TaskUser();

                        $criteria = new Criteria("workflow");

                        $criteria->add(TaskUserPeer::TAS_UID, $arrayWebEntryEventData["WEE_WE_TAS_UID"]);

                        $rsCriteria = TaskUserPeer::doSelectRS($criteria);
                        $rsCriteria->setFetchmode(ResultSet::FETCHMODE_ASSOC);

                        while ($rsCriteria->next()) {
                            $row = $rsCriteria->getRow();

                            $result = $taskUser->remove($row["TAS_UID"], $row["USR_UID"], $row["TU_TYPE"],
                                $row["TU_RELATION"]);
                        }

                        //Assign
                        $result = $task->assignUser(
                            $arrayWebEntryEventData["WEE_WE_TAS_UID"],
                            $arrayData["USR_UID"],
                            1
                        );
                    }

                    //Route
                    if (array_key_exists('ACT_UID', $arrayData)) {
                        if ($arrayData['ACT_UID'] != $arrayWebEntryEventData['ACT_UID']) {
                            //Delete
                            $result = $task->deleteAllRoutesOfTask(
                                $arrayWebEntryEventData['PRJ_UID'], $arrayWebEntryEventData['WEE_WE_TAS_UID'], true
                            );
                        }

                        //Add
                        $workflow = Workflow::load($arrayWebEntryEventData["PRJ_UID"]);

                        $result = $workflow->addRoute($arrayWebEntryEventData["WEE_WE_TAS_UID"], $arrayData["ACT_UID"],
                            "SEQUENTIAL");
                    }

                    //WebEntry
                    $arrayDataAux = array();
                    $webEntryMap = [
                        'DYN_UID' => 'DYN_UID',
                        'USR_UID' => 'USR_UID',
                        'WE_TYPE' => 'WE_TYPE',
                        'WE_TITLE' => 'WEE_TITLE',
                        'WE_DESCRIPTION' => 'WEE_DESCRIPTION',
                        'WE_CUSTOM_TITLE' => 'WE_CUSTOM_TITLE',
                        'WE_AUTHENTICATION' => 'WE_AUTHENTICATION',
                        'WE_HIDE_INFORMATION_BAR' => 'WE_HIDE_INFORMATION_BAR',
                        'WE_CALLBACK' => 'WE_CALLBACK',
                        'WE_CALLBACK_URL' => 'WE_CALLBACK_URL',
                        'WE_LINK_GENERATION' => 'WE_LINK_GENERATION',
                        'WE_LINK_SKIN' => 'WE_LINK_SKIN',
                        'WE_LINK_LANGUAGE' => 'WE_LINK_LANGUAGE',
                        'WE_LINK_DOMAIN' => 'WE_LINK_DOMAIN',
                        'WE_DATA' => 'WEE_URL',
                        'WE_SHOW_IN_NEW_CASE' => 'WE_SHOW_IN_NEW_CASE',
                    ];
                    foreach ($webEntryMap as $k => $v) {
                        if (array_key_exists($v, $arrayData)) {
                            $arrayDataAux[$k] = $arrayData[$v];
                        }
                    }

                    if (count($arrayDataAux) > 0) {
                        $arrayDataAux = $this->webEntry->update(
                            $arrayWebEntryEventData["WEE_WE_UID"],
                            $userUidUpdater,
                            $arrayDataAux
                        );
                    }
                }

                //WebEntry-Event
                $webEntryEvent = WebEntryEventPeer::retrieveByPK($webEntryEventUid);

                $webEntryEvent->fromArray($arrayData, BasePeer::TYPE_FIELDNAME);

                if ($webEntryEvent->validate()) {
                    $cnn->begin();

                    $result = $webEntryEvent->save();

                    $cnn->commit();

                    //Set WEE_TITLE
                    if (isset($arrayData["WEE_TITLE"])) {
                        $result = Content::addContent("WEE_TITLE", "", $webEntryEventUid, SYS_LANG,
                            $arrayData["WEE_TITLE"]);
                    }

                    //Set WEE_DESCRIPTION
                    if (isset($arrayData["WEE_DESCRIPTION"])) {
                        $result = Content::addContent("WEE_DESCRIPTION", "", $webEntryEventUid, SYS_LANG,
                            $arrayData["WEE_DESCRIPTION"]);
                    }

                    //Return
                    $arrayData = $arrayDataBackup;

                    if (!$this->formatFieldNameInUppercase) {
                        $arrayData = array_change_key_case($arrayData, CASE_LOWER);
                    }

                    return $arrayData;
                } else {
                    $msg = "";

                    foreach ($webEntryEvent->getValidationFailures() as $validationFailure) {
                        $msg = $msg . (($msg != "") ? "\n" : "") . $validationFailure->getMessage();
                    }

                    throw new Exception(G::LoadTranslation("ID_REGISTRY_CANNOT_BE_UPDATED") . (($msg != "") ? "\n" . $msg : ""));
                }
            } catch (Exception $e) {
                $cnn->rollback();

                $this->deleteWebEntry($this->webEntryEventWebEntryUid, $this->webEntryEventWebEntryTaskUid);

                throw $e;
            }
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Delete WebEntry-Event
     *
     * @param string $webEntryEventUid Unique id of WebEntry-Event
     *
     * @return void
     * @throws Exception
     */
    public function delete($webEntryEventUid)
    {
        try {
            //Verify data
            $this->throwExceptionIfNotExistsWebEntryEvent($webEntryEventUid,
                $this->arrayFieldNameForException["webEntryEventUid"]);

            //Set variables
            $arrayWebEntryEventData = $this->getWebEntryEvent($webEntryEventUid, true);

            //Delete WebEntry
            $this->deleteWebEntry($arrayWebEntryEventData["WEE_WE_UID"], $arrayWebEntryEventData["WEE_WE_TAS_UID"]);

            //Delete WebEntry-Event
            $criteria = new Criteria("workflow");

            $criteria->add(WebEntryEventPeer::WEE_UID, $webEntryEventUid, Criteria::EQUAL);

            $result = WebEntryEventPeer::doDelete($criteria);
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Get criteria for WebEntry-Event
     *
     * @category webentry2,PROD-181,webentry1
     * @link https://processmaker.atlassian.net/browse/PROD-181 Web Entry 2 Feature definition
     * @link URL description https://processmaker.atlassian.net/browse/PROD-1 Web Entry 1 Feature definition
     * @group webentry2
     * @return Criteria
     * @throws Exception
     */
    public function getWebEntryEventCriteria()
    {
        try {
            $criteria = new Criteria("workflow");
            $criteria->addSelectColumn(WebEntryEventPeer::WEE_UID);
            $criteria->addSelectColumn(WebEntryEventPeer::PRJ_UID);
            $criteria->addSelectColumn(WebEntryEventPeer::EVN_UID);
            $criteria->addSelectColumn(WebEntryEventPeer::ACT_UID);
            $criteria->addSelectColumn(WebEntryEventPeer::DYN_UID);
            $criteria->addSelectColumn(WebEntryEventPeer::USR_UID);
            $criteria->addSelectColumn(WebEntryEventPeer::WEE_TITLE);
            $criteria->addSelectColumn(WebEntryEventPeer::WEE_DESCRIPTION);
            $criteria->addSelectColumn(WebEntryEventPeer::WEE_STATUS);
            $criteria->addSelectColumn(WebEntryEventPeer::WEE_WE_UID);
            $criteria->addSelectColumn(WebEntryEventPeer::WEE_WE_TAS_UID);
            $criteria->addSelectColumn(WebEntryPeer::WE_DATA . " AS WEE_WE_URL");
            $criteria->addSelectColumn(WebEntryPeer::WE_CUSTOM_TITLE);
            $criteria->addSelectColumn(WebEntryPeer::WE_TYPE);
            $criteria->addSelectColumn(WebEntryPeer::WE_AUTHENTICATION);
            $criteria->addSelectColumn(WebEntryPeer::WE_HIDE_INFORMATION_BAR);
            $criteria->addSelectColumn(WebEntryPeer::WE_CALLBACK);
            $criteria->addSelectColumn(WebEntryPeer::WE_CALLBACK_URL);
            $criteria->addSelectColumn(WebEntryPeer::WE_LINK_GENERATION);
            $criteria->addSelectColumn(WebEntryPeer::WE_LINK_SKIN);
            $criteria->addSelectColumn(WebEntryPeer::WE_LINK_LANGUAGE);
            $criteria->addSelectColumn(WebEntryPeer::WE_LINK_DOMAIN);
            $criteria->addSelectColumn(WebEntryPeer::TAS_UID);
            $criteria->addSelectColumn(WebEntryPeer::WE_SHOW_IN_NEW_CASE);
            $criteria->addJoin(WebEntryEventPeer::WEE_WE_UID, WebEntryPeer::WE_UID, Criteria::LEFT_JOIN);

            return $criteria;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Get data of a WebEntry-Event from a record
     *
     * @param array $record Record
     *
     * @return array Return an array with data WebEntry-Event
     * @throws Exception
     */
    public function getWebEntryEventDataFromRecord(array $record)
    {
        try {
            if ($record["WEE_WE_UID"] . "" != "") {
                $record["WEE_WE_URL"] = $this->getGeneratedLink(
                    $record["WEE_UID"], $record["PRJ_UID"],
                    $record["WE_LINK_GENERATION"], $record["WE_LINK_DOMAIN"],
                    $record["WE_LINK_LANGUAGE"], $record["WE_LINK_SKIN"],
                    $record["WEE_WE_URL"]
                );
            }

            return array(
                $this->getFieldNameByFormatFieldName("WEE_UID") => $record["WEE_UID"],
                $this->getFieldNameByFormatFieldName("EVN_UID") => $record["EVN_UID"],
                $this->getFieldNameByFormatFieldName("ACT_UID") => $record["ACT_UID"],
                $this->getFieldNameByFormatFieldName("DYN_UID") => $record["DYN_UID"],
                $this->getFieldNameByFormatFieldName("USR_UID") => $record["USR_UID"],
                $this->getFieldNameByFormatFieldName("WEE_TITLE") => $record["WEE_TITLE"],
                $this->getFieldNameByFormatFieldName("WEE_DESCRIPTION") => $record["WEE_DESCRIPTION"] . "",
                $this->getFieldNameByFormatFieldName("WEE_STATUS") => $record["WEE_STATUS"] . "",
                $this->getFieldNameByFormatFieldName("WEE_URL") => $record["WEE_WE_URL"] . "",
                $this->getFieldNameByFormatFieldName("WE_TYPE") => $record["WE_TYPE"],
                $this->getFieldNameByFormatFieldName("WE_CUSTOM_TITLE") => $record["WE_CUSTOM_TITLE"],
                $this->getFieldNameByFormatFieldName("WE_AUTHENTICATION") => $record["WE_AUTHENTICATION"],
                $this->getFieldNameByFormatFieldName("WE_HIDE_INFORMATION_BAR") => $record["WE_HIDE_INFORMATION_BAR"],
                $this->getFieldNameByFormatFieldName("WE_CALLBACK") => $record["WE_CALLBACK"],
                $this->getFieldNameByFormatFieldName("WE_CALLBACK_URL") => $record["WE_CALLBACK_URL"],
                $this->getFieldNameByFormatFieldName("WE_LINK_GENERATION") => $record["WE_LINK_GENERATION"],
                $this->getFieldNameByFormatFieldName("WE_LINK_SKIN") => $record["WE_LINK_SKIN"],
                $this->getFieldNameByFormatFieldName("WE_LINK_LANGUAGE") => $record["WE_LINK_LANGUAGE"],
                $this->getFieldNameByFormatFieldName("WE_LINK_DOMAIN") => $record["WE_LINK_DOMAIN"],
                $this->getFieldNameByFormatFieldName("WE_SHOW_IN_NEW_CASE") => $record["WE_SHOW_IN_NEW_CASE"],
                $this->getFieldNameByFormatFieldName("TAS_UID") => $record["TAS_UID"],
            );
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Get all WebEntry-Events
     *
     * @param string $projectUid Unique id of Project
     *
     * @return array Return an array with all WebEntry-Events
     * @throws Exception
     */
    public function getWebEntryEvents($projectUid)
    {
        try {
            $arrayWebEntryEvent = array();

            //Verify data
            $process = new BusinessModelProcess();

            $process->throwExceptionIfNotExistsProcess($projectUid, $this->arrayFieldNameForException["projectUid"]);

            //Get data
            $criteria = $this->getWebEntryEventCriteria();

            $criteria->add(WebEntryEventPeer::PRJ_UID, $projectUid, Criteria::EQUAL);

            $rsCriteria = WebEntryEventPeer::doSelectRS($criteria);
            $rsCriteria->setFetchmode(ResultSet::FETCHMODE_ASSOC);

            while ($rsCriteria->next()) {
                $row = $rsCriteria->getRow();

                $arrayWebEntryEvent[] = $this->getWebEntryEventDataFromRecord($row);
            }

            //Return
            return $arrayWebEntryEvent;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Get all WebEntry-Events
     * Return an array with all WebEntry-Events
     * @param boolean $considerShowInCase
     * @return array
     * @throws Exception
     */
    public function getAllWebEntryEvents($considerShowInCase = false)
    {
        try {
            $result = array();
            $criteria = $this->getWebEntryEventCriteria();
            $criteria->addJoin(
                WebEntryEventPeer::PRJ_UID,
                ProcessPeer::PRO_UID,
                Criteria::JOIN
            );
            if ($considerShowInCase) {
                $criteria->add(
                    WebEntryPeer::WE_SHOW_IN_NEW_CASE,
                    "0",
                    Criteria::EQUAL
                );
            }
            $criteria->add(ProcessPeer::PRO_STATUS, 'ACTIVE', Criteria::EQUAL);
            $rsCriteria = WebEntryEventPeer::doSelectRS($criteria);
            $rsCriteria->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            while ($rsCriteria->next()) {
                $row = $rsCriteria->getRow();
                $result[] = $this->getWebEntryEventDataFromRecord($row);
            }

            return $result;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Get data of a WebEntry-Event
     *
     * @param string $webEntryEventUid Unique id of WebEntry-Event
     * @param bool $flagGetRecord Value that set the getting
     *
     * @return array Return an array with data of a WebEntry-Event
     * @throws Exception
     */
    public function getWebEntryEvent($webEntryEventUid, $flagGetRecord = false)
    {
        try {
            //Verify data
            $this->throwExceptionIfNotExistsWebEntryEvent($webEntryEventUid,
                $this->arrayFieldNameForException["webEntryEventUid"]);

            //Get data
            $criteria = $this->getWebEntryEventCriteria();

            $criteria->add(WebEntryEventPeer::WEE_UID, $webEntryEventUid, Criteria::EQUAL);

            $rsCriteria = WebEntryEventPeer::doSelectRS($criteria);
            $rsCriteria->setFetchmode(ResultSet::FETCHMODE_ASSOC);

            $rsCriteria->next();

            $row = $rsCriteria->getRow();

            //Return
            return (!$flagGetRecord) ? $this->getWebEntryEventDataFromRecord($row) : $row;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Get data of a WebEntry-Event by unique id of Event
     *
     * @param string $projectUid Unique id of Project
     * @param string $eventUid Unique id of Event
     * @param bool $flagGetRecord Value that set the getting
     *
     * @return array Return an array with data of a WebEntry-Event by unique id of Event
     * @throws Exception
     */
    public function getWebEntryEventByEvent($projectUid, $eventUid, $flagGetRecord = false)
    {
        try {
            //Verify data
            $process = new BusinessModelProcess();

            $process->throwExceptionIfNotExistsProcess($projectUid, $this->arrayFieldNameForException["projectUid"]);

            if (!$this->existsEvent($projectUid, $eventUid)) {
                throw new Exception(G::LoadTranslation("ID_WEB_ENTRY_EVENT_DOES_NOT_IS_REGISTERED",
                    array($this->arrayFieldNameForException["eventUid"], $eventUid)));
            }

            //Get data
            $criteria = $this->getWebEntryEventCriteria();

            $criteria->add(WebEntryEventPeer::PRJ_UID, $projectUid, Criteria::EQUAL);
            $criteria->add(WebEntryEventPeer::EVN_UID, $eventUid, Criteria::EQUAL);

            $rsCriteria = WebEntryEventPeer::doSelectRS($criteria);
            $rsCriteria->setFetchmode(ResultSet::FETCHMODE_ASSOC);

            $rsCriteria->next();

            $row = $rsCriteria->getRow();

            //Return
            return (!$flagGetRecord) ? $this->getWebEntryEventDataFromRecord($row) : $row;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * This function verify if a user $userUid was configure in a Web Entry and return the total of records
     *
     * @param string $userUid uid of a user
     *
     * @return integer $total
     * @throws Exception
     */
    public function getWebEntryRelatedToUser($userUid)
    {
        try {
            //Get data
            $criteria = $this->getWebEntryEventCriteria();
            $criteria->add(WebEntryEventPeer::USR_UID, $userUid, Criteria::EQUAL);
            $total = WebEntryEventPeer::doCount($criteria);

            return $total;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Web Entry 2.0 Rest - Generate Link EP
     *
     * @category PROD-181,HOR-3210
     * @link https://processmaker.atlassian.net/browse/PROD-181 Web Entry 2 Feature definition
     * @link https://processmaker.atlassian.net/browse/HOR-3210 Generate link specification
     * @group webentry2
     */
    public function generateLink($prj_uid, $wee_uid)
    {
        $webEntryEvent = WebEntryEventPeer::retrieveByPK($wee_uid);
        if (empty($webEntryEvent)) {
            throw new Exception(
                G::LoadTranslation("ID_WEB_ENTRY_EVENT_DOES_NOT_EXIST", array("WEE_UID", $wee_uid))
            );
        }
        $webEntry = WebEntryPeer::retrieveByPK($webEntryEvent->getWeeWeUid());
        if (empty($webEntryEvent)) {
            throw new Exception(
                G::LoadTranslation(
                    "ID_WEB_ENTRY_EVENT_DOES_NOT_EXIST",
                    array("WE_UID", $webEntryEvent->getWeeWeUid())
                )
            );
        }

        return $this->getGeneratedLink(
            $webEntry->getWeUid(),
            $prj_uid,
            $webEntry->getWeLinkGeneration(),
            $webEntry->getWeLinkDomain(),
            $webEntry->getWeLinkLanguage(),
            $webEntry->getWeLinkSkin(),
            $webEntry->getWeData()
        );
    }

    /**
     * Get the WebEntry URL.
     *
     * @param string $weUid
     * @param string $weLinkGeneration
     * @param string $weLinkDomain
     * @param string $weLinkLanguage
     * @param string $weLinkSkin
     * @param string $weData
     * @return string
     */
    private function getGeneratedLink(
        $weUid,
        $prj_uid,
        $weLinkGeneration,
        $weLinkDomain,
        $weLinkLanguage,
        $weLinkSkin,
        $weData
    ) {
        $http = (G::is_https()) ? "https://" : "http://";
        if ($weLinkGeneration === 'ADVANCED') {
            $domain = $weLinkDomain;
            $hasProtocol = strpos($domain, 'http://') === 0 ||
                strpos($domain, 'https://') === 0;
            $url = ($hasProtocol ? '' : $http) .
                $domain .
                "/sys" . SYS_SYS . "/" .
                $weLinkLanguage . "/" .
                $weLinkSkin . "/" . $prj_uid;

            return $url . "/" . $weData;
        } else {
            $url = $http . $_SERVER["HTTP_HOST"] . "/sys" . SYS_SYS . "/" . SYS_LANG . "/" . SYS_SKIN . "/" . $prj_uid;

            return $url . "/" . $weData;
        }
    }

    /**
     * This function return the uid of user related to the webEntry
     * @param string $authentication, can be ANONYMOUS, LOGIN_REQUIRED
     * @param string $usrUid
     * @return string
    */
    public function getWebEntryUser($authentication = 'ANONYMOUS', $usrUid = '')
    {
        //The webEntry old does not have type of authentication defined
        //The webEntry2.0 can be has values ANONYMOUS or LOGIN_REQUIRED
        if ($authentication === 'ANONYMOUS' || empty($authentication)) {
            $user = new User();
            return $user->getGuestUser();
        } else {
            return $usrUid;
        }
    }
}
