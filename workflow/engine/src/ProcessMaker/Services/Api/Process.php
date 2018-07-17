<?php
namespace ProcessMaker\Services\Api;

use \ProcessMaker\Services\Api;
use \Luracast\Restler\RestException;

//TODO we need Refactor this class

/**
 * 
 * Process Api Controller
 *
 * @protected
 */
class Process extends Api
{
    public function index($proTitle = "", $proCategory = "", $start = 0, $limit = 25)
    {
        try {
            $arrayFilterData = array();

            if ($proTitle != "") {
                $arrayFilterData["processName"] = $proTitle;
            }

            if ($proCategory != "") {
                $arrayFilterData["category"] = $proCategory;
            }

            $process = new \ProcessMaker\BusinessModel\Process();
            $data = $process->loadAllProcess($arrayFilterData, $start, $limit);

            // Composing Response
            $response = array(
                'processes' => $data['data'],
                'totalCount' => $data['totalCount']
            );

            return $response;

        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }

    public function get($processUid)
    {
        $response = array();

        try {
            $process = new \ProcessMaker\BusinessModel\Process();

            $data = $process->loadProcess($processUid);

            //Response
            $response["success"] = true;
            $response["message"] = "Process load successfully";
            $response["data"] = $data;
        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }

        return $response;
    }

    /**
     * Create process
     * 
     * @param array $request_data
     * 
     * @return array
     * @throws RestException 
     * 
     * @access protected
     * @class AccessControl {@permission PM_FACTORY}
     */
    public function post($request_data = null)
    {
        defined('SYS_LANG') || define("SYS_LANG", $request_data["lang"]);

        try {
            $process = new \ProcessMaker\BusinessModel\Process();
            $userUid = $this->getUserId();

            return $process->createProcess($userUid, $request_data);

        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }

    /**
     * Update a process by UID.
     *
     * @param string $processUid
     * @param array $request_data
     *
     * @return array
     * @throws RestException
     *
     * @access protected
     * @class AccessControl {@permission PM_FACTORY}
     */
    public function put($processUid, $request_data = null)
    {
        $response = array();

        try {
            $process = new \ProcessMaker\BusinessModel\Process();
            $userUid = $this->getUserId();

            $data = $process->updateProcess($processUid, $userUid, $request_data);

            //Response
            $response["success"] = true;
            $response["message"] = "Process updated successfully";
            $response["data"] = $data;
        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }

        return $response;
    }

    public function delete($processUid, $checkCases = 1)
    {
        $response = array();

        try {
            $process = new \ProcessMaker\BusinessModel\Process();

            $result = $process->deleteProcess($processUid, (($checkCases && $checkCases == 1)? true : false));

            //Response
            $response["success"] = true;
            $response["message"] = "Process was deleted successfully";
        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }

        return $response;
    }


    /**
     * @url GET /:processUid/activity/:activityUid
     */
    public function getActivity($activityUid, $processUid)
    {
        $response = array();

        try {
            $task1 = new \Task();
            $task2 = new \ProcessMaker\BusinessModel\Task();

            $arrayData = $task1->load($activityUid);

            $arrayData = array(
                //"tas_uid"   => $activityUid,
                "tas_title" => $arrayData["TAS_TITLE"],
                "tas_description" => $arrayData["TAS_DESCRIPTION"],
                "tas_posx"  => $arrayData["TAS_POSX"],
                "tas_posy"  => $arrayData["TAS_POSY"],
                "tas_start" => $arrayData["TAS_START"],
                "_extended" => array(
                    "properties" => $task2->getProperties($activityUid, true),
                    "steps" => array(
                        "steps"       => $task2->getSteps($activityUid, true),
                        "conditions"  => "...", //lo mismo que steps //$task->getSteps()
                        "triggers"    => $task2->getTriggers($activityUid, true),
                        "users"       => $task2->getUsers($activityUid, 1, true),
                        "users_adhoc" => $task2->getUsers($activityUid, 2, true)
                    )
                )
            );

            //Response
            $response["success"] = true;
            $response["message"] = "Properties loaded successfully";
            $response["data"]    = array("activity" => $arrayData);
        } catch (\Exception $e) {
            //Response
            $response["success"] = false;
            $response["message"] = $e->getMessage();
        }

        return $response;
    }

    /**
     * @url GET /:processUid/activity/:activityUid/properties
     */
    public function getActivityProperties($activityUid, $processUid)
    {
        $response = array();

        try {
            $task1 = new \Task();

            $arrayData = $task1->load($activityUid);

            $arrayData = array(
                //"tas_uid"   => $activityUid,
                "tas_title" => $arrayData["TAS_TITLE"],
                "tas_description" => $arrayData["TAS_DESCRIPTION"],
                "tas_posx"  => $arrayData["TAS_POSX"],
                "tas_posy"  => $arrayData["TAS_POSY"],
                "tas_start" => $arrayData["TAS_START"]
            );

            //Response
            $response["success"] = true;
            $response["message"] = "Properties loaded successfully";
            $response["data"]    = array("activity" => $arrayData);
        } catch (\Exception $e) {
            //Response
            $response["success"] = false;
            $response["message"] = $e->getMessage();
        }

        return $response;
    }

    /**
     * @url GET /:processUid/activity/:activityUid/extended
     */
    public function getActivityExtended($activityUid, $processUid)
    {
        $response = array();

        try {
            $task2 = new \ProcessMaker\BusinessModel\Task();

            $arrayData = array(
                "_extended" => array(
                    "properties" => $task2->getProperties($activityUid, true),
                    "steps" => array(
                        "steps"       => $task2->getSteps($activityUid, true),
                        "conditions"  => "...", //lo mismo que steps //$task->getSteps()
                        "triggers"    => $task2->getTriggers($activityUid, true),
                        "users"       => $task2->getUsers($activityUid, 1, true),
                        "users_adhoc" => $task2->getUsers($activityUid, 2, true)
                    )
                )
            );

            //Response
            $response["success"] = true;
            $response["message"] = "Extended loaded successfully";
            $response["data"]    = array("activity" => $arrayData);
        } catch (\Exception $e) {
            //Response
            $response["success"] = false;
            $response["message"] = $e->getMessage();
        }

        return $response;
    }

    /**
     * @url GET /:processUid/activity/:activityUid/steps/list
     */
    public function getActivityStepsList($activityUid, $processUid, $start = 0, $limit = 10)
    {
        $response = array();

        try {
            $task = new \ProcessMaker\BusinessModel\Task();

            $data = $task->getStepsList($activityUid, $processUid, false, $start, $limit);

            //Response
            $response["success"] = true;
            $response["message"] = "Steps loaded successfully";
            $response["data"]    = $data;
        } catch (\Exception $e) {
            //Response
            $response["success"] = false;
            $response["message"] = $e->getMessage();
        }

        return $response;
    }
}

