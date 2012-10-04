<?php

require_once 'propel/om/BaseObject.php';

require_once 'propel/om/Persistent.php';


include_once 'propel/util/Criteria.php';

include_once 'classes/model/AdditionalTablesPeer.php';

/**
 * Base class that represents a row from the 'ADDITIONAL_TABLES' table.
 *
 * 
 *
 * @package    workflow.classes.model.om
 */
abstract class BaseAdditionalTables extends BaseObject implements Persistent
{

    /**
     * The Peer class.
     * Instance provides a convenient way of calling static methods on a class
     * that calling code may not be able to identify.
     * @var        AdditionalTablesPeer
    */
    protected static $peer;

    /**
     * The value for the add_tab_uid field.
     * @var        string
     */
    protected $add_tab_uid = '';

    /**
     * The value for the add_tab_name field.
     * @var        string
     */
    protected $add_tab_name = '';

    /**
     * The value for the add_tab_class_name field.
     * @var        string
     */
    protected $add_tab_class_name = '';

    /**
     * The value for the add_tab_description field.
     * @var        string
     */
    protected $add_tab_description;

    /**
     * The value for the add_tab_sdw_log_insert field.
     * @var        int
     */
    protected $add_tab_sdw_log_insert = 0;

    /**
     * The value for the add_tab_sdw_log_update field.
     * @var        int
     */
    protected $add_tab_sdw_log_update = 0;

    /**
     * The value for the add_tab_sdw_log_delete field.
     * @var        int
     */
    protected $add_tab_sdw_log_delete = 0;

    /**
     * The value for the add_tab_sdw_log_select field.
     * @var        int
     */
    protected $add_tab_sdw_log_select = 0;

    /**
     * The value for the add_tab_sdw_max_length field.
     * @var        int
     */
    protected $add_tab_sdw_max_length = 0;

    /**
     * The value for the add_tab_sdw_auto_delete field.
     * @var        int
     */
    protected $add_tab_sdw_auto_delete = 0;

    /**
     * The value for the add_tab_plg_uid field.
     * @var        string
     */
    protected $add_tab_plg_uid = '';

    /**
     * The value for the dbs_uid field.
     * @var        string
     */
    protected $dbs_uid = '';

    /**
     * The value for the pro_uid field.
     * @var        string
     */
    protected $pro_uid = '';

    /**
     * The value for the add_tab_type field.
     * @var        string
     */
    protected $add_tab_type = '';

    /**
     * The value for the add_tab_grid field.
     * @var        string
     */
    protected $add_tab_grid = '';

    /**
     * The value for the add_tab_tag field.
     * @var        string
     */
    protected $add_tab_tag = '';

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
     * Get the [add_tab_uid] column value.
     * 
     * @return     string
     */
    public function getAddTabUid()
    {

        return $this->add_tab_uid;
    }

    /**
     * Get the [add_tab_name] column value.
     * 
     * @return     string
     */
    public function getAddTabName()
    {

        return $this->add_tab_name;
    }

    /**
     * Get the [add_tab_class_name] column value.
     * 
     * @return     string
     */
    public function getAddTabClassName()
    {

        return $this->add_tab_class_name;
    }

    /**
     * Get the [add_tab_description] column value.
     * 
     * @return     string
     */
    public function getAddTabDescription()
    {

        return $this->add_tab_description;
    }

    /**
     * Get the [add_tab_sdw_log_insert] column value.
     * 
     * @return     int
     */
    public function getAddTabSdwLogInsert()
    {

        return $this->add_tab_sdw_log_insert;
    }

    /**
     * Get the [add_tab_sdw_log_update] column value.
     * 
     * @return     int
     */
    public function getAddTabSdwLogUpdate()
    {

        return $this->add_tab_sdw_log_update;
    }

    /**
     * Get the [add_tab_sdw_log_delete] column value.
     * 
     * @return     int
     */
    public function getAddTabSdwLogDelete()
    {

        return $this->add_tab_sdw_log_delete;
    }

    /**
     * Get the [add_tab_sdw_log_select] column value.
     * 
     * @return     int
     */
    public function getAddTabSdwLogSelect()
    {

        return $this->add_tab_sdw_log_select;
    }

    /**
     * Get the [add_tab_sdw_max_length] column value.
     * 
     * @return     int
     */
    public function getAddTabSdwMaxLength()
    {

        return $this->add_tab_sdw_max_length;
    }

    /**
     * Get the [add_tab_sdw_auto_delete] column value.
     * 
     * @return     int
     */
    public function getAddTabSdwAutoDelete()
    {

        return $this->add_tab_sdw_auto_delete;
    }

    /**
     * Get the [add_tab_plg_uid] column value.
     * 
     * @return     string
     */
    public function getAddTabPlgUid()
    {

        return $this->add_tab_plg_uid;
    }

    /**
     * Get the [dbs_uid] column value.
     * 
     * @return     string
     */
    public function getDbsUid()
    {

        return $this->dbs_uid;
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
     * Get the [add_tab_type] column value.
     * 
     * @return     string
     */
    public function getAddTabType()
    {

        return $this->add_tab_type;
    }

    /**
     * Get the [add_tab_grid] column value.
     * 
     * @return     string
     */
    public function getAddTabGrid()
    {

        return $this->add_tab_grid;
    }

    /**
     * Get the [add_tab_tag] column value.
     * 
     * @return     string
     */
    public function getAddTabTag()
    {

        return $this->add_tab_tag;
    }

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
            $this->modifiedColumns[] = AdditionalTablesPeer::ADD_TAB_UID;
        }

    } // setAddTabUid()

    /**
     * Set the value of [add_tab_name] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setAddTabName($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->add_tab_name !== $v || $v === '') {
            $this->add_tab_name = $v;
            $this->modifiedColumns[] = AdditionalTablesPeer::ADD_TAB_NAME;
        }

    } // setAddTabName()

    /**
     * Set the value of [add_tab_class_name] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setAddTabClassName($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->add_tab_class_name !== $v || $v === '') {
            $this->add_tab_class_name = $v;
            $this->modifiedColumns[] = AdditionalTablesPeer::ADD_TAB_CLASS_NAME;
        }

    } // setAddTabClassName()

    /**
     * Set the value of [add_tab_description] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setAddTabDescription($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->add_tab_description !== $v) {
            $this->add_tab_description = $v;
            $this->modifiedColumns[] = AdditionalTablesPeer::ADD_TAB_DESCRIPTION;
        }

    } // setAddTabDescription()

    /**
     * Set the value of [add_tab_sdw_log_insert] column.
     * 
     * @param      int $v new value
     * @return     void
     */
    public function setAddTabSdwLogInsert($v)
    {

        // Since the native PHP type for this column is integer,
        // we will cast the input value to an int (if it is not).
        if ($v !== null && !is_int($v) && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->add_tab_sdw_log_insert !== $v || $v === 0) {
            $this->add_tab_sdw_log_insert = $v;
            $this->modifiedColumns[] = AdditionalTablesPeer::ADD_TAB_SDW_LOG_INSERT;
        }

    } // setAddTabSdwLogInsert()

    /**
     * Set the value of [add_tab_sdw_log_update] column.
     * 
     * @param      int $v new value
     * @return     void
     */
    public function setAddTabSdwLogUpdate($v)
    {

        // Since the native PHP type for this column is integer,
        // we will cast the input value to an int (if it is not).
        if ($v !== null && !is_int($v) && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->add_tab_sdw_log_update !== $v || $v === 0) {
            $this->add_tab_sdw_log_update = $v;
            $this->modifiedColumns[] = AdditionalTablesPeer::ADD_TAB_SDW_LOG_UPDATE;
        }

    } // setAddTabSdwLogUpdate()

    /**
     * Set the value of [add_tab_sdw_log_delete] column.
     * 
     * @param      int $v new value
     * @return     void
     */
    public function setAddTabSdwLogDelete($v)
    {

        // Since the native PHP type for this column is integer,
        // we will cast the input value to an int (if it is not).
        if ($v !== null && !is_int($v) && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->add_tab_sdw_log_delete !== $v || $v === 0) {
            $this->add_tab_sdw_log_delete = $v;
            $this->modifiedColumns[] = AdditionalTablesPeer::ADD_TAB_SDW_LOG_DELETE;
        }

    } // setAddTabSdwLogDelete()

    /**
     * Set the value of [add_tab_sdw_log_select] column.
     * 
     * @param      int $v new value
     * @return     void
     */
    public function setAddTabSdwLogSelect($v)
    {

        // Since the native PHP type for this column is integer,
        // we will cast the input value to an int (if it is not).
        if ($v !== null && !is_int($v) && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->add_tab_sdw_log_select !== $v || $v === 0) {
            $this->add_tab_sdw_log_select = $v;
            $this->modifiedColumns[] = AdditionalTablesPeer::ADD_TAB_SDW_LOG_SELECT;
        }

    } // setAddTabSdwLogSelect()

    /**
     * Set the value of [add_tab_sdw_max_length] column.
     * 
     * @param      int $v new value
     * @return     void
     */
    public function setAddTabSdwMaxLength($v)
    {

        // Since the native PHP type for this column is integer,
        // we will cast the input value to an int (if it is not).
        if ($v !== null && !is_int($v) && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->add_tab_sdw_max_length !== $v || $v === 0) {
            $this->add_tab_sdw_max_length = $v;
            $this->modifiedColumns[] = AdditionalTablesPeer::ADD_TAB_SDW_MAX_LENGTH;
        }

    } // setAddTabSdwMaxLength()

    /**
     * Set the value of [add_tab_sdw_auto_delete] column.
     * 
     * @param      int $v new value
     * @return     void
     */
    public function setAddTabSdwAutoDelete($v)
    {

        // Since the native PHP type for this column is integer,
        // we will cast the input value to an int (if it is not).
        if ($v !== null && !is_int($v) && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->add_tab_sdw_auto_delete !== $v || $v === 0) {
            $this->add_tab_sdw_auto_delete = $v;
            $this->modifiedColumns[] = AdditionalTablesPeer::ADD_TAB_SDW_AUTO_DELETE;
        }

    } // setAddTabSdwAutoDelete()

    /**
     * Set the value of [add_tab_plg_uid] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setAddTabPlgUid($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->add_tab_plg_uid !== $v || $v === '') {
            $this->add_tab_plg_uid = $v;
            $this->modifiedColumns[] = AdditionalTablesPeer::ADD_TAB_PLG_UID;
        }

    } // setAddTabPlgUid()

    /**
     * Set the value of [dbs_uid] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setDbsUid($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->dbs_uid !== $v || $v === '') {
            $this->dbs_uid = $v;
            $this->modifiedColumns[] = AdditionalTablesPeer::DBS_UID;
        }

    } // setDbsUid()

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
            $this->modifiedColumns[] = AdditionalTablesPeer::PRO_UID;
        }

    } // setProUid()

    /**
     * Set the value of [add_tab_type] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setAddTabType($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->add_tab_type !== $v || $v === '') {
            $this->add_tab_type = $v;
            $this->modifiedColumns[] = AdditionalTablesPeer::ADD_TAB_TYPE;
        }

    } // setAddTabType()

    /**
     * Set the value of [add_tab_grid] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setAddTabGrid($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->add_tab_grid !== $v || $v === '') {
            $this->add_tab_grid = $v;
            $this->modifiedColumns[] = AdditionalTablesPeer::ADD_TAB_GRID;
        }

    } // setAddTabGrid()

    /**
     * Set the value of [add_tab_tag] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setAddTabTag($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->add_tab_tag !== $v || $v === '') {
            $this->add_tab_tag = $v;
            $this->modifiedColumns[] = AdditionalTablesPeer::ADD_TAB_TAG;
        }

    } // setAddTabTag()

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

            $this->add_tab_uid = $rs->getString($startcol + 0);

            $this->add_tab_name = $rs->getString($startcol + 1);

            $this->add_tab_class_name = $rs->getString($startcol + 2);

            $this->add_tab_description = $rs->getString($startcol + 3);

            $this->add_tab_sdw_log_insert = $rs->getInt($startcol + 4);

            $this->add_tab_sdw_log_update = $rs->getInt($startcol + 5);

            $this->add_tab_sdw_log_delete = $rs->getInt($startcol + 6);

            $this->add_tab_sdw_log_select = $rs->getInt($startcol + 7);

            $this->add_tab_sdw_max_length = $rs->getInt($startcol + 8);

            $this->add_tab_sdw_auto_delete = $rs->getInt($startcol + 9);

            $this->add_tab_plg_uid = $rs->getString($startcol + 10);

            $this->dbs_uid = $rs->getString($startcol + 11);

            $this->pro_uid = $rs->getString($startcol + 12);

            $this->add_tab_type = $rs->getString($startcol + 13);

            $this->add_tab_grid = $rs->getString($startcol + 14);

            $this->add_tab_tag = $rs->getString($startcol + 15);

            $this->resetModified();

            $this->setNew(false);

            // FIXME - using NUM_COLUMNS may be clearer.
            return $startcol + 16; // 16 = AdditionalTablesPeer::NUM_COLUMNS - AdditionalTablesPeer::NUM_LAZY_LOAD_COLUMNS).

        } catch (Exception $e) {
            throw new PropelException("Error populating AdditionalTables object", $e);
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
            $con = Propel::getConnection(AdditionalTablesPeer::DATABASE_NAME);
        }

        try {
            $con->begin();
            AdditionalTablesPeer::doDelete($this, $con);
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
            $con = Propel::getConnection(AdditionalTablesPeer::DATABASE_NAME);
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
                    $pk = AdditionalTablesPeer::doInsert($this, $con);
                    $affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
                                         // should always be true here (even though technically
                                         // BasePeer::doInsert() can insert multiple rows).

                    $this->setNew(false);
                } else {
                    $affectedRows += AdditionalTablesPeer::doUpdate($this, $con);
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


            if (($retval = AdditionalTablesPeer::doValidate($this, $columns)) !== true) {
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
        $pos = AdditionalTablesPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                return $this->getAddTabUid();
                break;
            case 1:
                return $this->getAddTabName();
                break;
            case 2:
                return $this->getAddTabClassName();
                break;
            case 3:
                return $this->getAddTabDescription();
                break;
            case 4:
                return $this->getAddTabSdwLogInsert();
                break;
            case 5:
                return $this->getAddTabSdwLogUpdate();
                break;
            case 6:
                return $this->getAddTabSdwLogDelete();
                break;
            case 7:
                return $this->getAddTabSdwLogSelect();
                break;
            case 8:
                return $this->getAddTabSdwMaxLength();
                break;
            case 9:
                return $this->getAddTabSdwAutoDelete();
                break;
            case 10:
                return $this->getAddTabPlgUid();
                break;
            case 11:
                return $this->getDbsUid();
                break;
            case 12:
                return $this->getProUid();
                break;
            case 13:
                return $this->getAddTabType();
                break;
            case 14:
                return $this->getAddTabGrid();
                break;
            case 15:
                return $this->getAddTabTag();
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
        $keys = AdditionalTablesPeer::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getAddTabUid(),
            $keys[1] => $this->getAddTabName(),
            $keys[2] => $this->getAddTabClassName(),
            $keys[3] => $this->getAddTabDescription(),
            $keys[4] => $this->getAddTabSdwLogInsert(),
            $keys[5] => $this->getAddTabSdwLogUpdate(),
            $keys[6] => $this->getAddTabSdwLogDelete(),
            $keys[7] => $this->getAddTabSdwLogSelect(),
            $keys[8] => $this->getAddTabSdwMaxLength(),
            $keys[9] => $this->getAddTabSdwAutoDelete(),
            $keys[10] => $this->getAddTabPlgUid(),
            $keys[11] => $this->getDbsUid(),
            $keys[12] => $this->getProUid(),
            $keys[13] => $this->getAddTabType(),
            $keys[14] => $this->getAddTabGrid(),
            $keys[15] => $this->getAddTabTag(),
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
        $pos = AdditionalTablesPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                $this->setAddTabUid($value);
                break;
            case 1:
                $this->setAddTabName($value);
                break;
            case 2:
                $this->setAddTabClassName($value);
                break;
            case 3:
                $this->setAddTabDescription($value);
                break;
            case 4:
                $this->setAddTabSdwLogInsert($value);
                break;
            case 5:
                $this->setAddTabSdwLogUpdate($value);
                break;
            case 6:
                $this->setAddTabSdwLogDelete($value);
                break;
            case 7:
                $this->setAddTabSdwLogSelect($value);
                break;
            case 8:
                $this->setAddTabSdwMaxLength($value);
                break;
            case 9:
                $this->setAddTabSdwAutoDelete($value);
                break;
            case 10:
                $this->setAddTabPlgUid($value);
                break;
            case 11:
                $this->setDbsUid($value);
                break;
            case 12:
                $this->setProUid($value);
                break;
            case 13:
                $this->setAddTabType($value);
                break;
            case 14:
                $this->setAddTabGrid($value);
                break;
            case 15:
                $this->setAddTabTag($value);
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
        $keys = AdditionalTablesPeer::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) {
            $this->setAddTabUid($arr[$keys[0]]);
        }

        if (array_key_exists($keys[1], $arr)) {
            $this->setAddTabName($arr[$keys[1]]);
        }

        if (array_key_exists($keys[2], $arr)) {
            $this->setAddTabClassName($arr[$keys[2]]);
        }

        if (array_key_exists($keys[3], $arr)) {
            $this->setAddTabDescription($arr[$keys[3]]);
        }

        if (array_key_exists($keys[4], $arr)) {
            $this->setAddTabSdwLogInsert($arr[$keys[4]]);
        }

        if (array_key_exists($keys[5], $arr)) {
            $this->setAddTabSdwLogUpdate($arr[$keys[5]]);
        }

        if (array_key_exists($keys[6], $arr)) {
            $this->setAddTabSdwLogDelete($arr[$keys[6]]);
        }

        if (array_key_exists($keys[7], $arr)) {
            $this->setAddTabSdwLogSelect($arr[$keys[7]]);
        }

        if (array_key_exists($keys[8], $arr)) {
            $this->setAddTabSdwMaxLength($arr[$keys[8]]);
        }

        if (array_key_exists($keys[9], $arr)) {
            $this->setAddTabSdwAutoDelete($arr[$keys[9]]);
        }

        if (array_key_exists($keys[10], $arr)) {
            $this->setAddTabPlgUid($arr[$keys[10]]);
        }

        if (array_key_exists($keys[11], $arr)) {
            $this->setDbsUid($arr[$keys[11]]);
        }

        if (array_key_exists($keys[12], $arr)) {
            $this->setProUid($arr[$keys[12]]);
        }

        if (array_key_exists($keys[13], $arr)) {
            $this->setAddTabType($arr[$keys[13]]);
        }

        if (array_key_exists($keys[14], $arr)) {
            $this->setAddTabGrid($arr[$keys[14]]);
        }

        if (array_key_exists($keys[15], $arr)) {
            $this->setAddTabTag($arr[$keys[15]]);
        }

    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return     Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(AdditionalTablesPeer::DATABASE_NAME);

        if ($this->isColumnModified(AdditionalTablesPeer::ADD_TAB_UID)) {
            $criteria->add(AdditionalTablesPeer::ADD_TAB_UID, $this->add_tab_uid);
        }

        if ($this->isColumnModified(AdditionalTablesPeer::ADD_TAB_NAME)) {
            $criteria->add(AdditionalTablesPeer::ADD_TAB_NAME, $this->add_tab_name);
        }

        if ($this->isColumnModified(AdditionalTablesPeer::ADD_TAB_CLASS_NAME)) {
            $criteria->add(AdditionalTablesPeer::ADD_TAB_CLASS_NAME, $this->add_tab_class_name);
        }

        if ($this->isColumnModified(AdditionalTablesPeer::ADD_TAB_DESCRIPTION)) {
            $criteria->add(AdditionalTablesPeer::ADD_TAB_DESCRIPTION, $this->add_tab_description);
        }

        if ($this->isColumnModified(AdditionalTablesPeer::ADD_TAB_SDW_LOG_INSERT)) {
            $criteria->add(AdditionalTablesPeer::ADD_TAB_SDW_LOG_INSERT, $this->add_tab_sdw_log_insert);
        }

        if ($this->isColumnModified(AdditionalTablesPeer::ADD_TAB_SDW_LOG_UPDATE)) {
            $criteria->add(AdditionalTablesPeer::ADD_TAB_SDW_LOG_UPDATE, $this->add_tab_sdw_log_update);
        }

        if ($this->isColumnModified(AdditionalTablesPeer::ADD_TAB_SDW_LOG_DELETE)) {
            $criteria->add(AdditionalTablesPeer::ADD_TAB_SDW_LOG_DELETE, $this->add_tab_sdw_log_delete);
        }

        if ($this->isColumnModified(AdditionalTablesPeer::ADD_TAB_SDW_LOG_SELECT)) {
            $criteria->add(AdditionalTablesPeer::ADD_TAB_SDW_LOG_SELECT, $this->add_tab_sdw_log_select);
        }

        if ($this->isColumnModified(AdditionalTablesPeer::ADD_TAB_SDW_MAX_LENGTH)) {
            $criteria->add(AdditionalTablesPeer::ADD_TAB_SDW_MAX_LENGTH, $this->add_tab_sdw_max_length);
        }

        if ($this->isColumnModified(AdditionalTablesPeer::ADD_TAB_SDW_AUTO_DELETE)) {
            $criteria->add(AdditionalTablesPeer::ADD_TAB_SDW_AUTO_DELETE, $this->add_tab_sdw_auto_delete);
        }

        if ($this->isColumnModified(AdditionalTablesPeer::ADD_TAB_PLG_UID)) {
            $criteria->add(AdditionalTablesPeer::ADD_TAB_PLG_UID, $this->add_tab_plg_uid);
        }

        if ($this->isColumnModified(AdditionalTablesPeer::DBS_UID)) {
            $criteria->add(AdditionalTablesPeer::DBS_UID, $this->dbs_uid);
        }

        if ($this->isColumnModified(AdditionalTablesPeer::PRO_UID)) {
            $criteria->add(AdditionalTablesPeer::PRO_UID, $this->pro_uid);
        }

        if ($this->isColumnModified(AdditionalTablesPeer::ADD_TAB_TYPE)) {
            $criteria->add(AdditionalTablesPeer::ADD_TAB_TYPE, $this->add_tab_type);
        }

        if ($this->isColumnModified(AdditionalTablesPeer::ADD_TAB_GRID)) {
            $criteria->add(AdditionalTablesPeer::ADD_TAB_GRID, $this->add_tab_grid);
        }

        if ($this->isColumnModified(AdditionalTablesPeer::ADD_TAB_TAG)) {
            $criteria->add(AdditionalTablesPeer::ADD_TAB_TAG, $this->add_tab_tag);
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
        $criteria = new Criteria(AdditionalTablesPeer::DATABASE_NAME);

        $criteria->add(AdditionalTablesPeer::ADD_TAB_UID, $this->add_tab_uid);

        return $criteria;
    }

    /**
     * Returns the primary key for this object (row).
     * @return     string
     */
    public function getPrimaryKey()
    {
        return $this->getAddTabUid();
    }

    /**
     * Generic method to set the primary key (add_tab_uid column).
     *
     * @param      string $key Primary key.
     * @return     void
     */
    public function setPrimaryKey($key)
    {
        $this->setAddTabUid($key);
    }

    /**
     * Sets contents of passed object to values from current object.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param      object $copyObj An object of AdditionalTables (or compatible) type.
     * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @throws     PropelException
     */
    public function copyInto($copyObj, $deepCopy = false)
    {

        $copyObj->setAddTabName($this->add_tab_name);

        $copyObj->setAddTabClassName($this->add_tab_class_name);

        $copyObj->setAddTabDescription($this->add_tab_description);

        $copyObj->setAddTabSdwLogInsert($this->add_tab_sdw_log_insert);

        $copyObj->setAddTabSdwLogUpdate($this->add_tab_sdw_log_update);

        $copyObj->setAddTabSdwLogDelete($this->add_tab_sdw_log_delete);

        $copyObj->setAddTabSdwLogSelect($this->add_tab_sdw_log_select);

        $copyObj->setAddTabSdwMaxLength($this->add_tab_sdw_max_length);

        $copyObj->setAddTabSdwAutoDelete($this->add_tab_sdw_auto_delete);

        $copyObj->setAddTabPlgUid($this->add_tab_plg_uid);

        $copyObj->setDbsUid($this->dbs_uid);

        $copyObj->setProUid($this->pro_uid);

        $copyObj->setAddTabType($this->add_tab_type);

        $copyObj->setAddTabGrid($this->add_tab_grid);

        $copyObj->setAddTabTag($this->add_tab_tag);


        $copyObj->setNew(true);

        $copyObj->setAddTabUid(''); // this is a pkey column, so set to default value

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
     * @return     AdditionalTables Clone of current object.
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
     * @return     AdditionalTablesPeer
     */
    public function getPeer()
    {
        if (self::$peer === null) {
            self::$peer = new AdditionalTablesPeer();
        }
        return self::$peer;
    }
}

