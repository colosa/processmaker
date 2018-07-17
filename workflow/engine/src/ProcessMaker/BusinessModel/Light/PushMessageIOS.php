<?php
/**
 * Class PushMessageIOS
 * Class to send push notifications for iOS
 * Example usage
 *
 * $ios = new PushMessageIOS($passphrase);
 * $ios->setDevices($devicesToken);
 * $response = $an->send($message);
 *
 */

namespace ProcessMaker\BusinessModel\Light;

use ProcessMaker\Core\System;

class PushMessageIOS
{
    private $url = 'ssl://gateway.sandbox.push.apple.com:2195';
    private $passphrase = "sample";
    private $pemFile = 'mobileios.pem';
    private $devices = array();
    private $response = array();
    private $numberDevices = 0;

    /**
     * Sete server notification Ios
     * @param $url string the url server
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * Set key passphrase
     * @param string $passphrase update your private key's
     */
    public function setKey($passphrase)
    {
        $this->passphrase = $passphrase;
    }

    /**
     * Set name file .pem
     * @param string $file name file .pem
     */
    public function setPemFile($file)
    {
        $file = file_exists(PATH_CONFIG . $file) ? $file : 'mobileios.pem';
        $this->pemFile = $file;
    }

    /**
     * Set the devices token to send to
     * @param array $devicesToken of device tokens to send to
     */
    public function setDevices($devicesToken)
    {
        if (is_array($devicesToken)) {
            $this->devices = $devicesToken;
        } else {
            $this->devices = array($devicesToken);
        }
    }

    /**
     * Set the setting value config
     */
    public function setSettingNotification()
    {
        $conf = System::getSystemConfiguration(PATH_CONFIG . 'mobile.ini');
        $this->setUrl($conf['apple']['url']);
        $this->setKey($conf['apple']['passphrase']);
        $this->setPemFile($conf['apple']['pemFile']);
    }

    /**
     * Send the message to the device
     * @param $message string the message to send
     * @param $data object for payload body
     * @return array
     * @throws \Exception
     */
    public function send($message, $data)
    {
        $this->numberDevices = count($this->devices);
        if (!is_array($this->devices) || $this->numberDevices == 0) {
            $this->error("No devices set");
        }
        if (strlen($this->passphrase) < 8) {
            $this->error("Server API Key not set");
        }

        $ctx = stream_context_create();
        stream_context_set_option($ctx, 'ssl', 'local_cert', PATH_CONFIG . $this->pemFile);
        stream_context_set_option($ctx, 'ssl', 'passphrase', $this->passphrase);

        // Open a connection to the APNS server
//        $fp = stream_socket_client(
//            $this->url, $err,
//            $errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);
//        if (!$fp)
//            exit("Failed to connect: $err $errstr" . PHP_EOL);
        $alert = new \stdClass();
        $alert->{'loc-key'} =  $data['taskAssignType'];
        $alert->{'loc-args'} =  array($message);
        // Create the payload body
        if (!is_null($data)) {
            $body['aps'] = array(
                'alert' => $alert,
                'sound' => 'default',
                'data' => $data
            );
        } else {
            $body['aps'] = array(
                'alert' => $alert,
                'sound' => 'default'
            );
        }

        // Encode the payload as JSON
        $payload = json_encode($body);

//        // Build the binary notification
//        $msg = chr(0) . pack('n', 32) . pack('H*', $this->devices) . pack('n', strlen($payload)) . $payload;
//
//        // Send it to the server
//        $result = fwrite($fp, $msg, strlen($msg));
//        fclose($fp);

        foreach ($this->devices as $item) {
            // Open a connection to the APNS server
            $fp = stream_socket_client($this->url, $err, $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);

            if (!$fp) {
                throw (new \Exception(\G::LoadTranslation('ID_FAILED') . ': ' . "$err $errstr"));
            } else {
                //echo 'Apple service is online. ' . '<br />';
            }

            // Build the binary notification
            $msg = chr(0) . pack('n', 32) . pack('H*', $item) . pack('n', strlen($payload)) . $payload;

            // Send it to the server
            $result = fwrite($fp, $msg, strlen($msg));

            if (!$result) {
                $this->response['undelivered'][] = 'Undelivered message count: ' . $item;
            } else {
                $this->response['delivered'][] = 'Delivered message count: ' . $item;
            }

            if ($fp) {
                fclose($fp);
                //echo 'The connection has been closed by the client' . '<br />';
            }
        }

        return $this->response;
    }

    public function getNumberDevices()
    {
        return $this->numberDevices;
    }

    public function error($msg)
    {
        echo "Android send notification failed with error:";
        echo "\t" . $msg;
    }
}