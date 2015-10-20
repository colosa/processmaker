<?php

require_once 'propel/om/BaseObject.php';

require_once 'propel/om/Persistent.php';


include_once 'propel/util/Criteria.php';

include_once 'classes/model/NotificationDevicePeer.php';

/**
 * Base class that represents a row from the 'NOTIFICATION_DEVICE' table.
 *
 * 
 *
 * @package    workflow.classes.model.om
 */
abstract class BaseNotificationDevice extends BaseObject implements Persistent
{

    /**
     * The Peer class.
     * Instance provides a convenient way of calling static methods on a class
     * that calling code may not be able to identify.
     * @var        NotificationDevicePeer
    */
    protected static $peer;

    /**
     * The value for the dev_uid field.
     * @var        string
     */
    protected $dev_uid = '';

    /**
     * The value for the usr_uid field.
     * @var        string
     */
    protected $usr_uid = '';

    /**
     * The value for the sys_lang field.
     * @var        string
     */
    protected $sys_lang = '';

    /**
     * The value for the dev_reg_id field.
     * @var        string
     */
    protected $dev_reg_id = '';

    /**
     * The value for the dev_type field.
     * @var        string
     */
    protected $dev_type = '';

    /**
     * The value for the dev_create field.
     * @var        int
     */
    protected $dev_create;

    /**
     * The value for the dev_update field.
     * @var        int
     */
    protected $dev_update;

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
     * Get the [dev_uid] column value.
     * 
     * @return     string
     */
    public function getDevUid()
    {

        return $this->dev_uid;
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
     * Get the [sys_lang] column value.
     * 
     * @return     string
     */
    public function getSysLang()
    {

        return $this->sys_lang;
    }

    /**
     * Get the [dev_reg_id] column value.
     * 
     * @return     string
     */
    public function getDevRegId()
    {

        return $this->dev_reg_id;
    }

    /**
     * Get the [dev_type] column value.
     * 
     * @return     string
     */
    public function getDevType()
    {

        return $this->dev_type;
    }

    /**
     * Get the [optionally formatted] [dev_create] column value.
     * 
     * @param      string $format The date/time format string (either date()-style or strftime()-style).
     *                          If format is NULL, then the integer unix timestamp will be returned.
     * @return     mixed Formatted date/time value as string or integer unix timestamp (if format is NULL).
     * @throws     PropelException - if unable to convert the date/time to timestamp.
     */
    public function getDevCreate($format = 'Y-m-d H:i:s')
    {

        if ($this->dev_create === null || $this->dev_create === '') {
            return null;
        } elseif (!is_int($this->dev_create)) {
            // a non-timestamp value was set externally, so we convert it
            $ts = strtotime($this->dev_create);
            if ($ts === -1 || $ts === false) {
                throw new PropelException("Unable to parse value of [dev_create] as date/time value: " .
                    var_export($this->dev_create, true));
            }
        } else {
            $ts = $this->dev_create;
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
     * Get the [optionally formatted] [dev_update] column value.
     * 
     * @param      string $format The date/time format string (either date()-style or strftime()-style).
     *                          If format is NULL, then the integer unix timestamp will be returned.
     * @return     mixed Formatted date/time value as string or integer unix timestamp (if format is NULL).
     * @throws     PropelException - if unable to convert the date/time to timestamp.
     */
    public function getDevUpdate($format = 'Y-m-d H:i:s')
    {

        if ($this->dev_update === null || $this->dev_update === '') {
            return null;
        } elseif (!is_int($this->dev_update)) {
            // a non-timestamp value was set externally, so we convert it
            $ts = strtotime($this->dev_update);
            if ($ts === -1 || $ts === false) {
                throw new PropelException("Unable to parse value of [dev_update] as date/time value: " .
                    var_export($this->dev_update, true));
            }
        } else {
            $ts = $this->dev_update;
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
     * Set the value of [dev_uid] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setDevUid($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->dev_uid !== $v || $v === '') {
            $this->dev_uid = $v;
            $this->modifiedColumns[] = NotificationDevicePeer::DEV_UID;
        }

    } // setDevUid()

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
            $this->modifiedColumns[] = NotificationDevicePeer::USR_UID;
        }

    } // setUsrUid()

    /**
     * Set the value of [sys_lang] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setSysLang($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->sys_lang !== $v || $v === '') {
            $this->sys_lang = $v;
            $this->modifiedColumns[] = NotificationDevicePeer::SYS_LANG;
        }

    } // setSysLang()

    /**
     * Set the value of [dev_reg_id] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setDevRegId($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->dev_reg_id !== $v || $v === '') {
            $this->dev_reg_id = $v;
            $this->modifiedColumns[] = NotificationDevicePeer::DEV_REG_ID;
        }

    } // setDevRegId()

    /**
     * Set the value of [dev_type] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setDevType($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->dev_type !== $v || $v === '') {
            $this->dev_type = $v;
            $this->modifiedColumns[] = NotificationDevicePeer::DEV_TYPE;
        }

    } // setDevType()

    /**
     * Set the value of [dev_create] column.
     * 
     * @param      int $v new value
     * @return     void
     */
    public function setDevCreate($v)
    {

        if ($v !== null && !is_int($v)) {
            $ts = strtotime($v);
            //Date/time accepts null values
            if ($v == '') {
                $ts = null;
            }
            if ($ts === -1 || $ts === false) {
                throw new PropelException("Unable to parse date/time value for [dev_create] from input: " .
                    var_export($v, true));
            }
        } else {
            $ts = $v;
        }
        if ($this->dev_create !== $ts) {
            $this->dev_create = $ts;
            $this->modifiedColumns[] = NotificationDevicePeer::DEV_CREATE;
        }

    } // setDevCreate()

    /**
     * Set the value of [dev_update] column.
     * 
     * @param      int $v new value
     * @return     void
     */
    public function setDevUpdate($v)
    {

        if ($v !== null && !is_int($v)) {
            $ts = strtotime($v);
            //Date/time accepts null values
            if ($v == '') {
                $ts = null;
            }
            if ($ts === -1 || $ts === false) {
                throw new PropelException("Unable to parse date/time value for [dev_update] from input: " .
                    var_export($v, true));
            }
        } else {
            $ts = $v;
        }
        if ($this->dev_update !== $ts) {
            $this->dev_update = $ts;
            $this->modifiedColumns[] = NotificationDevicePeer::DEV_UPDATE;
        }

    } // setDevUpdate()

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

            $this->dev_uid = $rs->getString($startcol + 0);

            $this->usr_uid = $rs->getString($startcol + 1);

            $this->sys_lang = $rs->getString($startcol + 2);

            $this->dev_reg_id = $rs->getString($startcol + 3);

            $this->dev_type = $rs->getString($startcol + 4);

            $this->dev_create = $rs->getTimestamp($startcol + 5, null);

            $this->dev_update = $rs->getTimestamp($startcol + 6, null);

            $this->resetModified();

            $this->setNew(false);

            // FIXME - using NUM_COLUMNS may be clearer.
            return $startcol + 7; // 7 = NotificationDevicePeer::NUM_COLUMNS - NotificationDevicePeer::NUM_LAZY_LOAD_COLUMNS).

        } catch (Exception $e) {
            throw new PropelException("Error populating NotificationDevice object", $e);
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
            $con = Propel::getConnection(NotificationDevicePeer::DATABASE_NAME);
        }

        try {
            $con->begin();
            NotificationDevicePeer::doDelete($this, $con);
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
            $con = Propel::getConnection(NotificationDevicePeer::DATABASE_NAME);
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
                    $pk = NotificationDevicePeer::doInsert($this, $con);
                    $affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
                                         // should always be true here (even though technically
                                         // BasePeer::doInsert() can insert multiple rows).

                    $this->setNew(false);
                } else {
                    $affectedRows += NotificationDevicePeer::doUpdate($this, $con);
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


            if (($retval = NotificationDevicePeer::doValidate($this, $columns)) !== true) {
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
        $pos = NotificationDevicePeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                return $this->getDevUid();
                break;
            case 1:
                return $this->getUsrUid();
                break;
            case 2:
                return $this->getSysLang();
                break;
            case 3:
                return $this->getDevRegId();
                break;
            case 4:
                return $this->getDevType();
                break;
            case 5:
                return $this->getDevCreate();
                break;
            case 6:
                return $this->getDevUpdate();
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
        $keys = NotificationDevicePeer::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getDevUid(),
            $keys[1] => $this->getUsrUid(),
            $keys[2] => $this->getSysLang(),
            $keys[3] => $this->getDevRegId(),
            $keys[4] => $this->getDevType(),
            $keys[5] => $this->getDevCreate(),
            $keys[6] => $this->getDevUpdate(),
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
        $pos = NotificationDevicePeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                $this->setDevUid($value);
                break;
            case 1:
                $this->setUsrUid($value);
                break;
            case 2:
                $this->setSysLang($value);
                break;
            case 3:
                $this->setDevRegId($value);
                break;
            case 4:
                $this->setDevType($value);
                break;
            case 5:
                $this->setDevCreate($value);
                break;
            case 6:
                $this->setDevUpdate($value);
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
        $keys = NotificationDevicePeer::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) {
            $this->setDevUid($arr[$keys[0]]);
        }

        if (array_key_exists($keys[1], $arr)) {
            $this->setUsrUid($arr[$keys[1]]);
        }

        if (array_key_exists($keys[2], $arr)) {
            $this->setSysLang($arr[$keys[2]]);
        }

        if (array_key_exists($keys[3], $arr)) {
            $this->setDevRegId($arr[$keys[3]]);
        }

        if (array_key_exists($keys[4], $arr)) {
            $this->setDevType($arr[$keys[4]]);
        }

        if (array_key_exists($keys[5], $arr)) {
            $this->setDevCreate($arr[$keys[5]]);
        }

        if (array_key_exists($keys[6], $arr)) {
            $this->setDevUpdate($arr[$keys[6]]);
        }

    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return     Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(NotificationDevicePeer::DATABASE_NAME);

        if ($this->isColumnModified(NotificationDevicePeer::DEV_UID)) {
            $criteria->add(NotificationDevicePeer::DEV_UID, $this->dev_uid);
        }

        if ($this->isColumnModified(NotificationDevicePeer::USR_UID)) {
            $criteria->add(NotificationDevicePeer::USR_UID, $this->usr_uid);
        }

        if ($this->isColumnModified(NotificationDevicePeer::SYS_LANG)) {
            $criteria->add(NotificationDevicePeer::SYS_LANG, $this->sys_lang);
        }

        if ($this->isColumnModified(NotificationDevicePeer::DEV_REG_ID)) {
            $criteria->add(NotificationDevicePeer::DEV_REG_ID, $this->dev_reg_id);
        }

        if ($this->isColumnModified(NotificationDevicePeer::DEV_TYPE)) {
            $criteria->add(NotificationDevicePeer::DEV_TYPE, $this->dev_type);
        }

        if ($this->isColumnModified(NotificationDevicePeer::DEV_CREATE)) {
            $criteria->add(NotificationDevicePeer::DEV_CREATE, $this->dev_create);
        }

        if ($this->isColumnModified(NotificationDevicePeer::DEV_UPDATE)) {
            $criteria->add(NotificationDevicePeer::DEV_UPDATE, $this->dev_update);
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
        $criteria = new Criteria(NotificationDevicePeer::DATABASE_NAME);

        $criteria->add(NotificationDevicePeer::DEV_UID, $this->dev_uid);
        $criteria->add(NotificationDevicePeer::USR_UID, $this->usr_uid);

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

        $pks[0] = $this->getDevUid();

        $pks[1] = $this->getUsrUid();

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

        $this->setDevUid($keys[0]);

        $this->setUsrUid($keys[1]);

    }

    /**
     * Sets contents of passed object to values from current object.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param      object $copyObj An object of NotificationDevice (or compatible) type.
     * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @throws     PropelException
     */
    public function copyInto($copyObj, $deepCopy = false)
    {

        $copyObj->setSysLang($this->sys_lang);

        $copyObj->setDevRegId($this->dev_reg_id);

        $copyObj->setDevType($this->dev_type);

        $copyObj->setDevCreate($this->dev_create);

        $copyObj->setDevUpdate($this->dev_update);


        $copyObj->setNew(true);

        $copyObj->setDevUid(''); // this is a pkey column, so set to default value

        $copyObj->setUsrUid(''); // this is a pkey column, so set to default value

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
     * @return     NotificationDevice Clone of current object.
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
     * @return     NotificationDevicePeer
     */
    public function getPeer()
    {
        if (self::$peer === null) {
            self::$peer = new NotificationDevicePeer();
        }
        return self::$peer;
    }
}

