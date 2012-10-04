<?php

require_once 'propel/om/BaseObject.php';

require_once 'propel/om/Persistent.php';


include_once 'propel/util/Criteria.php';

include_once 'classes/model/AppEventPeer.php';

/**
 * Base class that represents a row from the 'APP_EVENT' table.
 *
 * 
 *
 * @package    workflow.classes.model.om
 */
abstract class BaseAppEvent extends BaseObject implements Persistent
{

    /**
     * The Peer class.
     * Instance provides a convenient way of calling static methods on a class
     * that calling code may not be able to identify.
     * @var        AppEventPeer
    */
    protected static $peer;

    /**
     * The value for the app_uid field.
     * @var        string
     */
    protected $app_uid = '';

    /**
     * The value for the del_index field.
     * @var        int
     */
    protected $del_index = 0;

    /**
     * The value for the evn_uid field.
     * @var        string
     */
    protected $evn_uid = '';

    /**
     * The value for the app_evn_action_date field.
     * @var        int
     */
    protected $app_evn_action_date;

    /**
     * The value for the app_evn_attempts field.
     * @var        int
     */
    protected $app_evn_attempts = 0;

    /**
     * The value for the app_evn_last_execution_date field.
     * @var        int
     */
    protected $app_evn_last_execution_date;

    /**
     * The value for the app_evn_status field.
     * @var        string
     */
    protected $app_evn_status = 'OPEN';

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
     * Get the [app_uid] column value.
     * 
     * @return     string
     */
    public function getAppUid()
    {

        return $this->app_uid;
    }

    /**
     * Get the [del_index] column value.
     * 
     * @return     int
     */
    public function getDelIndex()
    {

        return $this->del_index;
    }

    /**
     * Get the [evn_uid] column value.
     * 
     * @return     string
     */
    public function getEvnUid()
    {

        return $this->evn_uid;
    }

    /**
     * Get the [optionally formatted] [app_evn_action_date] column value.
     * 
     * @param      string $format The date/time format string (either date()-style or strftime()-style).
     *                          If format is NULL, then the integer unix timestamp will be returned.
     * @return     mixed Formatted date/time value as string or integer unix timestamp (if format is NULL).
     * @throws     PropelException - if unable to convert the date/time to timestamp.
     */
    public function getAppEvnActionDate($format = 'Y-m-d H:i:s')
    {

        if ($this->app_evn_action_date === null || $this->app_evn_action_date === '') {
            return null;
        } elseif (!is_int($this->app_evn_action_date)) {
            // a non-timestamp value was set externally, so we convert it
            $ts = strtotime($this->app_evn_action_date);
            if ($ts === -1 || $ts === false) {
                throw new PropelException("Unable to parse value of [app_evn_action_date] as date/time value: " .
                    var_export($this->app_evn_action_date, true));
            }
        } else {
            $ts = $this->app_evn_action_date;
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
     * Get the [app_evn_attempts] column value.
     * 
     * @return     int
     */
    public function getAppEvnAttempts()
    {

        return $this->app_evn_attempts;
    }

    /**
     * Get the [optionally formatted] [app_evn_last_execution_date] column value.
     * 
     * @param      string $format The date/time format string (either date()-style or strftime()-style).
     *                          If format is NULL, then the integer unix timestamp will be returned.
     * @return     mixed Formatted date/time value as string or integer unix timestamp (if format is NULL).
     * @throws     PropelException - if unable to convert the date/time to timestamp.
     */
    public function getAppEvnLastExecutionDate($format = 'Y-m-d H:i:s')
    {

        if ($this->app_evn_last_execution_date === null || $this->app_evn_last_execution_date === '') {
            return null;
        } elseif (!is_int($this->app_evn_last_execution_date)) {
            // a non-timestamp value was set externally, so we convert it
            $ts = strtotime($this->app_evn_last_execution_date);
            if ($ts === -1 || $ts === false) {
                throw new PropelException("Unable to parse value of [app_evn_last_execution_date] as date/time value: " .
                    var_export($this->app_evn_last_execution_date, true));
            }
        } else {
            $ts = $this->app_evn_last_execution_date;
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
     * Get the [app_evn_status] column value.
     * 
     * @return     string
     */
    public function getAppEvnStatus()
    {

        return $this->app_evn_status;
    }

    /**
     * Set the value of [app_uid] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setAppUid($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->app_uid !== $v || $v === '') {
            $this->app_uid = $v;
            $this->modifiedColumns[] = AppEventPeer::APP_UID;
        }

    } // setAppUid()

    /**
     * Set the value of [del_index] column.
     * 
     * @param      int $v new value
     * @return     void
     */
    public function setDelIndex($v)
    {

        // Since the native PHP type for this column is integer,
        // we will cast the input value to an int (if it is not).
        if ($v !== null && !is_int($v) && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->del_index !== $v || $v === 0) {
            $this->del_index = $v;
            $this->modifiedColumns[] = AppEventPeer::DEL_INDEX;
        }

    } // setDelIndex()

    /**
     * Set the value of [evn_uid] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setEvnUid($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->evn_uid !== $v || $v === '') {
            $this->evn_uid = $v;
            $this->modifiedColumns[] = AppEventPeer::EVN_UID;
        }

    } // setEvnUid()

    /**
     * Set the value of [app_evn_action_date] column.
     * 
     * @param      int $v new value
     * @return     void
     */
    public function setAppEvnActionDate($v)
    {

        if ($v !== null && !is_int($v)) {
            $ts = strtotime($v);
            if ($ts === -1 || $ts === false) {
                throw new PropelException("Unable to parse date/time value for [app_evn_action_date] from input: " .
                    var_export($v, true));
            }
        } else {
            $ts = $v;
        }
        if ($this->app_evn_action_date !== $ts) {
            $this->app_evn_action_date = $ts;
            $this->modifiedColumns[] = AppEventPeer::APP_EVN_ACTION_DATE;
        }

    } // setAppEvnActionDate()

    /**
     * Set the value of [app_evn_attempts] column.
     * 
     * @param      int $v new value
     * @return     void
     */
    public function setAppEvnAttempts($v)
    {

        // Since the native PHP type for this column is integer,
        // we will cast the input value to an int (if it is not).
        if ($v !== null && !is_int($v) && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->app_evn_attempts !== $v || $v === 0) {
            $this->app_evn_attempts = $v;
            $this->modifiedColumns[] = AppEventPeer::APP_EVN_ATTEMPTS;
        }

    } // setAppEvnAttempts()

    /**
     * Set the value of [app_evn_last_execution_date] column.
     * 
     * @param      int $v new value
     * @return     void
     */
    public function setAppEvnLastExecutionDate($v)
    {

        if ($v !== null && !is_int($v)) {
            $ts = strtotime($v);
            if ($ts === -1 || $ts === false) {
                throw new PropelException("Unable to parse date/time value for [app_evn_last_execution_date] from input: " .
                    var_export($v, true));
            }
        } else {
            $ts = $v;
        }
        if ($this->app_evn_last_execution_date !== $ts) {
            $this->app_evn_last_execution_date = $ts;
            $this->modifiedColumns[] = AppEventPeer::APP_EVN_LAST_EXECUTION_DATE;
        }

    } // setAppEvnLastExecutionDate()

    /**
     * Set the value of [app_evn_status] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setAppEvnStatus($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->app_evn_status !== $v || $v === 'OPEN') {
            $this->app_evn_status = $v;
            $this->modifiedColumns[] = AppEventPeer::APP_EVN_STATUS;
        }

    } // setAppEvnStatus()

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

            $this->app_uid = $rs->getString($startcol + 0);

            $this->del_index = $rs->getInt($startcol + 1);

            $this->evn_uid = $rs->getString($startcol + 2);

            $this->app_evn_action_date = $rs->getTimestamp($startcol + 3, null);

            $this->app_evn_attempts = $rs->getInt($startcol + 4);

            $this->app_evn_last_execution_date = $rs->getTimestamp($startcol + 5, null);

            $this->app_evn_status = $rs->getString($startcol + 6);

            $this->resetModified();

            $this->setNew(false);

            // FIXME - using NUM_COLUMNS may be clearer.
            return $startcol + 7; // 7 = AppEventPeer::NUM_COLUMNS - AppEventPeer::NUM_LAZY_LOAD_COLUMNS).

        } catch (Exception $e) {
            throw new PropelException("Error populating AppEvent object", $e);
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
            $con = Propel::getConnection(AppEventPeer::DATABASE_NAME);
        }

        try {
            $con->begin();
            AppEventPeer::doDelete($this, $con);
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
            $con = Propel::getConnection(AppEventPeer::DATABASE_NAME);
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
                    $pk = AppEventPeer::doInsert($this, $con);
                    $affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
                                         // should always be true here (even though technically
                                         // BasePeer::doInsert() can insert multiple rows).

                    $this->setNew(false);
                } else {
                    $affectedRows += AppEventPeer::doUpdate($this, $con);
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


            if (($retval = AppEventPeer::doValidate($this, $columns)) !== true) {
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
        $pos = AppEventPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                return $this->getAppUid();
                break;
            case 1:
                return $this->getDelIndex();
                break;
            case 2:
                return $this->getEvnUid();
                break;
            case 3:
                return $this->getAppEvnActionDate();
                break;
            case 4:
                return $this->getAppEvnAttempts();
                break;
            case 5:
                return $this->getAppEvnLastExecutionDate();
                break;
            case 6:
                return $this->getAppEvnStatus();
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
        $keys = AppEventPeer::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getAppUid(),
            $keys[1] => $this->getDelIndex(),
            $keys[2] => $this->getEvnUid(),
            $keys[3] => $this->getAppEvnActionDate(),
            $keys[4] => $this->getAppEvnAttempts(),
            $keys[5] => $this->getAppEvnLastExecutionDate(),
            $keys[6] => $this->getAppEvnStatus(),
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
        $pos = AppEventPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                $this->setAppUid($value);
                break;
            case 1:
                $this->setDelIndex($value);
                break;
            case 2:
                $this->setEvnUid($value);
                break;
            case 3:
                $this->setAppEvnActionDate($value);
                break;
            case 4:
                $this->setAppEvnAttempts($value);
                break;
            case 5:
                $this->setAppEvnLastExecutionDate($value);
                break;
            case 6:
                $this->setAppEvnStatus($value);
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
        $keys = AppEventPeer::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) {
            $this->setAppUid($arr[$keys[0]]);
        }

        if (array_key_exists($keys[1], $arr)) {
            $this->setDelIndex($arr[$keys[1]]);
        }

        if (array_key_exists($keys[2], $arr)) {
            $this->setEvnUid($arr[$keys[2]]);
        }

        if (array_key_exists($keys[3], $arr)) {
            $this->setAppEvnActionDate($arr[$keys[3]]);
        }

        if (array_key_exists($keys[4], $arr)) {
            $this->setAppEvnAttempts($arr[$keys[4]]);
        }

        if (array_key_exists($keys[5], $arr)) {
            $this->setAppEvnLastExecutionDate($arr[$keys[5]]);
        }

        if (array_key_exists($keys[6], $arr)) {
            $this->setAppEvnStatus($arr[$keys[6]]);
        }

    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return     Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(AppEventPeer::DATABASE_NAME);

        if ($this->isColumnModified(AppEventPeer::APP_UID)) {
            $criteria->add(AppEventPeer::APP_UID, $this->app_uid);
        }

        if ($this->isColumnModified(AppEventPeer::DEL_INDEX)) {
            $criteria->add(AppEventPeer::DEL_INDEX, $this->del_index);
        }

        if ($this->isColumnModified(AppEventPeer::EVN_UID)) {
            $criteria->add(AppEventPeer::EVN_UID, $this->evn_uid);
        }

        if ($this->isColumnModified(AppEventPeer::APP_EVN_ACTION_DATE)) {
            $criteria->add(AppEventPeer::APP_EVN_ACTION_DATE, $this->app_evn_action_date);
        }

        if ($this->isColumnModified(AppEventPeer::APP_EVN_ATTEMPTS)) {
            $criteria->add(AppEventPeer::APP_EVN_ATTEMPTS, $this->app_evn_attempts);
        }

        if ($this->isColumnModified(AppEventPeer::APP_EVN_LAST_EXECUTION_DATE)) {
            $criteria->add(AppEventPeer::APP_EVN_LAST_EXECUTION_DATE, $this->app_evn_last_execution_date);
        }

        if ($this->isColumnModified(AppEventPeer::APP_EVN_STATUS)) {
            $criteria->add(AppEventPeer::APP_EVN_STATUS, $this->app_evn_status);
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
        $criteria = new Criteria(AppEventPeer::DATABASE_NAME);

        $criteria->add(AppEventPeer::APP_UID, $this->app_uid);
        $criteria->add(AppEventPeer::DEL_INDEX, $this->del_index);
        $criteria->add(AppEventPeer::EVN_UID, $this->evn_uid);

        return $criteria;
    }

    /**
     * Returns the composite primary key for this object.
     * The array elements will be in same order as specified in XML.
     * @return     array
     */
    public function getPrimaryKey()
    {
        $pks = array();

        $pks[0] = $this->getAppUid();

        $pks[1] = $this->getDelIndex();

        $pks[2] = $this->getEvnUid();

        return $pks;
    }

    /**
     * Set the [composite] primary key.
     *
     * @param      array $keys The elements of the composite key (order must match the order in XML file).
     * @return     void
     */
    public function setPrimaryKey($keys)
    {

        $this->setAppUid($keys[0]);

        $this->setDelIndex($keys[1]);

        $this->setEvnUid($keys[2]);

    }

    /**
     * Sets contents of passed object to values from current object.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param      object $copyObj An object of AppEvent (or compatible) type.
     * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @throws     PropelException
     */
    public function copyInto($copyObj, $deepCopy = false)
    {

        $copyObj->setAppEvnActionDate($this->app_evn_action_date);

        $copyObj->setAppEvnAttempts($this->app_evn_attempts);

        $copyObj->setAppEvnLastExecutionDate($this->app_evn_last_execution_date);

        $copyObj->setAppEvnStatus($this->app_evn_status);


        $copyObj->setNew(true);

        $copyObj->setAppUid(''); // this is a pkey column, so set to default value

        $copyObj->setDelIndex('0'); // this is a pkey column, so set to default value

        $copyObj->setEvnUid(''); // this is a pkey column, so set to default value

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
     * @return     AppEvent Clone of current object.
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
     * @return     AppEventPeer
     */
    public function getPeer()
    {
        if (self::$peer === null) {
            self::$peer = new AppEventPeer();
        }
        return self::$peer;
    }
}

