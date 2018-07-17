<?php

class WsGetVariableResponse
{
    public $status_code = 0;
    public $message = '';
    public $variables = null;
    public $timestamp = '';

    /**
     * Function __construct
     * Constructor of the class
     *
     * @param string $status
     * @param string $message
     * @param string $variables
     * @return void
     */
    function __construct ($status, $message, $variables)
    {
        $this->status_code = $status;
        $this->message = $message;
        $this->variables = $variables;
        $this->timestamp = date( 'Y-m-d H:i:s' );
    }
}
