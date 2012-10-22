<?php
/**
 * class.database_mssql.php
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
    public $iFetchType = MSSQL_ASSOC;

    public function __construct ($sType = DB_ADAPTER, $sServer = DB_HOST, $sUser = DB_USER, $sPass = DB_PASS, $sDataBase = DB_NAME)
    {
        $this->sType = $sType;
        $this->sServer = $sServer;
        $this->sUser = $sUser;
        $this->sPass = $sPass;
        $this->sDataBase = $sDataBase;
        $this->oConnection = @mssql_connect( $sServer, $sUser, $sPass ) || null;
        $this->sQuoteCharacter = ' ';
        $this->nullString = 'NULL';
        $this->sQuoteCharacterBegin = '[';
        $this->sQuoteCharacterEnd = ']';
    }

    public function generateCreateTableSQL ($sTable, $aColumns)
    {
        $sKeys = '';
        $sSQL = 'CREATE TABLE  ' . $this->sQuoteCharacter . $sTable . $this->sQuoteCharacter . '(';

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

    public function generateDropTableSQL ($sTable)
    {
        return 'DROP TABLE ' . $this->sQuoteCharacter . $sTable . $this->sQuoteCharacter . $this->sEndLine;
    }

    public function generateDropColumnSQL ($sTable, $sColumn)
    {
        // SQL Server first should remove the restriction before the Elimination of the field
        $oConstraint = $this->dropFieldConstraint( $sTable, $sColumn );
        $sSQL = 'ALTER TABLE ' . $this->sQuoteCharacter . $sTable . $this->sQuoteCharacter . ' DROP COLUMN ' . $this->sQuoteCharacter . $sColumn . $this->sQuoteCharacter . $this->sEndLine;
        return $sSQL;
    }

    public function generateAddColumnSQL ($sTable, $sColumn, $aParameters)
    {
        if (isset( $aParameters['Type'] ) && isset( $aParameters['Null'] )) {
            $sDefault = "";
            $sType = $aParameters['Type'];
            $sDataType = $aParameters['Type'];
            if (! in_array( $sDataType, array ("TEXT","DATE"
            ) )) {
                $sType = substr( $sType, 0, strpos( $sType, '(' ) );
            }
            switch ($sType) {
                case 'VARCHAR':
                case 'TEXT':
                    $sDefault = " DEFAULT '' ";
                    break;
                case 'DATE':
                    $sDataType = " CHAR(19) ";
                    $sDefault = " DEFAULT '0000-00-00' "; // The date data type to use char (19)
                    break;
                case 'INT':
                case 'FLOAT':
                    $sDataType = $sType;
                    $sDefault = " DEFAULT 0 ";
                    break;
            }
            $sSQL = "ALTER TABLE " . $this->sQuoteCharacter . $sTable . $this->sQuoteCharacter . " ADD " . $this->sQuoteCharacter . $sColumn . $this->sQuoteCharacter . " " . $aParameters['Type'];
            if ($aParameters['Null'] == 'YES') {
                $sSQL .= " NULL";
            } else {
                $sSQL .= " NOT NULL " . $sDefault;

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
            if (isset( $aParameters['Default'] ) && $aParameters['Default'] != '') {
                $sSQL .= " DEFAULT '" . $aParameters['Default'] . "'";
            }
        }
        $sSQL .= $this->sEndLine;
        return $sSQL;
    }

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
            if (trim( $aParameters['Default'] == '' ) && $aParameters['Type'] == 'datetime') {
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
     * Get primary key
     *
     * @param eter string $sTable
     * @return string $sPrimaryKey
     */
    public function getPrimaryKey ($sTable)
    {
        try {
            $sSQL = " SELECT 	c.COLUMN_NAME " . " FROM 	INFORMATION_SCHEMA.TABLE_CONSTRAINTS pk , " . "       INFORMATION_SCHEMA.KEY_COLUMN_USAGE c " . " WHERE 	pk.TABLE_NAME = '" . trim( $sTable ) . "' " . " AND	CONSTRAINT_TYPE = 'PRIMARY KEY' " . " AND	c.TABLE_NAME = pk.TABLE_NAME " . " AND	c.CONSTRAINT_NAME = pk.CONSTRAINT_NAME ";
            $oPrimaryKey = $this->executeQuery( $sSQL );
            $aPrimaryKey = mssql_fetch_array( $oPrimaryKey );
            mssql_free_result( $oPrimaryKey );
            return $aPrimaryKey[0];
        } catch (Exception $oException) {
            throw $oException;
        }
    }

    /**
     * Get Field Constraint
     *
     * @param eter string $sTable
     * @param eter string $sField
     * @return string $sFieldConstraint
     */
    public function getFieldConstraint ($sTable, $sField)
    {
        try {
            $sSQL = " select a.name " . " from sysobjects a " . "   inner join syscolumns b on a.id = b.cdefault " . " where a.xtype = 'D' " . " and a.parent_obj = (select id from sysobjects where xtype = 'U' and name = '" . trim( $sTable ) . "') " . " and b.name = '" . trim( $sField ) . "' ";

            $oFieldConstraint = $this->executeQuery( $sSQL );
            $aFieldConstraint = mssql_fetch_array( $oFieldConstraint );
            mssql_free_result( $oFieldConstraint );
            return $aFieldConstraint[0];
        } catch (Exception $oException) {
            throw $oException;
        }
    }

    /**
     * drop Field Constraint
     *
     * @param eter string $sTable
     * @param eter string $sField
     * @return object $oFieldConstraint
     */
    public function dropFieldConstraint ($sTable, $sField)
    {
        try {
            $sConstraint = $this->getFieldConstraint( $sTable, $sField );
            $sSQL = "ALTER TABLE " . $sTable . " DROP CONSTRAINT " . $sConstraint . $this->sEndLine;
            $oFieldConstraint = $this->executeQuery( $sSQL );
            return $oFieldConstraint;
        } catch (Exception $oException) {
            throw $oException;
        }
    }

    public function generateDropPrimaryKeysSQL ($sTable)
    {
        try {
            if ($sTable == '') {
                throw new Exception( 'The table name cannot be empty!' );
            }
            $sPrimayKey = $this->getPrimaryKey( $sTable );

            return ' ALTER TABLE ' . $sTable . ' DROP CONSTRAINT ' . $sPrimayKey . $this->sEndLine;
        } catch (Exception $oException) {
            throw $oException;
        }
    }

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

    public function generateShowTablesSQL ()
    {
        return 'SHOW TABLES' . $this->sEndLine;
    }

    public function generateShowTablesLikeSQL ($sTable)
    {
        return "SHOW TABLES LIKE '" . $sTable . "'" . $this->sEndLine;
    }

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

    public function generateTableIndexSQL ($sTable)
    {
        return 'SHOW INDEX FROM ' . $this->sQuoteCharacter . $sTable . $this->sQuoteCharacter . " " . $this->sEndLine;
        //return 'SHOW INDEX FROM ' . $this->sQuoteCharacter . $sTable . $this->sQuoteCharacter . " WHERE Key_name <> 'PRIMARY'" . $this->sEndLine;
    }

    public function isConnected ()
    {
        if (! $this->oConnection) {
            return false;
        }
        return $this->executeQuery( 'USE ' . $this->sDataBase );
    }

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
                $logFile = PATH_DATA . 'log' . PATH_SEP . 'query.log';
                $fp = fopen( $logFile, 'a+' );
                fwrite( $fp, date( "Y-m-d H:i:s" ) . " " . $this->sDataBase . " " . $sQuery . "\n" );
                fclose( $fp );
            }
        } catch (Exception $oException) {
        }
    }

    public function executeQuery ($sQuery)
    {
        $this->logQuery( $sQuery );

        try {
            if ($this->oConnection) {
                @mssql_select_db( $this->sDataBase );

                return @mssql_query( $sQuery );
            } else {
                throw new Exception( 'invalid connection to database ' . $this->sDataBase );
            }
        } catch (Exception $oException) {
            $this->logQuery( $oException->getMessage() );
            throw $oException;
        }
    }

    public function countResults ($oDataset)
    {
        return @mssql_num_rows( $oDataset );
    }

    public function getRegistry ($oDataset)
    {
        return @mssql_fetch_array( $oDataset, $this->iFetchType );
    }

    public function close ()
    {
        @mssql_close( $this->oConnection );
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
                        $values[] = "'" . addslashes( $field['value'] ) . "'";
                        break;
                    case 'int':
                    default:
                        $values[] = addslashes( $field['value'] );
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
                        $fields[] = $this->putQuotes( $field['field'] ) . " = '" . addslashes( $field['value'] ) . "'";
                        break;
                    case 'int':
                    default:
                        $fields[] = $this->putQuotes( $field['field'] ) . " = " . addslashes( $field['value'] );
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
                            $where[] = $this->putQuotes( $field['field'] ) . " = '" . addslashes( $field['value'] ) . "'";
                            break;
                        case 'int':
                        default:
                            $where[] = $this->putQuotes( $field['field'] ) . " = " . addslashes( $field['value'] );
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
        return $this->sQuoteCharacterBegin . $element . $this->sQuoteCharacterEnd;
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
        $sConcat = "";
        for ($i = 0; $i < $nums; $i ++) {
            if (isset( $vars[$i] )) {
                $sConcat .= $vars[$i];
                if (($i + 1) < $nums) {
                    $sConcat .= " + ";
                }
            }
        }
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
     * @author Hector Cortez <hector@gmail.com>
     * date 2010-08-04
     *
     * @return string $sCompare
     */
    public function getCaseWhen ($compareValue, $trueResult, $falseResult)
    {
        $sCompare = " CASE WHEN " . $compareValue . " THEN " . $trueResult . " ELSE " . $falseResult . " END ";
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
        $sql = "IF NOT EXISTS (SELECT * FROM sysobjects WHERE name='OBJECT_PERMISSION' AND xtype='U')
          CREATE TABLE OBJECT_PERMISSION (
          OP_UID varchar(32) NOT NULL,
          PRO_UID varchar(32) NOT NULL,
          TAS_UID varchar(32) NOT NULL,
          USR_UID varchar(32) NOT NULL,
          OP_USER_RELATION int NOT NULL default '1',
          OP_TASK_SOURCE varchar(32) NOT NULL,
          OP_PARTICIPATE int NOT NULL default '1',
          OP_OBJ_TYPE varchar(15) NOT NULL default 'ANY',
          OP_OBJ_UID varchar(32) NOT NULL,
          OP_ACTION varchar(10) NOT NULL default 'VIEW',
          CONSTRAINT PK_PRO_UID PRIMARY KEY CLUSTERED (PRO_UID, TAS_UID,USR_UID, OP_TASK_SOURCE, OP_OBJ_UID)  )";
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

        $sqlConcat = " U.USR_LASTNAME + ' ' + USR_FIRSTNAME AS [USER] ";
        $sqlGroupBy = " U.USR_LASTNAME + ' ' + USR_FIRSTNAME ";

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
        $sqlConcat = " U.USR_LASTNAME + ' ' + USR_FIRSTNAME AS [USER] ";
        $sqlGroupBy = " U.USR_LASTNAME + ' ' + USR_FIRSTNAME ";

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

        $sqlConcat = " U.USR_LASTNAME + ' ' + USR_FIRSTNAME AS [USER] ";
        $sqlGroupBy = " U.USR_LASTNAME + ' ' + USR_FIRSTNAME ";

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

        $sqlConcat = " U.USR_LASTNAME + ' ' + USR_FIRSTNAME AS [USER] ";
        $sqlGroupBy = " U.USR_LASTNAME + ' ' + USR_FIRSTNAME ";

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

        if (strlen( trim( $dbIP ) ) <= 0) {
            $dbIP = DB_HOST;
        }
        if ($link = @mssql_connect( $dbIP, $dbUser, $dbPasswd )) {
            @mssql_select_db( DB_NAME, $link );
            $oResult = @mssql_query( "select substring(@@version, 21, 6) + ' (' + CAST(SERVERPROPERTY ('productlevel') as varchar(10)) + ') ' + CAST(SERVERPROPERTY('productversion') AS VARCHAR(15)) + ' ' + CAST(SERVERPROPERTY ('edition') AS VARCHAR(25)) as version; ", $link );
            $aResult = @mssql_fetch_array( $oResult );
            @mssql_free_result( $oResult );
            $v = $aResult[0];
        } else {
            throw new Exception( @mssql_error( $link ) );
        }
        return (isset( $v )) ? $v : 'none';

    }

    /*
     * query functions for class class.net.php
     *
     */
    public function getDropTable ($sTableName)
    {
        $sql = "IF NOT EXISTS (SELECT * FROM sysobjects WHERE name='" . $sTableName . "' AND xtype='U') " . "DROP TABLE ['" . $sTableName . "']";
        return $sql;
    }

    public function getTableDescription ($sTableName)
    {
        $sql = " select column_name as Field,
              data_type + ' ' +
              (case data_type
              when 'char'
                then '(' + convert (varchar(6),character_maximum_length) + ')'
              when 'varchar'
                then '(' + convert (varchar(6),character_maximum_length) + ')'
              when 'nchar'
                then '(' + convert (varchar(6),character_maximum_length) + ')'
              when 'nvarchar'
                then '(' + convert (varchar(6),character_maximum_length) + ')'
              else ' ' end) as Type,
              (case is_nullable
              when 'No' then 'NO' else 'YES' END) AS AsNull,
              COLUMN_DEFAULT as [Default]
            FROM information_schema.columns
            WHERE table_name = '" . trim( $sTableName ) . "'" . " Order by Ordinal_Position asc ";
        return $sql;
    }

    public function getFieldNull ()
    {
        $fieldName = "AsNull";
        return $fieldName;
    }

    public function getValidate ($validate)
    {
        $oValidate = true;
        return $oValidate;
    }

    /**
     * Determines whether a table exists
     * It is part of class.reportTables.php
     */
    public function reportTableExist ()
    {
        $bExists = true;
        $oConnection = mssql_connect( DB_HOST, DB_USER, DB_PASS );
        mssql_select_db( DB_NAME );
        $oDataset = mssql_query( 'SELECT COUNT(*) FROM REPORT_TABLE' ) || ($bExists = false);

        return $bExists;
    }

    /**
     * It is part of class.pagedTable.php
     */
    public function getLimitRenderTable ($nCurrentPage, $nRowsPerPage)
    {
        $sql = "";
        return $sql;
    }

    /**
     * Determining the existence of a table
     */
    public function tableExists ($table, $db)
    {
        $sql = "SELECT * FROM sysobjects WHERE name='" . $table . "' AND type='u'";
        $bExists = true;
        $oConnection = mssql_connect( DB_HOST, DB_USER, DB_PASS );
        mssql_select_db( DB_NAME );
        $oDataset = mssql_query( $sql ) || ($bExists = false);
        return $bExists;
    }
}

