<?php
namespace ProcessMaker\Services\Api\Project;

use \ProcessMaker\Services\Api;
use \Luracast\Restler\RestException;

/**
 * Project\TimerEvent Api Controller
 *
 * @protected
 */
class TimerEvent extends Api
{
    private $timerEvent;

    private $arrayFieldIso8601 = [
        'tmrevn_next_run_date',
        'tmrevn_last_run_date',
        'tmrevn_last_execution_date'
    ];

    /**
     * Constructor of the class
     *
     * return void
     */
    public function __construct()
    {
        try {
            $this->timerEvent = new \ProcessMaker\BusinessModel\TimerEvent();

            $this->timerEvent->setFormatFieldNameInUppercase(false);
        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }

    /**
     * @url GET /:prj_uid/timer-events
     *
     * @param string $prj_uid {@min 32}{@max 32}
     */
    public function doGetTimerEvents($prj_uid)
    {
        try {
            $response = $this->timerEvent->getTimerEvents($prj_uid);

            return \ProcessMaker\Util\DateTime::convertUtcToIso8601($response, $this->arrayFieldIso8601);
        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }

    /**
     * @url GET /:prj_uid/timer-event/:tmrevn_uid
     *
     * @param string $prj_uid    {@min 32}{@max 32}
     * @param string $tmrevn_uid {@min 32}{@max 32}
     */
    public function doGetTimerEvent($prj_uid, $tmrevn_uid)
    {
        try {
            $response = $this->timerEvent->getTimerEvent($tmrevn_uid);

            return \ProcessMaker\Util\DateTime::convertUtcToIso8601($response, $this->arrayFieldIso8601);
        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }

    /**
     * @url GET /:prj_uid/timer-event/event/:evn_uid
     *
     * @param string $prj_uid {@min 32}{@max 32}
     * @param string $evn_uid {@min 32}{@max 32}
     */
    public function doGetTimerEventEvent($prj_uid, $evn_uid)
    {
        try {
            $response = $this->timerEvent->getTimerEventByEvent($prj_uid, $evn_uid);

            return \ProcessMaker\Util\DateTime::convertUtcToIso8601($response, $this->arrayFieldIso8601);
        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }

    /**
     * Create timer event for a project.
     * 
     * @url POST /:prj_uid/timer-event
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
    public function doPostTimerEvent($prj_uid, array $request_data)
    {
        try {
            \ProcessMaker\BusinessModel\Validator::throwExceptionIfDataNotMetIso8601Format($request_data, $this->arrayFieldIso8601);

            $arrayData = $this->timerEvent->create($prj_uid, \ProcessMaker\Util\DateTime::convertDataToUtc($request_data, $this->arrayFieldIso8601));

            $response = $arrayData;

            return \ProcessMaker\Util\DateTime::convertUtcToIso8601($response, $this->arrayFieldIso8601);
        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }

    /**
     * Update timer event.
     *
     * @url PUT /:prj_uid/timer-event/:tmrevn_uid
     *
     * @param string $prj_uid      {@min 32}{@max 32}
     * @param string $tmrevn_uid   {@min 32}{@max 32}
     * @param array  $request_data
     *
     * @throws RestException
     *
     * @access protected
     * @class AccessControl {@permission PM_FACTORY}
     */
    public function doPutTimerEvent($prj_uid, $tmrevn_uid, array $request_data)
    {
        try {
            \ProcessMaker\BusinessModel\Validator::throwExceptionIfDataNotMetIso8601Format($request_data, $this->arrayFieldIso8601);

            $arrayData = $this->timerEvent->update($tmrevn_uid, \ProcessMaker\Util\DateTime::convertDataToUtc($request_data, $this->arrayFieldIso8601));
        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }

    /**
     * @url DELETE /:prj_uid/timer-event/:tmrevn_uid
     * @access protected
     * @class AccessControl {@permission PM_FACTORY}
     *
     * @param string $prj_uid    {@min 32}{@max 32}
     * @param string $tmrevn_uid {@min 32}{@max 32}
     */
    public function doDeleteTimerEvent($prj_uid, $tmrevn_uid)
    {
        try {
            $this->timerEvent->delete($tmrevn_uid);
        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }
}

