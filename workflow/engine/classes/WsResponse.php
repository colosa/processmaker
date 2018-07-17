<?php

class WsResponse
{
    public $status_code = 0;
    public $message = '';
    public $timestamp = '';

    /**
     * Function __construct
     * Constructor of the class
     *
     * @param string $status
     * @param string $message
     * @return void
     */
    function __construct ($status, $message)
    {
        $this->status_code = $status;
        $this->message = $message;
        $this->timestamp = date( 'Y-m-d H:i:s' );
    }

    /**
     * Function getPayloadString
     *
     * @param string $operation
     * @return string
     */
    function getPayloadString ($operation)
    {
        $res = "<$operation>\n";
        $res .= "<status_code>" . $this->status_code . "</status_code>";
        $res .= "<message>" . $this->message . "</message>";
        $res .= "<timestamp>" . $this->timestamp . "</timestamp>";
        //    $res .= "<array>" . $this->timestamp . "</array>";
        $res .= "<$operation>";
        return $res;
    }

    /**
     * Function getPayloadArray
     *
     * @return array
     */
    function getPayloadArray ()
    {
        return array ("status_code" => $this->status_code,'message' => $this->message,'timestamp' => $this->timestamp
        );
    }
}
