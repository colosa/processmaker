<?php

use ProcessMaker\Plugins\PluginRegistry;

/**
 * The point of this application is upload the file and create the input document record
 * if the post attached file has error code 0 continue in other case nothing to do.
 */
if (isset($_FILES) && $_FILES["ATTACH_FILE"]["error"] == 0) {
    try {
        $application = new Application();
        if (!$application->exists($_POST["APPLICATION"])) {
            throw new Exception(G::LoadTranslation("ID_CASE_NOT_EXISTS") . ": {$_POST['APPLICATION']}");
        }

        $folderId = "";
        $fileTags = "";

        if (isset($_POST["DOC_UID"]) && $_POST["DOC_UID"] != - 1) {
            //The document is of an Specific Input Document. Get path and Tag information
            require_once("classes/model/AppFolder.php");
            require_once("classes/model/InputDocument.php");

            $oInputDocument = new InputDocument();
            $aID = $oInputDocument->load($_POST["DOC_UID"]);

            //Get the Custom Folder ID (create if necessary)
            $oFolder = new AppFolder();
            $folderId = $oFolder->createFromPath($aID["INP_DOC_DESTINATION_PATH"], $_POST["APPLICATION"]);

            //Tags
            $fileTags = $oFolder->parseTags($aID["INP_DOC_TAGS"], $_POST["APPLICATION"]);
        }

        $appDocument = new AppDocument();

        if (isset($_POST["APP_DOC_UID"]) && trim($_POST["APP_DOC_UID"]) != "") {
            //Update
            echo "[update]";
            $aFields["APP_DOC_UID"] = $_POST["APP_DOC_UID"];
            $aFields["DOC_VERSION"] = $_POST["DOC_VERSION"];
            $aFields["APP_DOC_FILENAME"] = $_FILES["ATTACH_FILE"]["name"];

            if (isset($_POST["APPLICATION"])) {
                $aFields["APP_UID"] = $_POST["APPLICATION"];
            }

            if (isset($_POST["INDEX"])) {
                $aFields["DEL_INDEX"] = $_POST["INDEX"];
            }

            if (isset($_POST["USR_UID"])) {
                $aFields["USR_UID"] = $_POST["USR_UID"];
            }

            if (isset($_POST["DOC_UID"])) {
                $aFields["DOC_UID"] = $_POST["DOC_UID"];
            }

            if (isset($_POST["APP_DOC_TYPE"])) {
                $aFields["APP_DOC_TYPE"] = $_POST["APP_DOC_TYPE"];
            }

            $aFields["APP_DOC_CREATE_DATE"] = date("Y-m-d H:i:s");
            $aFields["APP_DOC_COMMENT"] = (isset($_POST["COMMENT"])) ? $_POST["COMMENT"] : "";
            $aFields["APP_DOC_TITLE"] = (isset($_POST["TITLE"])) ? $_POST["TITLE"] : "";

            //$aFields["FOLDER_UID"] = $folderId,
            //$aFields["APP_DOC_TAGS"] = $fileTags

            $aFields["APP_DOC_FIELDNAME"] = $_POST["APP_DOC_FIELDNAME"];
        } else {
            //New record
            $aFields = array(
                "APP_UID" => $_POST["APPLICATION"],
                "DEL_INDEX" => $_POST["INDEX"],
                "USR_UID" => $_POST["USR_UID"],
                "DOC_UID" => $_POST["DOC_UID"],
                "APP_DOC_TYPE" => $_POST["APP_DOC_TYPE"],
                "APP_DOC_CREATE_DATE" => date("Y-m-d H:i:s"),
                "APP_DOC_COMMENT" => (isset($_POST["COMMENT"])) ? $_POST["COMMENT"] : "",
                "APP_DOC_TITLE" => (isset($_POST["TITLE"])) ? $_POST["TITLE"] : "",
                "APP_DOC_FILENAME" => (isset($_FILES["ATTACH_FILE"]["name"])) ? $_FILES["ATTACH_FILE"]["name"] : "",
                "FOLDER_UID" => $folderId, "APP_DOC_TAGS" => $fileTags,
                "APP_DOC_FIELDNAME" => (isset($_POST["APP_DOC_FIELDNAME"])) ? $_POST["APP_DOC_FIELDNAME"] : ((isset($_FILES["ATTACH_FILE"]["name"])) ? $_FILES["ATTACH_FILE"]["name"] : "")
            );
        }

        $appDocument->create($aFields);

        $sAppUid = $appDocument->getAppUid();
        $sAppDocUid = $appDocument->getAppDocUid();
        $iDocVersion = $appDocument->getDocVersion();
        $info = pathinfo($appDocument->getAppDocFilename());
        $ext = (isset($info["extension"])) ? $info["extension"] : "";

        //Save the file
        echo $sPathName = PATH_DOCUMENT . G::getPathFromUID($sAppUid) . PATH_SEP;
        echo $sFileName = $sAppDocUid . "_" . $iDocVersion . "." . $ext;
        print G::uploadFile($_FILES["ATTACH_FILE"]["tmp_name"], $sPathName, $sFileName);
        print("* The file " . $_FILES["ATTACH_FILE"]["name"] . " was uploaded successfully in case " . $sAppUid . " as input document..\n");

        //set variable for APP_DOC_UID
        $appUid = $_POST['APPLICATION'];
        $case = new Cases();
        $fields = $case->loadCase($appUid);
        $fields['APP_DATA'][$appDocument->getAppDocFieldname()] = G::json_encode([$appDocument->getAppDocUid()]);
        $fields['APP_DATA'][$appDocument->getAppDocFieldname() . '_label'] = G::json_encode([$appDocument->getAppDocFilename()]);
        $case->updateCase($appUid, $fields);

        $_SESSION["APPLICATION"] = $fields['APP_DATA']["APPLICATION"];
        $_SESSION["PROCESS"] = $fields['APP_DATA']["PROCESS"];
        $_SESSION["TASK"] = $fields['APP_DATA']["TASK"];
        $_SESSION["INDEX"] = $fields['APP_DATA']["INDEX"];
        $_SESSION["USER_LOGGED"] = $fields['APP_DATA']["USER_LOGGED"];

        //Plugin Hook PM_UPLOAD_DOCUMENT for upload document
        $oPluginRegistry = PluginRegistry::loadSingleton();

        if ($oPluginRegistry->existsTrigger(PM_UPLOAD_DOCUMENT) && class_exists("uploadDocumentData")) {
            $triggerDetail = $oPluginRegistry->getTriggerInfo(PM_UPLOAD_DOCUMENT);
            $documentData = new uploadDocumentData($_POST["APPLICATION"], $_POST["USR_UID"], $sPathName . $sFileName, $aFields["APP_DOC_FILENAME"], $sAppDocUid, $iDocVersion);
            $uploadReturn = $oPluginRegistry->executeTriggers(PM_UPLOAD_DOCUMENT, $documentData);

            if ($uploadReturn) {
                $aFields["APP_DOC_PLUGIN"] = $triggerDetail->getNamespace();

                if (!isset($aFields["APP_DOC_UID"])) {
                    $aFields["APP_DOC_UID"] = $sAppDocUid;
                }

                if (!isset($aFields["DOC_VERSION"])) {
                    $aFields["DOC_VERSION"] = $iDocVersion;
                }

                $appDocument->update($aFields);

                unlink($sPathName . $sFileName);
            }
        }
        //End plugin
    } catch (Exception $e) {
        $token = strtotime("now");
        PMException::registerErrorLog($e, $token);
        G::outRes(G::LoadTranslation("ID_EXCEPTION_LOG_INTERFAZ", array($token)));
    }
}
