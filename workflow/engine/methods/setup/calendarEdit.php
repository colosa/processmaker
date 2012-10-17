<?php
/**
 * calendarEdit.php
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
if ($RBAC->userCanAccess( 'PM_SETUP' ) != 1 && $RBAC->userCanAccess( 'PM_SETUP_ADVANCE' ) != 1) {
    G::SendTemporalMessage( 'ID_USER_HAVENT_RIGHTS_PAGE', 'error', 'labels' );
    //G::header('location: ../login/login');
    die();
}

$G_MAIN_MENU = 'processmaker';
$G_SUB_MENU = 'setup';
$G_ID_MENU_SELECTED = 'SETUP';
$G_ID_SUB_MENU_SELECTED = 'CALENDAR';

$CalendarUid = str_replace( '"', '', isset( $_GET['id'] ) ? $_GET['id'] : G::GenerateUniqueID() );
//TODO: Add validation before save for all fields
G::LoadClass( 'calendar' );
$calendarObj = new calendar();
if ((isset( $_GET['id'] )) && ($_GET['id'] != "")) {
    $fields = $calendarObj->getCalendarInfoE( $CalendarUid );
    $fields['OLD_NAME'] = $fields['CALENDAR_NAME'];
}

if (! (isset( $fields['CALENDAR_UID'] ))) { //For a new Calendar
    $fields['CALENDAR_UID'] = $CalendarUid;
    $fields['OLD_NAME'] = '';
    //Default Business Hour
    $fields['BUSINESS_DAY'][1]['CALENDAR_BUSINESS_DAY'] = 7;
    $fields['BUSINESS_DAY'][1]['CALENDAR_BUSINESS_START'] = "09:00";
    $fields['BUSINESS_DAY'][1]['CALENDAR_BUSINESS_END'] = "17:00";
}
if ((isset( $_GET['cp'] )) && ($_GET['cp'] == 1)) { // Copy Calendar
    $fields['CALENDAR_UID'] = G::GenerateUniqueID();
    $fields['CALENDAR_NAME'] = G::LoadTranslation( "ID_COPY_OF" ) . " " . $fields['CALENDAR_NAME'];
}
$G_PUBLISH = new Publisher();
$G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'setup/calendarEdit', '', $fields, 'calendarSave' );
G::RenderPage( 'publishBlank', 'blank' );

