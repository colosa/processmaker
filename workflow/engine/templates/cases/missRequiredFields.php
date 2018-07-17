<?php
/**
 * groups_Tree.php
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
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * For more information, contact Colosa Inc, 2566 Le Jeune Rd.,
 * Coral Gables, FL, 33134, USA, or email info@colosa.com.
 *
 */

/**
 * @Description This is the View of all groups from a determinated user
 * @author Erik Amaru Ortiz <erik@colosa.com>
 * @Date 24/04/2008
 * @LastModification 30/05/2008
 */


$filter = new InputFilter();
$_POST = $filter->xssFilterHard($_POST);
$ID_ERROR = $filter->xssFilterHard(G::LoadTranslation('ID_ERROR'));
$ID_REQUIRED_FIELDS_ERROR = $filter->xssFilterHard(G::LoadTranslation('ID_REQUIRED_FIELDS_ERROR'));

    $width_content = '500px';
	
	$html =  '
	<div class="boxTopBlue"><div class="a"></div><div class="b"></div><div class="c"></div></div>
	<div class="boxContentBlue">
  		<table width="100%" style="margin:0px;" cellspacing="0" cellpadding="0">
  			<tr>
	  			<td class="userGroupTitle"><font color="red">'.$ID_ERROR.'! </font> '.$ID_REQUIRED_FIELDS_ERROR.'</td>
  			</tr>
		</table>
	</div>
	<div class="boxBottomBlue"><div class="a"></div><div class="b"></div><div class="c"></div></div>';
	
	$rq = $_POST['req_val'];
	foreach( $rq as $indexRq => $field) {
    if ( is_array($field) ) {
    	foreach ($field as $row => $fieldsRow) {
    		foreach ($fieldsRow as $fieldGrid) {
    			$html .= "<table width='100%' cellspacing='0' cellpadding='0' border='1' style='border:0px;'>
					       <tr><td width='300px' class='treeNode' style='border:0px;background-color:transparent;'>
					       	<font color=black>(*) The field <font color=blue><b>$fieldGrid</b></font> of the row 
					       	<font color=blue><b>$row</b></font> in the grid 
					       	<font color=blue><b>$indexRq</b></font> is required!</font>
					       </td></tr>
					       </table> ";
    		}    		
    	}
    } else {
      $html .= "<table width='100%' cellspacing='0' cellpadding='0' border='1' style='border:0px;'>
			       <tr><td width='300px' class='treeNode' style='border:0px;background-color:transparent;'>
			       	<font color=black>(*) The field <font color=blue><b>$field</b></font> is required!</font>
			       </td></tr>
			       </table> ";
		}
	}

    $netxpage = $_POST['next_step']['PAGE'];
	$previouspage = $_POST['previous_step']['PAGE'];

	$html .= '</td></tr><tr><td align="center">';
	/*----------------------------------********---------------------------------*/

		$html .= '<input type="button" value="Back" class="module_app_button___gray" onclick="javascript:history.back()">';
		$html .= '<input type="button" value="Continue" class="module_app_button___gray" onclick="javascript:location.href=\''.$netxpage.'\'">';

	/*----------------------------------********---------------------------------*/

    echo '<div class="grid" style="width:'.$width_content.'">
	<div class="boxTop"><div class="a"></div><div class="b"></div><div class="c"></div></div>
	<div class="content" style="">
		  <table width="490px">
	      <tbody><tr>
	        <td valign="top">
	           '.$html.'
	        </td>
	      </tr>
	    </tbody></table>
	</div>
	<div class="boxBottom"><div class="a"></div><div class="b"></div><div class="c"></div></div>
	</div>';

	
	
 
	
