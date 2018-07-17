<?php


/**
 * Class WsCreateDepartmentResponse
 *
 * @package workflow.engine.classes
 */
class WsCreateDepartmentResponse
{
    public $status_code = 0;
    public $message = '';
    public $departmentUID = '';
    public $timestamp = '';

    /**
     * Function __construct
     * Constructor of the class
     *
     * @param string $status
     * @param string $message
     * @param string $departmentUID
     * @return void
     */
    function __construct ($status, $message, $departmentUID)
    {
        $this->status_code = $status;
        $this->message = $message;
        $this->departmentUID = $departmentUID;
        $this->timestamp = date( 'Y-m-d H:i:s' );
    }
}
