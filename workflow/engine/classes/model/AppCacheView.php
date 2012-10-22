<?php
/**
 * AppCacheView.php
 * @package    workflow.engine.classes.model
 */

require_once ('classes/model/om/BaseAppCacheView.php');


/**
 * Skeleton subclass for representing a row from the 'APP_CACHE_VIEW' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 */

require_once ('classes/model/Application.php');
require_once ('classes/model/AppDelegation.php');
require_once ('classes/model/AppDelay.php');
require_once ('classes/model/Task.php');
require_once ('classes/model/AdditionalTables.php');


/**
 * @package    workflow.engine.classes.model
 * @access public
 */
class AppCacheView extends BaseAppCacheView
{
    public $confCasesList;
    public $pathToAppCacheFiles;

    public function getAllCounters($aTypes, $userUid, $processSummary = false)
    {
        $aResult = array();

        foreach ($aTypes as $type) {
            $aResult[$type] = $this->getListCounters($type, $userUid, $processSummary);
        }

        return $aResult;
    }

    public function getListCounters($type, $userUid, $processSummary)
    {
        switch ($type) {
            case 'to_do':
                $criteria = $this->getToDoCountCriteria($userUid);
                break;
            case 'draft':
                $criteria = $this->getDraftCountCriteria($userUid);
                break;
            case 'sent':
                $criteria = $this->getSentCountCriteria($userUid);
                //return AppCacheViewPeer::doCount($criteria, true);
                break;
            case 'selfservice':
                $criteria = $this->getUnassignedCountCriteria($userUid);
                break;
            case 'paused':
                $criteria = $this->getPausedCountCriteria($userUid);
                break;
            case 'completed':
                $criteria = $this->getCompletedCountCriteria($userUid);
                break;
            case 'cancelled':
                $criteria = $this->getCancelledCountCriteria($userUid);
                break;
            case 'to_revise':
                $criteria = $this->getToReviseCountCriteria($userUid);
                break;
            default:
                return $type;
        }

        return AppCacheViewPeer::doCount($criteria);
    }

    /**
     * gets the todo cases list criteria
     * param $userUid the current userUid
     * param $doCount if true this will return the criteria for count cases only
     * @return Criteria object $Criteria
     */
    public function getToDo($userUid, $doCount)
    {
        //adding configuration fields from the configuration options
        //and forming the criteria object
        if ($doCount && !isset($this->confCasesList['PMTable']) && !empty($this->confCasesList['PMTable'])) {
            $criteria  = new Criteria('workflow');
        } else {
            $criteria = $this->addPMFieldsToCriteria('todo');
        }

        $criteria->addSelectColumn(AppCacheViewPeer::TAS_UID);
        $criteria->addSelectColumn(AppCacheViewPeer::PRO_UID);

        $criteria->add(AppCacheViewPeer::APP_STATUS, "TO_DO", CRITERIA::EQUAL);
        $criteria->add(AppCacheViewPeer::USR_UID, $userUid);
        $criteria->add(AppCacheViewPeer::DEL_FINISH_DATE, null, Criteria::ISNULL);
        $criteria->add(AppCacheViewPeer::APP_THREAD_STATUS, 'OPEN');
        $criteria->add(AppCacheViewPeer::DEL_THREAD_STATUS, 'OPEN');

        return $criteria;
    }

    /**
     * gets the todo cases list criteria for count
     * param $userUid the current userUid
     * @return Criteria object $Criteria
     */
    public function getToDoCountCriteria($userUid)
    {
        return $this->getToDo($userUid, true);
    }

    /**
     * gets the todo cases list criteria for list
     * param $userUid the current userUid
     * @return Criteria object $Criteria
     */
    public function getToDoListCriteria($userUid)
    {
        return $this->getToDo($userUid, false);
    }

    /**
     * gets the DRAFT cases list criteria
     * param $userUid the current userUid
     * param $doCount if true this will return the criteria for count cases only
     * @return Criteria object $Criteria
     */
    public function getDraft($userUid, $doCount)
    {
        //adding configuration fields from the configuration options
        //and forming the criteria object
        if ($doCount && !isset($this->confCasesList['PMTable']) && !empty($this->confCasesList['PMTable'])) {
            $criteria = new Criteria('workflow');
        } else {
            $criteria = $this->addPMFieldsToCriteria('draft');
        }

        $criteria->add(AppCacheViewPeer::APP_STATUS, "DRAFT", CRITERIA::EQUAL);
        $criteria->add(AppCacheViewPeer::USR_UID, $userUid);

        //$criteria->add(AppCacheViewPeer::DEL_FINISH_DATE, null, Criteria::ISNULL);
        $criteria->add(AppCacheViewPeer::APP_THREAD_STATUS, 'OPEN');
        $criteria->add(AppCacheViewPeer::DEL_THREAD_STATUS, 'OPEN');

        return $criteria;
    }

    /**
     * gets the DRAFT cases list criteria for count
     * param $userUid the current userUid
     * @return Criteria object $Criteria
     */
    public function getDraftCountCriteria($userUid)
    {
        return $this->getDraft($userUid, true);
    }

    /**
     * gets the DRAFT cases list criteria for list
     * param $userUid the current userUid
     * @return Criteria object $Criteria
     */
    public function getDraftListCriteria($userUid)
    {
        return $this->getDraft($userUid, false);
    }

    /**
     * gets the SENT cases list criteria
     * param $userUid the current userUid
     * param $doCount if true this will return the criteria for count cases only
     * @return Criteria object $Criteria
     */
    public function getSent($userUid, $doCount)
    {
        //adding configuration fields from the configuration options
        //and forming the criteria object
        if ($doCount && !isset($this->confCasesList['PMTable']) && !empty($this->confCasesList['PMTable'])) {
            $criteria = new Criteria('workflow');
        } else {
            $criteria = $this->addPMFieldsToCriteria('sent');
        }

        $criteria->add(AppCacheViewPeer::APP_STATUS, "DRAFT", CRITERIA::EQUAL);
        $criteria->add(AppCacheViewPeer::USR_UID, $userUid);

        //$criteria->add(AppCacheViewPeer::DEL_FINISH_DATE, null, Criteria::ISNULL);
        $criteria->add(AppCacheViewPeer::APP_THREAD_STATUS, 'OPEN');
        $criteria->add(AppCacheViewPeer::DEL_THREAD_STATUS, 'OPEN');

        return $criteria;
    }

    /**
     * gets the SENT cases list criteria for count
     * param $userUid the current userUid
     * @return Criteria object $Criteria
     */
    public function getSentCountCriteria($userUid)
    {
        /*$criteria = new Criteria('workflow');
        $criteria = $this->addPMFieldsToCriteria('sent');

        $criteria->add(AppCacheViewPeer::USR_UID, $userUid);

        return $criteria;*/
        return $this->getSentListCriteria($userUid);
    }

    /**
     * gets the SENT cases list criteria for list
     * param $userUid the current userUid
     * @return Criteria object $Criteria
     */
    public function getSentListCriteria ($userUid)
    {
        $criteria = $this->addPMFieldsToCriteria('sent');

        //$criteria->addAsColumn('MAX_DEL_INDEX', 'MAX(' . AppDelegationPeer::DEL_INDEX . ')');
        //$criteria->addJoin(AppCacheViewPeer::APP_UID , AppDelegationPeer::APP_UID, Criteria::LEFT_JOIN);
        //$criteria->add(AppCacheViewPeer::USR_UID, $userUid);
        //$criteria->addGroupByColumn(AppCacheViewPeer::APP_UID);
        //$criteria->addGroupByColumn(AppCacheViewPeer::APP_);
        $criteria->add(AppCacheViewPeer::PREVIOUS_USR_UID, $userUid);
        $criteria->add(AppCacheViewPeer::DEL_FINISH_DATE, null, Criteria::ISNULL);

        return $criteria;
    }

    public function getSentListProcessCriteria($userUid)
    {
        $criteria = $this->addPMFieldsToCriteria('sent');
        $criteria->add(AppCacheViewPeer::USR_UID, $userUid);
        return $criteria;
    }

    /**
     * get user's SelfService tasks
     * @param string $sUIDUser
     * @return $rows
     */
    public function getSelfServiceTasks($userUid = '')
    {
        $rows[] = array();
        $tasks  = array();

        //check starting task assigned directly to this user
        $c = new Criteria();
        $c->clearSelectColumns();
        $c->addSelectColumn(TaskPeer::TAS_UID);
        $c->addSelectColumn(TaskPeer::PRO_UID);
        $c->addJoin(TaskPeer::PRO_UID, ProcessPeer::PRO_UID, Criteria::LEFT_JOIN);
        $c->addJoin(TaskPeer::TAS_UID, TaskUserPeer::TAS_UID, Criteria::LEFT_JOIN);
        $c->add(ProcessPeer::PRO_STATUS, 'ACTIVE');
        $c->add(TaskPeer::TAS_ASSIGN_TYPE, 'SELF_SERVICE');
        $c->add(TaskUserPeer::USR_UID, $userUid);

        $rs = TaskPeer::doSelectRS($c);
        $rs->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $rs->next();
        $row = $rs->getRow();

        while (is_array($row)) {
            $tasks[] = $row['TAS_UID'];
            $rs->next();
            $row = $rs->getRow();
        }

        //check groups assigned to SelfService task
        G::LoadClass('groups');
        $group = new Groups();
        $aGroups = $group->getActiveGroupsForAnUser($userUid);

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

        return $tasks;
    }

    public function getSelfServiceCasesByEvaluate($userUid)
    {
        $cases = array();

        //check groups assigned to SelfService task
        G::LoadClass('groups');
        $group = new Groups();
        $aGroups = $group->getActiveGroupsForAnUser($userUid);

        $c = new Criteria();
        $c->clearSelectColumns();
        $c->addSelectColumn(TaskPeer::TAS_UID);
        $c->addSelectColumn(TaskPeer::PRO_UID);
        $c->addSelectColumn(TaskPeer::TAS_GROUP_VARIABLE);
        $c->addJoin(TaskPeer::PRO_UID, ProcessPeer::PRO_UID, Criteria::LEFT_JOIN);
        $c->addJoin(TaskPeer::TAS_UID, TaskUserPeer::TAS_UID, Criteria::LEFT_JOIN);
        $c->add(ProcessPeer::PRO_STATUS, 'ACTIVE');
        $c->add(TaskPeer::TAS_ASSIGN_TYPE, 'SELF_SERVICE');
        $c->add(TaskPeer::TAS_GROUP_VARIABLE, '', Criteria::NOT_EQUAL);
        $rs = TaskPeer::doSelectRS($c);
        $rs->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $rs->next();

        if ($rs->getRecordCount() > 0) {
            if (!class_exists('Cases')) {
                G::loadClass('case');
            }
            $caseInstance = new Cases();
            while ($row = $rs->getRow()) {
                $tasGroupVariable = str_replace(array('@', '#'), '', $row['TAS_GROUP_VARIABLE']);
                $c2 = new Criteria();
                $c2->clearSelectColumns();
                $c2->addSelectColumn(AppDelegationPeer::APP_UID);
                $c2->add(AppDelegationPeer::TAS_UID, $row['TAS_UID']);
                $c2->add(AppDelegationPeer::USR_UID, '');
                $c2->add(AppDelegationPeer::DEL_THREAD_STATUS, 'OPEN');
                $rs2 = AppDelegationPeer::doSelectRS($c2);
                $rs2->setFetchmode(ResultSet::FETCHMODE_ASSOC);
                $rs2->next();
                while ($row2 = $rs2->getRow()) {
                    $caseData = $caseInstance->LoadCase($row2['APP_UID']);
                    if (isset($caseData['APP_DATA'][$tasGroupVariable])) {
                        if (trim($caseData['APP_DATA'][$tasGroupVariable]) != '') {
                            if (in_array(trim($caseData['APP_DATA'][$tasGroupVariable]), $aGroups)) {
                                $cases[] = $row2['APP_UID'];
                            }
                        }
                    }
                    $rs2->next();
                }
                $rs->next();
            }
        }
        return $cases;
    }

    /**
     * gets the UNASSIGNED cases list criteria
     * param $userUid the current userUid
     * param $doCount if true this will return the criteria for count cases only
     * @return Criteria object $Criteria
     */
    public function getUnassigned($userUid, $doCount)
    {
        //get the valid selfservice tasks for this user
        if (!class_exists('Cases')) {
            G::loadClass('case');
        }

        $oCase = new Cases();
        $tasks = $this->getSelfServiceTasks( $userUid );
        //adding configuration fields from the configuration options
        //and forming the criteria object
        if ($doCount && !isset($this->confCasesList['PMTable']) && !empty($this->confCasesList['PMTable'])) {
            $criteria = new Criteria('workflow');
        } else {
            $criteria = $this->addPMFieldsToCriteria('unassigned');
        }

        $criteria->add(AppCacheViewPeer::DEL_FINISH_DATE, null, Criteria::ISNULL);
        $criteria->add(AppCacheViewPeer::USR_UID, '');

        $cases = $this->getSelfServiceCasesByEvaluate($userUid);
        if (!empty($cases)) {
            $criteria->add(
            $criteria->getNewCriterion(AppCacheViewPeer::TAS_UID, $tasks, Criteria::IN)->
              addOr($criteria->getNewCriterion(AppCacheViewPeer::APP_UID, $cases, Criteria::IN))
            );
        } else {
            $criteria->add(AppCacheViewPeer::TAS_UID, $tasks, Criteria::IN);
        }

        return $criteria;
    }

    /**
     * gets the UNASSIGNED cases list criteria for count
     * param $userUid the current userUid
     * @return Criteria object $Criteria
     */
    public function getUnassignedCountCriteria($userUid)
    {
        return $this->getUnassigned($userUid, true);
    }

    /**
     * gets the UNASSIGNED cases list criteria for list
     * param $userUid the current userUid
     * @return Criteria object $Criteria
     */
    public function getUnassignedListCriteria($userUid)
    {
        return $this->getUnassigned($userUid, false);
    }

    /**
     * gets the PAUSED cases list criteria
     * param $userUid the current userUid
     * param $doCount if true this will return the criteria for count cases only
     * @return Criteria object $Criteria
     */
    public function getPaused($userUid, $doCount)
    {
        //adding configuration fields from the configuration options
        //and forming the criteria object
        if ($doCount && !isset($this->confCasesList['PMTable']) && !empty($this->confCasesList['PMTable'])) {
            $criteria = new Criteria('workflow');
        } else {
            $criteria = $this->addPMFieldsToCriteria('paused');
        }

        $criteria->add(AppCacheViewPeer::USR_UID, $userUid);

        //join with APP_DELAY table using APP_UID and DEL_INDEX
        $appDelayConds[] = array(AppCacheViewPeer::APP_UID, AppDelayPeer::APP_UID);
        $appDelayConds[] = array(AppCacheViewPeer::DEL_INDEX, AppDelayPeer::APP_DEL_INDEX);
        $criteria->addJoinMC($appDelayConds, Criteria::LEFT_JOIN);

        $criteria->add(
            $criteria->getNewCriterion(AppDelayPeer::APP_DISABLE_ACTION_USER, null, Criteria::ISNULL)->
            addOr($criteria->getNewCriterion(AppDelayPeer::APP_DISABLE_ACTION_USER, 0))
        );

        $criteria->add(AppDelayPeer::APP_DELAY_UID, null, Criteria::ISNOTNULL);
        $criteria->add(AppDelayPeer::APP_TYPE, 'PAUSE');

        return $criteria;
    }

    /**
     * gets the PAUSED cases list criteria for count
     * param $userUid the current userUid
     * @return Criteria object $Criteria
     */
    public function getPausedCountCriteria($userUid)
    {
        return $this->getPaused($userUid, true);
    }

    /**
     * gets the PAUSED cases list criteria for list
     * param $userUid the current userUid
     * @return Criteria object $Criteria
     */
    public function getPausedListCriteria($userUid)
    {
        return $this->getPaused($userUid, false);
    }

    /**
     * gets the TO_REVISE cases list criteria
     * param $userUid the current userUid
     * param $doCount if true this will return the criteria for count cases only
     * @return Criteria object $Criteria
     */
    public function getToRevise($userUid, $doCount)
    {
        require_once ('classes/model/ProcessUser.php');
        require_once ('classes/model/GroupUser.php');

        //adding configuration fields from the configuration options
        //and forming the criteria object
        $oCriteria = new Criteria('workflow');
        $oCriteria->add(ProcessUserPeer::USR_UID, $userUid);
        $oCriteria->add(ProcessUserPeer::PU_TYPE, 'SUPERVISOR');
        $oDataset = ProcessUserPeer::doSelectRS($oCriteria);
        $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $oDataset->next();
        $aProcesses = array();

        while ($aRow = $oDataset->getRow()) {
            $aProcesses[] = $aRow['PRO_UID'];
            $oDataset->next();
        }

        $oCriteria = new Criteria('workflow');
        $oCriteria->addSelectColumn(ProcessUserPeer::PRO_UID);
        $oCriteria->add(ProcessUserPeer::PU_TYPE, 'GROUP_SUPERVISOR');
        $oCriteria->addJoin(ProcessUserPeer::USR_UID, GroupUserPeer::USR_UID, Criteria::LEFT_JOIN);
        $oCriteria->add(GroupUserPeer::USR_UID, $userUid);
        $oDataset = ProcessUserPeer::doSelectRS($oCriteria);
        $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $oDataset->next();

        while ($aRow = $oDataset->getRow()) {
            $aProcesses[] = $aRow['PRO_UID'];
            $oDataset->next();
        }


        if ($doCount && !isset($this->confCasesList['PMTable']) && !empty($this->confCasesList['PMTable'])) {
            $c = new Criteria('workflow');
        } else {
            $c = $this->addPMFieldsToCriteria('todo');
        }

        $c->add(AppCacheViewPeer::PRO_UID, $aProcesses, Criteria::IN);
        $c->add(AppCacheViewPeer::APP_STATUS, 'TO_DO');
        $c->add(AppCacheViewPeer::DEL_FINISH_DATE, null, Criteria::ISNULL);
        $c->add(AppCacheViewPeer::APP_THREAD_STATUS, 'OPEN');
        $c->add(AppCacheViewPeer::DEL_THREAD_STATUS, 'OPEN');

        return $c;
    }

    /**
     * gets the ToRevise cases list criteria for count
     * param $userUid the current userUid
     * @return Criteria object $Criteria
     */
    public function getToReviseCountCriteria($userUid)
    {
        return $this->getToRevise($userUid, true);
    }

    /**
     * gets the PAUSED cases list criteria for list
     * param $userUid the current userUid
     * @return Criteria object $Criteria
     */
    public function getToReviseListCriteria($userUid)
    {
        return $this->getToRevise($userUid, false);
    }

    /**
     * gets the COMPLETED cases list criteria
     * param $userUid the current userUid
     * param $doCount if true this will return the criteria for count cases only
     * @return Criteria object $Criteria
     */
    public function getCompleted($userUid, $doCount)
    {
        //adding configuration fields from the configuration options
        //and forming the criteria object
        if ($doCount && !isset($this->confCasesList['PMTable']) && !empty($this->confCasesList['PMTable'])) {
            $criteria = new Criteria('workflow');
        } else {
            $criteria = $this->addPMFieldsToCriteria('completed');
        }

        $criteria->add(AppCacheViewPeer::APP_STATUS, "COMPLETED", CRITERIA::EQUAL);
        $criteria->add(AppCacheViewPeer::USR_UID, $userUid);

        //$criteria->add(AppCacheViewPeer::DEL_FINISH_DATE, null, Criteria::ISNULL);
        $criteria->add(AppCacheViewPeer::APP_THREAD_STATUS, 'OPEN');
        $criteria->add(AppCacheViewPeer::DEL_THREAD_STATUS, 'OPEN');


        //$c->add(AppDelegationPeer::DEL_PREVIOUS, '0', Criteria::NOT_EQUAL);
        $criteria->addGroupByColumn(AppCacheViewPeer::APP_UID);

        return $criteria;
    }

    /**
     * gets the COMPLETED cases list criteria for count
     * param $userUid the current userUid
     * @return Criteria object $Criteria
     */
    public function getCompletedCountCriteria($userUid)
    {
        return $this->getCompleted($userUid, true);
    }

    /**
     * gets the COMPLETED cases list criteria for list
     * param $userUid the current userUid
     * @return Criteria object $Criteria
     */
    public function getCompletedListCriteria($userUid)
    {
        return $this->getCompleted($userUid, false);
    }

    /**
     * gets the CANCELLED cases list criteria
     * param $userUid the current userUid
     * param $doCount if true this will return the criteria for count cases only
     * @return Criteria object $Criteria
     */
    public function getCancelled($userUid, $doCount)
    {
        //adding configuration fields from the configuration options
        //and forming the criteria object
        if ($doCount && !isset($this->confCasesList['PMTable']) && !empty($this->confCasesList['PMTable'])) {
            $criteria = new Criteria('workflow');
        } else {
            $criteria = $this->addPMFieldsToCriteria('cancelled');
        }

        $criteria->add(AppCacheViewPeer::APP_STATUS, "CANCELLED", CRITERIA::EQUAL);
        $criteria->add(AppCacheViewPeer::USR_UID, $userUid);
        $criteria->add(AppCacheViewPeer::DEL_THREAD_STATUS, 'CLOSED');

        return $criteria;
    }

    /**
     * gets the CANCELLED cases list criteria for count
     * param $userUid the current userUid
     * @return Criteria object $Criteria
     */
    public function getCancelledCountCriteria($userUid)
    {
        return $this->getCancelled($userUid, true);
    }

    /**
     * gets the CANCELLED cases list criteria for list
     * param $userUid the current userUid
     * @return Criteria object $Criteria
     */
    public function getCancelledListCriteria($userUid)
    {
        return $this->getCancelled($userUid, false);
    }

    /**
     * gets the ADVANCED SEARCH cases list criteria for count
     * param $userUid the current userUid
     * @return Criteria object $Criteria
     */
    public function getSearchCountCriteria()
    {
        //$criteria = new Criteria('workflow');
        //$criteria = $this->addPMFieldsToCriteria('sent');

        //return $criteria;
        //return $this->getSearchCriteria(true);
        return $this->getSearchListCriteria();
    }

    public function getSearchAllCount()
    {
        $criteriaCount = new Criteria('workflow');
        $totalCount = ApplicationPeer::doCount($criteriaCount);

        return $totalCount;
    }

    /**
     * gets the ADVANCED SEARCH cases list criteria for list
     * param $userUid the current userUid
     * @return Criteria object $Criteria
     */
    public function getSearchListCriteria()
    {
        $criteria = $this->addPMFieldsToCriteria('sent');

        $criteria->addAsColumn('MAX_DEL_INDEX', 'MAX(' . AppCacheViewPeer::DEL_INDEX . ')');
        //$criteria->add(AppCacheViewPeer::USR_UID, $userUid);
        $criteria->addGroupByColumn(AppCacheViewPeer::APP_UID);

        return $criteria;
        //return $this->getSearchCriteria(false);
    }

    /**
     * gets the ADVANCED SEARCH cases list criteria for count
     * param $userUid the current userUid
     * @return Criteria object $Criteria
     */
    public function getSimpleSearchCountCriteria()
    {
        //$criteria = new Criteria('workflow');
        $criteria = $this->addPMFieldsToCriteria('sent');
        $criteria->add(AppCacheViewPeer::USR_UID, $_SESSION['USER_LOGGED']);

        return $criteria;
        //return $this->getSearchCriteria(true);
    }

    /**
     * gets the ADVANCED SEARCH cases list criteria for list
     * param $userUid the current userUid
     * @return Criteria object $Criteria
     */
    public function getSimpleSearchListCriteria()
    {
        $criteria = $this->addPMFieldsToCriteria('sent');
        $criteria->addAsColumn('DEL_INDEX', 'MAX(' . AppCacheViewPeer::DEL_INDEX . ')');
        $criteria->add(AppCacheViewPeer::USR_UID, $_SESSION['USER_LOGGED']);
        //$criteria->add(AppCacheViewPeer::USR_UID, $userUid);
        $criteria->addGroupByColumn(AppCacheViewPeer::APP_UID);

        return $criteria;
        //return $this->getSearchCriteria(false);
    }

    /**
     * gets the ADVANCED SEARCH cases list criteria for STATUS
     * param $userUid the current userUid
     * @return Criteria object $Criteria
     */
    public function getSearchStatusCriteria()
    {
        $criteria = new Criteria('workflow');

        return $criteria;
    }

    /**
     * gets the SENT cases list criteria for list
     * param $userUid the current userUid
     * @return Criteria object $Criteria
     */

    /**
     * gets the cases list criteria using the advanced search
     * param $doCount if true this will return the criteria for count cases only
     * @return Criteria object $Criteria
     */
    public function getSearchCriteria($doCount)
    {
        //adding configuration fields from the configuration options
        //and forming the criteria object
        if ($doCount) {
            $criteria = new Criteria('workflow');
        } else {
            $criteria = $this->addPMFieldsToCriteria('sent');
        }

        //$criteria->add(AppCacheViewPeer::APP_STATUS, "TO_DO", CRITERIA::EQUAL);
        $criteria->add(AppCacheViewPeer::DEL_INDEX, 1);

        //$criteria->add(AppCacheViewPeer::DEL_FINISH_DATE, null, Criteria::ISNULL);
        //$criteria->add(AppCacheViewPeer::APP_THREAD_STATUS, 'OPEN');
        //$criteria->add(AppCacheViewPeer::DEL_THREAD_STATUS, 'OPEN');

        return $criteria;
    }

    /**
     * loads the configuration fields from the database based in an action parameter
     * then assemble the Criteria object with these data.
     * @param  String $action
     * @return Criteria object $Criteria
     */
    public function addPMFieldsToCriteria($action)
    {
        $caseColumns = array();

        if (!class_exists('AdditionalTables')) {
            require_once ("classes/model/AdditionalTables.php");
        }

        $caseReaderFields = array();
        $oCriteria = new Criteria('workflow');
        $oCriteria->clearSelectColumns();

        //default configuration fields array
        $defaultFields = $this->getDefaultFields();

        //if there is PMTABLE for this case list:
        if (!empty($this->confCasesList) &&
            isset($this->confCasesList['PMTable']) &&
            trim($this->confCasesList['PMTable']) != ''
        ) {
            //getting the table name
            $oAdditionalTables = AdditionalTablesPeer::retrieveByPK($this->confCasesList['PMTable']);
            $tableName = $oAdditionalTables->getAddTabName();

            foreach ($this->confCasesList['second']['data'] as $fieldData) {
                if (!in_array($fieldData['name'],$defaultFields)) {
                    $fieldName = $tableName . '.' . $fieldData['name'];
                    $oCriteria->addSelectColumn($fieldName);
                } else {
                    switch ($fieldData['fieldType']) {
                        case 'case field':
                            $configTable = 'APP_CACHE_VIEW';
                            break;
                        case 'delay field':
                            $configTable = 'APP_DELAY';
                            break;
                        default:
                            $configTable = 'APP_CACHE_VIEW';
                            break;
                    }

                    $fieldName = $configTable . '.' . $fieldData['name'];
                    $oCriteria->addSelectColumn($fieldName);
                }
            }

            //add the default and hidden DEL_INIT_DATE
            $oCriteria->addSelectColumn('APP_CACHE_VIEW.DEL_INIT_DATE');
            //$oCriteria->addAlias("PM_TABLE", $tableName);

            //Add the JOIN
            $oCriteria->addJoin(AppCacheViewPeer::APP_UID, $tableName.'.APP_UID', Criteria::LEFT_JOIN);

            return $oCriteria;
        } else {
            //else this list do not have a PM Table
            if (is_array($this->confCasesList) && !empty($this->confCasesList['second']['data'])) {
                foreach ($this->confCasesList['second']['data'] as $fieldData) {
                    switch ($fieldData['fieldType']) {
                        case 'case field':
                            $configTable = 'APP_CACHE_VIEW';
                            break;
                        case 'delay field':
                            $configTable = 'APP_DELAY';
                            break;
                        default:
                            $configTable = 'APP_CACHE_VIEW';
                            break;
                    }

                    $fieldName = $configTable . '.' . $fieldData['name'];
                    $oCriteria->addSelectColumn($fieldName);
                }
            } else {
                //foreach ($defaultFields as $field) {
                $oCriteria->addSelectColumn('*');
                //}
            }

            //add the default and hidden DEL_INIT_DATE
            $oCriteria->addSelectColumn('APP_CACHE_VIEW.DEL_INIT_DATE');

            return $oCriteria;
        }
    }

    /**
     * gets the Criteria object for the general cases list.
     * @param Boolean $doCount
     * @return Criteria
     */
    public function getGeneralCases($doCount = 'false')
    {
        if ($doCount && !isset($this->confCasesList['PMTable']) && !empty($this->confCasesList['PMTable'])) {
            $oCriteria = new Criteria('workflow');
        } else {
            $oCriteria = $this->addPMFieldsToCriteria('completed');
        }

        $oCriteria->addAsColumn('DEL_INDEX', 'MIN(' . AppCacheViewPeer::DEL_INDEX . ')');
        $oCriteria->addJoin(AppCacheViewPeer::APP_UID, AppDelegationPeer::APP_UID, Criteria::LEFT_JOIN);
        $oCriteria->add(
            $oCriteria->getNewCriterion(AppCacheViewPeer::APP_THREAD_STATUS, 'OPEN')->
            addOr(
                $oCriteria->getNewCriterion(AppCacheViewPeer::APP_STATUS, 'COMPLETED')->
                addAnd($oCriteria->getNewCriterion(AppDelegationPeer::DEL_PREVIOUS, 0))
            )
        );

        if (!$doCount) {
            $oCriteria->addGroupByColumn(AppCacheViewPeer::APP_UID);
        }

        //$oCriteria->addDescendingOrderByColumn(AppCacheViewPeer::APP_NUMBER);

        return $oCriteria;
    }

    /**
     * gets the ALL cases list criteria for count
     * @return Criteria object $Criteria
     */
    public function getAllCasesCountCriteria($userUid)
    {
        $oCriteria = $this->getGeneralCases(true);
        $oCriteria->add(AppCacheViewPeer::USR_UID, $userUid);

        return $oCriteria;
    }

    /**
     * gets the ALL cases list criteria for list
     * @return Criteria object $Criteria
     */
    public function getAllCasesListCriteria($userUid)
    {
        $oCriteria = $this->getGeneralCases(false);
        $oCriteria->add(AppCacheViewPeer::USR_UID, $userUid);

        return $oCriteria;
    }

    /**
     * gets the ALL cases list criteria for count
     * @return Criteria object $Criteria
     */
    public function getGeneralCountCriteria()
    {
        return $this->getGeneralCases(true);
    }

    /**
     * gets the ALL cases list criteria for list
     * @return Criteria object $Criteria
     */
    public function getGeneralListCriteria()
    {
        return $this->getGeneralCases(false);
    }

    public function getToReassign($doCount)
    {
        if ($doCount && !isset($this->confCasesList['PMTable']) && !empty($this->confCasesList['PMTable'])) {
            $oCriteria = new Criteria('workflow');
        } else {
            $oCriteria = $this->addPMFieldsToCriteria('to_do');
        }

        $oCriteria->add(AppCacheViewPeer::APP_STATUS, 'TO_DO');
        $oCriteria->add(AppCacheViewPeer::APP_CURRENT_USER, '', Criteria::NOT_EQUAL);
        $oCriteria->add(AppCacheViewPeer::DEL_FINISH_DATE, null, Criteria::ISNULL);
        $oCriteria->add(AppCacheViewPeer::APP_THREAD_STATUS, 'OPEN');
        $oCriteria->add(AppCacheViewPeer::DEL_THREAD_STATUS, 'OPEN');
        //$oCriteria->addDescendingOrderByColumn(AppCacheViewPeer::APP_NUMBER);

        return $oCriteria;
    }

    /**
     * gets the ALL cases list criteria for count
     * @return Criteria object $Criteria
     */
    public function getToReassignCountCriteria()
    {
        return $this->getToReassign(true);
    }

    /**
     * gets the ALL cases list criteria for list
     * @return Criteria object $Criteria
     */
    public function getToReassignListCriteria()
    {
        return $this->getToReassign(false);
    }

    public function getDefaultFields()
    {
        return array(
            'APP_UID',
            'DEL_INDEX',
            'APP_NUMBER',
            'APP_STATUS',
            'USR_UID',
            'PREVIOUS_USR_UID',
            'TAS_UID',
            'PRO_UID',
            'DEL_DELEGATE_DATE',
            'DEL_INIT_DATE',
            'DEL_TASK_DUE_DATE',
            'DEL_FINISH_DATE',
            'DEL_THREAD_STATUS',
            'APP_THREAD_STATUS',
            'APP_TITLE',
            'APP_PRO_TITLE',
            'APP_TAS_TITLE',
            'APP_CURRENT_USER',
            'APP_DEL_PREVIOUS_USER',
            'DEL_PRIORITY',
            'DEL_DURATION',
            'DEL_QUEUE_DURATION',
            'DEL_DELAY_DURATION',
            'DEL_STARTED',
            'DEL_FINISHED',
            'DEL_DELAYED',
            'APP_CREATE_DATE',
            'APP_FINISH_DATE',
            'APP_UPDATE_DATE',
            'APP_OVERDUE_PERCENTAGE',
            'APP_DELAY_UID',
            'APP_THREAD_INDEX',
            'APP_DEL_INDEX',
            'APP_TYPE',
            'APP_DELEGATION_USER',
            'APP_ENABLE_ACTION_USER',
            'APP_ENABLE_ACTION_DATE',
            'APP_DISABLE_ACTION_USER',
            'APP_DISABLE_ACTION_DATE',
            'APP_AUTOMATIC_DISABLED_DATE'
        );
    }

    public function setPathToAppCacheFiles($path)
    {
        $this->pathToAppCacheFiles = $path;
    }

    public function getMySQLVersion()
    {
        $con = Propel::getConnection("workflow");
        $stmt = $con->createStatement();
        $sql = "select version()  ";
        $rs1 = $stmt->executeQuery($sql, ResultSet::FETCHMODE_NUM);
        $rs1->next();
        $row = $rs1->getRow();

        return $row[0];
    }

    public function checkGrantsForUser($root = false)
    {
        try {
            if ($root) {
                $con = Propel::getConnection("root");
            } else {
                $con = Propel::getConnection("workflow");
            }

            $stmt = $con->createStatement();
            $sql = "select CURRENT_USER(), USER() ";
            $rs1 = $stmt->executeQuery($sql, ResultSet::FETCHMODE_NUM);
            $rs1->next();
            $row = $rs1->getRow();
            $mysqlUser = str_replace('@', "'@'", $row[0]);

            $super = false;

            $sql = "SELECT *
                    FROM `information_schema`.`USER_PRIVILEGES`
                    WHERE GRANTEE = \"'$mysqlUser'\" and PRIVILEGE_TYPE = 'SUPER' ";

            $rs1 = $stmt->executeQuery($sql, ResultSet::FETCHMODE_ASSOC);
            $rs1->next();
            $row = $rs1->getRow();

            if (is_array($row = $rs1->getRow())) {
                $super = true;
            }

            return array('user' => $mysqlUser, 'super' => $super);
        } catch (Exception $e) {
            return array('error' => true, 'msg' => $e->getMessage());
        }
    }

    public function setSuperForUser($mysqlUser)
    {
        try {
            $con = Propel::getConnection("root");
            $stmt = $con->createStatement();
            $sql = "GRANT SUPER on *.* to '$mysqlUser' ";
            $rs1 = $stmt->executeQuery($sql, ResultSet::FETCHMODE_NUM);

            return array();
        } catch (Exception $e) {
            return array('error' => true, 'msg' => $e->getMessage());
        }
    }

    /**
     * search for table APP_CACHE_VIEW
     * @return void
     *
     */
    public function checkAppCacheView()
    {
        $con = Propel::getConnection("workflow");
        $stmt = $con->createStatement();

        //check if table APP_CACHE_VIEW exists
        $sql = "SHOW TABLES";
        $rs1 = $stmt->executeQuery($sql, ResultSet::FETCHMODE_NUM);
        $rs1->next();
        $found = false;

        while (is_array($row = $rs1->getRow()) && !$found) {
            if (strtolower($row[0]) == 'app_cache_view') {
                $found = true;
            }

            $rs1->next();
        }

        //now count how many records there are ..
        $count = '-';

        if ($found) {
            $oCriteria = new Criteria('workflow');
            $count = AppCacheViewPeer::doCount($oCriteria);
        }

        return array('found' => $found, 'count' => $count);
    }

    /**
     * populate (fill) the table APP_CACHE_VIEW
     * @return void
     */
    public function fillAppCacheView($lang)
    {
        $con = Propel::getConnection("workflow");
        $stmt = $con->createStatement();

        $sql = "truncate table APP_CACHE_VIEW ";
        $rs1 = $stmt->executeQuery($sql, ResultSet::FETCHMODE_ASSOC);

        $filenameSql = $this->pathToAppCacheFiles . 'app_cache_view_insert.sql';

        if (!file_exists($filenameSql)) {
            throw (new Exception("file app_cache_view_insert.sql does not exist "));
        }

        $sql = explode(';', file_get_contents($filenameSql));

        foreach ($sql as $key => $val) {
            $val = str_replace('{lang}', $lang, $val);
            $stmt->executeQuery($val);
        }

        $sql = "select count(*) as CANT from APP_CACHE_VIEW ";
        $rs1 = $stmt->executeQuery($sql, ResultSet::FETCHMODE_ASSOC);
        $rs1->next();
        $row1 = $rs1->getRow();
        $cant = $row1['CANT'];

        return "done $cant rows in table APP_CACHE_VIEW";
    }

    /**
     * Insert an app delegatiojn trigger
     * @return void
     */
    public function triggerAppDelegationInsert($lang, $recreate = false)
    {
        $con = Propel::getConnection("workflow");
        $stmt = $con->createStatement();

        $rs = $stmt->executeQuery('Show TRIGGERS', ResultSet::FETCHMODE_ASSOC);
        $rs->next();
        $row = $rs->getRow();
        $found = false;

        while (is_array($row)) {
            if (strtolower($row['Trigger'] == 'APP_DELEGATION_INSERT') &&
                strtoupper($row['Table']) == 'APP_DELEGATION'
            ) {
                $found = true;
            }

            $rs->next();
            $row = $rs->getRow();
        }

        if ($recreate) {
            $rs = $stmt->executeQuery('DROP TRIGGER IF EXISTS APP_DELEGATION_INSERT');
            $found = false;
        }

        if (!$found) {
            $filenameSql = $this->pathToAppCacheFiles . 'triggerAppDelegationInsert.sql';

            if (!file_exists($filenameSql)) {
                throw (new Exception("file triggerAppDelegationInsert.sql does not exist "));
            }

            $sql = file_get_contents($filenameSql);
            $sql = str_replace('{lang}', $lang, $sql);
            $stmt->executeQuery($sql);

            return 'created';
        }

        return 'exists';
    }

    /**
     * update the App Delegation triggers
     * @return void
     */
    public function triggerAppDelegationUpdate($lang, $recreate = false)
    {
        $con = Propel::getConnection("workflow");
        $stmt = $con->createStatement();

        $rs = $stmt->executeQuery("Show TRIGGERS", ResultSet::FETCHMODE_ASSOC);
        $rs->next();
        $row = $rs->getRow();
        $found = false;

        while (is_array($row)) {
            if (strtolower($row['Trigger'] == 'APP_DELEGATION_UPDATE') &&
                strtoupper($row['Table']) == 'APP_DELEGATION'
            ) {
                $found = true;
            }

            $rs->next();
            $row = $rs->getRow();
        }

        if ($recreate) {
            $rs = $stmt->executeQuery('DROP TRIGGER IF EXISTS APP_DELEGATION_UPDATE');
            $found = false;
        }

        if (!$found) {
            $filenameSql = $this->pathToAppCacheFiles . '/triggerAppDelegationUpdate.sql';

            if (!file_exists($filenameSql)) {
                throw (new Exception("file triggerAppDelegationUpdate.sql does not exist "));
            }

            $sql = file_get_contents($filenameSql);
            $sql = str_replace('{lang}', $lang, $sql);
            $stmt->executeQuery($sql);

            return 'created';
        }

        return 'exists';
    }

    /**
     * update the Application triggers
     * @return void
     */
    public function triggerApplicationUpdate($lang, $recreate = false)
    {
        $con = Propel::getConnection("workflow");
        $stmt = $con->createStatement();

        $rs = $stmt->executeQuery("Show TRIGGERS", ResultSet::FETCHMODE_ASSOC);
        $rs->next();
        $row = $rs->getRow();
        $found = false;

        while (is_array($row)) {
            if (strtolower($row['Trigger'] == 'APPLICATION_UPDATE') && strtoupper($row['Table']) == 'APPLICATION') {
                $found = true;
            }

            $rs->next();
            $row = $rs->getRow();
        }

        if ($recreate) {
            $rs = $stmt->executeQuery('DROP TRIGGER IF EXISTS APPLICATION_UPDATE');
            $found = false;
        }

        if (!$found) {
            $filenameSql = $this->pathToAppCacheFiles . '/triggerApplicationUpdate.sql';

            if (!file_exists($filenameSql)) {
                throw (new Exception("file triggerApplicationUpdate.sql doesn't exist "));
            }

            $sql = file_get_contents($filenameSql);
            $sql = str_replace('{lang}', $lang, $sql);
            $stmt->executeQuery($sql);

            return 'created';
        }

        return 'exists';
    }

    /**
     * update the Application triggers
     * @return void
     */
    public function triggerApplicationDelete($lang, $recreate = false)
    {
        $con = Propel::getConnection("workflow");
        $stmt = $con->createStatement();

        $rs = $stmt->executeQuery("Show TRIGGERS", ResultSet::FETCHMODE_ASSOC);
        $rs->next();
        $row = $rs->getRow();
        $found = false;

        while (is_array($row)) {
            if (strtolower($row['Trigger'] == 'APPLICATION_DELETE') && strtoupper($row['Table']) == 'APPLICATION') {
                $found = true;
            }

            $rs->next();
            $row = $rs->getRow();
        }

        if ($recreate) {
            $rs = $stmt->executeQuery('DROP TRIGGER IF EXISTS APPLICATION_DELETE');
            $found = false;
        }

        if (!$found) {
            $filenameSql = $this->pathToAppCacheFiles . '/triggerApplicationDelete.sql';

            if (!file_exists($filenameSql)) {
                throw (new Exception("file triggerApplicationDelete.sql doesn't exist"));
            }

            $sql = file_get_contents ($filenameSql);
            $sql = str_replace('{lang}', $lang, $sql);
            $stmt->executeQuery($sql);

            return 'created';
        }

        return 'exists';
    }

    public function triggerContentUpdate($lang, $recreate = false)
    {
        $cnn = Propel::getConnection("workflow");
        $stmt = $cnn->createStatement();

        $rs = $stmt->executeQuery("Show TRIGGERS", ResultSet::FETCHMODE_ASSOC);
        $found = false;

        while ($rs->next()) {
            $row = $rs->getRow();

            if (strtolower($row["Trigger"] == "CONTENT_UPDATE") && strtoupper($row["Table"]) == "CONTENT") {
                $found = true;
            }
        }

        if ($recreate) {
            $rs = $stmt->executeQuery("DROP TRIGGER IF EXISTS CONTENT_UPDATE");
            $found = false;
        }

        if (!$found) {
            $filenameSql = $this->pathToAppCacheFiles . PATH_SEP . "triggerContentUpdate.sql";

            if (!file_exists($filenameSql)) {
                throw (new Exception("file triggerContentUpdate.sql doesn't exist"));
            }

            $sql = file_get_contents($filenameSql);
            $sql = str_replace("{lang}", $lang, $sql);

            $stmt->executeQuery($sql);

            return "created";
        }

        return "exists";
    }

    /**
     * Retrieve the SQL code to create the APP_CACHE_VIEW triggers.
     *
     * @return array each value is a SQL statement to create a trigger.
     */
    public function getTriggers($lang)
    {
        $triggerFiles = array(
            'triggerApplicationDelete.sql',
            'triggerApplicationUpdate.sql',
            'triggerAppDelegationUpdate.sql',
            'triggerAppDelegationInsert.sql',
            'triggerContentUpdate.sql'
        );

        $triggers = array();

        foreach ($triggerFiles as $triggerFile) {
            $trigger = file_get_contents("{$this->pathToAppCacheFiles}/$triggerFile");

            if ($trigger === false) {
                throw new Exception("Could not read trigger contents in $triggerFile");
            }

            $trigger = str_replace('{lang}', $lang, $trigger);
            $triggers[$triggerFile] = $trigger;
        }

        return $triggers;
    }

    public function getFormatedUser($sFormat, $aCaseUser, $userIndex)
    {
        require_once('classes/model/Users.php');

        $oUser = new Users();

        try {
            $aCaseUserRecord = $oUser->load($aCaseUser[$userIndex]);
            $sCaseUser = G::getFormatUserList ($sFormat,$aCaseUserRecord);
            // . ' (' . $aCaseUserRecord['USR_USERNAME'] . ')';]
        } catch (Exception $e) {
            $sCaseUser = '';
        }

        return $sCaseUser;
    }

    public function replaceRowUserData($rowData)
    {
        try {
            G::loadClass('configuration');
            $oConfig = new Configuration();
            $aConfig = $oConfig->load('ENVIRONMENT_SETTINGS');
            $aConfig = unserialize($aConfig['CFG_VALUE']);
        } catch (Exception $e) {
            // if there is no configuration record then.
            $aConfig['format'] = '@userName';
        }

        if (isset($rowData['USR_UID'])&&isset($rowData['APP_CURRENT_USER'])) {
            $rowData['APP_CURRENT_USER'] = $this->getFormatedUser($aConfig['format'],$rowData,'USR_UID');
        }

        if (isset($rowData['PREVIOUS_USR_UID'])&&isset($rowData['APP_DEL_PREVIOUS_USER'])) {
            $rowData['APP_DEL_PREVIOUS_USER'] = $this->getFormatedUser($aConfig['format'],$rowData,'PREVIOUS_USR_UID');
        }

        return ($rowData);
    }

    //Added By Qennix
    public function getTotalCasesByAllUsers()
    {
        $oCriteria = new Criteria("workflow");

        $oCriteria->addSelectColumn(AppCacheViewPeer::USR_UID);
        $oCriteria->addAsColumn("CNT", "COUNT(DISTINCT(APP_UID))");
        $oCriteria->addGroupByColumn(AppCacheViewPeer::USR_UID);
        $dat = AppCacheViewPeer::doSelectRS($oCriteria);
        $dat->setFetchmode(ResultSet::FETCHMODE_ASSOC);

        $aRows = array();

        while ($dat->next()) {
            $row = $dat->getRow();

            $aRows[$row["USR_UID"]] = $row["CNT"];
        }

        return $aRows;
    }

    public function appTitleByTaskCaseLabelUpdate($taskUid, $lang, $cron = 0)
    {
        $taskDefTitle = null;

        $criteria = new Criteria("workflow");

        $criteria->addSelectColumn(ContentPeer::CON_VALUE);
        $criteria->add(ContentPeer::CON_CATEGORY, "TAS_DEF_TITLE");
        $criteria->add(ContentPeer::CON_ID, $taskUid);
        $criteria->add(ContentPeer::CON_LANG, $lang);

        $rsCriteria = ContentPeer::doSelectRS($criteria);
        $rsCriteria->setFetchmode(ResultSet::FETCHMODE_ASSOC);

        while ($rsCriteria->next()) {
            $row = $rsCriteria->getRow();

            $taskDefTitle = $row["CON_VALUE"];
        }

        //Get cases
        $criteriaAPPCV = new Criteria("workflow");

        $criteriaAPPCV->setDistinct();
        $criteriaAPPCV->addSelectColumn(AppCacheViewPeer::APP_UID);
        $criteriaAPPCV->add(AppCacheViewPeer::DEL_THREAD_STATUS, "OPEN");
        $criteriaAPPCV->add(AppCacheViewPeer::TAS_UID, $taskUid);

        $rsCriteriaAPPCV = AppCacheViewPeer::doSelectRS($criteriaAPPCV);
        $rsCriteriaAPPCV->setFetchmode(ResultSet::FETCHMODE_ASSOC);

        while ($rsCriteriaAPPCV->next()) {
            if ($cron == 1) {
                $arrayCron = unserialize(trim(@file_get_contents(PATH_DATA . "cron")));
                $arrayCron["processcTimeStart"] = time();
                @file_put_contents(PATH_DATA . "cron", serialize($arrayCron));
            }

            $row = $rsCriteriaAPPCV->getRow();

            $appcvAppUid = $row["APP_UID"];

            //Current task?
            $criteria = new Criteria("workflow");

            $criteria->addSelectColumn(AppCacheViewPeer::APP_UID);
            $criteria->add(AppCacheViewPeer::APP_UID, $appcvAppUid);
            $criteria->add(AppCacheViewPeer::DEL_THREAD_STATUS, "OPEN");
            $criteria->add(AppCacheViewPeer::TAS_UID, $taskUid);

            $rsCriteria = AppCacheViewPeer::doSelectRS($criteria);
            $rsCriteria->setFetchmode(ResultSet::FETCHMODE_ASSOC);

            if ($rsCriteria->next()) {
                $appTitle = $taskDefTitle;

                $app = new Application();
                $arrayAppField = $app->Load($appcvAppUid);

                $appTitle    = (!empty($appTitle))? $appTitle : "#" . $arrayAppField["APP_NUMBER"];
                $appTitleNew = G::replaceDataField($appTitle, unserialize($arrayAppField["APP_DATA"]));

                if (isset($arrayAppField["APP_TITLE"]) && $arrayAppField["APP_TITLE"] != $appTitleNew) {
                    //Updating the value in content, where...
                    $criteria1 = new Criteria("workflow");

                    $criteria1->add(ContentPeer::CON_CATEGORY, "APP_TITLE");
                    $criteria1->add(ContentPeer::CON_ID, $appcvAppUid);
                    $criteria1->add(ContentPeer::CON_LANG, $lang);

                    //Update set
                    $criteria2 = new Criteria("workflow");

                    $criteria2->add(ContentPeer::CON_VALUE, $appTitleNew);

                    BasePeer::doUpdate($criteria1, $criteria2, Propel::getConnection("workflow"));
                }
            }
        }
    }
}

