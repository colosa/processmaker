<?php
    /**
    * Class defined to be instanced and handle rest single parameters.
    *
    *
    * @category   Zend
    * @package    ProcessMaker
    * @subpackage workflow
    * @copyright  Copyright (c) ProcessMaker  Colosa Inc.
    * @version    Release: @2.0.44@
    * @since      Class available since Release 2.0.44
    */
    
require_once("SimpleMessage.php");
/**
* Class defined to be instanced and handle rest single parameters
*/
class RestMessage extends SimpleMessage
{
    /**
    * Call the parent Curl initialization and set the type of the message
    */
    public function RestMessage()
    {
        parent::__construct();
        $this->type = "rest";
    }
    /**
    * Format the array parameter to a single rest line format separed by '/'.
    */
    protected function format(array $message)
    {
        $rest = "";
        if (!empty($message)){		
            if (is_array($message)){
                foreach($message as $index => $data)
                {
                    $rest .= "/" . $data;
                }
                $rest .= "/";
            }
        }
        return $rest;
    }
}

?>
