<?php

/**
 * departments_Delete.php
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
if (($RBAC_Response = $RBAC->userCanAccess( "PM_USERS" )) != 1) {
    return $RBAC_Response;
}

require_once 'classes/model/Department.php';
require_once 'classes/model/Users.php';

$oDpto = new Department();

if (! isset( $_POST['DEP_UID'] )) {
    return;
}

$ocriteria = new Criteria( 'workflow' );
$ocriteria->addSelectColumn( DepartmentPeer::DEP_MANAGER );
$ocriteria->add( DepartmentPeer::DEP_UID, $_POST['DEP_UID'] );
$oDataset = DepartmentPeer::doSelectRS( $ocriteria );
$oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
$oDataset->next();
$aRow = $oDataset->getRow();

$oCriteria1 = new Criteria( 'workflow' );
$oCriteria1->add( UsersPeer::USR_REPORTS_TO, $aRow['DEP_MANAGER'], Criteria::EQUAL );
$oCriteria2 = new Criteria( 'workflow' );
$oCriteria2->add( UsersPeer::USR_REPORTS_TO, '' );
BasePeer::doUpdate( $oCriteria1, $oCriteria2, Propel::getConnection( 'workflow' ) );

$oCriteriaA = new Criteria( 'workflow' );
$oCriteriaA->add( UsersPeer::DEP_UID, $_POST['DEP_UID'], Criteria::EQUAL );
$oCriteriaB = new Criteria( 'workflow' );
$oCriteriaB->add( UsersPeer::DEP_UID, '' );
BasePeer::doUpdate( $oCriteriaA, $oCriteriaB, Propel::getConnection( 'workflow' ) );

$oDpto->remove( urldecode( $_POST['DEP_UID'] ) );

