<?php
require_once 'classes/model/OutputDocument.php';
$uid = $_SERVER['QUERY_STRING'];
$oOutputDocument = new OutputDocument();
$aFields = $oOutputDocument->load( $uid );
$type = $aFields['OUT_DOC_TYPE'];

if ($type == 'JRXML') {
    $extension = 'jrxml';
}
if ($type == 'ACROFORM') {
    $extension = 'pdf';
}

$fileJrxml = PATH_DYNAFORM . $aFields['PRO_UID'] . PATH_SEP . $aFields['OUT_DOC_UID'] . '.' . $extension;

$bDownload = true;
// The ereg_replace function has been DEPRECATED as of PHP 5.3.0.
// $downFileName = ereg_replace('[^A-Za-z0-9_]', '_', $aFields['OUT_DOC_TITLE'] ) . '.' . $extension;

$downFileName = preg_replace( '/[^A-Za-z0-9_]/i', '_', $aFields['OUT_DOC_TITLE'] ) . '.' . $extension;
G::streamFile( $fileJrxml, $bDownload, $downFileName );

