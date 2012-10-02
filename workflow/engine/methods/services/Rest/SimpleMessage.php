<?php

require_once("CURLMessage.php");

//Class defined to set enough curl configuration
class SimpleMessage extends CURLMessage
{
	public function SimpleMessage()
	{
		parent::__construct();
	}
	//set the message in order to follow the message format, empty caused this class should not be instanced
	protected function format( array $message)
	{
	}
	//Setting CURL Url, enough to attach a message.
	protected function setMoreProperties( $messageFormated)
	{
	  	//Please, remove this comment, is only for looging proposes.
		//
		echo "Request: ".$this->server_method . PATH_SEP . $messageFormated." <br>";
		//
		curl_setopt($this->ch, CURLOPT_URL, $this->server_method . $messageFormated);
	}
}
?>