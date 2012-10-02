<?php

require_once("SimpleMessage.php");

//Class defined to be instanced and handle rest single parameters
class RestMessage extends SimpleMessage
{
	//call the parent Curl initialization and set the type of the message
	public function RestMessage()
	{
		parent::__construct();
	    $this->type = "rest";
	}
	//Format the array parameter to a single rest line format separed by '/'.
	protected function format( array $message)
	{
	    $rest = "";
		if ( !empty( $message) )
		{		
			if ( is_array( $message) )
			{
				foreach( $message as $index => $data)
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
