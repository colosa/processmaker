<?php

require_once 'classes/model/om/BaseListInbox.php';

/**
 * Skeleton subclass for representing a row from the 'LIST_INBOX' table.
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
class ListInbox extends BaseListInbox
{
    private $additionalClassName = '';

    /**
     * Create List Inbox Table
     *
     * @param type $data
     * @return type
     *
     */
    public function create($data, $isSelfService = false)
    {
        $con = Propel::getConnection(ListInboxPeer::DATABASE_NAME);
        try {
            if (isset($data['APP_TITLE'])) {
                $oCase = new Cases();
                $aData = $oCase->loadCase($data["APP_UID"]);
                $data['APP_TITLE'] = G::replaceDataField($data['APP_TITLE'], $aData['APP_DATA']);
            }
            if (!empty($data['PRO_UID']) && empty($data['PRO_ID'])) {
                $p = new Process();
                $data['PRO_ID'] =  $p->load($data['PRO_UID'])['PRO_ID'];
            }
            if (!empty($data['USR_UID'])) {
                $u = new Users();
                $data['USR_ID'] = $data['USR_UID']==='SELF_SERVICES' ? null : $u->load($data['USR_UID'])['USR_ID'];
            }
            if (!empty($data['TAS_UID'])) {
                $t = new Task();
                $data['TAS_ID'] = $t->load($data['TAS_UID'])['TAS_ID'];
            }
            if (!empty($data['APP_STATUS'])) {
                $data['APP_STATUS_ID'] = Application::$app_status_values[$data['APP_STATUS']];
            }
            $this->fromArray($data, BasePeer::TYPE_FIELDNAME);
            if ($this->validate()) {
                $result = $this->save();
            } else {
                $e = new Exception("Failed Validation in class " . get_class($this) . ".");
                $e->aValidationFailures = $this->getValidationFailures();
                throw ($e);
            }
            $con->commit();

            // create participated history
            $listParticipatedHistory = new ListParticipatedHistory();
            $listParticipatedHistory->remove($data['APP_UID'], $data['DEL_INDEX']);
            $listParticipatedHistory = new ListParticipatedHistory();
            $listParticipatedHistory->create($data);

            // create participated history
            $listMyInbox = new ListMyInbox();
            $listMyInbox->refresh($data);

            // remove and create participated last
            if (!$isSelfService) {
                $oCriteria = new Criteria('workflow');
                $oCriteria->add(ListParticipatedLastPeer::APP_UID, $data['APP_UID']);
                $oCriteria->add(ListParticipatedLastPeer::USR_UID, $data['USR_UID']);
                $exit = ListParticipatedLastPeer::doCount($oCriteria);
                if ($exit) {
                    $oCriteria = new Criteria('workflow');
                    $oCriteria->add(ListParticipatedLastPeer::APP_UID, $data['APP_UID']);
                    $oCriteria->add(ListParticipatedLastPeer::USR_UID, $data['USR_UID']);
                    ListParticipatedLastPeer::doDelete($oCriteria);
                }

                $listParticipatedLast = new ListParticipatedLast();
                $listParticipatedLast->create($data);
                $listParticipatedLast = new ListParticipatedLast();
                $listParticipatedLast->refresh($data);
            } else {
                $data['USR_UID_CURRENT'] = $data['DEL_PREVIOUS_USR_UID'];
                $data['DEL_CURRENT_USR_LASTNAME'] = '';
                $data['DEL_CURRENT_USR_USERNAME'] = '';
                $data['DEL_CURRENT_USR_FIRSTNAME'] = '';

                $listParticipatedLast = new ListParticipatedLast();
                $listParticipatedLast->refresh($data, $isSelfService);
                $data['USR_UID'] = 'SELF_SERVICES';
                $listParticipatedLast = new ListParticipatedLast();
                $listParticipatedLast->create($data);
                $listParticipatedLast = new ListParticipatedLast();
                $listParticipatedLast->refresh($data, $isSelfService);
                $listUnassigned = new ListUnassigned();
                $listUnassigned->newRow($data, $data['DEL_PREVIOUS_USR_UID']);
            }

            return $result;
        } catch (Exception $e) {
            $con->rollback();
            throw ($e);
        }
    }

    /**
     *  Update List Inbox Table
     *
     * @param type $data
     * @return type
     * @throws type
     */
    public function update($data, $isSelfService = false)
    {
        if (isset($data['APP_TITLE'])) {
            $oCase = new Cases();
            $aData = $oCase->loadCase($data["APP_UID"]);
            $data['APP_TITLE'] = G::replaceDataField($data['APP_TITLE'], $aData['APP_DATA']);
        }
        if ($isSelfService) {
            $listParticipatedLast = new ListParticipatedLast();
            $listParticipatedLast->remove($data['APP_UID'], $data['USR_UID'], $data['DEL_INDEX']);
            //Update
            //Update - SET
            $criteriaSet = new Criteria("workflow");
            $criteriaSet->add(ListParticipatedLastPeer::USR_UID, $data["USR_UID"]);
            //Update - WHERE
            $criteriaWhere = new Criteria("workflow");
            $criteriaWhere->add(ListParticipatedLastPeer::APP_UID, $data["APP_UID"], Criteria::EQUAL);
            $criteriaWhere->add(ListParticipatedLastPeer::USR_UID, "SELF_SERVICES", Criteria::EQUAL);
            $criteriaWhere->add(ListParticipatedLastPeer::DEL_INDEX, $data["DEL_INDEX"], Criteria::EQUAL);

            BasePeer::doUpdate($criteriaWhere, $criteriaSet, Propel::getConnection("workflow"));

            //Update
            $listParticipatedLast = new ListParticipatedLast();
            $listParticipatedLast->refresh($data);
        } else {
            if (isset($data["APP_UID"]) &&
                isset($data["USER_UID"]) &&
                isset($data["DEL_INDEX"]) &&
                isset($data["APP_TITLE"])
            ) {
                //Update
                //Update - SET
                $criteriaSet = new Criteria("workflow");
                $criteriaSet->add(ListParticipatedLastPeer::APP_TITLE, $data["APP_TITLE"]);

                //Update - WHERE
                $criteriaWhere = new Criteria("workflow");
                $criteriaWhere->add(ListParticipatedLastPeer::APP_UID, $data["APP_UID"], Criteria::EQUAL);
                $criteriaWhere->add(ListParticipatedLastPeer::USR_UID, $data["USER_UID"], Criteria::EQUAL);
                $criteriaWhere->add(ListParticipatedLastPeer::DEL_INDEX, $data["DEL_INDEX"], Criteria::EQUAL);

                $result = BasePeer::doUpdate($criteriaWhere, $criteriaSet, Propel::getConnection("workflow"));
            }
        }

        if ((array_key_exists('TAS_UID', $data) && isset($data['TAS_UID'])) &&
            (array_key_exists('TAS_UID', $data) && isset($data['PRO_UID'])) &&
            isset($data['APP_UID'])
        ) {
            $data['DEL_PRIORITY'] = $this->getTaskPriority($data['TAS_UID'], $data['PRO_UID'], $data["APP_UID"]);
        }

        if (!empty($data['USR_UID'])) {
            $u = new Users();
            $data['USR_ID'] = $data['USR_UID']==='SELF_SERVICES' ? null : $u->load($data['USR_UID'])['USR_ID'];
        }
        if (!empty($data['TAS_UID'])) {
            $t = new Task();
            $data['TAS_ID'] = $t->load($data['TAS_UID'])['TAS_ID'];
        }
        if (!empty($data['APP_STATUS'])) {
            $data['APP_STATUS_ID'] = Application::$app_status_values[$data['APP_STATUS']];
        }
        $con = Propel::getConnection(ListInboxPeer::DATABASE_NAME);
        try {
            $con->begin();
            $this->setNew(false);
            $this->fromArray($data, BasePeer::TYPE_FIELDNAME);
            if ($this->validate()) {
                $result = $this->save();
                $con->commit();

                // update participated history
                $listParticipatedHistory = new ListParticipatedHistory();
                $listParticipatedHistory->update($data);
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
     * Remove List Inbox
     *
     * @param type $seqName
     * @return type
     * @throws type
     *
     */
    public function remove($app_uid, $del_index)
    {
        $con = Propel::getConnection(ListInboxPeer::DATABASE_NAME);
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
     * Remove All List Inbox
     *
     * @param type $seqName
     * @return type
     * @throws type
     *
     */
    public function removeAll($app_uid)
    {
        $con = Propel::getConnection(ListInboxPeer::DATABASE_NAME);
        try {
            $this->setAppUid($app_uid);

            $con->begin();
            $this->delete();
            $con->commit();
        } catch (Exception $e) {
            $con->rollback();
            throw ($e);
        }
    }

    /**
     * Define the variables before created the row
     *
     * This method is used before create the new data
     * we completed the information about some variables
     * for create the record
     *
     * @param array $data
     * @param string $delPreviusUsrUid Uid from the user previous
     * @param boolean $isSelfService this value define if the case is Unassigned
     * @return void
     *
     */
    public function newRow(
        $data,
        $delPreviusUsrUid,
        $isSelfService = false
    ) {
        $removeList = true;
        if (isset($data['REMOVED_LIST'])) {
            $removeList = $data['REMOVED_LIST'];
            unset($data['REMOVED_LIST']);
        }
        $data['DEL_PREVIOUS_USR_UID'] = $delPreviusUsrUid;
        if (isset($data['DEL_TASK_DUE_DATE'])) {
            $data['DEL_DUE_DATE'] = $data['DEL_TASK_DUE_DATE'];
        }

        if (!isset($data['DEL_DUE_DATE'])) {
            $filters = array("APP_UID" => $data["APP_UID"], "DEL_INDEX" => $data['DEL_INDEX']);
            $data['DEL_DUE_DATE'] = $this->getAppDelegationInfo($filters, 'DEL_TASK_DUE_DATE');
        }

        if (isset($data['APP_INIT_DATE'])) {
            $data['DEL_INIT_DATE'] = $data['APP_INIT_DATE'];
        }

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
        $criteria->addSelectColumn(TaskPeer::TAS_TITLE);
        $criteria->addSelectColumn(TaskPeer::TAS_DEF_TITLE);
        $criteria->add(TaskPeer::TAS_UID, $data['TAS_UID'], Criteria::EQUAL);
        $dataset = TaskPeer::doSelectRS($criteria);
        $dataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $dataset->next();
        $aRow = $dataset->getRow();
        if ($aRow['TAS_DEF_TITLE'] == '') {
            $criteria = new Criteria();
            $criteria->addSelectColumn(ApplicationPeer::APP_TITLE);
            $criteria->add(ApplicationPeer::APP_UID, $data['APP_UID'], Criteria::EQUAL);
            $dataset = ApplicationPeer::doSelectRS($criteria);
            $dataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $dataset->next();
            $aRowApp = $dataset->getRow();
            $aRow['TAS_DEF_TITLE'] = $aRowApp['APP_TITLE'];
        }
        $data['APP_TITLE'] = $aRow['TAS_DEF_TITLE'];
        $data['APP_TAS_TITLE'] = $aRow['TAS_TITLE'];


        $criteria = new Criteria();
        $criteria->addSelectColumn(ProcessPeer::PRO_TITLE);
        $criteria->add(ProcessPeer::PRO_UID, $data['PRO_UID'], Criteria::EQUAL);
        $dataset = ProcessPeer::doSelectRS($criteria);
        $dataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $dataset->next();
        $aRow = $dataset->getRow();
        $data['APP_PRO_TITLE'] = $aRow['PRO_TITLE'];

        $data['DEL_PRIORITY'] = $this->getTaskPriority($data['TAS_UID'], $data['PRO_UID'], $data["APP_UID"]);

        $data['APP_PREVIOUS_USER'] = '';
        if ($data['DEL_PREVIOUS_USR_UID'] === '') {
            global $RBAC;
            if (isset($RBAC->aUserInfo['USER_INFO'])) {
                $aUser = $RBAC->aUserInfo['USER_INFO'];
                $data['DEL_PREVIOUS_USR_UID'] = $aUser['USR_UID'];
            }
        }
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

        $criteria = new Criteria();
        $criteria->addSelectColumn(SubApplicationPeer::DEL_INDEX_PARENT);
        $criteria->add(SubApplicationPeer::APP_PARENT, $data['APP_UID'], Criteria::EQUAL);
        $dataset = SubApplicationPeer::doSelectRS($criteria);
        $dataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        if ($dataset->next()) {
            $aSub = $dataset->getRow();
            if ($aSub['DEL_INDEX_PARENT'] == $data['DEL_PREVIOUS'] && !$isSelfService) {
                self::create($data, $isSelfService);
                return 1;
            }
        }

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
            $data['DEL_CURRENT_USR_USERNAME']  = $aRow['USR_USERNAME'];
            $data['DEL_CURRENT_USR_FIRSTNAME'] = $aRow['USR_FIRSTNAME'];
            $data['DEL_CURRENT_USR_LASTNAME']  = $aRow['USR_LASTNAME'];
        }
        self::create($data, $isSelfService);
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
        $action = isset($filters['action']) ? $filters['action'] : '';
        $usrUid = isset($filters['usr_uid']) ? $filters['usr_uid'] : '';
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
        $appUidCheck = isset($filters['appUidCheck']) ? $filters['appUidCheck'] : array();

        //Check the inbox to call
        switch ($action) {
            case 'draft':
                $criteria->add(ListInboxPeer::APP_STATUS, 'DRAFT', Criteria::EQUAL);
                $criteria->add(ListInboxPeer::USR_UID, $usrUid, Criteria::EQUAL);
                break;
            case 'to_revise':
                $criteria->add(ListInboxPeer::APP_STATUS, 'TO_DO', Criteria::EQUAL);
                $oAppCache = new AppCacheView();
                $aProcesses = $oAppCache->getProUidSupervisor($usrUid);
                $criteria->add(ListInboxPeer::PRO_UID, $aProcesses, Criteria::IN);
                break;
            case 'to_reassign':
                $criteria->add(ListInboxPeer::APP_STATUS, 'TO_DO', Criteria::EQUAL);
                if ($usrUid !== '') {
                    $criteria->add(ListInboxPeer::USR_UID, $usrUid, Criteria::EQUAL);
                }
                break;
            default://todo
                $criteria->add(ListInboxPeer::APP_STATUS, 'TO_DO', Criteria::EQUAL);
                $criteria->add(ListInboxPeer::USR_UID, $usrUid, Criteria::EQUAL);
        }

        //Filter Read Unread All
        switch ($filter) {
            case 'read':
                $criteria->add(ListInboxPeer::DEL_INIT_DATE, null, Criteria::ISNOTNULL);
                break;
            case 'unread':
                $criteria->add(ListInboxPeer::DEL_INIT_DATE, null, Criteria::ISNULL);
                break;
        }

        //Filter Task Status
        switch ($filterStatus) {
            case 'ON_TIME':
                $criteria->add(
                    ListInboxPeer::DEL_RISK_DATE,
                    "TIMEDIFF(". ListInboxPeer::DEL_RISK_DATE." , NOW( ) ) > 0",
                    Criteria::CUSTOM
                );
                break;
            case 'AT_RISK':
                $criteria->add(
                    ListInboxPeer::DEL_RISK_DATE,
                    "TIMEDIFF(". ListInboxPeer::DEL_RISK_DATE .", NOW( ) ) < 0",
                    Criteria::CUSTOM
                );
                $criteria->add(
                    ListInboxPeer::DEL_DUE_DATE,
                    "TIMEDIFF(". ListInboxPeer::DEL_DUE_DATE .", NOW( ) ) >  0",
                    Criteria::CUSTOM
                );
                break;
            case 'OVERDUE':
                $criteria->add(
                    ListInboxPeer::DEL_DUE_DATE,
                    "TIMEDIFF(". ListInboxPeer::DEL_DUE_DATE." , NOW( ) ) < 0",
                    Criteria::CUSTOM
                );
                break;
        }

        //Filter Search
        if ($search != '') {
            //Check if we need to search to the APP_UID
            if (!empty($caseLink)) {
                $criteria->add(ListInboxPeer::APP_UID, $search, Criteria::EQUAL);
            } else {
                //If we have additional tables configured in the custom cases list, prepare the variables for search
                $casesList = new \ProcessMaker\BusinessModel\Cases();
                $casesList->getSearchCriteriaListCases($criteria, __CLASS__ . 'Peer', $search, $this->additionalClassName, $additionalColumns);
            }
        }

        //Filter Process Id
        if ($process != '') {
            $criteria->add(ListInboxPeer::PRO_UID, $process, Criteria::EQUAL);
        }

        //Filter Category
        if ($category != '') {
            $criteria->addSelectColumn(ProcessPeer::PRO_CATEGORY);
            $aConditions   = array();
            $aConditions[] = array(ListInboxPeer::PRO_UID, ProcessPeer::PRO_UID);
            $aConditions[] = array(ProcessPeer::PRO_CATEGORY, "'" . $category . "'");
            $criteria->addJoinMC($aConditions, Criteria::INNER_JOIN);
        }
        //Those filters: $newestthan, $oldestthan is used from mobile GET /light/todo
        if ($newestthan != '') {
            $criteria->add( $criteria->getNewCriterion( ListInboxPeer::DEL_DELEGATE_DATE, $newestthan, Criteria::GREATER_THAN ));
        }

        if ($oldestthan != '') {
            $criteria->add( $criteria->getNewCriterion( ListInboxPeer::DEL_DELEGATE_DATE, $oldestthan, Criteria::LESS_THAN ));
        }

        //Review in the specific lot of cases
        if (!empty($appUidCheck)) {
            $criteria->add(ListInboxPeer::APP_UID, $appUidCheck, Criteria::IN);
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
        $pmTable = new PmTable();
        $list = isset($filters['action']) ? $filters['action'] : "";
        $criteria = $pmTable->addPMFieldsToList($list);
        $this->additionalClassName = $pmTable->tableClassName;
        $additionalColumns = $criteria->getSelectColumns();
        $filters['usr_uid'] = $usr_uid;

        $criteria->addSelectColumn(ListInboxPeer::APP_UID);
        $criteria->addSelectColumn(ListInboxPeer::DEL_INDEX);
        $criteria->addSelectColumn(ListInboxPeer::USR_UID);
        $criteria->addSelectColumn(ListInboxPeer::TAS_UID);
        $criteria->addSelectColumn(ListInboxPeer::PRO_UID);
        $criteria->addSelectColumn(ListInboxPeer::APP_NUMBER);
        $criteria->addSelectColumn(ListInboxPeer::APP_STATUS);
        $criteria->addSelectColumn(ListInboxPeer::APP_TITLE);
        $criteria->addSelectColumn(ListInboxPeer::APP_PRO_TITLE);
        $criteria->addSelectColumn(ListInboxPeer::APP_TAS_TITLE);
        $criteria->addSelectColumn(ListInboxPeer::APP_UPDATE_DATE);
        $criteria->addSelectColumn(ListInboxPeer::DEL_PREVIOUS_USR_UID);
        $criteria->addSelectColumn(ListInboxPeer::DEL_PREVIOUS_USR_USERNAME);
        $criteria->addSelectColumn(ListInboxPeer::DEL_PREVIOUS_USR_FIRSTNAME);
        $criteria->addSelectColumn(ListInboxPeer::DEL_PREVIOUS_USR_LASTNAME);
        $criteria->addSelectColumn(ListInboxPeer::DEL_DELEGATE_DATE);
        $criteria->addSelectColumn(ListInboxPeer::DEL_INIT_DATE);
        $criteria->addSelectColumn(ListInboxPeer::DEL_DUE_DATE);
        $criteria->addSelectColumn(ListInboxPeer::DEL_PRIORITY);
        $criteria->addSelectColumn(ListInboxPeer::DEL_RISK_DATE);
        $criteria->addSelectColumn(UsersPeer::USR_UID);
        $criteria->addSelectColumn(UsersPeer::USR_FIRSTNAME);
        $criteria->addSelectColumn(UsersPeer::USR_LASTNAME);
        $criteria->addSelectColumn(UsersPeer::USR_USERNAME);
        $criteria->addJoin(ListInboxPeer::USR_UID, UsersPeer::USR_UID, Criteria::LEFT_JOIN);
        self::loadFilters($criteria, $filters, $additionalColumns);

        //We will be defined the sort
        $casesList = new \ProcessMaker\BusinessModel\Cases();
        $sort = $casesList->getSortColumn(
            __CLASS__ . 'Peer',
            BasePeer::TYPE_FIELDNAME,
            empty($filters['sort']) ? "APP_UPDATE_DATE" : $filters['sort'],
            "APP_UPDATE_DATE",
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

        $dataset = ListInboxPeer::doSelectRS($criteria, Propel::getDbConnection('workflow_ro'));
        $dataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $data = array();
        $aPriorities = array('1' => 'VL', '2' => 'L', '3' => 'N', '4' => 'H', '5' => 'VH');
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

    public function getTaskPriority($taskUid, $proUid, $appUid)
    {
        $criteria = new Criteria();
        $criteria->addSelectColumn(TaskPeer::TAS_PRIORITY_VARIABLE);
        $criteria->add(TaskPeer::TAS_UID, $taskUid, Criteria::EQUAL);
        $criteria->add(TaskPeer::PRO_UID, $proUid, Criteria::EQUAL);
        $dataset = TaskPeer::doSelectRS($criteria);
        $dataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $dataset->next();
        $aRow = $dataset->getRow();
        $priority = $aRow['TAS_PRIORITY_VARIABLE'];
        if (strlen($priority)>2) {
            $oCase = new Cases();
            $aData = $oCase->loadCase($appUid);
            $priorityLabel = substr($priority, 2, strlen($priority));
            if (isset($aData['APP_DATA'][$priorityLabel])) {
                $priority = $aData['APP_DATA'][$priorityLabel];
            }
        }
        return $priority != "" ? $priority : 3;
    }

    public function getAppDelegationInfo($filters, $fieldName)
    {
        $criteria = new Criteria();
        eval('$criteria->addSelectColumn( AppDelegationPeer::'.$fieldName.');');
        foreach ($filters as $k => $v) {
            eval('$criteria->add( AppDelegationPeer::'.$k.',$v, Criteria::EQUAL);');
        }
        $dataset = AppDelegationPeer::doSelectRS($criteria);
        $dataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $dataset->next();
        $aRow = $dataset->getRow();
        return isset($aRow[$fieldName]) ? $aRow[$fieldName] : null;
    }

    /**
     * Returns the number of cases of a user
     * @param string $usrUid
     * @param array  $filters
     * @return int
     */
    public function getCountList($usrUid, $filters = array())
    {
        $filters['usr_uid'] = $usrUid;
        $criteria = new Criteria();
        $criteria->addSelectColumn('COUNT(*) AS TOTAL');
        $criteria->add(ListInboxPeer::USR_UID, $usrUid, Criteria::EQUAL);
        if (count($filters)) {
            self::loadFilters($criteria, $filters);
        }
        $dataset = ListInboxPeer::doSelectRS($criteria);
        $dataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $dataset->next();
        $aRow = $dataset->getRow();
        return (int)$aRow['TOTAL'];
    }
}
