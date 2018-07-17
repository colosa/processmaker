<?php
/**
 * Class PushMessageAndroid
 * Class to send push notifications using Google Cloud Messaging for Android
 * Example usage
 *
 * $an = new PushMessageAndroid();
 * $an->setKey($apiKey);
 * $an->setDevices($devices);
 * $response = $an->send($message);
 *
 */

namespace ProcessMaker\BusinessModel\Light;

use ProcessMaker\Core\System;

class PushMessageAndroid
{
    private $url = 'https://android.googleapis.com/gcm/send';
    private $serverApiKey = "AIzaSyBO-VLXGhjf0PPlwmPFTPQEKIBfVDydLAk";
    private $devices = array();
    private $numberDevices = 0;

    /**
     * @param $url string the url server
     */
    public function setUrl($url)
    {
        $this->$url = $url;
    }

    /**
     * @param $apiKeyIn string the server API key
     */
    public function setKey($apiKeyIn)
    {
        $this->serverApiKey = $apiKeyIn;
    }

    /**
     *    Set the devices to send to
     * @param $deviceIds string or array of device tokens to send
     */
    public function setDevices($deviceIds)
    {
        if (is_array($deviceIds)) {
            $this->devices = $deviceIds;
        } else {
            $this->devices = array($deviceIds);
        }
    }

    /**
     * Set the setting value config
     */
    public function setSettingNotification()
    {
        $conf = System::getSystemConfiguration(PATH_CONFIG . 'mobile.ini');
        $this->setUrl($conf['android']['url']);
        $this->setKey($conf['android']['serverApiKey']);
    }

    /**
     * Send the message to the device
     * @param $message string the message to send
     * @param $data array send extra information for app
     * @return mixed
     */
    public function send($message, $data)
    {
        $this->numberDevices = count($this->devices);
        if (!is_array($this->devices) || $this->numberDevices == 0) {
            $this->error("No devices set");
        }
        if (strlen($this->serverApiKey) < 8) {
            $this->error("Server API Key not set");
        }

        if (!is_null($data)) {
            $fields = array(
                'registration_ids' => $this->devices,
                'data' => array(
                    "message" => $message,
                    "data" => $data
                ),
            );
        } else {
            $fields = array(
                'registration_ids' => $this->devices,
                'data' => array("message" => $message),
            );
        }

        $headers = array(
            'Authorization: key=' . $this->serverApiKey,
            'Content-Type: application/json'
        );
        // Open connection
        $ch = curl_init();

        // Set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $this->url);

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

        // Avoids problem with https certificate
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        // Execute post
        $result = curl_exec($ch);

        // Close connection
        curl_close($ch);
        return $result;
    }

    public function getNumberDevices()
    {
        return $this->numberDevices;
    }

    public function error($msg)
    {
        echo "Android send notification failed with error:";
        echo "\t" . $msg;
        exit(1);
    }
}