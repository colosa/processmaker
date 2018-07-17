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
 * @Updated Nov 6th, 2009 bye Eriknyk
 */

    $host = new Net($_POST['srv']);
    $host = $filter->xssFilterHard($host);
    $width_content = '550px';
    $filter = new InputFilter();
    $_POST = $filter->xssFilterHard($_POST);
    $ID_SETUP_MAILCONF_TITLE = $filter->xssFilterHard(G::loadTranslation('ID_SETUP_MAILCONF_TITLE'));
    
	$html = '
	<div class="boxTopBlue"><div class="a"></div><div class="b"></div><div class="c"></div></div>
	<div class="boxContentBlue">
		<table style="margin:0px;" cellspacing="0" cellpadding="0">
			<tr>
				<td class="userGroupTitle"><center>'.$ID_SETUP_MAILCONF_TITLE.'</center></td>
			</tr>
		</table>
	</div>
	<div class="boxBottomBlue"><div class="a"></div><div class="b"></div><div class="c"></div></div>';

	$tests = Array(
		'',
		'Resolve host name '.$_POST['srv'],
		'Checking port <b>'.$_POST['port'].'</b>',
		'Establishing connection to host <b>'.$host->hostname.'</b>'
    );
	$tests[] = 'Login as <b>'.$_POST['account'].'</b> on '.$host->hostname.' SMTP Server';

	
	if($_POST['send_test_mail'] == 'yes'){
		$tests[] = 'Sending a test mail from <b>'.$_POST['account'].'</b> to '.$_POST['mail_to'].'...';
	}	
	$tree->showSign = false;
	$n = Array('','uno','dos','tres','cuatro','cinco');
	for($i=1; $i<count($tests);$i++)
	{
		$html .= "
		<div id='test_$i' style='display:none'>
		<table width='100%' cellspacing='0' cellpadding='0' border='1' style='border:0px;'>
			<tr>
				<td width=10 class='treeNode' style='border:0px;background-color:transparent;'><span id='status_$i'></span></td>
				<td width='*' class='treeNode' style='border:0px;background-color:transparent;'>
				  &nbsp;<span id='action_$i'>$tests[$i]</span>
				</td>
			</tr>
			<tr>
				<td width='*' class='treeNode' style='border:0px;background-color:transparent;' colspan='2'>
				  <span id='status2_$i'></span>
				</td>
			</tr>
		</table>
		</div>";
	}
	echo '<div class="grid" style="width:'.$width_content.'">
	<div class="boxTop"><div class="a"></div><div class="b"></div><div class="c"></div></div>
	<div class="content" style="">
		  <table width="90%">
	      <tbody><tr>
	        <td valign="top">
	           '.$html.'
	        </td>
	      </tr>
	    </tbody></table>
	</div>
	<div class="boxBottom"><div class="a"></div><div class="b"></div><div class="c"></div></div>
	</div>';
	print ("<div id='bnt_ok' style='display:none'><input type=button class='module_app_button___gray' onclick='jvascript:cancelTestConnection()' value='DONE'></div>");
