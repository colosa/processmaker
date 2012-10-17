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

$USR_UID = $_GET['USR_UID'];

G::LoadClass( 'case' );
$oProcessMap = new Cases();

$c = $oProcessMap->getCriteriaUsersCases( 'TO_DO', $USR_UID );
$array["TO_DO"] = ApplicationPeer::doCount( $c );

$c = $oProcessMap->getCriteriaUsersCases( 'COMPLETED', $USR_UID );
$array["COMPLETED"] = ApplicationPeer::doCount( $c );

$c = $oProcessMap->getCriteriaUsersCases( 'DRAFT', $USR_UID );
$array["DRAFT"] = ApplicationPeer::doCount( $c );

$c = $oProcessMap->getCriteriaUsersCases( 'CANCELLED', $USR_UID );
$array["CANCELLED"] = ApplicationPeer::doCount( $c );

$array["USR_UID"] = $USR_UID;

$G_PUBLISH = new Publisher();
$G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'users/users_DeleteAssign', '', $array, '' );
G::RenderPage( 'publish', 'raw' );

