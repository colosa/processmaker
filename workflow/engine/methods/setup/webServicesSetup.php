<?php
/**
 * webServicesSetup.php
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
if (($RBAC_Response = $RBAC->userCanAccess( "PM_FACTORY" )) != 1)
    return $RBAC_Response;

G::LoadClass( 'groups' );

$dbc = new DBConnection();
$ses = new DBSession( $dbc );

if (! isset( $_SESSION['END_POINT'] )) {
    $aFields['WS_HOST'] = $_SERVER['HTTP_HOST'];
    $aFields['WS_WORKSPACE'] = SYS_SYS;
} else {
    if (strpos( $_SESSION['END_POINT'], 'https' ) !== false) {
        preg_match( '@^(?:https://)?([^/]+)@i', $_SESSION['END_POINT'], $coincidencias );
    } else {
        preg_match( '@^(?:http://)?([^/]+)@i', $_SESSION['END_POINT'], $coincidencias );
    }
    $aAux = explode( ':', $coincidencias[1] );
    $aFields['WS_HOST'] = $aAux[0];
    $aFields['WS_PORT'] = (isset( $aAux[1] ) ? $aAux[1] : '');
    $aAux = explode( $aAux[0] . (isset( $aAux[1] ) ? ':' . $aAux[1] : ''), $_SESSION['END_POINT'] );
    $aAux = explode( '/', $aAux[1] );
    $aFields['WS_WORKSPACE'] = substr( $aAux[1], 3 );
}

$rows[] = array ('uid' => 'char','name' => 'char','age' => 'integer','balance' => 'float'
);
$rows[] = array ('uid' => 'http','name' => 'http'
);
$rows[] = array ('uid' => 'https','name' => 'https'
);

$_DBArray['protocol'] = $rows;
$_SESSION['_DBArray'] = $_DBArray;

$G_PUBLISH = new Publisher();
$G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'setup/webServicesSetup', '', $aFields, 'webServicesSetupSave' );

G::RenderPage( "publish", "raw" );

