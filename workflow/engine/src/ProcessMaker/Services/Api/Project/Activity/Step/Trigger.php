<?php
namespace ProcessMaker\Services\Api\Project\Activity\Step;

use \ProcessMaker\Services\Api;
use \Luracast\Restler\RestException;

/**
 * Project\Activity\Step\Trigger Api Controller
 *
 * @protected
 */
class Trigger extends Api
{
    /**
     * @url GET /:prj_uid/activity/:act_uid/step/:step_uid/trigger/:tri_uid/:type
     *
     * @param string $tri_uid
     * @param string $step_uid
     * @param string $act_uid
     * @param string $prj_uid
     * @param string $type     {@from body}{@choice before,after}
     */
    public function doGetActivityStepTrigger($tri_uid, $step_uid, $act_uid, $prj_uid, $type)
    {
        try {
            $stepTrigger = new \ProcessMaker\BusinessModel\Step\Trigger();

            $response = $stepTrigger->getTrigger($step_uid, strtoupper($type), $act_uid, $tri_uid);

            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * Assign a trigger to a step.
     * 
     * @url POST /:prj_uid/activity/:act_uid/step/:step_uid/trigger
     * @status 201
     * 
     * @param string $step_uid
     * @param string $act_uid
     * @param string $prj_uid
     * @param StepTriggerPostStructure $request_data
     * 
     * @return array
     * @throws RestException 
     * 
     * @access protected
     * @class AccessControl {@permission PM_FACTORY}
     */
    public function doPostActivityStepTrigger($step_uid, $act_uid, $prj_uid, StepTriggerPostStructure $request_data = null)
    {
        try {
            $request_data = (array)($request_data);

            $stepTrigger = new \ProcessMaker\BusinessModel\Step\Trigger();

            $arrayData = $stepTrigger->create($step_uid, $request_data["st_type"], $act_uid, $request_data["tri_uid"], $request_data);
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * Update activity step trigger.
     *
     * @url PUT /:prj_uid/activity/:act_uid/step/:step_uid/trigger/:tri_uid
     *
     * @param string $tri_uid
     * @param string $step_uid
     * @param string $act_uid
     * @param string $prj_uid
     * @param StepTriggerPutStructure $request_data
     *
     * @throws RestException
     *
     * @access protected
     * @class AccessControl {@permission PM_FACTORY}
     */
    public function doPutActivityStepTrigger($tri_uid, $step_uid, $act_uid, $prj_uid, StepTriggerPutStructure $request_data = null)
    {
        try {
            $request_data = (array)($request_data);

            $stepTrigger = new \ProcessMaker\BusinessModel\Step\Trigger();

            $arrayData = $stepTrigger->update($step_uid, $request_data["st_type"], $act_uid, $tri_uid, $request_data);
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url DELETE /:prj_uid/activity/:act_uid/step/:step_uid/trigger/:tri_uid/:type
     * @access protected
     * @class AccessControl {@permission PM_FACTORY}
     *
     * @param string $tri_uid
     * @param string $step_uid
     * @param string $act_uid
     * @param string $prj_uid
     * @param string $type     {@from body}{@choice before,after}
     */
    public function doDeleteActivityStepTrigger($tri_uid, $step_uid, $act_uid, $prj_uid, $type)
    {
        try {
            $stepTrigger = new \ProcessMaker\BusinessModel\Step\Trigger();

            $stepTrigger->delete($step_uid, strtoupper($type), $act_uid, $tri_uid);
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    //Step "Assign Task"

    /**
     * @url GET /:prj_uid/activity/:act_uid/step/trigger/:tri_uid/:type
     *
     * @param string $tri_uid
     * @param string $act_uid
     * @param string $prj_uid
     * @param string $type    {@from body}{@choice before-assignment,before-routing,after-routing}
     */
    public function doGetActivityStepAssignTaskTrigger($tri_uid, $act_uid, $prj_uid, $type)
    {
        try {
            $stepTrigger = new \ProcessMaker\BusinessModel\Step\Trigger();

            $response = $stepTrigger->getTrigger("", strtoupper(str_replace("-", "_", $type)), $act_uid, $tri_uid);

            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * Assign a trigger to the "Assignment" or "Routing" step section of an activity.
     * 
     * @url POST /:prj_uid/activity/:act_uid/step/trigger
     * @status 201
     * 
     * @param string $act_uid
     * @param string $prj_uid
     * @param StepAssignTaskTriggerPostStructure $request_data
     * 
     * @return array
     * @throws RestException 
     * 
     * @access protected
     * @class AccessControl {@permission PM_FACTORY}
     */
    public function doPostActivityStepAssignTaskTrigger($act_uid, $prj_uid, StepAssignTaskTriggerPostStructure $request_data = null)
    {
        try {
            $request_data = (array)($request_data);

            $stepTrigger = new \ProcessMaker\BusinessModel\Step\Trigger();

            $arrayData = $stepTrigger->create("", $request_data["st_type"], $act_uid, $request_data["tri_uid"], $request_data);
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * Update activity step assign task trigger.
     *
     * @url PUT /:prj_uid/activity/:act_uid/step/trigger/:tri_uid
     *
     * @param string $tri_uid
     * @param string $act_uid
     * @param string $prj_uid
     * @param StepAssignTaskTriggerPutStructure $request_data
     *
     * @throws RestException
     *
     * @access protected
     * @class AccessControl {@permission PM_FACTORY}
     */
    public function doPutActivityStepAssignTaskTrigger($tri_uid, $act_uid, $prj_uid, StepAssignTaskTriggerPutStructure $request_data = null)
    {
        try {
            $request_data = (array)($request_data);

            $stepTrigger = new \ProcessMaker\BusinessModel\Step\Trigger();

            $arrayData = $stepTrigger->update("", $request_data["st_type"], $act_uid, $tri_uid, $request_data);
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url DELETE /:prj_uid/activity/:act_uid/step/trigger/:tri_uid/:type
     * @access protected
     * @class AccessControl {@permission PM_FACTORY}
     *
     * @param string $tri_uid
     * @param string $act_uid
     * @param string $prj_uid
     * @param string $type    {@from body}{@choice before-assignment,before-routing,after-routing}
     */
    public function doDeleteActivityStepAssignTaskTrigger($tri_uid, $act_uid, $prj_uid, $type)
    {
        try {
            $stepTrigger = new \ProcessMaker\BusinessModel\Step\Trigger();

            $stepTrigger->delete("", strtoupper(str_replace("-", "_", $type)), $act_uid, $tri_uid);
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }
}

class StepTriggerPostStructure
{
    /**
     * @var string {@from body}{@choice BEFORE,AFTER}{@required true}
     */
    public $st_type;

    /**
     * @var string {@from body}{@min 32}{@max 32}{@required true}
     */
    public $tri_uid;

    /**
     * @var string {@required false}
     */
    public $st_condition;

    /**
     * @var int {@from body}{@min 1} {@required false}
     */
    public $st_position;
}

class StepTriggerPutStructure
{
    /**
     * @var string {@from body}{@choice BEFORE,AFTER}{@required true}
     */
    public $st_type;

    /**
     * @var string {@required false}
     */
    public $st_condition;

    /**
     * @var int {@from body}{@min 1} {@required false}
     */
    public $st_position;
}

class StepAssignTaskTriggerPostStructure
{
    /**
     * @var string {@from body}{@choice BEFORE_ASSIGNMENT,BEFORE_ROUTING,AFTER_ROUTING}{@required true}
     */
    public $st_type;

    /**
     * @var string {@from body}{@min 32}{@max 32}{@required true}
     */
    public $tri_uid;

    /**
     * @var string {@required false}
     */
    public $st_condition;

    /**
     * @var int {@from body}{@min 1} {@required false}
     */
    public $st_position;
}

class StepAssignTaskTriggerPutStructure
{
    /**
     * @var string {@from body}{@choice BEFORE_ASSIGNMENT,BEFORE_ROUTING,AFTER_ROUTING}{@required true}
     */
    public $st_type;

    /**
     * @var string {@required false}
     */
    public $st_condition;

    /**
     * @var int {@from body}{@min 1} {@required false}
     */
    public $st_position;
}

