<?php
if (!isset($_SESSION['USER_LOGGED'])) {
    $responseObject = new stdclass();
    $responseObject->error = G::LoadTranslation('ID_LOGIN_AGAIN');
    $responseObject->success = true;
    $responseObject->lostSession = true;
    print G::json_encode( $responseObject );
    die();
}

try {
    $userUid = $_SESSION['USER_LOGGED'];

    $filters['paged'] = isset($_REQUEST["paged"]) ? $_REQUEST["paged"] : true;
    $filters['count'] = isset($_REQUEST['count']) ? $_REQUEST["count"] : true;
    $filters['category'] = isset($_REQUEST["category"]) ? $_REQUEST["category"] : "";
    $filters['process'] = isset($_REQUEST["process"]) ? $_REQUEST["process"] : "";
    $filters['search'] = isset($_REQUEST["search"]) ? $_REQUEST["search"] : "";
    $filters['filter'] = isset($_REQUEST["filter"]) ? $_REQUEST["filter"] : "";
    $filters['dateFrom'] = (!empty($_REQUEST["dateFrom"])) ? substr($_REQUEST["dateFrom"], 0, 10) : "";
    $filters['dateTo'] = (!empty($_REQUEST["dateTo"])) ? substr($_REQUEST["dateTo"], 0, 10) : "";
    $filters['start'] = isset($_REQUEST["start"]) ? $_REQUEST["start"] : "0";
    $filters['limit'] = isset($_REQUEST["limit"]) ? $_REQUEST["limit"] : "25";
    $filters['sort'] = (isset($_REQUEST['sort'])) ? (($_REQUEST['sort'] == 'APP_STATUS_LABEL') ? 'APP_STATUS' : $_REQUEST["sort"]) : '';
    $filters['dir'] = isset($_REQUEST["dir"]) ? $_REQUEST["dir"] : "DESC";
    $filters['action'] = isset($_REQUEST["action"]) ? $_REQUEST["action"] : "";
    $filters['user'] = isset($_REQUEST["user"]) ? $_REQUEST["user"] : "";
    $listName = isset($_REQUEST["list"]) ? $_REQUEST["list"] : "inbox";
    $filters['filterStatus'] = isset($_REQUEST["filterStatus"]) ? $_REQUEST["filterStatus"] : "";
    $filters['sort'] = G::toUpper($filters['sort']);
    $openApplicationUid = (isset($_REQUEST['openApplicationUid']) && $_REQUEST['openApplicationUid'] != '') ? $_REQUEST['openApplicationUid'] : null;

    //Define user when is reassign
    if ($filters['action'] == 'to_reassign') {
        if ($filters['user'] == '' ) {
            $userUid = '';
        }
        if ($filters['user'] !== '' && $filters['user'] !== 'CURRENT_USER') {
            $userUid = $filters['user'];
        }
    }

    // Select list
    switch ($listName) {
        case 'inbox':
            $list = new ListInbox();
            $listpeer = 'ListInboxPeer';
            break;
        case 'participated_history':
            $list = new ListParticipatedHistory();
            $listpeer = 'ListParticipatedHistoryPeer';
            break;
        case 'participated':
        case 'participated_last':
            $list = new ListParticipatedLast();
            $listpeer = 'ListParticipatedLastPeer';
            break;
        case 'completed':
            $list = new ListCompleted();
            $listpeer = 'ListCompletedPeer';
            break;
        case 'paused':
            $list = new ListPaused();
            $listpeer = 'ListPausedPeer';
            break;
        case 'canceled':
            $list = new ListCanceled();
            $listpeer = 'ListCanceledPeer';
            break;
        case 'my_inbox':
            $list = new ListMyInbox();
            $listpeer = 'ListMyInboxPeer';
            break;
        case 'unassigned':
            $list = new ListUnassigned();
            $listpeer = 'ListUnassignedPeer';
            break;
    }

    // Validate filters
    $filters['search'] = (!is_null($openApplicationUid))? $openApplicationUid : $filters['search'];
    //Set a flag for review in the list by APP_UID when is used the case Link with parallel task
    $filters['caseLink'] = (!is_null($openApplicationUid))? $openApplicationUid : '';

    $filters['start'] = (int)$filters['start'];
    $filters['start'] = abs($filters['start']);
    if ($filters['start'] != 0) {
        $filters['start']+1;
    }

    $filters['limit'] = (int)$filters['limit'];
    $filters['limit'] = abs($filters['limit']);
    if ($filters['limit'] == 0) {
        $conf = new Configurations();
        $generalConfCasesList = $conf->getConfiguration('ENVIRONMENT_SETTINGS', '');
        if (isset($generalConfCasesList['casesListRowNumber'])) {
            $filters['limit'] = (int)$generalConfCasesList['casesListRowNumber'];
        } else {
            $filters['limit'] = 25;
        }
    } else {
        $filters['limit'] = (int)$filters['limit'];
    }
    
    switch ($filters['sort']) {
        case 'APP_CURRENT_USER':
            $filters['sort'] = 'DEL_CURRENT_USR_LASTNAME';
            break;
        case 'DEL_TASK_DUE_DATE':
            $filters['sort'] = 'DEL_DUE_DATE';
            break;
        case 'APP_UPDATE_DATE':
            $filters['sort'] = 'DEL_DELEGATE_DATE';
            break;
        case 'APP_DEL_PREVIOUS_USER':
            $filters['sort'] = 'DEL_DUE_DATE';
            break;
        case 'DEL_CURRENT_TAS_TITLE':
            $filters['sort'] = 'APP_TAS_TITLE';
            break;
        case 'APP_STATUS_LABEL':
            $filters['sort'] = 'APP_STATUS';
            break;
    }

    $filters['dir'] = G::toUpper($filters['dir']);
    if (!($filters['dir'] == 'DESC' || $filters['dir'] == 'ASC')) {
        $filters['dir'] = 'DESC';
    }

    $result = $list->loadList(
        $userUid,
        $filters,
        function (array $record)
        {
            try {
                if (isset($record["DEL_PREVIOUS_USR_UID"])) {
                    if ($record["DEL_PREVIOUS_USR_UID"] == "") {
                        $appDelegation = AppDelegationPeer::retrieveByPK($record["APP_UID"], $record["DEL_INDEX"]);

                        if (!is_null($appDelegation)) {
                            $appDelegationPrevious = AppDelegationPeer::retrieveByPK($record["APP_UID"], $appDelegation->getDelPrevious());

                            if (!is_null($appDelegationPrevious)) {
                                $taskPrevious = TaskPeer::retrieveByPK($appDelegationPrevious->getTasUid());

                                if (!is_null($taskPrevious)) {
                                    switch ($taskPrevious->getTasType()) {
                                        case "SCRIPT-TASK":
                                            $record["DEL_PREVIOUS_USR_UID"] = $taskPrevious->getTasType();
                                            break;
                                    }
                                }
                            }
                        }
                    }

                    $record["PREVIOUS_USR_UID"]       = $record["DEL_PREVIOUS_USR_UID"];
                    $record["PREVIOUS_USR_USERNAME"]  = $record["DEL_PREVIOUS_USR_USERNAME"];
                    $record["PREVIOUS_USR_FIRSTNAME"] = $record["DEL_PREVIOUS_USR_FIRSTNAME"];
                    $record["PREVIOUS_USR_LASTNAME"]  = $record["DEL_PREVIOUS_USR_LASTNAME"];
                }

                if (isset($record["DEL_DUE_DATE"])) {
                    $record["DEL_TASK_DUE_DATE"] = $record["DEL_DUE_DATE"];
                }

                if (isset($record["APP_PAUSED_DATE"])) {
                    $record["APP_UPDATE_DATE"] = $record["APP_PAUSED_DATE"];
                }

                if (isset($record["DEL_CURRENT_USR_USERNAME"])) {
                    $record["USR_USERNAME"]    = $record["DEL_CURRENT_USR_USERNAME"];
                    $record["USR_FIRSTNAME"]   = $record["DEL_CURRENT_USR_FIRSTNAME"];
                    $record["USR_LASTNAME"]    = $record["DEL_CURRENT_USR_LASTNAME"];
                    $record["APP_UPDATE_DATE"] = $record["DEL_DELEGATE_DATE"];
                }

                if (isset($record['DEL_CURRENT_TAS_TITLE']) && $record['DEL_CURRENT_TAS_TITLE'] != '') {
                    $record['APP_TAS_TITLE'] = $record['DEL_CURRENT_TAS_TITLE'];
                }

                if (isset($record["APP_STATUS"])) {
                    $record["APP_STATUS_LABEL"] = G::LoadTranslation("ID_" . $record["APP_STATUS"]);
                }

                //Return
                return $record;
            } catch (Exception $e) {
                throw $e;
            }
        }
    );

    $response = array();
    $response['filters']        = $filters;
    $response['totalCount']     = $list->getCountList($userUid, $filters);
    $response['data'] = \ProcessMaker\Util\DateTime::convertUtcToTimeZone($result);
    echo G::json_encode($response);
} catch (Exception $e) {
    $msg = array("error" => $e->getMessage());
    echo G::json_encode($msg);
}
