<?php
require_once ('classes/model/AdditionalTables.php');

$oAdditionalTables = new AdditionalTables();
$aData = $oAdditionalTables->load( $_POST['ADD_TABLE'], true );
$addTabName = $aData['ADD_TAB_NAME'];
$c = 1;
foreach ($aData['FIELDS'] as $iRow => $aRow) {
    if ($aRow['FLD_KEY'] == 1) {
        $aRow['PRO_VARIABLE'] = '';
        $aFields['FIELDS'][$c ++] = $aRow;
    }
}
$aFields['DYN_UID'] = $_POST['DYN_UID'];
$aFields['ADD_TABLE'] = $_POST['ADD_TABLE'];
$aFields['PRO_UID'] = $_POST['PRO_UID'];
$aFields['DYN_TITLE'] = $_POST['DYN_TITLE'];
$aFields['DYN_TYPE'] = $_POST['DYN_TYPE'];
$aFields['ACTION'] = $_POST['ACTION'];
$aFields['DYN_DESCRIPTION'] = $_POST['DYN_DESCRIPTION'];
$aFields['VALIDATION_MESSAGE'] = G::LoadTranslation( 'ID_FILL_PRIMARY_KEYS' );

G::LoadClass( 'xmlfield_InputPM' );

$G_PUBLISH = new Publisher();
$G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'dynaforms/dynaforms_AssignVariables', '', $aFields, SYS_URI . 'dynaforms/dynaforms_Save' );

G::RenderPage( 'publish-raw', 'raw' );

