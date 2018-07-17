<?php
namespace ProcessMaker\Services\Api;

use \ProcessMaker\Services\Api;
use \Luracast\Restler\RestException;

/**
 * EmailServer Api Controller
 *
 * @protected
 */
class EmailServer extends Api
{
    private $emailServer;

    /**
     * Constructor of the class
     *
     * return void
     */
    public function __construct()
    {
        try {
            $this->emailServer = new \ProcessMaker\BusinessModel\EmailServer();

            $this->emailServer->setFormatFieldNameInUppercase(false);
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url GET
     *
     * @param string $filter
     * @param int    $start
     * @param int    $limit
     *
     */
    public function index($filter = null, $start = null, $limit = null)
    {
        try {
            $arrayAux = $this->emailServer->getEmailServers(array("filter" => $filter), null, null, $start, $limit);

            $response = $arrayAux["data"];

            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url GET /:mess_uid
     *
     * @param string $mess_uid {@min 32}{@max 32}
     */
    public function doGet($mess_uid)
    {
        try {
            $response = $this->emailServer->getEmailServer($mess_uid);

            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url GET /paged
     *
     * @param string $filter
     * @param int    $start
     * @param int    $limit
     */
    public function doGetPaged($filter = null, $start = null, $limit = null)
    {
        try {
            $response = $this->emailServer->getEmailServers(array("filter" => $filter), null, null, $start, $limit);

            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * Test connection.
     * 
     * @url POST /test-connection
     * 
     * @param array $request_data
     * 
     * @return array
     * @throws RestException 
     * 
     * @access protected
     * @class AccessControl {@permission PM_SETUP_EMAIL}
     */
    public function doPostTestConnection(array $request_data)
    {
        try {
            $arrayData = $this->emailServer->testConnection($request_data);

            $response = $arrayData;

            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * Create email server.
     * 
     * @url POST
     * @status 201
     * 
     * @param array $request_data
     * 
     * @return array
     * @throws RestException 
     * 
     * @access protected
     * @class AccessControl {@permission PM_SETUP_EMAIL}
     */
    public function doPost(array $request_data)
    {
        try {
            $arrayData = $this->emailServer->create($request_data);

            $response = $arrayData;

            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * Update email server.
     *
     * @url PUT /:mess_uid
     * @status 200
     *
     * @param string $mess_uid     {@min 32}{@max 32}
     * @param array  $request_data
     *
     * @throws RestException
     *
     * @access protected
     * @class AccessControl {@permission PM_SETUP_EMAIL}
     */
    public function doPut($mess_uid, array $request_data)
    {
        try {
            $arrayData = $this->emailServer->update($mess_uid, $request_data);
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url DELETE /:mess_uid
     * @access protected
     * @class AccessControl {@permission PM_SETUP_EMAIL}
     *
     * @param string $mess_uid {@min 32}{@max 32}
     *
     * @status 200
     */
    public function doDelete($mess_uid)
    {
        try {
            $this->emailServer->delete($mess_uid);
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }
}

