<?php
namespace ProcessMaker\Services\Api\Project;

use \ProcessMaker\Services\Api;
use \Luracast\Restler\RestException;
use \ProcessMaker\Util\DateTime;
use \ProcessMaker\BusinessModel\Validator;

/**
 * Project\WebEntry Api Controller
 *
 * @protected
 */
class WebEntry extends Api
{
    private $webEntry;

    private $arrayFieldIso8601 = [
        "we_create_date",
        "we_update_date"
    ];

    /**
     * Constructor of the class
     *
     * return void
     */
    public function __construct()
    {
        try {
            $this->webEntry = new \ProcessMaker\BusinessModel\WebEntry();

            $this->webEntry->setFormatFieldNameInUppercase(false);
            $this->webEntry->setArrayFieldNameForException(array("processUid" => "prj_uid"));
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url GET /:prj_uid/web-entries
     *
     * @param string $prj_uid {@min 32}{@max 32}
     */
    public function doGetWebEntries($prj_uid)
    {
        try {
            $response = $this->webEntry->getWebEntries($prj_uid);

            return DateTime::convertUtcToIso8601($response, $this->arrayFieldIso8601);
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url GET /:prj_uid/web-entry/:we_uid
     *
     * @param string $prj_uid {@min 32}{@max 32}
     * @param string $we_uid  {@min 32}{@max 32}
     */
    public function doGetWebEntry($prj_uid, $we_uid)
    {
        try {
            $response = $this->webEntry->getWebEntry($we_uid);

            return DateTime::convertUtcToIso8601($response, $this->arrayFieldIso8601);
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * Creates a new Web Entry using the method "PHP pages with Web Services".
     * 
     * @url POST /:prj_uid/web-entry
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
    public function doPostWebEntry($prj_uid, array $request_data)
    {
        try {
            Validator::throwExceptionIfDataNotMetIso8601Format($request_data, $this->arrayFieldIso8601);
            $arrayData = $this->webEntry->create($prj_uid, $this->getUserId(), DateTime::convertDataToUtc($request_data, $this->arrayFieldIso8601));

            $response = $arrayData;

            return DateTime::convertUtcToIso8601($response, $this->arrayFieldIso8601);
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * Update web-entry.
     *
     * @url PUT /:prj_uid/web-entry/:we_uid
     *
     * @param string $prj_uid      {@min 32}{@max 32}
     * @param string $we_uid       {@min 32}{@max 32}
     * @param array  $request_data
     *
     * @throws RestException
     *
     * @access protected
     * @class AccessControl {@permission PM_FACTORY}
     */
    public function doPutWebEntry($prj_uid, $we_uid, array $request_data)
    {
        try {
            Validator::throwExceptionIfDataNotMetIso8601Format($request_data, $this->arrayFieldIso8601);
            $arrayData = $this->webEntry->update($we_uid, $this->getUserId(), DateTime::convertDataToUtc($request_data, $this->arrayFieldIso8601));
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url DELETE /:prj_uid/web-entry/:we_uid
     * @access protected
     * @class AccessControl {@permission PM_FACTORY}
     *
     * @param string $prj_uid {@min 32}{@max 32}
     * @param string $we_uid  {@min 32}{@max 32}
     */
    public function doDeleteWebEntry($prj_uid, $we_uid)
    {
        try {
            $this->webEntry->delete($we_uid);
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }
}

