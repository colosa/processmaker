<?php
require_once 'propel/om/BaseObject.php';
require_once 'propel/om/Persistent.php';

include_once 'propel/util/Criteria.php';
include_once 'classes/model/LanguagePeer.php';

/**
 * Base class that represents a row from the 'LANGUAGE' table.
 *
 * @package    workflow.classes.model.om
 */
abstract class BaseLanguage extends BaseObject implements Persistent
{

    /**
     * The Peer class.
     * Instance provides a convenient way of calling static methods on a class
     * that calling code may not be able to identify.
     * @var        LanguagePeer
    */
    protected static $peer;

    /**
     * The value for the lan_id field.
     * @var        string
     */
    protected $lan_id = '';

    /**
     * The value for the lan_location field.
     * @var        string
     */
    protected $lan_location = '';

    /**
     * The value for the lan_name field.
     * @var        string
     */
    protected $lan_name = '';

    /**
     * The value for the lan_native_name field.
     * @var        string
     */
    protected $lan_native_name = '';

    /**
     * The value for the lan_direction field.
     * @var        string
     */
    protected $lan_direction = 'L';

    /**
     * The value for the lan_weight field.
     * @var        int
     */
    protected $lan_weight = 0;

    /**
     * The value for the lan_enabled field.
     * @var        string
     */
    protected $lan_enabled = '1';

    /**
     * The value for the lan_calendar field.
     * @var        string
     */
    protected $lan_calendar = 'GREGORIAN';

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
     * Get the [lan_id] column value.
     * 
     * @return     string
     */
    public function getLanId()
    {

        return $this->lan_id;
    }

    /**
     * Get the [lan_location] column value.
     * 
     * @return     string
     */
    public function getLanLocation()
    {

        return $this->lan_location;
    }

    /**
     * Get the [lan_name] column value.
     * 
     * @return     string
     */
    public function getLanName()
    {

        return $this->lan_name;
    }

    /**
     * Get the [lan_native_name] column value.
     * 
     * @return     string
     */
    public function getLanNativeName()
    {

        return $this->lan_native_name;
    }

    /**
     * Get the [lan_direction] column value.
     * 
     * @return     string
     */
    public function getLanDirection()
    {

        return $this->lan_direction;
    }

    /**
     * Get the [lan_weight] column value.
     * 
     * @return     int
     */
    public function getLanWeight()
    {

        return $this->lan_weight;
    }

    /**
     * Get the [lan_enabled] column value.
     * 
     * @return     string
     */
    public function getLanEnabled()
    {

        return $this->lan_enabled;
    }

    /**
     * Get the [lan_calendar] column value.
     * 
     * @return     string
     */
    public function getLanCalendar()
    {

        return $this->lan_calendar;
    }

    /**
     * Set the value of [lan_id] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setLanId($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->lan_id !== $v || $v === '') {
            $this->lan_id = $v;
            $this->modifiedColumns[] = LanguagePeer::LAN_ID;
        }

    } // setLanId()

    /**
     * Set the value of [lan_location] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setLanLocation($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->lan_location !== $v || $v === '') {
            $this->lan_location = $v;
            $this->modifiedColumns[] = LanguagePeer::LAN_LOCATION;
        }

    } // setLanLocation()

    /**
     * Set the value of [lan_name] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setLanName($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->lan_name !== $v || $v === '') {
            $this->lan_name = $v;
            $this->modifiedColumns[] = LanguagePeer::LAN_NAME;
        }

    } // setLanName()

    /**
     * Set the value of [lan_native_name] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setLanNativeName($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->lan_native_name !== $v || $v === '') {
            $this->lan_native_name = $v;
            $this->modifiedColumns[] = LanguagePeer::LAN_NATIVE_NAME;
        }

    } // setLanNativeName()

    /**
     * Set the value of [lan_direction] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setLanDirection($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->lan_direction !== $v || $v === 'L') {
            $this->lan_direction = $v;
            $this->modifiedColumns[] = LanguagePeer::LAN_DIRECTION;
        }

    } // setLanDirection()

    /**
     * Set the value of [lan_weight] column.
     * 
     * @param      int $v new value
     * @return     void
     */
    public function setLanWeight($v)
    {

        // Since the native PHP type for this column is integer,
        // we will cast the input value to an int (if it is not).
        if ($v !== null && !is_int($v) && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->lan_weight !== $v || $v === 0) {
            $this->lan_weight = $v;
            $this->modifiedColumns[] = LanguagePeer::LAN_WEIGHT;
        }

    } // setLanWeight()

    /**
     * Set the value of [lan_enabled] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setLanEnabled($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->lan_enabled !== $v || $v === '1') {
            $this->lan_enabled = $v;
            $this->modifiedColumns[] = LanguagePeer::LAN_ENABLED;
        }

    } // setLanEnabled()

    /**
     * Set the value of [lan_calendar] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setLanCalendar($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->lan_calendar !== $v || $v === 'GREGORIAN') {
            $this->lan_calendar = $v;
            $this->modifiedColumns[] = LanguagePeer::LAN_CALENDAR;
        }

    } // setLanCalendar()

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

            $this->lan_id = $rs->getString($startcol + 0);

            $this->lan_location = $rs->getString($startcol + 1);

            $this->lan_name = $rs->getString($startcol + 2);

            $this->lan_native_name = $rs->getString($startcol + 3);

            $this->lan_direction = $rs->getString($startcol + 4);

            $this->lan_weight = $rs->getInt($startcol + 5);

            $this->lan_enabled = $rs->getString($startcol + 6);

            $this->lan_calendar = $rs->getString($startcol + 7);

            $this->resetModified();

            $this->setNew(false);

            // FIXME - using NUM_COLUMNS may be clearer.
            return $startcol + 7; // 7 = LanguagePeer::NUM_COLUMNS - LanguagePeer::NUM_LAZY_LOAD_COLUMNS).

        } catch (Exception $e) {
            throw new PropelException("Error populating Language object", $e);
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
            $con = Propel::getConnection(LanguagePeer::DATABASE_NAME);
        }

        try {
            $con->begin();
            LanguagePeer::doDelete($this, $con);
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
            $con = Propel::getConnection(LanguagePeer::DATABASE_NAME);
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
                    $pk = LanguagePeer::doInsert($this, $con);
                    $affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
                                         // should always be true here (even though technically
                                         // BasePeer::doInsert() can insert multiple rows).

                    $this->setNew(false);
                } else {
                    $affectedRows += LanguagePeer::doUpdate($this, $con);
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


            if (($retval = LanguagePeer::doValidate($this, $columns)) !== true) {
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
        $pos = LanguagePeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                return $this->getLanId();
                break;
            case 1:
                return $this->getLanLocation();
                break;
            case 2:
                return $this->getLanName();
                break;
            case 3:
                return $this->getLanNativeName();
                break;
            case 4:
                return $this->getLanDirection();
                break;
            case 5:
                return $this->getLanWeight();
                break;
            case 6:
                return $this->getLanEnabled();
                break;
            case 7:
                return $this->getLanCalendar();
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
        $keys = LanguagePeer::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getLanId(),
            $keys[1] => $this->getLanLocation(),
            $keys[2] => $this->getLanName(),
            $keys[3] => $this->getLanNativeName(),
            $keys[4] => $this->getLanDirection(),
            $keys[5] => $this->getLanWeight(),
            $keys[6] => $this->getLanEnabled(),
            $keys[7] => $this->getLanCalendar(),
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
        $pos = LanguagePeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                $this->setLanId($value);
                break;
            case 1:
                $this->setLanLocation($value);
                break;
            case 2:
                $this->setLanName($value);
                break;
            case 3:
                $this->setLanNativeName($value);
                break;
            case 4:
                $this->setLanDirection($value);
                break;
            case 5:
                $this->setLanWeight($value);
                break;
            case 6:
                $this->setLanEnabled($value);
                break;
            case 7:
                $this->setLanCalendar($value);
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
        $keys = LanguagePeer::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) {
            $this->setLanId($arr[$keys[0]]);
        }

        if (array_key_exists($keys[1], $arr)) {
            $this->setLanName($arr[$keys[1]]);
        }

        if (array_key_exists($keys[2], $arr)) {
            $this->setLanNativeName($arr[$keys[2]]);
        }

        if (array_key_exists($keys[3], $arr)) {
            $this->setLanDirection($arr[$keys[3]]);
        }

        if (array_key_exists($keys[4], $arr)) {
            $this->setLanWeight($arr[$keys[4]]);
        }

        if (array_key_exists($keys[5], $arr)) {
            $this->setLanEnabled($arr[$keys[5]]);
        }

        if (array_key_exists($keys[6], $arr)) {
            $this->setLanCalendar($arr[$keys[6]]);
        }

        if (array_key_exists($keys[7], $arr)) {
            $this->setLanCalendar($arr[$keys[7]]);
        }

    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return     Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(LanguagePeer::DATABASE_NAME);

        if ($this->isColumnModified(LanguagePeer::LAN_ID)) {
            $criteria->add(LanguagePeer::LAN_ID, $this->lan_id);
        }

        if ($this->isColumnModified(LanguagePeer::LAN_LOCATION)) {
            $criteria->add(LanguagePeer::LAN_ID, $this->lan_location);
        }

        if ($this->isColumnModified(LanguagePeer::LAN_NAME)) {
            $criteria->add(LanguagePeer::LAN_NAME, $this->lan_name);
        }

        if ($this->isColumnModified(LanguagePeer::LAN_NATIVE_NAME)) {
            $criteria->add(LanguagePeer::LAN_NATIVE_NAME, $this->lan_native_name);
        }

        if ($this->isColumnModified(LanguagePeer::LAN_DIRECTION)) {
            $criteria->add(LanguagePeer::LAN_DIRECTION, $this->lan_direction);
        }

        if ($this->isColumnModified(LanguagePeer::LAN_WEIGHT)) {
            $criteria->add(LanguagePeer::LAN_WEIGHT, $this->lan_weight);
        }

        if ($this->isColumnModified(LanguagePeer::LAN_ENABLED)) {
            $criteria->add(LanguagePeer::LAN_ENABLED, $this->lan_enabled);
        }

        if ($this->isColumnModified(LanguagePeer::LAN_CALENDAR)) {
            $criteria->add(LanguagePeer::LAN_CALENDAR, $this->lan_calendar);
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
        $criteria = new Criteria(LanguagePeer::DATABASE_NAME);

        $criteria->add(LanguagePeer::LAN_ID, $this->lan_id);

        return $criteria;
    }

    /**
     * Returns the primary key for this object (row).
     * @return     string
     */
    public function getPrimaryKey()
    {
        return $this->getLanId();
    }

    /**
     * Generic method to set the primary key (lan_id column).
     *
     * @param      string $key Primary key.
     * @return     void
     */
    public function setPrimaryKey($key)
    {
        $this->setLanId($key);
    }

    /**
     * Sets contents of passed object to values from current object.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param      object $copyObj An object of Language (or compatible) type.
     * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @throws     PropelException
     */
    public function copyInto($copyObj, $deepCopy = false)
    {

        $copyObj->setLanName($this->lan_location);

        $copyObj->setLanName($this->lan_name);

        $copyObj->setLanNativeName($this->lan_native_name);

        $copyObj->setLanDirection($this->lan_direction);

        $copyObj->setLanWeight($this->lan_weight);

        $copyObj->setLanEnabled($this->lan_enabled);

        $copyObj->setLanCalendar($this->lan_calendar);


        $copyObj->setNew(true);

        $copyObj->setLanId(''); // this is a pkey column, so set to default value

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
     * @return     Language Clone of current object.
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
     * @return     LanguagePeer
     */
    public function getPeer()
    {
        if (self::$peer === null) {
            self::$peer = new LanguagePeer();
        }
        return self::$peer;
    }
}

