<?php
/**
 * outputdocs_Edit.php
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
    
    require_once 'classes/model/OutputDocument.php';
    $ooutputDocument = new OutputDocument();
    if (isset( $_GET['OUT_DOC_UID'] )) {
        $aFields = $ooutputDocument->load( $_GET['OUT_DOC_UID'] );
    } else {
        $aFields = array ();
        $aFields['PRO_UID'] = $_GET['PRO_UID'];
    }
    
    require_once 'classes/model/OutputDocument.php';
    $ooutputDocument = new OutputDocument();
    if (isset( $_GET['OUT_DOC_UID'] )) {
        $aFields = $ooutputDocument->load( $_GET['OUT_DOC_UID'] );
    } else {
        $aFields = array ();
        $aFields['PRO_UID'] = $_GET['PRO_UID'];
    }
    
    $type = isset( $aFields['OUT_DOC_TYPE'] ) ? $aFields['OUT_DOC_TYPE'] : 'HTML';
    
    G::LoadClass( 'xmlfield_InputPM' );
    $G_PUBLISH = new Publisher();
    
    switch ($type) {
        case 'HTML':
            global $G_PUBLISH;
            $G_PUBLISH = new Publisher();
            $fcontent  = '';
            $proUid    = '';
            $filename  = '';
            $title  = '';
            require_once 'classes/model/OutputDocument.php';
            $oOutputDocument = new OutputDocument();
            if (isset( $_REQUEST['OUT_DOC_UID'] )) {
                $aFields = $oOutputDocument->load( $_REQUEST['OUT_DOC_UID'] );
                $fcontent = $aFields['OUT_DOC_TEMPLATE'];
                $proUid   = $aFields['PRO_UID'];
                $filename = $aFields['OUT_DOC_FILENAME'];
                $title    = $aFields['OUT_DOC_TITLE'];
            }
            $aData = Array (
                'PRO_UID' => $proUid,
                'OUT_DOC_TEMPLATE' => $fcontent,
                'FILENAME' => $filename,
                'OUT_DOC_UID'=> $_REQUEST['OUT_DOC_UID'],
                'OUT_DOC_TITLE'=> $title,
            );
            $G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'outputdocs/outputdocs_Edit', '', $aData );
            G::RenderPage( 'publish', 'blank' );
            die();
            break;
        case 'JRXML':
            break;
        case 'ACROFORM':
            $type = $aFields['OUT_DOC_TYPE'];
            if ($type == 'JRXML') {
                $extension = 'jrxml';
            }
            if ($type == 'ACROFORM') {
                $extension = 'pdf';
            }
                
            // The ereg_replace function has been DEPRECATED as of PHP 5.3.0.
            // $downFileName = ereg_replace('[^A-Za-z0-9_]', '_', $aFields['OUT_DOC_TITLE'] ) . '.' . $extension;
            $downFileName = preg_replace( '/[^A-Za-z0-9_]/i', '_', $aFields['OUT_DOC_TITLE'] ) . '.' . $extension;
            $filename = PATH_DYNAFORM . $aFields['PRO_UID'] . PATH_SEP . $aFields['OUT_DOC_UID'] . '.' . $extension;
            if (file_exists( $filename )) {
                $aFields['FILENAME'] = $downFileName;
            } else {
                $aFields['FILENAME'] = '';
            }
            
            $aFields['FILELINK'] = '../outputdocs/downloadFile?' . $aFields['OUT_DOC_UID'];
            $G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'outputdocs/outputdocsUploadFile', '', $aFields, '../outputdocs/uploadFile' );
            $G_PUBLISH->AddContent( 'view', 'outputdocs/editJrxml' );
            break;
    }
    G::RenderPage( 'publish', 'raw' );
} catch (Exception $oException) {
    die( $oException->getMessage() );
}

