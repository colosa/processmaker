<?php

class WsGetCaseNotesResponse
{
    public $status_code = 0;
    public $message = '';
    public $notes = null;
    public $timestamp = '';

    /**
     * Function __construct
     * Constructor of the class
     *
     * @param string $status
     * @param string $message
     * @param array|object|string $notes
     * @return void
     */
    function __construct ($status, $message, $notes)
    {
        $this->status_code = $status;
        $this->message = $message;
        $this->notes = $notes;
        $this->timestamp = date( 'Y-m-d H:i:s' );
    }
}
