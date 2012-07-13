<?php
// ProcessMaker Test Unit Bootstrap

// Defining the PATH_SEP constant, he we are defining if the the path separator symbol will be '\\' or '/'
define('PATH_SEP', '/');

if (!defined('__DIR__')) {
  define ('__DIR__', dirname(__FILE__));
}

// Defining the Home Directory
define('PATH_TRUNK', realpath(__DIR__ . '/../') . PATH_SEP);
define('PATH_HOME',  PATH_TRUNK . 'workflow' . PATH_SEP);

define('SYS_SYS', $GLOBALS['SYS_SYS']);
define('SYS_LANG', $GLOBALS['SYS_LANG']);
define('SYS_SKIN', $GLOBALS['SYS_SKIN']);
define('DB_ADAPTER', $GLOBALS['DB_ADAPTER']);
define('DB_NAME', $GLOBALS['DB_NAME']);
define('DB_USER', $GLOBALS['DB_USER']);
define('DB_PASS', $GLOBALS['DB_PASS']);
define('DB_HOST', $GLOBALS['DB_HOST']);
define('PATH_C', PATH_TRUNK . 'tmp' );
define('PATH_SMARTY_C', PATH_TRUNK . 'tmp' );
define('PATH_SMARTY_CACHE', PATH_TRUNK . 'tmp' );

mkdir (PATH_C);
require  PATH_HOME . 'engine' . PATH_SEP . 'config' . PATH_SEP . 'paths.php';

set_include_path(
    PATH_CORE . PATH_SEPARATOR .
    PATH_THIRDPARTY . PATH_SEPARATOR .
    PATH_THIRDPARTY . 'pear'. PATH_SEPARATOR .
    PATH_RBAC_CORE . PATH_SEPARATOR .
    get_include_path()
);

// perpare propel env.
require_once "propel/Propel.php";
require_once "creole/Creole.php";
require_once "pear/Net/JSON.php";
Propel::init( PATH_CORE . "config/databases.php" );

//initialize required classes
G::LoadClass ('dbtable');
G::LoadClass ('system');

//read memcached configuration
$config = System::getSystemConfiguration ('', '', SYS_SYS);
define ('MEMCACHED_ENABLED', $config ['memcached']);
define ('MEMCACHED_SERVER', $config ['memcached_server']);
define ('TIME_ZONE', $config ['time_zone']);