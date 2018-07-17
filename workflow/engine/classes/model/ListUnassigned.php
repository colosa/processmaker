<?php

require_once 'classes/model/om/BaseListUnassigned.php';


/**
 * Skeleton subclass for representing a row from the 'LIST_UNASSIGNED' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    classes.model
 */
// @codingStandardsIgnoreStart
class ListUnassigned extends BaseListUnassigned
{
    private $additionalClassName = '';
    private $total = 0;

    /**
     * Create List Unassigned Table
     *
     * @param type $data
     * @return type
     *
     */
    public function create($data)
    {
        if (!empty($data['PRO_UID']) && empty($data['PRO_ID'])) {
            $p = new Process();
            $data['PRO_ID'] =  $p->load($data['PRO_UID'])['PRO_ID'];
        }
        if (!empty($data['TAS_UID'])) {
            $t = new Task();
            $data['TAS_ID'] = $t->load($data['TAS_UID'])['TAS_ID'];
        }
        $con = Propel::getConnection(ListUnassignedPeer::DATABASE_NAME);
        try {
            $this->fromArray($data, BasePeer::TYPE_FIELDNAME);
            if ($this->validate()) {
                $result = $this->save();
            } else {
                $e = new Exception("Failed Validation in class " . get_class($this) . ".");
                $e->aValidationFailures = $this->getValidationFailures();
                throw ($e);
            }
            $con->commit();
            return $result;
        } catch (Exception $e) {
            $con->rollback();
            throw ($e);
        }
    }

    /**
     *  Update List Unassigned Table
     *
     * @param type $data
     * @return type
     * @throws type
     */
    public function update($data)
    {
        if (!empty($data['TAS_UID'])) {
            $t = new Task();
            $data['TAS_ID'] = $t->load($data['TAS_UID'])['TAS_ID'];
        }
        $con = Propel::getConnection(ListUnassignedPeer::DATABASE_NAME);
        try {
            $con->begin();
            $this->setNew(false);
            $this->fromArray($data, BasePeer::TYPE_FIELDNAME);
            if ($this->validate()) {
                $result = $this->save();
                $con->commit();
                return $result;
            } else {
                $con->rollback();
                throw (new Exception("Failed Validation in class " . get_class($this) . "."));
            }
        } catch (Exception $e) {
            $con->rollback();
            throw ($e);
        }
    }

    /**
     * Remove List Unassigned
     *
     * @param type $seqName
     * @return type
     * @throws type
     *
     */
    public function remove($appUid, $delIndex)
    {
        $con = Propel::getConnection(ListUnassignedPeer::DATABASE_NAME);
        try {
            $this->setAppUid($appUid);
            $this->setDelIndex($delIndex);

            $con->begin();
            $this->delete();
            $con->commit();
        } catch (Exception $e) {
            $con->rollback();
            throw ($e);
        }
    }

    public function newRow($data, $delPreviusUsrUid)
    {
        $data['DEL_PREVIOUS_USR_UID'] = $delPreviusUsrUid;
        $data['DEL_DUE_DATE'] = isset($data['DEL_TASK_DUE_DATE']) ? $data['DEL_TASK_DUE_DATE'] : '';

        $criteria = new Criteria();
        $criteria->addSelectColumn(ApplicationPeer::APP_NUMBER);
        $criteria->addSelectColumn(ApplicationPeer::APP_UPDATE_DATE);
        $criteria->add(ApplicationPeer::APP_UID, $data['APP_UID'], Criteria::EQUAL);
        $dataset = ApplicationPeer::doSelectRS($criteria);
        $dataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $dataset->next();
        $aRow = $dataset->getRow();
        $data = array_merge($data, $aRow);

        $criteria = new Criteria();
        $criteria->addSelectColumn(ProcessPeer::PRO_TITLE);
        $criteria->add(ProcessPeer::PRO_UID, $data['PRO_UID'], Criteria::EQUAL);
        $dataset = ProcessPeer::doSelectRS($criteria);
        $dataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $dataset->next();
        $aRow = $dataset->getRow();
        $data['APP_PRO_TITLE'] = $aRow['PRO_TITLE'];


        $criteria = new Criteria();
        $criteria->addSelectColumn(TaskPeer::TAS_TITLE);
        $criteria->add(TaskPeer::TAS_UID, $data['TAS_UID'], Criteria::EQUAL);
        $dataset = TaskPeer::doSelectRS($criteria);
        $dataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $dataset->next();
        $aRow = $dataset->getRow();
        $data['APP_TAS_TITLE'] = $aRow['TAS_TITLE'];


        $data['APP_PREVIOUS_USER'] = '';
        if ($data['DEL_PREVIOUS_USR_UID'] != '') {
            $criteria = new Criteria();
            $criteria->addSelectColumn(UsersPeer::USR_USERNAME);
            $criteria->addSelectColumn(UsersPeer::USR_FIRSTNAME);
            $criteria->addSelectColumn(UsersPeer::USR_LASTNAME);
            $criteria->add(UsersPeer::USR_UID, $data['DEL_PREVIOUS_USR_UID'], Criteria::EQUAL);
            $dataset = UsersPeer::doSelectRS($criteria);
            $dataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $dataset->next();
            $aRow = $dataset->getRow();
            $data['DEL_PREVIOUS_USR_USERNAME']  = $aRow['USR_USERNAME'];
            $data['DEL_PREVIOUS_USR_FIRSTNAME'] = $aRow['USR_FIRSTNAME'];
            $data['DEL_PREVIOUS_USR_LASTNAME']  = $aRow['USR_LASTNAME'];
        }

        self::create($data);
        return true;
    }

    /**
     * This function add restriction in the query related to the filters
     * @param Criteria $criteria, must be contain only select of columns
     * @param array $filters
     * @param array $additionalColumns information about the new columns related to custom cases list
     * @throws PropelException
     */
    public function loadFilters(&$criteria, $filters, $additionalColumns = array())
    {
        $filter = isset($filters['filter']) ? $filters['filter'] : '';
        $search = isset($filters['search']) ? $filters['search'] : '';
        $caseLink = isset($filters['caseLink']) ? $filters['caseLink'] : '';
        $process = isset($filters['process']) ? $filters['process'] : '';
        $category = isset($filters['category']) ? $filters['category'] : '';
        $newestthan = isset($filters['newestthan']) ? $filters['newestthan'] : '';
        $oldestthan = isset($filters['oldestthan']) ? $filters['oldestthan'] : '';
        $appUidCheck = isset($filters['appUidCheck']) ? $filters['appUidCheck'] : array();

        //Filter Search
        if ($search != '') {
            //Check if we need to search to the APP_UID
            if (!empty($caseLink)) {
                $criteria->add(ListUnassignedPeer::APP_UID, $search, Criteria::EQUAL);
            } else {
                //If we have additional tables configured in the custom cases list, prepare the variables for search
                $casesList = new \ProcessMaker\BusinessModel\Cases();
                $casesList->getSearchCriteriaListCases($criteria, __CLASS__ . 'Peer', $search, $this->additionalClassName, $additionalColumns);
            }
        }

        //Filter Process Id
        if ($process != '') {
            $criteria->add(ListUnassignedPeer::PRO_UID, $process, Criteria::EQUAL);
        }

        //Filter Category
        if ($category != '') {
            $criteria->addSelectColumn(ProcessPeer::PRO_CATEGORY);
            $aConditions   = array();
            $aConditions[] = array(ListUnassignedPeer::PRO_UID, ProcessPeer::PRO_UID);
            $aConditions[] = array(ProcessPeer::PRO_CATEGORY, "'" . $category . "'");
            $criteria->addJoinMC($aConditions, Criteria::INNER_JOIN);
        }

        //Those filters: $newestthan, $oldestthan is used from mobile GET /light/unassigned
        if ($newestthan != '') {
            $criteria->add(
                $criteria->getNewCriterion(ListUnassignedPeer::DEL_DELEGATE_DATE, $newestthan, Criteria::GREATER_THAN)
            );
        }

        if ($oldestthan != '') {
            $criteria->add(
                $criteria->getNewCriterion(ListUnassignedPeer::DEL_DELEGATE_DATE, $oldestthan, Criteria::LESS_THAN)
            );
        }

        //Review in the specific lot of cases
        if (!empty($appUidCheck)) {
            $criteria->add(ListUnassignedPeer::APP_UID, $appUidCheck, Criteria::IN);
        }
    }

    /**
     * This function get the information in the corresponding cases list
     * @param string $usr_uid, must be show cases related to this user
     * @param array $filters for apply in the result
     * @param callable $callbackRecord
     * @return array $data
     * @throws PropelException
     */
    public function loadList($usr_uid, $filters = array(), callable $callbackRecord = null)
    {
        $pmTable = new PmTable();
        $criteria = $pmTable->addPMFieldsToList('unassigned');
        $this->additionalClassName = $pmTable->tableClassName;
        $additionalColumns = $criteria->getSelectColumns();

        $criteria->addSelectColumn(ListUnassignedPeer::APP_UID);
        $criteria->addSelectColumn(ListUnassignedPeer::DEL_INDEX);
        $criteria->addSelectColumn(ListUnassignedPeer::TAS_UID);
        $criteria->addSelectColumn(ListUnassignedPeer::PRO_UID);
        $criteria->addSelectColumn(ListUnassignedPeer::APP_NUMBER);
        $criteria->addSelectColumn(ListUnassignedPeer::APP_TITLE);
        $criteria->addSelectColumn(ListUnassignedPeer::APP_PRO_TITLE);
        $criteria->addSelectColumn(ListUnassignedPeer::APP_TAS_TITLE);
        $criteria->addSelectColumn(ListUnassignedPeer::APP_UPDATE_DATE);
        $criteria->addSelectColumn(ListUnassignedPeer::DEL_PREVIOUS_USR_USERNAME);
        $criteria->addSelectColumn(ListUnassignedPeer::DEL_PREVIOUS_USR_FIRSTNAME);
        $criteria->addSelectColumn(ListUnassignedPeer::DEL_PREVIOUS_USR_LASTNAME);
        $criteria->addSelectColumn(ListUnassignedPeer::DEL_PREVIOUS_USR_UID);
        $criteria->addSelectColumn(ListUnassignedPeer::DEL_DELEGATE_DATE);
        $criteria->addSelectColumn(ListUnassignedPeer::DEL_DUE_DATE);
        $criteria->addSelectColumn(ListUnassignedPeer::DEL_PRIORITY);
        //Self Service Value Based Assignment
        $criteria = $this->getCriteriaWhereSelfService($criteria, $usr_uid);

        //Apply some filters
        self::loadFilters($criteria, $filters, $additionalColumns);

        //We will be defined the sort
        $casesList = new \ProcessMaker\BusinessModel\Cases();
        $sort = $casesList->getSortColumn(
            __CLASS__ . 'Peer',
            BasePeer::TYPE_FIELDNAME,
            empty($filters['sort']) ? "DEL_DELEGATE_DATE" : $filters['sort'],
            "DEL_DELEGATE_DATE",
            $this->additionalClassName,
            $additionalColumns
        );

        $dir   = isset($filters['dir']) ? $filters['dir'] : "ASC";
        $start = isset($filters['start']) ? $filters['start'] : "0";
        $limit = isset($filters['limit']) ? $filters['limit'] : "25";
        $paged = isset($filters['paged']) ? $filters['paged'] : 1;
        $count = isset($filters['count']) ? $filters['count'] : 1;
        if ($dir == "DESC") {
            $criteria->addDescendingOrderByColumn($sort);
        } else {
            $criteria->addAscendingOrderByColumn($sort);
        }
        $this->total = ListUnassignedPeer::doCount($criteria);
        if ($paged == 1) {
            $criteria->setLimit($limit);
            $criteria->setOffset($start);
        }
        $dataset = ListUnassignedPeer::doSelectRS($criteria);
        $dataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $aPriorities = array('1' => 'VL', '2' => 'L', '3' => 'N', '4' => 'H', '5' => 'VH');

        $data = array();
        while ($dataset->next()) {
            $aRow = (is_null($callbackRecord))? $dataset->getRow() : $callbackRecord($dataset->getRow());
            $aRow['DEL_PRIORITY'] = (isset($aRow['DEL_PRIORITY']) &&
                is_numeric($aRow['DEL_PRIORITY']) &&
                $aRow['DEL_PRIORITY'] <= 5 &&
                $aRow['DEL_PRIORITY'] > 0) ? $aRow['DEL_PRIORITY'] : 3;
            $aRow['DEL_PRIORITY'] = G::LoadTranslation("ID_PRIORITY_{$aPriorities[$aRow['DEL_PRIORITY']]}");
            $data[] = $aRow;
        }
        return $data;
    }

    /**
     * Get SelfService Value Based
     *
     * @param string $userUid
     * @return array $arrayAppAssignSelfServiceValueData
     * @throws Exception
     */
    public function getSelfServiceCasesByEvaluate($userUid)
    {
        try {
            $arrayAppAssignSelfServiceValueData = array();

            //Get APP_UIDs
            $group = new Groups();
            $arrayUid   = $group->getActiveGroupsForAnUser($userUid); //Set UIDs of Groups (Groups of User)
            $arrayUid[] = $userUid;                                   //Set UID of User

            $criteria = new Criteria("workflow");

            $criteria->setDistinct();
            $criteria->addSelectColumn(AppAssignSelfServiceValuePeer::APP_UID);
            $criteria->addSelectColumn(AppAssignSelfServiceValuePeer::DEL_INDEX);
            $criteria->addSelectColumn(AppAssignSelfServiceValuePeer::TAS_UID);

            $criteria->add(
                AppAssignSelfServiceValuePeer::ID,
                AppAssignSelfServiceValuePeer::ID.
                " IN (SELECT ".AppAssignSelfServiceValueGroupPeer::ID.
                " FROM ".AppAssignSelfServiceValueGroupPeer::TABLE_NAME.
                " WHERE ".AppAssignSelfServiceValueGroupPeer::GRP_UID." IN ('".
                implode("','", $arrayUid)."'))",
                Criteria::CUSTOM
            );

            $rsCriteria = AppAssignSelfServiceValuePeer::doSelectRS($criteria);
            $rsCriteria->setFetchmode(ResultSet::FETCHMODE_ASSOC);

            while ($rsCriteria->next()) {
                $row = $rsCriteria->getRow();

                $arrayAppAssignSelfServiceValueData[] = array(
                    "APP_UID" => $row["APP_UID"],
                    "DEL_INDEX" => $row["DEL_INDEX"],
                    "TAS_UID" => $row["TAS_UID"]
                );
            }

            //Return
            return $arrayAppAssignSelfServiceValueData;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * get user's SelfService tasks
     * @param string $userUid
     * @return array $tasks
     */
    public function getSelfServiceTasks($userUid = '')
    {
        $rows[] = array();
        $tasks  = array();

        //check self service tasks assigned directly to this user
        $c = new Criteria();
        $c->clearSelectColumns();
        $c->addSelectColumn(TaskPeer::TAS_UID);
        $c->addSelectColumn(TaskPeer::PRO_UID);
        $c->addJoin(TaskPeer::PRO_UID, ProcessPeer::PRO_UID, Criteria::LEFT_JOIN);
        $c->addJoin(TaskPeer::TAS_UID, TaskUserPeer::TAS_UID, Criteria::LEFT_JOIN);
        $c->add(ProcessPeer::PRO_STATUS, 'ACTIVE');
        $c->add(TaskPeer::TAS_ASSIGN_TYPE, 'SELF_SERVICE');
        $c->add(TaskPeer::TAS_GROUP_VARIABLE, '');
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
        $c->add(TaskPeer::TAS_GROUP_VARIABLE, '');
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

    /**
     * Returns the number of cases of a user
     * @param string $userUid
     * @param array $filters
     * @return int $total
     */
    public function getCountList($userUid, $filters = array())
    {
        $criteria = new Criteria('workflow');
        $this->getCriteriaWhereSelfService($criteria, $userUid);
        $total = ListUnassignedPeer::doCount($criteria);
        return (int)$total;
    }

    /**
     * This function get the where criteria for the SelfService and SelfService value based
     *
     * @param criteria $criteria
     * @param string $userUid
     * @return criteria $criteria
     */
    public function getCriteriaWhereSelfService($criteria, $userUid)
    {
        $tasks = $this->getSelfServiceTasks($userUid);
        $aSelfServiceValueBased = $this->getSelfServiceCasesByEvaluate($userUid);

        if (!empty($aSelfServiceValueBased)) {
            //Self Service Value Based Assignment
            $criterionAux = null;

            //Load Self Service Value Based Assignment
            $firstRow = current($aSelfServiceValueBased);
            $criterionAux = sprintf(
                "((
                    LIST_UNASSIGNED.APP_UID='%s' AND 
                    LIST_UNASSIGNED.DEL_INDEX=%d AND 
                    LIST_UNASSIGNED.TAS_UID='%s'
                ) ",
                $firstRow["APP_UID"],
                $firstRow["DEL_INDEX"],
                $firstRow["TAS_UID"]
            );
            foreach (array_slice($aSelfServiceValueBased, 1) as $value) {
                $criterionAux .= sprintf(
                    " OR (
                        LIST_UNASSIGNED.APP_UID='%s' AND 
                        LIST_UNASSIGNED.DEL_INDEX=%d AND 
                        LIST_UNASSIGNED.TAS_UID='%s'
                    ) ",
                    $value["APP_UID"],
                    $value["DEL_INDEX"],
                    $value["TAS_UID"]
                );
            }
            $criterionAux .= ")";
            //And Load SelfService
            $criteria->add(
                $criteria->getNewCriterion(
                    ListUnassignedPeer::TAS_UID,
                    $tasks,
                    Criteria::IN
                )->addOr(
                    $criteria->getNewCriterion(
                        ListUnassignedPeer::TAS_UID,
                        $criterionAux,
                        Criteria::CUSTOM
                    )
                )
            );
        } else {
            //Self Service
            $criteria->add(ListUnassignedPeer::TAS_UID, $tasks, Criteria::IN);
        }

        return $criteria;
    }
}
