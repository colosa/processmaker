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
     * Constructor of the class
     *
     * return void
     */
    public function __construct()
    {
        try {
            $user = new \ProcessMaker\BusinessModel\User();

            $usrUid = $this->getUserId();

            if (!$user->checkPermission($usrUid, "PM_SETUP")) {
                throw new \Exception(\G::LoadTranslation("ID_USER_NOT_HAVE_PERMISSION", array($usrUid)));
            }
        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }

    /**
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
     * @url PUT /:cal_uid
     *
     * @param string $cal_uid      {@min 32}{@max 32}
     * @param array  $request_data
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

