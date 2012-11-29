<?php
/**
 * patterns_Ajax.php
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
//$oJSON = new Services_JSON();
if (isset( $_POST['mode'] ) && $_POST['mode'] != '') {
    $aData = $_POST;
} else {
    $aData = $_POST['form'];
}

G::LoadClass( 'tasks' );
$oTasks = new Tasks();
$rou_id = 0;
switch ($aData['action']) {
    case 'savePattern':
        //if ($aData['ROU_TYPE'] != $aData['ROU_TYPE_OLD'])
        //{
        $oTasks->deleteAllRoutesOfTask( $aData['PROCESS'], $aData['TASK'] );
        //}
        require_once 'classes/model/Route.php';
        $oRoute = new Route();
        switch ($aData['ROU_TYPE']) {
            case 'SEQUENTIAL':
            case 'SEC-JOIN':
        /*if ($aData['ROU_UID'] != '')
        {
	  	    $aFields['ROU_UID'] = $aData['ROU_UID'];
	  	  }*/
	  	  $aFields['PRO_UID'] = $aData['PROCESS'];
                $aFields['TAS_UID'] = $aData['TASK'];
                $aFields['ROU_NEXT_TASK'] = $aData['ROU_NEXT_TASK'];
                $aFields['ROU_TYPE'] = $aData['ROU_TYPE'];
                //$aFields['ROU_TO_LAST_USER'] = $aData['ROU_TO_LAST_USER'];
                $rou_id = $oRoute->create( $aFields );
                break;
            case 'SELECT':
                foreach ($aData['GRID_SELECT_TYPE'] as $iKey => $aRow) {
                    /*if ($aRow['ROU_UID'] != '')
          {
	  	      $aFields['ROU_UID'] = $aRow['ROU_UID'];
	  	    }*/
                    $aFields['PRO_UID'] = $aData['PROCESS'];
                    $aFields['TAS_UID'] = $aData['TASK'];
                    $aFields['ROU_NEXT_TASK'] = $aRow['ROU_NEXT_TASK'];
                    $aFields['ROU_CASE'] = $iKey;
                    $aFields['ROU_TYPE'] = $aData['ROU_TYPE'];
                    $aFields['ROU_CONDITION'] = $aRow['ROU_CONDITION'];
                    //$aFields['ROU_TO_LAST_USER'] = $aRow['ROU_TO_LAST_USER'];
                    $rou_id = $oRoute->create( $aFields );
                    unset( $aFields );
                }
                break;
            case 'EVALUATE':
                foreach ($aData['GRID_EVALUATE_TYPE'] as $iKey => $aRow) {
                    /*if ($aRow['ROU_UID'] != '')
          {
	  	      $aFields['ROU_UID'] = $aRow['ROU_UID'];
	  	    }*/
                    $aFields['PRO_UID'] = $aData['PROCESS'];
                    $aFields['TAS_UID'] = $aData['TASK'];
                    $aFields['ROU_NEXT_TASK'] = $aRow['ROU_NEXT_TASK'];
                    $aFields['ROU_CASE'] = $iKey;
                    $aFields['ROU_TYPE'] = $aData['ROU_TYPE'];
                    $aFields['ROU_CONDITION'] = $aRow['ROU_CONDITION'];
                    //$aFields['ROU_TO_LAST_USER'] = $aRow['ROU_TO_LAST_USER'];
                    $rou_id = $oRoute->create( $aFields );
                    unset( $aFields );
                }
                break;
            case 'PARALLEL':
                foreach ($aData['GRID_PARALLEL_TYPE'] as $iKey => $aRow) {
                    /*if ($aRow['ROU_UID'] != '')
          {
	  	      $aFields['ROU_UID'] = $aRow['ROU_UID'];
	  	    }*/
                    $aFields['PRO_UID'] = $aData['PROCESS'];
                    $aFields['TAS_UID'] = $aData['TASK'];
                    $aFields['ROU_NEXT_TASK'] = $aRow['ROU_NEXT_TASK'];
                    $aFields['ROU_CASE'] = $iKey;
                    $aFields['ROU_TYPE'] = $aData['ROU_TYPE'];
                    $rou_id = $oRoute->create( $aFields );
                    unset( $aFields );
                }
                break;
            case 'PARALLEL-BY-EVALUATION':
                foreach ($aData['GRID_PARALLEL_EVALUATION_TYPE'] as $iKey => $aRow) {
                    /*if ($aRow['ROU_UID'] != '')
          {
	  	      $aFields['ROU_UID'] = $aRow['ROU_UID'];
	  	    }*/

                    $aFields['PRO_UID'] = $aData['PROCESS'];
                    $aFields['TAS_UID'] = $aData['TASK'];
                    $aFields['ROU_NEXT_TASK'] = $aRow['ROU_NEXT_TASK'];
                    $aFields['ROU_CASE'] = $iKey;
                    $aFields['ROU_TYPE'] = $aData['ROU_TYPE'];
                    $aFields['ROU_CONDITION'] = $aRow['ROU_CONDITION'];
                    if (isset( $aRow['ROU_OPTIONAL'] ) && trim( $aRow['ROU_OPTIONAL'] ) != '' && ($aRow['ROU_OPTIONAL'] === 'TRUE' || $aRow['ROU_OPTIONAL'] === 'FALSE'))
                        $aFields['ROU_OPTIONAL'] = $aRow['ROU_OPTIONAL'];
                    $rou_id = $oRoute->create( $aFields );
                    unset( $aFields );
                }
                break;
            case 'DISCRIMINATOR': //Girish ->Added to save changes, while editing the route
                foreach ($aData['GRID_DISCRIMINATOR_TYPE'] as $iKey => $aRow) {
                    $aFields['PRO_UID'] = $aData['PROCESS'];
                    $aFields['TAS_UID'] = $aData['TASK'];
                    $aFields['ROU_NEXT_TASK'] = $aRow['ROU_NEXT_TASK'];
                    $aFields['ROU_CASE'] = $iKey;
                    $aFields['ROU_TYPE'] = $aData['ROU_TYPE'];
                    $aFields['ROU_CONDITION'] = $aRow['ROU_CONDITION'];
                    $aFields['ROU_OPTIONAL'] = $aRow['ROU_OPTIONAL'];
                    $routeData = $oTasks->getRouteByType( $aData['PROCESS'], $aRow['ROU_NEXT_TASK'], $aData['ROU_TYPE'] );
                    foreach ($routeData as $route) {
                        $sFields['ROU_UID'] = $route['ROU_UID'];
                        $sFields['ROU_CONDITION'] = $aRow['ROU_CONDITION'];
                        $sFields['ROU_OPTIONAL'] = $aRow['ROU_OPTIONAL'];
                        $rou_id = $oRoute->update( $sFields );
                    }
                    $rou_id = $oRoute->create( $aFields );
                    unset( $aFields );
                }
                break;
        }
        echo $rou_id;
}
?>