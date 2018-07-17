<?php
//require_once 'classes/model/om/BaseTask.php';
//require_once 'classes/model/Content.php';


/**
 * Skeleton subclass for representing a row from the 'TASK' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    workflow.engine.classes.model
 */
class Task extends BaseTask
{
    /**
     * This value goes in the content table
     * @var        string
     */
    protected $tas_title_content = '';

    /**
     * Get the tas_title column value.
     * @return     string
     */
    public function getTasTitleContent()
    {
        if ($this->getTasUid() == "") {
            throw (new Exception("Error in getTasTitle, the getTasUid() can't be blank"));
        }

        $lang = defined('SYS_LANG')? SYS_LANG : 'en';
        $this->tas_title_content = Content::load('TAS_TITLE', '', $this->getTasUid(), $lang);

        return $this->tas_title_content;
    }

    /**
     * Set the tas_title column value.
     *
     * @param      string $v new value
     * @return     void
     */
    public function setTasTitleContent($v)
    {
        if ($this->getTasUid() == "") {
            throw (new Exception("Error in setTasTitle, the getTasUid() can't be blank"));
        }

        $v = isset($v)? ((string)$v) : '';
        $lang = defined('SYS_LANG')? SYS_LANG : 'en';

        if (in_array(TaskPeer::TAS_TITLE, $this->modifiedColumns) || $v === "") {
            $this->tas_title_content = $v;

            $res = Content::addContent('TAS_TITLE', '', $this->getTasUid(), $lang, $this->tas_title_content);

            return $res;
        }

        return 0;
    }

    /**
     * This value goes in the content table
     * @var        string
     */
    protected $tas_description_content = '';

    /**
     * Get the tas_description column value.
     * @return     string
     */
    public function getTasDescriptionContent()
    {
        if ($this->getTasUid() == "") {
            throw (new Exception( "Error in getTasDescription, the getTasUid() can't be blank"));
        }

        $lang = defined('SYS_LANG')? SYS_LANG : 'en';
        $this->tas_description_content = Content::load('TAS_DESCRIPTION', '', $this->getTasUid(), $lang);

        return $this->tas_description_content;
    }

    /**
     * Set the tas_description column value.
     *
     * @param      string $v new value
     * @return     void
     */
    public function setTasDescriptionContent($v)
    {
        if ($this->getTasUid() == "") {
            throw (new Exception("Error in setTasDescription, the getTasUid() can't be blank"));
        }

        $v = isset($v)? ((string)$v) : '';
        $lang = defined('SYS_LANG')? SYS_LANG : 'en';

        if (in_array(TaskPeer::TAS_DESCRIPTION, $this->modifiedColumns) || $v === "") {
            $this->tas_description_content = $v;

            $res = Content::addContent('TAS_DESCRIPTION', '', $this->getTasUid(), $lang, $this->tas_description_content);

            return $res;
        }

        return 0;
    }

    /**
     * This value goes in the content table
     * @var        string
     */
    protected $tas_def_title_content = '';

    /**
     * Get the tas_def_title column value.
     * @return     string
     */
    public function getTasDefTitleContent()
    {
        if ($this->getTasUid() == "") {
            throw (new Exception( "Error in getTasDefTitle, the getTasUid() can't be blank"));
        }

        $lang = defined('SYS_LANG')? SYS_LANG : 'en';
        $this->tas_def_title_content = Content::load('TAS_DEF_TITLE', '', $this->getTasUid(), $lang);

        return $this->tas_def_title_content;
    }

    /**
     * Set the tas_def_title column value.
     *
     * @param      string $v new value
     * @return     void
     */
    public function setTasDefTitleContent($v)
    {
        if ($this->getTasUid() == "") {
            throw (new Exception("Error in setTasDefTitle, the getTasUid() can't be blank"));
        }

        $v = isset($v)? ((string)$v) : '';
        $lang = defined('SYS_LANG')? SYS_LANG : 'en';

        if (in_array(TaskPeer::TAS_DEF_TITLE, $this->modifiedColumns) || $v === "") {
            $this->tas_def_title_content = $v;

            $res = Content::addContent('TAS_DEF_TITLE', '', $this->getTasUid(), $lang, $this->tas_def_title_content);

            return $res;
        }

        return 0;
    }

    /**
     * This value goes in the content table
     * @var        string
     */
    protected $tas_def_description_content = '';

    /**
     * Get the tas_def_description column value.
     * @return     string
     */
    public function getTasDefDescriptionContent()
    {
        if ($this->getTasUid() == "") {
            throw (new Exception( "Error in getTasDefDescription, the getTasUid() can't be blank"));
        }

        $lang = defined('SYS_LANG')? SYS_LANG : 'en';
        $this->tas_def_description_content = Content::load('TAS_DEF_DESCRIPTION', '', $this->getTasUid(), $lang);

        return $this->tas_def_description_content;
    }

    /**
     * Set the tas_def_description column value.
     *
     * @param      string $v new value
     * @return     void
     */
    public function setTasDefDescriptionContent($v)
    {
        if ($this->getTasUid() == "") {
            throw (new Exception("Error in setTasDefDescription, the getTasUid() can't be blank"));
        }

        $v = isset($v)? ((string)$v) : '';
        $lang = defined('SYS_LANG')? SYS_LANG : 'en';

        if (in_array(TaskPeer::TAS_DEF_DESCRIPTION, $this->modifiedColumns) || $v === "") {
            $this->tas_def_description_content = $v;

            $res = Content::addContent('TAS_DEF_DESCRIPTION', '', $this->getTasUid(), $lang, $v);
            return $res;
        }

        return 0;
    }

    /**
     * This value goes in the content table
     * @var        string
     */
    protected $tas_def_proc_code_content = '';

    /**
     * Get the tas_def_proc_code column value.
     * @return     string
     */
    public function getTasDefProcCodeContent()
    {
        if ($this->getTasUid() == "") {
            throw (new Exception( "Error in getTasDefProcCode, the getTasUid() can't be blank"));
        }

        $lang = defined('SYS_LANG')? SYS_LANG : 'en';
        $this->tas_def_proc_code_content = Content::load('TAS_DEF_PROC_CODE', '', $this->getTasUid(), $lang);

        return $this->tas_def_proc_code_content;
    }

    /**
     * Set the tas_def_proc_code column value.
     *
     * @param      string $v new value
     * @return     void
     */
    public function setTasDefProcCodeContent($v)
    {
        if ($this->getTasUid() == "") {
            throw (new Exception("Error in setTasDefProcCode, the getTasUid() can't be blank"));
        }

        $v = isset($v)? ((string)$v) : '';
        $lang = defined('SYS_LANG')? SYS_LANG : 'en';

        if (in_array(TaskPeer::TAS_DEF_PROC_CODE, $this->modifiedColumns) || $v === "") {
            $this->tas_def_proc_code_content = $v;

            $res = Content::addContent('TAS_DEF_PROC_CODE', '', $this->getTasUid(), $lang, $this->tas_def_proc_code_content);

            return $res;
        }

        return 0;
    }

    /**
     * This value goes in the content table
     * @var        string
     */
    protected $tas_def_message_content = '';

    /**
     * Get the tas_def_message column value.
     * @return     string
     */
    public function getTasDefMessageContent()
    {
        if ($this->getTasUid() == "") {
            throw (new Exception( "Error in getTasDefMessage, the getTasUid() can't be blank"));
        }

        $lang = defined('SYS_LANG')? SYS_LANG : 'en';
        $this->tas_def_message_content = Content::load('TAS_DEF_MESSAGE', '', $this->getTasUid(), $lang);

        return $this->tas_def_message_content;
    }

    /**
     * Set the tas_def_message column value.
     *
     * @param      string $v new value
     * @return     void
     */
    public function setTasDefMessageContent($v)
    {
        if ($this->getTasUid() == "") {
            throw (new Exception("Error in setTasDefMessage, the getTasUid() can't be blank"));
        }
        $v = isset($v)? ((string)$v) : '';
        $lang = defined('SYS_LANG')? SYS_LANG : 'en';

        if (in_array(TaskPeer::TAS_DEF_MESSAGE, $this->modifiedColumns) || $v === "") {
            $this->tas_def_message_content = $v;

            $res = Content::addContent('TAS_DEF_MESSAGE', '', $this->getTasUid(), $lang, $this->tas_def_message_content);

            return $res;
        }

        return 0;
    }

    /**
     * This value goes in the content table
     * @var        string
     */
    protected $tas_def_subject_message_content = '';

    /**
     * Get the tas_def_message column value.
     * @return     string
     */
    public function getTasDefSubjectMessageContent()
    {
        if ($this->getTasUid() == "") {
            throw (new Exception("Error in getTasDefSubjectMessage, the getTasUid() can't be blank"));
        }

        $lang = defined('SYS_LANG')? SYS_LANG : 'en';
        $this->tas_def_subject_message_content = Content::load('TAS_DEF_SUBJECT_MESSAGE', '', $this->getTasUid(), $lang);

        return $this->tas_def_subject_message_content;
    }

    /**
     * Set the tas_def_subject_message column value.
     *
     * @param      string $v new value
     * @return     void
     */
    public function setTasDefSubjectMessageContent($v)
    {
        if ($this->getTasUid() == "") {
            throw (new Exception( "Error in setTasDefSubjectMessage, the getTasUid() can't be blank"));
        }

        $v = isset($v)? ((string)$v) : '';
        $lang = defined('SYS_LANG')? SYS_LANG : 'en';

        if (in_array(TaskPeer::TAS_DEF_SUBJECT_MESSAGE, $this->modifiedColumns) || $v === "") {
            $this->tas_def_subject_message_content = $v;

            $res = Content::addContent('TAS_DEF_SUBJECT_MESSAGE', '', $this->getTasUid(), $lang, $v);

            return $res;
        }

        return 0;
    }

    /**
     * create a new Task
     *
     * @param      array $aData with new values
     * @return     string
     */
    public function create($aData, $generateUid = true)
    {
        $con = Propel::getConnection(TaskPeer::DATABASE_NAME);

        try {
            if ($generateUid) {
                $sTaskUID = G::generateUniqueID();
            } else {
                $sTaskUID = $aData['TAS_UID'];
            }

            $con->begin();
            $this->setProUid($aData['PRO_UID']);
            $this->setTasUid($sTaskUID);
            $this->setTasTitle((isset($aData['TAS_TITLE']) ? $aData['TAS_TITLE']: ''));
            $this->setTasDescription((isset($aData['TAS_DESCRIPTION']) ? $aData['TAS_DESCRIPTION']: ''));
            $this->setTasDefTitle("");
            $this->setTasDefDescription("");
            $this->setTasDefProcCode("");
            $this->setTasDefMessage("");
            $this->setTasDefSubjectMessage("");
            $this->setTasType("NORMAL");
            $this->setTasDuration("1");
            $this->setTasDelayType("");
            $this->setTasTemporizer("");
            $this->setTasTypeDay("");
            $this->setTasTimeunit("DAYS");
            $this->setTasAlert("FALSE");
            $this->setTasPriorityVariable("");
            $this->setTasAssignType("BALANCED");
            $this->setTasAssignVariable("@@SYS_NEXT_USER_TO_BE_ASSIGNED");
            $this->setTasAssignLocation("FALSE");
            $this->setTasAssignLocationAdhoc("FALSE");
            $this->setTasTransferFly("FALSE");
            $this->setTasLastAssigned("0");
            $this->setTasUser("0");
            $this->setTasCanUpload("FALSE");
            $this->setTasViewUpload("FALSE");
            $this->setTasViewAdditionalDocumentation("FALSE");
            $this->setTasCanCancel("FALSE");
            $this->setTasOwnerApp("FALSE");
            $this->setStgUid("");
            $this->setTasCanPause("FALSE");
            $this->setTasCanSendMessage("TRUE");
            $this->setTasCanDeleteDocs("FALSE");
            $this->setTasSelfService("FALSE");
            $this->setTasStart("FALSE");
            $this->setTasToLastUser("FALSE");
            $this->setTasSendLastEmail("FALSE");
            $this->setTasDerivation("NORMAL");
            $this->setTasPosx("");
            $this->setTasPosy("");
            $this->setTasColor("");
            $this->setTasGroupVariable("");
            if (!$generateUid && !empty($aData['TAS_ID'])) {
                $this->setTasId($aData['TAS_ID']);
            }
            $this->fromArray($aData,BasePeer::TYPE_FIELDNAME);

            if ($this->validate()) {
                $this->setTasTitleContent((isset($aData['TAS_TITLE']) ? $aData['TAS_TITLE']: ''));
                $this->setTasDescriptionContent((isset($aData['TAS_DESCRIPTION']) ? $aData['TAS_DESCRIPTION']: ''));
                $this->setTasDefTitleContent("");
                $this->setTasDefDescriptionContent("");
                $this->setTasDefProcCodeContent("");
                $this->setTasDefMessageContent("");
                $this->setTasDefSubjectMessageContent("");
                $this->save();
                $con->commit();
                return $sTaskUID;
            } else {
                $con->rollback();
                $e = new Exception("Failed Validation in class " . get_class($this) . ".");
                $e->aValidationFailures=$this->getValidationFailures();

                throw ($e);
            }
        } catch (Exception $e) {
            $con->rollback();

            throw ($e);
        }
    }

    public function kgetassigType($pro_uid, $tas)
    {
        $k = new Criteria();
        $k->clearSelectColumns();
        $k->addSelectColumn(TaskPeer::TAS_UID);
        $k->addSelectColumn(TaskPeer::TAS_ASSIGN_TYPE);
        $k->add(TaskPeer::PRO_UID, $pro_uid );
        $k->add(TaskPeer::TAS_UID, $tas );
        $rs = TaskPeer::doSelectRS($k);
        $rs->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $rs->next();
        $row = $rs->getRow();

        return $row;
    }

    /**
     * @param $pro_uid
     * @param $tas
     * @return array
     */
    public function getEmailServerSettingsForNotification($pro_uid, $tas)
    {
        $oCriteria = new Criteria();
        $oCriteria->clearSelectColumns();
        $oCriteria->addSelectColumn(TaskPeer::TAS_NOT_EMAIL_FROM_FORMAT);
        $oCriteria->add(TaskPeer::PRO_UID, $pro_uid );
        $oCriteria->add(TaskPeer::TAS_UID, $tas );
        $rs = TaskPeer::doSelectRS($oCriteria);
        $rs->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $rs->next();
        $row = $rs->getRow();

        return $row;
    }

    public function load($TasUid)
    {
        try {
            $oRow = TaskPeer::retrieveByPK($TasUid);

            if (!is_null($oRow)) {
                $aFields = $oRow->toArray(BasePeer::TYPE_FIELDNAME);

                $this->fromArray($aFields, BasePeer::TYPE_FIELDNAME); //Populating an object from of the array
                                                                      //Populating attributes
                $this->setNew(false);

                /*----------------------------------********---------------------------------*/

                ///////
                return $aFields;
            } else {
                throw (new Exception("The row '" . $TasUid . "' in table TASK doesn't exist!"));
            }
        } catch (Exception $oError) {
            throw ($oError);
        }
    }

    public function update($fields)
    {
        require_once ("classes/model/AppCacheView.php");
        require_once ("classes/model/Configuration.php");

        $con = Propel::getConnection(TaskPeer::DATABASE_NAME);

        try {
            $con->begin();
            $oldValues = $this->load($fields["TAS_UID"]);
            $this->fromArray($fields, BasePeer::TYPE_FIELDNAME);
            $this->validateAssignType($fields,$oldValues);
            
            if ($this->validate()) {
                $taskDefTitlePrevious = $oldValues["TAS_DEF_TITLE"];

                $contentResult = 0;

                if (array_key_exists("TAS_TITLE", $fields)) {
                    $contentResult += $this->setTasTitleContent($fields["TAS_TITLE"]);
                }

                if (array_key_exists("TAS_DESCRIPTION", $fields)) {
                    $contentResult += $this->setTasDescriptionContent($fields["TAS_DESCRIPTION"]);
                }

                if (array_key_exists("TAS_DEF_TITLE", $fields)) {
                    $contentResult += $this->setTasDefTitleContent($fields["TAS_DEF_TITLE"]);
                }

                if (array_key_exists("TAS_DEF_DESCRIPTION", $fields)) {
                    $contentResult += $this->setTasDefDescriptionContent($fields["TAS_DEF_DESCRIPTION"]);
                }

                if (array_key_exists("TAS_DEF_PROC_CODE", $fields)) {
                    $contentResult += $this->setTasDefProcCodeContent($fields["TAS_DEF_PROC_CODE"]);
                }

                if (array_key_exists("TAS_DEF_MESSAGE", $fields)) {
                    $contentResult += $this->setTasDefMessageContent(trim($fields["TAS_DEF_MESSAGE"]));
                }

                if (array_key_exists("TAS_DEF_SUBJECT_MESSAGE", $fields)) {
                    $contentResult += $this->setTasDefSubjectMessageContent(trim($fields["TAS_DEF_SUBJECT_MESSAGE"]));
                }

                if (array_key_exists("TAS_CALENDAR", $fields)) {
                    $contentResult += $this->setTasCalendar($fields['TAS_UID'],$fields["TAS_CALENDAR"]);
                }

                $result = $this->save();
                $result = ($result == 0)? (($contentResult > 0)? 1 : 0) : $result;
                $con->commit();

                if ($result == 1 &&
                    array_key_exists("TAS_DEF_TITLE", $fields) &&
                    $fields["TAS_DEF_TITLE"] != $taskDefTitlePrevious
                   ) {
                    $criteria = new Criteria("workflow");

                    $criteria->addAsColumn("APPCV_NUM_ROWS", "COUNT(DISTINCT " . AppCacheViewPeer::APP_UID . ")");
                    $criteria->add(AppCacheViewPeer::DEL_THREAD_STATUS, "OPEN");
                    $criteria->add(AppCacheViewPeer::TAS_UID, $fields["TAS_UID"]);

                    $rsCriteria = AppCacheViewPeer::doSelectRS($criteria);
                    $rsCriteria->setFetchmode(ResultSet::FETCHMODE_ASSOC);

                    $rsCriteria->next();
                    $row = $rsCriteria->getRow();

                    $appcvNumRows = intval($row["APPCV_NUM_ROWS"]);

                    if ($appcvNumRows <= 1000) {
                        $appcv = new AppCacheView();
                        $appcv->appTitleByTaskCaseLabelUpdate($fields["TAS_UID"], SYS_LANG);

                        $result = 2;
                    } else {
                        //Delete record
                        $criteria = new Criteria("workflow");

                        $criteria->add(ConfigurationPeer::CFG_UID, "TAS_APP_TITLE_UPDATE");
                        $criteria->add(ConfigurationPeer::OBJ_UID, $fields["TAS_UID"]);
                        $criteria->add(ConfigurationPeer::CFG_VALUE, SYS_LANG);

                        $numRowDeleted = ConfigurationPeer::doDelete($criteria);

                        //Insert record
                        $conf = new Configuration();

                        $conf->create(
                            array(
                                "CFG_UID"   => "TAS_APP_TITLE_UPDATE",
                                "OBJ_UID"   => $fields["TAS_UID"],
                                "CFG_VALUE" => SYS_LANG,
                                "PRO_UID"   => "",
                                "USR_UID"   => "",
                                "APP_UID"   => ""
                            )
                        );

                        $result = 3;
                    }
                }

                return $result;
            } else {
                $con->rollback();

                throw (new Exception("Failed Validation in class " . get_class($this) . "."));
            }
        } catch (Exception $e) {
            $con->rollback();

            throw ($e);
        }
    }

    public function remove($TasUid)
    {
        $oConnection = Propel::getConnection(TaskPeer::DATABASE_NAME);

        try {
            $oTask = TaskPeer::retrieveByPK($TasUid);

            if (!is_null($oTask)) {
                $oConnection->begin();

                Content::removeContent('TAS_TITLE', '', $oTask->getTasUid());
                Content::removeContent('TAS_DESCRIPTION', '', $oTask->getTasUid());
                Content::removeContent('TAS_DEF_TITLE', '', $oTask->getTasUid());
                Content::removeContent('TAS_DEF_DESCRIPTION', '', $oTask->getTasUid());
                Content::removeContent('TAS_DEF_PROC_CODE', '', $oTask->getTasUid());
                Content::removeContent('TAS_DEF_MESSAGE', '', $oTask->getTasUid());
                Content::removeContent('TAS_DEF_SUBJECT_MESSAGE', '', $oTask->getTasUid());

                $iResult = $oTask->delete();
                $oConnection->commit();

                return $iResult;
            } else {
                throw (new Exception( "The row '" . $TasUid . "' in table TASK doesn't exist!"));
            }
        } catch (Exception $oError) {
            $oConnection->rollback();

            throw ($oError);
        }
    }

    /**
     * verify if Task row specified in [TasUid] exists.
     *
     * @param      string $sProUid   the uid of the Prolication
     */
    public function taskExists($TasUid)
    {
        $con = Propel::getConnection(TaskPeer::DATABASE_NAME);

        try {
            $oPro = TaskPeer::retrieveByPk($TasUid);

            if (is_object($oPro) && get_class($oPro) == 'Task') {
                return true;
            } else {
                return false;
            }
        } catch (Exception $oError) {
            throw ($oError);
        }
    }

    /**
     * create a new Task
     *
     * @param      array $aData with new values
     * @return     void
     */
    public function createRow($aData)
    {
        $con = Propel::getConnection(TaskPeer::DATABASE_NAME);

        try {
            $con->begin();

            $this->fromArray($aData,BasePeer::TYPE_FIELDNAME);

            if ($this->validate()) {
                $this->setTasTitleContent((isset($aData['TAS_TITLE'])? $aData['TAS_TITLE'] : ''));
                $this->setTasDescriptionContent((isset($aData['TAS_DESCRIPTION'])? $aData['TAS_DESCRIPTION'] : ''));
                $this->setTasDefTitleContent((isset($aData['TAS_DEF_TITLE'])? $aData['TAS_DEF_TITLE'] : ''));
                $this->setTasDefDescriptionContent((isset($aData['TAS_DEF_DESCRIPTION'])? $aData['TAS_DEF_DESCRIPTION'] : ''));
                $this->setTasDefProcCodeContent((isset($aData['TAS_DEF_DESCRIPTION'])? $aData['TAS_DEF_DESCRIPTION'] : ''));
                $this->setTasDefMessageContent((isset($aData['TAS_DEF_MESSAGE'])? $aData['TAS_DEF_MESSAGE'] : ''));

                $strAux = isset($aData['TAS_DEF_SUBJECT_MESSAGE'])? $aData['TAS_DEF_SUBJECT_MESSAGE'] : '';
                $this->setTasDefSubjectMessageContent($strAux);

                $this->save();
                $con->commit();

                return;
            } else {
                $con->rollback();
                $e = new Exception("Failed Validation in class " . get_class($this) . ".");
                $e->aValidationFailures=$this->getValidationFailures();

                throw ($e);
            }
        } catch (Exception $e) {
            $con->rollback();
            throw ($e);
        }
    }

    public function setTasCalendar($taskUid, $calendarUid)
    {
        //Save Calendar ID for this process
        $calendarObj = new Calendar();
        $calendarObj->assignCalendarTo($taskUid, $calendarUid, 'TASK');
    }

    public function getDelegatedTaskData($TAS_UID, $APP_UID, $DEL_INDEX)
    {
        require_once ('classes/model/AppDelegation.php');
        require_once ('classes/model/Task.php');

        $oTask = new Task();

        $aFields = $oTask->load($TAS_UID);
        $oCriteria = new Criteria('workflow');
        $oCriteria->add(AppDelegationPeer::APP_UID, $APP_UID);
        $oCriteria->add(AppDelegationPeer::DEL_INDEX, $DEL_INDEX);
        $oDataset = AppDelegationPeer::doSelectRS($oCriteria);
        $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $oDataset->next();
        $taskData = $oDataset->getRow();

        $iDiff = strtotime($taskData['DEL_FINISH_DATE']) - strtotime($taskData['DEL_INIT_DATE']);

        $aFields['INIT_DATE'] = (
            $taskData['DEL_INIT_DATE'] != null ?
            $taskData['DEL_INIT_DATE'] : G::LoadTranslation('ID_CASE_NOT_YET_STARTED')
        );

        $aFields['DUE_DATE'] = (
            $taskData['DEL_TASK_DUE_DATE'] != null ?
            $taskData['DEL_TASK_DUE_DATE'] : G::LoadTranslation('ID_NOT_FINISHED')
        );

        $aFields['FINISH'] = (
            $taskData['DEL_FINISH_DATE'] != null ?
            $taskData['DEL_FINISH_DATE'] : G::LoadTranslation('ID_NOT_FINISHED')
        );

        $aFields['DURATION'] = ($taskData['DEL_FINISH_DATE'] != null ? (int) ($iDiff / 3600) . ' ' . ((int) ($iDiff / 3600) == 1 ? G::LoadTranslation('ID_HOUR') : G::LoadTranslation('ID_HOURS')) . ' ' . (int) (($iDiff % 3600) / 60) . ' ' . ((int) (($iDiff % 3600) / 60) == 1 ? G::LoadTranslation('ID_MINUTE') : G::LoadTranslation('ID_MINUTES')) . ' ' . (int) (($iDiff % 3600) % 60) . ' ' . ((int) (($iDiff % 3600) % 60) == 1 ? G::LoadTranslation('ID_SECOND') : G::LoadTranslation('ID_SECONDS')) : G::LoadTranslation('ID_NOT_FINISHED'));

        return $aFields;
    }

    //Added by qennix
    //Gets Starting Event of current task
    public function getStartingEvent()
    {
        require_once ('classes/model/Event.php');

        $oCriteria = new Criteria('workflow');
        $oCriteria->addSelectColumn(EventPeer::EVN_UID);
        $oCriteria->add(EventPeer::EVN_TAS_UID_TO, $this->tas_uid);
        //$oCriteria->add(EventPeer::EVN_TYPE, 'bpmnEventMessageStart');
        $oDataset = EventPeer::doSelectRS($oCriteria);
        $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);

        if ($oDataset->next()) {
            $row = $oDataset->getRow();
            $event_uid = $row['EVN_UID'];
        } else {
            $event_uid = '';
        }

        return $event_uid;
    }
    
    public function validateAssignType($newValues,$oldValues)
    {
        if(isset($newValues['TAS_ASSIGN_TYPE']) && isset($oldValues['TAS_ASSIGN_TYPE'])) {
            $newAssigType = $newValues['TAS_ASSIGN_TYPE'];
            $oldAssigType = $oldValues['TAS_ASSIGN_TYPE'];
            if($newAssigType == 'SELF_SERVICE'){
                $newAssigType = isset($newValues['TAS_GROUP_VARIABLE'])?(empty($newValues['TAS_GROUP_VARIABLE'])?'SELF_SERVICE':'SELF_SERVICE_VALUE_BASED'):'SELF_SERVICE';
            }
            if($oldAssigType == 'SELF_SERVICE'){
                $oldAssigType = isset($oldValues['TAS_GROUP_VARIABLE'])?(empty($oldValues['TAS_GROUP_VARIABLE'])?'SELF_SERVICE':'SELF_SERVICE_VALUE_BASED'):'SELF_SERVICE';
            }
            if(($oldAssigType == 'SELF_SERVICE' && $newAssigType != 'SELF_SERVICE') || ($oldAssigType == 'SELF_SERVICE_VALUE_BASED' && $newAssigType != 'SELF_SERVICE_VALUE_BASED')) {    
                $oCriteria = new Criteria();
                $oCriteria->add(AppDelegationPeer::DEL_THREAD_STATUS, "OPEN");
                $oCriteria->add(AppDelegationPeer::TAS_UID, $newValues['TAS_UID']);
                $oCriteria->add(AppDelegationPeer::USR_UID, "");
                $oApplication = AppDelegationPeer::doSelectOne($oCriteria);
                if(!empty($oApplication)) {
                    throw (new Exception(G::LoadTranslation('ID_CURRENT_ASSING_TYPE_WITH_CASES')));        
                }
            }
        }
    }
}

            
