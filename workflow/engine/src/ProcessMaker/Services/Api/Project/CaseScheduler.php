<?php
namespace ProcessMaker\Services\Api\Project;

use \ProcessMaker\Services\Api;
use \Luracast\Restler\RestException;

/**
 * Project\CaseScheduler Api Controller
 *
 * @protected
 */
class CaseScheduler extends Api
{
    /**
     * @param string $prjUid {@min 32} {@max 32}
     *
     * @url GET /:prjUid/case-schedulers
     */
    public function doGetProjectCaseSchedulers($prjUid)
    {
        try {
            $caseScheduler = new \ProcessMaker\BusinessModel\CaseScheduler();
            $arrayData = $caseScheduler->getCaseSchedulers($prjUid);
            //Response
            $response = $arrayData;
        } catch (\Exception $e) {
            //response
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
        return $response;
    }

    /**
     * @param string $prjUid {@min 32} {@max 32}
     * @param string $caseSchedulerUid {@min 32} {@max 32}
     *
     * @url GET /:prjUid/case-scheduler/:caseSchedulerUid
     */
    public function doGetProjectCaseScheduler($prjUid, $caseSchedulerUid)
    {
        try {
            $caseScheduler = new \ProcessMaker\BusinessModel\CaseScheduler();
            $objectData = $caseScheduler->getCaseScheduler($prjUid, $caseSchedulerUid);
            //Response
            $response = $objectData;
        } catch (\Exception $e) {
            //response
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
        return $response;
    }

    /**
     * Creates a new Case Scheduler. 
     * 
     * @url POST /:prjUid/case-scheduler
     * @status 201
     * 
     * @param string $prjUid {@min 32} {@max 32}
     * @param CaseSchedulerStructure $request_data
     * 
     * @return mixed
     * @throws RestException 
     * 
     * @access protected
     * @class AccessControl {@permission PM_FACTORY}
     */
    public function doPostProjectCaseScheduler($prjUid, CaseSchedulerStructure $request_data =  null)
    {
        try {
            $userUid = $this->getUserId();
            $request_data = (array)($request_data);
            $caseScheduler = new \ProcessMaker\BusinessModel\CaseScheduler();
            $objectData = $caseScheduler->addCaseScheduler($prjUid, $request_data, $userUid);
            //Response
            $response = $objectData;
        } catch (\Exception $e) {
            //response
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
        return $response;
    }

    /**
     * Update project case scheduler.
     *
     * @url PUT /:prjUid/case-scheduler/:schUid
     *
     * @param string $prjUid {@min 32} {@max 32}
     * @param string $schUid {@min 32} {@max 32}
     * @param CaseSchedulerStructure $request_data
     *
     * @return array
     * @throws RestException
     *
     * @access protected
     * @class AccessControl {@permission PM_FACTORY}
     */
    public function doPutProjectCaseScheduler($prjUid, $schUid, CaseSchedulerStructure $request_data)
    {
        try {
            $userUid = $this->getUserId();
            $request_data = (array)($request_data);
            $caseScheduler = new \ProcessMaker\BusinessModel\CaseScheduler();
            $objectData = $caseScheduler->updateCaseScheduler($prjUid, $request_data, $userUid, $schUid);
            //Response
            $response = $objectData;
        } catch (\Exception $e) {
            //response
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
        return $response;
    }

    /**
     * @url DELETE /:prjUid/case-scheduler/:schUid
     * @access protected
     * @class AccessControl {@permission PM_FACTORY}
     *
     * @param string $prjUid {@min 32} {@max 32}
     * @param string $schUid {@min 32} {@max 32}
     *
     */
    public function doDeleteProjectCaseScheduler($prjUid, $schUid)
    {
        try {
            $caseScheduler = new \ProcessMaker\BusinessModel\CaseScheduler();
            $caseScheduler->deleteCaseScheduler($schUid);
        } catch (\Exception $e) {
            //response
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }
}

class CaseSchedulerStructure
{
    /**
     * @var string {@from body} {@min 0} {@max 100}
     */
    public $sch_name;

    /**
     * @var string {@from body} {@min 0} {@max 100}
     */
    public $sch_del_user_name;

    /**
     * @var string {@from body} {@min 0} {@max 100}
     */
    public $sch_del_user_uid;

    /**
     * @var string {@from body} {@min 32} {@max 32}
     */
    public $pro_uid;

    /**
     * @var string {@from body} {@min 32} {@max 32}
     */
    public $tas_uid;

    /**
     * @var string {@from body}
     */
    public $sch_time_next_run;

    /**
     * @var string {@from body}
     */
    public $sch_last_run_time;

    /**
     * @var string {@from body} {@choice ACTIVE,INACTIVE}
     */
    public $sch_state;

    /**
     * @var string {@from body} {@min 0} {@max 15}
     */
    public $sch_last_state;

    /**
     * @var string {@from body} {@min 32} {@max 32}
     */
    public $usr_uid;

    /**
     * @var string {@from body} {@choice 1,2,3,4,5}
     */
    public $sch_option;

    /**
     * @var string {@from body}
     */
    public $sch_start_time;

    /**
     * @var string {@from body}
     */
    public $sch_start_date;

    /**
     * @var string {@from body} {@min 0} {@max 5}
     */
    public $sch_days_perform_task;

    /**
     * @var string {@from body} {@min 0} {@max 4}
     */
    public $sch_every_days;

    /**
     * @var string {@from body} {@min 0} {@max 14}
     */
    public $sch_week_days;

    /**
     * @var string {@from body} {@choice 1,2,}
     */
    public $sch_start_day;

    /**
     * @var string {@from body}
     */
    public $sch_start_day_opt_1;

    /**
     * @var string {@from body} {@max 3}
     */
    public $sch_start_day_opt_2;
    /**
     * @var string {@from body} {@min 0} {@max 32}
     */
    public $sch_months;

    /**
     * @var string {@from body}
     */
    public $sch_end_date;

    /**
     * @var string {@from body} {@min 0} {@max 15}
     */
    public $sch_repeat_every;
}

