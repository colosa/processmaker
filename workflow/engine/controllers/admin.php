<?php

use ProcessMaker\Core\System;

/**
 * Admin controller
 *
 * @access public
 */
class Admin extends Controller
{
    /**
     * UX - User experience
     */

    public $debug = true;

    public function system ()
    {
        global $RBAC;
        $RBAC->requirePermissions( 'PM_SETUP' );
        require_once PATH_CONTROLLERS . 'main.php';

        $skinsList = System::getSkingList();
        foreach ($skinsList['skins'] as $key => $value) {
            if ($value['SKIN_WORKSPACE'] != 'Global') {
                unset( $skinsList['skins'][$key] );
            }
        }
        $skins = array ();
        $mainController = new Main();
        $languagesList = $mainController->getLanguagesList();
        $languagesList[] = array ("", G::LoadTranslation("ID_USE_LANGUAGE_URL"));
        $sysConf = System::getSystemConfiguration( PATH_CONFIG . 'env.ini' );

        foreach ($skinsList['skins'] as $skin) {
            $skins[] = array ($skin['SKIN_FOLDER_ID'],$skin['SKIN_NAME']);
        }

        $this->includeExtJS( 'admin/system' );

        // $c = new Configurations();
        // $configPage = $c->getConfiguration('usersList', 'pageSize','',$_SESSION['USER_LOGGED']);
        // $Config['pageSize'] = isset($configPage['pageSize']) ? $configPage['pageSize'] : 20;
        if (isset($sysConf["session.gc_maxlifetime"])) {
            $sysConf["session_gc_maxlifetime"] = $sysConf["session.gc_maxlifetime"];
        } else {
            $sysConf["session_gc_maxlifetime"] = ini_get('session.gc_maxlifetime');
        }

        $this->setJSVar( 'skinsList', $skins );
        $this->setJSVar( 'languagesList', $languagesList );
        $this->setJSVar('timeZonesList', array_map(function ($value) { return [$value, $value]; }, DateTimeZone::listIdentifiers()));
        $this->setJSVar( 'sysConf', $sysConf );

        G::RenderPage( 'publish', 'extJs' );
    }

    public function uxList ()
    {
        global $RBAC;
        $RBAC->requirePermissions( 'PM_SETUP' );
        require_once PATH_CONTROLLERS . 'adminProxy.php';
        $this->includeExtJS( 'admin/uxUsersList' );

        $c = new Configurations();
        $configPage = $c->getConfiguration( 'usersList', 'pageSize', '', $_SESSION['USER_LOGGED'] );
        $Config['pageSize'] = isset( $configPage['pageSize'] ) ? $configPage['pageSize'] : 20;

        $this->setJSVar( 'CONFIG', $Config );
        $this->setJSVar( 'FORMATS', $c->getFormats() );
        $this->setJSVar( 'uxTypes', AdminProxy::getUxTypesList( 'list' ) );

        G::RenderPage( 'publish', 'extJs' );
    }

    /**
     * CALENDAR
     * getting default list
     *
     * @param string $httpData->PRO_UID (opional)
     */
    public function calendarEdit ($httpData)
    {
        global $RBAC;
        //$RBAC->requirePermissions('PM_SETUP_ADVANCE');

        $CalendarUid = str_replace( '"', '', isset( $_GET['id'] ) ? $_GET['id'] : G::GenerateUniqueID() );
        $calendarObj = new Calendar();

        if ((isset( $_GET['id'] )) && ($_GET['id'] != "")) {
            $fields = $calendarObj->getCalendarInfoE( $CalendarUid );
            $fields['OLD_NAME'] = $fields['CALENDAR_NAME'];
        }
        // For a new Calendar
        if (! isset( $fields['CALENDAR_UID'] )) {
            $fields['CALENDAR_UID'] = $CalendarUid;
            $fields['OLD_NAME'] = '';

            //Default Business Hour
            $fields['BUSINESS_DAY'][1]['CALENDAR_BUSINESS_DAY'] = 7;
            $fields['BUSINESS_DAY'][1]['CALENDAR_BUSINESS_START'] = "09:00";
            $fields['BUSINESS_DAY'][1]['CALENDAR_BUSINESS_END'] = "17:00";
        }
        // Copy Calendar
        if ((isset( $_GET['cp'] )) && ($_GET['cp'] == 1)) {
            $fields['CALENDAR_UID'] = G::GenerateUniqueID();
            $fields['CALENDAR_NAME'] = G::LoadTranslation( "ID_COPY_OF" ) . " " . $fields['CALENDAR_NAME'];
            $fields['OLD_NAME'] = $fields['CALENDAR_NAME'];
        }

        $c = new Configurations();
        $configPage = $c->getConfiguration( 'additionalTablesList', 'pageSize', '', $_SESSION['USER_LOGGED'] );
        $Config['pageSize'] = isset( $configPage['pageSize'] ) ? $configPage['pageSize'] : 20;

        $this->includeExtJS( 'admin/calendarEdit' );
        $this->setView( 'admin/calendarEdit' );
        $businessDayArray = array ();

        for ($i = 0; $i < sizeof( $fields['BUSINESS_DAY'] ); $i ++) {
            $businessDayArray[$i] = $fields['BUSINESS_DAY'][$i + 1];
        }

        $fields['BUSINESS_DAY'] = $businessDayArray;
        //validating if the calendar is new, it means that we don't have the $_GET array
        $fields['HOLIDAY'] = (isset( $_GET['id'] ) && $_GET['id'] != '') ? $fields['HOLIDAY'] : array ();
        $holidayArray = array ();
        for ($i = 0; $i < sizeof( $fields['HOLIDAY'] ); $i ++) {
            $holidayArray[$i] = $fields['HOLIDAY'][$i + 1];

        }

        $_GET['id'] = (isset( $_GET['id'] ) && $_GET['id'] != '') ? $_GET['id'] : '';
        $fields['HOLIDAY'] = $holidayArray;
        $fields['NEWCALENDAR'] = 'NO';
        if (isset( $_GET['id'] ) && $_GET['id'] == '') {
            $fields['CALENDAR_UID'] = G::GenerateUniqueID();
            $fields['NEWCALENDAR'] = 'YES';
        }
        $this->setJSVar( 'CALENDAR_UID', $fields['CALENDAR_UID'] );
        $this->setJSVar( 'fields', $fields );

        G::RenderPage( 'publish', 'extJs' );
    }

    /**
     * getting email configuration
     * @autor Alvaro <alvaro@colosa.com>
     */
    public function emails ()
    {
        global $RBAC;

        $RBAC->requirePermissions( 'PM_SETUP_ADVANCE' );
        $this->includeExtJS( 'admin/emails' );
        //render content
        G::RenderPage( 'publish', 'extJs' );
    }

    /**
     * getting default list
     *
     * @param string $httpData->PRO_UID (opional)
     */
    public function pmLogo ($httpData)
    {
        global $RBAC;
        $RBAC->requirePermissions( 'PM_SETUP_ADVANCE', 'PM_SETUP_LOGO');

        $c = new Configurations();
        $configPage = $c->getConfiguration( 'additionalTablesList', 'pageSize', '', $_SESSION['USER_LOGGED'] );
        $Config['pageSize'] = isset( $configPage['pageSize'] ) ? $configPage['pageSize'] : 20;

        $this->includeExtJS( 'admin/pmLogo' );
        $this->setView( 'admin/pmLogo' );

        //assigning js variables
        $this->setJSVar( 'FORMATS', $c->getFormats() );
        $this->setJSVar( 'CONFIG', $Config );
        $this->setJSVar( 'PRO_UID', isset( $_GET['PRO_UID'] ) ? $_GET['PRO_UID'] : false );

        if (isset( $_SESSION['_cache_pmtables'] )) {
            unset( $_SESSION['_cache_pmtables'] );
        }

        if (isset( $_SESSION['ADD_TAB_UID'] )) {
            unset( $_SESSION['ADD_TAB_UID'] );
        }
        //render content
        G::RenderPage( 'publish', 'extJs' );
    }

    public function maintenance()
    {
        $this->setView('admin/maintenance');
        $this->render('extJs');
    }

    public function getSystemInfo()
    {
        global $RBAC;
        $RBAC->requirePermissions('PM_SETUP_ADVANCE');

        $this->setResponseType('json');
        $data = [];

        foreach ($this->_getSystemInfo() as $row) {
            $data[] = [
                'label' => $row[0],
                'value' => $row[1],
                'section' => $row[2]
            ];
        }
        return $data;
    }

    private function _getSystemInfo ()
    {

        if (getenv( 'HTTP_CLIENT_IP' )) {
            $ip = getenv( 'HTTP_CLIENT_IP' );
        } else {
            if (getenv( 'HTTP_X_FORWARDED_FOR' )) {
                $ip = getenv( 'HTTP_X_FORWARDED_FOR' );
            } else {
                $ip = getenv( 'REMOTE_ADDR' );
            }
        }

        $redhat = '';
        if (file_exists( '/etc/redhat-release' )) {
            $fnewsize = filesize( '/etc/redhat-release' );
            $fp = fopen( '/etc/redhat-release', 'r' );
            $redhat = trim( fread( $fp, $fnewsize ) );
            fclose( $fp );
        }

        $redhat .= " (" . PHP_OS . ")";
        if (defined( "DB_HOST" )) {
            $dbNetView = new Net( DB_HOST );
            $dbNetView->loginDbServer( DB_USER, DB_PASS );

            $dbConns = new DbConnections( '' );
            $availdb = '';
            foreach ($dbConns->getDbServicesAvailables() as $key => $val) {
                if ($availdb != '') {
                    $availdb .= ', ';
                }
                $availdb .= $val['name'];
            }

            try {
                $sMySQLVersion = $dbNetView->getDbServerVersion( DB_ADAPTER );
            } catch (Exception $oException) {
                $sMySQLVersion = '?????';
            }
        }


        if (file_exists(PATH_HTML . "lib/versions")) {
            $versions = json_decode(file_get_contents(PATH_HTML . "lib/versions"), true);
            $pmuiVer = $versions["pmui_ver"];
            $mafeVer = $versions["mafe_ver"];
            $pmdynaformVer = $versions["pmdynaform_ver"];
        } else {
            $pmuiVer = $mafeVer = $pmdynaformVer = "(unknown)";
        }

        $sysSection = G::loadTranslation('ID_SYSTEM_INFO' );
        $pmSection = G::LoadTranslation('ID_PROCESS_INFORMATION');

        $properties = array ();
        $ee = '';
        /*----------------------------------********---------------------------------*/
        $systemName = 'ProcessMaker';
        if (defined('SYSTEM_NAME')) {
            $systemName = SYSTEM_NAME;
        }
        $properties[] = array ($systemName. ' Ver.', System::getVersion() . $ee, $pmSection);
        $properties[] = array("PMUI JS Lib. Ver.", $pmuiVer, $pmSection);
        $properties[] = array("MAFE JS Lib. Ver.", $mafeVer, $pmSection);
        $properties[] = array("PM Dynaform JS Lib. Ver.", $pmdynaformVer, $pmSection);

        if (file_exists(PATH_DATA. 'log/upgrades.log')) {
            $properties[] = array (G::LoadTranslation('ID_UPGRADES_PATCHES'), '<a href="#" onclick="showUpgradedLogs(); return false;">' . G::LoadTranslation( 'ID_UPGRADE_VIEW_LOG') . '</a>' ,$pmSection);
        } else {
            $properties[] = array (G::LoadTranslation('ID_UPGRADES_PATCHES'), G::LoadTranslation( 'ID_UPGRADE_NEVER_UPGRADE') ,$pmSection);
        }

        $properties[] = array (G::LoadTranslation('ID_OPERATING_SYSTEM') ,$redhat,$sysSection
        );
        $properties[] = array (G::LoadTranslation('ID_TIME_ZONE') ,(defined( 'TIME_ZONE' )) ? TIME_ZONE : "Unknown",$sysSection
        );
        $properties[] = array (G::LoadTranslation('ID_WEB_SERVER') ,getenv( 'SERVER_SOFTWARE' ),$sysSection
        );
        $properties[] = array (G::LoadTranslation('ID_SERVER_NAME') ,getenv( 'SERVER_NAME' ),$pmSection
        );
        $properties[] = array (G::LoadTranslation('ID_SERVER_IP') ,$this->lookup( $ip ),$sysSection
        );
        $properties[] = array (G::LoadTranslation('ID_PHP_VERSION') ,phpversion(),$sysSection
        );

        if (defined( "DB_HOST" )) {
            $properties[] = array (G::LoadTranslation('ID_DATABASE') ,$dbNetView->dbName( DB_ADAPTER ) . ' (Version ' . $sMySQLVersion . ')',$pmSection
            );
            $properties[] = array (G::LoadTranslation('ID_DATABASE_SERVER') ,DB_HOST,$pmSection
            );
            $properties[] = array (G::LoadTranslation('ID_DATABASE_NAME') ,DB_NAME,$pmSection
            );
            $properties[] = array (G::LoadTranslation('ID_AVAILABLE_DB') ,$availdb,$sysSection
            );
        } else {
            $properties[] = array (G::LoadTranslation('ID_DATABASE') ,"Not defined",$pmSection
            );
            $properties[] = array (G::LoadTranslation('ID_DATABASE_SERVER') ,"Not defined",$pmSection
            );
            $properties[] = array (G::LoadTranslation('ID_DATABASE_NAME') ,"Not defined",$pmSection
            );
            $properties[] = array (G::LoadTranslation('ID_AVAILABLE_DB') ,"Not defined",$sysSection
            );
        }

        $properties[] = array ( G::LoadTranslation('ID_WORKSPACE') ,defined( "SYS_SYS" ) ? SYS_SYS : "Not defined",$pmSection
        );

        $properties[] = array ( G::LoadTranslation('ID_SERVER_PROTOCOL') ,getenv( 'SERVER_PROTOCOL' ),$sysSection
        );
        $properties[] = array ( G::LoadTranslation('ID_SERVER_PORT') ,getenv( 'SERVER_PORT' ),$sysSection
        );
        //$sysSection[] = array('Remote Host', getenv ('REMOTE_HOST'), $sysSection);
        $properties[] = array ( G::LoadTranslation('ID_SERVER_NAME') , getenv( 'SERVER_ADDR' ),$sysSection
        );
        $properties[] = array ( G::LoadTranslation('ID_USER_BROWSER') , getenv( 'HTTP_USER_AGENT' ),$sysSection
        );

        return $properties;
    }

    private function lookup ($target)
    {
        global $ntarget;
        $msg = $target . ' => ';
        //if (eregi ('[a-zA-Z]', $target))
        if (preg_match( '[a-zA-Z]', $target )) {
            //Made compatible to PHP 5.3
            $ntarget = gethostbyname( $target );
        } else {
            $ntarget = gethostbyaddr( $target );
        }
        $msg .= $ntarget;
        return ($msg);
    }
}

