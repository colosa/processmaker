<?php

require_once 'propel/om/BaseObject.php';

require_once 'propel/om/Persistent.php';


include_once 'propel/util/Criteria.php';

include_once 'classes/model/ReportTablePeer.php';

/**
 * Base class that represents a row from the 'REPORT_TABLE' table.
 *
 * 
 *
 * @package    workflow.classes.model.om
 */
abstract class BaseReportTable extends BaseObject implements Persistent
{

    /**
     * The Peer class.
     * Instance provides a convenient way of calling static methods on a class
     * that calling code may not be able to identify.
     * @var        ReportTablePeer
    */
    protected static $peer;

    /**
     * The value for the rep_tab_uid field.
     * @var        string
     */
    protected $rep_tab_uid = '';

    /**
     * The value for the pro_uid field.
     * @var        string
     */
    protected $pro_uid = '';

    /**
     * The value for the rep_tab_name field.
     * @var        string
     */
    protected $rep_tab_name = '';

    /**
     * The value for the rep_tab_type field.
     * @var        string
     */
    protected $rep_tab_type = '';

    /**
     * The value for the rep_tab_grid field.
     * @var        string
     */
    protected $rep_tab_grid = '';

    /**
     * The value for the rep_tab_connection field.
     * @var        string
     */
    protected $rep_tab_connection = '';

    /**
     * The value for the rep_tab_create_date field.
     * @var        int
     */
    protected $rep_tab_create_date;

    /**
     * The value for the rep_tab_status field.
     * @var        string
     */
    protected $rep_tab_status = 'ACTIVE';

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
     * Get the [rep_tab_uid] column value.
     * 
     * @return     string
     */
    public function getRepTabUid()
    {

        return $this->rep_tab_uid;
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
     * Get the [rep_tab_name] column value.
     * 
     * @return     string
     */
    public function getRepTabName()
    {

        return $this->rep_tab_name;
    }

    /**
     * Get the [rep_tab_type] column value.
     * 
     * @return     string
     */
    public function getRepTabType()
    {

        return $this->rep_tab_type;
    }

    /**
     * Get the [rep_tab_grid] column value.
     * 
     * @return     string
     */
    public function getRepTabGrid()
    {

        return $this->rep_tab_grid;
    }

    /**
     * Get the [rep_tab_connection] column value.
     * 
     * @return     string
     */
    public function getRepTabConnection()
    {

        return $this->rep_tab_connection;
    }

    /**
     * Get the [optionally formatted] [rep_tab_create_date] column value.
     * 
     * @param      string $format The date/time format string (either date()-style or strftime()-style).
     *                          If format is NULL, then the integer unix timestamp will be returned.
     * @return     mixed Formatted date/time value as string or integer unix timestamp (if format is NULL).
     * @throws     PropelException - if unable to convert the date/time to timestamp.
     */
    public function getRepTabCreateDate($format = 'Y-m-d H:i:s')
    {

        if ($this->rep_tab_create_date === null || $this->rep_tab_create_date === '') {
            return null;
        } elseif (!is_int($this->rep_tab_create_date)) {
            // a non-timestamp value was set externally, so we convert it
            $ts = strtotime($this->rep_tab_create_date);
            if ($ts === -1 || $ts === false) {
                throw new PropelException("Unable to parse value of [rep_tab_create_date] as date/time value: " .
                    var_export($this->rep_tab_create_date, true));
            }
        } else {
            $ts = $this->rep_tab_create_date;
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
     * Get the [rep_tab_status] column value.
     * 
     * @return     string
     */
    public function getRepTabStatus()
    {

        return $this->rep_tab_status;
    }

    /**
     * Set the value of [rep_tab_uid] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setRepTabUid($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->rep_tab_uid !== $v || $v === '') {
            $this->rep_tab_uid = $v;
            $this->modifiedColumns[] = ReportTablePeer::REP_TAB_UID;
        }

    } // setRepTabUid()

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
            $this->modifiedColumns[] = ReportTablePeer::PRO_UID;
        }

    } // setProUid()

    /**
     * Set the value of [rep_tab_name] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setRepTabName($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->rep_tab_name !== $v || $v === '') {
            $this->rep_tab_name = $v;
            $this->modifiedColumns[] = ReportTablePeer::REP_TAB_NAME;
        }

    } // setRepTabName()

    /**
     * Set the value of [rep_tab_type] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setRepTabType($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->rep_tab_type !== $v || $v === '') {
            $this->rep_tab_type = $v;
            $this->modifiedColumns[] = ReportTablePeer::REP_TAB_TYPE;
        }

    } // setRepTabType()

    /**
     * Set the value of [rep_tab_grid] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setRepTabGrid($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->rep_tab_grid !== $v || $v === '') {
            $this->rep_tab_grid = $v;
            $this->modifiedColumns[] = ReportTablePeer::REP_TAB_GRID;
        }

    } // setRepTabGrid()

    /**
     * Set the value of [rep_tab_connection] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setRepTabConnection($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->rep_tab_connection !== $v || $v === '') {
            $this->rep_tab_connection = $v;
            $this->modifiedColumns[] = ReportTablePeer::REP_TAB_CONNECTION;
        }

    } // setRepTabConnection()

    /**
     * Set the value of [rep_tab_create_date] column.
     * 
     * @param      int $v new value
     * @return     void
     */
    public function setRepTabCreateDate($v)
    {

        if ($v !== null && !is_int($v)) {
            $ts = strtotime($v);
            if ($ts === -1 || $ts === false) {
                throw new PropelException("Unable to parse date/time value for [rep_tab_create_date] from input: " .
                    var_export($v, true));
            }
        } else {
            $ts = $v;
        }
        if ($this->rep_tab_create_date !== $ts) {
            $this->rep_tab_create_date = $ts;
            $this->modifiedColumns[] = ReportTablePeer::REP_TAB_CREATE_DATE;
        }

    } // setRepTabCreateDate()

    /**
     * Set the value of [rep_tab_status] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setRepTabStatus($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->rep_tab_status !== $v || $v === 'ACTIVE') {
            $this->rep_tab_status = $v;
            $this->modifiedColumns[] = ReportTablePeer::REP_TAB_STATUS;
        }

    } // setRepTabStatus()

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

            $this->rep_tab_uid = $rs->getString($startcol + 0);

            $this->pro_uid = $rs->getString($startcol + 1);

            $this->rep_tab_name = $rs->getString($startcol + 2);

            $this->rep_tab_type = $rs->getString($startcol + 3);

            $this->rep_tab_grid = $rs->getString($startcol + 4);

            $this->rep_tab_connection = $rs->getString($startcol + 5);

            $this->rep_tab_create_date = $rs->getTimestamp($startcol + 6, null);

            $this->rep_tab_status = $rs->getString($startcol + 7);

            $this->resetModified();

            $this->setNew(false);

            // FIXME - using NUM_COLUMNS may be clearer.
            return $startcol + 8; // 8 = ReportTablePeer::NUM_COLUMNS - ReportTablePeer::NUM_LAZY_LOAD_COLUMNS).

        } catch (Exception $e) {
            throw new PropelException("Error populating ReportTable object", $e);
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
            $con = Propel::getConnection(ReportTablePeer::DATABASE_NAME);
        }

        try {
            $con->begin();
            ReportTablePeer::doDelete($this, $con);
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
            $con = Propel::getConnection(ReportTablePeer::DATABASE_NAME);
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
                    $pk = ReportTablePeer::doInsert($this, $con);
                    $affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
                                         // should always be true here (even though technically
                                         // BasePeer::doInsert() can insert multiple rows).

                    $this->setNew(false);
                } else {
                    $affectedRows += ReportTablePeer::doUpdate($this, $con);
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


            if (($retval = ReportTablePeer::doValidate($this, $columns)) !== true) {
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
        $pos = ReportTablePeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                return $this->getRepTabUid();
                break;
            case 1:
                return $this->getProUid();
                break;
            case 2:
                return $this->getRepTabName();
                break;
            case 3:
                return $this->getRepTabType();
                break;
            case 4:
                return $this->getRepTabGrid();
                break;
            case 5:
                return $this->getRepTabConnection();
                break;
            case 6:
                return $this->getRepTabCreateDate();
                break;
            case 7:
                return $this->getRepTabStatus();
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
        $keys = ReportTablePeer::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getRepTabUid(),
            $keys[1] => $this->getProUid(),
            $keys[2] => $this->getRepTabName(),
            $keys[3] => $this->getRepTabType(),
            $keys[4] => $this->getRepTabGrid(),
            $keys[5] => $this->getRepTabConnection(),
            $keys[6] => $this->getRepTabCreateDate(),
            $keys[7] => $this->getRepTabStatus(),
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
        $pos = ReportTablePeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                $this->setRepTabUid($value);
                break;
            case 1:
                $this->setProUid($value);
                break;
            case 2:
                $this->setRepTabName($value);
                break;
            case 3:
                $this->setRepTabType($value);
                break;
            case 4:
                $this->setRepTabGrid($value);
                break;
            case 5:
                $this->setRepTabConnection($value);
                break;
            case 6:
                $this->setRepTabCreateDate($value);
                break;
            case 7:
                $this->setRepTabStatus($value);
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
        $keys = ReportTablePeer::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) {
            $this->setRepTabUid($arr[$keys[0]]);
        }

        if (array_key_exists($keys[1], $arr)) {
            $this->setProUid($arr[$keys[1]]);
        }

        if (array_key_exists($keys[2], $arr)) {
            $this->setRepTabName($arr[$keys[2]]);
        }

        if (array_key_exists($keys[3], $arr)) {
            $this->setRepTabType($arr[$keys[3]]);
        }

        if (array_key_exists($keys[4], $arr)) {
            $this->setRepTabGrid($arr[$keys[4]]);
        }

        if (array_key_exists($keys[5], $arr)) {
            $this->setRepTabConnection($arr[$keys[5]]);
        }

        if (array_key_exists($keys[6], $arr)) {
            $this->setRepTabCreateDate($arr[$keys[6]]);
        }

        if (array_key_exists($keys[7], $arr)) {
            $this->setRepTabStatus($arr[$keys[7]]);
        }

    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return     Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(ReportTablePeer::DATABASE_NAME);

        if ($this->isColumnModified(ReportTablePeer::REP_TAB_UID)) {
            $criteria->add(ReportTablePeer::REP_TAB_UID, $this->rep_tab_uid);
        }

        if ($this->isColumnModified(ReportTablePeer::PRO_UID)) {
            $criteria->add(ReportTablePeer::PRO_UID, $this->pro_uid);
        }

        if ($this->isColumnModified(ReportTablePeer::REP_TAB_NAME)) {
            $criteria->add(ReportTablePeer::REP_TAB_NAME, $this->rep_tab_name);
        }

        if ($this->isColumnModified(ReportTablePeer::REP_TAB_TYPE)) {
            $criteria->add(ReportTablePeer::REP_TAB_TYPE, $this->rep_tab_type);
        }

        if ($this->isColumnModified(ReportTablePeer::REP_TAB_GRID)) {
            $criteria->add(ReportTablePeer::REP_TAB_GRID, $this->rep_tab_grid);
        }

        if ($this->isColumnModified(ReportTablePeer::REP_TAB_CONNECTION)) {
            $criteria->add(ReportTablePeer::REP_TAB_CONNECTION, $this->rep_tab_connection);
        }

        if ($this->isColumnModified(ReportTablePeer::REP_TAB_CREATE_DATE)) {
            $criteria->add(ReportTablePeer::REP_TAB_CREATE_DATE, $this->rep_tab_create_date);
        }

        if ($this->isColumnModified(ReportTablePeer::REP_TAB_STATUS)) {
            $criteria->add(ReportTablePeer::REP_TAB_STATUS, $this->rep_tab_status);
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
        $criteria = new Criteria(ReportTablePeer::DATABASE_NAME);

        $criteria->add(ReportTablePeer::REP_TAB_UID, $this->rep_tab_uid);

        return $criteria;
    }

    /**
     * Returns the primary key for this object (row).
     * @return     string
     */
    public function getPrimaryKey()
    {
        return $this->getRepTabUid();
    }

    /**
     * Generic method to set the primary key (rep_tab_uid column).
     *
     * @param      string $key Primary key.
     * @return     void
     */
    public function setPrimaryKey($key)
    {
        $this->setRepTabUid($key);
    }

    /**
     * Sets contents of passed object to values from current object.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param      object $copyObj An object of ReportTable (or compatible) type.
     * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @throws     PropelException
     */
    public function copyInto($copyObj, $deepCopy = false)
    {

        $copyObj->setProUid($this->pro_uid);

        $copyObj->setRepTabName($this->rep_tab_name);

        $copyObj->setRepTabType($this->rep_tab_type);

        $copyObj->setRepTabGrid($this->rep_tab_grid);

        $copyObj->setRepTabConnection($this->rep_tab_connection);

        $copyObj->setRepTabCreateDate($this->rep_tab_create_date);

        $copyObj->setRepTabStatus($this->rep_tab_status);


        $copyObj->setNew(true);

        $copyObj->setRepTabUid(''); // this is a pkey column, so set to default value

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
     * @return     ReportTable Clone of current object.
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
     * @return     ReportTablePeer
     */
    public function getPeer()
    {
        if (self::$peer === null) {
            self::$peer = new ReportTablePeer();
        }
        return self::$peer;
    }
}

