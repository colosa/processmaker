<?php

require_once 'propel/om/BaseObject.php';

require_once 'propel/om/Persistent.php';


include_once 'propel/util/Criteria.php';

include_once 'classes/model/SubApplicationPeer.php';

/**
 * Base class that represents a row from the 'SUB_APPLICATION' table.
 *
 * 
 *
 * @package    workflow.classes.model.om
 */
abstract class BaseSubApplication extends BaseObject implements Persistent
{

    /**
     * The Peer class.
     * Instance provides a convenient way of calling static methods on a class
     * that calling code may not be able to identify.
     * @var        SubApplicationPeer
    */
    protected static $peer;

    /**
     * The value for the app_uid field.
     * @var        string
     */
    protected $app_uid = '';

    /**
     * The value for the app_parent field.
     * @var        string
     */
    protected $app_parent = '';

    /**
     * The value for the del_index_parent field.
     * @var        int
     */
    protected $del_index_parent = 0;

    /**
     * The value for the del_thread_parent field.
     * @var        int
     */
    protected $del_thread_parent = 0;

    /**
     * The value for the sa_status field.
     * @var        string
     */
    protected $sa_status = '';

    /**
     * The value for the sa_values_out field.
     * @var        string
     */
    protected $sa_values_out;

    /**
     * The value for the sa_values_in field.
     * @var        string
     */
    protected $sa_values_in;

    /**
     * The value for the sa_init_date field.
     * @var        int
     */
    protected $sa_init_date;

    /**
     * The value for the sa_finish_date field.
     * @var        int
     */
    protected $sa_finish_date;

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
     * Get the [app_parent] column value.
     * 
     * @return     string
     */
    public function getAppParent()
    {

        return $this->app_parent;
    }

    /**
     * Get the [del_index_parent] column value.
     * 
     * @return     int
     */
    public function getDelIndexParent()
    {

        return $this->del_index_parent;
    }

    /**
     * Get the [del_thread_parent] column value.
     * 
     * @return     int
     */
    public function getDelThreadParent()
    {

        return $this->del_thread_parent;
    }

    /**
     * Get the [sa_status] column value.
     * 
     * @return     string
     */
    public function getSaStatus()
    {

        return $this->sa_status;
    }

    /**
     * Get the [sa_values_out] column value.
     * 
     * @return     string
     */
    public function getSaValuesOut()
    {

        return $this->sa_values_out;
    }

    /**
     * Get the [sa_values_in] column value.
     * 
     * @return     string
     */
    public function getSaValuesIn()
    {

        return $this->sa_values_in;
    }

    /**
     * Get the [optionally formatted] [sa_init_date] column value.
     * 
     * @param      string $format The date/time format string (either date()-style or strftime()-style).
     *                          If format is NULL, then the integer unix timestamp will be returned.
     * @return     mixed Formatted date/time value as string or integer unix timestamp (if format is NULL).
     * @throws     PropelException - if unable to convert the date/time to timestamp.
     */
    public function getSaInitDate($format = 'Y-m-d H:i:s')
    {

        if ($this->sa_init_date === null || $this->sa_init_date === '') {
            return null;
        } elseif (!is_int($this->sa_init_date)) {
            // a non-timestamp value was set externally, so we convert it
            $ts = strtotime($this->sa_init_date);
            if ($ts === -1 || $ts === false) {
                throw new PropelException("Unable to parse value of [sa_init_date] as date/time value: " .
                    var_export($this->sa_init_date, true));
            }
        } else {
            $ts = $this->sa_init_date;
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
     * Get the [optionally formatted] [sa_finish_date] column value.
     * 
     * @param      string $format The date/time format string (either date()-style or strftime()-style).
     *                          If format is NULL, then the integer unix timestamp will be returned.
     * @return     mixed Formatted date/time value as string or integer unix timestamp (if format is NULL).
     * @throws     PropelException - if unable to convert the date/time to timestamp.
     */
    public function getSaFinishDate($format = 'Y-m-d H:i:s')
    {

        if ($this->sa_finish_date === null || $this->sa_finish_date === '') {
            return null;
        } elseif (!is_int($this->sa_finish_date)) {
            // a non-timestamp value was set externally, so we convert it
            $ts = strtotime($this->sa_finish_date);
            if ($ts === -1 || $ts === false) {
                throw new PropelException("Unable to parse value of [sa_finish_date] as date/time value: " .
                    var_export($this->sa_finish_date, true));
            }
        } else {
            $ts = $this->sa_finish_date;
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
            $this->modifiedColumns[] = SubApplicationPeer::APP_UID;
        }

    } // setAppUid()

    /**
     * Set the value of [app_parent] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setAppParent($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->app_parent !== $v || $v === '') {
            $this->app_parent = $v;
            $this->modifiedColumns[] = SubApplicationPeer::APP_PARENT;
        }

    } // setAppParent()

    /**
     * Set the value of [del_index_parent] column.
     * 
     * @param      int $v new value
     * @return     void
     */
    public function setDelIndexParent($v)
    {

        // Since the native PHP type for this column is integer,
        // we will cast the input value to an int (if it is not).
        if ($v !== null && !is_int($v) && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->del_index_parent !== $v || $v === 0) {
            $this->del_index_parent = $v;
            $this->modifiedColumns[] = SubApplicationPeer::DEL_INDEX_PARENT;
        }

    } // setDelIndexParent()

    /**
     * Set the value of [del_thread_parent] column.
     * 
     * @param      int $v new value
     * @return     void
     */
    public function setDelThreadParent($v)
    {

        // Since the native PHP type for this column is integer,
        // we will cast the input value to an int (if it is not).
        if ($v !== null && !is_int($v) && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->del_thread_parent !== $v || $v === 0) {
            $this->del_thread_parent = $v;
            $this->modifiedColumns[] = SubApplicationPeer::DEL_THREAD_PARENT;
        }

    } // setDelThreadParent()

    /**
     * Set the value of [sa_status] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setSaStatus($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->sa_status !== $v || $v === '') {
            $this->sa_status = $v;
            $this->modifiedColumns[] = SubApplicationPeer::SA_STATUS;
        }

    } // setSaStatus()

    /**
     * Set the value of [sa_values_out] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setSaValuesOut($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->sa_values_out !== $v) {
            $this->sa_values_out = $v;
            $this->modifiedColumns[] = SubApplicationPeer::SA_VALUES_OUT;
        }

    } // setSaValuesOut()

    /**
     * Set the value of [sa_values_in] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setSaValuesIn($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->sa_values_in !== $v) {
            $this->sa_values_in = $v;
            $this->modifiedColumns[] = SubApplicationPeer::SA_VALUES_IN;
        }

    } // setSaValuesIn()

    /**
     * Set the value of [sa_init_date] column.
     * 
     * @param      int $v new value
     * @return     void
     */
    public function setSaInitDate($v)
    {

        if ($v !== null && !is_int($v)) {
            $ts = strtotime($v);
            if ($ts === -1 || $ts === false) {
                throw new PropelException("Unable to parse date/time value for [sa_init_date] from input: " .
                    var_export($v, true));
            }
        } else {
            $ts = $v;
        }
        if ($this->sa_init_date !== $ts) {
            $this->sa_init_date = $ts;
            $this->modifiedColumns[] = SubApplicationPeer::SA_INIT_DATE;
        }

    } // setSaInitDate()

    /**
     * Set the value of [sa_finish_date] column.
     * 
     * @param      int $v new value
     * @return     void
     */
    public function setSaFinishDate($v)
    {

        if ($v !== null && !is_int($v)) {
            $ts = strtotime($v);
            if ($ts === -1 || $ts === false) {
                throw new PropelException("Unable to parse date/time value for [sa_finish_date] from input: " .
                    var_export($v, true));
            }
        } else {
            $ts = $v;
        }
        if ($this->sa_finish_date !== $ts) {
            $this->sa_finish_date = $ts;
            $this->modifiedColumns[] = SubApplicationPeer::SA_FINISH_DATE;
        }

    } // setSaFinishDate()

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

            $this->app_parent = $rs->getString($startcol + 1);

            $this->del_index_parent = $rs->getInt($startcol + 2);

            $this->del_thread_parent = $rs->getInt($startcol + 3);

            $this->sa_status = $rs->getString($startcol + 4);

            $this->sa_values_out = $rs->getString($startcol + 5);

            $this->sa_values_in = $rs->getString($startcol + 6);

            $this->sa_init_date = $rs->getTimestamp($startcol + 7, null);

            $this->sa_finish_date = $rs->getTimestamp($startcol + 8, null);

            $this->resetModified();

            $this->setNew(false);

            // FIXME - using NUM_COLUMNS may be clearer.
            return $startcol + 9; // 9 = SubApplicationPeer::NUM_COLUMNS - SubApplicationPeer::NUM_LAZY_LOAD_COLUMNS).

        } catch (Exception $e) {
            throw new PropelException("Error populating SubApplication object", $e);
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
            $con = Propel::getConnection(SubApplicationPeer::DATABASE_NAME);
        }

        try {
            $con->begin();
            SubApplicationPeer::doDelete($this, $con);
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
            $con = Propel::getConnection(SubApplicationPeer::DATABASE_NAME);
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
                    $pk = SubApplicationPeer::doInsert($this, $con);
                    $affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
                                         // should always be true here (even though technically
                                         // BasePeer::doInsert() can insert multiple rows).

                    $this->setNew(false);
                } else {
                    $affectedRows += SubApplicationPeer::doUpdate($this, $con);
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


            if (($retval = SubApplicationPeer::doValidate($this, $columns)) !== true) {
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
        $pos = SubApplicationPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                return $this->getAppParent();
                break;
            case 2:
                return $this->getDelIndexParent();
                break;
            case 3:
                return $this->getDelThreadParent();
                break;
            case 4:
                return $this->getSaStatus();
                break;
            case 5:
                return $this->getSaValuesOut();
                break;
            case 6:
                return $this->getSaValuesIn();
                break;
            case 7:
                return $this->getSaInitDate();
                break;
            case 8:
                return $this->getSaFinishDate();
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
        $keys = SubApplicationPeer::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getAppUid(),
            $keys[1] => $this->getAppParent(),
            $keys[2] => $this->getDelIndexParent(),
            $keys[3] => $this->getDelThreadParent(),
            $keys[4] => $this->getSaStatus(),
            $keys[5] => $this->getSaValuesOut(),
            $keys[6] => $this->getSaValuesIn(),
            $keys[7] => $this->getSaInitDate(),
            $keys[8] => $this->getSaFinishDate(),
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
        $pos = SubApplicationPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                $this->setAppParent($value);
                break;
            case 2:
                $this->setDelIndexParent($value);
                break;
            case 3:
                $this->setDelThreadParent($value);
                break;
            case 4:
                $this->setSaStatus($value);
                break;
            case 5:
                $this->setSaValuesOut($value);
                break;
            case 6:
                $this->setSaValuesIn($value);
                break;
            case 7:
                $this->setSaInitDate($value);
                break;
            case 8:
                $this->setSaFinishDate($value);
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
        $keys = SubApplicationPeer::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) {
            $this->setAppUid($arr[$keys[0]]);
        }

        if (array_key_exists($keys[1], $arr)) {
            $this->setAppParent($arr[$keys[1]]);
        }

        if (array_key_exists($keys[2], $arr)) {
            $this->setDelIndexParent($arr[$keys[2]]);
        }

        if (array_key_exists($keys[3], $arr)) {
            $this->setDelThreadParent($arr[$keys[3]]);
        }

        if (array_key_exists($keys[4], $arr)) {
            $this->setSaStatus($arr[$keys[4]]);
        }

        if (array_key_exists($keys[5], $arr)) {
            $this->setSaValuesOut($arr[$keys[5]]);
        }

        if (array_key_exists($keys[6], $arr)) {
            $this->setSaValuesIn($arr[$keys[6]]);
        }

        if (array_key_exists($keys[7], $arr)) {
            $this->setSaInitDate($arr[$keys[7]]);
        }

        if (array_key_exists($keys[8], $arr)) {
            $this->setSaFinishDate($arr[$keys[8]]);
        }

    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return     Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(SubApplicationPeer::DATABASE_NAME);

        if ($this->isColumnModified(SubApplicationPeer::APP_UID)) {
            $criteria->add(SubApplicationPeer::APP_UID, $this->app_uid);
        }

        if ($this->isColumnModified(SubApplicationPeer::APP_PARENT)) {
            $criteria->add(SubApplicationPeer::APP_PARENT, $this->app_parent);
        }

        if ($this->isColumnModified(SubApplicationPeer::DEL_INDEX_PARENT)) {
            $criteria->add(SubApplicationPeer::DEL_INDEX_PARENT, $this->del_index_parent);
        }

        if ($this->isColumnModified(SubApplicationPeer::DEL_THREAD_PARENT)) {
            $criteria->add(SubApplicationPeer::DEL_THREAD_PARENT, $this->del_thread_parent);
        }

        if ($this->isColumnModified(SubApplicationPeer::SA_STATUS)) {
            $criteria->add(SubApplicationPeer::SA_STATUS, $this->sa_status);
        }

        if ($this->isColumnModified(SubApplicationPeer::SA_VALUES_OUT)) {
            $criteria->add(SubApplicationPeer::SA_VALUES_OUT, $this->sa_values_out);
        }

        if ($this->isColumnModified(SubApplicationPeer::SA_VALUES_IN)) {
            $criteria->add(SubApplicationPeer::SA_VALUES_IN, $this->sa_values_in);
        }

        if ($this->isColumnModified(SubApplicationPeer::SA_INIT_DATE)) {
            $criteria->add(SubApplicationPeer::SA_INIT_DATE, $this->sa_init_date);
        }

        if ($this->isColumnModified(SubApplicationPeer::SA_FINISH_DATE)) {
            $criteria->add(SubApplicationPeer::SA_FINISH_DATE, $this->sa_finish_date);
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
        $criteria = new Criteria(SubApplicationPeer::DATABASE_NAME);

        $criteria->add(SubApplicationPeer::APP_UID, $this->app_uid);
        $criteria->add(SubApplicationPeer::APP_PARENT, $this->app_parent);
        $criteria->add(SubApplicationPeer::DEL_INDEX_PARENT, $this->del_index_parent);
        $criteria->add(SubApplicationPeer::DEL_THREAD_PARENT, $this->del_thread_parent);

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

        $pks[1] = $this->getAppParent();

        $pks[2] = $this->getDelIndexParent();

        $pks[3] = $this->getDelThreadParent();

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

        $this->setAppParent($keys[1]);

        $this->setDelIndexParent($keys[2]);

        $this->setDelThreadParent($keys[3]);

    }

    /**
     * Sets contents of passed object to values from current object.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param      object $copyObj An object of SubApplication (or compatible) type.
     * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @throws     PropelException
     */
    public function copyInto($copyObj, $deepCopy = false)
    {

        $copyObj->setSaStatus($this->sa_status);

        $copyObj->setSaValuesOut($this->sa_values_out);

        $copyObj->setSaValuesIn($this->sa_values_in);

        $copyObj->setSaInitDate($this->sa_init_date);

        $copyObj->setSaFinishDate($this->sa_finish_date);


        $copyObj->setNew(true);

        $copyObj->setAppUid(''); // this is a pkey column, so set to default value

        $copyObj->setAppParent(''); // this is a pkey column, so set to default value

        $copyObj->setDelIndexParent('0'); // this is a pkey column, so set to default value

        $copyObj->setDelThreadParent('0'); // this is a pkey column, so set to default value

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
     * @return     SubApplication Clone of current object.
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
     * @return     SubApplicationPeer
     */
    public function getPeer()
    {
        if (self::$peer === null) {
            self::$peer = new SubApplicationPeer();
        }
        return self::$peer;
    }
}

