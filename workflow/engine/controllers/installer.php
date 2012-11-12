<?php
/**
 * Install Controller
 *
 * @author Erik A. O. <erik@colosa.com>
 */

class Installer extends Controller
{
  public $path_config;
  public $path_languages;
  public $path_plugins;
  public $path_xmlforms;
  public $path_shared;
  public $path_sep;

  public $link;  #resource for database connection

  public function __construct()
  {
    $this->path_config    = PATH_CORE.'config/';
    $this->path_languages = PATH_CORE.'content/languages/';
    $this->path_plugins   = PATH_CORE.'plugins/';
    $this->path_xmlforms  = PATH_CORE.'xmlform/';
    $this->path_public    = PATH_HOME.'public_html/index.html';
    $this->path_shared    = PATH_TRUNK.'shared/';
    $this->path_sep       = PATH_SEP;
  }

  public function index($httpData)
  {
    $step1_txt = 'If any of these items is not supported (marked as No) then please take actions to correct them.<br><br>' .
                 'Failure to do so could lead to your ProcessMaker installation not functioning correctly!<br><br>' .
                 //'(*) MSSQL Support is optional.<br><br>' .
                 '(*) OpenSSL is optional.<br><br>' .
                 '(*) LDAP is optional.';

    $step2_txt = 'These settings are recommended for PHP in order to ensure full compatibility with ProcessMaker. <> ' .
                 'However, ProcessMaker still operate if your settings do not quite match the recommended';
    $step3_txt = 'In order for ProcessMaker to work correctly, it needs to be able read and write to certain directories and their files.<br>' .
                 'Make sure to give read and write access to the directories listed below and all their subdirectories and files.';
    $step4_txt = 'ProcessMaker stores all of its data in a database. Enter the address and port number used by the database. Also enter' .
                 'the username and password of the database user who will set up the databases used by ProcessMaker<br>';
    $step5_txt = 'ProcessMaker uses workspaces to store data in the database. Please enter a valid workspace name and a username and password to login'.
                 ' as the administrator.';
    $step6_txt = 'xxx';

    $licenseContent = file_get_contents(PATH_TRUNK . 'LICENSE.txt');

    $this->includeExtJS('installer/CardLayout', false);
    $this->includeExtJS('installer/Wizard', false);
    $this->includeExtJS('installer/Header', false);
    $this->includeExtJS('installer/Card', false);

    $this->includeExtJS('installer/installer_cards');
    $this->includeExtJS('installer/main', false);

    $this->setJSVar('licenseTxt', $licenseContent);
    $this->setJSVar('step1_txt', $step1_txt);
    $this->setJSVar('step2_txt', $step2_txt);
    $this->setJSVar('step3_txt', $step3_txt);
    $this->setJSVar('step4_txt', $step4_txt);
    $this->setJSVar('step5_txt', $step5_txt);
    $this->setJSVar('step6_txt', $step6_txt);

    $this->setJSVar('path_config',    $this->path_config );
    $this->setJSVar('path_languages', $this->path_languages );
    $this->setJSVar('path_plugins',   $this->path_plugins );
    $this->setJSVar('path_xmlforms',  $this->path_xmlforms );
    $this->setJSVar('path_public',    $this->path_public );
    $this->setJSVar('path_shared',    $this->path_shared );
    $this->setJSVar('path_sep',       $this->path_sep );

    $this->setView('installer/main');

    G::RenderPage('publish', 'extJs');
  }

  public function newSite()
  {
    $textStep1 = 'ProcessMaker stores all of its data in a database. This screen gives the installation program the information needed to create this database.<br><br>' .
                 'If you are installing ProcessMaker on a remote web server, you will need to get this information from your Database Server.';
    $textStep2 = 'ProcessMaker uses a workspaces to store data. Please select a valid workspace name and credentials to log in it.';

    $this->includeExtJS('installer/CardLayout', false);
    $this->includeExtJS('installer/Wizard', false);
    $this->includeExtJS('installer/Header', false);
    $this->includeExtJS('installer/Card', false);
    $this->includeExtJS('installer/newSite', false);

    $this->setJSVar('textStep1', $textStep1);
    $this->setJSVar('textStep2', $textStep2);

    $this->setJSVar('DB_ADAPTER', DB_ADAPTER);
    $aux = explode(':', DB_HOST);
    $this->setJSVar('DB_HOST', $aux[0]);
    $this->setJSVar('DB_PORT', isset($aux[1]) ? $aux[1] : (DB_ADAPTER == 'mssql' ? '1433' : '3306'));
    $this->setJSVar('DB_NAME', 'workflow');
    $this->setJSVar('DB_USER', '');
    $this->setJSVar('DB_PASS', '');
    $this->setJSVar('pathConfig', PATH_CORE . 'config' . PATH_SEP);
    $this->setJSVar('pathLanguages', PATH_LANGUAGECONT);
    $this->setJSVar('pathPlugins', PATH_PLUGINS);
    $this->setJSVar('pathXmlforms', PATH_XMLFORM);
    $this->setJSVar('pathShared', PATH_DATA);

    $this->setView('installer/newSite');

    G::RenderPage('publish', 'extJs');
  }

  public function getSystemInfo()
  {
    $this->setResponseType('json');

    // PHP info and verification
    $phpVer = phpversion();
    preg_match('/[0-9\.]+/', $phpVer, $match);
    $phpVerNum = (float) $match[0];

    $info = new stdclass();
    $info->php = new stdclass();
    $info->mysql = new stdclass();
    $info->mssql = new stdclass();
    $info->openssl = new stdclass();
    $info->curl = new stdclass();
    $info->dom = new stdclass();
    $info->gd = new stdclass();
    $info->multibyte = new stdclass();
    $info->soap = new stdclass();
    $info->ldap = new stdclass();
    $info->memory = new stdclass();

    $info->php->version = phpversion();
    $info->php->result  = version_compare(phpversion(), '5.2.10') >= 0 ? true : false;

    // MYSQL info and verification
    $info->mysql->result  = false;
    if ( function_exists ( 'mysql_query' ) ) {
      $mysqlVer = mysql_get_client_info();
      preg_match('/[0-9\.]+/', $mysqlVer, $match);
      $mysqlNum = (float) $match[0];
      $info->mysql->version = 'Client API version ' . $mysqlVer;
      $info->mysql->result  = $mysqlNum >= 5.0 ? true : false;
    }

    // MSSQL info and verification
    $info->mssql->result  = false;
    $info->mssql->version = 'not enabled';
    if ( function_exists ( 'mssql_query' ) ) {
      $info->mssql->result  = true;
      $info->mssql->version = 'enabled';
    }

    // OpenSSL info
    $info->openssl->result  = false;
    $info->openssl->version = 'not enabled';
    if ( function_exists ( 'openssl_open' ) ) {
      $info->openssl->result  = true;
      $info->openssl->version = 'enabled';
    }

    // Curl info
    $info->curl->result  = false;
    $info->curl->version = 'not enabled';
    if ( function_exists ( 'curl_version' ) ) {
      $info->curl->result  = true;
      $version = curl_version();
      $info->curl->version  = 'cURL ' . $version['version'];
      $info->openssl->version  = $version['ssl_version'];
    }

    // DOMDocument info
    $info->dom->result = false;
    $info->dom->version = 'not enabled';
    if ( class_exists ( 'DOMDocument' ) ) {
      $info->dom->result  = true;
      $info->dom->version = 'enabled';
    }

    // GD info
    $info->gd->result = false;
    $info->gd->version = 'not enabled';
    if ( function_exists ( 'gd_info' ) ) {
      $info->gd->result  = true;
      $gdinfo = gd_info();
      $info->gd->version = $gdinfo['GD Version'] ;
    }

    // Multibyte info
    $info->multibyte->result = false;
    $info->multibyte->version = 'not enabled';
    if ( function_exists ( 'mb_check_encoding' ) ) {
      $info->multibyte->result  = true;
      $info->multibyte->version = 'enabled';
    }

    // soap info
    $info->soap->result = false;
    $info->soap->version = 'not enabled';
    if ( class_exists ( 'SoapClient' ) ) {
      $info->soap->result  = true;
      $info->soap->version = 'enabled';
    }

    // ldap info
    $info->ldap->result = false;
    $info->ldap->version = 'not enabled';
    if ( function_exists ( 'ldap_connect' ) ) {
      $info->ldap->result  = true;
      $info->ldap->version = 'enabled';
    }

    // memory limit verification
    $memory = (int)ini_get("memory_limit");
    $info->memory->version = $memory . 'M';
    if ( $memory > 80 ) {
      $info->memory->result  = true;
    }
    else {
      $info->memory->result  = false;
    }

    return $info;
  }

  public function is_dir_writable($path)
  {
    return G::is_writable_r($path);
  }

  public function getPermissionInfo()
  {
    $this->setResponseType('json');

    $info = new StdClass();
    $info->success = true;
    $noWritableFiles = array();

    // pathConfig
    $info->pathConfig->message = 'unwriteable';
    $info->pathConfig->result  = G::is_writable_r($_REQUEST['pathConfig'], $noWritableFiles);
    if ( $info->pathConfig->result ) {
      $info->pathConfig->message = 'writeable';
    }
    else {
      $info->success = false;
    }

    $info->pathLanguages->message = 'unwriteable';
    $info->pathLanguages->result  = G::is_writable_r($_REQUEST['pathLanguages'], $noWritableFiles);
    if ( $info->pathLanguages->result ) {
      $info->pathLanguages->message = 'writeable';
    }
    else {
      $info->success = false;
    }

    $info->pathPlugins->message = 'unwriteable';
    $info->pathPlugins->result  = G::is_writable_r($_REQUEST['pathPlugins'], $noWritableFiles);
    if ( $info->pathPlugins->result ) {
      $info->pathPlugins->message = 'writeable';
    }
    else {
      $info->success = false;
    }

    $info->pathXmlforms->message = 'unwriteable';
    $info->pathXmlforms->result  = G::is_writable_r($_REQUEST['pathXmlforms'], $noWritableFiles);
    if ( $info->pathXmlforms->result ) {
      $info->pathXmlforms->message = 'writeable';
    }
    else {
      $info->success = false;
    }

    $info->pathPublic->message = 'unwriteable';
    $info->pathPublic->result  = G::is_writable_r($_REQUEST['pathPublic'], $noWritableFiles);
    if ( $info->pathPublic->result ) {
      $info->pathShared->message = 'writeable';
    }
    else {
      $info->success = false;
    }

    $info->pathShared->message = 'unwriteable';
    $info->pathShared->result  = G::is_writable_r($_REQUEST['pathShared'], $noWritableFiles);
    if ( $info->pathShared->result ) {
      $info->pathShared->message = 'writeable';
    }
    else {
      G::verifyPath($_REQUEST['pathShared'], true);
      $info->pathShared->result = G::is_writable_r($_REQUEST['pathShared'], $noWritableFiles);
      if ( $info->pathShared->result )
        $info->pathShared->message = 'writeable';
      else
        $info->success = false;
    }

    if ($info->pathShared->result) {
      $aux = pathinfo($_REQUEST['pathLogFile']);
      G::verifyPath($aux['dirname'], true);
      if (is_dir($aux['dirname'])) {
        if (!file_exists($_REQUEST['pathLogFile'])) {
          @file_put_contents($_REQUEST['pathLogFile'], '');
        }
      }
    }

    $info->pathLogFile->message = 'Could not create the installation log';
    $info->pathLogFile->result  = file_exists($_REQUEST['pathLogFile']);

    if ($info->pathLogFile->result) {
      $info->pathLogFile->message = 'Installation log created';
    }

    if ($info->success) {
      $info->notify = 'Success, all required directories are writable.';
    }
    else {
      $info->notify = 'Some directories and/or files inside it are not writable.';
    }

    $info->noWritableFiles = $noWritableFiles;

    return $info;
  }

  public function testConnection ()
  {
    $this->setResponseType('json');
    if ($_REQUEST['db_engine'] == 'mysql') {
      return $this->testMySQLconnection();
    }
    else {
      return $this->testMSSQLconnection();
    }
  }

  /**
   * log the queries and other information to install.log,
   * the install.log files should be placed in shared/logs
   * for that reason we are using the $_REQUEST of pathShared
   */
  public function installLog( $text )
  {
    $serverAddr = $_SERVER['SERVER_ADDR'];
    //if this function is called outside the createWorkspace, just returns and do nothing
    if ( !isset( $_REQUEST['pathShared']) )
      return;

    //log file is in shared/logs
    $pathShared = trim($_REQUEST['pathShared']);
    if ( substr($pathShared,-1) != '/' ) $pathShared .= '/';
    $logFile = $pathShared .  'log/install.log';

    if ( !is_file($logFile) ) {
      G::mk_dir(dirname($pathShared));
      $fpt = fopen ( $logFile, 'w' );
      if ( $fpt !== NULL ) {
        fwrite( $fpt, sprintf ( "%s %s\n", date('Y:m:d H:i:s'), '----- starting log file ------' ));
        fclose( $fpt);
      }
      else {
        throw ( new Exception ( sprintf ( "File '%s' is not writeable. Please check permission before continue", $logFile ) ) );
        return $false;
      }
    }

    $fpt = fopen ( $logFile, 'a' );
    fwrite( $fpt, sprintf ( "%s %s\n", date('Y:m:d H:i:s'), trim($text) ));
    fclose( $fpt);
    return true;
  }

  /**
   * function to create a workspace
   * in fact this function is calling appropiate functions for mysql and mssql
   */
  public function createWorkspace()
  {
    $this->setResponseType('json');
    if ($_REQUEST['db_engine'] == 'mysql') {
      $info = $this->createMySQLWorkspace();
    }
    else {
      $info = $this->createMSSQLWorkspace();
    }

    return $info;
  }

  public function forceTogenerateTranslationsFiles($url)
  {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, (isset($_SERVER['HTTPS']) ? ($_SERVER['HTTPS'] != '' ? 'https://' : 'http://') : 'http://') . $_SERVER['HTTP_HOST'] . '/js/ext/translation.en.js?r=' . rand(1, 10000));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);
    curl_exec($ch);
    curl_close($ch);
  }

  /**
   * send a query to MySQL and log the query
   */
  public function mysqlQuery($sql)
  {
    $this->installLog($sql);
    $query = @mysql_query($sql, $this->link);
    if (!$query) {
      $errorMessage = mysql_error($this->link);
      $this->installLog('MySQL error: ' . $errorMessage);
      throw new Exception($errorMessage);
      return false;
    }
    @mysql_free_result($query);
    return true;
  }

  /**
   * send a query to MSSQL and log the query
   */
  public function mssqlQuery($sql)
  {
    $this->installLog( $sql );
    $query = @mssql_query($sql, $this->link);
    if (!$query) {
      $errorMessage = mssql_get_last_message();
      $this->installLog ( 'MSSQL error: ' . $errorMessage );
      throw ( new Exception ( $errorMessage ) );
      return false;
    }
    @mssql_free_result($query);
    return true;
  }

  /**
   * query_sql_file send many statements to server
   *
   * @param   string  $file
   * @param   string  $connection
   * @return  array   $report
   */
  public function mysqlFileQuery($file)
  {
    if ( !is_file($file) ) {
      throw ( new Exception ( sprintf ( "File $file is not a valid sql file", $file ) ) );
      return $false;
    }
    $this->installLog ( 'Procesing: ' . $file );
    $startTime = microtime(true);
    // $content = file_get_contents($file);
    // $queries = explode(';', $content);

    // foreach( $queries as $sql) {
    //   if (trim($sql) != '') {
    //     $query = @mysql_query($sql, $this->link);
    //     if (!$query) {
    //       $errorMessage = mysql_error($this->link);

    //       $this->installLog ( sprintf ( 'MySQL error: %s  Query: %s ', $errorMessage, $sql ) );
    //       throw ( new Exception ( $errorMessage ) );
    //       return false;
    //     }
    //   }
    // }

    //erik: New Update, to support more complex queries

    $lines = file($file);
    $previous = NULL;
    $errors = '';
    @mysql_query("SET NAMES 'utf8';");
    foreach ($lines as $j => $line) {
      $line = trim($line); // Remove comments from the script

      if (strpos($line, "--") === 0) {
        $line = substr($line, 0, strpos($line, "--"));
      }

      if (empty($line)) {
        continue;
      }

      if (strpos($line, "#") === 0) {
        $line = substr($line, 0, strpos($line, "#"));
      }

      if (empty($line)) {
        continue;
      }

      // Concatenate the previous line, if any, with the current
      if ($previous) {
        $line = $previous . " " . $line;
      }
      $previous = NULL;

      // If the current line doesnt end with ; then put this line together
      // with the next one, thus supporting multi-line statements.
      if (strrpos($line, ";") != strlen($line) - 1) {
        $previous = $line;
        continue;
      }

      $line = substr($line, 0, strrpos($line, ";"));
      @mysql_query($line, $this->link);
    }

    $endTime = microtime(true);
    $this->installLog ( sprintf ('File: %s processed in %3.2f seconds', basename($file) , $endTime - $startTime ) );
    return true;
  }

  /**
   * query_sql_file send many statements to server
   *
   * @param   string  $file
   * @param   string  $connection
   * @return  array   $report
   */
  public function mssqlFileQuery($file)
  {
    if ( !is_file($file) ) {
      throw ( new Exception ( sprintf ( "File $file is not a valid sql file", $file ) ) );
      return $false;
    }
    $this->installLog ( 'Procesing: ' . $file );
    $startTime = microtime(true);
    $content = file_get_contents($file);
    $queries = explode(';', $content);

    foreach( $queries as $sql) {
      $query = @mssql_query($sql, $this->link);
      if (!$query) {
        $errorMessage = mssql_get_last_message();

        $this->installLog ( sprintf ( 'MSSQL error: %s  Query: %s ', $errorMessage, $sql ) );
        throw ( new Exception ( $errorMessage ) );
        return false;
      }
    }
    $endTime = microtime(true);
    $this->installLog ( sprintf ('File: %s processed in %3.2f seconds', basename($file) , $endTime - $startTime ) );
    return true;
  }

  /**
   * set Grant Privileges for MySQL
   *
   * @param  string    $psUser
   * @param  string    $psPassword
   * @param  string    $psDatabase
   * @return void
   */
  public function setGrantPrivilegesMySQL($psUser, $psPassword, $psDatabase, $host)
  {
    $host = ($host == 'localhost' || $host == '127.0.0.1' ? 'localhost' : '%');
    $query = sprintf("GRANT ALL PRIVILEGES ON `%s`.* TO %s@'%s' IDENTIFIED BY '%s' WITH GRANT OPTION", $psDatabase, $psUser, $host, $psPassword);
    $this->mysqlQuery($query);
  }

  /**
   * set Grant Privileges for SQLServer
   *
   * @param  string    $psUser
   * @param  string    $psPassword
   * @param  string    $psDatabase
   * @return void
   */
  public function setGrantPrivilegesMSSQL($psUser, $psPassword, $psDatabase)
  {

    $query = sprintf ( "IF  EXISTS (SELECT * FROM sys.server_principals WHERE name = N'%s') DROP LOGIN [%s]", $psUser, $psUser );
    $this->mssqlQuery( $query );

    $query = sprintf ( "CREATE LOGIN [%s] WITH PASSWORD=N'%s', DEFAULT_DATABASE=[%s], CHECK_EXPIRATION=OFF, CHECK_POLICY=OFF ", $psUser, $psPassword, $psDatabase );
    $this->mssqlQuery( $query );

    $query = sprintf ( "USE %s;", $psDatabase );
    $this->mssqlQuery( $query );

    $query = sprintf ( "IF  EXISTS (SELECT * FROM sys.database_principals WHERE name = N'%s') DROP USER [%s]", $psUser, $psUser );
    $this->mssqlQuery( $query );

    $query = sprintf ( "CREATE USER %s FOR LOGIN %s;", $psUser, $psUser );
    $this->mssqlQuery( $query );

    $query = sprintf ( "sp_addrolemember 'db_owner', '%s' ", $psUser );
    $this->mssqlQuery( $query );

    $query = sprintf ( "sp_addrolemember 'db_ddladmin', '%s' ", $psUser );
    $this->mssqlQuery( $query );

    $query = sprintf ( "sp_addrolemember 'db_accessadmin', '%s' ", $psUser );
    $this->mssqlQuery( $query );

    $query = sprintf ( "use master " );
    $this->mssqlQuery( $query );

    return true;
  }

  public function createMySQLWorkspace()
  {
    ini_set('max_execution_time', '0');
    $info->result   = false;
    $info->message = '';
    $info->canRedirect = true;

    $db_hostname   = trim($_REQUEST['db_hostname']);
    $db_port       = trim($_REQUEST['db_port']);
    $db_username   = trim($_REQUEST['db_username']);
    $db_password   = trim($_REQUEST['db_password']);
    $wf            = trim($_REQUEST['wfDatabase']);
    $rb            = trim($_REQUEST['rbDatabase']);
    $rp            = trim($_REQUEST['rpDatabase']);
    $workspace     = trim($_REQUEST['workspace']);
    $pathConfig    = trim($_REQUEST['pathConfig']);
    $pathLanguages = trim($_REQUEST['pathLanguages']);
    $pathPlugins   = trim($_REQUEST['pathPlugins']);
    $pathShared    = trim($_REQUEST['pathShared']);
    $pathXmlforms  = trim($_REQUEST['pathXmlforms']);
    $adminPassword = trim($_REQUEST['adminPassword']);
    $adminUsername = trim($_REQUEST['adminUsername']);
    $deleteDB      = ($_REQUEST['deleteDB'] == 'true');

    if ( substr($pathShared,-1) != '/' ) $pathShared .= '/';

    $this->installLog ( '-------------------------------------------' );
    $this->installLog ( sprintf ( "Creating workspace '%s' ", $workspace) );

    try {
      $db_host = ($db_port != '' && $db_port != 3306) ? $db_hostname . ':' . $db_port  : $db_hostname ;
      $this->link = @mysql_connect($db_host, $db_username, $db_password);
      $this->installLog (sprintf ( "Connected to server %s:%d using user: '%s' ", $db_hostname, $db_port, $db_username ) );

      if ($deleteDB) {
        $q  = sprintf('DROP DATABASE IF EXISTS %s;' , $wf, $wf);
        $this->mysqlQuery($q);

        $q  = sprintf('DROP DATABASE IF EXISTS %s;' , $rb, $rb);
        $this->mysqlQuery($q);

        $q  = sprintf('DROP DATABASE IF EXISTS %s;' , $rp, $rp);
        $this->mysqlQuery($q);
      }

      // CREATE databases wf_workflow, rb_workflow and rp_workflow
      $q  = sprintf('CREATE DATABASE IF NOT EXISTS %s;' , $wf, $wf);
      $this->mysqlQuery($q);

      $q  = sprintf('CREATE DATABASE IF NOT EXISTS %s;' , $rb, $rb);
      $this->mysqlQuery($q);

      $q  = sprintf('CREATE DATABASE IF NOT EXISTS %s;' , $rp, $rp);
      $this->mysqlQuery($q);

      // CREATE users and GRANT Privileges
      $wfPass = G::generate_password(12);
      $rbPass = G::generate_password(12);
      $rpPass = G::generate_password(12);
      $this->setGrantPrivilegesMySQL($wf, $wfPass, $wf, $db_hostname);
      $this->setGrantPrivilegesMySQL($rb, $rbPass, $rb, $db_hostname);
      $this->setGrantPrivilegesMySQL($rp, $rpPass, $rp, $db_hostname);

      // Generate the db.php file and folders
      $path_site  = $pathShared . "/sites/" . $workspace ."/";
      $db_file    = $path_site. "db.php";
      @mkdir($path_site, 0777, true);
      @mkdir($path_site . "files/",         0777, true);
      @mkdir($path_site . "mailTemplates/", 0777, true);
      @mkdir($path_site . "public/",        0777, true);
      @mkdir($path_site . "reports/",       0777, true);
      @mkdir($path_site . "xmlForms",       0777, true);

      $dbText = "<?php\n";
      $dbText .= sprintf ( "// Processmaker configuration\n" );
      $dbText .= sprintf ( "  define ('DB_ADAPTER',     '%s' );\n", 'mysql' );
      $dbText .= sprintf ( "  define ('DB_HOST',        '%s' );\n", $db_host );
      $dbText .= sprintf ( "  define ('DB_NAME',        '%s' );\n", $wf );
      $dbText .= sprintf ( "  define ('DB_USER',        '%s' );\n", $wf );
      $dbText .= sprintf ( "  define ('DB_PASS',        '%s' );\n", $wfPass );
      $dbText .= sprintf ( "  define ('DB_RBAC_HOST',   '%s' );\n", $db_host );
      $dbText .= sprintf ( "  define ('DB_RBAC_NAME',   '%s' );\n", $rb );
      $dbText .= sprintf ( "  define ('DB_RBAC_USER',   '%s' );\n", $rb );
      $dbText .= sprintf ( "  define ('DB_RBAC_PASS',   '%s' );\n", $rbPass );
      $dbText .= sprintf ( "  define ('DB_REPORT_HOST', '%s' );\n", $db_host );
      $dbText .= sprintf ( "  define ('DB_REPORT_NAME', '%s' );\n", $rp );
      $dbText .= sprintf ( "  define ('DB_REPORT_USER', '%s' );\n", $rp );
      $dbText .= sprintf ( "  define ('DB_REPORT_PASS', '%s' );\n", $rpPass );

      $this->installLog("Creating: " . $db_file );
      file_put_contents ( $db_file, $dbText);

      // Generate the databases.php file
      $databases_file = $path_site . 'databases.php';
      $dbData  = sprintf("\$dbAdapter    = '%s';\n", 'mysql');
      $dbData .= sprintf("\$dbHost       = '%s';\n", $db_host);
      $dbData .= sprintf("\$dbName       = '%s';\n", $wf);
      $dbData .= sprintf("\$dbUser       = '%s';\n", $wf);
      $dbData .= sprintf("\$dbPass       = '%s';\n", $wfPass);
      $dbData .= sprintf("\$dbRbacHost   = '%s';\n", $db_host);
      $dbData .= sprintf("\$dbRbacName   = '%s';\n", $rb);
      $dbData .= sprintf("\$dbRbacUser   = '%s';\n", $rb);
      $dbData .= sprintf("\$dbRbacPass   = '%s';\n", $rbPass);
      $dbData .= sprintf("\$dbReportHost = '%s';\n", $db_host);
      $dbData .= sprintf("\$dbReportName = '%s';\n", $rp);
      $dbData .= sprintf("\$dbReportUser = '%s';\n", $rp);
      $dbData .= sprintf("\$dbReportPass = '%s';\n", $rpPass);
      $databasesText = str_replace('{dbData}', $dbData, @file_get_contents(PATH_HOME . 'engine/templates/installer/databases.tpl'));

      $this->installLog('Creating: ' . $databases_file);
      file_put_contents($databases_file, $databasesText);


      // Execute scripts to create and populates databases
      $query = sprintf ( "USE %s;", $rb );
      $this->mysqlQuery( $query );

      $this->mysqlFileQuery ( PATH_RBAC_HOME . 'engine/data/mysql/schema.sql' );
      $this->mysqlFileQuery ( PATH_RBAC_HOME . 'engine/data/mysql/insert.sql' );

      $query = sprintf ( "USE %s;", $wf );
      $this->mysqlQuery( $query );
      $this->mysqlFileQuery ( PATH_HOME . 'engine/data/mysql/schema.sql' );
      $this->mysqlFileQuery ( PATH_HOME . 'engine/data/mysql/insert.sql' );

      // Create the triggers
      if (file_exists(PATH_HOME . 'engine/methods/setup/setupSchemas/triggerAppDelegationInsert.sql') &&
          file_exists(PATH_HOME . 'engine/methods/setup/setupSchemas/triggerAppDelegationUpdate.sql') &&
          file_exists(PATH_HOME . 'engine/methods/setup/setupSchemas/triggerApplicationUpdate.sql') &&
          file_exists(PATH_HOME . 'engine/methods/setup/setupSchemas/triggerApplicationDelete.sql') &&
          file_exists(PATH_HOME . 'engine/methods/setup/setupSchemas/triggerContentUpdate.sql')) {
        $this->mysqlQuery(@file_get_contents(PATH_HOME . 'engine/methods/setup/setupSchemas/triggerAppDelegationInsert.sql'));
        $this->mysqlQuery(@file_get_contents(PATH_HOME . 'engine/methods/setup/setupSchemas/triggerAppDelegationUpdate.sql'));
        $this->mysqlQuery(@file_get_contents(PATH_HOME . 'engine/methods/setup/setupSchemas/triggerApplicationUpdate.sql'));
        $this->mysqlQuery(@file_get_contents(PATH_HOME . 'engine/methods/setup/setupSchemas/triggerApplicationDelete.sql'));
        $this->mysqlQuery(@file_get_contents(PATH_HOME . 'engine/methods/setup/setupSchemas/triggerContentUpdate.sql'));
        $this->mysqlQuery("INSERT INTO `CONFIGURATION` (
                            `CFG_UID`,
                            `CFG_VALUE`
                           )
                           VALUES (
                             'APP_CACHE_VIEW_ENGINE',
                             '" . mysql_real_escape_string(serialize(array('LANG' => 'en', 'STATUS' => 'active'))) . "'
                           )");
      }

      // Change admin user
      $query = sprintf ( "USE %s;", $wf );
      $this->mysqlQuery( $query );

      $query = sprintf ( "UPDATE USERS SET USR_USERNAME = '%s', USR_PASSWORD = '%s' WHERE USR_UID = '00000000000000000000000000000001' ", $adminUsername, md5($adminPassword) );
      $this->mysqlQuery( $query );

      $query = sprintf ( "USE %s;", $rb );
      $this->mysqlQuery( $query );

      $query = sprintf ( "UPDATE USERS SET USR_USERNAME = '%s', USR_PASSWORD = '%s' WHERE USR_UID = '00000000000000000000000000000001' ", $adminUsername, md5($adminPassword) );
      $this->mysqlQuery( $query );

      // Write the paths_installed.php file (contains all the information configured so far)
      if ( !file_exists(FILE_PATHS_INSTALLED) ) {
        $sh = md5( filemtime( PATH_GULLIVER . '/class.g.php' ) );
        $h  = G::encrypt($db_hostname.$sh.$db_username.$sh.$db_password, $sh);
        $dbText = "<?php\n";
        $dbText .= sprintf ( "  define('PATH_DATA',         '%s');\n", $pathShared );
        $dbText .= sprintf ( "  define('PATH_C',            '%s');\n", $pathShared.'compiled/');
        $dbText .= sprintf ( "  define('HASH_INSTALLATION', '%s');\n", $h );
        $dbText .= sprintf ( "  define('SYSTEM_HASH',       '%s');\n", $sh );
        $this->installLog("Creating: " . FILE_PATHS_INSTALLED );
        file_put_contents ( FILE_PATHS_INSTALLED, $dbText);
      }

      /**
       * AppCacheView Build
       */
      define('HASH_INSTALLATION', $h );
      define('SYSTEM_HASH',       $sh );
      define('PATH_DB', $pathShared . 'sites' . PATH_SEP );
      define('SYS_SYS', 'workflow');

      require_once("propel/Propel.php");

      Propel::init( PATH_CORE . "config/databases.php" );
      $con = Propel::getConnection('workflow');

      require_once('classes/model/AppCacheView.php');
      $lang='en';

      //setup the appcacheview object, and the path for the sql files
      $appCache = new AppCacheView();

      $appCache->setPathToAppCacheFiles ( PATH_METHODS . 'setup' . PATH_SEP .'setupSchemas'. PATH_SEP );

      //APP_DELEGATION INSERT
      $res = $appCache->triggerAppDelegationInsert($lang, true);

      //APP_DELEGATION Update
      $res = $appCache->triggerAppDelegationUpdate($lang, true);

      //APPLICATION UPDATE
      $res = $appCache->triggerApplicationUpdate($lang, true);

      //APPLICATION DELETE
      $res = $appCache->triggerApplicationDelete($lang, true);

      //CONTENT UPDATE
      $res = $appCache->triggerContentUpdate($lang, true);

      //build using the method in AppCacheView Class
      $res = $appCache->fillAppCacheView($lang);

      //end AppCacheView Build

      //erik: for new env conf handling
      G::loadClass('system');
      $envFile = PATH_CONFIG . 'env.ini';

      //writting for new installtions to use the new skin 'uxmind' with new Front End ExtJs Based
      $updatedConf['default_skin'] = 'classic';
      $info->uri = '/sys' . $_REQUEST['workspace'] . '/en/classic/login/login';

      try {
        G::update_php_ini($envFile, $updatedConf);
      }
      catch (Exception $e) {
        $info->result  = false;
        $info->message = "ProcessMaker couldn't write on configuration file: $envFile.<br/>";
        $info->message .= "The new ProcessMaker UI couldn't be applied on installation, you can enable it after from Admin->System settings.";
        $this->installLog("Installed but with error, couldn't update env.ini" );
        return $info;
      }

      // getting configuration from env.ini
      $sysConf = System::getSystemConfiguration($envFile);

      try {
        // update the main index file
        $indexFileUpdated = System::updateIndexFile(array(
          'lang' => 'en',
          'skin' => $updatedConf['default_skin']
        ));
      }
      catch (Exception $e) {
        $info->result  = false;
        $info->message = "ProcessMaker couldn't write on configuration file: ".PATH_HTML."index.html.<br/>";
        $info->message .= "The new ProcessMaker UI couldn't be applied on installation, you can enable it after from Admin->System settings.";
        $this->installLog("Installed but with error, couldn't update index.html" );
        return $info;
      }

      $this->installLog("Index File updated $indexFileUpdated with lang: {$sysConf['default_lang']}, skin: {$sysConf['default_skin']} " );
      $this->installLog("Install completed Succesfully" );

      $info->result  = true;
      $info->message = 'Succesfully OK';
    }
    catch (Exception $e) {
      $info->canRedirect = false;
      $info->result  = false;
      $info->message = $e->getMessage();
    }
    return $info;
  }

  public function createMSSQLWorkspace()
  {
    ini_set('max_execution_time', '0');
    $info->result   = false;
    $info->message = '';

    $db_hostname   = trim($_REQUEST['db_hostname']);
    $db_port       = trim($_REQUEST['db_port']);
    $db_username   = trim($_REQUEST['db_username']);
    $db_password   = trim($_REQUEST['db_password']);
    $wf            = trim($_REQUEST['wfDatabase']);
    $rb            = trim($_REQUEST['rbDatabase']);
    $rp            = trim($_REQUEST['rpDatabase']);
    $workspace     = trim($_REQUEST['workspace']);
    $pathConfig    = trim($_REQUEST['pathConfig']);
    $pathLanguages = trim($_REQUEST['pathLanguages']);
    $pathPlugins   = trim($_REQUEST['pathPlugins']);
    $pathShared    = trim($_REQUEST['pathShared']);
    $pathXmlforms  = trim($_REQUEST['pathXmlforms']);
    $adminPassword = trim($_REQUEST['adminPassword']);
    $adminUsername = trim($_REQUEST['adminUsername']);
    $deleteDB      = ($_REQUEST['deleteDB'] == 'true');

    if ( substr($pathShared,-1) != '/' ) $pathShared .= '/';

    $this->installLog ( '-------------------------------------------' );
    $this->installLog ( sprintf ( "Creating workspace '%s' ", $workspace) );

    try {
      $db_host = ($db_port != '' && $db_port != 1433) ? $db_hostname . ':' . $db_port  : $db_hostname ;
      $this->link = @mssql_connect($db_host, $db_username, $db_password);
      $this->installLog (sprintf ( "Connected to server %s:%d using user: '%s' ", $db_hostname, $db_port, $db_username ) );

      $this->mssqlQuery( 'USE [master]' );

      // DROP databases wf_workflow, rb_workflow and rp_workflow
      if ($deleteDB) {
        $q  = sprintf ("IF EXISTS (SELECT name FROM sys.databases WHERE name='%s' ) DROP DATABASE %s" , $wf, $wf );
        $this->mssqlQuery( $q);

        $q  = sprintf ("IF EXISTS (SELECT name FROM sys.databases WHERE name='%s' ) DROP DATABASE %s" , $rb, $rb );
        $this->mssqlQuery( $q);

        $q  = sprintf ("IF EXISTS (SELECT name FROM sys.databases WHERE name='%s' ) DROP DATABASE %s" , $rp, $rp );
        $this->mssqlQuery( $q);
      }

      // CREATE databases wf_workflow, rb_workflow and rp_workflow
      $q  = sprintf ("IF NOT EXISTS (SELECT * FROM sys.databases WHERE name='%s' ) CREATE DATABASE %s" , $wf, $wf );
      $this->mssqlQuery( $q);

      $q  = sprintf ("IF NOT EXISTS (SELECT * FROM sys.databases WHERE name='%s' ) CREATE DATABASE %s" , $rb, $rb );
      $this->mssqlQuery( $q);

      $q  = sprintf ("IF NOT EXISTS (SELECT * FROM sys.databases WHERE name='%s' ) CREATE DATABASE %s" , $rp, $rp );
      $this->mssqlQuery( $q);

      //CREATE users and GRANT Privileges
      $wfPass = G::generate_password(12);
      $rbPass = G::generate_password(12);
      $rpPass = G::generate_password(12);
      $this->setGrantPrivilegesMSSQL($wf, $wfPass, $wf );
      $this->setGrantPrivilegesMSSQL($rb, $rbPass, $rb );
      $this->setGrantPrivilegesMSSQL($rp, $rpPass, $rp );

      //Generate the db.php file and folders
      $path_site  = $pathShared . "/sites/" . $workspace ."/";
      $db_file    = $path_site. "db.php";
      mkdir($path_site, 0777, true);
      @mkdir($path_site . "files/",         0777, true);
      @mkdir($path_site . "mailTemplates/", 0777, true);
      @mkdir($path_site . "public/",        0777, true);
      @mkdir($path_site . "reports/",       0777, true);
      @mkdir($path_site . "xmlForms",       0777, true);

      $dbText = "<?php\n";
      $dbText .= sprintf ( "// Processmaker configuration\n" );
      $dbText .= sprintf ( "  define ('DB_ADAPTER',     '%s' );\n", 'mssql' );
      $dbText .= sprintf ( "  define ('DB_HOST',        '%s' );\n", $db_host );
      $dbText .= sprintf ( "  define ('DB_NAME',        '%s' );\n", $wf );
      $dbText .= sprintf ( "  define ('DB_USER',        '%s' );\n", $wf );
      $dbText .= sprintf ( "  define ('DB_PASS',        '%s' );\n", $wfPass );
      $dbText .= sprintf ( "  define ('DB_RBAC_HOST',   '%s' );\n", $db_host );
      $dbText .= sprintf ( "  define ('DB_RBAC_NAME',   '%s' );\n", $rb );
      $dbText .= sprintf ( "  define ('DB_RBAC_USER',   '%s' );\n", $rb );
      $dbText .= sprintf ( "  define ('DB_RBAC_PASS',   '%s' );\n", $rbPass );
      $dbText .= sprintf ( "  define ('DB_REPORT_HOST', '%s' );\n", $db_host );
      $dbText .= sprintf ( "  define ('DB_REPORT_NAME', '%s' );\n", $rp );
      $dbText .= sprintf ( "  define ('DB_REPORT_USER', '%s' );\n", $rp );
      $dbText .= sprintf ( "  define ('DB_REPORT_PASS', '%s' );\n", $rpPass );

      $this->installLog("Creating: " . $db_file );
      file_put_contents ( $db_file, $dbText);

      // Generate the databases.php file
      $databases_file = $path_site . 'databases.php';
      $dbData  = sprintf("\$dbAdapter    = '%s';\n", 'mssql');
      $dbData .= sprintf("\$dbHost       = '%s';\n", $db_host);
      $dbData .= sprintf("\$dbName       = '%s';\n", $wf);
      $dbData .= sprintf("\$dbUser       = '%s';\n", $wf);
      $dbData .= sprintf("\$dbPass       = '%s';\n", $wfPass);
      $dbData .= sprintf("\$dbRbacHost   = '%s';\n", $db_host);
      $dbData .= sprintf("\$dbRbacName   = '%s';\n", $rb);
      $dbData .= sprintf("\$dbRbacUser   = '%s';\n", $rb);
      $dbData .= sprintf("\$dbRbacPass   = '%s';\n", $rbPass);
      $dbData .= sprintf("\$dbReportHost = '%s';\n", $db_host);
      $dbData .= sprintf("\$dbReportName = '%s';\n", $rp);
      $dbData .= sprintf("\$dbReportUser = '%s';\n", $rp);
      $dbData .= sprintf("\$dbReportPass = '%s';\n", $rpPass);
      $databasesText = str_replace('{dbData}', $dbData, @file_get_contents(PATH_HOME . 'engine/templates/installer/databases.tpl'));

      $this->installLog('Creating: ' . $databases_file);
      file_put_contents($databases_file, $databasesText);

      //execute scripts to create and populates databases
      $query = sprintf ( "USE %s;", $rb );
      $this->mssqlQuery( $query );

      $this->mssqlFileQuery ( PATH_RBAC_HOME . 'engine/data/mssql/schema.sql' );
      $this->mssqlFileQuery ( PATH_RBAC_HOME . 'engine/data/mssql/insert.sql' );

      $query = sprintf ( "USE %s;", $wf );
      $this->mssqlQuery( $query );
      $this->mssqlFileQuery ( PATH_HOME . 'engine/data/mssql/schema.sql' );
      $this->mssqlFileQuery ( PATH_HOME . 'engine/data/mssql/insert.sql' );

      // Create the triggers
      if (file_exists(PATH_HOME . 'engine/plugins/enterprise/data/triggerAppDelegationInsert.sql') &&
          file_exists(PATH_HOME . 'engine/plugins/enterprise/data/triggerAppDelegationUpdate.sql') &&
          file_exists(PATH_HOME . 'engine/plugins/enterprise/data/triggerApplicationUpdate.sql') &&
          file_exists(PATH_HOME . 'engine/plugins/enterprise/data/triggerApplicationDelete.sql') &&
          file_exists(PATH_HOME . 'engine/plugins/enterprise/data/triggerContentUpdate.sql')) {
        $this->mssqlQuery(@file_get_contents(PATH_HOME . 'engine/plugins/enterprise/data/triggerAppDelegationInsert.sql'));
        $this->mssqlQuery(@file_get_contents(PATH_HOME . 'engine/plugins/enterprise/data/triggerAppDelegationUpdate.sql'));
        $this->mssqlQuery(@file_get_contents(PATH_HOME . 'engine/plugins/enterprise/data/triggerApplicationUpdate.sql'));
        $this->mssqlQuery(@file_get_contents(PATH_HOME . 'engine/plugins/enterprise/data/triggerApplicationDelete.sql'));
        $this->mssqlQuery(@file_get_contents(PATH_HOME . 'engine/plugins/enterprise/data/triggerContentUpdate.sql'));
        $this->mssqlQuery("INSERT INTO CONFIGURATION (
                            CFG_UID,
                            CFG_VALUE
                           )
                           VALUES (
                             'APP_CACHE_VIEW_ENGINE',
                             '" . addslashes(serialize(array('LANG' => 'en', 'STATUS' => 'active'))) . "'
                           )");
      }

      //change admin user
      $query = sprintf ( "USE %s;", $wf );
      $this->mssqlQuery( $query );

      $query = sprintf ( "UPDATE USERS SET USR_USERNAME = '%s', USR_PASSWORD = '%s' WHERE USR_UID = '00000000000000000000000000000001' ", $adminUsername, md5($adminPassword) );
      $this->mssqlQuery( $query );

      $query = sprintf ( "USE %s;", $rb );
      $this->mssqlQuery( $query );

      $query = sprintf ( "UPDATE USERS SET USR_USERNAME = '%s', USR_PASSWORD = '%s' WHERE USR_UID = '00000000000000000000000000000001' ", $adminUsername, md5($adminPassword) );
      $this->mssqlQuery( $query );

      // Write the paths_installed.php file (contains all the information configured so far)
      if ( !file_exists(FILE_PATHS_INSTALLED) ) {
        $sh = md5( filemtime( PATH_GULLIVER . '/class.g.php' ) );
        $h  = G::encrypt($db_hostname.$sh.$db_username.$sh.$db_password.'1' , $sh);
        $dbText = "<?php\n";
        $dbText .= sprintf ( "  define ('PATH_DATA',        '%s' );\n", $pathShared );
        $dbText .= sprintf ( "  define ('PATH_C',           '%s' );\n", $pathShared.'compiled/');
        $dbText .= sprintf ( "  define ('HASH_INSTALLATION', '%s' );\n", $h );
        $dbText .= sprintf ( "  define ('SYSTEM_HASH',       '%s' );\n", $sh );
        $this->installLog("Creating: " . FILE_PATHS_INSTALLED );
        file_put_contents ( FILE_PATHS_INSTALLED, $dbText);
      }
      $this->installLog("Install completed Succesfully" );
      $info->result  = true;
      $info->message = 'Succesfully';
      $info->url = '/sys' . $_REQUEST['workspace'] . '/en/classic/main/login';
    }
    catch (Exception $e) {
      $info->result  = false;
      $info->message = $e->getMessage();
    }
    return $info;
  }

  public function getEngines()
  {
    $this->setResponseType('json');
    $engines = array();
    if (function_exists('mysql_query')) {
      $engine = new stdclass();
      $engine->id = 'mysql';
      $engine->label = 'MySQL';
      $engines[] = $engine;
    }
    /** DISABLED TEMPORARELY
    if (function_exists('mssql_query')) {
      $engine = new stdclass();
      $engine->id = 'mssql';
      $engine->label = 'Microsoft SQL Server';
      $engines[] = $engine;
    }*/
    return $engines;
  }

  public function checkDatabases()
  {
    $this->setResponseType('json');
    $info = new stdclass();

    if ($_REQUEST['db_engine'] == 'mysql') {
      $link = @mysql_connect($_REQUEST['db_hostname'], $_REQUEST['db_username'], $_REQUEST['db_password']);
      $dataset = @mysql_query("show databases like '" . $_REQUEST['wfDatabase'] . "'", $link);
      $info->wfDatabaseExists = (@mysql_num_rows($dataset) > 0);
      $dataset = @mysql_query("show databases like '" . $_REQUEST['rbDatabase'] . "'", $link);
      $info->rbDatabaseExists = (@mysql_num_rows($dataset) > 0);
      $dataset = @mysql_query("show databases like '" . $_REQUEST['rpDatabase'] . "'", $link);
      $info->rpDatabaseExists = (@mysql_num_rows($dataset) > 0);
    }
    else {
      $link = @mssql_connect($_REQUEST['db_hostname'], $_REQUEST['db_username'], $_REQUEST['db_password']);
      $dataset = @mssql_query("select * from sys.databases where name = '" . $_REQUEST['wfDatabase'] . "'", $link);
      $info->wfDatabaseExists = (@mssql_num_rows($dataset) > 0);
      $dataset = @mssql_query("select * from sys.databases where name = '" . $_REQUEST['rbDatabase'] . "'", $link);
      $info->rbDatabaseExists = (@mssql_num_rows($dataset) > 0);
      $dataset = @mssql_query("select * from sys.databases where name = '" . $_REQUEST['rpDatabase'] . "'", $link);
      $info->rpDatabaseExists = (@mssql_num_rows($dataset) > 0);
    }

    $info->errMessage = 'Database already exists, check "Delete Databases if exists" to overwrite the exiting databases.';

    return $info;
  }

  /**
   * Privates functions section, non callable by http request
   */

  private function testMySQLconnection()
  {
    $info->result   = false;
    $info->message = '';
    if ( !function_exists("mysql_connect") ) {
      $info->message = 'php-mysql is Not Installed';
      return $info;
    }
    $db_hostname = $_REQUEST['db_hostname'];
    $db_port     = $_REQUEST['db_port'];
    $db_username = $_REQUEST['db_username'];
    $db_password = $_REQUEST['db_password'];
    $fp = @fsockopen($db_hostname, $db_port, $errno, $errstr, 30);
    if ( !$fp ) {
      $info->message .= "Connection Error: $errstr ($errno)";
      return $info;
    }

    $db_host = ($db_port != '' && $db_port != 1433) ? $db_hostname . ':' . $db_port  : $db_hostname ;
    $link = @mysql_connect($db_host, $db_username, $db_password);
    if (!$link) {
       $info->message .= "Connection Error: unable to connect to MySQL using provided credentials.";
       return $info;
    }
    $res = @mysql_query("SELECT * FROM `information_schema`.`USER_PRIVILEGES` where (GRANTEE = \"'$db_username'@'$db_hostname'\" OR GRANTEE = \"'$db_username'@'%'\") and PRIVILEGE_TYPE = 'SUPER' ", $link);
    $row = @mysql_fetch_array($res);
    $hasSuper = is_array($row);
    @mysql_free_result($res);
    @mysql_close($link);
    if (!$hasSuper) {
      $info->message .= "Connection Error: User '$db_username' can't create databases and Users <br>Please provide an user with SUPER privilege.";
      return $info;
    }
    $info->message .= "Succesfully connected to MySQL Server";
    $info->result   = true;
    return $info;
  }

  private function testMSSQLconnection()
  {
    $info->result   = false;
    $info->message = '';
    if ( !function_exists("mssql_connect") ) {
      $info->message = 'php-mssql is Not Installed';
      return $info;
    }

    $db_hostname = $_REQUEST['db_hostname'];
    $db_port     = $_REQUEST['db_port'];
    $db_username = $_REQUEST['db_username'];
    $db_password = $_REQUEST['db_password'];

    $fp = @fsockopen($db_hostname, $db_port, $errno, $errstr, 30);
    if ( !$fp ) {
      $info->message .= "Connection Error: $errstr ($errno)";
      return $info;
    }

    $db_host = ($db_port != '' && $db_port != 1433) ? $db_hostname . ':' . $db_port  : $db_hostname ;
    $link = @mssql_connect($db_host, $db_username, $db_password);
    if (!$link) {
       $info->message .= "Connection Error: unable to connect to MSSQL using provided credentials.";
       return $info;
    }

    //checking if user has the dbcreator role
    $hasDbCreator     = false;
    $hasSecurityAdmin = false;
    $hasSysAdmin      = false;

    $res = @mssql_query( "EXEC sp_helpsrvrolemember 'dbcreator' ", $link );
    $row = mssql_fetch_array($res);
    while ( is_array( $row ) ) {
      if ( $row['MemberName'] == $db_username ) $hasDbCreator = true;
      $row = mssql_fetch_array($res);
    }
    mssql_free_result($res);

    $res = @mssql_query( "EXEC sp_helpsrvrolemember 'sysadmin' ", $link );
    $row = mssql_fetch_array($res);
    while ( is_array( $row ) ) {
      if ( $row['MemberName'] == $db_username ) $hasSysAdmin = true;
      $row = mssql_fetch_array($res);
    }
    mssql_free_result($res);

    $res = @mssql_query( "EXEC sp_helpsrvrolemember 'SecurityAdmin' ", $link );
    $row = mssql_fetch_array($res);
    while ( is_array( $row ) ) {
      if ( $row['MemberName'] == $db_username ) $hasSecurityAdmin = true;
      $row = mssql_fetch_array($res);
    }
    mssql_free_result($res);

    if ( ! ( $hasSysAdmin || ( $hasSecurityAdmin && $hasDbCreator) ) ) {
      $info->message .= "Connection Error: User '$db_username' can't create databases and Users <br>Please provide an user with sysadmin role or dbcreator and securityadmin roles.";
      return $info;
    }

    $info->message .= "Succesfully connected to MSSQL Server";
    $info->result   = true;
    return $info;
  }
}