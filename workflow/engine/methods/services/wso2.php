<?php

function wsBaseLogin ($username, $password)
{
    G::LoadClass( 'wsBase' );
    $ws = new wsBase();
    $res = $ws->login( $username, $password );
    return $res->getPayloadArray();
}

function wsBaseProcessList ($studentName)
{
    G::LoadClass( 'wsBase' );
    $ws = new wsBase();
    $result = $ws->processList();
    //$result[] = array ( 'guid' => 'a' . $studentName , 'name' => 'bc' );
    //$result[] = array ( 'guid' => '2a' , 'name' => '2bc' .  $studentName );
    //$result[] = array ( 'guid' => '2a' , 'name' => '2bc' .  $studentName );
    return array ("processes" => $result
    );
    //return array ( "status_code" => 12, "message" => 'abx', "timestamp" => 'aa' );
}

/* Map of the service operation "ExamResult" to php function "ExamResult" */
$operations = array ("processesList" => "wsBaseProcessList","login" => "wsBaseLogin"
);

/* just tell your function parameters should be in mixed format,
   that is here parameter will be the string with the name in it*/

$opParams = array ("wsBaseProcessList" => "MIXED","wsBaseLogin" => "MIXED"
);

//$wsdl = PATH_METHODS . "services" . PATH_SEP . "pmos.wsdl";
$wsdl = "/home/fernando/processmaker/trunk/workflow/engine/methods/services/pmos.wsdl";
echo $wsdl;
echo file_get_contents( $wsdl );
die();
/* Created the WSService */
$svr = new WSService( array ("wsdl" => $wsdl,"operations" => $operations,"opParams" => $opParams
) );

/* Reply the client */
$svr->reply();

die();

