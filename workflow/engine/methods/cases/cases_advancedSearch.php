<?php
/**
 * cases_advancedSearch.php
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
 * by The Answer
 */

$G_MAIN_MENU = 'processmaker';
$G_SUB_MENU = 'cases';
$G_ID_MENU_SELECTED = 'CASES';
$G_ID_SUB_MENU_SELECTED = 'CASES_ADVANCEDSEARCH';
$G_PUBLISH = new Publisher();

global $RBAC;
$permisse = $RBAC->userCanAccess( 'PM_ALLCASES' );
$userlogged = $_SESSION['USER_LOGGED'];

require_once ("classes/model/ProcessUser.php");
$oCriteria = new Criteria( 'workflow' );
$oCriteria->addSelectColumn( ProcessUserPeer::PU_UID );
$oCriteria->addSelectColumn( ProcessUserPeer::PRO_UID );
$oCriteria->add( ProcessUserPeer::USR_UID, $userlogged );
$oCriteria->add( ProcessUserPeer::PU_TYPE, "SUPERVISOR" );

$oDataset = ProcessUserPeer::doSelectRS( $oCriteria );
$oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
$oDataset->next();

$aSupervisor = array ();
while ($aRow = $oDataset->getRow()) {
    $aSupervisor[] = $aRow['PRO_UID'];
    $oDataset->next();
}

G::LoadClass( 'case' );
$oCases = new Cases();

if (isset( $_POST['form'] )) {
    $fields['CASE_NUMBER'] = $_POST['form']['CASE_NUMBER'];
    $fields['PROCESS'] = $_POST['form']['PROCESS'];
    $fields['TASKS'] = $_POST['form']['TASKS'];
    $fields['CURRENT_USER'] = $_POST['form']['CURRENT_USER'];
    $fields['SENT_BY'] = $_POST['form']['SENT_BY'];
    $fields['LAST_MODIFICATION_F'] = $_POST['form']['LAST_MODIFICATION_F'];
    $fields['LAST_MODIFICATION_T'] = $_POST['form']['LAST_MODIFICATION_T'];
    $fields['APP_STATUS'] = $_POST['form']['APP_STATUS'];

    $Criteria = $oCases->getAdvancedSearch( $fields['CASE_NUMBER'], $fields['PROCESS'], $fields['TASKS'], $fields['CURRENT_USER'], $fields['SENT_BY'], $fields['LAST_MODIFICATION_F'], $fields['LAST_MODIFICATION_T'], $fields['APP_STATUS'], $permisse, $userlogged, $aSupervisor );
    $G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'cases/cases_advancedSearchFilter', '', $fields );
} else {
    //list($Criteria,$xmlform) = $oCases->getConditionCasesList('gral');
    $Criteria = $oCases->getAdvancedSearch( '', '', '', '', '', '', '', '', $permisse, $userlogged, $aSupervisor );
    $G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'cases/cases_advancedSearchFilter' );
}
$G_PUBLISH->AddContent( 'propeltable', 'paged-table', 'cases/cases_advancedSearch', $Criteria );

G::RenderPage( 'publish', 'blank' );
?>
<script>
    parent.outerLayout.hide('east');
    parent.PANEL_EAST_OPEN = false;
</script>

