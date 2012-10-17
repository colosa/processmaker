<?php
/**
 * setupAjax.php
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
    //$oSMTPJSON   = new Services_JSON();
    //$oSMTPData   = $oSMTPJSON->decode(stripslashes($_POST['data']));
    //$sOutput = '';
G::LoadClass( 'setup' );

$oSMTPSetup = new Setup( new DBConnection() );

$action = strtolower( $_GET['action'] );
$data = $_GET;

$arr = get_class_methods( get_class( $oSMTPSetup ) );
foreach ($arr as $method) {
    if ($method == $action)
        $oSMTPSetup->{$action}( $_GET );
}

