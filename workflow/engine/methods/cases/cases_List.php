<?php
/**
 * cases_List.php
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

/**
 * Cases list (Refactored)
 * By Erik A.
 * O. <erik@colosa.com, aortiz.erik@gmail.com>
 */

/* Permissions */
if (($RBAC_Response = $RBAC->userCanAccess( "PM_CASES" )) != 1) {
    return $RBAC_Response;
}

    /* Includes */
G::LoadClass( 'case' );
G::LoadClass( 'configuration' );

// $_GET['l'] has the type of cases list like todo,pause,cancel, all


$conf = new Configurations();
if (! isset( $_GET['l'] )) {
    $confCasesList = $conf->loadObject( 'ProcessMaker', 'cases_List', '', $_SESSION['USER_LOGGED'], '' );
    if (is_array( $confCasesList )) {
        $sTypeList = $confCasesList['sTypeList'];
    } else {
        $sTypeList = 'to_do';
    }
} else {
    $sTypeList = $_GET['l'];
    $confCasesList = array ('sTypeList' => $sTypeList
    );
    $conf->saveObject( $confCasesList, 'ProcessMaker', 'cases_List', '', $_SESSION['USER_LOGGED'], '' );
}

$sUIDUserLogged = $_SESSION['USER_LOGGED'];
$_SESSION['CASES_MENU_OPTION'] = $sTypeList;

$oCases = new Cases();

/**
 * here we verify if there is a any case with a unpause on this day
 */
if ($sTypeList === 'to_do' or $sTypeList === 'draft' or $sTypeList === 'paused') {
    $oCases->ThrowUnpauseDaemon( date( 'Y-m-d' ) );
}

/* *
 * Prepare the addtional filters before to show
 * By Erik
 */

$aAdditionalFilter = Array ();

if (isset( $_GET['PROCESS_UID'] ) and $_GET['PROCESS_UID'] != "0" && $_GET['PROCESS_UID'] != "") {
    $PRO_UID = $_GET['PROCESS_UID'];
    $aAdditionalFilter['PRO_UID'] = $PRO_UID;
} else {
    $PRO_UID = "0";
}
if (isset( $_GET['READ'] ) and $_GET['READ'] == "1") {
    $aAdditionalFilter['READ'] = $_GET['READ'];
}
if (isset( $_GET['UNREAD'] ) and $_GET['UNREAD'] == "1") {
    $aAdditionalFilter['UNREAD'] = $_GET['UNREAD'];
}

if (isset( $_GET['APP_STATUS_FILTER'] ) and $_GET['APP_STATUS_FILTER'] != "ALL") {
    $aAdditionalFilter['APP_STATUS_FILTER'] = $_GET['APP_STATUS_FILTER'];
}

if (isset( $_GET['MINE'] ) and $_GET['MINE'] == "1") {
    $aAdditionalFilter['MINE'] = $_GET['MINE'];
}

switch ($sTypeList) {
    case 'to_do':
        if (defined( 'ENABLE_CASE_LIST_OPTIMIZATION' )) {
            $aCriteria = $oCases->prepareCriteriaForToDo( $sUIDUserLogged );
            $xmlfile = 'cases/cases_ListTodoNew';
        } else {
            list ($aCriteria, $xmlfile) = $oCases->getConditionCasesList( $sTypeList, $sUIDUserLogged, true, $aAdditionalFilter );
        }
        break;
    default:
        list ($aCriteria, $xmlfile) = $oCases->getConditionCasesList( $sTypeList, $sUIDUserLogged, true, $aAdditionalFilter );
}

/*
$rs = ApplicationPeer::doSelectRS($aCriteria);
    $rs->setFetchmode(ResultSet::FETCHMODE_ASSOC);
    $aRows1 = Array();
    while($rs->next()) {
        $aRows1[] = $rs->getRow();

    }

g::pr($aRows1);die;*/
/* GET , POST & $_SESSION Vars */

if (! isset( $_GET['PROCESS_UID'] )) {
    $oCase = new Cases();
    $rs = ApplicationPeer::doSelectRS( $aCriteria );
    $rs->setFetchmode( ResultSet::FETCHMODE_ASSOC );

    $aProcess = Array ();
    while ($rs->next()) {
        $aRow = $rs->getRow();
        //g::pr($aRow); die;
        if (! InAssocArray( $aRow, 'PRO_UID', $aRow['PRO_UID'] )) {
            array_push( $aProcess, Array ('PRO_UID' => $aRow['PRO_UID'],'PRO_TITLE' => $aRow['APP_PRO_TITLE'] ) );
        }
    }

    $_DBArray['_PROCESSES'] = array_merge( Array (Array ('PRO_UID' => 'char','PRO_TITLE' => 'char' ) ), $aProcess );
    $_SESSION['_DBArray'] = $_DBArray;
} else {
    $_DBArray = $_SESSION['_DBArray'];
}

/* Render page */
$G_PUBLISH = new Publisher();
$G_PUBLISH->ROWS_PER_PAGE = 12;

if ($sTypeList == 'to_reassign') {
    $G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'cases/cases_ReassignBy', '', array ('REASSIGN_BY' => 1 ) );
}

$aData = Array ('PROCESS_FILTER' => $PRO_UID,'APP_STATUS_FILTER' => (isset( $_GET['APP_STATUS_FILTER'] ) ? $_GET['APP_STATUS_FILTER'] : '0')
);

$G_PUBLISH->AddContent( 'propeltable', 'paged-table', $xmlfile, $aCriteria, $aData );

G::RenderPage( 'publish', 'blank' );

function InAssocArray ($a, $k, $v)
{
    foreach ($a as $item) {
        if (isset( $item[$k] ) && $v == $item[$k]) {
            return true;
        }
    }
    return false;
}

?>
<script>
  try{
    oPropelTable = document.getElementById('publisherContent[0]');
    oTable = oPropelTable.getElementsByTagName('table');
    oTable[0].style.width = '98%';
    oTable[1].style.width = '98%';

    parent.outerLayout.hide('east');
    parent.PANEL_EAST_OPEN = false;
if(parent.refreshCountFolders) parent.refreshCountFolders();
  }catch(e){}
</script>
<?php

