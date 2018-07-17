<?php
/**
 * myInfo_Save.php
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
    ini_set( 'display_errors', '1' );
    global $RBAC;
    switch ($RBAC->userCanAccess( 'PM_LOGIN' )) {
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
    if (isset( $_FILES['form']['name']['USR_RESUME'] )) {
        $_POST['form']['USR_RESUME'] = $_FILES['form']['name']['USR_RESUME'];
    }
    if ($_POST['form']['USR_EMAIL'] != '') {
        // The ereg function has been DEPRECATED as of PHP 5.3.0.
        // if (!ereg("^[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*$", $_POST['form']['USR_EMAIL'])) {
        if (! preg_match( "/^[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*$/", $_POST['form']['USR_EMAIL'] )) {
            G::SendTemporalMessage( 'ID_INCORRECT_EMAIL', 'error' );
        }
    }
    if (! isset( $_POST['form']['USR_NEW_PASS'] )) {
        $_POST['form']['USR_NEW_PASS'] = '';
    }
    if ($_POST['form']['USR_NEW_PASS'] != '') {
        $_POST['form']['USR_PASSWORD'] = Bootstrap::hashPassword( $_POST['form']['USR_NEW_PASS'] );
    }
    if (! isset( $_POST['form']['USR_CITY'] )) {
        $_POST['form']['USR_CITY'] = '';
    }
    if (! isset( $_POST['form']['USR_LOCATION'] )) {
        $_POST['form']['USR_LOCATION'] = '';
    }
    if (! isset( $_POST['form']['USR_ROLE'] )) {
        $_POST['form']['USR_ROLE'] = '';
    }
    $aData['USR_UID'] = $_POST['form']['USR_UID'];
    $aData['USR_USERNAME'] = $_POST['form']['USR_USERNAME'];
    if (isset( $_POST['form']['USR_PASSWORD'] )) {
        if ($_POST['form']['USR_PASSWORD'] != '') {
            $aData['USR_PASSWORD'] = $_POST['form']['USR_PASSWORD'];
            require_once 'classes/model/UsersProperties.php';
            $oUserProperty = new UsersProperties();
            $aUserProperty = $oUserProperty->loadOrCreateIfNotExists( $_POST['form']['USR_UID'], array ('USR_PASSWORD_HISTORY' => serialize( array (G::encryptOld( $_POST['form']['USR_NEW_PASS'] )
            ) )
            ) );
            $aErrors = $oUserProperty->validatePassword( $_POST['form']['USR_NEW_PASS'], $aUserProperty['USR_LAST_UPDATE_DATE'], $aUserProperty['USR_LOGGED_NEXT_TIME'] );
            if (count( $aErrors ) > 0) {
                $sDescription = G::LoadTranslation( 'ID_POLICY_ALERT' ) . ':<br /><br />';
                foreach ($aErrors as $sError) {
                    switch ($sError) {
                        case 'ID_PPP_MINIMUN_LENGTH':
                            $sDescription .= ' - ' . G::LoadTranslation( $sError ) . ': ' . PPP_MINIMUN_LENGTH . '<br />';
                            break;
                        case 'ID_PPP_MAXIMUN_LENGTH':
                            $sDescription .= ' - ' . G::LoadTranslation( $sError ) . ': ' . PPP_MAXIMUN_LENGTH . '<br />';
                            break;
                        case 'ID_PPP_EXPIRATION_IN':
                            $sDescription .= ' - ' . G::LoadTranslation( $sError ) . ' ' . PPP_EXPIRATION_IN . ' ' . G::LoadTranslation( 'ID_DAYS' ) . '<br />';
                            break;
                        default:
                            $sDescription .= ' - ' . G::LoadTranslation( $sError ) . '<br />';
                            break;
                    }
                }
                $sDescription .= '<br />' . G::LoadTranslation( 'ID_PLEASE_CHANGE_PASSWORD_POLICY' );
                G::SendMessageText( $sDescription, 'warning' );
                G::header( 'Location: ' . $_SERVER['HTTP_REFERER'] );
                die();
            }
            $aHistory = unserialize( $aUserProperty['USR_PASSWORD_HISTORY'] );
            if (! is_array( $aHistory )) {
                $aHistory = array ();
            }
            if (! defined( 'PPP_PASSWORD_HISTORY' )) {
                define( 'PPP_PASSWORD_HISTORY', 0 );
            }
            if (PPP_PASSWORD_HISTORY > 0) {
                if (count( $aHistory ) >= PPP_PASSWORD_HISTORY) {
                    array_shift( $aHistory );
                }
                $aHistory[] = $_POST['form']['USR_NEW_PASS'];
            }
            $aUserProperty['USR_LAST_UPDATE_DATE'] = date( 'Y-m-d H:i:s' );
            $aUserProperty['USR_LOGGED_NEXT_TIME'] = 1;
            $aUserProperty['USR_PASSWORD_HISTORY'] = serialize( $aHistory );
            $oUserProperty->update( $aUserProperty );
        }
    }
    $aData['USR_FIRSTNAME'] = $_POST['form']['USR_FIRSTNAME'];
    $aData['USR_LASTNAME'] = $_POST['form']['USR_LASTNAME'];
    $aData['USR_EMAIL'] = $_POST['form']['USR_EMAIL'];
    $aData['USR_DUE_DATE'] = $_POST['form']['USR_DUE_DATE'];
    $aData['USR_UPDATE_DATE'] = date( 'Y-m-d H:i:s' );
    $RBAC->updateUser( $aData );
    $aData['USR_PASSWORD'] = G::encryptOld( $_POST['form']['USR_USERNAME'] ); //fake :p
    $aData['USR_COUNTRY'] = $_POST['form']['USR_COUNTRY'];
    $aData['USR_CITY'] = $_POST['form']['USR_CITY'];
    $aData['USR_LOCATION'] = $_POST['form']['USR_LOCATION'];
    $aData['USR_ADDRESS'] = $_POST['form']['USR_ADDRESS'];
    $aData['USR_PHONE'] = $_POST['form']['USR_PHONE'];
    $aData['USR_ZIP_CODE'] = $_POST['form']['USR_ZIP_CODE'];
    $aData['USR_POSITION'] = $_POST['form']['USR_POSITION'];
    if ($_POST['form']['USR_RESUME'] != '') {
        $aData['USR_RESUME'] = $_POST['form']['USR_RESUME'];
    }
    require_once 'classes/model/Users.php';
    $oUser = new Users();
    $oUser->update( $aData );
    if ($_FILES['form']['tmp_name']['USR_PHOTO'] != '') {
        $aAux = explode( '.', $_FILES['form']['name']['USR_PHOTO'] );
        G::uploadFile( $_FILES['form']['tmp_name']['USR_PHOTO'], PATH_IMAGES_ENVIRONMENT_USERS, $aData['USR_UID'] . '.' . $aAux[1] );
        G::resizeImage( PATH_IMAGES_ENVIRONMENT_USERS . $aData['USR_UID'] . '.' . $aAux[1], 96, 96, PATH_IMAGES_ENVIRONMENT_USERS . $aData['USR_UID'] . '.gif' );
    }
    if ($_FILES['form']['tmp_name']['USR_RESUME'] != '') {
        G::uploadFile( $_FILES['form']['tmp_name']['USR_RESUME'], PATH_IMAGES_ENVIRONMENT_FILES . $aData['USR_UID'] . '/', $_FILES['form']['name']['USR_RESUME'] );
    }

    /* Saving preferences */
    $def_lang = $_POST['form']['PREF_DEFAULT_LANG'];
    $def_menu = $_POST['form']['PREF_DEFAULT_MENUSELECTED'];
    $def_cases_menu = $_POST['form']['PREF_DEFAULT_CASES_MENUSELECTED'];

    $oConf = new Configurations();
    $aConf = Array ('DEFAULT_LANG' => $def_lang,'DEFAULT_MENU' => $def_menu,'DEFAULT_CASES_MENU' => $def_cases_menu
    );

    /*UPDATING SESSION VARIABLES*/
    $aUser = $RBAC->userObj->load( $_SESSION['USER_LOGGED'] );
    $_SESSION['USR_FULLNAME'] = $aUser['USR_FIRSTNAME'] . ' ' . $aUser['USR_LASTNAME'];

    $oConf->aConfig = $aConf;
    $oConf->saveConfig( 'USER_PREFERENCES', '', '', $_SESSION['USER_LOGGED'] );

    G::SendTemporalMessage( 'ID_CHANGES_SAVED', 'info', 'labels' );
    G::header( 'location: myInfo' );
} catch (Exception $oException) {
    $token = strtotime("now");
    PMException::registerErrorLog($oException, $token);
    G::outRes( G::LoadTranslation("ID_EXCEPTION_LOG_INTERFAZ", array($token)) );
    die;
}

