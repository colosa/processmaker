<?php
/**
 * users_New.php
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

require_once 'classes/model/AppDelegation.php';

G::LoadClass( 'dates' );
$oDates = new dates();

$oCriteria = new Criteria( 'workflow' );
$oCriteria->addSelectColumn( AppDelegationPeer::APP_UID );
$oCriteria->addSelectColumn( AppDelegationPeer::DEL_INDEX );
$oCriteria->addSelectColumn( AppDelegationPeer::TAS_UID );
$oCriteria->addSelectColumn( AppDelegationPeer::DEL_INIT_DATE );
$oCriteria->addSelectColumn( AppDelegationPeer::DEL_FINISH_DATE );

$oDataset = AppDelegationPeer::doSelectRS( $oCriteria );
$oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
$oDataset->next();
while ($aRow = $oDataset->getRow()) {

    $oCriteria = new Criteria( 'workflow' );
    $oCriteria->addSelectColumn( TaskPeer::TAS_UID );
    $oCriteria->add( TaskPeer::TAS_UID, $aRow['TAS_UID'] );
    $oDataseti = TaskPeer::doSelectRS( $oCriteria );
    $oDataseti->setFetchmode( ResultSet::FETCHMODE_ASSOC );
    $oDataseti->next();
    $b = 0;
    while ($aRows = $oDataseti->getRow()) {
        if (TaskPeer::doCount( $oCriteria ) == 1) {
            $b = 1;
        }
        $oDataseti->next();
    }

    if ($b == 1) {
        if ($aRow['DEL_INIT_DATE'] != null && $aRow['DEL_FINISH_DATE'] != null) {
            $fDuration = $oDates->calculateDuration( $aRow['DEL_INIT_DATE'], $aRow['DEL_FINISH_DATE'], null, null, $aRow['TAS_UID'] );

            $oCriteria = new Criteria( 'workflow' );
            $sql = "UPDATE APP_DELEGATION SET DEL_DURATION='" . $fDuration . "'
    								WHERE APP_UID='" . $aRow['APP_UID'] . "' AND DEL_INDEX='" . $aRow['DEL_INDEX'] . "'";

            $con = Propel::getConnection( "workflow" );
            $stmt = $con->prepareStatement( $sql );
            $rs = $stmt->executeQuery();
        } else {
            $oCriteria = new Criteria( 'workflow' );
            $sql = "UPDATE APP_DELEGATION SET DEL_DURATION=0
    								WHERE APP_UID='" . $aRow['APP_UID'] . "' AND DEL_INDEX='" . $aRow['DEL_INDEX'] . "'";

            $con = Propel::getConnection( "workflow" );
            $stmt = $con->prepareStatement( $sql );
            $rs = $stmt->executeQuery();
        }
    } else {
        $oCriteria = new Criteria( 'workflow' );
        $sql = "UPDATE APP_DELEGATION SET DEL_DURATION=0
    								WHERE APP_UID='" . $aRow['APP_UID'] . "' AND DEL_INDEX='" . $aRow['DEL_INDEX'] . "'";

        $con = Propel::getConnection( "workflow" );
        $stmt = $con->prepareStatement( $sql );
        $rs = $stmt->executeQuery();
    }

    $oDataset->next();
}

//G::header('location: reportsList');

