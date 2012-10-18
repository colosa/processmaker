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
G::LoadInclude( 'ajax' );
$oJSON = new Services_JSON();
if (isset( $_REQUEST['data'] )) {
    $oData = $oJSON->decode( stripslashes( $_REQUEST['data'] ) );
    $sOutput = '';
} else {
    if (! isset( $_POST['form'] )) {
        $aData = urldecode_values( $_POST );
    } else {
        $aData = urldecode_values( $_POST['form'] );
    }
}
G::LoadClass( 'processMap' );
$oProcessMap = new processMap( new DBConnection() );
require_once 'classes/model/Task.php';
require_once 'classes/model/Event.php';
$oEvent = new Event();
$oTask = new Task();
if (isset( $_REQUEST['action'] )) {
    switch ($_REQUEST['action']) {
        case 'load':
            $sOutput = $oProcessMap->load( $oData->uid );
            echo $sOutput;
            break;
        case 'addTask':
            $sOutput = $oProcessMap->addTask( $oData->uid, $oData->position->x, $oData->position->y, $oData->cordinate->x, $oData->cordinate->y );
            echo $sOutput;
            break;
        case 'saveTaskPosition':
            $sOutput = $oProcessMap->saveTaskPosition( $oData->uid, $oData->position->x, $oData->position->y );
            echo $sOutput;
            break;
        case 'saveEventPosition':
            $sOutput = $oProcessMap->saveEventPosition( $oData->uid, $oData->position->x, $oData->position->y );
            echo $sOutput;
            break;
        case 'saveGatewayPosition':
            $sOutput = $oProcessMap->saveGatewayPosition( $oData->uid, $oData->position->x, $oData->position->y );
            //echo $sOutput;
            break;
        case 'saveTaskCordinates':
            $sOutput = $oProcessMap->saveTaskCordinates( $oData->uid, $oData->position->x, $oData->position->y );
            echo $sOutput;
            break;
        case 'saveAnnotationCordinates':
            $sOutput = $oProcessMap->saveAnnotationCordinates( $oData->uid, $oData->position->x, $oData->position->y );
            echo $sOutput;
            break;
        case 'deleteTask':
            $sOutput = $oProcessMap->deleteTask( $oData->tas_uid );
            echo $sOutput;
            break;
        case 'addGateway':
            $sOutput = $oProcessMap->addGateway( $oData );
            echo $sOutput;
            break;
        case 'deleteGateway':
            $sOutput = $oProcessMap->deleteGateway( $oData->pro_uid, $oData->gat_uid );
            echo $sOutput;
            break;
        case 'updateTaskName':
            $aTask['TAS_UID'] = $oData->uid;
            $aTask['TAS_TITLE'] = $oData->label;
            $oTask->update( $aTask );
            break;
        case 'updateTask':
            $aTask['TAS_UID'] = $oData->uid;
            $aTask['TAS_BOUNDARY'] = $oData->boundary;
            $oTask->update( $aTask );
            $sOutput = $oJSON->encode( $oData );
            echo $sOutput;
            break;
        case 'addSubProcess':
            $sOutput = $oProcessMap->addSubProcess( $oData->uid, $oData->position->x, $oData->position->y );
            echo $sOutput;
            break;
        case 'deleteSubProcess':
            $sOutput = $oProcessMap->deleteSubProcess( $oData->pro_uid, $oData->tas_uid );
            break;
        case 'addText':
            $sOutput = $oProcessMap->addText( $oData->uid, $oData->label, $oData->position->x, $oData->position->y, $oData->task_uid );
            echo $sOutput;
            break;
        case 'updateText':
            $sOutput = $oProcessMap->updateText( $oData->uid, $oData->label, $oData->next_uid );
            echo $sOutput;
            break;
        case 'saveTextPosition':
            $sOutput = $oProcessMap->saveTextPosition( $oData->uid, $oData->position->x, $oData->position->y );
            break;
        case 'deleteText':
            $sOutput = $oProcessMap->deleteText( $oData->uid );
            echo $sOutput;
            break;
        case 'getProcesses':
            $sOutput = $oProcessMap->getAllProcesses();
            echo $sOutput;
            break;
        case 'dynaforms':
            $sOutput = $oProcessMap->getDynaformList( $oData->uid );
            $sOutput = $oJSON->encode( $sOutput );
            echo $sOutput;
            break;
        case 'webEntry_validate':
            include (PATH_METHODS . 'processes/processes_webEntryValidate.php');
            break;
        case 'webEntry_generate':
            include (PATH_METHODS . 'processes/processes_webEntryGenerate.php');
            break;
        case 'webEntry':
            $sOutput = $oProcessMap->listNewWebEntry( $oData->uid, $oData->evn_uid );
            echo $sOutput;
            break;
        case 'loadTask':
            $oOutput = $oTask->load( $oData->uid );
            $sOutput = $oJSON->encode( $oOutput );
            echo $sOutput;
            break;
        case 'assignProcessUser':
            $oProcessMap->assignProcessUser( $oData->PRO_UID, $oData->USR_UID );
            break;
        case 'removeProcessUser':
            $oProcessMap->removeProcessUser( $oData->PU_UID );
            break;
        case 'saveInterMessageEvent':
            $aData['TAS_UID'] = $oData->uid;
            $aData['TAS_SEND_LAST_EMAIL'] = strtoupper( $oData->tas_send );
            $aData['TAS_DEF_MESSAGE'] = $oData->data;
            if (isset( $aData['TAS_SEND_LAST_EMAIL'] ) && $aData['TAS_SEND_LAST_EMAIL'] == 'FALSE') {
                $aData['TAS_DEF_MESSAGE'] = '';
            } else {
                $aData['TAS_DEF_MESSAGE'] = str_replace( '@amp@', '&', $aData['TAS_DEF_MESSAGE'] );
            }
            $sOutput = $oTask->update( $aData );
            echo $sOutput;
            break;
        case 'editObjectPermission':
            // we also need the process uid variable for the function.
            $oProcessMap->editObjectPermission( $oData->op_uid, $oData->pro_uid );
            break;
        case 'triggersList':
            $sOutput = $oProcessMap->getTriggers( $oData->pro_uid );
            $sOutput = $oJSON->encode( $sOutput );
            echo $sOutput;
            break;
        case 'loadCategory':
            $sOutput = $oProcessMap->loadProcessCategory();
            $sOutput = $oJSON->encode( $sOutput );
            echo $sOutput;
            break;
        case 'saveProcess':
            $aData['PRO_UID'] = $oData->PRO_UID;
            $aData['PRO_CALENDAR'] = $oData->PRO_CALENDAR;
            $aData['PRO_CATEGORY'] = $oData->PRO_CATEGORY;
            $aData['PRO_DEBUG'] = $oData->PRO_DEBUG;
            $aData['PRO_DESCRIPTION'] = $oData->PRO_DESCRIPTION;
            $aData['PRO_TITLE'] = $oData->PRO_TITLE;
            $sOutput = $oProcessMap->updateProcess( $aData );
            echo $sOutput;
            break;

        case 'saveStartEvent':
            $aData['TAS_UID'] = $oData->tas_uid;
            $aData['TAS_START'] = $oData->tas_start;
            $aData['TAS_EVN_UID'] = '';
            $oTask->update( $aData );
            break;
        case 'deleteStartEvent':
            $aData['TAS_UID'] = $oData->tas_uid;
            $aData['TAS_START'] = $oData->tas_start;
            $aData['TAS_EVN_UID'] = '';
            $oTask->update( $aData );
            if (isset( $oData->evn_uid ))
                $oEvent->remove( $oData->evn_uid );
            break;
        case 'updateEvent':
            $aData['EVN_UID'] = $oData->evn_uid;
            $aData['EVN_TYPE'] = $oData->evn_type;
            $oEvent = EventPeer::retrieveByPK( $aData['EVN_UID'] );
            if (! is_null( $oEvent ))
                $oEvent->update( $aData );
            break;
        case 'saveEvents':
            $sOutput = $oProcessMap->saveExtEvents( $oData );
            echo $sOutput;
            break;
        case 'addEvent':
            $sOutput = $oProcessMap->saveExtddEvents( $oData );
            echo $sOutput;
            /*
          $aData['PRO_UID']  = $oData->uid;
          $aData['EVN_TYPE']  = $oData->tas_type;
          $aData['EVN_STATUS'] = 'ACTIVE';
          $aData['EVN_WHEN'] = '1';
          $aData['EVN_ACTION'] = '';
          if(preg_match("/Inter/", $oData->tas_type)){
            $aData['EVN_RELATED_TO'] = 'MULTIPLE';
            $aData['EVN_TAS_UID_FROM'] = $oData->tas_from;
            $aData['EVN_TAS_UID_TO'] = $oData->tas_to;
            $sOutput =  $oEvent->create($aData);
            echo $sOutput;
          }
          //Code for Start Events only
          if(preg_match("/Start/", $oData->tas_type)){
               $oEvn_uid='';
               $aData['EVN_RELATED_TO'] = 'SINGLE';
               $aData['TAS_UID'] = $oData->tas_uid;
               $oTaskData = $oTask->load($aData['TAS_UID']);
              if($oTaskData['TAS_EVN_UID'] == ''){
                 $oEvn_uid =  $oEvent->create($aData);
              }else{
                 $aData['EVN_UID'] = $oTaskData['TAS_EVN_UID'];
                 $oEvn_uid = $aData['EVN_UID'];
                 $oEvent->update($aData);
              }
              $aTask['TAS_UID'] = $oData->tas_uid;
              $aTask['TAS_EVN_UID'] = $oEvn_uid;
              $aTask['TAS_START'] = 'TRUE';
              $oTask->update($aTask);
          }*/
            break;
        case 'deleteRoute':
            require_once 'classes/model/Route.php';
            $oRoute = new Route();
            $sOutput = $oRoute->remove( $oData->uid );
            echo $sOutput;
            break;
        case 'deleteEvent':
            $sOutput = $oEvent->remove( $oData->uid );
            echo $sOutput;
            break;
        case 'assign':
            G::LoadClass( 'tasks' );
            $oTasks = new Tasks();
            switch ($oData->TU_RELATION) {
                case 1:
                    echo $oTasks->assignUser( $oData->TAS_UID, $oData->USR_UID, $oData->TU_TYPE );
                    break;
                case 2:
                    echo $oTasks->assignGroup( $oData->TAS_UID, $oData->USR_UID, $oData->TU_TYPE );
                    break;
            }
            break;
        case 'ofToAssign':
            G::LoadClass( 'tasks' );
            $oTasks = new Tasks();
            switch ($oData->TU_RELATION) {
                case 1:
                    echo $oTasks->ofToAssignUser( $oData->TAS_UID, $oData->USR_UID, $oData->TU_TYPE );
                    break;
                case 2:
                    echo $oTasks->ofToAssignGroup( $oData->TAS_UID, $oData->USR_UID, $oData->TU_TYPE );
                    break;
            }
            break;

        case 'saveSubprocessDetails':
            //$aTask=$oTask->load($_POST['form']['TASKS']);
            //$aTask=$oTask->load($_POST['form']['PRO_UID']);
            $out = array ();
            $in = array ();

            if (isset( $_POST['VAR_OUT'] ) && $_POST['VAR_OUT'] != '') {
                $varOut = explode( '|', $_POST['VAR_OUT'] );
                $aVarOut1 = G::json_decode( $varOut[0] );
                $aVarOut2 = G::json_decode( $varOut[1] );
                for ($i = 1; $i <= count( $aVarOut1 ); $i ++) {
                    $out[$aVarOut1[$i - 1]] = $aVarOut2[$i - 1];
                }
            }
            if (isset( $_POST['VAR_IN'] ) && $_POST['VAR_IN'] != '') {
                $varIn = explode( '|', $_POST['VAR_IN'] );
                $aVarIn1 = G::json_decode( $varIn[0] );
                $aVarIn2 = G::json_decode( $varIn[1] );
                for ($i = 1; $i <= count( $aVarIn1 ); $i ++) {
                    $in[$aVarIn1[$i - 1]] = $aVarIn2[$i - 1];
                }
            }
            if ($_POST['VAR_IN'] == '')
                $in[$_POST['VAR_IN']] = '';

                //Getting first Tasks of selected process
            $aNewCase = $oProcessMap->subProcess_TaskIni( $_POST['PRO_UID'] );
            $i = 0;
            foreach ($aNewCase as $aRow) {
                if ($i > 0 && $aRow['pro_uid'] == $_POST['sProcessUID']) {
                    $sTASKS = $aRow['uid'];
                }
                $i ++;
            }

            //$aTask=($_POST['TASKS']!=0)?$oTask->load($_POST['TASKS']):0;
            $aTask = ($sTASKS != 0) ? $oTask->load( $sTASKS ) : 0;
            //$aTask['PRO_UID']=0;


            if (isset( $_POST['SP_SYNCHRONOUS'] ) && $_POST['SP_SYNCHRONOUS'] == '') {
                $_POST['SP_SYNCHRONOUS'] = '0';
            }

            if (! isset( $_POST['SP_SYNCHRONOUS'] )) {
                $_POST['SP_SYNCHRONOUS'] = '0';
            }

            require_once 'classes/model/SubProcess.php';
            $oOP = new SubProcess();
            $aData = array ('SP_UID' => $_POST['SP_UID'],            //G::generateUniqueID(),
'PRO_UID' => $aTask['PRO_UID'],'TAS_UID' => $sTASKS,'PRO_PARENT' => $_POST['PRO_PARENT'],'TAS_PARENT' => $_POST['TAS_PARENT'],'SP_TYPE' => 'SIMPLE','SP_SYNCHRONOUS' => $_POST['SP_SYNCHRONOUS'],'SP_SYNCHRONOUS_TYPE' => 'ALL','SP_SYNCHRONOUS_WAIT' => 0,'SP_VARIABLES_OUT' => serialize( $out ),'SP_VARIABLES_IN' => serialize( $in ),'SP_GRID_IN' => ''
            );

            $oOP->update( $aData );
            require_once 'classes/model/Content.php';
            $lang = defined( 'SYS_LANG' ) ? SYS_LANG : 'en';
            //$cont = Content::addContent( 'SP_TITLE', '', $_POST['form']['SP_UID'], $lang, $_POST['form']['SPROCESS_NAME'] );
            $cont = Content::addContent( 'TAS_TITLE', '', $_POST['TAS_PARENT'], $lang, $_POST['SPROCESS_NAME'] );
            break;

        case 'subprocessProperties':
            require_once 'classes/model/Content.php';
            $lang = defined( 'SYS_LANG' ) ? SYS_LANG : 'en';
            //$cont = Content::addContent( 'SP_TITLE', '', $_POST['form']['SP_UID'], $lang, $_POST['form']['SPROCESS_NAME'] );
            $cont = Content::addContent( 'TAS_TITLE', '', $_POST['TAS_PARENT'], $lang, $_POST['SPROCESS_NAME'] );
            break;

        case 'deleteTriggers':
            try {
                require_once ('classes/model/Triggers.php');
                require_once ('classes/model/StepTrigger.php');
                $TRI_UIDS = explode( ',', $_POST['TRI_UID'] );
                foreach ($TRI_UIDS as $i => $TRI_UID) {
                    $oTrigger = new Triggers();
                    $triggerObj = $oTrigger->load( $TRI_UID );
                    $oTrigger->remove( $TRI_UID );

                    $oStepTrigger = new StepTrigger();
                    $oStepTrigger->removeTrigger( $TRI_UID );
                }
                $result->success = true;
                $result->message = G::LoadTranslation( 'ID_TRIGGERS_REMOVED' );
            } catch (Exception $e) {
                $result->success = false;
                $result->message = $e->getMessage();
            }
            print G::json_encode( $result );
            break;

        case 'getOutputDocsTemplates':
            require_once 'classes/model/OutputDocument.php';
            $ooutputDocument = new OutputDocument();
            if (isset( $_GET['OUT_DOC_UID'] )) {
                $rows = $ooutputDocument->load( $_GET['OUT_DOC_UID'] );
                $tmpData = G::json_encode( $rows );
                $tmpData = str_replace( "\\/", "/", '{success:true,data:' . $tmpData . '}' ); // unescape the slashes
                $result = $tmpData;
                echo $result;
                break;

            }

    }
}

