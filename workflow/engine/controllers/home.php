<?php

use ProcessMaker\Core\System;

/**
 * Home controller
 *
 * @inherits Controller
 * @access public
 */

class Home extends Controller
{
    private $userUid;
    private $userName;
    private $userFullName;
    private $userRolName;
    private $userUxType;
    private $userUxBaseTemplate;

    private $appListStart = 0;
    private $appListLimit = 15;

    private $clientBrowser;
    private $lastSkin;
    private $usrId;

    /**
     * Check the if the user has permissions over functions
     */
    public function call ($name)
    {
        global $RBAC;
        $RBAC->allows(basename(__FILE__), $name);
        parent::call($name);
    }

    public function __construct ()
    {
        // setting client browser information
        $this->clientBrowser = G::getBrowser();

        // getting the ux type from user o group conf.
        $this->userUxType = isset( $_SESSION['user_experience'] ) ? $_SESSION['user_experience'] : 'SIMPLIFIED';
        $this->lastSkin = isset( $_SESSION['user_last_skin'] ) ? $_SESSION['user_last_skin'] : 'neoclassic';
        $this->userUxBaseTemplate = (is_dir( PATH_CUSTOM_SKINS . 'uxs' )) ? PATH_CUSTOM_SKINS . 'simplified' . PATH_SEP . 'templates' : 'home';

        if (isset( $_SESSION['USER_LOGGED'] ) && ! empty( $_SESSION['USER_LOGGED'] )) {
            $this->userUid = isset( $_SESSION['USER_LOGGED'] ) ? $_SESSION['USER_LOGGED'] : null;
            $this->userName = isset( $_SESSION['USR_USERNAME'] ) ? $_SESSION['USR_USERNAME'] : '';
            $this->userFullName = isset( $_SESSION['USR_FULLNAME'] ) ? $_SESSION['USR_FULLNAME'] : '';
            $this->userRolName = isset( $_SESSION['USR_ROLENAME'] ) ? $_SESSION['USR_ROLENAME'] : '';

            $users = new Users();
            $users = $users->load($this->userUid);
            $this->usrId = $users["USR_ID"];
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
            $data = array (
                'u' => '',
                'p' => '',
                'm' => ''
            );
        }

        $this->setVar( 'msg', $data['m'] );
        $this->setVar( 'usr', $data['u'] );
        $this->setVar( 'pwd', $data['p'] );
        $this->setVar('timeZoneFailed',  (isset($data['timeZoneFailed']))?  $data['timeZoneFailed'] : 0);
        $this->setVar('userTimeZone',    (isset($data['userTimeZone']))?    $data['userTimeZone'] : '');
        $this->setVar('browserTimeZone', (isset($data['browserTimeZone']))? $data['browserTimeZone'] : '');
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

        $userProperty = new UsersProperties();
        $process = new Process();
        $case = new Cases();
        $sysConf = System::getSystemConfiguration( PATH_CONFIG . 'env.ini' );

        //Get ProcessStatistics Info
        $start = 0;
        $limit = '';

        $proData = $process->getAllProcesses( $start, $limit, null, null, false, true );
        $processList = $case->getStartCasesPerType( $_SESSION['USER_LOGGED'], 'category' );

        $processesList = array ();

        foreach ($processList as $key => $valueProcess) {
            foreach ($proData as $keyPro => $valuePro) {
                if (!isset($valueProcess['pro_uid'])) {
                    $valueProcess['pro_uid'] = '';
                }
                if ($valueProcess['pro_uid'] == $valuePro['PRO_UID']) {
                    $processesList[] = $valueProcess;
                }
            }
        }

        $switchLink = $userProperty->getUserLocation( $_SESSION['USER_LOGGED'], SYS_LANG );

        if (!isset($_COOKIE['workspaceSkin'])) {
            if (substr( $sysConf['default_skin'], 0, 2 ) == 'ux') {
                $_SESSION['_defaultUserLocation'] = $switchLink;
                $switchLink = '/sys' . SYS_SYS . '/' . SYS_LANG . '/' . $sysConf['default_skin'] . '/main';
            }
        }

        $oServerConf = & ServerConf::getSingleton();

        if ($oServerConf->isRtl( SYS_LANG )) {
            $swRtl = 1;
        } else {
            $swRtl = 0;
        }

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

        $this->setVar( 'usrUid', $this->userUid );
        $this->setVar( 'userName', $this->userName );
        $this->setVar( 'processList', $processesList );
        $this->setVar( 'canStartCase', $case->canStartCase( $_SESSION['USER_LOGGED'] ) );
        $this->setVar( 'userUxType', $this->userUxType );
        $this->setVar( 'clientBrowser', $this->clientBrowser['name'] );
        $this->setVar( 'switchLink', $switchLink );
        $this->setVar( 'arrayMnuOption', $arrayMnuOption );
        $this->setVar( 'mnuNewCase', $mnuNewCase );
        $this->setVar( 'rtl', $swRtl );

        $this->render();
    }

    public function indexSingle ($httpData)
    {
        $step = new Step();

        $solrEnabled = false;

        if (($solrConf = System::solrEnv()) !== false) {

            $ApplicationSolrIndex = new AppSolr(
                $solrConf["solr_enabled"],
                $solrConf["solr_host"],
                $solrConf["solr_instance"]
            );

            if ($ApplicationSolrIndex->isSolrEnabled() && $solrConf['solr_enabled'] == true) {
                //Check if there are missing records to reindex and reindex them
                $ApplicationSolrIndex->synchronizePendingApplications();
                $solrEnabled = true;
            } else{
                $solrEnabled = false;
            }
        }

        if ($solrEnabled) {
            $cases = $ApplicationSolrIndex->getAppGridData($this->userUid, 0, 1, 'todo');
        } else {

            $apps = new Applications();

            $cases = $apps->getAll( $this->userUid, 0, 1, 'todo' );
        }

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

        $this->setVar( 'usrUid', $this->userUid );
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
                $title = G::LoadTranslation("ID_MY_INBOX");
                break;
            case 'draft':
                $title = G::LoadTranslation("ID_MY_DRAFTS");
                break;
            case 'unassigned':
                $title = G::LoadTranslation("ID_UNASSIGNED_INBOX");
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
        $this->setVar( 'title', $title );
        $this->setVar( 'noPerms', G::LoadTranslation( 'ID_CASES_NOTES_NO_PERMISSIONS' ));
        $this->setVar( 'appListStart', $this->appListLimit );
        $this->setVar( 'appListLimit', 10 );
        $this->setVar( 'listType', $httpData->t );

        $this->render();
    }

    public function appAdvancedSearch ($httpData)
    {
        $title = G::LoadTranslation("ID_ADVANCEDSEARCH");
        $httpData->t = 'search';

        // settings html template
        $this->setView( $this->userUxBaseTemplate . PATH_SEP . 'appListSearch' );

        // get data
        $process = (isset($httpData->process)) ? $httpData->process : null;
        $status = (isset($httpData->status)) ? $httpData->status : null;
        $search = (isset($httpData->search)) ? $httpData->search : null;
        $category = (isset($httpData->category)) ? $httpData->category : null;
        $user = (isset($httpData->user)) ? $httpData->user : null;
        $dateFrom = (isset($httpData->dateFrom)) ? $httpData->dateFrom : null;
        $dateTo = (isset($httpData->dateTo)) ? $httpData->dateTo : null;
        $processTitle = "";
        if (!empty($process)) {
            $processTitle = Process::loadById($process)->getProTitle();
        }
        $userName = "";
        if (!empty($user) && $user !== "ALL" && $user !== "CURRENT_USER") {
            $userObject = Users::loadById($user);
            $userName = $userObject->getUsrLastname() . " " . $userObject->getUsrFirstname();
        }

        $cases = $this->getAppsData(
            $httpData->t,
            null,
            null,
            $user,
            null,
            $search,
            $process,
            $status,
            $dateFrom,
            $dateTo,
            null,
            null,
            'APP_CACHE_VIEW.APP_NUMBER',
            $category
        );
        $arraySearch = array($process,  $status,  $search, $category, $user, $dateFrom, $dateTo );

        // settings vars and rendering
        $this->setVar( 'statusValues', $this->getStatusArray( $httpData->t, $this->userUid) );
        $this->setVar( 'categoryValues', $this->getCategoryArray() );
        $this->setVar( 'allUsersValues', $this->getAllUsersArray( 'search' ) );
        $this->setVar( 'categoryTitle', G::LoadTranslation("ID_CATEGORY") );
        $this->setVar( 'processTitle', G::LoadTranslation("ID_PROCESS") );
        $this->setVar( 'statusTitle', G::LoadTranslation("ID_STATUS") );
        $this->setVar( 'searchTitle', G::LoadTranslation("ID_SEARCH") );
        $this->setVar( 'userTitle', G::LoadTranslation("ID_USER") );
        $this->setVar( 'fromTitle', G::LoadTranslation("ID_DELEGATE_DATE_FROM") );
        $this->setVar( 'toTitle', G::LoadTranslation("ID_DELEGATE_DATE_TO") );
        $this->setVar( 'filterTitle', G::LoadTranslation("ID_FILTER") );
        $this->setVar( 'arraySearch', $arraySearch );

        $this->setVar( 'cases', $cases['data'] );
        $this->setVar( 'title', $title );
        $this->setVar( 'noPerms', G::LoadTranslation( 'ID_CASES_NOTES_NO_PERMISSIONS' ));
        $this->setVar( 'appListStart', $this->appListLimit );
        $this->setVar( 'appListLimit', 10 );
        $this->setVar( 'listType', $httpData->t );

        $this->setVar( 'processCurrentTitle', $processTitle );
        $this->setVar( 'userCurrentName', $userName );
        $this->setVar( 'currentUserLabel', G::LoadTranslation( "ID_ALL_USERS" ) );
        $this->setVar( 'allProcessLabel', G::LoadTranslation("ID_ALL_PROCESS") );

        $this->render();
    }

    public function getApps ($httpData)
    {
        $cases = $this->getAppsData( $httpData->t, $httpData->start, $httpData->limit );

        $this->setView( $this->userUxBaseTemplate . PATH_SEP . 'applications' );
        $this->setVar( 'cases', $cases['data'] );
        $this->render();
    }

    public function getAppsData (
        $type,
        $start = null,
        $limit = null,
        $user = null,
        $filter = null,
        $search = null,
        $process = null,
        $status = null,
        $dateFrom = null,
        $dateTo = null,
        $callback = null,
        $dir = 'DESC',
        $sort = "APP_CACHE_VIEW.APP_NUMBER",
        $category = null)
    {

        $appNotes = new AppNotes();

        $start = empty( $start ) ? $this->appListStart : $start;
        $limit = empty( $limit ) ? $this->appListLimit : $limit;

        $notesStart = 0;
        $notesLimit = 4;
        switch ($user) {
            case 'CURRENT_USER':
                $user = $this->usrId;
                break;
            case 'ALL':
                $user = null;
                break;
            case null:
                if ($type === 'search') {
                    $user = null;
                } else {
                    $user = $this->usrId;
                }
                break;
            default:

                break;
        }

        $solrEnabled = false;

        if ((
            $type == "todo" || $type == "draft" || $type == "paused" || $type == "sent" ||
            $type == "selfservice" || $type == "unassigned" || $type == "search"
        ) &&
        (($solrConf = System::solrEnv()) !== false)
        ) {

            $ApplicationSolrIndex = new AppSolr(
                $solrConf["solr_enabled"],
                $solrConf["solr_host"],
                $solrConf["solr_instance"]
            );

            if ($ApplicationSolrIndex->isSolrEnabled() && $solrConf['solr_enabled'] == true) {
                //Check if there are missing records to reindex and reindex them
                $ApplicationSolrIndex->synchronizePendingApplications();
                $solrEnabled = true;
            } else{
                $solrEnabled = false;
            }
        }

        if ($solrEnabled) {
            $cases = $ApplicationSolrIndex->getAppGridData(
                $user,
                $start,
                $limit,
                $type,
                $filter,
                $search,
                $process,
                $status,
                '',
                $dateFrom,
                $dateTo,
                $callback,
                $dir,
                $sort,
                $category
            );
        } else {
            $dataList['userId']   = $user;
            $dataList['userUid']  = $this->userUid;
            $dataList['start']    = $start;
            $dataList['limit']    = $limit;
            $dataList['filter']   = $filter;
            $dataList['search']   = $search;
            $dataList['process']  = $process;
            $dataList['status']   = $status;
            $dataList['dateFrom'] = $dateFrom;
            $dataList['dateTo']   = $dateTo;
            $dataList['callback'] = $callback;
            $dataList['dir']      = $dir;
            $dataList['sort']     = $sort;
            $dataList['category'] = $category;
            $dataList['action']   = $type;
            $dataList['dir'] = 'DESC';
            /*----------------------------------********---------------------------------*/
                $case = new \ProcessMaker\BusinessModel\Cases();
                $cases = $case->getList($dataList);
                foreach ($cases['data'] as &$value) {
                    $value = array_change_key_case($value, CASE_UPPER);
                }
            /*----------------------------------********---------------------------------*/

        }

        if(empty($cases) && $type == 'search') {
            $case = new \ProcessMaker\BusinessModel\Cases();
            $cases = $case->getCasesSearch($dataList);
            foreach ($cases['data'] as &$value) {
                $value = array_change_key_case($value, CASE_UPPER);
            }
        }

        if(!isset($cases['totalCount'])){
            $cases['totalCount'] = $cases['total'];
        }

        // formating & complitting apps data with 'Notes'
        foreach ($cases['data'] as $i => $row) {
            // Formatting
            $appTitle = str_replace( '#', '', $row['APP_TITLE'] );

            if (is_numeric( $appTitle )) {
                $cases['data'][$i]['APP_TITLE'] = G::LoadTranslation( 'ID_CASE' ) . ' ' . $appTitle;
            }

            if (isset( $row['DEL_DELEGATE_DATE'] )) {
                $conf = new Configurations();
                $generalConfCasesList = $conf->getConfiguration( 'ENVIRONMENT_SETTINGS', '' );
                $cases['data'][$i]['DEL_DELEGATE_DATE'] = '';
                if (defined('SYS_SYS')) {
                    if (isset( $generalConfCasesList['casesListDateFormat'] ) && ! empty( $generalConfCasesList['casesListDateFormat'] )) {
                        $cases['data'][$i]['DEL_DELEGATE_DATE'] = $conf->getSystemDate($row['DEL_DELEGATE_DATE'], 'casesListDateFormat');
                    }
                }
                if ($cases['data'][$i]['DEL_DELEGATE_DATE'] == '') {
                    $cases['data'][$i]['DEL_DELEGATE_DATE'] = $conf->getSystemDate($row['DEL_DELEGATE_DATE']);
                }
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
        $case = new Cases();
        $aData = $case->startCase( $httpData->id, $_SESSION['USER_LOGGED'] );

        $_SESSION['APPLICATION'] = $aData['APPLICATION'];
        $_SESSION['INDEX'] = $aData['INDEX'];
        $_SESSION['PROCESS'] = $aData['PROCESS'];
        $_SESSION['TASK'] = $httpData->id;
        $_SESSION['STEP_POSITION'] = 0;
        $_SESSION['CASES_REFRESH'] = true;

        $oCase = new Cases();
        $aNextStep = $oCase->getNextStep( $_SESSION['PROCESS'], $_SESSION['APPLICATION'], $_SESSION['INDEX'], $_SESSION['STEP_POSITION'] );
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

    function getUserArray($action, $userUid, $search = null)
    {
        $conf = new Configurations();
        $confEnvSetting = $conf->getFormats();
        $users = array();
        $users[] = array("CURRENT_USER", G::LoadTranslation("ID_CURRENT_USER"));
        $users[] = array("ALL", G::LoadTranslation("ID_ALL_USERS"));

        //now get users, just for the Search action
        switch ($action) {
            case 'search_simple':
            case 'search':
                $cUsers = new Criteria('workflow');
                $cUsers->clearSelectColumns();
                $cUsers->addSelectColumn(UsersPeer::USR_UID);
                $cUsers->addSelectColumn(UsersPeer::USR_FIRSTNAME);
                $cUsers->addSelectColumn(UsersPeer::USR_LASTNAME);
                $cUsers->addSelectColumn(UsersPeer::USR_USERNAME);
                $cUsers->addSelectColumn(UsersPeer::USR_ID);
                if (!empty($search)) {
                    $cUsers->add(
                        $cUsers->getNewCriterion(UsersPeer::USR_FIRSTNAME, '%' . $search . '%', Criteria::LIKE)->addOr(
                        $cUsers->getNewCriterion(UsersPeer::USR_LASTNAME, '%' . $search . '%', Criteria::LIKE))
                    );
                }
                $oDataset = UsersPeer::doSelectRS($cUsers);
                $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
                $oDataset->next();
                while ($aRow = $oDataset->getRow()) {
                    $usrFullName = $conf->usersNameFormatBySetParameters(
                        $confEnvSetting["format"],
                        $aRow["USR_USERNAME"],
                        $aRow["USR_FIRSTNAME"],
                        $aRow["USR_LASTNAME"]
                    );
                    $users[] = array($aRow['USR_ID'], htmlentities($usrFullName, ENT_QUOTES, "UTF-8"));
                    $oDataset->next();
                }
                break;
            default:
                return $users;
                break;
        }
        return $users;
    }

    function getCategoryArray ()
    {
        $category = array();
        $category[] = array ("",G::LoadTranslation( "ID_ALL_CATEGORIES" ));

        $criteria = new Criteria( 'workflow' );
        $criteria->addSelectColumn( ProcessCategoryPeer::CATEGORY_UID );
        $criteria->addSelectColumn( ProcessCategoryPeer::CATEGORY_NAME );
        $dataset = ProcessCategoryPeer::doSelectRS( $criteria );
        $dataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
        $dataset->next();

        while ($row = $dataset->getRow()) {
            $category[] = array ($row['CATEGORY_UID'],$row['CATEGORY_NAME']);
            $dataset->next();
        }
        return $category;
    }

    function getAllUsersArray ($action)
    {
        global $oAppCache;
        $users = array ();
        $users[] = array ("CURRENT_USER",G::LoadTranslation( "ID_CURRENT_USER" ));
        $users[] = array ("",G::LoadTranslation( "ID_ALL_USERS" ));

        if ($action == 'to_reassign') {
            //now get users, just for the Search action
            $cUsers = $oAppCache->getToReassignListCriteria(null);
            $cUsers->addSelectColumn( AppCacheViewPeer::USR_UID );

            if (g::MySQLSintaxis()) {
                $cUsers->addGroupByColumn( AppCacheViewPeer::USR_UID );
            }

            $cUsers->addAscendingOrderByColumn( AppCacheViewPeer::APP_CURRENT_USER );
            $oDataset = AppCacheViewPeer::doSelectRS( $cUsers );
            $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
            $oDataset->next();
            while ($aRow = $oDataset->getRow()) {
                $users[] = array ($aRow['USR_UID'],$aRow['APP_CURRENT_USER']);
                $oDataset->next();
            }
        }
        return $users;
    }

    function getStatusArray ($action, $userUid)
    {
        $status = array();
        $aStatus = Application::$app_status_values;
        $status[] = array('', G::LoadTranslation('ID_ALL_STATUS'));
        foreach ($aStatus as $key => $value) {
            if ($action == 'search') {
                $status[] =  array ($value, G::LoadTranslation( 'ID_CASES_STATUS_' . $key ));
            } else {
                $status[] =  array ($key, G::LoadTranslation( 'ID_CASES_STATUS_' . $key ));
            }
        }
        return $status;
    }

    /**
     * Get the list of active processes
     *
     * @global type $oAppCache
     * @param type $action
     * @param type $userUid
     * @return array
     */
    private function getProcessArray($action, $userUid, $search=null)
    {
        $processes = array();
        $processes[] = array("", G::LoadTranslation("ID_ALL_PROCESS"));

        $cProcess = new Criteria("workflow");
        $cProcess->clearSelectColumns();
        $cProcess->addSelectColumn(ProcessPeer::PRO_ID);
        $cProcess->addSelectColumn(ProcessPeer::PRO_TITLE);
        $cProcess->add(ProcessPeer::PRO_STATUS, "ACTIVE");
        if (!empty($search)) {
            $cProcess->add(ProcessPeer::PRO_TITLE, "%$search%", Criteria::LIKE);
        }
        $oDataset = ProcessPeer::doSelectRS($cProcess);
        $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $oDataset->next();
        while ($aRow = $oDataset->getRow()) {
          $processes[] = array($aRow["PRO_ID"], $aRow["PRO_TITLE"]);
          $oDataset->next();
        }

        return ($processes);
    }

    /**
     * Get the list of processes
     * @param type $httpData
     */
    public function getProcesses($httpData)
    {
        $processes = [];
        foreach ($this->getProcessArray($httpData->t, null, $httpData->term) as $row) {
            $processes[] = [
                'id'    => $row[0],
                'label' => $row[1],
                'value' => $row[1],
            ];
        }
        print G::json_encode($processes);
    }

    /**
     * Get the list of users
     * @param type $httpData
     */
    public function getUsers($httpData)
    {
        $users = [];
        foreach ($this->getUserArray($httpData->t, null, $httpData->term) as $row) {
            $users[] = [
                'id'    => $row[0],
                'label' => $row[1],
                'value' => $row[1],
            ];
        }
        print G::json_encode($users);
    }
}

