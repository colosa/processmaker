<?php
/**
 * inputdocs_Delete.php
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

    if (isset( $_POST['function'] )) {
        $sfunction = $_POST['function'];
    } else {
        $sfunction = $_POST['functions'];
    }
    $result = new stdclass();
    switch ($sfunction) {
        case 'getRelationInfDoc':
            try {
                $oStepSupervisor = new StepSupervisor();
                $fields2 = $oStepSupervisor->loadInfo( $_POST['INP_DOC_UID'] );
                $result->passed = true;
                if (is_array( $fields2 )) {
                    $result->passed = false;
                }
                $result->success = true;
                $result->msg = $result->passed ? '' : G::LoadTranslation( 'ID_INPUTDOCUMENT_TASK_RELATION_EXISTS' );
            } catch (Exception $e) {
                $result->success = false;
                $result->passed = false;
                $result->msg = $e->getMessage();
            }
            print G::json_encode( $result );
            break;
        case 'deleteInputDocument':
            try {
                $oStepSupervisor = new StepSupervisor();
                $fields2 = $oStepSupervisor->loadInfo( $_POST['INP_DOC_UID'] );
                $oStepSupervisor->remove( $fields2['STEP_UID'] );

                $oPermission = new ObjectPermission();
                $fields3 = $oPermission->loadInfo( $_POST['INP_DOC_UID'] );
                if (is_array( $fields3 )) {
                    $oPermission->remove( $fields3['OP_UID'] );
                }

                $oInputDocument = new InputDocument();
                $fields = $oInputDocument->load( $_POST['INP_DOC_UID'] );

                $oInputDocument->remove( $_POST['INP_DOC_UID'] );

                $oStep = new Step();
                $oStep->removeStep( 'INPUT_DOCUMENT', $_POST['INP_DOC_UID'] );

                $oOP = new ObjectPermission();
                $oOP->removeByObject( 'INPUT', $_POST['INP_DOC_UID'] );

                //refresh dbarray with the last change in inputDocument
                $oMap = new ProcessMap();
                $oCriteria = $oMap->getInputDocumentsCriteria( $fields['PRO_UID'] );

                $result->success = true;
                $result->msg = G::LoadTranslation( 'ID_INPUTDOCUMENT_REMOVED' );
            } catch (Exception $e) {
                $result->success = false;
                $result->msg = $e->getMessage();
            }
            print G::json_encode( $result );
            break;
    }
} catch (Exception $oException) {
    $token = strtotime("now");
    PMException::registerErrorLog($oException, $token);
    G::outRes( G::LoadTranslation("ID_EXCEPTION_LOG_INTERFAZ", array($token)) );
    die;
}

