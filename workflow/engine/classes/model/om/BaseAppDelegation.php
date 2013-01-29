<?php

require_once 'propel/om/BaseObject.php';

require_once 'propel/om/Persistent.php';


include_once 'propel/util/Criteria.php';

include_once 'classes/model/AppDelegationPeer.php';

/**
 * Base class that represents a row from the 'APP_DELEGATION' table.
 *
 * 
 *
 * @package    workflow.classes.model.om
 */
abstract class BaseAppDelegation extends BaseObject implements Persistent
{

    /**
     * The Peer class.
     * Instance provides a convenient way of calling static methods on a class
     * that calling code may not be able to identify.
     * @var        AppDelegationPeer
    */
    protected static $peer;

    /**
     * The value for the app_uid field.
     * @var        string
     */
    protected $app_uid = '';

    /**
     * The value for the del_index field.
     * @var        int
     */
    protected $del_index = 0;

    /**
     * The value for the del_previous field.
     * @var        int
     */
    protected $del_previous = 0;

    /**
     * The value for the del_last_index field.
     * @var        int
     */
    protected $del_last_index = 0;

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
     * The value for the usr_uid field.
     * @var        string
     */
    protected $usr_uid = '';

    /**
     * The value for the del_type field.
     * @var        string
     */
    protected $del_type = 'NORMAL';

    /**
     * The value for the del_thread field.
     * @var        int
     */
    protected $del_thread = 0;

    /**
     * The value for the del_thread_status field.
     * @var        string
     */
    protected $del_thread_status = 'OPEN';

    /**
     * The value for the del_priority field.
     * @var        string
     */
    protected $del_priority = '3';

    /**
     * The value for the del_delegate_date field.
     * @var        int
     */
    protected $del_delegate_date;

    /**
     * The value for the del_init_date field.
     * @var        int
     */
    protected $del_init_date;

    /**
     * The value for the del_task_due_date field.
     * @var        int
     */
    protected $del_task_due_date;

    /**
     * The value for the del_finish_date field.
     * @var        int
     */
    protected $del_finish_date;

    /**
     * The value for the del_duration field.
     * @var        double
     */
    protected $del_duration = 0;

    /**
     * The value for the del_queue_duration field.
     * @var        double
     */
    protected $del_queue_duration = 0;

    /**
     * The value for the del_delay_duration field.
     * @var        double
     */
    protected $del_delay_duration = 0;

    /**
     * The value for the del_started field.
     * @var        int
     */
    protected $del_started = 0;

    /**
     * The value for the del_finished field.
     * @var        int
     */
    protected $del_finished = 0;

    /**
     * The value for the del_delayed field.
     * @var        int
     */
    protected $del_delayed = 0;

    /**
     * The value for the del_data field.
     * @var        string
     */
    protected $del_data;

    /**
     * The value for the app_overdue_percentage field.
     * @var        double
     */
    protected $app_overdue_percentage = 0;

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
     * Get the [app_uid] column value.
     * 
     * @return     string
     */
    public function getAppUid()
    {

        return $this->app_uid;
    }

    /**
     * Get the [del_index] column value.
     * 
     * @return     int
     */
    public function getDelIndex()
    {

        return $this->del_index;
    }

    /**
     * Get the [del_previous] column value.
     * 
     * @return     int
     */
    public function getDelPrevious()
    {

        return $this->del_previous;
    }

    /**
     * Get the [del_last_index] column value.
     * 
     * @return     int
     */
    public function getDelLastIndex()
    {

        return $this->del_last_index;
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
     * Get the [usr_uid] column value.
     * 
     * @return     string
     */
    public function getUsrUid()
    {

        return $this->usr_uid;
    }

    /**
     * Get the [del_type] column value.
     * 
     * @return     string
     */
    public function getDelType()
    {

        return $this->del_type;
    }

    /**
     * Get the [del_thread] column value.
     * 
     * @return     int
     */
    public function getDelThread()
    {

        return $this->del_thread;
    }

    /**
     * Get the [del_thread_status] column value.
     * 
     * @return     string
     */
    public function getDelThreadStatus()
    {

        return $this->del_thread_status;
    }

    /**
     * Get the [del_priority] column value.
     * 
     * @return     string
     */
    public function getDelPriority()
    {

        return $this->del_priority;
    }

    /**
     * Get the [optionally formatted] [del_delegate_date] column value.
     * 
     * @param      string $format The date/time format string (either date()-style or strftime()-style).
     *                          If format is NULL, then the integer unix timestamp will be returned.
     * @return     mixed Formatted date/time value as string or integer unix timestamp (if format is NULL).
     * @throws     PropelException - if unable to convert the date/time to timestamp.
     */
    public function getDelDelegateDate($format = 'Y-m-d H:i:s')
    {

        if ($this->del_delegate_date === null || $this->del_delegate_date === '') {
            return null;
        } elseif (!is_int($this->del_delegate_date)) {
            // a non-timestamp value was set externally, so we convert it
            $ts = strtotime($this->del_delegate_date);
            if ($ts === -1 || $ts === false) {
                throw new PropelException("Unable to parse value of [del_delegate_date] as date/time value: " .
                    var_export($this->del_delegate_date, true));
            }
        } else {
            $ts = $this->del_delegate_date;
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
     * Get the [optionally formatted] [del_init_date] column value.
     * 
     * @param      string $format The date/time format string (either date()-style or strftime()-style).
     *                          If format is NULL, then the integer unix timestamp will be returned.
     * @return     mixed Formatted date/time value as string or integer unix timestamp (if format is NULL).
     * @throws     PropelException - if unable to convert the date/time to timestamp.
     */
    public function getDelInitDate($format = 'Y-m-d H:i:s')
    {

        if ($this->del_init_date === null || $this->del_init_date === '') {
            return null;
        } elseif (!is_int($this->del_init_date)) {
            // a non-timestamp value was set externally, so we convert it
            $ts = strtotime($this->del_init_date);
            if ($ts === -1 || $ts === false) {
                throw new PropelException("Unable to parse value of [del_init_date] as date/time value: " .
                    var_export($this->del_init_date, true));
            }
        } else {
            $ts = $this->del_init_date;
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
     * Get the [optionally formatted] [del_task_due_date] column value.
     * 
     * @param      string $format The date/time format string (either date()-style or strftime()-style).
     *                          If format is NULL, then the integer unix timestamp will be returned.
     * @return     mixed Formatted date/time value as string or integer unix timestamp (if format is NULL).
     * @throws     PropelException - if unable to convert the date/time to timestamp.
     */
    public function getDelTaskDueDate($format = 'Y-m-d H:i:s')
    {

        if ($this->del_task_due_date === null || $this->del_task_due_date === '') {
            return null;
        } elseif (!is_int($this->del_task_due_date)) {
            // a non-timestamp value was set externally, so we convert it
            $ts = strtotime($this->del_task_due_date);
            if ($ts === -1 || $ts === false) {
                throw new PropelException("Unable to parse value of [del_task_due_date] as date/time value: " .
                    var_export($this->del_task_due_date, true));
            }
        } else {
            $ts = $this->del_task_due_date;
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
     * Get the [optionally formatted] [del_finish_date] column value.
     * 
     * @param      string $format The date/time format string (either date()-style or strftime()-style).
     *                          If format is NULL, then the integer unix timestamp will be returned.
     * @return     mixed Formatted date/time value as string or integer unix timestamp (if format is NULL).
     * @throws     PropelException - if unable to convert the date/time to timestamp.
     */
    public function getDelFinishDate($format = 'Y-m-d H:i:s')
    {

        if ($this->del_finish_date === null || $this->del_finish_date === '') {
            return null;
        } elseif (!is_int($this->del_finish_date)) {
            // a non-timestamp value was set externally, so we convert it
            $ts = strtotime($this->del_finish_date);
            if ($ts === -1 || $ts === false) {
                throw new PropelException("Unable to parse value of [del_finish_date] as date/time value: " .
                    var_export($this->del_finish_date, true));
            }
        } else {
            $ts = $this->del_finish_date;
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
     * Get the [del_duration] column value.
     * 
     * @return     double
     */
    public function getDelDuration()
    {

        return $this->del_duration;
    }

    /**
     * Get the [del_queue_duration] column value.
     * 
     * @return     double
     */
    public function getDelQueueDuration()
    {

        return $this->del_queue_duration;
    }

    /**
     * Get the [del_delay_duration] column value.
     * 
     * @return     double
     */
    public function getDelDelayDuration()
    {

        return $this->del_delay_duration;
    }

    /**
     * Get the [del_started] column value.
     * 
     * @return     int
     */
    public function getDelStarted()
    {

        return $this->del_started;
    }

    /**
     * Get the [del_finished] column value.
     * 
     * @return     int
     */
    public function getDelFinished()
    {

        return $this->del_finished;
    }

    /**
     * Get the [del_delayed] column value.
     * 
     * @return     int
     */
    public function getDelDelayed()
    {

        return $this->del_delayed;
    }

    /**
     * Get the [del_data] column value.
     * 
     * @return     string
     */
    public function getDelData()
    {

        return $this->del_data;
    }

    /**
     * Get the [app_overdue_percentage] column value.
     * 
     * @return     double
     */
    public function getAppOverduePercentage()
    {

        return $this->app_overdue_percentage;
    }

    /**
     * Set the value of [app_uid] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setAppUid($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->app_uid !== $v || $v === '') {
            $this->app_uid = $v;
            $this->modifiedColumns[] = AppDelegationPeer::APP_UID;
        }

    } // setAppUid()

    /**
     * Set the value of [del_index] column.
     * 
     * @param      int $v new value
     * @return     void
     */
    public function setDelIndex($v)
    {

        // Since the native PHP type for this column is integer,
        // we will cast the input value to an int (if it is not).
        if ($v !== null && !is_int($v) && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->del_index !== $v || $v === 0) {
            $this->del_index = $v;
            $this->modifiedColumns[] = AppDelegationPeer::DEL_INDEX;
        }

    } // setDelIndex()

    /**
     * Set the value of [del_previous] column.
     * 
     * @param      int $v new value
     * @return     void
     */
    public function setDelPrevious($v)
    {

        // Since the native PHP type for this column is integer,
        // we will cast the input value to an int (if it is not).
        if ($v !== null && !is_int($v) && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->del_previous !== $v || $v === 0) {
            $this->del_previous = $v;
            $this->modifiedColumns[] = AppDelegationPeer::DEL_PREVIOUS;
        }

    } // setDelPrevious()

    /**
     * Set the value of [del_last_index] column.
     * 
     * @param      int $v new value
     * @return     void
     */
    public function setDelLastIndex($v)
    {

        // Since the native PHP type for this column is integer,
        // we will cast the input value to an int (if it is not).
        if ($v !== null && !is_int($v) && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->del_last_index !== $v || $v === 0) {
            $this->del_last_index = $v;
            $this->modifiedColumns[] = AppDelegationPeer::DEL_LAST_INDEX;
        }

    } // setDelLastIndex()

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
            $this->modifiedColumns[] = AppDelegationPeer::PRO_UID;
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
            $this->modifiedColumns[] = AppDelegationPeer::TAS_UID;
        }

    } // setTasUid()

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
            $this->modifiedColumns[] = AppDelegationPeer::USR_UID;
        }

    } // setUsrUid()

    /**
     * Set the value of [del_type] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setDelType($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->del_type !== $v || $v === 'NORMAL') {
            $this->del_type = $v;
            $this->modifiedColumns[] = AppDelegationPeer::DEL_TYPE;
        }

    } // setDelType()

    /**
     * Set the value of [del_thread] column.
     * 
     * @param      int $v new value
     * @return     void
     */
    public function setDelThread($v)
    {

        // Since the native PHP type for this column is integer,
        // we will cast the input value to an int (if it is not).
        if ($v !== null && !is_int($v) && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->del_thread !== $v || $v === 0) {
            $this->del_thread = $v;
            $this->modifiedColumns[] = AppDelegationPeer::DEL_THREAD;
        }

    } // setDelThread()

    /**
     * Set the value of [del_thread_status] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setDelThreadStatus($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->del_thread_status !== $v || $v === 'OPEN') {
            $this->del_thread_status = $v;
            $this->modifiedColumns[] = AppDelegationPeer::DEL_THREAD_STATUS;
        }

    } // setDelThreadStatus()

    /**
     * Set the value of [del_priority] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setDelPriority($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->del_priority !== $v || $v === '3') {
            $this->del_priority = $v;
            $this->modifiedColumns[] = AppDelegationPeer::DEL_PRIORITY;
        }

    } // setDelPriority()

    /**
     * Set the value of [del_delegate_date] column.
     * 
     * @param      int $v new value
     * @return     void
     */
    public function setDelDelegateDate($v)
    {

        if ($v !== null && !is_int($v)) {
            $ts = strtotime($v);
            if ($ts === -1 || $ts === false) {
                throw new PropelException("Unable to parse date/time value for [del_delegate_date] from input: " .
                    var_export($v, true));
            }
        } else {
            $ts = $v;
        }
        if ($this->del_delegate_date !== $ts) {
            $this->del_delegate_date = $ts;
            $this->modifiedColumns[] = AppDelegationPeer::DEL_DELEGATE_DATE;
        }

    } // setDelDelegateDate()

    /**
     * Set the value of [del_init_date] column.
     * 
     * @param      int $v new value
     * @return     void
     */
    public function setDelInitDate($v)
    {

        if ($v !== null && !is_int($v)) {
            $ts = strtotime($v);
            if ($ts === -1 || $ts === false) {
                throw new PropelException("Unable to parse date/time value for [del_init_date] from input: " .
                    var_export($v, true));
            }
        } else {
            $ts = $v;
        }
        if ($this->del_init_date !== $ts) {
            $this->del_init_date = $ts;
            $this->modifiedColumns[] = AppDelegationPeer::DEL_INIT_DATE;
        }

    } // setDelInitDate()

    /**
     * Set the value of [del_task_due_date] column.
     * 
     * @param      int $v new value
     * @return     void
     */
    public function setDelTaskDueDate($v)
    {

        if ($v !== null && !is_int($v)) {
            $ts = strtotime($v);
            if ($ts === -1 || $ts === false) {
                throw new PropelException("Unable to parse date/time value for [del_task_due_date] from input: " .
                    var_export($v, true));
            }
        } else {
            $ts = $v;
        }
        if ($this->del_task_due_date !== $ts) {
            $this->del_task_due_date = $ts;
            $this->modifiedColumns[] = AppDelegationPeer::DEL_TASK_DUE_DATE;
        }

    } // setDelTaskDueDate()

    /**
     * Set the value of [del_finish_date] column.
     * 
     * @param      int $v new value
     * @return     void
     */
    public function setDelFinishDate($v)
    {

        if ($v !== null && !is_int($v)) {
            $ts = strtotime($v);
            if ($ts === -1 || $ts === false) {
                throw new PropelException("Unable to parse date/time value for [del_finish_date] from input: " .
                    var_export($v, true));
            }
        } else {
            $ts = $v;
        }
        if ($this->del_finish_date !== $ts) {
            $this->del_finish_date = $ts;
            $this->modifiedColumns[] = AppDelegationPeer::DEL_FINISH_DATE;
        }

    } // setDelFinishDate()

    /**
     * Set the value of [del_duration] column.
     * 
     * @param      double $v new value
     * @return     void
     */
    public function setDelDuration($v)
    {

        if ($this->del_duration !== $v || $v === 0) {
            $this->del_duration = $v;
            $this->modifiedColumns[] = AppDelegationPeer::DEL_DURATION;
        }

    } // setDelDuration()

    /**
     * Set the value of [del_queue_duration] column.
     * 
     * @param      double $v new value
     * @return     void
     */
    public function setDelQueueDuration($v)
    {

        if ($this->del_queue_duration !== $v || $v === 0) {
            $this->del_queue_duration = $v;
            $this->modifiedColumns[] = AppDelegationPeer::DEL_QUEUE_DURATION;
        }

    } // setDelQueueDuration()

    /**
     * Set the value of [del_delay_duration] column.
     * 
     * @param      double $v new value
     * @return     void
     */
    public function setDelDelayDuration($v)
    {

        if ($this->del_delay_duration !== $v || $v === 0) {
            $this->del_delay_duration = $v;
            $this->modifiedColumns[] = AppDelegationPeer::DEL_DELAY_DURATION;
        }

    } // setDelDelayDuration()

    /**
     * Set the value of [del_started] column.
     * 
     * @param      int $v new value
     * @return     void
     */
    public function setDelStarted($v)
    {

        // Since the native PHP type for this column is integer,
        // we will cast the input value to an int (if it is not).
        if ($v !== null && !is_int($v) && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->del_started !== $v || $v === 0) {
            $this->del_started = $v;
            $this->modifiedColumns[] = AppDelegationPeer::DEL_STARTED;
        }

    } // setDelStarted()

    /**
     * Set the value of [del_finished] column.
     * 
     * @param      int $v new value
     * @return     void
     */
    public function setDelFinished($v)
    {

        // Since the native PHP type for this column is integer,
        // we will cast the input value to an int (if it is not).
        if ($v !== null && !is_int($v) && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->del_finished !== $v || $v === 0) {
            $this->del_finished = $v;
            $this->modifiedColumns[] = AppDelegationPeer::DEL_FINISHED;
        }

    } // setDelFinished()

    /**
     * Set the value of [del_delayed] column.
     * 
     * @param      int $v new value
     * @return     void
     */
    public function setDelDelayed($v)
    {

        // Since the native PHP type for this column is integer,
        // we will cast the input value to an int (if it is not).
        if ($v !== null && !is_int($v) && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->del_delayed !== $v || $v === 0) {
            $this->del_delayed = $v;
            $this->modifiedColumns[] = AppDelegationPeer::DEL_DELAYED;
        }

    } // setDelDelayed()

    /**
     * Set the value of [del_data] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setDelData($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->del_data !== $v) {
            $this->del_data = $v;
            $this->modifiedColumns[] = AppDelegationPeer::DEL_DATA;
        }

    } // setDelData()

    /**
     * Set the value of [app_overdue_percentage] column.
     * 
     * @param      double $v new value
     * @return     void
     */
    public function setAppOverduePercentage($v)
    {

        if ($this->app_overdue_percentage !== $v || $v === 0) {
            $this->app_overdue_percentage = $v;
            $this->modifiedColumns[] = AppDelegationPeer::APP_OVERDUE_PERCENTAGE;
        }

    } // setAppOverduePercentage()

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

            $this->app_uid = $rs->getString($startcol + 0);

            $this->del_index = $rs->getInt($startcol + 1);

            $this->del_previous = $rs->getInt($startcol + 2);

            $this->del_last_index = $rs->getInt($startcol + 3);

            $this->pro_uid = $rs->getString($startcol + 4);

            $this->tas_uid = $rs->getString($startcol + 5);

            $this->usr_uid = $rs->getString($startcol + 6);

            $this->del_type = $rs->getString($startcol + 7);

            $this->del_thread = $rs->getInt($startcol + 8);

            $this->del_thread_status = $rs->getString($startcol + 9);

            $this->del_priority = $rs->getString($startcol + 10);

            $this->del_delegate_date = $rs->getTimestamp($startcol + 11, null);

            $this->del_init_date = $rs->getTimestamp($startcol + 12, null);

            $this->del_task_due_date = $rs->getTimestamp($startcol + 13, null);

            $this->del_finish_date = $rs->getTimestamp($startcol + 14, null);

            $this->del_duration = $rs->getFloat($startcol + 15);

            $this->del_queue_duration = $rs->getFloat($startcol + 16);

            $this->del_delay_duration = $rs->getFloat($startcol + 17);

            $this->del_started = $rs->getInt($startcol + 18);

            $this->del_finished = $rs->getInt($startcol + 19);

            $this->del_delayed = $rs->getInt($startcol + 20);

            $this->del_data = $rs->getString($startcol + 21);

            $this->app_overdue_percentage = $rs->getFloat($startcol + 22);

            $this->resetModified();

            $this->setNew(false);

            // FIXME - using NUM_COLUMNS may be clearer.
            return $startcol + 23; // 23 = AppDelegationPeer::NUM_COLUMNS - AppDelegationPeer::NUM_LAZY_LOAD_COLUMNS).

        } catch (Exception $e) {
            throw new PropelException("Error populating AppDelegation object", $e);
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
            $con = Propel::getConnection(AppDelegationPeer::DATABASE_NAME);
        }

        try {
            $con->begin();
            AppDelegationPeer::doDelete($this, $con);
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
            $con = Propel::getConnection(AppDelegationPeer::DATABASE_NAME);
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
                    $pk = AppDelegationPeer::doInsert($this, $con);
                    $affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
                                         // should always be true here (even though technically
                                         // BasePeer::doInsert() can insert multiple rows).

                    $this->setNew(false);
                } else {
                    $affectedRows += AppDelegationPeer::doUpdate($this, $con);
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


            if (($retval = AppDelegationPeer::doValidate($this, $columns)) !== true) {
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
        $pos = AppDelegationPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                return $this->getAppUid();
                break;
            case 1:
                return $this->getDelIndex();
                break;
            case 2:
                return $this->getDelPrevious();
                break;
            case 3:
                return $this->getDelLastIndex();
                break;
            case 4:
                return $this->getProUid();
                break;
            case 5:
                return $this->getTasUid();
                break;
            case 6:
                return $this->getUsrUid();
                break;
            case 7:
                return $this->getDelType();
                break;
            case 8:
                return $this->getDelThread();
                break;
            case 9:
                return $this->getDelThreadStatus();
                break;
            case 10:
                return $this->getDelPriority();
                break;
            case 11:
                return $this->getDelDelegateDate();
                break;
            case 12:
                return $this->getDelInitDate();
                break;
            case 13:
                return $this->getDelTaskDueDate();
                break;
            case 14:
                return $this->getDelFinishDate();
                break;
            case 15:
                return $this->getDelDuration();
                break;
            case 16:
                return $this->getDelQueueDuration();
                break;
            case 17:
                return $this->getDelDelayDuration();
                break;
            case 18:
                return $this->getDelStarted();
                break;
            case 19:
                return $this->getDelFinished();
                break;
            case 20:
                return $this->getDelDelayed();
                break;
            case 21:
                return $this->getDelData();
                break;
            case 22:
                return $this->getAppOverduePercentage();
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
        $keys = AppDelegationPeer::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getAppUid(),
            $keys[1] => $this->getDelIndex(),
            $keys[2] => $this->getDelPrevious(),
            $keys[3] => $this->getDelLastIndex(),
            $keys[4] => $this->getProUid(),
            $keys[5] => $this->getTasUid(),
            $keys[6] => $this->getUsrUid(),
            $keys[7] => $this->getDelType(),
            $keys[8] => $this->getDelThread(),
            $keys[9] => $this->getDelThreadStatus(),
            $keys[10] => $this->getDelPriority(),
            $keys[11] => $this->getDelDelegateDate(),
            $keys[12] => $this->getDelInitDate(),
            $keys[13] => $this->getDelTaskDueDate(),
            $keys[14] => $this->getDelFinishDate(),
            $keys[15] => $this->getDelDuration(),
            $keys[16] => $this->getDelQueueDuration(),
            $keys[17] => $this->getDelDelayDuration(),
            $keys[18] => $this->getDelStarted(),
            $keys[19] => $this->getDelFinished(),
            $keys[20] => $this->getDelDelayed(),
            $keys[21] => $this->getDelData(),
            $keys[22] => $this->getAppOverduePercentage(),
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
        $pos = AppDelegationPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                $this->setAppUid($value);
                break;
            case 1:
                $this->setDelIndex($value);
                break;
            case 2:
                $this->setDelPrevious($value);
                break;
            case 3:
                $this->setDelLastIndex($value);
                break;
            case 4:
                $this->setProUid($value);
                break;
            case 5:
                $this->setTasUid($value);
                break;
            case 6:
                $this->setUsrUid($value);
                break;
            case 7:
                $this->setDelType($value);
                break;
            case 8:
                $this->setDelThread($value);
                break;
            case 9:
                $this->setDelThreadStatus($value);
                break;
            case 10:
                $this->setDelPriority($value);
                break;
            case 11:
                $this->setDelDelegateDate($value);
                break;
            case 12:
                $this->setDelInitDate($value);
                break;
            case 13:
                $this->setDelTaskDueDate($value);
                break;
            case 14:
                $this->setDelFinishDate($value);
                break;
            case 15:
                $this->setDelDuration($value);
                break;
            case 16:
                $this->setDelQueueDuration($value);
                break;
            case 17:
                $this->setDelDelayDuration($value);
                break;
            case 18:
                $this->setDelStarted($value);
                break;
            case 19:
                $this->setDelFinished($value);
                break;
            case 20:
                $this->setDelDelayed($value);
                break;
            case 21:
                $this->setDelData($value);
                break;
            case 22:
                $this->setAppOverduePercentage($value);
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
        $keys = AppDelegationPeer::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) {
            $this->setAppUid($arr[$keys[0]]);
        }

        if (array_key_exists($keys[1], $arr)) {
            $this->setDelIndex($arr[$keys[1]]);
        }

        if (array_key_exists($keys[2], $arr)) {
            $this->setDelPrevious($arr[$keys[2]]);
        }

        if (array_key_exists($keys[3], $arr)) {
            $this->setDelLastIndex($arr[$keys[3]]);
        }

        if (array_key_exists($keys[4], $arr)) {
            $this->setProUid($arr[$keys[4]]);
        }

        if (array_key_exists($keys[5], $arr)) {
            $this->setTasUid($arr[$keys[5]]);
        }

        if (array_key_exists($keys[6], $arr)) {
            $this->setUsrUid($arr[$keys[6]]);
        }

        if (array_key_exists($keys[7], $arr)) {
            $this->setDelType($arr[$keys[7]]);
        }

        if (array_key_exists($keys[8], $arr)) {
            $this->setDelThread($arr[$keys[8]]);
        }

        if (array_key_exists($keys[9], $arr)) {
            $this->setDelThreadStatus($arr[$keys[9]]);
        }

        if (array_key_exists($keys[10], $arr)) {
            $this->setDelPriority($arr[$keys[10]]);
        }

        if (array_key_exists($keys[11], $arr)) {
            $this->setDelDelegateDate($arr[$keys[11]]);
        }

        if (array_key_exists($keys[12], $arr)) {
            $this->setDelInitDate($arr[$keys[12]]);
        }

        if (array_key_exists($keys[13], $arr)) {
            $this->setDelTaskDueDate($arr[$keys[13]]);
        }

        if (array_key_exists($keys[14], $arr)) {
            $this->setDelFinishDate($arr[$keys[14]]);
        }

        if (array_key_exists($keys[15], $arr)) {
            $this->setDelDuration($arr[$keys[15]]);
        }

        if (array_key_exists($keys[16], $arr)) {
            $this->setDelQueueDuration($arr[$keys[16]]);
        }

        if (array_key_exists($keys[17], $arr)) {
            $this->setDelDelayDuration($arr[$keys[17]]);
        }

        if (array_key_exists($keys[18], $arr)) {
            $this->setDelStarted($arr[$keys[18]]);
        }

        if (array_key_exists($keys[19], $arr)) {
            $this->setDelFinished($arr[$keys[19]]);
        }

        if (array_key_exists($keys[20], $arr)) {
            $this->setDelDelayed($arr[$keys[20]]);
        }

        if (array_key_exists($keys[21], $arr)) {
            $this->setDelData($arr[$keys[21]]);
        }

        if (array_key_exists($keys[22], $arr)) {
            $this->setAppOverduePercentage($arr[$keys[22]]);
        }

    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return     Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(AppDelegationPeer::DATABASE_NAME);

        if ($this->isColumnModified(AppDelegationPeer::APP_UID)) {
            $criteria->add(AppDelegationPeer::APP_UID, $this->app_uid);
        }

        if ($this->isColumnModified(AppDelegationPeer::DEL_INDEX)) {
            $criteria->add(AppDelegationPeer::DEL_INDEX, $this->del_index);
        }

        if ($this->isColumnModified(AppDelegationPeer::DEL_PREVIOUS)) {
            $criteria->add(AppDelegationPeer::DEL_PREVIOUS, $this->del_previous);
        }

        if ($this->isColumnModified(AppDelegationPeer::DEL_LAST_INDEX)) {
            $criteria->add(AppDelegationPeer::DEL_LAST_INDEX, $this->del_last_index);
        }

        if ($this->isColumnModified(AppDelegationPeer::PRO_UID)) {
            $criteria->add(AppDelegationPeer::PRO_UID, $this->pro_uid);
        }

        if ($this->isColumnModified(AppDelegationPeer::TAS_UID)) {
            $criteria->add(AppDelegationPeer::TAS_UID, $this->tas_uid);
        }

        if ($this->isColumnModified(AppDelegationPeer::USR_UID)) {
            $criteria->add(AppDelegationPeer::USR_UID, $this->usr_uid);
        }

        if ($this->isColumnModified(AppDelegationPeer::DEL_TYPE)) {
            $criteria->add(AppDelegationPeer::DEL_TYPE, $this->del_type);
        }

        if ($this->isColumnModified(AppDelegationPeer::DEL_THREAD)) {
            $criteria->add(AppDelegationPeer::DEL_THREAD, $this->del_thread);
        }

        if ($this->isColumnModified(AppDelegationPeer::DEL_THREAD_STATUS)) {
            $criteria->add(AppDelegationPeer::DEL_THREAD_STATUS, $this->del_thread_status);
        }

        if ($this->isColumnModified(AppDelegationPeer::DEL_PRIORITY)) {
            $criteria->add(AppDelegationPeer::DEL_PRIORITY, $this->del_priority);
        }

        if ($this->isColumnModified(AppDelegationPeer::DEL_DELEGATE_DATE)) {
            $criteria->add(AppDelegationPeer::DEL_DELEGATE_DATE, $this->del_delegate_date);
        }

        if ($this->isColumnModified(AppDelegationPeer::DEL_INIT_DATE)) {
            $criteria->add(AppDelegationPeer::DEL_INIT_DATE, $this->del_init_date);
        }

        if ($this->isColumnModified(AppDelegationPeer::DEL_TASK_DUE_DATE)) {
            $criteria->add(AppDelegationPeer::DEL_TASK_DUE_DATE, $this->del_task_due_date);
        }

        if ($this->isColumnModified(AppDelegationPeer::DEL_FINISH_DATE)) {
            $criteria->add(AppDelegationPeer::DEL_FINISH_DATE, $this->del_finish_date);
        }

        if ($this->isColumnModified(AppDelegationPeer::DEL_DURATION)) {
            $criteria->add(AppDelegationPeer::DEL_DURATION, $this->del_duration);
        }

        if ($this->isColumnModified(AppDelegationPeer::DEL_QUEUE_DURATION)) {
            $criteria->add(AppDelegationPeer::DEL_QUEUE_DURATION, $this->del_queue_duration);
        }

        if ($this->isColumnModified(AppDelegationPeer::DEL_DELAY_DURATION)) {
            $criteria->add(AppDelegationPeer::DEL_DELAY_DURATION, $this->del_delay_duration);
        }

        if ($this->isColumnModified(AppDelegationPeer::DEL_STARTED)) {
            $criteria->add(AppDelegationPeer::DEL_STARTED, $this->del_started);
        }

        if ($this->isColumnModified(AppDelegationPeer::DEL_FINISHED)) {
            $criteria->add(AppDelegationPeer::DEL_FINISHED, $this->del_finished);
        }

        if ($this->isColumnModified(AppDelegationPeer::DEL_DELAYED)) {
            $criteria->add(AppDelegationPeer::DEL_DELAYED, $this->del_delayed);
        }

        if ($this->isColumnModified(AppDelegationPeer::DEL_DATA)) {
            $criteria->add(AppDelegationPeer::DEL_DATA, $this->del_data);
        }

        if ($this->isColumnModified(AppDelegationPeer::APP_OVERDUE_PERCENTAGE)) {
            $criteria->add(AppDelegationPeer::APP_OVERDUE_PERCENTAGE, $this->app_overdue_percentage);
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
        $criteria = new Criteria(AppDelegationPeer::DATABASE_NAME);

        $criteria->add(AppDelegationPeer::APP_UID, $this->app_uid);
        $criteria->add(AppDelegationPeer::DEL_INDEX, $this->del_index);

        return $criteria;
    }

    /**
     * Returns the composite primary key for this object.
     * The array elements will be in same order as specified in XML.
     * @return     array
     */
    public function getPrimaryKey()
    {
        $pks = array();

        $pks[0] = $this->getAppUid();

        $pks[1] = $this->getDelIndex();

        return $pks;
    }

    /**
     * Set the [composite] primary key.
     *
     * @param      array $keys The elements of the composite key (order must match the order in XML file).
     * @return     void
     */
    public function setPrimaryKey($keys)
    {

        $this->setAppUid($keys[0]);

        $this->setDelIndex($keys[1]);

    }

    /**
     * Sets contents of passed object to values from current object.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param      object $copyObj An object of AppDelegation (or compatible) type.
     * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @throws     PropelException
     */
    public function copyInto($copyObj, $deepCopy = false)
    {

        $copyObj->setDelPrevious($this->del_previous);

        $copyObj->setDelLastIndex($this->del_last_index);

        $copyObj->setProUid($this->pro_uid);

        $copyObj->setTasUid($this->tas_uid);

        $copyObj->setUsrUid($this->usr_uid);

        $copyObj->setDelType($this->del_type);

        $copyObj->setDelThread($this->del_thread);

        $copyObj->setDelThreadStatus($this->del_thread_status);

        $copyObj->setDelPriority($this->del_priority);

        $copyObj->setDelDelegateDate($this->del_delegate_date);

        $copyObj->setDelInitDate($this->del_init_date);

        $copyObj->setDelTaskDueDate($this->del_task_due_date);

        $copyObj->setDelFinishDate($this->del_finish_date);

        $copyObj->setDelDuration($this->del_duration);

        $copyObj->setDelQueueDuration($this->del_queue_duration);

        $copyObj->setDelDelayDuration($this->del_delay_duration);

        $copyObj->setDelStarted($this->del_started);

        $copyObj->setDelFinished($this->del_finished);

        $copyObj->setDelDelayed($this->del_delayed);

        $copyObj->setDelData($this->del_data);

        $copyObj->setAppOverduePercentage($this->app_overdue_percentage);


        $copyObj->setNew(true);

        $copyObj->setAppUid(''); // this is a pkey column, so set to default value

        $copyObj->setDelIndex('0'); // this is a pkey column, so set to default value

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
     * @return     AppDelegation Clone of current object.
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
     * @return     AppDelegationPeer
     */
    public function getPeer()
    {
        if (self::$peer === null) {
            self::$peer = new AppDelegationPeer();
        }
        return self::$peer;
    }
}

