<?php

require_once 'propel/om/BaseObject.php';

require_once 'propel/om/Persistent.php';


include_once 'propel/util/Criteria.php';

include_once 'classes/model/SwimlanesElementsPeer.php';

/**
 * Base class that represents a row from the 'SWIMLANES_ELEMENTS' table.
 *
 * 
 *
 * @package    workflow.classes.model.om
 */
abstract class BaseSwimlanesElements extends BaseObject implements Persistent
{

    /**
     * The Peer class.
     * Instance provides a convenient way of calling static methods on a class
     * that calling code may not be able to identify.
     * @var        SwimlanesElementsPeer
    */
    protected static $peer;

    /**
     * The value for the swi_uid field.
     * @var        string
     */
    protected $swi_uid = '';

    /**
     * The value for the pro_uid field.
     * @var        string
     */
    protected $pro_uid = '';

    /**
     * The value for the swi_type field.
     * @var        string
     */
    protected $swi_type = 'LINE';

    /**
     * The value for the swi_x field.
     * @var        int
     */
    protected $swi_x = 0;

    /**
     * The value for the swi_y field.
     * @var        int
     */
    protected $swi_y = 0;

    /**
     * The value for the swi_width field.
     * @var        int
     */
    protected $swi_width = 0;

    /**
     * The value for the swi_height field.
     * @var        int
     */
    protected $swi_height = 0;

    /**
     * The value for the swi_next_uid field.
     * @var        string
     */
    protected $swi_next_uid = '';

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
     * Get the [swi_uid] column value.
     * 
     * @return     string
     */
    public function getSwiUid()
    {

        return $this->swi_uid;
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
     * Get the [swi_type] column value.
     * 
     * @return     string
     */
    public function getSwiType()
    {

        return $this->swi_type;
    }

    /**
     * Get the [swi_x] column value.
     * 
     * @return     int
     */
    public function getSwiX()
    {

        return $this->swi_x;
    }

    /**
     * Get the [swi_y] column value.
     * 
     * @return     int
     */
    public function getSwiY()
    {

        return $this->swi_y;
    }

    /**
     * Get the [swi_width] column value.
     * 
     * @return     int
     */
    public function getSwiWidth()
    {

        return $this->swi_width;
    }

    /**
     * Get the [swi_height] column value.
     * 
     * @return     int
     */
    public function getSwiHeight()
    {

        return $this->swi_height;
    }

    /**
     * Get the [swi_next_uid] column value.
     * 
     * @return     string
     */
    public function getSwiNextUid()
    {

        return $this->swi_next_uid;
    }

    /**
     * Set the value of [swi_uid] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setSwiUid($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->swi_uid !== $v || $v === '') {
            $this->swi_uid = $v;
            $this->modifiedColumns[] = SwimlanesElementsPeer::SWI_UID;
        }

    } // setSwiUid()

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
            $this->modifiedColumns[] = SwimlanesElementsPeer::PRO_UID;
        }

    } // setProUid()

    /**
     * Set the value of [swi_type] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setSwiType($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->swi_type !== $v || $v === 'LINE') {
            $this->swi_type = $v;
            $this->modifiedColumns[] = SwimlanesElementsPeer::SWI_TYPE;
        }

    } // setSwiType()

    /**
     * Set the value of [swi_x] column.
     * 
     * @param      int $v new value
     * @return     void
     */
    public function setSwiX($v)
    {

        // Since the native PHP type for this column is integer,
        // we will cast the input value to an int (if it is not).
        if ($v !== null && !is_int($v) && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->swi_x !== $v || $v === 0) {
            $this->swi_x = $v;
            $this->modifiedColumns[] = SwimlanesElementsPeer::SWI_X;
        }

    } // setSwiX()

    /**
     * Set the value of [swi_y] column.
     * 
     * @param      int $v new value
     * @return     void
     */
    public function setSwiY($v)
    {

        // Since the native PHP type for this column is integer,
        // we will cast the input value to an int (if it is not).
        if ($v !== null && !is_int($v) && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->swi_y !== $v || $v === 0) {
            $this->swi_y = $v;
            $this->modifiedColumns[] = SwimlanesElementsPeer::SWI_Y;
        }

    } // setSwiY()

    /**
     * Set the value of [swi_width] column.
     * 
     * @param      int $v new value
     * @return     void
     */
    public function setSwiWidth($v)
    {

        // Since the native PHP type for this column is integer,
        // we will cast the input value to an int (if it is not).
        if ($v !== null && !is_int($v) && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->swi_width !== $v || $v === 0) {
            $this->swi_width = $v;
            $this->modifiedColumns[] = SwimlanesElementsPeer::SWI_WIDTH;
        }

    } // setSwiWidth()

    /**
     * Set the value of [swi_height] column.
     * 
     * @param      int $v new value
     * @return     void
     */
    public function setSwiHeight($v)
    {

        // Since the native PHP type for this column is integer,
        // we will cast the input value to an int (if it is not).
        if ($v !== null && !is_int($v) && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->swi_height !== $v || $v === 0) {
            $this->swi_height = $v;
            $this->modifiedColumns[] = SwimlanesElementsPeer::SWI_HEIGHT;
        }

    } // setSwiHeight()

    /**
     * Set the value of [swi_next_uid] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setSwiNextUid($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->swi_next_uid !== $v || $v === '') {
            $this->swi_next_uid = $v;
            $this->modifiedColumns[] = SwimlanesElementsPeer::SWI_NEXT_UID;
        }

    } // setSwiNextUid()

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

            $this->swi_uid = $rs->getString($startcol + 0);

            $this->pro_uid = $rs->getString($startcol + 1);

            $this->swi_type = $rs->getString($startcol + 2);

            $this->swi_x = $rs->getInt($startcol + 3);

            $this->swi_y = $rs->getInt($startcol + 4);

            $this->swi_width = $rs->getInt($startcol + 5);

            $this->swi_height = $rs->getInt($startcol + 6);

            $this->swi_next_uid = $rs->getString($startcol + 7);

            $this->resetModified();

            $this->setNew(false);

            // FIXME - using NUM_COLUMNS may be clearer.
            return $startcol + 8; // 8 = SwimlanesElementsPeer::NUM_COLUMNS - SwimlanesElementsPeer::NUM_LAZY_LOAD_COLUMNS).

        } catch (Exception $e) {
            throw new PropelException("Error populating SwimlanesElements object", $e);
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
            $con = Propel::getConnection(SwimlanesElementsPeer::DATABASE_NAME);
        }

        try {
            $con->begin();
            SwimlanesElementsPeer::doDelete($this, $con);
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
            $con = Propel::getConnection(SwimlanesElementsPeer::DATABASE_NAME);
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
                    $pk = SwimlanesElementsPeer::doInsert($this, $con);
                    $affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
                                         // should always be true here (even though technically
                                         // BasePeer::doInsert() can insert multiple rows).

                    $this->setNew(false);
                } else {
                    $affectedRows += SwimlanesElementsPeer::doUpdate($this, $con);
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


            if (($retval = SwimlanesElementsPeer::doValidate($this, $columns)) !== true) {
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
        $pos = SwimlanesElementsPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                return $this->getSwiUid();
                break;
            case 1:
                return $this->getProUid();
                break;
            case 2:
                return $this->getSwiType();
                break;
            case 3:
                return $this->getSwiX();
                break;
            case 4:
                return $this->getSwiY();
                break;
            case 5:
                return $this->getSwiWidth();
                break;
            case 6:
                return $this->getSwiHeight();
                break;
            case 7:
                return $this->getSwiNextUid();
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
        $keys = SwimlanesElementsPeer::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getSwiUid(),
            $keys[1] => $this->getProUid(),
            $keys[2] => $this->getSwiType(),
            $keys[3] => $this->getSwiX(),
            $keys[4] => $this->getSwiY(),
            $keys[5] => $this->getSwiWidth(),
            $keys[6] => $this->getSwiHeight(),
            $keys[7] => $this->getSwiNextUid(),
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
        $pos = SwimlanesElementsPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                $this->setSwiUid($value);
                break;
            case 1:
                $this->setProUid($value);
                break;
            case 2:
                $this->setSwiType($value);
                break;
            case 3:
                $this->setSwiX($value);
                break;
            case 4:
                $this->setSwiY($value);
                break;
            case 5:
                $this->setSwiWidth($value);
                break;
            case 6:
                $this->setSwiHeight($value);
                break;
            case 7:
                $this->setSwiNextUid($value);
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
        $keys = SwimlanesElementsPeer::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) {
            $this->setSwiUid($arr[$keys[0]]);
        }

        if (array_key_exists($keys[1], $arr)) {
            $this->setProUid($arr[$keys[1]]);
        }

        if (array_key_exists($keys[2], $arr)) {
            $this->setSwiType($arr[$keys[2]]);
        }

        if (array_key_exists($keys[3], $arr)) {
            $this->setSwiX($arr[$keys[3]]);
        }

        if (array_key_exists($keys[4], $arr)) {
            $this->setSwiY($arr[$keys[4]]);
        }

        if (array_key_exists($keys[5], $arr)) {
            $this->setSwiWidth($arr[$keys[5]]);
        }

        if (array_key_exists($keys[6], $arr)) {
            $this->setSwiHeight($arr[$keys[6]]);
        }

        if (array_key_exists($keys[7], $arr)) {
            $this->setSwiNextUid($arr[$keys[7]]);
        }

    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return     Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(SwimlanesElementsPeer::DATABASE_NAME);

        if ($this->isColumnModified(SwimlanesElementsPeer::SWI_UID)) {
            $criteria->add(SwimlanesElementsPeer::SWI_UID, $this->swi_uid);
        }

        if ($this->isColumnModified(SwimlanesElementsPeer::PRO_UID)) {
            $criteria->add(SwimlanesElementsPeer::PRO_UID, $this->pro_uid);
        }

        if ($this->isColumnModified(SwimlanesElementsPeer::SWI_TYPE)) {
            $criteria->add(SwimlanesElementsPeer::SWI_TYPE, $this->swi_type);
        }

        if ($this->isColumnModified(SwimlanesElementsPeer::SWI_X)) {
            $criteria->add(SwimlanesElementsPeer::SWI_X, $this->swi_x);
        }

        if ($this->isColumnModified(SwimlanesElementsPeer::SWI_Y)) {
            $criteria->add(SwimlanesElementsPeer::SWI_Y, $this->swi_y);
        }

        if ($this->isColumnModified(SwimlanesElementsPeer::SWI_WIDTH)) {
            $criteria->add(SwimlanesElementsPeer::SWI_WIDTH, $this->swi_width);
        }

        if ($this->isColumnModified(SwimlanesElementsPeer::SWI_HEIGHT)) {
            $criteria->add(SwimlanesElementsPeer::SWI_HEIGHT, $this->swi_height);
        }

        if ($this->isColumnModified(SwimlanesElementsPeer::SWI_NEXT_UID)) {
            $criteria->add(SwimlanesElementsPeer::SWI_NEXT_UID, $this->swi_next_uid);
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
        $criteria = new Criteria(SwimlanesElementsPeer::DATABASE_NAME);

        $criteria->add(SwimlanesElementsPeer::SWI_UID, $this->swi_uid);

        return $criteria;
    }

    /**
     * Returns the primary key for this object (row).
     * @return     string
     */
    public function getPrimaryKey()
    {
        return $this->getSwiUid();
    }

    /**
     * Generic method to set the primary key (swi_uid column).
     *
     * @param      string $key Primary key.
     * @return     void
     */
    public function setPrimaryKey($key)
    {
        $this->setSwiUid($key);
    }

    /**
     * Sets contents of passed object to values from current object.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param      object $copyObj An object of SwimlanesElements (or compatible) type.
     * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @throws     PropelException
     */
    public function copyInto($copyObj, $deepCopy = false)
    {

        $copyObj->setProUid($this->pro_uid);

        $copyObj->setSwiType($this->swi_type);

        $copyObj->setSwiX($this->swi_x);

        $copyObj->setSwiY($this->swi_y);

        $copyObj->setSwiWidth($this->swi_width);

        $copyObj->setSwiHeight($this->swi_height);

        $copyObj->setSwiNextUid($this->swi_next_uid);


        $copyObj->setNew(true);

        $copyObj->setSwiUid(''); // this is a pkey column, so set to default value

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
     * @return     SwimlanesElements Clone of current object.
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
     * @return     SwimlanesElementsPeer
     */
    public function getPeer()
    {
        if (self::$peer === null) {
            self::$peer = new SwimlanesElementsPeer();
        }
        return self::$peer;
    }
}

