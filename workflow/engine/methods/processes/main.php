<?php
/**
 * main.php Cases List main processor
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

/*$access = $RBAC->userCanAccess('PM_FACTORY');
if( $access != 1 ) {
  switch ($access) {
  	case -2:
  	  G::SendTemporalMessage('ID_USER_HAVENT_RIGHTS_SYSTEM', 'error', 'labels');
  	  G::header('location: ../login/login');
  	break;
    case -1:
  	default:
  	  G::SendTemporalMessage('ID_USER_HAVENT_RIGHTS_PAGE', 'error', 'labels');
  	  G::header('location: ../login/login');
  	break;
  }
  exit();
}*/
//next two variables store the current process uid and the last processmap used
//print_r ($_SESSION['PROCESS'] );
//print_r ($_SESSION['PROCESSMAP'] );
$RBAC->requirePermissions( 'PM_FACTORY' );

$G_MAIN_MENU = 'processmaker';
$G_SUB_MENU = 'process';
$G_ID_MENU_SELECTED = 'PROCESSES';
$G_ID_SUB_MENU_SELECTED = '-';

$G_PUBLISH = new Publisher();
// $oHeadPublisher = & headPublisher::getSingleton();
//$oHeadPublisher->addScriptFile('/jscore/processes/main.js');
$G_PUBLISH->AddContent( 'view', 'processes/mainLoad' );

if (isset( $_GET['type'] ))
    G::RenderPage( "publishBlank", "blank" );
else
    G::RenderPage( "publish" );

