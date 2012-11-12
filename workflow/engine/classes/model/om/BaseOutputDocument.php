<?php

require_once 'propel/om/BaseObject.php';

require_once 'propel/om/Persistent.php';


include_once 'propel/util/Criteria.php';

include_once 'classes/model/OutputDocumentPeer.php';

/**
 * Base class that represents a row from the 'OUTPUT_DOCUMENT' table.
 *
 *
 *
 * @package    workflow.classes.model.om
 */
abstract class BaseOutputDocument extends BaseObject implements Persistent
{

    /**
     * The Peer class.
     * Instance provides a convenient way of calling static methods on a class
     * that calling code may not be able to identify.
     * @var        OutputDocumentPeer
    */
    protected static $peer;

    /**
     * The value for the out_doc_uid field.
     * @var        string
     */
    protected $out_doc_uid = '';

    /**
     * The value for the pro_uid field.
     * @var        string
     */
    protected $pro_uid = '';

    /**
     * The value for the out_doc_report_generator field.
     * @var        string
     */
    protected $out_doc_report_generator = 'HTML2PDF';

    /**
     * The value for the out_doc_landscape field.
     * @var        int
     */
    protected $out_doc_landscape = 0;

    /**
     * The value for the out_doc_media field.
     * @var        string
     */
    protected $out_doc_media = 'Letter';

    /**
     * The value for the out_doc_left_margin field.
     * @var        int
     */
    protected $out_doc_left_margin = 30;

    /**
     * The value for the out_doc_right_margin field.
     * @var        int
     */
    protected $out_doc_right_margin = 15;

    /**
     * The value for the out_doc_top_margin field.
     * @var        int
     */
    protected $out_doc_top_margin = 15;

    /**
     * The value for the out_doc_bottom_margin field.
     * @var        int
     */
    protected $out_doc_bottom_margin = 15;

    /**
     * The value for the out_doc_generate field.
     * @var        string
     */
    protected $out_doc_generate = 'BOTH';

    /**
     * The value for the out_doc_type field.
     * @var        string
     */
    protected $out_doc_type = 'HTML';

    /**
     * The value for the out_doc_current_revision field.
     * @var        int
     */
    protected $out_doc_current_revision = 0;

    /**
     * The value for the out_doc_field_mapping field.
     * @var        string
     */
    protected $out_doc_field_mapping;

    /**
     * The value for the out_doc_versioning field.
     * @var        int
     */
    protected $out_doc_versioning = 0;

    /**
     * The value for the out_doc_destination_path field.
     * @var        string
     */
    protected $out_doc_destination_path;

    /**
     * The value for the out_doc_tags field.
     * @var        string
     */
    protected $out_doc_tags;

    /**
     * The value for the out_doc_pdf_security_enabled field.
     * @var        int
     */
    protected $out_doc_pdf_security_enabled = 0;

    /**
     * The value for the out_doc_pdf_security_open_password field.
     * @var        string
     */
    protected $out_doc_pdf_security_open_password = '';

    /**
     * The value for the out_doc_pdf_security_owner_password field.
     * @var        string
     */
    protected $out_doc_pdf_security_owner_password = '';

    /**
     * The value for the out_doc_pdf_security_permissions field.
     * @var        string
     */
    protected $out_doc_pdf_security_permissions = '';

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
     * Get the [out_doc_uid] column value.
     *
     * @return     string
     */
    public function getOutDocUid()
    {

        return $this->out_doc_uid;
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
     * Get the [out_doc_report_generator] column value.
     *
     * @return     string
     */
    public function getOutDocReportGenerator()
    {

        return $this->out_doc_report_generator;
    }

    /**
     * Get the [out_doc_landscape] column value.
     *
     * @return     int
     */
    public function getOutDocLandscape()
    {

        return $this->out_doc_landscape;
    }

    /**
     * Get the [out_doc_media] column value.
     *
     * @return     string
     */
    public function getOutDocMedia()
    {

        return $this->out_doc_media;
    }

    /**
     * Get the [out_doc_left_margin] column value.
     *
     * @return     int
     */
    public function getOutDocLeftMargin()
    {

        return $this->out_doc_left_margin;
    }

    /**
     * Get the [out_doc_right_margin] column value.
     *
     * @return     int
     */
    public function getOutDocRightMargin()
    {

        return $this->out_doc_right_margin;
    }

    /**
     * Get the [out_doc_top_margin] column value.
     *
     * @return     int
     */
    public function getOutDocTopMargin()
    {

        return $this->out_doc_top_margin;
    }

    /**
     * Get the [out_doc_bottom_margin] column value.
     *
     * @return     int
     */
    public function getOutDocBottomMargin()
    {

        return $this->out_doc_bottom_margin;
    }

    /**
     * Get the [out_doc_generate] column value.
     *
     * @return     string
     */
    public function getOutDocGenerate()
    {

        return $this->out_doc_generate;
    }

    /**
     * Get the [out_doc_type] column value.
     *
     * @return     string
     */
    public function getOutDocType()
    {

        return $this->out_doc_type;
    }

    /**
     * Get the [out_doc_current_revision] column value.
     *
     * @return     int
     */
    public function getOutDocCurrentRevision()
    {

        return $this->out_doc_current_revision;
    }

    /**
     * Get the [out_doc_field_mapping] column value.
     *
     * @return     string
     */
    public function getOutDocFieldMapping()
    {

        return $this->out_doc_field_mapping;
    }

    /**
     * Get the [out_doc_versioning] column value.
     *
     * @return     int
     */
    public function getOutDocVersioning()
    {

        return $this->out_doc_versioning;
    }

    /**
     * Get the [out_doc_destination_path] column value.
     *
     * @return     string
     */
    public function getOutDocDestinationPath()
    {

        return $this->out_doc_destination_path;
    }

    /**
     * Get the [out_doc_tags] column value.
     *
     * @return     string
     */
    public function getOutDocTags()
    {

        return $this->out_doc_tags;
    }

    /**
     * Get the [out_doc_pdf_security_enabled] column value.
     *
     * @return     int
     */
    public function getOutDocPdfSecurityEnabled()
    {

        return $this->out_doc_pdf_security_enabled;
    }

    /**
     * Get the [out_doc_pdf_security_open_password] column value.
     *
     * @return     string
     */
    public function getOutDocPdfSecurityOpenPassword()
    {

        return $this->out_doc_pdf_security_open_password;
    }

    /**
     * Get the [out_doc_pdf_security_owner_password] column value.
     *
     * @return     string
     */
    public function getOutDocPdfSecurityOwnerPassword()
    {

        return $this->out_doc_pdf_security_owner_password;
    }

    /**
     * Get the [out_doc_pdf_security_permissions] column value.
     *
     * @return     string
     */
    public function getOutDocPdfSecurityPermissions()
    {

        return $this->out_doc_pdf_security_permissions;
    }

    /**
     * Set the value of [out_doc_uid] column.
     *
     * @param      string $v new value
     * @return     void
     */
    public function setOutDocUid($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->out_doc_uid !== $v || $v === '') {
            $this->out_doc_uid = $v;
            $this->modifiedColumns[] = OutputDocumentPeer::OUT_DOC_UID;
        }

    } // setOutDocUid()

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
            $this->modifiedColumns[] = OutputDocumentPeer::PRO_UID;
        }

    } // setProUid()

    /**
     * Set the value of [out_doc_report_generator] column.
     *
     * @param      string $v new value
     * @return     void
     */
    public function setOutDocReportGenerator($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->out_doc_report_generator !== $v || $v === 'HTML2PDF') {
            $this->out_doc_report_generator = $v;
            $this->modifiedColumns[] = OutputDocumentPeer::OUT_DOC_REPORT_GENERATOR;
        }

    } // setOutDocReportGenerator()

    /**
     * Set the value of [out_doc_landscape] column.
     *
     * @param      int $v new value
     * @return     void
     */
    public function setOutDocLandscape($v)
    {

        // Since the native PHP type for this column is integer,
        // we will cast the input value to an int (if it is not).
        if ($v !== null && !is_int($v) && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->out_doc_landscape !== $v || $v === 0) {
            $this->out_doc_landscape = $v;
            $this->modifiedColumns[] = OutputDocumentPeer::OUT_DOC_LANDSCAPE;
        }

    } // setOutDocLandscape()

    /**
     * Set the value of [out_doc_media] column.
     *
     * @param      string $v new value
     * @return     void
     */
    public function setOutDocMedia($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->out_doc_media !== $v || $v === 'Letter') {
            $this->out_doc_media = $v;
            $this->modifiedColumns[] = OutputDocumentPeer::OUT_DOC_MEDIA;
        }

    } // setOutDocMedia()

    /**
     * Set the value of [out_doc_left_margin] column.
     *
     * @param      int $v new value
     * @return     void
     */
    public function setOutDocLeftMargin($v)
    {

        // Since the native PHP type for this column is integer,
        // we will cast the input value to an int (if it is not).
        if ($v !== null && !is_int($v) && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->out_doc_left_margin !== $v || $v === 30) {
            $this->out_doc_left_margin = $v;
            $this->modifiedColumns[] = OutputDocumentPeer::OUT_DOC_LEFT_MARGIN;
        }

    } // setOutDocLeftMargin()

    /**
     * Set the value of [out_doc_right_margin] column.
     *
     * @param      int $v new value
     * @return     void
     */
    public function setOutDocRightMargin($v)
    {

        // Since the native PHP type for this column is integer,
        // we will cast the input value to an int (if it is not).
        if ($v !== null && !is_int($v) && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->out_doc_right_margin !== $v || $v === 15) {
            $this->out_doc_right_margin = $v;
            $this->modifiedColumns[] = OutputDocumentPeer::OUT_DOC_RIGHT_MARGIN;
        }

    } // setOutDocRightMargin()

    /**
     * Set the value of [out_doc_top_margin] column.
     *
     * @param      int $v new value
     * @return     void
     */
    public function setOutDocTopMargin($v)
    {

        // Since the native PHP type for this column is integer,
        // we will cast the input value to an int (if it is not).
        if ($v !== null && !is_int($v) && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->out_doc_top_margin !== $v || $v === 15) {
            $this->out_doc_top_margin = $v;
            $this->modifiedColumns[] = OutputDocumentPeer::OUT_DOC_TOP_MARGIN;
        }

    } // setOutDocTopMargin()

    /**
     * Set the value of [out_doc_bottom_margin] column.
     *
     * @param      int $v new value
     * @return     void
     */
    public function setOutDocBottomMargin($v)
    {

        // Since the native PHP type for this column is integer,
        // we will cast the input value to an int (if it is not).
        if ($v !== null && !is_int($v) && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->out_doc_bottom_margin !== $v || $v === 15) {
            $this->out_doc_bottom_margin = $v;
            $this->modifiedColumns[] = OutputDocumentPeer::OUT_DOC_BOTTOM_MARGIN;
        }

    } // setOutDocBottomMargin()

    /**
     * Set the value of [out_doc_generate] column.
     *
     * @param      string $v new value
     * @return     void
     */
    public function setOutDocGenerate($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->out_doc_generate !== $v || $v === 'BOTH') {
            $this->out_doc_generate = $v;
            $this->modifiedColumns[] = OutputDocumentPeer::OUT_DOC_GENERATE;
        }

    } // setOutDocGenerate()

    /**
     * Set the value of [out_doc_type] column.
     *
     * @param      string $v new value
     * @return     void
     */
    public function setOutDocType($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->out_doc_type !== $v || $v === 'HTML') {
            $this->out_doc_type = $v;
            $this->modifiedColumns[] = OutputDocumentPeer::OUT_DOC_TYPE;
        }

    } // setOutDocType()

    /**
     * Set the value of [out_doc_current_revision] column.
     *
     * @param      int $v new value
     * @return     void
     */
    public function setOutDocCurrentRevision($v)
    {

        // Since the native PHP type for this column is integer,
        // we will cast the input value to an int (if it is not).
        if ($v !== null && !is_int($v) && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->out_doc_current_revision !== $v || $v === 0) {
            $this->out_doc_current_revision = $v;
            $this->modifiedColumns[] = OutputDocumentPeer::OUT_DOC_CURRENT_REVISION;
        }

    } // setOutDocCurrentRevision()

    /**
     * Set the value of [out_doc_field_mapping] column.
     *
     * @param      string $v new value
     * @return     void
     */
    public function setOutDocFieldMapping($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->out_doc_field_mapping !== $v) {
            $this->out_doc_field_mapping = $v;
            $this->modifiedColumns[] = OutputDocumentPeer::OUT_DOC_FIELD_MAPPING;
        }

    } // setOutDocFieldMapping()

    /**
     * Set the value of [out_doc_versioning] column.
     *
     * @param      int $v new value
     * @return     void
     */
    public function setOutDocVersioning($v)
    {

        // Since the native PHP type for this column is integer,
        // we will cast the input value to an int (if it is not).
        if ($v !== null && !is_int($v) && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->out_doc_versioning !== $v || $v === 0) {
            $this->out_doc_versioning = $v;
            $this->modifiedColumns[] = OutputDocumentPeer::OUT_DOC_VERSIONING;
        }

    } // setOutDocVersioning()

    /**
     * Set the value of [out_doc_destination_path] column.
     *
     * @param      string $v new value
     * @return     void
     */
    public function setOutDocDestinationPath($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->out_doc_destination_path !== $v) {
            $this->out_doc_destination_path = $v;
            $this->modifiedColumns[] = OutputDocumentPeer::OUT_DOC_DESTINATION_PATH;
        }

    } // setOutDocDestinationPath()

    /**
     * Set the value of [out_doc_tags] column.
     *
     * @param      string $v new value
     * @return     void
     */
    public function setOutDocTags($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->out_doc_tags !== $v) {
            $this->out_doc_tags = $v;
            $this->modifiedColumns[] = OutputDocumentPeer::OUT_DOC_TAGS;
        }

    } // setOutDocTags()

    /**
     * Set the value of [out_doc_pdf_security_enabled] column.
     *
     * @param      int $v new value
     * @return     void
     */
    public function setOutDocPdfSecurityEnabled($v)
    {

        // Since the native PHP type for this column is integer,
        // we will cast the input value to an int (if it is not).
        if ($v !== null && !is_int($v) && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->out_doc_pdf_security_enabled !== $v || $v === 0) {
            $this->out_doc_pdf_security_enabled = $v;
            $this->modifiedColumns[] = OutputDocumentPeer::OUT_DOC_PDF_SECURITY_ENABLED;
        }

    } // setOutDocPdfSecurityEnabled()

    /**
     * Set the value of [out_doc_pdf_security_open_password] column.
     *
     * @param      string $v new value
     * @return     void
     */
    public function setOutDocPdfSecurityOpenPassword($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->out_doc_pdf_security_open_password !== $v || $v === '') {
            $this->out_doc_pdf_security_open_password = $v;
            $this->modifiedColumns[] = OutputDocumentPeer::OUT_DOC_PDF_SECURITY_OPEN_PASSWORD;
        }

    } // setOutDocPdfSecurityOpenPassword()

    /**
     * Set the value of [out_doc_pdf_security_owner_password] column.
     *
     * @param      string $v new value
     * @return     void
     */
    public function setOutDocPdfSecurityOwnerPassword($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->out_doc_pdf_security_owner_password !== $v || $v === '') {
            $this->out_doc_pdf_security_owner_password = $v;
            $this->modifiedColumns[] = OutputDocumentPeer::OUT_DOC_PDF_SECURITY_OWNER_PASSWORD;
        }

    } // setOutDocPdfSecurityOwnerPassword()

    /**
     * Set the value of [out_doc_pdf_security_permissions] column.
     *
     * @param      string $v new value
     * @return     void
     */
    public function setOutDocPdfSecurityPermissions($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->out_doc_pdf_security_permissions !== $v || $v === '') {
            $this->out_doc_pdf_security_permissions = $v;
            $this->modifiedColumns[] = OutputDocumentPeer::OUT_DOC_PDF_SECURITY_PERMISSIONS;
        }

    } // setOutDocPdfSecurityPermissions()

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

            $this->out_doc_uid = $rs->getString($startcol + 0);

            $this->pro_uid = $rs->getString($startcol + 1);

            $this->out_doc_report_generator = $rs->getString($startcol + 2);

            $this->out_doc_landscape = $rs->getInt($startcol + 3);

            $this->out_doc_media = $rs->getString($startcol + 4);

            $this->out_doc_left_margin = $rs->getInt($startcol + 5);

            $this->out_doc_right_margin = $rs->getInt($startcol + 6);

            $this->out_doc_top_margin = $rs->getInt($startcol + 7);

            $this->out_doc_bottom_margin = $rs->getInt($startcol + 8);

            $this->out_doc_generate = $rs->getString($startcol + 9);

            $this->out_doc_type = $rs->getString($startcol + 10);

            $this->out_doc_current_revision = $rs->getInt($startcol + 11);

            $this->out_doc_field_mapping = $rs->getString($startcol + 12);

            $this->out_doc_versioning = $rs->getInt($startcol + 13);

            $this->out_doc_destination_path = $rs->getString($startcol + 14);

            $this->out_doc_tags = $rs->getString($startcol + 15);

            $this->out_doc_pdf_security_enabled = $rs->getInt($startcol + 16);

            $this->out_doc_pdf_security_open_password = $rs->getString($startcol + 17);

            $this->out_doc_pdf_security_owner_password = $rs->getString($startcol + 18);

            $this->out_doc_pdf_security_permissions = $rs->getString($startcol + 19);

            $this->resetModified();

            $this->setNew(false);

            // FIXME - using NUM_COLUMNS may be clearer.
            return $startcol + 20; // 20 = OutputDocumentPeer::NUM_COLUMNS - OutputDocumentPeer::NUM_LAZY_LOAD_COLUMNS).

        } catch (Exception $e) {
            throw new PropelException("Error populating OutputDocument object", $e);
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
            $con = Propel::getConnection(OutputDocumentPeer::DATABASE_NAME);
        }

        try {
            $con->begin();
            OutputDocumentPeer::doDelete($this, $con);
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
            $con = Propel::getConnection(OutputDocumentPeer::DATABASE_NAME);
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
                    $pk = OutputDocumentPeer::doInsert($this, $con);
                    $affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
                                         // should always be true here (even though technically
                                         // BasePeer::doInsert() can insert multiple rows).

                    $this->setNew(false);
                } else {
                    $affectedRows += OutputDocumentPeer::doUpdate($this, $con);
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


            if (($retval = OutputDocumentPeer::doValidate($this, $columns)) !== true) {
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
        $pos = OutputDocumentPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                return $this->getOutDocUid();
                break;
            case 1:
                return $this->getProUid();
                break;
            case 2:
                return $this->getOutDocReportGenerator();
                break;
            case 3:
                return $this->getOutDocLandscape();
                break;
            case 4:
                return $this->getOutDocMedia();
                break;
            case 5:
                return $this->getOutDocLeftMargin();
                break;
            case 6:
                return $this->getOutDocRightMargin();
                break;
            case 7:
                return $this->getOutDocTopMargin();
                break;
            case 8:
                return $this->getOutDocBottomMargin();
                break;
            case 9:
                return $this->getOutDocGenerate();
                break;
            case 10:
                return $this->getOutDocType();
                break;
            case 11:
                return $this->getOutDocCurrentRevision();
                break;
            case 12:
                return $this->getOutDocFieldMapping();
                break;
            case 13:
                return $this->getOutDocVersioning();
                break;
            case 14:
                return $this->getOutDocDestinationPath();
                break;
            case 15:
                return $this->getOutDocTags();
                break;
            case 16:
                return $this->getOutDocPdfSecurityEnabled();
                break;
            case 17:
                return $this->getOutDocPdfSecurityOpenPassword();
                break;
            case 18:
                return $this->getOutDocPdfSecurityOwnerPassword();
                break;
            case 19:
                return $this->getOutDocPdfSecurityPermissions();
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
        $keys = OutputDocumentPeer::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getOutDocUid(),
            $keys[1] => $this->getProUid(),
            $keys[2] => $this->getOutDocReportGenerator(),
            $keys[3] => $this->getOutDocLandscape(),
            $keys[4] => $this->getOutDocMedia(),
            $keys[5] => $this->getOutDocLeftMargin(),
            $keys[6] => $this->getOutDocRightMargin(),
            $keys[7] => $this->getOutDocTopMargin(),
            $keys[8] => $this->getOutDocBottomMargin(),
            $keys[9] => $this->getOutDocGenerate(),
            $keys[10] => $this->getOutDocType(),
            $keys[11] => $this->getOutDocCurrentRevision(),
            $keys[12] => $this->getOutDocFieldMapping(),
            $keys[13] => $this->getOutDocVersioning(),
            $keys[14] => $this->getOutDocDestinationPath(),
            $keys[15] => $this->getOutDocTags(),
            $keys[16] => $this->getOutDocPdfSecurityEnabled(),
            $keys[17] => $this->getOutDocPdfSecurityOpenPassword(),
            $keys[18] => $this->getOutDocPdfSecurityOwnerPassword(),
            $keys[19] => $this->getOutDocPdfSecurityPermissions(),
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
        $pos = OutputDocumentPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                $this->setOutDocUid($value);
                break;
            case 1:
                $this->setProUid($value);
                break;
            case 2:
                $this->setOutDocReportGenerator($value);
                break;
            case 3:
                $this->setOutDocLandscape($value);
                break;
            case 4:
                $this->setOutDocMedia($value);
                break;
            case 5:
                $this->setOutDocLeftMargin($value);
                break;
            case 6:
                $this->setOutDocRightMargin($value);
                break;
            case 7:
                $this->setOutDocTopMargin($value);
                break;
            case 8:
                $this->setOutDocBottomMargin($value);
                break;
            case 9:
                $this->setOutDocGenerate($value);
                break;
            case 10:
                $this->setOutDocType($value);
                break;
            case 11:
                $this->setOutDocCurrentRevision($value);
                break;
            case 12:
                $this->setOutDocFieldMapping($value);
                break;
            case 13:
                $this->setOutDocVersioning($value);
                break;
            case 14:
                $this->setOutDocDestinationPath($value);
                break;
            case 15:
                $this->setOutDocTags($value);
                break;
            case 16:
                $this->setOutDocPdfSecurityEnabled($value);
                break;
            case 17:
                $this->setOutDocPdfSecurityOpenPassword($value);
                break;
            case 18:
                $this->setOutDocPdfSecurityOwnerPassword($value);
                break;
            case 19:
                $this->setOutDocPdfSecurityPermissions($value);
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
        $keys = OutputDocumentPeer::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) {
            $this->setOutDocUid($arr[$keys[0]]);
        }

        if (array_key_exists($keys[1], $arr)) {
            $this->setProUid($arr[$keys[1]]);
        }

        if (array_key_exists($keys[2], $arr)) {
            $this->setOutDocReportGenerator($arr[$keys[2]]);
        }

        if (array_key_exists($keys[3], $arr)) {
            $this->setOutDocLandscape($arr[$keys[3]]);
        }

        if (array_key_exists($keys[4], $arr)) {
            $this->setOutDocMedia($arr[$keys[4]]);
        }

        if (array_key_exists($keys[5], $arr)) {
            $this->setOutDocLeftMargin($arr[$keys[5]]);
        }

        if (array_key_exists($keys[6], $arr)) {
            $this->setOutDocRightMargin($arr[$keys[6]]);
        }

        if (array_key_exists($keys[7], $arr)) {
            $this->setOutDocTopMargin($arr[$keys[7]]);
        }

        if (array_key_exists($keys[8], $arr)) {
            $this->setOutDocBottomMargin($arr[$keys[8]]);
        }

        if (array_key_exists($keys[9], $arr)) {
            $this->setOutDocGenerate($arr[$keys[9]]);
        }

        if (array_key_exists($keys[10], $arr)) {
            $this->setOutDocType($arr[$keys[10]]);
        }

        if (array_key_exists($keys[11], $arr)) {
            $this->setOutDocCurrentRevision($arr[$keys[11]]);
        }

        if (array_key_exists($keys[12], $arr)) {
            $this->setOutDocFieldMapping($arr[$keys[12]]);
        }

        if (array_key_exists($keys[13], $arr)) {
            $this->setOutDocVersioning($arr[$keys[13]]);
        }

        if (array_key_exists($keys[14], $arr)) {
            $this->setOutDocDestinationPath($arr[$keys[14]]);
        }

        if (array_key_exists($keys[15], $arr)) {
            $this->setOutDocTags($arr[$keys[15]]);
        }

        if (array_key_exists($keys[16], $arr)) {
            $this->setOutDocPdfSecurityEnabled($arr[$keys[16]]);
        }

        if (array_key_exists($keys[17], $arr)) {
            $this->setOutDocPdfSecurityOpenPassword($arr[$keys[17]]);
        }

        if (array_key_exists($keys[18], $arr)) {
            $this->setOutDocPdfSecurityOwnerPassword($arr[$keys[18]]);
        }

        if (array_key_exists($keys[19], $arr)) {
            $this->setOutDocPdfSecurityPermissions($arr[$keys[19]]);
        }

    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return     Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(OutputDocumentPeer::DATABASE_NAME);

        if ($this->isColumnModified(OutputDocumentPeer::OUT_DOC_UID)) {
            $criteria->add(OutputDocumentPeer::OUT_DOC_UID, $this->out_doc_uid);
        }

        if ($this->isColumnModified(OutputDocumentPeer::PRO_UID)) {
            $criteria->add(OutputDocumentPeer::PRO_UID, $this->pro_uid);
        }

        if ($this->isColumnModified(OutputDocumentPeer::OUT_DOC_REPORT_GENERATOR)) {
            $criteria->add(OutputDocumentPeer::OUT_DOC_REPORT_GENERATOR, $this->out_doc_report_generator);
        }

        if ($this->isColumnModified(OutputDocumentPeer::OUT_DOC_LANDSCAPE)) {
            $criteria->add(OutputDocumentPeer::OUT_DOC_LANDSCAPE, $this->out_doc_landscape);
        }

        if ($this->isColumnModified(OutputDocumentPeer::OUT_DOC_MEDIA)) {
            $criteria->add(OutputDocumentPeer::OUT_DOC_MEDIA, $this->out_doc_media);
        }

        if ($this->isColumnModified(OutputDocumentPeer::OUT_DOC_LEFT_MARGIN)) {
            $criteria->add(OutputDocumentPeer::OUT_DOC_LEFT_MARGIN, $this->out_doc_left_margin);
        }

        if ($this->isColumnModified(OutputDocumentPeer::OUT_DOC_RIGHT_MARGIN)) {
            $criteria->add(OutputDocumentPeer::OUT_DOC_RIGHT_MARGIN, $this->out_doc_right_margin);
        }

        if ($this->isColumnModified(OutputDocumentPeer::OUT_DOC_TOP_MARGIN)) {
            $criteria->add(OutputDocumentPeer::OUT_DOC_TOP_MARGIN, $this->out_doc_top_margin);
        }

        if ($this->isColumnModified(OutputDocumentPeer::OUT_DOC_BOTTOM_MARGIN)) {
            $criteria->add(OutputDocumentPeer::OUT_DOC_BOTTOM_MARGIN, $this->out_doc_bottom_margin);
        }

        if ($this->isColumnModified(OutputDocumentPeer::OUT_DOC_GENERATE)) {
            $criteria->add(OutputDocumentPeer::OUT_DOC_GENERATE, $this->out_doc_generate);
        }

        if ($this->isColumnModified(OutputDocumentPeer::OUT_DOC_TYPE)) {
            $criteria->add(OutputDocumentPeer::OUT_DOC_TYPE, $this->out_doc_type);
        }

        if ($this->isColumnModified(OutputDocumentPeer::OUT_DOC_CURRENT_REVISION)) {
            $criteria->add(OutputDocumentPeer::OUT_DOC_CURRENT_REVISION, $this->out_doc_current_revision);
        }

        if ($this->isColumnModified(OutputDocumentPeer::OUT_DOC_FIELD_MAPPING)) {
            $criteria->add(OutputDocumentPeer::OUT_DOC_FIELD_MAPPING, $this->out_doc_field_mapping);
        }

        if ($this->isColumnModified(OutputDocumentPeer::OUT_DOC_VERSIONING)) {
            $criteria->add(OutputDocumentPeer::OUT_DOC_VERSIONING, $this->out_doc_versioning);
        }

        if ($this->isColumnModified(OutputDocumentPeer::OUT_DOC_DESTINATION_PATH)) {
            $criteria->add(OutputDocumentPeer::OUT_DOC_DESTINATION_PATH, $this->out_doc_destination_path);
        }

        if ($this->isColumnModified(OutputDocumentPeer::OUT_DOC_TAGS)) {
            $criteria->add(OutputDocumentPeer::OUT_DOC_TAGS, $this->out_doc_tags);
        }

        if ($this->isColumnModified(OutputDocumentPeer::OUT_DOC_PDF_SECURITY_ENABLED)) {
            $criteria->add(OutputDocumentPeer::OUT_DOC_PDF_SECURITY_ENABLED, $this->out_doc_pdf_security_enabled);
        }

        if ($this->isColumnModified(OutputDocumentPeer::OUT_DOC_PDF_SECURITY_OPEN_PASSWORD)) {
            $criteria->add(OutputDocumentPeer::OUT_DOC_PDF_SECURITY_OPEN_PASSWORD, $this->out_doc_pdf_security_open_password);
        }

        if ($this->isColumnModified(OutputDocumentPeer::OUT_DOC_PDF_SECURITY_OWNER_PASSWORD)) {
            $criteria->add(OutputDocumentPeer::OUT_DOC_PDF_SECURITY_OWNER_PASSWORD, $this->out_doc_pdf_security_owner_password);
        }

        if ($this->isColumnModified(OutputDocumentPeer::OUT_DOC_PDF_SECURITY_PERMISSIONS)) {
            $criteria->add(OutputDocumentPeer::OUT_DOC_PDF_SECURITY_PERMISSIONS, $this->out_doc_pdf_security_permissions);
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
        $criteria = new Criteria(OutputDocumentPeer::DATABASE_NAME);

        $criteria->add(OutputDocumentPeer::OUT_DOC_UID, $this->out_doc_uid);

        return $criteria;
    }

    /**
     * Returns the primary key for this object (row).
     * @return     string
     */
    public function getPrimaryKey()
    {
        return $this->getOutDocUid();
    }

    /**
     * Generic method to set the primary key (out_doc_uid column).
     *
     * @param      string $key Primary key.
     * @return     void
     */
    public function setPrimaryKey($key)
    {
        $this->setOutDocUid($key);
    }

    /**
     * Sets contents of passed object to values from current object.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param      object $copyObj An object of OutputDocument (or compatible) type.
     * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @throws     PropelException
     */
    public function copyInto($copyObj, $deepCopy = false)
    {

        $copyObj->setProUid($this->pro_uid);

        $copyObj->setOutDocReportGenerator($this->out_doc_report_generator);

        $copyObj->setOutDocLandscape($this->out_doc_landscape);

        $copyObj->setOutDocMedia($this->out_doc_media);

        $copyObj->setOutDocLeftMargin($this->out_doc_left_margin);

        $copyObj->setOutDocRightMargin($this->out_doc_right_margin);

        $copyObj->setOutDocTopMargin($this->out_doc_top_margin);

        $copyObj->setOutDocBottomMargin($this->out_doc_bottom_margin);

        $copyObj->setOutDocGenerate($this->out_doc_generate);

        $copyObj->setOutDocType($this->out_doc_type);

        $copyObj->setOutDocCurrentRevision($this->out_doc_current_revision);

        $copyObj->setOutDocFieldMapping($this->out_doc_field_mapping);

        $copyObj->setOutDocVersioning($this->out_doc_versioning);

        $copyObj->setOutDocDestinationPath($this->out_doc_destination_path);

        $copyObj->setOutDocTags($this->out_doc_tags);

        $copyObj->setOutDocPdfSecurityEnabled($this->out_doc_pdf_security_enabled);

        $copyObj->setOutDocPdfSecurityOpenPassword($this->out_doc_pdf_security_open_password);

        $copyObj->setOutDocPdfSecurityOwnerPassword($this->out_doc_pdf_security_owner_password);

        $copyObj->setOutDocPdfSecurityPermissions($this->out_doc_pdf_security_permissions);


        $copyObj->setNew(true);

        $copyObj->setOutDocUid(''); // this is a pkey column, so set to default value

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
     * @return     OutputDocument Clone of current object.
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
     * @return     OutputDocumentPeer
     */
    public function getPeer()
    {
        if (self::$peer === null) {
            self::$peer = new OutputDocumentPeer();
        }
        return self::$peer;
    }
}

