<?php

/**
 * processes_Ajax.php
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
try {
    /* global $RBAC;
      switch ($RBAC->userCanAccess('PM_FACTORY'))
      {
      case -2:
      G::SendTemporalMessage('ID_USER_HAVENT_RIGHTS_SYSTEM', 'error', 'labels');
      G::header('location: ../login/login');
      die;
      break;
      case -1:
      G::SendTemporalMessage('ID_USER_HAVENT_RIGHTS_PAGE', 'error', 'labels');
      G::header('location: ../login/login');
      die;
      break;
      } */
    //$oJSON = new Services_JSON();


    $filter = new InputFilter();
    $_GET = $filter->xssFilterHard($_GET);
    $_POST = $filter->xssFilterHard($_POST);
    $_REQUEST = $filter->xssFilterHard($_REQUEST);
    //$_SESSION = $filter->xssFilterHard($_SESSION); 

    if (isset($_REQUEST['data'])) {
        if($_REQUEST['action']=="addText"||$_REQUEST['action']=="updateText") {
            $oData = Bootstrap::json_decode($_REQUEST['data']);
            $oDataAux = G::json_decode($_REQUEST['data']);
            $oDataAux = (array)$oDataAux;
        } else {
            $oData = Bootstrap::json_decode(stripslashes($_REQUEST['data']));
            $oDataAux = G::json_decode(stripslashes($_REQUEST['data']));
            $oDataAux = (array)$oDataAux;
        }
        //$oData = $oJSON->decode( stripslashes( $_REQUEST['data'] ) );
        $sOutput = '';
        $sTask = '';

        if(array_key_exists('pro_uid', $oDataAux) || array_key_exists('uid', $oDataAux) || array_key_exists('PRO_UID', $oDataAux) || array_key_exists('UID', $oDataAux)) {
            if(array_key_exists('pro_uid', $oDataAux) || array_key_exists('PRO_UID', $oDataAux)) {
                if(array_key_exists('pro_uid', $oDataAux)) {
                    $proUid = $oDataAux['pro_uid'];
                } else {
                    $proUid = $oDataAux['PRO_UID'];
                }
            } else {
                $proUid = $oDataAux['uid'];
                $uidAux = $proUid;
            }

            $infoProcess = new Processes();

            if(!$infoProcess->processExists($proUid)) {
                $oSL = new SwimlanesElements();
                if($oSL->swimlanesElementsExists($proUid)) {
                    $aFields = $oSL->load($proUid);
                    $proUid = $aFields['PRO_UID'];
                } else {
                    $k = new Criteria('workflow');
                    $k->clearSelectColumns();
                    $k->addSelectColumn(TaskPeer::PRO_UID);
                    $k->add(TaskPeer::TAS_UID, $uidAux );
                    $rs = TaskPeer::doSelectRS($k);
                    $rs->setFetchmode(ResultSet::FETCHMODE_ASSOC);
                    $rs->next();
                    $row = $rs->getRow();
                    $proUid = $row['PRO_UID'];
                }
            }
            $resultProcess = $infoProcess->getProcessRow($proUid);
        } else {
            if(array_key_exists('PU_UID', $oDataAux)) {
                $c = new Criteria('workflow');
                $c->clearSelectColumns();
                $c->addSelectColumn(ProcessUserPeer::PRO_UID);
                $c->addSelectColumn(ProcessUserPeer::USR_UID);
                $c->add(ProcessUserPeer::PU_UID, $oData->PU_UID);
                $oDataset = AppDelegationPeer::doSelectRS($c);
                $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
                $oDataset->next();
                $row = $oDataset->getRow();
                $userSupervisor = $row['USR_UID'];

                $infoProcess = new Processes();
                $resultProcess = $infoProcess->getProcessRow($row['PRO_UID']);
            }
        }
    }

    if(isset($_REQUEST['pro_uid']) && !empty($_REQUEST['pro_uid']) || isset($_REQUEST['PRO_UID']) && !empty($_REQUEST['PRO_UID'])) {
        if(isset($_REQUEST['pro_uid']) && !empty($_REQUEST['pro_uid'])) {
            $proUid = $_REQUEST['pro_uid'];
        } else {
            $proUid = $_REQUEST['PRO_UID'];
        }

        $infoProcess = new Processes();
        $resultProcess = $infoProcess->getProcessRow($proUid);
    }

    if(isset($proUid) && $proUid != "") {

        $infoProcess = new Processes();

        if (!in_array($_REQUEST["action"], array("load"))) {
            $infoProcess->updateProcessRow(array(
                "PRO_UID"         => $proUid,
                "PRO_UPDATE_DATE" => date("Y-m-d H:i:s")
            ));
        }

        $resultProcess = $infoProcess->getProcessRow($proUid);
    }

    $oProcessMap = new ProcessMap(new DBConnection());

    switch ($_REQUEST['action']) {
        case 'load':
            $_SESSION['PROCESS'] = $oData->uid;
            if ($oData->ct) {
                $sOutput = $oProcessMap->load($oData->uid, true, $_SESSION['APPLICATION'], - 1, $_SESSION['TASK'], $oData->ct);
            } else {
                if ($oData->mode) {
                    $sOutput = $oProcessMap->load($oData->uid);
                } else {
                    if ($_SESSION['TASK'] != - 1) {
                        $taskUid = $_SESSION['TASK'];
                    } else {
                        $c = new Criteria('workflow');
                        $c->clearSelectColumns();
                        $c->addSelectColumn(AppDelegationPeer::TAS_UID);
                        $c->add(AppDelegationPeer::APP_UID, $_SESSION['APPLICATION']);
                        $c->add(AppDelegationPeer::DEL_INDEX, $_SESSION['INDEX']);
                        $oDataset = AppDelegationPeer::doSelectRS($c);
                        $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
                        $oDataset->next();
                        $aData = $oDataset->getRow();
                        $taskUid = isset($aData['TAS_UID']) ? $aData['TAS_UID'] : - 1;
                    }
                    $sOutput = $oProcessMap->load($oData->uid, true, $_SESSION['APPLICATION'], $_SESSION['INDEX'], $taskUid);
                }
            }
            break;
        case 'process_Edit':
            $oProcessMap->editProcess($oData->pro_uid);
            break;
        case 'process_Export':
            include (PATH_METHODS . 'processes/processes_Export.php');
            G::auditLog('ExportProcess','Export process "'.$resultProcess['PRO_TITLE'].'"');
            break;
        case 'process_User':
            include (PATH_METHODS . 'processes/processes_User.php');
            break;
        case 'availableProcessesUser':
            include (PATH_METHODS . 'processes/processes_availableProcessesUser.php');
            break;
        case 'webEntry_generate':
            include (PATH_METHODS . 'processes/processes_webEntryGenerate.php');
            break;
        // add this event to validate de data to create a Web Entry
        case 'webEntry_validate':
            include (PATH_METHODS . 'processes/processes_webEntryValidate.php');
            break;
        case 'webEntry_delete':

            $filter = new InputFilter();
            $form = $_REQUEST;
            $filePath = PATH_DATA . "sites" . PATH_SEP . SYS_SYS . PATH_SEP . "public" . PATH_SEP . $form['PRO_UID'] . PATH_SEP . $form['FILENAME'];
            if (file_exists($filePath)) {
                unlink($filter->validateInput($filePath, 'path'));
                $webEntry = new \ProcessMaker\BusinessModel\WebEntry();
                $webEntry->deleteClassic($form['PRO_UID'], $filePath);
            }
            $filePath = PATH_DATA . "sites" . PATH_SEP . SYS_SYS . PATH_SEP . "public" . PATH_SEP . $form['PRO_UID'] . PATH_SEP . str_replace(".php", "Post", $form['FILENAME']) . ".php";
            if (file_exists($filePath)) {
                unlink($filter->validateInput($filePath, 'path'));
            }
            $oProcessMap->webEntry($_REQUEST['PRO_UID']);
            G::auditLog('WebEntry', 'Delete web entry (' . $form['FILENAME'] . ') in process "' . $resultProcess['PRO_TITLE'] . '"');
            break;
        case 'webEntry_new':
            $oProcessMap->webEntry_new($oData->PRO_UID);
            break;
        case 'assignProcessUser':
            $oProcessMap->assignProcessUser($oData->PRO_UID, $oData->USR_UID, $oData->TYPE_UID);

            $oProcessMap = new ProcessMap();
            $oProcessMap->listProcessesUser($oData->PRO_UID);
            G::auditLog('AssignRole','Assign new supervisor ('.$oData->USR_UID.') in process "'.$resultProcess['PRO_TITLE'].'"');
            break;
        case 'removeProcessUser':
            $oProcessMap->removeProcessUser($oData->PU_UID);
            foreach ($_SESSION['_DBArray']['data'] as $key => $value) {
                if ($value['LA_PU_UID'] == $oData->PU_UID) {
                    unset($_SESSION['_DBArray']['data'][$key]);
                    break;
                }
            }
            G::auditLog('RemoveUser','Remove supervisor ('.$userSupervisor.') in process "'.$resultProcess['PRO_TITLE'].'"');
            break;
        case 'supervisorDynaforms':
            $oProcessMap->supervisorDynaforms($oData->pro_uid);
            break;
        case 'supervisorInputs':
            $oProcessMap->supervisorInputs($oData->pro_uid);
            break;
        case 'webEntry':
            $oProcessMap->webEntry($oData->pro_uid);
            break;
        case 'webEntry_Val_Assig':
            include (PATH_METHODS . 'processes/webEntry_Val_Assig.php');
            break;
        case 'saveTitlePosition':
            $sOutput = $oProcessMap->saveTitlePosition($oData->pro_uid, $oData->position->x, $oData->position->y);
            break;
        case 'steps':
            switch ($oData->option) {
                case 1:
                    $oProcessMap->steps($oData->proUid, $oData->tasUid);
                    break;
                case 2:
                    $oProcessMap->stepsConditions($oData->proUid, $oData->tasUid);
                    break;
                case 3:
                    $oProcessMap->stepsTriggers($oData->proUid, $oData->tasUid);
                    break;
            }
            break;
        case 'users':
            $oProcessMap->users($oData->pro_uid, $oData->tas_uid);
            break;
        case 'users_adhoc':
            $oProcessMap->users_adhoc($oData->pro_uid, $oData->tas_uid);
            break;
        case 'addTask':
            $sOutput = $oProcessMap->addTask($oData->uid, $oData->position->x, $oData->position->y);
            $sOutputAux = G::json_decode($sOutput);
            $sOutputAux = (array)$sOutputAux;
            G::auditLog('AddTask','Add new task ('.$sOutputAux['uid'].') in process "'.$resultProcess['PRO_TITLE'].'"');
            break;
        case 'addSubProcess':
            $sOutput = $oProcessMap->addSubProcess($oData->uid, $oData->position->x, $oData->position->y);
            $sOutputAux = G::json_decode($sOutput);
            $sOutputAux = (array)$sOutputAux;
            G::auditLog('AddSubProcess','Add new sub process ('.$sOutputAux['uid'].') in process "'.$resultProcess['PRO_TITLE'].'"');
            break;
        case 'taskColor':
            $oTask->taskColor($oData->pro_uid, $oData->tas_uid);
            break;
        case 'addTaskHidden':
            $sOutput = $oProcessMap->addTaskHidden($oData->uid, $oData->position->x, $oData->position->y);
            break;
        case 'editTaskProperties':
            $oProcessMap->editTaskProperties($oData->uid, (isset($oData->iForm) ? $oData->iForm : 1), $oData->index);
            break;
        case 'saveTaskPosition':
            $sOutput = $oProcessMap->saveTaskPosition($oData->uid, $oData->position->x, $oData->position->y);
            $oTask = new Task();
            $oTask->load($uidAux);
            G::auditLog('SaveTaskPosition','Change task position ('.$oTask->getTasTitle().') in process "'.$resultProcess['PRO_TITLE'].'"');
            break;
        case 'deleteTask':
            $oTaskNewPattern = new Task();
            $taskInfo=$oTaskNewPattern->load($oData->tas_uid);
            $titleTask=$taskInfo['TAS_TITLE'];
            G::auditlog("DeleteTask",'Delete Task -> '.$titleTask.' : '.$oData->tas_uid);
            $sOutput = $oProcessMap->deleteTask($oData->tas_uid);
            break;
        case 'addGuide':
            $sOutput = $oProcessMap->addGuide($oData->uid, $oData->position, $oData->direction);
            $sOutputAux = G::json_decode($sOutput);
            $sOutputAux = (array)$sOutputAux;
            G::auditLog('Add'.ucwords($oDataAux['direction']).'Line','Add '.$oDataAux['direction'].' line ('.$sOutputAux['uid'].') in process "'.$resultProcess['PRO_TITLE'].'"');
            break;
        case 'saveGuidePosition':
            $sOutput = $oProcessMap->saveGuidePosition($oData->uid, $oData->position, $oData->direction);
            G::auditLog('SaveGuidePosition','Change '.$oData->direction.' line position  ('.$oData->uid.') in process "'.$resultProcess['PRO_TITLE'].'"');
            break;
        case 'deleteGuide':
            $sOutput = $oProcessMap->deleteGuide($oData->uid);
            G::auditLog('DeleteLine','Delete line ('.$oData->uid.') in process "'.$resultProcess['PRO_TITLE'].'"');
            break;
        case 'deleteGuides':
            $sOutput = $oProcessMap->deleteGuides($oData->pro_uid);
            G::auditLog('DeleteLines','Delete all lines in process "'.$resultProcess['PRO_TITLE'].'"');
            break;
        case 'addText':
            $sOutput = $oProcessMap->addText($oData->uid, html_entity_decode(html_entity_decode($oData->label)), $oData->position->x, $oData->position->y);
            $sOutputAux = G::json_decode($sOutput);
            $sOutputAux = (array)$sOutputAux;
            G::auditLog('AddText','Add new text ('.$sOutputAux['uid'].') in process "'.$resultProcess['PRO_TITLE'].'"');
            break;
        case 'updateText':
            $sOutput = $oProcessMap->updateText($oData->uid, $oData->label);
            G::auditLog('UpdateText','Edit text ('.$oData->uid.' ) in process "'.$resultProcess['PRO_TITLE'].'"');
            break;
        case 'saveTextPosition':
            $sOutput = $oProcessMap->saveTextPosition($oData->uid, $oData->position->x, $oData->position->y);
            G::auditLog('SaveTextPosition','Change text position ('.$oData->uid.' ) in process "'.$resultProcess['PRO_TITLE'].'"');
            break;
        case 'deleteText':
            $sOutput = $oProcessMap->deleteText($oData->uid);
            G::auditLog('DeleteText','Delete text ('.$oData->uid.' ) in process "'.$resultProcess['PRO_TITLE'].'"');
            break;
        case 'dynaforms':
            $oProcessMap->dynaformsList($oData->pro_uid);
            break;
        case 'inputs':
            $oProcessMap->inputdocsList($oData->pro_uid);
            break;
        case 'outputs':
            $oProcessMap->outputdocsList($oData->pro_uid);
            break;
        case 'triggers':
            $oProcessMap->triggersList($oData->pro_uid);
            break;
        case 'case_scheduler':
            if (isset($_REQUEST['PRO_UID'])) {
                $oProcessMap->caseSchedulerList($_REQUEST['PRO_UID']);
            }
            break;
        case 'log_case_scheduler':
            if (isset($_REQUEST['PRO_UID'])) {
                $oProcessMap->logCaseSchedulerList($_REQUEST['PRO_UID']);
            }
            break;
        case 'messages':
            $oProcessMap->messagesList($oData->pro_uid);
            break;
        case 'reportTables':
            $oProcessMap->reportTablesList($oData->pro_uid);
            break;
        case 'derivations':
            if (!isset($oData->type)) {
                $oProcessMap->currentPattern($oData->pro_uid, $oData->tas_uid);
            } else {
                switch ($oData->type) {
                    case 0:
                        $oData->type = 'SEQUENTIAL';
                        break;
                    case 1:
                        $oData->type = 'SELECT';
                        break;
                    case 2:
                        $oData->type = 'EVALUATE';
                        break;
                    case 3:
                        $oData->type = 'PARALLEL';
                        break;
                    case 4:
                        $oData->type = 'PARALLEL-BY-EVALUATION';
                        break;
                    case 5:
                        $oData->type = 'SEC-JOIN';
                        break;
                    case 8:
                        $oData->type = 'DISCRIMINATOR';
                        break;
                }
                $oProcessMap->newPattern($oData->pro_uid, $oData->tas_uid, $oData->next_task, $oData->type);
            }
            break;
        case 'saveNewPattern':
            switch ($oData->type) {
                case 0:
                    $sType = 'SEQUENTIAL';
                    $oProcessNewPattern = new Process();
                    $taskProcess=$oProcessNewPattern->load($oData->pro_uid);
                    $titleProcess=$taskProcess['PRO_TITLE'];
                    $oTaskNewPattern = new Task();
                    $taskInfo=$oTaskNewPattern->load($oData->tas_uid);
                    $titleTask=$taskInfo['TAS_TITLE'];
                    if ($oData->next_task != "-1") {
                        $oTaskNextNewPattern = new Task();
                        $taskNextInfo=$oTaskNextNewPattern->load($oData->next_task);
                        $titleNextTask=$taskNextInfo['TAS_TITLE'];
                    } else {
                        $titleNextTask=G::LoadTranslation("ID_END_OF_PROCESS");
                    }
                    if ($titleNextTask=='') {
                        G::auditLog("DerivationRule",'PROCESS NAME : '.$titleProcess.' : '.$oData->pro_uid.' Routing rule : END OF PROCESS Task Name -> '.$titleTask.' : '.$oData->tas_uid);
                    }else{
                        G::auditLog("DerivationRule",'PROCESS NAME : '.$titleProcess.' : '.$oData->pro_uid.' Routing rule : '.$sType.' from -> '.$titleTask.' : '.$oData->tas_uid.' To -> '.$titleNextTask.' : '.$oData->next_task);
                    }
                    break;
                case 1:
                    $sType = 'SELECT';
                    $oProcessNewPattern = new Process();
                    $taskProcess=$oProcessNewPattern->load($oData->pro_uid);
                    $titleProcess=$taskProcess['PRO_TITLE'];
                    $oTaskNewPattern = new Task();
                    $taskInfo=$oTaskNewPattern->load($oData->tas_uid);
                    $titleTask=$taskInfo['TAS_TITLE'];
                    if ($oData->next_task != "-1") {
                        $oTaskNextNewPattern = new Task();
                        $taskNextInfo=$oTaskNextNewPattern->load($oData->next_task);
                        $titleNextTask=$taskNextInfo['TAS_TITLE'];
                    } else {
                        $titleNextTask=G::LoadTranslation("ID_END_OF_PROCESS");
                    }
                    G::auditLog("DerivationRule",'PROCESS NAME : '.$titleProcess.' : '.$oData->pro_uid.' Routing rule : '.$sType.' from -> '.$titleTask.' : '.$oData->tas_uid.' To -> '.$titleNextTask.' : '.$oData->next_task);
                    break;
                case 2:
                    $sType = 'EVALUATE';
                    $oProcessNewPattern = new Process();
                    $taskProcess=$oProcessNewPattern->load($oData->pro_uid);
                    $titleProcess=$taskProcess['PRO_TITLE'];
                    $oTaskNewPattern = new Task();
                    $taskInfo=$oTaskNewPattern->load($oData->tas_uid);
                    $titleTask=$taskInfo['TAS_TITLE'];
                    if ($oData->next_task != "-1") {
                        $oTaskNextNewPattern = new Task();
                        $taskNextInfo=$oTaskNextNewPattern->load($oData->next_task);
                        $titleNextTask=$taskNextInfo['TAS_TITLE'];
                    } else {
                        $titleNextTask=G::LoadTranslation("ID_END_OF_PROCESS");
                    }
                    G::auditLog("DerivationRule",'PROCESS NAME : '.$titleProcess.' : '.$oData->pro_uid.' Routing rule : '.$sType.' from -> '.$titleTask.' : '.$oData->tas_uid.' To -> '.$titleNextTask.' : '.$oData->next_task);
                    break;
                case 3:
                    $sType = 'PARALLEL';
                    $oProcessNewPattern = new Process();
                    $taskProcess=$oProcessNewPattern->load($oData->pro_uid);
                    $titleProcess=$taskProcess['PRO_TITLE'];
                    $oTaskNewPattern = new Task();
                    $taskInfo=$oTaskNewPattern->load($oData->tas_uid);
                    $titleTask=$taskInfo['TAS_TITLE'];
                    if ($oData->next_task != "-1") {
                        $oTaskNextNewPattern = new Task();
                        $taskNextInfo=$oTaskNextNewPattern->load($oData->next_task);
                        $titleNextTask=$taskNextInfo['TAS_TITLE'];
                    } else {
                        $titleNextTask=G::LoadTranslation("ID_END_OF_PROCESS");
                    }
                    G::auditLog("DerivationRule",'PROCESS NAME : '.$titleProcess.' : '.$oData->pro_uid.' Routing rule : '.$sType.' from -> '.$titleTask.' : '.$oData->tas_uid.' To -> '.$titleNextTask.' : '.$oData->next_task);
                    break;
                case 4:
                    $sType = 'PARALLEL-BY-EVALUATION';
                    $oProcessNewPattern = new Process();
                    $taskProcess=$oProcessNewPattern->load($oData->pro_uid);
                    $titleProcess=$taskProcess['PRO_TITLE'];
                    $oTaskNewPattern = new Task();
                    $taskInfo=$oTaskNewPattern->load($oData->tas_uid);
                    $titleTask=$taskInfo['TAS_TITLE'];
                    if ($oData->next_task != "-1") {
                        $oTaskNextNewPattern = new Task();
                        $taskNextInfo=$oTaskNextNewPattern->load($oData->next_task);
                        $titleNextTask=$taskNextInfo['TAS_TITLE'];
                    } else {
                        $titleNextTask=G::LoadTranslation("ID_END_OF_PROCESS");
                    }
                    G::auditLog("DerivationRule",'PROCESS NAME : '.$titleProcess.' : '.$oData->pro_uid.' Routing rule : '.$sType.' from -> '.$titleTask.' : '.$oData->tas_uid.' To -> '.$titleNextTask.' : '.$oData->next_task);
                    break;
                case 5:
                    $sType = 'SEC-JOIN';
                    $oProcessNewPattern = new Process();
                    $taskProcess=$oProcessNewPattern->load($oData->pro_uid);
                    $titleProcess=$taskProcess['PRO_TITLE'];
                    $oTaskNewPattern = new Task();
                    $taskInfo=$oTaskNewPattern->load($oData->tas_uid);
                    $titleTask=$taskInfo['TAS_TITLE'];
                    if ($oData->next_task != "-1") {
                        $oTaskNextNewPattern = new Task();
                        $taskNextInfo=$oTaskNextNewPattern->load($oData->next_task);
                        $titleNextTask=$taskNextInfo['TAS_TITLE'];
                    } else {
                        $titleNextTask=G::LoadTranslation("ID_END_OF_PROCESS");
                    }
                    G::auditLog("DerivationRule",'PROCESS NAME : '.$titleProcess.' : '.$oData->pro_uid.' Routing rule : '.$sType.' from -> '.$titleTask.' : '.$oData->tas_uid.' To -> '.$titleNextTask.' : '.$oData->next_task);
                    break;
                case 8:
                    $sType = 'DISCRIMINATOR';
                    $oProcessNewPattern = new Process();
                    $taskProcess=$oProcessNewPattern->load($oData->pro_uid);
                    $titleProcess=$taskProcess['PRO_TITLE'];
                    $oTaskNewPattern = new Task();
                    $taskInfo=$oTaskNewPattern->load($oData->tas_uid);
                    $titleTask=$taskInfo['TAS_TITLE'];
                    if ($oData->next_task != "-1") {
                        $oTaskNextNewPattern = new Task();
                        $taskNextInfo=$oTaskNextNewPattern->load($oData->next_task);
                        $titleNextTask=$taskNextInfo['TAS_TITLE'];
                    } else {
                        $titleNextTask=G::LoadTranslation("ID_END_OF_PROCESS");
                    }
                    G::auditLog("DerivationRule",'PROCESS NAME : '.$titleProcess.' : '.$oData->pro_uid.' Routing rule : '.$sType.' from -> '.$titleTask.' : '.$oData->tas_uid.' To -> '.$titleNextTask.' : '.$oData->next_task);
                    break;
            }
            if (($oData->type != 0) && ($oData->type != 5) && ($oData->type != 8)) {
                if ($oProcessMap->getNumberOfRoutes($oData->pro_uid, $oData->tas_uid, $oData->next_task, $sType) > 0) {
                    die();
                }
                unset($aRow);
            }
            if (($oData->delete) || ($oData->type == 0) || ($oData->type == 5) || ($oData->type == 8)) {

                $oTasks = new Tasks();
                $oTasks->deleteAllRoutesOfTask($oData->pro_uid, $oData->tas_uid);
                $oTasks->deleteAllGatewayOfTask($oData->pro_uid, $oData->tas_uid);
            }
            $oProcessMap->saveNewPattern($oData->pro_uid, $oData->tas_uid, $oData->next_task, $sType, $oData->delete);
            break;
        case 'deleteAllRoutes':

            $oTaskNewPattern = new Task();
            $taskInfo=$oTaskNewPattern->load($oData->tas_uid);
            $titleTask=$taskInfo['TAS_TITLE'];
            G::auditlog("DeleteRoutes",'Delete All Routes From Task -> '.$titleTask.' : '.$oData->tas_uid);
            $oTasks = new Tasks();
            $oTasks->deleteAllRoutesOfTask($oData->pro_uid, $oData->tas_uid);
            break;
        case 'objectPermissions':
            $oProcessMap->objectsPermissionsList($oData->pro_uid);
            break;
        case 'newObjectPermission':
            $oProcessMap->newObjectPermission($oData->pro_uid);
            break;
        case 'editObjectPermission':
            // we also need the process uid variable for the function.
            $oProcessMap->editObjectPermission($oData->op_uid, $oData->pro_uid);
            break;
        case 'caseTracker':
            $oProcessMap->caseTracker($oData->pro_uid);
            break;
        case 'caseTrackerObjects':
            $oProcessMap->caseTrackerObjects($oData->pro_uid);
            break;
        case 'processFilesManager':
            $_SESSION['PFMDirectory'] = '';
            $oProcessMap->processFilesManager($oData->pro_uid);
            break;
        case 'exploreDirectory':
            $_SESSION["PFMDirectory"] = $oData->main_directory;
            $oProcessMap->exploreDirectory($oData->pro_uid, $oData->main_directory, $oData->directory);
            G::auditLog('ProcessFileManager','Upload template ('.$oData->main_directory.') in process "'.$resultProcess['PRO_TITLE'].'"');
            break;
        case 'deleteFile':
            $oProcessMap->deleteFile($oData->pro_uid, $oData->main_directory, $oData->directory, $oData->file);
            G::auditLog('ProcessFileManager','Delete template ('.$oData->main_directory.': '.$oData->file.') in process "'.$resultProcess['PRO_TITLE'].'"');
            break;
        case 'deleteDirectory':
            $oProcessMap->deleteDirectory($oData->pro_uid, $oData->main_directory, $oData->directory, $oData->dir_to_delete);
            break;
        case 'downloadFile':
            $oProcessMap->downloadFile($oData->pro_uid, $oData->main_directory, $oData->directory, $oData->file);
            break;
        case 'deleteSubProcess':
            $oTaskNewPattern = new Task();
            $taskInfo=$oTaskNewPattern->load($oData->tas_uid);
            $titleTask=$taskInfo['TAS_TITLE'];
            G::auditlog("DeleteSubProcess",'Delete Sub-Process -> '.$titleTask.' : '.$oData->tas_uid);
            $sOutput = $oProcessMap->deleteSubProcess($oData->pro_uid, $oData->tas_uid);
            break;
        case 'subProcess_Properties':
            $oProcessMap->subProcess_Properties($oData->pro_uid, $oData->tas_uid, $oData->index);
            break;
        case 'showDetailsPMDWL':

            $oProcesses = new Processes();
            $oProcesses->ws_open_public();
            $aFields = get_object_vars($oProcesses->ws_processGetData($oData->pro_uid));

            $aFields['description'] = nl2br($aFields['description']);
            $aFields['installSteps'] = nl2br($aFields['installSteps']);
            switch ($aFields['privacy']) {
                case 'FREE':
                    $aFields['link_label'] = G::LoadTranslation('ID_DOWNLOAD');
                    $aFields['link_href'] = '../processes/downloadPML?id=' . $oData->pro_uid . '&s=' . $sessionId;
                    break;
                case 'PUBLIC':
                    require_once 'classes/model/Configuration.php';
                    $oCriteria = new Criteria('workflow');
                    $oCriteria->addSelectColumn(ConfigurationPeer::CFG_VALUE);
                    $oCriteria->add(ConfigurationPeer::CFG_UID, 'REGISTER_INFORMATION');
                    $oCriteria->add(ConfigurationPeer::USR_UID, $_SESSION['USER_LOGGED']);
                    if (ConfigurationPeer::doCount($oCriteria) > 0) {
                        $oDataset = ConfigurationPeer::doSelectRS($oCriteria);
                        $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
                        $oDataset->next();
                        $aRow = $oDataset->getRow();
                        $aRI = unserialize($aRow['CFG_VALUE']);
                        try {
                            if ($oProcesses->ws_open($aRI['u'], $aRI['p']) == 1) {
                                $bExists = true;
                            } else {
                                $bExists = false;
                            }
                        } catch (Exception $oException) {
                            $bExists = false;
                        }
                        if ($bExists) {
                            $aFields['link_label'] = G::LoadTranslation('ID_DOWNLOAD');
                            $aFields['link_href'] = '../processes/downloadPML?id=' . $oData->pro_uid . '&s=' . $sessionId;
                        } else {
                            $aFields['link_label'] = G::LoadTranslation('ID_NEED_REGISTER');
                            $aFields['link_href'] = "javascript:registerPML('" . $oData->pro_uid . "');";
                        }
                    } else {
                        $aFields['link_label'] = G::LoadTranslation('ID_NEED_REGISTER');
                        $aFields['link_href'] = "javascript:registerPML('" . $oData->pro_uid . "');";
                    }
                    break;
            }
            $G_PUBLISH = new Publisher();
            $G_PUBLISH->AddContent('xmlform', 'xmlform', 'processes/objectpmView', '', $aFields, '');
            G::RenderPage('publish', 'raw');
            break;
        case 'registerPML':
            $aFields = array();
            $aFields['pro_uid'] = $oData->pro_uid;
            $aFields['link_create_account'] = PML_SERVER;
            $G_PUBLISH = new Publisher();
            $G_PUBLISH->AddContent('xmlform', 'xmlform', 'processes/registerPML', '', $aFields, '');
            G::RenderPage('publish', 'raw');
            break;
        case 'loginPML':


            $oProcesses = new Processes();
            try {
                if ($oProcesses->ws_open($oData->u, $oData->p) == 1) {
                    $bExists = true;
                } else {
                    $bExists = false;
                }
            } catch (Exception $oException) {
                $bExists = false;
            }
            $oResponse = new stdclass();
            if ($bExists) {
                require_once 'classes/model/Configuration.php';
                $oConfiguration = new Configuration();
                $oConfiguration->create(array('CFG_UID' => 'REGISTER_INFORMATION', 'OBJ_UID' => '', 'CFG_VALUE' => serialize(array('u' => $oData->u, 'p' => $oData->p
                    )), 'PRO_UID' => '', 'USR_UID' => $_SESSION['USER_LOGGED'], 'APP_UID' => ''
                ));
                $oResponse->sLabel = G::LoadTranslation('ID_DOWNLOAD');
                $oResponse->sLink = '../processes/downloadPML?id=' . $oData->pro_uid . '&s=' . $sessionId;
            }
            $oResponse->bExists = $bExists;
            //$oJSON = new Services_JSON();
            echo Bootstrap::json_encode($oResponse);
            break;
        case 'editFile':
            //echo $_REQUEST['filename'];
            global $G_PUBLISH;
            $G_PUBLISH = new Publisher();
            ///-- $sDirectory = PATH_DATA_MAILTEMPLATES . $_REQUEST['pro_uid'] . PATH_SEP . $_REQUEST['filename'];
            $sDir = "";
            if (isset($_SESSION['PFMDirectory'])) {
                $sDir = $_SESSION['PFMDirectory'];
            }
            switch ($sDir) {
                case 'mailTemplates':
                    $sDirectory = PATH_DATA_MAILTEMPLATES . $_REQUEST['pro_uid'] . PATH_SEP . $_REQUEST['filename'];
                    G::auditLog('ProcessFileManager','Edit template ('.$_REQUEST['filename'].') in process "'.$resultProcess['PRO_TITLE'].'"');
                    break;
                case 'public':
                    $sDirectory = PATH_DATA_PUBLIC . $_REQUEST['pro_uid'] . PATH_SEP . $_REQUEST['filename'];
                    G::auditLog('ProcessFileManager','Edit public template ('.$_REQUEST['filename'].') in process "'.$resultProcess['PRO_TITLE'].'"');
                    break;
                default:
                    $sDirectory = PATH_DATA_MAILTEMPLATES . $_REQUEST['pro_uid'] . PATH_SEP . $_REQUEST['filename'];
                    break;
            }
            $fcontent = file_get_contents($sDirectory);
            $extion = explode(".", $_REQUEST['filename']);
            //            $oHeadPublisher = &headPublisher::getSingleton();
            //            $oHeadPublisher->clearScripts();
            //            $oHeadPublisher->addScriptFile( '/js/tinymce/jscripts/tiny_mce/tiny_mce.js' );
            //            $jscriptCode .= '
            //
            ////                    var tmpArrToStr = Array.prototype.toStr;
            ////                    var tmpObjToStr = Object.prototype.toStr;
            ////                    var tmpObjConcat = Object.prototype.concat;
            ////                    var tmpObjGetByKey = Object.prototype.get_by_key;
            ////                    var tmpObjExpand = Object.prototype.expand;
            ////                    var tmpObjSetParent = Object.prototype.setParent;
            ////                    var tmpObjIsSetKey = Object.prototype.isset_key;
            ////
            ////                    delete Array.prototype.toStr;
            ////                    delete Object.prototype.toStr;
            ////                    delete Object.prototype.concat;
            ////                    delete Object.prototype.get_by_key;
            ////                    delete Object.prototype.expand;
            ////                    delete Object.prototype.setParent;
            ////                    delete Object.prototype.isset_key;
            ////                alert ("hi");
            ////                document.body.onload = function(){
            //                    alert ("hello");
            //                    tinyMCE.baseURL = "/js/tinymce/jscripts/tiny_mce";
            //                    tinyMCE.init({
            //                        theme   : "advanced",
            //                        plugins : "fullpage",
            //                        mode    : "specific_textareas",
            //                        editor_selector : "tmceEditor",
            //                        width   : "640",
            //                        height  : "300",
            //                        theme_advanced_buttons3_add : "fullpage"
            //                    });
            ////                    alert ("goodbye");
            ////                }
            //            ';
            //            $oHeadPublisher->addScriptCode($jscriptCode);
            $_REQUEST['fcontent'] = $fcontent;

            preg_match_all('/\@(?:([\>])([a-zA-Z\_]\w*)|([a-zA-Z\_][\w\-\>\:]*)\(((?:[^\\\\\)]*(?:[\\\\][\w\W])?)*)\))((?:\s*\[[\'"]?\w+[\'"]?\])+)?/',$fcontent,$matches);
            foreach ($matches[0] as $key => $value) {
                preg_match('/(\!--)'.$value.'(-->)/',$fcontent, $match);
                if (count($match)==0) {
                    $fcontent = preg_replace ('/'.$value.'/i','<!--'.$value.'-->',$fcontent);
                }
            }
            preg_match_all('/\@(?:([\<])([a-zA-Z\_]\w*)|([a-zA-Z\_][\w\-\>\:]*)\(((?:[^\\\\\)]*(?:[\\\\][\w\W])?)*)\))((?:\s*\[[\'"]?\w+[\'"]?\])+)?/',$fcontent,$matches);
            foreach ($matches[0] as $key => $value) {
                    preg_match('/(\!--)'.$value.'(-->)/',$fcontent, $match);
                    if (count($match)==0) {
                        $fcontent = preg_replace ('/'.$value.'/i','<!--'.$value.'-->',$fcontent);
                    }
            }

            //if($extion[count($extion)-1]=='html' || $extion[count($extion)-1]=='txt'){
            $aData = Array('pro_uid' => $_REQUEST['pro_uid'], 'fcontent' => $fcontent, 'filename' => $_REQUEST['filename']);
            $G_PUBLISH->AddContent('xmlform', 'xmlform', 'processes/processes_FileEdit', '', $aData);
            G::RenderPage('publish', 'raw');
            // $G_PUBLISH->AddContent( 'view', 'processes/processesFileEditEmail' );
            // G::RenderPage( 'publish', 'blank' );
            break;
        case 'saveFile':
            $_REQUEST['pro_uid'] = $filter->xssFilterHard($_REQUEST['pro_uid']);
            $_REQUEST['filename'] = $filter->xssFilterHard($_REQUEST['filename']);
            global $G_PUBLISH;
            $G_PUBLISH = new Publisher();
            global $RBAC;
            if ( $RBAC->userCanAccess('PM_FACTORY') == 1) {

                $app = new Processes();
                if (!$app->processExists($_REQUEST['pro_uid'])) {
                    echo G::LoadTranslation('ID_PROCESS_UID_NOT_DEFINED');
                    die;
                }

                $sDir = "";
                if (isset($_REQUEST['MAIN_DIRECTORY'])) {
                    $_REQUEST['MAIN_DIRECTORY'] = $filter->xssFilterHard($_REQUEST['MAIN_DIRECTORY']);
                    $sDir = $_REQUEST['MAIN_DIRECTORY'];
                }
                switch ($sDir) {
                    case 'mailTemplates':
                        $sDirectory = PATH_DATA_MAILTEMPLATES . $_REQUEST['pro_uid'] . PATH_SEP . $_REQUEST['filename'];
                        G::auditLog('ProcessFileManager','Save template ('.$_REQUEST['filename'].') in process "'.$resultProcess['PRO_TITLE'].'"');
                        break;
                    case 'public':
                        $sDirectory = PATH_DATA_PUBLIC . $_REQUEST['pro_uid'] . PATH_SEP . $_REQUEST['filename'];
                        G::auditLog('ProcessFileManager','Save public template ('.$_REQUEST['filename'].') in process "'.$resultProcess['PRO_TITLE'].'"');
                        break;
                    default:
                        $sDirectory = PATH_DATA_MAILTEMPLATES . $_REQUEST['pro_uid'] . PATH_SEP . $_REQUEST['filename'];
                        break;
                }
                $fp = fopen($sDirectory, 'w');
                $content = stripslashes($_REQUEST['fcontent']);
                $content = str_replace("@amp@", "&", $content);
                $content = base64_decode($content);
                fwrite($fp, $content);
                fclose($fp);
                $sDirectory = $filter->xssFilterHard($sDirectory);
                echo 'saved: ' . $sDirectory;
            }
            break;
        case 'getSessid':
            if(isset($_SESSION['USER_LOGGED'])){
                echo Bootstrap::json_encode(1);
            }else{
                echo Bootstrap::json_encode(0);
            }
            break;
        case 'events':
            $oProcessMap->eventsList($oData->pro_uid, $oData->type);
            break;
            /**
            * returns an array with all Dynaforms Fields
            */
        case 'getVariableList':

            $proUid = isset($_REQUEST['process']) ? $_REQUEST['process'] : '';
            $queryText = isset($_REQUEST['queryText']) ? $_REQUEST['queryText'] : '';
            switch($_REQUEST['type']) {
                case 'system';
                    $typeVars = $_REQUEST['type'];
                break;
                case 'process';
                    $typeVars = $_REQUEST['type'];
                break;
                case 'grid';
                    $typeVars = $_REQUEST['type'];
                break;     
            }
            if ($_REQUEST['type'] == 'all') {
                $aFields = getDynaformsVars($proUid);
            } else {
                $aFields = getDynaformsVars($proUid, $typeVars, isset($_REQUEST['bIncMulSelFields']) ? $_REQUEST['bIncMulSelFields'] : 1);
            }
            $aVariables = array();

            if ($queryText != "") {
                foreach ($aFields as $key => $value) {
                    if (stristr($aFields[$key]["sName"], $queryText)) {
                        $aVariables[] = $aFields[$key];
                    }
                }
            } else {
                switch ($_REQUEST["type"]) {
                    case "system" :
                        foreach ($aFields as $key => $value) {
                            if ($aFields[$key]["sType"] == "system") {
                                $aVariables[] = $aFields[$key];
                            }
                        }
                        break;
                    default :
                        foreach ($aFields as $key => $value) {
                            $aVariables[] = $aFields[$key];
                        }
                        break;
                }
            }

            echo Bootstrap::json_encode($aVariables);
            break;
            /**
            * returns the prefix mean
            *
            */
        case 'getVariablePrefix':
            $_REQUEST['prefix'] = $filter->xssFilterHard($_REQUEST['prefix']);
            $_REQUEST['prefix'] = $_REQUEST['prefix'] != null ? $_REQUEST['prefix'] : 'ID_TO_STRING';
            $prefix = $filter->xssFilterHard(G::LoadTranslation($_REQUEST['prefix']));
            echo G::LoadTranslation($prefix);
            break;
            /**
            * return an array with all Variables of Grid type
            */
        case 'getGridList':

            $proUid = isset($_REQUEST['PRO_UID']) ? $_REQUEST['PRO_UID'] : '';

            $aFields = getGridsVars($proUid);

            $aVariables = array();
            foreach ($aFields as $key => $value) {
                $aVariables[] = $aFields[$key];
            }
            echo Bootstrap::json_encode($aVariables);
            break;
            /**
            * return an array with all Grid Variables according to Grid
            */
        case 'getVariableGrid':

            $proUid = isset($_REQUEST['PRO_UID']) ? $_REQUEST['PRO_UID'] : '';
            $dynUid = isset($_REQUEST['DYN_UID']) ? $_REQUEST['DYN_UID'] : '';

            $aFields = getVarsGrid($proUid, $dynUid);

            $aVariables = array();

            foreach ($aFields as $key => $value) {
                $aVariables[] = $key;
            }

            echo Bootstrap::json_encode($aVariables);
            break;
        case 'getDynaformFieldList':

            $dynaformFields = array();
            $resultArray = array();
            $proUid = isset($_REQUEST['PRO_UID']) ? $_REQUEST['PRO_UID'] : '';
            $dynUid = isset($_REQUEST['DYN_UID']) ? $_REQUEST['DYN_UID'] : '';
            if (is_file(PATH_DATA . '/sites/' . SYS_SYS . '/xmlForms/' . $proUid . '/' . $dynUid . '.xml') && filesize(PATH_DATA . '/sites/' . SYS_SYS . '/xmlForms/' . $proUid . '/' . $dynUid . '.xml') > 0) {
                $dyn = new DynaformHandler(PATH_DATA . '/sites/' . SYS_SYS . '/xmlForms/' . $proUid . '/' . $dynUid . '.xml');
                $dynaformFields[] = $dyn->getFields();
            }
            foreach ($dynaformFields as $aDynFormFields) {
                foreach ($aDynFormFields as $field) {
                    $resultArray[] = array("id" => $field->nodeName, "name" => $field->nodeName);
                }
            }
            echo Bootstrap::json_encode($resultArray);
            // var_dump($resultArray);
            break;
        /*
          case 'saveFile':
          global $G_PUBLISH;
          $G_PUBLISH = new Publisher();
          $sDirectory = PATH_DATA_MAILTEMPLATES . $_REQUEST['pro_uid'] . PATH_SEP . $_REQUEST['filename'];

          $fp = fopen($sDirectory, 'w');
          $content = stripslashes($_REQUEST['fcontent']);
          $content = str_replace("@amp@", "&", $content);
          fwrite($fp, $content);
          fclose($fp);
          echo 'saved: '. $sDirectory;
          break;
         */
        case 'emptyFileOptions':
            global $G_PUBLISH;
            $G_PUBLISH = new Publisher();
            $G_PUBLISH->AddContent('xmlform', 'xmlform', 'processes/processes_FileEditCreateEmpty', '');
            G::RenderPage('publish', 'raw');
            break;
        case "taskCases":
            require_once 'classes/model/AppDelegation.php';
            $criteria = new Criteria('workflow');
            $criteria->addSelectColumn(AppDelegationPeer::APP_UID);
            $criteria->addSelectColumn(AppDelegationPeer::DEL_INDEX);
            $criteria->addSelectColumn(AppDelegationPeer::TAS_UID);
            $criteria->add(AppDelegationPeer::TAS_UID, $oData->task_uid);
            $criteria->add(AppDelegationPeer::DEL_THREAD_STATUS, 'OPEN');
            $casesNumRec = AppDelegationPeer::doCount($criteria);
            if ($casesNumRec == 0) {
                require_once 'classes/model/AppDelay.php';
                $criteria = new Criteria('workflow');
                $criteria->addSelectColumn(AppDelayPeer::APP_UID);
                $criteria->addSelectColumn(AppDelayPeer::APP_DEL_INDEX);
                $criteria->add(AppDelayPeer::PRO_UID, $oData->pro_uid);
                $criteria->add(AppDelayPeer::APP_TYPE, 'PAUSE');
                $criteria->add(AppDelayPeer::APP_DISABLE_ACTION_DATE, null, Criteria::ISNULL);
                $dataset = AppDelayPeer::doSelectRS($criteria);
                if ($dataset->getRecordCount() > 0) {
                    $dataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
                    $dataset->next();
                    while ($row = $dataset->getRow()) {
                        $criteria = new Criteria('workflow');
                        $criteria->addSelectColumn(AppDelegationPeer::TAS_UID);
                        $criteria->add(AppDelegationPeer::APP_UID, $row['APP_UID']);
                        $criteria->add(AppDelegationPeer::DEL_INDEX, $row['APP_DEL_INDEX']);
                        $criteria->add(AppDelegationPeer::TAS_UID, $oData->task_uid);
                        $casesNumRec += AppDelegationPeer::doCount($criteria);
                        $dataset->next();
                    }
                }
            }
            $response = new stdclass();
            $response->casesNumRec = $casesNumRec;
            //$json = new Services_JSON();
            $sOutput = Bootstrap::json_encode($response);
            break;
        case "verifyNameFile":
            $response = array();
            $status = "OK";

            $filename = $_POST["filename"];

            $pathDirectory = PATH_DATA_MAILTEMPLATES . $_REQUEST["pro_uid"] . PATH_SEP;

            if (file_exists($pathDirectory . PATH_SEP . $filename)) {
                $status = "ERROR";
            }

            $response["status"] = $status;
            echo Bootstrap::json_encode($response);
            break;
    }
    if (isset($sOutput)) {
        die($sOutput);
    }
} catch (Exception $oException) {
    $token = strtotime("now");
    PMException::registerErrorLog($oException, $token);
    G::outRes( G::LoadTranslation("ID_EXCEPTION_LOG_INTERFAZ", array($token)) );
    die;
}

