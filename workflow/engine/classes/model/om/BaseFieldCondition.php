<?php

require_once 'propel/om/BaseObject.php';

require_once 'propel/om/Persistent.php';


include_once 'propel/util/Criteria.php';

include_once 'classes/model/FieldConditionPeer.php';

/**
 * Base class that represents a row from the 'FIELD_CONDITION' table.
 *
 * 
 *
 * @package    workflow.classes.model.om
 */
abstract class BaseFieldCondition extends BaseObject implements Persistent
{

    /**
     * The Peer class.
     * Instance provides a convenient way of calling static methods on a class
     * that calling code may not be able to identify.
     * @var        FieldConditionPeer
    */
    protected static $peer;

    /**
     * The value for the fcd_uid field.
     * @var        string
     */
    protected $fcd_uid = '';

    /**
     * The value for the fcd_function field.
     * @var        string
     */
    protected $fcd_function;

    /**
     * The value for the fcd_fields field.
     * @var        string
     */
    protected $fcd_fields;

    /**
     * The value for the fcd_condition field.
     * @var        string
     */
    protected $fcd_condition;

    /**
     * The value for the fcd_events field.
     * @var        string
     */
    protected $fcd_events;

    /**
     * The value for the fcd_event_owners field.
     * @var        string
     */
    protected $fcd_event_owners;

    /**
     * The value for the fcd_status field.
     * @var        string
     */
    protected $fcd_status;

    /**
     * The value for the fcd_dyn_uid field.
     * @var        string
     */
    protected $fcd_dyn_uid;

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
     * Get the [fcd_uid] column value.
     * 
     * @return     string
     */
    public function getFcdUid()
    {

        return $this->fcd_uid;
    }

    /**
     * Get the [fcd_function] column value.
     * 
     * @return     string
     */
    public function getFcdFunction()
    {

        return $this->fcd_function;
    }

    /**
     * Get the [fcd_fields] column value.
     * 
     * @return     string
     */
    public function getFcdFields()
    {

        return $this->fcd_fields;
    }

    /**
     * Get the [fcd_condition] column value.
     * 
     * @return     string
     */
    public function getFcdCondition()
    {

        return $this->fcd_condition;
    }

    /**
     * Get the [fcd_events] column value.
     * 
     * @return     string
     */
    public function getFcdEvents()
    {

        return $this->fcd_events;
    }

    /**
     * Get the [fcd_event_owners] column value.
     * 
     * @return     string
     */
    public function getFcdEventOwners()
    {

        return $this->fcd_event_owners;
    }

    /**
     * Get the [fcd_status] column value.
     * 
     * @return     string
     */
    public function getFcdStatus()
    {

        return $this->fcd_status;
    }

    /**
     * Get the [fcd_dyn_uid] column value.
     * 
     * @return     string
     */
    public function getFcdDynUid()
    {

        return $this->fcd_dyn_uid;
    }

    /**
     * Set the value of [fcd_uid] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setFcdUid($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->fcd_uid !== $v || $v === '') {
            $this->fcd_uid = $v;
            $this->modifiedColumns[] = FieldConditionPeer::FCD_UID;
        }

    } // setFcdUid()

    /**
     * Set the value of [fcd_function] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setFcdFunction($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->fcd_function !== $v) {
            $this->fcd_function = $v;
            $this->modifiedColumns[] = FieldConditionPeer::FCD_FUNCTION;
        }

    } // setFcdFunction()

    /**
     * Set the value of [fcd_fields] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setFcdFields($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->fcd_fields !== $v) {
            $this->fcd_fields = $v;
            $this->modifiedColumns[] = FieldConditionPeer::FCD_FIELDS;
        }

    } // setFcdFields()

    /**
     * Set the value of [fcd_condition] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setFcdCondition($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->fcd_condition !== $v) {
            $this->fcd_condition = $v;
            $this->modifiedColumns[] = FieldConditionPeer::FCD_CONDITION;
        }

    } // setFcdCondition()

    /**
     * Set the value of [fcd_events] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setFcdEvents($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->fcd_events !== $v) {
            $this->fcd_events = $v;
            $this->modifiedColumns[] = FieldConditionPeer::FCD_EVENTS;
        }

    } // setFcdEvents()

    /**
     * Set the value of [fcd_event_owners] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setFcdEventOwners($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->fcd_event_owners !== $v) {
            $this->fcd_event_owners = $v;
            $this->modifiedColumns[] = FieldConditionPeer::FCD_EVENT_OWNERS;
        }

    } // setFcdEventOwners()

    /**
     * Set the value of [fcd_status] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setFcdStatus($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->fcd_status !== $v) {
            $this->fcd_status = $v;
            $this->modifiedColumns[] = FieldConditionPeer::FCD_STATUS;
        }

    } // setFcdStatus()

    /**
     * Set the value of [fcd_dyn_uid] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setFcdDynUid($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->fcd_dyn_uid !== $v) {
            $this->fcd_dyn_uid = $v;
            $this->modifiedColumns[] = FieldConditionPeer::FCD_DYN_UID;
        }

    } // setFcdDynUid()

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

            $this->fcd_uid = $rs->getString($startcol + 0);

            $this->fcd_function = $rs->getString($startcol + 1);

            $this->fcd_fields = $rs->getString($startcol + 2);

            $this->fcd_condition = $rs->getString($startcol + 3);

            $this->fcd_events = $rs->getString($startcol + 4);

            $this->fcd_event_owners = $rs->getString($startcol + 5);

            $this->fcd_status = $rs->getString($startcol + 6);

            $this->fcd_dyn_uid = $rs->getString($startcol + 7);

            $this->resetModified();

            $this->setNew(false);

            // FIXME - using NUM_COLUMNS may be clearer.
            return $startcol + 8; // 8 = FieldConditionPeer::NUM_COLUMNS - FieldConditionPeer::NUM_LAZY_LOAD_COLUMNS).

        } catch (Exception $e) {
            throw new PropelException("Error populating FieldCondition object", $e);
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
            $con = Propel::getConnection(FieldConditionPeer::DATABASE_NAME);
        }

        try {
            $con->begin();
            FieldConditionPeer::doDelete($this, $con);
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
            $con = Propel::getConnection(FieldConditionPeer::DATABASE_NAME);
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
                    $pk = FieldConditionPeer::doInsert($this, $con);
                    $affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
                                         // should always be true here (even though technically
                                         // BasePeer::doInsert() can insert multiple rows).

                    $this->setNew(false);
                } else {
                    $affectedRows += FieldConditionPeer::doUpdate($this, $con);
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


            if (($retval = FieldConditionPeer::doValidate($this, $columns)) !== true) {
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
        $pos = FieldConditionPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                return $this->getFcdUid();
                break;
            case 1:
                return $this->getFcdFunction();
                break;
            case 2:
                return $this->getFcdFields();
                break;
            case 3:
                return $this->getFcdCondition();
                break;
            case 4:
                return $this->getFcdEvents();
                break;
            case 5:
                return $this->getFcdEventOwners();
                break;
            case 6:
                return $this->getFcdStatus();
                break;
            case 7:
                return $this->getFcdDynUid();
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
        $keys = FieldConditionPeer::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getFcdUid(),
            $keys[1] => $this->getFcdFunction(),
            $keys[2] => $this->getFcdFields(),
            $keys[3] => $this->getFcdCondition(),
            $keys[4] => $this->getFcdEvents(),
            $keys[5] => $this->getFcdEventOwners(),
            $keys[6] => $this->getFcdStatus(),
            $keys[7] => $this->getFcdDynUid(),
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
        $pos = FieldConditionPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                $this->setFcdUid($value);
                break;
            case 1:
                $this->setFcdFunction($value);
                break;
            case 2:
                $this->setFcdFields($value);
                break;
            case 3:
                $this->setFcdCondition($value);
                break;
            case 4:
                $this->setFcdEvents($value);
                break;
            case 5:
                $this->setFcdEventOwners($value);
                break;
            case 6:
                $this->setFcdStatus($value);
                break;
            case 7:
                $this->setFcdDynUid($value);
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
        $keys = FieldConditionPeer::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) {
            $this->setFcdUid($arr[$keys[0]]);
        }

        if (array_key_exists($keys[1], $arr)) {
            $this->setFcdFunction($arr[$keys[1]]);
        }

        if (array_key_exists($keys[2], $arr)) {
            $this->setFcdFields($arr[$keys[2]]);
        }

        if (array_key_exists($keys[3], $arr)) {
            $this->setFcdCondition($arr[$keys[3]]);
        }

        if (array_key_exists($keys[4], $arr)) {
            $this->setFcdEvents($arr[$keys[4]]);
        }

        if (array_key_exists($keys[5], $arr)) {
            $this->setFcdEventOwners($arr[$keys[5]]);
        }

        if (array_key_exists($keys[6], $arr)) {
            $this->setFcdStatus($arr[$keys[6]]);
        }

        if (array_key_exists($keys[7], $arr)) {
            $this->setFcdDynUid($arr[$keys[7]]);
        }

    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return     Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(FieldConditionPeer::DATABASE_NAME);

        if ($this->isColumnModified(FieldConditionPeer::FCD_UID)) {
            $criteria->add(FieldConditionPeer::FCD_UID, $this->fcd_uid);
        }

        if ($this->isColumnModified(FieldConditionPeer::FCD_FUNCTION)) {
            $criteria->add(FieldConditionPeer::FCD_FUNCTION, $this->fcd_function);
        }

        if ($this->isColumnModified(FieldConditionPeer::FCD_FIELDS)) {
            $criteria->add(FieldConditionPeer::FCD_FIELDS, $this->fcd_fields);
        }

        if ($this->isColumnModified(FieldConditionPeer::FCD_CONDITION)) {
            $criteria->add(FieldConditionPeer::FCD_CONDITION, $this->fcd_condition);
        }

        if ($this->isColumnModified(FieldConditionPeer::FCD_EVENTS)) {
            $criteria->add(FieldConditionPeer::FCD_EVENTS, $this->fcd_events);
        }

        if ($this->isColumnModified(FieldConditionPeer::FCD_EVENT_OWNERS)) {
            $criteria->add(FieldConditionPeer::FCD_EVENT_OWNERS, $this->fcd_event_owners);
        }

        if ($this->isColumnModified(FieldConditionPeer::FCD_STATUS)) {
            $criteria->add(FieldConditionPeer::FCD_STATUS, $this->fcd_status);
        }

        if ($this->isColumnModified(FieldConditionPeer::FCD_DYN_UID)) {
            $criteria->add(FieldConditionPeer::FCD_DYN_UID, $this->fcd_dyn_uid);
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
        $criteria = new Criteria(FieldConditionPeer::DATABASE_NAME);

        $criteria->add(FieldConditionPeer::FCD_UID, $this->fcd_uid);

        return $criteria;
    }

    /**
     * Returns the primary key for this object (row).
     * @return     string
     */
    public function getPrimaryKey()
    {
        return $this->getFcdUid();
    }

    /**
     * Generic method to set the primary key (fcd_uid column).
     *
     * @param      string $key Primary key.
     * @return     void
     */
    public function setPrimaryKey($key)
    {
        $this->setFcdUid($key);
    }

    /**
     * Sets contents of passed object to values from current object.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param      object $copyObj An object of FieldCondition (or compatible) type.
     * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @throws     PropelException
     */
    public function copyInto($copyObj, $deepCopy = false)
    {

        $copyObj->setFcdFunction($this->fcd_function);

        $copyObj->setFcdFields($this->fcd_fields);

        $copyObj->setFcdCondition($this->fcd_condition);

        $copyObj->setFcdEvents($this->fcd_events);

        $copyObj->setFcdEventOwners($this->fcd_event_owners);

        $copyObj->setFcdStatus($this->fcd_status);

        $copyObj->setFcdDynUid($this->fcd_dyn_uid);


        $copyObj->setNew(true);

        $copyObj->setFcdUid(''); // this is a pkey column, so set to default value

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
     * @return     FieldCondition Clone of current object.
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
     * @return     FieldConditionPeer
     */
    public function getPeer()
    {
        if (self::$peer === null) {
            self::$peer = new FieldConditionPeer();
        }
        return self::$peer;
    }
}

