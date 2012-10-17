<?php
/**
 * pluginsChange.php
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

// lets display the items
$pluginFile = $_GET['id'];
$pluginStatus = $_GET['status'];

$items = array ();
G::LoadClass( 'plugin' );
//here we are enabling or disabling the plugin and all related options registered.


$oPluginRegistry = & PMPluginRegistry::getSingleton();

if ($handle = opendir( PATH_PLUGINS )) {
    while (false !== ($file = readdir( $handle ))) {
        if (strpos( $file, '.php', 1 ) && $file == $pluginFile) {

            if ($pluginStatus == '1') {
                //print "change to disable";
                $details = $oPluginRegistry->getPluginDetails( $pluginFile );
                $oPluginRegistry->disablePlugin( $details->sNamespace );
                $size = file_put_contents( PATH_DATA_SITE . 'plugin.singleton', $oPluginRegistry->serializeInstance() );
                print "size saved : $size  <br>";
            } else {
                //print "change to ENABLED";
                require_once (PATH_PLUGINS . $pluginFile);
                $details = $oPluginRegistry->getPluginDetails( $pluginFile );
                $oPluginRegistry->enablePlugin( $details->sNamespace );
                $oPluginRegistry->setupPlugins(); //get and setup enabled plugins
                $size = file_put_contents( PATH_DATA_SITE . 'plugin.singleton', $oPluginRegistry->serializeInstance() );
                print "size saved : $size  <br>";
            }
        }
    }
    closedir( $handle );
}

  //$oPluginRegistry->showArrays();
  //G::Header('location: pluginsList');

