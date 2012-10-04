<?php

require_once 'propel/om/BaseObject.php';

require_once 'propel/om/Persistent.php';


include_once 'propel/util/Criteria.php';

include_once 'classes/model/GatewayPeer.php';

/**
 * Base class that represents a row from the 'GATEWAY' table.
 *
 * 
 *
 * @package    workflow.classes.model.om
 */
abstract class BaseGateway extends BaseObject implements Persistent
{

    /**
     * The Peer class.
     * Instance provides a convenient way of calling static methods on a class
     * that calling code may not be able to identify.
     * @var        GatewayPeer
    */
    protected static $peer;

    /**
     * The value for the gat_uid field.
     * @var        string
     */
    protected $gat_uid = '';

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
     * The value for the gat_next_task field.
     * @var        string
     */
    protected $gat_next_task = '';

    /**
     * The value for the gat_x field.
     * @var        int
     */
    protected $gat_x = 0;

    /**
     * The value for the gat_y field.
     * @var        int
     */
    protected $gat_y = 0;

    /**
     * The value for the gat_type field.
     * @var        string
     */
    protected $gat_type = '';

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
     * Get the [gat_uid] column value.
     * 
     * @return     string
     */
    public function getGatUid()
    {

        return $this->gat_uid;
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
     * Get the [gat_next_task] column value.
     * 
     * @return     string
     */
    public function getGatNextTask()
    {

        return $this->gat_next_task;
    }

    /**
     * Get the [gat_x] column value.
     * 
     * @return     int
     */
    public function getGatX()
    {

        return $this->gat_x;
    }

    /**
     * Get the [gat_y] column value.
     * 
     * @return     int
     */
    public function getGatY()
    {

        return $this->gat_y;
    }

    /**
     * Get the [gat_type] column value.
     * 
     * @return     string
     */
    public function getGatType()
    {

        return $this->gat_type;
    }

    /**
     * Set the value of [gat_uid] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setGatUid($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->gat_uid !== $v || $v === '') {
            $this->gat_uid = $v;
            $this->modifiedColumns[] = GatewayPeer::GAT_UID;
        }

    } // setGatUid()

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
            $this->modifiedColumns[] = GatewayPeer::PRO_UID;
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
            $this->modifiedColumns[] = GatewayPeer::TAS_UID;
        }

    } // setTasUid()

    /**
     * Set the value of [gat_next_task] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setGatNextTask($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->gat_next_task !== $v || $v === '') {
            $this->gat_next_task = $v;
            $this->modifiedColumns[] = GatewayPeer::GAT_NEXT_TASK;
        }

    } // setGatNextTask()

    /**
     * Set the value of [gat_x] column.
     * 
     * @param      int $v new value
     * @return     void
     */
    public function setGatX($v)
    {

        // Since the native PHP type for this column is integer,
        // we will cast the input value to an int (if it is not).
        if ($v !== null && !is_int($v) && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->gat_x !== $v || $v === 0) {
            $this->gat_x = $v;
            $this->modifiedColumns[] = GatewayPeer::GAT_X;
        }

    } // setGatX()

    /**
     * Set the value of [gat_y] column.
     * 
     * @param      int $v new value
     * @return     void
     */
    public function setGatY($v)
    {

        // Since the native PHP type for this column is integer,
        // we will cast the input value to an int (if it is not).
        if ($v !== null && !is_int($v) && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->gat_y !== $v || $v === 0) {
            $this->gat_y = $v;
            $this->modifiedColumns[] = GatewayPeer::GAT_Y;
        }

    } // setGatY()

    /**
     * Set the value of [gat_type] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setGatType($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->gat_type !== $v || $v === '') {
            $this->gat_type = $v;
            $this->modifiedColumns[] = GatewayPeer::GAT_TYPE;
        }

    } // setGatType()

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

            $this->gat_uid = $rs->getString($startcol + 0);

            $this->pro_uid = $rs->getString($startcol + 1);

            $this->tas_uid = $rs->getString($startcol + 2);

            $this->gat_next_task = $rs->getString($startcol + 3);

            $this->gat_x = $rs->getInt($startcol + 4);

            $this->gat_y = $rs->getInt($startcol + 5);

            $this->gat_type = $rs->getString($startcol + 6);

            $this->resetModified();

            $this->setNew(false);

            // FIXME - using NUM_COLUMNS may be clearer.
            return $startcol + 7; // 7 = GatewayPeer::NUM_COLUMNS - GatewayPeer::NUM_LAZY_LOAD_COLUMNS).

        } catch (Exception $e) {
            throw new PropelException("Error populating Gateway object", $e);
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
            $con = Propel::getConnection(GatewayPeer::DATABASE_NAME);
        }

        try {
            $con->begin();
            GatewayPeer::doDelete($this, $con);
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
            $con = Propel::getConnection(GatewayPeer::DATABASE_NAME);
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
                    $pk = GatewayPeer::doInsert($this, $con);
                    $affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
                                         // should always be true here (even though technically
                                         // BasePeer::doInsert() can insert multiple rows).

                    $this->setNew(false);
                } else {
                    $affectedRows += GatewayPeer::doUpdate($this, $con);
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


            if (($retval = GatewayPeer::doValidate($this, $columns)) !== true) {
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
        $pos = GatewayPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                return $this->getGatUid();
                break;
            case 1:
                return $this->getProUid();
                break;
            case 2:
                return $this->getTasUid();
                break;
            case 3:
                return $this->getGatNextTask();
                break;
            case 4:
                return $this->getGatX();
                break;
            case 5:
                return $this->getGatY();
                break;
            case 6:
                return $this->getGatType();
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
        $keys = GatewayPeer::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getGatUid(),
            $keys[1] => $this->getProUid(),
            $keys[2] => $this->getTasUid(),
            $keys[3] => $this->getGatNextTask(),
            $keys[4] => $this->getGatX(),
            $keys[5] => $this->getGatY(),
            $keys[6] => $this->getGatType(),
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
        $pos = GatewayPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                $this->setGatUid($value);
                break;
            case 1:
                $this->setProUid($value);
                break;
            case 2:
                $this->setTasUid($value);
                break;
            case 3:
                $this->setGatNextTask($value);
                break;
            case 4:
                $this->setGatX($value);
                break;
            case 5:
                $this->setGatY($value);
                break;
            case 6:
                $this->setGatType($value);
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
        $keys = GatewayPeer::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) {
            $this->setGatUid($arr[$keys[0]]);
        }

        if (array_key_exists($keys[1], $arr)) {
            $this->setProUid($arr[$keys[1]]);
        }

        if (array_key_exists($keys[2], $arr)) {
            $this->setTasUid($arr[$keys[2]]);
        }

        if (array_key_exists($keys[3], $arr)) {
            $this->setGatNextTask($arr[$keys[3]]);
        }

        if (array_key_exists($keys[4], $arr)) {
            $this->setGatX($arr[$keys[4]]);
        }

        if (array_key_exists($keys[5], $arr)) {
            $this->setGatY($arr[$keys[5]]);
        }

        if (array_key_exists($keys[6], $arr)) {
            $this->setGatType($arr[$keys[6]]);
        }

    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return     Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(GatewayPeer::DATABASE_NAME);

        if ($this->isColumnModified(GatewayPeer::GAT_UID)) {
            $criteria->add(GatewayPeer::GAT_UID, $this->gat_uid);
        }

        if ($this->isColumnModified(GatewayPeer::PRO_UID)) {
            $criteria->add(GatewayPeer::PRO_UID, $this->pro_uid);
        }

        if ($this->isColumnModified(GatewayPeer::TAS_UID)) {
            $criteria->add(GatewayPeer::TAS_UID, $this->tas_uid);
        }

        if ($this->isColumnModified(GatewayPeer::GAT_NEXT_TASK)) {
            $criteria->add(GatewayPeer::GAT_NEXT_TASK, $this->gat_next_task);
        }

        if ($this->isColumnModified(GatewayPeer::GAT_X)) {
            $criteria->add(GatewayPeer::GAT_X, $this->gat_x);
        }

        if ($this->isColumnModified(GatewayPeer::GAT_Y)) {
            $criteria->add(GatewayPeer::GAT_Y, $this->gat_y);
        }

        if ($this->isColumnModified(GatewayPeer::GAT_TYPE)) {
            $criteria->add(GatewayPeer::GAT_TYPE, $this->gat_type);
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
        $criteria = new Criteria(GatewayPeer::DATABASE_NAME);

        $criteria->add(GatewayPeer::GAT_UID, $this->gat_uid);

        return $criteria;
    }

    /**
     * Returns the primary key for this object (row).
     * @return     string
     */
    public function getPrimaryKey()
    {
        return $this->getGatUid();
    }

    /**
     * Generic method to set the primary key (gat_uid column).
     *
     * @param      string $key Primary key.
     * @return     void
     */
    public function setPrimaryKey($key)
    {
        $this->setGatUid($key);
    }

    /**
     * Sets contents of passed object to values from current object.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param      object $copyObj An object of Gateway (or compatible) type.
     * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @throws     PropelException
     */
    public function copyInto($copyObj, $deepCopy = false)
    {

        $copyObj->setProUid($this->pro_uid);

        $copyObj->setTasUid($this->tas_uid);

        $copyObj->setGatNextTask($this->gat_next_task);

        $copyObj->setGatX($this->gat_x);

        $copyObj->setGatY($this->gat_y);

        $copyObj->setGatType($this->gat_type);


        $copyObj->setNew(true);

        $copyObj->setGatUid(''); // this is a pkey column, so set to default value

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
     * @return     Gateway Clone of current object.
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
     * @return     GatewayPeer
     */
    public function getPeer()
    {
        if (self::$peer === null) {
            self::$peer = new GatewayPeer();
        }
        return self::$peer;
    }
}

