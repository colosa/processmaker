<?php

/**
 * class.dbrecordset.php
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
 * DBRecordset class definition
 * Provides access to a generalized table it assumes that the dbconnection object is already initialized for the table should be also provided in order to provide
 *
 * @package gulliver.system
 *
 * @author Fernando Ontiveros Lira <fernando@colosa.com>
 * @copyright (C) 2002 by Colosa Development Team.
 */
class DBRecordSet
{
    var $result = null;

    /**
     * Starts connection to Database using default values
     *
     * @author Fernando Ontiveros Lira <fernando@colosa.com>
     * @access public
     * @param string $intResult Database recordset default value = false
     * @return void
     */
    function DBRecordSet ($intResult = null)
    {
        $this->SetTo( $intResult );
    }

    /**
     * Set conecction to Database
     *
     * @author Fernando Ontiveros Lira <fernando@colosa.com>
     * @access public
     * @param string $intResult connection string default value = false
     * @return void
     */
    function SetTo ($intResult = null)
    {
        if ($intResult === null) {
            $dberror = PEAR::raiseError( null, DB_ERROR_OBJECT_NOT_DEFINED, null, 'null', "You tried to call to a DBRecordset with an invalid result recordset.", 'G_Error', true );
            DBconnection::logError( $dberror );
        }
        if ($intResult) {
            $this->result = $intResult;
        }
    }

    /**
     * Function Free
     *
     * @author David S. Callizaya S. <davidsantos@colosa.com>
     * @access public
     * @return string
     */
    function Free ()
    {
        if ($this->result === null) {
            $dberror = PEAR::raiseError( null, DB_ERROR_OBJECT_NOT_DEFINED, null, 'null', "You tried to call to a DBRecordset with an invalid result recordset.", 'G_Error', true );
            DBconnection::logError( $dberror );
        }
        $this->result->free();
        return;
    }

    /**
     * Function Count
     *
     * @author David S. Callizaya S. <davidsantos@colosa.com>
     * @access public
     * @return string
     */
    function Count ()
    {
        if ($this->result === null) {
            $dberror = PEAR::raiseError( null, DB_ERROR_OBJECT_NOT_DEFINED, null, 'null', "You tried to call to a DBRecordset with an invalid result recordset.", 'G_Error', true );
            DBconnection::logError( $dberror );
        }
        return $this->result->numRows();
    }

    /**
     * Function Read
     *
     * @author David S. Callizaya S. <davidsantos@colosa.com>
     * @access public
     * @return string
     */
    function Read ()
    {
        if ($this->result === null) {
            $dberror = PEAR::raiseError( null, DB_ERROR_OBJECT_NOT_DEFINED, null, 'null', "You tried to call to a DBRecordset with an invalid result recordset.", 'G_Error', true );
            DBconnection::logError( $dberror );
        }
        $res = $this->result->fetchRow( DB_FETCHMODE_ASSOC );
        //for Pgsql databases,
        //if ( PEAR_DATABASE == "pgsql" && is_array ( $res ) ) { $res = array_change_key_case( $res, CASE_UPPER);  }


        /* Comment Code: This block is not required now because
         *  of the the use of the G::sqlEscape() instead of addslashes
         *  funcion over each  field in DBTable.
         * @author David Callizaya
         */
        /*if (is_array ($res) )
        foreach ($res as $key => $val)
        $res[$key] = stripslashes ($val);  remove the slashes*/

        return $res;
    }

    /**
     * Function ReadAbsolute
     *
     * @author David S. Callizaya S. <davidsantos@colosa.com>
     * @access public
     * @return string
     */
    function ReadAbsolute ()
    {
        $res = $this->result->fetchRow( DB_FETCHMODE_ORDERED );
        //for Pgsql databases,
        //if ( PEAR_DATABASE == "pgsql" && is_array ( $res ) ) { $res = array_change_key_case( $res, CASE_UPPER);    }
        return $res;
    }
}

