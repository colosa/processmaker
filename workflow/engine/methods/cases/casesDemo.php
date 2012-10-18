<?php
/**
 * casesDemo.php
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

$rows[] = array ( 'uid' => 'char', 'name' => 'char', 'age' => 'integer', 'balance' => 'float' );
$rows[] = array ( 'uid' => 11, 'name' => 'john',   'age' => 44, 'balance' => 123423 );
$rows[] = array ( 'uid' => 22, 'name' => 'bobby',  'age' => 33, 'balance' => 23456 );
$rows[] = array ( 'uid' => 33, 'name' => 'Dan',    'age' => 22, 'balance' => 34567 );
$rows[] = array ( 'uid' => 33, 'name' => 'Mike',   'age' => 21, 'balance' => 4567 );
$rows[] = array ( 'uid' => 44, 'name' => 'Paul',   'age' => 22, 'balance' => 567 );
$rows[] = array ( 'uid' => 55, 'name' => 'Will',   'age' => 23, 'balance' => 67 );
$rows[] = array ( 'uid' => 66, 'name' => 'Ernest', 'age' => 24, 'balance' => 7 );
$rows[] = array ( 'uid' => 77, 'name' => 'Albert', 'age' => 25, 'balance' => 84567 );
$rows[] = array ( 'uid' => 88, 'name' => 'Sue',    'age' => 26, 'balance' => 94567 );
$rows[] = array ( 'uid' => 99, 'name' => 'Freddy', 'age' => 22, 'balance' => 04567 );

$_DBArray['user'] = $rows;
$_SESSION['_DBArray'] = $_DBArray;
//krumo ( $_DBArray );

    G::LoadClass( 'ArrayPeer');
    $c = new Criteria ('dbarray');
    $c->setDBArrayTable('user');
//    $c->add ( 'user.age', 22 , Criteria::GREATER_EQUAL );
//    $c->add ( 'user.age', 22 , Criteria::EQUAL );
    $c->add ( 'user.name', '%au%' , Criteria::LIKE );
//    $c->add ( 'user.balance', 3456 , Criteria::GREATER_EQUAL );
    $c->addAscendingOrderByColumn ('name');

  $G_MAIN_MENU            = 'processmaker';
  $G_ID_MENU_SELECTED     = 'CASES';
  $G_PUBLISH = new Publisher;
//  $G_PUBLISH->AddContent( 'propeltable', 'paged-table', 'cases/casesDemo', $c );
	//$G_PUBLISH->AddContent('smarty', 'cases/casesDemo', '', '', $Fields);
//  G::RenderPage( "publish" );
//die;

/* Includes */
G::LoadClass('pmScript');
G::LoadClass('case');
G::LoadClass('derivation');
$oCase     = new Cases ();
$appUid = isset ($_SESSION['APPLICATION']) ? $_SESSION['APPLICATION'] : '';
$appFields = $oCase->loadCase( $appUid );

$Fields['APP_UID']       = $appFields['APP_UID'];
$Fields['APP_NUMBER']    = $appFields['APP_NUMBER'];
$Fields['APP_STATUS']    = $appFields['APP_STATUS'];
$Fields['STATUS']        = $appFields['STATUS'];
$Fields['APP_TITLE']     = $appFields['TITLE'];
$Fields['PRO_UID']       = $appFields['PRO_UID'];
$Fields['APP_PARALLEL']  = $appFields['APP_PARALLEL'];
$Fields['APP_INIT_USER'] = $appFields['APP_INIT_USER'];
$Fields['APP_CUR_USER']  = $appFields['APP_CUR_USER'];
$Fields['APP_DATA']      = $appFields['APP_DATA'];
$Fields['CREATOR']       = $appFields['CREATOR'];
$Fields['APP_PIN']       = $appFields['APP_PIN'];
$Fields['APP_PROC_CODE'] = $appFields['APP_PROC_CODE'];

$Fields['PRO_TITLE'] = Content::load ( 'PRO_TITLE', '', $appFields['PRO_UID'], SYS_LANG );
$oUser = new Users();
$oUser->load( $appFields['APP_CUR_USER'] );
$Fields['CUR_USER']     = $oUser->getUsrFirstname() . ' ' . $oUser->getUsrLastname();

$threads     = $oCase->GetAllThreads ($appFields['APP_UID']);
$Fields['THREADS']  = $threads;
$Fields['CANT_THREADS']  = count($threads);

$Fields['CANT_APP_DATA'] = count($Fields['APP_DATA']);
$delegations = $oCase->GetAllDelegations ($appFields['APP_UID']);
foreach ( $delegations as $key => $val ) {
  $delegations[$key]['TAS_TITLE'] = Content::load ( 'TAS_TITLE', '', $val['TAS_UID'], SYS_LANG );
  if ($val['USR_UID'] != -1) {
    $oUser->load( $val['USR_UID'] );
    $delegations[$key]['USR_NAME'] = $oUser->getUsrFirstname() . ' ' . $oUser->getUsrLastname();
  }
  else {
    $delegations[$key]['USR_NAME'] = 'Unknow user (Sub-Process User)';
  }
}
$Fields['CANT_DELEGATIONS']  = count($delegations);
$Fields['DELEGATIONS']  = $delegations;

require_once 'classes/model/AppDelay.php';
$oCriteria = new Criteria('workflow');
$oCriteria->addSelectColumn(AppDelayPeer::APP_THREAD_INDEX);
$oCriteria->addSelectColumn(AppDelayPeer::APP_DEL_INDEX);
$oCriteria->addSelectColumn(AppDelayPeer::APP_TYPE);
$oCriteria->addSelectColumn(AppDelayPeer::APP_STATUS);
$oCriteria->addSelectColumn(AppDelayPeer::APP_ENABLE_ACTION_USER);
$oCriteria->addSelectColumn(AppDelayPeer::APP_ENABLE_ACTION_DATE);
$oCriteria->addSelectColumn(AppDelayPeer::APP_DISABLE_ACTION_USER);
$oCriteria->addSelectColumn(AppDelayPeer::APP_DISABLE_ACTION_DATE);
$oCriteria->add(AppDelayPeer::APP_UID, $appUid);
$oCriteria->addAscendingOrderByColumn(AppDelayPeer::APP_TYPE);
$oCriteria->addAscendingOrderByColumn(AppDelayPeer::APP_ENABLE_ACTION_DATE);
$oDataset = AppDelayPeer::doSelectRS($oCriteria);
$oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
$oDataset->next();
$aDelays = array();
while ($aRow = $oDataset->getRow()) {
  $aDelays[] = $aRow;
  $oDataset->next();
}
$Fields['DELAYS'] = $aDelays;
$Fields['CANT_DELAYS'] = count($aDelays);

require_once 'classes/model/SubApplication.php';
$oCriteria = new Criteria('workflow');
$oCriteria->addSelectColumn(SubApplicationPeer::APP_UID );
$oCriteria->addSelectColumn(SubApplicationPeer::APP_PARENT );
$oCriteria->addSelectColumn(SubApplicationPeer::DEL_INDEX_PARENT );
$oCriteria->addSelectColumn(SubApplicationPeer::DEL_THREAD_PARENT);
$oCriteria->addSelectColumn(SubApplicationPeer::SA_STATUS );
$oCriteria->addSelectColumn(SubApplicationPeer::SA_INIT_DATE );
$oCriteria->addSelectColumn(SubApplicationPeer::SA_FINISH_DATE);
$oCriteria->addSelectColumn(ApplicationPeer::APP_NUMBER);
$oCriteria->add(SubApplicationPeer::APP_UID, $appUid);
$oCriteria->addJoin(ApplicationPeer::APP_UID, SubApplicationPeer::APP_PARENT, Criteria::LEFT_JOIN);
$oCriteria->addAscendingOrderByColumn(SubApplicationPeer::APP_UID);
$oDataset = SubApplicationPeer::doSelectRS($oCriteria);
$oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
$oDataset->next();
$aSubprocess = array();
while ($aRow = $oDataset->getRow()) {
  $aSubprocess[] = $aRow;
  $oDataset->next();
}
$Fields['SUBAPPLICATIONS'] = $aSubprocess;
$Fields['CANT_SUBAPPLICATIONS'] = count($aSubprocess);

  /* Render page */
  $G_MAIN_MENU            = 'processmaker';
  $G_ID_MENU_SELECTED     = 'CASES';
  $G_PUBLISH = new Publisher;
  //$G_PUBLISH->AddContent( 'propeltable', 'paged-table', 'cases/casesDemo', $c );
	$G_PUBLISH->AddContent('smarty', 'cases/casesDemo', '', '', $Fields);
  G::RenderPage( "publish" );

}
catch ( Exception $e ){
  $G_PUBLISH = new Publisher;
	$aMessage['MESSAGE'] = $e->getMessage();
  $G_PUBLISH->AddContent('xmlform', 'xmlform', 'login/showMessage', '', $aMessage );
  G::RenderPage('publish');
}

