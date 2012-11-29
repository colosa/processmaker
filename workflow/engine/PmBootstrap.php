<?php
//test
class PmBootstrap extends Bootstrap
{
    public $pmConfig = array();
    public $isRestRequest = false;

    //wrapped
    public function __construct($config)
    {
        parent::__construct($config);

        define('PATH_HOME',     PATH_TRUNK . 'workflow' . PATH_SEP);
        define('PATH_OUTTRUNK', realpath(PATH_TRUNK . '../') . PATH_SEP);

        require_once PATH_HOME . 'engine/config/paths.php';

        //if (php_sapi_name() !== 'cli') {
            session_start(); // starting session
        //}
    }

    //wrapped
    public function configure()
    {
        parent::configure();

        $this->pmConfig = System::getSystemConfiguration();

        $e_all  = defined('E_DEPRECATED') ? E_ALL  & ~E_DEPRECATED : E_ALL;
        $e_all  = defined('E_STRICT')     ? $e_all & ~E_STRICT     : $e_all;
        $e_all  = $this->pmConfig['debug'] ? $e_all                : $e_all & ~E_NOTICE;

        // Do not change any of these settings directly, use env.ini instead
        ini_set('display_errors', $this->pmConfig['debug']);
        ini_set('error_reporting', $e_all);
        ini_set('short_open_tag', 'On');
        ini_set('default_charset', "UTF-8");
        ini_set('memory_limit', $this->pmConfig['memory_limit']);
        ini_set('soap.wsdl_cache_enabled', $this->pmConfig['wsdl_cache']);
        ini_set('date.timezone', $this->pmConfig['time_zone']);

        define ('DEBUG_SQL_LOG', $this->pmConfig['debug_sql']);
        define ('DEBUG_TIME_LOG', $this->pmConfig['debug_time']);
        define ('DEBUG_CALENDAR_LOG', $this->pmConfig['debug_calendar']);
        define ('MEMCACHED_ENABLED',  $this->pmConfig['memcached']);
        define ('MEMCACHED_SERVER',   $this->pmConfig['memcached_server']);
        define ('TIME_ZONE', $this->pmConfig['time_zone']);

        // enable ERROR_SHOW_SOURCE_CODE to display the source code for any WARNING OR NOTICE
        define ('ERROR_SHOW_SOURCE_CODE', true);
    }

    //wrapped
    public function registerClasses()
    {
        parent::registerClasses();

        // (dynamic load)
        $basePath = PATH_CORE . 'lib/';

        $this->autoloader->register('ProcessMaker', PATH_CORE . 'lib/');

        $this->autoloader->register('Haanga', PATH_THIRDPARTY . 'Haanga/lib/');

        // pm workflow classes (static load)
        $this->autoloader->registerClass('System', PATH_CORE . 'classes/class.system');

        $this->autoloader->registerClass('Services_JSON', PATH_THIRDPARTY .'pear/json/class.json');
        $this->autoloader->registerClass('Smarty', PATH_THIRDPARTY . 'smarty/libs/Smarty.class');

        $this->autoloader->registerClass('Propel', PATH_THIRDPARTY . 'propel/Propel');
        $this->autoloader->registerClass('Creole', PATH_THIRDPARTY . 'creole/Creole');
        $this->autoloader->registerClass('Log', PATH_THIRDPARTY . 'pear/Log');


        $this->autoloader->registerClass('error', PATH_GULLIVER . 'class.error');
        $this->autoloader->registerClass('dbconnection', PATH_GULLIVER . 'class.dbconnection');
        $this->autoloader->registerClass('dbsession', PATH_GULLIVER . 'class.dbsession');
        $this->autoloader->registerClass('dbrecordset', PATH_GULLIVER . 'class.dbrecordset');
        $this->autoloader->registerClass('dbtable', PATH_GULLIVER . 'class.dbtable');
        $this->autoloader->registerClass('rbac', PATH_GULLIVER . 'class.rbac' );
        $this->autoloader->registerClass('publisher', PATH_GULLIVER . 'class.publisher');
        $this->autoloader->registerClass('templatePower', PATH_GULLIVER . 'class.templatePower');
        $this->autoloader->registerClass('xmlDocument', PATH_GULLIVER . 'class.xmlDocument');
        $this->autoloader->registerClass('XmlForm_Field_XmlMenu', PATH_GULLIVER . 'class.xmlMenu');
        $this->autoloader->registerClass('xmlform', PATH_GULLIVER . 'class.xmlform');

        $this->autoloader->registerClass('xmlformExtension', PATH_GULLIVER . 'class.xmlformExtension');
        $this->autoloader->registerClass('form', PATH_GULLIVER . 'class.form');
        $this->autoloader->registerClass('menu', PATH_GULLIVER . 'class.menu');
        $this->autoloader->registerClass('xmlMenu', PATH_GULLIVER . 'class.xmlMenu');
        $this->autoloader->registerClass('dvEditor', PATH_GULLIVER . 'class.dvEditor');
        $this->autoloader->registerClass('wysiwygEditor', PATH_GULLIVER . 'class.wysiwygEditor');
        $this->autoloader->registerClass('Controller', PATH_GULLIVER . 'class.controller');
        $this->autoloader->registerClass('HttpProxyController', PATH_GULLIVER . 'class.httpProxyController');
        $this->autoloader->registerClass('PmException', PATH_GULLIVER . 'class.pmException');
        $this->autoloader->registerClass('headPublisher', PATH_GULLIVER . 'class.headPublisher');
        $this->autoloader->registerClass('Xml_Node', PATH_GULLIVER . 'class.xmlDocument');
        $this->autoloader->registerClass('Xml_document', PATH_GULLIVER . 'class.xmlDocument');
        $this->autoloader->registerClass('XmlForm_Field_*', PATH_GULLIVER . 'class.xmlform');
        $this->autoloader->registerClass('serverConf', PATH_CORE . 'classes/class.serverConfiguration');
    }

    /**
     * Verify write permissions for folders that processmaker needs write
     */
    public function verifyWritableFolders()
    {
        // Verifiying permissions processmaker writable directories
        $writableDirs = array(PATH_CONFIG, PATH_XMLFORM, PATH_HTML, PATH_PLUGINS);

        if (defined('PATH_DATA')) {
          $writableDirs[] = PATH_DATA;
        }

        try {
          G::verifyWriteAccess($writableDirs);
        } catch (Exception $e) {
          G::renderTemplate('write_access_denied.exception', array('files' => $e->files));
          die();
        }
    }

    /**
     * On this method have some sys env, configuration, and other fixes
     */
    public function fixEnvironment()
    {
        // IIS Compatibility, SERVER_ADDR doesn't exist on that env, so we need to define it.
        $_SERVER['SERVER_ADDR'] = isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : $_SERVER['SERVER_NAME'];

        //check if it is a installation instance
        if(!defined('PATH_C')) {
            // is a intallation instance, so we need to define PATH_C and PATH_LANGUAGECONT constants temporarily
            define('PATH_C', (rtrim(G::sys_get_temp_dir(), PATH_SEP) . PATH_SEP));
            define('PATH_LANGUAGECONT', PATH_HOME . 'engine/content/languages/' );
        }
    }

    public function loadLeimud()
    {
        $oHeadPublisher = headPublisher::getSingleton();

        // Defining the maborak js file, this file is the concat of many js files and here we are including all of them.
        $oHeadPublisher->addMaborakFile(PATH_GULLIVER_HOME . 'js' . PATH_SEP . 'maborak/core/maborak.js' );
        $oHeadPublisher->addMaborakFile(PATH_GULLIVER_HOME . 'js' . PATH_SEP . 'common/core/common.js' );
        $oHeadPublisher->addMaborakFile(PATH_GULLIVER_HOME . 'js' . PATH_SEP . 'common/core/effects.js' );
        $oHeadPublisher->addMaborakFile(PATH_GULLIVER_HOME . 'js' . PATH_SEP . 'common/core/webResource.js' );
        $oHeadPublisher->addMaborakFile(PATH_GULLIVER_HOME . 'js' . PATH_SEP . 'dveditor/core/dveditor.js' );
        $oHeadPublisher->addMaborakFile(PATH_GULLIVER_HOME . 'js' . PATH_SEP . 'tinymce/jscripts/tiny_mce/tiny_mce.js' );
        $oHeadPublisher->addMaborakFile(PATH_GULLIVER_HOME . 'js' . PATH_SEP . 'common/tree/tree.js' );
        $oHeadPublisher->addMaborakFile(PATH_GULLIVER_HOME . 'js' . PATH_SEP . 'json/core/json.js' );
        $oHeadPublisher->addMaborakFile(PATH_GULLIVER_HOME . 'js' . PATH_SEP . 'form/core/form.js' );
        $oHeadPublisher->addMaborakFile(PATH_GULLIVER_HOME . 'js' . PATH_SEP . 'form/core/pagedTable.js' );
        $oHeadPublisher->addMaborakFile(PATH_GULLIVER_HOME . 'js' . PATH_SEP . 'grid/core/grid.js' );
        $oHeadPublisher->addMaborakFile(PATH_GULLIVER_HOME . 'js' . PATH_SEP . 'maborak/core/module.panel.js'    , true );
        $oHeadPublisher->addMaborakFile(PATH_GULLIVER_HOME . 'js' . PATH_SEP . 'maborak/core/module.validator.js', true );
        $oHeadPublisher->addMaborakFile(PATH_GULLIVER_HOME . 'js' . PATH_SEP . 'maborak/core/module.app.js'      , true );
        $oHeadPublisher->addMaborakFile(PATH_GULLIVER_HOME . 'js' . PATH_SEP . 'maborak/core/module.rpc.js'      , true );
        $oHeadPublisher->addMaborakFile(PATH_GULLIVER_HOME . 'js' . PATH_SEP . 'maborak/core/module.fx.js'       , true );
        $oHeadPublisher->addMaborakFile(PATH_GULLIVER_HOME . 'js' . PATH_SEP . 'maborak/core/module.drag.js'     , true );
        $oHeadPublisher->addMaborakFile(PATH_GULLIVER_HOME . 'js' . PATH_SEP . 'maborak/core/module.drop.js'     , true );
        $oHeadPublisher->addMaborakFile(PATH_GULLIVER_HOME . 'js' . PATH_SEP . 'maborak/core/module.dom.js'      , true );
        $oHeadPublisher->addMaborakFile(PATH_GULLIVER_HOME . 'js' . PATH_SEP . 'maborak/core/module.abbr.js'     , true );
        $oHeadPublisher->addMaborakFile(PATH_GULLIVER_HOME . 'js' . PATH_SEP . 'maborak/core/module.dashboard.js', true );
        $oHeadPublisher->addMaborakFile(PATH_GULLIVER_HOME . 'js' . PATH_SEP . 'widgets/js-calendar/js-calendar.js' );
        $oHeadPublisher->addMaborakFile(PATH_GULLIVER_HOME . 'js' . PATH_SEP . 'widgets/suggest/bsn.AutoSuggest_2.1.3.js' );
        $oHeadPublisher->addMaborakFile(PATH_GULLIVER_HOME . 'js' . PATH_SEP . 'widgets/tooltip/pmtooltip.js' );
        $oHeadPublisher->addMaborakFile(PATH_GULLIVER_HOME . 'js' . PATH_SEP . 'thirdparty/krumo/krumo.js' );
        $oHeadPublisher->addMaborakFile(PATH_GULLIVER_HOME . 'js' . PATH_SEP . 'widgets/calendar/pmcalendar.js' , true );
        $oHeadPublisher->addMaborakFile(PATH_CORE          . 'js' . PATH_SEP . 'cases/core/cases.js' , true );
        $oHeadPublisher->addMaborakFile(PATH_CORE          . 'js' . PATH_SEP . 'cases/core/cases_Step.js', true );
        $oHeadPublisher->addMaborakFile(PATH_CORE          . 'js' . PATH_SEP . 'processmap/core/processmap.js', true );
        $oHeadPublisher->addMaborakFile(PATH_CORE          . 'js' . PATH_SEP . 'appFolder/core/appFolderList.js', true );
        $oHeadPublisher->addMaborakFile(PATH_THIRDPARTY    . 'htmlarea/editor.js', true );

        //$oHeadPublisher->addMaborakFile(PATH_GULLIVER_HOME . 'js' . PATH_SEP . "widgets/jscalendar/lang/calendar-" . SYS_LANG . ".js");
    }

    public function dispatchResource()
    {
        $realPath = $this->matchRoute['path'];

        switch ($this->matchRoute['type']) {
            case 'sysUnnamed' :
                require_once('sysUnnamed.php');
                die;
                break;

            case 'sysNamed' :
                header('location : ' . $_SERVER['REQUEST_URI'] . '/' .SYS_LANG. '/classic/login/login' );
                die;
                break;

            case 'jsMethod' :
                G::parseURI(getenv("REQUEST_URI"));
                $filename = PATH_METHODS . SYS_COLLECTION . '/' . SYS_TARGET . '.js';
                G::streamFile($filename);
                die;
                break;

            case 'errorFile':
                header ("location: /errors/error404.php?url=" . urlencode($_SERVER['REQUEST_URI']));
                if ( DEBUG_TIME_LOG )
                    G::logTimeByPage(); //log this page
                die;
                break;

            case 'plugin':
                //Get the Plugin Folder, always the first element
                $pluginFolder   = array_shift($realPath);
                //The other parts are the realpath into public_html (no matter how many elements)
                $filePath       = implode(PATH_SEP, $realPath);
                $pluginFilename = PATH_PLUGINS . $pluginFolder . PATH_SEP . 'public_html'. PATH_SEP . $filePath;

                if (file_exists($pluginFilename)) {
                    G::streamFile ($pluginFilename);
                }
                die;
                break;

            case 'skin':
                $fileToBeStreamed = str_replace("/skin/", PATH_CUSTOM_SKINS, $_SERVER['REQUEST_URI']);

                if (file_exists($fileToBeStreamed)) {
                    G::streamFile($fileToBeStreamed);
                }
                die;
                break;

            default :
                $realPath .= strpos(basename($realPath), '.') === false ? '.php' : '';
                G::streamFile($realPath);
                die;
        }
    }

    public function fixPmFiles()
    {
        // verify if index.html exists
        if (!file_exists(PATH_HTML . 'index.html')) { // if not, create it from template
            file_put_contents(
              PATH_HTML . 'index.html',
              G::parseTemplate(PATH_TPL . 'index.html', array('lang' => SYS_LANG, 'skin' => SYS_SKIN))
            );
        }
    }

    public function dispatchInstaller()
    {
        // new installer, extjs based
        define('PATH_DATA', PATH_C);
        require_once ( PATH_CONTROLLERS . 'installer.php' );
        $controller = 'Installer';

        // if the method name is empty set default to index method
        if (strpos(SYS_TARGET, '/') !== false) {
            list($controller, $controllerAction) = explode('/', SYS_TARGET);
        }
        else {
            $controllerAction = SYS_TARGET;
        }

        $controllerAction = ($controllerAction != '' && $controllerAction != 'login')? $controllerAction: 'index';

        // create the installer controller and call its method
        if( is_callable(Array('Installer', $controllerAction)) ) {
            $installer = new $controller();
            $installer->setHttpRequestData($_REQUEST);
            $installer->call($controllerAction);
        }
        else {
            $_SESSION['phpFileNotFound'] = $_SERVER['REQUEST_URI'];
            header ("location: /errors/error404.php?url=" . urlencode($_SERVER['REQUEST_URI']));
        }
    }

    public function initPropel($sys = '')
    {
        if (empty($sys)) {
            if (! defined(SYS_SYS)) {
                throw new Exception("Error: Undefined syemtem env. constant 'SYS_SYS'");
            }

            $sys = SYS_SYS;
        }

        // setup propel definitions and logging
        if (defined('DEBUG_SQL_LOG') && DEBUG_SQL_LOG) {
            define('PM_PID', mt_rand(1,999999));

            // register debug connection decorator driver
            Creole::registerDriver('*', 'creole.contrib.DebugConnection');

            // initialize Propel with converted config file
            Propel::init( PATH_CORE . "config/databases.php" );

            // unified log file for all databases
            $logFile = PATH_DATA . 'log' . PATH_SEP . 'propel.log';
            $logger = Log::singleton('file', $logFile, 'wf ' . $sys, null, PEAR_LOG_INFO);
            Propel::setLogger($logger);

            // log file for workflow database
            $con = Propel::getConnection('workflow');
            if ($con instanceof DebugConnection) {
              $con->setLogger($logger);
            }
            // log file for rbac database
            $con = Propel::getConnection('rbac');

            if ($con instanceof DebugConnection) {
              $con->setLogger($logger);
            }

            // log file for report database
            $con = Propel::getConnection('rp');
            if ($con instanceof DebugConnection) {
              $con->setLogger($logger);
            }
        } else {
            Propel::init( PATH_CORE . "config/databases.php" );
        }

        Creole::registerDriver('dbarray', 'creole.contrib.DBArrayConnection');
    }

    public function verifyUserSession($target, $collection)
    {
        // this is the blank list to allow execute scripts with no login (without session started)
        $noLoginFiles   = $noLoginFolders = array();
        $noLoginFiles[] = 'login';
        $noLoginFiles[] = 'authentication';
        $noLoginFiles[] = 'login_Ajax';
        $noLoginFiles[] = 'dbInfo';
        $noLoginFiles[] = 'sysLoginVerify';
        $noLoginFiles[] = 'processes_Ajax';
        $noLoginFiles[] = 'updateTranslation';
        $noLoginFiles[] = 'autoinstallProcesses';
        $noLoginFiles[] = 'autoinstallPlugins';
        $noLoginFiles[] = 'heartbeatStatus';
        $noLoginFiles[] = 'showLogoFile';
        $noLoginFiles[] = 'forgotPassword';
        $noLoginFiles[] = 'retrivePassword';
        $noLoginFiles[] = 'defaultAjaxDynaform';
        $noLoginFiles[] = 'dynaforms_checkDependentFields';

        $noLoginFolders[] = 'services';
        $noLoginFolders[] = 'tracker';
        $noLoginFolders[] = 'installer';

      // This sentence is used when you lost the Session
      if (! in_array(SYS_TARGET, $noLoginFiles)
        && ! in_array(SYS_COLLECTION, $noLoginFolders)
        && $bWE != true && $collectionPlugin != 'services'
        && ! $isRestRequest
      ) {
        $bRedirect = true;

        if (isset($_GET['sid'])) {
          G::LoadClass('sessions');
          $oSessions = new Sessions();
          if ($aSession = $oSessions->verifySession($_GET['sid'])) {
            require_once 'classes/model/Users.php';
            $oUser = new Users();
            $aUser = $oUser->load($aSession['USR_UID']);
            $_SESSION['USER_LOGGED']  = $aUser['USR_UID'];
            $_SESSION['USR_USERNAME'] = $aUser['USR_USERNAME'];
            $bRedirect = false;
            $RBAC->initRBAC();
            $RBAC->loadUserRolePermission( $RBAC->sSystem, $_SESSION['USER_LOGGED'] );
            $memKey = 'rbacSession' . session_id();
            $memcache->set($memKey, $RBAC->aUserInfo, PMmemcached::EIGHT_HOURS );
          }
        }

        if ($bRedirect) {
            if (substr(SYS_SKIN, 0, 2) == 'ux' && SYS_SKIN != 'uxs') {  // verify if the current skin is a 'ux' variant
                $loginUrl = 'main/login';
            } else if (strpos($_SERVER['REQUEST_URI'], '/home') !== false){ //verify is it is using the uxs skin for simplified interface
                $loginUrl = 'home/login';
            } else {
                $loginUrl = 'login/login'; // just set up the classic login
            }

            if (empty($_POST)) {
                header('location: ' . SYS_URI . $loginUrl . '?u=' . urlencode($_SERVER['REQUEST_URI']));
            } else {
                if ($isControllerCall) {
                    header("HTTP/1.0 302 session lost in controller");
                } else {
                  header('location: ' . SYS_URI . $loginUrl);
                }
            }
            die();
        }
      }
    }
}
