<?php
namespace ProcessMaker\Services\Api\Project;

use \ProcessMaker\Services\Api;
use \Luracast\Restler\RestException;

/**
 * Project\EmailEvent Api Controller
 *
 * @protected
 */
class EmailEvent extends Api
{
    private $EmailEvent;

    /**
     * Constructor of the class
     *
     * return void
     */
    public function __construct()
    {
        try {
            $this->EmailEvent = new \ProcessMaker\BusinessModel\EmailEvent();
        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }
    
    /**
     * @url GET /:prj_uid/email-event/accounts/:from
     *
     * @param string $prj_uid {@min 1} {@max 32}
     * @param string $from
     */
    public function doGetEmailEventAccounts($prj_uid, $from = "emailUsers")
    {
        try {
            if($from == "emailUsers") {
                $response = $this->EmailEvent->GetEmailEventAccounts();
            } else {
                $response = $this->EmailEvent->getEmailEventServerAccounts();
            }
            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }
    
    /**
     * @url GET /:prj_uid/email-event/:act_uid
     *
     * @param string $prj_uid {@min 1} {@max 32}
     * @param string $act_uid {@min 1} {@max 32}
     */
    public function doGetEmailEvent($prj_uid, $act_uid)
    {
        try {
            $response = $this->EmailEvent->getEmailEventData($prj_uid, $act_uid);
            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }
    
    /**
     * @url POST /:prj_uid/email-event
     *
     * @param string $prj_uid {@min 1} {@max 32}
     */
    public function doPostEmailEvent($prj_uid, array $request_data)
    {
        try {
            $response = $this->EmailEvent->save($prj_uid, $request_data);
            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }
    
    /**
     * @url PUT /:prj_uid/email-event/:email_event_uid
     *
     * @param string $prj_uid      {@min 32}{@max 32}
     * @param string $email_event_uid    {@min 32}{@max 32}
     * @param array  $request_data
     */
    public function doPutEmailEvent($prj_uid, $email_event_uid, array $request_data)
    {
        try {
            $arrayData = $this->EmailEvent->update($email_event_uid, $request_data);
        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }
    
    /**
     * @url DELETE /:prj_uid/email-event/:email_event_uid
     *
     * @param string $prj_uid   {@min 32}{@max 32}
     * @param string $email_event_uid {@min 32}{@max 32}
     */
    public function doDeleteEmailEvent($prj_uid, $email_event_uid)
    {
        try {
            $this->EmailEvent->delete($prj_uid, $email_event_uid);
        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }
    
    /**
     * @url DELETE /:prj_uid/email-event/by-event/:act_uid
     *
     * @param string $prj_uid   {@min 32}{@max 32}
     * @param string $act_uid {@min 32}{@max 32}
     */
    public function doDeleteEmailEventByEvent ($prj_uid, $act_uid)
    {
        try {
            $this->EmailEvent->deleteByEvent($prj_uid, $act_uid);
        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }

}

