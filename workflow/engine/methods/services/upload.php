<?php
/**
 * class.wsBase.php
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

/**
 * @Updated Dec 14, 2009 by Erik <erik@colosa.com>
 *
 * The point of this application is upload the file and create the input document record
 *
 * if the post attached file has error code 0 continue in other case nothing to do.
 */

if (isset( $_FILES ) && $_FILES["ATTACH_FILE"]["error"] == 0) {
    try {
        G::LoadClass( "case" );

        $folderId = "";
        $fileTags = "";

        if (isset( $_POST["DOC_UID"] ) && $_POST["DOC_UID"] != - 1) {
            //The document is of an Specific Input Document. Get path and Tag information
            require_once ("classes/model/AppFolder.php");
            require_once ("classes/model/InputDocument.php");

            $oInputDocument = new InputDocument();
            $aID = $oInputDocument->load( $_POST["DOC_UID"] );

            //Get the Custom Folder ID (create if necessary)
            $oFolder = new AppFolder();
            $folderId = $oFolder->createFromPath( $aID["INP_DOC_DESTINATION_PATH"], $_POST["APPLICATION"] );

            //Tags
            $fileTags = $oFolder->parseTags( $aID["INP_DOC_TAGS"], $_POST["APPLICATION"] );
        }

        $oAppDocument = new AppDocument();

        if (isset( $_POST["APP_DOC_UID"] ) && trim( $_POST["APP_DOC_UID"] ) != "") {
            //Update
            echo "[update]";
            $aFields["APP_DOC_UID"] = $_POST["APP_DOC_UID"];
            $aFields["DOC_VERSION"] = $_POST["DOC_VERSION"];
            $aFields["APP_DOC_FILENAME"] = $_FILES["ATTACH_FILE"]["name"];

            if (isset( $_POST["APPLICATION"] )) {
                $aFields["APP_UID"] = $_POST["APPLICATION"];
            }

            if (isset( $_POST["INDEX"] )) {
                $aFields["DEL_INDEX"] = $_POST["INDEX"];
            }

            if (isset( $_POST["USR_UID"] )) {
                $aFields["USR_UID"] = $_POST["USR_UID"];
            }

            if (isset( $_POST["DOC_UID"] )) {
                $aFields["DOC_UID"] = $_POST["DOC_UID"];
            }

            if (isset( $_POST["APP_DOC_TYPE"] )) {
                $aFields["APP_DOC_TYPE"] = $_POST["APP_DOC_TYPE"];
            }

            $aFields["APP_DOC_CREATE_DATE"] = date( "Y-m-d H:i:s" );
            $aFields["APP_DOC_COMMENT"] = (isset( $_POST["COMMENT"] )) ? $_POST["COMMENT"] : "";
            $aFields["APP_DOC_TITLE"] = (isset( $_POST["TITLE"] )) ? $_POST["TITLE"] : "";

            //$aFields["FOLDER_UID"] = $folderId,
            //$aFields["APP_DOC_TAGS"] = $fileTags


            $aFields["APP_DOC_FIELDNAME"] = $_POST["APP_DOC_FIELDNAME"];
        } else {
            //New record
            $aFields = array ("APP_UID" => $_POST["APPLICATION"],"DEL_INDEX" => $_POST["INDEX"],"USR_UID" => $_POST["USR_UID"],"DOC_UID" => $_POST["DOC_UID"],"APP_DOC_TYPE" => $_POST["APP_DOC_TYPE"],"APP_DOC_CREATE_DATE" => date( "Y-m-d H:i:s" ),"APP_DOC_COMMENT" => (isset( $_POST["COMMENT"] )) ? $_POST["COMMENT"] : "","APP_DOC_TITLE" => (isset( $_POST["TITLE"] )) ? $_POST["TITLE"] : "","APP_DOC_FILENAME" => (isset( $_FILES["ATTACH_FILE"]["name"] )) ? $_FILES["ATTACH_FILE"]["name"] : "","FOLDER_UID" => $folderId,"APP_DOC_TAGS" => $fileTags,"APP_DOC_FIELDNAME" => $_POST["APP_DOC_FIELDNAME"]
            );
        }

        $oAppDocument->create( $aFields );

        $sAppUid = $oAppDocument->getAppUid();
        $sAppDocUid = $oAppDocument->getAppDocUid();
        $iDocVersion = $oAppDocument->getDocVersion();
        $info = pathinfo( $oAppDocument->getAppDocFilename() );
        $ext = (isset( $info["extension"] )) ? $info["extension"] : "";

        //Save the file
        echo $sPathName = PATH_DOCUMENT . $sAppUid . PATH_SEP;
        echo $sFileName = $sAppDocUid . "_" . $iDocVersion . "." . $ext;
        print G::uploadFile( $_FILES["ATTACH_FILE"]["tmp_name"], $sPathName, $sFileName );
        print ("* The file " . $_FILES["ATTACH_FILE"]["name"] . " was uploaded successfully in case " . $sAppUid . " as input document..\n") ;

        //Get current Application Fields
        $application = new Application();
        $appFields = $application->Load( $_POST["APPLICATION"] );
        $appFields = unserialize( $appFields["APP_DATA"] );

        $_SESSION["APPLICATION"] = $appFields["APPLICATION"];
        $_SESSION["PROCESS"] = $appFields["PROCESS"];
        $_SESSION["TASK"] = $appFields["TASK"];
        $_SESSION["INDEX"] = $appFields["INDEX"];
        $_SESSION["USER_LOGGED"] = $appFields["USER_LOGGED"]; //$_POST["USR_UID"]
        //$_SESSION["USR_USERNAME"]  = $appFields["USR_USERNAME"];
        //$_SESSION["STEP_POSITION"] = 0;


        //Plugin Hook PM_UPLOAD_DOCUMENT for upload document
        $oPluginRegistry = &PMPluginRegistry::getSingleton();

        if ($oPluginRegistry->existsTrigger( PM_UPLOAD_DOCUMENT ) && class_exists( "uploadDocumentData" )) {
            $triggerDetail = $oPluginRegistry->getTriggerInfo( PM_UPLOAD_DOCUMENT );
            $documentData = new uploadDocumentData( $_POST["APPLICATION"], $_POST["USR_UID"], $sPathName . $sFileName, $aFields["APP_DOC_FILENAME"], $sAppDocUid, $iDocVersion );
            $uploadReturn = $oPluginRegistry->executeTriggers( PM_UPLOAD_DOCUMENT, $documentData );

            if ($uploadReturn) {
                $aFields["APP_DOC_PLUGIN"] = $triggerDetail->sNamespace;

                if (! isset( $aFields["APP_DOC_UID"] )) {
                    $aFields["APP_DOC_UID"] = $sAppDocUid;
                }

                if (! isset( $aFields["DOC_VERSION"] )) {
                    $aFields["DOC_VERSION"] = $iDocVersion;
                }

                $oAppDocument->update( $aFields );

                unlink( $sPathName . $sFileName );
            }
        }
        //End plugin
    } catch (Exception $e) {
        print ($e->getMessage()) ;
    }
}

