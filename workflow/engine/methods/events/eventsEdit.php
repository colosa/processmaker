<?php
/**
 * events_Edit.php
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
if ($RBAC->userCanAccess( 'PM_SETUP' ) != 1) {
    G::SendTemporalMessage( 'ID_USER_HAVENT_RIGHTS_PAGE', 'error', 'labels' );
    G::header( 'location: ../login/login' );
    die();
}

G::LoadClass( 'tasks' );
G::LoadClass( 'processMap' );
global $_DBArray;

if (isset( $_GET['EVN_UID'] )) {
    require_once 'classes/model/Event.php';
    $oEvent = new Event();
    $aFields = $oEvent->load( $_GET['EVN_UID'] );
} else {
    $aFields = array ('PRO_UID' => $_GET['PRO_UID'] );
}

if (! isset( $_SESSION['PROCESS'] )) {
    if (isset( $aFields['PRO_UID'] )) {
        $_SESSION['PROCESS'] = $aFields['PRO_UID'];
    }
}

$oTasks = new Tasks();
$aAux1 = $oTasks->getAllTasks( $aFields['PRO_UID'] );
$aTasks = array ();
$aTasks[] = array ('TAS_UID' => 'char','TAS_TITLE' => 'char');
foreach ($aAux1 as $aAux2) {
    if ($aAux2['TAS_TYPE'] != 'SUBPROCESS') {
        $aTasks[] = array ('TAS_UID' => $aAux2['TAS_UID'],'TAS_TITLE' => $aAux2['TAS_TITLE'] );
    }
}

$oProcessMap = new processMap( new DBConnection() );
$aTriggersList = $oProcessMap->getTriggers( $_SESSION['PROCESS'] );
$aTriggersFileds = Array ('TRI_UID' => 'char','TRI_TITLE' => 'char');

foreach ($aTriggersList as $i => $v) {
    unset( $aTriggersList[$i]['PRO_UID'] );
    unset( $aTriggersList[$i]['TRI_DESCRIPTION'] );
    $aTriggersList[$i]['TRI_TITLE'] = (strlen( $aTriggersList[$i]['TRI_TITLE'] ) > 32) ? substr( $aTriggersList[$i]['TRI_TITLE'], 0, 32 ) . '...' : $aTriggersList[$i]['TRI_TITLE'];
}

$aTriggersList = array_merge( Array ($aTriggersFileds, $aTriggersList ));

$_DBArray['tasks'] = $aTasks;
$_DBArray['TMP_TRIGGERS'] = $aTriggersList;

$_SESSION['_DBArray'] = $_DBArray;

$G_PUBLISH = new Publisher();
$G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'events/eventsEdit', '', $aFields, '../events/eventsSave' );
G::RenderPage( 'publish', 'raw' );

