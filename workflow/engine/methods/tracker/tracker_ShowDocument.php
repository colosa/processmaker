<?php
/**
 * tracker_ShowDocument.php
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

require_once ("classes/model/AppDocumentPeer.php");

$oAppDocument = new AppDocument();
if (! isset( $_GET['v'] )) { //Load last version of the document
    $docVersion = $oAppDocument->getLastAppDocVersion( $_GET['a'] );
} else {
    $docVersion = $_GET['v'];
}
$oAppDocument->Fields = $oAppDocument->load( $_GET['a'], $docVersion );

$sAppDocUid = $oAppDocument->getAppDocUid();
$iDocVersion = $oAppDocument->getDocVersion();
$info = pathinfo( $oAppDocument->getAppDocFilename() );
$ext = $info['extension'];

if (isset( $_GET['b'] )) {
    if ($_GET['b'] == '0') {
        $bDownload = false;
    } else {
        $bDownload = true;
    }
} else {
    $bDownload = true;
}

$realPath = PATH_DOCUMENT . $oAppDocument->Fields['APP_UID'] . '/' . $sAppDocUid . '_' . $iDocVersion . '.' . $ext;
$realPath1 = PATH_DOCUMENT . $oAppDocument->Fields['APP_UID'] . '/' . $sAppDocUid . '.' . $ext;
$sw_file_exists = false;
if (file_exists( $realPath )) {
    $sw_file_exists = true;
} elseif (file_exists( $realPath1 )) {
    $sw_file_exists = true;
    $realPath = $realPath1;
}

if (! $sw_file_exists) {
    $error_message = "'" . $oAppDocument->Fields['APP_DOC_FILENAME'] . "' " . G::LoadTranslation( 'ID_ERROR_STREAMING_FILE' );
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
        $res['message'] = $oAppDocument->Fields['APP_DOC_FILENAME'];
        print G::json_encode( $res );
    } else {
        G::streamFile( $realPath, $bDownload, $oAppDocument->Fields['APP_DOC_FILENAME'] );
    }
}

