<?php
G::LoadClass( 'processMap' );
G::LoadClass( 'XmlForm_Field' );
//$oXMLfield = new XmlForm_Field_TextPM(new DBConnection);
$aFields = getDynaformsVars( $_GET['pid'] );
if (isset( $_GET['type'] ))

    $aType = $_GET['type'];

else
    $aType = '';

$aRows[0] = Array ('fieldname' => 'char','variable' => 'char','type' => 'type','label' => 'char'
);
foreach ($aFields as $aField) {
    switch ($aType) {
        case "system":
            if ($aField['sType'] == "system") {
                $aRows[] = Array ('fieldname' => $_GET['sFieldName'],'variable' => $_GET['sSymbol'] . $aField['sName'],'variable_label' => '<div class="pm__dynavars"> <a id="dynalink" href=# onclick="insertFormVar(\'' . $_GET['sFieldName'] . '\',\'' . $_GET['sSymbol'] . $aField['sName'] . '\');">' . $_GET['sSymbol'] . $aField['sName'] . '</a></div>','type' => $aField['sType'],'label' => $aField['sLabel']
                );
            }
            break;
        case "process":
            if ($aField['sType'] != "system") {
                $aRows[] = Array ('fieldname' => $_GET['sFieldName'],'variable' => $_GET['sSymbol'] . $aField['sName'],'variable_label' => '<div class="pm__dynavars"> <a id="dynalink" href=# onclick="insertFormVar(\'' . $_GET['sFieldName'] . '\',\'' . $_GET['sSymbol'] . $aField['sName'] . '\');">' . $_GET['sSymbol'] . $aField['sName'] . '</a></div>','type' => $aField['sType'],'label' => $aField['sLabel']
                );
            }
            break;
        default:
            $aRows[] = Array ('fieldname' => $_GET['sFieldName'],'variable' => $_GET['sSymbol'] . $aField['sName'],'variable_label' => '<div class="pm__dynavars"> <a id="dynalink" href=# onclick="insertFormVar(\'' . $_GET['sFieldName'] . '\',\'' . $_GET['sSymbol'] . $aField['sName'] . '\');">' . $_GET['sSymbol'] . $aField['sName'] . '</a></div>','type' => $aField['sType'],'label' => $aField['sLabel']
            );
            break;
    }

}

array_shift( $aRows );
$result['totalCount'] = count( $aRows );
$result['data'] = $aRows;
print G::json_encode( $result );

