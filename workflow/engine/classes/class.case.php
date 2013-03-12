<?php

/**
 * class.case.php
 * @package    workflow.engine.classes
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
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * For more information, contact Colosa Inc, 2566 Le Jeune Rd.,
 * Coral Gables, FL, 33134, USA, or email info@colosa.com.
 *
 */
/* require_once ("classes/model/Application.php");
  require_once ("classes/model/AppCacheView.php");
  require_once ("classes/model/AppDelay.php");
  require_once ("classes/model/AppDelegation.php");
  require_once ("classes/model/AppDocument.php");
  require_once ("classes/model/AppEvent.php");
  require_once ("classes/model/AppHistory.php");
  require_once ("classes/model/AppMessage.php");
  require_once ("classes/model/AppNotes.php");
  require_once ("classes/model/AppOwner.php");
  require_once ("classes/model/AppSolrQueue.php");
  require_once ("classes/model/AppThread.php");
  require_once ("classes/model/CaseTracker.php");
  require_once ("classes/model/CaseTrackerObject.php");
  require_once ("classes/model/Configuration.php");
  require_once ("classes/model/Content.php");
  require_once ("classes/model/DbSource.php");
  require_once ("classes/model/Dynaform.php"); */
//require_once ("classes/model/InputDocument.php");
//require_once ("classes/model/Language.php");
//require_once ("classes/model/ObjectPermission.php");
//require_once ("classes/model/OutputDocument.php");
//require_once ("classes/model/Process.php");
//require_once ("classes/model/ProcessUser.php");
//require_once ("classes/model/ReportTable.php");
//require_once ("classes/model/ReportVar.php");
//require_once ("classes/model/Route.php");
//require_once ("classes/model/Step.php");
//require_once ("classes/model/StepSupervisor.php");
//require_once ("classes/model/StepTrigger.php");
//require_once ("classes/model/SubApplication.php");
//require_once ("classes/model/Task.php");
//require_once ("classes/model/TaskUser.php");
//require_once ("classes/model/Triggers.php");
//require_once ("classes/model/Users.php");

G::LoadClass("pmScript");

/**
 * A Cases object where you can do start, load, update, refresh about cases
 * This object is applied to Task
 * @package    workflow.engine.classes
 */
class Cases
{

    private $appSolr = null;

    public function __construct()
    {
        //get Solr initialization variables
        if (($solrConf = System::solrEnv()) !== false) {
            G::LoadClass('AppSolr');
            $this->appSolr = new AppSolr($solrConf['solr_enabled'], $solrConf['solr_host'], $solrConf['solr_instance']);
        }
    }

    /*
     * Ask if an user can start a case
     * @param string $sUIDUser
     * @return boolean
     */

    public function canStartCase($sUIDUser = '', $processUid = '')
    {
        $c = new Criteria();
        $c->clearSelectColumns();
        $c->addSelectColumn('COUNT(*)');
        $c->addJoin(TaskPeer::PRO_UID, ProcessPeer::PRO_UID, Criteria::LEFT_JOIN);
        $c->addJoin(TaskPeer::TAS_UID, TaskUserPeer::TAS_UID, Criteria::LEFT_JOIN);
        $c->add(ProcessPeer::PRO_STATUS, 'ACTIVE');
        $c->add(ProcessPeer::PRO_SUBPROCESS, '0');
        $c->add(TaskPeer::TAS_START, 'TRUE');
        $c->add(TaskUserPeer::USR_UID, $sUIDUser);
        if ($processUid != '') {
            $c->add(TaskPeer::PRO_UID, $processUid);
        }

        $rs = TaskPeer::doSelectRS($c);
        $rs->next();
        $row = $rs->getRow();
        $count = $row[0];
        if ($count > 0) {
            return true;
        }

        //check groups
        G::LoadClass('groups');
        $group = new Groups();
        $aGroups = $group->getActiveGroupsForAnUser($sUIDUser);

        $c = new Criteria();
        $c->clearSelectColumns();
        $c->addSelectColumn('COUNT(*)');
        $c->addJoin(TaskPeer::PRO_UID, ProcessPeer::PRO_UID, Criteria::LEFT_JOIN);
        $c->addJoin(TaskPeer::TAS_UID, TaskUserPeer::TAS_UID, Criteria::LEFT_JOIN);
        $c->add(ProcessPeer::PRO_STATUS, 'ACTIVE');
        $c->add(ProcessPeer::PRO_SUBPROCESS, '0');
        $c->add(TaskPeer::TAS_START, 'TRUE');
        $c->add(TaskUserPeer::USR_UID, $aGroups, Criteria::IN);
        if ($processUid != '') {
            $c->add(TaskPeer::PRO_UID, $processUid);
        }

        $rs = TaskPeer::doSelectRS($c);
        $rs->next();
        $row = $rs->getRow();
        $count = $row[0];
        return ($count > 0);
    }

    /*
     * get user starting tasks
     * @param string $sUIDUser
     * @return $rows
     */

    public function getStartCases($sUIDUser = '')
    {
        $rows[] = array('uid' => 'char', 'value' => 'char');
        $tasks = array();

        $c = new Criteria();
        $c->clearSelectColumns();
        $c->addSelectColumn(TaskPeer::TAS_UID);
        $c->addSelectColumn(TaskPeer::PRO_UID);
        $c->addJoin(TaskPeer::PRO_UID, ProcessPeer::PRO_UID, Criteria::LEFT_JOIN);
        $c->addJoin(TaskPeer::TAS_UID, TaskUserPeer::TAS_UID, Criteria::LEFT_JOIN);
        $c->add(ProcessPeer::PRO_STATUS, 'ACTIVE');
        $c->add(TaskPeer::TAS_START, 'TRUE');
        $c->add(TaskUserPeer::USR_UID, $sUIDUser);

        $rs = TaskPeer::doSelectRS($c);
        $rs->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $rs->next();
        $row = $rs->getRow();

        while (is_array($row)) {
            $tasks[] = $row['TAS_UID'];
            $rs->next();
            $row = $rs->getRow();
        }

        //check groups
        G::LoadClass('groups');
        $group = new Groups();
        $aGroups = $group->getActiveGroupsForAnUser($sUIDUser);

        $c = new Criteria();
        $c->clearSelectColumns();
        $c->addSelectColumn(TaskPeer::TAS_UID);
        $c->addSelectColumn(TaskPeer::PRO_UID);
        $c->addJoin(TaskPeer::PRO_UID, ProcessPeer::PRO_UID, Criteria::LEFT_JOIN);
        $c->addJoin(TaskPeer::TAS_UID, TaskUserPeer::TAS_UID, Criteria::LEFT_JOIN);
        $c->add(ProcessPeer::PRO_STATUS, 'ACTIVE');
        $c->add(TaskPeer::TAS_START, 'TRUE');
        $c->add(TaskUserPeer::USR_UID, $aGroups, Criteria::IN);

        $rs = TaskPeer::doSelectRS($c);
        $rs->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $rs->next();
        $row = $rs->getRow();

        while (is_array($row)) {
            $tasks[] = $row['TAS_UID'];
            $rs->next();
            $row = $rs->getRow();
        }

        $c = new Criteria();
        $c->addSelectColumn(TaskPeer::TAS_UID);
        $c->addSelectColumn(TaskPeer::PRO_UID);
        $c->addAsColumn('TAS_TITLE', 'C1.CON_VALUE');
        $c->addAsColumn('PRO_TITLE', 'C2.CON_VALUE');
        $c->addAlias('C1', 'CONTENT');
        $c->addAlias('C2', 'CONTENT');
        $aConditions = array();
        $aConditions[] = array(TaskPeer::TAS_UID, 'C1.CON_ID');
        $aConditions[] = array(
            'C1.CON_CATEGORY', DBAdapter::getStringDelimiter() . 'TAS_TITLE' . DBAdapter::getStringDelimiter()
        );
        $aConditions[] = array(
            'C1.CON_LANG', DBAdapter::getStringDelimiter() . SYS_LANG . DBAdapter::getStringDelimiter()
        );
        $c->addJoinMC($aConditions, Criteria::LEFT_JOIN);
        $aConditions = array();
        $aConditions[] = array(TaskPeer::PRO_UID, 'C2.CON_ID');
        $aConditions[] = array(
            'C2.CON_CATEGORY', DBAdapter::getStringDelimiter() . 'PRO_TITLE' . DBAdapter::getStringDelimiter()
        );
        $aConditions[] = array(
            'C2.CON_LANG', DBAdapter::getStringDelimiter() . SYS_LANG . DBAdapter::getStringDelimiter()
        );
        $c->addJoinMC($aConditions, Criteria::LEFT_JOIN);
        $c->add(TaskPeer::TAS_UID, $tasks, Criteria::IN);
        $c->addAscendingOrderByColumn('PRO_TITLE');
        $c->addAscendingOrderByColumn('TAS_TITLE');
        $rs = TaskPeer::doSelectRS($c);
        $rs->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $rs->next();
        while ($row = $rs->getRow()) {
            $rows[] = array(
                'uid' => $row['TAS_UID'],
                'value' => $row['PRO_TITLE'] . ' (' . $row['TAS_TITLE'] . ')',
                'pro_uid' => $row['PRO_UID']
            );
            $rs->next();
            $row = $rs->getRow();
        }
        return $rows;
    }

    /*
     * get user starting tasks, but per type (dropdown, radio and category type)
     * @param string $sUIDUser
     * @return $rows
     */

    public function getStartCasesPerType($sUIDUser = '', $typeView = null)
    {
        $rows[] = array('uid' => 'char', 'value' => 'char');
        $tasks = array();

        $c = new Criteria();
        $c->clearSelectColumns();
        $c->addSelectColumn(TaskPeer::TAS_UID);
        $c->addSelectColumn(TaskPeer::PRO_UID);
        $c->addJoin(TaskPeer::PRO_UID, ProcessPeer::PRO_UID, Criteria::LEFT_JOIN);
        $c->addJoin(TaskPeer::TAS_UID, TaskUserPeer::TAS_UID, Criteria::LEFT_JOIN);
        $c->add(ProcessPeer::PRO_STATUS, 'ACTIVE');
        $c->add(TaskPeer::TAS_START, 'TRUE');
        $c->add(TaskUserPeer::USR_UID, $sUIDUser);

        $rs = TaskPeer::doSelectRS($c);
        $rs->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $rs->next();
        $row = $rs->getRow();
        while (is_array($row)) {
            $tasks[] = $row['TAS_UID'];
            $rs->next();
            $row = $rs->getRow();
        }

        //check groups
        G::LoadClass('groups');
        $group = new Groups();
        $aGroups = $group->getActiveGroupsForAnUser($sUIDUser);

        $c = new Criteria();
        $c->clearSelectColumns();
        $c->addSelectColumn(TaskPeer::TAS_UID);
        $c->addSelectColumn(TaskPeer::PRO_UID);
        $c->addJoin(TaskPeer::PRO_UID, ProcessPeer::PRO_UID, Criteria::LEFT_JOIN);
        $c->addJoin(TaskPeer::TAS_UID, TaskUserPeer::TAS_UID, Criteria::LEFT_JOIN);
        $c->add(ProcessPeer::PRO_STATUS, 'ACTIVE');
        $c->add(TaskPeer::TAS_START, 'TRUE');
        $c->add(TaskUserPeer::USR_UID, $aGroups, Criteria::IN);

        $rs = TaskPeer::doSelectRS($c);
        $rs->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $rs->next();
        $row = $rs->getRow();
        while (is_array($row)) {
            $tasks[] = $row['TAS_UID'];
            $rs->next();
            $row = $rs->getRow();
        }

        $c = new Criteria();
        $c->addSelectColumn(TaskPeer::TAS_UID);
        $c->addSelectColumn(TaskPeer::PRO_UID);
        $c->addAsColumn('TAS_TITLE', 'C1.CON_VALUE');
        $c->addAsColumn('PRO_TITLE', 'C2.CON_VALUE');
        $c->addAlias('C1', 'CONTENT');
        $c->addAlias('C2', 'CONTENT');
        if ($typeView == 'category') {
            $c->addAsColumn('PRO_CATEGORY', 'PCS.PRO_CATEGORY');
            $c->addAsColumn('CATEGORY_NAME', 'PCSCAT.CATEGORY_NAME');
            $c->addAlias('PCS', 'PROCESS');
            $c->addAlias('PCSCAT', 'PROCESS_CATEGORY');
            $aConditions = array();
            $aConditions[] = array(TaskPeer::PRO_UID, 'PCS.PRO_UID');
            $c->addJoinMC($aConditions, Criteria::LEFT_JOIN);
            $aConditions = array();
            $aConditions[] = array('PCS.PRO_CATEGORY', 'PCSCAT.CATEGORY_UID');
            $c->addJoinMC($aConditions, Criteria::LEFT_JOIN);
        }

        $aConditions = array();
        $aConditions[] = array(TaskPeer::TAS_UID, 'C1.CON_ID');
        $aConditions[] = array(
            'C1.CON_CATEGORY', DBAdapter::getStringDelimiter() . 'TAS_TITLE' . DBAdapter::getStringDelimiter()
        );
        $aConditions[] = array(
            'C1.CON_LANG', DBAdapter::getStringDelimiter() . SYS_LANG . DBAdapter::getStringDelimiter()
        );
        $c->addJoinMC($aConditions, Criteria::LEFT_JOIN);
        $aConditions = array();
        $aConditions[] = array(TaskPeer::PRO_UID, 'C2.CON_ID');
        $aConditions[] = array(
            'C2.CON_CATEGORY', DBAdapter::getStringDelimiter() . 'PRO_TITLE' . DBAdapter::getStringDelimiter()
        );
        $aConditions[] = array(
            'C2.CON_LANG', DBAdapter::getStringDelimiter() . SYS_LANG . DBAdapter::getStringDelimiter()
        );
        $c->addJoinMC($aConditions, Criteria::LEFT_JOIN);
        $c->add(TaskPeer::TAS_UID, $tasks, Criteria::IN);

        if ($typeView == 'category') {
            $c->addDescendingOrderByColumn('PRO_CATEGORY');
        } else {
            $c->addAscendingOrderByColumn('PRO_TITLE');
            $c->addAscendingOrderByColumn('TAS_TITLE');
        }

        $rs = TaskPeer::doSelectRS($c);
        $rs->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $rs->next();
        while ($row = $rs->getRow()) {
            if ($typeView == 'category') {
                $taskTitle = TaskPeer::retrieveByPK($row['TAS_UID']);
                $row['TAS_TITLE'] = $taskTitle->getTasTitle();
                $row['CATEGORY_NAME'] = ($row['CATEGORY_NAME'] == '') ?
                        G::LoadTranslation('ID_PROCESS_NOCATEGORY') : $row['CATEGORY_NAME'];
                $rows[] = array(
                    'uid' => $row['TAS_UID'],
                    'value' => $row['PRO_TITLE'] . ' (' . $row['TAS_TITLE'] . ')',
                    'pro_uid' => $row['PRO_UID'],
                    'cat' => $row['PRO_CATEGORY'],
                    'catname' => $row['CATEGORY_NAME']
                );
            } else {
                $rows[] = array(
                    'uid' => $row['TAS_UID'],
                    'value' => $row['PRO_TITLE'] . ' (' . $row['TAS_TITLE'] . ')',
                    'pro_uid' => $row['PRO_UID']
                );
            }
            $rs->next();
            $row = $rs->getRow();
        }
        return $rows;
    }

    /*
     * get user's SelfService tasks
     * @param string $sUIDUser
     * @return $rows
     */

    public function getSelfServiceTasks($sUIDUser = '')
    {
        $rows[] = array('uid' => '', 'value' => '');
        $tasks = array();

        $c = new Criteria();
        $c->clearSelectColumns();
        $c->addSelectColumn(TaskPeer::TAS_UID);
        $c->addSelectColumn(TaskPeer::PRO_UID);
        $c->addJoin(TaskPeer::PRO_UID, ProcessPeer::PRO_UID, Criteria::LEFT_JOIN);
        $c->addJoin(TaskPeer::TAS_UID, TaskUserPeer::TAS_UID, Criteria::LEFT_JOIN);
        $c->add(ProcessPeer::PRO_STATUS, 'ACTIVE');
        $c->add(TaskPeer::TAS_ASSIGN_TYPE, 'SELF_SERVICE');
        $c->add(TaskUserPeer::USR_UID, $sUIDUser);

        $rs = TaskPeer::doSelectRS($c);
        $rs->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $rs->next();
        $row = $rs->getRow();

        while (is_array($row)) {
            $tasks[] = $row['TAS_UID'];
            $rs->next();
            $row = $rs->getRow();
        }

        //check groups
        G::LoadClass('groups');
        $group = new Groups();
        $aGroups = $group->getActiveGroupsForAnUser($sUIDUser);

        $c = new Criteria();
        $c->clearSelectColumns();
        $c->addSelectColumn(TaskPeer::TAS_UID);
        $c->addSelectColumn(TaskPeer::PRO_UID);
        $c->addJoin(TaskPeer::PRO_UID, ProcessPeer::PRO_UID, Criteria::LEFT_JOIN);
        $c->addJoin(TaskPeer::TAS_UID, TaskUserPeer::TAS_UID, Criteria::LEFT_JOIN);
        $c->add(ProcessPeer::PRO_STATUS, 'ACTIVE');
        $c->add(TaskPeer::TAS_ASSIGN_TYPE, 'SELF_SERVICE');
        $c->add(TaskUserPeer::USR_UID, $aGroups, Criteria::IN);

        $rs = TaskPeer::doSelectRS($c);
        $rs->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $rs->next();
        $row = $rs->getRow();

        while (is_array($row)) {
            $tasks[] = $row['TAS_UID'];
            $rs->next();
            $row = $rs->getRow();
        }

        $c = new Criteria();
        $c->addSelectColumn(TaskPeer::TAS_UID);
        $c->addSelectColumn(TaskPeer::PRO_UID);
        $c->addAsColumn('TAS_TITLE', 'C1.CON_VALUE');
        $c->addAsColumn('PRO_TITLE', 'C2.CON_VALUE');
        $c->addAlias('C1', 'CONTENT');
        $c->addAlias('C2', 'CONTENT');
        $aConditions = array();
        $aConditions[] = array(TaskPeer::TAS_UID, 'C1.CON_ID');
        $aConditions[] = array(
            'C1.CON_CATEGORY', DBAdapter::getStringDelimiter() . 'TAS_TITLE' . DBAdapter::getStringDelimiter()
        );
        $aConditions[] = array(
            'C1.CON_LANG', DBAdapter::getStringDelimiter() . SYS_LANG . DBAdapter::getStringDelimiter()
        );
        $c->addJoinMC($aConditions, Criteria::LEFT_JOIN);
        $aConditions = array();
        $aConditions[] = array(TaskPeer::PRO_UID, 'C2.CON_ID');
        $aConditions[] = array(
            'C2.CON_CATEGORY', DBAdapter::getStringDelimiter() . 'PRO_TITLE' . DBAdapter::getStringDelimiter()
        );
        $aConditions[] = array(
            'C2.CON_LANG', DBAdapter::getStringDelimiter() . SYS_LANG . DBAdapter::getStringDelimiter()
        );
        $c->addJoinMC($aConditions, Criteria::LEFT_JOIN);
        $c->add(TaskPeer::TAS_UID, $tasks, Criteria::IN);
        $c->addAscendingOrderByColumn('PRO_TITLE');
        $c->addAscendingOrderByColumn('TAS_TITLE');
        $rs = TaskPeer::doSelectRS($c);
        $rs->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $rs->next();
        while ($row = $rs->getRow()) {
            $rows[] = array(
                'uid' => $row['TAS_UID'],
                'value' => $row['PRO_TITLE'] . ' (' . $row['TAS_TITLE'] . ')',
                'pro_uid' => $row['PRO_UID']
            );
            $rs->next();
            $row = $rs->getRow();
        }
        return $rows;
    }

    public function isSelfService($USR_UID, $TAS_UID, $APP_UID = '')
    {
        $tasks = $this->getSelfServiceTasks($USR_UID);

        foreach ($tasks as $key => $val) {
            if ($TAS_UID == $val['uid']) {
                return true;
            }
        }

        if ($APP_UID != '') {
            $groupsInstance = new Groups();
            $groups = $groupsInstance->getActiveGroupsForAnUser($USR_UID);
            $taskInstance = new Task();
            $taskData = $taskInstance->Load($TAS_UID);
            $tasGroupVariable = str_replace(array('@', '#'), '', $taskData['TAS_GROUP_VARIABLE']);
            $caseData = $this->LoadCase($APP_UID);
            if (isset($caseData['APP_DATA'][$tasGroupVariable])) {
                if (trim($caseData['APP_DATA'][$tasGroupVariable]) != '') {
                    if (in_array(trim($caseData['APP_DATA'][$tasGroupVariable]), $groups)) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /*
     * Load an user existing case, this info is used in CaseResume
     * @param string  $sAppUid
     * @param integer $iDelIndex > 0 //get the Delegation fields
     * @return Fields
     */

    public function loadCase($sAppUid, $iDelIndex = 0)
    {
        try {
            $oApp = new Application;
            $aFields = $oApp->Load($sAppUid);
            //$aFields = $oApp->toArray(BasePeer::TYPE_FIELDNAME);
            $appData = @unserialize($aFields['APP_DATA']);

            // BUG 8134, FIX!// for single/double quote troubles // Unserialize with utf8 content get trouble
            if ($appData === false) {
                $appData = preg_replace('!s:(\d+):"(.*?)";!e', "'s:'.strlen('$2').':\"$2\";'", $aFields['APP_DATA']);
                $appData = @unserialize($appData);
            }

            $aFields['APP_DATA'] = G::array_merges(G::getSystemConstants(), $appData);
            switch ($oApp->getAppStatus()) {
                case 'COMPLETED':
                    $aFields['STATUS'] = G::LoadTranslation('ID_COMPLETED');
                    break;
                case 'CANCELLED':
                    $aFields['STATUS'] = G::LoadTranslation('ID_CANCELLED');
                    break;
                case 'PAUSED':
                    $aFields['STATUS'] = G::LoadTranslation('ID_PAUSED');
                    break;
                case 'DRAFT':
                    $aFields['STATUS'] = G::LoadTranslation('ID_DRAFT');
                    break;
                case 'TO_DO':
                    $aFields['STATUS'] = G::LoadTranslation('ID_TO_DO');
                    break;
            }
            $oUser = new Users();
            try {
                $oUser->load($oApp->getAppInitUser());
                $uFields = $oUser->toArray(BasePeer::TYPE_FIELDNAME);
                //$aFields['TITLE'] = $oApp->getAppTitle();
                $aFields['TITLE'] = $aFields['APP_TITLE'];
                $aFields['DESCRIPTION'] = $aFields['APP_DESCRIPTION'];
                $aFields['CREATOR'] = $oUser->getUsrFirstname() . ' ' . $oUser->getUsrLastname();
                $aFields['CREATE_DATE'] = $oApp->getAppCreateDate();
                $aFields['UPDATE_DATE'] = $oApp->getAppUpdateDate();
            } catch (Exception $oError) {
                $aFields['TITLE'] = $oApp->getAppTitle();
                $aFields['DESCRIPTION'] = '';
                $aFields['CREATOR'] = '(USER_DELETED)';
                $aFields['CREATE_DATE'] = $oApp->getAppCreateDate();
                $aFields['UPDATE_DATE'] = $oApp->getAppUpdateDate();
            }

            if ($iDelIndex > 0) {
                //get the Delegation fields,
                $oAppDel = new AppDelegation();
                $oAppDel->Load($sAppUid, $iDelIndex);
                $aAppDel = $oAppDel->toArray(BasePeer::TYPE_FIELDNAME);
                $aFields['TAS_UID'] = $aAppDel['TAS_UID'];
                $aFields['DEL_INDEX'] = $aAppDel['DEL_INDEX'];
                $aFields['DEL_PREVIOUS'] = $aAppDel['DEL_PREVIOUS'];
                $aFields['DEL_TYPE'] = $aAppDel['DEL_TYPE'];
                $aFields['DEL_PRIORITY'] = $aAppDel['DEL_PRIORITY'];
                $aFields['DEL_THREAD_STATUS'] = $aAppDel['DEL_THREAD_STATUS'];
                $aFields['DEL_THREAD'] = $aAppDel['DEL_THREAD'];
                $aFields['DEL_DELEGATE_DATE'] = $aAppDel['DEL_DELEGATE_DATE'];
                $aFields['DEL_INIT_DATE'] = $aAppDel['DEL_INIT_DATE'];
                $aFields['DEL_TASK_DUE_DATE'] = $aAppDel['DEL_TASK_DUE_DATE'];
                $aFields['DEL_FINISH_DATE'] = $aAppDel['DEL_FINISH_DATE'];
                $aFields['CURRENT_USER_UID'] = $aAppDel['USR_UID'];
                try {
                    $oCurUser = new Users();
                    $oCurUser->load($aAppDel['USR_UID']);
                    $aFields['CURRENT_USER'] = $oCurUser->getUsrFirstname() . ' ' . $oCurUser->getUsrLastname();
                } catch (Exception $oError) {
                    $aFields['CURRENT_USER'] = '';
                }
            }
            return $aFields;
        } catch (exception $e) {
            throw ($e);
        }
    }

    /*
     * LoadCaseByNumber
     * @param string $caseNumber
     * @return $aCases
     */

    public function loadCaseByNumber($sCaseNumber)
    {
        //('SELECT * FROM APP_DELEGATION WHERE APP_PROC_CODE="'.$sCaseNumber.'" ');
        try {
            $aCases = array();
            $c = new Criteria();
            $c->add(ApplicationPeer::APP_PROC_CODE, $sCaseNumber);
            $rs = ApplicationPeer::doSelectRs($c);
            $rs->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $rs->next();
            $row = $rs->getRow();
            while (is_array($row)) {
                $case['APP_UID'] = $row['APP_UID'];
                $case['APP_NUMBER'] = $row['APP_NUMBER'];
                $case['APP_STATUS'] = $row['APP_STATUS'];
                $case['PRO_UID'] = $row['PRO_UID'];
                $case['APP_PARALLEL'] = $row['APP_PARALLEL'];
                $case['APP_CUR_USER'] = $row['APP_CUR_USER'];
                $aCases[] = $case;
                $rs->next();
                $row = $rs->getRow();
            }
            return $aCases;
        } catch (exception $e) {
            throw ($e);
        }
    }

    /*
     * This function loads the label case
     * PROCESO:
     *    If there is a label then it is loaded
     *    To get APP_DELEGATIONS that they are opened in the case
     *    To look for APP_DELEGATIONS wich TASK in it, It has a label defined(CASE_TITLE)
     *    We need to read the last APP_DELEGATION->TASK
     * @param string $sAppUid
     * @param string $aAppData
     * @param string $sLabel
     * @return $appLabel
     */

    public function refreshCaseLabel($sAppUid, $aAppData, $sLabel)
    {
        $getAppLabel = "getApp$sLabel";
        $getTasDef = "getTasDef$sLabel";
        $oApplication = new Application;
        if (!$oApplication->exists($sAppUid)) {
            return null;
        } else {
            $oApplication->load($sAppUid);
            $appLabel = $oApplication->$getAppLabel();
        }
        $cri = new Criteria;
        $cri->add(AppDelegationPeer::APP_UID, $sAppUid);
        $cri->add(AppDelegationPeer::DEL_THREAD_STATUS, "OPEN");
        $currentDelegations = AppDelegationPeer::doSelect($cri);
        for ($r = count($currentDelegations) - 1; $r >= 0; $r--) {
            $task = TaskPeer::retrieveByPk($currentDelegations[$r]->getTasUid());
            $caseLabel = $task->$getTasDef();
            if ($caseLabel != '') {
                $appLabel = G::replaceDataField($caseLabel, $aAppData);
                break;
            }
        }
        return $appLabel;
    }

    /*
     * This function loads the title and description label in a case
     * PROCESO:
     *    If there is a label then it is loaded
     *    To get APP_DELEGATIONS that they are opened in the case
     *    To look for APP_DELEGATIONS wich TASK in it, It has a label defined(CASE_TITLE)
     *    We need to read the last APP_DELEGATION->TASK
     * @param string $sAppUid
     * @param array $aAppData
     * @return $res
     */

    public function refreshCaseTitleAndDescription($sAppUid, $aAppData)
    {
        $res['APP_TITLE'] = null;
        $res['APP_DESCRIPTION'] = null;
        //$res['APP_PROC_CODE']   = null;

        $oApplication = new Application;
        try {
            $fields = $oApplication->load($sAppUid);
        } catch (Exception $e) {
            return $res;
        }

        $res['APP_TITLE'] = $fields['APP_TITLE']; // $oApplication->$getAppLabel();
        $res['APP_DESCRIPTION'] = $fields['APP_DESCRIPTION'];

        $lang = defined('SYS_LANG') ? SYS_LANG : 'en';
        $bUpdatedDefTitle = false;
        $bUpdatedDefDescription = false;
        $cri = new Criteria;
        $cri->add(AppDelegationPeer::APP_UID, $sAppUid);
        $cri->add(AppDelegationPeer::DEL_THREAD_STATUS, "OPEN");
        $currentDelegations = AppDelegationPeer::doSelect($cri);
        //load only the tas_def fields, because these three or two values are needed
        for ($r = count($currentDelegations) - 1; $r >= 0; $r--) {
            $c = new Criteria();
            $c->clearSelectColumns();
            $c->addSelectColumn(ContentPeer::CON_CATEGORY);
            $c->addSelectColumn(ContentPeer::CON_VALUE);
            $c->add(ContentPeer::CON_ID, $currentDelegations[$r]->getTasUid());
            $c->add(ContentPeer::CON_LANG, $lang);
            $rs = TaskPeer::doSelectRS($c);
            $rs->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $rs->next();
            $row = $rs->getRow();

            while (is_array($row)) {
                switch ($row['CON_CATEGORY']) {
                    case 'TAS_DEF_TITLE':
                        $tasDefTitle = $row['CON_VALUE'];
                        if ($tasDefTitle != '' && !$bUpdatedDefTitle) {
                            $res['APP_TITLE'] = G::replaceDataField($tasDefTitle, $aAppData);
                            $bUpdatedDefTitle = true;
                        }
                        break;
                    case 'TAS_DEF_DESCRIPTION':
                        $tasDefDescription = $row['CON_VALUE'];
                        $tasDefDescription = $row['CON_VALUE'];
                        if ($tasDefDescription != '' && !$bUpdatedDefDescription) {
                            $res['APP_DESCRIPTION'] = G::replaceDataField($tasDefDescription, $aAppData);
                            $bUpdatedDefDescription = true;
                        }
                        break;
                }
                $rs->next();
                $row = $rs->getRow();
            }
        }
        return $res;
    }

    /*
     * optimized for speed. This function loads the title and description label in a case
     *    If there is a label then it is loaded
     *    Get Open APP_DELEGATIONS in the case
     *    To look for APP_DELEGATIONS wich TASK in it, It has a label defined(CASE_TITLE)
     *    We need to read the last APP_DELEGATION->TASK
     * @param string $sAppUid
     * @param array $aAppData
     * @return $res
     */

    public function newRefreshCaseTitleAndDescription($sAppUid, $fields, $aAppData)
    {
        $res = array();

        $lang = defined('SYS_LANG') ? SYS_LANG : 'en';
        $bUpdatedDefTitle = false;
        $bUpdatedDefDescription = false;

        $cri = new Criteria;
        $cri->clearSelectColumns();
        $cri->addSelectColumn(AppDelegationPeer::TAS_UID);
        $cri->add(AppDelegationPeer::APP_UID, $sAppUid);
        $cri->add(AppDelegationPeer::DEL_THREAD_STATUS, "OPEN");
        $rsCri = AppDelegationPeer::doSelectRS($cri);
        $rsCri->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $rsCri->next();
        $rowCri = $rsCri->getRow();

        //load only the tas_def fields, because these three or two values are needed
        while (is_array($rowCri)) {
            $c = new Criteria();
            $c->clearSelectColumns();
            $c->addSelectColumn(ContentPeer::CON_CATEGORY);
            $c->addSelectColumn(ContentPeer::CON_VALUE);
            $c->add(ContentPeer::CON_ID, $rowCri['TAS_UID']);
            $c->add(ContentPeer::CON_LANG, $lang);
            $rs = TaskPeer::doSelectRS($c);
            $rs->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $rs->next();
            $row = $rs->getRow();
            while (is_array($row)) {
                switch ($row['CON_CATEGORY']) {
                    case 'TAS_DEF_TITLE':
                        if ($bUpdatedDefTitle) {
                            break;
                        }
                        $tasDefTitle = trim($row['CON_VALUE']);
                        if ($tasDefTitle != '') {
                            $newAppTitle = G::replaceDataField($tasDefTitle, $aAppData);
                            $res['APP_TITLE'] = $newAppTitle;
                            if (isset($fields['APP_TITLE']) && $fields['APP_TITLE'] == $newAppTitle) {
                                break;
                            }
                            $bUpdatedDefTitle = true;
                            /// updating the value in content for row (APP_TITLE,$lan)
                            $con = Propel::getConnection('workflow');
                            $c1 = new Criteria('workflow');
                            $c1->add(ContentPeer::CON_CATEGORY, 'APP_TITLE');
                            $c1->add(ContentPeer::CON_ID, $sAppUid);
                            $c1->add(ContentPeer::CON_LANG, $lang);

                            // update set
                            $c2 = new Criteria('workflow');
                            $c2->add(ContentPeer::CON_VALUE, $newAppTitle);
                            BasePeer::doUpdate($c1, $c2, $con);
                        }
                        break;
                    case 'TAS_DEF_DESCRIPTION':
                        if ($bUpdatedDefDescription) {
                            break;
                        }
                        $tasDefDescription = trim($row['CON_VALUE']);
                        if ($tasDefDescription != '') {
                            $newAppDescription = G::replaceDataField($tasDefDescription, $aAppData);
                            $res['APP_DESCRIPTION'] = $newAppDescription;
                            if (isset($fields['APP_DESCRIPTION']) &&
                                    $fields['APP_DESCRIPTION'] == $newAppDescription) {
                                break;
                            }
                            $bUpdatedDefDescription = true;
                            /// updating the value in content for row (APP_TITLE,$lan)
                            $con = Propel::getConnection('workflow');
                            $c1 = new Criteria('workflow');
                            $c1->add(ContentPeer::CON_CATEGORY, 'APP_DESCRIPTION');
                            $c1->add(ContentPeer::CON_ID, $sAppUid);
                            $c1->add(ContentPeer::CON_LANG, $lang);
                            // update set
                            $c2 = new Criteria('workflow');
                            $c2->add(ContentPeer::CON_VALUE, $newAppDescription);
                            BasePeer::doUpdate($c1, $c2, $con);
                        }
                        break;
                }
                $rs->next();
                $row = $rs->getRow();
            }
            $rsCri->next();
            $rowCri = $rsCri->getRow();
        }
        return $res;
    }

    /**
     * Small function, it uses to return the title from a case
     *
     *
     * @name refreshCaseTitle
     * @param  string $sAppUid
     * @param  array $aAppData
     * @access public
     * @return $appLabel
     */
    public function refreshCaseTitle($sAppUid, $aAppData)
    {
        return $this->refreshCaseLabel($sAppUid, $aAppData, "Title");
    }

    /**
     * Small function, it uses to return the description from a case
     *
     *
     * @name refreshCaseDescription
     * @param  string $sAppUid
     * @param  array $aAppData
     * @access public
     * @return $appLabel
     */
    public function refreshCaseDescription($sAppUid, $aAppData)
    {
        return $this->refreshCaseLabel($sAppUid, $aAppData, "Description");
    }

    /**
     * Small function, it uses to return the code process from a case
     *
     *
     * @name refreshCaseDescription
     * @param  string $sAppUid
     * @param  array $aAppData
     * @access public
     * @return $appLabel
     */
    public function refreshCaseStatusCode($sAppUid, $aAppData)
    {
        return $this->refreshCaseLabel($sAppUid, $aAppData, "ProcCode");
    }

    /**
     * This function return an array without difference
     *
     *
     * @name arrayRecursiveDiff
     * @param  array $aArray1
     * @param  array $aArray2
     * @access public
     * @return $appLabel
     */
    public function arrayRecursiveDiff($aArray1, $aArray2)
    {
        $aReturn = array();
        foreach ($aArray1 as $mKey => $mValue) {
            if (array_key_exists($mKey, $aArray2)) {
                if (is_array($mValue)) {
                    $aRecursiveDiff = $this->arrayRecursiveDiff($mValue, $aArray2[$mKey]);
                    if (count($aRecursiveDiff)) {
                        $aReturn[$mKey] = $aRecursiveDiff;
                    }
                } else {
                    if ($mValue != $aArray2[$mKey]) {
                        $aReturn[$mKey] = $aArray2[$mKey];
                    }
                }
            } else {
                $aReturn[$mKey] = isset($aArray2[$mKey]) ? $aArray2[$mKey] : null;
            }
        }
        return $aReturn;
    }

    /*
     * Update an existing case, this info is used in CaseResume
     *
     * @name updateCase
     * @param string  $sAppUid
     * @param integer $iDelIndex > 0 //get the Delegation fields
     * @return Fields
     */

    public function updateCase($sAppUid, $Fields = array())
    {
        try {
            $aApplicationFields = $Fields['APP_DATA'];
            $Fields['APP_UID'] = $sAppUid;
            $Fields['APP_UPDATE_DATE'] = 'now';
            $Fields['APP_DATA'] = serialize($Fields['APP_DATA']);
            /*
              $oApp = new Application;
              $appFields = $oApp->load($sAppUid);
             */
            $oApp = ApplicationPeer::retrieveByPk($sAppUid);
            $appFields = $oApp->toArray(BasePeer::TYPE_FIELDNAME);
            if (isset($Fields['APP_TITLE'])) {
                $appFields['APP_TITLE'] = $Fields['APP_TITLE'];
            }
            if (isset($Fields['APP_DESCRIPTION'])) {
                $appFields['APP_DESCRIPTION'] = $Fields['APP_DESCRIPTION'];
            }
            $newValues = $this->newRefreshCaseTitleAndDescription($sAppUid, $appFields, $aApplicationFields);

            //Start: Save History --By JHL
            if (isset($Fields['CURRENT_DYNAFORM'])) {
                //only when that variable is set.. from Save
                $FieldsBefore = $this->loadCase($sAppUid);
                $FieldsDifference = $this->arrayRecursiveDiff($FieldsBefore['APP_DATA'], $aApplicationFields);
                $fieldsOnBoth = array_intersect_assoc($FieldsBefore['APP_DATA'], $aApplicationFields);
                //Add fields that weren't in previous version
                foreach ($aApplicationFields as $key => $value) {
                    if (!(isset($fieldsOnBoth[$key]))) {
                        $FieldsDifference[$key] = $value;
                    }
                }
                if ((is_array($FieldsDifference)) && (count($FieldsDifference) > 0)) {
                    //There are changes
                    $appHistory = new AppHistory();
                    $aFieldsHistory = $Fields;
                    $aFieldsHistory['APP_DATA'] = serialize($FieldsDifference);
                    $appHistory->insertHistory($aFieldsHistory);
                }
            }
            //End Save History
            //we are removing the app_title and app_description from this array,
            //because they already be updated in  newRefreshCaseTitleAndDescription function
            if (isset($Fields['APP_TITLE'])) {
                unset($Fields['APP_TITLE']);
            }
            if (isset($Fields['APP_DESCRIPTION'])) {
                unset($Fields['APP_DESCRIPTION']);
            }
            $oApp->update($Fields);

            $DEL_INDEX = isset($Fields['DEL_INDEX']) ? $Fields['DEL_INDEX'] : '';
            $TAS_UID = isset($Fields['TAS_UID']) ? $Fields['TAS_UID'] : '';

            G::LoadClass('reportTables');
            require_once 'classes/model/AdditionalTables.php';
            $oReportTables = new ReportTables();
            $addtionalTables = new additionalTables();

            $oReportTables->updateTables($appFields['PRO_UID'], $sAppUid, $Fields['APP_NUMBER'], $aApplicationFields);
            $addtionalTables->updateReportTables(
                    $appFields['PRO_UID'], $sAppUid, $Fields['APP_NUMBER'], $aApplicationFields, $Fields['APP_STATUS']
            );

            //now update the priority in appdelegation table, using the defined variable in task
            if (trim($DEL_INDEX) != '' && trim($TAS_UID) != '') {
                //optimized code to avoid load task content row.
                $c = new Criteria();
                $c->clearSelectColumns();
                $c->addSelectColumn(TaskPeer::TAS_PRIORITY_VARIABLE);
                $c->add(TaskPeer::TAS_UID, $TAS_UID);
                $rs = TaskPeer::doSelectRS($c);
                $rs->setFetchmode(ResultSet::FETCHMODE_ASSOC);
                $rs->next();
                $row = $rs->getRow();
                $VAR_PRI = substr($row['TAS_PRIORITY_VARIABLE'], 2);
                //end optimized code.

                $x = unserialize($Fields['APP_DATA']);
                if (isset($x[$VAR_PRI])) {
                    if (trim($x[$VAR_PRI]) != '') {
                        $oDel = new AppDelegation;
                        $array = array();
                        $array['APP_UID'] = $sAppUid;
                        $array['DEL_INDEX'] = $DEL_INDEX;
                        $array['TAS_UID'] = $TAS_UID;
                        $array['DEL_PRIORITY'] = (isset($x[$VAR_PRI]) ?
                                        ($x[$VAR_PRI] >= 1 && $x[$VAR_PRI] <= 5 ? $x[$VAR_PRI] : '3') : '3');
                        $oDel->update($array);
                    }
                }
            }
            //Update Solr Index
            if ($this->appSolr != null) {
                $this->appSolr->updateApplicationSearchIndex($sAppUid);
            }
            return $Fields;
        } catch (exception $e) {
            throw ($e);
        }
    }

    /*
     * Remove an existing case,
     *
     * @name removeCase
     * @param string  $sAppUid
     * @return Fields
     */

    public function removeCase($sAppUid)
    {
        try {
            $this->getExecuteTriggerProcess($sAppUid, 'DELETED');

            $oAppDelegation = new AppDelegation();
            $oAppDocument = new AppDocument();

            //Delete the delegations of a application
            $oCriteria2 = new Criteria('workflow');
            $oCriteria2->add(AppDelegationPeer::APP_UID, $sAppUid);
            $oDataset2 = AppDelegationPeer::doSelectRS($oCriteria2);
            $oDataset2->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $oDataset2->next();
            while ($aRow2 = $oDataset2->getRow()) {
                $oAppDelegation->remove($sAppUid, $aRow2['DEL_INDEX']);
                $oDataset2->next();
            }
            //Delete the documents assigned to a application
            $oCriteria2 = new Criteria('workflow');
            $oCriteria2->add(AppDocumentPeer::APP_UID, $sAppUid);
            $oDataset2 = AppDocumentPeer::doSelectRS($oCriteria2);
            $oDataset2->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $oDataset2->next();
            while ($aRow2 = $oDataset2->getRow()) {
                $oAppDocument->remove($aRow2['APP_DOC_UID'], $aRow2['DOC_VERSION']);
                $oDataset2->next();
            }
            //Delete the actions from a application
            $oCriteria2 = new Criteria('workflow');
            $oCriteria2->add(AppDelayPeer::APP_UID, $sAppUid);
            AppDelayPeer::doDelete($oCriteria2);
            //Delete the messages from a application
            $oCriteria2 = new Criteria('workflow');
            $oCriteria2->add(AppMessagePeer::APP_UID, $sAppUid);
            AppMessagePeer::doDelete($oCriteria2);
            //Delete the threads from a application
            $oCriteria2 = new Criteria('workflow');
            $oCriteria2->add(AppThreadPeer::APP_UID, $sAppUid);
            AppThreadPeer::doDelete($oCriteria2);
            //Delete the events from a application
            $criteria = new Criteria("workflow");
            $criteria->add(AppEventPeer::APP_UID, $sAppUid);
            AppEventPeer::doDelete($criteria);
            //Delete the histories from a application
            $criteria = new Criteria("workflow");
            $criteria->add(AppHistoryPeer::APP_UID, $sAppUid);
            AppHistoryPeer::doDelete($criteria);
            //Delete the notes from a application
            $criteria = new Criteria("workflow");
            $criteria->add(AppNotesPeer::APP_UID, $sAppUid);
            AppNotesPeer::doDelete($criteria);
            //Delete the owners from a application
            $criteria = new Criteria("workflow");
            $criteria->add(AppOwnerPeer::APP_UID, $sAppUid);
            AppOwnerPeer::doDelete($criteria);
            //Delete the SolrQueue from a application
            $criteria = new Criteria("workflow");
            $criteria->add(AppSolrQueuePeer::APP_UID, $sAppUid);
            AppSolrQueuePeer::doDelete($criteria);

            //Before delete verify if is a child case
            $oCriteria2 = new Criteria('workflow');
            $oCriteria2->add(SubApplicationPeer::APP_UID, $sAppUid);
            $oCriteria2->add(SubApplicationPeer::SA_STATUS, 'ACTIVE');
            if (SubApplicationPeer::doCount($oCriteria2) > 0) {
                G::LoadClass('derivation');
                $oDerivation = new Derivation();
                $oDerivation->verifyIsCaseChild($sAppUid);
            }
            //Delete the registries in the table SUB_APPLICATION
            $oCriteria2 = new Criteria('workflow');
            $oCriteria2->add(SubApplicationPeer::APP_UID, $sAppUid);
            SubApplicationPeer::doDelete($oCriteria2);
            $oCriteria2 = new Criteria('workflow');
            $oCriteria2->add(SubApplicationPeer::APP_PARENT, $sAppUid);
            SubApplicationPeer::doDelete($oCriteria2);

            //Delete record of the APPLICATION table (trigger: delete records of the APP_CACHE_VIEW table)
            $application = new Application();
            $result = $application->remove($sAppUid);

            //delete application from index
            if ($this->appSolr != null) {
                $this->appSolr->deleteApplicationSearchIndex($sAppUid);
            }

            return $result;
        } catch (exception $e) {
            throw ($e);
        }
    }

    /*
     * Set the DEL_INIT_DATE
     *
     * @name setDelInitDate
     * @param string $sAppUid
     * @param string $iDelIndex
     * @return Fields
     */

    public function setDelInitDate($sAppUid, $iDelIndex)
    {
        try {
            $oAppDel = AppDelegationPeer::retrieveByPk($sAppUid, $iDelIndex);
            $oAppDel->setDelInitDate("now");
            $oAppDel->save();
            //update searchindex
            if ($this->appSolr != null) {
                $this->appSolr->updateApplicationSearchIndex($sAppUid);
            }
        } catch (exception $e) {
            throw ($e);
        }
    }

    /*
     * Set user who is claim (ex-catch) a self service routing
     *
     * @name setCatchUser
     * @param string $sAppUid
     * @param string $iDelIndex
     * @param string $usrId
     * @return Fields
     */

    public function setCatchUser($sAppUid, $iDelIndex, $usrId)
    {
        try {
            $oAppDel = AppDelegationPeer::retrieveByPk($sAppUid, $iDelIndex);
            $oAppDel->setDelInitDate("now");
            $oAppDel->setUsrUid($usrId);
            $oAppDel->save();

            //update searchindex
            if ($this->appSolr != null) {
                $this->appSolr->updateApplicationSearchIndex($sAppUid);
            }
        } catch (exception $e) {
            throw ($e);
        }
    }

    /*
     * GetOpenThreads
     *
     * @name GetOpenThreads
     * @param string $sAppUid
     * @return $row (number of APP_DELEGATION rows)
     */

    public function GetOpenThreads($sAppUid)
    {
        try {
            $c = new Criteria();
            $c->clearSelectColumns();
            $c->addSelectColumn('COUNT(*)');
            $c->add(AppDelegationPeer::APP_UID, $sAppUid);
            $c->add(AppDelegationPeer::DEL_THREAD_STATUS, 'OPEN');
            $rs = AppDelegationPeer::doSelectRs($c);
            $rs->next();
            $row = $rs->getRow();
            return intval($row[0]);
        } catch (exception $e) {
            throw ($e);
        }
    }

    /*
     * this function returns SiblingThreads in a task
     *
     * @name getSiblingThreads
     * @param string $sAppUid
     * @param string $iDelIndex
     * @return $aThreads
     */

    public function getSiblingThreads($sAppUid, $iDelIndex)
    {
        try {
            //get the parent thread
            $c = new Criteria();
            $c->add(AppThreadPeer::APP_UID, $sAppUid);
            $c->add(AppThreadPeer::DEL_INDEX, $iDelIndex);
            $rs = AppThreadPeer::doSelectRs($c);
            $rs->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $rs->next();
            $row = $rs->getRow();
            $iParent = $row['APP_THREAD_PARENT'];

            //get the sibling
            $aThreads = array();
            $c = new Criteria();
            $c->add(AppThreadPeer::APP_UID, $sAppUid);
            $c->add(AppThreadPeer::APP_THREAD_PARENT, $iParent);
            $c->add(AppThreadPeer::DEL_INDEX, $iDelIndex, Criteria::NOT_EQUAL);
            $rs = AppThreadPeer::doSelectRs($c);
            $rs->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $rs->next();
            $row = $rs->getRow();
            while (is_array($row)) {
                $aThreads[] = $row;
                $rs->next();
                $row = $rs->getRow();
            }
            return $aThreads;
        } catch (exception $e) {
            throw ($e);
        }
    }

    /*
     * This function returns the threads open in a task
     * get an array with all sibling threads open from next task
     *
     * @name getOpenSiblingThreads,
     * @param string $sNextTask
     * @param string $sAppUid
     * @param string $iDelIndex
     * @param string $sCurrentTask
     * @return $aThreads
     */

    public function getOpenSiblingThreads($sNextTask, $sAppUid, $iDelIndex, $sCurrentTask)
    {
        try {
            //Get all tasks that are previous to my NextTask, we want to know if there are pending task for my nexttask
            //we need to filter only seq joins going to my next task
            //and we are removing the current task from the search
            $aThreads = array();
            $oCriteria = new Criteria('workflow');
            $oCriteria->add(RoutePeer::ROU_NEXT_TASK, $sNextTask);
            $oCriteria->add(RoutePeer::TAS_UID, $sCurrentTask, Criteria::NOT_EQUAL);
            $oCriteria->add(RoutePeer::ROU_TYPE, 'SEC-JOIN');
            $oDataset = RoutePeer::doSelectRs($oCriteria);
            $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $oDataset->next();
            while ($aRow = $oDataset->getRow()) {
                $aPrevious = $this->searchOpenPreviousTasks($aRow['TAS_UID'], $sAppUid);
                if (is_array($aPrevious) && count($aPrevious) > 0) {
                    $aThreads[] = array_merge($aPrevious, $aThreads);
                }
                $oDataset->next();
            }
            return $aThreads;
        } catch (exception $e) {
            throw ($e);
        }
    }

    /**
     * This function looks for the open previous task
     * get an array with all sibling previous threads open from next task
     *
     * @name searchOpenPreviousTasks,
     * @param string $taskUid
     * @param string $sAppUid
     * @param array $aPreviousTasks optional array that serves to trace the task routes and avoid infinite loops.
     * @return $aThreads
     */
    public function searchOpenPreviousTasks($taskUid, $sAppUid, $aPreviousTasks = array())
    {
        //in this array we are storing all open delegation rows.
        $aTaskReviewed = array();

        //check if this task ( $taskUid ) has open delegations
        $delegations = $this->getReviewedTasks($taskUid, $sAppUid);

        if ($delegations !== false) {
            if (count($delegations['open']) > 0) {
                //there is an open delegation, so we need to return the delegation row
                return $delegations['open'];
            } else {
                return array(); //returning empty array
            }
        }
        // if not we check previous tasks
        // until here this task has not appdelegations records.
        // get all previous task from $taskUid, and return open delegations rows, if there are

        $oCriteria = new Criteria('workflow');
        $oCriteria->add(RoutePeer::ROU_NEXT_TASK, $taskUid);
        $oDataset = RoutePeer::doSelectRs($oCriteria);
        $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $oDataset->next();
        $aRow = $oDataset->getRow();

        while (is_array($aRow)) {
            $delegations = $this->getReviewedTasks($aRow['TAS_UID'], $sAppUid);
            if ($delegations !== false) {
                if (count($delegations['open']) > 0) {
                    //there is an open delegation, so we need to return the delegation row
                    $aTaskReviewed = array_merge($aTaskReviewed, $delegations['open']);
                } else {
                    if ($aRow['ROU_TYPE'] == 'PARALLEL-BY-EVALUATION') {
                        $aTaskReviewed = array();
                    } else {
                        $aTaskReviewed = array_merge($aTaskReviewed, $delegations['closed']);
                    }
                }
            } else {
                if (!in_array($aRow['TAS_UID'], $aPreviousTasks)) {
                    // storing the current task uid of the task currently checked
                    $aPreviousTasks[] = $aRow['TAS_UID'];
                    // passing the array of previous tasks in oprder to avoid an infinite loop that prevents
                    $openPreviousTask = $this->searchOpenPreviousTasks($aRow['TAS_UID'], $sAppUid, $aPreviousTasks);
                    if (count($aPreviousTasks) > 0) {
                        $aTaskReviewed = array_merge($aTaskReviewed, $openPreviousTask);
                    }
                }
            }
            $oDataset->next();
            $aRow = $oDataset->getRow();
        }
        return $aTaskReviewed;
    }

    /**
     * Get reviewed tasks (delegations started)
     * @param string $taskUid
     * @param string $sAppUid
     * @author erik amaru ortiz <erik@colosa.com>
     * @return array within the open & closed tasks
     *         false -> when has not any delegation started for that task
     */
    public function getReviewedTasks($taskUid, $sAppUid)
    {
        $openTasks = $closedTasks = array();

        // get all delegations fro this task
        $oCriteria2 = new Criteria('workflow');
        $oCriteria2->add(AppDelegationPeer::APP_UID, $sAppUid);
        $oCriteria2->add(AppDelegationPeer::TAS_UID, $taskUid);

        $oDataset2 = AppDelegationPeer::doSelectRs($oCriteria2);
        $oDataset2->setFetchmode(ResultSet::FETCHMODE_ASSOC);

        // loop and separate open & closed delegations in theirs respective arrays
        while ($oDataset2->next()) {
            $row = $oDataset2->getRow();
            if ($row['DEL_THREAD_STATUS'] == 'OPEN') {
                $openTasks[] = $row;
            } else {
                $closedTasks[] = $row;
            }
        }

        if (count($openTasks) == 0 && count($closedTasks) == 0) {
            return false; // return false because there is not any delegation for this task.
        } else {
            return array('open' => $openTasks, 'closed' => $closedTasks);
        }
    }

    /*
     * This function returns the total number of previous task
     *
     * @name CountTotalPreviousTasks
     * @param string $sTasUid $nextDel['TAS_UID']
     * @return $row[0]
     */

    public function CountTotalPreviousTasks($sTasUid)
    {
        try {
            $c = new Criteria();
            $c->clearSelectColumns();
            $c->addSelectColumn('COUNT(*)');
            $c->add(RoutePeer::ROU_NEXT_TASK, $sTasUid);
            $rs = RoutePeer::doSelectRs($c);
            $rs->next();
            $row = $rs->getRow();
            return intval($row[0]);
        } catch (exception $e) {
            throw ($e);
        }
    }

    /*
     * This function gets open and null  rows in delegation
     *
     * @name getOpenNullDelegations
     * @param string $sAppUid $nextDel['TAS_UID']
     * @param string $sTasUid
     * @return $pendingDel
     */

    public function getOpenNullDelegations($sAppUid, $sTasUid)
    {
        $pendingDel = array();
        try {
            //first query
            $c = new Criteria();
            $c->clearSelectColumns();
            $c->addSelectColumn(AppDelegationPeer::APP_UID);
            $c->addSelectColumn(AppDelegationPeer::DEL_INDEX);
            $c->addSelectColumn(AppDelegationPeer::DEL_PREVIOUS);
            $c->addSelectColumn(AppDelegationPeer::PRO_UID);
            $c->addSelectColumn(AppDelegationPeer::TAS_UID);
            $c->addSelectColumn(AppDelegationPeer::USR_UID);
            $c->addSelectColumn(AppDelegationPeer::DEL_TYPE);
            $c->addSelectColumn(AppDelegationPeer::DEL_PRIORITY);
            $c->addSelectColumn(AppDelegationPeer::DEL_THREAD);
            $c->addSelectColumn(AppDelegationPeer::DEL_THREAD_STATUS);
            $c->addSelectColumn(AppDelegationPeer::DEL_DELEGATE_DATE);
            $c->addSelectColumn(AppDelegationPeer::DEL_INIT_DATE);
            $c->addSelectColumn(AppDelegationPeer::DEL_TASK_DUE_DATE);
            $c->addSelectColumn(AppDelegationPeer::DEL_FINISH_DATE);
            $c->addSelectColumn(RoutePeer::ROU_UID);
            $c->addSelectColumn(RoutePeer::ROU_PARENT);
            $c->addSelectColumn(RoutePeer::ROU_NEXT_TASK);
            $c->addSelectColumn(RoutePeer::ROU_CASE);
            $c->addSelectColumn(RoutePeer::ROU_TYPE);
            $c->addSelectColumn(RoutePeer::ROU_CONDITION);
            $c->addSelectColumn(RoutePeer::ROU_TO_LAST_USER);
            $c->addSelectColumn(RoutePeer::ROU_OPTIONAL);
            $c->addSelectColumn(RoutePeer::ROU_SEND_EMAIL);

            $c->addJoin(AppDelegationPeer::TAS_UID, RoutePeer::TAS_UID);
            $c->add(RoutePeer::ROU_NEXT_TASK, $sTasUid);
            $c->add(AppDelegationPeer::APP_UID, $sAppUid);
            $rs = RoutePeer::doSelectRs($c);
            $rs->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $rs->next();
            $row = $rs->getRow();
            while (is_array($row)) {
                if ($row['DEL_THREAD_STATUS'] == 'OPEN' && $row['APP_UID'] = $sAppUid) {
                    $pendingDel[] = $row;
                }
                $rs->next();
                $row = $rs->getRow();
            }
            return $pendingDel;
        } catch (exception $e) {
            throw ($e);
        }
    }

    /*
     * This function looks for some open rows in delegation
     *
     * @name isRouteOpen
     * @param string $sAppUid $nextDel['APP_UID']
     * @param string $sTasUid $nextDel['TAS_UID']
     * @return true or false
     */

    public function isRouteOpen($sAppUid, $sTasUid)
    {
        try {
            $c = new Criteria();
            $c->clearSelectColumns();
            $c->addSelectColumn('COUNT(*)');
            $c->add(AppDelegationPeer::APP_UID, $sAppUid);
            $c->add(AppDelegationPeer::TAS_UID, $sTasUid);
            $c->add(AppDelegationPeer::DEL_THREAD_STATUS, 'OPEN');
            $rs = RoutePeer::doSelectRs($c);
            $rs->next();
            $row = $rs->getRow();
            $open = ($row[0] >= 1);
            if ($open) {
                return true;
            }
            $c->clearSelectColumns();
            $c->addSelectColumn(AppDelegationPeer::DEL_INDEX);
            $c->addSelectColumn(AppDelegationPeer::USR_UID);
            $c->addSelectColumn(AppDelegationPeer::DEL_TYPE);
            $c->addSelectColumn(AppDelegationPeer::DEL_THREAD);
            $c->addSelectColumn(AppDelegationPeer::DEL_THREAD_STATUS);
            $c->addSelectColumn(RoutePeer::ROU_UID);
            $c->addSelectColumn(RoutePeer::ROU_NEXT_TASK);
            $c->addSelectColumn(RoutePeer::ROU_CASE);
            $c->addSelectColumn(RoutePeer::ROU_TYPE);

            $c->addJoin(AppDelegationPeer::TAS_UID, RoutePeer::TAS_UID);
            $c->add(AppDelegationPeer::APP_UID, $sAppUid);
            $c->add(RoutePeer::ROU_NEXT_TASK, $sTasUid);
            $rs = RoutePeer::doSelectRs($c);
            $rs->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $rs->next();
            $row = $rs->getRow();
            $sql = 'SELECT D.*,R.* ' .
                    'FROM ROUTE R LEFT JOIN APP_DELEGATION D ON (R.TAS_UID=D.TAS_UID) WHERE APP_UID="' .
                    $sAppUid . '" AND ROU_NEXT_TASK="' . $sTasUid . '"';

            while (is_array($row)) {
                switch ($row['DEL_THREAD_STATUS']) {
                    case 'OPEN':
                        //case 'NONE':
                        $open = true;
                        break;
                    case 'CLOSED':
                        //case 'DONE':
                        //case 'NOTDONE':
                        break;
                    case '':
                    case null:
                    default:
                        $open = $this->isRouteOpen($sAppUid, $row['TAS_UID']);
                        break;
                }
                if ($open) {
                    return true;
                }
                $rs->next();
                $row = $rs->getRow();
            }
            return false;
        } catch (exception $e) {
            throw ($e);
        }
    }

    /*
     * This function creates a new row into APP_DELEGATION
     *
     * @name newAppDelegation
     * @param string $sProUid,
     * @param string $sAppUid,
     * @param string $sTasUid,
     * @param string $sUsrUid
     * @param string $sPrevious
     * @param string $iPriority
     * @param string $sDelType
     * @param string $iAppThreadIndex
     * @return void
     */

    public function newAppDelegation($sProUid, $sAppUid, $sTasUid, $sUsrUid, $sPrevious, $iPriority, $sDelType, $iAppThreadIndex = 1, $nextDel = null)
    {
        try {
            $appDel = new AppDelegation();
            $result = $appDel->createAppDelegation(
                    $sProUid, $sAppUid, $sTasUid, $sUsrUid, $iAppThreadIndex, $iPriority, false, $sPrevious, $nextDel
            );
            //update searchindex
            if ($this->appSolr != null) {
                $this->appSolr->updateApplicationSearchIndex($sAppUid);
            }
            return $result;
        } catch (exception $e) {
            throw ($e);
        }
    }

    /*
     * this function is used to update a row in APP_DELEGATION
     *
     *
     * @name updateAppDelegation
     * @param string $sAppUid,
     * @param string $iDelIndex
     * @param string $iAppThreadIndex,
     * @return true
     */

    public function updateAppDelegation($sAppUid, $iDelIndex, $iAppThreadIndex)
    {
        try {
            $appDelegation = new AppDelegation();
            $aData = array();
            $aData['APP_UID'] = $sAppUid;
            $aData['DEL_INDEX'] = $iDelIndex;
            $aData['DEL_THREAD'] = $iAppThreadIndex;
            $appDelegation->update($aData);
            //update searchindex
            if ($this->appSolr != null) {
                $this->appSolr->updateApplicationSearchIndex($sAppUid);
            }
            return true;
        } catch (exception $e) {
            throw ($e);
        }
    }

    /*
     * This function gets all rows in APP_DELEGATION
     *
     * @name GetAllDelegations
     * @param string $sAppUid
     * @return $aDelegations
     */

    public function GetAllDelegations($sAppUid)
    {
        //('SELECT * FROM APP_DELEGATION WHERE APP_UID="'.$currentDelegation['APP_UID'].'" ');
        try {
            $aDelegations = array();
            $c = new Criteria();
            $c->add(AppDelegationPeer::APP_UID, $sAppUid);
            $rs = AppDelegationPeer::doSelectRs($c);
            $rs->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $rs->next();
            $row = $rs->getRow();
            while (is_array($row)) {
                $aDelegations[] = $row;
                $rs->next();
                $row = $rs->getRow();
            }
            return $aDelegations;
        } catch (exception $e) {
            throw ($e);
        }
    }

    /*
     * We're getting all threads in a task
     *
     * @name GetAllDelegations
     * @param string $sAppUid
     * @return $aThreads
     */

    public function GetAllThreads($sAppUid)
    {
        //('SELECT * FROM APP_DELEGATION WHERE APP_UID="'.$currentDelegation['APP_UID'].'" ');
        try {
            $aThreads = array();
            $c = new Criteria();
            $c->add(AppThreadPeer::APP_UID, $sAppUid);
            $rs = AppThreadPeer::doSelectRs($c);
            $rs->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $rs->next();
            $row = $rs->getRow();
            while (is_array($row)) {
                $aThreads[] = $row;
                $rs->next();
                $row = $rs->getRow();
            }
            return $aThreads;
        } catch (exception $e) {
            throw ($e);
        }
    }

    /*
     * With this function we can do update in AppThread
     *
     * @name updateAppThread
     * @param string $sAppUid,
     * @param string $iAppThreadIndex,
     * @param string $iNewDelIndex
     * @return $iNewDelIndex;
     */

    public function updateAppThread($sAppUid, $iAppThreadIndex, $iNewDelIndex)
    {
        try {
            /// updating the DEL_INDEX value in the APP_THREAD
            $con = Propel::getConnection('workflow');
            $c1 = new Criteria('workflow');
            $c1->add(AppThreadPeer::APP_UID, $sAppUid);
            $c1->add(AppThreadPeer::APP_THREAD_INDEX, $iAppThreadIndex);

            // update set
            $c2 = new Criteria('workflow');
            $c2->add(AppThreadPeer::DEL_INDEX, $iNewDelIndex);
            BasePeer::doUpdate($c1, $c2, $con);
            //update searchindex
            if ($this->appSolr != null) {
                $this->appSolr->updateApplicationSearchIndex($sAppUid);
            }
            return $iNewDelIndex;
        } catch (exception $e) {
            throw ($e);
        }
    }

    /*
     * This function changes the status to CLOSED in appThread
     *
     * @name closeAppThread
     * @param string $sAppUid,
     * @param string $iAppThreadIndex,
     * @return true
     */

    public function closeAppThread($sAppUid, $iAppThreadIndex)
    {
        try {
            $appThread = new AppThread();
            $aData = array();
            $aData['APP_UID'] = $sAppUid;
            $aData['APP_THREAD_INDEX'] = $iAppThreadIndex;
            $aData['APP_THREAD_STATUS'] = 'CLOSED';

            $appThread->update($aData);
            return true;
        } catch (exception $e) {
            throw ($e);
        }
    }

    /*
     * This function updates a row in APP_DELEGATION
     *
     * @name closeAllDelegations
     * @param string $sAppUid
     * @return void
     */

    public function closeAllThreads($sAppUid)
    {
        try {
            $c = new Criteria();
            $c->add(AppThreadPeer::APP_UID, $sAppUid);
            $c->add(AppThreadPeer::APP_THREAD_STATUS, 'OPEN');
            $rowObj = AppThreadPeer::doSelect($c);
            foreach ($rowObj as $appThread) {
                $appThread->setAppThreadStatus('CLOSED');
                if ($appThread->Validate()) {
                    $appThread->Save();
                } else {
                    $msg = '';
                    foreach ($this->getValidationFailures() as $objValidationFailure) {
                        $msg .= $objValidationFailure->getMessage() . "<br/>";
                    }
                    throw (new PropelException('The row cannot be created!', new PropelException($msg)));
                }
            }
            //update searchindex
            if ($this->appSolr != null) {
                $this->appSolr->updateApplicationSearchIndex($sAppUid);
            }
        } catch (exception $e) {
            throw ($e);
        }
    }

    /*
     * This function creates a new row in appThread
     *
     * @name newAppThread
     * @param string $sAppUid,
     * @param string $iNewDelIndex
     * @param string $iAppParent
     * @return $iAppThreadIndex $iNewDelIndex, $iAppThreadIndex );
     */

    public function newAppThread($sAppUid, $iNewDelIndex, $iAppParent)
    {
        try {
            $appThread = new AppThread();
            $result = $appThread->createAppThread($sAppUid, $iNewDelIndex, $iAppParent);
            //update searchindex
            if ($this->appSolr != null) {
                $this->appSolr->updateApplicationSearchIndex($sAppUid);
            }
            return $result;
        } catch (exception $e) {
            throw ($e);
        }
    }

    /*
     * With this function we can change status to CLOSED in APP_DELEGATION
     *
     * @name closeAllDelegations
     * @param string $sAppUid
     * @return
     */

    public function closeAllDelegations($sAppUid)
    {
        try {
            $c = new Criteria();
            $c->add(AppDelegationPeer::APP_UID, $sAppUid);
            $c->add(AppDelegationPeer::DEL_THREAD_STATUS, 'OPEN');
            $rowObj = AppDelegationPeer::doSelect($c);
            foreach ($rowObj as $appDel) {
                $appDel->setDelThreadStatus('CLOSED');
                if ($appDel->Validate()) {
                    $appDel->Save();
                } else {
                    $msg = '';
                    foreach ($this->getValidationFailures() as $objValidationFailure) {
                        $msg .= $objValidationFailure->getMessage() . "<br/>";
                    }
                    throw (new PropelException('The row cannot be created!', new PropelException($msg)));
                }
            }
            //update searchindex
            if ($this->appSolr != null) {
                $this->appSolr->updateApplicationSearchIndex($sAppUid);
            }
        } catch (exception $e) {
            throw ($e);
        }
    }

    /*
     * With this we can change the status to CLOSED in APP_DELEGATION
     *
     * @name CloseCurrentDelegation
     * @param string $sAppUid
     * @param string $iDelIndex
     * @return Fields
     */

    public function CloseCurrentDelegation($sAppUid, $iDelIndex)
    {
        try {
            $c = new Criteria();
            $c->add(AppDelegationPeer::APP_UID, $sAppUid);
            $c->add(AppDelegationPeer::DEL_INDEX, $iDelIndex);
            $rowObj = AppDelegationPeer::doSelect($c);
            G::LoadClass('dates');
            $oDates = new dates();
            foreach ($rowObj as $appDel) {
                $appDel->setDelThreadStatus('CLOSED');
                $appDel->setDelFinishDate('now');
                if ($appDel->Validate()) {
                    $appDel->Save();
                } else {
                    $msg = '';
                    foreach ($this->getValidationFailures() as $objValidationFailure) {
                        $msg .= $objValidationFailure->getMessage() . "<br/>";
                    }
                    throw (new PropelException('The row cannot be created!', new PropelException($msg)));
                }
            }
        } catch (exception $e) {
            throw ($e);
        }
    }

    /*
     * This function creates a new row in APP_DELEGATION whether it has status closed
     *
     * @name ReactivateCurrentDelegation
     * @Description:  This function reativate the case previously cancelled from to do
     * @param string $sAppUid
     * @param string $iDelIndex
     * @return Fields
     */

    public function ReactivateCurrentDelegation($sAppUid, $iDelegation)
    {
        try {
            $c = new Criteria();
            $c->add(AppDelegationPeer::APP_UID, $sAppUid);
            $c->add(AppDelegationPeer::DEL_INDEX, $iDelegation);

            $rowObj = AppDelegationPeer::doSelect($c);
            foreach ($rowObj as $appDel) {
                $appDel->setDelThreadStatus('OPEN');
                $appDel->setDelFinishDate(null);
                if ($appDel->Validate()) {
                    $appDel->Save();
                } else {
                    $msg = '';
                    foreach ($this->getValidationFailures() as $objValidationFailure) {
                        $msg .= $objValidationFailure->getMessage() . "<br/>";
                    }
                    throw (new PropelException('The row cannot be created!', new PropelException($msg)));
                }
            }
        } catch (exception $e) {
            throw ($e);
        }
    }

    /*
     * This function start a case using the task for the user $sUsrUid
     * With this function we can Start a case
     *
     * @name startCase
     * @param string $sTasUid
     * @param string $sUsrUid
     * @return Fields
     */

    public function startCase($sTasUid, $sUsrUid, $isSubprocess = false)
    {
        if ($sTasUid != '') {
            try {
                $this->Task = new Task;
                $Fields = $this->Task->Load($sTasUid);

                //To allow Self Service as the first task
                if (($Fields['TAS_ASSIGN_TYPE'] != 'SELF_SERVICE') && ($sUsrUid == '')) {
                    throw (new Exception('You tried to start a new case without send the USER UID!'));
                }

                //Process
                $sProUid = $this->Task->getProUid();
                $this->Process = new Process;
                $proFields = $this->Process->Load($sProUid);

                //application
                $Application = new Application;
                $sAppUid = $Application->create($sProUid, $sUsrUid);

                //appDelegation
                $AppDelegation = new AppDelegation;
                $iAppThreadIndex = 1; // Start Thread
                $iAppDelPrio = 3; // Priority
                $iDelIndex = $AppDelegation->createAppDelegation(
                        $sProUid, $sAppUid, $sTasUid, $sUsrUid, $iAppThreadIndex, $iAppDelPrio, $isSubprocess
                );

                //appThread
                $AppThread = new AppThread;
                $iAppThreadIndex = $AppThread->createAppThread($sAppUid, $iDelIndex, 0);

                //DONE: Al ya existir un delegation, se puede "calcular" el caseTitle.
                $Fields = $Application->toArray(BasePeer::TYPE_FIELDNAME);
                $aApplicationFields = $Fields['APP_DATA'];
                $newValues = $this->newRefreshCaseTitleAndDescription($sAppUid, $Fields, $aApplicationFields);
                if (!isset($newValues['APP_TITLE'])) {
                    $newValues['APP_TITLE'] = '';
                }

                $caseNumber = $Fields['APP_NUMBER'];
                $Application->update($Fields);

                //Update the task last assigned (for web entry and web services)
                G::LoadClass('derivation');
                $oDerivation = new Derivation();
                $oDerivation->setTasLastAssigned($sTasUid, $sUsrUid);
                //update searchindex
                if ($this->appSolr != null) {
                    $this->appSolr->updateApplicationSearchIndex($sAppUid);
                }
            } catch (exception $e) {
                throw ($e);
            }
        } else {
            throw (new Exception('You tried to start a new case without send the USER UID or TASK UID!'));
        }

        //call plugin
        if (class_exists('folderData')) {
            $folderData = new folderData(
                            $sProUid,
                            $proFields['PRO_TITLE'],
                            $sAppUid,
                            $newValues['APP_TITLE'],
                            $sUsrUid
            );
            $oPluginRegistry = &PMPluginRegistry::getSingleton();
            $oPluginRegistry->executeTriggers(PM_CREATE_CASE, $folderData);
        }
        //end plugin
        return array(
            'APPLICATION' => $sAppUid,
            'INDEX' => $iDelIndex,
            'PROCESS' => $sProUid,
            'CASE_NUMBER' => $caseNumber
        );
    }

    /*
     * Get the next step
     *
     * @name getNextStep
     * @param string $sProUid
     * @param string $sAppUid
     * @param integer $iDelIndex
     * @param integer $iPosition
     * @return array
     */

    public function getNextStep($sProUid = '', $sAppUid = '', $iDelIndex = 0, $iPosition = 0)
    {
        G::LoadClass('pmScript');
        $oPMScript = new PMScript();
        $oApplication = new Application();
        //$aFields    = $oApplication->load($sAppUid);
        $oApplication = ApplicationPeer::retrieveByPk($sAppUid);
        $aFields = $oApplication->toArray(BasePeer::TYPE_FIELDNAME);
        if (!is_array($aFields['APP_DATA'])) {
            $aFields['APP_DATA'] = G::array_merges(G::getSystemConstants(), unserialize($aFields['APP_DATA']));
        }
        $oPMScript->setFields($aFields['APP_DATA']);

        try {
            //get the current Delegation, and TaskUID
            $c = new Criteria('workflow');
            $c->add(AppDelegationPeer::PRO_UID, $sProUid);
            $c->add(AppDelegationPeer::APP_UID, $sAppUid);
            $c->add(AppDelegationPeer::DEL_INDEX, $iDelIndex);
            $aRow = AppDelegationPeer::doSelect($c);

            if (!isset($aRow[0])) {
                return false;
            }

            $sTaskUid = $aRow[0]->getTasUid();

            //get max step for this task
            $c = new Criteria();
            $c->clearSelectColumns();
            $c->addSelectColumn('MAX(' . StepPeer::STEP_POSITION . ')');
            $c->add(StepPeer::PRO_UID, $sProUid);
            $c->add(StepPeer::TAS_UID, $sTaskUid);
            $rs = StepPeer::doSelectRS($c);
            $rs->next();
            $row = $rs->getRow();
            $iLastStep = intval($row[0]);

            $iPosition += 1;
            $aNextStep = null;
            if ($iPosition <= $iLastStep) {
                //to do:      $oApplication = new Application($this->_dbc);
                //to do:      $oApplication->load($sApplicationUID);
                //to do:      G::LoadClass('pmScript');
                //to do:      $oPMScript = new PMScript();
                //to do:      $oPMScript->setFields($oApplication->Fields['APP_DATA']);
                while ($iPosition <= $iLastStep) {
                    $bAccessStep = false;
                    //step
                    $oStep = new Step;
                    $oStep = $oStep->loadByProcessTaskPosition($sProUid, $sTaskUid, $iPosition);
                    if ($oStep) {
                        if (trim($oStep->getStepCondition()) !== '') {
                            $oPMScript->setScript($oStep->getStepCondition());
                            $bAccessStep = $oPMScript->evaluate();
                        } else {
                            $bAccessStep = true;
                        }
                        if ($bAccessStep) {
                            switch ($oStep->getStepTypeObj()) {
                                case 'DYNAFORM':
                                    $sAction = 'EDIT';
                                    break;
                                case 'OUTPUT_DOCUMENT':
                                    $sAction = 'GENERATE';
                                    break;
                                case 'INPUT_DOCUMENT':
                                    $sAction = 'ATTACH';
                                    break;
                                case 'EXTERNAL':
                                    $sAction = 'EDIT';
                                    break;
                                case 'MESSAGE':
                                    $sAction = '';
                                    break;
                            }
                            $aNextStep = array(
                                'TYPE' => $oStep->getStepTypeObj(),
                                'UID' => $oStep->getStepUidObj(),
                                'POSITION' => $oStep->getStepPosition(),
                                'PAGE' => 'cases_Step?TYPE=' . $oStep->getStepTypeObj() . '&UID=' .
                                $oStep->getStepUidObj() . '&POSITION=' . $oStep->getStepPosition() .
                                '&ACTION=' . $sAction
                            );
                            $iPosition = $iLastStep;
                        }
                    }
                    $iPosition += 1;
                }
            }
            if (!$aNextStep) {
                $aNextStep = array(
                    'TYPE' => 'DERIVATION',
                    'UID' => -1,
                    'POSITION' => ($iLastStep + 1),
                    'PAGE' => 'cases_Step?TYPE=ASSIGN_TASK&UID=-1&POSITION=10000&ACTION=ASSIGN'
                );
            }
            return $aNextStep;
        } catch (exception $e) {
            throw ($e);
        }
    }

    /*
     * Get the previous step
     *
     * @name getPreviousStep
     * @param string $sProUid
     * @param string $sAppUid
     * @param integer $iDelIndex
     * @param integer $iPosition
     * @return array
     */

    public function getPreviousStep($sProUid = '', $sAppUid = '', $iDelIndex = 0, $iPosition = 0)
    {
        //Note: Depreciated, delete in the future
        G::LoadClass('pmScript');
        $oPMScript = new PMScript();
        $oApplication = new Application();
        //$aFields = $oApplication->load($sAppUid);
        $oApplication = ApplicationPeer::retrieveByPk($sAppUid);
        $aFields = $oApplication->toArray(BasePeer::TYPE_FIELDNAME);
        if (!is_array($aFields['APP_DATA'])) {
            $aFields['APP_DATA'] = G::array_merges(G::getSystemConstants(), unserialize($aFields['APP_DATA']));
        }
        $oPMScript->setFields($aFields['APP_DATA']);

        try {
            //get the current Delegation, and TaskUID
            $c = new Criteria();
            $c->add(AppDelegationPeer::PRO_UID, $sProUid);
            $c->add(AppDelegationPeer::APP_UID, $sAppUid);
            $c->add(AppDelegationPeer::DEL_INDEX, $iDelIndex);
            $aRow = AppDelegationPeer::doSelect($c);

            $sTaskUid = $aRow[0]->getTasUid();
            $iFirstStep = 1;

            if ($iPosition == 10000) {
                //get max step for this task
                $c = new Criteria();
                $c->clearSelectColumns();
                $c->addSelectColumn('MAX(' . StepPeer::STEP_POSITION . ')');
                $c->add(StepPeer::PRO_UID, $sProUid);
                $c->add(StepPeer::TAS_UID, $sTaskUid);
                $rs = StepPeer::doSelectRS($c);
                $rs->next();
                $row = $rs->getRow();
                $iPosition = intval($row[0]);
            } else {
                $iPosition -= 1;
            }

            $aPreviousStep = null;
            if ($iPosition >= 1) {
                while ($iPosition >= $iFirstStep) {
                    $bAccessStep = false;
                    //step
                    $oStep = new Step;
                    $oStep = $oStep->loadByProcessTaskPosition($sProUid, $sTaskUid, $iPosition);
                    if ($oStep) {
                        if (trim($oStep->getStepCondition()) !== '') {
                            $oPMScript->setScript($oStep->getStepCondition());
                            $bAccessStep = $oPMScript->evaluate();
                        } else {
                            $bAccessStep = true;
                        }
                        if ($bAccessStep) {
                            switch ($oStep->getStepTypeObj()) {
                                case 'DYNAFORM':
                                    $sAction = 'EDIT';
                                    break;
                                case 'OUTPUT_DOCUMENT':
                                    $sAction = 'GENERATE';
                                    break;
                                case 'INPUT_DOCUMENT':
                                    $sAction = 'ATTACH';
                                    break;
                                case 'EXTERNAL':
                                    $sAction = 'EDIT';
                                    break;
                                case 'MESSAGE':
                                    $sAction = '';
                                    break;
                            }
                            $aPreviousStep = array('TYPE' => $oStep->getStepTypeObj(),
                                'UID' => $oStep->getStepUidObj(),
                                'POSITION' => $oStep->getStepPosition(),
                                'PAGE' => 'cases_Step?TYPE=' . $oStep->getStepTypeObj() . '&UID=' .
                                $oStep->getStepUidObj() . '&POSITION=' .
                                $oStep->getStepPosition() . '&ACTION=' . $sAction
                            );
                            $iPosition = $iFirstStep;
                        }
                    }
                    $iPosition -= 1;
                }
            }
            if (!$aPreviousStep) {
                $aPreviousStep = false;
            }
            return $aPreviousStep;
        } catch (exception $e) {
            throw ($e);
        }
    }

    /*
     * Get the next supervisor step
     *
     * @name getNextSupervisorStep
     * @param string $sProcessUID
     * @param string $iPosition
     * @param integer $sType
     * @return $aNextStep
     */

    public function getNextSupervisorStep($sProcessUID, $iPosition, $sType = 'DYNAFORM')
    {
        $iPosition += 1;
        $oCriteria = new Criteria();
        $oCriteria->add(StepSupervisorPeer::PRO_UID, $sProcessUID);
        $oCriteria->add(StepSupervisorPeer::STEP_TYPE_OBJ, $sType);
        $oCriteria->add(StepSupervisorPeer::STEP_POSITION, $iPosition);
        $oDataset = StepSupervisorPeer::doSelectRS($oCriteria);
        $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $oDataset->next();
        $aRow = $oDataset->getRow();
        if (!$aRow) {
            $oCriteria = new Criteria();
            $oCriteria->add(StepSupervisorPeer::PRO_UID, $sProcessUID);
            $oCriteria->add(StepSupervisorPeer::STEP_TYPE_OBJ, $sType);
            $oCriteria->add(StepSupervisorPeer::STEP_POSITION, 1);
            $oDataset = StepSupervisorPeer::doSelectRS($oCriteria);
            $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $oDataset->next();
            $aRow = $oDataset->getRow();
        }
        $aNextStep = array('UID' => $aRow['STEP_UID_OBJ'], 'POSITION' => $aRow['STEP_POSITION']);
        return $aNextStep;
    }

    /*
     * Get the previous supervisor step
     *
     * @name getPreviousSupervisorStep
     * @param string $sProcessUID
     * @param string $iPosition
     * @param integer $sType
     * @return $aNextStep
     */

    public function getPreviousSupervisorStep($sProcessUID, $iPosition, $sType = 'DYNAFORM')
    {
        $iPosition -= 1;
        if ($iPosition > 0) {
            $oCriteria = new Criteria();
            $oCriteria->add(StepSupervisorPeer::PRO_UID, $sProcessUID);
            $oCriteria->add(StepSupervisorPeer::STEP_TYPE_OBJ, $sType);
            $oCriteria->add(StepSupervisorPeer::STEP_POSITION, $iPosition);
            $oDataset = StepSupervisorPeer::doSelectRS($oCriteria);
            $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $oDataset->next();
            $aRow = $oDataset->getRow();
            if (!$aRow) {
                $oCriteria = new Criteria();
                $oCriteria->add(StepSupervisorPeer::PRO_UID, $sProcessUID);
                $oCriteria->add(StepSupervisorPeer::STEP_TYPE_OBJ, $sType);
                $oCriteria->add(StepSupervisorPeer::STEP_POSITION, 1);
                $oDataset = StepSupervisorPeer::doSelectRS($oCriteria);
                $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
                $oDataset->next();
                $aRow = $oDataset->getRow();
            }
            $aNextStep = array('UID' => $aRow['STEP_UID_OBJ'], 'POSITION' => $aRow['STEP_POSITION']);
            return $aNextStep;
        } else {
            return false;
        }
    }

    /*
     * Get the transfer History
     *
     * @name getTransferHistoryCriteria
     * @param string $sAppUid
     * @return array
     */

    public function getTransferHistoryCriteria($sAppUid)
    {
        $c = new Criteria('workflow');
        $c->addAsColumn('TAS_TITLE', 'TAS_TITLE.CON_VALUE');
        $c->addSelectColumn(UsersPeer::USR_FIRSTNAME);
        $c->addSelectColumn(UsersPeer::USR_LASTNAME);
        $c->addSelectColumn(AppDelegationPeer::DEL_DELEGATE_DATE);
        $c->addSelectColumn(AppDelegationPeer::PRO_UID);
        $c->addSelectColumn(AppDelegationPeer::TAS_UID);
        $c->addSelectColumn(AppDelegationPeer::APP_UID);
        $c->addSelectColumn(AppDelegationPeer::DEL_INDEX);
        ///-- $c->addAsColumn('USR_NAME', "CONCAT(USR_LASTNAME, ' ', USR_FIRSTNAME)");
        $sDataBase = 'database_' . strtolower(DB_ADAPTER);
        if (G::LoadSystemExist($sDataBase)) {
            G::LoadSystem($sDataBase);
            $oDataBase = new database();
            $c->addAsColumn('USR_NAME', $oDataBase->concatString("USR_LASTNAME", "' '", "USR_FIRSTNAME"));
            $c->addAsColumn(
                    'DEL_FINISH_DATE', $oDataBase->getCaseWhen("DEL_FINISH_DATE IS NULL", "'-'", AppDelegationPeer::DEL_FINISH_DATE)
            );
            $c->addAsColumn(
                    'APP_TYPE', $oDataBase->getCaseWhen("DEL_FINISH_DATE IS NULL", "'IN_PROGRESS'", AppDelayPeer::APP_TYPE)
            );
        }
        $c->addSelectColumn(AppDelegationPeer::DEL_INIT_DATE);
        $c->addSelectColumn(AppDelayPeer::APP_ENABLE_ACTION_DATE);
        $c->addSelectColumn(AppDelayPeer::APP_DISABLE_ACTION_DATE);
        //APP_DELEGATION LEFT JOIN USERS
        $c->addJoin(AppDelegationPeer::USR_UID, UsersPeer::USR_UID, Criteria::LEFT_JOIN);

        //APP_DELAY FOR MORE DESCRIPTION
        //$c->addJoin(AppDelegationPeer::DEL_INDEX, AppDelayPeer::APP_DEL_INDEX, Criteria::LEFT_JOIN);
        //$c->addJoin(AppDelegationPeer::APP_UID, AppDelayPeer::APP_UID, Criteria::LEFT_JOIN);
        $del = DBAdapter::getStringDelimiter();
        $app = array();
        $app[] = array(AppDelegationPeer::DEL_INDEX, AppDelayPeer::APP_DEL_INDEX);
        $app[] = array(AppDelegationPeer::APP_UID, AppDelayPeer::APP_UID);
        $c->addJoinMC($app, Criteria::LEFT_JOIN);

        //LEFT JOIN CONTENT TAS_TITLE
        $c->addAlias("TAS_TITLE", 'CONTENT');
        $del = DBAdapter::getStringDelimiter();
        $appTitleConds = array();
        $appTitleConds[] = array(AppDelegationPeer::TAS_UID, 'TAS_TITLE.CON_ID');
        $appTitleConds[] = array('TAS_TITLE.CON_CATEGORY', $del . 'TAS_TITLE' . $del);
        $appTitleConds[] = array('TAS_TITLE.CON_LANG', $del . SYS_LANG . $del);
        $c->addJoinMC($appTitleConds, Criteria::LEFT_JOIN);

        //WHERE
        $c->add(AppDelegationPeer::APP_UID, $sAppUid);

        //ORDER BY
        $c->clearOrderByColumns();
        $c->addAscendingOrderByColumn(AppDelegationPeer::DEL_DELEGATE_DATE);

        return $c;
    }

    /*
     * Get the Criteria for To Do Cases List
     * @param string $sUIDUserLogged
     * @return array ( 'where' => Criteria, 'group' => Criteria )
     * @return array
     */

    public function prepareCriteriaForToDo($sUIDUserLogged)
    {
        // NEW QUERY
        $c = new Criteria('workflow');
        //$gf->clearSelectColumns();DEL_INIT_DATE
        $c->addSelectColumn(AppCacheViewPeer::APP_UID);
        $c->addSelectColumn(AppCacheViewPeer::DEL_INDEX);
        $c->addSelectColumn(AppCacheViewPeer::APP_NUMBER);
        $c->addSelectColumn(AppCacheViewPeer::APP_STATUS);
        $c->addSelectColumn(AppCacheViewPeer::USR_UID);
        $c->addSelectColumn(AppCacheViewPeer::PREVIOUS_USR_UID);
        $c->addSelectColumn(AppCacheViewPeer::TAS_UID);
        $c->addSelectColumn(AppCacheViewPeer::PRO_UID);
        $c->addSelectColumn(AppCacheViewPeer::DEL_DELEGATE_DATE);
        $c->addSelectColumn(AppCacheViewPeer::DEL_INIT_DATE);
        //$c->addSelectColumn(AppCacheViewPeer::DEL_TASK_DUE_DATE  );
        $c->addAsColumn(
                'DEL_TASK_DUE_DATE', " IF (" . AppCacheViewPeer::DEL_TASK_DUE_DATE . " <= NOW(), CONCAT('<span style=\'color:red\';>', " .
                AppCacheViewPeer::DEL_TASK_DUE_DATE . ", '</span>'), " . AppCacheViewPeer::DEL_TASK_DUE_DATE . ") "
        );
        $c->addSelectColumn(AppCacheViewPeer::DEL_FINISH_DATE);
        $c->addSelectColumn(AppCacheViewPeer::DEL_THREAD_STATUS);
        $c->addSelectColumn(AppCacheViewPeer::APP_THREAD_STATUS);
        $c->addSelectColumn(AppCacheViewPeer::APP_TITLE);
        $c->addSelectColumn(AppCacheViewPeer::APP_PRO_TITLE);
        $c->addSelectColumn(AppCacheViewPeer::APP_TAS_TITLE);
        $c->addSelectColumn(AppCacheViewPeer::APP_CURRENT_USER);
        $c->addSelectColumn(AppCacheViewPeer::APP_DEL_PREVIOUS_USER);
        $c->addSelectColumn(AppCacheViewPeer::DEL_PRIORITY);
        $c->addSelectColumn(AppCacheViewPeer::DEL_DURATION);
        $c->addSelectColumn(AppCacheViewPeer::DEL_QUEUE_DURATION);
        $c->addSelectColumn(AppCacheViewPeer::DEL_DELAY_DURATION);
        $c->addSelectColumn(AppCacheViewPeer::DEL_STARTED);
        $c->addSelectColumn(AppCacheViewPeer::DEL_FINISHED);
        $c->addSelectColumn(AppCacheViewPeer::DEL_DELAYED);
        $c->addSelectColumn(AppCacheViewPeer::APP_CREATE_DATE);
        $c->addSelectColumn(AppCacheViewPeer::APP_FINISH_DATE);
        $c->addSelectColumn(AppCacheViewPeer::APP_UPDATE_DATE);

        $c->add(AppCacheViewPeer::USR_UID, $sUIDUserLogged);
        $c->add(AppCacheViewPeer::DEL_FINISH_DATE, null, Criteria::ISNULL);
        $c->add(AppCacheViewPeer::APP_STATUS, 'TO_DO');
        $c->add(AppCacheViewPeer::APP_THREAD_STATUS, 'OPEN');

        //call cleanup session vars
        return $c;
        //return array ( 'where' => $cf, 'whereFilter' => $cf, 'group' => $g , 'groupFilter' => $gf );
    }

    //DEPRECATED
    /**
     * Get the condition for Cases List
     *
     * @name getConditionCasesList
     * @param string $sTypeList
     * @param string $sUIDUserLogged
     * @param string $ClearSession
     * @param string $aAdditionalFilter
     * @return array
     */
    public function getConditionCasesList($sTypeList = 'all', $sUIDUserLogged = '', $ClearSession = true, $aAdditionalFilter = null)
    {
        $c = new Criteria('workflow');
        $c->clearSelectColumns();
        $c->addSelectColumn(ApplicationPeer::APP_UID);
        $c->addSelectColumn(ApplicationPeer::APP_NUMBER);
        $c->addSelectColumn(ApplicationPeer::APP_UPDATE_DATE);
        $c->addSelectColumn(ApplicationPeer::PRO_UID);
        $c->addSelectColumn(ApplicationPeer::APP_INIT_USER);
        $c->addSelectColumn(AppDelegationPeer::DEL_PRIORITY);
        //$c->addSelectColumn(AppDelegationPeer::DEL_TASK_DUE_DATE);
        $c->addAsColumn(
                'DEL_TASK_DUE_DATE', " IF (" . AppDelegationPeer::DEL_TASK_DUE_DATE . " <= NOW(), CONCAT('<span style=\'color:red\';>', " .
                AppDelegationPeer::DEL_TASK_DUE_DATE . ", '</span>'), " . AppDelegationPeer::DEL_TASK_DUE_DATE . ") "
        );

        global $RBAC;
        //seems the PM_SUPERVISOR can delete a completed case
        if ($sTypeList == "completed" && $RBAC->userCanAccess('PM_SUPERVISOR') == 1) {
            $c->addAsColumn("DEL_LINK", "CONCAT('" . G::LoadTranslation('ID_DELETE') . "')");
        }

        $c->addSelectColumn(AppDelegationPeer::DEL_INDEX);
        $c->addSelectColumn(AppDelegationPeer::TAS_UID);
        $c->addSelectColumn(AppDelegationPeer::DEL_INIT_DATE);
        $c->addSelectColumn(AppDelegationPeer::DEL_FINISH_DATE);
        $c->addSelectColumn(UsersPeer::USR_UID);
        $c->addAsColumn('APP_CURRENT_USER', "CONCAT(USERS.USR_LASTNAME, ' ', USERS.USR_FIRSTNAME)");
        $c->addSelectColumn(ApplicationPeer::APP_STATUS);
        $c->addAsColumn('APP_TITLE', 'APP_TITLE.CON_VALUE');
        $c->addAsColumn('APP_PRO_TITLE', 'PRO_TITLE.CON_VALUE');
        $c->addAsColumn('APP_TAS_TITLE', 'TAS_TITLE.CON_VALUE');
        //$c->addAsColumn('APP_DEL_PREVIOUS_USER', 'APP_LAST_USER.USR_USERNAME');
        $c->addAsColumn(
                'APP_DEL_PREVIOUS_USER', "CONCAT(APP_LAST_USER.USR_LASTNAME,
            ' ',
            APP_LAST_USER.USR_FIRSTNAME)"
        );

        $c->addAlias("APP_TITLE", 'CONTENT');
        $c->addAlias("PRO_TITLE", 'CONTENT');
        $c->addAlias("TAS_TITLE", 'CONTENT');
        $c->addAlias("APP_PREV_DEL", 'APP_DELEGATION');
        $c->addAlias("APP_LAST_USER", 'USERS');

        $c->addJoin(ApplicationPeer::APP_UID, AppDelegationPeer::APP_UID, Criteria::LEFT_JOIN);
        $c->addJoin(AppDelegationPeer::TAS_UID, TaskPeer::TAS_UID, Criteria::LEFT_JOIN);
        $appThreadConds[] = array(ApplicationPeer::APP_UID, AppThreadPeer::APP_UID);
        $appThreadConds[] = array(AppDelegationPeer::DEL_INDEX, AppThreadPeer::DEL_INDEX);
        $c->addJoinMC($appThreadConds, Criteria::LEFT_JOIN);
        $c->addJoin(AppDelegationPeer::USR_UID, UsersPeer::USR_UID, Criteria::LEFT_JOIN);

        $del = DBAdapter::getStringDelimiter();
        $appTitleConds = array();
        $appTitleConds[] = array(ApplicationPeer::APP_UID, 'APP_TITLE.CON_ID');
        $appTitleConds[] = array('APP_TITLE.CON_CATEGORY', $del . 'APP_TITLE' . $del);
        $appTitleConds[] = array('APP_TITLE.CON_LANG', $del . SYS_LANG . $del);
        $c->addJoinMC($appTitleConds, Criteria::LEFT_JOIN);

        $proTitleConds = array();
        $proTitleConds[] = array(ApplicationPeer::PRO_UID, 'PRO_TITLE.CON_ID');
        $proTitleConds[] = array('PRO_TITLE.CON_CATEGORY', $del . 'PRO_TITLE' . $del);
        $proTitleConds[] = array('PRO_TITLE.CON_LANG', $del . SYS_LANG . $del);
        $c->addJoinMC($proTitleConds, Criteria::LEFT_JOIN);

        $tasTitleConds = array();
        $tasTitleConds[] = array(AppDelegationPeer::TAS_UID, 'TAS_TITLE.CON_ID');
        $tasTitleConds[] = array('TAS_TITLE.CON_CATEGORY', $del . 'TAS_TITLE' . $del);
        $tasTitleConds[] = array('TAS_TITLE.CON_LANG', $del . SYS_LANG . $del);
        $c->addJoinMC($tasTitleConds, Criteria::LEFT_JOIN);

        $prevConds = array();
        $prevConds[] = array(ApplicationPeer::APP_UID, 'APP_PREV_DEL.APP_UID');
        $prevConds[] = array('APP_PREV_DEL.DEL_INDEX', AppDelegationPeer::DEL_PREVIOUS);
        $c->addJoinMC($prevConds, Criteria::LEFT_JOIN);

        $usrConds = array();
        $usrConds[] = array('APP_PREV_DEL.USR_UID', 'APP_LAST_USER.USR_UID');
        $c->addJoinMC($usrConds, Criteria::LEFT_JOIN);

        $c->add(TaskPeer::TAS_TYPE, 'SUBPROCESS', Criteria::NOT_EQUAL);

        //gral, to_revise, to_reassign dont have userid in the query
        if ($sTypeList != 'gral' && $sTypeList != 'to_revise' && $sTypeList != 'to_reassign' &&
                $sTypeList != 'my_started' && $sTypeList != 'sent') {
            $c->add(UsersPeer::USR_UID, $sUIDUserLogged);
        }

        /**
         * Additional filters
         * By Erik <erik@colosa.com>
         */
        if (isset($aAdditionalFilter) && is_array($aAdditionalFilter)) {
            foreach ($aAdditionalFilter as $sFilter => $sValue) {
                switch ($sFilter) {
                    case 'PRO_UID':
                        if ($sValue != "0") {
                            $c->add(ApplicationPeer::PRO_UID, $sValue, Criteria::EQUAL);
                        }
                        break;
                    case 'READ':
                        $c->add(AppDelegationPeer::DEL_INIT_DATE, null, Criteria::ISNOTNULL);
                        break;
                    case 'UNREAD':
                        $c->add(AppDelegationPeer::DEL_INIT_DATE, null, Criteria::ISNULL);
                        break;
                }
            }
        }

        $filesList = array(
            //7 standard list
            'to_do' => 'cases/cases_ListTodo',
            'draft' => 'cases/cases_ListDraft',
            'paused' => 'cases/cases_ListOnHold',
            'cancelled' => 'cases/cases_ListCancelled',
            'completed' => 'cases/cases_ListCompleted',
            'sent' => 'cases/cases_ListSent',
            'selfservice' => 'cases/cases_ListSelfService',
            //5 admin list
            'all' => 'cases/cases_ListAll',
            'to_revise' => 'cases/cases_ListToRevise',
            'to_reassign' => 'cases/cases_ListAll_Reassign',
            'my_started' => 'cases/cases_ListStarted',
            'Alldelete' => 'cases/cases_ListAllDelete'
        );
        switch ($sTypeList) {
            case 'all':
                $c->add(
                        $c->getNewCriterion(
                                        AppThreadPeer::APP_THREAD_STATUS, 'OPEN')->
                                addOr($c->getNewCriterion(ApplicationPeer::APP_STATUS, 'COMPLETED')->
                                        addAnd($c->getNewCriterion(AppDelegationPeer::DEL_PREVIOUS, 0)
                                        )
                                )
                );
                $c->addDescendingOrderByColumn(ApplicationPeer::APP_NUMBER);
                break;
            case 'my_started':
                $oCriteria = new Criteria('workflow');
                $oCriteria->addSelectColumn(AppDelayPeer::APP_UID);
                $oCriteria->add(
                        $oCriteria->getNewCriterion(
                                AppDelayPeer::APP_DISABLE_ACTION_USER, null, Criteria::ISNULL
                        )->addOr(
                                $oCriteria->getNewCriterion(AppDelayPeer::APP_DISABLE_ACTION_USER, 0)
                        )
                );
                //$oCriteria->add(AppDelayPeer::APP_DISABLE_ACTION_USER, null, Criteria::ISNULL);
                $oDataset = AppDelayPeer::doSelectRS($oCriteria);
                $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
                $oDataset->next();
                $aProcesses = array();
                while ($aRow = $oDataset->getRow()) {
                    $aProcesses[] = $aRow['APP_UID'];
                    $oDataset->next();
                }

                $c->add($c->getNewCriterion(ApplicationPeer::APP_INIT_USER, $sUIDUserLogged));
                $c->add(
                        $c->getNewCriterion(
                                AppThreadPeer::APP_THREAD_STATUS, 'OPEN'
                        )->addOr(
                                $c->getNewCriterion(
                                        ApplicationPeer::APP_STATUS, 'COMPLETED'
                                )->addAnd(
                                        $c->getNewCriterion(AppDelegationPeer::DEL_PREVIOUS, 0)
                                )
                        )
                );
                $c->add($c->getNewCriterion(ApplicationPeer::APP_UID, $aProcesses, Criteria::NOT_IN));
                $c->addDescendingOrderByColumn(ApplicationPeer::APP_NUMBER);
                break;
            case 'to_do':
                $c->add(ApplicationPeer::APP_STATUS, 'TO_DO');
                $c->add(AppDelegationPeer::DEL_FINISH_DATE, null, Criteria::ISNULL);
                $c->add(AppThreadPeer::APP_THREAD_STATUS, 'OPEN');
                $c->add(AppDelegationPeer::DEL_THREAD_STATUS, 'OPEN');
                $c->addDescendingOrderByColumn(ApplicationPeer::APP_NUMBER);
                break;
            case 'draft':
                $c->add(ApplicationPeer::APP_STATUS, 'DRAFT');
                $c->add(AppDelegationPeer::DEL_FINISH_DATE, null, Criteria::ISNULL);
                $c->add(AppDelegationPeer::DEL_THREAD_STATUS, 'OPEN');
                $c->addDescendingOrderByColumn(ApplicationPeer::APP_NUMBER);
                break;
            case 'paused':
                $appDelayConds[] = array(ApplicationPeer::APP_UID, AppDelayPeer::APP_UID);
                $appDelayConds[] = array(AppDelegationPeer::DEL_INDEX, AppDelayPeer::APP_DEL_INDEX);
                $c->addJoinMC($appDelayConds, Criteria::LEFT_JOIN);
                $c->add(AppDelayPeer::APP_DELAY_UID, null, Criteria::ISNOTNULL);
                $c->add(AppDelayPeer::APP_TYPE, array("REASSIGN", "ADHOC", "CANCEL"), Criteria::NOT_IN);
                $c->add(
                        $c->getNewCriterion(AppDelayPeer::APP_DISABLE_ACTION_USER, null, Criteria::ISNULL)->
                                addOr($c->getNewCriterion(AppDelayPeer::APP_DISABLE_ACTION_USER, 0))
                );
                $c->addDescendingOrderByColumn(ApplicationPeer::APP_NUMBER);
                break;
            case 'cancelled':
                $c->add(
                        $c->getNewCriterion(AppThreadPeer::APP_THREAD_STATUS, 'CLOSED')->
                                addAnd($c->getNewCriterion(ApplicationPeer::APP_STATUS, 'CANCELLED'))
                );
                $c->addDescendingOrderByColumn(ApplicationPeer::APP_NUMBER);
                break;
            case 'completed':
                $c->add(ApplicationPeer::APP_STATUS, 'COMPLETED');
                $c->add(AppDelegationPeer::DEL_PREVIOUS, '0', Criteria::NOT_EQUAL);
                //$c->addAsColumn('DEL_FINISH_DATE', 'max('.AppDelegationPeer::DEL_FINISH_DATE.')');
                $c->addGroupByColumn(ApplicationPeer::APP_UID);
                $c->addDescendingOrderByColumn(ApplicationPeer::APP_NUMBER);
                break;
            case 'gral':
                $c->add(
                        $c->getNewCriterion(AppThreadPeer::APP_THREAD_STATUS, 'OPEN')->
                                addOr($c->getNewCriterion(ApplicationPeer::APP_STATUS, 'COMPLETED')->
                                        addAnd($c->getNewCriterion(AppDelegationPeer::DEL_PREVIOUS, 0)))
                );
                $c->addDescendingOrderByColumn(ApplicationPeer::APP_NUMBER);
                $params = array();
                $sSql = BasePeer::createSelectSql($c, $params);
                break;
            case 'to_revise':
                $oCriteria = new Criteria('workflow');
                $oCriteria->add(ProcessUserPeer::USR_UID, $sUIDUserLogged);
                $oCriteria->add(ProcessUserPeer::PU_TYPE, 'SUPERVISOR');
                $oDataset = ProcessUserPeer::doSelectRS($oCriteria);
                $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
                $oDataset->next();
                $aProcesses = array();
                while ($aRow = $oDataset->getRow()) {
                    $aProcesses[] = $aRow['PRO_UID'];
                    $oDataset->next();
                }
                $c->add(ApplicationPeer::PRO_UID, $aProcesses, Criteria::IN);
                $c->add(ApplicationPeer::APP_STATUS, 'TO_DO');
                $c->add(AppDelegationPeer::DEL_FINISH_DATE, null, Criteria::ISNULL);
                $c->add(AppThreadPeer::APP_THREAD_STATUS, 'OPEN');
                $c->add(AppDelegationPeer::DEL_THREAD_STATUS, 'OPEN');
                $c->addDescendingOrderByColumn(ApplicationPeer::APP_NUMBER);
                break;
            case 'selfservice':
                //get the valid selfservice tasks for this user
                $oCase = new Cases();
                $tasks = $oCase->getSelfServiceTasks($_SESSION['USER_LOGGED']);
                $aTasks = array();
                foreach ($tasks as $key => $val) {
                    if (strlen(trim($val['uid'])) > 10) {
                        $aTasks[] = $val['uid'];
                    }
                }
                $c = new Criteria('workflow');
                $c->clearSelectColumns();
                $c->addSelectColumn(ApplicationPeer::APP_UID);
                $c->addSelectColumn(ApplicationPeer::APP_NUMBER);
                $c->addSelectColumn(ApplicationPeer::APP_UPDATE_DATE);
                $c->addSelectColumn(ApplicationPeer::PRO_UID);
                $c->addSelectColumn(ApplicationPeer::APP_INIT_USER);
                $c->addSelectColumn(AppDelegationPeer::DEL_PRIORITY);

                $c->addAsColumn(
                        'DEL_TASK_DUE_DATE', " IF (" . AppDelegationPeer::DEL_TASK_DUE_DATE . " <= NOW(),
                    CONCAT('<span style=\'color:red\';>', " . AppDelegationPeer::DEL_TASK_DUE_DATE .
                        ", '</span>'), " . AppDelegationPeer::DEL_TASK_DUE_DATE . ") "
                );

                $c->addSelectColumn(AppDelegationPeer::DEL_INDEX);
                $c->addSelectColumn(AppDelegationPeer::TAS_UID);
                $c->addSelectColumn(AppDelegationPeer::DEL_INIT_DATE);
                $c->addSelectColumn(AppDelegationPeer::DEL_FINISH_DATE);
                $c->addSelectColumn(UsersPeer::USR_UID);
                $c->addAsColumn('APP_CURRENT_USER', "CONCAT(USERS.USR_LASTNAME, ' ', USERS.USR_FIRSTNAME)");
                $c->addSelectColumn(ApplicationPeer::APP_STATUS);
                $c->addAsColumn('APP_TITLE', 'APP_TITLE.CON_VALUE');
                $c->addAsColumn('APP_PRO_TITLE', 'PRO_TITLE.CON_VALUE');
                $c->addAsColumn('APP_TAS_TITLE', 'TAS_TITLE.CON_VALUE');

                $c->addAlias("APP_TITLE", 'CONTENT');
                $c->addAlias("PRO_TITLE", 'CONTENT');
                $c->addAlias("TAS_TITLE", 'CONTENT');

                $c->addJoin(ApplicationPeer::APP_UID, AppDelegationPeer::APP_UID, Criteria::LEFT_JOIN);
                $c->addJoin(AppDelegationPeer::TAS_UID, TaskPeer::TAS_UID, Criteria::LEFT_JOIN);
                $appThreadConds[] = array(ApplicationPeer::APP_UID, AppThreadPeer::APP_UID);
                $appThreadConds[] = array(AppDelegationPeer::DEL_INDEX, AppThreadPeer::DEL_INDEX);
                $c->addJoinMC($appThreadConds, Criteria::LEFT_JOIN);
                $c->addJoin(AppDelegationPeer::USR_UID, UsersPeer::USR_UID, Criteria::LEFT_JOIN);

                $del = DBAdapter::getStringDelimiter();
                $appTitleConds = array();
                $appTitleConds[] = array(ApplicationPeer::APP_UID, 'APP_TITLE.CON_ID');
                $appTitleConds[] = array('APP_TITLE.CON_CATEGORY', $del . 'APP_TITLE' . $del);
                $appTitleConds[] = array('APP_TITLE.CON_LANG', $del . SYS_LANG . $del);
                $c->addJoinMC($appTitleConds, Criteria::LEFT_JOIN);
                //
                $proTitleConds = array();
                $proTitleConds[] = array(ApplicationPeer::PRO_UID, 'PRO_TITLE.CON_ID');
                $proTitleConds[] = array('PRO_TITLE.CON_CATEGORY', $del . 'PRO_TITLE' . $del);
                $proTitleConds[] = array('PRO_TITLE.CON_LANG', $del . SYS_LANG . $del);
                $c->addJoinMC($proTitleConds, Criteria::LEFT_JOIN);
                //
                $tasTitleConds = array();
                $tasTitleConds[] = array(AppDelegationPeer::TAS_UID, 'TAS_TITLE.CON_ID');
                $tasTitleConds[] = array('TAS_TITLE.CON_CATEGORY', $del . 'TAS_TITLE' . $del);
                $tasTitleConds[] = array('TAS_TITLE.CON_LANG', $del . SYS_LANG . $del);
                $c->addJoinMC($tasTitleConds, Criteria::LEFT_JOIN);

                $c->add(AppDelegationPeer::USR_UID, '');
                $c->add(AppDelegationPeer::TAS_UID, $aTasks, Criteria::IN);
                break;
            case 'to_reassign':
                $c->add(
                        $c->getNewCriterion(ApplicationPeer::APP_STATUS, 'TO_DO')->
                                addOr($c->getNewCriterion(ApplicationPeer::APP_STATUS, 'DRAFT'))
                );
                $c->add(AppDelegationPeer::DEL_FINISH_DATE, null, Criteria::ISNULL);
                $c->add(AppThreadPeer::APP_THREAD_STATUS, 'OPEN');
                $c->add(AppDelegationPeer::DEL_THREAD_STATUS, 'OPEN');
                $c->addDescendingOrderByColumn(ApplicationPeer::APP_NUMBER);
                break;
            case 'sent':
                $oCriteria = new Criteria('workflow');
                $oCriteria->addSelectColumn(AppDelayPeer::APP_UID);
                $oCriteria->add(
                        $oCriteria->getNewCriterion(
                                AppDelayPeer::APP_DISABLE_ACTION_USER, null, Criteria::ISNULL
                        )->addOr(
                                $oCriteria->getNewCriterion(AppDelayPeer::APP_DISABLE_ACTION_USER, 0)
                        )
                );
                $oDataset = AppDelayPeer::doSelectRS($oCriteria);
                $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
                $oDataset->next();
                $aProcesses = array();
                while ($aRow = $oDataset->getRow()) {
                    $aProcesses[] = $aRow['APP_UID'];
                    $oDataset->next();
                }
                if (isset($aAdditionalFilter) && isset($aAdditionalFilter['MINE'])) {
                    $c->add($c->getNewCriterion(ApplicationPeer::APP_INIT_USER, $sUIDUserLogged));
                } else {
                    $c->add(
                            $c->getNewCriterion(
                                    ApplicationPeer::APP_INIT_USER, $sUIDUserLogged
                            )->addOr(
                                    $c->getNewCriterion(
                                            AppDelegationPeer::USR_UID, $sUIDUserLogged
                                    )
                            )
                    );
                }
                if (isset($aAdditionalFilter) && isset($aAdditionalFilter['APP_STATUS_FILTER'])) {
                    $c->add(ApplicationPeer::APP_STATUS, $sValue, Criteria::EQUAL);
                } else {
                    $c->add(ApplicationPeer::APP_STATUS, 'DRAFT', Criteria::NOT_EQUAL);
                }

                $c->add(
                        $c->getNewCriterion(
                                AppDelegationPeer::DEL_THREAD_STATUS, 'CLOSED'
                        )->addOr(
                                $c->getNewCriterion(
                                        ApplicationPeer::APP_STATUS, 'COMPLETED'
                                )->addAnd(
                                        $c->getNewCriterion(AppDelegationPeer::DEL_PREVIOUS, 0)
                                )
                        )
                );
                $c->add($c->getNewCriterion(ApplicationPeer::APP_UID, $aProcesses, Criteria::NOT_IN));
                $c->addDescendingOrderByColumn(ApplicationPeer::APP_NUMBER);
                break;
        }

        //select the xmlList to show
        if ($sTypeList == 'gral') {
            if ($RBAC->userCanAccess('PM_DELETECASE') == 1) {
                $xmlfile = $filesList['Alldelete'];
            } else {
                $xmlfile = $filesList['all'];
            }
        } else {
            $xmlfile = $filesList[$sTypeList];
        }

        if ($ClearSession) {
            //OPCION_1: claening the $_SESSION and updating the List.... only case List
            foreach ($filesList as $file) {
                $id = G::createUID('', $file . '.xml');
                unset($_SESSION['pagedTable[' . $id . ']']);
                unset($_SESSION[$id]);
            }
            //OPTION_2: cleaning the $_SESSION and whole List and xmls
            $cur = array_keys($_SESSION);
            foreach ($cur as $key) {
                if (substr($key, 0, 11) === "pagedTable[") {
                    unset($_SESSION[$key]);
                } else {
                    $xml = G::getUIDName($key, '');
                    if (strpos($xml, '.xml') !== false) {
                        unset($_SESSION[$key]);
                    }
                }
            }
        }
        return array($c, $xmlfile);
    }

    /*
     * Get a case in its current index
     *
     * @name loadCaseInCurrentDelegation
     * @param string $sTypeList
     * @param string $sUIDUserLogged
     * @Author Erik Amaru Ortiz <erik@colosa.com>
     * @return array
     */

    public function loadCaseInCurrentDelegation($APP_UID, $titles = false)
    {
        $c = new Criteria('workflow');
        $c->clearSelectColumns();
        $c->addSelectColumn(ApplicationPeer::APP_UID);
        $c->addSelectColumn(ApplicationPeer::APP_NUMBER);
        $c->addSelectColumn(ApplicationPeer::APP_UPDATE_DATE);
        $c->addSelectColumn(AppDelegationPeer::DEL_PRIORITY);
        //$c->addSelectColumn(AppDelegationPeer::DEL_TASK_DUE_DATE);
        $c->addAsColumn(
                'DEL_TASK_DUE_DATE', " IF (" . AppDelegationPeer::DEL_TASK_DUE_DATE . " <= NOW(),
            " . AppDelegationPeer::DEL_TASK_DUE_DATE . " ,
            " . AppDelegationPeer::DEL_TASK_DUE_DATE . ") "
        );

        $c->addSelectColumn(AppDelegationPeer::DEL_INDEX);
        $c->addSelectColumn(AppDelegationPeer::TAS_UID);
        $c->addSelectColumn(AppDelegationPeer::DEL_INIT_DATE);
        $c->addSelectColumn(AppDelegationPeer::DEL_FINISH_DATE);
        $c->addSelectColumn(UsersPeer::USR_UID);
        $c->addAsColumn('APP_CURRENT_USER', "CONCAT(USERS.USR_LASTNAME, ' ', USERS.USR_FIRSTNAME)");
        $c->addSelectColumn(ApplicationPeer::APP_STATUS);
        if ($titles) {
            $c->addAsColumn('APP_TITLE', 'APP_TITLE.CON_VALUE');
            $c->addAsColumn('APP_PRO_TITLE', 'PRO_TITLE.CON_VALUE');
            $c->addAsColumn('APP_TAS_TITLE', 'TAS_TITLE.CON_VALUE');
        }
        //$c->addAsColumn('APP_DEL_PREVIOUS_USER', 'APP_LAST_USER.USR_USERNAME');
        $c->addAsColumn(
                'APP_DEL_PREVIOUS_USER', "CONCAT(APP_LAST_USER.USR_LASTNAME, ' ', APP_LAST_USER.USR_FIRSTNAME)");

        if ($titles) {
            $c->addAlias("APP_TITLE", 'CONTENT');
            $c->addAlias("PRO_TITLE", 'CONTENT');
            $c->addAlias("TAS_TITLE", 'CONTENT');
        }
        $c->addAlias("APP_PREV_DEL", 'APP_DELEGATION');
        $c->addAlias("APP_LAST_USER", 'USERS');

        $c->addJoin(ApplicationPeer::APP_UID, AppDelegationPeer::APP_UID, Criteria::LEFT_JOIN);
        $c->addJoin(AppDelegationPeer::TAS_UID, TaskPeer::TAS_UID, Criteria::LEFT_JOIN);
        $appThreadConds[] = array(ApplicationPeer::APP_UID, AppThreadPeer::APP_UID);
        $appThreadConds[] = array(AppDelegationPeer::DEL_INDEX, AppThreadPeer::DEL_INDEX);
        $c->addJoinMC($appThreadConds, Criteria::LEFT_JOIN);
        $c->addJoin(AppDelegationPeer::USR_UID, UsersPeer::USR_UID, Criteria::LEFT_JOIN);

        if ($titles) {
            $del = DBAdapter::getStringDelimiter();
            $appTitleConds = array();
            $appTitleConds[] = array(ApplicationPeer::APP_UID, 'APP_TITLE.CON_ID');
            $appTitleConds[] = array('APP_TITLE.CON_CATEGORY', $del . 'APP_TITLE' . $del);
            $appTitleConds[] = array('APP_TITLE.CON_LANG', $del . SYS_LANG . $del);
            $c->addJoinMC($appTitleConds, Criteria::LEFT_JOIN);

            $proTitleConds = array();
            $proTitleConds[] = array(ApplicationPeer::PRO_UID, 'PRO_TITLE.CON_ID');
            $proTitleConds[] = array('PRO_TITLE.CON_CATEGORY', $del . 'PRO_TITLE' . $del);
            $proTitleConds[] = array('PRO_TITLE.CON_LANG', $del . SYS_LANG . $del);
            $c->addJoinMC($proTitleConds, Criteria::LEFT_JOIN);

            $tasTitleConds = array();
            $tasTitleConds[] = array(AppDelegationPeer::TAS_UID, 'TAS_TITLE.CON_ID');
            $tasTitleConds[] = array('TAS_TITLE.CON_CATEGORY', $del . 'TAS_TITLE' . $del);
            $tasTitleConds[] = array('TAS_TITLE.CON_LANG', $del . SYS_LANG . $del);
            $c->addJoinMC($tasTitleConds, Criteria::LEFT_JOIN);
        }

        $prevConds = array();
        $prevConds[] = array(ApplicationPeer::APP_UID, 'APP_PREV_DEL.APP_UID');
        $prevConds[] = array('APP_PREV_DEL.DEL_INDEX', AppDelegationPeer::DEL_PREVIOUS);
        $c->addJoinMC($prevConds, Criteria::LEFT_JOIN);

        $usrConds = array();
        $usrConds[] = array('APP_PREV_DEL.USR_UID', 'APP_LAST_USER.USR_UID');
        $c->addJoinMC($usrConds, Criteria::LEFT_JOIN);

        $c->add(TaskPeer::TAS_TYPE, 'SUBPROCESS', Criteria::NOT_EQUAL);

        $c->add(AppDelegationPeer::DEL_FINISH_DATE, null, Criteria::ISNULL);
        $c->add(AppThreadPeer::APP_THREAD_STATUS, 'OPEN');
        $c->add(AppDelegationPeer::DEL_THREAD_STATUS, 'OPEN');

        $c->add(ApplicationPeer::APP_UID, $APP_UID);

        $oDataset = ApplicationPeer::doSelectRS($c);
        $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $oDataset->next();
        $row = $oDataset->getRow();

        return $row;
    }

    /**
     * Get a case in its current index
     *
     * @name loadCaseByDelegation
     * @param string $appUid,
     * @param string $delIndex
     * @author gustavo cruz
     * @return array
     */
    public function loadCaseByDelegation($appUid, $delIndex)
    {
        $c = new Criteria('workflow');
        $c->clearSelectColumns();
        $c->addSelectColumn(ApplicationPeer::APP_UID);
        $c->addSelectColumn(ApplicationPeer::APP_NUMBER);
        $c->addSelectColumn(ApplicationPeer::APP_UPDATE_DATE);
        $c->addSelectColumn(AppDelegationPeer::DEL_PRIORITY);
        //$c->addSelectColumn(AppDelegationPeer::DEL_TASK_DUE_DATE);
        $c->addAsColumn(
                'DEL_TASK_DUE_DATE', " IF (" . AppDelegationPeer::DEL_TASK_DUE_DATE . " <= NOW(),  " . AppDelegationPeer::DEL_TASK_DUE_DATE . " ,
            " . AppDelegationPeer::DEL_TASK_DUE_DATE . ") "
        );

        $c->addSelectColumn(AppDelegationPeer::DEL_INDEX);
        $c->addSelectColumn(AppDelegationPeer::TAS_UID);
        $c->addSelectColumn(AppDelegationPeer::DEL_INIT_DATE);
        $c->addSelectColumn(AppDelegationPeer::DEL_FINISH_DATE);
        $c->addSelectColumn(UsersPeer::USR_UID);
        $c->addAsColumn('APP_CURRENT_USER', "CONCAT(USERS.USR_LASTNAME, ' ', USERS.USR_FIRSTNAME)");
        $c->addSelectColumn(ApplicationPeer::APP_STATUS);
        $c->addAsColumn('APP_TITLE', 'APP_TITLE.CON_VALUE');
        $c->addAsColumn('APP_PRO_TITLE', 'PRO_TITLE.CON_VALUE');
        $c->addAsColumn('APP_TAS_TITLE', 'TAS_TITLE.CON_VALUE');
        //$c->addAsColumn('APP_DEL_PREVIOUS_USER', 'APP_LAST_USER.USR_USERNAME');
        $c->addAsColumn(
                'APP_DEL_PREVIOUS_USER', "CONCAT(APP_LAST_USER.USR_LASTNAME, ' ', APP_LAST_USER.USR_FIRSTNAME)");

        $c->addAlias("APP_TITLE", 'CONTENT');
        $c->addAlias("PRO_TITLE", 'CONTENT');
        $c->addAlias("TAS_TITLE", 'CONTENT');
        $c->addAlias("APP_PREV_DEL", 'APP_DELEGATION');
        $c->addAlias("APP_LAST_USER", 'USERS');

        $c->addJoin(ApplicationPeer::APP_UID, AppDelegationPeer::APP_UID, Criteria::LEFT_JOIN);
        $c->addJoin(AppDelegationPeer::TAS_UID, TaskPeer::TAS_UID, Criteria::LEFT_JOIN);
        $appThreadConds[] = array(ApplicationPeer::APP_UID, AppThreadPeer::APP_UID);
        $appThreadConds[] = array(AppDelegationPeer::DEL_INDEX, AppThreadPeer::DEL_INDEX);
        $c->addJoinMC($appThreadConds, Criteria::LEFT_JOIN);
        $c->addJoin(AppDelegationPeer::USR_UID, UsersPeer::USR_UID, Criteria::LEFT_JOIN);

        $del = DBAdapter::getStringDelimiter();
        $appTitleConds = array();
        $appTitleConds[] = array(ApplicationPeer::APP_UID, 'APP_TITLE.CON_ID');
        $appTitleConds[] = array('APP_TITLE.CON_CATEGORY', $del . 'APP_TITLE' . $del);
        $appTitleConds[] = array('APP_TITLE.CON_LANG', $del . SYS_LANG . $del);
        $c->addJoinMC($appTitleConds, Criteria::LEFT_JOIN);

        $proTitleConds = array();
        $proTitleConds[] = array(ApplicationPeer::PRO_UID, 'PRO_TITLE.CON_ID');
        $proTitleConds[] = array('PRO_TITLE.CON_CATEGORY', $del . 'PRO_TITLE' . $del);
        $proTitleConds[] = array('PRO_TITLE.CON_LANG', $del . SYS_LANG . $del);
        $c->addJoinMC($proTitleConds, Criteria::LEFT_JOIN);

        $tasTitleConds = array();
        $tasTitleConds[] = array(AppDelegationPeer::TAS_UID, 'TAS_TITLE.CON_ID');
        $tasTitleConds[] = array('TAS_TITLE.CON_CATEGORY', $del . 'TAS_TITLE' . $del);
        $tasTitleConds[] = array('TAS_TITLE.CON_LANG', $del . SYS_LANG . $del);
        $c->addJoinMC($tasTitleConds, Criteria::LEFT_JOIN);

        $prevConds = array();
        $prevConds[] = array(ApplicationPeer::APP_UID, 'APP_PREV_DEL.APP_UID');
        $prevConds[] = array('APP_PREV_DEL.DEL_INDEX', AppDelegationPeer::DEL_PREVIOUS);
        $c->addJoinMC($prevConds, Criteria::LEFT_JOIN);

        $usrConds = array();
        $usrConds[] = array('APP_PREV_DEL.USR_UID', 'APP_LAST_USER.USR_UID');
        $c->addJoinMC($usrConds, Criteria::LEFT_JOIN);

        $c->add(TaskPeer::TAS_TYPE, 'SUBPROCESS', Criteria::NOT_EQUAL);

        $c->add(AppDelegationPeer::DEL_FINISH_DATE, null, Criteria::ISNULL);
        $c->add(AppThreadPeer::APP_THREAD_STATUS, 'OPEN');
        $c->add(AppDelegationPeer::DEL_THREAD_STATUS, 'OPEN');

        $c->add(ApplicationPeer::APP_UID, $appUid);
        $c->add(AppDelegationPeer::DEL_INDEX, $delIndex);

        $oDataset = ApplicationPeer::doSelectRS($c);
        $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $oDataset->next();
        $row = $oDataset->getRow();

        return $row;
    }

    /**
     *
     * @name ThrowUnpauseDaemon
     * author: erik@colosa.com
     * Description: This method set all cases with the APP_DISABLE_ACTION_DATE for today
     * @return void
     */
    public function ThrowUnpauseDaemon($today, $cron = 0)
    {
        $today = ($today == date('Y-m-d')) ? date('Y-m-d') : $today;
        $c = new Criteria('workflow');
        $c->clearSelectColumns();
        $c->add(
                $c->getNewCriterion(AppDelayPeer::APP_DISABLE_ACTION_USER, null, Criteria::ISNULL)->
                        addOr($c->getNewCriterion(AppDelayPeer::APP_DISABLE_ACTION_USER, 0)
                        )
        );
        $c->add(
                $c->getNewCriterion(
                                AppDelayPeer::APP_DISABLE_ACTION_DATE, $today . ' 23:59:59', Criteria::LESS_EQUAL)->
                        addAnd($c->getNewCriterion(AppDelayPeer::APP_DISABLE_ACTION_DATE, null, Criteria::ISNOTNULL)
                        )
        );
        $d = AppDelayPeer::doSelectRS($c);
        $d->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $d->next();

        while ($aRow = $d->getRow()) {
            if ($cron == 1) {
                $arrayCron = unserialize(trim(@file_get_contents(PATH_DATA . "cron")));
                $arrayCron["processcTimeStart"] = time();
                @file_put_contents(PATH_DATA . "cron", serialize($arrayCron));
            }

            $this->unpauseCase($aRow['APP_UID'], $aRow['APP_DEL_INDEX'], 'System Daemon');
            $d->next();
        }
    }

    /*
     * it Changes the date and APP_DISABLE_ACTION_USER to unpause cases
     *
     * @name UnpauseRoutedCasesWithPauseFlagEnabled
     * @param string $usrLogged
     * @return void
     */

    public function UnpauseRoutedCasesWithPauseFlagEnabled($usrLogged)
    {
        $c = new Criteria();
        $c->clearSelectColumns();
        $c->addSelectColumn(AppDelayPeer::APP_DELAY_UID);
        $c->add(
                $c->getNewCriterion(
                                AppDelayPeer::APP_DELEGATION_USER, $usrLogged, Criteria::EQUAL)->
                        addAnd($c->getNewCriterion(AppDelegationPeer::DEL_THREAD_STATUS, 'CLOSED', Criteria::EQUAL))->
                        addAnd($c->getNewCriterion(AppDelayPeer::APP_DISABLE_ACTION_USER, null, Criteria::ISNULL)->
                                addOr($c->getNewCriterion(AppDelayPeer::APP_DISABLE_ACTION_USER, 0))
                        )
        );
        $aConditions = array();
        $aConditions[] = array(AppDelayPeer::APP_UID, AppDelegationPeer::APP_UID);
        $aConditions[] = array(AppDelayPeer::APP_DEL_INDEX, AppDelegationPeer::DEL_INDEX);
        $c->addJoinMC($aConditions, Criteria::LEFT_JOIN);
        $rs = AppDelayPeer::doSelectRS($c);
        $rs->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        while ($rs->next()) {
            $row = $rs->getRow();
            if (is_array($row)) {
                $con = Propel::getConnection('workflow');
                $c1 = new Criteria('workflow');
                $c1->add(AppDelayPeer::APP_DELAY_UID, $row['APP_DELAY_UID']);
                // update set
                $c2 = new Criteria('workflow');
                $c2->add(AppDelayPeer::APP_DISABLE_ACTION_USER, $usrLogged);
                $c2->add(AppDelayPeer::APP_DISABLE_ACTION_DATE, date('Y-m-d'));
                BasePeer::doUpdate($c1, $c2, $con);
            }
        }
    }

    /*
     * Get the application UID by case number
     *
     * @name getApplicationUIDByNumber
     * @param integer $iApplicationNumber
     * @return string
     */

    public function getApplicationUIDByNumber($iApplicationNumber)
    {
        $oCriteria = new Criteria();
        $oCriteria->add(ApplicationPeer::APP_NUMBER, $iApplicationNumber);
        $oApplication = ApplicationPeer::doSelectOne($oCriteria);
        if (!is_null($oApplication)) {
            return $oApplication->getAppUid();
        } else {
            return null;
        }
    }

    /*
     * Get the current delegation of a user or a case
     * @name getCurrentDelegation
     * @param string $sApplicationUID
     * @param string $sUserUID
     * @return integer
     */

    public function getCurrentDelegation($sApplicationUID = '', $sUserUID = '')
    {
        $oCriteria = new Criteria();
        $oCriteria->add(AppDelegationPeer::APP_UID, $sApplicationUID);
        $oCriteria->add(AppDelegationPeer::USR_UID, $sUserUID);
        $oCriteria->add(AppDelegationPeer::DEL_THREAD_STATUS, 'OPEN');
        $oCriteria->addDescendingOrderByColumn(AppDelegationPeer::DEL_DELEGATE_DATE);
        $oApplication = AppDelegationPeer::doSelectOne($oCriteria);
        if (!is_null($oApplication)) {
            return $oApplication->getDelIndex();
        }

        //if the user is not in the task, we need to return a valid del index, so we are returning the latest delindex
        $oCriteria = new Criteria();
        $oCriteria->add(AppDelegationPeer::APP_UID, $sApplicationUID);
        $oCriteria->addDescendingOrderByColumn(AppDelegationPeer::DEL_DELEGATE_DATE);
        $oApplication = AppDelegationPeer::doSelectOne($oCriteria);
        if (!is_null($oApplication)) {
            return $oApplication->getDelIndex();
        }
        throw ( new Exception('this case has 0 delegations') );
    }

    /*
     * Get the current delegation of a user or a case
     * @name loadTriggers
     * @param string $sTasUid
     * @param string $sStepType
     * @param array $sStepUidObj
     * @param string $sTriggerType
     * @return integer
     */

    public function loadTriggers($sTasUid, $sStepType, $sStepUidObj, $sTriggerType)
    {
        $aTriggers = array();
        if (($sStepUidObj != -1) && ($sStepUidObj != -2)) {
            $c = new Criteria();
            $c->clearSelectColumns();
            $c->addSelectColumn(StepPeer::STEP_UID);
            $c->add(StepPeer::TAS_UID, $sTasUid);
            $c->add(StepPeer::STEP_TYPE_OBJ, $sStepType);
            $c->add(StepPeer::STEP_UID_OBJ, $sStepUidObj);
            $rs = StepPeer::doSelectRS($c);
            $rs->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $rs->next();
            $row = $rs->getRow();
            $sStepUid = $row['STEP_UID'];
        } else {
            $sStepUid = $sStepUidObj;
        }
        $c = new Criteria();
        $c->clearSelectColumns();
        $c->addSelectColumn(TriggersPeer::TRI_UID);
        $c->addSelectColumn(StepTriggerPeer::ST_CONDITION);
        $c->addSelectColumn(TriggersPeer::TRI_TYPE);
        $c->addSelectColumn(TriggersPeer::TRI_WEBBOT);
        $c->add(StepTriggerPeer::STEP_UID, $sStepUid);
        $c->add(StepTriggerPeer::TAS_UID, $sTasUid);
        $c->add(StepTriggerPeer::ST_TYPE, $sTriggerType);
        $c->addJoin(StepTriggerPeer::TRI_UID, TriggersPeer::TRI_UID, Criteria::LEFT_JOIN);
        $c->addAscendingOrderByColumn(StepTriggerPeer::ST_POSITION);
        $rs = TriggersPeer::doSelectRS($c);
        $rs->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $rs->next();
        $row = $rs->getRow();
        while (is_array($row)) {
            $aTriggers[] = $row;
            $rs->next();
            $row = $rs->getRow();
        }
        return $aTriggers;
    }

    /*
     * Execute trigger in task
     * @name executeTriggers
     * @param string $sTasUid
     * @param string $sStepType
     * @param array $sStepUidObj
     * @param string $sTriggerType
     * @param array $aFields
     * @return integer
     */

    public function executeTriggers($sTasUid, $sStepType, $sStepUidObj, $sTriggerType, $aFields = array())
    {
        $aTriggers = $this->loadTriggers($sTasUid, $sStepType, $sStepUidObj, $sTriggerType);
        if (count($aTriggers) > 0) {
            global $oPMScript;
            $oPMScript = new PMScript();
            $oPMScript->setFields($aFields);
            foreach ($aTriggers as $aTrigger) {
                $bExecute = true;
                if ($aTrigger['ST_CONDITION'] !== '') {
                    $oPMScript->setScript($aTrigger['ST_CONDITION']);
                    $bExecute = $oPMScript->evaluate();
                }
                if ($bExecute) {
                    $oPMScript->setScript($aTrigger['TRI_WEBBOT']);
                    $oPMScript->execute();
                }
            }
            return $oPMScript->aFields;
        } else {
            return $aFields;
        }
    }

    /*
     * Get the trigger's names
     * @name getTriggerNames
     * @param string $triggers
     * @return integer
     */

    public function getTriggerNames($triggers)
    {
        $triggers_info = Array();
        $aTriggers = array();
        foreach ($triggers as $key => $val) {
            $aTriggers[] = $val['TRI_UID'];
        }
        $lang = defined('SYS_LANG') ? SYS_LANG : 'en';
        $c = new Criteria();
        $c->clearSelectColumns();
        $c->addSelectColumn(ContentPeer::CON_ID);
        $c->addSelectColumn(ContentPeer::CON_VALUE);
        $c->add(ContentPeer::CON_ID, $aTriggers, Criteria::IN);
        $c->add(ContentPeer::CON_CATEGORY, 'TRI_TITLE');
        $c->add(ContentPeer::CON_LANG, $lang);
        $rs = TriggersPeer::doSelectRS($c);
        $rs->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $rs->next();
        $row = $rs->getRow();
        while (is_array($row)) {
            $info[$row['CON_ID']] = ($row['CON_VALUE'] != '' ? $row['CON_VALUE'] : '-');
            $rs->next();
            $row = $rs->getRow();
        }
        foreach ($triggers as $key => $val) {
            if (isset($info[$val['TRI_UID']])) {
                $triggers_info[] = $info[$val['TRI_UID']];
            } else {
                $triggers_info[] = Content::load('TRI_TITLE', '', $val['TRI_UID'], $lang);
            }
        }
        return $triggers_info;
    }

    /*
     * Return the input documents list criteria object
     *
     * @name getInputDocumentsCriteria
     * @param string $sApplicationUID
     * @param string $iDelegation
     * @param string $sDocumentUID
     * @param string $sAppDocuUID
     * @return object
     */

    public function getInputDocumentsCriteria($sApplicationUID, $iDelegation, $sDocumentUID, $sAppDocuUID = '')
    {
        try {
            $deletePermission = $this->getAllObjectsFrom(
                    $_SESSION['PROCESS'], $sApplicationUID, $_SESSION['TASK'], $_SESSION['USER_LOGGED'], $ACTION = 'DELETE'
            );
            $listing = false;
            $oPluginRegistry = & PMPluginRegistry::getSingleton();
            if ($oPluginRegistry->existsTrigger(PM_CASE_DOCUMENT_LIST)) {
                $folderData = new folderData(null, null, $sApplicationUID, null, $_SESSION['USER_LOGGED']);
                $folderData->PMType = "INPUT";
                $folderData->returnList = true;
                //$oPluginRegistry      = & PMPluginRegistry::getSingleton();
                $listing = $oPluginRegistry->executeTriggers(PM_CASE_DOCUMENT_LIST, $folderData);
            }

            $oUser = new Users();
            $oAppDocument = new AppDocument();
            $oCriteria = new Criteria('workflow');
            $oCriteria->add(AppDocumentPeer::APP_UID, $sApplicationUID);
            //$oCriteria->add(AppDocumentPeer::DEL_INDEX, $iDelegation);
            $oCriteria->add(AppDocumentPeer::DOC_UID, $sDocumentUID);
            if ($sAppDocuUID != "") {
                $oCriteria->add(AppDocumentPeer::APP_DOC_UID, $sAppDocuUID);
            }

            $oCriteria->add(AppDocumentPeer::APP_DOC_TYPE, array('INPUT', 'ATTACHED'), CRITERIA::IN);
            $oCriteria->add(AppDocumentPeer::APP_DOC_STATUS, array('ACTIVE'), Criteria::IN);
            $oCriteria->addAscendingOrderByColumn(AppDocumentPeer::APP_DOC_UID);

            $oDataset = AppDocumentPeer::doSelectRS($oCriteria);
            $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $oDataset->next();
            $aInputDocuments = array();
            $aInputDocuments[] = array(
                'APP_DOC_UID' => 'char',
                'DOC_VERSION' => 'char',
                'DOC_UID' => 'char',
                'APP_DOC_COMMENT' => 'char',
                'APP_DOC_FILENAME' => 'char',
                'APP_DOC_INDEX' => 'integer'
            );
            while ($aRow = $oDataset->getRow()) {
                $aAux = $oAppDocument->load($aRow['APP_DOC_UID'], $aRow['DOC_VERSION']);
                $lastVersion = $oAppDocument->getLastAppDocVersion($aRow['APP_DOC_UID'], $sApplicationUID);
                $aFields = array(
                    'APP_DOC_UID' => $aAux['APP_DOC_UID'],
                    'DOC_VERSION' => $aAux['DOC_VERSION'],
                    'DOC_UID' => $aAux['DOC_UID'],
                    'APP_DOC_COMMENT' => $aAux['APP_DOC_COMMENT'],
                    'APP_DOC_FILENAME' => $aAux['APP_DOC_FILENAME'],
                    'APP_DOC_INDEX' => $aAux['APP_DOC_INDEX']
                );
                if ($aFields['APP_DOC_FILENAME'] != '') {
                    $aFields['TITLE'] = $aFields['APP_DOC_FILENAME'];
                } else {
                    $aFields['TITLE'] = $aFields['APP_DOC_COMMENT'];
                }
                $aFields['POSITION'] = isset($_SESSION['STEP_POSITION']) ? $_SESSION['STEP_POSITION'] : 1;
                $aFields['CONFIRM'] = G::LoadTranslation('ID_CONFIRM_DELETE_INPUT_AND_HISTORY');

                $aFields['ID_DELETE'] = '';
                if (in_array($aRow['APP_DOC_UID'], $deletePermission['INPUT_DOCUMENTS'])) {
                    $aFields['ID_DELETE'] = G::LoadTranslation('ID_DELETE');
                }
                $aFields['REPLACE_LABEL'] = "";
                if (($aRow['DOC_VERSION'] == $lastVersion) || ($sAppDocuUID != "")) {
                    $aFields['REPLACE_LABEL'] = G::LoadTranslation('ID_REPLACE');
                    $oInputDocument = new InputDocument();
                    $inputDocumentFields = $oInputDocument->load($aRow['DOC_UID']);
                    if ($inputDocumentFields['INP_DOC_VERSIONING']) {
                        $aFields['NEWVERSION_LABEL'] = G::LoadTranslation('ID_NEW_VERSION');
                    }
                }
                if ($aRow['DOC_VERSION'] > 1) {
                    $aFields['VERSIONHISTORY_LABEL'] = G::LoadTranslation('ID_VERSION_HISTORY');
                }

                if ($aRow['USR_UID'] != -1) {
                    $aUser = $oUser->load($aRow['USR_UID']);
                    $aFields['CREATOR'] = $aUser['USR_FIRSTNAME'] . ' ' . $aUser['USR_LASTNAME'];
                } else {
                    $aFields['CREATOR'] = '***';
                }

                $aFields['APP_DOC_CREATE_DATE'] = $aRow['APP_DOC_CREATE_DATE'];
                $aFields['DOC_VERSION_LABEL'] = $aRow['DOC_VERSION'];
                $aFields['DOWNLOAD_LABEL'] = G::LoadTranslation('ID_DOWNLOAD');
                $aFields['DOWNLOAD_LINK'] = "cases_ShowDocument?a=" .
                        $aRow['APP_DOC_UID'] . "&v=" . $aRow['DOC_VERSION'];

                if (is_array($listing)) {
                    foreach ($listing as $folderitem) {
                        if ($folderitem->filename == $aRow['APP_DOC_UID']) {
                            $aFields['DOWNLOAD_LABEL'] = G::LoadTranslation('ID_GET_EXTERNAL_FILE');
                            $aFields['DOWNLOAD_LINK'] = $folderitem->downloadScript;
                            continue;
                        }
                    }
                }
                $aFields['COMMENT'] = $aFields['APP_DOC_COMMENT'];
                if (($aRow['DOC_VERSION'] == $lastVersion) || ($sAppDocuUID != "")) {
                    $aInputDocuments[] = $aFields;
                }
                $oDataset->next();
            }

            global $_DBArray;
            $_DBArray['inputDocuments'] = $aInputDocuments;
            $_SESSION['_DBArray'] = $_DBArray;
            G::LoadClass('ArrayPeer');
            $oCriteria = new Criteria('dbarray');
            $oCriteria->setDBArrayTable('inputDocuments');
            // $oCriteria->addAscendingOrderByColumn(AppDocumentPeer::APP_DOC_CREATE_DATE);
            return $oCriteria;
        } catch (exception $oException) {
            throw $oException;
        }
    }

    /*
     * Return the input documents list to Review
     *
     * @name getInputDocumentsCriteriaToRevise
     * @param string $sApplicationUID
     * @return object
     */

    public function getInputDocumentsCriteriaToRevise($sApplicationUID)
    {
        try {
            $oAppDocument = new AppDocument();
            $oCriteria = new Criteria('workflow');
            $oCriteria->add(AppDocumentPeer::APP_UID, $sApplicationUID);
            $oCriteria->add(AppDocumentPeer::APP_DOC_TYPE, array('INPUT', 'ATTACHED'), CRITERIA::IN);
            $oCriteria->add(AppDocumentPeer::APP_DOC_STATUS, array('ACTIVE'), Criteria::IN);
            $oCriteria->addAscendingOrderByColumn(AppDocumentPeer::APP_DOC_INDEX);
            $oDataset = AppDocumentPeer::doSelectRS($oCriteria);
            $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $oDataset->next();
            $aInputDocuments = array();
            $aInputDocuments[] = array(
                'APP_DOC_UID' => 'char',
                'DOC_UID' => 'char',
                'APP_DOC_COMMENT' => 'char',
                'APP_DOC_FILENAME' => 'char',
                'APP_DOC_INDEX' => 'integer'
            );
            while ($aRow = $oDataset->getRow()) {
                $aAux = $oAppDocument->load($aRow['APP_DOC_UID'], $aRow['DOC_VERSION']);
                $aFields = array(
                    'APP_DOC_UID' => $aAux['APP_DOC_UID'],
                    'DOC_UID' => $aAux['DOC_UID'],
                    'APP_DOC_COMMENT' => $aAux['APP_DOC_COMMENT'],
                    'APP_DOC_FILENAME' => $aAux['APP_DOC_FILENAME'],
                    'APP_DOC_INDEX' => $aAux['APP_DOC_INDEX']
                );

                if ($aFields['APP_DOC_FILENAME'] != '') {
                    $aFields['TITLE'] = $aFields['APP_DOC_FILENAME'];
                } else {
                    $aFields['TITLE'] = $aFields['APP_DOC_COMMENT'];
                }
                $aFields['CREATE_DATE'] = $aRow['APP_DOC_CREATE_DATE'];
                $aFields['TYPE'] = $aRow['APP_DOC_TYPE'];

                $aFields['POSITION'] = $_SESSION['STEP_POSITION'];
                $aFields['CONFIRM'] = G::LoadTranslation('ID_CONFIRM_DELETE_ELEMENT');
                $aInputDocuments[] = $aFields;
                $oDataset->next();
            }
            global $_DBArray;
            $_DBArray['inputDocuments'] = $aInputDocuments;
            $_SESSION['_DBArray'] = $_DBArray;
            G::LoadClass('ArrayPeer');
            $oCriteria = new Criteria('dbarray');
            $oCriteria->setDBArrayTable('inputDocuments');
            $oCriteria->addAscendingOrderByColumn(AppDocumentPeer::APP_DOC_INDEX);
            return $oCriteria;
        } catch (exception $oException) {
            throw $oException;
        }
    }

    /**
     * Add a input document
     *
     * Return the application document ID
     *
     * @param string $inputDocumentUid Input document ID
     * @param string $appDocUid Application document ID
     * @param int $docVersion Document version
     * @param string $appDocType Document type
     * @param string $appDocComment Document comment
     * @param string $inputDocumentAction Action, posible values: null or empty (Add), "R" (Replace), "NV" (New Version)
     * @param string $applicationUid Application ID
     * @param int $delIndex Delegation index
     * @param string $taskUid Task ID
     * @param string $userUid User ID
     * @param string $option Option, posible values: "xmlform", "file"
     * @param string $file File ($_FILES["form"]["name"]["APP_DOC_FILENAME"] or path to file)
     * @param int $fileError File error ($_FILES["form"]["error"]["APP_DOC_FILENAME"] or 0)
     * @param string $fileTmpName File temporal name ($_FILES["form"]["tmp_name"]["APP_DOC_FILENAME"] or null)
     * @return string Return application document ID
     */
    public function addInputDocument($inputDocumentUid, $appDocUid, $docVersion, $appDocType, $appDocComment, $inputDocumentAction, $applicationUid, $delIndex, $taskUid, $userUid, $option, $file, $fileError = 0, $fileTmpName = null)
    {
        $appDocFileName = null;
        $sw = 0;

        switch ($option) {
            case "xmlform":
                $appDocFileName = $file;

                if ($fileError == 0) {
                    $sw = 1;
                }
                break;
            case "file":
                $appDocFileName = basename($file);

                if (file_exists($file) && is_file($file)) {
                    $sw = 1;
                }
                break;
        }

        if ($sw == 0) {
            return null;
        }

        //Info
        $inputDocument = new InputDocument();
        $arrayInputDocumentData = $inputDocument->load($inputDocumentUid);

        //Get the Custom Folder ID (create if necessary)
        $appFolder = new AppFolder();
        $folderId = $appFolder->createFromPath($arrayInputDocumentData["INP_DOC_DESTINATION_PATH"], $applicationUid);

        $tags = $appFolder->parseTags($arrayInputDocumentData["INP_DOC_TAGS"], $applicationUid);

        $appDocument = new AppDocument();
        $arrayField = array();

        switch ($inputDocumentAction) {
            case "R":
                //Replace
                $arrayField = array(
                    "APP_DOC_UID" => $appDocUid,
                    "APP_UID" => $applicationUid,
                    "DOC_VERSION" => $docVersion,
                    "DEL_INDEX" => $delIndex,
                    "USR_UID" => $userUid,
                    "DOC_UID" => $inputDocumentUid,
                    "APP_DOC_TYPE" => $appDocType,
                    "APP_DOC_CREATE_DATE" => date("Y-m-d H:i:s"),
                    "APP_DOC_COMMENT" => $appDocComment,
                    "APP_DOC_TITLE" => "",
                    "APP_DOC_FILENAME" => $appDocFileName,
                    "FOLDER_UID" => $folderId,
                    "APP_DOC_TAGS" => $tags
                );

                $appDocument->update($arrayField);
                break;
            case "NV":
                //New Version
                $arrayField = array(
                    "APP_DOC_UID" => $appDocUid,
                    "APP_UID" => $applicationUid,
                    "DEL_INDEX" => $delIndex,
                    "USR_UID" => $userUid,
                    "DOC_UID" => $inputDocumentUid,
                    "APP_DOC_TYPE" => $appDocType,
                    "APP_DOC_CREATE_DATE" => date("Y-m-d H:i:s"),
                    "APP_DOC_COMMENT" => $appDocComment,
                    "APP_DOC_TITLE" => "",
                    "APP_DOC_FILENAME" => $appDocFileName,
                    "FOLDER_UID" => $folderId,
                    "APP_DOC_TAGS" => $tags
                );

                $appDocument->create($arrayField);
                break;
            default:
                //New
                $arrayField = array(
                    "APP_UID" => $applicationUid,
                    "DEL_INDEX" => $delIndex,
                    "USR_UID" => $userUid,
                    "DOC_UID" => $inputDocumentUid,
                    "APP_DOC_TYPE" => $appDocType,
                    "APP_DOC_CREATE_DATE" => date("Y-m-d H:i:s"),
                    "APP_DOC_COMMENT" => $appDocComment,
                    "APP_DOC_TITLE" => "",
                    "APP_DOC_FILENAME" => $appDocFileName,
                    "FOLDER_UID" => $folderId,
                    "APP_DOC_TAGS" => $tags
                );

                $appDocument->create($arrayField);
                break;
        }

        //Save the file
        $appDocUid = $appDocument->getAppDocUid();
        $docVersion = $appDocument->getDocVersion();
        $arrayInfo = pathinfo($appDocument->getAppDocFilename());
        $extension = (isset($arrayInfo["extension"])) ? $arrayInfo["extension"] : null;
        $strPathName = PATH_DOCUMENT . $applicationUid . PATH_SEP;
        $strFileName = $appDocUid . "_" . $docVersion . "." . $extension;

        switch ($option) {
            case "xmlform":
                G::uploadFile($fileTmpName, $strPathName, $strFileName);
                break;
            case "file":
                $umaskOld = umask(0);

                if (!is_dir($strPathName)) {
                    G::verifyPath($strPathName, true);
                }

                copy($file, $strPathName . $strFileName);
                chmod($strPathName . $strFileName, 0666);
                umask($umaskOld);
                break;
        }

        //Plugin Hook PM_UPLOAD_DOCUMENT for upload document
        $pluginRegistry = &PMPluginRegistry::getSingleton();

        if ($pluginRegistry->existsTrigger(PM_UPLOAD_DOCUMENT) && class_exists("uploadDocumentData")) {
            $triggerDetail = $pluginRegistry->getTriggerInfo(PM_UPLOAD_DOCUMENT);
            $documentData = new uploadDocumentData(
                            $applicationUid,
                            $userUid,
                            $strPathName . $strFileName,
                            $arrayField["APP_DOC_FILENAME"],
                            $appDocUid,
                            $docVersion
            );
            $uploadReturn = $pluginRegistry->executeTriggers(PM_UPLOAD_DOCUMENT, $documentData);

            if ($uploadReturn) {
                $arrayField["APP_DOC_PLUGIN"] = $triggerDetail->sNamespace;

                if (!isset($arrayField["APP_DOC_UID"])) {
                    $arrayField["APP_DOC_UID"] = $appDocUid;
                }

                if (!isset($arrayField["DOC_VERSION"])) {
                    $arrayField["DOC_VERSION"] = $docVersion;
                }

                $appDocument->update($arrayField);

                unlink($strPathName . $strFileName);
            }
        }
        //End plugin

        return $appDocUid;
    }

    /*
     * Return the input documents list to Review
     *
     * @name getInputDocumentsCriteriaToRevise
     * @param string $sApplicationUID
     * @return object
     */

    public function getOutputDocumentsCriteriaToRevise($sApplicationUID)
    {
        try {
            $oAppDocument = new AppDocument();
            $oCriteria = new Criteria('workflow');
            $oCriteria->add(AppDocumentPeer::APP_UID, $sApplicationUID);
            $oCriteria->add(AppDocumentPeer::APP_DOC_TYPE, 'OUTPUT');
            $oCriteria->add(AppDocumentPeer::APP_DOC_STATUS, array('ACTIVE'), Criteria::IN);
            $oCriteria->addAscendingOrderByColumn(AppDocumentPeer::APP_DOC_INDEX);
            $oDataset = AppDocumentPeer::doSelectRS($oCriteria);
            $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $oDataset->next();
            $aOutputDocuments = array();
            $aOutputDocuments[] = array(
                'APP_DOC_UID' => 'char',
                'DOC_UID' => 'char',
                'APP_DOC_COMMENT' => 'char',
                'APP_DOC_FILENAME' => 'char',
                'APP_DOC_INDEX' => 'integer',
                'APP_DOC_CREATE_DATE' => 'char'
            );
            while ($aRow = $oDataset->getRow()) {
                $aAux = $oAppDocument->load($aRow['APP_DOC_UID'], $aRow['DOC_VERSION']);
                $aFields = array(
                    'APP_DOC_UID' => $aAux['APP_DOC_UID'],
                    'DOC_UID' => $aAux['DOC_UID'],
                    'APP_DOC_COMMENT' => $aAux['APP_DOC_COMMENT'],
                    'APP_DOC_FILENAME' => $aAux['APP_DOC_FILENAME'],
                    'APP_DOC_INDEX' => $aAux['APP_DOC_INDEX'],
                    'APP_DOC_CREATE_DATE' => $aRow['APP_DOC_CREATE_DATE']
                );
                if ($aFields['APP_DOC_FILENAME'] != '') {
                    $aFields['TITLE'] = $aFields['APP_DOC_FILENAME'];
                } else {
                    $aFields['TITLE'] = $aFields['APP_DOC_COMMENT'];
                }
                $aOutputDocuments[] = $aFields;
                $oDataset->next();
            }
            global $_DBArray;
            $_DBArray['outputDocuments'] = $aOutputDocuments;
            $_SESSION['_DBArray'] = $_DBArray;
            G::LoadClass('ArrayPeer');
            $oCriteria = new Criteria('dbarray');
            $oCriteria->setDBArrayTable('outputDocuments');
            $oCriteria->addAscendingOrderByColumn(AppDocumentPeer::APP_DOC_INDEX);
            return $oCriteria;
        } catch (exception $oException) {
            throw $oException;
        }
    }

    /*
     * Return the (Application) Criteria object
     *
     * @name getCriteriaProcessCases
     * @param string $status
     * @param string $PRO_UID
     * @return object
     */

    public function getCriteriaProcessCases($status, $PRO_UID)
    {
        $c = new Criteria('workflow');
        $c->add(ApplicationPeer::APP_STATUS, $status);
        $c->add(ApplicationPeer::PRO_UID, $PRO_UID);
        return $c;
    }

    /*
     * pause a Case
     *
     * @name pauseCase
     * @param string $sApplicationUID
     * @param string $iDelegation
     * @param string $sUserUID
     * @param string $sUnpauseDate
     * @return object
     */

    public function pauseCase($sApplicationUID, $iDelegation, $sUserUID, $sUnpauseDate = null)
    {
        $this->CloseCurrentDelegation($sApplicationUID, $iDelegation);
        $oApplication = new Application();
        $aFields = $oApplication->Load($sApplicationUID);
        //get the appthread row id ( APP_THREAD_INDEX' )
        $oCriteria = new Criteria('workflow');
        $oCriteria->clearSelectColumns();
        $oCriteria->addSelectColumn(AppThreadPeer::APP_THREAD_INDEX);
        $oCriteria->add(AppThreadPeer::APP_UID, $sApplicationUID);
        $oCriteria->add(AppThreadPeer::DEL_INDEX, $iDelegation);
        $oDataset = AppThreadPeer::doSelectRS($oCriteria);
        $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $oDataset->next();
        $aRow = $oDataset->getRow();

        //now create a row in APP_DELAY with type PAUSE
        $aData['PRO_UID'] = $aFields['PRO_UID'];
        $aData['APP_UID'] = $sApplicationUID;
        $aData['APP_THREAD_INDEX'] = $aRow['APP_THREAD_INDEX'];
        $aData['APP_DEL_INDEX'] = $iDelegation;
        $aData['APP_TYPE'] = 'PAUSE';
        $aData['APP_STATUS'] = $aFields['APP_STATUS'];
        $aData['APP_DELEGATION_USER'] = $sUserUID;
        $aData['APP_ENABLE_ACTION_USER'] = $sUserUID;
        $aData['APP_ENABLE_ACTION_DATE'] = date('Y-m-d H:i:s');
        $aData['APP_DISABLE_ACTION_DATE'] = $sUnpauseDate;
        $oAppDelay = new AppDelay();
        $oAppDelay->create($aData);

        $aFields['APP_STATUS'] = 'PAUSED';
        $oApplication->update($aFields);

        //update searchindex
        if ($this->appSolr != null) {
            $this->appSolr->updateApplicationSearchIndex($sApplicationUID);
        }

        $this->getExecuteTriggerProcess($sApplicationUID, 'PAUSED');
    }

    /*
     * unpause a case
     *
     * @name unpauseCase
     * @param string $sApplicationUID
     * @param string $iDelegation
     * @param string $sUserUID
     * @return object
     */

    public function unpauseCase($sApplicationUID, $iDelegation, $sUserUID)
    {
        //get information about current $iDelegation row
        $oAppDelegation = new AppDelegation();
        $aFieldsDel = $oAppDelegation->Load($sApplicationUID, $iDelegation);
        //and creates a new AppDelegation row with the same user, task, process, etc.
        $proUid = $aFieldsDel['PRO_UID'];
        $appUid = $aFieldsDel['APP_UID'];
        $tasUid = $aFieldsDel['TAS_UID'];
        $usrUid = $aFieldsDel['USR_UID'];
        $delThread = $aFieldsDel['DEL_THREAD'];
        $iIndex = $oAppDelegation->createAppDelegation($proUid, $appUid, $tasUid, $usrUid, $delThread);

        //update other fields in the recent new appDelegation
        $aData = array();
        $aData['APP_UID'] = $aFieldsDel['APP_UID'];
        $aData['DEL_INDEX'] = $iIndex;
        $aData['DEL_PREVIOUS'] = $aFieldsDel['DEL_PREVIOUS'];
        $aData['DEL_TYPE'] = $aFieldsDel['DEL_TYPE'];
        $aData['DEL_PRIORITY'] = $aFieldsDel['DEL_PRIORITY'];
        $aData['DEL_DELEGATE_DATE'] = $aFieldsDel['DEL_DELEGATE_DATE'];
        $aData['DEL_INIT_DATE'] = date('Y-m-d H:i:s');
        $aData['DEL_FINISH_DATE'] = null;
        $oAppDelegation->update($aData);

        //get the APP_DELAY row ( with app_uid, del_index and app_type=pause
        $oCriteria = new Criteria('workflow');
        $oCriteria->clearSelectColumns();
        $oCriteria->addSelectColumn(AppDelayPeer::APP_DELAY_UID);
        $oCriteria->addSelectColumn(AppDelayPeer::APP_THREAD_INDEX);
        $oCriteria->addSelectColumn(AppDelayPeer::APP_STATUS);
        $oCriteria->add(AppDelayPeer::APP_UID, $sApplicationUID);
        $oCriteria->add(AppDelayPeer::APP_DEL_INDEX, $iDelegation);
        $oCriteria->add(AppDelayPeer::APP_TYPE, 'PAUSE');
        $oCriteria->add(
                $oCriteria->getNewCriterion(
                                AppDelayPeer::APP_DISABLE_ACTION_USER, null, Criteria::ISNULL)->
                        addOr($oCriteria->getNewCriterion(AppDelayPeer::APP_DISABLE_ACTION_USER, 0))
        );
        $oDataset = AppDelayPeer::doSelectRS($oCriteria);
        $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $oDataset->next();
        $aRow = $oDataset->getRow();

        $oApplication = new Application();
        $aFields = $oApplication->Load($sApplicationUID);
        $aFields['APP_STATUS'] = $aRow['APP_STATUS'];
        $oApplication->update($aFields);

        //update the DEL_INDEX ? in APP_THREAD table?
        $aUpdate = array(
            'APP_UID' => $sApplicationUID,
            'APP_THREAD_INDEX' => $aRow['APP_THREAD_INDEX'],
            'DEL_INDEX' => $iIndex
        );
        $oAppThread = new AppThread();
        $oAppThread->update($aUpdate);

        $aData['APP_DELAY_UID'] = $aRow['APP_DELAY_UID'];
        $aData['APP_DISABLE_ACTION_USER'] = $sUserUID;
        $aData['APP_DISABLE_ACTION_DATE'] = date('Y-m-d H:i:s');
        $oAppDelay = new AppDelay();
        $aFieldsDelay = $oAppDelay->update($aData);

        //update searchindex
        if ($this->appSolr != null) {
            $this->appSolr->updateApplicationSearchIndex($sApplicationUID);
        }
    }

    /*
     * cancel a case
     *
     * @name cancelCase
     * @param string $sApplicationUID
     * @param string $iIndex
     * @param string $user_logged
     * @return void
     */

    public function cancelCase($sApplicationUID, $iIndex, $user_logged)
    {
        $this->getExecuteTriggerProcess($sApplicationUID, 'CANCELED');

        $oApplication = new Application();
        $aFields = $oApplication->load($sApplicationUID);
        $oCriteria = new Criteria('workflow');
        $oCriteria->add(AppDelegationPeer::APP_UID, $sApplicationUID);
        $oCriteria->add(AppDelegationPeer::DEL_FINISH_DATE, null, Criteria::ISNULL);
        if (AppDelegationPeer::doCount($oCriteria) == 1) {
            $aFields['APP_STATUS'] = 'CANCELLED';
            $oApplication->update($aFields);
        }
        $this->CloseCurrentDelegation($sApplicationUID, $iIndex);
        $oAppDel = new AppDelegation();
        $oAppDel->Load($sApplicationUID, $iIndex);
        $aAppDel = $oAppDel->toArray(BasePeer::TYPE_FIELDNAME);
        $this->closeAppThread($sApplicationUID, $aAppDel['DEL_THREAD']);

        $delay = new AppDelay();
        $array['PRO_UID'] = $aFields['PRO_UID'];
        $array['APP_UID'] = $sApplicationUID;

        $c = new Criteria('workflow');
        $c->clearSelectColumns();
        $c->addSelectColumn(AppThreadPeer::APP_THREAD_INDEX);
        $c->add(AppThreadPeer::APP_UID, $sApplicationUID);
        $c->add(AppThreadPeer::DEL_INDEX, $iIndex);
        $oDataset = AppThreadPeer::doSelectRS($c);
        $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $oDataset->next();
        $aRow = $oDataset->getRow();
        $array['APP_THREAD_INDEX'] = $aRow['APP_THREAD_INDEX'];
        $array['APP_DEL_INDEX'] = $iIndex;
        $array['APP_TYPE'] = 'CANCEL';

        $c = new Criteria('workflow');
        $c->clearSelectColumns();
        $c->addSelectColumn(ApplicationPeer::APP_STATUS);
        $c->add(ApplicationPeer::APP_UID, $sApplicationUID);
        $oDataset = ApplicationPeer::doSelectRS($c);
        $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $oDataset->next();
        $aRow1 = $oDataset->getRow();
        $array['APP_STATUS'] = $aRow1['APP_STATUS'];

        $array['APP_DELEGATION_USER'] = $user_logged;
        $array['APP_ENABLE_ACTION_USER'] = $user_logged;
        $array['APP_ENABLE_ACTION_DATE'] = date('Y-m-d H:i:s');
        $delay->create($array);

        //Before cancel a case verify if is a child case
        $oCriteria2 = new Criteria('workflow');
        $oCriteria2->add(SubApplicationPeer::APP_UID, $sApplicationUID);
        $oCriteria2->add(SubApplicationPeer::SA_STATUS, 'ACTIVE');
        if (SubApplicationPeer::doCount($oCriteria2) > 0) {
            G::LoadClass('derivation');
            $oDerivation = new Derivation();
            $oDerivation->verifyIsCaseChild($sApplicationUID);
        }

        //update searchindex
        if ($this->appSolr != null) {
            $this->appSolr->updateApplicationSearchIndex($sApplicationUID);
        }
    }

    /*
     * reactive a case
     *
     * @name reactivateCase
     * @param string $sApplicationUID
     * @param string $iIndex
     * @param string $user_logged
     * @return void
     */

    public function reactivateCase($sApplicationUID, $iIndex, $user_logged)
    {
        $oApplication = new Application();
        $aFields = $oApplication->load(
                (isset($_POST['sApplicationUID']) ? $_POST['sApplicationUID'] : $_SESSION['APPLICATION'])
        );
        $aFields['APP_STATUS'] = 'TO_DO';
        $oApplication->update($aFields);
        $this->ReactivateCurrentDelegation($sApplicationUID, $iIndex);
        $c = new Criteria('workflow');
        $c->clearSelectColumns();
        $c->addSelectColumn(AppDelayPeer::APP_DELAY_UID);

        $c->add(AppDelayPeer::APP_UID, $sApplicationUID);
        $c->add(AppDelayPeer::PRO_UID, $aFields['PRO_UID']);
        $c->add(AppDelayPeer::APP_DEL_INDEX, $iIndex);
        $c->add(AppDelayPeer::APP_TYPE, 'CANCEL');
        $c->add(AppDelayPeer::APP_DISABLE_ACTION_USER, 0);
        $c->add(AppDelayPeer::APP_DISABLE_ACTION_DATE, null, Criteria::ISNULL);

        $oDataset = AppDelayPeer::doSelectRS($c);
        $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $oDataset->next();
        $aRow = $oDataset->getRow();
        //var_dump($aRow);
        $aFields = array();
        $aFields['APP_DELAY_UID'] = $aRow['APP_DELAY_UID'];
        $aFields['APP_DISABLE_ACTION_USER'] = $user_logged;
        $aFields['APP_DISABLE_ACTION_DATE'] = date('Y-m-d H:i:s');

        $delay = new AppDelay();
        $delay->update($aFields);
        //$this->ReactivateCurrentDelegation($sApplicationUID);
        $con = Propel::getConnection('workflow');
        $sql = "UPDATE APP_THREAD SET APP_THREAD_STATUS = 'OPEN' WHERE APP_UID =  '$sApplicationUID' " .
                " AND DEL_INDEX  ='$iIndex' ";
        $stmt = $con->createStatement();
        $rs = $stmt->executeQuery($sql, ResultSet::FETCHMODE_ASSOC);

        //update searchindex
        if ($this->appSolr != null) {
            $this->appSolr->updateApplicationSearchIndex($sApplicationUID);
        }
    }

    /*
     * reassign a case
     *
     * @name reassignCase
     * @param string $sApplicationUID
     * @param string $iDelegation
     * @param string $sUserUID
     * @param string $newUserUID
     * @param string $sType
     * @return true
     */

    public function reassignCase($sApplicationUID, $iDelegation, $sUserUID, $newUserUID, $sType = 'REASSIGN')
    {
        $this->CloseCurrentDelegation($sApplicationUID, $iDelegation);
        $oAppDelegation = new AppDelegation();
        $aFieldsDel = $oAppDelegation->Load($sApplicationUID, $iDelegation);
        $iIndex = $oAppDelegation->createAppDelegation(
                $aFieldsDel['PRO_UID'], $aFieldsDel['APP_UID'], $aFieldsDel['TAS_UID'], $aFieldsDel['USR_UID'], $aFieldsDel['DEL_THREAD']
        );
        $aData = array();
        $aData['APP_UID'] = $aFieldsDel['APP_UID'];
        $aData['DEL_INDEX'] = $iIndex;
        $aData['DEL_PREVIOUS'] = $aFieldsDel['DEL_PREVIOUS'];
        $aData['DEL_TYPE'] = $aFieldsDel['DEL_TYPE'];
        $aData['DEL_PRIORITY'] = $aFieldsDel['DEL_PRIORITY'];
        $aData['DEL_DELEGATE_DATE'] = $aFieldsDel['DEL_DELEGATE_DATE'];
        $aData['USR_UID'] = $newUserUID;
        $aData['DEL_INIT_DATE'] = null;
        $aData['DEL_FINISH_DATE'] = null;
        $oAppDelegation->update($aData);
        $oAppThread = new AppThread();
        $oAppThread->update(
                array(
                    'APP_UID' => $sApplicationUID,
                    'APP_THREAD_INDEX' => $aFieldsDel['DEL_THREAD'],
                    'DEL_INDEX' => $iIndex)
        );

        //Save in APP_DELAY
        $oApplication = new Application();
        $aFields = $oApplication->Load($sApplicationUID);
        $aData['PRO_UID'] = $aFieldsDel['PRO_UID'];
        $aData['APP_UID'] = $sApplicationUID;
        $aData['APP_THREAD_INDEX'] = $aFieldsDel['DEL_THREAD'];
        $aData['APP_DEL_INDEX'] = $iDelegation;
        $aData['APP_TYPE'] = ($sType != '' ? $sType : 'REASSIGN');
        $aData['APP_STATUS'] = $aFields['APP_STATUS'];
        $aData['APP_DELEGATION_USER'] = $sUserUID;
        $aData['APP_ENABLE_ACTION_USER'] = $sUserUID;
        $aData['APP_ENABLE_ACTION_DATE'] = date('Y-m-d H:i:s');
        $oAppDelay = new AppDelay();
        $oAppDelay->create($aData);

        //update searchindex
        if ($this->appSolr != null) {
            $this->appSolr->updateApplicationSearchIndex($sApplicationUID);
        }

        $this->getExecuteTriggerProcess($sApplicationUID, 'REASSIGNED');
        return true;
    }

    /*
     * get all dynaforms that they have send it
     *
     * @name getAllDynaformsStepsToRevise
     * @param string $APP_UID
     * @return object
     */

    public function getAllDynaformsStepsToRevise($APP_UID)
    {
        $aCase = $this->loadCase($APP_UID);
        $oCriteria = new Criteria('workflow');
        $oCriteria->addSelectColumn(StepSupervisorPeer::STEP_UID);
        $oCriteria->addSelectColumn(StepSupervisorPeer::PRO_UID);
        $oCriteria->addSelectColumn(StepSupervisorPeer::STEP_TYPE_OBJ);
        $oCriteria->addSelectColumn(StepSupervisorPeer::STEP_UID_OBJ);
        $oCriteria->addSelectColumn(StepSupervisorPeer::STEP_POSITION);
        $oCriteria->add(StepSupervisorPeer::PRO_UID, $aCase['PRO_UID']);
        $oCriteria->add(StepSupervisorPeer::STEP_TYPE_OBJ, 'DYNAFORM');
        $oCriteria->addAscendingOrderByColumn(StepSupervisorPeer::STEP_POSITION);
        $oDataset = StepSupervisorPeer::doSelectRS($oCriteria);
        $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        return $oDataset;
    }

    /*
     * get all inputs that they have send it
     *
     * @name getAllInputsStepsToRevise
     * @param string $APP_UID
     * @return object
     */

    public function getAllInputsStepsToRevise($APP_UID)
    {
        $aCase = $this->loadCase($APP_UID);
        $oCriteria = new Criteria('workflow');
        $oCriteria->addSelectColumn(StepSupervisorPeer::STEP_UID);
        $oCriteria->addSelectColumn(StepSupervisorPeer::PRO_UID);
        $oCriteria->addSelectColumn(StepSupervisorPeer::STEP_TYPE_OBJ);
        $oCriteria->addSelectColumn(StepSupervisorPeer::STEP_UID_OBJ);
        $oCriteria->addSelectColumn(StepSupervisorPeer::STEP_POSITION);
        $oCriteria->add(StepSupervisorPeer::PRO_UID, $aCase['PRO_UID']);
        $oCriteria->add(StepSupervisorPeer::STEP_TYPE_OBJ, 'INPUT_DOCUMENT');
        $oCriteria->addAscendingOrderByColumn(StepSupervisorPeer::STEP_POSITION);
        $oDataset = StepSupervisorPeer::doSelectRS($oCriteria);
        $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        return $oDataset;
    }

    /*
     * get all upload document that they have send it
     *
     * @name getAllUploadedDocumentsCriteria
     * @param string $APP_UID
     * @return object
     */

    public function getAllUploadedDocumentsCriteria($sProcessUID, $sApplicationUID, $sTasKUID, $sUserUID)
    {
        //verifica si existe la tabla OBJECT_PERMISSION
        $this->verifyTable();
        $listing = false;
        $oPluginRegistry = & PMPluginRegistry::getSingleton();
        if ($oPluginRegistry->existsTrigger(PM_CASE_DOCUMENT_LIST)) {
            $folderData = new folderData(null, null, $sApplicationUID, null, $sUserUID);
            $folderData->PMType = "INPUT";
            $folderData->returnList = true;
            //$oPluginRegistry      = & PMPluginRegistry::getSingleton();
            $listing = $oPluginRegistry->executeTriggers(PM_CASE_DOCUMENT_LIST, $folderData);
        }

        $aObjectPermissions = $this->getAllObjects($sProcessUID, $sApplicationUID, $sTasKUID, $sUserUID);

        if (!is_array($aObjectPermissions)) {
            $aObjectPermissions = array(
                'DYNAFORMS' => array(-1),
                'INPUT_DOCUMENTS' => array(-1),
                'OUTPUT_DOCUMENTS' => array(-1)
            );
        }
        if (!isset($aObjectPermissions['DYNAFORMS'])) {
            $aObjectPermissions['DYNAFORMS'] = array(-1);
        } else {
            if (!is_array($aObjectPermissions['DYNAFORMS'])) {
                $aObjectPermissions['DYNAFORMS'] = array(-1);
            }
        }
        if (!isset($aObjectPermissions['INPUT_DOCUMENTS'])) {
            $aObjectPermissions['INPUT_DOCUMENTS'] = array(-1);
        } else {
            if (!is_array($aObjectPermissions['INPUT_DOCUMENTS'])) {
                $aObjectPermissions['INPUT_DOCUMENTS'] = array(-1);
            }
        }
        if (!isset($aObjectPermissions['OUTPUT_DOCUMENTS'])) {
            $aObjectPermissions['OUTPUT_DOCUMENTS'] = array(-1);
        } else {
            if (!is_array($aObjectPermissions['OUTPUT_DOCUMENTS'])) {
                $aObjectPermissions['OUTPUT_DOCUMENTS'] = array(-1);
            }
        }

        $aDelete = $this->getAllObjectsFrom($sProcessUID, $sApplicationUID, $sTasKUID, $sUserUID, 'DELETE');
        $oAppDocument = new AppDocument();
        $oCriteria = new Criteria('workflow');
        $oCriteria->add(AppDocumentPeer::APP_UID, $sApplicationUID);
        $oCriteria->add(AppDocumentPeer::APP_DOC_TYPE, array('INPUT'), Criteria::IN);
        $oCriteria->add(AppDocumentPeer::APP_DOC_STATUS, array('ACTIVE'), Criteria::IN);
        //$oCriteria->add(AppDocumentPeer::APP_DOC_UID, $aObjectPermissions['INPUT_DOCUMENTS'], Criteria::IN);
        $oCriteria->add(
                $oCriteria->getNewCriterion(
                                AppDocumentPeer::APP_DOC_UID, $aObjectPermissions['INPUT_DOCUMENTS'], Criteria::IN)->
                        addOr($oCriteria->getNewCriterion(AppDocumentPeer::USR_UID, array($sUserUID, '-1'), Criteria::IN))
        );

        $aConditions = array();
        $aConditions[] = array(AppDocumentPeer::APP_UID, AppDelegationPeer::APP_UID);
        $aConditions[] = array(AppDocumentPeer::DEL_INDEX, AppDelegationPeer::DEL_INDEX);
        $oCriteria->addJoinMC($aConditions, Criteria::LEFT_JOIN);
        $oCriteria->add(AppDelegationPeer::PRO_UID, $sProcessUID);
        $oCriteria->addAscendingOrderByColumn(AppDocumentPeer::APP_DOC_INDEX);
        $oDataset = AppDocumentPeer::doSelectRS($oCriteria);
        $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $oDataset->next();
        $aInputDocuments = array();
        $aInputDocuments[] = array(
            'APP_DOC_UID' => 'char',
            'DOC_UID' => 'char',
            'APP_DOC_COMMENT' => 'char',
            'APP_DOC_FILENAME' => 'char', 'APP_DOC_INDEX' => 'integer'
        );
        $oUser = new Users();
        while ($aRow = $oDataset->getRow()) {
            $oCriteria2 = new Criteria('workflow');
            $oCriteria2->add(AppDelegationPeer::APP_UID, $sApplicationUID);
            $oCriteria2->add(AppDelegationPeer::DEL_INDEX, $aRow['DEL_INDEX']);
            $oDataset2 = AppDelegationPeer::doSelectRS($oCriteria2);
            $oDataset2->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $oDataset2->next();
            $aRow2 = $oDataset2->getRow();
            $oTask = new Task();
            if ($oTask->taskExists($aRow2['TAS_UID'])) {
                $aTask = $oTask->load($aRow2['TAS_UID']);
            } else {
                $aTask = array('TAS_TITLE' => '(TASK DELETED)');
            }
            $aAux = $oAppDocument->load($aRow['APP_DOC_UID'], $aRow['DOC_VERSION']);
            $lastVersion = $oAppDocument->getLastAppDocVersion($aRow['APP_DOC_UID'], $sApplicationUID);
            try {
                $aAux1 = $oUser->load($aAux['USR_UID']);
                $sUser = $aAux1['USR_FIRSTNAME'] . ' ' . $aAux1['USR_LASTNAME'];
            } catch (Exception $oException) {
                //$sUser = '(USER DELETED)';
                $sUser = '***';
            }
            $aFields = array(
                'APP_DOC_UID' => $aAux['APP_DOC_UID'],
                'DOC_UID' => $aAux['DOC_UID'],
                'APP_DOC_COMMENT' => $aAux['APP_DOC_COMMENT'],
                'APP_DOC_FILENAME' => $aAux['APP_DOC_FILENAME'],
                'APP_DOC_INDEX' => $aAux['APP_DOC_INDEX'],
                'TYPE' => $aAux['APP_DOC_TYPE'],
                'ORIGIN' => $aTask['TAS_TITLE'],
                'CREATE_DATE' => $aAux['APP_DOC_CREATE_DATE'],
                'CREATED_BY' => $sUser
            );
            if ($aFields['APP_DOC_FILENAME'] != '') {
                $aFields['TITLE'] = $aFields['APP_DOC_FILENAME'];
            } else {
                $aFields['TITLE'] = $aFields['APP_DOC_COMMENT'];
            }
            $aFields['POSITION'] = $_SESSION['STEP_POSITION'];
            $aFields['CONFIRM'] = G::LoadTranslation('ID_CONFIRM_DELETE_ELEMENT');
            if (in_array($aRow['APP_DOC_UID'], $aDelete['INPUT_DOCUMENTS'])) {
                $aFields['ID_DELETE'] = G::LoadTranslation('ID_DELETE');
            }

            $aFields['DOWNLOAD_LABEL'] = G::LoadTranslation('ID_DOWNLOAD');
            $aFields['DOWNLOAD_LINK'] = "cases_ShowDocument?a=" . $aRow['APP_DOC_UID'] . "&v=" . $aRow['DOC_VERSION'];
            $aFields['DOC_VERSION'] = $aRow['DOC_VERSION'];
            if (is_array($listing)) {
                foreach ($listing as $folderitem) {
                    if ($folderitem->filename == $aRow['APP_DOC_UID']) {
                        $aFields['DOWNLOAD_LABEL'] = G::LoadTranslation('ID_GET_EXTERNAL_FILE');
                        $aFields['DOWNLOAD_LINK'] = $folderitem->downloadScript;
                        continue;
                    }
                }
            }
            if ($lastVersion == $aRow['DOC_VERSION']) {
                //Show only last version
                $aInputDocuments[] = $aFields;
            }
            $oDataset->next();
        }
        $oAppDocument = new AppDocument();
        $oCriteria = new Criteria('workflow');
        $oCriteria->add(AppDocumentPeer::APP_UID, $sApplicationUID);
        $oCriteria->add(AppDocumentPeer::APP_DOC_TYPE, array('ATTACHED'), Criteria::IN);
        $oCriteria->add(AppDocumentPeer::APP_DOC_STATUS, array('ACTIVE'), Criteria::IN);

        $oCriteria->add(
                $oCriteria->getNewCriterion(
                                AppDocumentPeer::APP_DOC_UID, $aObjectPermissions['INPUT_DOCUMENTS'], Criteria::IN
                        )->
                        addOr($oCriteria->getNewCriterion(AppDocumentPeer::USR_UID, array($sUserUID, '-1'), Criteria::IN)));

        $aConditions = array();
        $aConditions[] = array(AppDocumentPeer::APP_UID, AppDelegationPeer::APP_UID);
        $aConditions[] = array(AppDocumentPeer::DEL_INDEX, AppDelegationPeer::DEL_INDEX);
        $oCriteria->addJoinMC($aConditions, Criteria::LEFT_JOIN);
        $oCriteria->add(AppDelegationPeer::PRO_UID, $sProcessUID);
        $oCriteria->addAscendingOrderByColumn(AppDocumentPeer::APP_DOC_INDEX);
        $oDataset = AppDocumentPeer::doSelectRS($oCriteria);
        $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $oDataset->next();
        while ($aRow = $oDataset->getRow()) {
            $oCriteria2 = new Criteria('workflow');
            $oCriteria2->add(AppDelegationPeer::APP_UID, $sApplicationUID);
            $oCriteria2->add(AppDelegationPeer::DEL_INDEX, $aRow['DEL_INDEX']);
            $oDataset2 = AppDelegationPeer::doSelectRS($oCriteria2);
            $oDataset2->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $oDataset2->next();
            $aRow2 = $oDataset2->getRow();
            $oTask = new Task();
            if ($oTask->taskExists($aRow2['TAS_UID'])) {
                $aTask = $oTask->load($aRow2['TAS_UID']);
            } else {
                $aTask = array('TAS_TITLE' => '(TASK DELETED)');
            }
            $aAux = $oAppDocument->load($aRow['APP_DOC_UID'], $aRow['DOC_VERSION']);
            $lastVersion = $oAppDocument->getLastAppDocVersion($aRow['APP_DOC_UID'], $sApplicationUID);
            try {
                $aAux1 = $oUser->load($aAux['USR_UID']);
                $sUser = $aAux1['USR_FIRSTNAME'] . ' ' . $aAux1['USR_LASTNAME'];
            } catch (Exception $oException) {
                $sUser = '***';
            }
            $aFields = array(
                'APP_DOC_UID' => $aAux['APP_DOC_UID'],
                'DOC_UID' => $aAux['DOC_UID'],
                'APP_DOC_COMMENT' => $aAux['APP_DOC_COMMENT'],
                'APP_DOC_FILENAME' => $aAux['APP_DOC_FILENAME'],
                'APP_DOC_INDEX' => $aAux['APP_DOC_INDEX'],
                'TYPE' => $aAux['APP_DOC_TYPE'],
                'ORIGIN' => $aTask['TAS_TITLE'],
                'CREATE_DATE' => $aAux['APP_DOC_CREATE_DATE'],
                'CREATED_BY' => $sUser
            );
            if ($aFields['APP_DOC_FILENAME'] != '') {
                $aFields['TITLE'] = $aFields['APP_DOC_FILENAME'];
            } else {
                $aFields['TITLE'] = $aFields['APP_DOC_COMMENT'];
            }
            $aFields['POSITION'] = $_SESSION['STEP_POSITION'];

            $aFields['CONFIRM'] = G::LoadTranslation('ID_CONFIRM_DELETE_ELEMENT');
            if (in_array($aRow['APP_DOC_UID'], $aDelete['INPUT_DOCUMENTS'])) {
                $aFields['ID_DELETE'] = G::LoadTranslation('ID_DELETE');
            }

            $aFields['DOWNLOAD_LABEL'] = G::LoadTranslation('ID_DOWNLOAD');
            $aFields['DOWNLOAD_LINK'] = "cases_ShowDocument?a=" . $aRow['APP_DOC_UID'];
            if ($lastVersion == $aRow['DOC_VERSION']) {
                //Show only last version
                $aInputDocuments[] = $aFields;
            }
            $oDataset->next();
        }
        // Get input documents added/modified by a supervisor - Begin
        $oAppDocument = new AppDocument();
        $oCriteria = new Criteria('workflow');
        $oCriteria->add(AppDocumentPeer::APP_UID, $sApplicationUID);
        $oCriteria->add(AppDocumentPeer::APP_DOC_TYPE, array('INPUT'), Criteria::IN);
        $oCriteria->add(AppDocumentPeer::APP_DOC_STATUS, array('ACTIVE'), Criteria::IN);
        $oCriteria->add(AppDocumentPeer::DEL_INDEX, 100000);

        $oCriteria->add(
                $oCriteria->getNewCriterion(
                                AppDocumentPeer::APP_DOC_UID, $aObjectPermissions['INPUT_DOCUMENTS'], Criteria::IN
                        )->
                        addOr($oCriteria->getNewCriterion(AppDocumentPeer::USR_UID, array($sUserUID, '-1'), Criteria::IN)));

        $oCriteria->addJoin(AppDocumentPeer::APP_UID, ApplicationPeer::APP_UID, Criteria::LEFT_JOIN);
        $oCriteria->add(ApplicationPeer::PRO_UID, $sProcessUID);
        $oCriteria->addAscendingOrderByColumn(AppDocumentPeer::APP_DOC_INDEX);
        $oDataset = AppDocumentPeer::doSelectRS($oCriteria);
        $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $oDataset->next();
        $oUser = new Users();
        while ($aRow = $oDataset->getRow()) {
            $aTask = array('TAS_TITLE' => '[ ' . G::LoadTranslation('ID_SUPERVISOR') . ' ]');
            $aAux = $oAppDocument->load($aRow['APP_DOC_UID'], $aRow['DOC_VERSION']);
            $lastVersion = $oAppDocument->getLastAppDocVersion($aRow['APP_DOC_UID'], $sApplicationUID);
            try {
                $aAux1 = $oUser->load($aAux['USR_UID']);
                $sUser = $aAux1['USR_FIRSTNAME'] . ' ' . $aAux1['USR_LASTNAME'];
            } catch (Exception $oException) {
                $sUser = '***';
            }
            $aFields = array(
                'APP_DOC_UID' => $aAux['APP_DOC_UID'],
                'DOC_UID' => $aAux['DOC_UID'],
                'APP_DOC_COMMENT' => $aAux['APP_DOC_COMMENT'],
                'APP_DOC_FILENAME' => $aAux['APP_DOC_FILENAME'],
                'APP_DOC_INDEX' => $aAux['APP_DOC_INDEX'],
                'TYPE' => $aAux['APP_DOC_TYPE'],
                'ORIGIN' => $aTask['TAS_TITLE'],
                'CREATE_DATE' => $aAux['APP_DOC_CREATE_DATE'],
                'CREATED_BY' => $sUser
            );
            if ($aFields['APP_DOC_FILENAME'] != '') {
                $aFields['TITLE'] = $aFields['APP_DOC_FILENAME'];
            } else {
                $aFields['TITLE'] = $aFields['APP_DOC_COMMENT'];
            }
            $aFields['POSITION'] = $_SESSION['STEP_POSITION'];
            $aFields['CONFIRM'] = G::LoadTranslation('ID_CONFIRM_DELETE_ELEMENT');
            if (in_array($aRow['APP_DOC_UID'], $aDelete['INPUT_DOCUMENTS'])) {
                $aFields['ID_DELETE'] = G::LoadTranslation('ID_DELETE');
            }

            $aFields['DOWNLOAD_LABEL'] = G::LoadTranslation('ID_DOWNLOAD');
            $aFields['DOWNLOAD_LINK'] = "cases_ShowDocument?a=" . $aRow['APP_DOC_UID'] . "&v=" . $aRow['DOC_VERSION'];
            $aFields['DOC_VERSION'] = $aRow['DOC_VERSION'];
            if (is_array($listing)) {
                foreach ($listing as $folderitem) {
                    if ($folderitem->filename == $aRow['APP_DOC_UID']) {
                        $aFields['DOWNLOAD_LABEL'] = G::LoadTranslation('ID_GET_EXTERNAL_FILE');
                        $aFields['DOWNLOAD_LINK'] = $folderitem->downloadScript;
                        continue;
                    }
                }
            }
            if ($lastVersion == $aRow['DOC_VERSION']) {
                //Show only last version
                $aInputDocuments[] = $aFields;
            }
            $oDataset->next();
        }
        // Get input documents added/modified by a supervisor - End
        global $_DBArray;
        $_DBArray['inputDocuments'] = $aInputDocuments;
        $_SESSION['_DBArray'] = $_DBArray;
        G::LoadClass('ArrayPeer');
        $oCriteria = new Criteria('dbarray');
        $oCriteria->setDBArrayTable('inputDocuments');
        $oCriteria->addDescendingOrderByColumn('CREATE_DATE');
        return $oCriteria;
    }

    /*
     * get all generate document
     *
     * @name getAllGeneratedDocumentsCriteria
     * @param string $sProcessUID
     * @param string $sApplicationUID
     * @param string $sTasKUID
     * @param string $sUserUID
     * @return object
     */

    public function getAllGeneratedDocumentsCriteria($sProcessUID, $sApplicationUID, $sTasKUID, $sUserUID)
    {
        //verifica si la tabla OBJECT_PERMISSION
        $this->verifyTable();
        $listing = false;
        $oPluginRegistry = & PMPluginRegistry::getSingleton();
        if ($oPluginRegistry->existsTrigger(PM_CASE_DOCUMENT_LIST)) {
            $folderData = new folderData(null, null, $sApplicationUID, null, $sUserUID);
            $folderData->PMType = "OUTPUT";
            $folderData->returnList = true;
            //$oPluginRegistry = & PMPluginRegistry::getSingleton();
            $listing = $oPluginRegistry->executeTriggers(PM_CASE_DOCUMENT_LIST, $folderData);
        }

        $aObjectPermissions = $this->getAllObjects($sProcessUID, $sApplicationUID, $sTasKUID, $sUserUID);
        if (!is_array($aObjectPermissions)) {
            $aObjectPermissions = array(
                'DYNAFORMS' => array(-1),
                'INPUT_DOCUMENTS' => array(-1),
                'OUTPUT_DOCUMENTS' => array(-1)
            );
        }
        if (!isset($aObjectPermissions['DYNAFORMS'])) {
            $aObjectPermissions['DYNAFORMS'] = array(-1);
        } else {
            if (!is_array($aObjectPermissions['DYNAFORMS'])) {
                $aObjectPermissions['DYNAFORMS'] = array(-1);
            }
        }
        if (!isset($aObjectPermissions['INPUT_DOCUMENTS'])) {
            $aObjectPermissions['INPUT_DOCUMENTS'] = array(-1);
        } else {
            if (!is_array($aObjectPermissions['INPUT_DOCUMENTS'])) {
                $aObjectPermissions['INPUT_DOCUMENTS'] = array(-1);
            }
        }
        if (!isset($aObjectPermissions['OUTPUT_DOCUMENTS'])) {
            $aObjectPermissions['OUTPUT_DOCUMENTS'] = array(-1);
        } else {
            if (!is_array($aObjectPermissions['OUTPUT_DOCUMENTS'])) {
                $aObjectPermissions['OUTPUT_DOCUMENTS'] = array(-1);
            }
        }
        $aDelete = $this->getAllObjectsFrom($sProcessUID, $sApplicationUID, $sTasKUID, $sUserUID, 'DELETE');
        $oAppDocument = new AppDocument();
        $oCriteria = new Criteria('workflow');
        $oCriteria->add(AppDocumentPeer::APP_UID, $sApplicationUID);
        $oCriteria->add(AppDocumentPeer::APP_DOC_TYPE, 'OUTPUT');
        $oCriteria->add(AppDocumentPeer::APP_DOC_STATUS, array('ACTIVE'), Criteria::IN);
        //$oCriteria->add(AppDocumentPeer::APP_DOC_UID, $aObjectPermissions['OUTPUT_DOCUMENTS'], Criteria::IN);
        $oCriteria->add(
                $oCriteria->getNewCriterion(
                        AppDocumentPeer::APP_DOC_UID, $aObjectPermissions['OUTPUT_DOCUMENTS'], Criteria::IN)->addOr($oCriteria->getNewCriterion(AppDocumentPeer::USR_UID, $sUserUID, Criteria::EQUAL))
        );

        $aConditions = array();
        $aConditions[] = array(AppDocumentPeer::APP_UID, AppDelegationPeer::APP_UID);
        $aConditions[] = array(AppDocumentPeer::DEL_INDEX, AppDelegationPeer::DEL_INDEX);
        $oCriteria->addJoinMC($aConditions, Criteria::LEFT_JOIN);
        $oCriteria->add(AppDelegationPeer::PRO_UID, $sProcessUID);
        $oCriteria->addAscendingOrderByColumn(AppDocumentPeer::APP_DOC_INDEX);
        $oDataset = AppDocumentPeer::doSelectRS($oCriteria);
        $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $oDataset->next();
        $aOutputDocuments = array();
        $aOutputDocuments[] = array(
            'APP_DOC_UID' => 'char',
            'DOC_UID' => 'char',
            'APP_DOC_COMMENT' => 'char',
            'APP_DOC_FILENAME' => 'char',
            'APP_DOC_INDEX' => 'integer'
        );
        $oUser = new Users();
        while ($aRow = $oDataset->getRow()) {
            $oCriteria2 = new Criteria('workflow');
            $oCriteria2->add(AppDelegationPeer::APP_UID, $sApplicationUID);
            $oCriteria2->add(AppDelegationPeer::DEL_INDEX, $aRow['DEL_INDEX']);
            $oDataset2 = AppDelegationPeer::doSelectRS($oCriteria2);
            $oDataset2->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $oDataset2->next();
            $aRow2 = $oDataset2->getRow();
            $oTask = new Task();
            if ($oTask->taskExists($aRow2['TAS_UID'])) {
                $aTask = $oTask->load($aRow2['TAS_UID']);
            } else {
                $aTask = array('TAS_TITLE' => '(TASK DELETED)');
            }
            $lastVersion = $oAppDocument->getLastDocVersion($aRow['DOC_UID'], $sApplicationUID);
            if ($lastVersion == $aRow['DOC_VERSION']) {
                //Only show last document Version
                $aAux = $oAppDocument->load($aRow['APP_DOC_UID'], $aRow['DOC_VERSION']);
                //Get output Document information
                $oOutputDocument = new OutputDocument();
                $aGields = $oOutputDocument->load($aRow['DOC_UID']);
                //OUTPUTDOCUMENT
                $outDocTitle = $aGields['OUT_DOC_TITLE'];
                switch ($aGields['OUT_DOC_GENERATE']) {
                    //G::LoadTranslation(ID_DOWNLOAD)
                    case "PDF":
                        $fileDoc = 'javascript:alert("NO DOC")';
                        $fileDocLabel = " ";
                        $filePdf = 'cases_ShowOutputDocument?a=' .
                                $aRow['APP_DOC_UID'] . '&v=' . $aRow['DOC_VERSION'] . '&ext=pdf&random=' . rand();
                        $filePdfLabel = ".pdf";
                        if (is_array($listing)) {
                            foreach ($listing as $folderitem) {
                                if (($folderitem->filename == $aRow['APP_DOC_UID']) && ($folderitem->type == "PDF")) {
                                    $filePdfLabel = G::LoadTranslation('ID_GET_EXTERNAL_FILE') . " .pdf";
                                    $filePdf = $folderitem->downloadScript;
                                    continue;
                                }
                            }
                        }
                        break;
                    case "DOC":
                        $fileDoc = 'cases_ShowOutputDocument?a=' .
                                $aRow['APP_DOC_UID'] . '&v=' . $aRow['DOC_VERSION'] . '&ext=doc&random=' . rand();
                        $fileDocLabel = ".doc";
                        $filePdf = 'javascript:alert("NO PDF")';
                        $filePdfLabel = " ";
                        if (is_array($listing)) {
                            foreach ($listing as $folderitem) {
                                if (($folderitem->filename == $aRow['APP_DOC_UID']) && ($folderitem->type == "DOC")) {
                                    $fileDocLabel = G::LoadTranslation('ID_GET_EXTERNAL_FILE') . " .doc";
                                    $fileDoc = $folderitem->downloadScript;
                                    continue;
                                }
                            }
                        }
                        break;
                    case "BOTH":
                        $fileDoc = 'cases_ShowOutputDocument?a=' .
                                $aRow['APP_DOC_UID'] . '&v=' . $aRow['DOC_VERSION'] . '&ext=doc&random=' . rand();
                        $fileDocLabel = ".doc";
                        if (is_array($listing)) {
                            foreach ($listing as $folderitem) {
                                if (($folderitem->filename == $aRow['APP_DOC_UID']) && ($folderitem->type == "DOC")) {
                                    $fileDocLabel = G::LoadTranslation('ID_GET_EXTERNAL_FILE') . " .doc";
                                    $fileDoc = $folderitem->downloadScript;
                                    continue;
                                }
                            }
                        }
                        $filePdf = 'cases_ShowOutputDocument?a=' .
                                $aRow['APP_DOC_UID'] . '&v=' . $aRow['DOC_VERSION'] . '&ext=pdf&random=' . rand();
                        $filePdfLabel = ".pdf";

                        if (is_array($listing)) {
                            foreach ($listing as $folderitem) {
                                if (($folderitem->filename == $aRow['APP_DOC_UID']) && ($folderitem->type == "PDF")) {
                                    $filePdfLabel = G::LoadTranslation('ID_GET_EXTERNAL_FILE') . " .pdf";
                                    $filePdf = $folderitem->downloadScript;
                                    continue;
                                }
                            }
                        }
                        break;
                }
                try {
                    $aAux1 = $oUser->load($aAux['USR_UID']);
                    $sUser = $aAux1['USR_FIRSTNAME'] . ' ' . $aAux1['USR_LASTNAME'];
                } catch (Exception $oException) {
                    $sUser = '(USER DELETED)';
                }

                //if both documents were generated, we choose the pdf one, only if doc was
                //generate then choose the doc file.
                $firstDocLink = $filePdf;
                $firstDocLabel = $filePdfLabel;
                if ($aGields['OUT_DOC_GENERATE'] == 'DOC') {
                    $firstDocLink = $fileDoc;
                    $firstDocLabel = $fileDocLabel;
                }

                $aFields = array(
                    'APP_DOC_UID' => $aAux['APP_DOC_UID'],
                    'DOC_UID' => $aAux['DOC_UID'],
                    'APP_DOC_COMMENT' => $aAux['APP_DOC_COMMENT'],
                    'APP_DOC_FILENAME' => $aAux['APP_DOC_FILENAME'],
                    'APP_DOC_INDEX' => $aAux['APP_DOC_INDEX'],
                    'ORIGIN' => $aTask['TAS_TITLE'],
                    'CREATE_DATE' => $aAux['APP_DOC_CREATE_DATE'],
                    'CREATED_BY' => $sUser,
                    'FILEDOC' => $fileDoc,
                    'FILEPDF' => $filePdf,
                    'OUTDOCTITLE' => $outDocTitle,
                    'DOC_VERSION' => $aAux['DOC_VERSION'],
                    'TYPE' => $aAux['APP_DOC_TYPE'] . ' ' . $aGields['OUT_DOC_GENERATE'],
                    'DOWNLOAD_LINK' => $firstDocLink,
                    'DOWNLOAD_FILE' => $aAux['APP_DOC_FILENAME'] . $firstDocLabel
                );

                if (trim($fileDocLabel) != '') {
                    $aFields['FILEDOCLABEL'] = $fileDocLabel;
                }
                if (trim($filePdfLabel) != '') {
                    $aFields['FILEPDFLABEL'] = $filePdfLabel;
                }
                if ($aFields['APP_DOC_FILENAME'] != '') {
                    $aFields['TITLE'] = $aFields['APP_DOC_FILENAME'];
                } else {
                    $aFields['TITLE'] = $aFields['APP_DOC_COMMENT'];
                }
                $aFields['POSITION'] = $_SESSION['STEP_POSITION'];
                $aFields['CONFIRM'] = G::LoadTranslation('ID_CONFIRM_DELETE_ELEMENT');
                if (in_array($aRow['APP_DOC_UID'], $aObjectPermissions['OUTPUT_DOCUMENTS'])) {
                    if (in_array($aRow['APP_DOC_UID'], $aDelete['OUTPUT_DOCUMENTS'])) {
                        $aFields['ID_DELETE'] = G::LoadTranslation('ID_DELETE');
                    }
                }
                $aOutputDocuments[] = $aFields;
            }
            $oDataset->next();
        }
        global $_DBArray;
        $_DBArray['outputDocuments'] = $aOutputDocuments;
        $_SESSION['_DBArray'] = $_DBArray;
        G::LoadClass('ArrayPeer');
        $oCriteria = new Criteria('dbarray');
        $oCriteria->setDBArrayTable('outputDocuments');
        $oCriteria->addDescendingOrderByColumn('CREATE_DATE');
        return $oCriteria;
    }

    /*
     * get all dynaforms in a task
     *
     * @name getallDynaformsCriteria
     * @param string $sProcessUID
     * @param string $sApplicationUID
     * @param string $sTasKUID
     * @param string $sUserUID
     * @return object
     */

    public function getallDynaformsCriteria($sProcessUID, $sApplicationUID, $sTasKUID, $sUserUID)
    {
        //check OBJECT_PERMISSION table
        $this->verifyTable();

        $aObjectPermissions = $this->getAllObjects($sProcessUID, $sApplicationUID, $sTasKUID, $sUserUID);
        if (!is_array($aObjectPermissions)) {
            $aObjectPermissions = array(
                'DYNAFORMS' => array(-1),
                'INPUT_DOCUMENTS' => array(-1),
                'OUTPUT_DOCUMENTS' => array(-1)
            );
        }
        if (!isset($aObjectPermissions['DYNAFORMS'])) {
            $aObjectPermissions['DYNAFORMS'] = array(-1);
        } else {
            if (!is_array($aObjectPermissions['DYNAFORMS'])) {
                $aObjectPermissions['DYNAFORMS'] = array(-1);
            }
        }
        if (!isset($aObjectPermissions['INPUT_DOCUMENTS'])) {
            $aObjectPermissions['INPUT_DOCUMENTS'] = array(-1);
        } else {
            if (!is_array($aObjectPermissions['INPUT_DOCUMENTS'])) {
                $aObjectPermissions['INPUT_DOCUMENTS'] = array(-1);
            }
        }
        if (!isset($aObjectPermissions['OUTPUT_DOCUMENTS'])) {
            $aObjectPermissions['OUTPUT_DOCUMENTS'] = array(-1);
        } else {
            if (!is_array($aObjectPermissions['OUTPUT_DOCUMENTS'])) {
                $aObjectPermissions['OUTPUT_DOCUMENTS'] = array(-1);
            }
        }
        $oCriteria = new Criteria('workflow');
        $oCriteria->add(ApplicationPeer::APP_UID, $sApplicationUID);
        $oCriteria->addJoin(ApplicationPeer::PRO_UID, StepPeer::PRO_UID);
        $oCriteria->addJoin(StepPeer::STEP_UID_OBJ, DynaformPeer::DYN_UID);
        $oCriteria->add(StepPeer::STEP_TYPE_OBJ, 'DYNAFORM');
        $oCriteria->add(StepPeer::STEP_UID_OBJ, $aObjectPermissions['DYNAFORMS'], Criteria::IN);

        //These fields are missing now is completed
        $oCriteria->addSelectColumn(DynaformPeer::DYN_UID);
        $oCriteria->addSelectColumn(DynaformPeer::DYN_TYPE);
        $oCriteria->addSelectColumn(DynaformPeer::DYN_FILENAME);
        $oCriteria->addSelectColumn(ApplicationPeer::PRO_UID);
        ///-- Adding column STEP_POSITION for standardization
        $oCriteria->addSelectColumn(StepPeer::STEP_POSITION);

        $oCriteria->addAscendingOrderByColumn(StepPeer::STEP_POSITION);
        $oCriteria->setDistinct();


        $oDataset = DynaformPeer::doSelectRS($oCriteria);
        $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $oDataset->next();
        $aInputDocuments = array();
        $aInputDocuments[] = array(
            'DYN_TITLE' => 'char'
        );

        while ($aRow = $oDataset->getRow()) {
            $o = new Dynaform();
            $o->setDynUid($aRow['DYN_UID']);
            $aFields['DYN_TITLE'] = $o->getDynTitle();
            $aFields['DYN_UID'] = $aRow['DYN_UID'];
            $aFields['EDIT'] = G::LoadTranslation('ID_EDIT');
            $aFields['PRO_UID'] = $sProcessUID;
            $aFields['APP_UID'] = $sApplicationUID;
            $aFields['TAS_UID'] = $sTasKUID;
            $aInputDocuments[] = $aFields;
            $oDataset->next();
        }

        $distinctArray = $aInputDocuments;
        $distinctArrayBase = $aInputDocuments;
        $distinctOriginal = array();
        foreach ($distinctArray as $distinctArrayKey => $distinctArrayValue) {
            $distinctOriginalPush = 1;
            foreach ($distinctOriginal as $distinctOriginalKey => $distinctOriginalValue) {
                if ($distinctArrayValue == $distinctOriginalValue) {
                    $distinctOriginalPush = 0;
                }
            }
            if ($distinctOriginalPush == 1) {
                $distinctOriginal[] = $distinctArrayValue;
            }
        }
        $aInputDocuments = $distinctOriginal;

        global $_DBArray;
        $_DBArray['Dynaforms'] = $aInputDocuments;
        $_SESSION['_DBArray'] = $_DBArray;
        G::LoadClass('ArrayPeer');
        $oCriteria = new Criteria('dbarray');
        $oCriteria->setDBArrayTable('Dynaforms');
        $oCriteria->setDistinct();
        //$oCriteria->addAscendingOrderByColumn(AppDocumentPeer::APP_DOC_CREATE_DATE);
        return $oCriteria;
    }

    /*
     * This function sends notifications in a task
     *
     * @name sendNotifications
     * @param string $sCurrentTask
     * @param array $aTasks
     * @param array $aFields
     * @param string $sApplicationUID
     * @param string $iDelegation
     * @param string $sFrom
     * @return void
     */

    public function sendNotifications($sCurrentTask, $aTasks, $aFields, $sApplicationUID, $iDelegation, $sFrom = "")
    {
        try {
            $applicationData = $this->loadCase($sApplicationUID);
            $aFields["APP_NUMBER"] = $applicationData["APP_NUMBER"];

            $oConfiguration = new Configuration();
            $sDelimiter = DBAdapter::getStringDelimiter();
            $oCriteria = new Criteria('workflow');
            $oCriteria->add(ConfigurationPeer::CFG_UID, 'Emails');
            $oCriteria->add(ConfigurationPeer::OBJ_UID, '');
            $oCriteria->add(ConfigurationPeer::PRO_UID, '');
            $oCriteria->add(ConfigurationPeer::USR_UID, '');
            $oCriteria->add(ConfigurationPeer::APP_UID, '');
            if (ConfigurationPeer::doCount($oCriteria) == 0) {
                $oConfiguration->create(array(
                    'CFG_UID' => 'Emails',
                    'OBJ_UID' => '',
                    'CFG_VALUE' => '',
                    'PRO_UID' => '',
                    'USR_UID' => '',
                    'APP_UID' => ''
                ));
                $aConfiguration = array();
            } else {
                $aConfiguration = $oConfiguration->load('Emails', '', '', '', '');
                if ($aConfiguration['CFG_VALUE'] != '') {
                    $aConfiguration = unserialize($aConfiguration["CFG_VALUE"]);
                    $passwd = $aConfiguration["MESS_PASSWORD"];
                    $passwdDec = G::decrypt($passwd, "EMAILENCRYPT");
                    $auxPass = explode('hash:', $passwdDec);
                    if (count($auxPass) > 1) {
                        if (count($auxPass) == 2) {
                            $passwd = $auxPass[1];
                        } else {
                            array_shift($auxPass);
                            $passwd = implode('', $auxPass);
                        }
                    }
                    $aConfiguration["MESS_PASSWORD"] = $passwd;
                } else {
                    $aConfiguration = array();
                }
            }

            if (!isset($aConfiguration['MESS_ENABLED']) || $aConfiguration['MESS_ENABLED'] != '1') {
                return false;
            }

            //Send derivation notification - Start
            $oTask = new Task();
            $aTaskInfo = $oTask->load($sCurrentTask);

            if ($aTaskInfo['TAS_SEND_LAST_EMAIL'] != 'TRUE') {
                return false;
            }

            if ($sFrom == '') {
                $sFrom = '"ProcessMaker"';
            }

            $hasEmailFrom = preg_match('/(.+)@(.+)\.(.+)/', $sFrom, $match);

            if (!$hasEmailFrom || strpos($sFrom, $aConfiguration['MESS_ACCOUNT']) === false) {
                if (($aConfiguration['MESS_ENGINE'] != 'MAIL') && ($aConfiguration['MESS_ACCOUNT'] != '')) {
                    $sFrom .= ' <' . $aConfiguration['MESS_ACCOUNT'] . '>';
                } else {
                    if (($aConfiguration['MESS_ENGINE'] == 'MAIL')) {
                        $sFrom .= ' <info@' . gethostbyaddr('127.0.0.1') . '>';
                    } else {
                        if ($aConfiguration['MESS_SERVER'] != '') {
                            if (($sAux = @gethostbyaddr($aConfiguration['MESS_SERVER']))) {
                                $sFrom .= ' <info@' . $sAux . '>';
                            } else {
                                $sFrom .= ' <info@' . $aConfiguration['MESS_SERVER'] . '>';
                            }
                        } else {
                            $sFrom .= ' <info@processmaker.com>';
                        }
                    }
                }
            }

            if (isset($aTaskInfo['TAS_DEF_SUBJECT_MESSAGE']) && $aTaskInfo['TAS_DEF_SUBJECT_MESSAGE'] != '') {
                $sSubject = G::replaceDataField($aTaskInfo['TAS_DEF_SUBJECT_MESSAGE'], $aFields);
            } else {
                $sSubject = G::LoadTranslation('ID_MESSAGE_SUBJECT_DERIVATION');
            }

            //erik: new behaviour for messages
            G::loadClass('configuration');
            $oConf = new Configurations;
            $oConf->loadConfig($x, 'TAS_EXTRA_PROPERTIES', $aTaskInfo['TAS_UID'], '', '');
            $conf = $oConf->aConfig;

            $pathEmail = PATH_DATA_SITE . "mailTemplates" . PATH_SEP . $aTaskInfo["PRO_UID"] . PATH_SEP;
            $swtplDefault = 0;
            $sBody = null;

            if (isset($conf["TAS_DEF_MESSAGE_TYPE"]) &&
                    isset($conf["TAS_DEF_MESSAGE_TEMPLATE"]) &&
                    $conf["TAS_DEF_MESSAGE_TYPE"] == "template" &&
                    $conf["TAS_DEF_MESSAGE_TEMPLATE"] != ""
            ) {
                if ($conf["TAS_DEF_MESSAGE_TEMPLATE"] == "alert_message.html") {
                    $swtplDefault = 1;
                }

                $fileTemplate = $pathEmail . $conf["TAS_DEF_MESSAGE_TEMPLATE"];

                if (!file_exists($fileTemplate)) {
                    throw (new Exception("Template file \"$fileTemplate\" does not exist."));
                }

                $sBody = G::replaceDataGridField(file_get_contents($fileTemplate), $aFields);
            } else {
                $sBody = nl2br(G::replaceDataGridField($aTaskInfo["TAS_DEF_MESSAGE"], $aFields));
            }

            G::LoadClass("tasks");
            G::LoadClass("groups");
            G::LoadClass("spool");

            $task = new Tasks();
            $group = new Groups();
            $oUser = new Users();

            foreach ($aTasks as $aTask) {
                $sTo = null;
                $sCc = null;

                switch ($aTask["TAS_ASSIGN_TYPE"]) {
                    case "SELF_SERVICE":
                        if ($swtplDefault == 1) {
                            G::verifyPath($pathEmail, true); //Create if it does not exist
                            $fileTemplate = $pathEmail . "unassignedMessage.html";

                            if (!file_exists($fileTemplate)) {
                                @copy(PATH_TPL . "mails" . PATH_SEP . "unassignedMessage.html", $fileTemplate);
                            }

                            $sBody = G::replaceDataField(file_get_contents($fileTemplate), $aFields);
                        }

                        if (isset($aTask["TAS_UID"]) && !empty($aTask["TAS_UID"])) {
                            $arrayTaskUser = array();

                            $arrayAux1 = $task->getGroupsOfTask($aTask["TAS_UID"], 1);

                            foreach ($arrayAux1 as $arrayGroup) {
                                $arrayAux2 = $group->getUsersOfGroup($arrayGroup["GRP_UID"]);

                                foreach ($arrayAux2 as $arrayUser) {
                                    $arrayTaskUser[] = $arrayUser["USR_UID"];
                                }
                            }

                            $arrayAux1 = $task->getUsersOfTask($aTask["TAS_UID"], 1);

                            foreach ($arrayAux1 as $arrayUser) {
                                $arrayTaskUser[] = $arrayUser["USR_UID"];
                            }

                            $criteria = new Criteria("workflow");

                            $criteria->addSelectColumn(UsersPeer::USR_UID);
                            $criteria->addSelectColumn(UsersPeer::USR_USERNAME);
                            $criteria->addSelectColumn(UsersPeer::USR_FIRSTNAME);
                            $criteria->addSelectColumn(UsersPeer::USR_LASTNAME);
                            $criteria->addSelectColumn(UsersPeer::USR_EMAIL);
                            $criteria->add(UsersPeer::USR_UID, $arrayTaskUser, Criteria::IN);
                            $rsCriteria = UsersPeer::doSelectRs($criteria);
                            $rsCriteria->setFetchmode(ResultSet::FETCHMODE_ASSOC);

                            $to = null;
                            $cc = null;
                            $sw = 1;

                            while ($rsCriteria->next()) {
                                $row = $rsCriteria->getRow();

                                $toAux = (
                                        (($row["USR_FIRSTNAME"] != "") || ($row["USR_LASTNAME"] != "")) ?
                                                $row["USR_FIRSTNAME"] . " " . $row["USR_LASTNAME"] . " " : ""
                                        ) . "<" . $row["USR_EMAIL"] . ">";

                                if ($sw == 1) {
                                    $to = $toAux;
                                    $sw = 0;
                                } else {
                                    $cc = $cc . (($cc != null) ? "," : null) . $toAux;
                                }
                            }

                            $sTo = $to;
                            $sCc = $cc;
                        }
                        break;
                    default:
                        if (isset($aTask["USR_UID"]) && !empty($aTask["USR_UID"])) {
                            $aUser = $oUser->load($aTask["USR_UID"]);

                            $sTo = (
                                    (($aUser["USR_FIRSTNAME"] != "") || ($aUser["USR_LASTNAME"] != "")) ?
                                            $aUser["USR_FIRSTNAME"] . " " . $aUser["USR_LASTNAME"] . " " : ""
                                    ) . "<" . $aUser["USR_EMAIL"] . ">";
                        }
                        break;
                }

                if ($sTo != null) {
                    $oSpool = new spoolRun();
                    if ($aConfiguration['MESS_RAUTH'] == false || (is_string($aConfiguration['MESS_RAUTH']) && $aConfiguration['MESS_RAUTH'] == 'false')) {
                        $aConfiguration['MESS_RAUTH'] = 0;
                    } else {
                        $aConfiguration['MESS_RAUTH'] = 1;
                    }

                    $oSpool->setConfig(array(
                        "MESS_ENGINE" => $aConfiguration["MESS_ENGINE"],
                        "MESS_SERVER" => $aConfiguration["MESS_SERVER"],
                        "MESS_PORT" => $aConfiguration["MESS_PORT"],
                        "MESS_ACCOUNT" => $aConfiguration["MESS_ACCOUNT"],
                        "MESS_PASSWORD" => $aConfiguration["MESS_PASSWORD"],
                        "SMTPAuth" => ($aConfiguration["MESS_RAUTH"] == "1") ? true : false,
                        "SMTPSecure" => (isset($aConfiguration["SMTPSecure"])) ? $aConfiguration["SMTPSecure"] : ""
                    ));

                    $oSpool->create(array(
                        "msg_uid" => "",
                        "app_uid" => $sApplicationUID,
                        "del_index" => $iDelegation,
                        "app_msg_type" => "DERIVATION",
                        "app_msg_subject" => $sSubject,
                        "app_msg_from" => $sFrom,
                        "app_msg_to" => $sTo,
                        "app_msg_body" => $sBody,
                        "app_msg_cc" => $sCc,
                        "app_msg_bcc" => "",
                        "app_msg_attach" => "",
                        "app_msg_template" => "",
                        "app_msg_status" => "pending"
                    ));

                    if (($aConfiguration["MESS_BACKGROUND"] == "") ||
                            ($aConfiguration["MESS_TRY_SEND_INMEDIATLY"] == "1")
                    ) {
                        $oSpool->sendMail();
                    }
                }
            }
            //Send derivation notification - End
        } catch (Exception $oException) {
            throw $oException;
        }
    }

    /**
     * Obtain all user permits for Dynaforms, Input and output documents
     *
     * function getAllObjects ($PRO_UID, $APP_UID, $TAS_UID, $USR_UID)
     * @author Erik Amaru Ortiz <erik@colosa.com>
     * @access public
     * @param  Process ID, Application ID, Task ID and User ID
     * @return Array within all user permitions all objects' types
     */
    public function getAllObjects($PRO_UID, $APP_UID, $TAS_UID = '', $USR_UID = '')
    {
        $ACTIONS = Array('VIEW', 'BLOCK', 'DELETE'); //TO COMPLETE
        $MAIN_OBJECTS = Array();
        $RESULT_OBJECTS = Array();

        foreach ($ACTIONS as $action) {
            $MAIN_OBJECTS[$action] = $this->getAllObjectsFrom($PRO_UID, $APP_UID, $TAS_UID, $USR_UID, $action);
        }
        /* ADDITIONAL OPERATIONS */
        /*         * * BETWEN VIEW AND BLOCK** */
        $RESULT_OBJECTS['DYNAFORMS'] = G::arrayDiff(
                        $MAIN_OBJECTS['VIEW']['DYNAFORMS'], $MAIN_OBJECTS['BLOCK']['DYNAFORMS']
        );
        $RESULT_OBJECTS['INPUT_DOCUMENTS'] = G::arrayDiff(
                        $MAIN_OBJECTS['VIEW']['INPUT_DOCUMENTS'], $MAIN_OBJECTS['BLOCK']['INPUT_DOCUMENTS']
        );
        $RESULT_OBJECTS['OUTPUT_DOCUMENTS'] = array_merge_recursive(
                G::arrayDiff($MAIN_OBJECTS['VIEW']['OUTPUT_DOCUMENTS'], $MAIN_OBJECTS['BLOCK']['OUTPUT_DOCUMENTS']), G::arrayDiff($MAIN_OBJECTS['DELETE']['OUTPUT_DOCUMENTS'], $MAIN_OBJECTS['BLOCK']['OUTPUT_DOCUMENTS'])
        );
        $RESULT_OBJECTS['CASES_NOTES'] = G::arrayDiff(
                        $MAIN_OBJECTS['VIEW']['CASES_NOTES'], $MAIN_OBJECTS['BLOCK']['CASES_NOTES']
        );
        array_push($RESULT_OBJECTS['DYNAFORMS'], -1);
        array_push($RESULT_OBJECTS['INPUT_DOCUMENTS'], -1);
        array_push($RESULT_OBJECTS['OUTPUT_DOCUMENTS'], -1);
        array_push($RESULT_OBJECTS['CASES_NOTES'], -1);

        return $RESULT_OBJECTS;
    }

    /**
     * Obtain all user permits for Dynaforms, Input and output documents from some action [VIEW, BLOCK, etc...]
     *
     * function getAllObjectsFrom ($PRO_UID, $APP_UID, $TAS_UID, $USR_UID, $ACTION)
     * @author Erik Amaru Ortiz <erik@colosa.com>
     * @access public
     * @param  Process ID, Application ID, Task ID, User ID, Action
     * @return Array within all user permitions all objects' types
     */
    public function getAllObjectsFrom($PRO_UID, $APP_UID, $TAS_UID = '', $USR_UID = '', $ACTION = '')
    {
        $aCase = $this->loadCase($APP_UID);
        $USER_PERMISSIONS = Array();
        $GROUP_PERMISSIONS = Array();
        $RESULT = Array("DYNAFORM" => Array(), "INPUT" => Array(), "OUTPUT" => Array(), "CASES_NOTES" => 0);

        //permissions per user
        $oCriteria = new Criteria('workflow');
        $oCriteria->add(
                $oCriteria->getNewCriterion(ObjectPermissionPeer::USR_UID, $USR_UID)->addOr(
                        $oCriteria->getNewCriterion(ObjectPermissionPeer::USR_UID, '')->addOr(
                                $oCriteria->getNewCriterion(ObjectPermissionPeer::USR_UID, '0')
                        )
                )
        );
        $oCriteria->add(ObjectPermissionPeer::PRO_UID, $PRO_UID);
        $oCriteria->add(ObjectPermissionPeer::OP_ACTION, $ACTION);
        $oCriteria->add(
                $oCriteria->getNewCriterion(ObjectPermissionPeer::TAS_UID, $TAS_UID)->addOr(
                        $oCriteria->getNewCriterion(ObjectPermissionPeer::TAS_UID, '')->addOr(
                                $oCriteria->getNewCriterion(ObjectPermissionPeer::TAS_UID, '0')
                        )
                )
        );
        $oCriteria->add(
                $oCriteria->getNewCriterion(ObjectPermissionPeer::OP_CASE_STATUS, 'ALL')->addOr(
                        $oCriteria->getNewCriterion(ObjectPermissionPeer::OP_CASE_STATUS, '')->addOr(
                                $oCriteria->getNewCriterion(ObjectPermissionPeer::OP_CASE_STATUS, '0')
                        )
                )
        );
        $rs = ObjectPermissionPeer::doSelectRS($oCriteria);
        $rs->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $rs->next();
        while ($row = $rs->getRow()) {
            if (
                    (($aCase['APP_STATUS'] == $row['OP_CASE_STATUS']) ||
                    ($row['OP_CASE_STATUS'] == '') ||
                    ($row['OP_CASE_STATUS'] == 'ALL')) ||
                    ($row['OP_CASE_STATUS'] == '')) {
                array_push($USER_PERMISSIONS, $row);
            }
            $rs->next();
        }
        //permissions per group
        G::loadClass('groups');
        $gr = new Groups();
        $records = $gr->getActiveGroupsForAnUser($USR_UID);
        foreach ($records as $group) {
            $oCriteria = new Criteria('workflow');
            $oCriteria->add(ObjectPermissionPeer::USR_UID, $group);
            $oCriteria->add(ObjectPermissionPeer::PRO_UID, $PRO_UID);
            $oCriteria->add(ObjectPermissionPeer::OP_ACTION, $ACTION);
            $oCriteria->add(
                    $oCriteria->getNewCriterion(ObjectPermissionPeer::TAS_UID, $TAS_UID)->addOr(
                            $oCriteria->getNewCriterion(ObjectPermissionPeer::TAS_UID, '')->addOr(
                                    $oCriteria->getNewCriterion(ObjectPermissionPeer::TAS_UID, '0')
                            )
                    )
            );
            $oCriteria->add(
                    $oCriteria->getNewCriterion(ObjectPermissionPeer::OP_CASE_STATUS, 'ALL')->addOr(
                            $oCriteria->getNewCriterion(ObjectPermissionPeer::OP_CASE_STATUS, '')->addOr(
                                    $oCriteria->getNewCriterion(ObjectPermissionPeer::OP_CASE_STATUS, '0')
                            )
                    )
            );
            $rs = ObjectPermissionPeer::doSelectRS($oCriteria);
            $rs->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            while ($rs->next()) {
                array_push($GROUP_PERMISSIONS, $rs->getRow());
            }
        }
        $PERMISSIONS = array_merge($USER_PERMISSIONS, $GROUP_PERMISSIONS);
        foreach ($PERMISSIONS as $row) {
            $USER = $row['USR_UID'];
            $USER_RELATION = $row['OP_USER_RELATION'];
            $TASK_SOURCE = $row['OP_TASK_SOURCE'];
            $PARTICIPATE = $row['OP_PARTICIPATE'];
            $O_TYPE = $row['OP_OBJ_TYPE'];
            $O_UID = $row['OP_OBJ_UID'];
            $ACTION = $row['OP_ACTION'];
            $CASE_STATUS = $row['OP_CASE_STATUS'];

            // here!,. we should verify $PARTICIPATE
            $sw_participate = false; // must be false for default
            if (($row['OP_CASE_STATUS'] != 'COMPLETED') && ($row['OP_CASE_STATUS'] != '') && ($row['OP_CASE_STATUS'] != '0')) {
                if ($PARTICIPATE == 1) {
                    $oCriteriax = new Criteria('workflow');
                    $oCriteriax->add(AppDelegationPeer::USR_UID, $USR_UID);
                    $oCriteriax->add(AppDelegationPeer::APP_UID, $APP_UID);

                    if (AppDelegationPeer::doCount($oCriteriax) == 0) {
                        $sw_participate = true;
                    }
                }
            }
            if (!$sw_participate) {
                switch ($O_TYPE) {
                    case 'ANY':
                        //for dynaforms
                        $oCriteria = new Criteria('workflow');
                        $oCriteria->add(ApplicationPeer::APP_UID, $APP_UID);
                        $oCriteria->addJoin(ApplicationPeer::PRO_UID, StepPeer::PRO_UID);
                        $oCriteria->addJoin(StepPeer::STEP_UID_OBJ, DynaformPeer::DYN_UID);
                        if ($aCase['APP_STATUS'] != 'COMPLETED') {
                            if ($TASK_SOURCE != '' && $TASK_SOURCE != "0" && $TASK_SOURCE != 0) {
                                $oCriteria->add(StepPeer::TAS_UID, $TASK_SOURCE);
                            }
                        }
                        $oCriteria->add(StepPeer::STEP_TYPE_OBJ, 'DYNAFORM');
                        $oCriteria->addAscendingOrderByColumn(StepPeer::STEP_POSITION);
                        $oCriteria->setDistinct();

                        $oDataset = DynaformPeer::doSelectRS($oCriteria);
                        $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
                        $oDataset->next();

                        while ($aRow = $oDataset->getRow()) {
                            if (!in_array($aRow['DYN_UID'], $RESULT['DYNAFORM'])) {
                                array_push($RESULT['DYNAFORM'], $aRow['DYN_UID']);
                            }
                            $oDataset->next();
                        }

                        //inputs
                        $oCriteria = new Criteria('workflow');
                        $oCriteria->addSelectColumn(AppDocumentPeer::APP_DOC_UID);
                        $oCriteria->addSelectColumn(AppDocumentPeer::APP_DOC_TYPE);
                        $oCriteria->add(AppDelegationPeer::APP_UID, $APP_UID);
                        $oCriteria->add(AppDelegationPeer::PRO_UID, $PRO_UID);
                        if ($aCase['APP_STATUS'] != 'COMPLETED') {
                            if ($TASK_SOURCE != '' && $TASK_SOURCE != "0" && $TASK_SOURCE != 0) {
                                $oCriteria->add(AppDelegationPeer::TAS_UID, $TASK_SOURCE);
                            }
                        }
                        $oCriteria->add(
                                $oCriteria->getNewCriterion(AppDocumentPeer::APP_DOC_TYPE, 'INPUT')->
                                        addOr($oCriteria->getNewCriterion(AppDocumentPeer::APP_DOC_TYPE, 'OUTPUT'))->
                                        addOr($oCriteria->
                                                getNewCriterion(AppDocumentPeer::APP_DOC_TYPE, 'ATTACHED'))
                        );
                        $aConditions = Array();
                        $aConditions[] = array(AppDelegationPeer::APP_UID, AppDocumentPeer::APP_UID);
                        $aConditions[] = array(AppDelegationPeer::DEL_INDEX, AppDocumentPeer::DEL_INDEX);
                        $oCriteria->addJoinMC($aConditions, Criteria::LEFT_JOIN);

                        $oDataset = DynaformPeer::doSelectRS($oCriteria);
                        $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
                        $oDataset->next();
                        while ($aRow = $oDataset->getRow()) {
                            if ($aRow['APP_DOC_TYPE'] == "ATTACHED") {
                                $aRow['APP_DOC_TYPE'] = "INPUT";
                            }
                            if (!in_array($aRow['APP_DOC_UID'], $RESULT[$aRow['APP_DOC_TYPE']])) {
                                array_push($RESULT[$aRow['APP_DOC_TYPE']], $aRow['APP_DOC_UID']);
                            }
                            $oDataset->next();
                        }
                        $RESULT['CASES_NOTES'] = 1;
                        break;
                    case 'DYNAFORM':
                        $oCriteria = new Criteria('workflow');
                        $oCriteria->add(ApplicationPeer::APP_UID, $APP_UID);
                        if ($aCase['APP_STATUS'] != 'COMPLETED') {
                            if ($TASK_SOURCE != '' && $TASK_SOURCE != "0") {
                                $oCriteria->add(StepPeer::TAS_UID, $TASK_SOURCE);
                            }
                        }
                        if ($O_UID != '' && $O_UID != '0') {
                            $oCriteria->add(DynaformPeer::DYN_UID, $O_UID);
                        }
                        $oCriteria->addJoin(ApplicationPeer::PRO_UID, StepPeer::PRO_UID);
                        $oCriteria->addJoin(StepPeer::STEP_UID_OBJ, DynaformPeer::DYN_UID);
                        $oCriteria->add(StepPeer::STEP_TYPE_OBJ, 'DYNAFORM');
                        $oCriteria->addAscendingOrderByColumn(StepPeer::STEP_POSITION);
                        $oCriteria->setDistinct();

                        $oDataset = DynaformPeer::doSelectRS($oCriteria);
                        $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
                        $oDataset->next();

                        while ($aRow = $oDataset->getRow()) {
                            if (!in_array($aRow['DYN_UID'], $RESULT['DYNAFORM'])) {
                                array_push($RESULT['DYNAFORM'], $aRow['DYN_UID']);
                            }
                            $oDataset->next();
                        }
                        break;
                    case 'INPUT':
                    case 'OUTPUT':
                        if ($row['OP_OBJ_TYPE'] == 'INPUT') {
                            $obj_type = 'INPUT';
                        } else {
                            $obj_type = 'OUTPUT';
                        }
                        $oCriteria = new Criteria('workflow');
                        $oCriteria->addSelectColumn(AppDocumentPeer::APP_DOC_UID);
                        $oCriteria->addSelectColumn(AppDocumentPeer::APP_DOC_TYPE);
                        $oCriteria->add(AppDelegationPeer::APP_UID, $APP_UID);
                        $oCriteria->add(AppDelegationPeer::PRO_UID, $PRO_UID);
                        if ($aCase['APP_STATUS'] != 'COMPLETED') {
                            if ($TASK_SOURCE != '' && $TASK_SOURCE != "0" && $TASK_SOURCE != 0) {
                                $oCriteria->add(AppDelegationPeer::TAS_UID, $TASK_SOURCE);
                            }
                        }
                        if ($O_UID != '' && $O_UID != '0') {
                            $oCriteria->add(AppDocumentPeer::DOC_UID, $O_UID);
                        }
                        if ($obj_type == 'INPUT') {
                            $oCriteria->add(
                                    $oCriteria->getNewCriterion(AppDocumentPeer::APP_DOC_TYPE, $obj_type)->
                                            addOr($oCriteria->getNewCriterion(AppDocumentPeer::APP_DOC_TYPE, 'ATTACHED'))
                            );
                        } else {
                            $oCriteria->add(AppDocumentPeer::APP_DOC_TYPE, $obj_type);
                        }

                        $aConditions = Array();
                        $aConditions[] = array(AppDelegationPeer::APP_UID, AppDocumentPeer::APP_UID);
                        $aConditions[] = array(AppDelegationPeer::DEL_INDEX, AppDocumentPeer::DEL_INDEX);
                        $oCriteria->addJoinMC($aConditions, Criteria::LEFT_JOIN);

                        $oDataset = AppDocumentPeer::doSelectRS($oCriteria);
                        $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
                        $oDataset->next();
                        while ($aRow = $oDataset->getRow()) {
                            if (!in_array($aRow['APP_DOC_UID'], $RESULT[$obj_type])) {
                                array_push($RESULT[$obj_type], $aRow['APP_DOC_UID']);
                            }
                            $oDataset->next();
                        }
                        if ($obj_type == 'INPUT') {
                            // For supervisor documents
                            $oCriteria = new Criteria('workflow');
                            $oCriteria->addSelectColumn(AppDocumentPeer::APP_DOC_UID);
                            $oCriteria->addSelectColumn(AppDocumentPeer::APP_DOC_TYPE);
                            $oCriteria->add(ApplicationPeer::APP_UID, $APP_UID);
                            $oCriteria->add(ApplicationPeer::PRO_UID, $PRO_UID);
                            if ($O_UID != '' && $O_UID != '0') {
                                $oCriteria->add(AppDocumentPeer::DOC_UID, $O_UID);
                            }
                            $oCriteria->add(AppDocumentPeer::APP_DOC_TYPE, 'INPUT');
                            $oCriteria->add(AppDocumentPeer::DEL_INDEX, 100000);

                            $oCriteria->addJoin(ApplicationPeer::APP_UID, AppDocumentPeer::APP_UID, Criteria::LEFT_JOIN);

                            $oDataset = AppDocumentPeer::doSelectRS($oCriteria);
                            $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
                            $oDataset->next();
                            while ($aRow = $oDataset->getRow()) {
                                if (!in_array($aRow['APP_DOC_UID'], $RESULT['INPUT'])) {
                                    array_push($RESULT['INPUT'], $aRow['APP_DOC_UID']);
                                }
                                $oDataset->next();
                            }
                        }
                        break;
                    case 'CASES_NOTES':
                        $RESULT['CASES_NOTES'] = 1;
                        break;
                }
            }
        }
        return Array(
            "DYNAFORMS" => $RESULT['DYNAFORM'],
            "INPUT_DOCUMENTS" => $RESULT['INPUT'],
            "OUTPUT_DOCUMENTS" => $RESULT['OUTPUT'],
            "CASES_NOTES" => $RESULT['CASES_NOTES']
        );
    }

    /*
     * to check the user External
     * @author Everth The Answer
     *
     * verifyCaseTracker($case, $pin)
     * @access public
     * @param  $case, $pin
     * @return Array
     */

    public function verifyCaseTracker($case, $pin)
    {
        //CASE INSENSITIVE pin
        $pin = G::toUpper($pin);
        $pin = md5($pin);

        $oCriteria = new Criteria('workflow');
        $oCriteria->addSelectColumn(ApplicationPeer::APP_UID);
        $oCriteria->addSelectColumn(ApplicationPeer::APP_PIN);
        $oCriteria->addSelectColumn(ApplicationPeer::PRO_UID);
        $oCriteria->addSelectColumn(ApplicationPeer::APP_NUMBER);
        $oCriteria->addSelectColumn(ApplicationPeer::APP_PROC_CODE);
        //$oCriteria->add(ApplicationPeer::APP_NUMBER, $case);
        $oCriteria->add(ApplicationPeer::APP_PROC_CODE, $case);

        $oDataset = DynaformPeer::doSelectRS($oCriteria);
        $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $oDataset->next();
        $aRow = $oDataset->getRow();

        $sw = 0;
        if (is_array($aRow)) {
            $PRO_UID = $aRow['PRO_UID'];
            $APP_UID = $aRow['APP_UID'];
            $PIN = $aRow['APP_PIN'];
        } else {
            $oCriteria = new Criteria('workflow');
            $oCriteria->addSelectColumn(ApplicationPeer::APP_UID);
            $oCriteria->addSelectColumn(ApplicationPeer::APP_PIN);
            $oCriteria->addSelectColumn(ApplicationPeer::PRO_UID);
            $oCriteria->addSelectColumn(ApplicationPeer::APP_NUMBER);
            $oCriteria->addSelectColumn(ApplicationPeer::APP_PROC_CODE);
            $oCriteria->add(ApplicationPeer::APP_NUMBER, $case);

            $oDataseti = DynaformPeer::doSelectRS($oCriteria);
            $oDataseti->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $oDataseti->next();
            $aRowi = $oDataseti->getRow();

            if (is_array($aRowi)) {
                $PRO_UID = $aRowi['PRO_UID'];
                $APP_UID = $aRowi['APP_UID'];
                $PIN = $aRowi['APP_PIN'];
            } else {
                $sw = 1;
            }
        }

        $s = 0;
        if ($sw == 1) {
            //it isn't a case
            return -1;
        } else {
            $s++;
        }
        if ($PIN != $pin) {
            //the pin isn't correct
            return -2;
        } else {
            $s++;
        }
        $res = array();
        $res['PRO_UID'] = $PRO_UID;
        $res['APP_UID'] = $APP_UID;

        if ($s == 2) {
            return $res;
        }
    }

    /*
     * funcion caseTrackerPermissions, by Everth
     *
     * @name caseTrackerPermissions
     * @param string $PRO_UID
     * @return string
     */

    public function caseTrackerPermissions($PRO_UID)
    {
        $newCaseTracker = new CaseTracker();
        $caseTracker = $newCaseTracker->load($PRO_UID);
        if (is_array($caseTracker)) {
            $caseTracker['CT_MAP_TYPE'] = ($caseTracker['CT_MAP_TYPE'] != 'NONE') ? true : false;
            //$caseTracker['CT_DERIVATION_HISTORY']  = ($caseTracker['CT_DERIVATION_HISTORY'] == 1)? true : false;
            //$caseTracker['CT_MESSAGE_HISTORY']     = ($caseTracker['CT_MESSAGE_HISTORY'] == 1)? true : false;

            $criteria = new Criteria();
            $criteria->add(CaseTrackerObjectPeer::PRO_UID, $PRO_UID);
            $caseTracker['DYNADOC'] = (CaseTrackerObjectPeer::doCount($criteria) > 0) ? true : false;
        }
        return $caseTracker;
    }

    /*
     * This funcion creates a temporally OBJECT_PERMISSION table
     * by Everth The Answer
     *
     * @name verifyTable
     * @param
     * @return object
     */

    public function verifyTable()
    {
        $oCriteria = new Criteria('workflow');
        $del = DBAdapter::getStringDelimiter();

        $sDataBase = 'database_' . strtolower(DB_ADAPTER);
        if (G::LoadSystemExist($sDataBase)) {
            G::LoadSystem($sDataBase);
            $oDataBase = new database();
            $sql = $oDataBase->createTableObjectPermission();
        }
        $con = Propel::getConnection("workflow");
        $stmt = $con->prepareStatement($sql);
        $rs = $stmt->executeQuery();
    }

    /*
     * funcion input documents for case tracker
     * by Everth The Answer
     *
     * @name getAllUploadedDocumentsCriteriaTracker
     * @param string $sProcessUID
     * @param string $sApplicationUID
     * @param string $sDocUID
     * @return object
     */

    public function getAllUploadedDocumentsCriteriaTracker($sProcessUID, $sApplicationUID, $sDocUID)
    {
        $oAppDocument = new AppDocument();
        $oCriteria = new Criteria('workflow');
        $oCriteria->add(AppDocumentPeer::APP_UID, $sApplicationUID);
        //$oCriteria->add(AppDocumentPeer::APP_DOC_TYPE, array('INPUT'), Criteria::IN);
        $oCriteria->add(AppDocumentPeer::APP_DOC_TYPE, 'INPUT');
        $oCriteria->add(AppDocumentPeer::APP_DOC_STATUS, array('ACTIVE'), Criteria::IN);
        $oCriteria->add(AppDocumentPeer::DOC_UID, $sDocUID);
        $aConditions = array();
        $aConditions[] = array(AppDocumentPeer::APP_UID, AppDelegationPeer::APP_UID);
        $aConditions[] = array(AppDocumentPeer::DEL_INDEX, AppDelegationPeer::DEL_INDEX);
        $oCriteria->addJoinMC($aConditions, Criteria::LEFT_JOIN);
        $oCriteria->add(AppDelegationPeer::PRO_UID, $sProcessUID);
        $oCriteria->addAscendingOrderByColumn(AppDocumentPeer::APP_DOC_INDEX);
        $oDataset = AppDocumentPeer::doSelectRS($oCriteria);
        $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $oDataset->next();
        $aInputDocuments = array();
        $aInputDocuments[] = array(
            'APP_DOC_UID' => 'char',
            'DOC_UID' => 'char',
            'APP_DOC_COMMENT' => 'char',
            'APP_DOC_FILENAME' => 'char',
            'APP_DOC_INDEX' => 'integer'
        );
        while ($aRow = $oDataset->getRow()) {
            $oCriteria2 = new Criteria('workflow');
            $oCriteria2->add(AppDelegationPeer::APP_UID, $sApplicationUID);
            $oCriteria2->add(AppDelegationPeer::DEL_INDEX, $aRow['DEL_INDEX']);
            $oDataset2 = AppDelegationPeer::doSelectRS($oCriteria2);
            $oDataset2->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $oDataset2->next();
            $aRow2 = $oDataset2->getRow();
            $oTask = new Task();
            if ($oTask->taskExists($aRow2['TAS_UID'])) {
                $aTask = $oTask->load($aRow2['TAS_UID']);
            } else {
                $aTask = array('TAS_TITLE' => '(TASK DELETED)');
            }
            $aAux = $oAppDocument->load($aRow['APP_DOC_UID'], $aRow['DOC_VERSION']);
            $aFields = array(
                'APP_DOC_UID' => $aAux['APP_DOC_UID'],
                'DOC_UID' => $aAux['DOC_UID'],
                'APP_DOC_COMMENT' => $aAux['APP_DOC_COMMENT'],
                'APP_DOC_FILENAME' => $aAux['APP_DOC_FILENAME'],
                'APP_DOC_INDEX' => $aAux['APP_DOC_INDEX'],
                'TYPE' => $aAux['APP_DOC_TYPE'], 'ORIGIN' => $aTask['TAS_TITLE']
            );
            if ($aFields['APP_DOC_FILENAME'] != '') {
                $aFields['TITLE'] = $aFields['APP_DOC_FILENAME'];
            } else {
                $aFields['TITLE'] = $aFields['APP_DOC_COMMENT'];
            }
            //$aFields['POSITION'] = $_SESSION['STEP_POSITION'];
            $aFields['CONFIRM'] = G::LoadTranslation('ID_CONFIRM_DELETE_ELEMENT');
            $aInputDocuments[] = $aFields;
            $oDataset->next();
        }
        $oAppDocument = new AppDocument();
        $oCriteria = new Criteria('workflow');
        $oCriteria->add(AppDocumentPeer::APP_UID, $sApplicationUID);
        //$oCriteria->add(AppDocumentPeer::APP_DOC_TYPE, array('ATTACHED'), Criteria::IN);
        $oCriteria->add(AppDocumentPeer::APP_DOC_TYPE, 'ATTACHED');
        $oCriteria->add(AppDocumentPeer::APP_DOC_STATUS, array('ACTIVE'), Criteria::IN);
        $oCriteria->addAscendingOrderByColumn(AppDocumentPeer::APP_DOC_INDEX);
        $oDataset = AppDocumentPeer::doSelectRS($oCriteria);
        $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $oDataset->next();
        while ($aRow = $oDataset->getRow()) {
            $oCriteria2 = new Criteria('workflow');
            $oCriteria2->add(AppDelegationPeer::DEL_INDEX, $aRow['DEL_INDEX']);
            $oDataset2 = AppDelegationPeer::doSelectRS($oCriteria2);
            $oDataset2->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $oDataset2->next();
            $aRow2 = $oDataset2->getRow();
            $oTask = new Task();
            if ($oTask->taskExists($aRow2['TAS_UID'])) {
                $aTask = $oTask->load($aRow2['TAS_UID']);
            } else {
                $aTask = array('TAS_TITLE' => '(TASK DELETED)');
            }
            $aAux = $oAppDocument->load($aRow['APP_DOC_UID'], $aRow['DOC_VERSION']);
            $aFields = array(
                'APP_DOC_UID' => $aAux['APP_DOC_UID'],
                'DOC_UID' => $aAux['DOC_UID'],
                'APP_DOC_COMMENT' => $aAux['APP_DOC_COMMENT'],
                'APP_DOC_FILENAME' => $aAux['APP_DOC_FILENAME'],
                'APP_DOC_INDEX' => $aAux['APP_DOC_INDEX'],
                'TYPE' => $aAux['APP_DOC_TYPE'], 'ORIGIN' => $aTask['TAS_TITLE']
            );
            if ($aFields['APP_DOC_FILENAME'] != '') {
                $aFields['TITLE'] = $aFields['APP_DOC_FILENAME'];
            } else {
                $aFields['TITLE'] = $aFields['APP_DOC_COMMENT'];
            }
            //$aFields['POSITION'] = $_SESSION['STEP_POSITION'];
            $aFields['CONFIRM'] = G::LoadTranslation('ID_CONFIRM_DELETE_ELEMENT');
            $aInputDocuments[] = $aFields;
            $oDataset->next();
        }
        global $_DBArray;
        $_DBArray['inputDocuments'] = $aInputDocuments;
        $_SESSION['_DBArray'] = $_DBArray;
        G::LoadClass('ArrayPeer');
        $oCriteria = new Criteria('dbarray');
        $oCriteria->setDBArrayTable('inputDocuments');
        $oCriteria->addAscendingOrderByColumn(AppDocumentPeer::APP_DOC_CREATE_DATE);
        return $oCriteria;
    }

    /*
     * funcion output documents for case tracker
     * by Everth The Answer
     *
     * @name getAllGeneratedDocumentsCriteriaTracker
     * @param string $sProcessUID
     * @param string $sApplicationUID
     * @param string $sDocUID
     * @return object
     */

    public function getAllGeneratedDocumentsCriteriaTracker($sProcessUID, $sApplicationUID, $sDocUID)
    {
        $oAppDocument = new AppDocument();
        $oCriteria = new Criteria('workflow');
        $oCriteria->add(AppDocumentPeer::APP_UID, $sApplicationUID);
        $oCriteria->add(AppDocumentPeer::APP_DOC_TYPE, 'OUTPUT');
        $oCriteria->add(AppDocumentPeer::APP_DOC_STATUS, array('ACTIVE'), Criteria::IN);
        $oCriteria->add(AppDocumentPeer::DOC_UID, $sDocUID);
        $aConditions = array();
        $aConditions[] = array(AppDocumentPeer::APP_UID, AppDelegationPeer::APP_UID);
        $aConditions[] = array(AppDocumentPeer::DEL_INDEX, AppDelegationPeer::DEL_INDEX);
        $oCriteria->addJoinMC($aConditions, Criteria::LEFT_JOIN);
        $oCriteria->add(AppDelegationPeer::PRO_UID, $sProcessUID);
        $oCriteria->addAscendingOrderByColumn(AppDocumentPeer::APP_DOC_INDEX);
        $oDataset = AppDocumentPeer::doSelectRS($oCriteria);
        $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $oDataset->next();
        $aOutputDocuments = array();
        $aOutputDocuments[] = array(
            'APP_DOC_UID' => 'char',
            'DOC_UID' => 'char',
            'APP_DOC_COMMENT' => 'char',
            'APP_DOC_FILENAME' => 'char',
            'APP_DOC_INDEX' => 'integer'
        );
        while ($aRow = $oDataset->getRow()) {
            $oCriteria2 = new Criteria('workflow');
            $oCriteria2->add(AppDelegationPeer::APP_UID, $sApplicationUID);
            $oCriteria2->add(AppDelegationPeer::DEL_INDEX, $aRow['DEL_INDEX']);
            $oDataset2 = AppDelegationPeer::doSelectRS($oCriteria2);
            $oDataset2->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $oDataset2->next();
            $aRow2 = $oDataset2->getRow();
            $oTask = new Task();
            if ($oTask->taskExists($aRow2['TAS_UID'])) {
                $aTask = $oTask->load($aRow2['TAS_UID']);
            } else {
                $aTask = array('TAS_TITLE' => '(TASK DELETED)');
            }
            $aAux = $oAppDocument->load($aRow['APP_DOC_UID'], $aRow['DOC_VERSION']);
            $aFields = array(
                'APP_DOC_UID' => $aAux['APP_DOC_UID'],
                'DOC_UID' => $aAux['DOC_UID'],
                'APP_DOC_COMMENT' => $aAux['APP_DOC_COMMENT'],
                'APP_DOC_FILENAME' => $aAux['APP_DOC_FILENAME'],
                'APP_DOC_INDEX' => $aAux['APP_DOC_INDEX'],
                'ORIGIN' => $aTask['TAS_TITLE']
            );
            if ($aFields['APP_DOC_FILENAME'] != '') {
                $aFields['TITLE'] = $aFields['APP_DOC_FILENAME'];
            } else {
                $aFields['TITLE'] = $aFields['APP_DOC_COMMENT'];
            }
            //$aFields['POSITION'] = $_SESSION['STEP_POSITION'];
            $aFields['CONFIRM'] = G::LoadTranslation('ID_CONFIRM_DELETE_ELEMENT');
            $aOutputDocuments[] = $aFields;
            $oDataset->next();
        }

        global $_DBArray;
        $_DBArray['outputDocuments'] = $aOutputDocuments;
        $_SESSION['_DBArray'] = $_DBArray;
        G::LoadClass('ArrayPeer');
        $oCriteria = new Criteria('dbarray');
        $oCriteria->setDBArrayTable('outputDocuments');
        $oCriteria->addAscendingOrderByColumn(AppDocumentPeer::APP_DOC_CREATE_DATE);
        return $oCriteria;
    }

    /*
     * funcion History messages for case tracker
     * by Everth The Answer
     *
     * @name getHistoryMessagesTracker
     * @param string sApplicationUID
     * @return object
     */

    public function getHistoryMessagesTracker($sApplicationUID)
    {
        $oAppDocument = new AppDocument();
        $oCriteria = new Criteria('workflow');
        $oCriteria->add(AppMessagePeer::APP_UID, $sApplicationUID);
        $oCriteria->addAscendingOrderByColumn(AppMessagePeer::APP_MSG_DATE);
        $oDataset = AppMessagePeer::doSelectRS($oCriteria);
        $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $oDataset->next();

        $aMessages = array();
        $aMessages[] = array(
            'APP_MSG_UID' => 'char',
            'APP_UID' => 'char',
            'DEL_INDEX' => 'char',
            'APP_MSG_TYPE' => 'char',
            'APP_MSG_SUBJECT' => 'char',
            'APP_MSG_FROM' => 'char',
            'APP_MSG_TO' => 'char',
            'APP_MSG_BODY' => 'char',
            'APP_MSG_DATE' => 'char',
            'APP_MSG_CC' => 'char',
            'APP_MSG_BCC' => 'char',
            'APP_MSG_TEMPLATE' => 'char',
            'APP_MSG_STATUS' => 'char',
            'APP_MSG_ATTACH' => 'char'
        );
        while ($aRow = $oDataset->getRow()) {
            $aMessages[] = array(
                'APP_MSG_UID' => $aRow['APP_MSG_UID'],
                'APP_UID' => $aRow['APP_UID'],
                'DEL_INDEX' => $aRow['DEL_INDEX'],
                'APP_MSG_TYPE' => $aRow['APP_MSG_TYPE'],
                'APP_MSG_SUBJECT' => $aRow['APP_MSG_SUBJECT'],
                'APP_MSG_FROM' => $aRow['APP_MSG_FROM'],
                'APP_MSG_TO' => $aRow['APP_MSG_TO'],
                'APP_MSG_BODY' => $aRow['APP_MSG_BODY'],
                'APP_MSG_DATE' => $aRow['APP_MSG_DATE'],
                'APP_MSG_CC' => $aRow['APP_MSG_CC'],
                'APP_MSG_BCC' => $aRow['APP_MSG_BCC'],
                'APP_MSG_TEMPLATE' => $aRow['APP_MSG_TEMPLATE'],
                'APP_MSG_STATUS' => $aRow['APP_MSG_STATUS'],
                'APP_MSG_ATTACH' => $aRow['APP_MSG_ATTACH']
            );
            $oDataset->next();
        }

        global $_DBArray;
        $_DBArray['messages'] = $aMessages;
        $_SESSION['_DBArray'] = $_DBArray;
        G::LoadClass('ArrayPeer');
        $oCriteria = new Criteria('dbarray');
        $oCriteria->setDBArrayTable('messages');

        return $oCriteria;
    }

    /*
     * funcion History messages for case tracker ExtJS
     * @name getHistoryMessagesTrackerExt
     * @param string sApplicationUID
     * @param string Msg_UID
     * @return array
     */

    public function getHistoryMessagesTrackerExt($sApplicationUID, $start = null, $limit = null)
    {
        G::LoadClass('ArrayPeer');
        global $_DBArray;

        $oAppDocument = new AppDocument();
        $oCriteria = new Criteria('workflow');
        $oCriteria->add(AppMessagePeer::APP_UID, $sApplicationUID);
        $oCriteria->addAscendingOrderByColumn(AppMessagePeer::APP_MSG_DATE);
        if (!is_null($start)) {
            $oCriteria->setOffset($start);
        }
        if (!is_null($limit)) {
            $oCriteria->setLimit($limit);
        }
        $oDataset = AppMessagePeer::doSelectRS($oCriteria);
        $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $oDataset->next();
        $aMessages = array();

        while ($aRow = $oDataset->getRow()) {
            $aMessages[] = array('APP_MSG_UID' => $aRow['APP_MSG_UID'],
                'APP_UID' => $aRow['APP_UID'],
                'DEL_INDEX' => $aRow['DEL_INDEX'],
                'APP_MSG_TYPE' => $aRow['APP_MSG_TYPE'],
                'APP_MSG_SUBJECT' => $aRow['APP_MSG_SUBJECT'],
                'APP_MSG_FROM' => $aRow['APP_MSG_FROM'],
                'APP_MSG_TO' => $aRow['APP_MSG_TO'],
                'APP_MSG_BODY' => $aRow['APP_MSG_BODY'],
                'APP_MSG_DATE' => $aRow['APP_MSG_DATE'],
                'APP_MSG_CC' => $aRow['APP_MSG_CC'],
                'APP_MSG_BCC' => $aRow['APP_MSG_BCC'],
                'APP_MSG_TEMPLATE' => $aRow['APP_MSG_TEMPLATE'],
                'APP_MSG_STATUS' => $aRow['APP_MSG_STATUS'],
                'APP_MSG_ATTACH' => $aRow['APP_MSG_ATTACH'],
                'APP_MSG_SHOW_MESSAGE' => $aRow['APP_MSG_SHOW_MESSAGE']
            );
            $oDataset->next();
        }

        $_DBArray['messages'] = $aMessages;
        $_SESSION['_DBArray'] = $_DBArray;

        $oCriteria = new Criteria('dbarray');
        $oCriteria->setDBArrayTable('messages');

        return $aMessages;
    }

    /*
     * funcion History messages for case tracker
     * by Everth The Answer
     *
     * @name getHistoryMessagesTrackerView
     * @param string sApplicationUID
     * @param string Msg_UID
     * @return array
     */

    public function getHistoryMessagesTrackerView($sApplicationUID, $Msg_UID)
    {
        $oAppDocument = new AppDocument();
        $oCriteria = new Criteria('workflow');
        $oCriteria->add(AppMessagePeer::APP_UID, $sApplicationUID);
        $oCriteria->add(AppMessagePeer::APP_MSG_UID, $Msg_UID);
        $oCriteria->addAscendingOrderByColumn(AppMessagePeer::APP_MSG_DATE);
        $oDataset = AppMessagePeer::doSelectRS($oCriteria);
        $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $oDataset->next();
        $aRow = $oDataset->getRow();
        return $aRow;
    }

    /*
     * This function gets all data about APP_DOCUMENT
     *
     * @name getAllObjectsFromProcess
     * @param string sApplicationUID
     * @param object OBJ_TYPE
     * @return array
     */

    public function getAllObjectsFromProcess($PRO_UID, $OBJ_TYPE = '%')
    {
        $RESULT = Array();
        $oCriteria = new Criteria('workflow');
        $oCriteria->addSelectColumn(AppDocumentPeer::APP_DOC_UID);
        $oCriteria->addSelectColumn(AppDocumentPeer::APP_UID);
        $oCriteria->addSelectColumn(AppDocumentPeer::DEL_INDEX);
        $oCriteria->addSelectColumn(AppDocumentPeer::DOC_UID);
        $oCriteria->addSelectColumn(AppDocumentPeer::USR_UID);
        $oCriteria->addSelectColumn(AppDocumentPeer::APP_DOC_TYPE);
        $oCriteria->addSelectColumn(AppDocumentPeer::APP_DOC_CREATE_DATE);
        $oCriteria->addSelectColumn(AppDocumentPeer::APP_DOC_INDEX);

        $oCriteria->add(ApplicationPeer::PRO_UID, $PRO_UID);
        $oCriteria->addJoin(ApplicationPeer::APP_UID, AppDocumentPeer::APP_UID);

        $oCriteria->add(AppDocumentPeer::APP_DOC_TYPE, $OBJ_TYPE, Criteria::LIKE);

        $oDataset = DynaformPeer::doSelectRS($oCriteria);
        $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);

        while ($oDataset->next()) {
            $row = $oDataset->getRow();
            $oAppDocument = new AppDocument();
            $oAppDocument->Fields = $oAppDocument->load($row['APP_DOC_UID'], $row['DOC_VERSION']);

            $row['APP_DOC_FILENAME'] = $oAppDocument->Fields['APP_DOC_FILENAME'];
            array_push($RESULT, $row);
        }
        return $RESULT;
    }

    /*
     * execute triggers after derivation
     *
     * @name executeTriggersAfterExternal
     * @param string $sProcess
     * @param string $sTask
     * @param string $sApplication
     * @param string $iIndex
     * @param string $iStepPosition
     * @param array  $aNewData
     * @return void
     */

    public function executeTriggersAfterExternal($sProcess, $sTask, $sApplication, $iIndex, $iStepPosition, $aNewData = array())
    {
        //load the variables
        $Fields = $this->loadCase($sApplication);
        $Fields['APP_DATA'] = array_merge($Fields['APP_DATA'], G::getSystemConstants());
        $Fields['APP_DATA'] = array_merge($Fields['APP_DATA'], $aNewData);
        //execute triggers
        $oCase = new Cases();
        $aNextStep = $this->getNextStep($sProcess, $sApplication, $iIndex, $iStepPosition - 1);
        $Fields['APP_DATA'] = $this->ExecuteTriggers(
                $sTask, 'EXTERNAL', $aNextStep['UID'], 'AFTER', $Fields['APP_DATA']
        );
        //save data
        $aData = array();
        $aData['APP_NUMBER'] = $Fields['APP_NUMBER'];
        $aData['APP_PROC_STATUS'] = $Fields['APP_PROC_STATUS'];
        $aData['APP_DATA'] = $Fields['APP_DATA'];
        $aData['DEL_INDEX'] = $iIndex;
        $aData['TAS_UID'] = $sTask;
        $this->updateCase($sApplication, $aData);
    }

    /*
     * this function gets the current user in a task
     *
     * @name thisIsTheCurrentUser
     * @param string $sApplicationUID
     * @param string $iIndex
     * @param string $sUserUID
     * @param string $sAction
     * @param string $sURL
     * @return void
     */

    public function thisIsTheCurrentUser($sApplicationUID, $iIndex, $sUserUID, $sAction = '', $sURL = '')
    {
        $c = new Criteria('workflow');
        $c->add(AppDelegationPeer::APP_UID, $sApplicationUID);
        $c->add(AppDelegationPeer::DEL_INDEX, $iIndex);
        $c->add(AppDelegationPeer::USR_UID, $sUserUID);
        switch ($sAction) {
            case '':
                return (boolean) AppDelegationPeer::doCount($c);
                break;
            case 'REDIRECT':
                if (!(boolean) AppDelegationPeer::doCount($c)) {
                    $c = new Criteria('workflow');
                    $c->addSelectColumn(UsersPeer::USR_USERNAME);
                    $c->addSelectColumn(UsersPeer::USR_FIRSTNAME);
                    $c->addSelectColumn(UsersPeer::USR_LASTNAME);
                    $c->add(AppDelegationPeer::APP_UID, $sApplicationUID);
                    $c->add(AppDelegationPeer::DEL_INDEX, $iIndex);
                    $c->addJoin(AppDelegationPeer::USR_UID, UsersPeer::USR_UID, Criteria::LEFT_JOIN);
                    $oDataset = AppDelegationPeer::doSelectRs($c);
                    $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
                    $oDataset->next();
                    $aData = $oDataset->getRow();
                    G::SendMessageText(
                            G::LoadTranslation('ID_CASE_IS_CURRENTLY_WITH_ANOTHER_USER') . ': ' .
                            $aData['USR_FIRSTNAME'] . ' ' . $aData['USR_LASTNAME'] .
                            ' (' . $aData['USR_USERNAME'] . ')', 'error'
                    );
                    G::header('Location: ' . $sURL);
                    die;
                } else {
                    $c->add(AppDelegationPeer::DEL_FINISH_DATE, null, Criteria::ISNULL);
                    if (!(boolean) AppDelegationPeer::doCount($c)) {
                        G::SendMessageText(G::LoadTranslation('ID_CASE_ALREADY_DERIVATED'), 'error');
                        G::header('Location: ' . $sURL);
                        die;
                    }
                }
                break;
            case 'SHOW_MESSAGE':
                if (!(boolean) AppDelegationPeer::doCount($c)) {
                    $c = new Criteria('workflow');
                    $c->addSelectColumn(UsersPeer::USR_USERNAME);
                    $c->addSelectColumn(UsersPeer::USR_FIRSTNAME);
                    $c->addSelectColumn(UsersPeer::USR_LASTNAME);
                    $c->add(AppDelegationPeer::APP_UID, $sApplicationUID);
                    $c->add(AppDelegationPeer::DEL_INDEX, $iIndex);
                    $c->addJoin(AppDelegationPeer::USR_UID, UsersPeer::USR_UID, Criteria::LEFT_JOIN);
                    $oDataset = AppDelegationPeer::doSelectRs($c);
                    $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
                    $oDataset->next();
                    $aData = $oDataset->getRow();
                    die('<strong>' .
                            G::LoadTranslation('ID_CASE_ALREADY_DERIVATED') . ': ' .
                            $aData['USR_FIRSTNAME'] . ' ' .
                            $aData['USR_LASTNAME'] . ' (' . $aData['USR_USERNAME'] . ')</strong>'
                    );
                } else {
                    $c->add(AppDelegationPeer::DEL_FINISH_DATE, null, Criteria::ISNULL);
                    if (!(boolean) AppDelegationPeer::doCount($c)) {
                        die('<strong>' . G::LoadTranslation('ID_CASE_ALREADY_DERIVATED') . '</strong>');
                    }
                }
                break;
        }
    }

    /*
     * this function gets the user in Case
     *
     * @name getCriteriaUsersCases
     * @param string $status
     * @param string $USR_UID
     * @return object
     */

    public function getCriteriaUsersCases($status, $USR_UID)
    {
        $c = new Criteria('workflow');
        $c->addJoin(ApplicationPeer::APP_UID, AppDelegationPeer::APP_UID, Criteria::LEFT_JOIN);
        $c->add(ApplicationPeer::APP_STATUS, $status);
        $c->add(AppDelegationPeer::USR_UID, $USR_UID);
        $c->add(AppDelegationPeer::DEL_FINISH_DATE, null, Criteria::ISNULL);
        return $c;
    }

    /*
     * this function gets information in a search
     *
     * @name getCriteriaUsersCases
     * @param string $sCase
     * @param string $sTask
     * @param string $sCurrentUser
     * @param string $sSentby
     * @param string $sLastModFrom
     * @param string $sLastModTo
     * @param string $status
     * @param string $permisse
     * @param string $userlogged
     * @param array  $aSupervisor
     * @return object
     */

    public function getAdvancedSearch($sCase, $sProcess, $sTask, $sCurrentUser, $sSentby, $sLastModFrom, $sLastModTo, $sStatus, $permisse, $userlogged, $aSupervisor)
    {
        $sTypeList = '';
        $sUIDUserLogged = '';

        $c = new Criteria('workflow');
        $c->clearSelectColumns();
        $c->addSelectColumn(ApplicationPeer::APP_UID);
        $c->addSelectColumn(ApplicationPeer::APP_NUMBER);
        $c->addSelectColumn(ApplicationPeer::APP_UPDATE_DATE);
        $c->addSelectColumn(AppDelegationPeer::DEL_PRIORITY);
        //$c->addSelectColumn(AppDelegationPeer::DEL_TASK_DUE_DATE);
        $c->addAsColumn(
                'DEL_TASK_DUE_DATE', " IF (" . AppDelegationPeer::DEL_TASK_DUE_DATE . " <= NOW(), CONCAT('<span style=\'color:red\';>', " .
                AppDelegationPeer::DEL_TASK_DUE_DATE . ", '</span>'), " . AppDelegationPeer::DEL_TASK_DUE_DATE . ") ");
        $c->addSelectColumn(AppDelegationPeer::DEL_INDEX);
        $c->addSelectColumn(AppDelegationPeer::TAS_UID);
        $c->addSelectColumn(AppDelegationPeer::DEL_INIT_DATE);
        $c->addSelectColumn(AppDelegationPeer::DEL_FINISH_DATE);
        $c->addSelectColumn(UsersPeer::USR_UID);
        $c->addAsColumn('APP_CURRENT_USER', "CONCAT(USERS.USR_LASTNAME, ' ', USERS.USR_FIRSTNAME)");
        $c->addSelectColumn(ApplicationPeer::APP_STATUS);
        $c->addAsColumn('APP_TITLE', 'APP_TITLE.CON_VALUE');
        $c->addAsColumn('APP_PRO_TITLE', 'PRO_TITLE.CON_VALUE');
        $c->addAsColumn('APP_TAS_TITLE', 'TAS_TITLE.CON_VALUE');
        //$c->addAsColumn('APP_DEL_PREVIOUS_USER', 'APP_LAST_USER.USR_USERNAME');
        $c->addAsColumn(
                'APP_DEL_PREVIOUS_USER', "CONCAT(APP_LAST_USER.USR_LASTNAME, ' ', APP_LAST_USER.USR_FIRSTNAME)"
        );

        $c->addAlias("APP_TITLE", 'CONTENT');
        $c->addAlias("PRO_TITLE", 'CONTENT');
        $c->addAlias("TAS_TITLE", 'CONTENT');
        $c->addAlias("APP_PREV_DEL", 'APP_DELEGATION');
        $c->addAlias("APP_LAST_USER", 'USERS');

        $c->addJoin(ApplicationPeer::APP_UID, AppDelegationPeer::APP_UID, Criteria::LEFT_JOIN);
        $c->addJoin(AppDelegationPeer::TAS_UID, TaskPeer::TAS_UID, Criteria::LEFT_JOIN);
        $appThreadConds[] = array(ApplicationPeer::APP_UID, AppThreadPeer::APP_UID);
        $appThreadConds[] = array(AppDelegationPeer::DEL_INDEX, AppThreadPeer::DEL_INDEX);
        $c->addJoinMC($appThreadConds, Criteria::LEFT_JOIN);
        $c->addJoin(AppDelegationPeer::USR_UID, UsersPeer::USR_UID, Criteria::LEFT_JOIN);

        $del = DBAdapter::getStringDelimiter();
        $appTitleConds = array();
        $appTitleConds[] = array(ApplicationPeer::APP_UID, 'APP_TITLE.CON_ID');
        $appTitleConds[] = array('APP_TITLE.CON_CATEGORY', $del . 'APP_TITLE' . $del);
        $appTitleConds[] = array('APP_TITLE.CON_LANG', $del . SYS_LANG . $del);
        $c->addJoinMC($appTitleConds, Criteria::LEFT_JOIN);

        $proTitleConds = array();
        $proTitleConds[] = array(ApplicationPeer::PRO_UID, 'PRO_TITLE.CON_ID');
        $proTitleConds[] = array('PRO_TITLE.CON_CATEGORY', $del . 'PRO_TITLE' . $del);
        $proTitleConds[] = array('PRO_TITLE.CON_LANG', $del . SYS_LANG . $del);
        $c->addJoinMC($proTitleConds, Criteria::LEFT_JOIN);

        $tasTitleConds = array();
        $tasTitleConds[] = array(AppDelegationPeer::TAS_UID, 'TAS_TITLE.CON_ID');
        $tasTitleConds[] = array('TAS_TITLE.CON_CATEGORY', $del . 'TAS_TITLE' . $del);
        $tasTitleConds[] = array('TAS_TITLE.CON_LANG', $del . SYS_LANG . $del);
        $c->addJoinMC($tasTitleConds, Criteria::LEFT_JOIN);

        $prevConds = array();
        $prevConds[] = array(ApplicationPeer::APP_UID, 'APP_PREV_DEL.APP_UID');
        $prevConds[] = array('APP_PREV_DEL.DEL_INDEX', AppDelegationPeer::DEL_PREVIOUS);
        $c->addJoinMC($prevConds, Criteria::LEFT_JOIN);

        $usrConds = array();
        $usrConds[] = array('APP_PREV_DEL.USR_UID', 'APP_LAST_USER.USR_UID');
        $c->addJoinMC($usrConds, Criteria::LEFT_JOIN);

        $c->add(TaskPeer::TAS_TYPE, 'SUBPROCESS', Criteria::NOT_EQUAL);

        $c->add(
                $c->getNewCriterion(AppThreadPeer::APP_THREAD_STATUS, 'OPEN')->
                        addOr($c->getNewCriterion(ApplicationPeer::APP_STATUS, 'COMPLETED')->
                                addAnd($c->getNewCriterion(AppDelegationPeer::DEL_PREVIOUS, 0)))
        );

        if ($sCase != '') {
            $c->add(ApplicationPeer::APP_NUMBER, $sCase);
        }
        if ($sProcess != '') {
            $c->add(ApplicationPeer::PRO_UID, $sProcess);
        }
        if ($sTask != '' && $sTask != "0" && $sTask != 0) {
            $c->add(AppDelegationPeer::TAS_UID, $sTask);
        }
        if ($sCurrentUser != '') {
            $c->add(ApplicationPeer::APP_CUR_USER, $sCurrentUser);
        }
        if ($sSentby != '') {
            $c->add('APP_PREV_DEL.USR_UID', $sSentby);
        }
        if ($sLastModFrom != '0000-00-00' && $sLastModTo != '0000-00-00' && $sLastModFrom != '' && $sLastModTo != '') {
            $c->add(
                    $c->getNewCriterion(ApplicationPeer::APP_UPDATE_DATE, $sLastModFrom . ' 00:00:00', Criteria::GREATER_EQUAL)->
                            addAnd($c->getNewCriterion(ApplicationPeer::APP_UPDATE_DATE, $sLastModTo . ' 23:59:59', Criteria::LESS_EQUAL))
            );
        }
        if ($sStatus != '') {
            if ($sStatus != 'gral') {
                $c->add(ApplicationPeer::APP_STATUS, $sStatus);
            }
        }

        if ($permisse != 0) {
            $c->add(
                    $c->getNewCriterion(AppDelegationPeer::USR_UID, $userlogged)->
                            addOr($c->getNewCriterion(AppDelegationPeer::PRO_UID, $aSupervisor, Criteria::IN))
            );
        }

        $c->addDescendingOrderByColumn(ApplicationPeer::APP_NUMBER);

        return $c;
    }

    //**DEPRECATED
    /*
     * this function gets a condition rule
     *
     * @name getConditionCasesCount
     * @param string $type
     * @return int
     */
    public function getConditionCasesCount($type, $sumary = null)
    {
        $result = 0;
        return $result;

        $nCount = 0;

        list($aCriteria, $xmlfile) = $this->getConditionCasesList($type, $_SESSION['USER_LOGGED'], false);
        $rs = ApplicationPeer::doSelectRS($aCriteria);
        $rs->setFetchmode(ResultSet::FETCHMODE_ASSOC);

        if (isset($sumary) && $sumary === true) {
            $sumary = Array();
            while ($rs->next()) {
                $nCount++;
                $row = $rs->getRow();
                if (isset($sumary[$row['PRO_UID']])) {
                    $sumary[$row['PRO_UID']]['count'] += 1;
                } else {
                    $sumary[$row['PRO_UID']]['count'] = 1;
                    $sumary[$row['PRO_UID']]['name'] = $row['APP_PRO_TITLE'];
                }
            }
            return Array('count' => $nCount, 'sumary' => $sumary);
        } else {
            while ($rs->next()) {
                $nCount++;
            }
            return $nCount;
        }
    }

    //**DEPRECATED
    /*
     * this function gets all conditions rules
     *
     * @name getAllConditionCasesCount
     * @param string $type
     * @return array
     */
    public function getAllConditionCasesCount($types, $sumary = null)
    {
        $aResult = Array();
        foreach ($types as $type) {
            $aResult[$type] = $this->getConditionCasesCount($type, $sumary);
        }
        return $aResult;
    }

    /*
     * this function gets a user that it is in a case
     *
     * @name userParticipatedInCase
     * @param string $sAppUid
     * @param string $sUIDUserLogged
     * @return int
     */

    public function userParticipatedInCase($sAppUid, $sUIDUserLogged)
    {
        $c = new Criteria('workflow');
        $c->addSelectColumn(AppDelegationPeer::APP_UID);
        $c->add(AppDelegationPeer::APP_UID, $sAppUid);
        $c->add(AppDelegationPeer::USR_UID, $sUIDUserLogged);

        $rs = ApplicationPeer::doSelectRS($c);
        $count = 0;
        while ($rs->next()) {
            $count++;
        }
        return $count;
    }

    /*
     * Get the current delegation of a case (This is a clone of getCurrentDelegation but this will return
     * the index with out filtering by user or status.
     * todo: deprecated ?
     * @name getCurrentDelegationCase
     * @param string $sApplicationUID
     * @return integer
     */

    public function getCurrentDelegationCase($sApplicationUID = '')
    {
        $oSession = new DBSession(new DBConnection());
        $oDataset = $oSession->Execute('
            SELECT
            DEL_INDEX
            FROM
            APP_DELEGATION
            WHERE
            APP_UID = "' . $sApplicationUID . '"
            ORDER BY DEL_DELEGATE_DATE DESC
        ');
        $aRow = $oDataset->Read();
        return $aRow['DEL_INDEX'];
    }

    public function clearCaseSessionData()
    {
        if (isset($_SESSION['APPLICATION'])) {
            unset($_SESSION['APPLICATION']);
        }
        if (isset($_SESSION['PROCESS'])) {
            unset($_SESSION['PROCESS']);
        }
        if (isset($_SESSION['INDEX'])) {
            unset($_SESSION['INDEX']);
        }
        if (isset($_SESSION['STEP_POSITION'])) {
            unset($_SESSION['STEP_POSITION']);
        }
    }

    /**
     * Jump to the determinated case by its Application number
     *
     * @param interger $APP_NUMBER
     */
    public function jumpToCase($APP_NUMBER)
    {
        $_GET['APP_UID'] = $oCase->getApplicationUIDByNumber($_GET['APP_NUMBER']);
        $_GET['DEL_INDEX'] = $oCase->getCurrentDelegation($_GET['APP_UID'], $_SESSION['USER_LOGGED']);
        if (is_null($_GET['DEL_INDEX'])) {
            $participated = $oCase->userParticipatedInCase($_GET['APP_UID'], $_SESSION['USER_LOGGED']);
            if ($participated == 0) {
                if (is_null($_GET['APP_UID'])) {
                    G::SendMessageText(G::LoadTranslation('ID_CASE_DOES_NOT_EXISTS'), 'info');
                } else {
                    G::SendMessageText(G::LoadTranslation('ID_CASE_IS_CURRENTLY_WITH_ANOTHER_USER'), 'info');
                }
                G::header('location: cases_List');
            }
        }
    }

    public function discriminateCases($aData)
    {
        $siblingThreadData = $this->GetAllOpenDelegation($aData);
        foreach ($siblingThreadData as $thread => $threadData) {
            $this->closeAppThread($aData['APP_UID'], $threadData['DEL_INDEX']); //Close Sibling AppThreads
            $this->CloseCurrentDelegation($aData['APP_UID'], $threadData['DEL_INDEX']); //Close Sibling AppDelegations
            //update searchindex
            if ($this->appSolr != null) {
                $this->appSolr->updateApplicationSearchIndex($aData['APP_UID']);
            }
        }
    }

    /*
     * We're getting all threads in a task
     *
     * @name GetAllThreads of Particular Parent Thread
     * @param string $sAppUid
     * @param string $sAppParent
     * @return $aThreads
     */

    public function GetAllOpenDelegation($aData)
    {
        try {
            $aThreads = array();
            $c = new Criteria();
            $c->add(AppDelegationPeer::APP_UID, $aData['APP_UID']);
            $c->add(AppDelegationPeer::DEL_PREVIOUS, $aData['APP_THREAD_PARENT']);
            $c->add(AppDelegationPeer::DEL_THREAD_STATUS, 'OPEN');
            $rs = AppDelegationPeer::doSelectRs($c);
            $rs->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $rs->next();
            $row = $rs->getRow();
            while (is_array($row)) {
                $aThreads[] = $row;
                $rs->next();
                $row = $rs->getRow();
            }
            return $aThreads;
        } catch (exception $e) {
            throw ($e);
        }
    }

    public function getUsersToReassign($TAS_UID, $USR_UID)
    {
        G::LoadClass('groups');
        G::LoadClass('tasks');

        $oTasks = new Tasks();
        $aAux = $oTasks->getGroupsOfTask($TAS_UID, 1);
        $row = array();

        $groups = new Groups();
        foreach ($aAux as $aGroup) {
            $aUsers = $groups->getUsersOfGroup($aGroup['GRP_UID']);
            foreach ($aUsers as $aUser) {
                if ($aUser['USR_UID'] != $USR_UID) {
                    $row[] = $aUser['USR_UID'];
                }
            }
        }

        $aAux = $oTasks->getUsersOfTask($TAS_UID, 1);
        foreach ($aAux as $aUser) {
            if ($aUser['USR_UID'] != $USR_UID) {
                $row[] = $aUser['USR_UID'];
            }
        }

        // Group Ad Hoc
        $oTasks = new Tasks();
        $aAux = $oTasks->getGroupsOfTask($TAS_UID, 2);
        $groups = new Groups();
        foreach ($aAux as $aGroup) {
            $aUsers = $groups->getUsersOfGroup($aGroup['GRP_UID']);
            foreach ($aUsers as $aUser) {
                if ($aUser['USR_UID'] != $USR_UID) {
                    $row[] = $aUser['USR_UID'];
                }
            }
        }

        // User Ad Hoc
        $aAux = $oTasks->getUsersOfTask($TAS_UID, 2);
        foreach ($aAux as $aUser) {
            if ($aUser['USR_UID'] != $USR_UID) {
                $row[] = $aUser['USR_UID'];
            }
        }

        require_once 'classes/model/Users.php';
        $c = new Criteria('workflow');
        $c->addSelectColumn(UsersPeer::USR_UID);
        $c->addSelectColumn(UsersPeer::USR_USERNAME);
        $c->addSelectColumn(UsersPeer::USR_FIRSTNAME);
        $c->addSelectColumn(UsersPeer::USR_LASTNAME);
        $c->add(UsersPeer::USR_UID, $row, Criteria::IN);

        $rs = UsersPeer::doSelectRs($c);
        $rs->setFetchmode(ResultSet::FETCHMODE_ASSOC);

        $rows = Array();
        while ($rs->next()) {
            $rows[] = $rs->getRow();
        }

        return $rows;
    }

    /*
     * this function gets all users that already participated in a case
     *
     * @name getUsersParticipatedInCase
     * @param string $sAppUid
     * @return array (criteria+array)
     */

    public function getUsersParticipatedInCase($sAppUid)
    {
        $c = new Criteria('workflow');
        $c->addSelectColumn(AppDelegationPeer::APP_UID);
        $c->addSelectColumn(AppDelegationPeer::USR_UID);
        $c->addSelectColumn(UsersPeer::USR_USERNAME);
        $c->addSelectColumn(UsersPeer::USR_EMAIL);

        $c->add(AppDelegationPeer::APP_UID, $sAppUid, CRITERIA::EQUAL);
        $c->addJoin(AppDelegationPeer::USR_UID, UsersPeer::USR_UID, Criteria::LEFT_JOIN);
        $rs = AppDelegationPeer::doSelectRS($c);
        $rs->setFetchmode(ResultSet::FETCHMODE_ASSOC);

        $rows = array();
        $rs->next();
        while ($row = $rs->getRow()) {
            $rows[$row['USR_UID']] = $row;
            $rs->next();
        }
        $response['criteria'] = $c;
        $response['array'] = $rows;
        return $response;
    }

    public function getCaseNotes($applicationID, $type = 'array', $userUid = '')
    {
        require_once ( "classes/model/AppNotes.php" );
        $appNotes = new AppNotes();
        $appNotes = $appNotes->getNotesList($applicationID, $userUid);
        $response = '';
        if (is_array($appNotes)) {
            switch ($type) {
                case 'array':
                    $response = array();
                    foreach ($appNotes['array']['notes'] as $key => $value) {
                        $list = array();
                        $list['FULL_NAME'] = $value['USR_FIRSTNAME'] . " " . $value['USR_LASTNAME'];
                        foreach ($value as $keys => $value) {
                            if ($keys != 'USR_FIRSTNAME' && $keys != 'USR_LASTNAME' && $keys != 'USR_EMAIL') {
                                $list[$keys] = $value;
                            }
                        }
                        $response[$key + 1] = $list;
                    }
                    break;
                case 'object':
                    $response = new stdclass();
                    foreach ($appNotes['array']['notes'] as $key => $value) {
                        $response->$key->FULL_NAME = $value['USR_FIRSTNAME'] . " " . $value['USR_LASTNAME'];
                        foreach ($value as $keys => $value) {
                            if ($keys != 'USR_FIRSTNAME' && $keys != 'USR_LASTNAME' && $keys != 'USR_EMAIL') {
                                $response->$key->$keys = $value;
                            }
                        }
                    }
                    break;
                case 'string':
                    $response = '';
                    foreach ($appNotes['array']['notes'] as $key => $value) {
                        $response .= $value['USR_FIRSTNAME'] . " " .
                                $value['USR_LASTNAME'] . " " .
                                "(" . $value['USR_USERNAME'] . ")" .
                                " " . $value['NOTE_CONTENT'] . " " . " (" . $value['NOTE_DATE'] . " ) " .
                                " \n";
                    }
                    break;
            }
        }
        return $response;
    }

    public function getExecuteTriggerProcess($appUid, $action)
    {
        if ((!isset($appUid) && $appUid == '') || (!isset($action) && $action == '')) {
            return false;
        }

        $aFields = $this->loadCase($appUid);
        $proUid = $aFields['PRO_UID'];

        require_once ( "classes/model/Process.php" );
        $appProcess = new Process();
        $webBotTrigger = $appProcess->getTriggerWebBotProcess($proUid, $action);

        if ($webBotTrigger != false && $webBotTrigger != '') {
            global $oPMScript;
            $oPMScript = new PMScript();
            $oPMScript->setFields($aFields['APP_DATA']);
            $oPMScript->setScript($webBotTrigger);
            $oPMScript->execute();
            $aFields['APP_DATA'] = array_merge($aFields['APP_DATA'], $oPMScript->aFields);
            $this->updateCase($aFields['APP_UID'], $aFields);
            return true;
        }
        return false;
    }
}
 