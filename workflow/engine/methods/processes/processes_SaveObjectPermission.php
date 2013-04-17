<?php
/**
 * processes_Save.php
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
global $RBAC;
$access = $RBAC->userCanAccess( 'PM_FACTORY' );
if ($access != 1) {
    switch ($access) {
        case - 1:
            G::SendTemporalMessage( 'ID_USER_HAVENT_RIGHTS_PAGE', 'error', 'labels' );
            G::header( 'location: ../login/login' );
            die();
            break;
        case - 2:
            G::SendTemporalMessage( 'ID_USER_HAVENT_RIGHTS_SYSTEM', 'error', 'labels' );
            G::header( 'location: ../login/login' );
            die();
            break;
        default:
            G::SendTemporalMessage( 'ID_USER_HAVENT_RIGHTS_PAGE', 'error', 'labels' );
            G::header( 'location: ../login/login' );
            die();
            break;
    }
}
if (isset( $_POST['form'] )) {
    $sValue = $_POST['form']; //For old processmap
} else {
    $sValue = $_POST; //For new processmap EXtjs
}


list ($iRelation, $sUserGroup) = explode( '|', $sValue['GROUP_USER'] );
$sObjectUID = '';
switch ($sValue['OP_OBJ_TYPE']) {
    case 'ANY':
  /*case 'ANY_DYNAFORM':
  case 'ANY_INPUT':
  case 'ANY_OUTPUT':*/
    $sObjectUID = '';
        break;
    case 'DYNAFORM':
        $sObjectUID = $sValue['DYNAFORMS'];
        break;
    case 'INPUT':
        $sObjectUID = $sValue['INPUTS'];
        break;
    case 'OUTPUT':
        $sObjectUID = $sValue['OUTPUTS'];
        break;
    case 'MSGS_HISTORY':
        $sObjectUID = $sValue['MSGS_HISTORY'];
        break;
}
require_once 'classes/model/ObjectPermission.php';
$oOP = new ObjectPermission();
$aData = array ('OP_UID' => G::generateUniqueID(),'PRO_UID' => $sValue['PRO_UID'],'TAS_UID' => $sValue['TAS_UID'],'USR_UID' => (string) $sUserGroup,'OP_USER_RELATION' => $iRelation,'OP_TASK_SOURCE' => $sValue['OP_TASK_SOURCE'],'OP_PARTICIPATE' => $sValue['OP_PARTICIPATE'],'OP_OBJ_TYPE' => $sValue['OP_OBJ_TYPE'],'OP_OBJ_UID' => $sObjectUID,'OP_ACTION' => $sValue['OP_ACTION'],'OP_CASE_STATUS' => $sValue['OP_CASE_STATUS']);
$oOP->fromArray( $aData, BasePeer::TYPE_FIELDNAME );
$oOP->save();
G::LoadClass( 'processMap' );
$oProcessMap = new ProcessMap();
$oProcessMap->getObjectsPermissionsCriteria( $sValue['PRO_UID'] );
