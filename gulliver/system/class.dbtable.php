<?php

/**
 * class.dbtable.php
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
 * DBTable class definition
 * This class provides acces to a generalized table
 * it assumes that the dbconnection object is already initialized, the table name, as well as the primary keys
 * for the table should be also provided in order to provide the class a way to generate an UPDATE query properly.
 *
 * @package gulliver.system
 * @author Fernando Ontiveros Lira <fernando@colosa.com>
 * @copyright (C) 2002 by Colosa Development Team.
 *
 */
class DBTable
{
    var $_dbc;
    var $_dbses;
    var $_dset;
    var $table_name;
    var $table_keys;
    var $Fields = null;
    var $is_new;
    var $errorLevel;
    var $debug = false;

    /**
     * Initiate a database conecction using default values
     *
     * @author Fernando Ontiveros Lira <fernando@colosa.com>
     * @access public
     * @param object $objConnection conecction string
     * @return void
     */
    function dBTable ($objConnection = null, $strTable = "", $arrKeys = array( 'UID' ))
    {
        $this->_dbc = null;
        $this->_dbses = null;
        $this->SetTo( $objConnection, $strTable, $arrKeys );
    }

    /**
     * Initiate a database conecction using default values
     *
     * @author Fernando Ontiveros Lira <fernando@colosa.com>
     * @access public
     * @param object $objDBConnection conecction string
     * @param string $strTable Table name defaultvalue=''
     * @param array $arrKeys table keys defaultvalue=UID
     * @return void
     */
    function setTo ($objDBConnection, $strTable = "", $arrKeys = array( 'UID' ))
    {

        $this->_dbc = $objDBConnection;
        if ($this->_dbc != null && strcasecmp( get_class( $objDBConnection ), 'dbconnection' ) == 0) {
            $this->_dbses = new DBSession( $this->_dbc );
            $this->_dbses->UseDB( DB_NAME );
        } else {
            $dberror = PEAR::raiseError( null, DB_ERROR_OBJECT_NOT_DEFINED, null, 'null', "You tried to call to a DBTable function without create an instance of DBConnection", 'G_Error', true );
            //DBconnection::logError( $dberror );
            return $dberror;
        }
        $this->is_new = true;
        $this->Fields = null;
        $this->table_name = $strTable;
        if (is_array( $arrKeys )) {
            $this->table_keys = $arrKeys;
        } else {
            $this->table_keys = array (0 => $arrKeys
            );
        }
        $this->errorLevel = $this->_dbc->errorLevel;
    }

    /**
     * Loads full description of a referenced table in Fields
     *
     * @author Fernando Ontiveros Lira <fernando@colosa.com>
     * @access public
     * @return void
     */
    function loadEmpty ()
    {
        $stQry = "DESCRIBE `" . $this->table_name . "`";
        $dset = $this->_dbses->execute( $stQry );
        //$dset = new DBRecordSet( $this->_dbses->Result );
        $nlim = $dset->Count();
        $this->Fields = null;
        for ($ncount = 0; $ncount < $nlim; $ncount ++) {
            $data = $dset->Read();
            $fname = $data['Field'];
            $fval = "";
            $ftypearr = explode( $data['Type'], '(' );
            $ftype = $ftypearr[0];

            if ($data['Key'] == 'PRI') {
                if (is_array( $this->table_keys )) {
                    $this->table_keys[count( $this->table_keys ) - 1] = $fname;
                } else {
                    $this->table_keys[0] = $fname;
                }
            }

            switch ($ftype) {
                case 'int':
                case 'smallint':
                case 'tinyint':
                case 'decimal':
                case 'numeric':
                case 'double':
                case 'float':
                    $fval = 0;
                    break;
            }

            $this->Fields[$fname] = $fval;
            $this->Fields[$ncount] = &$this->Fields[$fname];
        }
    }

    /**
     * Return specified field on the referenced table
     *
     * @author Fernando Ontiveros Lira <fernando@colosa.com>
     * @access public
     * @param string $strWhere string which contains conditions
     * @return strint
     */
    function loadWhere ($strWhere)
    {
        $this->Fields = null;

        $stQry = "SELECT * FROM `" . $this->table_name . "`";
        if ($strWhere != "") {
            $stQry .= " WHERE " . $strWhere;
        }
        $this->_dset = $this->_dbses->Execute( $stQry, $this->debug, $this->errorLevel );
        if (DB::isError( $this->_dset )) {
            return $this->_dset;
        }

        if ($this->_dset->Count() > 0) {
            $this->Fields = $this->_dset->Read();
            $this->is_new = false;
        } else {
            $this->Fields = null;
            $this->is_new = true;
        }

        return $this->Fields;
    }

    /**
     * Return all fields on the referenced table
     *
     * @author Fernando Ontiveros Lira <fernando@colosa.com>
     * @access public
     * @param array array of arguments key values
     * @return void
     */
    function load ()
    {
        //    bug::traceRoute();
        $ncount = 0;
        $stWhere = "";
        $arrKeys = func_get_args();
        if (isset( $arrKeys[0] ) && is_array( $arrKeys[0] )) {
            foreach ($this->table_keys as $key => $val) {
                if ($stWhere == "") {
                    $stWhere .= " $val = '" . $arrKeys[0][$val] . "' ";
                } else {
                    $stWhere .= " AND $val = '" . $arrKeys[0][$val] . "' ";
                }
            }
        } else {
            foreach ($arrKeys as $val) {
                if ($stWhere == "") {
                    $stWhere .= $this->table_keys[$ncount] . "='" . $val . "'";
                } else {
                    $stWhere .= " AND " . $this->table_keys[$ncount] . "='" . $val . "'";
                }
                $ncount ++;
            }
        }
        return $this->LoadWhere( $stWhere );
    }

    /**
     * Function nextvalPGSql
     *
     * @author David S. Callizaya S. <davidsantos@colosa.com>
     * @access public
     * @param eter string seq
     * @return string
     */
    function nextvalPGSql ($seq)
    {
        $stQry = " Select NEXTVAL( '$seq' ) ";
        $dset = $this->_dbses->Execute( $stQry );
        $row = $dset->Read();
        if (is_array( $row )) {
            return $row['NEXTVAL'];
        }
        die( "Sequence '$seq' is not exist!!" );
        return - 1;
    }

    /**
     * Insert a new row
     *
     * @author Fernando Ontiveros Lira <fernando@colosa.com>
     * @access public
     * @return boolean
     *
     */
    function insert ()
    {
        $strFields = "";
        $strValues = "";
        if (defined( 'DB_ADAPTER' )) {
            $DBEngine = DB_ADAPTER;
        } else {
            $DBEngine = 'mysql';
        }
        foreach ($this->Fields as $field => $val) {
            $strFields .= $field . ",";
            $iskey = false;
            if (isset( $this->table_keys ) && is_array( $this->table_keys )) {
                $iskey = in_array( $field, $this->table_keys ) && strtoupper( substr( trim( $val ), 0, 7 ) ) == "NEXTVAL";
            }
            $dbcType = isset( $this->_dbc->type ) ? $this->_dbc->type : $DBEngine;
            // Commented by new format of textarea in javascript
            if (! $iskey) {
                $val = "'" . $val . "'";
            }
                ///--  $val = "'" . G::sqlEscape( $val , $dbcType ) . "'";
            $strValues .= $val . ", ";
        }
        $strFields = substr( $strFields, 0, strlen( $strFields ) - 1 );
        $strValues = substr( $strValues, 0, strlen( $strValues ) - 1 );

        $stQry = "INSERT INTO `" . $this->table_name . "` ( " . $strFields . " ) values ( " . $strValues . " ) ";

        $result = $this->_dbses->Execute( $stQry, $this->debug );
        return $result;
    }

    /**
     * Update an existing row
     *
     * @author Fernando Ontiveros Lira <fernando@colosa.com>
     * @access public
     * @return boolean
     */
    function update ()
    {
        $stQry = "";

        $stWhere = '';
        $remainKeys = array ();

        if (defined( 'DB_ADAPTER' )) {
            $DBEngine = DB_ADAPTER;
        } else {
            $DBEngine = 'mysql';
        }

        foreach ($this->table_keys as $k => $v) {
            $remainKeys[$v] = false;
        }
        foreach ($this->Fields as $field => $val) {
            $iskey = false;
            $iskey = in_array( $field, $this->table_keys );
            if ($iskey == false) {
                $stQry .= $field . "='" . $val . "', ";
                // Commented by new format of textarea in javascript
                ///-- $stQry .= $field . "='" . G::sqlEscape ( $val, isset( $this->_dbc->type) ? $this->_dbc->type : $DBEngine ) . "', ";
            } else {
                if ($stWhere == "") {
                    $stWhere .= $field . "='" . G::sqlEscape( $val, isset( $this->_dbc->type ) ? $this->_dbc->type : $DBEngine ) . "'";
                } else {
                    $stWhere .= " AND " . $field . "='" . G::sqlEscape( $val, isset( $this->_dbc->type ) ? $this->_dbc->type : $DBEngine ) . "'";
                }
                $remainKeys[$field] = true;
            }
        }
        foreach ($remainKeys as $field => $bool)
            if ($bool == false) {
                if ($stWhere != "") {
                    $stWhere = " AND ";
                }
                $stWhere .= $field . "= ''";
                $remainKeys[$field] = true;
            }

        $stQry = trim( $stQry );
        $stQry = substr( $stQry, 0, strlen( $stQry ) - 1 ); //to remove the last comma ,
        if (! $stQry) {
            return;
        }
        $stQry = "UPDATE `" . $this->table_name . "` SET  " . $stQry;
        $stWhere = trim( $stWhere );
        if ($stWhere != "") {
            $stQry .= " WHERE " . $stWhere;
        }
        $result = false;

        $result = $this->_dbses->execute( $stQry, $this->debug, $this->errorLevel );
        $this->is_new = false;
        return $result;
    }

    /**
     * Save a register in a table
     *
     * depending of value of "is_new" inserts or update is do it
     *
     * @author Fernando Ontiveros Lira <fernando@colosa.com>
     * @access public
     * @return boolean
     */
    function save ()
    {
        if ($this->is_new == true) {
            return $this->Insert();
        } else {
            return $this->Update();
        }
    }

    /**
     * Delete an existing row
     *
     * @author Fernando Ontiveros Lira <fernando@colosa.com>
     * @access public
     * @return boolean
     */
    function delete ()
    {
        $stQry = "delete from `" . $this->table_name . "` ";

        $stWhere = '';
        $remainKeys = array ();
        if (defined( 'DB_ADAPTER' )) {
            $DBEngine = DB_ADAPTER;
        } else {
            $DBEngine = 'mysql';
        }
        foreach ($this->table_keys as $k => $v) {
            $remainKeys[$v] = false;
        }
        if (is_array( $this->Fields )) {
            foreach ($this->Fields as $field => $val) {
                $iskey = false;
                $iskey = in_array( $field, $this->table_keys );
                if ($iskey == true) {
                    if ($stWhere == "") {
                        $stWhere .= $field . "='" . G::sqlEscape( $val, isset( $this->_dbc->type ) ? $this->_dbc->type : $DBEngine ) . "'";
                    } else {
                        $stWhere .= " AND " . $field . "='" . G::sqlEscape( $val, isset( $this->_dbc->type ) ? $this->_dbc->type : $DBEngine ) . "'";
                    }
                    $remainKeys[$field] = true;
                }
            }
        }
        foreach ($remainKeys as $field => $bool)
            if ($bool == false) {
                if ($stWhere != "") {
                    $stWhere .= " AND ";
                }
                $stWhere .= $field . "= ''";
                $remainKeys[$field] = true;
            }

        $stQry = trim( $stQry );
        $stWhere = trim( $stWhere );
        if ($stWhere == '') {
            $dberror = PEAR::raiseError( null, G_ERROR_WARNING_MESSAGE, null, 'null', "You tried to call delete method without WHERE clause, if you want to delete all records use dbsession", 'G_Error', true );
            DBconnection::logError( $dberror, $this->errorLevel );
            return $dberror;
        }
        $stQry .= " WHERE " . $stWhere;

        $result = $this->_dbses->execute( $stQry, $this->debug, $this->errorLevel );
        $this->is_new = false;
        return $result;
    }

    /**
     * Move to next record in a recordset
     *
     * Move to next record in a recordset, this is useful where the load method have a recordset with many rows
     *
     * @author Fernando Ontiveros Lira <fernando@colosa.com>
     * @access public
     * @return boolean
     */
    function next ()
    {
        $this->Fields = $this->_dset->read();
    }
}

