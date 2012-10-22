<?php
/**
 * main.php Cases List main processor
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

global $RBAC;
$RBAC->requirePermissions( 'PM_SETUP_ADVANCE' );
$G_PUBLISH = new Publisher();

G::LoadClass( 'configuration' );
$c = new Configurations();
$configPage = $c->getConfiguration( 'additionalTablesList', 'pageSize', '', $_SESSION['USER_LOGGED'] );
$Config['pageSize'] = isset( $configPage['pageSize'] ) ? $configPage['pageSize'] : 20;

$oHeadPublisher = & headPublisher::getSingleton();

$repTabPluginPermissions = false;
global $G_TMP_MENU;
$oMenu = new Menu();
$oMenu->load( 'setup' );

$simpleREportsPlugin = false;
foreach ($oMenu->Options as $i => $option) {
    if ($oMenu->Types[$i] == 'private' && $oMenu->Id[$i] == 'PLUGIN_REPTAB_PERMISSIONS') {
        $simpleREportsPlugin = array ();
        $simpleREportsPlugin['label'] = $oMenu->Labels[$i];
        $simpleREportsPlugin['fn'] = $oMenu->Options[$i];
        break;
    }
}

$oHeadPublisher->assign( '_PLUGIN_SIMPLEREPORTS', $simpleREportsPlugin );

$oHeadPublisher->addExtJsScript( 'reportTables/main', true ); //adding a javascript file .js
$oHeadPublisher->addContent( 'reportTables/main' ); //adding a html file  .html.
$oHeadPublisher->assign( 'FORMATS', $c->getFormats() );
$oHeadPublisher->assign( 'CONFIG', $Config );
$oHeadPublisher->assign( 'PRO_UID', isset( $_GET['PRO_UID'] ) ? $_GET['PRO_UID'] : false );
G::RenderPage( 'publish', 'extJs' );

