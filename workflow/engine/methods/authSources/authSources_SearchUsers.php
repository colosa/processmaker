<?php
/**
 * authSources_SearchUsers.php
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
if ($RBAC->userCanAccess( 'PM_SETUP_ADVANCE' ) != 1) {
    G::SendTemporalMessage( 'ID_USER_HAVENT_RIGHTS_PAGE', 'error', 'labels' );
    G::header( 'location: ../login/login' );
    die();
}

$G_MAIN_MENU = 'processmaker';
$G_SUB_MENU = 'users';
$G_ID_MENU_SELECTED = 'USERS';
$G_ID_SUB_MENU_SELECTED = 'AUTH_SOURCES';

$G_PUBLISH = new Publisher();
$fields = $RBAC->getAuthSource( $_GET['sUID'] );
if (file_exists( PATH_PLUGINS . $fields['AUTH_SOURCE_PROVIDER'] . PATH_SEP . $fields['AUTH_SOURCE_PROVIDER'] . 'Edit.xml' )) {
    $pluginEnabled = 0;
    
    if (file_exists(PATH_PLUGINS . $fields["AUTH_SOURCE_PROVIDER"] . ".php")) {
        $pluginRegistry = &PMPluginRegistry::getSingleton();
        $pluginDetail = $pluginRegistry->getPluginDetails($fields["AUTH_SOURCE_PROVIDER"] . ".php");
    
        if ($pluginDetail && $pluginDetail->enabled) {
            $pluginEnabled = 1;
        }
    }

    if ($pluginEnabled == 0) {
       $G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'login/showMessage', '', array ('MESSAGE' => G::LoadTranslation( 'ID_AUTH_SOURCE_MISSING' ) ) );
       G::RenderPage( 'publish', 'blank' );
    } else {
        G::LoadClass('configuration');
        $c = new Configurations();
        $configPage = $c->getConfiguration('additionalTablesList', 'pageSize','',$_SESSION['USER_LOGGED']);
        $Config['pageSize'] = isset($configPage['pageSize']) ? $configPage['pageSize'] : 20;

        $oHeadPublisher = & headPublisher::getSingleton ();

        $oHeadPublisher->assign("FORMATS", $c->getFormats());
        $oHeadPublisher->assign("CONFIG", $Config);

        if (file_exists(PATH_PLUGINS . $fields["AUTH_SOURCE_PROVIDER"] . PATH_SEP . $fields["AUTH_SOURCE_PROVIDER"] . 'Flag')) {
            $oHeadPublisher = & headPublisher::getSingleton ();

            $oHeadPublisher->assign("Fields", $fields);
            $oHeadPublisher->addExtJsScript (PATH_PLUGINS . $fields["AUTH_SOURCE_PROVIDER"] . PATH_SEP . 'js' . PATH_SEP . 'library', false, true );
            $oHeadPublisher->addExtJsScript (PATH_PLUGINS . $fields["AUTH_SOURCE_PROVIDER"] . PATH_SEP . 'js' . PATH_SEP . 'ldapAdvancedSearch', false, true );
            G::RenderPage ('publish', 'extJs');
            die();
        }
    }
}

$G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'authSources/authSources_SearchUsers', '', array ('AUTH_SOURCE_UID' => $_GET['sUID']), '../authSources/authSources_ImportUsers' );
G::RenderPage( 'publish', 'blank' );

