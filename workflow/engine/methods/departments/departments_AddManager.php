<?php

/**
 * departments_AddManager.php
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

G::LoadClass( 'departos' );

$dbc = new DBConnection();
$ses = new DBSession( $dbc );

//print_r($_GET);
$oDpto = new Departos();
//$DptoUid = (isset($_GET['UID'])) ? urldecode($_GET['UID']):'';
$aUser = Array ();
$aUser[] = Array ('USR_UID' => 'char','USR_FIRSTNAME' => 'char','USR_LASTNAME' => 'char' );

$aUserManagers = $oDpto->getUsersManagers();
$aUser_Manager = array_merge( $aUser, $aUserManagers );
//print_r($aUser_Manager);
/*
   global $_DBArray;
   $_DBArray['aManager']   = $aUser_Manager;
   $_SESSION['_DBArray'] = $_DBArray;
   G::LoadClass('ArrayPeer');
   $oCriteria = new Criteria('dbarray');
   $oCriteria->setDBArrayTable('aManager');

   */

$aFields = array ();
$aFields['DEP_UID'] = $_GET['SUID'];
$G_PUBLISH = new Publisher();
$G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'departments/departments_AddManager', '', $aFields, 'departments_SaveManager' );

G::RenderPage( "publish", "raw" );

