<?php

/**
 * tracker_Messages.php
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
/*
 * History messages for Case Tracker
 *
 * @author Everth S. Berrios Morales <everth@colosa.com>
 *
 */
if (! isset( $_SESSION['PROCESS'] )) {
    G::header( 'location: login' );
}
$G_MAIN_MENU = 'caseTracker';
$G_ID_MENU_SELECTED = 'MESSAGES';

$oHeadPublisher->addScriptFile( '/jscore/tracker/tracker.js' );

G::LoadClass( 'case' );
$oCase = new Cases();
$aFields = $oCase->loadCase( $_SESSION['APPLICATION'] );

$idProcess = $_SESSION['PROCESS'];
$oProcess = new Process();
$aProcessFieds = $oProcess->load( $idProcess );
$noShowTitle = 0;
if (isset( $aProcessFieds['PRO_SHOW_MESSAGE'] )) {
    $noShowTitle = $aProcessFieds['PRO_SHOW_MESSAGE'];
}

if (isset( $aFields['TITLE'] )) {
    $aFields['APP_TITLE'] = $aFields['TITLE'];
}
if ($aFields['APP_PROC_CODE'] != '') {
    $aFields['APP_NUMBER'] = $aFields['APP_PROC_CODE'];
}
$aFields['CASE'] = G::LoadTranslation( 'ID_CASE' );
$aFields['TITLE'] = G::LoadTranslation( 'ID_TITLE' );

$G_PUBLISH = new Publisher();
if ($noShowTitle == 0) {
    $G_PUBLISH->AddContent( 'smarty', 'cases/cases_title', '', '', $aFields );
}
$G_PUBLISH->AddContent( 'propeltable', 'paged-table', 'tracker/tracker_Messages', Cases::getHistoryMessagesTracker( $_SESSION['APPLICATION'] ), array ('VIEW' => G::LoadTranslation( 'ID_VIEW' )
) );
G::RenderPage( 'publish' );

