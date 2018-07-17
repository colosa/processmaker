<?php
/**
 * fields_Delete.php
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

if (! (isset( $_POST['A'] ) && $_POST['A'] !== '')) {
    return;
}

$file = G::decrypt( $_POST['A'], URL_KEY );

$dbc = new DBConnection( PATH_DYNAFORM . $file . '.xml', '', '', '', 'myxml' );
$ses = new DBSession( $dbc );

$fields = new DynaFormField( $dbc );

if (! isset( $_POST['XMLNODE_NAME'] )) {
    return;
}

$fields->Delete( $_POST['XMLNODE_NAME'] );

$i = 0;
$aFields = array ();
$aFields[] = array ('XMLNODE_NAME' => 'char','TYPE' => 'char','UP' => 'char','DOWN' => 'char');
$oSession = new DBSession( new DBConnection( PATH_DYNAFORM . $file . '.xml', '', '', '', 'myxml' ) );
$oDataset = $oSession->Execute( 'SELECT * FROM dynaForm WHERE NOT( XMLNODE_NAME = "" )' );
$iMaximun = $oDataset->count();
while ($aRow = $oDataset->Read()) {
    $aFields[] = array ('XMLNODE_NAME' => $aRow['XMLNODE_NAME'],'TYPE' => $aRow['TYPE'],'UP' => ($i > 0 ? G::LoadTranslation( 'ID_UP' ) : ''),'DOWN' => ($i < $iMaximun - 1 ? G::LoadTranslation( 'ID_DOWN' ) : '') );
    $i ++;
}
global $_DBArray;
$_DBArray['fields'] = $aFields;
$_SESSION['_DBArray'] = $_DBArray;

