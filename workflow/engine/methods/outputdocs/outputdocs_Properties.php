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
    $type = isset( $aFields['OUT_DOC_TYPE'] ) ? $aFields['OUT_DOC_TYPE'] : 'HTML';
    $aFields['OUT_DOC_TAGS'] = isset( $aFields['OUT_DOC_TAGS'] ) ? $aFields['OUT_DOC_TAGS'] : 'OUTPUT';
    $aFields['OUT_DOC_VERSIONING'] = strval( $aFields['OUT_DOC_VERSIONING'] );
    $aFields['OUT_DOC_LANDSCAPE'] = strval( $aFields['OUT_DOC_LANDSCAPE'] );
    if (isset( $aFields['OUT_DOC_PDF_SECURITY_ENABLED'] )) {
        $aFields['OUT_DOC_PDF_SECURITY_ENABLED'] = strval( $aFields['OUT_DOC_PDF_SECURITY_ENABLED'] );
    }
    if (isset( $aFields['OUT_DOC_PDF_SECURITY_OPEN_PASSWORD'] ) && $aFields['OUT_DOC_PDF_SECURITY_OPEN_PASSWORD'] != "") {
        $aFields['OUT_DOC_PDF_SECURITY_OPEN_PASSWORD'] = G::decrypt( $aFields['OUT_DOC_PDF_SECURITY_OPEN_PASSWORD'], $_GET['OUT_DOC_UID'] );
        $aFields['OUT_DOC_PDF_SECURITY_OWNER_PASSWORD'] = G::decrypt( $aFields['OUT_DOC_PDF_SECURITY_OWNER_PASSWORD'], $_GET['OUT_DOC_UID'] );
    }

    $G_PUBLISH = new Publisher();
    switch ($type) {
        case 'HTML':
            $G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'outputdocs/outputdocs_Properties', '', $aFields, '../outputdocs/outputdocs_Save' );
            break;
        case 'JRXML':
            $G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'outputdocs/outputdocs_Properties', '', $aFields, '../outputdocs/outputdocs_Save' );

            break;
        case 'ACROFORM':
            $G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'outputdocs/outputdocs_Properties', '', $aFields, '../outputdocs/outputdocs_Save' );
            break;
    }
    G::RenderPage( 'publish', 'raw' );
} catch (Exception $oException) {
    $token = strtotime("now");
    PMException::registerErrorLog($oException, $token);
    G::outRes( G::LoadTranslation("ID_EXCEPTION_LOG_INTERFAZ", array($token)) );
    die;
}

