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

  //G::genericForceLogin( 'WF_MYINFO' , 'login/noViewPage', $urlLogin = 'login/login' );


  $WIDTH_PANEL = 350;

  $filter = new InputFilter();

  $groups = new Groups();

  $allGroups= $groups->getAllGroups();
  $xVar = 1;
  $html = '';
  $htmlGroup = "<table width=\"100%\" class=\"pagedTable\" cellspacing='0' cellpadding='0' border='0' style='border:0px;'>";
  foreach($allGroups as $group) {
  	
  	$RowClass = ($xVar%2==0)? 'Row1': 'Row2';
  	$xVar++;
    $ID_EDIT     = G::LoadTranslation('ID_EDIT');
    $ID_MEMBERS  = G::LoadTranslation('ID_MEMBERS');
    $ID_DELETE   = G::LoadTranslation('ID_DELETE');
    $UID         = htmlentities($group->getGrpUid());
    //$GROUP_TITLE = htmlentities($group->getGrpTitle());
    $GROUP_TITLE = strip_tags($group->getGrpTitle());
    $ID_NEW         = G::LoadTranslation('ID_NEW');
    $ID_GROUPS      = G::loadTranslation("ID_GROUPS");

    $ID_EDIT     = $filter->xssFilterHard($ID_EDIT);
    $ID_MEMBERS  = $filter->xssFilterHard($ID_MEMBERS);
    $ID_DELETE   = $filter->xssFilterHard($ID_DELETE);
    $UID         = $filter->xssFilterHard($UID);
    $GROUP_TITLE = $filter->xssFilterHard($GROUP_TITLE);
    $ID_NEW      = $filter->xssFilterHard($ID_NEW);
    $ID_GROUPS   = $filter->xssFilterHard($ID_GROUPS);

    $htmlGroup   .="
        <tr id=\"{$xVar}\" onclick=\"focusRow(this, 'Selected')\" onmouseout=\"setRowClass(this, '{$RowClass}')\" onmouseover=\"setRowClass(this, 'RowPointer' )\" class=\"{$RowClass}\">
          <td><img src=\"/images/users.png\" border=\"0\" width=\"20\" height=\"20\"/></td>
          <td>{$GROUP_TITLE}</td>
          <td>[<a class=\"normal\" href=\"#\" onclick=\"editGroup('{$UID}');return false;\">{$ID_EDIT}</a>]</td>
          <td>[<a class=\"normal\" href=\"#\" onclick=\"selectGroup('{$UID}');return false;\">{$ID_MEMBERS}</a>]</td>
          <td>[<a  href=\"#\" onclick=\"deleteGroup('{$UID}');return false;\">{$ID_DELETE}</a>]</td>
        </tr>";
  }
  $htmlGroup .= "</table>";
  
  echo '<div class="treeBase" style="width:'.($WIDTH_PANEL).'px">
			<div class="boxTop"><div class="a"></div><div class="b"></div><div class="c"></div></div>
			<div class="content">
			  <table class="treeNode">
		        <tr>
		          <td valign="top">
		            <div class="boxTopBlue"><div class="a"></div><div class="b"></div><div class="c"></div></div>
					<div class="boxContentBlue">
					  <table width="95%" style="margin:0px;" cellspacing="0" cellpadding="0">
					    <tr>
						  <td class="userGroupTitle">'.$ID_GROUPS.'</td>
						</tr>
					  </table>
					</div>
					<div class="boxBottomBlue"><div class="a"></div><div class="b"></div><div class="c"></div></div>
					
				  	<div class="userGroupLink"><a href="#" onclick="addGroup();return false;">'.$ID_NEW.'</a></div>
				  	
				  	<div id="groupsListDiv" style="height:350px; width:'.($WIDTH_PANEL-20).'px; overflow:auto">
				  	  <table class="pagedTableDefault"><tr><td>' 
  					  .$htmlGroup.
				  	 '</td></tr></table>
				  	</div>
		          </td>
		        </tr>
		      </table>
			</div>
			<div class="boxBottom"><div class="a"></div><div class="b"></div><div class="c"></div></div>
		</div>';
  ?>
  <script>
  var screenX = WindowSize();
	wW = screenX[0];
	wH = screenX[1];

	document.getElementById('groupsListDiv').style.height = (wH/100)*70; 
  </script>
