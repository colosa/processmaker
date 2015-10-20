<?php

/**

 *

 * processes_ImportFile.php

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



global $RBAC;

$RBAC->requirePermissions( 'PM_SETUP_ADVANCE' );



try {

    //load the variables

    G::LoadClass( 'plugin' );

    if (! isset( $_FILES['form']['error']['PLUGIN_FILENAME'] ) || $_FILES['form']['error']['PLUGIN_FILENAME'] == 1) {

        throw (new Exception( G::loadTranslation( 'ID_ERROR_UPLOADING_PLUGIN_FILENAME' ) ));

    }



    //save the file

    if ($_FILES['form']['error']['PLUGIN_FILENAME'] == 0) {

        $filename = $_FILES['form']['name']['PLUGIN_FILENAME'];

        $path = PATH_DOCUMENT . 'input' . PATH_SEP;

        $tempName = $_FILES['form']['tmp_name']['PLUGIN_FILENAME'];

        G::uploadFile( $tempName, $path, $filename );

    }



    //save the files Enterprise

    if ($_FILES['form']['error']['PLUGIN_FILENAME'] == 0) {

        $filename = $_FILES['form']['name']['PLUGIN_FILENAME'];

        $path = PATH_DOCUMENT . 'input' . PATH_SEP;

        if (strpos($filename, 'enterprise') !== false) {



            G::LoadThirdParty( 'pear/Archive', 'Tar' );

            $tar = new Archive_Tar( $path . $filename );

            $sFileName = substr( $filename, 0, strrpos( $filename, '.' ) );

            $sClassName = substr( $filename, 0, strpos( $filename, '-' ) );

            $sClassName = !empty($sClassName) ? $sClassName : $sFileName;



            $files = $tar->listContent();

            $licenseName = '';

            $listFiles = array();

            foreach ($files as $key => $val) {

                if (strpos(trim($val['filename']), 'enterprise/data/') !== false) {

                    $listFiles[] = trim($val['filename']);

                }

                if (strpos(trim($val['filename']), 'license_') !== false) {

                    $licenseName = trim($val['filename']);

                }

            }

            $tar->extractList( $listFiles,  PATH_PLUGINS . 'data');

            $tar->extractList( $licenseName, PATH_PLUGINS);



            $pluginRegistry = &PMPluginRegistry::getSingleton();

            $autoPlugins = glob(PATH_PLUGINS . "data/enterprise/data/*.tar");

            $autoPluginsA = array();



            foreach ($autoPlugins as $filePath) {

                $plName = basename($filePath);

                //if (!(in_array($plName, $def))) {

                if (strpos($plName, 'enterprise') === false) {

                    $autoPluginsA[]["sFilename"] = $plName;

                }

            }



            $aPlugins = $autoPluginsA;

            foreach ($aPlugins as $key=>$aPlugin) {

                $sClassName = substr($aPlugin["sFilename"], 0, strpos($aPlugin["sFilename"], "-"));



                $oTar = new Archive_Tar(PATH_PLUGINS . "data/enterprise/data/" . $aPlugin["sFilename"]);

                $oTar->extract(PATH_PLUGINS);



                if (!(class_exists($sClassName))) {

                    require_once (PATH_PLUGINS . $sClassName . ".php");

                }



                $pluginDetail = $pluginRegistry->getPluginDetails($sClassName . ".php");

                $pluginRegistry->installPlugin($pluginDetail->sNamespace); //error

            }



            file_put_contents(PATH_DATA_SITE . "plugin.singleton", $pluginRegistry->serializeInstance());

            $licfile = glob(PATH_PLUGINS . "*.dat");



            if ((isset($licfile[0])) && ( is_file($licfile[0]) )) {

                $licfilename = basename($licfile[0]);

                @copy($licfile[0], PATH_DATA_SITE . $licfilename);

                @unlink($licfile[0]);

            }



            require_once ('classes/model/AddonsStore.php');

            AddonsStore::checkLicenseStore();

            $licenseManager = &pmLicenseManager::getSingleton();

            AddonsStore::updateAll(false);



            $message = G::loadTranslation( 'ID_ENTERPRISE_INSTALLED') . ' ' . G::loadTranslation( 'ID_LOG_AGAIN');

            G::SendMessageText($message, "INFO");

            $licenseManager = &pmLicenseManager::getSingleton();

            die('<script type="text/javascript">parent.parent.location = "../login/login";</script>');

        }

    }



    //save the packages plugins

    if ($_FILES['form']['error']['PLUGIN_FILENAME'] == 0) {

        $filename = $_FILES['form']['name']['PLUGIN_FILENAME'];

        $path = PATH_DOCUMENT . 'input' . PATH_SEP;

        if (strpos($filename, 'plugins-') !== false) {



            G::LoadThirdParty( 'pear/Archive', 'Tar' );

            $tar = new Archive_Tar( $path . $filename );

            $sFileName = substr( $filename, 0, strrpos( $filename, '.' ) );

            $sClassName = substr( $filename, 0, strpos( $filename, '-' ) );

            $sClassName = !empty($sClassName) ? $sClassName : $sFileName;



            $files = $tar->listContent();

            $licenseName = '';

            $listFiles = array();

            foreach ($files as $key => $val) {

                if (strpos(trim($val['filename']), 'plugins/') !== false) {

                    $listFiles[] = trim($val['filename']);

                }

                if (strpos(trim($val['filename']), 'license_') !== false) {

                    $licenseName = trim($val['filename']);

                }

            }

            $tar->extractList( $listFiles,  PATH_PLUGINS . 'data');

            $tar->extractList( $licenseName, PATH_PLUGINS);



            $pluginRegistry = &PMPluginRegistry::getSingleton();

            $autoPlugins = glob(PATH_PLUGINS . "data/plugins/*.tar");

            $autoPluginsA = array();



            foreach ($autoPlugins as $filePath) {

                $plName = basename($filePath);

                //if (!(in_array($plName, $def))) {

                if (strpos($plName, 'enterprise') === false) {

                    $autoPluginsA[]["sFilename"] = $plName;

                }

            }



            $aPlugins = $autoPluginsA;

            foreach ($aPlugins as $key=>$aPlugin) {

                $sClassName = substr($aPlugin["sFilename"], 0, strpos($aPlugin["sFilename"], "-"));



                $oTar = new Archive_Tar(PATH_PLUGINS . "data/plugins/" . $aPlugin["sFilename"]);

                $oTar->extract(PATH_PLUGINS);



                if (!(class_exists($sClassName))) {

                    require_once (PATH_PLUGINS . $sClassName . ".php");

                }



                $pluginDetail = $pluginRegistry->getPluginDetails($sClassName . ".php");

                $pluginRegistry->installPlugin($pluginDetail->sNamespace); //error

            }



            file_put_contents(PATH_DATA_SITE . "plugin.singleton", $pluginRegistry->serializeInstance());



            $licfile = glob(PATH_PLUGINS . "*.dat");



            if ((isset($licfile[0])) && ( is_file($licfile[0]) )) {

                $licfilename = basename($licfile[0]);

                @copy($licfile[0], PATH_DATA_SITE . $licfilename);

                @unlink($licfile[0]);

            }



            require_once ('classes/model/AddonsStore.php');

            AddonsStore::checkLicenseStore();

            $licenseManager = &pmLicenseManager::getSingleton();

            AddonsStore::updateAll(false);



            $message = G::loadTranslation( 'ID_ENTERPRISE_INSTALLED') . ' ' . G::loadTranslation( 'ID_LOG_AGAIN');

            G::SendMessageText($message, "INFO");

            $licenseManager = &pmLicenseManager::getSingleton();

            die('<script type="text/javascript">parent.parent.location = "../login/login";</script>');

        }

    }



    if (! $_FILES['form']['type']['PLUGIN_FILENAME'] == 'application/octet-stream') {

        $pluginFilename = $_FILES['form']['type']['PLUGIN_FILENAME'];

        throw (new Exception( G::loadTranslation( 'ID_FILES_INVALID_PLUGIN_FILENAME', SYS_LANG, array ("pluginFilename" => $pluginFilename

        ) ) ));

    }



    G::LoadThirdParty( 'pear/Archive', 'Tar' );

    $tar = new Archive_Tar( $path . $filename );

    $sFileName = substr( $filename, 0, strrpos( $filename, '.' ) );

    $sClassName = substr( $filename, 0, strpos( $filename, '-' ) );

    $sClassName = !empty($sClassName) ? $sClassName : $sFileName;



    $aFiles = $tar->listContent();

    $bMainFile = false;

    $bClassFile = false;

    if (! is_array( $aFiles )) {

        throw (new Exception( G::loadTranslation( 'ID_FAILED_IMPORT_PLUGINS', SYS_LANG, array ("filename" => $filename

        ) ) ));

    }

    foreach ($aFiles as $key => $val) {

        if (trim($val['filename']) == $sClassName . '.php')

            $bMainFile = true;

        if (trim($val['filename']) == $sClassName . PATH_SEP . 'class.' . $sClassName . '.php')

            $bClassFile = true;

    }



    $partnerFlag = (defined('PARTNER_FLAG')) ? PARTNER_FLAG : false;

    if (($sClassName == 'enterprise') && ($partnerFlag)) {

        $pathFileFlag = PATH_DATA . 'flagNewLicence';

        file_put_contents($pathFileFlag, 'New Enterprise');

    }



    $oPluginRegistry = & PMPluginRegistry::getSingleton();

    $pluginFile = $sClassName . '.php';



    if ($bMainFile && $bClassFile) {

        $sAux = $sClassName . 'Plugin';

        $fVersionOld = 0.0;

        if (file_exists( PATH_PLUGINS . $pluginFile )) {

            if (! class_exists( $sAux ) && ! class_exists( $sClassName . 'plugin' )) {

                include PATH_PLUGINS . $pluginFile;

            }

            if (! class_exists( $sAux )) {

                $sAux = $sClassName . 'plugin';

            }

            $oClass = new $sAux( $sClassName );

            $fVersionOld = $oClass->iVersion;

            unset( $oClass );

        }

        $res = $tar->extract( $path );



        /*----------------------------------********---------------------------------*/



        //Check if is enterprise plugin

        $sContent = file_get_contents( $path . $pluginFile );

        $chain = preg_quote( 'extends enterprisePlugin' );

        if (strpos( $sContent, $chain )) {

            throw (new Exception( 'The plugin ' . $filename . ' is a Enterprise Edition Plugin, please install the Enterprise Plugins Manager to use this plugin.' ));

        }

        $sContent = str_ireplace( $sAux, $sAux . '_', $sContent );

        $sContent = str_ireplace( 'PATH_PLUGINS', "'" . $path . "'", $sContent );

        $sContent = preg_replace( "/\\\$oPluginRegistry\s*=\s*&\s*PMPluginRegistry::getSingleton\s*\(\s*\)\s*;/i", null, $sContent );

        $sContent = preg_replace( "/\\\$oPluginRegistry->registerPlugin\s*\(\s*[\"\']" . $sClassName . "[\"\']\s*,\s*__FILE__\s*\)\s*;/i", null, $sContent );



        //header('Content-Type: text/plain');var_dump($sClassName, $sContent);die;

        file_put_contents( $path . $pluginFile, $sContent );



        $sAux = $sAux . '_';



        include ($path . $pluginFile);



        $oClass = new $sAux( $sClassName );

        $fVersionNew = $oClass->iVersion;

        if (! isset( $oClass->iPMVersion )) {

            $oClass->iPMVersion = 0;

        }

        if ($oClass->iPMVersion > 0) {

            G::LoadClass( "system" );

            if (System::getVersion() > 0) {

                if ($oClass->iPMVersion > System::getVersion()) {

                    //throw new Exception('This plugin needs version ' . $oClass->iPMVersion . ' or higher of ProcessMaker');

                }

            }

        }

        if (! isset( $oClass->aDependences )) {

            $oClass->aDependences = null;

        }

        if (! empty( $oClass->aDependences )) {

            foreach ($oClass->aDependences as $aDependence) {

                if (file_exists( PATH_PLUGINS . $aDependence['sClassName'] . '.php' )) {

                    require_once PATH_PLUGINS . $aDependence['sClassName'] . '.php';

                    if (! $oPluginRegistry->getPluginDetails( $aDependence['sClassName'] . '.php' )) {

                        $sDependence = $aDependence['sClassName'];

                        throw new Exception( G::loadTranslation( 'ID_PLUGIN_DEPENDENCE_PLUGIN', SYS_LANG, array ("Dependence" => $sDependence

                        ) ) );

                    }

                } else {

                    $sDependence = $aDependence['sClassName'];

                    throw new Exception( G::loadTranslation( 'ID_PLUGIN_DEPENDENCE_PLUGIN', SYS_LANG, array ("Dependence" => $sDependence

                    ) ) );

                }

            }

        }

        unset( $oClass );

        if ($fVersionOld > $fVersionNew) {

            throw new Exception( G::loadTranslation( 'ID_RECENT_VERSION_PLUGIN' ) );

        }

        $res = $tar->extract( PATH_PLUGINS );

    } else {

        throw (new Exception( G::loadTranslation( 'ID_FILE_CONTAIN_CLASS_PLUGIN', SYS_LANG, array ("filename" => $filename,"className" => $sClassName

        ) ) ));

    }



    if (! file_exists( PATH_PLUGINS . $sClassName . '.php' )) {

        throw (new Exception( G::loadTranslation( 'ID_FILE_PLUGIN_NOT_EXISTS', SYS_LANG, array ("pluginFile" => $pluginFile

        ) ) ));

    }



    require_once (PATH_PLUGINS . $pluginFile);



    $oPluginRegistry->registerPlugin( $sClassName, PATH_PLUGINS . $sClassName . ".php" );

    $size = file_put_contents( PATH_DATA_SITE . "plugin.singleton", $oPluginRegistry->serializeInstance() );



    $details = $oPluginRegistry->getPluginDetails( $pluginFile );



    $oPluginRegistry->installPlugin( $details->sNamespace );



    $oPluginRegistry->setupPlugins(); //get and setup enabled plugins

    $size = file_put_contents( PATH_DATA_SITE . "plugin.singleton", $oPluginRegistry->serializeInstance() );



    $response = $oPluginRegistry->verifyTranslation( $details->sNamespace);

    G::auditLog("InstallPlugin", "Plugin Name: ".$details->sNamespace );



    //if ($response->recordsCountSuccess <= 0) {

        //throw (new Exception( 'The plugin ' . $details->sNamespace . ' couldn\'t verify any translation item. Verified Records:' . $response->recordsCountSuccess));

    //}



    G::header( "Location: pluginsMain" );

    die();

} catch (Exception $e) {

    $_SESSION['__PLUGIN_ERROR__'] = $e->getMessage();

    G::header( 'Location: pluginsMain' );

    die();

}


