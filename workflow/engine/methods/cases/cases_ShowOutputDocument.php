<?php
/**
 * cases_ShowOutputDocument.php
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
/*
 * Created on 13-02-2008
 *
 * @author David Callizaya <davidsantos@colosa.com>
 */

require_once ("classes/model/AppDocumentPeer.php");

$oAppDocument = new AppDocument();
$oAppDocument->Fields = $oAppDocument->load( $_GET['a'], (isset( $_GET['v'] )) ? $_GET['v'] : NULL );

$sAppDocUid = $oAppDocument->getAppDocUid();
$info = pathinfo( $oAppDocument->getAppDocFilename() );
if (! isset( $_GET['ext'] )) {
    $ext = $info['extension'];
} else {
    if ($_GET['ext'] != '') {
        $ext = $_GET['ext'];
    } else {
        $ext = $info['extension'];
    }
}
$ver = (isset( $_GET['v'] ) && $_GET['v'] != '') ? '_' . $_GET['v'] : '';

if (! $ver) //This code is in the case the outputdocument won't be versioned
    $ver = '_1';

$realPath = PATH_DOCUMENT . G::getPathFromUID($oAppDocument->Fields['APP_UID']) . '/outdocs/' . $sAppDocUid . $ver . '.' . $ext;
$realPath1 = PATH_DOCUMENT . G::getPathFromUID($oAppDocument->Fields['APP_UID']) . '/outdocs/' . $info['basename'] . $ver . '.' . $ext;
$realPath2 = PATH_DOCUMENT . G::getPathFromUID($oAppDocument->Fields['APP_UID']) . '/outdocs/' . $info['basename'] . '.' . $ext;

$sw_file_exists = false;
if (file_exists( $realPath )) {
    $sw_file_exists = true;
} elseif (file_exists( $realPath1 )) {
    $sw_file_exists = true;
    $realPath = $realPath1;
} elseif (file_exists( $realPath2 )) {
    $sw_file_exists = true;
    $realPath = $realPath2;
}

if (! $sw_file_exists) {

    $oPluginRegistry = & PMPluginRegistry::getSingleton();
    if ($oPluginRegistry->existsTrigger( PM_UPLOAD_DOCUMENT )) {
        $error_message = G::LoadTranslation( 'ID_ERROR_FILE_NOT_EXIST', SYS_LANG, array('filename' => $info['basename'] . $ver . '.' . $ext) ) . ' ' . G::LoadTranslation('ID_CONTACT_ADMIN');
    } else {
        $error_message = "'" . $info['basename'] . $ver . '.' . $ext . "' " . G::LoadTranslation( 'ID_ERROR_STREAMING_FILE' );
    }

    if ((isset( $_POST['request'] )) && ($_POST['request'] == true)) {
            $res['success'] = 'failure';
            $res['message'] = $error_message;
            print G::json_encode( $res );
    } else {
        G::SendMessageText( $error_message, "ERROR" );
        $backUrlObj = explode( "sys" . SYS_SYS, $_SERVER['HTTP_REFERER'] );
        G::header( "location: " . "/sys" . SYS_SYS . $backUrlObj[1] );
        die();
    }

} else {
    if ((isset( $_POST['request'] )) && ($_POST['request'] == true)) {
        $res['success'] = 'success';
        $res['message'] = $info['basename'] . $ver . '.' . $ext;
        print G::json_encode( $res );
    } else {
        G::streamFile( $realPath, true, $info['basename'] . $ver . '.' . $ext );
    }
}
//G::streamFile ( $realPath, true);

