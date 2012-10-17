<?php

/**
 * HttpProxyController
 *
 * @author Erik Amaru Ortiz <erik@colosa.com, aortiz.erik@gmail.com>
 * @package gulliver.system
 * @access public
 */
class PMException extends Exception
{

    public function __construct ($message, $code = 0, $previous = null)
    {
        //parent::__construct($message, 1, $previous);
        parent::__construct( $message, 1 );
    }

    public function __toString ()
    {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}

