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
 */
G::LoadClass( 'xmlfield_InputPM' );
$aFields = getDynaformsVars( $_POST['sProcess'], true, isset( $_POST['bIncMulSelFields'] ) ? $_POST['bIncMulSelFields'] : 0 );

$sHTML = '<select name="_Var_Form_" id="_Var_Form_" size="' . count( $aFields ) . '" style="width:100%;' . (! isset( $_POST['sNoShowLeyend'] ) ? 'height:50%;' : '') . '" ondblclick="insertFormVar(\'' . $_POST['sFieldName'] . '\', this.value);">';
foreach ($aFields as $aField) {
    $sHTML .= '<option value="' . $_POST['sSymbol'] . $aField['sName'] . '">' . $_POST['sSymbol'] . $aField['sName'] . ' (' . $aField['sType'] . ')</option>';
}

$aRows[0] = Array ('fieldname' => 'char','variable' => 'char','type' => 'type','label' => 'char'
);
foreach ($aFields as $aField) {
    $aRows[] = Array ('fieldname' => $_POST['sFieldName'],'variable' => $_POST['sSymbol'] . $aField['sName'],'variable_label' => '<div class="pm__dynavars"> <a id="dynalink" href=# onclick="insertFormVar(\'' . $_POST['sFieldName'] . '\',\'' . $_POST['sSymbol'] . $aField['sName'] . '\');">' . $_POST['sSymbol'] . $aField['sName'] . '</a></div>','type' => $aField['sType'],'label' => $aField['sLabel']
    );
}

$sHTML .= '</select>';
$sHTML = '';

if (! isset( $_POST['sNoShowLeyend'] )) {
    $sHTML = '<table width="100%">';
    $sHTML .= '<tr><td align="center" class="module_app_input___gray" colspan="2"><b>Variables cast prefix</b></td></tr>';
    if (isset( $_POST['sType'] )) {
        $sHTML .= '<tr><td class="module_app_input___gray">' . G::LoadTranslation( 'ID_ESC' ) . '</td></tr>';
        $sHTML .= '<tr><td class="module_app_input___gray">' . G::LoadTranslation( 'ID_NONEC' ) . '</td></tr>';
        /*$sHTML .= '<tr><td class="module_app_input___gray">' . G::LoadTranslation('ID_EURL') . '</td></tr>';
    $sHTML .= '<tr><td class="module_app_input___gray">' . G::LoadTranslation('ID_EVAL') . '</td></tr>';
    $sHTML .= '<tr><td class="module_app_input___gray">' . G::LoadTranslation('ID_ESCJS') . '</td></tr>';
    $sHTML .= '<tr><td class="module_app_input___gray">' . G::LoadTranslation('ID_ESCSJS') . '</td></tr>';
    $sHTML .= '<tr><td class="module_app_input___gray">' . G::LoadTranslation('ID_FUNCTION') . '</td></tr>';*/
    } else {
        $sHTML .= '<tr><td class="module_app_input___gray" width="5%">@@</td><td class="module_app_input___gray">' . G::LoadTranslation( 'ID_TO_STRING' ) . '</td></tr>';
        $sHTML .= '<tr><td class="module_app_input___gray" width="5%">@#</td><td class="module_app_input___gray">' . G::LoadTranslation( 'ID_TO_FLOAT' ) . '</td></tr>';
        $sHTML .= '<tr><td class="module_app_input___gray" width="5%">@%</td><td class="module_app_input___gray">' . G::LoadTranslation( 'ID_TO_INTEGER' ) . '</td></tr>';
        $sHTML .= '<tr><td class="module_app_input___gray" width="5%">@?</td><td class="module_app_input___gray">' . G::LoadTranslation( 'ID_TO_URL' ) . '</td></tr>';
        $sHTML .= '<tr><td class="module_app_input___gray" width="5%">@$</td><td class="module_app_input___gray">' . G::LoadTranslation( 'ID_SQL_ESCAPE' ) . '</td></tr>';
        $sHTML .= '<tr><td class="module_app_input___gray" width="5%">@=</td><td class="module_app_input___gray">' . G::LoadTranslation( 'ID_REPLACE_WITHOUT_CHANGES' ) . '</td></tr>';
    }
    $sHTML .= '<tr><td align="center" class="module_app_input___gray" colspan="2">&nbsp;</td></tr>';
    //$sHTML .= '<tr><td align="center" class="module_app_input___gray" colspan="2">' . G::LoadTranslation('ID_DOCLICK') . '</td></tr>';
    $sHTML .= '</table>';
} else {
    // please don't remove this definition if there isn't some sort of html tags before the css styles aren't loaded in IE
    $sHTML = '<table width="100%">';
    $sHTML .= '</table>';
}
$sStyle = " <style type=\"text/css\">

.pm__dynavars a#dynalink{color:#000000;}

/* begin css tabs */
ul#tabnav { /* general settings */
text-align: left; /* set to left, right or center */
margin: 1em 0 1em 0; /* set margins as desired */
font: bold 11px verdana, arial, sans-serif; /* set font as desired */
border-bottom: 1px solid #ccc; /* set border COLOR as desired */
list-style-type: none;
padding: 3px 10px 3px 10px; /* THIRD number must change with respect to padding-top (X) below */
}

ul#tabnav li { /* do not change */
display: inline;
}

div#all li.all, div#system li.system, div#process li.process, div#tab4 li.tab4 { /* settings for selected tab */
border-bottom: 1px solid #fff; /* set border color to page background color */
background-color: #fff; /* set background color to match above border color */
}

div#all li.all a, div#system li.system a, div#process li.process a, div#tab4 li.tab4 a { /* settings for selected tab link */
background-color: #fff; /* set selected tab background color as desired */
color: #000; /* set selected tab link color as desired */
position: relative;
top: 1px;
padding-top: 4px; /* must change with respect to padding (X) above and below */
}

ul#tabnav li a { /* settings for all tab links */
padding: 3px 4px; /* set padding (tab size) as desired; FIRST number must change with respect to padding-top (X) above */
border: 1px solid #aaa; /* set border COLOR as desired; usually matches border color specified in #tabnav */
background-color: #ccc; /* set unselected tab background color as desired */
color: #666; /* set unselected tab link color as desired */
margin-right: 10px; /* set additional spacing between tabs as desired */
text-decoration: none;
border-bottom: none;
}

ul#tabnav a:hover { /* settings for hover effect */
background: #fff; /* set desired hover color */
}

/* end css tabs */

</style>";
$cssTabs = "<div id=\"all\">
                <ul id=\"tabnav\">
                    <li class=\"all\"><a href=\"#\" onclick=\"changeVariables('all','" . $_POST['sProcess'] . "','" . $_POST['sFieldName'] . "','" . $_POST['sSymbol'] . "','processVariablesContent');\">All variables</a></li>
                    <li class=\"system\"><a href=\"#\" onclick=\"changeVariables('system','" . $_POST['sProcess'] . "','" . $_POST['sFieldName'] . "','" . $_POST['sSymbol'] . "','processVariablesContent');\">System</a></li>
                    <li class=\"process\"><a href=\"#\" onclick=\"changeVariables('process','" . $_POST['sProcess'] . "','" . $_POST['sFieldName'] . "','" . $_POST['sSymbol'] . "','processVariablesContent');\">Process</a></li>
                </ul>
            </div>
            ";
echo $sHTML;
echo $sStyle;

////////////////////////////////////////////////////////


echo "<div id=\"processVariablesContent\">";
echo $cssTabs;
G::LoadClass( 'ArrayPeer' );

global $_DBArray;
$_DBArray['dynavars'] = $aRows;
$_SESSION['_DBArray'] = $_DBArray;

G::LoadClass( 'ArrayPeer' );
$oCriteria = new Criteria( 'dbarray' );
$oCriteria->setDBArrayTable( 'dynavars' );

$aFields = array ();

$G_PUBLISH = new Publisher();
$oHeadPublisher = & headPublisher::getSingleton();
$oHeadPublisher->addScriptFile( "/jscore/controls/varsAjax.js" );
$G_PUBLISH->AddContent( 'propeltable', 'paged-table', 'triggers/dynavars', $oCriteria );
G::RenderPage( 'publish', 'raw' );

echo "</div>";

