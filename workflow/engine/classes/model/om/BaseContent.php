<?php

require_once 'propel/om/BaseObject.php';

require_once 'propel/om/Persistent.php';


include_once 'propel/util/Criteria.php';

include_once 'classes/model/ContentPeer.php';

/**
 * Base class that represents a row from the 'CONTENT' table.
 *
 * 
 *
 * @package    workflow.classes.model.om
 */
abstract class BaseContent extends BaseObject implements Persistent
{

    /**
     * The Peer class.
     * Instance provides a convenient way of calling static methods on a class
     * that calling code may not be able to identify.
     * @var        ContentPeer
    */
    protected static $peer;

    /**
     * The value for the con_category field.
     * @var        string
     */
    protected $con_category = '';

    /**
     * The value for the con_parent field.
     * @var        string
     */
    protected $con_parent = '';

    /**
     * The value for the con_id field.
     * @var        string
     */
    protected $con_id = '';

    /**
     * The value for the con_lang field.
     * @var        string
     */
    protected $con_lang = '';

    /**
     * The value for the con_value field.
     * @var        string
     */
    protected $con_value;

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
     * Get the [con_category] column value.
     * 
     * @return     string
     */
    public function getConCategory()
    {

        return $this->con_category;
    }

    /**
     * Get the [con_parent] column value.
     * 
     * @return     string
     */
    public function getConParent()
    {

        return $this->con_parent;
    }

    /**
     * Get the [con_id] column value.
     * 
     * @return     string
     */
    public function getConId()
    {

        return $this->con_id;
    }

    /**
     * Get the [con_lang] column value.
     * 
     * @return     string
     */
    public function getConLang()
    {

        return $this->con_lang;
    }

    /**
     * Get the [con_value] column value.
     * 
     * @return     string
     */
    public function getConValue()
    {

        return $this->con_value;
    }

    /**
     * Set the value of [con_category] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setConCategory($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->con_category !== $v || $v === '') {
            $this->con_category = $v;
            $this->modifiedColumns[] = ContentPeer::CON_CATEGORY;
        }

    } // setConCategory()

    /**
     * Set the value of [con_parent] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setConParent($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->con_parent !== $v || $v === '') {
            $this->con_parent = $v;
            $this->modifiedColumns[] = ContentPeer::CON_PARENT;
        }

    } // setConParent()

    /**
     * Set the value of [con_id] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setConId($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->con_id !== $v || $v === '') {
            $this->con_id = $v;
            $this->modifiedColumns[] = ContentPeer::CON_ID;
        }

    } // setConId()

    /**
     * Set the value of [con_lang] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setConLang($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->con_lang !== $v || $v === '') {
            $this->con_lang = $v;
            $this->modifiedColumns[] = ContentPeer::CON_LANG;
        }

    } // setConLang()

    /**
     * Set the value of [con_value] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setConValue($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->con_value !== $v) {
            $this->con_value = $v;
            $this->modifiedColumns[] = ContentPeer::CON_VALUE;
        }

    } // setConValue()

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

            $this->con_category = $rs->getString($startcol + 0);

            $this->con_parent = $rs->getString($startcol + 1);

            $this->con_id = $rs->getString($startcol + 2);

            $this->con_lang = $rs->getString($startcol + 3);

            $this->con_value = $rs->getString($startcol + 4);

            $this->resetModified();

            $this->setNew(false);

            // FIXME - using NUM_COLUMNS may be clearer.
            return $startcol + 5; // 5 = ContentPeer::NUM_COLUMNS - ContentPeer::NUM_LAZY_LOAD_COLUMNS).

        } catch (Exception $e) {
            throw new PropelException("Error populating Content object", $e);
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
            $con = Propel::getConnection(ContentPeer::DATABASE_NAME);
        }

        try {
            $con->begin();
            ContentPeer::doDelete($this, $con);
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
            $con = Propel::getConnection(ContentPeer::DATABASE_NAME);
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
                    $pk = ContentPeer::doInsert($this, $con);
                    $affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
                                         // should always be true here (even though technically
                                         // BasePeer::doInsert() can insert multiple rows).

                    $this->setNew(false);
                } else {
                    $affectedRows += ContentPeer::doUpdate($this, $con);
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


            if (($retval = ContentPeer::doValidate($this, $columns)) !== true) {
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
        $pos = ContentPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                return $this->getConCategory();
                break;
            case 1:
                return $this->getConParent();
                break;
            case 2:
                return $this->getConId();
                break;
            case 3:
                return $this->getConLang();
                break;
            case 4:
                return $this->getConValue();
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
        $keys = ContentPeer::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getConCategory(),
            $keys[1] => $this->getConParent(),
            $keys[2] => $this->getConId(),
            $keys[3] => $this->getConLang(),
            $keys[4] => $this->getConValue(),
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
        $pos = ContentPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                $this->setConCategory($value);
                break;
            case 1:
                $this->setConParent($value);
                break;
            case 2:
                $this->setConId($value);
                break;
            case 3:
                $this->setConLang($value);
                break;
            case 4:
                $this->setConValue($value);
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
        $keys = ContentPeer::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) {
            $this->setConCategory($arr[$keys[0]]);
        }

        if (array_key_exists($keys[1], $arr)) {
            $this->setConParent($arr[$keys[1]]);
        }

        if (array_key_exists($keys[2], $arr)) {
            $this->setConId($arr[$keys[2]]);
        }

        if (array_key_exists($keys[3], $arr)) {
            $this->setConLang($arr[$keys[3]]);
        }

        if (array_key_exists($keys[4], $arr)) {
            $this->setConValue($arr[$keys[4]]);
        }

    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return     Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(ContentPeer::DATABASE_NAME);

        if ($this->isColumnModified(ContentPeer::CON_CATEGORY)) {
            $criteria->add(ContentPeer::CON_CATEGORY, $this->con_category);
        }

        if ($this->isColumnModified(ContentPeer::CON_PARENT)) {
            $criteria->add(ContentPeer::CON_PARENT, $this->con_parent);
        }

        if ($this->isColumnModified(ContentPeer::CON_ID)) {
            $criteria->add(ContentPeer::CON_ID, $this->con_id);
        }

        if ($this->isColumnModified(ContentPeer::CON_LANG)) {
            $criteria->add(ContentPeer::CON_LANG, $this->con_lang);
        }

        if ($this->isColumnModified(ContentPeer::CON_VALUE)) {
            $criteria->add(ContentPeer::CON_VALUE, $this->con_value);
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
        $criteria = new Criteria(ContentPeer::DATABASE_NAME);

        $criteria->add(ContentPeer::CON_CATEGORY, $this->con_category);
        $criteria->add(ContentPeer::CON_PARENT, $this->con_parent);
        $criteria->add(ContentPeer::CON_ID, $this->con_id);
        $criteria->add(ContentPeer::CON_LANG, $this->con_lang);

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

        $pks[0] = $this->getConCategory();

        $pks[1] = $this->getConParent();

        $pks[2] = $this->getConId();

        $pks[3] = $this->getConLang();

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

        $this->setConCategory($keys[0]);

        $this->setConParent($keys[1]);

        $this->setConId($keys[2]);

        $this->setConLang($keys[3]);

    }

    /**
     * Sets contents of passed object to values from current object.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param      object $copyObj An object of Content (or compatible) type.
     * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @throws     PropelException
     */
    public function copyInto($copyObj, $deepCopy = false)
    {

        $copyObj->setConValue($this->con_value);


        $copyObj->setNew(true);

        $copyObj->setConCategory(''); // this is a pkey column, so set to default value

        $copyObj->setConParent(''); // this is a pkey column, so set to default value

        $copyObj->setConId(''); // this is a pkey column, so set to default value

        $copyObj->setConLang(''); // this is a pkey column, so set to default value

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
     * @return     Content Clone of current object.
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
     * @return     ContentPeer
     */
    public function getPeer()
    {
        if (self::$peer === null) {
            self::$peer = new ContentPeer();
        }
        return self::$peer;
    }
}

