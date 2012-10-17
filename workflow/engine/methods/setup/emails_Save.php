<?php
/**
 * emails.php
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

require_once 'classes/model/Configuration.php';
$oConfiguration = new Configuration();
$aFields['MESS_ENABLED'] = isset( $_POST['form']['MESS_ENABLED'] ) ? $_POST['form']['MESS_ENABLED'] : '';
$aFields['MESS_ENGINE'] = isset( $_POST['form']['MESS_ENABLED'] ) ? $_POST['form']['MESS_ENGINE'] : '';
$aFields['MESS_SERVER'] = isset( $_POST['form']['MESS_ENABLED'] ) ? trim( $_POST['form']['MESS_SERVER'] ) : '';
$aFields['MESS_RAUTH'] = isset( $_POST['form']['MESS_ENABLED'] ) ? isset( $_POST['form']['MESS_RAUTH'] ) ? $_POST['form']['MESS_RAUTH'] : '' : '';
$aFields['MESS_PORT'] = isset( $_POST['form']['MESS_ENABLED'] ) ? $_POST['form']['MESS_PORT'] : '';
$aFields['MESS_ACCOUNT'] = isset( $_POST['form']['MESS_ENABLED'] ) ? $_POST['form']['MESS_ACCOUNT'] : '';
$aFields['MESS_PASSWORD'] = isset( $_POST['form']['MESS_ENABLED'] ) ? $_POST['form']['MESS_PASSWORD'] : '';
$aFields['MESS_PASSWORD_HIDDEN'] = isset( $_POST['form']['MESS_ENABLED'] ) ? $_POST['form']['MESS_PASSWORD_HIDDEN'] : '';
if ($aFields['MESS_PASSWORD_HIDDEN'] != '') {
    $aFields['MESS_PASSWORD'] = $aFields['MESS_PASSWORD_HIDDEN'];
}
$aFields['MESS_PASSWORD_HIDDEN'] = '';
$aPasswd = G::decrypt( $aFields['MESS_PASSWORD'], 'EMAILENCRYPT' );
$passwd = $aFields['MESS_PASSWORD'];
$passwdDec = G::decrypt( $passwd, 'EMAILENCRYPT' );
$auxPass = explode( 'hash:', $passwdDec );
if (count( $auxPass ) > 1) {
    if (count( $auxPass ) == 2) {
        $passwd = $auxPass[1];
    } else {
        array_shift( $auxPass );
        $passwd = implode( '', $auxPass );
    }
}
$aFields['MESS_PASSWORD'] = $passwd;
if ($aFields['MESS_PASSWORD'] != '') { // for plain text
    $aFields['MESS_PASSWORD'] = 'hash:' . $aFields['MESS_PASSWORD'];
    $aFields['MESS_PASSWORD'] = G::encrypt( $aFields['MESS_PASSWORD'], 'EMAILENCRYPT' );
}
$aFields['MESS_BACKGROUND'] = isset( $_POST['form']['MESS_ENABLED'] ) ? isset( $_POST['form']['MESS_BACKGROUND'] ) ? $_POST['form']['MESS_BACKGROUND'] : '' : '';
$aFields['MESS_EXECUTE_EVERY'] = isset( $_POST['form']['MESS_ENABLED'] ) ? $_POST['form']['MESS_EXECUTE_EVERY'] : '';
$aFields['MESS_SEND_MAX'] = isset( $_POST['form']['MESS_ENABLED'] ) ? $_POST['form']['MESS_SEND_MAX'] : '';
$aFields['SMTPSecure'] = isset( $_POST['form']['MESS_ENABLED'] ) ? $_POST['form']['SMTPSecure'] : '';
$aFields['MESS_TRY_SEND_INMEDIATLY'] = isset( $_POST['form']['MESS_ENABLED'] ) ? isset( $_POST['form']['MESS_TRY_SEND_INMEDIATLY'] ) ? $_POST['form']['MESS_TRY_SEND_INMEDIATLY'] : '' : '';
$oConfiguration->update( array ('CFG_UID' => 'Emails','OBJ_UID' => '','CFG_VALUE' => serialize( $aFields ),'PRO_UID' => '','USR_UID' => '','APP_UID' => ''
) );
G::SendTemporalMessage( 'ID_CHANGES_SAVED', 'TMP-INFO', 'label', 4, '100%' );
G::header( 'location: emails' );

