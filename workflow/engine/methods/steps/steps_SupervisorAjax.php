<?php
/**
 * steps_SupervisorAjax.php
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

    $oProcessMap = new ProcessMap();
        
    $infoProcess = new Processes();
    $resultProcess = $infoProcess->getProcessRow($_POST['PRO_UID']);

    switch ($_POST['action']) {
        case 'availableSupervisorDynaforms':
            $oProcessMap->availableSupervisorDynaforms( $_POST['PRO_UID'] );
            break;
        case 'assignSupervisorDynaform':
            $oProcessMap->assignSupervisorStep( $_POST['PRO_UID'], 'DYNAFORM', $_POST['DYN_UID'] );
            G::auditLog('AssignSupervisorDynaform','Assign Supervisor Dynaform ('.$_POST['DYN_UID'].') in Process "'.$resultProcess['PRO_TITLE'].'"');
            break;
        case 'removeSupervisorDynaform':
            $oProcessMap->removeSupervisorStep( $_POST['STEP_UID'], $_POST['PRO_UID'], 'DYNAFORM', $_POST['DYN_UID'], $_POST['STEP_POSITION'] );
            G::auditLog('RemoveSupervisorDynaform','Remove Supervisor Dynaform ('.$_POST['DYN_UID'].') in Process "'.$resultProcess['PRO_TITLE'].'"');
            break;
        case 'availableSupervisorInputs':
            $oProcessMap->availableSupervisorInputs( $_POST['PRO_UID'] );
            break;
        case 'assignSupervisorInput':
            $oProcessMap->assignSupervisorStep( $_POST['PRO_UID'], 'INPUT_DOCUMENT', $_POST['INP_DOC_UID'] );
            G::auditLog('AssignSupervisorInput','Assign Supervisor Input ('.$_POST['INP_DOC_UID'].') in Process "'.$resultProcess['PRO_TITLE'].'"');
            break;
        case 'removeSupervisorInput':
            $oProcessMap->removeSupervisorStep( $_POST['STEP_UID'], $_POST['PRO_UID'], 'INPUT_DOCUMENT', $_POST['INP_DOC_UID'], $_POST['STEP_POSITION'] );
            G::auditLog('RemoveSupervisorInput','Remove Supervisor Input ('.$_POST['INP_DOC_UID'].') in Process "'.$resultProcess['PRO_TITLE'].'"');
            break;
    }
} catch (Exception $oException) {
    $token = strtotime("now");
    PMException::registerErrorLog($oException, $token);
    G::outRes( G::LoadTranslation("ID_EXCEPTION_LOG_INTERFAZ", array($token)) );
    die;
}

