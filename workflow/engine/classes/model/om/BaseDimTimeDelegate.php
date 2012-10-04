<?php

require_once 'propel/om/BaseObject.php';

require_once 'propel/om/Persistent.php';


include_once 'propel/util/Criteria.php';

include_once 'classes/model/DimTimeDelegatePeer.php';

/**
 * Base class that represents a row from the 'DIM_TIME_DELEGATE' table.
 *
 * 
 *
 * @package    workflow.classes.model.om
 */
abstract class BaseDimTimeDelegate extends BaseObject implements Persistent
{

    /**
     * The Peer class.
     * Instance provides a convenient way of calling static methods on a class
     * that calling code may not be able to identify.
     * @var        DimTimeDelegatePeer
    */
    protected static $peer;

    /**
     * The value for the time_id field.
     * @var        string
     */
    protected $time_id = '';

    /**
     * The value for the month_id field.
     * @var        int
     */
    protected $month_id = 0;

    /**
     * The value for the qtr_id field.
     * @var        int
     */
    protected $qtr_id = 0;

    /**
     * The value for the year_id field.
     * @var        int
     */
    protected $year_id = 0;

    /**
     * The value for the month_name field.
     * @var        string
     */
    protected $month_name = '0';

    /**
     * The value for the month_desc field.
     * @var        string
     */
    protected $month_desc = '';

    /**
     * The value for the qtr_name field.
     * @var        string
     */
    protected $qtr_name = '';

    /**
     * The value for the qtr_desc field.
     * @var        string
     */
    protected $qtr_desc = '';

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
     * Get the [time_id] column value.
     * 
     * @return     string
     */
    public function getTimeId()
    {

        return $this->time_id;
    }

    /**
     * Get the [month_id] column value.
     * 
     * @return     int
     */
    public function getMonthId()
    {

        return $this->month_id;
    }

    /**
     * Get the [qtr_id] column value.
     * 
     * @return     int
     */
    public function getQtrId()
    {

        return $this->qtr_id;
    }

    /**
     * Get the [year_id] column value.
     * 
     * @return     int
     */
    public function getYearId()
    {

        return $this->year_id;
    }

    /**
     * Get the [month_name] column value.
     * 
     * @return     string
     */
    public function getMonthName()
    {

        return $this->month_name;
    }

    /**
     * Get the [month_desc] column value.
     * 
     * @return     string
     */
    public function getMonthDesc()
    {

        return $this->month_desc;
    }

    /**
     * Get the [qtr_name] column value.
     * 
     * @return     string
     */
    public function getQtrName()
    {

        return $this->qtr_name;
    }

    /**
     * Get the [qtr_desc] column value.
     * 
     * @return     string
     */
    public function getQtrDesc()
    {

        return $this->qtr_desc;
    }

    /**
     * Set the value of [time_id] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setTimeId($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->time_id !== $v || $v === '') {
            $this->time_id = $v;
            $this->modifiedColumns[] = DimTimeDelegatePeer::TIME_ID;
        }

    } // setTimeId()

    /**
     * Set the value of [month_id] column.
     * 
     * @param      int $v new value
     * @return     void
     */
    public function setMonthId($v)
    {

        // Since the native PHP type for this column is integer,
        // we will cast the input value to an int (if it is not).
        if ($v !== null && !is_int($v) && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->month_id !== $v || $v === 0) {
            $this->month_id = $v;
            $this->modifiedColumns[] = DimTimeDelegatePeer::MONTH_ID;
        }

    } // setMonthId()

    /**
     * Set the value of [qtr_id] column.
     * 
     * @param      int $v new value
     * @return     void
     */
    public function setQtrId($v)
    {

        // Since the native PHP type for this column is integer,
        // we will cast the input value to an int (if it is not).
        if ($v !== null && !is_int($v) && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->qtr_id !== $v || $v === 0) {
            $this->qtr_id = $v;
            $this->modifiedColumns[] = DimTimeDelegatePeer::QTR_ID;
        }

    } // setQtrId()

    /**
     * Set the value of [year_id] column.
     * 
     * @param      int $v new value
     * @return     void
     */
    public function setYearId($v)
    {

        // Since the native PHP type for this column is integer,
        // we will cast the input value to an int (if it is not).
        if ($v !== null && !is_int($v) && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->year_id !== $v || $v === 0) {
            $this->year_id = $v;
            $this->modifiedColumns[] = DimTimeDelegatePeer::YEAR_ID;
        }

    } // setYearId()

    /**
     * Set the value of [month_name] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setMonthName($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->month_name !== $v || $v === '0') {
            $this->month_name = $v;
            $this->modifiedColumns[] = DimTimeDelegatePeer::MONTH_NAME;
        }

    } // setMonthName()

    /**
     * Set the value of [month_desc] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setMonthDesc($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->month_desc !== $v || $v === '') {
            $this->month_desc = $v;
            $this->modifiedColumns[] = DimTimeDelegatePeer::MONTH_DESC;
        }

    } // setMonthDesc()

    /**
     * Set the value of [qtr_name] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setQtrName($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->qtr_name !== $v || $v === '') {
            $this->qtr_name = $v;
            $this->modifiedColumns[] = DimTimeDelegatePeer::QTR_NAME;
        }

    } // setQtrName()

    /**
     * Set the value of [qtr_desc] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setQtrDesc($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->qtr_desc !== $v || $v === '') {
            $this->qtr_desc = $v;
            $this->modifiedColumns[] = DimTimeDelegatePeer::QTR_DESC;
        }

    } // setQtrDesc()

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

            $this->time_id = $rs->getString($startcol + 0);

            $this->month_id = $rs->getInt($startcol + 1);

            $this->qtr_id = $rs->getInt($startcol + 2);

            $this->year_id = $rs->getInt($startcol + 3);

            $this->month_name = $rs->getString($startcol + 4);

            $this->month_desc = $rs->getString($startcol + 5);

            $this->qtr_name = $rs->getString($startcol + 6);

            $this->qtr_desc = $rs->getString($startcol + 7);

            $this->resetModified();

            $this->setNew(false);

            // FIXME - using NUM_COLUMNS may be clearer.
            return $startcol + 8; // 8 = DimTimeDelegatePeer::NUM_COLUMNS - DimTimeDelegatePeer::NUM_LAZY_LOAD_COLUMNS).

        } catch (Exception $e) {
            throw new PropelException("Error populating DimTimeDelegate object", $e);
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
            $con = Propel::getConnection(DimTimeDelegatePeer::DATABASE_NAME);
        }

        try {
            $con->begin();
            DimTimeDelegatePeer::doDelete($this, $con);
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
            $con = Propel::getConnection(DimTimeDelegatePeer::DATABASE_NAME);
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
                    $pk = DimTimeDelegatePeer::doInsert($this, $con);
                    $affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
                                         // should always be true here (even though technically
                                         // BasePeer::doInsert() can insert multiple rows).

                    $this->setNew(false);
                } else {
                    $affectedRows += DimTimeDelegatePeer::doUpdate($this, $con);
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


            if (($retval = DimTimeDelegatePeer::doValidate($this, $columns)) !== true) {
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
        $pos = DimTimeDelegatePeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                return $this->getTimeId();
                break;
            case 1:
                return $this->getMonthId();
                break;
            case 2:
                return $this->getQtrId();
                break;
            case 3:
                return $this->getYearId();
                break;
            case 4:
                return $this->getMonthName();
                break;
            case 5:
                return $this->getMonthDesc();
                break;
            case 6:
                return $this->getQtrName();
                break;
            case 7:
                return $this->getQtrDesc();
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
        $keys = DimTimeDelegatePeer::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getTimeId(),
            $keys[1] => $this->getMonthId(),
            $keys[2] => $this->getQtrId(),
            $keys[3] => $this->getYearId(),
            $keys[4] => $this->getMonthName(),
            $keys[5] => $this->getMonthDesc(),
            $keys[6] => $this->getQtrName(),
            $keys[7] => $this->getQtrDesc(),
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
        $pos = DimTimeDelegatePeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                $this->setTimeId($value);
                break;
            case 1:
                $this->setMonthId($value);
                break;
            case 2:
                $this->setQtrId($value);
                break;
            case 3:
                $this->setYearId($value);
                break;
            case 4:
                $this->setMonthName($value);
                break;
            case 5:
                $this->setMonthDesc($value);
                break;
            case 6:
                $this->setQtrName($value);
                break;
            case 7:
                $this->setQtrDesc($value);
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
        $keys = DimTimeDelegatePeer::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) {
            $this->setTimeId($arr[$keys[0]]);
        }

        if (array_key_exists($keys[1], $arr)) {
            $this->setMonthId($arr[$keys[1]]);
        }

        if (array_key_exists($keys[2], $arr)) {
            $this->setQtrId($arr[$keys[2]]);
        }

        if (array_key_exists($keys[3], $arr)) {
            $this->setYearId($arr[$keys[3]]);
        }

        if (array_key_exists($keys[4], $arr)) {
            $this->setMonthName($arr[$keys[4]]);
        }

        if (array_key_exists($keys[5], $arr)) {
            $this->setMonthDesc($arr[$keys[5]]);
        }

        if (array_key_exists($keys[6], $arr)) {
            $this->setQtrName($arr[$keys[6]]);
        }

        if (array_key_exists($keys[7], $arr)) {
            $this->setQtrDesc($arr[$keys[7]]);
        }

    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return     Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(DimTimeDelegatePeer::DATABASE_NAME);

        if ($this->isColumnModified(DimTimeDelegatePeer::TIME_ID)) {
            $criteria->add(DimTimeDelegatePeer::TIME_ID, $this->time_id);
        }

        if ($this->isColumnModified(DimTimeDelegatePeer::MONTH_ID)) {
            $criteria->add(DimTimeDelegatePeer::MONTH_ID, $this->month_id);
        }

        if ($this->isColumnModified(DimTimeDelegatePeer::QTR_ID)) {
            $criteria->add(DimTimeDelegatePeer::QTR_ID, $this->qtr_id);
        }

        if ($this->isColumnModified(DimTimeDelegatePeer::YEAR_ID)) {
            $criteria->add(DimTimeDelegatePeer::YEAR_ID, $this->year_id);
        }

        if ($this->isColumnModified(DimTimeDelegatePeer::MONTH_NAME)) {
            $criteria->add(DimTimeDelegatePeer::MONTH_NAME, $this->month_name);
        }

        if ($this->isColumnModified(DimTimeDelegatePeer::MONTH_DESC)) {
            $criteria->add(DimTimeDelegatePeer::MONTH_DESC, $this->month_desc);
        }

        if ($this->isColumnModified(DimTimeDelegatePeer::QTR_NAME)) {
            $criteria->add(DimTimeDelegatePeer::QTR_NAME, $this->qtr_name);
        }

        if ($this->isColumnModified(DimTimeDelegatePeer::QTR_DESC)) {
            $criteria->add(DimTimeDelegatePeer::QTR_DESC, $this->qtr_desc);
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
        $criteria = new Criteria(DimTimeDelegatePeer::DATABASE_NAME);

        $criteria->add(DimTimeDelegatePeer::TIME_ID, $this->time_id);

        return $criteria;
    }

    /**
     * Returns the primary key for this object (row).
     * @return     string
     */
    public function getPrimaryKey()
    {
        return $this->getTimeId();
    }

    /**
     * Generic method to set the primary key (time_id column).
     *
     * @param      string $key Primary key.
     * @return     void
     */
    public function setPrimaryKey($key)
    {
        $this->setTimeId($key);
    }

    /**
     * Sets contents of passed object to values from current object.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param      object $copyObj An object of DimTimeDelegate (or compatible) type.
     * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @throws     PropelException
     */
    public function copyInto($copyObj, $deepCopy = false)
    {

        $copyObj->setMonthId($this->month_id);

        $copyObj->setQtrId($this->qtr_id);

        $copyObj->setYearId($this->year_id);

        $copyObj->setMonthName($this->month_name);

        $copyObj->setMonthDesc($this->month_desc);

        $copyObj->setQtrName($this->qtr_name);

        $copyObj->setQtrDesc($this->qtr_desc);


        $copyObj->setNew(true);

        $copyObj->setTimeId(''); // this is a pkey column, so set to default value

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
     * @return     DimTimeDelegate Clone of current object.
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
     * @return     DimTimeDelegatePeer
     */
    public function getPeer()
    {
        if (self::$peer === null) {
            self::$peer = new DimTimeDelegatePeer();
        }
        return self::$peer;
    }
}

