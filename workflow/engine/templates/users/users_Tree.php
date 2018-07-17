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
	
	require_once 'classes/model/Users.php';
	
	$oCriteria = new Criteria();
	$oCriteria->addSelectColumn(UsersPeer::USR_FIRSTNAME);
	$oCriteria->addSelectColumn(UsersPeer::USR_LASTNAME);
	$oCriteria->add(UsersPeer::USR_UID, $_GET['sUserUID']);
	$oDataset = UsersPeer::doSelectRS($oCriteria);
	$oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
	$oDataset->next();
	$aRow = $oDataset->getRow();
	
	$groups = new Groups();
	
	$tree = new PmTree();
	$tree->name = 'Users';
	$tree->nodeType = "base";
	$tree->width = "350px";
	$tree->value = '
	<div class="boxTopBlue"><div class="a"></div><div class="b"></div><div class="c"></div></div>
	<div class="boxContentBlue">
  		<table width="100%" style="margin:0px;" cellspacing="0" cellpadding="0">
  			<tr>
	  			<td class="userGroupTitle">' . G::loadTranslation("ID_USER_GROUPS") . ' ' . $aRow['USR_FIRSTNAME'] . ' ' . $aRow['USR_LASTNAME'] . '</td>
  			</tr>
		</table>
	</div>
	<div class="boxBottomBlue"><div class="a"></div><div class="b"></div><div class="c"></div></div>
	<div class="userGroupLink"><a href="#" onclick="showUserGroupInterface(\''.$_GET['sUserUID'].'\');return false;">'.G::LoadTranslation('ID_ASSIGN_GROUP').'</a></div>';
	
	$tree->showSign = false;
	$allGroups = $groups->getUserGroups($_GET['sUserUID']);
	
	foreach ($allGroups as $group) {
	    $ID_DELETE = G::LoadTranslation('ID_DELETE');
	    $groupUID = htmlentities($group->getGrpUid());
	    $userUID = $_GET['sUserUID'];
	    $GROUP_TITLE = strip_tags($group->getGrpTitle());
	    $html = <<< innerHTML
	      <table cellspacing='0' cellpadding='0' border='1' style='border:0px;'>
	        <tr>
	          <td width='250px' class='treeNode' style='border:0px;background-color:transparent;'>{$GROUP_TITLE}</td>	
	          <td class='treeNode' style='border:0px;background-color:transparent;'>[<a href="#" onclick="deleteGroup('{$groupUID}','{$userUID}');return false;">{$ID_DELETE}</a>]</td>
	        </tr>
	      </table>
innerHTML;
	    $ch = &$tree->addChild($group->getGrpUid(), $html, array('nodeType' => 'child'));
	    $ch->point = '<img src="/images/users.png" />';
	}
	if($groups->getNumberGroups($_GET['sUserUID']) == 0){
		$ch = &$tree->addChild('', G::LoadTranslation('ID_MSG_NORESULTS_USERGROUP'), array('nodeType' => 'child'));
	}
	
	print ($tree->render());
