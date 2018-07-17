<?php

namespace ProcessMaker\Services\Api\Light;

use \ProcessMaker\Services\Api;
use \Luracast\Restler\RestException;

/**
 *
 * Process Api Controller
 *
 * @protected
 */
class NotificationDevice extends Api
{
    /**
     * Post Create register device with userUid
     *
     * @param array $request_data
     *
     * @author Ronald Quenta <ronald.quenta@processmaker.com>
     * @return array
     *
     * @url POST /notification
     */
    public function saveDevice($request_data)
    {
        try {
            $use_uid   = $this->getUserId();
            $oNotification = new \ProcessMaker\BusinessModel\Light\NotificationDevice();
            $response = $oNotification->saveDevice($use_uid, $request_data);
        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
        return $response;
    }

    /**
     * Update device language.
     *
     * @url PUT /notification/:dev_uid
     *
     * @param string $dev_uid {@min 32}{@max 32}
     * @param array $request_data
     *
     * @return array
     * @throws RestException
     */
    public function updateDeviceLanguage($dev_uid, $request_data)
    {
        try {
            $use_uid   = $this->getUserId();
            $oNotification = new \ProcessMaker\BusinessModel\Light\NotificationDevice();
            $response = $oNotification->updateDevice($dev_uid, $use_uid, $request_data);
        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
        return $response;
    }

    /**
     * Get all list record device
     *
     * @author Ronald Quenta <ronald.quenta@processmaker.com>
     * @return array
     *
     * @url GET /notification
     */
    public function getDevice()
    {
        try {
            $oNotification = new \NotificationDevice();
            $response = $oNotification->getAll();
        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
        return $response;
    }

    /**
     * @url DELETE /notification/:dev_uid
     * This actions is executed in the logout action
     *
     * Delete record device with dev_uid and usr_uid
     *
     * @param string $dev_uid {@min 32}{@max 32}
     *
     * @return array
     */
    public function deleteDevice($dev_uid)
    {
        try {
            $usr_uid   = $this->getUserId();
            $oNotification = new \NotificationDevice();
            $response = $oNotification->remove($dev_uid, $usr_uid);
        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
        return $response;
    }

    /**
     * Send Message by device id
     *
     * @param array $request_data
     *
     * @author Ronald Quenta <ronald.quenta@processmaker.com>
     * @return array
     *
     * @url POST /notification/sendmessage
     */
    public function sendMessage($request_data)
    {
        try {
            // type si string or array users ids
            $usrIds = $request_data['userIds'];
            $message = $request_data['message'];
            // if need send data each device
            $data = isset($request_data['data'])?$request_data['data'] : null;
            $oNotification = new \ProcessMaker\BusinessModel\Light\NotificationDevice();
            $response = $oNotification->sendMessage($usrIds, $message, $data);
        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
        return $response;
    }
}