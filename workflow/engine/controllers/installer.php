<?php

/**
 * Install Controller
 *
 * @author Erik A. O. <erik@colosa.com>
 */
global $translation;
include PATH_LANGUAGECONT."translation.".SYS_LANG;

class Installer extends Controller
{
    public $path_config;
    public $path_languages;
    public $path_plugins;
    public $path_xmlforms;
    public $path_shared;
    public $path_sep;
    public $systemName;

    public $link; #resource for database connection


    public function __construct ()
    {
        $this->path_config = PATH_CORE . 'config/';
        $this->path_languages = PATH_CORE . 'content/languages/';
        $this->path_plugins = PATH_CORE . 'plugins/';
        $this->path_xmlforms = PATH_CORE . 'xmlform/';
        $this->path_public = PATH_HOME . 'public_html/index.html';
        $this->path_shared = PATH_TRUNK . 'shared/';
        $this->path_sep = PATH_SEP;
        $this->systemName = '';
    }

    public function index ($httpData)
    {
        $licenseContent = file_get_contents( PATH_TRUNK . 'LICENSE.txt' );

        $this->includeExtJS( 'installer/CardLayout', false );
        $this->includeExtJS( 'installer/Wizard', false );
        $this->includeExtJS( 'installer/Header', false );
        $this->includeExtJS( 'installer/Card', false );

        $this->includeExtJS( 'installer/installer_cards' );
        $this->includeExtJS( 'installer/main', false );

        $this->setJSVar( 'licenseTxt', $licenseContent );

        $this->setJSVar( 'path_config', $this->path_config );
        $this->setJSVar( 'path_languages', $this->path_languages );
        $this->setJSVar( 'path_plugins', $this->path_plugins );
        $this->setJSVar( 'path_xmlforms', $this->path_xmlforms );
        $this->setJSVar( 'path_public', $this->path_public );
        $this->setJSVar( 'path_shared', $this->path_shared );
        $this->setJSVar( 'path_sep', $this->path_sep );

        $this->setView( 'installer/main' );

        G::RenderPage( 'publish', 'extJs' );
    }

    public function newSite ()
    {
        $textStep1 = G::LoadTranslation('ID_PROCESSMAKER_REQUIREMENTS_DESCRIPTION_STEP4_1');
        $textStep2 = G::LoadTranslation('ID_PROCESSMAKER_REQUIREMENTS_DESCRIPTION_STEP5');

        $this->includeExtJS( 'installer/CardLayout', false );
        $this->includeExtJS( 'installer/Wizard', false );
        $this->includeExtJS( 'installer/Header', false );
        $this->includeExtJS( 'installer/Card', false );
        $this->includeExtJS( 'installer/newSite', false );

        $this->setJSVar( 'textStep1', $textStep1 );
        $this->setJSVar( 'textStep2', $textStep2 );

        $this->setJSVar( 'DB_ADAPTER', DB_ADAPTER );
        $aux = explode( ':', DB_HOST );
        $this->setJSVar( 'DB_HOST', $aux[0] );
        $this->setJSVar( 'DB_PORT', isset( $aux[1] ) ? $aux[1] : (DB_ADAPTER == 'mssql' ? '1433' : '3306') );
        $this->setJSVar( 'DB_NAME', 'workflow' );
        $this->setJSVar( 'DB_USER', '' );
        $this->setJSVar( 'DB_PASS', '' );
        $this->setJSVar( 'pathConfig', PATH_CORE . 'config' . PATH_SEP );
        $this->setJSVar( 'pathLanguages', PATH_LANGUAGECONT );
        $this->setJSVar( 'pathPlugins', PATH_PLUGINS );
        $this->setJSVar( 'pathXmlforms', PATH_XMLFORM );
        $this->setJSVar( 'pathShared', PATH_DATA );

        $this->setView( 'installer/newSite' );

        G::RenderPage( 'publish', 'extJs' );
    }

    public function getSystemInfo ()
    {
        //$echo "<script> document.write(TRANSLATIONS) </script>";
        //print_r ($valu);die();
        $this->setResponseType( 'json' );

        // PHP info and verification
        $phpVer = phpversion();
        preg_match( '/[0-9\.]+/', $phpVer, $match );
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
        $info->php->result = version_compare(phpversion(), '5.2.10') >= 0 ? true : false;

        // MYSQL info and verification
        $info->mysql->result = false;
        if (function_exists( 'mysql_query' )) {
            $mysqlVer = mysql_get_client_info();
            preg_match( '/[0-9\.]+/', $mysqlVer, $match );
            $mysqlNum = (float) $match[0];
            $info->mysql->version = 'Client API version ' . $mysqlVer;
            $info->mysql->result = $mysqlNum >= 5.0 ? true : false;
        }

        // MSSQL info and verification
        $info->mssql->result = false;
        $info->mssql->version = G::LoadTranslation('ID_NOT_ENABLED');
        if (function_exists( 'mssql_query' )) {
            $info->mssql->result = true;
            $info->mssql->version = G::LoadTranslation('ID_ENABLED');
        }

        // OpenSSL info
        $info->openssl->result = false;
        $info->openssl->version = G::LoadTranslation('ID_NOT_ENABLED');
        if (function_exists( 'openssl_open' )) {
            $info->openssl->result = true;
            $info->openssl->version = G::LoadTranslation('ID_ENABLED');
        }

        // Curl info
        $info->curl->result = false;
        $info->curl->version = G::LoadTranslation('ID_NOT_ENABLED');
        if (function_exists( 'curl_version' )) {
            $info->curl->result = true;
            $version = curl_version();
            $info->curl->version = 'cURL ' . $version['version'];
            $info->openssl->version = $version['ssl_version'];
        }

        // DOMDocument info
        $info->dom->result = false;
        $info->dom->version = G::LoadTranslation('ID_NOT_ENABLED');
        if (class_exists( 'DOMDocument' )) {
            $info->dom->result = true;
            $info->dom->version = G::LoadTranslation('ID_ENABLED');
        }

        // GD info
        $info->gd->result = false;
        $info->gd->version = G::LoadTranslation('ID_NOT_ENABLED');
        if (function_exists( 'gd_info' )) {
            $info->gd->result = true;
            $gdinfo = gd_info();
            $info->gd->version = $gdinfo['GD Version'];
        }

        // Multibyte info
        $info->multibyte->result = false;
        $info->multibyte->version = G::LoadTranslation('ID_NOT_ENABLED');
        if (function_exists( 'mb_check_encoding' )) {
            $info->multibyte->result = true;
            $info->multibyte->version = G::LoadTranslation('ID_ENABLED');
        }

        // soap info
        $info->soap->result = false;
        $info->soap->version = G::LoadTranslation('ID_NOT_ENABLED');
        if (class_exists( 'SoapClient' )) {
            $info->soap->result = true;
            $info->soap->version = G::LoadTranslation('ID_ENABLED');
        }

        // ldap info
        $info->ldap->result = false;
        $info->ldap->version = G::LoadTranslation('ID_NOT_ENABLED');
        if (function_exists( 'ldap_connect' )) {
            $info->ldap->result = true;
            $info->ldap->version = G::LoadTranslation('ID_ENABLED');
        }

        // memory limit verification
        $memory = (int) ini_get( "memory_limit" );
        $info->memory->version = $memory . 'M';
        if ($memory > 80) {
            $info->memory->result = true;
        } else {
            $info->memory->result = false;
        }

        return $info;
    }

    public function is_dir_writable ($path)
    {
        return G::is_writable_r( $path );
    }

    public function getPermissionInfo ()
    {
        $this->setResponseType( 'json' );
        $info = new StdClass();
        $info->success = true;
        $noWritableFiles = array ();

        // pathConfig
        $info->pathConfig = new stdclass();
        $info->pathConfig->message = G::LoadTranslation('ID_INDEX_NOT_WRITEABLE');
        $info->pathConfig->result = G::is_writable_r( $_REQUEST['pathConfig'], $noWritableFiles );
        if ($info->pathConfig->result) {
            $info->pathConfig->message = G::LoadTranslation('ID_WRITEABLE');
        } else {
            $info->success = false;
        }

        $info->pathLanguages = new stdclass();
        $info->pathLanguages->message = G::LoadTranslation('ID_INDEX_NOT_WRITEABLE');
        $info->pathLanguages->result = G::is_writable_r( $_REQUEST['pathLanguages'], $noWritableFiles );
        if ($info->pathLanguages->result) {
            $info->pathLanguages->message = G::LoadTranslation('ID_WRITEABLE');
        } else {
            $info->success = false;
        }

        $info->pathPlugins = new stdclass();
        $info->pathPlugins->message = G::LoadTranslation('ID_INDEX_NOT_WRITEABLE');
        $info->pathPlugins->result = G::is_writable_r( $_REQUEST['pathPlugins'], $noWritableFiles );
        if ($info->pathPlugins->result) {
            $info->pathPlugins->message = G::LoadTranslation('ID_WRITEABLE');
        } else {
            $info->success = false;
        }

        $info->pathXmlforms = new stdclass();
        $info->pathXmlforms->message = G::LoadTranslation('ID_INDEX_NOT_WRITEABLE');
        $info->pathXmlforms->result = G::is_writable_r( $_REQUEST['pathXmlforms'], $noWritableFiles );
        if ($info->pathXmlforms->result) {
            $info->pathXmlforms->message = G::LoadTranslation('ID_WRITEABLE');
        } else {
            $info->success = false;
        }

        $info->pathPublic = new stdclass();

        $info->pathShared = new stdclass();
        $info->pathPublic->message = G::LoadTranslation('ID_INDEX_NOT_WRITEABLE');
        $info->pathPublic->result = G::is_writable_r( $_REQUEST['pathPublic'], $noWritableFiles );
        if ($info->pathPublic->result) {
            $info->pathShared->message = G::LoadTranslation('ID_WRITEABLE');
        } else {
            $info->success = false;
        }

        $info->pathShared->message = G::LoadTranslation('ID_INDEX_NOT_WRITEABLE');
        $info->pathShared->result = G::is_writable_r( $_REQUEST['pathShared'], $noWritableFiles );
        if ($info->pathShared->result) {
            $info->pathShared->message = G::LoadTranslation('ID_WRITEABLE');
        } else {
            G::verifyPath( $_REQUEST['pathShared'], true );
            $info->pathShared->result = G::is_writable_r( $_REQUEST['pathShared'], $noWritableFiles );
            if ($info->pathShared->result) {
                $info->pathShared->message = G::LoadTranslation('ID_WRITEABLE');
            } else {
                $info->success = false;
            }
        }

        if ($info->pathShared->result) {
            $aux = pathinfo( $_REQUEST['pathLogFile'] );
            G::verifyPath( $aux['dirname'], true );
            if (is_dir( $aux['dirname'] )) {
                if (! file_exists( $_REQUEST['pathLogFile'] )) {
                    @file_put_contents( $_REQUEST['pathLogFile'], '' );
                    chmod($_REQUEST['pathShared'], 0770);
                }
            }
        }

        $info->pathLogFile = new stdclass();
        $info->pathLogFile->message = G::LoadTranslation('ID_CREATE_LOG_INSTALLATION');
        $info->pathLogFile->result = file_exists( $_REQUEST['pathLogFile'] );

        if ($info->pathLogFile->result) {
            $info->pathLogFile->message = G::LoadTranslation('ID_INSTALLATION_LOG');
        }

        if ($info->success) {
            $info->notify = G::LoadTranslation('ID_SUCCESS_DIRECTORIES_WRITABLE');
        } else {
            $info->notify = G::LoadTranslation('ID_DIRECTORIES_NOT_WRITABLE');
        }

        $info->noWritableFiles = $noWritableFiles;

        return $info;
    }

    public function testConnection ()
    {
        $this->setResponseType( 'json' );
        if ($_REQUEST['db_engine'] == 'mysql') {
            return $this->testMySQLconnection();
        } else {
            return $this->testMSSQLconnection();
        }
    }

    /**
     * log the queries and other information to install.log,
     * the install.log files should be placed in shared/logs
     * for that reason we are using the $_REQUEST of pathShared
     */
    public function installLog ($text)
    {
        $serverAddr = $_SERVER['SERVER_ADDR'];
        //if this function is called outside the createWorkspace, just returns and do nothing
        if (! isset( $_REQUEST['pathShared'] )) {
            return;
        }
            //log file is in shared/logs
        $pathShared = trim( $_REQUEST['pathShared'] );
        if (substr( $pathShared, - 1 ) != '/') {
            $pathShared .= '/';
        }
        $pathSharedLog =  $pathShared . 'log/';
        G::verifyPath($pathSharedLog, true);
        $logFile = $pathSharedLog . 'install.log';

        if (! is_file( $logFile )) {
            G::mk_dir( dirname( $pathShared ) );
            $fpt = fopen( $logFile, 'w' );
            if ($fpt !== null) {
                fwrite( $fpt, sprintf( "%s %s\n", date( 'Y:m:d H:i:s' ), '----- '. G::LoadTranslation('ID_STARTING_LOG_FILE') .' ------' ) );
                fclose( $fpt );
            } else {
                throw (new Exception( G::LoadTranslation('ID_FILE_NOT_WRITEABLE', SYS_LANG, Array($logFile) ) ));
                return $false;
            }
        }

        $fpt = fopen( $logFile, 'a' );
        fwrite( $fpt, sprintf( "%s %s\n", date( 'Y:m:d H:i:s' ), trim( $text ) ) );
        fclose( $fpt );
        return true;
    }

    /**
     * function to create a workspace
     * in fact this function is calling appropiate functions for mysql and mssql
     */
    public function createWorkspace ()
    {
        $pathSharedPartner = trim( $_REQUEST['pathShared'] );
        if (file_exists(trim($pathSharedPartner,PATH_SEP). PATH_SEP .'partner.info')) {
            $this->systemName = $this->getSystemName($pathSharedPartner);
            $_REQUEST["PARTNER_FLAG"] = true;
        }
        $this->setResponseType( 'json' );
        if ($_REQUEST['db_engine'] == 'mysql') {
            $info = $this->createMySQLWorkspace();
        } else {
            $info = $this->createMSSQLWorkspace();
        }

        return $info;
    }

    public function forceTogenerateTranslationsFiles ($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, G::browserCacheFilesUrl((isset($_SERVER["HTTPS"])? (($_SERVER["HTTPS"] != "")? "https://" : "http://") : "http://") . $_SERVER["HTTP_HOST"] . "/js/ext/translation.en.js?r=" . rand(1, 10000)));
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
    public function mysqlQuery ($sql)
    {
        $this->installLog( $sql );
        $query = @mysql_query( $sql, $this->link );
        if (! $query) {
            $errorMessage = mysql_error( $this->link );
            $this->installLog( G::LoadTranslation('ID_MYSQL_ERROR', SYS_LANG, Array($errorMessage) ) );
            throw new Exception( $errorMessage );
            return false;
        }
        @mysql_free_result( $query );
        return true;
    }

    /**
     * send a query to MSSQL and log the query
     */
    public function mssqlQuery ($sql)
    {
        $this->installLog( $sql );
        $query = @mssql_query( $sql, $this->link );
        if (! $query) {
            $errorMessage = mssql_get_last_message();
            $this->installLog( G::LoadTranslation('ID_MYSQL_ERROR', SYS_LANG, Array($errorMessage) ));
            throw (new Exception( $errorMessage ));
            return false;
        }
        @mssql_free_result( $query );
        return true;
    }

    /**
     * query_sql_file send many statements to server
     *
     * @param string $file
     * @param string $connection
     * @return array $report
     */
    public function mysqlFileQuery ($file)
    {
        if (! is_file( $file )) {
            throw (new Exception( G::LoadTranslation('ID_SQL_FILE_INVALID', SYS_LANG, Array($file) ) ));
            return $false;
        }
        $this->installLog( G::LoadTranslation('ID_PROCESING', SYS_LANG, Array($file) ));
        $startTime = microtime( true );
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


        $lines = file( $file );
        $previous = null;
        $errors = '';
        @mysql_query( "SET NAMES 'utf8';" );
        foreach ($lines as $j => $line) {
            $line = trim( $line ); // Remove comments from the script


            if (strpos( $line, "--" ) === 0) {
                $line = substr( $line, 0, strpos( $line, "--" ) );
            }

            if (empty( $line )) {
                continue;
            }

            if (strpos( $line, "#" ) === 0) {
                $line = substr( $line, 0, strpos( $line, "#" ) );
            }

            if (empty( $line )) {
                continue;
            }

            // Concatenate the previous line, if any, with the current
            if ($previous) {
                $line = $previous . " " . $line;
            }
            $previous = null;

            // If the current line doesnt end with ; then put this line together
            // with the next one, thus supporting multi-line statements.
            if (strrpos( $line, ";" ) != strlen( $line ) - 1) {
                $previous = $line;
                continue;
            }

            $line = substr( $line, 0, strrpos( $line, ";" ) );
            @mysql_query( $line, $this->link );
        }

        $endTime = microtime( true );
        $this->installLog( G::LoadTranslation('ID_FILE_PROCESSED', SYS_LANG, Array(basename( $file ), $endTime - $startTime )) );
        return true;
    }

    /**
     * query_sql_file send many statements to server
     *
     * @param string $file
     * @param string $connection
     * @return array $report
     */
    public function mssqlFileQuery ($file)
    {
        if (! is_file( $file )) {
            throw (new Exception( G::LoadTranslation('ID_SQL_FILE_INVALID', SYS_LANG, Array($file) )));
            return $false;
        }
        $this->installLog( G::LoadTranslation('ID_PROCESING', SYS_LANG, Array($file) ));
        $startTime = microtime( true );
        $content = file_get_contents( $file );
        $queries = explode( ';', $content );

        foreach ($queries as $sql) {
            $query = @mssql_query( $sql, $this->link );
            if (! $query) {
                $errorMessage = mssql_get_last_message();
                $this->installLog( G::LoadTranslation('ID_MYSQL_ERROR',SYS_LANG, Array( $errorMessage . G::LoadTranslation('ID_QUERY') .": ". $sql) ));
                throw (new Exception( $errorMessage ));
                return false;
            }
        }
        $endTime = microtime( true );
        $this->installLog( G::LoadTranslation('ID_FILE_PROCESSED', SYS_LANG, Array(basename( $file ), $endTime - $startTime )) );
        return true;
    }

    /**
     * set Grant Privileges for MySQL
     *
     * @param string $psUser
     * @param string $psPassword
     * @param string $psDatabase
     * @return void
     */
    public function setGrantPrivilegesMySQL ($psUser, $psPassword, $psDatabase, $host)
    {
        $host = ($host == 'localhost' || $host == '127.0.0.1' ? 'localhost' : '%');
        $query = sprintf( "GRANT ALL PRIVILEGES ON `%s`.* TO %s@'%s' IDENTIFIED BY '%s' WITH GRANT OPTION", $psDatabase, $psUser, $host, $psPassword );
        $this->mysqlQuery( $query );
    }

    /**
     * set Grant Privileges for SQLServer
     *
     * @param string $psUser
     * @param string $psPassword
     * @param string $psDatabase
     * @return void
     */
    public function setGrantPrivilegesMSSQL ($psUser, $psPassword, $psDatabase)
    {

        $query = sprintf( "IF  EXISTS (SELECT * FROM sys.server_principals WHERE name = N'%s') DROP LOGIN [%s]", $psUser, $psUser );
        $this->mssqlQuery( $query );

        $query = sprintf( "CREATE LOGIN [%s] WITH PASSWORD=N'%s', DEFAULT_DATABASE=[%s], CHECK_EXPIRATION=OFF, CHECK_POLICY=OFF ", $psUser, $psPassword, $psDatabase );
        $this->mssqlQuery( $query );

        $query = sprintf( "USE %s;", $psDatabase );
        $this->mssqlQuery( $query );

        $query = sprintf( "IF  EXISTS (SELECT * FROM sys.database_principals WHERE name = N'%s') DROP USER [%s]", $psUser, $psUser );
        $this->mssqlQuery( $query );

        $query = sprintf( "CREATE USER %s FOR LOGIN %s;", $psUser, $psUser );
        $this->mssqlQuery( $query );

        $query = sprintf( "sp_addrolemember 'db_owner', '%s' ", $psUser );
        $this->mssqlQuery( $query );

        $query = sprintf( "sp_addrolemember 'db_ddladmin', '%s' ", $psUser );
        $this->mssqlQuery( $query );

        $query = sprintf( "sp_addrolemember 'db_accessadmin', '%s' ", $psUser );
        $this->mssqlQuery( $query );

        $query = sprintf( "use master " );
        $this->mssqlQuery( $query );

        return true;
    }

    public function createMySQLWorkspace ()
    {
        ini_set( 'max_execution_time', '0' );
        $info = new StdClass();
        $info->result = false;
        $info->message = '';
        $info->canRedirect = true;

        $db_hostname = trim( $_REQUEST['db_hostname'] );
        $db_port = trim( $_REQUEST['db_port'] );
        $db_username = trim( $_REQUEST['db_username'] );
        $db_password = trim( $_REQUEST['db_password'] );
        $wf = trim( $_REQUEST['wfDatabase'] );
        $rb = trim( $_REQUEST['rbDatabase'] );
        $rp = trim( $_REQUEST['rpDatabase'] );
        $workspace = trim( $_REQUEST['workspace'] );
        $pathConfig = trim( $_REQUEST['pathConfig'] );
        $pathLanguages = trim( $_REQUEST['pathLanguages'] );
        $pathPlugins = trim( $_REQUEST['pathPlugins'] );
        $pathShared = trim( $_REQUEST['pathShared'] );
        $pathXmlforms = trim( $_REQUEST['pathXmlforms'] );
        $adminPassword = trim( $_REQUEST['adminPassword'] );
        $adminUsername = trim( $_REQUEST['adminUsername'] );
        $deleteDB = ($_REQUEST['deleteDB'] == 'true');

        if (substr( $pathShared, - 1 ) != '/') {
            $pathShared .= '/';
        }

        $this->installLog( '-------------------------------------------' );
        $this->installLog( G::LoadTranslation('ID_CREATING_WORKSPACE', SYS_LANG, Array($workspace)));

        try {
            $db_host = ($db_port != '' && $db_port != 3306) ? $db_hostname . ':' . $db_port : $db_hostname;
            $this->link = @mysql_connect( $db_host, $db_username, $db_password );
            $this->installLog( G::LoadTranslation('ID_CONNECT_TO_SERVER', SYS_LANG, Array($db_hostname, $db_port, $db_username ) ));

            if ($deleteDB) {
                $q = sprintf( 'DROP DATABASE IF EXISTS %s;', $wf, $wf );
                $this->mysqlQuery( $q );

                $q = sprintf( 'DROP DATABASE IF EXISTS %s;', $rb, $rb );
                $this->mysqlQuery( $q );

                $q = sprintf( 'DROP DATABASE IF EXISTS %s;', $rp, $rp );
                $this->mysqlQuery( $q );
            }

            // CREATE databases wf_workflow, rb_workflow and rp_workflow
            $q = sprintf( 'CREATE DATABASE IF NOT EXISTS %s;', $wf, $wf );
            $this->mysqlQuery( $q );

            $q = sprintf( 'CREATE DATABASE IF NOT EXISTS %s;', $rb, $rb );
            $this->mysqlQuery( $q );

            $q = sprintf( 'CREATE DATABASE IF NOT EXISTS %s;', $rp, $rp );
            $this->mysqlQuery( $q );

            // CREATE users and GRANT Privileges
            $wfPass = G::generate_password( 12 );
            $rbPass = G::generate_password( 12 );
            $rpPass = G::generate_password( 12 );
            $this->setGrantPrivilegesMySQL( $wf, $wfPass, $wf, $db_hostname );
            $this->setGrantPrivilegesMySQL( $rb, $rbPass, $rb, $db_hostname );
            $this->setGrantPrivilegesMySQL( $rp, $rpPass, $rp, $db_hostname );

            // Generate the db.php file and folders
            $pathSharedSites = $pathShared;
            $path_site = $pathShared . "/sites/" . $workspace . "/";
            $db_file = $path_site . "db.php";
            @mkdir( $path_site, 0777, true );
            @mkdir( $path_site . "files/", 0777, true );
            @mkdir( $path_site . "mailTemplates/", 0777, true );
            @mkdir( $path_site . "public/", 0777, true );
            @mkdir( $path_site . "reports/", 0777, true );
            @mkdir( $path_site . "xmlForms", 0777, true );

            $dbText = "<?php\n";
            $dbText .= sprintf( "// Processmaker configuration\n" );
            $dbText .= sprintf( "  define ('DB_ADAPTER',     '%s' );\n", 'mysql' );
            $dbText .= sprintf( "  define ('DB_HOST',        '%s' );\n", $db_host );
            $dbText .= sprintf( "  define ('DB_NAME',        '%s' );\n", $wf );
            $dbText .= sprintf( "  define ('DB_USER',        '%s' );\n", $wf );
            $dbText .= sprintf( "  define ('DB_PASS',        '%s' );\n", $wfPass );
            $dbText .= sprintf( "  define ('DB_RBAC_HOST',   '%s' );\n", $db_host );
            $dbText .= sprintf( "  define ('DB_RBAC_NAME',   '%s' );\n", $rb );
            $dbText .= sprintf( "  define ('DB_RBAC_USER',   '%s' );\n", $rb );
            $dbText .= sprintf( "  define ('DB_RBAC_PASS',   '%s' );\n", $rbPass );
            $dbText .= sprintf( "  define ('DB_REPORT_HOST', '%s' );\n", $db_host );
            $dbText .= sprintf( "  define ('DB_REPORT_NAME', '%s' );\n", $rp );
            $dbText .= sprintf( "  define ('DB_REPORT_USER', '%s' );\n", $rp );
            $dbText .= sprintf( "  define ('DB_REPORT_PASS', '%s' );\n", $rpPass );
            if (defined('PARTNER_FLAG') || isset($_REQUEST['PARTNER_FLAG'])) {
                $dbText .= "\n";
                $dbText .= "  define ('PARTNER_FLAG', " . ((defined('PARTNER_FLAG')) ? PARTNER_FLAG : ((isset($_REQUEST['PARTNER_FLAG'])) ? $_REQUEST['PARTNER_FLAG']:'false')) . ");\n";
                if ($this->systemName != '') {
                    $dbText .= "  define ('SYSTEM_NAME', '" . $this->systemName . "');\n";
                }
            }

            $this->installLog( G::LoadTranslation('ID_CREATING', SYS_LANG, Array($db_file) ));
            file_put_contents( $db_file, $dbText );

            // Generate the databases.php file
            $databases_file = $path_site . 'databases.php';
            $dbData = sprintf( "\$dbAdapter    = '%s';\n", 'mysql' );
            $dbData .= sprintf( "\$dbHost       = '%s';\n", $db_host );
            $dbData .= sprintf( "\$dbName       = '%s';\n", $wf );
            $dbData .= sprintf( "\$dbUser       = '%s';\n", $wf );
            $dbData .= sprintf( "\$dbPass       = '%s';\n", $wfPass );
            $dbData .= sprintf( "\$dbRbacHost   = '%s';\n", $db_host );
            $dbData .= sprintf( "\$dbRbacName   = '%s';\n", $rb );
            $dbData .= sprintf( "\$dbRbacUser   = '%s';\n", $rb );
            $dbData .= sprintf( "\$dbRbacPass   = '%s';\n", $rbPass );
            $dbData .= sprintf( "\$dbReportHost = '%s';\n", $db_host );
            $dbData .= sprintf( "\$dbReportName = '%s';\n", $rp );
            $dbData .= sprintf( "\$dbReportUser = '%s';\n", $rp );
            $dbData .= sprintf( "\$dbReportPass = '%s';\n", $rpPass );
            $databasesText = str_replace( '{dbData}', $dbData, @file_get_contents( PATH_HOME . 'engine/templates/installer/databases.tpl' ) );

            $this->installLog( G::LoadTranslation('ID_CREATING', SYS_LANG, Array($databases_file) ));
            file_put_contents( $databases_file, $databasesText );

            // Execute scripts to create and populates databases
            $query = sprintf( "USE %s;", $rb );
            $this->mysqlQuery( $query );

            $this->mysqlFileQuery( PATH_RBAC_HOME . 'engine/data/mysql/schema.sql' );
            $this->mysqlFileQuery( PATH_RBAC_HOME . 'engine/data/mysql/insert.sql' );

            $query = sprintf( "USE %s;", $wf );
            $this->mysqlQuery( $query );
            $this->mysqlFileQuery( PATH_HOME . 'engine/data/mysql/schema.sql' );
            $this->mysqlFileQuery( PATH_HOME . 'engine/data/mysql/insert.sql' );

            if (defined('PARTNER_FLAG') || isset($_REQUEST['PARTNER_FLAG'])) {
                $this->setPartner();
                $this->setConfiguration();
            }

            // Create the triggers
            if (file_exists( PATH_HOME . 'engine/methods/setup/setupSchemas/triggerAppDelegationInsert.sql' ) && file_exists( PATH_HOME . 'engine/methods/setup/setupSchemas/triggerAppDelegationUpdate.sql' ) && file_exists( PATH_HOME . 'engine/methods/setup/setupSchemas/triggerApplicationUpdate.sql' ) && file_exists( PATH_HOME . 'engine/methods/setup/setupSchemas/triggerApplicationDelete.sql' ) && file_exists( PATH_HOME . 'engine/methods/setup/setupSchemas/triggerContentUpdate.sql' )) {
                $this->mysqlQuery( @file_get_contents( PATH_HOME . 'engine/methods/setup/setupSchemas/triggerAppDelegationInsert.sql' ) );
                $this->mysqlQuery( @file_get_contents( PATH_HOME . 'engine/methods/setup/setupSchemas/triggerAppDelegationUpdate.sql' ) );
                $this->mysqlQuery( @file_get_contents( PATH_HOME . 'engine/methods/setup/setupSchemas/triggerApplicationUpdate.sql' ) );
                $this->mysqlQuery( @file_get_contents( PATH_HOME . 'engine/methods/setup/setupSchemas/triggerApplicationDelete.sql' ) );
                $this->mysqlQuery( @file_get_contents( PATH_HOME . 'engine/methods/setup/setupSchemas/triggerContentUpdate.sql' ) );
                $this->mysqlQuery( "INSERT INTO `CONFIGURATION` (
                            `CFG_UID`,
                            `CFG_VALUE`
                           )
                           VALUES (
                             'APP_CACHE_VIEW_ENGINE',
                             '" . mysql_real_escape_string( serialize( array ('LANG' => 'en','STATUS' => 'active'
                ) ) ) . "'
                           )" );
            }

            // Change admin user
            $query = sprintf( "USE %s;", $wf );
            $this->mysqlQuery( $query );

            $query = sprintf( "UPDATE USERS SET USR_USERNAME = '%s', USR_PASSWORD = '%s' WHERE USR_UID = '00000000000000000000000000000001' ", $adminUsername, md5( $adminPassword ) );
            $this->mysqlQuery( $query );

            $query = sprintf( "USE %s;", $rb );
            $this->mysqlQuery( $query );

            $query = sprintf( "UPDATE USERS SET USR_USERNAME = '%s', USR_PASSWORD = '%s' WHERE USR_UID = '00000000000000000000000000000001' ", $adminUsername, md5( $adminPassword ) );
            $this->mysqlQuery( $query );

            // Write the paths_installed.php file (contains all the information configured so far)
            if (! file_exists( FILE_PATHS_INSTALLED )) {
                $sh = md5( filemtime( PATH_GULLIVER . '/class.g.php' ) );
                $h = G::encrypt( $db_hostname . $sh . $db_username . $sh . $db_password, $sh );
                $dbText = "<?php\n";
                $dbText .= sprintf( "  define('PATH_DATA',         '%s');\n", $pathShared );
                $dbText .= sprintf( "  define('PATH_C',            '%s');\n", $pathShared . 'compiled/' );
                $dbText .= sprintf( "  define('HASH_INSTALLATION', '%s');\n", $h );
                $dbText .= sprintf( "  define('SYSTEM_HASH',       '%s');\n", $sh );
                $this->installLog( G::LoadTranslation('ID_CREATING', SYS_LANG, Array(FILE_PATHS_INSTALLED) ));
                file_put_contents( FILE_PATHS_INSTALLED, $dbText );
            }

            /**
             * AppCacheView Build
             */
            define( 'HASH_INSTALLATION', $h );
            define( 'SYSTEM_HASH', $sh );
            define( 'PATH_DB', $pathShared . 'sites' . PATH_SEP );
            define( 'SYS_SYS', $workspace );

            require_once ("propel/Propel.php");

            Propel::init( PATH_CORE . "config/databases.php" );
            $con = Propel::getConnection( 'workflow' );

            require_once ('classes/model/AppCacheView.php');
            $lang = 'en';

            //setup the appcacheview object, and the path for the sql files
            $appCache = new AppCacheView();

            $appCache->setPathToAppCacheFiles( PATH_METHODS . 'setup' . PATH_SEP . 'setupSchemas' . PATH_SEP );

            //Update APP_DELEGATION.DEL_LAST_INDEX data
            $res = $appCache->updateAppDelegationDelLastIndex($lang, true);

            //APP_DELEGATION INSERT
            $res = $appCache->triggerAppDelegationInsert( $lang, true );

            //APP_DELEGATION Update
            $res = $appCache->triggerAppDelegationUpdate( $lang, true );

            //APPLICATION UPDATE
            $res = $appCache->triggerApplicationUpdate( $lang, true );

            //APPLICATION DELETE
            $res = $appCache->triggerApplicationDelete( $lang, true );

            //CONTENT UPDATE
            $res = $appCache->triggerContentUpdate( $lang, true );

            //build using the method in AppCacheView Class
            $res = $appCache->fillAppCacheView( $lang );

            //end AppCacheView Build


            //erik: for new env conf handling
            G::loadClass( 'system' );
            $envFile = PATH_CONFIG . 'env.ini';

            // getting configuration from env.ini
            $sysConf = System::getSystemConfiguration( $envFile );

            $langUri = 'en';
            if (isset($sysConf['default_lang'])) {
                $langUri = $sysConf['default_lang'];
            }

            $skinUri = 'neoclassic';
            if (isset($sysConf['default_skin'])) {
                $skinUri = $sysConf['default_skin'];
            }

            $updatedConf['default_lang'] = $langUri;
            $updatedConf['default_skin'] = $skinUri;
            $info->uri =  PATH_SEP . 'sys' . $_REQUEST['workspace'] . PATH_SEP . $langUri . PATH_SEP . $skinUri . PATH_SEP . 'login' . PATH_SEP . 'login';

            $indexFileUpdated = true;
            if (defined('PARTNER_FLAG') || isset($_REQUEST['PARTNER_FLAG'])) {
                $this->buildParternExtras($adminUsername, $adminPassword, $_REQUEST['workspace'], $langUri, $skinUri);
            } else {
                try {
                    G::update_php_ini( $envFile, $updatedConf );
                } catch (Exception $e) {
                    $info->result = false;
                    $info->message = G::LoadTranslation('ID_PROCESSMAKER_WRITE_CONFIG_INDEX', SYS_LANG, Array($envFile));
                    $info->message .= G::LoadTranslation('ID_PROCESSMAKER_UI_NOT_INSTALL');
                    $this->installLog( G::LoadTranslation('ID_INSTALL_BUT_ERROR', SYS_LANG, Array('env.ini')));
                    return $info;
                }

                try {
                    // update the main index file
                    $indexFileUpdated = System::updateIndexFile(array('lang' => 'en','skin' => $updatedConf['default_skin']));
                } catch (Exception $e) {
                    $info->result = false;
                    $info->message = G::LoadTranslation('ID_PROCESSMAKER_WRITE_CONFIG_INDEX', SYS_LANG, Array(PATH_HTML . "index.html."));
                    $info->message .= G::LoadTranslation('ID_PROCESSMAKER_UI_NOT_INSTALL');
                    $this->installLog( G::LoadTranslation('ID_INSTALL_BUT_ERROR', SYS_LANG, Array('index.html')));
                    return $info;
                }
            }

            $this->installLog( G::LoadTranslation('ID_INDEX_FILE_UPDATED', SYS_LANG, Array($indexFileUpdated, $sysConf['default_lang'],$sysConf['default_skin'])));
            $this->installLog( G::LoadTranslation('ID_INSTALL_SUCESS') );

            $info->result = true;
            $info->message = G::LoadTranslation('ID_INSTALL_SUCESS');
            $info->messageFinish = G::LoadTranslation('ID_PROCESSMAKER_SUCCESS_INSTALLED', SYS_LANG, Array($workspace));;
        } catch (Exception $e) {
            $info->canRedirect = false;
            $info->result = false;
            $info->message = $e->getMessage();
        }
        return $info;
    }

    public function createMSSQLWorkspace ()
    {
        ini_set( 'max_execution_time', '0' );
        $info->result = false;
        $info->message = '';

        $db_hostname = trim( $_REQUEST['db_hostname'] );
        $db_port = trim( $_REQUEST['db_port'] );
        $db_username = trim( $_REQUEST['db_username'] );
        $db_password = trim( $_REQUEST['db_password'] );
        $wf = trim( $_REQUEST['wfDatabase'] );
        $rb = trim( $_REQUEST['rbDatabase'] );
        $rp = trim( $_REQUEST['rpDatabase'] );
        $workspace = trim( $_REQUEST['workspace'] );
        $pathConfig = trim( $_REQUEST['pathConfig'] );
        $pathLanguages = trim( $_REQUEST['pathLanguages'] );
        $pathPlugins = trim( $_REQUEST['pathPlugins'] );
        $pathShared = trim( $_REQUEST['pathShared'] );
        $pathXmlforms = trim( $_REQUEST['pathXmlforms'] );
        $adminPassword = trim( $_REQUEST['adminPassword'] );
        $adminUsername = trim( $_REQUEST['adminUsername'] );
        $deleteDB = ($_REQUEST['deleteDB'] == 'true');

        if (substr( $pathShared, - 1 ) != '/') {
            $pathShared .= '/';
        }

        $this->installLog( '-------------------------------------------' );
        $this->installLog( G::LoadTranslation('ID_CREATING_WORKSPACE', SYS_LANG, Array($workspace) ) );

        try {
            $db_host = ($db_port != '' && $db_port != 1433) ? $db_hostname . ':' . $db_port : $db_hostname;
            $this->link = @mssql_connect( $db_host, $db_username, $db_password );
            $this->installLog( G::LoadTranslation('ID_CONNECT_TO_SERVER', SYS_LANG, Array( $db_hostname, $db_port, $db_username )) );

            $this->mssqlQuery( 'USE [master]' );

            // DROP databases wf_workflow, rb_workflow and rp_workflow
            if ($deleteDB) {
                $q = sprintf( "IF EXISTS (SELECT name FROM sys.databases WHERE name='%s' ) DROP DATABASE %s", $wf, $wf );
                $this->mssqlQuery( $q );

                $q = sprintf( "IF EXISTS (SELECT name FROM sys.databases WHERE name='%s' ) DROP DATABASE %s", $rb, $rb );
                $this->mssqlQuery( $q );

                $q = sprintf( "IF EXISTS (SELECT name FROM sys.databases WHERE name='%s' ) DROP DATABASE %s", $rp, $rp );
                $this->mssqlQuery( $q );
            }

            // CREATE databases wf_workflow, rb_workflow and rp_workflow
            $q = sprintf( "IF NOT EXISTS (SELECT * FROM sys.databases WHERE name='%s' ) CREATE DATABASE %s", $wf, $wf );
            $this->mssqlQuery( $q );

            $q = sprintf( "IF NOT EXISTS (SELECT * FROM sys.databases WHERE name='%s' ) CREATE DATABASE %s", $rb, $rb );
            $this->mssqlQuery( $q );

            $q = sprintf( "IF NOT EXISTS (SELECT * FROM sys.databases WHERE name='%s' ) CREATE DATABASE %s", $rp, $rp );
            $this->mssqlQuery( $q );

            //CREATE users and GRANT Privileges
            $wfPass = G::generate_password( 12 );
            $rbPass = G::generate_password( 12 );
            $rpPass = G::generate_password( 12 );
            $this->setGrantPrivilegesMSSQL( $wf, $wfPass, $wf );
            $this->setGrantPrivilegesMSSQL( $rb, $rbPass, $rb );
            $this->setGrantPrivilegesMSSQL( $rp, $rpPass, $rp );

            //Generate the db.php file and folders
            $path_site = $pathShared . "/sites/" . $workspace . "/";
            $db_file = $path_site . "db.php";
            mkdir( $path_site, 0777, true );
            @mkdir( $path_site . "files/", 0777, true );
            @mkdir( $path_site . "mailTemplates/", 0777, true );
            @mkdir( $path_site . "public/", 0777, true );
            @mkdir( $path_site . "reports/", 0777, true );
            @mkdir( $path_site . "xmlForms", 0777, true );

            $dbText = "<?php\n";
            $dbText .= sprintf( "// Processmaker configuration\n" );
            $dbText .= sprintf( "  define ('DB_ADAPTER',     '%s' );\n", 'mssql' );
            $dbText .= sprintf( "  define ('DB_HOST',        '%s' );\n", $db_host );
            $dbText .= sprintf( "  define ('DB_NAME',        '%s' );\n", $wf );
            $dbText .= sprintf( "  define ('DB_USER',        '%s' );\n", $wf );
            $dbText .= sprintf( "  define ('DB_PASS',        '%s' );\n", $wfPass );
            $dbText .= sprintf( "  define ('DB_RBAC_HOST',   '%s' );\n", $db_host );
            $dbText .= sprintf( "  define ('DB_RBAC_NAME',   '%s' );\n", $rb );
            $dbText .= sprintf( "  define ('DB_RBAC_USER',   '%s' );\n", $rb );
            $dbText .= sprintf( "  define ('DB_RBAC_PASS',   '%s' );\n", $rbPass );
            $dbText .= sprintf( "  define ('DB_REPORT_HOST', '%s' );\n", $db_host );
            $dbText .= sprintf( "  define ('DB_REPORT_NAME', '%s' );\n", $rp );
            $dbText .= sprintf( "  define ('DB_REPORT_USER', '%s' );\n", $rp );
            $dbText .= sprintf( "  define ('DB_REPORT_PASS', '%s' );\n", $rpPass );
            if (defined('PARTNER_FLAG') || isset($_REQUEST['PARTNER_FLAG'])) {
                $dbText .= "\n";
                $dbText .= "  define ('PARTNER_FLAG', " . ((defined('PARTNER_FLAG')) ? PARTNER_FLAG : ((isset($_REQUEST['PARTNER_FLAG'])) ? $_REQUEST['PARTNER_FLAG']:'false')) . ");\n";
                if ($this->systemName != '') {
                    $dbText .= "  define ('SYSTEM_NAME', '" . $this->systemName . "');\n";
                }
            }

            $this->installLog( G::LoadTranslation('ID_CREATING', SYS_LANG, Array($db_file) ));
            file_put_contents( $db_file, $dbText );

            // Generate the databases.php file
            $databases_file = $path_site . 'databases.php';
            $dbData = sprintf( "\$dbAdapter    = '%s';\n", 'mssql' );
            $dbData .= sprintf( "\$dbHost       = '%s';\n", $db_host );
            $dbData .= sprintf( "\$dbName       = '%s';\n", $wf );
            $dbData .= sprintf( "\$dbUser       = '%s';\n", $wf );
            $dbData .= sprintf( "\$dbPass       = '%s';\n", $wfPass );
            $dbData .= sprintf( "\$dbRbacHost   = '%s';\n", $db_host );
            $dbData .= sprintf( "\$dbRbacName   = '%s';\n", $rb );
            $dbData .= sprintf( "\$dbRbacUser   = '%s';\n", $rb );
            $dbData .= sprintf( "\$dbRbacPass   = '%s';\n", $rbPass );
            $dbData .= sprintf( "\$dbReportHost = '%s';\n", $db_host );
            $dbData .= sprintf( "\$dbReportName = '%s';\n", $rp );
            $dbData .= sprintf( "\$dbReportUser = '%s';\n", $rp );
            $dbData .= sprintf( "\$dbReportPass = '%s';\n", $rpPass );
            $databasesText = str_replace( '{dbData}', $dbData, @file_get_contents( PATH_HOME . 'engine/templates/installer/databases.tpl' ) );

            $this->installLog( G::LoadTranslation('ID_CREATING', SYS_LANG, Array($databases_file) ));
            file_put_contents( $databases_file, $databasesText );

            //execute scripts to create and populates databases
            $query = sprintf( "USE %s;", $rb );
            $this->mssqlQuery( $query );

            $this->mssqlFileQuery( PATH_RBAC_HOME . 'engine/data/mssql/schema.sql' );
            $this->mssqlFileQuery( PATH_RBAC_HOME . 'engine/data/mssql/insert.sql' );

            $query = sprintf( "USE %s;", $wf );
            $this->mssqlQuery( $query );
            $this->mssqlFileQuery( PATH_HOME . 'engine/data/mssql/schema.sql' );
            $this->mssqlFileQuery( PATH_HOME . 'engine/data/mssql/insert.sql' );

            // Create the triggers
            if (file_exists( PATH_HOME . 'engine/plugins/enterprise/data/triggerAppDelegationInsert.sql' ) && file_exists( PATH_HOME . 'engine/plugins/enterprise/data/triggerAppDelegationUpdate.sql' ) && file_exists( PATH_HOME . 'engine/plugins/enterprise/data/triggerApplicationUpdate.sql' ) && file_exists( PATH_HOME . 'engine/plugins/enterprise/data/triggerApplicationDelete.sql' ) && file_exists( PATH_HOME . 'engine/plugins/enterprise/data/triggerContentUpdate.sql' )) {
                $this->mssqlQuery( @file_get_contents( PATH_HOME . 'engine/plugins/enterprise/data/triggerAppDelegationInsert.sql' ) );
                $this->mssqlQuery( @file_get_contents( PATH_HOME . 'engine/plugins/enterprise/data/triggerAppDelegationUpdate.sql' ) );
                $this->mssqlQuery( @file_get_contents( PATH_HOME . 'engine/plugins/enterprise/data/triggerApplicationUpdate.sql' ) );
                $this->mssqlQuery( @file_get_contents( PATH_HOME . 'engine/plugins/enterprise/data/triggerApplicationDelete.sql' ) );
                $this->mssqlQuery( @file_get_contents( PATH_HOME . 'engine/plugins/enterprise/data/triggerContentUpdate.sql' ) );
                $this->mssqlQuery( "INSERT INTO CONFIGURATION (
                            CFG_UID,
                            CFG_VALUE
                           )
                           VALUES (
                             'APP_CACHE_VIEW_ENGINE',
                             '" . addslashes( serialize( array ('LANG' => 'en','STATUS' => 'active'
                ) ) ) . "'
                           )" );
            }

            //change admin user
            $query = sprintf( "USE %s;", $wf );
            $this->mssqlQuery( $query );

            $query = sprintf( "UPDATE USERS SET USR_USERNAME = '%s', USR_PASSWORD = '%s' WHERE USR_UID = '00000000000000000000000000000001' ", $adminUsername, md5( $adminPassword ) );
            $this->mssqlQuery( $query );

            $query = sprintf( "USE %s;", $rb );
            $this->mssqlQuery( $query );

            $query = sprintf( "UPDATE USERS SET USR_USERNAME = '%s', USR_PASSWORD = '%s' WHERE USR_UID = '00000000000000000000000000000001' ", $adminUsername, md5( $adminPassword ) );
            $this->mssqlQuery( $query );

            // Write the paths_installed.php file (contains all the information configured so far)
            if (! file_exists( FILE_PATHS_INSTALLED )) {
                $sh = md5( filemtime( PATH_GULLIVER . '/class.g.php' ) );
                $h = G::encrypt( $db_hostname . $sh . $db_username . $sh . $db_password . '1', $sh );
                $dbText = "<?php\n";
                $dbText .= sprintf( "  define ('PATH_DATA',        '%s' );\n", $pathShared );
                $dbText .= sprintf( "  define ('PATH_C',           '%s' );\n", $pathShared . 'compiled/' );
                $dbText .= sprintf( "  define ('HASH_INSTALLATION', '%s' );\n", $h );
                $dbText .= sprintf( "  define ('SYSTEM_HASH',       '%s' );\n", $sh );
                $this->installLog( G::LoadTranslation('ID_CREATING', SYS_LANG, Array(FILE_PATHS_INSTALLED) ));
                file_put_contents( FILE_PATHS_INSTALLED, $dbText );
            }
            $this->installLog( G::LoadTranslation('ID_INSTALL_SUCESS') );
            $info->result = true;
            $info->message = G::LoadTranslation('ID_INSTALL_SUCESS');
            $info->url = '/sys' . $_REQUEST['workspace'] . '/en/neoclassic/login/login';
            $info->messageFinish = G::LoadTranslation('ID_PROCESSMAKER_SUCCESS_INSTALLED', SYS_LANG, Array($workspace));;
        } catch (Exception $e) {
            $info->result = false;
            $info->message = $e->getMessage();
        }
        return $info;
    }

    public function getSystemName ($siteShared)
    {
        $systemName = '';
        if (substr( $siteShared, - 1 ) != '/') {
            $siteShared .= '/';
        }

        if (file_exists($siteShared . 'partner.info')) {
            $dataInfo = parse_ini_file($siteShared . 'partner.info');
            if (isset($dataInfo['system_name'])) {
                $systemName = trim($dataInfo['system_name']);
            }
        }
        return $systemName;
    }

    public function getEngines ()
    {
        $this->setResponseType( 'json' );
        $engines = array ();
        if (function_exists( 'mysql_query' )) {
            $engine = new stdclass();
            $engine->id = 'mysql';
            $engine->label = 'MySQL';
            $engines[] = $engine;
        }
        /**
         * DISABLED TEMPORARELY
         * if (function_exists('mssql_query')) {
         * $engine = new stdclass();
         * $engine->id = 'mssql';
         * $engine->label = 'Microsoft SQL Server';
         * $engines[] = $engine;
         * }
         */
        return $engines;
    }

    public function checkDatabases ()
    {
        $this->setResponseType( 'json' );
        $info = new stdclass();

        if ($_REQUEST['db_engine'] == 'mysql') {
            $link = @mysql_connect( $_REQUEST['db_hostname'], $_REQUEST['db_username'], $_REQUEST['db_password'] );
            $dataset = @mysql_query( "show databases like '" . $_REQUEST['wfDatabase'] . "'", $link );
            $info->wfDatabaseExists = (@mysql_num_rows( $dataset ) > 0);
            $dataset = @mysql_query( "show databases like '" . $_REQUEST['rbDatabase'] . "'", $link );
            $info->rbDatabaseExists = (@mysql_num_rows( $dataset ) > 0);
            $dataset = @mysql_query( "show databases like '" . $_REQUEST['rpDatabase'] . "'", $link );
            $info->rpDatabaseExists = (@mysql_num_rows( $dataset ) > 0);
        } else {
            $link = @mssql_connect( $_REQUEST['db_hostname'], $_REQUEST['db_username'], $_REQUEST['db_password'] );
            $dataset = @mssql_query( "select * from sys.databases where name = '" . $_REQUEST['wfDatabase'] . "'", $link );
            $info->wfDatabaseExists = (@mssql_num_rows( $dataset ) > 0);
            $dataset = @mssql_query( "select * from sys.databases where name = '" . $_REQUEST['rbDatabase'] . "'", $link );
            $info->rbDatabaseExists = (@mssql_num_rows( $dataset ) > 0);
            $dataset = @mssql_query( "select * from sys.databases where name = '" . $_REQUEST['rpDatabase'] . "'", $link );
            $info->rpDatabaseExists = (@mssql_num_rows( $dataset ) > 0);
        }

        $info->errMessage = G::LoadTranslation('ID_DATABASE_EXISTS_OVERWRITE');

        return $info;
    }

    /**
     * Privates functions section, non callable by http request
     */

    private function testMySQLconnection ()
    {
        $info = new StdClass();
        $info->result = false;
        $info->message = '';
        if (! function_exists( "mysql_connect" )) {
            $info->message = G::LoadTranslation('ID_PHP_MYSQL_NOT _INSTALL');
            return $info;
        }
        $db_hostname = $_REQUEST['db_hostname'];
        $db_port = $_REQUEST['db_port'];
        $db_username = $_REQUEST['db_username'];
        $db_password = $_REQUEST['db_password'];
        $fp = @fsockopen( $db_hostname, $db_port, $errno, $errstr, 30 );
        if (! $fp) {
            $info->message .= G::LoadTranslation('ID_CONNECTION_ERROR', SYS_LANG, Array($errstr ($errno)));
            return $info;
        }

        $db_host = ($db_port != '' && $db_port != 1433) ? $db_hostname . ':' . $db_port : $db_hostname;
        $link = @mysql_connect( $db_host, $db_username, $db_password );
        if (! $link) {
            $info->message .= G::LoadTranslation('ID_MYSQL_CREDENTIALS_WRONG');
            return $info;
        }
        $res = @mysql_query( "SELECT * FROM `information_schema`.`USER_PRIVILEGES` where (GRANTEE = \"'$db_username'@'$db_hostname'\" OR GRANTEE = \"'$db_username'@'%'\") and PRIVILEGE_TYPE = 'SUPER' ", $link );
        $row = @mysql_fetch_array( $res );
        $hasSuper = is_array( $row );
        @mysql_free_result( $res );
        @mysql_close( $link );
        if (! $hasSuper) {
            $info->message .= G::LoadTranslation('ID_CONNECTION_ERROR_PRIVILEGE', SYS_LANG, Array($db_username));
            return $info;
        }
        $info->message .= G::LoadTranslation('ID_MYSQL_SUCCESS_CONNECT');
        $info->result = true;
        return $info;
    }

    private function testMSSQLconnection ()
    {
        $info->result = false;
        $info->message = '';
        if (! function_exists( "mssql_connect" )) {
            $info->message = G::LoadTranslation('ID_PHP_MSSQL_NOT_INSTALLED');
            return $info;
        }

        $db_hostname = $_REQUEST['db_hostname'];
        $db_port = $_REQUEST['db_port'];
        $db_username = $_REQUEST['db_username'];
        $db_password = $_REQUEST['db_password'];

        $fp = @fsockopen( $db_hostname, $db_port, $errno, $errstr, 30 );
        if (! $fp) {
            $info->message .= G::LoadTranslation('ID_CONNECTION_ERROR', SYS_LANG, Array($errstr ($errno)));
            return $info;
        }

        $db_host = ($db_port != '' && $db_port != 1433) ? $db_hostname . ':' . $db_port : $db_hostname;
        $link = @mssql_connect( $db_host, $db_username, $db_password );
        if (! $link) {
            $info->message .= G::LoadTranslation('ID_MYSQL_CREDENTIALS_WRONG');
            return $info;
        }

        //checking if user has the dbcreator role
        $hasDbCreator = false;
        $hasSecurityAdmin = false;
        $hasSysAdmin = false;

        $res = @mssql_query( "EXEC sp_helpsrvrolemember 'dbcreator' ", $link );
        $row = mssql_fetch_array( $res );
        while (is_array( $row )) {
            if ($row['MemberName'] == $db_username) {
                $hasDbCreator = true;
            }
            $row = mssql_fetch_array( $res );
        }
        mssql_free_result( $res );

        $res = @mssql_query( "EXEC sp_helpsrvrolemember 'sysadmin' ", $link );
        $row = mssql_fetch_array( $res );
        while (is_array( $row )) {
            if ($row['MemberName'] == $db_username) {
                $hasSysAdmin = true;
            }
            $row = mssql_fetch_array( $res );
        }
        mssql_free_result( $res );

        $res = @mssql_query( "EXEC sp_helpsrvrolemember 'SecurityAdmin' ", $link );
        $row = mssql_fetch_array( $res );
        while (is_array( $row )) {
            if ($row['MemberName'] == $db_username) {
                $hasSecurityAdmin = true;
            }
            $row = mssql_fetch_array( $res );
        }
        mssql_free_result( $res );

        if (! ($hasSysAdmin || ($hasSecurityAdmin && $hasDbCreator))) {
            $info->message .= G::LoadTranslation('ID_CONNECTION_ERROR_SECURITYADMIN', SYS_LANG, Array($db_username) );
            return $info;
        }

        $info->message .= G::LoadTranslation('ID_MSSQL_SUCCESS_CONNECT');
        $info->result = true;
        return $info;
    }

    public function setPartner()
    {
        if (defined('PARTNER_FLAG') || isset($_REQUEST['PARTNER_FLAG'])) {
            // Execute sql for partner
            $pathMysqlPartner = PATH_CORE . 'data' . PATH_SEP . 'partner' . PATH_SEP . 'mysql' . PATH_SEP;
            if (G::verifyPath($pathMysqlPartner)) {
                $res = array();
                $filesSlq = glob($pathMysqlPartner . '*.sql');
                foreach ($filesSlq as $value) {
                    $this->mysqlFileQuery($value);
                }
            }

            // Execute to change of skin
            $pathSkinPartner = PATH_CORE . 'data' . PATH_SEP . 'partner' . PATH_SEP . 'skin' . PATH_SEP;
            if (G::verifyPath($pathSkinPartner)) {
                $res = array();
                $fileTar = glob($pathSkinPartner . '*.tar');
                foreach ($fileTar as $value) {
                    $dataFile = pathinfo($value);
                    $nameSkinTmp = $dataFile['filename'];
                    G::LoadThirdParty( 'pear/Archive', 'Tar' );
                    $tar = new Archive_Tar( $value );

                    $pathSkinTmp = $pathSkinPartner . 'tmp' . PATH_SEP;
                    G::rm_dir($pathSkinTmp);
                    G::verifyPath($pathSkinTmp, true);
                    chmod( $pathSkinTmp, 0777);
                    $tar->extract($pathSkinTmp);

                    $pathSkinName = $pathSkinTmp . $nameSkinTmp . PATH_SEP;
                    chmod( $pathSkinName, 0777);
                    G::verifyPath(PATH_CORE . 'skinEngine' . PATH_SEP . 'tmp', true);
                    $skinClassic = PATH_CORE . 'skinEngine' . PATH_SEP . 'tmp' . PATH_SEP;

                    if (is_dir($pathSkinName)) {
                        $this->copyFile($pathSkinName, $skinClassic);
                    }

                    G::rm_dir(PATH_CORE . 'skinEngine' . PATH_SEP . 'base');
                    rename(PATH_CORE . 'skinEngine' . PATH_SEP . 'tmp', PATH_CORE . 'skinEngine' . PATH_SEP . 'base');
                    G::rm_dir(PATH_CORE . 'skinEngine' . PATH_SEP . 'tmp');

                    break;
                }
            }
        }
    }

    function copyFile($fromDir, $toDir, $chmod=0777)
    {
        $errors = array();
        $messages = array();

        if (!is_writable($toDir))  {
            $errors[]='target '.$toDir.' is not writable';
        }
        if (!is_dir($toDir)) {
            $errors[]='target '.$toDir.' is not a directory';
        }
        if (!is_dir($fromDir)) {
            $errors[]='source '.$fromDir.' is not a directory';
        }
        if (!empty($errors)) {
            return false;
        }

        $exceptions = array ('.','..');
        $handle = opendir($fromDir);
        while (false !== ($item=readdir($handle))) {
            if (!in_array($item,$exceptions)) {
                $from = str_replace('//','/',$fromDir.'/'.$item);
                $to = str_replace('//','/',$toDir.'/'.$item);
                if (is_file($from)) {
                    if (@copy($from,$to)) {
                        chmod($to,$chmod);
                        touch($to,filemtime($from));
                    }
                }

                if (is_dir($from)) {
                    if (@mkdir($to)) {
                        chmod($to,$chmod);
                    }
                    $this->copyFile($from,$to,$chmod);
                }
            }
        }

        closedir($handle);
    }

    public function setConfiguration()
    {
        //a:4:{s:26:"login_enableForgotPassword";b:0;s:27:"login_enableVirtualKeyboard";b:0;s:21:"login_defaultLanguage";s:5:"pt-BR";s:10:"dateFormat";s:15:"d \\d\\e F \\d\\e Y";}
        $value = array(
            'login_defaultLanguage' => "pt-BR",
            "dateFormat" => 'd \d\e F \d\e Y'
        ); 

        $value = serialize($value);
        $query = "INSERT INTO CONFIGURATION (CFG_UID, CFG_VALUE) VALUES ('ENVIRONMENT_SETTINGS', '".mysql_real_escape_string($value)."')";

        $this->mysqlQuery($query);
    }

    public function buildParternExtras($username, $password, $workspace, $lang, $skinName)
    {
        ini_set('max_execution_time', '0');
        ini_set('memory_limit', '256M');

        $serv = 'http://';
        if (isset($_SERVER['HTTPS']) && trim($_SERVER['HTTPS']) != '') {
            $serv = 'https://';
        }
        $serv .= $_SERVER['SERVER_NAME'];
        if (isset($_SERVER['SERVER_PORT']) && trim($_SERVER['SERVER_PORT']) != '') {
            $serv .= ':' . $_SERVER['SERVER_PORT'];
        }

        // create session
        $cookiefile =  sys_get_temp_dir() . PATH_SEP . 'curl-session';

        $fp = fopen($cookiefile, "w");
        fclose($fp);
        chmod($cookiefile, 0777);

        $user = urlencode($username);
        $pass = urlencode($password);
        $lang = urlencode($lang);

        $ch = curl_init();

        // set URL and other appropriate options
        curl_setopt($ch, CURLOPT_URL, "$serv/sys{$workspace}/{$lang}/{$skinName}/login/authentication");
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookiefile);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookiefile);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "form[USR_USERNAME]=$user&form[USR_PASSWORD]=$pass&form[USER_LANG]=$lang");
        curl_setopt($ch, CURLOPT_TIMEOUT, 90);

        $output = curl_exec($ch);
        curl_close($ch);

        /**
         * Upload translation .po file
         */

        $ch = curl_init();
        $postData = array();
        // File to upload/post

        $postData['form[LANGUAGE_FILENAME]'] = "@".PATH_CORE."content/translations/processmaker.$lang.po";
        curl_setopt($ch, CURLOPT_URL, "$serv/sys{$workspace}/{$lang}/{$skinName}/setup/languages_Import");
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_VERBOSE, 0);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookiefile);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookiefile);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_TIMEOUT, 90);

        $output = curl_exec($ch);
        curl_close($ch);

        /**
         * Upload skin file
         */

        $ch = curl_init();
        $postData = array();

        $skins = glob(PATH_CORE."data/partner/*.tar");
        if (count($skins) > 0) {
            $skin = $skins[0];

            $postData['overwrite_files'] = "on";
            $postData['workspace'] = "global";
            $postData['option'] = "standardupload";
            $postData['action'] = "importSkin";
            // File to upload/post
            $postData['uploadedFile'] = "@".$skin;

            curl_setopt($ch, CURLOPT_URL, "$serv/sys{$workspace}/{$lang}/{$skinName}/setup/skin_Ajax");
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_VERBOSE, 0);
            curl_setopt($ch, CURLOPT_COOKIEFILE, $cookiefile);
            curl_setopt($ch, CURLOPT_COOKIEJAR, $cookiefile);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
            curl_setopt($ch, CURLOPT_TIMEOUT, 90);

            $output = curl_exec($ch);
            curl_close($ch);
        }

        /**
         * Upload plugin file
         */

        $ch = curl_init();
        $postData = array();
        // resolv the plugin name
        $plugins = glob(PATH_CORE."plugins/*.tar");
        if (count($plugins) > 0) {
            $pluginName = $plugins[0];

            // File to upload/post
            $postData['form[PLUGIN_FILENAME]'] = "@{$pluginName}";
            curl_setopt($ch, CURLOPT_URL, "$serv/sys{$workspace}/{$lang}/{$skinName}/setup/pluginsImportFile");
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_VERBOSE, 0);
            curl_setopt($ch, CURLOPT_COOKIEFILE, $cookiefile);
            curl_setopt($ch, CURLOPT_COOKIEJAR, $cookiefile);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
            curl_setopt($ch, CURLOPT_TIMEOUT, 90);

            $output = curl_exec($ch);
            curl_close($ch);
        }

        /**
         * Active plugins to enterprise
         */

        $plugins = glob(PATH_CORE."plugins/*.php");
        foreach ($plugins as $value) {
            $dataPlugin = pathinfo($value);
            $namePlugin = $dataPlugin['filename'];
            if ($value != 'enterprise') {
                $oCriteria = new Criteria();
                $oCriteria->addSelectColumn( AddonsManagerPeer::STORE_ID );
                $oCriteria->add( AddonsManagerPeer::ADDON_NAME, $namePlugin, Criteria::EQUAL );
                $oDataset  = AddonsManagerPeer::doSelectRs( $oCriteria );
                $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
                if ($oDataset->next()) {
                    $dataStore = $oDataset->getRow();
                    $ch = curl_init();
                    $postData = array();
                    $postData['action'] = "enable";
                    $postData['addon']  = $namePlugin;
                    $postData['store']  = $dataStore['STORE_ID'];

                    error_log($postData);
                    curl_setopt($ch, CURLOPT_URL, "$serv/sys{$workspace}/{$lang}/{$skinName}/enterprise/addonsStoreAction");
                    curl_setopt($ch, CURLOPT_HEADER, 0);
                    curl_setopt($ch, CURLOPT_VERBOSE, 0);
                    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookiefile);
                    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookiefile);
                    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
                    curl_setopt($ch, CURLOPT_TIMEOUT, 90);

                    $output = curl_exec($ch);
                    error_log('res=>' . $output);
                    curl_close($ch);
                }
            }
        }
    }
}

