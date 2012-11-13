<?php
/**
 * sysGeneric.php
 *
 * ProcessMaker Open Source Edition
 * Copyright (C) 2004 - 2008 Colosa Inc.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * For more information, contact Colosa Inc, 2566 Le Jeune Rd.,
 * Coral Gables, FL, 33134, USA, or email info@colosa.com.
 *
 */

/**
 * sysGeneric - ProcessMaker Bootstrap
 * this file is used initialize main variables, redirect and dispatch all requests
 */

  // Defining the PATH_SEP constant, he we are defining if the the path separator symbol will be '\\' or '/'
  define('PATH_SEP', '/');

  // Defining the Home Directory
  $realdocuroot = str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']);
  $docuroot = explode(PATH_SEP , $realdocuroot);

  array_pop($docuroot);
  $pathhome = implode(PATH_SEP, $docuroot) . PATH_SEP;

  // try to find automatically the trunk directory where are placed the RBAC and Gulliver directories
  // in a normal installation you don't need to change it.
  array_pop($docuroot);
  $pathTrunk = implode(PATH_SEP, $docuroot) . PATH_SEP ;

  array_pop($docuroot);
  $pathOutTrunk = implode(PATH_SEP, $docuroot) . PATH_SEP ;

  define('PATH_HOME',     $pathhome);
  define('PATH_TRUNK',    $pathTrunk);
  define('PATH_OUTTRUNK', $pathOutTrunk);

  // Including these files we get the PM paths and definitions (that should be just one file.
  require_once $pathhome . PATH_SEP . 'engine' . PATH_SEP . 'config' . PATH_SEP . 'paths.php';
  require_once PATH_CORE . 'classes' . PATH_SEP . 'class.system.php';

  // starting session
  session_start();

  $config = System::getSystemConfiguration();

  $e_all  = defined('E_DEPRECATED') ? E_ALL  & ~E_DEPRECATED : E_ALL;
  $e_all  = defined('E_STRICT')     ? $e_all & ~E_STRICT     : $e_all;
  $e_all  = $config['debug']        ? $e_all                 : $e_all & ~E_NOTICE;

  // Do not change any of these settings directly, use env.ini instead
  ini_set('display_errors', $config['debug']);
  ini_set('error_reporting', $e_all);
  ini_set('short_open_tag', 'On');
  ini_set('default_charset', "UTF-8");
  ini_set('memory_limit', $config['memory_limit']);
  ini_set('soap.wsdl_cache_enabled', $config['wsdl_cache']);
  ini_set('date.timezone', $config['time_zone']);

  define ('DEBUG_SQL_LOG', $config['debug_sql']);
  define ('DEBUG_TIME_LOG', $config['debug_time']);
  define ('DEBUG_CALENDAR_LOG', $config['debug_calendar']);
  define ('MEMCACHED_ENABLED',  $config['memcached']);
  define ('MEMCACHED_SERVER',   $config['memcached_server']);
  define ('TIME_ZONE', $config['time_zone']);

  // IIS Compatibility, SERVER_ADDR doesn't exist on that env, so we need to define it.
  $_SERVER['SERVER_ADDR'] = isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : $_SERVER['SERVER_NAME'];

  //to do: make different environments.  sys

  define ('ERROR_SHOW_SOURCE_CODE', true);  // enable ERROR_SHOW_SOURCE_CODE to display the source code for any WARNING OR NOTICE
  //define ( 'ERROR_LOG_NOTICE_ERROR', true );  //enable ERROR_LOG_NOTICE_ERROR to log Notices messages in default apache log

  //check if it is a installation instance
  if(!defined('PATH_C')) {
    // is a intallation instance, so we need to define PATH_C and PATH_LANGUAGECONT constants temporarily
    define('PATH_C', (rtrim(G::sys_get_temp_dir(), PATH_SEP) . PATH_SEP));
    define('PATH_LANGUAGECONT', PATH_HOME . 'engine/content/languages/' );
  }

  // defining Virtual URLs
  $virtualURITable = array();
  $virtualURITable['/plugin/(*)']    = 'plugin';
  $virtualURITable['/(sys*)/(*.js)'] = 'jsMethod';
  $virtualURITable['/js/(*)']        = PATH_GULLIVER_HOME . 'js/';
  $virtualURITable['/jscore/(*)']    = PATH_CORE . 'js/';

  if ( defined('PATH_C') ) {
    $virtualURITable['/jsform/(*.js)'] = PATH_C . 'xmlform/';
    $virtualURITable['/extjs/(*)']     = PATH_C . 'ExtJs/';
  }

  $virtualURITable['/htmlarea/(*)']                  = PATH_THIRDPARTY . 'htmlarea/';
  $virtualURITable['/sys[a-zA-Z][a-zA-Z0-9]{0,}()/'] = 'sysNamed';
  $virtualURITable['/(sys*)']                        = FALSE;
  $virtualURITable['/errors/(*)']                    = PATH_GULLIVER_HOME . 'methods/errors/';
  $virtualURITable['/gulliver/(*)']                  = PATH_GULLIVER_HOME . 'methods/';
  $virtualURITable['/controls/(*)']                  = PATH_GULLIVER_HOME . 'methods/controls/';
  $virtualURITable['/html2ps_pdf/(*)']               = PATH_THIRDPARTY . 'html2ps_pdf/';
  $virtualURITable['/images/']                       = 'errorFile';
  $virtualURITable['/skins/']                        = 'errorFile';
  $virtualURITable['/files/']                        = 'errorFile';
  $virtualURITable['/[a-zA-Z][a-zA-Z0-9]{0,}()']     = 'sysUnnamed';
  $virtualURITable['/rest/(*)']                      = 'rest-service';
  $virtualURITable['/update/(*)']                    = PATH_GULLIVER_HOME . 'methods/update/';
  $virtualURITable['/(*)']                           = PATH_HTML;

  $isRestRequest = false;

  // Verify if we need to redirect or stream the file, if G:VirtualURI returns true means we are going to redirect the page
  if ( G::virtualURI($_SERVER['REQUEST_URI'], $virtualURITable , $realPath ))
  {
    // review if the file requested belongs to public_html plugin
    if ( substr ( $realPath, 0,6) == 'plugin' ) {
      // Another way to get the path of Plugin public_html and stream the correspondent file, By JHL Jul 14, 08
      // TODO: $pathsQuery will be used?
      $pathsQuery = '';
      // Get the query side
      // Did we use this variable $pathsQuery for something??
      $forQuery = explode("?",$realPath);
      if (isset($forQuery[1])) {
        $pathsQuery = $forQuery[1];
      }

      //Get that path in array
      $paths          = explode ( PATH_SEP, $forQuery[0] );
      //remove the "plugin" word from
      $paths[0]       = substr ( $paths[0],6);
      //Get the Plugin Folder, always the first element
      $pluginFolder   = array_shift($paths);
      //The other parts are the realpath into public_html (no matter how many elements)
      $filePath       = implode(PATH_SEP,$paths);
      $pluginFilename = PATH_PLUGINS . $pluginFolder . PATH_SEP . 'public_html'. PATH_SEP . $filePath;

      if ( file_exists ( $pluginFilename ) ) {
        G::streamFile ( $pluginFilename );
      }
      die;
    }

    $requestUriArray = explode("/",$_SERVER['REQUEST_URI']);

    if((isset($requestUriArray[1]))&&($requestUriArray[1] == 'skin')) {
      // This will allow to public images of Custom Skins, By JHL Feb 28, 11
      $pathsQuery="";
      // Get the query side
      // This way we remove garbage
      $forQuery = explode("?",$realPath);
      if (isset($forQuery[1])) {
        $pathsQuery = $forQuery[1];
      }

      //Get that path in array
      $paths = explode ( PATH_SEP, $forQuery[0] );
      $fileToBeStreamed=str_replace("/skin/",PATH_CUSTOM_SKINS,$_SERVER['REQUEST_URI']);

      if ( file_exists ( $fileToBeStreamed ) ) {
        G::streamFile ( $fileToBeStreamed );
      }
      die;
    }
    switch ($realPath) {
      case 'sysUnnamed' :
        require_once('sysUnnamed.php');
        die;
        break;
      case 'sysNamed' :
        header('location : ' . $_SERVER['REQUEST_URI'] . '/' .SYS_LANG. '/classic/login/login' );
        die;
        break;
      case 'jsMethod' :
        G::parseURI ( getenv( "REQUEST_URI" ) );
        $filename = PATH_METHODS . SYS_COLLECTION . '/' . SYS_TARGET . '.js';
        G::streamFile ( $filename );
        die;
        break;
      case 'errorFile':
        header ("location: /errors/error404.php?url=" . urlencode($_SERVER['REQUEST_URI']));
        if ( DEBUG_TIME_LOG ) G::logTimeByPage(); //log this page
        die;
        break;
      default :
        if (substr($realPath, 0, 12) == 'rest-service') {
          $isRestRequest = true;
        } else {
          $realPath = explode('?', $realPath);
          $realPath[0] .= strpos(basename($realPath[0]), '.') === false ? '.php' : '';
          G::streamFile ( $realPath[0] );
          die;
        }
    }
  }//virtual URI parser

  // the request correspond to valid php page, now parse the URI
  G::parseURI(getenv("REQUEST_URI"), $isRestRequest);

  if(G::isPMUnderUpdating())
  {
      header("location: /update/updating.php");
      if ( DEBUG_TIME_LOG ) G::logTimeByPage();
      die;
  }

  // verify if index.html exists
  if (!file_exists(PATH_HTML . 'index.html')) { // if not, create it from template
    file_put_contents(
      PATH_HTML . 'index.html',
      G::parseTemplate(PATH_TPL . 'index.html', array('lang' => SYS_LANG, 'skin' => SYS_SKIN))
    );
  }

  define('SYS_URI' , '/sys' .  SYS_TEMP . '/' . SYS_LANG . '/' . SYS_SKIN . '/');

  // defining the serverConf singleton
  if (defined('PATH_DATA') && file_exists(PATH_DATA)) {
    //Instance Server Configuration Singleton
    G::LoadClass('serverConfiguration');
    $oServerConf =& serverConf::getSingleton();
  }

  // Call Gulliver Classes
  G::LoadThirdParty('pear/json','class.json');
  G::LoadThirdParty('smarty/libs','Smarty.class');
  G::LoadSystem('error');
  G::LoadSystem('dbconnection');
  G::LoadSystem('dbsession');
  G::LoadSystem('dbrecordset');
  G::LoadSystem('dbtable');
  G::LoadSystem('rbac' );
  G::LoadSystem('publisher');
  G::LoadSystem('templatePower');
  G::LoadSystem('xmlDocument');
  G::LoadSystem('xmlform');
  G::LoadSystem('xmlformExtension');
  G::LoadSystem('form');
  G::LoadSystem('menu');
  G::LoadSystem("xmlMenu");
  G::LoadSystem('dvEditor');
  G::LoadSystem('controller');
  G::LoadSystem('httpProxyController');
  G::LoadSystem('pmException');

  // Create headPublisher singleton
  G::LoadSystem('headPublisher');
  $oHeadPublisher =& headPublisher::getSingleton();

  // Installer, redirect to install if we don't have a valid shared data folder
  if ( !defined('PATH_DATA') || !file_exists(PATH_DATA)) {

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
    die;
  }

  // Load Language Translation
  G::LoadTranslationObject(defined('SYS_LANG')?SYS_LANG:"en");

  // look for a disabled workspace
  if($oServerConf->isWSDisabled(SYS_TEMP)){
    $aMessage['MESSAGE'] = G::LoadTranslation('ID_DISB_WORKSPACE');
    $G_PUBLISH           = new Publisher;
    $G_PUBLISH->AddContent('xmlform', 'xmlform', 'login/showMessage', '', $aMessage );
    G::RenderPage( 'publish' );
    die;
  }

  // database and workspace definition
  // if SYS_TEMP exists, the URL has a workspace, now we need to verify if exists their db.php file
  if ( defined('SYS_TEMP') && SYS_TEMP != '')  {
    //this is the default, the workspace db.php file is in /shared/workflow/sites/SYS_SYS
    if ( file_exists( PATH_DB .  SYS_TEMP . '/db.php' ) ) {
      require_once( PATH_DB .  SYS_TEMP . '/db.php' );
      define ( 'SYS_SYS' , SYS_TEMP );

      // defining constant for workspace shared directory
      define ( 'PATH_WORKSPACE' , PATH_DB . SYS_SYS . PATH_SEP );
      // including workspace shared classes -> particularlly for pmTables
      set_include_path(get_include_path() . PATH_SEPARATOR . PATH_WORKSPACE);
    }
    else {
      G::SendTemporalMessage ('ID_NOT_WORKSPACE', "error");
      G::header('location: /sys/' . SYS_LANG . '/' . SYS_SKIN . '/main/sysLogin?errno=2');
      die;
    }
  }
  else {  //when we are in global pages, outside any valid workspace
    if (SYS_TARGET==='newSite') {
      $phpFile = G::ExpandPath('methods') . SYS_COLLECTION . "/" . SYS_TARGET.'.php';
      require_once($phpFile);
      die();
    }
    else {
      if(SYS_TARGET=="dbInfo"){ //Show dbInfo when no SYS_SYS
          require_once( PATH_METHODS . "login/dbInfo.php" );
      }
      else{

        if (substr(SYS_SKIN, 0, 2) === 'ux' && SYS_TARGET != 'sysLoginVerify') { // new ux sysLogin - extjs based form
          require_once PATH_CONTROLLERS . 'main.php';
          $controllerClass  = 'Main';
          $controllerAction = SYS_TARGET == 'sysLoginVerify' ? SYS_TARGET : 'sysLogin';
          //if the method exists
          if( is_callable(Array($controllerClass, $controllerAction)) ) {
            $controller = new $controllerClass();
            $controller->setHttpRequestData($_REQUEST);
            $controller->call($controllerAction);
          }
        }
        else { // classic sysLogin interface
          require_once( PATH_METHODS . "login/sysLogin.php" ) ;
          die();
        }
      }
      if ( DEBUG_TIME_LOG ) G::logTimeByPage(); //log this page
      die();
    }
  }

  // PM Paths DATA
  define('PATH_DATA_SITE',                 PATH_DATA      . 'sites/' . SYS_SYS . '/');
  define('PATH_DOCUMENT',                  PATH_DATA_SITE . 'files/');
  define('PATH_DATA_MAILTEMPLATES',        PATH_DATA_SITE . 'mailTemplates/');
  define('PATH_DATA_PUBLIC',               PATH_DATA_SITE . 'public/');
  define('PATH_DATA_REPORTS',              PATH_DATA_SITE . 'reports/');
  define('PATH_DYNAFORM',                  PATH_DATA_SITE . 'xmlForms/');
  define('PATH_IMAGES_ENVIRONMENT_FILES',  PATH_DATA_SITE . 'usersFiles'.PATH_SEP);
  define('PATH_IMAGES_ENVIRONMENT_USERS',  PATH_DATA_SITE . 'usersPhotographies'.PATH_SEP);
  define('SERVER_NAME',  $_SERVER ['SERVER_NAME']);
  define('SERVER_PORT',  $_SERVER ['SERVER_PORT']);

  // create memcached singleton
  G::LoadClass ( 'memcached' );
  $memcache = & PMmemcached::getSingleton(SYS_SYS);

  // verify configuration for rest service
  if ($isRestRequest) {
      // disable until confirm that rest is enabled & configured on rest-config.ini file
      $isRestRequest = false;
      $confFile = '';
      $restApiClassPath = '';

      // try load and getting rest configuration
      if (file_exists(PATH_DATA_SITE . 'rest-config.ini')) {
          $confFile = PATH_DATA_SITE . 'rest-config.ini';
          $restApiClassPath = PATH_DATA_SITE;
      } elseif (file_exists(PATH_CONFIG . 'rest-config.ini')) {
          $confFile = PATH_CONFIG . 'rest-config.ini';
      }
      if (! empty($confFile) && $restConfig = @parse_ini_file($confFile, true)) {
          if (array_key_exists('enable_service', $restConfig)) {
              if ($restConfig['enable_service'] == 'true' || $restConfig['enable_service'] == '1') {
                  $isRestRequest = true; // rest service enabled
              }
          }
      }
  }

  // load Plugins base class
  G::LoadClass('plugin');

  //here we are loading all plugins registered
  //the singleton has a list of enabled plugins
  $sSerializedFile = PATH_DATA_SITE . 'plugin.singleton';
  $oPluginRegistry =& PMPluginRegistry::getSingleton();

  if (file_exists ($sSerializedFile)) {
    $oPluginRegistry->unSerializeInstance(file_get_contents($sSerializedFile));
  }

  // setup propel definitions and logging
  require_once ( "propel/Propel.php" );
  require_once ( "creole/Creole.php" );

  if (defined('DEBUG_SQL_LOG') && DEBUG_SQL_LOG) {
    define('PM_PID', mt_rand(1,999999));
    require_once 'Log.php';

    // register debug connection decorator driver
    Creole::registerDriver('*', 'creole.contrib.DebugConnection');

    // initialize Propel with converted config file
    Propel::init( PATH_CORE . "config/databases.php" );

    // unified log file for all databases
    $logFile = PATH_DATA . 'log' . PATH_SEP . 'propel.log';
    $logger = Log::singleton('file', $logFile, 'wf ' . SYS_SYS, null, PEAR_LOG_INFO);
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
  }
  else {
    Propel::init( PATH_CORE . "config/databases.php" );
  }

  Creole::registerDriver('dbarray', 'creole.contrib.DBArrayConnection');

  // Session Initializations
  ini_set('session.auto_start', '1');

  // The register_globals feature has been DEPRECATED as of PHP 5.3.0. default value Off.
  // ini_set( 'register_globals', 'Off' );
  //session_start();
  ob_start();

  // Rebuild the base Workflow translations if not exists
  if( ! is_file(PATH_LANGUAGECONT . 'translation.en') ){
    require_once ( "classes/model/Translation.php" );
    $fields = Translation::generateFileTranslation('en');
  }

  // TODO: Verify if the language set into url is defined in translations env.
  if( SYS_LANG != 'en' && ! is_file(PATH_LANGUAGECONT . 'translation.' . SYS_LANG) ){
    require_once ( "classes/model/Translation.php" );
    $fields = Translation::generateFileTranslation(SYS_LANG);
  }

  // Setup plugins
  $oPluginRegistry->setupPlugins(); //get and setup enabled plugins
  $avoidChangedWorkspaceValidation = false;

  // Load custom Classes and Model from Plugins.
  G::LoadAllPluginModelClasses();

  // jump to php file in methods directory
  $collectionPlugin = '';
  if ($oPluginRegistry->isRegisteredFolder(SYS_COLLECTION)) {
    $phpFile = PATH_PLUGINS . SYS_COLLECTION . PATH_SEP . SYS_TARGET.'.php';
    $targetPlugin = explode( '/', SYS_TARGET );
    $collectionPlugin = $targetPlugin[0];
    $avoidChangedWorkspaceValidation = true;
  }
  else {
    $phpFile = G::ExpandPath('methods') . SYS_COLLECTION . PATH_SEP . SYS_TARGET.'.php';
  }

  // services is a special folder,
  if ( SYS_COLLECTION == 'services' ) {
    $avoidChangedWorkspaceValidation = true;
    $targetPlugin = explode( '/', SYS_TARGET );

    if ( $targetPlugin[0] == 'webdav' ) {
      $phpFile = G::ExpandPath('methods') . SYS_COLLECTION . PATH_SEP . 'webdav.php';
    }
  }

  if (SYS_COLLECTION == 'login' && SYS_TARGET == 'login') {
    $avoidChangedWorkspaceValidation = true;
  }

  //the index.php file, this new feature will allow automatically redirects to valid php file inside any methods folder
  /* DEPRECATED
  if ( SYS_TARGET == '' ) {
    $phpFile = str_replace ( '.php', 'index.php', $phpFile );
    $phpFile = include ( $phpFile );
  }*/
  $bWE = false;
  $isControllerCall = false;
  if ( substr(SYS_COLLECTION , 0,8) === 'gulliver' ) {
    $phpFile = PATH_GULLIVER_HOME . 'methods/' . substr( SYS_COLLECTION , 8) . SYS_TARGET.'.php';
  }
  else {
    //when the file is part of the public directory of any PROCESS, this a ProcessMaker feature
    if (preg_match('/^[0-9][[:alnum:]]+$/', SYS_COLLECTION) == 1) { //the pattern is /sysSYS/LANG/SKIN/PRO_UID/file
      $auxPart = explode ( '/' ,  $_SERVER['REQUEST_URI']);
      $aAux = explode('?', $auxPart[ count($auxPart)-1]);
      //$extPart = explode ( '.' , $auxPart[ count($auxPart)-1] );
      $extPart = explode ( '.' , $aAux[0] );
      $queryPart = isset($aAux[1])?$aAux[1]:"";
      $extension = $extPart[ count($extPart)-1 ];
      $phpFile = PATH_DATA_SITE . 'public' . PATH_SEP .  SYS_COLLECTION . PATH_SEP . urldecode ($auxPart[ count($auxPart)-1]);
      $aAux = explode('?', $phpFile);
      $phpFile = $aAux[0];

      if ($extension != 'php') {
        G::streamFile($phpFile);
        die;
      }

      $avoidChangedWorkspaceValidation=true;
      $bWE = true;
      //$phpFile = PATH_DATA_SITE . 'public' . PATH_SEP .  SYS_COLLECTION . PATH_SEP . $auxPart[ count($auxPart)-1];
    }

    //erik: verify if it is a Controller Class or httpProxyController Class
    if (is_file(PATH_CONTROLLERS . SYS_COLLECTION . '.php')) {
      require_once PATH_CONTROLLERS . SYS_COLLECTION . '.php';
      $controllerClass  = SYS_COLLECTION;
      //if the method name is empty set default to index method
      $controllerAction = SYS_TARGET != '' ? SYS_TARGET : 'index';
      //if the method exists
      if (is_callable(Array($controllerClass, $controllerAction)) ) {
        $isControllerCall = true;
      }
    }

    if (!$isControllerCall && ! file_exists($phpFile) && ! $isRestRequest) {
      $_SESSION['phpFileNotFound'] = $_SERVER['REQUEST_URI'];
      header("location: /errors/error404.php?url=" . urlencode($_SERVER['REQUEST_URI']));
      die;
    }
  }

  //redirect to login, if user changed the workspace in the URL
  if (! $avoidChangedWorkspaceValidation && isset($_SESSION['WORKSPACE']) && $_SESSION['WORKSPACE'] != SYS_SYS) {
    $_SESSION['WORKSPACE'] = SYS_SYS;
    G::SendTemporalMessage ('ID_USER_HAVENT_RIGHTS_SYSTEM', "error");
    // verify if the current skin is a 'ux' variant
    $urlPart = substr(SYS_SKIN, 0, 2) == 'ux' && SYS_SKIN != 'uxs' ? '/main/login' : '/login/login';

    header('Location: /sys' . SYS_SYS . '/' . SYS_LANG . '/' . SYS_SKIN . $urlPart);
    die;
  }

  // enable rbac
  $RBAC = &RBAC::getSingleton( PATH_DATA, session_id() );
  $RBAC->sSystem = 'PROCESSMAKER';

  // define and send Headers for all pages
  if (! defined('EXECUTE_BY_CRON')) {
    header("Expires: " . gmdate("D, d M Y H:i:s", mktime( 0,0,0,date('m'),date('d')-1,date('Y') ) ) . " GMT");
    header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
    header("Cache-Control: no-store, no-cache, must-revalidate");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");

    // get the language direction from ServerConf
    define('SYS_LANG_DIRECTION', $oServerConf->getLanDirection() );

    if((isset( $_SESSION['USER_LOGGED'] ))&&(!(isset($_GET['sid'])))) {
      $RBAC->initRBAC();
      //using optimization with memcache, the user data will be in memcache 8 hours, or until session id goes invalid
      $memKey = 'rbacSession' . session_id();
      if ( ($RBAC->aUserInfo = $memcache->get($memKey)) === false ) {
        $RBAC->loadUserRolePermission( $RBAC->sSystem, $_SESSION['USER_LOGGED'] );
        $memcache->set( $memKey, $RBAC->aUserInfo, PMmemcached::EIGHT_HOURS );
      }
    }
    else {
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
      $noLoginFiles[] = 'genericAjax';

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
          }
          else if (strpos($_SERVER['REQUEST_URI'], '/home') !== false){ //verify is it is using the uxs skin for simplified interface
            $loginUrl = 'home/login';
          }
          else {
            $loginUrl = 'login/login'; // just set up the classic login
          }

          if (empty($_POST)) {
            header('location: ' . SYS_URI . $loginUrl . '?u=' . urlencode($_SERVER['REQUEST_URI']));

          }
          else {
            if ($isControllerCall) {
      		    header("HTTP/1.0 302 session lost in controller");
            }
            else {
              header('location: ' . SYS_URI . $loginUrl);
            }
          }
          die();
        }
      }
    }
    $_SESSION['phpLastFileFound'] = $_SERVER['REQUEST_URI'];

    /**
     * New feature for Gulliver framework to support Controllers & HttpProxyController classes handling
     * @author Erik Amaru Ortiz <erik@colosa.com, aortiz.erik@gmail.com>
     */
    if ($isControllerCall) { //Instance the Controller object and call the request method
      $controller = new $controllerClass();
      $controller->setHttpRequestData($_REQUEST);
      $controller->call($controllerAction);
    } elseif ($isRestRequest) {
      G::dispatchRestService(SYS_TARGET, $restConfig, $restApiClassPath);
    } else {
      require_once $phpFile;
    }

    if (defined('SKIP_HEADERS')){
      header("Expires: " . gmdate("D, d M Y H:i:s", mktime(0, 0, 0, date('m'), date('d'), date('Y') + 1)) . " GMT");
      header('Cache-Control: public');
      header('Pragma: ');
    }

    ob_end_flush();
    if (DEBUG_TIME_LOG) {
      G::logTimeByPage(); //log this page
    }
  }
