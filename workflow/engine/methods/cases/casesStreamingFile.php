<?php
$actionAjax = isset( $_REQUEST['actionAjax'] ) ? $_REQUEST['actionAjax'] : null;

if ($actionAjax == "streaming") {

    $app_uid = isset( $_REQUEST['a'] ) ? $_REQUEST['a'] : null;
    $inp_doc_uid = isset( $_REQUEST['d'] ) ? htmlspecialchars($_REQUEST['d']) : null;
    $oAppDocument = new \AppDocument();

    if (! isset( $fileData['version'] )) {
        $docVersion = $oAppDocument->getLastAppDocVersion( $inp_doc_uid );
    } else {
        $docVersion = $fileData['version'];
    }

    $oAppDocument->Fields = $oAppDocument->load( $inp_doc_uid, $docVersion );

    $sAppDocUid  = $oAppDocument->getAppDocUid();
    $iDocVersion = $oAppDocument->getDocVersion();
    $info = pathinfo( $oAppDocument->getAppDocFilename() );
    $ext  = (isset($info['extension'])?$info['extension']:'');

    $file = \G::getPathFromFileUID($oAppDocument->Fields['APP_UID'], $sAppDocUid);

    $realPath  = PATH_DOCUMENT . G::getPathFromUID($app_uid) . '/' . $file[0] . $file[1] . '_' . $iDocVersion . '.' . $ext;
    $realPath1 = PATH_DOCUMENT . G::getPathFromUID($app_uid) . '/' . $file[0] . $file[1] . '.' . $ext;

    if (file_exists( $realPath )) {
        $finfo    = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $realPath);
        finfo_close($finfo);
        if ($ext == "mp3") {
            $mimeType = "audio/mpeg";
        }
        rangeDownload($realPath,$mimeType);
    } elseif (file_exists( $realPath1 )) {
        $finfo    = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $realPath1);
        finfo_close($finfo);
        if ($ext == "mp3") {
            $mimeType = "audio/mpeg";
        }
        rangeDownload($realPath1,$mimeType);
    } else {
        header ("HTTP/1.0 404 Not Found");
        return;
    }
    exit(0);
}

if ($actionAjax == "fileMobile") {
    $app_uid = isset( $_REQUEST['a'] ) ? $_REQUEST['a'] : null;
    $inp_doc_uid = isset( $_REQUEST['d'] ) ? htmlspecialchars($_REQUEST['d']) : null;

    $structure = file_get_contents(PATH_HTML ."/mobile/index.json");
    $structure = json_decode($structure);
    foreach($structure as $build){
        foreach($build as $file){
            $file->lastModified = date ("D, d M Y H:i:s \G\M\T", filemtime(PATH_HTML ."/mobile/".$file->file));
        }
    }
    G::header( 'Content-Type: application/json' );
    echo G::json_encode($structure);
    exit(0);
}

exit;

function rangeDownload($location,$mimeType)
{

    $filter = new InputFilter();
    $location = $filter->xssFilterHard($location, "path");
    if (!file_exists($location))
    {
        header ("HTTP/1.0 404 Not Found");
        return;
    }
    $size  = filesize($location);
    $time  = date('r', filemtime($location));

    $fm = @fopen($location, 'rb');
    if (!$fm)
    {
        header ("HTTP/1.0 505 Internal server error");
        return;
    }

    $begin  = 0;
    $end  = $size - 1;

    if (isset($_SERVER['HTTP_RANGE']))
    {
        if (preg_match('/bytes=\h*(\d+)-(\d*)[\D.*]?/i', $_SERVER['HTTP_RANGE'], $matches))
        {
            $begin  = intval($matches[1]);
            if (!empty($matches[2]))
            {
                $end  = intval($matches[2]);
            }
        }
    }

    header('HTTP/1.0 206 Partial Content');
    header("Content-Type: $mimeType");
    header('Cache-Control: public, must-revalidate, max-age=0');
    header('Pragma: no-cache');
    header('Accept-Ranges: bytes');
    header('Content-Length:' . (($end - $begin) + 1));
    if (isset($_SERVER['HTTP_RANGE']))
    {
        header("Content-Range: bytes $begin-$end/$size");
    }
    header("Content-Disposition: inline; filename=$location");
    header("Content-Transfer-Encoding: binary");
    header("Last-Modified: $time");

    $cur  = $begin;
    fseek($fm, $begin, 0);

    while(!feof($fm) && $cur <= $end && (connection_status() == 0))
    {
        set_time_limit(0);
        print fread($fm, min(1024 * 16, ($end - $cur) + 1));
        $cur += 1024 * 16;
        flush();
    }
}
