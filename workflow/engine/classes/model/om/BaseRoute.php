<?php

require_once 'propel/om/BaseObject.php';

require_once 'propel/om/Persistent.php';


include_once 'propel/util/Criteria.php';

include_once 'classes/model/RoutePeer.php';

/**
 * Base class that represents a row from the 'ROUTE' table.
 *
 * 
 *
 * @package    workflow.classes.model.om
 */
abstract class BaseRoute extends BaseObject implements Persistent
{

    /**
     * The Peer class.
     * Instance provides a convenient way of calling static methods on a class
     * that calling code may not be able to identify.
     * @var        RoutePeer
    */
    protected static $peer;

    /**
     * The value for the rou_uid field.
     * @var        string
     */
    protected $rou_uid = '';

    /**
     * The value for the rou_parent field.
     * @var        string
     */
    protected $rou_parent = '0';

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
     * The value for the rou_next_task field.
     * @var        string
     */
    protected $rou_next_task = '0';

    /**
     * The value for the rou_case field.
     * @var        int
     */
    protected $rou_case = 0;

    /**
     * The value for the rou_type field.
     * @var        string
     */
    protected $rou_type = 'SEQUENTIAL';

    /**
     * The value for the rou_condition field.
     * @var        string
     */
    protected $rou_condition = '';

    /**
     * The value for the rou_to_last_user field.
     * @var        string
     */
    protected $rou_to_last_user = 'FALSE';

    /**
     * The value for the rou_optional field.
     * @var        string
     */
    protected $rou_optional = 'FALSE';

    /**
     * The value for the rou_send_email field.
     * @var        string
     */
    protected $rou_send_email = 'TRUE';

    /**
     * The value for the rou_sourceanchor field.
     * @var        int
     */
    protected $rou_sourceanchor = 1;

    /**
     * The value for the rou_targetanchor field.
     * @var        int
     */
    protected $rou_targetanchor = 0;

    /**
     * The value for the rou_to_port field.
     * @var        int
     */
    protected $rou_to_port = 1;

    /**
     * The value for the rou_from_port field.
     * @var        int
     */
    protected $rou_from_port = 2;

    /**
     * The value for the rou_evn_uid field.
     * @var        string
     */
    protected $rou_evn_uid = '';

    /**
     * The value for the gat_uid field.
     * @var        string
     */
    protected $gat_uid = '';

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
     * Get the [rou_uid] column value.
     * 
     * @return     string
     */
    public function getRouUid()
    {

        return $this->rou_uid;
    }

    /**
     * Get the [rou_parent] column value.
     * 
     * @return     string
     */
    public function getRouParent()
    {

        return $this->rou_parent;
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
     * Get the [rou_next_task] column value.
     * 
     * @return     string
     */
    public function getRouNextTask()
    {

        return $this->rou_next_task;
    }

    /**
     * Get the [rou_case] column value.
     * 
     * @return     int
     */
    public function getRouCase()
    {

        return $this->rou_case;
    }

    /**
     * Get the [rou_type] column value.
     * 
     * @return     string
     */
    public function getRouType()
    {

        return $this->rou_type;
    }

    /**
     * Get the [rou_condition] column value.
     * 
     * @return     string
     */
    public function getRouCondition()
    {

        return $this->rou_condition;
    }

    /**
     * Get the [rou_to_last_user] column value.
     * 
     * @return     string
     */
    public function getRouToLastUser()
    {

        return $this->rou_to_last_user;
    }

    /**
     * Get the [rou_optional] column value.
     * 
     * @return     string
     */
    public function getRouOptional()
    {

        return $this->rou_optional;
    }

    /**
     * Get the [rou_send_email] column value.
     * 
     * @return     string
     */
    public function getRouSendEmail()
    {

        return $this->rou_send_email;
    }

    /**
     * Get the [rou_sourceanchor] column value.
     * 
     * @return     int
     */
    public function getRouSourceanchor()
    {

        return $this->rou_sourceanchor;
    }

    /**
     * Get the [rou_targetanchor] column value.
     * 
     * @return     int
     */
    public function getRouTargetanchor()
    {

        return $this->rou_targetanchor;
    }

    /**
     * Get the [rou_to_port] column value.
     * 
     * @return     int
     */
    public function getRouToPort()
    {

        return $this->rou_to_port;
    }

    /**
     * Get the [rou_from_port] column value.
     * 
     * @return     int
     */
    public function getRouFromPort()
    {

        return $this->rou_from_port;
    }

    /**
     * Get the [rou_evn_uid] column value.
     * 
     * @return     string
     */
    public function getRouEvnUid()
    {

        return $this->rou_evn_uid;
    }

    /**
     * Get the [gat_uid] column value.
     * 
     * @return     string
     */
    public function getGatUid()
    {

        return $this->gat_uid;
    }

    /**
     * Set the value of [rou_uid] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setRouUid($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->rou_uid !== $v || $v === '') {
            $this->rou_uid = $v;
            $this->modifiedColumns[] = RoutePeer::ROU_UID;
        }

    } // setRouUid()

    /**
     * Set the value of [rou_parent] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setRouParent($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->rou_parent !== $v || $v === '0') {
            $this->rou_parent = $v;
            $this->modifiedColumns[] = RoutePeer::ROU_PARENT;
        }

    } // setRouParent()

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
            $this->modifiedColumns[] = RoutePeer::PRO_UID;
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
            $this->modifiedColumns[] = RoutePeer::TAS_UID;
        }

    } // setTasUid()

    /**
     * Set the value of [rou_next_task] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setRouNextTask($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->rou_next_task !== $v || $v === '0') {
            $this->rou_next_task = $v;
            $this->modifiedColumns[] = RoutePeer::ROU_NEXT_TASK;
        }

    } // setRouNextTask()

    /**
     * Set the value of [rou_case] column.
     * 
     * @param      int $v new value
     * @return     void
     */
    public function setRouCase($v)
    {

        // Since the native PHP type for this column is integer,
        // we will cast the input value to an int (if it is not).
        if ($v !== null && !is_int($v) && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->rou_case !== $v || $v === 0) {
            $this->rou_case = $v;
            $this->modifiedColumns[] = RoutePeer::ROU_CASE;
        }

    } // setRouCase()

    /**
     * Set the value of [rou_type] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setRouType($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->rou_type !== $v || $v === 'SEQUENTIAL') {
            $this->rou_type = $v;
            $this->modifiedColumns[] = RoutePeer::ROU_TYPE;
        }

    } // setRouType()

    /**
     * Set the value of [rou_condition] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setRouCondition($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->rou_condition !== $v || $v === '') {
            $this->rou_condition = $v;
            $this->modifiedColumns[] = RoutePeer::ROU_CONDITION;
        }

    } // setRouCondition()

    /**
     * Set the value of [rou_to_last_user] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setRouToLastUser($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->rou_to_last_user !== $v || $v === 'FALSE') {
            $this->rou_to_last_user = $v;
            $this->modifiedColumns[] = RoutePeer::ROU_TO_LAST_USER;
        }

    } // setRouToLastUser()

    /**
     * Set the value of [rou_optional] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setRouOptional($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->rou_optional !== $v || $v === 'FALSE') {
            $this->rou_optional = $v;
            $this->modifiedColumns[] = RoutePeer::ROU_OPTIONAL;
        }

    } // setRouOptional()

    /**
     * Set the value of [rou_send_email] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setRouSendEmail($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->rou_send_email !== $v || $v === 'TRUE') {
            $this->rou_send_email = $v;
            $this->modifiedColumns[] = RoutePeer::ROU_SEND_EMAIL;
        }

    } // setRouSendEmail()

    /**
     * Set the value of [rou_sourceanchor] column.
     * 
     * @param      int $v new value
     * @return     void
     */
    public function setRouSourceanchor($v)
    {

        // Since the native PHP type for this column is integer,
        // we will cast the input value to an int (if it is not).
        if ($v !== null && !is_int($v) && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->rou_sourceanchor !== $v || $v === 1) {
            $this->rou_sourceanchor = $v;
            $this->modifiedColumns[] = RoutePeer::ROU_SOURCEANCHOR;
        }

    } // setRouSourceanchor()

    /**
     * Set the value of [rou_targetanchor] column.
     * 
     * @param      int $v new value
     * @return     void
     */
    public function setRouTargetanchor($v)
    {

        // Since the native PHP type for this column is integer,
        // we will cast the input value to an int (if it is not).
        if ($v !== null && !is_int($v) && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->rou_targetanchor !== $v || $v === 0) {
            $this->rou_targetanchor = $v;
            $this->modifiedColumns[] = RoutePeer::ROU_TARGETANCHOR;
        }

    } // setRouTargetanchor()

    /**
     * Set the value of [rou_to_port] column.
     * 
     * @param      int $v new value
     * @return     void
     */
    public function setRouToPort($v)
    {

        // Since the native PHP type for this column is integer,
        // we will cast the input value to an int (if it is not).
        if ($v !== null && !is_int($v) && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->rou_to_port !== $v || $v === 1) {
            $this->rou_to_port = $v;
            $this->modifiedColumns[] = RoutePeer::ROU_TO_PORT;
        }

    } // setRouToPort()

    /**
     * Set the value of [rou_from_port] column.
     * 
     * @param      int $v new value
     * @return     void
     */
    public function setRouFromPort($v)
    {

        // Since the native PHP type for this column is integer,
        // we will cast the input value to an int (if it is not).
        if ($v !== null && !is_int($v) && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->rou_from_port !== $v || $v === 2) {
            $this->rou_from_port = $v;
            $this->modifiedColumns[] = RoutePeer::ROU_FROM_PORT;
        }

    } // setRouFromPort()

    /**
     * Set the value of [rou_evn_uid] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setRouEvnUid($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->rou_evn_uid !== $v || $v === '') {
            $this->rou_evn_uid = $v;
            $this->modifiedColumns[] = RoutePeer::ROU_EVN_UID;
        }

    } // setRouEvnUid()

    /**
     * Set the value of [gat_uid] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setGatUid($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->gat_uid !== $v || $v === '') {
            $this->gat_uid = $v;
            $this->modifiedColumns[] = RoutePeer::GAT_UID;
        }

    } // setGatUid()

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

            $this->rou_uid = $rs->getString($startcol + 0);

            $this->rou_parent = $rs->getString($startcol + 1);

            $this->pro_uid = $rs->getString($startcol + 2);

            $this->tas_uid = $rs->getString($startcol + 3);

            $this->rou_next_task = $rs->getString($startcol + 4);

            $this->rou_case = $rs->getInt($startcol + 5);

            $this->rou_type = $rs->getString($startcol + 6);

            $this->rou_condition = $rs->getString($startcol + 7);

            $this->rou_to_last_user = $rs->getString($startcol + 8);

            $this->rou_optional = $rs->getString($startcol + 9);

            $this->rou_send_email = $rs->getString($startcol + 10);

            $this->rou_sourceanchor = $rs->getInt($startcol + 11);

            $this->rou_targetanchor = $rs->getInt($startcol + 12);

            $this->rou_to_port = $rs->getInt($startcol + 13);

            $this->rou_from_port = $rs->getInt($startcol + 14);

            $this->rou_evn_uid = $rs->getString($startcol + 15);

            $this->gat_uid = $rs->getString($startcol + 16);

            $this->resetModified();

            $this->setNew(false);

            // FIXME - using NUM_COLUMNS may be clearer.
            return $startcol + 17; // 17 = RoutePeer::NUM_COLUMNS - RoutePeer::NUM_LAZY_LOAD_COLUMNS).

        } catch (Exception $e) {
            throw new PropelException("Error populating Route object", $e);
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
            $con = Propel::getConnection(RoutePeer::DATABASE_NAME);
        }

        try {
            $con->begin();
            RoutePeer::doDelete($this, $con);
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
            $con = Propel::getConnection(RoutePeer::DATABASE_NAME);
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
                    $pk = RoutePeer::doInsert($this, $con);
                    $affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
                                         // should always be true here (even though technically
                                         // BasePeer::doInsert() can insert multiple rows).

                    $this->setNew(false);
                } else {
                    $affectedRows += RoutePeer::doUpdate($this, $con);
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


            if (($retval = RoutePeer::doValidate($this, $columns)) !== true) {
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
        $pos = RoutePeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                return $this->getRouUid();
                break;
            case 1:
                return $this->getRouParent();
                break;
            case 2:
                return $this->getProUid();
                break;
            case 3:
                return $this->getTasUid();
                break;
            case 4:
                return $this->getRouNextTask();
                break;
            case 5:
                return $this->getRouCase();
                break;
            case 6:
                return $this->getRouType();
                break;
            case 7:
                return $this->getRouCondition();
                break;
            case 8:
                return $this->getRouToLastUser();
                break;
            case 9:
                return $this->getRouOptional();
                break;
            case 10:
                return $this->getRouSendEmail();
                break;
            case 11:
                return $this->getRouSourceanchor();
                break;
            case 12:
                return $this->getRouTargetanchor();
                break;
            case 13:
                return $this->getRouToPort();
                break;
            case 14:
                return $this->getRouFromPort();
                break;
            case 15:
                return $this->getRouEvnUid();
                break;
            case 16:
                return $this->getGatUid();
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
        $keys = RoutePeer::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getRouUid(),
            $keys[1] => $this->getRouParent(),
            $keys[2] => $this->getProUid(),
            $keys[3] => $this->getTasUid(),
            $keys[4] => $this->getRouNextTask(),
            $keys[5] => $this->getRouCase(),
            $keys[6] => $this->getRouType(),
            $keys[7] => $this->getRouCondition(),
            $keys[8] => $this->getRouToLastUser(),
            $keys[9] => $this->getRouOptional(),
            $keys[10] => $this->getRouSendEmail(),
            $keys[11] => $this->getRouSourceanchor(),
            $keys[12] => $this->getRouTargetanchor(),
            $keys[13] => $this->getRouToPort(),
            $keys[14] => $this->getRouFromPort(),
            $keys[15] => $this->getRouEvnUid(),
            $keys[16] => $this->getGatUid(),
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
        $pos = RoutePeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                $this->setRouUid($value);
                break;
            case 1:
                $this->setRouParent($value);
                break;
            case 2:
                $this->setProUid($value);
                break;
            case 3:
                $this->setTasUid($value);
                break;
            case 4:
                $this->setRouNextTask($value);
                break;
            case 5:
                $this->setRouCase($value);
                break;
            case 6:
                $this->setRouType($value);
                break;
            case 7:
                $this->setRouCondition($value);
                break;
            case 8:
                $this->setRouToLastUser($value);
                break;
            case 9:
                $this->setRouOptional($value);
                break;
            case 10:
                $this->setRouSendEmail($value);
                break;
            case 11:
                $this->setRouSourceanchor($value);
                break;
            case 12:
                $this->setRouTargetanchor($value);
                break;
            case 13:
                $this->setRouToPort($value);
                break;
            case 14:
                $this->setRouFromPort($value);
                break;
            case 15:
                $this->setRouEvnUid($value);
                break;
            case 16:
                $this->setGatUid($value);
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
        $keys = RoutePeer::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) {
            $this->setRouUid($arr[$keys[0]]);
        }

        if (array_key_exists($keys[1], $arr)) {
            $this->setRouParent($arr[$keys[1]]);
        }

        if (array_key_exists($keys[2], $arr)) {
            $this->setProUid($arr[$keys[2]]);
        }

        if (array_key_exists($keys[3], $arr)) {
            $this->setTasUid($arr[$keys[3]]);
        }

        if (array_key_exists($keys[4], $arr)) {
            $this->setRouNextTask($arr[$keys[4]]);
        }

        if (array_key_exists($keys[5], $arr)) {
            $this->setRouCase($arr[$keys[5]]);
        }

        if (array_key_exists($keys[6], $arr)) {
            $this->setRouType($arr[$keys[6]]);
        }

        if (array_key_exists($keys[7], $arr)) {
            $this->setRouCondition($arr[$keys[7]]);
        }

        if (array_key_exists($keys[8], $arr)) {
            $this->setRouToLastUser($arr[$keys[8]]);
        }

        if (array_key_exists($keys[9], $arr)) {
            $this->setRouOptional($arr[$keys[9]]);
        }

        if (array_key_exists($keys[10], $arr)) {
            $this->setRouSendEmail($arr[$keys[10]]);
        }

        if (array_key_exists($keys[11], $arr)) {
            $this->setRouSourceanchor($arr[$keys[11]]);
        }

        if (array_key_exists($keys[12], $arr)) {
            $this->setRouTargetanchor($arr[$keys[12]]);
        }

        if (array_key_exists($keys[13], $arr)) {
            $this->setRouToPort($arr[$keys[13]]);
        }

        if (array_key_exists($keys[14], $arr)) {
            $this->setRouFromPort($arr[$keys[14]]);
        }

        if (array_key_exists($keys[15], $arr)) {
            $this->setRouEvnUid($arr[$keys[15]]);
        }

        if (array_key_exists($keys[16], $arr)) {
            $this->setGatUid($arr[$keys[16]]);
        }

    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return     Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(RoutePeer::DATABASE_NAME);

        if ($this->isColumnModified(RoutePeer::ROU_UID)) {
            $criteria->add(RoutePeer::ROU_UID, $this->rou_uid);
        }

        if ($this->isColumnModified(RoutePeer::ROU_PARENT)) {
            $criteria->add(RoutePeer::ROU_PARENT, $this->rou_parent);
        }

        if ($this->isColumnModified(RoutePeer::PRO_UID)) {
            $criteria->add(RoutePeer::PRO_UID, $this->pro_uid);
        }

        if ($this->isColumnModified(RoutePeer::TAS_UID)) {
            $criteria->add(RoutePeer::TAS_UID, $this->tas_uid);
        }

        if ($this->isColumnModified(RoutePeer::ROU_NEXT_TASK)) {
            $criteria->add(RoutePeer::ROU_NEXT_TASK, $this->rou_next_task);
        }

        if ($this->isColumnModified(RoutePeer::ROU_CASE)) {
            $criteria->add(RoutePeer::ROU_CASE, $this->rou_case);
        }

        if ($this->isColumnModified(RoutePeer::ROU_TYPE)) {
            $criteria->add(RoutePeer::ROU_TYPE, $this->rou_type);
        }

        if ($this->isColumnModified(RoutePeer::ROU_CONDITION)) {
            $criteria->add(RoutePeer::ROU_CONDITION, $this->rou_condition);
        }

        if ($this->isColumnModified(RoutePeer::ROU_TO_LAST_USER)) {
            $criteria->add(RoutePeer::ROU_TO_LAST_USER, $this->rou_to_last_user);
        }

        if ($this->isColumnModified(RoutePeer::ROU_OPTIONAL)) {
            $criteria->add(RoutePeer::ROU_OPTIONAL, $this->rou_optional);
        }

        if ($this->isColumnModified(RoutePeer::ROU_SEND_EMAIL)) {
            $criteria->add(RoutePeer::ROU_SEND_EMAIL, $this->rou_send_email);
        }

        if ($this->isColumnModified(RoutePeer::ROU_SOURCEANCHOR)) {
            $criteria->add(RoutePeer::ROU_SOURCEANCHOR, $this->rou_sourceanchor);
        }

        if ($this->isColumnModified(RoutePeer::ROU_TARGETANCHOR)) {
            $criteria->add(RoutePeer::ROU_TARGETANCHOR, $this->rou_targetanchor);
        }

        if ($this->isColumnModified(RoutePeer::ROU_TO_PORT)) {
            $criteria->add(RoutePeer::ROU_TO_PORT, $this->rou_to_port);
        }

        if ($this->isColumnModified(RoutePeer::ROU_FROM_PORT)) {
            $criteria->add(RoutePeer::ROU_FROM_PORT, $this->rou_from_port);
        }

        if ($this->isColumnModified(RoutePeer::ROU_EVN_UID)) {
            $criteria->add(RoutePeer::ROU_EVN_UID, $this->rou_evn_uid);
        }

        if ($this->isColumnModified(RoutePeer::GAT_UID)) {
            $criteria->add(RoutePeer::GAT_UID, $this->gat_uid);
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
        $criteria = new Criteria(RoutePeer::DATABASE_NAME);

        $criteria->add(RoutePeer::ROU_UID, $this->rou_uid);

        return $criteria;
    }

    /**
     * Returns the primary key for this object (row).
     * @return     string
     */
    public function getPrimaryKey()
    {
        return $this->getRouUid();
    }

    /**
     * Generic method to set the primary key (rou_uid column).
     *
     * @param      string $key Primary key.
     * @return     void
     */
    public function setPrimaryKey($key)
    {
        $this->setRouUid($key);
    }

    /**
     * Sets contents of passed object to values from current object.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param      object $copyObj An object of Route (or compatible) type.
     * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @throws     PropelException
     */
    public function copyInto($copyObj, $deepCopy = false)
    {

        $copyObj->setRouParent($this->rou_parent);

        $copyObj->setProUid($this->pro_uid);

        $copyObj->setTasUid($this->tas_uid);

        $copyObj->setRouNextTask($this->rou_next_task);

        $copyObj->setRouCase($this->rou_case);

        $copyObj->setRouType($this->rou_type);

        $copyObj->setRouCondition($this->rou_condition);

        $copyObj->setRouToLastUser($this->rou_to_last_user);

        $copyObj->setRouOptional($this->rou_optional);

        $copyObj->setRouSendEmail($this->rou_send_email);

        $copyObj->setRouSourceanchor($this->rou_sourceanchor);

        $copyObj->setRouTargetanchor($this->rou_targetanchor);

        $copyObj->setRouToPort($this->rou_to_port);

        $copyObj->setRouFromPort($this->rou_from_port);

        $copyObj->setRouEvnUid($this->rou_evn_uid);

        $copyObj->setGatUid($this->gat_uid);


        $copyObj->setNew(true);

        $copyObj->setRouUid(''); // this is a pkey column, so set to default value

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
     * @return     Route Clone of current object.
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
     * @return     RoutePeer
     */
    public function getPeer()
    {
        if (self::$peer === null) {
            self::$peer = new RoutePeer();
        }
        return self::$peer;
    }
}

