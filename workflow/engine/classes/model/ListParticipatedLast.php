<?php

// @codingStandardsIgnoreStart
require_once 'classes/model/om/BaseListParticipatedLast.php';

/**
 * Skeleton subclass for representing a row from the 'LIST_PARTICIPATED_LAST' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 */
class ListParticipatedLast extends BaseListParticipatedLast
{
    private $additionalClassName = '';

    /**
     * Create List Participated History Table.
     *
     * @param type $data
     *
     * @return type
     */
    public function create($data)
    {
        $criteria = new Criteria();
        $criteria->addSelectColumn(ApplicationPeer::APP_STATUS);
        $criteria->add(ApplicationPeer::APP_UID, $data['APP_UID'], Criteria::EQUAL);
        $dataset = UsersPeer::doSelectRS($criteria);
        $dataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $dataset->next();
        $aRow = $dataset->getRow();
        $data['APP_STATUS'] = $aRow['APP_STATUS'];

        $currentInformation = array();
        if ($data['USR_UID'] != 'SELF_SERVICES') {
            if ($data['USR_UID'] != '') {
                $criteria = new Criteria();
                $criteria->addSelectColumn(UsersPeer::USR_USERNAME);
                $criteria->addSelectColumn(UsersPeer::USR_FIRSTNAME);
                $criteria->addSelectColumn(UsersPeer::USR_LASTNAME);
                $criteria->add(UsersPeer::USR_UID, $data['USR_UID'], Criteria::EQUAL);
                $dataset = UsersPeer::doSelectRS($criteria);
                $dataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
                $dataset->next();
                $aRow = $dataset->getRow();
                $data['DEL_CURRENT_USR_USERNAME'] = $aRow['USR_USERNAME'];
                $data['DEL_CURRENT_USR_FIRSTNAME'] = $aRow['USR_FIRSTNAME'];
                $data['DEL_CURRENT_USR_LASTNAME'] = $aRow['USR_LASTNAME'];
                $data['DEL_CURRENT_TAS_TITLE'] = $data['APP_TAS_TITLE'];
                $currentInformation = array(
                    'DEL_CURRENT_USR_USERNAME' => $data['DEL_CURRENT_USR_USERNAME'],
                    'DEL_CURRENT_USR_FIRSTNAME' => $data['DEL_CURRENT_USR_FIRSTNAME'],
                    'DEL_CURRENT_USR_LASTNAME' => $data['DEL_CURRENT_USR_LASTNAME'],
                    'DEL_CURRENT_TAS_TITLE' => $data['APP_TAS_TITLE']
                );
            }
        } else {
            $getData['USR_UID'] = $data['USR_UID_CURRENT'];
            $getData['APP_UID'] = $data['APP_UID'];
            $row = $this->getRowFromList($getData);
            if (is_array($row) && sizeof($row)) {
                $currentInformation = array(
                    'DEL_CURRENT_USR_USERNAME' => '',
                    'DEL_CURRENT_USR_FIRSTNAME' => '',
                    'DEL_CURRENT_USR_LASTNAME' => '',
                    'DEL_CURRENT_TAS_TITLE' => $data['APP_TAS_TITLE']
                );
            }
        }

        if ($this->primaryKeysExists($data)) {
            return;
        }

        if (!empty($data['PRO_UID']) && empty($data['PRO_ID'])) {
            $p = new Process();
            $data['PRO_ID'] = $p->load($data['PRO_UID'])['PRO_ID'];
        }
        if (!empty($data['USR_UID'])) {
            $u = new Users();
            $data['USR_ID'] =  $data['USR_UID']==='SELF_SERVICES' ? null : $u->load($data['USR_UID'])['USR_ID'];
        }
        if (!empty($data['TAS_UID'])) {
            $t = new Task();
            $data['TAS_ID'] = $t->load($data['TAS_UID'])['TAS_ID'];
        }
        if (!empty($data['APP_STATUS'])) {
            $data['APP_STATUS_ID'] = Application::$app_status_values[$data['APP_STATUS']];
        }
        //We will update the current information
        if (count($currentInformation) > 0) {
            $this->updateCurrentInfoByAppUid($data['APP_UID'], $currentInformation);
        }

        $con = Propel::getConnection(ListParticipatedLastPeer::DATABASE_NAME);
        try {
            $this->fromArray($data, BasePeer::TYPE_FIELDNAME);
            if ($this->validate()) {
                $result = $this->save();
            } else {
                $e = new Exception('Failed Validation in class '.get_class($this).'.');
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
     * This function update the row related to the appUid with the current information
     * @param string $appUid
     * @param array $currentInformation
     * @return void
    */
    private function updateCurrentInfoByAppUid($appUid, $currentInformation)
    {
        //Update - WHERE
        $criteriaWhere = new Criteria('workflow');
        $criteriaWhere->add(ListParticipatedLastPeer::APP_UID, $appUid, Criteria::EQUAL);
        //Update - SET
        $criteriaSet = new Criteria('workflow');
        $criteriaSet->add(ListParticipatedLastPeer::DEL_CURRENT_USR_USERNAME, $currentInformation['DEL_CURRENT_USR_USERNAME']);
        $criteriaSet->add(ListParticipatedLastPeer::DEL_CURRENT_USR_FIRSTNAME, $currentInformation['DEL_CURRENT_USR_FIRSTNAME']);
        $criteriaSet->add(ListParticipatedLastPeer::DEL_CURRENT_USR_LASTNAME, $currentInformation['DEL_CURRENT_USR_LASTNAME']);
        $criteriaSet->add(ListParticipatedLastPeer::DEL_CURRENT_TAS_TITLE, $currentInformation['DEL_CURRENT_TAS_TITLE']);

        BasePeer::doUpdate($criteriaWhere, $criteriaSet, Propel::getConnection('workflow'));
    }

    /**
     *  Update List Participated History Table.
     *
     * @param type $data
     *
     * @return type
     *
     * @throws type
     */
    public function update($data)
    {
        $data['DEL_THREAD_STATUS'] = (isset($data['DEL_THREAD_STATUS'])) ? $data['DEL_THREAD_STATUS'] : 'OPEN';
        if (!empty($data['USR_UID'])) {
            $u = new Users();
            $data['USR_ID'] = $data['USR_UID'] === 'SELF_SERVICES' ? null : $u->load($data['USR_UID'])['USR_ID'];
        }
        if (!empty($data['TAS_UID'])) {
            $t = new Task();
            $data['TAS_ID'] = $t->load($data['TAS_UID'])['TAS_ID'];
        }
        if (!empty($data['APP_STATUS'])) {
            $data['APP_STATUS_ID'] = Application::$app_status_values[$data['APP_STATUS']];
        }
        $con = Propel::getConnection(ListParticipatedLastPeer::DATABASE_NAME);
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
                throw (new Exception('Failed Validation in class '.get_class($this).'.'));
            }
        } catch (Exception $e) {
            $con->rollback();
            throw ($e);
        }
    }
    /**
     * Refresh List Participated Last.
     *
     * @param type $seqName
     *
     * @return type
     *
     * @throws type
     */
    public function refresh($data, $isSelfService = false)
    {
        $data['APP_STATUS'] = (empty($data['APP_STATUS'])) ? 'TO_DO' : $data['APP_STATUS'];
        if (!empty($data['USR_UID'])) {
            $u = new Users();
            $data['USR_ID'] = $data['USR_UID'] === 'SELF_SERVICES' ? null : $u->load($data['USR_UID'])['USR_ID'];
        }
        if (!empty($data['TAS_UID'])) {
            $t = new Task();
            $data['TAS_ID'] = $t->load($data['TAS_UID'])['TAS_ID'];
        }
        if (!empty($data['APP_STATUS'])) {
            $data['APP_STATUS_ID'] = Application::$app_status_values[$data['APP_STATUS']];
        }
        if (!$isSelfService) {
            if ($data['USR_UID'] == '') {
                return;
            }

            $criteria = new Criteria();
            $criteria->addSelectColumn(UsersPeer::USR_USERNAME);
            $criteria->addSelectColumn(UsersPeer::USR_FIRSTNAME);
            $criteria->addSelectColumn(UsersPeer::USR_LASTNAME);
            $criteria->add(UsersPeer::USR_UID, $data['USR_UID'], Criteria::EQUAL);
            $dataset = UsersPeer::doSelectRS($criteria);
            $dataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $dataset->next();
            $aRow = $dataset->getRow();

            //Update - WHERE
            $criteriaWhere = new Criteria('workflow');
            $criteriaWhere->add(ListParticipatedLastPeer::APP_UID, $data['APP_UID'], Criteria::EQUAL);
            //Update - SET
            $criteriaSet = new Criteria('workflow');
            $criteriaSet->add(ListParticipatedLastPeer::DEL_CURRENT_USR_USERNAME, $aRow['USR_USERNAME']);
            $criteriaSet->add(ListParticipatedLastPeer::DEL_CURRENT_USR_FIRSTNAME, $aRow['USR_FIRSTNAME']);
            $criteriaSet->add(ListParticipatedLastPeer::DEL_CURRENT_USR_LASTNAME, $aRow['USR_LASTNAME']);

            if (isset($data['APP_TAS_TITLE'])) {
                $criteriaSet->add(ListParticipatedLastPeer::DEL_CURRENT_TAS_TITLE, $data['APP_TAS_TITLE']);
            }

            BasePeer::doUpdate($criteriaWhere, $criteriaSet, Propel::getConnection('workflow'));
        }
        $this->update($data);
    }
    /**
     * Remove List Participated History.
     *
     * @param type $seqName
     *
     * @return type
     *
     * @throws type
     */
    public function remove($app_uid, $usr_uid, $del_index)
    {
        try {
            if (!is_null(ListParticipatedLastPeer::retrieveByPK($app_uid, $usr_uid, $del_index))) {
                $criteria = new Criteria('workflow');

                $criteria->add(ListParticipatedLastPeer::APP_UID, $app_uid);
                $criteria->add(ListParticipatedLastPeer::USR_UID, $usr_uid);
                $criteria->add(ListParticipatedLastPeer::DEL_INDEX, $del_index);
                $result = ListParticipatedLastPeer::doDelete($criteria);
            } else {
                $criteria = new Criteria('workflow');
                $criteria->add(ListParticipatedLastPeer::APP_UID, $app_uid);
                $criteria->add(ListParticipatedLastPeer::USR_UID, $usr_uid);
                $rsCriteria = ListParticipatedLastPeer::doSelectRS($criteria);

                if ($rsCriteria->next()) {
                    $criteria2 = clone $criteria;
                    $result = ListParticipatedLastPeer::doDelete($criteria2);
                }
            }
        } catch (Exception $e) {
            throw $e;
        }
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
        $dateFrom = isset($filters['dateFrom']) ? $filters['dateFrom'] : '';
        $dateTo = isset($filters['dateTo']) ? $filters['dateTo'] : '';
        $filterStatus = isset($filters['filterStatus']) ? $filters['filterStatus'] : '';
        $newestthan = isset($filters['newestthan']) ? $filters['newestthan'] : '';
        $oldestthan = isset($filters['oldestthan']) ? $filters['oldestthan'] : '';

        //Filter Started by me and Completed by me
        switch ($filter) {
            case 'started':
                $criteria->add(ListParticipatedLastPeer::DEL_INDEX, 1, Criteria::EQUAL);
                break;
            case 'completed':
                $criteria->add(ListParticipatedLastPeer::APP_STATUS, 'COMPLETED', Criteria::EQUAL);
                break;
        }
        //Check the inbox to call
        switch ($filterStatus) {
            case 'DRAFT':
                $criteria->add(ListParticipatedLastPeer::APP_STATUS, 'DRAFT', Criteria::EQUAL);
                break;
            case 'TO_DO':
                $criteria->add(ListParticipatedLastPeer::APP_STATUS, 'TO_DO', Criteria::EQUAL);
                break;
            case 'COMPLETED':
                $criteria->add(ListParticipatedLastPeer::APP_STATUS, 'COMPLETED', Criteria::EQUAL);
                break;
            case 'CANCELLED':
                $criteria->add(ListParticipatedLastPeer::APP_STATUS, 'CANCELLED', Criteria::EQUAL);
                break;
        }

        //Filter Search
        if ($search != '') {
            //Check if we need to search to the APP_UID
            if (!empty($caseLink)) {
                $criteria->add(ListParticipatedLastPeer::APP_UID, $search, Criteria::EQUAL);
            } else {
                //If we have additional tables configured in the custom cases list, prepare the variables for search
                $casesList = new \ProcessMaker\BusinessModel\Cases();
                $casesList->getSearchCriteriaListCases($criteria, __CLASS__ . 'Peer', $search, $this->additionalClassName, $additionalColumns);
            }
        }

        //Filter Process Id
        if ($process != '') {
            $criteria->add(ListParticipatedLastPeer::PRO_UID, $process, Criteria::EQUAL);
        }

        //Filter Category
        if ($category != '') {
            $criteria->addSelectColumn(ProcessPeer::PRO_CATEGORY);
            $aConditions = array();
            $aConditions[] = array(ListParticipatedLastPeer::PRO_UID, ProcessPeer::PRO_UID);
            $aConditions[] = array(ProcessPeer::PRO_CATEGORY, "'".$category."'");
            $criteria->addJoinMC($aConditions, Criteria::INNER_JOIN);
        }

        //Those filters: $newestthan, $oldestthan is used from mobile GET /light/participated
        if ($newestthan != '') {
            $criteria->add( $criteria->getNewCriterion( ListParticipatedLastPeer::DEL_DELEGATE_DATE, $newestthan, Criteria::GREATER_THAN ));
        }

        if ($oldestthan != '') {
            $criteria->add( $criteria->getNewCriterion( ListParticipatedLastPeer::DEL_DELEGATE_DATE, $oldestthan, Criteria::LESS_THAN ));
        }
    }

    /**
     * This function get the information in the corresponding cases list
     * @param string $usr_uid, must be show cases related to this user
     * @param array $filters for apply in the result
     * @param null $callbackRecord
     * @param string $appUid related to the specific case
     * @return array $data
     * @throws PropelException
     */
    public function loadList($usr_uid, $filters = array(), $callbackRecord = null, $appUid = '')
    {
        $pmTable = new PmTable();
        $criteria = $pmTable->addPMFieldsToList('sent');
        $this->additionalClassName = $pmTable->tableClassName;
        $additionalColumns = $criteria->getSelectColumns();

        $criteria->addSelectColumn(ListParticipatedLastPeer::APP_UID);
        $criteria->addSelectColumn(ListParticipatedLastPeer::DEL_INDEX);
        $criteria->addSelectColumn(ListParticipatedLastPeer::USR_UID);
        $criteria->addSelectColumn(ListParticipatedLastPeer::TAS_UID);
        $criteria->addSelectColumn(ListParticipatedLastPeer::PRO_UID);
        $criteria->addSelectColumn(ListParticipatedLastPeer::APP_NUMBER);
        $criteria->addSelectColumn(ApplicationPeer::APP_TITLE);
        $criteria->addSelectColumn(ListParticipatedLastPeer::APP_PRO_TITLE);
        $criteria->addSelectColumn(ListParticipatedLastPeer::APP_TAS_TITLE);
        $criteria->addSelectColumn(ListParticipatedLastPeer::APP_STATUS);
        $criteria->addSelectColumn(ListParticipatedLastPeer::DEL_PREVIOUS_USR_UID);
        $criteria->addSelectColumn(ListParticipatedLastPeer::DEL_PREVIOUS_USR_USERNAME);
        $criteria->addSelectColumn(ListParticipatedLastPeer::DEL_PREVIOUS_USR_FIRSTNAME);
        $criteria->addSelectColumn(ListParticipatedLastPeer::DEL_PREVIOUS_USR_LASTNAME);
        $criteria->addSelectColumn(ListParticipatedLastPeer::DEL_CURRENT_USR_USERNAME);
        $criteria->addSelectColumn(ListParticipatedLastPeer::DEL_CURRENT_USR_FIRSTNAME);
        $criteria->addSelectColumn(ListParticipatedLastPeer::DEL_CURRENT_USR_LASTNAME);
        $criteria->addSelectColumn(ListParticipatedLastPeer::DEL_CURRENT_TAS_TITLE);
        $criteria->addSelectColumn(ListParticipatedLastPeer::DEL_DELEGATE_DATE);
        $criteria->addSelectColumn(ListParticipatedLastPeer::DEL_INIT_DATE);
        $criteria->addSelectColumn(ListParticipatedLastPeer::DEL_DUE_DATE);
        $criteria->addSelectColumn(ListParticipatedLastPeer::DEL_PRIORITY);
        $criteria->addSelectColumn(ListParticipatedLastPeer::DEL_THREAD_STATUS);
        $criteria->add(ListParticipatedLastPeer::USR_UID, $usr_uid, Criteria::EQUAL);

        //Check if the user was participated in a specific case
        if ($appUid != '') {
            $criteria->add(ListParticipatedLastPeer::APP_UID, $appUid, Criteria::EQUAL);
        }

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

        $dir = isset($filters['dir']) ? $filters['dir'] : 'ASC';
        $start = isset($filters['start']) ? $filters['start'] : '0';
        $limit = isset($filters['limit']) ? $filters['limit'] : '25';
        $paged = isset($filters['paged']) ? $filters['paged'] : 1;

        if ($dir == 'DESC') {
            $criteria->addDescendingOrderByColumn($sort);
        } else {
            $criteria->addAscendingOrderByColumn($sort);
        }

        if ($paged == 1) {
            $criteria->setLimit($limit);
            $criteria->setOffset($start);
        }

        $criteria->addJoin(ListParticipatedLastPeer::APP_UID, ApplicationPeer::APP_UID, Criteria::LEFT_JOIN);

        $dataset = ListParticipatedLastPeer::doSelectRS($criteria, Propel::getDbConnection('workflow_ro'));
        $dataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $data = array();
        $aPriorities = array('1' => 'VL', '2' => 'L', '3' => 'N', '4' => 'H', '5' => 'VH');
        while ($dataset->next()) {
            $aRow = (is_null($callbackRecord)) ? $dataset->getRow() : $callbackRecord($dataset->getRow());
            $aRow['DEL_PRIORITY'] = (
                isset($aRow['DEL_PRIORITY']) &&
                is_numeric($aRow['DEL_PRIORITY']) &&
                $aRow['DEL_PRIORITY'] <= 5 &&
                $aRow['DEL_PRIORITY'] > 0) ? $aRow['DEL_PRIORITY'] : 3;
            $aRow['DEL_PRIORITY'] = G::LoadTranslation("ID_PRIORITY_{$aPriorities[$aRow['DEL_PRIORITY']]}");
            $data[] = $aRow;
        }

        return $data;
    }

    public function primaryKeysExists($data)
    {
        $criteria = new Criteria('workflow');
        $criteria->add(ListParticipatedLastPeer::APP_UID, $data['APP_UID']);
        $criteria->add(ListParticipatedLastPeer::USR_UID, $data['USR_UID']);
        $criteria->add(ListParticipatedLastPeer::DEL_INDEX, $data['DEL_INDEX']);
        $dataset = UsersPeer::doSelectRS($criteria);
        $dataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $dataset->next();
        $aRow = $dataset->getRow();
        if (is_array($aRow)) {
            if (sizeof($aRow)) {
                return true;
            }
        }

        return false;
    }

    public function getRowFromList($data)
    {
        $criteria = new Criteria('workflow');
        $criteria->add(ListParticipatedLastPeer::APP_UID, $data['APP_UID']);
        $criteria->add(ListParticipatedLastPeer::USR_UID, $data['USR_UID']);
        $dataset = ListParticipatedLastPeer::doSelectRS($criteria);
        $dataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $dataset->next();
        $aRow = $dataset->getRow();
        if (is_array($aRow)) {
            if (sizeof($aRow)) {
                return $aRow;
            }
        }

        return false;
    }

    /**
     * Returns the number of cases of a user.
     *
     * @param string $usrUid
     * @param array  $filters
     *
     * @return int
     */
    public function getCountList($usrUid, $filters = array())
    {
        $criteria = new Criteria();
        $criteria->addSelectColumn('COUNT(*) AS TOTAL');
        $criteria->add(ListParticipatedLastPeer::USR_UID, $usrUid, Criteria::EQUAL);
        if (count($filters)) {
            self::loadFilters($criteria, $filters);
        }
        $dataset = ListParticipatedLastPeer::doSelectRS($criteria);
        $dataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $dataset->next();
        $aRow = $dataset->getRow();

        return (int) $aRow['TOTAL'];
    }

    /**
     * @deprecated This function is deprecated, it hasn’t been removed because of its compatibility with the External Registration plugin
     * @param $where
     * @param $set
     * @return void
     */
    public function updateCurrentUser($where, $set)
    {
        $con = Propel::getConnection('workflow');
        //Update - WHERE
        $criteriaWhere = new Criteria('workflow');
        $criteriaWhere->add(ListParticipatedLastPeer::APP_UID, $where['APP_UID'], Criteria::EQUAL);
        $criteriaWhere->add(ListParticipatedLastPeer::USR_UID, $where['USR_UID'], Criteria::EQUAL);
        $criteriaWhere->add(ListParticipatedLastPeer::DEL_INDEX, $where['DEL_INDEX'], Criteria::EQUAL);
        //Update - SET
        $criteriaSet = new Criteria('workflow');
        foreach ($set as $k => $v) {
            eval('$criteriaSet->add( ListParticipatedLastPeer::' . $k . ',$v, Criteria::EQUAL);');
        }
        BasePeer::doUpdate($criteriaWhere, $criteriaSet, $con);
    }
}
