<?php
/**
 * cases_Save.php
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
switch ($RBAC->userCanAccess( 'PM_CASES' )) {
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

/* GET , POST & $_SESSION Vars */

/* unset any variable, because we are starting a new case */
if (isset( $_SESSION['APPLICATION'] )) {
    unset( $_SESSION['APPLICATION'] );
}
if (isset( $_SESSION['PROCESS'] )) {
    unset( $_SESSION['PROCESS'] );
}
if (isset( $_SESSION['TASK'] )) {
    unset( $_SESSION['TASK'] );
}
if (isset( $_SESSION['INDEX'] )) {
    unset( $_SESSION['INDEX'] );
}
if (isset( $_SESSION['STEP_POSITION'] )) {
    unset( $_SESSION['STEP_POSITION'] );
}

//If no variables are submitted and the $_POST variable is empty
if (!isset($_POST['form'])) {
    $_POST['form'] = array();
}

/* Process */
try {
    $oCase = new Cases();
    $aData = $oCase->startCase( $_POST['form']['TAS_UID'], $_SESSION['USER_LOGGED'] );
    $_SESSION['APPLICATION'] = $aData['APPLICATION'];
    $_SESSION['INDEX'] = $aData['INDEX'];
    $_SESSION['PROCESS'] = $aData['PROCESS'];
    $_SESSION['TASK'] = $_POST['form']['TAS_UID'];
    $_SESSION['STEP_POSITION'] = 0;

    $_SESSION['CASES_REFRESH'] = true;

    $oCase = new Cases();
    $aNextStep = $oCase->getNextStep( $_SESSION['PROCESS'], $_SESSION['APPLICATION'], $_SESSION['INDEX'], $_SESSION['STEP_POSITION'] );
    $_SESSION['BREAKSTEP']['NEXT_STEP'] = $aNextStep;

    G::header( 'location: ' . $aNextStep['PAGE'] );
} catch (Exception $e) {
    $_SESSION['G_MESSAGE'] = $e->getMessage();
    $_SESSION['G_MESSAGE_TYPE'] = 'error';
    G::header( 'location: cases_New' );
}

