<?php
 /**
 * saveProcess.php
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
 
try{
  G::LoadClass('xpdl');
  G::LoadClass('processMap');
  G::LoadClass('tasks');
  $oProcessMap = new processMap(new DBConnection);
  $oProcess = new Xpdl();
  $oTask    = new Task();
  if(isset($_POST['PRO_UID'])){
    $idProcess = $_POST['PRO_UID'];
      if($oProcess->processExists ($idProcess)){
        $process['ID'] = $idProcess;
      }
      else{
        $result = array(); 
        $result['status_code'] = $idProcess;
        $result['message']     = "the process does not exist";
        $result['timestamp']   = date('Y-m-d H:i:s');
        echo G::json_encode($result);
        die;
      }
  }
  else{
    $result = array(); 
    $result['status_code'] = 1;
    $result['message']     = "you don't send the process uid";
    $result['timestamp']   = date('Y-m-d H:i:s');
    echo G::json_encode($result);
    die;
  }
  $aTasks                 = G::json_decode ( $_POST['tasks'] );
  $aRoutes                = G::json_decode ( $_POST['routes'] );
  $aEvents                = G::json_decode ( $_POST['events'] );
  $aGateways              = G::json_decode ( $_POST['gateways'] );
  $aAnnotations           = G::json_decode ( $_POST['annotations'] );
  $aSubprocess            = G::json_decode ( $_POST['subprocess'] );
  $fields                 = $oProcess->serializeProcess($idProcess);
  $oData                  = unserialize($fields);
  $aRoutes                = $oProcess->super_unique($aRoutes);
  $sOutput                = '';
  $subProcesses           = array();
  foreach($aTasks as $endBase => $valueEnd){
    $tasks['ID_TASK'] = $valueEnd['0'];
    $tasks['START']   = '';
    foreach($aEvents as $id => $value){
      if($value['1'] == 'bpmnEventEmptyStart' or $value['1'] == 'bpmnEventMessageStart' or $value['1'] == 'bpmnEventTimerStart' ){
        foreach($aRoutes as $endR => $valueR){
          if($tasks['ID_TASK'] == $valueR['2'] and $valueR['1'] == $value['0'] ){
            $tasks['START'] = 'TRUE';
            unset($aEvents[$id]);
            unset($aRoutes[$endR]);
          }
        }
      }
    }
    if( $tasks['START'] == ''){
      $tasks['START'] = 'FALSE';
    }   
    $tasks['TAS_TITLE']  = $valueEnd['1'];
    $tasks['X']          = $valueEnd['2'];
    $tasks['Y']          = $valueEnd['3'];
    $tasks['TAS_TYPE']   = $valueEnd['6'];
    $tasks['ID_PROCESS'] = $idProcess;
    //$tasks['TASK_TYPE']  = 'NORMAL';
    $tasks['DESCRIPTION']= '';
        $oData->tasks[]= $oProcess->createDataTask($tasks);
  }
  $endArray = array();
  $endArray           = $oProcess->convertArrayEnd($aEvents,$idProcess);
  $oData->lanes       = $oProcess->createLanesPM($aAnnotations,$idProcess);
  $transitions        = $oProcess->createArrayRoutes($oData->tasks,$aRoutes,$aEvents,$aGateways,$aEvents,$idProcess);
  $oData->routes      = $transitions['ROUTES'];
  $routeTransitions   = $transitions['TRANSITIONS'];
  $numberRoutes       = $transitions['NUMBER'];
  $oData->tasks       = $transitions['TASKS'];
  $taskHidden         = $transitions['HIDDEN'];
  foreach($aSubprocess as $key => $value){
    //print_R($value['0']);
    //$sOutput = $oTask->remove($value['0']);
    $sOutput = $oProcessMap->addSubProcess($idProcess,$value['2'],$value['3']);
    /*$subProcess[$key]['ID_PROCESS']  = $idProcess;
    $subProcess[$key]['TAS_TITLE']   = $idProcess;
    $subProcess[$key]['ID_TASK']     = $value['0'];
    $subProcess[$key]['TAS_UID']     = $value['0'];
    $subProcess[$key]['TASK_PARENT'] = '';
    $subProcess[$key]['PROCESS_PARENT'] = '';
    $subProcess[$key]['TASK_TYPE']   = 'SUBPROCESS';
    $subProcess[$key]['DESCRIPTION'] = '';
    $subProcess[$key]['X']           = $value['2'];
    $subProcess[$key]['Y']           = $value['3'];    
    
    print_R($subProcess[$key]);
    $subProcesses[] = $oProcess->createSubProcess($subProcess);
    $oData->tasks[] = $oProcess->createTask($subProcess[$key]);*/
  }
  $numberTransitions  = sizeof($routeTransitions);
  if($numberTransitions > 0){
    $routesArray   = $oProcess->createGateways($routeTransitions,$endArray,$oData->routes,$numberRoutes,$idProcess,$taskHidden);
    $oData->routes = $routesArray;
  }
  
  $oProcess->updateProcessFromDataXpdl($oData,$oData->tasks);

  $result->success = true;
  $result->msg = G::LoadTranslation('ID_PROCESS_SAVE_SUCCESS');

} catch (Exception $e) {
  $result->success = false;
  $result->msg = $e->getMessage();
}

print G::json_encode($result);
