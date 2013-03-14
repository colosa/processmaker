<?php
/**
 * processes_Export.php
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

//G::LoadThirdParty( 'pear/json', 'class.json' );

try {

    function myTruncate ($chain, $limit, $break = '.', $pad = '...')
    {
        if (strlen( $chain ) <= $limit) {
            return $chain;
        }
        $breakpoint = strpos( $chain, $break, $limit );
        if (false !== $breakpoint) {
            $len = strlen( $chain ) - 1;
            if ($breakpoint < $len) {
                $chain = substr( $chain, 0, $breakpoint ) . $pad;
            }
        }
        return $chain;
    }

    function addTitlle ($Category, $Id, $Lang)
    {
        require_once 'classes/model/Content.php';
        $content = new Content();
        $value = $content->load( $Category, '', $Id, $Lang );
        return $value;
    }

    //$oJSON = new Services_JSON();
    $stdObj = Bootstrap::json_decode( $_POST['data'] );
    if (isset( $stdObj->pro_uid ))
        $sProUid = $stdObj->pro_uid;
    else
        throw (new Exception( G::LoadTranslation('ID_PROCESS_UID_NOT_DEFINED') ));

        /* Includes */
    G::LoadClass( 'processes' );
    $oProcess = new Processes();
    $proFields = $oProcess->serializeProcess( $sProUid );
    $Fields = $oProcess->saveSerializedProcess( $proFields );
    $pathLength = strlen( PATH_DATA . "sites" . PATH_SEP . SYS_SYS . PATH_SEP . "files" . PATH_SEP . "output" . PATH_SEP );
    $length = strlen( $Fields['PRO_TITLE'] ) + $pathLength;

    foreach ($Fields as $key => $value) {
        if ($key == 'PRO_TITLE') {
            $Fields[$key] = myTruncate( $value, 65, ' ', '...' );
        }
        if ($key == 'FILENAME') {
            $Fields[$key] = myTruncate( $value, 60, '_', '...pm' );
        }
        if (($length) >= 250) {
            if ($key == 'FILENAME_LINK') {
                list ($file, $rest) = explode( 'p=', $value );
                list ($filenameLink, $rest) = explode( '&', $rest );
                $Fields[$key] = myTruncate( $filenameLink, 250 - $pathLength, '_', '' );
                $Fields[$key] = $file . "p=" . $Fields[$key] . '&' . $rest;
            }
        }
    }

    /* Render page */
    if (isset( $_REQUEST["processMap"] ) && $_REQUEST["processMap"] == 1) {
        $G_PUBLISH = new Publisher();
        $G_PUBLISH->AddContent( "xmlform", "xmlform", "processes/processes_Export", "", $Fields );

        G::RenderPage( "publish", "raw" );
    } else {
        $xmlFrm = new XmlForm();
        $xmlFrm->home = PATH_XMLFORM . "processes" . PATH_SEP;
        $xmlFrm->parseFile( "processes_Export.xml", SYS_LANG, true );

        $Fields["xmlFrmFieldLabel"] = array ("title" => $xmlFrm->fields["TITLE"]->label,"proTitle" => $xmlFrm->fields["PRO_TITLE"]->label,"proDescription" => $xmlFrm->fields["PRO_DESCRIPTION"]->label,"size" => $xmlFrm->fields["SIZE"]->label,"fileName" => $xmlFrm->fields["FILENAME_LABEL"]->label
        );

        echo G::json_encode( $Fields );
    }
} catch (Exception $e) {
    $G_PUBLISH = new Publisher();
    $aMessage['MESSAGE'] = $e->getMessage();
    $G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'login/showMessage', '', $aMessage );
    G::RenderPage( 'publish', 'raw' );
}
