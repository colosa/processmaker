<?php
/**
 * messages_Edit.php
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
if (($RBAC_Response = $RBAC->userCanAccess( "PM_FACTORY" )) != 1) {
    return $RBAC_Response;
}
//G::genericForceLogin( 'WF_MYINFO' , 'login/noViewPage', $urlLogin = 'login/login' );

$dbc = new DBConnection();
$ses = new DBSession( $dbc );

$messages = new Message( $dbc );
$messages->Fields['MESS_UID'] = (isset( $_GET['MESS_UID'] )) ? urldecode( $_GET['MESS_UID'] ) : '0';
$messages->Load( $messages->Fields['MESS_UID'] );
$messages->Fields['PRO_UID'] = isset( $messages->Fields['PRO_UID'] ) ? $messages->Fields['PRO_UID'] : $_GET['PRO_UID'];

$G_PUBLISH = new Publisher();
$G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'messages/messages_Edit', '', $messages->Fields, SYS_URI . 'messages/messages_Save' );

G::RenderPage( "publish", "raw" );

