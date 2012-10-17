<?php
/**
 * calendarValidate.php
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
// validating the fields of the Calendar Edit form.
// checking permissions
switch ($RBAC->userCanAccess( 'PM_SETUP_ADVANCE' )) {
    case - 2:
        G::SendTemporalMessage( 'ID_USER_HAVENT_RIGHTS_SYSTEM', 'error', 'labels' );
        G::header( 'location: ../login/login' );
        die();
        break;
    case - 1:
        G::SendTemporalMessage( 'ID_USER_HAVENT_RIGHTS_PAGE', 'error', 'labels' );
        G::header( 'location: ../login/login' );
        die();
        break;
}
// validating the fields
$message = array ();
$oldName = isset( $_POST['oldName'] ) ? $_POST['oldName'] : '';

switch ($_POST['action']) {
    case 'calendarName':
        require_once ('classes/model/CalendarDefinition.php');
        $oCalendar = new CalendarDefinition();
        $aCalendars = $oCalendar->getCalendarList( false, true );
        //    var_dump($_POST['name']);
        //    var_dump($aCalendars);
        //$count = 0;
        $aCalendarDefinitions = end( $aCalendars );
        //    var_dump($aCalendarDefinitions);
        foreach ($aCalendarDefinitions as $aDefinitions) {
            if (trim( $_POST['name'] ) == '') {
                $validated = false;
                $message = G::loadTranslation( 'ID_CALENDAR_INVALID_NAME' );
                break;
            }
            if ($aDefinitions['CALENDAR_NAME'] != $_POST['name']) {
                $validated = true;
            } else {
                if ($aDefinitions['CALENDAR_NAME'] != $oldName) {
                    $validated = false;
                    $message = G::loadTranslation( 'ID_CALENDAR_INVALID_NAME' );
                    break;
                }
            }
        }
        break;
    case 'calendarDates':
        $validated = false;
        $message = G::loadTranslation( 'ID_CALENDAR_INVALID_WORK_DATES' );
        break;
}
if (! $validated) {
    echo ($message);
}

