<?php
/**
 * varsAjax.php
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
 *
 */
$_SERVER["QUERY_STRING"] = isset($_SERVER["QUERY_STRING"])?$_SERVER["QUERY_STRING"]:'';
$_REQUEST["sProcess"] = isset($_REQUEST["sProcess"])?$_REQUEST["sProcess"]:'';
$_REQUEST["sFieldName"] = isset($_REQUEST["sFieldName"])?$_REQUEST["sFieldName"]:'';
$_REQUEST['sSymbol']= isset($_REQUEST["sSymbol"])?$_REQUEST["sSymbol"]:'';

$html = '<form action="uploader.php?'.$_SERVER["QUERY_STRING"].'&q=upload" onLoad="onLoad()" method="post" enctype="multipart/form-data" onsubmit="">';
$html .= '<div id="d_variables">';
$html .= '<table width="90%" align="center">';

$html .= '<tr style="display:none; visibility:hidden;">';
$html .= '<td colspan="3">';
$html .= '<input type="hidden" id="process" value="'.$_REQUEST['sProcess'].'">';
$html .= '<input type="hidden" id="selectedField" name="selectedField" value="'.$_REQUEST['sFieldName'].'"/> ';
$html .= '</td>';
$html .= '</tr>';

$html .= '<tr>';
$html .= '<td width="50%">';
$html .= '<label for="type_label">'.G::LoadTranslation('ID_TINY_TYPE_VARIABLE').'</label>';
$html .= '</td>';

$html .= '<td width="25%">';
$html .= '<label for="prefix_label">'.G::LoadTranslation('ID_PREFIX').'</label>';
$html .= '</td>';

$html .= '<td width="25%">';
$html .= '<label for="variables_label">'.G::LoadTranslation( 'ID_SEARCH').'</label>';
$html .= '</td>';
$html .= '</tr>';

$html .= '<tr>';
$html .= '<td width="25%">';
$html .= '<select name="type_variables" id="type_variables">';
$html .= '<option value="all">'.G::LoadTranslation( 'ID_TINY_ALL_VARIABLES' ).'</option>';
$html .= '<option value="system">'.G::LoadTranslation( 'ID_TINY_SYSTEM_VARIABLES' ).'</option>';
$html .= '<option value="process">'.G::LoadTranslation( 'ID_TINY_PROCESS_VARIABLES' ).'</option>';
$html .= '</select> &nbsp;&nbsp;&nbsp;&nbsp;';
$html .= '</td>';

$html .= '<td width="25%">';
$html .= '<select name="prefix" id="prefix">';

$html .= '<option value="ID_TO_STRING">@@</option>';
$html .= '<option value="ID_TO_FLOAT">@#</option>';
$html .= '<option value="ID_TO_INTEGER">@%</option>';
$html .= '<option value="ID_TO_URL">@?</option>';
$html .= '<option value="ID_SQL_ESCAPE">@$</option>';
$html .= '<option value="ID_REPLACE_WITHOUT_CHANGES">@=</option>';

$html .= '</select> &nbsp;&nbsp;&nbsp;&nbsp;';
$html .= '</td>';

$html .= '<td width="25%" valign="top">';
$html .= '<input type="text" id="search" size="15">';
$html .= '</td>';
$html .= '</tr>';
$html .= '<tr>';
$html .= '<tr><td><label for="prefix_label">'.G::LoadTranslation( 'ID_VARIABLES' ).'</label></td></tr>';
$html .= '<tr>';

$html .= '<td colspan="3">';

G::LoadClass( 'xmlfield_InputPM' );
$aFields = getDynaformsVars( $_REQUEST['sProcess'], true, isset( $_POST['bIncMulSelFields'] ) ? $_POST['bIncMulSelFields'] : 0 );

$displayOption = '';
if (isset($_REQUEST['displayOption'])){
    $displayOption = 'displayOption="'.$_REQUEST['displayOption'].'"';
} else {
    $displayOption = 'displayOption="normal"' ;
}
$html .= '<select name="_Var_Form_" id="_Var_Form_" size="8"  style="width:100%;' . (! isset( $_POST['sNoShowLeyend'] ) ? 'height:170;' : '') . '" '.$displayOption.'>';

foreach ($aFields as $aField) {
    $html .= '<option value="' . $_REQUEST['sSymbol'] . $aField['sName'] . '">' . $_REQUEST['sSymbol'] . $aField['sName'] . ' (' . $aField['sType'] . ')</option>';
}

$aRows[0] = Array ('fieldname' => 'char','variable' => 'char','type' => 'type','label' => 'char');
foreach ($aFields as $aField) {
    $aRows[] = Array ('fieldname' => $_REQUEST['sFieldName'], 'variable' => $_REQUEST['sSymbol'] . $aField['sName'],'variable_label' => '<div class="pm__dynavars"> <a id="dynalink" href=# onclick="insertFormVar(\'' . $_REQUEST['sFieldName'] . '\',\'' . $_REQUEST['sSymbol'] . $aField['sName'] . '\');">' . $_REQUEST['sSymbol'] . $aField['sName'] . '</a></div>','type' => $aField['sType'],'label' => $aField['sLabel']
    );
}
$html .= '</select>';

$html .= '</td>';
$html .= '</tr>';
$html .= '</table>';
$html .= '</div>';

$html .= '<br>';
$html .= '<table border="1" width="90%" align="center">';
$html .= '<tr width="40%">';
$html .= '<td>'.G::LoadTranslation('ID_RESULT').'</td>';
$html .= '<td><span id="selectedVariableLabel">@@SYS_LANG</span></td>';
$html .= '</tr>';
$html .= '<tr width="60%">';
$html .= '<td>'.G::LoadTranslation('ID_DESCRIPTION').'</td>';
$html .= '<td><span id="desc_variables">'.G::LoadTranslation('ID_SYSTEM').'</span></td>';
$html .= '</tr>';
$html .= '</table>';
$html .= '</div>';
$html .= '<br>';
$html .= '<table width="90%" align="center">';
$html .= '<tr><td>';
$html .= '<label for="desc_prefix">*<span id="desc_prefix">' . G::LoadTranslation( 'ID_TO_STRING' ) . '</span></label>';
$html .= '</td></tr>';
$html .= '</div>';

$html .= '</form>';

$display = 'raw';

$G_PUBLISH = new Publisher();
$oHeadPublisher = & headPublisher::getSingleton();
$oHeadPublisher->addScriptFile('/jscore/controls/variablePicker.js');
if (isset($_REQUEST['displayOption'])) {
    if($_REQUEST['displayOption']=='tinyMCE'){
        $display = 'blank';
        $oHeadPublisher->addScriptFile('/js/tinymce/jscripts/tiny_mce/tiny_mce_popup.js');
        $oHeadPublisher->addScriptFile('/js/tinymce/jscripts/tiny_mce/plugins/pmVariablePicker/editor_plugin_src.js');
    }
}

echo $html;

G::RenderPage( 'publish', $display );
