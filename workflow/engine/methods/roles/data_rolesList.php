<?php
/**
 * data_rolesList.php
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

$filter = new InputFilter();
$_POST = $filter->xssFilterHard($_POST);

isset( $_POST['textFilter'] ) ? $filter = $_POST['textFilter'] : $filter = '';

if ($filter != "") {
    $aRoles = $RBAC->getAllRolesFilter( $filter );
} else {
    $aRoles = $RBAC->getAllRoles();
}

//$ocaux = $oAdditionalTables->getDataCriteria($_GET['sUID']);
//
//$rs = AdditionalTablesPeer::DoSelectRs ($ocaux);
//$rs->setFetchmode (ResultSet::FETCHMODE_ASSOC);
//
//$rows = Array();
//while($rs->next()){
//	$rows[] = $rs->getRow();
//}
echo '{roles: ' . G::json_encode( $aRoles ) . '}';

