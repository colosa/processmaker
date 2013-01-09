<?php

/**
 * class.case.php
 *
 * @package workflow.engine.classes
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
 *
 */
require_once 'classes/model/AdditionalTables.php';

/**
 * PmTable Class
 * New class to handle pmTable in native form invoking to Phing & Propel
 *
 * @author Erik Amaru Ortiz <erik@colosa.com>
 */
class PmTable
{

    private $dom = null;
    private $schemaFile = '';
    private $tableName;
    private $columns;
    private $baseDir = '';
    private $targetDir = '';
    private $configDir = '';
    private $dataDir = '';
    private $classesDir = '';
    private $className = '';
    private $dataSource = '';
    private $rootNode;
    private $dbConfig;
    private $db;
    private $alterTable = true;
    private $keepData = false;

    public function __construct ($tableName = null)
    {
        if (isset( $tableName )) {
            $this->tableName = $tableName;
            $this->className = $this->toCamelCase( $tableName );
        }

        $this->dbConfig = new StdClass();
    }

    /**
     * Set columns to pmTable
     *
     * @param array $columns contains a array of abjects
     * array(StdClass->field_name, field_type, field_size, field_null, field_key, field_autoincrement,...)
     */
    public function setColumns ($columns)
    {
        $this->columns = $columns;
    }

    /**
     * Set a data source
     *
     * @param string $dbsUid DBS_UID to relate the pmTable to phisical table
     */
    public function setDataSource ($dbsUid)
    {
        $this->dataSource = self::resolveDbSource( $dbsUid );

        switch ($this->dataSource) {
            case 'workflow':
                $this->dbConfig->adapter = DB_ADAPTER;
                $this->dbConfig->host = DB_HOST;
                $this->dbConfig->name = DB_NAME;
                $this->dbConfig->user = DB_USER;
                $this->dbConfig->passwd = DB_PASS;
                $this->dbConfig->port = 3306; //FIXME update this when port for workflow dsn will be available
                break;
            case 'rp':
                $this->dbConfig->adapter = DB_ADAPTER;
                $this->dbConfig->host = DB_REPORT_HOST;
                $this->dbConfig->name = DB_REPORT_NAME;
                $this->dbConfig->user = DB_REPORT_USER;
                $this->dbConfig->passwd = DB_REPORT_PASS;
                $this->dbConfig->port = 3306; //FIXME update this when port for rp dsn will be available
                break;
            default:
                require_once 'classes/model/DbSource.php';
                $oDBSource = new DbSource();
                $proUid = $oDBSource->getValProUid( $this->dataSource );
                $dbSource = $oDBSource->load( $this->dataSource, $proUid );

                if (is_object( $dbSource )) {
                    $this->dbConfig->adapter = $dbSource->getDbsType();
                    $this->dbConfig->host = $dbSource->getDbsServer();
                    $this->dbConfig->name = $dbSource->getDbsDatabaseName();
                    $this->dbConfig->user = $dbSource->getDbsUsername();
                    $this->dbConfig->passwd = $dbSource->getDbsPassword();
                    $this->dbConfig->port = $dbSource->getDbsPort();
                }
                if (is_array( $dbSource )) {
                    $this->dbConfig->adapter = $dbSource['DBS_TYPE'];
                    $this->dbConfig->host = $dbSource['DBS_SERVER'];
                    $this->dbConfig->name = $dbSource['DBS_DATABASE_NAME'];
                    $this->dbConfig->user = $dbSource['DBS_USERNAME'];
                    $this->dbConfig->passwd = $dbSource['DBS_PASSWORD'];
                    $this->dbConfig->port = $dbSource['DBS_PORT'];
                } else {
                    throw new Exception( "Db source with id $dbsUid does not exist!" );
                }
        }
    }

    /**
     * Backward compatibility function
     * Resolve a propel data source
     *
     * @param string $dbsUid corresponding to DBS_UID key
     * @return string contains resolved DBS_UID
     */
    public function resolveDbSource ($dbsUid)
    {
        switch ($dbsUid) {
            case 'workflow':
            case 'wf':
            case '0':
            case '':
            case null:
                $dbsUid = 'workflow';
                break;
            case 'rp':
            case 'report':
                $dbsUid = 'rp';
                break;
        }

        return $dbsUid;
    }

    public function getDataSource ()
    {
        return $this->dataSource;
    }

    /**
     * get Data base config object
     *
     * @return object containing dbConfig var
     */
    public function getDbConfig ()
    {
        return $this->dbConfig;
    }

    public function setAlterTable ($value)
    {
        $this->alterTable = $value;
    }

    public function setKeepData($value)
    {
        $this->keepData = $value;
    }

    /**
     * Build the pmTable with all dependencies
     */
    public function build ()
    {
        $this->prepare();
        $this->preparePropelIniFile();
        $this->buildSchema();

        if ($this->alterTable) {
            $this->phingbuildModel();
            $this->phingbuildSql();
            $this->upgradeDatabase();
        }
    }

    public function buildModelFor ($dbsUid, $tablesList)
    {
        $this->setDataSource( $dbsUid );
        $loadSchema = false;
        $this->prepare( $loadSchema );
        $this->phingbuildModel();
        $this->phingbuildSql();
        //$this->upgradeDatabaseFor($this->dataSource, $tablesList);
    }

    /**
     * Prepare the pmTable env
     */
    public function prepare ($loadSchema = true)
    {
        //prevent execute prepare() twice or more
        if (is_object( $this->dom )) {
            return true;
        }

        $this->schemaFilename = 'schema.xml';
        $this->baseDir = PATH_DB . SYS_SYS . PATH_SEP;
        $this->targetDir = $this->baseDir . 'pmt-propel' . PATH_SEP . $this->dataSource . PATH_SEP;
        $this->configDir = $this->targetDir . 'config' . PATH_SEP;
        $this->dataDir = $this->targetDir . 'data' . PATH_SEP;
        $this->classesDir = $this->baseDir . 'classes' . PATH_SEP;

        // G::mk_dir create the requested dir and the parents directories if not exists
        G::mk_dir( $this->configDir );
        G::mk_dir( $this->dataDir );

        if ($loadSchema) {
            $this->loadSchema();
        }
    }

    public function loadSchema ()
    {
        $this->dom = new DOMDocument( '1.0', 'utf-8' );
        $this->dom->preserveWhiteSpace = false;
        $this->dom->formatOutput = true;

        if (file_exists( $this->configDir . $this->schemaFilename )) {
            if (@$this->dom->load( $this->configDir . $this->schemaFilename ) !== true) {
                throw new Exception( 'Error: ' . $this->schemaFilename . ' is a invalid xml file!' );
            }
            $this->rootNode = $this->dom->firstChild;
        } else {
            $this->rootNode = $this->dom->createElement( 'database' );
            $this->rootNode->setAttribute( 'name', $this->dataSource );
            $this->dom->appendChild( $this->rootNode );
        }
    }

    /**
     * Build the xml schema for propel
     */
    public function buildSchema ()
    {
        $tableNode = $this->dom->createElement( 'table' );
        $tableNode->setAttribute( 'name', $this->tableName );

        if ($this->hasAutoIncrementPKey()) {
            $tableNode->setAttribute( 'idMethod', 'native' );
        }

        // specifying collation
        switch ($this->dbConfig->adapter) {
            case 'mysql':
                $vendorNode = $this->dom->createElement( 'vendor' );
                $vendorNode->setAttribute( 'type', $this->dbConfig->adapter );
                $parameterNode = $this->dom->createElement( 'parameter' );
                $parameterNode->setAttribute( 'name', 'Collation' );
                $parameterNode->setAttribute( 'value', 'utf8_general_ci' );
                $vendorNode->appendChild( $parameterNode );
                $tableNode->appendChild( $vendorNode );
                break;
        }

        foreach ($this->columns as $column) {

            // create the column node
            $columnNode = $this->dom->createElement( 'column' );
            // setting column node attributes
            $columnNode->setAttribute( 'name', $column->field_name );
            $columnNode->setAttribute( 'type', $column->field_type );

            if ($column->field_size != '' && $column->field_size != 0) {
                $columnNode->setAttribute( 'size', $column->field_size );
            }

            $columnNode->setAttribute( 'required', ($column->field_null ? 'false' : 'true') );

            // only define the primaryKey attribute if it is defined
            if ($column->field_key) {
                $columnNode->setAttribute( 'primaryKey', "true" );
            }

            // only define the autoIncrement attribute if it is defined
            if ($column->field_autoincrement) {
                $columnNode->setAttribute( 'autoIncrement', "true" );
            }
            $tableNode->appendChild( $columnNode );
        }

        $xpath = new DOMXPath( $this->dom );
        $xtable = $xpath->query( '/database/table[@name="' . $this->tableName . '"]' );

        if ($xtable->length == 0) {
            //the table definition does not exist, then just append the new node
            $this->rootNode->appendChild( $tableNode );
        } else {
            // the table definition already exist, then replace the node
            $replacedNode = $xtable->item( 0 );
            $this->rootNode->replaceChild( $tableNode, $replacedNode );
        }

        // saving the xml result file
        $this->saveSchema();
    }

    /**
     * Remove the pmTable and all related objects, files and others
     */
    public function remove ()
    {
        $this->prepare();
        $this->removeFromSchema();
        $this->removeModelFiles();
        $this->dropTable();
    }

    /**
     * Remove the target pmTable from schema of propel
     */
    public function removeFromSchema ()
    {
        $xpath = new DOMXPath( $this->dom );
        // locate the node
        $xtable = $xpath->query( '/database/table[@name="' . $this->tableName . '"]' );
        if ($xtable->length == 0) {
            return false;
        }

        $this->rootNode->removeChild( $xtable->item( 0 ) );
        // saving the xml result file
        $this->saveSchema();
    }

    /**
     * Remove the model related classes files
     */
    public function removeModelFiles ()
    {
        @unlink( $this->classesDir . $this->className . '.php' );
        @unlink( $this->classesDir . $this->className . 'Peer.php' );
        @unlink( $this->classesDir . 'map' . PATH_SEP . $this->className . 'MapBuilder.php' );
        @unlink( $this->classesDir . 'om' . PATH_SEP . 'Base' . $this->className . '.php' );
        @unlink( $this->classesDir . 'om' . PATH_SEP . 'Base' . $this->className . 'Peer.php' );
    }

    /**
     * Drop the phisical table of target pmTable or any specified as parameter
     */
    public function dropTable ($tableName = null)
    {
        $tableName = isset( $tableName ) ? $tableName : $this->tableName;
        $con = Propel::getConnection( $this->dataSource );
        $stmt = $con->createStatement();

        if (is_object( $con )) {
            try {
                $stmt->executeQuery( "DROP TABLE {$tableName}" );
            } catch (Exception $e) {
                throw new Exception( "Physical table '$tableName' does not exist!" );
            }
        }
    }

    /**
     * Save the xml schema for propel
     */
    public function saveSchema ()
    {
        $this->dom->save( $this->configDir . $this->schemaFilename );
    }

    /**
     * Prepare and create if not exists the propel ini file
     */
    public function preparePropelIniFile ()
    {
        $adapter = $this->dbConfig->adapter;

        if (file_exists( $this->configDir . "propel.$adapter.ini" )) {
            return true;
        }

        if (! file_exists( PATH_CORE . PATH_SEP . 'config' . PATH_SEP . "propel.$adapter.ini" )) {
            throw new Exception( "Invalid or not supported engine '$adapter'!" );
        }

        @copy( PATH_CORE . PATH_SEP . 'config' . PATH_SEP . "propel.$adapter.ini", $this->configDir . "propel.$adapter.ini" );
    }

    /**
     * Upgrade the phisical database for the target pmTable
     * It executes the schema.sql autogenerated by propel, but just execute the correspondent sentenses
     * for the related table
     * - this function is not executing other sentenses like 'SET FOREIGN_KEY_CHECKS = 0;' for mysql, and others
     */
    public function upgradeDatabase ()
    {
        $con = Propel::getConnection( $this->dataSource );
        $stmt = $con->createStatement();
        $lines = file( $this->dataDir . $this->dbConfig->adapter . PATH_SEP . 'schema.sql' );
        $previous = null;
        $queryStack = array ();
        $aDNS = $con->getDSN();
        $dbEngine = $aDNS["phptype"];

        foreach ($lines as $j => $line) {
            switch ($dbEngine) {
                case 'mysql':
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
                    // just execute the drop and create table for target table nad not for others
                    if (stripos( $line, 'CREATE TABLE' ) !== false || stripos( $line, 'DROP TABLE' ) !== false) {
                        $isCreateForCurrentTable = preg_match( '/CREATE\sTABLE\s[\[\'\"\`]{1}' . $this->tableName . '[\]\'\"\`]{1}/i', $line, $match );
                        if ($isCreateForCurrentTable) {
                            $queryStack['create'] = $line;
                        } else {
                            $isDropForCurrentTable = preg_match( '/DROP TABLE.*[\[\'\"\`]{1}' . $this->tableName . '[\]\'\"\`]{1}/i', $line, $match );
                            if ($isDropForCurrentTable) {
                                $queryStack['drop'] = $line;
                            }
                        }
                    }
                    break;
                case 'mssql':
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

                    if (strpos( $line, $this->tableName ) == false) {
                        continue;
                    }

                    $auxCreate = explode( 'CREATE', $line );
                    $auxDrop = explode( 'IF EXISTS', $auxCreate['0'] );

                    $queryStack['drop'] = 'IF EXISTS' . $auxDrop['1'];
                    $queryStack['create'] = 'CREATE' . $auxCreate['1'];

                    break;
                case 'oracle':
                    $line = trim( $line ); // Remove comments from the script
                    if (empty( $line )) {
                        continue;
                    }
                    switch (true) {
                        case preg_match( "/^CREATE TABLE\s/i", $line ):
                            if (strpos( $line, $this->tableName ) == true) {
                                $inCreate = true;
                                $lineCreate .= $line . ' ';
                            }
                            break;
                        case preg_match( "/ALTER TABLE\s/i", $line ):
                            if (strpos( $line, $this->tableName ) == true) {
                                $inAlter = true;
                                $lineAlter .= $line . ' ';
                            }
                            break;
                        case preg_match( "/^DROP TABLE\s/i", $line ):
                            if (strpos( $line, $this->tableName ) == true) {
                                $inDrop = true;
                                $lineDrop .= $line . ' ';
                                if (strrpos( $line, ";" ) > 0) {
                                    $queryStack['drop'] = $lineDrop;
                                    $inDrop = false;
                                }
                            }
                            break;
                        default:
                            if ($inCreate) {
                                $lineCreate .= $line . ' ';
                                if (strrpos( $line, ";" ) > 0) {
                                    $queryStack['create'] = $lineCreate;
                                    $inCreate = false;
                                }
                            }
                            if ($inAlter) {
                                $lineAlter .= $line . ' ';
                                if (strrpos( $line, ";" ) > 0) {
                                    $queryStack['alter'] = $lineAlter;
                                    $inAlter = false;
                                }
                            }
                            if ($inDrop) {
                                $lineDrop .= $line . ' ';
                                if (strrpos( $line, ";" ) > 0) {
                                    $queryStack['drop'] = $lineDrop;
                                    $inDrop = false;
                                }
                            }
                    }
                    break;
            }
        }

        $table = $this->tableName;
        $tableBackup = $this->tableName . "_BAK";

        $sqlTableBackup = null;
        $swTableBackup = 0;

        switch ($dbEngine) {
            case "mysql":
                $sqlTableBackup = "CREATE TABLE $tableBackup SELECT * FROM $table";
                break;
            case "mssql":
                $sqlTableBackup = "SELECT * INTO $tableBackup FROM $table";
                break;
            case "oracle":
                $sqlTableBackup = "CREATE TABLE $tableBackup AS SELECT * FROM $table";
                break;

        }

        if ($dbEngine == 'oracle') {
            $queryStack['drop'] = substr( $queryStack['drop'], 0, strrpos( $queryStack['drop'], ";" ) );
            $queryStack['create'] = substr( $queryStack['create'], 0, strrpos( $queryStack['create'], ";" ) );
            $queryStack['alter'] = substr( $queryStack['alter'], 0, strrpos( $queryStack['alter'], ";" ) );
            $queryIfExistTable = "SELECT TABLE_NAME FROM USER_TABLES WHERE TABLE_NAME = '" . $this->tableName . "'";

            $rs = $stmt->executeQuery( $queryIfExistTable );

            if ($rs->next()) {
                if ($this->keepData && $sqlTableBackup != null) {
                    //Delete backup if exists
                    $rs = $stmt->executeQuery(str_replace($table, $tableBackup, $queryStack["drop"]));

                    //Create backup
                    $rs = $stmt->executeQuery($sqlTableBackup, ResultSet::FETCHMODE_ASSOC);
                    $swTableBackup = 1;
                }

                $stmt->executeQuery( $queryStack['drop'] );
            }

            $stmt->executeQuery( $queryStack['create'] );
            $stmt->executeQuery( $queryStack['alter'] );
        } else {
            if (isset( $queryStack['create'] )) {
                // first at all we need to verify if we have a valid schema defined,
                // so we verify that creating a dummy table
                $swapQuery = str_replace( $this->tableName, $this->tableName . '_TMP', $queryStack['create'] );

                // if there is a problem with user defined table schema executeQuery() will throw a sql exception
                $stmt->executeQuery( $swapQuery );

                // if there was not problem above proceced deleting the dummy table and drop and create the target table
                $stmt->executeQuery( "DROP TABLE {$this->tableName}_TMP" );
                if (! isset( $queryStack['drop'] )) {
                    $queryStack['drop'] = "DROP TABLE {$this->tableName}";
                }
                if (! isset( $queryStack['create'] )) {
                    throw new Exception( 'A problem occurred resolving the schema to update for this table' );
                }

                if ($this->keepData && $sqlTableBackup != null) {
                    //Delete backup if exists
                    $rs = $stmt->executeQuery(str_replace($table, $tableBackup, $queryStack["drop"]));

                    //Create backup
                    $rs = $stmt->executeQuery($sqlTableBackup, ResultSet::FETCHMODE_ASSOC);
                    $swTableBackup = 1;
                }

                $stmt->executeQuery( $queryStack['drop'] );
                $stmt->executeQuery( $queryStack['create'] );
            }
        }

        if ($swTableBackup == 1) {
            $tableFileName = str_replace("_", " ", strtolower($table));
            $tableFileName = str_replace(" ", null, ucwords($tableFileName));

            require_once (PATH_WORKSPACE . "classes" . PATH_SEP . "$tableFileName.php");

            $sql = "SELECT * FROM $tableBackup";
            $rs = $stmt->executeQuery($sql, ResultSet::FETCHMODE_ASSOC);

            while ($rs->next()) {
                $row = $rs->getRow();

                $oTable = new $tableFileName();
                $oTable->fromArray($row, BasePeer::TYPE_FIELDNAME);
                $oTable->save();
            }

            //Delete backup
            $rs = $stmt->executeQuery(str_replace($table, $tableBackup, $queryStack["drop"]));
        }
    }

    public function upgradeDatabaseFor ($dataSource, $tablesList = array())
    {
        $con = Propel::getConnection( $dataSource );
        $stmt = $con->createStatement();
        $lines = file( $this->dataDir . $this->dbConfig->adapter . PATH_SEP . 'schema.sql' );
        $previous = null;
        $errors = '';

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

            // execute
            $isCreate = stripos( $line, 'CREATE TABLE' ) !== false;
            $isDrop = stripos( $line, 'DROP TABLE' ) !== false;

            if ($isCreate || $isDrop) {
                if (preg_match( '/TABLE\s[\'\"\`]+(\w+)[\'\"\`]+/i', $line, $match )) {
                    if (in_array( $match[1], $tablesList )) {
                        //error_log($line);
                        try {
                            $stmt->executeQuery( $line );
                        } catch (Exception $e) {
                            $errors .= $e->getMessage() . "\n";
                            continue;
                        }
                    }
                }
            }
        }

        return $errors;
    }

    /**
     * verify if on the columns list was set a column as primary key
     *
     * @return boolean to affirm if was defined a column as pk.
     */
    public function hasAutoIncrementPKey ()
    {
        foreach ($this->columns as $column) {
            if ($column->field_autoincrement) {
                return true;
            }
        }

        return false;
    }

    /**
     *
     * @return array contains all supported columns types provided by propel
     */
    public function getPropelSupportedColumnTypes ()
    {
        /**
         * http://www.propelorm.org/wiki/Documentation/1.2/Schema
         * [type = "BOOLEAN|TINYINT|SMALLINT|INTEGER|BIGINT|DOUBLE|FLOAT|REAL|DECIMAL|CHAR|{VARCHAR}
         * |LONGVARCHAR|DATE|TIME|TIMESTAMP|BLOB|CLOB"]
         */
        $types = array ();

        $types['BOOLEAN'] = 'BOOLEAN';
        $types['TINYINT'] = 'TINYINT';
        $types['SMALLINT'] = 'SMALLINT';
        $types['INTEGER'] = 'INTEGER';
        $types['BIGINT'] = 'BIGINT';
        $types['DOUBLE'] = 'DOUBLE';
        $types['FLOAT'] = 'FLOAT';
        $types['REAL'] = 'REAL';
        $types['DECIMAL'] = 'DECIMAL';
        $types['CHAR'] = 'CHAR';
        $types['VARCHAR'] = 'VARCHAR';
        $types['LONGVARCHAR'] = 'LONGVARCHAR';
        $types['DATE'] = 'DATE';
        $types['TIME'] = 'TIME';
        $types['DATETIME'] = 'DATETIME';
        //$types['BLOB'] = 'BLOB'; <- disabled
        //$types['CLOB'] = 'CLOB'; <- disabled


        return $types;
    }

    /**
     *
     * @param string $name any string witha name separated by underscore
     * @return string contains a camelcase expresion for $name
     */
    public function toCamelCase ($name)
    {
        $tmp = explode( '_', trim( $name ) );
        foreach ($tmp as $i => $part) {
            $tmp[$i] = ucFirst( strtolower( $part ) );
        }
        return implode( '', $tmp );
    }

    /**
     * Run om task for phing to build all mdoel classes
     */
    public function phingbuildModel ()
    {
        $this->_callPhing( 'om' );
    }

    /**
     * Run sql task for phing to generate the sql schema
     */
    public function phingbuildSql ()
    {
        $this->_callPhing( 'sql' );
    }

    /**
     * call phing to execute a determinated task
     *
     * @param string $taskName [om|sql]
     */
    private function _callPhing ($taskName)
    {
        $options = array ('project.dir' => $this->configDir,'build.properties' => "propel.{$this->dbConfig->adapter}.ini",'propel.targetPackage' => 'classes','propel.output.dir' => $this->targetDir,'propel.php.dir' => $this->baseDir
        );

        self::callPhing( array ($taskName
        ), PATH_THIRDPARTY . 'propel-generator/build.xml', $options, false );
    }

    /**
     *
     * @param string $target - task name to execute
     * @param string $buildFile - build file path
     * @param array $options - array options to override the options on .ini file
     * @param bool $verbose - to show a verbose output
     */
    public static function callPhing ($target, $buildFile = '', $options = array(), $verbose = true)
    {
        G::loadClass( 'pmPhing' );

        $args = array ();
        foreach ($options as $key => $value) {
            $args[] = "-D$key=$value";
        }

        if ($buildFile) {
            $args[] = '-f';
            $args[] = realpath( $buildFile );
        }

        if (! $verbose) {
            $args[] = '-q';
        }

        if (is_array( $target )) {
            $args = array_merge( $args, $target );
        } else {
            $args[] = $target;
        }

        if (DIRECTORY_SEPARATOR != '\\' && (function_exists( 'posix_isatty' ) && @posix_isatty( STDOUT ))) {
            $args[] = '-logger';
            $args[] = 'phing.listener.AnsiColorLogger';
        }

        Phing::startup();
        Phing::setProperty( 'phing.home', getenv( 'PHING_HOME' ) );

        $m = new pmPhing();
        $m->execute( $args );
        $m->runBuild();
    }
}

