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
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 * For more information, contact Colosa Inc, 2566 Le Jeune Rd.,
 * Coral Gables, FL, 33134, USA, or email info@colosa.com.
 */
//try {


//First review if there is no error with the uploaded document
if ((isset( $_FILES['form'] )) && ($_FILES['form']['error']['APP_DOC_FILENAME'] != 0)) {
    $code = $_FILES['form']['error']['APP_DOC_FILENAME'];
    switch ($code) {
        case UPLOAD_ERR_INI_SIZE:
            $message = G::LoadTranslation( 'ID_UPLOAD_ERR_INI_SIZE' );
            break;
        case UPLOAD_ERR_FORM_SIZE:
            $message = G::LoadTranslation( 'ID_UPLOAD_ERR_FORM_SIZE' );
            break;
        case UPLOAD_ERR_PARTIAL:
            $message = G::LoadTranslation( 'ID_UPLOAD_ERR_PARTIAL' );
            break;
        case UPLOAD_ERR_NO_FILE:
            $message = G::LoadTranslation( 'ID_UPLOAD_ERR_NO_FILE' );
            break;
        case UPLOAD_ERR_NO_TMP_DIR:
            $message = G::LoadTranslation( 'ID_UPLOAD_ERR_NO_TMP_DIR' );
            break;
        case UPLOAD_ERR_CANT_WRITE:
            $message = G::LoadTranslation( 'ID_UPLOAD_ERR_CANT_WRITE' );
            break;
        case UPLOAD_ERR_EXTENSION:
            $message = G::LoadTranslation( 'ID_UPLOAD_ERR_EXTENSION' );
            break;
        default:
            $message = G::LoadTranslation( 'ID_UPLOAD_ERR_UNKNOWN' );
            break;
    }
    G::SendMessageText( $message, "ERROR" );
    $backUrlObj = explode( "sys" . SYS_SYS, $_SERVER['HTTP_REFERER'] );
    G::header( "location: " . "/sys" . SYS_SYS . $backUrlObj[1] );
    die();
}

$inputDocumentUid = $_GET["UID"]; //$_POST["form"]["DOC_UID"]
$appDocUid = (isset($_POST["form"]["APP_DOC_UID"]))? $_POST["form"]["APP_DOC_UID"] : "";
$docVersion = (isset($_POST["form"]["docVersion"]))? intval($_POST["form"]["docVersion"]) : "";
$appDocType = (isset($_POST["form"]["APP_DOC_TYPE"]))? $_POST["form"]["APP_DOC_TYPE"] : "";
$appDocComment = (isset($_POST["form"]["APP_DOC_COMMENT"]))? $_POST["form"]["APP_DOC_COMMENT"] : "";
$actionType = (isset($_POST["form"]["actionType"]))? $_POST["form"]["actionType"] : "";

$case = new Cases();
$case->thisIsTheCurrentUser($_SESSION["APPLICATION"], $_SESSION["INDEX"], $_SESSION["USER_LOGGED"], "REDIRECT", "casesListExtJs");

//Load the fields
$arrayField = $case->loadCase($_SESSION["APPLICATION"]);
$arrayField["APP_DATA"] = array_merge($arrayField["APP_DATA"], G::getSystemConstants());

//Triggers
$arrayTrigger = $case->loadTriggers($_SESSION["TASK"], "INPUT_DOCUMENT", $inputDocumentUid, "AFTER");

//Trigger debug routines
//Cleaning debug variables
$_SESSION["TRIGGER_DEBUG"]["ERRORS"] = array();
$_SESSION["TRIGGER_DEBUG"]["DATA"] = array();
$_SESSION["TRIGGER_DEBUG"]["TRIGGERS_NAMES"] = array();
$_SESSION["TRIGGER_DEBUG"]["TRIGGERS_VALUES"] = array();
$_SESSION['TRIGGER_DEBUG']['TRIGGERS_EXECUTION_TIME'] = [];

$_SESSION["TRIGGER_DEBUG"]["NUM_TRIGGERS"] = count($arrayTrigger);
$_SESSION["TRIGGER_DEBUG"]["TIME"] = "AFTER";

if ($_SESSION["TRIGGER_DEBUG"]["NUM_TRIGGERS"] > 0) {
    $_SESSION["TRIGGER_DEBUG"]["TRIGGERS_NAMES"] = array_column($arrayTrigger, 'TRI_TITLE');
    $_SESSION["TRIGGER_DEBUG"]["TRIGGERS_VALUES"] = $arrayTrigger;
}

//***Validating the file allowed extensions***
$oInputDocument = new InputDocument();
$InpDocData = $oInputDocument->load( $inputDocumentUid );

if(isset($_FILES["form"]["name"]["APP_DOC_FILENAME"]) && isset($_FILES["form"]["tmp_name"]["APP_DOC_FILENAME"])){
    $res = G::verifyInputDocExtension($InpDocData['INP_DOC_TYPE_FILE'], $_FILES["form"]["name"]["APP_DOC_FILENAME"], $_FILES["form"]["tmp_name"]["APP_DOC_FILENAME"]);
}else{
    $res = new stdclass();
    $res->status = false;
    $res->message = G::LoadTranslation('ID_UPLOAD_ERR_INI_SIZE' );
}
if($res->status == 0){
	$message = $res->message;
	G::SendMessageText( $message, "ERROR" );
	$backUrlObj = explode( "sys" . SYS_SYS, $_SERVER['HTTP_REFERER'] );
	G::header( "location: " . "/sys" . SYS_SYS . $backUrlObj[1] );
	die();
}

//Add Input Document
if (isset($_FILES) && isset($_FILES["form"]) && count($_FILES["form"]) > 0) {
    try {
        $appDocUid = $case->addInputDocument(
            $inputDocumentUid,
            $appDocUid,
            $docVersion,
            $appDocType,
            $appDocComment,
            $actionType,
            $_SESSION["APPLICATION"],
            $_SESSION["INDEX"],
            $_SESSION["TASK"],
            $_SESSION["USER_LOGGED"],
            "xmlform",
            $_FILES["form"]["name"]["APP_DOC_FILENAME"],
            $_FILES["form"]["error"]["APP_DOC_FILENAME"],
            $_FILES["form"]["tmp_name"]["APP_DOC_FILENAME"],
            $_FILES["form"]["size"]["APP_DOC_FILENAME"]
        );
    } catch (Exception $e) {
        G::SendMessageText($e->getMessage(), "ERROR");

        $arrayAux = explode("sys" . SYS_SYS, $_SERVER["HTTP_REFERER"]);
        G::header("location: /sys" . SYS_SYS . $arrayAux[1]);
        exit(0);
    }
}

if ($_SESSION["TRIGGER_DEBUG"]["NUM_TRIGGERS"] > 0) {
    //Trigger - Execute after - Start
    $arrayField["APP_DATA"] = $case->executeTriggers(
        $_SESSION["TASK"],
        "INPUT_DOCUMENT",
        $inputDocumentUid,
        "AFTER",
        $arrayField["APP_DATA"]
    );
    //Trigger - Execute after - End
}

//Save data
$arrayData = array();
$arrayData["APP_NUMBER"] = $arrayField["APP_NUMBER"];
//$arrayData["APP_PROC_STATUS"] = $arrayField["APP_PROC_STATUS"];
$arrayData["APP_DATA"]  = $arrayField["APP_DATA"];
$arrayData["DEL_INDEX"] = $_SESSION["INDEX"];
$arrayData["TAS_UID"]   = $_SESSION["TASK"];
$arrayData["PRO_UID"]   = $_SESSION["PROCESS"];
$arrayData["USER_UID"]  = $_SESSION["USER_LOGGED"];
$arrayData["CURRENT_DYNAFORM"] = $inputDocumentUid;
$arrayData["OBJECT_TYPE"]      = "INPUT_DOCUMENT";

$case->updateCase($_SESSION["APPLICATION"], $arrayData);

//go to the next step
//if (!isset($_POST['form']['MORE'])) {
if (false) {
    $aNextStep = $case->getNextStep($_SESSION["PROCESS"], $_SESSION["APPLICATION"], $_SESSION["INDEX"], $_SESSION["STEP_POSITION"]);
    $_SESSION['STEP_POSITION'] = $aNextStep['POSITION'];

    if ($_SESSION['TRIGGER_DEBUG']['ISSET']) {
        $_SESSION['TRIGGER_DEBUG']['BREAKPAGE'] = $aNextStep['PAGE'];
        G::header( 'location: ' . $aNextStep['PAGE'] . '&breakpoint=triggerdebug' );
        die();
    }

    G::header( 'location: ' . $aNextStep['PAGE'] );
    die();
} else {
    if (isset( $_SERVER['HTTP_REFERER'] )) {
        if ($_SERVER['HTTP_REFERER'] != '') {

            if ($_SESSION['TRIGGER_DEBUG']['ISSET']) {
                $_SESSION['TRIGGER_DEBUG']['BREAKPAGE'] = $_SERVER['HTTP_REFERER'];
                G::header( 'location: ' . $_SERVER['HTTP_REFERER'] . '&breakpoint=triggerdebug' );
                die();
            }

            G::header( 'location: ' . $_SERVER['HTTP_REFERER'] );
            die();
        } else {
            $aNextStep = $case->getNextStep($_SESSION["PROCESS"], $_SESSION["APPLICATION"], $_SESSION["INDEX"], $_SESSION["STEP_POSITION"] - 1);
            $_SESSION['STEP_POSITION'] = $aNextStep['POSITION'];

            if ($_SESSION['TRIGGER_DEBUG']['ISSET']) {
                $_SESSION['TRIGGER_DEBUG']['BREAKPAGE'] = $aNextStep['PAGE'];
                G::header( 'location: ' . $aNextStep['PAGE'] . '&breakpoint=triggerdebug' );
                die();
            }

            G::header( 'location: ' . $aNextStep['PAGE'] );
            die();
        }
    } else {
        $aNextStep = $case->getNextStep($_SESSION["PROCESS"], $_SESSION["APPLICATION"], $_SESSION["INDEX"], $_SESSION["STEP_POSITION"] - 1);
        $_SESSION['STEP_POSITION'] = $aNextStep['POSITION'];

        if ($_SESSION['TRIGGER_DEBUG']['ISSET']) {
            $_SESSION['TRIGGER_DEBUG']['BREAKPAGE'] = $aNextStep['PAGE'];
            G::header( 'location: ' . $aNextStep['PAGE'] . '&breakpoint=triggerdebug' );
            die();
        }

        G::header( 'location: ' . $aNextStep['PAGE'] );
        die();
    }
}
$_SESSION['BREAKSTEP']['NEXT_STEP'] = $aNextStep;
/*
  } catch ( Exception $e ) {

      $aMessage['MESSAGE'] = $e->getMessage();
      $G_PUBLISH          = new Publisher;
      $G_PUBLISH->AddContent('xmlform', 'xmlform', 'login/showMessage', '', $aMessage );
      G::RenderPage( 'publish' );
  }*/
