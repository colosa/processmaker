<?php

/**
 * Home controller
 *
 * @author Erik Amaru Ortiz <erik@colosa.com, aortiz.erik@gmail.com>
 * @inherits Controller
 * @access public
 */

class Home extends Controller
{
    private $userID;
    private $userName;
    private $userFullName;
    private $userRolName;
    private $userUxType;
    private $userUxBaseTemplate;

    private $appListStart = 0;
    private $appListLimit = 15;

    private $clientBrowser;
    private $lastSkin;

    public function __construct ()
    {
        //die($_SESSION['user_experience']);
        // setting client browser information
        $this->clientBrowser = G::getBrowser();

        // getting the ux type from user o group conf.
        $this->userUxType = isset( $_SESSION['user_experience'] ) ? $_SESSION['user_experience'] : 'SIMPLIFIED';
        $this->lastSkin = isset( $_SESSION['user_last_skin'] ) ? $_SESSION['user_last_skin'] : 'classic';
        $this->userUxBaseTemplate = (is_dir( PATH_CUSTOM_SKINS . 'uxs' )) ? PATH_CUSTOM_SKINS . 'simplified' . PATH_SEP . 'templates' : 'home';

        if (isset( $_SESSION['USER_LOGGED'] ) && ! empty( $_SESSION['USER_LOGGED'] )) {
            $this->userID = isset( $_SESSION['USER_LOGGED'] ) ? $_SESSION['USER_LOGGED'] : null;
            $this->userName = isset( $_SESSION['USR_USERNAME'] ) ? $_SESSION['USR_USERNAME'] : '';
            $this->userFullName = isset( $_SESSION['USR_FULLNAME'] ) ? $_SESSION['USR_FULLNAME'] : '';
            $this->userRolName = isset( $_SESSION['USR_ROLENAME'] ) ? $_SESSION['USR_ROLENAME'] : '';
        }
    }

    public function login ($httpData)
    {
        //start new session
        @session_destroy();
        session_start();
        session_regenerate_id();

        $data = isset( $httpData->d ) ? unserialize( base64_decode( $httpData->d ) ) : '';
        $template = $this->clientBrowser['name'] == 'msie' ? 'login_ie' : 'login_standard';
        $skin = $this->clientBrowser['name'] == 'msie' ? $this->lastSkin : 'simplified';

        if (! is_array( $data )) {
            $data = array ('u' => '','p' => '','m' => ''
            );
        }

        $this->setVar( 'msg', $data['m'] );
        $this->setVar( 'usr', $data['u'] );
        $this->setVar( 'pwd', $data['p'] );
        $this->setVar( 'skin', $skin );

        $this->setView( $this->userUxBaseTemplate . PATH_SEP . $template );
        $this->render();
    }

    /**
     * getting default list
     *
     * @param string $httpData (opional)
     */

    public function index ($httpData)
    {
        if ($this->userUxType == 'SINGLE') {
            $this->indexSingle( $httpData );
            return;
        }

        require_once 'classes/model/UsersProperties.php';
        G::LoadClass( 'process' );
        G::LoadClass( 'case' );

        $userProperty = new UsersProperties();
        $process = new Process();
        $case = new Cases();
        G::loadClass( 'system' );
        $sysConf = System::getSystemConfiguration( PATH_CONFIG . 'env.ini' );

        //Get ProcessStatistics Info
        $start = 0;
        $limit = '';

        $proData = $process->getAllProcesses( $start, $limit );
        $processList = $case->getStartCasesPerType( $_SESSION['USER_LOGGED'], 'category' );
        $switchLink = $userProperty->getUserLocation( $_SESSION['USER_LOGGED'] );

        if (substr( $sysConf['default_skin'], 0, 2 ) == 'ux') {
            $_SESSION['_defaultUserLocation'] = $switchLink;
            $switchLink = '/sys' . SYS_SYS . '/' . SYS_LANG . '/' . $sysConf['default_skin'] . '/main';
        }

        unset( $processList[0] );

        //Get simplified options
        global $G_TMP_MENU;

        $mnu = new Menu();
        $mnu->load( 'simplified' );
        $arrayMnuOption = array ();
        $mnuNewCase = array ();

        if (! empty( $mnu->Options )) {
            foreach ($mnu->Options as $index => $value) {
                $option = array ('id' => $mnu->Id[$index],'url' => $mnu->Options[$index],'label' => $mnu->Labels[$index],'icon' => $mnu->Icons[$index],'class' => $mnu->ElementClass[$index]
                );

                if ($mnu->Id[$index] != 'S_NEW_CASE') {
                    $arrayMnuOption[] = $option;
                } else {
                    $mnuNewCase = $option;
                }
            }
        }

        $this->setView( $this->userUxBaseTemplate . PATH_SEP . 'index' );

        $this->setVar( 'usrUid', $this->userID );
        $this->setVar( 'userName', $this->userName );
        $this->setVar( 'processList', $processList );
        $this->setVar( 'canStartCase', $case->canStartCase( $_SESSION['USER_LOGGED'] ) );
        $this->setVar( 'userUxType', $this->userUxType );
        $this->setVar( 'clientBrowser', $this->clientBrowser['name'] );
        $this->setVar( 'switchLink', $switchLink );
        $this->setVar( 'arrayMnuOption', $arrayMnuOption );
        $this->setVar( 'mnuNewCase', $mnuNewCase );

        $this->render();
    }

    public function indexSingle ($httpData)
    {
        require_once 'classes/model/Step.php';
        G::LoadClass( 'applications' );

        $apps = new Applications();
        $step = new Step();

        $cases = $apps->getAll( $this->userID, 0, 1, 'todo' );

        if (! isset( $cases['data'][0] )) {
            //the current user has not any aplication to do
            $this->setView( $this->userUxBaseTemplate . PATH_SEP . 'indexSingle' );
            $this->setVar( 'default_url', $this->userUxBaseTemplate . "/" . 'error?no=2' );
            $this->render();
            exit();
        }

        $lastApp = $cases['data'][0];
        $_SESSION['INDEX'] = $lastApp['DEL_INDEX'];
        $_SESSION['APPLICATION'] = $lastApp['APP_UID'];
        $_SESSION['PROCESS'] = $lastApp['PRO_UID'];
        $_SESSION['TASK'] = $lastApp['TAS_UID'];

        $steps = $apps->getSteps( $lastApp['APP_UID'], $lastApp['DEL_INDEX'], $lastApp['TAS_UID'], $lastApp['PRO_UID'] );
        $lastStep = array_pop( $steps );
        $lastStep['title'] = G::LoadTranslation( 'ID_FINISH' );
        $steps[] = $lastStep;

        $this->setView( $this->userUxBaseTemplate . PATH_SEP . 'indexSingle' );

        $this->setVar( 'usrUid', $this->userID );
        $this->setVar( 'userName', $this->userName );
        $this->setVar( 'steps', $steps );
        $this->setVar( 'default_url', "cases/cases_Open?APP_UID={$lastApp['APP_UID']}&DEL_INDEX={$lastApp['DEL_INDEX']}&action=todo" );

        $this->render();
    }

    public function appList ($httpData)
    {
        // setting default list applications types [default: todo]
        $httpData->t = isset( $httpData->t ) ? $httpData->t : 'todo';

        // setting main list title
        switch ($httpData->t) {
            case 'todo':
                $title = 'My Inbox';
                break;
            case 'draft':
                $title = 'My Drafts';
                break;
            case 'unassigned':
                $title = 'Unassigned Inbox';
                break;
            default:
                $title = ucwords( $httpData->t );
                break;
        }

        // getting apps data
        $cases = $this->getAppsData( $httpData->t );

        // settings html template
        $this->setView( $this->userUxBaseTemplate . PATH_SEP . 'appList' );

        // settings vars and rendering
        $this->setVar( 'cases', $cases['data'] );
        $this->setVar( 'cases_count', $cases['totalCount'] );
        $this->setVar( 'title', $title );
        $this->setVar( 'appListStart', $this->appListLimit );
        $this->setVar( 'appListLimit', 10 );
        $this->setVar( 'listType', $httpData->t );

        $this->render();
    }

    public function getApps ($httpData)
    {
        $cases = $this->getAppsData( $httpData->t, $httpData->start, $httpData->limit );

        $this->setView( $this->userUxBaseTemplate . PATH_SEP . 'applications' );
        $this->setVar( 'cases', $cases['data'] );
        $this->render();
    }

    public function getAppsData ($type, $start = null, $limit = null)
    {
        require_once ("classes/model/AppNotes.php");
        G::LoadClass( 'applications' );

        $apps = new Applications();
        $appNotes = new AppNotes();

        $start = empty( $start ) ? $this->appListStart : $start;
        $limit = empty( $limit ) ? $this->appListLimit : $limit;

        $notesStart = 0;
        $notesLimit = 4;

        $cases = $apps->getAll( $this->userID, $start, $limit, $type );
        //g::pr($cases['data']); die;


        // formating & complitting apps data with 'Notes'
        foreach ($cases['data'] as $i => $row) {
            // Formatting
            $appTitle = str_replace( '#', '', $row['APP_TITLE'] );

            if (is_numeric( $appTitle )) {
                $cases['data'][$i]['APP_TITLE'] = G::LoadTranslation( 'ID_CASE' ) . ' ' . $appTitle;
            }

            if (isset( $row['DEL_DELEGATE_DATE'] )) {
                $cases['data'][$i]['DEL_DELEGATE_DATE'] = G::getformatedDate( $row['DEL_DELEGATE_DATE'], 'M d, yyyy - h:i:s' );
            }
            if (isset( $row['APP_DEL_PREVIOUS_USER'] )) {
                $cases['data'][$i]['APP_DEL_PREVIOUS_USER'] = ucwords( $row['APP_DEL_PREVIOUS_USER'] );
            }
            // Completting with Notes
            $notes = $appNotes->getNotesList( $row['APP_UID'], '', $notesStart, $notesLimit );
            $notes = $notes['array'];

            $cases['data'][$i]['NOTES_COUNT'] = $notes['totalCount'];
            $cases['data'][$i]['NOTES_LIST'] = $notes['notes'];
        }
        return $cases;
    }

    public function startCase ($httpData)
    {
        G::LoadClass( 'case' );
        $case = new Cases();
        $aData = $case->startCase( $httpData->id, $_SESSION['USER_LOGGED'] );

        $_SESSION['APPLICATION'] = $aData['APPLICATION'];
        $_SESSION['INDEX'] = $aData['INDEX'];
        $_SESSION['PROCESS'] = $aData['PROCESS'];
        $_SESSION['TASK'] = $httpData->id;
        $_SESSION['STEP_POSITION'] = 0;
        $_SESSION['CASES_REFRESH'] = true;

        // Execute Events
        require_once 'classes/model/Event.php';
        $event = new Event();
        $event->createAppEvents( $_SESSION['PROCESS'], $_SESSION['APPLICATION'], $_SESSION['INDEX'], $_SESSION['TASK'] );

        $oCase = new Cases();
        $aNextStep = $oCase->getNextStep( $_SESSION['PROCESS'], $_SESSION['APPLICATION'], $_SESSION['INDEX'], $_SESSION['STEP_POSITION'] );
        //../cases/cases_Open?APP_UID={$APP.APP_UID}&DEL_INDEX={$APP.DEL_INDEX}&action=todo
        $aNextStep['PAGE'] = '../cases/cases_Open?APP_UID=' . $aData['APPLICATION'] . '&DEL_INDEX=' . $aData['INDEX'] . '&action=draft';
        $_SESSION['BREAKSTEP']['NEXT_STEP'] = $aNextStep;

        $this->redirect( $aNextStep['PAGE'] );
    }

    public function error ($httpData)
    {
        $httpData->no = isset( $httpData->no ) ? $httpData->no : 0;

        switch ($httpData->no) {
            case 2:
                $tpl = $this->userUxBaseTemplate . PATH_SEP . 'noAppsMsg';
                break;
            default:
                $tpl = $this->userUxBaseTemplate . PATH_SEP . 'error';
        }

        $this->setView( $tpl );
        $this->render();
    }
}

