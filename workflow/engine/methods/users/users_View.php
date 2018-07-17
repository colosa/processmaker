<?php
/**
 * users_View.php
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
    switch ($RBAC->userCanAccess( 'PM_FACTORY' )) {
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

    $_SESSION['CURRENT_USER'] = $_GET['USR_UID'];
    $oUser = new Users();
    $aFields = $oUser->load( $_GET['USR_UID'] );
    $aFields['USR_PASSWORD'] = '********';
    $aFields['MESSAGE0'] = str_replace( "\r\n", "<br>", G::LoadTranslation( 'ID_USER_REGISTERED' ) ) . '!';
    $aFields['MESSAGE1'] = str_replace( "\r\n", "<br>", G::LoadTranslation( 'ID_MSG_ERROR_USR_USERNAME' ) );
    $aFields['MESSAGE2'] = str_replace( "\r\n", "<br>", G::LoadTranslation( 'ID_MSG_ERROR_DUE_DATE' ) );
    $aFields['MESSAGE3'] = str_replace( "\r\n", "<br>", G::LoadTranslation( 'ID_NEW_PASS_SAME_OLD_PASS' ) );
    $aFields['MESSAGE4'] = str_replace( "\r\n", "<br>", G::LoadTranslation( 'ID_MSG_ERROR_USR_FIRSTNAME' ) );
    $aFields['MESSAGE5'] = str_replace( "\r\n", "<br>", G::LoadTranslation( 'ID_MSG_ERROR_USR_LASTNAME' ) );
    $aFields['NO_RESUME'] = G::LoadTranslation( 'ID_NO_RESUME' );
    $aFields['START_DATE'] = date( 'Y-m-d' );
    $aFields['END_DATE'] = date( 'Y-m-d', mktime( 0, 0, 0, date( 'm' ), date( 'd' ), date( 'Y' ) + 5 ) );
    $aFields['RANDOM'] = rand();
    $G_MAIN_MENU = 'processmaker';
    $G_ID_MENU_SELECTED = 'USERS';
    $G_PUBLISH = new Publisher();
    $G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'users/users_View.xml', '', $aFields );
    krumo( $_SESSION );
    if ($_GET['USR_UID'] == '00000000000000000000000000000001') { //$G_PUBLISH->AddContent('xmlform', 'xmlform', 'users/users_ViewAdmin.xml', '', $aFields);
        // administrator due date must have a longer range
        $aFields['END_DATE'] = date( 'Y-m-d', mktime( 0, 0, 0, date( 'm' ), date( 'd' ), date( 'Y' ) + 10 ) );
        krumo( "asdasd" );
        $G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'users/users_EditAdmin.xml', 'display:none', $aFields, 'users_Save?USR_UID=' . $_SESSION['CURRENT_USER'] );
    } else {
        $G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'users/users_Edit.xml', 'display:none', $aFields, 'users_Save?USR_UID=' . $_SESSION['CURRENT_USER'] );
    }
    G::RenderPage( 'publish' );
} catch (Exception $oException) {
    $token = strtotime("now");
    PMException::registerErrorLog($oException, $token);
    G::outRes( G::LoadTranslation("ID_EXCEPTION_LOG_INTERFAZ", array($token)) );
    die;
}

