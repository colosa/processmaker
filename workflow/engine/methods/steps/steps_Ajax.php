<?php
/**
 * steps_Ajax.php
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
if (!isset($_SESSION['USER_LOGGED'])) {
    G::SendTemporalMessage('ID_LOGIN_AGAIN', 'warning', 'labels');
    die('<script type="text/javascript">parent.location = "../login/login";</script>');
}

try {
    global $RBAC;
    switch ($RBAC->userCanAccess( 'PM_FACTORY' )) {
        case - 2:
            G::SendTemporalMessage( 'ID_USER_HAVENT_RIGHTS_SYSTEM', 'error', 'labels' );
            G::header( 'location: ../login/login' );
            die();
            break;
        case - 1:
            G::SendTemporalMessage( 'ID_USER_HAVENT_RIGHTS_PAGE', 'error', 'labels' );
            G::header( 'location: ../login/login' );
            die();
            break;
    }

    require_once 'classes/model/StepTrigger.php';
    require_once 'classes/model/Triggers.php';
    G::LoadInclude( 'ajax' );
    if (! empty( $_GET )) {
        if (! isset( $_GET['form'] )) {
            $aData = urldecode_values( $_GET );
        } else {
            $aData = urldecode_values( $_GET['form'] );
        }
    } else {
        if (! isset( $_POST['form'] )) {
            $aData = urldecode_values( $_POST );
        } else {
            $aData = urldecode_values( $_POST['form'] );
        }
    }
    switch ($aData['action']) {
        case 'showTriggers':
            G::LoadClass( 'processMap' );
            $oProcessMap = new ProcessMap();
            global $G_PUBLISH;
            $G_PUBLISH = new Publisher();
            if ($aData['sType'] == 'BEFORE') {
                $G_PUBLISH->AddContent( 'propeltable', 'paged-table', 'steps/triggersBefore_List', $oProcessMap->getStepTriggersCriteria( $aData['sStep'], $_SESSION['TASK'], $aData['sType'] ), array ('STEP' => $aData['sStep']) );
            } else {
                $G_PUBLISH->AddContent( 'propeltable', 'paged-table', 'steps/triggersAfter_List',  $oProcessMap->getStepTriggersCriteria( $aData['sStep'], $_SESSION['TASK'], $aData['sType'] ), array ('STEP' => $aData['sStep']) );
            }
            G::RenderPage( 'publish-twocolumns', 'raw' );
            break;
        case 'availableTriggers':
            $oCriteria = new Criteria( 'workflow' );
            $oCriteria->addSelectColumn( 'TRI_UID' );
            $oCriteria->add( StepTriggerPeer::TAS_UID, $_SESSION['TASK'] );
            $oCriteria->add( StepTriggerPeer::STEP_UID, $aData['sStep'] );
            $oCriteria->add( StepTriggerPeer::ST_TYPE, $aData['sType'] );
            $oDataset = StepTriggerPeer::doSelectRS( $oCriteria );
            $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
            $oDataset->next();
            $sUIDs = "'0'";
            $aUIDs = array ();
            while ($aRow = $oDataset->getRow()) {
                $sUIDs .= ",'" . $aRow['TRI_UID'] . "'";
                $aUIDs[] = $aRow['TRI_UID'];
                $oDataset->next();
            }
            $oCriteria = new Criteria( 'workflow' );
            $oCriteria->addSelectColumn( 'COUNT(TRI_UID) AS CANTITY' );
            $oCriteria->add( TriggersPeer::TRI_UID, $aUIDs, Criteria::NOT_IN );
            $oCriteria->add( TriggersPeer::PRO_UID, $aData['sProcess'] );
            $oDataset = TriggersPeer::doSelectRS( $oCriteria );
            $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
            $oDataset->next();
            $aRow = $oDataset->getRow();
            if ((int) $aRow['CANTITY'] > 0) {
                $aFields['LANG'] = SYS_LANG;
                $aFields['UIDS'] = $sUIDs;
                $aFields['PROCESS'] = $aData['sProcess'];
                $aFields['action'] = 'assignTrigger';
                $aFields['STEP_UID'] = $aData['sStep'];
                $aFields['ST_TYPE'] = $aData['sType'];
                global $G_PUBLISH;
                G::LoadClass( 'xmlfield_InputPM' );
                $G_PUBLISH = new Publisher();
                $G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'steps/triggers_Assign', '', $aFields, '../steps/steps_Ajax' );
                G::RenderPage( 'publish', 'raw' );
            } else {
                global $G_PUBLISH;
                $G_PUBLISH = new Publisher();
                $G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'steps/triggers_NoAssign', '' );
                G::RenderPage( 'publish', 'raw' );
            }
            break;
        case 'assignTrigger':
            $aFields = array ('STEP_UID' => $aData['STEP_UID'],'TAS_UID' => $_SESSION['TASK'],'TRI_UID' => $aData['TRI_UID'],'ST_TYPE' => $aData['ST_TYPE']
            );
            $oStepTrigger = new StepTrigger();
            $oStepTrigger->create( $aFields );
            $aFields['ST_CONDITION'] = $aData['ST_CONDITION'];
            $aFields['ST_POSITION'] = ($oStepTrigger->getNextPosition( $aData['STEP_UID'], $aData['ST_TYPE'] ) - 1);
            $oStepTrigger->update( $aFields );
            break;
        case 'editTriggerCondition':
            require_once 'classes/model/Step.php';
            require_once 'classes/model/Triggers.php';
            $oStep = new Step();

            $aFields['STEP_UID'] = $aData['sStep'];
            $aFields['TRI_UID'] = $aData['sTrigger'];
            $aFields['ST_TYPE'] = $aData['sType'];

            $Trigger = new Triggers();
            $aRow = $Trigger->load( $aData['sTrigger'] );

            $oStepTrigger = new StepTrigger();
            $aFields = $oStepTrigger->load( $aFields['STEP_UID'], $_SESSION['TASK'], $aFields['TRI_UID'], $aFields['ST_TYPE'] );
            $aFields['action'] = 'saveTriggerCondition';
            $aFields['PROCESS'] = $aRow['PRO_UID'];
            global $G_PUBLISH;
            G::LoadClass( 'xmlfield_InputPM' );
            $G_PUBLISH = new Publisher();
            $G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'steps/triggersCondition_Edit', '', $aFields, '../steps/steps_Ajax' );
            G::RenderPage( 'publish', 'raw' );
            break;
        case 'saveTriggerCondition':
            $oStepTrigger = new StepTrigger();
            $oStepTrigger->update( array ('STEP_UID' => $aData['STEP_UID'],'TAS_UID' => $_SESSION['TASK'],'TRI_UID' => $aData['TRI_UID'],'ST_TYPE' => $aData['ST_TYPE'],'ST_CONDITION' => $aData['ST_CONDITION']
            ) );
            break;
        case 'upTrigger':
            $aData['iPosition'] = (int) $aData['iPosition'];
            $oStepTrigger = new StepTrigger();
            $oStepTrigger->up( $aData['sStep'], $_SESSION['TASK'], $aData['sTrigger'], $aData['sType'], $aData['iPosition'] );
            break;
        case 'downTrigger':
            $aData['iPosition'] = (int) $aData['iPosition'];
            $oStepTrigger = new StepTrigger();
            $oStepTrigger->down( $aData['sStep'], $_SESSION['TASK'], $aData['sTrigger'], $aData['sType'], $aData['iPosition'] );
            break;
        case 'ofToAssignTrigger':
            $oStepTrigger = new StepTrigger();
            $oStepTrigger->reOrder( $aData['sStep'], $_SESSION['TASK'], $aData['sType'], $aData['iPosition'] );
            $oStepTrigger->remove( $aData['sStep'], $_SESSION['TASK'], $aData['sTrigger'], $aData['sType'] );
            break;
        case 'counterTriggers':
            G::LoadClass( 'processMap' );
            $oProcessMap = new ProcessMap();
            $oCriteria1 = $oProcessMap->getStepTriggersCriteria( $aData['sStep'], $_SESSION['TASK'], $aData['sType'] );
            if ($aData['sType'] == 'BEFORE') {
                $oCriteria2 = $oProcessMap->getStepTriggersCriteria( $aData['sStep'], $_SESSION['TASK'], 'AFTER' );
            } else {
                $oCriteria2 = $oProcessMap->getStepTriggersCriteria( $aData['sStep'], $_SESSION['TASK'], 'BEFORE' );
            }
            $iCantity = StepTriggerPeer::doCount( $oCriteria1 );
            $iTotal = $iCantity + StepTriggerPeer::doCount( $oCriteria2 );
            echo $iTotal . '|' . $iCantity;
            break;
    }
} catch (Exception $oException) {
    die( $oException->getMessage() );
}

