<?php
/**
 * class.Sessions.php
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
require_once 'classes/model/Session.php';

/**
 * Sessions - Sessions class
 *
 * @package workflow.engine.ProcessMaker
 * @author Everth S. Berrios Morales
 * @copyright 2008 COLOSA
 */

class Sessions
{

    protected $tmpfile;
    private $sessionId;
    private $globals;

    /**
     * This function is the constructor of the Sessions class
     *
     * @param string $sSessionId
     * @return void
     */
    public function __construct ($sSessionId = NULL)
    {
        $this->sessionId = $sSessionId;
    }

    /**
     * This function gets the user session
     *
     *
     * @name getSessionUser
     *
     * @param string sSessionId
     * @return array
     */
    public function getSessionUser ($sSessionId = NULL)
    {
        try {
            if ($sSessionId != NULL) {
                $this->sessionId = $sSessionId;
            } else if ($this->sessionId == NULL) {
                throw new Exception( 'session id was not set.' );
            }

            $oCriteria = new Criteria( 'workflow' );
            $oCriteria->addSelectColumn( SessionPeer::USR_UID );
            $oCriteria->addSelectColumn( SessionPeer::SES_STATUS );
            $oCriteria->addSelectColumn( SessionPeer::SES_DUE_DATE );
            $oCriteria->add( SessionPeer::SES_UID, $this->sessionId );

            $oDataset = SessionPeer::doSelectRS( $oCriteria );
            $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
            $oDataset->next();
            $aRow = $oDataset->getRow();

            if (! is_array( $aRow )) {
                $this->deleteTmpfile();
            }
            return $aRow;
        } catch (Exception $oError) {
            throw ($oError);
        }
    }

    /**
     * This function checks the user session
     *
     *
     * @name verifySession
     *
     * @param string sSessionId
     * @return array
     */
    public function verifySession ($sSessionId = NULL)
    {
        try {
            if ($sSessionId != NULL) {
                $this->sessionId = $sSessionId;
            } else if ($this->sessionId == NULL) {
                throw new Exception( 'session id was not set.' );
            }

            $date = date( 'Y-m-d H:i:s' );
            $oCriteria = new Criteria( 'workflow' );
            $oCriteria->addSelectColumn( SessionPeer::USR_UID );
            $oCriteria->addSelectColumn( SessionPeer::SES_STATUS );
            $oCriteria->addSelectColumn( SessionPeer::SES_DUE_DATE );
            $oCriteria->add( SessionPeer::SES_UID, $this->sessionId );
            $oCriteria->add( SessionPeer::SES_STATUS, 'ACTIVE' );
            $oCriteria->add( SessionPeer::SES_DUE_DATE, $date, Criteria::GREATER_EQUAL );

            $oDataset = SessionPeer::doSelectRS( $oCriteria );
            $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
            $oDataset->next();
            $aRow = $oDataset->getRow();

            if (! is_array( $aRow )) {
                $this->deleteTmpfile();
            }

            return $aRow;
        } catch (Exception $oError) {
            throw ($oError);
        }
    }

    /**
     * This function registers into globals variables
     *
     *
     * @name registerGlobal
     *
     * @param string $name
     * @param string $value
     * @return void
     */
    public function registerGlobal ($name, $value)
    {
        $this->tmpfile = G::sys_get_temp_dir() . PATH_SEP . "pm-rg-{$this->sessionId}";

        if ($this->sessionId == NULL) {
            throw new Exception( 'session id was not set.' );
        }

        $tmpfile_content = '';
        if (is_file( $this->tmpfile ) && trim( file_get_contents( $this->tmpfile ) ) != '') {
            $tmpfile_content = file_get_contents( $this->tmpfile );
        }

        //getting the global array
        if ($tmpfile_content != '') {
            $this->globals = unserialize( $tmpfile_content );
        } else {
            $this->globals = Array ();
        }

        //registering the new global variable
        $this->globals[$name] = $value;

        //saving the global array
        $tmpfile_content = serialize( $this->globals );
        file_put_contents( $this->tmpfile, $tmpfile_content );

    }

    /**
     * This function gets a global variable
     *
     *
     * @name getGlobal
     *
     * @param string $name
     * @return string
     */
    public function getGlobal ($name)
    {
        $this->tmpfile = G::sys_get_temp_dir() . PATH_SEP . "pm-rg-{$this->sessionId}";

        if ($this->sessionId == NULL) {
            throw new Exception( 'session id was not set.' );
        }

        $tmpfile_content = '';
        if (is_file( $this->tmpfile ) && trim( file_get_contents( $this->tmpfile ) ) != '') {
            $tmpfile_content = file_get_contents( $this->tmpfile );
        }

        //getting the global array
        if ($tmpfile_content != '') {
            $this->globals = unserialize( $tmpfile_content );
        } else {
            $this->globals = Array ();
        }

        //getting the new global variable
        if (isset( $this->globals[$name] )) {
            return $this->globals[$name];
        } else {
            return '';
        }
    }

    /**
     * This function gets all globals variables
     *
     *
     * @name getGlobals
     *
     * @param string $name
     * @return array
     */
    public function getGlobals ()
    {
        $this->tmpfile = G::sys_get_temp_dir() . PATH_SEP . "pm-rg-{$this->sessionId}";

        if ($this->sessionId == NULL) {
            throw new Exception( 'session id was not set.' );
        }

        $tmpfile_content = '';
        if (is_file( $this->tmpfile ) && trim( file_get_contents( $this->tmpfile ) ) != '') {
            $tmpfile_content = file_get_contents( $this->tmpfile );
        }

        //getting the global array
        if ($tmpfile_content != '') {
            $this->globals = unserialize( $tmpfile_content );
        } else {
            $this->globals = Array ();
        }
        return $this->globals;
    }

    /**
     * This function removes a temporal file
     *
     *
     * @name deleteTmpfile
     *
     * param
     * @return void
     */
    private function deleteTmpfile ()
    {
        if ($this->sessionId == NULL) {
            throw new Exception( 'session id was not set.' );
        }
        $this->tmpfile = G::sys_get_temp_dir() . PATH_SEP . "pm-rg-{$this->sessionId}";
        @unlink( $this->tmpfile );
    }

}


















