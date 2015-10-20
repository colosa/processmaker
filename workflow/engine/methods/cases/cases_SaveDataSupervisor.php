<?php
/**
 * cases_SaveData.php
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
//validate the data post

$dynaForm = DynaformPeer::retrieveByPK($_GET["UID"]);

$flagDynaFormNewVersion = !is_null($dynaForm) && $dynaForm->getDynVersion() == 2;

if ($flagDynaFormNewVersion) {
    $dataForm = $_POST["form"];
}

$oForm = new Form( $_SESSION['PROCESS'] . '/' . $_GET['UID'], PATH_DYNAFORM );
$oForm->validatePost();

/* Includes */
G::LoadClass( 'case' );

//load the variables
$oCase = new Cases();
$Fields = $oCase->loadCase( $_SESSION['APPLICATION'] );

if ($flagDynaFormNewVersion) {
    $Fields["APP_DATA"] = array_merge($Fields["APP_DATA"], $dataForm);
}

$Fields['APP_DATA'] = array_merge( $Fields['APP_DATA'], $_POST['form'] );

//save data
$aData = array ();
$aData['APP_NUMBER'] = $Fields['APP_NUMBER'];
$aData['APP_PROC_STATUS'] = $Fields['APP_PROC_STATUS'];
$aData['APP_DATA'] = $Fields['APP_DATA'];
$aData['DEL_INDEX'] = $_SESSION['INDEX'];
$aData['TAS_UID'] = $_SESSION['TASK'];
$aData['CURRENT_DYNAFORM'] = $_GET['UID'];
$aData['PRO_UID'] = $Fields['PRO_UID'];
$aData['USER_UID'] = $_SESSION['USER_LOGGED'];
$aData['APP_STATUS'] = $Fields['APP_STATUS'];

//$aData = $oCase->loadCase( $_SESSION['APPLICATION'] );
$oCase->updateCase( $_SESSION['APPLICATION'], $aData );
G::SendTemporalMessage( 'ID_SAVED_SUCCESSFULLY', 'info' );

//Save files
if (isset( $_FILES["form"]["name"] ) && count( $_FILES["form"]["name"] ) > 0) {
	$arrayField = array ();
	$arrayFileName = array ();
	$arrayFileTmpName = array ();
	$arrayFileError = array ();
	$i = 0;

	foreach ($_FILES["form"]["name"] as $fieldIndex => $fieldValue) {
		if (is_array( $fieldValue )) {
			foreach ($fieldValue as $index => $value) {
				if (is_array( $value )) {
					foreach ($value as $grdFieldIndex => $grdFieldValue) {
						$arrayField[$i]["grdName"] = $fieldIndex;
						$arrayField[$i]["grdFieldName"] = $grdFieldIndex;
						$arrayField[$i]["index"] = $index;

						$arrayFileName[$i] = $_FILES["form"]["name"][$fieldIndex][$index][$grdFieldIndex];
						$arrayFileTmpName[$i] = $_FILES["form"]["tmp_name"][$fieldIndex][$index][$grdFieldIndex];
						$arrayFileError[$i] = $_FILES["form"]["error"][$fieldIndex][$index][$grdFieldIndex];
						$i = $i + 1;
					}
				}
			}
		} else {
			$arrayField[$i] = $fieldIndex;

			$arrayFileName[$i] = $_FILES["form"]["name"][$fieldIndex];
			$arrayFileTmpName[$i] = $_FILES["form"]["tmp_name"][$fieldIndex];
			$arrayFileError[$i] = $_FILES["form"]["error"][$fieldIndex];
			$i = $i + 1;
		}
	}
	if (count( $arrayField ) > 0) {
		for ($i = 0; $i <= count( $arrayField ) - 1; $i ++) {
			if ($arrayFileError[$i] == 0) {
				$indocUid = null;
				$fieldName = null;
				$fileSizeByField = 0;

				if (is_array( $arrayField[$i] )) {
					if (isset( $_POST["INPUTS"][$arrayField[$i]["grdName"]][$arrayField[$i]["grdFieldName"]] ) && ! empty( $_POST["INPUTS"][$arrayField[$i]["grdName"]][$arrayField[$i]["grdFieldName"]] )) {
						$indocUid = $_POST["INPUTS"][$arrayField[$i]["grdName"]][$arrayField[$i]["grdFieldName"]];
					}

					$fieldName = $arrayField[$i]["grdName"] . "_" . $arrayField[$i]["index"] . "_" . $arrayField[$i]["grdFieldName"];

					if (isset($_FILES["form"]["size"][$arrayField[$i]["grdName"]][$arrayField[$i]["index"]][$arrayField[$i]["grdFieldName"]])) {
						$fileSizeByField = $_FILES["form"]["size"][$arrayField[$i]["grdName"]][$arrayField[$i]["index"]][$arrayField[$i]["grdFieldName"]];
					}
				} else {
					if (isset( $_POST["INPUTS"][$arrayField[$i]] ) && ! empty( $_POST["INPUTS"][$arrayField[$i]] )) {
						$indocUid = $_POST["INPUTS"][$arrayField[$i]];
					}

					$fieldName = $arrayField[$i];

					if (isset($_FILES["form"]["size"][$fieldName])) {
						$fileSizeByField = $_FILES["form"]["size"][$fieldName];
					}
				}

				if ($indocUid != null) {
					//require_once ("classes/model/AppFolder.php");
					//require_once ("classes/model/InputDocument.php");

					$oInputDocument = new InputDocument();
					$aID = $oInputDocument->load( $indocUid );

					//Get the Custom Folder ID (create if necessary)
					$oFolder = new AppFolder();

					//***Validating the file allowed extensions***
					$res = G::verifyInputDocExtension($aID['INP_DOC_TYPE_FILE'], $arrayFileName[$i], $arrayFileTmpName[$i]);
					if($res->status == 0){
						$message = $res->message;
						G::SendMessageText( $message, "ERROR" );
						$backUrlObj = explode( "sys" . SYS_SYS, $_SERVER['HTTP_REFERER'] );
						G::header( "location: " . "/sys" . SYS_SYS . $backUrlObj[1] );
						die();
					}

					//--- Validate Filesize of $_FILE
					$inpDocMaxFilesize = $aID["INP_DOC_MAX_FILESIZE"];
					$inpDocMaxFilesizeUnit = $aID["INP_DOC_MAX_FILESIZE_UNIT"];

					$inpDocMaxFilesize = $inpDocMaxFilesize * (($inpDocMaxFilesizeUnit == "MB")? 1024 *1024 : 1024); //Bytes

					if ($inpDocMaxFilesize > 0 && $fileSizeByField > 0) {
						if ($fileSizeByField > $inpDocMaxFilesize) {
							G::SendMessageText(G::LoadTranslation("ID_SIZE_VERY_LARGE_PERMITTED"), "ERROR");
							$arrayAux1 = explode("sys" . SYS_SYS, $_SERVER["HTTP_REFERER"]);
							G::header("location: /sys" . SYS_SYS . $arrayAux1[1]);
							exit(0);
						}
					}

					$aFields = array ("APP_UID" => $_SESSION["APPLICATION"],"DEL_INDEX" => $_SESSION["INDEX"],"USR_UID" => $_SESSION["USER_LOGGED"],"DOC_UID" => $indocUid,"APP_DOC_TYPE" => "INPUT","APP_DOC_CREATE_DATE" => date( "Y-m-d H:i:s" ),"APP_DOC_COMMENT" => "","APP_DOC_TITLE" => "","APP_DOC_FILENAME" => $arrayFileName[$i],"FOLDER_UID" => $oFolder->createFromPath( $aID["INP_DOC_DESTINATION_PATH"] ),"APP_DOC_TAGS" => $oFolder->parseTags( $aID["INP_DOC_TAGS"] ),"APP_DOC_FIELDNAME" => $fieldName);
				} else {
					$aFields = array ("APP_UID" => $_SESSION["APPLICATION"],"DEL_INDEX" => $_SESSION["INDEX"],"USR_UID" => $_SESSION["USER_LOGGED"],"DOC_UID" => - 1,"APP_DOC_TYPE" => "ATTACHED","APP_DOC_CREATE_DATE" => date( "Y-m-d H:i:s" ),"APP_DOC_COMMENT" => "","APP_DOC_TITLE" => "","APP_DOC_FILENAME" => $arrayFileName[$i],"APP_DOC_FIELDNAME" => $fieldName);
				}

				$oAppDocument = new AppDocument();
				$oAppDocument->create( $aFields );

				$iDocVersion = $oAppDocument->getDocVersion();
				$sAppDocUid = $oAppDocument->getAppDocUid();
				$aInfo = pathinfo( $oAppDocument->getAppDocFilename() );
				$sExtension = ((isset( $aInfo["extension"] )) ? $aInfo["extension"] : "");
				$pathUID = G::getPathFromUID($_SESSION["APPLICATION"]);
				$sPathName = PATH_DOCUMENT . $pathUID . PATH_SEP;
				$sFileName = $sAppDocUid . "_" . $iDocVersion . "." . $sExtension;
				G::uploadFile( $arrayFileTmpName[$i], $sPathName, $sFileName );

				//Plugin Hook PM_UPLOAD_DOCUMENT for upload document
				$oPluginRegistry = &PMPluginRegistry::getSingleton();

				if ($oPluginRegistry->existsTrigger( PM_UPLOAD_DOCUMENT ) && class_exists( "uploadDocumentData" )) {
					$triggerDetail = $oPluginRegistry->getTriggerInfo( PM_UPLOAD_DOCUMENT );
					$documentData = new uploadDocumentData( $_SESSION["APPLICATION"], $_SESSION["USER_LOGGED"], $sPathName . $sFileName, $aFields["APP_DOC_FILENAME"], $sAppDocUid, $iDocVersion );
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
			}
		}
	}
}

//go to the next step
$aNextStep = $oCase->getNextSupervisorStep( $_SESSION['PROCESS'], $_SESSION['STEP_POSITION'] );
G::header( 'location: cases_StepToRevise?DYN_UID=' . $aNextStep['UID'] . '&APP_UID=' . $_SESSION['APPLICATION'] . '&DEL_INDEX=' . $_SESSION['INDEX'] );

