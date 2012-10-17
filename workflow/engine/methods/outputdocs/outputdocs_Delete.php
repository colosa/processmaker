<?php
/**
 * outputdocs_Delete.php
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
    require_once 'classes/model/ObjectPermission.php';
    require_once 'classes/model/Step.php';
    G::LoadClass( 'processMap' );
    $oOutputDocument = new OutputDocument();
    $fields = $oOutputDocument->load( $_POST['OUT_DOC_UID'] );
    $oOutputDocument->remove( $_POST['OUT_DOC_UID'] );
    $oStep = new Step();
    $oStep->removeStep( 'OUTPUT_DOCUMENT', $_POST['OUT_DOC_UID'] );
    $oOP = new ObjectPermission();
    $oOP->removeByObject( 'OUTPUT', $_POST['OUT_DOC_UID'] );
    //refresh dbarray with the last change in outputDocument
    $oMap = new processMap();
    $oCriteria = $oMap->getOutputDocumentsCriteria( $fields['PRO_UID'] );
    $result->success = true;
    $result->msg = G::LoadTranslation( 'ID_OUTPUTDOCUMENT_REMOVED' );
} catch (Exception $e) {
    $result->success = false;
    $result->msg = $e->getMessage();
    //die($oException->getMessage());
}
print G::json_encode( $result );

