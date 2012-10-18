<?php
/**
 * data_casesSchedulerLog.php
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

require_once 'classes/model/LogCasesSchedulerPeer.php';
require_once 'classes/model/LogCasesScheduler.php';
G::LoadClass( 'configuration' );

$co = new Configurations();
$config = $co->getConfiguration( 'casesSchedulerLogList', 'pageSize', '', $_SESSION['USER_LOGGED'] );
$limit_size = isset( $config['pageSize'] ) ? $config['pageSize'] : 20;

$start = isset( $_REQUEST['start'] ) ? $_REQUEST['start'] : 0;
$limit = isset( $_REQUEST['limit'] ) ? $_REQUEST['limit'] : $limit_size;
$filter = (isset( $_POST['textFilter'] )) ? $_POST['textFilter'] : '';

$oCriteria = new Criteria( 'workflow' );
$oCriteria->clearSelectColumns();
$oCriteria->addSelectColumn( LogCasesSchedulerPeer::LOG_CASE_UID );
$oCriteria->addSelectColumn( LogCasesSchedulerPeer::PRO_UID );
$oCriteria->addSelectColumn( LogCasesSchedulerPeer::TAS_UID );
$oCriteria->addSelectColumn( LogCasesSchedulerPeer::USR_NAME );
$oCriteria->addSelectColumn( LogCasesSchedulerPeer::EXEC_DATE );
$oCriteria->addSelectColumn( LogCasesSchedulerPeer::EXEC_HOUR );
$oCriteria->addSelectColumn( LogCasesSchedulerPeer::RESULT );
$oCriteria->addSelectColumn( LogCasesSchedulerPeer::SCH_UID );
$oCriteria->addSelectColumn( LogCasesSchedulerPeer::WS_CREATE_CASE_STATUS );
$oCriteria->addSelectColumn( LogCasesSchedulerPeer::WS_ROUTE_CASE_STATUS );

if ($filter != '') {
    $c_or = $oCriteria->getNewCriterion( LogCasesSchedulerPeer::WS_CREATE_CASE_STATUS, '%' . $filter . '%', Criteria::LIKE )->addOr( $oCriteria->getNewCriterion( LogCasesSchedulerPeer::WS_ROUTE_CASE_STATUS, '%' . $filter . '%', Criteria::LIKE ) );
    $oCriteria->add( $c_or );
}

$oDataset = LogCasesSchedulerPeer::doSelectRS( $oCriteria );
$oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );

$addTables = Array ();
while ($oDataset->next()) {
    $addTables[] = $oDataset->getRow();
}
$results = count( $addTables );

$oCriteria->setOffset( $start );
$oCriteria->setLimit( $limit );

$oCriteria->addDescendingOrderByColumn( LogCasesSchedulerPeer::EXEC_DATE );
$oCriteria->addDescendingOrderByColumn( LogCasesSchedulerPeer::EXEC_HOUR );

$oDataset = LogCasesSchedulerPeer::doSelectRS( $oCriteria );
$oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
//$oDataset = LogCasesSchedulerPeer::doSelectRS ( $oCriteria );
//$oDataset->setFetchmode ( ResultSet::FETCHMODE_ASSOC );
$addTables = Array ();
while ($oDataset->next()) {
    $addTables[] = $oDataset->getRow();
}
//$oLogCasesScheduler = new LogCasesScheduler();
//$arrData = $oLogCasesScheduler->getAll();
echo '{results: ' . $results . ', rows: ' . G::json_encode( $addTables ) . '}';

