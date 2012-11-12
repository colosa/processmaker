<?php

/**
 * outputdocs_Save.php
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

    //default:


    require_once 'classes/model/OutputDocument.php';
    G::LoadClass( 'processMap' );

    $oOutputDocument = new OutputDocument();

    if (isset( $_POST['form'] ))
        $aData = $_POST['form']; //For old process map form
    else
        $aData = $_POST; //For Extjs (Since we are not using form in ExtJS)


    if (isset( $aData['OUT_DOC_TITLE'] ) && $aData['OUT_DOC_TITLE'] != '') {
        $oForm = new Form( 'outputdocs/outputdocs_Properties', PATH_XMLFORM );
        $aData = $oForm->validatePost();
        if (isset( $aData['OUT_DOC_PDF_SECURITY_ENABLED'] ) && $aData['OUT_DOC_PDF_SECURITY_ENABLED'] == "0") {
            $aData['OUT_DOC_PDF_SECURITY_OPEN_PASSWORD'] = "";
            $aData['OUT_DOC_PDF_SECURITY_OWNER_PASSWORD'] = "";
            $aData['OUT_DOC_PDF_SECURITY_PERMISSIONS'] = "";
        }
        if (isset( $aData['OUT_DOC_PDF_SECURITY_OPEN_PASSWORD'] ) && $aData['OUT_DOC_PDF_SECURITY_OPEN_PASSWORD'] != "") {
            $aData['OUT_DOC_PDF_SECURITY_OPEN_PASSWORD'] = G::encrypt( $aData['OUT_DOC_PDF_SECURITY_OPEN_PASSWORD'], $aData['OUT_DOC_UID'] );
            $aData['OUT_DOC_PDF_SECURITY_OWNER_PASSWORD'] = G::encrypt( $aData['OUT_DOC_PDF_SECURITY_OWNER_PASSWORD'], $aData['OUT_DOC_UID'] );
        }
    }
    if ($aData['OUT_DOC_UID'] == '') {

        if ((isset( $aData['OUT_DOC_TYPE'] )) && ($aData['OUT_DOC_TYPE'] == 'JRXML')) {
            $dynaformUid = $aData['DYN_UID'];
            $outDocUid = $oOutputDocument->create( $aData );
            G::LoadClass( 'javaBridgePM' );
            $jbpm = new JavaBridgePM();
            print $jbpm->generateJrxmlFromDynaform( $outDocUid, $dynaformUid, 'classic' );
        } else {
            $outDocUid = $oOutputDocument->create( $aData );
        }
    } else {
        $oOutputDocument->update( $aData );
    }

    if (isset( $aData['PRO_UID'] )) {
        //refresh dbarray with the last change in outputDocument
        $oMap = new processMap();
        $oCriteria = $oMap->getOutputDocumentsCriteria( $aData['PRO_UID'] );
    }

} catch (Exception $oException) {
    die( $oException->getMessage() );
}

