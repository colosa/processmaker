<?php
/**
 * authSources_ImportUsers.php
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
if ($RBAC->userCanAccess( 'PM_SETUP_ADVANCE' ) != 1) {
    G::SendTemporalMessage( 'ID_USER_HAVENT_RIGHTS_PAGE', 'error', 'labels' );
    G::header( 'location: ../login/login' );
    die();
}
$aFields = $RBAC->getAuthSource( $_POST['form']['AUTH_SOURCE_UID'] );
$aAttributes = array();
if (isset($aFields['AUTH_SOURCE_DATA']['AUTH_SOURCE_GRID_ATTRIBUTE'])) {
    $aAttributes = $aFields['AUTH_SOURCE_DATA']['AUTH_SOURCE_GRID_ATTRIBUTE'];    
}

//G::LoadThirdParty( 'pear/json', 'class.json' );
//$oJSON = new Services_JSON();

foreach ($_POST['aUsers'] as $sUser) {
    $matches = array ();
    $aUser = (array) Bootstrap::json_decode( stripslashes( $sUser ) );
    $aData['USR_USERNAME'] = str_replace( "*", "'", $aUser['sUsername'] );
    $aData['USR_PASSWORD'] = md5( str_replace( "*", "'", $aUser['sUsername'] ) );
    // note added by gustavo gustavo-at-colosa.com
    // asign the FirstName and LastName variables
    // add replace to change D*Souza to D'Souza by krlos
    $aData['USR_FIRSTNAME'] = str_replace( "*", "'", $aUser['sFirstname'] );
    $aData['USR_LASTNAME'] = str_replace( "*", "'", $aUser['sLastname'] );
    $aData['USR_EMAIL'] = $aUser['sEmail'];
    $aData['USR_DUE_DATE'] = date( 'Y-m-d', mktime( 0, 0, 0, date( 'm' ), date( 'd' ), date( 'Y' ) + 2 ) );
    $aData['USR_CREATE_DATE'] = date( 'Y-m-d H:i:s' );
    $aData['USR_UPDATE_DATE'] = date( 'Y-m-d H:i:s' );
    $aData['USR_BIRTHDAY'] = date( 'Y-m-d' );
    $aData['USR_STATUS'] = 1;
    $aData['USR_AUTH_TYPE'] = strtolower( $aFields['AUTH_SOURCE_PROVIDER'] );
    $aData['UID_AUTH_SOURCE'] = $aFields['AUTH_SOURCE_UID'];
    // validating with regexp if there are some missing * inside the DN string
    // if it's so the is changed to the ' character
    preg_match( '/[a-zA-Z]\*[a-zA-Z]/', $aUser['sDN'], $matches );
    foreach ($matches as $key => $match) {
        $newMatch = str_replace( '*', '\'', $match );
        $aUser['sDN'] = str_replace( $match, $newMatch, $aUser['sDN'] );
    }
    $aData['USR_AUTH_USER_DN'] = $aUser['sDN'];
    try {
        $sUserUID = $RBAC->createUser( $aData, 'PROCESSMAKER_OPERATOR' );
    } catch(Exception $oError) {
        $G_PUBLISH = new Publisher();
        $G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'login/showMessage', '', array ('MESSAGE' => $oError->getMessage()) );
        G::RenderPage("publish", "blank");
        die();
    }
    $aData['USR_STATUS'] = 'ACTIVE';
    $aData['USR_UID'] = $sUserUID;
    $aData['USR_PASSWORD'] = md5( $sUserUID ); //fake :p
    $aData['USR_ROLE'] = 'PROCESSMAKER_OPERATOR';

    if (count($aAttributes)) {
        foreach ($aAttributes as $value) {
            if (isset($aUser[$value['attributeUser']])) {
                $aData[$value['attributeUser']] = str_replace( "*", "'", $aUser[$value['attributeUser']] );
                if ($value['attributeUser'] == 'USR_STATUS') {
                    $evalValue = $aData[$value['attributeUser']];

                    $statusValue = 'INACTIVE';
                    if (is_string($evalValue) && G::toUpper($evalValue) == 'ACTIVE') {
                        $statusValue = 'ACTIVE';
                    }
                    if (is_bool($evalValue) && $evalValue == true) {
                        $statusValue = 'ACTIVE';
                    }
                    if ( (is_float($evalValue) || is_int($evalValue) ||
                          is_integer($evalValue) || is_numeric($evalValue)) && (int)$evalValue != 0) {
                        $statusValue = 'ACTIVE';
                    }
                    $aData[$value['attributeUser']] = $statusValue;
                }
            }
        }
    }
    require_once 'classes/model/Users.php';
    $oUser = new Users();
    $oUser->create( $aData );
}

G::header( 'Location: ../users/users_List' );

