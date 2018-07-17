<?php
namespace ProcessMaker\Services\Api\Project;

use \ProcessMaker\Services\Api;
use \Luracast\Restler\RestException;

/**
 * Project\MessageEventDefinition Api Controller
 *
 * @protected
 */
class MessageEventDefinition extends Api
{
    private $messageEventDefinition;

    /**
     * Constructor of the class
     *
     * return void
     */
    public function __construct()
    {
        try {
            $this->messageEventDefinition = new \ProcessMaker\BusinessModel\MessageEventDefinition();

            $this->messageEventDefinition->setFormatFieldNameInUppercase(false);
        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }

    /**
     * @url GET /:prj_uid/message-event-definitions
     *
     * @param string $prj_uid {@min 32}{@max 32}
     */
    public function doGetMessageEventDefinitions($prj_uid)
    {
        try {
            $response = $this->messageEventDefinition->getMessageEventDefinitions($prj_uid);

            return $response;
        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }

    /**
     * @url GET /:prj_uid/message-event-definition/:msged_uid
     *
     * @param string $prj_uid   {@min 32}{@max 32}
     * @param string $msged_uid {@min 32}{@max 32}
     */
    public function doGetMessageEventDefinition($prj_uid, $msged_uid)
    {
        try {
            $response = $this->messageEventDefinition->getMessageEventDefinition($msged_uid);

            return $response;
        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }

    /**
     * @url GET /:prj_uid/message-event-definition/event/:evn_uid
     *
     * @param string $prj_uid {@min 32}{@max 32}
     * @param string $evn_uid {@min 32}{@max 32}
     */
    public function doGetMessageEventDefinitionEvent($prj_uid, $evn_uid)
    {
        try {
            $response = $this->messageEventDefinition->getMessageEventDefinitionByEvent($prj_uid, $evn_uid);

            return $response;
        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }

    /**
     * Create message event definition.
     * 
     * @url POST /:prj_uid/message-event-definition
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
    public function doPostMessageEventDefinition($prj_uid, array $request_data)
    {
        try {
            $arrayData = $this->messageEventDefinition->create($prj_uid, $request_data);

            $response = $arrayData;

            return $response;
        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }

    /**
     * Update message event definition.
     *
     * @url PUT /:prj_uid/message-event-definition/:msged_uid
     *
     * @param string $prj_uid      {@min 32}{@max 32}
     * @param string $msged_uid    {@min 32}{@max 32}
     * @param array  $request_data
     *
     * @throws RestException
     *
     * @access protected
     * @class AccessControl {@permission PM_FACTORY}
     */
    public function doPutMessageEventDefinition($prj_uid, $msged_uid, array $request_data)
    {
        try {
            $arrayData = $this->messageEventDefinition->update($msged_uid, $request_data);
        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }

    /**
     * @url DELETE /:prj_uid/message-event-definition/:msged_uid
     * @access protected
     * @class AccessControl {@permission PM_FACTORY}
     *
     * @param string $prj_uid   {@min 32}{@max 32}
     * @param string $msged_uid {@min 32}{@max 32}
     */
    public function doDeleteMessageEventDefinition($prj_uid, $msged_uid)
    {
        try {
            $this->messageEventDefinition->delete($msged_uid);
        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }
}

