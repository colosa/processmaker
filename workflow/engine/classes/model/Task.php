<?php
/**
 * Task.php
 * @package    workflow.engine.classes.model
 *
 * ProcessMaker Open Source Edition
 * Copyright (C) 2004 - 2011 Colosa Inc.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * For more information, contact Colosa Inc, 2566 Le Jeune Rd.,
 * Coral Gables, FL, 33134, USA, or email info@colosa.com.
 *
 */

require_once 'classes/model/om/BaseTask.php';
require_once 'classes/model/Content.php';


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
class Task extends BaseTask {
 /**
   * This value goes in the content table
   * @var        string
   */
  protected $tas_title = '';
  /**
   * Get the tas_title column value.
   * @return     string
   */
  public function getTasTitle()
  {
    if ( $this->getTasUid() == "" ) {
      throw ( new Exception( "Error in getTasTitle, the getTasUid() can't be blank") );
    }
    $lang = defined ( 'SYS_LANG') ? SYS_LANG : 'en';
    $this->tas_title = Content::load ( 'TAS_TITLE', '', $this->getTasUid(), $lang );
    return $this->tas_title;
  }
  /**
   * Set the tas_title column value.
   *
   * @param      string $v new value
   * @return     void
   */
  public function setTasTitle($v)
  {
    if ( $this->getTasUid() == "" ) {
      throw ( new Exception( "Error in setTasTitle, the getTasUid() can't be blank") );
    }
    $v=isset($v)?((string)$v):'';
    $lang = defined ( 'SYS_LANG') ? SYS_LANG : 'en';
    if ($this->tas_title !== $v || $v==="") {
      $this->tas_title = $v;
      
      $res = Content::addContent( 'TAS_TITLE', '', $this->getTasUid(), $lang, $this->tas_title );
      return $res;
    }
    return 0;
  }
  /**
   * This value goes in the content table
   * @var        string
   */
  protected $tas_description = '';
  /**
   * Get the tas_description column value.
   * @return     string
   */
  public function getTasDescription()
  {
    if ( $this->getTasUid() == "" ) {
      throw ( new Exception( "Error in getTasDescription, the getTasUid() can't be blank") );
    }
    $lang = defined ( 'SYS_LANG') ? SYS_LANG : 'en';
    $this->tas_description = Content::load ( 'TAS_DESCRIPTION', '', $this->getTasUid(), $lang );
    return $this->tas_description;
  }
  /**
   * Set the tas_description column value.
   *
   * @param      string $v new value
   * @return     void
   */
  public function setTasDescription($v)
  {
    if ( $this->getTasUid() == "" ) {
      throw ( new Exception( "Error in setTasDescription, the getTasUid() can't be blank") );
    }
    $v=isset($v)?((string)$v):'';
    $lang = defined ( 'SYS_LANG') ? SYS_LANG : 'en';
    if ($this->tas_description !== $v || $v==="") {
      $this->tas_description = $v;
      
      $res = Content::addContent( 'TAS_DESCRIPTION', '', $this->getTasUid(), $lang, $this->tas_description );
      return $res;
    }
    return 0;
  }
  /**
   * This value goes in the content table
   * @var        string
   */
  protected $tas_def_title = '';
  /**
   * Get the tas_def_title column value.
   * @return     string
   */
  public function getTasDefTitle()
  {
    if ( $this->getTasUid() == "" ) {
      throw ( new Exception( "Error in getTasDefTitle, the getTasUid() can't be blank") );
    }
    $lang = defined ( 'SYS_LANG') ? SYS_LANG : 'en';
    $this->tas_def_title = Content::load ( 'TAS_DEF_TITLE', '', $this->getTasUid(), $lang );
    return $this->tas_def_title;
  }
  /**
   * Set the tas_def_title column value.
   *
   * @param      string $v new value
   * @return     void
   */
  public function setTasDefTitle($v)
  {
    if ( $this->getTasUid() == "" ) {
      throw ( new Exception( "Error in setTasDefTitle, the getTasUid() can't be blank") );
    }
    $v=isset($v)?((string)$v):'';
    $lang = defined ( 'SYS_LANG') ? SYS_LANG : 'en';
    if ($this->tas_def_title !== $v || $v==="") {
      $this->tas_def_title = $v;
      
      $res = Content::addContent( 'TAS_DEF_TITLE', '', $this->getTasUid(), $lang, $this->tas_def_title );
      return $res;
    }
    return 0;
  }
  /**
   * This value goes in the content table
   * @var        string
   */
  protected $tas_def_description = '';
  /**
   * Get the tas_def_description column value.
   * @return     string
   */
  public function getTasDefDescription()
  {
    if ( $this->getTasUid() == "" ) {
      throw ( new Exception( "Error in getTasDefDescription, the getTasUid() can't be blank") );
    }
    $lang = defined ( 'SYS_LANG') ? SYS_LANG : 'en';
    $this->tas_def_description = Content::load ( 'TAS_DEF_DESCRIPTION', '', $this->getTasUid(), $lang );
    return $this->tas_def_description;
  }
  /**
   * Set the tas_def_description column value.
   *
   * @param      string $v new value
   * @return     void
   */
  public function setTasDefDescription($v)
  {
    if ( $this->getTasUid() == "" ) {
      throw ( new Exception( "Error in setTasDefDescription, the getTasUid() can't be blank") );
    }
    $v=isset($v)?((string)$v):'';
    $lang = defined ( 'SYS_LANG') ? SYS_LANG : 'en';
    if ($this->tas_def_description !== $v || $v==="") {
      $this->tas_def_description = $v;
      
      $res = Content::addContent( 'TAS_DEF_DESCRIPTION', '', $this->getTasUid(), $lang, $this->tas_def_description );
      return $res;
    }
    return 0;
  }
  /**
   * This value goes in the content table
   * @var        string
   */
  protected $tas_def_proc_code = '';
  /**
   * Get the tas_def_proc_code column value.
   * @return     string
   */
  public function getTasDefProcCode()
  {
    if ( $this->getTasUid() == "" ) {
      throw ( new Exception( "Error in getTasDefProcCode, the getTasUid() can't be blank") );
    }
    $lang = defined ( 'SYS_LANG') ? SYS_LANG : 'en';
    $this->tas_def_proc_code = Content::load ( 'TAS_DEF_PROC_CODE', '', $this->getTasUid(), $lang );
    return $this->tas_def_proc_code;
  }
  /**
   * Set the tas_def_proc_code column value.
   *
   * @param      string $v new value
   * @return     void
   */
  public function setTasDefProcCode($v)
  {
    if ( $this->getTasUid() == "" ) {
      throw ( new Exception( "Error in setTasDefProcCode, the getTasUid() can't be blank") );
    }
    $v=isset($v)?((string)$v):'';
    $lang = defined ( 'SYS_LANG') ? SYS_LANG : 'en';
    if ($this->tas_def_proc_code !== $v || $v==="") {
      $this->tas_def_proc_code = $v;
      
      $res = Content::addContent( 'TAS_DEF_PROC_CODE', '', $this->getTasUid(), $lang, $this->tas_def_proc_code );
      return $res;
    }
    return 0;
  }
  /**
   * This value goes in the content table
   * @var        string
   */
  protected $tas_def_message = '';
  /**
   * Get the tas_def_message column value.
   * @return     string
   */
  public function getTasDefMessage()
  {
    if ( $this->getTasUid() == "" ) {
      throw ( new Exception( "Error in getTasDefMessage, the getTasUid() can't be blank") );
    }
    $lang = defined ( 'SYS_LANG') ? SYS_LANG : 'en';
    $this->tas_def_message = Content::load ( 'TAS_DEF_MESSAGE', '', $this->getTasUid(), $lang );
    return $this->tas_def_message;
  }

  /**
   * Set the tas_def_message column value.
   *
   * @param      string $v new value
   * @return     void
   */
  public function setTasDefMessage($v)
  {
    if ( $this->getTasUid() == "" ) {
      throw ( new Exception( "Error in setTasDefMessage, the getTasUid() can't be blank") );
    }
    $v=isset($v)?((string)$v):'';
    $lang = defined ( 'SYS_LANG') ? SYS_LANG : 'en';
    if ($this->tas_def_message !== $v || $v==="") {
      $this->tas_def_message = $v;
      
      $res = Content::addContent( 'TAS_DEF_MESSAGE', '', $this->getTasUid(), $lang, $this->tas_def_message );
      return $res;
    }
    return 0;
  }

   /**
   * This value goes in the content table
   * @var        string
   */
  protected $tas_def_subject_message = '';

  /**
   * Get the tas_def_message column value.
   * @return     string
   */
  public function getTasDefSubjectMessage()
  {
    if ( $this->getTasUid() == "" ) {
      throw ( new Exception( "Error in getTasDefSubjectMessage, the getTasUid() can't be blank") );
    }
    $lang = defined ( 'SYS_LANG') ? SYS_LANG : 'en';
    $this->tas_def_subject_message = Content::load ( 'TAS_DEF_SUBJECT_MESSAGE', '', $this->getTasUid(), $lang );
    return $this->tas_def_subject_message;
  }


  /**
   * Set the tas_def_subject_message column value.
   *
   * @param      string $v new value
   * @return     void
   */
  public function setTasDefSubjectMessage($v)
  {
    if ( $this->getTasUid() == "" ) {
      throw ( new Exception( "Error in setTasDefSubjectMessage, the getTasUid() can't be blank") );
    }
    $v = isset($v)? ((string)$v) : '';
    $lang = defined ( 'SYS_LANG') ? SYS_LANG : 'en';
    
    if ($this->tas_def_subject_message !== $v || $v==="") {
      $this->tas_def_subject_message = $v;
      
      $res = Content::addContent( 'TAS_DEF_SUBJECT_MESSAGE', '', $this->getTasUid(), $lang, $this->tas_def_subject_message ); 
    
      return $res;
    }
    return 0;
  }

  /**
   * create a new Task
   *
   * @param      array $aData with new values
   * @return     void
   */
  function create($aData)
  {
    $con = Propel::getConnection(TaskPeer::DATABASE_NAME);
    try
    {
      $sTaskUID = G::generateUniqueID();
      $con->begin();
      $this->setProUid($aData['PRO_UID']);
      $this->setTasUid($sTaskUID);
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
      $this->fromArray($aData,BasePeer::TYPE_FIELDNAME);
      if($this->validate())
      {
        $this->setTasTitle((isset($aData['TAS_TITLE']) ? $aData['TAS_TITLE']: ''));
        $this->setTasDescription("");
        $this->setTasDefTitle("");
        $this->setTasDefDescription("");
        $this->setTasDefProcCode("");
        $this->setTasDefMessage("");
        $this->setTasDefSubjectMessage("");
        $this->save();
        $con->commit();
        return $sTaskUID;
      }
      else
      {
        $con->rollback();
        $e=new Exception("Failed Validation in class ".get_class($this).".");
        $e->aValidationFailures=$this->getValidationFailures();
        throw($e);
      }
    }
    catch(Exception $e)
    {
      $con->rollback();
      throw($e);
    }
  }

public function kgetassigType($pro_uid, $tas){
  
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

  public function load($TasUid)
  {
    try {
      $oRow = TaskPeer::retrieveByPK($TasUid);
      
      if (!is_null($oRow))
      {
        $aFields = $oRow->toArray(BasePeer::TYPE_FIELDNAME);
        
        $this->fromArray($aFields, BasePeer::TYPE_FIELDNAME); //Populating an object from of the array //Populating attributes
        $this->setNew(false);
        
        ///////
        //Create new records for TASK in CONTENT for the current language, this if is necesary //Populating others attributes
        $this->setTasUid($TasUid);
          
        $aFields["TAS_TITLE"] = $this->getTasTitle();
        $aFields["TAS_DESCRIPTION"] = $this->getTasDescription();
        $aFields["TAS_DEF_TITLE"] = $this->getTasDefTitle();
        $aFields["TAS_DEF_DESCRIPTION"] = $this->getTasDefDescription();
        $aFields["TAS_DEF_PROC_CODE"] = $this->getTasDefProcCode();
        $aFields["TAS_DEF_MESSAGE"] = $this->getTasDefMessage();
        $aFields["TAS_DEF_SUBJECT_MESSAGE"] = $this->getTasDefSubjectMessage();
        
        ///////
        return $aFields;
      }
      else {
        throw(new Exception("The row '" . $TasUid . "' in table TASK doesn't exist!"));
      }
    }
    catch (Exception $oError) {
      throw($oError);
    }
  }
  
  function update($fields)
  {
    ini_set("max_execution_time", 0);
    
    require_once ("classes/model/AppCacheView.php");
    
    $con = Propel::getConnection(TaskPeer::DATABASE_NAME);
    try
    {
      $con->begin();
      $this->load($fields['TAS_UID']);
      $this->fromArray($fields,BasePeer::TYPE_FIELDNAME);
      if($this->validate())
      {
        $contentResult=0;
        if (array_key_exists("TAS_TITLE", $fields)) $contentResult+=$this->setTasTitle($fields["TAS_TITLE"]);
        if (array_key_exists("TAS_DESCRIPTION", $fields)) $contentResult+=$this->setTasDescription($fields["TAS_DESCRIPTION"]);
        if (array_key_exists("TAS_DEF_TITLE", $fields)) $contentResult+=$this->setTasDefTitle($fields["TAS_DEF_TITLE"]);
        if (array_key_exists("TAS_DEF_DESCRIPTION", $fields)) $contentResult+=$this->setTasDefDescription($fields["TAS_DEF_DESCRIPTION"]);
        if (array_key_exists("TAS_DEF_PROC_CODE", $fields)) $contentResult+=$this->setTasDefProcCode($fields["TAS_DEF_PROC_CODE"]);
        if (array_key_exists("TAS_DEF_MESSAGE", $fields)) $contentResult+=$this->setTasDefMessage(trim($fields["TAS_DEF_MESSAGE"]));        
        if (array_key_exists("TAS_DEF_SUBJECT_MESSAGE", $fields)) $contentResult+=$this->setTasDefSubjectMessage(trim($fields["TAS_DEF_SUBJECT_MESSAGE"]));
        if (array_key_exists("TAS_CALENDAR", $fields)) $contentResult+=$this->setTasCalendar($fields['TAS_UID'],$fields["TAS_CALENDAR"]);
        $result=$this->save();
        $result=($result==0)?($contentResult>0?1:0):$result;
        $con->commit();
        
        if ($result == 1 && array_key_exists("TAS_DEF_TITLE", $fields)) {
          //Get cases
          $criteriaAPPCV = new Criteria("workflow");
          
          $criteriaAPPCV->setDistinct();
          $criteriaAPPCV->addSelectColumn(AppCacheViewPeer::APP_UID);
          $criteriaAPPCV->add(AppCacheViewPeer::TAS_UID, $fields["TAS_UID"]);
          
          $rsCriteriaAPPCV = AppCacheViewPeer::doSelectRS($criteriaAPPCV);
          $rsCriteriaAPPCV->setFetchmode(ResultSet::FETCHMODE_ASSOC);
          
          while ($rsCriteriaAPPCV->next()) {
            $row = $rsCriteriaAPPCV->getRow();
            
            $appcv_application_uid = $row["APP_UID"];
            
            //Get DEL_INDEX_MAX
            $criteria = new Criteria("workflow");
            
            $criteria->addAsColumn("DEL_INDEX_MAX", "MAX(" . AppCacheViewPeer::DEL_INDEX . ")");
            $criteria->add(AppCacheViewPeer::APP_UID, $appcv_application_uid);
            
            $rsCriteria = AppCacheViewPeer::doSelectRS($criteria);
            $rsCriteria->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            
            $rsCriteria->next();
            $row = $rsCriteria->getRow();
            
            $appcvDelIndexMax = $row["DEL_INDEX_MAX"];
            
            //Current task?
            $criteria = new Criteria("workflow");
            
            $criteria->addSelectColumn(AppCacheViewPeer::APP_UID);
            $criteria->add(AppCacheViewPeer::APP_UID, $appcv_application_uid);
            $criteria->add(AppCacheViewPeer::DEL_INDEX, $appcvDelIndexMax);
            $criteria->add(AppCacheViewPeer::TAS_UID, $fields["TAS_UID"]);
            
            $rsCriteria = AppCacheViewPeer::doSelectRS($criteria);
            $rsCriteria->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            
            if ($rsCriteria->next()) {
              $appTitle = $fields["TAS_DEF_TITLE"];
              
              $app = new Application();
              $arrayAppField = $app->Load($appcv_application_uid);
              
              $appTitle    = (!empty($appTitle))? $appTitle : "#" . $arrayAppField["APP_NUMBER"];
              $appTitleNew = G::replaceDataField($appTitle, unserialize($arrayAppField["APP_DATA"]));
              
              if (isset($arrayAppField["APP_TITLE"]) && $arrayAppField["APP_TITLE"] != $appTitleNew) {
                //Updating the value in content, where...
                $criteria1 = new Criteria("workflow");
                $criteria1->add(ContentPeer::CON_CATEGORY, "APP_TITLE");
                $criteria1->add(ContentPeer::CON_ID, $appcv_application_uid);
                $criteria1->add(ContentPeer::CON_LANG, SYS_LANG);
                
                //Update set
                $criteria2 = new Criteria("workflow");
                $criteria2->add(ContentPeer::CON_VALUE, $appTitleNew);
                
                BasePeer::doUpdate($criteria1, $criteria2, Propel::getConnection("workflow"));
              }
            }
          }
        }
        
        return $result;
      }
      else
      {
        $con->rollback();
        throw(new Exception("Failed Validation in class ".get_class($this)."."));
      }
    }
    catch(Exception $e)
    {
      $con->rollback();
      throw($e);
    }
  }
  function remove($TasUid)
  {
    $oConnection = Propel::getConnection(TaskPeer::DATABASE_NAME);
    try {
      $oTask = TaskPeer::retrieveByPK($TasUid);
      if (!is_null($oTask))
      {
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
      }
      else {
        throw( new Exception( "The row '" . $TasUid . "' in table TASK doesn't exist!" ));
      }
    }
    catch (Exception $oError) {
      $oConnection->rollback();
      throw($oError);
    }
  }

  /**
   * verify if Task row specified in [TasUid] exists.
   *
   * @param      string $sProUid   the uid of the Prolication
   */

  function taskExists ( $TasUid ) {
    $con = Propel::getConnection(TaskPeer::DATABASE_NAME);
    try {
      $oPro = TaskPeer::retrieveByPk( $TasUid );
      if ( is_object($oPro) && get_class ($oPro) == 'Task' ) {
        return true;
      }
      else {
        return false;
      }
    }
    catch (Exception $oError) {
      throw($oError);
    }
  }
  
   /**
   * create a new Task
   *
   * @param      array $aData with new values
   * @return     void
   */
  function createRow($aData)
  {
    $con = Propel::getConnection(TaskPeer::DATABASE_NAME);
    try
    {     
      $con->begin();
      
      $this->fromArray($aData,BasePeer::TYPE_FIELDNAME);
      if($this->validate())
      {
        $this->setTasTitle((isset($aData['TAS_TITLE']) ? $aData['TAS_TITLE']: ''));
        $this->setTasDescription((isset($aData['TAS_DESCRIPTION']) ? $aData['TAS_DESCRIPTION']: ''));
        $this->setTasDefTitle((isset($aData['TAS_DEF_TITLE']) ? $aData['TAS_DEF_TITLE']: ''));
        $this->setTasDefDescription((isset($aData['TAS_DEF_DESCRIPTION']) ? $aData['TAS_DEF_DESCRIPTION']: ''));
        $this->setTasDefProcCode((isset($aData['TAS_DEF_DESCRIPTION']) ? $aData['TAS_DEF_DESCRIPTION']: ''));
        $this->setTasDefMessage((isset($aData['TAS_DEF_MESSAGE']) ? $aData['TAS_DEF_MESSAGE']: ''));
        $this->setTasDefSubjectMessage((isset($aData['TAS_DEF_SUBJECT_MESSAGE']) ? $aData['TAS_DEF_SUBJECT_MESSAGE']: ''));
        $this->save();
        $con->commit();
        return;
      }
      else
      {
        $con->rollback();
        $e=new Exception("Failed Validation in class ".get_class($this).".");
        $e->aValidationFailures=$this->getValidationFailures();
        throw($e);
      }
    }
    catch(Exception $e)
    {
      $con->rollback();
      throw($e);
    }
  }
  function setTasCalendar($taskUid,$calendarUid){
    //Save Calendar ID for this process
  G::LoadClass("calendar");
  $calendarObj=new Calendar();
  $calendarObj->assignCalendarTo($taskUid,$calendarUid,'TASK');
  }

  function getDelegatedTaskData($TAS_UID, $APP_UID, $DEL_INDEX)
  {
    require_once 'classes/model/AppDelegation.php';
		require_once 'classes/model/Task.php';
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
		$aFields['INIT_DATE'] = ($taskData['DEL_INIT_DATE'] != null ? $taskData['DEL_INIT_DATE'] : G::LoadTranslation('ID_CASE_NOT_YET_STARTED'));
		$aFields['DUE_DATE'] = ($taskData['DEL_TASK_DUE_DATE'] != null ? $taskData['DEL_TASK_DUE_DATE'] : G::LoadTranslation('ID_NOT_FINISHED'));
		$aFields['FINISH'] = ($taskData['DEL_FINISH_DATE'] != null ? $taskData['DEL_FINISH_DATE'] : G::LoadTranslation('ID_NOT_FINISHED'));
		$aFields['DURATION'] = ($taskData['DEL_FINISH_DATE'] != null ? (int) ($iDiff / 3600) . ' ' . ((int) ($iDiff / 3600) == 1 ? G::LoadTranslation('ID_HOUR') : G::LoadTranslation('ID_HOURS')) . ' ' . (int) (($iDiff % 3600) / 60) . ' ' . ((int) (($iDiff % 3600) / 60) == 1 ? G::LoadTranslation('ID_MINUTE') : G::LoadTranslation('ID_MINUTES')) . ' ' . (int) (($iDiff % 3600) % 60) . ' ' . ((int) (($iDiff % 3600) % 60) == 1 ? G::LoadTranslation('ID_SECOND') : G::LoadTranslation('ID_SECONDS')) : G::LoadTranslation('ID_NOT_FINISHED'));
    
		return $aFields;
  }
  
  //Added by qennix
  //Gets Starting Event of current task
  function getStartingEvent(){
    require_once 'classes/model/Event.php';
    $oCriteria = new Criteria('workflow');
    $oCriteria->addSelectColumn(EventPeer::EVN_UID);
    $oCriteria->add(EventPeer::EVN_TAS_UID_TO,$this->tas_uid);
    //$oCriteria->add(EventPeer::EVN_TYPE,'bpmnEventMessageStart');
    $oDataset = EventPeer::doSelectRS($oCriteria);
    $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
    if ($oDataset->next()){
      $row = $oDataset->getRow();
      $event_uid = $row['EVN_UID']; 
    }else{
      $event_uid = '';
    }
    return $event_uid;
  }
} // Task
