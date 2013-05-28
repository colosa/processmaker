<?php
/**
 * cases_SchedulerNew.php
 *
 * ProcessMaker Open Source Edition
 * Copyright (C) 2004 - 2010 Colosa Inc.23
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

    /*
    switch ($RBAC->userCanAccess('PM_FACTORY'))
    {
    case -2:
      G::SendTemporalMessage('ID_USER_HAVENT_RIGHTS_SYSTEM', 'error', 'labels');
      G::header('location: ../login/login');
      die;
    break;
    case -1:
      G::SendTemporalMessage('ID_USER_HAVENT_RIGHTS_PAGE', 'error', 'labels');
      G::header('location: ../login/login');
      die;
    break;
    }
    */
    /*
    $aFields['MESSAGE0']   = str_replace("\r\n","<br>",G::LoadTranslation('ID_USER_REGISTERED')) . '!';
    $aFields['MESSAGE1']   = str_replace("\r\n","<br>",G::LoadTranslation('ID_MSG_ERROR_USR_USERNAME'));
    $aFields['MESSAGE2']   = str_replace("\r\n","<br>",G::LoadTranslation('ID_MSG_ERROR_DUE_DATE'));
    $aFields['MESSAGE3']   = str_replace("\r\n","<br>",G::LoadTranslation('ID_NEW_PASS_SAME_OLD_PASS'));
    $aFields['MESSAGE4']   = str_replace("\r\n","<br>",G::LoadTranslation('ID_MSG_ERROR_USR_FIRSTNAME'));
    $aFields['MESSAGE5']   = str_replace("\r\n","<br>",G::LoadTranslation('ID_MSG_ERROR_USR_LASTNAME'));
    // the default role variable sets the value that will be showed as the default for the role field.
    $aFields['DEFAULT_ROLE']   = 'PROCESSMAKER_OPERATOR';
    $aFields['START_DATE'] = date('Y-m-d');
    $aFields['END_DATE']   = date('Y-m-d', mktime(0, 0, 0, date('m'), date('d'), date('Y') + 5));
    $aFields['USR_DUE_DATE']= date('Y-m-d', mktime(0, 0, 0, date('m'), date('d'), date('Y') + 1));
    */

    require_once 'classes/model/CaseScheduler.php';
    require_once 'classes/model/Process.php';
    require_once 'classes/model/Task.php';

    $G_MAIN_MENU = 'processmaker';
    $G_SUB_MENU = 'cases';

    $G_ID_MENU_SELECTED = 'CASES';
    $G_ID_SUB_MENU_SELECTED = 'CASES_SCHEDULER';

    $G_PUBLISH = new Publisher();

    G::LoadClass( 'case' );
    $aFields['PHP_START_DATE'] = date( 'Y-m-d' );
    $aFields['PRO_UID'] = isset( $_GET['PRO_UID'] ) ? $_GET['PRO_UID'] : $_SESSION['PROCESS'];
    $aFields['PHP_CURRENT_DATE'] = $aFields['PHP_START_DATE'];
    $aFields['PHP_END_DATE'] = date( 'Y-m-d', mktime( 0, 0, 0, date( 'm' ), date( 'd' ), date( 'Y' ) + 5 ) );

    /* Prepare page before to show */

    /*-- Base
    $aFields = array();
    $oCase = new Cases();
    $_DBArray['NewCase'] = $oCase->getStartCases( $_SESSION['USER_LOGGED'] );
    */

    $oCaseScheduler = new CaseScheduler();
    //$_DBArray['NewProcess'] = $oCaseScheduler->getProcessDescription();
    //$_DBArray['NewTask'] = $oCaseScheduler->getTaskDescription();
    // var_dump($oCaseScheduler->getAllProcess()); die;

    $aFields['UID_SCHEDULER'] = "scheduler";

    $aFields['SCH_LIST'] = '';
    foreach ($_SESSION['_DBArray']['cases_scheduler'] as $key => $item) {
        $aFields['SCH_LIST'] .=  $item['SCH_NAME'] . '|';
    }

    $G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'cases/cases_Scheduler_New.xml', '', $aFields, 'cases_Scheduler_Save' );
    G::RenderPage( 'publishBlank', 'blank' );

} catch (Exception $oException) {
    die( $oException->getMessage() );
}

