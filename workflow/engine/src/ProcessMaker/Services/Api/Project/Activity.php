<?php
namespace ProcessMaker\Services\Api\Project;

use \Exception;
use \Luracast\Restler\RestException;
use \ProcessMaker\BusinessModel\Task;
use \ProcessMaker\Project\Adapter\BpmnWorkflow;
use \ProcessMaker\Services\Api;

/**
 * Project\Activity Api Controller
 *
 * @protected
 */
class Activity extends Api
{
    /**
     * @param string $prj_uid {@min 32} {@max 32}
     * @param string $act_uid {@min 32} {@max 32}
     * @param string $filter {@choice definition,,properties}
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     * @return array
     *
     * @url GET /:prj_uid/activity/:act_uid
     */
    public function doGetProjectActivity($prj_uid, $act_uid, $filter = '')
    {
        try {
            $hiddenFields = array('tas_start', 'pro_uid', 'tas_uid', 'tas_delay_type', 'tas_temporizer', 'tas_alert',
                'tas_mi_instance_variable', 'tas_mi_complete_variable', 'tas_assign_location',
                'tas_assign_location_adhoc', 'tas_last_assigned', 'tas_user', 'tas_can_upload', 'tas_view_upload',
                'tas_view_additional_documentation', 'tas_can_cancel', 'tas_owner_app', 'tas_can_pause',
                'tas_can_send_message', 'tas_can_delete_docs', 'tas_self_service', 'tas_to_last_user',
                'tas_derivation', 'tas_posx', 'tas_posy', 'tas_width', 'tas_height', 'tas_color', 'tas_evn_uid',
                'tas_boundary', 'tas_def_proc_code', 'stg_uid'
            );
            $definition = array();
            $properties = array();

            if ($filter == '' || $filter == 'definition') {
                // DEFINITION
                $definition = array();
                $response['definition'] = $definition;
            }

            if ($filter == '' || $filter == 'properties') {
                // PROPERTIES
                $task = new \ProcessMaker\BusinessModel\Task();
                $properties = $task->getProperties($prj_uid, $act_uid, true, false);
                foreach ($properties as $key => $value) {
                    if (in_array($key, $hiddenFields)) {
                        unset($properties[$key]);
                    }
                }
                $response['properties'] = $properties;
            }

            return $response;
        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }


    /**
     * @param string $pro_uid {@min 32} {@max 32}
     * @param string $tas_uid {@min 32} {@max 32}
     * @param string $filter {@choice definition,,properties}
     *
     * @author Gustavo Cruz <gustavo.cruz@colosa.com>
     * @copyright Colosa - Bolivia
     * @return array
     *
     * @url GET /:pro_uid/activity/:tas_uid/feature-configuration
     */
    public function doGetProjectActivityFeatureConfiguration($pro_uid, $tas_uid, $filter = '')
    {
        try {
            $configurations = array();
            /*----------------------------------********---------------------------------*/
            return $configurations;
        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }

    /**
     * Update an activity.
     *
     * @url PUT /:prj_uid/activity/:act_uid
     *
     * @param string $prj_uid {@min 32} {@max 32}
     * @param string $act_uid {@min 32} {@max 32}
     * @param ActivityPropertiesStructure $properties {@from body}
     * @param array $request_data
     *
     * @return void
     * @throws RestException
     *
     * @access protected
     * @class AccessControl {@permission PM_FACTORY}
     */
    public function doPutProjectActivity($prj_uid, $act_uid, ActivityPropertiesStructure $properties, $request_data =  array())
    {
        try {
            if (isset($request_data['properties']['tas_start'])) {
                unset($request_data['properties']['tas_start']);
            }
            $task = new \ProcessMaker\BusinessModel\Task();
            $properties = $task->updateProperties($prj_uid, $act_uid, $request_data);
            /*----------------------------------********---------------------------------*/
        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }



    /**
     * This method remove an activity and all related components
     * @param string $prj_uid {@min 32} {@max 32}
     * @param string $act_uid {@min 32} {@max 32}
     * @return array
     * @access protected
     * @class  AccessControl {@permission PM_FACTORY}
     * @url DELETE /:prj_uid/activity/:act_uid
     */
    public function doDeleteProjectActivity($prj_uid, $act_uid)
    {
        try {
            $task = new Task();
            $task->setFormatFieldNameInUppercase(false);
            $task->setArrayParamException(array("taskUid" => "act_uid"));

            $response = $task->hasPendingCases(array("act_uid" => $act_uid, "case_type" => "assigned"));
            if ($response->result !== false) {
                $project = new BpmnWorkflow();
                $prj = $project->load($prj_uid);
                $prj->removeActivity($act_uid);
            } else {
                throw new RestException(403, $response->message);
            }
        } catch (Exception $e) {
            $resCode = $e->getCode() == 0 ? Api::STAT_APP_EXCEPTION : $e->getCode();
            throw new RestException($resCode, $e->getMessage());
        }
    }

    /**
     * @url GET /:prj_uid/activity/:act_uid/steps
     *
     * @param string $act_uid {@min 32}{@max 32}
     * @param string $prj_uid {@min 32}{@max 32}
     */
    public function doGetActivitySteps($act_uid, $prj_uid)
    {
        try {
            $task = new \ProcessMaker\BusinessModel\Task();
            $task->setFormatFieldNameInUppercase(false);
            $task->setArrayParamException(array("taskUid" => "act_uid", "stepUid" => "step_uid"));

            $response = $task->getSteps($act_uid);

            $step = new \ProcessMaker\Services\Api\Project\Activity\Step();

            for ($i = 0; $i < count($response); $i++) {
                $response[$i]["triggers"] = $step->doGetActivityStepTriggers($response[$i]["step_uid"], $act_uid, $prj_uid);
            }

            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url GET /:prj_uid/activity/:act_uid/available-steps
     *
     * @param string $act_uid {@min 32}{@max 32}
     * @param string $prj_uid {@min 32}{@max 32}
     */
    public function doGetActivityAvailableSteps($act_uid, $prj_uid)
    {
        try {
            $task = new \ProcessMaker\BusinessModel\Task();
            $task->setFormatFieldNameInUppercase(false);
            $task->setArrayParamException(array("taskUid" => "act_uid"));

            $response = $task->getAvailableSteps($act_uid);

            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }
    
    /**
     * Get activity validate self service.
     *
     * @url PUT /:prj_uid/activity/validate-active-cases
     *
     * @param string $prj_uid {@min 32}{@max 32}
     * @param array $request_data
     *
     * @return \StdClass
     * @throws RestException
     *
     * @access protected
     * @class AccessControl {@permission PM_FACTORY}
     */
    public function doGetActivityValidateSelfService($prj_uid, $request_data =  array())
    {
        try {
            $task = new \ProcessMaker\BusinessModel\Task();
            $task->setFormatFieldNameInUppercase(false);
            $task->setArrayParamException(array("taskUid" => "act_uid"));

            $response = $task->hasPendingCases($request_data);

            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }
}


class ActivityPropertiesStructure
{
    /**
     * @var string {@from body} {@min 1} {@max 200}
     */
    public $tas_title;

    /**
     * @var string {@from body} {@required false}
     */
    public $tas_description;

    /**
     * @var string {@from body} {@required false}
     */
    public $tas_priority_variable;

    /**
     * @var string {@from body} {@required false}
     */
    public $tas_derivation_screen_tpl;

    /**
     * @var string {@from body} {@choice TRUE,FALSE} {@required false}
     */
    public $tas_start;

    /**
     * @var string {@from body} {@choice BALANCED,MANUAL,EVALUATE,REPORT_TO,SELF_SERVICE,SELF_SERVICE_EVALUATE,MULTIPLE_INSTANCE,MULTIPLE_INSTANCE_VALUE_BASED} {@required false}
     */
    public $tas_assign_type;

    /**
     * @var string {@from body} {@required false}
     */
    public $tas_assign_variable;

    /**
     * @var string {@from body} {@required false}
     */
    public $tas_group_variable;

    /**
     * @var int {@from body} {@required false}
     */
    public $tas_selfservice_time;

    /**
     * @var int {@from body} {@choice 1,0}
     */
    public $tas_selfservice_timeout;

    /**
     * @var string {@from body} {@choice DAYS,,HOURS,MINUTES} {@required false}
     */
    public $tas_selfservice_time_unit;

    /**
     * @var string {@from body} {@min 0} {@max 32} {@required false}
     */
    public $tas_selfservice_trigger_uid;

    /**
     * @var string {@from body} {@choice EVERY_TIME,ONCE}
     */
    public $tas_selfservice_execution;

    /**
     * @var string {@from body} {@choice TRUE,FALSE}
     */
    public $tas_transfer_fly;

    /**
     * @var int {@from body} {@required false}
     */
    public $tas_duration;

    /**
     * @var string {@from body} {@required false}
     */
    public $tas_timeunit;

    /**
     * @var string {@from body} {@required false}
     */
    public $tas_type_day;

    /**
     * @var string {@from body} {@required false}
     */
    public $tas_calendar;

    /**
     * @var string {@from body} {@choice NORMAL,ADHOC,SUBPROCESS}
     */
    public $tas_type;

    /**
     * @var string {@from body} {@required false}
     */
    public $tas_def_title;

    /**
     * @var string {@from body} {@required false}
     */
    public $tas_def_description;

    /**
     * @var string {@from body} {@choice TRUE,FALSE}
     */
    public $tas_send_last_email;

    /**
     * @var string {@from body} {@required false}
     */
    public $tas_def_subject_message;

    /**
     * @var string {@from body} {@required false}
     */
    public $tas_def_message_type;

    /**
     * @var string {@from body} {@required false}
     */
    public $tas_def_message;

    /**
     * @var string {@from body} {@required false}
     */
    public $tas_def_message_template;

    /**
     * @var int {@from body} {@required false}
     */
    public $tas_not_email_from_format;
}

