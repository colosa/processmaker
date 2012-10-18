<?php
/**
 * tracker_Show.php
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
* dynaforms & documents for Case Tracker
*
* @author Everth S. Berrios Morales <everth@colosa.com>
*
*/

if (! isset( $_SESSION['PROCESS'] )) {
    G::header( 'location: login' );
}

global $_DBArray;
if (! isset( $_DBArray )) {
    $_DBArray = array ();
}

$G_MAIN_MENU = 'caseTracker';
$G_ID_MENU_SELECTED = 'DYNADOC';
global $G_PUBLISH;

switch ($_GET['CTO_TYPE_OBJ']) {
    case 'DYNAFORM':
        G::LoadClass( 'case' );
        $oCase = new Cases();
        $Fields = $oCase->loadCase( $_SESSION['APPLICATION'] );
        $Fields['APP_DATA']['__DYNAFORM_OPTIONS']['PREVIOUS_STEP_LABEL'] = '';
        $Fields['APP_DATA']['__DYNAFORM_OPTIONS']['NEXT_STEP_LABEL'] = '';
        $Fields['APP_DATA']['__DYNAFORM_OPTIONS']['NEXT_STEP'] = '#';
        $Fields['APP_DATA']['__DYNAFORM_OPTIONS']['NEXT_ACTION'] = 'alert("Sample"); return false;';
        $Fields['APP_DATA']['__DYNAFORM_OPTIONS']['PRINT_PREVIEW'] = '#';
        $Fields['APP_DATA']['__DYNAFORM_OPTIONS']['PRINT_PREVIEW_ACTION'] = 'tracker_PrintView?CTO_UID_OBJ=' . $_GET['CTO_UID_OBJ'] . '&CTO_TYPE_OBJ=PRINT_PREVIEW';
        $_SESSION['CTO_UID_OBJ'] = $_GET['CTO_UID_OBJ'];
        $G_PUBLISH = new Publisher();
        $G_PUBLISH->AddContent( 'dynaform', 'xmlform', $_SESSION['PROCESS'] . '/' . $_GET['CTO_UID_OBJ'], '', $Fields['APP_DATA'], '', '', 'view' );
        G::RenderPage( 'publish' );
        break;

    case 'INPUT_DOCUMENT':
        G::LoadClass( 'case' );
        $oCase = new Cases();
        $c = $oCase->getAllUploadedDocumentsCriteriaTracker( $_SESSION['PROCESS'], $_SESSION['APPLICATION'], $_GET['CTO_UID_OBJ'] );

        $oHeadPublisher = & headPublisher::getSingleton();
        $oHeadPublisher->addScriptFile( '/jscore/tracker/tracker.js' );

        $G_PUBLISH = new Publisher();
        $G_PUBLISH->AddContent( 'propeltable', 'paged-table', 'tracker/tracker_Inputdocs', $c );
        G::RenderPage( 'publish' );
        break;

    case 'OUTPUT_DOCUMENT':
        G::LoadClass( 'case' );
        $oCase = new Cases();
        $c = $oCase->getAllGeneratedDocumentsCriteriaTracker( $_SESSION['PROCESS'], $_SESSION['APPLICATION'], $_GET['CTO_UID_OBJ'] );

        $oHeadPublisher = & headPublisher::getSingleton();
        $oHeadPublisher->addScriptFile( '/jscore/tracker/tracker.js' );

        $G_PUBLISH = new Publisher();
        $G_PUBLISH->AddContent( 'propeltable', 'paged-table', 'tracker/tracker_Outputdocs', $c );
        G::RenderPage( 'publish' );
        break;
}

