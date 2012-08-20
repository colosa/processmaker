<?php
/**
 * cron.php
 * @package workflow-engine-bin
 */
ini_set('display_errors', 1);
error_reporting(E_ALL);
ini_set('memory_limit', '300M'); // nore: this may need to be higher for many projects
$mem_limit = (int) ini_get('memory_limit');

if ( !defined('PATH_SEP') ) {
  define('PATH_SEP', ( substr(PHP_OS, 0, 3) == 'WIN' ) ? '\\' : '/');
}

$docuroot = explode(PATH_SEP, str_replace('engine' . PATH_SEP . 'methods' . PATH_SEP . 'services', '', dirname(__FILE__)));
array_pop($docuroot);
array_pop($docuroot);
$pathhome = implode(PATH_SEP, $docuroot) . PATH_SEP;
//try to find automatically the trunk directory where are placed the RBAC and Gulliver directories
//in a normal installation you don't need to change it.
array_pop($docuroot);
$pathTrunk = implode(PATH_SEP, $docuroot) . PATH_SEP ;
array_pop($docuroot);
$pathOutTrunk = implode( PATH_SEP, $docuroot) . PATH_SEP ;
// to do: check previous algorith for Windows  $pathTrunk = "c:/home/";
define('PATH_HOME',     $pathhome);
define('PATH_TRUNK',    $pathTrunk);
define('PATH_OUTTRUNK', $pathOutTrunk);

require_once (PATH_HOME . 'engine' . PATH_SEP . 'config' . PATH_SEP . 'paths.php');
require_once PATH_CORE . 'classes' . PATH_SEP . 'class.system.php';

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

//default values
$bCronIsRunning = false;
$sLastExecution = '';
if ( file_exists(PATH_DATA . 'cron') ) {
  $aAux = unserialize( trim( @file_get_contents(PATH_DATA . 'cron')) );
  $bCronIsRunning = (boolean)$aAux['bCronIsRunning'];
  $sLastExecution = $aAux['sLastExecution'];
}
else {
  //if not exists the file, just create a new one with current date
  @file_put_contents(PATH_DATA . 'cron', serialize(array('bCronIsRunning' => '1', 'sLastExecution' => date('Y-m-d H:i:s'))));
}

$WS = '';
$argsx = '';
$sDate = '';
$dateSystem = date("Y-m-d H:i:s");

for ($i = 1; $i <= count($argv) - 1; $i++) {
  if( strpos($argv[$i], '+d') !== false){
    $sDate = substr($argv[$i],2);
  } else if( strpos($argv[$i], '+w') !== false){
    $WS = substr($argv[$i],2);
  } else {
    $argsx .= ' '.$argv[$i];
  }
}

//if $sDate is not set, so take the system time
if ($sDate != "") {
    eprintln("[Applying date filter: $sDate]");
} else {
    $sDate = $dateSystem;
}


if( $WS=='' ){
  $oDirectory = dir(PATH_DB);
  $cws = 0;
  while($sObject = $oDirectory->read()) {
    if (($sObject != '.') && ($sObject != '..')) {
      if (is_dir(PATH_DB . $sObject)) {

        if (file_exists(PATH_DB . $sObject . PATH_SEP . 'db.php')) {
          $cws++;
          system("php -f \"".dirname(__FILE__).PATH_SEP."cron_single.php\" $sObject \"$sDate\" \"$dateSystem\" $argsx", $retval);
        }
      }
    }
  }
} else {
  $cws = 1;
  system("php -f \"".dirname(__FILE__).PATH_SEP."cron_single.php\" $WS \"$sDate\" \"$dateSystem\" $argsx", $retval);
}
eprintln("Finished $cws workspaces processed.");

