<?php

/**
 * class.dbsession.php
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
 * DBSession class definition
 * It is useful to stablish a database connection using an specific database
 *
 * @package gulliver.system
 * @author Fernando Ontiveros Lira <fernando@colosa.com>
 * @copyright (C) 2002 by Colosa Development Team.
 *
 */
class DBSession
{
    var $dbc = null;
    var $dbname = '';
    var $result = false;

    /**
     * Starts a session using a connection with an specific database
     *
     * @author Fernando Ontiveros Lira <fernando@colosa.com>
     *
     * @access public
     * @param object $objConnection
     * @param string $strDBName
     * @return void
     *
     */
    function DBSession ($objConnection = null, $strDBName = '')
    {
        if ($strDBName != '') {
            $strDBName = $objConnection->db->_db;
        }
        $this->setTo( $objConnection, $strDBName );
    }

    /**
     * It's like a constructor
     *
     * @author Fernando Ontiveros Lira <fernando@colosa.com>
     *
     * @access public
     * @param object $objConnection
     * @param string $strDBName
     * @return void
     *
     */
    function setTo ($objConnection = null, $strDBName = DB_NAME)
    {
        if ($objConnection != null) {
            $this->Free();
            $this->dbc = $objConnection;
            $this->dbname = $strDBName;

            //enable utf8 in mysql databases
            if ($this->dbc->db->phptype == 'mysql') { //utf-8
                $this->dbc->db->query( "SET NAMES 'utf8'; " );
            }
        }
    }

    /**
     * UseDB stablish a database for the connection
     *
     * @author Fernando Ontiveros Lira <fernando@colosa.com>
     *
     * @access public
     * @param string $strDBName
     * @return void
     *
     */
    function UseDB ($strDBName = DB_NAME)
    {
        $this->dbname = $strDBName;
    }

    /**
     * Function Execute, to execute a query and send back the recordset.
     *
     * @access public
     * @param eter string strQuery
     * @param eter string debug
     * @param eter string error
     * @return string
     */
    function Execute ($strQuery = '', $debug = false, $errorLevel = null)
    {
        //BUG::traceRoute();
        if ($this->dbc == null) {
            $dberror = PEAR::raiseError( null, DB_ERROR_OBJECT_NOT_DEFINED, null, 'null', 'You have tried to call a DBSession function without create an instance of DBConnection', 'G_Error', true );
            DBconnection::logError( $dberror, $errorLevel );
            return $dberror;
        }
        ;

        if ($errorLevel === null) {
            $errorLevel = $this->dbc->errorLevel;
        }
        $this->Free( true );

        if ($debug) {
            print ($strQuery . "<br>\n") ;
        }
        $this->result = $this->dbc->db->query( $strQuery );
        if (DB::isError( $this->result )) {
            $this->dbc->logError( $this->result, $errorLevel );
            return $this->result;
        }
        $dset = new DBRecordSet( $this->result );
        return $dset;
    }

    /**
     * Function Query, just to execute the query.
     *
     * @access public
     * @param eter string strQuery
     * @param eter string debug
     * @param eter string error
     * @return string
     */
    /*    function deprecated... by Onti, 30th july 2007
    function Query( $strQuery = '', $debug = false, $error = '' )
    {
        if ( $error == '' && defined('ERROR_STATE') ) {
            $error = ERROR_STATE;
        }

        if ( !defined('ERROR_STATE') ) $error = 4;

        if( $debug ) {
            print( $strQuery . "<br>\n" );
        }

        $this->Free( true );

        $this->result = $this->dbc->db->query ( $strQuery );

        if (DB::isError ($this->result)) {
            $this->dbc->ShowLogError( $this->result->getMessage() , $this->result->userinfo, $error);
            die;
        }
        return;
    }
    */

    /**
     * Function Free
     *
     * @access public
     * @param eter string debug
     * @return string
     */
    function Free ($debug = false)
    {
        if (is_resource( $this->result )) {
            $this->result->Free();
        }
        return;
    }
}
