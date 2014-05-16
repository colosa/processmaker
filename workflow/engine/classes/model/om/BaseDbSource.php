<?php

require_once 'propel/om/BaseObject.php';

require_once 'propel/om/Persistent.php';


include_once 'propel/util/Criteria.php';

include_once 'classes/model/DbSourcePeer.php';

/**
 * Base class that represents a row from the 'DB_SOURCE' table.
 *
 * 
 *
 * @package    workflow.classes.model.om
 */
abstract class BaseDbSource extends BaseObject implements Persistent
{

    /**
     * The Peer class.
     * Instance provides a convenient way of calling static methods on a class
     * that calling code may not be able to identify.
     * @var        DbSourcePeer
    */
    protected static $peer;

    /**
     * The value for the dbs_uid field.
     * @var        string
     */
    protected $dbs_uid = '';

    /**
     * The value for the pro_uid field.
     * @var        string
     */
    protected $pro_uid = '0';

    /**
     * The value for the dbs_type field.
     * @var        string
     */
    protected $dbs_type = '0';

    /**
     * The value for the dbs_server field.
     * @var        string
     */
    protected $dbs_server = '0';

    /**
     * The value for the dbs_database_name field.
     * @var        string
     */
    protected $dbs_database_name = '0';

    /**
     * The value for the dbs_username field.
     * @var        string
     */
    protected $dbs_username = '0';

    /**
     * The value for the dbs_password field.
     * @var        string
     */
    protected $dbs_password = '';

    /**
     * The value for the dbs_port field.
     * @var        int
     */
    protected $dbs_port = 0;

    /**
     * The value for the dbs_encode field.
     * @var        string
     */
    protected $dbs_encode = '';

    /**
     * The value for the dbs_connection_type field.
     * @var        string
     */
    protected $dbs_connection_type = 'NORMAL';

    /**
     * The value for the dbs_tns field.
     * @var        string
     */
    protected $dbs_tns = '';

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
     * Get the [dbs_uid] column value.
     * 
     * @return     string
     */
    public function getDbsUid()
    {

        return $this->dbs_uid;
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
     * Get the [dbs_type] column value.
     * 
     * @return     string
     */
    public function getDbsType()
    {

        return $this->dbs_type;
    }

    /**
     * Get the [dbs_server] column value.
     * 
     * @return     string
     */
    public function getDbsServer()
    {

        return $this->dbs_server;
    }

    /**
     * Get the [dbs_database_name] column value.
     * 
     * @return     string
     */
    public function getDbsDatabaseName()
    {

        return $this->dbs_database_name;
    }

    /**
     * Get the [dbs_username] column value.
     * 
     * @return     string
     */
    public function getDbsUsername()
    {

        return $this->dbs_username;
    }

    /**
     * Get the [dbs_password] column value.
     * 
     * @return     string
     */
    public function getDbsPassword()
    {

        return $this->dbs_password;
    }

    /**
     * Get the [dbs_port] column value.
     * 
     * @return     int
     */
    public function getDbsPort()
    {

        return $this->dbs_port;
    }

    /**
     * Get the [dbs_encode] column value.
     * 
     * @return     string
     */
    public function getDbsEncode()
    {

        return $this->dbs_encode;
    }

    /**
     * Get the [dbs_connection_type] column value.
     * 
     * @return     string
     */
    public function getDbsConnectionType()
    {

        return $this->dbs_connection_type;
    }

    /**
     * Get the [dbs_tns] column value.
     * 
     * @return     string
     */
    public function getDbsTns()
    {

        return $this->dbs_tns;
    }

    /**
     * Set the value of [dbs_uid] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setDbsUid($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->dbs_uid !== $v || $v === '') {
            $this->dbs_uid = $v;
            $this->modifiedColumns[] = DbSourcePeer::DBS_UID;
        }

    } // setDbsUid()

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

        if ($this->pro_uid !== $v || $v === '0') {
            $this->pro_uid = $v;
            $this->modifiedColumns[] = DbSourcePeer::PRO_UID;
        }

    } // setProUid()

    /**
     * Set the value of [dbs_type] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setDbsType($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->dbs_type !== $v || $v === '0') {
            $this->dbs_type = $v;
            $this->modifiedColumns[] = DbSourcePeer::DBS_TYPE;
        }

    } // setDbsType()

    /**
     * Set the value of [dbs_server] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setDbsServer($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->dbs_server !== $v || $v === '0') {
            $this->dbs_server = $v;
            $this->modifiedColumns[] = DbSourcePeer::DBS_SERVER;
        }

    } // setDbsServer()

    /**
     * Set the value of [dbs_database_name] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setDbsDatabaseName($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->dbs_database_name !== $v || $v === '0') {
            $this->dbs_database_name = $v;
            $this->modifiedColumns[] = DbSourcePeer::DBS_DATABASE_NAME;
        }

    } // setDbsDatabaseName()

    /**
     * Set the value of [dbs_username] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setDbsUsername($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->dbs_username !== $v || $v === '0') {
            $this->dbs_username = $v;
            $this->modifiedColumns[] = DbSourcePeer::DBS_USERNAME;
        }

    } // setDbsUsername()

    /**
     * Set the value of [dbs_password] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setDbsPassword($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->dbs_password !== $v || $v === '') {
            $this->dbs_password = $v;
            $this->modifiedColumns[] = DbSourcePeer::DBS_PASSWORD;
        }

    } // setDbsPassword()

    /**
     * Set the value of [dbs_port] column.
     * 
     * @param      int $v new value
     * @return     void
     */
    public function setDbsPort($v)
    {

        // Since the native PHP type for this column is integer,
        // we will cast the input value to an int (if it is not).
        if ($v !== null && !is_int($v) && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->dbs_port !== $v || $v === 0) {
            $this->dbs_port = $v;
            $this->modifiedColumns[] = DbSourcePeer::DBS_PORT;
        }

    } // setDbsPort()

    /**
     * Set the value of [dbs_encode] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setDbsEncode($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->dbs_encode !== $v || $v === '') {
            $this->dbs_encode = $v;
            $this->modifiedColumns[] = DbSourcePeer::DBS_ENCODE;
        }

    } // setDbsEncode()

    /**
     * Set the value of [dbs_connection_type] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setDbsConnectionType($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->dbs_connection_type !== $v || $v === 'NORMAL') {
            $this->dbs_connection_type = $v;
            $this->modifiedColumns[] = DbSourcePeer::DBS_CONNECTION_TYPE;
        }

    } // setDbsConnectionType()

    /**
     * Set the value of [dbs_tns] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setDbsTns($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->dbs_tns !== $v || $v === '') {
            $this->dbs_tns = $v;
            $this->modifiedColumns[] = DbSourcePeer::DBS_TNS;
        }

    } // setDbsTns()

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

            $this->dbs_uid = $rs->getString($startcol + 0);

            $this->pro_uid = $rs->getString($startcol + 1);

            $this->dbs_type = $rs->getString($startcol + 2);

            $this->dbs_server = $rs->getString($startcol + 3);

            $this->dbs_database_name = $rs->getString($startcol + 4);

            $this->dbs_username = $rs->getString($startcol + 5);

            $this->dbs_password = $rs->getString($startcol + 6);

            $this->dbs_port = $rs->getInt($startcol + 7);

            $this->dbs_encode = $rs->getString($startcol + 8);

            $this->dbs_connection_type = $rs->getString($startcol + 9);

            $this->dbs_tns = $rs->getString($startcol + 10);

            $this->resetModified();

            $this->setNew(false);

            // FIXME - using NUM_COLUMNS may be clearer.
            return $startcol + 11; // 11 = DbSourcePeer::NUM_COLUMNS - DbSourcePeer::NUM_LAZY_LOAD_COLUMNS).

        } catch (Exception $e) {
            throw new PropelException("Error populating DbSource object", $e);
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
            $con = Propel::getConnection(DbSourcePeer::DATABASE_NAME);
        }

        try {
            $con->begin();
            DbSourcePeer::doDelete($this, $con);
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
            $con = Propel::getConnection(DbSourcePeer::DATABASE_NAME);
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
                    $pk = DbSourcePeer::doInsert($this, $con);
                    $affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
                                         // should always be true here (even though technically
                                         // BasePeer::doInsert() can insert multiple rows).

                    $this->setNew(false);
                } else {
                    $affectedRows += DbSourcePeer::doUpdate($this, $con);
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


            if (($retval = DbSourcePeer::doValidate($this, $columns)) !== true) {
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
        $pos = DbSourcePeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                return $this->getDbsUid();
                break;
            case 1:
                return $this->getProUid();
                break;
            case 2:
                return $this->getDbsType();
                break;
            case 3:
                return $this->getDbsServer();
                break;
            case 4:
                return $this->getDbsDatabaseName();
                break;
            case 5:
                return $this->getDbsUsername();
                break;
            case 6:
                return $this->getDbsPassword();
                break;
            case 7:
                return $this->getDbsPort();
                break;
            case 8:
                return $this->getDbsEncode();
                break;
            case 9:
                return $this->getDbsConnectionType();
                break;
            case 10:
                return $this->getDbsTns();
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
        $keys = DbSourcePeer::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getDbsUid(),
            $keys[1] => $this->getProUid(),
            $keys[2] => $this->getDbsType(),
            $keys[3] => $this->getDbsServer(),
            $keys[4] => $this->getDbsDatabaseName(),
            $keys[5] => $this->getDbsUsername(),
            $keys[6] => $this->getDbsPassword(),
            $keys[7] => $this->getDbsPort(),
            $keys[8] => $this->getDbsEncode(),
            $keys[9] => $this->getDbsConnectionType(),
            $keys[10] => $this->getDbsTns(),
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
        $pos = DbSourcePeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                $this->setDbsUid($value);
                break;
            case 1:
                $this->setProUid($value);
                break;
            case 2:
                $this->setDbsType($value);
                break;
            case 3:
                $this->setDbsServer($value);
                break;
            case 4:
                $this->setDbsDatabaseName($value);
                break;
            case 5:
                $this->setDbsUsername($value);
                break;
            case 6:
                $this->setDbsPassword($value);
                break;
            case 7:
                $this->setDbsPort($value);
                break;
            case 8:
                $this->setDbsEncode($value);
                break;
            case 9:
                $this->setDbsConnectionType($value);
                break;
            case 10:
                $this->setDbsTns($value);
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
        $keys = DbSourcePeer::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) {
            $this->setDbsUid($arr[$keys[0]]);
        }

        if (array_key_exists($keys[1], $arr)) {
            $this->setProUid($arr[$keys[1]]);
        }

        if (array_key_exists($keys[2], $arr)) {
            $this->setDbsType($arr[$keys[2]]);
        }

        if (array_key_exists($keys[3], $arr)) {
            $this->setDbsServer($arr[$keys[3]]);
        }

        if (array_key_exists($keys[4], $arr)) {
            $this->setDbsDatabaseName($arr[$keys[4]]);
        }

        if (array_key_exists($keys[5], $arr)) {
            $this->setDbsUsername($arr[$keys[5]]);
        }

        if (array_key_exists($keys[6], $arr)) {
            $this->setDbsPassword($arr[$keys[6]]);
        }

        if (array_key_exists($keys[7], $arr)) {
            $this->setDbsPort($arr[$keys[7]]);
        }

        if (array_key_exists($keys[8], $arr)) {
            $this->setDbsEncode($arr[$keys[8]]);
        }

        if (array_key_exists($keys[9], $arr)) {
            $this->setDbsConnectionType($arr[$keys[9]]);
        }

        if (array_key_exists($keys[10], $arr)) {
            $this->setDbsTns($arr[$keys[10]]);
        }

    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return     Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(DbSourcePeer::DATABASE_NAME);

        if ($this->isColumnModified(DbSourcePeer::DBS_UID)) {
            $criteria->add(DbSourcePeer::DBS_UID, $this->dbs_uid);
        }

        if ($this->isColumnModified(DbSourcePeer::PRO_UID)) {
            $criteria->add(DbSourcePeer::PRO_UID, $this->pro_uid);
        }

        if ($this->isColumnModified(DbSourcePeer::DBS_TYPE)) {
            $criteria->add(DbSourcePeer::DBS_TYPE, $this->dbs_type);
        }

        if ($this->isColumnModified(DbSourcePeer::DBS_SERVER)) {
            $criteria->add(DbSourcePeer::DBS_SERVER, $this->dbs_server);
        }

        if ($this->isColumnModified(DbSourcePeer::DBS_DATABASE_NAME)) {
            $criteria->add(DbSourcePeer::DBS_DATABASE_NAME, $this->dbs_database_name);
        }

        if ($this->isColumnModified(DbSourcePeer::DBS_USERNAME)) {
            $criteria->add(DbSourcePeer::DBS_USERNAME, $this->dbs_username);
        }

        if ($this->isColumnModified(DbSourcePeer::DBS_PASSWORD)) {
            $criteria->add(DbSourcePeer::DBS_PASSWORD, $this->dbs_password);
        }

        if ($this->isColumnModified(DbSourcePeer::DBS_PORT)) {
            $criteria->add(DbSourcePeer::DBS_PORT, $this->dbs_port);
        }

        if ($this->isColumnModified(DbSourcePeer::DBS_ENCODE)) {
            $criteria->add(DbSourcePeer::DBS_ENCODE, $this->dbs_encode);
        }

        if ($this->isColumnModified(DbSourcePeer::DBS_CONNECTION_TYPE)) {
            $criteria->add(DbSourcePeer::DBS_CONNECTION_TYPE, $this->dbs_connection_type);
        }

        if ($this->isColumnModified(DbSourcePeer::DBS_TNS)) {
            $criteria->add(DbSourcePeer::DBS_TNS, $this->dbs_tns);
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
        $criteria = new Criteria(DbSourcePeer::DATABASE_NAME);

        $criteria->add(DbSourcePeer::DBS_UID, $this->dbs_uid);
        $criteria->add(DbSourcePeer::PRO_UID, $this->pro_uid);

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

        $pks[0] = $this->getDbsUid();

        $pks[1] = $this->getProUid();

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

        $this->setDbsUid($keys[0]);

        $this->setProUid($keys[1]);

    }

    /**
     * Sets contents of passed object to values from current object.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param      object $copyObj An object of DbSource (or compatible) type.
     * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @throws     PropelException
     */
    public function copyInto($copyObj, $deepCopy = false)
    {

        $copyObj->setDbsType($this->dbs_type);

        $copyObj->setDbsServer($this->dbs_server);

        $copyObj->setDbsDatabaseName($this->dbs_database_name);

        $copyObj->setDbsUsername($this->dbs_username);

        $copyObj->setDbsPassword($this->dbs_password);

        $copyObj->setDbsPort($this->dbs_port);

        $copyObj->setDbsEncode($this->dbs_encode);

        $copyObj->setDbsConnectionType($this->dbs_connection_type);

        $copyObj->setDbsTns($this->dbs_tns);


        $copyObj->setNew(true);

        $copyObj->setDbsUid(''); // this is a pkey column, so set to default value

        $copyObj->setProUid('0'); // this is a pkey column, so set to default value

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
     * @return     DbSource Clone of current object.
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
     * @return     DbSourcePeer
     */
    public function getPeer()
    {
        if (self::$peer === null) {
            self::$peer = new DbSourcePeer();
        }
        return self::$peer;
    }
}

