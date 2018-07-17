<?php
namespace ProcessMaker\Services\Api\Project;

use \ProcessMaker\Services\Api;
use \Luracast\Restler\RestException;

/**
 * Project\Event Api Controller
 *
 * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
 * @copyright Colosa - Bolivia
 *
 * @protected
 */
class Event extends Api
{
    /**
     * @param string $prj_uid {@min 1} {@max 32}
     * @param string $filter {@choice message,conditional,,multiple}
     *
     * @access public
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @return array
     *
     * @url GET /:prj_uid/events
     */
    public function doGetEvents($prj_uid, $filter = '')
    {
        try {
            $hiddenFields = array('pro_uid', 'evn_action_parameters',
                'evn_posx', 'evn_posy', 'evn_type', 'tas_evn_uid', 'evn_max_attempts'
            );
            $event = new \ProcessMaker\BusinessModel\Event();
            $response = $event->getEvents($prj_uid, $filter);
            foreach ($response as &$eventData) {
                foreach ($eventData as $key => $value) {
                    if (in_array($key, $hiddenFields)) {
                        unset($eventData[$key]);
                    }
                }
            }
            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @param string $prj_uid {@min 1} {@max 32}
     * @param string $evn_uid {@min 1} {@max 32}
     *
     * @access public
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @return array
     *
     * @url GET /:prj_uid/event/:evn_uid
     */
    public function doGetEvent($prj_uid, $evn_uid)
    {
        try {
            $hiddenFields = array('pro_uid', 'evn_action_parameters',
                'evn_posx', 'evn_posy', 'evn_type', 'tas_evn_uid', 'evn_max_attempts'
            );
            $event = new \ProcessMaker\BusinessModel\Event();
            $response = $event->getEvents($prj_uid, '', $evn_uid);
            foreach ($response as $key => $eventData) {
                if (in_array($key, $hiddenFields)) {
                    unset($response[$key]);
                }
            }
            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @param string $prj_uid {@min 1} {@max 32}
     * @param array $request_data
     * @param string $evn_description {@from body} {@min 1}
     * @param string $evn_status {@from body} {@choice ACTIVE,INACTIVE}
     * @param string $evn_action {@from body} {@choice SEND_MESSAGE,EXECUTE_CONDITIONAL_TRIGGER,EXECUTE_TRIGGER}
     * @param string $evn_related_to {@from body} {@choice SINGLE,MULTIPLE}
     * @param string $evn_tas_estimated_duration {@from body} {@type float}
     * @param string $evn_time_unit {@from body} {@choice DAYS,HOURS}
     * @param string $evn_when {@from body} {@type float}
     * @param string $evn_when_occurs {@from body} {@choice AFTER_TIME,TASK_STARTED}
     * @param string $tri_uid {@from body} {@min 1}
     * @param string $tas_uid {@from body}
     * @param string $evn_tas_uid_from {@from body}
     * @param string $evn_tas_uid_to {@from body}
     * @param string $evn_conditions {@from body}
     *
     * @access public
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @return array
     *
     * @url POST /:prj_uid/event
     * @status 201
     */
    public function doPostEvent(
        $prj_uid,
        $request_data,
        $evn_description,
        $evn_status,
        $evn_action,
        $evn_related_to,
        $evn_tas_estimated_duration,
        $evn_time_unit,
        $evn_when,
        $evn_when_occurs,
        $tri_uid,
        $tas_uid = '',
        $evn_tas_uid_from = '',
        $evn_tas_uid_to = '',
        $evn_conditions = ''
    ) {
        try {
            $hiddenFields = array('pro_uid', 'evn_action_parameters',
                'evn_posx', 'evn_posy', 'evn_type', 'tas_evn_uid', 'evn_max_attempts'
            );
            $event = new \ProcessMaker\BusinessModel\Event();
            $response = $event->saveEvents($prj_uid, $request_data, true);
            foreach ($response as $key => $eventData) {
                if (in_array($key, $hiddenFields)) {
                    unset($response[$key]);
                }
            }
            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * Update event.
     *
     * @url PUT /:prj_uid/event/:evn_uid
     *
     * @param string $prj_uid {@min 1} {@max 32}
     * @param string $evn_uid {@min 1} {@max 32}
     * @param array $request_data
     * @param string $evn_description {@from body} {@min 1}
     * @param string $evn_status {@from body} {@choice ACTIVE,INACTIVE}
     * @param string $evn_action {@from body} {@choice SEND_MESSAGE,EXECUTE_CONDITIONAL_TRIGGER,EXECUTE_TRIGGER}
     * @param string $evn_related_to {@from body} {@choice SINGLE,MULTIPLE}
     * @param string $evn_tas_estimated_duration {@from body} {@min 1}
     * @param string $evn_time_unit {@from body} {@choice DAYS,HOURS}
     * @param string $evn_when {@from body} {@type float}
     * @param string $evn_when_occurs {@from body} {@choice AFTER_TIME,TASK_STARTED}
     * @param string $tri_uid {@from body} {@min 1}
     * @param string $tas_uid {@from body}
     * @param string $evn_tas_uid_from {@from body}
     * @param string $evn_tas_uid_to {@from body}
     * @param string $evn_conditions {@from body}
     *
     * @return void
     * @throws RestException
     *
     * @access protected
     * @class AccessControl {@permission PM_FACTORY}
     */
    public function doPutEvent (
        $prj_uid,
        $evn_uid,
        $request_data,
        $evn_description,
        $evn_status,
        $evn_action,
        $evn_related_to,
        $evn_tas_estimated_duration,
        $evn_time_unit,
        $evn_when,
        $evn_when_occurs,
        $tri_uid,
        $tas_uid = '',
        $evn_tas_uid_from = '',
        $evn_tas_uid_to = '',
        $evn_conditions = ''
    ) {
        try {
            $hiddenFields = array(
                'pro_uid',
                'evn_action_parameters',
                'evn_posx',
                'evn_posy',
                'evn_type',
                'tas_evn_uid',
                'evn_max_attempts'
            );
            $request_data['evn_uid'] = $evn_uid;
            $event = new \ProcessMaker\BusinessModel\Event();
            $event->saveEvents($prj_uid, $request_data);
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url DELETE /:prj_uid/event/:evn_uid
     * @access protected
     * @class AccessControl {@permission PM_FACTORY}
     *
     * @param string $prj_uid {@min 1} {@max 32}
     * @param string $evn_uid {@min 1} {@max 32}
     * @return void
     */
    public function doDeleteEvent($prj_uid, $evn_uid)
    {
        try {
            $event = new \ProcessMaker\BusinessModel\Event();
            $response = $event->deleteEvent($prj_uid, $evn_uid);
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }
}

