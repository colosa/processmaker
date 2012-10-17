<?php
/**
 * webEntryValidate_Val_assig
 * it gets the assign type for the task
 * with pro_uid and tas_uid
 */

$sPRO_UID = $oData->PRO_UID;
$sTASKS = $oData->TASKS;
$sDYNAFORM = $oData->DYNAFORM;

if (G::is_https())
    $http = 'https://';
else
    $http = 'http://';

$endpoint = $http . $_SERVER['HTTP_HOST'] . '/sys' . SYS_SYS . '/' . SYS_LANG . '/' . SYS_SKIN . '/services/wsdl2';
@$client = new SoapClient( $endpoint );

G::LoadClass( 'Task' );
G::LoadClass( 'User' );
G::LoadClass( 'TaskUser' );

$oTask = new Task();
$TaskFields = $oTask->kgetassigType( $sPRO_UID, $sTASKS );

if ($TaskFields['TAS_ASSIGN_TYPE'] == 'BALANCED')
    echo 1;
else
    echo 0;

?>
