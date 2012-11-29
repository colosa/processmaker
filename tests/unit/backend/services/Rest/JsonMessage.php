<?php
    /**
    * Class defined to be instanced and handle Json messages.
    *
    *
    * @category   Zend
    * @package    ProcessMaker
    * @subpackage workflow
    * @copyright  Copyright (c) ProcessMaker  Colosa Inc.
    * @version    Release: @2.0.44@
    * @since      Class available since Release 2.0.44
    */
    
require_once("FormatedMessage.php");
/**
* Class defined to be instanced and handle Json messages
*/
class JsonMessage extends FormatedMessage
{
    /**
    * Call the parent Curl initialization and set the type of the message
    */
    public function JsonMessage()
    {   
        parent::__construct();
        $this->type = "json";
    }
    /**
    * Format the array parameter to a json format. 
    */
    protected function format(array $message)
    {
        if (empty($message)){		
            return ;
        }
        if (is_array($message)){
            $jsonMessage = json_encode( $message);
        }
        return $jsonMessage;
    }
}

?>