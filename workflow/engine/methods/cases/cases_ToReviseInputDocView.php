<?php
/**
 * cases_Step.php
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

/* Permissions */
switch ($RBAC->userCanAccess( 'PM_SUPERVISOR' )) {
    case - 2:
        G::SendTemporalMessage( 'ID_USER_HAVENT_RIGHTS_SYSTEM', 'error', 'labels' );
        G::header( 'location: ../login/login' );
        die();
        break;
    case - 1:
        G::SendTemporalMessage( 'ID_USER_HAVENT_RIGHTS_PAGE', 'error', 'labels' );
        G::header( 'location: ../login/login' );
        die();
        break;
}

if ((int) $_SESSION['INDEX'] < 1) {
    G::SendTemporalMessage( 'ID_USER_HAVENT_RIGHTS_PAGE', 'error', 'labels' );
    G::header( 'location: ' . $_SERVER['HTTP_REFERER'] );
    die();
}

/* GET , POST & $_SESSION Vars */
//$_SESSION['STEP_POSITION'] = (int)$_GET['POSITION'];


/* Menues */
$G_MAIN_MENU = 'processmaker';
$G_SUB_MENU = 'cases';
$G_ID_MENU_SELECTED = 'CASES';
$G_ID_SUB_MENU_SELECTED = 'CASES_TO_REVISE';

$oCase = new Cases();
$Fields = $oCase->loadCase( $_SESSION['APPLICATION'] );

require_once 'classes/model/AppDocument.php';
require_once 'classes/model/Users.php';

$G_PUBLISH = new Publisher();

$oAppDocument = new AppDocument();
$oAppDocument->Fields = $oAppDocument->load( $_GET['DOC'] );
$oo = $oAppDocument->load( $_GET['DOC'] );

$oUser = new Users();
$aUser = $oUser->load( $oAppDocument->Fields['USR_UID'] );
$Fields['CREATOR'] = $aUser['USR_FIRSTNAME'] . ' ' . $aUser['USR_LASTNAME'];

$oAppDocument->Fields['VIEW'] = G::LoadTranslation( 'ID_OPEN' );
$oAppDocument->Fields['FILE'] = 'cases_ShowDocument?a=' . $_GET['DOC'] . '&r=' . rand();
$G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'cases/cases_ViewInputDocumentToRevise', '', G::array_merges( $Fields, $oAppDocument->Fields ), '' );

G::RenderPage( 'publish' );

