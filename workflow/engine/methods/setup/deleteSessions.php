<?php

function getAllFiles ($directory, $recursive = true)
{
    $result = array ();
    $handle = opendir( $directory );
    while ($datei = readdir( $handle )) {
        if (($datei != '.') && ($datei != '..')) {
            $file = $directory . $datei;
            if (is_dir( $file )) {
                if ($recursive) {
                    $result = array_merge( $result, getAllFiles( $file . '/' ) );
                }
            } else {
                $result[] = $file;
            }
        }
    }
    closedir( $handle );
    return $result;
}

function getFilesTimestamp ($directory, $recursive = true)
{
    $allFiles = getAllFiles( $directory, $recursive );
    $fileArray = array ();
    foreach ($allFiles as $val) {
        $timeResult['file'] = $val;
        $timeResult['timestamp'] = filemtime( $val );
        $fileArray[] = $timeResult;
    }
    return $fileArray;
}

$currentTime = strtotime( "now" );
$timeDifference = 72 * 60 * 60;
$limitTime = $currentTime - $timeDifference;
$sessionsPath = PATH_DATA . 'session' . PATH_SEP;
$filesResult = getFilesTimestamp( $sessionsPath );
$count = 0;

foreach ($filesResult as $file) {
    if ($file['timestamp'] < $limitTime) {
        unlink( $file['file'] );
        $count ++;
    }
}

if ($count > 0) {
    $response['message'] = G::loadTranslation( 'ID_REMOVED_SESSION_FILES' );
} else {
    $response['message'] = G::loadTranslation( 'ID_NO_OLDER_SESSION_FILES' );
}

echo $response['message'];

