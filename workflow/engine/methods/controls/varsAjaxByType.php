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

/**
 * the varAjaxByType file as the varAjax.php handle the render of the diferent
 * types of dynaform variables there are 3 of them system, process, and the default
 * that show both system and process variables.
 * uses almost the same variables passed to varsAjax, plus $_POST['type'] that is the
 * type of the variables. Then it render a propel table with all the variables
 * loaded for that type.
 *
 * @author Gustavo Cruz gustavo-at-colosa.com
 * @param $_POST variables
 */

G::LoadClass( 'xmlfield_InputPM' );
$aFields = getDynaformsVars( $_POST['sProcess'], true, isset( $_POST['bIncMulSelFields'] ) ? $_POST['bIncMulSelFields'] : 0 );
$aType = $_POST['type'];

$aRows[0] = Array ('fieldname' => 'char','variable' => 'char','type' => 'type','label' => 'char'
);
foreach ($aFields as $aField) {
    switch ($aType) {
        case "system":
            if ($aField['sType'] == "system") {
                $aRows[] = Array ('fieldname' => $_POST['sFieldName'],'variable' => $_POST['sSymbol'] . $aField['sName'],'variable_label' => '<div class="pm__dynavars"> <a id="dynalink" href=# onclick="insertFormVar(\'' . $_POST['sFieldName'] . '\',\'' . $_POST['sSymbol'] . $aField['sName'] . '\');">' . $_POST['sSymbol'] . $aField['sName'] . '</a></div>','type' => $aField['sType'],'label' => $aField['sLabel']
                );
            }
            break;
        case "process":
            if ($aField['sType'] != "system") {
                $aRows[] = Array ('fieldname' => $_POST['sFieldName'],'variable' => $_POST['sSymbol'] . $aField['sName'],'variable_label' => '<div class="pm__dynavars"> <a id="dynalink" href=# onclick="insertFormVar(\'' . $_POST['sFieldName'] . '\',\'' . $_POST['sSymbol'] . $aField['sName'] . '\');">' . $_POST['sSymbol'] . $aField['sName'] . '</a></div>','type' => $aField['sType'],'label' => $aField['sLabel']
                );
            }
            break;
        default:
            $aRows[] = Array ('fieldname' => $_POST['sFieldName'],'variable' => $_POST['sSymbol'] . $aField['sName'],'variable_label' => '<div class="pm__dynavars"> <a id="dynalink" href=# onclick="insertFormVar(\'' . $_POST['sFieldName'] . '\',\'' . $_POST['sSymbol'] . $aField['sName'] . '\');">' . $_POST['sSymbol'] . $aField['sName'] . '</a></div>','type' => $aField['sType'],'label' => $aField['sLabel']
            );
            break;
    }

}
// Use and make a load translation variable call to the titles of the tabs
$cssTabs = "<div id=\"" . strtolower( $_POST['type'] ) . "\">
                <ul id=\"tabnav\">
                    <li class=\"all\"><a href=\"#\" onclick=\"changeVariables('all','" . $_POST['sProcess'] . "','" . $_POST['sFieldName'] . "','" . $_POST['sSymbol'] . "','processVariablesContent');\">All variables</a></li>
                    <li class=\"system\"><a href=\"#\" onclick=\"changeVariables('system','" . $_POST['sProcess'] . "','" . $_POST['sFieldName'] . "','" . $_POST['sSymbol'] . "','processVariablesContent');\">System</a></li>
                    <li class=\"process\"><a href=\"#\" onclick=\"changeVariables('process','" . $_POST['sProcess'] . "','" . $_POST['sFieldName'] . "','" . $_POST['sSymbol'] . "','processVariablesContent');\">Process</a></li>
                </ul>
            </div>
            ";

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
$G_PUBLISH->AddContent( 'propeltable', 'paged-table', 'triggers/dynavars', $oCriteria );
G::RenderPage( 'publish', 'raw' );

