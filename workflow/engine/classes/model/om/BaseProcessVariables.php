<?php

require_once 'propel/om/BaseObject.php';

require_once 'propel/om/Persistent.php';


include_once 'propel/util/Criteria.php';

include_once 'classes/model/ProcessVariablesPeer.php';

/**
 * Base class that represents a row from the 'PROCESS_VARIABLES' table.
 *
 * 
 *
 * @package    workflow.classes.model.om
 */
abstract class BaseProcessVariables extends BaseObject implements Persistent
{

    /**
     * The Peer class.
     * Instance provides a convenient way of calling static methods on a class
     * that calling code may not be able to identify.
     * @var        ProcessVariablesPeer
    */
    protected static $peer;

    /**
     * The value for the var_uid field.
     * @var        string
     */
    protected $var_uid;

    /**
     * The value for the prj_uid field.
     * @var        string
     */
    protected $prj_uid;

    /**
     * The value for the var_name field.
     * @var        string
     */
    protected $var_name = '';

    /**
     * The value for the var_field_type field.
     * @var        string
     */
    protected $var_field_type = '';

    /**
     * The value for the var_field_size field.
     * @var        int
     */
    protected $var_field_size;

    /**
     * The value for the var_label field.
     * @var        string
     */
    protected $var_label = '';

    /**
     * The value for the var_dbconnection field.
     * @var        string
     */
    protected $var_dbconnection;

    /**
     * The value for the var_sql field.
     * @var        string
     */
    protected $var_sql;

    /**
     * The value for the var_null field.
     * @var        int
     */
    protected $var_null = 0;

    /**
     * The value for the var_default field.
     * @var        string
     */
    protected $var_default = '';

    /**
     * The value for the var_accepted_values field.
     * @var        string
     */
    protected $var_accepted_values;

    /**
     * The value for the inp_doc_uid field.
     * @var        string
     */
    protected $inp_doc_uid = '';

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
     * Get the [var_uid] column value.
     * 
     * @return     string
     */
    public function getVarUid()
    {

        return $this->var_uid;
    }

    /**
     * Get the [prj_uid] column value.
     * 
     * @return     string
     */
    public function getPrjUid()
    {

        return $this->prj_uid;
    }

    /**
     * Get the [var_name] column value.
     * 
     * @return     string
     */
    public function getVarName()
    {

        return $this->var_name;
    }

    /**
     * Get the [var_field_type] column value.
     * 
     * @return     string
     */
    public function getVarFieldType()
    {

        return $this->var_field_type;
    }

    /**
     * Get the [var_field_size] column value.
     * 
     * @return     int
     */
    public function getVarFieldSize()
    {

        return $this->var_field_size;
    }

    /**
     * Get the [var_label] column value.
     * 
     * @return     string
     */
    public function getVarLabel()
    {

        return $this->var_label;
    }

    /**
     * Get the [var_dbconnection] column value.
     * 
     * @return     string
     */
    public function getVarDbconnection()
    {

        return $this->var_dbconnection;
    }

    /**
     * Get the [var_sql] column value.
     * 
     * @return     string
     */
    public function getVarSql()
    {

        return $this->var_sql;
    }

    /**
     * Get the [var_null] column value.
     * 
     * @return     int
     */
    public function getVarNull()
    {

        return $this->var_null;
    }

    /**
     * Get the [var_default] column value.
     * 
     * @return     string
     */
    public function getVarDefault()
    {

        return $this->var_default;
    }

    /**
     * Get the [var_accepted_values] column value.
     * 
     * @return     string
     */
    public function getVarAcceptedValues()
    {

        return $this->var_accepted_values;
    }

    /**
     * Get the [inp_doc_uid] column value.
     * 
     * @return     string
     */
    public function getInpDocUid()
    {

        return $this->inp_doc_uid;
    }

    /**
     * Set the value of [var_uid] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setVarUid($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->var_uid !== $v) {
            $this->var_uid = $v;
            $this->modifiedColumns[] = ProcessVariablesPeer::VAR_UID;
        }

    } // setVarUid()

    /**
     * Set the value of [prj_uid] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setPrjUid($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->prj_uid !== $v) {
            $this->prj_uid = $v;
            $this->modifiedColumns[] = ProcessVariablesPeer::PRJ_UID;
        }

    } // setPrjUid()

    /**
     * Set the value of [var_name] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setVarName($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->var_name !== $v || $v === '') {
            $this->var_name = $v;
            $this->modifiedColumns[] = ProcessVariablesPeer::VAR_NAME;
        }

    } // setVarName()

    /**
     * Set the value of [var_field_type] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setVarFieldType($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->var_field_type !== $v || $v === '') {
            $this->var_field_type = $v;
            $this->modifiedColumns[] = ProcessVariablesPeer::VAR_FIELD_TYPE;
        }

    } // setVarFieldType()

    /**
     * Set the value of [var_field_size] column.
     * 
     * @param      int $v new value
     * @return     void
     */
    public function setVarFieldSize($v)
    {

        // Since the native PHP type for this column is integer,
        // we will cast the input value to an int (if it is not).
        if ($v !== null && !is_int($v) && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->var_field_size !== $v) {
            $this->var_field_size = $v;
            $this->modifiedColumns[] = ProcessVariablesPeer::VAR_FIELD_SIZE;
        }

    } // setVarFieldSize()

    /**
     * Set the value of [var_label] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setVarLabel($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->var_label !== $v || $v === '') {
            $this->var_label = $v;
            $this->modifiedColumns[] = ProcessVariablesPeer::VAR_LABEL;
        }

    } // setVarLabel()

    /**
     * Set the value of [var_dbconnection] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setVarDbconnection($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->var_dbconnection !== $v) {
            $this->var_dbconnection = $v;
            $this->modifiedColumns[] = ProcessVariablesPeer::VAR_DBCONNECTION;
        }

    } // setVarDbconnection()

    /**
     * Set the value of [var_sql] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setVarSql($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->var_sql !== $v) {
            $this->var_sql = $v;
            $this->modifiedColumns[] = ProcessVariablesPeer::VAR_SQL;
        }

    } // setVarSql()

    /**
     * Set the value of [var_null] column.
     * 
     * @param      int $v new value
     * @return     void
     */
    public function setVarNull($v)
    {

        // Since the native PHP type for this column is integer,
        // we will cast the input value to an int (if it is not).
        if ($v !== null && !is_int($v) && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->var_null !== $v || $v === 0) {
            $this->var_null = $v;
            $this->modifiedColumns[] = ProcessVariablesPeer::VAR_NULL;
        }

    } // setVarNull()

    /**
     * Set the value of [var_default] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setVarDefault($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->var_default !== $v || $v === '') {
            $this->var_default = $v;
            $this->modifiedColumns[] = ProcessVariablesPeer::VAR_DEFAULT;
        }

    } // setVarDefault()

    /**
     * Set the value of [var_accepted_values] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setVarAcceptedValues($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->var_accepted_values !== $v) {
            $this->var_accepted_values = $v;
            $this->modifiedColumns[] = ProcessVariablesPeer::VAR_ACCEPTED_VALUES;
        }

    } // setVarAcceptedValues()

    /**
     * Set the value of [inp_doc_uid] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setInpDocUid($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->inp_doc_uid !== $v || $v === '') {
            $this->inp_doc_uid = $v;
            $this->modifiedColumns[] = ProcessVariablesPeer::INP_DOC_UID;
        }

    } // setInpDocUid()

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

            $this->var_uid = $rs->getString($startcol + 0);

            $this->prj_uid = $rs->getString($startcol + 1);

            $this->var_name = $rs->getString($startcol + 2);

            $this->var_field_type = $rs->getString($startcol + 3);

            $this->var_field_size = $rs->getInt($startcol + 4);

            $this->var_label = $rs->getString($startcol + 5);

            $this->var_dbconnection = $rs->getString($startcol + 6);

            $this->var_sql = $rs->getString($startcol + 7);

            $this->var_null = $rs->getInt($startcol + 8);

            $this->var_default = $rs->getString($startcol + 9);

            $this->var_accepted_values = $rs->getString($startcol + 10);

            $this->inp_doc_uid = $rs->getString($startcol + 11);

            $this->resetModified();

            $this->setNew(false);

            // FIXME - using NUM_COLUMNS may be clearer.
            return $startcol + 12; // 12 = ProcessVariablesPeer::NUM_COLUMNS - ProcessVariablesPeer::NUM_LAZY_LOAD_COLUMNS).

        } catch (Exception $e) {
            throw new PropelException("Error populating ProcessVariables object", $e);
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
            $con = Propel::getConnection(ProcessVariablesPeer::DATABASE_NAME);
        }

        try {
            $con->begin();
            ProcessVariablesPeer::doDelete($this, $con);
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
            $con = Propel::getConnection(ProcessVariablesPeer::DATABASE_NAME);
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
                    $pk = ProcessVariablesPeer::doInsert($this, $con);
                    $affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
                                         // should always be true here (even though technically
                                         // BasePeer::doInsert() can insert multiple rows).

                    $this->setNew(false);
                } else {
                    $affectedRows += ProcessVariablesPeer::doUpdate($this, $con);
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


            if (($retval = ProcessVariablesPeer::doValidate($this, $columns)) !== true) {
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
        $pos = ProcessVariablesPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                return $this->getVarUid();
                break;
            case 1:
                return $this->getPrjUid();
                break;
            case 2:
                return $this->getVarName();
                break;
            case 3:
                return $this->getVarFieldType();
                break;
            case 4:
                return $this->getVarFieldSize();
                break;
            case 5:
                return $this->getVarLabel();
                break;
            case 6:
                return $this->getVarDbconnection();
                break;
            case 7:
                return $this->getVarSql();
                break;
            case 8:
                return $this->getVarNull();
                break;
            case 9:
                return $this->getVarDefault();
                break;
            case 10:
                return $this->getVarAcceptedValues();
                break;
            case 11:
                return $this->getInpDocUid();
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
        $keys = ProcessVariablesPeer::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getVarUid(),
            $keys[1] => $this->getPrjUid(),
            $keys[2] => $this->getVarName(),
            $keys[3] => $this->getVarFieldType(),
            $keys[4] => $this->getVarFieldSize(),
            $keys[5] => $this->getVarLabel(),
            $keys[6] => $this->getVarDbconnection(),
            $keys[7] => $this->getVarSql(),
            $keys[8] => $this->getVarNull(),
            $keys[9] => $this->getVarDefault(),
            $keys[10] => $this->getVarAcceptedValues(),
            $keys[11] => $this->getInpDocUid(),
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
        $pos = ProcessVariablesPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                $this->setVarUid($value);
                break;
            case 1:
                $this->setPrjUid($value);
                break;
            case 2:
                $this->setVarName($value);
                break;
            case 3:
                $this->setVarFieldType($value);
                break;
            case 4:
                $this->setVarFieldSize($value);
                break;
            case 5:
                $this->setVarLabel($value);
                break;
            case 6:
                $this->setVarDbconnection($value);
                break;
            case 7:
                $this->setVarSql($value);
                break;
            case 8:
                $this->setVarNull($value);
                break;
            case 9:
                $this->setVarDefault($value);
                break;
            case 10:
                $this->setVarAcceptedValues($value);
                break;
            case 11:
                $this->setInpDocUid($value);
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
        $keys = ProcessVariablesPeer::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) {
            $this->setVarUid($arr[$keys[0]]);
        }

        if (array_key_exists($keys[1], $arr)) {
            $this->setPrjUid($arr[$keys[1]]);
        }

        if (array_key_exists($keys[2], $arr)) {
            $this->setVarName($arr[$keys[2]]);
        }

        if (array_key_exists($keys[3], $arr)) {
            $this->setVarFieldType($arr[$keys[3]]);
        }

        if (array_key_exists($keys[4], $arr)) {
            $this->setVarFieldSize($arr[$keys[4]]);
        }

        if (array_key_exists($keys[5], $arr)) {
            $this->setVarLabel($arr[$keys[5]]);
        }

        if (array_key_exists($keys[6], $arr)) {
            $this->setVarDbconnection($arr[$keys[6]]);
        }

        if (array_key_exists($keys[7], $arr)) {
            $this->setVarSql($arr[$keys[7]]);
        }

        if (array_key_exists($keys[8], $arr)) {
            $this->setVarNull($arr[$keys[8]]);
        }

        if (array_key_exists($keys[9], $arr)) {
            $this->setVarDefault($arr[$keys[9]]);
        }

        if (array_key_exists($keys[10], $arr)) {
            $this->setVarAcceptedValues($arr[$keys[10]]);
        }

        if (array_key_exists($keys[11], $arr)) {
            $this->setInpDocUid($arr[$keys[11]]);
        }

    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return     Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(ProcessVariablesPeer::DATABASE_NAME);

        if ($this->isColumnModified(ProcessVariablesPeer::VAR_UID)) {
            $criteria->add(ProcessVariablesPeer::VAR_UID, $this->var_uid);
        }

        if ($this->isColumnModified(ProcessVariablesPeer::PRJ_UID)) {
            $criteria->add(ProcessVariablesPeer::PRJ_UID, $this->prj_uid);
        }

        if ($this->isColumnModified(ProcessVariablesPeer::VAR_NAME)) {
            $criteria->add(ProcessVariablesPeer::VAR_NAME, $this->var_name);
        }

        if ($this->isColumnModified(ProcessVariablesPeer::VAR_FIELD_TYPE)) {
            $criteria->add(ProcessVariablesPeer::VAR_FIELD_TYPE, $this->var_field_type);
        }

        if ($this->isColumnModified(ProcessVariablesPeer::VAR_FIELD_SIZE)) {
            $criteria->add(ProcessVariablesPeer::VAR_FIELD_SIZE, $this->var_field_size);
        }

        if ($this->isColumnModified(ProcessVariablesPeer::VAR_LABEL)) {
            $criteria->add(ProcessVariablesPeer::VAR_LABEL, $this->var_label);
        }

        if ($this->isColumnModified(ProcessVariablesPeer::VAR_DBCONNECTION)) {
            $criteria->add(ProcessVariablesPeer::VAR_DBCONNECTION, $this->var_dbconnection);
        }

        if ($this->isColumnModified(ProcessVariablesPeer::VAR_SQL)) {
            $criteria->add(ProcessVariablesPeer::VAR_SQL, $this->var_sql);
        }

        if ($this->isColumnModified(ProcessVariablesPeer::VAR_NULL)) {
            $criteria->add(ProcessVariablesPeer::VAR_NULL, $this->var_null);
        }

        if ($this->isColumnModified(ProcessVariablesPeer::VAR_DEFAULT)) {
            $criteria->add(ProcessVariablesPeer::VAR_DEFAULT, $this->var_default);
        }

        if ($this->isColumnModified(ProcessVariablesPeer::VAR_ACCEPTED_VALUES)) {
            $criteria->add(ProcessVariablesPeer::VAR_ACCEPTED_VALUES, $this->var_accepted_values);
        }

        if ($this->isColumnModified(ProcessVariablesPeer::INP_DOC_UID)) {
            $criteria->add(ProcessVariablesPeer::INP_DOC_UID, $this->inp_doc_uid);
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
        $criteria = new Criteria(ProcessVariablesPeer::DATABASE_NAME);

        $criteria->add(ProcessVariablesPeer::VAR_UID, $this->var_uid);

        return $criteria;
    }

    /**
     * Returns the primary key for this object (row).
     * @return     string
     */
    public function getPrimaryKey()
    {
        return $this->getVarUid();
    }

    /**
     * Generic method to set the primary key (var_uid column).
     *
     * @param      string $key Primary key.
     * @return     void
     */
    public function setPrimaryKey($key)
    {
        $this->setVarUid($key);
    }

    /**
     * Sets contents of passed object to values from current object.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param      object $copyObj An object of ProcessVariables (or compatible) type.
     * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @throws     PropelException
     */
    public function copyInto($copyObj, $deepCopy = false)
    {

        $copyObj->setPrjUid($this->prj_uid);

        $copyObj->setVarName($this->var_name);

        $copyObj->setVarFieldType($this->var_field_type);

        $copyObj->setVarFieldSize($this->var_field_size);

        $copyObj->setVarLabel($this->var_label);

        $copyObj->setVarDbconnection($this->var_dbconnection);

        $copyObj->setVarSql($this->var_sql);

        $copyObj->setVarNull($this->var_null);

        $copyObj->setVarDefault($this->var_default);

        $copyObj->setVarAcceptedValues($this->var_accepted_values);

        $copyObj->setInpDocUid($this->inp_doc_uid);


        $copyObj->setNew(true);

        $copyObj->setVarUid(NULL); // this is a pkey column, so set to default value

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
     * @return     ProcessVariables Clone of current object.
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
     * @return     ProcessVariablesPeer
     */
    public function getPeer()
    {
        if (self::$peer === null) {
            self::$peer = new ProcessVariablesPeer();
        }
        return self::$peer;
    }
}

