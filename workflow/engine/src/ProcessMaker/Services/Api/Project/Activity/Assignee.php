<?php
namespace ProcessMaker\Services\Api\Project\Activity;

use \ProcessMaker\Services\Api;
use \Luracast\Restler\RestException;

/**
 * Project\Activity\Assignee Api Controller
 *
 * @protected
 */
class Assignee extends Api
{
    private $task;

    /**
     * Constructor of the class
     *
     * return void
     */
    public function __construct()
    {
        try {
            $this->task = new \ProcessMaker\BusinessModel\Task();
        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }

    /**
     * @url GET /:prj_uid/activity/:act_uid/assignee/paged
     * @url GET /:prj_uid/activity/:act_uid/assignee
     *
     * @param string $prj_uid {@min 32}{@max 32}
     * @param string $act_uid {@min 32}{@max 32}
     */
    public function doGetActivityAssignees($prj_uid, $act_uid, $filter = null, $lfilter = null, $rfilter = null, $start = null, $limit = null, $type = null)
    {
        try {
            $arrayFilterData = array(
                "filter"       => (!is_null($filter))? $filter : ((!is_null($lfilter))? $lfilter : ((!is_null($rfilter))? $rfilter : null)),
                "filterOption" => (!is_null($filter))? ""      : ((!is_null($lfilter))? "LEFT"   : ((!is_null($rfilter))? "RIGHT"  : ""))
            );

            $response = $this->task->getTaskAssignees($prj_uid, $act_uid, "ASSIGNEE", 1, $arrayFilterData, $start, $limit, $type);

            return (preg_match("/^.*\/paged.*$/", $this->restler->url))? $response : $response["data"];
        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }

    /**
     * @url GET /:prj_uid/activity/:act_uid/available-assignee/paged
     * @url GET /:prj_uid/activity/:act_uid/available-assignee
     *
     * @param string $prj_uid {@min 32}{@max 32}
     * @param string $act_uid {@min 32}{@max 32}
     */
    public function doGetActivityAvailableAssignees($prj_uid, $act_uid, $filter = null, $lfilter = null, $rfilter = null, $start = null, $limit = null, $type = null)
    {
        try {
            $arrayFilterData = array(
                "filter"       => (!is_null($filter))? $filter : ((!is_null($lfilter))? $lfilter : ((!is_null($rfilter))? $rfilter : null)),
                "filterOption" => (!is_null($filter))? ""      : ((!is_null($lfilter))? "LEFT"   : ((!is_null($rfilter))? "RIGHT"  : ""))
            );

            $response = $this->task->getTaskAssignees($prj_uid, $act_uid, "AVAILABLE", 1, $arrayFilterData, $start, $limit, $type);

            return (preg_match("/^.*\/paged.*$/", $this->restler->url))? $response : $response["data"];
        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }

    /**
     * @url GET /:prjUid/activity/:actUid/assignee/:aasUid
     *
     * @param string $prjUid {@min 32} {@max 32}
     * @param string $actUid {@min 32} {@max 32}
     * @param string $aasUid {@min 32} {@max 32}
     */
    public function doGetActivityAssignee($prjUid, $actUid, $aasUid)
    {
        try {
            $response = $this->task->getTaskAssignee($prjUid, $actUid, $aasUid);

            return $response;
        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }

    /**
     * Assign an user or group to a task.
     * 
     * @url POST /:prjUid/activity/:actUid/assignee
     * @status 201
     * 
     * @param string $prjUid {@min 32} {@max 32}
     * @param string $actUid {@min 32} {@max 32}
     * @param string $aas_uid {@min 32} {@max 32}
     * @param string $aas_type {@choice user,group}
     * 
     * @return void
     * @throws RestException 
     * 
     * @access protected
     * @class AccessControl {@permission PM_FACTORY}
     */
    public function doPostActivityAssignee($prjUid, $actUid, $aas_uid, $aas_type)
    {
        try {
            $this->task->addTaskAssignee($prjUid, $actUid, $aas_uid, $aas_type);
        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }

    /**
     * @url DELETE /:prjUid/activity/:actUid/assignee/:aasUid
     * @access protected
     * @class AccessControl {@permission PM_FACTORY}
     *
     * @param string $prjUid {@min 32} {@max 32}
     * @param string $actUid {@min 32} {@max 32}
     * @param string $aasUid {@min 32} {@max 32}
     */
    public function doDeleteActivityAssignee($prjUid, $actUid, $aasUid)
    {
        try {
            $this->task->removeTaskAssignee($prjUid, $actUid, $aasUid);
        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }

    /**
     * @url GET /:prj_uid/activity/:act_uid/adhoc-assignee/paged
     * @url GET /:prj_uid/activity/:act_uid/adhoc-assignee
     *
     * @param string $prj_uid {@min 32}{@max 32}
     * @param string $act_uid {@min 32}{@max 32}
     */
    public function doGetActivityAdhocAssignees($prj_uid, $act_uid, $filter = null, $lfilter = null, $rfilter = null, $start = null, $limit = null, $type = null)
    {
        try {
            $arrayFilterData = array(
                "filter"       => (!is_null($filter))? $filter : ((!is_null($lfilter))? $lfilter : ((!is_null($rfilter))? $rfilter : null)),
                "filterOption" => (!is_null($filter))? ""      : ((!is_null($lfilter))? "LEFT"   : ((!is_null($rfilter))? "RIGHT"  : ""))
            );

            $response = $this->task->getTaskAssignees($prj_uid, $act_uid, "ASSIGNEE", 2, $arrayFilterData, $start, $limit, $type);

            return (preg_match("/^.*\/paged.*$/", $this->restler->url))? $response : $response["data"];
        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }

    /**
     * @url GET /:prj_uid/activity/:act_uid/adhoc-available-assignee/paged
     * @url GET /:prj_uid/activity/:act_uid/adhoc-available-assignee
     *
     * @param string $prj_uid {@min 32}{@max 32}
     * @param string $act_uid {@min 32}{@max 32}
     */
    public function doGetActivityAdhocAvailableAssignees($prj_uid, $act_uid, $filter = null, $lfilter = null, $rfilter = null, $start = null, $limit = null, $type = null)
    {
        try {
            $arrayFilterData = array(
                "filter"       => (!is_null($filter))? $filter : ((!is_null($lfilter))? $lfilter : ((!is_null($rfilter))? $rfilter : null)),
                "filterOption" => (!is_null($filter))? ""      : ((!is_null($lfilter))? "LEFT"   : ((!is_null($rfilter))? "RIGHT"  : ""))
            );

            $response = $this->task->getTaskAssignees($prj_uid, $act_uid, "AVAILABLE", 2, $arrayFilterData, $start, $limit, $type);

            return (preg_match("/^.*\/paged.*$/", $this->restler->url))? $response : $response["data"];
        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }

    /**
     * @url GET /:prjUid/activity/:actUid/adhoc-assignee/:aasUid
     *
     * @param string $prjUid {@min 32} {@max 32}
     * @param string $actUid {@min 32} {@max 32}
     * @param string $assUid {@min 32} {@max 32}
     */
    public function doGetActivityAdhocAssignee($prjUid, $actUid, $aasUid)
    {
        try {
            $response = $this->task->getTaskAdhocAssignee($prjUid, $actUid, $aasUid);

            return $response;
        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }

    /**
     * Assign an user or group to a task (Ad-hoc assignment).
     * 
     * @url POST /:prjUid/activity/:actUid/adhoc-assignee
     * @status 201
     * 
     * @param string $prjUid {@min 32} {@max 32}
     * @param string $actUid {@min 32} {@max 32}
     * @param string $ada_uid {@min 32} {@max 32}
     * @param string $ada_type {@choice user,group}
     * 
     * @return void
     * @throws RestException 
     * 
     * @access protected
     * @class AccessControl {@permission PM_FACTORY}
     */
    public function doPostActivityAdhocAssignee($prjUid, $actUid, $ada_uid, $ada_type)
    {
        try {
            $this->task->addTaskAdhocAssignee($prjUid, $actUid, $ada_uid, $ada_type);
        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }

    /**
     * @url DELETE /:prjUid/activity/:actUid/adhoc-assignee/:adaUid
     * @access protected
     * @class AccessControl {@permission PM_FACTORY}
     *
     * @param string $prjUid {@min 32} {@max 32}
     * @param string $actUid {@min 32} {@max 32}
     * @param string $adaUid {@min 32} {@max 32}
     */
    public function doDeleteActivityAdhocAssignee($prjUid, $actUid, $adaUid)
    {
        try {
            $this->task->removeTaskAdhocAssignee($prjUid, $actUid, $adaUid);
        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }

    /**
     * @url GET /:prjUid/activity/:actUid/assignee/all
     *
     * @param string $prjUid {@min 32} {@max 32}
     * @param string $actUid {@min 32} {@max 32}
     * @param string $filter
     * @param int    $start
     * @param int    $limit
     * @param string $type
     */
    public function doGetActivityAssigneesAll($prjUid, $actUid, $filter = null, $start = null, $limit = null, $type = null)
    {
        try {
            $response = $this->task->getTaskAssigneesAll($prjUid, $actUid, $filter, $start, $limit, $type);

            return $response;
        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }

    /**
     * @url GET /:prjUid/activity/:actUid/adhoc-assignee/all
     *
     * @param string $prjUid {@min 32} {@max 32}
     * @param string $actUid {@min 32} {@max 32}
     * @param string $filter
     * @param int    $start
     * @param int    $limit
     * @param string $type
     */
    public function doGetActivityAdhocAssigneesAll($prjUid, $actUid, $filter = null, $start = null, $limit = null, $type = null)
    {
        $response = array();
        try {
            $response = $this->task->getTaskAdhocAssigneesAll($prjUid, $actUid, $filter, $start, $limit, $type);

            return $response;
        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }
}

