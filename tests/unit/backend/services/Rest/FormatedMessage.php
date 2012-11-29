<?php
    /**
    * Class defined to set all the configuration that XML, Json or other needs.
    *
    *
    * @category   Zend
    * @package    ProcessMaker
    * @subpackage workflow
    * @copyright  Copyright (c) ProcessMaker  Colosa Inc.
    * @version    Release: @2.0.44@
    * @since      Class available since Release 2.0.44
    */
    
require_once("CURLMessage.php");

/**
* Class defined to set all the configuration that XML, Json or other needs.
*/
class FormatedMessage extends CURLMessage
{
    public function FormatedMessage()
    {
        parent::__construct();
    }
    /**
    * Set the message in order to follow the message format, empty caused this class should not be instanced
    */
    protected function format(array $message)
    {
    }
    /**
    * Setting CURL Url, Content and the message to be send
    */
    protected function setMoreProperties($messageFormated)
    {
        //Please, remove this comment, is only for looging proposes.
        //
        echo "Request: " . $this->server_method . PATH_SEP . $messageFormated . " <br>";
        //
        curl_setopt($this->ch,CURLOPT_URL,$this->server_method);
        $contentSelected = $this->content . $this->type;
        curl_setopt($this->ch,CURLOPT_HTTPHEADER,array($contentSelected));
        curl_setopt($this->ch,CURLOPT_POSTFIELDS,$messageFormated);
    }
}
?>