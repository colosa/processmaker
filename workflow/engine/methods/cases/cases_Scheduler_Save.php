<?php
/**
 * cases_Scheduler_Save.php
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
    /*
   global $RBAC;
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
    $oCaseScheduler = new CaseScheduler();

    if (empty( $_POST )) {
        die( 'The information sended is empty!' );
    }

    $aData['SCH_UID'] = G::generateUniqueID();
    $aData['SCH_NAME'] = $_POST['form']['SCH_NAME'];
    $aData['SCH_DEL_USER_NAME'] = $_POST['form']['SCH_USER_NAME'];
    $aData['SCH_DEL_USER_PASS'] = md5( $_POST['form']['SCH_USER_PASSWORD'] );
    $aData['SCH_DEL_USER_UID'] = $_POST['form']['SCH_USER_UID'];
    $aData['PRO_UID'] = $_POST['form']['PRO_UID'];
    $aData['TAS_UID'] = $_POST['form']['TAS_UID'];

    $aData['SCH_STATE'] = 'ACTIVE';
    $aData['SCH_LAST_STATE'] = 'CREATED'; // 'ACTIVE';
    $aData['USR_UID'] = $_SESSION['USER_LOGGED'];

    $sOption = $_POST['form']['SCH_OPTION'];
    $aData['SCH_OPTION'] = $sOption;

    if ($_POST['form']['SCH_START_DATE'] != '') {
        $sDateTmp = $_POST['form']['SCH_START_DATE'];
    } else {
        $sDateTmp = date( 'Y-m-d' );
    }
    $sTimeTmp = $_POST['form']['SCH_START_TIME'];
    $aData['SCH_START_TIME'] = date( 'Y-m-d', strtotime( $sDateTmp ) ) . ' ' . date( 'H:i:s', strtotime( $sTimeTmp ) );
    $aData['SCH_START_DATE'] = date( 'Y-m-d', strtotime( $sDateTmp ) ) . ' ' . date( 'H:i:s', strtotime( $sTimeTmp ) );

    $nActualTime = $_POST['form']['SCH_START_TIME']; // time();
    // $nActualDate = date("Y-m-d  H:i:s", $nActualTime);


    $sValue = '';
    $sDaysPerformTask = '';
    $sWeeks = '';
    $sMonths = '';
    $sMonths = '';
    $sStartDay = '';
    $nSW = 0;
    $aData['SCH_START_DAY'] = '';
    $aData['SCH_REPEAT_EVERY'] = '';
    $aData['SCH_REPEAT_UNTIL'] = '';
    $aData['SCH_DAYS_PERFORM_TASK'] = '';
    switch ($sOption) {
        case '1': // Option 1
            $sValue = $_POST['form']['SCH_DAYS_PERFORM_TASK'];
            switch ($sValue) {
                case '1':
                    $aData['SCH_DAYS_PERFORM_TASK'] = $_POST['form']['SCH_DAYS_PERFORM_TASK'] . '|1';
                    break;
                case '2':
                    $aData['SCH_OPTION'] = '2';
                    $aData['SCH_EVERY_DAYS'] = '1';
                    $aData['SCH_WEEK_DAYS'] = '1|2|3|4|5|';
                    break;
                case '3': // Every [n] Days
                    $sDaysPerformTask = $_POST['form']['SCH_DAYS_PERFORM_TASK_OPT_3'];
                    $aData['SCH_DAYS_PERFORM_TASK'] = $_POST['form']['SCH_DAYS_PERFORM_TASK'] . '|' . $_POST['form']['SCH_DAYS_PERFORM_TASK_OPT_3'];
                    break;
            }
            break;

        case '2': // If the option is zero, set by default 1
            if (empty( $_POST['form']['SCH_EVERY_DAYS'] ))
                $nEveryDays = 1;
            else
                $nEveryDays = $_POST['form']['SCH_EVERY_DAYS'];
            $aData['SCH_EVERY_DAYS'] = $nEveryDays;
            $sWeeks = '';
            if (! empty( $_POST['form']['SCH_WEEK_DAYS'] )) {
                $aWeekDays = $_POST['form']['SCH_WEEK_DAYS'];
                foreach ($aWeekDays as $value) {
                    $sWeeks = $sWeeks . $value . '|';
                }
            }
            if (! empty( $_POST['form']['SCH_WEEK_DAYS_2'] )) {
                $aWeekDays2 = $_POST['form']['SCH_WEEK_DAYS_2'];
                foreach ($aWeekDays2 as $value) {
                    $sWeeks = $sWeeks . $value . '|';
                }
            }
            $sStartTime = $_POST['form']['SCH_START_TIME'];
            $aData['SCH_WEEK_DAYS'] = $sWeeks;

            break;
        case '3':
            $nStartDay = $_POST['form']['SCH_START_DAY'];
            if ($nStartDay == 1) {
                $aData['SCH_START_DAY'] = $nStartDay . '|' . $_POST['form']['SCH_START_DAY_OPT_1'];
            } else {
                $aData['SCH_START_DAY'] = $nStartDay . '|' . $_POST['form']['SCH_START_DAY_OPT_2_WEEKS'] . '|' . $_POST['form']['SCH_START_DAY_OPT_2_DAYS_WEEK'];
            }

            $sMonths = '';
            if (! empty( $_POST['form']['SCH_MONTHS'] )) {
                $aMonths = $_POST['form']['SCH_MONTHS'];
                foreach ($aMonths as $value) {
                    $sMonths = $sMonths . $value . '|';
                }
            }
            if (! empty( $_POST['form']['SCH_MONTHS_2'] )) {
                $aMonths2 = $_POST['form']['SCH_MONTHS_2'];
                foreach ($aMonths2 as $value) {
                    $sMonths = $sMonths . $value . '|';
                }
            }
            if (! empty( $_POST['form']['SCH_MONTHS_3'] )) {
                $aMonths3 = $_POST['form']['SCH_MONTHS_3'];
                foreach ($aMonths3 as $value) {
                    $sMonths = $sMonths . $value . '|';
                }
            }
            $aData['SCH_MONTHS'] = $sMonths;
            $sStartDay = $aData['SCH_START_DAY'];
            $sValue = $nStartDay;
            break;

    }
    echo "<br>sOption: " . $sOption;
    if (($sOption != '1') && ($sOption != '4') && ($sOption != '5')) {
        if ($sStartDay == '') {
            $sStartDay = date( 'Y-m-d' );
        }
        //                echo $sOption."*". $sValue."*". $nActualTime."*". $sDaysPerformTask."*". $sWeeks."*". $sStartDay ."*". $sMonths."<br>";
        $dCurrentDay = date( "d" );
        $dCurrentMonth = date( "m" );
        $aStartDay = explode( "|", $aData['SCH_START_DAY'] );
        if ($sOption == '3' && $aStartDay[0] == '1') {
            $monthsArray = explode( "|", $sMonths );
            foreach ($monthsArray as $row) {
                if ($dCurrentMonth == $row && $dCurrentDay < $aStartDay[1]) {
                    $startTime = $_POST['form']['SCH_START_TIME'] . ":00";
                    $aData['SCH_TIME_NEXT_RUN'] = date( 'Y' ) . '-' . $row . '-' . $aStartDay[1] . ' ' . $startTime;
                    break;
                } else {
                    $aData['SCH_TIME_NEXT_RUN'] = $oCaseScheduler->updateNextRun( $sOption, $sValue, $nActualTime, $sDaysPerformTask, $sWeeks, $sStartDay, $sMonths, $sDateTmp );
                }
            }
        } else {
            $aData['SCH_TIME_NEXT_RUN'] = $oCaseScheduler->updateNextRun( $sOption, $sValue, $nActualTime, $sDaysPerformTask, $sWeeks, $sStartDay, $sMonths, $sDateTmp );
        }
        //                print_r ($aData['SCH_TIME_NEXT_RUN']);
        //                die;
    } else {
        if ($sOption == '4') {
            $aData['SCH_END_DATE'] = $aData['SCH_START_TIME'];
        }
        $aData['SCH_TIME_NEXT_RUN'] = $aData['SCH_START_TIME'];
        if ($sOption == 5) {
            $aData['SCH_START_TIME'] = time();
            $aData['SCH_START_DATE'] = $aData['SCH_START_TIME'];
            $nextRun = $_POST['form']['SCH_REPEAT_EVERY'] * 60 * 60;
            $aData['SCH_REPEAT_EVERY'] = $_POST['form']['SCH_REPEAT_EVERY'];
            $date = $aData['SCH_START_TIME'];
            $date += $nextRun;
            $date = date( "Y-m-d H:i", $date );
            $aData['SCH_TIME_NEXT_RUN'] = $date;
        }
    }
    if (trim( $_POST['form']['SCH_END_DATE'] ) != '') {
        $aData['SCH_END_DATE'] = $_POST['form']['SCH_END_DATE'];
    }

    if (! empty( $_POST['form']['SCH_REPEAT_TASK_CHK'] )) {
        $nOptEvery = $_POST['form']['SCH_REPEAT_EVERY_OPT'];
        if ($nOptEvery == 2)
            $aData['SCH_REPEAT_EVERY'] = $_POST['form']['SCH_REPEAT_EVERY'] * 60;
        else
            $aData['SCH_REPEAT_EVERY'] = $_POST['form']['SCH_REPEAT_EVERY'];

    }

    if ((isset( $_POST['form']['CASE_SH_PLUGIN_UID'] )) && ($_POST['form']['CASE_SH_PLUGIN_UID'] != "")) {
        $aData['CASE_SH_PLUGIN_UID'] = $_POST['form']['CASE_SH_PLUGIN_UID'];
    }
    //$aData['SCH_END_DATE'] = "2020-12-30";
    $oCaseScheduler->create( $aData );
    $sch_uid = $oCaseScheduler->getSchUid();

    if ((isset( $_POST['form']['CASE_SH_PLUGIN_UID'] )) && ($_POST['form']['CASE_SH_PLUGIN_UID'] != "")) {
        $params = explode( "--", $_REQUEST['form']['CASE_SH_PLUGIN_UID'] );
        $oPluginRegistry = & PMPluginRegistry::getSingleton();
        $activePluginsForCaseScheduler = $oPluginRegistry->getCaseSchedulerPlugins();

        foreach ($activePluginsForCaseScheduler as $key => $caseSchedulerPluginDetail) {
            if (($caseSchedulerPluginDetail->sNamespace == $params[0]) && ($caseSchedulerPluginDetail->sActionId == $params[1])) {
                $caseSchedulerSelected = $caseSchedulerPluginDetail;

            }
        }
        if ((isset( $caseSchedulerSelected )) && (is_object( $caseSchedulerSelected ))) {
            //Save the form
            $oData = $_POST['pluginFields'];
            $oData['SCH_UID'] = $aData['SCH_UID'];
            $oPluginRegistry->executeMethod( $caseSchedulerPluginDetail->sNamespace, $caseSchedulerPluginDetail->sActionSave, $oData );
        }
    }

    //  //Added by Qennix
    //  //Update Start Time Event in BPMN
    //
    //  echo $_POST['form']['TAS_UID']."<<----";
    //  if (isset($_POST['form']['TAS_UID'])){
    //  require_once 'classes/model/Event.php';
    //  require_once 'classes/model/Task.php';
    //  echo $_POST['form']['TAS_UID']."<<----";
    //
    //  $oTask = new Task();
    //  $oTask->load($_POST['form']['TAS_UID']);
    //  $evn_uid = $oTask->getStartingEvent();
    //  $event = new Event();
    //  $editEvent = array();
    //  $editEvent['EVN_UID'] = $evn_uid;
    //  $editEvent['EVN_ACTION'] = $sch_uid;
    //  $event->update($editEvent);
    //  //End Adding
    //  }


    G::header( 'location: cases_Scheduler_List?PRO_UID=' . $_POST['form']['PRO_UID'] );

} catch (Exception $oException) {
    die( $oException->getMessage() );
}

