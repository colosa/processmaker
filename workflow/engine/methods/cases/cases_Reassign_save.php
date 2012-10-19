<?php
/**
 * cases_Reassign_save.php
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
try {
    global $RBAC;
    switch ($RBAC->userCanAccess( 'PM_REASSIGNCASE' )) {
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
    //print_r($_POST); die;
    G::LoadClass( 'case' );
    $oCase = new Cases();

    if ($_POST['USERS'] != '') {
        $oCase->reassignCase( $_POST['APP_UID'], $_POST['DEL_INDEX'], $_SESSION['USER_LOGGED'], $_POST['USERS'] );

    }

    require_once 'classes/model/Users.php';
    $oUser = new Users();
    $aUser = $oUser->load( $_POST['USERS'] );

    $Fields = array ();

    $Fields['USERS'] = $aUser['USR_FIRSTNAME'] . ' ' . $aUser['USR_LASTNAME'] . ' (' . $aUser['USR_USERNAME'] . ')';

    G::LoadClass( 'case' );
    $oCases = new Cases();
    $aCases = $oCases->loadCase( $_POST['APP_UID'], $_POST['DEL_INDEX'] );

    $Fields['APP_NUMBER'] = $aCases['APP_NUMBER'];

    $G_MAIN_MENU = 'processmaker';
    $G_SUB_MENU = 'cases';
    $G_ID_MENU_SELECTED = 'CASES';
    $G_ID_SUB_MENU_SELECTED = 'CASES_TO_REASSIGN';
    $G_PUBLISH = new Publisher();
    $G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'cases/cases_Reassign.xml', '', $Fields, '' );
    G::RenderPage( 'publish', 'blank' );
    //G::SendMessageText(G::LoadTranslation('ID_FINISHED'), 'info');
    //G::header('Location: cases_List');


} catch (Exception $oException) {
    die( $oException->getMessage() );
}

