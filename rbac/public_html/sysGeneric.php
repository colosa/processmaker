<?php
  //sysGeneric, to redirect workspace, the url should by encrypted or not

//***************** URL KEY *********************************************
  define("URL_KEY", 'c0l0s40pt1mu59r1m3' );

//***************** System Directories & Paths **************************
  define('PATH_SEP', '/');

  // Defining the Home
  $docuroot = explode ('/', $_SERVER["DOCUMENT_ROOT"]);
  array_pop($docuroot);
  $pathhome = implode( PATH_SEP, $docuroot );
  define('PATH_HOME', $pathhome . PATH_SEP );


  //try to find automatically the RBAC and Gulliver directories
  //in a normal installation you don't need to change it.
  array_pop($docuroot);
  $pathTrunk = implode( PATH_SEP, $docuroot ) . PATH_SEP ;
//  $pathTrunk = "c:/home/";


  define( 'PATH_GULLIVER_HOME', $pathTrunk . 'gulliver' . PATH_SEP );
  define( 'PATH_RBAC_HOME',     $pathTrunk . 'rbac' . PATH_SEP );

  define( 'PATH_GULLIVER',   PATH_GULLIVER_HOME . 'system' . PATH_SEP );   //gulliver system classes
  define( 'PATH_TEMPLATE',   PATH_GULLIVER_HOME . 'templates' . PATH_SEP );
  define( 'PATH_THIRDPARTY', $pathTrunk . 'thirdparty' . PATH_SEP );
  define( 'PATH_RBAC',      PATH_RBAC_HOME . 'engine/classes' . PATH_SEP );  //to enable rbac version 2
  define( 'PATH_DATA',          '/shared/rbac/');
  define( 'PATH_HTML',      PATH_HOME . 'public_html' . PATH_SEP );

  ini_set('display_errors','On');
  require_once( PATH_GULLIVER . PATH_SEP . 'class.g.php');
  //********************* SMARTY PATHS *************************************
  if ( strstr ( getenv ( 'OS' ), 'Windows' ) ) {
    define( 'PATH_SMARTY_C',       'c:/tmp/smarty/c' );
    define( 'PATH_SMARTY_CACHE',   'c:/tmp/smarty/cache' );
  }
  else {
    define( 'PATH_SMARTY_C',       PATH_DATA . 'smarty/c' );
    define( 'PATH_SMARTY_CACHE',   PATH_DATA . 'smarty/cache' );
  }

//************ set to the default Gulliver error handler and Fatal error handler **************
  //G::setErrorHandler ( );
  //G::setFatalErrorHandler ( );
  /*** enable display_error On to caught even fatal errors ***/
  ini_set('display_errors','On');
  /*** enable ERROR_SHOW_SOURCE_CODE to display the source code for any WARNING OR NOTICE ***/
  define ('ERROR_SHOW_SOURCE_CODE', true);
  /*** enable ERROR_LOG_NOTICE_ERROR to log Notices messages in default apache log ***/
//  define ( 'ERROR_LOG_NOTICE_ERROR', true );

/* Virtual URLs */
$virtualURITable = array();
$virtualURITable['/js/(*)'] = PATH_GULLIVER_HOME . 'js/';
$virtualURITable['/jsform/(*.js)'] = PATH_DATA . 'xmlform/';
$virtualURITable['/(sys*)'] = FALSE;
//$virtualURITable['/jsprocessform/(*.js)'] = 'PROCESSFORMS';
/*To sysUnnamed*/
$virtualURITable['/[a-zA-Z][a-zA-Z0-9]{0,}()'] = 'sysUnnamed';
$virtualURITable['/(*)'] = PATH_HTML;

//************** verify if we need to redirect or stream the file **************
  if (G::virtualURI($_SERVER["REQUEST_URI"], $virtualURITable , $realPath )) {
    if ($realPath==='sysUnnamed') {
      require_once('sysUnnamed.php');die;
    } else {
      $realPath = explode('?', $realPath);
    	G::streamFile ( $realPath[0] );
    	die;
    }
  }

//************** verify if the URI is encrypted or not **************
  G::parseURI ( getenv( "REQUEST_URI" ) );

  //print '-' . SYS_TEMP .'-' . SYS_LANG. '-' . SYS_SKIN.'-' . SYS_COLLECTION.'-' . SYS_TARGET.'<br>';


  ini_alter( "include_path", "." );
  ini_set( "include_path",  PATH_GULLIVER_HOME . 'thirdparty/pear' );
  require_once ( "../engine/pre_processor.php" );
?>