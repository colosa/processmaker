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
class ListUnassigned extends BaseListUnassigned
{
    /**
     * Create List Unassigned Table
     *
     * @param type $data
     * @return type
     *
     */
    public function create($data)
    {
        $con = Propel::getConnection( ListUnassignedPeer::DATABASE_NAME );
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
            return $result;
        } catch(Exception $e) {
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
        $con = Propel::getConnection( ListUnassignedPeer::DATABASE_NAME );
        try {
            $con->begin();
            $this->setNew( false );
            $this->fromArray( $data, BasePeer::TYPE_FIELDNAME );
            if ($this->validate()) {
                $result = $this->save();
                $con->commit();
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
     * Remove List Unassigned
     *
     * @param type $seqName
     * @return type
     * @throws type
     *
     */
    public function remove ($app_uid)
    {
        $con = Propel::getConnection( ListUnassignedPeer::DATABASE_NAME );
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

    public function newRow ($data, $delPreviusUsrUid) {
        $data['UNA_UID'] = (isset($data['UNA_UID'])) ? $data['UNA_UID']: G::GenerateUniqueId() ;
        $data['DEL_PREVIOUS_USR_UID'] = $delPreviusUsrUid;
        $data['DEL_DUE_DATE'] = $data['DEL_TASK_DUE_DATE'];

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
            $data['APP_PREVIOUS_USR_USERNAME']  = $aRow['USR_USERNAME'];
            $data['APP_PREVIOUS_USR_FIRSTNAME'] = $aRow['USR_FIRSTNAME'];
            $data['APP_PREVIOUS_USR_LASTNAME']  = $aRow['USR_LASTNAME'];
        }

        self::create($data);
        return $data['UNA_UID'];
    }

    public function loadFilters (&$criteria, $filters)
    {
        $filter = isset($filters['filter']) ? $filters['filter'] : "";
        $search = isset($filters['search']) ? $filters['search'] : "";
        $process = isset($filters['process']) ? $filters['process'] : "";
        $category = isset($filters['category']) ? $filters['category'] : "";
        $dateFrom = isset($filters['dateFrom']) ? $filters['dateFrom'] : "";
        $dateTo = isset($filters['dateTo']) ? $filters['dateTo'] : "";

        if ($filter != '') {
            switch ($filter) {
                case 'read':
                    $criteria->add( ListUnassignedPeer::DEL_INIT_DATE, null, Criteria::ISNOTNULL );
                    break;
                case 'unread':
                    $criteria->add( ListUnassignedPeer::DEL_INIT_DATE, null, Criteria::ISNULL );
                    break;
            }
        }

        if ($search != '') {
            $criteria->add(
                $criteria->getNewCriterion( ListUnassignedPeer::APP_TITLE, '%' . $search . '%', Criteria::LIKE )->
                    addOr( $criteria->getNewCriterion( ListUnassignedPeer::APP_TAS_TITLE, '%' . $search . '%', Criteria::LIKE )->
                        addOr( $criteria->getNewCriterion( ListUnassignedPeer::APP_NUMBER, $search, Criteria::LIKE ) ) ) );
        }

        if ($process != '') {
            $criteria->add( ListUnassignedPeer::PRO_UID, $process, Criteria::EQUAL);
        }

        if ($category != '') {
            // INNER JOIN FOR TAS_TITLE
            $criteria->addSelectColumn(ProcessPeer::PRO_CATEGORY);
            $aConditions   = array();
            $aConditions[] = array(ListUnassignedPeer::PRO_UID, ProcessPeer::PRO_UID);
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

                $criteria->add( $criteria->getNewCriterion( ListUnassignedPeer::DEL_DELEGATE_DATE, $dateFrom, Criteria::GREATER_EQUAL )->
                    addAnd( $criteria->getNewCriterion( ListUnassignedPeer::DEL_DELEGATE_DATE, $dateTo, Criteria::LESS_EQUAL ) ) );
            } else {
                $dateFrom = $dateFrom . " 00:00:00";

                $criteria->add( ListUnassignedPeer::DEL_DELEGATE_DATE, $dateFrom, Criteria::GREATER_EQUAL );
            }
        } elseif ($dateTo != "") {
            $dateTo = $dateTo . " 23:59:59";

            $criteria->add( ListUnassignedPeer::DEL_DELEGATE_DATE, $dateTo, Criteria::LESS_EQUAL );
        }
    }

    public function countTotal ($usr_uid, $filters = array())
    {
        $criteria = new Criteria();
        $aConditions   = array();
        $aConditions[] = array(ListUnassignedPeer::UNA_UID, ListUnassignedGroupPeer::UNA_UID);
        $aConditions[] = array(ListUnassignedGroupPeer::USR_UID, "'" . $usr_uid . "'");
        $criteria->addJoinMC($aConditions, Criteria::INNER_JOIN);
        self::loadFilters($criteria, $filters);
        $total = ListUnassignedPeer::doCount( $criteria );
        return (int)$total;
    }

    public function loadList($usr_uid, $filters = array(), $callbackRecord = null)
    {
        $resp = array();
        $criteria = new Criteria();

        $criteria->addSelectColumn(ListUnassignedPeer::APP_UID);
        $criteria->addSelectColumn(ListUnassignedPeer::DEL_INDEX);
        $criteria->addSelectColumn(ListUnassignedPeer::TAS_UID);
        $criteria->addSelectColumn(ListUnassignedPeer::PRO_UID);
        $criteria->addSelectColumn(ListUnassignedPeer::APP_NUMBER);
        $criteria->addSelectColumn(ListUnassignedPeer::APP_TITLE);
        $criteria->addSelectColumn(ListUnassignedPeer::APP_PRO_TITLE);
        $criteria->addSelectColumn(ListUnassignedPeer::APP_TAS_TITLE);
        $criteria->addSelectColumn(ListUnassignedPeer::APP_PREVIOUS_USR_USERNAME);
        $criteria->addSelectColumn(ListUnassignedPeer::APP_PREVIOUS_USR_FIRSTNAME);
        $criteria->addSelectColumn(ListUnassignedPeer::APP_PREVIOUS_USR_LASTNAME);
        $criteria->addSelectColumn(ListUnassignedPeer::DEL_PREVIOUS_USR_UID);
        $criteria->addSelectColumn(ListUnassignedPeer::DEL_DELEGATE_DATE);
        $criteria->addSelectColumn(ListUnassignedPeer::DEL_DUE_DATE);
        $criteria->addSelectColumn(ListUnassignedPeer::DEL_PRIORITY);
        $aConditions   = array();
        $aConditions[] = array(ListUnassignedPeer::UNA_UID, ListUnassignedGroupPeer::UNA_UID);
        $aConditions[] = array(ListUnassignedGroupPeer::USR_UID, "'" . $usr_uid . "'");
        $criteria->addJoinMC($aConditions, Criteria::INNER_JOIN);

        self::loadFilters($criteria, $filters);

        $sort  = (!empty($filters['sort'])) ? $filters['sort'] : "LIST_UNASSIGNED.DEL_DELEGATE_DATE";
        $dir   = isset($filters['dir']) ? $filters['dir'] : "ASC";
        $start = isset($filters['start']) ? $filters['start'] : "0";
        $limit = isset($filters['limit']) ? $filters['limit'] : "25";
        $paged = isset($filters['paged']) ? $filters['paged'] : 1;
        $count = isset($filters['count']) ? $filters['count'] : 1;

        if ($count == 1) {
            $criteriaTotal = clone $criteria;
            $resp['total'] = ListUnassignedPeer::doCount( $criteriaTotal );
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

        $dataset = ListUnassignedPeer::doSelectRS($criteria);
        $dataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $data = array();
        $aPriorities = array ('1' => 'VL','2' => 'L','3' => 'N','4' => 'H','5' => 'VH');
        while ($dataset->next()) {
            $aRow = (is_null($callbackRecord))? $dataset->getRow() : $callbackRecord($dataset->getRow());

            $aRow['DEL_PRIORITY'] = G::LoadTranslation( "ID_PRIORITY_{$aPriorities[$aRow['DEL_PRIORITY']]}" );
            $data[] = $aRow;
        }

        if ($count == 1) {
            $resp['data'] = $data;
        } else {
            $resp = $data;
        }
        return $resp;
    }
    /**
     * Generate Data
     *
     * @return object criteria
     */
    public function generateData($appUid,$delPreviusUsrUid){
        try {
            G::LoadClass("case");

            //Generate data
            $case = new Cases();

            $criteria = new Criteria("workflow");

            $criteria->addSelectColumn(AppDelegationPeer::APP_UID);
            $criteria->addSelectColumn(AppDelegationPeer::DEL_INDEX);
            $criteria->addSelectColumn(ApplicationPeer::APP_DATA);
            $criteria->addSelectColumn(AppDelegationPeer::PRO_UID);
            $criteria->addSelectColumn(AppDelegationPeer::DEL_TASK_DUE_DATE);
            $criteria->addSelectColumn(TaskPeer::TAS_UID);
            $criteria->addSelectColumn(TaskPeer::TAS_GROUP_VARIABLE);
            $criteria->addJoin(AppDelegationPeer::APP_UID, ApplicationPeer::APP_UID, Criteria::LEFT_JOIN);
            $criteria->addJoin(AppDelegationPeer::TAS_UID, TaskPeer::TAS_UID, Criteria::LEFT_JOIN);
            $criteria->add(TaskPeer::TAS_ASSIGN_TYPE, "SELF_SERVICE", Criteria::EQUAL);
            //$criteria->add(TaskPeer::TAS_GROUP_VARIABLE, "", Criteria::NOT_EQUAL);
            $criteria->add(AppDelegationPeer::USR_UID, "", Criteria::EQUAL);
            $criteria->add(AppDelegationPeer::DEL_THREAD_STATUS, "OPEN", Criteria::EQUAL);
            $criteria->add(AppDelegationPeer::APP_UID, $appUid, Criteria::EQUAL);

            $rsCriteria = AppDelegationPeer::doSelectRS($criteria);
            $rsCriteria->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            while ($rsCriteria->next()) {
                $row = $rsCriteria->getRow();

                $applicationData = $case->unserializeData($row["APP_DATA"]);
                $taskGroupVariable = trim($row["TAS_GROUP_VARIABLE"], " @#");
                $delPreviusUsrUid = '';
                $unaUid = $this->newRow($row,$delPreviusUsrUid);
                //Selfservice by group
                if ($taskGroupVariable != "" && isset($applicationData[$taskGroupVariable]) && trim($applicationData[$taskGroupVariable]) != "") {
                   $gprUid = trim($applicationData[$taskGroupVariable]);
                   //Define Users by Group
                   $gpr = new GroupUser();
                   $arrayUsers = $gpr->getAllGroupUser($gprUid);
                   foreach($arrayUsers as $urow){
                       $newRow["USR_UID"] = $urow["USR_UID"];
                       $listUnassignedGpr = new ListUnassignedGroup();
                       $listUnassignedGpr->newRow($unaUid,$urow["USR_UID"],"GROUP",$gprUid);
                   }
                } else {
                    //Define all users assigned to Task
                    $task = new TaskUser();
                    $arrayUsers = $task->getAllUsersTask($row["TAS_UID"]);
                    $arrayUniqueUsers = array( ) ;
                    foreach($arrayUsers as $urow){
                        if( !in_array( $urow["USR_UID"], $arrayUniqueUsers) ){
                            $arrayUniqueUsers[]=$urow["USR_UID"] ;
                            $newRow["USR_UID"] = $urow["USR_UID"];
                            $listUnassignedGpr = new ListUnassignedGroup();
                            $listUnassignedGpr->newRow($unaUid,$urow["USR_UID"],"USER","");
                        }
                   }
                }
            }
        } catch (Exception $e) {
            throw $e;
        }
    }
}

