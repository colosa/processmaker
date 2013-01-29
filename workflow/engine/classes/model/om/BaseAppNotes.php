<?php

require_once 'propel/om/BaseObject.php';

require_once 'propel/om/Persistent.php';


include_once 'propel/util/Criteria.php';

include_once 'classes/model/AppNotesPeer.php';

/**
 * Base class that represents a row from the 'APP_NOTES' table.
 *
 * 
 *
 * @package    workflow.classes.model.om
 */
abstract class BaseAppNotes extends BaseObject implements Persistent
{

    /**
     * The Peer class.
     * Instance provides a convenient way of calling static methods on a class
     * that calling code may not be able to identify.
     * @var        AppNotesPeer
    */
    protected static $peer;

    /**
     * The value for the app_uid field.
     * @var        string
     */
    protected $app_uid = '';

    /**
     * The value for the usr_uid field.
     * @var        string
     */
    protected $usr_uid = '';

    /**
     * The value for the note_date field.
     * @var        int
     */
    protected $note_date;

    /**
     * The value for the note_content field.
     * @var        string
     */
    protected $note_content;

    /**
     * The value for the note_type field.
     * @var        string
     */
    protected $note_type = 'USER';

    /**
     * The value for the note_availability field.
     * @var        string
     */
    protected $note_availability = 'PUBLIC';

    /**
     * The value for the note_origin_obj field.
     * @var        string
     */
    protected $note_origin_obj = '';

    /**
     * The value for the note_affected_obj1 field.
     * @var        string
     */
    protected $note_affected_obj1 = '';

    /**
     * The value for the note_affected_obj2 field.
     * @var        string
     */
    protected $note_affected_obj2 = '';

    /**
     * The value for the note_recipients field.
     * @var        string
     */
    protected $note_recipients;

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
     * Get the [usr_uid] column value.
     * 
     * @return     string
     */
    public function getUsrUid()
    {

        return $this->usr_uid;
    }

    /**
     * Get the [optionally formatted] [note_date] column value.
     * 
     * @param      string $format The date/time format string (either date()-style or strftime()-style).
     *                          If format is NULL, then the integer unix timestamp will be returned.
     * @return     mixed Formatted date/time value as string or integer unix timestamp (if format is NULL).
     * @throws     PropelException - if unable to convert the date/time to timestamp.
     */
    public function getNoteDate($format = 'Y-m-d H:i:s')
    {

        if ($this->note_date === null || $this->note_date === '') {
            return null;
        } elseif (!is_int($this->note_date)) {
            // a non-timestamp value was set externally, so we convert it
            $ts = strtotime($this->note_date);
            if ($ts === -1 || $ts === false) {
                throw new PropelException("Unable to parse value of [note_date] as date/time value: " .
                    var_export($this->note_date, true));
            }
        } else {
            $ts = $this->note_date;
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
     * Get the [note_content] column value.
     * 
     * @return     string
     */
    public function getNoteContent()
    {

        return $this->note_content;
    }

    /**
     * Get the [note_type] column value.
     * 
     * @return     string
     */
    public function getNoteType()
    {

        return $this->note_type;
    }

    /**
     * Get the [note_availability] column value.
     * 
     * @return     string
     */
    public function getNoteAvailability()
    {

        return $this->note_availability;
    }

    /**
     * Get the [note_origin_obj] column value.
     * 
     * @return     string
     */
    public function getNoteOriginObj()
    {

        return $this->note_origin_obj;
    }

    /**
     * Get the [note_affected_obj1] column value.
     * 
     * @return     string
     */
    public function getNoteAffectedObj1()
    {

        return $this->note_affected_obj1;
    }

    /**
     * Get the [note_affected_obj2] column value.
     * 
     * @return     string
     */
    public function getNoteAffectedObj2()
    {

        return $this->note_affected_obj2;
    }

    /**
     * Get the [note_recipients] column value.
     * 
     * @return     string
     */
    public function getNoteRecipients()
    {

        return $this->note_recipients;
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
            $this->modifiedColumns[] = AppNotesPeer::APP_UID;
        }

    } // setAppUid()

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
            $this->modifiedColumns[] = AppNotesPeer::USR_UID;
        }

    } // setUsrUid()

    /**
     * Set the value of [note_date] column.
     * 
     * @param      int $v new value
     * @return     void
     */
    public function setNoteDate($v)
    {

        if ($v !== null && !is_int($v)) {
            $ts = strtotime($v);
            if ($ts === -1 || $ts === false) {
                throw new PropelException("Unable to parse date/time value for [note_date] from input: " .
                    var_export($v, true));
            }
        } else {
            $ts = $v;
        }
        if ($this->note_date !== $ts) {
            $this->note_date = $ts;
            $this->modifiedColumns[] = AppNotesPeer::NOTE_DATE;
        }

    } // setNoteDate()

    /**
     * Set the value of [note_content] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setNoteContent($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->note_content !== $v) {
            $this->note_content = $v;
            $this->modifiedColumns[] = AppNotesPeer::NOTE_CONTENT;
        }

    } // setNoteContent()

    /**
     * Set the value of [note_type] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setNoteType($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->note_type !== $v || $v === 'USER') {
            $this->note_type = $v;
            $this->modifiedColumns[] = AppNotesPeer::NOTE_TYPE;
        }

    } // setNoteType()

    /**
     * Set the value of [note_availability] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setNoteAvailability($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->note_availability !== $v || $v === 'PUBLIC') {
            $this->note_availability = $v;
            $this->modifiedColumns[] = AppNotesPeer::NOTE_AVAILABILITY;
        }

    } // setNoteAvailability()

    /**
     * Set the value of [note_origin_obj] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setNoteOriginObj($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->note_origin_obj !== $v || $v === '') {
            $this->note_origin_obj = $v;
            $this->modifiedColumns[] = AppNotesPeer::NOTE_ORIGIN_OBJ;
        }

    } // setNoteOriginObj()

    /**
     * Set the value of [note_affected_obj1] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setNoteAffectedObj1($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->note_affected_obj1 !== $v || $v === '') {
            $this->note_affected_obj1 = $v;
            $this->modifiedColumns[] = AppNotesPeer::NOTE_AFFECTED_OBJ1;
        }

    } // setNoteAffectedObj1()

    /**
     * Set the value of [note_affected_obj2] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setNoteAffectedObj2($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->note_affected_obj2 !== $v || $v === '') {
            $this->note_affected_obj2 = $v;
            $this->modifiedColumns[] = AppNotesPeer::NOTE_AFFECTED_OBJ2;
        }

    } // setNoteAffectedObj2()

    /**
     * Set the value of [note_recipients] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setNoteRecipients($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->note_recipients !== $v) {
            $this->note_recipients = $v;
            $this->modifiedColumns[] = AppNotesPeer::NOTE_RECIPIENTS;
        }

    } // setNoteRecipients()

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

            $this->usr_uid = $rs->getString($startcol + 1);

            $this->note_date = $rs->getTimestamp($startcol + 2, null);

            $this->note_content = $rs->getString($startcol + 3);

            $this->note_type = $rs->getString($startcol + 4);

            $this->note_availability = $rs->getString($startcol + 5);

            $this->note_origin_obj = $rs->getString($startcol + 6);

            $this->note_affected_obj1 = $rs->getString($startcol + 7);

            $this->note_affected_obj2 = $rs->getString($startcol + 8);

            $this->note_recipients = $rs->getString($startcol + 9);

            $this->resetModified();

            $this->setNew(false);

            // FIXME - using NUM_COLUMNS may be clearer.
            return $startcol + 10; // 10 = AppNotesPeer::NUM_COLUMNS - AppNotesPeer::NUM_LAZY_LOAD_COLUMNS).

        } catch (Exception $e) {
            throw new PropelException("Error populating AppNotes object", $e);
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
            $con = Propel::getConnection(AppNotesPeer::DATABASE_NAME);
        }

        try {
            $con->begin();
            AppNotesPeer::doDelete($this, $con);
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
            $con = Propel::getConnection(AppNotesPeer::DATABASE_NAME);
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
                    $pk = AppNotesPeer::doInsert($this, $con);
                    $affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
                                         // should always be true here (even though technically
                                         // BasePeer::doInsert() can insert multiple rows).

                    $this->setNew(false);
                } else {
                    $affectedRows += AppNotesPeer::doUpdate($this, $con);
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


            if (($retval = AppNotesPeer::doValidate($this, $columns)) !== true) {
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
        $pos = AppNotesPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                return $this->getUsrUid();
                break;
            case 2:
                return $this->getNoteDate();
                break;
            case 3:
                return $this->getNoteContent();
                break;
            case 4:
                return $this->getNoteType();
                break;
            case 5:
                return $this->getNoteAvailability();
                break;
            case 6:
                return $this->getNoteOriginObj();
                break;
            case 7:
                return $this->getNoteAffectedObj1();
                break;
            case 8:
                return $this->getNoteAffectedObj2();
                break;
            case 9:
                return $this->getNoteRecipients();
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
        $keys = AppNotesPeer::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getAppUid(),
            $keys[1] => $this->getUsrUid(),
            $keys[2] => $this->getNoteDate(),
            $keys[3] => $this->getNoteContent(),
            $keys[4] => $this->getNoteType(),
            $keys[5] => $this->getNoteAvailability(),
            $keys[6] => $this->getNoteOriginObj(),
            $keys[7] => $this->getNoteAffectedObj1(),
            $keys[8] => $this->getNoteAffectedObj2(),
            $keys[9] => $this->getNoteRecipients(),
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
        $pos = AppNotesPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                $this->setUsrUid($value);
                break;
            case 2:
                $this->setNoteDate($value);
                break;
            case 3:
                $this->setNoteContent($value);
                break;
            case 4:
                $this->setNoteType($value);
                break;
            case 5:
                $this->setNoteAvailability($value);
                break;
            case 6:
                $this->setNoteOriginObj($value);
                break;
            case 7:
                $this->setNoteAffectedObj1($value);
                break;
            case 8:
                $this->setNoteAffectedObj2($value);
                break;
            case 9:
                $this->setNoteRecipients($value);
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
        $keys = AppNotesPeer::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) {
            $this->setAppUid($arr[$keys[0]]);
        }

        if (array_key_exists($keys[1], $arr)) {
            $this->setUsrUid($arr[$keys[1]]);
        }

        if (array_key_exists($keys[2], $arr)) {
            $this->setNoteDate($arr[$keys[2]]);
        }

        if (array_key_exists($keys[3], $arr)) {
            $this->setNoteContent($arr[$keys[3]]);
        }

        if (array_key_exists($keys[4], $arr)) {
            $this->setNoteType($arr[$keys[4]]);
        }

        if (array_key_exists($keys[5], $arr)) {
            $this->setNoteAvailability($arr[$keys[5]]);
        }

        if (array_key_exists($keys[6], $arr)) {
            $this->setNoteOriginObj($arr[$keys[6]]);
        }

        if (array_key_exists($keys[7], $arr)) {
            $this->setNoteAffectedObj1($arr[$keys[7]]);
        }

        if (array_key_exists($keys[8], $arr)) {
            $this->setNoteAffectedObj2($arr[$keys[8]]);
        }

        if (array_key_exists($keys[9], $arr)) {
            $this->setNoteRecipients($arr[$keys[9]]);
        }

    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return     Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(AppNotesPeer::DATABASE_NAME);

        if ($this->isColumnModified(AppNotesPeer::APP_UID)) {
            $criteria->add(AppNotesPeer::APP_UID, $this->app_uid);
        }

        if ($this->isColumnModified(AppNotesPeer::USR_UID)) {
            $criteria->add(AppNotesPeer::USR_UID, $this->usr_uid);
        }

        if ($this->isColumnModified(AppNotesPeer::NOTE_DATE)) {
            $criteria->add(AppNotesPeer::NOTE_DATE, $this->note_date);
        }

        if ($this->isColumnModified(AppNotesPeer::NOTE_CONTENT)) {
            $criteria->add(AppNotesPeer::NOTE_CONTENT, $this->note_content);
        }

        if ($this->isColumnModified(AppNotesPeer::NOTE_TYPE)) {
            $criteria->add(AppNotesPeer::NOTE_TYPE, $this->note_type);
        }

        if ($this->isColumnModified(AppNotesPeer::NOTE_AVAILABILITY)) {
            $criteria->add(AppNotesPeer::NOTE_AVAILABILITY, $this->note_availability);
        }

        if ($this->isColumnModified(AppNotesPeer::NOTE_ORIGIN_OBJ)) {
            $criteria->add(AppNotesPeer::NOTE_ORIGIN_OBJ, $this->note_origin_obj);
        }

        if ($this->isColumnModified(AppNotesPeer::NOTE_AFFECTED_OBJ1)) {
            $criteria->add(AppNotesPeer::NOTE_AFFECTED_OBJ1, $this->note_affected_obj1);
        }

        if ($this->isColumnModified(AppNotesPeer::NOTE_AFFECTED_OBJ2)) {
            $criteria->add(AppNotesPeer::NOTE_AFFECTED_OBJ2, $this->note_affected_obj2);
        }

        if ($this->isColumnModified(AppNotesPeer::NOTE_RECIPIENTS)) {
            $criteria->add(AppNotesPeer::NOTE_RECIPIENTS, $this->note_recipients);
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
        $criteria = new Criteria(AppNotesPeer::DATABASE_NAME);


        return $criteria;
    }

    /**
     * Returns NULL since this table doesn't have a primary key.
     * This method exists only for BC and is deprecated!
     * @return     null
     */
    public function getPrimaryKey()
    {
        return null;
    }

    /**
     * Dummy primary key setter.
     *
     * This function only exists to preserve backwards compatibility.  It is no longer
     * needed or required by the Persistent interface.  It will be removed in next BC-breaking
     * release of Propel.
     *
     * @deprecated
     */
     public function setPrimaryKey($pk)
     {
         // do nothing, because this object doesn't have any primary keys
     }

    /**
     * Sets contents of passed object to values from current object.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param      object $copyObj An object of AppNotes (or compatible) type.
     * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @throws     PropelException
     */
    public function copyInto($copyObj, $deepCopy = false)
    {

        $copyObj->setAppUid($this->app_uid);

        $copyObj->setUsrUid($this->usr_uid);

        $copyObj->setNoteDate($this->note_date);

        $copyObj->setNoteContent($this->note_content);

        $copyObj->setNoteType($this->note_type);

        $copyObj->setNoteAvailability($this->note_availability);

        $copyObj->setNoteOriginObj($this->note_origin_obj);

        $copyObj->setNoteAffectedObj1($this->note_affected_obj1);

        $copyObj->setNoteAffectedObj2($this->note_affected_obj2);

        $copyObj->setNoteRecipients($this->note_recipients);


        $copyObj->setNew(true);

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
     * @return     AppNotes Clone of current object.
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
     * @return     AppNotesPeer
     */
    public function getPeer()
    {
        if (self::$peer === null) {
            self::$peer = new AppNotesPeer();
        }
        return self::$peer;
    }
}

