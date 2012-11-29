<?php

require_once("JsonMessage.php");
require_once("XmlMessage.php");
require_once("RestMessage.php");

$msg = array( 'user'=>'admin' , 'password'=>'admin');
$method = "login";

$jsonm = new JsonMessage();
$jsonm->send($method,$msg);
$jsonm->displayResponse();

$xmlm = new XmlMessage();
$xmlm->send($method, $msg);
$xmlm->displayResponse();

$msg0 = array( "LABEL", "LOGIN", "en");
$table = "TRANSLATION";

$rest = new RestMessage();
$rest->sendGET($table,$msg0);
$rest->displayResponse();

$msg1 = array( "HOUSE", "PUSHIN", "en", "sample", "2012-06-06" );
$rest->sendPOST($table,$msg1);
$rest->displayResponse();

$msg2 = array( "HOUSE", "PUSHIN", "en", "samplemod", "2012-07-06" );
$rest->sendPUT($table,$msg2);
$rest->displayResponse();

$msg3 = array( "HOUSE", "PUSHIN", "en");
$rest->sendDELETE($table,$msg3);
$rest->displayResponse();

?>
