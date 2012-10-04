<?php

require_once 'propel/om/BaseObject.php';

require_once 'propel/om/Persistent.php';


include_once 'propel/util/Criteria.php';

include_once 'classes/model/CalendarDefinitionPeer.php';

/**
 * Base class that represents a row from the 'CALENDAR_DEFINITION' table.
 *
 * 
 *
 * @package    workflow.classes.model.om
 */
abstract class BaseCalendarDefinition extends BaseObject implements Persistent
{

    /**
     * The Peer class.
     * Instance provides a convenient way of calling static methods on a class
     * that calling code may not be able to identify.
     * @var        CalendarDefinitionPeer
    */
    protected static $peer;

    /**
     * The value for the calendar_uid field.
     * @var        string
     */
    protected $calendar_uid = '';

    /**
     * The value for the calendar_name field.
     * @var        string
     */
    protected $calendar_name = '';

    /**
     * The value for the calendar_create_date field.
     * @var        int
     */
    protected $calendar_create_date;

    /**
     * The value for the calendar_update_date field.
     * @var        int
     */
    protected $calendar_update_date;

    /**
     * The value for the calendar_work_days field.
     * @var        string
     */
    protected $calendar_work_days = '';

    /**
     * The value for the calendar_description field.
     * @var        string
     */
    protected $calendar_description;

    /**
     * The value for the calendar_status field.
     * @var        string
     */
    protected $calendar_status = 'ACTIVE';

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
     * Get the [calendar_uid] column value.
     * 
     * @return     string
     */
    public function getCalendarUid()
    {

        return $this->calendar_uid;
    }

    /**
     * Get the [calendar_name] column value.
     * 
     * @return     string
     */
    public function getCalendarName()
    {

        return $this->calendar_name;
    }

    /**
     * Get the [optionally formatted] [calendar_create_date] column value.
     * 
     * @param      string $format The date/time format string (either date()-style or strftime()-style).
     *                          If format is NULL, then the integer unix timestamp will be returned.
     * @return     mixed Formatted date/time value as string or integer unix timestamp (if format is NULL).
     * @throws     PropelException - if unable to convert the date/time to timestamp.
     */
    public function getCalendarCreateDate($format = 'Y-m-d H:i:s')
    {

        if ($this->calendar_create_date === null || $this->calendar_create_date === '') {
            return null;
        } elseif (!is_int($this->calendar_create_date)) {
            // a non-timestamp value was set externally, so we convert it
            $ts = strtotime($this->calendar_create_date);
            if ($ts === -1 || $ts === false) {
                throw new PropelException("Unable to parse value of [calendar_create_date] as date/time value: " .
                    var_export($this->calendar_create_date, true));
            }
        } else {
            $ts = $this->calendar_create_date;
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
     * Get the [optionally formatted] [calendar_update_date] column value.
     * 
     * @param      string $format The date/time format string (either date()-style or strftime()-style).
     *                          If format is NULL, then the integer unix timestamp will be returned.
     * @return     mixed Formatted date/time value as string or integer unix timestamp (if format is NULL).
     * @throws     PropelException - if unable to convert the date/time to timestamp.
     */
    public function getCalendarUpdateDate($format = 'Y-m-d H:i:s')
    {

        if ($this->calendar_update_date === null || $this->calendar_update_date === '') {
            return null;
        } elseif (!is_int($this->calendar_update_date)) {
            // a non-timestamp value was set externally, so we convert it
            $ts = strtotime($this->calendar_update_date);
            if ($ts === -1 || $ts === false) {
                throw new PropelException("Unable to parse value of [calendar_update_date] as date/time value: " .
                    var_export($this->calendar_update_date, true));
            }
        } else {
            $ts = $this->calendar_update_date;
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
     * Get the [calendar_work_days] column value.
     * 
     * @return     string
     */
    public function getCalendarWorkDays()
    {

        return $this->calendar_work_days;
    }

    /**
     * Get the [calendar_description] column value.
     * 
     * @return     string
     */
    public function getCalendarDescription()
    {

        return $this->calendar_description;
    }

    /**
     * Get the [calendar_status] column value.
     * 
     * @return     string
     */
    public function getCalendarStatus()
    {

        return $this->calendar_status;
    }

    /**
     * Set the value of [calendar_uid] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setCalendarUid($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->calendar_uid !== $v || $v === '') {
            $this->calendar_uid = $v;
            $this->modifiedColumns[] = CalendarDefinitionPeer::CALENDAR_UID;
        }

    } // setCalendarUid()

    /**
     * Set the value of [calendar_name] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setCalendarName($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->calendar_name !== $v || $v === '') {
            $this->calendar_name = $v;
            $this->modifiedColumns[] = CalendarDefinitionPeer::CALENDAR_NAME;
        }

    } // setCalendarName()

    /**
     * Set the value of [calendar_create_date] column.
     * 
     * @param      int $v new value
     * @return     void
     */
    public function setCalendarCreateDate($v)
    {

        if ($v !== null && !is_int($v)) {
            $ts = strtotime($v);
            if ($ts === -1 || $ts === false) {
                throw new PropelException("Unable to parse date/time value for [calendar_create_date] from input: " .
                    var_export($v, true));
            }
        } else {
            $ts = $v;
        }
        if ($this->calendar_create_date !== $ts) {
            $this->calendar_create_date = $ts;
            $this->modifiedColumns[] = CalendarDefinitionPeer::CALENDAR_CREATE_DATE;
        }

    } // setCalendarCreateDate()

    /**
     * Set the value of [calendar_update_date] column.
     * 
     * @param      int $v new value
     * @return     void
     */
    public function setCalendarUpdateDate($v)
    {

        if ($v !== null && !is_int($v)) {
            $ts = strtotime($v);
            if ($ts === -1 || $ts === false) {
                throw new PropelException("Unable to parse date/time value for [calendar_update_date] from input: " .
                    var_export($v, true));
            }
        } else {
            $ts = $v;
        }
        if ($this->calendar_update_date !== $ts) {
            $this->calendar_update_date = $ts;
            $this->modifiedColumns[] = CalendarDefinitionPeer::CALENDAR_UPDATE_DATE;
        }

    } // setCalendarUpdateDate()

    /**
     * Set the value of [calendar_work_days] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setCalendarWorkDays($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->calendar_work_days !== $v || $v === '') {
            $this->calendar_work_days = $v;
            $this->modifiedColumns[] = CalendarDefinitionPeer::CALENDAR_WORK_DAYS;
        }

    } // setCalendarWorkDays()

    /**
     * Set the value of [calendar_description] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setCalendarDescription($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->calendar_description !== $v) {
            $this->calendar_description = $v;
            $this->modifiedColumns[] = CalendarDefinitionPeer::CALENDAR_DESCRIPTION;
        }

    } // setCalendarDescription()

    /**
     * Set the value of [calendar_status] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setCalendarStatus($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->calendar_status !== $v || $v === 'ACTIVE') {
            $this->calendar_status = $v;
            $this->modifiedColumns[] = CalendarDefinitionPeer::CALENDAR_STATUS;
        }

    } // setCalendarStatus()

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

            $this->calendar_uid = $rs->getString($startcol + 0);

            $this->calendar_name = $rs->getString($startcol + 1);

            $this->calendar_create_date = $rs->getTimestamp($startcol + 2, null);

            $this->calendar_update_date = $rs->getTimestamp($startcol + 3, null);

            $this->calendar_work_days = $rs->getString($startcol + 4);

            $this->calendar_description = $rs->getString($startcol + 5);

            $this->calendar_status = $rs->getString($startcol + 6);

            $this->resetModified();

            $this->setNew(false);

            // FIXME - using NUM_COLUMNS may be clearer.
            return $startcol + 7; // 7 = CalendarDefinitionPeer::NUM_COLUMNS - CalendarDefinitionPeer::NUM_LAZY_LOAD_COLUMNS).

        } catch (Exception $e) {
            throw new PropelException("Error populating CalendarDefinition object", $e);
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
            $con = Propel::getConnection(CalendarDefinitionPeer::DATABASE_NAME);
        }

        try {
            $con->begin();
            CalendarDefinitionPeer::doDelete($this, $con);
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
            $con = Propel::getConnection(CalendarDefinitionPeer::DATABASE_NAME);
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
                    $pk = CalendarDefinitionPeer::doInsert($this, $con);
                    $affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
                                         // should always be true here (even though technically
                                         // BasePeer::doInsert() can insert multiple rows).

                    $this->setNew(false);
                } else {
                    $affectedRows += CalendarDefinitionPeer::doUpdate($this, $con);
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


            if (($retval = CalendarDefinitionPeer::doValidate($this, $columns)) !== true) {
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
        $pos = CalendarDefinitionPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                return $this->getCalendarUid();
                break;
            case 1:
                return $this->getCalendarName();
                break;
            case 2:
                return $this->getCalendarCreateDate();
                break;
            case 3:
                return $this->getCalendarUpdateDate();
                break;
            case 4:
                return $this->getCalendarWorkDays();
                break;
            case 5:
                return $this->getCalendarDescription();
                break;
            case 6:
                return $this->getCalendarStatus();
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
        $keys = CalendarDefinitionPeer::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getCalendarUid(),
            $keys[1] => $this->getCalendarName(),
            $keys[2] => $this->getCalendarCreateDate(),
            $keys[3] => $this->getCalendarUpdateDate(),
            $keys[4] => $this->getCalendarWorkDays(),
            $keys[5] => $this->getCalendarDescription(),
            $keys[6] => $this->getCalendarStatus(),
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
        $pos = CalendarDefinitionPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                $this->setCalendarUid($value);
                break;
            case 1:
                $this->setCalendarName($value);
                break;
            case 2:
                $this->setCalendarCreateDate($value);
                break;
            case 3:
                $this->setCalendarUpdateDate($value);
                break;
            case 4:
                $this->setCalendarWorkDays($value);
                break;
            case 5:
                $this->setCalendarDescription($value);
                break;
            case 6:
                $this->setCalendarStatus($value);
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
        $keys = CalendarDefinitionPeer::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) {
            $this->setCalendarUid($arr[$keys[0]]);
        }

        if (array_key_exists($keys[1], $arr)) {
            $this->setCalendarName($arr[$keys[1]]);
        }

        if (array_key_exists($keys[2], $arr)) {
            $this->setCalendarCreateDate($arr[$keys[2]]);
        }

        if (array_key_exists($keys[3], $arr)) {
            $this->setCalendarUpdateDate($arr[$keys[3]]);
        }

        if (array_key_exists($keys[4], $arr)) {
            $this->setCalendarWorkDays($arr[$keys[4]]);
        }

        if (array_key_exists($keys[5], $arr)) {
            $this->setCalendarDescription($arr[$keys[5]]);
        }

        if (array_key_exists($keys[6], $arr)) {
            $this->setCalendarStatus($arr[$keys[6]]);
        }

    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return     Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(CalendarDefinitionPeer::DATABASE_NAME);

        if ($this->isColumnModified(CalendarDefinitionPeer::CALENDAR_UID)) {
            $criteria->add(CalendarDefinitionPeer::CALENDAR_UID, $this->calendar_uid);
        }

        if ($this->isColumnModified(CalendarDefinitionPeer::CALENDAR_NAME)) {
            $criteria->add(CalendarDefinitionPeer::CALENDAR_NAME, $this->calendar_name);
        }

        if ($this->isColumnModified(CalendarDefinitionPeer::CALENDAR_CREATE_DATE)) {
            $criteria->add(CalendarDefinitionPeer::CALENDAR_CREATE_DATE, $this->calendar_create_date);
        }

        if ($this->isColumnModified(CalendarDefinitionPeer::CALENDAR_UPDATE_DATE)) {
            $criteria->add(CalendarDefinitionPeer::CALENDAR_UPDATE_DATE, $this->calendar_update_date);
        }

        if ($this->isColumnModified(CalendarDefinitionPeer::CALENDAR_WORK_DAYS)) {
            $criteria->add(CalendarDefinitionPeer::CALENDAR_WORK_DAYS, $this->calendar_work_days);
        }

        if ($this->isColumnModified(CalendarDefinitionPeer::CALENDAR_DESCRIPTION)) {
            $criteria->add(CalendarDefinitionPeer::CALENDAR_DESCRIPTION, $this->calendar_description);
        }

        if ($this->isColumnModified(CalendarDefinitionPeer::CALENDAR_STATUS)) {
            $criteria->add(CalendarDefinitionPeer::CALENDAR_STATUS, $this->calendar_status);
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
        $criteria = new Criteria(CalendarDefinitionPeer::DATABASE_NAME);

        $criteria->add(CalendarDefinitionPeer::CALENDAR_UID, $this->calendar_uid);

        return $criteria;
    }

    /**
     * Returns the primary key for this object (row).
     * @return     string
     */
    public function getPrimaryKey()
    {
        return $this->getCalendarUid();
    }

    /**
     * Generic method to set the primary key (calendar_uid column).
     *
     * @param      string $key Primary key.
     * @return     void
     */
    public function setPrimaryKey($key)
    {
        $this->setCalendarUid($key);
    }

    /**
     * Sets contents of passed object to values from current object.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param      object $copyObj An object of CalendarDefinition (or compatible) type.
     * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @throws     PropelException
     */
    public function copyInto($copyObj, $deepCopy = false)
    {

        $copyObj->setCalendarName($this->calendar_name);

        $copyObj->setCalendarCreateDate($this->calendar_create_date);

        $copyObj->setCalendarUpdateDate($this->calendar_update_date);

        $copyObj->setCalendarWorkDays($this->calendar_work_days);

        $copyObj->setCalendarDescription($this->calendar_description);

        $copyObj->setCalendarStatus($this->calendar_status);


        $copyObj->setNew(true);

        $copyObj->setCalendarUid(''); // this is a pkey column, so set to default value

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
     * @return     CalendarDefinition Clone of current object.
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
     * @return     CalendarDefinitionPeer
     */
    public function getPeer()
    {
        if (self::$peer === null) {
            self::$peer = new CalendarDefinitionPeer();
        }
        return self::$peer;
    }
}

