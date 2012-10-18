<?php
/**
 * fields_List.php
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


G::LoadClass( 'dynaFormField' );

$G_MAIN_MENU = 'processmaker';
$G_SUB_MENU = 'processes';
$G_ID_MENU_SELECTED = 'PROCESSES';
$G_ID_SUB_MENU_SELECTED = 'FIELDS';

$PRO_UID = '746B734DC23311';
$file = $PRO_UID . '/' . 'myInfo';

define( 'DB_XMLDB_HOST', PATH_DYNAFORM . $file . '.xml' );
define( 'DB_XMLDB_USER', '' );
define( 'DB_XMLDB_PASS', '' );
define( 'DB_XMLDB_NAME', '' );
define( 'DB_XMLDB_TYPE', 'myxml' );

$G_PUBLISH = new Publisher();

$Parameters = array ('SYS_LANG' => SYS_LANG,'URL' => G::encrypt( $file, URL_KEY ));

$G_PUBLISH->AddContent( 'pagedtable', 'paged-table', 'dynaforms/fields_List', '', $Parameters, '', 'dynaforms_PagedTableAjax' );

G::RenderPage( "publish" );

