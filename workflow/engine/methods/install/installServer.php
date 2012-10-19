<?php
/**
 * installServer.php
 *
 * ProcessMaker Open Source Edition
 * Copyright (C) 2004 - 2008 Colosa Inc.23
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
 * For more information, contact Colosa Inc, 2566 Le Jeune Rd.,
 * Coral Gables, FL, 33134, USA, or email info@colosa.com.
 */

$isWindows = PHP_OS == 'WINNT' ? true : false;

$oJSON = new Services_JSON();
$action = $_POST['action'];
$dataClient = $oJSON->decode( stripslashes( $_POST['data'] ) );
function find_SQL_Version ($my = 'mysql', $infExe = null)
{
    if (PHP_OS == "WINNT" && ! $infExe) {
        return false;
    }
    $output = shell_exec( $my . ' -V' );
    preg_match( '@[0-9]+\.[0-9]+\.[0-9]+@', $output, $version );
    return $version[0];
}

if ($action === "check") {
    /* TODO: Check if this space is required */
    print " ";
    G::LoadClass( 'Installer' );
    $inst = new Installer();
    $siteName = "workflow";
    $p1 = (isset( $dataClient->ao_admin_pass1 )) ? $dataClient->ao_admin_pass1 : 'admin';
    $p2 = (isset( $dataClient->ao_admin_pass2 )) ? $dataClient->ao_admin_pass2 : 'admin';
    $s = $inst->create_site( Array ('name' => 'workflow','path_data' => $dataClient->path_data,'path_compiled' => $dataClient->path_compiled,'admin' => Array ('username' => (isset( $dataClient->ao_admin )) ? $dataClient->ao_admin : 'admin','password' => $p1
    ),'advanced' => Array ('ao_db' => (isset( $dataClient->ao_db ) && $dataClient->ao_db === 2) ? false : true,'ao_db_drop' => (isset( $dataClient->ao_db_drop ) && $dataClient->ao_db_drop === true) ? true : false,'ao_db_wf' => (isset( $dataClient->ao_db_wf )) ? $dataClient->ao_db_wf : 'wf_' . $siteName,'ao_db_rb' => (isset( $dataClient->ao_db_rb )) ? $dataClient->ao_db_rb : 'rb_' . $siteName,'ao_db_rp' => (isset( $dataClient->ao_db_rp )) ? $dataClient->ao_db_rp : 'rp_' . $siteName
    ),'database' => Array ('hostname' => $dataClient->mysqlH,'username' => $dataClient->mysqlU,'password' => $dataClient->mysqlP
    )
    ) );
    $data = null;
    $data->phpVersion = (version_compare( PHP_VERSION, "5.1.0", ">" )) ? true : false;
    if (trim( $dataClient->mysqlH ) == '' || trim( $dataClient->mysqlU ) == '') {
        $con = array ('connection' => false,'grant' => false,'message' => 'Please complete the input fields (Hostname/Username)'
        );
    }
    $data->mysqlConnection = $s['result']['database']['connection'];
    $data->grantPriv = $s['result']['database']['grant'];
    $data->databaseMessage = $s['result']['database']['message'];
    $data->mysqlVersion = $s['result']['database']['version'];
    $data->path_data = $s['result']['path_data'];
    $data->path_compiled = true;
    $data->checkMemory = (((int) ini_get( "memory_limit" )) >= 40) ? true : false;
    #$data->checkmqgpc	=(get_magic_quotes_gpc())?false:true;
    $data->checkPI = $inst->is_dir_writable( PATH_CORE . "config/" );
    $data->checkDL = $inst->is_dir_writable( PATH_CORE . "content/languages/" );
    $data->checkDLJ = $inst->is_dir_writable( PATH_CORE . "js/labels/" );
    $data->checkPL = $inst->is_dir_writable( PATH_CORE . "plugins/" );
    $data->checkXF = $inst->is_dir_writable( PATH_CORE . "xmlform/" );
    $data->ao_db_wf = $s['result']['database']['ao']['ao_db_wf'];
    $data->ao_db_rb = $s['result']['database']['ao']['ao_db_rb'];
    $data->ao_db_rp = $s['result']['database']['ao']['ao_db_rp'];

    $data->ao_admin = $s['result']['admin']['username'];
    $data->ao_admin_pass = ($p1 !== $p2) ? false : true;

    //*Autoinstall Process and Plugins. By JHL
    // March 11th. 2009
    // To enable the way of aoutoinstall process and/or plugins
    // at same time of initial PM setup


    //Get Available autoinstall process
    $data->availableProcess = $inst->getDirectoryFiles( PATH_OUTTRUNK . "autoinstall", "pm" );

    //Get Available autoinstall plugins
    $data->availablePlugins = $inst->getDirectoryFiles( PATH_OUTTRUNK . "autoinstall", "tar" );

    //End autoinstall


    $data->microtime = microtime( true );
    echo $oJSON->encode( $data );
} elseif ($action === "install") {
    /*
     * Installation with SIMPLE POST
     *
     * Data necessary for the POST:
     *
     *
     * 	action=install
     * 	data=	{"mysqlE":"Path/to/mysql.exe",
     * 		"mysqlH":"Mysqlhostname",
     * 		"mysqlU":"mysqlUsername",
     * 		"mysqlP":"mysqlPassword",
     * 		"path_data":"/path/to/workflow_data/",
     * 		"path_compiled":"/path/to/compiled/",
     *              "heartbeatEnabled":"1"}
     *
     *--------------------------------------------------------------------------------------------------------------
     *
     * Steps to install.
     * 1) This data is required:
     * 	$HOSTNAME
     * 	$USERNAME
     * 	$PASSWORD
     * 	$PATH_TO_WORKFLOW_DATA
     * 	$PATH_TO_COMPILED DATA
     * 2) create $PATH_TO_WORKFLOW_DATA
     * 3) create $PATH_TO_COMPILED_DATA
     * 4) Create the site workflow
     *
     * 	4.1 Create user (mysql) wf_workflow , password: sample
     *		4.1.1 Create database wf_workflow with user wf_workflow
     *		4.1.2 Give all priviledges to database wf_workflow for user wf_workflow
     *		4.1.3 Dump file processmaker/workflow/engine/data/mysql/schema.sql
     *		4.1.4 Dump file processmaker/workflow/engine/data/mysql/insert.sql
     *
     * 	4.2 Create user (mysql) wf_rbac, password: sample
     *		4.2.1 Create database wf_rbac with user wf_rbac
     *		4.2.2 Give all priviledges to databse wf_rbac for user wf_rbac
     *		4.2.3 Dump file processmaker/rbac/engine/data/mysql/schema.sql
     *		4.2.4 Dump file processmaker/rbac/engine/data/mysql/insert.sql
     *
     *	4.3 Create configuratoin file and directories to site workflow
     *
     *		4.3.1 Create directories:
     *
     *			$PATH_TO_WORKFLOW_DATA./sites/workflow/
     *			$PATH_TO_WORKFLOW_DATA./sites/workflow/cutomFunctions/
     *			$PATH_TO_WORKFLOW_DATA./sites/workflow/rtfs/
     *			$PATH_TO_WORKFLOW_DATA./sites/workflow/xmlforms/
     *			$PATH_TO_WORKFLOW_DATA./sites/workflow/processesImages/
     *			$PATH_TO_WORKFLOW_DATA./sites/workflow/files/
     *		4.3.2 Create file.
     *
     *			$PATH_TO_WORKFLOW_DATA./sites/workflow/db.php
     *
     *			with these contents replacing $HOSTNAME.
     *
    			<?php
				// Processmaker configuration
				define ('DB_ADAPTER', 'mysql' );
				define ('DB_HOST', $HOSTNAME );
				define ('DB_NAME', 'wf_workflow' );
				define ('DB_USER', 'wf_workflow' );
				define ('DB_PASS', 'sample' );
				define ('DB_RBAC_HOST', $HOSTNAME );
				define ('DB_RBAC_NAME', 'rbac_workflow' );
				define ('DB_RBAC_USER', 'rbac_workflow' );
				define ('DB_RBAC_PASS', 'sample' );
    ?>

    *	4.4 Create file workflow/engine/config/paths_installed.php with these contents.
    *
    *		<?php
    define( 'PATH_DATA', '$PATH_TO_WORKFLOW_DATA' );
    define( 'PATH_C', '$PATH_TO_COMPILED_DATA' );
    ?>

    *   Restarting:
    * 	$PATH_TO_WORKFLOW_DATA
    * 	$PATH_TO_COMPILED DATA
    *
    *	4.2 Update translation from this url (background)
    *
    *		http://ProcessmakerHostname/sysworkflow/en/classic/tools/updateTranslation
    *
    *
    *
    *
    *5) Auto install processes and plugins
    *5.1 Install processes
    *5.2 Install plugins
    * */

    $report = null;

    try {

        require_once 'Log.php';

        $sp = "/";
        $dir_data = $dataClient->path_data;

        $dir_data = (substr( $dir_data, - 1 ) == $sp) ? $dir_data : $dir_data . "/";
        $dir_compiled = $dir_data . "compiled/";
        $dir_log = "{$dir_data}log/";
        global $isWindows;

        @mkdir( $dir_data . "sites", 0777, true );
        @mkdir( $dir_compiled, 0777, true );
        @mkdir( $dir_log, 0777, true );

        $logFilename = "{$dir_log}install.log";
        $displayLog = Log::singleton( 'display', '', 'INSTALLER', array ('lineFormat' => "%{message}"
        ) );
        $fileLog = Log::singleton( 'file', $logFilename, 'INSTALLER' );

        global $logger;
        $logger = Log::singleton( 'composite' );
        $logger->addChild( $displayLog );

        $create_db = "create-db.sql";
        $schema = "schema.sql";

        G::LoadClass( 'Installer' );

        /* Create default workspace called workflow */
        $inst = new Installer();
        $siteName = "workflow";
        $p1 = (isset( $dataClient->ao_admin_pass1 )) ? $dataClient->ao_admin_pass1 : 'admin';
        $p2 = (isset( $dataClient->ao_admin_pass2 )) ? $dataClient->ao_admin_pass2 : 'admin';

        $s = $inst->create_site( Array ('name' => 'workflow','path_data' => $dataClient->path_data,'path_compiled' => $dataClient->path_compiled,'admin' => Array ('username' => (isset( $dataClient->ao_admin )) ? $dataClient->ao_admin : 'admin','password' => $p1
        ),'advanced' => Array ('ao_db' => (isset( $dataClient->ao_db ) && $dataClient->ao_db === 2) ? false : true,'ao_db_drop' => (isset( $dataClient->ao_db_drop ) && $dataClient->ao_db_drop === true) ? true : false,'ao_db_wf' => (isset( $dataClient->ao_db_wf )) ? $dataClient->ao_db_wf : 'wf_' . $siteName,'ao_db_rb' => (isset( $dataClient->ao_db_rb )) ? $dataClient->ao_db_rb : 'rb_' . $siteName,'ao_db_rp' => (isset( $dataClient->ao_db_rp )) ? $dataClient->ao_db_rp : 'rp_' . $siteName
        ),'database' => Array ('hostname' => $dataClient->mysqlH,'username' => $dataClient->mysqlU,'password' => $dataClient->mysqlP
        )
        ), true );
        if ($s['created']) {
            $report = $inst->report;
        } else {
            /* On a failed install, $inst->report is blank because the
            * installation didnt occured at all. So we use the test report
            * instead.
            */
            $report = $s['result'];
        }
        $installError = (! $s['created']);
    } catch (Exception $e) {
        $installError = ($e->getMessage() ? $e->getMessage() : true);
    }

    if ($installError) {
        header( 'HTTP', true, 500 );
    }

    /* Status is used in the Windows installer, do not change this */
    print_r( "Status: " . (($installError) ? 'FAILED' : 'SUCCESS') . "\n\n" );

    /* Try to open the file log, if it fails, set it to NULL, so we don't try to
    * write to it again afterwards. If it succeeds, add to the logger.
    * Only open the log after writing status, otherwise a warning can be issued
    * which will affect the Windows installer.
    */
    if (! $fileLog->open()) {
        $fileLog = null;
        $displayLog->log( "Failed to create file log in $logFilename" );
    } else {
        $logger->addChild( $fileLog );
        $fileLog->log( " ** Starting installation ** " );
        $fileLog->log( "Status: " . (($installError) ? 'FAILED' : 'SUCCESS') );
        $displayLog->log( "This log is also available in $logFilename" );
    }

    $installArgs = (array) $dataClient;
    $hiddenFields = array ('mysqlP','ao_admin_pass1','ao_admin_pass2');
    foreach ($installArgs as $arg => $param) {
        if (in_array( $arg, $hiddenFields )) {
            $installArgs[$arg] = "********";
        }
    }

    $logger->log( "Installation arguments\n" . neat_r( array ($installArgs) ) );

    if (isset( $report )) {
        $logger->log( "Installation report\n" . neat_r( array ($report ) ) );
    } else {
        $logger->log( "** Installation crashed **" );
    }

    if (is_string( $installError )) {
        $logger->log( "Error message: $installError" );
    }

    if ($installError) {
        $logger->log( "Installation ending with errors" );
        die();
    }

    $sh = md5( filemtime( PATH_GULLIVER . "/class.g.php" ) );
    $h = G::encrypt( $dataClient->mysqlH . $sh . $dataClient->mysqlU . $sh . $dataClient->mysqlP . $sh . $inst->cc_status, $sh );
    $db_text = "<?php\n" . "define( 'PATH_DATA', '" . $dir_data . "' );\n" . "define( 'PATH_C',    '" . $dir_compiled . "' );\n" . "define( 'HASH_INSTALLATION','" . $h . "' );\n" . "define( 'SYSTEM_HASH','" . $sh . "' );\n" . "?>";
    $fp = fopen( FILE_PATHS_INSTALLED, "w" );
    fputs( $fp, $db_text, strlen( $db_text ) );
    fclose( $fp );

    /* Update languages */
    $update = file_get_contents( "http://" . $_SERVER['SERVER_NAME'] . ":" . $_SERVER['SERVER_PORT'] . "/sysworkflow/en/classic/tools/updateTranslation" );
    $logger->log( "Update language      => " . ((! $update) ? $update : "OK") );

    /* Heartbeat Enable/Disable */
    if (! isset( $dataClient->heartbeatEnabled )) {
        $dataClient->heartbeatEnabled = true;
    }
    $update = file_get_contents( "http://" . $_SERVER['SERVER_NAME'] . ":" . $_SERVER['SERVER_PORT'] . "/sysworkflow/en/classic/install/heartbeatStatus?status=" . $dataClient->heartbeatEnabled );
    $logger->log( "Heartbeat Status     => " . str_replace( "<br>", "\n", $update ) );

    /* Autoinstall Process */
    $update = file_get_contents( "http://" . $_SERVER['SERVER_NAME'] . ":" . $_SERVER['SERVER_PORT'] . "/sysworkflow/en/classic/install/autoinstallProcesses" );
    if (trim( str_replace( "<br>", "", $update ) ) == "") {
        $update = "Nothing to do.";
    }
    $logger->log( "Process AutoInstall  => " . str_replace( "<br>", "\n", $update ) );

    /* Autoinstall Plugins */
    $update = file_get_contents( "http://" . $_SERVER['SERVER_NAME'] . ":" . $_SERVER['SERVER_PORT'] . "/sysworkflow/en/classic/install/autoinstallPlugins" );
    if (trim( str_replace( "<br>", "", $update ) ) == "") {
        $update = "Nothing to do.";
    }
    $logger->log( "Plugin AutoInstall   => " . str_replace( "<br>", "\n", $update ) );
    $logger->log( "Installation finished successfuly" );
}

/*
    neat_r works like print_r but with much less visual clutter.
    By Jake Lodwick. Copy freely.
*/
function neat_r ($arr, $return = false)
{
    $out = array ();
    $oldtab = "    ";
    $newtab = "  ";

    $lines = explode( "\n", print_r( $arr, true ) );

    foreach ($lines as $line) {

        //remove numeric indexes like "[0] =>" unless the value is an array
        //if (substr($line, -5) != "Array") {
        $line = preg_replace( "/^(\s*)\[[0-9]+\] => /", "$1", $line, 1 );
        //}


        //garbage symbols
        foreach (array ("Array" => "","[" => "","]" => ""
        ) as
        //" =>"        => ":",
        $old => $new) {
            $out = str_replace( $old, $new, $out );
        }

        //garbage lines
        if (in_array( trim( $line ), array ("Array","(",")",""
        ) )) {
            continue;
        }

        //indents
        $indent = "";
        $indents = floor( (substr_count( $line, $oldtab ) - 1) / 2 );
        if ($indents > 0) {
            for ($i = 0; $i < $indents; $i ++) {
                $indent .= $newtab;
            }
        }

        $out[] = $indent . trim( $line );
    }

    $out = implode( "\n", $out );
    if ($return == true) {
        return $out;
    }

    return $out;
}

