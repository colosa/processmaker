<?php
/**
 * triggers_Save.php
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
if (($RBAC_Response = $RBAC->userCanAccess( "PM_FACTORY" )) != 1) {
    return $RBAC_Response;
}
G::LoadClass( 'reportTables' );

if (isset( $_POST['form'] )) {
    $values = $_POST['form']; //For Old processmap
} else {
    $values = $_POST; //For extjs,since we are not using form
    $values['FIELDS'] = explode( ',', $_POST['FIELDS'] );
}

$oReportTable = new ReportTable();
if (! isset( $values['REP_TAB_CONNECTION'] )) {
    $values['REP_TAB_CONNECTION'] = 'report';
}
if ($values['REP_TAB_UID'] != '') {
    $aReportTable = $oReportTable->load( $values['REP_TAB_UID'] );
    $sOldTableName = $aReportTable['REP_TAB_NAME'];
    $sOldConnection = $aReportTable['REP_TAB_CONNECTION'];
} else {
    $sOldTableName = $values['REP_TAB_NAME'];
    $sOldConnection = $values['REP_TAB_CONNECTION'];
    $oReportTable->create( $values );
    $values['REP_TAB_UID'] = $oReportTable->getRepTabUid();
}

$oReportTable->update( $values );
$oReportVar = new ReportVar();
$oReportTables = new ReportTables();
$oReportTables->deleteAllReportVars( $values['REP_TAB_UID'] );
$aFields = array ();
if ($values['REP_TAB_TYPE'] == 'GRID') {
    $aAux = explode( '-', $values['REP_TAB_GRID'] );
    global $G_FORM;
    $G_FORM = new Form( $values['PRO_UID'] . '/' . $aAux[1], PATH_DYNAFORM, SYS_LANG, false );
    $aAux = $G_FORM->getVars( false );
    foreach ($aAux as $aField) {
        $values['FIELDS'][] = $aField['sName'] . '-' . $aField['sType'];
    }
}
foreach ($values['FIELDS'] as $sField) {
    $aField = explode( '-', $sField );
    switch ($aField[1]) {
        case 'currency':
        case 'percentage':
            $sType = 'number';
            break;
        case 'text':
        case 'password':
        case 'dropdown':
        case 'yesno':
        case 'checkbox':
        case 'radiogroup':
        case 'hidden':
            $sType = 'char';
            break;
        case 'textarea':
            $sType = 'text';
            break;
        case 'date':
            $sType = 'date';
            break;
        default:
            $sType = 'char';
            break;
    }
    $oReportVar->create( array ('REP_TAB_UID' => $values['REP_TAB_UID'],'PRO_UID' => $values['PRO_UID'],'REP_VAR_NAME' => $aField[0],'REP_VAR_TYPE' => $sType
    ) );
    $aFields[] = array ('sFieldName' => $aField[0],'sType' => $sType
    );
}
$oReportTables->dropTable( $sOldTableName, $sOldConnection );
$oReportTables->createTable( $values['REP_TAB_NAME'], $values['REP_TAB_CONNECTION'], $values['REP_TAB_TYPE'], $aFields );
$oReportTables->populateTable( $values['REP_TAB_NAME'], $values['REP_TAB_CONNECTION'], $values['REP_TAB_TYPE'], $aFields, $values['PRO_UID'], $values['REP_TAB_GRID'] );

