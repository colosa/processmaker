<?php

require_once 'propel/om/BaseObject.php';

require_once 'propel/om/Persistent.php';


include_once 'propel/util/Criteria.php';

include_once 'classes/model/DashletInstancePeer.php';

/**
 * Base class that represents a row from the 'DASHLET_INSTANCE' table.
 *
 * 
 *
 * @package    workflow.classes.model.om
 */
abstract class BaseDashletInstance extends BaseObject implements Persistent
{

    /**
     * The Peer class.
     * Instance provides a convenient way of calling static methods on a class
     * that calling code may not be able to identify.
     * @var        DashletInstancePeer
    */
    protected static $peer;

    /**
     * The value for the das_ins_uid field.
     * @var        string
     */
    protected $das_ins_uid = '';

    /**
     * The value for the das_uid field.
     * @var        string
     */
    protected $das_uid = '';

    /**
     * The value for the das_ins_owner_type field.
     * @var        string
     */
    protected $das_ins_owner_type = '';

    /**
     * The value for the das_ins_owner_uid field.
     * @var        string
     */
    protected $das_ins_owner_uid = '';

    /**
     * The value for the das_ins_additional_properties field.
     * @var        string
     */
    protected $das_ins_additional_properties;

    /**
     * The value for the das_ins_create_date field.
     * @var        int
     */
    protected $das_ins_create_date;

    /**
     * The value for the das_ins_update_date field.
     * @var        int
     */
    protected $das_ins_update_date;

    /**
     * The value for the das_ins_status field.
     * @var        int
     */
    protected $das_ins_status = 1;

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
     * Get the [das_ins_uid] column value.
     * 
     * @return     string
     */
    public function getDasInsUid()
    {

        return $this->das_ins_uid;
    }

    /**
     * Get the [das_uid] column value.
     * 
     * @return     string
     */
    public function getDasUid()
    {

        return $this->das_uid;
    }

    /**
     * Get the [das_ins_owner_type] column value.
     * 
     * @return     string
     */
    public function getDasInsOwnerType()
    {

        return $this->das_ins_owner_type;
    }

    /**
     * Get the [das_ins_owner_uid] column value.
     * 
     * @return     string
     */
    public function getDasInsOwnerUid()
    {

        return $this->das_ins_owner_uid;
    }

    /**
     * Get the [das_ins_additional_properties] column value.
     * 
     * @return     string
     */
    public function getDasInsAdditionalProperties()
    {

        return $this->das_ins_additional_properties;
    }

    /**
     * Get the [optionally formatted] [das_ins_create_date] column value.
     * 
     * @param      string $format The date/time format string (either date()-style or strftime()-style).
     *                          If format is NULL, then the integer unix timestamp will be returned.
     * @return     mixed Formatted date/time value as string or integer unix timestamp (if format is NULL).
     * @throws     PropelException - if unable to convert the date/time to timestamp.
     */
    public function getDasInsCreateDate($format = 'Y-m-d H:i:s')
    {

        if ($this->das_ins_create_date === null || $this->das_ins_create_date === '') {
            return null;
        } elseif (!is_int($this->das_ins_create_date)) {
            // a non-timestamp value was set externally, so we convert it
            $ts = strtotime($this->das_ins_create_date);
            if ($ts === -1 || $ts === false) {
                throw new PropelException("Unable to parse value of [das_ins_create_date] as date/time value: " .
                    var_export($this->das_ins_create_date, true));
            }
        } else {
            $ts = $this->das_ins_create_date;
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
     * Get the [optionally formatted] [das_ins_update_date] column value.
     * 
     * @param      string $format The date/time format string (either date()-style or strftime()-style).
     *                          If format is NULL, then the integer unix timestamp will be returned.
     * @return     mixed Formatted date/time value as string or integer unix timestamp (if format is NULL).
     * @throws     PropelException - if unable to convert the date/time to timestamp.
     */
    public function getDasInsUpdateDate($format = 'Y-m-d H:i:s')
    {

        if ($this->das_ins_update_date === null || $this->das_ins_update_date === '') {
            return null;
        } elseif (!is_int($this->das_ins_update_date)) {
            // a non-timestamp value was set externally, so we convert it
            $ts = strtotime($this->das_ins_update_date);
            if ($ts === -1 || $ts === false) {
                throw new PropelException("Unable to parse value of [das_ins_update_date] as date/time value: " .
                    var_export($this->das_ins_update_date, true));
            }
        } else {
            $ts = $this->das_ins_update_date;
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
     * Get the [das_ins_status] column value.
     * 
     * @return     int
     */
    public function getDasInsStatus()
    {

        return $this->das_ins_status;
    }

    /**
     * Set the value of [das_ins_uid] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setDasInsUid($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->das_ins_uid !== $v || $v === '') {
            $this->das_ins_uid = $v;
            $this->modifiedColumns[] = DashletInstancePeer::DAS_INS_UID;
        }

    } // setDasInsUid()

    /**
     * Set the value of [das_uid] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setDasUid($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->das_uid !== $v || $v === '') {
            $this->das_uid = $v;
            $this->modifiedColumns[] = DashletInstancePeer::DAS_UID;
        }

    } // setDasUid()

    /**
     * Set the value of [das_ins_owner_type] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setDasInsOwnerType($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->das_ins_owner_type !== $v || $v === '') {
            $this->das_ins_owner_type = $v;
            $this->modifiedColumns[] = DashletInstancePeer::DAS_INS_OWNER_TYPE;
        }

    } // setDasInsOwnerType()

    /**
     * Set the value of [das_ins_owner_uid] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setDasInsOwnerUid($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->das_ins_owner_uid !== $v || $v === '') {
            $this->das_ins_owner_uid = $v;
            $this->modifiedColumns[] = DashletInstancePeer::DAS_INS_OWNER_UID;
        }

    } // setDasInsOwnerUid()

    /**
     * Set the value of [das_ins_additional_properties] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setDasInsAdditionalProperties($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->das_ins_additional_properties !== $v) {
            $this->das_ins_additional_properties = $v;
            $this->modifiedColumns[] = DashletInstancePeer::DAS_INS_ADDITIONAL_PROPERTIES;
        }

    } // setDasInsAdditionalProperties()

    /**
     * Set the value of [das_ins_create_date] column.
     * 
     * @param      int $v new value
     * @return     void
     */
    public function setDasInsCreateDate($v)
    {

        if ($v !== null && !is_int($v)) {
            $ts = strtotime($v);
            if ($ts === -1 || $ts === false) {
                throw new PropelException("Unable to parse date/time value for [das_ins_create_date] from input: " .
                    var_export($v, true));
            }
        } else {
            $ts = $v;
        }
        if ($this->das_ins_create_date !== $ts) {
            $this->das_ins_create_date = $ts;
            $this->modifiedColumns[] = DashletInstancePeer::DAS_INS_CREATE_DATE;
        }

    } // setDasInsCreateDate()

    /**
     * Set the value of [das_ins_update_date] column.
     * 
     * @param      int $v new value
     * @return     void
     */
    public function setDasInsUpdateDate($v)
    {

        if ($v !== null && !is_int($v)) {
            $ts = strtotime($v);
            if ($ts === -1 || $ts === false) {
                throw new PropelException("Unable to parse date/time value for [das_ins_update_date] from input: " .
                    var_export($v, true));
            }
        } else {
            $ts = $v;
        }
        if ($this->das_ins_update_date !== $ts) {
            $this->das_ins_update_date = $ts;
            $this->modifiedColumns[] = DashletInstancePeer::DAS_INS_UPDATE_DATE;
        }

    } // setDasInsUpdateDate()

    /**
     * Set the value of [das_ins_status] column.
     * 
     * @param      int $v new value
     * @return     void
     */
    public function setDasInsStatus($v)
    {

        // Since the native PHP type for this column is integer,
        // we will cast the input value to an int (if it is not).
        if ($v !== null && !is_int($v) && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->das_ins_status !== $v || $v === 1) {
            $this->das_ins_status = $v;
            $this->modifiedColumns[] = DashletInstancePeer::DAS_INS_STATUS;
        }

    } // setDasInsStatus()

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

            $this->das_ins_uid = $rs->getString($startcol + 0);

            $this->das_uid = $rs->getString($startcol + 1);

            $this->das_ins_owner_type = $rs->getString($startcol + 2);

            $this->das_ins_owner_uid = $rs->getString($startcol + 3);

            $this->das_ins_additional_properties = $rs->getString($startcol + 4);

            $this->das_ins_create_date = $rs->getTimestamp($startcol + 5, null);

            $this->das_ins_update_date = $rs->getTimestamp($startcol + 6, null);

            $this->das_ins_status = $rs->getInt($startcol + 7);

            $this->resetModified();

            $this->setNew(false);

            // FIXME - using NUM_COLUMNS may be clearer.
            return $startcol + 8; // 8 = DashletInstancePeer::NUM_COLUMNS - DashletInstancePeer::NUM_LAZY_LOAD_COLUMNS).

        } catch (Exception $e) {
            throw new PropelException("Error populating DashletInstance object", $e);
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
            $con = Propel::getConnection(DashletInstancePeer::DATABASE_NAME);
        }

        try {
            $con->begin();
            DashletInstancePeer::doDelete($this, $con);
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
            $con = Propel::getConnection(DashletInstancePeer::DATABASE_NAME);
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
                    $pk = DashletInstancePeer::doInsert($this, $con);
                    $affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
                                         // should always be true here (even though technically
                                         // BasePeer::doInsert() can insert multiple rows).

                    $this->setNew(false);
                } else {
                    $affectedRows += DashletInstancePeer::doUpdate($this, $con);
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


            if (($retval = DashletInstancePeer::doValidate($this, $columns)) !== true) {
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
        $pos = DashletInstancePeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                return $this->getDasInsUid();
                break;
            case 1:
                return $this->getDasUid();
                break;
            case 2:
                return $this->getDasInsOwnerType();
                break;
            case 3:
                return $this->getDasInsOwnerUid();
                break;
            case 4:
                return $this->getDasInsAdditionalProperties();
                break;
            case 5:
                return $this->getDasInsCreateDate();
                break;
            case 6:
                return $this->getDasInsUpdateDate();
                break;
            case 7:
                return $this->getDasInsStatus();
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
        $keys = DashletInstancePeer::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getDasInsUid(),
            $keys[1] => $this->getDasUid(),
            $keys[2] => $this->getDasInsOwnerType(),
            $keys[3] => $this->getDasInsOwnerUid(),
            $keys[4] => $this->getDasInsAdditionalProperties(),
            $keys[5] => $this->getDasInsCreateDate(),
            $keys[6] => $this->getDasInsUpdateDate(),
            $keys[7] => $this->getDasInsStatus(),
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
        $pos = DashletInstancePeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                $this->setDasInsUid($value);
                break;
            case 1:
                $this->setDasUid($value);
                break;
            case 2:
                $this->setDasInsOwnerType($value);
                break;
            case 3:
                $this->setDasInsOwnerUid($value);
                break;
            case 4:
                $this->setDasInsAdditionalProperties($value);
                break;
            case 5:
                $this->setDasInsCreateDate($value);
                break;
            case 6:
                $this->setDasInsUpdateDate($value);
                break;
            case 7:
                $this->setDasInsStatus($value);
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
        $keys = DashletInstancePeer::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) {
            $this->setDasInsUid($arr[$keys[0]]);
        }

        if (array_key_exists($keys[1], $arr)) {
            $this->setDasUid($arr[$keys[1]]);
        }

        if (array_key_exists($keys[2], $arr)) {
            $this->setDasInsOwnerType($arr[$keys[2]]);
        }

        if (array_key_exists($keys[3], $arr)) {
            $this->setDasInsOwnerUid($arr[$keys[3]]);
        }

        if (array_key_exists($keys[4], $arr)) {
            $this->setDasInsAdditionalProperties($arr[$keys[4]]);
        }

        if (array_key_exists($keys[5], $arr)) {
            $this->setDasInsCreateDate($arr[$keys[5]]);
        }

        if (array_key_exists($keys[6], $arr)) {
            $this->setDasInsUpdateDate($arr[$keys[6]]);
        }

        if (array_key_exists($keys[7], $arr)) {
            $this->setDasInsStatus($arr[$keys[7]]);
        }

    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return     Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(DashletInstancePeer::DATABASE_NAME);

        if ($this->isColumnModified(DashletInstancePeer::DAS_INS_UID)) {
            $criteria->add(DashletInstancePeer::DAS_INS_UID, $this->das_ins_uid);
        }

        if ($this->isColumnModified(DashletInstancePeer::DAS_UID)) {
            $criteria->add(DashletInstancePeer::DAS_UID, $this->das_uid);
        }

        if ($this->isColumnModified(DashletInstancePeer::DAS_INS_OWNER_TYPE)) {
            $criteria->add(DashletInstancePeer::DAS_INS_OWNER_TYPE, $this->das_ins_owner_type);
        }

        if ($this->isColumnModified(DashletInstancePeer::DAS_INS_OWNER_UID)) {
            $criteria->add(DashletInstancePeer::DAS_INS_OWNER_UID, $this->das_ins_owner_uid);
        }

        if ($this->isColumnModified(DashletInstancePeer::DAS_INS_ADDITIONAL_PROPERTIES)) {
            $criteria->add(DashletInstancePeer::DAS_INS_ADDITIONAL_PROPERTIES, $this->das_ins_additional_properties);
        }

        if ($this->isColumnModified(DashletInstancePeer::DAS_INS_CREATE_DATE)) {
            $criteria->add(DashletInstancePeer::DAS_INS_CREATE_DATE, $this->das_ins_create_date);
        }

        if ($this->isColumnModified(DashletInstancePeer::DAS_INS_UPDATE_DATE)) {
            $criteria->add(DashletInstancePeer::DAS_INS_UPDATE_DATE, $this->das_ins_update_date);
        }

        if ($this->isColumnModified(DashletInstancePeer::DAS_INS_STATUS)) {
            $criteria->add(DashletInstancePeer::DAS_INS_STATUS, $this->das_ins_status);
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
        $criteria = new Criteria(DashletInstancePeer::DATABASE_NAME);

        $criteria->add(DashletInstancePeer::DAS_INS_UID, $this->das_ins_uid);

        return $criteria;
    }

    /**
     * Returns the primary key for this object (row).
     * @return     string
     */
    public function getPrimaryKey()
    {
        return $this->getDasInsUid();
    }

    /**
     * Generic method to set the primary key (das_ins_uid column).
     *
     * @param      string $key Primary key.
     * @return     void
     */
    public function setPrimaryKey($key)
    {
        $this->setDasInsUid($key);
    }

    /**
     * Sets contents of passed object to values from current object.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param      object $copyObj An object of DashletInstance (or compatible) type.
     * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @throws     PropelException
     */
    public function copyInto($copyObj, $deepCopy = false)
    {

        $copyObj->setDasUid($this->das_uid);

        $copyObj->setDasInsOwnerType($this->das_ins_owner_type);

        $copyObj->setDasInsOwnerUid($this->das_ins_owner_uid);

        $copyObj->setDasInsAdditionalProperties($this->das_ins_additional_properties);

        $copyObj->setDasInsCreateDate($this->das_ins_create_date);

        $copyObj->setDasInsUpdateDate($this->das_ins_update_date);

        $copyObj->setDasInsStatus($this->das_ins_status);


        $copyObj->setNew(true);

        $copyObj->setDasInsUid(''); // this is a pkey column, so set to default value

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
     * @return     DashletInstance Clone of current object.
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
     * @return     DashletInstancePeer
     */
    public function getPeer()
    {
        if (self::$peer === null) {
            self::$peer = new DashletInstancePeer();
        }
        return self::$peer;
    }
}

