<?php
/**
 * process_webEntryValidate
 * validates if the username and password are valid data and if the user assigned
 * to the webentry has the rights and persmissions required
 */

$sPRO_UID = $oData->PRO_UID;
$sTASKS = $oData->TASKS;
$sTASKS_SEL = $oData->TASKS_NAME;
//echo $sTASKS."<br>";
$sDYNAFORM = $oData->DYNAFORM;
$sWE_TYPE = $oData->WE_TYPE;
$sWS_USER = trim( $oData->WS_USER );
$sWS_PASS = trim( $oData->WS_PASS );
$sWS_ROUNDROBIN = $oData->WS_ROUNDROBIN;
$sWE_USR = $oData->WE_USR;

//echo ($sPRO_UID."<br>");
//echo ($sTASKS."<br>");
//echo ($sDYNAFORM."<br>");


if (G::is_https())
    $http = 'https://';
else
    $http = 'http://';

$endpoint = $http . $_SERVER['HTTP_HOST'] . '/sys' . SYS_SYS . '/' . SYS_LANG . '/' . SYS_SKIN . '/services/wsdl2';
@$client = new SoapClient( $endpoint );

$user = $sWS_USER;
$pass = $sWS_PASS;

$params = array ('userid' => $user,'password' => $pass
);
$result = $client->__SoapCall( 'login', array ($params
) );

//$_SESSION ['WS_SESSION_ID'] = '';


//if ($result->status_code == 0) {
//    $_SESSION ['WS_SESSION_ID'] = $result->message;
//}


$fields['status_code'] = $result->status_code;
$fields['message'] = 'ProcessMaker WebService version: ' . $result->version . "\n" . $result->message;
$fields['version'] = $result->version;
$fields['time_stamp'] = $result->timestamp;
$messageCode = 1;

G::LoadClass( 'Task' );
G::LoadClass( 'User' );
G::LoadClass( 'TaskUser' );
G::LoadClass( 'Groupwf' );
/**
 * note added by gustavo cruz gustavo-at-colosa-dot-com
 * This is a little check to see if the GroupUser class has been declared or not.
 * Seems that the problem its present in a windows installation of PM however.
 * It's seems that could be replicated in a Linux server easily.
 * I recomend that in some way check already if a imported class is declared
 * somewhere else or maybe delegate the task to the G Class LoadClass method.
 */
if (! class_exists( 'GroupUser' )) {
    G::LoadClass( 'GroupUser' );
}
// if the user has been authenticated, then check if has the rights or
// permissions to create the webentry
if ($result->status_code == 0) {
    $oCriteria = new Criteria( 'workflow' );
    $oCriteria->addSelectColumn( UsersPeer::USR_UID );
    $oCriteria->addSelectColumn( TaskUserPeer::USR_UID );
    $oCriteria->addSelectColumn( TaskUserPeer::TAS_UID );
    $oCriteria->addJoin( TaskUserPeer::USR_UID, UsersPeer::USR_UID, Criteria::LEFT_JOIN );
    $oCriteria->add( TaskUserPeer::TAS_UID, $sTASKS );
    $oCriteria->add( UsersPeer::USR_USERNAME, $sWS_USER );
    //$oCriteria->add(TaskUserPeer::TU_RELATION,1);
    $userIsAssigned = TaskUserPeer::doCount( $oCriteria );
    // if the user is not assigned directly, maybe a have the task a group with the user
    if ($userIsAssigned < 1) {
        $oCriteria = new Criteria( 'workflow' );
        $oCriteria->addSelectColumn( UsersPeer::USR_UID );
        $oCriteria->addJoin( UsersPeer::USR_UID, GroupUserPeer::USR_UID, Criteria::LEFT_JOIN );
        $oCriteria->addJoin( GroupUserPeer::GRP_UID, TaskUserPeer::USR_UID, Criteria::LEFT_JOIN );
        $oCriteria->add( TaskUserPeer::TAS_UID, $sTASKS );
        $oCriteria->add( UsersPeer::USR_USERNAME, $sWS_USER );
        $userIsAssigned = GroupUserPeer::doCount( $oCriteria );
        if (! ($userIsAssigned >= 1)) {
            $messageCode = "The User \"" . $sWS_USER . "\" doesn't have the task \"" . $sTASKS_SEL . "\" assigned";
        }
    }

} else {
    $messageCode = $result->message;
}

echo ($messageCode);
?>
