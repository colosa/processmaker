<?php
/**
 * users_AuthSourceSave.php
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

$aData = $RBAC->load( $_POST['form']['USR_UID'] );
unset( $aData['USR_ROLE'] );
if ($_POST['form']['UID_AUTH_SOURCE'] == 'MYSQL') {
    $aData['USR_AUTH_TYPE'] = 'MYSQL';
    $aData['UID_AUTH_SOURCE'] = '';
} else {
    $aFields = $RBAC->getAuthSource( $_POST['form']['UID_AUTH_SOURCE'] );
    $aData['USR_AUTH_TYPE'] = $aFields['AUTH_SOURCE_PROVIDER'];
    $aData['UID_AUTH_SOURCE'] = $_POST['form']['UID_AUTH_SOURCE'];
}
$aData['USR_AUTH_USER_DN'] = $_POST['form']['USR_AUTH_USER_DN'];
$RBAC->updateUser( $aData );

G::header( 'location: users_List' );

