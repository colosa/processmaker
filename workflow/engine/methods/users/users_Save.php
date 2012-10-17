<?php
/**
 * users_Save.php
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
    if (empty( $_POST ) || ! isset( $_POST['form'] )) {
        if (empty( $_FILES ))
            throw (new Exception( G::loadTranslation( 'ID_ERROR_UPLOADING_FILENAME' ) ));
        else
            throw (new Exception( G::loadTranslation( 'ID_POSTED_DATA_EMPTY' ) ));
    }

    $form = $_POST['form'];

    if (isset( $_GET['USR_UID'] )) {
        $form['USR_UID'] = $_GET['USR_UID'];
    } else {
        $form['USR_UID'] = '';
    }

    if (isset( $_FILES['form']['name']['USR_RESUME'] )) {
        if ($_FILES['form']['tmp_name']['USR_RESUME'] != '') {
            $form['USR_RESUME'] = $_FILES['form']['name']['USR_RESUME'];
        } else {
            $form['USR_RESUME'] = '';
        }
    }

    if (! isset( $form['USR_NEW_PASS'] )) {
        $form['USR_NEW_PASS'] = '';
    }
    if ($form['USR_NEW_PASS'] != '') {
        $form['USR_PASSWORD'] = md5( $form['USR_NEW_PASS'] );
    }
    if (! isset( $form['USR_CITY'] )) {
        $form['USR_CITY'] = '';
    }
    if (! isset( $form['USR_LOCATION'] )) {
        $form['USR_LOCATION'] = '';
    }
    if (! isset( $form['USR_AUTH_USER_DN'] )) {
        $form['USR_AUTH_USER_DN'] = '';
    }
    if ($form['USR_UID'] == '') {
        $aData['USR_USERNAME'] = $form['USR_USERNAME'];
        $aData['USR_PASSWORD'] = $form['USR_PASSWORD'];
        $aData['USR_FIRSTNAME'] = $form['USR_FIRSTNAME'];
        $aData['USR_LASTNAME'] = $form['USR_LASTNAME'];
        $aData['USR_EMAIL'] = $form['USR_EMAIL'];
        $aData['USR_DUE_DATE'] = $form['USR_DUE_DATE'];
        $aData['USR_CREATE_DATE'] = date( 'Y-m-d H:i:s' );
        $aData['USR_UPDATE_DATE'] = date( 'Y-m-d H:i:s' );
        $aData['USR_BIRTHDAY'] = date( 'Y-m-d' );
        $aData['USR_AUTH_USER_DN'] = $form['USR_AUTH_USER_DN'];
        //fixing bug in inactive user when the admin create a new user.
        $statusWF = $form['USR_STATUS'];
        $aData['USR_STATUS'] = $form['USR_STATUS'] == 'ACTIVE' ? 1 : 0;
        $sUserUID = $RBAC->createUser( $aData, $form['USR_ROLE'] );
        $aData['USR_STATUS'] = $statusWF;
        $aData['USR_UID'] = $sUserUID;
        $aData['USR_PASSWORD'] = md5( $sUserUID ); //fake :p
        $aData['USR_COUNTRY'] = $form['USR_COUNTRY'];
        $aData['USR_CITY'] = $form['USR_CITY'];
        $aData['USR_LOCATION'] = $form['USR_LOCATION'];
        $aData['USR_ADDRESS'] = $form['USR_ADDRESS'];
        $aData['USR_PHONE'] = $form['USR_PHONE'];
        $aData['USR_ZIP_CODE'] = $form['USR_ZIP_CODE'];
        $aData['USR_POSITION'] = $form['USR_POSITION'];
        //  Commented by removal of resume in the addition and modification of user.
        //  $aData['USR_RESUME']      = $form['USR_RESUME'];
        $aData['USR_ROLE'] = $form['USR_ROLE'];
        $aData['USR_REPLACED_BY'] = $form['USR_REPLACED_BY'];

        require_once 'classes/model/Users.php';
        $oUser = new Users();
        $oUser->create( $aData );
        if ($_FILES['form']['error']['USR_PHOTO'] != 1) {
            if ($_FILES['form']['tmp_name']['USR_PHOTO'] != '') {
                G::uploadFile( $_FILES['form']['tmp_name']['USR_PHOTO'], PATH_IMAGES_ENVIRONMENT_USERS, $sUserUID . '.gif' );
            }
        } else {
            G::SendTemporalMessage( 'ID_FILE_TOO_BIG', 'error' );
        }
        if ($_FILES['form']['error']['USR_RESUME'] != 1) {
            if ($_FILES['form']['tmp_name']['USR_RESUME'] != '') {
                G::uploadFile( $_FILES['form']['tmp_name']['USR_RESUME'], PATH_IMAGES_ENVIRONMENT_FILES . $sUserUID . '/', $_FILES['form']['name']['USR_RESUME'] );
            }
        } else {
            G::SendTemporalMessage( 'ID_FILE_TOO_BIG', 'error' );
        }
    } else {
        $aData['USR_UID'] = $form['USR_UID'];
        $aData['USR_USERNAME'] = $form['USR_USERNAME'];

        if (isset( $form['USR_PASSWORD'] )) {
            if ($form['USR_PASSWORD'] != '') {
                $aData['USR_PASSWORD'] = $form['USR_PASSWORD'];
                require_once 'classes/model/UsersProperties.php';
                $oUserProperty = new UsersProperties();
                $aUserProperty = $oUserProperty->loadOrCreateIfNotExists( $form['USR_UID'], array ('USR_PASSWORD_HISTORY' => serialize( array (md5( $form['USR_PASSWORD'] )
                ) )
                ) );

                $RBAC->loadUserRolePermission( 'PROCESSMAKER', $_SESSION['USER_LOGGED'] );
                if ($RBAC->aUserInfo['PROCESSMAKER']['ROLE']['ROL_CODE'] == 'PROCESSMAKER_ADMIN') {
                    $aUserProperty['USR_LAST_UPDATE_DATE'] = date( 'Y-m-d H:i:s' );
                    $aUserProperty['USR_LOGGED_NEXT_TIME'] = 1;
                    $oUserProperty->update( $aUserProperty );
                }

                $aErrors = $oUserProperty->validatePassword( $form['USR_NEW_PASS'], $aUserProperty['USR_LAST_UPDATE_DATE'], 0 );
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
                    //it's looking a password igual into aHistory array that was send for post in md5 way
                    $c = 0;
                    $sw = 1;
                    while (count( $aHistory ) >= 1 && count( $aHistory ) > $c && $sw) {
                        if (strcmp( trim( $aHistory[$c] ), trim( $form['USR_PASSWORD'] ) ) == 0) {
                            $sw = 0;
                        }
                        $c ++;
                    }
                    if ($sw == 0) {
                        $sDescription = G::LoadTranslation( 'ID_POLICY_ALERT' ) . ':<br /><br />';
                        $sDescription .= ' - ' . G::LoadTranslation( 'PASSWORD_HISTORY' ) . ': ' . PPP_PASSWORD_HISTORY . '<br />';
                        $sDescription .= '<br />' . G::LoadTranslation( 'ID_PLEASE_CHANGE_PASSWORD_POLICY' ) . '';
                        G::SendMessageText( $sDescription, 'warning' );
                        G::header( 'Location: ' . $_SERVER['HTTP_REFERER'] );
                        die();
                    }
                    //
                    if (count( $aHistory ) >= PPP_PASSWORD_HISTORY) {
                        $sLastPassw = array_shift( $aHistory );
                    }
                    $aHistory[] = $form['USR_PASSWORD'];
                }
                $aUserProperty['USR_LAST_UPDATE_DATE'] = date( 'Y-m-d H:i:s' );
                $aUserProperty['USR_LOGGED_NEXT_TIME'] = 1;
                $aUserProperty['USR_PASSWORD_HISTORY'] = serialize( $aHistory );
                $oUserProperty->update( $aUserProperty );
            }
        }
        $aData['USR_FIRSTNAME'] = $form['USR_FIRSTNAME'];
        $aData['USR_LASTNAME'] = $form['USR_LASTNAME'];
        $aData['USR_EMAIL'] = $form['USR_EMAIL'];
        $aData['USR_DUE_DATE'] = $form['USR_DUE_DATE'];
        $aData['USR_UPDATE_DATE'] = date( 'Y-m-d H:i:s' );
        if (isset( $form['USR_STATUS'] )) {
            $aData['USR_STATUS'] = $form['USR_STATUS'];
        }
        if (isset( $form['USR_ROLE'] )) {
            $RBAC->updateUser( $aData, $form['USR_ROLE'] );
        } else {
            $RBAC->updateUser( $aData );
        }
        $aData['USR_COUNTRY'] = $form['USR_COUNTRY'];
        $aData['USR_CITY'] = $form['USR_CITY'];
        $aData['USR_LOCATION'] = $form['USR_LOCATION'];
        $aData['USR_ADDRESS'] = $form['USR_ADDRESS'];
        $aData['USR_PHONE'] = $form['USR_PHONE'];
        $aData['USR_ZIP_CODE'] = $form['USR_ZIP_CODE'];
        $aData['USR_POSITION'] = $form['USR_POSITION'];

        if ($form['USR_RESUME'] != '') {
            $aData['USR_RESUME'] = $form['USR_RESUME'];
        }
        if (isset( $form['USR_ROLE'] )) {
            $aData['USR_ROLE'] = $form['USR_ROLE'];
        }

        if (isset( $form['USR_REPLACED_BY'] )) {
            $aData['USR_REPLACED_BY'] = $form['USR_REPLACED_BY'];
        }
        if (isset( $form['USR_AUTH_USER_DN'] )) {
            $aData['USR_AUTH_USER_DN'] = $form['USR_AUTH_USER_DN'];
        }

        require_once 'classes/model/Users.php';
        $oUser = new Users();
        $oUser->update( $aData );
        $aExtensions = array ("AIS","BMP","BW","CDR","CDT","CGM","CMX","CPT","DCX","DIB","EMF","GBR","GIF","GIH","ICO","IFF","ILBM","JFIF","JIF","JPE","JPEG","JPG","KDC","LBM","MAC","PAT","PCD","PCT","PCX","PIC","PICT","PNG","PNTG","PIX","PSD","PSP","QTI","QTIF","RGB","RGBA","RIF","RLE","SGI","TGA","TIF","TIFF","WMF","XCF"
        );

        $sPhotoFile = $_FILES['form']['name']['USR_PHOTO'];
        $aPhotoFile = explode( '.', $sPhotoFile );
        $sExtension = strtoupper( $aPhotoFile[sizeof( $aPhotoFile ) - 1] );
        if ((strlen( $sPhotoFile ) > 0) && (! in_array( $sExtension, $aExtensions ))) {
            throw (new Exception( G::LoadTranslation( 'ID_ERROR_UPLOADING_IMAGE_TYPE' ) ));
        }
        if ($_FILES['form']['error']['USR_PHOTO'] != 1) {
            if ($_FILES['form']['tmp_name']['USR_PHOTO'] != '') {
                $aAux = explode( '.', $_FILES['form']['name']['USR_PHOTO'] );
                G::uploadFile( $_FILES['form']['tmp_name']['USR_PHOTO'], PATH_IMAGES_ENVIRONMENT_USERS, $aData['USR_UID'] . '.' . $aAux[1] );
                G::resizeImage( PATH_IMAGES_ENVIRONMENT_USERS . $aData['USR_UID'] . '.' . $aAux[1], 96, 96, PATH_IMAGES_ENVIRONMENT_USERS . $aData['USR_UID'] . '.gif' );
            }
        } else {
            G::SendTemporalMessage( 'ID_FILE_TOO_BIG', 'error' );
        }
        if ($_FILES['form']['error']['USR_RESUME'] != 1) {
            if ($_FILES['form']['tmp_name']['USR_RESUME'] != '') {
                G::uploadFile( $_FILES['form']['tmp_name']['USR_RESUME'], PATH_IMAGES_ENVIRONMENT_FILES . $aData['USR_UID'] . '/', $_FILES['form']['name']['USR_RESUME'] );
            }
        } else {
            G::SendTemporalMessage( 'ID_FILE_TOO_BIG', 'error' );
        }
    }

    if ($_SESSION['USER_LOGGED'] == $form['USR_UID']) {
        /*UPDATING SESSION VARIABLES*/
        $aUser = $RBAC->userObj->load( $_SESSION['USER_LOGGED'] );
        $_SESSION['USR_FULLNAME'] = $aUser['USR_FIRSTNAME'] . ' ' . $aUser['USR_LASTNAME'];
    }

    //Save Calendar assigment
    if ((isset( $form['USR_CALENDAR'] ))) {
        //Save Calendar ID for this user
        G::LoadClass( "calendar" );
        $calendarObj = new Calendar();
        $calendarObj->assignCalendarTo( $aData['USR_UID'], $form['USR_CALENDAR'], 'USER' );
    }

    G::header( 'location: users_List' );
} catch (Exception $e) {
    $G_MAIN_MENU = 'processmaker';
    $G_SUB_MENU = 'users';
    $G_ID_MENU_SELECTED = 'USERS';
    $G_ID_SUB_MENU_SELECTED = '';

    $aMessage = array ();
    $aMessage['MESSAGE'] = $e->getMessage();
    $G_PUBLISH = new Publisher();
    $G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'login/showMessage', '', $aMessage );
    G::RenderPage( 'publish', 'blank' );
}

