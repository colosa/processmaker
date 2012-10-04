<?php

require_once 'propel/om/BaseObject.php';

require_once 'propel/om/Persistent.php';


include_once 'propel/util/Criteria.php';

include_once 'classes/model/SubProcessPeer.php';

/**
 * Base class that represents a row from the 'SUB_PROCESS' table.
 *
 * 
 *
 * @package    workflow.classes.model.om
 */
abstract class BaseSubProcess extends BaseObject implements Persistent
{

    /**
     * The Peer class.
     * Instance provides a convenient way of calling static methods on a class
     * that calling code may not be able to identify.
     * @var        SubProcessPeer
    */
    protected static $peer;

    /**
     * The value for the sp_uid field.
     * @var        string
     */
    protected $sp_uid = '';

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
     * The value for the pro_parent field.
     * @var        string
     */
    protected $pro_parent = '';

    /**
     * The value for the tas_parent field.
     * @var        string
     */
    protected $tas_parent = '';

    /**
     * The value for the sp_type field.
     * @var        string
     */
    protected $sp_type = '';

    /**
     * The value for the sp_synchronous field.
     * @var        int
     */
    protected $sp_synchronous = 0;

    /**
     * The value for the sp_synchronous_type field.
     * @var        string
     */
    protected $sp_synchronous_type = '';

    /**
     * The value for the sp_synchronous_wait field.
     * @var        int
     */
    protected $sp_synchronous_wait = 0;

    /**
     * The value for the sp_variables_out field.
     * @var        string
     */
    protected $sp_variables_out;

    /**
     * The value for the sp_variables_in field.
     * @var        string
     */
    protected $sp_variables_in;

    /**
     * The value for the sp_grid_in field.
     * @var        string
     */
    protected $sp_grid_in = '';

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
     * Get the [sp_uid] column value.
     * 
     * @return     string
     */
    public function getSpUid()
    {

        return $this->sp_uid;
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
     * Get the [pro_parent] column value.
     * 
     * @return     string
     */
    public function getProParent()
    {

        return $this->pro_parent;
    }

    /**
     * Get the [tas_parent] column value.
     * 
     * @return     string
     */
    public function getTasParent()
    {

        return $this->tas_parent;
    }

    /**
     * Get the [sp_type] column value.
     * 
     * @return     string
     */
    public function getSpType()
    {

        return $this->sp_type;
    }

    /**
     * Get the [sp_synchronous] column value.
     * 
     * @return     int
     */
    public function getSpSynchronous()
    {

        return $this->sp_synchronous;
    }

    /**
     * Get the [sp_synchronous_type] column value.
     * 
     * @return     string
     */
    public function getSpSynchronousType()
    {

        return $this->sp_synchronous_type;
    }

    /**
     * Get the [sp_synchronous_wait] column value.
     * 
     * @return     int
     */
    public function getSpSynchronousWait()
    {

        return $this->sp_synchronous_wait;
    }

    /**
     * Get the [sp_variables_out] column value.
     * 
     * @return     string
     */
    public function getSpVariablesOut()
    {

        return $this->sp_variables_out;
    }

    /**
     * Get the [sp_variables_in] column value.
     * 
     * @return     string
     */
    public function getSpVariablesIn()
    {

        return $this->sp_variables_in;
    }

    /**
     * Get the [sp_grid_in] column value.
     * 
     * @return     string
     */
    public function getSpGridIn()
    {

        return $this->sp_grid_in;
    }

    /**
     * Set the value of [sp_uid] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setSpUid($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->sp_uid !== $v || $v === '') {
            $this->sp_uid = $v;
            $this->modifiedColumns[] = SubProcessPeer::SP_UID;
        }

    } // setSpUid()

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
            $this->modifiedColumns[] = SubProcessPeer::PRO_UID;
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
            $this->modifiedColumns[] = SubProcessPeer::TAS_UID;
        }

    } // setTasUid()

    /**
     * Set the value of [pro_parent] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setProParent($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->pro_parent !== $v || $v === '') {
            $this->pro_parent = $v;
            $this->modifiedColumns[] = SubProcessPeer::PRO_PARENT;
        }

    } // setProParent()

    /**
     * Set the value of [tas_parent] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setTasParent($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->tas_parent !== $v || $v === '') {
            $this->tas_parent = $v;
            $this->modifiedColumns[] = SubProcessPeer::TAS_PARENT;
        }

    } // setTasParent()

    /**
     * Set the value of [sp_type] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setSpType($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->sp_type !== $v || $v === '') {
            $this->sp_type = $v;
            $this->modifiedColumns[] = SubProcessPeer::SP_TYPE;
        }

    } // setSpType()

    /**
     * Set the value of [sp_synchronous] column.
     * 
     * @param      int $v new value
     * @return     void
     */
    public function setSpSynchronous($v)
    {

        // Since the native PHP type for this column is integer,
        // we will cast the input value to an int (if it is not).
        if ($v !== null && !is_int($v) && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->sp_synchronous !== $v || $v === 0) {
            $this->sp_synchronous = $v;
            $this->modifiedColumns[] = SubProcessPeer::SP_SYNCHRONOUS;
        }

    } // setSpSynchronous()

    /**
     * Set the value of [sp_synchronous_type] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setSpSynchronousType($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->sp_synchronous_type !== $v || $v === '') {
            $this->sp_synchronous_type = $v;
            $this->modifiedColumns[] = SubProcessPeer::SP_SYNCHRONOUS_TYPE;
        }

    } // setSpSynchronousType()

    /**
     * Set the value of [sp_synchronous_wait] column.
     * 
     * @param      int $v new value
     * @return     void
     */
    public function setSpSynchronousWait($v)
    {

        // Since the native PHP type for this column is integer,
        // we will cast the input value to an int (if it is not).
        if ($v !== null && !is_int($v) && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->sp_synchronous_wait !== $v || $v === 0) {
            $this->sp_synchronous_wait = $v;
            $this->modifiedColumns[] = SubProcessPeer::SP_SYNCHRONOUS_WAIT;
        }

    } // setSpSynchronousWait()

    /**
     * Set the value of [sp_variables_out] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setSpVariablesOut($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->sp_variables_out !== $v) {
            $this->sp_variables_out = $v;
            $this->modifiedColumns[] = SubProcessPeer::SP_VARIABLES_OUT;
        }

    } // setSpVariablesOut()

    /**
     * Set the value of [sp_variables_in] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setSpVariablesIn($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->sp_variables_in !== $v) {
            $this->sp_variables_in = $v;
            $this->modifiedColumns[] = SubProcessPeer::SP_VARIABLES_IN;
        }

    } // setSpVariablesIn()

    /**
     * Set the value of [sp_grid_in] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setSpGridIn($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->sp_grid_in !== $v || $v === '') {
            $this->sp_grid_in = $v;
            $this->modifiedColumns[] = SubProcessPeer::SP_GRID_IN;
        }

    } // setSpGridIn()

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

            $this->sp_uid = $rs->getString($startcol + 0);

            $this->pro_uid = $rs->getString($startcol + 1);

            $this->tas_uid = $rs->getString($startcol + 2);

            $this->pro_parent = $rs->getString($startcol + 3);

            $this->tas_parent = $rs->getString($startcol + 4);

            $this->sp_type = $rs->getString($startcol + 5);

            $this->sp_synchronous = $rs->getInt($startcol + 6);

            $this->sp_synchronous_type = $rs->getString($startcol + 7);

            $this->sp_synchronous_wait = $rs->getInt($startcol + 8);

            $this->sp_variables_out = $rs->getString($startcol + 9);

            $this->sp_variables_in = $rs->getString($startcol + 10);

            $this->sp_grid_in = $rs->getString($startcol + 11);

            $this->resetModified();

            $this->setNew(false);

            // FIXME - using NUM_COLUMNS may be clearer.
            return $startcol + 12; // 12 = SubProcessPeer::NUM_COLUMNS - SubProcessPeer::NUM_LAZY_LOAD_COLUMNS).

        } catch (Exception $e) {
            throw new PropelException("Error populating SubProcess object", $e);
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
            $con = Propel::getConnection(SubProcessPeer::DATABASE_NAME);
        }

        try {
            $con->begin();
            SubProcessPeer::doDelete($this, $con);
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
            $con = Propel::getConnection(SubProcessPeer::DATABASE_NAME);
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
                    $pk = SubProcessPeer::doInsert($this, $con);
                    $affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
                                         // should always be true here (even though technically
                                         // BasePeer::doInsert() can insert multiple rows).

                    $this->setNew(false);
                } else {
                    $affectedRows += SubProcessPeer::doUpdate($this, $con);
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


            if (($retval = SubProcessPeer::doValidate($this, $columns)) !== true) {
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
        $pos = SubProcessPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                return $this->getSpUid();
                break;
            case 1:
                return $this->getProUid();
                break;
            case 2:
                return $this->getTasUid();
                break;
            case 3:
                return $this->getProParent();
                break;
            case 4:
                return $this->getTasParent();
                break;
            case 5:
                return $this->getSpType();
                break;
            case 6:
                return $this->getSpSynchronous();
                break;
            case 7:
                return $this->getSpSynchronousType();
                break;
            case 8:
                return $this->getSpSynchronousWait();
                break;
            case 9:
                return $this->getSpVariablesOut();
                break;
            case 10:
                return $this->getSpVariablesIn();
                break;
            case 11:
                return $this->getSpGridIn();
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
        $keys = SubProcessPeer::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getSpUid(),
            $keys[1] => $this->getProUid(),
            $keys[2] => $this->getTasUid(),
            $keys[3] => $this->getProParent(),
            $keys[4] => $this->getTasParent(),
            $keys[5] => $this->getSpType(),
            $keys[6] => $this->getSpSynchronous(),
            $keys[7] => $this->getSpSynchronousType(),
            $keys[8] => $this->getSpSynchronousWait(),
            $keys[9] => $this->getSpVariablesOut(),
            $keys[10] => $this->getSpVariablesIn(),
            $keys[11] => $this->getSpGridIn(),
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
        $pos = SubProcessPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                $this->setSpUid($value);
                break;
            case 1:
                $this->setProUid($value);
                break;
            case 2:
                $this->setTasUid($value);
                break;
            case 3:
                $this->setProParent($value);
                break;
            case 4:
                $this->setTasParent($value);
                break;
            case 5:
                $this->setSpType($value);
                break;
            case 6:
                $this->setSpSynchronous($value);
                break;
            case 7:
                $this->setSpSynchronousType($value);
                break;
            case 8:
                $this->setSpSynchronousWait($value);
                break;
            case 9:
                $this->setSpVariablesOut($value);
                break;
            case 10:
                $this->setSpVariablesIn($value);
                break;
            case 11:
                $this->setSpGridIn($value);
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
        $keys = SubProcessPeer::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) {
            $this->setSpUid($arr[$keys[0]]);
        }

        if (array_key_exists($keys[1], $arr)) {
            $this->setProUid($arr[$keys[1]]);
        }

        if (array_key_exists($keys[2], $arr)) {
            $this->setTasUid($arr[$keys[2]]);
        }

        if (array_key_exists($keys[3], $arr)) {
            $this->setProParent($arr[$keys[3]]);
        }

        if (array_key_exists($keys[4], $arr)) {
            $this->setTasParent($arr[$keys[4]]);
        }

        if (array_key_exists($keys[5], $arr)) {
            $this->setSpType($arr[$keys[5]]);
        }

        if (array_key_exists($keys[6], $arr)) {
            $this->setSpSynchronous($arr[$keys[6]]);
        }

        if (array_key_exists($keys[7], $arr)) {
            $this->setSpSynchronousType($arr[$keys[7]]);
        }

        if (array_key_exists($keys[8], $arr)) {
            $this->setSpSynchronousWait($arr[$keys[8]]);
        }

        if (array_key_exists($keys[9], $arr)) {
            $this->setSpVariablesOut($arr[$keys[9]]);
        }

        if (array_key_exists($keys[10], $arr)) {
            $this->setSpVariablesIn($arr[$keys[10]]);
        }

        if (array_key_exists($keys[11], $arr)) {
            $this->setSpGridIn($arr[$keys[11]]);
        }

    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return     Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(SubProcessPeer::DATABASE_NAME);

        if ($this->isColumnModified(SubProcessPeer::SP_UID)) {
            $criteria->add(SubProcessPeer::SP_UID, $this->sp_uid);
        }

        if ($this->isColumnModified(SubProcessPeer::PRO_UID)) {
            $criteria->add(SubProcessPeer::PRO_UID, $this->pro_uid);
        }

        if ($this->isColumnModified(SubProcessPeer::TAS_UID)) {
            $criteria->add(SubProcessPeer::TAS_UID, $this->tas_uid);
        }

        if ($this->isColumnModified(SubProcessPeer::PRO_PARENT)) {
            $criteria->add(SubProcessPeer::PRO_PARENT, $this->pro_parent);
        }

        if ($this->isColumnModified(SubProcessPeer::TAS_PARENT)) {
            $criteria->add(SubProcessPeer::TAS_PARENT, $this->tas_parent);
        }

        if ($this->isColumnModified(SubProcessPeer::SP_TYPE)) {
            $criteria->add(SubProcessPeer::SP_TYPE, $this->sp_type);
        }

        if ($this->isColumnModified(SubProcessPeer::SP_SYNCHRONOUS)) {
            $criteria->add(SubProcessPeer::SP_SYNCHRONOUS, $this->sp_synchronous);
        }

        if ($this->isColumnModified(SubProcessPeer::SP_SYNCHRONOUS_TYPE)) {
            $criteria->add(SubProcessPeer::SP_SYNCHRONOUS_TYPE, $this->sp_synchronous_type);
        }

        if ($this->isColumnModified(SubProcessPeer::SP_SYNCHRONOUS_WAIT)) {
            $criteria->add(SubProcessPeer::SP_SYNCHRONOUS_WAIT, $this->sp_synchronous_wait);
        }

        if ($this->isColumnModified(SubProcessPeer::SP_VARIABLES_OUT)) {
            $criteria->add(SubProcessPeer::SP_VARIABLES_OUT, $this->sp_variables_out);
        }

        if ($this->isColumnModified(SubProcessPeer::SP_VARIABLES_IN)) {
            $criteria->add(SubProcessPeer::SP_VARIABLES_IN, $this->sp_variables_in);
        }

        if ($this->isColumnModified(SubProcessPeer::SP_GRID_IN)) {
            $criteria->add(SubProcessPeer::SP_GRID_IN, $this->sp_grid_in);
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
        $criteria = new Criteria(SubProcessPeer::DATABASE_NAME);

        $criteria->add(SubProcessPeer::SP_UID, $this->sp_uid);

        return $criteria;
    }

    /**
     * Returns the primary key for this object (row).
     * @return     string
     */
    public function getPrimaryKey()
    {
        return $this->getSpUid();
    }

    /**
     * Generic method to set the primary key (sp_uid column).
     *
     * @param      string $key Primary key.
     * @return     void
     */
    public function setPrimaryKey($key)
    {
        $this->setSpUid($key);
    }

    /**
     * Sets contents of passed object to values from current object.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param      object $copyObj An object of SubProcess (or compatible) type.
     * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @throws     PropelException
     */
    public function copyInto($copyObj, $deepCopy = false)
    {

        $copyObj->setProUid($this->pro_uid);

        $copyObj->setTasUid($this->tas_uid);

        $copyObj->setProParent($this->pro_parent);

        $copyObj->setTasParent($this->tas_parent);

        $copyObj->setSpType($this->sp_type);

        $copyObj->setSpSynchronous($this->sp_synchronous);

        $copyObj->setSpSynchronousType($this->sp_synchronous_type);

        $copyObj->setSpSynchronousWait($this->sp_synchronous_wait);

        $copyObj->setSpVariablesOut($this->sp_variables_out);

        $copyObj->setSpVariablesIn($this->sp_variables_in);

        $copyObj->setSpGridIn($this->sp_grid_in);


        $copyObj->setNew(true);

        $copyObj->setSpUid(''); // this is a pkey column, so set to default value

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
     * @return     SubProcess Clone of current object.
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
     * @return     SubProcessPeer
     */
    public function getPeer()
    {
        if (self::$peer === null) {
            self::$peer = new SubProcessPeer();
        }
        return self::$peer;
    }
}

