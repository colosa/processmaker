<?php
$RBAC->allows(basename(__FILE__), 'downloadFileHash');

if (!isset($_GET["file_hash"])) {
    throw new Exception("Invalid Request, param 'file_hash' was not sent.");
}

$httpStream = new \ProcessMaker\Util\IO\HttpStream();
$outputDir = PATH_DATA . "sites" . PATH_SEP . SYS_SYS . PATH_SEP . "files" . PATH_SEP . "output" . PATH_SEP;
$fileName = urldecode(base64_decode($_GET["file_hash"]));
$processFile = $outputDir . $fileName;

//Verify if the file related to process exist in the corresponding path
$fileInformation = pathinfo($processFile);
$processFile = $outputDir . $fileInformation['basename'];
if (!file_exists($processFile)) {
    throw new Exception("Error, couldn't find request file: $fileName");
}
$fileExtension = $fileInformation['extension'];
$httpStream->loadFromFile($processFile);
$httpStream->setHeader("Content-Type", "application/$fileExtension");
$httpStream->send();
