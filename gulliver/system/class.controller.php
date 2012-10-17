<?php

/**
 * Controller Class
 * Implementing MVC Pattern
 *
 * @author Erik Amaru Ortiz <erik@colosa.com, aortiz.erik@gmail.com>
 * @package gulliver.system
 * @access private
 */
class Controller
{
    /**
     *
     * @var boolean debug switch for general purpose
     */
    public $debug = null;
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

    /**
     *
     * @var object - headPublisher object to handle the output
     */
    private $headPublisher = null;

    /**
     *
     * @var string - response type var. possibles values: json|plain
     */
    private $responseType = '';

    /**
     *
     * @var string - layout to pass skinEngine
     */
    private $layout = '';

    /**
     * Magic setter method
     *
     * @param string $name
     * @param string $value
     */
    public function __set ($name, $value)
    {
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
        if (array_key_exists( $name, $this->__data__ )) {
            return $this->__data__[$name];
        }

        $trace = debug_backtrace();
        trigger_error( 'Undefined property via __get(): ' . $name . ' in ' . $trace[0]['file'] . ' on line ' . $trace[0]['line'], E_USER_NOTICE );
        return null;
    }

    /**
     * Magic isset method
     *
     * @param string $name
     */
    public function __isset ($name)
    {
        return isset( $this->__data__[$name] );
    }

    /**
     * Magic unset method
     *
     * @param string $name
     */
    public function __unset ($name)
    {
        unset( $this->__data__[$name] );
    }

    /**
     * Set Response type method
     *
     * @param string $type contains : json|plain
     */
    public function setResponseType ($type)
    {
        $this->responseType = $type;
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
            if ($this->responseType == 'json') {
                print G::json_encode( $result );
            }
        } catch (Exception $e) {
            if ($this->responseType != 'json') {
                $result->exception->class = get_class( $e );
                $result->exception->code = $e->getCode();

                $template = new TemplatePower( PATH_TEMPLATE . 'controller.exception.tpl' );
                $template->prepare();
                $template->assign( 'controller', (function_exists( 'get_called_class' ) ? get_called_class() : 'Controller') );
                $template->assign( 'message', $e->getMessage() );
                $template->assign( 'file', $e->getFile() );
                $template->assign( 'line', $e->getLine() );
                $template->assign( 'trace', $e->getTraceAsString() );

                echo $template->getOutputContent();

            } else {
                $result->success = false;
                $result->msg = $e->getMessage();
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
                $result->error = $error;

                $result->exception->class = get_class( $e );
                $result->exception->code = $e->getCode();
                print G::json_encode( $result );
            }
        }
    }

    /**
     * Set the http request data
     *
     * @param array $data
     */
    public function setHttpRequestData ($data)
    {
        if (! is_object( $this->__request__ )) {
            $this->__request__ = new stdclass();
        }
        if (is_array( $data )) {
            while ($var = each( $data )) {
                $this->__request__->$var['key'] = $var['value'];
            }
        } else {
            $this->__request__ = $data;
        }
    }

    /**
     * Get debug var.
     * method
     *
     * @param boolan $val boolean value for debug var.
     */
    public function setDebug ($val)
    {
        $this->debug = $val;
    }

    /**
     * Get debug var.
     * method
     */
    public function getDebug ()
    {
        if ($this->debug === null) {
            $this->debug = defined( 'DEBUG' ) && DEBUG ? true : false;
        }

        return $this->debug;
    }

    /**
     * * HeadPublisher Functions Binding **
     */

    /**
     * Include a particular extjs library or extension to the main output
     *
     * @param string $srcFile path of a extjs library or extension
     * @param boolean $debug debug flag to indicate if the js output will be minifield or not
     * $debug: true -> the js content will be not minified (readable)
     * false -> the js content will be minified
     */
    public function includeExtJSLib ($srcFile, $debug = false)
    {
        $this->getHeadPublisher()->usingExtJs( $srcFile, ($debug ? $debug : $this->getDebug()) );
    }

    /**
     * Include a javascript file that is using extjs framework to the main output
     *
     * @param string $srcFile path of javascrit file to include
     * @param boolean $debug debug flag to indicate if the js output will be minifield or not
     * $debug: true -> the js content will be not minified (readable)
     * false -> the js content will be minified
     */
    public function includeExtJS ($srcFile, $debug = false)
    {
        $this->getHeadPublisher()->addExtJsScript( $srcFile, ($debug ? $debug : $this->getDebug()) );
    }

    /**
     * Include a Html file to the main output
     *
     * @param string $file path of html file to include to the main output
     */
    public function setView ($file)
    {
        $this->getHeadPublisher()->addContent( $file );
    }

    /**
     * Set variables to be accesible by javascripts
     *
     * @param string $name contains var. name
     * @param string $value conatins var. value
     */
    public function setJSVar ($name, $value)
    {
        $this->getHeadPublisher()->assign( $name, $value );
    }

    /**
     * Set variables to be accesible by the extjs layout template
     *
     * @param string $name contains var. name
     * @param string $value conatins var. value
     */
    public function setVar ($name, $value)
    {
        $this->getHeadPublisher()->assignVar( $name, $value );
    }

    /**
     * method to get the local getHeadPublisher object
     */
    public function getHeadPublisher ()
    {
        if (! is_object( $this->headPublisher )) {
            $this->headPublisher = headPublisher::getSingleton();
        }

        return $this->headPublisher;
    }

    public function setLayout ($layout)
    {
        $this->layout = $layout;
    }

    public function render ($type = 'mvc')
    {
        G::RenderPage( 'publish', $type, null, $this->layout );
    }

    public function header ($header)
    {
        G::header( $header );
    }

    public function redirect ($url)
    {
        G::header( "Location: $url" );
    }
}

