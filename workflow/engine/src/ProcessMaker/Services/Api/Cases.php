<?php
namespace ProcessMaker\Services\Api;

use \ProcessMaker\Services\Api;
use \Luracast\Restler\RestException;


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
    /**
     * Get list Cases To Do
     *
     * @param string $start {@from path}
     * @param string $limit {@from path}
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
            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * Get list Cases To Do with paged
     *
     * @param string $start {@from path}
     * @param string $limit {@from path}
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
            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * Get list Cases Draft
     *
     * @param string $start {@from path}
     * @param string $limit {@from path}
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
            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * Get list Cases Draft with paged
     *
     * @param string $start {@from path}
     * @param string $limit {@from path}
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
            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * Get list Cases Participated
     *
     * @param string $start {@from path}
     * @param string $limit {@from path}
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
            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * Get list Cases Participated with paged
     *
     * @param string $start {@from path}
     * @param string $limit {@from path}
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
            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * Get list Cases Unassigned
     *
     * @param string $start {@from path}
     * @param string $limit {@from path}
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
            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * Get list Cases Unassigned with paged
     *
     * @param string $start {@from path}
     * @param string $limit {@from path}
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
            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * Get list Cases Paused
     *
     * @param string $start {@from path}
     * @param string $limit {@from path}
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
            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * Get list Cases Paused with paged
     *
     * @param string $start {@from path}
     * @param string $limit {@from path}
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
            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * Get list Cases Advanced Search
     *
     * @param string $start {@from path}
     * @param string $limit {@from path}
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
            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * Get list Cases Advanced Search with Paged
     *
     * @param string $start {@from path}
     * @param string $limit {@from path}
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
            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url GET /:app_uid
     *
     * @param string $app_uid {@min 32}{@max 32}
     */
    public function doGetCaseInfo($app_uid)
    {
        try {
            $case = new \ProcessMaker\BusinessModel\Cases();
            $case->setFormatFieldNameInUppercase(false);

            return $case->getCaseInfo($app_uid, $this->getUserId());
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

            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url POST
     *
     * @param string $pro_uid {@from body} {@min 32}{@max 32}
     * @param string $tas_uid {@from body} {@min 32}{@max 32}
     * @param array $variables {@from body}
     *
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
     * @url POST /impersonate
     *
     * @param string $pro_uid {@from body} {@min 32}{@max 32}
     * @param string $usr_uid {@from body} {@min 32}{@max 32}
     * @param string $tas_uid {@from body} {@min 32}{@max 32}
     * @param array $variables {@from body}
     *
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
     * @url PUT /:app_uid/reassign-case
     *
     * @param string $app_uid {@min 32}{@max 32}
     * @param string $usr_uid_source {@from body} {@min 32}{@max 32}
     * @param string $usr_uid_target {@from body} {@min 32}{@max 32}
     * @param string $del_index {@from body}
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
     * Route Case
     * @url PUT /:app_uid/route-case
     *
     * @param string $app_uid {@min 32}{@max 32}
     * @param string $del_index {@from body}
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
     * @param string $cas_uid {@min 1}{@max 32}
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @url PUT /:cas_uid/cancel
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
     * @param string $cas_uid {@min 1}{@max 32}
     * @param string $unpaused_date {@from body}
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @url PUT /:cas_uid/pause
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
     * @param string $cas_uid {@min 1}{@max 32}
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @url PUT /:cas_uid/unpause
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
     * Unpause Case
     *
     * @param string $cas_uid {@min 1}{@max 32}
     * @param string $tri_uid {@min 1}{@max 32}
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @url PUT /:cas_uid/execute-trigger/:tri_uid
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
     *
     * @param string $cas_uid {@min 1}{@max 32}
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @url DELETE /:cas_uid
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
     * @param string $app_uid {@min 1}{@max 32}
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @url GET /:app_uid/variables
     */
    public function doGetCaseVariables($app_uid)
    {
        try {
            $usr_uid = $this->getUserId();
            $cases = new \ProcessMaker\BusinessModel\Cases();
            $response = $cases->getCaseVariables($app_uid, $usr_uid);
            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * Put Case Variables
     *
     * @param string $app_uid {@min 1}{@max 32}
     * @param array $request_data
     * @param string $dyn_uid {@from path}
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @url PUT /:app_uid/variable
     */
    public function doPutCaseVariables($app_uid, $request_data, $dyn_uid = '')
    {
        try {
            $usr_uid = $this->getUserId();
            $cases = new \ProcessMaker\BusinessModel\Cases();
            $cases->setCaseVariables($app_uid, $request_data, $dyn_uid, $usr_uid);
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * Get Case Notes
     *
     * @param string $start {@from path}
     * @param string $limit {@from path}
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
            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * Get Case Notes with Paged
     *
     * @param string $start {@from path}
     * @param string $limit {@from path}
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
            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * Put Case Variables
     *
     * @param string $app_uid {@min 1}{@max 32}
     * @param string $note_content {@min 1}{@max 500}
     * @param int $send_mail {@choice 1,0}
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @url POST /:app_uid/note
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

            return $response;
        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }

    /**
     * Execute triggers
     *
     * @param string $app_uid {@min 1}{@max 32}
     * @param int $del_index {@from body}
     * @param string $obj_type {@from body}
     * @param string $obj_uid {@from body}
     *
     * @copyright Colosa - Bolivia
     *
     * @url PUT /:app_uid/execute-triggers
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
}

