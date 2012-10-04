<?php

require_once 'propel/om/BaseObject.php';

require_once 'propel/om/Persistent.php';


include_once 'propel/util/Criteria.php';

include_once 'classes/model/DepartmentPeer.php';

/**
 * Base class that represents a row from the 'DEPARTMENT' table.
 *
 * 
 *
 * @package    workflow.classes.model.om
 */
abstract class BaseDepartment extends BaseObject implements Persistent
{

    /**
     * The Peer class.
     * Instance provides a convenient way of calling static methods on a class
     * that calling code may not be able to identify.
     * @var        DepartmentPeer
    */
    protected static $peer;

    /**
     * The value for the dep_uid field.
     * @var        string
     */
    protected $dep_uid = '';

    /**
     * The value for the dep_parent field.
     * @var        string
     */
    protected $dep_parent = '';

    /**
     * The value for the dep_manager field.
     * @var        string
     */
    protected $dep_manager = '';

    /**
     * The value for the dep_location field.
     * @var        int
     */
    protected $dep_location = 0;

    /**
     * The value for the dep_status field.
     * @var        string
     */
    protected $dep_status = 'ACTIVE';

    /**
     * The value for the dep_ref_code field.
     * @var        string
     */
    protected $dep_ref_code = '';

    /**
     * The value for the dep_ldap_dn field.
     * @var        string
     */
    protected $dep_ldap_dn = '';

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
     * Get the [dep_uid] column value.
     * 
     * @return     string
     */
    public function getDepUid()
    {

        return $this->dep_uid;
    }

    /**
     * Get the [dep_parent] column value.
     * 
     * @return     string
     */
    public function getDepParent()
    {

        return $this->dep_parent;
    }

    /**
     * Get the [dep_manager] column value.
     * 
     * @return     string
     */
    public function getDepManager()
    {

        return $this->dep_manager;
    }

    /**
     * Get the [dep_location] column value.
     * 
     * @return     int
     */
    public function getDepLocation()
    {

        return $this->dep_location;
    }

    /**
     * Get the [dep_status] column value.
     * 
     * @return     string
     */
    public function getDepStatus()
    {

        return $this->dep_status;
    }

    /**
     * Get the [dep_ref_code] column value.
     * 
     * @return     string
     */
    public function getDepRefCode()
    {

        return $this->dep_ref_code;
    }

    /**
     * Get the [dep_ldap_dn] column value.
     * 
     * @return     string
     */
    public function getDepLdapDn()
    {

        return $this->dep_ldap_dn;
    }

    /**
     * Set the value of [dep_uid] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setDepUid($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->dep_uid !== $v || $v === '') {
            $this->dep_uid = $v;
            $this->modifiedColumns[] = DepartmentPeer::DEP_UID;
        }

    } // setDepUid()

    /**
     * Set the value of [dep_parent] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setDepParent($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->dep_parent !== $v || $v === '') {
            $this->dep_parent = $v;
            $this->modifiedColumns[] = DepartmentPeer::DEP_PARENT;
        }

    } // setDepParent()

    /**
     * Set the value of [dep_manager] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setDepManager($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->dep_manager !== $v || $v === '') {
            $this->dep_manager = $v;
            $this->modifiedColumns[] = DepartmentPeer::DEP_MANAGER;
        }

    } // setDepManager()

    /**
     * Set the value of [dep_location] column.
     * 
     * @param      int $v new value
     * @return     void
     */
    public function setDepLocation($v)
    {

        // Since the native PHP type for this column is integer,
        // we will cast the input value to an int (if it is not).
        if ($v !== null && !is_int($v) && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->dep_location !== $v || $v === 0) {
            $this->dep_location = $v;
            $this->modifiedColumns[] = DepartmentPeer::DEP_LOCATION;
        }

    } // setDepLocation()

    /**
     * Set the value of [dep_status] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setDepStatus($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->dep_status !== $v || $v === 'ACTIVE') {
            $this->dep_status = $v;
            $this->modifiedColumns[] = DepartmentPeer::DEP_STATUS;
        }

    } // setDepStatus()

    /**
     * Set the value of [dep_ref_code] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setDepRefCode($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->dep_ref_code !== $v || $v === '') {
            $this->dep_ref_code = $v;
            $this->modifiedColumns[] = DepartmentPeer::DEP_REF_CODE;
        }

    } // setDepRefCode()

    /**
     * Set the value of [dep_ldap_dn] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setDepLdapDn($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->dep_ldap_dn !== $v || $v === '') {
            $this->dep_ldap_dn = $v;
            $this->modifiedColumns[] = DepartmentPeer::DEP_LDAP_DN;
        }

    } // setDepLdapDn()

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

            $this->dep_uid = $rs->getString($startcol + 0);

            $this->dep_parent = $rs->getString($startcol + 1);

            $this->dep_manager = $rs->getString($startcol + 2);

            $this->dep_location = $rs->getInt($startcol + 3);

            $this->dep_status = $rs->getString($startcol + 4);

            $this->dep_ref_code = $rs->getString($startcol + 5);

            $this->dep_ldap_dn = $rs->getString($startcol + 6);

            $this->resetModified();

            $this->setNew(false);

            // FIXME - using NUM_COLUMNS may be clearer.
            return $startcol + 7; // 7 = DepartmentPeer::NUM_COLUMNS - DepartmentPeer::NUM_LAZY_LOAD_COLUMNS).

        } catch (Exception $e) {
            throw new PropelException("Error populating Department object", $e);
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
            $con = Propel::getConnection(DepartmentPeer::DATABASE_NAME);
        }

        try {
            $con->begin();
            DepartmentPeer::doDelete($this, $con);
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
            $con = Propel::getConnection(DepartmentPeer::DATABASE_NAME);
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
                    $pk = DepartmentPeer::doInsert($this, $con);
                    $affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
                                         // should always be true here (even though technically
                                         // BasePeer::doInsert() can insert multiple rows).

                    $this->setNew(false);
                } else {
                    $affectedRows += DepartmentPeer::doUpdate($this, $con);
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


            if (($retval = DepartmentPeer::doValidate($this, $columns)) !== true) {
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
        $pos = DepartmentPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                return $this->getDepUid();
                break;
            case 1:
                return $this->getDepParent();
                break;
            case 2:
                return $this->getDepManager();
                break;
            case 3:
                return $this->getDepLocation();
                break;
            case 4:
                return $this->getDepStatus();
                break;
            case 5:
                return $this->getDepRefCode();
                break;
            case 6:
                return $this->getDepLdapDn();
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
        $keys = DepartmentPeer::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getDepUid(),
            $keys[1] => $this->getDepParent(),
            $keys[2] => $this->getDepManager(),
            $keys[3] => $this->getDepLocation(),
            $keys[4] => $this->getDepStatus(),
            $keys[5] => $this->getDepRefCode(),
            $keys[6] => $this->getDepLdapDn(),
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
        $pos = DepartmentPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                $this->setDepUid($value);
                break;
            case 1:
                $this->setDepParent($value);
                break;
            case 2:
                $this->setDepManager($value);
                break;
            case 3:
                $this->setDepLocation($value);
                break;
            case 4:
                $this->setDepStatus($value);
                break;
            case 5:
                $this->setDepRefCode($value);
                break;
            case 6:
                $this->setDepLdapDn($value);
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
        $keys = DepartmentPeer::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) {
            $this->setDepUid($arr[$keys[0]]);
        }

        if (array_key_exists($keys[1], $arr)) {
            $this->setDepParent($arr[$keys[1]]);
        }

        if (array_key_exists($keys[2], $arr)) {
            $this->setDepManager($arr[$keys[2]]);
        }

        if (array_key_exists($keys[3], $arr)) {
            $this->setDepLocation($arr[$keys[3]]);
        }

        if (array_key_exists($keys[4], $arr)) {
            $this->setDepStatus($arr[$keys[4]]);
        }

        if (array_key_exists($keys[5], $arr)) {
            $this->setDepRefCode($arr[$keys[5]]);
        }

        if (array_key_exists($keys[6], $arr)) {
            $this->setDepLdapDn($arr[$keys[6]]);
        }

    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return     Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(DepartmentPeer::DATABASE_NAME);

        if ($this->isColumnModified(DepartmentPeer::DEP_UID)) {
            $criteria->add(DepartmentPeer::DEP_UID, $this->dep_uid);
        }

        if ($this->isColumnModified(DepartmentPeer::DEP_PARENT)) {
            $criteria->add(DepartmentPeer::DEP_PARENT, $this->dep_parent);
        }

        if ($this->isColumnModified(DepartmentPeer::DEP_MANAGER)) {
            $criteria->add(DepartmentPeer::DEP_MANAGER, $this->dep_manager);
        }

        if ($this->isColumnModified(DepartmentPeer::DEP_LOCATION)) {
            $criteria->add(DepartmentPeer::DEP_LOCATION, $this->dep_location);
        }

        if ($this->isColumnModified(DepartmentPeer::DEP_STATUS)) {
            $criteria->add(DepartmentPeer::DEP_STATUS, $this->dep_status);
        }

        if ($this->isColumnModified(DepartmentPeer::DEP_REF_CODE)) {
            $criteria->add(DepartmentPeer::DEP_REF_CODE, $this->dep_ref_code);
        }

        if ($this->isColumnModified(DepartmentPeer::DEP_LDAP_DN)) {
            $criteria->add(DepartmentPeer::DEP_LDAP_DN, $this->dep_ldap_dn);
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
        $criteria = new Criteria(DepartmentPeer::DATABASE_NAME);

        $criteria->add(DepartmentPeer::DEP_UID, $this->dep_uid);

        return $criteria;
    }

    /**
     * Returns the primary key for this object (row).
     * @return     string
     */
    public function getPrimaryKey()
    {
        return $this->getDepUid();
    }

    /**
     * Generic method to set the primary key (dep_uid column).
     *
     * @param      string $key Primary key.
     * @return     void
     */
    public function setPrimaryKey($key)
    {
        $this->setDepUid($key);
    }

    /**
     * Sets contents of passed object to values from current object.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param      object $copyObj An object of Department (or compatible) type.
     * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @throws     PropelException
     */
    public function copyInto($copyObj, $deepCopy = false)
    {

        $copyObj->setDepParent($this->dep_parent);

        $copyObj->setDepManager($this->dep_manager);

        $copyObj->setDepLocation($this->dep_location);

        $copyObj->setDepStatus($this->dep_status);

        $copyObj->setDepRefCode($this->dep_ref_code);

        $copyObj->setDepLdapDn($this->dep_ldap_dn);


        $copyObj->setNew(true);

        $copyObj->setDepUid(''); // this is a pkey column, so set to default value

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
     * @return     Department Clone of current object.
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
     * @return     DepartmentPeer
     */
    public function getPeer()
    {
        if (self::$peer === null) {
            self::$peer = new DepartmentPeer();
        }
        return self::$peer;
    }
}

