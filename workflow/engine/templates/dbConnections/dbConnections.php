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

G::LoadClass('tree');
G::LoadClass('net');

$width_content = '430px';

$html = '
 <div class="boxTopBlue"><div class="a"></div><div class="b"></div><div class="c"></div></div>
 <div class="boxContentBlue">
	 <table style="margin:0px;" cellspacing="0" cellpadding="0">
		 <tr>
			 <td class="userGroupTitle"><center>'.G::loadTranslation('DBCONNECTIONS_TITLE').'</center></td>
		 </tr>
	 </table>
 </div>
 <div class="boxBottomBlue"><div class="a"></div><div class="b"></div><div class="c"></div></div>
 ';

$flagTns = ($_POST["type"] == "oracle" && $_POST["connectionType"] == "TNS")? 1 : 0;

if ($flagTns == 0) {
    $host = new NET($_POST["server"]);

    $port = $_POST["port"];

    if ($port == "default") {
        //setting defaults ports
        switch ($_POST["type"]) {
            case "mysql":  $port = 3306; break;
            case "pgsql":  $port = 5432; break;
            case "mssql":  $port = 1433; break;
            case "oracle": $port = 1521; break;
        }

        $_POST["port"] = $port;
        $port = "default ($port)";
    }

    $tests = array(
        "",
        G::loadTranslation('ID_HOST_NAME').'  <b>'.$_POST['server'].'</b>',
        G::loadTranslation('ID_CHECK_PORT').'  <b>'.$port.'</b>',
        G::loadTranslation('ID_CONNECT_HOST').'  <b>'.$host->ip.':'.$_POST['port'].'</b>',
        G::loadTranslation('ID_OPEN_DB').'['.$_POST['db_name'].'] '.G::loadTranslation('ID_IN').'  '.$_POST['type'].' '.G::loadTranslation('ID_SERVICE')
    );
} else {
    $tests = array(
        "",
        "Test TNS" . " <strong>" . $_POST["tns"] . "</strong>"
    );
}

 $n = Array('','uno','dos','tres','cuatro','cinco');

 for($i=1; $i<count($tests);$i++)
 {
 	$html .= "
 	<div id='test_$i' style='display:none'>
 	<table width='100%' cellspacing='0' cellpadding='0' border='1' style='border:1px;'>
 		<tr>
 			<td width='10px' class='treeNode' style='border:0px;background-color:transparent;'>
 				<IMG src=\"/images/".$n[$i].".gif\" width=\"25\" height=\"25\" align=\"left\" border=\"0\">
 			</td>
 			<td width='410px' class='treeNode' style='border:0px;background-color:transparent;'>
 			<div id='action_$i'>$tests[$i]</div>
 			</td>
 		</tr>
 		<tr>
 			<td width='10px' class='treeNode' style='border:0px;background-color:transparent;'>
 			</td>
 			<td  class='treeNode' style='border:0px;background-color:transparent;'>
 				<div id='status_$i'></div>
 			</td>
 		</tr>
 	</table>
 	</div>";
 }

 echo '<div class="grid" style="width:'.$width_content.'">
 <div class="boxTop"><div class="a"></div><div class="b"></div><div class="c"></div></div>
 <div class="content" style="">
 	  <table >
       <tbody><tr>
         <td valign="top">
            '.$html.'
         </td>
       </tr>
     </tbody></table>
 </div>
 <div class="boxBottom"><div class="a"></div><div class="b"></div><div class="c"></div></div>
 </div>';


 print ("<div id='bnt_abort' style='display:block'><input type=button class='module_app_button___gray' onclick='jvascript:abortTestConnection()' value='ABORT'></div>");
 print ("<div id='bnt_ok' style='display:none'><input type=button class='module_app_button___gray' onclick='jvascript:cancelTestConnection()' value='DONE'></div>");
