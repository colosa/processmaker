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

$tree = new PmTree();
$tree->name = 'Groups';
$tree->nodeType = "base";
$tree->width = "200px";
$tree->contentWidth = "220";
$tree->value = '
	 <div class="boxTopBlue"><div class="a"></div><div class="b"></div><div class="c"></div></div>
	 <div class="boxContentBlue">

	  <table width="100%" style="margin:0px;" cellspacing="0" cellpadding="0">
	  <tr>
		  <td class="userGroupTitle">Steps List</td>
	  </tr>
	</table>
	</div>
	<div class="boxBottomBlue"><div class="a"></div><div class="b"></div><div class="c"></div></div>
	';
$tree->showSign = false;

$o = new Cases();
$steps = $o->getAllDynaformsStepsToRevise($_GET['APP_UID']);
$APP_UID = $_GET['APP_UID'];
$DEL_INDEX = $_GET['DEL_INDEX'];


$html = "
      <table cellspacing='0' cellpadding='0' border='1' style='border:0px;'>
        <tr>
        <td class='treeNode' style='border:0px;background-color:transparent;'><b>Dynaforms<b></td>
        </tr>
      </table>";

        $ch = &$tree->addChild("", $html, array('nodeType' => 'child'));
        $ch->point = '</span><img src="/images/plus.gif" />';
        $i=1;
        $PRO_UID='';
        $DYN_UID='';
foreach ($steps as $step) {
        require_once 'classes/model/Dynaform.php';
        $od = new Dynaform();
        $dynaformF = $od->Load($step['STEP_UID_OBJ']);

        $n = $step['STEP_POSITION'];
		$TITLE = " - ".$dynaformF['DYN_TITLE'];
		$DYN_UID = $dynaformF['DYN_UID'];
		$PRO_UID = $step['PRO_UID'];

        $html = "
      <table cellspacing='0' cellpadding='0' border='1' style='border:0px;'>
        <tr>
        <td class='treeNode' style='border:0px;background-color:transparent;'><span id='focus$i'></td>
        <td class='treeNode' style='border:0px;background-color:transparent;'>&nbsp;&nbsp;$n&nbsp;&nbsp;</td>
          <td class='treeNode' style='border:0px;background-color:transparent;'>
		  	<a href=\"cases_StepToRevise?type=DYNAFORM&ex=$i&PRO_UID=$PRO_UID&DYN_UID=$DYN_UID&APP_UID=$APP_UID&position=".$step['STEP_POSITION']."&DEL_INDEX=$DEL_INDEX\">{$TITLE}</a>
		  </td>
        </tr>
      </table>";

        $ch = &$tree->addChild("", $html, array('nodeType' => 'child'));
        $ch->point = '<img src="/images/ftv2mnode.gif" />';
		$i++;
}

$html = "
      <table cellspacing='0' cellpadding='0' border='1' style='border:0px;'>
        <tr>
          <td class='treeNode' style='border:0px;background-color:transparent;'><span id='focus$i'></td>
          <td class='treeNode' style='border:0px;background-color:transparent;'>
		  	<a href=\"cases_StepToReviseInputs?PRO_UID=$PRO_UID&APP_UID=$APP_UID&DEL_INDEX=$DEL_INDEX\">&nbsp;&nbsp;Input Documents</a>
		  </td>
        </tr>
      </table>";
        $ch = &$tree->addChild("", $html, array('nodeType' => 'child'));
        $ch->point = '</span><img src="/images/plus.gif" />';

$steps = $o->getAllInputsStepsToRevise($_GET['APP_UID']);
//$i=1;

foreach ($steps as $step) {
        require_once 'classes/model/InputDocument.php';
        $od = new InputDocument();
        $IDF = $od->Load($step['STEP_UID_OBJ']);

        $n = $step['STEP_POSITION'];
		$TITLE = " - ".$IDF['INP_DOC_TITLE'];
		$INP_DOC_UID = $IDF['INP_DOC_UID'];
		$PRO_UID = $step['PRO_UID'];

        $html = "
      <table cellspacing='0' cellpadding='0' border='1' style='border:0px;'>
        <tr>
        <td class='treeNode' style='border:0px;background-color:transparent;'><span id='focus$i'></td>
        <td class='treeNode' style='border:0px;background-color:transparent;'>&nbsp;&nbsp;$n&nbsp;&nbsp;</td>
          <td class='treeNode' style='border:0px;background-color:transparent;'>
		  	<a href=\"cases_StepToReviseInputs?type=INPUT_DOCUMENT&ex=$i&PRO_UID=$PRO_UID&INP_DOC_UID=$INP_DOC_UID&APP_UID=$APP_UID&position=".$step['STEP_POSITION']."&DEL_INDEX=$DEL_INDEX\">{$TITLE}</a>
		  </td>
        </tr>
      </table>";

        $ch = &$tree->addChild("", $html, array('nodeType' => 'child'));
        $ch->point = '<img src="/images/ftv2mnode.gif" />';
		$i++;
}

$i++;
$html = "
      <table cellspacing='0' cellpadding='0' border='1' style='border:0px;'>
        <tr>
          <td class='treeNode' style='border:0px;background-color:transparent;'><span id='focus$i'></td>
          <td class='treeNode' style='border:0px;background-color:transparent;'>
		  	<a href='cases_StepToReviseOutputs?ex=$i&PRO_UID=$PRO_UID&DEL_INDEX=$DEL_INDEX&APP_UID=$APP_UID'>&nbsp;&nbsp;Output Documents</a>
		  </td>
        </tr>
      </table>";

        $ch = &$tree->addChild("", $html, array('nodeType' => 'child'));
        $ch->point = '</span><img src="/images/ftv2doc.gif" />';

print ($tree->render());
//
