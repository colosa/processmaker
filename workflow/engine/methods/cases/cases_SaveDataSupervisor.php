<?php
/**
 * cases_SaveData.php
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
//validate the data post
$oForm = new Form( $_SESSION['PROCESS'] . '/' . $_GET['UID'], PATH_DYNAFORM );
$oForm->validatePost();

/* Includes */
G::LoadClass( 'case' );

//load the variables
$oCase = new Cases();
$Fields = $oCase->loadCase( $_SESSION['APPLICATION'] );
$Fields['APP_DATA'] = array_merge( $Fields['APP_DATA'], $_POST['form'] );

//save data
$aData = array ();
$aData['APP_NUMBER'] = $Fields['APP_NUMBER'];
$aData['APP_PROC_STATUS'] = $Fields['APP_PROC_STATUS'];
$aData['APP_DATA'] = $Fields['APP_DATA'];
$aData['DEL_INDEX'] = $_SESSION['INDEX'];
$aData['TAS_UID'] = $_SESSION['TASK'];
$aData['CURRENT_DYNAFORM'] = $_GET['UID'];
$aData['PRO_UID'] = $Fields['PRO_UID'];
$aData['USER_UID'] = $_SESSION['USER_LOGGED'];
$aData['APP_STATUS'] = $Fields['APP_STATUS'];

//$aData = $oCase->loadCase( $_SESSION['APPLICATION'] );
$oCase->updateCase( $_SESSION['APPLICATION'], $aData );

//go to the next step
$aNextStep = $oCase->getNextSupervisorStep( $_SESSION['PROCESS'], $_SESSION['STEP_POSITION'] );
$_SESSION['STEP_POSITION'] = $aNextStep['POSITION'];
G::header( 'location: cases_StepToRevise?DYN_UID=' . $aNextStep['UID'] . '&APP_UID=' . $_SESSION['APPLICATION'] . '&DEL_INDEX=' . $_SESSION['INDEX'] );

