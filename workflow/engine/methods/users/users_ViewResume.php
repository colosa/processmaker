<?php
/**
 * users_ViewResume.php
 *
 * ProcessMaker Open Source Edition
 * Copyright (C) 2004 - 2008 Colosa Inc.23
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 * For more information, contact Colosa Inc, 2566 Le Jeune Rd.,
 * Coral Gables, FL, 33134, USA, or email info@colosa.com.
 */
if (($RBAC_Response = $RBAC->userCanAccess( "PM_LOGIN" )) != 1)
    return $RBAC_Response;
G::LoadClass( "user" );

$uid = (isset( $_SESSION['CURRENT_USER'] ) ? $_SESSION['CURRENT_USER'] : $_SESSION['USER_LOGGED']);
require_once 'classes/model/Users.php';
$oUser = new Users();
$form = $oUser->load( $uid );
if (! isset( $form['USR_RESUME'] ) || $form['USR_RESUME'] === '')
    die( G::LoadTranslation( 'ID_WITHOUT_RESUME' ) );
$direction = PATH_IMAGES_ENVIRONMENT_FILES . $uid . "/" . $form['USR_RESUME'];
if (! file_exists( $direction )) {
    die( 'The file "' . $direction . '"doesn\'t exist in the server!' );
}
//	echo $direction ;
header( 'Pragma: ' );
header( 'Cache-Control: cache' );

G::sendHeaders( $direction );
readfile( $direction );
//DumpHeaders($direction);


/*
 * This function is verified to work with Netscape and the *very latest*
 * version of IE.  I don't know if it works with Opera, but it should now.
 */
function DumpHeaders ($filename)
{

    global $root_path;

    if (! $filename)
        return;

    $HTTP_USER_AGENT = $_SERVER['HTTP_USER_AGENT'];

    $isIE = 0;

    if (strstr( $HTTP_USER_AGENT, 'compatible; MSIE ' ) !== false && strstr( $HTTP_USER_AGENT, 'Opera' ) === false) {
        $isIE = 1;
    }

    if (strstr( $HTTP_USER_AGENT, 'compatible; MSIE 6' ) !== false && strstr( $HTTP_USER_AGENT, 'Opera' ) === false) {
        $isIE6 = 1;
    }

    // The ereg_replace function has been DEPRECATED as of PHP 5.3.0.
    // $aux = ereg_replace('[^-a-zA-Z0-9\.]', '_', $filename);
    $aux = preg_replace( '/[^-a-zA-Z0-9\.]/', '_', $filename );
    $aux = explode( '_', $aux );
    $downloadName = $aux[count( $aux ) - 1];
    //  $downloadName = $filename;


    //$downloadName = ereg_replace('[^-a-zA-Z0-9\.]', '_', $filename);


    if ($isIE && ! isset( $isIE6 )) {
        // http://support.microsoft.com/support/kb/articles/Q182/3/15.asp
        // Do not have quotes around filename, but that applied to
        // "attachment"... does it apply to inline too?


        // This combination seems to work mostly.  IE 5.5 SP 1 has
        // known issues (see the Microsoft Knowledge Base)
        header( "Content-Disposition: inline; filename=$downloadName" );

        // This works for most types, but doesn't work with Word files
        header( "Content-Type: application/download; name=\"$downloadName\"" );

        //header("Content-Type: $type0/$type1; name=\"$downloadName\"");
        //header("Content-Type: application/x-msdownload; name=\"$downloadName\"");
        //header("Content-Type: application/octet-stream; name=\"$downloadName\"");
    } else {
        header( "Content-Disposition: attachment; filename=\"$downloadName\"" );
        header( "Content-Type: application/octet-stream; name=\"$downloadName\"" );
    }

    //$filename = PATH_UPLOAD . "$filename";
    readfile( $filename );
}

//G::header2( "location: /files/" .$_SESSION['ENVIRONMENT']. "/" .$appid, $filename);

