<?php

/**
 * cases_SaveDocument.php
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
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * For more information, contact Colosa Inc, 2566 Le Jeune Rd.,
 * Coral Gables, FL, 33134, USA, or email info@colosa.com.
 *
 */
try {
    $docUid     = $_POST['form']['DOC_UID'];
    $appDocUid  = $_POST['form']['APP_DOC_UID'];
    $docVersion = $_POST['form']['docVersion'];
    $actionType = $_POST['form']['actionType'];

    $appId   = $_GET['appId'];
    $docType = isset($_GET['docType']) ? $_GET['docType'] : "";

    //save info

    require_once ("classes/model/AppDocument.php");
    require_once ('classes/model/AppFolder.php');
    require_once ('classes/model/InputDocument.php');

    $oInputDocument = new InputDocument();
    if ($_GET['UID'] != -1) {
        $aID = $oInputDocument->load($_GET['UID']);
    } else {
        $oFolder = new AppFolder();
        $folderStructure = $oFolder->getFolderStructure(isset($_GET['folderId']) ? $_GET['folderId'] : "/");
        $aID = array('INP_DOC_DESTINATION_PATH' => $folderStructure['PATH']);
    }


    $oAppDocument = new AppDocument();


    //Get the Custom Folder ID (create if necessary)
    $oFolder = new AppFolder();
    if ($_GET['UID'] != -1) {
        //krumo("jhl");
        $folderId = $oFolder->createFromPath($aID['INP_DOC_DESTINATION_PATH'], $appId);
        //Tags
        $fileTags = $oFolder->parseTags($aID['INP_DOC_TAGS'], $appId);
    } else {
        $folderId = isset($_GET['folderId']) ? $_GET['folderId'] : "/";
        $fileTags = "EXTERNAL";
    }
    switch ($actionType) {
        case "R": //replace
            $aFields = array('APP_DOC_UID' => $appDocUid,
                'APP_UID' => $appId,
                'DOC_VERSION' => $docVersion,
                'DEL_INDEX' => 1,
                'USR_UID' => $_SESSION['USER_LOGGED'],
                'DOC_UID' => $docUid,
                'APP_DOC_TYPE' => $_POST['form']['APP_DOC_TYPE'],
                'APP_DOC_CREATE_DATE' => date('Y-m-d H:i:s'),
                'APP_DOC_COMMENT' => isset($_POST['form']['APP_DOC_COMMENT']) ? $_POST['form']['APP_DOC_COMMENT'] : '',
                'APP_DOC_TITLE' => '',
                'APP_DOC_FILENAME' => isset($_FILES['form']['name']['APP_DOC_FILENAME']) ? $_FILES['form']['name']['APP_DOC_FILENAME'] : '',
                'FOLDER_UID' => $folderId,
                'APP_DOC_TAGS' => $fileTags);


            $oAppDocument->update($aFields);
            break;
        case "NV": //New Version

            $aFields = array('APP_DOC_UID' => $appDocUid,
                'APP_UID' => $appId,
                'DEL_INDEX' => 1,
                'USR_UID' => $_SESSION['USER_LOGGED'],
                'DOC_UID' => $docUid,
                'APP_DOC_TYPE' => $_POST['form']['APP_DOC_TYPE'],
                'APP_DOC_CREATE_DATE' => date('Y-m-d H:i:s'),
                'APP_DOC_COMMENT' => isset($_POST['form']['APP_DOC_COMMENT']) ? $_POST['form']['APP_DOC_COMMENT'] : '',
                'APP_DOC_TITLE' => '',
                'APP_DOC_FILENAME' => isset($_FILES['form']['name']['APP_DOC_FILENAME']) ? $_FILES['form']['name']['APP_DOC_FILENAME'] : '',
                'FOLDER_UID' => $folderId,
                'APP_DOC_TAGS' => $fileTags);

            $oAppDocument->create($aFields);
            break;
        default: //New
            $aFields = array('APP_UID' => $appId,
                'DEL_INDEX' => isset($_SESSION['INDEX']) ? $_SESSION['INDEX'] : 1,
                'USR_UID' => $_SESSION['USER_LOGGED'],
                'DOC_UID' => $docUid,
                'APP_DOC_TYPE' => $_POST['form']['APP_DOC_TYPE'],
                'APP_DOC_CREATE_DATE' => date('Y-m-d H:i:s'),
                'APP_DOC_COMMENT' => isset($_POST['form']['APP_DOC_COMMENT']) ? $_POST['form']['APP_DOC_COMMENT'] : '',
                'APP_DOC_TITLE' => '',
                'APP_DOC_FILENAME' => isset($_FILES['form']['name']['APP_DOC_FILENAME']) ? $_FILES['form']['name']['APP_DOC_FILENAME'] : '',
                'FOLDER_UID' => $folderId,
                'APP_DOC_TAGS' => $fileTags);

            $oAppDocument->create($aFields);
            break;
    }

    $sAppDocUid = $oAppDocument->getAppDocUid();
    $iDocVersion = $oAppDocument->getDocVersion();
    $info = pathinfo($oAppDocument->getAppDocFilename());
    $ext = (isset($info['extension']) ? $info['extension'] : '');

    //save the file
    if (!empty($_FILES['form'])) {
        if ($_FILES['form']['error']['APP_DOC_FILENAME'] == 0) {
            $sPathName = PATH_DOCUMENT . G::getPathFromUID($appId) . PATH_SEP;
            $sFileName = $sAppDocUid . "_" . $iDocVersion . '.' . $ext;
            G::uploadFile($_FILES['form']['tmp_name']['APP_DOC_FILENAME'], $sPathName, $sFileName);

            //Plugin Hook PM_UPLOAD_DOCUMENT for upload document
            $oPluginRegistry = & PMPluginRegistry::getSingleton();
            if ($oPluginRegistry->existsTrigger(PM_UPLOAD_DOCUMENT) && class_exists('uploadDocumentData')) {

                $oData['APP_UID'] = $appId;
                $documentData = new uploadDocumentData(
                                $appId,
                                $_SESSION['USER_LOGGED'],
                                $sPathName . $sFileName,
                                $aFields['APP_DOC_FILENAME'],
                                $sAppDocUid
                );

                $oPluginRegistry->executeTriggers(PM_UPLOAD_DOCUMENT, $documentData);
                unlink($sPathName . $sFileName);
            }
            //end plugin
        }
    }

    G::header('location: appFolderList');
    die;
} catch (Exception $e) {
    /* Render Error page */
    $aMessage['MESSAGE'] = $e->getMessage();
    $G_PUBLISH = new Publisher;
    $G_PUBLISH->AddContent('xmlform', 'xmlform', 'login/showMessage', '', $aMessage);
    G::RenderPage('publish');
}
 