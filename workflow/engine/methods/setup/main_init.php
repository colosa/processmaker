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

$RBAC->requirePermissions( "PM_SETUP", "PM_USERS" );

///////
$adminSelected = null;

if (isset( $_REQUEST["s"] )) {
    $adminSelected = $_REQUEST["s"];
} else {
    if (isset( $_SESSION["ADMIN_SELECTED"] )) {
        $adminSelected = $_SESSION["ADMIN_SELECTED"];
    }
}

///////
$oHeadPublisher = &headPublisher::getSingleton();

global $G_TMP_MENU;

$oMenu = new Menu();
$oMenu->load( "setup" );
$items = array ();

$menuTypes = array_unique( $oMenu->Types );
foreach ($menuTypes as $i => $v) {
    if ($v == "admToolsContent") {
        unset( $menuTypes[$i] );
        break;
    }
}

//sort($menuTypes);


$tabItems = array ();
$i = 0;

foreach ($menuTypes as $menuType) {
    $tabItems[$i] = new stdclass();
    $tabItems[$i]->id = $menuType;
    $LABEL_TRANSLATION = G::LoadTranslation( "ID_" . strtoupper( $menuType ) );

    if (substr( $LABEL_TRANSLATION, 0, 2 ) !== "**") {
        $title = $LABEL_TRANSLATION;
    } else {
        $title = str_replace( "_", " ", ucwords( $menuType ) );
    }

    $tabItems[$i]->title = $title;
    $i ++;
}

///////
$tabActive = "";

if ($adminSelected != null) {
    foreach ($oMenu->Options as $i => $option) {
        if ($oMenu->Id[$i] == $adminSelected) {
            $tabActive = (in_array( $oMenu->Types[$i], array ("","admToolsContent"
            ) )) ? "plugins" : $oMenu->Types[$i];
            break;
        }
    }
}

///////
$oHeadPublisher->addExtJsScript( "setup/main", true ); //adding a javascript file .js
$oHeadPublisher->addContent( "setup/main" ); //adding a html file .html.
$oHeadPublisher->assign( "tabActive", $tabActive );
$oHeadPublisher->assign( "tabItems", $tabItems );
$oHeadPublisher->assign( "_item_selected", (($adminSelected != null) ? $adminSelected : "") );

G::RenderPage( "publish", "extJs" );

//this patch enables the load of the plugin list panel inside de main admin panel iframe
if (isset( $_GET["action"] ) && $_GET["action"] == "pluginsList") {
    echo "
  <script type=\"text/javascript\">
  document.getElementById(\"setup-frame\").src = \"pluginsList\";
  </script>
  ";
}

