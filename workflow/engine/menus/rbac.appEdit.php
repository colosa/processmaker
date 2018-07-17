<?php
/**
 * rbac.appEdit.php
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
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * For more information, contact Colosa Inc, 2566 Le Jeune Rd.,
 * Coral Gables, FL, 33134, USA, or email info@colosa.com.
 *
 */

global $G_TMP_MENU;
$appid = $_SESSION['CURRENT_APPLICATION'];

$G_TMP_MENU->AddIdRawOption( "OP1", "rbac/appList.html" );
$G_TMP_MENU->AddIdRawOption( "OP2", "rbac/appDel.html" );

switch( SYS_LANG )
{
case 'es':
  $G_TMP_MENU->Labels = array(
    "Cancelar",
    "Eliminar Applicación"
  );
  break;
case 'po':
  $G_TMP_MENU->Labels = array(
    "Cancelar",
    "Eliminar Application"
  );
  break;
default:
  $G_TMP_MENU->Labels = array(
    "Cancel",
    "Remove Application"
  );
  break;
}

//si no hay nada relacionado a esta aplicación se puede BORRAR!!
$dbc = new DBConnection(DB_HOST, DB_RBAC_USER, DB_RBAC_PASS, DB_RBAC_NAME );
$obj = New RBAC_Application;
$obj->SetTo ($dbc);
$sw = $obj->canRemoveApplication ($appid);
if ($sw > 0)
  $G_TMP_MENU->disableOptionId ("OP2");

?>
