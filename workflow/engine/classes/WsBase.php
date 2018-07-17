<?php

//It works with the table CONFIGURATION in a WF dataBase
use ProcessMaker\Core\System;

/**
 * Copyright (C) 2009 COLOSA
 * License: LGPL, see LICENSE
 * Last Modify: 26.06.2008 10:05:00
 * Last modify by: Erik Amaru Ortiz <erik@colosa.com>
 * Last Modify comment(26.06.2008): the session expired verification was removed from here to soap class
 *
 * @package workflow.engine.classes
 */
class WsBase
{
    public $stored_system_variables; //boolean
    public $wsSessionId; //web service session id, if the wsbase function is used from a WS request

    public function __construct($params = null)
    {
        $this->stored_system_variables = false;

        if ($params != null) {
            $this->stored_system_variables = (isset($params->stored_system_variables) ? $params->stored_system_variables : false);

            $this->wsSessionId = isset($params->wsSessionId) ? $params->wsSessionId : '';
        }
    }

    /**
     * function to start a web services session in ProcessMaker
     *
     * @param string $userid
     * @param string $password
     * @return $wsResponse will return an object
     */
    public function login($userid, $password)
    {
        global $RBAC;

        try {
            //To enable compatibility with hash login, method Enable.
            //It's necessary to enable the hash start session because there are use cases in both,
            //the web entry and in the case planner, where the password is still used in the hash
            //format so that is possible to start a session. Thiw way we will mantain the
            //compatibility with this type of loggin.
            $RBAC->enableLoginWithHash();
            $uid = $RBAC->VerifyLogin($userid, $password);

            switch ($uid) {
                case '':
                case - 1: //The user doesn't exist
                    $wsResponse = new WsResponse(3, G::loadTranslation('ID_USER_NOT_REGISTERED'));
                    break;
                case - 2: //The password is incorrect
                    $wsResponse = new WsResponse(4, G::loadTranslation('ID_WRONG_PASS'));
                    break;
                case - 3: //The user is inactive
                    $wsResponse = new WsResponse(5, G::loadTranslation('ID_USER_INACTIVE'));
                    break;
                case - 4: //The Due date is finished
                    $wsResponse = new WsResponse(5, G::loadTranslation('ID_USER_INACTIVE'));
                    break;
            }

            if ($uid < 0 || $uid == '') {
                throw (new Exception(serialize($wsResponse)));
            }

            //check access to PM
            $RBAC->loadUserRolePermission($RBAC->sSystem, $uid);
            $res = $RBAC->userCanAccess("PM_LOGIN");

            if ($res != 1 && $uid !== RBAC::GUEST_USER_UID) {
                $wsResponse = new WsResponse(2, G::loadTranslation('ID_USER_HAVENT_RIGHTS_SYSTEM'));
                throw (new Exception(serialize($wsResponse)));
            }

            $sessionId = G::generateUniqueID();
            $wsResponse = new WsResponse('0', $sessionId);

            $session = new Session();
            $session->setSesUid($sessionId);
            $session->setSesStatus('ACTIVE');
            $session->setUsrUid($uid);
            $session->setSesRemoteIp($_SERVER['REMOTE_ADDR']);
            $session->setSesInitDate(date('Y-m-d H:i:s'));
            $session->setSesDueDate(date('Y-m-d H:i:s', mktime(date('H'), date('i') + 15, date('s'), date('m'), date('d'), date('Y'))));
            $session->setSesEndDate('');
            $session->Save();

            //save the session in DataBase
        } catch (Exception $e) {
            $wsResponse = unserialize($e->getMessage());
        }

        //To enable compatibility with hash login, method disable.
        $RBAC->disableLoginWithHash();
        return $wsResponse;
    }

    /**
     * get all groups
     *
     * @param none
     * @return $result will return an object
     */
    public function processList()
    {
        try {
            $result = [];
            $oCriteria = new Criteria('workflow');
            $oCriteria->add(ProcessPeer::PRO_STATUS, 'DISABLED', Criteria::NOT_EQUAL);
            $oDataset = ProcessPeer::doSelectRS($oCriteria);
            $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $oDataset->next();

            while ($aRow = $oDataset->getRow()) {
                $oProcess = new Process();
                $arrayProcess = $oProcess->load($aRow['PRO_UID']);
                $result[] = array('guid' => $aRow['PRO_UID'], 'name' => $arrayProcess['PRO_TITLE']
                );
                $oDataset->next();
            }

            return $result;
        } catch (Exception $e) {
            $result[] = array('guid' => $e->getMessage(), 'name' => $e->getMessage()
            );

            return $result;
        }
    }

    /**
     * get all roles, to see all roles
     *
     * @param none
     * @return $result will return an object
     */
    public function roleList()
    {
        try {
            $result = [];

            $RBAC = & RBAC::getSingleton();
            $RBAC->initRBAC();
            $oCriteria = $RBAC->listAllRoles();
            $oDataset = GulliverBasePeer::doSelectRs($oCriteria);
            $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $oDataset->next();

            while ($aRow = $oDataset->getRow()) {
                $result[] = array('guid' => $aRow['ROL_UID'], 'name' => $aRow['ROL_CODE']
                );
                $oDataset->next();
            }

            return $result;
        } catch (Exception $e) {
            $result[] = array('guid' => $e->getMessage(), 'name' => $e->getMessage()
            );

            return $result;
        }
    }

    /**
     * get all groups
     *
     * @param null $search
     * @param null $regex
     * @param null $start
     * @param null $limit
     * @return array|stdClass
     */
    public function groupList($regex = null, $start = null, $limit = null)
    {
        try {
            $criteria = new Criteria('workflow');
            $criteria->addSelectColumn(GroupwfPeer::GRP_UID);
            $criteria->addSelectColumn(GroupwfPeer::GRP_TITLE);
            $criteria->add(GroupwfPeer::GRP_STATUS, 'ACTIVE');
            $criteria->addAscendingOrderByColumn(GroupwfPeer::GRP_TITLE);
            if ($regex) {
                $regex = GroupwfPeer::GRP_TITLE . " REGEXP '" . $regex . "'";
                $criteria->add(GroupwfPeer::GRP_TITLE, $regex, Criteria::CUSTOM);
            }
            if ($start) {
                $criteria->setOffset($start);
            }
            if ($limit) {
                $criteria->setLimit($limit);
            }
            $rs = GroupwfPeer::doSelectRS($criteria);
            $rs->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $result = [];
            while ($rs->next()) {
                $rows = $rs->getRow();
                $result[] = array('guid' => $rows['GRP_UID'], 'name' => $rows['GRP_TITLE']);
            }
            return $result;
        } catch (Exception $e) {
            $result[] = array('guid' => $e->getMessage(), 'name' => $e->getMessage());
            return $result;
        }
    }

    /**
     * get all department
     *
     * @param none
     * @return $result will return an object
     */
    public function departmentList()
    {
        try {
            $result = [];
            $oCriteria = new Criteria('workflow');
            $oCriteria->add(DepartmentPeer::DEP_STATUS, 'ACTIVE');
            $oDataset = DepartmentPeer::doSelectRS($oCriteria);
            $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $oDataset->next();

            while ($aRow = $oDataset->getRow()) {
                $oDepartment = new Department();
                $aDepartment = $oDepartment->Load($aRow['DEP_UID']);
                $node['guid'] = $aRow['DEP_UID'];
                $node['name'] = $aDepartment['DEP_TITLE'];
                $node['parentUID'] = $aDepartment['DEP_PARENT'];
                $node['dn'] = $aDepartment['DEP_LDAP_DN'];

                //get the users from this department
                $c = new Criteria();
                $c->clearSelectColumns();
                $c->addSelectColumn('COUNT(*)');
                $c->add(UsersPeer::DEP_UID, $aRow['DEP_UID']);
                $rs = UsersPeer::doSelectRS($c);
                $rs->next();
                $row = $rs->getRow();
                $count = $row[0];

                $node['users'] = $count;
                $result[] = $node;
                $oDataset->next();
            }

            return $result;
        } catch (Exception $e) {
            $result[] = array('guid' => $e->getMessage(), 'name' => $e->getMessage()
            );

            return $result;
        }
    }

    /**
     * Get case list
     *
     * @param string $userId
     * @return $result will return an object
     */
    public function caseList($userUid)
    {
        try {
            $solrEnabled = 0;

            if (($solrEnv = System::solrEnv()) !== false) {
                $appSolr = new AppSolr(
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
                    $arrayData = [];

                    $delegationIndexes = [];
                    $columsToInclude = array("APP_UID");
                    $solrSearchText = null;

                    //Todo
                    $solrSearchText = $solrSearchText . (($solrSearchText != null) ? " OR " : null) . "(APP_STATUS:TO_DO AND APP_ASSIGNED_USERS:" . $userUid . ")";
                    $delegationIndexes[] = "APP_ASSIGNED_USER_DEL_INDEX_" . $userUid . "_txt";

                    //Draft
                    $solrSearchText = $solrSearchText . (($solrSearchText != null) ? " OR " : null) . "(APP_STATUS:DRAFT AND APP_DRAFT_USER:" . $userUid . ")";
                    //Index is allways 1

                    $solrSearchText = "($solrSearchText)";

                    //Add del_index dynamic fields to list of resulting columns
                    $columsToIncludeFinal = array_merge($columsToInclude, $delegationIndexes);

                    $solrRequestData = EntitySolrRequestData::createForRequestPagination(
                                    array(
                                        "workspace" => $solrEnv["solr_instance"],
                                        "startAfter" => 0,
                                        "pageSize" => 1000,
                                        "searchText" => $solrSearchText,
                                        "numSortingCols" => 1,
                                        "sortCols" => array("APP_NUMBER"),
                                        "sortDir" => array(strtolower("DESC")),
                                        "includeCols" => $columsToIncludeFinal,
                                        "resultFormat" => "json"
                                    )
                    );

                    //Use search index to return list of cases
                    $searchIndex = new BpmnEngineServicesSearchIndex($appSolr->isSolrEnabled(), $solrEnv["solr_host"]);

                    //Execute query
                    $solrQueryResult = $searchIndex->getDataTablePaginatedList($solrRequestData);

                    //Get the missing data from database
                    $arrayApplicationUid = [];

                    foreach ($solrQueryResult->aaData as $i => $data) {
                        $arrayApplicationUid[] = $data["APP_UID"];
                    }

                    $aaappsDBData = $appSolr->getListApplicationDelegationData($arrayApplicationUid);

                    foreach ($solrQueryResult->aaData as $i => $data) {
                        //Initialize array
                        $delIndexes = []; //Store all the delegation indexes
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
                            $aRow = [];

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

                            $aRow["APP_NUMBER"] = $row["APP_NUMBER"];
                            $aRow["APP_STATUS"] = $row["APP_STATUS"];
                            $aRow["PRO_UID"] = $row["PRO_UID"];
                            $aRow["DEL_INDEX"] = $row["DEL_INDEX"];

                            $arrayData[] = array(
                                "guid" => $aRow["APP_UID"],
                                "name" => $aRow["APP_NUMBER"],
                                "status" => $aRow["APP_STATUS"],
                                "delIndex" => $aRow["DEL_INDEX"],
                                "processId" => $aRow["PRO_UID"]
                            );
                        }
                    }

                    return $arrayData;
                } catch (InvalidIndexSearchTextException $e) {
                    $arrayData = [];

                    $arrayData[] = array(
                        "guid" => $e->getMessage(),
                        "name" => $e->getMessage(),
                        "status" => $e->getMessage(),
                        "delIndex" => $e->getMessage(),
                        "processId" => $e->getMessage()
                    );

                    return $arrayData;
                }
            } else {
                $arrayData = [];

                $criteria = new Criteria("workflow");

                $criteria->addSelectColumn(AppCacheViewPeer::APP_UID);
                $criteria->addSelectColumn(AppCacheViewPeer::DEL_INDEX);
                $criteria->addSelectColumn(AppCacheViewPeer::APP_NUMBER);
                $criteria->addSelectColumn(AppCacheViewPeer::APP_STATUS);
                $criteria->addSelectColumn(AppCacheViewPeer::PRO_UID);

                $criteria->add(AppCacheViewPeer::USR_UID, $userUid);

                $criteria->add(
                        //ToDo - getToDo()
                        $criteria->getNewCriterion(AppCacheViewPeer::APP_STATUS, "TO_DO", CRITERIA::EQUAL)->addAnd(
                                $criteria->getNewCriterion(AppCacheViewPeer::DEL_FINISH_DATE, null, Criteria::ISNULL))->addAnd(
                                $criteria->getNewCriterion(AppCacheViewPeer::APP_THREAD_STATUS, "OPEN"))->addAnd(
                                $criteria->getNewCriterion(AppCacheViewPeer::DEL_THREAD_STATUS, "OPEN"))
                )->addOr(
                        //Draft - getDraft()
                        $criteria->getNewCriterion(AppCacheViewPeer::APP_STATUS, "DRAFT", CRITERIA::EQUAL)->addAnd(
                                $criteria->getNewCriterion(AppCacheViewPeer::APP_THREAD_STATUS, "OPEN"))->addAnd(
                                $criteria->getNewCriterion(AppCacheViewPeer::DEL_THREAD_STATUS, "OPEN"))
                );

                $criteria->addDescendingOrderByColumn(AppCacheViewPeer::APP_NUMBER);

                $rsCriteria = AppCacheViewPeer::doSelectRS($criteria);
                $rsCriteria->setFetchmode(ResultSet::FETCHMODE_ASSOC);

                while ($rsCriteria->next()) {
                    $row = $rsCriteria->getRow();

                    $arrayData[] = array(
                        "guid" => $row["APP_UID"],
                        "name" => $row["APP_NUMBER"],
                        "status" => $row["APP_STATUS"],
                        "delIndex" => $row["DEL_INDEX"],
                        "processId" => $row["PRO_UID"]
                    );
                }

                return $arrayData;
            }
        } catch (Exception $e) {
            $arrayData = [];

            $arrayData[] = array(
                "guid" => $e->getMessage(),
                "name" => $e->getMessage(),
                "status" => $e->getMessage(),
                "delIndex" => $e->getMessage(),
                "processId" => $e->getMessage()
            );

            return $arrayData;
        }
    }

    /**
     * Get unassigned case list
     *
     * @param string $userId
     * @return $result will return an object
     */
    public function unassignedCaseList($userId)
    {
        try {
            $result = [];
            $oAppCache = new AppCacheView();
            $Criteria = $oAppCache->getUnassignedListCriteria($userId);
            $oDataset = AppCacheViewPeer::doSelectRS($Criteria);
            $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $oDataset->next();

            while ($aRow = $oDataset->getRow()) {
                $result[] = array('guid' => $aRow['APP_UID'],
                    'name' => $aRow['APP_NUMBER'],
                    'delIndex' => $aRow['DEL_INDEX'],
                    'processId' => $aRow['PRO_UID']);

                $oDataset->next();
            }

            return $result;
        } catch (Exception $e) {
            $result[] = array('guid' => $e->getMessage(),
                'name' => $e->getMessage(),
                'status' => $e->getMessage(),
                'status' => $e->getMessage(),
                'processId' => $e->getMessage());

            return $result;
        }
    }

    /**
     * Get all users
     *
     * @param none
     * @return array $result, will return an array
     * @throws Exception
     */
    public function userList()
    {
        try {
            $result = [];
            $criteria = new Criteria('workflow');
            $criteria->add(UsersPeer::USR_STATUS, 'ACTIVE');
            $criteria->add(UsersPeer::USR_UID, [RBAC::GUEST_USER_UID], Criteria::NOT_IN);
            $dataset = UsersPeer::doSelectRS($criteria);
            $dataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $dataset->next();

            while ($row = $dataset->getRow()) {
                $result[] = ['guid' => $row['USR_UID'], 'name' => $row['USR_USERNAME']];
                $dataset->next();
            }

            return $result;
        } catch (Exception $e) {
            $result[] = [
                'guid' => $e->getMessage(),
                'name' => $e->getMessage()
            ];

            return $result;
        }
    }

    /**
     * get list of all the available triggers in a workspace
     *
     * @param none
     * @return $result will return an object
     */
    public function triggerList()
    {
        try {
            $result = [];
            $oCriteria = new Criteria('workflow');
            $oCriteria->addSelectColumn(TriggersPeer::TRI_UID);
            $oCriteria->addSelectColumn(TriggersPeer::PRO_UID);
            $oCriteria->addAsColumn('TITLE', TriggersPeer::TRI_TITLE);
            $oDataset = TriggersPeer::doSelectRS($oCriteria);
            $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $oDataset->next();

            while ($aRow = $oDataset->getRow()) {
                $result[] = array('guid' => $aRow['TRI_UID'], 'name' => $aRow['TITLE'], 'processId' => $aRow['PRO_UID']
                );

                $oDataset->next();
            }

            return $result;
        } catch (Exception $e) {
            $result[] = array('guid' => $e->getMessage(), 'name' => $e->getMessage()
            );

            return $result;
        }
    }

    /**
     * get list of the uploaded documents for a given case
     *
     * @param string $sApplicationUID
     * @param string $sUserUID
     * @return $result
     */
    public function inputDocumentList($sApplicationUID, $sUserUID)
    {
        try {
            $oCase = new Cases();
            $fields = $oCase->loadCase($sApplicationUID);
            $sProcessUID = $fields['PRO_UID'];
            $sTaskUID = '';
            $oCriteria = $oCase->getAllUploadedDocumentsCriteria($sProcessUID, $sApplicationUID, $sTaskUID, $sUserUID);

            $result = [];
            global $_DBArray;

            foreach ($_DBArray['inputDocuments'] as $key => $row) {
                if (isset($row['DOC_VERSION'])) {
                    $docrow = [];
                    $docrow['guid'] = $row['APP_DOC_UID'];
                    $docrow['filename'] = $row['APP_DOC_FILENAME'];
                    $docrow['docId'] = $row['DOC_UID'];
                    $docrow['version'] = $row['DOC_VERSION'];
                    $docrow['createDate'] = $row['CREATE_DATE'];
                    $docrow['createBy'] = $row['CREATED_BY'];
                    $docrow['type'] = $row['TYPE'];
                    $docrow['index'] = $row['APP_DOC_INDEX'];
                    $docrow['link'] = 'cases/' . $row['DOWNLOAD_LINK'];
                    $result[] = $docrow;
                }
            }

            return $result;
        } catch (Exception $e) {
            $result[] = array('guid' => $e->getMessage()
            );

            return $result;
        }
    }

    /**
     * input document process list
     *
     * @param string $sProcessUID
     * @return $result will return an object
     */
    public function inputDocumentProcessList($sProcessUID)
    {
        try {
            global $_DBArray;

            $_DBArray = (isset($_SESSION['_DBArray']) ? $_SESSION['_DBArray'] : '');

            $oMap = new ProcessMap();
            $oCriteria = $oMap->getInputDocumentsCriteria($sProcessUID);
            $oDataset = InputDocumentPeer::doSelectRS($oCriteria);
            $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $oDataset->next();

            $result = [];

            while ($aRow = $oDataset->getRow()) {
                if ($aRow['INP_DOC_TITLE'] == null) {
                    //There is no transaltion for this Document name, try to get/regenerate the label
                    $inputDocument = new InputDocument();
                    $inputDocumentObj = $inputDocument->load($aRow['INP_DOC_UID']);
                    $aRow['INP_DOC_TITLE'] = $inputDocumentObj['INP_DOC_TITLE'];
                    $aRow['INP_DOC_DESCRIPTION'] = $inputDocumentObj['INP_DOC_DESCRIPTION'];
                }

                $docrow = [];
                $docrow['guid'] = $aRow['INP_DOC_UID'];
                $docrow['name'] = $aRow['INP_DOC_TITLE'];
                $docrow['description'] = $aRow['INP_DOC_DESCRIPTION'];
                $result[] = $docrow;
                $oDataset->next();
            }

            return $result;
        } catch (Exception $e) {
            $result[] = array('guid' => $e->getMessage()
            );

            return $result;
        }
    }

    /**
     * output document list
     *
     * @param string $sApplicationUID
     * @param string $sUserUID
     * @return $result will return an object
     */
    public function outputDocumentList($sApplicationUID, $sUserUID)
    {
        try {
            $oCase = new Cases();
            $fields = $oCase->loadCase($sApplicationUID);
            $sProcessUID = $fields['PRO_UID'];
            $sTaskUID = '';
            $oCriteria = $oCase->getAllGeneratedDocumentsCriteria($sProcessUID, $sApplicationUID, $sTaskUID, $sUserUID);

            $result = [];
            global $_DBArray;

            foreach ($_DBArray['outputDocuments'] as $key => $row) {
                if (isset($row['DOC_VERSION'])) {
                    $docrow = [];
                    $docrow['guid'] = $row['APP_DOC_UID'];
                    $docrow['filename'] = $row['DOWNLOAD_FILE'];

                    $docrow['docId'] = $row['DOC_UID'];
                    $docrow['version'] = $row['DOC_VERSION'];
                    $docrow['createDate'] = $row['CREATE_DATE'];
                    $docrow['createBy'] = $row['CREATED_BY'];
                    $docrow['type'] = $row['TYPE'];
                    $docrow['index'] = $row['APP_DOC_INDEX'];
                    $docrow['link'] = 'cases/' . $row['DOWNLOAD_LINK'];
                    $result[] = $docrow;
                }
            }

            return $result;
        } catch (Exception $e) {
            $result[] = array('guid' => $e->getMessage()
            );

            return $result;
        }
    }

    /**
     * remove document
     *
     * @param string $appDocUid
     * @return $result will return an object
     */
    public function removeDocument($appDocUid)
    {
        try {
            $oAppDocument = new AppDocument();
            $oAppDocument->remove($appDocUid, 1); //always send version 1
            $result = new WsResponse(0, " $appDocUid");

            return $result;
        } catch (Exception $e) {
            $result = new WsResponse(100, $e->getMessage());

            return $result;
        }
    }

    /**
     * get task list
     *
     * @param string $userId
     * @return $result will return an object
     */
    public function taskList($userId)
    {
        try {
            $oGroup = new Groups();
            $aGroups = $oGroup->getActiveGroupsForAnUser($userId);

            $result = [];
            $oCriteria = new Criteria('workflow');
            $del = DBAdapter::getStringDelimiter();
            $oCriteria->addSelectColumn(TaskPeer::PRO_UID);
            $oCriteria->addSelectColumn(TaskPeer::TAS_UID);
            $oCriteria->addSelectColumn(TaskPeer::TAS_TITLE);
            $oCriteria->addSelectColumn(TaskPeer::TAS_START);
            $oCriteria->setDistinct();
            $oCriteria->addJoin(TaskPeer::TAS_UID, TaskUserPeer::TAS_UID, Criteria::LEFT_JOIN);
            $oCriteria->addOr(TaskUserPeer::USR_UID, $userId);
            $oCriteria->addOr(TaskUserPeer::USR_UID, $aGroups, Criteria::IN);

            $oDataset = TaskPeer::doSelectRS($oCriteria);
            $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $oDataset->next();

            while ($aRow = $oDataset->getRow()) {
                $result[] = array('guid' => $aRow['TAS_UID'],
                    'name' => $aRow['TAS_TITLE'],
                    'processId' => $aRow['PRO_UID'],
                    'initialTask' => $aRow['TAS_START'] == 'TRUE' ? '1' : '0');
                $oDataset->next();
            }

            return $result;
        } catch (Exception $e) {
            $result[] = array('guid' => $e->getMessage(), 'name' => $e->getMessage()
            );

            return $result;
        }
    }

    /**
     * send message
     *
     * @param string $caseId
     * @param string $sFrom
     * @param string $sTo
     * @param string $sCc
     * @param string $sBcc
     * @param string $sSubject
     * @param string $sTemplate
     * @param $appFields = null
     * @param $aAttachment = null
     * @param boolean $showMessage = true
     * @param int $delIndex = 0
     * @param $config = array
     * @return $result will return an object
     */
    public function sendMessage(
    $caseId, $sFrom, $sTo, $sCc, $sBcc, $sSubject, $sTemplate, $appFields = null, $aAttachment = null, $showMessage = true, $delIndex = 0, $config = [], $gmail = 0
    ) {
        try {

                /*----------------------------------********---------------------------------*/
                $aSetup = System::getEmailConfiguration();
            /*----------------------------------********---------------------------------*/

            $msgError = "";
            if (sizeof($aSetup) == 0) {
                $msgError = "The default configuration wasn't defined";
            }

            $oSpool = new SpoolRun();

            $oSpool->setConfig($aSetup);

            $oCase = new Cases();

            $oldFields = $oCase->loadCase($caseId);
            if ($gmail == 1) {
                $pathEmail = PATH_DATA_SITE . 'mailTemplates' . PATH_SEP;
            } else {
                $pathEmail = PATH_DATA_SITE . 'mailTemplates' . PATH_SEP . $oldFields['PRO_UID'] . PATH_SEP;
            }
            $fileTemplate = $pathEmail . $sTemplate;
            G::mk_dir($pathEmail, 0777, true);

            if (!file_exists($fileTemplate)) {
                $data['FILE_TEMPLATE'] = $fileTemplate;
                $result = new WsResponse(28, G::LoadTranslation('ID_TEMPLATE_FILE_NOT_EXIST', SYS_LANG, $data));

                return $result;
            }

            if ($appFields == null) {
                $Fields = $oldFields['APP_DATA'];
            } else {
                $Fields = array_merge($oldFields['APP_DATA'], $appFields);
            }

            $sBody = G::replaceDataGridField(file_get_contents($fileTemplate), $Fields, false);

            $sFrom = G::buildFrom($aSetup, $sFrom);

            $showMessage = ($showMessage) ? 1 : 0;

            $messageArray = array(
                "msg_uid" => "",
                "app_uid" => $caseId,
                "del_index" => $delIndex,
                "app_msg_type" => "TRIGGER",
                "app_msg_subject" => $sSubject,
                "app_msg_from" => $sFrom,
                "app_msg_to" => $sTo,
                "app_msg_body" => $sBody,
                "app_msg_cc" => $sCc,
                "app_msg_bcc" => $sBcc,
                "app_msg_attach" => $aAttachment,
                "app_msg_template" => "",
                "app_msg_status" => "pending",
                "app_msg_show_message" => $showMessage,
                "app_msg_error" => $msgError,
                "contentTypeIsHtml" => (preg_match("/^.+\.html?$/i", $fileTemplate)) ? true : false
            );

            $oSpool->create($messageArray);
            if ($gmail != 1) {
                $oSpool->sendMail();

                if ($oSpool->status == 'sent') {
                    $result = new WsResponse(0, G::loadTranslation('ID_MESSAGE_SENT') . ": " . $sTo);
                } else {
                    $result = new WsResponse(29, $oSpool->status . ' ' . $oSpool->error . print_r($aSetup, 1));
                }
            } else {
                $result = "";
            }
            return $result;
        } catch (Exception $e) {
            return new WsResponse(100, $e->getMessage());
        }
    }

    /**
     * get case information
     *
     * @param string $caseId
     * @param string $iDelIndex
     * @param bool   $flagUseDelIndex
     *
     * @return $result will return an object
     */
    public function getCaseInfo($caseId, $iDelIndex, $flagUseDelIndex = false)
    {
        try {
            $oCase = new Cases();
            $aRows = $oCase->loadCase($caseId, $iDelIndex);

            if (count($aRows) == 0) {
                $data['CASE_NUMBER'] = $caseNumber;
                $result = new WsResponse(16, G::loadTranslation('ID_CASE_DOES_NOT_EXIST', SYS_LANG, $data));

                return $result;
            }

            $oProcess = new Process();

            try {
                $uFields = $oProcess->load($aRows['PRO_UID']);
                $processName = $uFields['PRO_TITLE'];
            } catch (Exception $e) {
                $processName = '';
            }

            $result = new WsResponse(0, G::loadTranslation('ID_COMMAND_EXECUTED_SUCCESSFULLY'));
            $result->caseId = $aRows['APP_UID'];
            $result->caseNumber = $aRows['APP_NUMBER'];
            $result->caseName = $aRows['TITLE'];
            $result->caseStatus = $aRows['APP_STATUS'];
            $result->caseParalell = $aRows['APP_PARALLEL'];
            $result->caseCreatorUser = $aRows['APP_INIT_USER'];
            $result->caseCreatorUserName = $aRows['CREATOR'];
            $result->processId = $aRows['PRO_UID'];
            $result->processName = $processName;
            $result->createDate = $aRows['CREATE_DATE'];
            $result->updateDate = $aRows['UPDATE_DATE'];

            //now fill the array of AppDelay
            $oCriteria = new Criteria('workflow');
            $oCriteria->addSelectColumn(AppDelayPeer::APP_DEL_INDEX);
            $oCriteria->add(AppDelayPeer::APP_UID, $caseId);
            $oCriteria->add(AppDelayPeer::APP_TYPE, 'PAUSE');
            $oCriteria->add(AppDelayPeer::APP_DISABLE_ACTION_USER, '0');

            $oDataset = AppDelayPeer::doSelectRS($oCriteria);
            $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $aIndexsPaused = [];
            while ($oDataset->next()) {
                $data = $oDataset->getRow();
                $aIndexsPaused[] = $data['APP_DEL_INDEX'];
            }

            //now fill the array of AppDelegationPeer
            $oCriteria = new Criteria('workflow');
            $oCriteria->addSelectColumn(AppDelegationPeer::DEL_INDEX);
            $oCriteria->addSelectColumn(AppDelegationPeer::USR_UID);
            $oCriteria->addSelectColumn(AppDelegationPeer::TAS_UID);
            $oCriteria->addSelectColumn(AppDelegationPeer::DEL_THREAD);
            $oCriteria->addSelectColumn(AppDelegationPeer::DEL_THREAD_STATUS);
            $oCriteria->addSelectColumn(AppDelegationPeer::DEL_FINISH_DATE);
            $oCriteria->addSelectColumn(AppDelegationPeer::DEL_INIT_DATE);
            $oCriteria->addSelectColumn(AppDelegationPeer::DEL_TASK_DUE_DATE);
            $oCriteria->add(AppDelegationPeer::APP_UID, $caseId);

            if (count($aIndexsPaused)) {
                $cton1 = $oCriteria->getNewCriterion(AppDelegationPeer::DEL_FINISH_DATE, null, Criteria::ISNULL);
                $cton2 = $oCriteria->getNewCriterion(AppDelegationPeer::DEL_INDEX, $aIndexsPaused, Criteria::IN);
                $cton1->addOR($cton2);
                $oCriteria->add($cton1);
            } else {
                $oCriteria->add(AppDelegationPeer::DEL_FINISH_DATE, null, Criteria::ISNULL);
            }

            $oCriteria->addAscendingOrderByColumn(AppDelegationPeer::DEL_INDEX);
            $oDataset = AppDelegationPeer::doSelectRS($oCriteria);
            $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);

            $aCurrentUsers = [];

            while ($oDataset->next()) {
                $aAppDel = $oDataset->getRow();

                $oUser = new Users();

                try {
                    $oUser->load($aAppDel['USR_UID']);
                    $uFields = $oUser->toArray(BasePeer::TYPE_FIELDNAME);
                    $currentUserName = $oUser->getUsrFirstname() . ' ' . $oUser->getUsrLastname();
                } catch (Exception $e) {
                    $currentUserName = '';
                }

                $oTask = new Task();

                try {
                    $uFields = $oTask->load($aAppDel['TAS_UID']);
                    $taskName = $uFields['TAS_TITLE'];
                } catch (Exception $e) {
                    $taskName = '';
                }

                $currentUser = new stdClass();
                $currentUser->userId = $aAppDel['USR_UID'];
                $currentUser->userName = $currentUserName;
                $currentUser->taskId = $aAppDel['TAS_UID'];
                $currentUser->taskName = $taskName;
                $currentUser->delIndex = $aAppDel['DEL_INDEX'];
                $currentUser->delThread = $aAppDel['DEL_THREAD'];
                $currentUser->delThreadStatus = $aAppDel['DEL_THREAD_STATUS'];
                $currentUser->delStatus = ($aAppDel["DEL_THREAD_STATUS"] == 'CLOSED') ? 'PAUSED' : $aRows['APP_STATUS'];
                $currentUser->delInitDate = $aAppDel["DEL_INIT_DATE"];
                $currentUser->delTaskDueDate = $aAppDel["DEL_TASK_DUE_DATE"];
                $aCurrentUsers[] = $currentUser;
            }

            $result->currentUsers = $aCurrentUsers;

            return $result;
        } catch (Exception $e) {
            $result = new WsResponse(100, $e->getMessage());

            return $result;
        }
    }

    /**
     * Create an new user
     *
     * @param string sessionId : The session ID.
     * @param string userName : The username for the new user.
     * @param string firstName : The user's first name.
     * @param string lastName : The user's last name.
     * @param string email : The user's email address.
     * @param string role : The user's role, such as "PROCESSMAKER_ADMIN" or "PROCESSMAKER_OPERATOR".
     * @param string password : The user's password such as "Be@gle2" (It will be automatically encrypted
     * with an MD5 hash).
     * @param string dueDate : Optional parameter. The expiration date must be a string in the format "yyyy-mm-dd".
     * @param string status : Optional parameter. The user's status, such as "ACTIVE", "INACTIVE" or "VACATION".
     * @return $result will return an object
     */
    public function createUser($userName, $firstName, $lastName, $email, $role, $password, $dueDate = null, $status = null)
    {
        try {
            global $RBAC;

            $RBAC->initRBAC();

            if (empty($userName)) {
                $result = new WsCreateUserResponse(25, G::loadTranslation("ID_USERNAME_REQUIRED"), null);

                return $result;
            }

            if (empty($firstName)) {
                $result = new WsCreateUserResponse(27, G::loadTranslation("ID_MSG_ERROR_USR_FIRSTNAME"), null);

                return $result;
            }

            if (empty($password)) {
                $result = new WsCreateUserResponse(26, G::loadTranslation("ID_PASSWD_REQUIRED"), null);

                return $result;
            }

            $mktimeDueDate = 0;

            if (!empty($dueDate) && $dueDate != 'null' && $dueDate) {
                if (!preg_match("/^(\d{4})-(\d{2})-(\d{2})$/", $dueDate, $arrayMatch)) {
                    $result = new WsCreateUserResponse(- 1, G::loadTranslation("ID_INVALID_DATA") . " $dueDate", null);

                    return $result;
                } else {
                    $mktimeDueDate = mktime(0, 0, 0, intval($arrayMatch[2]), intval($arrayMatch[3]), intval($arrayMatch[1]));
                }
            } else {
                $mktimeDueDate = mktime(0, 0, 0, date("m"), date("d"), date("Y") + 1);
            }

            if (!empty($status) && $status != 'null' && $status) {
                if ($status != "ACTIVE" && $status != "INACTIVE" && $status != "VACATION") {
                    $result = new WsCreateUserResponse(- 1, G::loadTranslation("ID_INVALID_DATA") . " $status", null);

                    return $result;
                }
            } else {
                $status = "ACTIVE";
            }

            $arrayRole = $RBAC->loadById($role);
            $strRole = null;

            if (is_array($arrayRole)) {
                $strRole = $arrayRole["ROL_CODE"];
            } else {
                $strRole = $role;

                if ($RBAC->verifyByCode($role) == 0) {
                    $data = [];
                    $data["ROLE"] = $role;

                    $result = new WsCreateUserResponse(6, G::loadTranslation("ID_INVALID_ROLE", SYS_LANG, $data), null);

                    return $result;
                }
            }

            if (strlen($password) > 20) {
                $result = new WsCreateUserResponse(- 1, G::loadTranslation("ID_PASSWORD_SURPRASES"), null);

                return $result;
            }

            if ($RBAC->verifyUser($userName) == 1) {
                $data = [];
                $data["USER_ID"] = $userName;

                $result = new WsCreateUserResponse(7, G::loadTranslation("ID_USERNAME_ALREADY_EXISTS", SYS_LANG, $data), null);

                return $result;
            }

            //Set fields
            $arrayData = [];

            $arrayData["USR_USERNAME"] = $userName;
            $arrayData["USR_PASSWORD"] = Bootstrap::hashPassword($password);
            $arrayData["USR_FIRSTNAME"] = $firstName;
            $arrayData["USR_LASTNAME"] = $lastName;
            $arrayData["USR_EMAIL"] = $email;
            $arrayData["USR_DUE_DATE"] = $mktimeDueDate;
            $arrayData["USR_CREATE_DATE"] = date("Y-m-d H:i:s");
            $arrayData["USR_UPDATE_DATE"] = date("Y-m-d H:i:s");
            $arrayData["USR_BIRTHDAY"] = date("Y-m-d");
            $arrayData["USR_AUTH_USER_DN"] = "";
            $arrayData["USR_STATUS"] = ($status == "ACTIVE") ? 1 : 0;

            try {
                $userUid = $RBAC->createUser($arrayData, $strRole);
            } catch (Exception $oError) {
                $result = new WsCreateUserResponse(100, $oError->getMessage(), null);
                return $result;
            }

            $arrayData["USR_UID"] = $userUid;
            $arrayData["USR_STATUS"] = $status;
            $arrayData["USR_COUNTRY"] = "";
            $arrayData["USR_CITY"] = "";
            $arrayData["USR_LOCATION"] = "";
            $arrayData["USR_ADDRESS"] = "";
            $arrayData["USR_PHONE"] = "";
            $arrayData["USR_ZIP_CODE"] = "";
            $arrayData["USR_POSITION"] = "";
            $arrayData["USR_ROLE"] = $strRole;

            $user = new Users();
            $user->create($arrayData);

            //Response
            $data = [];
            $data["FIRSTNAME"] = $firstName;
            $data["LASTNAME"] = $lastName;
            $data["USER_ID"] = $userName;

            $res = new WsResponse(0, G::loadTranslation("ID_USER_CREATED_SUCCESSFULLY", SYS_LANG, $data));

            $result = array("status_code" => $res->status_code, "message" => $res->message, "userUID" => $userUid, "timestamp" => $res->timestamp
            );

            return $result;
        } catch (Exception $e) {
            $result = new WsCreateUserResponse(100, $e->getMessage(), null);

            return $result;
        }
    }

    /**
     * Update user
     *
     * @param string userUid : The user UID.
     * @param string userName : The username for the user.
     * @param string firstName : Optional parameter. The user's first name.
     * @param string lastName : Optional parameter. The user's last name.
     * @param string email : Optional parameter. The user's email address.
     * @param string dueDate : Optional parameter. The expiration date must be a string in the format "yyyy-mm-dd".
     * @param string status : Optional parameter. The user's status, such as "ACTIVE", "INACTIVE" or "VACATION".
     * @param string role : Optional parameter. The user's role, such
     * as "PROCESSMAKER_ADMIN" or "PROCESSMAKER_OPERATOR".
     * @param string password : Optional parameter. The user's password such as "Be@gle2" (It will be automatically
     * encrypted with an MD5 hash).
     * @return $result will return an object
     */
    public function updateUser($userUid, $userName, $firstName = null, $lastName = null, $email = null, $dueDate = null, $status = null, $role = null, $password = null)
    {
        try {
            global $RBAC;

            $RBAC->initRBAC();

            if (empty($userUid)) {
                $result = new WsResponse(25, G::LoadTranslation("ID_REQUIRED_FIELD") . " userUid");

                return $result;
            }

            if (empty($userName)) {
                $result = new WsResponse(25, G::LoadTranslation("ID_USERNAME_REQUIRED"));

                return $result;
            }

            if ($RBAC->verifyUserId($userUid) == 0) {
                $result = new WsResponse(3, G::loadTranslation("ID_USER_NOT_REGISTERED_SYSTEM"));

                return $result;
            }

            $mktimeDueDate = 0;

            if (!empty($dueDate)) {
                if (!preg_match("/^(\d{4})-(\d{2})-(\d{2})$/", $dueDate, $arrayMatch)) {
                    $result = new WsResponse(- 1, G::LoadTranslation("ID_INVALID_DATA") . " $dueDate");

                    return $result;
                } else {
                    $mktimeDueDate = mktime(0, 0, 0, intval($arrayMatch[2]), intval($arrayMatch[3]), intval($arrayMatch[1]));
                }
            }

            if (!empty($status)) {
                if ($status != "ACTIVE" && $status != "INACTIVE" && $status != "VACATION") {
                    $result = new WsResponse(- 1, G::LoadTranslation("ID_INVALID_DATA") . " $status");

                    return $result;
                }
            }

            $strRole = null;

            if (!empty($role)) {
                $arrayRole = $RBAC->loadById($role);

                if (is_array($arrayRole)) {
                    $strRole = $arrayRole["ROL_CODE"];
                } else {
                    $strRole = $role;

                    if ($RBAC->verifyByCode($role) == 0) {
                        $data = [];
                        $data["ROLE"] = $role;

                        $result = new WsResponse(6, G::LoadTranslation("ID_INVALID_ROLE", SYS_LANG, $data));

                        return $result;
                    }
                }
            }

            if (!empty($password) && strlen($password) > 20) {
                $result = new WsResponse(- 1, G::LoadTranslation("ID_PASSWORD_SURPRASES"));

                return $result;
            }

            $criteria = new Criteria();
            $criteria->addSelectColumn(UsersPeer::USR_UID);
            $criteria->add(UsersPeer::USR_USERNAME, $userName);
            $criteria->add(UsersPeer::USR_UID, $userUid, Criteria::NOT_EQUAL);
            $rs = UsersPeer::doSelectRS($criteria);

            if ($rs->next()) {
                $data = [];
                $data["USER_ID"] = $userName;

                $result = new WsResponse(7, G::LoadTranslation("ID_USERNAME_ALREADY_EXISTS", SYS_LANG, $data));

                return $result;
            }

            //Set fields
            $arrayData = [];

            $arrayData["USR_UID"] = $userUid;
            $arrayData["USR_USERNAME"] = $userName;

            if (!empty($firstName)) {
                $arrayData["USR_FIRSTNAME"] = $firstName;
            }

            if (!empty($lastName)) {
                $arrayData["USR_LASTNAME"] = $lastName;
            }

            if (!empty($email)) {
                $arrayData["USR_EMAIL"] = $email;
            }

            if ($mktimeDueDate != 0) {
                $arrayData["USR_DUE_DATE"] = $mktimeDueDate;
            }

            $arrayData["USR_UPDATE_DATE"] = date("Y-m-d H:i:s");

            if (!empty($status)) {
                $arrayData["USR_STATUS"] = $status;
            }

            if ($strRole != null) {
                $arrayData["USR_ROLE"] = $strRole;
            }

            if (!empty($password)) {
                $arrayData["USR_PASSWORD"] = Bootstrap::hashPassword($password);
            }

            //Update user
            if ($strRole != null) {
                $RBAC->updateUser($arrayData, $strRole);
            } else {
                $RBAC->updateUser($arrayData);
            }

            $user = new Users();
            $user->update($arrayData);

            //Response
            $res = new WsResponse(0, G::LoadTranslation("ID_UPDATED_SUCCESSFULLY"));

            $result = array("status_code" => $res->status_code, "message" => $res->message, "timestamp" => $res->timestamp
            );

            return $result;
        } catch (Exception $e) {
            $result = new WsResponse(100, $e->getMessage());

            return $result;
        }
    }

    /**
     * Information User
     * @param string userUid : The user UID.
     * @return $result will return an object
     */
    public function informationUser($userUid)
    {
        try {
            if (empty($userUid)) {
                $result = new WsResponse(100, G::LoadTranslation("ID_REQUIRED_FIELD") . " userUid");

                return $result;
            }

            $user = new Users();
            $userInfo = $user->getAllInformation($userUid);

            //Response
            $res = new WsResponse(0, G::LoadTranslation("ID_COMMAND_EXECUTED_SUCCESSFULLY"));

            $result = new stdClass();
            $result->status_code = $res->status_code;
            $result->message = $res->message;
            $result->timestamp = $res->timestamp;
            $result->info = $userInfo;

            return $result;
        } catch (Exception $e) {
            $result = new WsResponse(100, $e->getMessage());

            return $result;
        }
    }

    /**
     * create Group
     *
     * @param string $groupName
     * @return $result will return an object
     */
    public function createGroup($groupName)
    {
        try {
            if (trim($groupName) == '') {
                $result = new WsCreateGroupResponse(25, G::loadTranslation('ID_GROUP_NAME_REQUIRED'), '');
                return $result;
            }

            $group = new Groupwf();
            $grpRow['GRP_TITLE'] = $groupName;
            $groupId = $group->create($grpRow);

            $data['GROUP_NAME'] = $groupName;

            $result = new WsCreateGroupResponse(0, G::loadTranslation('ID_GROUP_CREATED_SUCCESSFULLY', SYS_LANG, $data), $groupId);

            return $result;
        } catch (Exception $e) {
            $result = WsCreateGroupResponse(100, $e->getMessage(), '');

            return $result;
        }
    }

    /**
     * Create New Department link on the top section of the left pane allows you to create a root-level department.
     *
     * @param string $departmentName
     * @param string $parentUID
     * @return $result will return an object
     */
    public function createDepartment($departmentName, $parentUID)
    {
        try {
            if (trim($departmentName) == '') {
                $result = new WsCreateDepartmentResponse(25, G::loadTranslation('ID_DEPARTMENT_NAME_REQUIRED'), '');

                return $result;
            }

            $department = new Department();

            if (($parentUID != '') && !($department->existsDepartment($parentUID))) {
                $result = new WsCreateDepartmentResponse(26, G::loadTranslation('ID_PARENT_DEPARTMENT_NOT_EXIST'), $parentUID);

                return $result;
            }

            if ($department->checkDepartmentName($departmentName, $parentUID)) {
                $result = new WsCreateDepartmentResponse(27, G::loadTranslation('ID_DEPARTMENT_EXISTS'), '');

                return $result;
            }

            $row['DEP_TITLE'] = $departmentName;
            $row['DEP_PARENT'] = $parentUID;

            $departmentId = $department->create($row);

            $data['DEPARTMENT_NAME'] = $departmentName;
            $data['PARENT_UID'] = $parentUID;
            $data['DEPARTMENT_NAME'] = $departmentName;

            $result = new WsCreateDepartmentResponse(0, G::loadTranslation('ID_DEPARTMENT_CREATED_SUCCESSFULLY', SYS_LANG, $data), $departmentId);

            return $result;
        } catch (Exception $e) {
            $result = WsCreateDepartmentResponse(100, $e->getMessage(), '');

            return $result;
        }
    }

    /**
     * remove user from group
     *
     * @param string $appDocUid
     * @return $result will return an object
     */
    public function removeUserFromGroup($userId, $groupId)
    {
        try {
            global $RBAC;

            $RBAC->initRBAC();
            $user = $RBAC->verifyUserId($userId);

            if ($user == 0) {
                $result = new WsResponse(3, G::loadTranslation('ID_USER_NOT_REGISTERED_SYSTEM'));

                return $result;
            }

            $groups = new Groups();
            $very_group = $groups->verifyGroup($groupId);

            if ($very_group == 0) {
                $result = new WsResponse(9, G::loadTranslation('ID_GROUP_NOT_REGISTERED_SYSTEM'));

                return $result;
            }

            $very_user = $groups->verifyUsertoGroup($groupId, $userId);

            if ($very_user == 1) {
                $oGroup = new Groups();
                $oGroup->removeUserOfGroup($groupId, $userId);
                $result = new WsResponse(0, G::loadTranslation('ID_COMMAND_EXECUTED_SUCCESSFULY'));

                return $result;
            }

            $result = new WsResponse(8, G::loadTranslation('ID_USER_NOT_REGISTERED_GROUP'));

            return $result;
        } catch (Exception $e) {
            $result = new WsResponse(100, $e->getMessage());

            return $result;
        }
    }

    /**
     * assigns a user to a group
     *
     * @param string $userId
     * @param string $groupId
     * @return $result will return an object
     */
    public function assignUserToGroup($userId, $groupId)
    {
        try {
            global $RBAC;
            $RBAC->initRBAC();
            $user = $RBAC->verifyUserId($userId);

            if ($user == 0) {
                $result = new WsResponse(3, G::loadTranslation('ID_USER_NOT_REGISTERED_SYSTEM'));
                return $result;
            }

            $groups = new Groups();
            $very_group = $groups->verifyGroup($groupId);

            if ($very_group == 0) {
                $result = new WsResponse(9, G::loadTranslation('ID_GROUP_NOT_REGISTERED_SYSTEM'));

                return $result;
            }

            $very_user = $groups->verifyUsertoGroup($groupId, $userId);

            if ($very_user == 1) {
                $result = new WsResponse(8, G::loadTranslation('ID_USER_ALREADY_EXISTS_GROUP'));

                return $result;
            }

            $groups->addUserToGroup($groupId, $userId);
            $result = new WsResponse(0, G::loadTranslation('ID_COMMAND_EXECUTED_SUCCESSFULY'));

            return $result;
        } catch (Exception $e) {
            $result = new WsResponse(100, $e->getMessage());

            return $result;
        }
    }

    /**
     * assigns user to department
     *
     * @param string $userId
     * @param string $depId
     * @param string $manager
     * @return $result will return an object
     */
    public function assignUserToDepartment($userId, $depId, $manager)
    {
        try {
            global $RBAC;
            $RBAC->initRBAC();
            $user = $RBAC->verifyUserId($userId);

            if ($user == 0) {
                $result = new WsResponse(3, G::loadTranslation('ID_USER_NOT_REGISTERED_SYSTEM'));

                return $result;
            }

            $deps = new Department();

            if (!$deps->existsDepartment($depId)) {
                $data['DEP_ID'] = $depId;

                $result = new WsResponse(100, G::loadTranslation('ID_DEPARTMENT_NOT_REGISTERED_SYSTEM', SYS_LANG, $data));

                return $result;
            }

            if (!$deps->existsUserInDepartment($depId, $userId)) {
                $deps->addUserToDepartment($depId, $userId, $manager, true);
            }

            $result = new WsResponse(0, G::loadTranslation('ID_COMMAND_EXECUTED_SUCCESSFULY'));

            return $result;
        } catch (Exception $e) {
            $result = new WsResponse(100, $e->getMessage());

            return $result;
        }
    }

    /**
     * sends variables to a case
     *
     * @param string $caseId
     * @param string $variables
     * @return $result will return an object
     */
    public function sendVariables($caseId, $variables)
    {
        //delegation where app uid (caseId) y usruid(session) ordenar delindes descendente y agaarr el primero
        //delfinishdate != null error
        try {
            $oCriteria = new Criteria('workflow');
            $oCriteria->addSelectColumn(AppDelegationPeer::DEL_FINISH_DATE);
            $oCriteria->add(AppDelegationPeer::APP_UID, $caseId);
            $oCriteria->add(AppDelegationPeer::DEL_FINISH_DATE, null, Criteria::ISNULL);

            $oCriteria->addDescendingOrderByColumn(AppDelegationPeer::DEL_INDEX);
            $oDataset = AppDelegationPeer::doSelectRS($oCriteria);
            $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);

            $cnt = 0;

            while ($oDataset->next()) {
                $aRow = $oDataset->getRow();
                $cnt ++;
            }

            if ($cnt == 0) {
                $result = new WsResponse(18, G::loadTranslation('ID_CASE_DELEGATION_ALREADY_CLOSED'));

                return $result;
            }

            if (is_array($variables)) {
                $cant = count($variables);

                if ($cant > 0) {
                    $oCase = new Cases();
                    $oldFields = $oCase->loadCase($caseId);
                    $oldFields['APP_DATA'] = array_merge($oldFields['APP_DATA'], $variables);
                    ob_start();
                    print_r($variables);
                    $cdata = ob_get_contents();
                    ob_end_clean();
                    $up_case = $oCase->updateCase($caseId, $oldFields);

                    $result = new WsResponse(0, $cant . " " . G::loadTranslation('ID_VARIABLES_RECEIVED') . ": \n" . trim(str_replace('Array', '', $cdata)));

                    return $result;
                } else {
                    $result = new WsResponse(23, G::loadTranslation('ID_VARIABLES_PARAM_ZERO'));

                    return $result;
                }
            } else {
                $result = new WsResponse(24, G::loadTranslation('ID_VARIABLES_PARAM_NOT_ARRAY'));

                return $result;
            }
        } catch (Exception $e) {
            $result = new WsResponse(100, $e->getMessage());

            return $result;
        }
    }

    /**
     * get variables The variables can be system variables and/or case variables
     *
     * @param string $caseId
     * @param string $variables
     * @return $result will return an object
     */
    public function getVariables($caseId, $variables)
    {
        try {
            if (is_array($variables)) {
                $cant = count($variables);

                if ($cant > 0) {
                    $oCase = new Cases();

                    $caseFields = $oCase->loadCase($caseId);
                    $oldFields = $caseFields['APP_DATA'];
                    $resFields = [];

                    foreach ($variables as $key => $val) {
                        $a .= $val->name . ', ';

                        if (isset($oldFields[$val->name])) {
                            if (!is_array($oldFields[$val->name])) {
                                $node = new stdClass();
                                $node->name = $val->name;
                                $node->value = $oldFields[$val->name];
                                $resFields[] = $node;
                            } else {
                                foreach ($oldFields[$val->name] as $gridKey => $gridRow) {
                                    //Special Variables like grids or checkgroups
                                    if (is_array($gridRow)) {
                                        //Grids
                                        foreach ($gridRow as $col => $colValue) {
                                            $node = new stdClass();
                                            $node->name = $val->name . "][" . $gridKey . "][" . $col;
                                            $node->value = $colValue;
                                            $resFields[] = $node;
                                        }
                                    } else {
                                        //Checkgroups, Radiogroups
                                        $node = new stdClass();
                                        $node->name = $key;
                                        $node->value = implode("|", $val);
                                        $resFields[] = $node;
                                    }
                                }
                            }
                        }
                    }

                    $result = new WsGetVariableResponse(0, count($resFields) . G::loadTranslation('ID_VARIABLES_SENT'), $resFields);

                    return $result;
                } else {
                    $result = new WsGetVariableResponse(23, G::loadTranslation('ID_VARIABLES_PARAM_ZERO'), null);

                    return $result;
                }
            } else {
                $result = new WsGetVariableResponse(24, G::loadTranslation('ID_VARIABLES_PARAM_NOT_ARRAY'), null);
                return $result;
            }
        } catch (Exception $e) {
            $result = new WsGetVariableResponse(100, $e->getMessage(), null);

            return $result;
        }
    }

    /**
     * get all variables the system and case selected
     *
     * @param string $caseId
     *
     * @return $result will return an object
     */
    public function getVariablesNames($caseId)
    {
        try {
            $oCase = new Cases();

            $caseFields = $oCase->loadCase($caseId);

            $oldFields = $caseFields['APP_DATA'];
            $resFields = [];

            foreach ($oldFields as $key => $val) {
                $node = new stdClass();
                $node->name = $key;
                $resFields[] = $node;
            }

            $result = new WsGetVariableResponse(0, count($resFields) . G::loadTranslation('ID_VARIABLES_SENT'), $resFields);

            return $result;
        } catch (Exception $e) {
            $result = new WsGetVariableResponse(100, $e->getMessage(), null);

            return $result;
        }
    }

    /**
     * new Case begins a new case under the name of the logged-in user.
     *
     * @param string $processId
     * @param string $userId
     * @param string $taskId
     * @param string $variables
     * @param int $executeTriggers : Optional parameter. The execution all triggers of the task, according to your steps, 1 yes 0 no.
     * @param string $status
     * @return $result will return an object
     */
    public function newCase($processId, $userId, $taskId, $variables, $executeTriggers = 0, $status = 'DRAFT')
    {
        //$executeTriggers, this parameter is not important, it may be the last parameter in the method

        $g = new G();

        try {
            $g->sessionVarSave();

            $_SESSION["PROCESS"] = $processId;
            $_SESSION["TASK"] = $taskId;
            $_SESSION["USER_LOGGED"] = $userId;

            $Fields = [];

            if (is_array($variables) && count($variables) > 0) {
                $Fields = $variables;
            }

            $oProcesses = new Processes();
            $pro = $oProcesses->processExists($processId);

            if (!$pro) {
                $result = new WsResponse(11, G::LoadTranslation('ID_INVALID_PROCESS') . " " . $processId);

                $g->sessionVarRestore();

                return $result;
            }

            $oCase = new Cases();
            $startingTasks = $oCase->getStartCases($userId);
            array_shift($startingTasks); //remove the first row, the header row
            $founded = '';
            $tasksInThisProcess = 0;
            $validTaskId = $taskId;

            foreach ($startingTasks as $key => $val) {
                if ($val['pro_uid'] == $processId) {
                    $tasksInThisProcess ++;
                    $validTaskId = $val['uid'];
                }

                if ($val['uid'] == $taskId) {
                    $founded = $val['value'];
                }
            }

            if ($taskId == '') {
                if ($tasksInThisProcess == 1) {
                    $founded = $validTaskId;
                    $taskId = $validTaskId;
                }

                if ($tasksInThisProcess > 1) {
                    $result = new WsResponse(13, G::LoadTranslation('ID_MULTIPLE_STARTING_TASKS'));

                    $g->sessionVarRestore();

                    return $result;
                }
            }

            $task = TaskPeer::retrieveByPK($taskId);

            $arrayTaskTypeToExclude = array("START-TIMER-EVENT", "START-MESSAGE-EVENT");

            if (!is_null($task) && !in_array($task->getTasType(), $arrayTaskTypeToExclude) && $founded == "") {
                $result = new WsResponse(14, G::LoadTranslation('ID_TASK_INVALID_USER_NOT_ASSIGNED_TASK'));

                $g->sessionVarRestore();

                return $result;
            }

            //Start case
            $case = $oCase->startCase($taskId, $userId);

            $_SESSION['APPLICATION'] = $case['APPLICATION'];
            $_SESSION['PROCESS'] = $case['PROCESS'];
            $_SESSION['TASK'] = $taskId;
            $_SESSION['INDEX'] = $case['INDEX'];
            $_SESSION['USER_LOGGED'] = $userId;
            $_SESSION['USR_USERNAME'] = (isset($case['USR_USERNAME'])) ? $case['USR_USERNAME'] : '';
            $_SESSION['STEP_POSITION'] = 0;

            $caseId = $case['APPLICATION'];
            $caseNr = $case['CASE_NUMBER'];

            $oldFields = $oCase->loadCase($caseId);

            $oldFields['APP_DATA'] = array_merge($oldFields['APP_DATA'], $Fields);

            $oldFields['DEL_INDEX'] = $case['INDEX'];
            $oldFields['TAS_UID'] = $taskId;

            if (!is_null($status) && $status != 'DRAFT') {
                $oldFields['APP_STATUS'] = $status;
            }

            $up_case = $oCase->updateCase($caseId, $oldFields);

            //Execute all triggers of the task, according to your steps
            if ($executeTriggers == 1) {
                $task = new Tasks();
                $arrayStep = $task->getStepsOfTask($taskId);

                foreach ($arrayStep as $step) {
                    $arrayField = $oCase->loadCase($caseId);

                    $arrayField["APP_DATA"] = $oCase->executeTriggers($taskId, $step["STEP_TYPE_OBJ"], $step["STEP_UID_OBJ"], "BEFORE", $arrayField["APP_DATA"]);
                    $arrayField["APP_DATA"] = $oCase->executeTriggers($taskId, $step["STEP_TYPE_OBJ"], $step["STEP_UID_OBJ"], "AFTER", $arrayField["APP_DATA"]);

                    unset($arrayField['APP_STATUS']);
                    unset($arrayField['APP_PROC_STATUS']);
                    unset($arrayField['APP_PROC_CODE']);
                    unset($arrayField['APP_PIN']);
                    $arrayField = $oCase->updateCase($caseId, $arrayField);
                }
            }

            //Response
            $result = new WsResponse(0, G::LoadTranslation('ID_STARTED_SUCCESSFULLY'));
            $result->caseId = $caseId;
            $result->caseNumber = $caseNr;

            $g->sessionVarRestore();

            return $result;
        } catch (Exception $e) {
            $result = new WsResponse(100, $e->getMessage());

            $g->sessionVarRestore();

            return $result;
        }
    }

    /**
     * creates a new case impersonating a user who has the proper privileges to create new cases
     *
     * @param string $processId
     * @param string $userId
     * @param string $variables
     * @param string $taskId, must be in the starting group.
     * @return $result will return an object
     */
    public function newCaseImpersonate($processId, $userId, $variables, $taskId = '')
    {
        try {
            if (is_array($variables)) {
                if (count($variables) > 0) {
                    $c = count($variables);
                    $Fields = $variables;
                } else {
                    if ($c == 0) {
                        $result = new WsResponse(10, G::loadTranslation('ID_ARRAY_VARIABLES_EMPTY'));

                        return $result;
                    }
                }
            } else {
                $result = new WsResponse(10, G::loadTranslation('ID_VARIABLES_PARAM_NOT_ARRAY'));

                return $result;
            }

            $processes = new Processes();

            if (!$processes->processExists($processId)) {
                $result = new WsResponse(11, G::loadTranslation('ID_INVALID_PROCESS') . " " . $processId . "!!");

                return $result;
            }

            $user = new Users();

            if (!$user->userExists($userId)) {
                $result = new WsResponse(11, G::loadTranslation('ID_USER_NOT_REGISTERED') . " " . $userId . "!!");

                return $result;
            }

            $oCase = new Cases();

            $numTasks = 0;
            if ($taskId != '') {
                $aTasks = $processes->getStartingTaskForUser($processId, null);
                foreach ($aTasks as $task) {
                    if ($task['TAS_UID'] == $taskId) {
                        $arrayTask[0]['TAS_UID'] = $taskId;
                        $numTasks = 1;
                    }
                }
            } else {
                $arrayTask = $processes->getStartingTaskForUser($processId, null);
                $numTasks = count($arrayTask);
            }

            if ($numTasks == 1) {
                $case = $oCase->startCase($arrayTask[0]['TAS_UID'], $userId);
                $caseId = $case['APPLICATION'];
                $caseNumber = $case['CASE_NUMBER'];

                $oldFields = $oCase->loadCase($caseId);

                $oldFields['APP_DATA'] = array_merge($oldFields['APP_DATA'], $Fields);

                $up_case = $oCase->updateCase($caseId, $oldFields);

                $result = new WsResponse(0, G::loadTranslation('ID_COMMAND_EXECUTED_SUCCESSFULLY'));

                $result->caseId = $caseId;
                $result->caseNumber = $caseNumber;

                return $result;
            } else {
                if ($numTasks == 0) {
                    $result = new WsResponse(12, G::loadTranslation('ID_NO_STARTING_TASK'));

                    return $result;
                }

                if ($numTasks > 1) {
                    $result = new WsResponse(13, G::loadTranslation('ID_MULTIPLE_STARTING_TASKS'));

                    return $result;
                }
            }
        } catch (Exception $e) {
            $result = new WsResponse(100, $e->getMessage());

            return $result;
        }
    }

    /**
     * Execute the trigger defined in the steps
     * This function is used when the case is derived from abe, Soap, PMFDerivateCase
     *
     * @param string $caseId, Uid related to the case
     * @param array $appData, contain all the information about the case
     * @param string $tasUid, Uid related to the task
     * @param string $stepType, before or after step
     * @param string $stepUidObj, can be -1, -2
     * @param string $triggerType, can be BEFORE, AFTER
     * @param string $labelAssigment, label related to the triggerType
     */
    public function executeTriggerFromDerivate(
    $caseId, &$appData, $tasUid, $stepType, $stepUidObj, $triggerType, $labelAssigment = ''
    ) {
        $varTriggers = "";
        $oCase = new Cases();

        //Execute triggers before assignment
        $aTriggers = $oCase->loadTriggers($tasUid, $stepType, $stepUidObj, $triggerType);

        if (count($aTriggers) > 0) {
            $varTriggers = $varTriggers . "<br /><b>" . $labelAssigment . "</b><br />";

            $oPMScript = new PMScript();

            foreach ($aTriggers as $aTrigger) {
                //Set variables
                $params = new stdClass();
                $params->appData = $appData;

                if ($this->stored_system_variables) {
                    $params->option = "STORED SESSION";
                    $params->SID = $this->wsSessionId;
                }

                $appFields["APP_DATA"] = array_merge($appData, G::getSystemConstants($params));

                //PMScript
                $oPMScript->setFields($appFields['APP_DATA']);
                $bExecute = true;

                if ($aTrigger['ST_CONDITION'] !== '') {
                    $oPMScript->setScript($aTrigger['ST_CONDITION']);
                    $bExecute = $oPMScript->evaluate();
                }

                if ($bExecute) {
                    $oPMScript->setDataTrigger($aTrigger);
                    $oPMScript->setScript($aTrigger['TRI_WEBBOT']);
                    $oPMScript->execute();

                    $trigger = TriggersPeer::retrieveByPk($aTrigger["TRI_UID"]);
                    $varTriggers = $varTriggers . "&nbsp;- " . nl2br(htmlentities($trigger->getTriTitle(), ENT_QUOTES)) . "<br />";

                    $appFields['APP_DATA'] = $oPMScript->aFields;
                    unset($appFields['APP_STATUS']);
                    unset($appFields['APP_PROC_STATUS']);
                    unset($appFields['APP_PROC_CODE']);
                    unset($appFields['APP_PIN']);
                    $oCase->updateCase($caseId, $appFields);

                    //We need to update the variable $appData for use the new variables in the next trigger
                    $appData = array_merge($appData, $appFields['APP_DATA']);
                }
            }
        }
        return $varTriggers;
    }

    /**
     * Derivate Case moves the case to the next task in the process according to the routing rules
     * This function is used from: action by email, web entry, PMFDerivateCase, Mobile
     * @param string $userId
     * @param string $caseId
     * @param string $delIndex
     * @param array $tasks
     * @param bool   $bExecuteTriggersBeforeAssignment
     * @return $result will return an object
     */
    public function derivateCase($userId, $caseId, $delIndex, $bExecuteTriggersBeforeAssignment = false, $tasks = [])
    {
        $g = new G();

        try {
            $g->sessionVarSave();

            $_SESSION["APPLICATION"] = $caseId;
            $_SESSION["INDEX"] = $delIndex;
            $_SESSION["USER_LOGGED"] = $userId;

            //Define variables
            $sStatus = 'TO_DO';
            $varResponse = '';
            $previousAppData = [];

            if ($delIndex == '') {
                $oCriteria = new Criteria('workflow');
                $oCriteria->addSelectColumn(AppDelegationPeer::DEL_INDEX);
                $oCriteria->add(AppDelegationPeer::APP_UID, $caseId);
                $oCriteria->add(AppDelegationPeer::DEL_FINISH_DATE, null, Criteria::ISNULL);

                if (AppDelegationPeer::doCount($oCriteria) > 1) {
                    $result = new WsResponse(20, G::LoadTranslation('ID_SPECIFY_DELEGATION_INDEX'));
                    return $result;
                }

                $oDataset = AppDelegationPeer::doSelectRS($oCriteria);
                $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
                $oDataset->next();
                $aRow = $oDataset->getRow();
                $delIndex = $aRow['DEL_INDEX'];
            }

            $oAppDel = new AppDelegation();
            $appdel = $oAppDel->Load($caseId, $delIndex);

            if ($userId != $appdel['USR_UID']) {
                $result = new WsResponse(17, G::LoadTranslation('ID_CASE_ASSIGNED_ANOTHER_USER'));

                return $result;
            }

            if ($appdel['DEL_FINISH_DATE'] != null) {
                $result = new WsResponse(18, G::LoadTranslation('ID_CASE_DELEGATION_ALREADY_CLOSED'));

                return $result;
            }

            //Validate if the case is paused or cancelled
            $oAppDelay = new AppDelay();
            $aRow = $oAppDelay->getCasesCancelOrPaused($caseId);
            if (is_array($aRow)) {
                if (isset($aRow['APP_DISABLE_ACTION_USER']) && $aRow['APP_DISABLE_ACTION_USER'] != 0 && isset($aRow['APP_DISABLE_ACTION_DATE']) && $aRow['APP_DISABLE_ACTION_DATE'] != '') {
                    $result = new WsResponse(19, G::LoadTranslation('ID_CASE_IN_STATUS') . " " . $aRow['APP_TYPE']);

                    return $result;
                }
            }

            $aData = [];
            $aData['APP_UID'] = $caseId;
            $aData['DEL_INDEX'] = $delIndex;
            $aData['USER_UID'] = $userId;

            //Load data
            $oCase = new Cases();
            $appFields = $oCase->loadCase($caseId, $delIndex);

            if (is_null($appFields["DEL_INIT_DATE"])) {
                $oCase->setDelInitDate($caseId, $delIndex);
                $appFields = $oCase->loadCase($caseId, $delIndex);
            }
            unset($appFields['APP_ROUTING_DATA']);

            $appFields["APP_DATA"]["APPLICATION"] = $caseId;

            if (!isset($_SESSION["PROCESS"])) {
                $_SESSION["PROCESS"] = $appFields["PRO_UID"];
            }

            global $oPMScript;

            if (isset($oPMScript->aFields['APPLICATION']) && ($oPMScript->aFields['APPLICATION'] != $caseId)) {
                $previousAppData = $oPMScript->aFields;
            }

            $varTriggers = "\n";
            //Execute triggers before assignment
            if ($bExecuteTriggersBeforeAssignment) {
                $varTriggers .= $this->executeTriggerFromDerivate($caseId, $appFields["APP_DATA"], $appdel['TAS_UID'], 'ASSIGN_TASK', -1, 'BEFORE', "-= Before Assignment =-");
            }

            //Execute triggers before routing
            $varTriggers .= $this->executeTriggerFromDerivate($caseId, $appFields["APP_DATA"], $appdel['TAS_UID'], 'ASSIGN_TASK', -2, 'BEFORE', "-= Before Derivation =-");

            $oDerivation = new Derivation();
            if (!empty($tasks)) {
                $nextDelegations = $tasks;
            } else {
                $derive = $oDerivation->prepareInformation($aData);

                if (isset($derive[1])) {
                    if ($derive[1]['ROU_TYPE'] == 'SELECT') {
                        $result = new WsResponse(21, G::LoadTranslation('ID_CAN_NOT_ROUTE_CASE_USING_WEBSERVICES'));

                        return $result;
                    }
                } else {
                    $result = new WsResponse(22, G::LoadTranslation('ID_TASK_DOES_NOT_HAVE_ROUTING_RULE'));

                    return $result;
                }

                foreach ($derive as $key => $val) {
                    //Routed to the next task, if end process then not exist user
                    $nodeNext = [];
                    $usrasgdUid = null;
                    $usrasgdUserName = null;

                    if (isset($val['NEXT_TASK']['USER_ASSIGNED'])) {
                        $usrasgdUid = '';
                        if (isset($val['NEXT_TASK']['USER_ASSIGNED']['USR_UID'])) {
                            $usrasgdUid = $val['NEXT_TASK']['USER_ASSIGNED']['USR_UID'];
                        }
                        if (isset($val['NEXT_TASK']['USER_ASSIGNED']['USR_USERNAME'])) {
                            $usrasgdUserName = '(' . $val['NEXT_TASK']['USER_ASSIGNED']['USR_USERNAME'] . ')';
                        } else {
                            $usrasgdUserName = '';
                        }
                    }

                    $nodeNext['TAS_UID'] = $val['NEXT_TASK']['TAS_UID'];
                    $nodeNext['USR_UID'] = $usrasgdUid;
                    $nodeNext['TAS_ASSIGN_TYPE'] = $val['NEXT_TASK']['TAS_ASSIGN_TYPE'];
                    $nodeNext['TAS_DEF_PROC_CODE'] = $val['NEXT_TASK']['TAS_DEF_PROC_CODE'];
                    $nodeNext['DEL_PRIORITY'] = $appdel['DEL_PRIORITY'];
                    $nodeNext['TAS_PARENT'] = $val['NEXT_TASK']['TAS_PARENT'];
                    $nodeNext['ROU_PREVIOUS_TYPE'] = (isset($val['NEXT_TASK']['ROU_PREVIOUS_TYPE'])) ? $val['NEXT_TASK']['ROU_PREVIOUS_TYPE'] : '';
                    $nodeNext['ROU_PREVIOUS_TASK'] = (isset($val['NEXT_TASK']['ROU_PREVIOUS_TASK'])) ? $val['NEXT_TASK']['ROU_PREVIOUS_TASK'] : '';
                    $nextDelegations[] = $nodeNext;
                    $varResponse = $varResponse . (($varResponse != '') ? ',' : '') . $val['NEXT_TASK']['TAS_TITLE'] . $usrasgdUserName;
                }
            }
            $appFields['DEL_INDEX'] = $delIndex;

            if (isset($derive['TAS_UID'])) {
                $appFields['TAS_UID'] = $derive['TAS_UID'];
            }

            //Get from the route information the nextTask
            $nextTaskUid = $appdel['TAS_UID'];
            $nextRouteType = '';
            do {
                $oRoute = new \Route();
                $nextRouteTask = $oRoute->getNextRouteByTask($nextTaskUid);
                $prefix = '';
                if (isset($nextRouteTask['ROU_NEXT_TASK'])) {
                    $nextTaskUid = $nextRouteTask['ROU_NEXT_TASK'];
                    $nextRouteType = $nextRouteTask['ROU_TYPE'];
                    $prefix = substr($nextTaskUid, 0, 4);
                }
            } while ($prefix == 'gtg-');

            $aCurrentDerivation = array(
                'APP_UID' => $caseId,
                'DEL_INDEX' => $delIndex,
                'APP_STATUS' => $sStatus,
                'TAS_UID' => $appdel['TAS_UID'],
                'ROU_TYPE' => $nextRouteType
            );

            //We define some parameters in the before the derivation
            //Then this function will be route the case
            $oDerivation->beforeDerivate(
                    $aData, $nextDelegations, $nextRouteType, $aCurrentDerivation
            );

            //Execute triggers after routing
            $varTriggers .= $this->executeTriggerFromDerivate($caseId, $appFields["APP_DATA"], $appdel['TAS_UID'], 'ASSIGN_TASK', -2, 'AFTER', "-= After Derivation =-");

            $sFromName = "";

            if ($userId != "") {
                $user = new Users();
                $arrayUserData = $user->load($userId);

                if (trim($arrayUserData["USR_EMAIL"]) == "") {
                    $arrayUserData["USR_EMAIL"] = "info@" . $_SERVER["HTTP_HOST"];
                }

                $sFromName = "\"" . $arrayUserData["USR_FIRSTNAME"] . " " . $arrayUserData["USR_LASTNAME"] . "\" <" . $arrayUserData["USR_EMAIL"] . ">";
            }

            $oCase->sendNotifications($appdel['TAS_UID'], $nextDelegations, $appFields['APP_DATA'], $caseId, $delIndex, $sFromName);

            $oProcess = new Process();
            $oProcessFieds = $oProcess->Load($appFields['PRO_UID']);
            //here debug mode in web entry


            if (isset($oProcessFieds['PRO_DEBUG']) && $oProcessFieds['PRO_DEBUG']) {
                $result = new WsResponse(0, $varResponse . "
                        <br><br><table width='100%' cellpadding='0' cellspacing='0'><tr><td class='FormTitle'>
                    " . G::LoadTranslation('ID_DEBUG_MESSAGE') . "</td></tr></table>" . $varTriggers);
            } else {
                $result = new WsResponse(0, $varResponse . " --- " . $oProcessFieds['PRO_DEBUG']);
            }

            $res = $result->getPayloadArray();

            //Now fill the array of AppDelegationPeer
            $oCriteria = new Criteria('workflow');
            $oCriteria->addSelectColumn(AppDelegationPeer::DEL_INDEX);
            $oCriteria->addSelectColumn(AppDelegationPeer::USR_UID);
            $oCriteria->addSelectColumn(AppDelegationPeer::TAS_UID);
            $oCriteria->addSelectColumn(AppDelegationPeer::DEL_THREAD);
            $oCriteria->addSelectColumn(AppDelegationPeer::DEL_THREAD_STATUS);
            $oCriteria->addSelectColumn(AppDelegationPeer::DEL_FINISH_DATE);
            $oCriteria->add(AppDelegationPeer::APP_UID, $caseId);
            $oCriteria->add(AppDelegationPeer::DEL_PREVIOUS, $delIndex);
            $oCriteria->addAscendingOrderByColumn(AppDelegationPeer::DEL_INDEX);
            $oDataset = AppDelegationPeer::doSelectRS($oCriteria);
            $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);

            $aCurrentUsers = [];

            while ($oDataset->next()) {
                $aAppDel = $oDataset->getRow();
                $oUser = new Users();

                try {
                    $oUser->load($aAppDel['USR_UID']);
                    $uFields = $oUser->toArray(BasePeer::TYPE_FIELDNAME);
                    $currentUserName = $oUser->getUsrFirstname() . ' ' . $oUser->getUsrLastname();
                } catch (Exception $e) {
                    $currentUserName = '';
                }

                $oTask = new Task();

                try {
                    $uFields = $oTask->load($aAppDel['TAS_UID']);
                    $taskName = $uFields['TAS_TITLE'];
                } catch (Exception $e) {
                    $taskName = '';
                }

                //Execute events
                $eventPro = $appFields['PRO_UID'];
                $eventApp = $caseId;
                $eventInd = $aAppDel['DEL_INDEX'];
                $eventTas = $aAppDel['TAS_UID'];

                $oEvent = new Event();
                $oEvent->createAppEvents($eventPro, $eventApp, $eventInd, $eventTas);
                //End events

                $currentUser = new stdClass();
                $currentUser->userId = $aAppDel['USR_UID'];
                $currentUser->userName = $currentUserName;
                $currentUser->taskId = $aAppDel['TAS_UID'];
                $currentUser->taskName = $taskName;
                $currentUser->delIndex = $aAppDel['DEL_INDEX'];
                $currentUser->delThread = $aAppDel['DEL_THREAD'];
                $currentUser->delThreadStatus = $aAppDel['DEL_THREAD_STATUS'];
                $aCurrentUsers[] = $currentUser;
            }

            $res['routing'] = $aCurrentUsers;

            $g->sessionVarRestore();

            if (!empty($previousAppData)) {
                $oPMScript->aFields = $previousAppData;
            }

            return $res;
        } catch (Exception $e) {
            $result = new WsResponse(100, $e->getMessage());

            $g->sessionVarRestore();

            return $result;
        }
    }

    /**
     * execute Trigger, executes a ProcessMaker trigger.
     * Note that triggers which are tied to case derivation
     * will executing automatically.
     *
     * @param string $userId
     * @param string $caseId
     * @param string $delIndex
     * @return $result will return an object
     */
    public function executeTrigger($userId, $caseId, $triggerIndex, $delIndex)
    {
        $g = new G();

        try {
            $g->sessionVarSave();

            $_SESSION["APPLICATION"] = $caseId;
            $_SESSION["INDEX"] = $delIndex;
            $_SESSION["USER_LOGGED"] = $userId;

            $oAppDel = new AppDelegation();
            $appdel = $oAppDel->Load($caseId, $delIndex);

            if ($userId != $appdel['USR_UID']) {
                $result = new WsResponse(17, G::loadTranslation('ID_CASE_ASSIGNED_ANOTHER_USER'));

                $g->sessionVarRestore();

                return $result;
            }

            if ($appdel['DEL_FINISH_DATE'] != null) {
                $result = new WsResponse(18, G::loadTranslation('ID_CASE_DELEGATION_ALREADY_CLOSED'));

                $g->sessionVarRestore();

                return $result;
            }

            $oCriteria = new Criteria('workflow');
            $oCriteria->addSelectColumn(AppDelayPeer::APP_UID);
            $oCriteria->addSelectColumn(AppDelayPeer::APP_DEL_INDEX);
            $oCriteria->addSelectColumn(AppDelayPeer::APP_TYPE);
            $oCriteria->addSelectColumn(AppDelayPeer::APP_DISABLE_ACTION_USER);
            $oCriteria->addSelectColumn(AppDelayPeer::APP_DISABLE_ACTION_DATE);
            $oCriteria->add(AppDelayPeer::APP_UID, $caseId);
            $oCriteria->add($oCriteria->getNewCriterion(AppDelayPeer::APP_TYPE, 'PAUSE')->addOr($oCriteria->getNewCriterion(AppDelayPeer::APP_TYPE, 'CANCEL')));
            $oCriteria->addAscendingOrderByColumn(AppDelayPeer::APP_ENABLE_ACTION_DATE);
            $oDataset = AppDelayPeer::doSelectRS($oCriteria);
            $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $oDataset->next();
            $aRow = $oDataset->getRow();

            if (is_array($aRow)) {
                if ($aRow['APP_DISABLE_ACTION_USER'] == 0 || is_null($aRow['APP_DISABLE_ACTION_DATE'])) {
                    $result = new WsResponse(19, G::loadTranslation('ID_CASE_IN_STATUS') . " " . $aRow['APP_TYPE']);

                    $g->sessionVarRestore();

                    return $result;
                }
            }

            //Load data
            $oCase = new Cases();
            $appFields = $oCase->loadCase($caseId);

            $appFields["APP_DATA"]["APPLICATION"] = $caseId;

            if (!isset($_SESSION["PROCESS"])) {
                $_SESSION["PROCESS"] = $appFields["PRO_UID"];
            }

            //executeTrigger
            $aTriggers = [];
            $c = new Criteria();
            $c->add(TriggersPeer::TRI_UID, $triggerIndex);
            $rs = TriggersPeer::doSelectRS($c);
            $rs->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $rs->next();
            $row = $rs->getRow();

            if (is_array($row) && $row['TRI_TYPE'] == 'SCRIPT') {
                $aTriggers[] = $row;

                $oPMScript = new PMScript();
                $oPMScript->setDataTrigger($row);
                $oPMScript->setFields($appFields['APP_DATA']);
                $oPMScript->setScript($row['TRI_WEBBOT']);
                $oPMScript->execute();

                if (isset($oPMScript->aFields["__ERROR__"]) && trim($oPMScript->aFields["__ERROR__"]) != "" && $oPMScript->aFields["__ERROR__"] != "none") {
                    throw new Exception($oPMScript->aFields["__ERROR__"]);
                }

                //Save data - Start
                $appFields['APP_DATA'] = $oPMScript->aFields;
                unset($appFields['APP_STATUS']);
                unset($appFields['APP_PROC_STATUS']);
                unset($appFields['APP_PROC_CODE']);
                unset($appFields['APP_PIN']);
                $oCase->updateCase($caseId, $appFields);
                //Save data - End
            } else {
                $data['TRIGGER_INDEX'] = $triggerIndex;
                $result = new WsResponse(100, G::loadTranslation('ID_INVALID_TRIGGER', SYS_LANG, $data));

                $g->sessionVarRestore();

                return $result;
            }

            $result = new WsResponse(0, G::loadTranslation('ID_EXECUTED') . ": " . trim($row['TRI_WEBBOT']));

            $g->sessionVarRestore();

            return $result;
        } catch (Exception $e) {
            $result = new WsResponse(100, $e->getMessage());

            $g->sessionVarRestore();

            return $result;
        }
    }

    /**
     * task Case
     *
     * @param string sessionId : The session ID which is obtained when logging in
     * @param string caseId : The case ID. The caseList() function can be used to find the ID number for cases
     * @return $result returns the current task for a given case. Note that the logged-in user must have privileges
     * to access the task
     */
    public function taskCase($caseId)
    {
        $result = [];
        try {
            $oCriteria = new Criteria('workflow');
            $oCriteria->addSelectColumn(AppDelegationPeer::DEL_INDEX);
            $oCriteria->addSelectColumn(AppDelegationPeer::TAS_UID);
            $oCriteria->addSelectColumn(TaskPeer::TAS_TITLE);
            $oCriteria->addJoin(AppDelegationPeer::TAS_UID, TaskPeer::TAS_UID);
            $oCriteria->add(AppDelegationPeer::APP_UID, $caseId);
            $oCriteria->add(AppDelegationPeer::DEL_THREAD_STATUS, 'OPEN');
            $oCriteria->add(AppDelegationPeer::DEL_FINISH_DATE, null, Criteria::ISNULL);
            $oDataset = AppDelegationPeer::doSelectRS($oCriteria);
            $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $oDataset->next();

            while ($aRow = $oDataset->getRow()) {
                $result[] = array(
                    'guid' => $aRow['TAS_UID'],
                    'name' => $aRow['TAS_TITLE'],
                    'delegate' => $aRow['DEL_INDEX']
                );
                $oDataset->next();
            }

            return $result;
        } catch (Exception $e) {
            $result[] = array('guid' => $e->getMessage(), 'name' => $e->getMessage(), 'delegate' => $e->getMessage());

            return $result;
        }
    }

    /**
     * process list verified
     *
     * @param string sessionId : The session ID which is obtained when logging in
     * @param string userId :
     * @return $result will return an object
     */
    public function processListVerified($userId)
    {
        try {
            $oCase = new Cases();
            $rows = $oCase->getStartCases($userId);
            $result = [];

            foreach ($rows as $key => $val) {
                if ($key != 0) {
                    $result[] = array('guid' => $val['pro_uid'], 'name' => $val['value']
                    );
                }
            }

            return $result;
        } catch (Exception $e) {
            $result[] = array('guid' => $e->getMessage(), 'name' => $e->getMessage()
            );

            return $result;
        }
    }

    /**
     * reassign Case
     *
     * @param string sessionId : The session ID (which was obtained during login)
     * @param string caseId : The case ID (which can be obtained with the caseList() function)
     * @param string delIndex : The delegation index number of the case (which can be obtained with the caseList()
     * function).
     * @param string userIdSource : The user who is currently assigned the case.
     * @param string userIdTarget : The target user who will be newly assigned to the case.
     * @return $result will return an object
     */
    public function reassignCase($sessionId, $caseId, $delIndex, $userIdSource, $userIdTarget)
    {
        $g = new G();

        try {
            $g->sessionVarSave();

            $_SESSION["APPLICATION"] = $caseId;
            $_SESSION["INDEX"] = $delIndex;
            $_SESSION["USER_LOGGED"] = $userIdSource;

            if ($userIdTarget == $userIdSource) {
                $result = new WsResponse(30, G::loadTranslation('ID_TARGET_ORIGIN_USER_SAME'));

                $g->sessionVarRestore();

                return $result;
            }

            /**
             * ****************( 1 )*****************
             */
            $oCriteria = new Criteria('workflow');
            $oCriteria->add(UsersPeer::USR_STATUS, 'ACTIVE');
            $oCriteria->add(UsersPeer::USR_UID, $userIdSource);
            $oDataset = UsersPeer::doSelectRS($oCriteria);
            $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $oDataset->next();
            $aRow = $oDataset->getRow();

            if (!is_array($aRow)) {
                $result = new WsResponse(31, G::loadTranslation('ID_INVALID_ORIGIN_USER'));

                $g->sessionVarRestore();

                return $result;
            }

            /**
             * ****************( 2 )*****************
             */
            $oCase = new Cases();
            $rows = $oCase->loadCase($caseId);

            if (!is_array($aRow)) {
                $result = new WsResponse(32, G::loadTranslation('ID_CASE_NOT_OPEN'));

                $g->sessionVarRestore();

                return $result;
            }

            /**
             * ****************( 3 )*****************
             */
            $oCriteria = new Criteria('workflow');
            $aConditions = [];
            $oCriteria->add(AppDelegationPeer::APP_UID, $caseId);
            $oCriteria->add(AppDelegationPeer::USR_UID, $userIdSource);
            $oCriteria->add(AppDelegationPeer::DEL_INDEX, $delIndex);
            $oCriteria->add(AppDelegationPeer::DEL_FINISH_DATE, null, Criteria::ISNULL);
            $oDataset = AppDelegationPeer::doSelectRS($oCriteria);
            $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $oDataset->next();
            $aRow = $oDataset->getRow();

            if (!is_array($aRow)) {
                $result = new WsResponse(33, G::loadTranslation('ID_INVALID_CASE_DELEGATION_INDEX'));

                $g->sessionVarRestore();

                return $result;
            }

            $tasUid = $aRow['TAS_UID'];
            $derivation = new Derivation();
            $userList = $derivation->getAllUsersFromAnyTask($tasUid, true);

            if (!in_array($userIdTarget, $userList)) {
                $result = new WsResponse(34, G::loadTranslation('ID_TARGET_USER_DOES_NOT_HAVE_RIGHTS'));

                $g->sessionVarRestore();

                return $result;
            }

            /**
             * ****************( 4 )*****************
             */
            $oCriteria = new Criteria('workflow');
            $oCriteria->add(UsersPeer::USR_STATUS, 'ACTIVE');
            $oCriteria->add(UsersPeer::USR_UID, $userIdTarget);
            $oDataset = UsersPeer::doSelectRS($oCriteria);
            $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $oDataset->next();
            $aRow = $oDataset->getRow();

            if (!is_array($aRow)) {
                $result = new WsResponse(35, G::loadTranslation('ID_TARGET_USER_DESTINATION_INVALID'));

                $g->sessionVarRestore();

                return $result;
            }

            /**
             * ****************( 5 )*****************
             */
            $var = $oCase->reassignCase($caseId, $delIndex, $userIdSource, $userIdTarget);

            if (!$var) {
                $result = new WsResponse(36, G::loadTranslation('ID_CASE_COULD_NOT_REASSIGNED'));

                $g->sessionVarRestore();

                return $result;
            }

            $result = new WsResponse(0, G::loadTranslation('ID_COMMAND_EXECUTED_SUCCESSFULLY'));

            $g->sessionVarRestore();

            return $result;
        } catch (Exception $e) {
            $result[] = array('guid' => $e->getMessage(), 'name' => $e->getMessage()
            );

            $g->sessionVarRestore();

            return $result;
        }
    }

    /**
     * get system information
     *
     * @param string sessionId : The session ID (which was obtained at login)
     * @return $eturns information about the WAMP/LAMP stack, the workspace database, the IP number and version
     * of ProcessMaker, and the IP number and version of web browser of the user
     */
    public function systemInformation()
    {
        try {
            define('SKIP_RENDER_SYSTEM_INFORMATION', true);

            require_once(PATH_METHODS . 'login' . PATH_SEP . 'dbInfo.php');

            $result->status_code = 0;
            $result->message = G::loadTranslation('ID_SUCESSFUL');
            $result->timestamp = date('Y-m-d H:i:s');
            $result->version = System::getVersion();
            $result->operatingSystem = $redhat;
            $result->webServer = getenv('SERVER_SOFTWARE');
            $result->serverName = getenv('SERVER_NAME');
            $result->serverIp = $Fields['IP']; //lookup ($ip);
            $result->phpVersion = phpversion();
            $result->databaseVersion = $Fields['DATABASE'];
            $result->databaseServerIp = $Fields['DATABASE_SERVER'];
            $result->databaseName = $Fields['DATABASE_NAME'];
            $result->availableDatabases = $Fields['AVAILABLE_DB'];
            $result->userBrowser = $Fields['HTTP_USER_AGENT'];
            $result->userIp = $Fields['IP'];

            return $result;
        } catch (Exception $e) {
            $result = new WsResponse(100, $e->getMessage());

            return $result;
        }
    }

    /**
     * import process fromLibrary: downloads and imports a process from the ProcessMaker library
     *
     * @param string sessionId : The session ID (which was obtained at login).
     * @param string processId :
     * @param string version :
     * @param string importOption :
     * @param string usernameLibrary : The username to obtain access to the ProcessMaker library.
     * @param string passwordLibrary : The password to obtain access to the ProcessMaker library.
     * @return $eturns will return an object
     */
    public function getCaseNotes($applicationID, $userUid = '')
    {
        try {
            $result = new wsGetCaseNotesResponse(0, G::loadTranslation('ID_SUCCESS'), Cases::getCaseNotes($applicationID, 'array', $userUid));

            $var = [];

            foreach ($result->notes as $key => $value) {
                $var2 = [];

                foreach ($value as $keys => $values) {
                    $field = strtolower($keys);
                    $var2[$field] = $values;
                }

                $var[] = $var2;
            }

            $result->notes = $var;

            return $result;
        } catch (Exception $e) {
            $result = new WsResponse(100, $e->getMessage());

            return $result;
        }
    }

    /**
     * Delete case
     *
     * @param string caseUid : ID of the case.
     * @return $result will return an object
     */
    public function deleteCase($caseUid)
    {
        $g = new G();

        try {
            $g->sessionVarSave();

            $_SESSION["APPLICATION"] = $caseUid;

            if (empty($caseUid)) {
                $result = new WsResponse(100, G::LoadTranslation("ID_REQUIRED_FIELD") . " caseUid");

                $g->sessionVarRestore();

                return $result;
            }

            $case = new Cases();
            $case->removeCase($caseUid);

            //Response
            $res = new WsResponse(0, G::LoadTranslation("ID_COMMAND_EXECUTED_SUCCESSFULLY"));

            $result = array("status_code" => $res->status_code, "message" => $res->message, "timestamp" => $res->timestamp
            );

            $g->sessionVarRestore();

            return $result;
        } catch (Exception $e) {
            $result = new WsResponse(100, $e->getMessage());

            $g->sessionVarRestore();

            return $result;
        }
    }

    /**
     * Cancel case
     *
     * @param string caseUid : ID of the case.
     * @param int delIndex : Delegation index of the case.
     * @param string userUid : The unique ID of the user who will cancel the case.
     * @return $result will return an object
     */
    public function cancelCase($caseUid, $delIndex, $userUid)
    {
        $g = new G();

        try {
            $g->sessionVarSave();

            $_SESSION["APPLICATION"] = $caseUid;
            $_SESSION["INDEX"] = $delIndex;
            $_SESSION["USER_LOGGED"] = $userUid;

            if (empty($caseUid)) {
                $result = new WsResponse(100, G::LoadTranslation("ID_REQUIRED_FIELD") . " caseUid");

                $g->sessionVarRestore();

                return $result;
            }

            if (empty($delIndex)) {
                $result = new WsResponse(100, G::LoadTranslation("ID_REQUIRED_FIELD") . " delIndex");

                $g->sessionVarRestore();

                return $result;
            }

            if (empty($userUid)) {
                $result = new WsResponse(100, G::LoadTranslation("ID_REQUIRED_FIELD") . " userUid");

                $g->sessionVarRestore();

                return $result;
            }

            $oApplication = new Application();
            $aFields = $oApplication->load($caseUid);
            if ($aFields['APP_STATUS'] == 'DRAFT') {
                $result = new WsResponse(100, G::LoadTranslation("ID_CASE_IN_STATUS") . " DRAFT");
                $g->sessionVarRestore();
                return $result;
            }
            $oAppThread = new AppThread();
            $cant = $oAppThread->countStatus($caseUid, 'OPEN');
            if ($cant > 1) {
                $result = new WsResponse(100, G::LoadTranslation("ID_CASE_CANCELLED_PARALLEL"));
                $g->sessionVarRestore();
                return $result;
            }

            $case = new Cases();
            $case->cancelCase($caseUid, $delIndex, $userUid);

            //Response
            $res = new WsResponse(0, G::LoadTranslation("ID_COMMAND_EXECUTED_SUCCESSFULLY"));

            $result = array("status_code" => $res->status_code, "message" => $res->message, "timestamp" => $res->timestamp
            );

            $g->sessionVarRestore();

            return $result;
        } catch (Exception $e) {
            $result = new WsResponse(100, $e->getMessage());

            $g->sessionVarRestore();

            return $result;
        }
    }

    /**
     * Pause case
     *
     * @param string caseUid : ID of the case.
     * @param int delIndex : Delegation index of the case.
     * @param string userUid : The unique ID of the user who will pause the case.
     * @param string unpauseDate : Optional parameter. The date in the format "yyyy-mm-dd" indicating when to unpause
     * the case.
     * @return $result will return an object
     */
    public function pauseCase($caseUid, $delIndex, $userUid, $unpauseDate = null)
    {
        $g = new G();

        try {
            $g->sessionVarSave();

            $_SESSION["APPLICATION"] = $caseUid;
            $_SESSION["INDEX"] = $delIndex;
            $_SESSION["USER_LOGGED"] = $userUid;

            if (empty($caseUid)) {
                $result = new WsResponse(100, G::LoadTranslation("ID_REQUIRED_FIELD") . " caseUid");

                $g->sessionVarRestore();

                return $result;
            }

            if (empty($delIndex)) {
                $result = new WsResponse(100, G::LoadTranslation("ID_REQUIRED_FIELD") . " delIndex");

                $g->sessionVarRestore();

                return $result;
            }
            if (empty($userUid)) {
                $result = new WsResponse(100, G::LoadTranslation("ID_REQUIRED_FIELD") . " userUid");

                $g->sessionVarRestore();

                return $result;
            }
            if (strlen($unpauseDate) >= 10) {
                if (!preg_match("/^\d{4}-\d{2}-\d{2}| \d{2}:\d{2}:\d{2}$/", $unpauseDate)) {
                    $result = new WsResponse(100, G::LoadTranslation("ID_INVALID_DATA") . " $unpauseDate");

                    $g->sessionVarRestore();

                    return $result;
                }
            } else {
                $unpauseDate = null;
            }
            $case = new Cases();
            $case->pauseCase($caseUid, $delIndex, $userUid, $unpauseDate);

            //Response
            $res = new WsResponse(0, G::LoadTranslation("ID_COMMAND_EXECUTED_SUCCESSFULLY"));

            $result = array("status_code" => $res->status_code, "message" => $res->message, "timestamp" => $res->timestamp
            );

            $g->sessionVarRestore();
            return $result;
        } catch (Exception $e) {
            $result = new WsResponse(100, $e->getMessage());

            $g->sessionVarRestore();

            return $result;
        }
    }

    /**
     * Unpause case
     *
     * @param string caseUid : ID of the case.
     * @param int delIndex : Delegation index of the case.
     * @param string userUid : The unique ID of the user who will unpause the case.
     * @return $result will return an object
     */
    public function unpauseCase($caseUid, $delIndex, $userUid)
    {
        $g = new G();

        try {
            $g->sessionVarSave();

            $_SESSION["APPLICATION"] = $caseUid;
            $_SESSION["INDEX"] = $delIndex;
            $_SESSION["USER_LOGGED"] = $userUid;

            if (empty($caseUid)) {
                $result = new WsResponse(100, G::LoadTranslation("ID_REQUIRED_FIELD") . " caseUid");

                $g->sessionVarRestore();

                return $result;
            }

            if (empty($delIndex)) {
                $result = new WsResponse(100, G::LoadTranslation("ID_REQUIRED_FIELD") . " delIndex");

                $g->sessionVarRestore();

                return $result;
            }

            if (empty($userUid)) {
                $result = new WsResponse(100, G::LoadTranslation("ID_REQUIRED_FIELD") . " userUid");

                $g->sessionVarRestore();

                return $result;
            }

            $case = new Cases();
            $case->unpauseCase($caseUid, $delIndex, $userUid);

            //Response
            $res = new WsResponse(0, G::LoadTranslation("ID_COMMAND_EXECUTED_SUCCESSFULLY"));

            $result = array("status_code" => $res->status_code, "message" => $res->message, "timestamp" => $res->timestamp
            );

            $g->sessionVarRestore();

            return $result;
        } catch (Exception $e) {
            $result = new WsResponse(100, $e->getMessage());

            $g->sessionVarRestore();

            return $result;
        }
    }

    /**
     * Add case note
     *
     * @param string caseUid : ID of the case.
     * @param string processUid : ID of the process.
     * @param string taskUid : ID of the task.
     * @param string userUid : The unique ID of the user who will add note case.
     * @param string note : Note of the case.
     * @param int sendMail : Optional parameter. If set to 1, will send an email to all participants in the case.
     * @return $result will return an object
     */
    public function addCaseNote($caseUid, $processUid, $taskUid, $userUid, $note, $sendMail = 1)
    {
        try {
            if (empty($caseUid)) {
                $result = new WsResponse(100, G::LoadTranslation("ID_REQUIRED_FIELD") . " caseUid");

                return $result;
            }

            if (empty($processUid)) {
                $result = new WsResponse(100, G::LoadTranslation("ID_REQUIRED_FIELD") . " processUid");

                return $result;
            }

            if (empty($taskUid)) {
                $result = new WsResponse(100, G::LoadTranslation("ID_REQUIRED_FIELD") . " taskUid");

                return $result;
            }

            if (empty($userUid)) {
                $result = new WsResponse(100, G::LoadTranslation("ID_REQUIRED_FIELD") . " userUid");

                return $result;
            }

            if (empty($note)) {
                $result = new WsResponse(100, G::LoadTranslation("ID_REQUIRED_FIELD") . " note");

                return $result;
            }

            $case = new Cases();

            $respView = $case->getAllObjectsFrom($processUid, $caseUid, $taskUid, $userUid, "VIEW");
            $respBlock = $case->getAllObjectsFrom($processUid, $caseUid, $taskUid, $userUid, "BLOCK");

            if ($respView["CASES_NOTES"] == 0 && $respBlock["CASES_NOTES"] == 0) {
                $result = new WsResponse(100, G::LoadTranslation("ID_CASES_NOTES_NO_PERMISSIONS"));

                return $result;
            }

            //Add note case
            $appNote = new AppNotes();
            $response = $appNote->addCaseNote($caseUid, $userUid, $note, $sendMail);

            //Response
            $result = new WsResponse(0, G::LoadTranslation("ID_COMMAND_EXECUTED_SUCCESSFULLY"));

            return $result;
        } catch (Exception $e) {
            $result = new WsResponse(100, $e->getMessage());

            return $result;
        }
    }

    /**
     * ClaimCase
     *
     * @param string $userId
     * @param string $guid
     * @param string $delIndex
     * @return $result will return an object
     */
    public function claimCase($userId, $guid, $delIndex)
    {
        try {
            $oCase = new Cases();
            $oCase->loadCase($guid);
            $oCase->setCatchUser($guid, $delIndex, $userId);

            $result = new WsResponse(0, G::LoadTranslation("ID_COMMAND_EXECUTED_SUCCESSFULLY"));
            return $result;
        } catch (Exception $e) {
            $result = new WsResponse(100, $e->getMessage());
            return $result;
        }
    }
}
