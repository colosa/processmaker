<?php

require_once 'propel/om/BaseObject.php';

require_once 'propel/om/Persistent.php';


include_once 'propel/util/Criteria.php';

include_once 'classes/model/ApplicationPeer.php';

/**
 * Base class that represents a row from the 'APPLICATION' table.
 *
 * 
 *
 * @package    workflow.classes.model.om
 */
abstract class BaseApplication extends BaseObject implements Persistent
{

    /**
     * The Peer class.
     * Instance provides a convenient way of calling static methods on a class
     * that calling code may not be able to identify.
     * @var        ApplicationPeer
    */
    protected static $peer;

    /**
     * The value for the app_uid field.
     * @var        string
     */
    protected $app_uid = '';

    /**
     * The value for the app_number field.
     * @var        int
     */
    protected $app_number = 0;

    /**
     * The value for the app_parent field.
     * @var        string
     */
    protected $app_parent = '0';

    /**
     * The value for the app_status field.
     * @var        string
     */
    protected $app_status = '';

    /**
     * The value for the pro_uid field.
     * @var        string
     */
    protected $pro_uid = '';

    /**
     * The value for the app_proc_status field.
     * @var        string
     */
    protected $app_proc_status = '';

    /**
     * The value for the app_proc_code field.
     * @var        string
     */
    protected $app_proc_code = '';

    /**
     * The value for the app_parallel field.
     * @var        string
     */
    protected $app_parallel = 'NO';

    /**
     * The value for the app_init_user field.
     * @var        string
     */
    protected $app_init_user = '';

    /**
     * The value for the app_cur_user field.
     * @var        string
     */
    protected $app_cur_user = '';

    /**
     * The value for the app_create_date field.
     * @var        int
     */
    protected $app_create_date;

    /**
     * The value for the app_init_date field.
     * @var        int
     */
    protected $app_init_date;

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
     * The value for the app_data field.
     * @var        string
     */
    protected $app_data;

    /**
     * The value for the app_pin field.
     * @var        string
     */
    protected $app_pin = '';

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
     * Get the [app_number] column value.
     * 
     * @return     int
     */
    public function getAppNumber()
    {

        return $this->app_number;
    }

    /**
     * Get the [app_parent] column value.
     * 
     * @return     string
     */
    public function getAppParent()
    {

        return $this->app_parent;
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
     * Get the [pro_uid] column value.
     * 
     * @return     string
     */
    public function getProUid()
    {

        return $this->pro_uid;
    }

    /**
     * Get the [app_proc_status] column value.
     * 
     * @return     string
     */
    public function getAppProcStatus()
    {

        return $this->app_proc_status;
    }

    /**
     * Get the [app_proc_code] column value.
     * 
     * @return     string
     */
    public function getAppProcCode()
    {

        return $this->app_proc_code;
    }

    /**
     * Get the [app_parallel] column value.
     * 
     * @return     string
     */
    public function getAppParallel()
    {

        return $this->app_parallel;
    }

    /**
     * Get the [app_init_user] column value.
     * 
     * @return     string
     */
    public function getAppInitUser()
    {

        return $this->app_init_user;
    }

    /**
     * Get the [app_cur_user] column value.
     * 
     * @return     string
     */
    public function getAppCurUser()
    {

        return $this->app_cur_user;
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
     * Get the [optionally formatted] [app_init_date] column value.
     * 
     * @param      string $format The date/time format string (either date()-style or strftime()-style).
     *                          If format is NULL, then the integer unix timestamp will be returned.
     * @return     mixed Formatted date/time value as string or integer unix timestamp (if format is NULL).
     * @throws     PropelException - if unable to convert the date/time to timestamp.
     */
    public function getAppInitDate($format = 'Y-m-d H:i:s')
    {

        if ($this->app_init_date === null || $this->app_init_date === '') {
            return null;
        } elseif (!is_int($this->app_init_date)) {
            // a non-timestamp value was set externally, so we convert it
            $ts = strtotime($this->app_init_date);
            if ($ts === -1 || $ts === false) {
                throw new PropelException("Unable to parse value of [app_init_date] as date/time value: " .
                    var_export($this->app_init_date, true));
            }
        } else {
            $ts = $this->app_init_date;
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
     * Get the [app_data] column value.
     * 
     * @return     string
     */
    public function getAppData()
    {

        return $this->app_data;
    }

    /**
     * Get the [app_pin] column value.
     * 
     * @return     string
     */
    public function getAppPin()
    {

        return $this->app_pin;
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
            $this->modifiedColumns[] = ApplicationPeer::APP_UID;
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
            $this->modifiedColumns[] = ApplicationPeer::APP_NUMBER;
        }

    } // setAppNumber()

    /**
     * Set the value of [app_parent] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setAppParent($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->app_parent !== $v || $v === '0') {
            $this->app_parent = $v;
            $this->modifiedColumns[] = ApplicationPeer::APP_PARENT;
        }

    } // setAppParent()

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
            $this->modifiedColumns[] = ApplicationPeer::APP_STATUS;
        }

    } // setAppStatus()

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
            $this->modifiedColumns[] = ApplicationPeer::PRO_UID;
        }

    } // setProUid()

    /**
     * Set the value of [app_proc_status] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setAppProcStatus($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->app_proc_status !== $v || $v === '') {
            $this->app_proc_status = $v;
            $this->modifiedColumns[] = ApplicationPeer::APP_PROC_STATUS;
        }

    } // setAppProcStatus()

    /**
     * Set the value of [app_proc_code] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setAppProcCode($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->app_proc_code !== $v || $v === '') {
            $this->app_proc_code = $v;
            $this->modifiedColumns[] = ApplicationPeer::APP_PROC_CODE;
        }

    } // setAppProcCode()

    /**
     * Set the value of [app_parallel] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setAppParallel($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->app_parallel !== $v || $v === 'NO') {
            $this->app_parallel = $v;
            $this->modifiedColumns[] = ApplicationPeer::APP_PARALLEL;
        }

    } // setAppParallel()

    /**
     * Set the value of [app_init_user] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setAppInitUser($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->app_init_user !== $v || $v === '') {
            $this->app_init_user = $v;
            $this->modifiedColumns[] = ApplicationPeer::APP_INIT_USER;
        }

    } // setAppInitUser()

    /**
     * Set the value of [app_cur_user] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setAppCurUser($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->app_cur_user !== $v || $v === '') {
            $this->app_cur_user = $v;
            $this->modifiedColumns[] = ApplicationPeer::APP_CUR_USER;
        }

    } // setAppCurUser()

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
            $this->modifiedColumns[] = ApplicationPeer::APP_CREATE_DATE;
        }

    } // setAppCreateDate()

    /**
     * Set the value of [app_init_date] column.
     * 
     * @param      int $v new value
     * @return     void
     */
    public function setAppInitDate($v)
    {

        if ($v !== null && !is_int($v)) {
            $ts = strtotime($v);
            if ($ts === -1 || $ts === false) {
                throw new PropelException("Unable to parse date/time value for [app_init_date] from input: " .
                    var_export($v, true));
            }
        } else {
            $ts = $v;
        }
        if ($this->app_init_date !== $ts) {
            $this->app_init_date = $ts;
            $this->modifiedColumns[] = ApplicationPeer::APP_INIT_DATE;
        }

    } // setAppInitDate()

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
            $this->modifiedColumns[] = ApplicationPeer::APP_FINISH_DATE;
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
            $this->modifiedColumns[] = ApplicationPeer::APP_UPDATE_DATE;
        }

    } // setAppUpdateDate()

    /**
     * Set the value of [app_data] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setAppData($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->app_data !== $v) {
            $this->app_data = $v;
            $this->modifiedColumns[] = ApplicationPeer::APP_DATA;
        }

    } // setAppData()

    /**
     * Set the value of [app_pin] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setAppPin($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->app_pin !== $v || $v === '') {
            $this->app_pin = $v;
            $this->modifiedColumns[] = ApplicationPeer::APP_PIN;
        }

    } // setAppPin()

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

            $this->app_number = $rs->getInt($startcol + 1);

            $this->app_parent = $rs->getString($startcol + 2);

            $this->app_status = $rs->getString($startcol + 3);

            $this->pro_uid = $rs->getString($startcol + 4);

            $this->app_proc_status = $rs->getString($startcol + 5);

            $this->app_proc_code = $rs->getString($startcol + 6);

            $this->app_parallel = $rs->getString($startcol + 7);

            $this->app_init_user = $rs->getString($startcol + 8);

            $this->app_cur_user = $rs->getString($startcol + 9);

            $this->app_create_date = $rs->getTimestamp($startcol + 10, null);

            $this->app_init_date = $rs->getTimestamp($startcol + 11, null);

            $this->app_finish_date = $rs->getTimestamp($startcol + 12, null);

            $this->app_update_date = $rs->getTimestamp($startcol + 13, null);

            $this->app_data = $rs->getString($startcol + 14);

            $this->app_pin = $rs->getString($startcol + 15);

            $this->resetModified();

            $this->setNew(false);

            // FIXME - using NUM_COLUMNS may be clearer.
            return $startcol + 16; // 16 = ApplicationPeer::NUM_COLUMNS - ApplicationPeer::NUM_LAZY_LOAD_COLUMNS).

        } catch (Exception $e) {
            throw new PropelException("Error populating Application object", $e);
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
            $con = Propel::getConnection(ApplicationPeer::DATABASE_NAME);
        }

        try {
            $con->begin();
            ApplicationPeer::doDelete($this, $con);
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
            $con = Propel::getConnection(ApplicationPeer::DATABASE_NAME);
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
                    $pk = ApplicationPeer::doInsert($this, $con);
                    $affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
                                         // should always be true here (even though technically
                                         // BasePeer::doInsert() can insert multiple rows).

                    $this->setNew(false);
                } else {
                    $affectedRows += ApplicationPeer::doUpdate($this, $con);
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


            if (($retval = ApplicationPeer::doValidate($this, $columns)) !== true) {
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
        $pos = ApplicationPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                return $this->getAppNumber();
                break;
            case 2:
                return $this->getAppParent();
                break;
            case 3:
                return $this->getAppStatus();
                break;
            case 4:
                return $this->getProUid();
                break;
            case 5:
                return $this->getAppProcStatus();
                break;
            case 6:
                return $this->getAppProcCode();
                break;
            case 7:
                return $this->getAppParallel();
                break;
            case 8:
                return $this->getAppInitUser();
                break;
            case 9:
                return $this->getAppCurUser();
                break;
            case 10:
                return $this->getAppCreateDate();
                break;
            case 11:
                return $this->getAppInitDate();
                break;
            case 12:
                return $this->getAppFinishDate();
                break;
            case 13:
                return $this->getAppUpdateDate();
                break;
            case 14:
                return $this->getAppData();
                break;
            case 15:
                return $this->getAppPin();
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
        $keys = ApplicationPeer::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getAppUid(),
            $keys[1] => $this->getAppNumber(),
            $keys[2] => $this->getAppParent(),
            $keys[3] => $this->getAppStatus(),
            $keys[4] => $this->getProUid(),
            $keys[5] => $this->getAppProcStatus(),
            $keys[6] => $this->getAppProcCode(),
            $keys[7] => $this->getAppParallel(),
            $keys[8] => $this->getAppInitUser(),
            $keys[9] => $this->getAppCurUser(),
            $keys[10] => $this->getAppCreateDate(),
            $keys[11] => $this->getAppInitDate(),
            $keys[12] => $this->getAppFinishDate(),
            $keys[13] => $this->getAppUpdateDate(),
            $keys[14] => $this->getAppData(),
            $keys[15] => $this->getAppPin(),
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
        $pos = ApplicationPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                $this->setAppNumber($value);
                break;
            case 2:
                $this->setAppParent($value);
                break;
            case 3:
                $this->setAppStatus($value);
                break;
            case 4:
                $this->setProUid($value);
                break;
            case 5:
                $this->setAppProcStatus($value);
                break;
            case 6:
                $this->setAppProcCode($value);
                break;
            case 7:
                $this->setAppParallel($value);
                break;
            case 8:
                $this->setAppInitUser($value);
                break;
            case 9:
                $this->setAppCurUser($value);
                break;
            case 10:
                $this->setAppCreateDate($value);
                break;
            case 11:
                $this->setAppInitDate($value);
                break;
            case 12:
                $this->setAppFinishDate($value);
                break;
            case 13:
                $this->setAppUpdateDate($value);
                break;
            case 14:
                $this->setAppData($value);
                break;
            case 15:
                $this->setAppPin($value);
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
        $keys = ApplicationPeer::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) {
            $this->setAppUid($arr[$keys[0]]);
        }

        if (array_key_exists($keys[1], $arr)) {
            $this->setAppNumber($arr[$keys[1]]);
        }

        if (array_key_exists($keys[2], $arr)) {
            $this->setAppParent($arr[$keys[2]]);
        }

        if (array_key_exists($keys[3], $arr)) {
            $this->setAppStatus($arr[$keys[3]]);
        }

        if (array_key_exists($keys[4], $arr)) {
            $this->setProUid($arr[$keys[4]]);
        }

        if (array_key_exists($keys[5], $arr)) {
            $this->setAppProcStatus($arr[$keys[5]]);
        }

        if (array_key_exists($keys[6], $arr)) {
            $this->setAppProcCode($arr[$keys[6]]);
        }

        if (array_key_exists($keys[7], $arr)) {
            $this->setAppParallel($arr[$keys[7]]);
        }

        if (array_key_exists($keys[8], $arr)) {
            $this->setAppInitUser($arr[$keys[8]]);
        }

        if (array_key_exists($keys[9], $arr)) {
            $this->setAppCurUser($arr[$keys[9]]);
        }

        if (array_key_exists($keys[10], $arr)) {
            $this->setAppCreateDate($arr[$keys[10]]);
        }

        if (array_key_exists($keys[11], $arr)) {
            $this->setAppInitDate($arr[$keys[11]]);
        }

        if (array_key_exists($keys[12], $arr)) {
            $this->setAppFinishDate($arr[$keys[12]]);
        }

        if (array_key_exists($keys[13], $arr)) {
            $this->setAppUpdateDate($arr[$keys[13]]);
        }

        if (array_key_exists($keys[14], $arr)) {
            $this->setAppData($arr[$keys[14]]);
        }

        if (array_key_exists($keys[15], $arr)) {
            $this->setAppPin($arr[$keys[15]]);
        }

    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return     Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(ApplicationPeer::DATABASE_NAME);

        if ($this->isColumnModified(ApplicationPeer::APP_UID)) {
            $criteria->add(ApplicationPeer::APP_UID, $this->app_uid);
        }

        if ($this->isColumnModified(ApplicationPeer::APP_NUMBER)) {
            $criteria->add(ApplicationPeer::APP_NUMBER, $this->app_number);
        }

        if ($this->isColumnModified(ApplicationPeer::APP_PARENT)) {
            $criteria->add(ApplicationPeer::APP_PARENT, $this->app_parent);
        }

        if ($this->isColumnModified(ApplicationPeer::APP_STATUS)) {
            $criteria->add(ApplicationPeer::APP_STATUS, $this->app_status);
        }

        if ($this->isColumnModified(ApplicationPeer::PRO_UID)) {
            $criteria->add(ApplicationPeer::PRO_UID, $this->pro_uid);
        }

        if ($this->isColumnModified(ApplicationPeer::APP_PROC_STATUS)) {
            $criteria->add(ApplicationPeer::APP_PROC_STATUS, $this->app_proc_status);
        }

        if ($this->isColumnModified(ApplicationPeer::APP_PROC_CODE)) {
            $criteria->add(ApplicationPeer::APP_PROC_CODE, $this->app_proc_code);
        }

        if ($this->isColumnModified(ApplicationPeer::APP_PARALLEL)) {
            $criteria->add(ApplicationPeer::APP_PARALLEL, $this->app_parallel);
        }

        if ($this->isColumnModified(ApplicationPeer::APP_INIT_USER)) {
            $criteria->add(ApplicationPeer::APP_INIT_USER, $this->app_init_user);
        }

        if ($this->isColumnModified(ApplicationPeer::APP_CUR_USER)) {
            $criteria->add(ApplicationPeer::APP_CUR_USER, $this->app_cur_user);
        }

        if ($this->isColumnModified(ApplicationPeer::APP_CREATE_DATE)) {
            $criteria->add(ApplicationPeer::APP_CREATE_DATE, $this->app_create_date);
        }

        if ($this->isColumnModified(ApplicationPeer::APP_INIT_DATE)) {
            $criteria->add(ApplicationPeer::APP_INIT_DATE, $this->app_init_date);
        }

        if ($this->isColumnModified(ApplicationPeer::APP_FINISH_DATE)) {
            $criteria->add(ApplicationPeer::APP_FINISH_DATE, $this->app_finish_date);
        }

        if ($this->isColumnModified(ApplicationPeer::APP_UPDATE_DATE)) {
            $criteria->add(ApplicationPeer::APP_UPDATE_DATE, $this->app_update_date);
        }

        if ($this->isColumnModified(ApplicationPeer::APP_DATA)) {
            $criteria->add(ApplicationPeer::APP_DATA, $this->app_data);
        }

        if ($this->isColumnModified(ApplicationPeer::APP_PIN)) {
            $criteria->add(ApplicationPeer::APP_PIN, $this->app_pin);
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
        $criteria = new Criteria(ApplicationPeer::DATABASE_NAME);

        $criteria->add(ApplicationPeer::APP_UID, $this->app_uid);

        return $criteria;
    }

    /**
     * Returns the primary key for this object (row).
     * @return     string
     */
    public function getPrimaryKey()
    {
        return $this->getAppUid();
    }

    /**
     * Generic method to set the primary key (app_uid column).
     *
     * @param      string $key Primary key.
     * @return     void
     */
    public function setPrimaryKey($key)
    {
        $this->setAppUid($key);
    }

    /**
     * Sets contents of passed object to values from current object.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param      object $copyObj An object of Application (or compatible) type.
     * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @throws     PropelException
     */
    public function copyInto($copyObj, $deepCopy = false)
    {

        $copyObj->setAppNumber($this->app_number);

        $copyObj->setAppParent($this->app_parent);

        $copyObj->setAppStatus($this->app_status);

        $copyObj->setProUid($this->pro_uid);

        $copyObj->setAppProcStatus($this->app_proc_status);

        $copyObj->setAppProcCode($this->app_proc_code);

        $copyObj->setAppParallel($this->app_parallel);

        $copyObj->setAppInitUser($this->app_init_user);

        $copyObj->setAppCurUser($this->app_cur_user);

        $copyObj->setAppCreateDate($this->app_create_date);

        $copyObj->setAppInitDate($this->app_init_date);

        $copyObj->setAppFinishDate($this->app_finish_date);

        $copyObj->setAppUpdateDate($this->app_update_date);

        $copyObj->setAppData($this->app_data);

        $copyObj->setAppPin($this->app_pin);


        $copyObj->setNew(true);

        $copyObj->setAppUid(''); // this is a pkey column, so set to default value

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
     * @return     Application Clone of current object.
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
     * @return     ApplicationPeer
     */
    public function getPeer()
    {
        if (self::$peer === null) {
            self::$peer = new ApplicationPeer();
        }
        return self::$peer;
    }
}

