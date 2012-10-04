<?php

require_once 'propel/om/BaseObject.php';

require_once 'propel/om/Persistent.php';


include_once 'propel/util/Criteria.php';

include_once 'classes/model/AppHistoryPeer.php';

/**
 * Base class that represents a row from the 'APP_HISTORY' table.
 *
 * 
 *
 * @package    workflow.classes.model.om
 */
abstract class BaseAppHistory extends BaseObject implements Persistent
{

    /**
     * The Peer class.
     * Instance provides a convenient way of calling static methods on a class
     * that calling code may not be able to identify.
     * @var        AppHistoryPeer
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
     * The value for the dyn_uid field.
     * @var        string
     */
    protected $dyn_uid = '';

    /**
     * The value for the usr_uid field.
     * @var        string
     */
    protected $usr_uid = '';

    /**
     * The value for the app_status field.
     * @var        string
     */
    protected $app_status = '';

    /**
     * The value for the history_date field.
     * @var        int
     */
    protected $history_date;

    /**
     * The value for the history_data field.
     * @var        string
     */
    protected $history_data;

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
     * Get the [dyn_uid] column value.
     * 
     * @return     string
     */
    public function getDynUid()
    {

        return $this->dyn_uid;
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
     * Get the [app_status] column value.
     * 
     * @return     string
     */
    public function getAppStatus()
    {

        return $this->app_status;
    }

    /**
     * Get the [optionally formatted] [history_date] column value.
     * 
     * @param      string $format The date/time format string (either date()-style or strftime()-style).
     *                          If format is NULL, then the integer unix timestamp will be returned.
     * @return     mixed Formatted date/time value as string or integer unix timestamp (if format is NULL).
     * @throws     PropelException - if unable to convert the date/time to timestamp.
     */
    public function getHistoryDate($format = 'Y-m-d H:i:s')
    {

        if ($this->history_date === null || $this->history_date === '') {
            return null;
        } elseif (!is_int($this->history_date)) {
            // a non-timestamp value was set externally, so we convert it
            $ts = strtotime($this->history_date);
            if ($ts === -1 || $ts === false) {
                throw new PropelException("Unable to parse value of [history_date] as date/time value: " .
                    var_export($this->history_date, true));
            }
        } else {
            $ts = $this->history_date;
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
     * Get the [history_data] column value.
     * 
     * @return     string
     */
    public function getHistoryData()
    {

        return $this->history_data;
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
            $this->modifiedColumns[] = AppHistoryPeer::APP_UID;
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
            $this->modifiedColumns[] = AppHistoryPeer::DEL_INDEX;
        }

    } // setDelIndex()

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
            $this->modifiedColumns[] = AppHistoryPeer::PRO_UID;
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
            $this->modifiedColumns[] = AppHistoryPeer::TAS_UID;
        }

    } // setTasUid()

    /**
     * Set the value of [dyn_uid] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setDynUid($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->dyn_uid !== $v || $v === '') {
            $this->dyn_uid = $v;
            $this->modifiedColumns[] = AppHistoryPeer::DYN_UID;
        }

    } // setDynUid()

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
            $this->modifiedColumns[] = AppHistoryPeer::USR_UID;
        }

    } // setUsrUid()

    /**
     * Set the value of [app_status] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setAppStatus($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->app_status !== $v || $v === '') {
            $this->app_status = $v;
            $this->modifiedColumns[] = AppHistoryPeer::APP_STATUS;
        }

    } // setAppStatus()

    /**
     * Set the value of [history_date] column.
     * 
     * @param      int $v new value
     * @return     void
     */
    public function setHistoryDate($v)
    {

        if ($v !== null && !is_int($v)) {
            $ts = strtotime($v);
            if ($ts === -1 || $ts === false) {
                throw new PropelException("Unable to parse date/time value for [history_date] from input: " .
                    var_export($v, true));
            }
        } else {
            $ts = $v;
        }
        if ($this->history_date !== $ts) {
            $this->history_date = $ts;
            $this->modifiedColumns[] = AppHistoryPeer::HISTORY_DATE;
        }

    } // setHistoryDate()

    /**
     * Set the value of [history_data] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setHistoryData($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->history_data !== $v) {
            $this->history_data = $v;
            $this->modifiedColumns[] = AppHistoryPeer::HISTORY_DATA;
        }

    } // setHistoryData()

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

            $this->pro_uid = $rs->getString($startcol + 2);

            $this->tas_uid = $rs->getString($startcol + 3);

            $this->dyn_uid = $rs->getString($startcol + 4);

            $this->usr_uid = $rs->getString($startcol + 5);

            $this->app_status = $rs->getString($startcol + 6);

            $this->history_date = $rs->getTimestamp($startcol + 7, null);

            $this->history_data = $rs->getString($startcol + 8);

            $this->resetModified();

            $this->setNew(false);

            // FIXME - using NUM_COLUMNS may be clearer.
            return $startcol + 9; // 9 = AppHistoryPeer::NUM_COLUMNS - AppHistoryPeer::NUM_LAZY_LOAD_COLUMNS).

        } catch (Exception $e) {
            throw new PropelException("Error populating AppHistory object", $e);
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
            $con = Propel::getConnection(AppHistoryPeer::DATABASE_NAME);
        }

        try {
            $con->begin();
            AppHistoryPeer::doDelete($this, $con);
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
            $con = Propel::getConnection(AppHistoryPeer::DATABASE_NAME);
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
                    $pk = AppHistoryPeer::doInsert($this, $con);
                    $affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
                                         // should always be true here (even though technically
                                         // BasePeer::doInsert() can insert multiple rows).

                    $this->setNew(false);
                } else {
                    $affectedRows += AppHistoryPeer::doUpdate($this, $con);
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


            if (($retval = AppHistoryPeer::doValidate($this, $columns)) !== true) {
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
        $pos = AppHistoryPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                return $this->getProUid();
                break;
            case 3:
                return $this->getTasUid();
                break;
            case 4:
                return $this->getDynUid();
                break;
            case 5:
                return $this->getUsrUid();
                break;
            case 6:
                return $this->getAppStatus();
                break;
            case 7:
                return $this->getHistoryDate();
                break;
            case 8:
                return $this->getHistoryData();
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
        $keys = AppHistoryPeer::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getAppUid(),
            $keys[1] => $this->getDelIndex(),
            $keys[2] => $this->getProUid(),
            $keys[3] => $this->getTasUid(),
            $keys[4] => $this->getDynUid(),
            $keys[5] => $this->getUsrUid(),
            $keys[6] => $this->getAppStatus(),
            $keys[7] => $this->getHistoryDate(),
            $keys[8] => $this->getHistoryData(),
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
        $pos = AppHistoryPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                $this->setProUid($value);
                break;
            case 3:
                $this->setTasUid($value);
                break;
            case 4:
                $this->setDynUid($value);
                break;
            case 5:
                $this->setUsrUid($value);
                break;
            case 6:
                $this->setAppStatus($value);
                break;
            case 7:
                $this->setHistoryDate($value);
                break;
            case 8:
                $this->setHistoryData($value);
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
        $keys = AppHistoryPeer::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) {
            $this->setAppUid($arr[$keys[0]]);
        }

        if (array_key_exists($keys[1], $arr)) {
            $this->setDelIndex($arr[$keys[1]]);
        }

        if (array_key_exists($keys[2], $arr)) {
            $this->setProUid($arr[$keys[2]]);
        }

        if (array_key_exists($keys[3], $arr)) {
            $this->setTasUid($arr[$keys[3]]);
        }

        if (array_key_exists($keys[4], $arr)) {
            $this->setDynUid($arr[$keys[4]]);
        }

        if (array_key_exists($keys[5], $arr)) {
            $this->setUsrUid($arr[$keys[5]]);
        }

        if (array_key_exists($keys[6], $arr)) {
            $this->setAppStatus($arr[$keys[6]]);
        }

        if (array_key_exists($keys[7], $arr)) {
            $this->setHistoryDate($arr[$keys[7]]);
        }

        if (array_key_exists($keys[8], $arr)) {
            $this->setHistoryData($arr[$keys[8]]);
        }

    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return     Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(AppHistoryPeer::DATABASE_NAME);

        if ($this->isColumnModified(AppHistoryPeer::APP_UID)) {
            $criteria->add(AppHistoryPeer::APP_UID, $this->app_uid);
        }

        if ($this->isColumnModified(AppHistoryPeer::DEL_INDEX)) {
            $criteria->add(AppHistoryPeer::DEL_INDEX, $this->del_index);
        }

        if ($this->isColumnModified(AppHistoryPeer::PRO_UID)) {
            $criteria->add(AppHistoryPeer::PRO_UID, $this->pro_uid);
        }

        if ($this->isColumnModified(AppHistoryPeer::TAS_UID)) {
            $criteria->add(AppHistoryPeer::TAS_UID, $this->tas_uid);
        }

        if ($this->isColumnModified(AppHistoryPeer::DYN_UID)) {
            $criteria->add(AppHistoryPeer::DYN_UID, $this->dyn_uid);
        }

        if ($this->isColumnModified(AppHistoryPeer::USR_UID)) {
            $criteria->add(AppHistoryPeer::USR_UID, $this->usr_uid);
        }

        if ($this->isColumnModified(AppHistoryPeer::APP_STATUS)) {
            $criteria->add(AppHistoryPeer::APP_STATUS, $this->app_status);
        }

        if ($this->isColumnModified(AppHistoryPeer::HISTORY_DATE)) {
            $criteria->add(AppHistoryPeer::HISTORY_DATE, $this->history_date);
        }

        if ($this->isColumnModified(AppHistoryPeer::HISTORY_DATA)) {
            $criteria->add(AppHistoryPeer::HISTORY_DATA, $this->history_data);
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
        $criteria = new Criteria(AppHistoryPeer::DATABASE_NAME);


        return $criteria;
    }

    /**
     * Returns NULL since this table doesn't have a primary key.
     * This method exists only for BC and is deprecated!
     * @return     null
     */
    public function getPrimaryKey()
    {
        return null;
    }

    /**
     * Dummy primary key setter.
     *
     * This function only exists to preserve backwards compatibility.  It is no longer
     * needed or required by the Persistent interface.  It will be removed in next BC-breaking
     * release of Propel.
     *
     * @deprecated
     */
     public function setPrimaryKey($pk)
     {
         // do nothing, because this object doesn't have any primary keys
     }

    /**
     * Sets contents of passed object to values from current object.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param      object $copyObj An object of AppHistory (or compatible) type.
     * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @throws     PropelException
     */
    public function copyInto($copyObj, $deepCopy = false)
    {

        $copyObj->setAppUid($this->app_uid);

        $copyObj->setDelIndex($this->del_index);

        $copyObj->setProUid($this->pro_uid);

        $copyObj->setTasUid($this->tas_uid);

        $copyObj->setDynUid($this->dyn_uid);

        $copyObj->setUsrUid($this->usr_uid);

        $copyObj->setAppStatus($this->app_status);

        $copyObj->setHistoryDate($this->history_date);

        $copyObj->setHistoryData($this->history_data);


        $copyObj->setNew(true);

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
     * @return     AppHistory Clone of current object.
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
     * @return     AppHistoryPeer
     */
    public function getPeer()
    {
        if (self::$peer === null) {
            self::$peer = new AppHistoryPeer();
        }
        return self::$peer;
    }
}

