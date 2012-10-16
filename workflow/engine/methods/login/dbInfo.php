<?php

/**
 * dbInfo.php
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
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * For more information, contact Colosa Inc, 2566 Le Jeune Rd.,
 * Coral Gables, FL, 33134, USA, or email info@colosa.com.
 *
 */

function lookup ($target)
{
    global $ntarget;
    $msg = $target . ' => ';
      //if (eregi ( '[a-zA-Z]', $target ))
    if (preg_match( '[a-zA-Z]', $target )) {
        //Made compatible to PHP 5.3
        $ntarget = gethostbyname ( $target );
    } else {
        $ntarget = gethostbyaddr ( $target );
    }
    $msg .= $ntarget;
    return ($msg);
}

G::LoadClass("system");

if (getenv ( 'HTTP_CLIENT_IP' )) {
    $ip = getenv ( 'HTTP_CLIENT_IP' );
} else {
    if (getenv ( 'HTTP_X_FORWARDED_FOR' )) {
        $ip = getenv ( 'HTTP_X_FORWARDED_FOR' );
    } else {
        $ip = getenv ( 'REMOTE_ADDR' );
    }
}

$redhat = '';
if (file_exists ( '/etc/redhat-release' )) {
    $fnewsize = filesize ( '/etc/redhat-release' );
    $fp = fopen ( '/etc/redhat-release', 'r' );
    $redhat = trim ( fread ( $fp, $fnewsize ) );
    fclose ( $fp );
}

$redhat .= " (" . PHP_OS . ")";
if (defined ( "DB_HOST" )) {
    G::LoadClass ( 'net' );
    G::LoadClass ( 'dbConnections' );
    $dbNetView = new NET ( DB_HOST );
    $dbNetView->loginDbServer ( DB_USER, DB_PASS );
    $dbConns = new dbConnections ( '' );
    $availdb = '';
    foreach ($dbConns->getDbServicesAvailables () as $key => $val) {
        if ($availdb != '') {
            $availdb .= ', ';
        }
        $availdb .= $val ['name'];
    }
    try {
        $sMySQLVersion = $dbNetView->getDbServerVersion ( DB_ADAPTER );
    } catch (Exception $oException) {
        $sMySQLVersion = '?????';
    }
}

$Fields ['SYSTEM'] = $redhat;
if (defined ( "DB_HOST" )) {
    $Fields ['DATABASE'] = $dbNetView->dbName ( DB_ADAPTER ) . ' (Version ' . $sMySQLVersion . ')';
    $Fields ['DATABASE_SERVER'] = DB_HOST;
    $Fields ['DATABASE_NAME'] = DB_NAME;
    $Fields ['AVAILABLE_DB'] = $availdb;
} else {
    $Fields ['DATABASE'] = "Not defined";
    $Fields ['DATABASE_SERVER'] = "Not defined";
    $Fields ['DATABASE_NAME'] = "Not defined";
    $Fields ['AVAILABLE_DB'] = "Not defined";
}
$eeT="";
if (class_exists('pmLicenseManager')) {
    $eeT=" - Enterprise Edition";
}
$Fields ['PHP'] = phpversion ();
$Fields ['FLUID'] = System::getVersion() . $eeT;
$Fields ['IP'] = lookup ( $ip );
$Fields ['ENVIRONMENT'] = defined ( "SYS_SYS" ) ? SYS_SYS : "Not defined";
$Fields ['SERVER_SOFTWARE'] = getenv ( 'SERVER_SOFTWARE' );
$Fields ['SERVER_NAME'] = getenv ( 'SERVER_NAME' );
$Fields ['SERVER_PROTOCOL'] = getenv ( 'SERVER_PROTOCOL' );
$Fields ['SERVER_PORT'] = getenv ( 'SERVER_PORT' );
$Fields ['REMOTE_HOST'] = getenv ( 'REMOTE_HOST' );
$Fields ['SERVER_ADDR'] = getenv ( 'SERVER_ADDR' );
$Fields ['HTTP_USER_AGENT'] = getenv ( 'HTTP_USER_AGENT' );
$Fields ['TIME_ZONE'] = (defined('TIME_ZONE')) ? TIME_ZONE : "Unknown";

if (!defined( 'SKIP_RENDER_SYSTEM_INFORMATION')) {
    $G_PUBLISH = new Publisher ( );
    $G_PUBLISH->AddContent ( 'xmlform', 'xmlform', 'login/dbInfo', '', $Fields, 'appNew2' );
    G::RenderPage ( 'publish', 'raw' );
}

