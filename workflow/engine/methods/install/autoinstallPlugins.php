<?php

use ProcessMaker\Core\Installer;
use ProcessMaker\Plugins\PluginRegistry;

$inst = new Installer();

$oProcess = new Processes();

//Get Available autoinstall process
$availablePlugins = $inst->getDirectoryFiles( PATH_OUTTRUNK . "autoinstall", "tar" );

rsort( $availablePlugins );

$path = PATH_OUTTRUNK . "autoinstall" . PATH_SEP;
$message = "";
foreach ($availablePlugins as $filename) {


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
    $oPluginRegistry = PluginRegistry::loadSingleton();

    $pluginFile = $sClassName . '.php';
    if (! file_exists( PATH_PLUGINS . $sClassName . '.php' )) {
        throw (new Exception( "File '$pluginFile' doesn't exist " ));
    }

    require_once (PATH_PLUGINS . $pluginFile);
    $details = $oPluginRegistry->getPluginDetails( $pluginFile );

    $oPluginRegistry->installPlugin($details->getNamespace());
    $oPluginRegistry->enablePlugin($details->getNamespace());
    $oPluginRegistry->setupPlugins(); //get and setup enabled plugins
    $oPluginRegistry->savePlugin($details->getNamespace());

    $message .= "$filename - OK<br>";

}

echo $message;

