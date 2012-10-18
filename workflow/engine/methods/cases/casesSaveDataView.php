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
//$oForm = new Form ( $_SESSION ['PROCESS'] . '/' . $_GET ['UID'], PATH_DYNAFORM );
//$oForm->validatePost ();

/* @author Alvaro Campos Sanchez */
/* Includes */
G::LoadClass( 'case' );

//load the variables
$oCase = new Cases();
$oCase->thisIsTheCurrentUser( $_SESSION['APPLICATION'], $_SESSION['INDEX'], $_SESSION['USER_LOGGED'], 'REDIRECT', 'cases_List' );
$Fields = $oCase->loadCase( $_SESSION['APPLICATION'] );
$Fields['APP_DATA'] = array_merge( $Fields['APP_DATA'], G::getSystemConstants() );
$Fields['APP_DATA'] = array_merge( $Fields['APP_DATA'], (array) $_POST['form'] );

#here we must verify if is a debug session
$trigger_debug_session = $_SESSION['TRIGGER_DEBUG']['ISSET']; #here we must verify if is a debugg session
#trigger debug routines...
//cleaning debug variables
$_SESSION['TRIGGER_DEBUG']['ERRORS'] = Array ();
$_SESSION['TRIGGER_DEBUG']['DATA'] = Array ();
$_SESSION['TRIGGER_DEBUG']['TRIGGERS_NAMES'] = Array ();
$_SESSION['TRIGGER_DEBUG']['TRIGGERS_VALUES'] = Array ();
$triggers = $oCase->loadTriggers( $_SESSION['TASK'], 'DYNAFORM', $_GET['UID'], 'AFTER' );
$_SESSION['TRIGGER_DEBUG']['NUM_TRIGGERS'] = count( $triggers );
$_SESSION['TRIGGER_DEBUG']['TIME'] = 'AFTER';

if ($_SESSION['TRIGGER_DEBUG']['NUM_TRIGGERS'] != 0) {
    $_SESSION['TRIGGER_DEBUG']['TRIGGERS_NAMES'] = $oCase->getTriggerNames( $triggers );
    $_SESSION['TRIGGER_DEBUG']['TRIGGERS_VALUES'] = $triggers;
}

if ($_SESSION['TRIGGER_DEBUG']['NUM_TRIGGERS'] != 0) {
    //Execute after triggers - Start
    $Fields['APP_DATA'] = $oCase->ExecuteTriggers( $_SESSION['TASK'], 'DYNAFORM', $_GET['UID'], 'AFTER', $Fields['APP_DATA'] );
    //Execute after triggers - End
}

//go to the next step
$aNextStep = $oCase->getNextStep( $_SESSION['PROCESS'], $_SESSION['APPLICATION'], $_SESSION['INDEX'], $_SESSION['STEP_POSITION'] );
if (isset( $_GET['_REFRESH_'] )) {
    G::header( 'location: ' . $_SERVER['HTTP_REFERER'] );
    die();
}
$_SESSION['STEP_POSITION'] = $aNextStep['POSITION'];

$_SESSION['BREAKSTEP']['NEXT_STEP'] = $aNextStep['PAGE'];

if ($trigger_debug_session) {
    $_SESSION['TRIGGER_DEBUG']['BREAKPAGE'] = $aNextStep['PAGE'];
    $aNextStep['PAGE'] = $aNextStep['PAGE'] . '&breakpoint=triggerdebug';
}

G::header( 'location: ' . $aNextStep['PAGE'] );

