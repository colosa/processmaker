<?php

require_once 'propel/om/BaseObject.php';

require_once 'propel/om/Persistent.php';


include_once 'propel/util/Criteria.php';

include_once 'classes/model/FieldsPeer.php';

/**
 * Base class that represents a row from the 'FIELDS' table.
 *
 * 
 *
 * @package    workflow.classes.model.om
 */
abstract class BaseFields extends BaseObject implements Persistent
{

    /**
     * The Peer class.
     * Instance provides a convenient way of calling static methods on a class
     * that calling code may not be able to identify.
     * @var        FieldsPeer
    */
    protected static $peer;

    /**
     * The value for the fld_uid field.
     * @var        string
     */
    protected $fld_uid = '';

    /**
     * The value for the add_tab_uid field.
     * @var        string
     */
    protected $add_tab_uid = '';

    /**
     * The value for the fld_index field.
     * @var        int
     */
    protected $fld_index = 1;

    /**
     * The value for the fld_name field.
     * @var        string
     */
    protected $fld_name = '';

    /**
     * The value for the fld_description field.
     * @var        string
     */
    protected $fld_description;

    /**
     * The value for the fld_type field.
     * @var        string
     */
    protected $fld_type = '';

    /**
     * The value for the fld_size field.
     * @var        int
     */
    protected $fld_size = 0;

    /**
     * The value for the fld_null field.
     * @var        int
     */
    protected $fld_null = 1;

    /**
     * The value for the fld_auto_increment field.
     * @var        int
     */
    protected $fld_auto_increment = 0;

    /**
     * The value for the fld_key field.
     * @var        int
     */
    protected $fld_key = 0;

    /**
     * The value for the fld_foreign_key field.
     * @var        int
     */
    protected $fld_foreign_key = 0;

    /**
     * The value for the fld_foreign_key_table field.
     * @var        string
     */
    protected $fld_foreign_key_table = '';

    /**
     * The value for the fld_dyn_name field.
     * @var        string
     */
    protected $fld_dyn_name = '';

    /**
     * The value for the fld_dyn_uid field.
     * @var        string
     */
    protected $fld_dyn_uid = '';

    /**
     * The value for the fld_filter field.
     * @var        int
     */
    protected $fld_filter = 0;

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
     * Get the [fld_uid] column value.
     * 
     * @return     string
     */
    public function getFldUid()
    {

        return $this->fld_uid;
    }

    /**
     * Get the [add_tab_uid] column value.
     * 
     * @return     string
     */
    public function getAddTabUid()
    {

        return $this->add_tab_uid;
    }

    /**
     * Get the [fld_index] column value.
     * 
     * @return     int
     */
    public function getFldIndex()
    {

        return $this->fld_index;
    }

    /**
     * Get the [fld_name] column value.
     * 
     * @return     string
     */
    public function getFldName()
    {

        return $this->fld_name;
    }

    /**
     * Get the [fld_description] column value.
     * 
     * @return     string
     */
    public function getFldDescription()
    {

        return $this->fld_description;
    }

    /**
     * Get the [fld_type] column value.
     * 
     * @return     string
     */
    public function getFldType()
    {

        return $this->fld_type;
    }

    /**
     * Get the [fld_size] column value.
     * 
     * @return     int
     */
    public function getFldSize()
    {

        return $this->fld_size;
    }

    /**
     * Get the [fld_null] column value.
     * 
     * @return     int
     */
    public function getFldNull()
    {

        return $this->fld_null;
    }

    /**
     * Get the [fld_auto_increment] column value.
     * 
     * @return     int
     */
    public function getFldAutoIncrement()
    {

        return $this->fld_auto_increment;
    }

    /**
     * Get the [fld_key] column value.
     * 
     * @return     int
     */
    public function getFldKey()
    {

        return $this->fld_key;
    }

    /**
     * Get the [fld_foreign_key] column value.
     * 
     * @return     int
     */
    public function getFldForeignKey()
    {

        return $this->fld_foreign_key;
    }

    /**
     * Get the [fld_foreign_key_table] column value.
     * 
     * @return     string
     */
    public function getFldForeignKeyTable()
    {

        return $this->fld_foreign_key_table;
    }

    /**
     * Get the [fld_dyn_name] column value.
     * 
     * @return     string
     */
    public function getFldDynName()
    {

        return $this->fld_dyn_name;
    }

    /**
     * Get the [fld_dyn_uid] column value.
     * 
     * @return     string
     */
    public function getFldDynUid()
    {

        return $this->fld_dyn_uid;
    }

    /**
     * Get the [fld_filter] column value.
     * 
     * @return     int
     */
    public function getFldFilter()
    {

        return $this->fld_filter;
    }

    /**
     * Set the value of [fld_uid] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setFldUid($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->fld_uid !== $v || $v === '') {
            $this->fld_uid = $v;
            $this->modifiedColumns[] = FieldsPeer::FLD_UID;
        }

    } // setFldUid()

    /**
     * Set the value of [add_tab_uid] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setAddTabUid($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->add_tab_uid !== $v || $v === '') {
            $this->add_tab_uid = $v;
            $this->modifiedColumns[] = FieldsPeer::ADD_TAB_UID;
        }

    } // setAddTabUid()

    /**
     * Set the value of [fld_index] column.
     * 
     * @param      int $v new value
     * @return     void
     */
    public function setFldIndex($v)
    {

        // Since the native PHP type for this column is integer,
        // we will cast the input value to an int (if it is not).
        if ($v !== null && !is_int($v) && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->fld_index !== $v || $v === 1) {
            $this->fld_index = $v;
            $this->modifiedColumns[] = FieldsPeer::FLD_INDEX;
        }

    } // setFldIndex()

    /**
     * Set the value of [fld_name] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setFldName($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->fld_name !== $v || $v === '') {
            $this->fld_name = $v;
            $this->modifiedColumns[] = FieldsPeer::FLD_NAME;
        }

    } // setFldName()

    /**
     * Set the value of [fld_description] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setFldDescription($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->fld_description !== $v) {
            $this->fld_description = $v;
            $this->modifiedColumns[] = FieldsPeer::FLD_DESCRIPTION;
        }

    } // setFldDescription()

    /**
     * Set the value of [fld_type] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setFldType($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->fld_type !== $v || $v === '') {
            $this->fld_type = $v;
            $this->modifiedColumns[] = FieldsPeer::FLD_TYPE;
        }

    } // setFldType()

    /**
     * Set the value of [fld_size] column.
     * 
     * @param      int $v new value
     * @return     void
     */
    public function setFldSize($v)
    {

        // Since the native PHP type for this column is integer,
        // we will cast the input value to an int (if it is not).
        if ($v !== null && !is_int($v) && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->fld_size !== $v || $v === 0) {
            $this->fld_size = $v;
            $this->modifiedColumns[] = FieldsPeer::FLD_SIZE;
        }

    } // setFldSize()

    /**
     * Set the value of [fld_null] column.
     * 
     * @param      int $v new value
     * @return     void
     */
    public function setFldNull($v)
    {

        // Since the native PHP type for this column is integer,
        // we will cast the input value to an int (if it is not).
        if ($v !== null && !is_int($v) && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->fld_null !== $v || $v === 1) {
            $this->fld_null = $v;
            $this->modifiedColumns[] = FieldsPeer::FLD_NULL;
        }

    } // setFldNull()

    /**
     * Set the value of [fld_auto_increment] column.
     * 
     * @param      int $v new value
     * @return     void
     */
    public function setFldAutoIncrement($v)
    {

        // Since the native PHP type for this column is integer,
        // we will cast the input value to an int (if it is not).
        if ($v !== null && !is_int($v) && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->fld_auto_increment !== $v || $v === 0) {
            $this->fld_auto_increment = $v;
            $this->modifiedColumns[] = FieldsPeer::FLD_AUTO_INCREMENT;
        }

    } // setFldAutoIncrement()

    /**
     * Set the value of [fld_key] column.
     * 
     * @param      int $v new value
     * @return     void
     */
    public function setFldKey($v)
    {

        // Since the native PHP type for this column is integer,
        // we will cast the input value to an int (if it is not).
        if ($v !== null && !is_int($v) && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->fld_key !== $v || $v === 0) {
            $this->fld_key = $v;
            $this->modifiedColumns[] = FieldsPeer::FLD_KEY;
        }

    } // setFldKey()

    /**
     * Set the value of [fld_foreign_key] column.
     * 
     * @param      int $v new value
     * @return     void
     */
    public function setFldForeignKey($v)
    {

        // Since the native PHP type for this column is integer,
        // we will cast the input value to an int (if it is not).
        if ($v !== null && !is_int($v) && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->fld_foreign_key !== $v || $v === 0) {
            $this->fld_foreign_key = $v;
            $this->modifiedColumns[] = FieldsPeer::FLD_FOREIGN_KEY;
        }

    } // setFldForeignKey()

    /**
     * Set the value of [fld_foreign_key_table] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setFldForeignKeyTable($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->fld_foreign_key_table !== $v || $v === '') {
            $this->fld_foreign_key_table = $v;
            $this->modifiedColumns[] = FieldsPeer::FLD_FOREIGN_KEY_TABLE;
        }

    } // setFldForeignKeyTable()

    /**
     * Set the value of [fld_dyn_name] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setFldDynName($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->fld_dyn_name !== $v || $v === '') {
            $this->fld_dyn_name = $v;
            $this->modifiedColumns[] = FieldsPeer::FLD_DYN_NAME;
        }

    } // setFldDynName()

    /**
     * Set the value of [fld_dyn_uid] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setFldDynUid($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->fld_dyn_uid !== $v || $v === '') {
            $this->fld_dyn_uid = $v;
            $this->modifiedColumns[] = FieldsPeer::FLD_DYN_UID;
        }

    } // setFldDynUid()

    /**
     * Set the value of [fld_filter] column.
     * 
     * @param      int $v new value
     * @return     void
     */
    public function setFldFilter($v)
    {

        // Since the native PHP type for this column is integer,
        // we will cast the input value to an int (if it is not).
        if ($v !== null && !is_int($v) && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->fld_filter !== $v || $v === 0) {
            $this->fld_filter = $v;
            $this->modifiedColumns[] = FieldsPeer::FLD_FILTER;
        }

    } // setFldFilter()

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

            $this->fld_uid = $rs->getString($startcol + 0);

            $this->add_tab_uid = $rs->getString($startcol + 1);

            $this->fld_index = $rs->getInt($startcol + 2);

            $this->fld_name = $rs->getString($startcol + 3);

            $this->fld_description = $rs->getString($startcol + 4);

            $this->fld_type = $rs->getString($startcol + 5);

            $this->fld_size = $rs->getInt($startcol + 6);

            $this->fld_null = $rs->getInt($startcol + 7);

            $this->fld_auto_increment = $rs->getInt($startcol + 8);

            $this->fld_key = $rs->getInt($startcol + 9);

            $this->fld_foreign_key = $rs->getInt($startcol + 10);

            $this->fld_foreign_key_table = $rs->getString($startcol + 11);

            $this->fld_dyn_name = $rs->getString($startcol + 12);

            $this->fld_dyn_uid = $rs->getString($startcol + 13);

            $this->fld_filter = $rs->getInt($startcol + 14);

            $this->resetModified();

            $this->setNew(false);

            // FIXME - using NUM_COLUMNS may be clearer.
            return $startcol + 15; // 15 = FieldsPeer::NUM_COLUMNS - FieldsPeer::NUM_LAZY_LOAD_COLUMNS).

        } catch (Exception $e) {
            throw new PropelException("Error populating Fields object", $e);
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
            $con = Propel::getConnection(FieldsPeer::DATABASE_NAME);
        }

        try {
            $con->begin();
            FieldsPeer::doDelete($this, $con);
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
            $con = Propel::getConnection(FieldsPeer::DATABASE_NAME);
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
                    $pk = FieldsPeer::doInsert($this, $con);
                    $affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
                                         // should always be true here (even though technically
                                         // BasePeer::doInsert() can insert multiple rows).

                    $this->setNew(false);
                } else {
                    $affectedRows += FieldsPeer::doUpdate($this, $con);
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


            if (($retval = FieldsPeer::doValidate($this, $columns)) !== true) {
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
        $pos = FieldsPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                return $this->getFldUid();
                break;
            case 1:
                return $this->getAddTabUid();
                break;
            case 2:
                return $this->getFldIndex();
                break;
            case 3:
                return $this->getFldName();
                break;
            case 4:
                return $this->getFldDescription();
                break;
            case 5:
                return $this->getFldType();
                break;
            case 6:
                return $this->getFldSize();
                break;
            case 7:
                return $this->getFldNull();
                break;
            case 8:
                return $this->getFldAutoIncrement();
                break;
            case 9:
                return $this->getFldKey();
                break;
            case 10:
                return $this->getFldForeignKey();
                break;
            case 11:
                return $this->getFldForeignKeyTable();
                break;
            case 12:
                return $this->getFldDynName();
                break;
            case 13:
                return $this->getFldDynUid();
                break;
            case 14:
                return $this->getFldFilter();
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
        $keys = FieldsPeer::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getFldUid(),
            $keys[1] => $this->getAddTabUid(),
            $keys[2] => $this->getFldIndex(),
            $keys[3] => $this->getFldName(),
            $keys[4] => $this->getFldDescription(),
            $keys[5] => $this->getFldType(),
            $keys[6] => $this->getFldSize(),
            $keys[7] => $this->getFldNull(),
            $keys[8] => $this->getFldAutoIncrement(),
            $keys[9] => $this->getFldKey(),
            $keys[10] => $this->getFldForeignKey(),
            $keys[11] => $this->getFldForeignKeyTable(),
            $keys[12] => $this->getFldDynName(),
            $keys[13] => $this->getFldDynUid(),
            $keys[14] => $this->getFldFilter(),
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
        $pos = FieldsPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                $this->setFldUid($value);
                break;
            case 1:
                $this->setAddTabUid($value);
                break;
            case 2:
                $this->setFldIndex($value);
                break;
            case 3:
                $this->setFldName($value);
                break;
            case 4:
                $this->setFldDescription($value);
                break;
            case 5:
                $this->setFldType($value);
                break;
            case 6:
                $this->setFldSize($value);
                break;
            case 7:
                $this->setFldNull($value);
                break;
            case 8:
                $this->setFldAutoIncrement($value);
                break;
            case 9:
                $this->setFldKey($value);
                break;
            case 10:
                $this->setFldForeignKey($value);
                break;
            case 11:
                $this->setFldForeignKeyTable($value);
                break;
            case 12:
                $this->setFldDynName($value);
                break;
            case 13:
                $this->setFldDynUid($value);
                break;
            case 14:
                $this->setFldFilter($value);
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
        $keys = FieldsPeer::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) {
            $this->setFldUid($arr[$keys[0]]);
        }

        if (array_key_exists($keys[1], $arr)) {
            $this->setAddTabUid($arr[$keys[1]]);
        }

        if (array_key_exists($keys[2], $arr)) {
            $this->setFldIndex($arr[$keys[2]]);
        }

        if (array_key_exists($keys[3], $arr)) {
            $this->setFldName($arr[$keys[3]]);
        }

        if (array_key_exists($keys[4], $arr)) {
            $this->setFldDescription($arr[$keys[4]]);
        }

        if (array_key_exists($keys[5], $arr)) {
            $this->setFldType($arr[$keys[5]]);
        }

        if (array_key_exists($keys[6], $arr)) {
            $this->setFldSize($arr[$keys[6]]);
        }

        if (array_key_exists($keys[7], $arr)) {
            $this->setFldNull($arr[$keys[7]]);
        }

        if (array_key_exists($keys[8], $arr)) {
            $this->setFldAutoIncrement($arr[$keys[8]]);
        }

        if (array_key_exists($keys[9], $arr)) {
            $this->setFldKey($arr[$keys[9]]);
        }

        if (array_key_exists($keys[10], $arr)) {
            $this->setFldForeignKey($arr[$keys[10]]);
        }

        if (array_key_exists($keys[11], $arr)) {
            $this->setFldForeignKeyTable($arr[$keys[11]]);
        }

        if (array_key_exists($keys[12], $arr)) {
            $this->setFldDynName($arr[$keys[12]]);
        }

        if (array_key_exists($keys[13], $arr)) {
            $this->setFldDynUid($arr[$keys[13]]);
        }

        if (array_key_exists($keys[14], $arr)) {
            $this->setFldFilter($arr[$keys[14]]);
        }

    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return     Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(FieldsPeer::DATABASE_NAME);

        if ($this->isColumnModified(FieldsPeer::FLD_UID)) {
            $criteria->add(FieldsPeer::FLD_UID, $this->fld_uid);
        }

        if ($this->isColumnModified(FieldsPeer::ADD_TAB_UID)) {
            $criteria->add(FieldsPeer::ADD_TAB_UID, $this->add_tab_uid);
        }

        if ($this->isColumnModified(FieldsPeer::FLD_INDEX)) {
            $criteria->add(FieldsPeer::FLD_INDEX, $this->fld_index);
        }

        if ($this->isColumnModified(FieldsPeer::FLD_NAME)) {
            $criteria->add(FieldsPeer::FLD_NAME, $this->fld_name);
        }

        if ($this->isColumnModified(FieldsPeer::FLD_DESCRIPTION)) {
            $criteria->add(FieldsPeer::FLD_DESCRIPTION, $this->fld_description);
        }

        if ($this->isColumnModified(FieldsPeer::FLD_TYPE)) {
            $criteria->add(FieldsPeer::FLD_TYPE, $this->fld_type);
        }

        if ($this->isColumnModified(FieldsPeer::FLD_SIZE)) {
            $criteria->add(FieldsPeer::FLD_SIZE, $this->fld_size);
        }

        if ($this->isColumnModified(FieldsPeer::FLD_NULL)) {
            $criteria->add(FieldsPeer::FLD_NULL, $this->fld_null);
        }

        if ($this->isColumnModified(FieldsPeer::FLD_AUTO_INCREMENT)) {
            $criteria->add(FieldsPeer::FLD_AUTO_INCREMENT, $this->fld_auto_increment);
        }

        if ($this->isColumnModified(FieldsPeer::FLD_KEY)) {
            $criteria->add(FieldsPeer::FLD_KEY, $this->fld_key);
        }

        if ($this->isColumnModified(FieldsPeer::FLD_FOREIGN_KEY)) {
            $criteria->add(FieldsPeer::FLD_FOREIGN_KEY, $this->fld_foreign_key);
        }

        if ($this->isColumnModified(FieldsPeer::FLD_FOREIGN_KEY_TABLE)) {
            $criteria->add(FieldsPeer::FLD_FOREIGN_KEY_TABLE, $this->fld_foreign_key_table);
        }

        if ($this->isColumnModified(FieldsPeer::FLD_DYN_NAME)) {
            $criteria->add(FieldsPeer::FLD_DYN_NAME, $this->fld_dyn_name);
        }

        if ($this->isColumnModified(FieldsPeer::FLD_DYN_UID)) {
            $criteria->add(FieldsPeer::FLD_DYN_UID, $this->fld_dyn_uid);
        }

        if ($this->isColumnModified(FieldsPeer::FLD_FILTER)) {
            $criteria->add(FieldsPeer::FLD_FILTER, $this->fld_filter);
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
        $criteria = new Criteria(FieldsPeer::DATABASE_NAME);

        $criteria->add(FieldsPeer::FLD_UID, $this->fld_uid);

        return $criteria;
    }

    /**
     * Returns the primary key for this object (row).
     * @return     string
     */
    public function getPrimaryKey()
    {
        return $this->getFldUid();
    }

    /**
     * Generic method to set the primary key (fld_uid column).
     *
     * @param      string $key Primary key.
     * @return     void
     */
    public function setPrimaryKey($key)
    {
        $this->setFldUid($key);
    }

    /**
     * Sets contents of passed object to values from current object.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param      object $copyObj An object of Fields (or compatible) type.
     * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @throws     PropelException
     */
    public function copyInto($copyObj, $deepCopy = false)
    {

        $copyObj->setAddTabUid($this->add_tab_uid);

        $copyObj->setFldIndex($this->fld_index);

        $copyObj->setFldName($this->fld_name);

        $copyObj->setFldDescription($this->fld_description);

        $copyObj->setFldType($this->fld_type);

        $copyObj->setFldSize($this->fld_size);

        $copyObj->setFldNull($this->fld_null);

        $copyObj->setFldAutoIncrement($this->fld_auto_increment);

        $copyObj->setFldKey($this->fld_key);

        $copyObj->setFldForeignKey($this->fld_foreign_key);

        $copyObj->setFldForeignKeyTable($this->fld_foreign_key_table);

        $copyObj->setFldDynName($this->fld_dyn_name);

        $copyObj->setFldDynUid($this->fld_dyn_uid);

        $copyObj->setFldFilter($this->fld_filter);


        $copyObj->setNew(true);

        $copyObj->setFldUid(''); // this is a pkey column, so set to default value

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
     * @return     Fields Clone of current object.
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
     * @return     FieldsPeer
     */
    public function getPeer()
    {
        if (self::$peer === null) {
            self::$peer = new FieldsPeer();
        }
        return self::$peer;
    }
}

