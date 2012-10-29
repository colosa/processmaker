<?php

require_once 'propel/om/BaseObject.php';

require_once 'propel/om/Persistent.php';


include_once 'propel/util/Criteria.php';

include_once 'classes/model/AppMessagePeer.php';

/**
 * Base class that represents a row from the 'APP_MESSAGE' table.
 *
 * 
 *
 * @package    workflow.classes.model.om
 */
abstract class BaseAppMessage extends BaseObject implements Persistent
{

    /**
     * The Peer class.
     * Instance provides a convenient way of calling static methods on a class
     * that calling code may not be able to identify.
     * @var        AppMessagePeer
    */
    protected static $peer;

    /**
     * The value for the app_msg_uid field.
     * @var        string
     */
    protected $app_msg_uid;

    /**
     * The value for the msg_uid field.
     * @var        string
     */
    protected $msg_uid;

    /**
     * The value for the app_uid field.
     * @var        string
     */
    protected $app_uid = '';

    /**
     * The value for the del_index field.
     * @var        int
     */
    protected $del_index = 0;

    /**
     * The value for the app_msg_type field.
     * @var        string
     */
    protected $app_msg_type = '';

    /**
     * The value for the app_msg_subject field.
     * @var        string
     */
    protected $app_msg_subject = '';

    /**
     * The value for the app_msg_from field.
     * @var        string
     */
    protected $app_msg_from = '';

    /**
     * The value for the app_msg_to field.
     * @var        string
     */
    protected $app_msg_to;

    /**
     * The value for the app_msg_body field.
     * @var        string
     */
    protected $app_msg_body;

    /**
     * The value for the app_msg_date field.
     * @var        int
     */
    protected $app_msg_date;

    /**
     * The value for the app_msg_cc field.
     * @var        string
     */
    protected $app_msg_cc;

    /**
     * The value for the app_msg_bcc field.
     * @var        string
     */
    protected $app_msg_bcc;

    /**
     * The value for the app_msg_template field.
     * @var        string
     */
    protected $app_msg_template;

    /**
     * The value for the app_msg_status field.
     * @var        string
     */
    protected $app_msg_status;

    /**
     * The value for the app_msg_attach field.
     * @var        string
     */
    protected $app_msg_attach;

    /**
     * The value for the app_msg_send_date field.
     * @var        int
     */
    protected $app_msg_send_date;

    /**
     * The value for the app_msg_show_message field.
     * @var        int
     */
    protected $app_msg_show_message = 1;

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
     * Get the [app_msg_uid] column value.
     * 
     * @return     string
     */
    public function getAppMsgUid()
    {

        return $this->app_msg_uid;
    }

    /**
     * Get the [msg_uid] column value.
     * 
     * @return     string
     */
    public function getMsgUid()
    {

        return $this->msg_uid;
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
     * Get the [del_index] column value.
     * 
     * @return     int
     */
    public function getDelIndex()
    {

        return $this->del_index;
    }

    /**
     * Get the [app_msg_type] column value.
     * 
     * @return     string
     */
    public function getAppMsgType()
    {

        return $this->app_msg_type;
    }

    /**
     * Get the [app_msg_subject] column value.
     * 
     * @return     string
     */
    public function getAppMsgSubject()
    {

        return $this->app_msg_subject;
    }

    /**
     * Get the [app_msg_from] column value.
     * 
     * @return     string
     */
    public function getAppMsgFrom()
    {

        return $this->app_msg_from;
    }

    /**
     * Get the [app_msg_to] column value.
     * 
     * @return     string
     */
    public function getAppMsgTo()
    {

        return $this->app_msg_to;
    }

    /**
     * Get the [app_msg_body] column value.
     * 
     * @return     string
     */
    public function getAppMsgBody()
    {

        return $this->app_msg_body;
    }

    /**
     * Get the [optionally formatted] [app_msg_date] column value.
     * 
     * @param      string $format The date/time format string (either date()-style or strftime()-style).
     *                          If format is NULL, then the integer unix timestamp will be returned.
     * @return     mixed Formatted date/time value as string or integer unix timestamp (if format is NULL).
     * @throws     PropelException - if unable to convert the date/time to timestamp.
     */
    public function getAppMsgDate($format = 'Y-m-d H:i:s')
    {

        if ($this->app_msg_date === null || $this->app_msg_date === '') {
            return null;
        } elseif (!is_int($this->app_msg_date)) {
            // a non-timestamp value was set externally, so we convert it
            $ts = strtotime($this->app_msg_date);
            if ($ts === -1 || $ts === false) {
                throw new PropelException("Unable to parse value of [app_msg_date] as date/time value: " .
                    var_export($this->app_msg_date, true));
            }
        } else {
            $ts = $this->app_msg_date;
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
     * Get the [app_msg_cc] column value.
     * 
     * @return     string
     */
    public function getAppMsgCc()
    {

        return $this->app_msg_cc;
    }

    /**
     * Get the [app_msg_bcc] column value.
     * 
     * @return     string
     */
    public function getAppMsgBcc()
    {

        return $this->app_msg_bcc;
    }

    /**
     * Get the [app_msg_template] column value.
     * 
     * @return     string
     */
    public function getAppMsgTemplate()
    {

        return $this->app_msg_template;
    }

    /**
     * Get the [app_msg_status] column value.
     * 
     * @return     string
     */
    public function getAppMsgStatus()
    {

        return $this->app_msg_status;
    }

    /**
     * Get the [app_msg_attach] column value.
     * 
     * @return     string
     */
    public function getAppMsgAttach()
    {

        return $this->app_msg_attach;
    }

    /**
     * Get the [optionally formatted] [app_msg_send_date] column value.
     * 
     * @param      string $format The date/time format string (either date()-style or strftime()-style).
     *                          If format is NULL, then the integer unix timestamp will be returned.
     * @return     mixed Formatted date/time value as string or integer unix timestamp (if format is NULL).
     * @throws     PropelException - if unable to convert the date/time to timestamp.
     */
    public function getAppMsgSendDate($format = 'Y-m-d H:i:s')
    {

        if ($this->app_msg_send_date === null || $this->app_msg_send_date === '') {
            return null;
        } elseif (!is_int($this->app_msg_send_date)) {
            // a non-timestamp value was set externally, so we convert it
            $ts = strtotime($this->app_msg_send_date);
            if ($ts === -1 || $ts === false) {
                throw new PropelException("Unable to parse value of [app_msg_send_date] as date/time value: " .
                    var_export($this->app_msg_send_date, true));
            }
        } else {
            $ts = $this->app_msg_send_date;
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
     * Get the [app_msg_show_message] column value.
     * 
     * @return     int
     */
    public function getAppMsgShowMessage()
    {

        return $this->app_msg_show_message;
    }

    /**
     * Set the value of [app_msg_uid] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setAppMsgUid($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->app_msg_uid !== $v) {
            $this->app_msg_uid = $v;
            $this->modifiedColumns[] = AppMessagePeer::APP_MSG_UID;
        }

    } // setAppMsgUid()

    /**
     * Set the value of [msg_uid] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setMsgUid($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->msg_uid !== $v) {
            $this->msg_uid = $v;
            $this->modifiedColumns[] = AppMessagePeer::MSG_UID;
        }

    } // setMsgUid()

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
            $this->modifiedColumns[] = AppMessagePeer::APP_UID;
        }

    } // setAppUid()

    /**
     * Set the value of [del_index] column.
     * 
     * @param      int $v new value
     * @return     void
     */
    public function setDelIndex($v)
    {

        // Since the native PHP type for this column is integer,
        // we will cast the input value to an int (if it is not).
        if ($v !== null && !is_int($v) && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->del_index !== $v || $v === 0) {
            $this->del_index = $v;
            $this->modifiedColumns[] = AppMessagePeer::DEL_INDEX;
        }

    } // setDelIndex()

    /**
     * Set the value of [app_msg_type] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setAppMsgType($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->app_msg_type !== $v || $v === '') {
            $this->app_msg_type = $v;
            $this->modifiedColumns[] = AppMessagePeer::APP_MSG_TYPE;
        }

    } // setAppMsgType()

    /**
     * Set the value of [app_msg_subject] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setAppMsgSubject($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->app_msg_subject !== $v || $v === '') {
            $this->app_msg_subject = $v;
            $this->modifiedColumns[] = AppMessagePeer::APP_MSG_SUBJECT;
        }

    } // setAppMsgSubject()

    /**
     * Set the value of [app_msg_from] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setAppMsgFrom($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->app_msg_from !== $v || $v === '') {
            $this->app_msg_from = $v;
            $this->modifiedColumns[] = AppMessagePeer::APP_MSG_FROM;
        }

    } // setAppMsgFrom()

    /**
     * Set the value of [app_msg_to] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setAppMsgTo($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->app_msg_to !== $v) {
            $this->app_msg_to = $v;
            $this->modifiedColumns[] = AppMessagePeer::APP_MSG_TO;
        }

    } // setAppMsgTo()

    /**
     * Set the value of [app_msg_body] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setAppMsgBody($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->app_msg_body !== $v) {
            $this->app_msg_body = $v;
            $this->modifiedColumns[] = AppMessagePeer::APP_MSG_BODY;
        }

    } // setAppMsgBody()

    /**
     * Set the value of [app_msg_date] column.
     * 
     * @param      int $v new value
     * @return     void
     */
    public function setAppMsgDate($v)
    {

        if ($v !== null && !is_int($v)) {
            $ts = strtotime($v);
            if ($ts === -1 || $ts === false) {
                throw new PropelException("Unable to parse date/time value for [app_msg_date] from input: " .
                    var_export($v, true));
            }
        } else {
            $ts = $v;
        }
        if ($this->app_msg_date !== $ts) {
            $this->app_msg_date = $ts;
            $this->modifiedColumns[] = AppMessagePeer::APP_MSG_DATE;
        }

    } // setAppMsgDate()

    /**
     * Set the value of [app_msg_cc] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setAppMsgCc($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->app_msg_cc !== $v) {
            $this->app_msg_cc = $v;
            $this->modifiedColumns[] = AppMessagePeer::APP_MSG_CC;
        }

    } // setAppMsgCc()

    /**
     * Set the value of [app_msg_bcc] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setAppMsgBcc($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->app_msg_bcc !== $v) {
            $this->app_msg_bcc = $v;
            $this->modifiedColumns[] = AppMessagePeer::APP_MSG_BCC;
        }

    } // setAppMsgBcc()

    /**
     * Set the value of [app_msg_template] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setAppMsgTemplate($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->app_msg_template !== $v) {
            $this->app_msg_template = $v;
            $this->modifiedColumns[] = AppMessagePeer::APP_MSG_TEMPLATE;
        }

    } // setAppMsgTemplate()

    /**
     * Set the value of [app_msg_status] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setAppMsgStatus($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->app_msg_status !== $v) {
            $this->app_msg_status = $v;
            $this->modifiedColumns[] = AppMessagePeer::APP_MSG_STATUS;
        }

    } // setAppMsgStatus()

    /**
     * Set the value of [app_msg_attach] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setAppMsgAttach($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->app_msg_attach !== $v) {
            $this->app_msg_attach = $v;
            $this->modifiedColumns[] = AppMessagePeer::APP_MSG_ATTACH;
        }

    } // setAppMsgAttach()

    /**
     * Set the value of [app_msg_send_date] column.
     * 
     * @param      int $v new value
     * @return     void
     */
    public function setAppMsgSendDate($v)
    {

        if ($v !== null && !is_int($v)) {
            $ts = strtotime($v);
            if ($ts === -1 || $ts === false) {
                throw new PropelException("Unable to parse date/time value for [app_msg_send_date] from input: " .
                    var_export($v, true));
            }
        } else {
            $ts = $v;
        }
        if ($this->app_msg_send_date !== $ts) {
            $this->app_msg_send_date = $ts;
            $this->modifiedColumns[] = AppMessagePeer::APP_MSG_SEND_DATE;
        }

    } // setAppMsgSendDate()

    /**
     * Set the value of [app_msg_show_message] column.
     * 
     * @param      int $v new value
     * @return     void
     */
    public function setAppMsgShowMessage($v)
    {

        // Since the native PHP type for this column is integer,
        // we will cast the input value to an int (if it is not).
        if ($v !== null && !is_int($v) && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->app_msg_show_message !== $v || $v === 1) {
            $this->app_msg_show_message = $v;
            $this->modifiedColumns[] = AppMessagePeer::APP_MSG_SHOW_MESSAGE;
        }

    } // setAppMsgShowMessage()

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

            $this->app_msg_uid = $rs->getString($startcol + 0);

            $this->msg_uid = $rs->getString($startcol + 1);

            $this->app_uid = $rs->getString($startcol + 2);

            $this->del_index = $rs->getInt($startcol + 3);

            $this->app_msg_type = $rs->getString($startcol + 4);

            $this->app_msg_subject = $rs->getString($startcol + 5);

            $this->app_msg_from = $rs->getString($startcol + 6);

            $this->app_msg_to = $rs->getString($startcol + 7);

            $this->app_msg_body = $rs->getString($startcol + 8);

            $this->app_msg_date = $rs->getTimestamp($startcol + 9, null);

            $this->app_msg_cc = $rs->getString($startcol + 10);

            $this->app_msg_bcc = $rs->getString($startcol + 11);

            $this->app_msg_template = $rs->getString($startcol + 12);

            $this->app_msg_status = $rs->getString($startcol + 13);

            $this->app_msg_attach = $rs->getString($startcol + 14);

            $this->app_msg_send_date = $rs->getTimestamp($startcol + 15, null);

            $this->app_msg_show_message = $rs->getInt($startcol + 16);

            $this->resetModified();

            $this->setNew(false);

            // FIXME - using NUM_COLUMNS may be clearer.
            return $startcol + 17; // 17 = AppMessagePeer::NUM_COLUMNS - AppMessagePeer::NUM_LAZY_LOAD_COLUMNS).

        } catch (Exception $e) {
            throw new PropelException("Error populating AppMessage object", $e);
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
            $con = Propel::getConnection(AppMessagePeer::DATABASE_NAME);
        }

        try {
            $con->begin();
            AppMessagePeer::doDelete($this, $con);
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
            $con = Propel::getConnection(AppMessagePeer::DATABASE_NAME);
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
                    $pk = AppMessagePeer::doInsert($this, $con);
                    $affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
                                         // should always be true here (even though technically
                                         // BasePeer::doInsert() can insert multiple rows).

                    $this->setNew(false);
                } else {
                    $affectedRows += AppMessagePeer::doUpdate($this, $con);
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


            if (($retval = AppMessagePeer::doValidate($this, $columns)) !== true) {
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
        $pos = AppMessagePeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                return $this->getAppMsgUid();
                break;
            case 1:
                return $this->getMsgUid();
                break;
            case 2:
                return $this->getAppUid();
                break;
            case 3:
                return $this->getDelIndex();
                break;
            case 4:
                return $this->getAppMsgType();
                break;
            case 5:
                return $this->getAppMsgSubject();
                break;
            case 6:
                return $this->getAppMsgFrom();
                break;
            case 7:
                return $this->getAppMsgTo();
                break;
            case 8:
                return $this->getAppMsgBody();
                break;
            case 9:
                return $this->getAppMsgDate();
                break;
            case 10:
                return $this->getAppMsgCc();
                break;
            case 11:
                return $this->getAppMsgBcc();
                break;
            case 12:
                return $this->getAppMsgTemplate();
                break;
            case 13:
                return $this->getAppMsgStatus();
                break;
            case 14:
                return $this->getAppMsgAttach();
                break;
            case 15:
                return $this->getAppMsgSendDate();
                break;
            case 16:
                return $this->getAppMsgShowMessage();
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
        $keys = AppMessagePeer::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getAppMsgUid(),
            $keys[1] => $this->getMsgUid(),
            $keys[2] => $this->getAppUid(),
            $keys[3] => $this->getDelIndex(),
            $keys[4] => $this->getAppMsgType(),
            $keys[5] => $this->getAppMsgSubject(),
            $keys[6] => $this->getAppMsgFrom(),
            $keys[7] => $this->getAppMsgTo(),
            $keys[8] => $this->getAppMsgBody(),
            $keys[9] => $this->getAppMsgDate(),
            $keys[10] => $this->getAppMsgCc(),
            $keys[11] => $this->getAppMsgBcc(),
            $keys[12] => $this->getAppMsgTemplate(),
            $keys[13] => $this->getAppMsgStatus(),
            $keys[14] => $this->getAppMsgAttach(),
            $keys[15] => $this->getAppMsgSendDate(),
            $keys[16] => $this->getAppMsgShowMessage(),
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
        $pos = AppMessagePeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                $this->setAppMsgUid($value);
                break;
            case 1:
                $this->setMsgUid($value);
                break;
            case 2:
                $this->setAppUid($value);
                break;
            case 3:
                $this->setDelIndex($value);
                break;
            case 4:
                $this->setAppMsgType($value);
                break;
            case 5:
                $this->setAppMsgSubject($value);
                break;
            case 6:
                $this->setAppMsgFrom($value);
                break;
            case 7:
                $this->setAppMsgTo($value);
                break;
            case 8:
                $this->setAppMsgBody($value);
                break;
            case 9:
                $this->setAppMsgDate($value);
                break;
            case 10:
                $this->setAppMsgCc($value);
                break;
            case 11:
                $this->setAppMsgBcc($value);
                break;
            case 12:
                $this->setAppMsgTemplate($value);
                break;
            case 13:
                $this->setAppMsgStatus($value);
                break;
            case 14:
                $this->setAppMsgAttach($value);
                break;
            case 15:
                $this->setAppMsgSendDate($value);
                break;
            case 16:
                $this->setAppMsgShowMessage($value);
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
        $keys = AppMessagePeer::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) {
            $this->setAppMsgUid($arr[$keys[0]]);
        }

        if (array_key_exists($keys[1], $arr)) {
            $this->setMsgUid($arr[$keys[1]]);
        }

        if (array_key_exists($keys[2], $arr)) {
            $this->setAppUid($arr[$keys[2]]);
        }

        if (array_key_exists($keys[3], $arr)) {
            $this->setDelIndex($arr[$keys[3]]);
        }

        if (array_key_exists($keys[4], $arr)) {
            $this->setAppMsgType($arr[$keys[4]]);
        }

        if (array_key_exists($keys[5], $arr)) {
            $this->setAppMsgSubject($arr[$keys[5]]);
        }

        if (array_key_exists($keys[6], $arr)) {
            $this->setAppMsgFrom($arr[$keys[6]]);
        }

        if (array_key_exists($keys[7], $arr)) {
            $this->setAppMsgTo($arr[$keys[7]]);
        }

        if (array_key_exists($keys[8], $arr)) {
            $this->setAppMsgBody($arr[$keys[8]]);
        }

        if (array_key_exists($keys[9], $arr)) {
            $this->setAppMsgDate($arr[$keys[9]]);
        }

        if (array_key_exists($keys[10], $arr)) {
            $this->setAppMsgCc($arr[$keys[10]]);
        }

        if (array_key_exists($keys[11], $arr)) {
            $this->setAppMsgBcc($arr[$keys[11]]);
        }

        if (array_key_exists($keys[12], $arr)) {
            $this->setAppMsgTemplate($arr[$keys[12]]);
        }

        if (array_key_exists($keys[13], $arr)) {
            $this->setAppMsgStatus($arr[$keys[13]]);
        }

        if (array_key_exists($keys[14], $arr)) {
            $this->setAppMsgAttach($arr[$keys[14]]);
        }

        if (array_key_exists($keys[15], $arr)) {
            $this->setAppMsgSendDate($arr[$keys[15]]);
        }

        if (array_key_exists($keys[16], $arr)) {
            $this->setAppMsgShowMessage($arr[$keys[16]]);
        }

    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return     Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(AppMessagePeer::DATABASE_NAME);

        if ($this->isColumnModified(AppMessagePeer::APP_MSG_UID)) {
            $criteria->add(AppMessagePeer::APP_MSG_UID, $this->app_msg_uid);
        }

        if ($this->isColumnModified(AppMessagePeer::MSG_UID)) {
            $criteria->add(AppMessagePeer::MSG_UID, $this->msg_uid);
        }

        if ($this->isColumnModified(AppMessagePeer::APP_UID)) {
            $criteria->add(AppMessagePeer::APP_UID, $this->app_uid);
        }

        if ($this->isColumnModified(AppMessagePeer::DEL_INDEX)) {
            $criteria->add(AppMessagePeer::DEL_INDEX, $this->del_index);
        }

        if ($this->isColumnModified(AppMessagePeer::APP_MSG_TYPE)) {
            $criteria->add(AppMessagePeer::APP_MSG_TYPE, $this->app_msg_type);
        }

        if ($this->isColumnModified(AppMessagePeer::APP_MSG_SUBJECT)) {
            $criteria->add(AppMessagePeer::APP_MSG_SUBJECT, $this->app_msg_subject);
        }

        if ($this->isColumnModified(AppMessagePeer::APP_MSG_FROM)) {
            $criteria->add(AppMessagePeer::APP_MSG_FROM, $this->app_msg_from);
        }

        if ($this->isColumnModified(AppMessagePeer::APP_MSG_TO)) {
            $criteria->add(AppMessagePeer::APP_MSG_TO, $this->app_msg_to);
        }

        if ($this->isColumnModified(AppMessagePeer::APP_MSG_BODY)) {
            $criteria->add(AppMessagePeer::APP_MSG_BODY, $this->app_msg_body);
        }

        if ($this->isColumnModified(AppMessagePeer::APP_MSG_DATE)) {
            $criteria->add(AppMessagePeer::APP_MSG_DATE, $this->app_msg_date);
        }

        if ($this->isColumnModified(AppMessagePeer::APP_MSG_CC)) {
            $criteria->add(AppMessagePeer::APP_MSG_CC, $this->app_msg_cc);
        }

        if ($this->isColumnModified(AppMessagePeer::APP_MSG_BCC)) {
            $criteria->add(AppMessagePeer::APP_MSG_BCC, $this->app_msg_bcc);
        }

        if ($this->isColumnModified(AppMessagePeer::APP_MSG_TEMPLATE)) {
            $criteria->add(AppMessagePeer::APP_MSG_TEMPLATE, $this->app_msg_template);
        }

        if ($this->isColumnModified(AppMessagePeer::APP_MSG_STATUS)) {
            $criteria->add(AppMessagePeer::APP_MSG_STATUS, $this->app_msg_status);
        }

        if ($this->isColumnModified(AppMessagePeer::APP_MSG_ATTACH)) {
            $criteria->add(AppMessagePeer::APP_MSG_ATTACH, $this->app_msg_attach);
        }

        if ($this->isColumnModified(AppMessagePeer::APP_MSG_SEND_DATE)) {
            $criteria->add(AppMessagePeer::APP_MSG_SEND_DATE, $this->app_msg_send_date);
        }

        if ($this->isColumnModified(AppMessagePeer::APP_MSG_SHOW_MESSAGE)) {
            $criteria->add(AppMessagePeer::APP_MSG_SHOW_MESSAGE, $this->app_msg_show_message);
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
        $criteria = new Criteria(AppMessagePeer::DATABASE_NAME);

        $criteria->add(AppMessagePeer::APP_MSG_UID, $this->app_msg_uid);

        return $criteria;
    }

    /**
     * Returns the primary key for this object (row).
     * @return     string
     */
    public function getPrimaryKey()
    {
        return $this->getAppMsgUid();
    }

    /**
     * Generic method to set the primary key (app_msg_uid column).
     *
     * @param      string $key Primary key.
     * @return     void
     */
    public function setPrimaryKey($key)
    {
        $this->setAppMsgUid($key);
    }

    /**
     * Sets contents of passed object to values from current object.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param      object $copyObj An object of AppMessage (or compatible) type.
     * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @throws     PropelException
     */
    public function copyInto($copyObj, $deepCopy = false)
    {

        $copyObj->setMsgUid($this->msg_uid);

        $copyObj->setAppUid($this->app_uid);

        $copyObj->setDelIndex($this->del_index);

        $copyObj->setAppMsgType($this->app_msg_type);

        $copyObj->setAppMsgSubject($this->app_msg_subject);

        $copyObj->setAppMsgFrom($this->app_msg_from);

        $copyObj->setAppMsgTo($this->app_msg_to);

        $copyObj->setAppMsgBody($this->app_msg_body);

        $copyObj->setAppMsgDate($this->app_msg_date);

        $copyObj->setAppMsgCc($this->app_msg_cc);

        $copyObj->setAppMsgBcc($this->app_msg_bcc);

        $copyObj->setAppMsgTemplate($this->app_msg_template);

        $copyObj->setAppMsgStatus($this->app_msg_status);

        $copyObj->setAppMsgAttach($this->app_msg_attach);

        $copyObj->setAppMsgSendDate($this->app_msg_send_date);

        $copyObj->setAppMsgShowMessage($this->app_msg_show_message);


        $copyObj->setNew(true);

        $copyObj->setAppMsgUid(NULL); // this is a pkey column, so set to default value

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
     * @return     AppMessage Clone of current object.
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
     * @return     AppMessagePeer
     */
    public function getPeer()
    {
        if (self::$peer === null) {
            self::$peer = new AppMessagePeer();
        }
        return self::$peer;
    }
}

