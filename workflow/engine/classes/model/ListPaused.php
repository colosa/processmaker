<?php

require_once 'classes/model/om/BaseListPaused.php';


/**
 * Skeleton subclass for representing a row from the 'LIST_PAUSED' table.
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
class ListPaused extends BaseListPaused
{
    private $additionalClassName = '';

    /**
     * Create List Paused Table
     *
     * @param type $data
     * @return type
     *
     */
    public function create($data)
    {
        $criteria = new Criteria();
        $criteria->addSelectColumn(ApplicationPeer::APP_TITLE);
        $criteria->add(ApplicationPeer::APP_UID, $data['APP_UID'], Criteria::EQUAL);
        $dataset = ApplicationPeer::doSelectRS($criteria);
        $dataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $dataset->next();
        $aRow = $dataset->getRow();
        if (!isset($data['APP_TITLE'])) {
            $data['APP_TITLE'] = $aRow['APP_TITLE'];
        }

        $criteria = new Criteria();
        $criteria->addSelectColumn(ProcessPeer::PRO_TITLE);
        $criteria->add(ProcessPeer::PRO_UID, $data['PRO_UID'], Criteria::EQUAL);
        $dataset = ProcessPeer::doSelectRS($criteria);
        $dataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $dataset->next();
        $aRow = $dataset->getRow();
        $data['APP_PRO_TITLE'] = $aRow['PRO_TITLE'];

        $criteria = new Criteria();
        $criteria->addSelectColumn(AppDelegationPeer::USR_UID);
        $criteria->addSelectColumn(AppDelegationPeer::TAS_UID);
        $criteria->addSelectColumn(AppDelegationPeer::DEL_INIT_DATE);
        $criteria->addSelectColumn(AppDelegationPeer::DEL_DELEGATE_DATE);
        $criteria->addSelectColumn(AppDelegationPeer::DEL_TASK_DUE_DATE);
        $criteria->addSelectColumn(AppDelegationPeer::DEL_PREVIOUS);
        $criteria->add(AppDelegationPeer::APP_UID, $data['APP_UID'], Criteria::EQUAL);
        $criteria->add(AppDelegationPeer::DEL_INDEX, $data['DEL_INDEX'], Criteria::EQUAL);
        $dataset = AppDelegationPeer::doSelectRS($criteria);
        $dataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $dataset->next();
        $aRow = $dataset->getRow();
        $data['USR_UID'] = isset($data['USR_UID']) ? $data['USR_UID'] : $aRow['USR_UID'];
        $data['TAS_UID'] = $aRow['TAS_UID'];
        $data['DEL_INIT_DATE'] = $aRow['DEL_INIT_DATE'];
        $data['DEL_DUE_DATE'] = $aRow['DEL_TASK_DUE_DATE'];
        $data['DEL_DELEGATE_DATE'] = $aRow['DEL_DELEGATE_DATE'];
        $delPrevious = $aRow['DEL_PREVIOUS'];

        $criteria = new Criteria();
        $criteria->addSelectColumn(AppDelegationPeer::USR_UID);
        $criteria->add(AppDelegationPeer::APP_UID, $data['APP_UID'], Criteria::EQUAL);
        $criteria->add(AppDelegationPeer::DEL_INDEX, $delPrevious, Criteria::EQUAL);
        $dataset = AppDelegationPeer::doSelectRS($criteria);
        $dataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $dataset->next();
        $aRow = $dataset->getRow();
        $data['DEL_PREVIOUS_USR_UID'] = $aRow['USR_UID'];

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

        $criteria = new Criteria();
        $criteria->addSelectColumn(TaskPeer::TAS_TITLE);
        $criteria->add(TaskPeer::TAS_UID, $data['TAS_UID'], Criteria::EQUAL);
        $dataset = TaskPeer::doSelectRS($criteria);
        $dataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $dataset->next();
        $aRow = $dataset->getRow();
        $data['APP_TAS_TITLE'] = $aRow['TAS_TITLE'];

        $criteria = new Criteria();
        $criteria->addSelectColumn(UsersPeer::USR_USERNAME);
        $criteria->addSelectColumn(UsersPeer::USR_FIRSTNAME);
        $criteria->addSelectColumn(UsersPeer::USR_LASTNAME);
        $criteria->add(UsersPeer::USR_UID, $data['USR_UID'], Criteria::EQUAL);
        $dataset = UsersPeer::doSelectRS($criteria);
        $dataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $dataset->next();
        $aRow = $dataset->getRow();
        $data['DEL_CURRENT_USR_USERNAME']  = $aRow['USR_USERNAME'];
        $data['DEL_CURRENT_USR_FIRSTNAME'] = $aRow['USR_FIRSTNAME'];
        $data['DEL_CURRENT_USR_LASTNAME']  = $aRow['USR_LASTNAME'];

        $data['APP_PAUSED_DATE'] = Date("Y-m-d H:i:s");

        $oListInbox = new ListInbox();
        $oListInbox->remove($data['APP_UID'], $data['DEL_INDEX']);

        if (!empty($data['PRO_UID']) && empty($data['PRO_ID'])) {
            $p = new Process();
            $data['PRO_ID'] =  $p->load($data['PRO_UID'])['PRO_ID'];
        }
        if (!empty($data['USR_UID'])) {
            $u = new Users();
            $data['USR_ID'] = $u->load($data['USR_UID'])['USR_ID'];
        }
        if (!empty($data['TAS_UID'])) {
            $t = new Task();
            $data['TAS_ID'] = $t->load($data['TAS_UID'])['TAS_ID'];
        }
        $con = Propel::getConnection(ListPausedPeer::DATABASE_NAME);
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
     *  Update List Paused Table
     *
     * @param type $data
     * @return type
     * @throws type
     */
    public function update($data)
    {
        if (!empty($data['USR_UID'])) {
            $u = new Users();
            $data['USR_ID'] = $u->load($data['USR_UID'])['USR_ID'];
        }
        if (!empty($data['TAS_UID'])) {
            $t = new Task();
            $data['TAS_ID'] = $t->load($data['TAS_UID'])['TAS_ID'];
        }
        $con = Propel::getConnection(ListPausedPeer::DATABASE_NAME);
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
     * Remove List Paused
     *
     * @param type $seqName
     * @return type
     * @throws type
     *
     */
    public function remove($app_uid, $del_index, $data_inbox)
    {
        $oRow = ApplicationPeer::retrieveByPK($app_uid);
        $aFields = $oRow->toArray(BasePeer::TYPE_FIELDNAME);
        $data_inbox['APP_STATUS'] = $aFields['APP_STATUS'];
        $listInbox = new ListInbox();
        $listInbox->newRow($data_inbox, $data_inbox['USR_UID']);

        $con = Propel::getConnection(ListPausedPeer::DATABASE_NAME);
        try {
            $this->setAppUid($app_uid);
            $this->setDelIndex($del_index);
            $con->begin();
            $this->delete();
            $con->commit();
        } catch (Exception $e) {
            $con->rollback();
            throw ($e);
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
        $filterStatus = isset($filters['filterStatus']) ? $filters['filterStatus'] : '';

        //Filter Read Unread All
        switch ($filter) {
            case 'read':
                $criteria->add(ListPausedPeer::DEL_INIT_DATE, null, Criteria::ISNOTNULL);
                break;
            case 'unread':
                $criteria->add(ListPausedPeer::DEL_INIT_DATE, null, Criteria::ISNULL);
                break;
        }

        //Filter Search
        if ($search != '') {
            //Check if we need to search to the APP_UID
            if (!empty($caseLink)) {
                $criteria->add(ListPausedPeer::APP_UID, $search, Criteria::EQUAL);
            } else {
                //If we have additional tables configured in the custom cases list, prepare the variables for search
                $casesList = new \ProcessMaker\BusinessModel\Cases();
                $casesList->getSearchCriteriaListCases($criteria, __CLASS__ . 'Peer', $search, $this->additionalClassName, $additionalColumns);
            }
        }

        //Filter Process Id
        if ($process != '') {
            $criteria->add(ListPausedPeer::PRO_UID, $process, Criteria::EQUAL);
        }

        //Filter Category
        if ($category != '') {
            $criteria->addSelectColumn(ProcessPeer::PRO_CATEGORY);
            $aConditions   = array();
            $aConditions[] = array(ListPausedPeer::PRO_UID, ProcessPeer::PRO_UID);
            $aConditions[] = array(ProcessPeer::PRO_CATEGORY, "'" . $category . "'");
            $criteria->addJoinMC($aConditions, Criteria::INNER_JOIN);
        }
    }

    /**
     * This function get the information in the corresponding cases list
     * @param string $usr_uid, must be show cases related to this user
     * @param array $filters for apply in the result
     * @param null $callbackRecord
     * @return array $data
     * @throws PropelException
     */
    public function loadList($usr_uid, $filters = array(), $callbackRecord = null)
    {
        $resp = array();
        $pmTable = new PmTable();
        $criteria = $pmTable->addPMFieldsToList('paused');
        $this->additionalClassName = $pmTable->tableClassName;
        $additionalColumns = $criteria->getSelectColumns();

        $criteria->addSelectColumn(ListPausedPeer::APP_UID);
        $criteria->addSelectColumn(ListPausedPeer::USR_UID);
        $criteria->addSelectColumn(ListPausedPeer::TAS_UID);
        $criteria->addSelectColumn(ListPausedPeer::PRO_UID);
        $criteria->addSelectColumn(ListPausedPeer::APP_NUMBER);
        $criteria->addSelectColumn(ListPausedPeer::APP_TITLE);
        $criteria->addSelectColumn(ListPausedPeer::APP_PRO_TITLE);
        $criteria->addSelectColumn(ListPausedPeer::APP_TAS_TITLE);
        $criteria->addSelectColumn(ListPausedPeer::APP_PAUSED_DATE);
        $criteria->addSelectColumn(ListPausedPeer::APP_RESTART_DATE);
        $criteria->addSelectColumn(ListPausedPeer::DEL_INDEX);
        $criteria->addSelectColumn(ListPausedPeer::DEL_PREVIOUS_USR_UID);
        $criteria->addSelectColumn(ListPausedPeer::DEL_PREVIOUS_USR_USERNAME);
        $criteria->addSelectColumn(ListPausedPeer::DEL_PREVIOUS_USR_FIRSTNAME);
        $criteria->addSelectColumn(ListPausedPeer::DEL_PREVIOUS_USR_LASTNAME);
        $criteria->addSelectColumn(ListPausedPeer::DEL_CURRENT_USR_FIRSTNAME);
        $criteria->addSelectColumn(ListPausedPeer::DEL_CURRENT_USR_LASTNAME);
        $criteria->addSelectColumn(ListPausedPeer::DEL_CURRENT_USR_USERNAME);
        $criteria->addSelectColumn(ListPausedPeer::DEL_DELEGATE_DATE);
        $criteria->addSelectColumn(ListPausedPeer::DEL_INIT_DATE);
        $criteria->addSelectColumn(ListPausedPeer::DEL_DUE_DATE);
        $criteria->addSelectColumn(ListPausedPeer::DEL_PRIORITY);
        $criteria->add(ListPausedPeer::USR_UID, $usr_uid, Criteria::EQUAL);
        self::loadFilters($criteria, $filters, $additionalColumns);

        //We will be defined the sort
        $casesList = new \ProcessMaker\BusinessModel\Cases();
        $sort = $casesList->getSortColumn(
            __CLASS__ . 'Peer',
            BasePeer::TYPE_FIELDNAME,
            empty($filters['sort']) ? "APP_PAUSED_DATE" : $filters['sort'],
            "APP_PAUSED_DATE",
            $this->additionalClassName,
            $additionalColumns
        );

        $dir   = isset($filters['dir']) ? $filters['dir'] : "ASC";
        $start = isset($filters['start']) ? $filters['start'] : "0";
        $limit = isset($filters['limit']) ? $filters['limit'] : "25";
        $paged = isset($filters['paged']) ? $filters['paged'] : 1;

        if ($dir == "DESC") {
            $criteria->addDescendingOrderByColumn($sort);
        } else {
            $criteria->addAscendingOrderByColumn($sort);
        }

        if ($paged == 1) {
            $criteria->setLimit($limit);
            $criteria->setOffset($start);
        }

        $dataset = ListPausedPeer::doSelectRS($criteria, Propel::getDbConnection('workflow_ro'));
        $dataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $data = array();
        while ($dataset->next()) {
            $aRow = (is_null($callbackRecord))? $dataset->getRow() : $callbackRecord($dataset->getRow());

            $data[] = $aRow;
        }

        return $data;
    }

    /**
     * Returns the number of cases of a user
     * @param string $usrUid
     * @param array  $filters
     * @return int
     */
    public function getCountList($usrUid, $filters = array())
    {
        $criteria = new Criteria();
        $criteria->addSelectColumn('COUNT(*) AS TOTAL');
        $criteria->add(ListPausedPeer::USR_UID, $usrUid, Criteria::EQUAL);
        if (count($filters)) {
            self::loadFilters($criteria, $filters);
        }
        $dataset = ListPausedPeer::doSelectRS($criteria);
        $dataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $dataset->next();
        $aRow = $dataset->getRow();
        return (int)$aRow['TOTAL'];
    }
} // ListPaused
