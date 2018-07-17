<?php
namespace ProcessMaker\Services\Api\Project;

use \ProcessMaker\Services\Api;
use \Luracast\Restler\RestException;

/**
 * Project\MessageType Api Controller
 *
 * @protected
 */
class MessageType extends Api
{
    private $message;

    /**
     * Constructor of the class
     *
     * return void
     */
    public function __construct()
    {
        try {
            $this->messageType = new \ProcessMaker\BusinessModel\MessageType();

            $this->messageType->setFormatFieldNameInUppercase(false);
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url GET /:prj_uid/message-types
     *
     * @param string $prj_uid {@min 32}{@max 32}
     */
    public function doGetMessageTypes($prj_uid, $filter = null, $start = null, $limit = null)
    {
        try {
            $arrayAux = $this->messageType->getMessageTypes($prj_uid, array("filter" => $filter), null, null, $start, $limit);

            $response = $arrayAux["data"];

            return $response;
        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }

    /**
     * @url GET /:prj_uid/message-type/:msgt_uid
     *
     * @param string $prj_uid  {@min 32}{@max 32}
     * @param string $msgt_uid {@min 32}{@max 32}
     */
    public function doGetMessageType($prj_uid, $msgt_uid)
    {
        try {
            $response = $this->messageType->getMessageType($msgt_uid);

            return $response;
        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }

    /**
     * Create message type
     * 
     * @url POST /:prj_uid/message-type
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
    public function doPostMessageType($prj_uid, array $request_data)
    {
        try {
            $arrayData = $this->messageType->create($prj_uid, $request_data);

            $response = $arrayData;

            return $response;
        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }

    /**
     * Update message type.
     *
     * @url PUT /:prj_uid/message-type/:msgt_uid
     *
     * @param string $prj_uid      {@min 32}{@max 32}
     * @param string $msgt_uid     {@min 32}{@max 32}
     * @param array  $request_data
     *
     * @throws RestException
     *
     * @access protected
     * @class AccessControl {@permission PM_FACTORY}
     */
    public function doPutMessageType($prj_uid, $msgt_uid, array $request_data)
    {
        try {
            $arrayData = $this->messageType->update($msgt_uid, $request_data);
        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }

    /**
     * @url DELETE /:prj_uid/message-type/:msgt_uid
     * @access protected
     * @class AccessControl {@permission PM_FACTORY}
     *
     * @param string $prj_uid  {@min 32}{@max 32}
     * @param string $msgt_uid {@min 32}{@max 32}
     */
    public function doDeleteMessageType($prj_uid, $msgt_uid)
    {
        try {
            $this->messageType->delete($msgt_uid);
        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }
}

