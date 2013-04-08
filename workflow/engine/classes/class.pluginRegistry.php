<?php

/**
 * class.pluginRegistry.php
 *
 * @package workflow.engine.classes
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
 */

/**
 *
 * @package workflow.engine.classes
 */

class pluginDetail
{
    public $sNamespace;
    public $sClassName;
    public $sFriendlyName = null;
    public $sDescription = null;
    public $sSetupPage = null;
    public $sFilename;
    public $sPluginFolder = '';
    public $sCompanyLogo = '';
    public $iVersion = 0;
    public $enabled = false;
    public $aWorkspaces = null;
    public $bPrivate = false;

    /**
     * This function is the constructor of the pluginDetail class
     *
     * @param string $sNamespace
     * @param string $sClassName
     * @param string $sFilename
     * @param string $sFriendlyName
     * @param string $sPluginFolder
     * @param string $sDescription
     * @param string $sSetupPage
     * @param integer $iVersion
     * @return void
     */
    public function __construct ($sNamespace, $sClassName, $sFilename, $sFriendlyName = '', $sPluginFolder = '', $sDescription = '', $sSetupPage = '', $iVersion = 0)
    {
        $this->sNamespace = $sNamespace;
        $this->sClassName = $sClassName;
        $this->sFriendlyName = $sFriendlyName;
        $this->sDescription = $sDescription;
        $this->sSetupPage = $sSetupPage;
        $this->iVersion = $iVersion;
        $this->sFilename = $sFilename;
        if ($sPluginFolder == '') {
            $this->sPluginFolder = $sNamespace;
        } else {
            $this->sPluginFolder = $sPluginFolder;
        }
    }
}

/**
 *
 * @package workflow.engine.classes
 */

class PMPluginRegistry
{
    private $_aPluginDetails = array ();
    private $_aPlugins = array ();
    private $_aMenus = array ();
    private $_aFolders = array ();
    private $_aTriggers = array ();
    private $_aDashlets = array ();
    private $_aReports = array ();
    private $_aPmFunctions = array ();
    private $_aRedirectLogin = array ();
    private $_aSteps = array ();
    private $_aCSSStyleSheets = array ();
    private $_aToolbarFiles = array ();
    private $_aCaseSchedulerPlugin = array ();
    private $_aTaskExtendedProperties = array ();
    private $_aDashboardPages = array ();

    /**
     * Registry a plugin javascript to include with js core at same runtime
     */
    private $_aJavascripts = array ();

    /**
     * Contains all rest services classes from plugins
     */
    private $_restServices = array ();

    private static $instance = null;

    /**
     * This function is the constructor of the PMPluginRegistry class
     * param
     *
     * @return void
     */
    public function __construct ()
    {
    }

    /**
     * This function is instancing to this class
     * param
     *
     * @return object
     */
    public function &getSingleton ()
    {
        if (self::$instance == null) {
            self::$instance = new PMPluginRegistry();
        }
        return self::$instance;
    }

    /**
     * This function generates a storable representation of a value
     * param
     *
     * @return void
     */
    public function serializeInstance ()
    {
        return serialize( self::$instance );
    }

    /**
     * This function takes a single serialized variable and converts it back a code
     *
     * @param string $serialized
     * @return void
     */
    public function unSerializeInstance ($serialized)
    {
        if (self::$instance == null) {
            self::$instance = new PMPluginRegistry();
        }

        $instance = unserialize( $serialized );
        self::$instance = $instance;
    }

    /**
     * Save the current instance to the plugin singleton
     */
    public function save ()
    {
        file_put_contents( PATH_DATA_SITE . 'plugin.singleton', $this->serializeInstance() );
    }

    /**
     * Register the plugin in the singleton
     *
     * @param unknown_type $sClassName
     * @param unknown_type $sNamespace
     * @param unknown_type $sFilename
     */
    public function registerPlugin ($sNamespace, $sFilename = null)
    {
        //require_once ($sFilename);


        $sClassName = $sNamespace . "plugin";
        $plugin = new $sClassName( $sNamespace, $sFilename );

        if (isset( $this->_aPluginDetails[$sNamespace] )) {
            $this->_aPluginDetails[$sNamespace]->iVersion = $plugin->iVersion;

            return;
        }

        $detail = new pluginDetail( $sNamespace, $sClassName, $sFilename, $plugin->sFriendlyName, $plugin->sPluginFolder, $plugin->sDescription, $plugin->sSetupPage, $plugin->iVersion );

        if (isset( $plugin->aWorkspaces )) {
            $detail->aWorkspaces = $plugin->aWorkspaces;
        }

        if (isset( $plugin->bPrivate )) {
            $detail->bPrivate = $plugin->bPrivate;
        }

        //if (isset($this->_aPluginDetails[$sNamespace])){
        //    $detail->enabled = $this->_aPluginDetails[$sNamespace]->enabled;
        //}


        $this->_aPluginDetails[$sNamespace] = $detail;
    }

    /**
     * get the plugin details, by filename
     *
     * @param unknown_type $sFilename
     */
    public function getPluginDetails ($sFilename)
    {
        foreach ($this->_aPluginDetails as $key => $row) {
            if ($sFilename == baseName( $row->sFilename )) {
                return $row;
            }
        }
        return null;
    }

    /**
     * Enable the plugin in the singleton
     *
     * @param unknown_type $sNamespace
     */
    public function enablePlugin ($sNamespace)
    {
        foreach ($this->_aPluginDetails as $namespace => $detail) {
            if ($sNamespace == $namespace) {
                $this->registerFolder( $sNamespace, $sNamespace, $detail->sPluginFolder );
                //register the default directory, later we can have more
                $this->_aPluginDetails[$sNamespace]->enabled = true;
                $oPlugin = new $detail->sClassName( $detail->sNamespace, $detail->sFilename );
                $this->_aPlugins[$detail->sNamespace] = $oPlugin;
                if (method_exists( $oPlugin, 'enable' )) {
                    $oPlugin->enable();
                    $this->verifyTranslation($detail->sNamespace); 
                }
                return true;
            }
        }
        throw new Exception( "Unable to enable plugin '$sNamespace' (plugin not found)" );
    }

    /**
     * disable the plugin in the singleton
     *
     * @param unknown_type $sNamespace
     */
    public function disablePlugin ($sNamespace, $eventPlugin = 1)
    {
        $sw = false;

        foreach ($this->_aPluginDetails as $namespace => $detail) {
            if ($namespace == $sNamespace) {
                unset( $this->_aPluginDetails[$sNamespace] );

                if ($eventPlugin == 1) {
                    $plugin = new $detail->sClassName( $detail->sNamespace, $detail->sFilename );
                    $this->_aPlugins[$detail->sNamespace] = $plugin;
                    if (method_exists( $plugin, "disable" )) {
                        $plugin->disable();
                    }
                }

                $sw = true;
            }
        }

        if (! $sw) {
            throw new Exception( "Unable to disable plugin '$sNamespace' (plugin not found)" );
        }

        foreach ($this->_aMenus as $key => $detail) {
            if ($detail->sNamespace == $sNamespace) {
                unset( $this->_aMenus[$key] );
            }
        }
        foreach ($this->_aFolders as $key => $detail) {
            if ($detail->sNamespace == $sNamespace) {
                unset( $this->_aFolders[$key] );
            }
        }
        foreach ($this->_aTriggers as $key => $detail) {
            if ($detail->sNamespace == $sNamespace) {
                unset( $this->_aTriggers[$key] );
            }
        }
        foreach ($this->_aDashlets as $key => $detail) {
            if ($detail == $sNamespace) {
                unset( $this->_aDashlets[$key] );
            }
        }
        foreach ($this->_aReports as $key => $detail) {
            if ($detail == $sNamespace) {
                unset( $this->_aReports[$key] );
            }
        }
        foreach ($this->_aPmFunctions as $key => $detail) {
            if ($detail == $sNamespace) {
                unset( $this->_aPmFunctions[$key] );
            }
        }
        foreach ($this->_aRedirectLogin as $key => $detail) {
            if ($detail->sNamespace == $sNamespace) {
                unset( $this->_aRedirectLogin[$key] );
            }
        }
        foreach ($this->_aSteps as $key => $detail) {
            if ($detail->sNamespace == $sNamespace) {
                unset( $this->_aSteps[$key] );
            }
        }
        foreach ($this->_aToolbarFiles as $key => $detail) {
            if ($detail->sNamespace == $sNamespace) {
                unset( $this->_aToolbarFiles[$key] );
            }
        }
        foreach ($this->_aCSSStyleSheets as $key => $detail) {
            if ($detail->sNamespace == $sNamespace) {
                unset( $this->_aCSSStyleSheets[$key] );
            }
        }
        foreach ($this->_aCaseSchedulerPlugin as $key => $detail) {
            if ($detail->sNamespace == $sNamespace) {
                unset( $this->_aCaseSchedulerPlugin[$key] );
            }
        }
        foreach ($this->_aTaskExtendedProperties as $key => $detail) {
            if ($detail->sNamespace == $sNamespace) {
                unset( $this->_aTaskExtendedProperties[$key] );
            }
        }
        foreach ($this->_aDashboardPages as $key => $detail) {
            if ($detail->sNamespace == $sNamespace) {
                unset( $this->_aDashboardPages[$key] );
            }
        }

        //unregistering javascripts from this plugin
        $this->unregisterJavascripts( $sNamespace );
        //unregistering rest services from this plugin
        $this->unregisterRestService( $sNamespace );
    }

    /**
     * get status plugin in the singleton
     *
     * @param unknown_type $sNamespace
     */
    public function getStatusPlugin ($sNamespace)
    {
        foreach ($this->_aPluginDetails as $namespace => $detail) {
            if ($sNamespace == $namespace) {
                if ($this->_aPluginDetails[$sNamespace]->enabled) {
                    return 'enabled';
                } else {
                    return 'disabled';
                }
            }
        }
        return 0;
    }

    /**
     * Install a plugin archive.
     * If pluginName is specified, the archive will
     * only be installed if it contains this plugin.
     *
     * @return bool true if enabled, false otherwise
     */
    public function installPluginArchive ($filename, $pluginName)
    {
        G::LoadThirdParty( "pear/Archive", "Tar" );
        $tar = new Archive_Tar( $filename );
        $files = $tar->listContent();
        $plugins = array ();
        $namePlugin = array ();
        foreach ($files as $f) {
            //if (preg_match("/^([\w\.]*).ini$/", $f["filename"], $matches)) {
            if (preg_match( "/^(.*pluginConfig)\.ini$/", $f["filename"], $matches )) {
                $plugins[] = $matches[1];
            }
            if (preg_match( "/^.*($pluginName)\.php$/", $f["filename"], $matches )) {
                $namePlugin[] = $matches[1];
            }
        }

        if (count( $plugins ) > 1) {
            throw new Exception( "Multiple plugins in one archive are not supported currently" );
        }

        //if (isset($pluginName) && !in_array($pluginName, $plugins)) {
        if (isset( $pluginName ) && ! in_array( $pluginName, $namePlugin )) {
            throw new Exception( "Plugin '$pluginName' not found in archive" );
        }

        //$pluginName = $plugins[0];
        $pluginFile = "$pluginName.php";

        /*
        $oldPluginStatus = $this->getStatusPlugin($pluginFile);

        if ($pluginStatus != 0) {
          $oldDetails = $this->getPluginDetails($pluginFile);
          $oldVersion = $oldDetails->iVersion;
        } else {
          $oldDetails = NULL;
          $oldVersion = NULL;
        }
        */

        //$pluginIni = $tar->extractInString("$pluginName.ini");
        //$pluginConfig = parse_ini_string($pluginIni);
        /*
        if (!empty($oClass->aDependences)) {
          foreach ($oClass->aDependences as $aDependence) {
            if (file_exists(PATH_PLUGINS . $aDependence['sClassName'] . '.php')) {
              require_once PATH_PLUGINS . $aDependence['sClassName'] . '.php';
              if (!$oPluginRegistry->getPluginDetails($aDependence['sClassName'] . '.php')) {
                throw new Exception('This plugin needs "' . $aDependence['sClassName'] . '" plugin');
              }
            }
            else {
              throw new Exception('This plugin needs "' . $aDependence['sClassName'] . '" plugin');
            }
          }
        }
        unset($oClass);
        if ($fVersionOld > $fVersionNew) {
          throw new Exception('A recent version of this plugin was already installed.');
        }
        */

        $res = $tar->extract( PATH_PLUGINS );

        if (! file_exists( PATH_PLUGINS . $pluginFile )) {
            throw (new Exception( "File \"$pluginFile\" doesn't exist" ));
        }

        require_once (PATH_PLUGINS . $pluginFile);
        $details = $this->getPluginDetails( $pluginFile );

        $this->installPlugin( $details->sNamespace );
        $this->setupPlugins();

        $this->enablePlugin( $details->sNamespace );
        $this->save();
    }

    public function uninstallPlugin ($sNamespace)
    {
        $pluginFile = $sNamespace . ".php";

        if (! file_exists( PATH_PLUGINS . $pluginFile )) {
            throw (new Exception( "File \"$pluginFile\" doesn't exist" ));
        }

        ///////
        require_once (PATH_PLUGINS . $pluginFile);

        foreach ($this->_aPluginDetails as $namespace => $detail) {
            if ($namespace == $sNamespace) {
                $this->enablePlugin( $detail->sNamespace );
                $this->disablePlugin( $detail->sNamespace );

                ///////
                $plugin = new $detail->sClassName( $detail->sNamespace, $detail->sFilename );
                $this->_aPlugins[$detail->sNamespace] = $plugin;

                if (method_exists( $plugin, "uninstall" )) {
                    $plugin->uninstall();
                }

                ///////
                $this->save();
                ///////
                $pluginDir = PATH_PLUGINS . $detail->sPluginFolder;

                if (isset( $detail->sFilename ) && ! empty( $detail->sFilename ) && file_exists( $detail->sFilename )) {
                    unlink( $detail->sFilename );
                }

                if (isset( $detail->sPluginFolder ) && ! empty( $detail->sPluginFolder ) && file_exists( $pluginDir )) {
                    G::rm_dir( $pluginDir );
                }

                ///////
                $this->uninstallPluginWorkspaces( array ($sNamespace
                ) );
                ///////
                break;
            }
        }
    }

    public function uninstallPluginWorkspaces ($arrayPlugin)
    {
        G::LoadClass( "system" );
        G::LoadClass( "wsTools" );

        $workspace = System::listWorkspaces();

        foreach ($workspace as $indexWS => $ws) {
            $wsPathDataSite = PATH_DATA . "sites" . PATH_SEP . $ws->name . PATH_SEP;

            if (file_exists( $wsPathDataSite . "plugin.singleton" )) {
                //G::LoadClass("plugin");
                //Here we are loading all plug-ins registered
                //The singleton has a list of enabled plug-ins


                $pluginRegistry = &PMPluginRegistry::getSingleton();
                $pluginRegistry->unSerializeInstance( file_get_contents( $wsPathDataSite . "plugin.singleton" ) );

                ///////
                $attributes = $pluginRegistry->getAttributes();

                foreach ($arrayPlugin as $index => $value) {
                    if (isset( $attributes["_aPluginDetails"][$value] )) {
                        $pluginRegistry->disablePlugin( $value, 0 );
                    }
                }

                ///////
                file_put_contents( $wsPathDataSite . "plugin.singleton", $pluginRegistry->serializeInstance() );
            }
        }
    }

    /**
     * install the plugin
     *
     * @param unknown_type $sNamespace
     */
    public function installPlugin ($sNamespace)
    {
        try {
            foreach ($this->_aPluginDetails as $namespace => $detail) {
                if ($sNamespace == $namespace) {
                    $oPlugin = new $detail->sClassName( $detail->sNamespace, $detail->sFilename );
                    $this->_aPlugins[$detail->sNamespace] = $oPlugin;
                    $oPlugin->install();
                }
            }
        } catch (Exception $e) {
            global $G_PUBLISH;
            $aMessage['MESSAGE'] = $e->getMessage();
            $G_PUBLISH = new Publisher();
            $G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'login/showMessage', '', $aMessage );
            G::RenderPage( 'publish' );
            die();
        }

    }

    /**
     * Register a menu in the singleton
     *
     * @param unknown_type $sNamespace
     * @param unknown_type $sMenuId
     * @param unknown_type $sFilename
     */
    public function registerMenu ($sNamespace, $sMenuId, $sFilename)
    {
        $found = false;
        foreach ($this->_aMenus as $row => $detail) {
            if ($sMenuId == $detail->sMenuId && $sNamespace == $detail->sNamespace) {
                $found = true;
            }
        }
        if (! $found) {
            $menuDetail = new menuDetail( $sNamespace, $sMenuId, $sFilename );
            $this->_aMenus[] = $menuDetail;
        }
    }

    /**
     * Register a dashlet class in the singleton
     *
     * @param unknown_type $className
     */
    public function registerDashlets ($namespace)
    {
        $found = false;
        foreach ($this->_aDashlets as $row => $detail) {
            if ($namespace == $detail) {
                $found = true;
            }
        }
        if (! $found) {
            $this->_aDashlets[] = $namespace;
        }
    }

    /**
     * Register a stylesheet in the singleton
     *
     * @param unknown_type $sNamespace
     * @param unknown_type $sPage
     */
    public function registerCss ($sNamespace, $sCssFile)
    {
        $found = false;
        foreach ($this->_aCSSStyleSheets as $row => $detail) {
            if ($sCssFile == $detail->sCssFile && $sNamespace == $detail->sNamespace) {
                $detail->sCssFile = $sCssFile;
                $found = true;
            }
        }
        if (! $found) {
            $cssFile = new cssFile( $sNamespace, $sCssFile );
            $this->_aCSSStyleSheets[] = $cssFile;
        }
    }

    /**
     * return all css
     *
     * @return array
     */
    public function getRegisteredCss ()
    {
        return $this->_aCSSStyleSheets;
    }

    /**
     * Register a plugin javascript to run with core js script at same runtime
     *
     * @param string $sNamespace
     * @param string $coreJsFile
     * @param array/string $pluginJsFile
     */
    public function registerJavascript ($sNamespace, $sCoreJsFile, $pluginJsFile)
    {

        foreach ($this->_aJavascripts as $i => $js) {
            if ($sCoreJsFile == $js->sCoreJsFile && $sNamespace == $js->sNamespace) {
                if (is_string( $pluginJsFile )) {
                    if (! in_array( $pluginJsFile, $this->_aJavascripts[$i]->pluginJsFile )) {
                        $this->_aJavascripts[$i]->pluginJsFile[] = $pluginJsFile;
                    }
                } elseif (is_array( $pluginJsFile )) {
                    $this->_aJavascripts[$i]->pluginJsFile = array_unique( array_merge( $pluginJsFile, $this->_aJavascripts[$i]->pluginJsFile ) );
                } else {
                    throw new Exception( 'Invalid third param, $pluginJsFile should be a string or array - ' . gettype( $pluginJsFile ) . ' given.' );
                }
                return $this->_aJavascripts[$i];
            }
        }

        $js = new StdClass();
        $js->sNamespace = $sNamespace;
        $js->sCoreJsFile = $sCoreJsFile;
        $js->pluginJsFile = Array ();

        if (is_string( $pluginJsFile )) {
            $js->pluginJsFile[] = $pluginJsFile;
        } elseif (is_array( $pluginJsFile )) {
            $js->pluginJsFile = array_merge( $js->pluginJsFile, $pluginJsFile );
        } else {
            throw new Exception( 'Invalid third param, $pluginJsFile should be a string or array - ' . gettype( $pluginJsFile ) . ' given.' );
        }

        $this->_aJavascripts[] = $js;
    }

    /**
     * return all plugin javascripts
     *
     * @return array
     */
    public function getRegisteredJavascript ()
    {
        return $this->_aJavascripts;
    }

    /**
     * return all plugin javascripts given a core js file, from all namespaces or a single namespace
     *
     * @param string $sCoreJsFile
     * @param string $sNamespace
     * @return array
     */
    public function getRegisteredJavascriptBy ($sCoreJsFile, $sNamespace = '')
    {
        $scripts = array ();

        if ($sNamespace == '') {
            foreach ($this->_aJavascripts as $i => $js) {
                if ($sCoreJsFile == $js->sCoreJsFile) {
                    $scripts = array_merge( $scripts, $this->_aJavascripts[$i]->pluginJsFile );
                }
            }
        } else {
            foreach ($this->_aJavascripts as $i => $js) {
                if ($sCoreJsFile == $js->sCoreJsFile && $sNamespace == $js->sNamespace) {
                    $scripts = array_merge( $scripts, $this->_aJavascripts[$i]->pluginJsFile );
                }
            }
        }
        return $scripts;
    }

    /**
     * unregister all javascripts from a namespace or a js core file given
     *
     * @param string $sNamespace
     * @param string $sCoreJsFile
     * @return array
     */
    public function unregisterJavascripts ($sNamespace, $sCoreJsFile = '')
    {
        if ($sCoreJsFile == '') {
            // if $sCoreJsFile=='' unregister all js from this namespace
            foreach ($this->_aJavascripts as $i => $js) {
                if ($sNamespace == $js->sNamespace) {
                    unset( $this->_aJavascripts[$i] );
                }
            }
            // Re-index when all js were unregistered
            $this->_aJavascripts = array_values( $this->_aJavascripts );
        } else {
            foreach ($this->_aJavascripts as $i => $js) {
                if ($sCoreJsFile == $js->sCoreJsFile && $sNamespace == $js->sNamespace) {
                    unset( $this->_aJavascripts[$i] );
                    // Re-index for each js that was unregistered
                    $this->_aJavascripts = array_values( $this->_aJavascripts );
                }
            }
        }
    }

    /**
     * Register a reports class in the singleton
     *
     * @param unknown_type $sNamespace
     * @param unknown_type $sMenuId
     * @param unknown_type $sFilename
     */
    public function registerReport ($sNamespace)
    {
        $found = false;
        foreach ($this->_aReports as $row => $detail) {
            if ($sNamespace == $detail) {
                $found = true;
            }
        }
        if (! $found) {
            $this->_aReports[] = $sNamespace;
        }
    }

    /**
     * Register a PmFunction class in the singleton
     *
     * @param unknown_type $sNamespace
     * @param unknown_type $sMenuId
     * @param unknown_type $sFilename
     */
    public function registerPmFunction ($sNamespace)
    {
        $found = false;
        foreach ($this->_aPmFunctions as $row => $detail) {
            if ($sNamespace == $detail) {
                $found = true;
            }
        }
        if (! $found) {
            $this->_aPmFunctions[] = $sNamespace;
        }
    }

    /**
     * Register a redirectLogin class in the singleton
     *
     * @param unknown_type $sNamespace
     * @param unknown_type $sRole
     * @param unknown_type $sPath
     */
    public function registerRedirectLogin ($sNamespace, $sRole, $sPathMethod)
    {
        $found = false;
        foreach ($this->_aRedirectLogin as $row => $detail) {
            if (($sNamespace == $detail->sNamespace) && ($sRole == $detail->sRoleCode)) {
                //Filters based on Workspace and Role Code
                $found = true;
            }
        }
        if (! $found) {
            $this->_aRedirectLogin[] = new redirectDetail( $sNamespace, $sRole, $sPathMethod );
        }
    }

    /**
     * Register a folder for methods
     *
     * @param unknown_type $sFolderName
     */
    public function registerFolder ($sNamespace, $sFolderId, $sFolderName)
    {
        $found = false;
        foreach ($this->_aFolders as $row => $detail) {
            if ($sFolderId == $detail->sFolderId && $sNamespace == $detail->sNamespace) {
                $found = true;
            }
        }

        if (! $found) {
            $this->_aFolders[] = new folderDetail( $sNamespace, $sFolderId, $sFolderName );
        }
    }

    /**
     * Register a step for process
     *
     * @param unknown_type $sFolderName
     */
    public function registerStep ($sNamespace, $sStepId, $sStepName, $sStepTitle, $setupStepPage = '')
    {
        $found = false;
        foreach ($this->_aSteps as $row => $detail) {
            if ($sStepId == $detail->sStepId && $sNamespace == $detail->sNamespace) {
                $found = true;
            }
        }

        if (! $found) {
            $this->_aSteps[] = new stepDetail( $sNamespace, $sStepId, $sStepName, $sStepTitle, $setupStepPage );
        }
    }

    /**
     * return true if the $sFolderName is registered in the singleton
     *
     * @param unknown_type $sFolderName
     */
    public function isRegisteredFolder ($sFolderName)
    {
        foreach ($this->_aFolders as $row => $folder) {
            if ($sFolderName == $folder->sFolderName && is_dir( PATH_PLUGINS . $folder->sFolderName )) {
                return true;
            } elseif ($sFolderName == $folder->sFolderName && is_dir( PATH_PLUGINS . $folder->sNamespace . PATH_SEP . $folder->sFolderName )) {
                return $folder->sNamespace;
            }
        }
        return false;
    }

    /**
     * return all menus related to a menuId
     *
     * @param unknown_type $menuId
     */
    public function getMenus ($menuId)
    {
        foreach ($this->_aMenus as $row => $detail) {
            if ($menuId == $detail->sMenuId && file_exists( $detail->sFilename )) {
                include ($detail->sFilename);
            }
        }
    }

    /**
     * return all dashlets classes registered
     *
     * @return array
     */
    public function getDashlets ()
    {
        return $this->_aDashlets;
    }

    /**
     * this function returns all reports registered
     *
     * @return array
     */
    public function getReports ()
    {
        return $this->_aReports;
        $report = array ();
        foreach ($this->_aReports as $row => $detail) {
            $sClassName = str_replace( 'plugin', 'class', $this->_aPluginDetails[$detail]->sClassName );
            $report[] = $sClassName;
        }
        return $report;
    }

    /**
     * This function returns all pmFunctions registered
     * @ array
     */
    public function getPmFunctions ()
    {
        return $this->_aPmFunctions;
        $pmf = array ();
        foreach ($this->_aPmFunctions as $row => $detail) {
            $sClassName = str_replace( 'plugin', 'class', $this->_aPluginDetails[$detail]->sClassName );
            $pmf[] = $sClassName;
        }
        return $pmf;
    }

    /**
     * This function returns all steps registered
     *
     * @return string
     */
    public function getSteps ()
    {
        return $this->_aSteps;
    }

    /**
     * This function returns all redirect registered
     *
     * @return string
     */
    public function getRedirectLogins ()
    {
        return $this->_aRedirectLogin;
    }

    /**
     * execute all triggers related to a triggerId
     *
     * @param unknown_type $menuId
     * @return object
     */
    public function executeTriggers ($triggerId, $oData)
    {
        foreach ($this->_aTriggers as $row => $detail) {
            if ($triggerId == $detail->sTriggerId) {
                //review all folders registered for this namespace
                $found = false;
                $classFile = '';

                foreach ($this->_aFolders as $row => $folder) {
                    $fname = PATH_PLUGINS . $folder->sFolderName . PATH_SEP . 'class.' . $folder->sFolderName . '.php';
                    if ($detail->sNamespace == $folder->sNamespace && file_exists( $fname )) {
                        $found = true;
                        $classFile = $fname;
                    }
                }
                if ($found) {
                    require_once ($classFile);
                    $sClassName = substr( $this->_aPluginDetails[$detail->sNamespace]->sClassName, 0, 1 ) . str_replace( 'plugin', 'class', substr( $this->_aPluginDetails[$detail->sNamespace]->sClassName, 1 ) );
                    $obj = new $sClassName();
                    $methodName = $detail->sTriggerName;
                    $response = $obj->{$methodName}( $oData );
                    if (PEAR::isError( $response )) {
                        print $response->getMessage();
                        return;
                    }
                    return $response;
                } else {
                    print "error in call method " . $detail->sTriggerName;
                }
            }
        }
    }

    /**
     * verify if exists triggers related to a triggerId
     *
     * @param unknown_type $triggerId
     */
    public function existsTrigger ($triggerId)
    {
        $found = false;
        foreach ($this->_aTriggers as $row => $detail) {
            if ($triggerId == $detail->sTriggerId) {
                //review all folders registered for this namespace
                foreach ($this->_aFolders as $row => $folder) {
                    $fname = PATH_PLUGINS . $folder->sFolderName . PATH_SEP . 'class.' . $folder->sFolderName . '.php';
                    if ($detail->sNamespace == $folder->sNamespace && file_exists( $fname )) {
                        $found = true;
                    }
                }
            }
        }
        return $found;
    }

    /**
     * Return info related to a triggerId
     *
     * @param unknown_type $triggerId
     * @return object
     */
    public function getTriggerInfo ($triggerId)
    {
        $found = null;
        foreach ($this->_aTriggers as $row => $detail) {
            if ($triggerId == $detail->sTriggerId) {
                //review all folders registered for this namespace
                foreach ($this->_aFolders as $row => $folder) {
                    $fname = PATH_PLUGINS . $folder->sFolderName . PATH_SEP . 'class.' . $folder->sFolderName . '.php';
                    if ($detail->sNamespace == $folder->sNamespace && file_exists( $fname )) {
                        $found = $detail;
                    }
                }
            }
        }
        return $found;
    }

    /**
     * Register a trigger in the Singleton
     *
     * @param unknown_type $sTriggerId
     * @param unknown_type $sMethodFunction
     * @return void
     */
    public function registerTrigger ($sNamespace, $sTriggerId, $sTriggerName)
    {
        $found = false;
        foreach ($this->_aTriggers as $row => $detail) {
            if ($sTriggerId == $detail->sTriggerId && $sNamespace == $detail->sNamespace) {
                $found = true;
            }
        }
        if (! $found) {
            $triggerDetail = new triggerDetail( $sNamespace, $sTriggerId, $sTriggerName );
            $this->_aTriggers[] = $triggerDetail;
        }
    }

    /**
     * get plugin
     *
     * @param unknown_type $sNamespace
     * @return void
     */
    public function &getPlugin ($sNamespace)
    {
        if (array_key_exists( $sNamespace, $this->_aPlugins )) {
            return $this->_aPlugins[$sNamespace];
        }
        /*
        $aDetails = KTUtil::arrayGet($this->_aPluginDetails, $sNamespace);
        if (empty($aDetails)) {
            return null;
        }
        $sFilename = $aDetails[2];
        if (!empty($sFilename)) {
            require_once($sFilename);
        }
        $sClassName = $aDetails[0];
        $oPlugin =& new $sClassName($sFilename);
        $this->_aPlugins[$sNamespace] =& $oPlugin;
        return $oPlugin;
        */
    }

    /**
     * set company logo
     *
     * @param unknown_type $sNamespace
     * @param unknown_type $filename
     * @return void
     */
    public function setCompanyLogo ($sNamespace, $filename)
    {
        $found = false;
        foreach ($this->_aPluginDetails as $row => $detail) {
            if ($sNamespace == $detail->sNamespace) {
                $this->_aPluginDetails[$sNamespace]->sCompanyLogo = $filename;
            }
        }
    }

    /**
     * get company logo
     *
     * @param unknown_type $default
     * @return void
     */
    public function getCompanyLogo ($default)
    {
        $sCompanyLogo = $default;
        foreach ($this->_aPluginDetails as $row => $detail) {
            if (trim( $detail->sCompanyLogo ) != '') {
                $sCompanyLogo = $detail->sCompanyLogo;
            }
        }
        return $sCompanyLogo;
    }

    /**
     * get setup Plugins
     *
     * @param unknown_type $default
     * @return void
     */
    public function setupPlugins ()
    {
        try {
            $iPlugins = 0;
            require_once ( 'class.serverConfiguration.php' );
            $oServerConf = & serverConf::getSingleton();
            $oServerConf->addPlugin( SYS_SYS, $this->_aPluginDetails );
            foreach ($this->_aPluginDetails as $namespace => $detail) {
                if (isset( $detail->enabled ) && $detail->enabled) {
                    if (! empty( $detail->sFilename ) && file_exists( $detail->sFilename )) {
                        if (strpos( $detail->sFilename, PATH_SEP ) !== false) {
                            $aux = explode( PATH_SEP, $detail->sFilename );
                        } else {
                            $aux = explode( chr( 92 ), $detail->sFilename );
                        }
                        $sFilename = PATH_PLUGINS . $aux[count( $aux ) - 1];
                        if (! file_exists( $sFilename )) {
                            continue;
                        }
                        require_once $sFilename;
                        if (class_exists( $detail->sClassName )) {
                            $oPlugin = new $detail->sClassName( $detail->sNamespace, $detail->sFilename );
                            $this->_aPlugins[$detail->sNamespace] = $oPlugin;
                            $iPlugins ++;
                            $oPlugin->setup();
                        }
                    }
                }
            }
            $this->eevalidate();
            return $iPlugins;
        } catch (Exception $e) {
            global $G_PUBLISH;
            $aMessage['MESSAGE'] = $e->getMessage();
            $G_PUBLISH = new Publisher();
            $G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'login/showMessage', '', $aMessage );
            G::RenderPage( 'publish' );
            die();
        }

    }

    /**
     * this function execute a Method
     *
     * @param string $sNamespace
     * @param string $methodName
     * @param object $oData
     * @return object
     */
    public function executeMethod ($sNamespace, $methodName, $oData)
    {
        $response = null;
        try {
            $details = $this->_aPluginDetails[$sNamespace];
            $pluginFolder = $details->sPluginFolder;
            $className = $details->sClassName;
            $classFile = PATH_PLUGINS . $pluginFolder . PATH_SEP . 'class.' . $pluginFolder . '.php';
            if (file_exists( $classFile )) {
                $sClassName = substr_replace( $className, "class", - 6, 6 );
                //$sClassName = str_replace ( 'plugin', 'class', $className );
                if (! class_exists( $sClassName )) {
                    require_once $classFile;
                }
                $obj = new $sClassName();
                if (! in_array( $methodName, get_class_methods( $obj ) )) {
                    throw (new Exception( "The method '$methodName' doesn't exist in class '$sClassName' " ));
                }
                $obj->sNamespace = $details->sNamespace;
                $obj->sClassName = $details->sClassName;
                $obj->sFilename = $details->sFilename;
                $obj->iVersion = $details->iVersion;
                $obj->sFriendlyName = $details->sFriendlyName;
                $obj->sPluginFolder = $details->sPluginFolder;
                $response = $obj->{$methodName}( $oData );
            }
            return $response;
        } catch (Exception $e) {
            throw ($e);
        }
    }

    /**
     * this function gets Fields For Page on Setup
     *
     * @param string $sNamespace
     * @return object
     */
    public function getFieldsForPageSetup ($sNamespace)
    {
        $oData = null;
        return $this->executeMethod( $sNamespace, 'getFieldsForPageSetup', $oData );
    }

    /**
     * this function updates Fields For Page on Setup
     *
     * @param string $sNamespace
     * @return void
     */
    public function updateFieldsForPageSetup ($sNamespace, $oData)
    {
        if (! isset( $this->_aPluginDetails[$sNamespace] )) {
            throw (new Exception( "The namespace '$sNamespace' doesn't exist in plugins folder." ));
        }

        return $this->executeMethod( $sNamespace, 'updateFieldsForPageSetup', $oData );
    }

    public function eevalidate ()
    {
        $fileL = PATH_DATA_SITE . 'license.dat';
        $fileS = PATH_DATA . 'license.dat';
        if ((file_exists( $fileL )) || (file_exists( $fileS ))) {
            //Found a License
            if (class_exists( 'pmLicenseManager' )) {
                $sSerializedFile = PATH_DATA_SITE . 'lmn.singleton';
                $pmLicenseManagerO = & pmLicenseManager::getSingleton();
                if (file_exists( $sSerializedFile )) {
                    $pmLicenseManagerO->unSerializeInstance( file_get_contents( $sSerializedFile ) );
                }
            }
        }
    }

    /**
     * Register a toolbar for dynaform editor in the singleton
     *
     * @param unknown_type $sNamespace
     * @param unknown_type $sToolbarId
     * @param unknown_type $sFilename
     */
    public function registerToolbarFile ($sNamespace, $sToolbarId, $sFilename)
    {
        $found = false;
        foreach ($this->_aToolbarFiles as $row => $detail) {
            if ($sToolbarId == $detail->sToolbarId && $sNamespace == $detail->sNamespace) {
                $found = true;
            }
        }
        if (! $found) {
            $toolbarDetail = new toolbarDetail( $sNamespace, $sToolbarId, $sFilename );
            $this->_aToolbarFiles[] = $toolbarDetail;
        }
    }

    /**
     * return all toolbar files related to a sToolbarId
     *
     * @param unknown_type $sToolbarId (NORMAL, GRID)
     */
    public function getToolbarOptions ($sToolbarId)
    {
        foreach ($this->_aToolbarFiles as $row => $detail) {
            if ($sToolbarId == $detail->sToolbarId && file_exists( $detail->sFilename )) {
                include ($detail->sFilename);
            }
        }
    }

    /**
     * Register a Case Scheduler Plugin
     */
    public function registerCaseSchedulerPlugin ($sNamespace, $sActionId, $sActionForm, $sActionSave, $sActionExecute, $sActionGetFields)
    {
        $found = false;
        foreach ($this->_aCaseSchedulerPlugin as $row => $detail) {
            if ($sActionId == $detail->sActionId && $sNamespace == $detail->sNamespace) {
                $found = true;
            }
        }

        if (! $found) {
            $this->_aCaseSchedulerPlugin[] = new caseSchedulerPlugin( $sNamespace, $sActionId, $sActionForm, $sActionSave, $sActionExecute, $sActionGetFields );
        }
    }

    /**
     * This function returns all Case Scheduler Plugins registered
     *
     * @return string
     */
    public function getCaseSchedulerPlugins ()
    {
        return $this->_aCaseSchedulerPlugin;
    }

    /**
     * Register a Task Extended property page in the singleton
     *
     * @param unknown_type $sNamespace
     * @param unknown_type $sPage
     */

    public function registerTaskExtendedProperty ($sNamespace, $sPage, $sName, $sIcon)
    {
        $found = false;
        foreach ($this->_aTaskExtendedProperties as $row => $detail) {
            if ($sPage == $detail->sPage && $sNamespace == $detail->sNamespace) {
                $detail->sName = $sName;
                $detail->sIcon = $sIcon;
                $found = true;
            }
        }
        if (! $found) {
            $taskExtendedProperty = new taskExtendedProperty( $sNamespace, $sPage, $sName, $sIcon );
            $this->_aTaskExtendedProperties[] = $taskExtendedProperty;
        }
    }

    /**
     * Register a dashboard page for cases in the singleton
     *
     * @param unknown_type $sNamespace
     * @param unknown_type $sPage
     * @param unknown_type $sName
     * @param unknown_type $sIcon
     */
    public function registerDashboardPage ($sNamespace, $sPage, $sName, $sIcon)
    {
        foreach ($this->_aDashboardPages as $row => $detail) {
            if ($sPage == $detail->sPage && $sNamespace == $detail->sNamespace) {
                $detail->sName = $sName;
                $detail->sIcon = $sIcon;
                $found = true;
            }
        }
        if (! $found) {
            $dashboardPage = new dashboardPage( $sNamespace, $sPage, $sName, $sIcon );
            $this->_aDashboardPages[] = $dashboardPage;
        }
    }

    /**
     * Register a rest service class from a plugin to be served by processmaker
     *
     * @param string $sNamespace The namespace for the plugin
     * @param string $classname The service (api) class name
     * @param string $path (optional) the class file path, if it is not set the system will try resolve the
     * file path from its classname.
     */
    public function registerRestService ($sNamespace, $classname, $path = '')
    {
        $restService = new StdClass();
        $restService->sNamespace = $sNamespace;
        $restService->classname = $classname;

        if (empty( $path )) {
            $path = PATH_PLUGINS . $restService->sNamespace . "/services/rest/$classname.php";

            if (! file_exists( $path )) {
                $path = PATH_PLUGINS . $restService->sNamespace . "/services/rest/crud/$classname.php";
            }
        }

        if (! file_exists( $path )) {
            return false;
        }

        $restService->path = $path;
        $this->_restServices[] = $restService;

        return true;
    }

    /**
     * Unregister a rest service class of a plugin
     *
     * @param string $sNamespace The namespace for the plugin
     */
    public function unregisterRestService ($sNamespace)
    {
        foreach ($this->_restServices as $i => $service) {
            if ($sNamespace == $service->sNamespace) {
                unset( $this->_restServices[$i] );
            }
        }
        // Re-index when all js were unregistered
        $this->_restServices = array_values( $this->_restServices );
    }

    public function getRegisteredRestServices ()
    {
        return $this->_restServices;
    }

    public function getRegisteredRestClassFiles ()
    {
        $restClassFiles = array ();

        foreach ($this->_restServices as $restService) {
            $restClassFiles[] = $restService->path;
        }

        return $restClassFiles;
    }

    /**
     * return all dashboard pages
     *
     * @return array
     */
    public function getDashboardPages ()
    {
        return $this->_aDashboardPages;
    }

    /**
     * return all tasl extended properties
     *
     * @return array
     */
    public function getTaskExtendedProperties ()
    {
        return $this->_aTaskExtendedProperties;
    }

    public function registerDashboard ()
    {
        // Dummy function for backwards compatibility
    }

    public function getAttributes ()
    {
        return get_object_vars( $this );
    }

    public function verifyTranslation ($namePlugin)
    {
        $language = new Language();
        $pathPluginTranslations = PATH_PLUGINS . $namePlugin . PATH_SEP . 'translations' . PATH_SEP;
        if (file_exists($pathPluginTranslations . 'translations.php')) {
            if (!file_exists($pathPluginTranslations . $namePlugin . '.' . SYS_LANG . '.po')) {
                $language->createLanguagePlugin($namePlugin, SYS_LANG);
            }
            $language->updateLanguagePlugin($namePlugin, SYS_LANG);
        }
    } 
}

