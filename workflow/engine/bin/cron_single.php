<?php
/**
 * cron_single.php
 * @package workflow-engine-bin
 */
ini_set('display_errors', 1);
error_reporting(E_ALL);
ini_set('memory_limit', '128M');

if (!defined('SYS_LANG')) {
	define('SYS_LANG', 'en');
}

if (!defined('PATH_HOME')) {
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
  G::LoadSystem('table');
  G::LoadSystem('pagedTable');
  G::LoadClass ( 'system' );
  require_once ( "propel/Propel.php" );
  require_once ( "creole/Creole.php" );
}

require_once 'classes/model/AppDelegation.php';
require_once 'classes/model/Event.php';
require_once 'classes/model/AppEvent.php';
require_once 'classes/model/CaseScheduler.php';
//G::loadClass('pmScript');

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

if (!defined('SYS_SYS')) {
  $sObject = $argv[1];
  $sNow    = $argv[2];
  $sFilter = '';
  
  for($i=3; $i<count($argv); $i++){
      $sFilter .= ' '.$argv[$i];
  }

  $oDirectory = dir(PATH_DB);

  if (is_dir(PATH_DB . $sObject)) {
    saveLog ( 'main', 'action', "checking folder " . PATH_DB . $sObject );
    if (file_exists(PATH_DB . $sObject . PATH_SEP . 'db.php')) {

      define('SYS_SYS', $sObject);

      include_once(PATH_HOME.'engine'.PATH_SEP.'config'.PATH_SEP.'paths_installed.php');
      include_once(PATH_HOME.'engine'.PATH_SEP.'config'.PATH_SEP.'paths.php');

      //***************** PM Paths DATA **************************
      define( 'PATH_DATA_SITE',                 PATH_DATA      . 'sites/' . SYS_SYS . '/');
      define( 'PATH_DOCUMENT',                  PATH_DATA_SITE . 'files/' );
      define( 'PATH_DATA_MAILTEMPLATES',        PATH_DATA_SITE . 'mailTemplates/' );
      define( 'PATH_DATA_PUBLIC',               PATH_DATA_SITE . 'public/' );
      define( 'PATH_DATA_REPORTS',              PATH_DATA_SITE . 'reports/' );
      define( 'PATH_DYNAFORM',                  PATH_DATA_SITE . 'xmlForms/' );
      define( 'PATH_IMAGES_ENVIRONMENT_FILES',  PATH_DATA_SITE . 'usersFiles'.PATH_SEP);
      define( 'PATH_IMAGES_ENVIRONMENT_USERS',  PATH_DATA_SITE . 'usersPhotographies'.PATH_SEP);

      if(is_file(PATH_DATA_SITE.PATH_SEP.'.server_info')){
        $SERVER_INFO = file_get_contents(PATH_DATA_SITE.PATH_SEP.'.server_info');
        $SERVER_INFO = unserialize($SERVER_INFO);
        //print_r($SERVER_INFO);
        define( 'SERVER_NAME',  $SERVER_INFO ['SERVER_NAME']);
        define( 'SERVER_PORT',  $SERVER_INFO ['SERVER_PORT']);
      } else {
        eprintln("WARNING! No server info found!", 'red');
      }
      
      $sContent = file_get_contents(PATH_DB . $sObject . PATH_SEP . 'db.php');

      $sContent = str_replace('<?php', '', $sContent);
      $sContent = str_replace('<?', '', $sContent);
      $sContent = str_replace('?>', '', $sContent);
      $sContent = str_replace('define', '', $sContent);
      $sContent = str_replace("('", "$", $sContent);
      $sContent = str_replace("',", '=', $sContent);
      $sContent = str_replace(");", ';', $sContent);

      eval($sContent);
      $dsn = $DB_ADAPTER . '://' . $DB_USER . ':' . $DB_PASS . '@' . $DB_HOST . '/' . $DB_NAME;
      $dsnRbac = $DB_ADAPTER . '://' . $DB_RBAC_USER . ':' . $DB_RBAC_PASS . '@' . $DB_RBAC_HOST . '/' . $DB_RBAC_NAME;
      $dsnRp = $DB_ADAPTER . '://' . $DB_REPORT_USER . ':' . $DB_REPORT_PASS . '@' . $DB_REPORT_HOST . '/' . $DB_REPORT_NAME;
      switch ($DB_ADAPTER) {
        case 'mysql':
          $dsn     .= '?encoding=utf8';
          $dsnRbac .= '?encoding=utf8';
        break;
        case 'mssql':
          //$dsn     .= '?sendStringAsUnicode=false';
          //$dsnRbac .= '?sendStringAsUnicode=false';
        break;
        default:
        break;
      }
      $pro['datasources']['workflow']['connection'] = $dsn;
      $pro['datasources']['workflow']['adapter'] = $DB_ADAPTER;
      $pro['datasources']['rbac']['connection'] = $dsnRbac;
      $pro['datasources']['rbac']['adapter'] = $DB_ADAPTER;
      $pro['datasources']['rp']['connection'] = $dsnRp;
      $pro['datasources']['rp']['adapter'] = $DB_ADAPTER;
      //$pro['datasources']['dbarray']['connection'] = 'dbarray://user:pass@localhost/pm_os';
      //$pro['datasources']['dbarray']['adapter']    = 'dbarray';
      $oFile = fopen(PATH_CORE . 'config/_databases_.php', 'w');
      fwrite($oFile, '<?php global $pro;return $pro; ?>');
      fclose($oFile);
      Propel::init(PATH_CORE . 'config/_databases_.php');
      //Creole::registerDriver('dbarray', 'creole.contrib.DBArrayConnection');

      eprintln("Processing workspace: " . $sObject, 'green');
      try{
        processWorkspace();
      }catch(Exception $e){
        echo  $e->getMessage();
        eprintln("Probelm in workspace: " . $sObject.' it was omitted.', 'red');
      }
      eprintln();
    }
  }
  unlink(PATH_CORE . 'config/_databases_.php');
}
else {
	processWorkspace();
}

//finally update the file
@file_put_contents(PATH_DATA . 'cron', serialize(array('bCronIsRunning' => '0', 'sLastExecution' => date('Y-m-d H:i:s'))));


function processWorkspace() {
  global $sLastExecution;
  try {
    resendEmails();
    unpauseApplications();
    calculateDuration();
    executePlugins();
    executeEvents($sLastExecution);
    executeScheduledCases();
  }
  catch (Exception $oError) {
    saveLog ("main", "error", "Error processing workspace : " . $oError->getMessage() . "\n" );
  }
}

function resendEmails() {
  global $sFilter;
  if($sFilter!='' && strpos($sFilter, 'emails') === false) return false;

  setExecutionMessage("Resending emails");
  
  try {
    G::LoadClass('spool');
    $oSpool = new spoolRun();
    $oSpool->resendEmails();
    saveLog('resendEmails', 'action', 'Resending Emails', "c");
    
    $aSpoolWarnings = $oSpool->getWarnings();
    if( $aSpoolWarnings !== false ) {
       foreach($aSpoolWarnings as $sWarning){
       	 print('MAIL SPOOL WARNING: ' . $sWarning."\n");
       	 saveLog('resendEmails', 'warning', 'MAIL SPOOL WARNING: ' . $sWarning);
       }
    }
    setExecutionResultMessage('DONE');
  }
  catch (Exception $oError) {
    setExecutionResultMessage('WITH ERRORS', 'error');
    eprintln("  '-".$oError->getMessage(), 'red');
    saveLog('resendEmails', 'error', 'Error Resending Emails: ' . $oError->getMessage());
  }
}

function unpauseApplications() {
  global $sFilter;
  global $sNow;
  if($sFilter!='' &&  strpos($sFilter, 'unpause') === false) return false;
  setExecutionMessage("Unpausing applications");

  try {
    G::LoadClass('case');
    $oCases = new Cases();
    $oCases->ThrowUnpauseDaemon($sNow);
    setExecutionResultMessage('DONE');
    saveLog('unpauseApplications', 'action', 'Unpausing Applications');
  }
  catch (Exception $oError) {
    setExecutionResultMessage('WITH ERRORS', 'error');
    eprintln("  '-".$oError->getMessage(), 'red');
    saveLog('unpauseApplications', 'error', 'Error Unpausing Applications: ' . $oError->getMessage());
  }
}

function executePlugins(){
  global $sFilter;
  if($sFilter!='' &&  strpos($sFilter, 'plugins') === false) return false;

  $pathCronPlugins = PATH_CORE.'bin'.PATH_SEP.'plugins'.PATH_SEP;
  
  //erik: verify if the plugin dir exists
  if (!is_dir($pathCronPlugins)) {
    return false;
  }
  
  if ($handle = opendir( $pathCronPlugins )) {
    while ( false !== ($file = readdir($handle))) {
      if ( strpos($file, '.php',1) && is_file($pathCronPlugins . $file) ) {
        $filename  = str_replace('.php' , '', $file) ;
        $className = $filename . 'ClassCron';

        include_once ( $pathCronPlugins . $file );  //$filename. ".php"
        $oPlugin = new $className();
        if (method_exists($oPlugin, 'executeCron')) {
          $oPlugin->executeCron();
          setExecutionMessage("Executing Plugins");
          setExecutionResultMessage('DONE');
        }
      }
    }
  }
}
function calculateDuration() {
  global $sFilter;
  if($sFilter!='' &&  strpos($sFilter, 'calculate') === false) return false;
  setExecutionMessage("Calculating Duration");

  try {
    $oAppDelegation = new AppDelegation();
    $oAppDelegation->calculateDuration();
    setExecutionResultMessage('DONE');
    saveLog('calculateDuration', 'action', 'Calculating Duration');
  }
  catch (Exception $oError) {
    setExecutionResultMessage('WITH ERRORS', 'error');
    eprintln("  '-".$oError->getMessage(), 'red');
    saveLog('calculateDuration', 'error', 'Error Calculating Duration: ' . $oError->getMessage());
  }
}

function executeEvents($sLastExecution, $sNow=null) {
  
  global $sFilter;
  global $sNow;
  if($sFilter!='' && strpos($sFilter, 'events') === false) return false;

  setExecutionMessage("Executing events");
  setExecutionResultMessage('PROCESSING');
  try {      
    $oAppEvent = new AppEvent();
    saveLog('executeEvents', 'action', "Executing Events $sLastExecution, $sNow ");
    $n = $oAppEvent->executeEvents($sNow);
    setExecutionMessage("|- End Execution events");
    setExecutionResultMessage("Processed $n");
    //saveLog('executeEvents', 'action', $res );
  }
  catch (Exception $oError) {
    setExecutionResultMessage('WITH ERRORS', 'error');
    eprintln("  '-".$oError->getMessage(), 'red');
    saveLog('calculateAlertsDueDate', 'Error', 'Error Executing Events: ' . $oError->getMessage());
  }
}

function executeScheduledCases($sNow=null){
  try{
    global $sFilter;
    global $sNow;
    if($sFilter!='' && strpos($sFilter, 'scheduler') === false) return false;
  
    setExecutionMessage("Executing the scheduled starting cases");
    setExecutionResultMessage('PROCESSING');
  
    $sNow = isset($sNow)? $sNow: date('Y-m-d H:i:s');
    $oCaseScheduler = new CaseScheduler;
    $oCaseScheduler->caseSchedulerCron($sNow);
    setExecutionResultMessage('DONE');
  } catch(Exception $oError){
    setExecutionResultMessage('WITH ERRORS', 'error');
    eprintln("  '-".$oError->getMessage(), 'red');
  }
}

function saveLog($sSource, $sType, $sDescription) {
  try {
    global $isDebug;
    if ( $isDebug ) 
    print date('H:i:s') ." ($sSource) $sType $sDescription <br>\n";
    @fwrite($oFile, date('Y-m-d H:i:s') . '(' . $sSource . ') ' . $sDescription . "\n");
    
    G::verifyPath(PATH_DATA . 'log' . PATH_SEP, true);
    if ($sType == 'action') {
      $oFile = @fopen(PATH_DATA . 'log' . PATH_SEP . 'cron.log', 'a+');
    }
    else {
      $oFile = @fopen(PATH_DATA . 'log' . PATH_SEP . 'cronError.log', 'a+');
    }
    @fwrite($oFile, date('Y-m-d H:i:s') . '(' . $sSource . ') ' . $sDescription . "\n");
    @fclose($oFile);
  }
  catch (Exception $oError) {
    //CONTINUE
  }
}


function setExecutionMessage($m){
  $len = strlen($m);
  $linesize = 60;
  $rOffset = $linesize - $len;

  eprint("* $m");
  for($i=0; $i<$rOffset; $i++) eprint('.');
}

function setExecutionResultMessage($m, $t=''){
  $c='green';
  if($t=='error') $c = 'red';
  if($t=='info')  $c = 'yellow';
  eprintln("[$m]", $c);
}







