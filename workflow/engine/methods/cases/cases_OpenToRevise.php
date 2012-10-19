<?php
/**
 * cases_Open.php
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
/* Permissions */

if ($RBAC->userCanAccess( 'PM_SUPERVISOR' ) != 1) {
    switch ($RBAC->userCanAccess( 'PM_SUPERVISOR' )) {
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

    /* Includes */
G::LoadClass( 'case' );

/* GET , POST & $_SESSION Vars */
if (isset( $_SESSION['APPLICATION'] )) {
    unset( $_SESSION['APPLICATION'] );
}
if (isset( $_SESSION['PROCESS'] )) {
    unset( $_SESSION['PROCESS'] );
}
if (isset( $_SESSION['INDEX'] )) {
    unset( $_SESSION['INDEX'] );
}
if (isset( $_SESSION['STEP_POSITION'] )) {
    unset( $_SESSION['STEP_POSITION'] );
}

/* Process the info */
$oCase = new Cases();
$sAppUid = $_GET['APP_UID'];
$iDelIndex = $_GET['DEL_INDEX'];

$_SESSION['APPLICATION'] = $_GET['APP_UID'];
$_SESSION['INDEX'] = $_GET['DEL_INDEX'];

$aFields = $oCase->loadCase( $sAppUid, $iDelIndex );

$_SESSION['PROCESS'] = $aFields['PRO_UID'];
$_SESSION['TASK'] = $aFields['TAS_UID'];
$_SESSION['STEP_POSITION'] = 0;

/* Redirect to next step */
$aNextStep = $oCase->getNextSupervisorStep( $_SESSION['PROCESS'], 0 );
$sPage = "cases_StepToRevise?type=DYNAFORM&PRO_UID=" . $aFields['PRO_UID'] . "&DYN_UID=" . $aNextStep['UID'] . "&APP_UID=$sAppUid&DEL_INDEX=$iDelIndex&position=1"; //$aNextStep['PAGE'];
G::header( 'location: ' . $sPage );

