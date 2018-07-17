<?php
namespace ProcessMaker\Services\Api;

use \ProcessMaker\Services\Api;
use \Luracast\Restler\RestException;
use \ProcessMaker\Util\DateTime;
use \ProcessMaker\BusinessModel\Validator;
use AppDocument;

/**
 * Cases Api Controller
 *
 * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
 * @copyright Colosa - Bolivia
 *
 * @protected
 */
class Cases extends Api
{
    private $arrayFieldIso8601 = [
        "del_init_date",
        "del_finish_date",
        "del_task_due_date",
        "del_risk_date",
        "del_delegate_date",
        "app_create_date",
        "app_update_date",
        "app_finish_date",
        "del_delegate_date",
        "note_date"
    ];

    public function __isAllowed()
    {
        try {
            $methodName = $this->restler->apiMethodInfo->methodName;
            $arrayArgs  = $this->restler->apiMethodInfo->arguments;
            switch ($methodName) {
                case 'doGetCaseVariables':
                    $applicationUid = $this->parameters[$arrayArgs['app_uid']];
                    $dynaformUid = $this->parameters[$arrayArgs['dyn_uid']];
                    $delIndex = $this->parameters[$arrayArgs['app_index']];
                    $userUid = $this->getUserId();
                    //Check if the user has the case
                    $appDelegation = new \AppDelegation();
                    $aCurUser = $appDelegation->getCurrentUsers($applicationUid, $delIndex);
                    if (!empty($aCurUser)) {
                        foreach ($aCurUser as $key => $value) {
                            if ($value === $userUid) {
                                return true;
                            }
                        }
                    }
                    //Check if the user has Permissions
                    $oCases = new \ProcessMaker\BusinessModel\Cases();
                    return $oCases->checkUserHasPermissionsOrSupervisor($userUid, $applicationUid, $dynaformUid);
                    break;
                case 'doPostReassign':
                    $arrayParameters = $this->parameters[0]['cases'];
                    $usrUid = $this->getUserId();

                    //Check if the user is supervisor process
                    $case = new \ProcessMaker\BusinessModel\Cases();
                    $user = new \ProcessMaker\BusinessModel\User();

                    $count = 0;

                    foreach ($arrayParameters as $value) {
                        $arrayApplicationData = $case->getApplicationRecordByPk($value['APP_UID'], [], false);

                        if (!empty($arrayApplicationData)) {
                            if (!$user->checkPermission($usrUid, 'PM_REASSIGNCASE')) {
                                if ($user->checkPermission($usrUid, 'PM_REASSIGNCASE_SUPERVISOR')) {
                                    $supervisor = new \ProcessMaker\BusinessModel\ProcessSupervisor();
                                    $flagps = $supervisor->isUserProcessSupervisor($arrayApplicationData['PRO_UID'], $usrUid);
                                    if (!$flagps) {
                                        $count = $count + 1;
                                    }

                                }
                            }
                        }
                    }

                    if ($count == 0) {
                        return true;
                    }
                    break;
                case "doGetCaseInfo" :
                    $appUid = $this->parameters[$arrayArgs['app_uid']];
                    $usrUid = $this->getUserId();
                    //Check if the user is supervisor process
                    $case = new \ProcessMaker\BusinessModel\Cases();
                    $user = new \ProcessMaker\BusinessModel\User();
                    $arrayApplicationData = $case->getApplicationRecordByPk($appUid, [], false);
                    if (!empty($arrayApplicationData)) {
                        $criteria = new \Criteria("workflow");
                        $criteria->addSelectColumn(\AppDelegationPeer::APP_UID);
                        $criteria->add(\AppDelegationPeer::APP_UID, $appUid);
                        $criteria->add(\AppDelegationPeer::USR_UID, $usrUid);
                        $criteria->setLimit(1);
                        $rsCriteria = \AppDelegationPeer::doSelectRS($criteria);
                        if ($rsCriteria->next()) {
                            return true;
                        } else {
                            $supervisor = new \ProcessMaker\BusinessModel\ProcessSupervisor();
                            $flagps = $supervisor->isUserProcessSupervisor($arrayApplicationData['PRO_UID'], $usrUid);
                            return $flagps;
                        }
                    }
                    break;
                case 'doDownloadInputDocument':
                    //Verify if the user can be download the file
                    $appDocUid = $this->parameters[$arrayArgs['app_doc_uid']];
                    $version = $this->parameters[$arrayArgs['v']];
                    $usrUid = $this->getUserId();
                    $oAppDocument = new AppDocument();
                    if ($version == 0) {
                        $docVersion = $oAppDocument->getLastAppDocVersion($appDocUid);
                    } else {
                        $docVersion = $version;
                    }
                    if (defined('DISABLE_DOWNLOAD_DOCUMENTS_SESSION_VALIDATION') && DISABLE_DOWNLOAD_DOCUMENTS_SESSION_VALIDATION == 0) {
                        if ($oAppDocument->canDownloadInput($usrUid, $appDocUid, $docVersion)) {
                            return true;
                        }
                    } else {
                        return true;
                    }
                    break;
            }

            return false;
        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }

    /**
     * Get list Cases To Do
     *
     * @param int $start {@from path}
     * @param int $limit {@from path}
     * @param string $sort {@from path}
     * @param string $dir {@from path}
     * @param string $cat_uid {@from path}
     * @param string $pro_uid {@from path}
     * @param string $search {@from path}
     * @return array
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @url GET
     */
    public function doGetCasesListToDo(
        $start = 0,
        $limit = 0,
        $sort = 'APP_CACHE_VIEW.APP_NUMBER',
        $dir = 'DESC',
        $cat_uid = '',
        $pro_uid = '',
        $search = ''
    ) {
        try {
            $dataList['userId'] = $this->getUserId();
            $dataList['action'] = 'todo';
            $dataList['paged']  = false;

            $dataList['start'] = $start;
            $dataList['limit'] = $limit;
            $dataList['sort'] = $sort;
            $dataList['dir'] = $dir;
            $dataList['category'] = $cat_uid;
            $dataList['process'] = $pro_uid;
            $dataList['search'] = $search;
            $oCases = new \ProcessMaker\BusinessModel\Cases();
            $response = $oCases->getList($dataList);

            return DateTime::convertUtcToIso8601($response, $this->arrayFieldIso8601);
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * Get list Cases To Do with paged
     *
     * @param int $start {@from path}
     * @param int $limit {@from path}
     * @param string $sort {@from path}
     * @param string $dir {@from path}
     * @param string $cat_uid {@from path}
     * @param string $pro_uid {@from path}
     * @param string $search {@from path}
     * @return array
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @url GET /paged
     */
    public function doGetCasesListToDoPaged(
        $start = 0,
        $limit = 0,
        $sort = 'APP_CACHE_VIEW.APP_NUMBER',
        $dir = 'DESC',
        $cat_uid = '',
        $pro_uid = '',
        $search = ''
    ) {
        try {
            $dataList['userId'] = $this->getUserId();
            $dataList['action'] = 'todo';
            $dataList['paged']  = true;

            $dataList['start'] = $start;
            $dataList['limit'] = $limit;
            $dataList['sort'] = $sort;
            $dataList['dir'] = $dir;
            $dataList['category'] = $cat_uid;
            $dataList['process'] = $pro_uid;
            $dataList['search'] = $search;
            $oCases = new \ProcessMaker\BusinessModel\Cases();
            $response = $oCases->getList($dataList);
            return DateTime::convertUtcToIso8601($response, $this->arrayFieldIso8601);
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * Get list Cases Draft
     *
     * @param int $start {@from path}
     * @param int $limit {@from path}
     * @param string $sort {@from path}
     * @param string $dir {@from path}
     * @param string $cat_uid {@from path}
     * @param string $pro_uid {@from path}
     * @param string $search {@from path}
     * @return array
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @url GET /draft
     */
    public function doGetCasesListDraft(
        $start = 0,
        $limit = 0,
        $sort = 'APP_CACHE_VIEW.APP_NUMBER',
        $dir = 'DESC',
        $cat_uid = '',
        $pro_uid = '',
        $search = ''
    ) {
        try {
            $dataList['userId'] = $this->getUserId();
            $dataList['action'] = 'draft';
            $dataList['paged']  = false;

            $dataList['start'] = $start;
            $dataList['limit'] = $limit;
            $dataList['sort'] = $sort;
            $dataList['dir'] = $dir;
            $dataList['category'] = $cat_uid;
            $dataList['process'] = $pro_uid;
            $dataList['search'] = $search;
            $oCases = new \ProcessMaker\BusinessModel\Cases();
            $response = $oCases->getList($dataList);
            return DateTime::convertUtcToIso8601($response, $this->arrayFieldIso8601);
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * Get list Cases Draft with paged
     *
     * @param int $start {@from path}
     * @param int $limit {@from path}
     * @param string $sort {@from path}
     * @param string $dir {@from path}
     * @param string $cat_uid {@from path}
     * @param string $pro_uid {@from path}
     * @param string $search {@from path}
     * @return array
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @url GET /draft/paged
     */
    public function doGetCasesListDraftPaged(
        $start = 0,
        $limit = 0,
        $sort = 'APP_CACHE_VIEW.APP_NUMBER',
        $dir = 'DESC',
        $cat_uid = '',
        $pro_uid = '',
        $search = ''
    ) {
        try {
            $dataList['userId'] = $this->getUserId();
            $dataList['action'] = 'draft';
            $dataList['paged']  = true;

            $dataList['start'] = $start;
            $dataList['limit'] = $limit;
            $dataList['sort'] = $sort;
            $dataList['dir'] = $dir;
            $dataList['category'] = $cat_uid;
            $dataList['process'] = $pro_uid;
            $dataList['search'] = $search;
            $oCases = new \ProcessMaker\BusinessModel\Cases();
            $response = $oCases->getList($dataList);
            return DateTime::convertUtcToIso8601($response, $this->arrayFieldIso8601);
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * Get list Cases Participated
     *
     * @param int $start {@from path}
     * @param int $limit {@from path}
     * @param string $sort {@from path}
     * @param string $dir {@from path}
     * @param string $cat_uid {@from path}
     * @param string $pro_uid {@from path}
     * @param string $search {@from path}
     * @return array
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @url GET /participated
     */
    public function doGetCasesListParticipated(
        $start = 0,
        $limit = 0,
        $sort = 'APP_CACHE_VIEW.APP_NUMBER',
        $dir = 'DESC',
        $cat_uid = '',
        $pro_uid = '',
        $search = ''
    ) {
        try {
            $dataList['userId'] = $this->getUserId();
            $dataList['action'] = 'sent';
            $dataList['paged']  = false;

            $dataList['start'] = $start;
            $dataList['limit'] = $limit;
            $dataList['sort'] = $sort;
            $dataList['dir'] = $dir;
            $dataList['category'] = $cat_uid;
            $dataList['process'] = $pro_uid;
            $dataList['search'] = $search;
            $oCases = new \ProcessMaker\BusinessModel\Cases();
            $response = $oCases->getList($dataList);
            return DateTime::convertUtcToIso8601($response, $this->arrayFieldIso8601);
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * Get list Cases Participated with paged
     *
     * @param int $start {@from path}
     * @param int $limit {@from path}
     * @param string $sort {@from path}
     * @param string $dir {@from path}
     * @param string $cat_uid {@from path}
     * @param string $pro_uid {@from path}
     * @param string $search {@from path}
     * @return array
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @url GET /participated/paged
     */
    public function doGetCasesListParticipatedPaged(
        $start = 0,
        $limit = 0,
        $sort = 'APP_CACHE_VIEW.APP_NUMBER',
        $dir = 'DESC',
        $cat_uid = '',
        $pro_uid = '',
        $search = ''
    ) {
        try {
            $dataList['userId'] = $this->getUserId();
            $dataList['action'] = 'sent';
            $dataList['paged']  = true;

            $dataList['start'] = $start;
            $dataList['limit'] = $limit;
            $dataList['sort'] = $sort;
            $dataList['dir'] = $dir;
            $dataList['category'] = $cat_uid;
            $dataList['process'] = $pro_uid;
            $dataList['search'] = $search;
            $oCases = new \ProcessMaker\BusinessModel\Cases();
            $response = $oCases->getList($dataList);
            return DateTime::convertUtcToIso8601($response, $this->arrayFieldIso8601);
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * Get list Cases Unassigned
     *
     * @param int $start {@from path}
     * @param int $limit {@from path}
     * @param string $sort {@from path}
     * @param string $dir {@from path}
     * @param string $cat_uid {@from path}
     * @param string $pro_uid {@from path}
     * @param string $search {@from path}
     * @return array
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @url GET /unassigned
     */
    public function doGetCasesListUnassigned(
        $start = 0,
        $limit = 0,
        $sort = 'APP_CACHE_VIEW.APP_NUMBER',
        $dir = 'DESC',
        $cat_uid = '',
        $pro_uid = '',
        $search = ''
    ) {
        try {
            $dataList['userId'] = $this->getUserId();
            $dataList['action'] = 'unassigned';
            $dataList['paged']  = false;

            $dataList['start'] = $start;
            $dataList['limit'] = $limit;
            $dataList['sort'] = $sort;
            $dataList['dir'] = $dir;
            $dataList['category'] = $cat_uid;
            $dataList['process'] = $pro_uid;
            $dataList['search'] = $search;
            $oCases = new \ProcessMaker\BusinessModel\Cases();
            $response = $oCases->getList($dataList);
            return DateTime::convertUtcToIso8601($response, $this->arrayFieldIso8601);
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * Get list Cases Unassigned with paged
     *
     * @param int $start {@from path}
     * @param int $limit {@from path}
     * @param string $sort {@from path}
     * @param string $dir {@from path}
     * @param string $cat_uid {@from path}
     * @param string $pro_uid {@from path}
     * @param string $search {@from path}
     * @return array
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @url GET /unassigned/paged
     */
    public function doGetCasesListUnassignedPaged(
        $start = 0,
        $limit = 0,
        $sort = 'APP_CACHE_VIEW.APP_NUMBER',
        $dir = 'DESC',
        $cat_uid = '',
        $pro_uid = '',
        $search = ''
    ) {
        try {
            $dataList['userId'] = $this->getUserId();
            $dataList['action'] = 'unassigned';
            $dataList['paged']  = true;

            $dataList['start'] = $start;
            $dataList['limit'] = $limit;
            $dataList['sort'] = $sort;
            $dataList['dir'] = $dir;
            $dataList['category'] = $cat_uid;
            $dataList['process'] = $pro_uid;
            $dataList['search'] = $search;
            $oCases = new \ProcessMaker\BusinessModel\Cases();
            $response = $oCases->getList($dataList);
            return DateTime::convertUtcToIso8601($response, $this->arrayFieldIso8601);
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * Get list Cases Paused
     *
     * @param int $start {@from path}
     * @param int $limit {@from path}
     * @param string $sort {@from path}
     * @param string $dir {@from path}
     * @param string $cat_uid {@from path}
     * @param string $pro_uid {@from path}
     * @param string $search {@from path}
     * @return array
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @url GET /paused
     */
    public function doGetCasesListPaused(
        $start = 0,
        $limit = 0,
        $sort = 'APP_CACHE_VIEW.APP_NUMBER',
        $dir = 'DESC',
        $cat_uid = '',
        $pro_uid = '',
        $search = ''
    ) {
        try {
            $dataList['userId'] = $this->getUserId();
            $dataList['action'] = 'paused';
            $dataList['paged']  = false;

            $dataList['start'] = $start;
            $dataList['limit'] = $limit;
            $dataList['sort'] = $sort;
            $dataList['dir'] = $dir;
            $dataList['category'] = $cat_uid;
            $dataList['process'] = $pro_uid;
            $dataList['search'] = $search;
            $oCases = new \ProcessMaker\BusinessModel\Cases();
            $response = $oCases->getList($dataList);
            return DateTime::convertUtcToIso8601($response, $this->arrayFieldIso8601);
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * Get list Cases Paused with paged
     *
     * @param int $start {@from path}
     * @param int $limit {@from path}
     * @param string $sort {@from path}
     * @param string $dir {@from path}
     * @param string $cat_uid {@from path}
     * @param string $pro_uid {@from path}
     * @param string $search {@from path}
     * @return array
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @url GET /paused/paged
     */
    public function doGetCasesListPausedPaged(
        $start = 0,
        $limit = 0,
        $sort = 'APP_CACHE_VIEW.APP_NUMBER',
        $dir = 'DESC',
        $cat_uid = '',
        $pro_uid = '',
        $search = ''
    ) {
        try {
            $dataList['userId'] = $this->getUserId();
            $dataList['action'] = 'paused';
            $dataList["paged"]  = true;

            $dataList['start'] = $start;
            $dataList['limit'] = $limit;
            $dataList['sort'] = $sort;
            $dataList['dir'] = $dir;
            $dataList['category'] = $cat_uid;
            $dataList['process'] = $pro_uid;
            $dataList['search'] = $search;
            $oCases = new \ProcessMaker\BusinessModel\Cases();
            $response = $oCases->getList($dataList);
            return DateTime::convertUtcToIso8601($response, $this->arrayFieldIso8601);
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * Get list Cases Advanced Search
     *
     * @param int $start {@from path}
     * @param int $limit {@from path}
     * @param string $sort {@from path}
     * @param string $dir {@from path}
     * @param string $cat_uid {@from path}
     * @param string $pro_uid {@from path}
     * @param string $app_status {@from path}
     * @param string $user {@from path}
     * @param string $date_from {@from path}
     * @param string $date_to {@from path}
     * @param string $search {@from path}
     * @return array
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @url GET /advanced-search
     */
    public function doGetCasesListAdvancedSearch(
        $start = 0,
        $limit = 0,
        $sort = 'APP_CACHE_VIEW.APP_NUMBER',
        $dir = 'DESC',
        $cat_uid = '',
        $pro_uid = '',
        $app_status = '',
        $usr_uid = '',
        $date_from = '',
        $date_to = '',
        $search = ''
    ) {
        try {
            $dataList['userId'] = $this->getUserId();
            $dataList['action'] = 'search';
            $dataList['paged']  = false;

            $dataList['start'] = $start;
            $dataList['limit'] = $limit;
            $dataList['sort'] = $sort;
            $dataList['dir'] = $dir;
            $dataList['category'] = $cat_uid;
            $dataList['process'] = $pro_uid;
            $dataList['status'] = $app_status;
            $dataList['user'] = $usr_uid;
            $dataList['dateFrom'] = $date_from;
            $dataList['dateTo'] = $date_to;
            $dataList['search'] = $search;
            $oCases = new \ProcessMaker\BusinessModel\Cases();
            $response = $oCases->getList($dataList);
            return DateTime::convertUtcToIso8601($response, $this->arrayFieldIso8601);
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * Get list Cases Advanced Search with Paged
     *
     * @param int $start {@from path}
     * @param int $limit {@from path}
     * @param string $sort {@from path}
     * @param string $dir {@from path}
     * @param string $cat_uid {@from path}
     * @param string $pro_uid {@from path}
     * @param string $app_status {@from path}
     * @param string $usr_uid {@from path}
     * @param string $date_from {@from path}
     * @param string $date_to {@from path}
     * @param string $search {@from path}
     * @return array
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @url GET /advanced-search/paged
     */
    public function doGetCasesListAdvancedSearchPaged(
        $start = 0,
        $limit = 0,
        $sort = 'APP_CACHE_VIEW.APP_NUMBER',
        $dir = 'DESC',
        $cat_uid = '',
        $pro_uid = '',
        $app_status = '',
        $usr_uid = '',
        $date_from = '',
        $date_to = '',
        $search = ''
    ) {
        try {
            $dataList['userId'] = $this->getUserId();
            $dataList['action'] = 'search';
            $dataList['paged']  = true;

            $dataList['start'] = $start;
            $dataList['limit'] = $limit;
            $dataList['sort'] = $sort;
            $dataList['dir'] = $dir;
            $dataList['category'] = $cat_uid;
            $dataList['process'] = $pro_uid;
            $dataList['status'] = $app_status;
            $dataList['user'] = $usr_uid;
            $dataList['dateFrom'] = $date_from;
            $dataList['dateTo'] = $date_to;
            $dataList['search'] = $search;
            $oCases = new \ProcessMaker\BusinessModel\Cases();
            $response = $oCases->getList($dataList);
            return DateTime::convertUtcToIso8601($response, $this->arrayFieldIso8601);
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @access protected
     * @class AccessControl {@className \ProcessMaker\Services\Api\Cases}
     * @url GET /:app_uid
     *
     * @param string $app_uid {@min 32}{@max 32}
     */
    public function doGetCaseInfo($app_uid)
    {
        try {
            $case = new \ProcessMaker\BusinessModel\Cases();
            $case->setFormatFieldNameInUppercase(false);

            $caseInfo = $case->getCaseInfo($app_uid, $this->getUserId());
            $caseInfo = DateTime::convertUtcToIso8601($caseInfo, $this->arrayFieldIso8601);
            return $caseInfo;
        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }

    /**
     * @url GET /:app_uid/current-task
     *
     * @param string $app_uid {@min 32}{@max 32}
     */
    public function doGetTaskCase($app_uid)
    {
        try {
            $case = new \ProcessMaker\BusinessModel\Cases();
            $case->setFormatFieldNameInUppercase(false);

            $arrayData = $case->getTaskCase($app_uid, $this->getUserId());

            $response = $arrayData;

            return DateTime::convertUtcToIso8601($response, $this->arrayFieldIso8601);
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * Start a new case and assign the logged-in user to work on the initial task 
     * in the case. Note that the logged-in user must be in the pool of assigned 
     * users for the initial task.
     * 
     * @url POST
     * 
     * @param string $pro_uid {@from body} {@min 32}{@max 32}
     * @param string $tas_uid {@from body} {@min 32}{@max 32}
     * @param array $variables {@from body}
     * 
     * @return array
     * @throws RestException 
     * 
     * @access protected
     * @class AccessControl {@permission PM_CASES}
     */
    public function doPostCase($pro_uid, $tas_uid, $variables = null)
    {
        try {
            $userUid = $this->getUserId();
            $cases = new \ProcessMaker\BusinessModel\Cases();
            $oData = $cases->addCase($pro_uid, $tas_uid, $userUid, $variables);
            return $oData;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * Creates a new case. It is similar to POST /cases, but it impersonates the 
     * session variables, so it is more robust than POST /cases. Note that the 
     * specified user to work on the case must be assigned to the pool of users 
     * for the initial task. Also note that the new case's status will be set to 
     * "DRAFT", not "TO_DO". If wishing to change the new case's status to "TO_DO", 
     * then create the following trigger in the process and use 
     * PUT /cases/{app_uid}/execute-trigger/{tri_uid} to execute it.
     * 
     * @url POST /impersonate
     * 
     * @param string $pro_uid {@from body} {@min 32}{@max 32}
     * @param string $usr_uid {@from body} {@min 32}{@max 32}
     * @param string $tas_uid {@from body} {@min 32}{@max 32}
     * @param array $variables {@from body}
     * 
     * @return array
     * @throws RestException 
     * 
     * @access protected
     * @class AccessControl {@permission PM_CASES}
     */
    public function doPostCaseImpersonate($pro_uid, $usr_uid, $tas_uid, $variables = null)
    {
        try {
            $cases = new \ProcessMaker\BusinessModel\Cases();
            $oData = $cases->addCaseImpersonate($pro_uid, $usr_uid, $tas_uid, $variables);
            return $oData;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * Update case reassignment.
     *
     * @url PUT /:app_uid/reassign-case
     *
     * @param string $app_uid {@min 32}{@max 32}
     * @param string $usr_uid_source {@from body} {@min 32}{@max 32}
     * @param string $usr_uid_target {@from body} {@min 32}{@max 32}
     * @param string $del_index {@from body}
     *
     * @throws RestException
     *
     * @access protected
     * @class AccessControl {@permission PM_REASSIGNCASE,PM_REASSIGNCASE_SUPERVISOR}
     */
    public function doPutReassignCase($app_uid, $usr_uid_source, $usr_uid_target, $del_index = null)
    {
        try {
            $userUid = $this->getUserId();
            $cases = new \ProcessMaker\BusinessModel\Cases();
            $cases->updateReassignCase($app_uid, $userUid, $del_index, $usr_uid_source, $usr_uid_target);
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * Route Case.
     *
     * @url PUT /:app_uid/route-case
     *
     * @param string $app_uid {@min 32}{@max 32}
     * @param string $del_index {@from body}
     *
     * @throws RestException
     *
     * @access protected
     * @class AccessControl {@permission PM_CASES}
     */
    public function doPutRouteCase($app_uid, $del_index = null)
    {
        try {
            $userUid = $this->getUserId();
            $cases = new \ProcessMaker\BusinessModel\Cases();
            $cases->updateRouteCase($app_uid, $userUid, $del_index);
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * Cancel Case
     *
     * @url PUT /:cas_uid/cancel
     *
     * @param string $cas_uid {@min 1}{@max 32}
     *
     * @throws RestException
     *
     * @access protected
     * @class AccessControl {@permission PM_CANCELCASE}
     */
    public function doPutCancelCase($cas_uid)
    {
        try {
            $userUid = $this->getUserId();
            $cases = new \ProcessMaker\BusinessModel\Cases();
            $cases->putCancelCase($cas_uid, $userUid);
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * Pause Case
     *
     * @url PUT /:cas_uid/pause
     *
     * @param string $cas_uid {@min 1}{@max 32}
     * @param string $unpaused_date {@from body}
     *
     * @throws RestException
     *
     * @access protected
     * @class AccessControl {@permission PM_CASES}
     */
    public function doPutPauseCase($cas_uid, $unpaused_date = null)
    {
        try {
            $userUid = $this->getUserId();
            $cases = new \ProcessMaker\BusinessModel\Cases();
            if ($unpaused_date == null) {
                $cases->putPauseCase($cas_uid, $userUid);
            } else {
                $cases->putPauseCase($cas_uid, $userUid, false, $unpaused_date);
            }
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * Unpause Case
     *
     * @url PUT /:cas_uid/unpause
     *
     * @param string $cas_uid {@min 1}{@max 32}
     *
     * @throws RestException
     *
     * @access protected
     * @class AccessControl {@permission PM_CASES}
     */
    public function doPutUnpauseCase($cas_uid)
    {
        try {
            $userUid = $this->getUserId();
            $cases = new \ProcessMaker\BusinessModel\Cases();
            $cases->putUnpauseCase($cas_uid, $userUid);
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * Execute trigger in a case.
     *
     * @url PUT /:cas_uid/execute-trigger/:tri_uid
     *
     * @param string $cas_uid {@min 1}{@max 32}
     * @param string $tri_uid {@min 1}{@max 32}
     *
     * @throws RestException
     *
     * @access protected
     * @class AccessControl {@permission PM_CASES}
     */
    public function doPutExecuteTriggerCase($cas_uid, $tri_uid)
    {
        try {
            $userUid = $this->getUserId();
            $cases = new \ProcessMaker\BusinessModel\Cases();
            $cases->putExecuteTriggerCase($cas_uid, $tri_uid, $userUid);
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * Delete Case
     * @url DELETE /:cas_uid
     *
     * @access protected
     * @class AccessControl {@permission PM_CASES}
     *
     * @param string $cas_uid {@min 1}{@max 32}
     */
    public function doDeleteCase($cas_uid)
    {
        try {
            $usr_uid = $this->getUserId();
            $cases = new \ProcessMaker\BusinessModel\Cases();
            $cases->deleteCase($cas_uid, $usr_uid);
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * Get Case Variables
     *
     * @access protected
     * @class  AccessControl {@className \ProcessMaker\Services\Api\Cases}
     * @url GET /:app_uid/variables
     *
     * @param string $app_uid {@min 1}{@max 32}
     * @param string $dyn_uid
     * @param string $pro_uid
     * @param string $act_uid
     * @param int $app_index
     * @return mixed
     * @throws RestException
     */
    public function doGetCaseVariables($app_uid, $dyn_uid = null, $pro_uid = null, $act_uid = null, $app_index = null)
    {
        try {
            $usr_uid = $this->getUserId();
            $cases = new \ProcessMaker\BusinessModel\Cases();
            $response = $cases->getCaseVariables($app_uid, $usr_uid, $dyn_uid, $pro_uid, $act_uid, $app_index);
            return DateTime::convertUtcToIso8601($response);
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * Put Case Variables
     *
     * @url PUT /:app_uid/variable
     *
     * @param string $app_uid {@min 1}{@max 32}
     * @param array $request_data
     * @param string $dyn_uid {@from path}
     * @param int $del_index {@from path}
     *
     * @throws RestException
     *
     * @access protected
     * @class AccessControl {@permission PM_CASES}
     */
    public function doPutCaseVariables($app_uid, $request_data, $dyn_uid = '', $del_index = 0)
    {
        try {
            $usr_uid = $this->getUserId();
            $cases = new \ProcessMaker\BusinessModel\Cases();
            $request_data = \ProcessMaker\Util\DateTime::convertDataToUtc($request_data);
            $cases->setCaseVariables($app_uid, $request_data, $dyn_uid, $usr_uid, $del_index);
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * Get Case Notes
     *
     * @param string $app_uid {@min 1}{@max 32}
     * @param int $start {@from path}
     * @param int $limit {@from path}
     * @param string $sort {@from path}
     * @param string $dir {@from path}
     * @param string $usr_uid {@from path}
     * @param string $date_from {@from path}
     * @param string $date_to {@from path}
     * @param string $search {@from path}
     * @return array
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @url GET /:app_uid/notes
     */
    public function doGetCaseNotes(
        $app_uid,
        $start = 0,
        $limit = 25,
        $sort = 'APP_CACHE_VIEW.APP_NUMBER',
        $dir = 'DESC',
        $usr_uid = '',
        $date_from = '',
        $date_to = '',
        $search = ''
    ) {
        try {
            $dataList['paged']  = false;

            $dataList['start'] = $start;
            $dataList['limit'] = $limit;
            $dataList['sort'] = $sort;
            $dataList['dir'] = $dir;
            $dataList['user'] = $usr_uid;
            $dataList['dateFrom'] = $date_from;
            $dataList['dateTo'] = $date_to;
            $dataList['search'] = $search;

            $usr_uid = $this->getUserId();
            $cases = new \ProcessMaker\BusinessModel\Cases();
            $response = $cases->getCaseNotes($app_uid, $usr_uid, $dataList);
            return DateTime::convertUtcToIso8601($response, $this->arrayFieldIso8601);
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * Get Case Notes with Paged
     *
     * @param int $start {@from path}
     * @param int $limit {@from path}
     * @param string $sort {@from path}
     * @param string $dir {@from path}
     * @param string $usr_uid {@from path}
     * @param string $date_from {@from path}
     * @param string $date_to {@from path}
     * @param string $search {@from path}
     * @return array
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @url GET /:app_uid/notes/paged
     */
    public function doGetCaseNotesPaged(
        $app_uid,
        $start = 0,
        $limit = 25,
        $sort = 'APP_CACHE_VIEW.APP_NUMBER',
        $dir = 'DESC',
        $usr_uid = '',
        $date_from = '',
        $date_to = '',
        $search = ''
    ) {
        try {
            $dataList['start'] = $start;
            $dataList['limit'] = $limit;
            $dataList['sort'] = $sort;
            $dataList['dir'] = $dir;
            $dataList['user'] = $usr_uid;
            $dataList['dateFrom'] = $date_from;
            $dataList['dateTo'] = $date_to;
            $dataList['search'] = $search;

            $usr_uid = $this->getUserId();
            $cases = new \ProcessMaker\BusinessModel\Cases();
            $response = $cases->getCaseNotes($app_uid, $usr_uid, $dataList);
            return DateTime::convertUtcToIso8601($response, $this->arrayFieldIso8601);
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * Create a new case note for a given case. Note that only users who are 
     * currently assigned to work on the case or have Process Permissions to 
     * access case notes may create a case note.
     *
     * @url POST /:app_uid/note
     * 
     * @param string $app_uid {@min 1}{@max 32}
     * @param string $note_content {@min 1}{@max 500}
     * @param int $send_mail {@choice 1,0}
     * 
     * @return void
     * @throws RestException 
     * 
     * @access protected
     * @class AccessControl {@permission PM_CASES}
     */
    public function doPostCaseNote($app_uid, $note_content, $send_mail = 0)
    {
        try {
            $usr_uid = $this->getUserId();
            $cases = new \ProcessMaker\BusinessModel\Cases();
            $send_mail = ($send_mail == 0) ? false : true;
            $cases->saveCaseNote($app_uid, $usr_uid, $note_content, $send_mail);
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url GET /:app_uid/tasks
     *
     * @param string $app_uid {@min 32}{@max 32}
     */
    public function doGetTasks($app_uid)
    {
        try {
            $case = new \ProcessMaker\BusinessModel\Cases();
            $case->setFormatFieldNameInUppercase(false);

            $response = $case->getTasks($app_uid);

            return DateTime::convertUtcToIso8601($response, $this->arrayFieldIso8601);
        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }

    /**
     * Execute triggers
     *
     * @url PUT /:app_uid/execute-triggers
     *
     * @param string $app_uid {@min 1}{@max 32}
     * @param int $del_index {@from body}
     * @param string $obj_type {@from body}
     * @param string $obj_uid {@from body}
     *
     * @throws RestException
     *
     * @class AccessControl {@permission PM_CASES}
     * @access protected
     */
    public function doPutExecuteTriggers($app_uid, $del_index, $obj_type, $obj_uid)
    {
        try {
            $cases = new \ProcessMaker\BusinessModel\Cases();
            $cases->putExecuteTriggers($app_uid, $del_index, $obj_type, $obj_uid);
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url GET /:app_uid/:del_index/steps
     *
     * @param string $app_uid {@min 32}{@max 32}
     * @param int $del_index
     *
     */
    public function doGetSteps($app_uid, $del_index)
    {
        try {
            $case = new \ProcessMaker\BusinessModel\Cases();
            $case->setFormatFieldNameInUppercase(false);

            $response = $case->getSteps($app_uid, $del_index);

            return $response;
        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }

    /**
     * Get process list for start case
     *
     * @url GET /start-cases
     *
     * @param string $type_view {@from path}
     * @return array
     *
     */
    public function doGetCasesListStarCase(
        $type_view = ''
    ) {
        try {
            $usr_uid = $this->getUserId();
            $case = new \ProcessMaker\BusinessModel\Cases();

            $response = $case->getCasesListStarCase($usr_uid, $type_view);

            return $response;
        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }

    /**
     * Get process list bookmark for start case
     *
     * @url GET /bookmark-start-cases
     *
     * @param string $type_view {@from path}
     * @return array
     *
     */
    public function doGetCasesListBookmarkStarCase(
        $type_view = ''
    ) {
        try {
            $usr_uid = $this->getUserId();
            $case = new \ProcessMaker\BusinessModel\Cases();

            $response = $case->getCasesListBookmarkStarCase($usr_uid, $type_view);

            return $response;
        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }


    /**
     * Mark a task process as a bookmark
     * 
     * @url POST /bookmark/:tas_uid
     * 
     * @param string $tas_uid {@min 32}{@max 32}
     * 
     * @return void
     * @throws RestException 
     * 
     * @access protected
     * @class AccessControl {@permission PM_CASES}
     */
    public function doPostBookmarkStartCase($tas_uid)
    {
        try {
            $userLoggedUid = $this->getUserId();
            $user = new \ProcessMaker\BusinessModel\User();
            $user->updateBookmark($userLoggedUid, $tas_uid, 'INSERT');
        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }

    /**
     * Remove a task process from bookmarks
     * @url DELETE /bookmark/:tas_uid
     *
     * @param string $tas_uid {@min 32}{@max 32}
     *
     */
    public function doDeleteBookmarkStartCase($tas_uid)
    {
        try {
            $userLoggedUid = $this->getUserId();
            $user = new \ProcessMaker\BusinessModel\User();
            $user->updateBookmark($userLoggedUid, $tas_uid, 'DELETE');
        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }

    /**
     * Batch reassign
     * @url POST /reassign
     *
     * @access protected
     * @class  AccessControl {@className \ProcessMaker\Services\Api\Cases}
     *
     * @param array $request_data
     *
     */
    public function doPostReassign($request_data)
    {
        try {
            $case = new \ProcessMaker\BusinessModel\Cases();
            $response = $case->doPostReassign($request_data);
            return $response;
        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }

}
