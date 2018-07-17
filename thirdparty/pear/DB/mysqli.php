<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker: */
// +----------------------------------------------------------------------+
// | PHP Version 4                                                        |
// +----------------------------------------------------------------------+
// | Copyright (c) 1997-2004 The PHP Group                                |
// +----------------------------------------------------------------------+
// | This source file is subject to version 2.02 of the PHP license,      |
// | that is bundled with this package in the file LICENSE, and is        |
// | available at through the world-wide-web at                           |
// | http://www.php.net/license/2_02.txt.                                 |
// | If you did not receive a copy of the PHP license and are unable to   |
// | obtain it through the world-wide-web, please send a note to          |
// | license@php.net so we can mail you a copy immediately.               |
// +----------------------------------------------------------------------+
// | Author: Daniel Convissor <danielc@php.net>                           |
// +----------------------------------------------------------------------+
//
// $Id: mysqli.php 3355 2005-06-11 22:14:40Z nbm $


// EXPERIMENTAL


require_once 'DB/common.php';

/**
 * Database independent query interface definition for PHP's mysqli
 * extension.
 *
 * This is for MySQL versions 4.1 and above.  Requires PHP 5.
 *
 * Note that persistent connections no longer exist.
 *
 * @package  DB
 * @version  $Id: mysqli.php 3355 2005-06-11 22:14:40Z nbm $
 * @category Database
 * @author   Daniel Convissor <danielc@php.net>
 * @since    Class functional since Release 1.6.3
 */
class DB_mysqli extends DB_common
{
    // {{{ properties

    var $connection;
    var $phptype, $dbsyntax;
    var $prepare_tokens = array();
    var $prepare_types = array();
    var $num_rows = array();
    var $transaction_opcount = 0;
    var $autocommit = true;
    var $fetchmode = DB_FETCHMODE_ORDERED; /* Default fetch mode */
    var $_db = false;

    /**
     * Array for converting MYSQLI_*_FLAG constants to text values
     * @var    array
     * @access public
     * @since  Property available since Release 1.6.5
     */
    var $mysqli_flags = array(
        MYSQLI_NOT_NULL_FLAG        => 'not_null',
        MYSQLI_PRI_KEY_FLAG         => 'primary_key',
        MYSQLI_UNIQUE_KEY_FLAG      => 'unique_key',
        MYSQLI_MULTIPLE_KEY_FLAG    => 'multiple_key',
        MYSQLI_BLOB_FLAG            => 'blob',
        MYSQLI_UNSIGNED_FLAG        => 'unsigned',
        MYSQLI_ZEROFILL_FLAG        => 'zerofill',
        MYSQLI_AUTO_INCREMENT_FLAG  => 'auto_increment',
        MYSQLI_TIMESTAMP_FLAG       => 'timestamp',
        MYSQLI_SET_FLAG             => 'set',
        // MYSQLI_NUM_FLAG             => 'numeric',  // unnecessary
        // MYSQLI_PART_KEY_FLAG        => 'multiple_key',  // duplicatvie
        MYSQLI_GROUP_FLAG           => 'group_by'
    );

    /**
     * Array for converting MYSQLI_TYPE_* constants to text values
     * @var    array
     * @access public
     * @since  Property available since Release 1.6.5
     */
    var $mysqli_types = array(
        MYSQLI_TYPE_DECIMAL     => 'decimal',
        MYSQLI_TYPE_TINY        => 'tinyint',
        MYSQLI_TYPE_SHORT       => 'int',
        MYSQLI_TYPE_LONG        => 'int',
        MYSQLI_TYPE_FLOAT       => 'float',
        MYSQLI_TYPE_DOUBLE      => 'double',
        // MYSQLI_TYPE_NULL        => 'DEFAULT NULL',  // let flags handle it
        MYSQLI_TYPE_TIMESTAMP   => 'timestamp',
        MYSQLI_TYPE_LONGLONG    => 'bigint',
        MYSQLI_TYPE_INT24       => 'mediumint',
        MYSQLI_TYPE_DATE        => 'date',
        MYSQLI_TYPE_TIME        => 'time',
        MYSQLI_TYPE_DATETIME    => 'datetime',
        MYSQLI_TYPE_YEAR        => 'year',
        MYSQLI_TYPE_NEWDATE     => 'date',
        MYSQLI_TYPE_ENUM        => 'enum',
        MYSQLI_TYPE_SET         => 'set',
        MYSQLI_TYPE_TINY_BLOB   => 'tinyblob',
        MYSQLI_TYPE_MEDIUM_BLOB => 'mediumblob',
        MYSQLI_TYPE_LONG_BLOB   => 'longblob',
        MYSQLI_TYPE_BLOB        => 'blob',
        MYSQLI_TYPE_VAR_STRING  => 'varchar',
        MYSQLI_TYPE_STRING      => 'char',
        MYSQLI_TYPE_GEOMETRY    => 'geometry',
    );

    // }}}
    // {{{ constructor

    /**
     * DB_mysql constructor.
     *
     * @access public
     */
    function DB_mysqli()
    {
        $this->DB_common();
        $this->phptype = 'mysqli';
        $this->dbsyntax = 'mysqli';
        $this->features = array(
            'prepare' => false,
            'ssl' => true,
            'transactions' => true,
            'limit' => 'alter'
        );
        $this->errorcode_map = array(
            1004 => DB_ERROR_CANNOT_CREATE,
            1005 => DB_ERROR_CANNOT_CREATE,
            1006 => DB_ERROR_CANNOT_CREATE,
            1007 => DB_ERROR_ALREADY_EXISTS,
            1008 => DB_ERROR_CANNOT_DROP,
            1022 => DB_ERROR_ALREADY_EXISTS,
            1046 => DB_ERROR_NODBSELECTED,
            1048 => DB_ERROR_CONSTRAINT,
            1050 => DB_ERROR_ALREADY_EXISTS,
            1051 => DB_ERROR_NOSUCHTABLE,
            1054 => DB_ERROR_NOSUCHFIELD,
            1062 => DB_ERROR_ALREADY_EXISTS,
            1064 => DB_ERROR_SYNTAX,
            1100 => DB_ERROR_NOT_LOCKED,
            1136 => DB_ERROR_VALUE_COUNT_ON_ROW,
            1146 => DB_ERROR_NOSUCHTABLE,
            1216 => DB_ERROR_CONSTRAINT,
            1217 => DB_ERROR_CONSTRAINT,
        );
    }

    // }}}
    // {{{ connect()

    /**
     * Connect to a database and log in as the specified user.
     *
     * @param string $dsn the data source name (see DB::parseDSN for syntax)
     * @param boolean $persistent (optional) whether the connection should
     *                            be persistent
     * @return mixed DB_OK on success, a DB error on failure
     * @access public
     */
    function connect($dsninfo, $persistent = false)
    {
        if (!DB::assertExtension('mysqli')) {
            return $this->raiseError(DB_ERROR_EXTENSION_NOT_FOUND);
        }

        $this->dsn = $dsninfo;
        $conn      = false;
        @ini_set('track_errors', true);

        if ($this->getOption('ssl') === true) {
            $init = mysqli_init();
            mysqli_ssl_set(
                $init,
                empty($dsninfo['key'])    ? null : $dsninfo['key'],
                empty($dsninfo['cert'])   ? null : $dsninfo['cert'],
                empty($dsninfo['ca'])     ? null : $dsninfo['ca'],
                empty($dsninfo['capath']) ? null : $dsninfo['capath'],
                empty($dsninfo['cipher']) ? null : $dsninfo['cipher']
            );
            if ($conn = @mysqli_real_connect($init,
                                             $dsninfo['hostspec'],
                                             $dsninfo['username'],
                                             $dsninfo['password'],
                                             $dsninfo['database'],
                                             $dsninfo['port'],
                                             $dsninfo['socket']))
            {
                $conn = $init;
            }
        } else {
            $conn = @mysqli_connect(
                $dsninfo['hostspec'],
                $dsninfo['username'],
                $dsninfo['password'],
                $dsninfo['database'],
                $dsninfo['port'],
                $dsninfo['socket']
            );
        }

        @ini_restore('track_errors');

        if (!$conn) {
            if (($err = @mysqli_connect_error()) != '') {
                return $this->raiseError(DB_ERROR_CONNECT_FAILED, null, null,
                                         null, $err);
            } elseif (empty($php_errormsg)) {
                return $this->raiseError(DB_ERROR_CONNECT_FAILED);
            } else {
                return $this->raiseError(DB_ERROR_CONNECT_FAILED, null, null,
                                         null, $php_errormsg);
            }
        }

        if ($dsninfo['database']) {
            $this->_db = $dsninfo['database'];
        }

        $this->connection = $conn;
        return DB_OK;
    }

    // }}}
    // {{{ disconnect()

    /**
     * Log out and disconnect from the database.
     *
     * @return boolean true on success, false if not connected
     * @access public
     */
    function disconnect()
    {
        $ret = @mysqli_close($this->connection);
        $this->connection = null;
        return $ret;
    }

    // }}}
    // {{{ simpleQuery()

    /**
     * Send a query to MySQL and return the results as a MySQL resource
     * identifier.
     *
     * @param string $query the SQL query
     * @return mixed a valid MySQL result for successful SELECT
     *               queries, DB_OK for other successful queries.
     *               A DB error is returned on failure.
     * @access public
     */
    function simpleQuery($query)
    {
        $ismanip = DB::isManip($query);
        $this->last_query = $query;
        $query = $this->modifyQuery($query);
        if ($this->_db) {
            if (!@mysqli_select_db($this->connection, $this->_db)) {
                return $this->mysqlRaiseError(DB_ERROR_NODBSELECTED);
            }
        }
        if (!$this->autocommit && $ismanip) {
            if ($this->transaction_opcount == 0) {
                $result = @mysqli_query($this->connection, 'SET AUTOCOMMIT=0');
                $result = @mysqli_query($this->connection, 'BEGIN');
                if (!$result) {
                    return $this->mysqlRaiseError();
                }
            }
            $this->transaction_opcount++;
        }
        $result = @mysqli_query($this->connection, $query);
        if (!$result) {
            return $this->mysqlRaiseError();
        }
# this next block is still sketchy..
        if (is_object($result)) {
            $numrows = $this->numrows($result);
            if (is_object($numrows)) {
                return $numrows;
            }
# need to come up with different means for next line
# since $result is object (int)$result won't fly...
//            $this->num_rows[(int)$result] = $numrows;
            return $result;
        }
        return DB_OK;
    }

    // }}}
    // {{{ nextResult()

    /**
     * Move the internal mysql result pointer to the next available result.
     *
     * This method has not been implemented yet.
     *
     * @param resource $result a valid sql result resource
     * @return false
     * @access public
     */
    function nextResult($result)
    {
        return false;
    }

    // }}}
    // {{{ fetchInto()

    /**
     * Fetch a row and insert the data into an existing array.
     *
     * Formating of the array and the data therein are configurable.
     * See DB_result::fetchInto() for more information.
     *
     * @param resource $result    query result identifier
     * @param array    $arr       (reference) array where data from the row
     *                            should be placed
     * @param int      $fetchmode how the resulting array should be indexed
     * @param int      $rownum    the row number to fetch
     *
     * @return mixed DB_OK on success, null when end of result set is
     *               reached or on failure
     *
     * @see DB_result::fetchInto()
     * @access private
     */
    function fetchInto($result, &$arr, $fetchmode, $rownum=null)
    {
        if ($rownum !== null) {
            if (!@mysqli_data_seek($result, $rownum)) {
                return null;
            }
        }
        if ($fetchmode & DB_FETCHMODE_ASSOC) {
            $arr = @mysqli_fetch_array($result, MYSQLI_ASSOC);
            if ($this->options['portability'] & DB_PORTABILITY_LOWERCASE && $arr) {
                $arr = array_change_key_case($arr, CASE_LOWER);
            }
        } else {
            $arr = @mysqli_fetch_row($result);
        }
        if (!$arr) {
            $errno = @mysqli_errno($this->connection);
            if (!$errno) {
                return null;
            }
            return $this->mysqlRaiseError($errno);
        }
        if ($this->options['portability'] & DB_PORTABILITY_RTRIM) {
            /*
             * Even though this DBMS already trims output, we do this because
             * a field might have intentional whitespace at the end that
             * gets removed by DB_PORTABILITY_RTRIM under another driver.
             */
            $this->_rtrimArrayValues($arr);
        }
        if ($this->options['portability'] & DB_PORTABILITY_NULL_TO_EMPTY) {
            $this->_convertNullArrayValuesToEmpty($arr);
        }
        return DB_OK;
    }

    // }}}
    // {{{ freeResult()

    /**
     * Free the internal resources associated with $result.
     *
     * @param resource $result MySQL result identifier
     * @return bool true on success, false if $result is invalid
     * @access public
     */
    function freeResult($result)
    {
# need to come up with different means for next line
# since $result is object (int)$result won't fly...
//        unset($this->num_rows[(int)$result]);
        return @mysqli_free_result($result);
    }

    // }}}
    // {{{ numCols()

    /**
     * Get the number of columns in a result set.
     *
     * @param $result MySQL result identifier
     *
     * @access public
     *
     * @return int the number of columns per row in $result
     */
    function numCols($result)
    {
        $cols = @mysqli_num_fields($result);

        if (!$cols) {
            return $this->mysqlRaiseError();
        }

        return $cols;
    }

    // }}}
    // {{{ numRows()

    /**
     * Get the number of rows in a result set.
     *
     * @param resource $result MySQL result identifier
     * @return int the number of rows in $result
     * @access public
     */
    function numRows($result)
    {
        $rows = @mysqli_num_rows($result);
        if ($rows === null) {
            return $this->mysqlRaiseError();
        }
        return $rows;
    }

    // }}}
    // {{{ autoCommit()

    /**
     * Enable/disable automatic commits.
     */
    function autoCommit($onoff = false)
    {
        // XXX if $this->transaction_opcount > 0, we should probably
        // issue a warning here.
        $this->autocommit = $onoff ? true : false;
        return DB_OK;
    }

    // }}}
    // {{{ commit()

    /**
     * Commit the current transaction.
     */
    function commit()
    {
        if ($this->transaction_opcount > 0) {
            if ($this->_db) {
                if (!@mysqli_select_db($this->connection, $this->_db)) {
                    return $this->mysqlRaiseError(DB_ERROR_NODBSELECTED);
                }
            }
            $result = @mysqli_query($this->connection, 'COMMIT');
            $result = @mysqli_query($this->connection, 'SET AUTOCOMMIT=1');
            $this->transaction_opcount = 0;
            if (!$result) {
                return $this->mysqlRaiseError();
            }
        }
        return DB_OK;
    }

    // }}}
    // {{{ rollback()

    /**
     * Roll back (undo) the current transaction.
     */
    function rollback()
    {
        if ($this->transaction_opcount > 0) {
            if ($this->_db) {
                if (!@mysqli_select_db($this->connection, $this->_db)) {
                    return $this->mysqlRaiseError(DB_ERROR_NODBSELECTED);
                }
            }
            $result = @mysqli_query($this->connection, 'ROLLBACK');
            $result = @mysqli_query($this->connection, 'SET AUTOCOMMIT=1');
            $this->transaction_opcount = 0;
            if (!$result) {
                return $this->mysqlRaiseError();
            }
        }
        return DB_OK;
    }

    // }}}
    // {{{ affectedRows()

    /**
     * Gets the number of rows affected by the data manipulation
     * query.  For other queries, this function returns 0.
     *
     * @return integer number of rows affected by the last query
     */
    function affectedRows()
    {
        if (DB::isManip($this->last_query)) {
            return @mysqli_affected_rows($this->connection);
        } else {
            return 0;
        }
     }

    // }}}
    // {{{ errorNative()

    /**
     * Get the native error code of the last error (if any) that
     * occured on the current connection.
     *
     * @return int native MySQL error code
     * @access public
     */
    function errorNative()
    {
        return @mysqli_errno($this->connection);
    }

    // }}}
    // {{{ nextId()

    /**
     * Returns the next free id in a sequence
     *
     * @param string  $seq_name  name of the sequence
     * @param boolean $ondemand  when true, the seqence is automatically
     *                           created if it does not exist
     *
     * @return int  the next id number in the sequence.  DB_Error if problem.
     *
     * @internal
     * @see DB_common::nextID()
     * @access public
     */
    function nextId($seq_name, $ondemand = true)
    {
        $seqname = $this->getSequenceName($seq_name);
        do {
            $repeat = 0;
            $this->pushErrorHandling(PEAR_ERROR_RETURN);
            $result = $this->query("UPDATE ${seqname} ".
                                   'SET id=LAST_INSERT_ID(id+1)');
            $this->popErrorHandling();
            if ($result === DB_OK) {
                /** COMMON CASE **/
                $id = @mysqli_insert_id($this->connection);
                if ($id != 0) {
                    return $id;
                }
                /** EMPTY SEQ TABLE **/
                // Sequence table must be empty for some reason, so fill it and return 1
                // Obtain a user-level lock
                $result = $this->getOne("SELECT GET_LOCK('${seqname}_lock',10)");
                if (DB::isError($result)) {
                    return $this->raiseError($result);
                }
                if ($result == 0) {
                    // Failed to get the lock, bail with a DB_ERROR_NOT_LOCKED error
                    return $this->mysqlRaiseError(DB_ERROR_NOT_LOCKED);
                }

                // add the default value
                $result = $this->query("REPLACE INTO ${seqname} (id) VALUES (0)");
                if (DB::isError($result)) {
                    return $this->raiseError($result);
                }

                // Release the lock
                $result = $this->getOne("SELECT RELEASE_LOCK('${seqname}_lock')");
                if (DB::isError($result)) {
                    return $this->raiseError($result);
                }
                // We know what the result will be, so no need to try again
                return 1;

            /** ONDEMAND TABLE CREATION **/
            } elseif ($ondemand && DB::isError($result) &&
                $result->getCode() == DB_ERROR_NOSUCHTABLE)
            {
                $result = $this->createSequence($seq_name);
                // Since createSequence initializes the ID to be 1,
                // we do not need to retrieve the ID again (or we will get 2)
                if (DB::isError($result)) {
                    return $this->raiseError($result);
                } else {
                    // First ID of a newly created sequence is 1
                    return 1;
                }

            /** BACKWARDS COMPAT **/
            } elseif (DB::isError($result) &&
                      $result->getCode() == DB_ERROR_ALREADY_EXISTS)
            {
                // see _BCsequence() comment
                $result = $this->_BCsequence($seqname);
                if (DB::isError($result)) {
                    return $this->raiseError($result);
                }
                $repeat = 1;
            }
        } while ($repeat);

        return $this->raiseError($result);
    }

    /**
     * Creates a new sequence
     *
     * @param string $seq_name  name of the new sequence
     *
     * @return int  DB_OK on success.  A DB_Error object is returned if
     *              problems arise.
     *
     * @internal
     * @see DB_common::createSequence()
     * @access public
     */
    function createSequence($seq_name)
    {
        $seqname = $this->getSequenceName($seq_name);
        $res = $this->query("CREATE TABLE ${seqname} ".
                            '(id INTEGER UNSIGNED AUTO_INCREMENT NOT NULL,'.
                            ' PRIMARY KEY(id))');
        if (DB::isError($res)) {
            return $res;
        }
        // insert yields value 1, nextId call will generate ID 2
        return $this->query("INSERT INTO ${seqname} (id) VALUES (0)");
    }

    // }}}
    // {{{ dropSequence()

    /**
     * Deletes a sequence
     *
     * @param string $seq_name  name of the sequence to be deleted
     *
     * @return int  DB_OK on success.  DB_Error if problems.
     *
     * @internal
     * @see DB_common::dropSequence()
     * @access public
     */
    function dropSequence($seq_name)
    {
        return $this->query('DROP TABLE ' . $this->getSequenceName($seq_name));
    }

    // }}}
    // {{{ _BCsequence()

    /**
     * Backwards compatibility with old sequence emulation implementation
     * (clean up the dupes).
     *
     * @param string $seqname The sequence name to clean up
     * @return mixed DB_Error or true
     */
    function _BCsequence($seqname)
    {
        // Obtain a user-level lock... this will release any previous
        // application locks, but unlike LOCK TABLES, it does not abort
        // the current transaction and is much less frequently used.
        $result = $this->getOne("SELECT GET_LOCK('${seqname}_lock',10)");
        if (DB::isError($result)) {
            return $result;
        }
        if ($result == 0) {
            // Failed to get the lock, can't do the conversion, bail
            // with a DB_ERROR_NOT_LOCKED error
            return $this->mysqlRaiseError(DB_ERROR_NOT_LOCKED);
        }

        $highest_id = $this->getOne("SELECT MAX(id) FROM ${seqname}");
        if (DB::isError($highest_id)) {
            return $highest_id;
        }
        // This should kill all rows except the highest
        // We should probably do something if $highest_id isn't
        // numeric, but I'm at a loss as how to handle that...
        $result = $this->query("DELETE FROM ${seqname} WHERE id <> $highest_id");
        if (DB::isError($result)) {
            return $result;
        }

        // If another thread has been waiting for this lock,
        // it will go thru the above procedure, but will have no
        // real effect
        $result = $this->getOne("SELECT RELEASE_LOCK('${seqname}_lock')");
        if (DB::isError($result)) {
            return $result;
        }
        return true;
    }

    // }}}
    // {{{ quoteIdentifier()

    /**
     * Quote a string so it can be safely used as a table or column name
     *
     * Quoting style depends on which database driver is being used.
     *
     * MySQL can't handle the backtick character (<kbd>`</kbd>) in
     * table or column names.
     *
     * @param string $str  identifier name to be quoted
     *
     * @return string  quoted identifier string
     *
     * @since 1.6.0
     * @access public
     * @internal
     */
    function quoteIdentifier($str)
    {
        return '`' . $str . '`';
    }

    // }}}
    // {{{ escapeSimple()

    /**
     * Escape a string according to the current DBMS's standards
     *
     * @param string $str  the string to be escaped
     *
     * @return string  the escaped string
     *
     * @internal
     */
    function escapeSimple($str) {
        return @mysqli_real_escape_string($this->connection, $str);
    }

    // }}}
    // {{{ modifyQuery()

    function modifyQuery($query)
    {
        return $query;
    }

    // }}}
    // {{{ modifyLimitQuery()

    function modifyLimitQuery($query, $from, $count, $params = array())
    {
        if (DB::isManip($query)) {
            return $query . " LIMIT $count";
        } else {
            return $query . " LIMIT $from, $count";
        }
    }

    // }}}
    // {{{ mysqlRaiseError()

    /**
     * Gather information about an error, then use that info to create a
     * DB error object and finally return that object.
     *
     * @param  integer  $errno  PEAR error number (usually a DB constant) if
     *                          manually raising an error
     * @return object  DB error object
     * @see DB_common::errorCode()
     * @see DB_common::raiseError()
     */
    function mysqlRaiseError($errno = null)
    {
        if ($errno === null) {
            if ($this->options['portability'] & DB_PORTABILITY_ERRORS) {
                $this->errorcode_map[1022] = DB_ERROR_CONSTRAINT;
                $this->errorcode_map[1048] = DB_ERROR_CONSTRAINT_NOT_NULL;
                $this->errorcode_map[1062] = DB_ERROR_CONSTRAINT;
            } else {
                // Doing this in case mode changes during runtime.
                $this->errorcode_map[1022] = DB_ERROR_ALREADY_EXISTS;
                $this->errorcode_map[1048] = DB_ERROR_CONSTRAINT;
                $this->errorcode_map[1062] = DB_ERROR_ALREADY_EXISTS;
            }
            $errno = $this->errorCode(mysqli_errno($this->connection));
        }
        return $this->raiseError($errno, null, null, null,
                                 @mysqli_errno($this->connection) . ' ** ' .
                                 @mysqli_error($this->connection));
    }

    // }}}
    // {{{ tableInfo()

    /**
     * Returns information about a table or a result set.
     *
     * WARNING: this method will probably not work because the mysqli_*()
     * functions it relies upon may not exist.
     *
     * @param object|string  $result  DB_result object from a query or a
     *                                string containing the name of a table
     * @param int            $mode    a valid tableInfo mode
     * @return array  an associative array with the information requested
     *                or an error object if something is wrong
     * @access public
     * @internal
     * @see DB_common::tableInfo()
     */
    function tableInfo($result, $mode = null) {
        if (isset($result->result)) {
            /*
             * Probably received a result object.
             * Extract the result resource identifier.
             */
            $id = $result->result;
            $got_string = false;
        } elseif (is_string($result)) {
            /*
             * Probably received a table name.
             * Create a result resource identifier.
             */
            $id = @mysqli_query($this->connection,
                                "SELECT * FROM $result LIMIT 0");
            $got_string = true;
        } else {
            /*
             * Probably received a result resource identifier.
             * Copy it.
             * Deprecated.  Here for compatibility only.
             */
            $id = $result;
            $got_string = false;
        }

        if (!is_a($id, 'mysqli_result')) {
            return $this->mysqlRaiseError(DB_ERROR_NEED_MORE_DATA);
        }

        if ($this->options['portability'] & DB_PORTABILITY_LOWERCASE) {
            $case_func = 'strtolower';
        } else {
            $case_func = 'strval';
        }

        $count = @mysqli_num_fields($id);

        // made this IF due to performance (one if is faster than $count if's)
        if (!$mode) {
            for ($i=0; $i<$count; $i++) {
                $tmp = @mysqli_fetch_field($id);
                $res[$i]['table'] = $case_func($tmp->table);
                $res[$i]['name']  = $case_func($tmp->name);
                $res[$i]['type']  = isset($this->mysqli_types[$tmp->type]) ?
                                          $this->mysqli_types[$tmp->type] :
                                          'unknown';
                $res[$i]['len']   = $tmp->max_length;

                $res[$i]['flags'] = '';
                foreach ($this->mysqli_flags as $const => $means) {
                    if ($tmp->flags & $const) {
                        $res[$i]['flags'] .= $means . ' ';
                    }
                }
                if ($tmp->def) {
                    $res[$i]['flags'] .= 'default_' . rawurlencode($tmp->def);
                }
                $res[$i]['flags'] = trim($res[$i]['flags']);
            }
        } else { // full
            $res['num_fields']= $count;

            for ($i=0; $i<$count; $i++) {
                $tmp = @mysqli_fetch_field($id);
                $res[$i]['table'] = $case_func($tmp->table);
                $res[$i]['name']  = $case_func($tmp->name);
                $res[$i]['type']  = isset($this->mysqli_types[$tmp->type]) ?
                                          $this->mysqli_types[$tmp->type] :
                                          'unknown';
                $res[$i]['len']   = $tmp->max_length;

                $res[$i]['flags'] = '';
                foreach ($this->mysqli_flags as $const => $means) {
                    if ($tmp->flags & $const) {
                        $res[$i]['flags'] .= $means . ' ';
                    }
                }
                if ($tmp->def) {
                    $res[$i]['flags'] .= 'default_' . rawurlencode($tmp->def);
                }
                $res[$i]['flags'] = trim($res[$i]['flags']);

                if ($mode & DB_TABLEINFO_ORDER) {
                    $res['order'][$res[$i]['name']] = $i;
                }
                if ($mode & DB_TABLEINFO_ORDERTABLE) {
                    $res['ordertable'][$res[$i]['table']][$res[$i]['name']] = $i;
                }
            }
        }

        // free the result only if we were called on a table
        if ($got_string) {
            @mysqli_free_result($id);
        }
        return $res;
    }

    // }}}
    // {{{ getSpecialQuery()

    /**
     * Returns the query needed to get some backend info.
     *
     * @param string $type What kind of info you want to retrieve
     * @return string The SQL query string
     */
    function getSpecialQuery($type)
    {
        switch ($type) {
            case 'tables':
                return 'SHOW TABLES';
            case 'views':
                return DB_ERROR_NOT_CAPABLE;
            case 'users':
                $sql = 'select distinct User from user';
                if ($this->dsn['database'] != 'mysql') {
                    $dsn = $this->dsn;
                    $dsn['database'] = 'mysql';
                    if (DB::isError($db = DB::connect($dsn))) {
                        return $db;
                    }
                    $sql = $db->getCol($sql);
                    $db->disconnect();
                    // XXX Fixme the mysql driver should take care of this
                    if (!@mysqli_select_db($this->connection, $this->dsn['database'])) {
                        return $this->mysqlRaiseError(DB_ERROR_NODBSELECTED);
                    }
                }
                return $sql;
            case 'databases':
                return 'SHOW DATABASES';
            default:
                return null;
        }
    }

   // }}}

}

/*
 * Local variables:
 * tab-width: 4
 * c-basic-offset: 4
 * End:
 */

?>
