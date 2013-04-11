<?php
/**
 * data_rolesPermissions.php
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

$ROL_UID = $_GET['rUID'];
$TYPE_DATA = $_GET["type"];

global $RBAC;

$filter = (isset( $_REQUEST['textFilter'] )) ? $_REQUEST['textFilter'] : '';

//BUG 7554: erik/ hook for existents environments that have not PM_CANCELCASE
if ($RBAC->permissionsObj->loadByCode( 'PM_CANCELCASE' ) === false) {
    $RBAC->permissionsObj->create( array ('PER_CODE' => 'PM_CANCELCASE','PER_CREATE_DATE' => date( 'Y-m-d H:i:s' )
    ) );
}

if ($TYPE_DATA == 'list')
    $oDataset = $RBAC->getRolePermissions( $ROL_UID, $filter, 1 );
if ($TYPE_DATA == 'show')
    $oDataset = $RBAC->getAllPermissions( $ROL_UID, $RBAC->sSystem, $filter, 1 );

$rows = Array ();
$per = new RolesPermissions();
while ($oDataset->next()) {
    $row = $oDataset->getRow();
    $per->setPerUid($row['PER_UID']);
    $row['PER_NAME'] = $per->getPermissionName();
    $rows[] = $row;
}

echo '{permissions: ' . G::json_encode( $rows ) . '}';

