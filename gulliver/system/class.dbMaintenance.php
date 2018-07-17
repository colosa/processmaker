<?php

/**
 * class.database_base.php
 *
 * @package gulliver.system
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

/**
 *
 *
 * Database Maintenance class
 *
 * author Erik A. Ortiz <erik@colosa.com, aortiz.erik@gmail.com>
 * date May 17th, 2010
 *
 * @package gulliver.system
 */

class DataBaseMaintenance
{
    private $host;
    private $user;
    private $passwd;

    private $link;
    private $dbName;
    public $result;
    protected $tmpDir;
    protected $outfile;
    protected $infile;
    protected $isWindows;

    /**
     * __construct
     *
     * @param string $host is null
     * @param string $user is null
     * @param string $passwd is null
     *
     * @return none
     */
    public function __construct ($host = null, $user = null, $passwd = null)
    {
        $this->tmpDir = './';
        $this->link = null;
        $this->dbName = null;
        $this->isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
        if (isset( $host ) && isset( $user ) && isset( $passwd )) {
            $this->host = $host;
            $this->user = $user;
            $this->passwd = $passwd;
        }
    }

    /**
     * setUser
     *
     * @param string $user
     *
     * @return none
     */
    public function setUser ($user)
    {
        $this->user = $user;
    }

    /**
     * setPasswd
     *
     * @param string $passwd
     *
     * @return none
     */
    public function setPasswd ($passwd)
    {
        $this->passwd = $passwd;
    }

    /**
     * setHost
     *
     * @param string $host
     *
     * @return none
     */
    public function setHost ($host)
    {
        $this->host = $host;
    }

    /**
     * setTempDir
     *
     * @param string $tmpDir
     *
     * @return none
     */
    public function setTempDir ($tmpDir)
    {
        $this->tmpDir = $tmpDir;
        if (! file_exists( $tmpDir )) {
            mkdir( $this->tmpDir );
        }
    }

    /**
     * getTempDir
     *
     * @return $this->tmpDir
     */
    public function getTempDir ()
    {
        return $this->tmpDir;
    }

    /**
     * status
     *
     * @return $this->link
     */
    public function status ()
    {
        return $$this->link;
    }

    /**
     * connect
     *
     * @param string $dbname is null
     *
     * @return none
     */
    public function connect ($dbname = null)
    {
        if ($this->link != null) {
            mysql_close( $this->link );
            $this->link = null;
        }
        if (isset( $dbname )) {
            $this->dbName = $dbname;
        }

        $this->link = mysql_connect( $this->host, $this->user, $this->passwd );
        @mysql_query( "SET NAMES 'utf8';" );
        @mysql_query( "SET FOREIGN_KEY_CHECKS=0;" );
        if (! $this->link) {
            throw new Exception( "Couldn't connect to host {$this->host} with user {$this->user}" );
        }

        if ($this->dbName != null) {
            $this->selectDataBase( $this->dbName );
        }
    }

    /**
     * setDbName
     *
     * @param string $dbname is null
     *
     * @return none
     */
    public function setDbName ($dbname)
    {
        $this->dbName = $dbname;
    }

    /**
     * selectDataBase
     *
     * @param string $dbname
     *
     * @return none
     */
    public function selectDataBase ($dbname)
    {
        $this->setDbName( $dbname );
        if (! @mysql_select_db( $this->dbName, $this->link )) {
            throw new Exception( "Couldn't select database $dbname" );
        }
    }

    /**
     * query
     *
     * @param string $sql
     *
     * @return $aRows
     */
    public function query ($sql)
    {
        $this->result = @mysql_query( $sql );
        if ($this->result) {
            $aRows = Array ();
            while ($aRow = @mysql_fetch_assoc( $this->result )) {
                array_push( $aRows, $aRow );
            }
            return $aRows;
        } else {
            return false;
        }
    }

    /**
     * error
     *
     * @return @mysql_error()
     */
    public function error ()
    {
        return @mysql_error( $this->link );
    }

    /**
     * getTablesList
     *
     * @return $aRows
     */
    public function getTablesList ()
    {
        $this->result = @mysql_query( "SHOW TABLES;" );
        $aRows = Array ();
        while ($aRow = mysql_fetch_row( $this->result )) {
            array_push( $aRows, $aRow[0] );
        }
        return $aRows;
    }

    /**
     * dumpData
     *
     * @param string $table
     *
     * @return boolean true or false
     */
    function dumpData ($table)
    {
        $this->outfile = $this->tmpDir . $table . '.dump';

        //if the file exists delete it
        if (is_file( $this->outfile )) {
            @unlink( $this->outfile );
        }

        $sql = "SELECT * INTO OUTFILE '{$this->outfile}' FIELDS TERMINATED BY '\t|\t' OPTIONALLY ENCLOSED BY '\"' LINES TERMINATED BY '\t\t\r\r\n' FROM $table";
        // The mysql_escape_string function has been DEPRECATED as of PHP 5.3.0.
        // Commented that is not assigned to a variable.
        // mysql_escape_string("';");
        if (! @mysql_query( $sql )) {
            $ws = (defined("SYS_SYS"))? SYS_SYS : "Wokspace Undefined";
            Bootstrap::registerMonolog('MysqlCron', 400, mysql_error(), array('sql'=>$sql), $ws, 'processmaker.log');
            $varRes = mysql_error() . "\n";
            G::outRes( $varRes );
            return false;
        }
        return true;
    }

    /**
     * restoreData
     *
     * @param string $backupFile
     *
     * @return boolean true or false
     */
    function restoreData ($backupFile)
    {
        $tableName = str_replace( '.dump', '', basename( $backupFile ) );
        $sql = "LOAD DATA INFILE '$backupFile' INTO TABLE $tableName FIELDS TERMINATED BY '\t|\t' OPTIONALLY ENCLOSED BY '\"' LINES TERMINATED BY '\t\t\r\r\n'";
        if (! @mysql_query( $sql )) {
            $ws = (defined("SYS_SYS"))? SYS_SYS : "Wokspace Undefined";
            Bootstrap::registerMonolog('MysqlCron', 400, mysql_error(), array('sql'=>$sql), $ws, 'processmaker.log');
            $varRes = mysql_error() . "\n";
            G::outRes( $varRes );
            return false;
        }
        return true;
    }

    /**
     * restoreAllData
     *
     * @param string $type default value null
     *
     * @return none
     */
    function restoreAllData ($type = null)
    {

        $aTables = $this->getTablesList();

        foreach ($aTables as $table) {
            if (isset( $type ) && $type == 'sql') {
                $this->infile = $this->tmpDir . $table . ".sql";
                if (is_file( $this->infile )) {
                    $queries = $this->restoreFromSql( $this->infile, true );
                    if (! isset( $queries )) {
                        $queries = "unknown";
                    }
                    printf( "%-59s%20s", "Restored table $table", "$queries queries\n" );
                }
            } else {
                $this->infile = $this->tmpDir . $table . ".dump";
                if (is_file( $this->infile )) {
                    $this->restoreData( $this->infile );
                    printf( "%20s %s %s\n", 'Restoring data from ', $this->infile, " in table $table" );
                }
            }
        }
    }

    /**
     * createDb
     *
     * @param string $dbname
     * @param string $drop default value false
     *
     * @return none
     */
    function createDb ($dbname, $drop = false)
    {
        if ($drop) {
            $sql = "DROP DATABASE IF EXISTS $dbname;";
            if (! @mysql_query( $sql )) {
                throw new Exception( mysql_error() );
            }
        }
        $sql = "CREATE DATABASE IF NOT EXISTS $dbname DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;";
        if (! @mysql_query( $sql )) {
            throw new Exception( mysql_error() );
        }
    }

    /**
     * restoreFromSql2
     *
     * @param string $sqlfile
     *
     * @return none
     */
    function restoreFromSql2 ($sqlfile)
    {

        ini_set( 'memory_limit', '512M' );
        if (! is_file( $sqlfile )) {
            throw new Exception( "the $sqlfile doesn't exist!" );
        }
        $query = file_get_contents( $sqlfile );
        $mysqli = new mysqli( $this->host, $this->user, $this->passwd, $this->dbName );

        /* check connection */
        if (mysqli_connect_errno()) {
            printf( "Connect failed: %s\n", mysqli_connect_error() );
            exit();
        }

        /* execute multi query */
        if ($mysqli->multi_query( $query )) {
            do {
                /* store first result set */
                if ($result = $mysqli->store_result()) {
                    while ($row = $result->fetch_row()) {
                    }
                    $result->free();
                }

            } while ($mysqli->next_result());
        }

        /* close connection */
        $mysqli->close();
    }

    /**
     * backupDataBaseSchema
     *
     * @param string $outfile
     *
     * @return none
     */
    function backupDataBase ($outfile)
    {
        $password = escapeshellarg($this->passwd);
        
        //On Windows, escapeshellarg() instead replaces percent signs, exclamation 
        //marks (delayed variable substitution) and double quotes with spaces and 
        //adds double quotes around the string.
        //See: http://php.net/manual/en/function.escapeshellarg.php
        if ($this->isWindows) {
            $password = $this->escapeshellargCustom($this->passwd);
        }
        $aHost = explode(':', $this->host);
        $dbHost = $aHost[0];
        if (isset($aHost[1])) {
            $dbPort = $aHost[1];
            $command = 'mysqldump'
                . ' --user=' . $this->user
                . ' --password=' . $password
                . ' --host=' . $dbHost
                . ' --port=' . $dbPort
                . ' --opt'
                . ' --skip-comments'
                . ' ' . $this->dbName
                . ' > ' . $outfile;
        } else {
            $command = 'mysqldump'
                . ' --host=' . $dbHost
                . ' --user=' . $this->user
                . ' --opt'
                . ' --skip-comments'
                . ' --password=' . $password
                . ' ' . $this->dbName
                . ' > ' . $outfile;
        }
        shell_exec($command);
    }

    /**
     * string escapeshellargCustom ( string $arg , character $quotes)
     * 
     * escapeshellarg() adds single quotes around a string and quotes/escapes any 
     * existing single quotes allowing you to pass a string directly to a shell 
     * function and having it be treated as a single safe argument. This function 
     * should be used to escape individual arguments to shell functions coming 
     * from user input. The shell functions include exec(), system() and the 
     * backtick operator.
     * 
     * On Windows, escapeshellarg() instead replaces percent signs, exclamation 
     * marks (delayed variable substitution) and double quotes with spaces and 
     * adds double quotes around the string.
     */
    private function escapeshellargCustom($string, $quotes = "")
    {
        if ($quotes === "") {
            $quotes = $this->isWindows ? "\"" : "'";
        }
        $n = strlen($string);
        $special = ["!", "%", "\""];
        $substring = "";
        $result1 = [];
        $result2 = [];
        for ($i = 0; $i < $n; $i++) {
            if (in_array($string[$i], $special, true)) {
                $result2[] = $string[$i];
                $result1[] = $substring;
                $substring = "";
            } else {
                $substring = $substring . $string[$i];
            }
        }
        $result1[] = $substring;
        //Rebuild the password string
        $n = count($result1);
        for ($i = 0; $i < $n; $i++) {
            $result1[$i] = trim(escapeshellarg($result1[$i]), $quotes);
            if (isset($result2[$i])) {
                $result1[$i] = $result1[$i] . $result2[$i];
            }
        }
        //add simple quotes, see escapeshellarg function
        $newString = $quotes . implode("", $result1) . $quotes;
        return $newString;
    }

    /**
     * restoreFromSql
     *
     * @param string $sqlfile
     *
     * @return boolean false or true
     */
    function restoreFromSql ($sqlfile, $type = 'file')
    {
        ini_set( 'memory_limit', '64M' );
        if ($type == 'file' && ! is_file( $sqlfile )) {
            throw new Exception( "the $sqlfile doesn't exist!" );
        }

        $metaFile = str_replace( '.sql', '.meta', $sqlfile );

        $queries = 0;

        if (is_file( $metaFile )) {
            echo "Using $metaFile as metadata.\n";
            $fp = fopen( $sqlfile, 'rb' );
            $fpmd = fopen( $metaFile, 'r' );
            while ($offset = fgets( $fpmd, 1024 )) {
                $buffer = intval( $offset ); //reading the size of $oData
                $query = fread( $fp, $buffer ); //reading string $oData
                $queries += 1;

                if (! @mysql_query( $query )) {
                    $varRes = mysql_error() . "\n";
                    G::outRes( $varRes );
                    $varRes = "==>" . $query . "<==\n";
                    G::outRes( $varRes );
                }
            }

        } else {
            $queries = null;
            try {
                $mysqli = new mysqli( $this->host, $this->user, $this->passwd, $this->dbName );
                /* check connection */
                if (mysqli_connect_errno()) {
                    printf( "Connect failed: %s\n", mysqli_connect_error() );
                    exit();
                }
                if ($type == 'file') {
                    $query = file_get_contents( $sqlfile );
                } else if ($type == 'string') {
                    $query = $sqlfile;
                } else {
                    return false;
                }

                if (trim( $query ) == "") {
                    return false;
                }

                    /* execute multi query */
                if ($mysqli->multi_query( $query )) {
                    do {
                        /* store first result set */
                        if ($result = $mysqli->store_result()) {
                            while ($row = $result->fetch_row()) {
                                //printf("%s\n", $row[0]);
                            }
                            $result->free();
                        }
                        /* print divider */
                        if ($mysqli->more_results()) {
                            //printf("-----------------\n");
                        }
                    } while ($mysqli->next_result());
                } else {
                    throw new Exception( mysqli_error( $mysqli ) );
                }

                    /* close connection */
                $mysqli->close();
            } catch (Exception $e) {
                echo $query;
                $token = strtotime("now");
                PMException::registerErrorLog($e, $token);
                G::outRes( G::LoadTranslation("ID_EXCEPTION_LOG_INTERFAZ", array($token)) );
            }
        }
        return $queries;
    }

    /**
     * getSchemaFromTable
     *
     * @param string $tablename
     *
     * @return string $tableSchema
     */
    function getSchemaFromTable ($tablename)
    {
        //$tableSchema = "/* Structure for table `$tablename` */\n";
        //$tableSchema .= "DROP TABLE IF EXISTS `$tablename`;\n\n";
        $tableSchema = "";
        $sql = "show create table `$tablename`; ";
        $result = @mysql_query( $sql );
        if ($result) {
            if ($row = mysql_fetch_assoc( $result )) {
                $tableSchema .= $row['Create Table'] . ";\n\n";
            }
            mysql_free_result( $result );
        } else {
            G::outRes( mysql_error() );
        }
        return $tableSchema;
    }

    /**
     * removeCommentsIntoString
     *
     * @param string $str
     *
     * @return string $str
     */
    function removeCommentsIntoString ($str)
    {
        $str = preg_replace( '/\/\*[\w\W]*\*\//', '', $str );
        $str = preg_replace( "/--[\w\W]*\\n/", '', $str );
        $str = preg_replace( "/\/\/[\w\W]*\\n/", '', $str );
        $str = preg_replace( "/\#[\w\W]*\\n/", '', $str );
        return $str;
    }
}