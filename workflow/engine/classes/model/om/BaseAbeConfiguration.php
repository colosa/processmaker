<?php

require_once 'propel/om/BaseObject.php';

require_once 'propel/om/Persistent.php';


include_once 'propel/util/Criteria.php';

include_once 'classes/model/AbeConfigurationPeer.php';

/**
 * Base class that represents a row from the 'ABE_CONFIGURATION' table.
 *
 * 
 *
 * @package    workflow.classes.model.om
 */
abstract class BaseAbeConfiguration extends BaseObject implements Persistent
{

    /**
     * The Peer class.
     * Instance provides a convenient way of calling static methods on a class
     * that calling code may not be able to identify.
     * @var        AbeConfigurationPeer
    */
    protected static $peer;

    /**
     * The value for the abe_uid field.
     * @var        string
     */
    protected $abe_uid = '';

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
     * The value for the abe_type field.
     * @var        string
     */
    protected $abe_type = '';

    /**
     * The value for the abe_template field.
     * @var        string
     */
    protected $abe_template = '';

    /**
     * The value for the abe_dyn_type field.
     * @var        string
     */
    protected $abe_dyn_type = 'NORMAL';

    /**
     * The value for the dyn_uid field.
     * @var        string
     */
    protected $dyn_uid = '';

    /**
     * The value for the abe_email_field field.
     * @var        string
     */
    protected $abe_email_field = '';

    /**
     * The value for the abe_action_field field.
     * @var        string
     */
    protected $abe_action_field = '';

    /**
     * The value for the abe_case_note_in_response field.
     * @var        int
     */
    protected $abe_case_note_in_response = 0;

    /**
     * The value for the abe_create_date field.
     * @var        int
     */
    protected $abe_create_date;

    /**
     * The value for the abe_update_date field.
     * @var        int
     */
    protected $abe_update_date;

    /**
     * The value for the abe_subject_field field.
     * @var        string
     */
    protected $abe_subject_field = '';

    /**
     * The value for the abe_mailserver_or_mailcurrent field.
     * @var        int
     */
    protected $abe_mailserver_or_mailcurrent = 0;

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
     * Get the [abe_uid] column value.
     * 
     * @return     string
     */
    public function getAbeUid()
    {

        return $this->abe_uid;
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
     * Get the [abe_type] column value.
     * 
     * @return     string
     */
    public function getAbeType()
    {

        return $this->abe_type;
    }

    /**
     * Get the [abe_template] column value.
     * 
     * @return     string
     */
    public function getAbeTemplate()
    {

        return $this->abe_template;
    }

    /**
     * Get the [abe_dyn_type] column value.
     * 
     * @return     string
     */
    public function getAbeDynType()
    {

        return $this->abe_dyn_type;
    }

    /**
     * Get the [dyn_uid] column value.
     * 
     * @return     string
     */
    public function getDynUid()
    {

        return $this->dyn_uid;
    }

    /**
     * Get the [abe_email_field] column value.
     * 
     * @return     string
     */
    public function getAbeEmailField()
    {

        return $this->abe_email_field;
    }

    /**
     * Get the [abe_action_field] column value.
     * 
     * @return     string
     */
    public function getAbeActionField()
    {

        return $this->abe_action_field;
    }

    /**
     * Get the [abe_case_note_in_response] column value.
     * 
     * @return     int
     */
    public function getAbeCaseNoteInResponse()
    {

        return $this->abe_case_note_in_response;
    }

    /**
     * Get the [optionally formatted] [abe_create_date] column value.
     * 
     * @param      string $format The date/time format string (either date()-style or strftime()-style).
     *                          If format is NULL, then the integer unix timestamp will be returned.
     * @return     mixed Formatted date/time value as string or integer unix timestamp (if format is NULL).
     * @throws     PropelException - if unable to convert the date/time to timestamp.
     */
    public function getAbeCreateDate($format = 'Y-m-d H:i:s')
    {

        if ($this->abe_create_date === null || $this->abe_create_date === '') {
            return null;
        } elseif (!is_int($this->abe_create_date)) {
            // a non-timestamp value was set externally, so we convert it
            $ts = strtotime($this->abe_create_date);
            if ($ts === -1 || $ts === false) {
                throw new PropelException("Unable to parse value of [abe_create_date] as date/time value: " .
                    var_export($this->abe_create_date, true));
            }
        } else {
            $ts = $this->abe_create_date;
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
     * Get the [optionally formatted] [abe_update_date] column value.
     * 
     * @param      string $format The date/time format string (either date()-style or strftime()-style).
     *                          If format is NULL, then the integer unix timestamp will be returned.
     * @return     mixed Formatted date/time value as string or integer unix timestamp (if format is NULL).
     * @throws     PropelException - if unable to convert the date/time to timestamp.
     */
    public function getAbeUpdateDate($format = 'Y-m-d H:i:s')
    {

        if ($this->abe_update_date === null || $this->abe_update_date === '') {
            return null;
        } elseif (!is_int($this->abe_update_date)) {
            // a non-timestamp value was set externally, so we convert it
            $ts = strtotime($this->abe_update_date);
            if ($ts === -1 || $ts === false) {
                throw new PropelException("Unable to parse value of [abe_update_date] as date/time value: " .
                    var_export($this->abe_update_date, true));
            }
        } else {
            $ts = $this->abe_update_date;
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
     * Get the [abe_subject_field] column value.
     * 
     * @return     string
     */
    public function getAbeSubjectField()
    {

        return $this->abe_subject_field;
    }

    /**
     * Get the [abe_mailserver_or_mailcurrent] column value.
     * 
     * @return     int
     */
    public function getAbeMailserverOrMailcurrent()
    {

        return $this->abe_mailserver_or_mailcurrent;
    }

    /**
     * Set the value of [abe_uid] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setAbeUid($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->abe_uid !== $v || $v === '') {
            $this->abe_uid = $v;
            $this->modifiedColumns[] = AbeConfigurationPeer::ABE_UID;
        }

    } // setAbeUid()

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
            $this->modifiedColumns[] = AbeConfigurationPeer::PRO_UID;
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
            $this->modifiedColumns[] = AbeConfigurationPeer::TAS_UID;
        }

    } // setTasUid()

    /**
     * Set the value of [abe_type] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setAbeType($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->abe_type !== $v || $v === '') {
            $this->abe_type = $v;
            $this->modifiedColumns[] = AbeConfigurationPeer::ABE_TYPE;
        }

    } // setAbeType()

    /**
     * Set the value of [abe_template] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setAbeTemplate($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->abe_template !== $v || $v === '') {
            $this->abe_template = $v;
            $this->modifiedColumns[] = AbeConfigurationPeer::ABE_TEMPLATE;
        }

    } // setAbeTemplate()

    /**
     * Set the value of [abe_dyn_type] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setAbeDynType($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->abe_dyn_type !== $v || $v === 'NORMAL') {
            $this->abe_dyn_type = $v;
            $this->modifiedColumns[] = AbeConfigurationPeer::ABE_DYN_TYPE;
        }

    } // setAbeDynType()

    /**
     * Set the value of [dyn_uid] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setDynUid($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->dyn_uid !== $v || $v === '') {
            $this->dyn_uid = $v;
            $this->modifiedColumns[] = AbeConfigurationPeer::DYN_UID;
        }

    } // setDynUid()

    /**
     * Set the value of [abe_email_field] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setAbeEmailField($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->abe_email_field !== $v || $v === '') {
            $this->abe_email_field = $v;
            $this->modifiedColumns[] = AbeConfigurationPeer::ABE_EMAIL_FIELD;
        }

    } // setAbeEmailField()

    /**
     * Set the value of [abe_action_field] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setAbeActionField($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->abe_action_field !== $v || $v === '') {
            $this->abe_action_field = $v;
            $this->modifiedColumns[] = AbeConfigurationPeer::ABE_ACTION_FIELD;
        }

    } // setAbeActionField()

    /**
     * Set the value of [abe_case_note_in_response] column.
     * 
     * @param      int $v new value
     * @return     void
     */
    public function setAbeCaseNoteInResponse($v)
    {

        // Since the native PHP type for this column is integer,
        // we will cast the input value to an int (if it is not).
        if ($v !== null && !is_int($v) && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->abe_case_note_in_response !== $v || $v === 0) {
            $this->abe_case_note_in_response = $v;
            $this->modifiedColumns[] = AbeConfigurationPeer::ABE_CASE_NOTE_IN_RESPONSE;
        }

    } // setAbeCaseNoteInResponse()

    /**
     * Set the value of [abe_create_date] column.
     * 
     * @param      int $v new value
     * @return     void
     */
    public function setAbeCreateDate($v)
    {

        if ($v !== null && !is_int($v)) {
            $ts = strtotime($v);
            //Date/time accepts null values
            if ($v == '') {
                $ts = null;
            }
            if ($ts === -1 || $ts === false) {
                throw new PropelException("Unable to parse date/time value for [abe_create_date] from input: " .
                    var_export($v, true));
            }
        } else {
            $ts = $v;
        }
        if ($this->abe_create_date !== $ts) {
            $this->abe_create_date = $ts;
            $this->modifiedColumns[] = AbeConfigurationPeer::ABE_CREATE_DATE;
        }

    } // setAbeCreateDate()

    /**
     * Set the value of [abe_update_date] column.
     * 
     * @param      int $v new value
     * @return     void
     */
    public function setAbeUpdateDate($v)
    {

        if ($v !== null && !is_int($v)) {
            $ts = strtotime($v);
            //Date/time accepts null values
            if ($v == '') {
                $ts = null;
            }
            if ($ts === -1 || $ts === false) {
                throw new PropelException("Unable to parse date/time value for [abe_update_date] from input: " .
                    var_export($v, true));
            }
        } else {
            $ts = $v;
        }
        if ($this->abe_update_date !== $ts) {
            $this->abe_update_date = $ts;
            $this->modifiedColumns[] = AbeConfigurationPeer::ABE_UPDATE_DATE;
        }

    } // setAbeUpdateDate()

    /**
     * Set the value of [abe_subject_field] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setAbeSubjectField($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->abe_subject_field !== $v || $v === '') {
            $this->abe_subject_field = $v;
            $this->modifiedColumns[] = AbeConfigurationPeer::ABE_SUBJECT_FIELD;
        }

    } // setAbeSubjectField()

    /**
     * Set the value of [abe_mailserver_or_mailcurrent] column.
     * 
     * @param      int $v new value
     * @return     void
     */
    public function setAbeMailserverOrMailcurrent($v)
    {

        // Since the native PHP type for this column is integer,
        // we will cast the input value to an int (if it is not).
        if ($v !== null && !is_int($v) && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->abe_mailserver_or_mailcurrent !== $v || $v === 0) {
            $this->abe_mailserver_or_mailcurrent = $v;
            $this->modifiedColumns[] = AbeConfigurationPeer::ABE_MAILSERVER_OR_MAILCURRENT;
        }

    } // setAbeMailserverOrMailcurrent()

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

            $this->abe_uid = $rs->getString($startcol + 0);

            $this->pro_uid = $rs->getString($startcol + 1);

            $this->tas_uid = $rs->getString($startcol + 2);

            $this->abe_type = $rs->getString($startcol + 3);

            $this->abe_template = $rs->getString($startcol + 4);

            $this->abe_dyn_type = $rs->getString($startcol + 5);

            $this->dyn_uid = $rs->getString($startcol + 6);

            $this->abe_email_field = $rs->getString($startcol + 7);

            $this->abe_action_field = $rs->getString($startcol + 8);

            $this->abe_case_note_in_response = $rs->getInt($startcol + 9);

            $this->abe_create_date = $rs->getTimestamp($startcol + 10, null);

            $this->abe_update_date = $rs->getTimestamp($startcol + 11, null);

            $this->abe_subject_field = $rs->getString($startcol + 12);

            $this->abe_mailserver_or_mailcurrent = $rs->getInt($startcol + 13);

            $this->resetModified();

            $this->setNew(false);

            // FIXME - using NUM_COLUMNS may be clearer.
            return $startcol + 14; // 14 = AbeConfigurationPeer::NUM_COLUMNS - AbeConfigurationPeer::NUM_LAZY_LOAD_COLUMNS).

        } catch (Exception $e) {
            throw new PropelException("Error populating AbeConfiguration object", $e);
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
            $con = Propel::getConnection(AbeConfigurationPeer::DATABASE_NAME);
        }

        try {
            $con->begin();
            AbeConfigurationPeer::doDelete($this, $con);
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
            $con = Propel::getConnection(AbeConfigurationPeer::DATABASE_NAME);
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
                    $pk = AbeConfigurationPeer::doInsert($this, $con);
                    $affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
                                         // should always be true here (even though technically
                                         // BasePeer::doInsert() can insert multiple rows).

                    $this->setNew(false);
                } else {
                    $affectedRows += AbeConfigurationPeer::doUpdate($this, $con);
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


            if (($retval = AbeConfigurationPeer::doValidate($this, $columns)) !== true) {
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
        $pos = AbeConfigurationPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                return $this->getAbeUid();
                break;
            case 1:
                return $this->getProUid();
                break;
            case 2:
                return $this->getTasUid();
                break;
            case 3:
                return $this->getAbeType();
                break;
            case 4:
                return $this->getAbeTemplate();
                break;
            case 5:
                return $this->getAbeDynType();
                break;
            case 6:
                return $this->getDynUid();
                break;
            case 7:
                return $this->getAbeEmailField();
                break;
            case 8:
                return $this->getAbeActionField();
                break;
            case 9:
                return $this->getAbeCaseNoteInResponse();
                break;
            case 10:
                return $this->getAbeCreateDate();
                break;
            case 11:
                return $this->getAbeUpdateDate();
                break;
            case 12:
                return $this->getAbeSubjectField();
                break;
            case 13:
                return $this->getAbeMailserverOrMailcurrent();
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
        $keys = AbeConfigurationPeer::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getAbeUid(),
            $keys[1] => $this->getProUid(),
            $keys[2] => $this->getTasUid(),
            $keys[3] => $this->getAbeType(),
            $keys[4] => $this->getAbeTemplate(),
            $keys[5] => $this->getAbeDynType(),
            $keys[6] => $this->getDynUid(),
            $keys[7] => $this->getAbeEmailField(),
            $keys[8] => $this->getAbeActionField(),
            $keys[9] => $this->getAbeCaseNoteInResponse(),
            $keys[10] => $this->getAbeCreateDate(),
            $keys[11] => $this->getAbeUpdateDate(),
            $keys[12] => $this->getAbeSubjectField(),
            $keys[13] => $this->getAbeMailserverOrMailcurrent(),
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
        $pos = AbeConfigurationPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                $this->setAbeUid($value);
                break;
            case 1:
                $this->setProUid($value);
                break;
            case 2:
                $this->setTasUid($value);
                break;
            case 3:
                $this->setAbeType($value);
                break;
            case 4:
                $this->setAbeTemplate($value);
                break;
            case 5:
                $this->setAbeDynType($value);
                break;
            case 6:
                $this->setDynUid($value);
                break;
            case 7:
                $this->setAbeEmailField($value);
                break;
            case 8:
                $this->setAbeActionField($value);
                break;
            case 9:
                $this->setAbeCaseNoteInResponse($value);
                break;
            case 10:
                $this->setAbeCreateDate($value);
                break;
            case 11:
                $this->setAbeUpdateDate($value);
                break;
            case 12:
                $this->setAbeSubjectField($value);
                break;
            case 13:
                $this->setAbeMailserverOrMailcurrent($value);
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
        $keys = AbeConfigurationPeer::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) {
            $this->setAbeUid($arr[$keys[0]]);
        }

        if (array_key_exists($keys[1], $arr)) {
            $this->setProUid($arr[$keys[1]]);
        }

        if (array_key_exists($keys[2], $arr)) {
            $this->setTasUid($arr[$keys[2]]);
        }

        if (array_key_exists($keys[3], $arr)) {
            $this->setAbeType($arr[$keys[3]]);
        }

        if (array_key_exists($keys[4], $arr)) {
            $this->setAbeTemplate($arr[$keys[4]]);
        }

        if (array_key_exists($keys[5], $arr)) {
            $this->setAbeDynType($arr[$keys[5]]);
        }

        if (array_key_exists($keys[6], $arr)) {
            $this->setDynUid($arr[$keys[6]]);
        }

        if (array_key_exists($keys[7], $arr)) {
            $this->setAbeEmailField($arr[$keys[7]]);
        }

        if (array_key_exists($keys[8], $arr)) {
            $this->setAbeActionField($arr[$keys[8]]);
        }

        if (array_key_exists($keys[9], $arr)) {
            $this->setAbeCaseNoteInResponse($arr[$keys[9]]);
        }

        if (array_key_exists($keys[10], $arr)) {
            $this->setAbeCreateDate($arr[$keys[10]]);
        }

        if (array_key_exists($keys[11], $arr)) {
            $this->setAbeUpdateDate($arr[$keys[11]]);
        }

        if (array_key_exists($keys[12], $arr)) {
            $this->setAbeSubjectField($arr[$keys[12]]);
        }

        if (array_key_exists($keys[13], $arr)) {
            $this->setAbeMailserverOrMailcurrent($arr[$keys[13]]);
        }

    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return     Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(AbeConfigurationPeer::DATABASE_NAME);

        if ($this->isColumnModified(AbeConfigurationPeer::ABE_UID)) {
            $criteria->add(AbeConfigurationPeer::ABE_UID, $this->abe_uid);
        }

        if ($this->isColumnModified(AbeConfigurationPeer::PRO_UID)) {
            $criteria->add(AbeConfigurationPeer::PRO_UID, $this->pro_uid);
        }

        if ($this->isColumnModified(AbeConfigurationPeer::TAS_UID)) {
            $criteria->add(AbeConfigurationPeer::TAS_UID, $this->tas_uid);
        }

        if ($this->isColumnModified(AbeConfigurationPeer::ABE_TYPE)) {
            $criteria->add(AbeConfigurationPeer::ABE_TYPE, $this->abe_type);
        }

        if ($this->isColumnModified(AbeConfigurationPeer::ABE_TEMPLATE)) {
            $criteria->add(AbeConfigurationPeer::ABE_TEMPLATE, $this->abe_template);
        }

        if ($this->isColumnModified(AbeConfigurationPeer::ABE_DYN_TYPE)) {
            $criteria->add(AbeConfigurationPeer::ABE_DYN_TYPE, $this->abe_dyn_type);
        }

        if ($this->isColumnModified(AbeConfigurationPeer::DYN_UID)) {
            $criteria->add(AbeConfigurationPeer::DYN_UID, $this->dyn_uid);
        }

        if ($this->isColumnModified(AbeConfigurationPeer::ABE_EMAIL_FIELD)) {
            $criteria->add(AbeConfigurationPeer::ABE_EMAIL_FIELD, $this->abe_email_field);
        }

        if ($this->isColumnModified(AbeConfigurationPeer::ABE_ACTION_FIELD)) {
            $criteria->add(AbeConfigurationPeer::ABE_ACTION_FIELD, $this->abe_action_field);
        }

        if ($this->isColumnModified(AbeConfigurationPeer::ABE_CASE_NOTE_IN_RESPONSE)) {
            $criteria->add(AbeConfigurationPeer::ABE_CASE_NOTE_IN_RESPONSE, $this->abe_case_note_in_response);
        }

        if ($this->isColumnModified(AbeConfigurationPeer::ABE_CREATE_DATE)) {
            $criteria->add(AbeConfigurationPeer::ABE_CREATE_DATE, $this->abe_create_date);
        }

        if ($this->isColumnModified(AbeConfigurationPeer::ABE_UPDATE_DATE)) {
            $criteria->add(AbeConfigurationPeer::ABE_UPDATE_DATE, $this->abe_update_date);
        }

        if ($this->isColumnModified(AbeConfigurationPeer::ABE_SUBJECT_FIELD)) {
            $criteria->add(AbeConfigurationPeer::ABE_SUBJECT_FIELD, $this->abe_subject_field);
        }

        if ($this->isColumnModified(AbeConfigurationPeer::ABE_MAILSERVER_OR_MAILCURRENT)) {
            $criteria->add(AbeConfigurationPeer::ABE_MAILSERVER_OR_MAILCURRENT, $this->abe_mailserver_or_mailcurrent);
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
        $criteria = new Criteria(AbeConfigurationPeer::DATABASE_NAME);

        $criteria->add(AbeConfigurationPeer::ABE_UID, $this->abe_uid);

        return $criteria;
    }

    /**
     * Returns the primary key for this object (row).
     * @return     string
     */
    public function getPrimaryKey()
    {
        return $this->getAbeUid();
    }

    /**
     * Generic method to set the primary key (abe_uid column).
     *
     * @param      string $key Primary key.
     * @return     void
     */
    public function setPrimaryKey($key)
    {
        $this->setAbeUid($key);
    }

    /**
     * Sets contents of passed object to values from current object.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param      object $copyObj An object of AbeConfiguration (or compatible) type.
     * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @throws     PropelException
     */
    public function copyInto($copyObj, $deepCopy = false)
    {

        $copyObj->setProUid($this->pro_uid);

        $copyObj->setTasUid($this->tas_uid);

        $copyObj->setAbeType($this->abe_type);

        $copyObj->setAbeTemplate($this->abe_template);

        $copyObj->setAbeDynType($this->abe_dyn_type);

        $copyObj->setDynUid($this->dyn_uid);

        $copyObj->setAbeEmailField($this->abe_email_field);

        $copyObj->setAbeActionField($this->abe_action_field);

        $copyObj->setAbeCaseNoteInResponse($this->abe_case_note_in_response);

        $copyObj->setAbeCreateDate($this->abe_create_date);

        $copyObj->setAbeUpdateDate($this->abe_update_date);

        $copyObj->setAbeSubjectField($this->abe_subject_field);

        $copyObj->setAbeMailserverOrMailcurrent($this->abe_mailserver_or_mailcurrent);


        $copyObj->setNew(true);

        $copyObj->setAbeUid(''); // this is a pkey column, so set to default value

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
     * @return     AbeConfiguration Clone of current object.
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
     * @return     AbeConfigurationPeer
     */
    public function getPeer()
    {
        if (self::$peer === null) {
            self::$peer = new AbeConfigurationPeer();
        }
        return self::$peer;
    }
}

