<?php

use ProcessMaker\Core\System;

/**
 * skinsExport.php
 *
 * ProcessMaker Open Source Edition
 * Copyright (C) 2004 - 2008 Colosa Inc.
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

function copyFile ($input, $output)
{
    $content = file_get_contents( $input );
    $filename = $output . PATH_SEP . basename( $input );
    return file_put_contents( $filename, $content );
}

function savePluginFile ($tplName, $fileName, $fields)
{
    $pluginTpl = PATH_GULLIVER_HOME . 'bin' . PATH_SEP . 'tasks' . PATH_SEP . 'templates' . PATH_SEP . $tplName . '.tpl';
    $template = new TemplatePower( $pluginTpl );
    $template->prepare();

    if (is_array( $fields )) {
        foreach ($fields as $block => $data) {
            $template->gotoBlock( "_ROOT" );
            if (is_array( $data ))
            foreach ($data as $rowId => $row) {
                    $template->newBlock( $block );
                    foreach ($row as $key => $val)
                        $template->assign( $key, $val );
            }
            else
                $template->assign( $block, $data );
        }
    }

    $content = $template->getOutputContent();
    $iSize = file_put_contents( $fileName, $content );
    return $iSize;
}

function addTarFolder ($tar, $pathBase, $pluginHome)
{
    $aux = explode( PATH_SEP, $pathBase );
    if ($aux[count( $aux ) - 2] == '.svn')
        return;

    if ($handle = opendir( $pathBase )) {
        while (false !== ($file = readdir( $handle ))) {
            if (is_file( $pathBase . $file )) {
                //print "file $file \n";
                $tar->addModify( $pathBase . $file, '', $pluginHome );
            }
            if (is_dir( $pathBase . $file ) && $file != '..' && $file != '.') {
                //print "dir $pathBase$file \n";
                addTarFolder( $tar, $pathBase . $file . PATH_SEP, $pluginHome );
            }
        }
        closedir( $handle );
    }
}

function packPlugin ($pluginName, $version)
{

    $pathBase = PATH_DATA . 'skins' . PATH_SEP . $pluginName . PATH_SEP;
    $pathHome = PATH_DATA . 'skins' . PATH_SEP . $pluginName;
    $fileTar = PATH_DATA . 'skins' . PATH_SEP . $pluginName . '-' . $version . '.tar';


    /*
    $pluginDirectory    = PATH_PLUGINS  . $pluginName;
    $pluginOutDirectory = PATH_OUTTRUNK . 'plugins' . PATH_SEP . $pluginName;
    $pluginHome         = PATH_OUTTRUNK . 'plugins' . PATH_SEP . $pluginName;

    //verify if plugin exists,
    $pluginClassFilename = PATH_PLUGINS . $pluginName . PATH_SEP . 'class.' . $pluginName . '.php';
    if ( !is_file ( $pluginClassFilename ) ) {
      printf("The plugin %s doesn't exist in this file %s \n", pakeColor::colorize( $pluginName, 'ERROR'), pakeColor::colorize( $pluginClassFilename, 'INFO') );
      die ;
    }
    */

    $tar = new Archive_Tar( $fileTar );
    $tar->_compress = false;

    //$tar->createModify( $pathHome . PATH_SEP . $pluginName . '.php' ,'', $pathHome);
    addTarFolder( $tar, $pathBase, $pathHome );
    $aFiles = $tar->listContent();
    return $fileTar;
}

global $RBAC;
switch ($RBAC->userCanAccess( 'PM_SETUP' )) {
    case - 2:
        G::SendTemporalMessage( 'ID_USER_HAVENT_RIGHTS_SYSTEM', 'error', 'labels' );
        G::header( 'location: ../login/login' );
        die();
        break;
    case - 1:
        G::SendTemporalMessage( 'ID_USER_HAVENT_RIGHTS_PAGE', 'error', 'labels' );
        G::header( 'location: ../login/login' );
        die();
        break;
}

$id = $_GET['id'];

$fileObj = PATH_SKINS . $id . '.cnf';

if (! file_exists( $fileObj )) {
    $oConf = new stdClass();
    $oConf->name = $id;
    $oConf->description = "description of skin $id ";
    $oConf->version = 1;
    file_put_contents( $fileObj, serialize( $oConf ) );
}

$oConf = unserialize( file_get_contents( $fileObj ) );
$oConf->version += 1;
file_put_contents( $fileObj, serialize( $oConf ) );

$pathHome = PATH_DATA . 'skins' . PATH_SEP . $id . PATH_SEP;
$pathBase = PATH_DATA . 'skins' . PATH_SEP . $id . PATH_SEP . $id . PATH_SEP;
$pathPublic = $pathBase . 'data' . PATH_SEP . 'public_html' . PATH_SEP;
$pathImages = PATH_HTML . 'skins' . PATH_SEP . $id . PATH_SEP . 'images' . PATH_SEP;

G::mk_dir( $pathBase );
G::mk_dir( $pathBase . 'data' );
G::mk_dir( $pathPublic );
G::mk_dir( $pathPublic . 'images' );

//  file_put_contents ( PATH_DATA . 'skins' . PATH_SEP . $id  , "hello world" );
$fields['className'] = $id;
$fields['version'] = $oConf->version;
$fields['description'] = $oConf->description;
$fields['PMversion'] = System::getVersion();
savePluginFile( 'skinPluginMainClass', $pathHome . $id . '.php', $fields );

savePluginFile( 'skinPluginClass', $pathBase . 'class.' . $id . '.php', $fields );

copyFile( PATH_SKINS . $id . '.php', $pathBase . 'data' );
copyFile( PATH_SKINS . $id . '.html', $pathBase . 'data' );
copyFile( PATH_SKINS . $id . '.cnf', $pathBase . 'data' );

copyFile( PATH_HTML . 'skins' . PATH_SEP . $id . PATH_SEP . 'iepngfix.htc', $pathPublic );
copyFile( PATH_HTML . 'skins' . PATH_SEP . $id . PATH_SEP . 'style.css', $pathPublic );

$aFiles = array ();
if ($handle = opendir( $pathImages )) {
    while (false !== ($file = readdir( $handle ))) {
        if (substr( $file, 0, 1 ) != '.') {
            if (isset( $aFiles[$file] ))
                $aFiles[$file] = 0;
            copyFile( $pathImages . $file, $pathPublic . 'images' . PATH_SEP );

        }
    }
    closedir( $handle );
}

$fileTar = packPlugin( $id, $oConf->version );

$bDownload = true;
G::streamFile( $fileTar, $bDownload, basename( $fileTar ) );

@G::rm_dir( $pathHome );
@unlink( $fileTar );

