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

G::LoadClass( "configuration" );

$conf = new Configurations();

$oHeadPublisher = &headPublisher::getSingleton();
$oHeadPublisher->addExtJsScript( "cases/main", false ); //Adding a javascript file .js
$oHeadPublisher->addContent( "cases/main" ); //Adding a html file  .html.


$keyMem = "USER_PREFERENCES" . $_SESSION["USER_LOGGED"];
$memcache = &PMmemcached::getSingleton( SYS_SYS );

if (($arrayConfig = $memcache->get( $keyMem )) === false) {
    $conf->loadConfig( $x, "USER_PREFERENCES", "", "", $_SESSION["USER_LOGGED"], "" );
    $arrayConfig = $conf->aConfig;
    $memcache->set( $keyMem, $arrayConfig, PMmemcached::ONE_HOUR );
}

$confDefaultOption = "";

if (isset( $arrayConfig["DEFAULT_CASES_MENU"] )) { //this user has a configuration record
    $confDefaultOption = $arrayConfig["DEFAULT_CASES_MENU"];

    global $G_TMP_MENU;

    $oMenu = new Menu();
    $oMenu->load( "cases" );
    $defaultOption = "";

    foreach ($oMenu->Id as $i => $id) {
        if ($id == $confDefaultOption) {
            $defaultOption = $oMenu->Options[$i];
            break;
        }
    }

    $defaultOption = ($defaultOption != "") ? $defaultOption : "casesListExtJs";
} else {
    $defaultOption = "casesListExtJs";
    $confDefaultOption = "CASES_INBOX";
}

if (isset( $_GET["id"] ) && isset( $_GET["id"] )) {
    $defaultOption = "../cases/open?APP_UID=" . $_GET["id"] . "&DEL_INDEX=" . $_GET["i"];

    if (isset( $_GET["a"] )) {
        $defaultOption .= "&action=" . $_GET["a"];
    }
}

$oServerConf = & serverConf::getSingleton();
if ($oServerConf->isRtl( SYS_LANG )) {
    $regionTreePanel = 'east';
    $regionDebug = 'west';
} else {
    $regionTreePanel = 'west';
    $regionDebug = 'east';
}
$oHeadPublisher->assign( 'regionTreePanel', $regionTreePanel );
$oHeadPublisher->assign( 'regionDebug', $regionDebug );
$oHeadPublisher->assign( "defaultOption", $defaultOption ); //User menu permissions
$oHeadPublisher->assign( "_nodeId", isset( $confDefaultOption ) ? $confDefaultOption : "PM_USERS" ); //User menu permissions
$oHeadPublisher->assign( "FORMATS", $conf->getFormats() );

$_SESSION["current_ux"] = "NORMAL";

G::RenderPage( "publish", "extJs" );

