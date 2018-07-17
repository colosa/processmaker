<?php
namespace ProcessMaker\BusinessModel;

use \G;
use ProcessMaker\Plugins\Interfaces\CaseSchedulerPlugin;
use ProcessMaker\Plugins\PluginRegistry;

class CaseScheduler
{
    /**
     * Return case scheduler of a project
     * @param string $sProcessUID
     * @return array
     *
     * @access public
     */
    public function getCaseSchedulers($sProcessUID = '')
    {
        try {
            $oCriteria = new \Criteria( 'workflow' );
            $oCriteria->clearSelectColumns();
            $oCriteria->addSelectColumn( \CaseSchedulerPeer::SCH_UID );
            $oCriteria->addSelectColumn( \CaseSchedulerPeer::SCH_NAME );
            $oCriteria->addSelectColumn( \CaseSchedulerPeer::SCH_DEL_USER_NAME );
            $oCriteria->addSelectColumn( \CaseSchedulerPeer::SCH_DEL_USER_UID );
            $oCriteria->addSelectColumn( \CaseSchedulerPeer::PRO_UID );
            $oCriteria->addSelectColumn( \CaseSchedulerPeer::TAS_UID );
            $oCriteria->addSelectColumn( \CaseSchedulerPeer::SCH_TIME_NEXT_RUN );
            $oCriteria->addSelectColumn( \CaseSchedulerPeer::SCH_LAST_RUN_TIME );
            $oCriteria->addSelectColumn( \CaseSchedulerPeer::SCH_STATE );
            $oCriteria->addSelectColumn( \CaseSchedulerPeer::SCH_LAST_STATE );
            $oCriteria->addSelectColumn( \CaseSchedulerPeer::USR_UID );
            $oCriteria->addSelectColumn( \CaseSchedulerPeer::SCH_OPTION );
            $oCriteria->addSelectColumn( \CaseSchedulerPeer::SCH_START_TIME );
            $oCriteria->addSelectColumn( \CaseSchedulerPeer::SCH_START_DATE );
            $oCriteria->addSelectColumn( \CaseSchedulerPeer::SCH_DAYS_PERFORM_TASK );
            $oCriteria->addSelectColumn( \CaseSchedulerPeer::SCH_EVERY_DAYS );
            $oCriteria->addSelectColumn( \CaseSchedulerPeer::SCH_WEEK_DAYS );
            $oCriteria->addSelectColumn( \CaseSchedulerPeer::SCH_START_DAY );
            $oCriteria->addSelectColumn( \CaseSchedulerPeer::SCH_MONTHS );
            $oCriteria->addSelectColumn( \CaseSchedulerPeer::SCH_END_DATE );
            $oCriteria->addSelectColumn( \CaseSchedulerPeer::SCH_REPEAT_EVERY );
            $oCriteria->addSelectColumn( \CaseSchedulerPeer::SCH_REPEAT_UNTIL );
            $oCriteria->addSelectColumn( \CaseSchedulerPeer::SCH_REPEAT_STOP_IF_RUNNING );
            $oCriteria->addSelectColumn( \CaseSchedulerPeer::CASE_SH_PLUGIN_UID );
            $oCriteria->add( \CaseSchedulerPeer::PRO_UID, $sProcessUID );
            $oDataset = \CaseSchedulerPeer::doSelectRS( $oCriteria );
            $oDataset->setFetchmode( \ResultSet::FETCHMODE_ASSOC );
            $oDataset->next();
            $aRows = array();
            while ($aRow = $oDataset->getRow()) {
                $aRow = array_change_key_case($aRow, CASE_LOWER);
                $aRows[] = $aRow;
                $oDataset->next();
            }
            return $aRows;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Return case scheduler of a project
     * @param string $sProcessUID
     * @param string $sCaseSchedulerUID
     * @return array
     *
     * @access public
     */
    public function getCaseScheduler($sProcessUID = '', $sCaseSchedulerUID = '')
    {
        try {
            $oCaseSchedulerTest = \CaseSchedulerPeer::retrieveByPK( $sCaseSchedulerUID );
            if (is_null($oCaseSchedulerTest)) {
                throw new \Exception(\G::LoadTranslation("ID_CASE_SCHEDULER_DOES_NOT_EXIST", array($sCaseSchedulerUID)));
            }
            $oCriteria = new \Criteria( 'workflow' );
            $oCriteria->clearSelectColumns();
            $oCriteria->addSelectColumn( \CaseSchedulerPeer::SCH_UID );
            $oCriteria->addSelectColumn( \CaseSchedulerPeer::SCH_NAME );
            $oCriteria->addSelectColumn( \CaseSchedulerPeer::SCH_DEL_USER_NAME );
            $oCriteria->addSelectColumn( \CaseSchedulerPeer::SCH_DEL_USER_UID );
            $oCriteria->addSelectColumn( \CaseSchedulerPeer::PRO_UID );
            $oCriteria->addSelectColumn( \CaseSchedulerPeer::TAS_UID );
            $oCriteria->addSelectColumn( \CaseSchedulerPeer::SCH_TIME_NEXT_RUN );
            $oCriteria->addSelectColumn( \CaseSchedulerPeer::SCH_LAST_RUN_TIME );
            $oCriteria->addSelectColumn( \CaseSchedulerPeer::SCH_STATE );
            $oCriteria->addSelectColumn( \CaseSchedulerPeer::SCH_LAST_STATE );
            $oCriteria->addSelectColumn( \CaseSchedulerPeer::USR_UID );
            $oCriteria->addSelectColumn( \CaseSchedulerPeer::SCH_OPTION );
            $oCriteria->addSelectColumn( \CaseSchedulerPeer::SCH_START_TIME );
            $oCriteria->addSelectColumn( \CaseSchedulerPeer::SCH_START_DATE );
            $oCriteria->addSelectColumn( \CaseSchedulerPeer::SCH_DAYS_PERFORM_TASK );
            $oCriteria->addSelectColumn( \CaseSchedulerPeer::SCH_EVERY_DAYS );
            $oCriteria->addSelectColumn( \CaseSchedulerPeer::SCH_WEEK_DAYS );
            $oCriteria->addSelectColumn( \CaseSchedulerPeer::SCH_START_DAY );
            $oCriteria->addSelectColumn( \CaseSchedulerPeer::SCH_MONTHS );
            $oCriteria->addSelectColumn( \CaseSchedulerPeer::SCH_END_DATE );
            $oCriteria->addSelectColumn( \CaseSchedulerPeer::SCH_REPEAT_EVERY );
            $oCriteria->addSelectColumn( \CaseSchedulerPeer::SCH_REPEAT_UNTIL );
            $oCriteria->addSelectColumn( \CaseSchedulerPeer::SCH_REPEAT_STOP_IF_RUNNING );
            $oCriteria->addSelectColumn( \CaseSchedulerPeer::CASE_SH_PLUGIN_UID );
            $oCriteria->add( \CaseSchedulerPeer::PRO_UID, $sProcessUID );
            $oCriteria->add( \CaseSchedulerPeer::SCH_UID, $sCaseSchedulerUID );
            $oDataset = \CaseSchedulerPeer::doSelectRS( $oCriteria );
            $oDataset->setFetchmode( \ResultSet::FETCHMODE_ASSOC );
            $oDataset->next();
            $aRows = array();
            while ($aRow = $oDataset->getRow()) {
                $aRow = array_change_key_case($aRow, CASE_LOWER);
                $aRows = $aRow;
                $oDataset->next();
            }
            return $aRows;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Get data of unique ids of a Task (Unique id of Process)
     *
     * @param string $taskUid Unique id of Task
     *
     * return array
     */
    public function getTaskUid($taskUid)
    {
        try {
            $criteria = new \Criteria("workflow");
            $criteria->addSelectColumn(\TaskPeer::TAS_UID);
            $criteria->add(\TaskPeer::TAS_UID, $taskUid, \Criteria::EQUAL);
            $rsCriteria = \TaskPeer::doSelectRS($criteria);
            $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);
            $rsCriteria->next();
            return $rsCriteria->getRow();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Checks if the name exists in the case Scheduler
     *
     * @param string $processUid Unique id of Process
     * @param string $name       Name
     *
     * return bool Return true if the name exists, false otherwise
     */
    public function existsName($processUid, $name)
    {
        try {
            $criteria = new \Criteria("workflow");
            $criteria->addSelectColumn(\CaseSchedulerPeer::TAS_UID);
            $criteria->add(\CaseSchedulerPeer::SCH_NAME, $name, \Criteria::EQUAL);
            $criteria->add(\CaseSchedulerPeer::PRO_UID, $processUid, \Criteria::EQUAL);
            $rsCriteria = \CaseSchedulerPeer::doSelectRS($criteria);
            $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);
            $rsCriteria->next();
            return $rsCriteria->getRow();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Checks if the name exists in the case Scheduler
     *
     * @param string $processUid Unique id of Process
     * @param string $name       Name
     *
     * return bool Return true if the name exists, false otherwise
     */
    public function existsNameUpdate($processUid, $schUid, $name)
    {
        try {
            $criteria = new \Criteria("workflow");
            $criteria->addSelectColumn(\CaseSchedulerPeer::TAS_UID);
            $criteria->add(\CaseSchedulerPeer::SCH_NAME, $name, \Criteria::EQUAL);
            $criteria->add(\CaseSchedulerPeer::SCH_UID, $schUid, \Criteria::NOT_EQUAL);
            $criteria->add(\CaseSchedulerPeer::PRO_UID, $processUid, \Criteria::EQUAL);
            $rsCriteria = \CaseSchedulerPeer::doSelectRS($criteria);
            $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);
            $rsCriteria->next();
            return $rsCriteria->getRow();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Checks if the user exists
     *
     * @param string $userName  Name
     * @param string $sTaskUID  Task
     *
     * return message
     */
    public function getUser($userName, $sTaskUID)
    {
        try {
            $sTASKS = $sTaskUID;
            $sWS_USER = trim( $userName );
            $oCriteria = new \Criteria( 'workflow' );
            $oCriteria->addSelectColumn( \UsersPeer::USR_UID );
            $oCriteria->addSelectColumn( \TaskUserPeer::USR_UID );
            $oCriteria->addSelectColumn( \TaskUserPeer::TAS_UID );
            $oCriteria->addSelectColumn( \UsersPeer::USR_USERNAME );
            $oCriteria->addSelectColumn( \UsersPeer::USR_FIRSTNAME );
            $oCriteria->addSelectColumn( \UsersPeer::USR_LASTNAME );
            $oCriteria->addJoin( \TaskUserPeer::USR_UID, \UsersPeer::USR_UID, \Criteria::LEFT_JOIN );
            $oCriteria->add( \TaskUserPeer::TAS_UID, $sTASKS );
            $oCriteria->add( \UsersPeer::USR_USERNAME, $sWS_USER );
            $userIsAssigned = \TaskUserPeer::doCount( $oCriteria );
            if ($userIsAssigned < 1) {
                $oCriteria = new \Criteria( 'workflow' );
                $oCriteria->addSelectColumn( \UsersPeer::USR_UID );
                $oCriteria->addJoin( \UsersPeer::USR_UID, \GroupUserPeer::USR_UID, \Criteria::LEFT_JOIN );
                $oCriteria->addJoin( \GroupUserPeer::GRP_UID, \TaskUserPeer::USR_UID, \Criteria::LEFT_JOIN );
                $oCriteria->add( \TaskUserPeer::TAS_UID, $sTASKS );
                $oCriteria->add( \UsersPeer::USR_USERNAME, $sWS_USER );
                $userIsAssigned = \GroupUserPeer::doCount( $oCriteria );
                if (! ($userIsAssigned >= 1)) {
                    throw new \Exception(\G::LoadTranslation("ID_USER_DOES_NOT_HAVE_ACTIVITY_ASSIGNED", array($sWS_USER, $sTASKS)));
                }
            }
            $oDataset = \TaskUserPeer::doSelectRS($oCriteria);
            $oDataset->setFetchmode(\ResultSet::FETCHMODE_ASSOC);
            $oDataset->next();
            while ($aRow = $oDataset->getRow()) {
                $messageCode = $aRow['USR_UID'];
                $oDataset->next();
            }
            return $messageCode;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Create a new case scheduler of a project
     * @param string $sProcessUID
     * @param array  $caseSchedulerData
     * @param string $userUID
     * @return array
     *
     * @access public
     */
    public function addCaseScheduler($sProcessUID, $caseSchedulerData, $userUID)
    {
        try {
            require_once(PATH_TRUNK . "workflow" . PATH_SEP . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "CaseScheduler.php");
            $caseSchedulerData['sch_repeat_stop_if_running'] = '0';
            $caseSchedulerData['case_sh_plugin_uid'] = null;
            $caseSchedulerData = array_change_key_case($caseSchedulerData, CASE_UPPER);
            $sOption = $caseSchedulerData['SCH_OPTION'];
            if (empty($caseSchedulerData)) {
                die( \G::LoadTranslation("ID_INFORMATION_EMPTY") );
            }
            $arrayTaskUid = $this->getTaskUid($caseSchedulerData['TAS_UID']);
            if (empty($arrayTaskUid)) {
                throw (new \Exception( \G::LoadTranslation("ID_TASK_NOT_FOUND", array($caseSchedulerData['TAS_UID']))));
            }
            if ($caseSchedulerData['SCH_NAME']=='') {
                throw new \Exception(\G::LoadTranslation("ID_CAN_NOT_BE_EMPTY", array ('sch_name')));
            }
            if ($this->existsName($sProcessUID, $caseSchedulerData['SCH_NAME'])) {
                throw new \Exception(\G::LoadTranslation("ID_CASE_SCHEDULER_DUPLICATE"));
            }
            $mUser = $this->getUser($caseSchedulerData['SCH_DEL_USER_NAME'], $caseSchedulerData['TAS_UID']);
            $oUser = \UsersPeer::retrieveByPK( $mUser );
            if (is_null($oUser)) {
                throw (new \Exception($mUser));
            }
            $oUserPass = $oUser->getUsrPassword();
            $caseSchedulerData['SCH_DEL_USER_PASS'] = $oUserPass;
            if ($sOption != '5') {
                $pattern="/^([0-1][0-9]|[2][0-3])[\:]([0-5][0-9])$/";
                if (!preg_match($pattern, $caseSchedulerData['SCH_START_TIME'])) {
                    throw new \Exception(\G::LoadTranslation("ID_INVALID_SCH_START_TIME"));
                }
            }
            $patternDate="/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/";
            if ($sOption == '1' || $sOption == '2' || $sOption == '3') {
                if (!preg_match($patternDate, $caseSchedulerData['SCH_START_DATE'])) {
                    throw new \Exception(\G::LoadTranslation("ID_INVALID_SCH_START_DATE"));
                }
                if ($caseSchedulerData['SCH_START_DATE'] == "") {
                    throw new \Exception(\G::LoadTranslation("ID_CAN_NOT_BE_NULL", array('sch_start_date')));
                }
            }
            if ($sOption == '2') {
                $caseSchedulerData['SCH_EVERY_DAYS'] = 1;
            } else {
                $caseSchedulerData['SCH_EVERY_DAYS'] = 0;
            }
            $oCaseScheduler = new \CaseScheduler();
            $caseSchedulerData['SCH_UID'] = \G::generateUniqueID();
            $caseSchedulerData['PRO_UID'] = $sProcessUID;
            $caseSchedulerData['SCH_STATE'] = 'ACTIVE';
            $caseSchedulerData['SCH_LAST_STATE'] = 'CREATED'; // 'ACTIVE';
            $caseSchedulerData['USR_UID'] = $userUID;
            $caseSchedulerData['SCH_DEL_USER_UID'] = $caseSchedulerData['USR_UID'];
            $sTimeTmp = $caseSchedulerData['SCH_START_TIME'];
            $nActualTime = $caseSchedulerData['SCH_START_TIME']; // time();
            $sValue = '';
            $sDaysPerformTask = '';
            $sWeeks = '';
            $sMonths = '';
            $sStartDay = '';
            $caseSchedulerData['SCH_DAYS_PERFORM_TASK'] = '';
            switch ($sOption) {
                case '1': // If the option is zero, set by default 1
                    $caseSchedulerData['SCH_DAYS_PERFORM_TASK'] = '1';
                    $sValue = $caseSchedulerData['SCH_DAYS_PERFORM_TASK'];
                    switch ($sValue) {
                        case '1':
                            $caseSchedulerData['SCH_DAYS_PERFORM_TASK'] = $caseSchedulerData['SCH_DAYS_PERFORM_TASK'] . '|1';
                            $caseSchedulerData['SCH_MONTHS'] ='0|0|0|0|0|0|0|0|0|0|0|0';
                            $caseSchedulerData['SCH_WEEK_DAYS'] ='0|0|0|0|0|0|0';
                            break;
                        case '2':
                            $caseSchedulerData['SCH_OPTION'] = '2';
                            $caseSchedulerData['SCH_EVERY_DAYS'] = '1'; //check
                            $caseSchedulerData['SCH_WEEK_DAYS'] = '1|2|3|4|5|'; //check
                            break;
                        case '3': // Every [n] Days
                            $sDaysPerformTask = $caseSchedulerData['SCH_DAYS_PERFORM_TASK'];
                            $caseSchedulerData['SCH_DAYS_PERFORM_TASK'] = $caseSchedulerData['SCH_DAYS_PERFORM_TASK'];
                            break;
                    }
                    break;
                case '2': // If the option is zero, set by default 1
                    if ($caseSchedulerData['SCH_WEEK_DAYS'] == "") {
                        throw new \Exception(\G::LoadTranslation("ID_CAN_NOT_BE_NULL", array('sch_week_days')));
                    } else {
                        $weeks = $caseSchedulerData['SCH_WEEK_DAYS'];
                        $weeks = explode("|", $weeks);
                        foreach ($weeks as $row) {
                            if ($row == "1" || $row == "2" || $row == "3" || $row == "4" || $row == "5"|| $row == "6" || $row == "7") {
                                $caseSchedulerData['SCH_WEEK_DAYS'] = $caseSchedulerData['SCH_WEEK_DAYS'];
                            } else {
                                throw new \Exception(\G::LoadTranslation("ID_INVALID_VALUE_FOR", array('sch_week_days')));
                            }
                        }
                    }
                    $caseSchedulerData['SCH_MONTHS'] ='0|0|0|0|0|0|0|0|0|0|0|0';
                    if (empty( $caseSchedulerData['SCH_EVERY_DAYS'] )) {
                        $nEveryDays = 1;
                    } else {
                        $nEveryDays = $caseSchedulerData['SCH_EVERY_DAYS'];
                    }
                    $caseSchedulerData['SCH_EVERY_DAYS'] = $nEveryDays;
                    if (! empty( $caseSchedulerData['SCH_WEEK_DAYS'] )) {
                        $aWeekDays = $caseSchedulerData['SCH_WEEK_DAYS'];
                    }
                    $sStartTime = $caseSchedulerData['SCH_START_TIME'];
                    $sWeeks = $caseSchedulerData['SCH_WEEK_DAYS'] . '|';
                    break;
                case '3':
                    $nStartDay = $caseSchedulerData['SCH_START_DAY'];
                    if ($nStartDay == "") {
                        throw new \Exception(\G::LoadTranslation("ID_CAN_NOT_BE_NULL", array('sch_start_day')));
                    }
                    if ($nStartDay == 1) {
                        if ($caseSchedulerData['SCH_START_DAY_OPT_1'] == "") {
                            throw new \Exception(\G::LoadTranslation("ID_CAN_NOT_BE_NULL", array('sch_start_day_opt_1')));
                        }
                        $temp = $caseSchedulerData['SCH_START_DAY_OPT_1'];
                        $temp = (int)$temp;
                        if ($temp >= 1 && $temp <= 31) {
                            $caseSchedulerData['SCH_START_DAY_OPT_1'] = $caseSchedulerData['SCH_START_DAY_OPT_1'];
                        } else {
                            throw new \Exception(\G::LoadTranslation("ID_INVALID_SCH_START_DAY_1"));
                        }
                        $caseSchedulerData['SCH_START_DAY'] = $nStartDay . '|' . $caseSchedulerData['SCH_START_DAY_OPT_1'];
                    } else {
                        if ($caseSchedulerData['SCH_START_DAY_OPT_2'] == "") {
                            throw new \Exception(\G::LoadTranslation("ID_CAN_NOT_BE_NULL", array('sch_start_day_opt_2')));
                        }
                        $caseSchedulerData['SCH_START_DAY'] = $nStartDay . '|' . $caseSchedulerData['SCH_START_DAY_OPT_2'];
                        $optionTwo = $caseSchedulerData['SCH_START_DAY_OPT_2']{0};
                        if ($optionTwo == "1" || $optionTwo == "2" || $optionTwo == "3" || $optionTwo == "4" || $optionTwo == "5") {
                            $caseSchedulerData['SCH_START_DAY_OPT_2'] = $caseSchedulerData['SCH_START_DAY_OPT_2'];
                        } else {
                            throw new \Exception(\G::LoadTranslation("ID_INVALID_VALUE_FOR", array('sch_start_day_opt_2')));
                        }
                        $pipelineTwo = $caseSchedulerData['SCH_START_DAY_OPT_2']{1};
                        if ($pipelineTwo == "|") {
                            $caseSchedulerData['SCH_START_DAY_OPT_2'] = $caseSchedulerData['SCH_START_DAY_OPT_2'];
                        } else {
                            throw new \Exception(\G::LoadTranslation("ID_INVALID_VALUE_FOR", array('sch_start_day_opt_2')));
                        }
                        $dayTwo = $caseSchedulerData['SCH_START_DAY_OPT_2']{2};
                        if ($dayTwo == "1" || $dayTwo == "2" || $dayTwo == "3" || $dayTwo == "4" || $dayTwo == "5" || $dayTwo == "6" || $dayTwo == "7") {
                            $caseSchedulerData['SCH_START_DAY_OPT_2'] = $caseSchedulerData['SCH_START_DAY_OPT_2'];
                        } else {
                            throw new \Exception(\G::LoadTranslation("ID_INVALID_VALUE_FOR", array('sch_start_day_opt_2')));
                        }
                    }
                    if ($nStartDay == "") {
                        throw new \Exception(\G::LoadTranslation("ID_CAN_NOT_BE_NULL", array('sch_start_day')));
                    }
                    if ($caseSchedulerData['SCH_MONTHS'] == "") {
                        throw new \Exception(\G::LoadTranslation("ID_CAN_NOT_BE_NULL", array('sch_months')));
                    }
                    if (! empty( $caseSchedulerData['SCH_MONTHS'] )) {
                        $aMonths = $caseSchedulerData['SCH_MONTHS'];
                        $aMonths = explode("|", $aMonths);
                        foreach ($aMonths as $row) {
                            if ($row == "1" || $row == "2" || $row == "3" || $row == "4" || $row == "5"|| $row == "6" || $row == "7"|| $row == "8" || $row == "9" || $row == "10"|| $row == "11" || $row == "12") {
                                $caseSchedulerData['SCH_MONTHS'] = $caseSchedulerData['SCH_MONTHS'];
                            } else {
                                throw new \Exception(\G::LoadTranslation("ID_INVALID_VALUE_FOR", array('sch_months')));
                            }
                        }
                    }
                    $sMonths = $caseSchedulerData['SCH_MONTHS'];
                    $sStartDay = $caseSchedulerData['SCH_START_DAY'];
                    $sValue = $nStartDay;
                    break;
            }
            if (($sOption != '1') && ($sOption != '4') && ($sOption != '5')) {
                $sDateTmp = '';
                if ($sStartDay == '') {
                    $sStartDay = date('Y-m-d');
                } else {
                    $size = strlen($caseSchedulerData['SCH_START_DAY']);
                    if ($size > 4) {
                        $aaStartDay = explode( "|", $caseSchedulerData['SCH_START_DAY'] );
                        $aaStartDay[0] = $aaStartDay[0];
                        $aaStartDay[1] = $aaStartDay[1];
                        $aaStartDay[2]= ($aaStartDay[2] == 7 ? 1 : $aaStartDay[2]);
                        $sStartDay = $aaStartDay[0].'|'.$aaStartDay[1].'|'.$aaStartDay[2];
                    }
                }
                $dCurrentDay = date("d");
                $dCurrentMonth = date("m");
                $aStartDay = explode( "|", $caseSchedulerData['SCH_START_DAY'] );
                if ($sOption == '3' && $aStartDay[0] == '1') {
                    $monthsArray = explode( "|", $sMonths );
                    foreach ($monthsArray as $row) {
                        if ($dCurrentMonth == $row && $dCurrentDay < $aStartDay[1]) {
                            $startTime = $caseSchedulerData['SCH_START_TIME'] . ":00";
                            $caseSchedulerData['SCH_TIME_NEXT_RUN'] = date('Y') . '-' . $row . '-' . $aStartDay[1] . ' ' . $startTime;
                            break;
                        } else {
                            $caseSchedulerData['SCH_TIME_NEXT_RUN'] = $oCaseScheduler->updateNextRun( $sOption, $sValue, $nActualTime, $sDaysPerformTask, $sWeeks, $sStartDay, $sMonths, $sDateTmp );
                        }
                    }
                } else {
                    $caseSchedulerData['SCH_TIME_NEXT_RUN'] = $oCaseScheduler->updateNextRun( $sOption, $sValue, $nActualTime, $sDaysPerformTask, $sWeeks, $sStartDay, $sMonths, $sDateTmp );
                }
            } else {
                if ($sOption == '4') {
                    $sDateTmp = date('Y-m-d');
                    $caseSchedulerData['SCH_START_TIME'] = date('Y-m-d', strtotime( $sDateTmp )) . ' ' . date('H:i:s', strtotime( $sTimeTmp ));
                    $caseSchedulerData['SCH_START_DATE'] = $caseSchedulerData['SCH_START_TIME'];
                    $caseSchedulerData['SCH_END_DATE'] = $caseSchedulerData['SCH_START_TIME'];
                }
                $caseSchedulerData['SCH_TIME_NEXT_RUN'] = $caseSchedulerData['SCH_START_TIME'];
                if ($sOption == '5') {
                    if ($caseSchedulerData['SCH_START_DATE'] != '') {
                        $sDateTmp = $caseSchedulerData['SCH_START_DATE'];
                    } else {
                        $sDateTmp = date('Y-m-d');
                        $caseSchedulerData['SCH_START_DATE'] = $sDateTmp;
                    }
                    $caseSchedulerData['SCH_START_TIME'] = time();
                    $caseSchedulerData['SCH_START_DATE'] = $caseSchedulerData['SCH_START_TIME'];
                    if ($caseSchedulerData['SCH_REPEAT_EVERY'] == "") {
                        throw new \Exception(\G::LoadTranslation("ID_CAN_NOT_BE_NULL", array('sch_repeat_every')));
                    }
                    $patternHour="/^([0-9]|0[0-9]|1[0-9]|2[0-3]).[0-5][0-9]$/";
                    if (!preg_match($patternHour, $caseSchedulerData['SCH_REPEAT_EVERY'])) {
                        throw new \Exception(\G::LoadTranslation("ID_INVALID_SCH_REPEAT"));
                    }
                    $nextRun = $caseSchedulerData['SCH_REPEAT_EVERY'] * 60 * 60;
                    $caseSchedulerData['SCH_REPEAT_EVERY'] = $caseSchedulerData['SCH_REPEAT_EVERY'];
                    $date = $caseSchedulerData['SCH_START_TIME'];
                    $date += $nextRun;
                    $date = date("Y-m-d H:i", $date);
                    $caseSchedulerData['SCH_TIME_NEXT_RUN'] = $date;
                }
            }
            if (! empty( $caseSchedulerData['SCH_REPEAT_TASK_CHK'] )) {
                if (trim( $caseSchedulerData['SCH_END_DATE'] ) != '') {
                    $caseSchedulerData['SCH_END_DATE'] = $caseSchedulerData['SCH_END_DATE'];
                }
            }
            if (! empty( $caseSchedulerData['SCH_REPEAT_TASK_CHK'] )) {
                $nOptEvery = $caseSchedulerData['SCH_REPEAT_EVERY_OPT'];
                if ($nOptEvery == 2) {
                    $caseSchedulerData['SCH_REPEAT_EVERY'] = $caseSchedulerData['SCH_REPEAT_EVERY'] * 60;
                } else {
                    $caseSchedulerData['SCH_REPEAT_EVERY'] = $caseSchedulerData['SCH_REPEAT_EVERY'];
                }
            }
            if ((isset( $caseSchedulerData['CASE_SH_PLUGIN_UID'] )) && ($caseSchedulerData['CASE_SH_PLUGIN_UID'] != "")) {
                $caseSchedulerData['CASE_SH_PLUGIN_UID'] = $caseSchedulerData['CASE_SH_PLUGIN_UID'];
            }
            // check this data
            $caseSchedulerData['SCH_REPEAT_UNTIL'] = '';
            $caseSchedulerData['SCH_REPEAT_STOP_IF_RUNNING'] = '0';
            $caseSchedulerData['CASE_SH_PLUGIN_UID'] = null;
            //
            $oCaseScheduler->create( $caseSchedulerData );
            $oCriteria = $this->getCaseScheduler($sProcessUID, $caseSchedulerData['SCH_UID']);
            return $oCriteria;
        } catch (Exception $oException) {
            die( $oException->getMessage() );
        }
    }

    /**
     * Update case scheduler for a project
     * @param string $sProcessUID
     * @param array  $caseSchedulerData
     * @param string $userUID
     * @param string $sSchUID
     *
     * @access public
     */
    public function updateCaseScheduler($sProcessUID, $caseSchedulerData, $userUID, $sSchUID = '')
    {
        try {
            require_once(PATH_TRUNK . "workflow" . PATH_SEP . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "CaseScheduler.php");
            $caseSchedulerData = array_change_key_case($caseSchedulerData, CASE_UPPER);
            if (empty( $caseSchedulerData )) {
                die( \G::LoadTranslation("ID_INFORMATION_EMPTY") );
            }
            $oCaseScheduler = new \CaseScheduler();
            $aFields = $oCaseScheduler->Load($sSchUID);
            if ($caseSchedulerData['SCH_OPTION'] == null) {
                $sOption = $aFields['SCH_OPTION'];
                $caseSchedulerData['SCH_OPTION'] = $sOption;
            } else {
                $sOption = $caseSchedulerData['SCH_OPTION'];
            }
            $caseSchedulerData['sch_repeat_stop_if_running'] = '0';
            $caseSchedulerData['case_sh_plugin_uid'] = null;
            $caseSchedulerData = array_change_key_case($caseSchedulerData, CASE_UPPER);
            $arrayTaskUid = $this->getTaskUid($caseSchedulerData['TAS_UID']);
            if (empty($arrayTaskUid)) {
                throw new \Exception(\G::LoadTranslation("ID_TASK_NOT_FOUND", array($caseSchedulerData['TAS_UID'])));
            }
            if ($caseSchedulerData['SCH_NAME']=='') {
                throw new \Exception(\G::LoadTranslation("ID_CAN_NOT_BE_EMPTY", array ('sch_name')));
            }
            if ($this->existsNameUpdate($sProcessUID, $sSchUID, $caseSchedulerData['SCH_NAME'])) {
                throw new \Exception(\G::LoadTranslation("ID_CASE_SCHEDULER_DUPLICATE"));
            }
            $mUser = $this->getUser($caseSchedulerData['SCH_DEL_USER_NAME'], $caseSchedulerData['TAS_UID']);
            $oUser = \UsersPeer::retrieveByPK( $mUser );
            if (is_null($oUser)) {
                throw (new \Exception($mUser));
            }
            $oUserPass = $oUser->getUsrPassword();
            $caseSchedulerData['SCH_DEL_USER_PASS'] = $oUserPass;
            if ($sOption != '5') {
                $pattern="/^([0-1][0-9]|[2][0-3])[\:]([0-5][0-9])$/";
                if (!preg_match($pattern, $caseSchedulerData['SCH_START_TIME'])) {
                    throw new \Exception(\G::LoadTranslation("ID_INVALID_SCH_START_TIME"));
                }
            }
            $patternDate="/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/";
            if ($sOption == '1' || $sOption == '2' || $sOption == '3') {
                if (!preg_match($patternDate, $caseSchedulerData['SCH_START_DATE'])) {
                    throw new \Exception(\G::LoadTranslation("ID_INVALID_SCH_START_DATE"));
                }
                if ($caseSchedulerData['SCH_START_DATE'] == "") {
                    throw new \Exception(\G::LoadTranslation("ID_CAN_NOT_BE_NULL", array('sch_start_date')));
                }
            }
            if ($sOption == '2') {
                $caseSchedulerData['SCH_EVERY_DAYS'] = 1;
            } else {
                $caseSchedulerData['SCH_EVERY_DAYS'] = 0;
            }
            $oCaseScheduler = new \CaseScheduler();
            $caseSchedulerData['SCH_UID'] = $sSchUID;
            $caseSchedulerData['PRO_UID'] = $sProcessUID;
            if ($caseSchedulerData['SCH_STATE'] == "" || $caseSchedulerData['SCH_STATE'] == null) {
                throw (new \Exception( 'sch_state can not be null'));
            } else {
                if ($caseSchedulerData['SCH_STATE']  == 'ACTIVE') {
                    $caseSchedulerData['SCH_LAST_STATE']  = 'CREATED';
                } else {
                    $caseSchedulerData['SCH_LAST_STATE'] = 'ACTIVE';
                }
            }
            $caseSchedulerData['USR_UID'] = $userUID;
            $caseSchedulerData['SCH_DEL_USER_UID'] = $caseSchedulerData['USR_UID'];
            $sTimeTmp = $caseSchedulerData['SCH_START_TIME'];
            $nActualTime = $caseSchedulerData['SCH_START_TIME']; // time();
            $sValue = '';
            $sDaysPerformTask = '';
            $sWeeks = '';
            $sMonths = '';
            $sStartDay = '';
            $caseSchedulerData['SCH_DAYS_PERFORM_TASK'] = '';
            switch ($sOption) {
                case '1': // If the option is zero, set by default 1
                    $caseSchedulerData['SCH_DAYS_PERFORM_TASK'] = '1';
                    $sValue = $caseSchedulerData['SCH_DAYS_PERFORM_TASK'];
                    switch ($sValue) {
                        case '1':
                            $caseSchedulerData['SCH_DAYS_PERFORM_TASK'] = $caseSchedulerData['SCH_DAYS_PERFORM_TASK'] . '|1';
                            $caseSchedulerData['SCH_MONTHS'] ='0|0|0|0|0|0|0|0|0|0|0|0';
                            $caseSchedulerData['SCH_WEEK_DAYS'] ='0|0|0|0|0|0|0';
                            break;
                        case '2':
                            $caseSchedulerData['SCH_OPTION'] = '2';
                            $caseSchedulerData['SCH_EVERY_DAYS'] = '1'; //check
                            $caseSchedulerData['SCH_WEEK_DAYS'] = '1|2|3|4|5|'; //check
                            break;
                        case '3': // Every [n] Days
                            $sDaysPerformTask = $caseSchedulerData['SCH_DAYS_PERFORM_TASK'];
                            $caseSchedulerData['SCH_DAYS_PERFORM_TASK'] = $caseSchedulerData['SCH_DAYS_PERFORM_TASK'];
                            break;
                    }
                    break;
                case '2': // If the option is zero, set by default 1
                    if ($caseSchedulerData['SCH_WEEK_DAYS'] == "") {
                        throw new \Exception(\G::LoadTranslation("ID_CAN_NOT_BE_NULL", array('sch_week_days')));
                    } else {
                        $weeks = $caseSchedulerData['SCH_WEEK_DAYS'];
                        $weeks = explode("|", $weeks);
                        foreach ($weeks as $row) {
                            if ($row == "1" || $row == "2" || $row == "3" || $row == "4" || $row == "5"|| $row == "6" || $row == "7") {
                                $caseSchedulerData['SCH_WEEK_DAYS'] = $caseSchedulerData['SCH_WEEK_DAYS'];
                            } else {
                                throw new \Exception(\G::LoadTranslation("ID_INVALID_VALUE_FOR", array('sch_week_days')));
                            }
                        }
                    }
                    $caseSchedulerData['SCH_MONTHS'] ='0|0|0|0|0|0|0|0|0|0|0|0';
                    if (empty( $caseSchedulerData['SCH_EVERY_DAYS'] )) {
                        $nEveryDays = 1;
                    } else {
                        $nEveryDays = $caseSchedulerData['SCH_EVERY_DAYS'];
                    }
                    $caseSchedulerData['SCH_EVERY_DAYS'] = $nEveryDays;
                    if (! empty( $caseSchedulerData['SCH_WEEK_DAYS'] )) {
                        $aWeekDays = $caseSchedulerData['SCH_WEEK_DAYS'];
                    }
                    $sStartTime = $caseSchedulerData['SCH_START_TIME'];
                    $sWeeks = $caseSchedulerData['SCH_WEEK_DAYS'] . '|';
                    break;
                case '3':
                    $nStartDay = $caseSchedulerData['SCH_START_DAY'];
                    if ($nStartDay == "") {
                        throw new \Exception(\G::LoadTranslation("ID_CAN_NOT_BE_NULL", array('sch_start_day')));
                    }
                    if ($nStartDay == 1) {
                        if ($caseSchedulerData['SCH_START_DAY_OPT_1'] == "") {
                            throw new \Exception(\G::LoadTranslation("ID_CAN_NOT_BE_NULL", array('sch_start_day_opt_1')));
                        }
                        $temp = $caseSchedulerData['SCH_START_DAY_OPT_1'];
                        $temp = (int)$temp;
                        if ($temp >= 1 && $temp <= 31) {
                            $caseSchedulerData['SCH_START_DAY_OPT_1'] = $caseSchedulerData['SCH_START_DAY_OPT_1'];
                        } else {
                            throw new \Exception(\G::LoadTranslation("ID_INVALID_SCH_START_DAY_1"));
                        }
                        $caseSchedulerData['SCH_START_DAY'] = $nStartDay . '|' . $caseSchedulerData['SCH_START_DAY_OPT_1'];
                    } else {
                        if ($caseSchedulerData['SCH_START_DAY_OPT_2'] == "") {
                            throw new \Exception(\G::LoadTranslation("ID_CAN_NOT_BE_NULL", array('sch_start_day_opt_2')));
                        }
                        $caseSchedulerData['SCH_START_DAY'] = $nStartDay . '|' . $caseSchedulerData['SCH_START_DAY_OPT_2'];
                        $optionTwo = $caseSchedulerData['SCH_START_DAY_OPT_2']{0};
                        if ($optionTwo == "1" || $optionTwo == "2" || $optionTwo == "3" || $optionTwo == "4" || $optionTwo == "5") {
                            $caseSchedulerData['SCH_START_DAY_OPT_2'] = $caseSchedulerData['SCH_START_DAY_OPT_2'];
                        } else {
                            throw new \Exception(\G::LoadTranslation("ID_INVALID_VALUE_FOR", array('sch_start_day_opt_2')));
                        }
                        $pipelineTwo = $caseSchedulerData['SCH_START_DAY_OPT_2']{1};
                        if ($pipelineTwo == "|") {
                            $caseSchedulerData['SCH_START_DAY_OPT_2'] = $caseSchedulerData['SCH_START_DAY_OPT_2'];
                        } else {
                            throw new \Exception(\G::LoadTranslation("ID_INVALID_VALUE_FOR", array('sch_start_day_opt_2')));
                        }
                        $dayTwo = $caseSchedulerData['SCH_START_DAY_OPT_2']{2};
                        if ($dayTwo == "1" || $dayTwo == "2" || $dayTwo == "3" || $dayTwo == "4" || $dayTwo == "5" || $dayTwo == "6" || $dayTwo == "7") {
                            $caseSchedulerData['SCH_START_DAY_OPT_2'] = $caseSchedulerData['SCH_START_DAY_OPT_2'];
                        } else {
                            throw new \Exception(\G::LoadTranslation("ID_INVALID_VALUE_FOR", array('sch_start_day_opt_2')));
                        }
                    }
                    if ($nStartDay == "") {
                        throw new \Exception(\G::LoadTranslation("ID_CAN_NOT_BE_NULL", array('sch_start_day')));
                    }
                    if ($caseSchedulerData['SCH_MONTHS'] == "") {
                        throw new \Exception(\G::LoadTranslation("ID_CAN_NOT_BE_NULL", array('sch_months')));
                    }
                    if (! empty( $caseSchedulerData['SCH_MONTHS'] )) {
                        $aMonths = $caseSchedulerData['SCH_MONTHS'];
                        $aMonths = explode("|", $aMonths);
                        foreach ($aMonths as $row) {
                            if ($row == "1" || $row == "2" || $row == "3" || $row == "4" || $row == "5"|| $row == "6" || $row == "7"|| $row == "8" || $row == "9" || $row == "10"|| $row == "11" || $row == "12") {
                                $caseSchedulerData['SCH_MONTHS'] = $caseSchedulerData['SCH_MONTHS'];
                            } else {
                                throw new \Exception(\G::LoadTranslation("ID_INVALID_VALUE_FOR", array('sch_months')));
                            }
                        }
                    }
                    $sMonths = $caseSchedulerData['SCH_MONTHS'];
                    $sStartDay = $caseSchedulerData['SCH_START_DAY'];
                    $sValue = $nStartDay;
                    break;
            }

            if (($sOption != '1') && ($sOption != '4') && ($sOption != '5')) {
                if ($sStartDay == '') {
                    $sStartDay = date('Y-m-d');
                } else {
                    $size = strlen($caseSchedulerData['SCH_START_DAY']);
                    if ($size > 4) {
                        $aaStartDay = explode( "|", $caseSchedulerData['SCH_START_DAY'] );
                        $aaStartDay[0] = $aaStartDay[0];
                        $aaStartDay[1] = $aaStartDay[1];
                        $aaStartDay[2]= ($aaStartDay[2] == 7 ? 1 : $aaStartDay[2]);
                        $sStartDay = $aaStartDay[0].'|'.$aaStartDay[1].'|'.$aaStartDay[2];
                    }
                }
                $dCurrentDay = date("d");
                $dCurrentMonth = date("m");
                $aStartDay = explode( "|", $caseSchedulerData['SCH_START_DAY'] );
                $sDateTmp = '';
                if ($sOption == '3' && $aStartDay[0] == '1') {
                    $monthsArray = explode( "|", $sMonths );
                    foreach ($monthsArray as $row) {
                        if ($dCurrentMonth == $row && $dCurrentDay < $aStartDay[1]) {
                            $startTime = $caseSchedulerData['SCH_START_TIME'] . ":00";
                            $caseSchedulerData['SCH_TIME_NEXT_RUN'] = date('Y') . '-' . $row . '-' . $aStartDay[1] . ' ' . $startTime;
                            break;
                        } else {
                            $caseSchedulerData['SCH_TIME_NEXT_RUN'] = $oCaseScheduler->updateNextRun( $sOption, $sValue, $nActualTime, $sDaysPerformTask, $sWeeks, $sStartDay, $sMonths, $sDateTmp );
                        }
                    }
                } else {
                    $caseSchedulerData['SCH_TIME_NEXT_RUN'] = $oCaseScheduler->updateNextRun( $sOption, $sValue, $nActualTime, $sDaysPerformTask, $sWeeks, $sStartDay, $sMonths, $sDateTmp );
                }
            } else {
                if ($sOption == '4') {
                    $sDateTmp = date('Y-m-d');
                    $caseSchedulerData['SCH_START_TIME'] = date('Y-m-d', strtotime( $sDateTmp )) . ' ' . date('H:i:s', strtotime( $sTimeTmp ));
                    $caseSchedulerData['SCH_START_DATE'] = $caseSchedulerData['SCH_START_TIME'];
                    $caseSchedulerData['SCH_END_DATE'] = $caseSchedulerData['SCH_START_TIME'];
                }
                $caseSchedulerData['SCH_TIME_NEXT_RUN'] = $caseSchedulerData['SCH_START_TIME'];
                if ($sOption == '5') {
                    if ($caseSchedulerData['SCH_START_DATE'] != '') {
                        $sDateTmp = $caseSchedulerData['SCH_START_DATE'];
                    } else {
                        $sDateTmp = date('Y-m-d');
                        $caseSchedulerData['SCH_START_DATE'] = $sDateTmp;
                    }
                    $caseSchedulerData['SCH_START_TIME'] = time();
                    $caseSchedulerData['SCH_START_DATE'] = $caseSchedulerData['SCH_START_TIME'];
                    if ($caseSchedulerData['SCH_REPEAT_EVERY'] == "") {
                        throw new \Exception(\G::LoadTranslation("ID_CAN_NOT_BE_NULL", array('sch_repeat_every')));
                    }
                    $patternHour="/^([0-9]|0[0-9]|1[0-9]|2[0-3]).[0-5][0-9]$/";
                    if (!preg_match($patternHour, $caseSchedulerData['SCH_REPEAT_EVERY'])) {
                        throw new \Exception(\G::LoadTranslation("ID_INVALID_SCH_REPEAT"));
                    }
                    $nextRun = $caseSchedulerData['SCH_REPEAT_EVERY'] * 60 * 60;
                    $caseSchedulerData['SCH_REPEAT_EVERY'] = $caseSchedulerData['SCH_REPEAT_EVERY'];
                    $date = $caseSchedulerData['SCH_START_TIME'];
                    $date += $nextRun;
                    $date = date("Y-m-d H:i", $date);
                    $caseSchedulerData['SCH_TIME_NEXT_RUN'] = $date;
                }
            }
            if (! empty( $caseSchedulerData['SCH_REPEAT_TASK_CHK'] )) {
                if (trim( $caseSchedulerData['SCH_END_DATE'] ) != '') {
                    $caseSchedulerData['SCH_END_DATE'] = $caseSchedulerData['SCH_END_DATE'];
                }
            }
            if (! empty( $caseSchedulerData['SCH_REPEAT_TASK_CHK'] )) {
                $nOptEvery = $caseSchedulerData['SCH_REPEAT_EVERY_OPT'];
                if ($nOptEvery == 2) {
                    $caseSchedulerData['SCH_REPEAT_EVERY'] = $caseSchedulerData['SCH_REPEAT_EVERY'] * 60;
                } else {
                    $caseSchedulerData['SCH_REPEAT_EVERY'] = $caseSchedulerData['SCH_REPEAT_EVERY'];
                }
            }
            if ((isset( $caseSchedulerData['CASE_SH_PLUGIN_UID'] )) && ($caseSchedulerData['CASE_SH_PLUGIN_UID'] != "")) {
                $caseSchedulerData['CASE_SH_PLUGIN_UID'] = $caseSchedulerData['CASE_SH_PLUGIN_UID'];
            }
            // check this data
            $caseSchedulerData['SCH_REPEAT_UNTIL'] = '';
            $caseSchedulerData['SCH_REPEAT_STOP_IF_RUNNING'] = '0';
            $caseSchedulerData['CASE_SH_PLUGIN_UID'] = null;
            //
            $oCaseScheduler->Update($caseSchedulerData);
            $oCriteria = $this->getCaseScheduler($sProcessUID, $sSchUID);
            return $oCriteria;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Delete a case scheduler of a project
     *
     * @param string $sSchUID
     *
     * @access public
     */
    public function deleteCaseScheduler($sSchUID)
    {
        try {
            require_once(PATH_TRUNK . "workflow" . PATH_SEP . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "CaseScheduler.php");
            $oCaseScheduler = new \CaseScheduler();
            if (!isset($sSchUID)) {
                return;
            }
            $event = \BpmnEventPeer::retrieveByPK($sSchUID);
            if (is_object($event)) {
                $event->delete();
            }
            $oCaseScheduler->remove($sSchUID);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Create/Update CaseScheduler
     *
     * @param string $caseSchedulerUid      Unique id of CaseScheduler
     * @param string $projectUid            Unique id of Project
     * @param string $userUidCreatorUpdater Unique id of creator/updater User
     * @param array  $arrayData             Data
     * @param array  $arrayDataPlugin       Data plugin
     *
     * return void
     */
    public function createUpdate($caseSchedulerUid, $projectUid, $userUidCreatorUpdater, array $arrayData, array $arrayDataPlugin = array())
    {
        try {
            //Set variables
            $flagInsert = ($caseSchedulerUid == "")? true : false;
            $option = ($flagInsert)? "INS" : "UPD";

            //Set data
            if ($flagInsert) {
                $caseSchedulerUid = \ProcessMaker\Util\Common::generateUID();
            }

            foreach ($arrayData as $key => $value) {
                if (is_array($value)) {
                    foreach ($value as $key2 => $value2) {
                        $arrayData[$key][$key2] = trim($value2);
                    }
                } else {
                    $arrayData[$key] = trim($value);
                }
            }

            $arrayCaseSchedulerData = array();
            $arrayCaseSchedulerData["SCH_UID"]  = $caseSchedulerUid;
            $arrayCaseSchedulerData["SCH_NAME"] = $arrayData["SCH_NAME"];
            $arrayCaseSchedulerData["PRO_UID"]  = $projectUid;
            $arrayCaseSchedulerData["TAS_UID"]  = $arrayData["TAS_UID"];

            $arrayCaseSchedulerData["SCH_DEL_USER_UID"] = $arrayData["SCH_USER_UID"];
            $arrayCaseSchedulerData["SCH_DEL_USER_NAME"] = $arrayData["SCH_USER_NAME"];

            switch ($option) {
                case "INS":
                    $arrayCaseSchedulerData["SCH_DEL_USER_PASS"] = \Bootstrap::hashPassword($arrayData["SCH_USER_PASSWORD"]);

                    $arrayCaseSchedulerData["SCH_STATE"] = "ACTIVE";
                    $arrayCaseSchedulerData["SCH_LAST_STATE"] = "CREATED";

                    $sDateTmp = ($arrayData["SCH_START_DATE"] != "")? $arrayData["SCH_START_DATE"] : date("Y-m-d");
                    break;
                case "UPD":
                    if ($arrayData["SCH_USER_PASSWORD"] != "DefaultPM") {
                        $arrayCaseSchedulerData["SCH_DEL_USER_PASS"] = \Bootstrap::hashPassword($arrayData["SCH_USER_PASSWORD"]);
                    }

                    $sDateTmp = $arrayData["SCH_START_DATE"];
                    break;
            }

            $caseSchedulerOption = (int)($arrayData["SCH_OPTION"]);

            $arrayCaseSchedulerData["USR_UID"] = $userUidCreatorUpdater;
            $arrayCaseSchedulerData["SCH_OPTION"] = $caseSchedulerOption;
            $arrayCaseSchedulerData["SCH_START_TIME"] = date("Y-m-d", strtotime($sDateTmp)) . " " . date("H:i:s", strtotime($arrayData["SCH_START_TIME"]));
            $arrayCaseSchedulerData["SCH_START_DATE"] = date("Y-m-d", strtotime($sDateTmp)) . " " . date("H:i:s", strtotime($arrayData["SCH_START_TIME"]));

            $sValue = "";
            $sDaysPerformTask = "";
            $sWeeks = "";
            $sMonths = "";

            switch ($option) {
                case "INS":
                    $arrayCaseSchedulerData["SCH_START_DAY"] = "";
                    $arrayCaseSchedulerData["SCH_REPEAT_EVERY"] = "";
                    $arrayCaseSchedulerData["SCH_REPEAT_UNTIL"] = "";
                    $arrayCaseSchedulerData["SCH_DAYS_PERFORM_TASK"] = "";
                    break;
                case "UPD":
                    break;
            }

            switch ($caseSchedulerOption) {
                case 1:
                    //Option 1
                    $sValue = $arrayData["SCH_DAYS_PERFORM_TASK"];

                    switch ($sValue) {
                        case "1":
                            $arrayCaseSchedulerData["SCH_DAYS_PERFORM_TASK"] = $arrayData["SCH_DAYS_PERFORM_TASK"] . "|1";
                            break;
                        case "2":
                            $arrayCaseSchedulerData["SCH_OPTION"] = "2";
                            $arrayCaseSchedulerData["SCH_EVERY_DAYS"] = "1";
                            $arrayCaseSchedulerData["SCH_WEEK_DAYS"] = "1|2|3|4|5|";
                            break;
                        case "3":
                            //Every [n] Days
                            $sDaysPerformTask = $arrayData["SCH_DAYS_PERFORM_TASK_OPT_3"];
                            $arrayCaseSchedulerData["SCH_DAYS_PERFORM_TASK"] = $arrayData["SCH_DAYS_PERFORM_TASK"] . "|" . $arrayData["SCH_DAYS_PERFORM_TASK_OPT_3"];
                            break;
                    }
                    break;
                case 2:
                    //If the option is zero, set by default 1
                    $arrayCaseSchedulerData["SCH_EVERY_DAYS"] = (empty($arrayData["SCH_EVERY_DAYS"]))? 1 : $arrayData["SCH_EVERY_DAYS"];

                    $sWeeks = "";

                    if (!empty($arrayData["SCH_WEEK_DAYS"])) {
                        $aWeekDays = $arrayData["SCH_WEEK_DAYS"];

                        foreach ($aWeekDays as $value) {
                            $sWeeks = $sWeeks . $value . "|";
                        }
                    }

                    if (!empty($arrayData["SCH_WEEK_DAYS_2"])) {
                        $aWeekDays2 = $arrayData["SCH_WEEK_DAYS_2"];

                        foreach ($aWeekDays2 as $value) {
                            $sWeeks = $sWeeks . $value . "|";
                        }
                    }

                    $sStartTime = $arrayData["SCH_START_TIME"];
                    $arrayCaseSchedulerData["SCH_WEEK_DAYS"] = $sWeeks;
                    break;
                case 3:
                    $nStartDay = $arrayData["SCH_START_DAY"];

                    if ($nStartDay == 1) {
                        $arrayCaseSchedulerData["SCH_START_DAY"] = $nStartDay . "|" . $arrayData["SCH_START_DAY_OPT_1"];
                    } else {
                        $arrayCaseSchedulerData["SCH_START_DAY"] = $nStartDay . "|" . $arrayData["SCH_START_DAY_OPT_2_WEEKS"] . "|" . $arrayData["SCH_START_DAY_OPT_2_DAYS_WEEK"];
                    }

                    $sMonths = "";

                    if (!empty($arrayData["SCH_MONTHS"])) {
                        $aMonths = $arrayData["SCH_MONTHS"];

                        foreach ($aMonths as $value) {
                            $sMonths = $sMonths . $value . "|";
                        }
                    }

                    if (!empty($arrayData["SCH_MONTHS_2"])) {
                        $aMonths2 = $arrayData["SCH_MONTHS_2"];

                        foreach ($aMonths2 as $value) {
                            $sMonths = $sMonths . $value . "|";
                        }
                    }

                    if (!empty($arrayData["SCH_MONTHS_3"])) {
                        $aMonths3 = $arrayData["SCH_MONTHS_3"];

                        foreach ($aMonths3 as $value) {
                            $sMonths = $sMonths . $value . "|";
                        }
                    }

                    $arrayCaseSchedulerData["SCH_MONTHS"] = $sMonths;
                    $sValue = $nStartDay;
                    break;
            }

            $caseScheduler = new \CaseScheduler();

            $recalculateDate = false;
            $recalculateTime = false;

            switch ($option) {
                case "INS":
                    $recalculateDate = true;
                    $recalculateTime = true;
                    break;
                case "UPD":
                    $arrayDataAux = $caseScheduler->load($caseSchedulerUid);

                    $arrayCaseSchedulerData["SCH_END_DATE"] = ($arrayData["SCH_END_DATE"] != "")? $arrayData["SCH_END_DATE"] : null;

                    //If the start date has changed then recalculate the next run time
                    $recalculateDate = ($arrayData["SCH_START_DATE"] != $arrayData["PREV_SCH_START_DATE"])? true : false;
                    $recalculateTime = (date("H:i:s", strtotime($arrayData["SCH_START_TIME"])) != date("H:i:s", strtotime($arrayData["PREV_SCH_START_TIME"])))? true : false;
                    break;
            }

            switch ($caseSchedulerOption) {
                case 4:
                    //$arrayCaseSchedulerData["SCH_END_DATE"] = $arrayCaseSchedulerData["SCH_START_TIME"];
                    break;
                case 5:
                    switch ($option) {
                        case "INS":
                            $arrayCaseSchedulerData["SCH_START_TIME"] = time();
                            $arrayCaseSchedulerData["SCH_START_DATE"] = $arrayCaseSchedulerData["SCH_START_TIME"];
                            break;
                        case "UPD":
                            break;
                    }

                    $arrayCaseSchedulerData["SCH_REPEAT_EVERY"] = $arrayData["SCH_REPEAT_EVERY"];
                    break;
            }

            switch ($option) {
                case "INS":
                    $arrayCaseSchedulerData["SCH_TIME_NEXT_RUN"] = date("Y-m-d") . " " . (($caseSchedulerOption != 5)? $arrayData["SCH_START_TIME"] . ":00" : date("H:i:s"));

                    if ($arrayData["SCH_END_DATE"] != "") {
                        $arrayCaseSchedulerData["SCH_END_DATE"] = $arrayData["SCH_END_DATE"];
                    }
                    break;
                case "UPD":
                    break;
            }

            //if (!empty($arrayData["SCH_REPEAT_TASK_CHK"])) {
            //    if ($arrayData["SCH_REPEAT_EVERY_OPT"] . "" == "2") {
            //        $arrayCaseSchedulerData["SCH_REPEAT_EVERY"] = ((int)($arrayData["SCH_REPEAT_EVERY"])) * 60;
            //    } else {
            //        $arrayCaseSchedulerData["SCH_REPEAT_EVERY"] = (int)($arrayData["SCH_REPEAT_EVERY"]);
            //    }
            //}

            //Create/Update
            $caseSchedulerAux = new \CaseScheduler();

            $caseSchedulerUid = "";
            $arrayCaseSchedulerDataOld = array();

            switch ($option) {
                case "INS":
                    if (isset($arrayData["CASE_SH_PLUGIN_UID"]) && $arrayData["CASE_SH_PLUGIN_UID"] != "") {
                        $arrayCaseSchedulerData["CASE_SH_PLUGIN_UID"] = $arrayData["CASE_SH_PLUGIN_UID"];
                    }

                    $result = $caseScheduler->create($arrayCaseSchedulerData);

                    $caseSchedulerUid = $caseScheduler->getSchUid();
                    $arrayCaseSchedulerDataOld = $caseSchedulerAux->load($caseSchedulerUid);
                    break;
                case "UPD":
                    $caseSchedulerUid = $caseScheduler->getSchUid();
                    $arrayCaseSchedulerDataOld = $caseSchedulerAux->load($caseSchedulerUid);

                    $result = $caseScheduler->update($arrayCaseSchedulerData);
                    break;
            }

            //Update the SCH_TIME_NEXT_RUN field
            $caseScheduler = new \CaseScheduler();

            $arrayCaseSchedulerData = $caseScheduler->load($caseSchedulerUid);

            $flagUpdateTimeNextRun = false;

            switch ((int)($arrayCaseSchedulerData["SCH_OPTION"])) {
                case 1:
                    //Daily
                    $flagUpdateTimeNextRun = $recalculateDate || $recalculateTime;
                    break;
                case 2:
                    //Weekly
                    $flagUpdateTimeNextRun = $recalculateDate || $recalculateTime || $arrayCaseSchedulerData["SCH_WEEK_DAYS"] != $arrayCaseSchedulerDataOld["SCH_WEEK_DAYS"];
                    break;
                case 3:
                    //Monthly
                    $flagUpdateTimeNextRun = $recalculateDate || $recalculateTime || $arrayCaseSchedulerData["SCH_START_DAY"] != $arrayCaseSchedulerDataOld["SCH_START_DAY"] || $arrayCaseSchedulerData["SCH_MONTHS"] != $arrayCaseSchedulerDataOld["SCH_MONTHS"];
                    break;
                case 4:
                    //One time only
                    $flagUpdateTimeNextRun = $recalculateTime || $arrayCaseSchedulerData["SCH_START_TIME"] != $arrayCaseSchedulerDataOld["SCH_START_TIME"];
                    break;
                case 5:
                    //Every
                    $flagUpdateTimeNextRun = $option == "INS" || $arrayCaseSchedulerData["SCH_REPEAT_EVERY"] != $arrayCaseSchedulerDataOld["SCH_REPEAT_EVERY"];
                    break;
            }

            if ($flagUpdateTimeNextRun) {
                $caseSchedulerTimeNextRunNew = $caseScheduler->getTimeNextRunByDate($arrayCaseSchedulerData, date("Y-m-d H:i:s"));
            }

            //Plugin
            if (isset($arrayData["CASE_SH_PLUGIN_UID"]) && $arrayData["CASE_SH_PLUGIN_UID"] != "") {
                $oPluginRegistry = PluginRegistry::loadSingleton();
                $activePluginsForCaseScheduler = $oPluginRegistry->getCaseSchedulerPlugins();

                $params = explode("--", $arrayData["CASE_SH_PLUGIN_UID"]);
                /** @var CaseSchedulerPlugin $caseSchedulerPluginDetail */
                foreach ($activePluginsForCaseScheduler as $caseSchedulerPluginDetail) {
                    if ($caseSchedulerPluginDetail->equalNamespaceTo($params[0]) && $caseSchedulerPluginDetail->equalActionIdTo($params[1])) {
                        $caseSchedulerSelected = $caseSchedulerPluginDetail;
                    }
                }

                if (isset($caseSchedulerSelected) && is_object($caseSchedulerSelected)) {
                    //Save the form
                    $arrayDataPlugin["SCH_UID"] = $arrayCaseSchedulerData["SCH_UID"];
                    $oPluginRegistry->executeMethod($caseSchedulerPluginDetail->getNamespace(), $caseSchedulerPluginDetail->getActionSave(), $arrayDataPlugin);
                }
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }
}

