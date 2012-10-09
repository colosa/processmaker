<?php

/**
 * class.serverConfiguration.php
 *
 * @package workflow.engine.ProcessMaker
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
 */

/**
 * ServerConfiguration - serverConf class
 *
 * @author Hugo Loza
 * @copyright 2010 COLOSA
 * @license GNU Affero General Public License
 * @package workflow.engine.ProcessMaker
 */
class serverConf
{
    private $_aProperties = array ();
    private $_aHeartbeatConfig = array ();
    private $_aWSapces = array ();
    private $aWSinfo = array ();
    private $pluginsA = array ();
    private $errors = array ();
    private static $instance = NULL;
    private $haveSetupData = false;
    private $beatType = 'starting';
    private $ip;
    private $index = 0;
    private $os;
    private $webserver;
    private $host;
    private $php;
    private $mysql;
    private $pmVersion;
    private $pmProduct = 'PMCE';
    private $nextBeatDate;
    public $logins;
    private $lanDirection;
    private $lanLanguage;
    public $workspaces = array ();
    public $rtlLang = array ('ar','iw','fa'
    );
    public $filePath = '';

    public function __construct ()
    {
        if (defined( 'PATH_DATA' )) {
            $this->filePath = PATH_DATA . 'srvConf.singleton';
        }
    }

    /**
     * This function is instancing to this object
     * param
     *
     * @return object
     */
    function &getSingleton ()
    {
        if (self::$instance == NULL) {
            self::$instance = new serverConf();
            if ((file_exists( self::$instance->filePath )) && (filesize( self::$instance->filePath ) > 0)) {
                self::$instance->unSerializeInstance( file_get_contents( self::$instance->filePath ) );
            }
        }
        return self::$instance;
    }

    /**
     * This function generates a storable representation of this obejct
     * param
     *
     * @return void
     */
    function serializeInstance ()
    {
        return serialize( self::$instance );
    }

    /**
     * This function takes a single serialized object and converts it back a object
     *
     * @param string $serialized
     * @return void
     */
    function unSerializeInstance ($serialized)
    {
        if (self::$instance == NULL) {
            self::$instance = new serverConf();
        }

        if ($instance = @unserialize( $serialized )) {
            self::$instance = $instance;
        }
    }

    /**
     * This will save the object in the specified file (defined as a property of this class)
     * param
     *
     * @return void
     */

    function saveSingleton ()
    {
        if (defined( 'PATH_DATA' )) {
            $this->filePath = PATH_DATA . 'srvConf.singleton';
            $size = file_put_contents( $this->filePath, $this->serializeInstance() );
        }
    }

    /**
     * With this is possible to save a property that will be saved in the properties
     * array of this class.
     *
     * @param string $propertyName
     * @param string $propertyValue
     */
    function setProperty ($propertyName, $propertyValue)
    {
        $this->_aProperties[$propertyName] = $propertyValue;
        $this->saveSingleton();
    }

    /**
     * To unset a defined property.
     * If it doesn't exist then it does nothing.
     *
     * @param string $propertyName
     * @return void
     */
    function unsetProperty ($propertyName)
    {
        if (isset( $this->_aProperties[$propertyName] )) {
            unset( $this->_aProperties[$propertyName] );
            $this->saveSingleton();
        }
    }

    /**
     * Returns the value of a defined property.
     * If it doesn't exist then returns null
     *
     * @param string $propertyName
     * @return string/null
     */
    function getProperty ($propertyName)
    {
        if (isset( $this->_aProperties[$propertyName] )) {
            return $this->_aProperties[$propertyName];
        } else {
            return null;
        }
    }

    /**
     * Used to have a record of succesful logins to the system (total and by WS)
     * param
     *
     * @return void
     */
    function sucessfulLogin ()
    {
        $this->logins ++;
        if (isset( $this->workspaces[SYS_SYS] ) && isset( $this->workspaces[SYS_SYS]['WSP_LOGINS'] ))
            $this->workspaces[SYS_SYS]['WSP_LOGINS'] ++;

        if (isset( $this->workspaces[SYS_SYS] ) && ! isset( $this->workspaces[SYS_SYS]['WSP_LOGINS'] ))
            $this->workspaces[SYS_SYS]['WSP_LOGINS'] = 1;

        $this->saveSingleton();
    }

    function setWsInfo ($wsname, $info)
    {
        $this->aWSinfo[$wsname] = $info;
    }

    /**
     * This will togle the status of a workspace (enabled,disabled)
     *
     * @param string $wsName
     * @return void
     */
    function changeStatusWS ($wsName)
    {

        if (isset( $this->_aWSapces[$wsName] )) { //Enable WS
            unset( $this->_aWSapces[$wsName] );
        } else {
            $this->_aWSapces[$wsName] = 'disabled';
        }
        $this->saveSingleton();
    }

    /**
     * Return the status of a WS.
     * If is disabled will return 1 otherwise 0
     *
     * @param $wsname
     * @return boolean
     */
    function isWSDisabled ($wsName)
    {
        return isset( $this->_aWSapces[$wsName] );
    }

    /**
     * Check only if the server address or server name has changed,
     * to send another beat in next minute.
     * param
     *
     * @return boolean
     */
    function checkIfHostNameHasChanged ()
    {
        //removed the PM_VERSION control, because when an upgrade is done, the haveSetupData has to be changed.
        if ($this->ip != getenv( 'SERVER_ADDR' ))
            return false;

        if ($this->host != getenv( 'SERVER_NAME' ))
            return false;

        return $this->haveSetupData;
    }

    /**
     * Will return a list of all WS in this system and their related information.
     *
     * @uses getWSList
     * param
     * @return array
     */
    function getWSList ()
    {
        $dir = PATH_DB;
        $wsArray = array ();
        if (file_exists( $dir )) {
            if ($handle = opendir( $dir )) {
                while (false !== ($file = readdir( $handle ))) {
                    if (($file != ".") && ($file != "..")) {
                        if (file_exists( PATH_DB . $file . '/db.php' )) { //print $file."/db.php <hr>";
                            $statusl = ($this->isWSDisabled( $file )) ? 'DISABLED' : 'ENABLED';
                            if (isset( $this->aWSinfo[$file] )) {
                                $wsInfo = $this->aWSinfo[$file];
                            } else {
                                $wsInfo['num_processes'] = "not gathered yet";
                                $wsInfo['num_cases'] = "not gathered yet";
                                ;
                                $wsInfo['num_users'] = "not gathered yet";
                            }
                            $wsArray[$file] = array ('WSP_ID' => $file,'WSP_NAME' => $file,'WSP_STATUS' => $statusl,'WSP_PROCESS_COUNT' => $wsInfo['num_processes'],'WSP_CASES_COUNT' => $wsInfo['num_cases'],'WSP_USERS_COUNT' => isset( $wsInfo['num_users'] ) ? $wsInfo['num_users'] : ""
                            );
                            if (isset( $this->workspaces[$file]['WSP_LOGINS'] ))
                                $wsArray[$file]['WSP_LOGINS'] = $this->workspaces[$file]['WSP_LOGINS'];

                        }
                    }
                }
                closedir( $handle );
            }
        }
        return $wsArray;

    }

    /**
     * Will return all the information of a WS.
     * - Status
     * - # of cases
     * - # of processes
     * - # of users
     *
     * @param string $wsName
     * @return array
     */
    function getWorkspaceInfo ($wsName)
    {
        $aResult = Array ('num_processes' => '0','num_cases' => '0'
        );
        $result = array ();
        require_once 'classes/model/Process.php';
        require_once 'classes/model/Application.php';
        require_once 'classes/model/Users.php';

        $Criteria = new Criteria( 'workflow' );
        $Criteria->add( ProcessPeer::PRO_STATUS, 'ACTIVE', CRITERIA::EQUAL );
        $aResult['num_processes'] = ProcessPeer::doCount( $Criteria );

        $Criteria = new Criteria( 'workflow' );
        $Criteria->add( ApplicationPeer::APP_STATUS, 'COMPLETED', CRITERIA::NOT_EQUAL );
        $aResult['num_cases'] = ApplicationPeer::doCount( $Criteria );

        $Criteria = new Criteria( 'workflow' );
        $Criteria->add( UsersPeer::USR_STATUS, array ('DELETED','DISABLED'
        ), CRITERIA::NOT_IN );
        $aResult['num_users'] = UsersPeer::doCount( $Criteria );
        return $aResult;
    }

    /**
     * Will list the plugins of the system
     * param
     *
     * @return array
     */
    function getPluginsList ()
    {
        return $this->pluginsA;
    }

    /**
     * *
     * Register a PLugin
     */
    function addPlugin ($workspace, $info)
    {
        $this->pluginsA[$workspace] = $info;
    }

    function getDBVersion ()
    {
        $sMySQLVersion = '?????';
        if (defined( "DB_HOST" )) {
            G::LoadClass( 'net' );
            G::LoadClass( 'dbConnections' );
            $dbNetView = new NET( DB_HOST );
            $dbNetView->loginDbServer( DB_USER, DB_PASS );

            $dbConns = new dbConnections( '' );
            $availdb = '';
            foreach ($dbConns->getDbServicesAvailables() as $key => $val) {
                if ($availdb != '')
                    $availdb .= ', ';
                $availdb .= $val['name'];
            }

            try {
                $sMySQLVersion = $dbNetView->getDbServerVersion( 'mysql' );
            } catch (Exception $oException) {
                $sMySQLVersion = '?????';
            }
        }
        return $sMySQLVersion;
    }

    /**
     * Will reset all the logins' count
     * param
     *
     * @return void
     */
    function resetLogins ()
    {
        $this->logins = 0;
        if (is_array( $this->workspaces )) {
            foreach ($this->workspaces as $wsName => $wsinfo) {
                $this->workspaces[$wsName]['WSP_LOGINS'] = 0;
            }
        }
    }

    /**
     * Get the value of language direction property
     *
     * @param void
     * @return string
     */
    function getLanDirection ()
    {
        if (! isset( $this->lanDirection )) {
            $this->lanDirection = 'L';
        }
        if (defined( 'SYS_LANG' )) {
            //if we already have the landirection for this language, just return from serverConf
            if ($this->lanLanguage == SYS_LANG)
                return $this->lanDirection;

                //if not , we need to query Database, in order to get the direction
            $this->lanDirection = 'L'; //default value;
            $this->lanLanguage = SYS_LANG;
            require_once 'classes/model/Language.php';
            $oLang = new Language();
            try {
                $aLang = $oLang->load( SYS_LANG );
                if (isset( $aLang['LAN_DIRECTION'] )) {
                    $this->lanDirection = strtoupper( $aLang['LAN_DIRECTION'] );
                }
                $this->saveSingleton();
            } catch (Exception $e) {
                $this->lanDirection = 'L';
            }
        }
        return $this->lanDirection;
    }

    /**
     * With this is possible to save a property that will be saved in the properties
     * array of this class.
     *
     * @param string $propertyName
     * @param string $propertyValue
     * @param string $workspace
     */
    function setHeartbeatProperty ($propertyName, $propertyValue, $workspace)
    {
        $this->_aHeartbeatConfig[$workspace][$propertyName] = $propertyValue;
        $this->saveSingleton();
    }

    /**
     * To unset a defined property.
     * If it doesn't exist then it does nothing.
     *
     * @param string $propertyName
     * @param string $workspace
     * @return void
     */
    function unsetHeartbeatProperty ($propertyName, $workspace)
    {
        if (isset( $this->_aHeartbeatConfig[$workspace][$propertyName] ))
            unset( $this->_aHeartbeatConfig[$workspace][$propertyName] );
        $this->saveSingleton();
    }

    /**
     * Returns the value of a defined property.
     * If it doesn't exist then returns null
     *
     * @param string $propertyName
     * @return string/null
     */
    function getHeartbeatProperty ($propertyName, $workspace)
    {
        if (isset( $this->_aHeartbeatConfig[$workspace][$propertyName] )) {
            return $this->_aHeartbeatConfig[$workspace][$propertyName];
        } else {
            return null;
        }
    }

    function isRtl ($lang = SYS_LANG)
    {
        $lang = substr( $lang, 0, 2 );
        return in_array( $lang, $this->rtlLang );
    }

}