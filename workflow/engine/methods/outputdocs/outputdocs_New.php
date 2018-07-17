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
    $aFields['OUT_DOC_TYPE'] = 'HTML';
    $enabledJavaBridge = false;

    $G_PUBLISH = new Publisher();
    if (! $enabledJavaBridge) {
        $xmlform = 'outputdocs/outputdocs_Properties';
        $G_PUBLISH->AddContent( 'xmlform', 'xmlform', $xmlform, '', $aFields, '../outputdocs/outputdocs_Save' );
    } else {
        $xmlform = 'outputdocs/outputdocs_New';
        $G_PUBLISH->AddContent( 'xmlform', 'xmlform', $xmlform, '', $aFields, '../outputdocs/outputdocs_Save' );
    }
    G::RenderPage( 'publish', 'raw' );
} catch (Exception $oException) {
    $token = strtotime("now");
    PMException::registerErrorLog($oException, $token);
    G::outRes( G::LoadTranslation("ID_EXCEPTION_LOG_INTERFAZ", array($token)) );
    die;
}

