<?php

require_once 'propel/om/BaseObject.php';

require_once 'propel/om/Persistent.php';


include_once 'propel/util/Criteria.php';

include_once 'classes/model/SessionPeer.php';

/**
 * Base class that represents a row from the 'SESSION' table.
 *
 * 
 *
 * @package    workflow.classes.model.om
 */
abstract class BaseSession extends BaseObject implements Persistent
{

    /**
     * The Peer class.
     * Instance provides a convenient way of calling static methods on a class
     * that calling code may not be able to identify.
     * @var        SessionPeer
    */
    protected static $peer;

    /**
     * The value for the ses_uid field.
     * @var        string
     */
    protected $ses_uid = '';

    /**
     * The value for the ses_status field.
     * @var        string
     */
    protected $ses_status = 'ACTIVE';

    /**
     * The value for the usr_uid field.
     * @var        string
     */
    protected $usr_uid = 'ACTIVE';

    /**
     * The value for the ses_remote_ip field.
     * @var        string
     */
    protected $ses_remote_ip = '0.0.0.0';

    /**
     * The value for the ses_init_date field.
     * @var        string
     */
    protected $ses_init_date = '';

    /**
     * The value for the ses_due_date field.
     * @var        string
     */
    protected $ses_due_date = '';

    /**
     * The value for the ses_end_date field.
     * @var        string
     */
    protected $ses_end_date = '';

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
     * Get the [ses_uid] column value.
     * 
     * @return     string
     */
    public function getSesUid()
    {

        return $this->ses_uid;
    }

    /**
     * Get the [ses_status] column value.
     * 
     * @return     string
     */
    public function getSesStatus()
    {

        return $this->ses_status;
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
     * Get the [ses_remote_ip] column value.
     * 
     * @return     string
     */
    public function getSesRemoteIp()
    {

        return $this->ses_remote_ip;
    }

    /**
     * Get the [ses_init_date] column value.
     * 
     * @return     string
     */
    public function getSesInitDate()
    {

        return $this->ses_init_date;
    }

    /**
     * Get the [ses_due_date] column value.
     * 
     * @return     string
     */
    public function getSesDueDate()
    {

        return $this->ses_due_date;
    }

    /**
     * Get the [ses_end_date] column value.
     * 
     * @return     string
     */
    public function getSesEndDate()
    {

        return $this->ses_end_date;
    }

    /**
     * Set the value of [ses_uid] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setSesUid($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->ses_uid !== $v || $v === '') {
            $this->ses_uid = $v;
            $this->modifiedColumns[] = SessionPeer::SES_UID;
        }

    } // setSesUid()

    /**
     * Set the value of [ses_status] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setSesStatus($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->ses_status !== $v || $v === 'ACTIVE') {
            $this->ses_status = $v;
            $this->modifiedColumns[] = SessionPeer::SES_STATUS;
        }

    } // setSesStatus()

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

        if ($this->usr_uid !== $v || $v === 'ACTIVE') {
            $this->usr_uid = $v;
            $this->modifiedColumns[] = SessionPeer::USR_UID;
        }

    } // setUsrUid()

    /**
     * Set the value of [ses_remote_ip] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setSesRemoteIp($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->ses_remote_ip !== $v || $v === '0.0.0.0') {
            $this->ses_remote_ip = $v;
            $this->modifiedColumns[] = SessionPeer::SES_REMOTE_IP;
        }

    } // setSesRemoteIp()

    /**
     * Set the value of [ses_init_date] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setSesInitDate($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->ses_init_date !== $v || $v === '') {
            $this->ses_init_date = $v;
            $this->modifiedColumns[] = SessionPeer::SES_INIT_DATE;
        }

    } // setSesInitDate()

    /**
     * Set the value of [ses_due_date] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setSesDueDate($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->ses_due_date !== $v || $v === '') {
            $this->ses_due_date = $v;
            $this->modifiedColumns[] = SessionPeer::SES_DUE_DATE;
        }

    } // setSesDueDate()

    /**
     * Set the value of [ses_end_date] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setSesEndDate($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->ses_end_date !== $v || $v === '') {
            $this->ses_end_date = $v;
            $this->modifiedColumns[] = SessionPeer::SES_END_DATE;
        }

    } // setSesEndDate()

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

            $this->ses_uid = $rs->getString($startcol + 0);

            $this->ses_status = $rs->getString($startcol + 1);

            $this->usr_uid = $rs->getString($startcol + 2);

            $this->ses_remote_ip = $rs->getString($startcol + 3);

            $this->ses_init_date = $rs->getString($startcol + 4);

            $this->ses_due_date = $rs->getString($startcol + 5);

            $this->ses_end_date = $rs->getString($startcol + 6);

            $this->resetModified();

            $this->setNew(false);

            // FIXME - using NUM_COLUMNS may be clearer.
            return $startcol + 7; // 7 = SessionPeer::NUM_COLUMNS - SessionPeer::NUM_LAZY_LOAD_COLUMNS).

        } catch (Exception $e) {
            throw new PropelException("Error populating Session object", $e);
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
            $con = Propel::getConnection(SessionPeer::DATABASE_NAME);
        }

        try {
            $con->begin();
            SessionPeer::doDelete($this, $con);
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
            $con = Propel::getConnection(SessionPeer::DATABASE_NAME);
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
                    $pk = SessionPeer::doInsert($this, $con);
                    $affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
                                         // should always be true here (even though technically
                                         // BasePeer::doInsert() can insert multiple rows).

                    $this->setNew(false);
                } else {
                    $affectedRows += SessionPeer::doUpdate($this, $con);
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


            if (($retval = SessionPeer::doValidate($this, $columns)) !== true) {
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
        $pos = SessionPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                return $this->getSesUid();
                break;
            case 1:
                return $this->getSesStatus();
                break;
            case 2:
                return $this->getUsrUid();
                break;
            case 3:
                return $this->getSesRemoteIp();
                break;
            case 4:
                return $this->getSesInitDate();
                break;
            case 5:
                return $this->getSesDueDate();
                break;
            case 6:
                return $this->getSesEndDate();
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
        $keys = SessionPeer::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getSesUid(),
            $keys[1] => $this->getSesStatus(),
            $keys[2] => $this->getUsrUid(),
            $keys[3] => $this->getSesRemoteIp(),
            $keys[4] => $this->getSesInitDate(),
            $keys[5] => $this->getSesDueDate(),
            $keys[6] => $this->getSesEndDate(),
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
        $pos = SessionPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                $this->setSesUid($value);
                break;
            case 1:
                $this->setSesStatus($value);
                break;
            case 2:
                $this->setUsrUid($value);
                break;
            case 3:
                $this->setSesRemoteIp($value);
                break;
            case 4:
                $this->setSesInitDate($value);
                break;
            case 5:
                $this->setSesDueDate($value);
                break;
            case 6:
                $this->setSesEndDate($value);
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
        $keys = SessionPeer::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) {
            $this->setSesUid($arr[$keys[0]]);
        }

        if (array_key_exists($keys[1], $arr)) {
            $this->setSesStatus($arr[$keys[1]]);
        }

        if (array_key_exists($keys[2], $arr)) {
            $this->setUsrUid($arr[$keys[2]]);
        }

        if (array_key_exists($keys[3], $arr)) {
            $this->setSesRemoteIp($arr[$keys[3]]);
        }

        if (array_key_exists($keys[4], $arr)) {
            $this->setSesInitDate($arr[$keys[4]]);
        }

        if (array_key_exists($keys[5], $arr)) {
            $this->setSesDueDate($arr[$keys[5]]);
        }

        if (array_key_exists($keys[6], $arr)) {
            $this->setSesEndDate($arr[$keys[6]]);
        }

    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return     Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(SessionPeer::DATABASE_NAME);

        if ($this->isColumnModified(SessionPeer::SES_UID)) {
            $criteria->add(SessionPeer::SES_UID, $this->ses_uid);
        }

        if ($this->isColumnModified(SessionPeer::SES_STATUS)) {
            $criteria->add(SessionPeer::SES_STATUS, $this->ses_status);
        }

        if ($this->isColumnModified(SessionPeer::USR_UID)) {
            $criteria->add(SessionPeer::USR_UID, $this->usr_uid);
        }

        if ($this->isColumnModified(SessionPeer::SES_REMOTE_IP)) {
            $criteria->add(SessionPeer::SES_REMOTE_IP, $this->ses_remote_ip);
        }

        if ($this->isColumnModified(SessionPeer::SES_INIT_DATE)) {
            $criteria->add(SessionPeer::SES_INIT_DATE, $this->ses_init_date);
        }

        if ($this->isColumnModified(SessionPeer::SES_DUE_DATE)) {
            $criteria->add(SessionPeer::SES_DUE_DATE, $this->ses_due_date);
        }

        if ($this->isColumnModified(SessionPeer::SES_END_DATE)) {
            $criteria->add(SessionPeer::SES_END_DATE, $this->ses_end_date);
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
        $criteria = new Criteria(SessionPeer::DATABASE_NAME);

        $criteria->add(SessionPeer::SES_UID, $this->ses_uid);

        return $criteria;
    }

    /**
     * Returns the primary key for this object (row).
     * @return     string
     */
    public function getPrimaryKey()
    {
        return $this->getSesUid();
    }

    /**
     * Generic method to set the primary key (ses_uid column).
     *
     * @param      string $key Primary key.
     * @return     void
     */
    public function setPrimaryKey($key)
    {
        $this->setSesUid($key);
    }

    /**
     * Sets contents of passed object to values from current object.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param      object $copyObj An object of Session (or compatible) type.
     * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @throws     PropelException
     */
    public function copyInto($copyObj, $deepCopy = false)
    {

        $copyObj->setSesStatus($this->ses_status);

        $copyObj->setUsrUid($this->usr_uid);

        $copyObj->setSesRemoteIp($this->ses_remote_ip);

        $copyObj->setSesInitDate($this->ses_init_date);

        $copyObj->setSesDueDate($this->ses_due_date);

        $copyObj->setSesEndDate($this->ses_end_date);


        $copyObj->setNew(true);

        $copyObj->setSesUid(''); // this is a pkey column, so set to default value

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
     * @return     Session Clone of current object.
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
     * @return     SessionPeer
     */
    public function getPeer()
    {
        if (self::$peer === null) {
            self::$peer = new SessionPeer();
        }
        return self::$peer;
    }
}

