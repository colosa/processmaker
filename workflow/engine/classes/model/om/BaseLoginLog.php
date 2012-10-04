<?php

require_once 'propel/om/BaseObject.php';

require_once 'propel/om/Persistent.php';


include_once 'propel/util/Criteria.php';

include_once 'classes/model/LoginLogPeer.php';

/**
 * Base class that represents a row from the 'LOGIN_LOG' table.
 *
 * 
 *
 * @package    workflow.classes.model.om
 */
abstract class BaseLoginLog extends BaseObject implements Persistent
{

    /**
     * The Peer class.
     * Instance provides a convenient way of calling static methods on a class
     * that calling code may not be able to identify.
     * @var        LoginLogPeer
    */
    protected static $peer;

    /**
     * The value for the log_uid field.
     * @var        string
     */
    protected $log_uid = '';

    /**
     * The value for the log_status field.
     * @var        string
     */
    protected $log_status = '';

    /**
     * The value for the log_ip field.
     * @var        string
     */
    protected $log_ip = '';

    /**
     * The value for the log_sid field.
     * @var        string
     */
    protected $log_sid = '';

    /**
     * The value for the log_init_date field.
     * @var        int
     */
    protected $log_init_date;

    /**
     * The value for the log_end_date field.
     * @var        int
     */
    protected $log_end_date;

    /**
     * The value for the log_client_hostname field.
     * @var        string
     */
    protected $log_client_hostname = '';

    /**
     * The value for the usr_uid field.
     * @var        string
     */
    protected $usr_uid = '';

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
     * Get the [log_uid] column value.
     * 
     * @return     string
     */
    public function getLogUid()
    {

        return $this->log_uid;
    }

    /**
     * Get the [log_status] column value.
     * 
     * @return     string
     */
    public function getLogStatus()
    {

        return $this->log_status;
    }

    /**
     * Get the [log_ip] column value.
     * 
     * @return     string
     */
    public function getLogIp()
    {

        return $this->log_ip;
    }

    /**
     * Get the [log_sid] column value.
     * 
     * @return     string
     */
    public function getLogSid()
    {

        return $this->log_sid;
    }

    /**
     * Get the [optionally formatted] [log_init_date] column value.
     * 
     * @param      string $format The date/time format string (either date()-style or strftime()-style).
     *                          If format is NULL, then the integer unix timestamp will be returned.
     * @return     mixed Formatted date/time value as string or integer unix timestamp (if format is NULL).
     * @throws     PropelException - if unable to convert the date/time to timestamp.
     */
    public function getLogInitDate($format = 'Y-m-d H:i:s')
    {

        if ($this->log_init_date === null || $this->log_init_date === '') {
            return null;
        } elseif (!is_int($this->log_init_date)) {
            // a non-timestamp value was set externally, so we convert it
            $ts = strtotime($this->log_init_date);
            if ($ts === -1 || $ts === false) {
                throw new PropelException("Unable to parse value of [log_init_date] as date/time value: " .
                    var_export($this->log_init_date, true));
            }
        } else {
            $ts = $this->log_init_date;
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
     * Get the [optionally formatted] [log_end_date] column value.
     * 
     * @param      string $format The date/time format string (either date()-style or strftime()-style).
     *                          If format is NULL, then the integer unix timestamp will be returned.
     * @return     mixed Formatted date/time value as string or integer unix timestamp (if format is NULL).
     * @throws     PropelException - if unable to convert the date/time to timestamp.
     */
    public function getLogEndDate($format = 'Y-m-d H:i:s')
    {

        if ($this->log_end_date === null || $this->log_end_date === '') {
            return null;
        } elseif (!is_int($this->log_end_date)) {
            // a non-timestamp value was set externally, so we convert it
            $ts = strtotime($this->log_end_date);
            if ($ts === -1 || $ts === false) {
                throw new PropelException("Unable to parse value of [log_end_date] as date/time value: " .
                    var_export($this->log_end_date, true));
            }
        } else {
            $ts = $this->log_end_date;
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
     * Get the [log_client_hostname] column value.
     * 
     * @return     string
     */
    public function getLogClientHostname()
    {

        return $this->log_client_hostname;
    }

    /**
     * Get the [usr_uid] column value.
     * 
     * @return     string
     */
    public function getUsrUid()
    {

        return $this->usr_uid;
    }

    /**
     * Set the value of [log_uid] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setLogUid($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->log_uid !== $v || $v === '') {
            $this->log_uid = $v;
            $this->modifiedColumns[] = LoginLogPeer::LOG_UID;
        }

    } // setLogUid()

    /**
     * Set the value of [log_status] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setLogStatus($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->log_status !== $v || $v === '') {
            $this->log_status = $v;
            $this->modifiedColumns[] = LoginLogPeer::LOG_STATUS;
        }

    } // setLogStatus()

    /**
     * Set the value of [log_ip] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setLogIp($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->log_ip !== $v || $v === '') {
            $this->log_ip = $v;
            $this->modifiedColumns[] = LoginLogPeer::LOG_IP;
        }

    } // setLogIp()

    /**
     * Set the value of [log_sid] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setLogSid($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->log_sid !== $v || $v === '') {
            $this->log_sid = $v;
            $this->modifiedColumns[] = LoginLogPeer::LOG_SID;
        }

    } // setLogSid()

    /**
     * Set the value of [log_init_date] column.
     * 
     * @param      int $v new value
     * @return     void
     */
    public function setLogInitDate($v)
    {

        if ($v !== null && !is_int($v)) {
            $ts = strtotime($v);
            if ($ts === -1 || $ts === false) {
                throw new PropelException("Unable to parse date/time value for [log_init_date] from input: " .
                    var_export($v, true));
            }
        } else {
            $ts = $v;
        }
        if ($this->log_init_date !== $ts) {
            $this->log_init_date = $ts;
            $this->modifiedColumns[] = LoginLogPeer::LOG_INIT_DATE;
        }

    } // setLogInitDate()

    /**
     * Set the value of [log_end_date] column.
     * 
     * @param      int $v new value
     * @return     void
     */
    public function setLogEndDate($v)
    {

        if ($v !== null && !is_int($v)) {
            $ts = strtotime($v);
            if ($ts === -1 || $ts === false) {
                throw new PropelException("Unable to parse date/time value for [log_end_date] from input: " .
                    var_export($v, true));
            }
        } else {
            $ts = $v;
        }
        if ($this->log_end_date !== $ts) {
            $this->log_end_date = $ts;
            $this->modifiedColumns[] = LoginLogPeer::LOG_END_DATE;
        }

    } // setLogEndDate()

    /**
     * Set the value of [log_client_hostname] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setLogClientHostname($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->log_client_hostname !== $v || $v === '') {
            $this->log_client_hostname = $v;
            $this->modifiedColumns[] = LoginLogPeer::LOG_CLIENT_HOSTNAME;
        }

    } // setLogClientHostname()

    /**
     * Set the value of [usr_uid] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setUsrUid($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->usr_uid !== $v || $v === '') {
            $this->usr_uid = $v;
            $this->modifiedColumns[] = LoginLogPeer::USR_UID;
        }

    } // setUsrUid()

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

            $this->log_uid = $rs->getString($startcol + 0);

            $this->log_status = $rs->getString($startcol + 1);

            $this->log_ip = $rs->getString($startcol + 2);

            $this->log_sid = $rs->getString($startcol + 3);

            $this->log_init_date = $rs->getTimestamp($startcol + 4, null);

            $this->log_end_date = $rs->getTimestamp($startcol + 5, null);

            $this->log_client_hostname = $rs->getString($startcol + 6);

            $this->usr_uid = $rs->getString($startcol + 7);

            $this->resetModified();

            $this->setNew(false);

            // FIXME - using NUM_COLUMNS may be clearer.
            return $startcol + 8; // 8 = LoginLogPeer::NUM_COLUMNS - LoginLogPeer::NUM_LAZY_LOAD_COLUMNS).

        } catch (Exception $e) {
            throw new PropelException("Error populating LoginLog object", $e);
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
            $con = Propel::getConnection(LoginLogPeer::DATABASE_NAME);
        }

        try {
            $con->begin();
            LoginLogPeer::doDelete($this, $con);
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
            $con = Propel::getConnection(LoginLogPeer::DATABASE_NAME);
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
                    $pk = LoginLogPeer::doInsert($this, $con);
                    $affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
                                         // should always be true here (even though technically
                                         // BasePeer::doInsert() can insert multiple rows).

                    $this->setNew(false);
                } else {
                    $affectedRows += LoginLogPeer::doUpdate($this, $con);
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


            if (($retval = LoginLogPeer::doValidate($this, $columns)) !== true) {
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
        $pos = LoginLogPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                return $this->getLogUid();
                break;
            case 1:
                return $this->getLogStatus();
                break;
            case 2:
                return $this->getLogIp();
                break;
            case 3:
                return $this->getLogSid();
                break;
            case 4:
                return $this->getLogInitDate();
                break;
            case 5:
                return $this->getLogEndDate();
                break;
            case 6:
                return $this->getLogClientHostname();
                break;
            case 7:
                return $this->getUsrUid();
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
        $keys = LoginLogPeer::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getLogUid(),
            $keys[1] => $this->getLogStatus(),
            $keys[2] => $this->getLogIp(),
            $keys[3] => $this->getLogSid(),
            $keys[4] => $this->getLogInitDate(),
            $keys[5] => $this->getLogEndDate(),
            $keys[6] => $this->getLogClientHostname(),
            $keys[7] => $this->getUsrUid(),
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
        $pos = LoginLogPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                $this->setLogUid($value);
                break;
            case 1:
                $this->setLogStatus($value);
                break;
            case 2:
                $this->setLogIp($value);
                break;
            case 3:
                $this->setLogSid($value);
                break;
            case 4:
                $this->setLogInitDate($value);
                break;
            case 5:
                $this->setLogEndDate($value);
                break;
            case 6:
                $this->setLogClientHostname($value);
                break;
            case 7:
                $this->setUsrUid($value);
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
        $keys = LoginLogPeer::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) {
            $this->setLogUid($arr[$keys[0]]);
        }

        if (array_key_exists($keys[1], $arr)) {
            $this->setLogStatus($arr[$keys[1]]);
        }

        if (array_key_exists($keys[2], $arr)) {
            $this->setLogIp($arr[$keys[2]]);
        }

        if (array_key_exists($keys[3], $arr)) {
            $this->setLogSid($arr[$keys[3]]);
        }

        if (array_key_exists($keys[4], $arr)) {
            $this->setLogInitDate($arr[$keys[4]]);
        }

        if (array_key_exists($keys[5], $arr)) {
            $this->setLogEndDate($arr[$keys[5]]);
        }

        if (array_key_exists($keys[6], $arr)) {
            $this->setLogClientHostname($arr[$keys[6]]);
        }

        if (array_key_exists($keys[7], $arr)) {
            $this->setUsrUid($arr[$keys[7]]);
        }

    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return     Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(LoginLogPeer::DATABASE_NAME);

        if ($this->isColumnModified(LoginLogPeer::LOG_UID)) {
            $criteria->add(LoginLogPeer::LOG_UID, $this->log_uid);
        }

        if ($this->isColumnModified(LoginLogPeer::LOG_STATUS)) {
            $criteria->add(LoginLogPeer::LOG_STATUS, $this->log_status);
        }

        if ($this->isColumnModified(LoginLogPeer::LOG_IP)) {
            $criteria->add(LoginLogPeer::LOG_IP, $this->log_ip);
        }

        if ($this->isColumnModified(LoginLogPeer::LOG_SID)) {
            $criteria->add(LoginLogPeer::LOG_SID, $this->log_sid);
        }

        if ($this->isColumnModified(LoginLogPeer::LOG_INIT_DATE)) {
            $criteria->add(LoginLogPeer::LOG_INIT_DATE, $this->log_init_date);
        }

        if ($this->isColumnModified(LoginLogPeer::LOG_END_DATE)) {
            $criteria->add(LoginLogPeer::LOG_END_DATE, $this->log_end_date);
        }

        if ($this->isColumnModified(LoginLogPeer::LOG_CLIENT_HOSTNAME)) {
            $criteria->add(LoginLogPeer::LOG_CLIENT_HOSTNAME, $this->log_client_hostname);
        }

        if ($this->isColumnModified(LoginLogPeer::USR_UID)) {
            $criteria->add(LoginLogPeer::USR_UID, $this->usr_uid);
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
        $criteria = new Criteria(LoginLogPeer::DATABASE_NAME);

        $criteria->add(LoginLogPeer::LOG_UID, $this->log_uid);

        return $criteria;
    }

    /**
     * Returns the primary key for this object (row).
     * @return     string
     */
    public function getPrimaryKey()
    {
        return $this->getLogUid();
    }

    /**
     * Generic method to set the primary key (log_uid column).
     *
     * @param      string $key Primary key.
     * @return     void
     */
    public function setPrimaryKey($key)
    {
        $this->setLogUid($key);
    }

    /**
     * Sets contents of passed object to values from current object.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param      object $copyObj An object of LoginLog (or compatible) type.
     * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @throws     PropelException
     */
    public function copyInto($copyObj, $deepCopy = false)
    {

        $copyObj->setLogStatus($this->log_status);

        $copyObj->setLogIp($this->log_ip);

        $copyObj->setLogSid($this->log_sid);

        $copyObj->setLogInitDate($this->log_init_date);

        $copyObj->setLogEndDate($this->log_end_date);

        $copyObj->setLogClientHostname($this->log_client_hostname);

        $copyObj->setUsrUid($this->usr_uid);


        $copyObj->setNew(true);

        $copyObj->setLogUid(''); // this is a pkey column, so set to default value

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
     * @return     LoginLog Clone of current object.
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
     * @return     LoginLogPeer
     */
    public function getPeer()
    {
        if (self::$peer === null) {
            self::$peer = new LoginLogPeer();
        }
        return self::$peer;
    }
}

