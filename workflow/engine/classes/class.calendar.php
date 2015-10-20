<?php
/**
 * class.calendar.php
 *
 * @package workflow.engine.classes
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
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 * For more information, contact Colosa Inc, 2566 Le Jeune Rd.,
 * Coral Gables, FL, 33134, USA, or email info@colosa.com.
 *
 *
 *
 * @name calendar
 * created 2010-03-22
 *
 * @author Hugo Loza <hugo@colosa.com> 2010-03-22 *
 */

require_once ("classes/model/CalendarDefinition.php");

/**
 * A Calendar object where it is defined Working Days, Business Hours and Holidays
 * A Calendar is applied to a User, Process or Task
 * Extends CalendarDefinition.
 *
 * @author Hugo Loza <hugo@colosa.com> 2010-03-22
 * @uses CalendarDefinition
 * @package workflow.engine.classes
 *
 */
class calendar extends CalendarDefinition
{
	public $pmCalendarUid = '';
	public $pmCalendarData = array();

	public function getCalendar ($userUid, $proUid = null, $tasUid = null)
	{
		require_once 'classes/model/CalendarAssignments.php';

		$criteria = new Criteria ( 'workflow' );
		$criteria->clearSelectColumns ( );

		$calendarData = array();

		//Default Calendar
		$calendarData['UID']  = '00000000000000000000000000000001';
		$calendarData['TYPE'] = 'DEFAULT';

		//Load User,Task and Process calendars (if exist)
		$criteria->addSelectColumn ( CalendarAssignmentsPeer::CALENDAR_UID );
		$criteria->addSelectColumn ( CalendarAssignmentsPeer::OBJECT_UID );
		$criteria->addSelectColumn ( CalendarAssignmentsPeer::OBJECT_TYPE );
		$criteria->add ( CalendarAssignmentsPeer::OBJECT_UID, array($userUid, $proUid, $tasUid), CRITERIA::IN );
		$oDataset = CalendarAssignmentsPeer::doSelectRS ( $criteria );
		$oDataset->setFetchmode ( ResultSet::FETCHMODE_ASSOC );
		$oDataset->next ();

		$calendarArray = array();
		while (is_array($aRow = $oDataset->getRow ())) {
			if ($aRow['OBJECT_UID']==$userUid) {
				$calendarArray['USER'] = $aRow ['CALENDAR_UID'];
			}
			if ($aRow['OBJECT_UID']==$proUid) {
				$calendarArray['PROCESS'] = $aRow ['CALENDAR_UID'];
			}
			if ($aRow['OBJECT_UID']==$tasUid) {
				$calendarArray['TASK'] = $aRow ['CALENDAR_UID'];
			}
			$oDataset->next ();
		}

		if (isset($calendarArray['USER'])) {
			$calendarData['UID']  = $calendarArray['USER'];
			$calendarData['TYPE'] = 'USER';
		} elseif (isset($calendarArray['PROCESS'])) {
			$calendarData['UID']  = $calendarArray['PROCESS'];
			$calendarData['TYPE'] = 'PROCESS';
		} elseif (isset($calendarArray['TASK'])) {
			$calendarData['UID']  = $calendarArray['TASK'];
			$calendarData['TYPE'] = 'TASK';
		}

		$this->pmCalendarUid = $calendarData['UID'];
		return $this->pmCalendarUid;
	}

	public function getCalendarData ($calendarUid = null)
	{
		require_once ( 'classes/model/CalendarDefinition.php' );

		$calendarUid = (is_null($calendarUid)) ? $this->pmCalendarUid : $calendarUid;
		$this->pmCalendarUid = $calendarUid;

		//if exists the row in the database propel will update it, otherwise will insert.
		$tr = CalendarDefinitionPeer::retrieveByPK ( $calendarUid );

		$defaultCalendar ['CALENDAR_UID'] = '00000000000000000000000000000001';
		$defaultCalendar ['CALENDAR_NAME'] = 'Default';
		$defaultCalendar ['CALENDAR_CREATE_DATE'] = date ( 'Y-m-d' );
		$defaultCalendar ['CALENDAR_UPDATE_DATE'] = date ( 'Y-m-d' );
		$defaultCalendar ['CALENDAR_DESCRIPTION'] = 'Default';
		$defaultCalendar ['CALENDAR_STATUS'] = 'ACTIVE';
		$defaultCalendar ['CALENDAR_WORK_DAYS'] = '1|2|3|4|5';
		$defaultCalendar ['CALENDAR_WORK_DAYS'] = explode ( '|', '1|2|3|4|5' );
		$defaultCalendar ['BUSINESS_DAY'] [1] ['CALENDAR_BUSINESS_DAY'] = 7;
		$defaultCalendar ['BUSINESS_DAY'] [1] ['CALENDAR_BUSINESS_START'] = '09:00';
		$defaultCalendar ['BUSINESS_DAY'] [1] ['CALENDAR_BUSINESS_END'] = '17:00';
		$defaultCalendar ['BUSINESS_DAY'] [1] ['DIFF_HOURS'] = '8';
		$defaultCalendar ['HOURS_FOR_DAY'] = '8';
		$defaultCalendar ['HOLIDAY'] = array ();

		if ((is_object ( $tr ) && get_class ( $tr ) == 'CalendarDefinition')) {
			$fields ['CALENDAR_UID'] = $tr->getCalendarUid ();
			$fields ['CALENDAR_NAME'] = $tr->getCalendarName ();
			$fields ['CALENDAR_CREATE_DATE'] = $tr->getCalendarCreateDate ();
			$fields ['CALENDAR_UPDATE_DATE'] = $tr->getCalendarUpdateDate ();
			$fields ['CALENDAR_DESCRIPTION'] = $tr->getCalendarDescription ();
			$fields ['CALENDAR_STATUS'] = $tr->getCalendarStatus ();
			$fields ['CALENDAR_WORK_DAYS'] = $tr->getCalendarWorkDays ();
			$fields ['CALENDAR_WORK_DAYS_A'] = explode ( '|', $tr->getCalendarWorkDays () );
		} else {
			$fields=$defaultCalendar;
			//$this->saveCalendarInfo ( $fields );
			$fields ['CALENDAR_WORK_DAYS'] = '1|2|3|4|5';
			$fields ['CALENDAR_WORK_DAYS_A'] = explode ( '|', '1|2|3|4|5' );
			//$tr = CalendarDefinitionPeer::retrieveByPK ( $calendarUid );
		}

		$CalendarBusinessHoursObj = new CalendarBusinessHours ( );
		$CalendarBusinessHours = $this->getCalendarBusinessHours ( $calendarUid );

		$numDay = 8;
		$daysHours = array();
		$hoursCant = array();
		$modaHours = 0;
		$keyModa = 0;
		foreach ($CalendarBusinessHours as $value) {
			if ($value['CALENDAR_BUSINESS_DAY'] != $numDay) {
				$numDay = $value['CALENDAR_BUSINESS_DAY'];
				$daysHours[$numDay] = 0;
			}
			$daysHours[$numDay] += $value['DIFF_HOURS'];
		}
		foreach ($daysHours as $value) {
			if (isset($hoursCant[$value])) {
				$hoursCant[$value]++;
			} else {
				$hoursCant[$value] = 1;
			}
		}

		foreach ($hoursCant as $key => $value) {
			if ($value > $modaHours ) {
				$modaHours = $value;
				$keyModa = $key;
			}
		}

		$fields ['HOURS_FOR_DAY'] = $keyModa;
		$fields ['BUSINESS_DAY'] = $CalendarBusinessHours;

		$CalendarHolidaysObj = new CalendarHolidays ( );
		$CalendarHolidays = $this->getCalendarHolidays ( $calendarUid );
		$fields ['HOLIDAY'] = $CalendarHolidays;
		$fields=$this->validateCalendarInfo($fields, $defaultCalendar);

		$this->pmCalendarData = $fields;
		return $this->pmCalendarData;
	}

	public function getCalendarBusinessHours ($calendarUid = null)
	{
		require_once ( 'classes/model/CalendarBusinessHours.php' );

		$calendarUid = (is_null($calendarUid)) ? $this->pmCalendarUid : $calendarUid;
		$this->pmCalendarUid = $calendarUid;

		$criteria = new Criteria('workflow');
		$criteria->clearSelectColumns ( );

		$criteria->addSelectColumn (  CalendarBusinessHoursPeer::CALENDAR_UID );
		$criteria->addSelectColumn (  CalendarBusinessHoursPeer::CALENDAR_BUSINESS_DAY );
		$criteria->addSelectColumn (  CalendarBusinessHoursPeer::CALENDAR_BUSINESS_START );
		$criteria->addSelectColumn (  CalendarBusinessHoursPeer::CALENDAR_BUSINESS_END );

		$criteria->add (  CalendarBusinessHoursPeer::CALENDAR_UID, $calendarUid , CRITERIA::EQUAL );
		$criteria->addDescendingOrderByColumn ( CalendarBusinessHoursPeer::CALENDAR_BUSINESS_DAY );
		$criteria->addAscendingOrderByColumn ( CalendarBusinessHoursPeer::CALENDAR_BUSINESS_START );

		$rs = CalendarBusinessHoursPeer::doSelectRS($criteria);
		$rs->setFetchmode(ResultSet::FETCHMODE_ASSOC);
		$rs->next();
		$row = $rs->getRow();

		$fields = array();
		$count = 0;

		while (is_array($row)) {
			$count++;
			$iniTime = (float)str_replace(':', '', $row['CALENDAR_BUSINESS_START']);
			$finTime = (float)str_replace(':', '', $row['CALENDAR_BUSINESS_END']);
			$row['DIFF_HOURS'] = (($finTime-$iniTime)/100);
			$fields[$count] = $row;
			$rs->next();
			$row = $rs->getRow();
		}

		return $fields;
	}

	public function getCalendarHolidays ($calendarUid = null)
	{
		require_once ( 'classes/model/CalendarHolidays.php' );

		$calendarUid = (is_null($calendarUid)) ? $this->pmCalendarUid : $calendarUid;
		$this->pmCalendarUid = $calendarUid;

		$criteria = new Criteria('workflow');
		$criteria->clearSelectColumns ( );

		$criteria->addSelectColumn (  CalendarHolidaysPeer::CALENDAR_UID );
		$criteria->addSelectColumn (  CalendarHolidaysPeer::CALENDAR_HOLIDAY_NAME );
		$criteria->addSelectColumn (  CalendarHolidaysPeer::CALENDAR_HOLIDAY_START );
		$criteria->addSelectColumn (  CalendarHolidaysPeer::CALENDAR_HOLIDAY_END );

		$criteria->add (  CalendarHolidaysPeer::CALENDAR_UID, $calendarUid , CRITERIA::EQUAL );

		$rs = CalendarHolidaysPeer::doSelectRS($criteria);
		$rs->setFetchmode(ResultSet::FETCHMODE_ASSOC);
		$rs->next();
		$row = $rs->getRow();
		$fields=array();
	
		$count=0;

		while (is_array($row)) {
			$count++;
			$a=explode(' ',$row['CALENDAR_HOLIDAY_START']);
			$row['CALENDAR_HOLIDAY_START']=$a[0];
			$a=explode(' ',$row['CALENDAR_HOLIDAY_END']);
			$row['CALENDAR_HOLIDAY_END']=$a[0];
			$fields[$count] = $row;
			$rs->next();
			$row = $rs->getRow();
		}

		return $fields;
	}

	public function validateCalendarInfo ($fields, $defaultCalendar)
	{
		try {
			//Validate if Working days are Correct
			//Minimun 3 ?
			$workingDays=explode ( '|', $fields['CALENDAR_WORK_DAYS'] );
			if (count($workingDays)<3) {
				throw (new Exception ( 'You must define at least 3 Working Days!' ));
			}
			//Validate that all Working Days have Bussines Hours
			if (count($fields ['BUSINESS_DAY'])<1) {
				throw (new Exception ( 'You must define at least one Business Day for all days' ));
			}

			$workingDaysOK=array();
			foreach ($workingDays as $key => $day) {
				$workingDaysOK[$day]=false;
			}

			$sw_all = false;
			foreach ($fields ['BUSINESS_DAY'] as $keyB => $businessHours) {
				if (($businessHours['CALENDAR_BUSINESS_DAY']==7)) {
					$sw_all=true;
				} elseif((in_array($businessHours['CALENDAR_BUSINESS_DAY'],$workingDays))) {
					$workingDaysOK[$businessHours['CALENDAR_BUSINESS_DAY']]=true;
				}
			}

			$sw_days = true;
			foreach ($workingDaysOK as $day => $sw_day) {
				$sw_days = $sw_days && $sw_day;
			}

			if (!($sw_all || $sw_days)) {
				throw (new Exception ( 'Not all working days have their correspondent business day' ));
			}
			//Validate Holidays

			return $fields;
		} catch (Exception $e) {
			//print $e->getMessage();
			//$this->addCalendarLog('!!!!!!! BAD CALENDAR DEFINITION. '.$e->getMessage());
			$defaultCalendar ['CALENDAR_WORK_DAYS'] = '1|2|3|4|5';
			$defaultCalendar ['CALENDAR_WORK_DAYS_A'] = explode ( '|', '1|2|3|4|5' );
			return $defaultCalendar;
		}
	}

    /**
     *
     * @param string(32) $userUid
     * @param string(32) $proUid
     * @param string(32) $tasUid
     */
    function calendar ($userUid = NULL, $proUid = NULL, $tasUid = NULL)
    {
        $this->userUid = $userUid;
        $this->proUid = $proUid;
        $this->tasUid = $tasUid;
        $this->calendarLog = "";
        $this->setupCalendar( $userUid, $proUid, $tasUid );
    }

    /**
     * Small function used to add important information about the calcs and actions
     * to the log (that log will be saved)
     *
     * @name addCalendarLog
     * @param text $msg
     * @access public
     *
     */
    function addCalendarLog ($msg)
    {
        $this->calendarLog .= "\n" . date( "D M j G:i:s T Y" ) . ": " . $msg;
    }

    /**
     * setupCalendar is used to generate a valid instance of calendar using $userUid, $proUid and $tasUid
     * to find a valid calendar.
     * If there is no valid calendar then use the Default
     *
     * @name setupCalendar
     * @param string(32) $userUid
     * @param string(32) $proUid
     * @param string(32) $tasUid
     * @return void
     */
    function setupCalendar ($userUid, $proUid, $tasUid)
    {
        $calendarDefinition = $this->getCalendarFor( $userUid, $proUid, $tasUid );
        $this->calendarUid = $calendarDefinition['CALENDAR_UID'];
        $this->calendarDefinition = $calendarDefinition;
    }

    //// FUNTION CALCULATE DATE
    public function calculateDate ($iniDate, $duration, $formatDuration, $calendarData = array())
    {
    	$calendarData = (count($calendarData)) ? $calendarData : $this->pmCalendarData;
    	$this->pmCalendarData = $calendarData;

    	if ( G::toUpper($formatDuration) == 'DAYS' ) {
    		$duration = $duration*$this->pmCalendarData['HOURS_FOR_DAY'];
    	}

    	/*
    	 $log = array();
    	$titles = array();
    	$titles[] = 'HOURS';
    	$titles[] = 'DATE';
    	$titles[] = '**DAY';
    	$titles[] = '**RANGE';
    	$titles[] = '**HOURS RANGE';
    	$titles[] = '**SUM HOURS';
    	$titles[] = '**NEXT DATE';
    	$log[] = $titles;

    	$dataLog = array();
    	$dataLog[] = $duration;
    	$dataLog[] = $iniDate;
    	$dataLog[] = '-----';
    	$dataLog[] = '-----';
    	$dataLog[] = '-----';
    	$dataLog[] = '-----';
    	$dataLog[] = '-----';
    	$log[] = $dataLog;
    	*/
    	$hoursDuration = (float)$duration;
    	$newDate = $iniDate;
    	
    	if ( G::toUpper($formatDuration) == 'MINUTES' ) {
    		$hoursDuration = $duration / 60;
    	}

    	while ($hoursDuration > 0) {
    		//$dataLog = array();
    		$newDate = $this->getIniDate($newDate);
    
    		//$dataLog[] = $hoursDuration;
    		//$dataLog[] = $newDate;
    
    		$rangeWorkHour = $this->getRangeWorkHours($newDate, $calendarData['BUSINESS_DAY']);
    		$onlyDate = (date('Y-m-d',strtotime($newDate))) . ' ' . $rangeWorkHour['END'];
    
    		//$dataLog[] = date('l',strtotime($newDate));
    		//$dataLog[] = $rangeWorkHour['START'] . ' / ' . $rangeWorkHour['END'];
    		//$dataLog[] = $rangeWorkHour['TOTAL'];
    
    		if ( (((float)$hoursDuration) >= ((float)$rangeWorkHour['TOTAL'])) ||
    				((strtotime($onlyDate) - strtotime($newDate)) < (((float)$hoursDuration)*3600))
    		) {
    			$secondRes = (float)(strtotime($onlyDate) - strtotime($newDate));
    			$newDate = $onlyDate;
    			$hoursDuration -= (float)($secondRes/3600);
    			//$dataLog[] = (float)($secondRes/3600);
    		} else {
    			$newDate = date("Y-m-d H:i:s", strtotime("+" . round(((float)($hoursDuration)) * 3600) . " seconds", strtotime($newDate)));
    			//$dataLog[] = (float)($hoursDuration);
    			$hoursDuration = 0;
    		}
    		//$dataLog[] = $newDate;
    		//$log[] = $dataLog;
    	}

    	//$this->showLog($log);
    	$result['DUE_DATE'] = $newDate;
    	$result['DUE_DATE_SECONDS'] = strtotime($newDate);
    	return $result;
    }
    
    public function calculateDuration ($iniDate, $finDate = null, $calendarData = array())
    {
    	if ((is_null($finDate)) || ($finDate == '')) {
    		$finDate = date('Y-m-d H:i:s');
    	}
    
    	$calendarData = (count($calendarData)) ? $calendarData : $this->pmCalendarData;
    	$this->pmCalendarData = $calendarData;
    
    	$secondDuration = 0.00;
    
    	if ( (strtotime($iniDate)) < (strtotime($finDate)) ) {
    		$timeIniDate = strtotime($iniDate);
    		$timeFinDate = strtotime($finDate);
    
    	} elseif ( (strtotime($finDate)) < (strtotime($iniDate)) ) {
    		$timeIniDate = strtotime($finDate);
    		$timeFinDate = strtotime($iniDate);
    		$auxDate = $iniDate;
    		$iniDate = $finDate;
    		$finDate = $auxDate;
    	} else {
    		return $secondDuration;
    	}
    
    	$finDate = $this->getIniDate($finDate);
    	$newDate = $iniDate;
    	while ($timeIniDate < $timeFinDate) {
    		//$dataLog = array();
    		$newDate = $this->getIniDate($newDate);
    
    		//$dataLog[] = $hoursDuration;
    		//$dataLog[] = $newDate;
    
    		$rangeWorkHour = $this->getRangeWorkHours($newDate, $calendarData['BUSINESS_DAY']);
    		$onlyDate = (date('Y-m-d',strtotime($newDate))) . ' ' . $rangeWorkHour['END'];
    
    		//$dataLog[] = date('l',strtotime($newDate));
    		//$dataLog[] = $rangeWorkHour['START'] . ' / ' . $rangeWorkHour['END'];
    		//$dataLog[] = $rangeWorkHour['TOTAL'];
    
    		if ( (strtotime($finDate)) < (strtotime($onlyDate)) ) {
    			$secondRes = ( ((float)strtotime($finDate)) - ((float)strtotime($newDate)) );
    			$timeIniDate = strtotime($finDate);
    			$secondDuration += (float)$secondRes;
    		} else {
    			$secondRes = ( ((float)strtotime($onlyDate)) - ((float)strtotime($newDate)) );
    			$newDate = $onlyDate;
    			$timeIniDate = strtotime($onlyDate);
    			$secondDuration += (float)$secondRes;
    		}
    		//$dataLog[] = $newDate;
    		//$log[] = $dataLog;
    	}
    	return $secondDuration;
    }
    
    public function getRangeWorkHours ($date, $workHours)
    {
    	$auxIniDate = explode(' ', $date);
    	$timeDate = $auxIniDate['1'];
    	$timeDate = (float)str_replace(':', '', ((strlen($timeDate) == 8) ? $timeDate : $timeDate.':00') );
    	$weekDay = date('w',strtotime($date));
    
    	$workHoursDay = array();
    	$tempWorkHoursDay = array();
    
    	foreach ($workHours as $value) {
    		if ($value['CALENDAR_BUSINESS_DAY'] == $weekDay) {
    			$rangeWorkHour = array();
    			$timeStart = $value['CALENDAR_BUSINESS_START'];
    			$timeEnd   = $value['CALENDAR_BUSINESS_END'];
    			$rangeWorkHour['START'] = ((strlen($timeStart) == 8) ? $timeStart : $timeStart.':00');
    			$rangeWorkHour['END']   = ((strlen($timeEnd) == 8) ? $timeEnd : $timeEnd.':00');
    
    			$workHoursDay[] = $rangeWorkHour;
    		}
    
    		if ($value['CALENDAR_BUSINESS_DAY'] == '7') {
    			$rangeWorkHour = array();
    			$timeStart = $value['CALENDAR_BUSINESS_START'];
    			$timeEnd   = $value['CALENDAR_BUSINESS_END'];
    			$rangeWorkHour['START'] = ((strlen($timeStart) == 8) ? $timeStart : $timeStart.':00');
    			$rangeWorkHour['END']   = ((strlen($timeEnd) == 8) ? $timeEnd : $timeEnd.':00');
    
    			$tempWorkHoursDay[] = $rangeWorkHour;
    		}
    	}
    
    	if ( !(count($workHoursDay)) ) {
    		$workHoursDay = $tempWorkHoursDay;
    	}
    
    	foreach ($workHoursDay as $value) {
    		$iniTime = (float)str_replace(':', '', $value['START']);
    		$finTime = (float)str_replace(':', '', $value['END']);
    
    		if ( ($iniTime <= $timeDate)  && ($timeDate <= $finTime) ) {
    			//pr($finTime .' menos '.$iniTime .' = '.($finTime-$iniTime));
    			$value['TOTAL'] = (($finTime-$iniTime)/10000);
    			return $value;
    		}
    	}
    	return false;
    }
    
    public function getIniDate ($iniDate, $calendarData = array())
    {
    	$calendarData = (count($calendarData)) ? $calendarData : $this->pmCalendarData;
    	$this->pmCalendarData = $calendarData;
    	$flagIniDate = true;

    	while ($flagIniDate) {
    		// 1 if it's a work day
    		$weekDay = date('w',strtotime($iniDate));
    		if ( !(in_array($weekDay, $calendarData['CALENDAR_WORK_DAYS_A'])) ) {
    			$iniDate = date('Y-m-d'.' 00:00:00' , strtotime('+1 day', strtotime($iniDate)));
    			continue;
    		}

    		// 2 if it's a holiday
    		$iniDateHolidayDay = $this->is_holiday($iniDate);
    		if ($iniDateHolidayDay) {
    			$iniDate = date('Y-m-d'.' 00:00:00' , strtotime('+1 day', strtotime($iniDate)));
    			continue;
    		}

    		// 3 if it's work time
    		$workHours = $this->nextWorkHours($iniDate, $weekDay);
    		if ( !($workHours['STATUS']) ) {
    			$iniDate = date('Y-m-d'.' 00:00:00' , strtotime('+1 day', strtotime($iniDate)));
    			continue;
    		} else {
    			$iniDate = $workHours['DATE'];
    		}

    		$flagIniDate = false;
    	}

    	return $iniDate;
    }
    
    public function nextWorkHours ($date, $weekDay, $workHours = array())
    {
    	$workHours = (count($workHours)) ? $workHours : $this->pmCalendarData['BUSINESS_DAY'];
    
    	$auxIniDate = explode(' ', $date);
    	$timeDate = $auxIniDate['1'];
    	$timeDate = (float)str_replace(':', '', ((strlen($timeDate) == 8) ? $timeDate : $timeDate.':00') );
    	$nextWorkHours = array();
    
    	$workHoursDay = array();
    	$tempWorkHoursDay = array();
    
    	foreach ($workHours as $value) {
    		if ($value['CALENDAR_BUSINESS_DAY'] == $weekDay) {
    			$rangeWorkHour = array();
    			$timeStart = $value['CALENDAR_BUSINESS_START'];
    			$timeEnd   = $value['CALENDAR_BUSINESS_END'];
    			$rangeWorkHour['START'] = ((strlen($timeStart) == 8) ? $timeStart : $timeStart.':00');
    			$rangeWorkHour['END']   = ((strlen($timeEnd) == 8) ? $timeEnd : $timeEnd.':00');
    
    			$workHoursDay[] = $rangeWorkHour;
    		}
    
    		if ($value['CALENDAR_BUSINESS_DAY'] == '7') {
    			$rangeWorkHour = array();
    			$timeStart = $value['CALENDAR_BUSINESS_START'];
    			$timeEnd   = $value['CALENDAR_BUSINESS_END'];
    			$rangeWorkHour['START'] = ((strlen($timeStart) == 8) ? $timeStart : $timeStart.':00');
    			$rangeWorkHour['END']   = ((strlen($timeEnd) == 8) ? $timeEnd : $timeEnd.':00');
    
    			$tempWorkHoursDay[] = $rangeWorkHour;
    		}
    	}
    
    	if ( !(count($workHoursDay)) ) {
    		$workHoursDay = $tempWorkHoursDay;
    	}
    
    	$countHours = count($workHoursDay);
    	if ($countHours) {
    		for ($i = 1; $i < $countHours; $i++) {
    			for ($j = 0; $j < $countHours-$i; $j++) {
    				$dataft = (float)str_replace(':', '', $workHoursDay[$j]['START']);
    				$datasc = (float)str_replace(':', '', $workHoursDay[$j+1]['END']);
    				if ($dataft > $datasc) {
    					$aux = $workHoursDay[$j+1];
    					$workHoursDay[$j+1] = $workHoursDay[$j];
    					$workHoursDay[$j] = $aux;
    				}
    			}
    		}
    
    		foreach ($workHoursDay as $value) {
    			$iniTime = (float)str_replace(':', '', ((strlen($value['START']) == 8) ? $value['START'] : $value['START'].':00'));
    			$finTime = (float)str_replace(':', '', ((strlen($value['END']) == 8) ? $value['END'] : $value['END'].':00'));
    
    			if ( $timeDate <= $iniTime ) {
    				$nextWorkHours['STATUS'] = true;
    				$nextWorkHours['DATE']   = $auxIniDate['0'] . ' ' . ((strlen($value['START']) == 8) ? $value['START'] : $value['START'].':00');
    				return $nextWorkHours;
    			} elseif ( ($iniTime <= $timeDate)  && ($timeDate < $finTime) ) {
    				$nextWorkHours['STATUS'] = true;
    				$nextWorkHours['DATE']   = $date;
    				return $nextWorkHours;
    			}
    		}
    	}
    
    	$nextWorkHours['STATUS'] = false;
    	return $nextWorkHours;
    }
    
    public function is_holiday ($date, $holidays = array())
    {
    	$holidays = (count($holidays)) ? $holidays : $this->pmCalendarData['HOLIDAY'];
    
    	$auxIniDate = explode(' ', $date);
    	$iniDate = $auxIniDate['0'];
    	$iniDate = strtotime($iniDate);
    
    	foreach ($holidays as $value) {
    		$holidayStartDate = strtotime(date('Y-m-d',strtotime($value['CALENDAR_HOLIDAY_START'])));
    		$holidayEndDate   = strtotime(date('Y-m-d',strtotime($value['CALENDAR_HOLIDAY_END'])));
    
    		if ( ($holidayStartDate <= $iniDate) && ($iniDate <= $holidayEndDate) ) {
    			return true;
    		}
    	}
    	return false;
    }

    /**
     * getnextValidBusinessHoursrange is used recursivily to find a valid BusinessHour
     * for the given $date and $time.
     * This function use all the exeptions defined for
     * Working days, Business Hours and Holidays.
     *
     * @author Hugo Loza <hugo@colosa.com>
     * @name getNextValidBusinessHoursRange
     * @param date $date
     * @param time $time
     *
     * @var array $businessHoursA Object with all the infromation about the valid Business Hours found
     * $return array('DATE'=>$date,'TIME'=>$time,'BUSINESS_HOURS'=>$businessHoursA)
     */
    function getNextValidBusinessHoursRange ($date, $time)
    {
        $this->addCalendarLog( "================= Start : $date,$time ================" );

        //First Validate if is a valid date
        $sw_valid_date = false;
        $sw_date_changed = false;
        while (! $sw_valid_date) {
            $dateArray = explode( "-", $date );
            $hour = 0;
            $minute = 0;
            $second = 0;
            $month = $dateArray[1];
            $day = $dateArray[2];
            $year = $dateArray[0];
            $weekDay = date( "w", mktime( $hour, $minute, $second, $month, $day, $year ) );
            $weekDayLabel = date( "l", mktime( $hour, $minute, $second, $month, $day, $year ) );
            $dateInt = mktime( $hour, $minute, $second, $month, $day, $year );

            $this->addCalendarLog( "**** $weekDayLabel ($weekDay) * $date" );
            $sw_week_day = false;
            $sw_not_holiday = true;

            if (in_array( $weekDay, $this->calendarDefinition['CALENDAR_WORK_DAYS_A'] )) {
                $sw_week_day = true;
            }
            if (! $sw_week_day) {
                $this->addCalendarLog( "Valid working Dates: " . $this->calendarDefinition['CALENDAR_WORK_DAYS_A'] );
                $this->addCalendarLog( "- Non working Day" );
            }

            foreach ($this->calendarDefinition['HOLIDAY'] as $key => $holiday) {
                //Normalize Holiday date to SAME year of date


                $holidayStartA = explode( " ", $holiday['CALENDAR_HOLIDAY_START'] );
                $holidayStartA = explode( "-", $holidayStartA[0] );

                $normalizedHolidayStart = date( "Y-m-d", mktime( $hour, $minute, $second, $holidayStartA[1], $holidayStartA[2], $year ) );
                $normalizedHolidayStartInt = mktime( $hour, $minute, $second, $holidayStartA[1], $holidayStartA[2], $year );

                $holidayEndA = explode( " ", $holiday['CALENDAR_HOLIDAY_END'] );
                $holidayEndA = explode( "-", $holidayEndA[0] );
                $normalizedHolidayEnd = date( "Y-m-d", mktime( $hour, $minute, $second, $holidayEndA[1], $holidayEndA[2], $year ) );
                $normalizedHolidayEndInt = mktime( $hour, $minute, $second, $holidayEndA[1], $holidayEndA[2], $year );
                $sw_not_holiday_aux = true;
                if ($dateInt >= $normalizedHolidayStartInt && $dateInt <= $normalizedHolidayEndInt) {
                    $sw_not_holiday = false;
                    $sw_not_holiday_aux = false;
                }
                if (! $sw_not_holiday_aux) {
                    $this->addCalendarLog( "It is a holiday -> " . $holiday['CALENDAR_HOLIDAY_NAME'] . " ($normalizedHolidayStart - $normalizedHolidayEnd)" );
                }
            }
            $sw_valid_date = $sw_week_day && $sw_not_holiday;

            if (! $sw_valid_date) { //Go to next day
                $date = date( "Y-m-d", mktime( $hour, $minute + 1, $second, $month, $day + 1, $year ) );
                $sw_date_changed = true;
            } else {
                $this->addCalendarLog( "FOUND VALID DATE -> $date" );

                //We got a valid day, now get the valid Business Hours
                //Here Need to find a rule to get the most nea
                $businessHoursA = array ();
                $prevHour = "00:00";

                if ($sw_date_changed) { // If date has changed then Use the first available period
                    $time = "00:01";
                }

                foreach ($this->calendarDefinition['BUSINESS_DAY'] as $keyBH => $businessHours) {

                    // First the period may correspond to ALL or to the current week day
                    if (($businessHours['CALENDAR_BUSINESS_DAY'] == 7) || ($businessHours['CALENDAR_BUSINESS_DAY'] == $weekDay)) {
                        $this->addCalendarLog( "Validating ($time/$prevHour) From: " . $businessHours['CALENDAR_BUSINESS_START'] . " to " . $businessHours['CALENDAR_BUSINESS_END'] );

                        //Prev Hour
                        $prevHourA = explode( ":", $prevHour );
                        $prevHourSeconds = ($prevHourA[0] * 60 * 60) + ($prevHour[1] * 60);

                        $calendarBusinessStartA = explode( ":", $businessHours['CALENDAR_BUSINESS_START'] );
                        $calendarBusinessStartSeconds = ($calendarBusinessStartA[0] * 60 * 60) + ($calendarBusinessStartA[1] * 60);

                        $calendarBusinessEndA = explode( ":", $businessHours['CALENDAR_BUSINESS_END'] );
                        $calendarBusinessEndSeconds = ($calendarBusinessEndA[0] * 60 * 60) + ($calendarBusinessEndA[1] * 60);

                        $timeAuxA = explode( ":", $time );
                        $timeAuxSeconds = ($timeAuxA[0] * 60 * 60) + ($timeAuxA[1] * 60);

                        if (($timeAuxSeconds >= $prevHourSeconds) && ($timeAuxSeconds < $calendarBusinessEndSeconds)) {
                            $this->addCalendarLog( "*** FOUND VALID BUSINESS HOUR " . $businessHours['CALENDAR_BUSINESS_START'] . " - " . $businessHours['CALENDAR_BUSINESS_END'] );

                            if ($timeAuxSeconds < $calendarBusinessStartSeconds) { //Out of range then assign first hour
                                $this->addCalendarLog( "Set to default start hour to: " . $businessHours['CALENDAR_BUSINESS_START'] );
                                $time = $businessHours['CALENDAR_BUSINESS_START'];
                            }
                            $prevHour = $businessHours['CALENDAR_BUSINESS_END'];
                            $businessHoursA = $businessHours;
                        }

                    }
                }
            }

            if (empty( $businessHoursA )) {
                $this->addCalendarLog( "> No Valid Business Hour found for current date, go to next" );
                $date = date( "Y-m-d", mktime( $hour, $minute + 1, $second, $month, $day + 1, $year ) );
                $sw_date_changed = true;
                $sw_valid_date = false;
            }

        }

        $return['DATE'] = $date;
        $return['TIME'] = $time;
        $return['BUSINESS_HOURS'] = $businessHoursA;

        return $return;
    }





    /**************SLA classes***************/
    public function dashCalculateDate ($iniDate, $duration, $formatDuration, $calendarData = array())
    {
    	if ( G::toUpper($formatDuration) == 'DAYS' ) {
    		$duration = $duration*$calendarData['HOURS_FOR_DAY'];
    	}
      if ( G::toUpper($formatDuration) == 'MINUTES' ) {
          $duration = $duration/60;
      }
    	$hoursDuration = (float)$duration;
    	$newDate = $iniDate;
    
    	while ($hoursDuration > 0) {
    		$newDate = $this->dashGetIniDate($newDate, $calendarData);
    
    		$rangeWorkHour = $this->dashGetRangeWorkHours($newDate, $calendarData['BUSINESS_DAY']);
    		$onlyDate = (date('Y-m-d',strtotime($newDate))) . ' ' . $rangeWorkHour['END'];
    
    		if ( (((float)$hoursDuration) >= ((float)$rangeWorkHour['TOTAL'])) ||
    				((strtotime($onlyDate) - strtotime($newDate)) < (((float)$hoursDuration)*3600))
    		) {
    			$secondRes = (float)(strtotime($onlyDate) - strtotime($newDate));
    			$newDate = $onlyDate;
    			$hoursDuration -= (float)($secondRes/3600);
    		} else {
    			$newDate = date('Y-m-d H:i:s', strtotime('+' . (((float)$hoursDuration)*3600) . ' seconds', strtotime($newDate)));
    			$hoursDuration = 0;
    		}
    	}
    	return $newDate;
    }
    
    //Calculate the duration betwen two dates with a calendar
    public function dashCalculateDurationWithCalendar ($iniDate, $finDate = null, $calendarData = array())
    {
    	if ((is_null($finDate)) || ($finDate == '')) {
    		$finDate = date('Y-m-d H:i:s');
    	}
    

        if ((strtotime($finDate)) <= (strtotime($iniDate))) {
            return 0.00;
        }
	
        $secondDuration = 0.00;
    
    	$finDate = $this->dashGetIniDate($finDate, $calendarData);
    	$newDate = $iniDate;

		$timeIniDate = strtotime($iniDate);
		$timeFinDate = strtotime($finDate);

    	while ($timeIniDate < $timeFinDate) {
    		$newDate = $this->dashGetIniDate($newDate, $calendarData);
    
    		$rangeWorkHour = $this->dashGetRangeWorkHours($newDate, $calendarData['BUSINESS_DAY']);
    		$onlyDate = (date('Y-m-d',strtotime($newDate))) . ' ' . $rangeWorkHour['END'];
    
    		if ( (strtotime($finDate)) < (strtotime($onlyDate)) ) {
    			$secondRes = ( ((float)strtotime($finDate)) - ((float)strtotime($newDate)) );
    			$timeIniDate = strtotime($finDate);
    			$secondDuration += (float)$secondRes;
    		} else {
    			$secondRes = ( ((float)strtotime($onlyDate)) - ((float)strtotime($newDate)) );
    			$newDate = $onlyDate;
    			$timeIniDate = strtotime($onlyDate);
    			$secondDuration += (float)$secondRes;
    		}
    	}
    	return $secondDuration;
    }
    
    public function dashGetIniDate ($iniDate, $calendarData = array())
    {
    	$flagIniDate = true;
    
    	while ($flagIniDate) {
    		// 1 if it's a work day
    		$weekDay = date('w',strtotime($iniDate));
    		if ( !(in_array($weekDay, $calendarData['CALENDAR_WORK_DAYS_A'])) ) {
    			$iniDate = date('Y-m-d'.' 00:00:00' , strtotime('+1 day', strtotime($iniDate)));
    			continue;
    		}
    
    		// 2 if it's a holiday
    		$iniDateHolidayDay = $this->dashIs_holiday($iniDate, $calendarData['HOLIDAY']);
    		if ($iniDateHolidayDay) {
    			$iniDate = date('Y-m-d'.' 00:00:00' , strtotime('+1 day', strtotime($iniDate)));
    			continue;
    		}
    
    		// 3 if it's work time
    		$workHours = $this->dashNextWorkHours($iniDate, $weekDay, $calendarData['BUSINESS_DAY']);
    		if ( !($workHours['STATUS']) ) {
    			$iniDate = date('Y-m-d'.' 00:00:00' , strtotime('+1 day', strtotime($iniDate)));
    			continue;
    		} else {
    			$iniDate = $workHours['DATE'];
    		}
    		$flagIniDate = false;
    	}
    
    	return $iniDate;
    }
    
    public function dashNextWorkHours ($date, $weekDay, $workHours = array())
    {
    	$auxIniDate = explode(' ', $date);
    
    	$timeDate = $auxIniDate['1'];
    	$timeDate = (float)str_replace(':', '', ((strlen($timeDate) == 8) ? $timeDate : $timeDate.':00') );
    	$nextWorkHours = array();
    
    	$workHoursDay = array();
    	$tempWorkHoursDay = array();
    
    	foreach ($workHours as $value) {
    		if ($value['CALENDAR_BUSINESS_DAY'] == $weekDay) {
    			$rangeWorkHour = array();
    			$timeStart = $value['CALENDAR_BUSINESS_START'];
    			$timeEnd   = $value['CALENDAR_BUSINESS_END'];
    			$rangeWorkHour['START'] = ((strlen($timeStart) == 8) ? $timeStart : $timeStart.':00');
    			$rangeWorkHour['END']   = ((strlen($timeEnd) == 8) ? $timeEnd : $timeEnd.':00');
    
    			$workHoursDay[] = $rangeWorkHour;
    		}
    
    		if ($value['CALENDAR_BUSINESS_DAY'] == '7') {
    			$rangeWorkHour = array();
    			$timeStart = $value['CALENDAR_BUSINESS_START'];
    			$timeEnd   = $value['CALENDAR_BUSINESS_END'];
    			$rangeWorkHour['START'] = ((strlen($timeStart) == 8) ? $timeStart : $timeStart.':00');
    			$rangeWorkHour['END']   = ((strlen($timeEnd) == 8) ? $timeEnd : $timeEnd.':00');
    
    			$tempWorkHoursDay[] = $rangeWorkHour;
    		}
    	}
    
    	if ( !(count($workHoursDay)) ) {
    		$workHoursDay = $tempWorkHoursDay;
    	}
    
    	$countHours = count($workHoursDay);
    	if ($countHours) {
    		for ($i = 1; $i < $countHours; $i++) {
    			for ($j = 0; $j < $countHours-$i; $j++) {
    				$dataft = (float)str_replace(':', '', $workHoursDay[$j]['START']);
    				$datasc = (float)str_replace(':', '', $workHoursDay[$j+1]['END']);
    				if ($dataft > $datasc) {
    					$aux = $workHoursDay[$j+1];
    					$workHoursDay[$j+1] = $workHoursDay[$j];
    					$workHoursDay[$j] = $aux;
    				}
    			}
    		}
    
    		foreach ($workHoursDay as $value) {
    			$iniTime = (float)str_replace(':', '', ((strlen($value['START']) == 8) ? $value['START'] : $value['START'].':00'));
    			$finTime = (float)str_replace(':', '', ((strlen($value['END']) == 8) ? $value['END'] : $value['END'].':00'));
    
    			if ( $timeDate <= $iniTime ) {
    				$nextWorkHours['STATUS'] = true;
    				$nextWorkHours['DATE']   = $auxIniDate['0'] . ' ' . ((strlen($value['START']) == 8) ? $value['START'] : $value['START'].':00');
    				return $nextWorkHours;
    			} elseif ( ($iniTime <= $timeDate)  && ($timeDate < $finTime) ) {
    				$nextWorkHours['STATUS'] = true;
    				$nextWorkHours['DATE']   = $date;
    				return $nextWorkHours;
    			}
    		}
    	}
    
    	$nextWorkHours['STATUS'] = false;
    	return $nextWorkHours;
    }
    
    public function dashIs_holiday ($date, $holidays = array())
    {
    	$auxIniDate = explode(' ', $date);
    	$iniDate = $auxIniDate['0'];
    	$iniDate = strtotime($iniDate);
    
    	foreach ($holidays as $value) {
    		$holidayStartDate = strtotime(date('Y-m-d',strtotime($value['CALENDAR_HOLIDAY_START'])));
    		$holidayEndDate   = strtotime(date('Y-m-d',strtotime($value['CALENDAR_HOLIDAY_END'])));
    
    		if ( ($holidayStartDate <= $iniDate) && ($iniDate <= $holidayEndDate) ) {
    			return true;
    		}
    	}
    	return false;
    }
    
    public function dashGetRangeWorkHours ($date, $workHours)
    {
    	$auxIniDate = explode(' ', $date);
    	$timeDate = $auxIniDate['1'];
    	$timeDate = (float)str_replace(':', '', ((strlen($timeDate) == 8) ? $timeDate : $timeDate.':00') );
    	$weekDay = date('w',strtotime($date));
    
    	$workHoursDay = array();
    	$tempWorkHoursDay = array();
    
    	foreach ($workHours as $value) {
    		if ($value['CALENDAR_BUSINESS_DAY'] == $weekDay) {
    			$rangeWorkHour = array();
    			$timeStart = $value['CALENDAR_BUSINESS_START'];
    			$timeEnd   = $value['CALENDAR_BUSINESS_END'];
    			$rangeWorkHour['START'] = ((strlen($timeStart) == 8) ? $timeStart : $timeStart.':00');
    			$rangeWorkHour['END']   = ((strlen($timeEnd) == 8) ? $timeEnd : $timeEnd.':00');
    
    			$workHoursDay[] = $rangeWorkHour;
    		}
    
    		if ($value['CALENDAR_BUSINESS_DAY'] == '7') {
    			$rangeWorkHour = array();
    			$timeStart = $value['CALENDAR_BUSINESS_START'];
    			$timeEnd   = $value['CALENDAR_BUSINESS_END'];
    			$rangeWorkHour['START'] = ((strlen($timeStart) == 8) ? $timeStart : $timeStart.':00');
    			$rangeWorkHour['END']   = ((strlen($timeEnd) == 8) ? $timeEnd : $timeEnd.':00');
    
    			$tempWorkHoursDay[] = $rangeWorkHour;
    		}
    	}
    
    	if ( !(count($workHoursDay)) ) {
    		$workHoursDay = $tempWorkHoursDay;
    	}
    
    	foreach ($workHoursDay as $value) {
    		$iniTime = (float)str_replace(':', '', $value['START']);
    		$finTime = (float)str_replace(':', '', $value['END']);
    
    		if ( ($iniTime <= $timeDate)  && ($timeDate <= $finTime) ) {
    			//pr($finTime .' menos '.$iniTime .' = '.($finTime-$iniTime));
    			$value['TOTAL'] = (($finTime-$iniTime)/10000);
    			return $value;
    		}
    	}
    	return false;
    }

}
?>
