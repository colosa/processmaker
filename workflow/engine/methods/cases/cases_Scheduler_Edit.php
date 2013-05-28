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

    require_once 'classes/model/CaseScheduler.php';
    require_once 'classes/model/Process.php';
    require_once 'classes/model/Task.php';

    //	$G_MAIN_MENU           = 'processmaker';
    //	$G_ID_MENU_SELECTED    = 'CASES';
    $G_PUBLISH = new Publisher();

    G::LoadClass( 'case' );

    /* Prepare page before to show */

    $oCaseScheduler = new CaseScheduler();
    $aFields = $oCaseScheduler->load( $_GET['SCH_UID'] );

    $aFields['UID_SCHEDULER'] = "scheduler";

    // load according the scheduler option selected daily/weekly/monthly/one time
    $nOpt = $aFields['SCH_OPTION'];
    switch ($nOpt) {
        case 1:
            $aStartDay = explode( '|', $aFields['SCH_DAYS_PERFORM_TASK'] );
            if ($aStartDay[0] != 3) {
                $aFields['SCH_DAYS_PERFORM_TASK'] = $aStartDay[0];
            } else {
                $aFields['SCH_DAYS_PERFORM_TASK'] = $aStartDay[0];
                $aFields['SCH_DAYS_PERFORM_TASK_OPT_3'] = $aStartDay[1];
            }

            break;
        case 2:
            $aFields['SCH_WEEK_DAYS_2'] = $aFields['SCH_WEEK_DAYS'];
            break;
        case 3: // $nStartDay = $aFields['SCH_START_DAY'];
            $aStartDay = explode( '|', $aFields['SCH_START_DAY'] );
            if ($aStartDay[0] == 1) {
                $aFields['SCH_START_DAY_OPT_1'] = $aStartDay[1];
            } else {
                $aFields['SCH_START_DAY_OPT_2_WEEKS'] = $aStartDay[1];
                $aFields['SCH_START_DAY_OPT_2_DAYS_WEEK'] = $aStartDay[2];
            }
            $aFields['SCH_START_DAY'] = $aStartDay[0];
            $aFields['SCH_MONTHS_2'] = $aFields['SCH_MONTHS'];
            $aFields['SCH_MONTHS_3'] = $aFields['SCH_MONTHS'];
            break;
        case 4:

            break;
    }

    $aFields['SCH_START_TIME'] = date( 'H:i', strtotime( $aFields['SCH_START_TIME'] ) );
    $aFields['PREV_SCH_START_TIME'] = $aFields['SCH_START_TIME'];

    $aFields['SCH_START_DATE'] = date( 'Y-m-d', strtotime( $aFields['SCH_START_DATE'] ) );
    $aFields['PREV_SCH_START_DATE'] = $aFields['SCH_START_DATE'];

    if (! empty( $aFields['SCH_END_DATE'] )) {
        $aFields['SCH_END_DATE'] = date( 'Y-m-d', strtotime( $aFields['SCH_END_DATE'] ) );
        $aFields['PREV_SCH_END_DATE'] = date( 'Y-m-d', strtotime( $aFields['SCH_END_DATE'] ) );
    }
    if ($aFields['SCH_REPEAT_STOP_IF_RUNNING'] == 0 || $aFields['SCH_REPEAT_STOP_IF_RUNNING'] == null) {
        $aFields['SCH_REPEAT_STOP_IF_RUNNING'] = null;
    } else {
        $aFields['SCH_REPEAT_STOP_IF_RUNNING'] = 'On';
    }

    $aFields['SCH_USER_NAME'] = $aFields['SCH_DEL_USER_NAME'];
    $aFields['SCH_USER_PASSWORD'] = 'DefaultPM';
    $aFields['SCH_USER_UID'] = $aFields['SCH_DEL_USER_UID'];
    $aFields['SCH_START_DATE'] = date( "Y-m-d", strtotime( $aFields['SCH_START_DATE'] ) );

    // validating if any of the advanced fields is non empty
    //        var_dump($aFields['SCH_END_DATE']);
    //        var_dump($aFields['SCH_REPEAT_EVERY']);
    //        die();
    if ($aFields['SCH_END_DATE'] != null || trim( $aFields['SCH_REPEAT_EVERY'] ) != '') {
        $aFields['SCH_ADVANCED'] = 'true';
    } else {
        $aFields['SCH_ADVANCED'] = 'false';
    }

    $aFields['PRO_UID_TMP'] = isset( $_GET['PRO_UID'] ) ? $_GET['PRO_UID'] : $_SESSION['PROCESS'];
    $aFields['PHP_START_DATE'] = date( 'Y-m-d' );
    $aFields['PHP_END_DATE'] = date( 'Y-m-d', mktime( 0, 0, 0, date( 'm' ), date( 'd' ), date( 'Y' ) + 5 ) );

    $aFields['SCH_LIST'] = '';
    foreach ($_SESSION['_DBArray']['cases_scheduler'] as $key => $item) {
        $aFields['SCH_LIST'] .= ($item['SCH_UID'] != $_GET['SCH_UID']) ? $item['SCH_NAME'] . '|' : '' ;
    }


    $G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'cases/cases_Scheduler_Edit.xml', '', $aFields, 'cases_Scheduler_Update' );

    G::RenderPage( 'publishBlank', 'blank' );

} catch (Exception $oException) {
    die( $oException->getMessage() );
}

