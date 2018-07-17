<?php
namespace ProcessMaker\Services\Api\Project;

use \ProcessMaker\Services\Api;
use \Luracast\Restler\RestException;

/**
 * Project\CaseTracker Api Controller
 *
 * @protected
 */
class CaseTracker extends Api
{
    /**
     * @url GET /:prj_uid/case-tracker/property
     *
     * @param string $prj_uid {@min 32}{@max 32}
     */
    public function doGetCaseTrackerProperty($prj_uid)
    {
        try {
            $caseTracker = new \ProcessMaker\BusinessModel\CaseTracker();

            $response = $caseTracker->getCaseTracker($prj_uid);

            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * Update case tracker.
     *
     * @url PUT /:prj_uid/case-tracker/property
     *
     * @param string $prj_uid         {@min 32}{@max 32}
     * @param array  $request_data
     * @param string $map_type        {@from body}{@choice NONE,PROCESSMAP,STAGES}
     * @param int    $routing_history {@from body}{@choice 0,1}
     * @param int    $message_history {@from body}{@choice 0,1}
     *
     * @throws RestException
     *
     * @access protected
     * @class AccessControl {@permission PM_FACTORY}
     */
    public function doPutCaseTracker(
        $prj_uid,
        $request_data,
        $map_type = "NONE",
        $routing_history = 0,
        $message_history = 0
    ) {
        try {
            $caseTracker = new \ProcessMaker\BusinessModel\CaseTracker();

            $arrayData = $caseTracker->update($prj_uid, $request_data);
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url GET /:prj_uid/case-tracker/objects
     *
     * @param string $prj_uid {@min 32}{@max 32}
     */
    public function doGetCaseTrackerObjects($prj_uid)
    {
        try {
            $caseTracker = new \ProcessMaker\BusinessModel\CaseTracker();

            $response = $caseTracker->getCaseTrackerObjects($prj_uid);

            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url GET /:prj_uid/case-tracker/available-objects
     *
     * @param string $prj_uid {@min 32}{@max 32}
     */
    public function doGetCaseTrackerAvailableObjects($prj_uid)
    {
        try {
            $caseTracker = new \ProcessMaker\BusinessModel\CaseTracker();

            $response = $caseTracker->getAvailableCaseTrackerObjects($prj_uid);

            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }
}

