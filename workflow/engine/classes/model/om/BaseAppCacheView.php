<?php

require_once 'propel/om/BaseObject.php';

require_once 'propel/om/Persistent.php';


include_once 'propel/util/Criteria.php';

include_once 'classes/model/AppCacheViewPeer.php';

/**
 * Base class that represents a row from the 'APP_CACHE_VIEW' table.
 *
 * 
 *
 * @package    workflow.classes.model.om
 */
abstract class BaseAppCacheView extends BaseObject implements Persistent
{

    /**
     * The Peer class.
     * Instance provides a convenient way of calling static methods on a class
     * that calling code may not be able to identify.
     * @var        AppCacheViewPeer
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
     * The value for the del_last_index field.
     * @var        int
     */
    protected $del_last_index = 0;

    /**
     * The value for the app_number field.
     * @var        int
     */
    protected $app_number = 0;

    /**
     * The value for the app_status field.
     * @var        string
     */
    protected $app_status = '';

    /**
     * The value for the usr_uid field.
     * @var        string
     */
    protected $usr_uid = '';

    /**
     * The value for the previous_usr_uid field.
     * @var        string
     */
    protected $previous_usr_uid = '';

    /**
     * The value for the tas_uid field.
     * @var        string
     */
    protected $tas_uid = '';

    /**
     * The value for the pro_uid field.
     * @var        string
     */
    protected $pro_uid = '';

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
     * The value for the del_thread_status field.
     * @var        string
     */
    protected $del_thread_status = 'OPEN';

    /**
     * The value for the app_thread_status field.
     * @var        string
     */
    protected $app_thread_status = 'OPEN';

    /**
     * The value for the app_title field.
     * @var        string
     */
    protected $app_title = '';

    /**
     * The value for the app_pro_title field.
     * @var        string
     */
    protected $app_pro_title = '';

    /**
     * The value for the app_tas_title field.
     * @var        string
     */
    protected $app_tas_title = '';

    /**
     * The value for the app_current_user field.
     * @var        string
     */
    protected $app_current_user = '';

    /**
     * The value for the app_del_previous_user field.
     * @var        string
     */
    protected $app_del_previous_user = '';

    /**
     * The value for the del_priority field.
     * @var        string
     */
    protected $del_priority = '3';

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
     * The value for the app_create_date field.
     * @var        int
     */
    protected $app_create_date;

    /**
     * The value for the app_finish_date field.
     * @var        int
     */
    protected $app_finish_date;

    /**
     * The value for the app_update_date field.
     * @var        int
     */
    protected $app_update_date;

    /**
     * The value for the app_overdue_percentage field.
     * @var        double
     */
    protected $app_overdue_percentage;

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
     * Get the [del_last_index] column value.
     * 
     * @return     int
     */
    public function getDelLastIndex()
    {

        return $this->del_last_index;
    }

    /**
     * Get the [app_number] column value.
     * 
     * @return     int
     */
    public function getAppNumber()
    {

        return $this->app_number;
    }

    /**
     * Get the [app_status] column value.
     * 
     * @return     string
     */
    public function getAppStatus()
    {

        return $this->app_status;
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
     * Get the [previous_usr_uid] column value.
     * 
     * @return     string
     */
    public function getPreviousUsrUid()
    {

        return $this->previous_usr_uid;
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
     * Get the [pro_uid] column value.
     * 
     * @return     string
     */
    public function getProUid()
    {

        return $this->pro_uid;
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
     * Get the [del_thread_status] column value.
     * 
     * @return     string
     */
    public function getDelThreadStatus()
    {

        return $this->del_thread_status;
    }

    /**
     * Get the [app_thread_status] column value.
     * 
     * @return     string
     */
    public function getAppThreadStatus()
    {

        return $this->app_thread_status;
    }

    /**
     * Get the [app_title] column value.
     * 
     * @return     string
     */
    public function getAppTitle()
    {

        return $this->app_title;
    }

    /**
     * Get the [app_pro_title] column value.
     * 
     * @return     string
     */
    public function getAppProTitle()
    {

        return $this->app_pro_title;
    }

    /**
     * Get the [app_tas_title] column value.
     * 
     * @return     string
     */
    public function getAppTasTitle()
    {

        return $this->app_tas_title;
    }

    /**
     * Get the [app_current_user] column value.
     * 
     * @return     string
     */
    public function getAppCurrentUser()
    {

        return $this->app_current_user;
    }

    /**
     * Get the [app_del_previous_user] column value.
     * 
     * @return     string
     */
    public function getAppDelPreviousUser()
    {

        return $this->app_del_previous_user;
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
     * Get the [optionally formatted] [app_create_date] column value.
     * 
     * @param      string $format The date/time format string (either date()-style or strftime()-style).
     *                          If format is NULL, then the integer unix timestamp will be returned.
     * @return     mixed Formatted date/time value as string or integer unix timestamp (if format is NULL).
     * @throws     PropelException - if unable to convert the date/time to timestamp.
     */
    public function getAppCreateDate($format = 'Y-m-d H:i:s')
    {

        if ($this->app_create_date === null || $this->app_create_date === '') {
            return null;
        } elseif (!is_int($this->app_create_date)) {
            // a non-timestamp value was set externally, so we convert it
            $ts = strtotime($this->app_create_date);
            if ($ts === -1 || $ts === false) {
                throw new PropelException("Unable to parse value of [app_create_date] as date/time value: " .
                    var_export($this->app_create_date, true));
            }
        } else {
            $ts = $this->app_create_date;
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
     * Get the [optionally formatted] [app_finish_date] column value.
     * 
     * @param      string $format The date/time format string (either date()-style or strftime()-style).
     *                          If format is NULL, then the integer unix timestamp will be returned.
     * @return     mixed Formatted date/time value as string or integer unix timestamp (if format is NULL).
     * @throws     PropelException - if unable to convert the date/time to timestamp.
     */
    public function getAppFinishDate($format = 'Y-m-d H:i:s')
    {

        if ($this->app_finish_date === null || $this->app_finish_date === '') {
            return null;
        } elseif (!is_int($this->app_finish_date)) {
            // a non-timestamp value was set externally, so we convert it
            $ts = strtotime($this->app_finish_date);
            if ($ts === -1 || $ts === false) {
                throw new PropelException("Unable to parse value of [app_finish_date] as date/time value: " .
                    var_export($this->app_finish_date, true));
            }
        } else {
            $ts = $this->app_finish_date;
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
     * Get the [optionally formatted] [app_update_date] column value.
     * 
     * @param      string $format The date/time format string (either date()-style or strftime()-style).
     *                          If format is NULL, then the integer unix timestamp will be returned.
     * @return     mixed Formatted date/time value as string or integer unix timestamp (if format is NULL).
     * @throws     PropelException - if unable to convert the date/time to timestamp.
     */
    public function getAppUpdateDate($format = 'Y-m-d H:i:s')
    {

        if ($this->app_update_date === null || $this->app_update_date === '') {
            return null;
        } elseif (!is_int($this->app_update_date)) {
            // a non-timestamp value was set externally, so we convert it
            $ts = strtotime($this->app_update_date);
            if ($ts === -1 || $ts === false) {
                throw new PropelException("Unable to parse value of [app_update_date] as date/time value: " .
                    var_export($this->app_update_date, true));
            }
        } else {
            $ts = $this->app_update_date;
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
            $this->modifiedColumns[] = AppCacheViewPeer::APP_UID;
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
            $this->modifiedColumns[] = AppCacheViewPeer::DEL_INDEX;
        }

    } // setDelIndex()

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
            $this->modifiedColumns[] = AppCacheViewPeer::DEL_LAST_INDEX;
        }

    } // setDelLastIndex()

    /**
     * Set the value of [app_number] column.
     * 
     * @param      int $v new value
     * @return     void
     */
    public function setAppNumber($v)
    {

        // Since the native PHP type for this column is integer,
        // we will cast the input value to an int (if it is not).
        if ($v !== null && !is_int($v) && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->app_number !== $v || $v === 0) {
            $this->app_number = $v;
            $this->modifiedColumns[] = AppCacheViewPeer::APP_NUMBER;
        }

    } // setAppNumber()

    /**
     * Set the value of [app_status] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setAppStatus($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->app_status !== $v || $v === '') {
            $this->app_status = $v;
            $this->modifiedColumns[] = AppCacheViewPeer::APP_STATUS;
        }

    } // setAppStatus()

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
            $this->modifiedColumns[] = AppCacheViewPeer::USR_UID;
        }

    } // setUsrUid()

    /**
     * Set the value of [previous_usr_uid] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setPreviousUsrUid($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->previous_usr_uid !== $v || $v === '') {
            $this->previous_usr_uid = $v;
            $this->modifiedColumns[] = AppCacheViewPeer::PREVIOUS_USR_UID;
        }

    } // setPreviousUsrUid()

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
            $this->modifiedColumns[] = AppCacheViewPeer::TAS_UID;
        }

    } // setTasUid()

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
            $this->modifiedColumns[] = AppCacheViewPeer::PRO_UID;
        }

    } // setProUid()

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
            $this->modifiedColumns[] = AppCacheViewPeer::DEL_DELEGATE_DATE;
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
            $this->modifiedColumns[] = AppCacheViewPeer::DEL_INIT_DATE;
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
            $this->modifiedColumns[] = AppCacheViewPeer::DEL_TASK_DUE_DATE;
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
            $this->modifiedColumns[] = AppCacheViewPeer::DEL_FINISH_DATE;
        }

    } // setDelFinishDate()

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
            $this->modifiedColumns[] = AppCacheViewPeer::DEL_THREAD_STATUS;
        }

    } // setDelThreadStatus()

    /**
     * Set the value of [app_thread_status] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setAppThreadStatus($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->app_thread_status !== $v || $v === 'OPEN') {
            $this->app_thread_status = $v;
            $this->modifiedColumns[] = AppCacheViewPeer::APP_THREAD_STATUS;
        }

    } // setAppThreadStatus()

    /**
     * Set the value of [app_title] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setAppTitle($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->app_title !== $v || $v === '') {
            $this->app_title = $v;
            $this->modifiedColumns[] = AppCacheViewPeer::APP_TITLE;
        }

    } // setAppTitle()

    /**
     * Set the value of [app_pro_title] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setAppProTitle($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->app_pro_title !== $v || $v === '') {
            $this->app_pro_title = $v;
            $this->modifiedColumns[] = AppCacheViewPeer::APP_PRO_TITLE;
        }

    } // setAppProTitle()

    /**
     * Set the value of [app_tas_title] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setAppTasTitle($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->app_tas_title !== $v || $v === '') {
            $this->app_tas_title = $v;
            $this->modifiedColumns[] = AppCacheViewPeer::APP_TAS_TITLE;
        }

    } // setAppTasTitle()

    /**
     * Set the value of [app_current_user] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setAppCurrentUser($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->app_current_user !== $v || $v === '') {
            $this->app_current_user = $v;
            $this->modifiedColumns[] = AppCacheViewPeer::APP_CURRENT_USER;
        }

    } // setAppCurrentUser()

    /**
     * Set the value of [app_del_previous_user] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setAppDelPreviousUser($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->app_del_previous_user !== $v || $v === '') {
            $this->app_del_previous_user = $v;
            $this->modifiedColumns[] = AppCacheViewPeer::APP_DEL_PREVIOUS_USER;
        }

    } // setAppDelPreviousUser()

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
            $this->modifiedColumns[] = AppCacheViewPeer::DEL_PRIORITY;
        }

    } // setDelPriority()

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
            $this->modifiedColumns[] = AppCacheViewPeer::DEL_DURATION;
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
            $this->modifiedColumns[] = AppCacheViewPeer::DEL_QUEUE_DURATION;
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
            $this->modifiedColumns[] = AppCacheViewPeer::DEL_DELAY_DURATION;
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
            $this->modifiedColumns[] = AppCacheViewPeer::DEL_STARTED;
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
            $this->modifiedColumns[] = AppCacheViewPeer::DEL_FINISHED;
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
            $this->modifiedColumns[] = AppCacheViewPeer::DEL_DELAYED;
        }

    } // setDelDelayed()

    /**
     * Set the value of [app_create_date] column.
     * 
     * @param      int $v new value
     * @return     void
     */
    public function setAppCreateDate($v)
    {

        if ($v !== null && !is_int($v)) {
            $ts = strtotime($v);
            if ($ts === -1 || $ts === false) {
                throw new PropelException("Unable to parse date/time value for [app_create_date] from input: " .
                    var_export($v, true));
            }
        } else {
            $ts = $v;
        }
        if ($this->app_create_date !== $ts) {
            $this->app_create_date = $ts;
            $this->modifiedColumns[] = AppCacheViewPeer::APP_CREATE_DATE;
        }

    } // setAppCreateDate()

    /**
     * Set the value of [app_finish_date] column.
     * 
     * @param      int $v new value
     * @return     void
     */
    public function setAppFinishDate($v)
    {

        if ($v !== null && !is_int($v)) {
            $ts = strtotime($v);
            if ($ts === -1 || $ts === false) {
                throw new PropelException("Unable to parse date/time value for [app_finish_date] from input: " .
                    var_export($v, true));
            }
        } else {
            $ts = $v;
        }
        if ($this->app_finish_date !== $ts) {
            $this->app_finish_date = $ts;
            $this->modifiedColumns[] = AppCacheViewPeer::APP_FINISH_DATE;
        }

    } // setAppFinishDate()

    /**
     * Set the value of [app_update_date] column.
     * 
     * @param      int $v new value
     * @return     void
     */
    public function setAppUpdateDate($v)
    {

        if ($v !== null && !is_int($v)) {
            $ts = strtotime($v);
            if ($ts === -1 || $ts === false) {
                throw new PropelException("Unable to parse date/time value for [app_update_date] from input: " .
                    var_export($v, true));
            }
        } else {
            $ts = $v;
        }
        if ($this->app_update_date !== $ts) {
            $this->app_update_date = $ts;
            $this->modifiedColumns[] = AppCacheViewPeer::APP_UPDATE_DATE;
        }

    } // setAppUpdateDate()

    /**
     * Set the value of [app_overdue_percentage] column.
     * 
     * @param      double $v new value
     * @return     void
     */
    public function setAppOverduePercentage($v)
    {

        if ($this->app_overdue_percentage !== $v) {
            $this->app_overdue_percentage = $v;
            $this->modifiedColumns[] = AppCacheViewPeer::APP_OVERDUE_PERCENTAGE;
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

            $this->del_last_index = $rs->getInt($startcol + 2);

            $this->app_number = $rs->getInt($startcol + 3);

            $this->app_status = $rs->getString($startcol + 4);

            $this->usr_uid = $rs->getString($startcol + 5);

            $this->previous_usr_uid = $rs->getString($startcol + 6);

            $this->tas_uid = $rs->getString($startcol + 7);

            $this->pro_uid = $rs->getString($startcol + 8);

            $this->del_delegate_date = $rs->getTimestamp($startcol + 9, null);

            $this->del_init_date = $rs->getTimestamp($startcol + 10, null);

            $this->del_task_due_date = $rs->getTimestamp($startcol + 11, null);

            $this->del_finish_date = $rs->getTimestamp($startcol + 12, null);

            $this->del_thread_status = $rs->getString($startcol + 13);

            $this->app_thread_status = $rs->getString($startcol + 14);

            $this->app_title = $rs->getString($startcol + 15);

            $this->app_pro_title = $rs->getString($startcol + 16);

            $this->app_tas_title = $rs->getString($startcol + 17);

            $this->app_current_user = $rs->getString($startcol + 18);

            $this->app_del_previous_user = $rs->getString($startcol + 19);

            $this->del_priority = $rs->getString($startcol + 20);

            $this->del_duration = $rs->getFloat($startcol + 21);

            $this->del_queue_duration = $rs->getFloat($startcol + 22);

            $this->del_delay_duration = $rs->getFloat($startcol + 23);

            $this->del_started = $rs->getInt($startcol + 24);

            $this->del_finished = $rs->getInt($startcol + 25);

            $this->del_delayed = $rs->getInt($startcol + 26);

            $this->app_create_date = $rs->getTimestamp($startcol + 27, null);

            $this->app_finish_date = $rs->getTimestamp($startcol + 28, null);

            $this->app_update_date = $rs->getTimestamp($startcol + 29, null);

            $this->app_overdue_percentage = $rs->getFloat($startcol + 30);

            $this->resetModified();

            $this->setNew(false);

            // FIXME - using NUM_COLUMNS may be clearer.
            return $startcol + 31; // 31 = AppCacheViewPeer::NUM_COLUMNS - AppCacheViewPeer::NUM_LAZY_LOAD_COLUMNS).

        } catch (Exception $e) {
            throw new PropelException("Error populating AppCacheView object", $e);
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
            $con = Propel::getConnection(AppCacheViewPeer::DATABASE_NAME);
        }

        try {
            $con->begin();
            AppCacheViewPeer::doDelete($this, $con);
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
            $con = Propel::getConnection(AppCacheViewPeer::DATABASE_NAME);
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
                    $pk = AppCacheViewPeer::doInsert($this, $con);
                    $affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
                                         // should always be true here (even though technically
                                         // BasePeer::doInsert() can insert multiple rows).

                    $this->setNew(false);
                } else {
                    $affectedRows += AppCacheViewPeer::doUpdate($this, $con);
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


            if (($retval = AppCacheViewPeer::doValidate($this, $columns)) !== true) {
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
        $pos = AppCacheViewPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                return $this->getDelLastIndex();
                break;
            case 3:
                return $this->getAppNumber();
                break;
            case 4:
                return $this->getAppStatus();
                break;
            case 5:
                return $this->getUsrUid();
                break;
            case 6:
                return $this->getPreviousUsrUid();
                break;
            case 7:
                return $this->getTasUid();
                break;
            case 8:
                return $this->getProUid();
                break;
            case 9:
                return $this->getDelDelegateDate();
                break;
            case 10:
                return $this->getDelInitDate();
                break;
            case 11:
                return $this->getDelTaskDueDate();
                break;
            case 12:
                return $this->getDelFinishDate();
                break;
            case 13:
                return $this->getDelThreadStatus();
                break;
            case 14:
                return $this->getAppThreadStatus();
                break;
            case 15:
                return $this->getAppTitle();
                break;
            case 16:
                return $this->getAppProTitle();
                break;
            case 17:
                return $this->getAppTasTitle();
                break;
            case 18:
                return $this->getAppCurrentUser();
                break;
            case 19:
                return $this->getAppDelPreviousUser();
                break;
            case 20:
                return $this->getDelPriority();
                break;
            case 21:
                return $this->getDelDuration();
                break;
            case 22:
                return $this->getDelQueueDuration();
                break;
            case 23:
                return $this->getDelDelayDuration();
                break;
            case 24:
                return $this->getDelStarted();
                break;
            case 25:
                return $this->getDelFinished();
                break;
            case 26:
                return $this->getDelDelayed();
                break;
            case 27:
                return $this->getAppCreateDate();
                break;
            case 28:
                return $this->getAppFinishDate();
                break;
            case 29:
                return $this->getAppUpdateDate();
                break;
            case 30:
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
        $keys = AppCacheViewPeer::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getAppUid(),
            $keys[1] => $this->getDelIndex(),
            $keys[2] => $this->getDelLastIndex(),
            $keys[3] => $this->getAppNumber(),
            $keys[4] => $this->getAppStatus(),
            $keys[5] => $this->getUsrUid(),
            $keys[6] => $this->getPreviousUsrUid(),
            $keys[7] => $this->getTasUid(),
            $keys[8] => $this->getProUid(),
            $keys[9] => $this->getDelDelegateDate(),
            $keys[10] => $this->getDelInitDate(),
            $keys[11] => $this->getDelTaskDueDate(),
            $keys[12] => $this->getDelFinishDate(),
            $keys[13] => $this->getDelThreadStatus(),
            $keys[14] => $this->getAppThreadStatus(),
            $keys[15] => $this->getAppTitle(),
            $keys[16] => $this->getAppProTitle(),
            $keys[17] => $this->getAppTasTitle(),
            $keys[18] => $this->getAppCurrentUser(),
            $keys[19] => $this->getAppDelPreviousUser(),
            $keys[20] => $this->getDelPriority(),
            $keys[21] => $this->getDelDuration(),
            $keys[22] => $this->getDelQueueDuration(),
            $keys[23] => $this->getDelDelayDuration(),
            $keys[24] => $this->getDelStarted(),
            $keys[25] => $this->getDelFinished(),
            $keys[26] => $this->getDelDelayed(),
            $keys[27] => $this->getAppCreateDate(),
            $keys[28] => $this->getAppFinishDate(),
            $keys[29] => $this->getAppUpdateDate(),
            $keys[30] => $this->getAppOverduePercentage(),
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
        $pos = AppCacheViewPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                $this->setDelLastIndex($value);
                break;
            case 3:
                $this->setAppNumber($value);
                break;
            case 4:
                $this->setAppStatus($value);
                break;
            case 5:
                $this->setUsrUid($value);
                break;
            case 6:
                $this->setPreviousUsrUid($value);
                break;
            case 7:
                $this->setTasUid($value);
                break;
            case 8:
                $this->setProUid($value);
                break;
            case 9:
                $this->setDelDelegateDate($value);
                break;
            case 10:
                $this->setDelInitDate($value);
                break;
            case 11:
                $this->setDelTaskDueDate($value);
                break;
            case 12:
                $this->setDelFinishDate($value);
                break;
            case 13:
                $this->setDelThreadStatus($value);
                break;
            case 14:
                $this->setAppThreadStatus($value);
                break;
            case 15:
                $this->setAppTitle($value);
                break;
            case 16:
                $this->setAppProTitle($value);
                break;
            case 17:
                $this->setAppTasTitle($value);
                break;
            case 18:
                $this->setAppCurrentUser($value);
                break;
            case 19:
                $this->setAppDelPreviousUser($value);
                break;
            case 20:
                $this->setDelPriority($value);
                break;
            case 21:
                $this->setDelDuration($value);
                break;
            case 22:
                $this->setDelQueueDuration($value);
                break;
            case 23:
                $this->setDelDelayDuration($value);
                break;
            case 24:
                $this->setDelStarted($value);
                break;
            case 25:
                $this->setDelFinished($value);
                break;
            case 26:
                $this->setDelDelayed($value);
                break;
            case 27:
                $this->setAppCreateDate($value);
                break;
            case 28:
                $this->setAppFinishDate($value);
                break;
            case 29:
                $this->setAppUpdateDate($value);
                break;
            case 30:
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
        $keys = AppCacheViewPeer::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) {
            $this->setAppUid($arr[$keys[0]]);
        }

        if (array_key_exists($keys[1], $arr)) {
            $this->setDelIndex($arr[$keys[1]]);
        }

        if (array_key_exists($keys[2], $arr)) {
            $this->setDelLastIndex($arr[$keys[2]]);
        }

        if (array_key_exists($keys[3], $arr)) {
            $this->setAppNumber($arr[$keys[3]]);
        }

        if (array_key_exists($keys[4], $arr)) {
            $this->setAppStatus($arr[$keys[4]]);
        }

        if (array_key_exists($keys[5], $arr)) {
            $this->setUsrUid($arr[$keys[5]]);
        }

        if (array_key_exists($keys[6], $arr)) {
            $this->setPreviousUsrUid($arr[$keys[6]]);
        }

        if (array_key_exists($keys[7], $arr)) {
            $this->setTasUid($arr[$keys[7]]);
        }

        if (array_key_exists($keys[8], $arr)) {
            $this->setProUid($arr[$keys[8]]);
        }

        if (array_key_exists($keys[9], $arr)) {
            $this->setDelDelegateDate($arr[$keys[9]]);
        }

        if (array_key_exists($keys[10], $arr)) {
            $this->setDelInitDate($arr[$keys[10]]);
        }

        if (array_key_exists($keys[11], $arr)) {
            $this->setDelTaskDueDate($arr[$keys[11]]);
        }

        if (array_key_exists($keys[12], $arr)) {
            $this->setDelFinishDate($arr[$keys[12]]);
        }

        if (array_key_exists($keys[13], $arr)) {
            $this->setDelThreadStatus($arr[$keys[13]]);
        }

        if (array_key_exists($keys[14], $arr)) {
            $this->setAppThreadStatus($arr[$keys[14]]);
        }

        if (array_key_exists($keys[15], $arr)) {
            $this->setAppTitle($arr[$keys[15]]);
        }

        if (array_key_exists($keys[16], $arr)) {
            $this->setAppProTitle($arr[$keys[16]]);
        }

        if (array_key_exists($keys[17], $arr)) {
            $this->setAppTasTitle($arr[$keys[17]]);
        }

        if (array_key_exists($keys[18], $arr)) {
            $this->setAppCurrentUser($arr[$keys[18]]);
        }

        if (array_key_exists($keys[19], $arr)) {
            $this->setAppDelPreviousUser($arr[$keys[19]]);
        }

        if (array_key_exists($keys[20], $arr)) {
            $this->setDelPriority($arr[$keys[20]]);
        }

        if (array_key_exists($keys[21], $arr)) {
            $this->setDelDuration($arr[$keys[21]]);
        }

        if (array_key_exists($keys[22], $arr)) {
            $this->setDelQueueDuration($arr[$keys[22]]);
        }

        if (array_key_exists($keys[23], $arr)) {
            $this->setDelDelayDuration($arr[$keys[23]]);
        }

        if (array_key_exists($keys[24], $arr)) {
            $this->setDelStarted($arr[$keys[24]]);
        }

        if (array_key_exists($keys[25], $arr)) {
            $this->setDelFinished($arr[$keys[25]]);
        }

        if (array_key_exists($keys[26], $arr)) {
            $this->setDelDelayed($arr[$keys[26]]);
        }

        if (array_key_exists($keys[27], $arr)) {
            $this->setAppCreateDate($arr[$keys[27]]);
        }

        if (array_key_exists($keys[28], $arr)) {
            $this->setAppFinishDate($arr[$keys[28]]);
        }

        if (array_key_exists($keys[29], $arr)) {
            $this->setAppUpdateDate($arr[$keys[29]]);
        }

        if (array_key_exists($keys[30], $arr)) {
            $this->setAppOverduePercentage($arr[$keys[30]]);
        }

    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return     Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(AppCacheViewPeer::DATABASE_NAME);

        if ($this->isColumnModified(AppCacheViewPeer::APP_UID)) {
            $criteria->add(AppCacheViewPeer::APP_UID, $this->app_uid);
        }

        if ($this->isColumnModified(AppCacheViewPeer::DEL_INDEX)) {
            $criteria->add(AppCacheViewPeer::DEL_INDEX, $this->del_index);
        }

        if ($this->isColumnModified(AppCacheViewPeer::DEL_LAST_INDEX)) {
            $criteria->add(AppCacheViewPeer::DEL_LAST_INDEX, $this->del_last_index);
        }

        if ($this->isColumnModified(AppCacheViewPeer::APP_NUMBER)) {
            $criteria->add(AppCacheViewPeer::APP_NUMBER, $this->app_number);
        }

        if ($this->isColumnModified(AppCacheViewPeer::APP_STATUS)) {
            $criteria->add(AppCacheViewPeer::APP_STATUS, $this->app_status);
        }

        if ($this->isColumnModified(AppCacheViewPeer::USR_UID)) {
            $criteria->add(AppCacheViewPeer::USR_UID, $this->usr_uid);
        }

        if ($this->isColumnModified(AppCacheViewPeer::PREVIOUS_USR_UID)) {
            $criteria->add(AppCacheViewPeer::PREVIOUS_USR_UID, $this->previous_usr_uid);
        }

        if ($this->isColumnModified(AppCacheViewPeer::TAS_UID)) {
            $criteria->add(AppCacheViewPeer::TAS_UID, $this->tas_uid);
        }

        if ($this->isColumnModified(AppCacheViewPeer::PRO_UID)) {
            $criteria->add(AppCacheViewPeer::PRO_UID, $this->pro_uid);
        }

        if ($this->isColumnModified(AppCacheViewPeer::DEL_DELEGATE_DATE)) {
            $criteria->add(AppCacheViewPeer::DEL_DELEGATE_DATE, $this->del_delegate_date);
        }

        if ($this->isColumnModified(AppCacheViewPeer::DEL_INIT_DATE)) {
            $criteria->add(AppCacheViewPeer::DEL_INIT_DATE, $this->del_init_date);
        }

        if ($this->isColumnModified(AppCacheViewPeer::DEL_TASK_DUE_DATE)) {
            $criteria->add(AppCacheViewPeer::DEL_TASK_DUE_DATE, $this->del_task_due_date);
        }

        if ($this->isColumnModified(AppCacheViewPeer::DEL_FINISH_DATE)) {
            $criteria->add(AppCacheViewPeer::DEL_FINISH_DATE, $this->del_finish_date);
        }

        if ($this->isColumnModified(AppCacheViewPeer::DEL_THREAD_STATUS)) {
            $criteria->add(AppCacheViewPeer::DEL_THREAD_STATUS, $this->del_thread_status);
        }

        if ($this->isColumnModified(AppCacheViewPeer::APP_THREAD_STATUS)) {
            $criteria->add(AppCacheViewPeer::APP_THREAD_STATUS, $this->app_thread_status);
        }

        if ($this->isColumnModified(AppCacheViewPeer::APP_TITLE)) {
            $criteria->add(AppCacheViewPeer::APP_TITLE, $this->app_title);
        }

        if ($this->isColumnModified(AppCacheViewPeer::APP_PRO_TITLE)) {
            $criteria->add(AppCacheViewPeer::APP_PRO_TITLE, $this->app_pro_title);
        }

        if ($this->isColumnModified(AppCacheViewPeer::APP_TAS_TITLE)) {
            $criteria->add(AppCacheViewPeer::APP_TAS_TITLE, $this->app_tas_title);
        }

        if ($this->isColumnModified(AppCacheViewPeer::APP_CURRENT_USER)) {
            $criteria->add(AppCacheViewPeer::APP_CURRENT_USER, $this->app_current_user);
        }

        if ($this->isColumnModified(AppCacheViewPeer::APP_DEL_PREVIOUS_USER)) {
            $criteria->add(AppCacheViewPeer::APP_DEL_PREVIOUS_USER, $this->app_del_previous_user);
        }

        if ($this->isColumnModified(AppCacheViewPeer::DEL_PRIORITY)) {
            $criteria->add(AppCacheViewPeer::DEL_PRIORITY, $this->del_priority);
        }

        if ($this->isColumnModified(AppCacheViewPeer::DEL_DURATION)) {
            $criteria->add(AppCacheViewPeer::DEL_DURATION, $this->del_duration);
        }

        if ($this->isColumnModified(AppCacheViewPeer::DEL_QUEUE_DURATION)) {
            $criteria->add(AppCacheViewPeer::DEL_QUEUE_DURATION, $this->del_queue_duration);
        }

        if ($this->isColumnModified(AppCacheViewPeer::DEL_DELAY_DURATION)) {
            $criteria->add(AppCacheViewPeer::DEL_DELAY_DURATION, $this->del_delay_duration);
        }

        if ($this->isColumnModified(AppCacheViewPeer::DEL_STARTED)) {
            $criteria->add(AppCacheViewPeer::DEL_STARTED, $this->del_started);
        }

        if ($this->isColumnModified(AppCacheViewPeer::DEL_FINISHED)) {
            $criteria->add(AppCacheViewPeer::DEL_FINISHED, $this->del_finished);
        }

        if ($this->isColumnModified(AppCacheViewPeer::DEL_DELAYED)) {
            $criteria->add(AppCacheViewPeer::DEL_DELAYED, $this->del_delayed);
        }

        if ($this->isColumnModified(AppCacheViewPeer::APP_CREATE_DATE)) {
            $criteria->add(AppCacheViewPeer::APP_CREATE_DATE, $this->app_create_date);
        }

        if ($this->isColumnModified(AppCacheViewPeer::APP_FINISH_DATE)) {
            $criteria->add(AppCacheViewPeer::APP_FINISH_DATE, $this->app_finish_date);
        }

        if ($this->isColumnModified(AppCacheViewPeer::APP_UPDATE_DATE)) {
            $criteria->add(AppCacheViewPeer::APP_UPDATE_DATE, $this->app_update_date);
        }

        if ($this->isColumnModified(AppCacheViewPeer::APP_OVERDUE_PERCENTAGE)) {
            $criteria->add(AppCacheViewPeer::APP_OVERDUE_PERCENTAGE, $this->app_overdue_percentage);
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
        $criteria = new Criteria(AppCacheViewPeer::DATABASE_NAME);

        $criteria->add(AppCacheViewPeer::APP_UID, $this->app_uid);
        $criteria->add(AppCacheViewPeer::DEL_INDEX, $this->del_index);

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
     * @param      object $copyObj An object of AppCacheView (or compatible) type.
     * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @throws     PropelException
     */
    public function copyInto($copyObj, $deepCopy = false)
    {

        $copyObj->setDelLastIndex($this->del_last_index);

        $copyObj->setAppNumber($this->app_number);

        $copyObj->setAppStatus($this->app_status);

        $copyObj->setUsrUid($this->usr_uid);

        $copyObj->setPreviousUsrUid($this->previous_usr_uid);

        $copyObj->setTasUid($this->tas_uid);

        $copyObj->setProUid($this->pro_uid);

        $copyObj->setDelDelegateDate($this->del_delegate_date);

        $copyObj->setDelInitDate($this->del_init_date);

        $copyObj->setDelTaskDueDate($this->del_task_due_date);

        $copyObj->setDelFinishDate($this->del_finish_date);

        $copyObj->setDelThreadStatus($this->del_thread_status);

        $copyObj->setAppThreadStatus($this->app_thread_status);

        $copyObj->setAppTitle($this->app_title);

        $copyObj->setAppProTitle($this->app_pro_title);

        $copyObj->setAppTasTitle($this->app_tas_title);

        $copyObj->setAppCurrentUser($this->app_current_user);

        $copyObj->setAppDelPreviousUser($this->app_del_previous_user);

        $copyObj->setDelPriority($this->del_priority);

        $copyObj->setDelDuration($this->del_duration);

        $copyObj->setDelQueueDuration($this->del_queue_duration);

        $copyObj->setDelDelayDuration($this->del_delay_duration);

        $copyObj->setDelStarted($this->del_started);

        $copyObj->setDelFinished($this->del_finished);

        $copyObj->setDelDelayed($this->del_delayed);

        $copyObj->setAppCreateDate($this->app_create_date);

        $copyObj->setAppFinishDate($this->app_finish_date);

        $copyObj->setAppUpdateDate($this->app_update_date);

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
     * @return     AppCacheView Clone of current object.
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
     * @return     AppCacheViewPeer
     */
    public function getPeer()
    {
        if (self::$peer === null) {
            self::$peer = new AppCacheViewPeer();
        }
        return self::$peer;
    }
}

