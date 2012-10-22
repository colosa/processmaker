<?php
/**
 * reportTables_Edit.php
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
G::LoadClass( 'xmlfield_InputPM' );
$aFields['FIELDS'] = array ();
if (isset( $_GET['REP_TAB_UID'] )) {
    $oReportTable = new ReportTable();
    $aFields = $oReportTable->load( $_GET['REP_TAB_UID'] );
    $aTheFields = getDynaformsVars( $aFields['PRO_UID'], false );
    $oReportTables = new ReportTables();
    $aVars = $oReportTables->getTableVars( $_GET['REP_TAB_UID'] );
    $aFields['FIELDS'] = array ();
    foreach ($aTheFields as $aField) {
        if (in_array( $aField['sName'], $aVars )) {
            $aFields['FIELDS'][] = $aField['sName'] . '-' . $aField['sType'];
        }
    }
} else {
    $aFields['PRO_UID'] = $_GET['PRO_UID'];
    $aFields['FIELDS'] = array ();
    $aTheFields = getDynaformsVars( $aFields['PRO_UID'], false );
}
$aProcessFields[] = array ('FIELD_UID' => 'char','FIELD_NAME' => 'char'
);
$aTheFields = getDynaformsVars( $aFields['PRO_UID'], false );
foreach ($aTheFields as $aField) {
    $aProcessFields[] = array ('FIELD_UID' => $aField['sName'] . '-' . $aField['sType'],'FIELD_NAME' => $aField['sName']
    );
}
$aProcessGridFields[] = array ('FIELD_UID' => 'char','FIELD_NAME' => 'char'
);
$aTheFields = getGridsVars( $aFields['PRO_UID'] );
foreach ($aTheFields as $aField) {
    $aProcessGridFields[] = array ('FIELD_UID' => $aField['sName'] . '-' . $aField['sXmlForm'],'FIELD_NAME' => $aField['sName']
    );
}
global $_DBArray;
$_DBArray['processFields'] = $aProcessFields;
$_DBArray['processGridFields'] = $aProcessGridFields;
$_SESSION['_DBArray'] = $_DBArray;

$aFields['LANG'] = SYS_LANG;
$G_PUBLISH = new Publisher();
$G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'reportTables/reportTables_Edit', '', $aFields, '../reportTables/reportTables_Save' );
G::RenderPage( 'publish', 'blank' );

