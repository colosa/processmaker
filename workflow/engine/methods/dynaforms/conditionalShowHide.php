<?php
/**
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

/*
  * @Author Erik Amaru Ortiz <erik@colosa.com>
  * @Date Aug 26th, 2009
  */
require_once "classes/model/FieldCondition.php";
G::LoadClass( 'ArrayPeer' );

$G_PUBLISH = new Publisher();
$oHeadPublisher = & headPublisher::getSingleton();
$DISPLAY_MAX_SIZE = 25;
global $_DBArray;

$oFieldCondition = new FieldCondition();
$DYN_UID = $_SESSION['Current_Dynafom']['Parameters']['DYN_UID'];
$aRows = $oFieldCondition->getAllByDynUid( $DYN_UID );

$aFieldNames = Array ('FCD_NRO','FCD_UID','FCD_FUNCTION','FCD_FIELDS','FCD_CONDITION','FCD_EVENTS','FCD_EVENT_OWNERS','FCD_STATUS','FCD_DYN_UID' );

//Routines to limit the show in list max size for some fields that can have large size
$inndex = 0;
$aRowsTmp = Array ();
foreach ($aRows as $aRow) {
    $aRow['FCD_NRO'] = ++ $inndex;
    if (strlen( $aRow['FCD_FIELDS'] ) > $DISPLAY_MAX_SIZE) {
        $aRow['FCD_FIELDS'] = substr( $aRow['FCD_FIELDS'], 0, $DISPLAY_MAX_SIZE ) . '...';
    }

    if ($aRow['FCD_FUNCTION'] == 'showAll' || $aRow['FCD_FUNCTION'] == 'hideAll') {
        $aRow['FCD_FIELDS'] = 'ALL';
    }

    if (strlen( $aRow['FCD_CONDITION'] ) > $DISPLAY_MAX_SIZE) {
        $aRow['FCD_CONDITION'] = substr( $aRow['FCD_CONDITION'], 0, $DISPLAY_MAX_SIZE ) . '...';
    }
    if (strlen( $aRow['FCD_EVENT_OWNERS'] ) > $DISPLAY_MAX_SIZE) {
        $aRow['FCD_EVENT_OWNERS'] = substr( $aRow['FCD_EVENT_OWNERS'], 0, $DISPLAY_MAX_SIZE ) . '...';
    }
    array_push( $aRowsTmp, $aRow );
}

$aRows = array_merge( Array ($aFieldNames), $aRowsTmp );

$_DBArray['virtual_pmtable'] = $aRows;
$_SESSION['_DBArray'] = $_DBArray;

$oCriteria = new Criteria( 'dbarray' );
$oCriteria->setDBArrayTable( 'virtual_pmtable' );

$oHeadPublisher->addScriptFile( '/jscore/dynaforms/dynaforms_conditionalShowHide.js' );
$G_PUBLISH->AddContent( 'propeltable', 'paged-table', 'dynaforms/dynaforms_ConditionalShowHideList', $oCriteria, Array ('DYN_UID' => $DYN_UID), '' );
G::RenderPage( 'publish', 'raw' );

