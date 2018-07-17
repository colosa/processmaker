<?php

use ProcessMaker\Plugins\PluginRegistry;

$pluginFile   = $_GET['id'];

$oPluginRegistry = PluginRegistry::loadSingleton();

$details = $oPluginRegistry->getPluginDetails( $pluginFile );
$folder = $details->getFolder();
$xmlform = (isset($folder)) ?  $folder . '/' . $details->getSetupPage()  : '';

$G_MAIN_MENU            = 'processmaker';
$G_ID_MENU_SELECTED     = 'SETUP';
$G_SUB_MENU             = 'setup';
$G_ID_SUB_MENU_SELECTED = 'PLUGINS';
$G_PUBLISH = new Publisher;
try {
    //the setup page is a special page
    if (substr($xmlform,-4) == '.php' && file_exists(PATH_PLUGINS . $xmlform)) {
        require_once ( PATH_PLUGINS . $xmlform  );
        die;
    }
    //the setup page is a xmlform and using the default showform and saveform function to serialize data
    if (!file_exists(PATH_PLUGINS.$xmlform.'.xml')) {
        throw ( new Exception ('setup .xml file is not defined for this plugin') );
    }

    $Fields = $oPluginRegistry->getFieldsForPageSetup( $details->getNamespace() );
    $G_PUBLISH->AddContent( 'xmlform', 'xmlform', $xmlform, '',$Fields ,'pluginsSetupSave?id='.$pluginFile );
} catch (Exception $e) {
    $aMessage['MESSAGE'] = $e->getMessage();
    $G_PUBLISH->AddContent('xmlform', 'xmlform', 'login/showMessage', '', $aMessage );
}
G::RenderPage('publishBlank', 'blank');

