<?php
namespace ProcessMaker\Services\Api\Project\MessageType;

use \ProcessMaker\Services\Api;
use \Luracast\Restler\RestException;

/**
 * Project\MessageType\Variable Api Controller
 *
 * @protected
 */
class Variable extends Api
{
    private $variable;

    /**
     * Constructor of the class
     *
     * return void
     */
    public function __construct()
    {
        try {
            $this->variable = new \ProcessMaker\BusinessModel\MessageType\Variable();

            $this->variable->setFormatFieldNameInUppercase(false);
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getVariable()));
        }
    }

    /**
     * @url GET /:prj_uid/message-type/:msgt_uid/variables
     *
     * @param string $prj_uid  {@min 32}{@max 32}
     * @param string $msgt_uid {@min 32}{@max 32}
     */
    public function doGetMessageTypeVariables($prj_uid, $msgt_uid)
    {
        try {
            $arrayAux = $this->variable->getMessageTypeVariables($msgt_uid);

            $response = $arrayAux["data"];

            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getVariable()));
        }
    }

    /**
     * @url GET /:prj_uid/message-type/:msgt_uid/variable/:msgtv_uid
     *
     * @param string $prj_uid   {@min 32}{@max 32}
     * @param string $msgt_uid  {@min 32}{@max 32}
     * @param string $msgtv_uid {@min 32}{@max 32}
     */
    public function doGetVariable($prj_uid, $msgt_uid, $msgtv_uid)
    {
        try {
            $response = $this->variable->getMessageTypeVariable($msgtv_uid);

            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getVariable()));
        }
    }

    /**
     * Return data of the new Message created.
     * 
     * @url POST /:prj_uid/message-type/:msgt_uid/variable
     * @status 201
     * 
     * @param string $prj_uid      {@min 32}{@max 32}
     * @param string $msgt_uid     {@min 32}{@max 32}
     * @param array  $request_data
     * 
     * @return array
     * @throws RestException
     * 
     * @access protected
     * @class AccessControl {@permission PM_FACTORY}
     */
    public function doPostMessageTypeVariable($prj_uid, $msgt_uid, array $request_data)
    {
        try {
            $arrayData = $this->variable->create($msgt_uid, $request_data);

            $response = $arrayData;

            return $response;
        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }

    /**
     * Update message type variable.
     *
     * @url PUT /:prj_uid/message-type/:msgt_uid/variable/:msgtv_uid
     *
     * @param string $prj_uid      {@min 32}{@max 32}
     * @param string $msgt_uid     {@min 32}{@max 32}
     * @param string $msgtv_uid    {@min 32}{@max 32}
     * @param array  $request_data
     *
     * @throws RestException
     *
     * @access protected
     * @class AccessControl {@permission PM_FACTORY}
     */
    public function doPutMessageTypeVariable($prj_uid, $msgt_uid, $msgtv_uid, array $request_data)
    {
        try {
            $arrayData = $this->variable->update($msgtv_uid, $request_data);
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url DELETE /:prj_uid/user/message-type/:msgt_uid/variable/:msgtv_uid
     * @access protected
     * @class AccessControl {@permission PM_FACTORY}
     *
     * @param string $prj_uid   {@min 32}{@max 32}
     * @param string $msgt_uid  {@min 32}{@max 32}
     * @param string $msgtv_uid {@min 32}{@max 32}
     */
    public function doDeleteMessageTypeVariable($prj_uid, $msgt_uid, $msgtv_uid)
    {
        try {
            $this->variable->delete($msgtv_uid);
        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }
}

