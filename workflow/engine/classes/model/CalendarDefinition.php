<?php
/**
 * CalendarDefinition.php
 *
 * @package workflow.engine.classes.model
 */

require_once 'classes/model/om/BaseCalendarDefinition.php';
require_once 'classes/model/CalendarBusinessHours.php';
require_once 'classes/model/CalendarHolidays.php';
require_once 'classes/model/CalendarAssignments.php';

/**
 * Skeleton subclass for representing a row from the 'CALENDAR_DEFINITION' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements. This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package workflow.engine.classes.model
 */
class CalendarDefinition extends BaseCalendarDefinition
{
    public $calendarLog = '';

    public function getCalendarList ($onlyActive = false, $arrayMode = false)
    {
        $Criteria = new Criteria( 'workflow' );
        $Criteria->clearSelectColumns();

        $Criteria->addSelectColumn( CalendarDefinitionPeer::CALENDAR_UID );
        $Criteria->addSelectColumn( CalendarDefinitionPeer::CALENDAR_NAME );
        $Criteria->addSelectColumn( CalendarDefinitionPeer::CALENDAR_CREATE_DATE );
        $Criteria->addSelectColumn( CalendarDefinitionPeer::CALENDAR_UPDATE_DATE );
        $Criteria->addSelectColumn( CalendarDefinitionPeer::CALENDAR_DESCRIPTION );
        $Criteria->addSelectColumn( CalendarDefinitionPeer::CALENDAR_STATUS );
        // $Criteria->addAsColumn('DELETABLE', "IF (CALENDAR_UID <> '00000000000000000000000000000001', '".G::LoadTranslation('ID_DELETE')."','') ");
        $Criteria->addAsColumn( 'DELETABLE', "CASE WHEN CALENDAR_UID <> '00000000000000000000000000000001' THEN '" . G::LoadTranslation( 'ID_DELETE' ) . "' ELSE '' END " );
        // Note: This list doesn't show deleted items (STATUS = DELETED)
        if ($onlyActive) {
            // Show only active. Used on assignment lists
            $Criteria->add( calendarDefinitionPeer::CALENDAR_STATUS, "ACTIVE", CRITERIA::EQUAL );
        } else {
            // Show Active and Inactive calendars. USed in main list
            $Criteria->add( calendarDefinitionPeer::CALENDAR_STATUS, array ("ACTIVE","INACTIVE"), CRITERIA::IN );
        }

        $Criteria->add( calendarDefinitionPeer::CALENDAR_UID, "xx", CRITERIA::NOT_EQUAL );

        if (! $arrayMode) {
            return $Criteria;
        } else {
            $oDataset = calendarDefinitionPeer::doSelectRS( $Criteria );
            $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
            $oDataset->next();
            $calendarA = array (0 => 'dummy');
            $calendarCount = 0;
            while (is_array( $aRow = $oDataset->getRow() )) {
                $calendarCount ++;
                $calendarA[$calendarCount] = $aRow;
                $oDataset->next();
            }
            $return['criteria'] = $Criteria;
            $return['array'] = $calendarA;
            return $return;
        }
    }
    //Added by qennix
    //Gets criteria for listing
    public function getCalendarCriterias ($filter, $start, $limit)
    {
        $Criteria = new Criteria( 'workflow' );
        $Criteria->clearSelectColumns();
        $Criteria->addSelectColumn( CalendarDefinitionPeer::CALENDAR_UID );
        if ($filter != '') {
            $Criteria->add( $Criteria->getNewCriterion( CalendarDefinitionPeer::CALENDAR_NAME, '%' . $filter . '%', Criteria::LIKE )->addOr( $Criteria->getNewCriterion( CalendarDefinitionPeer::CALENDAR_DESCRIPTION, '%' . $filter . '%', Criteria::LIKE ) ) );
        }
        $Criteria->add( CalendarDefinitionPeer::CALENDAR_STATUS, 'DELETED', Criteria::NOT_EQUAL );

        $oCriteria = new Criteria( 'workflow' );
        $oCriteria->addSelectColumn( CalendarDefinitionPeer::CALENDAR_UID );
        $oCriteria->addSelectColumn( CalendarDefinitionPeer::CALENDAR_NAME );
        $oCriteria->addSelectColumn( CalendarDefinitionPeer::CALENDAR_CREATE_DATE );
        $oCriteria->addSelectColumn( CalendarDefinitionPeer::CALENDAR_UPDATE_DATE );
        $oCriteria->addSelectColumn( CalendarDefinitionPeer::CALENDAR_DESCRIPTION );
        $oCriteria->addSelectColumn( CalendarDefinitionPeer::CALENDAR_STATUS );
        if ($filter != '') {
            $oCriteria->add( $oCriteria->getNewCriterion( CalendarDefinitionPeer::CALENDAR_NAME, '%' . $filter . '%', Criteria::LIKE )->addOr( $oCriteria->getNewCriterion( CalendarDefinitionPeer::CALENDAR_DESCRIPTION, '%' . $filter . '%', Criteria::LIKE ) ) );
        }
        $oCriteria->add( CalendarDefinitionPeer::CALENDAR_STATUS, 'DELETED', Criteria::NOT_EQUAL );
        $oCriteria->setLimit( $limit );
        $oCriteria->setOffset( $start );

        $criterias = array ();
        $criterias['COUNTER'] = $Criteria;
        $criterias['LIST'] = $oCriteria;
        return $criterias;
    }

    public function getCalendarInfo ($CalendarUid)
    {
        //if exists the row in the database propel will update it, otherwise will insert.
        $tr = CalendarDefinitionPeer::retrieveByPK( $CalendarUid );

        $defaultCalendar['CALENDAR_UID'] = "00000000000000000000000000000001";
        $defaultCalendar['CALENDAR_NAME'] = "Default";
        $defaultCalendar['CALENDAR_CREATE_DATE'] = date( "Y-m-d" );
        $defaultCalendar['CALENDAR_UPDATE_DATE'] = date( "Y-m-d" );
        $defaultCalendar['CALENDAR_DESCRIPTION'] = "Default";
        $defaultCalendar['CALENDAR_STATUS'] = "ACTIVE";
        $defaultCalendar['CALENDAR_WORK_DAYS'] = "1|2|3|4|5";
        $defaultCalendar['CALENDAR_WORK_DAYS'] = explode( "|", "1|2|3|4|5" );
        $defaultCalendar['BUSINESS_DAY'][1]['CALENDAR_BUSINESS_DAY'] = 7;
        $defaultCalendar['BUSINESS_DAY'][1]['CALENDAR_BUSINESS_START'] = "09:00";
        $defaultCalendar['BUSINESS_DAY'][1]['CALENDAR_BUSINESS_END'] = "17:00";
        $defaultCalendar['HOLIDAY'] = array ();

        if ((is_object( $tr ) && get_class( $tr ) == 'CalendarDefinition')) {
            $fields['CALENDAR_UID'] = $tr->getCalendarUid();
            $fields['CALENDAR_NAME'] = $tr->getCalendarName();
            $fields['CALENDAR_CREATE_DATE'] = $tr->getCalendarCreateDate();
            $fields['CALENDAR_UPDATE_DATE'] = $tr->getCalendarUpdateDate();
            $fields['CALENDAR_DESCRIPTION'] = $tr->getCalendarDescription();
            $fields['CALENDAR_STATUS'] = $tr->getCalendarStatus();
            $fields['CALENDAR_WORK_DAYS'] = $tr->getCalendarWorkDays();
            $fields['CALENDAR_WORK_DAYS_A'] = explode( "|", $tr->getCalendarWorkDays() );
        } else {
            $fields = $defaultCalendar;
            $this->saveCalendarInfo( $fields );
            $fields['CALENDAR_WORK_DAYS'] = "1|2|3|4|5";
            $fields['CALENDAR_WORK_DAYS_A'] = explode( "|", "1|2|3|4|5" );
            $tr = CalendarDefinitionPeer::retrieveByPK( $CalendarUid );
        }
        $CalendarBusinessHoursObj = new CalendarBusinessHours();
        $CalendarBusinessHours = $CalendarBusinessHoursObj->getCalendarBusinessHours( $CalendarUid );
        $fields['BUSINESS_DAY'] = $CalendarBusinessHours;
        $CalendarHolidaysObj = new CalendarHolidays();
        $CalendarHolidays = $CalendarHolidaysObj->getCalendarHolidays( $CalendarUid );
        $fields['HOLIDAY'] = $CalendarHolidays;
        $fields = $this->validateCalendarInfo( $fields, $defaultCalendar );
        //********************
        return $fields;
    }
    //for edit
    public function getCalendarInfoE ($CalendarUid)
    {
        //if exists the row in the database propel will update it, otherwise will insert.
        $tr = CalendarDefinitionPeer::retrieveByPK( $CalendarUid );
        $defaultCalendar['CALENDAR_UID'] = "00000000000000000000000000000001";
        $defaultCalendar['CALENDAR_NAME'] = "Default";
        $defaultCalendar['CALENDAR_CREATE_DATE'] = date( "Y-m-d" );
        $defaultCalendar['CALENDAR_UPDATE_DATE'] = date( "Y-m-d" );
        $defaultCalendar['CALENDAR_DESCRIPTION'] = "Default";
        $defaultCalendar['CALENDAR_STATUS'] = "ACTIVE";
        $defaultCalendar['CALENDAR_WORK_DAYS'] = "1|2|3|4|5";
        $defaultCalendar['CALENDAR_WORK_DAYS'] = explode( "|", "1|2|3|4|5" );
        $defaultCalendar['BUSINESS_DAY'][1]['CALENDAR_BUSINESS_DAY'] = 7;
        $defaultCalendar['BUSINESS_DAY'][1]['CALENDAR_BUSINESS_START'] = "09:00";
        $defaultCalendar['BUSINESS_DAY'][1]['CALENDAR_BUSINESS_END'] = "17:00";
        $defaultCalendar['HOLIDAY'] = array ();

        if ((is_object( $tr ) && get_class( $tr ) == 'CalendarDefinition')) {
            $fields['CALENDAR_UID'] = $tr->getCalendarUid();
            $fields['CALENDAR_NAME'] = $tr->getCalendarName();
            $fields['CALENDAR_CREATE_DATE'] = $tr->getCalendarCreateDate();
            $fields['CALENDAR_UPDATE_DATE'] = $tr->getCalendarUpdateDate();
            $fields['CALENDAR_DESCRIPTION'] = $tr->getCalendarDescription();
            $fields['CALENDAR_STATUS'] = $tr->getCalendarStatus();
            $fields['CALENDAR_WORK_DAYS'] = $tr->getCalendarWorkDays();
            $fields['CALENDAR_WORK_DAYS_A'] = explode( "|", $tr->getCalendarWorkDays() );
        } else {
            $fields = $defaultCalendar;
            $this->saveCalendarInfo( $fields );
            $fields['CALENDAR_WORK_DAYS'] = "1|2|3|4|5";
            $fields['CALENDAR_WORK_DAYS_A'] = explode( "|", "1|2|3|4|5" );
            $tr = CalendarDefinitionPeer::retrieveByPK( $CalendarUid );
        }
        $CalendarBusinessHoursObj = new CalendarBusinessHours();
        $CalendarBusinessHours = $CalendarBusinessHoursObj->getCalendarBusinessHours( $CalendarUid );
        $fields['BUSINESS_DAY'] = $CalendarBusinessHours;
        $CalendarHolidaysObj = new CalendarHolidays();
        $CalendarHolidays = $CalendarHolidaysObj->getCalendarHolidays( $CalendarUid );
        $fields['HOLIDAY'] = $CalendarHolidays;
        // $fields=$this->validateCalendarInfo($fields, $defaultCalendar); //********************
        return $fields;
    }
    //end for edit

    public function validateCalendarInfo ($fields, $defaultCalendar)
    {
        try {
            //Validate if Working days are Correct
            //Minimun 3 ?
            $workingDays = explode( "|", $fields['CALENDAR_WORK_DAYS'] );
            if (count( $workingDays ) < 3) {
                throw (new Exception( "You must define at least 3 Working Days!" ));
            }
            //Validate that all Working Days have Bussines Hours
            if (count( $fields['BUSINESS_DAY'] ) < 1) {
                throw (new Exception( "You must define at least one Business Day for all days" ));
            }
            $workingDaysOK = array ();
            foreach ($workingDays as $key => $day) {
                $workingDaysOK[$day] = false;
            }
            $sw_all = false;
            foreach ($fields['BUSINESS_DAY'] as $keyB => $businessHours) {
                if (($businessHours['CALENDAR_BUSINESS_DAY'] == 7)) {
                    $sw_all = true;
                } elseif ((in_array( $businessHours['CALENDAR_BUSINESS_DAY'], $workingDays ))) {
                    $workingDaysOK[$businessHours['CALENDAR_BUSINESS_DAY']] = true;
                }
            }
            $sw_days = true;

            foreach ($workingDaysOK as $day => $sw_day) {
                $sw_days = $sw_days && $sw_day;
            }
            if (! ($sw_all || $sw_days)) {
                throw (new Exception( "Not all working days have their correspondent business day" ));
            }
            //Validate Holidays
            return $fields;
        } catch (Exception $e) {
            //print $e->getMessage();
            $this->addCalendarLog( "!!!!!!! BAD CALENDAR DEFINITION. " . $e->getMessage() );
            $defaultCalendar['CALENDAR_WORK_DAYS'] = "1|2|3|4|5";
            $defaultCalendar['CALENDAR_WORK_DAYS_A'] = explode( "|", "1|2|3|4|5" );
            return $defaultCalendar;
        }

    }

    public function saveCalendarInfo ($aData)
    {
        $CalendarUid = $aData['CALENDAR_UID'];
        $CalendarName = $aData['CALENDAR_NAME'];
        $CalendarDescription = $aData['CALENDAR_DESCRIPTION'];
        $CalendarStatus = isset( $aData['CALENDAR_STATUS'] ) ? $aData['CALENDAR_STATUS'] : "INACTIVE";
        $defaultCalendars[] = '00000000000000000000000000000001';
        if (in_array( $aData['CALENDAR_UID'], $defaultCalendars )) {
            $CalendarStatus = 'ACTIVE';
            $CalendarName = 'Default';
        }
        $CalendarWorkDays = isset( $aData['CALENDAR_WORK_DAYS'] ) ? implode( "|", $aData['CALENDAR_WORK_DAYS'] ) : "";

        //if exists the row in the database propel will update it, otherwise will insert.
        $tr = CalendarDefinitionPeer::retrieveByPK( $CalendarUid );
        if (! (is_object( $tr ) && get_class( $tr ) == 'CalendarDefinition')) {
            $tr = new CalendarDefinition();
            $tr->setCalendarCreateDate( 'now' );
        }
        $tr->setCalendarUid( $CalendarUid );
        $tr->setCalendarName( $CalendarName );
        $tr->setCalendarUpdateDate( 'now' );
        $tr->setCalendarDescription( $CalendarDescription );
        $tr->setCalendarStatus( $CalendarStatus );
        $tr->setCalendarWorkDays( $CalendarWorkDays );

        if ($tr->validate()) {
            // we save it, since we get no validation errors, or do whatever else you like.
            $res = $tr->save();
            //Calendar Business Hours Save code.
            //First Delete all current records
            $CalendarBusinessHoursObj = new CalendarBusinessHours();
            $CalendarBusinessHoursObj->deleteAllCalendarBusinessHours( $CalendarUid );
            //Save all the sent records
            foreach ($aData['BUSINESS_DAY'] as $key => $objData) {
                $objData['CALENDAR_UID'] = $CalendarUid;
                $CalendarBusinessHoursObj->saveCalendarBusinessHours( $objData );
            }
            //Holiday Save code.
            //First Delete all current records
            $CalendarHolidayObj = new CalendarHolidays();
            $CalendarHolidayObj->deleteAllCalendarHolidays( $CalendarUid );
            //Save all the sent records
            foreach ($aData['HOLIDAY'] as $key => $objData) {
                if (($objData['CALENDAR_HOLIDAY_NAME'] != "") && ($objData['CALENDAR_HOLIDAY_START'] != "") && ($objData['CALENDAR_HOLIDAY_END'] != "")) {
                    $objData['CALENDAR_UID'] = $CalendarUid;
                    $CalendarHolidayObj->saveCalendarHolidays( $objData );
                }
            }
        } else {
            // Something went wrong. We can now get the validationFailures and handle them.
            $msg = '';
            $validationFailuresArray = $tr->getValidationFailures();
            foreach ($validationFailuresArray as $objValidationFailure) {
                $msg .= $objValidationFailure->getMessage() . "<br/>";
            }
            //return array ( 'codError' => -100, 'rowsAffected' => 0, 'message' => $msg );
        }
        //return array ( 'codError' => 0, 'rowsAffected' => $res, 'message' => '');
        //to do: uniform  coderror structures for all classes
        //if ( $res['codError'] < 0 ) {
        //  G::SendMessageText ( $res['message'] , 'error' );
        //}
    }

    public function deleteCalendar ($CalendarUid)
    {
        //if exists the row in the database propel will update it, otherwise will insert.
        $tr = CalendarDefinitionPeer::retrieveByPK( $CalendarUid );

        if (! (is_object( $tr ) && get_class( $tr ) == 'CalendarDefinition')) {
            //
            return false;
        }

        $defaultCalendars[] = '00000000000000000000000000000001';
        if (in_array( $tr->getCalendarUid(), $defaultCalendars )) {
            return false;
        }

        $tr->setCalendarStatus( 'DELETED' );
        $tr->setCalendarUpdateDate( 'now' );
        if ($tr->validate()) {
            // we save it, since we get no validation errors, or do whatever else you like.
            $res = $tr->save();
        } else {
            // Something went wrong. We can now get the validationFailures and handle them.
            $msg = '';
            $validationFailuresArray = $tr->getValidationFailures();
            foreach ($validationFailuresArray as $objValidationFailure) {
                $msg .= $objValidationFailure->getMessage() . "<br/>";
            }
            G::SendMessage( "ERROR", $msg );
            //return array ( 'codError' => -100, 'rowsAffected' => 0, 'message' => $msg );
        }
        //return array ( 'codError' => 0, 'rowsAffected' => $res, 'message' => '');
        //to do: uniform  coderror structures for all classes
        //if ( $res['codError'] < 0 ) {
        //  G::SendMessageText ( $res['message'] , 'error' );
        //}
    }

    public function getCalendarFor ($userUid, $proUid, $tasUid, $sw_validate = true)
    {
        $Criteria = new Criteria( 'workflow' );
        //Default Calendar
        $calendarUid = "00000000000000000000000000000001";
        $calendarOwner = "DEFAULT";
        //Load User,Task and Process calendars (if exist)
        $Criteria->addSelectColumn( CalendarAssignmentsPeer::CALENDAR_UID );
        $Criteria->addSelectColumn( CalendarAssignmentsPeer::OBJECT_UID );
        $Criteria->addSelectColumn( CalendarAssignmentsPeer::OBJECT_TYPE );
        $Criteria->add( CalendarAssignmentsPeer::OBJECT_UID, array ($userUid,$proUid,$tasUid), CRITERIA::IN );
        $oDataset = CalendarAssignmentsPeer::doSelectRS( $Criteria );
        $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
        $oDataset->next();
        $calendarArray = array ();
        while (is_array( $aRow = $oDataset->getRow() )) {
            if ($aRow['OBJECT_UID'] == $userUid) {
                $calendarArray['USER'] = $aRow['CALENDAR_UID'];
            }
            if ($aRow['OBJECT_UID'] == $proUid) {
                $calendarArray['PROCESS'] = $aRow['CALENDAR_UID'];
            }
            if ($aRow['OBJECT_UID'] == $tasUid) {
                $calendarArray['TASK'] = $aRow['CALENDAR_UID'];
            }
            $oDataset->next();
        }

        if (isset( $calendarArray['USER'] )) {
            $calendarUid = $calendarArray['USER'];
            $calendarOwner = "USER";
        } elseif (isset( $calendarArray['PROCESS'] )) {
            $calendarUid = $calendarArray['PROCESS'];
            $calendarOwner = "PROCESS";
        } elseif (isset( $calendarArray['TASK'] )) {
            $calendarUid = $calendarArray['TASK'];
            $calendarOwner = "TASK";
        }

        //print "<h1>$calendarUid</h1>";
        if ($sw_validate) {
            $calendarDefinition = $this->getCalendarInfo( $calendarUid );
        } else {
            $calendarDefinition = $this->getCalendarInfoE( $calendarUid );
        }
        $calendarDefinition['CALENDAR_APPLIED'] = $calendarOwner;
        $this->addCalendarLog( "--=== Calendar Applied: " . $calendarDefinition['CALENDAR_NAME'] . " -> $calendarOwner" );
        return $calendarDefinition;
    }

    public function assignCalendarTo ($objectUid, $calendarUid, $objectType)
    {
        //if exists the row in the database propel will update it, otherwise will insert.
        $tr = CalendarAssignmentsPeer::retrieveByPK( $objectUid );
        if ($calendarUid != "") {
            if (! (is_object( $tr ) && get_class( $tr ) == 'CalendarAssignments')) {
                $tr = new CalendarAssignments();
            }
            $tr->setObjectUid( $objectUid );
            $tr->setCalendarUid( $calendarUid );
            $tr->setObjectType( $objectType );

            if ($tr->validate()) {
                // we save it, since we get no validation errors, or do whatever else you like.
                $res = $tr->save();
            } else {
                // Something went wrong. We can now get the validationFailures and handle them.
                $msg = '';
                $validationFailuresArray = $tr->getValidationFailures();
                foreach ($validationFailuresArray as $objValidationFailure) {
                    $msg .= $objValidationFailure->getMessage() . "<br/>";
                }
                //return array ( 'codError' => -100, 'rowsAffected' => 0, 'message' => $msg );
            }
        } else {
            //Delete record
            if ((is_object( $tr ) && get_class( $tr ) == 'CalendarAssignments')) {
                $tr->delete();
            }
        }
    }
    //Added by Qennix
    //Counts all users,task,process by calendar
    public function getAllCounterByCalendar ($type)
    {
        $oCriteria = new Criteria( 'workflow' );
        $oCriteria->addSelectColumn( CalendarAssignmentsPeer::CALENDAR_UID );
        $oCriteria->addSelectColumn( 'COUNT(*) AS CNT' );
        $oCriteria->addGroupByColumn( CalendarAssignmentsPeer::CALENDAR_UID );
        $oCriteria->add( CalendarAssignmentsPeer::OBJECT_TYPE, $type );
        $oDataset = CalendarAssignmentsPeer::doSelectRS( $oCriteria );
        $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
        $aCounter = Array ();
        while ($oDataset->next()) {
            $row = $oDataset->getRow();
            $aCounter[$row['CALENDAR_UID']] = $row['CNT'];
        }
        return $aCounter;
    }

    public function loadByCalendarName ($calendarName)
    {
        $Criteria = new Criteria( 'workflow' );
        $Criteria->addSelectColumn( CalendarDefinitionPeer::CALENDAR_UID );
        $Criteria->addSelectColumn( CalendarDefinitionPeer::CALENDAR_NAME );
        $Criteria->addSelectColumn( CalendarDefinitionPeer::CALENDAR_CREATE_DATE );
        $Criteria->addSelectColumn( CalendarDefinitionPeer::CALENDAR_UPDATE_DATE );
        $Criteria->addSelectColumn( CalendarDefinitionPeer::CALENDAR_DESCRIPTION );
        $Criteria->addSelectColumn( CalendarDefinitionPeer::CALENDAR_STATUS );
        $Criteria->add( calendarDefinitionPeer::CALENDAR_NAME, $calendarName, CRITERIA::EQUAL );
        $oDataset = calendarDefinitionPeer::doSelectRS( $Criteria );
        $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
        $oDataset->next();
        return $oDataset->getRow();
    }
}
// CalendarDefinition

