<?php

require_once 'propel/om/BaseObject.php';

require_once 'propel/om/Persistent.php';


include_once 'propel/util/Criteria.php';

include_once 'classes/model/EventPeer.php';

/**
 * Base class that represents a row from the 'EVENT' table.
 *
 * 
 *
 * @package    workflow.classes.model.om
 */
abstract class BaseEvent extends BaseObject implements Persistent
{

    /**
     * The Peer class.
     * Instance provides a convenient way of calling static methods on a class
     * that calling code may not be able to identify.
     * @var        EventPeer
    */
    protected static $peer;

    /**
     * The value for the evn_uid field.
     * @var        string
     */
    protected $evn_uid = '';

    /**
     * The value for the pro_uid field.
     * @var        string
     */
    protected $pro_uid = '';

    /**
     * The value for the evn_status field.
     * @var        string
     */
    protected $evn_status = 'OPEN';

    /**
     * The value for the evn_when_occurs field.
     * @var        string
     */
    protected $evn_when_occurs = 'SINGLE';

    /**
     * The value for the evn_related_to field.
     * @var        string
     */
    protected $evn_related_to = 'SINGLE';

    /**
     * The value for the tas_uid field.
     * @var        string
     */
    protected $tas_uid = '';

    /**
     * The value for the evn_tas_uid_from field.
     * @var        string
     */
    protected $evn_tas_uid_from = '';

    /**
     * The value for the evn_tas_uid_to field.
     * @var        string
     */
    protected $evn_tas_uid_to = '';

    /**
     * The value for the evn_tas_estimated_duration field.
     * @var        double
     */
    protected $evn_tas_estimated_duration = 0;

    /**
     * The value for the evn_time_unit field.
     * @var        string
     */
    protected $evn_time_unit = 'DAYS';

    /**
     * The value for the evn_when field.
     * @var        double
     */
    protected $evn_when = 0;

    /**
     * The value for the evn_max_attempts field.
     * @var        int
     */
    protected $evn_max_attempts = 3;

    /**
     * The value for the evn_action field.
     * @var        string
     */
    protected $evn_action = '';

    /**
     * The value for the evn_conditions field.
     * @var        string
     */
    protected $evn_conditions;

    /**
     * The value for the evn_action_parameters field.
     * @var        string
     */
    protected $evn_action_parameters;

    /**
     * The value for the tri_uid field.
     * @var        string
     */
    protected $tri_uid = '';

    /**
     * The value for the evn_posx field.
     * @var        int
     */
    protected $evn_posx = 0;

    /**
     * The value for the evn_posy field.
     * @var        int
     */
    protected $evn_posy = 0;

    /**
     * The value for the evn_type field.
     * @var        string
     */
    protected $evn_type = '';

    /**
     * The value for the tas_evn_uid field.
     * @var        string
     */
    protected $tas_evn_uid = '';

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
     * Get the [evn_uid] column value.
     * 
     * @return     string
     */
    public function getEvnUid()
    {

        return $this->evn_uid;
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
     * Get the [evn_status] column value.
     * 
     * @return     string
     */
    public function getEvnStatus()
    {

        return $this->evn_status;
    }

    /**
     * Get the [evn_when_occurs] column value.
     * 
     * @return     string
     */
    public function getEvnWhenOccurs()
    {

        return $this->evn_when_occurs;
    }

    /**
     * Get the [evn_related_to] column value.
     * 
     * @return     string
     */
    public function getEvnRelatedTo()
    {

        return $this->evn_related_to;
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
     * Get the [evn_tas_uid_from] column value.
     * 
     * @return     string
     */
    public function getEvnTasUidFrom()
    {

        return $this->evn_tas_uid_from;
    }

    /**
     * Get the [evn_tas_uid_to] column value.
     * 
     * @return     string
     */
    public function getEvnTasUidTo()
    {

        return $this->evn_tas_uid_to;
    }

    /**
     * Get the [evn_tas_estimated_duration] column value.
     * 
     * @return     double
     */
    public function getEvnTasEstimatedDuration()
    {

        return $this->evn_tas_estimated_duration;
    }

    /**
     * Get the [evn_time_unit] column value.
     * 
     * @return     string
     */
    public function getEvnTimeUnit()
    {

        return $this->evn_time_unit;
    }

    /**
     * Get the [evn_when] column value.
     * 
     * @return     double
     */
    public function getEvnWhen()
    {

        return $this->evn_when;
    }

    /**
     * Get the [evn_max_attempts] column value.
     * 
     * @return     int
     */
    public function getEvnMaxAttempts()
    {

        return $this->evn_max_attempts;
    }

    /**
     * Get the [evn_action] column value.
     * 
     * @return     string
     */
    public function getEvnAction()
    {

        return $this->evn_action;
    }

    /**
     * Get the [evn_conditions] column value.
     * 
     * @return     string
     */
    public function getEvnConditions()
    {

        return $this->evn_conditions;
    }

    /**
     * Get the [evn_action_parameters] column value.
     * 
     * @return     string
     */
    public function getEvnActionParameters()
    {

        return $this->evn_action_parameters;
    }

    /**
     * Get the [tri_uid] column value.
     * 
     * @return     string
     */
    public function getTriUid()
    {

        return $this->tri_uid;
    }

    /**
     * Get the [evn_posx] column value.
     * 
     * @return     int
     */
    public function getEvnPosx()
    {

        return $this->evn_posx;
    }

    /**
     * Get the [evn_posy] column value.
     * 
     * @return     int
     */
    public function getEvnPosy()
    {

        return $this->evn_posy;
    }

    /**
     * Get the [evn_type] column value.
     * 
     * @return     string
     */
    public function getEvnType()
    {

        return $this->evn_type;
    }

    /**
     * Get the [tas_evn_uid] column value.
     * 
     * @return     string
     */
    public function getTasEvnUid()
    {

        return $this->tas_evn_uid;
    }

    /**
     * Set the value of [evn_uid] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setEvnUid($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->evn_uid !== $v || $v === '') {
            $this->evn_uid = $v;
            $this->modifiedColumns[] = EventPeer::EVN_UID;
        }

    } // setEvnUid()

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
            $this->modifiedColumns[] = EventPeer::PRO_UID;
        }

    } // setProUid()

    /**
     * Set the value of [evn_status] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setEvnStatus($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->evn_status !== $v || $v === 'OPEN') {
            $this->evn_status = $v;
            $this->modifiedColumns[] = EventPeer::EVN_STATUS;
        }

    } // setEvnStatus()

    /**
     * Set the value of [evn_when_occurs] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setEvnWhenOccurs($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->evn_when_occurs !== $v || $v === 'SINGLE') {
            $this->evn_when_occurs = $v;
            $this->modifiedColumns[] = EventPeer::EVN_WHEN_OCCURS;
        }

    } // setEvnWhenOccurs()

    /**
     * Set the value of [evn_related_to] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setEvnRelatedTo($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->evn_related_to !== $v || $v === 'SINGLE') {
            $this->evn_related_to = $v;
            $this->modifiedColumns[] = EventPeer::EVN_RELATED_TO;
        }

    } // setEvnRelatedTo()

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
            $this->modifiedColumns[] = EventPeer::TAS_UID;
        }

    } // setTasUid()

    /**
     * Set the value of [evn_tas_uid_from] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setEvnTasUidFrom($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->evn_tas_uid_from !== $v || $v === '') {
            $this->evn_tas_uid_from = $v;
            $this->modifiedColumns[] = EventPeer::EVN_TAS_UID_FROM;
        }

    } // setEvnTasUidFrom()

    /**
     * Set the value of [evn_tas_uid_to] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setEvnTasUidTo($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->evn_tas_uid_to !== $v || $v === '') {
            $this->evn_tas_uid_to = $v;
            $this->modifiedColumns[] = EventPeer::EVN_TAS_UID_TO;
        }

    } // setEvnTasUidTo()

    /**
     * Set the value of [evn_tas_estimated_duration] column.
     * 
     * @param      double $v new value
     * @return     void
     */
    public function setEvnTasEstimatedDuration($v)
    {

        if ($this->evn_tas_estimated_duration !== $v || $v === 0) {
            $this->evn_tas_estimated_duration = $v;
            $this->modifiedColumns[] = EventPeer::EVN_TAS_ESTIMATED_DURATION;
        }

    } // setEvnTasEstimatedDuration()

    /**
     * Set the value of [evn_time_unit] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setEvnTimeUnit($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->evn_time_unit !== $v || $v === 'DAYS') {
            $this->evn_time_unit = $v;
            $this->modifiedColumns[] = EventPeer::EVN_TIME_UNIT;
        }

    } // setEvnTimeUnit()

    /**
     * Set the value of [evn_when] column.
     * 
     * @param      double $v new value
     * @return     void
     */
    public function setEvnWhen($v)
    {

        if ($this->evn_when !== $v || $v === 0) {
            $this->evn_when = $v;
            $this->modifiedColumns[] = EventPeer::EVN_WHEN;
        }

    } // setEvnWhen()

    /**
     * Set the value of [evn_max_attempts] column.
     * 
     * @param      int $v new value
     * @return     void
     */
    public function setEvnMaxAttempts($v)
    {

        // Since the native PHP type for this column is integer,
        // we will cast the input value to an int (if it is not).
        if ($v !== null && !is_int($v) && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->evn_max_attempts !== $v || $v === 3) {
            $this->evn_max_attempts = $v;
            $this->modifiedColumns[] = EventPeer::EVN_MAX_ATTEMPTS;
        }

    } // setEvnMaxAttempts()

    /**
     * Set the value of [evn_action] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setEvnAction($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->evn_action !== $v || $v === '') {
            $this->evn_action = $v;
            $this->modifiedColumns[] = EventPeer::EVN_ACTION;
        }

    } // setEvnAction()

    /**
     * Set the value of [evn_conditions] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setEvnConditions($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->evn_conditions !== $v) {
            $this->evn_conditions = $v;
            $this->modifiedColumns[] = EventPeer::EVN_CONDITIONS;
        }

    } // setEvnConditions()

    /**
     * Set the value of [evn_action_parameters] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setEvnActionParameters($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->evn_action_parameters !== $v) {
            $this->evn_action_parameters = $v;
            $this->modifiedColumns[] = EventPeer::EVN_ACTION_PARAMETERS;
        }

    } // setEvnActionParameters()

    /**
     * Set the value of [tri_uid] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setTriUid($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->tri_uid !== $v || $v === '') {
            $this->tri_uid = $v;
            $this->modifiedColumns[] = EventPeer::TRI_UID;
        }

    } // setTriUid()

    /**
     * Set the value of [evn_posx] column.
     * 
     * @param      int $v new value
     * @return     void
     */
    public function setEvnPosx($v)
    {

        // Since the native PHP type for this column is integer,
        // we will cast the input value to an int (if it is not).
        if ($v !== null && !is_int($v) && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->evn_posx !== $v || $v === 0) {
            $this->evn_posx = $v;
            $this->modifiedColumns[] = EventPeer::EVN_POSX;
        }

    } // setEvnPosx()

    /**
     * Set the value of [evn_posy] column.
     * 
     * @param      int $v new value
     * @return     void
     */
    public function setEvnPosy($v)
    {

        // Since the native PHP type for this column is integer,
        // we will cast the input value to an int (if it is not).
        if ($v !== null && !is_int($v) && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->evn_posy !== $v || $v === 0) {
            $this->evn_posy = $v;
            $this->modifiedColumns[] = EventPeer::EVN_POSY;
        }

    } // setEvnPosy()

    /**
     * Set the value of [evn_type] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setEvnType($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->evn_type !== $v || $v === '') {
            $this->evn_type = $v;
            $this->modifiedColumns[] = EventPeer::EVN_TYPE;
        }

    } // setEvnType()

    /**
     * Set the value of [tas_evn_uid] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setTasEvnUid($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->tas_evn_uid !== $v || $v === '') {
            $this->tas_evn_uid = $v;
            $this->modifiedColumns[] = EventPeer::TAS_EVN_UID;
        }

    } // setTasEvnUid()

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

            $this->evn_uid = $rs->getString($startcol + 0);

            $this->pro_uid = $rs->getString($startcol + 1);

            $this->evn_status = $rs->getString($startcol + 2);

            $this->evn_when_occurs = $rs->getString($startcol + 3);

            $this->evn_related_to = $rs->getString($startcol + 4);

            $this->tas_uid = $rs->getString($startcol + 5);

            $this->evn_tas_uid_from = $rs->getString($startcol + 6);

            $this->evn_tas_uid_to = $rs->getString($startcol + 7);

            $this->evn_tas_estimated_duration = $rs->getFloat($startcol + 8);

            $this->evn_time_unit = $rs->getString($startcol + 9);

            $this->evn_when = $rs->getFloat($startcol + 10);

            $this->evn_max_attempts = $rs->getInt($startcol + 11);

            $this->evn_action = $rs->getString($startcol + 12);

            $this->evn_conditions = $rs->getString($startcol + 13);

            $this->evn_action_parameters = $rs->getString($startcol + 14);

            $this->tri_uid = $rs->getString($startcol + 15);

            $this->evn_posx = $rs->getInt($startcol + 16);

            $this->evn_posy = $rs->getInt($startcol + 17);

            $this->evn_type = $rs->getString($startcol + 18);

            $this->tas_evn_uid = $rs->getString($startcol + 19);

            $this->resetModified();

            $this->setNew(false);

            // FIXME - using NUM_COLUMNS may be clearer.
            return $startcol + 20; // 20 = EventPeer::NUM_COLUMNS - EventPeer::NUM_LAZY_LOAD_COLUMNS).

        } catch (Exception $e) {
            throw new PropelException("Error populating Event object", $e);
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
            $con = Propel::getConnection(EventPeer::DATABASE_NAME);
        }

        try {
            $con->begin();
            EventPeer::doDelete($this, $con);
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
            $con = Propel::getConnection(EventPeer::DATABASE_NAME);
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
                    $pk = EventPeer::doInsert($this, $con);
                    $affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
                                         // should always be true here (even though technically
                                         // BasePeer::doInsert() can insert multiple rows).

                    $this->setNew(false);
                } else {
                    $affectedRows += EventPeer::doUpdate($this, $con);
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


            if (($retval = EventPeer::doValidate($this, $columns)) !== true) {
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
        $pos = EventPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                return $this->getEvnUid();
                break;
            case 1:
                return $this->getProUid();
                break;
            case 2:
                return $this->getEvnStatus();
                break;
            case 3:
                return $this->getEvnWhenOccurs();
                break;
            case 4:
                return $this->getEvnRelatedTo();
                break;
            case 5:
                return $this->getTasUid();
                break;
            case 6:
                return $this->getEvnTasUidFrom();
                break;
            case 7:
                return $this->getEvnTasUidTo();
                break;
            case 8:
                return $this->getEvnTasEstimatedDuration();
                break;
            case 9:
                return $this->getEvnTimeUnit();
                break;
            case 10:
                return $this->getEvnWhen();
                break;
            case 11:
                return $this->getEvnMaxAttempts();
                break;
            case 12:
                return $this->getEvnAction();
                break;
            case 13:
                return $this->getEvnConditions();
                break;
            case 14:
                return $this->getEvnActionParameters();
                break;
            case 15:
                return $this->getTriUid();
                break;
            case 16:
                return $this->getEvnPosx();
                break;
            case 17:
                return $this->getEvnPosy();
                break;
            case 18:
                return $this->getEvnType();
                break;
            case 19:
                return $this->getTasEvnUid();
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
        $keys = EventPeer::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getEvnUid(),
            $keys[1] => $this->getProUid(),
            $keys[2] => $this->getEvnStatus(),
            $keys[3] => $this->getEvnWhenOccurs(),
            $keys[4] => $this->getEvnRelatedTo(),
            $keys[5] => $this->getTasUid(),
            $keys[6] => $this->getEvnTasUidFrom(),
            $keys[7] => $this->getEvnTasUidTo(),
            $keys[8] => $this->getEvnTasEstimatedDuration(),
            $keys[9] => $this->getEvnTimeUnit(),
            $keys[10] => $this->getEvnWhen(),
            $keys[11] => $this->getEvnMaxAttempts(),
            $keys[12] => $this->getEvnAction(),
            $keys[13] => $this->getEvnConditions(),
            $keys[14] => $this->getEvnActionParameters(),
            $keys[15] => $this->getTriUid(),
            $keys[16] => $this->getEvnPosx(),
            $keys[17] => $this->getEvnPosy(),
            $keys[18] => $this->getEvnType(),
            $keys[19] => $this->getTasEvnUid(),
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
        $pos = EventPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                $this->setEvnUid($value);
                break;
            case 1:
                $this->setProUid($value);
                break;
            case 2:
                $this->setEvnStatus($value);
                break;
            case 3:
                $this->setEvnWhenOccurs($value);
                break;
            case 4:
                $this->setEvnRelatedTo($value);
                break;
            case 5:
                $this->setTasUid($value);
                break;
            case 6:
                $this->setEvnTasUidFrom($value);
                break;
            case 7:
                $this->setEvnTasUidTo($value);
                break;
            case 8:
                $this->setEvnTasEstimatedDuration($value);
                break;
            case 9:
                $this->setEvnTimeUnit($value);
                break;
            case 10:
                $this->setEvnWhen($value);
                break;
            case 11:
                $this->setEvnMaxAttempts($value);
                break;
            case 12:
                $this->setEvnAction($value);
                break;
            case 13:
                $this->setEvnConditions($value);
                break;
            case 14:
                $this->setEvnActionParameters($value);
                break;
            case 15:
                $this->setTriUid($value);
                break;
            case 16:
                $this->setEvnPosx($value);
                break;
            case 17:
                $this->setEvnPosy($value);
                break;
            case 18:
                $this->setEvnType($value);
                break;
            case 19:
                $this->setTasEvnUid($value);
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
        $keys = EventPeer::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) {
            $this->setEvnUid($arr[$keys[0]]);
        }

        if (array_key_exists($keys[1], $arr)) {
            $this->setProUid($arr[$keys[1]]);
        }

        if (array_key_exists($keys[2], $arr)) {
            $this->setEvnStatus($arr[$keys[2]]);
        }

        if (array_key_exists($keys[3], $arr)) {
            $this->setEvnWhenOccurs($arr[$keys[3]]);
        }

        if (array_key_exists($keys[4], $arr)) {
            $this->setEvnRelatedTo($arr[$keys[4]]);
        }

        if (array_key_exists($keys[5], $arr)) {
            $this->setTasUid($arr[$keys[5]]);
        }

        if (array_key_exists($keys[6], $arr)) {
            $this->setEvnTasUidFrom($arr[$keys[6]]);
        }

        if (array_key_exists($keys[7], $arr)) {
            $this->setEvnTasUidTo($arr[$keys[7]]);
        }

        if (array_key_exists($keys[8], $arr)) {
            $this->setEvnTasEstimatedDuration($arr[$keys[8]]);
        }

        if (array_key_exists($keys[9], $arr)) {
            $this->setEvnTimeUnit($arr[$keys[9]]);
        }

        if (array_key_exists($keys[10], $arr)) {
            $this->setEvnWhen($arr[$keys[10]]);
        }

        if (array_key_exists($keys[11], $arr)) {
            $this->setEvnMaxAttempts($arr[$keys[11]]);
        }

        if (array_key_exists($keys[12], $arr)) {
            $this->setEvnAction($arr[$keys[12]]);
        }

        if (array_key_exists($keys[13], $arr)) {
            $this->setEvnConditions($arr[$keys[13]]);
        }

        if (array_key_exists($keys[14], $arr)) {
            $this->setEvnActionParameters($arr[$keys[14]]);
        }

        if (array_key_exists($keys[15], $arr)) {
            $this->setTriUid($arr[$keys[15]]);
        }

        if (array_key_exists($keys[16], $arr)) {
            $this->setEvnPosx($arr[$keys[16]]);
        }

        if (array_key_exists($keys[17], $arr)) {
            $this->setEvnPosy($arr[$keys[17]]);
        }

        if (array_key_exists($keys[18], $arr)) {
            $this->setEvnType($arr[$keys[18]]);
        }

        if (array_key_exists($keys[19], $arr)) {
            $this->setTasEvnUid($arr[$keys[19]]);
        }

    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return     Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(EventPeer::DATABASE_NAME);

        if ($this->isColumnModified(EventPeer::EVN_UID)) {
            $criteria->add(EventPeer::EVN_UID, $this->evn_uid);
        }

        if ($this->isColumnModified(EventPeer::PRO_UID)) {
            $criteria->add(EventPeer::PRO_UID, $this->pro_uid);
        }

        if ($this->isColumnModified(EventPeer::EVN_STATUS)) {
            $criteria->add(EventPeer::EVN_STATUS, $this->evn_status);
        }

        if ($this->isColumnModified(EventPeer::EVN_WHEN_OCCURS)) {
            $criteria->add(EventPeer::EVN_WHEN_OCCURS, $this->evn_when_occurs);
        }

        if ($this->isColumnModified(EventPeer::EVN_RELATED_TO)) {
            $criteria->add(EventPeer::EVN_RELATED_TO, $this->evn_related_to);
        }

        if ($this->isColumnModified(EventPeer::TAS_UID)) {
            $criteria->add(EventPeer::TAS_UID, $this->tas_uid);
        }

        if ($this->isColumnModified(EventPeer::EVN_TAS_UID_FROM)) {
            $criteria->add(EventPeer::EVN_TAS_UID_FROM, $this->evn_tas_uid_from);
        }

        if ($this->isColumnModified(EventPeer::EVN_TAS_UID_TO)) {
            $criteria->add(EventPeer::EVN_TAS_UID_TO, $this->evn_tas_uid_to);
        }

        if ($this->isColumnModified(EventPeer::EVN_TAS_ESTIMATED_DURATION)) {
            $criteria->add(EventPeer::EVN_TAS_ESTIMATED_DURATION, $this->evn_tas_estimated_duration);
        }

        if ($this->isColumnModified(EventPeer::EVN_TIME_UNIT)) {
            $criteria->add(EventPeer::EVN_TIME_UNIT, $this->evn_time_unit);
        }

        if ($this->isColumnModified(EventPeer::EVN_WHEN)) {
            $criteria->add(EventPeer::EVN_WHEN, $this->evn_when);
        }

        if ($this->isColumnModified(EventPeer::EVN_MAX_ATTEMPTS)) {
            $criteria->add(EventPeer::EVN_MAX_ATTEMPTS, $this->evn_max_attempts);
        }

        if ($this->isColumnModified(EventPeer::EVN_ACTION)) {
            $criteria->add(EventPeer::EVN_ACTION, $this->evn_action);
        }

        if ($this->isColumnModified(EventPeer::EVN_CONDITIONS)) {
            $criteria->add(EventPeer::EVN_CONDITIONS, $this->evn_conditions);
        }

        if ($this->isColumnModified(EventPeer::EVN_ACTION_PARAMETERS)) {
            $criteria->add(EventPeer::EVN_ACTION_PARAMETERS, $this->evn_action_parameters);
        }

        if ($this->isColumnModified(EventPeer::TRI_UID)) {
            $criteria->add(EventPeer::TRI_UID, $this->tri_uid);
        }

        if ($this->isColumnModified(EventPeer::EVN_POSX)) {
            $criteria->add(EventPeer::EVN_POSX, $this->evn_posx);
        }

        if ($this->isColumnModified(EventPeer::EVN_POSY)) {
            $criteria->add(EventPeer::EVN_POSY, $this->evn_posy);
        }

        if ($this->isColumnModified(EventPeer::EVN_TYPE)) {
            $criteria->add(EventPeer::EVN_TYPE, $this->evn_type);
        }

        if ($this->isColumnModified(EventPeer::TAS_EVN_UID)) {
            $criteria->add(EventPeer::TAS_EVN_UID, $this->tas_evn_uid);
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
        $criteria = new Criteria(EventPeer::DATABASE_NAME);

        $criteria->add(EventPeer::EVN_UID, $this->evn_uid);

        return $criteria;
    }

    /**
     * Returns the primary key for this object (row).
     * @return     string
     */
    public function getPrimaryKey()
    {
        return $this->getEvnUid();
    }

    /**
     * Generic method to set the primary key (evn_uid column).
     *
     * @param      string $key Primary key.
     * @return     void
     */
    public function setPrimaryKey($key)
    {
        $this->setEvnUid($key);
    }

    /**
     * Sets contents of passed object to values from current object.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param      object $copyObj An object of Event (or compatible) type.
     * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @throws     PropelException
     */
    public function copyInto($copyObj, $deepCopy = false)
    {

        $copyObj->setProUid($this->pro_uid);

        $copyObj->setEvnStatus($this->evn_status);

        $copyObj->setEvnWhenOccurs($this->evn_when_occurs);

        $copyObj->setEvnRelatedTo($this->evn_related_to);

        $copyObj->setTasUid($this->tas_uid);

        $copyObj->setEvnTasUidFrom($this->evn_tas_uid_from);

        $copyObj->setEvnTasUidTo($this->evn_tas_uid_to);

        $copyObj->setEvnTasEstimatedDuration($this->evn_tas_estimated_duration);

        $copyObj->setEvnTimeUnit($this->evn_time_unit);

        $copyObj->setEvnWhen($this->evn_when);

        $copyObj->setEvnMaxAttempts($this->evn_max_attempts);

        $copyObj->setEvnAction($this->evn_action);

        $copyObj->setEvnConditions($this->evn_conditions);

        $copyObj->setEvnActionParameters($this->evn_action_parameters);

        $copyObj->setTriUid($this->tri_uid);

        $copyObj->setEvnPosx($this->evn_posx);

        $copyObj->setEvnPosy($this->evn_posy);

        $copyObj->setEvnType($this->evn_type);

        $copyObj->setTasEvnUid($this->tas_evn_uid);


        $copyObj->setNew(true);

        $copyObj->setEvnUid(''); // this is a pkey column, so set to default value

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
     * @return     Event Clone of current object.
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
     * @return     EventPeer
     */
    public function getPeer()
    {
        if (self::$peer === null) {
            self::$peer = new EventPeer();
        }
        return self::$peer;
    }
}

