<?php

/**
 * upgrade.php
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
/*
global $RBAC;
switch ($RBAC->userCanAccess('PM_FACTORY')) {
    case - 2:
        G::SendTemporalMessage('ID_USER_HAVENT_RIGHTS_SYSTEM', 'error', 'labels');
        G::header('location: ../login/login');
        die;
        break;
    case - 1:
        G::SendTemporalMessage('ID_USER_HAVENT_RIGHTS_PAGE', 'error', 'labels');
        G::header('location: ../login/login');
        die;
        break;
}
*/

/*$G_MAIN_MENU = 'processmaker';
$G_SUB_MENU = 'processes';
$G_ID_MENU_SELECTED = 'PROCESSES';
$G_ID_SUB_MENU_SELECTED = 'DB_CONNECTIONS';
*/

$G_PUBLISH = new Publisher();

$oProcess = new ProcessMap();
$oCriteria = $oProcess->getConditionProcessList();
if (ProcessPeer::doCount( $oCriteria ) > 0) {
    $aProcesses = array ();
    $aProcesses[] = array ('PRO_UID' => 'char','PRO_TITLE' => 'char' );
    $oDataset = StepPeer::doSelectRS( $oCriteria );
    $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
    $oDataset->next();
    $sProcessUID = '';
    while ($aRow = $oDataset->getRow()) {
        if ($sProcessUID == '') {
            $sProcessUID = $aRow['PRO_UID'];
        }
        $aProcesses[] = array ('PRO_UID' => $aRow['PRO_UID'],'PRO_TITLE' => $aRow['PRO_TITLE'] );
        $oDataset->next();
    }
    global $_DBArray;
    $_DBArray['PROCESSES'] = $aProcesses;
    $_SESSION['_DBArray'] = $_DBArray;
    $G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'dbConnections/dbConnections_Events' );
    require_once 'classes/model/DbSource.php';
    $oDBSource = new DbSource();
    $oCriteria = $oDBSource->getCriteriaDBSList( $sProcessUID );
    $G_PUBLISH->AddContent( 'propeltable', 'paged-table', 'dbConnections/dbConnections', $oCriteria );
} else {
    $G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'setup/noProcesses' );
}
G::RenderPage( 'publish' );

