<?php

require_once 'propel/om/BaseObject.php';

require_once 'propel/om/Persistent.php';


include_once 'propel/util/Criteria.php';

include_once 'classes/model/LogCasesSchedulerPeer.php';

/**
 * Base class that represents a row from the 'LOG_CASES_SCHEDULER' table.
 *
 * 
 *
 * @package    workflow.classes.model.om
 */
abstract class BaseLogCasesScheduler extends BaseObject implements Persistent
{

    /**
     * The Peer class.
     * Instance provides a convenient way of calling static methods on a class
     * that calling code may not be able to identify.
     * @var        LogCasesSchedulerPeer
    */
    protected static $peer;

    /**
     * The value for the log_case_uid field.
     * @var        string
     */
    protected $log_case_uid = '';

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
     * The value for the usr_name field.
     * @var        string
     */
    protected $usr_name = '';

    /**
     * The value for the exec_date field.
     * @var        int
     */
    protected $exec_date;

    /**
     * The value for the exec_hour field.
     * @var        string
     */
    protected $exec_hour = '12:00';

    /**
     * The value for the result field.
     * @var        string
     */
    protected $result = 'SUCCESS';

    /**
     * The value for the sch_uid field.
     * @var        string
     */
    protected $sch_uid = 'OPEN';

    /**
     * The value for the ws_create_case_status field.
     * @var        string
     */
    protected $ws_create_case_status;

    /**
     * The value for the ws_route_case_status field.
     * @var        string
     */
    protected $ws_route_case_status;

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
     * Get the [log_case_uid] column value.
     * 
     * @return     string
     */
    public function getLogCaseUid()
    {

        return $this->log_case_uid;
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
     * Get the [usr_name] column value.
     * 
     * @return     string
     */
    public function getUsrName()
    {

        return $this->usr_name;
    }

    /**
     * Get the [optionally formatted] [exec_date] column value.
     * 
     * @param      string $format The date/time format string (either date()-style or strftime()-style).
     *                          If format is NULL, then the integer unix timestamp will be returned.
     * @return     mixed Formatted date/time value as string or integer unix timestamp (if format is NULL).
     * @throws     PropelException - if unable to convert the date/time to timestamp.
     */
    public function getExecDate($format = 'Y-m-d')
    {

        if ($this->exec_date === null || $this->exec_date === '') {
            return null;
        } elseif (!is_int($this->exec_date)) {
            // a non-timestamp value was set externally, so we convert it
            $ts = strtotime($this->exec_date);
            if ($ts === -1 || $ts === false) {
                throw new PropelException("Unable to parse value of [exec_date] as date/time value: " .
                    var_export($this->exec_date, true));
            }
        } else {
            $ts = $this->exec_date;
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
     * Get the [exec_hour] column value.
     * 
     * @return     string
     */
    public function getExecHour()
    {

        return $this->exec_hour;
    }

    /**
     * Get the [result] column value.
     * 
     * @return     string
     */
    public function getResult()
    {

        return $this->result;
    }

    /**
     * Get the [sch_uid] column value.
     * 
     * @return     string
     */
    public function getSchUid()
    {

        return $this->sch_uid;
    }

    /**
     * Get the [ws_create_case_status] column value.
     * 
     * @return     string
     */
    public function getWsCreateCaseStatus()
    {

        return $this->ws_create_case_status;
    }

    /**
     * Get the [ws_route_case_status] column value.
     * 
     * @return     string
     */
    public function getWsRouteCaseStatus()
    {

        return $this->ws_route_case_status;
    }

    /**
     * Set the value of [log_case_uid] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setLogCaseUid($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->log_case_uid !== $v || $v === '') {
            $this->log_case_uid = $v;
            $this->modifiedColumns[] = LogCasesSchedulerPeer::LOG_CASE_UID;
        }

    } // setLogCaseUid()

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
            $this->modifiedColumns[] = LogCasesSchedulerPeer::PRO_UID;
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
            $this->modifiedColumns[] = LogCasesSchedulerPeer::TAS_UID;
        }

    } // setTasUid()

    /**
     * Set the value of [usr_name] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setUsrName($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->usr_name !== $v || $v === '') {
            $this->usr_name = $v;
            $this->modifiedColumns[] = LogCasesSchedulerPeer::USR_NAME;
        }

    } // setUsrName()

    /**
     * Set the value of [exec_date] column.
     * 
     * @param      int $v new value
     * @return     void
     */
    public function setExecDate($v)
    {

        if ($v !== null && !is_int($v)) {
            $ts = strtotime($v);
            if ($ts === -1 || $ts === false) {
                throw new PropelException("Unable to parse date/time value for [exec_date] from input: " .
                    var_export($v, true));
            }
        } else {
            $ts = $v;
        }
        if ($this->exec_date !== $ts) {
            $this->exec_date = $ts;
            $this->modifiedColumns[] = LogCasesSchedulerPeer::EXEC_DATE;
        }

    } // setExecDate()

    /**
     * Set the value of [exec_hour] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setExecHour($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->exec_hour !== $v || $v === '12:00') {
            $this->exec_hour = $v;
            $this->modifiedColumns[] = LogCasesSchedulerPeer::EXEC_HOUR;
        }

    } // setExecHour()

    /**
     * Set the value of [result] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setResult($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->result !== $v || $v === 'SUCCESS') {
            $this->result = $v;
            $this->modifiedColumns[] = LogCasesSchedulerPeer::RESULT;
        }

    } // setResult()

    /**
     * Set the value of [sch_uid] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setSchUid($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->sch_uid !== $v || $v === 'OPEN') {
            $this->sch_uid = $v;
            $this->modifiedColumns[] = LogCasesSchedulerPeer::SCH_UID;
        }

    } // setSchUid()

    /**
     * Set the value of [ws_create_case_status] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setWsCreateCaseStatus($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->ws_create_case_status !== $v) {
            $this->ws_create_case_status = $v;
            $this->modifiedColumns[] = LogCasesSchedulerPeer::WS_CREATE_CASE_STATUS;
        }

    } // setWsCreateCaseStatus()

    /**
     * Set the value of [ws_route_case_status] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setWsRouteCaseStatus($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->ws_route_case_status !== $v) {
            $this->ws_route_case_status = $v;
            $this->modifiedColumns[] = LogCasesSchedulerPeer::WS_ROUTE_CASE_STATUS;
        }

    } // setWsRouteCaseStatus()

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

            $this->log_case_uid = $rs->getString($startcol + 0);

            $this->pro_uid = $rs->getString($startcol + 1);

            $this->tas_uid = $rs->getString($startcol + 2);

            $this->usr_name = $rs->getString($startcol + 3);

            $this->exec_date = $rs->getDate($startcol + 4, null);

            $this->exec_hour = $rs->getString($startcol + 5);

            $this->result = $rs->getString($startcol + 6);

            $this->sch_uid = $rs->getString($startcol + 7);

            $this->ws_create_case_status = $rs->getString($startcol + 8);

            $this->ws_route_case_status = $rs->getString($startcol + 9);

            $this->resetModified();

            $this->setNew(false);

            // FIXME - using NUM_COLUMNS may be clearer.
            return $startcol + 10; // 10 = LogCasesSchedulerPeer::NUM_COLUMNS - LogCasesSchedulerPeer::NUM_LAZY_LOAD_COLUMNS).

        } catch (Exception $e) {
            throw new PropelException("Error populating LogCasesScheduler object", $e);
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
            $con = Propel::getConnection(LogCasesSchedulerPeer::DATABASE_NAME);
        }

        try {
            $con->begin();
            LogCasesSchedulerPeer::doDelete($this, $con);
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
            $con = Propel::getConnection(LogCasesSchedulerPeer::DATABASE_NAME);
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
                    $pk = LogCasesSchedulerPeer::doInsert($this, $con);
                    $affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
                                         // should always be true here (even though technically
                                         // BasePeer::doInsert() can insert multiple rows).

                    $this->setNew(false);
                } else {
                    $affectedRows += LogCasesSchedulerPeer::doUpdate($this, $con);
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


            if (($retval = LogCasesSchedulerPeer::doValidate($this, $columns)) !== true) {
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
        $pos = LogCasesSchedulerPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                return $this->getLogCaseUid();
                break;
            case 1:
                return $this->getProUid();
                break;
            case 2:
                return $this->getTasUid();
                break;
            case 3:
                return $this->getUsrName();
                break;
            case 4:
                return $this->getExecDate();
                break;
            case 5:
                return $this->getExecHour();
                break;
            case 6:
                return $this->getResult();
                break;
            case 7:
                return $this->getSchUid();
                break;
            case 8:
                return $this->getWsCreateCaseStatus();
                break;
            case 9:
                return $this->getWsRouteCaseStatus();
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
        $keys = LogCasesSchedulerPeer::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getLogCaseUid(),
            $keys[1] => $this->getProUid(),
            $keys[2] => $this->getTasUid(),
            $keys[3] => $this->getUsrName(),
            $keys[4] => $this->getExecDate(),
            $keys[5] => $this->getExecHour(),
            $keys[6] => $this->getResult(),
            $keys[7] => $this->getSchUid(),
            $keys[8] => $this->getWsCreateCaseStatus(),
            $keys[9] => $this->getWsRouteCaseStatus(),
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
        $pos = LogCasesSchedulerPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                $this->setLogCaseUid($value);
                break;
            case 1:
                $this->setProUid($value);
                break;
            case 2:
                $this->setTasUid($value);
                break;
            case 3:
                $this->setUsrName($value);
                break;
            case 4:
                $this->setExecDate($value);
                break;
            case 5:
                $this->setExecHour($value);
                break;
            case 6:
                $this->setResult($value);
                break;
            case 7:
                $this->setSchUid($value);
                break;
            case 8:
                $this->setWsCreateCaseStatus($value);
                break;
            case 9:
                $this->setWsRouteCaseStatus($value);
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
        $keys = LogCasesSchedulerPeer::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) {
            $this->setLogCaseUid($arr[$keys[0]]);
        }

        if (array_key_exists($keys[1], $arr)) {
            $this->setProUid($arr[$keys[1]]);
        }

        if (array_key_exists($keys[2], $arr)) {
            $this->setTasUid($arr[$keys[2]]);
        }

        if (array_key_exists($keys[3], $arr)) {
            $this->setUsrName($arr[$keys[3]]);
        }

        if (array_key_exists($keys[4], $arr)) {
            $this->setExecDate($arr[$keys[4]]);
        }

        if (array_key_exists($keys[5], $arr)) {
            $this->setExecHour($arr[$keys[5]]);
        }

        if (array_key_exists($keys[6], $arr)) {
            $this->setResult($arr[$keys[6]]);
        }

        if (array_key_exists($keys[7], $arr)) {
            $this->setSchUid($arr[$keys[7]]);
        }

        if (array_key_exists($keys[8], $arr)) {
            $this->setWsCreateCaseStatus($arr[$keys[8]]);
        }

        if (array_key_exists($keys[9], $arr)) {
            $this->setWsRouteCaseStatus($arr[$keys[9]]);
        }

    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return     Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(LogCasesSchedulerPeer::DATABASE_NAME);

        if ($this->isColumnModified(LogCasesSchedulerPeer::LOG_CASE_UID)) {
            $criteria->add(LogCasesSchedulerPeer::LOG_CASE_UID, $this->log_case_uid);
        }

        if ($this->isColumnModified(LogCasesSchedulerPeer::PRO_UID)) {
            $criteria->add(LogCasesSchedulerPeer::PRO_UID, $this->pro_uid);
        }

        if ($this->isColumnModified(LogCasesSchedulerPeer::TAS_UID)) {
            $criteria->add(LogCasesSchedulerPeer::TAS_UID, $this->tas_uid);
        }

        if ($this->isColumnModified(LogCasesSchedulerPeer::USR_NAME)) {
            $criteria->add(LogCasesSchedulerPeer::USR_NAME, $this->usr_name);
        }

        if ($this->isColumnModified(LogCasesSchedulerPeer::EXEC_DATE)) {
            $criteria->add(LogCasesSchedulerPeer::EXEC_DATE, $this->exec_date);
        }

        if ($this->isColumnModified(LogCasesSchedulerPeer::EXEC_HOUR)) {
            $criteria->add(LogCasesSchedulerPeer::EXEC_HOUR, $this->exec_hour);
        }

        if ($this->isColumnModified(LogCasesSchedulerPeer::RESULT)) {
            $criteria->add(LogCasesSchedulerPeer::RESULT, $this->result);
        }

        if ($this->isColumnModified(LogCasesSchedulerPeer::SCH_UID)) {
            $criteria->add(LogCasesSchedulerPeer::SCH_UID, $this->sch_uid);
        }

        if ($this->isColumnModified(LogCasesSchedulerPeer::WS_CREATE_CASE_STATUS)) {
            $criteria->add(LogCasesSchedulerPeer::WS_CREATE_CASE_STATUS, $this->ws_create_case_status);
        }

        if ($this->isColumnModified(LogCasesSchedulerPeer::WS_ROUTE_CASE_STATUS)) {
            $criteria->add(LogCasesSchedulerPeer::WS_ROUTE_CASE_STATUS, $this->ws_route_case_status);
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
        $criteria = new Criteria(LogCasesSchedulerPeer::DATABASE_NAME);

        $criteria->add(LogCasesSchedulerPeer::LOG_CASE_UID, $this->log_case_uid);

        return $criteria;
    }

    /**
     * Returns the primary key for this object (row).
     * @return     string
     */
    public function getPrimaryKey()
    {
        return $this->getLogCaseUid();
    }

    /**
     * Generic method to set the primary key (log_case_uid column).
     *
     * @param      string $key Primary key.
     * @return     void
     */
    public function setPrimaryKey($key)
    {
        $this->setLogCaseUid($key);
    }

    /**
     * Sets contents of passed object to values from current object.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param      object $copyObj An object of LogCasesScheduler (or compatible) type.
     * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @throws     PropelException
     */
    public function copyInto($copyObj, $deepCopy = false)
    {

        $copyObj->setProUid($this->pro_uid);

        $copyObj->setTasUid($this->tas_uid);

        $copyObj->setUsrName($this->usr_name);

        $copyObj->setExecDate($this->exec_date);

        $copyObj->setExecHour($this->exec_hour);

        $copyObj->setResult($this->result);

        $copyObj->setSchUid($this->sch_uid);

        $copyObj->setWsCreateCaseStatus($this->ws_create_case_status);

        $copyObj->setWsRouteCaseStatus($this->ws_route_case_status);


        $copyObj->setNew(true);

        $copyObj->setLogCaseUid(''); // this is a pkey column, so set to default value

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
     * @return     LogCasesScheduler Clone of current object.
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
     * @return     LogCasesSchedulerPeer
     */
    public function getPeer()
    {
        if (self::$peer === null) {
            self::$peer = new LogCasesSchedulerPeer();
        }
        return self::$peer;
    }
}

