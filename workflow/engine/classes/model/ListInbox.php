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

class ListInbox extends BaseListInbox
{
    /**
     * Create List Inbox Table
     *
     * @param type $data
     * @return type
     *
     */
    public function create($data, $isSelfService = false)
    {
        $con = Propel::getConnection( ListInboxPeer::DATABASE_NAME );
        try {
            $this->fromArray( $data, BasePeer::TYPE_FIELDNAME );
            if ($this->validate()) {
                $result = $this->save();
            } else {
                $e = new Exception( "Failed Validation in class " . get_class( $this ) . "." );
                $e->aValidationFailures = $this->getValidationFailures();
                throw ($e);
            }
            $con->commit();

            // create participated history
            $listParticipatedHistory = new ListParticipatedHistory();
            $listParticipatedHistory->remove($data['APP_UID'],$data['DEL_INDEX']);
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
                    $users = new Users();
                    $users->refreshTotal($data['USR_UID'], 'removed', 'participated');
                }

                $listParticipatedLast = new ListParticipatedLast();
                $listParticipatedLast->create($data);
                $listParticipatedLast = new ListParticipatedLast();
                $listParticipatedLast->refresh($data);
            } else {
                $data['USR_UID'] = $data['DEL_PREVIOUS_USR_UID'];
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
            }

            return $result;
        } catch(Exception $e) {
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
        if ($isSelfService) {
            $users = new Users();
            $users->refreshTotal($data['USR_UID'], 'add', 'inbox');

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
            $users = new Users();
            $users->refreshTotal($data['USR_UID'], 'add', 'participated');
        } else {
            if (isset($data["APP_UID"]) && isset($data["USER_UID"]) && isset($data["DEL_INDEX"]) && isset($data["APP_TITLE"])) {
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

        if((array_key_exists('TAS_UID', $data) && isset($data['TAS_UID'])) && (array_key_exists('TAS_UID', $data) && isset($data['PRO_UID'])) && isset($data['APP_UID'])) {
            $data['DEL_PRIORITY'] = $this->getTaskPriority($data['TAS_UID'], $data['PRO_UID'], $data["APP_UID"]);
        }

        $con = Propel::getConnection( ListInboxPeer::DATABASE_NAME );
        try {
            $con->begin();
            $this->setNew( false );
            $this->fromArray( $data, BasePeer::TYPE_FIELDNAME );
            if ($this->validate()) {
                $result = $this->save();
                $con->commit();

                // update participated history
                $listParticipatedHistory = new ListParticipatedHistory();
                $listParticipatedHistory->update($data);
                return $result;
            } else {
                $con->rollback();
                throw (new Exception( "Failed Validation in class " . get_class( $this ) . "." ));
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
    public function remove ($app_uid, $del_index)
    {
        $con = Propel::getConnection( ListInboxPeer::DATABASE_NAME );
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
    public function removeAll ($app_uid)
    {
        $con = Propel::getConnection( ListInboxPeer::DATABASE_NAME );
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

    public function newRow ($data, $delPreviusUsrUid, $isInitSubprocess = false, $dataPreviusApplication = array(), $isSelfService = false)
    {
        $removeList = true;
        if (isset($data['REMOVED_LIST'])) {
            $removeList = $data['REMOVED_LIST'];
            unset($data['REMOVED_LIST']);
        }
        $data['DEL_PREVIOUS_USR_UID'] = $delPreviusUsrUid;
        if (isset($data['DEL_TASK_DUE_DATE'])) {
            $data['DEL_DUE_DATE'] = $data['DEL_TASK_DUE_DATE'];
        }

        if(!isset($data['DEL_DUE_DATE'])) {
            $filters = array("APP_UID" => $data["APP_UID"], "DEL_INDEX" => $data['DEL_INDEX']);
            $data['DEL_DUE_DATE'] = $this->getAppDelegationInfo($filters,'DEL_TASK_DUE_DATE');
        }

        $criteria = new Criteria();
        $criteria->addSelectColumn( ApplicationPeer::APP_NUMBER );
        $criteria->addSelectColumn( ApplicationPeer::APP_UPDATE_DATE );
        $criteria->add( ApplicationPeer::APP_UID, $data['APP_UID'], Criteria::EQUAL );
        $dataset = ApplicationPeer::doSelectRS($criteria);
        $dataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $dataset->next();
        $aRow = $dataset->getRow();
        $data = array_merge($data, $aRow);


        $criteria = new Criteria();
        $criteria->addSelectColumn(ContentPeer::CON_VALUE);
        $criteria->add( ContentPeer::CON_ID, $data['APP_UID'], Criteria::EQUAL );
        $criteria->add( ContentPeer::CON_CATEGORY, 'APP_TITLE', Criteria::EQUAL );
        $criteria->add( ContentPeer::CON_LANG, SYS_LANG, Criteria::EQUAL );
        $dataset = ContentPeer::doSelectRS($criteria);
        $dataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $dataset->next();
        $aRow = $dataset->getRow();
        $data['APP_TITLE'] = $aRow['CON_VALUE'];


        $criteria = new Criteria();
        $criteria->addSelectColumn(ContentPeer::CON_VALUE);
        $criteria->add( ContentPeer::CON_ID, $data['PRO_UID'], Criteria::EQUAL );
        $criteria->add( ContentPeer::CON_CATEGORY, 'PRO_TITLE', Criteria::EQUAL );
        $criteria->add( ContentPeer::CON_LANG, SYS_LANG, Criteria::EQUAL );
        $dataset = ContentPeer::doSelectRS($criteria);
        $dataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $dataset->next();
        $aRow = $dataset->getRow();
        $data['APP_PRO_TITLE'] = $aRow['CON_VALUE'];


        $criteria = new Criteria();
        $criteria->addSelectColumn(ContentPeer::CON_VALUE);
        $criteria->add( ContentPeer::CON_ID, $data['TAS_UID'], Criteria::EQUAL );
        $criteria->add( ContentPeer::CON_CATEGORY, 'TAS_TITLE', Criteria::EQUAL );
        $criteria->add( ContentPeer::CON_LANG, SYS_LANG, Criteria::EQUAL );
        $dataset = ContentPeer::doSelectRS($criteria);
        $dataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $dataset->next();
        $aRow = $dataset->getRow();
        $data['APP_TAS_TITLE'] = $aRow['CON_VALUE'];


        $data['DEL_PRIORITY'] = $this->getTaskPriority($data['TAS_UID'], $data['PRO_UID'], $data["APP_UID"]);


        $data['APP_PREVIOUS_USER'] = '';
        if ($data['DEL_PREVIOUS_USR_UID'] != '') {
            $criteria = new Criteria();
            $criteria->addSelectColumn(UsersPeer::USR_USERNAME);
            $criteria->addSelectColumn(UsersPeer::USR_FIRSTNAME);
            $criteria->addSelectColumn(UsersPeer::USR_LASTNAME);
            $criteria->add( UsersPeer::USR_UID, $data['DEL_PREVIOUS_USR_UID'], Criteria::EQUAL );
            $dataset = UsersPeer::doSelectRS($criteria);
            $dataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $dataset->next();
            $aRow = $dataset->getRow();
            $data['DEL_PREVIOUS_USR_USERNAME']  = $aRow['USR_USERNAME'];
            $data['DEL_PREVIOUS_USR_FIRSTNAME'] = $aRow['USR_FIRSTNAME'];
            $data['DEL_PREVIOUS_USR_LASTNAME']  = $aRow['USR_LASTNAME'];
        }

        $users = new Users();
        $criteria = new Criteria();
        $criteria->addSelectColumn(SubApplicationPeer::DEL_INDEX_PARENT);
        $criteria->add( SubApplicationPeer::APP_PARENT, $data['APP_UID'], Criteria::EQUAL );
        $dataset = SubApplicationPeer::doSelectRS($criteria);
        $dataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        if ($dataset->next()) {
            $aSub = $dataset->getRow();
            if ($aSub['DEL_INDEX_PARENT'] == $data['DEL_PREVIOUS'] && !$isSelfService) {
                $users->refreshTotal($data['USR_UID'], 'add', 'inbox');
                self::create($data, $isSelfService);
                return 1;
            }
        }

        if (!$isInitSubprocess) {
            if ($data['APP_STATUS'] == 'DRAFT') {
                $users->refreshTotal($data['USR_UID'], 'add', 'draft');
            } else {
                $oRow = ApplicationPeer::retrieveByPK($data['APP_UID']);
                $aFields = $oRow->toArray( BasePeer::TYPE_FIELDNAME );
                if ($removeList) {
                    if ($data['DEL_INDEX'] == 2 || $aFields['APP_STATUS'] == 'DRAFT') {
                        $criteria = new Criteria();
                        $criteria->addSelectColumn(SubApplicationPeer::APP_UID);
                        $criteria->add( SubApplicationPeer::APP_UID, $data['APP_UID'], Criteria::EQUAL );
                        $dataset = SubApplicationPeer::doSelectRS($criteria);
                        if ($dataset->next()) {
                            $users->refreshTotal($delPreviusUsrUid, 'remove', 'inbox');
                        } else {
                            $users->refreshTotal($delPreviusUsrUid, 'remove', 'draft');
                        }
                    } else {
                        $users->refreshTotal($delPreviusUsrUid, 'remove', 'inbox');
                    }
                }
                if (!$isSelfService) {
                    $users->refreshTotal($data['USR_UID'], 'add', 'inbox');
                }
            }
        } else {
            if($data['USR_UID'] !=''){
                $users->refreshTotal($data['USR_UID'], 'add', 'inbox');
            }
            if ($dataPreviusApplication['APP_STATUS'] == 'DRAFT') {
                $users->refreshTotal($dataPreviusApplication['CURRENT_USER_UID'], 'remove', 'draft');
            } else {
                $users->refreshTotal($dataPreviusApplication['CURRENT_USER_UID'], 'remove', 'inbox');
            }
        }
        self::create($data, $isSelfService);
    }

    public function loadFilters (&$criteria, $filters)
    {
        $filter         = isset($filters['filter']) ? $filters['filter'] : "";
        $search         = isset($filters['search']) ? $filters['search'] : "";
        $process        = isset($filters['process']) ? $filters['process'] : "";
        $category       = isset($filters['category']) ? $filters['category'] : "";
        $dateFrom       = isset($filters['dateFrom']) ? $filters['dateFrom'] : "";
        $dateTo         = isset($filters['dateTo']) ? $filters['dateTo'] : "";
        $filterStatus   = isset($filters['filterStatus']) ? $filters['filterStatus'] : "";
        $newestthan     = isset($filters['newestthan'] ) ? $filters['newestthan'] : '';
        $oldestthan     = isset($filters['oldestthan'] ) ? $filters['oldestthan'] : '';

        if ($filter != '') {
            switch ($filter) {
                case 'read':
                    $criteria->add( ListInboxPeer::DEL_INIT_DATE, null, Criteria::ISNOTNULL );
                    break;
                case 'unread':
                    $criteria->add( ListInboxPeer::DEL_INIT_DATE, null, Criteria::ISNULL );
                    break;
            }
        }

        if ($search != '') {
            $criteria->add(
                $criteria->getNewCriterion( ListInboxPeer::APP_TITLE, '%' . $search . '%', Criteria::LIKE )->
                addOr( $criteria->getNewCriterion( ListInboxPeer::APP_TAS_TITLE, '%' . $search . '%', Criteria::LIKE )->
                addOr( $criteria->getNewCriterion( ListInboxPeer::APP_NUMBER, $search, Criteria::LIKE ) ) ) );
        }

        if ($process != '') {
            $criteria->add( ListInboxPeer::PRO_UID, $process, Criteria::EQUAL);
        }

        if ($category != '') {
            // INNER JOIN FOR TAS_TITLE
            $criteria->addSelectColumn(ProcessPeer::PRO_CATEGORY);
            $aConditions   = array();
            $aConditions[] = array(ListInboxPeer::PRO_UID, ProcessPeer::PRO_UID);
            $aConditions[] = array(ProcessPeer::PRO_CATEGORY, "'" . $category . "'");
            $criteria->addJoinMC($aConditions, Criteria::INNER_JOIN);
        }

        if ($dateFrom != "") {
            if ($dateTo != "") {
                if ($dateFrom == $dateTo) {
                    $dateSame = $dateFrom;
                    $dateFrom = $dateSame . " 00:00:00";
                    $dateTo = $dateSame . " 23:59:59";
                } else {
                    $dateFrom = $dateFrom . " 00:00:00";
                    $dateTo = $dateTo . " 23:59:59";
                }

                $criteria->add( $criteria->getNewCriterion( ListInboxPeer::DEL_DELEGATE_DATE, $dateFrom, Criteria::GREATER_EQUAL )->
                addAnd( $criteria->getNewCriterion( ListInboxPeer::DEL_DELEGATE_DATE, $dateTo, Criteria::LESS_EQUAL ) ) );
            } else {
                $dateFrom = $dateFrom . " 00:00:00";

                $criteria->add( ListInboxPeer::DEL_DELEGATE_DATE, $dateFrom, Criteria::GREATER_EQUAL );
            }
        } elseif ($dateTo != "") {
            $dateTo = $dateTo . " 23:59:59";

            $criteria->add( ListInboxPeer::DEL_DELEGATE_DATE, $dateTo, Criteria::LESS_EQUAL );
        }

        if ($newestthan != '') {
            $criteria->add( $criteria->getNewCriterion( ListInboxPeer::DEL_DELEGATE_DATE, $newestthan, Criteria::GREATER_THAN ));
        }

        if ($oldestthan != '') {
            $criteria->add( $criteria->getNewCriterion( ListInboxPeer::DEL_DELEGATE_DATE, $oldestthan, Criteria::LESS_THAN ));
        }

        if ($filterStatus != '') {
            switch ($filterStatus) {
                case 'ON_TIME':
                    $criteria->add( ListInboxPeer::DEL_RISK_DATE  , "TIMEDIFF(". ListInboxPeer::DEL_RISK_DATE." , NOW( ) ) > 0", Criteria::CUSTOM);
                    break;
                case 'AT_RISK':
                    $criteria->add( ListInboxPeer::DEL_RISK_DATE  , "TIMEDIFF(". ListInboxPeer::DEL_RISK_DATE .", NOW( ) ) < 0", Criteria::CUSTOM);
                    $criteria->add( ListInboxPeer::DEL_DUE_DATE  , "TIMEDIFF(". ListInboxPeer::DEL_DUE_DATE .", NOW( ) ) >  0", Criteria::CUSTOM);
                    break;
                case 'OVERDUE':
                    $criteria->add( ListInboxPeer::DEL_DUE_DATE  , "TIMEDIFF(". ListInboxPeer::DEL_DUE_DATE." , NOW( ) ) < 0", Criteria::CUSTOM);
                    break;
            }
        }
    }

    public function countTotal ($usr_uid, $filters = array())
    {
        $criteria = new Criteria();
        $criteria->add( ListInboxPeer::USR_UID, $usr_uid, Criteria::EQUAL );
        if ($filters['action'] == 'draft') {
            $criteria->add( ListInboxPeer::APP_STATUS, 'DRAFT', Criteria::EQUAL );
        } else {
            $criteria->add( ListInboxPeer::APP_STATUS, 'TO_DO', Criteria::EQUAL );
        }
        self::loadFilters($criteria, $filters);
        $total = ListInboxPeer::doCount( $criteria );
        return (int)$total;
    }

    public function loadList($usr_uid, $filters = array(), $callbackRecord = null)
    {
        $criteria = new Criteria();

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
        $criteria->add( ListInboxPeer::USR_UID, $usr_uid, Criteria::EQUAL );
        self::loadFilters($criteria, $filters);

        $sort  = (!empty($filters['sort'])) ? $filters['sort'] : "LIST_INBOX.APP_UPDATE_DATE";
        $dir   = isset($filters['dir']) ? $filters['dir'] : "ASC";
        $start = isset($filters['start']) ? $filters['start'] : "0";
        $limit = isset($filters['limit']) ? $filters['limit'] : "25";
        $paged = isset($filters['paged']) ? $filters['paged'] : 1;

        if ($filters['action'] == 'draft') {
            $criteria->add( ListInboxPeer::APP_STATUS, 'DRAFT', Criteria::EQUAL );
        } else {
            $criteria->add( ListInboxPeer::APP_STATUS, 'TO_DO', Criteria::EQUAL );
        }

        if ($dir == "DESC") {
            $criteria->addDescendingOrderByColumn($sort);
        } else {
            $criteria->addAscendingOrderByColumn($sort);
        }

        if ($paged == 1) {
            $criteria->setLimit( $limit );
            $criteria->setOffset( $start );
        }

        $dataset = ListInboxPeer::doSelectRS($criteria, Propel::getDbConnection('workflow_ro') );
        $dataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $data = array();
        $aPriorities = array ('1' => 'VL','2' => 'L','3' => 'N','4' => 'H','5' => 'VH');
        while ($dataset->next()) {
            $aRow = (is_null($callbackRecord))? $dataset->getRow() : $callbackRecord($dataset->getRow());

            $aRow['DEL_PRIORITY'] = G::LoadTranslation( "ID_PRIORITY_{$aPriorities[$aRow['DEL_PRIORITY']]}" );
            $data[] = $aRow;
        }
        return $data;
    }

    public function getTaskPriority($taskUid, $proUid, $appUid)
    {
        $criteria = new Criteria();
        $criteria->addSelectColumn(TaskPeer::TAS_PRIORITY_VARIABLE);
        $criteria->add( TaskPeer::TAS_UID, $taskUid, Criteria::EQUAL );
        $criteria->add( TaskPeer::PRO_UID, $proUid, Criteria::EQUAL );
        $dataset = TaskPeer::doSelectRS($criteria);
        $dataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $dataset->next();
        $aRow = $dataset->getRow();
        $priority = $aRow['TAS_PRIORITY_VARIABLE'];
        if(strlen($priority)>2){
            $oCase = new Cases();
            $aData = $oCase->loadCase( $appUid );
            $priorityLabel = substr($priority, 2,strlen($priority));
            if (isset( $aData['APP_DATA'][$priorityLabel] )) {
                $priority = $aData['APP_DATA'][$priorityLabel];
            }
        }
        return $priority != "" ? $priority : 3;
    }

    public function getAppDelegationInfo($filters, $fieldName)
    {
        $criteria = new Criteria();
        eval('$criteria->addSelectColumn( AppDelegationPeer::'.$fieldName.');');
        foreach($filters as $k => $v) {
           eval('$criteria->add( AppDelegationPeer::'.$k.',$v, Criteria::EQUAL);');
        }
        $dataset = AppDelegationPeer::doSelectRS($criteria);
        $dataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $dataset->next();
        $aRow = $dataset->getRow();
        return isset($aRow[$fieldName]) ? $aRow[$fieldName] : NULL;
    }
}

