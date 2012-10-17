<?php
/**
 * calendar_Ajax.php
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
$_POST['action'] = $_REQUEST['action'];

switch ($_POST['action']) {
    case 'calendarList':
        G::LoadClass( 'configuration' );
        G::LoadClass( 'calendar' );

        $co = new Configurations();
        $config = $co->getConfiguration( 'calendarList', 'pageSize', '', $_SESSION['USER_LOGGED'] );

        $limit_size = isset( $config['pageSize'] ) ? $config['pageSize'] : 20;
        $start = isset( $_REQUEST['start'] ) ? $_REQUEST['start'] : 0;
        $limit = isset( $_REQUEST['limit'] ) ? $_REQUEST['limit'] : $limit_size;
        $filter = isset( $_REQUEST['textFilter'] ) ? $_REQUEST['textFilter'] : '';

        $calendar = new calendar();
        $CRI = $calendar->getCalendarCriterias( $filter, $start, $limit );

        $aUsers = $calendar->getAllCounterByCalendar( 'USER' );
        $aTasks = $calendar->getAllCounterByCalendar( 'TASK' );
        $aProcess = $calendar->getAllCounterByCalendar( 'PROCESS' );

        $total_cals = CalendarDefinitionPeer::doCount( $CRI['COUNTER'] );

        $oDataSet = CalendarDefinitionPeer::doSelectRS( $CRI['LIST'] );
        $oDataSet->setFetchmode( ResultSet::FETCHMODE_ASSOC );

        $aCals = array ();
        while ($oDataSet->next()) {
            $aCals[] = $oDataSet->getRow();
            $index = sizeof( $aCals ) - 1;
            $aCals[$index]['TOTAL_USERS'] = isset( $aUsers[$aCals[$index]['CALENDAR_UID']] ) ? $aUsers[$aCals[$index]['CALENDAR_UID']] : 0;
            $aCals[$index]['TOTAL_TASKS'] = isset( $aTasks[$aCals[$index]['CALENDAR_UID']] ) ? $aTasks[$aCals[$index]['CALENDAR_UID']] : 0;
            $aCals[$index]['TOTAL_PROCESS'] = isset( $aProcess[$aCals[$index]['CALENDAR_UID']] ) ? $aProcess[$aCals[$index]['CALENDAR_UID']] : 0;
        }
        echo '{cals: ' . G::json_encode( $aCals ) . ', total_cals: ' . $total_cals . '}';
        break;
    case 'updatePageSize':
        G::LoadClass( 'configuration' );
        $c = new Configurations();
        $arr['pageSize'] = $_REQUEST['size'];
        $arr['dateSave'] = date( 'Y-m-d H:i:s' );
        $config = Array ();
        $config[] = $arr;
        $c->aConfig = $config;
        $c->saveConfig( 'calendarList', 'pageSize', '', $_SESSION['USER_LOGGED'] );
        echo '{success: true}';
        break;
    case 'canDeleteCalendar':
        $cal_uid = $_POST['CAL_UID'];
        G::LoadClass( 'calendar' );
        $cal = new calendar();
        $total = 0;
        $u = $cal->getAllCounterByCalendar( 'USER' );
        $t_u = isset( $u[$cal_uid] ) ? $u[$cal_uid] : 0;
        $t = $cal->getAllCounterByCalendar( 'TASK' );
        $t_t = isset( $t[$cal_uid] ) ? $t[$cal_uid] : 0;
        $p = $cal->getAllCounterByCalendar( 'PROCESS' );
        $t_p = isset( $p[$cal_uid] ) ? $p[$cal_uid] : 0;
        $total = $t_u + $t_t + $t_p;
        $response = ($total == 0) ? 'true' : 'false';
        echo '{success: ' . $response . '}';
        break;
    case 'deleteCalendar':
        $CalendarUid = $_POST['CAL_UID'];
        G::LoadClass( 'calendar' );
        $calendarObj = new calendar();
        $calendarObj->deleteCalendar( $CalendarUid );
        echo '{success: true}';
        break;
}

