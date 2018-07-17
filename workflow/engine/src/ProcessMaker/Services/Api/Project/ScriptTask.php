<?php
namespace ProcessMaker\Services\Api\Project;

use \ProcessMaker\Services\Api;
use \Luracast\Restler\RestException;

/**
 * Project\ScriptTask Api Controller
 *
 * @protected
 */
class ScriptTask extends Api
{
    private $scriptTask;

    /**
     * Constructor of the class
     *
     * return void
     */
    public function __construct()
    {
        try {
            $this->scriptTask = new \ProcessMaker\BusinessModel\ScriptTask();

            $this->scriptTask->setFormatFieldNameInUppercase(false);
        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }

    /**
     * @url GET /:prj_uid/script-task/:scrtas_uid
     *
     * @param string $prj_uid        {@min 32}{@max 32}
     * @param string $scrtas_def_uid {@min 32}{@max 32}
     */
    public function doGetScriptTask($prj_uid, $scrtas_uid)
    {
        try {
            $response = $this->scriptTask->getScriptTask($scrtas_uid);

            return $response;
        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }

    /**
     * @url GET /:prj_uid/script-tasks
     *
     * @param string $prj_uid {@min 32}{@max 32}
     */
    public function doGetScriptTasks($prj_uid)
    {
        try {
            $response = $this->scriptTask->getScriptTasks($prj_uid);

            return $response;
        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }

    /**
     * @url GET /:prj_uid/script-task/activity/:act_uid
     *
     * @param string $prj_uid {@min 32}{@max 32}
     * @param string $act_uid {@min 32}{@max 32}
     */
    public function doGetScriptTaskActivity($prj_uid, $act_uid)
    {
        try {
            $response = $this->scriptTask->getScriptTaskByActivity($prj_uid, $act_uid);

            return $response;
        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }

    /**
     * Create script task for a project.
     * 
     * @url POST /:prj_uid/script-task
     * @status 201
     * 
     * @param string $prj_uid      {@min 32}{@max 32}
     * @param array  $request_data
     * 
     * @return array
     * @throws RestException
     * 
     * @access protected
     * @class AccessControl {@permission PM_FACTORY}
     */
    public function doPostScriptTask($prj_uid, array $request_data)
    {
        try {
            $arrayData = $this->scriptTask->create($prj_uid, $request_data);

            $response = $arrayData;

            return $response;
        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }

    /**
     * Update script task.
     *
     * @url PUT /:prj_uid/script-task/:scrtas_uid
     *
     * @param string $prj_uid      {@min 32}{@max 32}
     * @param string $scrtas_uid   {@min 32}{@max 32}
     * @param array  $request_data
     *
     * @throws RestException
     *
     * @access protected
     * @class AccessControl {@permission PM_FACTORY}
     */
    public function doPutScriptTask($prj_uid, $scrtas_uid, array $request_data)
    {
        try {
            $arrayData = $this->scriptTask->update($scrtas_uid, $request_data);
        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }

    /**
     * @url DELETE /:prj_uid/script-task/:scrtas_uid
     * @access protected
     * @class AccessControl {@permission PM_FACTORY}
     *
     * @param string $prj_uid    {@min 32}{@max 32}
     * @param string $scrtas_uid {@min 32}{@max 32}
     */
    public function doDeleteScriptTask($prj_uid, $scrtas_uid)
    {
        try {
            $this->scriptTask->delete($scrtas_uid);
        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }
}
