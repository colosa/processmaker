<?php
/**
 * showLogoFile.php
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
//  if (($RBAC_Response = $RBAC->userCanAccess("PM_CASES"))!=1) return $RBAC_Response;


$idDecode64 = base64_decode( $_GET['id'] );
$idExploded = explode( '/', $idDecode64 );
if ($idExploded[0] == '')
    array_shift( $idExploded );
if ($idExploded[0] == 'plugin') {
    //Get the Plugin Folder, always the first element
    $pluginFolder = $idExploded[1];
    $pluginFilename = PATH_PLUGINS . $pluginFolder . PATH_SEP . 'public_html' . PATH_SEP . $idExploded[2];
    if (file_exists( $pluginFilename )) {
        G::streamFile( $pluginFilename );
    }
    die();
}

$ainfoSite = explode( "/", $_SERVER["REQUEST_URI"] );
//it was added to show the logo into management plugin add by krlos
if (isset( $_GET['wsName'] ) && $_GET['wsName'] != '') {
    $ainfoSite[1] = $_GET['wsName'];
}
//end add
$dir = PATH_DATA . "sites" . PATH_SEP . str_replace( "sys", "", $ainfoSite[1] ) . PATH_SEP . "files/logos";
$imagen = $dir . PATH_SEP . $idDecode64;

if (is_file( $imagen )) {
    showLogo( $imagen );

} else {

    $newDir = PATH_DATA . "sites" . PATH_SEP . str_replace( "sys", "", $ainfoSite[1] ) . PATH_SEP . "files/logos";
    $dir = PATH_HOME . "public_html/files/logos";

    if (! is_dir( $newDir )) {
        G::mk_dir( $newDir );
    }
    //this function does copy all logos from public_html/files/logos to /shared/site/yourSite/files/logos
    //cpyMoreLogos($dir,$newDir);
    $newDir .= PATH_SEP . $idDecode64;
    $dir .= PATH_SEP . $idDecode64;
    copy( $dir, $newDir );
    showLogo( $newDir );
    die();

}

function showLogo ($imagen)
{
    $info = @getimagesize( $imagen );
    $fp = fopen( $imagen, "rb" );
    if ($info && $fp) {
        header( "Content-type: {$info['mime']}" );
        fpassthru( $fp );
        exit();
    } else {
        throw new Exception( "Image format not valid" );
    }
}

function cpyMoreLogos ($dir, $newDir)
{
    if (file_exists( $dir )) {
        if ($handle = opendir( $dir )) {
            while (false !== ($file = readdir( $handle ))) {
                if (($file != ".") && ($file != "..")) {
                    $extention = explode( ".", $file );
                    $aImageProp = getimagesize( $dir . '/' . $file, $info );
                    $sfileExtention = strtoupper( $extention[count( $extention ) - 1] );
                    if (in_array( $sfileExtention, array ('JPG','JPEG','PNG','GIF'
                    ) )) {

                        $dir1 = $dir . PATH_SEP . $file;
                        $dir2 = $newDir . PATH_SEP . $file;
                        //print $dir1 ."  *** ".$dir2."<br><br>";
                        copy( $dir1, $dir2 );

                    }
                }
            }
            closedir( $handle );
        }
    }
}

die();

?>
<script>

</script>

