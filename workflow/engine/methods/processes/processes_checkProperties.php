<?php
/**
 * processes_checkProperties.php
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


$filter = new InputFilter();
$form = $_POST['form'];
$form = $filter->xssFilterHard($form);

//$tasUid = $form['TASKS'];
$tasUid = $form['TAS_PARENT'];
$spSynchronous = $form['SP_SYNCHRONOUS'];

require_once 'classes/model/Route.php';
require_once 'classes/model/Task.php';

$oRoute = new Route();
$oCriteria = new Criteria( 'workflow' );
$oCriteria->addSelectColumn( RoutePeer::ROU_NEXT_TASK );
$oCriteria->add( RoutePeer::TAS_UID, $tasUid );
$oDataset = RoutePeer::doSelectRS( $oCriteria );
$oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );

$sw = 1;
// if there are more step we're looking them and we're checking TAS_ASSIGN_TYPE field
while ($oDataset->next() && $sw) {
    $aRow = $oDataset->getRow();

    $oCriteria1 = new Criteria( 'workflow' );
    $oCriteria1->addSelectColumn( TaskPeer::TAS_ASSIGN_TYPE );
    $oCriteria1->add( TaskPeer::PRO_UID, $form['PRO_PARENT'] );
    $oCriteria1->add( TaskPeer::TAS_UID, $aRow['ROU_NEXT_TASK'] );
    $oDataset1 = TaskPeer::doSelectRS( $oCriteria1 );
    $oDataset1->setFetchmode( ResultSet::FETCHMODE_ASSOC );
    $oDataset1->next();
    $aRow1 = $oDataset1->getRow();

    if ($spSynchronous && $aRow1['TAS_ASSIGN_TYPE'] == 'MANUAL')
        $sw = 0;

}
///If there are at least one TAS_ASSIGN_TYPE field with MANUAL it returns 1
if (! $sw)
    return print $spSynchronous;
else
    return print '0';


