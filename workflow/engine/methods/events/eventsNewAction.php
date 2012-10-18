<?php

/**
 * events_NewAction.php
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
global $RBAC;
if ($RBAC->userCanAccess( 'PM_SETUP' ) != 1) {
    G::SendTemporalMessage( 'ID_USER_HAVENT_RIGHTS_PAGE', 'error', 'labels' );
    G::header( 'location: ../login/login' );
    die();
}

global $_DBArray;

//get the posted fields of new Event and create a new record of that
require_once 'classes/model/Event.php';

$oEvent = new Event();
$envUId = $oEvent->create( $_POST );

$_SESSION['EVN_UID'] = $envUId;
require_once ('eventsEditAction.php');
die();

/*
//this page is showing the parameters for setup email messages and triggers,
//probably this will be changed soon.

$aTemplates   = array();
$aTemplates[] = array('TEMPLATE1' => 'char',
      	              'TEMPLATE2' => 'char');
$sDirectory = PATH_DATA_MAILTEMPLATES . $_POST['PRO_UID'] . PATH_SEP;
G::verifyPath($sDirectory, true);
$oDirectory   = dir($sDirectory);
while ($sObject = $oDirectory->read()) {
  if (($sObject !== '.') && ($sObject !== '..')) {
    $aTemplates[] = array('TEMPLATE1' => $sObject,
      	                  'TEMPLATE2' => $sObject);
  }
}
$_DBArray['templates'] = $aTemplates;

$aTriggers[] = array('TRI_UID'   => 'char',
      	             'TRI_TITLE' => 'char');
require_once 'classes/model/Triggers.php';
G::LoadClass('processMap');
$oProcessMap = new ProcessMap();
$oDataset = TriggersPeer::doSelectRS($oProcessMap->getTriggersCriteria($_POST['PRO_UID']));
$oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
$oDataset->next();
while ($aRow = $oDataset->getRow()) {
  $aTriggers[] = array('TRI_UID'   => $aRow['TRI_UID'],
      	               'TRI_TITLE' => $aRow['TRI_TITLE']);
  $oDataset->next();
}
$_DBArray['triggers'] = $aTriggers;

$_SESSION['_DBArray'] = $_DBArray;

$G_PUBLISH = new Publisher();
$G_PUBLISH->AddContent('xmlform', 'xmlform', 'events/events_EditAction', '', $_POST, '../events/eventsSave');
G::RenderPage('publish', 'raw');

*/

