<?php
/**
 * steps_Save.php
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
    require_once 'classes/model/Step.php';
    $oStep = new Step();
    $sStepUID = $oStep->create( array ('PRO_UID' => $_POST['sProcess'],'TAS_UID' => $_POST['sTask']
    ) );
    $oStep->update( array ('STEP_UID' => $sStepUID,'STEP_TYPE_OBJ' => $_POST['sType'],'STEP_UID_OBJ' => $_POST['sUID'],'STEP_POSITION' => ($oStep->getNextPosition( $_POST['sTask'] ) - 1),'STEP_MODE' => (isset( $_POST['sMode'] )) ? $_POST['sMode'] : 'EDIT'
    ) );
    G::auditlog("SaveNewStep","Save New Step -> ".$_POST['sUID'].' In Task -> '.$_POST['sTask'].' Type Step -> '.$_POST['sType']);

    $oProcessMap = new ProcessMap();
    $oProcessMap->getStepsCriteria( $_POST['sTask'] );
} catch (Exception $oException) {
    $token = strtotime("now");
    PMException::registerErrorLog($oException, $token);
    G::outRes( G::LoadTranslation("ID_EXCEPTION_LOG_INTERFAZ", array($token)) );
    die;
}

