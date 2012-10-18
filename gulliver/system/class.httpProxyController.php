<?php

/**
 * HttpProxyController
 *
 * @author Erik Amaru Ortiz <erik@colosa.com, aortiz.erik@gmail.com>
 * @package gulliver.system
 * @access private
 */
class HttpProxyController
{

    /**
     *
     * @var array - private array to store proxy data
     */
    private $__data__ = array ();

    /**
     *
     * @var object - private object to store the http request data
     */
    private $__request__;

    public $jsonResponse = true;

    private $sendResponse = true;

    /**
     * Magic setter method
     *
     * @param string $name
     * @param string $value
     */
    public function __set ($name, $value)
    {
        //echo "Setting '$name' to '$value'\n";
        $this->__data__[$name] = $value;
    }

    /**
     * Magic getter method
     *
     * @param string $name
     * @return string or NULL if the internal var doesn't exist
     */
    public function __get ($name)
    {
        //echo "Getting '$name'\n";
        if (array_key_exists( $name, $this->__data__ )) {
            return $this->__data__[$name];
        }

        /*$trace = debug_backtrace();
        trigger_error(
            'Undefined property via __get(): ' . $name .
            ' in ' . $trace[0]['file'] .
            ' on line ' . $trace[0]['line'],
            E_USER_NOTICE);
        return null;*/
    }

    /**
     * Magic isset method
     *
     * @param string $name
     */
    public function __isset ($name)
    {
        //echo "Is '$name' set?\n";
        return isset( $this->__data__[$name] );
    }

    /**
     * Magic unset method
     *
     * @param string $name
     */
    public function __unset ($name)
    {
        //echo "Unsetting '$name'\n";
        unset( $this->__data__[$name] );
    }

    /**
     * call to execute a internal proxy method and handle its exceptions
     *
     * @param string $name
     */
    public function call ($name)
    {
        try {
            $result = $this->$name( $this->__request__ );

            if (! $this->jsonResponse) {
                return null;
            }

            if (! $result) {
                $result = $this->__data__;
            }

        } catch (Exception $e) {
            $result->success = false;
            $result->message = $result->msg = $e->getMessage();
            switch (get_class( $e )) {
                case 'Exception':
                    $error = "SYSTEM ERROR";
                    break;
                case 'PMException':
                    $error = "PROCESSMAKER ERROR";
                    break;
                case 'PropelException':
                    $error = "DATABASE ERROR";
                    break;
                case 'UserException':
                    $error = "USER ERROR";
                    break;
            }
            $result->error = $e->getMessage();

            $result->exception->class = get_class( $e );
            $result->exception->code = $e->getCode();
            $result->exception->trace = $e->getTraceAsString();
        }

        if ($this->sendResponse) {
            print G::json_encode( $result );
        }
    }

    /**
     * Set the http request data
     *
     * @param array $data
     */
    public function setHttpRequestData ($data)
    {
        if (is_array( $data )) {
            while ($var = each( $data )) {
                $this->__request__->$var['key'] = $var['value'];
            }
        } else {
            $this->__request__ = $data;
        }
    }

    public function setJsonResponse ($bool)
    {
        $this->jsonResponse = $bool;
    }

    /**
     * Send response to client
     *
     * @param boolean $val
     */
    public function setSendResponse ($val)
    {
        $this->sendResponse = $val;
    }
}

