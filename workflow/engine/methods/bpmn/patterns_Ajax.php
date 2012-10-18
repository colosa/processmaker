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
G::LoadClass( 'processMap' );
$oJSON = new Services_JSON();
if (isset( $_POST['mode'] ) && $_POST['mode'] != '') {
    $aData = $_POST;
    $aData['TASK'] = $oJSON->decode( $_POST['TASK'] );
    $aData['ROU_NEXT_TASK'] = $oJSON->decode( $_POST['ROU_NEXT_TASK'] );
}

//Saving Gateway details into Gateway table
if ($aData['ROU_TYPE'] != 'SEQUENTIAL') {
    $oProcessMap = new processMap();
    //$sGatewayUID = $oProcessMap->saveNewGateway($aData['PROCESS'], $aData['TASK'][0], $aData['ROU_NEXT_TASK'][0]);
    require_once 'classes/model/Gateway.php';
    $oGateway = new Gateway();

    $aGatewayFields = array ();
    $aGatewayFields['GAT_UID'] = $aData['GAT_UID'];
    $aGatewayFields['TAS_UID'] = $aData['TASK'][0];
    $aGatewayFields['GAT_NEXT_TASK'] = $aData['ROU_NEXT_TASK'][0];
    $aGatewayFields['GAT_TYPE'] = '';
    $oGateway->update( $aGatewayFields );
    //$sGatewayUID   = $oProcessMap->saveNewGateway($aData['PROCESS'], $aData['TASK'][0], $aData['ROU_NEXT_TASK'][0]);
    //echo $sGatewayUID.'|';
}
//else
//  echo $aData['ROU_EVN_UID'].'|';   //sending route_event_uid in response


G::LoadClass( 'tasks' );
$oTasks = new Tasks();
$rou_id = 0;
$aFields['GAT_UID'] = $aData['GAT_UID'];
switch ($aData['action']) {
    case 'savePattern':
        foreach ($aData['TASK'] as $iKey => $aRow) {
            $oTasks->deleteAllRoutesOfTask( $aData['PROCESS'], $aRow );
        }
        require_once 'classes/model/Route.php';
        require_once 'classes/model/Event.php';
        $oEvent = new Event();
        $oRoute = new Route();
        switch ($aData['ROU_TYPE']) {
            case 'SEQUENTIAL':
            case 'SEC-JOIN':
                $aFields['PRO_UID'] = $aData['PROCESS'];
                $aFields['TAS_UID'] = $aData['TASK'][0];
                $aFields['ROU_NEXT_TASK'] = $aData['ROU_NEXT_TASK'][0];
                $aFields['ROU_TYPE'] = $aData['ROU_TYPE'];
                if (isset( $aData['ROU_EVN_UID'] ) && $aData['ROU_EVN_UID'] != '') {
                    $aFields['ROU_EVN_UID'] = $aData['ROU_EVN_UID'];
                    $aDataEvent = array ();
                    $aDataEvent['EVN_UID'] = $aData['ROU_EVN_UID'];
                    $aDataEvent['EVN_TAS_UID_FROM'] = $aData['TASK'][0];
                    $aDataEvent['EVN_RELATED_TO'] = 'MULTIPLE';
                    $oEvent->update( $aDataEvent );
                }
                if (isset( $aData['PORT_NUMBER_IP'] ))
                    $aFields['ROU_TO_PORT'] = $aData['PORT_NUMBER_IP'];
                if (isset( $aData['PORT_NUMBER_OP'] ))
                    $aFields['ROU_FROM_PORT'] = $aData['PORT_NUMBER_OP'];
                $rou_id = $oRoute->create( $aFields );
                break;
            case 'SELECT':
                foreach ($aData['GRID_SELECT_TYPE'] as $iKey => $aRow) {
                    $aFields['PRO_UID'] = $aData['PROCESS'];
                    $aFields['TAS_UID'] = $aData['TASK'];
                    $aFields['ROU_NEXT_TASK'] = $aRow;
                    $aFields['ROU_CASE'] = $iKey;
                    $aFields['ROU_TYPE'] = $aData['ROU_TYPE'];
                    if (isset( $aData['PORT_NUMBER_IP'] ))
                        $aFields['ROU_TO_PORT'] = $aData['PORT_NUMBER_IP'];
                    if (isset( $aData['PORT_NUMBER_OP'] ))
                        $aFields['ROU_FROM_PORT'] = $aData['PORT_NUMBER_OP'];
                    $rou_id = $oRoute->create( $aFields );
                    unset( $aFields );
                }
                break;
            case 'EVALUATE':
                foreach ($aData['ROU_NEXT_TASK'] as $iKey => $aRow) {
                    $aFields['PRO_UID'] = $aData['PROCESS'];
                    $aFields['TAS_UID'] = $aData['TASK'][0];
                    $aFields['ROU_NEXT_TASK'] = $aRow;
                    $aFields['ROU_CASE'] = $iKey;
                    $aFields['ROU_TYPE'] = $aData['ROU_TYPE'];
                    $aFields['ROU_CONDITION'] = $aRow['ROU_CONDITION'];
                    $aFields['GAT_UID'] = $aData['GAT_UID'];

                    if (isset( $aData['PORT_NUMBER_IP'] ))
                        $aFields['ROU_TO_PORT'] = $aData['PORT_NUMBER_IP'];
                    if (isset( $aData['PORT_NUMBER_OP'] ))
                        $aFields['ROU_FROM_PORT'] = $aData['PORT_NUMBER_OP'];
                        //$aFields['ROU_TO_LAST_USER'] = $aRow['ROU_TO_LAST_USER'];
                    $rou_id = $oRoute->create( $aFields );
                    unset( $aFields );
                }
                break;
            case 'PARALLEL':
                foreach ($aData['ROU_NEXT_TASK'] as $iKey => $aRow) {
                    $aFields['PRO_UID'] = $aData['PROCESS'];
                    $aFields['TAS_UID'] = $aData['TASK'][0];
                    $aFields['ROU_NEXT_TASK'] = $aRow;
                    $aFields['ROU_CASE'] = $iKey;
                    $aFields['ROU_TYPE'] = $aData['ROU_TYPE'];
                    $aFields['GAT_UID'] = $aData['GAT_UID'];
                    ;

                    if (isset( $aData['PORT_NUMBER_IP'] ))
                        $aFields['ROU_TO_PORT'] = $aData['PORT_NUMBER_IP'];
                    if (isset( $aData['PORT_NUMBER_OP'] ))
                        $aFields['ROU_FROM_PORT'] = $aData['PORT_NUMBER_OP'];

                    $rou_id = $oRoute->create( $aFields );
                    unset( $aFields );
                }
                break;
            case 'PARALLEL-BY-EVALUATION':
                foreach ($aData['ROU_NEXT_TASK'] as $iKey => $aRow) {
                    $aFields['PRO_UID'] = $aData['PROCESS'];
                    $aFields['TAS_UID'] = $aData['TASK'][0];
                    $aFields['ROU_NEXT_TASK'] = $aRow;
                    $aFields['ROU_CASE'] = $iKey;
                    $aFields['ROU_TYPE'] = $aData['ROU_TYPE'];
                    $aFields['GAT_UID'] = $aData['GAT_UID'];
                    ;

                    if (isset( $aData['PORT_NUMBER_IP'] ))
                        $aFields['ROU_TO_PORT'] = $aData['PORT_NUMBER_IP'];
                    if (isset( $aData['PORT_NUMBER_OP'] ))
                        $aFields['ROU_FROM_PORT'] = $aData['PORT_NUMBER_OP'];
                    $rou_id = $oRoute->create( $aFields );
                    unset( $aFields );
                }
                break;
            case 'DISCRIMINATOR': //Girish ->Added to save changes, while editing the route
                foreach ($aData['TASK'] as $iKey => $aRow) {
                    $aFields['PRO_UID'] = $aData['PROCESS'];
                    $aFields['TAS_UID'] = $aRow;
                    $aFields['ROU_NEXT_TASK'] = $aData['ROU_NEXT_TASK'][0];
                    $aFields['ROU_CASE'] = $iKey;
                    $aFields['ROU_TYPE'] = $aData['ROU_TYPE'];
                    $aFields['GAT_UID'] = $aData['GAT_UID'];
                    ;

                    if (isset( $aData['PORT_NUMBER_IP'] ))
                        $aFields['ROU_TO_PORT'] = $aData['PORT_NUMBER_IP'];
                    if (isset( $aData['PORT_NUMBER_OP'] ))
                        $aFields['ROU_FROM_PORT'] = $aData['PORT_NUMBER_OP'];
                    $routeData = $oTasks->getRouteByType( $aData['PROCESS'], $aData['ROU_NEXT_TASK'][0], $aData['ROU_TYPE'] );
                    foreach ($routeData as $route) {
                        $sFields['ROU_UID'] = $route['ROU_UID'];
                        $rou_id = $oRoute->update( $sFields );
                    }
                    $rou_id = $oRoute->create( $aFields );
                    unset( $aFields );
                }
                break;
        }
        echo $rou_id;
}

