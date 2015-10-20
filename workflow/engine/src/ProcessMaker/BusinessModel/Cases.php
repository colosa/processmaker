<?php
namespace ProcessMaker\BusinessModel;

use \G;
use \UsersPeer;
use \CasesPeer;

/**
 * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
 * @copyright Colosa - Bolivia
 */
class Cases
{
    private $formatFieldNameInUppercase = true;

    /**
     * Set the format of the fields name (uppercase, lowercase)
     *
     * @param bool $flag Value that set the format
     *
     * return void
     */
    public function setFormatFieldNameInUppercase($flag)
    {
        try {
            $this->formatFieldNameInUppercase = $flag;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get the name of the field according to the format
     *
     * @param string $fieldName Field name
     *
     * return string Return the field name according the format
     */
    public function getFieldNameByFormatFieldName($fieldName)
    {
        try {
            return ($this->formatFieldNameInUppercase)? strtoupper($fieldName) : strtolower($fieldName);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Verify if does not exist the Case in table APPLICATION
     *
     * @param string $applicationUid        Unique id of Case
     * @param string $delIndex              Delegation index
     * @param string $fieldNameForException Field name for the exception
     *
     * return void Throw exception if does not exist the Case in table APPLICATION
     */
    public function throwExceptionIfNotExistsCase($applicationUid, $delIndex, $fieldNameForException)
    {
        try {
            $obj = \ApplicationPeer::retrieveByPK($applicationUid);

            $flag = is_null($obj);

            if (!$flag && $delIndex > 0) {
                $obj = \AppDelegationPeer::retrieveByPK($applicationUid, $delIndex);

                $flag = is_null($obj);
            }

            if ($flag) {
                throw new \Exception(\G::LoadTranslation("ID_CASE_DOES_NOT_EXIST2", array($fieldNameForException, $applicationUid)));
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get list for Cases
     *
     * @access public
     * @param array $dataList, Data for list
     * @return array
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     */
    public function getList($dataList = array())
    {
        Validator::isArray($dataList, '$dataList');
        if (!isset($dataList["userId"])) {
            throw (new \Exception(\G::LoadTranslation("ID_USER_NOT_EXIST", array('userId',''))));
        } else {
            Validator::usrUid($dataList["userId"], "userId");
        }

        $user = new \ProcessMaker\BusinessModel\User();

        if (!$user->checkPermission($dataList["userId"], "PM_ALLCASES")) {
            throw new \Exception(\G::LoadTranslation("ID_CASE_USER_NOT_HAVE_PERMISSION", array($dataList["userId"])));
        }

        G::LoadClass("applications");
        $solrEnabled = false;
        $userUid = $dataList["userId"];
        $callback = isset( $dataList["callback"] ) ? $dataList["callback"] : "stcCallback1001";
        $dir = isset( $dataList["dir"] ) ? $dataList["dir"] : "DESC";
        $sort = isset( $dataList["sort"] ) ? $dataList["sort"] : "APP_CACHE_VIEW.APP_NUMBER";
        $start = isset( $dataList["start"] ) ? $dataList["start"] : "0";
        $limit = isset( $dataList["limit"] ) ? $dataList["limit"] : "";
        $filter = isset( $dataList["filter"] ) ? $dataList["filter"] : "";
        $process = isset( $dataList["process"] ) ? $dataList["process"] : "";
        $category = isset( $dataList["category"] ) ? $dataList["category"] : "";
        $status = isset( $dataList["status"] ) ? strtoupper( $dataList["status"] ) : "";
        $user = isset( $dataList["user"] ) ? $dataList["user"] : "";
        $search = isset( $dataList["search"] ) ? $dataList["search"] : "";
        $action = isset( $dataList["action"] ) ? $dataList["action"] : "todo";
        $paged = isset( $dataList["paged"] ) ? $dataList["paged"] : true;
        $type = "extjs";
        $dateFrom = (!empty( $dataList["dateFrom"] )) ? substr( $dataList["dateFrom"], 0, 10 ) : "";
        $dateTo = (!empty( $dataList["dateTo"] )) ? substr( $dataList["dateTo"], 0, 10 ) : "";
        $first = isset( $dataList["first"] ) ? true :false;

        $valuesCorrect = array('todo', 'draft', 'paused', 'sent', 'selfservice', 'unassigned', 'search');
        if (!in_array($action, $valuesCorrect)) {
            throw (new \Exception(\G::LoadTranslation("ID_INCORRECT_VALUE_ACTION")));
        }

        $start = (int)$start;
        $start = abs($start);
        if ($start != 0) {
            $start--;
        }
        $limit = (int)$limit;
        $limit = abs($limit);
        if ($limit == 0) {
            G::LoadClass("configuration");
            $conf = new \Configurations();
            $generalConfCasesList = $conf->getConfiguration('ENVIRONMENT_SETTINGS', '');
            if (isset($generalConfCasesList['casesListRowNumber'])) {
                $limit = (int)$generalConfCasesList['casesListRowNumber'];
            } else {
                $limit = 25;
            }
        } else {
            $limit = (int)$limit;
        }
        if ($sort != 'APP_CACHE_VIEW.APP_NUMBER') {
            $sort = G::toUpper($sort);
            $columnsAppCacheView = \AppCacheViewPeer::getFieldNames(\BasePeer::TYPE_FIELDNAME);
            if (!(in_array($sort, $columnsAppCacheView))) {
                $sort = 'APP_CACHE_VIEW.APP_NUMBER';
            }
        }
        $dir = G::toUpper($dir);
        if (!($dir == 'DESC' || $dir == 'ASC')) {
            $dir = 'DESC';
        }
        if ($process != '') {
            Validator::proUid($process, '$pro_uid');
        }
        if ($category != '') {
            Validator::catUid($category, '$cat_uid');
        }
        $status = G::toUpper($status);
        $listStatus = array('TO_DO', 'DRAFT', 'COMPLETED', 'CANCEL', 'OPEN', 'CLOSE');
        if (!(in_array($status, $listStatus))) {
            $status = '';
        }
        if ($user != '') {
            Validator::usrUid($user, '$usr_uid');
        }
        if ($dateFrom != '') {
            Validator::isDate($dateFrom, 'Y-m-d', '$date_from');
        }
        if ($dateTo != '') {
            Validator::isDate($dateTo, 'Y-m-d', '$date_to');
        }

        if ($action == 'search' || $action == 'to_reassign') {
            $userUid = ($user == "CURRENT_USER") ? $userUid : $user;
            if ($first) {
                $result = array();
                $result['totalCount'] = 0;
                $result['data'] = array();
                return $result;
            }
        }

        if ((
                $action == "todo" || $action == "draft" || $action == "paused" || $action == "sent" ||
                $action == "selfservice" || $action == "unassigned" || $action == "search"
            ) &&
            (($solrConf = \System::solrEnv()) !== false)
        ) {
            G::LoadClass("AppSolr");

            $ApplicationSolrIndex = new \AppSolr(
                $solrConf["solr_enabled"],
                $solrConf["solr_host"],
                $solrConf["solr_instance"]
            );

            if ($ApplicationSolrIndex->isSolrEnabled() && $solrConf['solr_enabled'] == true) {
                //Check if there are missing records to reindex and reindex them
                $ApplicationSolrIndex->synchronizePendingApplications();
                $solrEnabled = true;
            }
        }

        if ($solrEnabled) {
            $result = $ApplicationSolrIndex->getAppGridData(
                $userUid,
                $start,
                $limit,
                $action,
                $filter,
                $search,
                $process,
                $status,
                $type,
                $dateFrom,
                $dateTo,
                $callback,
                $dir,
                $sort,
                $category
            );
        } else {
            G::LoadClass("applications");
            $apps = new \Applications();
            $result = $apps->getAll(
                $userUid,
                $start,
                $limit,
                $action,
                $filter,
                $search,
                $process,
                $status,
                $type,
                $dateFrom,
                $dateTo,
                $callback,
                $dir,
                (strpos($sort, ".") !== false)? $sort : "APP_CACHE_VIEW." . $sort,
                $category,
                true,
                $paged
            );
        }
        if (!empty($result['data'])) {
            foreach ($result['data'] as &$value) {
                $value = array_change_key_case($value, CASE_LOWER);
            }
        }
        if ($paged == false) {
            $response = $result['data'];
        } else {
            $response['total'] = $result['totalCount'];
            $response['start'] = $start+1;
            $response['limit'] = $limit;
            $response['sort']  = G::toLower($sort);
            $response['dir']   = G::toLower($dir);
            $response['cat_uid']  = $category;
            $response['pro_uid']  = $process;
            $response['search']   = $search;
            if ($action == 'search') {
                $response['app_status'] = G::toLower($status);
                $response['usr_uid'] = $user;
                $response['date_from'] = $dateFrom;
                $response['date_to'] = $dateTo;
            }
            $response['data'] = $result['data'];
        }
        return $response;
    }

    /**
     * Get data of a Case
     *
     * @param string $applicationUid Unique id of Case
     * @param string $userUid Unique id of User
     *
     * return array Return an array with data of Case Info
     */
    public function getCaseInfo($applicationUid, $userUid)
    {
        try {
            $solrEnabled = 0;
            if (($solrEnv = \System::solrEnv()) !== false) {
                \G::LoadClass("AppSolr");
                $appSolr = new \AppSolr(
                    $solrEnv["solr_enabled"],
                    $solrEnv["solr_host"],
                    $solrEnv["solr_instance"]
                );
                if ($appSolr->isSolrEnabled() && $solrEnv["solr_enabled"] == true) {
                    //Check if there are missing records to reindex and reindex them
                    $appSolr->synchronizePendingApplications();
                    $solrEnabled = 1;
                }
            }
            if ($solrEnabled == 1) {
                try {
                    \G::LoadClass("searchIndex");
                    $arrayData = array();
                    $delegationIndexes = array();
                    $columsToInclude = array("APP_UID");
                    $solrSearchText = null;
                    //Todo
                    $solrSearchText = $solrSearchText . (($solrSearchText != null)? " OR " : null) . "(APP_STATUS:TO_DO AND APP_ASSIGNED_USERS:" . $userUid . ")";
                    $delegationIndexes[] = "APP_ASSIGNED_USER_DEL_INDEX_" . $userUid . "_txt";
                    //Draft
                    $solrSearchText = $solrSearchText . (($solrSearchText != null)? " OR " : null) . "(APP_STATUS:DRAFT AND APP_DRAFT_USER:" . $userUid . ")";
                    //Index is allways 1
                    $solrSearchText = "($solrSearchText)";
                    //Add del_index dynamic fields to list of resulting columns
                    $columsToIncludeFinal = array_merge($columsToInclude, $delegationIndexes);
                    $solrRequestData = \Entity_SolrRequestData::createForRequestPagination(
                        array(
                            "workspace"  => $solrEnv["solr_instance"],
                            "startAfter" => 0,
                            "pageSize"   => 1000,
                            "searchText" => $solrSearchText,
                            "numSortingCols" => 1,
                            "sortCols" => array("APP_NUMBER"),
                            "sortDir"  => array(strtolower("DESC")),
                            "includeCols"  => $columsToIncludeFinal,
                            "resultFormat" => "json"
                        )
                    );
                    //Use search index to return list of cases
                    $searchIndex = new \BpmnEngine_Services_SearchIndex($appSolr->isSolrEnabled(), $solrEnv["solr_host"]);
                    //Execute query
                    $solrQueryResult = $searchIndex->getDataTablePaginatedList($solrRequestData);
                    //Get the missing data from database
                    $arrayApplicationUid = array();
                    foreach ($solrQueryResult->aaData as $i => $data) {
                        $arrayApplicationUid[] = $data["APP_UID"];
                    }
                    $aaappsDBData = $appSolr->getListApplicationDelegationData($arrayApplicationUid);
                    foreach ($solrQueryResult->aaData as $i => $data) {
                        //Initialize array
                        $delIndexes = array(); //Store all the delegation indexes
                        //Complete empty values
                        $applicationUid = $data["APP_UID"]; //APP_UID
                        //Get all the indexes returned by Solr as columns
                        for ($i = count($columsToInclude); $i <= count($data) - 1; $i++) {
                            if (is_array($data[$columsToIncludeFinal[$i]])) {
                                foreach ($data[$columsToIncludeFinal[$i]] as $delIndex) {
                                    $delIndexes[] = $delIndex;
                                }
                            }
                        }
                        //Verify if the delindex is an array
                        //if is not check different types of repositories
                        //the delegation index must always be defined.
                        if (count($delIndexes) == 0) {
                            $delIndexes[] = 1; // the first default index
                        }
                        //Remove duplicated
                        $delIndexes = array_unique($delIndexes);
                        //Get records
                        foreach ($delIndexes as $delIndex) {
                            $aRow = array();
                            //Copy result values to new row from Solr server
                            $aRow["APP_UID"] = $data["APP_UID"];
                            //Get delegation data from DB
                            //Filter data from db
                            $indexes = $appSolr->aaSearchRecords($aaappsDBData, array(
                                "APP_UID" => $applicationUid,
                                "DEL_INDEX" => $delIndex
                            ));
                            foreach ($indexes as $index) {
                                $row = $aaappsDBData[$index];
                            }
                            if (!isset($row)) {
                                continue;
                            }
                            \G::LoadClass('wsBase');
                            $ws = new \wsBase();
                            $fields = $ws->getCaseInfo($applicationUid, $row["DEL_INDEX"]);
                            $array = json_decode(json_encode($fields), true);
                            if ($array ["status_code"] != 0) {
                                throw (new \Exception($array ["message"]));
                            } else {
                                $array['app_uid'] = $array['caseId'];
                                $array['app_number'] = $array['caseNumber'];
                                $array['app_name'] = $array['caseName'];
                                $array['app_status'] = $array['caseStatus'];
                                $array['app_init_usr_uid'] = $array['caseCreatorUser'];
                                $array['app_init_usr_username'] = trim($array['caseCreatorUserName']);
                                $array['pro_uid'] = $array['processId'];
                                $array['pro_name'] = $array['processName'];
                                $array['app_create_date'] = $array['createDate'];
                                $array['app_update_date'] = $array['updateDate'];
                                $array['current_task'] = $array['currentUsers'];
                                for ($i = 0; $i<=count($array['current_task'])-1; $i++) {
                                    $current_task = $array['current_task'][$i];
                                    $current_task['usr_uid'] = $current_task['userId'];
                                    $current_task['usr_name'] = trim($current_task['userName']);
                                    $current_task['tas_uid'] = $current_task['taskId'];
                                    $current_task['tas_title'] = $current_task['taskName'];
                                    $current_task['del_index'] = $current_task['delIndex'];
                                    $current_task['del_thread'] = $current_task['delThread'];
                                    $current_task['del_thread_status'] = $current_task['delThreadStatus'];
                                    unset($current_task['userId']);
                                    unset($current_task['userName']);
                                    unset($current_task['taskId']);
                                    unset($current_task['taskName']);
                                    unset($current_task['delIndex']);
                                    unset($current_task['delThread']);
                                    unset($current_task['delThreadStatus']);
                                    $aCurrent_task[] = $current_task;
                                }
                                unset($array['status_code']);
                                unset($array['message']);
                                unset($array['timestamp']);
                                unset($array['caseParalell']);
                                unset($array['caseId']);
                                unset($array['caseNumber']);
                                unset($array['caseName']);
                                unset($array['caseStatus']);
                                unset($array['caseCreatorUser']);
                                unset($array['caseCreatorUserName']);
                                unset($array['processId']);
                                unset($array['processName']);
                                unset($array['createDate']);
                                unset($array['updateDate']);
                                unset($array['currentUsers']);
                                $current_task = json_decode(json_encode($aCurrent_task), false);
                                $oResponse = json_decode(json_encode($array), false);
                                $oResponse->current_task = $current_task;
                            }
                            //Return
                            return $oResponse;
                        }
                    }
                } catch (\InvalidIndexSearchTextException $e) {
                    $arrayData = array();
                    $arrayData[] = array ("app_uid" => $e->getMessage(),
                                          "app_name" => $e->getMessage(),
                                          "del_index" => $e->getMessage(),
                                          "pro_uid" => $e->getMessage());
                    throw (new \Exception($arrayData));
                }
            } else {
                \G::LoadClass("wsBase");

                //Verify data
                $this->throwExceptionIfNotExistsCase($applicationUid, 0, $this->getFieldNameByFormatFieldName("APP_UID"));

                $criteria = new \Criteria("workflow");

                $criteria->addSelectColumn(\AppDelegationPeer::APP_UID);
                $criteria->add(\AppDelegationPeer::APP_UID, $applicationUid);
                $criteria->add(\AppDelegationPeer::USR_UID, $userUid);

                $rsCriteria = \AppDelegationPeer::doSelectRS($criteria);

                if (!$rsCriteria->next()) {
                    throw new \Exception(\G::LoadTranslation("ID_NO_PERMISSION_NO_PARTICIPATED"));
                }

                //Get data
                $ws = new \wsBase();

                $fields = $ws->getCaseInfo($applicationUid, 0);
                $array = json_decode(json_encode($fields), true);

                if ($array ["status_code"] != 0) {
                    throw (new \Exception($array ["message"]));
                } else {
                    $array['app_uid'] = $array['caseId'];
                    $array['app_number'] = $array['caseNumber'];
                    $array['app_name'] = $array['caseName'];
                    $array["app_status"] = $array["caseStatus"];
                    $array['app_init_usr_uid'] = $array['caseCreatorUser'];
                    $array['app_init_usr_username'] = trim($array['caseCreatorUserName']);
                    $array['pro_uid'] = $array['processId'];
                    $array['pro_name'] = $array['processName'];
                    $array['app_create_date'] = $array['createDate'];
                    $array['app_update_date'] = $array['updateDate'];
                    $array['current_task'] = $array['currentUsers'];

                    $aCurrent_task = array();

                    for ($i = 0; $i<=count($array['current_task'])-1; $i++) {
                        $current_task = $array['current_task'][$i];
                        $current_task['usr_uid'] = $current_task['userId'];
                        $current_task['usr_name'] = trim($current_task['userName']);
                        $current_task['tas_uid'] = $current_task['taskId'];
                        $current_task['tas_title'] = $current_task['taskName'];
                        $current_task['del_index'] = $current_task['delIndex'];
                        $current_task['del_thread'] = $current_task['delThread'];
                        $current_task['del_thread_status'] = $current_task['delThreadStatus'];
                        $current_task["del_init_date"] = $current_task["delInitDate"] . "";
                        $current_task["del_task_due_date"] = $current_task["delTaskDueDate"];
                        unset($current_task['userId']);
                        unset($current_task['userName']);
                        unset($current_task['taskId']);
                        unset($current_task['taskName']);
                        unset($current_task['delIndex']);
                        unset($current_task['delThread']);
                        unset($current_task['delThreadStatus']);
                        $aCurrent_task[] = $current_task;
                    }
                    unset($array['status_code']);
                    unset($array['message']);
                    unset($array['timestamp']);
                    unset($array['caseParalell']);
                    unset($array['caseId']);
                    unset($array['caseNumber']);
                    unset($array['caseName']);
                    unset($array['caseStatus']);
                    unset($array['caseCreatorUser']);
                    unset($array['caseCreatorUserName']);
                    unset($array['processId']);
                    unset($array['processName']);
                    unset($array['createDate']);
                    unset($array['updateDate']);
                    unset($array['currentUsers']);
                }
                $current_task = json_decode(json_encode($aCurrent_task), false);
                $oResponse = json_decode(json_encode($array), false);
                $oResponse->current_task = $current_task;
                //Return
                return $oResponse;
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get data Task Case
     *
     * @param string $applicationUid Unique id of Case
     * @param string $userUid Unique id of User
     *
     * return array Return an array with Task Case
     */
    public function getTaskCase($applicationUid, $userUid)
    {
        try {
            //Verify data
            $this->throwExceptionIfNotExistsCase($applicationUid, 0, $this->getFieldNameByFormatFieldName("APP_UID"));

            $criteria = new \Criteria("workflow");

            $criteria->addSelectColumn(\ApplicationPeer::APP_UID);

            $criteria->add(\ApplicationPeer::APP_UID, $applicationUid, \Criteria::EQUAL);
            $criteria->add(\ApplicationPeer::APP_STATUS, "COMPLETED", \Criteria::EQUAL);

            $rsCriteria = \ApplicationPeer::doSelectRS($criteria);

            if ($rsCriteria->next()) {
                throw new \Exception(\G::LoadTranslation("ID_CASE_NO_CURRENT_TASKS_BECAUSE_CASE_ITS_COMPLETED", array($this->getFieldNameByFormatFieldName("APP_UID"), $applicationUid)));
            }

            //Get data
            $result = array();

            $oCriteria = new \Criteria( 'workflow' );
            $del       = \DBAdapter::getStringDelimiter();
            $oCriteria->addSelectColumn( \AppDelegationPeer::DEL_INDEX );
            $oCriteria->addSelectColumn( \AppDelegationPeer::TAS_UID );
            $oCriteria->addSelectColumn(\AppDelegationPeer::DEL_INIT_DATE);
            $oCriteria->addSelectColumn(\AppDelegationPeer::DEL_TASK_DUE_DATE);
            $oCriteria->addAsColumn( 'TAS_TITLE', 'C1.CON_VALUE' );
            $oCriteria->addAlias( "C1", 'CONTENT' );
            $tasTitleConds   = array ();
            $tasTitleConds[] = array (\AppDelegationPeer::TAS_UID,'C1.CON_ID');
            $tasTitleConds[] = array ('C1.CON_CATEGORY',$del . 'TAS_TITLE' . $del);
            $tasTitleConds[] = array ('C1.CON_LANG',$del . SYS_LANG . $del);
            $oCriteria->addJoinMC( $tasTitleConds, \Criteria::LEFT_JOIN );
            $oCriteria->add( \AppDelegationPeer::APP_UID, $applicationUid );
            $oCriteria->add( \AppDelegationPeer::USR_UID, $userUid );
            $oCriteria->add( \AppDelegationPeer::DEL_THREAD_STATUS, 'OPEN' );
            $oCriteria->add( \AppDelegationPeer::DEL_FINISH_DATE, null, \Criteria::ISNULL );
            $oDataset = \AppDelegationPeer::doSelectRS( $oCriteria );
            $oDataset->setFetchmode( \ResultSet::FETCHMODE_ASSOC );
            $oDataset->next();
            while ($aRow = $oDataset->getRow()) {
                $result = array ('tas_uid'   => $aRow['TAS_UID'],
                                 'tas_title'  => $aRow['TAS_TITLE'],
                                 'del_index' => $aRow['DEL_INDEX'],
                                 "del_init_date"     => $aRow["DEL_INIT_DATE"] . "",
                                 "del_task_due_date" => $aRow["DEL_TASK_DUE_DATE"]);
                $oDataset->next();
            }
            //Return
            if (empty($result)) {
                throw new \Exception(\G::LoadTranslation("ID_CASES_INCORRECT_INFORMATION", array($applicationUid)));
            } else {
                return $result;
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Add New Case
     *
     * @param string $processUid Unique id of Project
     * @param string $taskUid Unique id of Activity (task)
     * @param string $userUid Unique id of Case
     * @param array $variables
     *
     * return array Return an array with Task Case
     */
    public function addCase($processUid, $taskUid, $userUid, $variables)
    {
        try {
            \G::LoadClass('wsBase');
            $ws = new \wsBase();
            if ($variables) {
                $variables = array_shift($variables);
            }
            Validator::proUid($processUid, '$pro_uid');
            $oTask = new \Task();
            if (! $oTask->taskExists($taskUid)) {
                throw new \Exception(\G::LoadTranslation("ID_INVALID_VALUE_FOR", array('tas_uid')));
            }
            $fields = $ws->newCase($processUid, $userUid, $taskUid, $variables);
            $array = json_decode(json_encode($fields), true);
            if ($array ["status_code"] != 0) {
                throw (new \Exception($array ["message"]));
            } else {
                $array['app_uid'] = $array['caseId'];
                $array['app_number'] = $array['caseNumber'];
                unset($array['status_code']);
                unset($array['message']);
                unset($array['timestamp']);
                unset($array['caseId']);
                unset($array['caseNumber']);
            }
            $oResponse = json_decode(json_encode($array), false);
            //Return
            return $oResponse;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Add New Case Impersonate
     *
     * @param string $processUid Unique id of Project
     * @param string $userUid Unique id of User
     * @param string $taskUid Unique id of Case
     * @param array $variables
     *
     * return array Return an array with Task Case
     */
    public function addCaseImpersonate($processUid, $userUid, $taskUid, $variables)
    {
        try {
            \G::LoadClass('wsBase');
            $ws = new \wsBase();
            if ($variables) {
                $variables = array_shift($variables);
            } elseif ($variables == null) {
                $variables = array(array());
            }
            Validator::proUid($processUid, '$pro_uid');
            $user = new \Users();
            if (! $user->userExists( $userUid )) {
                throw new \Exception(\G::LoadTranslation("ID_INVALID_VALUE_FOR", array('usr_uid')));
            }
            $fields = $ws->newCaseImpersonate($processUid, $userUid, $variables, $taskUid);
            $array = json_decode(json_encode($fields), true);
            if ($array ["status_code"] != 0) {
                if ($array ["status_code"] == 12) {
                    throw (new \Exception(\G::loadTranslation('ID_NO_STARTING_TASK') . '. tas_uid.'));
                } elseif ($array ["status_code"] == 13) {
                    throw (new \Exception(\G::loadTranslation('ID_MULTIPLE_STARTING_TASKS') . '. tas_uid.'));
                }
                throw (new \Exception($array ["message"]));
            } else {
                $array['app_uid'] = $array['caseId'];
                $array['app_number'] = $array['caseNumber'];
                unset($array['status_code']);
                unset($array['message']);
                unset($array['timestamp']);
                unset($array['caseId']);
                unset($array['caseNumber']);
            }
            $oResponse = json_decode(json_encode($array), false);
            //Return
            return $oResponse;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Reassign Case
     *
     * @param string $applicationUid Unique id of Case
     * @param string $userUid Unique id of User
     * @param string $delIndex
     * @param string $userUidSource Unique id of User Source
     * @param string $userUid $userUidTarget id of User Target
     *
     * return array Return an array with Task Case
     */
    public function updateReassignCase($applicationUid, $userUid, $delIndex, $userUidSource, $userUidTarget)
    {
        try {
            if (!$delIndex) {
                $delIndex = \AppDelegation::getCurrentIndex($applicationUid);
            }
            \G::LoadClass('wsBase');
            $ws = new \wsBase();
            $fields = $ws->reassignCase($userUid, $applicationUid, $delIndex, $userUidSource, $userUidTarget);
            $array = json_decode(json_encode($fields), true);
            if (array_key_exists("status_code", $array)) {
                if ($array ["status_code"] != 0) {
                    throw (new \Exception($array ["message"]));
                } else {
                    unset($array['status_code']);
                    unset($array['message']);
                    unset($array['timestamp']);
                }
            } else {
                throw new \Exception(\G::LoadTranslation("ID_CASES_INCORRECT_INFORMATION", array($applicationUid)));
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Put cancel case
     *
     * @access public
     * @param string $app_uid, Uid for case
     * @param string $usr_uid, Uid for user
     * @param string $del_index, Index for case
     * @return array
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     */
    public function putCancelCase($app_uid, $usr_uid, $del_index = false)
    {
        Validator::isString($app_uid, '$app_uid');
        Validator::isString($usr_uid, '$usr_uid');

        Validator::appUid($app_uid, '$app_uid');
        Validator::usrUid($usr_uid, '$usr_uid');

        if ($del_index === false) {
            $del_index = \AppDelegation::getCurrentIndex($app_uid);
        }
        Validator::isInteger($del_index, '$del_index');

        $case = new \Cases();
        $fields = $case->loadCase($app_uid);
        if ($fields['APP_STATUS'] == 'CANCELLED') {
            throw (new \Exception(\G::LoadTranslation("ID_CASE_ALREADY_CANCELED", array($app_uid))));
        }

        $appCacheView = new \AppCacheView();

        $arrayProcess = $appCacheView->getProUidSupervisor($usr_uid);

        $criteria = new \Criteria("workflow");

        $criteria->addSelectColumn(\AppDelegationPeer::APP_UID);
        $criteria->add(\AppDelegationPeer::APP_UID, $app_uid, \Criteria::EQUAL);
        $criteria->add(\AppDelegationPeer::DEL_INDEX, $del_index, \Criteria::EQUAL);
        $criteria->add(
            $criteria->getNewCriterion(\AppDelegationPeer::USR_UID, $usr_uid, \Criteria::EQUAL)->addOr(
            $criteria->getNewCriterion(\AppDelegationPeer::PRO_UID, $arrayProcess, \Criteria::IN))
        );
        $rsCriteria = \AppDelegationPeer::doSelectRS($criteria);

        if (!$rsCriteria->next()) {
            throw (new \Exception(\G::LoadTranslation("ID_CASE_USER_INVALID_CANCEL_CASE", array($usr_uid))));
        }

        $case->cancelCase( $app_uid, $del_index, $usr_uid );
    }

    /**
     * Put pause case
     *
     * @access public
     * @param string $app_uid , Uid for case
     * @param string $usr_uid , Uid for user
     * @param bool|string $del_index , Index for case
     * @param null|string $unpaused_date, Date for unpaused
     * @return array
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     */
    public function putPauseCase($app_uid, $usr_uid, $del_index = false, $unpaused_date = null)
    {
        Validator::isString($app_uid, '$app_uid');
        Validator::isString($usr_uid, '$usr_uid');

        Validator::appUid($app_uid, '$app_uid');
        Validator::usrUid($usr_uid, '$usr_uid');

        if ($del_index === false) {
            $del_index = \AppDelegation::getCurrentIndex($app_uid);
        }

        Validator::isInteger($del_index, '$del_index');

        $case = new \Cases();
        $fields = $case->loadCase($app_uid);
        if ($fields['APP_STATUS'] == 'CANCELLED') {
            throw (new \Exception(\G::LoadTranslation("ID_CASE_IS_CANCELED", array($app_uid))));
        }

        $oDelay = new \AppDelay();

        if ($oDelay->isPaused($app_uid, $del_index)) {
            throw (new \Exception(\G::LoadTranslation("ID_CASE_PAUSED", array($app_uid))));
        }

        $appCacheView = new \AppCacheView();

        $arrayProcess = $appCacheView->getProUidSupervisor($usr_uid);

        $criteria = new \Criteria("workflow");

        $criteria->addSelectColumn(\AppDelegationPeer::APP_UID);
        $criteria->add(\AppDelegationPeer::APP_UID, $app_uid, \Criteria::EQUAL);
        $criteria->add(\AppDelegationPeer::DEL_INDEX, $del_index, \Criteria::EQUAL);
        $criteria->add(
            $criteria->getNewCriterion(\AppDelegationPeer::USR_UID, $usr_uid, \Criteria::EQUAL)->addOr(
            $criteria->getNewCriterion(\AppDelegationPeer::PRO_UID, $arrayProcess, \Criteria::IN))
        );
        $criteria->add(\AppDelegationPeer::DEL_THREAD_STATUS, "OPEN", \Criteria::EQUAL);
        $criteria->add(\AppDelegationPeer::DEL_FINISH_DATE, null, \Criteria::ISNULL);

        $rsCriteria = \AppDelegationPeer::doSelectRS($criteria);

        if (!$rsCriteria->next()) {
            throw (new \Exception(\G::LoadTranslation("ID_CASE_USER_INVALID_PAUSED_CASE", array($usr_uid))));
        }

        if ($unpaused_date != null) {
            Validator::isDate($unpaused_date, 'Y-m-d', '$unpaused_date');
        }

        $case->pauseCase( $app_uid, $del_index, $usr_uid, $unpaused_date );
    }

    /**
     * Put unpause case
     *
     * @access public
     * @param string $app_uid , Uid for case
     * @param string $usr_uid , Uid for user
     * @param bool|string $del_index , Index for case
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     */
    public function putUnpauseCase($app_uid, $usr_uid, $del_index = false)
    {
        Validator::isString($app_uid, '$app_uid');
        Validator::isString($usr_uid, '$usr_uid');

        Validator::appUid($app_uid, '$app_uid');
        Validator::usrUid($usr_uid, '$usr_uid');

        if ($del_index === false) {
            $del_index = \AppDelegation::getCurrentIndex($app_uid);
        }
        Validator::isInteger($del_index, '$del_index');

        $oDelay = new \AppDelay();

        if (!$oDelay->isPaused($app_uid, $del_index)) {
            throw (new \Exception(\G::LoadTranslation("ID_CASE_NOT_PAUSED", array($app_uid))));
        }

        $appCacheView = new \AppCacheView();

        $arrayProcess = $appCacheView->getProUidSupervisor($usr_uid);

        $criteria = new \Criteria("workflow");
        $criteria->addSelectColumn(\AppDelegationPeer::APP_UID);
        $criteria->add(\AppDelegationPeer::APP_UID, $app_uid, \Criteria::EQUAL);
        $criteria->add(\AppDelegationPeer::DEL_INDEX, $del_index, \Criteria::EQUAL);
        $criteria->add(
            $criteria->getNewCriterion(\AppDelegationPeer::USR_UID, $usr_uid, \Criteria::EQUAL)->addOr(
            $criteria->getNewCriterion(\AppDelegationPeer::PRO_UID, $arrayProcess, \Criteria::IN))
        );

        $rsCriteria = \AppDelegationPeer::doSelectRS($criteria);

        if (!$rsCriteria->next()) {
            throw (new \Exception(\G::LoadTranslation("ID_CASE_USER_INVALID_UNPAUSE_CASE", array($usr_uid))));
        }

        $case = new \Cases();
        $case->unpauseCase( $app_uid, $del_index, $usr_uid );
    }

    /**
     * Put execute trigger case
     *
     * @access public
     * @param string $app_uid , Uid for case
     * @param string $usr_uid , Uid for user
     * @param bool|string $del_index , Index for case
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     */
    public function putExecuteTriggerCase($app_uid, $tri_uid, $usr_uid, $del_index = false)
    {
        Validator::isString($app_uid, '$app_uid');
        Validator::isString($tri_uid, '$tri_uid');
        Validator::isString($usr_uid, '$usr_uid');

        Validator::appUid($app_uid, '$app_uid');
        Validator::triUid($tri_uid, '$tri_uid');
        Validator::usrUid($usr_uid, '$usr_uid');

        if ($del_index === false) {
            $del_index = \AppDelegation::getCurrentIndex($app_uid);
        }
        Validator::isInteger($del_index, '$del_index');

        global $RBAC;
        if (!method_exists($RBAC, 'initRBAC')) {
            $RBAC = \RBAC::getSingleton( PATH_DATA, session_id() );
            $RBAC->sSystem = 'PROCESSMAKER';
        }

        $case = new \wsBase();
        $result = $case->executeTrigger($usr_uid, $app_uid, $tri_uid, $del_index);

        if ($result->status_code != 0) {
            throw new \Exception($result->message);
        }
    }

    /**
     * Delete case
     *
     * @access public
     * @param string $app_uid, Uid for case
     * @param string $usr_uid, Uid user
     * @return array
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     */
    public function deleteCase($app_uid, $usr_uid)
    {
        Validator::isString($app_uid, '$app_uid');
        Validator::appUid($app_uid, '$app_uid');

        $criteria = new \Criteria();
        $criteria->addSelectColumn( \ApplicationPeer::APP_STATUS );
        $criteria->addSelectColumn( \ApplicationPeer::APP_INIT_USER );
        $criteria->add( \ApplicationPeer::APP_UID, $app_uid, \Criteria::EQUAL );
        $dataset = \ApplicationPeer::doSelectRS($criteria);
        $dataset->setFetchmode(\ResultSet::FETCHMODE_ASSOC);
        $dataset->next();
        $aRow = $dataset->getRow();
        if ($aRow['APP_STATUS'] != 'DRAFT') {
            throw (new \Exception(\G::LoadTranslation("ID_DELETE_CASE_NO_STATUS")));
        }

        if ($aRow['APP_INIT_USER'] != $usr_uid) {
            throw (new \Exception(\G::LoadTranslation("ID_DELETE_CASE_NO_OWNER")));
        }

        $case = new \Cases();
        $case->removeCase( $app_uid );
    }

    /**
     * Route Case
     *
     * @param string $applicationUid Unique id of Case
     * @param string $userUid Unique id of User
     * @param string $delIndex
     * @param string $bExecuteTriggersBeforeAssignment
     *
     * return array Return an array with Task Case
     */
    public function updateRouteCase($applicationUid, $userUid, $delIndex)
    {
        try {
            if (!$delIndex) {
                $delIndex = \AppDelegation::getCurrentIndex($applicationUid);
            }
            \G::LoadClass('wsBase');
            $ws = new \wsBase();
            $fields = $ws->derivateCase($userUid, $applicationUid, $delIndex, $bExecuteTriggersBeforeAssignment = false);
            $array = json_decode(json_encode($fields), true);
            if ($array ["status_code"] != 0) {
                throw (new \Exception($array ["message"]));
            } else {
                unset($array['status_code']);
                unset($array['message']);
                unset($array['timestamp']);
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * get all upload document that they have send it
     *
     * @param string $sProcessUID Unique id of Process
     * @param string $sApplicationUID Unique id of Case
     * @param string $sTasKUID Unique id of Activity
     * @param string $sUserUID Unique id of User
     * @return object
     */
    public function getAllUploadedDocumentsCriteria($sProcessUID, $sApplicationUID, $sTasKUID, $sUserUID)
    {
        \G::LoadClass("configuration");
        $conf = new \Configurations();
        $confEnvSetting = $conf->getFormats();
        //verifica si existe la tabla OBJECT_PERMISSION
        $cases = new \cases();
        $cases->verifyTable();
        $listing = false;
        $oPluginRegistry = & \PMPluginRegistry::getSingleton();
        if ($oPluginRegistry->existsTrigger(PM_CASE_DOCUMENT_LIST)) {
            $folderData = new \folderData(null, null, $sApplicationUID, null, $sUserUID);
            $folderData->PMType = "INPUT";
            $folderData->returnList = true;
            //$oPluginRegistry      = & PMPluginRegistry::getSingleton();
            $listing = $oPluginRegistry->executeTriggers(PM_CASE_DOCUMENT_LIST, $folderData);
        }
        $aObjectPermissions = $cases->getAllObjects($sProcessUID, $sApplicationUID, $sTasKUID, $sUserUID);
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
        $aDelete = $cases->getAllObjectsFrom($sProcessUID, $sApplicationUID, $sTasKUID, $sUserUID, 'DELETE');
        $oAppDocument = new \AppDocument();
        $oCriteria = new \Criteria('workflow');
        $oCriteria->add(\AppDocumentPeer::APP_UID, $sApplicationUID);
        $oCriteria->add(\AppDocumentPeer::APP_DOC_TYPE, array('INPUT'), \Criteria::IN);
        $oCriteria->add(\AppDocumentPeer::APP_DOC_STATUS, array('ACTIVE'), \Criteria::IN);
        //$oCriteria->add(AppDocumentPeer::APP_DOC_UID, $aObjectPermissions['INPUT_DOCUMENTS'], Criteria::IN);
        $oCriteria->add(
            $oCriteria->getNewCriterion(
                \AppDocumentPeer::APP_DOC_UID, $aObjectPermissions['INPUT_DOCUMENTS'], \Criteria::IN)->
                addOr($oCriteria->getNewCriterion(\AppDocumentPeer::USR_UID, array($sUserUID, '-1'), \Criteria::IN))
        );
        $aConditions = array();
        $aConditions[] = array(\AppDocumentPeer::APP_UID, \AppDelegationPeer::APP_UID);
        $aConditions[] = array(\AppDocumentPeer::DEL_INDEX, \AppDelegationPeer::DEL_INDEX);
        $oCriteria->addJoinMC($aConditions, \Criteria::LEFT_JOIN);
        $oCriteria->add(\AppDelegationPeer::PRO_UID, $sProcessUID);
        $oCriteria->addAscendingOrderByColumn(\AppDocumentPeer::APP_DOC_INDEX);
        $oDataset = \AppDocumentPeer::doSelectRS($oCriteria);
        $oDataset->setFetchmode(\ResultSet::FETCHMODE_ASSOC);
        $oDataset->next();
        $aInputDocuments = array();
        $aInputDocuments[] = array(
            'APP_DOC_UID' => 'char',
            'DOC_UID' => 'char',
            'APP_DOC_COMMENT' => 'char',
            'APP_DOC_FILENAME' => 'char', 'APP_DOC_INDEX' => 'integer'
        );
        $oUser = new \Users();
        while ($aRow = $oDataset->getRow()) {
            $oCriteria2 = new \Criteria('workflow');
            $oCriteria2->add(\AppDelegationPeer::APP_UID, $sApplicationUID);
            $oCriteria2->add(\AppDelegationPeer::DEL_INDEX, $aRow['DEL_INDEX']);
            $oDataset2 = \AppDelegationPeer::doSelectRS($oCriteria2);
            $oDataset2->setFetchmode(\ResultSet::FETCHMODE_ASSOC);
            $oDataset2->next();
            $aRow2 = $oDataset2->getRow();
            $oTask = new \Task();
            if ($oTask->taskExists($aRow2['TAS_UID'])) {
                $aTask = $oTask->load($aRow2['TAS_UID']);
            } else {
                $aTask = array('TAS_TITLE' => '(TASK DELETED)');
            }
            $aAux = $oAppDocument->load($aRow['APP_DOC_UID'], $aRow['DOC_VERSION']);
            $lastVersion = $oAppDocument->getLastAppDocVersion($aRow['APP_DOC_UID'], $sApplicationUID);

            try {
                $aAux1 = $oUser->load($aAux['USR_UID']);

                $sUser = $conf->usersNameFormatBySetParameters($confEnvSetting["format"], $aAux1["USR_USERNAME"], $aAux1["USR_FIRSTNAME"], $aAux1["USR_LASTNAME"]);
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
            //$aFields['POSITION'] = $_SESSION['STEP_POSITION'];
            $aFields['CONFIRM'] = \G::LoadTranslation('ID_CONFIRM_DELETE_ELEMENT');
            if (in_array($aRow['APP_DOC_UID'], $aDelete['INPUT_DOCUMENTS'])) {
                $aFields['ID_DELETE'] = \G::LoadTranslation('ID_DELETE');
            }
            $aFields['DOWNLOAD_LABEL'] = \G::LoadTranslation('ID_DOWNLOAD');
            $aFields['DOWNLOAD_LINK'] = "cases_ShowDocument?a=" . $aRow['APP_DOC_UID'] . "&v=" . $aRow['DOC_VERSION'];
            $aFields['DOC_VERSION'] = $aRow['DOC_VERSION'];
            if (is_array($listing)) {
                foreach ($listing as $folderitem) {
                    if ($folderitem->filename == $aRow['APP_DOC_UID']) {
                        $aFields['DOWNLOAD_LABEL'] = \G::LoadTranslation('ID_GET_EXTERNAL_FILE');
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
        $oAppDocument = new \AppDocument();
        $oCriteria = new \Criteria('workflow');
        $oCriteria->add(\AppDocumentPeer::APP_UID, $sApplicationUID);
        $oCriteria->add(\AppDocumentPeer::APP_DOC_TYPE, array('ATTACHED'), \Criteria::IN);
        $oCriteria->add(\AppDocumentPeer::APP_DOC_STATUS, array('ACTIVE'), \Criteria::IN);
        $oCriteria->add(
            $oCriteria->getNewCriterion(
                \AppDocumentPeer::APP_DOC_UID, $aObjectPermissions['INPUT_DOCUMENTS'], \Criteria::IN
            )->
                addOr($oCriteria->getNewCriterion(\AppDocumentPeer::USR_UID, array($sUserUID, '-1'), \Criteria::IN)));
        $aConditions = array();
        $aConditions[] = array(\AppDocumentPeer::APP_UID, \AppDelegationPeer::APP_UID);
        $aConditions[] = array(\AppDocumentPeer::DEL_INDEX, \AppDelegationPeer::DEL_INDEX);
        $oCriteria->addJoinMC($aConditions, \Criteria::LEFT_JOIN);
        $oCriteria->add(\AppDelegationPeer::PRO_UID, $sProcessUID);
        $oCriteria->addAscendingOrderByColumn(\AppDocumentPeer::APP_DOC_INDEX);
        $oDataset = \AppDocumentPeer::doSelectRS($oCriteria);
        $oDataset->setFetchmode(\ResultSet::FETCHMODE_ASSOC);
        $oDataset->next();
        while ($aRow = $oDataset->getRow()) {
            $oCriteria2 = new \Criteria('workflow');
            $oCriteria2->add(\AppDelegationPeer::APP_UID, $sApplicationUID);
            $oCriteria2->add(\AppDelegationPeer::DEL_INDEX, $aRow['DEL_INDEX']);
            $oDataset2 = \AppDelegationPeer::doSelectRS($oCriteria2);
            $oDataset2->setFetchmode(\ResultSet::FETCHMODE_ASSOC);
            $oDataset2->next();
            $aRow2 = $oDataset2->getRow();
            $oTask = new \Task();
            if ($oTask->taskExists($aRow2['TAS_UID'])) {
                $aTask = $oTask->load($aRow2['TAS_UID']);
            } else {
                $aTask = array('TAS_TITLE' => '(TASK DELETED)');
            }
            $aAux = $oAppDocument->load($aRow['APP_DOC_UID'], $aRow['DOC_VERSION']);
            $lastVersion = $oAppDocument->getLastAppDocVersion($aRow['APP_DOC_UID'], $sApplicationUID);
            try {
                $aAux1 = $oUser->load($aAux['USR_UID']);

                $sUser = $conf->usersNameFormatBySetParameters($confEnvSetting["format"], $aAux1["USR_USERNAME"], $aAux1["USR_FIRSTNAME"], $aAux1["USR_LASTNAME"]);
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
            //$aFields['POSITION'] = $_SESSION['STEP_POSITION'];
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
        $oAppDocument = new \AppDocument();
        $oCriteria = new \Criteria('workflow');
        $oCriteria->add(\AppDocumentPeer::APP_UID, $sApplicationUID);
        $oCriteria->add(\AppDocumentPeer::APP_DOC_TYPE, array('INPUT'), \Criteria::IN);
        $oCriteria->add(\AppDocumentPeer::APP_DOC_STATUS, array('ACTIVE'), \Criteria::IN);
        $oCriteria->add(\AppDocumentPeer::DEL_INDEX, 100000);
        $oCriteria->addJoin(\AppDocumentPeer::APP_UID, \ApplicationPeer::APP_UID, \Criteria::LEFT_JOIN);
        $oCriteria->add(\ApplicationPeer::PRO_UID, $sProcessUID);
        $oCriteria->addAscendingOrderByColumn(\AppDocumentPeer::APP_DOC_INDEX);
        $oDataset = \AppDocumentPeer::doSelectRS($oCriteria);
        $oDataset->setFetchmode(\ResultSet::FETCHMODE_ASSOC);
        $oDataset->next();
        $oUser = new \Users();
        while ($aRow = $oDataset->getRow()) {
            $aTask = array('TAS_TITLE' => '[ ' . G::LoadTranslation('ID_SUPERVISOR') . ' ]');
            $aAux = $oAppDocument->load($aRow['APP_DOC_UID'], $aRow['DOC_VERSION']);
            $lastVersion = $oAppDocument->getLastAppDocVersion($aRow['APP_DOC_UID'], $sApplicationUID);
            try {
                $aAux1 = $oUser->load($aAux['USR_UID']);
                $sUser = $conf->usersNameFormatBySetParameters($confEnvSetting["format"], $aAux1["USR_USERNAME"], $aAux1["USR_FIRSTNAME"], $aAux1["USR_LASTNAME"]);
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
            //$aFields['POSITION'] = $_SESSION['STEP_POSITION'];
            $aFields['CONFIRM'] = \G::LoadTranslation('ID_CONFIRM_DELETE_ELEMENT');
            if (in_array($aRow['APP_DOC_UID'], $aDelete['INPUT_DOCUMENTS'])) {
                $aFields['ID_DELETE'] = \G::LoadTranslation('ID_DELETE');
            }
            $aFields['DOWNLOAD_LABEL'] = \G::LoadTranslation('ID_DOWNLOAD');
            $aFields['DOWNLOAD_LINK'] = "cases_ShowDocument?a=" . $aRow['APP_DOC_UID'] . "&v=" . $aRow['DOC_VERSION'];
            $aFields['DOC_VERSION'] = $aRow['DOC_VERSION'];
            if (is_array($listing)) {
                foreach ($listing as $folderitem) {
                    if ($folderitem->filename == $aRow['APP_DOC_UID']) {
                        $aFields['DOWNLOAD_LABEL'] = \G::LoadTranslation('ID_GET_EXTERNAL_FILE');
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
        \G::LoadClass('ArrayPeer');
        $oCriteria = new \Criteria('dbarray');
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
        \G::LoadClass("configuration");
        $conf = new \Configurations();
        $confEnvSetting = $conf->getFormats();
        //verifica si la tabla OBJECT_PERMISSION
        $cases = new \cases();
        $cases->verifyTable();
        $listing = false;
        $oPluginRegistry = & \PMPluginRegistry::getSingleton();
        if ($oPluginRegistry->existsTrigger(PM_CASE_DOCUMENT_LIST)) {
            $folderData = new \folderData(null, null, $sApplicationUID, null, $sUserUID);
            $folderData->PMType = "OUTPUT";
            $folderData->returnList = true;
            //$oPluginRegistry = & PMPluginRegistry::getSingleton();
            $listing = $oPluginRegistry->executeTriggers(PM_CASE_DOCUMENT_LIST, $folderData);
        }
        $aObjectPermissions = $cases->getAllObjects($sProcessUID, $sApplicationUID, $sTasKUID, $sUserUID);
        if (!is_array($aObjectPermissions)) {
            $aObjectPermissions = array('DYNAFORMS' => array(-1),'INPUT_DOCUMENTS' => array(-1),'OUTPUT_DOCUMENTS' => array(-1));
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
        $aDelete = $cases->getAllObjectsFrom($sProcessUID, $sApplicationUID, $sTasKUID, $sUserUID, 'DELETE');
        $oAppDocument = new \AppDocument();
        $oCriteria = new \Criteria('workflow');
        $oCriteria->add(\AppDocumentPeer::APP_UID, $sApplicationUID);
        $oCriteria->add(\AppDocumentPeer::APP_DOC_TYPE, 'OUTPUT');
        $oCriteria->add(\AppDocumentPeer::APP_DOC_STATUS, array('ACTIVE'), \Criteria::IN);
        //$oCriteria->add(AppDocumentPeer::APP_DOC_UID, $aObjectPermissions['OUTPUT_DOCUMENTS'], Criteria::IN);
        $oCriteria->add(
            $oCriteria->getNewCriterion(
                \AppDocumentPeer::APP_DOC_UID, $aObjectPermissions['OUTPUT_DOCUMENTS'], \Criteria::IN)->addOr($oCriteria->getNewCriterion(\AppDocumentPeer::USR_UID, $sUserUID, \Criteria::EQUAL))
        );
        $aConditions = array();
        $aConditions[] = array(\AppDocumentPeer::APP_UID, \AppDelegationPeer::APP_UID);
        $aConditions[] = array(\AppDocumentPeer::DEL_INDEX, \AppDelegationPeer::DEL_INDEX);
        $oCriteria->addJoinMC($aConditions, \Criteria::LEFT_JOIN);
        $oCriteria->add(\AppDelegationPeer::PRO_UID, $sProcessUID);
        $oCriteria->addAscendingOrderByColumn(\AppDocumentPeer::APP_DOC_INDEX);
        $oDataset = \AppDocumentPeer::doSelectRS($oCriteria);
        $oDataset->setFetchmode(\ResultSet::FETCHMODE_ASSOC);
        $oDataset->next();
        $aOutputDocuments = array();
        $aOutputDocuments[] = array(
            'APP_DOC_UID' => 'char',
            'DOC_UID' => 'char',
            'APP_DOC_COMMENT' => 'char',
            'APP_DOC_FILENAME' => 'char',
            'APP_DOC_INDEX' => 'integer'
        );
        $oUser = new \Users();
        while ($aRow = $oDataset->getRow()) {
            $oCriteria2 = new \Criteria('workflow');
            $oCriteria2->add(\AppDelegationPeer::APP_UID, $sApplicationUID);
            $oCriteria2->add(\AppDelegationPeer::DEL_INDEX, $aRow['DEL_INDEX']);
            $oDataset2 = \AppDelegationPeer::doSelectRS($oCriteria2);
            $oDataset2->setFetchmode(\ResultSet::FETCHMODE_ASSOC);
            $oDataset2->next();
            $aRow2 = $oDataset2->getRow();
            $oTask = new \Task();
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
                $oOutputDocument = new \OutputDocument();
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
                                    $filePdfLabel = \G::LoadTranslation('ID_GET_EXTERNAL_FILE') . " .pdf";
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
                                    $fileDocLabel = \G::LoadTranslation('ID_GET_EXTERNAL_FILE') . " .doc";
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
                                    $filePdfLabel = \G::LoadTranslation('ID_GET_EXTERNAL_FILE') . " .pdf";
                                    $filePdf = $folderitem->downloadScript;
                                    continue;
                                }
                            }
                        }
                        break;
                }
                try {
                    $aAux1 = $oUser->load($aAux['USR_UID']);
                    $sUser = $conf->usersNameFormatBySetParameters($confEnvSetting["format"], $aAux1["USR_USERNAME"], $aAux1["USR_FIRSTNAME"], $aAux1["USR_LASTNAME"]);
                } catch (\Exception $oException) {
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
                //$aFields['POSITION'] = $_SESSION['STEP_POSITION'];
                $aFields['CONFIRM'] = \G::LoadTranslation('ID_CONFIRM_DELETE_ELEMENT');
                if (in_array($aRow['APP_DOC_UID'], $aObjectPermissions['OUTPUT_DOCUMENTS'])) {
                    if (in_array($aRow['APP_DOC_UID'], $aDelete['OUTPUT_DOCUMENTS'])) {
                        $aFields['ID_DELETE'] = \G::LoadTranslation('ID_DELETE');
                    }
                }
                $aOutputDocuments[] = $aFields;
            }
            $oDataset->next();
        }
        global $_DBArray;
        $_DBArray['outputDocuments'] = $aOutputDocuments;
        \G::LoadClass('ArrayPeer');
        $oCriteria = new \Criteria('dbarray');
        $oCriteria->setDBArrayTable('outputDocuments');
        $oCriteria->addDescendingOrderByColumn('CREATE_DATE');
        return $oCriteria;
    }

    /**
     * Get Case Variables
     *
     * @access public
     * @param string $app_uid, Uid for case
     * @param string $usr_uid, Uid for user
     * @return array
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     */
    public function getCaseVariables($app_uid, $usr_uid)
    {
        Validator::isString($app_uid, '$app_uid');
        Validator::appUid($app_uid, '$app_uid');
        Validator::isString($usr_uid, '$usr_uid');
        Validator::usrUid($usr_uid, '$usr_uid');

        $appCacheView = new \AppCacheView();
        $isProcessSupervisor = $appCacheView->getProUidSupervisor($usr_uid);
        $criteria = new \Criteria("workflow");
        $criteria->addSelectColumn(\AppDelegationPeer::APP_UID);
        $criteria->add(\AppDelegationPeer::APP_UID, $app_uid, \Criteria::EQUAL);
        $criteria->add(\AppDelegationPeer::USR_UID, $usr_uid, \Criteria::EQUAL);
        $criteria->add(
            $criteria->getNewCriterion(\AppDelegationPeer::USR_UID, $usr_uid, \Criteria::EQUAL)->addOr(
            $criteria->getNewCriterion(\AppDelegationPeer::PRO_UID, $isProcessSupervisor, \Criteria::IN))
        );
        $rsCriteria = \AppDelegationPeer::doSelectRS($criteria);

        if (!$rsCriteria->next()) {
            throw (new \Exception(\G::LoadTranslation("ID_NO_PERMISSION_NO_PARTICIPATED", array($usr_uid))));
        }

        $case = new \Cases();
        $fields = $case->loadCase($app_uid);
        return $fields['APP_DATA'];
    }

    /**
     * Put Set Case Variables
     *
     * @access public
     * @param string $app_uid, Uid for case
     * @param array $app_data, Data for case variables
     * @param string $dyn_uid, Uid for dynaform
     * @param string $usr_uid, Uid for user
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     */
    public function setCaseVariables($app_uid, $app_data, $dyn_uid = null, $usr_uid)
    {
        Validator::isString($app_uid, '$app_uid');
        Validator::appUid($app_uid, '$app_uid');
        Validator::isArray($app_data, '$app_data');
        Validator::isString($usr_uid, '$usr_uid');
        Validator::usrUid($usr_uid, '$usr_uid');

        $arrayResult = $this->getStatusInfo($app_uid);

        if ($arrayResult["APP_STATUS"] == "CANCELLED") {
            throw new \Exception(\G::LoadTranslation("ID_CASE_CANCELLED", array($app_uid)));
        }

        if ($arrayResult["APP_STATUS"] == "COMPLETED") {
            throw new \Exception(\G::LoadTranslation("ID_CASE_IS_COMPLETED", array($app_uid)));
        }

        $appCacheView = new \AppCacheView();
        $isProcessSupervisor = $appCacheView->getProUidSupervisor($usr_uid);
        $criteria = new \Criteria("workflow");
        $criteria->addSelectColumn(\AppDelegationPeer::APP_UID);
        $criteria->add(\AppDelegationPeer::APP_UID, $app_uid, \Criteria::EQUAL);
        $criteria->add(\AppDelegationPeer::USR_UID, $usr_uid, \Criteria::EQUAL);
        $criteria->add(
            $criteria->getNewCriterion(\AppDelegationPeer::USR_UID, $usr_uid, \Criteria::EQUAL)->addOr(
            $criteria->getNewCriterion(\AppDelegationPeer::PRO_UID, $isProcessSupervisor, \Criteria::IN))
        );
        $rsCriteria = \AppDelegationPeer::doSelectRS($criteria);

        if (!$rsCriteria->next()) {
            throw (new \Exception(\G::LoadTranslation("ID_NO_PERMISSION_NO_PARTICIPATED", array($usr_uid))));
        }

        $_SESSION['APPLICATION'] = $app_uid;
        $_SESSION['USER_LOGGED'] = $usr_uid;

        $case = new \Cases();
        $fields = $case->loadCase($app_uid);
        $_POST['form'] = $app_data;

        if (!is_null($dyn_uid) && $dyn_uid != '') {
            $oDynaform = \DynaformPeer::retrieveByPK($dyn_uid);

            if ($oDynaform->getDynVersion() < 2) {
                $oForm = new \Form ( $fields['PRO_UID'] . "/" . $dyn_uid, PATH_DYNAFORM );
                $oForm->validatePost();
            }
        }

        $data['APP_DATA'] = array_merge($fields['APP_DATA'], $_POST['form']);
        $case->updateCase($app_uid, $data);
    }

    /**
     * Get Case Notes
     *
     * @access public
     * @param string $app_uid, Uid for case
     * @return array
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     */
    public function getCaseNotes($app_uid, $usr_uid, $data_get)
    {
        Validator::isString($app_uid, '$app_uid');
        Validator::appUid($app_uid, '$app_uid');
        Validator::isString($usr_uid, '$usr_uid');
        Validator::usrUid($usr_uid, '$usr_uid');
        Validator::isArray($data_get, '$data_get');

        Validator::isArray($data_get, '$data_get');
        $start = isset( $data_get["start"] ) ? $data_get["start"] : "0";
        $limit = isset( $data_get["limit"] ) ? $data_get["limit"] : "";
        $sort = isset( $data_get["sort"] ) ? $data_get["sort"] : "APP_NOTES.NOTE_DATE";
        $dir = isset( $data_get["dir"] ) ? $data_get["dir"] : "DESC";
        $user = isset( $data_get["user"] ) ? $data_get["user"] : "";
        $dateFrom = (!empty( $data_get["dateFrom"] )) ? substr( $data_get["dateFrom"], 0, 10 ) : "";
        $dateTo = (!empty( $data_get["dateTo"] )) ? substr( $data_get["dateTo"], 0, 10 ) : "";
        $search = isset( $data_get["search"] ) ? $data_get["search"] : "";
        $paged = isset( $data_get["paged"] ) ? $data_get["paged"] : true;

        $case = new \Cases();
        $caseLoad = $case->loadCase($app_uid);
        $pro_uid  = $caseLoad['PRO_UID'];
        $tas_uid  = \AppDelegation::getCurrentTask($app_uid);
        $respView  = $case->getAllObjectsFrom( $pro_uid, $app_uid, $tas_uid, $usr_uid, 'VIEW' );
        $respBlock = $case->getAllObjectsFrom( $pro_uid, $app_uid, $tas_uid, $usr_uid, 'BLOCK' );
        if ($respView['CASES_NOTES'] == 0 && $respBlock['CASES_NOTES'] == 0) {
            throw (new \Exception(\G::LoadTranslation("ID_CASES_NOTES_NO_PERMISSIONS")));
        }

        if ($sort != 'APP_NOTE.NOTE_DATE') {
            $sort = G::toUpper($sort);
            $columnsAppCacheView = \AppNotesPeer::getFieldNames(\BasePeer::TYPE_FIELDNAME);
            if (!(in_array($sort, $columnsAppCacheView))) {
                $sort = 'APP_NOTES.NOTE_DATE';
            } else {
                $sort = 'APP_NOTES.'.$sort;
            }
        }
        if ((int)$start == 1 || (int)$start == 0) {
            $start = 0;
        }
        $dir = G::toUpper($dir);
        if (!($dir == 'DESC' || $dir == 'ASC')) {
            $dir = 'DESC';
        }
        if ($user != '') {
            Validator::usrUid($user, '$usr_uid');
        }
        if ($dateFrom != '') {
            Validator::isDate($dateFrom, 'Y-m-d', '$date_from');
        }
        if ($dateTo != '') {
            Validator::isDate($dateTo, 'Y-m-d', '$date_to');
        }

        $appNote = new \AppNotes();
        $note_data = $appNote->getNotesList($app_uid, $user, $start, $limit, $sort, $dir, $dateFrom, $dateTo, $search);
        $response = array();
        if ($paged === true) {
            $response['total'] = $note_data['array']['totalCount'];
            $response['start'] = $start;
            $response['limit'] = $limit;
            $response['sort'] = $sort;
            $response['dir'] = $dir;
            $response['usr_uid'] = $user;
            $response['date_to'] = $dateTo;
            $response['date_from'] = $dateFrom;
            $response['search'] = $search;
            $response['data'] = array();
            $con = 0;
            foreach ($note_data['array']['notes'] as $value) {
                $response['data'][$con]['app_uid'] = $value['APP_UID'];
                $response['data'][$con]['usr_uid'] = $value['USR_UID'];
                $response['data'][$con]['note_date'] = $value['NOTE_DATE'];
                $response['data'][$con]['note_content'] = $value['NOTE_CONTENT'];
                $con++;
            }
        } else {
            $con = 0;
            foreach ($note_data['array']['notes'] as $value) {
                $response[$con]['app_uid'] = $value['APP_UID'];
                $response[$con]['usr_uid'] = $value['USR_UID'];
                $response[$con]['note_date'] = $value['NOTE_DATE'];
                $response[$con]['note_content'] = $value['NOTE_CONTENT'];
                $con++;
            }
        }
        return $response;
    }

    /**
     * Save new case note
     *
     * @access public
     * @param string $app_uid, Uid for case
     * @param array $app_data, Data for case variables
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     */
    public function saveCaseNote($app_uid, $usr_uid, $note_content, $send_mail = false)
    {
        Validator::isString($app_uid, '$app_uid');
        Validator::appUid($app_uid, '$app_uid');

        Validator::isString($usr_uid, '$usr_uid');
        Validator::usrUid($usr_uid, '$usr_uid');

        Validator::isString($note_content, '$note_content');
        if (strlen($note_content) > 500) {
            throw (new \Exception(\G::LoadTranslation("ID_INVALID_MAX_PERMITTED", array($note_content,'500'))));
        }

        Validator::isBoolean($send_mail, '$send_mail');

        $case = new \Cases();
        $caseLoad = $case->loadCase($app_uid);
        $pro_uid  = $caseLoad['PRO_UID'];
        $tas_uid  = \AppDelegation::getCurrentTask($app_uid);
        $respView  = $case->getAllObjectsFrom( $pro_uid, $app_uid, $tas_uid, $usr_uid, 'VIEW' );
        $respBlock = $case->getAllObjectsFrom( $pro_uid, $app_uid, $tas_uid, $usr_uid, 'BLOCK' );
        if ($respView['CASES_NOTES'] == 0 && $respBlock['CASES_NOTES'] == 0) {
            throw (new \Exception(\G::LoadTranslation("ID_CASES_NOTES_NO_PERMISSIONS")));
        }

        $note_content = addslashes($note_content);
        $appNote = new \AppNotes();
        $appNote->addCaseNote($app_uid, $usr_uid, $note_content, intval($send_mail));
    }

    /**
     * Get data of a Task from a record
     *
     * @param array $record Record
     *
     * return array Return an array with data Task
     */
    public function getTaskDataFromRecord(array $record)
    {
        try {
            return array(
                $this->getFieldNameByFormatFieldName("TAS_UID")         => $record["TAS_UID"],
                $this->getFieldNameByFormatFieldName("TAS_TITLE")       => $record["TAS_TITLE"] . "",
                $this->getFieldNameByFormatFieldName("TAS_DESCRIPTION") => $record["TAS_DESCRIPTION"] . "",
                $this->getFieldNameByFormatFieldName("TAS_START")       => ($record["TAS_START"] == "TRUE")? 1 : 0,
                $this->getFieldNameByFormatFieldName("TAS_TYPE")        => $record["TAS_TYPE"],
                $this->getFieldNameByFormatFieldName("TAS_DERIVATION")  => $record["TAS_DERIVATION"],
                $this->getFieldNameByFormatFieldName("TAS_ASSIGN_TYPE") => $record["TAS_ASSIGN_TYPE"],
                $this->getFieldNameByFormatFieldName("USR_UID")         => $record["USR_UID"] . "",
                $this->getFieldNameByFormatFieldName("USR_USERNAME")    => $record["USR_USERNAME"] . "",
                $this->getFieldNameByFormatFieldName("USR_FIRSTNAME")   => $record["USR_FIRSTNAME"] . "",
                $this->getFieldNameByFormatFieldName("USR_LASTNAME")    => $record["USR_LASTNAME"] . ""
            );
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get all Tasks of Case
     * Based in: processmaker/workflow/engine/classes/class.processMap.php
     * Method:   processMap::load()
     *
     * @param string $applicationUid Unique id of Case
     *
     * return array Return an array with all Tasks of Case
     */
    public function getTasks($applicationUid)
    {
        try {
            $arrayTask = array();

            //Verify data
            $this->throwExceptionIfNotExistsCase($applicationUid, 0, $this->getFieldNameByFormatFieldName("APP_UID"));

            //Set variables
            $process = new \Process();
            $application = new \Application();
            $conf = new \Configurations();

            $arrayApplicationData = $application->Load($applicationUid);
            $processUid = $arrayApplicationData["PRO_UID"];

            $confEnvSetting = $conf->getFormats();

            $taskUid = "";

            //Get data
            //SQL
            $delimiter = \DBAdapter::getStringDelimiter();

            $criteria = new \Criteria("workflow");

            $criteria->addSelectColumn(\TaskPeer::TAS_UID);
            $criteria->addAsColumn("TAS_TITLE", "CT.CON_VALUE");
            $criteria->addAsColumn("TAS_DESCRIPTION", "CD.CON_VALUE");
            $criteria->addSelectColumn(\TaskPeer::TAS_START);
            $criteria->addSelectColumn(\TaskPeer::TAS_TYPE);
            $criteria->addSelectColumn(\TaskPeer::TAS_DERIVATION);
            $criteria->addSelectColumn(\TaskPeer::TAS_ASSIGN_TYPE);
            $criteria->addSelectColumn(\UsersPeer::USR_UID);
            $criteria->addSelectColumn(\UsersPeer::USR_USERNAME);
            $criteria->addSelectColumn(\UsersPeer::USR_FIRSTNAME);
            $criteria->addSelectColumn(\UsersPeer::USR_LASTNAME);

            $criteria->addAlias("CT", \ContentPeer::TABLE_NAME);
            $criteria->addAlias("CD", \ContentPeer::TABLE_NAME);

            $arrayCondition = array();
            $arrayCondition[] = array(\TaskPeer::TAS_UID, "CT.CON_ID", \Criteria::EQUAL);
            $arrayCondition[] = array("CT.CON_CATEGORY", $delimiter . "TAS_TITLE" . $delimiter, \Criteria::EQUAL);
            $arrayCondition[] = array("CT.CON_LANG", $delimiter . SYS_LANG . $delimiter, \Criteria::EQUAL);
            $criteria->addJoinMC($arrayCondition, \Criteria::LEFT_JOIN);

            $arrayCondition = array();
            $arrayCondition[] = array(\TaskPeer::TAS_UID, "CD.CON_ID", \Criteria::EQUAL);
            $arrayCondition[] = array("CD.CON_CATEGORY", $delimiter . "TAS_DESCRIPTION" . $delimiter, \Criteria::EQUAL);
            $arrayCondition[] = array("CD.CON_LANG", $delimiter . SYS_LANG . $delimiter, \Criteria::EQUAL);
            $criteria->addJoinMC($arrayCondition, \Criteria::LEFT_JOIN);

            $criteria->addJoin(\TaskPeer::TAS_LAST_ASSIGNED, \UsersPeer::USR_UID, \Criteria::LEFT_JOIN);

            $criteria->add(\TaskPeer::PRO_UID, $processUid, \Criteria::EQUAL);

            $rsCriteria = \TaskPeer::doSelectRS($criteria);
            $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

            while ($rsCriteria->next()) {
                $row = $rsCriteria->getRow();

                //Task
                if ($row["TAS_TYPE"] == "NORMAL") {
                    if (($row["TAS_TITLE"] . "" == "")) {
                        //There is no Label in Current SYS_LANG language so try to find in English - by default
                        $task = new \Task();
                        $task->setTasUid($row["TAS_UID"]);

                        $row["TAS_TITLE"] = $task->getTasTitle();
                    }
                } else {
                    $criteria2 = new \Criteria("workflow");

                    $criteria2->addSelectColumn(\SubProcessPeer::PRO_UID);
                    $criteria2->addAsColumn("TAS_TITLE", "CT.CON_VALUE");
                    $criteria2->addAsColumn("TAS_DESCRIPTION", "CD.CON_VALUE");

                    $criteria2->addAlias("CT", \ContentPeer::TABLE_NAME);
                    $criteria2->addAlias("CD", \ContentPeer::TABLE_NAME);

                    $arrayCondition = array();
                    $arrayCondition[] = array(\SubProcessPeer::TAS_PARENT, "CT.CON_ID", \Criteria::EQUAL);
                    $arrayCondition[] = array("CT.CON_CATEGORY", $delimiter . "TAS_TITLE" . $delimiter, \Criteria::EQUAL);
                    $arrayCondition[] = array("CT.CON_LANG", $delimiter . SYS_LANG . $delimiter, \Criteria::EQUAL);
                    $criteria2->addJoinMC($arrayCondition, \Criteria::LEFT_JOIN);

                    $arrayCondition = array();
                    $arrayCondition[] = array(\SubProcessPeer::TAS_PARENT, "CD.CON_ID", \Criteria::EQUAL);
                    $arrayCondition[] = array("CD.CON_CATEGORY", $delimiter . "TAS_DESCRIPTION" . $delimiter, \Criteria::EQUAL);
                    $arrayCondition[] = array("CD.CON_LANG", $delimiter . SYS_LANG . $delimiter, \Criteria::EQUAL);
                    $criteria2->addJoinMC($arrayCondition, \Criteria::LEFT_JOIN);

                    $criteria2->add(\SubProcessPeer::PRO_PARENT, $processUid);
                    $criteria2->add(\SubProcessPeer::TAS_PARENT, $row["TAS_UID"]);

                    $rsCriteria2 = \SubProcessPeer::doSelectRS($criteria2);
                    $rsCriteria2->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

                    $rsCriteria2->next();

                    $row2 = $rsCriteria2->getRow();

                    if ($process->exists($row2["PRO_UID"])) {
                        $row["TAS_TITLE"] = $row2["TAS_TITLE"];
                        $row["TAS_DESCRIPTION"] = $row2["TAS_DESCRIPTION"];
                    }
                }

                //Routes
                $routeType = "";
                $arrayRoute = array();

                $criteria2 = new \Criteria("workflow");

                $criteria2->addAsColumn("ROU_NUMBER", \RoutePeer::ROU_CASE);
                $criteria2->addSelectColumn(\RoutePeer::ROU_TYPE);
                $criteria2->addSelectColumn(\RoutePeer::ROU_CONDITION);
                $criteria2->addAsColumn("TAS_UID", \RoutePeer::ROU_NEXT_TASK);
                $criteria2->add(\RoutePeer::PRO_UID, $processUid, \Criteria::EQUAL);
                $criteria2->add(\RoutePeer::TAS_UID, $row["TAS_UID"], \Criteria::EQUAL);
                $criteria2->addAscendingOrderByColumn("ROU_NUMBER");

                $rsCriteria2 = \RoutePeer::doSelectRS($criteria2);
                $rsCriteria2->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

                while ($rsCriteria2->next()) {
                    $row2 = $rsCriteria2->getRow();

                    $routeType = $row2["ROU_TYPE"];

                    $arrayRoute[] = array(
                        $this->getFieldNameByFormatFieldName("ROU_NUMBER")    => (int)($row2["ROU_NUMBER"]),
                        $this->getFieldNameByFormatFieldName("ROU_CONDITION") => $row2["ROU_CONDITION"] . "",
                        $this->getFieldNameByFormatFieldName("TAS_UID")       => $row2["TAS_UID"]
                    );
                }

                //Delegations
                $arrayAppDelegation = array();

                $criteria2 = new \Criteria("workflow");

                $criteria2->addSelectColumn(\AppDelegationPeer::DEL_INDEX);
                $criteria2->addSelectColumn(\AppDelegationPeer::DEL_INIT_DATE);
                $criteria2->addSelectColumn(\AppDelegationPeer::DEL_TASK_DUE_DATE);
                $criteria2->addSelectColumn(\AppDelegationPeer::DEL_FINISH_DATE);
                $criteria2->addSelectColumn(\UsersPeer::USR_UID);
                $criteria2->addSelectColumn(\UsersPeer::USR_USERNAME);
                $criteria2->addSelectColumn(\UsersPeer::USR_FIRSTNAME);
                $criteria2->addSelectColumn(\UsersPeer::USR_LASTNAME);

                $criteria2->addJoin(\AppDelegationPeer::USR_UID, \UsersPeer::USR_UID, \Criteria::LEFT_JOIN);

                $criteria2->add(\AppDelegationPeer::APP_UID, $applicationUid, \Criteria::EQUAL);
                $criteria2->add(\AppDelegationPeer::TAS_UID, $row["TAS_UID"], \Criteria::EQUAL);
                $criteria2->addAscendingOrderByColumn(\AppDelegationPeer::DEL_INDEX);

                $rsCriteria2 = \AppDelegationPeer::doSelectRS($criteria2);
                $rsCriteria2->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

                while ($rsCriteria2->next()) {
                    $row2 = $rsCriteria2->getRow();

                    $arrayAppDelegationDate = array(
                        "DEL_INIT_DATE"     => array("date" => $row2["DEL_INIT_DATE"],     "dateFormated" => \G::LoadTranslation("ID_CASE_NOT_YET_STARTED")),
                        "DEL_TASK_DUE_DATE" => array("date" => $row2["DEL_TASK_DUE_DATE"], "dateFormated" => \G::LoadTranslation("ID_CASE_NOT_YET_STARTED")),
                        "DEL_FINISH_DATE"   => array("date" => $row2["DEL_FINISH_DATE"],   "dateFormated" => \G::LoadTranslation("ID_NOT_FINISHED"))
                    );

                    foreach ($arrayAppDelegationDate as $key => $value) {
                        $d = $value;

                        if (!empty($d["date"])) {
                            $dateTime = new \DateTime($d["date"]);
                            $arrayAppDelegationDate[$key]["dateFormated"] = $dateTime->format($confEnvSetting["dateFormat"]);
                        }
                    }

                    $appDelegationDuration = \G::LoadTranslation("ID_NOT_FINISHED");

                    if (!empty($row2["DEL_FINISH_DATE"]) && !empty($row2["DEL_INIT_DATE"])) {
                        $t = strtotime($row2["DEL_FINISH_DATE"]) - strtotime($row2["DEL_INIT_DATE"]);

                        $h = $t * (1 / 60) * (1 / 60);
                        $m = ($h - (int)($h)) * (60 / 1);
                        $s = ($m - (int)($m)) * (60 / 1);

                        $h = (int)($h);
                        $m = (int)($m);

                        $appDelegationDuration = $h . " " . (($h == 1)? \G::LoadTranslation("ID_HOUR") : \G::LoadTranslation("ID_HOURS"));
                        $appDelegationDuration = $appDelegationDuration . " " . $m . " " . (($m == 1)? \G::LoadTranslation("ID_MINUTE") : \G::LoadTranslation("ID_MINUTES"));
                        $appDelegationDuration = $appDelegationDuration . " " . $s . " " . (($s == 1)? \G::LoadTranslation("ID_SECOND") : \G::LoadTranslation("ID_SECONDS"));
                    }

                    $arrayAppDelegation[] = array(
                        $this->getFieldNameByFormatFieldName("DEL_INDEX")         => (int)($row2["DEL_INDEX"]),
                        $this->getFieldNameByFormatFieldName("DEL_INIT_DATE")     => $arrayAppDelegationDate["DEL_INIT_DATE"]["dateFormated"],
                        $this->getFieldNameByFormatFieldName("DEL_TASK_DUE_DATE") => $arrayAppDelegationDate["DEL_TASK_DUE_DATE"]["dateFormated"],
                        $this->getFieldNameByFormatFieldName("DEL_FINISH_DATE")   => $arrayAppDelegationDate["DEL_FINISH_DATE"]["dateFormated"],
                        $this->getFieldNameByFormatFieldName("DEL_DURATION")      => $appDelegationDuration,
                        $this->getFieldNameByFormatFieldName("USR_UID")           => $row2["USR_UID"],
                        $this->getFieldNameByFormatFieldName("USR_USERNAME")      => $row2["USR_USERNAME"] . "",
                        $this->getFieldNameByFormatFieldName("USR_FIRSTNAME")     => $row2["USR_FIRSTNAME"] . "",
                        $this->getFieldNameByFormatFieldName("USR_LASTNAME")      => $row2["USR_LASTNAME"] . ""
                    );
                }

                //Status
                $status = "";

                //$criteria2
                $criteria2 = new \Criteria("workflow");

                $criteria2->addAsColumn("CANT", "COUNT(" . \AppDelegationPeer::APP_UID . ")");
                $criteria2->addAsColumn("FINISH", "MIN(" . \AppDelegationPeer::DEL_FINISH_DATE . ")");
                $criteria2->add(\AppDelegationPeer::APP_UID, $applicationUid, \Criteria::EQUAL);
                $criteria2->add(\AppDelegationPeer::TAS_UID, $row["TAS_UID"], \Criteria::EQUAL);

                $rsCriteria2 = \AppDelegationPeer::doSelectRS($criteria2);
                $rsCriteria2->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

                $rsCriteria2->next();

                $row2 = $rsCriteria2->getRow();

                //$criteria3
                $criteria3 = new \Criteria("workflow");

                $criteria3->addSelectColumn(\AppDelegationPeer::DEL_FINISH_DATE);
                $criteria3->add(\AppDelegationPeer::APP_UID, $applicationUid, \Criteria::EQUAL);
                $criteria3->add(\AppDelegationPeer::TAS_UID, $row["TAS_UID"], \Criteria::EQUAL);
                $criteria3->add(\AppDelegationPeer::DEL_FINISH_DATE, null, \Criteria::ISNULL);

                $rsCriteria3 = \AppDelegationPeer::doSelectRS($criteria3);
                $rsCriteria3->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

                $rsCriteria3->next();

                $row3 = $rsCriteria3->getRow();

                if ($row3) {
                    $row2["FINISH"] = "";
                }

                //Status
                if (empty($row2["FINISH"]) && !is_null($taskUid) && $row["TAS_UID"] == $taskUid) {
                    $status = "TASK_IN_PROGRESS"; //Red
                } else {
                    if (!empty($row2["FINISH"])) {
                        $status = "TASK_COMPLETED"; //Green
                    } else {
                        if ($routeType != "SEC-JOIN") {
                            if ($row2["CANT"] != 0) {
                                $status = "TASK_IN_PROGRESS"; //Red
                            } else {
                                $status = "TASK_PENDING_NOT_EXECUTED"; //Gray
                            }
                        } else {
                            //$status = "TASK_PARALLEL"; //Yellow

                            if ($row3) {
                                $status = "TASK_IN_PROGRESS"; //Red
                            } else {
                                $status = "TASK_PENDING_NOT_EXECUTED"; //Gray
                            }
                        }
                    }
                }

                //Set data
                $arrayAux = $this->getTaskDataFromRecord($row);
                $arrayAux[$this->getFieldNameByFormatFieldName("ROUTE")][$this->getFieldNameByFormatFieldName("TYPE")] = $routeType;
                $arrayAux[$this->getFieldNameByFormatFieldName("ROUTE")][$this->getFieldNameByFormatFieldName("TO")] = $arrayRoute;
                $arrayAux[$this->getFieldNameByFormatFieldName("DELEGATIONS")] = $arrayAppDelegation;
                $arrayAux[$this->getFieldNameByFormatFieldName("STATUS")] = $status;

                $arrayTask[] = $arrayAux;
            }

            //Return
            return $arrayTask;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Put execute triggers
     *
     * @access public
     * @param string $app_uid , Uid for case
     * @param int $del_index , Index for case
     * @param string $obj_type , Index for case
     * @param string $obj_uid , Index for case
     *
     * @copyright Colosa - Bolivia
     */
    public function putExecuteTriggers($app_uid, $del_index, $obj_type, $obj_uid)
    {
        Validator::isString($app_uid, '$app_uid');
        Validator::appUid($app_uid, '$app_uid');
        Validator::isInteger($del_index, '$del_index');

        $oCase = new \Cases();
        $aField = $oCase->loadCase($app_uid, $del_index);
        $tas_uid  = $aField["TAS_UID"];

        $task = new \Tasks();
        $aField["APP_DATA"] = $oCase->executeTriggers($tas_uid, $obj_type, $obj_uid, "AFTER", $aField["APP_DATA"]);
        $aField = $oCase->updateCase($app_uid, $aField);
    }

    /**
     * Get Steps evaluate
     *
     * @access public
     * @param string $app_uid, Uid for case
     * @param int $del_index , Index for case
     * @return array
     *
     * @copyright Colosa - Bolivia
     */
    public function getSteps($app_uid, $del_index)
    {
        Validator::isString($app_uid, '$app_uid');
        Validator::appUid($app_uid, '$app_uid');
        Validator::isInteger($del_index, '$del_index');

        $oCase = new \Cases();
        $aCaseField = $oCase->loadCase($app_uid, $del_index);
        $tas_uid  = $aCaseField["TAS_UID"];
        $pro_uid  = $aCaseField["PRO_UID"];

        $oApplication = new \Applications();
        $aField = $oApplication->getSteps($app_uid, $del_index, $tas_uid, $pro_uid);

        return $aField;
    }

    /**
     * Throw Message-Events for the Case
     *
     * @param string $elementOriginUid     Unique id of Element Origin (unique id of Task)
     * @param string $elementDestUid       Unique id of Element Dest   (unique id of Task)
     * @param array  $arrayApplicationData Case data
     *
     * return void
     */
    public function throwMessageEventBetweenElementOriginAndElementDest($elementOriginUid, $elementDestUid, array $arrayApplicationData)
    {
        try {
            //Verify if the Project is BPMN
            $bpmn = new \ProcessMaker\Project\Bpmn();

            if (!$bpmn->exists($arrayApplicationData["PRO_UID"])) {
                return;
            }

            //Element origin and dest
            $elementTaskRelation = new \ProcessMaker\BusinessModel\ElementTaskRelation();

            $arrayElement = array(
                "elementOrigin" => array("uid" => $elementOriginUid, "type" => "bpmnActivity"),
                "elementDest"   => array("uid" => $elementDestUid,   "type" => "bpmnActivity")
            );

            foreach ($arrayElement as $key => $value) {
                $arrayElementTaskRelationData = $elementTaskRelation->getElementTaskRelationWhere(
                    array(
                        \ElementTaskRelationPeer::PRJ_UID      => $arrayApplicationData["PRO_UID"],
                        \ElementTaskRelationPeer::ELEMENT_TYPE => "bpmnEvent",
                        \ElementTaskRelationPeer::TAS_UID      => $arrayElement[$key]["uid"]
                    ),
                    true
                );

                if (!is_null($arrayElementTaskRelationData)) {
                    $arrayElement[$key]["uid"]  = $arrayElementTaskRelationData["ELEMENT_UID"];
                    $arrayElement[$key]["type"] = "bpmnEvent";
                }
            }

            $elementOriginUid  = $arrayElement["elementOrigin"]["uid"];
            $elementOriginType = $arrayElement["elementOrigin"]["type"];
            $elementDestUid    = $arrayElement["elementDest"]["uid"];
            $elementDestType   = $arrayElement["elementDest"]["type"];

            //Get Message-Events of throw type
            $arrayEvent = $bpmn->getMessageEventsOfThrowTypeBetweenElementOriginAndElementDest(
                $elementOriginUid,
                $elementOriginType,
                $elementDestUid,
                $elementDestType
            );

            //Throw Message-Events
            $messageApplication = new \ProcessMaker\BusinessModel\MessageApplication();

            foreach ($arrayEvent as $value) {
                //Message-Application throw
                $result = $messageApplication->create($arrayApplicationData["APP_UID"], $arrayApplicationData["PRO_UID"], $value[0], $arrayApplicationData);
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Catch Message-Events for the Cases
     *
     * @param bool $frontEnd Flag to represent progress bar
     *
     * return void
     */
    public function catchMessageEvent($frontEnd = false)
    {
        try {
            \G::LoadClass("wsBase");

            //Set variables
            $ws = new \wsBase();
            $case = new \Cases();

            $messageApplication = new \ProcessMaker\BusinessModel\MessageApplication();
            $messageApplication->setFrontEnd($frontEnd);

            //Get data
            $totalMessageEvent = 0;

            $counterStartMessageEvent = 0;
            $counterIntermediateCatchMessageEvent = 0;
            $counter = 0;

            $flagFirstTime = false;

            $messageApplication->frontEndShow("START");

            do {
                $flagNextRecords = false;

                $arrayMessageApplicationUnread = $messageApplication->getMessageApplications(array("messageApplicationStatus" => "UNREAD"), null, null, 0, 1000);

                if (!$flagFirstTime) {
                    $totalMessageEvent = $arrayMessageApplicationUnread["total"];

                    $flagFirstTime = true;
                }

                foreach ($arrayMessageApplicationUnread["data"] as $value) {
                    if ($counter + 1 > $totalMessageEvent) {
                        $flagNextRecords = false;
                        break;
                    }

                    $arrayMessageApplicationData = $value;

                    $processUid = $arrayMessageApplicationData["PRJ_UID"];
                    $taskUid = $arrayMessageApplicationData["TAS_UID"];

                    $messageApplicationUid         = $arrayMessageApplicationData["MSGAPP_UID"];
                    $messageApplicationCorrelation = $arrayMessageApplicationData["MSGAPP_CORRELATION"];

                    $messageEventDefinitionUserUid     = $arrayMessageApplicationData["MSGED_USR_UID"];
                    $messageEventDefinitionCorrelation = $arrayMessageApplicationData["MSGED_CORRELATION"];

                    $arrayVariable = $messageApplication->mergeVariables($arrayMessageApplicationData["MSGED_VARIABLES"], $arrayMessageApplicationData["MSGAPP_VARIABLES"]);

                    $flagCatched = false;

                    switch ($arrayMessageApplicationData["EVN_TYPE"]) {
                        case "START":
                            if ($messageEventDefinitionCorrelation == $messageApplicationCorrelation && $messageEventDefinitionUserUid != "") {
                                //Start and derivate new Case
                                $result = $ws->newCase($processUid, $messageEventDefinitionUserUid, $taskUid, $arrayVariable);

                                $arrayResult = json_decode(json_encode($result), true);

                                if ($arrayResult["status_code"] == 0) {
                                    $applicationUid = $arrayResult["caseId"];

                                    $result = $ws->derivateCase($messageEventDefinitionUserUid, $applicationUid, 1);

                                    $flagCatched = true;

                                    //Counter
                                    $counterStartMessageEvent++;
                                }
                            }
                            break;
                        case "INTERMEDIATE":
                            $criteria = new \Criteria("workflow");

                            $criteria->addSelectColumn(\AppDelegationPeer::APP_UID);
                            $criteria->addSelectColumn(\AppDelegationPeer::DEL_INDEX);
                            $criteria->addSelectColumn(\AppDelegationPeer::USR_UID);

                            $criteria->add(\AppDelegationPeer::PRO_UID, $processUid, \Criteria::EQUAL);
                            $criteria->add(\AppDelegationPeer::TAS_UID, $taskUid, \Criteria::EQUAL);
                            $criteria->add(\AppDelegationPeer::DEL_THREAD_STATUS, "OPEN", \Criteria::EQUAL);
                            $criteria->add(\AppDelegationPeer::DEL_FINISH_DATE, null, \Criteria::ISNULL);

                            $rsCriteria = \AppDelegationPeer::doSelectRS($criteria);
                            $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

                            while ($rsCriteria->next()) {
                                $row = $rsCriteria->getRow();

                                $applicationUid = $row["APP_UID"];
                                $delIndex = $row["DEL_INDEX"];
                                $userUid = $row["USR_UID"];

                                $arrayApplicationData = $case->loadCase($applicationUid);

                                if (\G::replaceDataField($messageEventDefinitionCorrelation, $arrayApplicationData["APP_DATA"]) == $messageApplicationCorrelation) {
                                    //"Unpause" and derivate Case
                                    $arrayApplicationData["APP_DATA"] = array_merge($arrayApplicationData["APP_DATA"], $arrayVariable);

                                    $arrayResult = $case->updateCase($applicationUid, $arrayApplicationData);

                                    $result = $ws->derivateCase($userUid, $applicationUid, $delIndex);

                                    $flagCatched = true;
                                }
                            }

                            //Counter
                            if ($flagCatched) {
                                $counterIntermediateCatchMessageEvent++;
                            }
                            break;
                    }

                    //Message-Application catch
                    if ($flagCatched) {
                        $result = $messageApplication->update($messageApplicationUid, array("MSGAPP_STATUS" => "READ"));
                    }

                    $counter++;

                    //Progress bar
                    $messageApplication->frontEndShow("BAR", "Message-Events (unread): " . $counter . "/" . $totalMessageEvent . " " . $messageApplication->progressBar($totalMessageEvent, $counter));

                    $flagNextRecords = true;
                }
            } while ($flagNextRecords);

            $messageApplication->frontEndShow("TEXT", "Total Message-Events unread: " . $totalMessageEvent);
            $messageApplication->frontEndShow("TEXT", "Total cases started: " . $counterStartMessageEvent);
            $messageApplication->frontEndShow("TEXT", "Total cases continued: " . $counterIntermediateCatchMessageEvent);
            $messageApplication->frontEndShow("TEXT", "Total Message-Events pending: " . ($totalMessageEvent - ($counterStartMessageEvent + $counterIntermediateCatchMessageEvent)));

            $messageApplication->frontEndShow("END");
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get status info Case
     *
     * @param string $applicationUid Unique id of Case
     * @param int    $del_index      {@min 1}
     * @param string $userUid        Unique id of User
     *
     * return array Return an array with status info Case, array empty otherwise
     */
    public function getStatusInfo($applicationUid, $delIndex = 0, $userUid = "")
    {
        try {
            //Verify data
            $this->throwExceptionIfNotExistsCase($applicationUid, $delIndex, $this->getFieldNameByFormatFieldName("APP_UID"));

            //Get data
            //Status is PAUSED
            $delimiter = \DBAdapter::getStringDelimiter();

            $criteria = new \Criteria("workflow");

            $criteria->addSelectColumn($delimiter . "PAUSED" . $delimiter . " AS APP_STATUS");
            $criteria->addSelectColumn(\AppDelayPeer::APP_DEL_INDEX . " AS DEL_INDEX");

            $criteria->add(\AppDelayPeer::APP_UID, $applicationUid, \Criteria::EQUAL);
            $criteria->add(\AppDelayPeer::APP_TYPE, "PAUSE", \Criteria::EQUAL);
            $criteria->add(
                $criteria->getNewCriterion(\AppDelayPeer::APP_DISABLE_ACTION_USER, null, \Criteria::ISNULL)->addOr(
                $criteria->getNewCriterion(\AppDelayPeer::APP_DISABLE_ACTION_USER, 0, \Criteria::EQUAL))
            );

            if ($delIndex != 0) {
                $criteria->add(\AppDelayPeer::APP_DEL_INDEX, $delIndex, \Criteria::EQUAL);
            }

            if ($userUid != "") {
                $criteria->add(\AppDelayPeer::APP_DELEGATION_USER, $userUid, \Criteria::EQUAL);
            }

            $rsCriteria = \AppDelayPeer::doSelectRS($criteria);
            $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

            if ($rsCriteria->next()) {
                $row = $rsCriteria->getRow();

                //Return
                return array("APP_STATUS" => $row["APP_STATUS"], "DEL_INDEX" => $row["DEL_INDEX"]);
            }

            //Status is TO_DO, DRAFT
            $criteria = new \Criteria("workflow");

            $criteria->addSelectColumn(\ApplicationPeer::APP_STATUS);
            $criteria->addSelectColumn(\AppDelegationPeer::DEL_INDEX);

            $arrayCondition = array();
            $arrayCondition[] = array(\ApplicationPeer::APP_UID, \AppDelegationPeer::APP_UID, \Criteria::EQUAL);
            $arrayCondition[] = array(\ApplicationPeer::APP_UID, \AppThreadPeer::APP_UID, \Criteria::EQUAL);
            $arrayCondition[] = array(\ApplicationPeer::APP_UID, $delimiter . $applicationUid . $delimiter, \Criteria::EQUAL);
            $criteria->addJoinMC($arrayCondition, \Criteria::LEFT_JOIN);

            $criteria->add(
                $criteria->getNewCriterion(\ApplicationPeer::APP_STATUS, "TO_DO", \Criteria::EQUAL)->addAnd(
                $criteria->getNewCriterion(\AppDelegationPeer::DEL_FINISH_DATE, null, \Criteria::ISNULL))->addAnd(
                $criteria->getNewCriterion(\AppDelegationPeer::DEL_THREAD_STATUS, "OPEN"))->addAnd(
                $criteria->getNewCriterion(\AppThreadPeer::APP_THREAD_STATUS, "OPEN"))
            )->addOr(
                $criteria->getNewCriterion(\ApplicationPeer::APP_STATUS, "DRAFT", \Criteria::EQUAL)->addAnd(
                $criteria->getNewCriterion(\AppDelegationPeer::DEL_THREAD_STATUS, "OPEN"))->addAnd(
                $criteria->getNewCriterion(\AppThreadPeer::APP_THREAD_STATUS, "OPEN"))
            );

            if ($delIndex != 0) {
                $criteria->add(\AppDelegationPeer::DEL_INDEX, $delIndex, \Criteria::EQUAL);
            }

            if ($userUid != "") {
                $criteria->add(\AppDelegationPeer::USR_UID, $userUid, \Criteria::EQUAL);
            }

            $rsCriteria = \ApplicationPeer::doSelectRS($criteria);
            $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

            if ($rsCriteria->next()) {
                $row = $rsCriteria->getRow();

                //Return
                return array("APP_STATUS" => $row["APP_STATUS"], "DEL_INDEX" => $row["DEL_INDEX"]);
            }

            //Status is CANCELLED, COMPLETED
            $criteria = new \Criteria("workflow");

            $criteria->addSelectColumn(\ApplicationPeer::APP_STATUS);
            $criteria->addSelectColumn(\AppDelegationPeer::DEL_INDEX);

            $arrayCondition = array();
            $arrayCondition[] = array(\ApplicationPeer::APP_UID, \AppDelegationPeer::APP_UID, \Criteria::EQUAL);
            $arrayCondition[] = array(\ApplicationPeer::APP_UID, $delimiter . $applicationUid . $delimiter, \Criteria::EQUAL);
            $criteria->addJoinMC($arrayCondition, \Criteria::LEFT_JOIN);

            $criteria->add(\ApplicationPeer::APP_STATUS, array("CANCELLED", "COMPLETED"), \Criteria::IN);
            $criteria->add(\AppDelegationPeer::DEL_LAST_INDEX, 1, \Criteria::EQUAL);

            if ($delIndex != 0) {
                $criteria->add(\AppDelegationPeer::DEL_INDEX, $delIndex, \Criteria::EQUAL);
            }

            if ($userUid != "") {
                $criteria->add(\AppDelegationPeer::USR_UID, $userUid, \Criteria::EQUAL);
            }

            $rsCriteria = \ApplicationPeer::doSelectRS($criteria);
            $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

            if ($rsCriteria->next()) {
                $row = $rsCriteria->getRow();

                //Return
                return array("APP_STATUS" => $row["APP_STATUS"], "DEL_INDEX" => $row["DEL_INDEX"]);
            }

            //Return
            return array();
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
