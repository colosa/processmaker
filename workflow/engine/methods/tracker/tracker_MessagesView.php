<?php
/**
 * tracker_MessagesView.php
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
* Messages for Case Tracker
*
* @author Everth S. Berrios Morales <everth@colosa.com>
*
*/
if (! isset( $_SESSION['PROCESS'] )) {
    G::header( 'location: login' );
}
$G_MAIN_MENU = 'caseTracker';
$G_ID_MENU_SELECTED = 'MESSAGES';

G::LoadClass( "case" );
$Fields = Cases::getHistoryMessagesTrackerView( $_GET['APP_UID'], $_GET['APP_MSG_UID'] );

$G_PUBLISH = new Publisher();

$G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'tracker/tracker_MessagesView', '', $Fields );
G::RenderPage( 'publish' );

