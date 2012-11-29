<?php
/**
 * class.processTemplate.php
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

  class processTemplateClass extends PMPlugin  {

    function __construct (  ) {
    }

    function getNewProcessTemplateList ( $oData  )  {
      global $_DBArray;
      $rows[] = array ( 'uid' => 'char', 'name' => 'char',  );
      $rows[] = array ( 'uid' => '', 'name' => 'blank process' );
      $rows[] = array ( 'uid' => 1, 'name' => 'simple process, three tasks' );
      $rows[] = array ( 'uid' => 2, 'name' => 'simple parallel process' );
      $rows[] = array ( 'uid' => 3, 'name' => 'conditional process' );
      $rows[] = array ( 'uid' => 4, 'name' => 'double starting task' );
      $rows[] = array ( 'uid' => 5, 'name' => 'advanced parallel process' );
      
      $_DBArray['ProcessesNew'] = $rows;
    }
  
    function saveNewProcess ( $oData  )  {
      
      switch ($oData['PRO_TEMPLATE']) {
        case 1 : $this->simpleProcess ( $oData);
                 break;
        case 2 : $this->simpleParallel ( $oData);
                 break;
        case 3 : $this->conditional ( $oData);
                 break;
        case 4 : $this->doubleStart ( $oData);
                 break;
        case 5 : $this->fullParallel ( $oData);
                 break;
        default :
                 
      }
   	  
      
    }

    function simpleProcess ($oData  ) {
   	  //$oJSON  = new Services_JSON();
      $sProUid     = $oData['PRO_UID'];
      $sTemplate   = $oData['PRO_TEMPLATE'];
      $oProcessMap = $oData['PROCESSMAP'];
      
      $t1 = Bootstrap::json_decode( $oProcessMap->addTask( $sProUid, 300, 70)  );
      $t2 = Bootstrap::json_decode( $oProcessMap->addTask( $sProUid, 300, 160) );
      $t3 = Bootstrap::json_decode( $oProcessMap->addTask( $sProUid, 300, 250) );
      $task1 = $t1->uid;
      $task2 = $t2->uid;
      $task3 = $t3->uid;
    
      $aData = array("TAS_START"=>"TRUE","TAS_UID"=>$task1);
   	  $oTask = new Task();
 	    $oTask->update($aData);
    
      $oProcessMap->saveNewPattern($sProUid, $task1, $task2, 'SEQUENTIAL' );
      $oProcessMap->saveNewPattern($sProUid, $task2, $task3, 'SEQUENTIAL' );
      $oProcessMap->saveNewPattern($sProUid, $task3, -1, 'SEQUENTIAL' );
    }

    function simpleParallel ($oData  ) {
   	  //$oJSON  = new Services_JSON();
      $sProUid     = $oData['PRO_UID'];
      $sTemplate   = $oData['PRO_TEMPLATE'];
      $oProcessMap = $oData['PROCESSMAP'];
      
      $t1 = Bootstrap::json_decode( $oProcessMap->addTask( $sProUid, 300, 70)  );
      $t2 = Bootstrap::json_decode( $oProcessMap->addTask( $sProUid, 200, 160) );
      $t3 = Bootstrap::json_decode( $oProcessMap->addTask( $sProUid, 400, 160) );
      $t5 = Bootstrap::json_decode( $oProcessMap->addTask( $sProUid, 300, 250) );
    
      $aData = array("TAS_START"=>"TRUE","TAS_UID"=>$t1->uid);
   	  $oTask = new Task();
 	    $oTask->update($aData);
    
      $oProcessMap->saveNewPattern($sProUid, $t1->uid, $t2->uid, 'PARALLEL' );
      $oProcessMap->saveNewPattern($sProUid, $t1->uid, $t3->uid, 'PARALLEL' );
      $oProcessMap->saveNewPattern($sProUid, $t2->uid, $t5->uid, 'SEC-JOIN' );
      $oProcessMap->saveNewPattern($sProUid, $t3->uid, $t5->uid, 'SEC-JOIN' );
      $oProcessMap->saveNewPattern($sProUid, $t5->uid, -1, 'SEQUENTIAL' );
    }
    
    function fullParallel ($oData  ) {
   	  //$oJSON  = new Services_JSON();
      $sProUid     = $oData['PRO_UID'];
      $sTemplate   = $oData['PRO_TEMPLATE'];
      $oProcessMap = $oData['PROCESSMAP'];
      
      $t1 = Bootstrap::json_decode( $oProcessMap->addTask( $sProUid, 300, 70)  );
      $t2 = Bootstrap::json_decode( $oProcessMap->addTask( $sProUid, 100, 160) );
      $t3 = Bootstrap::json_decode( $oProcessMap->addTask( $sProUid, 300, 160) );
      $t4 = Bootstrap::json_decode( $oProcessMap->addTask( $sProUid, 500, 160) );
      $t5 = Bootstrap::json_decode( $oProcessMap->addTask( $sProUid, 200, 250) );
      $t6 = Bootstrap::json_decode( $oProcessMap->addTask( $sProUid, 500, 250) );
      $t7 = Bootstrap::json_decode( $oProcessMap->addTask( $sProUid, 350, 340) );
    
      $aData = array("TAS_START"=>"TRUE","TAS_UID"=>$t1->uid);
   	  $oTask = new Task();
 	    $oTask->update($aData);
    
      $oProcessMap->saveNewPattern($sProUid, $t1->uid, $t2->uid, 'PARALLEL' );
      $oProcessMap->saveNewPattern($sProUid, $t1->uid, $t3->uid, 'PARALLEL' );
      $oProcessMap->saveNewPattern($sProUid, $t1->uid, $t4->uid, 'PARALLEL' );
      $oProcessMap->saveNewPattern($sProUid, $t2->uid, $t5->uid, 'SEC-JOIN' );
      $oProcessMap->saveNewPattern($sProUid, $t3->uid, $t5->uid, 'SEC-JOIN' );
      $oProcessMap->saveNewPattern($sProUid, $t4->uid, $t6->uid, 'SEQUENTIAL' );
      $oProcessMap->saveNewPattern($sProUid, $t5->uid, $t7->uid, 'SEC-JOIN' );
      $oProcessMap->saveNewPattern($sProUid, $t6->uid, $t7->uid, 'SEC-JOIN' );
      $oProcessMap->saveNewPattern($sProUid, $t7->uid, -1, 'SEQUENTIAL' );
    }
    
    
    function conditional ($oData  ) {
   	  //$oJSON  = new Services_JSON();
      $sProUid     = $oData['PRO_UID'];
      $sTemplate   = $oData['PRO_TEMPLATE'];
      $oProcessMap = $oData['PROCESSMAP'];
      
      $t1 = Bootstrap::json_decode( $oProcessMap->addTask( $sProUid, 300, 70)  );
      $t2 = Bootstrap::json_decode( $oProcessMap->addTask( $sProUid, 200, 160) );
      $t3 = Bootstrap::json_decode( $oProcessMap->addTask( $sProUid, 400, 160) );
      $t4 = Bootstrap::json_decode( $oProcessMap->addTask( $sProUid, 300, 250) );
      $task1 = $t1->uid;
      $task2 = $t2->uid;
      $task3 = $t3->uid;
      $task4 = $t4->uid;
      
      $aData = array("TAS_START"=>"TRUE","TAS_UID"=>$task1);
   	  $oTask = new Task();
   	  $oTask->update($aData);
      
      $oProcessMap->saveNewPattern($sProUid, $task1, $task2, 'SELECT' );
      $oProcessMap->saveNewPattern($sProUid, $task1, $task3, 'SELECT' );
      $oProcessMap->saveNewPattern($sProUid, $task2, $task4, 'SEQUENTIAL' );
      $oProcessMap->saveNewPattern($sProUid, $task3, $task4, 'SEQUENTIAL' );
      $oProcessMap->saveNewPattern($sProUid, $task4, -1, 'SEQUENTIAL' );
    }
    
    
    function doubleStart ($oData  ) {
   	  //$oJSON  = new Services_JSON();
      $sProUid     = $oData['PRO_UID'];
      $sTemplate   = $oData['PRO_TEMPLATE'];
      $oProcessMap = $oData['PROCESSMAP'];
      
      $t1 = Bootstrap::json_decode( $oProcessMap->addTask( $sProUid, 200, 70)  );
      $t2 = Bootstrap::json_decode( $oProcessMap->addTask( $sProUid, 400, 70) );
      $t3 = Bootstrap::json_decode( $oProcessMap->addTask( $sProUid, 300, 160) );
      $task1 = $t1->uid;
      $task2 = $t2->uid;
      $task3 = $t3->uid;
      
      $aData = array("TAS_START"=>"TRUE","TAS_UID"=>$task1);
   	  $oTask = new Task();
   	  $oTask->update($aData);

      $aData = array("TAS_START"=>"TRUE","TAS_UID"=>$task2);
   	  $oTask = new Task();
   	  $oTask->update($aData);
      
      $oProcessMap->saveNewPattern($sProUid, $task1, $task3, 'SEQUENTIAL' );
      $oProcessMap->saveNewPattern($sProUid, $task2, $task3, 'SEQUENTIAL' );
      $oProcessMap->saveNewPattern($sProUid, $task3, -1, 'SEQUENTIAL' );
    }
    
    
    function setup()
    {
    }
  }