<?php

/**
 * autoinstallProcess.php
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
 */
G::LoadClass( 'Installer' );
$inst = new Installer();

G::LoadClass( 'processes' );
$oProcess = new Processes();

//Get Available autoinstall process
$availablePlugins = $inst->getDirectoryFiles( PATH_OUTTRUNK . "autoinstall", "tar" );

rsort( $availablePlugins );

$path = PATH_OUTTRUNK . "autoinstall" . PATH_SEP;
$message = "";
foreach ($availablePlugins as $filename) {

    G::LoadThirdParty( 'pear/Archive', 'Tar' );
    $tar = new Archive_Tar( $path . $filename );
    $sFileName = substr( $filename, 0, strrpos( $filename, '.' ) );
    $sClassName = substr( $filename, 0, strpos( $filename, '-' ) );

    $aFiles = $tar->listContent();
    $bMainFile = false;
    $bClassFile = false;
    foreach ($aFiles as $key => $val) {
        if ($val['filename'] == $sClassName . '.php') {
            $bMainFile = true;
        }
        if ($val['filename'] == $sClassName . PATH_SEP . 'class.' . $sClassName . '.php') {
            $bClassFile = true;
        }
    }
    if ($bMainFile && $bClassFile) {
        $res = $tar->extract( PATH_PLUGINS );
    } else {
        throw (new Exception( "The file $filename doesn't contain class: $sClassName " ));
    }

    //print "change to ENABLED";
    $oPluginRegistry = & PMPluginRegistry::getSingleton();

    $pluginFile = $sClassName . '.php';
    if (! file_exists( PATH_PLUGINS . $sClassName . '.php' )) {
        throw (new Exception( "File '$pluginFile' doesn't exist " ));
    }

    require_once (PATH_PLUGINS . $pluginFile);
    $details = $oPluginRegistry->getPluginDetails( $pluginFile );

    $oPluginRegistry->installPlugin( $details->sNamespace );
    $oPluginRegistry->enablePlugin( $details->sNamespace );
    $oPluginRegistry->setupPlugins(); //get and setup enabled plugins
    $size = file_put_contents( PATH_DATA_SITE . 'plugin.singleton', $oPluginRegistry->serializeInstance() );

    $message .= "$filename - OK<br>";

}

echo $message;

