<?php
/**
 * processes_ImportFile.php
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
ini_set( 'max_execution_time', '0' );

function reservedWordsSqlValidate ($data)
{
    $arrayAux = array ();
    $reservedWordsSql = G::reservedWordsSql();

    foreach ($data->reportTables as $rptIndex => $rptValue) {
        if (in_array( strtoupper( $rptValue["REP_TAB_NAME"] ), $reservedWordsSql )) {
            $arrayAux[] = $rptValue["REP_TAB_NAME"];
        }
    }

    if (count( $arrayAux ) > 0) {
        throw (new Exception( G::LoadTranslation( "ID_PMTABLE_INVALID_NAME", array (implode( ", ", $arrayAux )
        ) ) ));
    }

    $arrayAux = array ();

    foreach ($data->reportTablesVars as $rptIndex => $rptValue) {
        if (in_array( strtoupper( $rptValue["REP_VAR_NAME"] ), $reservedWordsSql )) {
            $arrayAux[] = $rptValue["REP_VAR_NAME"];
        }
    }

    if (count( $arrayAux ) > 0) {
        throw (new Exception( G::LoadTranslation( "ID_PMTABLE_INVALID_FIELD_NAME", array (implode( ", ", $arrayAux )
        ) ) ));
    }
}

$action = isset( $_REQUEST['ajaxAction'] ) ? $_REQUEST['ajaxAction'] : null;

$result = new stdClass();
$result->success = true;
$result->catchMessage = "";

if ($action == "uploadFileNewProcess") {
    try {
        //type of file: only pm
        $processFileType = $_REQUEST["processFileType"];
        $oProcess = new stdClass();
        $oData = new stdClass();

        $isCorrectTypeFile = 1;

        if (isset( $_FILES['form']['type']['PROCESS_FILENAME'] )) {
            $allowedExtensions = array ($processFileType
            );
            $allowedExtensions = array ('pm');
            if (! in_array( end( explode( ".", $_FILES['form']['name']['PROCESS_FILENAME'] ) ), $allowedExtensions )) {
                throw new Exception( G::LoadTranslation( "ID_FILE_UPLOAD_INCORRECT_EXTENSION" ) );
            }
        }
        if ($processFileType != "pm") {
            throw new Exception( G::LoadTranslation( "ID_ERROR_UPLOAD_FILE_CONTACT_ADMINISTRATOR" ) );
        }

        if ($processFileType == "pm") {
            G::LoadClass( 'processes' );
            $oProcess = new Processes();
        }

        $result->success = true;
        $result->ExistProcessInDatabase = ""; //"" -Default
        //0 -Dont exist process
        //1 -exist process
        $result->ExistGroupsInDatabase = ""; //"" -Default
        //0 -Dont exist process
        //1 -exist process
        $optionGroupExistInDatabase = isset( $_REQUEST["optionGroupExistInDatabase"] ) ? $_REQUEST["optionGroupExistInDatabase"] : null;

        //!Upload file
        if (! is_null( $optionGroupExistInDatabase )) {
            $filename = $_REQUEST["PRO_FILENAME"];
            $path = PATH_DOCUMENT . 'input' . PATH_SEP;
        } else {
            if ($_FILES['form']['error']['PROCESS_FILENAME'] == 0) {
                $filename = $_FILES['form']['name']['PROCESS_FILENAME'];
                $path = PATH_DOCUMENT . 'input' . PATH_SEP;
                $tempName = $_FILES['form']['tmp_name']['PROCESS_FILENAME'];
                //$action = "none";
                G::uploadFile( $tempName, $path, $filename );

            }
        }

        //importing a bpmn diagram, using external class to do it.
        if ($processFileType == "bpmn") {
            G::LoadClass( 'bpmnExport' );
            $bpmn = new bpmnExport();
            $bpmn->importBpmn( $path . $filename );
            die();
        }

        //if file is a .pm  file continues normally the importing
        if ($processFileType == "pm") {
            $oData = $oProcess->getProcessData( $path . $filename );
        }

        reservedWordsSqlValidate( $oData );

        //!Upload file
        $Fields['PRO_FILENAME'] = $filename;
        $Fields['IMPORT_OPTION'] = 2;

        $sProUid = $oData->process['PRO_UID'];

        $oData->process['PRO_UID_OLD'] = $sProUid;

        if ($oProcess->processExists( $sProUid )) {
            $result->ExistProcessInDatabase = 1;
        } else {
            $result->ExistProcessInDatabase = 0;
        }

        //!respect of the groups
        $result->ExistGroupsInDatabase = 1;
        $result->groupBeforeAccion = $action;
        if (! is_null( $optionGroupExistInDatabase )) {
            if ($optionGroupExistInDatabase == 1) {
                $oData->groupwfs = $oProcess->renameExistingGroups( $oData->groupwfs );
            } else if ($optionGroupExistInDatabase == 2) {
                $oBaseGroup = $oData->groupwfs;
                $oNewGroup = $oProcess->mergeExistingGroups( $oData->groupwfs );
                $oData->groupwfs = $oNewGroup;
                $oData->taskusers = $oProcess->mergeExistingUsers( $oBaseGroup, $oNewGroup, $oData->taskusers );
                $oData->objectPermissions = $oProcess->mergeExistingUsers( $oBaseGroup, $oNewGroup, $oData->objectPermissions );
            }
            $result->ExistGroupsInDatabase = 0;
        } else {
            if (! ($oProcess->checkExistingGroups( $oData->groupwfs ) > 0)) {
                $result->ExistGroupsInDatabase = 0;
            }
        }

        //replacing the processOwner user for the current user

        $oData->process['PRO_CREATE_USER'] = $_SESSION['USER_LOGGED'];

        //!respect of the groups

        if ($result->ExistProcessInDatabase == 0 && $result->ExistGroupsInDatabase == 0) {
            if ($processFileType == "pm") {
                $oProcess->createProcessFromData( $oData, $path . $filename );
            }
        }

        //!data ouput
        $result->sNewProUid = $sProUid;
        $result->proFileName = $Fields['PRO_FILENAME'];
    } catch (Exception $e) {
        $result->response = $e->getMessage();
        $result->catchMessage = $e->getMessage();
        $result->success = true;
    }
}

if ($action == "uploadFileNewProcessExist") {
    try {
        $option = $_REQUEST["IMPORT_OPTION"];
        $filename = $_REQUEST["PRO_FILENAME"];
        $processFileType = $_REQUEST["processFileType"];

        $result->ExistGroupsInDatabase = ""; //"" -Default
        //0 -Dont exist process
        //1 -exist process


        $optionGroupExistInDatabase = isset( $_REQUEST["optionGroupExistInDatabase"] ) ? $_REQUEST["optionGroupExistInDatabase"] : null;
        $sNewProUid = "";

        $oProcess = new stdClass();
        if ($processFileType != "pm") {
            throw new Exception( G::LoadTranslation( "ID_ERROR_UPLOAD_FILE_CONTACT_ADMINISTRATOR" ) );
        }

        //load the variables
        if ($processFileType == "pm") {
            G::LoadClass( 'processes' );
            $oProcess = new Processes();
        }

        $path = PATH_DOCUMENT . 'input' . PATH_SEP;

        if ($processFileType == "pm") {
            $oData = $oProcess->getProcessData( $path . $filename );
        }

        reservedWordsSqlValidate( $oData );

        $Fields['PRO_FILENAME'] = $filename;
        $sProUid = $oData->process['PRO_UID'];

        $oData->process['PRO_UID_OLD'] = $sProUid;

        $result->ExistGroupsInDatabase = 1;
        if (! is_null( $optionGroupExistInDatabase )) {
            if ($optionGroupExistInDatabase == 1) {
                $oData->groupwfs = $oProcess->renameExistingGroups( $oData->groupwfs );
            } else if ($optionGroupExistInDatabase == 2) {
                $oBaseGroup = $oData->groupwfs;
                $oNewGroup = $oProcess->mergeExistingGroups( $oData->groupwfs );
                $oData->groupwfs = $oNewGroup;
                $oData->taskusers = $oProcess->mergeExistingUsers( $oBaseGroup, $oNewGroup, $oData->taskusers );
            }
            $result->ExistGroupsInDatabase = 0;
        } else {
            if (! ($oProcess->checkExistingGroups( $oData->groupwfs ) > 0)) {
                $result->ExistGroupsInDatabase = 0;
            }
        }

        if ($result->ExistGroupsInDatabase == 0) {
            //Update the current Process, overwriting all tasks and steps
            if ($option == 1) {
                $oProcess->updateProcessFromData( $oData, $path . $filename );
                if (file_exists( PATH_OUTTRUNK . 'compiled' . PATH_SEP . 'xmlform' . PATH_SEP . $sProUid )) {
                    $oDirectory = dir( PATH_OUTTRUNK . 'compiled' . PATH_SEP . 'xmlform' . PATH_SEP . $sProUid );
                    while ($sObjectName = $oDirectory->read()) {
                        if (($sObjectName != '.') && ($sObjectName != '..')) {
                            unlink( PATH_OUTTRUNK . 'compiled' . PATH_SEP . 'xmlform' . PATH_SEP . $sProUid . PATH_SEP . $sObjectName );
                        }
                    }
                    $oDirectory->close();
                }
                $sNewProUid = $sProUid;
            }

            //Disable current Process and create a new version of the Process
            if ($option == 2) {
                $oProcess->disablePreviousProcesses( $sProUid );
                $sNewProUid = $oProcess->getUnusedProcessGUID();
                $oProcess->setProcessGuid( $oData, $sNewProUid );
                $oProcess->setProcessParent( $oData, $sProUid );
                $oData->process['PRO_TITLE'] = "New - " . $oData->process['PRO_TITLE'] . ' - ' . date( 'M d, H:i' );
                $oProcess->renewAll( $oData );

                if ($processFileType == "pm") {
                    $oProcess->createProcessFromData( $oData, $path . $filename );
                }
            }

            //Create a completely new Process without change the current Process
            if ($option == 3) {
                //krumo ($oData); die;
                $sNewProUid = $oProcess->getUnusedProcessGUID();
                $oProcess->setProcessGuid( $oData, $sNewProUid );
                $oData->process['PRO_TITLE'] = G::LoadTranslation('ID_COPY_OF'). ' - ' . $oData->process['PRO_TITLE'] . ' - ' . date( 'M d, H:i' );
                $oProcess->renewAll( $oData );

                if ($processFileType == "pm") {
                    $oProcess->createProcessFromData( $oData, $path . $filename );
                }
            }
        }

        //!data ouput
        $result->fileName = $filename;
        $result->importOption = $option;
        $result->sNewProUid = $sNewProUid;
        $result->success = true;
        $result->ExistGroupsInDatabase = $result->ExistGroupsInDatabase;
        $result->groupBeforeAccion = $action;
        //!data ouput
    } catch (Exception $e) {
        $result->response = $e->getMessage();
        $result->success = true;
    }
}

echo G::json_encode( $result );
exit();

