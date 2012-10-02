<?php

require_once("FormatedMessage.php");

//Class defined to be instanced and handle Json messages
class JsonMessage extends FormatedMessage
{
	//Call the parent Curl initialization and set the type of the message
	public function JsonMessage()
	{   
		parent::__construct();
	    $this->type = "json";
	}
	//Format the array parameter to a json format. 
	protected function format( array $message)
	{
		if ( empty( $message) )
		{		
			return ;
		}
		if ( is_array( $message) ) 
		{
			$jsonMessage = json_encode( $message);
		}
		return $jsonMessage;
	}
}

?>