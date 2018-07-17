<?php
/**
 * cases_InformationTree.php
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

$oTree           = new PmTree();
$oTree->nodeType ="blank";
$oTree->name     = 'Information';
$oTree->showSign = false;

$oNode        =& $oTree->addChild('1', '<a class="linkInBlue" href="#" onclick="showProcessMap();return false;">' . G::LoadTranslation('ID_PROCESS_MAP') . '</a>', array('nodeType'=>'parentBlue'));
$oNode->plus  = '';
$oNode->minus = '';
$oNode->point = '';

$oNode        =& $oTree->addChild('2', '<a class="linkInBlue" href="#" onclick="showProcessInformation();return false;">' . G::LoadTranslation('ID_PROCESS_INFORMATION') . '</a>', array('nodeType'=>'parentBlue'));
$oNode->plus  = '';
$oNode->minus = '';
$oNode->point = '';

if ($_SESSION['TASK'] != -1) {
  $oNode        =& $oTree->addChild('3', '<a class="linkInBlue" href="#" onclick="showTaskInformation();return false;">' . G::LoadTranslation('ID_TASK_INFORMATION') . '</a>', array('nodeType'=>'parentBlue'));
  $oNode->plus  = '';
  $oNode->minus = '';
  $oNode->point = '';
}

$oNode        =& $oTree->addChild('4', '<a class="linkInBlue" href="#" onclick="showTransferHistory();return false;">' . G::LoadTranslation('ID_CASE_HISTORY') . '</a>', array('nodeType'=>'parentBlue'));
$oNode->plus  = '';
$oNode->minus = '';
$oNode->point = '';

$oNode        =& $oTree->addChild('6', '<a class="linkInBlue" href="#" onclick="showHistoryMessages();return false;">' . G::LoadTranslation('ID_HISTORY_MESSAGE_CASE') . '</a>', array('nodeType'=>'parentBlue'));
$oNode->plus  = '';
$oNode->minus = '';
$oNode->point = '';

$oNode        =& $oTree->addChild('6', '<a class="linkInBlue" href="#" onclick="showDynaforms();return false;">' . G::LoadTranslation('ID_DYNAFORMS') . '</a>', array('nodeType'=>'parentBlue'));
$oNode->plus  = '';
$oNode->minus = '';
$oNode->point = '';

$oNode        =& $oTree->addChild('5', '<a class="linkInBlue" href="#" onclick="showUploadedDocuments();return false;">' . G::LoadTranslation('ID_UPLOADED_DOCUMENTS') . '</a>', array('nodeType'=>'parentBlue'));
$oNode->plus  = '';
$oNode->minus = '';
$oNode->point = '';

$oNode        =& $oTree->addChild('6', '<a class="linkInBlue" href="#" onclick="showGeneratedDocuments();return false;">' . G::LoadTranslation('ID_GENERATED_DOCUMENTS') . '</a>', array('nodeType'=>'parentBlue'));
$oNode->plus  = '';
$oNode->minus = '';
$oNode->point = '';
/*
$oNode        =& $oTree->addChild('6', '<a class="linkInBlue" href="#" onclick="stages();return false;">' . G::LoadTranslation('ID_STAGES') . '</a>', array('nodeType'=>'parentBlue'));
$oNode->plus  = '';
$oNode->minus = '';
$oNode->point = '';
*/
/*
require_once 'classes/model/Process.php';
$oProcess = new Process();
$Fields = $oProcess->Load( $_SESSION['PROCESS'] );
if($Fields['PRO_DEBUG']==1)
{
		$oNode        =& $oTree->addChild('7', '<a class="linkInBlue" href="../cases/casesDemo">' . G::LoadTranslation('ID_CASEDEMO') . '</a>', array('nodeType'=>'parentBlue'));
		$oNode->plus  = '';
		$oNode->minus = '';
		$oNode->point = '';
}		
*/
echo $oTree->render();
?>
