<?php
namespace ProcessMaker\Services\Api;

use \ProcessMaker\Services\Api;
use \Luracast\Restler\RestException;

/**
 * Calendar Api Controller
 *
 * @protected
 */
class Calendar extends Api
{
    private $formatFieldNameInUppercase = false;

    /**
     * 
     * @access protected
     * @class  AccessControl {@permission PM_SETUP_CALENDAR}
     * @url GET
     */
    public function index($filter = null, $start = null, $limit = null)
    {
        try {
            $calendar = new \ProcessMaker\BusinessModel\Calendar();
            $calendar->setFormatFieldNameInUppercase($this->formatFieldNameInUppercase);
            $response = $calendar->getCalendars(array("filter" => $filter), null, null, $start, $limit);

            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * 
     * @access protected
     * @class  AccessControl {@permission PM_SETUP_CALENDAR}
     * @url GET /:cal_uid
     *
     * @param string $cal_uid {@min 32}{@max 32}
     */
    public function doGet($cal_uid)
    {
        try {
            $calendar = new \ProcessMaker\BusinessModel\Calendar();
            $calendar->setFormatFieldNameInUppercase($this->formatFieldNameInUppercase);
            $response = $calendar->getCalendar($cal_uid);

            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * 
     * @access protected
     * @class  AccessControl {@permission PM_SETUP_CALENDAR}
     * @url POST
     *
     * @param array $request_data
     *
     * @status 201
     */
    public function doPost($request_data)
    {
        try {
            $calendar = new \ProcessMaker\BusinessModel\Calendar();
            $calendar->setFormatFieldNameInUppercase($this->formatFieldNameInUppercase);

            $arrayData = $calendar->create($request_data);

            $response = $arrayData;

            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * Update calendar.
     *
     * @url PUT /:cal_uid
     *
     * @param string $cal_uid      {@min 32}{@max 32}
     * @param array  $request_data
     *
     * @throws RestException
     *
     * @access protected
     * @class AccessControl {@permission PM_SETUP_CALENDAR}
     */
    public function doPut($cal_uid, $request_data)
    {
        try {
            $calendar = new \ProcessMaker\BusinessModel\Calendar();
            $calendar->setFormatFieldNameInUppercase($this->formatFieldNameInUppercase);

            $arrayData = $calendar->update($cal_uid, $request_data);
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * 
     * @access protected
     * @class  AccessControl {@permission PM_SETUP_CALENDAR}
     * @url DELETE /:cal_uid
     *
     * @param string $cal_uid {@min 32}{@max 32}
     */
    public function doDelete($cal_uid)
    {
        try {
            $calendar = new \ProcessMaker\BusinessModel\Calendar();
            $calendar->setFormatFieldNameInUppercase($this->formatFieldNameInUppercase);

            $calendar->delete($cal_uid);
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }
}

