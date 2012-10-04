<?php

require_once 'propel/om/BaseObject.php';

require_once 'propel/om/Persistent.php';


include_once 'propel/util/Criteria.php';

include_once 'classes/model/UsersPeer.php';

/**
 * Base class that represents a row from the 'USERS' table.
 *
 * 
 *
 * @package    workflow.classes.model.om
 */
abstract class BaseUsers extends BaseObject implements Persistent
{

    /**
     * The Peer class.
     * Instance provides a convenient way of calling static methods on a class
     * that calling code may not be able to identify.
     * @var        UsersPeer
    */
    protected static $peer;

    /**
     * The value for the usr_uid field.
     * @var        string
     */
    protected $usr_uid = '';

    /**
     * The value for the usr_username field.
     * @var        string
     */
    protected $usr_username = '';

    /**
     * The value for the usr_password field.
     * @var        string
     */
    protected $usr_password = '';

    /**
     * The value for the usr_firstname field.
     * @var        string
     */
    protected $usr_firstname = '';

    /**
     * The value for the usr_lastname field.
     * @var        string
     */
    protected $usr_lastname = '';

    /**
     * The value for the usr_email field.
     * @var        string
     */
    protected $usr_email = '';

    /**
     * The value for the usr_due_date field.
     * @var        int
     */
    protected $usr_due_date;

    /**
     * The value for the usr_create_date field.
     * @var        int
     */
    protected $usr_create_date;

    /**
     * The value for the usr_update_date field.
     * @var        int
     */
    protected $usr_update_date;

    /**
     * The value for the usr_status field.
     * @var        string
     */
    protected $usr_status = 'ACTIVE';

    /**
     * The value for the usr_country field.
     * @var        string
     */
    protected $usr_country = '';

    /**
     * The value for the usr_city field.
     * @var        string
     */
    protected $usr_city = '';

    /**
     * The value for the usr_location field.
     * @var        string
     */
    protected $usr_location = '';

    /**
     * The value for the usr_address field.
     * @var        string
     */
    protected $usr_address = '';

    /**
     * The value for the usr_phone field.
     * @var        string
     */
    protected $usr_phone = '';

    /**
     * The value for the usr_fax field.
     * @var        string
     */
    protected $usr_fax = '';

    /**
     * The value for the usr_cellular field.
     * @var        string
     */
    protected $usr_cellular = '';

    /**
     * The value for the usr_zip_code field.
     * @var        string
     */
    protected $usr_zip_code = '';

    /**
     * The value for the dep_uid field.
     * @var        string
     */
    protected $dep_uid = '';

    /**
     * The value for the usr_position field.
     * @var        string
     */
    protected $usr_position = '';

    /**
     * The value for the usr_resume field.
     * @var        string
     */
    protected $usr_resume = '';

    /**
     * The value for the usr_birthday field.
     * @var        int
     */
    protected $usr_birthday;

    /**
     * The value for the usr_role field.
     * @var        string
     */
    protected $usr_role = 'PROCESSMAKER_ADMIN';

    /**
     * The value for the usr_reports_to field.
     * @var        string
     */
    protected $usr_reports_to = '';

    /**
     * The value for the usr_replaced_by field.
     * @var        string
     */
    protected $usr_replaced_by = '';

    /**
     * The value for the usr_ux field.
     * @var        string
     */
    protected $usr_ux = 'NORMAL';

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
     * Get the [usr_uid] column value.
     * 
     * @return     string
     */
    public function getUsrUid()
    {

        return $this->usr_uid;
    }

    /**
     * Get the [usr_username] column value.
     * 
     * @return     string
     */
    public function getUsrUsername()
    {

        return $this->usr_username;
    }

    /**
     * Get the [usr_password] column value.
     * 
     * @return     string
     */
    public function getUsrPassword()
    {

        return $this->usr_password;
    }

    /**
     * Get the [usr_firstname] column value.
     * 
     * @return     string
     */
    public function getUsrFirstname()
    {

        return $this->usr_firstname;
    }

    /**
     * Get the [usr_lastname] column value.
     * 
     * @return     string
     */
    public function getUsrLastname()
    {

        return $this->usr_lastname;
    }

    /**
     * Get the [usr_email] column value.
     * 
     * @return     string
     */
    public function getUsrEmail()
    {

        return $this->usr_email;
    }

    /**
     * Get the [optionally formatted] [usr_due_date] column value.
     * 
     * @param      string $format The date/time format string (either date()-style or strftime()-style).
     *                          If format is NULL, then the integer unix timestamp will be returned.
     * @return     mixed Formatted date/time value as string or integer unix timestamp (if format is NULL).
     * @throws     PropelException - if unable to convert the date/time to timestamp.
     */
    public function getUsrDueDate($format = 'Y-m-d')
    {

        if ($this->usr_due_date === null || $this->usr_due_date === '') {
            return null;
        } elseif (!is_int($this->usr_due_date)) {
            // a non-timestamp value was set externally, so we convert it
            $ts = strtotime($this->usr_due_date);
            if ($ts === -1 || $ts === false) {
                throw new PropelException("Unable to parse value of [usr_due_date] as date/time value: " .
                    var_export($this->usr_due_date, true));
            }
        } else {
            $ts = $this->usr_due_date;
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
     * Get the [optionally formatted] [usr_create_date] column value.
     * 
     * @param      string $format The date/time format string (either date()-style or strftime()-style).
     *                          If format is NULL, then the integer unix timestamp will be returned.
     * @return     mixed Formatted date/time value as string or integer unix timestamp (if format is NULL).
     * @throws     PropelException - if unable to convert the date/time to timestamp.
     */
    public function getUsrCreateDate($format = 'Y-m-d H:i:s')
    {

        if ($this->usr_create_date === null || $this->usr_create_date === '') {
            return null;
        } elseif (!is_int($this->usr_create_date)) {
            // a non-timestamp value was set externally, so we convert it
            $ts = strtotime($this->usr_create_date);
            if ($ts === -1 || $ts === false) {
                throw new PropelException("Unable to parse value of [usr_create_date] as date/time value: " .
                    var_export($this->usr_create_date, true));
            }
        } else {
            $ts = $this->usr_create_date;
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
     * Get the [optionally formatted] [usr_update_date] column value.
     * 
     * @param      string $format The date/time format string (either date()-style or strftime()-style).
     *                          If format is NULL, then the integer unix timestamp will be returned.
     * @return     mixed Formatted date/time value as string or integer unix timestamp (if format is NULL).
     * @throws     PropelException - if unable to convert the date/time to timestamp.
     */
    public function getUsrUpdateDate($format = 'Y-m-d H:i:s')
    {

        if ($this->usr_update_date === null || $this->usr_update_date === '') {
            return null;
        } elseif (!is_int($this->usr_update_date)) {
            // a non-timestamp value was set externally, so we convert it
            $ts = strtotime($this->usr_update_date);
            if ($ts === -1 || $ts === false) {
                throw new PropelException("Unable to parse value of [usr_update_date] as date/time value: " .
                    var_export($this->usr_update_date, true));
            }
        } else {
            $ts = $this->usr_update_date;
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
     * Get the [usr_status] column value.
     * 
     * @return     string
     */
    public function getUsrStatus()
    {

        return $this->usr_status;
    }

    /**
     * Get the [usr_country] column value.
     * 
     * @return     string
     */
    public function getUsrCountry()
    {

        return $this->usr_country;
    }

    /**
     * Get the [usr_city] column value.
     * 
     * @return     string
     */
    public function getUsrCity()
    {

        return $this->usr_city;
    }

    /**
     * Get the [usr_location] column value.
     * 
     * @return     string
     */
    public function getUsrLocation()
    {

        return $this->usr_location;
    }

    /**
     * Get the [usr_address] column value.
     * 
     * @return     string
     */
    public function getUsrAddress()
    {

        return $this->usr_address;
    }

    /**
     * Get the [usr_phone] column value.
     * 
     * @return     string
     */
    public function getUsrPhone()
    {

        return $this->usr_phone;
    }

    /**
     * Get the [usr_fax] column value.
     * 
     * @return     string
     */
    public function getUsrFax()
    {

        return $this->usr_fax;
    }

    /**
     * Get the [usr_cellular] column value.
     * 
     * @return     string
     */
    public function getUsrCellular()
    {

        return $this->usr_cellular;
    }

    /**
     * Get the [usr_zip_code] column value.
     * 
     * @return     string
     */
    public function getUsrZipCode()
    {

        return $this->usr_zip_code;
    }

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
     * Get the [usr_position] column value.
     * 
     * @return     string
     */
    public function getUsrPosition()
    {

        return $this->usr_position;
    }

    /**
     * Get the [usr_resume] column value.
     * 
     * @return     string
     */
    public function getUsrResume()
    {

        return $this->usr_resume;
    }

    /**
     * Get the [optionally formatted] [usr_birthday] column value.
     * 
     * @param      string $format The date/time format string (either date()-style or strftime()-style).
     *                          If format is NULL, then the integer unix timestamp will be returned.
     * @return     mixed Formatted date/time value as string or integer unix timestamp (if format is NULL).
     * @throws     PropelException - if unable to convert the date/time to timestamp.
     */
    public function getUsrBirthday($format = 'Y-m-d')
    {

        if ($this->usr_birthday === null || $this->usr_birthday === '') {
            return null;
        } elseif (!is_int($this->usr_birthday)) {
            // a non-timestamp value was set externally, so we convert it
            $ts = strtotime($this->usr_birthday);
            if ($ts === -1 || $ts === false) {
                throw new PropelException("Unable to parse value of [usr_birthday] as date/time value: " .
                    var_export($this->usr_birthday, true));
            }
        } else {
            $ts = $this->usr_birthday;
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
     * Get the [usr_role] column value.
     * 
     * @return     string
     */
    public function getUsrRole()
    {

        return $this->usr_role;
    }

    /**
     * Get the [usr_reports_to] column value.
     * 
     * @return     string
     */
    public function getUsrReportsTo()
    {

        return $this->usr_reports_to;
    }

    /**
     * Get the [usr_replaced_by] column value.
     * 
     * @return     string
     */
    public function getUsrReplacedBy()
    {

        return $this->usr_replaced_by;
    }

    /**
     * Get the [usr_ux] column value.
     * 
     * @return     string
     */
    public function getUsrUx()
    {

        return $this->usr_ux;
    }

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
            $this->modifiedColumns[] = UsersPeer::USR_UID;
        }

    } // setUsrUid()

    /**
     * Set the value of [usr_username] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setUsrUsername($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->usr_username !== $v || $v === '') {
            $this->usr_username = $v;
            $this->modifiedColumns[] = UsersPeer::USR_USERNAME;
        }

    } // setUsrUsername()

    /**
     * Set the value of [usr_password] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setUsrPassword($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->usr_password !== $v || $v === '') {
            $this->usr_password = $v;
            $this->modifiedColumns[] = UsersPeer::USR_PASSWORD;
        }

    } // setUsrPassword()

    /**
     * Set the value of [usr_firstname] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setUsrFirstname($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->usr_firstname !== $v || $v === '') {
            $this->usr_firstname = $v;
            $this->modifiedColumns[] = UsersPeer::USR_FIRSTNAME;
        }

    } // setUsrFirstname()

    /**
     * Set the value of [usr_lastname] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setUsrLastname($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->usr_lastname !== $v || $v === '') {
            $this->usr_lastname = $v;
            $this->modifiedColumns[] = UsersPeer::USR_LASTNAME;
        }

    } // setUsrLastname()

    /**
     * Set the value of [usr_email] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setUsrEmail($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->usr_email !== $v || $v === '') {
            $this->usr_email = $v;
            $this->modifiedColumns[] = UsersPeer::USR_EMAIL;
        }

    } // setUsrEmail()

    /**
     * Set the value of [usr_due_date] column.
     * 
     * @param      int $v new value
     * @return     void
     */
    public function setUsrDueDate($v)
    {

        if ($v !== null && !is_int($v)) {
            $ts = strtotime($v);
            if ($ts === -1 || $ts === false) {
                throw new PropelException("Unable to parse date/time value for [usr_due_date] from input: " .
                    var_export($v, true));
            }
        } else {
            $ts = $v;
        }
        if ($this->usr_due_date !== $ts) {
            $this->usr_due_date = $ts;
            $this->modifiedColumns[] = UsersPeer::USR_DUE_DATE;
        }

    } // setUsrDueDate()

    /**
     * Set the value of [usr_create_date] column.
     * 
     * @param      int $v new value
     * @return     void
     */
    public function setUsrCreateDate($v)
    {

        if ($v !== null && !is_int($v)) {
            $ts = strtotime($v);
            if ($ts === -1 || $ts === false) {
                throw new PropelException("Unable to parse date/time value for [usr_create_date] from input: " .
                    var_export($v, true));
            }
        } else {
            $ts = $v;
        }
        if ($this->usr_create_date !== $ts) {
            $this->usr_create_date = $ts;
            $this->modifiedColumns[] = UsersPeer::USR_CREATE_DATE;
        }

    } // setUsrCreateDate()

    /**
     * Set the value of [usr_update_date] column.
     * 
     * @param      int $v new value
     * @return     void
     */
    public function setUsrUpdateDate($v)
    {

        if ($v !== null && !is_int($v)) {
            $ts = strtotime($v);
            if ($ts === -1 || $ts === false) {
                throw new PropelException("Unable to parse date/time value for [usr_update_date] from input: " .
                    var_export($v, true));
            }
        } else {
            $ts = $v;
        }
        if ($this->usr_update_date !== $ts) {
            $this->usr_update_date = $ts;
            $this->modifiedColumns[] = UsersPeer::USR_UPDATE_DATE;
        }

    } // setUsrUpdateDate()

    /**
     * Set the value of [usr_status] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setUsrStatus($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->usr_status !== $v || $v === 'ACTIVE') {
            $this->usr_status = $v;
            $this->modifiedColumns[] = UsersPeer::USR_STATUS;
        }

    } // setUsrStatus()

    /**
     * Set the value of [usr_country] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setUsrCountry($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->usr_country !== $v || $v === '') {
            $this->usr_country = $v;
            $this->modifiedColumns[] = UsersPeer::USR_COUNTRY;
        }

    } // setUsrCountry()

    /**
     * Set the value of [usr_city] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setUsrCity($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->usr_city !== $v || $v === '') {
            $this->usr_city = $v;
            $this->modifiedColumns[] = UsersPeer::USR_CITY;
        }

    } // setUsrCity()

    /**
     * Set the value of [usr_location] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setUsrLocation($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->usr_location !== $v || $v === '') {
            $this->usr_location = $v;
            $this->modifiedColumns[] = UsersPeer::USR_LOCATION;
        }

    } // setUsrLocation()

    /**
     * Set the value of [usr_address] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setUsrAddress($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->usr_address !== $v || $v === '') {
            $this->usr_address = $v;
            $this->modifiedColumns[] = UsersPeer::USR_ADDRESS;
        }

    } // setUsrAddress()

    /**
     * Set the value of [usr_phone] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setUsrPhone($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->usr_phone !== $v || $v === '') {
            $this->usr_phone = $v;
            $this->modifiedColumns[] = UsersPeer::USR_PHONE;
        }

    } // setUsrPhone()

    /**
     * Set the value of [usr_fax] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setUsrFax($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->usr_fax !== $v || $v === '') {
            $this->usr_fax = $v;
            $this->modifiedColumns[] = UsersPeer::USR_FAX;
        }

    } // setUsrFax()

    /**
     * Set the value of [usr_cellular] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setUsrCellular($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->usr_cellular !== $v || $v === '') {
            $this->usr_cellular = $v;
            $this->modifiedColumns[] = UsersPeer::USR_CELLULAR;
        }

    } // setUsrCellular()

    /**
     * Set the value of [usr_zip_code] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setUsrZipCode($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->usr_zip_code !== $v || $v === '') {
            $this->usr_zip_code = $v;
            $this->modifiedColumns[] = UsersPeer::USR_ZIP_CODE;
        }

    } // setUsrZipCode()

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
            $this->modifiedColumns[] = UsersPeer::DEP_UID;
        }

    } // setDepUid()

    /**
     * Set the value of [usr_position] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setUsrPosition($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->usr_position !== $v || $v === '') {
            $this->usr_position = $v;
            $this->modifiedColumns[] = UsersPeer::USR_POSITION;
        }

    } // setUsrPosition()

    /**
     * Set the value of [usr_resume] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setUsrResume($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->usr_resume !== $v || $v === '') {
            $this->usr_resume = $v;
            $this->modifiedColumns[] = UsersPeer::USR_RESUME;
        }

    } // setUsrResume()

    /**
     * Set the value of [usr_birthday] column.
     * 
     * @param      int $v new value
     * @return     void
     */
    public function setUsrBirthday($v)
    {

        if ($v !== null && !is_int($v)) {
            $ts = strtotime($v);
            if ($ts === -1 || $ts === false) {
                throw new PropelException("Unable to parse date/time value for [usr_birthday] from input: " .
                    var_export($v, true));
            }
        } else {
            $ts = $v;
        }
        if ($this->usr_birthday !== $ts) {
            $this->usr_birthday = $ts;
            $this->modifiedColumns[] = UsersPeer::USR_BIRTHDAY;
        }

    } // setUsrBirthday()

    /**
     * Set the value of [usr_role] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setUsrRole($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->usr_role !== $v || $v === 'PROCESSMAKER_ADMIN') {
            $this->usr_role = $v;
            $this->modifiedColumns[] = UsersPeer::USR_ROLE;
        }

    } // setUsrRole()

    /**
     * Set the value of [usr_reports_to] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setUsrReportsTo($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->usr_reports_to !== $v || $v === '') {
            $this->usr_reports_to = $v;
            $this->modifiedColumns[] = UsersPeer::USR_REPORTS_TO;
        }

    } // setUsrReportsTo()

    /**
     * Set the value of [usr_replaced_by] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setUsrReplacedBy($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->usr_replaced_by !== $v || $v === '') {
            $this->usr_replaced_by = $v;
            $this->modifiedColumns[] = UsersPeer::USR_REPLACED_BY;
        }

    } // setUsrReplacedBy()

    /**
     * Set the value of [usr_ux] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setUsrUx($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->usr_ux !== $v || $v === 'NORMAL') {
            $this->usr_ux = $v;
            $this->modifiedColumns[] = UsersPeer::USR_UX;
        }

    } // setUsrUx()

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

            $this->usr_uid = $rs->getString($startcol + 0);

            $this->usr_username = $rs->getString($startcol + 1);

            $this->usr_password = $rs->getString($startcol + 2);

            $this->usr_firstname = $rs->getString($startcol + 3);

            $this->usr_lastname = $rs->getString($startcol + 4);

            $this->usr_email = $rs->getString($startcol + 5);

            $this->usr_due_date = $rs->getDate($startcol + 6, null);

            $this->usr_create_date = $rs->getTimestamp($startcol + 7, null);

            $this->usr_update_date = $rs->getTimestamp($startcol + 8, null);

            $this->usr_status = $rs->getString($startcol + 9);

            $this->usr_country = $rs->getString($startcol + 10);

            $this->usr_city = $rs->getString($startcol + 11);

            $this->usr_location = $rs->getString($startcol + 12);

            $this->usr_address = $rs->getString($startcol + 13);

            $this->usr_phone = $rs->getString($startcol + 14);

            $this->usr_fax = $rs->getString($startcol + 15);

            $this->usr_cellular = $rs->getString($startcol + 16);

            $this->usr_zip_code = $rs->getString($startcol + 17);

            $this->dep_uid = $rs->getString($startcol + 18);

            $this->usr_position = $rs->getString($startcol + 19);

            $this->usr_resume = $rs->getString($startcol + 20);

            $this->usr_birthday = $rs->getDate($startcol + 21, null);

            $this->usr_role = $rs->getString($startcol + 22);

            $this->usr_reports_to = $rs->getString($startcol + 23);

            $this->usr_replaced_by = $rs->getString($startcol + 24);

            $this->usr_ux = $rs->getString($startcol + 25);

            $this->resetModified();

            $this->setNew(false);

            // FIXME - using NUM_COLUMNS may be clearer.
            return $startcol + 26; // 26 = UsersPeer::NUM_COLUMNS - UsersPeer::NUM_LAZY_LOAD_COLUMNS).

        } catch (Exception $e) {
            throw new PropelException("Error populating Users object", $e);
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
            $con = Propel::getConnection(UsersPeer::DATABASE_NAME);
        }

        try {
            $con->begin();
            UsersPeer::doDelete($this, $con);
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
            $con = Propel::getConnection(UsersPeer::DATABASE_NAME);
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
                    $pk = UsersPeer::doInsert($this, $con);
                    $affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
                                         // should always be true here (even though technically
                                         // BasePeer::doInsert() can insert multiple rows).

                    $this->setNew(false);
                } else {
                    $affectedRows += UsersPeer::doUpdate($this, $con);
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


            if (($retval = UsersPeer::doValidate($this, $columns)) !== true) {
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
        $pos = UsersPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                return $this->getUsrUid();
                break;
            case 1:
                return $this->getUsrUsername();
                break;
            case 2:
                return $this->getUsrPassword();
                break;
            case 3:
                return $this->getUsrFirstname();
                break;
            case 4:
                return $this->getUsrLastname();
                break;
            case 5:
                return $this->getUsrEmail();
                break;
            case 6:
                return $this->getUsrDueDate();
                break;
            case 7:
                return $this->getUsrCreateDate();
                break;
            case 8:
                return $this->getUsrUpdateDate();
                break;
            case 9:
                return $this->getUsrStatus();
                break;
            case 10:
                return $this->getUsrCountry();
                break;
            case 11:
                return $this->getUsrCity();
                break;
            case 12:
                return $this->getUsrLocation();
                break;
            case 13:
                return $this->getUsrAddress();
                break;
            case 14:
                return $this->getUsrPhone();
                break;
            case 15:
                return $this->getUsrFax();
                break;
            case 16:
                return $this->getUsrCellular();
                break;
            case 17:
                return $this->getUsrZipCode();
                break;
            case 18:
                return $this->getDepUid();
                break;
            case 19:
                return $this->getUsrPosition();
                break;
            case 20:
                return $this->getUsrResume();
                break;
            case 21:
                return $this->getUsrBirthday();
                break;
            case 22:
                return $this->getUsrRole();
                break;
            case 23:
                return $this->getUsrReportsTo();
                break;
            case 24:
                return $this->getUsrReplacedBy();
                break;
            case 25:
                return $this->getUsrUx();
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
        $keys = UsersPeer::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getUsrUid(),
            $keys[1] => $this->getUsrUsername(),
            $keys[2] => $this->getUsrPassword(),
            $keys[3] => $this->getUsrFirstname(),
            $keys[4] => $this->getUsrLastname(),
            $keys[5] => $this->getUsrEmail(),
            $keys[6] => $this->getUsrDueDate(),
            $keys[7] => $this->getUsrCreateDate(),
            $keys[8] => $this->getUsrUpdateDate(),
            $keys[9] => $this->getUsrStatus(),
            $keys[10] => $this->getUsrCountry(),
            $keys[11] => $this->getUsrCity(),
            $keys[12] => $this->getUsrLocation(),
            $keys[13] => $this->getUsrAddress(),
            $keys[14] => $this->getUsrPhone(),
            $keys[15] => $this->getUsrFax(),
            $keys[16] => $this->getUsrCellular(),
            $keys[17] => $this->getUsrZipCode(),
            $keys[18] => $this->getDepUid(),
            $keys[19] => $this->getUsrPosition(),
            $keys[20] => $this->getUsrResume(),
            $keys[21] => $this->getUsrBirthday(),
            $keys[22] => $this->getUsrRole(),
            $keys[23] => $this->getUsrReportsTo(),
            $keys[24] => $this->getUsrReplacedBy(),
            $keys[25] => $this->getUsrUx(),
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
        $pos = UsersPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                $this->setUsrUid($value);
                break;
            case 1:
                $this->setUsrUsername($value);
                break;
            case 2:
                $this->setUsrPassword($value);
                break;
            case 3:
                $this->setUsrFirstname($value);
                break;
            case 4:
                $this->setUsrLastname($value);
                break;
            case 5:
                $this->setUsrEmail($value);
                break;
            case 6:
                $this->setUsrDueDate($value);
                break;
            case 7:
                $this->setUsrCreateDate($value);
                break;
            case 8:
                $this->setUsrUpdateDate($value);
                break;
            case 9:
                $this->setUsrStatus($value);
                break;
            case 10:
                $this->setUsrCountry($value);
                break;
            case 11:
                $this->setUsrCity($value);
                break;
            case 12:
                $this->setUsrLocation($value);
                break;
            case 13:
                $this->setUsrAddress($value);
                break;
            case 14:
                $this->setUsrPhone($value);
                break;
            case 15:
                $this->setUsrFax($value);
                break;
            case 16:
                $this->setUsrCellular($value);
                break;
            case 17:
                $this->setUsrZipCode($value);
                break;
            case 18:
                $this->setDepUid($value);
                break;
            case 19:
                $this->setUsrPosition($value);
                break;
            case 20:
                $this->setUsrResume($value);
                break;
            case 21:
                $this->setUsrBirthday($value);
                break;
            case 22:
                $this->setUsrRole($value);
                break;
            case 23:
                $this->setUsrReportsTo($value);
                break;
            case 24:
                $this->setUsrReplacedBy($value);
                break;
            case 25:
                $this->setUsrUx($value);
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
        $keys = UsersPeer::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) {
            $this->setUsrUid($arr[$keys[0]]);
        }

        if (array_key_exists($keys[1], $arr)) {
            $this->setUsrUsername($arr[$keys[1]]);
        }

        if (array_key_exists($keys[2], $arr)) {
            $this->setUsrPassword($arr[$keys[2]]);
        }

        if (array_key_exists($keys[3], $arr)) {
            $this->setUsrFirstname($arr[$keys[3]]);
        }

        if (array_key_exists($keys[4], $arr)) {
            $this->setUsrLastname($arr[$keys[4]]);
        }

        if (array_key_exists($keys[5], $arr)) {
            $this->setUsrEmail($arr[$keys[5]]);
        }

        if (array_key_exists($keys[6], $arr)) {
            $this->setUsrDueDate($arr[$keys[6]]);
        }

        if (array_key_exists($keys[7], $arr)) {
            $this->setUsrCreateDate($arr[$keys[7]]);
        }

        if (array_key_exists($keys[8], $arr)) {
            $this->setUsrUpdateDate($arr[$keys[8]]);
        }

        if (array_key_exists($keys[9], $arr)) {
            $this->setUsrStatus($arr[$keys[9]]);
        }

        if (array_key_exists($keys[10], $arr)) {
            $this->setUsrCountry($arr[$keys[10]]);
        }

        if (array_key_exists($keys[11], $arr)) {
            $this->setUsrCity($arr[$keys[11]]);
        }

        if (array_key_exists($keys[12], $arr)) {
            $this->setUsrLocation($arr[$keys[12]]);
        }

        if (array_key_exists($keys[13], $arr)) {
            $this->setUsrAddress($arr[$keys[13]]);
        }

        if (array_key_exists($keys[14], $arr)) {
            $this->setUsrPhone($arr[$keys[14]]);
        }

        if (array_key_exists($keys[15], $arr)) {
            $this->setUsrFax($arr[$keys[15]]);
        }

        if (array_key_exists($keys[16], $arr)) {
            $this->setUsrCellular($arr[$keys[16]]);
        }

        if (array_key_exists($keys[17], $arr)) {
            $this->setUsrZipCode($arr[$keys[17]]);
        }

        if (array_key_exists($keys[18], $arr)) {
            $this->setDepUid($arr[$keys[18]]);
        }

        if (array_key_exists($keys[19], $arr)) {
            $this->setUsrPosition($arr[$keys[19]]);
        }

        if (array_key_exists($keys[20], $arr)) {
            $this->setUsrResume($arr[$keys[20]]);
        }

        if (array_key_exists($keys[21], $arr)) {
            $this->setUsrBirthday($arr[$keys[21]]);
        }

        if (array_key_exists($keys[22], $arr)) {
            $this->setUsrRole($arr[$keys[22]]);
        }

        if (array_key_exists($keys[23], $arr)) {
            $this->setUsrReportsTo($arr[$keys[23]]);
        }

        if (array_key_exists($keys[24], $arr)) {
            $this->setUsrReplacedBy($arr[$keys[24]]);
        }

        if (array_key_exists($keys[25], $arr)) {
            $this->setUsrUx($arr[$keys[25]]);
        }

    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return     Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(UsersPeer::DATABASE_NAME);

        if ($this->isColumnModified(UsersPeer::USR_UID)) {
            $criteria->add(UsersPeer::USR_UID, $this->usr_uid);
        }

        if ($this->isColumnModified(UsersPeer::USR_USERNAME)) {
            $criteria->add(UsersPeer::USR_USERNAME, $this->usr_username);
        }

        if ($this->isColumnModified(UsersPeer::USR_PASSWORD)) {
            $criteria->add(UsersPeer::USR_PASSWORD, $this->usr_password);
        }

        if ($this->isColumnModified(UsersPeer::USR_FIRSTNAME)) {
            $criteria->add(UsersPeer::USR_FIRSTNAME, $this->usr_firstname);
        }

        if ($this->isColumnModified(UsersPeer::USR_LASTNAME)) {
            $criteria->add(UsersPeer::USR_LASTNAME, $this->usr_lastname);
        }

        if ($this->isColumnModified(UsersPeer::USR_EMAIL)) {
            $criteria->add(UsersPeer::USR_EMAIL, $this->usr_email);
        }

        if ($this->isColumnModified(UsersPeer::USR_DUE_DATE)) {
            $criteria->add(UsersPeer::USR_DUE_DATE, $this->usr_due_date);
        }

        if ($this->isColumnModified(UsersPeer::USR_CREATE_DATE)) {
            $criteria->add(UsersPeer::USR_CREATE_DATE, $this->usr_create_date);
        }

        if ($this->isColumnModified(UsersPeer::USR_UPDATE_DATE)) {
            $criteria->add(UsersPeer::USR_UPDATE_DATE, $this->usr_update_date);
        }

        if ($this->isColumnModified(UsersPeer::USR_STATUS)) {
            $criteria->add(UsersPeer::USR_STATUS, $this->usr_status);
        }

        if ($this->isColumnModified(UsersPeer::USR_COUNTRY)) {
            $criteria->add(UsersPeer::USR_COUNTRY, $this->usr_country);
        }

        if ($this->isColumnModified(UsersPeer::USR_CITY)) {
            $criteria->add(UsersPeer::USR_CITY, $this->usr_city);
        }

        if ($this->isColumnModified(UsersPeer::USR_LOCATION)) {
            $criteria->add(UsersPeer::USR_LOCATION, $this->usr_location);
        }

        if ($this->isColumnModified(UsersPeer::USR_ADDRESS)) {
            $criteria->add(UsersPeer::USR_ADDRESS, $this->usr_address);
        }

        if ($this->isColumnModified(UsersPeer::USR_PHONE)) {
            $criteria->add(UsersPeer::USR_PHONE, $this->usr_phone);
        }

        if ($this->isColumnModified(UsersPeer::USR_FAX)) {
            $criteria->add(UsersPeer::USR_FAX, $this->usr_fax);
        }

        if ($this->isColumnModified(UsersPeer::USR_CELLULAR)) {
            $criteria->add(UsersPeer::USR_CELLULAR, $this->usr_cellular);
        }

        if ($this->isColumnModified(UsersPeer::USR_ZIP_CODE)) {
            $criteria->add(UsersPeer::USR_ZIP_CODE, $this->usr_zip_code);
        }

        if ($this->isColumnModified(UsersPeer::DEP_UID)) {
            $criteria->add(UsersPeer::DEP_UID, $this->dep_uid);
        }

        if ($this->isColumnModified(UsersPeer::USR_POSITION)) {
            $criteria->add(UsersPeer::USR_POSITION, $this->usr_position);
        }

        if ($this->isColumnModified(UsersPeer::USR_RESUME)) {
            $criteria->add(UsersPeer::USR_RESUME, $this->usr_resume);
        }

        if ($this->isColumnModified(UsersPeer::USR_BIRTHDAY)) {
            $criteria->add(UsersPeer::USR_BIRTHDAY, $this->usr_birthday);
        }

        if ($this->isColumnModified(UsersPeer::USR_ROLE)) {
            $criteria->add(UsersPeer::USR_ROLE, $this->usr_role);
        }

        if ($this->isColumnModified(UsersPeer::USR_REPORTS_TO)) {
            $criteria->add(UsersPeer::USR_REPORTS_TO, $this->usr_reports_to);
        }

        if ($this->isColumnModified(UsersPeer::USR_REPLACED_BY)) {
            $criteria->add(UsersPeer::USR_REPLACED_BY, $this->usr_replaced_by);
        }

        if ($this->isColumnModified(UsersPeer::USR_UX)) {
            $criteria->add(UsersPeer::USR_UX, $this->usr_ux);
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
        $criteria = new Criteria(UsersPeer::DATABASE_NAME);

        $criteria->add(UsersPeer::USR_UID, $this->usr_uid);

        return $criteria;
    }

    /**
     * Returns the primary key for this object (row).
     * @return     string
     */
    public function getPrimaryKey()
    {
        return $this->getUsrUid();
    }

    /**
     * Generic method to set the primary key (usr_uid column).
     *
     * @param      string $key Primary key.
     * @return     void
     */
    public function setPrimaryKey($key)
    {
        $this->setUsrUid($key);
    }

    /**
     * Sets contents of passed object to values from current object.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param      object $copyObj An object of Users (or compatible) type.
     * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @throws     PropelException
     */
    public function copyInto($copyObj, $deepCopy = false)
    {

        $copyObj->setUsrUsername($this->usr_username);

        $copyObj->setUsrPassword($this->usr_password);

        $copyObj->setUsrFirstname($this->usr_firstname);

        $copyObj->setUsrLastname($this->usr_lastname);

        $copyObj->setUsrEmail($this->usr_email);

        $copyObj->setUsrDueDate($this->usr_due_date);

        $copyObj->setUsrCreateDate($this->usr_create_date);

        $copyObj->setUsrUpdateDate($this->usr_update_date);

        $copyObj->setUsrStatus($this->usr_status);

        $copyObj->setUsrCountry($this->usr_country);

        $copyObj->setUsrCity($this->usr_city);

        $copyObj->setUsrLocation($this->usr_location);

        $copyObj->setUsrAddress($this->usr_address);

        $copyObj->setUsrPhone($this->usr_phone);

        $copyObj->setUsrFax($this->usr_fax);

        $copyObj->setUsrCellular($this->usr_cellular);

        $copyObj->setUsrZipCode($this->usr_zip_code);

        $copyObj->setDepUid($this->dep_uid);

        $copyObj->setUsrPosition($this->usr_position);

        $copyObj->setUsrResume($this->usr_resume);

        $copyObj->setUsrBirthday($this->usr_birthday);

        $copyObj->setUsrRole($this->usr_role);

        $copyObj->setUsrReportsTo($this->usr_reports_to);

        $copyObj->setUsrReplacedBy($this->usr_replaced_by);

        $copyObj->setUsrUx($this->usr_ux);


        $copyObj->setNew(true);

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
     * @return     Users Clone of current object.
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
     * @return     UsersPeer
     */
    public function getPeer()
    {
        if (self::$peer === null) {
            self::$peer = new UsersPeer();
        }
        return self::$peer;
    }
}

