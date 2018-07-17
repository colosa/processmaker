<?php

class Dates
{

    private $holidays = array();
    private $weekends = array();
    private $range = array();
    private $skipEveryYear = true;
    private $calendarDays = false; //by default we are using working days
    private $hoursPerDay = 8; //you should change this

    /**
     * Function that calculate a final date based on $sInitDate and $iDuration
     * This function also uses a Calendar component (class.calendar.php) where all the definition of
     * a User, task, Process or default calendar is defined.
     * base on that information is possible to setup different calendars
     * and apply them to a task, process or user. Each calendar have Working Days, Business Hours and Holidays
     *
     * @name calculateDate
     * @access public
     * @author Hugo Loza <hugo@colosa.com>
     * @param date $sInitDate
     * @param double $iDuration
     * @param string $sTimeUnit
     * @param string $iTypeDay
     * @param string $UsrUid
     * @param string $ProUid
     * @param string $TasUid
     * @return array('DUE_DATE'=>'Final calculated date formatted as Y-m-d H:i:s','DUE_DATE_SECONDS'=>'Final calculated date in seconds','OLD_DUE_DATE'=>'Using deprecate4d function','OLD_DUE_DATE_SECONDS'=>'Using deprecated function','DUE_DATE_LOG'=>'Log of all the calculations made')
     * @todo test this function with negative durations (for events)
     *
     *
     */
    public function calculateDate($sInitDate, $iDuration, $sTimeUnit, $iTypeDay, $UsrUid = null, $ProUid = null, $TasUid = null)
    {
        //$oldDate=$this->calculateDate_noCalendar( $sInitDate, $iDuration, $sTimeUnit, $iTypeDay, $UsrUid, $ProUid, $TasUid);
        //Set Calendar when the object is instanced in this order/priority (Task, User, Process, Default)
        $calendarObj = new Calendar($UsrUid, $ProUid, $TasUid);
        //Get next Business Hours/Range based on :
        switch (strtoupper($sTimeUnit)) {
            case 'DAYS':
                $hoursToProcess = $iDuration * 8;
                break; //In Hours
            default:
                $hoursToProcess = $iDuration;
                break; //In Hours
        }
        $dateArray = explode(" ", $sInitDate);
        $currentDate = $dateArray[0];
        $currentTime = isset($dateArray[1]) ? $dateArray[1] : "00:00:00";

        $startTime = (float) array_sum(explode(' ', microtime()));

        $calendarObj->addCalendarLog("* Starting at: $startTime");
        $calendarObj->addCalendarLog(">>>>> Hours to Process: $hoursToProcess");
        $calendarObj->addCalendarLog(">>>>> Current Date: $currentDate");
        $calendarObj->addCalendarLog(">>>>> Current Time: $currentTime");
        $array_hours = explode(":", $currentTime);
        $seconds2 = $array_hours[2];
        $minutes2 = 0;
        while ($hoursToProcess > 0) {
            $validBusinessHour = $calendarObj->getNextValidBusinessHoursRange($currentDate, $currentTime);
            //For Date/Time operations
            $currentDateA = explode("-", $validBusinessHour['DATE']);
            $currentTimeA = explode(":", $validBusinessHour['TIME']);
            $hour = $currentTimeA[0];
            $minute = $currentTimeA[1];
            $second = isset($currentTimeA[2]) ? $currentTimeA[2] : 0;
            $month = $currentDateA[1];
            $day = $currentDateA[2];
            $year = $currentDateA[0];
            $normalizedDate = date("Y-m-d H:i:s", mktime($hour, $minute, $second, $month, $day, $year));
            $normalizedDateInt = mktime($hour, $minute, $second, $month, $day, $year);
            $normalizedDateSeconds = ($hour * 60 * 60) + ($minute * 60);
            $arrayHour = explode(".", $hoursToProcess);
            if (isset($arrayHour[1])) {
                $minutes1 = $arrayHour[1];
                $cadm = strlen($minutes1);
                $minutes2 = (($minutes1 / pow(10, $cadm)) * 60);
            }
            $possibleTime = date("Y-m-d H:i:s", mktime($hour + $hoursToProcess, $minute + $minutes2, $second + $seconds2, $month, $day, $year));
            $possibleTimeInt = mktime($hour + $hoursToProcess, $minute + $minutes2, $second + $seconds2, $month, $day, $year);
            $offsetPermitedMinutes = "0";
            $calendarBusinessEndA = explode(":", $validBusinessHour['BUSINESS_HOURS']['CALENDAR_BUSINESS_END']);
            $calendarBusinessEndNormalized = date("Y-m-d H:i:s", mktime($calendarBusinessEndA[0], $calendarBusinessEndA[1] + $offsetPermitedMinutes, 0, $month, $day, $year));
            $calendarBusinessEndInt = mktime($calendarBusinessEndA[0], $calendarBusinessEndA[1] + $offsetPermitedMinutes, 0, $month, $day, $year);
            $calendarBusinessEndSeconds = ($calendarBusinessEndA[0] * 60 * 60) + ($calendarBusinessEndA[1] * 60);
            $calendarObj->addCalendarLog("Possible time: $possibleTime");
            $calendarObj->addCalendarLog("Current Start Date/Time: $normalizedDate");
            $calendarObj->addCalendarLog("Calendar Business End: $calendarBusinessEndNormalized");
            if ($possibleTimeInt > $calendarBusinessEndInt) {
                $currentDateTimeB = explode(" ", $calendarBusinessEndNormalized);
                $currentDate = $currentDateTimeB[0];
                $currentTime = $currentDateTimeB[1];
                $diff = abs($normalizedDateSeconds - $calendarBusinessEndSeconds);
                $diffHours = $diff / 3600;
                $hoursToProcess = $hoursToProcess - $diffHours;
            } else {
                $currentDateTimeA = explode(" ", $possibleTime);
                $currentDate = $currentDateTimeA[0];
                $currentTime = $currentDateTimeA[1];
                $hoursToProcess = 0;
            }
            $calendarObj->addCalendarLog("** Hours to Process: $hoursToProcess");
        }
        $calendarObj->addCalendarLog("+++++++++++ Calculated Due Date $currentDate $currentTime");
        $result['DUE_DATE'] = $currentDate . " " . $currentTime;
        $result['DUE_DATE_SECONDS'] = strtotime($currentDate . " " . $currentTime);
        //$result['OLD_DUE_DATE']        = date("Y-m-d H:i:s",$oldDate);
        //$result['OLD_DUE_DATE_SECONDS']= $oldDate;


        $endTime = (float) array_sum(explode(' ', microtime()));
        $calendarObj->addCalendarLog("* Ending at: $endTime");
        $calcTime = round($endTime - $startTime, 3);
        $calendarObj->addCalendarLog("** Processing time: " . sprintf("%.4f", ($endTime - $startTime)) . " seconds");
        $result['DUE_DATE_LOG'] = $calendarObj->calendarLog;
        return $result;
    }

    /**
     * Calculate $sInitDate + $iDaysCount, skipping non laborable days.
     * Input: Any valid strtotime function type input.
     * Returns: Integer timestamp of the result.
     * Warning: It will hangs if there is no possible days to count as
     * "laborable".
     *
     * @param date $sInitDate
     * @param double $iDuration
     * @param string $sTimeUnit
     * @param string $iTypeDay
     * @param string $UsrUid
     * @param string $ProUid
     * @param string $TasUid
     * @return integer timestamp of the result
     * @deprecated renamed by Hugo Loza (see calculateDate new function)
     */
    public function calculateDate_noCalendar($sInitDate, $iDuration, $sTimeUnit, $iTypeDay, $UsrUid = null, $ProUid = null, $TasUid = null)
    {
        //load in class variables the config of working days, holidays etc..
        $this->prepareInformation($UsrUid, $ProUid, $TasUid);
        $iHours = 0;
        $iDays = 0;
        //convert the $iDuration and $sTimeUnit in hours and days, take in mind 8 hours = 1 day. and then we will have similar for 5 days = 1 weekends
        if (strtolower($sTimeUnit) == 'hours') {
            $iAux = intval(abs($iDuration));
            $iHours = $iAux % $this->hoursPerDay;
            $iDays = intval($iAux / $this->hoursPerDay);
        }
        if (strtolower($sTimeUnit) == 'days') {
            $iAux = intval(abs($iDuration * $this->hoursPerDay));
            $iHours = $iAux % 8;
            $iDays = intval($iAux / 8);
        }
        $addSign = ($iDuration >= 0) ? '+' : '-';
        $iInitDate = strtotime($sInitDate);
        if ($iTypeDay == 1) {
            // working days
            // if there are days calculate the days,
            $iEndDate = $this->addDays($iInitDate, $iDays, $addSign);
            // if there are hours calculate the hours, and probably add a day if the quantity of hours for last day > 8 hours
            $iEndDate = $this->addHours($iEndDate, $iHours, $addSign);
        } else {
            // $task->getTasTypeDay() == 2 // calendar days
            $iEndDate = strtotime($addSign . $iDays . ' days ', $iInitDate);
            $iEndDate = strtotime($addSign . $iHours . ' hours ', $iEndDate);
        }
        return $iEndDate;
    }

    /**
     * Calculate duration of the $sInitDate - $sEndDate.
     *
     * @param date $sInitDate
     * @param date $sEndDate
     * @param string $UsrUid
     * @param string $ProUid
     * @param string $TasUid
     * @return int
     *
     */
    public function calculateDuration($sInitDate, $sEndDate = '', $UsrUid = null, $ProUid = null, $TasUid = null)
    {
        $this->prepareInformation($UsrUid, $ProUid, $TasUid);
        if ((string) $sEndDate == '') {
            $sEndDate = date('Y-m-d H:i:s');
        }
        if (strtotime($sInitDate) > strtotime($sEndDate)) {
            $sAux = $sInitDate;
            $sInitDate = $sEndDate;
            $sEndDate = $sAux;
        }
        $aAux1 = explode(' ', $sInitDate);
        $aAux2 = explode(' ', $sEndDate);
        $aInitDate = explode('-', $aAux1[0]);
        $aEndDate = explode('-', $aAux2[0]);
        $i = 1;
        $iWorkedDays = 0;
        $bFinished = false;
        $fHours1 = 0.0;
        $fHours2 = 0.0;
        if (count($aInitDate) != 3) {
            $aInitDate = array(0, 0, 0);
        }
        if (count($aEndDate) != 3) {
            $aEndDate = array(0, 0, 0);
        }
        if ($aInitDate !== $aEndDate) {
            while (!$bFinished && ($i < 10000)) {
                $sAux = date('Y-m-d', mktime(0, 0, 0, $aInitDate[1], $aInitDate[2] + $i, $aInitDate[0]));
                if ($sAux != implode('-', $aEndDate)) {
                    if (!in_array($sAux, $this->holidays)) {
                        if (!in_array(date('w', mktime(0, 0, 0, $aInitDate[1], $aInitDate[2] + $i, $aInitDate[0])), $this->weekends)) {
                            $iWorkedDays++;
                        }
                    }
                    $i++;
                } else {
                    $bFinished = true;
                }
            }
            if (isset($aAux1[1])) {
                $aAux1[1] = explode(':', $aAux1[1]);
                $fHours1 = 24 - ($aAux1[1][0] + ($aAux1[1][1] / 60) + ($aAux1[1][2] / 3600));
            }
            if (isset($aAux2[1])) {
                $aAux2[1] = explode(':', $aAux2[1]);
                $fHours2 = $aAux2[1][0] + ($aAux2[1][1] / 60) + ($aAux2[1][2] / 3600);
            }
            $fDuration = ($iWorkedDays * 24) + $fHours1 + $fHours2;
        } else {
            $fDuration = (strtotime($sEndDate) - strtotime($sInitDate)) / 3600;
        }
        return $fDuration;
    }

    /**
     * Configuration functions
     *
     * @param string $UsrUid
     * @param string $ProUid
     * @param string $TasUid
     * @return void
     */
    public function prepareInformation($UsrUid = null, $ProUid = null, $TasUid = null)
    {
        // setup calendarDays according the task
        if (isset($TasUid)) {
            $task = TaskPeer::retrieveByPK($TasUid);
            if (!is_null($task)) {
                $this->calendarDays = ($task->getTasTypeDay() == 2);
            }
        }

        //get an array with all holidays.
        $aoHolidays = HolidayPeer::doSelect(new Criteria());
        $holidays = array();
        foreach ($aoHolidays as $holiday) {
            $holidays[] = strtotime($holiday->getHldDate());
        }

        // by default the weekdays are from monday to friday
        $this->weekends = array(0, 6);
        $this->holidays = $holidays;
        return;
    }

    /**
     * Set to repeat for every year all dates defined in $this->holiday
     *
     * @param $bSkipEveryYear
     * @return void
     */
    public function setSkipEveryYear($bSkipEveryYear)
    {
        $this->skipEveryYear = $bSkipEveryYear === true;
    }

    /**
     * Add a single date to holidays
     *
     * @param data $sDate
     * @return void
     */
    public function addHoliday($sDate)
    {
        if ($date = strtotime($sDate)) {
            $this->holidays[] = self::truncateTime($date);
        } else {
            throw new Exception("Invalid date: $sDate.");
        }
    }

    /**
     * Set all the holidays
     *
     * @param date/array $aDate must be an array of (strtotime type) dates
     * @return void
     */
    public function setHolidays($aDates)
    {
        foreach ($aDates as $sDate) {
            $this->holidays = $aDates;
        }
    }

    /**
     * Set all the weekends
     *
     * @param array/integers $aWeekends must be an array of integers [1,7]
     * 1=Sunday
     * 7=Saturday
     * @return void
     */
    public function setWeekends($aWeekends)
    {
        $this->weekends = $aWeekends;
    }

    /**
     * Add one day of week to the weekends list
     *
     * @param $iDayNumber must be an array of integers [1,7]
     * 1=Sunday
     * 7=Saturday
     * @return void
     */
    public function skipDayOfWeek($iDayNumber)
    {
        if ($iDayNumber < 1 || $iDayNumber > 7) {
            throw new Exception("The day of week must be a number from 1 to 7.");
        }
        $this->weekends[] = $iDayNumber;
    }

    /**
     * Add a range of non working dates
     *
     * @param date $sDateA must be a (strtotime type) dates
     * @param date $sDateB must be a (strtotime type) dates
     * @return void
     */
    public function addNonWorkingRange($sDateA, $sDateB)
    {
        if ($date = strtotime($sDateA)) {
            $iDateA = self::truncateTime($date);
        } else {
            throw new Exception("Invalid date: $sDateA.");
        }
        if ($date = strtotime($sDateB)) {
            $iDateB = self::truncateTime($date);
        } else {
            throw new Exception("Invalid date: $sDateB.");
        }
        if ($iDateA > $iDateB) {
            $s = $iDateA;
            $iDateA = $iDateB;
            $iDateB = $s;
        }
        $this->range[] = array($iDateA, $iDateB);
    }

    /**
     * PRIVATE UTILITARY FUNCTIONS
     * Add days to the date
     *
     * @param date $iInitDate
     * @param int $iDaysCount
     * @param string $addSign
     * @return date $iEndDate
     */
    private function addDays($iInitDate, $iDaysCount, $addSign = '+')
    {
        $iEndDate = $iInitDate;
        $aList = $this->holidays;
        for ($r = 1; $r <= $iDaysCount; $r++) {
            $iEndDate = strtotime($addSign . "1 day", $iEndDate);
            $dayOfWeek = idate('w', $iEndDate); //now sunday=0
            if (array_search($dayOfWeek, $this->weekends) !== false) {
                $r--; //continue loop, but we are adding one more day.
            }
        }
        return $iEndDate;
    }

    /**
     * Add hours to the date
     *
     * @param date $iInitDate
     * @param int $iHoursCount
     * @param string $addSign
     * @return $iEndDate
     */
    private function addHours($sInitDate, $iHoursCount, $addSign = '+')
    {
        $iEndDate = strtotime($addSign . $iHoursCount . " hours", $sInitDate);
        return $iEndDate;
    }

    /**
     * Compare if the date is in range
     *
     * @param $iDate = valid timestamp
     * @return true if it is within any of the ranges defined.
     */
    private function inRange($iDate)
    {
        $aRange = $this->range;
        $iYear = idate('Y', $iDate);
        foreach ($aRange as $key => $rang) {
            if ($this->skipEveryYear) {
                $deltaYears = idate('Y', $rang[1]) - idate('Y', $rang[0]);
                $rang[0] = self::changeYear($rang[0], $iYear);
                $rang[1] = self::changeYear($rang[1], $iYear + $deltaYears);
            }
            if (($iDate >= $rang[0]) && ($iDate <= $rang[1])) {
                return true;
            }
        }
        return false;
    }

    /**
     * Truncate a date
     *
     * @param $iDate = valid timestamp
     * @return date
     */
    private function truncateTime($iDate)
    {
        return mktime(0, 0, 0, idate('m', $iDate), idate('d', $iDate), idate('Y', $iDate));
    }

    /**
     * Get time
     *
     * @param timestamp $iDate
     * @return date
     */
    private function getTime($iDate)
    {
        return array(idate('H', $iDate), idate('m', $iDate), idate('s', $iDate));
    }

    /**
     * Set time
     *
     * @param timestamp $iDate
     * @param timestamp $aTime
     * @return date
     */
    private function setTime($iDate, $aTime)
    {
        return mktime($aTime[0], $aTime[1], $aTime[2], idate('m', $iDate), idate('d', $iDate), idate('Y', $iDate));
    }

    /**
     * Returns an array with all the dates of $this->skip['List'] with its
     * year changed to $iYear.
     * Warning: Don't know what to do if change a 29-02-2004 to 29-02-2005
     * the last one doesn't exist.
     *
     * @param List $iYear
     * @return array
     */
    private function listForYear($iYear)
    {
        $aList = $this->holidays;
        foreach ($aList as $k => $v) {
            $aList[$k] = self::changeYear($v, $iYear);
        }
        return $aList;
    }

    /**
     * Returns an array with all the dates of $this->skip['List'] with its
     * year changed to $iYear.
     * Warning: Don't know what to do if change a 29-02-2004 to 29-02-2005
     * the last one doesn't exist.
     *
     * @param array $iYear
     * @param date $iDate
     * @return array
     */
    private function changeYear($iDate, $iYear)
    {
        if ($delta = ($iYear - idate('Y', $iDate))) {
            $iDate = strtotime("$delta year", $iDate);
        }
        return $iDate;
    }
}
