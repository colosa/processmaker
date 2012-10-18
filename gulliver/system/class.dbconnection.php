<?php
/**
 * class.dbconnection.php
 *
 * @package gulliver.system
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

/**
 *
 * @package gulliver.system
 *
 */

require_once ("DB.php");

define( 'DB_ERROR_NO_SHOW_AND_CONTINUE', 0 );
define( 'DB_ERROR_SHOW_AND_STOP', 1 );
define( 'DB_ERROR_SHOW_AND_CONTINUE', 2 );
define( 'DB_ERROR_SHOWALL_AND_STOP', 3 );
define( 'DB_ERROR_SHOWALL_AND_CONTINUE', 4 );

/**
 * DBConnection class definition
 * It is useful to stablish a connection
 *
 * @package gulliver.system
 * @author Fernando Ontiveros Lira <fernando@colosa.com>
 * @copyright (C) 2002 by Colosa Development Team.
 */
class DBConnection
{
    var $db;
    var $db_error;
    var $errorLevel;
    var $type;

    /**
     * ***************************************************************
     * /* Error types:
     * /* -1 Fatal error ( clase no instanced )
     * /* -2 Syntax error ( session missing, query malformed, etc )
     * /* -3 warning ( when the engine build a dangerous query, i.e delete without where clause )
     * /*
     * /* Error level:
     * /* 0 don't display any error information and continue.
     * /* 1 display small box with error information and die.
     * /* 2 display small box with error information and continue
     * /* 3 display complete error information and die.
     * /* 4 display complete error information and continue.
     * /*
     * /* Error Structure
     * /* int error code
     * /* string error message
     * /* string error detailed message
     * /*
     * /* In all cases, the error will be saved in the apache log file
     * /*
     * /*
     */

    /**
     * Starts DB connection with default values
     *
     * @author Fernando Ontiveros Lira <fernando@colosa.com>
     * @access public
     * @param const $strServer Host Name
     * @param const $strUser User Name
     * @param const $strPwd Password
     * @param const $strDB Database Name
     * @param string $type Connection Type
     * @param integer $strPort Used Port
     * @param string $errorLevel Error values posibles are:
     * @return string
     *
     */
    function DBConnection ($strServer = DB_HOST, $strUser = DB_USER, $strPwd = DB_PASS, $strDB = DB_NAME, $type = DB_ADAPTER, $strPort = 0, $errorLevel = 2)
    {
        $this->errorLevel = $errorLevel;
        if ($type == null) {
            $type = 'mysql';
        }
        $this->type = $type;
        //print "<hr>$type $strServer, $strUser, $strPwd, $strDB <hr>";
        if ($type == "mysql") {
            $dsn = "mysql://$strUser:$strPwd@$strServer/$strDB";
        }
        if ($type == "pgsql") {
            //$dsn = "pgsql://postgres@$strServer/$strDB";
            $prt = ($strPort == 0 || $strPort == 5432 ? '' : ":$strPort");
            $dsn = "pgsql://$strUser:$strPwd@$strServer$prt/$strDB";
        }
        if ($type == "odbc") {
            $dsn = "odbc://$strUser:$strPwd@$strServer/$strDB";
        }
        if ($type == "mssql") {
            $strServer = substr( $strServer, 0, strpos( $strServer, ':' ) );
            $prt = ($strPort == 0 || $strPort == 1433 ? '' : ":$strPort");
            $dsn = "mssql://$strUser:$strPwd@$strServer$prt/$strDB";
            ///--) $dsn = "mssql://$strUser:$strPwd@$strServer/$strDB";
        }
        if ($type == "oracle") {
            $dsn = "oci8://$strUser:$strPwd@$strServer/$strDB";
        }
        $this->db_error = null;
        if ($type === 'myxml') {
            $this->db = XMLDB::connect( $strServer );
        } else {
            $this->db = DB::connect( $dsn );
        }
        if (DB::isError( $this->db )) {
            $this->db_error = $this->db;
            $this->db = null;
            $this->logError( $this->db_error );
        }
    }

    /**
     * Close Connection and Generate Log Message
     *
     * @author Fernando Ontiveros Lira <fernando@colosa.com>
     * @access public
     * @return void
     */
    function Reset ()
    {
        if ($this->db) {
            $this->db->disconnect();
        }
        $this->db = null;
    }

    /**
     * Disconnect from Data base
     *
     * @author Fernando Ontiveros Lira <fernando@colosa.com>
     * @access public
     * @return void
     */
    function Free ()
    {
        $this->Reset();
    }

    /**
     * Close Connection
     *
     * @author Fernando Ontiveros Lira <fernando@colosa.com>
     * @access public
     * @return void
     */
    function Close ()
    {
        $this->Reset();
    }

    /**
     * log Errors
     *
     * @author Fernando Ontiveros Lira <fernando@colosa.com>
     * @access public
     * @param db_error $obj
     * @param string $errorLevel
     * @return void
     */
    function logError ($obj, $errorLevel = null)
    {
        global $_SESSION;
        global $_SERVER;
        if (is_null( $errorLevel ))
        if (isset( $this->errorLevel )) {
            $errorLevel = $this->errorLevel;
        } else {
            $errorLevel = DB_ERROR_SHOWALL_AND_STOP; //for fatal errors the default is 3, show detailed and die.
        }

        if ($errorLevel == DB_ERROR_SHOW_AND_STOP || $errorLevel == DB_ERROR_SHOW_AND_CONTINUE || $errorLevel == DB_ERROR_SHOWALL_AND_STOP || $errorLevel == DB_ERROR_SHOWALL_AND_CONTINUE) {
            print "<table border=1 style='font-family:Arial' cellspacing=1 cellpadding = 0 width=400 class= 'tableError' >";
            print "<tr><td><b>" . $obj->code . ' ' . $obj->message . "</b></td></tr>";
            if ($errorLevel == DB_ERROR_SHOWALL_AND_STOP || $errorLevel == DB_ERROR_SHOWALL_AND_CONTINUE) {
                print "<tr><td>" . $obj->userinfo . "</td></tr>";
            }
            print "</table>";
        }
        if (defined( 'DB_ERROR_BACKTRACE' ) && DB_ERROR_BACKTRACE) {
            print "<table border = 1 width=400 class= 'sendMsgRojo'><tr><td><textarea rows='12' cols='180' style='width:100%;font-family:courier;white-space:pre-line;overflow:auto;border:none;'>";
            print ((htmlentities( DBConnection::traceError() ))) ;
            print "</textarea></td></tr></table>";
        }
        //G::setErrorHandler ( );
        G::customErrorLog( 'DB_Error', $obj->code . ' ' . $obj->message . '-' . $obj->userinfo, '', '' );
        if ($errorLevel == DB_ERROR_SHOW_AND_STOP || $errorLevel == DB_ERROR_SHOWALL_AND_STOP) {
            die(); //stop
        }
    }

    /**
     * Get the trace of the current execution (debug_backtrace).
     *
     * @author David Callizaya
     * @param string $tts
     * @param string $limit
     * @return string
     */
    function traceError ($tts = 2, $limit = -1)
    {
        $trace = debug_backtrace();
        $out = '';
        foreach ($trace as $step) {
            if ($tts > 0) {
                $tts --;
            } else {
                $out .= '[' . basename( $step['file'] ) . ': ' . $step['line'] . '] : ' . $step['function'] . '(' . DBConnection::printArgs( $step['args'] ) . ")\n";
                $limit --;
                if ($limit === 0) {
                    return $out;
                }
            }
        }
        return $out;
    }

    /**
     * Print the arguments of a function
     *
     * @author David Callizaya
     * @param string $args
     * @return string
     */
    function printArgs ($args)
    {
        $out = '';
        if (is_array( $args )) {
            foreach ($args as $arg) {
                if ($out !== '') {
                    $out .= ' ,';
                }
                if (is_string( $arg )) {
                    $out .= "'" . ($arg) . "'";
                } elseif (is_array( $arg )) {
                    $out .= print_r( $arg, 1 );
                } elseif (is_object( $arg )) {
                    $out .= get_class( $arg ); // print_r ( $arg ,1 );
                } elseif (! isset( $arg )) {
                    $out .= 'NULL';
                } else {
                    $out .= sprintf( "%s", $arg );
                }
            }
        } else {
            if (! isset( $args )) {
                $out = 'NULL';
            } else {
                $out = print_r( $args, 1 );
            }
        }
        return $out;
    }

    /**
     * Gets last autoincrement value inserted
     *
     * @author Fernando Ontiveros Lira <fernando@colosa.com>
     * @access public
     * @return void
     */
    function GetLastID ()
    {
        if (PEAR_DATABASE == "mysql") {
            return mysql_insert_id();
        } else {
            $dberror = PEAR::raiseError( null, DB_ERROR_FEATURE_NOT_AVAILABLE, null, 'null', "getLastID with " . PEAR_DATABASE . ' database.', 'G_Error', true );
            DBconnection::logError( $dberror, DB_ERROR_SHOWALL_AND_STOP ); //this error will stop the execution, until we add this feature!!
            return $dberror;
        }
        return mysql_insert_id();
    }
}

