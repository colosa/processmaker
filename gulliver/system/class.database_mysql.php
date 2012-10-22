<?php
/**
 * class.database_mysql.php
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

G::LoadSystem( 'database_base' );

class database extends database_base
{

    public $iFetchType = MYSQL_ASSOC;

    /**
     * class database constructor
     *
     * @param $sType adapter type
     * @param $sServer server
     * @param $sUser db user
     * @param $sPass db user password
     * @param $sDataBase Database name
     */
    public function __construct ($sType = DB_ADAPTER, $sServer = DB_HOST, $sUser = DB_USER, $sPass = DB_PASS, $sDataBase = DB_NAME)
    {
        $this->sType = $sType;
        $this->sServer = $sServer;
        $this->sUser = $sUser;
        $this->sPass = $sPass;
        $this->sDataBase = $sDataBase;
        $this->oConnection = @mysql_connect( $sServer, $sUser, $sPass ) || null;
        $this->sQuoteCharacter = '`';
        $this->nullString = 'null';
    }

    /**
     * generate the sql sentence to create a table
     *
     * @param $sTable table name
     * @param $aColumns array of columns
     * @return $sSql the sql sentence
     */
    public function generateCreateTableSQL ($sTable, $aColumns)
    {
        $sKeys = '';
        $sSQL = 'CREATE TABLE IF NOT EXISTS ' . $this->sQuoteCharacter . $sTable . $this->sQuoteCharacter . '(';

        foreach ($aColumns as $sColumnName => $aParameters) {
            if ($sColumnName != 'INDEXES') {

                if ($sColumnName != '' && isset( $aParameters['Type'] ) && $aParameters['Type'] != '') {
                    $sSQL .= $this->sQuoteCharacter . $sColumnName . $this->sQuoteCharacter . ' ' . $aParameters['Type'];

                    if (isset( $aParameters['Null'] ) && $aParameters['Null'] == 'YES') {
                        $sSQL .= ' NULL';
                    } else {
                        $sSQL .= ' NOT NULL';
                    }
                    if (isset( $aParameters['Key'] ) && $aParameters['Key'] == 'PRI') {
                        $sKeys .= $this->sQuoteCharacter . $sColumnName . $this->sQuoteCharacter . ',';
                    }

                    if (isset( $aParameters['Default'] ) && $aParameters['Default'] != '') {
                        $sSQL .= " DEFAULT '" . $aParameters['Default'] . "'";
                    }

                    $sSQL .= ',';
                }
            }
        }
        $sSQL = substr( $sSQL, 0, - 1 );
        if ($sKeys != '') {
            $sSQL .= ',PRIMARY KEY(' . substr( $sKeys, 0, - 1 ) . ')';
        }
        $sSQL .= ')' . $this->sEndLine;

        return $sSQL;
    }

    /**
     * generate a drop table sentence
     *
     * @param $sTable table name
     * @return sql sentence string
     */
    public function generateDropTableSQL ($sTable)
    {
        return 'DROP TABLE ' . $this->sQuoteCharacter . $sTable . $this->sQuoteCharacter . $this->sEndLine;
    }

    /**
     * generate drop column sentence
     *
     * @param $sTable table name
     * @param $sColumn column name
     * @return $sSql sql sentence
     */
    public function generateDropColumnSQL ($sTable, $sColumn)
    {
        $sSQL = 'ALTER TABLE ' . $this->sQuoteCharacter . $sTable . $this->sQuoteCharacter . ' DROP COLUMN ' . $this->sQuoteCharacter . $sColumn . $this->sQuoteCharacter . $this->sEndLine;
        return $sSQL;
    }

    /**
     * generate an add column sentence
     *
     * @param $sTable table name
     * @param $sColumn column name
     * @param $aParameters parameters of field like typo or if it can be null
     * @return $sSql sql sentence
     */
    public function generateAddColumnSQL ($sTable, $sColumn, $aParameters)
    {
        if (isset( $aParameters['Type'] ) && isset( $aParameters['Null'] )) {
            $sSQL = 'ALTER TABLE ' . $this->sQuoteCharacter . $sTable . $this->sQuoteCharacter . ' ADD COLUMN ' . $this->sQuoteCharacter . $sColumn . $this->sQuoteCharacter . ' ' . $aParameters['Type'];
            if ($aParameters['Null'] == 'YES') {
                $sSQL .= ' NULL';
            } else {
                $sSQL .= ' NOT NULL';
            }
        }
        /*if ($aParameters['Key'] == 'PRI') {
         $sKeys .= 'ALTER TABLE ' . $this->sQuoteCharacter . $sTable . $this->sQuoteCharacter .
                ' ADD PRIMARY KEY (' . $this->sQuoteCharacter . $sColumn . $this->sQuoteCharacter . ')' . $this->sEndLine;
         }*/
        if (isset( $aParameters['AI'] )) {
            if ($aParameters['AI'] == 1) {
                $sSQL .= ' AUTO_INCREMENT';
            } else {
                if ($aParameters['Default'] != '') {
                    $sSQL .= " DEFAULT '" . $aParameters['Default'] . "'";
                }
            }
        } else {
            if (isset( $aParameters['Default'] )) {
                $sSQL .= " DEFAULT '" . $aParameters['Default'] . "'";
            }
        }
        $sSQL .= $this->sEndLine;
        return $sSQL;
    }

    /**
     * generate a change column sentence
     *
     * @param $sTable table name
     * @param $sColumn column name
     * @param $aParameters parameters of field like typo or if it can be null
     * @param $sColumnNewName column new name
     * @return $sSql sql sentence
     */
    public function generateChangeColumnSQL ($sTable, $sColumn, $aParameters, $sColumnNewName = '')
    {
        $sSQL = 'ALTER TABLE ' . $this->sQuoteCharacter . $sTable . $this->sQuoteCharacter . ' CHANGE COLUMN ' . $this->sQuoteCharacter . ($sColumnNewName != '' ? $sColumnNewName : $sColumn) . $this->sQuoteCharacter . ' ' . $this->sQuoteCharacter . $sColumn . $this->sQuoteCharacter;
        if (isset( $aParameters['Type'] )) {
            $sSQL .= ' ' . $aParameters['Type'];
        }
        if (isset( $aParameters['Null'] )) {
            if ($aParameters['Null'] == 'YES') {
                $sSQL .= ' NULL';
            } else {
                $sSQL .= ' NOT NULL';
            }
        }
        //if (isset($aParameters['AI'])) {
        //  if ($aParameters['AI'] == 1) {
        //    $sSQL .= ' AUTO_INCREMENT';
        //  }
        //  else {
        //    if (isset($aParameters['Default'])) {
        //      if ($aParameters['Default'] != '') {
        //        $sSQL .= " DEFAULT '" . $aParameters['Default'] . "'";
        //      }
        //    }
        //  }
        //}
        //else {
        if (isset( $aParameters['Default'] )) {
            if (trim( $aParameters['Default'] ) == '' && $aParameters['Type'] == 'datetime') {
                //do nothing
            } else {
                $sSQL .= " DEFAULT '" . $aParameters['Default'] . "'";
            }
            //}
        }
        if (! isset( $aParameters['Default'] ) && isset( $aParameters['Null'] ) && $aParameters['Null'] == 'YES') {
            $sSQL .= " DEFAULT NULL ";
        }
        //}
        $sSQL .= $this->sEndLine;
        return $sSQL;
    }

    /**
     * Generate and get the primary key in a sentence
     *
     * @param $sTable table name
     * @return $sSql sql sentence
     */
    public function generateGetPrimaryKeysSQL ($sTable)
    {
        try {
            if ($sTable == '') {
                throw new Exception( 'The table name cannot be empty!' );
            }
            return 'SHOW INDEX FROM  ' . $this->sQuoteCharacter . $sTable . $this->sQuoteCharacter . ' WHERE Seq_in_index = 1' . $this->sEndLine;
        } catch (Exception $oException) {
            throw $oException;
        }
    }

    /**
     * generate a sentence to drop the primary key
     *
     * @param $sTable table name
     * @return sql sentence
     */
    public function generateDropPrimaryKeysSQL ($sTable)
    {
        try {
            if ($sTable == '') {
                throw new Exception( 'The table name cannot be empty!' );
            }
            return 'ALTER TABLE ' . $this->sQuoteCharacter . $sTable . $this->sQuoteCharacter . ' DROP PRIMARY KEY' . $this->sEndLine;
        } catch (Exception $oException) {
            throw $oException;
        }
    }

    /**
     * generate a sentence to add multiple primary keys
     *
     * @param $sTable table name
     * @param $aPrimaryKeys array of primary keys
     * @return sql sentence
     */
    public function generateAddPrimaryKeysSQL ($sTable, $aPrimaryKeys)
    {
        try {
            if ($sTable == '') {
                throw new Exception( 'The table name cannot be empty!' );
            }
            $sSQL = 'ALTER TABLE ' . $this->sQuoteCharacter . $sTable . $this->sQuoteCharacter . ' ADD PRIMARY KEY (';
            foreach ($aPrimaryKeys as $sKey) {
                $sSQL .= $this->sQuoteCharacter . $sKey . $this->sQuoteCharacter . ',';
            }
            $sSQL = substr( $sSQL, 0, - 1 ) . ')' . $this->sEndLine;
            return $sSQL;
        } catch (Exception $oException) {
            throw $oException;
        }
    }

    /**
     * generate a sentence to drop an index
     *
     * @param $sTable table name
     * @param $sIndexName index name
     * @return sql sentence
     */
    public function generateDropKeySQL ($sTable, $sIndexName)
    {
        try {
            if ($sTable == '') {
                throw new Exception( 'The table name cannot be empty!' );
            }
            if ($sIndexName == '') {
                throw new Exception( 'The column name cannot be empty!' );
            }
            return 'ALTER TABLE ' . $this->sQuoteCharacter . $sTable . $this->sQuoteCharacter . ' DROP INDEX ' . $this->sQuoteCharacter . $sIndexName . $this->sQuoteCharacter . $this->sEndLine;
        } catch (Exception $oException) {
            throw $oException;
        }
    }

    /**
     * generate a sentence to add indexes or primary keys
     *
     * @param $sTable table name
     * @param $indexName index name
     * @param $aKeys array of keys
     * @return sql sentence
     */

    public function generateAddKeysSQL ($sTable, $indexName, $aKeys)
    {
        try {
            $indexType = 'INDEX';
            if ($indexName == 'primaryKey' || $indexName == 'PRIMARY') {
                $indexType = 'PRIMARY';
                $indexName = 'KEY';
            }
            $sSQL = 'ALTER TABLE ' . $this->sQuoteCharacter . $sTable . $this->sQuoteCharacter . ' ADD ' . $indexType . ' ' . $indexName . ' (';
            foreach ($aKeys as $sKey) {
                $sSQL .= $this->sQuoteCharacter . $sKey . $this->sQuoteCharacter . ', ';
            }
            $sSQL = substr( $sSQL, 0, - 2 );
            $sSQL .= ')' . $this->sEndLine;
            return $sSQL;
        } catch (Exception $oException) {
            throw $oException;
        }
    }

    /**
     * generate a sentence to show the tables
     *
     * @return sql sentence
     */
    public function generateShowTablesSQL ()
    {
        return 'SHOW TABLES' . $this->sEndLine;
    }

    /**
     * generate a sentence to show the tables with a like sentence
     *
     * @return sql sentence
     */
    public function generateShowTablesLikeSQL ($sTable)
    {
        return "SHOW TABLES LIKE '" . $sTable . "'" . $this->sEndLine;
    }

    /**
     * generate a sentence to show the tables with a like sentence
     *
     * @param $sTable table name
     * @return sql sentence
     */
    public function generateDescTableSQL ($sTable)
    {
        try {
            if ($sTable == '') {
                throw new Exception( 'The table name cannot be empty!' );
            }
            return 'DESC ' . $this->sQuoteCharacter . $sTable . $this->sQuoteCharacter . $this->sEndLine;
        } catch (Exception $oException) {
            throw $oException;
        }
    }

    /**
     * generate a sentence to show some table indexes
     *
     * @param $sTable table name
     * @return sql sentence
     */
    public function generateTableIndexSQL ($sTable)
    {
        return 'SHOW INDEX FROM ' . $this->sQuoteCharacter . $sTable . $this->sQuoteCharacter . " " . $this->sEndLine;
        //return 'SHOW INDEX FROM ' . $this->sQuoteCharacter . $sTable . $this->sQuoteCharacter . " WHERE Key_name <> 'PRIMARY'" . $this->sEndLine;
    }

    /**
     * execute a sentence to check if there is connection
     *
     * @return void
     */
    public function isConnected ()
    {
        if (! $this->oConnection) {
            return false;
        }
        return $this->executeQuery( 'USE ' . $this->sDataBase );
    }

    /**
     * generate a sentence to show the tables with a like sentence
     *
     * @param $sQuery sql query string
     * @return void
     */
    public function logQuery ($sQuery)
    {
        try {
            $found = false;
            if (substr( $sQuery, 0, 6 ) == 'SELECT') {
                $found = true;
            }
            if (substr( $sQuery, 0, 4 ) == 'SHOW') {
                $found = true;
            }
            if (substr( $sQuery, 0, 4 ) == 'DESC') {
                $found = true;
            }
            if (substr( $sQuery, 0, 4 ) == 'USE ') {
                $found = true;
            }
            if (! $found) {
                $logDir = PATH_DATA . 'log';
                if (! file_exists( $logDir )) {
                    if (! mkdir( $logDir )) {
                        return;
                    }
                }
                $logFile = "$logDir/query.log";
                $fp = fopen( $logFile, 'a+' );
                if ($fp !== false) {
                    fwrite( $fp, date( "Y-m-d H:i:s" ) . " " . $this->sDataBase . " " . $sQuery . "\n" );
                    fclose( $fp );
                }
            }
        } catch (Exception $oException) {
        }
    }

    /**
     * execute a sql query
     *
     * @param $sQuery table name
     * @return void
     */
    public function executeQuery ($sQuery)
    {
        $this->logQuery( $sQuery );

        try {
            if ($this->oConnection) {
                @mysql_select_db( $this->sDataBase );

                return @mysql_query( $sQuery );
            } else {
                throw new Exception( 'invalid connection to database ' . $this->sDataBase );
            }
        } catch (Exception $oException) {
            $this->logQuery( $oException->getMessage() );
            throw $oException;
        }
    }

    /**
     * count the rows of a dataset
     *
     * @param $oDataset
     * @return the number of rows
     */
    public function countResults ($oDataset)
    {
        return @mysql_num_rows( $oDataset );
    }

    /**
     * count an array of the registry from a dataset
     *
     * @param $oDataset
     * @return the registry
     */
    public function getRegistry ($oDataset)
    {
        return @mysql_fetch_array( $oDataset, $this->iFetchType );
    }

    /**
     * close the current connection
     *
     * @return void
     */
    public function close ()
    {
        @mysql_close( $this->oConnection );
    }

    public function generateInsertSQL ($table, $data)
    {
        $fields = array ();
        $values = array ();
        foreach ($data as $field) {
            $fields[] = $field['field'];
            if (! is_null( $field['value'] )) {
                switch ($field['type']) {
                    case 'text':
                    case 'date':
                        $values[] = "'" . mysql_real_escape_string( $field['value'] ) . "'";
                        break;
                    case 'int':
                    default:
                        $values[] = mysql_real_escape_string( $field['value'] );
                        break;
                }
            } else {
                $values[] = $this->nullString;
            }
        }
        $fields = array_map( array ($this,'putQuotes'
        ), $fields );
        $sql = sprintf( "INSERT INTO %s (%s) VALUES (%s)", $this->putQuotes( $table ), implode( ', ', $fields ), implode( ', ', $values ) );
        return $sql;
    }

    public function generateUpdateSQL ($table, $keys, $data)
    {
        $fields = array ();
        $where = array ();
        foreach ($data as $field) {
            if (! is_null( $field['value'] )) {
                switch ($field['type']) {
                    case 'text':
                    case 'date':
                        $fields[] = $this->putQuotes( $field['field'] ) . " = '" . mysql_real_escape_string( $field['value'] ) . "'";
                        break;
                    case 'int':
                    default:
                        $fields[] = $this->putQuotes( $field['field'] ) . " = " . mysql_real_escape_string( $field['value'] );
                        break;
                }
            } else {
                $values[] = $this->nullString;
            }
            if (in_array( $field['field'], $keys )) {
                $where[] = $fields[count( $fields ) - 1];
            }
        }
        $sql = sprintf( "UPDATE %s SET %s WHERE %s", $this->putQuotes( $table ), implode( ', ', $fields ), implode( ', ', $where ) );
        return $sql;
    }

    public function generateDeleteSQL ($table, $keys, $data)
    {
        $fields = array ();
        $where = array ();
        foreach ($data as $field) {
            if (in_array( $field['field'], $keys )) {
                if (! is_null( $field['value'] )) {
                    switch ($field['type']) {
                        case 'text':
                        case 'date':
                            $where[] = $this->putQuotes( $field['field'] ) . " = '" . mysql_real_escape_string( $field['value'] ) . "'";
                            break;
                        case 'int':
                        default:
                            $where[] = $this->putQuotes( $field['field'] ) . " = " . mysql_real_escape_string( $field['value'] );
                            break;
                    }
                } else {
                    $values[] = $this->nullString;
                }
            }
        }
        $sql = sprintf( "DELETE FROM %s WHERE %s", $this->putQuotes( $table ), implode( ', ', $where ) );
        return $sql;
    }

    public function generateSelectSQL ($table, $keys, $data)
    {
        $fields = array ();
        $where = array ();
        foreach ($data as $field) {
            if (in_array( $field['field'], $keys )) {
                if (! is_null( $field['value'] )) {
                    switch ($field['type']) {
                        case 'text':
                        case 'date':
                            $where[] = $this->putQuotes( $field['field'] ) . " = '" . mysql_real_escape_string( $field['value'] ) . "'";
                            break;
                        case 'int':
                        default:
                            $where[] = $this->putQuotes( $field['field'] ) . " = " . mysql_real_escape_string( $field['value'] );
                            break;
                    }
                } else {
                    $values[] = $this->nullString;
                }
            }
        }
        $sql = sprintf( "SELECT * FROM %s WHERE %s", $this->putQuotes( $table ), implode( ', ', $where ) );
        return $sql;
    }

    private function putQuotes ($element)
    {
        return $this->sQuoteCharacter . $element . $this->sQuoteCharacter;
    }

    /*=================================================================================================*/
    /**
     * concatString
     * Generates a string equivalent to the chosen database.
     *
     * author Hector Cortez <hector@gmail.com>
     * date 2010-08-04
     *
     * @return string $sConcat
     */
    public function concatString ()
    {
        $nums = func_num_args();
        $vars = func_get_args();

        $sConcat = " CONCAT(";
        for ($i = 0; $i < $nums; $i ++) {
            if (isset( $vars[$i] )) {
                $sConcat .= $vars[$i];
                if (($i + 1) < $nums) {
                    $sConcat .= ", ";
                }
            }
        }
        $sConcat .= ")";

        return $sConcat;

    }

    /*
     * query functions for class class.case.php
     *
     */
    /**
     * concatString
     * Generates a string equivalent to the case when
     *
     * author Hector Cortez <hector@gmail.com>
     * date 2010-08-04
     *
     * @return string $sCompare
     */
    public function getCaseWhen ($compareValue, $trueResult, $falseResult)
    {
        $sCompare = "IF(" . $compareValue . ", " . $trueResult . ", " . $falseResult . ") ";
        return $sCompare;

    }

    /**
     * Generates a string equivalent to create table ObjectPermission
     *
     * class.case.php
     * function verifyTable()
     *
     * @return string $sql
     */
    public function createTableObjectPermission ()
    {
        $sql = "CREATE TABLE IF NOT EXISTS `OBJECT_PERMISSION` (
                   `OP_UID` varchar(32) NOT NULL,
                   `PRO_UID` varchar(32) NOT NULL,
                   `TAS_UID` varchar(32) NOT NULL,
                   `USR_UID` varchar(32) NOT NULL,
                   `OP_USER_RELATION` int(1) NOT NULL default '1',
                   `OP_TASK_SOURCE` varchar(32) NOT NULL,
                   `OP_PARTICIPATE` int(1) NOT NULL default '1',
                   `OP_OBJ_TYPE` varchar(15) NOT NULL default 'ANY',
                   `OP_OBJ_UID` varchar(32) NOT NULL,
                   `OP_ACTION` varchar(10) NOT NULL default 'VIEW',
                   KEY `PRO_UID` (`PRO_UID`,`TAS_UID`,`USR_UID`,`OP_TASK_SOURCE`,`OP_OBJ_UID`)
                   )ENGINE=MyISAM DEFAULT CHARSET=latin1;";
        return $sql;
    }

    /*
     * query functions for class class.report.php
     *
     */
    /**
     * Generates a string query
     *
     * class.report.php
     * function generatedReport4()
     *
     * @return string $sql
     */
    public function getSelectReport4 ()
    {

        $sqlConcat = " CONCAT(U.USR_LASTNAME,' ',USR_FIRSTNAME) AS USER ";
        $sqlGroupBy = " USER ";

        $sql = "SELECT " . $sqlConcat . ", " . "  COUNT(*) AS CANTCASES,
                MIN(AD.DEL_DURATION) AS MIN,
                MAX(AD.DEL_DURATION) AS MAX,
                SUM(AD.DEL_DURATION) AS TOTALDUR,
                AVG(AD.DEL_DURATION) AS PROMEDIO
                FROM APPLICATION AS A
                LEFT JOIN APP_DELEGATION AS AD ON(A.APP_UID = AD.APP_UID AND AD.DEL_INDEX=1)
                LEFT JOIN USERS AS U ON(U.USR_UID = A.APP_INIT_USER)
                WHERE A.APP_UID<>''
                GROUP BY " . $sqlGroupBy;

        return $sql;

    }

    /**
     * Generates a string query
     *
     * class.report.php
     * function generatedReport4_filter()
     *
     * @return string $sql
     */
    public function getSelectReport4Filter ($var)
    {
        $sqlConcat = " CONCAT(U.USR_LASTNAME,' ',USR_FIRSTNAME) AS USER ";
        $sqlGroupBy = " USER ";

        $sql = " SELECT " . $sqlConcat . ", " . " COUNT(*) AS CANTCASES,
             MIN(AD.DEL_DURATION) AS MIN,
             MAX(AD.DEL_DURATION) AS MAX,
             SUM(AD.DEL_DURATION) AS TOTALDUR,
             AVG(AD.DEL_DURATION) AS PROMEDIO
             FROM APPLICATION AS A
             LEFT JOIN APP_DELEGATION AS AD ON(A.APP_UID = AD.APP_UID AND AD.DEL_INDEX=1)
             LEFT JOIN USERS AS U ON(U.USR_UID = A.APP_INIT_USER)
             " . $var . "
             GROUP BY " . $sqlGroupBy;

        return $sql;

    }

    /**
     * Generates a string query
     *
     * class.report.php
     * function generatedReport5()
     *
     * @return string $sql
     */
    public function getSelectReport5 ()
    {
        $sqlConcat = " CONCAT(U.USR_LASTNAME,' ',USR_FIRSTNAME) AS USER ";
        $sqlGroupBy = " USER ";

        $sql = " SELECT " . $sqlConcat . ", " . " COUNT(*) AS CANTCASES,
              MIN(AD.DEL_DURATION) AS MIN,
              MAX(AD.DEL_DURATION) AS MAX,
              SUM(AD.DEL_DURATION) AS TOTALDUR,
              AVG(AD.DEL_DURATION) AS PROMEDIO
              FROM APP_DELEGATION AS AD
              LEFT JOIN PROCESS AS P ON (P.PRO_UID = AD.PRO_UID)
              LEFT JOIN USERS AS U ON(U.USR_UID = AD.USR_UID)
              WHERE AD.APP_UID<>'' AND AD.DEL_FINISH_DATE IS NULL
              GROUP BY " . $sqlGroupBy;

        return $sql;

    }

    /**
     * Generates a string query
     *
     * class.report.php
     * function generatedReport5_filter()
     *
     * @return string $sql
     */
    public function getSelectReport5Filter ($var)
    {

        $sqlConcat = " CONCAT(U.USR_LASTNAME,' ',USR_FIRSTNAME) AS USER ";
        $sqlGroupBy = " USER ";

        $sql = "SELECT " . $sqlConcat . ", " . "  COUNT(*) AS CANTCASES,
              MIN(AD.DEL_DURATION) AS MIN,
              MAX(AD.DEL_DURATION) AS MAX,
              SUM(AD.DEL_DURATION) AS TOTALDUR,
              AVG(AD.DEL_DURATION) AS PROMEDIO
              FROM APP_DELEGATION AS AD
              LEFT JOIN PROCESS AS P ON (P.PRO_UID = AD.PRO_UID)
              LEFT JOIN USERS AS U ON(U.USR_UID = AD.USR_UID)
              " . $var . "
              GROUP BY " . $sqlGroupBy;

        return $sql;
    }

    /*
     * query functions for class class.net.php
     *
     */
    public function getServerVersion ($driver, $dbIP, $dbPort, $dbUser, $dbPasswd, $dbSourcename)
    {

        if ($link = @mysql_connect( $dbIP, $dbUser, $dbPasswd )) {
            $v = @mysql_get_server_info();
        } else {
            throw new Exception( @mysql_error( $link ) );
        }
        return (isset( $v )) ? $v : 'none';

    }

    /*
     * query functions for class class.net.php, class.reportTables.php
     *
     */
    public function getDropTable ($sTableName)
    {
        $sql = 'DROP TABLE IF EXISTS `' . $sTableName . '`';
        return $sql;
    }

    public function getTableDescription ($sTableName)
    {
        $sql = "DESC " . $sTableName;
        return $sql;
    }

    public function getFieldNull ()
    {
        $fieldName = "Null";
        return $fieldName;
    }

    public function getValidate ($validate)
    {
        $oValidate = $validate;
        return $oValidate;
    }

    /**
     * Determines whether a table exists
     * It is part of class.reportTables.php
     */
    public function reportTableExist ()
    {
        $bExists = true;
        $oConnection = mysql_connect( DB_HOST, DB_USER, DB_PASS );
        mysql_select_db( DB_NAME );
        $oDataset = mysql_query( 'SELECT COUNT(*) FROM REPORT_TABLE' ) || ($bExists = false);

        return $bExists;
    }

    /**
     * It is part of class.pagedTable.php
     */
    public function getLimitRenderTable ($nCurrentPage, $nRowsPerPage)
    {
        $sql = ' LIMIT ' . (($nCurrentPage - 1) * $nRowsPerPage) . ', ' . $nRowsPerPage;
        return $sql;
    }

    /**
     * Determining the existence of a table
     */
    public function tableExists ($tableName, $database)
    {
        @mysql_select_db( $database );
        $tables = array ();
        $tablesResult = mysql_query( "SHOW TABLES FROM $database;" );
        while ($row = @mysql_fetch_row( $tablesResult )) {
            $tables[] = $row[0];
        }
        if (in_array( $tableName, $tables )) {
            return true;
        }
        return false;
    }

    /*
     *   Determining the existence of a table (Depricated)
     */
    //  function tableExists ($table, $db) {
        //    $tables = mysql_list_tables ($db);
        //    while (list ($temp) = @mysql_fetch_array ($tables)) {
        //        if ($temp == $table) {
        //            return TRUE;
        //        }
        //    }
        //    return FALSE;
        //  }
}

