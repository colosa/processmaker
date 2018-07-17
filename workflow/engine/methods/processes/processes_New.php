<?php
/**
 * processes_New.php
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

$access = $RBAC->userCanAccess( 'PM_FACTORY' );
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

//call plugins
$oPluginRegistry = PluginRegistry::loadSingleton();
$oPluginRegistry->executeTriggers( PM_NEW_PROCESS_LIST, NULL );

$aFields['MESSAGE1'] = G::LoadTranslation( 'ID_MSG_ERROR_PRO_TITLE' );

$G_MAIN_MENU = 'processmaker';
$G_ID_MENU_SELECTED = 'PROCESSES';
$G_PUBLISH = new Publisher();
if (isset( $_DBArray['ProcessesNew'] )) {
    $G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'processes/processes_New', '', $aFields, 'processes_Save' );
} else {
    $G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'processes/processes_NewSimple', '', $aFields, 'processes_Save' );
}
G::RenderPage( 'publish', 'blank' );
