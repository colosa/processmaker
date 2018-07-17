<?php

global $RBAC;

use ProcessMaker\Core\System;

if ($RBAC->userCanAccess("PM_SETUP") != 1) {
    G::SendTemporalMessage("ID_USER_HAVENT_RIGHTS_PAGE", "error", "labels");
    exit(0);
}

//Cron status
$bCronIsRunning = false;
$sLastExecution = null;
$processcTimeProcess = 0;
$processcTimeStart   = 0;

$osIsLinux = strtoupper(substr(PHP_OS, 0, 3)) != "WIN";

if (file_exists(PATH_DATA . "cron")) {
    //Windows flag
    //Get data of cron file
    $arrayCron = unserialize(trim(file_get_contents(PATH_DATA . "cron")));

    $bCronIsRunning = (isset($arrayCron['bCronIsRunning']))? (bool)($arrayCron['bCronIsRunning']) : ((isset($arrayCron['flagIsRunning']))? (bool)($arrayCron['flagIsRunning']) : false);
    $sLastExecution = (isset($arrayCron['sLastExecution']))? $arrayCron['sLastExecution'] : ((isset($arrayCron['lastExecution']))? $arrayCron['lastExecution'] : '');

    $processcTimeProcess = (isset($arrayCron["processcTimeProcess"]))? (int)($arrayCron["processcTimeProcess"]) : 10; //Minutes
    $processcTimeStart   = (isset($arrayCron["processcTimeStart"]))? $arrayCron["processcTimeStart"] : 0;
}

if ($osIsLinux) {
    //Linux flag
    //Check if cron it's running
    exec("ps -fea | grep cron.php | grep -v grep", $arrayOutput);

    if (count($arrayOutput) > 0) {
        $bCronIsRunning = true;
    }
}

//if ($bCronIsRunning && $processcTimeStart != 0) {
//    if ((time() - $processcTimeStart) > ($processcTimeProcess * 60)) {
//        //Cron finished his execution for some reason
//        $bCronIsRunning = false;
//    }
//}

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
$oHeadPublisher->assign( "STATUS", $arrayStatus );

G::RenderPage( "publish", "extJs" );
