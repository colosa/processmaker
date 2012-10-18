<?php

class caseSchedulerProxy extends HttpProxyController
{

    function load ($params)
    {
        require_once "classes/model/Event.php";
        $PRO_UID = $params->PRO_UID;
        $EVN_UID = $params->EVN_UID;

        $oEvent = new Event();
        $oData = $oEvent->load( $EVN_UID );
        $sch_uid = $oData['EVN_ACTION'];

        if ($sch_uid != '') {
            G::LoadClass( 'processMap' );
            $oProcessMap = new processMap( new DBConnection() );
            $rows = $oProcessMap->caseNewSchedulerList( $sch_uid );
            if ($rows['SCH_OPTION'] == '3') {
                $sch_start_day = explode( '|', $rows['SCH_START_DAY'] );
                $count = count( $sch_start_day );
                switch ($count) {
                    case 1:
                        $rows['SCH_START_DAY'] = $sch_start_day[0];
                        break;
                    case 2:
                        $rows['SCH_START_DAY'] = $sch_start_day[0];
                        $rows['SCH_START_DAY_OPT_2_WEEKS'] = $sch_start_day[1];
                        break;
                    case 3:
                        $rows['SCH_START_DAY'] = $sch_start_day[0];
                        $rows['SCH_START_DAY_OPT_2_WEEKS'] = $sch_start_day[1];
                        $rows['SCH_START_DAY_OPT_2_DAYS_WEEK'] = $sch_start_day[2];
                        break;
                }
            }
            if ($rows['SCH_START_DATE'] != '') {
                $sch_str_dt = explode( ' ', $rows['SCH_START_DATE'] );
                $rows['SCH_START_DATE'] = $sch_str_dt[0];
            }
            if ($rows['SCH_END_DATE'] != '') {
                $sch_str_dt = explode( ' ', $rows['SCH_END_DATE'] );
                $rows['SCH_END_DATE'] = $sch_str_dt[0];
            }
            $this->success = true;
            $this->data = $rows;
        } else {
            $this->success = false;
        }
    }

    function delete ($params)
    {
        require_once 'classes/model/CaseScheduler.php';
        require_once 'classes/model/Event.php';
        $SCH_UID = $params->SCH_UID;
        $EVN_UID = $params->EVN_UID;
        $oCaseScheduler = new CaseScheduler();
        $oCaseScheduler->remove( $SCH_UID );
        $oEvent = new Event();
        $editE = array ();
        $editE['EVN_UID'] = $EVN_UID;
        $editE['EVN_ACTION'] = '';
        $oEvent->update( $editE );

        $this->success = true;
        $this->msg = G::LoadTranslation( 'ID_SCHEDULER_SUCCESS_DELETE' );
    }

    function changeStatus ($params)
    {
        require_once 'classes/model/CaseScheduler.php';
        $SCH_UID = $params->SCH_UID;
        $oCaseScheduler = new CaseScheduler();
        $oCaseScheduler->changeStatus( $SCH_UID );
        $oCaseScheduler->load( $SCH_UID );
        $this->success = true;
        $this->SCH_STATUS = $oCaseScheduler->getSchState();
        $this->msg = G::LoadTranslation( 'ID_SCHEDULER_SUCCESS_CHANGE_STATUS' );
    }

    function checkCredentials ($params)
    {
        require_once 'classes/model/Event.php';
        require_once 'classes/model/Users.php';
        require_once 'classes/model/TaskUser.php';
        require_once 'classes/model/GroupUser.php';
        $sPRO_UID = $params->PRO_UID;
        $sEVN_UID = $params->EVN_UID;
        $sWS_USER = trim( $params->WS_USER );
        $sWS_PASS = trim( $params->WS_PASS );

        if (G::is_https()) {
            $http = 'https://';
        } else {
            $http = 'http://';
        }
        $endpoint = $http . $_SERVER['HTTP_HOST'] . '/sys' . SYS_SYS . '/' . SYS_LANG . '/' . SYS_SKIN . '/services/wsdl2';
        @$client = new SoapClient( $endpoint );

        $user = $sWS_USER;
        $pass = $sWS_PASS;

        $parameters = array ('userid' => $user,'password' => $pass
        );
        $result = $client->__SoapCall( 'login', array ($parameters
        ) );

        $fields['status_code'] = $result->status_code;
        $fields['message'] = 'ProcessMaker WebService version: ' . $result->version . "\n" . $result->message;
        $fields['version'] = $result->version;
        $fields['time_stamp'] = $result->timestamp;
        $messageCode = true;
        $message = $result->message;

        G::LoadClass( 'Task' );
        //G::LoadClass ( 'Event' );
        G::LoadClass( 'User' );
        G::LoadClass( 'TaskUser' );
        G::LoadClass( 'Groupwf' );

        $event = new Event();
        $event->load( $sEVN_UID );
        $sTASKS = $event->getEvnTasUidTo();

        $task = new Task();
        $task->load( $sTASKS );
        $sTASKS_SEL = $task->getTasTitle();

        if (! class_exists( 'GroupUser' )) {
            G::LoadClass( 'GroupUser' );
        }
        // if the user has been authenticated, then check if has the rights or
        // permissions to create the webentry
        if ($result->status_code == 0) {
            $oCriteria = new Criteria( 'workflow' );
            $oCriteria->addSelectColumn( UsersPeer::USR_UID );
            $oCriteria->addSelectColumn( TaskUserPeer::USR_UID );
            $oCriteria->addSelectColumn( TaskUserPeer::TAS_UID );
            $oCriteria->addJoin( TaskUserPeer::USR_UID, UsersPeer::USR_UID, Criteria::LEFT_JOIN );
            $oCriteria->add( TaskUserPeer::TAS_UID, $sTASKS );
            $oCriteria->add( UsersPeer::USR_USERNAME, $sWS_USER );
            //$oCriteria->add(TaskUserPeer::TU_RELATION,1);
            $userIsAssigned = TaskUserPeer::doCount( $oCriteria );
            // if the user is not assigned directly, maybe a have the task a group with the user
            if ($userIsAssigned < 1) {
                $oCriteria = new Criteria( 'workflow' );
                $oCriteria->addSelectColumn( UsersPeer::USR_UID );
                $oCriteria->addJoin( UsersPeer::USR_UID, GroupUserPeer::USR_UID, Criteria::LEFT_JOIN );
                $oCriteria->addJoin( GroupUserPeer::GRP_UID, TaskUserPeer::USR_UID, Criteria::LEFT_JOIN );
                $oCriteria->add( TaskUserPeer::TAS_UID, $sTASKS );
                $oCriteria->add( UsersPeer::USR_USERNAME, $sWS_USER );
                $userIsAssigned = GroupUserPeer::doCount( $oCriteria );
                if (! ($userIsAssigned >= 1)) {
                    $messageCode = false;
                    $message = "The User \"" . $sWS_USER . "\" doesn't have the task \"" . $sTASKS_SEL . "\" assigned.";
                }
            }

        } else {
            $messageCode = false;
        }

        $this->success = $messageCode;
        $this->msg = $message;
    }

    function save ($params)
    {
        require_once 'classes/model/CaseScheduler.php';
        $oCaseScheduler = new CaseScheduler();

        $aData['SCH_UID'] = G::generateUniqueID();
        $aData['SCH_NAME'] = $params->fDescription; //$_POST['form']['SCH_NAME'];
        $aData['SCH_DEL_USER_NAME'] = $params->fUser; //$_POST['form']['SCH_USER_NAME'];
        $aData['SCH_DEL_USER_PASS'] = md5( $params->fPassword );
        $aData['SCH_DEL_USER_UID'] = $params->usr_uid; //$_POST['form']['SCH_USER_UID'];
        $aData['PRO_UID'] = $params->pro_uid; //$_POST['form']['PRO_UID'];
        $aData['TAS_UID'] = $params->tas_uid; //$_POST['form']['TAS_UID'];


        $aData['SCH_STATE'] = 'ACTIVE';
        $aData['SCH_LAST_STATE'] = 'CREATED'; // 'ACTIVE';
        $aData['USR_UID'] = $_SESSION['USER_LOGGED'];

        $sOption = $params->fType; //$_POST['form']['SCH_OPTION'];

        switch ($sOption) {
            case 'Daily':
                $sOption = '1';
                break;
            case 'Weekly':
                $sOption = '2';
                break;
            case 'Monthly':
                $sOption = '3';
                break;
            default:
                $sOption = '4';
                break;
        }

        $aData['SCH_OPTION'] = $sOption;

        //    if ($_POST['form']['SCH_START_DATE']!=''){
        //      $sDateTmp = $_POST['form']['SCH_START_DATE'];
        //    } else {
        //      $sDateTmp = date('Y-m-d');
        //    }


        $sDateTmp = $params->SCH_START_DATE;
        $sTimeTmp = $params->SCH_START_TIME; //$_POST['form']['SCH_START_TIME'];


        $aData['SCH_START_TIME'] = date( 'Y-m-d', strtotime( $sDateTmp ) ) . ' ' . date( 'H:i:s', strtotime( $sTimeTmp ) );
        $aData['SCH_START_DATE'] = date( 'Y-m-d', strtotime( $sDateTmp ) ) . ' ' . date( 'H:i:s', strtotime( $sTimeTmp ) );

        //g::pr($aData);


        $nActualTime = $sTimeTmp; //date("Y-m-d  H:i:s"); // time();
        //$nActualDate = date("Y-m-d  H:i:s");


        $sValue = '';
        $sDaysPerformTask = '';
        $sWeeks = '';
        $sMonths = '';
        $sMonths = '';
        $sStartDay = '';
        $nSW = 0;

        switch ($sOption) {
            case '1': // Option 1
                $sValue = isset( $params->SCH_DAYS_PERFORM_TASK ) ? $params->SCH_DAYS_PERFORM_TASK : '1'; //$_POST['form']['SCH_DAYS_PERFORM_TASK'];
                switch ($sValue) {
                    case '1':
                        $aData['SCH_DAYS_PERFORM_TASK'] = $sValue . '|1';
                        break;
                    case '2':
                        $aData['SCH_OPTION'] = '2';
                        $aData['SCH_EVERY_DAYS'] = '1';
                        $aData['SCH_WEEK_DAYS'] = '1|2|3|4|5|';
                        break;
                    case '3': // Every [n] Days
                        $sDaysPerformTask = $params->SCH_DAYS_PERFORM_TASK_OPT_3;
                        $aData['SCH_DAYS_PERFORM_TASK'] = $params->SCH_DAYS_PERFORM_TASK . '|' . $params->SCH_DAYS_PERFORM_TASK_OPT_3;
                        break;
                }
                break;
            case '2': // If the option is zero, set by default 1
                $sWeeks = '';
                if (isset( $params->W1 )) {
                    if ($sWeeks != '') {
                        $sWeeks .= '|';
                    }
                    $sWeeks .= '1';
                }
                if (isset( $params->W2 )) {
                    if ($sWeeks != '') {
                        $sWeeks .= '|';
                    }
                    $sWeeks .= '2';
                }
                if (isset( $params->W3 )) {
                    if ($sWeeks != '') {
                        $sWeeks .= '|';
                    }
                    $sWeeks .= '3';
                }
                if (isset( $params->W4 )) {
                    if ($sWeeks != '') {
                        $sWeeks .= '|';
                    }
                    $sWeeks .= '4';
                }
                if (isset( $params->W5 )) {
                    if ($sWeeks != '') {
                        $sWeeks .= '|';
                    }
                    $sWeeks .= '5';
                }
                if (isset( $params->W6 )) {
                    if ($sWeeks != '') {
                        $sWeeks .= '|';
                    }
                    $sWeeks .= '6';
                }
                if (isset( $params->W7 )) {
                    if ($sWeeks != '') {
                        $sWeeks .= '|';
                    }
                    $sWeeks .= '7';
                }
                $sStartTime = $params->SCH_START_TIME;
                $aData['SCH_WEEK_DAYS'] = $sWeeks;
                $aData['SCH_START_DAY'] = ''; //
                break;
            case '3':
                $nStartDay = $params->SCH_START_DAY;
                if ($nStartDay == 'Day of Month') {
                    $nStartDay = 1;
                }
                if ($nStartDay == 'The Day') {
                    $nStartDay = 2;
                }
                if ($nStartDay == 1) {
                    $aData['SCH_START_DAY'] = $nStartDay . '|' . $params->SCH_START_DAY_OPT_1;
                } else {
                    $opt2weeks = $params->SCH_START_DAY_OPT_2_WEEKS;
                    switch ($opt2weeks) {
                        case 'First':
                            $opt2weeks = 1;
                            break;
                        case 'Second':
                            $opt2weeks = 2;
                            break;
                        case 'Third':
                            $opt2weeks = 3;
                            break;
                        case 'Fourth':
                            $opt2weeks = 4;
                            break;
                        case 'Last':
                            $opt2weeks = 5;
                            break;
                    }
                    $opt2days = $params->SCH_START_DAY_OPT_2_DAYS_WEEK;
                    switch ($opt2days) {
                        case 'Monday':
                            $opt2days = 1;
                            break;
                        case 'Tuesday':
                            $opt2days = 2;
                            break;
                        case 'Wednesday':
                            $opt2days = 3;
                            break;
                        case 'Thursday':
                            $opt2days = 4;
                            break;
                        case 'Friday':
                            $opt2days = 5;
                            break;
                        case 'Saturday':
                            $opt2days = 6;
                            break;
                        case 'Sunday':
                            $opt2days = 7;
                            break;
                    }
                    $aData['SCH_START_DAY'] = $nStartDay . '|' . $opt2weeks . '|' . $opt2days;
                }

                $sMonths = '';
                if (isset( $params->M1 )) {
                    if ($sMonths != '') {
                        $sMonths .= '|';
                    }
                    $sMonths .= '1';
                }
                if (isset( $params->M2 )) {
                    if ($sMonths != '') {
                        $sMonths .= '|';
                    }
                    $sMonths .= '2';
                }
                if (isset( $params->M3 )) {
                    if ($sMonths != '') {
                        $sMonths .= '|';
                    }
                    $sMonths .= '3';
                }
                if (isset( $params->M4 )) {
                    if ($sMonths != '') {
                        $sMonths .= '|';
                    }
                    $sMonths .= '4';
                }
                if (isset( $params->M5 )) {
                    if ($sMonths != '') {
                        $sMonths .= '|';
                    }
                    $sMonths .= '5';
                }
                if (isset( $params->M6 )) {
                    if ($sMonths != '') {
                        $sMonths .= '|';
                    }
                    $sMonths .= '6';
                }
                if (isset( $params->M7 )) {
                    if ($sMonths != '') {
                        $sMonths .= '|';
                    }
                    $sMonths .= '7';
                }
                if (isset( $params->M8 )) {
                    if ($sMonths != '') {
                        $sMonths .= '|';
                    }
                    $sMonths .= '8';
                }
                if (isset( $params->M9 )) {
                    if ($sMonths != '') {
                        $sMonths .= '|';
                    }
                    $sMonths .= '9';
                }
                if (isset( $params->M10 )) {
                    if ($sMonths != '') {
                        $sMonths .= '|';
                    }
                    $sMonths .= '10';
                }
                if (isset( $params->M11 )) {
                    if ($sMonths != '') {
                        $sMonths .= '|';
                    }
                    $sMonths .= '11';
                }
                if (isset( $params->M12 )) {
                    if ($sMonths != '') {
                        $sMonths .= '|';
                    }
                    $sMonths .= '12';
                }
                //        if(!empty($params->SCH_MONTHS)){
                //          $aMonths = $params->SCH_MONTHS;
                //          foreach($aMonths as $value) {
                //            $sMonths = $sMonths . $value . '|' ;
                //          }
                //        }
                //        if(!empty($params->SCH_MONTHS_2)){
                //          $aMonths2 = $params->SCH_MONTHS_2;
                //          foreach($aMonths2 as $value) {
                //            $sMonths = $sMonths . $value . '|' ;
                //          }
                //        }
                //        if(!empty($params->SCH_MONTHS_3)){
                //          $aMonths3 = $params->SCH_MONTHS_3;
                //          foreach($aMonths3 as $value) {
                //            $sMonths = $sMonths . $value . '|' ;
                //          }
                //        }
                $aData['SCH_MONTHS'] = $sMonths;
                $sStartDay = $aData['SCH_START_DAY'];
                $sValue = $nStartDay;
                break;

        }
        if (($sOption != '1') && ($sOption != '4')) {
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
                        $startTime = $params->SCH_START_TIME . ":00";
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
        }

        if (isset( $params->SCH_END_DATE )) {
            if (trim( $params->SCH_END_DATE ) != '') {
                $aData['SCH_END_DATE'] = $params->SCH_END_DATE;
            }
        }

        if (! empty( $params->SCH_REPEAT_TASK_CHK )) {
            $nOptEvery = $params->SCH_REPEAT_EVERY_OPT;
            if ($nOptEvery == 2) {
                $aData['SCH_REPEAT_EVERY'] = $params->SCH_REPEAT_EVERY_OPT * 60;
            } else {
                $aData['SCH_REPEAT_EVERY'] = $params->SCH_REPEAT_EVERY_OPT;
            }
        }

        if ((isset( $_POST['form']['CASE_SH_PLUGIN_UID'] )) && ($_POST['form']['CASE_SH_PLUGIN_UID'] != "")) {
            $aData['CASE_SH_PLUGIN_UID'] = $_POST['form']['CASE_SH_PLUGIN_UID'];
        }
        //$aData['SCH_END_DATE'] = "2020-12-30";
        //g::pr($aData);
        $sch_uid = $params->sch_uid;
        if ($sch_uid != '') {
            $aData['SCH_UID'] = $sch_uid;
            $oCaseScheduler->Update( $aData );
            $sw_update = true;
        } else {
            $oCaseScheduler->create( $aData );
            $sch_uid = $oCaseScheduler->getSchUid();
            $sw_update = false;
        }

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

        //Added by Qennix
        //Update Start Time Event in BPMN
        require_once 'classes/model/Event.php';
        require_once 'classes/model/Task.php';

        $oTask = new Task();
        $oTask->load( $params->tas_uid );
        $evn_uid = $oTask->getStartingEvent();
        $tas_name = $oTask->getTasTitle();
        $event = new Event();
        $editEvent = array ();
        $editEvent['EVN_UID'] = $evn_uid;
        $editEvent['EVN_ACTION'] = $sch_uid;
        $event->update( $editEvent );
        //End Adding


        $sch = new CaseScheduler();
        $sch->load( $sch_uid );

        $this->success = true;
        $this->SCH_UID = $sch_uid;
        $this->NEXT = $sch->getSchTimeNextRun();
        $this->DESCRIPTION = $sch->getSchName();
        $this->TAS_NAME = $tas_name;
        if ($sw_update) {
            $this->msg = G::LoadTranslation( 'ID_SCHEDULER_SUCCESS_UPDATE' );
        } else {
            $this->msg = G::LoadTranslation( 'ID_SCHEDULER_SUCCESS_NEW' );
        }
    }

    function loadCS ($params)
    {
        require_once 'classes/model/CaseScheduler.php';
        $SCH_UID = $params->SCH_UID;
        $oCaseScheduler = new CaseScheduler();
        $data = $oCaseScheduler->load( $SCH_UID );
        $start_date = $data['SCH_START_DATE'];
        $start_date = date( 'Y-m-d', strtotime( $start_date ) );
        $data['START_DATE'] = $start_date;
        $end_date = $data['SCH_END_DATE'];
        if ($end_date != '') {
            $end_date = date( 'Y-m-d', strtotime( $end_date ) );
        }
        $data['END_DATE'] = $end_date;
        $exec_time = $data['SCH_START_TIME'];
        $exec_time = date( 'H:i', strtotime( $exec_time ) );
        $data['EXEC_TIME'] = $exec_time;

        $weeks = $data['SCH_WEEK_DAYS'];
        $week = explode( '|', $weeks );
        $w1 = $w2 = $w3 = $w4 = $w5 = $w6 = $w7 = false;
        foreach ($week as $w) {
            switch ($w) {
                case 1:
                    $w1 = true;
                    break;
                case 2:
                    $w2 = true;
                    break;
                case 3:
                    $w3 = true;
                    break;
                case 4:
                    $w4 = true;
                    break;
                case 5:
                    $w5 = true;
                    break;
                case 6:
                    $w6 = true;
                    break;
                case 7:
                    $w7 = true;
                    break;
            }
        }
        $data['W1'] = $w1;
        $data['W2'] = $w2;
        $data['W3'] = $w3;
        $data['W4'] = $w4;
        $data['W5'] = $w5;
        $data['W6'] = $w6;
        $data['W7'] = $w7;

        $years = $data['SCH_MONTHS'];
        $year = explode( '|', $years );
        $m1 = $m2 = $m3 = $m4 = $m5 = $m6 = $m7 = $m8 = $m9 = $m10 = $m11 = $m12 = false;
        foreach ($year as $month) {
            switch ($month) {
                case 1:
                    $m1 = true;
                    break;
                case 2:
                    $m2 = true;
                    break;
                case 3:
                    $m3 = true;
                    break;
                case 4:
                    $m4 = true;
                    break;
                case 5:
                    $m5 = true;
                    break;
                case 6:
                    $m6 = true;
                    break;
                case 7:
                    $m7 = true;
                    break;
                case 8:
                    $m8 = true;
                    break;
                case 9:
                    $m9 = true;
                    break;
                case 10:
                    $m10 = true;
                    break;
                case 11:
                    $m11 = true;
                    break;
                case 12:
                    $m12 = true;
                    break;
            }
        }

        $data['M1'] = $m1;
        $data['M2'] = $m2;
        $data['M3'] = $m3;
        $data['M4'] = $m4;
        $data['M5'] = $m5;
        $data['M6'] = $m6;
        $data['M7'] = $m7;
        $data['M8'] = $m8;
        $data['M9'] = $m9;
        $data['M10'] = $m10;
        $data['M11'] = $m11;
        $data['M12'] = $m12;

        $start_options = $data['SCH_START_DAY'];
        $options = explode( '|', $start_options );
        $data['TYPE_CMB'] = $options[0];
        if ($options[0] == 1) {
            $data['EACH_DAY'] = $options[1];
        } else {
            $data['CMB_1'] = $options[1];
            $data['CMB_2'] = $options[2];
        }
        $this->success = true;
        $this->data = $data;
    }

} //End caseSchedulerProxy

