<?php
/**
 * eventList.php
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
if ($RBAC->userCanAccess( 'PM_SETUP_ADVANCE' ) != 1) {
    G::SendTemporalMessage( 'ID_USER_HAVENT_RIGHTS_PAGE', 'error', 'labels' );
    G::header( 'location: ../login/login' );
    die();
}

G::LoadClass( 'configuration' );
$c = new Configurations();
$configPage = $c->getConfiguration( 'eventList', 'pageSize', '', $_SESSION['USER_LOGGED'] );
$Config['pageSize'] = isset( $configPage['pageSize'] ) ? $configPage['pageSize'] : 20;

$G_MAIN_MENU = 'processmaker';
$G_SUB_MENU = 'logs';
$G_ID_MENU_SELECTED = 'logs';
$G_ID_SUB_MENU_SELECTED = 'EVENT';

//get values for the comboBoxes
$userUid = (isset( $_SESSION['USER_LOGGED'] ) && $_SESSION['USER_LOGGED'] != '') ? $_SESSION['USER_LOGGED'] : null;
$status = array (array ('',G::LoadTranslation( 'ID_ALL' )
),array ("PENDING",G::LoadTranslation( 'ID_OPEN' )
),array ("COMPLETED",G::LoadTranslation( 'ID_CLOSE' )
)
);
$type = array (array ('',G::LoadTranslation( 'ID_ALL' )
),array ('SEND_MESSAGE',G::LoadTranslation( 'ID_EVENT_MESSAGE' )
),array ('EXECUTE_TRIGGER',G::LoadTranslation( 'ID_EVENT_TIMER' )
),array ('EXECUTE_CONDITIONAL_TRIGGER',G::LoadTranslation( 'ID_EVENT_CONDITIONAL' )
)
);
$processes = getProcessArray( $userUid );

$G_PUBLISH = new Publisher();

$oHeadPublisher = & headPublisher::getSingleton();
$oHeadPublisher->addExtJsScript( 'events/eventList', false ); //adding a javascript file .js
$oHeadPublisher->addContent( 'events/eventList' ); //adding a html file  .html.
//sending the columns to display in grid
$oHeadPublisher->assign( 'typeValues', $type );
$oHeadPublisher->assign( 'statusValues', $status );
$oHeadPublisher->assign( 'processValues', $processes );

function getProcessArray ($userUid)
{
    global $oAppCache;
    require_once ("classes/model/AppCacheView.php");

    $processes = Array ();
    $processes[] = array ('',G::LoadTranslation( 'ID_ALL_PROCESS' ));

    $cProcess = new Criteria( 'workflow' );
    $cProcess->clearSelectColumns();
    $cProcess->addSelectColumn( AppCacheViewPeer::PRO_UID );
    $cProcess->addSelectColumn( AppCacheViewPeer::APP_PRO_TITLE );
    $cProcess->setDistinct( AppCacheViewPeer::PRO_UID );

    $cProcess->addAscendingOrderByColumn( AppCacheViewPeer::APP_PRO_TITLE );

    $oDataset = AppCacheViewPeer::doSelectRS( $cProcess );
    $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
    $oDataset->next();

    while ($aRow = $oDataset->getRow()) {
        $processes[] = array ($aRow['PRO_UID'],$aRow['APP_PRO_TITLE']);
        $oDataset->next();
    }

    return $processes;
}

G::RenderPage( 'publish', 'extJs' );

