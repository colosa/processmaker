<?php
namespace ProcessMaker\Services\Api\Project;

use \ProcessMaker\Services\Api;
use \Luracast\Restler\RestException;

/**
 * Project\TriggerWizard Api Controller
 *
 * @protected
 */
class TriggerWizard extends Api
{
    private $formatFieldNameInUppercase = false;

    private $arrayFieldNameForException = array(
        "processUid"  => "prj_uid",
        "libraryName" => "lib_name",
        "methodName"  => "fn_name"
    );

    /**
     * @url GET /:prj_uid/trigger-wizard/:lib_name
     * @url GET /:prj_uid/trigger-wizard/:lib_name/:fn_name
     *
     * @param string $prj_uid  {@min 32}{@max 32}
     * @param string $lib_name
     * @param string $fn_name
     */
    public function doGetTriggerWizard($prj_uid, $lib_name, $fn_name = "")
    {
        try {
            $triggerWizard = new \ProcessMaker\BusinessModel\TriggerWizard();
            $triggerWizard->setFormatFieldNameInUppercase($this->formatFieldNameInUppercase);
            $triggerWizard->setArrayFieldNameForException($this->arrayFieldNameForException);

            $response = ($fn_name == "")? $triggerWizard->getLibrary($lib_name) : $triggerWizard->getMethod($lib_name, $fn_name);

            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url GET /:prj_uid/trigger-wizard/:lib_name/:fn_name/:tri_uid
     *
     * @param string $prj_uid  {@min 32}{@max 32}
     * @param string $lib_name
     * @param string $fn_name
     * @param string $tri_uid  {@min 32}{@max 32}
     */
    public function doGetTriggerWizardTrigger($prj_uid, $lib_name, $fn_name, $tri_uid)
    {
        try {
            $triggerWizard = new \ProcessMaker\BusinessModel\TriggerWizard();
            $triggerWizard->setFormatFieldNameInUppercase($this->formatFieldNameInUppercase);
            $triggerWizard->setArrayFieldNameForException($this->arrayFieldNameForException);

            $response = $triggerWizard->getTrigger($lib_name, $fn_name, $tri_uid);

            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * Create Trigger for a Process
     * 
     * @url POST /:prj_uid/trigger-wizard/:lib_name/:fn_name
     * @status 201
     * 
     * @param string $prj_uid      {@min 32}{@max 32}
     * @param string $lib_name
     * @param string $fn_name
     * @param array  $request_data
     * 
     * @return array
     * @throws RestException
     * 
     * @access protected
     * @class AccessControl {@permission PM_FACTORY}
     */
    public function doPostTriggerWizard($prj_uid, $lib_name, $fn_name, $request_data)
    {
        try {
            $triggerWizard = new \ProcessMaker\BusinessModel\TriggerWizard();
            $triggerWizard->setFormatFieldNameInUppercase($this->formatFieldNameInUppercase);
            $triggerWizard->setArrayFieldNameForException($this->arrayFieldNameForException);

            $arrayData = $triggerWizard->create($lib_name, $fn_name, $prj_uid, $request_data);

            $response = $arrayData;

            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * Update trigger wizard.
     *
     * @url PUT /:prj_uid/trigger-wizard/:lib_name/:fn_name/:tri_uid
     *
     * @param string $prj_uid      {@min 32}{@max 32}
     * @param string $lib_name
     * @param string $fn_name
     * @param string $tri_uid      {@min 32}{@max 32}
     * @param array  $request_data
     *
     * @throws RestException
     *
     * @access protected
     * @class AccessControl {@permission PM_FACTORY}
     */
    public function doPutTriggerWizard($prj_uid, $lib_name, $fn_name, $tri_uid, $request_data)
    {
        try {
            $triggerWizard = new \ProcessMaker\BusinessModel\TriggerWizard();
            $triggerWizard->setFormatFieldNameInUppercase($this->formatFieldNameInUppercase);
            $triggerWizard->setArrayFieldNameForException($this->arrayFieldNameForException);

            $arrayData = $triggerWizard->update($lib_name, $fn_name, $tri_uid, $request_data);
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }
}

