<?php
/**
 * workPeriodSave.php
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
if (($RBAC_Response = $RBAC->userCanAccess( "PM_SETUP" )) != 1)
    return $RBAC_Response;
G::LoadClass( "workPeriod" );

$frm = $_POST['form'];
$noWorkingDays[0] = isset( $frm['SUNDAY'] ) && $frm['SUNDAY'] != '';
$noWorkingDays[1] = isset( $frm['MONDAY'] ) && $frm['MONDAY'] != '';
$noWorkingDays[2] = isset( $frm['TUESDAY'] ) && $frm['TUESDAY'] != '';
$noWorkingDays[3] = isset( $frm['WEDNESDAY'] ) && $frm['WEDNESDAY'] != '';
$noWorkingDays[4] = isset( $frm['THURSDAY'] ) && $frm['THURSDAY'] != '';
$noWorkingDays[5] = isset( $frm['FRIDAY'] ) && $frm['FRIDAY'] != '';
$noWorkingDays[6] = isset( $frm['SATURDAY'] ) && $frm['SATURDAY'] != '';

$dbc = new DBConnection();
$obj = new workPeriod( $dbc );
$obj->Save( $frm['initPeriod1'], $frm['endPeriod1'], $frm['initPeriod2'], $frm['endPeriod2'], $noWorkingDays );

print "ok";
die();

