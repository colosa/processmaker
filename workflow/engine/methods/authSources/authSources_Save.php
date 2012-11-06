<?php
/**
 * authSources_Save.php
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

global $RBAC;
if ($RBAC->userCanAccess( 'PM_SETUP_ADVANCE' ) != 1) {
    G::SendTemporalMessage( 'ID_USER_HAVENT_RIGHTS_PAGE', 'error', 'labels' );
    G::header( 'location: ../login/login' );
    die();
}

unset( $_POST['form']['btnSave'] );

$aCommonFields = array ('AUTH_SOURCE_UID','AUTH_SOURCE_NAME','AUTH_SOURCE_PROVIDER','AUTH_SOURCE_SERVER_NAME','AUTH_SOURCE_PORT','AUTH_SOURCE_ENABLED_TLS','AUTH_ANONYMOUS','AUTH_SOURCE_SEARCH_USER','AUTH_SOURCE_PASSWORD','AUTH_SOURCE_VERSION','AUTH_SOURCE_BASE_DN','AUTH_SOURCE_OBJECT_CLASSES','AUTH_SOURCE_ATTRIBUTES');

$aFields = $aData = array ();
foreach ($_POST['form'] as $sField => $sValue) {
    if (in_array( $sField, $aCommonFields )) {
        $aFields[$sField] = $sValue;
    } else {
        $aData[$sField] = $sValue;
    }
}
unset($aData['AUTH_SOURCE_ATTRIBUTE_IDS']);
unset($aData['AUTH_SOURCE_SHOWGRID_FLAG']);
unset($aData['AUTH_SOURCE_GRID_TEXT']);
if (!isset($aData['AUTH_SOURCE_SHOWGRID']) || $aData['AUTH_SOURCE_SHOWGRID'] == 'off') {
    unset($aData['AUTH_SOURCE_GRID_ATTRIBUTE']);
}

$aFields['AUTH_SOURCE_DATA'] = $aData;

if ($aFields['AUTH_SOURCE_UID'] == '') {
    $RBAC->createAuthSource( $aFields );
} else {
    $RBAC->updateAuthSource( $aFields );
}

G::header( 'location: authSources_List' );

