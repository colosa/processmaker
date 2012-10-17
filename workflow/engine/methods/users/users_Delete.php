<?php
/**
 * users_Delete.php
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
try {
    global $RBAC;
    switch ($RBAC->userCanAccess( 'PM_FACTORY' )) {
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
    }
    if ($_GET['USR_UID'] == '00000000000000000000000000000001') {
        G::SendTemporalMessage( 'ID_CANNOT_CHANGE_STATUS_ADMIN_USER', 'error', 'usersLabels' );
        G::header( 'location: ' . $_SERVER['HTTP_REFERER'] );
        die();
    }
    /*$RBAC->removeUser($_GET['USR_UID']);
  require_once 'classes/model/Users.php';
  $oUser = new Users();
  $oUser->remove($_GET['USR_UID']);*/

    //print_r($_GET['USR_UID']); die
    G::LoadClass( 'tasks' );
    $oTasks = new Tasks();
    $oTasks->ofToAssignUserOfAllTasks( $_GET['USR_UID'] );
    G::LoadClass( 'groups' );
    $oGroups = new Groups();
    $oGroups->removeUserOfAllGroups( $_GET['USR_UID'] );
    $RBAC->changeUserStatus( $_GET['USR_UID'], 'CLOSED' );
    $_GET['USR_USERNAME'] = '';
    $RBAC->updateUser( array ('USR_UID' => $_GET['USR_UID'],'USR_USERNAME' => $_GET['USR_USERNAME']
    ), '' );

    require_once 'classes/model/Users.php';
    $oUser = new Users();
    $aFields = $oUser->load( $_GET['USR_UID'] );
    $aFields['USR_STATUS'] = 'CLOSED';
    $aFields['USR_USERNAME'] = '';
    $oUser->update( $aFields );
    G::header( 'location: users_List' );
} catch (Exception $oException) {
    die( $oException->getMessage() );
}

