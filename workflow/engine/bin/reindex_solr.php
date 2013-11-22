<?php
/**
 * 
 * ProcessMaker Open Source Edition
 * Copyright (C) 2004 - 2012 Colosa Inc.23
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 * For more information, contact Colosa Inc, 5304 Ventura Drive,
 * Delray Beach, FL, 33484, USA, or email info@colosa.com.
 * 
 */

// check script parameters
// php reindex_solr.php workspacename [reindexall|reindexmissing|optimizeindex] [-skip 1005] [-reindextrunksize 1000]
// var_dump($argv);
//(count ($argv) == 4) || ((count ($argv) == 5) && ($argv [3] != '-skip'))
$commandLineSyntaxMsg = "Invalid command line arguments: \n " .
  "syntax: ".
  "php reindex_solr.php [workspace_name] [reindexall|reindexmissing|optimizeindex|reindexone] [-skip {record_number}] [-reindextrunksize {trunk_size}] [-appuid {APP_UID}]\n" . 
  " Where \n".
  "       reindexall : reindex all the database. \n" . 
  "       reindexmissing: reindex only the missing records stored in database. \n".
  "                     (records defined in APP_SOLR_QUEUE table are required)\n" .
  "       optimizeindex: optimize the changes in the search index. (used to get faster results) \n" .
  " Optional Options: \n" .
  " -skip {record_number}: used to skip a number of records. \n ex: -skip 10000 //skips the first 10000 records. \n" .
  " -reindextrunksize {trunk_size}: specify the number of records sent to index each time. \n ex: -reindextrunksize 100 //(default = 1000) \n Reduce the trunk if using big documents, and memory is not enough. \n"; 

if ( (count ($argv) < 3) || ((count ($argv) % 2) == 0) || 
    ($argv [2] != 'reindexall' && $argv [2] != 'reindexmissing' && $argv [2] != 'optimizeindex'  && $argv [2] != 'reindexone')) {    
  print $commandLineSyntaxMsg;
  die ();
}
$workspaceName = $argv [1];
$ScriptAction = $argv [2];
$SkipRecords = 0;
$TrunkSize = 1000;
$appUid = "";
//3 5 7
if(count ($argv) > 3) {
  for($argNumber = 3 ; $argNumber < count ($argv) ; $argNumber += 2) {
    if(($argv [$argNumber] == '-skip' || $argv [$argNumber] == '-reindextrunksize' || $argv [$argNumber] == '-appuid')) {
      //get options
      if($argv [$argNumber] == '-skip') {
        //use skip option
        $SkipRecords = intval($argv [$argNumber + 1]);
      }
      if($argv [$argNumber] == '-reindextrunksize') {
        //use skip option
        $TrunkSize = intval($argv [$argNumber + 1]);
      }
      if($argv [$argNumber] == '-appuid') {
        //use skip option
        $appUid = $argv [$argNumber + 1];
      }      
    }
    else {
      print $commandLineSyntaxMsg;
      die ();
    }
  }
}

$debug = 1;

ini_set ('display_errors', 1);
//error_reporting (E_ALL);
ini_set ('memory_limit', '256M'); // set enough memory for the script

$e_all = defined( 'E_DEPRECATED' ) ? E_ALL & ~ E_DEPRECATED : E_ALL;
$e_all = defined( 'E_STRICT' ) ? $e_all & ~ E_STRICT : $e_all;
$e_all = $debug ? $e_all : $e_all & ~ E_NOTICE;

ini_set( 'error_reporting', $e_all );


if (! defined ('SYS_LANG')) {
  define ('SYS_LANG', 'en');
}

if (! defined ('PATH_HOME')) {
  if (! defined ('PATH_SEP')) {
    define ('PATH_SEP', (substr (PHP_OS, 0, 3) == 'WIN') ? '\\' : '/');
  }
  $docuroot = explode (PATH_SEP, str_replace ('engine' . PATH_SEP . 'methods' . PATH_SEP . 'services', '', dirname (__FILE__)));
  array_pop ($docuroot);
  array_pop ($docuroot);
  $pathhome = implode (PATH_SEP, $docuroot) . PATH_SEP;
  // try to find automatically the trunk directory where are placed the RBAC and
  // Gulliver directories
  // in a normal installation you don't need to change it.
  array_pop ($docuroot);
  $pathTrunk = implode (PATH_SEP, $docuroot) . PATH_SEP;
  array_pop ($docuroot);
  $pathOutTrunk = implode (PATH_SEP, $docuroot) . PATH_SEP;
  // to do: check previous algorith for Windows $pathTrunk = "c:/home/";
  
  define ('PATH_HOME', $pathhome);
  define ('PATH_TRUNK', $pathTrunk);
  define ('PATH_OUTTRUNK', $pathOutTrunk);
  
  require_once (PATH_HOME . 'engine' . PATH_SEP . 'config' . PATH_SEP . 'paths.php');
  require_once (PATH_GULLIVER . "class.bootstrap.php");
  Bootstrap::registerSystemClasses();
  spl_autoload_register(array('Bootstrap', 'autoloadClass'));
  
  G::LoadThirdParty ('pear/json', 'class.json');
  G::LoadThirdParty ('smarty/libs', 'Smarty.class');
  G::LoadSystem ('error');
  G::LoadSystem ('dbconnection');
  G::LoadSystem ('dbsession');
  G::LoadSystem ('dbrecordset');
  G::LoadSystem ('dbtable');
  G::LoadSystem ('rbac');
  G::LoadSystem ('publisher');
  G::LoadSystem ('templatePower');
  G::LoadSystem ('xmlDocument');
  G::LoadSystem ('xmlform');
  G::LoadSystem ('xmlformExtension');
  G::LoadSystem ('form');
  G::LoadSystem ('menu');
  G::LoadSystem ("xmlMenu");
  G::LoadSystem ('dvEditor');
  G::LoadSystem ('table');
  G::LoadSystem ('pagedTable');
  G::LoadClass ('system');
  require_once ("propel/Propel.php");
  require_once ("creole/Creole.php");
}

require_once 'classes/model/AppDelegation.php';
require_once 'classes/model/Event.php';
require_once 'classes/model/AppEvent.php';
require_once 'classes/model/CaseScheduler.php';
// G::loadClass('pmScript');

// //default values
// $bCronIsRunning = false;
// $sLastExecution = '';
// if ( file_exists(PATH_DATA . 'cron') ) {
// $aAux = unserialize( trim( @file_get_contents(PATH_DATA . 'cron')) );
// $bCronIsRunning = (boolean)$aAux['bCronIsRunning'];
// $sLastExecution = $aAux['sLastExecution'];
// }
// else {
// //if not exists the file, just create a new one with current date
// @file_put_contents(PATH_DATA . 'cron', serialize(array('bCronIsRunning' =>
// '1', 'sLastExecution' => date('Y-m-d H:i:s'))));
// }

print "PATH_HOME: " . PATH_HOME . "\n";
print "PATH_DB: " . PATH_DB . "\n";
print "PATH_CORE: " . PATH_CORE . "\n";

// define the site name (instance name)
if (! defined ('SYS_SYS')) {
  $sObject = $workspaceName;
  $sNow = ''; // $argv[2];
  $sFilter = '';
  
  for ($i = 3; $i < count ($argv); $i++) {
    $sFilter .= ' ' . $argv [$i];
  }
  
  $oDirectory = dir (PATH_DB);
  
  if (is_dir (PATH_DB . $sObject)) {
    saveLog ('main', 'action', "checking folder " . PATH_DB . $sObject);
    if (file_exists (PATH_DB . $sObject . PATH_SEP . 'db.php')) {
      
      define ('SYS_SYS', $sObject);
      
      // ****************************************
      // read initialize file
      require_once PATH_HOME . 'engine' . PATH_SEP . 'classes' . PATH_SEP . 'class.system.php';
      $config = System::getSystemConfiguration ('', '', SYS_SYS);
      define ('MEMCACHED_ENABLED', $config ['memcached']);
      define ('MEMCACHED_SERVER', $config ['memcached_server']);
      define ('TIME_ZONE', $config ['time_zone']);
      
      date_default_timezone_set (TIME_ZONE);
      print "TIME_ZONE: " . TIME_ZONE . "\n";
      print "MEMCACHED_ENABLED: " . MEMCACHED_ENABLED . "\n";
      print "MEMCACHED_SERVER: " . MEMCACHED_SERVER . "\n";
      // ****************************************
      
      include_once (PATH_HOME . 'engine' . PATH_SEP . 'config' . PATH_SEP . 'paths_installed.php');
      include_once (PATH_HOME . 'engine' . PATH_SEP . 'config' . PATH_SEP . 'paths.php');
      
      // ***************** PM Paths DATA **************************
      define ('PATH_DATA_SITE', PATH_DATA . 'sites/' . SYS_SYS . '/');
      define ('PATH_DOCUMENT', PATH_DATA_SITE . 'files/');
      define ('PATH_DATA_MAILTEMPLATES', PATH_DATA_SITE . 'mailTemplates/');
      define ('PATH_DATA_PUBLIC', PATH_DATA_SITE . 'public/');
      define ('PATH_DATA_REPORTS', PATH_DATA_SITE . 'reports/');
      define ('PATH_DYNAFORM', PATH_DATA_SITE . 'xmlForms/');
      define ('PATH_IMAGES_ENVIRONMENT_FILES', PATH_DATA_SITE . 'usersFiles' . PATH_SEP);
      define ('PATH_IMAGES_ENVIRONMENT_USERS', PATH_DATA_SITE . 'usersPhotographies' . PATH_SEP);
      
      // server info file
      if (is_file (PATH_DATA_SITE . PATH_SEP . '.server_info')) {
        $SERVER_INFO = file_get_contents (PATH_DATA_SITE . PATH_SEP . '.server_info');
        $SERVER_INFO = unserialize ($SERVER_INFO);
        // print_r($SERVER_INFO);
        define ('SERVER_NAME', $SERVER_INFO ['SERVER_NAME']);
        define ('SERVER_PORT', $SERVER_INFO ['SERVER_PORT']);
      }
      else {
        eprintln ("WARNING! No server info found!", 'red');
      }
      
      // read db configuration
      $sContent = file_get_contents (PATH_DB . $sObject . PATH_SEP . 'db.php');
      
      $sContent = str_replace ('<?php', '', $sContent);
      $sContent = str_replace ('<?', '', $sContent);
      $sContent = str_replace ('?>', '', $sContent);
      $sContent = str_replace ('define', '', $sContent);
      $sContent = str_replace ("('", "$", $sContent);
      $sContent = str_replace ("',", '=', $sContent);
      $sContent = str_replace (");", ';', $sContent);
      
      eval ($sContent);
      $dsn = $DB_ADAPTER . '://' . $DB_USER . ':' . $DB_PASS . '@' . $DB_HOST . '/' . $DB_NAME;
      $dsnRbac = $DB_ADAPTER . '://' . $DB_RBAC_USER . ':' . $DB_RBAC_PASS . '@' . $DB_RBAC_HOST . '/' . $DB_RBAC_NAME;
      $dsnRp = $DB_ADAPTER . '://' . $DB_REPORT_USER . ':' . $DB_REPORT_PASS . '@' . $DB_REPORT_HOST . '/' . $DB_REPORT_NAME;
      switch ($DB_ADAPTER) {
        case 'mysql' :
          $dsn .= '?encoding=utf8';
          $dsnRbac .= '?encoding=utf8';
          break;
        case 'mssql' :
          // $dsn .= '?sendStringAsUnicode=false';
          // $dsnRbac .= '?sendStringAsUnicode=false';
          break;
        default :
          break;
      }
      // initialize db
      $pro ['datasources'] ['workflow'] ['connection'] = $dsn;
      $pro ['datasources'] ['workflow'] ['adapter'] = $DB_ADAPTER;
      $pro ['datasources'] ['rbac'] ['connection'] = $dsnRbac;
      $pro ['datasources'] ['rbac'] ['adapter'] = $DB_ADAPTER;
      $pro ['datasources'] ['rp'] ['connection'] = $dsnRp;
      $pro ['datasources'] ['rp'] ['adapter'] = $DB_ADAPTER;
      // $pro['datasources']['dbarray']['connection'] =
      // 'dbarray://user:pass@localhost/pm_os';
      // $pro['datasources']['dbarray']['adapter'] = 'dbarray';
      $oFile = fopen (PATH_CORE . 'config/_databases_.php', 'w');
      fwrite ($oFile, '<?php global $pro;return $pro; ?>');
      fclose ($oFile);
      Propel::init (PATH_CORE . 'config/_databases_.php');
      // Creole::registerDriver('dbarray', 'creole.contrib.DBArrayConnection');
      
      eprintln ("Processing workspace: " . $sObject, 'green');
      try {
        processWorkspace ();
      }
      catch (Exception $e) {
        echo $e->getMessage ();
        eprintln ("Problem in workspace: " . $sObject . ' it was omitted.', 'red');
      }
      eprintln ();
      unlink (PATH_CORE . 'config/_databases_.php');
    }
  }

}
else {
  processWorkspace ();
}

// finally update the file
// @file_put_contents(PATH_DATA . 'cron', serialize(array('bCronIsRunning' =>
// '0', 'sLastExecution' => date('Y-m-d H:i:s'))));

function processWorkspace()
{
  global $sLastExecution;
  global $ScriptAction;
  global $SkipRecords;
  global $TrunkSize;
  global $appUid;
  
  try {
    
    if (($solrConf = System::solrEnv (SYS_SYS)) !== false) {
      G::LoadClass ('AppSolr');
      print "Solr Configuration file: " . PATH_DATA_SITE . "env.ini\n";
      print "solr_enabled: " . $solrConf ['solr_enabled'] . "\n";
      print "solr_host: " . $solrConf ['solr_host'] . "\n";
      print "solr_instance: " . $solrConf ['solr_instance'] . "\n";
      
      $oAppSolr = new AppSolr ($solrConf ['solr_enabled'], $solrConf ['solr_host'], $solrConf ['solr_instance']);
      if ($ScriptAction == "reindexall") {
        $oAppSolr->reindexAllApplications ($SkipRecords, $TrunkSize);
      }
      if ($ScriptAction == "reindexmissing") {
        $oAppSolr->synchronizePendingApplications ();
      }
      if ($ScriptAction == "optimizeindex") {
        $oAppSolr->optimizeSearchIndex ();
      }
      if($ScriptAction == "reindexone"){
        if($appUid == ""){
          print "Missing -appuid parameter. please complete it with this option.\n";
        }
        $oAppSolr->updateApplicationSearchIndex ($appUid, false);
      }
    }
    else {
      print "Incomplete Solr configuration. See configuration file: " . PATH_DATA_SITE . "env.ini";
    }
  
  }
  catch (Exception $oError) {
    saveLog ("main", "error", "Error processing workspace : " . $oError->getMessage () . "\n");
  }
}

function saveLog($sSource, $sType, $sDescription)
{
  try {
    global $isDebug;
    if ($isDebug)
      print date ('H:i:s') . " ($sSource) $sType $sDescription <br>\n";
    
    G::verifyPath (PATH_DATA . 'log' . PATH_SEP, true);
    $message = date ('Y-m-d H:i:s') . '(' . $sSource . ') ' . $sDescription . "\n";
    if ($sType == 'action') {
      G::log($message, PATH_DATA);
    }
    else {
      G::log($message, PATH_DATA, 'cronError.log');
    }
  }
  catch (Exception $oError) {
    // CONTINUE
  }
}

function setExecutionMessage($m)
{
  $len = strlen ($m);
  $linesize = 60;
  $rOffset = $linesize - $len;
  
  eprint ("* $m");
  for ($i = 0; $i < $rOffset; $i++)
    eprint ('.');
}

function setExecutionResultMessage($m, $t = '')
{
  $c = 'green';
  if ($t == 'error')
    $c = 'red';
  if ($t == 'info')
    $c = 'yellow';
  eprintln ("[$m]", $c);
}
