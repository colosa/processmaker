<?php
/**
 * pluginsSetup.php
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

use ProcessMaker\Plugins\PluginRegistry;

$pluginFile = $_GET['id'];

$oPluginRegistry = PluginRegistry::loadSingleton();

$details = $oPluginRegistry->getPluginDetails( $pluginFile );
$folder = $details->getFolder();
$xmlform = isset($folder) ? $folder . '/' . $details->getSetupPage() : '';

$G_MAIN_MENU = 'processmaker';
$G_ID_MENU_SELECTED = 'SETUP';
$G_SUB_MENU = 'setup';
$G_ID_SUB_MENU_SELECTED = 'PLUGINS';
$G_PUBLISH = new Publisher();
try {
    //the setup page is a special page
    if (substr( $xmlform, - 4 ) == '.php' && file_exists( PATH_PLUGINS . $xmlform )) {
        require_once (PATH_PLUGINS . $xmlform);
        die();
    }

    //the setup page is a xmlform and using the default showform and saveform function to serialize data
    if (! file_exists( PATH_PLUGINS . $xmlform . '.xml' ))
        throw (new Exception( 'setup .xml file is not defined for this plugin' ));

    $Fields = $oPluginRegistry->getFieldsForPageSetup( $details->getNamespace() );
    $G_PUBLISH->AddContent( 'xmlform', 'xmlform', $xmlform, '', $Fields, 'pluginsSetupSave?id=' . $pluginFile );
} catch (Exception $e) {
    $aMessage['MESSAGE'] = $e->getMessage();
    $G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'login/showMessage', '', $aMessage );
}
G::RenderPage( 'publishBlank', 'blank' );

