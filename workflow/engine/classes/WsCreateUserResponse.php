<?php

class WsCreateUserResponse
{
    public $status_code = 0;
    public $message = '';
    public $userUID = '';
    public $timestamp = '';

    /**
     * Function __construct
     * Constructor of the class
     *
     * @param string $status
     * @param string $message
     * @param string $userUID
     * @return void
     */
    function __construct ($status, $message, $userUID)
    {
        $this->status_code = $status;
        $this->message = $message;
        $this->userUID = $userUID;
        $this->timestamp = date( 'Y-m-d H:i:s' );
    }
}
