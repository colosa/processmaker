<?php
namespace ProcessMaker\Services\Api\Project;

use \ProcessMaker\Services\Api;
use \Luracast\Restler\RestException;

/**
 * Project\CaseTrackerObject Api Controller
 *
 * @protected
 */
class CaseTrackerObject extends Api
{
    /**
     * @url GET /:prj_uid/case-tracker/object/:cto_uid
     *
     * @param string $cto_uid {@min 32}{@max 32}
     * @param string $prj_uid {@min 32}{@max 32}
     */
    public function doGetCaseTrackerObject($cto_uid, $prj_uid)
    {
        try {
            $caseTrackerObject = new \ProcessMaker\BusinessModel\CaseTrackerObject();

            $response = $caseTrackerObject->getCaseTrackerObject($cto_uid);

            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * Assign an object (Dynaform, Input Document, Output Document) to a case tracker.
     * 
     * @url POST /:prj_uid/case-tracker/object
     * @status 201
     * 
     * @param string $prj_uid       {@min 32}{@max 32}
     * @param array  $request_data
     * @param string $cto_type_obj  {@from body}{@choice DYNAFORM,INPUT_DOCUMENT,OUTPUT_DOCUMENT}{@required true}
     * @param string $cto_uid_obj   {@from body}{@min 32}{@max 32}{@required true}
     * @param string $cto_condition {@from body}
     * @param int    $cto_position  {@from body}{@min 1}
     * 
     * @return array
     * @throws RestException 
     * 
     * @access protected
     * @class AccessControl {@permission PM_FACTORY}
     */
    public function doPostCaseTrackerObject(
        $prj_uid,
        $request_data,
        $cto_type_obj = "DYNAFORM",
        $cto_uid_obj = "00000000000000000000000000000000",
        $cto_condition = "",
        $cto_position = 1
    ) {
        try {
            $caseTrackerObject = new \ProcessMaker\BusinessModel\CaseTrackerObject();

            $arrayData = $caseTrackerObject->create($prj_uid, $request_data);

            $response = $arrayData;

            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * Update case tracker object.
     *
     * @url PUT /:prj_uid/case-tracker/object/:cto_uid
     *
     * @param string $cto_uid       {@min 32}{@max 32}
     * @param string $prj_uid       {@min 32}{@max 32}
     * @param array  $request_data
     * @param string $cto_type_obj  {@from body}{@choice DYNAFORM,INPUT_DOCUMENT,OUTPUT_DOCUMENT}
     * @param string $cto_uid_obj   {@from body}{@min 32}{@max 32}
     * @param string $cto_condition {@from body}
     * @param int    $cto_position  {@from body}{@min 1}
     *
     * @throws RestException
     *
     * @access protected
     * @class AccessControl {@permission PM_FACTORY}
     */
    public function doPutCaseTrackerObject(
        $cto_uid,
        $prj_uid,
        $request_data,
        $cto_type_obj = "DYNAFORM",
        $cto_uid_obj = "00000000000000000000000000000000",
        $cto_condition = "",
        $cto_position = 1
    ) {
        try {
            $caseTrackerObject = new \ProcessMaker\BusinessModel\CaseTrackerObject();

            $arrayData = $caseTrackerObject->update($cto_uid, $request_data);
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url DELETE /:prj_uid/case-tracker/object/:cto_uid
     * @access protected
     * @class AccessControl {@permission PM_FACTORY}
     *
     * @param string $cto_uid {@min 32}{@max 32}
     * @param string $prj_uid {@min 32}{@max 32}
     */
    public function doDeleteCaseTrackerObject($cto_uid, $prj_uid)
    {
        try {
            $caseTrackerObject = new \ProcessMaker\BusinessModel\CaseTrackerObject();

            $caseTrackerObject->delete($cto_uid);
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }
}

