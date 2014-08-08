<?php

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
        G::loadClass( 'system' );
        $skinsList = System::getSkingList();
        foreach ($skinsList['skins'] as $key => $value) {
            if ($value['SKIN_WORKSPACE'] != 'Global') {
                unset( $skinsList['skins'][$key] );
            }
        }
        $skins = array ();
        $timeZonesList = System::getAllTimeZones();
        $timeZonesList = array_keys( $timeZonesList );
        $mainController = new Main();
        $languagesList = $mainController->getLanguagesList();
        $languagesList[] = array ("", G::LoadTranslation("ID_USE_LANGUAGE_URL"));
        $sysConf = System::getSystemConfiguration( PATH_CONFIG . 'env.ini' );

        foreach ($skinsList['skins'] as $skin) {
            $skins[] = array ($skin['SKIN_FOLDER_ID'],$skin['SKIN_NAME']);
        }

        foreach ($timeZonesList as $tz) {
            $timeZones[] = array ($tz,$tz);
        }

        $this->includeExtJS( 'admin/system' );
        //G::LoadClass('configuration');

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
        $this->setJSVar( 'timeZonesList', $timeZones );
        $this->setJSVar( 'sysConf', $sysConf );

        G::RenderPage( 'publish', 'extJs' );
    }

    public function uxList ()
    {
        global $RBAC;
        $RBAC->requirePermissions( 'PM_SETUP' );
        require_once PATH_CONTROLLERS . 'adminProxy.php';
        $this->includeExtJS( 'admin/uxUsersList' );
        G::LoadClass( 'configuration' );

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
        G::LoadClass( 'configuration' );
        G::LoadClass( 'calendar' );

        $CalendarUid = str_replace( '"', '', isset( $_GET['id'] ) ? $_GET['id'] : G::GenerateUniqueID() );
        $calendarObj = new calendar();

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
        $RBAC->requirePermissions( 'PM_SETUP_ADVANCE' );

        G::LoadClass( 'configuration' );
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
}

