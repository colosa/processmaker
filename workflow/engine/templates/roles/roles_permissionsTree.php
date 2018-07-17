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
	 * @LastModification none
	 */

	$ROL_UID = $_GET['ROL_UID'];
	global $RBAC;
	$oDataset = $RBAC->getRolePermissions($ROL_UID);
	$roleCode = $RBAC->getRoleCode($ROL_UID);


	$tree = new PmTree();
	$tree->name = 'Users';
	$tree->nodeType = "base";
	$tree->width = "350px";
	$tree->value = '
	<div class="boxTopBlue"><div class="a"></div><div class="b"></div><div class="c"></div></div>
	<div class="boxContentBlue">
  		<table width="100%" style="margin:0px;" cellspacing="0" cellpadding="0">
  			<tr>
	  			<td class="userGroupTitle">' . G::LoadTranslation('ID_PERMISSIONS_FOR_THE_ROL') . ': '.$roleCode.'</td>
  			</tr>
		</table>
	</div>
	<div class="boxBottomBlue"><div class="a"></div><div class="b"></div><div class="c"></div></div>
	<div class="userGroupLink"><a href="#" onclick="showPermissions(\''.$_GET['ROL_UID'].'\');return false;">'.G::LoadTranslation('ID_ASSIGN').'</a></div>';

	$tree->showSign = false;

	$oDataset->next();
	while ($aRow = $oDataset->getRow()) {
	    $ID_REMOVE = G::LoadTranslation('ID_REMOVE');

	    $CODE = $aRow['PER_CODE'];
	    $UID = $aRow['PER_UID'];
	    if($ROL_UID != "00000000000000000000000000000002") { #because the admin remove permitions it doesn't posible
			$refer = "<a href=\"javascript:deletePermissionRole('{$ROL_UID}','{$UID}');\">{$ID_REMOVE}</a>";
		} else {
			$refer = "<font color='#CFCFCF'>{$ID_REMOVE}</font>";
		}

	    $html = "
	      <table cellspacing='0' cellpadding='0' border='1' style='border:0px;'>
	        <tr>
	          <td width='250px' class='treeNode' style='border:0px;background-color:transparent;'>{$CODE}</td>
	          <td class='treeNode' style='border:0px;background-color:transparent;'>[$refer]</td>
	        </tr>
	      </table>";

	    $ch = &$tree->addChild('', $html, array('nodeType' => 'child'));
	    $ch->point = '<img src="/images/users.png" />';

	    $oDataset->next();
	}


	print ($tree->render());
