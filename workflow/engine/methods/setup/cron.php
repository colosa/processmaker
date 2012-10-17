<?php
G::LoadClass( "system" );
G::LoadClass( "wsTools" );
G::LoadClass( "configuration" );

global $RBAC;

if ($RBAC->userCanAccess( "PM_SETUP_ADVANCE" ) != 1) {
    G::SendTemporalMessage( "ID_USER_HAVENT_RIGHTS_PAGE", "error", "labels" );
    exit( 0 );
}

//Cron status
$bCronIsRunning = false;
$sLastExecution = null;
$processcTimeProcess = 0;
$processcTimeStart = 0;

if (file_exists( PATH_DATA . "cron" )) {
    $arrayCron = unserialize( trim( @file_get_contents( PATH_DATA . "cron" ) ) );
    $bCronIsRunning = (boolean) ($arrayCron["bCronIsRunning"]);
    $sLastExecution = $arrayCron["sLastExecution"];
    $processcTimeProcess = (isset( $arrayCron["processcTimeProcess"] )) ? intval( $arrayCron["processcTimeProcess"] ) : 10;
    $processcTimeStart = (isset( $arrayCron["processcTimeStart"] )) ? $arrayCron["processcTimeStart"] : 0;
}

if ($bCronIsRunning && $processcTimeStart != 0) {
    if ((time() - $processcTimeStart) > ($processcTimeProcess * 60)) {
        //Cron finished his execution for some reason
        $bCronIsRunning = false;
    }
}

//Data
$c = new Configurations();
$configPage = $c->getConfiguration( "cronList", "pageSize", null, $_SESSION["USER_LOGGED"] );

$config = array ();
$config["pageSize"] = (isset( $configPage["pageSize"] )) ? $configPage["pageSize"] : 20;

$cronInfo = array ();
$fileLog = PATH_DATA . "log" . PATH_SEP . "cron.log";
$fileLogSize = (file_exists( $fileLog )) ? number_format( filesize( $fileLog ) * (1 / 1024) * (1 / 1024), 4, ".", "" ) : 0;

$cronInfo["status"] = G::LoadTranslation( (($bCronIsRunning) ? "ID_CRON_STATUS_ACTIVE" : "ID_CRON_STATUS_INACTIVE") );
$cronInfo["lastExecution"] = (! empty( $sLastExecution )) ? $sLastExecution : "";
$cronInfo["fileLogName"] = "cron.log";
$cronInfo["fileLogSize"] = $fileLogSize;
$cronInfo["fileLogPath"] = $fileLog;

//Workspaces
$workspaces = System::listWorkspaces();
$arrayAux = array ();

foreach ($workspaces as $index => $workspace) {
    $arrayAux[] = $workspace->name;
}

sort( $arrayAux );

$arrayWorkspace = array ();

foreach ($arrayAux as $index => $value) {
    $arrayWorkspace[] = array ($value,$value
    );
}

array_unshift( $arrayWorkspace, array ("ALL",G::LoadTranslation( "ID_ALL_WORKSPACES" )
) );

//Status
$arrayStatus = array (array ("ALL",G::LoadTranslation( "ID_ALL" )
),array ("COMPLETED",G::LoadTranslation( "COMPLETED" )
),array ("FAILED",G::LoadTranslation( "ID_FAILED" )
)
);

$oHeadPublisher = &headPublisher::getSingleton();
$oHeadPublisher->addContent( "setup/cron" ); //Adding a html file .html
$oHeadPublisher->addExtJsScript( "setup/cron", false ); //Adding a javascript file .js
$oHeadPublisher->assign( "CONFIG", $config );
$oHeadPublisher->assign( "CRON", $cronInfo );
$oHeadPublisher->assign( "WORKSPACE", $arrayWorkspace );
$oHeadPublisher->assign( "STATUS", $arrayStatus );

G::RenderPage( "publish", "extJs" );

