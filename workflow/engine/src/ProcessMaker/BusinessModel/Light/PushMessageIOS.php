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

class PushMessageIOS
{
    var $url = 'ssl://gateway.sandbox.push.apple.com:2195';
    var $passphrase = "sample";
    var $pemFile;
    var $devices = array();
    var $response = array();

    /**
     * @param $url string the url server
     */
    function setUrl($url){
        $this->url = $url;
    }

    /**
     * Constructor
     * @param $passphrase update your private key's
     */
    function setKey($passphrase){
        $this->passphrase = $passphrase;
    }

    /**
     *    Set the devices token to send to
     *    @param $deviceIds array of device tokens to send to
     */
    function setDevices($devicesToken)
    {
        if(is_array($devicesToken)){
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
        $conf = \System::getSystemConfiguration( PATH_CONFIG . 'mobile.ini' );
        $this->setUrl($conf['apple']['url']);
        $this->setKey($conf['apple']['passphrase']);
    }

    /**
     * Send the message to the device
     * @param $message the message to send
     * @return mixed
     */
    function send($message, $data)
    {
        if(!is_array($this->devices) || count($this->devices) == 0){
            $this->error("No devices set");
        }
        if(strlen($this->passphrase) < 8){
            $this->error("Server API Key not set");
        }

        $ctx = stream_context_create();
        stream_context_set_option($ctx, 'ssl', 'local_cert', PATH_CONFIG . 'mobileios.pem');
        stream_context_set_option($ctx, 'ssl', 'passphrase', $this->passphrase);

        // Open a connection to the APNS server
//        $fp = stream_socket_client(
//            $this->url, $err,
//            $errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);
//        if (!$fp)
//            exit("Failed to connect: $err $errstr" . PHP_EOL);

        // Create the payload body
        if (!is_null($data)) {
            $body['aps'] = array(
                'alert' => $message,
                'sound' => 'default',
                'data'  => $data
            );
        } else {
            $body['aps'] = array(
                'alert' => $message,
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
                throw (new \Exception( G::LoadTranslation( 'ID_FAILED' ).': ' ."$err $errstr"));
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

    function error($msg){
        echo "Android send notification failed with error:";
        echo "\t" . $msg;
    }
}
