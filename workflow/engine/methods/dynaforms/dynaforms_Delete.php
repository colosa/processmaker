<?php
/**
 * dynaforms_Delete.php
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
if (($RBAC_Response = $RBAC->userCanAccess( "PM_FACTORY" )) != 1) {
    return $RBAC_Response;
}

require_once ('classes/model/Dynaform.php');
require_once 'classes/model/ObjectPermission.php';
require_once 'classes/model/Step.php';
require_once 'classes/model/StepSupervisor.php';
require_once 'classes/model/CaseTrackerObject.php';

/*
In here we are deleting all datas about this Dynaform into DB
*/

if (isset( $_POST['function'] )) {
    $sfunction = $_POST['function'];
} else {
    $sfunction = $_POST['functions'];
}

switch ($sfunction) {
    case 'getRelationInfDynaform':
        $oStepSupervisor = new StepSupervisor();
        $fields2 = $oStepSupervisor->loadInfo( $_POST['DYN_UID'] );
        $result = false;
        if (is_array( $fields2 )) {
            $result = true;
        }
        return print $result;
        break;
    case 'getDynaformAssign':
        $oStep = new Step();
        $aDependent = $oStep->loadInfoAssigDynaform( $_POST['PRO_UID'], $_POST['DYN_UID'] );
        $result = false;
        if (is_array( $aDependent )) {
            $result = true;
        }
        return print $result;
        break;
    case 'deleteDynaform':
        $dynaform = new Dynaform();

        if (! isset( $_POST['DYN_UID'] )) {
            return;
        }
        //in table dynaform
        $dynaform->remove( $_POST['DYN_UID'] );

        //in table Step
        $oStep = new Step();
        $oStep->removeStep( 'DYNAFORM', $_POST['DYN_UID'] );

        //in table ObjectPermission
        $oOP = new ObjectPermission();
        $oOP->removeByObject( 'DYNAFORM', $_POST['DYN_UID'] );

        //in table Step_supervisor
        $oSS = new StepSupervisor();
        $oSS->removeByObject( 'DYNAFORM', $_POST['DYN_UID'] );

        //in table case_tracker_object
        $oCTO = new CaseTrackerObject();
        $oCTO->removeByObject( 'DYNAFORM', $_POST['DYN_UID'] );
        break;

    /**
     * erik: new and improved methods
     */
    case 'getDynaformSupervisorRelations':
        try {
            $oStepSupervisor = new StepSupervisor();
            $DYN_UIDS = explode( ',', $_POST['DYN_UID'] );

            $result->passed = true;
            foreach ($DYN_UIDS as $i => $DYN_UID) {
                $relationsList = $oStepSupervisor->loadInfo( $DYN_UID );
                if (is_array( $relationsList )) {
                    $result->passed = false;
                    break;
                }
            }

            $result->success = true;
            $result->msg = $result->passed ? '' : G::LoadTranslation( 'ID_DYNAFORM_SUPERVISOR_RELATION_EXISTS' );
        } catch (Exception $e) {
            $result->success = false;
            $result->passed = false;
            $result->msg = $e->getMessage();
        }
        print G::json_encode( $result );
        break;
    case 'getDynaformTaskRelations':
        try {
            $oStepSupervisor = new StepSupervisor();
            $DYN_UIDS = explode( ',', $_POST['DYN_UID'] );
            $results = Array ();

            $result->passed = true;
            foreach ($DYN_UIDS as $i => $DYN_UID) {
                $oStep = new Step();
                $aDependent = $oStep->loadInfoAssigDynaform( $_POST['PRO_UID'], $DYN_UID );
                if (is_array( $aDependent )) {
                    $result->passed = false;
                    break;
                }
            }

            $result->success = true;
            $result->msg = $result->passed ? '' : G::LoadTranslation( 'ID_DYNAFORM_TASK_RELATION_EXISTS' );
        } catch (Exception $e) {
            $result->success = false;
            $result->msg = $e->getMessage();
        }
        print G::json_encode( $result );
        break;
    case 'removeDynaform':
        try {
            if (! isset( $_POST['DYN_UID'] )) {
                throw new Exception( 'DYN_UID was not set!' );
            }

            $DYN_UIDS = explode( ',', $_POST['DYN_UID'] );

            foreach ($DYN_UIDS as $i => $DYN_UID) {
                $dynaform = new Dynaform();
                //in table dynaform
                $dynaform->remove( $DYN_UID );

                //in table Step
                $oStep = new Step();
                $oStep->removeStep( 'DYNAFORM', $DYN_UID );

                //in table ObjectPermission
                $oOP = new ObjectPermission();
                $oOP->removeByObject( 'DYNAFORM', $DYN_UID );

                //in table Step_supervisor
                $oSS = new StepSupervisor();
                $oSS->removeByObject( 'DYNAFORM', $DYN_UID );

                //in table case_tracker_object
                $oCTO = new CaseTrackerObject();
                $oCTO->removeByObject( 'DYNAFORM', $DYN_UID );
            }

            $result->success = true;
            $result->msg = G::LoadTranslation( 'ID_DYNAFORM_REMOVED' );
        } catch (Exception $e) {
            $result->success = false;
            $result->msg = $e->getMessage();
        }
        print G::json_encode( $result );
        break;
}

