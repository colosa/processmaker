<?php
/**
 * processes_List.php
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

$G_MAIN_MENU = 'processmaker';
$G_SUB_MENU = 'process';
$G_ID_MENU_SELECTED = 'PROCESSES';
$G_ID_SUB_MENU_SELECTED = '-';

$aLabels['LANG'] = SYS_LANG;
$aLabels['PRO_EDIT'] = G::LoadTranslation( 'ID_EDIT' );
$aLabels['PRO_DELETE'] = G::LoadTranslation( 'ID_DELETE' );
$aLabels['ACTIVE'] = G::LoadTranslation( 'ID_ACTIVE' );
$aLabels['INACTIVE'] = G::LoadTranslation( 'ID_INACTIVE' );
$aLabels['CONFIRM'] = G::LoadTranslation( 'ID_MSG_CONFIRM_DELETE_PROCESS' );

G::LoadClass( 'processMap' );
$oProcess = new processMap();
$c = $oProcess->getConditionProcessList();

function activeFalse ($value)
{
    return $value == "ACTIVE" ? "ID_ACTIVE" : "ID_INACTIVE";
}

$G_PUBLISH = new Publisher();
$G_PUBLISH->AddContent( 'propeltable', 'paged-table', 'processes/processes_List', $c, $aLabels, '' );
G::RenderPage( 'publish' );
