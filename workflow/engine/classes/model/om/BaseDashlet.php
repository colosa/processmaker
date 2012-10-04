<?php

require_once 'propel/om/BaseObject.php';

require_once 'propel/om/Persistent.php';


include_once 'propel/util/Criteria.php';

include_once 'classes/model/DashletPeer.php';

/**
 * Base class that represents a row from the 'DASHLET' table.
 *
 * 
 *
 * @package    workflow.classes.model.om
 */
abstract class BaseDashlet extends BaseObject implements Persistent
{

    /**
     * The Peer class.
     * Instance provides a convenient way of calling static methods on a class
     * that calling code may not be able to identify.
     * @var        DashletPeer
    */
    protected static $peer;

    /**
     * The value for the das_uid field.
     * @var        string
     */
    protected $das_uid = '';

    /**
     * The value for the das_class field.
     * @var        string
     */
    protected $das_class = '';

    /**
     * The value for the das_title field.
     * @var        string
     */
    protected $das_title = '';

    /**
     * The value for the das_description field.
     * @var        string
     */
    protected $das_description;

    /**
     * The value for the das_version field.
     * @var        string
     */
    protected $das_version = '1.0';

    /**
     * The value for the das_create_date field.
     * @var        int
     */
    protected $das_create_date;

    /**
     * The value for the das_update_date field.
     * @var        int
     */
    protected $das_update_date;

    /**
     * The value for the das_status field.
     * @var        int
     */
    protected $das_status = 1;

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
     * Get the [das_uid] column value.
     * 
     * @return     string
     */
    public function getDasUid()
    {

        return $this->das_uid;
    }

    /**
     * Get the [das_class] column value.
     * 
     * @return     string
     */
    public function getDasClass()
    {

        return $this->das_class;
    }

    /**
     * Get the [das_title] column value.
     * 
     * @return     string
     */
    public function getDasTitle()
    {

        return $this->das_title;
    }

    /**
     * Get the [das_description] column value.
     * 
     * @return     string
     */
    public function getDasDescription()
    {

        return $this->das_description;
    }

    /**
     * Get the [das_version] column value.
     * 
     * @return     string
     */
    public function getDasVersion()
    {

        return $this->das_version;
    }

    /**
     * Get the [optionally formatted] [das_create_date] column value.
     * 
     * @param      string $format The date/time format string (either date()-style or strftime()-style).
     *                          If format is NULL, then the integer unix timestamp will be returned.
     * @return     mixed Formatted date/time value as string or integer unix timestamp (if format is NULL).
     * @throws     PropelException - if unable to convert the date/time to timestamp.
     */
    public function getDasCreateDate($format = 'Y-m-d H:i:s')
    {

        if ($this->das_create_date === null || $this->das_create_date === '') {
            return null;
        } elseif (!is_int($this->das_create_date)) {
            // a non-timestamp value was set externally, so we convert it
            $ts = strtotime($this->das_create_date);
            if ($ts === -1 || $ts === false) {
                throw new PropelException("Unable to parse value of [das_create_date] as date/time value: " .
                    var_export($this->das_create_date, true));
            }
        } else {
            $ts = $this->das_create_date;
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
     * Get the [optionally formatted] [das_update_date] column value.
     * 
     * @param      string $format The date/time format string (either date()-style or strftime()-style).
     *                          If format is NULL, then the integer unix timestamp will be returned.
     * @return     mixed Formatted date/time value as string or integer unix timestamp (if format is NULL).
     * @throws     PropelException - if unable to convert the date/time to timestamp.
     */
    public function getDasUpdateDate($format = 'Y-m-d H:i:s')
    {

        if ($this->das_update_date === null || $this->das_update_date === '') {
            return null;
        } elseif (!is_int($this->das_update_date)) {
            // a non-timestamp value was set externally, so we convert it
            $ts = strtotime($this->das_update_date);
            if ($ts === -1 || $ts === false) {
                throw new PropelException("Unable to parse value of [das_update_date] as date/time value: " .
                    var_export($this->das_update_date, true));
            }
        } else {
            $ts = $this->das_update_date;
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
     * Get the [das_status] column value.
     * 
     * @return     int
     */
    public function getDasStatus()
    {

        return $this->das_status;
    }

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
            $this->modifiedColumns[] = DashletPeer::DAS_UID;
        }

    } // setDasUid()

    /**
     * Set the value of [das_class] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setDasClass($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->das_class !== $v || $v === '') {
            $this->das_class = $v;
            $this->modifiedColumns[] = DashletPeer::DAS_CLASS;
        }

    } // setDasClass()

    /**
     * Set the value of [das_title] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setDasTitle($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->das_title !== $v || $v === '') {
            $this->das_title = $v;
            $this->modifiedColumns[] = DashletPeer::DAS_TITLE;
        }

    } // setDasTitle()

    /**
     * Set the value of [das_description] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setDasDescription($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->das_description !== $v) {
            $this->das_description = $v;
            $this->modifiedColumns[] = DashletPeer::DAS_DESCRIPTION;
        }

    } // setDasDescription()

    /**
     * Set the value of [das_version] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setDasVersion($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->das_version !== $v || $v === '1.0') {
            $this->das_version = $v;
            $this->modifiedColumns[] = DashletPeer::DAS_VERSION;
        }

    } // setDasVersion()

    /**
     * Set the value of [das_create_date] column.
     * 
     * @param      int $v new value
     * @return     void
     */
    public function setDasCreateDate($v)
    {

        if ($v !== null && !is_int($v)) {
            $ts = strtotime($v);
            if ($ts === -1 || $ts === false) {
                throw new PropelException("Unable to parse date/time value for [das_create_date] from input: " .
                    var_export($v, true));
            }
        } else {
            $ts = $v;
        }
        if ($this->das_create_date !== $ts) {
            $this->das_create_date = $ts;
            $this->modifiedColumns[] = DashletPeer::DAS_CREATE_DATE;
        }

    } // setDasCreateDate()

    /**
     * Set the value of [das_update_date] column.
     * 
     * @param      int $v new value
     * @return     void
     */
    public function setDasUpdateDate($v)
    {

        if ($v !== null && !is_int($v)) {
            $ts = strtotime($v);
            if ($ts === -1 || $ts === false) {
                throw new PropelException("Unable to parse date/time value for [das_update_date] from input: " .
                    var_export($v, true));
            }
        } else {
            $ts = $v;
        }
        if ($this->das_update_date !== $ts) {
            $this->das_update_date = $ts;
            $this->modifiedColumns[] = DashletPeer::DAS_UPDATE_DATE;
        }

    } // setDasUpdateDate()

    /**
     * Set the value of [das_status] column.
     * 
     * @param      int $v new value
     * @return     void
     */
    public function setDasStatus($v)
    {

        // Since the native PHP type for this column is integer,
        // we will cast the input value to an int (if it is not).
        if ($v !== null && !is_int($v) && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->das_status !== $v || $v === 1) {
            $this->das_status = $v;
            $this->modifiedColumns[] = DashletPeer::DAS_STATUS;
        }

    } // setDasStatus()

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

            $this->das_uid = $rs->getString($startcol + 0);

            $this->das_class = $rs->getString($startcol + 1);

            $this->das_title = $rs->getString($startcol + 2);

            $this->das_description = $rs->getString($startcol + 3);

            $this->das_version = $rs->getString($startcol + 4);

            $this->das_create_date = $rs->getTimestamp($startcol + 5, null);

            $this->das_update_date = $rs->getTimestamp($startcol + 6, null);

            $this->das_status = $rs->getInt($startcol + 7);

            $this->resetModified();

            $this->setNew(false);

            // FIXME - using NUM_COLUMNS may be clearer.
            return $startcol + 8; // 8 = DashletPeer::NUM_COLUMNS - DashletPeer::NUM_LAZY_LOAD_COLUMNS).

        } catch (Exception $e) {
            throw new PropelException("Error populating Dashlet object", $e);
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
            $con = Propel::getConnection(DashletPeer::DATABASE_NAME);
        }

        try {
            $con->begin();
            DashletPeer::doDelete($this, $con);
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
            $con = Propel::getConnection(DashletPeer::DATABASE_NAME);
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
                    $pk = DashletPeer::doInsert($this, $con);
                    $affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
                                         // should always be true here (even though technically
                                         // BasePeer::doInsert() can insert multiple rows).

                    $this->setNew(false);
                } else {
                    $affectedRows += DashletPeer::doUpdate($this, $con);
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


            if (($retval = DashletPeer::doValidate($this, $columns)) !== true) {
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
        $pos = DashletPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                return $this->getDasUid();
                break;
            case 1:
                return $this->getDasClass();
                break;
            case 2:
                return $this->getDasTitle();
                break;
            case 3:
                return $this->getDasDescription();
                break;
            case 4:
                return $this->getDasVersion();
                break;
            case 5:
                return $this->getDasCreateDate();
                break;
            case 6:
                return $this->getDasUpdateDate();
                break;
            case 7:
                return $this->getDasStatus();
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
        $keys = DashletPeer::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getDasUid(),
            $keys[1] => $this->getDasClass(),
            $keys[2] => $this->getDasTitle(),
            $keys[3] => $this->getDasDescription(),
            $keys[4] => $this->getDasVersion(),
            $keys[5] => $this->getDasCreateDate(),
            $keys[6] => $this->getDasUpdateDate(),
            $keys[7] => $this->getDasStatus(),
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
        $pos = DashletPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                $this->setDasUid($value);
                break;
            case 1:
                $this->setDasClass($value);
                break;
            case 2:
                $this->setDasTitle($value);
                break;
            case 3:
                $this->setDasDescription($value);
                break;
            case 4:
                $this->setDasVersion($value);
                break;
            case 5:
                $this->setDasCreateDate($value);
                break;
            case 6:
                $this->setDasUpdateDate($value);
                break;
            case 7:
                $this->setDasStatus($value);
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
        $keys = DashletPeer::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) {
            $this->setDasUid($arr[$keys[0]]);
        }

        if (array_key_exists($keys[1], $arr)) {
            $this->setDasClass($arr[$keys[1]]);
        }

        if (array_key_exists($keys[2], $arr)) {
            $this->setDasTitle($arr[$keys[2]]);
        }

        if (array_key_exists($keys[3], $arr)) {
            $this->setDasDescription($arr[$keys[3]]);
        }

        if (array_key_exists($keys[4], $arr)) {
            $this->setDasVersion($arr[$keys[4]]);
        }

        if (array_key_exists($keys[5], $arr)) {
            $this->setDasCreateDate($arr[$keys[5]]);
        }

        if (array_key_exists($keys[6], $arr)) {
            $this->setDasUpdateDate($arr[$keys[6]]);
        }

        if (array_key_exists($keys[7], $arr)) {
            $this->setDasStatus($arr[$keys[7]]);
        }

    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return     Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(DashletPeer::DATABASE_NAME);

        if ($this->isColumnModified(DashletPeer::DAS_UID)) {
            $criteria->add(DashletPeer::DAS_UID, $this->das_uid);
        }

        if ($this->isColumnModified(DashletPeer::DAS_CLASS)) {
            $criteria->add(DashletPeer::DAS_CLASS, $this->das_class);
        }

        if ($this->isColumnModified(DashletPeer::DAS_TITLE)) {
            $criteria->add(DashletPeer::DAS_TITLE, $this->das_title);
        }

        if ($this->isColumnModified(DashletPeer::DAS_DESCRIPTION)) {
            $criteria->add(DashletPeer::DAS_DESCRIPTION, $this->das_description);
        }

        if ($this->isColumnModified(DashletPeer::DAS_VERSION)) {
            $criteria->add(DashletPeer::DAS_VERSION, $this->das_version);
        }

        if ($this->isColumnModified(DashletPeer::DAS_CREATE_DATE)) {
            $criteria->add(DashletPeer::DAS_CREATE_DATE, $this->das_create_date);
        }

        if ($this->isColumnModified(DashletPeer::DAS_UPDATE_DATE)) {
            $criteria->add(DashletPeer::DAS_UPDATE_DATE, $this->das_update_date);
        }

        if ($this->isColumnModified(DashletPeer::DAS_STATUS)) {
            $criteria->add(DashletPeer::DAS_STATUS, $this->das_status);
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
        $criteria = new Criteria(DashletPeer::DATABASE_NAME);

        $criteria->add(DashletPeer::DAS_UID, $this->das_uid);

        return $criteria;
    }

    /**
     * Returns the primary key for this object (row).
     * @return     string
     */
    public function getPrimaryKey()
    {
        return $this->getDasUid();
    }

    /**
     * Generic method to set the primary key (das_uid column).
     *
     * @param      string $key Primary key.
     * @return     void
     */
    public function setPrimaryKey($key)
    {
        $this->setDasUid($key);
    }

    /**
     * Sets contents of passed object to values from current object.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param      object $copyObj An object of Dashlet (or compatible) type.
     * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @throws     PropelException
     */
    public function copyInto($copyObj, $deepCopy = false)
    {

        $copyObj->setDasClass($this->das_class);

        $copyObj->setDasTitle($this->das_title);

        $copyObj->setDasDescription($this->das_description);

        $copyObj->setDasVersion($this->das_version);

        $copyObj->setDasCreateDate($this->das_create_date);

        $copyObj->setDasUpdateDate($this->das_update_date);

        $copyObj->setDasStatus($this->das_status);


        $copyObj->setNew(true);

        $copyObj->setDasUid(''); // this is a pkey column, so set to default value

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
     * @return     Dashlet Clone of current object.
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
     * @return     DashletPeer
     */
    public function getPeer()
    {
        if (self::$peer === null) {
            self::$peer = new DashletPeer();
        }
        return self::$peer;
    }
}

