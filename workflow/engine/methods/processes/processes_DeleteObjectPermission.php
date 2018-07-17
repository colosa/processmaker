<?php
/**
 * processes_Save.php
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
$access = $RBAC->userCanAccess('PM_FACTORY');
if ($access != 1) {
    switch ($access) {
        case -1:
            G::SendTemporalMessage('ID_USER_HAVENT_RIGHTS_PAGE', 'error', 'labels');
            G::header('location: ../login/login');
            die();
            break;
        case -2:
            G::SendTemporalMessage('ID_USER_HAVENT_RIGHTS_SYSTEM', 'error', 'labels');
            G::header('location: ../login/login');
            die();
            break;
        default:
            G::SendTemporalMessage('ID_USER_HAVENT_RIGHTS_PAGE', 'error', 'labels');
            G::header('location: ../login/login');
            die();
            break;
    }
}
try {
    require_once 'classes/model/ObjectPermission.php';
    $oOP = new ObjectPermission();
    $oOP = ObjectPermissionPeer::retrieveByPK($_GET['OP_UID']);
    $sProcessUID = $oOP->getProUid();
    $oOP->delete();

    $result = new stdclass();
    $result->success = true;
    $result->msg = G::LoadTranslation('ID_REPORTTABLE_REMOVED');

    $oProcessMap = new ProcessMap();
    $oProcessMap->getObjectsPermissionsCriteria($sProcessUID);
} catch (Exception $e) {
    $result = new stdclass();
    $result->success = false;
    $result->msg = $e->getMessage();
}
print G::json_encode($result);

$infoProcess = new Processes();
$resultProcess = $infoProcess->getProcessRow($sProcessUID);
G::auditLog('DeletePermissions',
    'Delete Permissions (' . $_GET['OP_UID'] . ') in Process "' . $resultProcess['PRO_TITLE'] . '"');
