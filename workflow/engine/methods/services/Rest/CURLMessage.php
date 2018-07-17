<?php
/**
 * Abstract class containing the CURL functionality, used to handle GET, POST, PUT and DELETE http methods.
 *
 * This class uses many different Curl functions, it would be great if this one gorws to allow the use of alll of them.
 *
 * @category Zend
 * @package ProcessMaker
 * @subpackage workflow
 * @copyright Copyright (c) ProcessMaker Colosa Inc.
 * @version Release: @2.0.44@
 * @since Class available since Release 2.0.44
 */

define( 'PATH_SEP', '/' );
define( 'COLON', ':' );
define( 'DOT', '.' );
define( 'PROTOCOL_HTTP', 'http' );

/**
 * Abstract class, containing the CURL functionality, used to handle GET, POST, PUT and DELETE http methods.
 */
abstract class CURLMessage
{
    protected $restServer; // e.g. "http://ralph.pmos.colosa.net/rest/ralph/";  host + technich dir + workspace
    protected $content = "Content-Type: application/"; //set the string used to attach next the kind of message to be handle.
    protected $serviceTechnic = "rest"; // name of the current durectory where the rest methods are.
    protected $server_method; // used to set the name of the remote host and the Rest method to be called.
    protected $type; // contains the type of the message.
    protected $ch; //curl handle instance.
    protected $output; // contains the output returned by the curl_exec function.


    /**
     * Setting the remote host and init the Curl handle options
     */
    function __construct ()
    {
        $serverDNS = explode( DOT, $_SERVER['SERVER_NAME'] );
        $serverDNS = array_reverse( $serverDNS );
        $workspace = array_pop( $serverDNS ); //***aware this must contains the workspace name***


        $this->restServer = PROTOCOL_HTTP . COLON . PATH_SEP . PATH_SEP;
        $this->restServer .= $_SERVER['SERVER_NAME'] . PATH_SEP;
        $this->restServer .= $this->serviceTechnic . PATH_SEP . $workspace . PATH_SEP;

        $this->ch = curl_init();
        curl_setopt( $this->ch, CURLOPT_TIMEOUT, 2 );
        curl_setopt( $this->ch, CURLOPT_POST, 1 );
        curl_setopt( $this->ch, CURLOPT_RETURNTRANSFER, 1 );
    }

    /**
     * set the message in order to follow the message format
     */
    abstract protected function format (array $message);

    /**
     * Set properties used in a simpleMessage Class like a set in a URI, or formatted as a JSon msg.
     */
    abstract protected function setMoreProperties ($messageFormated);

    /**
     * Attach the method to the restServer path, set the type of the message, and the message itself.
     */
    protected function setMessageProperties ($method, array $message)
    {
        $messageFormated = $this->format( $message );
        $this->server_method = $this->restServer . $method;
        $this->setMoreProperties( $messageFormated );
    }

    /**
     * Send or execute(curl notation) the message using a rest method
     */
    public function send ($method, array $message)
    {
        self::setMessageProperties( $method, $message );
        $this->output = curl_exec( $this->ch );
        return $this->output;
    }

    /**
     * Set the message to GET method type
     */
    public function sendGET ($method, array $message)
    {
        curl_setopt( $this->ch, CURLOPT_HTTPGET, true );
        return $this->send( $method, $message );
    }

    /**
     * Set the message to POST method type
     */
    public function sendPOST ($method, array $message)
    {
        curl_setopt( $this->ch, CURLOPT_POST, true );
        return $this->send( $method, $message );
    }

    /**
     * Set the message to PUT method type
     */
    public function sendPUT ($method, array $message)
    {
        curl_setopt( $this->ch, CURLOPT_PUT, true );
        return $this->send( $method, $message );
    }

    /**
     * Set the message to DELETE method type
     */
    public function sendDELETE ($method, array $message)
    {
        curl_setopt( $this->ch, CURLOPT_CUSTOMREQUEST, "DELETE" );
        return $this->send( $method, $message );
    }

    /**
     * Display all the data that the response could got.
     */
    public function displayResponse ()
    {

        $filter = new InputFilter();
        $error = curl_error( $this->ch );
        $error = $filter->xssFilterHard($error);
        $result = array ('header' => '','body' => '','curl_error' => '','http_code' => '','last_url' => ''
        );
        if ($error != "") {
            $result['curl_error'] = $error;
            return $result;
        }
        $response = $this->output;
        $response = $filter->xssFilterHard($response);
        $header_size = curl_getinfo( $this->ch, CURLINFO_HEADER_SIZE );
        $result['header'] = substr( $response, 0, $header_size );
        $result['body'] = substr( $response, $header_size );
        $result['http_code'] = curl_getinfo( $this->ch, CURLINFO_HTTP_CODE );
        $result['last_url'] = curl_getinfo( $this->ch, CURLINFO_EFFECTIVE_URL );
        $result = $filter->xssFilterHard($result);

        $this->type = $filter->xssFilterHard($this->type);
        echo $this->type . " Response: " . $response . "<BR>";
        foreach ($result as $index => $data) {
            if ($data != "") {
                echo $index . "=" . $data . "<BR>";
            }
        }
        echo "<BR>";
    }

    /**
     * Close the Curl session using the Curl Handle set by curl_init() function.
     */
    public function close ()
    {
        curl_close( $this->ch );
    }
}

