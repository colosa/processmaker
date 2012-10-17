<?php
/**
 * pluginsList.php
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

$RBAC->requirePermissions( 'PM_SETUP_ADVANCE' );

// lets display the items
//$items = array ( 'id' => 'char', 'title' => 'char', 'type' => 'char', 'creator' => 'char' , 'modifiedBy' => 'char', 'filename' => 'char', 'size' => 'char', 'mime' => 'char');


$items = Array ();
//***************** Plugins **************************
G::LoadClass( 'plugin' );
//here we are loading all plugins registered
//krumo ($items); die;
$aPluginsPP = array ();
if (is_file( PATH_PLUGINS . 'enterprise/data/data' )) {
    $aPlugins = unserialize( trim( file_get_contents( PATH_PLUGINS . 'enterprise/data/data' ) ) );
    foreach ($aPlugins as $aPlugin) {
        $aPluginsPP[] = substr( $aPlugin['sFilename'], 0, strpos( $aPlugin['sFilename'], '-' ) ) . '.php';
    }
}
$oPluginRegistry = & PMPluginRegistry::getSingleton();
if ($handle = opendir( PATH_PLUGINS )) {
    while (false !== ($file = readdir( $handle ))) {

        if (in_array( $file, $aPluginsPP )) {
            continue;
        }
        if (strpos( $file, '.php', 1 ) && is_file( PATH_PLUGINS . $file )) {
            include_once (PATH_PLUGINS . $file);
            $pluginDetail = $oPluginRegistry->getPluginDetails( $file );
            //print_R ($pluginDetail );
            //die;
            //$status = $pluginDetail->enabled ? 'Enabled' : 'Disabled';
            if ($pluginDetail == null)
                continue; //When for some reason we gen NULL plugin
            $status_label = $pluginDetail->enabled ? G::LoadTranslation( 'ID_ENABLED' ) : G::LoadTranslation( 'ID_DISABLED' );
            $status = $pluginDetail->enabled ? 1 : 0;
            if (isset( $pluginDetail->aWorkspaces )) {
                if (! in_array( SYS_SYS, $pluginDetail->aWorkspaces ))
                    continue;
            }
            $linkEditValue = $pluginDetail->sSetupPage != '' && $pluginDetail->enabled ? G::LoadTranslation( 'ID_SETUP' ) : ' ';
            //g::pr($pluginDetail->sSetupPage);
            $setup = $pluginDetail->sSetupPage != '' && $pluginDetail->enabled ? '1' : '0';

            $link = 'pluginsChange?id=' . $file . '&status=' . $pluginDetail->enabled;
            $linkEdit = 'pluginsSetup?id=' . $file;
            $pluginName = $pluginDetail->sFriendlyName;
            $pluginId = $pluginDetail->sNamespace;
            $removePluginMsg = str_replace( "\r\n", "<br>", G::LoadTranslation( 'ID_MSG_REMOVE_PLUGIN' ) );
            $linkRemove = 'javascript:showMessage(\'' . $removePluginMsg . '<br>' . $pluginName . ' \',\'' . $pluginId . '\')';
            //         $linkRemove = 'pluginsRemove?id='.$pluginId.'.php&status=1';
            if (isset( $pluginDetail )) {
                if (! $pluginDetail->bPrivate) {
                    $items[] = array ('id' => (count( $items ) + 1),'namespace' => $pluginDetail->sNamespace,'title' => $pluginDetail->sFriendlyName . "\n(" . $pluginDetail->sNamespace . '.php)','className' => $pluginDetail->sNamespace,'description' => $pluginDetail->sDescription,'version' => $pluginDetail->iVersion,'setupPage' => $pluginDetail->sSetupPage,'status_label' => $status_label,'status' => $status,'setup' => $setup,

                    'sFile' => $file,'sStatusFile' => $pluginDetail->enabled
                    );
                }
            }

        }
    }
    closedir( $handle );
}

$folders['items'] = $items;
//g::pr($items);
echo G::json_encode( $items );
die();
$_DBArray['plugins'] = $items;
$_SESSION['_DBArray'] = $_DBArray;

G::LoadClass( 'ArrayPeer' );
$c = new Criteria( 'dbarray' );
$c->setDBArrayTable( 'plugins' );
//$c->addAscendingOrderByColumn ('id');


$G_MAIN_MENU = 'processmaker';
$G_ID_MENU_SELECTED = 'SETUP';
$G_SUB_MENU = 'setup';
$G_ID_SUB_MENU_SELECTED = 'PLUGINS';

$G_PUBLISH = new Publisher();

$oHeadPublisher = & headPublisher::getSingleton();
$oHeadPublisher->addScriptFile( '/jscore/setup/pluginList.js' );

$G_PUBLISH->AddContent( 'propeltable', 'paged-table', 'setup/pluginList', $c );
G::RenderPage( 'publishBlank', 'blank' );

