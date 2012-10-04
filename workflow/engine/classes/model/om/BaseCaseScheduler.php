<?php

require_once 'propel/om/BaseObject.php';

require_once 'propel/om/Persistent.php';


include_once 'propel/util/Criteria.php';

include_once 'classes/model/CaseSchedulerPeer.php';

/**
 * Base class that represents a row from the 'CASE_SCHEDULER' table.
 *
 * 
 *
 * @package    workflow.classes.model.om
 */
abstract class BaseCaseScheduler extends BaseObject implements Persistent
{

    /**
     * The Peer class.
     * Instance provides a convenient way of calling static methods on a class
     * that calling code may not be able to identify.
     * @var        CaseSchedulerPeer
    */
    protected static $peer;

    /**
     * The value for the sch_uid field.
     * @var        string
     */
    protected $sch_uid;

    /**
     * The value for the sch_del_user_name field.
     * @var        string
     */
    protected $sch_del_user_name;

    /**
     * The value for the sch_del_user_pass field.
     * @var        string
     */
    protected $sch_del_user_pass;

    /**
     * The value for the sch_del_user_uid field.
     * @var        string
     */
    protected $sch_del_user_uid;

    /**
     * The value for the sch_name field.
     * @var        string
     */
    protected $sch_name;

    /**
     * The value for the pro_uid field.
     * @var        string
     */
    protected $pro_uid = '';

    /**
     * The value for the tas_uid field.
     * @var        string
     */
    protected $tas_uid = '';

    /**
     * The value for the sch_time_next_run field.
     * @var        int
     */
    protected $sch_time_next_run;

    /**
     * The value for the sch_last_run_time field.
     * @var        int
     */
    protected $sch_last_run_time;

    /**
     * The value for the sch_state field.
     * @var        string
     */
    protected $sch_state = 'ACTIVE';

    /**
     * The value for the sch_last_state field.
     * @var        string
     */
    protected $sch_last_state = '';

    /**
     * The value for the usr_uid field.
     * @var        string
     */
    protected $usr_uid = '';

    /**
     * The value for the sch_option field.
     * @var        int
     */
    protected $sch_option = 0;

    /**
     * The value for the sch_start_time field.
     * @var        int
     */
    protected $sch_start_time;

    /**
     * The value for the sch_start_date field.
     * @var        int
     */
    protected $sch_start_date;

    /**
     * The value for the sch_days_perform_task field.
     * @var        string
     */
    protected $sch_days_perform_task = '';

    /**
     * The value for the sch_every_days field.
     * @var        int
     */
    protected $sch_every_days = 0;

    /**
     * The value for the sch_week_days field.
     * @var        string
     */
    protected $sch_week_days = '0|0|0|0|0|0|0';

    /**
     * The value for the sch_start_day field.
     * @var        string
     */
    protected $sch_start_day = '';

    /**
     * The value for the sch_months field.
     * @var        string
     */
    protected $sch_months = '0|0|0|0|0|0|0|0|0|0|0|0';

    /**
     * The value for the sch_end_date field.
     * @var        int
     */
    protected $sch_end_date;

    /**
     * The value for the sch_repeat_every field.
     * @var        string
     */
    protected $sch_repeat_every = '';

    /**
     * The value for the sch_repeat_until field.
     * @var        string
     */
    protected $sch_repeat_until = '';

    /**
     * The value for the sch_repeat_stop_if_running field.
     * @var        int
     */
    protected $sch_repeat_stop_if_running = 0;

    /**
     * The value for the case_sh_plugin_uid field.
     * @var        string
     */
    protected $case_sh_plugin_uid;

    /**
     * Flag to prevent endless save loop, if this object is referenced
     * by another object which falls in this transaction.
     * @var        boolean
     */
    protected $alreadyInSave = false;

    /**
     * Flag to prevent endless validation loop, if this object is referenced
     * by another object which falls in this transaction.
     * @var        boolean
     */
    protected $alreadyInValidation = false;

    /**
     * Get the [sch_uid] column value.
     * 
     * @return     string
     */
    public function getSchUid()
    {

        return $this->sch_uid;
    }

    /**
     * Get the [sch_del_user_name] column value.
     * 
     * @return     string
     */
    public function getSchDelUserName()
    {

        return $this->sch_del_user_name;
    }

    /**
     * Get the [sch_del_user_pass] column value.
     * 
     * @return     string
     */
    public function getSchDelUserPass()
    {

        return $this->sch_del_user_pass;
    }

    /**
     * Get the [sch_del_user_uid] column value.
     * 
     * @return     string
     */
    public function getSchDelUserUid()
    {

        return $this->sch_del_user_uid;
    }

    /**
     * Get the [sch_name] column value.
     * 
     * @return     string
     */
    public function getSchName()
    {

        return $this->sch_name;
    }

    /**
     * Get the [pro_uid] column value.
     * 
     * @return     string
     */
    public function getProUid()
    {

        return $this->pro_uid;
    }

    /**
     * Get the [tas_uid] column value.
     * 
     * @return     string
     */
    public function getTasUid()
    {

        return $this->tas_uid;
    }

    /**
     * Get the [optionally formatted] [sch_time_next_run] column value.
     * 
     * @param      string $format The date/time format string (either date()-style or strftime()-style).
     *                          If format is NULL, then the integer unix timestamp will be returned.
     * @return     mixed Formatted date/time value as string or integer unix timestamp (if format is NULL).
     * @throws     PropelException - if unable to convert the date/time to timestamp.
     */
    public function getSchTimeNextRun($format = 'Y-m-d H:i:s')
    {

        if ($this->sch_time_next_run === null || $this->sch_time_next_run === '') {
            return null;
        } elseif (!is_int($this->sch_time_next_run)) {
            // a non-timestamp value was set externally, so we convert it
            $ts = strtotime($this->sch_time_next_run);
            if ($ts === -1 || $ts === false) {
                throw new PropelException("Unable to parse value of [sch_time_next_run] as date/time value: " .
                    var_export($this->sch_time_next_run, true));
            }
        } else {
            $ts = $this->sch_time_next_run;
        }
        if ($format === null) {
            return $ts;
        } elseif (strpos($format, '%') !== false) {
            return strftime($format, $ts);
        } else {
            return date($format, $ts);
        }
    }

    /**
     * Get the [optionally formatted] [sch_last_run_time] column value.
     * 
     * @param      string $format The date/time format string (either date()-style or strftime()-style).
     *                          If format is NULL, then the integer unix timestamp will be returned.
     * @return     mixed Formatted date/time value as string or integer unix timestamp (if format is NULL).
     * @throws     PropelException - if unable to convert the date/time to timestamp.
     */
    public function getSchLastRunTime($format = 'Y-m-d H:i:s')
    {

        if ($this->sch_last_run_time === null || $this->sch_last_run_time === '') {
            return null;
        } elseif (!is_int($this->sch_last_run_time)) {
            // a non-timestamp value was set externally, so we convert it
            $ts = strtotime($this->sch_last_run_time);
            if ($ts === -1 || $ts === false) {
                throw new PropelException("Unable to parse value of [sch_last_run_time] as date/time value: " .
                    var_export($this->sch_last_run_time, true));
            }
        } else {
            $ts = $this->sch_last_run_time;
        }
        if ($format === null) {
            return $ts;
        } elseif (strpos($format, '%') !== false) {
            return strftime($format, $ts);
        } else {
            return date($format, $ts);
        }
    }

    /**
     * Get the [sch_state] column value.
     * 
     * @return     string
     */
    public function getSchState()
    {

        return $this->sch_state;
    }

    /**
     * Get the [sch_last_state] column value.
     * 
     * @return     string
     */
    public function getSchLastState()
    {

        return $this->sch_last_state;
    }

    /**
     * Get the [usr_uid] column value.
     * 
     * @return     string
     */
    public function getUsrUid()
    {

        return $this->usr_uid;
    }

    /**
     * Get the [sch_option] column value.
     * 
     * @return     int
     */
    public function getSchOption()
    {

        return $this->sch_option;
    }

    /**
     * Get the [optionally formatted] [sch_start_time] column value.
     * 
     * @param      string $format The date/time format string (either date()-style or strftime()-style).
     *                          If format is NULL, then the integer unix timestamp will be returned.
     * @return     mixed Formatted date/time value as string or integer unix timestamp (if format is NULL).
     * @throws     PropelException - if unable to convert the date/time to timestamp.
     */
    public function getSchStartTime($format = 'Y-m-d H:i:s')
    {

        if ($this->sch_start_time === null || $this->sch_start_time === '') {
            return null;
        } elseif (!is_int($this->sch_start_time)) {
            // a non-timestamp value was set externally, so we convert it
            $ts = strtotime($this->sch_start_time);
            if ($ts === -1 || $ts === false) {
                throw new PropelException("Unable to parse value of [sch_start_time] as date/time value: " .
                    var_export($this->sch_start_time, true));
            }
        } else {
            $ts = $this->sch_start_time;
        }
        if ($format === null) {
            return $ts;
        } elseif (strpos($format, '%') !== false) {
            return strftime($format, $ts);
        } else {
            return date($format, $ts);
        }
    }

    /**
     * Get the [optionally formatted] [sch_start_date] column value.
     * 
     * @param      string $format The date/time format string (either date()-style or strftime()-style).
     *                          If format is NULL, then the integer unix timestamp will be returned.
     * @return     mixed Formatted date/time value as string or integer unix timestamp (if format is NULL).
     * @throws     PropelException - if unable to convert the date/time to timestamp.
     */
    public function getSchStartDate($format = 'Y-m-d H:i:s')
    {

        if ($this->sch_start_date === null || $this->sch_start_date === '') {
            return null;
        } elseif (!is_int($this->sch_start_date)) {
            // a non-timestamp value was set externally, so we convert it
            $ts = strtotime($this->sch_start_date);
            if ($ts === -1 || $ts === false) {
                throw new PropelException("Unable to parse value of [sch_start_date] as date/time value: " .
                    var_export($this->sch_start_date, true));
            }
        } else {
            $ts = $this->sch_start_date;
        }
        if ($format === null) {
            return $ts;
        } elseif (strpos($format, '%') !== false) {
            return strftime($format, $ts);
        } else {
            return date($format, $ts);
        }
    }

    /**
     * Get the [sch_days_perform_task] column value.
     * 
     * @return     string
     */
    public function getSchDaysPerformTask()
    {

        return $this->sch_days_perform_task;
    }

    /**
     * Get the [sch_every_days] column value.
     * 
     * @return     int
     */
    public function getSchEveryDays()
    {

        return $this->sch_every_days;
    }

    /**
     * Get the [sch_week_days] column value.
     * 
     * @return     string
     */
    public function getSchWeekDays()
    {

        return $this->sch_week_days;
    }

    /**
     * Get the [sch_start_day] column value.
     * 
     * @return     string
     */
    public function getSchStartDay()
    {

        return $this->sch_start_day;
    }

    /**
     * Get the [sch_months] column value.
     * 
     * @return     string
     */
    public function getSchMonths()
    {

        return $this->sch_months;
    }

    /**
     * Get the [optionally formatted] [sch_end_date] column value.
     * 
     * @param      string $format The date/time format string (either date()-style or strftime()-style).
     *                          If format is NULL, then the integer unix timestamp will be returned.
     * @return     mixed Formatted date/time value as string or integer unix timestamp (if format is NULL).
     * @throws     PropelException - if unable to convert the date/time to timestamp.
     */
    public function getSchEndDate($format = 'Y-m-d H:i:s')
    {

        if ($this->sch_end_date === null || $this->sch_end_date === '') {
            return null;
        } elseif (!is_int($this->sch_end_date)) {
            // a non-timestamp value was set externally, so we convert it
            $ts = strtotime($this->sch_end_date);
            if ($ts === -1 || $ts === false) {
                throw new PropelException("Unable to parse value of [sch_end_date] as date/time value: " .
                    var_export($this->sch_end_date, true));
            }
        } else {
            $ts = $this->sch_end_date;
        }
        if ($format === null) {
            return $ts;
        } elseif (strpos($format, '%') !== false) {
            return strftime($format, $ts);
        } else {
            return date($format, $ts);
        }
    }

    /**
     * Get the [sch_repeat_every] column value.
     * 
     * @return     string
     */
    public function getSchRepeatEvery()
    {

        return $this->sch_repeat_every;
    }

    /**
     * Get the [sch_repeat_until] column value.
     * 
     * @return     string
     */
    public function getSchRepeatUntil()
    {

        return $this->sch_repeat_until;
    }

    /**
     * Get the [sch_repeat_stop_if_running] column value.
     * 
     * @return     int
     */
    public function getSchRepeatStopIfRunning()
    {

        return $this->sch_repeat_stop_if_running;
    }

    /**
     * Get the [case_sh_plugin_uid] column value.
     * 
     * @return     string
     */
    public function getCaseShPluginUid()
    {

        return $this->case_sh_plugin_uid;
    }

    /**
     * Set the value of [sch_uid] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setSchUid($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->sch_uid !== $v) {
            $this->sch_uid = $v;
            $this->modifiedColumns[] = CaseSchedulerPeer::SCH_UID;
        }

    } // setSchUid()

    /**
     * Set the value of [sch_del_user_name] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setSchDelUserName($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->sch_del_user_name !== $v) {
            $this->sch_del_user_name = $v;
            $this->modifiedColumns[] = CaseSchedulerPeer::SCH_DEL_USER_NAME;
        }

    } // setSchDelUserName()

    /**
     * Set the value of [sch_del_user_pass] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setSchDelUserPass($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->sch_del_user_pass !== $v) {
            $this->sch_del_user_pass = $v;
            $this->modifiedColumns[] = CaseSchedulerPeer::SCH_DEL_USER_PASS;
        }

    } // setSchDelUserPass()

    /**
     * Set the value of [sch_del_user_uid] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setSchDelUserUid($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->sch_del_user_uid !== $v) {
            $this->sch_del_user_uid = $v;
            $this->modifiedColumns[] = CaseSchedulerPeer::SCH_DEL_USER_UID;
        }

    } // setSchDelUserUid()

    /**
     * Set the value of [sch_name] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setSchName($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->sch_name !== $v) {
            $this->sch_name = $v;
            $this->modifiedColumns[] = CaseSchedulerPeer::SCH_NAME;
        }

    } // setSchName()

    /**
     * Set the value of [pro_uid] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setProUid($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->pro_uid !== $v || $v === '') {
            $this->pro_uid = $v;
            $this->modifiedColumns[] = CaseSchedulerPeer::PRO_UID;
        }

    } // setProUid()

    /**
     * Set the value of [tas_uid] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setTasUid($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->tas_uid !== $v || $v === '') {
            $this->tas_uid = $v;
            $this->modifiedColumns[] = CaseSchedulerPeer::TAS_UID;
        }

    } // setTasUid()

    /**
     * Set the value of [sch_time_next_run] column.
     * 
     * @param      int $v new value
     * @return     void
     */
    public function setSchTimeNextRun($v)
    {

        if ($v !== null && !is_int($v)) {
            $ts = strtotime($v);
            if ($ts === -1 || $ts === false) {
                throw new PropelException("Unable to parse date/time value for [sch_time_next_run] from input: " .
                    var_export($v, true));
            }
        } else {
            $ts = $v;
        }
        if ($this->sch_time_next_run !== $ts) {
            $this->sch_time_next_run = $ts;
            $this->modifiedColumns[] = CaseSchedulerPeer::SCH_TIME_NEXT_RUN;
        }

    } // setSchTimeNextRun()

    /**
     * Set the value of [sch_last_run_time] column.
     * 
     * @param      int $v new value
     * @return     void
     */
    public function setSchLastRunTime($v)
    {

        if ($v !== null && !is_int($v)) {
            $ts = strtotime($v);
            if ($ts === -1 || $ts === false) {
                throw new PropelException("Unable to parse date/time value for [sch_last_run_time] from input: " .
                    var_export($v, true));
            }
        } else {
            $ts = $v;
        }
        if ($this->sch_last_run_time !== $ts) {
            $this->sch_last_run_time = $ts;
            $this->modifiedColumns[] = CaseSchedulerPeer::SCH_LAST_RUN_TIME;
        }

    } // setSchLastRunTime()

    /**
     * Set the value of [sch_state] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setSchState($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->sch_state !== $v || $v === 'ACTIVE') {
            $this->sch_state = $v;
            $this->modifiedColumns[] = CaseSchedulerPeer::SCH_STATE;
        }

    } // setSchState()

    /**
     * Set the value of [sch_last_state] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setSchLastState($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->sch_last_state !== $v || $v === '') {
            $this->sch_last_state = $v;
            $this->modifiedColumns[] = CaseSchedulerPeer::SCH_LAST_STATE;
        }

    } // setSchLastState()

    /**
     * Set the value of [usr_uid] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setUsrUid($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->usr_uid !== $v || $v === '') {
            $this->usr_uid = $v;
            $this->modifiedColumns[] = CaseSchedulerPeer::USR_UID;
        }

    } // setUsrUid()

    /**
     * Set the value of [sch_option] column.
     * 
     * @param      int $v new value
     * @return     void
     */
    public function setSchOption($v)
    {

        // Since the native PHP type for this column is integer,
        // we will cast the input value to an int (if it is not).
        if ($v !== null && !is_int($v) && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->sch_option !== $v || $v === 0) {
            $this->sch_option = $v;
            $this->modifiedColumns[] = CaseSchedulerPeer::SCH_OPTION;
        }

    } // setSchOption()

    /**
     * Set the value of [sch_start_time] column.
     * 
     * @param      int $v new value
     * @return     void
     */
    public function setSchStartTime($v)
    {

        if ($v !== null && !is_int($v)) {
            $ts = strtotime($v);
            if ($ts === -1 || $ts === false) {
                throw new PropelException("Unable to parse date/time value for [sch_start_time] from input: " .
                    var_export($v, true));
            }
        } else {
            $ts = $v;
        }
        if ($this->sch_start_time !== $ts) {
            $this->sch_start_time = $ts;
            $this->modifiedColumns[] = CaseSchedulerPeer::SCH_START_TIME;
        }

    } // setSchStartTime()

    /**
     * Set the value of [sch_start_date] column.
     * 
     * @param      int $v new value
     * @return     void
     */
    public function setSchStartDate($v)
    {

        if ($v !== null && !is_int($v)) {
            $ts = strtotime($v);
            if ($ts === -1 || $ts === false) {
                throw new PropelException("Unable to parse date/time value for [sch_start_date] from input: " .
                    var_export($v, true));
            }
        } else {
            $ts = $v;
        }
        if ($this->sch_start_date !== $ts) {
            $this->sch_start_date = $ts;
            $this->modifiedColumns[] = CaseSchedulerPeer::SCH_START_DATE;
        }

    } // setSchStartDate()

    /**
     * Set the value of [sch_days_perform_task] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setSchDaysPerformTask($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->sch_days_perform_task !== $v || $v === '') {
            $this->sch_days_perform_task = $v;
            $this->modifiedColumns[] = CaseSchedulerPeer::SCH_DAYS_PERFORM_TASK;
        }

    } // setSchDaysPerformTask()

    /**
     * Set the value of [sch_every_days] column.
     * 
     * @param      int $v new value
     * @return     void
     */
    public function setSchEveryDays($v)
    {

        // Since the native PHP type for this column is integer,
        // we will cast the input value to an int (if it is not).
        if ($v !== null && !is_int($v) && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->sch_every_days !== $v || $v === 0) {
            $this->sch_every_days = $v;
            $this->modifiedColumns[] = CaseSchedulerPeer::SCH_EVERY_DAYS;
        }

    } // setSchEveryDays()

    /**
     * Set the value of [sch_week_days] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setSchWeekDays($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->sch_week_days !== $v || $v === '0|0|0|0|0|0|0') {
            $this->sch_week_days = $v;
            $this->modifiedColumns[] = CaseSchedulerPeer::SCH_WEEK_DAYS;
        }

    } // setSchWeekDays()

    /**
     * Set the value of [sch_start_day] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setSchStartDay($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->sch_start_day !== $v || $v === '') {
            $this->sch_start_day = $v;
            $this->modifiedColumns[] = CaseSchedulerPeer::SCH_START_DAY;
        }

    } // setSchStartDay()

    /**
     * Set the value of [sch_months] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setSchMonths($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->sch_months !== $v || $v === '0|0|0|0|0|0|0|0|0|0|0|0') {
            $this->sch_months = $v;
            $this->modifiedColumns[] = CaseSchedulerPeer::SCH_MONTHS;
        }

    } // setSchMonths()

    /**
     * Set the value of [sch_end_date] column.
     * 
     * @param      int $v new value
     * @return     void
     */
    public function setSchEndDate($v)
    {

        if ($v !== null && !is_int($v)) {
            $ts = strtotime($v);
            if ($ts === -1 || $ts === false) {
                throw new PropelException("Unable to parse date/time value for [sch_end_date] from input: " .
                    var_export($v, true));
            }
        } else {
            $ts = $v;
        }
        if ($this->sch_end_date !== $ts) {
            $this->sch_end_date = $ts;
            $this->modifiedColumns[] = CaseSchedulerPeer::SCH_END_DATE;
        }

    } // setSchEndDate()

    /**
     * Set the value of [sch_repeat_every] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setSchRepeatEvery($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->sch_repeat_every !== $v || $v === '') {
            $this->sch_repeat_every = $v;
            $this->modifiedColumns[] = CaseSchedulerPeer::SCH_REPEAT_EVERY;
        }

    } // setSchRepeatEvery()

    /**
     * Set the value of [sch_repeat_until] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setSchRepeatUntil($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->sch_repeat_until !== $v || $v === '') {
            $this->sch_repeat_until = $v;
            $this->modifiedColumns[] = CaseSchedulerPeer::SCH_REPEAT_UNTIL;
        }

    } // setSchRepeatUntil()

    /**
     * Set the value of [sch_repeat_stop_if_running] column.
     * 
     * @param      int $v new value
     * @return     void
     */
    public function setSchRepeatStopIfRunning($v)
    {

        // Since the native PHP type for this column is integer,
        // we will cast the input value to an int (if it is not).
        if ($v !== null && !is_int($v) && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->sch_repeat_stop_if_running !== $v || $v === 0) {
            $this->sch_repeat_stop_if_running = $v;
            $this->modifiedColumns[] = CaseSchedulerPeer::SCH_REPEAT_STOP_IF_RUNNING;
        }

    } // setSchRepeatStopIfRunning()

    /**
     * Set the value of [case_sh_plugin_uid] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setCaseShPluginUid($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->case_sh_plugin_uid !== $v) {
            $this->case_sh_plugin_uid = $v;
            $this->modifiedColumns[] = CaseSchedulerPeer::CASE_SH_PLUGIN_UID;
        }

    } // setCaseShPluginUid()

    /**
     * Hydrates (populates) the object variables with values from the database resultset.
     *
     * An offset (1-based "start column") is specified so that objects can be hydrated
     * with a subset of the columns in the resultset rows.  This is needed, for example,
     * for results of JOIN queries where the resultset row includes columns from two or
     * more tables.
     *
     * @param      ResultSet $rs The ResultSet class with cursor advanced to desired record pos.
     * @param      int $startcol 1-based offset column which indicates which restultset column to start with.
     * @return     int next starting column
     * @throws     PropelException  - Any caught Exception will be rewrapped as a PropelException.
     */
    public function hydrate(ResultSet $rs, $startcol = 1)
    {
        try {

            $this->sch_uid = $rs->getString($startcol + 0);

            $this->sch_del_user_name = $rs->getString($startcol + 1);

            $this->sch_del_user_pass = $rs->getString($startcol + 2);

            $this->sch_del_user_uid = $rs->getString($startcol + 3);

            $this->sch_name = $rs->getString($startcol + 4);

            $this->pro_uid = $rs->getString($startcol + 5);

            $this->tas_uid = $rs->getString($startcol + 6);

            $this->sch_time_next_run = $rs->getTimestamp($startcol + 7, null);

            $this->sch_last_run_time = $rs->getTimestamp($startcol + 8, null);

            $this->sch_state = $rs->getString($startcol + 9);

            $this->sch_last_state = $rs->getString($startcol + 10);

            $this->usr_uid = $rs->getString($startcol + 11);

            $this->sch_option = $rs->getInt($startcol + 12);

            $this->sch_start_time = $rs->getTimestamp($startcol + 13, null);

            $this->sch_start_date = $rs->getTimestamp($startcol + 14, null);

            $this->sch_days_perform_task = $rs->getString($startcol + 15);

            $this->sch_every_days = $rs->getInt($startcol + 16);

            $this->sch_week_days = $rs->getString($startcol + 17);

            $this->sch_start_day = $rs->getString($startcol + 18);

            $this->sch_months = $rs->getString($startcol + 19);

            $this->sch_end_date = $rs->getTimestamp($startcol + 20, null);

            $this->sch_repeat_every = $rs->getString($startcol + 21);

            $this->sch_repeat_until = $rs->getString($startcol + 22);

            $this->sch_repeat_stop_if_running = $rs->getInt($startcol + 23);

            $this->case_sh_plugin_uid = $rs->getString($startcol + 24);

            $this->resetModified();

            $this->setNew(false);

            // FIXME - using NUM_COLUMNS may be clearer.
            return $startcol + 25; // 25 = CaseSchedulerPeer::NUM_COLUMNS - CaseSchedulerPeer::NUM_LAZY_LOAD_COLUMNS).

        } catch (Exception $e) {
            throw new PropelException("Error populating CaseScheduler object", $e);
        }
    }

    /**
     * Removes this object from datastore and sets delete attribute.
     *
     * @param      Connection $con
     * @return     void
     * @throws     PropelException
     * @see        BaseObject::setDeleted()
     * @see        BaseObject::isDeleted()
     */
    public function delete($con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("This object has already been deleted.");
        }

        if ($con === null) {
            $con = Propel::getConnection(CaseSchedulerPeer::DATABASE_NAME);
        }

        try {
            $con->begin();
            CaseSchedulerPeer::doDelete($this, $con);
            $this->setDeleted(true);
            $con->commit();
        } catch (PropelException $e) {
            $con->rollback();
            throw $e;
        }
    }

    /**
     * Stores the object in the database.  If the object is new,
     * it inserts it; otherwise an update is performed.  This method
     * wraps the doSave() worker method in a transaction.
     *
     * @param      Connection $con
     * @return     int The number of rows affected by this insert/update
     * @throws     PropelException
     * @see        doSave()
     */
    public function save($con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("You cannot save an object that has been deleted.");
        }

        if ($con === null) {
            $con = Propel::getConnection(CaseSchedulerPeer::DATABASE_NAME);
        }

        try {
            $con->begin();
            $affectedRows = $this->doSave($con);
            $con->commit();
            return $affectedRows;
        } catch (PropelException $e) {
            $con->rollback();
            throw $e;
        }
    }

    /**
     * Stores the object in the database.
     *
     * If the object is new, it inserts it; otherwise an update is performed.
     * All related objects are also updated in this method.
     *
     * @param      Connection $con
     * @return     int The number of rows affected by this insert/update and any referring
     * @throws     PropelException
     * @see        save()
     */
    protected function doSave($con)
    {
        $affectedRows = 0; // initialize var to track total num of affected rows
        if (!$this->alreadyInSave) {
            $this->alreadyInSave = true;


            // If this object has been modified, then save it to the database.
            if ($this->isModified()) {
                if ($this->isNew()) {
                    $pk = CaseSchedulerPeer::doInsert($this, $con);
                    $affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
                                         // should always be true here (even though technically
                                         // BasePeer::doInsert() can insert multiple rows).

                    $this->setNew(false);
                } else {
                    $affectedRows += CaseSchedulerPeer::doUpdate($this, $con);
                }
                $this->resetModified(); // [HL] After being saved an object is no longer 'modified'
            }

            $this->alreadyInSave = false;
        }
        return $affectedRows;
    } // doSave()

    /**
     * Array of ValidationFailed objects.
     * @var        array ValidationFailed[]
     */
    protected $validationFailures = array();

    /**
     * Gets any ValidationFailed objects that resulted from last call to validate().
     *
     *
     * @return     array ValidationFailed[]
     * @see        validate()
     */
    public function getValidationFailures()
    {
        return $this->validationFailures;
    }

    /**
     * Validates the objects modified field values and all objects related to this table.
     *
     * If $columns is either a column name or an array of column names
     * only those columns are validated.
     *
     * @param      mixed $columns Column name or an array of column names.
     * @return     boolean Whether all columns pass validation.
     * @see        doValidate()
     * @see        getValidationFailures()
     */
    public function validate($columns = null)
    {
        $res = $this->doValidate($columns);
        if ($res === true) {
            $this->validationFailures = array();
            return true;
        } else {
            $this->validationFailures = $res;
            return false;
        }
    }

    /**
     * This function performs the validation work for complex object models.
     *
     * In addition to checking the current object, all related objects will
     * also be validated.  If all pass then <code>true</code> is returned; otherwise
     * an aggreagated array of ValidationFailed objects will be returned.
     *
     * @param      array $columns Array of column names to validate.
     * @return     mixed <code>true</code> if all validations pass; 
                   array of <code>ValidationFailed</code> objects otherwise.
     */
    protected function doValidate($columns = null)
    {
        if (!$this->alreadyInValidation) {
            $this->alreadyInValidation = true;
            $retval = null;

            $failureMap = array();


            if (($retval = CaseSchedulerPeer::doValidate($this, $columns)) !== true) {
                $failureMap = array_merge($failureMap, $retval);
            }



            $this->alreadyInValidation = false;
        }

        return (!empty($failureMap) ? $failureMap : true);
    }

    /**
     * Retrieves a field from the object by name passed in as a string.
     *
     * @param      string $name name
     * @param      string $type The type of fieldname the $name is of:
     *                     one of the class type constants TYPE_PHPNAME,
     *                     TYPE_COLNAME, TYPE_FIELDNAME, TYPE_NUM
     * @return     mixed Value of field.
     */
    public function getByName($name, $type = BasePeer::TYPE_PHPNAME)
    {
        $pos = CaseSchedulerPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
        return $this->getByPosition($pos);
    }

    /**
     * Retrieves a field from the object by Position as specified in the xml schema.
     * Zero-based.
     *
     * @param      int $pos position in xml schema
     * @return     mixed Value of field at $pos
     */
    public function getByPosition($pos)
    {
        switch($pos) {
            case 0:
                return $this->getSchUid();
                break;
            case 1:
                return $this->getSchDelUserName();
                break;
            case 2:
                return $this->getSchDelUserPass();
                break;
            case 3:
                return $this->getSchDelUserUid();
                break;
            case 4:
                return $this->getSchName();
                break;
            case 5:
                return $this->getProUid();
                break;
            case 6:
                return $this->getTasUid();
                break;
            case 7:
                return $this->getSchTimeNextRun();
                break;
            case 8:
                return $this->getSchLastRunTime();
                break;
            case 9:
                return $this->getSchState();
                break;
            case 10:
                return $this->getSchLastState();
                break;
            case 11:
                return $this->getUsrUid();
                break;
            case 12:
                return $this->getSchOption();
                break;
            case 13:
                return $this->getSchStartTime();
                break;
            case 14:
                return $this->getSchStartDate();
                break;
            case 15:
                return $this->getSchDaysPerformTask();
                break;
            case 16:
                return $this->getSchEveryDays();
                break;
            case 17:
                return $this->getSchWeekDays();
                break;
            case 18:
                return $this->getSchStartDay();
                break;
            case 19:
                return $this->getSchMonths();
                break;
            case 20:
                return $this->getSchEndDate();
                break;
            case 21:
                return $this->getSchRepeatEvery();
                break;
            case 22:
                return $this->getSchRepeatUntil();
                break;
            case 23:
                return $this->getSchRepeatStopIfRunning();
                break;
            case 24:
                return $this->getCaseShPluginUid();
                break;
            default:
                return null;
                break;
        } // switch()
    }

    /**
     * Exports the object as an array.
     *
     * You can specify the key type of the array by passing one of the class
     * type constants.
     *
     * @param      string $keyType One of the class type constants TYPE_PHPNAME,
     *                        TYPE_COLNAME, TYPE_FIELDNAME, TYPE_NUM
     * @return     an associative array containing the field names (as keys) and field values
     */
    public function toArray($keyType = BasePeer::TYPE_PHPNAME)
    {
        $keys = CaseSchedulerPeer::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getSchUid(),
            $keys[1] => $this->getSchDelUserName(),
            $keys[2] => $this->getSchDelUserPass(),
            $keys[3] => $this->getSchDelUserUid(),
            $keys[4] => $this->getSchName(),
            $keys[5] => $this->getProUid(),
            $keys[6] => $this->getTasUid(),
            $keys[7] => $this->getSchTimeNextRun(),
            $keys[8] => $this->getSchLastRunTime(),
            $keys[9] => $this->getSchState(),
            $keys[10] => $this->getSchLastState(),
            $keys[11] => $this->getUsrUid(),
            $keys[12] => $this->getSchOption(),
            $keys[13] => $this->getSchStartTime(),
            $keys[14] => $this->getSchStartDate(),
            $keys[15] => $this->getSchDaysPerformTask(),
            $keys[16] => $this->getSchEveryDays(),
            $keys[17] => $this->getSchWeekDays(),
            $keys[18] => $this->getSchStartDay(),
            $keys[19] => $this->getSchMonths(),
            $keys[20] => $this->getSchEndDate(),
            $keys[21] => $this->getSchRepeatEvery(),
            $keys[22] => $this->getSchRepeatUntil(),
            $keys[23] => $this->getSchRepeatStopIfRunning(),
            $keys[24] => $this->getCaseShPluginUid(),
        );
        return $result;
    }

    /**
     * Sets a field from the object by name passed in as a string.
     *
     * @param      string $name peer name
     * @param      mixed $value field value
     * @param      string $type The type of fieldname the $name is of:
     *                     one of the class type constants TYPE_PHPNAME,
     *                     TYPE_COLNAME, TYPE_FIELDNAME, TYPE_NUM
     * @return     void
     */
    public function setByName($name, $value, $type = BasePeer::TYPE_PHPNAME)
    {
        $pos = CaseSchedulerPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
        return $this->setByPosition($pos, $value);
    }

    /**
     * Sets a field from the object by Position as specified in the xml schema.
     * Zero-based.
     *
     * @param      int $pos position in xml schema
     * @param      mixed $value field value
     * @return     void
     */
    public function setByPosition($pos, $value)
    {
        switch($pos) {
            case 0:
                $this->setSchUid($value);
                break;
            case 1:
                $this->setSchDelUserName($value);
                break;
            case 2:
                $this->setSchDelUserPass($value);
                break;
            case 3:
                $this->setSchDelUserUid($value);
                break;
            case 4:
                $this->setSchName($value);
                break;
            case 5:
                $this->setProUid($value);
                break;
            case 6:
                $this->setTasUid($value);
                break;
            case 7:
                $this->setSchTimeNextRun($value);
                break;
            case 8:
                $this->setSchLastRunTime($value);
                break;
            case 9:
                $this->setSchState($value);
                break;
            case 10:
                $this->setSchLastState($value);
                break;
            case 11:
                $this->setUsrUid($value);
                break;
            case 12:
                $this->setSchOption($value);
                break;
            case 13:
                $this->setSchStartTime($value);
                break;
            case 14:
                $this->setSchStartDate($value);
                break;
            case 15:
                $this->setSchDaysPerformTask($value);
                break;
            case 16:
                $this->setSchEveryDays($value);
                break;
            case 17:
                $this->setSchWeekDays($value);
                break;
            case 18:
                $this->setSchStartDay($value);
                break;
            case 19:
                $this->setSchMonths($value);
                break;
            case 20:
                $this->setSchEndDate($value);
                break;
            case 21:
                $this->setSchRepeatEvery($value);
                break;
            case 22:
                $this->setSchRepeatUntil($value);
                break;
            case 23:
                $this->setSchRepeatStopIfRunning($value);
                break;
            case 24:
                $this->setCaseShPluginUid($value);
                break;
        } // switch()
    }

    /**
     * Populates the object using an array.
     *
     * This is particularly useful when populating an object from one of the
     * request arrays (e.g. $_POST).  This method goes through the column
     * names, checking to see whether a matching key exists in populated
     * array. If so the setByName() method is called for that column.
     *
     * You can specify the key type of the array by additionally passing one
     * of the class type constants TYPE_PHPNAME, TYPE_COLNAME, TYPE_FIELDNAME,
     * TYPE_NUM. The default key type is the column's phpname (e.g. 'authorId')
     *
     * @param      array  $arr     An array to populate the object from.
     * @param      string $keyType The type of keys the array uses.
     * @return     void
     */
    public function fromArray($arr, $keyType = BasePeer::TYPE_PHPNAME)
    {
        $keys = CaseSchedulerPeer::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) {
            $this->setSchUid($arr[$keys[0]]);
        }

        if (array_key_exists($keys[1], $arr)) {
            $this->setSchDelUserName($arr[$keys[1]]);
        }

        if (array_key_exists($keys[2], $arr)) {
            $this->setSchDelUserPass($arr[$keys[2]]);
        }

        if (array_key_exists($keys[3], $arr)) {
            $this->setSchDelUserUid($arr[$keys[3]]);
        }

        if (array_key_exists($keys[4], $arr)) {
            $this->setSchName($arr[$keys[4]]);
        }

        if (array_key_exists($keys[5], $arr)) {
            $this->setProUid($arr[$keys[5]]);
        }

        if (array_key_exists($keys[6], $arr)) {
            $this->setTasUid($arr[$keys[6]]);
        }

        if (array_key_exists($keys[7], $arr)) {
            $this->setSchTimeNextRun($arr[$keys[7]]);
        }

        if (array_key_exists($keys[8], $arr)) {
            $this->setSchLastRunTime($arr[$keys[8]]);
        }

        if (array_key_exists($keys[9], $arr)) {
            $this->setSchState($arr[$keys[9]]);
        }

        if (array_key_exists($keys[10], $arr)) {
            $this->setSchLastState($arr[$keys[10]]);
        }

        if (array_key_exists($keys[11], $arr)) {
            $this->setUsrUid($arr[$keys[11]]);
        }

        if (array_key_exists($keys[12], $arr)) {
            $this->setSchOption($arr[$keys[12]]);
        }

        if (array_key_exists($keys[13], $arr)) {
            $this->setSchStartTime($arr[$keys[13]]);
        }

        if (array_key_exists($keys[14], $arr)) {
            $this->setSchStartDate($arr[$keys[14]]);
        }

        if (array_key_exists($keys[15], $arr)) {
            $this->setSchDaysPerformTask($arr[$keys[15]]);
        }

        if (array_key_exists($keys[16], $arr)) {
            $this->setSchEveryDays($arr[$keys[16]]);
        }

        if (array_key_exists($keys[17], $arr)) {
            $this->setSchWeekDays($arr[$keys[17]]);
        }

        if (array_key_exists($keys[18], $arr)) {
            $this->setSchStartDay($arr[$keys[18]]);
        }

        if (array_key_exists($keys[19], $arr)) {
            $this->setSchMonths($arr[$keys[19]]);
        }

        if (array_key_exists($keys[20], $arr)) {
            $this->setSchEndDate($arr[$keys[20]]);
        }

        if (array_key_exists($keys[21], $arr)) {
            $this->setSchRepeatEvery($arr[$keys[21]]);
        }

        if (array_key_exists($keys[22], $arr)) {
            $this->setSchRepeatUntil($arr[$keys[22]]);
        }

        if (array_key_exists($keys[23], $arr)) {
            $this->setSchRepeatStopIfRunning($arr[$keys[23]]);
        }

        if (array_key_exists($keys[24], $arr)) {
            $this->setCaseShPluginUid($arr[$keys[24]]);
        }

    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return     Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(CaseSchedulerPeer::DATABASE_NAME);

        if ($this->isColumnModified(CaseSchedulerPeer::SCH_UID)) {
            $criteria->add(CaseSchedulerPeer::SCH_UID, $this->sch_uid);
        }

        if ($this->isColumnModified(CaseSchedulerPeer::SCH_DEL_USER_NAME)) {
            $criteria->add(CaseSchedulerPeer::SCH_DEL_USER_NAME, $this->sch_del_user_name);
        }

        if ($this->isColumnModified(CaseSchedulerPeer::SCH_DEL_USER_PASS)) {
            $criteria->add(CaseSchedulerPeer::SCH_DEL_USER_PASS, $this->sch_del_user_pass);
        }

        if ($this->isColumnModified(CaseSchedulerPeer::SCH_DEL_USER_UID)) {
            $criteria->add(CaseSchedulerPeer::SCH_DEL_USER_UID, $this->sch_del_user_uid);
        }

        if ($this->isColumnModified(CaseSchedulerPeer::SCH_NAME)) {
            $criteria->add(CaseSchedulerPeer::SCH_NAME, $this->sch_name);
        }

        if ($this->isColumnModified(CaseSchedulerPeer::PRO_UID)) {
            $criteria->add(CaseSchedulerPeer::PRO_UID, $this->pro_uid);
        }

        if ($this->isColumnModified(CaseSchedulerPeer::TAS_UID)) {
            $criteria->add(CaseSchedulerPeer::TAS_UID, $this->tas_uid);
        }

        if ($this->isColumnModified(CaseSchedulerPeer::SCH_TIME_NEXT_RUN)) {
            $criteria->add(CaseSchedulerPeer::SCH_TIME_NEXT_RUN, $this->sch_time_next_run);
        }

        if ($this->isColumnModified(CaseSchedulerPeer::SCH_LAST_RUN_TIME)) {
            $criteria->add(CaseSchedulerPeer::SCH_LAST_RUN_TIME, $this->sch_last_run_time);
        }

        if ($this->isColumnModified(CaseSchedulerPeer::SCH_STATE)) {
            $criteria->add(CaseSchedulerPeer::SCH_STATE, $this->sch_state);
        }

        if ($this->isColumnModified(CaseSchedulerPeer::SCH_LAST_STATE)) {
            $criteria->add(CaseSchedulerPeer::SCH_LAST_STATE, $this->sch_last_state);
        }

        if ($this->isColumnModified(CaseSchedulerPeer::USR_UID)) {
            $criteria->add(CaseSchedulerPeer::USR_UID, $this->usr_uid);
        }

        if ($this->isColumnModified(CaseSchedulerPeer::SCH_OPTION)) {
            $criteria->add(CaseSchedulerPeer::SCH_OPTION, $this->sch_option);
        }

        if ($this->isColumnModified(CaseSchedulerPeer::SCH_START_TIME)) {
            $criteria->add(CaseSchedulerPeer::SCH_START_TIME, $this->sch_start_time);
        }

        if ($this->isColumnModified(CaseSchedulerPeer::SCH_START_DATE)) {
            $criteria->add(CaseSchedulerPeer::SCH_START_DATE, $this->sch_start_date);
        }

        if ($this->isColumnModified(CaseSchedulerPeer::SCH_DAYS_PERFORM_TASK)) {
            $criteria->add(CaseSchedulerPeer::SCH_DAYS_PERFORM_TASK, $this->sch_days_perform_task);
        }

        if ($this->isColumnModified(CaseSchedulerPeer::SCH_EVERY_DAYS)) {
            $criteria->add(CaseSchedulerPeer::SCH_EVERY_DAYS, $this->sch_every_days);
        }

        if ($this->isColumnModified(CaseSchedulerPeer::SCH_WEEK_DAYS)) {
            $criteria->add(CaseSchedulerPeer::SCH_WEEK_DAYS, $this->sch_week_days);
        }

        if ($this->isColumnModified(CaseSchedulerPeer::SCH_START_DAY)) {
            $criteria->add(CaseSchedulerPeer::SCH_START_DAY, $this->sch_start_day);
        }

        if ($this->isColumnModified(CaseSchedulerPeer::SCH_MONTHS)) {
            $criteria->add(CaseSchedulerPeer::SCH_MONTHS, $this->sch_months);
        }

        if ($this->isColumnModified(CaseSchedulerPeer::SCH_END_DATE)) {
            $criteria->add(CaseSchedulerPeer::SCH_END_DATE, $this->sch_end_date);
        }

        if ($this->isColumnModified(CaseSchedulerPeer::SCH_REPEAT_EVERY)) {
            $criteria->add(CaseSchedulerPeer::SCH_REPEAT_EVERY, $this->sch_repeat_every);
        }

        if ($this->isColumnModified(CaseSchedulerPeer::SCH_REPEAT_UNTIL)) {
            $criteria->add(CaseSchedulerPeer::SCH_REPEAT_UNTIL, $this->sch_repeat_until);
        }

        if ($this->isColumnModified(CaseSchedulerPeer::SCH_REPEAT_STOP_IF_RUNNING)) {
            $criteria->add(CaseSchedulerPeer::SCH_REPEAT_STOP_IF_RUNNING, $this->sch_repeat_stop_if_running);
        }

        if ($this->isColumnModified(CaseSchedulerPeer::CASE_SH_PLUGIN_UID)) {
            $criteria->add(CaseSchedulerPeer::CASE_SH_PLUGIN_UID, $this->case_sh_plugin_uid);
        }


        return $criteria;
    }

    /**
     * Builds a Criteria object containing the primary key for this object.
     *
     * Unlike buildCriteria() this method includes the primary key values regardless
     * of whether or not they have been modified.
     *
     * @return     Criteria The Criteria object containing value(s) for primary key(s).
     */
    public function buildPkeyCriteria()
    {
        $criteria = new Criteria(CaseSchedulerPeer::DATABASE_NAME);

        $criteria->add(CaseSchedulerPeer::SCH_UID, $this->sch_uid);

        return $criteria;
    }

    /**
     * Returns the primary key for this object (row).
     * @return     string
     */
    public function getPrimaryKey()
    {
        return $this->getSchUid();
    }

    /**
     * Generic method to set the primary key (sch_uid column).
     *
     * @param      string $key Primary key.
     * @return     void
     */
    public function setPrimaryKey($key)
    {
        $this->setSchUid($key);
    }

    /**
     * Sets contents of passed object to values from current object.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param      object $copyObj An object of CaseScheduler (or compatible) type.
     * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @throws     PropelException
     */
    public function copyInto($copyObj, $deepCopy = false)
    {

        $copyObj->setSchDelUserName($this->sch_del_user_name);

        $copyObj->setSchDelUserPass($this->sch_del_user_pass);

        $copyObj->setSchDelUserUid($this->sch_del_user_uid);

        $copyObj->setSchName($this->sch_name);

        $copyObj->setProUid($this->pro_uid);

        $copyObj->setTasUid($this->tas_uid);

        $copyObj->setSchTimeNextRun($this->sch_time_next_run);

        $copyObj->setSchLastRunTime($this->sch_last_run_time);

        $copyObj->setSchState($this->sch_state);

        $copyObj->setSchLastState($this->sch_last_state);

        $copyObj->setUsrUid($this->usr_uid);

        $copyObj->setSchOption($this->sch_option);

        $copyObj->setSchStartTime($this->sch_start_time);

        $copyObj->setSchStartDate($this->sch_start_date);

        $copyObj->setSchDaysPerformTask($this->sch_days_perform_task);

        $copyObj->setSchEveryDays($this->sch_every_days);

        $copyObj->setSchWeekDays($this->sch_week_days);

        $copyObj->setSchStartDay($this->sch_start_day);

        $copyObj->setSchMonths($this->sch_months);

        $copyObj->setSchEndDate($this->sch_end_date);

        $copyObj->setSchRepeatEvery($this->sch_repeat_every);

        $copyObj->setSchRepeatUntil($this->sch_repeat_until);

        $copyObj->setSchRepeatStopIfRunning($this->sch_repeat_stop_if_running);

        $copyObj->setCaseShPluginUid($this->case_sh_plugin_uid);


        $copyObj->setNew(true);

        $copyObj->setSchUid(NULL); // this is a pkey column, so set to default value

    }

    /**
     * Makes a copy of this object that will be inserted as a new row in table when saved.
     * It creates a new object filling in the simple attributes, but skipping any primary
     * keys that are defined for the table.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @return     CaseScheduler Clone of current object.
     * @throws     PropelException
     */
    public function copy($deepCopy = false)
    {
        // we use get_class(), because this might be a subclass
        $clazz = get_class($this);
        $copyObj = new $clazz();
        $this->copyInto($copyObj, $deepCopy);
        return $copyObj;
    }

    /**
     * Returns a peer instance associated with this om.
     *
     * Since Peer classes are not to have any instance attributes, this method returns the
     * same instance for all member of this class. The method could therefore
     * be static, but this would prevent one from overriding the behavior.
     *
     * @return     CaseSchedulerPeer
     */
    public function getPeer()
    {
        if (self::$peer === null) {
            self::$peer = new CaseSchedulerPeer();
        }
        return self::$peer;
    }
}

