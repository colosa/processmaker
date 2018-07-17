<?php
/**
 * groups_Save.php
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
//print_r($_POST);die;


if (($RBAC_Response = $RBAC->userCanAccess( "PM_USERS" )) != 1) {
    return $RBAC_Response;
}

$G_MAIN_MENU = 'wf.login';
$G_MENU_SELECTED = '';

//$oDepto = new Departos();


//$oDepto->saveNewDepto($_POST['form']);


//print_r($_GET);
$oDepto = new Department();

$depRow = $_POST['form'];

$DptoUid = (isset( $_POST['form']['SUID'] )) ? urldecode( $_POST['form']['SUID'] ) : '';
$DepParent = (isset( $_POST['form']['SDEPPARENT'] )) ? urldecode( $_POST['form']['SDEPPARENT'] ) : '';

//if($_POST['form']['SDEP_UID']==='' && $_POST['form']['SDEP_UID'] ==='')
//if($_POST['form']['SUID']!=='' && $_POST['form']['SDEPPARENT'] ==='')
if (strlen( $DptoUid ) > 1 && strlen( $DepParent ) == 1) {
    $oDepto->subcreate( $depRow );
    //unset ( $depRow['DEP_UID'] );
    //$oDepto->subcreate( $depRow );
    //$_POST['form']['GRP_UID']=$group->getGrpUid();
    //$group->update($_POST['form']);
} else {
    // 1ro
    ////////$oDepto->subcreate( $depRow );
    $oDepto->subupdate( $depRow );
}

