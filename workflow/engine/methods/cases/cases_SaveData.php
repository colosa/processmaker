<?php
/**
 * cases_SaveData.php
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
//validate the data post
if (!isset($_SESSION['USER_LOGGED'])) {
    if(!strpos($_SERVER['REQUEST_URI'], 'gmail')) {
        $responseObject = new stdclass();
        $responseObject->error = G::LoadTranslation('ID_LOGIN_AGAIN');
        $responseObject->success = true;
        $responseObject->lostSession = true;
        print G::json_encode( $responseObject );
        die();
    } else {
        G::SendTemporalMessage('ID_LOGIN_AGAIN', 'warning', 'labels');
        die('<script type="text/javascript">
                    try
                      {
    					var olink = document.location.href;
    					if(olink.search("gmail") != -1){
    						var data = olink.split("?");
                            var odata = data[1].split("&");
                            var appUid = odata[1].split("=");
    						var proUid = odata[0].split("=");

    						var dataToSend = {
                    			"action": "credentials",
                    			"operation": "refreshPmSession",
                    			"type": "processCall",
    							"funParams": [
                                               appUid[1],
                                               proUid[1]
                                ],
                    			"expectReturn": false
                			};
    						var x = parent.postMessage(JSON.stringify(dataToSend), "*");
    						if (x == undefined){
    							x = parent.parent.postMessage(JSON.stringify(dataToSend), "*");
    						}
						}else{
    						prnt = parent.parent;
	                        top.location = top.location;
    					}
                      }
                      catch (err)
                      {
                        parent.location = parent.location;
                      }
                    </script>');
    }
}

/**
 * If you can, you may want to set post_max_size to a low value (say 1M) to make
 * testing easier. First test to see how your script behaves. Try uploading a file
 * that is larger than post_max_size. If you do you will get a message like this
 * in your error log:
 *
 * [09-Jun-2010 19:28:01] PHP Warning:  POST Content-Length of 30980857 bytes exceeds
 * the limit of 2097152 bytes in Unknown on line 0
 *
 * This makes the script is not completed.
 *
 * Solving the problem:
 * The PHP documentation http://php.net/manual/en/ini.core.php#ini.post-max-size
 * provides a hack to solve this problem:
 *
 * If the size of post data is greater than post_max_size, the $_POST and $_FILES
 * superglobals are empty.
 */
if ($_SERVER['REQUEST_METHOD'] == 'POST' && empty($_POST) && empty($_FILES) && $_SERVER['CONTENT_LENGTH'] > 0) {
    $aMessage = array();
    $aMessage['MESSAGE'] = G::loadTranslation('ID_UPLOAD_ERR_INI_SIZE');
    $G_PUBLISH = new Publisher();
    $G_PUBLISH->AddContent('xmlform', 'xmlform', 'login/showMessage', '', $aMessage);
    G::RenderPage('publish', 'blank');
    die();
}

try {
    if ($_GET['APP_UID'] !== $_SESSION['APPLICATION']) {
        throw new Exception( G::LoadTranslation( 'ID_INVALID_APPLICATION_ID_MSG', array ('<a href=\'' . $_SERVER['HTTP_REFERER'] . '\'>{1}</a>',G::LoadTranslation( 'ID_REOPEN' ) ) ) );
    }

    $arrayVariableDocumentToDelete = [];

    //If no variables are submitted and the $_POST variable is empty
    if (!isset($_POST['form'])) {
        $_POST['form'] = array();
    }

    if (array_key_exists('__VARIABLE_DOCUMENT_DELETE__', $_POST['form'])) {
        if (is_array($_POST['form']['__VARIABLE_DOCUMENT_DELETE__']) && !empty($_POST['form']['__VARIABLE_DOCUMENT_DELETE__'])) {
            $arrayVariableDocumentToDelete = $_POST['form']['__VARIABLE_DOCUMENT_DELETE__'];
        }

        unset($_POST['form']['__VARIABLE_DOCUMENT_DELETE__']);
    }

    /*
     * PMDynaform
     * DYN_VERSION is 1: classic Dynaform,
     * DYN_VERSION is 2: responsive form, Pmdynaform.
     */
    $dynaForm = DynaformPeer::retrieveByPK($_GET["UID"]);

    $swpmdynaform = !is_null($dynaForm) && $dynaForm->getDynVersion() == 2;

    if ($swpmdynaform) {
        $pmdynaform = $_POST["form"];
    }

    $oForm = new Form( $_SESSION["PROCESS"] . "/" . $_GET["UID"], PATH_DYNAFORM );
    $oForm->validatePost();

    //Load the variables
    $oCase = new Cases();
    $oCase->thisIsTheCurrentUser( $_SESSION["APPLICATION"], $_SESSION["INDEX"], $_SESSION["USER_LOGGED"], "REDIRECT", "casesListExtJs" );
    $Fields = $oCase->loadCase( $_SESSION["APPLICATION"] );

    if ($swpmdynaform) {
        $dataFields = $Fields["APP_DATA"];
        $dataFields["CURRENT_DYNAFORM"] = $_GET['UID'];

        $oPmDynaform = new PmDynaform($dataFields);
        $pmdynaform = $oPmDynaform->validatePost($pmdynaform);

        $Fields["APP_DATA"] = array_merge( $Fields["APP_DATA"], $pmdynaform );
    }

    $Fields["APP_DATA"] = array_merge( $Fields["APP_DATA"], G::getSystemConstants() );
    $Fields["APP_DATA"] = array_merge( $Fields["APP_DATA"], $_POST["form"] );

    #here we must verify if is a debug session
    $trigger_debug_session = isset($_SESSION['TRIGGER_DEBUG']['ISSET']) ? $_SESSION['TRIGGER_DEBUG']['ISSET'] : null; #here we must verify if is a debugg session

    #trigger debug routines...

    //cleaning debug variables
    $_SESSION['TRIGGER_DEBUG']['ERRORS'] = Array ();
    $_SESSION['TRIGGER_DEBUG']['DATA'] = Array ();
    $_SESSION['TRIGGER_DEBUG']['TRIGGERS_NAMES'] = Array ();
    $_SESSION['TRIGGER_DEBUG']['TRIGGERS_VALUES'] = Array ();
    $_SESSION['TRIGGER_DEBUG']['TRIGGERS_EXECUTION_TIME'] = [];

    $triggers = $oCase->loadTriggers( $_SESSION['TASK'], 'DYNAFORM', $_GET['UID'], 'AFTER' );

    $_SESSION['TRIGGER_DEBUG']['NUM_TRIGGERS'] = count( $triggers );
    $_SESSION['TRIGGER_DEBUG']['TIME'] = G::toUpper(G::loadTranslation('ID_AFTER'));
    if ($_SESSION['TRIGGER_DEBUG']['NUM_TRIGGERS'] != 0) {
        $_SESSION['TRIGGER_DEBUG']['TRIGGERS_NAMES'] = array_column($triggers, 'TRI_TITLE');
        $_SESSION['TRIGGER_DEBUG']['TRIGGERS_VALUES'] = $triggers;
        $oProcess = new Process();
        $oProcessFieds = $oProcess->Load( $_SESSION['PROCESS'] );

        //trigger debug routines...
        if (isset( $oProcessFieds['PRO_DEBUG'] ) && $oProcessFieds['PRO_DEBUG']) {
            $trigger_debug_session = true;
        }
    }

    if ($_SESSION['TRIGGER_DEBUG']['NUM_TRIGGERS'] != 0) {
        //Execute after triggers - Start
        $Fields['APP_DATA'] = $oCase->ExecuteTriggers( $_SESSION['TASK'], 'DYNAFORM', $_GET['UID'], 'AFTER', $Fields['APP_DATA'] );
        //Execute after triggers - End

        $_SESSION['TRIGGER_DEBUG']['TRIGGERS_EXECUTION_TIME'] = $oCase->arrayTriggerExecutionTime;
    }

    //save data in PM Tables if necessary
    $newValues = array ();
    foreach ($_POST['form'] as $sField => $sAux) {
        if (isset( $oForm->fields[$sField]->pmconnection ) && isset( $oForm->fields[$sField]->pmfield )) {
            if (($oForm->fields[$sField]->pmconnection != '') && ($oForm->fields[$sField]->pmfield != '')) {
                if (isset( $oForm->fields[$oForm->fields[$sField]->pmconnection] )) {
                    require_once PATH_CORE . 'classes' . PATH_SEP . 'model' . PATH_SEP . 'AdditionalTables.php';
                    $oAdditionalTables = new AdditionalTables();
                    try {
                        $aData = $oAdditionalTables->load( $oForm->fields[$oForm->fields[$sField]->pmconnection]->pmtable, true );
                    } catch (Exception $oError) {
                        $aData = array ('FIELDS' => array ()
                        );
                    }
                    $aKeys = array ();
                    $aAux = explode( '|', $oForm->fields[$oForm->fields[$sField]->pmconnection]->keys );
                    $i = 0;
                    $aValues = array ();
                    if ($aData == "" || count($aData['FIELDS']) < 1) {
                        $message = G::LoadTranslation( 'ID_PMTABLE_NOT_FOUNDED_SAVED_DATA' );
                        G::SendMessageText( $message, "WARNING" );
                        $aRow = false;
                    } else {
                        foreach ($aData['FIELDS'] as $aField) {
                            if ($aField['FLD_KEY'] == '1') {
                                $aKeys[$aField['FLD_NAME']] = (isset( $aAux[$i] ) ? G::replaceDataField( $aAux[$i], $Fields['APP_DATA'] ) : '');
                                $i ++;
                            }
                            if ($aField['FLD_NAME'] == $oForm->fields[$sField]->pmfield) {
                                $aValues[$aField['FLD_NAME']] = $Fields['APP_DATA'][$sField];
                            } else {
                                $aValues[$aField['FLD_NAME']] = '';
                            }
                        }
                        try {
                            $aRow = $oAdditionalTables->getDataTable( $oForm->fields[$oForm->fields[$sField]->pmconnection]->pmtable, $aKeys );
                        } catch (Exception $oError) {
                            $aRow = false;
                        }
                    }

                    if ($aRow) {
                        foreach ($aValues as $sKey => $sValue) {
                            if ($sKey != $oForm->fields[$sField]->pmfield) {
                                $aValues[$sKey] = $aRow[$sKey];
                            }
                        }
                        try {
                            $oAdditionalTables->updateDataInTable( $oForm->fields[$oForm->fields[$sField]->pmconnection]->pmtable, $aValues );
                        } catch (Exception $oError) {
                            //Nothing
                        }
                    } else {
                        try {
                            // assembling the field list in order to save the data ina new record of a pm table
                            if (empty( $newValues )) {
                                $newValues = $aValues;
                            } else {
                                foreach ($aValues as $aValueKey => $aValueCont) {
                                    if (trim( $newValues[$aValueKey] ) == '') {
                                        $newValues[$aValueKey] = $aValueCont;
                                    }
                                }
                            }
                            //$oAdditionalTables->saveDataInTable ( $oForm->fields [$oForm->fields [$sField]->pmconnection]->pmtable, $aValues );
                        } catch (Exception $oError) {
                            //Nothing
                        }
                    }
                }
            }
        }
    }

    //save data
    $aData = array ();
    $aData['APP_NUMBER'] = $Fields['APP_NUMBER'];
    //$aData['APP_PROC_STATUS'] = $Fields['APP_PROC_STATUS'];
    $aData['APP_DATA'] = $Fields['APP_DATA'];
    $aData['DEL_INDEX'] = $_SESSION['INDEX'];
    $aData['TAS_UID'] = $_SESSION['TASK'];
    $aData['CURRENT_DYNAFORM'] = $_GET['UID'];
    $aData['USER_UID'] = $_SESSION['USER_LOGGED'];
    //$aData['APP_STATUS'] = $Fields['APP_STATUS'];
    $aData['PRO_UID'] = $_SESSION['PROCESS'];

    if ($swpmdynaform) {
        $aData['APP_DATA'] = array_merge($pmdynaform,$aData['APP_DATA']);
        $_POST["DynaformRequiredFields"] = '[]';
    }

    $oCase->updateCase( $_SESSION['APPLICATION'], $aData );

    // saving the data ina pm table in case that is a new record
    if (! empty( $newValues )) {

        $id = key( $newValues );
        $newValues[$id] = $aData['APP_DATA'][$id];
        foreach ($aKeys as $key => $value) {
            if (!isset($newValues[$key]) || $newValues[$key] == '') {
                $G_PUBLISH = new Publisher();
                $aMessage = array ();
                $aMessage['MESSAGE'] = G::LoadTranslation('ID_FILL_PRIMARY_KEYS') . ' ('. $key . ') ';
                $G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'login/showMessage', '', $aMessage );
                G::RenderPage( 'publish', 'blank' );
                die();
            }
        }
        $idPmtable = isset($oForm->fields[$id]->pmconnection->pmtable) && $oForm->fields[$id]->pmconnection->pmtable != '' ? $oForm->fields[$id]->pmconnection->pmtable : $oForm->fields[$id]->owner->tree->children[0]->attributes['pmtable'];

        if (!($oAdditionalTables->updateDataInTable($idPmtable, $newValues ))) {
            //<--This is to know if it is a new registry on the PM Table
            $oAdditionalTables->saveDataInTable($idPmtable, $newValues );
        }
    }

    //Save files

    if (isset( $_FILES["form"]["name"] ) && count( $_FILES["form"]["name"] ) > 0) {
        $oInputDocument = new \ProcessMaker\BusinessModel\Cases\InputDocument();
        $oInputDocument->uploadFileCase($_FILES, $oCase, $aData, $_SESSION["USER_LOGGED"], $_SESSION["APPLICATION"], $_SESSION["INDEX"]);
    }

    //Delete MultipleFile
    if (!empty($arrayVariableDocumentToDelete)) {
        $case = new \ProcessMaker\BusinessModel\Cases();

        $case->deleteMultipleFile($_SESSION['APPLICATION'], $arrayVariableDocumentToDelete);
    }

    //Go to the next step
    $aNextStep = $oCase->getNextStep( $_SESSION['PROCESS'], $_SESSION['APPLICATION'], $_SESSION['INDEX'], $_SESSION['STEP_POSITION'] );
    if (isset( $_GET['_REFRESH_'] )) {
        G::header( 'location: ' . $_SERVER['HTTP_REFERER'] );
        die();
    }

    $_SESSION['STEP_POSITION'] = $aNextStep['POSITION'];
    $_SESSION['BREAKSTEP']['NEXT_STEP'] = $aNextStep['PAGE'];
    $debuggerAvailable = true;

    if (isset( $_SESSION['current_ux'] ) && $_SESSION['current_ux'] == 'SIMPLIFIED') {
        $debuggerAvailable = false;
    }

    if ($trigger_debug_session && $debuggerAvailable) {
        $_SESSION['TRIGGER_DEBUG']['BREAKPAGE'] = $aNextStep['PAGE'];
        $aNextStep['PAGE'] = $aNextStep['PAGE'] . '&breakpoint=triggerdebug';
    }

    $oForm->validatePost();
    //$oJSON = new Services_JSON();
    $_POST['__notValidateThisFields__'] = (isset( $_POST['__notValidateThisFields__'] ) && $_POST['__notValidateThisFields__'] != '') ? $_POST['__notValidateThisFields__'] : $_POST['DynaformRequiredFields'];
    if ($missing_req_values = $oForm->validateRequiredFields( $_POST['form'], Bootstrap::json_decode( stripslashes( $_POST['__notValidateThisFields__'] ) ) )) {
        $fieldsRequired = Bootstrap::json_decode(str_replace(array("%27", "%39"), array("\"", "'"), $_POST["DynaformRequiredFields"]));

        foreach ($fieldsRequired as $key1 => $value1) {
           foreach ($missing_req_values as $key2 => $value2) {
                if ($value1->name == $value2) {
                    $missing_req_values[$key2] = $value1->label;
                }
           }
        }

        /*hotfix notValidateThisFields */
        $validate = false;
        $string = serialize($missing_req_values);
        if(!is_array($_POST['__notValidateThisFields__'])) {
            $notValidateThisFields = explode("," ,$_POST['__notValidateThisFields__']);
        } else {
            $notValidateThisFields = $_POST['__notValidateThisFields__'];
        }

        foreach($notValidateThisFields as $val) {
            if(strpos($val,"]")) {
                $gridField = substr($val,strrpos($val,"["),strlen($val));
                $gridField = preg_replace("/[^a-zA-Z0-9_-]+/", "", $gridField);
                $pattern = "/".$gridField."/i";
            } else {
                $pattern = "/".$val."/i";
            }
            preg_match($pattern, $string, $matches, PREG_OFFSET_CAPTURE);
            if(sizeof($matches)) {
                $validate = true;
            }
        }

        if(!$validate && !sizeof($matches)) {
            $_POST['next_step'] = $aNextStep;
            $_POST['previous_step'] = $oCase->getPreviousStep( $_SESSION['PROCESS'], $_SESSION['APPLICATION'], $_SESSION['INDEX'], $_SESSION['STEP_POSITION'] );
            $_POST['req_val'] = $missing_req_values;
            global $G_PUBLISH;
            $G_PUBLISH = new Publisher();
            $G_PUBLISH->AddContent( 'view', 'cases/missRequiredFields' );
            G::RenderPage( 'publish', 'blank' );
            exit( 0 );
        }
        /*end hotfix notValidateThisFields */
    }

    G::header( 'location: ' . $aNextStep['PAGE'] );

} catch (Exception $e) {
    $G_PUBLISH = new Publisher();
    $aMessage = array ();
    $aMessage['MESSAGE'] = $e->getMessage();
    $G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'login/showMessage', '', $aMessage );
    G::RenderPage( 'publish', 'blank' );
    die();
}
