<?php

/**
 * skinsSave.php
 *
 * ProcessMaker Open Source Edition
 * Copyright (C) 2004 - 2008 Colosa Inc.
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

function xcopy ($pathSource, $pathTarget)
{
    G::mk_dir( $pathTarget );
    if ($handle = opendir( $pathSource )) {
        while (false !== ($file = readdir( $handle ))) {
            if (substr( $file, 0, 1 ) != '.' && ! is_dir( $file )) {
                $content = file_get_contents( $pathSource . $file );
                $filename = $pathTarget . $file;
                file_put_contents( $filename, $content );
            }
        }
        closedir( $handle );
    }
}

global $RBAC;
switch ($RBAC->userCanAccess( 'PM_SETUP' )) {
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

$id = strip_tags( str_replace( ' ', '_', trim( $_POST['form']['NAME'] ) ) );
$desc = $_POST['form']['DESCRIPTION'];

$fileObj = PATH_SKINS . $id . '.cnf';

if (! file_exists( $fileObj )) {
    $oConf = new stdClass();
    $oConf->name = $id;
    $oConf->description = $desc;
    $oConf->version = 1;
    file_put_contents( $fileObj, serialize( $oConf ) );
}

$oConf = unserialize( file_get_contents( $fileObj ) );

$contentPHP = file_get_contents( PATH_SKINS . 'green.php' );
$contentPHP = str_replace( 'green.html', $id . '.html', $contentPHP );
file_put_contents( PATH_SKINS . $id . '.php', $contentPHP );

$contentHTML = file_get_contents( PATH_SKINS . 'green.html' );
$contentHTML = str_replace( 'green', $id, $contentHTML );
file_put_contents( PATH_SKINS . $id . '.html', $contentHTML );

$pathImages = PATH_HTML . 'skins' . PATH_SEP . $id . PATH_SEP . 'images' . PATH_SEP;
G::mk_dir( $pathImages );

xcopy( PATH_HTML . 'skins' . PATH_SEP . 'green' . PATH_SEP, PATH_HTML . 'skins' . PATH_SEP . $id . PATH_SEP );

xcopy( PATH_HTML . 'skins' . PATH_SEP . 'green' . PATH_SEP . 'images' . PATH_SEP, PATH_HTML . 'skins' . PATH_SEP . $id . PATH_SEP . 'images' . PATH_SEP );

G::Header( 'Location: ../../' . $id . '/setup/skinsList' );

