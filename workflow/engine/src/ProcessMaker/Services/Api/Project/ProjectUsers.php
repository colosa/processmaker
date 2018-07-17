<?php
namespace ProcessMaker\Services\Api\Project;

use \ProcessMaker\Services\Api;
use \Luracast\Restler\RestException;

/**
 * Project\ProjectUsers Api Controller
 *
 * @protected
 */
class ProjectUsers extends Api
{
    /**
     * @param string $prj_uid {@min 32} {@max 32}
     *
     * @url GET /:prj_uid/users
     */
    public function doGetProjectUsers($prj_uid)
    {
        try {
            $users = new \ProcessMaker\BusinessModel\ProjectUser();
            $arrayData = $users->getProjectUsers($prj_uid);
            //Response
            $response = $arrayData;
        } catch (\Exception $e) {
            //response
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
        return $response;
    }

    /**
     * @param string $prj_uid {@min 32} {@max 32}
     *
     * @url GET /:prj_uid/starting-tasks
     */
    public function doGetProjectStartingTasks($prj_uid)
    {
        try {
            $startingTasks = new \ProcessMaker\BusinessModel\ProjectUser();
            $arrayData = $startingTasks->getProjectStartingTasks($prj_uid);
            //Response
            $response = $arrayData;
        } catch (\Exception $e) {
            //response
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
        return $response;
    }

    /**
     * @param string $prj_uid {@min 32} {@max 32}
     * @param string $usr_uid {@min 32} {@max 32}
     *
     * @url GET /:prj_uid/user/:usr_uid/starting-tasks
     */
    public function doGetProjectStartingTaskUsers($prj_uid, $usr_uid)
    {
        try {
            $startingTasks = new \ProcessMaker\BusinessModel\ProjectUser();
            $arrayData = $startingTasks->getProjectStartingTaskUsers($prj_uid, $usr_uid);
            //Response
            $response = $arrayData;
        } catch (\Exception $e) {
            //response
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
        return $response;
    }

    /**
     * Return the user that can start a task.
     * 
     * @url POST /:prj_uid/ws/user/can-start-task
     * 
     * @param string $prj_uid {@min 32} {@max 32}
     * @param string $act_uid {@min 32} {@max 32}
     * @param wsUserCanStartTaskStructure $request_data
     * 
     * @return array
     * @throws RestException
     * 
     * @access protected
     * @class AccessControl {@permission PM_FACTORY}
     */
    public function doPostProjectWsUserCanStartTask($prj_uid, $act_uid = null, wsUserCanStartTaskStructure $request_data =  null)
    {
        try {
            $request_data = (array)($request_data);
            $user = new \ProcessMaker\BusinessModel\ProjectUser();
            $objectData = $user->projectWsUserCanStartTask($prj_uid, $act_uid, $request_data);
            //Response
            $response = $objectData;
        } catch (\Exception $e) {
            //response
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
        return $response;
    }
}

class wsUserCanStartTaskStructure
{
    /**
     * @var string {@from body}
     */
    public $username;
    /**
     * @var string {@from body}
     */
    public $password;
}

