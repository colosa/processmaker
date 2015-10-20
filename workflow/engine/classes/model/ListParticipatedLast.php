<?php

require_once 'classes/model/om/BaseListParticipatedLast.php';


/**
 * Skeleton subclass for representing a row from the 'LIST_PARTICIPATED_LAST' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    classes.model
*/

class ListParticipatedLast extends BaseListParticipatedLast
{
    /**
     * Create List Participated History Table
     *
     * @param type $data
     * @return type
     *
     */
    public function create($data)
    {
        $criteria = new Criteria();
        $criteria->addSelectColumn(ApplicationPeer::APP_STATUS);
        $criteria->add( ApplicationPeer::APP_UID, $data['APP_UID'], Criteria::EQUAL );
        $dataset = UsersPeer::doSelectRS($criteria);
        $dataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $dataset->next();
        $aRow = $dataset->getRow();
        $data['APP_STATUS']  = $aRow['APP_STATUS'];

        if ($data['USR_UID'] != 'SELF_SERVICES') {
           if($data['USR_UID'] != ''){
            $criteria = new Criteria();
            $criteria->addSelectColumn(UsersPeer::USR_USERNAME);
            $criteria->addSelectColumn(UsersPeer::USR_FIRSTNAME);
            $criteria->addSelectColumn(UsersPeer::USR_LASTNAME);
            $criteria->add( UsersPeer::USR_UID, $data['USR_UID'], Criteria::EQUAL );
            $dataset = UsersPeer::doSelectRS($criteria);
            $dataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $dataset->next();
            $aRow = $dataset->getRow();
            $data['DEL_CURRENT_USR_USERNAME']  = $aRow['USR_USERNAME'];
            $data['DEL_CURRENT_USR_FIRSTNAME'] = $aRow['USR_FIRSTNAME'];
            $data['DEL_CURRENT_USR_LASTNAME']  = $aRow['USR_LASTNAME'];

            $users = new Users();
            $users->refreshTotal($data['USR_UID'], 'add', 'participated');
            }
        }
        
        if($this->primaryKeysExists($data)) {
            return;
        }

        $con = Propel::getConnection( ListParticipatedLastPeer::DATABASE_NAME );
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
     *  Update List Participated History Table
     *
     * @param type $data
     * @return type
     * @throws type
     */
    public function update($data)
    {
        $data['DEL_THREAD_STATUS'] = (isset($data['DEL_THREAD_STATUS'])) ? $data['DEL_THREAD_STATUS'] : 'OPEN';
        $con = Propel::getConnection( ListParticipatedLastPeer::DATABASE_NAME );
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
     * Refresh List Participated Last
     *
     * @param type $seqName
     * @return type
     * @throws type
     *
     */
    public function refresh ($data, $isSelfService = false)
    {
        $data['APP_STATUS'] = (empty($data['APP_STATUS'])) ? 'TO_DO' : $data['APP_STATUS'];
        if (!$isSelfService) {
            if ($data["USR_UID"] == "") {
                return;
            }

            $criteria = new Criteria();
            $criteria->addSelectColumn(UsersPeer::USR_USERNAME);
            $criteria->addSelectColumn(UsersPeer::USR_FIRSTNAME);
            $criteria->addSelectColumn(UsersPeer::USR_LASTNAME);
            $criteria->add( UsersPeer::USR_UID, $data['USR_UID'], Criteria::EQUAL );
            $dataset = UsersPeer::doSelectRS($criteria);
            $dataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $dataset->next();
            $aRow = $dataset->getRow();

            //Update - WHERE
            $criteriaWhere = new Criteria("workflow");
            $criteriaWhere->add(ListParticipatedLastPeer::APP_UID, $data["APP_UID"], Criteria::EQUAL);
            //Update - SET
            $criteriaSet = new Criteria("workflow");
            $criteriaSet->add(ListParticipatedLastPeer::DEL_CURRENT_USR_USERNAME, $aRow['USR_USERNAME']);
            $criteriaSet->add(ListParticipatedLastPeer::DEL_CURRENT_USR_FIRSTNAME, $aRow['USR_FIRSTNAME']);
            $criteriaSet->add(ListParticipatedLastPeer::DEL_CURRENT_USR_LASTNAME, $aRow['USR_LASTNAME']);
            BasePeer::doUpdate($criteriaWhere, $criteriaSet, Propel::getConnection("workflow"));

        }
        $this->update($data);

    }
    /**
     * Remove List Participated History
     *
     * @param type $seqName
     * @return type
     * @throws type
     *
     */
    public function remove ($app_uid, $usr_uid, $del_index)
    {
        try {
            $flagDelete = false;

            if (!is_null(ListParticipatedLastPeer::retrieveByPK($app_uid, $usr_uid, $del_index))) {
                $criteria = new Criteria("workflow");

                $criteria->add(ListParticipatedLastPeer::APP_UID, $app_uid);
                $criteria->add(ListParticipatedLastPeer::USR_UID, $usr_uid);
                $criteria->add(ListParticipatedLastPeer::DEL_INDEX, $del_index);

                $result = ListParticipatedLastPeer::doDelete($criteria);

                $flagDelete = true;
            } else {
                $criteria = new Criteria("workflow");

                $criteria->add(ListParticipatedLastPeer::APP_UID, $app_uid);
                $criteria->add(ListParticipatedLastPeer::USR_UID, $usr_uid);

                $rsCriteria = ListParticipatedLastPeer::doSelectRS($criteria);

                if ($rsCriteria->next()) {
                    $criteria2 = clone $criteria;

                    $result = ListParticipatedLastPeer::doDelete($criteria2);

                    $flagDelete = true;
                }
            }

            if ($flagDelete) {
                $user = new Users();
                $user->refreshTotal($usr_uid, "removed", "participated");
            }
        } catch (Exception $e) {
            throw $e;
        }
    }


    public function loadFilters (&$criteria, $filters)
    {
        $filter = isset($filters['filter']) ? $filters['filter'] : "";
        $search = isset($filters['search']) ? $filters['search'] : "";
        $process = isset($filters['process']) ? $filters['process'] : "";
        $category = isset($filters['category']) ? $filters['category'] : "";
        $dateFrom = isset($filters['dateFrom']) ? $filters['dateFrom'] : "";
        $dateTo = isset($filters['dateTo']) ? $filters['dateTo'] : "";
        $filterStatus = isset($filters['filterStatus']) ? $filters['filterStatus'] : "";
        $newestthan     = isset($filters['newestthan'] ) ? $filters['newestthan'] : '';
        $oldestthan     = isset($filters['oldestthan'] ) ? $filters['oldestthan'] : '';

        if ($filter != '') {
            switch ($filter) {
                case 'read':
                    $criteria->add( ListParticipatedLastPeer::DEL_INIT_DATE, null, Criteria::ISNOTNULL );
                    break;
                case 'unread':
                    $criteria->add( ListParticipatedLastPeer::DEL_INIT_DATE, null, Criteria::ISNULL );
                    break;
            }
        }

        if ($search != '' ) {
            $criteria->add(
                $criteria->getNewCriterion( ListParticipatedLastPeer::APP_TITLE, '%' . $search . '%', Criteria::LIKE )->
                    addOr( $criteria->getNewCriterion( ListParticipatedLastPeer::APP_TAS_TITLE, '%' . $search . '%', Criteria::LIKE )->
                            addOr( $criteria->getNewCriterion( ListParticipatedLastPeer::APP_NUMBER, $search, Criteria::LIKE ) ) ) );
        }

        if($filterStatus != ''){
            $criteria->add(ListParticipatedLastPeer::APP_STATUS, '%' . $filterStatus . '%', Criteria::LIKE );
        }

        if ($process != '') {
            $criteria->add( ListParticipatedLastPeer::PRO_UID, $process, Criteria::EQUAL);
        }

        if ($category != '') {
            // INNER JOIN FOR TAS_TITLE
            $criteria->addSelectColumn(ProcessPeer::PRO_CATEGORY);
            $aConditions   = array();
            $aConditions[] = array(ListParticipatedLastPeer::PRO_UID, ProcessPeer::PRO_UID);
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

                $criteria->add( $criteria->getNewCriterion( ListParticipatedLastPeer::DEL_DELEGATE_DATE, $dateFrom, Criteria::GREATER_EQUAL )->
                    addAnd( $criteria->getNewCriterion( ListParticipatedLastPeer::DEL_DELEGATE_DATE, $dateTo, Criteria::LESS_EQUAL ) ) );
            } else {
                $dateFrom = $dateFrom . " 00:00:00";

                $criteria->add( ListParticipatedLastPeer::DEL_DELEGATE_DATE, $dateFrom, Criteria::GREATER_EQUAL );
            }
        } elseif ($dateTo != "") {
            $dateTo = $dateTo . " 23:59:59";

            $criteria->add( ListParticipatedLastPeer::DEL_DELEGATE_DATE, $dateTo, Criteria::LESS_EQUAL );
        }

        if ($newestthan != '') {
            $criteria->add( $criteria->getNewCriterion( ListParticipatedLastPeer::DEL_DELEGATE_DATE, $newestthan, Criteria::GREATER_THAN ));
        }

        if ($oldestthan != '') {
            $criteria->add( $criteria->getNewCriterion( ListParticipatedLastPeer::DEL_DELEGATE_DATE, $oldestthan, Criteria::LESS_THAN ));
        }
    }

    public function countTotal ($usr_uid, $filters = array())
    {
        $criteria = new Criteria();
        $criteria->add( ListParticipatedLastPeer::USR_UID, $usr_uid, Criteria::EQUAL );
        self::loadFilters($criteria, $filters);
        $total = ListParticipatedLastPeer::doCount( $criteria );
        return (int)$total;
    }

    public function loadList($usr_uid, $filters = array(), $callbackRecord = null)
    {
        $criteria = new Criteria();

        $criteria->addSelectColumn(ListParticipatedLastPeer::APP_UID);
        $criteria->addSelectColumn(ListParticipatedLastPeer::DEL_INDEX);
        $criteria->addSelectColumn(ListParticipatedLastPeer::USR_UID);
        $criteria->addSelectColumn(ListParticipatedLastPeer::TAS_UID);
        $criteria->addSelectColumn(ListParticipatedLastPeer::PRO_UID);
        $criteria->addSelectColumn(ListParticipatedLastPeer::APP_NUMBER);
        $criteria->addSelectColumn(ListParticipatedLastPeer::APP_TITLE);
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
        $criteria->addSelectColumn(ListParticipatedLastPeer::DEL_DELEGATE_DATE);
        $criteria->addSelectColumn(ListParticipatedLastPeer::DEL_INIT_DATE);
        $criteria->addSelectColumn(ListParticipatedLastPeer::DEL_DUE_DATE);
        $criteria->addSelectColumn(ListParticipatedLastPeer::DEL_PRIORITY);
        $criteria->addSelectColumn(ListParticipatedLastPeer::DEL_THREAD_STATUS);
        $criteria->add( ListParticipatedLastPeer::USR_UID, $usr_uid, Criteria::EQUAL );
        self::loadFilters($criteria, $filters);

        $sort  = (!empty($filters['sort'])) ? $filters['sort'] : "DEL_DELEGATE_DATE";
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
            $criteria->setLimit( $limit );
            $criteria->setOffset( $start );
        }

        $dataset = ListParticipatedLastPeer::doSelectRS($criteria, Propel::getDbConnection('workflow_ro') );
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
    
    public function primaryKeysExists($data) {
        $criteria = new Criteria("workflow");
        $criteria->add(ListParticipatedLastPeer::APP_UID, $data['APP_UID']);
        $criteria->add(ListParticipatedLastPeer::USR_UID, $data['USR_UID']);
        $criteria->add(ListParticipatedLastPeer::DEL_INDEX, $data['DEL_INDEX']);    
        $dataset = UsersPeer::doSelectRS($criteria);
        $dataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $dataset->next();
        $aRow = $dataset->getRow();
        if(is_array($aRow)) {
            if(sizeof($aRow)) {
                return true;
            }
        }
        return false;
    }
}

