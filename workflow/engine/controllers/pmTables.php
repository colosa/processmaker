<?php

/**
 * pmTables controller
 *
 * @author Erik Amaru Ortiz <erik@colosa.com, aortiz.erik@gmail.com>
 * @inherits Controller
 * @access public
 */

class pmTables extends Controller
{
    /**
     *
     * @param boolean debug
     */
    public $debug = false;

    /**
     * getting default list
     *
     * @param string $httpData->PRO_UID (opional)
     */
    public function index ($httpData)
    {
        global $RBAC;
        $RBAC->requirePermissions( 'PM_SETUP_ADVANCE' );

        G::LoadClass( 'configuration' );
        $c = new Configurations();
        $configPage = $c->getConfiguration( 'additionalTablesList', 'pageSize', '', $_SESSION['USER_LOGGED'] );
        $Config['pageSize'] = isset( $configPage['pageSize'] ) ? $configPage['pageSize'] : 20;

        $this->includeExtJS( 'pmTables/list', $this->debug );
        $this->includeExtJS( 'pmTables/export', $this->debug );
        $this->setView( 'pmTables/list' );

        //assigning js variables
        $this->setJSVar( 'PROCESSMAP', (isset($_REQUEST['PROCESSMAP'])) ? $_REQUEST['PROCESSMAP'] : 0);
        $this->setJSVar( 'FORMATS', $c->getFormats() );
        $this->setJSVar( 'CONFIG', $Config );
        $this->setJSVar( 'PRO_UID', isset( $_GET['PRO_UID'] ) ? $_GET['PRO_UID'] : false );

        $this->setJSVar( '_PLUGIN_SIMPLEREPORTS', $this->_getSimpleReportPluginDef() );

        if (isset( $_SESSION['_cache_pmtables'] )) {
            unset( $_SESSION['_cache_pmtables'] );
        }

        if (isset( $_SESSION['ADD_TAB_UID'] )) {
            unset( $_SESSION['ADD_TAB_UID'] );
        }

        //render content
        G::RenderPage( 'publish', 'extJs' );
    }

    /**
     * edit pmtable
     *
     * @param string $httpData->id
     */
    public function edit ($httpData)
    {
        require_once PATH_CONTROLLERS . 'pmTablesProxy.php';
        require_once 'classes/model/AdditionalTables.php';
        G::loadClass( 'pmTable' );

        $additionalTables = new AdditionalTables();
        $table = false;
        $addTabUid = isset( $httpData->id ) ? $httpData->id : false;
        $dataNumRows = 0;
        $repTabPluginPermissions = false;
        $columnsTypes = PmTable::getPropelSupportedColumnTypes();
        $jsFile = isset( $httpData->tableType ) && $httpData->tableType == 'report' ? 'editReport' : 'edit';
        $columnsTypesList = array ();

        foreach ($columnsTypes as $columnTypeName => $columnType) {
            $columnsTypesList[] = array ($columnTypeName,$columnType
            );
        }

        if ($addTabUid) {
            $tableData = $additionalTables->getAllData( $httpData->id, 0, 2 );
            $dataNumRows = $tableData['count'];
        }

        if ($addTabUid !== false) {
            // if it is a edit request
            $tableFields = array ();
            $fieldsList = array ();

            $table = $additionalTables->load( $addTabUid, true );
            //fix for backware compatibility
            $table['DBS_UID'] = $table['DBS_UID'] == null || $table['DBS_UID'] == '' ? 'workflow' : $table['DBS_UID'];
            $_SESSION['ADD_TAB_UID'] = $addTabUid;

            //list dynaform fields
            if ($table['ADD_TAB_TYPE'] == 'NORMAL' || $table['ADD_TAB_TYPE'] == 'GRID') {
                $repTabPluginPermissions = $this->_getSimpleReportPluginDef();
            }
        }

        $this->includeExtJS( 'pmTables/' . $jsFile );

        $this->setJSVar( 'PROCESSMAP', (isset($_REQUEST['PROCESSMAP'])) ? $_REQUEST['PROCESSMAP'] : 0);
        $this->setJSVar( 'ADD_TAB_UID', $addTabUid );
        $this->setJSVar( 'PRO_UID', isset( $_GET['PRO_UID'] ) ? $_GET['PRO_UID'] : false );
        $this->setJSVar( 'TABLE', $table );
        $this->setJSVar( 'dbg', isset( $httpData->dbg ) );
        $this->setJSVar( 'columnsTypes', $columnsTypesList );
        $this->setJSVar( 'dataNumRows', $dataNumRows );
        $this->setJSVar( '_plugin_permissions', $repTabPluginPermissions );
        $this->setJSVar( 'sizeTableName', $this->getSizeTableName());

        G::RenderPage( 'publish', 'extJs' );
    }

    /**
     * show pmTable data list
     *
     * @param string $httpData->id
     */
    public function data ($httpData)
    {
        require_once 'classes/model/AdditionalTables.php';
        $additionalTables = new AdditionalTables();
        $tableDef = $additionalTables->load( $httpData->id, true );

        $this->includeExtJS( 'pmTables/data', $this->debug );
        $this->setJSVar( 'tableDef', $tableDef );

        //g::pr($tableDef['FIELDS']);
        G::RenderPage( 'publish', 'extJs' );
    }

    public function export ($httpData)
    {
        $this->includeExtJS( 'pmTables/export', $this->debug ); //adding a javascript file .js
        $this->setView( 'pmTables/export' ); //adding a html file  .html.


        $toSend = Array ();
        $toSend['UID_LIST'] = $httpData->id;

        $this->setJSVar( 'EXPORT_TABLES', $toSend );
        G::RenderPage( 'publish', 'extJs' );
    }

    public function streamExported ($httpData)
    {
        $PUBLIC_ROOT_PATH = PATH_DATA . 'sites' . PATH_SEP . SYS_SYS . PATH_SEP . 'public' . PATH_SEP;
        $sFileName = $httpData->f;

        $realPath = $PUBLIC_ROOT_PATH . $sFileName;
        G::streamFile( $realPath, true );
        unlink( $realPath );
    }

    /**
     * - protected functions (non-callable from controller outside) -
     */

    /**
     * Get simple report plugin definition
     *
     * @param $type
     */
    protected function _getSimpleReportPluginDef ()
    {
        global $G_TMP_MENU;
        $oMenu = new Menu();
        $oMenu->load( 'setup' );
        $repTabPluginPermissions = false;

        foreach ($oMenu->Options as $i => $option) {
            if ($oMenu->Types[$i] == 'private' && $oMenu->Id[$i] == 'PLUGIN_REPTAB_PERMISSIONS') {
                $repTabPluginPermissions = array ();
                $repTabPluginPermissions['label'] = $oMenu->Labels[$i];
                $repTabPluginPermissions['fn'] = $oMenu->Options[$i];
                break;
            }
        }
        return $repTabPluginPermissions;
    }
    /**
     *
     * Return of size ok the engine on course.
     *
     * @return int
     */
    public function getSizeTableName()
    {
        switch (DB_ADAPTER) {
            case 'mysql':
                $tableSize = 64;
                break;
            case 'mssql':
                $tableSize = 128;
                break;
            case 'oci8':
                $tableSize = 30;
            default:
                $tableSize = 30;
                break;
        }
        $tableSize = $tableSize - 8; // Prefix PMT_
        return $tableSize;
    }
}

