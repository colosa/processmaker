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
        parent::__construct( $message, 1 );
    }

    public function __toString ()
    {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }

    public static function registerErrorLog($error, $token){
        $ws = (defined("SYS_SYS"))? SYS_SYS : "Wokspace Undefined";
        Bootstrap::registerMonolog('ExceptionCron', 400, $error->getMessage(), array('token'=>$token), $ws, 'processmaker.log');
    }
}
