<?php

require_once 'propel/om/BaseObject.php';

require_once 'propel/om/Persistent.php';


include_once 'propel/util/Criteria.php';

include_once 'classes/model/AppDelayPeer.php';

/**
 * Base class that represents a row from the 'APP_DELAY' table.
 *
 * 
 *
 * @package    workflow.classes.model.om
 */
abstract class BaseAppDelay extends BaseObject implements Persistent
{

    /**
     * The Peer class.
     * Instance provides a convenient way of calling static methods on a class
     * that calling code may not be able to identify.
     * @var        AppDelayPeer
    */
    protected static $peer;

    /**
     * The value for the app_delay_uid field.
     * @var        string
     */
    protected $app_delay_uid = '';

    /**
     * The value for the pro_uid field.
     * @var        string
     */
    protected $pro_uid = '0';

    /**
     * The value for the app_uid field.
     * @var        string
     */
    protected $app_uid = '0';

    /**
     * The value for the app_number field.
     * @var        int
     */
    protected $app_number = 0;

    /**
     * The value for the app_thread_index field.
     * @var        int
     */
    protected $app_thread_index = 0;

    /**
     * The value for the app_del_index field.
     * @var        int
     */
    protected $app_del_index = 0;

    /**
     * The value for the app_type field.
     * @var        string
     */
    protected $app_type = '0';

    /**
     * The value for the app_status field.
     * @var        string
     */
    protected $app_status = '0';

    /**
     * The value for the app_next_task field.
     * @var        string
     */
    protected $app_next_task = '0';

    /**
     * The value for the app_delegation_user field.
     * @var        string
     */
    protected $app_delegation_user = '0';

    /**
     * The value for the app_enable_action_user field.
     * @var        string
     */
    protected $app_enable_action_user = '0';

    /**
     * The value for the app_enable_action_date field.
     * @var        int
     */
    protected $app_enable_action_date;

    /**
     * The value for the app_disable_action_user field.
     * @var        string
     */
    protected $app_disable_action_user = '0';

    /**
     * The value for the app_disable_action_date field.
     * @var        int
     */
    protected $app_disable_action_date;

    /**
     * The value for the app_automatic_disabled_date field.
     * @var        int
     */
    protected $app_automatic_disabled_date;

    /**
     * The value for the app_delegation_user_id field.
     * @var        int
     */
    protected $app_delegation_user_id = 0;

    /**
     * The value for the pro_id field.
     * @var        int
     */
    protected $pro_id = 0;

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
     * Get the [app_delay_uid] column value.
     * 
     * @return     string
     */
    public function getAppDelayUid()
    {

        return $this->app_delay_uid;
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
     * Get the [app_uid] column value.
     * 
     * @return     string
     */
    public function getAppUid()
    {

        return $this->app_uid;
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
     * Get the [app_thread_index] column value.
     * 
     * @return     int
     */
    public function getAppThreadIndex()
    {

        return $this->app_thread_index;
    }

    /**
     * Get the [app_del_index] column value.
     * 
     * @return     int
     */
    public function getAppDelIndex()
    {

        return $this->app_del_index;
    }

    /**
     * Get the [app_type] column value.
     * 
     * @return     string
     */
    public function getAppType()
    {

        return $this->app_type;
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
     * Get the [app_next_task] column value.
     * 
     * @return     string
     */
    public function getAppNextTask()
    {

        return $this->app_next_task;
    }

    /**
     * Get the [app_delegation_user] column value.
     * 
     * @return     string
     */
    public function getAppDelegationUser()
    {

        return $this->app_delegation_user;
    }

    /**
     * Get the [app_enable_action_user] column value.
     * 
     * @return     string
     */
    public function getAppEnableActionUser()
    {

        return $this->app_enable_action_user;
    }

    /**
     * Get the [optionally formatted] [app_enable_action_date] column value.
     * 
     * @param      string $format The date/time format string (either date()-style or strftime()-style).
     *                          If format is NULL, then the integer unix timestamp will be returned.
     * @return     mixed Formatted date/time value as string or integer unix timestamp (if format is NULL).
     * @throws     PropelException - if unable to convert the date/time to timestamp.
     */
    public function getAppEnableActionDate($format = 'Y-m-d H:i:s')
    {

        if ($this->app_enable_action_date === null || $this->app_enable_action_date === '') {
            return null;
        } elseif (!is_int($this->app_enable_action_date)) {
            // a non-timestamp value was set externally, so we convert it
            $ts = strtotime($this->app_enable_action_date);
            if ($ts === -1 || $ts === false) {
                throw new PropelException("Unable to parse value of [app_enable_action_date] as date/time value: " .
                    var_export($this->app_enable_action_date, true));
            }
        } else {
            $ts = $this->app_enable_action_date;
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
     * Get the [app_disable_action_user] column value.
     * 
     * @return     string
     */
    public function getAppDisableActionUser()
    {

        return $this->app_disable_action_user;
    }

    /**
     * Get the [optionally formatted] [app_disable_action_date] column value.
     * 
     * @param      string $format The date/time format string (either date()-style or strftime()-style).
     *                          If format is NULL, then the integer unix timestamp will be returned.
     * @return     mixed Formatted date/time value as string or integer unix timestamp (if format is NULL).
     * @throws     PropelException - if unable to convert the date/time to timestamp.
     */
    public function getAppDisableActionDate($format = 'Y-m-d H:i:s')
    {

        if ($this->app_disable_action_date === null || $this->app_disable_action_date === '') {
            return null;
        } elseif (!is_int($this->app_disable_action_date)) {
            // a non-timestamp value was set externally, so we convert it
            $ts = strtotime($this->app_disable_action_date);
            if ($ts === -1 || $ts === false) {
                throw new PropelException("Unable to parse value of [app_disable_action_date] as date/time value: " .
                    var_export($this->app_disable_action_date, true));
            }
        } else {
            $ts = $this->app_disable_action_date;
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
     * Get the [optionally formatted] [app_automatic_disabled_date] column value.
     * 
     * @param      string $format The date/time format string (either date()-style or strftime()-style).
     *                          If format is NULL, then the integer unix timestamp will be returned.
     * @return     mixed Formatted date/time value as string or integer unix timestamp (if format is NULL).
     * @throws     PropelException - if unable to convert the date/time to timestamp.
     */
    public function getAppAutomaticDisabledDate($format = 'Y-m-d H:i:s')
    {

        if ($this->app_automatic_disabled_date === null || $this->app_automatic_disabled_date === '') {
            return null;
        } elseif (!is_int($this->app_automatic_disabled_date)) {
            // a non-timestamp value was set externally, so we convert it
            $ts = strtotime($this->app_automatic_disabled_date);
            if ($ts === -1 || $ts === false) {
                throw new PropelException("Unable to parse value of [app_automatic_disabled_date] as date/time value: " .
                    var_export($this->app_automatic_disabled_date, true));
            }
        } else {
            $ts = $this->app_automatic_disabled_date;
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
     * Get the [app_delegation_user_id] column value.
     * 
     * @return     int
     */
    public function getAppDelegationUserId()
    {

        return $this->app_delegation_user_id;
    }

    /**
     * Get the [pro_id] column value.
     * 
     * @return     int
     */
    public function getProId()
    {

        return $this->pro_id;
    }

    /**
     * Set the value of [app_delay_uid] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setAppDelayUid($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->app_delay_uid !== $v || $v === '') {
            $this->app_delay_uid = $v;
            $this->modifiedColumns[] = AppDelayPeer::APP_DELAY_UID;
        }

    } // setAppDelayUid()

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

        if ($this->pro_uid !== $v || $v === '0') {
            $this->pro_uid = $v;
            $this->modifiedColumns[] = AppDelayPeer::PRO_UID;
        }

    } // setProUid()

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

        if ($this->app_uid !== $v || $v === '0') {
            $this->app_uid = $v;
            $this->modifiedColumns[] = AppDelayPeer::APP_UID;
        }

    } // setAppUid()

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
            $this->modifiedColumns[] = AppDelayPeer::APP_NUMBER;
        }

    } // setAppNumber()

    /**
     * Set the value of [app_thread_index] column.
     * 
     * @param      int $v new value
     * @return     void
     */
    public function setAppThreadIndex($v)
    {

        // Since the native PHP type for this column is integer,
        // we will cast the input value to an int (if it is not).
        if ($v !== null && !is_int($v) && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->app_thread_index !== $v || $v === 0) {
            $this->app_thread_index = $v;
            $this->modifiedColumns[] = AppDelayPeer::APP_THREAD_INDEX;
        }

    } // setAppThreadIndex()

    /**
     * Set the value of [app_del_index] column.
     * 
     * @param      int $v new value
     * @return     void
     */
    public function setAppDelIndex($v)
    {

        // Since the native PHP type for this column is integer,
        // we will cast the input value to an int (if it is not).
        if ($v !== null && !is_int($v) && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->app_del_index !== $v || $v === 0) {
            $this->app_del_index = $v;
            $this->modifiedColumns[] = AppDelayPeer::APP_DEL_INDEX;
        }

    } // setAppDelIndex()

    /**
     * Set the value of [app_type] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setAppType($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->app_type !== $v || $v === '0') {
            $this->app_type = $v;
            $this->modifiedColumns[] = AppDelayPeer::APP_TYPE;
        }

    } // setAppType()

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

        if ($this->app_status !== $v || $v === '0') {
            $this->app_status = $v;
            $this->modifiedColumns[] = AppDelayPeer::APP_STATUS;
        }

    } // setAppStatus()

    /**
     * Set the value of [app_next_task] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setAppNextTask($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->app_next_task !== $v || $v === '0') {
            $this->app_next_task = $v;
            $this->modifiedColumns[] = AppDelayPeer::APP_NEXT_TASK;
        }

    } // setAppNextTask()

    /**
     * Set the value of [app_delegation_user] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setAppDelegationUser($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->app_delegation_user !== $v || $v === '0') {
            $this->app_delegation_user = $v;
            $this->modifiedColumns[] = AppDelayPeer::APP_DELEGATION_USER;
        }

    } // setAppDelegationUser()

    /**
     * Set the value of [app_enable_action_user] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setAppEnableActionUser($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->app_enable_action_user !== $v || $v === '0') {
            $this->app_enable_action_user = $v;
            $this->modifiedColumns[] = AppDelayPeer::APP_ENABLE_ACTION_USER;
        }

    } // setAppEnableActionUser()

    /**
     * Set the value of [app_enable_action_date] column.
     * 
     * @param      int $v new value
     * @return     void
     */
    public function setAppEnableActionDate($v)
    {

        if ($v !== null && !is_int($v)) {
            $ts = strtotime($v);
            //Date/time accepts null values
            if ($v == '') {
                $ts = null;
            }
            if ($ts === -1 || $ts === false) {
                throw new PropelException("Unable to parse date/time value for [app_enable_action_date] from input: " .
                    var_export($v, true));
            }
        } else {
            $ts = $v;
        }
        if ($this->app_enable_action_date !== $ts) {
            $this->app_enable_action_date = $ts;
            $this->modifiedColumns[] = AppDelayPeer::APP_ENABLE_ACTION_DATE;
        }

    } // setAppEnableActionDate()

    /**
     * Set the value of [app_disable_action_user] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setAppDisableActionUser($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->app_disable_action_user !== $v || $v === '0') {
            $this->app_disable_action_user = $v;
            $this->modifiedColumns[] = AppDelayPeer::APP_DISABLE_ACTION_USER;
        }

    } // setAppDisableActionUser()

    /**
     * Set the value of [app_disable_action_date] column.
     * 
     * @param      int $v new value
     * @return     void
     */
    public function setAppDisableActionDate($v)
    {

        if ($v !== null && !is_int($v)) {
            $ts = strtotime($v);
            //Date/time accepts null values
            if ($v == '') {
                $ts = null;
            }
            if ($ts === -1 || $ts === false) {
                throw new PropelException("Unable to parse date/time value for [app_disable_action_date] from input: " .
                    var_export($v, true));
            }
        } else {
            $ts = $v;
        }
        if ($this->app_disable_action_date !== $ts) {
            $this->app_disable_action_date = $ts;
            $this->modifiedColumns[] = AppDelayPeer::APP_DISABLE_ACTION_DATE;
        }

    } // setAppDisableActionDate()

    /**
     * Set the value of [app_automatic_disabled_date] column.
     * 
     * @param      int $v new value
     * @return     void
     */
    public function setAppAutomaticDisabledDate($v)
    {

        if ($v !== null && !is_int($v)) {
            $ts = strtotime($v);
            //Date/time accepts null values
            if ($v == '') {
                $ts = null;
            }
            if ($ts === -1 || $ts === false) {
                throw new PropelException("Unable to parse date/time value for [app_automatic_disabled_date] from input: " .
                    var_export($v, true));
            }
        } else {
            $ts = $v;
        }
        if ($this->app_automatic_disabled_date !== $ts) {
            $this->app_automatic_disabled_date = $ts;
            $this->modifiedColumns[] = AppDelayPeer::APP_AUTOMATIC_DISABLED_DATE;
        }

    } // setAppAutomaticDisabledDate()

    /**
     * Set the value of [app_delegation_user_id] column.
     * 
     * @param      int $v new value
     * @return     void
     */
    public function setAppDelegationUserId($v)
    {

        // Since the native PHP type for this column is integer,
        // we will cast the input value to an int (if it is not).
        if ($v !== null && !is_int($v) && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->app_delegation_user_id !== $v || $v === 0) {
            $this->app_delegation_user_id = $v;
            $this->modifiedColumns[] = AppDelayPeer::APP_DELEGATION_USER_ID;
        }

    } // setAppDelegationUserId()

    /**
     * Set the value of [pro_id] column.
     * 
     * @param      int $v new value
     * @return     void
     */
    public function setProId($v)
    {

        // Since the native PHP type for this column is integer,
        // we will cast the input value to an int (if it is not).
        if ($v !== null && !is_int($v) && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->pro_id !== $v || $v === 0) {
            $this->pro_id = $v;
            $this->modifiedColumns[] = AppDelayPeer::PRO_ID;
        }

    } // setProId()

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

            $this->app_delay_uid = $rs->getString($startcol + 0);

            $this->pro_uid = $rs->getString($startcol + 1);

            $this->app_uid = $rs->getString($startcol + 2);

            $this->app_number = $rs->getInt($startcol + 3);

            $this->app_thread_index = $rs->getInt($startcol + 4);

            $this->app_del_index = $rs->getInt($startcol + 5);

            $this->app_type = $rs->getString($startcol + 6);

            $this->app_status = $rs->getString($startcol + 7);

            $this->app_next_task = $rs->getString($startcol + 8);

            $this->app_delegation_user = $rs->getString($startcol + 9);

            $this->app_enable_action_user = $rs->getString($startcol + 10);

            $this->app_enable_action_date = $rs->getTimestamp($startcol + 11, null);

            $this->app_disable_action_user = $rs->getString($startcol + 12);

            $this->app_disable_action_date = $rs->getTimestamp($startcol + 13, null);

            $this->app_automatic_disabled_date = $rs->getTimestamp($startcol + 14, null);

            $this->app_delegation_user_id = $rs->getInt($startcol + 15);

            $this->pro_id = $rs->getInt($startcol + 16);

            $this->resetModified();

            $this->setNew(false);

            // FIXME - using NUM_COLUMNS may be clearer.
            return $startcol + 17; // 17 = AppDelayPeer::NUM_COLUMNS - AppDelayPeer::NUM_LAZY_LOAD_COLUMNS).

        } catch (Exception $e) {
            throw new PropelException("Error populating AppDelay object", $e);
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
            $con = Propel::getConnection(AppDelayPeer::DATABASE_NAME);
        }

        try {
            $con->begin();
            AppDelayPeer::doDelete($this, $con);
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
            $con = Propel::getConnection(AppDelayPeer::DATABASE_NAME);
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
                    $pk = AppDelayPeer::doInsert($this, $con);
                    $affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
                                         // should always be true here (even though technically
                                         // BasePeer::doInsert() can insert multiple rows).

                    $this->setNew(false);
                } else {
                    $affectedRows += AppDelayPeer::doUpdate($this, $con);
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


            if (($retval = AppDelayPeer::doValidate($this, $columns)) !== true) {
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
        $pos = AppDelayPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                return $this->getAppDelayUid();
                break;
            case 1:
                return $this->getProUid();
                break;
            case 2:
                return $this->getAppUid();
                break;
            case 3:
                return $this->getAppNumber();
                break;
            case 4:
                return $this->getAppThreadIndex();
                break;
            case 5:
                return $this->getAppDelIndex();
                break;
            case 6:
                return $this->getAppType();
                break;
            case 7:
                return $this->getAppStatus();
                break;
            case 8:
                return $this->getAppNextTask();
                break;
            case 9:
                return $this->getAppDelegationUser();
                break;
            case 10:
                return $this->getAppEnableActionUser();
                break;
            case 11:
                return $this->getAppEnableActionDate();
                break;
            case 12:
                return $this->getAppDisableActionUser();
                break;
            case 13:
                return $this->getAppDisableActionDate();
                break;
            case 14:
                return $this->getAppAutomaticDisabledDate();
                break;
            case 15:
                return $this->getAppDelegationUserId();
                break;
            case 16:
                return $this->getProId();
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
        $keys = AppDelayPeer::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getAppDelayUid(),
            $keys[1] => $this->getProUid(),
            $keys[2] => $this->getAppUid(),
            $keys[3] => $this->getAppNumber(),
            $keys[4] => $this->getAppThreadIndex(),
            $keys[5] => $this->getAppDelIndex(),
            $keys[6] => $this->getAppType(),
            $keys[7] => $this->getAppStatus(),
            $keys[8] => $this->getAppNextTask(),
            $keys[9] => $this->getAppDelegationUser(),
            $keys[10] => $this->getAppEnableActionUser(),
            $keys[11] => $this->getAppEnableActionDate(),
            $keys[12] => $this->getAppDisableActionUser(),
            $keys[13] => $this->getAppDisableActionDate(),
            $keys[14] => $this->getAppAutomaticDisabledDate(),
            $keys[15] => $this->getAppDelegationUserId(),
            $keys[16] => $this->getProId(),
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
        $pos = AppDelayPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                $this->setAppDelayUid($value);
                break;
            case 1:
                $this->setProUid($value);
                break;
            case 2:
                $this->setAppUid($value);
                break;
            case 3:
                $this->setAppNumber($value);
                break;
            case 4:
                $this->setAppThreadIndex($value);
                break;
            case 5:
                $this->setAppDelIndex($value);
                break;
            case 6:
                $this->setAppType($value);
                break;
            case 7:
                $this->setAppStatus($value);
                break;
            case 8:
                $this->setAppNextTask($value);
                break;
            case 9:
                $this->setAppDelegationUser($value);
                break;
            case 10:
                $this->setAppEnableActionUser($value);
                break;
            case 11:
                $this->setAppEnableActionDate($value);
                break;
            case 12:
                $this->setAppDisableActionUser($value);
                break;
            case 13:
                $this->setAppDisableActionDate($value);
                break;
            case 14:
                $this->setAppAutomaticDisabledDate($value);
                break;
            case 15:
                $this->setAppDelegationUserId($value);
                break;
            case 16:
                $this->setProId($value);
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
        $keys = AppDelayPeer::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) {
            $this->setAppDelayUid($arr[$keys[0]]);
        }

        if (array_key_exists($keys[1], $arr)) {
            $this->setProUid($arr[$keys[1]]);
        }

        if (array_key_exists($keys[2], $arr)) {
            $this->setAppUid($arr[$keys[2]]);
        }

        if (array_key_exists($keys[3], $arr)) {
            $this->setAppNumber($arr[$keys[3]]);
        }

        if (array_key_exists($keys[4], $arr)) {
            $this->setAppThreadIndex($arr[$keys[4]]);
        }

        if (array_key_exists($keys[5], $arr)) {
            $this->setAppDelIndex($arr[$keys[5]]);
        }

        if (array_key_exists($keys[6], $arr)) {
            $this->setAppType($arr[$keys[6]]);
        }

        if (array_key_exists($keys[7], $arr)) {
            $this->setAppStatus($arr[$keys[7]]);
        }

        if (array_key_exists($keys[8], $arr)) {
            $this->setAppNextTask($arr[$keys[8]]);
        }

        if (array_key_exists($keys[9], $arr)) {
            $this->setAppDelegationUser($arr[$keys[9]]);
        }

        if (array_key_exists($keys[10], $arr)) {
            $this->setAppEnableActionUser($arr[$keys[10]]);
        }

        if (array_key_exists($keys[11], $arr)) {
            $this->setAppEnableActionDate($arr[$keys[11]]);
        }

        if (array_key_exists($keys[12], $arr)) {
            $this->setAppDisableActionUser($arr[$keys[12]]);
        }

        if (array_key_exists($keys[13], $arr)) {
            $this->setAppDisableActionDate($arr[$keys[13]]);
        }

        if (array_key_exists($keys[14], $arr)) {
            $this->setAppAutomaticDisabledDate($arr[$keys[14]]);
        }

        if (array_key_exists($keys[15], $arr)) {
            $this->setAppDelegationUserId($arr[$keys[15]]);
        }

        if (array_key_exists($keys[16], $arr)) {
            $this->setProId($arr[$keys[16]]);
        }

    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return     Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(AppDelayPeer::DATABASE_NAME);

        if ($this->isColumnModified(AppDelayPeer::APP_DELAY_UID)) {
            $criteria->add(AppDelayPeer::APP_DELAY_UID, $this->app_delay_uid);
        }

        if ($this->isColumnModified(AppDelayPeer::PRO_UID)) {
            $criteria->add(AppDelayPeer::PRO_UID, $this->pro_uid);
        }

        if ($this->isColumnModified(AppDelayPeer::APP_UID)) {
            $criteria->add(AppDelayPeer::APP_UID, $this->app_uid);
        }

        if ($this->isColumnModified(AppDelayPeer::APP_NUMBER)) {
            $criteria->add(AppDelayPeer::APP_NUMBER, $this->app_number);
        }

        if ($this->isColumnModified(AppDelayPeer::APP_THREAD_INDEX)) {
            $criteria->add(AppDelayPeer::APP_THREAD_INDEX, $this->app_thread_index);
        }

        if ($this->isColumnModified(AppDelayPeer::APP_DEL_INDEX)) {
            $criteria->add(AppDelayPeer::APP_DEL_INDEX, $this->app_del_index);
        }

        if ($this->isColumnModified(AppDelayPeer::APP_TYPE)) {
            $criteria->add(AppDelayPeer::APP_TYPE, $this->app_type);
        }

        if ($this->isColumnModified(AppDelayPeer::APP_STATUS)) {
            $criteria->add(AppDelayPeer::APP_STATUS, $this->app_status);
        }

        if ($this->isColumnModified(AppDelayPeer::APP_NEXT_TASK)) {
            $criteria->add(AppDelayPeer::APP_NEXT_TASK, $this->app_next_task);
        }

        if ($this->isColumnModified(AppDelayPeer::APP_DELEGATION_USER)) {
            $criteria->add(AppDelayPeer::APP_DELEGATION_USER, $this->app_delegation_user);
        }

        if ($this->isColumnModified(AppDelayPeer::APP_ENABLE_ACTION_USER)) {
            $criteria->add(AppDelayPeer::APP_ENABLE_ACTION_USER, $this->app_enable_action_user);
        }

        if ($this->isColumnModified(AppDelayPeer::APP_ENABLE_ACTION_DATE)) {
            $criteria->add(AppDelayPeer::APP_ENABLE_ACTION_DATE, $this->app_enable_action_date);
        }

        if ($this->isColumnModified(AppDelayPeer::APP_DISABLE_ACTION_USER)) {
            $criteria->add(AppDelayPeer::APP_DISABLE_ACTION_USER, $this->app_disable_action_user);
        }

        if ($this->isColumnModified(AppDelayPeer::APP_DISABLE_ACTION_DATE)) {
            $criteria->add(AppDelayPeer::APP_DISABLE_ACTION_DATE, $this->app_disable_action_date);
        }

        if ($this->isColumnModified(AppDelayPeer::APP_AUTOMATIC_DISABLED_DATE)) {
            $criteria->add(AppDelayPeer::APP_AUTOMATIC_DISABLED_DATE, $this->app_automatic_disabled_date);
        }

        if ($this->isColumnModified(AppDelayPeer::APP_DELEGATION_USER_ID)) {
            $criteria->add(AppDelayPeer::APP_DELEGATION_USER_ID, $this->app_delegation_user_id);
        }

        if ($this->isColumnModified(AppDelayPeer::PRO_ID)) {
            $criteria->add(AppDelayPeer::PRO_ID, $this->pro_id);
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
        $criteria = new Criteria(AppDelayPeer::DATABASE_NAME);

        $criteria->add(AppDelayPeer::APP_DELAY_UID, $this->app_delay_uid);

        return $criteria;
    }

    /**
     * Returns the primary key for this object (row).
     * @return     string
     */
    public function getPrimaryKey()
    {
        return $this->getAppDelayUid();
    }

    /**
     * Generic method to set the primary key (app_delay_uid column).
     *
     * @param      string $key Primary key.
     * @return     void
     */
    public function setPrimaryKey($key)
    {
        $this->setAppDelayUid($key);
    }

    /**
     * Sets contents of passed object to values from current object.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param      object $copyObj An object of AppDelay (or compatible) type.
     * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @throws     PropelException
     */
    public function copyInto($copyObj, $deepCopy = false)
    {

        $copyObj->setProUid($this->pro_uid);

        $copyObj->setAppUid($this->app_uid);

        $copyObj->setAppNumber($this->app_number);

        $copyObj->setAppThreadIndex($this->app_thread_index);

        $copyObj->setAppDelIndex($this->app_del_index);

        $copyObj->setAppType($this->app_type);

        $copyObj->setAppStatus($this->app_status);

        $copyObj->setAppNextTask($this->app_next_task);

        $copyObj->setAppDelegationUser($this->app_delegation_user);

        $copyObj->setAppEnableActionUser($this->app_enable_action_user);

        $copyObj->setAppEnableActionDate($this->app_enable_action_date);

        $copyObj->setAppDisableActionUser($this->app_disable_action_user);

        $copyObj->setAppDisableActionDate($this->app_disable_action_date);

        $copyObj->setAppAutomaticDisabledDate($this->app_automatic_disabled_date);

        $copyObj->setAppDelegationUserId($this->app_delegation_user_id);

        $copyObj->setProId($this->pro_id);


        $copyObj->setNew(true);

        $copyObj->setAppDelayUid(''); // this is a pkey column, so set to default value

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
     * @return     AppDelay Clone of current object.
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
     * @return     AppDelayPeer
     */
    public function getPeer()
    {
        if (self::$peer === null) {
            self::$peer = new AppDelayPeer();
        }
        return self::$peer;
    }
}

