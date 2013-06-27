<?php
/**
 * triggers_Tree.php
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

try {
	G::LoadClass('tree');
	G::LoadClass('processMap');
  //call plugin
  $oPluginRegistry = &PMPluginRegistry::getSingleton();
  $externalSteps   = $oPluginRegistry->getSteps();

	$oProcessMap = new ProcessMap();
  $oTree           = new Tree();
  $oTree->nodeType = 'blank';
  $oTree->name     = 'Triggers';
  $oTree->showSign = false;
	$oCriteria = new Criteria('workflow');
	$oCriteria->add(StepPeer::PRO_UID, $_SESSION['PROCESS']);
  $oCriteria->add(StepPeer::TAS_UID, $_SESSION['TASK']);
  $oCriteria->addAscendingOrderByColumn(StepPeer::STEP_POSITION);
  $oDataset = StepPeer::doSelectRS($oCriteria);
  $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
  $oDataset->next();
  $i = 0;
  while ($aRow = $oDataset->getRow()) {
  	switch ($aRow['STEP_TYPE_OBJ']) {
  		case 'DYNAFORM':
  		  require_once 'classes/model/Dynaform.php';
  		  $oObject           = new Dynaform();
  		  $aFields           = $oObject->load($aRow['STEP_UID_OBJ']);
  		  $aRow['STEP_NAME'] = $aFields['DYN_TITLE'];
  		break;
  		case 'INPUT_DOCUMENT':
  		  require_once 'classes/model/InputDocument.php';
  		  $oObject           = new InputDocument();
  		  $aFields           = $oObject->load($aRow['STEP_UID_OBJ']);
  		  $aRow['STEP_NAME'] = $aFields['INP_DOC_TITLE'];
  		break;
  		case 'OUTPUT_DOCUMENT':
  		  require_once 'classes/model/OutputDocument.php';
  		  $oObject           = new OutputDocument();
  		  $aFields           = $oObject->load($aRow['STEP_UID_OBJ']);
  		  $aRow['STEP_NAME'] = $aFields['OUT_DOC_TITLE'];
  		break;
   		case 'EXTERNAL':
        $aRow['STEP_NAME'] = 'unknown ' . $aRow['STEP_UID'];
   		  foreach ( $externalSteps as $key=>$val ) {
   		  	if ( $val->sStepId == $aRow['STEP_UID_OBJ'] )
   		  	  $aRow['STEP_NAME'] = $val->sStepTitle;
    		  }
    		break;
  	}
    $oCriteria  = $oProcessMap->getStepTriggersCriteria($aRow['STEP_UID'], $_SESSION['TASK'], 'BEFORE');
    $iCantidad1 = StepTriggerPeer::doCount($oCriteria);
    $oCriteria  = $oProcessMap->getStepTriggersCriteria($aRow['STEP_UID'], $_SESSION['TASK'], 'AFTER');
    $iCantidad2 = StepTriggerPeer::doCount($oCriteria);
    $oNode             =& $oTree->addChild($aRow['STEP_UID'], '&nbsp;&nbsp;<span onclick="tree.expand(this.parentNode);" style="cursor: pointer;">' . $aRow['STEP_NAME'] . ' - ' . G::LoadTranslation('ID_TRIGGERS'). ' (<span id="TRIG_'.$aRow['STEP_UID'] . '">' . ($iCantidad1 + $iCantidad2) . '</span>)' . '</span>', array('nodeType'=>'parent'));
    $oNode->contracted = true;
    $oAux1             =& $oNode->addChild('before_node', '<span onclick="tree.expand(this.parentNode);showTriggers(\'' . $aRow['STEP_UID'] . '\', \'BEFORE\');" style="cursor: pointer;">' . G::LoadTranslation('ID_BEFORE') . ' - ' . G::LoadTranslation('ID_TRIGGERS'). ' (<span id="TRIG_'.$aRow['STEP_UID'].'_BEFORE">'. $iCantidad1 .'</span>) </span>', array('nodeType'=>'parent'));
    $oAux1->plus       = "<span  style='cursor:pointer;display:block;width:15;height:10px;' onclick='tree.expand(this.parentNode);showTriggers(\"" . $aRow['STEP_UID'] . "\", \"BEFORE\");'></span>";
    $oAux1->contracted = true;
    $oAux2             =& $oAux1->addChild($aRow['STEP_UID'] . '_before_node', '<span id="triggersSpan_' . $aRow['STEP_UID'] . '_BEFORE"></span>', array('nodeType'=>'parentBlue'));
    $oAux1             =& $oNode->addChild('after_node', '<span onclick="tree.expand(this.parentNode);showTriggers(\'' . $aRow['STEP_UID'] . '\', \'AFTER\');" style="cursor: pointer;">' . G::LoadTranslation('ID_AFTER') . ' - ' . G::LoadTranslation('ID_TRIGGERS'). ' (<span id="TRIG_'.$aRow['STEP_UID'].'_AFTER">'. $iCantidad2 .'</span>) </span>', array('nodeType'=>'parent'));
    $oAux1->plus       = "<span  style='cursor:pointer;display:block;width:15;height:10px;' onclick='tree.expand(this.parentNode);showTriggers(\"" . $aRow['STEP_UID'] . "\", \"AFTER\");'></span>";
    $oAux1->contracted = true;
    $oAux2             =& $oAux1->addChild($aRow['STEP_UID'] . '_after_node', '<span id="triggersSpan_' . $aRow['STEP_UID'] . '_AFTER"></span>', array('nodeType'=>'parentBlue'));
  	$oDataset->next();
  }
  $oCriteria  = $oProcessMap->getStepTriggersCriteria(-1, $_SESSION['TASK'], 'BEFORE');
  $iCantidad1 = StepTriggerPeer::doCount($oCriteria);
  $oCriteria  = $oProcessMap->getStepTriggersCriteria(-2, $_SESSION['TASK'], 'BEFORE');
  $iCantidad2 = StepTriggerPeer::doCount($oCriteria);
  $oCriteria  = $oProcessMap->getStepTriggersCriteria(-2, $_SESSION['TASK'], 'AFTER');
  $iCantidad3 = StepTriggerPeer::doCount($oCriteria);

  $oNode             =& $oTree->addChild('-1', '&nbsp;&nbsp;<span onclick="tree.expand(this.parentNode);" style="cursor: pointer;">[<b> ' . G::LoadTranslation('ID_ASSIGN_TASK') . ' </b>] ' . ' - ' . G::LoadTranslation('ID_TRIGGERS'). ' (<span id="TRIG_'.$aRow['STEP_UID'] . '">' . ($iCantidad1 + $iCantidad2 + $iCantidad3) . '</span>)' . '</span>', array('nodeType'=>'parent'));
  $oNode->contracted = true;
  $oAux1             =& $oNode->addChild('before_node', '<span onclick="tree.expand(this.parentNode);showTriggers(\'-1\', \'BEFORE\');" style="cursor: pointer;">' . G::LoadTranslation('ID_BEFORE_ASSIGNMENT') . ' - ' . G::LoadTranslation('ID_TRIGGERS'). ' (<span id="TRIG_-1_BEFORE">'. $iCantidad1 .'</span>) </span>', array('nodeType'=>'parent'));
  $oAux1->plus       = "<span  style='cursor:pointer;display:block;width:15;height:10px;' onclick='tree.expand(this.parentNode);showTriggers(\"-1\", \"BEFORE\");'></span>";
  $oAux1->contracted = true;
  $oAux2             =& $oAux1->addChild('-1_before_node', '<span id="triggersSpan_-1_BEFORE"></span>', array('nodeType'=>'parentBlue'));
  $oAux1             =& $oNode->addChild('before_node', '<span onclick="tree.expand(this.parentNode);showTriggers(\'-2\', \'BEFORE\');" style="cursor: pointer;">' . G::LoadTranslation('ID_BEFORE_DERIVATION') . ' - ' . G::LoadTranslation('ID_TRIGGERS'). ' (<span id="TRIG_-2_BEFORE">'. $iCantidad2 .'</span>) </span>', array('nodeType'=>'parent'));
  $oAux1->plus       = "<span  style='cursor:pointer;display:block;width:15;height:10px;' onclick='tree.expand(this.parentNode);showTriggers(\"-2\", \"BEFORE\");'></span>";
  $oAux1->contracted = true;
  $oAux2             =& $oAux1->addChild('-2_before_node', '<span id="triggersSpan_-2_BEFORE"></span>', array('nodeType'=>'parentBlue'));
  $oAux1             =& $oNode->addChild('after_node', '<span onclick="tree.expand(this.parentNode);showTriggers(\'-2\', \'AFTER\');" style="cursor: pointer;">' . G::LoadTranslation('ID_AFTER_DERIVATION') . ' - ' . G::LoadTranslation('ID_TRIGGERS'). ' (<span id="TRIG_-2_AFTER">'. $iCantidad3 .'</span>) </span>', array('nodeType'=>'parent'));
  $oAux1->plus       = "<span  style='cursor:pointer;display:block;width:15;height:10px;' onclick='tree.expand(this.parentNode);showTriggers(\"-2\", \"AFTER\");'></span>";
  $oAux1->contracted = true;
  $oAux2             =& $oAux1->addChild('-2_after_node', '<span id="triggersSpan_-2_AFTER"></span>', array('nodeType'=>'parentBlue'));

  $javascript = "
  <script type=\"text/javascript\">
  //Add css Codemirror
  var head = document.getElementsByTagName(\"head\")[0];
  var s = document.createElement(\"link\");

  s.setAttribute(\"href\", \"/js/codemirror/lib/codemirror.css\");
  s.setAttribute(\"type\", \"text/css\");
  s.setAttribute(\"rel\", \"stylesheet\");
  head.appendChild(s);

  var s = document.createElement(\"link\");
  s.setAttribute(\"href\", \"/js/codemirror/addon/hint/show-hint.css\");
  s.setAttribute(\"type\", \"text/css\");
  s.setAttribute(\"rel\", \"stylesheet\");
  head.appendChild(s);
  </script>
  ";

  echo $javascript . $oTree->render();
}
catch (Exception $oException) {
	die($oException->getMessage());
}
unset($_SESSION['PROCESS']);
?>