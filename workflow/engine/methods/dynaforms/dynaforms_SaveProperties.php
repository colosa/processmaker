<?php
/**
 * dynaforms_SaveProperties.php
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
    //G::genericForceLogin( 'WF_MYINFO' , 'login/noViewPage', $urlLogin = 'login/login' );
}



G::LoadInclude( 'ajax' );
G::LoadClass( 'dynaform' );
G::LoadClass( 'xmlDb' );

$dbc = new DBConnection();
$ses = new DBSession( $dbc );

//$dynaform = new dynaform( $dbc );


if ($_POST['form']['DYN_UID'] === '') {
    unset( $_POST['form']['DYN_UID'] );
}
$Fields = $_POST['form'];
if (! isset( $Fields['DYN_UID'] )) {
    return;
}
$file = G::decrypt( $Fields['A'], URL_KEY );
$Fields['DYN_FILENAME'] = (strcasecmp( substr( $file, - 5 ), '_tmp0' ) == 0) ? substr( $file, 0, strlen( $file ) - 5 ) : $file;
$_SESSION['CURRENT_DYNAFORM'] = $Fields;
//$dynaform->Save( $Fields );


$dbc2 = new DBConnection( PATH_DYNAFORM . $file . '.xml', '', '', '', 'myxml' );
$ses2 = new DBSession( $dbc2 );

if (! isset( $Fields['ENABLETEMPLATE'] )) {
    $Fields['ENABLETEMPLATE'] = "0";
}

$ses2->execute( G::replaceDataField( "UPDATE . SET WIDTH = @@WIDTH WHERE XMLNODE_NAME = 'dynaForm' ", $Fields ) );
$ses2->execute( G::replaceDataField( "UPDATE . SET ENABLETEMPLATE = @@ENABLETEMPLATE WHERE XMLNODE_NAME = 'dynaForm' ", $Fields ) );
$ses2->execute( G::replaceDataField( "UPDATE . SET MODE = @@MODE WHERE XMLNODE_NAME = 'dynaForm' ", $Fields ) );

