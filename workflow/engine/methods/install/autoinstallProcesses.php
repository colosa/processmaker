<?php

use ProcessMaker\Core\Installer;

$inst = new Installer();

$oProcess = new Processes();

//Get Available autoinstall process
$availableProcess = $inst->getDirectoryFiles( PATH_OUTTRUNK . "autoinstall", "pm" );

$path = PATH_OUTTRUNK . "autoinstall" . PATH_SEP;
$message = "";
foreach ($availableProcess as $processfile) {

    $oData = $oProcess->getProcessData( $path . $processfile );
    $Fields['PRO_FILENAME'] = $processfile;
    $Fields['IMPORT_OPTION'] = 2;
    $sProUid = $oData->process['PRO_UID'];
    if ($oProcess->processExists( $sProUid )) {
        $message .= "$processfile - Not imported (process exist)<br>";

    } else {
        $oProcess->createProcessFromData( $oData, $path . $processfile );
        $message .= "$processfile - OK<br>";
    }
}

echo $message;

