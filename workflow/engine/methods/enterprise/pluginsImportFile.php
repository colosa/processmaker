<?php

/**
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
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * For more information, contact Colosa Inc, 2566 Le Jeune Rd.,
 * Coral Gables, FL, 33134, USA, or email info@colosa.com.
 *
 */

global $RBAC;

use ProcessMaker\Plugins\PluginRegistry;

$RBAC->requirePermissions("PM_SETUP_ADVANCE");
require_once PATH_CORE . 'methods' . PATH_SEP . 'enterprise' . PATH_SEP . 'enterprise.php';

$response = array();
$status = 1;

try {

    if (!isset($_FILES["form"]["error"]["PLUGIN_FILENAME"]) || $_FILES["form"]["error"]["PLUGIN_FILENAME"] == 1) {
        $str = "There was an error uploading the file, probably the file size if greater than upload_max_filesize parameter in php.ini, please check this parameter and try again.";
        throw (new Exception($str));
    }

    //save the file
    if ($_FILES["form"]["error"]["PLUGIN_FILENAME"] == 0) {
        $filename = $_FILES["form"]["name"]["PLUGIN_FILENAME"];
        $path     = PATH_DOCUMENT . "input" . PATH_SEP ;
        $tempName = $_FILES["form"]["tmp_name"]["PLUGIN_FILENAME"];
        G::uploadFile($tempName, $path, $filename );
    }

    if (!$_FILES["form"]["type"]["PLUGIN_FILENAME"] == "application/octet-stream") {
        $str = "the uploaded files are invalid, expected \"application/octect-stream\" mime type file (".  $_FILES["form"]["type"]["PLUGIN_FILENAME"] . ")";
        throw (new Exception($str));
    }


    $tar = new Archive_Tar($path. $filename);
    $sFileName  = substr($filename, 0, strrpos($filename, "."));
    $sClassName = substr($filename, 0, strpos($filename, "-"));

    $aFiles = $tar->listContent();
    $bMainFile = false;
    $bClassFile = false;

    if (is_array($aFiles)) {
        foreach ($aFiles as $key => $val) {
            if ($val["filename"] == $sClassName . ".php") {
                $bMainFile = true;
            }
            if ($val["filename"] == $sClassName . PATH_SEP . "class." . $sClassName . ".php") {
                $bClassFile = true;
            }
        }
    } else {
        $str = "Failed to import the file default by doesn't a plugin or invalid file.";
        throw (new Exception($str));
    }

    $oPluginRegistry = PluginRegistry::loadSingleton();
    $pluginFile = $sClassName . '.php';

    if ($bMainFile && $bClassFile) {
        $sAux = $sClassName . 'Plugin';
        $fVersionOld = 0.0;
        if (file_exists(PATH_PLUGINS . $pluginFile)) {
            if (!class_exists($sAux) && !class_exists($sClassName . 'plugin')) {
                include PATH_PLUGINS . $pluginFile;
            }
            if (!class_exists($sAux)) {
                $sAux = $sClassName . 'plugin';
            }
            $oClass = new $sAux($sClassName);
            $fVersionOld = $oClass->iVersion;
            unset($oClass);
        }

        $res = $tar->extract($path);

        //Verify if not is Enterprise Plugin
        if (!$oPluginRegistry->isEnterprisePlugin($sClassName, $path)) {
            throw new Exception(G::LoadTranslation('ID_EEPLUGIN_IMPORT_PLUGIN_NOT_IS_ENTERPRISE', [$filename]));
        }

        $res = $tar->extract(PATH_PLUGINS);
    } else {
        $str = "The file $filename doesn't contain class: $sClassName ";
        throw (new Exception($str));
    }

    if (! file_exists(PATH_PLUGINS . $sClassName . '.php')) {
        $str = "File '$pluginFile' doesn't exists ";
        throw (new Exception($str));
    }

    require_once (PATH_PLUGINS . $pluginFile);

    $oPluginRegistry->registerPlugin($sClassName, PATH_PLUGINS . $sClassName . ".php");

    $details = $oPluginRegistry->getPluginDetails($pluginFile);

    $oPluginRegistry->installPlugin($details->getNamespace());
    $oPluginRegistry->setupPlugins(); //get and setup enabled plugins
    $oPluginRegistry->savePlugin($details->getNamespace());

    //G::header("Location: pluginsList");
    //die;

    $response["success"] = true;
} catch (Exception $e) {
    $response["message"] = $e->getMessage();
    $status = 0;
}

if ($status == 0) {
    $response["success"] = false;
}

G::outRes( G::json_encode($response) );

