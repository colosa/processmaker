<?php

/**
 * Class WsCreateGroupResponse
 *
 * @package workflow.engine.classes
 */
class WsCreateGroupResponse
{
    public $status_code = 0;
    public $message = '';
    public $groupUID = '';
    public $timestamp = '';

    /**
     * Function __construct
     * Constructor of the class
     *
     * @param string $status
     * @param string $message
     * @param string $groupUID
     * @return void
     */
    function __construct ($status, $message, $groupUID)
    {
        $this->status_code = $status;
        $this->message = $message;
        $this->groupUID = $groupUID;
        $this->timestamp = date( 'Y-m-d H:i:s' );
    }

}
