<?php
namespace ProcessMaker\BusinessModel;

class TimerEvent
{
    private $arrayFieldDefinition = array(
        "TMREVN_UID"                => array("type" => "string",   "required" => false, "empty" => false, "defaultValues" => array(), "fieldNameAux" => "timerEventUid"),

        "EVN_UID"                   => array("type" => "string",   "required" => true,  "empty" => false, "defaultValues" => array(), "fieldNameAux" => "eventUid"),
        "TMREVN_OPTION"             => array("type" => "string",   "required" => true,  "empty" => false, "defaultValues" => array("HOURLY", "DAILY", "MONTHLY", "EVERY", "ONE-DATE-TIME", "WAIT-FOR", "WAIT-UNTIL-SPECIFIED-DATE-TIME"), "fieldNameAux" => "timerEventOption"),
        "TMREVN_START_DATE"         => array("type" => "date",     "required" => false, "empty" => true,  "defaultValues" => array(), "fieldNameAux" => "timerEventStartDate"),
        "TMREVN_END_DATE"           => array("type" => "date",     "required" => false, "empty" => true,  "defaultValues" => array(), "fieldNameAux" => "timerEventEndDate"),
        "TMREVN_DAY"                => array("type" => "string",   "required" => false, "empty" => true,  "defaultValues" => array(), "fieldNameAux" => "timerEventDay"),
        "TMREVN_HOUR"               => array("type" => "string",   "required" => false, "empty" => true,  "defaultValues" => array(), "fieldNameAux" => "timerEventHour"),
        "TMREVN_MINUTE"             => array("type" => "string",   "required" => false, "empty" => true,  "defaultValues" => array(), "fieldNameAux" => "timerEventMinute"),
        "TMREVN_CONFIGURATION_DATA" => array("type" => "string",   "required" => false, "empty" => true,  "defaultValues" => array(), "fieldNameAux" => "timerEventConfigurationData"),
        "TMREVN_NEXT_RUN_DATE"      => array("type" => "datetime", "required" => false, "empty" => true,  "defaultValues" => array(), "fieldNameAux" => "timerEventNextRunDate"),
        "TMREVN_STATUS"             => array("type" => "string",   "required" => false, "empty" => false, "defaultValues" => array("ACTIVE", "INACTIVE", "PROCESSED"), "fieldNameAux" => "timerEventStatus")
    );

    private $formatFieldNameInUppercase = true;

    private $arrayFieldNameForException = array(
        "projectUid" => "PRJ_UID"
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
     * Get year, month, day, hour, minute and second by datetime
     *
     * @param string $datetime Datetime (yyyy-mm-dd hh:ii:ss)
     *
     * return array Return data
     */
    public function getYearMonthDayHourMinuteSecondByDatetime($datetime)
    {
        try {
            $arrayData = array();

            if (preg_match("/^([1-9]\d{3})\-(0[1-9]|1[0-2])\-(0[1-9]|[12][0-9]|3[01])(?:\s([0-1]\d|2[0-3])\:([0-5]\d)\:([0-5]\d))?$/", $datetime, $arrayMatch)) {
                $arrayData[] = $arrayMatch[1]; //Year
                $arrayData[] = $arrayMatch[2]; //Month
                $arrayData[] = $arrayMatch[3]; //Day
                $arrayData[] = (isset($arrayMatch[4]))? $arrayMatch[4] : "00"; //Hour
                $arrayData[] = (isset($arrayMatch[5]))? $arrayMatch[5] : "00"; //Minute
                $arrayData[] = (isset($arrayMatch[6]))? $arrayMatch[6] : "00"; //Second
            }

            //Return
            return $arrayData;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get valid Next Run Date
     *
     * @param array  $arrayTimerEventData Timer-Event data
     * @param string $datetime            Datetime
     * @param bool   $flagIncludeDatetime Flag
     *
     * return string Return the valid Next Run Date
     */
    public function getValidNextRunDateByDataAndDatetime(array $arrayTimerEventData, $datetime, $flagIncludeDatetime = true)
    {
        try {
            $nextRunDate = $datetime;

            //Get Next Run Date
            list($year, $month, $day, $hour, $minute, $second) = $this->getYearMonthDayHourMinuteSecondByDatetime($datetime);

            $arrayMonthsShort = array("Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec");
            $arrayWeekdays    = array("Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday");

            switch ($arrayTimerEventData["TMREVN_OPTION"]) {
                case "HOURLY":
                    $hhmmss = "$hour:" . $arrayTimerEventData["TMREVN_MINUTE"] . ":00";

                    $nextRunDate = "$year-$month-$day $hhmmss";

                    if (!$flagIncludeDatetime) {
                        $nextRunDate = date("Y-m-d H:i:s", strtotime("$nextRunDate +1 hour"));
                    }
                    break;
                case "DAILY":
                    $hhmmss = $arrayTimerEventData["TMREVN_HOUR"] . ":" . $arrayTimerEventData["TMREVN_MINUTE"] . ":00";

                    $arrayWeekdaysData = $arrayTimerEventData["TMREVN_CONFIGURATION_DATA"];

                    if (!empty($arrayWeekdaysData)) {
                        sort($arrayWeekdaysData);

                        $weekday = (int)(date("w", strtotime($datetime)));
                        $weekday = ($weekday == 0)? 7 : $weekday;

                        $firstWeekday = $arrayWeekdaysData[0];

                        $nextWeekday   = $firstWeekday;
                        $typeStatement = "this";
                        $flag = false;

                        foreach ($arrayWeekdaysData as $value) {
                            $d = $value;

                            if (($flagIncludeDatetime && $d >= $weekday) || (!$flagIncludeDatetime && $d > $weekday)) {
                                $nextWeekday = $d;
                                $flag = true;
                                break;
                            }
                        }

                        if (!$flag) {
                            $typeStatement = "next";
                        }

                        $nextRunDate = date("Y-m-d", strtotime("$year-$month-$day $typeStatement " . $arrayWeekdays[$nextWeekday - 1])) . " $hhmmss";
                    } else {
                        $nextRunDate = "$year-$month-$day $hhmmss";

                        if (!$flagIncludeDatetime) {
                            $nextRunDate = date("Y-m-d", strtotime("$nextRunDate +1 day")) . " $hhmmss";
                        }
                    }
                    break;
                case "MONTHLY":
                    $hhmmss = $arrayTimerEventData["TMREVN_HOUR"] . ":" . $arrayTimerEventData["TMREVN_MINUTE"] . ":00";

                    $arrayMonthsData = $arrayTimerEventData["TMREVN_CONFIGURATION_DATA"];

                    if (!empty($arrayMonthsData)) {
                        sort($arrayMonthsData);

                        $firstMonth = $arrayMonthsData[0];

                        $nextMonth = $firstMonth;
                        $flag = false;

                        foreach ($arrayMonthsData as $value) {
                            $m = $value;

                            if (($flagIncludeDatetime && $m >= $month) || (!$flagIncludeDatetime && $m > $month)) {
                                $nextMonth = $m;
                                $flag = true;
                                break;
                            }
                        }

                        if (!$flag) {
                            $year++;
                        }

                        if (checkdate((int)($nextMonth), (int)($arrayTimerEventData["TMREVN_DAY"]), (int)($year))) {
                            $nextRunDate = "$year-$nextMonth-" . $arrayTimerEventData["TMREVN_DAY"] . " $hhmmss";
                        } else {
                            $nextRunDate = date("Y-m-d", strtotime("last day of " . $arrayMonthsShort[((int)($nextMonth)) - 1] . " $year")) . " $hhmmss";
                        }
                    } else {
                        if (checkdate((int)($month), (int)($arrayTimerEventData["TMREVN_DAY"]), (int)($year))) {
                            $nextRunDate = "$year-$month-" . $arrayTimerEventData["TMREVN_DAY"] . " $hhmmss";
                        } else {
                            $nextRunDate = date("Y-m-d", strtotime("last day of " . $arrayMonthsShort[((int)($month)) - 1] . " $year")) . " $hhmmss";
                        }

                        if (!$flagIncludeDatetime) {
                            list($yearAux, $monthAux) = $this->getYearMonthDayHourMinuteSecondByDatetime(date("Y-m-d", strtotime("$year-$month-01 next month")));

                            if (checkdate((int)($monthAux), (int)($arrayTimerEventData["TMREVN_DAY"]), (int)($yearAux))) {
                                $nextRunDate = "$yearAux-$monthAux-" . $arrayTimerEventData["TMREVN_DAY"] . " $hhmmss";
                            } else {
                                $nextRunDate = date("Y-m-d", strtotime("last day of " . $arrayMonthsShort[((int)($monthAux)) - 1] . " $yearAux")) . " $hhmmss";
                            }
                        }
                    }
                    break;
                case "EVERY":
                    if ($arrayTimerEventData["TMREVN_HOUR"] . "" != "") {
                        $nextRunDate = date("Y-m-d H:i:s", strtotime("$nextRunDate +" . ((int)($arrayTimerEventData["TMREVN_HOUR"])) . " hours"));
                    }

                    if ($arrayTimerEventData["TMREVN_MINUTE"] . "" != "") {
                        $nextRunDate = date("Y-m-d H:i:s", strtotime("$nextRunDate +" . ((int)($arrayTimerEventData["TMREVN_MINUTE"])) . " minutes"));
                    }
                    break;
            }

            //Return
            return date("Y-m-d H:i:s", strtotime($nextRunDate));
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get Next Run Date
     *
     * @param array  $arrayTimerEventData Timer-Event data
     * @param string $datetime            Datetime
     * @param bool   $flagIncludeDatetime Flag
     *
     * return string Return the Next Run Date
     */
    public function getNextRunDateByDataAndDatetime(array $arrayTimerEventData, $datetime, $flagIncludeDatetime = true)
    {
        try {
            $nextRunDate = $datetime;

            //Get Next Run Date
            if (!is_array($arrayTimerEventData["TMREVN_CONFIGURATION_DATA"])) {
                $arrayTimerEventData["TMREVN_CONFIGURATION_DATA"] = unserialize($arrayTimerEventData["TMREVN_CONFIGURATION_DATA"]);
            }

            $timeDatetime = strtotime($datetime);

            $flagNextRunDate = true;

            switch ($arrayTimerEventData["TMREVN_OPTION"]) {
                case "HOURLY":
                case "DAILY":
                case "MONTHLY":
                //case "EVERY":
                    $nextRunDate = $this->getValidNextRunDateByDataAndDatetime($arrayTimerEventData, $arrayTimerEventData["TMREVN_START_DATE"] . " 00:00:00", $flagIncludeDatetime);
                    $timeNextRunDate = strtotime($nextRunDate);

                    if ($timeNextRunDate > $timeDatetime) {
                        $flagNextRunDate = false;
                    }
                    break;
            }

            if ($flagNextRunDate) {
                switch ($arrayTimerEventData["TMREVN_OPTION"]) {
                    case "HOURLY":
                    case "DAILY":
                    case "MONTHLY":
                    case "EVERY":
                        $nextRunDate = $this->getValidNextRunDateByDataAndDatetime($arrayTimerEventData, $datetime, $flagIncludeDatetime);
                        $timeNextRunDate = strtotime($nextRunDate);

                        if ($timeNextRunDate < $timeDatetime) {
                            $nextRunDate = $this->getValidNextRunDateByDataAndDatetime($arrayTimerEventData, $datetime, false);
                        }
                        break;
                }
            }

            //Return
            return $nextRunDate;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Unset fields
     *
     * @param array $arrayData Data with the fields
     *
     * return array Return data with the fields
     */
    public function unsetFields(array $arrayData)
    {
        try {
            unset($arrayData["TMREVN_UID"]);
            unset($arrayData["PRJ_UID"]);
            unset($arrayData["TMREVN_LAST_RUN_DATE"]);
            unset($arrayData["TMREVN_LAST_EXECUTION_DATE"]);

            //Return
            return $arrayData;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Verify if exists the Timer-Event
     *
     * @param string $timerEventUid Unique id of Timer-Event
     *
     * return bool Return true if exists the Timer-Event, false otherwise
     */
    public function exists($timerEventUid)
    {
        try {
            $obj = \TimerEventPeer::retrieveByPK($timerEventUid);

            return (!is_null($obj))? true : false;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Verify if exists the Event of a Timer-Event
     *
     * @param string $projectUid             Unique id of Project
     * @param string $eventUid               Unique id of Event
     * @param string $timerEventUidToExclude Unique id of Timer-Event to exclude
     *
     * return bool Return true if exists the Event of a Timer-Event, false otherwise
     */
    public function existsEvent($projectUid, $eventUid, $timerEventUidToExclude = "")
    {
        try {
            $criteria = new \Criteria("workflow");

            $criteria->addSelectColumn(\TimerEventPeer::TMREVN_UID);
            $criteria->add(\TimerEventPeer::PRJ_UID, $projectUid, \Criteria::EQUAL);

            if ($timerEventUidToExclude != "") {
                $criteria->add(\TimerEventPeer::TMREVN_UID, $timerEventUidToExclude, \Criteria::NOT_EQUAL);
            }

            $criteria->add(\TimerEventPeer::EVN_UID, $eventUid, \Criteria::EQUAL);

            $rsCriteria = \TimerEventPeer::doSelectRS($criteria);

            return ($rsCriteria->next())? true : false;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Verify if does not exists the Timer-Event
     *
     * @param string $timerEventUid         Unique id of Timer-Event
     * @param string $fieldNameForException Field name for the exception
     *
     * return void Throw exception if does not exists the Timer-Event
     */
    public function throwExceptionIfNotExistsTimerEvent($timerEventUid, $fieldNameForException)
    {
        try {
            if (!$this->exists($timerEventUid)) {
                throw new \Exception(\G::LoadTranslation("ID_TIMER_EVENT_DOES_NOT_EXIST", array($fieldNameForException, $timerEventUid)));
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Verify if is registered the Event
     *
     * @param string $projectUid             Unique id of Project
     * @param string $eventUid               Unique id of Event
     * @param string $fieldNameForException  Field name for the exception
     * @param string $timerEventUidToExclude Unique id of Timer-Event to exclude
     *
     * return void Throw exception if is registered the Event
     */
    public function throwExceptionIfEventIsRegistered($projectUid, $eventUid, $fieldNameForException, $timerEventUidToExclude = "")
    {
        try {
            if ($this->existsEvent($projectUid, $eventUid, $timerEventUidToExclude)) {
                throw new \Exception(\G::LoadTranslation("ID_TIMER_EVENT_ALREADY_REGISTERED", array($fieldNameForException, $eventUid)));
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Validate the data if they are invalid (INSERT and UPDATE)
     *
     * @param string $timerEventUid Unique id of Timer-Event
     * @param string $projectUid    Unique id of Project
     * @param array  $arrayData     Data
     *
     * return void Throw exception if data has an invalid value
     */
    public function throwExceptionIfDataIsInvalid($timerEventUid, $projectUid, array $arrayData)
    {
        try {
            //Set variables
            $arrayTimerEventData = ($timerEventUid == "")? array() : $this->getTimerEvent($timerEventUid, true);
            $flagInsert = ($timerEventUid == "")? true : false;

            $arrayFinalData = array_merge($arrayTimerEventData, $arrayData);

            //Verify data - Field definition
            $process = new \ProcessMaker\BusinessModel\Process();

            $process->throwExceptionIfDataNotMetFieldDefinition($arrayData, $this->arrayFieldDefinition, $this->arrayFieldNameForException, $flagInsert);

            //Verify data
            if (isset($arrayData["EVN_UID"])) {
                $arrayEventType   = array("START", "INTERMEDIATE");
                $arrayEventMarker = array("TIMER");

                $bpmnEvent = \BpmnEventPeer::retrieveByPK($arrayData["EVN_UID"]);

                if (is_null($bpmnEvent)) {
                    throw new \Exception(\G::LoadTranslation("ID_EVENT_NOT_EXIST", array($this->arrayFieldNameForException["eventUid"], $arrayData["EVN_UID"])));
                }

                if (!in_array($bpmnEvent->getEvnType(), $arrayEventType) || !in_array($bpmnEvent->getEvnMarker(), $arrayEventMarker)) {
                    throw new \Exception(\G::LoadTranslation("ID_EVENT_NOT_IS_TIMER_EVENT", array($this->arrayFieldNameForException["eventUid"], $arrayData["EVN_UID"])));
                }

                if ($bpmnEvent->getPrjUid() != $projectUid) {
                    throw new \Exception(\G::LoadTranslation("ID_EVENT_EVENT_NOT_BELONG_TO_PROJECT", array($this->arrayFieldNameForException["eventUid"], $arrayData["EVN_UID"], $this->arrayFieldNameForException["projectUid"], $projectUid)));
                }

                $this->throwExceptionIfEventIsRegistered($projectUid, $arrayData["EVN_UID"], $this->arrayFieldNameForException["eventUid"], $timerEventUid);
            }

            //Verify data - Field definition
            $arrayFieldDefinition = array();

            $bpmnEvent = \BpmnEventPeer::retrieveByPK($arrayFinalData["EVN_UID"]);

            switch ($bpmnEvent->getEvnType()) {
                case "START":
                    $arrayFieldDefinition = array(
                        "TMREVN_OPTION" => array("type" => "string", "required" => true, "empty" => false, "defaultValues" => array("HOURLY", "DAILY", "MONTHLY", "EVERY", "ONE-DATE-TIME"), "fieldNameAux" => "timerEventOption")
                    );
                    break;
                case "INTERMEDIATE":
                    $arrayFieldDefinition = array(
                        "TMREVN_OPTION" => array("type" => "string", "required" => true, "empty" => false, "defaultValues" => array("WAIT-FOR", "WAIT-UNTIL-SPECIFIED-DATE-TIME"), "fieldNameAux" => "timerEventOption")
                    );
                    break;
            }

            if (!empty($arrayFieldDefinition)) {
                $process->throwExceptionIfDataNotMetFieldDefinition($arrayFinalData, $arrayFieldDefinition, $this->arrayFieldNameForException, $flagInsert);
            }

            $arrayFieldDefinition = array();
            $arrayValidateData = array(
                "TMREVN_DAY"    => array("/^(?:0[1-9]|[12][0-9]|3[01])$/", $this->arrayFieldNameForException["timerEventDay"]),
                "TMREVN_HOUR"   => array("/^(?:[0-1]\d|2[0-3])$/",         $this->arrayFieldNameForException["timerEventHour"]),
                "TMREVN_MINUTE" => array("/^(?:[0-5]\d)$/",                $this->arrayFieldNameForException["timerEventMinute"])
            );

            switch ($arrayFinalData["TMREVN_OPTION"]) {
                case "HOURLY":
                    $arrayFieldDefinition = array(
                        "TMREVN_START_DATE" => array("type" => "date",   "required" => true,  "empty" => false, "defaultValues" => array(), "fieldNameAux" => "timerEventStartDate"),
                        "TMREVN_END_DATE"   => array("type" => "date",   "required" => false, "empty" => true,  "defaultValues" => array(), "fieldNameAux" => "timerEventEndDate"),
                        "TMREVN_MINUTE"     => array("type" => "string", "required" => true,  "empty" => false, "defaultValues" => array(), "fieldNameAux" => "timerEventMinute")
                    );
                    break;
                case "DAILY":
                    $arrayFieldDefinition = array(
                        "TMREVN_START_DATE"         => array("type" => "date",   "required" => true,  "empty" => false, "defaultValues" => array(), "fieldNameAux" => "timerEventStartDate"),
                        "TMREVN_END_DATE"           => array("type" => "date",   "required" => false, "empty" => true,  "defaultValues" => array(), "fieldNameAux" => "timerEventEndDate"),
                        "TMREVN_HOUR"               => array("type" => "string", "required" => true,  "empty" => false, "defaultValues" => array(), "fieldNameAux" => "timerEventHour"),
                        "TMREVN_MINUTE"             => array("type" => "string", "required" => true,  "empty" => false, "defaultValues" => array(), "fieldNameAux" => "timerEventMinute"),
                        "TMREVN_CONFIGURATION_DATA" => array("type" => "array",  "required" => false, "empty" => true,  "defaultValues" => array(1, 2, 3, 4, 5, 6, 7), "fieldNameAux" => "timerEventConfigurationData")
                    );
                    break;
                case "MONTHLY":
                    $arrayFieldDefinition = array(
                        "TMREVN_START_DATE"         => array("type" => "date",   "required" => true,  "empty" => false, "defaultValues" => array(), "fieldNameAux" => "timerEventStartDate"),
                        "TMREVN_END_DATE"           => array("type" => "date",   "required" => false, "empty" => true,  "defaultValues" => array(), "fieldNameAux" => "timerEventEndDate"),
                        "TMREVN_DAY"                => array("type" => "string", "required" => true,  "empty" => false, "defaultValues" => array(), "fieldNameAux" => "timerEventDay"),
                        "TMREVN_HOUR"               => array("type" => "string", "required" => true,  "empty" => false, "defaultValues" => array(), "fieldNameAux" => "timerEventHour"),
                        "TMREVN_MINUTE"             => array("type" => "string", "required" => true,  "empty" => false, "defaultValues" => array(), "fieldNameAux" => "timerEventMinute"),
                        "TMREVN_CONFIGURATION_DATA" => array("type" => "array",  "required" => false, "empty" => true,  "defaultValues" => array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12), "fieldNameAux" => "timerEventConfigurationData")
                    );
                    break;
                case "EVERY":
                    $arrayFieldDefinition = array(
                        "TMREVN_HOUR"   => array("type" => "string", "required" => true, "empty" => false, "defaultValues" => array(), "fieldNameAux" => "timerEventHour"),
                        "TMREVN_MINUTE" => array("type" => "string", "required" => true, "empty" => false, "defaultValues" => array(), "fieldNameAux" => "timerEventMinute")
                    );

                    $arrayValidateData["TMREVN_HOUR"][0]   = "/^(?:0?\d|[1-9]\d*)$/";
                    $arrayValidateData["TMREVN_MINUTE"][0] = "/^(?:0?\d|[1-9]\d*)$/";
                    break;
                case "ONE-DATE-TIME":
                    $arrayFieldDefinition = array(
                        "TMREVN_NEXT_RUN_DATE" => array("type" => "datetime", "required" => true, "empty" => false, "defaultValues" => array(), "fieldNameAux" => "timerEventNextRunDate")
                    );
                    break;
                case "WAIT-FOR":
                    //TMREVN_DAY
                    //TMREVN_HOUR
                    //TMREVN_MINUTE
                    $arrayValidateData["TMREVN_DAY"][0]    = "/^(?:0?\d|[1-9]\d*)$/";
                    $arrayValidateData["TMREVN_HOUR"][0]   = "/^(?:0?\d|[1-9]\d*)$/";
                    $arrayValidateData["TMREVN_MINUTE"][0] = "/^(?:0?\d|[1-9]\d*)$/";
                    break;
                case "WAIT-UNTIL-SPECIFIED-DATE-TIME":
                    $arrayFieldDefinition = array(
                        "TMREVN_CONFIGURATION_DATA" => array("type" => "string", "required" => true, "empty" => false, "defaultValues" => array(), "fieldNameAux" => "timerEventConfigurationData")
                    );
                    break;
            }

            if (!empty($arrayFieldDefinition)) {
                $process->throwExceptionIfDataNotMetFieldDefinition($arrayFinalData, $arrayFieldDefinition, $this->arrayFieldNameForException, $flagInsert);
            }

            foreach ($arrayValidateData as $key => $value) {
                if (isset($arrayData[$key]) && !preg_match($value[0], $arrayData[$key])) {
                    throw new \Exception(\G::LoadTranslation("ID_INVALID_VALUE", array($value[1])));
                }
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Create Timer-Event for a Project
     *
     * @param string $projectUid Unique id of Project
     * @param array  $arrayData  Data
     *
     * return array Return data of the new Timer-Event created
     */
    public function create($projectUid, array $arrayData)
    {
        try {
            //Verify data
            $process = new \ProcessMaker\BusinessModel\Process();
            $validator = new \ProcessMaker\BusinessModel\Validator();

            $validator->throwExceptionIfDataIsNotArray($arrayData, "\$arrayData");
            $validator->throwExceptionIfDataIsEmpty($arrayData, "\$arrayData");

            //Set data
            $arrayData = array_change_key_case($arrayData, CASE_UPPER);

            //Verify data
            $process->throwExceptionIfNotExistsProcess($projectUid, $this->arrayFieldNameForException["projectUid"]);

            $this->throwExceptionIfDataIsInvalid("", $projectUid, $arrayData);

            //Create
            $cnn = \Propel::getConnection("workflow");

            $arrayData = $this->unsetFields($arrayData);

            try {
                $timerEvent = new \TimerEvent();
                $bpmnEvent = \BpmnEventPeer::retrieveByPK($arrayData["EVN_UID"]);

                $timerEventUid = \ProcessMaker\Util\Common::generateUID();

                $arrayData["TMREVN_START_DATE"]    = (isset($arrayData["TMREVN_START_DATE"]) && $arrayData["TMREVN_START_DATE"] . "" != "")?       $arrayData["TMREVN_START_DATE"] : null;
                $arrayData["TMREVN_END_DATE"]      = (isset($arrayData["TMREVN_END_DATE"]) && $arrayData["TMREVN_END_DATE"] . "" != "")?           $arrayData["TMREVN_END_DATE"] : null;
                $arrayData["TMREVN_NEXT_RUN_DATE"] = (isset($arrayData["TMREVN_NEXT_RUN_DATE"]) && $arrayData["TMREVN_NEXT_RUN_DATE"] . "" != "")? $arrayData["TMREVN_NEXT_RUN_DATE"] : null;
                $arrayData["TMREVN_CONFIGURATION_DATA"] = serialize((isset($arrayData["TMREVN_CONFIGURATION_DATA"]))? $arrayData["TMREVN_CONFIGURATION_DATA"] : "");

                $timerEvent->fromArray($arrayData, \BasePeer::TYPE_FIELDNAME);

                $timerEvent->setTmrevnUid($timerEventUid);
                $timerEvent->setPrjUid($projectUid);

                if ($bpmnEvent->getEvnType() == "START") {
                    switch ($arrayData["TMREVN_OPTION"]) {
                        case "HOURLY":
                        case "DAILY":
                        case "MONTHLY":
                        case "EVERY":
                            $timerEvent->setTmrevnNextRunDate($this->getNextRunDateByDataAndDatetime($arrayData, date("Y-m-d H:i:s")));
                            break;
                    }
                }

                if ($timerEvent->validate()) {
                    $cnn->begin();

                    $result = $timerEvent->save();

                    $cnn->commit();

                    //Return
                    return $this->getTimerEvent($timerEventUid);
                } else {
                    $msg = "";

                    foreach ($timerEvent->getValidationFailures() as $validationFailure) {
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
     * Single create Timer-Event
     *
     * @param string $projectUid Unique id of Project
     * @param array  $arrayData  Data
     *
     * return string Return unique id of Timer-Event
     */
    public function singleCreate($projectUid, array $arrayData)
    {
        try {
            $cnn = \Propel::getConnection("workflow");

            try {
                $timerEvent = new \TimerEvent();

                $timerEventUid = \ProcessMaker\Util\Common::generateUID();

                $timerEvent->fromArray($arrayData, \BasePeer::TYPE_FIELDNAME);

                $timerEvent->setTmrevnUid($timerEventUid);
                $timerEvent->setPrjUid($projectUid);

                if ($timerEvent->validate()) {
                    $cnn->begin();

                    $result = $timerEvent->save();

                    $cnn->commit();

                    //Return
                    return $timerEventUid;
                } else {
                    $msg = "";

                    foreach ($timerEvent->getValidationFailures() as $validationFailure) {
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
     * Update Timer-Event
     *
     * @param string $timerEventUid Unique id of Timer-Event
     * @param array  $arrayData     Data
     *
     * return int Return the number of rows affected by this update
     */
    public function update($timerEventUid, array $arrayData)
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
            $arrayTimerEventData = $this->getTimerEvent($timerEventUid, true);

            $arrayFinalData = array_merge($arrayTimerEventData, $arrayData);

            //Verify data
            $this->throwExceptionIfNotExistsTimerEvent($timerEventUid, $this->arrayFieldNameForException["timerEventUid"]);

            $this->throwExceptionIfDataIsInvalid($timerEventUid, $arrayTimerEventData["PRJ_UID"], $arrayData);

            //Update
            $cnn = \Propel::getConnection("workflow");

            $arrayData = $this->unsetFields($arrayData);

            try {
                $timerEvent = \TimerEventPeer::retrieveByPK($timerEventUid);
                $bpmnEvent = \BpmnEventPeer::retrieveByPK($arrayFinalData["EVN_UID"]);

                if (isset($arrayData["TMREVN_START_DATE"])) {
                    $arrayData["TMREVN_START_DATE"] = ($arrayData["TMREVN_START_DATE"] . "" != "")? $arrayData["TMREVN_START_DATE"] : null;
                }

                if (isset($arrayData["TMREVN_END_DATE"])) {
                    $arrayData["TMREVN_END_DATE"] = ($arrayData["TMREVN_END_DATE"] . "" != "")? $arrayData["TMREVN_END_DATE"] : null;
                }

                if (isset($arrayData["TMREVN_NEXT_RUN_DATE"])) {
                    $arrayData["TMREVN_NEXT_RUN_DATE"] = ($arrayData["TMREVN_NEXT_RUN_DATE"] . "" != "")? $arrayData["TMREVN_NEXT_RUN_DATE"] : null;
                }

                if (isset($arrayData["TMREVN_CONFIGURATION_DATA"])) {
                    $arrayData["TMREVN_CONFIGURATION_DATA"] = serialize($arrayData["TMREVN_CONFIGURATION_DATA"]);
                }
                
                $oldValues = $timerEvent->toArray();

                $timerEvent->fromArray($arrayData, \BasePeer::TYPE_FIELDNAME);

                $nowValues = $timerEvent->toArray();
                //If the timer event undergoes any editing, the value of the 'TMREVN_STATUS' 
                //field must be changed to 'ACTIVE', otherwise the 'ONE-DATE-TIME' 
                //option will prevent execution.
                if (!($oldValues == $nowValues)) {
                    $timerEvent->setTmrevnStatus("ACTIVE");
                }

                if ($bpmnEvent->getEvnType() == "START") {
                    switch ($arrayFinalData["TMREVN_OPTION"]) {
                        case "HOURLY":
                        case "DAILY":
                        case "MONTHLY":
                        case "EVERY":
                            $flagUpdateNextRunDate = false;

                            $arrayFieldsToCheck = array();

                            switch ($arrayFinalData["TMREVN_OPTION"]) {
                                case "HOURLY":
                                    $arrayFieldsToCheck = array("TMREVN_START_DATE", "TMREVN_END_DATE", "TMREVN_MINUTE");
                                    break;
                                case "DAILY":
                                    $arrayFieldsToCheck = array("TMREVN_START_DATE", "TMREVN_END_DATE", "TMREVN_HOUR", "TMREVN_MINUTE", "TMREVN_CONFIGURATION_DATA");
                                    break;
                                case "MONTHLY":
                                    $arrayFieldsToCheck = array("TMREVN_START_DATE", "TMREVN_END_DATE", "TMREVN_DAY", "TMREVN_HOUR", "TMREVN_MINUTE", "TMREVN_CONFIGURATION_DATA");
                                    break;
                                case "EVERY":
                                    $arrayFieldsToCheck = array("TMREVN_HOUR", "TMREVN_MINUTE");
                                    break;
                            }

                            foreach ($arrayFieldsToCheck as $value) {
                                if (isset($arrayData[$value])) {
                                    if ($value == "TMREVN_CONFIGURATION_DATA") {
                                        $arrayAux = unserialize($arrayData[$value]);

                                        $array1 = array_diff($arrayAux, $arrayTimerEventData[$value]);
                                        $array2 = array_diff($arrayTimerEventData[$value], $arrayAux);

                                        $flagUpdateNextRunDate = !empty($array1) || !empty($array2);
                                    } else {
                                        $flagUpdateNextRunDate = $arrayData[$value] != $arrayTimerEventData[$value];
                                    }

                                    if ($flagUpdateNextRunDate) {
                                        break;
                                    }
                                }
                            }

                            if ($flagUpdateNextRunDate) {
                                $timerEvent->setTmrevnNextRunDate($this->getNextRunDateByDataAndDatetime($arrayFinalData, date("Y-m-d H:i:s")));
                            }
                            break;
                    }
                }

                if ($timerEvent->validate()) {
                    $cnn->begin();

                    $result = $timerEvent->save();

                    $cnn->commit();

                    //Return
                    return $result;
                } else {
                    $msg = "";

                    foreach ($timerEvent->getValidationFailures() as $validationFailure) {
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
     * Single update Timer-Event
     *
     * @param string $timerEventUid Unique id of Timer-Event
     * @param array  $arrayData     Data
     *
     * return int Return integer
     */
    public function singleUpdate($timerEventUid, array $arrayData)
    {
        try {
            $cnn = \Propel::getConnection("workflow");

            try {
                $timerEvent = \TimerEventPeer::retrieveByPK($timerEventUid);

                $timerEvent->fromArray($arrayData, \BasePeer::TYPE_FIELDNAME);

                if ($timerEvent->validate()) {
                    $cnn->begin();

                    $result = $timerEvent->save();

                    $cnn->commit();

                    //Return
                    return $result;
                } else {
                    $msg = "";

                    foreach ($timerEvent->getValidationFailures() as $validationFailure) {
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
     * Delete Timer-Event
     *
     * @param string $timerEventUid Unique id of Timer-Event
     *
     * return void
     */
    public function delete($timerEventUid)
    {
        try {
            //Verify data
            $this->throwExceptionIfNotExistsTimerEvent($timerEventUid, $this->arrayFieldNameForException["timerEventUid"]);

            //Delete
            $criteria = new \Criteria("workflow");

            $criteria->add(\TimerEventPeer::TMREVN_UID, $timerEventUid, \Criteria::EQUAL);

            $result = \TimerEventPeer::doDelete($criteria);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Delete Timer-Event
     *
     * @param array $arrayCondition Conditions
     *
     * return void
     */
    public function deleteWhere(array $arrayCondition)
    {
        try {
            //Delete
            $criteria = new \Criteria("workflow");

            foreach ($arrayCondition as $key => $value) {
                if (is_array($value)) {
                    $criteria->add($key, $value[0], $value[1]);
                } else {
                    $criteria->add($key, $value, \Criteria::EQUAL);
                }
            }

            $result = \TimerEventPeer::doDelete($criteria);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get criteria for Timer-Event
     *
     * return object
     */
    public function getTimerEventCriteria()
    {
        try {
            $criteria = new \Criteria("workflow");

            $criteria->addSelectColumn(\TimerEventPeer::TMREVN_UID);
            $criteria->addSelectColumn(\TimerEventPeer::PRJ_UID);
            $criteria->addSelectColumn(\TimerEventPeer::EVN_UID);
            $criteria->addSelectColumn(\TimerEventPeer::TMREVN_OPTION);
            $criteria->addSelectColumn(\TimerEventPeer::TMREVN_START_DATE);
            $criteria->addSelectColumn(\TimerEventPeer::TMREVN_END_DATE);
            $criteria->addSelectColumn(\TimerEventPeer::TMREVN_DAY);
            $criteria->addSelectColumn(\TimerEventPeer::TMREVN_HOUR);
            $criteria->addSelectColumn(\TimerEventPeer::TMREVN_MINUTE);
            $criteria->addSelectColumn(\TimerEventPeer::TMREVN_CONFIGURATION_DATA);
            $criteria->addSelectColumn(\TimerEventPeer::TMREVN_NEXT_RUN_DATE);
            $criteria->addSelectColumn(\TimerEventPeer::TMREVN_LAST_RUN_DATE);
            $criteria->addSelectColumn(\TimerEventPeer::TMREVN_LAST_EXECUTION_DATE);
            $criteria->addSelectColumn(\TimerEventPeer::TMREVN_STATUS);

            return $criteria;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get data of a Timer-Event from a record
     *
     * @param array $record Record
     *
     * return array Return an array with data Timer-Event
     */
    public function getTimerEventDataFromRecord(array $record)
    {
        try {
            return array(
                $this->getFieldNameByFormatFieldName("TMREVN_UID")                 => $record["TMREVN_UID"],
                $this->getFieldNameByFormatFieldName("EVN_UID")                    => $record["EVN_UID"],
                $this->getFieldNameByFormatFieldName("TMREVN_OPTION")              => $record["TMREVN_OPTION"],
                $this->getFieldNameByFormatFieldName("TMREVN_START_DATE")          => $record["TMREVN_START_DATE"] . "",
                $this->getFieldNameByFormatFieldName("TMREVN_END_DATE")            => $record["TMREVN_END_DATE"] . "",
                $this->getFieldNameByFormatFieldName("TMREVN_DAY")                 => $record["TMREVN_DAY"],
                $this->getFieldNameByFormatFieldName("TMREVN_HOUR")                => $record["TMREVN_HOUR"],
                $this->getFieldNameByFormatFieldName("TMREVN_MINUTE")              => $record["TMREVN_MINUTE"],
                $this->getFieldNameByFormatFieldName("TMREVN_CONFIGURATION_DATA")  => $record["TMREVN_CONFIGURATION_DATA"],
                $this->getFieldNameByFormatFieldName("TMREVN_NEXT_RUN_DATE")       => $record["TMREVN_NEXT_RUN_DATE"] . "",
                $this->getFieldNameByFormatFieldName("TMREVN_LAST_RUN_DATE")       => $record["TMREVN_LAST_RUN_DATE"] . "",
                $this->getFieldNameByFormatFieldName("TMREVN_LAST_EXECUTION_DATE") => $record["TMREVN_LAST_EXECUTION_DATE"] . "",
                $this->getFieldNameByFormatFieldName("TMREVN_STATUS")              => $record["TMREVN_STATUS"]
            );
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get all Timer-Events
     *
     * @param string $projectUid Unique id of Project
     *
     * return array Return an array with all Timer-Events
     */
    public function getTimerEvents($projectUid)
    {
        try {
            $arrayTimerEvent = array();

            //Verify data
            $process = new \ProcessMaker\BusinessModel\Process();

            $process->throwExceptionIfNotExistsProcess($projectUid, $this->arrayFieldNameForException["projectUid"]);

            //Get data
            $criteria = $this->getTimerEventCriteria();

            $criteria->add(\TimerEventPeer::PRJ_UID, $projectUid, \Criteria::EQUAL);

            $rsCriteria = \TimerEventPeer::doSelectRS($criteria);
            $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

            while ($rsCriteria->next()) {
                $row = $rsCriteria->getRow();
                $row["TMREVN_CONFIGURATION_DATA"] = unserialize($row["TMREVN_CONFIGURATION_DATA"]);

                $arrayTimerEvent[] = $this->getTimerEventDataFromRecord($row);
            }

            //Return
            return $arrayTimerEvent;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get data of a Timer-Event
     *
     * @param string $timerEventUid Unique id of Timer-Event
     * @param bool   $flagGetRecord Value that set the getting
     *
     * return array Return an array with data of a Timer-Event
     */
    public function getTimerEvent($timerEventUid, $flagGetRecord = false)
    {
        try {
            //Verify data
            $this->throwExceptionIfNotExistsTimerEvent($timerEventUid, $this->arrayFieldNameForException["timerEventUid"]);

            //Get data
            $criteria = $this->getTimerEventCriteria();

            $criteria->add(\TimerEventPeer::TMREVN_UID, $timerEventUid, \Criteria::EQUAL);

            $rsCriteria = \TimerEventPeer::doSelectRS($criteria);
            $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

            $result = $rsCriteria->next();

            $row = $rsCriteria->getRow();
            $row["TMREVN_CONFIGURATION_DATA"] = unserialize($row["TMREVN_CONFIGURATION_DATA"]);

            //Return
            return (!$flagGetRecord)? $this->getTimerEventDataFromRecord($row) : $row;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get data of a Timer-Event by unique id of Event
     *
     * @param string $projectUid    Unique id of Project
     * @param string $eventUid      Unique id of Event
     * @param bool   $flagGetRecord Value that set the getting
     *
     * return array Return an array with data of a Timer-Event by unique id of Event
     */
    public function getTimerEventByEvent($projectUid, $eventUid, $flagGetRecord = false)
    {
        try {
            //Verify data
            $process = new \ProcessMaker\BusinessModel\Process();
            $bpmnEvent = \BpmnEventPeer::retrieveByPK($eventUid);

            $process->throwExceptionIfNotExistsProcess($projectUid, $this->arrayFieldNameForException["projectUid"]);

            if (is_null($bpmnEvent)) {
                throw new \Exception(\G::LoadTranslation("ID_EVENT_NOT_EXIST", array($this->arrayFieldNameForException["eventUid"], $eventUid)));
            }

            if ($bpmnEvent->getPrjUid() != $projectUid) {
                throw new \Exception(\G::LoadTranslation("ID_EVENT_EVENT_NOT_BELONG_TO_PROJECT", array($this->arrayFieldNameForException["eventUid"], $eventUid, $this->arrayFieldNameForException["projectUid"], $projectUid)));
            }

            //Get data
            if (!$this->existsEvent($projectUid, $eventUid)) {
                //Return
                return array();
            }

            $criteria = $this->getTimerEventCriteria();

            $criteria->add(\TimerEventPeer::PRJ_UID, $projectUid, \Criteria::EQUAL);
            $criteria->add(\TimerEventPeer::EVN_UID, $eventUid, \Criteria::EQUAL);

            $rsCriteria = \TimerEventPeer::doSelectRS($criteria);
            $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

            $result = $rsCriteria->next();

            $row = $rsCriteria->getRow();
            $row["TMREVN_CONFIGURATION_DATA"] = unserialize($row["TMREVN_CONFIGURATION_DATA"]);

            //Return
            return (!$flagGetRecord)? $this->getTimerEventDataFromRecord($row) : $row;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Log
     *
     * @param string $action Action
     * @param string $value  Value
     *
     * return void
     */
    private function log($action, $value = "", $status = "action")
    {
        try {
            $workspace = (defined("SYS_SYS"))? SYS_SYS : "Wokspace Undefined";
            $ipClient = \G::getIpAddress();

            $actionTimer = "timereventcron: ";

            \G::log("|". $workspace ."|". $actionTimer . $action ."|". $status . "|" . $value , PATH_DATA, "timerevent.log");
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * The Syslog register the information in Monolog Class
     *
     * @param int $level DEBUG=100 INFO=200 NOTICE=250 WARNING=300 ERROR=400 CRITICAL=500
     * @param string $message
     * @param string $ipClient for Context information
     * @param string $action for Context information
     * @param string $timeZone for Context information
     * @param string $workspace for Context information
     * @param string $usrUid for Context information
     * @param string $proUid for Context information
     * @param string $tasUid for Context information
     * @param string $appUid for Context information
     * @param string $delIndex for Context information
     * @param string $stepUid for Context information
     * @param string $triUid for Context information
     * @param string $outDocUid for Context information
     * @param string $inpDocUid for Context information
     * @param string $url for Context information
     *
     * return void
     */
    private function syslog(
        $level,
        $message,
        $action='',
        $aContext = array()
    )
    {
        try {
            \Bootstrap::registerMonolog('TimerEventCron', $level, $message, $aContext, SYS_SYS, 'processmaker.log');
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Start/Continue case by Timer-Event
     *
     * @param string $datetime Datetime (yyyy-mm-dd hh:ii:ss)
     * @param bool   $frontEnd Flag to represent the terminal front-end
     *
     * return void
     */
    public function startContinueCaseByTimerEvent($datetime, $frontEnd = false)
    {
        try {

            //Set variables
            $ws = new \WsBase();
            $case = new \Cases();
            $common = new \ProcessMaker\Util\Common();
            $sysSys = (defined("SYS_SYS"))? SYS_SYS : "Undefined";

            $common->setFrontEnd($frontEnd);

            list($year, $month, $day, $hour, $minute) = $this->getYearMonthDayHourMinuteSecondByDatetime($datetime);

            $date    = "$year-$month-$day";
            $dateIni = "$year-$month-$day 00:00:00";
            $dateEnd = "$year-$month-$day 23:59:59";

            //Start Timer-Event (start new case) ///////////////////////////////////////////////////////////////////////
            $common->frontEndShow("START");

            $this->log("START-NEW-CASES", "Date \"$datetime (UTC +00:00)\": Start new cases");
            $aInfo = array(
                'ip'       => \G::getIpAddress()
                ,'action'   => 'START-NEW-CASES'
                ,'timeZone' => $datetime
                ,'workspace'=> $sysSys
            );
            $this->syslog(
                200
                ,'Start new cases'
                ,'START-NEW-CASES'
                ,$aInfo
            );

            //Query
            $criteria = $this->getTimerEventCriteria();

            $criteria->addSelectColumn(\BpmnEventPeer::EVN_NAME);
            $criteria->addSelectColumn(\ElementTaskRelationPeer::TAS_UID);

            $arrayCondition = array();
            $arrayCondition[] = array(\TimerEventPeer::PRJ_UID, \ProcessPeer::PRO_UID, \Criteria::EQUAL);
            $criteria->addJoinMC($arrayCondition, \Criteria::INNER_JOIN);

            $criteria->add(\ProcessPeer::PRO_STATUS, "ACTIVE", \Criteria::EQUAL);

            $arrayCondition = array();
            $arrayCondition[] = array(\TimerEventPeer::PRJ_UID, \ElementTaskRelationPeer::PRJ_UID, \Criteria::EQUAL);
            $arrayCondition[] = array(\TimerEventPeer::EVN_UID, \ElementTaskRelationPeer::ELEMENT_UID, \Criteria::EQUAL);
            $criteria->addJoinMC($arrayCondition, \Criteria::INNER_JOIN);

            $criteria->add(\ElementTaskRelationPeer::ELEMENT_TYPE, "bpmnEvent", \Criteria::EQUAL);

            $arrayCondition = array();
            $arrayCondition[] = array(\ElementTaskRelationPeer::PRJ_UID, \TaskPeer::PRO_UID, \Criteria::EQUAL);
            $arrayCondition[] = array(\ElementTaskRelationPeer::TAS_UID, \TaskPeer::TAS_UID, \Criteria::EQUAL);
            $criteria->addJoinMC($arrayCondition, \Criteria::INNER_JOIN);

            $criteria->add(\TaskPeer::TAS_TYPE, "START-TIMER-EVENT", \Criteria::EQUAL);

            $arrayCondition = array();
            $arrayCondition[] = array(\TimerEventPeer::PRJ_UID, \BpmnEventPeer::PRJ_UID, \Criteria::EQUAL);
            $arrayCondition[] = array(\TimerEventPeer::EVN_UID, \BpmnEventPeer::EVN_UID, \Criteria::EQUAL);
            $criteria->addJoinMC($arrayCondition, \Criteria::INNER_JOIN);

            $criteria->add(\BpmnEventPeer::EVN_TYPE, "START", \Criteria::EQUAL);
            $criteria->add(\BpmnEventPeer::EVN_MARKER, "TIMER", \Criteria::EQUAL);

            $criteria->add(\TimerEventPeer::TMREVN_OPTION, array("HOURLY", "DAILY", "MONTHLY", "EVERY", "ONE-DATE-TIME"), \Criteria::IN);
            $criteria->add(\TimerEventPeer::TMREVN_STATUS, "ACTIVE", \Criteria::EQUAL);

            $criteria->add(
                $criteria->getNewCriterion(\TimerEventPeer::TMREVN_NEXT_RUN_DATE, $dateIni, \Criteria::GREATER_EQUAL)->addAnd(
                $criteria->getNewCriterion(\TimerEventPeer::TMREVN_NEXT_RUN_DATE, $dateEnd, \Criteria::LESS_EQUAL))->addOr(
                $criteria->getNewCriterion(\TimerEventPeer::TMREVN_NEXT_RUN_DATE, $dateIni, \Criteria::LESS_THAN))
            );

            $criteria->add(
                $criteria->getNewCriterion(\TimerEventPeer::TMREVN_END_DATE, $date, \Criteria::GREATER_EQUAL)->addOr(
                $criteria->getNewCriterion(\TimerEventPeer::TMREVN_END_DATE, null, \Criteria::EQUAL))
            );

            $rsCriteria = \TimerEventPeer::doSelectRS($criteria);
            $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

            $flagRecord = false;

            while ($rsCriteria->next()) {
                $row = $rsCriteria->getRow();
                $row["TMREVN_CONFIGURATION_DATA"] = unserialize($row["TMREVN_CONFIGURATION_DATA"]);

                //Set variables
                $arrayTimerEventData = $row;

                $bpmnEventName = $row["EVN_NAME"];
                $taskUid       = $row["TAS_UID"];

                //Create the new case
                $timerEventNextRunDate = $arrayTimerEventData["TMREVN_NEXT_RUN_DATE"];
                $timerEventNextRunDateNew = "";

                $flagCase = false;

                if (strtotime($timerEventNextRunDate) < strtotime($dateIni)) {
                    $timerEventNextRunDateNew = $this->getNextRunDateByDataAndDatetime($arrayTimerEventData, $datetime); //Generate new date for old TMREVN_NEXT_RUN_DATE

                    $flagCase = true; //Create the old case
                } else {
                    list( , , , $hourCase, $minuteCase) = $this->getYearMonthDayHourMinuteSecondByDatetime($timerEventNextRunDate);

                    if ((int)($hour . $minute) <= (int)($hourCase . $minuteCase)) {
                        $flagCase = $hourCase == $hour && $minuteCase == $minute;
                    } else {
                        $timerEventNextRunDateNew = $this->getNextRunDateByDataAndDatetime($arrayTimerEventData, $datetime); //Generate new date for old TMREVN_NEXT_RUN_DATE

                        $flagCase = true; //Create the old case
                    }
                }

                if ($flagCase) {
                    //Update Timer-Event
                    $arrayData = [];

                    switch ($arrayTimerEventData['TMREVN_OPTION']) {
                        case 'HOURLY':
                        case 'DAILY':
                        case 'MONTHLY':
                        case 'EVERY':
                            if ($timerEventNextRunDateNew == '') {
                                $timerEventNextRunDateNew = $this->getNextRunDateByDataAndDatetime(
                                    $arrayTimerEventData, $timerEventNextRunDate, false
                                );
                            }

                            if ($arrayTimerEventData['TMREVN_OPTION'] != 'EVERY' &&
                                $arrayTimerEventData['TMREVN_END_DATE'] . '' != '' &&
                                strtotime($timerEventNextRunDateNew) > strtotime($arrayTimerEventData['TMREVN_END_DATE'] . ' 23:59:59')
                            ) {
                                $arrayData['TMREVN_STATUS'] = 'PROCESSED';
                            } else {
                                $arrayData['TMREVN_NEXT_RUN_DATE'] = $timerEventNextRunDateNew;
                            }
                            break;
                        case 'ONE-DATE-TIME':
                            $arrayData['TMREVN_STATUS'] = 'PROCESSED';
                            break;
                    }

                    $arrayData['TMREVN_LAST_RUN_DATE'] = $timerEventNextRunDate;
                    $arrayData['TMREVN_LAST_EXECUTION_DATE'] = date('Y-m-d H:i:s');

                    $result = $this->singleUpdate($arrayTimerEventData['TMREVN_UID'], $arrayData);

                    //Show info in terminal
                    if ($flagRecord) {
                        $common->frontEndShow("TEXT", "");
                    }

                    if ($bpmnEventName != "") {
                        $common->frontEndShow("TEXT", "> Name Timer-Event: $bpmnEventName");
                    }

                    $common->frontEndShow("TEXT", "> Creating the new case...");

                    //Start new case
                    $result = $ws->newCase($arrayTimerEventData["PRJ_UID"], "", $taskUid, array());

                    $arrayResult = \G::json_decode(\G::json_encode($result), true);

                    if ($arrayResult["status_code"] == 0) {
                        $applicationUid    = $arrayResult["caseId"];
                        $applicationNumber = $arrayResult["caseNumber"];

                        $common->frontEndShow("TEXT", "    - OK case #$applicationNumber was created");
                        $common->frontEndShow("TEXT", "> Routing the case #$applicationNumber...");

                        $this->log("CREATED-NEW-CASE", "Case #$applicationNumber created, APP_UID: $applicationUid");
                        $aInfo = array(
                             'ip'        => \G::getIpAddress()
                            ,'action'   => 'CREATED-NEW-CASE'
                            ,'timeZone' => $datetime
                            ,'workspace'=> $sysSys
                            ,'proUid'   => $arrayTimerEventData["PRJ_UID"]
                            ,'tasUid'   => $taskUid
                            ,'appUid'   => $applicationUid
                            ,'appNumber'=> $applicationNumber
                            ,'evnUid'   => $row['EVN_UID']
                            ,'evnName'  => $row['EVN_NAME']
                        );
                        $this->syslog(
                            200
                            ,"Case #$applicationNumber created"
                            ,'CREATED-NEW-CASE'
                            ,$aInfo
                        );

                        //Derivate new case
                        $result = $ws->derivateCase("", $applicationUid, 1);

                        $arrayResult = \G::json_decode(\G::json_encode($result), true);

                        if ($arrayResult["status_code"] == 0) {
                            $common->frontEndShow("TEXT", "    - OK");

                            $this->log("ROUTED-NEW-CASE", "Case #$applicationNumber routed, APP_UID: $applicationUid");
                            $aInfo = array(
                                 'ip'       => \G::getIpAddress()
                                ,'action'   => 'ROUTED-NEW-CASE'
                                ,'timeZone' => $datetime
                                ,'workspace'=> $sysSys
                                ,'proUid'   => $arrayTimerEventData["PRJ_UID"]
                                ,'tasUid'   => $taskUid
                                ,'appUid'   => $applicationUid
                                ,'appNumber'=> $applicationNumber
                                ,'delIndex' => '1'
                                ,'evnUid'   => $row['EVN_UID']
                                ,'evnName'  => $row['EVN_NAME']
                            );
                            $this->syslog(
                                200
                                ,"Case #$applicationNumber routed"
                                ,'ROUTED-NEW-CASE'
                                ,$aInfo
                            );
                        } else {
                            $common->frontEndShow("TEXT", "    - Failed: " . $arrayResult["message"]);

                            $this->log("ROUTED-NEW-CASE", $arrayResult["message"] . ", Case: #$applicationNumber, APP_UID: $applicationUid, PRO_UID: " . $arrayTimerEventData["PRJ_UID"], "Failed");
                            $aInfo = array(
                                 'ip'        => \G::getIpAddress()
                                ,'action'   => 'ROUTED-NEW-CASE'
                                ,'timeZone' => $datetime
                                ,'workspace'=> $sysSys
                                ,'proUid'   => $arrayTimerEventData["PRJ_UID"]
                                ,'tasUid'   => $taskUid
                                ,'appUid'   => $applicationUid
                                ,'appNumber'=> $applicationNumber
                                ,'delIndex' => '1'
                                ,'evnUid'   => $row['EVN_UID']
                                ,'evnName'  => $row['EVN_NAME']
                            );
                            $this->syslog(
                                500
                                ,"Failed case #$applicationNumber. " . $arrayResult["message"]
                                ,'ROUTED-NEW-CASE'
                                ,$aInfo
                            );
                        }
                    } else {
                        $common->frontEndShow("TEXT", "    - Failed: " . $arrayResult["message"]);

                        $this->log("CREATED-NEW-CASE", $arrayResult["message"] . ", PRO_UID: " . $arrayTimerEventData["PRJ_UID"], "Failed");
                        $aInfo = array(
                            'ip'        => \G::getIpAddress()
                            ,'action'   => 'ROUTED-NEW-CASE'
                            ,'timeZone' => $datetime
                            ,'workspace'=> $sysSys
                            ,'proUid'   => $arrayTimerEventData["PRJ_UID"]
                            ,'tasUid'   => $taskUid
                            ,'evnUid'   => $row['EVN_UID']
                            ,'evnName'  => $row['EVN_NAME']
                        );
                        $this->syslog(
                            500
                            ,"Failed case #$applicationNumber. " . $arrayResult["message"]
                            ,'CREATED-NEW-CASE'
                            ,$aInfo
                        );
                    }

                    $flagRecord = true;
                }
            }
            
            if (!$flagRecord) {
                $common->frontEndShow("TEXT", "Not exists any record to start a new case, on date \"$datetime (UTC +00:00)\"");
                $action = "NO-RECORDS";
                $this->log($action, "Not exists any record to start a new case");
                $aInfo = array(
                    'ip'        => \G::getIpAddress()
                    ,'action'   => $action
                    ,'TimeZone' => $datetime
                    ,'workspace'=> $sysSys
                );
                $this->syslog(
                    200
                    ,'Not exists any record to start a new case'
                    ,'NO-RECORDS'
                    ,$aInfo
                );
            }

            $common->frontEndShow("END");

            //Intermediate Catch Timer-Event (continue the case) ///////////////////////////////////////////////////////
            $action = "START-CONTINUE-CASES";
            $this->log($action, "Date \"$datetime (UTC +00:00)\": Start continue the cases");
            $aInfo = array(
                'ip'        => \G::getIpAddress()
                ,'action'   => $action
                ,'TimeZone' => $datetime
                ,'workspace'=> $sysSys
            );
            $this->syslog(
                200
                ,'Start continue the cases'
                ,'START-CONTINUE-CASES'
                ,$aInfo
            );

            //Query
            $criteriaMain = $this->getTimerEventCriteria();

            $criteriaMain->addSelectColumn(\AppDelegationPeer::APP_UID);
            $criteriaMain->addSelectColumn(\AppDelegationPeer::DEL_INDEX);
            $criteriaMain->addSelectColumn(\AppDelegationPeer::DEL_DELEGATE_DATE);
            $criteriaMain->addSelectColumn(\BpmnEventPeer::EVN_NAME);

            $arrayCondition = array();
            $arrayCondition[] = array(\AppDelegationPeer::PRO_UID, \ProcessPeer::PRO_UID, \Criteria::EQUAL);
            $criteriaMain->addJoinMC($arrayCondition, \Criteria::INNER_JOIN);

            $criteriaMain->add(\ProcessPeer::PRO_STATUS, "ACTIVE", \Criteria::EQUAL);

            $arrayCondition = array();
            $arrayCondition[] = array(\AppDelegationPeer::APP_UID, \ApplicationPeer::APP_UID, \Criteria::EQUAL);
            $criteriaMain->addJoinMC($arrayCondition, \Criteria::INNER_JOIN);

            $criteriaMain->add(\ApplicationPeer::APP_STATUS, "DRAFT", \Criteria::NOT_EQUAL);

            $arrayCondition = array();
            $arrayCondition[] = array(\AppDelegationPeer::PRO_UID, \TaskPeer::PRO_UID, \Criteria::EQUAL);
            $arrayCondition[] = array(\AppDelegationPeer::TAS_UID, \TaskPeer::TAS_UID, \Criteria::EQUAL);
            $criteriaMain->addJoinMC($arrayCondition, \Criteria::INNER_JOIN);

            $criteriaMain->add(\TaskPeer::TAS_TYPE, "INTERMEDIATE-CATCH-TIMER-EVENT", \Criteria::EQUAL);

            $arrayCondition = array();
            $arrayCondition[] = array(\TaskPeer::PRO_UID, \ElementTaskRelationPeer::PRJ_UID, \Criteria::EQUAL);
            $arrayCondition[] = array(\TaskPeer::TAS_UID, \ElementTaskRelationPeer::TAS_UID, \Criteria::EQUAL);
            $criteriaMain->addJoinMC($arrayCondition, \Criteria::INNER_JOIN);

            $criteriaMain->add(\ElementTaskRelationPeer::ELEMENT_TYPE, "bpmnEvent", \Criteria::EQUAL);

            $arrayCondition = array();
            $arrayCondition[] = array(\ElementTaskRelationPeer::PRJ_UID, \TimerEventPeer::PRJ_UID, \Criteria::EQUAL);
            $arrayCondition[] = array(\ElementTaskRelationPeer::ELEMENT_UID, \TimerEventPeer::EVN_UID, \Criteria::EQUAL);
            $criteriaMain->addJoinMC($arrayCondition, \Criteria::INNER_JOIN);

            $arrayCondition = array();
            $arrayCondition[] = array(\TimerEventPeer::PRJ_UID, \BpmnEventPeer::PRJ_UID, \Criteria::EQUAL);
            $arrayCondition[] = array(\TimerEventPeer::EVN_UID, \BpmnEventPeer::EVN_UID, \Criteria::EQUAL);
            $criteriaMain->addJoinMC($arrayCondition, \Criteria::INNER_JOIN);

            $criteriaMain->add(\BpmnEventPeer::EVN_TYPE, "INTERMEDIATE", \Criteria::EQUAL);
            $criteriaMain->add(\BpmnEventPeer::EVN_MARKER, "TIMER", \Criteria::EQUAL);

            $criteriaMain->add(\TimerEventPeer::TMREVN_OPTION, array("WAIT-FOR", "WAIT-UNTIL-SPECIFIED-DATE-TIME"), \Criteria::IN);
            $criteriaMain->add(\TimerEventPeer::TMREVN_STATUS, "ACTIVE", \Criteria::EQUAL);

            $criteriaMain->add(\AppDelegationPeer::DEL_THREAD_STATUS, "OPEN", \Criteria::EQUAL);
            $criteriaMain->add(\AppDelegationPeer::DEL_FINISH_DATE, null, \Criteria::ISNULL);

            //Number records total
            $criteriaCount = clone $criteriaMain;

            $criteriaCount->clearSelectColumns();
            $criteriaCount->addSelectColumn("COUNT(" . \AppDelegationPeer::APP_UID . ") AS NUM_REC");

            $rsCriteriaCount = \AppDelegationPeer::doSelectRS($criteriaCount);
            $rsCriteriaCount->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

            $result = $rsCriteriaCount->next();
            $row = $rsCriteriaCount->getRow();

            $numRecTotal = $row["NUM_REC"];

            //Query
            $total = $numRecTotal;
            $counter = 0;

            $start = 0;
            $limit = 1000;

            $flagRecord = false;

            do {
                $flagNextRecord = false;

                $criteria = clone $criteriaMain;

                $criteria->setOffset($start);
                $criteria->setLimit($limit);

                $rsCriteria = \AppDelegationPeer::doSelectRS($criteria);
                $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

                while ($rsCriteria->next()) {
                    if ($counter + 1 > $total) {
                        $flagNextRecord = false;
                        break;
                    }

                    $row = $rsCriteria->getRow();
                    $row["TMREVN_CONFIGURATION_DATA"] = unserialize($row["TMREVN_CONFIGURATION_DATA"]);

                    //Set variables
                    $arrayTimerEventData  = $row;
                    $arrayApplicationData = $case->loadCase($row["APP_UID"]);

                    $applicationUid    = $row["APP_UID"];
                    $applicationNumber = $arrayApplicationData["APP_NUMBER"];
                    $delIndex          = $row["DEL_INDEX"];
                    $delDelegateDate   = $row["DEL_DELEGATE_DATE"];
                    $bpmnEventName     = $row["EVN_NAME"];

                    //Continue the case
                    $continueCaseDate = $delDelegateDate;

                    switch ($arrayTimerEventData["TMREVN_OPTION"]) {
                        case "WAIT-FOR":
                            if ($arrayTimerEventData["TMREVN_DAY"] . "" != "") {
                                $continueCaseDate = date("Y-m-d H:i:s", strtotime("$continueCaseDate +" . ((int)($arrayTimerEventData["TMREVN_DAY"])) . " days"));
                            }

                            if ($arrayTimerEventData["TMREVN_HOUR"] . "" != "") {
                                $continueCaseDate = date("Y-m-d H:i:s", strtotime("$continueCaseDate +" . ((int)($arrayTimerEventData["TMREVN_HOUR"])) . " hours"));
                            }

                            if ($arrayTimerEventData["TMREVN_MINUTE"] . "" != "") {
                                $continueCaseDate = date("Y-m-d H:i:s", strtotime("$continueCaseDate +" . ((int)($arrayTimerEventData["TMREVN_MINUTE"])) . " minutes"));
                            }
                            break;
                        case "WAIT-UNTIL-SPECIFIED-DATE-TIME":
                            $continueCaseDate = \G::replaceDataField($arrayTimerEventData["TMREVN_CONFIGURATION_DATA"], $arrayApplicationData["APP_DATA"]);
                            break;
                    }

                    $arrayContinueCaseDateData = $this->getYearMonthDayHourMinuteSecondByDatetime($continueCaseDate);

                    if (!empty($arrayContinueCaseDateData)) {
                        $flagCase = false;

                        if (strtotime($continueCaseDate) < strtotime($dateIni)) {
                            $flagCase = true; //Continue the old case
                        } else {
                            $yearCase   = $arrayContinueCaseDateData[0];
                            $monthCase  = $arrayContinueCaseDateData[1];
                            $dayCase    = $arrayContinueCaseDateData[2];
                            $hourCase   = $arrayContinueCaseDateData[3];
                            $minuteCase = $arrayContinueCaseDateData[4];

                            if ("$yearCase-$monthCase-$dayCase" == "$year-$month-$day") {
                                if ((int)($hour . $minute) <= (int)($hourCase . $minuteCase)) {
                                    $flagCase = $hourCase == $hour && $minuteCase == $minute;
                                } else {
                                    $flagCase = true; //Continue the old case
                                }
                            }
                        }

                        if ($flagCase) {
                            //Show info in terminal
                            if ($flagRecord) {
                                $common->frontEndShow("TEXT", "");
                            }

                            if ($bpmnEventName != "") {
                                $common->frontEndShow("TEXT", "> Name Timer-Event: $bpmnEventName");
                            }

                            $common->frontEndShow("TEXT", "> Continue the case #$applicationNumber");
                            $common->frontEndShow("TEXT", "> Routing the case #$applicationNumber...");

                            //Continue the case
                            //Derivate case
                            $result = $ws->derivateCase("", $applicationUid, $delIndex);

                            $arrayResult = \G::json_decode(\G::json_encode($result), true);

                            if ($arrayResult["status_code"] == 0) {
                                $common->frontEndShow("TEXT", "    - OK");

                                $this->log("CONTINUED-CASE", "Case #$applicationNumber continued, APP_UID: $applicationUid");
                                $aInfo = array(
                                     'ip'        => \G::getIpAddress()
                                    ,'action'   => 'CONTINUED-CASE'
                                    ,'timeZone' => $datetime
                                    ,'workspace'=> $sysSys
                                    ,'proUid'   => $arrayTimerEventData["PRJ_UID"]
                                    ,'tasUid'   => $taskUid
                                    ,'appUid'   => $applicationUid
                                    ,'appNumber'=> $applicationNumber
                                    ,'evnUid'   => $row['EVN_UID']
                                    ,'evnName'  => $row['EVN_NAME']
                                );
                                $this->syslog(
                                    200
                                    ,"Case #$applicationNumber continued"
                                    ,'CONTINUED-CASE'
                                    ,$aInfo
                                );
                            } else {
                                $common->frontEndShow("TEXT", "    - Failed: " . $arrayResult["message"]);

                                $this->log("CONTINUED-CASE", $arrayResult["message"] . ", Case: #$applicationNumber, APP_UID: $applicationUid, PRO_UID: " . $arrayTimerEventData["PRJ_UID"], "Failed");
                                $aInfo = array(
                                     'ip'        => \G::getIpAddress()
                                    ,'action'   => 'CONTINUED-CASE'
                                    ,'timeZone' => $datetime
                                    ,'workspace'=> $sysSys
                                    ,'proUid'   => $arrayTimerEventData["PRJ_UID"]
                                    ,'tasUid'   => $taskUid
                                    ,'appUid'   => $applicationUid
                                    ,'appNumber'=> $applicationNumber
                                    ,'evnUid'   => $row['EVN_UID']
                                    ,'evnName'  => $row['EVN_NAME']
                                );
                                $this->syslog(
                                    500
                                    ,"Failed case #$applicationUid. " . $arrayResult["message"]
                                    ,'CONTINUED-CASE'
                                    ,$aInfo
                                );
                            }

                            $flagRecord = true;
                        }
                    } else {
                        $this->log("INVALID-CONTINUE-DATE", "Continue date: $continueCaseDate, Case: #$applicationNumber, APP_UID: $applicationUid, PRO_UID: " . $arrayTimerEventData["PRJ_UID"]);
                        $aInfo = array(
                                     'ip'        => \G::getIpAddress()
                                    ,'action'   => 'INVALID-CONTINUE-DATE'
                                    ,'timeZone' => $datetime
                                    ,'workspace'=> $sysSys
                                    ,'proUid'   => $arrayTimerEventData["PRJ_UID"]
                                    ,'tasUid'   => $taskUid
                                    ,'appUid'   => $applicationUid
                                    ,'appNumber'=> $applicationNumber
                                    ,'evnUid'   => $row['EVN_UID']
                                    ,'evnName'  => $row['EVN_NAME']
                                );
                        $this->syslog(
                            200
                            ,'Continue date '. $continueCaseDate
                            ,'INVALID-CONTINUE-DATE'
                            ,$aInfo
                        );
                    }

                    $counter++;

                    $flagNextRecord = true;
                }

                $start = $start + $limit;
            } while ($flagNextRecord);

            if (!$flagRecord) {
                $common->frontEndShow("TEXT", "No existing records to continue a case, on date \"$datetime (UTC +00:00)\"");

                $this->log("NO-RECORDS", "No existing records to continue a case");
                $aInfo = array(
                    'ip'        => \G::getIpAddress()
                    ,'action'   => $action
                    ,'TimeZone' => $datetime
                    ,'workspace'=> $sysSys
                );
                $this->syslog(
                    200
                    ,'No existing records to continue a case'
                    ,'NO-RECORDS'
                    ,$aInfo
                );
            }

            $common->frontEndShow("END");
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
