<?php
/**
 * processes_Delete.php
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

/*
global $RBAC;
switch ($RBAC->userCanAccess('PM_FACTORY'))
{
	case -2:
	  G::SendTemporalMessage('ID_USER_HAVENT_RIGHTS_SYSTEM', 'error', 'labels');
	  G::header('location: ../login/login');
	  die;
	break;
	case -1:
	  G::SendTemporalMessage('ID_USER_HAVENT_RIGHTS_PAGE', 'error', 'labels');
	  G::header('location: ../login/login');
	  die;
	break;
}
*/
$PRO_UID = $_GET['PRO_UID'];
require_once 'classes/model/Process.php';
G::LoadClass( 'case' );
$oProcessMap = new Cases();

$process = new Process();
$processData = $process->load( $PRO_UID );

$c = $oProcessMap->getCriteriaProcessCases( 'TO_DO', $PRO_UID );
$processData["TO_DO"] = ApplicationPeer::doCount( $c );

$c = $oProcessMap->getCriteriaProcessCases( 'COMPLETED', $PRO_UID );
$processData["COMPLETED"] = ApplicationPeer::doCount( $c );

$c = $oProcessMap->getCriteriaProcessCases( 'DRAFT', $PRO_UID );
$processData["DRAFT"] = ApplicationPeer::doCount( $c );

$c = $oProcessMap->getCriteriaProcessCases( 'CANCELLED', $PRO_UID );
$processData["CANCELLED"] = ApplicationPeer::doCount( $c );

$processData["PRO_UID"] = $PRO_UID;

$G_PUBLISH = new Publisher();
$G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'processes/processes_DeleteCases', '', $processData, 'processes_Delete.php' );
G::RenderPage( 'publish', 'raw' );

?>