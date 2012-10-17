<?php
/**
 * rolesUsersPermission.php
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
switch ($RBAC->userCanAccess( 'PM_USERS' )) {
    case - 2:
        G::SendTemporalMessage( 'ID_USER_HAVENT_RIGHTS_SYSTEM', 'error', 'labels' );
        G::header( 'location: ../login/login' );
        die();
        break;
    case - 1:
        G::SendTemporalMessage( 'ID_USER_HAVENT_RIGHTS_PAGE', 'error', 'labels' );
        G::header( 'location: ../login/login' );
        die();
        break;
    case - 3:
        G::SendTemporalMessage( 'ID_USER_HAVENT_RIGHTS_PAGE', 'error', 'labels' );
        G::header( 'location: ../login/login' );
        die();
        break;
}

$G_MAIN_MENU = 'processmaker';
$G_SUB_MENU = 'users';
$G_ID_MENU_SELECTED = 'USERS';
$G_ID_SUB_MENU_SELECTED = 'ROLES';

$G_PUBLISH = new Publisher();

$oHeadPublisher = & headPublisher::getSingleton();

$oHeadPublisher->addExtJsScript( 'roles/rolesUsersPermission', false ); //adding a javascript file .js
$oHeadPublisher->addContent( 'roles/rolesUsersPermission' ); //adding a html file  .html.


$roles = Array ();
$roles['ROL_UID'] = $_GET['rUID'];
$roles['ROL_CODE'] = $RBAC->getRoleCode( $_GET['rUID'] );
$roles['CURRENT_TAB'] = ($_GET['tab'] == 'permissions') ? 1 : 0;

$oHeadPublisher->assign( 'ROLES', $roles );
$oHeadPublisher->assign( 'permissionsAdmin', $RBAC->loadPermissionAdmin() );
G::RenderPage( 'publish', 'extJs' );

