<?php

use ProcessMaker\Plugins\PluginRegistry;


class Applications
{
    /**
     * This function return information by searching cases
     *
     * The query is related to advanced search with diferents filters
     * We can search by process, status of case, category of process, users, delegate date from and to
     *
     * @param string $userUid
     * @param integer $start for the pagination
     * @param integer $limit for the pagination
     * @param string $search
     * @param integer $process the pro_id
     * @param integer $status of the case
     * @param string $dir if the order is DESC or ASC
     * @param string $sort name of column by sort
     * @param string $category uid for the process
     * @param date $dateFrom
     * @param date $dateTo
     * @param string $columnSearch name of column for a specific search
     * @return array $result result of the query
     */
    public function searchAll(
        $userUid,
        $start = null,
        $limit = null,
        $search = null,
        $process = null,
        $status = null,
        $dir = null,
        $sort = null,
        $category = null,
        $dateFrom = null,
        $dateTo = null,
        $columnSearch = 'APP_TITLE'
    ) {
        //Exclude the Task Dummies in the delegations
        $arrayTaskTypeToExclude = array("WEBENTRYEVENT", "END-MESSAGE-EVENT", "START-MESSAGE-EVENT", "INTERMEDIATE-THROW-MESSAGE-EVENT", "INTERMEDIATE-CATCH-MESSAGE-EVENT");

        //Start the connection to database
        $con = Propel::getConnection(AppDelegationPeer::DATABASE_NAME);
        $con->begin();
        $stmt = $con->createStatement();

        $sqlData = "SELECT
                    STRAIGHT_JOIN APPLICATION.APP_NUMBER,
                    APPLICATION.APP_UID,
                    APPLICATION.APP_STATUS,
                    APPLICATION.APP_STATUS AS APP_STATUS_LABEL,
                    APPLICATION.PRO_UID,
                    APPLICATION.APP_CREATE_DATE,
                    APPLICATION.APP_FINISH_DATE,
                    APPLICATION.APP_UPDATE_DATE,
                    APPLICATION.APP_TITLE,
                    APP_DELEGATION.USR_UID,
                    APP_DELEGATION.TAS_UID,
                    APP_DELEGATION.DEL_INDEX,
                    APP_DELEGATION.DEL_LAST_INDEX,
                    APP_DELEGATION.DEL_DELEGATE_DATE,
                    APP_DELEGATION.DEL_INIT_DATE,
                    APP_DELEGATION.DEL_FINISH_DATE,
                    APP_DELEGATION.DEL_TASK_DUE_DATE,
                    APP_DELEGATION.DEL_RISK_DATE,
                    APP_DELEGATION.DEL_THREAD_STATUS,
                    APP_DELEGATION.DEL_PRIORITY,
                    APP_DELEGATION.DEL_DURATION,
                    APP_DELEGATION.DEL_QUEUE_DURATION,
                    APP_DELEGATION.DEL_STARTED,
                    APP_DELEGATION.DEL_DELAY_DURATION,
                    APP_DELEGATION.DEL_FINISHED,
                    APP_DELEGATION.DEL_DELAYED,
                    APP_DELEGATION.DEL_DELAY_DURATION,
                    TASK.TAS_TITLE AS APP_TAS_TITLE,
                    TASK.TAS_TYPE AS APP_TAS_TYPE,
                    USERS.USR_LASTNAME,
                    USERS.USR_FIRSTNAME,
                    USERS.USR_USERNAME,
                    PROCESS.PRO_TITLE AS APP_PRO_TITLE
                FROM APP_DELEGATION
        ";
        $sqlData .= " LEFT JOIN APPLICATION ON (APP_DELEGATION.APP_NUMBER = APPLICATION.APP_NUMBER)";
        $sqlData .= " LEFT JOIN TASK ON (APP_DELEGATION.TAS_ID = TASK.TAS_ID)";
        $sqlData .= " LEFT JOIN USERS ON (APP_DELEGATION.USR_ID = USERS.USR_ID)";
        $sqlData .= " LEFT JOIN PROCESS ON (APP_DELEGATION.PRO_ID = PROCESS.PRO_ID)";

        $sqlData .= " WHERE TASK.TAS_TYPE NOT IN ('" . implode("','",$arrayTaskTypeToExclude) . "')";
        switch ($status) {
            case 1: //DRAFT
                $sqlData .= " AND APP_DELEGATION.DEL_THREAD_STATUS='OPEN'";
                $sqlData .= " AND APPLICATION.APP_STATUS_ID = 1";
                break;
            case 2: //TO_DO
                $sqlData .= " AND APP_DELEGATION.DEL_THREAD_STATUS='OPEN'";
                $sqlData .= " AND APPLICATION.APP_STATUS_ID = 2";
                break;
            case 3: //COMPLETED
                $sqlData .= " AND APPLICATION.APP_STATUS_ID = 3";
                $sqlData .= " AND APP_DELEGATION.DEL_LAST_INDEX = 1";
                break;
            case 4: //CANCELLED
                $sqlData .= " AND APPLICATION.APP_STATUS_ID = 4";
                $sqlData .= " AND APP_DELEGATION.DEL_LAST_INDEX = 1";
                break;
            case "PAUSED": //This status is not considered in the search, maybe we can add in the new versions
                $sqlData .= " AND APPLICATION.APP_STATUS = 'TO_DO'";
                break;
            default: //All status
                $sqlData .= " AND (APP_DELEGATION.DEL_THREAD_STATUS = 'OPEN' ";
                $sqlData .= " OR (APP_DELEGATION.DEL_THREAD_STATUS = 'CLOSED' AND APP_DELEGATION.DEL_LAST_INDEX = 1)) ";
                break;
        }

        if (!empty($userUid)) {
            $sqlData .= " AND APP_DELEGATION.USR_ID = " . $userUid;
        }

        if (!empty($process)) {
            $sqlData .= " AND APP_DELEGATION.PRO_ID = " . $process;
        }

        if (!empty($category)) {
            $category = mysql_real_escape_string($category);
            $sqlData .= " AND PROCESS.PRO_CATEGORY = '{$category}'";
        }

        if (!empty($search)) {
            //If the filter is related to the APPLICATION table: APP_NUMBER or APP_TITLE
            if ($columnSearch === 'APP_NUMBER' || $columnSearch === 'APP_TITLE') {
                $sqlSearch = "SELECT APPLICATION.APP_NUMBER FROM APPLICATION";
                $sqlSearch .= " WHERE APPLICATION.{$columnSearch} LIKE '%{$search}%'";
                switch ($columnSearch) {
                    case 'APP_TITLE':
                        break;
                    case 'APP_NUMBER':
                        //Cast the search criteria to string
                        if (!is_string($search)) {
                            $search = (string)$search;
                        }
                        //Only if is integer we will to add to greater equal in the query
                        if (substr($search, 0, 1) != '0' && ctype_digit($search)) {
                            $sqlSearch .= " AND APPLICATION.{$columnSearch} >= {$search}";
                        }
                        break;
                }
                if (!empty($start)) {
                    $sqlSearch .= " LIMIT $start, " . $limit;
                } else {
                    $sqlSearch .= " LIMIT " . $limit;
                }
                $dataset = $stmt->executeQuery($sqlSearch);
                $appNumbers = array(-1);
                while ($dataset->next()) {
                    $newRow = $dataset->getRow();
                    array_push($appNumbers, $newRow['APP_NUMBER']);
                }
                $sqlData .= " AND APP_DELEGATION.APP_NUMBER IN (" . implode(",", $appNumbers) . ")";
            }
            //If the filter is related to the TASK table: TAS_TITLE
            if ($columnSearch === 'TAS_TITLE') {
                $sqlData .= " AND TASK.TAS_TITLE LIKE '%{$search}%' ";
            }

        }

        if (!empty($dateFrom)) {
            $sqlData .= " AND APP_DELEGATION.DEL_DELEGATE_DATE >= '{$dateFrom}'";
        }

        if (!empty($dateTo)) {
            $dateTo = $dateTo . " 23:59:59";
            $sqlData .= " AND APP_DELEGATION.DEL_DELEGATE_DATE <= '{$dateTo}'";
        }

        //Add the additional filters
        if (!empty($sort) && empty($search)) {
            switch ($sort) {
                case 'APP_NUMBER':
                    //The order by APP_DELEGATION.APP_NUMBER is must be fast than APPLICATION.APP_NUMBER
                    $sort = 'APP_DELEGATION.APP_NUMBER';
                    break;
                case 'APP_CURRENT_USER':
                    //The column APP_CURRENT_USER is result of concat those fields
                    $sort = 'USR_LASTNAME, USR_FIRSTNAME';
                    break;
            }
            $sqlData .= " ORDER BY " . $sort;
        }

        //Sorts the records in descending order by default
        if (!empty($dir) && empty($search)) {
            $sqlData .= "  " . $dir;
        }

        //Define the number of records by return
        if(empty($limit)) {
            $limit = 25;
        }
        if (!empty($start) && empty($search)) {
            $sqlData .= " LIMIT $start, " . $limit;
        } else {
            $sqlData .= " LIMIT " . $limit;
        }

        $oDataset = $stmt->executeQuery($sqlData);
        $result = array ();
        //By performance enable always the pagination
        $result['totalCount'] = $start + $limit + 1;
        $rows = array();
        $aPriorities = array ('1' => 'VL','2' => 'L','3' => 'N','4' => 'H','5' => 'VH');
        while ($oDataset->next()) {
            $aRow = $oDataset->getRow();
            if (isset( $aRow['APP_STATUS'] )) {
                $aRow['APP_STATUS_LABEL'] = G::LoadTranslation( "ID_{$aRow['APP_STATUS']}" );
            }
            if (isset( $aRow['DEL_PRIORITY'] )) {
                $aRow['DEL_PRIORITY'] = G::LoadTranslation( "ID_PRIORITY_{$aPriorities[$aRow['DEL_PRIORITY']]}" );
            }
            $aRow["APP_CURRENT_USER"] = $aRow["USR_LASTNAME"].' '.$aRow["USR_FIRSTNAME"];
            $aRow["APPDELCR_APP_TAS_TITLE"] = '';
            $aRow["USRCR_USR_UID"] = $aRow["USR_UID"];
            $aRow["USRCR_USR_FIRSTNAME"] = $aRow["USR_FIRSTNAME"];
            $aRow["USRCR_USR_LASTNAME"] = $aRow["USR_LASTNAME"];
            $aRow["USRCR_USR_USERNAME"] = $aRow["USR_USERNAME"];
            $aRow["APP_OVERDUE_PERCENTAGE"] = '';
            $rows[] = $aRow;
        }
        $result['data'] = $rows;
        return $result;
    }

    public function getAll(
        $userUid,
        $start = null,
        $limit = null,
        $action = null,
        $filter = null,
        $search = null,
        $process = null,
        $status = null,
        $type = null,
        $dateFrom = null,
        $dateTo = null,
        $callback = null,
        $dir = null,
        $sort = "APP_CACHE_VIEW.APP_NUMBER",
        $category = null,
        $configuration = true,
        $paged = true,
        $newerThan = '',
        $oldestThan = ''
    ) {
        $callback = isset($callback)? $callback : "stcCallback1001";
        $dir = isset($dir)? $dir : "DESC";

        if (isset($sort)) {
            $parser = new PHPSQLParser($sort);
            $sort = $parser->parsed;
            $sort = $sort[''][0];
        }

        $sort = isset($sort)? $sort : "";
        $start = isset($start)? $start : "0";
        $limit = isset($limit)? $limit : "25";
        $filter = isset($filter)? $filter : "";
        $search = isset($search)? $search : "";
        $process = isset($process)? $process : "";
        $category = isset($category)? $category : "";
        $status = isset($status)? $status : "";
        $action = isset($action)? $action : "todo";
        $type = isset($type)? $type : "extjs";
        $dateFrom = isset($dateFrom)? $dateFrom : "";
        $dateTo = isset($dateTo)? $dateTo : "";

        $oAppCache = new AppCacheView();

        if ($configuration == true) {
            //get data configuration
            $conf = new Configurations();
            $confCasesList = $conf->getConfiguration("casesList", ($action == "search" || $action == "simple_search")? "search" : $action);
            $oAppCache->confCasesList = $confCasesList;
        }

        $delimiter = DBAdapter::getStringDelimiter();

        // get the action based list
        switch ($action) {
            case "draft":
                $Criteria = $oAppCache->getDraftListCriteria($userUid);
                $CriteriaCount = $oAppCache->getDraftCountCriteria($userUid);
                break;
            case "sent":
                $Criteria = $oAppCache->getSentListCriteria($userUid);
                $CriteriaCount = $oAppCache->getSentCountCriteria($userUid);

                if (!empty($status)) {
                    $Criteria->add(AppCacheViewPeer::APP_STATUS, $status);
                    $CriteriaCount->add(AppCacheViewPeer::APP_STATUS, $status);
                }
                break;
            case "selfservice":
            case "unassigned":
                //$userUid can't be empty or null
                $Criteria = $oAppCache->getUnassignedListCriteria($userUid);
                $CriteriaCount = $oAppCache->getUnassignedCountCriteria($userUid);
                break;
            case "paused":
                $Criteria = $oAppCache->getPausedListCriteria($userUid);
                $CriteriaCount = $oAppCache->getPausedCountCriteria($userUid);
                break;
            case "completed":
                $Criteria = $oAppCache->getCompletedListCriteria($userUid);
                $CriteriaCount = $oAppCache->getCompletedCountCriteria($userUid);
                break;
            case "cancelled":
                $Criteria = $oAppCache->getCancelledListCriteria($userUid);
                $CriteriaCount = $oAppCache->getCancelledCountCriteria($userUid);
                break;
            case "search":
                //$Criteria = $oAppCache->getSearchListCriteria();
                //$CriteriaCount = $oAppCache->getSearchCountCriteria();

                switch ($status) {
                    case "TO_DO":
                        $Criteria = $oAppCache->getToDoListCriteria($userUid);
                        $CriteriaCount = $oAppCache->getToDoCountCriteria($userUid);
                        break;
                    case "DRAFT":
                        $Criteria = $oAppCache->getDraftListCriteria($userUid);
                        $CriteriaCount = $oAppCache->getDraftCountCriteria($userUid);
                        break;
                    case "PAUSED":
                        $Criteria = $oAppCache->getPausedListCriteria($userUid);
                        $CriteriaCount = $oAppCache->getPausedCountCriteria($userUid);
                        break;
                    case "CANCELLED":
                        $Criteria = $oAppCache->getCancelledListCriteria($userUid);
                        $CriteriaCount = $oAppCache->getCancelledCountCriteria($userUid);
                        break;
                    case "COMPLETED":
                        $Criteria = $oAppCache->getCompletedListCriteria($userUid);
                        $CriteriaCount = $oAppCache->getCompletedCountCriteria($userUid);

                        $Criteria->add (AppCacheViewPeer::DEL_LAST_INDEX,"1");
                        $CriteriaCount->add (AppCacheViewPeer::DEL_LAST_INDEX,"1");
                        break;
                    default:
                        //All status
                        $Criteria = $oAppCache->getAllCasesListCriteria2($userUid);
                        $CriteriaCount = $oAppCache->getAllCasesCountCriteria2($userUid);
                        break;
                }
                break;
            case "simple_search":
                $Criteria = $oAppCache->getSimpleSearchListCriteria();
                $CriteriaCount = $oAppCache->getSimpleSearchCountCriteria();
                break;
            case "to_revise":
                $Criteria = $oAppCache->getToReviseListCriteria($userUid);
                $CriteriaCount = $oAppCache->getToReviseCountCriteria($userUid);
                break;
            case "to_reassign":
                GLOBAL $RBAC;
                if($RBAC->userCanAccess('PM_REASSIGNCASE') == 1){
                    $Criteria = $oAppCache->getToReassignListCriteria($userUid);
                    $CriteriaCount = $oAppCache->getToReassignCountCriteria($userUid);
                } else {
                    $Criteria = $oAppCache->getToReassignSupervisorListCriteria($userUid);
                    $CriteriaCount = $oAppCache->getToReassignSupervisorCountCriteria($userUid);
                }
                break;
            case "all":
                $Criteria = $oAppCache->getAllCasesListCriteria($userUid);
                $CriteriaCount = $oAppCache->getAllCasesCountCriteria($userUid);
                break;
            case "gral":
                //General criteria probably will be deprecated
                $Criteria = $oAppCache->getGeneralListCriteria();
                $CriteriaCount = $oAppCache->getGeneralCountCriteria();
                break;
            case "todo":
                $Criteria = $oAppCache->getToDoListCriteria($userUid);
                $CriteriaCount = $oAppCache->getToDoCountCriteria($userUid);
                break;
            default:
                //All status
                $Criteria = $oAppCache->getAllCasesListCriteria2($userUid);
                $CriteriaCount = $oAppCache->getAllCasesCountCriteria2($userUid);
                break;
        }

        $arrayTaskTypeToExclude = array("WEBENTRYEVENT", "END-MESSAGE-EVENT", "START-MESSAGE-EVENT", "INTERMEDIATE-THROW-MESSAGE-EVENT", "INTERMEDIATE-CATCH-MESSAGE-EVENT");

        $Criteria->addSelectColumn(AppCacheViewPeer::TAS_UID);
        $Criteria->addSelectColumn(AppCacheViewPeer::PRO_UID);

        $Criteria->addJoin(AppCacheViewPeer::TAS_UID, TaskPeer::TAS_UID, Criteria::LEFT_JOIN);
        $Criteria->add(TaskPeer::TAS_TYPE, $arrayTaskTypeToExclude, Criteria::NOT_IN);

        $CriteriaCount->addJoin(AppCacheViewPeer::TAS_UID, TaskPeer::TAS_UID, Criteria::LEFT_JOIN);
        $CriteriaCount->add(TaskPeer::TAS_TYPE, $arrayTaskTypeToExclude, Criteria::NOT_IN);

        $Criteria->addAlias( 'CU', 'USERS' );
        $Criteria->addJoin( AppCacheViewPeer::USR_UID, 'CU.USR_UID', Criteria::LEFT_JOIN );
        $Criteria->addAsColumn( 'USR_UID', 'CU.USR_UID' );
        $Criteria->addAsColumn( 'USR_FIRSTNAME', 'CU.USR_FIRSTNAME' );
        $Criteria->addAsColumn( 'USR_LASTNAME', 'CU.USR_LASTNAME' );
        $Criteria->addAsColumn( 'USR_USERNAME', 'CU.USR_USERNAME' );

        $CriteriaCount->addAlias( 'CU', 'USERS' );
        $CriteriaCount->addJoin( AppCacheViewPeer::USR_UID, 'CU.USR_UID', Criteria::LEFT_JOIN );
        $CriteriaCount->addAsColumn( 'USR_UID', 'CU.USR_UID' );
        $CriteriaCount->addAsColumn( 'USR_FIRSTNAME', 'CU.USR_FIRSTNAME' );
        $CriteriaCount->addAsColumn( 'USR_LASTNAME', 'CU.USR_LASTNAME' );
        $CriteriaCount->addAsColumn( 'USR_USERNAME', 'CU.USR_USERNAME' );

        //Current delegation
        $appdelcrTableName = AppCacheViewPeer::TABLE_NAME;
        $appdelcrAppTasTitle = "APPDELCR.APP_TAS_TITLE";
        $appdelcrAppTasTitleCount = $appdelcrAppTasTitle;

        switch ($action) {
            case "sent":
                $appdelcrTableName = AppDelegationPeer::TABLE_NAME;
                $appdelcrAppTasTitle = "(SELECT CON_VALUE FROM CONTENT WHERE CON_ID = APPDELCR.TAS_UID AND CON_LANG = " . $delimiter . SYS_LANG . $delimiter . " AND CON_CATEGORY = " . $delimiter . "TAS_TITLE" . $delimiter . ")";
                $appdelcrAppTasTitleCount = "APPDELCR.TAS_UID";
                break;
            case "to_reassign":
                $appdelcrAppTasTitle = "APP_CACHE_VIEW.APP_TAS_TITLE";
                $appdelcrAppTasTitleCount = $appdelcrAppTasTitle;
                break;
        }

        $Criteria->addAsColumn("APPDELCR_APP_TAS_TITLE", $appdelcrAppTasTitle);
        $CriteriaCount->addAsColumn("APPDELCR_APP_TAS_TITLE", $appdelcrAppTasTitleCount);

        $Criteria->addAsColumn("USRCR_USR_UID", "USRCR.USR_UID");
        $Criteria->addAsColumn("USRCR_USR_FIRSTNAME", "USRCR.USR_FIRSTNAME");
        $Criteria->addAsColumn("USRCR_USR_LASTNAME", "USRCR.USR_LASTNAME");
        $Criteria->addAsColumn("USRCR_USR_USERNAME", "USRCR.USR_USERNAME");

        $CriteriaCount->addAsColumn("USRCR_USR_UID", "USRCR.USR_UID");
        $CriteriaCount->addAsColumn("USRCR_USR_FIRSTNAME", "USRCR.USR_FIRSTNAME");
        $CriteriaCount->addAsColumn("USRCR_USR_LASTNAME", "USRCR.USR_LASTNAME");
        $CriteriaCount->addAsColumn("USRCR_USR_USERNAME", "USRCR.USR_USERNAME");

        $Criteria->addAlias("APPDELCR", $appdelcrTableName);
        $Criteria->addAlias("USRCR", UsersPeer::TABLE_NAME);

        $CriteriaCount->addAlias("APPDELCR", $appdelcrTableName);
        $CriteriaCount->addAlias("USRCR", UsersPeer::TABLE_NAME);

        $arrayCondition = array();
        $arrayCondition[] = array(AppCacheViewPeer::APP_UID, "APPDELCR.APP_UID");
        $arrayCondition[] = array("APPDELCR.DEL_LAST_INDEX", 1);
        $Criteria->addJoinMC($arrayCondition, Criteria::LEFT_JOIN);
        $CriteriaCount->addJoinMC($arrayCondition, Criteria::LEFT_JOIN);

        $arrayCondition = array();
        $arrayCondition[] = array("APPDELCR.USR_UID", "USRCR.USR_UID");
        $Criteria->addJoinMC($arrayCondition, Criteria::LEFT_JOIN);
        $CriteriaCount->addJoinMC($arrayCondition, Criteria::LEFT_JOIN);

        //Previous user

        if (($action == "todo" || $action == "selfservice" || $action == "unassigned" || $action == "paused" || $action == "to_revise" || $action == "sent") || ($status == "TO_DO" || $status == "DRAFT" || $status == "PAUSED" || $status == "CANCELLED" || $status == "COMPLETED")) {
            $Criteria->addAlias( 'PU', 'USERS' );
            $Criteria->addJoin( AppCacheViewPeer::PREVIOUS_USR_UID, 'PU.USR_UID', Criteria::LEFT_JOIN );
            $Criteria->addAsColumn( 'PREVIOUS_USR_FIRSTNAME', 'PU.USR_FIRSTNAME' );
            $Criteria->addAsColumn( 'PREVIOUS_USR_LASTNAME', 'PU.USR_LASTNAME' );
            $Criteria->addAsColumn( 'PREVIOUS_USR_USERNAME', 'PU.USR_USERNAME' );

            $CriteriaCount->addAlias( 'PU', 'USERS' );
            $CriteriaCount->addJoin( AppCacheViewPeer::PREVIOUS_USR_UID, 'PU.USR_UID', Criteria::LEFT_JOIN );
            $CriteriaCount->addAsColumn( 'PREVIOUS_USR_FIRSTNAME', 'PU.USR_FIRSTNAME' );
            $CriteriaCount->addAsColumn( 'PREVIOUS_USR_LASTNAME', 'PU.USR_LASTNAME' );
            $CriteriaCount->addAsColumn( 'PREVIOUS_USR_USERNAME', 'PU.USR_USERNAME' );
        }

        /*
        if (! is_array( $confCasesList )) {
            $rows = $this->getDefaultFields( $action );
            $result = $this->genericJsonResponse( '', array (), $rows, 20, '' );
            //$conf->saveObject($result,'casesList',$action,'','','');
        }
        */

        //Add the process filter
        if (!empty($process)) {
            $Criteria->add(AppCacheViewPeer::PRO_UID, $process, Criteria::EQUAL);
            $CriteriaCount->add(AppCacheViewPeer::PRO_UID, $process, Criteria::EQUAL);
        }

        //Add the category filter
        if (!empty($category)) {
            require_once 'classes/model/Process.php';
            $Criteria->addAlias("CP", "PROCESS");
            $Criteria->add("CP.PRO_CATEGORY", $category, Criteria::EQUAL);
            $Criteria->addJoin(AppCacheViewPeer::PRO_UID, "CP.PRO_UID", Criteria::LEFT_JOIN);
            $Criteria->addAsColumn("CATEGORY_UID", "CP.PRO_CATEGORY");

            $CriteriaCount->addAlias("CP", "PROCESS");
            $CriteriaCount->add("CP.PRO_CATEGORY", $category, Criteria::EQUAL);
            $CriteriaCount->addJoin(AppCacheViewPeer::PRO_UID, "CP.PRO_UID", Criteria::LEFT_JOIN);
            $CriteriaCount->addAsColumn("CATEGORY_UID", "CP.PRO_CATEGORY");
        }

        // add the user filter
        /*
        if ($user != '') {
            $Criteria->add( AppCacheViewPeer::USR_UID, $user, Criteria::EQUAL );
            $CriteriaCount->add( AppCacheViewPeer::USR_UID, $user, Criteria::EQUAL );
        }
        if ($status != '') {
            $Criteria->add( AppCacheViewPeer::APP_STATUS, $status, Criteria::EQUAL );
            $CriteriaCount->add( AppCacheViewPeer::APP_STATUS, $status, Criteria::EQUAL );
        }
        */

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

                $Criteria->add( $Criteria->getNewCriterion( AppCacheViewPeer::DEL_DELEGATE_DATE, $dateFrom, Criteria::GREATER_EQUAL )->addAnd( $Criteria->getNewCriterion( AppCacheViewPeer::DEL_DELEGATE_DATE, $dateTo, Criteria::LESS_EQUAL ) ) );
                $CriteriaCount->add( $CriteriaCount->getNewCriterion( AppCacheViewPeer::DEL_DELEGATE_DATE, $dateFrom, Criteria::GREATER_EQUAL )->addAnd( $CriteriaCount->getNewCriterion( AppCacheViewPeer::DEL_DELEGATE_DATE, $dateTo, Criteria::LESS_EQUAL ) ) );
            } else {
                $dateFrom = $dateFrom . " 00:00:00";

                $Criteria->add( AppCacheViewPeer::DEL_DELEGATE_DATE, $dateFrom, Criteria::GREATER_EQUAL );
                $CriteriaCount->add( AppCacheViewPeer::DEL_DELEGATE_DATE, $dateFrom, Criteria::GREATER_EQUAL );
            }
        } elseif ($dateTo != "") {
            $dateTo = $dateTo . " 23:59:59";

            $Criteria->add( AppCacheViewPeer::DEL_DELEGATE_DATE, $dateTo, Criteria::LESS_EQUAL );
            $CriteriaCount->add( AppCacheViewPeer::DEL_DELEGATE_DATE, $dateTo, Criteria::LESS_EQUAL );
        }

        if ($newerThan != '') {
            if ($oldestThan != '') {
                $Criteria->add(
                    $Criteria->getNewCriterion(AppCacheViewPeer::DEL_DELEGATE_DATE, $newerThan, Criteria::GREATER_THAN)->addAnd(
                    $Criteria->getNewCriterion(AppCacheViewPeer::DEL_DELEGATE_DATE, $oldestThan, Criteria::LESS_THAN))
                );
                $CriteriaCount->add(
                    $CriteriaCount->getNewCriterion(AppCacheViewPeer::DEL_DELEGATE_DATE, $newerThan, Criteria::GREATER_THAN)->addAnd(
                    $CriteriaCount->getNewCriterion(AppCacheViewPeer::DEL_DELEGATE_DATE, $oldestThan, Criteria::LESS_THAN))
                );
            } else {
                $Criteria->add(AppCacheViewPeer::DEL_DELEGATE_DATE, $newerThan, Criteria::GREATER_THAN);
                $CriteriaCount->add( AppCacheViewPeer::DEL_DELEGATE_DATE, $newerThan, Criteria::GREATER_THAN);
            }
        } else {
            if ($oldestThan != '') {
                $Criteria->add(AppCacheViewPeer::DEL_DELEGATE_DATE, $oldestThan, Criteria::LESS_THAN);
                $CriteriaCount->add(AppCacheViewPeer::DEL_DELEGATE_DATE, $oldestThan, Criteria::LESS_THAN);
            }
        }

        //add the filter
        if ($filter != '') {
            switch ($filter) {
                case 'read':
                    $Criteria->add( AppCacheViewPeer::DEL_INIT_DATE, null, Criteria::ISNOTNULL );
                    $CriteriaCount->add( AppCacheViewPeer::DEL_INIT_DATE, null, Criteria::ISNOTNULL );
                    break;
                case 'unread':
                    $Criteria->add( AppCacheViewPeer::DEL_INIT_DATE, null, Criteria::ISNULL );
                    $CriteriaCount->add( AppCacheViewPeer::DEL_INIT_DATE, null, Criteria::ISNULL );
                    break;
                case 'started':
                    $Criteria->add( AppCacheViewPeer::DEL_INDEX, 1, Criteria::EQUAL );
                    $CriteriaCount->add( AppCacheViewPeer::DEL_INDEX, 1, Criteria::EQUAL );
                    break;
                case 'completed':
                    $Criteria->add( AppCacheViewPeer::APP_STATUS, 'COMPLETED', Criteria::EQUAL );
                    $CriteriaCount->add( AppCacheViewPeer::APP_STATUS, 'COMPLETED', Criteria::EQUAL );
                    break;
            }
        }

        //Add the search filter
        if ($search != '') {
            $oTmpCriteria = '';

            //If there is PMTable for this case list
            if (is_array($oAppCache->confCasesList) && count($oAppCache->confCasesList) > 0 && isset($oAppCache->confCasesList["PMTable"]) && trim($oAppCache->confCasesList["PMTable"]) != "") {
                //Default configuration fields array
                $defaultFields = $oAppCache->getDefaultFields();

                //Getting the table name
                $additionalTableUid = $oAppCache->confCasesList["PMTable"];

                $additionalTable = AdditionalTablesPeer::retrieveByPK($additionalTableUid);
                $tableName = $additionalTable->getAddTabName();

                $additionalTable = new AdditionalTables();
                $tableData = $additionalTable->load($additionalTableUid, true);

                $tableField = array();

                foreach ($tableData["FIELDS"] as $arrayField) {
                    $tableField[] = $arrayField["FLD_NAME"];
                }

                $oNewCriteria = new Criteria("workflow");
                $sw = 0;

                foreach ($oAppCache->confCasesList["second"]["data"] as $fieldData) {
                    if (!in_array($fieldData["name"], $defaultFields)) {
                        if (in_array($fieldData["name"], $tableField)) {
                            $fieldName = $tableName . "." . $fieldData["name"];

                            if ($sw == 0) {
                                $oTmpCriteria = $oNewCriteria->getNewCriterion($fieldName, "%" . $search . "%", Criteria::LIKE);
                            } else {
                                $oTmpCriteria = $oNewCriteria->getNewCriterion($fieldName, "%" . $search . "%", Criteria::LIKE)->addOr($oTmpCriteria);
                            }

                            $sw = 1;
                        }
                    }
                }

                //add the default and hidden DEL_INIT_DATE
            }

            // the criteria adds new fields if there are defined PM Table Fields in the cases list
            if ($oTmpCriteria != '') {
                $Criteria->add(
                    $Criteria->getNewCriterion(AppCacheViewPeer::APP_TITLE, '%' . $search . '%', Criteria::LIKE)->addOr(
                    $Criteria->getNewCriterion(AppCacheViewPeer::APP_TAS_TITLE, '%' . $search . '%', Criteria::LIKE)->addOr(
                    $Criteria->getNewCriterion(AppCacheViewPeer::APP_UID, $search, Criteria::EQUAL)->addOr(
                    $Criteria->getNewCriterion(AppCacheViewPeer::APP_NUMBER, $search, Criteria::EQUAL)->addOr(
                    $oTmpCriteria
                )))));
            } else {
                $Criteria->add(
                    $Criteria->getNewCriterion(AppCacheViewPeer::APP_TITLE, '%' . $search . '%', Criteria::LIKE)->addOr(
                    $Criteria->getNewCriterion(AppCacheViewPeer::APP_TAS_TITLE, '%' . $search . '%', Criteria::LIKE)->addOr(
                    $Criteria->getNewCriterion(AppCacheViewPeer::APP_UID, $search, Criteria::EQUAL)->addOr(
                    $Criteria->getNewCriterion(AppCacheViewPeer::APP_NUMBER, $search, Criteria::EQUAL)
                ))));
            }

            // the count query needs to be the normal criteria query if there are defined PM Table Fields in the cases list
            if ($oTmpCriteria != '') {
                $CriteriaCount = $Criteria;
            } else {
                $CriteriaCount->add(
                    $CriteriaCount->getNewCriterion(AppCacheViewPeer::APP_TITLE, '%' . $search . '%', Criteria::LIKE)->addOr(
                    $CriteriaCount->getNewCriterion(AppCacheViewPeer::APP_TAS_TITLE, '%' . $search . '%', Criteria::LIKE)->addOr(
                    $CriteriaCount->getNewCriterion(AppCacheViewPeer::APP_UID, $search, Criteria::EQUAL)->addOr(
                    $CriteriaCount->getNewCriterion(AppCacheViewPeer::APP_NUMBER, $search, Criteria::EQUAL)
                ))));
            }
        }

        // this is the optimal way or query to render the cases search list
        // fixing the bug related to the wrong data displayed in the list

        //here we count how many records exists for this criteria.
        //BUT there are some special cases, and if we dont optimize them the server will crash.
        $doCountAlreadyExecuted = $paged;
        //case 1. when the SEARCH action is selected and none filter, search criteria is defined,
        //we need to count using the table APPLICATION, because APP_CACHE_VIEW takes 3 seconds

        $tableNameAux = '';
        $totalCount = 0;
        if ($doCountAlreadyExecuted == true) {
            // in the case of reassign the distinct attribute shows a diferent count result comparing to the
            // original list
            //Check also $distinct in the method getListCounters(), this in AppCacheView.php
            $distinct = true;

            if ($action != "sent" && (($action == "todo" || $action == "selfservice" || $action == "unassigned" || $action == "to_reassign" || $action == "to_revise") || ($status == "TO_DO"))) {
                $distinct = false;
            }

            // first check if there is a PMTable defined within the list,
            // the issue that brokes the normal criteria query seems to be fixed
            if (isset( $oAppCache->confCasesList['PMTable'] ) && ! empty( $oAppCache->confCasesList['PMTable'] )) {
                // then
                $oAdditionalTables = AdditionalTablesPeer::retrieveByPK( $oAppCache->confCasesList['PMTable'] );
                $tableName = $oAdditionalTables->getAddTabName();
                $tableNameAux = $tableName;
                $tableName = strtolower( $tableName );
                $tableNameArray = explode( '_', $tableName );
                foreach ($tableNameArray as $item) {
                    $newTableName[] = ucfirst( $item );
                }
                $tableName = implode( '', $newTableName );
                // so the pm table class can be invoqued from the pm table model clases
                if (! class_exists( $tableName )) {
                    require_once (PATH_DB . SYS_SYS . PATH_SEP . "classes" . PATH_SEP . $tableName . ".php");
                }
            }
            $totalCount = AppCacheViewPeer::doCount($CriteriaCount, $distinct);
        }

        //Add sortable options
        $sortBk = $sort;

        if ($sortBk != "") {
            $sort = "";

            //Current delegation (*)
            if ($action == 'sent' || $action == 'simple_search' || $action == 'to_reassign') {
                switch ($sortBk) {
                    case "APP_CACHE_VIEW.APP_CURRENT_USER":
                        $sort = "USRCR_" . $conf->userNameFormatGetFirstFieldByUsersTable();
                        break;
                    case "APP_CACHE_VIEW.APP_TAS_TITLE":
                        $sort = "APPDELCR_APP_TAS_TITLE";
                        break;
                }
            }

            if (isset( $oAppCache->confCasesList['PMTable'] ) && ! empty( $oAppCache->confCasesList['PMTable'] ) && $tableNameAux != '') {
                $sortTable = explode(".", $sortBk);

                $additionalTableUid = $oAppCache->confCasesList["PMTable"];

                require_once 'classes/model/Fields.php';
                $oCriteria = new Criteria('workflow');

                $oCriteria->addSelectColumn(FieldsPeer::FLD_UID);
                $oCriteria->addSelectColumn(FieldsPeer::FLD_INDEX);
                $oCriteria->addSelectColumn(FieldsPeer::FLD_NAME);
                $oCriteria->addSelectColumn(FieldsPeer::FLD_DESCRIPTION);
                $oCriteria->addSelectColumn(FieldsPeer::FLD_TYPE);
                $oCriteria->addSelectColumn(FieldsPeer::FLD_SIZE);
                $oCriteria->addSelectColumn(FieldsPeer::FLD_NULL);
                $oCriteria->addSelectColumn(FieldsPeer::FLD_AUTO_INCREMENT);
                $oCriteria->addSelectColumn(FieldsPeer::FLD_KEY);
                $oCriteria->addSelectColumn(FieldsPeer::FLD_FOREIGN_KEY);
                $oCriteria->addSelectColumn(FieldsPeer::FLD_FOREIGN_KEY_TABLE);
                $oCriteria->addSelectColumn(FieldsPeer::FLD_DYN_NAME);
                $oCriteria->addSelectColumn(FieldsPeer::FLD_DYN_UID);
                $oCriteria->addSelectColumn(FieldsPeer::FLD_FILTER);
                $oCriteria->add(FieldsPeer::ADD_TAB_UID, $additionalTableUid);
                $oCriteria->add(FieldsPeer::FLD_NAME, $sortTable[1]);
                $oCriteria->addAscendingOrderByColumn(FieldsPeer::FLD_INDEX);

                $oDataset = FieldsPeer::doSelectRS($oCriteria);

                if ($oDataset->next()) {
                    $sort = $tableNameAux . "." . $sortTable[1];
                }
            }

            $arraySelectColumn = $Criteria->getSelectColumns();

            if (!in_array($sort, $arraySelectColumn)) {
                $sort = $sortBk;

                if (!in_array($sort, $arraySelectColumn)) {
                    $sort = AppCacheViewPeer::APP_NUMBER; //DEFAULT VALUE
                }
            }

            if ($dir == "DESC") {
                $Criteria->addDescendingOrderByColumn($sort);
            } else {
                $Criteria->addAscendingOrderByColumn($sort);
            }
        }

        //limit the results according the interface
        $Criteria->setLimit( $limit );
        $Criteria->setOffset( $start );

        //execute the query
        $oDataset = AppCacheViewPeer::doSelectRS( $Criteria, Propel::getDbConnection('workflow_ro') );

        $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );

        $result = array ();
        $result['totalCount'] = $totalCount;
        $rows = array ();
        $aPriorities = array ('1' => 'VL','2' => 'L','3' => 'N','4' => 'H','5' => 'VH');
        $index = $start;

        while ($oDataset->next()) {
            $aRow = $oDataset->getRow();

            //Current delegation (*)
            if ($action == 'sent' || $action == 'simple_search' || $action == 'to_reassign') {
                //Current task
                $aRow["APP_TAS_TITLE"] = $aRow["APPDELCR_APP_TAS_TITLE"];

                //Current user
                //if ($action != "to_reassign" ) {
                if (($action != "to_reassign") && ($action != "search") && ($action != "to revise")) {
                    $aRow["USR_UID"] = $aRow["USRCR_USR_UID"];
                    $aRow["USR_FIRSTNAME"] = $aRow["USRCR_USR_FIRSTNAME"];
                    $aRow["USR_LASTNAME"] = $aRow["USRCR_USR_LASTNAME"];
                    $aRow["USR_USERNAME"] = $aRow["USRCR_USR_USERNAME"];
                }
            }

            //Unassigned user
            if (! isset( $aRow['APP_CURRENT_USER'] )) {
                $aRow['APP_CURRENT_USER'] = "[" . strtoupper(G::LoadTranslation("ID_UNASSIGNED")) . "]";
            }

            // replacing the status data with their respective translation
            if (isset( $aRow['APP_STATUS'] )) {
                $aRow['APP_STATUS_LABEL'] = G::LoadTranslation( "ID_{$aRow['APP_STATUS']}" );
            }

            // replacing the priority data with their respective translation
            if (isset( $aRow['DEL_PRIORITY'] )) {
                $aRow['DEL_PRIORITY'] = G::LoadTranslation( "ID_PRIORITY_{$aPriorities[$aRow['DEL_PRIORITY']]}" );
            }

            $rows[] = $aRow;
        }

        $result['data'] = $rows;

        return $result;
    }

    //TODO: Encapsulates these and another default generation functions inside a class
    /**
     * generate all the default fields
     *
     * @return Array $fields
     */
    public function setDefaultFields ()
    {
        $fields = array ();
        $fields['APP_NUMBER'] = array ('name' => 'APP_NUMBER','fieldType' => 'case field','label' => G::loadTranslation( 'ID_CASESLIST_APP_NUMBER' ),'width' => 40,'align' => 'left'
        );
        $fields['APP_UID'] = array ('name' => 'APP_UID','fieldType' => 'key','label' => G::loadTranslation( 'ID_CASESLIST_APP_UID' ),'width' => 80,'align' => 'left'
        );
        $fields['DEL_INDEX'] = array ('name' => 'DEL_INDEX','fieldType' => 'key','label' => G::loadTranslation( 'ID_CASESLIST_DEL_INDEX' ),'width' => 50,'align' => 'left'
        );
        $fields['TAS_UID'] = array ('name' => 'TAS_UID','fieldType' => 'case field','label' => G::loadTranslation( 'ID_CASESLIST_TAS_UID' ),'width' => 80,'align' => 'left'
        );
        $fields['USR_UID'] = array ('name' => 'USR_UID','fieldType' => 'case field','label' => G::loadTranslation( 'ID_CASESLIST_USR_UID' ),'width' => 80,'align' => 'left','hidden' => true
        );
        $fields['PREVIOUS_USR_UID'] = array ('name' => 'PREVIOUS_USR_UID','fieldType' => 'case field','label' => G::loadTranslation( 'ID_CASESLIST_PREVIOUS_USR_UID' ),'width' => 80,'align' => 'left','hidden' => true
        );
        $fields['APP_TITLE'] = array ('name' => 'APP_TITLE','fieldType' => 'case field','label' => G::loadTranslation( 'ID_CASESLIST_APP_TITLE' ),'width' => 140,'align' => 'left'
        );
        $fields['APP_PRO_TITLE'] = array ('name' => 'APP_PRO_TITLE','fieldType' => 'case field','label' => G::loadTranslation( 'ID_CASESLIST_APP_PRO_TITLE' ),'width' => 140,'align' => 'left'
        );
        $fields['APP_TAS_TITLE'] = array ('name' => 'APP_TAS_TITLE','fieldType' => 'case field','label' => G::loadTranslation( 'ID_CASESLIST_APP_TAS_TITLE' ),'width' => 140,'align' => 'left'
        );
        $fields['APP_DEL_PREVIOUS_USER'] = array ('name' => 'APP_DEL_PREVIOUS_USER','fieldType' => 'case field','label' => G::loadTranslation( 'ID_CASESLIST_APP_DEL_PREVIOUS_USER' ),'width' => 120,'align' => 'left'
        );
        $fields['APP_CURRENT_USER'] = array ('name' => 'APP_CURRENT_USER','fieldType' => 'case field','label' => G::loadTranslation( 'ID_CASESLIST_APP_CURRENT_USER' ),'width' => 120,'align' => 'left'
        );
        $fields['USR_FIRSTNAME'] = array ('name' => 'USR_FIRSTNAME','fieldType' => 'case field','label' => G::loadTranslation( 'ID_CASESLIST_APP_CURRENT_USER' ),'width' => 120,'align' => 'left'
        );
        $fields['USR_LASTNAME'] = array ('name' => 'USR_LASTNAME','fieldType' => 'case field','label' => G::loadTranslation( 'ID_CASESLIST_APP_CURRENT_USER' ),'width' => 120,'align' => 'left'
        );
        $fields['DEL_TASK_DUE_DATE'] = array ('name' => 'DEL_TASK_DUE_DATE','fieldType' => 'case field','label' => G::loadTranslation( 'ID_CASESLIST_DEL_TASK_DUE_DATE' ),'width' => 100,'align' => 'left'
        );
        $fields['APP_UPDATE_DATE'] = array ('name' => 'APP_UPDATE_DATE','fieldType' => 'case field','label' => G::loadTranslation( 'ID_CASESLIST_APP_UPDATE_DATE' ),'width' => 100,'align' => 'left'
        );
        $fields['DEL_PRIORITY'] = array ('name' => 'DEL_PRIORITY','fieldType' => 'case field','label' => G::loadTranslation( 'ID_CASESLIST_DEL_PRIORITY' ),'width' => 80,'align' => 'left'
        );
        $fields['APP_STATUS'] = array ('name' => 'APP_STATUS','fieldType' => 'case field','label' => G::loadTranslation( 'ID_CASESLIST_APP_STATUS' ),'width' => 80,'align' => 'left'
        );
        $fields['APP_FINISH_DATE'] = array ('name' => 'APP_FINISH_DATE','fieldType' => 'case field','label' => G::loadTranslation( 'ID_CASESLIST_APP_FINISH_DATE' ),'width' => 100,'align' => 'left'
        );
        $fields['APP_DELAY_UID'] = array ('name' => 'APP_DELAY_UID','fieldType' => 'delay field','label' => G::loadTranslation( 'ID_CASESLIST_APP_DELAY_UID' ),'width' => 100,'align' => 'left'
        );
        $fields['APP_THREAD_INDEX'] = array ('name' => 'APP_THREAD_INDEX','fieldType' => 'delay field','label' => G::loadTranslation( 'ID_CASESLIST_APP_THREAD_INDEX' ),'width' => 100,'align' => 'left'
        );
        $fields['APP_DEL_INDEX'] = array ('name' => 'APP_DEL_INDEX','fieldType' => 'delay field','label' => G::loadTranslation( 'ID_CASESLIST_APP_DEL_INDEX' ),'width' => 100,'align' => 'left'
        );
        $fields['APP_TYPE'] = array ('name' => 'APP_TYPE','fieldType' => 'delay field','label' => G::loadTranslation( 'ID_CASESLIST_APP_TYPE' ),'width' => 100,'align' => 'left'
        );
        $fields['APP_DELEGATION_USER'] = array ('name' => 'APP_DELEGATION_USER','fieldType' => 'delay field','label' => G::loadTranslation( 'ID_CASESLIST_APP_DELEGATION_USER' ),'width' => 100,'align' => 'left'
        );
        $fields['APP_ENABLE_ACTION_USER'] = array ('name' => 'APP_ENABLE_ACTION_USER','fieldType' => 'delay field','label' => G::loadTranslation( 'ID_CASESLIST_APP_ENABLE_ACTION_USER' ),'width' => 100,'align' => 'left'
        );
        $fields['APP_ENABLE_ACTION_DATE'] = array ('name' => 'APP_ENABLE_ACTION_DATE','fieldType' => 'delay field','label' => G::loadTranslation( 'ID_CASESLIST_AAPP_ENABLE_ACTION_DATE' ),'width' => 100,'align' => 'left'
        );
        $fields['APP_DISABLE_ACTION_USER'] = array ('name' => 'APP_DISABLE_ACTION_USER','fieldType' => 'delay field','label' => G::loadTranslation( 'ID_CASESLIST_APP_DISABLE_ACTION_USER' ),'width' => 100,'align' => 'left'
        );
        $fields['APP_DISABLE_ACTION_DATE'] = array ('name' => 'APP_DISABLE_ACTION_DATE','fieldType' => 'delay field','label' => G::loadTranslation( 'ID_CASESLIST_APP_DISABLE_ACTION_DATE' ),'width' => 100,'align' => 'left'
        );
        $fields['APP_AUTOMATIC_DISABLED_DATE'] = array ('name' => 'APP_AUTOMATIC_DISABLED_DATE','fieldType' => 'delay field','label' => G::loadTranslation( 'ID_CASESLIST_APP_AUTOMATIC_DISABLED_DATE' ),'width' => 100,'align' => 'left'
        );
        return $fields;

    }

    /**
     * this function return the default fields for a default case list
     *
     * @param $action
     * @return an array with the default fields for an specific case list (action)
     */
    public function getDefaultFields ($action)
    {
        $rows = array ();
        switch ($action) {
            case 'todo': // #, Case, task, process, sent by, due date, Last Modify, Priority
                $fields = $this->setDefaultFields();
                $rows[] = $fields['APP_UID'];
                $rows[] = $fields['DEL_INDEX'];
                $rows[] = $fields['USR_UID'];
                $rows[] = $fields['PREVIOUS_USR_UID'];
                $rows[] = $fields['APP_NUMBER'];
                $rows[] = $fields['APP_TITLE'];
                $rows[] = $fields['APP_PRO_TITLE'];
                $rows[] = $fields['APP_TAS_TITLE'];
                $rows[] = $fields['APP_DEL_PREVIOUS_USER'];
                $rows[] = $fields['DEL_TASK_DUE_DATE'];
                $rows[] = $fields['APP_UPDATE_DATE'];
                $rows[] = $fields['DEL_PRIORITY'];
                break;
            case 'draft': //#, Case, task, process, due date, Last Modify, Priority },
                $fields = $this->setDefaultFields();
                $rows[] = $fields['APP_UID'];
                $rows[] = $fields['DEL_INDEX'];
                $rows[] = $fields['USR_UID'];
                $rows[] = $fields['PREVIOUS_USR_UID'];
                $rows[] = $fields['APP_NUMBER'];
                $rows[] = $fields['APP_TITLE'];
                $rows[] = $fields['APP_PRO_TITLE'];
                $rows[] = $fields['APP_TAS_TITLE'];
                $rows[] = $fields['DEL_TASK_DUE_DATE'];
                $rows[] = $fields['APP_UPDATE_DATE'];
                $rows[] = $fields['DEL_PRIORITY'];
                break;
            case 'sent': // #, Case, task, process, current user, sent by, Last Modify, Status
                $fields = $this->setDefaultFields();
                $rows[] = $fields['APP_UID'];
                $rows[] = $fields['DEL_INDEX'];
                $rows[] = $fields['USR_UID'];
                $rows[] = $fields['PREVIOUS_USR_UID'];
                $rows[] = $fields['APP_NUMBER'];
                $rows[] = $fields['APP_TITLE'];
                $rows[] = $fields['APP_PRO_TITLE'];
                $rows[] = $fields['APP_TAS_TITLE'];
                $rows[] = $fields['APP_DEL_PREVIOUS_USER'];
                $rows[] = $fields['APP_CURRENT_USER'];
                $rows[] = $fields['APP_UPDATE_DATE'];
                $rows[] = $fields['APP_STATUS'];
                $rows[] = $fields['USR_FIRSTNAME'];
                $rows[] = $fields['USR_LASTNAME'];
                break;
            case 'unassigned': //#, Case, task, process, completed by user, finish date
                $fields = $this->setDefaultFields();
                $rows[] = $fields['APP_UID'];
                $rows[] = $fields['DEL_INDEX'];
                $rows[] = $fields['USR_UID'];
                $rows[] = $fields['PREVIOUS_USR_UID'];
                $rows[] = $fields['APP_NUMBER'];
                $rows[] = $fields['APP_TITLE'];
                $rows[] = $fields['APP_PRO_TITLE'];
                $rows[] = $fields['APP_TAS_TITLE'];
                $rows[] = $fields['APP_DEL_PREVIOUS_USER'];
                $rows[] = $fields['APP_UPDATE_DATE'];
                break;
            case 'paused': //#, Case, task, process, sent by
                $fields = $this->setDefaultFields();
                $rows[] = $fields['APP_UID'];
                $rows[] = $fields['DEL_INDEX'];
                $rows[] = $fields['USR_UID'];
                $rows[] = $fields['PREVIOUS_USR_UID'];
                $rows[] = $fields['APP_NUMBER'];
                $rows[] = $fields['APP_TITLE'];
                $rows[] = $fields['APP_PRO_TITLE'];
                $rows[] = $fields['APP_TAS_TITLE'];
                $rows[] = $fields['APP_DEL_PREVIOUS_USER'];
                $rows[] = $fields['APP_UPDATE_DATE'];
                $rows[] = $fields['APP_THREAD_INDEX'];
                $rows[] = $fields['APP_DEL_INDEX'];
                break;
            case 'completed': //#, Case, task, process, completed by user, finish date
                $fields = $this->setDefaultFields();
                $rows[] = $fields['APP_UID'];
                $rows[] = $fields['DEL_INDEX'];
                $rows[] = $fields['USR_UID'];
                $rows[] = $fields['PREVIOUS_USR_UID'];
                $rows[] = $fields['APP_NUMBER'];
                $rows[] = $fields['APP_TITLE'];
                $rows[] = $fields['APP_PRO_TITLE'];
                $rows[] = $fields['APP_TAS_TITLE'];
                $rows[] = $fields['APP_DEL_PREVIOUS_USER'];
                $rows[] = $fields['APP_UPDATE_DATE'];
                $rows[] = $fields['USR_UID'];
                $rows[] = $fields['PREVIOUS_USR_UID'];
                break;
            case 'cancelled': //#, Case, task, process, due date, Last Modify
                $fields = $this->setDefaultFields();
                $rows[] = $fields['APP_UID'];
                $rows[] = $fields['DEL_INDEX'];
                $rows[] = $fields['USR_UID'];
                $rows[] = $fields['PREVIOUS_USR_UID'];
                $rows[] = $fields['APP_NUMBER'];
                $rows[] = $fields['APP_TITLE'];
                $rows[] = $fields['APP_PRO_TITLE'];
                $rows[] = $fields['APP_TAS_TITLE'];
                $rows[] = $fields['APP_DEL_PREVIOUS_USER'];
                $rows[] = $fields['APP_UPDATE_DATE'];
                $rows[] = $fields['USR_UID'];
                $rows[] = $fields['PREVIOUS_USR_UID'];
                break;
            case 'to_revise': //#, Case, task, process, due date, Last Modify
                $fields = $this->setDefaultFields();
                $rows[] = $fields['APP_UID'];
                $rows[] = $fields['DEL_INDEX'];
                $rows[] = $fields['USR_UID'];
                $rows[] = $fields['PREVIOUS_USR_UID'];
                $rows[] = $fields['APP_NUMBER'];
                $rows[] = $fields['APP_TITLE'];
                $rows[] = $fields['APP_PRO_TITLE'];
                $rows[] = $fields['APP_TAS_TITLE'];
                $rows[] = $fields['APP_DEL_PREVIOUS_USER'];
                $rows[] = $fields['APP_CURRENT_USER'];
                $rows[] = $fields['DEL_PRIORITY'];
                $rows[] = $fields['APP_STATUS'];
                break;
            case 'to_reassign': //#, Case, task, process, due date, Last Modify
                $fields = $this->setDefaultFields();
                $rows[] = $fields['APP_NUMBER'];
                $rows[] = $fields['TAS_UID'];
                $rows[] = $fields['DEL_INDEX'];
                $rows[] = $fields['USR_UID'];
                $rows[] = $fields['PREVIOUS_USR_UID'];
                $rows[] = $fields['APP_UID'];
                $rows[] = $fields['APP_TITLE'];
                $rows[] = $fields['APP_PRO_TITLE'];
                $rows[] = $fields['APP_TAS_TITLE'];
                $rows[] = $fields['APP_CURRENT_USER'];
                $rows[] = $fields['APP_UPDATE_DATE'];
                $rows[] = $fields['APP_STATUS'];
                break;
            case 'all': //#, Case, task, process, due date, Last Modify
                $fields = $this->setDefaultFields();
                $rows[] = $fields['APP_UID'];
                $rows[] = $fields['DEL_INDEX'];
                $rows[] = $fields['USR_UID'];
                $rows[] = $fields['PREVIOUS_USR_UID'];
                $rows[] = $fields['APP_NUMBER'];
                $rows[] = $fields['APP_TITLE'];
                $rows[] = $fields['APP_PRO_TITLE'];
                $rows[] = $fields['APP_TAS_TITLE'];
                $rows[] = $fields['APP_CURRENT_USER'];
                $rows[] = $fields['APP_DEL_PREVIOUS_USER'];
                $rows[] = $fields['APP_UPDATE_DATE'];
                $rows[] = $fields['APP_STATUS'];
                break;
            case 'gral': //#, Case, task, process, due date, Last Modify
                $fields = $this->setDefaultFields();
                $rows[] = $fields['APP_UID'];
                $rows[] = $fields['DEL_INDEX'];
                $rows[] = $fields['USR_UID'];
                $rows[] = $fields['PREVIOUS_USR_UID'];
                $rows[] = $fields['APP_NUMBER'];
                $rows[] = $fields['APP_TITLE'];
                $rows[] = $fields['APP_PRO_TITLE'];
                $rows[] = $fields['APP_TAS_TITLE'];
                $rows[] = $fields['APP_CURRENT_USER'];
                $rows[] = $fields['APP_DEL_PREVIOUS_USER'];
                $rows[] = $fields['APP_UPDATE_DATE'];
                $rows[] = $fields['APP_STATUS'];
                break;
        }
        return $rows;
    }

    /**
     * set the generic Json Response, using two array for the grid stores and a string for the pmtable name
     *
     * @param string $pmtable
     * @param array $first
     * @param array $second
     * @return $response a json string
     */
    public function genericJsonResponse ($pmtable, $first, $second, $rowsperpage, $dateFormat)
    {
        $firstGrid['totalCount'] = count( $first );
        $firstGrid['data'] = $first;
        $secondGrid['totalCount'] = count( $second );
        $secondGrid['data'] = $second;
        $result = array ();
        $result['first'] = $firstGrid;
        $result['second'] = $secondGrid;
        $result['PMTable'] = isset( $pmtable ) ? $pmtable : '';
        $result['rowsperpage'] = isset( $rowsperpage ) ? $rowsperpage : 20;
        $result['dateformat'] = isset( $dateFormat ) && $dateFormat != '' ? $dateFormat : 'M d, Y';
        return $result;
    }

    public function getSteps ($appUid, $index, $tasUid, $proUid)
    {
        $steps = Array ();
        $case = new Cases();
        $step = new Step();
        $appDocument = new AppDocument();

        $caseSteps = $step->getAllCaseSteps( $proUid, $tasUid, $appUid );

        //getting externals steps
        $oPluginRegistry = PluginRegistry::loadSingleton();
        $eSteps = $oPluginRegistry->getSteps();
        $externalSteps = array ();
        /** @var \ProcessMaker\Plugins\Interfaces\StepDetail $externalStep */
        foreach ($eSteps as $externalStep) {
            $externalSteps[$externalStep->getStepId()] = $externalStep;
        }

        //getting the case record
        if ($appUid) {
            $caseData = $case->loadCase( $appUid );
            $pmScript = new PMScript();
            $pmScript->setFields( $caseData['APP_DATA'] );
        }

        $externalStepCount = 0;

        foreach ($caseSteps as $caseStep) {
            // if it has a condition
            if (trim( $caseStep->getStepCondition() ) != '') {
                $pmScript->setScript( $caseStep->getStepCondition() );

                if (! $pmScript->evaluate()) {
                    //evaluated false, jump & continue with the others steps
                    continue;
                }
            }

            $stepUid = $caseStep->getStepUidObj();
            $stepType = $caseStep->getStepTypeObj();
            $stepPosition = $caseStep->getStepPosition();

            $stepItem = array ();
            $stepItem['id'] = $stepUid;
            $stepItem['type'] = $stepType;

            switch ($stepType) {
                case 'DYNAFORM':
                    $oDocument = DynaformPeer::retrieveByPK( $stepUid );

                    $stepItem['title'] = $oDocument->getDynTitle();
                    $stepItem['url'] = "cases/cases_Step?UID=$stepUid&TYPE=$stepType&POSITION=$stepPosition&ACTION=EDIT";
                    $stepItem['version'] = $oDocument->getDynVersion();
                    break;
                case 'OUTPUT_DOCUMENT':
                    $oDocument = OutputDocumentPeer::retrieveByPK( $caseStep->getStepUidObj() );
                    $outputDoc = $appDocument->getObject( $appUid, $index, $caseStep->getStepUidObj(), 'OUTPUT' );
                    $stepItem['title'] = $oDocument->getOutDocTitle();

                    if ($outputDoc['APP_DOC_UID']) {
                        $stepItem['url'] = "cases/cases_Step?UID=$stepUid&TYPE=$stepType&POSITION=$stepPosition&ACTION=VIEW&DOC={$outputDoc['APP_DOC_UID']}";
                    } else {
                        $stepItem['url'] = "cases/cases_Step?UID=$stepUid&TYPE=$stepType&POSITION=$stepPosition&ACTION=GENERATE";
                    }
                    break;
                case 'INPUT_DOCUMENT':
                    $oDocument = InputDocumentPeer::retrieveByPK( $stepUid );
                    $stepItem['title'] = $oDocument->getInpDocTitle();
                    $stepItem['url'] = "cases/cases_Step?UID=$stepUid&TYPE=$stepType&POSITION=$stepPosition&ACTION=ATTACH";
                    break;
                case 'EXTERNAL':
                    $stepTitle = 'unknown ' . $caseStep->getStepUidObj();
                    $oPluginRegistry = PluginRegistry::loadSingleton();
                    $externalStep = $externalSteps[$caseStep->getStepUidObj()];
                    $stepItem['id'] = $externalStep->getStepId();
                    $stepItem['title'] = $externalStep->getStepTitle();
                    $stepItem['url'] = "cases/cases_Step?UID={$externalStep->getStepId()}&TYPE=EXTERNAL&POSITION=$stepPosition&ACTION=EDIT";
                    break;
            }

            $steps[] = $stepItem;
        }

        //last, assign task
        $stepItem = array ();
        $stepItem['id'] = '-1';
        $stepItem['type'] = '';
        $stepItem['title'] = G::LoadTranslation( 'ID_ASSIGN_TASK' );
        $stepItem['url'] = "cases/cases_Step?TYPE=ASSIGN_TASK&UID=-1&POSITION=10000&ACTION=ASSIGN";

        $steps[] = $stepItem;

        return $steps;
    }
}
