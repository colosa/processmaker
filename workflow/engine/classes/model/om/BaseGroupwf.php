<?php

require_once 'propel/om/BaseObject.php';

require_once 'propel/om/Persistent.php';


include_once 'propel/util/Criteria.php';

include_once 'classes/model/GroupwfPeer.php';

/**
 * Base class that represents a row from the 'GROUPWF' table.
 *
 * 
 *
 * @package    workflow.classes.model.om
 */
abstract class BaseGroupwf extends BaseObject implements Persistent
{

    /**
     * The Peer class.
     * Instance provides a convenient way of calling static methods on a class
     * that calling code may not be able to identify.
     * @var        GroupwfPeer
    */
    protected static $peer;

    /**
     * The value for the grp_uid field.
     * @var        string
     */
    protected $grp_uid = '';

    /**
     * The value for the grp_status field.
     * @var        string
     */
    protected $grp_status = 'ACTIVE';

    /**
     * The value for the grp_ldap_dn field.
     * @var        string
     */
    protected $grp_ldap_dn = '';

    /**
     * The value for the grp_ux field.
     * @var        string
     */
    protected $grp_ux = 'NORMAL';

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
     * Get the [grp_uid] column value.
     * 
     * @return     string
     */
    public function getGrpUid()
    {

        return $this->grp_uid;
    }

    /**
     * Get the [grp_status] column value.
     * 
     * @return     string
     */
    public function getGrpStatus()
    {

        return $this->grp_status;
    }

    /**
     * Get the [grp_ldap_dn] column value.
     * 
     * @return     string
     */
    public function getGrpLdapDn()
    {

        return $this->grp_ldap_dn;
    }

    /**
     * Get the [grp_ux] column value.
     * 
     * @return     string
     */
    public function getGrpUx()
    {

        return $this->grp_ux;
    }

    /**
     * Set the value of [grp_uid] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setGrpUid($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->grp_uid !== $v || $v === '') {
            $this->grp_uid = $v;
            $this->modifiedColumns[] = GroupwfPeer::GRP_UID;
        }

    } // setGrpUid()

    /**
     * Set the value of [grp_status] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setGrpStatus($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->grp_status !== $v || $v === 'ACTIVE') {
            $this->grp_status = $v;
            $this->modifiedColumns[] = GroupwfPeer::GRP_STATUS;
        }

    } // setGrpStatus()

    /**
     * Set the value of [grp_ldap_dn] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setGrpLdapDn($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->grp_ldap_dn !== $v || $v === '') {
            $this->grp_ldap_dn = $v;
            $this->modifiedColumns[] = GroupwfPeer::GRP_LDAP_DN;
        }

    } // setGrpLdapDn()

    /**
     * Set the value of [grp_ux] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setGrpUx($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->grp_ux !== $v || $v === 'NORMAL') {
            $this->grp_ux = $v;
            $this->modifiedColumns[] = GroupwfPeer::GRP_UX;
        }

    } // setGrpUx()

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

            $this->grp_uid = $rs->getString($startcol + 0);

            $this->grp_status = $rs->getString($startcol + 1);

            $this->grp_ldap_dn = $rs->getString($startcol + 2);

            $this->grp_ux = $rs->getString($startcol + 3);

            $this->resetModified();

            $this->setNew(false);

            // FIXME - using NUM_COLUMNS may be clearer.
            return $startcol + 4; // 4 = GroupwfPeer::NUM_COLUMNS - GroupwfPeer::NUM_LAZY_LOAD_COLUMNS).

        } catch (Exception $e) {
            throw new PropelException("Error populating Groupwf object", $e);
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
            $con = Propel::getConnection(GroupwfPeer::DATABASE_NAME);
        }

        try {
            $con->begin();
            GroupwfPeer::doDelete($this, $con);
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
            $con = Propel::getConnection(GroupwfPeer::DATABASE_NAME);
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
                    $pk = GroupwfPeer::doInsert($this, $con);
                    $affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
                                         // should always be true here (even though technically
                                         // BasePeer::doInsert() can insert multiple rows).

                    $this->setNew(false);
                } else {
                    $affectedRows += GroupwfPeer::doUpdate($this, $con);
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


            if (($retval = GroupwfPeer::doValidate($this, $columns)) !== true) {
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
        $pos = GroupwfPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                return $this->getGrpUid();
                break;
            case 1:
                return $this->getGrpStatus();
                break;
            case 2:
                return $this->getGrpLdapDn();
                break;
            case 3:
                return $this->getGrpUx();
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
        $keys = GroupwfPeer::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getGrpUid(),
            $keys[1] => $this->getGrpStatus(),
            $keys[2] => $this->getGrpLdapDn(),
            $keys[3] => $this->getGrpUx(),
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
        $pos = GroupwfPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                $this->setGrpUid($value);
                break;
            case 1:
                $this->setGrpStatus($value);
                break;
            case 2:
                $this->setGrpLdapDn($value);
                break;
            case 3:
                $this->setGrpUx($value);
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
        $keys = GroupwfPeer::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) {
            $this->setGrpUid($arr[$keys[0]]);
        }

        if (array_key_exists($keys[1], $arr)) {
            $this->setGrpStatus($arr[$keys[1]]);
        }

        if (array_key_exists($keys[2], $arr)) {
            $this->setGrpLdapDn($arr[$keys[2]]);
        }

        if (array_key_exists($keys[3], $arr)) {
            $this->setGrpUx($arr[$keys[3]]);
        }

    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return     Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(GroupwfPeer::DATABASE_NAME);

        if ($this->isColumnModified(GroupwfPeer::GRP_UID)) {
            $criteria->add(GroupwfPeer::GRP_UID, $this->grp_uid);
        }

        if ($this->isColumnModified(GroupwfPeer::GRP_STATUS)) {
            $criteria->add(GroupwfPeer::GRP_STATUS, $this->grp_status);
        }

        if ($this->isColumnModified(GroupwfPeer::GRP_LDAP_DN)) {
            $criteria->add(GroupwfPeer::GRP_LDAP_DN, $this->grp_ldap_dn);
        }

        if ($this->isColumnModified(GroupwfPeer::GRP_UX)) {
            $criteria->add(GroupwfPeer::GRP_UX, $this->grp_ux);
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
        $criteria = new Criteria(GroupwfPeer::DATABASE_NAME);

        $criteria->add(GroupwfPeer::GRP_UID, $this->grp_uid);

        return $criteria;
    }

    /**
     * Returns the primary key for this object (row).
     * @return     string
     */
    public function getPrimaryKey()
    {
        return $this->getGrpUid();
    }

    /**
     * Generic method to set the primary key (grp_uid column).
     *
     * @param      string $key Primary key.
     * @return     void
     */
    public function setPrimaryKey($key)
    {
        $this->setGrpUid($key);
    }

    /**
     * Sets contents of passed object to values from current object.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param      object $copyObj An object of Groupwf (or compatible) type.
     * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @throws     PropelException
     */
    public function copyInto($copyObj, $deepCopy = false)
    {

        $copyObj->setGrpStatus($this->grp_status);

        $copyObj->setGrpLdapDn($this->grp_ldap_dn);

        $copyObj->setGrpUx($this->grp_ux);


        $copyObj->setNew(true);

        $copyObj->setGrpUid(''); // this is a pkey column, so set to default value

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
     * @return     Groupwf Clone of current object.
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
     * @return     GroupwfPeer
     */
    public function getPeer()
    {
        if (self::$peer === null) {
            self::$peer = new GroupwfPeer();
        }
        return self::$peer;
    }
}

