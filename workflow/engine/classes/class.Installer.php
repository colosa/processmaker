<?php

/**
 * class.Installer.php
 *
 * @package workflow.engine.ProcessMaker
 *
 * ProcessMaker Open Source Edition
 * Copyright (C) 2004 - 2011 Colosa Inc.
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
 *
 */
//
// It works with the table CONFIGURATION in a WF dataBase
//
// Copyright (C) 2007 COLOSA
//
// License: LGPL, see LICENSE
////////////////////////////////////////////////////


/**
 * Processmaker Installer
 *
 * @package workflow.engine.ProcessMaker
 * @author maborak
 * @copyright 2008 COLOSA
 */

class Installer
{
    public $options = Array ();
    public $result = Array ();
    public $error = Array ();
    public $report = Array ();
    private $connection_database;

    /**
     * construct of insert
     *
     * @param string $pPRO_UID
     * @return void
     */
    function __construct ()
    {
    }

    /**
     * create_site
     *
     * @param array $config
     * @param boolean $confirmed
     * @return void
     */
    public function create_site ($config = Array(), $confirmed = false)
    {
        $this->options = G::array_concat( Array ('isset' => false,'password' => G::generate_password( 12 ),'path_data' => @PATH_DATA,'path_compiled' => @PATH_C,'name' => $config['name'],'database' => Array (),'admin' => Array ('username' => 'admin','password' => 'admin'
        ),'advanced' => Array ('ao_db_wf' => 'wf_' . $config['name'],'ao_db_rb' => 'rb_' . $config['name'],'ao_db_rp' => 'rp_' . $config['name'],'ao_db_drop' => false
        )
        ), $config );
        $a = @explode( SYSTEM_HASH, G::decrypt( HASH_INSTALLATION, SYSTEM_HASH ) );
        $this->options['database'] = G::array_concat( Array ('username' => @$a[1],'password' => @$a[2],'hostname' => @$a[0]
        ), $this->options['database'] );
        return ($confirmed === true) ? $this->make_site() : $this->create_site_test();
    }

    /**
     * isset_site
     *
     * @param string $name Default value "workflow"
     * @return string file_exists(PATH_DATA."sites/".$name);
     */
    public function isset_site ($name = "workflow")
    {
        return file_exists( PATH_DATA . "sites/" . $name );
    }

    /**
     * create_site_test
     *
     * @return void
     */
    private function create_site_test ()
    {
        $name = (preg_match( '/^[\w]+$/i', trim( $this->options['name'] ) )) ? true : false;
        $result = Array ('path_data' => $this->is_dir_writable( $this->options['path_data'] ),'path_compiled' => $this->is_dir_writable( $this->options['path_compiled'] ),'database' => $this->check_connection(),'access_level' => $this->cc_status,'isset' => ($this->options['isset'] == true) ? $this->isset_site( $this->options['name'] ) : false,'microtime' => microtime(),'workspace' => $this->options['name'],'name' => array ('status' => $name,'message' => ($name) ? 'PASSED' : 'Workspace name invalid'
        ),'admin' => array ('username' => (preg_match( '/^[\w@\.-]+$/i', trim( $this->options['admin']['username'] ) )) ? true : false,'password' => ((trim( $this->options['admin']['password'] ) == '') ? false : true)
        )
        );
        $result['name']['message'] = ($result['isset']) ? 'Workspace already exist' : $result['name']['message'];
        $result['name']['status'] = ($result['isset']) ? false : $result['name']['status'];
        //print_r($result);
        return Array ('created' => G::var_compare( true, $result['path_data'], $result['database']['connection'], $result['name']['status'], $result['database']['version'], $result['database']['ao']['ao_db_wf']['status'], $result['database']['ao']['ao_db_rb']['status'], $result['database']['ao']['ao_db_rp']['status'], $result['admin']['username'], (($result['isset']) ? false : true), $result['admin']['password'] ),'result' => $result
        );
    }

    /**
     * make_site
     *
     * @return array $test
     */
    private function make_site ()
    {
        $test = $this->create_site_test();

        if ($test["created"] == true || $this->options["advanced"]["ao_db_drop"] == true) {
            /* Check if the hostname is local (localhost or 127.0.0.1) */
            $islocal = (strcmp( substr( $this->options['database']['hostname'], 0, strlen( 'localhost' ) ), 'localhost' ) === 0) || (strcmp( substr( $this->options['database']['hostname'], 0, strlen( '127.0.0.1' ) ), '127.0.0.1' ) === 0);

            $this->wf_site_name = $wf = $this->options['advanced']['ao_db_wf'];

            $this->rbac_site_name = $rb = $this->options['advanced']['ao_db_rb'];
            $this->report_site_name = $rp = $this->options['advanced']['ao_db_rp'];

            $schema = "schema.sql";
            $values = "insert.sql";

            if ($this->options['advanced']['ao_db_drop'] === true) {
                //Delete workspace directory if exists


                //Drop databases
                $this->run_query( "DROP DATABASE IF EXISTS " . $wf, "Drop database $wf" );
                $this->run_query( "DROP DATABASE IF EXISTS " . $rb, "Drop database $rb" );
                $this->run_query( "DROP DATABASE IF EXISTS " . $rp, "Drop database $rp" );
            }

            $this->run_query( "CREATE DATABASE IF NOT EXISTS " . $wf . " DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci", "Create database $wf" );
            $this->run_query( "CREATE DATABASE IF NOT EXISTS " . $rb . " DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci", "Create database $rb" );
            $this->run_query( "CREATE DATABASE IF NOT EXISTS " . $rp . " DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci", "Create database $rp" );

            if ($this->cc_status == 1) {
                $host = ($islocal) ? "localhost" : "%";
                $this->run_query( "GRANT ALL PRIVILEGES ON `$wf`.* TO $wf@'$host' IDENTIFIED BY '{$this->options['password']}' WITH GRANT OPTION", "Grant privileges for user $wf on database $wf" );
                $this->run_query( "GRANT ALL PRIVILEGES ON `$rb`.* TO $rb@'$host' IDENTIFIED BY '{$this->options['password']}' WITH GRANT OPTION", "Grant privileges for user $rb on database $rb" );
                $this->run_query( "GRANT ALL PRIVILEGES ON `$rp`.* TO $rp@'$host' IDENTIFIED BY '{$this->options['password']}' WITH GRANT OPTION", "Grant privileges for user $rp on database $rp" );
            }

            /* Dump schema workflow && data  */

            $this->log( "Import database schema:\n" );
            $myPortA = explode( ":", $this->options['database']['hostname'] );
            if (count( $myPortA ) < 2) {
                $myPortA[1] = "3306";
            }
            $myPort = $myPortA[1];
            $this->options['database']['hostname'] = $myPortA[0];

            mysql_select_db( $wf, $this->connection_database );
            $pws = PATH_WORKFLOW_MYSQL_DATA . $schema;
            $qws = $this->query_sql_file( PATH_WORKFLOW_MYSQL_DATA . $schema, $this->connection_database );
            $this->log( $qws, isset( $qws['errors'] ) );
            $qwv = $this->query_sql_file( PATH_WORKFLOW_MYSQL_DATA . $values, $this->connection_database );
            $this->log( $qwv, isset( $qwv['errors'] ) );

            /* Dump schema rbac && data  */
            $pws = PATH_RBAC_MYSQL_DATA . $schema;
            mysql_select_db( $rb, $this->connection_database );
            $qrs = $this->query_sql_file( PATH_RBAC_MYSQL_DATA . $schema, $this->connection_database );
            $this->log( $qrs, isset( $qrs['errors'] ) );
            $qrv = $this->query_sql_file( PATH_RBAC_MYSQL_DATA . $values, $this->connection_database );
            $this->log( $qrv, isset( $qrv['errors'] ) );

            mysql_select_db( $wf, $this->connection_database );

            require_once ("propel/Propel.php");
            require_once ('classes/model/AppCacheView.php');

            $appCache = new AppCacheView();
            $appCache->setPathToAppCacheFiles( PATH_METHODS . 'setup/setupSchemas/' );
            $triggers = $appCache->getTriggers( "en" );
            $this->log( "Create 'cases list cache' triggers" );
            foreach ($triggers as $triggerName => $trigger) {
                $this->run_query( $trigger, "-> Trigger $triggerName" );
            }

            $path_site = $this->options['path_data'] . "/sites/" . $this->options['name'] . "/";
            $db_file = $path_site . "db.php";
            @mkdir( $path_site, 0777, true );
            @mkdir( $path_site . "files/", 0777, true );
            @mkdir( $path_site . "mailTemplates/", 0777, true );
            @mkdir( $path_site . "public/", 0777, true );
            @mkdir( $path_site . "reports/", 0777, true );
            @mkdir( $path_site . "xmlForms", 0777, true );

            $db_text = "<?php\n" . "// Processmaker configuration\n" . "define ('DB_ADAPTER', 'mysql' );\n" . "define ('DB_HOST', '" . $this->options['database']['hostname'] . ":" . $myPort . "' );\n" . "define ('DB_NAME', '" . $wf . "' );\n" . "define ('DB_USER', '" . (($this->cc_status == 1) ? $wf : $this->options['database']['username']) . "' );\n" . "define ('DB_PASS', '" . (($this->cc_status == 1) ? $this->options['password'] : $this->options['database']['password']) . "' );\n" . "define ('DB_RBAC_HOST', '" . $this->options['database']['hostname'] . ":" . $myPort . "' );\n" . "define ('DB_RBAC_NAME', '" . $rb . "' );\n" . "define ('DB_RBAC_USER', '" . (($this->cc_status == 1) ? $rb : $this->options['database']['username']) . "' );\n" . "define ('DB_RBAC_PASS', '" . (($this->cc_status == 1) ? $this->options['password'] : $this->options['database']['password']) . "' );\n" . "define ('DB_REPORT_HOST', '" . $this->options['database']['hostname'] . ":" . $myPort . "' );\n" . "define ('DB_REPORT_NAME', '" . $rp . "' );\n" . "define ('DB_REPORT_USER', '" . (($this->cc_status == 1) ? $rp : $this->options['database']['username']) . "' );\n" . "define ('DB_REPORT_PASS', '" . (($this->cc_status == 1) ? $this->options['password'] : $this->options['database']['password']) . "' );\n" . "?>";
            $fp = @fopen( $db_file, "w" );
            $this->log( "Create: " . $db_file . "  => " . ((! $fp) ? $fp : "OK") . "\n", $fp === FALSE );
            $ff = @fputs( $fp, $db_text, strlen( $db_text ) );
            $this->log( "Write: " . $db_file . "  => " . ((! $ff) ? $ff : "OK") . "\n", $ff === FALSE );

            fclose( $fp );
            $this->set_admin();
        }
        return $test;
    }

    /**
     * set_admin
     *
     * @return void
     */
    public function set_admin ()
    {
        mysql_select_db( $this->wf_site_name, $this->connection_database );
        //  The mysql_escape_string function has been DEPRECATED as of PHP 5.3.0.
        //  $this->run_query('UPDATE USERS SET USR_USERNAME = \''.mysql_escape_string($this->options['admin']['username']).'\', `USR_PASSWORD` = \''.md5($this->options['admin']['password']).'\' WHERE `USR_UID` = \'00000000000000000000000000000001\' LIMIT 1',
        //    "Add 'admin' user in ProcessMaker (wf)");
        $this->run_query( 'UPDATE USERS SET USR_USERNAME = \'' . mysql_real_escape_string( $this->options['admin']['username'] ) . '\', ' . '  `USR_PASSWORD` = \'' . md5( $this->options['admin']['password'] ) . '\' ' . '  WHERE `USR_UID` = \'00000000000000000000000000000001\' LIMIT 1', "Add 'admin' user in ProcessMaker (wf)" );
        mysql_select_db( $this->rbac_site_name, $this->connection_database );
        // The mysql_escape_string function has been DEPRECATED as of PHP 5.3.0.
        // $this->run_query('UPDATE USERS SET USR_USERNAME = \''.mysql_escape_string($this->options['admin']['username']).'\', `USR_PASSWORD` = \''.md5($this->options['admin']['password']).'\' WHERE `USR_UID` = \'00000000000000000000000000000001\' LIMIT 1',
        //   "Add 'admin' user in ProcessMaker (rb)");
        $this->run_query( 'UPDATE USERS SET USR_USERNAME = \'' . mysql_real_escape_string( $this->options['admin']['username'] ) . '\', ' . '  `USR_PASSWORD` = \'' . md5( $this->options['admin']['password'] ) . '\' ' . '  WHERE `USR_UID` = \'00000000000000000000000000000001\' LIMIT 1', "Add 'admin' user in ProcessMaker (rb)" );
    }

    /**
     * Run a mysql query on the current database and take care of logging and
     * error handling.
     *
     * @param string $query SQL command
     * @param string $description Description to log instead of $query
     */
    private function run_query ($query, $description = NULL)
    {
        $result = @mysql_query( $query, $this->connection_database );
        $error = ($result) ? false : mysql_error();
        $this->log( ($description ? $description : $query) . " => " . (($error) ? $error : "OK") . "\n", $error );
    }

    /**
     * query_sql_file
     *
     * @param string $file
     * @param string $connection
     * @return array $report
     */
    public function query_sql_file ($file, $connection)
    {
        $lines = file( $file );
        $previous = NULL;
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
            $previous = NULL;

            // If the current line doesnt end with ; then put this line together
            // with the next one, thus supporting multi-line statements.
            if (strrpos( $line, ";" ) != strlen( $line ) - 1) {
                $previous = $line;
                continue;
            }

            $line = substr( $line, 0, strrpos( $line, ";" ) );
            @mysql_query( $line, $connection );
        }
    }

    /**
     * check_path
     *
     * @return void
     * @todo Empty function
     */
    private function check_path ()
    {

    }

    /**
     * function find_root_path
     *
     * @param string $path
     * @return string $path
     */
    private function find_root_path ($path)
    {
        $i = 0; //prevent loop inifinity
        while (! is_dir( $path ) && ($path = dirname( $path )) && ((strlen( $path ) > 1) && $i < 10)) {
            $i ++;
        }
        return $path;
    }

    /**
     * file_permisions
     *
     * @param string $file
     * @param integer $def default value 777
     * @return integer $def
     */
    public function file_permisions ($file, $def = 777)
    {
        if (PHP_OS == 'WINNT')
            return $def;
        else
            return (int) substr( sprintf( '%o', @fileperms( $file ) ), - 4 );
    }

    /**
     * is_dir_writable
     *
     * @param string $dir default value empty
     * @return string $path
     */
    public function is_dir_writable ($dir = '')
    {
        if (PHP_OS == 'WINNT') {
            $dir = $this->find_root_path( $dir );
            return file_exists( $dir );
        } else {
            $dir = $this->find_root_path( $dir );
            return (is_writable( $dir ) && is_readable( $dir ));
        }
    }

    /**
     * getDirectoryFiles
     *
     * @param string $dir default value empty
     * @return string $path
     */
    public function getDirectoryFiles ($dir, $extension)
    {
        $filesArray = array ();
        if (file_exists( $dir )) {
            if ($handle = opendir( $dir )) {
                while (false !== ($file = readdir( $handle ))) {
                    $fileParts = explode( ".", $file );
                    if ($fileParts[count( $fileParts ) - 1] == $extension) {
                        $filesArray[] = $file;
                    }
                }
                closedir( $handle );
            }
        }
        return $filesArray;
    }

    /**
     * check_db_empty
     *
     * @param string $dbName
     * @return boolean true or false
     */
    public function check_db_empty ($dbName)
    {
        $a = @mysql_select_db( $dbName, $this->connection_database );
        if (! $a) {
            return true;
        }
        $q = @mysql_query( 'SHOW TABLES', $this->connection_database );
        return (@mysql_num_rows( $q ) > 0) ? false : true;
    }

    /**
     * check_db
     *
     * @param string $dbName
     * @return Array Array('status' => true or false,'message' => string)
     */
    public function check_db ($dbName)
    {
        if (! $this->connection_database) {
            //erik: new verification if the mysql extension is enabled
            $error = class_exists( 'mysql_error' ) ? mysql_error() : 'Mysql Module for PHP is not enabled!';
            return Array ('status' => false,'message' => $error
            );
        } else {
            if (! mysql_select_db( $dbName, $this->connection_database ) && $this->cc_status != 1) {
                return Array ('status' => false,'message' => mysql_error()
                );
            } else {
                /*        var_dump($this->options['advanced']['ao_db_drop'],$this->cc_status,$this->check_db_empty($dbName));
        if(($this->options['advanced']['ao_db_drop']===false && $this->cc_status!=1 && !$this->check_db_empty($dbName)) )
        {
          return Array('status'=>false,'message'=>'Database is not empty');
        }
        else
        {
          return Array('status'=>true,'message'=>'OK');
        }*/
                if ($this->options['advanced']['ao_db_drop'] === true || $this->check_db_empty( $dbName )) {
                    return Array ('status' => true,'message' => 'PASSED'
                    );
                } else {
                    return Array ('status' => false,'message' => 'Database is not empty'
                    );
                }
            }
        }
    }

    /**
     * check_connection
     *
     * @return Array $rt
     */
    private function check_connection ()
    {
        if (! function_exists( "mysql_connect" )) {
            $this->cc_status = 0;
            $rt = Array ('connection' => false,'grant' => 0,'version' => false,'message' => "ERROR: Mysql Module for PHP is not enabled, try install <b>php-mysql</b> package.",'ao' => Array ('ao_db_wf' => false,'ao_db_rb' => false,'ao_db_rp' => false
            )
            );
        } else {
            $this->connection_database = @mysql_connect( $this->options['database']['hostname'], $this->options['database']['username'], $this->options['database']['password'] );
            $rt = Array ('version' => false,'ao' => Array ('ao_db_wf' => false,'ao_db_rb' => false,'ao_db_rp' => false
            )
            );
            if (! $this->connection_database) {
                $this->cc_status = 0;
                $rt['connection'] = false;
                $rt['grant'] = 0;
                $rt['message'] = "Mysql error: " . mysql_error();
            } else {
                preg_match( '@[0-9]+\.[0-9]+\.[0-9]+@', mysql_get_server_info( $this->connection_database ), $version );
                $rt['version'] = version_compare( @$version[0], "4.1.0", ">=" );
                $rt['connection'] = true;

                $dbNameTest = "PROCESSMAKERTESTDC";
                $db = @mysql_query( "CREATE DATABASE " . $dbNameTest, $this->connection_database );
                if (! $db) {
                    $this->cc_status = 3;
                    $rt['grant'] = 3;
                    //$rt['message'] = "Db GRANTS error:  ".mysql_error();
                    $rt['message'] = "Successful connection";
                } else {

                    //@mysql_drop_db("processmaker_testGA");
                    $usrTest = "wfrbtest";
                    $chkG = "GRANT ALL PRIVILEGES ON `" . $dbNameTest . "`.* TO " . $usrTest . "@'%' IDENTIFIED BY 'sample' WITH GRANT OPTION";
                    $ch = @mysql_query( $chkG, $this->connection_database );
                    if (! $ch) {
                        $this->cc_status = 2;
                        $rt['grant'] = 2;
                        //$rt['message'] = "USER PRIVILEGES ERROR";
                        $rt['message'] = "Successful connection";
                    } else {
                        $this->cc_status = 1;
                        @mysql_query( "DROP USER " . $usrTest . "@'%'", $this->connection_database );
                        $rt['grant'] = 1;
                        $rt['message'] = "Successful connection";
                    }
                    @mysql_query( "DROP DATABASE " . $dbNameTest, $this->connection_database );

                }
                //        var_dump($wf,$rb,$rp);
            }
        }
        $rt['ao']['ao_db_wf'] = $this->check_db( $this->options['advanced']['ao_db_wf'] );
        $rt['ao']['ao_db_rb'] = $this->check_db( $this->options['advanced']['ao_db_rb'] );
        $rt['ao']['ao_db_rp'] = $this->check_db( $this->options['advanced']['ao_db_rp'] );
        return $rt;
    }

    /**
     * log
     *
     * @param string $text
     * @return void
     */
    public function log ($text, $failed = NULL)
    {
        array_push( $this->report, $text );
        if ($failed)
            throw new Exception( is_string( $text ) ? $text : var_export( $text, true ) );
    }
}
?>
