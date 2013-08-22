<?php

require_once 'propel/om/BaseObject.php';

require_once 'propel/om/Persistent.php';


include_once 'propel/util/Criteria.php';

include_once 'classes/model/ConfigurationPeer.php';

/**
 * Base class that represents a row from the 'CONFIGURATION' table.
 *
 * 
 *
 * @package    workflow.classes.model.om
 */
abstract class BaseConfiguration extends BaseObject implements Persistent
{

    /**
     * The Peer class.
     * Instance provides a convenient way of calling static methods on a class
     * that calling code may not be able to identify.
     * @var        ConfigurationPeer
    */
    protected static $peer;

    /**
     * The value for the cfg_uid field.
     * @var        string
     */
    protected $cfg_uid = '';

    /**
     * The value for the obj_uid field.
     * @var        string
     */
    protected $obj_uid = '';

    /**
     * The value for the cfg_value field.
     * @var        string
     */
    protected $cfg_value;

    /**
     * The value for the pro_uid field.
     * @var        string
     */
    protected $pro_uid = '';

    /**
     * The value for the usr_uid field.
     * @var        string
     */
    protected $usr_uid = '';

    /**
     * The value for the app_uid field.
     * @var        string
     */
    protected $app_uid = '';

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
     * Get the [cfg_uid] column value.
     * 
     * @return     string
     */
    public function getCfgUid()
    {

        return $this->cfg_uid;
    }

    /**
     * Get the [obj_uid] column value.
     * 
     * @return     string
     */
    public function getObjUid()
    {

        return $this->obj_uid;
    }

    /**
     * Get the [cfg_value] column value.
     * 
     * @return     string
     */
    public function getCfgValue()
    {

        return $this->cfg_value;
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
     * Get the [usr_uid] column value.
     * 
     * @return     string
     */
    public function getUsrUid()
    {

        return $this->usr_uid;
    }

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
     * Set the value of [cfg_uid] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setCfgUid($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->cfg_uid !== $v || $v === '') {
            $this->cfg_uid = $v;
            $this->modifiedColumns[] = ConfigurationPeer::CFG_UID;
        }

    } // setCfgUid()

    /**
     * Set the value of [obj_uid] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setObjUid($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->obj_uid !== $v || $v === '') {
            $this->obj_uid = $v;
            $this->modifiedColumns[] = ConfigurationPeer::OBJ_UID;
        }

    } // setObjUid()

    /**
     * Set the value of [cfg_value] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setCfgValue($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->cfg_value !== $v) {
            $this->cfg_value = $v;
            $this->modifiedColumns[] = ConfigurationPeer::CFG_VALUE;
        }

    } // setCfgValue()

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
            $this->modifiedColumns[] = ConfigurationPeer::PRO_UID;
        }

    } // setProUid()

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
            $this->modifiedColumns[] = ConfigurationPeer::USR_UID;
        }

    } // setUsrUid()

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
            $this->modifiedColumns[] = ConfigurationPeer::APP_UID;
        }

    } // setAppUid()

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

            $this->cfg_uid = $rs->getString($startcol + 0);

            $this->obj_uid = $rs->getString($startcol + 1);

            $this->cfg_value = $rs->getString($startcol + 2);

            $this->pro_uid = $rs->getString($startcol + 3);

            $this->usr_uid = $rs->getString($startcol + 4);

            $this->app_uid = $rs->getString($startcol + 5);

            $this->resetModified();

            $this->setNew(false);

            // FIXME - using NUM_COLUMNS may be clearer.
            return $startcol + 6; // 6 = ConfigurationPeer::NUM_COLUMNS - ConfigurationPeer::NUM_LAZY_LOAD_COLUMNS).

        } catch (Exception $e) {
            throw new PropelException("Error populating Configuration object", $e);
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
            $con = Propel::getConnection(ConfigurationPeer::DATABASE_NAME);
        }

        try {
            $con->begin();
            ConfigurationPeer::doDelete($this, $con);
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
            $con = Propel::getConnection(ConfigurationPeer::DATABASE_NAME);
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
                    $pk = ConfigurationPeer::doInsert($this, $con);
                    $affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
                                         // should always be true here (even though technically
                                         // BasePeer::doInsert() can insert multiple rows).

                    $this->setNew(false);
                } else {
                    $affectedRows += ConfigurationPeer::doUpdate($this, $con);
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


            if (($retval = ConfigurationPeer::doValidate($this, $columns)) !== true) {
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
        $pos = ConfigurationPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                return $this->getCfgUid();
                break;
            case 1:
                return $this->getObjUid();
                break;
            case 2:
                return $this->getCfgValue();
                break;
            case 3:
                return $this->getProUid();
                break;
            case 4:
                return $this->getUsrUid();
                break;
            case 5:
                return $this->getAppUid();
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
        $keys = ConfigurationPeer::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getCfgUid(),
            $keys[1] => $this->getObjUid(),
            $keys[2] => $this->getCfgValue(),
            $keys[3] => $this->getProUid(),
            $keys[4] => $this->getUsrUid(),
            $keys[5] => $this->getAppUid(),
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
        $pos = ConfigurationPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                $this->setCfgUid($value);
                break;
            case 1:
                $this->setObjUid($value);
                break;
            case 2:
                $this->setCfgValue($value);
                break;
            case 3:
                $this->setProUid($value);
                break;
            case 4:
                $this->setUsrUid($value);
                break;
            case 5:
                $this->setAppUid($value);
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
        $keys = ConfigurationPeer::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) {
            $this->setCfgUid($arr[$keys[0]]);
        }

        if (array_key_exists($keys[1], $arr)) {
            $this->setObjUid($arr[$keys[1]]);
        }

        if (array_key_exists($keys[2], $arr)) {
            $this->setCfgValue($arr[$keys[2]]);
        }

        if (array_key_exists($keys[3], $arr)) {
            $this->setProUid($arr[$keys[3]]);
        }

        if (array_key_exists($keys[4], $arr)) {
            $this->setUsrUid($arr[$keys[4]]);
        }

        if (array_key_exists($keys[5], $arr)) {
            $this->setAppUid($arr[$keys[5]]);
        }

    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return     Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(ConfigurationPeer::DATABASE_NAME);

        if ($this->isColumnModified(ConfigurationPeer::CFG_UID)) {
            $criteria->add(ConfigurationPeer::CFG_UID, $this->cfg_uid);
        }

        if ($this->isColumnModified(ConfigurationPeer::OBJ_UID)) {
            $criteria->add(ConfigurationPeer::OBJ_UID, $this->obj_uid);
        }

        if ($this->isColumnModified(ConfigurationPeer::CFG_VALUE)) {
            $criteria->add(ConfigurationPeer::CFG_VALUE, $this->cfg_value);
        }

        if ($this->isColumnModified(ConfigurationPeer::PRO_UID)) {
            $criteria->add(ConfigurationPeer::PRO_UID, $this->pro_uid);
        }

        if ($this->isColumnModified(ConfigurationPeer::USR_UID)) {
            $criteria->add(ConfigurationPeer::USR_UID, $this->usr_uid);
        }

        if ($this->isColumnModified(ConfigurationPeer::APP_UID)) {
            $criteria->add(ConfigurationPeer::APP_UID, $this->app_uid);
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
        $criteria = new Criteria(ConfigurationPeer::DATABASE_NAME);

        $criteria->add(ConfigurationPeer::CFG_UID, $this->cfg_uid);
        $criteria->add(ConfigurationPeer::OBJ_UID, $this->obj_uid);
        $criteria->add(ConfigurationPeer::PRO_UID, $this->pro_uid);
        $criteria->add(ConfigurationPeer::USR_UID, $this->usr_uid);
        $criteria->add(ConfigurationPeer::APP_UID, $this->app_uid);

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

        $pks[0] = $this->getCfgUid();

        $pks[1] = $this->getObjUid();

        $pks[2] = $this->getProUid();

        $pks[3] = $this->getUsrUid();

        $pks[4] = $this->getAppUid();

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

        $this->setCfgUid($keys[0]);

        $this->setObjUid($keys[1]);

        $this->setProUid($keys[2]);

        $this->setUsrUid($keys[3]);

        $this->setAppUid($keys[4]);

    }

    /**
     * Sets contents of passed object to values from current object.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param      object $copyObj An object of Configuration (or compatible) type.
     * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @throws     PropelException
     */
    public function copyInto($copyObj, $deepCopy = false)
    {

        $copyObj->setCfgValue($this->cfg_value);


        $copyObj->setNew(true);

        $copyObj->setCfgUid(''); // this is a pkey column, so set to default value

        $copyObj->setObjUid(''); // this is a pkey column, so set to default value

        $copyObj->setProUid(''); // this is a pkey column, so set to default value

        $copyObj->setUsrUid(''); // this is a pkey column, so set to default value

        $copyObj->setAppUid(''); // this is a pkey column, so set to default value

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
     * @return     Configuration Clone of current object.
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
     * @return     ConfigurationPeer
     */
    public function getPeer()
    {
        if (self::$peer === null) {
            self::$peer = new ConfigurationPeer();
        }
        return self::$peer;
    }

    /**
     * Set properties related to Configuration table
     * 
     * @return void
     */
    public function setProperties()
    {
        $this->cfg_uid = '';
        $this->obj_uid = '';
        $this->cfg_value = '';
        $this->pro_uid = '';
        $this->usr_uid = '';
        $this->app_uid = '';
    }
}

