<?php
/**
 * workPeriod.php
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
$G_ENABLE_BLANK_SKIN = true;

$dbc = new DBConnection();
$ses = new DBSession( $dbc );
$obj = new workPeriod( $dbc );

$row = $obj->Load();

$row['SUNDAY'] = $row['noWorkingDays'][0];
$row['MONDAY'] = $row['noWorkingDays'][1];
$row['TUESDAY'] = $row['noWorkingDays'][2];
$row['WEDNESDAY'] = $row['noWorkingDays'][3];
$row['THURSDAY'] = $row['noWorkingDays'][4];
$row['FRIDAY'] = $row['noWorkingDays'][5];
$row['SATURDAY'] = $row['noWorkingDays'][6];

$G_PUBLISH = new Publisher();
$G_PUBLISH->AddContent( "image", "image", "workPeriodGraph" );
$G_PUBLISH->AddContent( "xmlform", "xmlform", "setup/workPeriod", "", $row, "workPeriodSave" );

G::RenderPage( 'publish' );

