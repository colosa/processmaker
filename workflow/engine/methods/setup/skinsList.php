<?php
/**
 * pluginsList.php
 *
 * ProcessMaker Open Source Edition
 * Copyright (C) 2004 - 2008 Colosa Inc.
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

if ($RBAC->userCanAccess( 'PM_SETUP' ) != 1 && $RBAC->userCanAccess( 'PM_SETUP_ADVANCE' ) != 1) {
    G::SendTemporalMessage( 'ID_USER_HAVENT_RIGHTS_PAGE', 'error', 'labels' );
    //G::header('location: ../login/login');
    die();
}

$G_MAIN_MENU = 'processmaker';
$G_SUB_MENU = 'setup';
$G_ID_MENU_SELECTED = 'SETUP';
$G_ID_SUB_MENU_SELECTED = 'CALENDAR';

$G_PUBLISH = new Publisher();

G::LoadClass( 'configuration' );
$c = new Configurations();
$configPage = $c->getConfiguration( 'skinList', 'pageSize', '', $_SESSION['USER_LOGGED'] );
$Config['pageSize'] = isset( $configPage['pageSize'] ) ? $configPage['pageSize'] : 20;

$oHeadPublisher = & headPublisher::getSingleton();
$oHeadPublisher->addExtJsScript( 'setup/skinList', false ); //adding a javascript file .js
$oHeadPublisher->addContent( 'setup/skinList' ); //adding a html file  .html.
$oHeadPublisher->assign( 'CONFIG', $Config );
$oHeadPublisher->assign( 'SYS_SKIN', SYS_SKIN );
$oHeadPublisher->assign( 'SYS_SYS', "sys".SYS_SYS );

$oHeadPublisher->assign( 'FORMATS', $c->getFormats() );

G::RenderPage( 'publish', 'extJs' );
die();

global $RBAC;
$access = $RBAC->userCanAccess( 'PM_SETUP' );
if ($access != 1) {
    switch ($access) {
        case - 1:
            G::SendTemporalMessage( 'ID_USER_HAVENT_RIGHTS_PAGE', 'error', 'labels' );
            G::header( 'location: ../login/login' );
            die();
            break;
        case - 2:
            G::SendTemporalMessage( 'ID_USER_HAVENT_RIGHTS_SYSTEM', 'error', 'labels' );
            G::header( 'location: ../login/login' );
            die();
            break;
        default:
            G::SendTemporalMessage( 'ID_USER_HAVENT_RIGHTS_PAGE', 'error', 'labels' );
            G::header( 'location: ../login/login' );
            die();
            break;
    }
}
// lets display the items
$items[] = array ('id' => 'char','title' => 'char','type' => 'char','creator' => 'char','modifiedBy' => 'char','filename' => 'char','size' => 'char','mime' => 'char'
);

//***************** Skins **************************
$aFiles = array ();
if ($handle = opendir( PATH_SKINS )) {
    while (false !== ($file = readdir( $handle ))) {
        G::pr( $file );
        $filename = substr( $file, 0, strrpos( $file, '.' ) );

        // list of no complete skins
        $aFilterSkinsList = Array ('blank','green','raw','tracker','iphone','green-submenu','extJsInitLoad','extJs'
        );

        if (! is_dir( PATH_SKINS . $file )) {
            if (! in_array( $filename, $aFilterSkinsList ) /*&& /*/ && ! strpos( $file, '.tar', 1 )) {
                if (! isset( $aFiles[$filename] ))
                    $aFiles[$filename] = 0;
                if (strpos( $file, '.php', 1 ))
                    $aFiles[$filename] += 1;
                if (strpos( $file, '.html', 1 ))
                    $aFiles[$filename] += 2;
            }
        }
    }

    closedir( $handle );

    //now walk in the array to get the .cnf file and display properties
    foreach ($aFiles as $key => $val) {
        $description = '';
        $version = '';
        if (file_exists( PATH_SKINS . $key . '.cnf' )) {
            $serial = file_get_contents( PATH_SKINS . $key . '.cnf' );
            $previousErrorRep = ini_get( "error_reporting" );
            error_reporting( E_ERROR );
            $prop = unserialize( $serial );
            error_reporting( $previousErrorRep );
            if (! is_object( $prop )) {
                @unlink( PATH_SKINS . $key . '.cnf' );
            }
            if (isset( $prop ) && isset( $prop->description ))
                $description = $prop->description;
            if (isset( $prop ) && isset( $prop->version ))
                $version = $prop->version;
        }

        $linkPackValue = G::LoadTranslation( 'ID_EXPORT' );
        $link = 'skinsExport?id=' . $key;
        $items[] = array ('id' => count( $items ),'name' => $key,'filename' => $key,'description' => $description,'version' => $version,'url' => $link,'linkPackValue' => $linkPackValue
        );
    }
    $folders['items'] = $items;
}

$_DBArray['plugins'] = $items;
$_SESSION['_DBArray'] = $_DBArray;

G::LoadClass( 'ArrayPeer' );
$c = new Criteria( 'dbarray' );
$c->setDBArrayTable( 'plugins' );
$c->addAscendingOrderByColumn( 'id' );

$G_MAIN_MENU = 'processmaker';
$G_ID_MENU_SELECTED = 'SETUP';
$G_SUB_MENU = 'setup';
$G_ID_SUB_MENU_SELECTED = 'SKINS';

$G_PUBLISH = new Publisher();

$G_PUBLISH->AddContent( 'propeltable', 'paged-table', 'setup/skinsList', $c );

G::RenderPage( 'publishBlank', 'blank' );

