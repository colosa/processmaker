<?php
/**
 * main.php Cases List main processor
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

$RBAC->requirePermissions( 'PM_SETUP', 'PM_USERS' );

$G_MAIN_MENU = 'processmaker';
$G_ID_MENU_SELECTED = 'SETUP';
$G_PUBLISH = new Publisher();

if (isset( $_GET['i18'] ))
    $_SESSION['DEV_FLAG'] = $_SESSION['TOOLS_VIEWTYPE'] = isset( $_GET['i18'] );
else {
    unset( $_SESSION['DEV_FLAG'] );
    unset( $_SESSION['TOOLS_VIEWTYPE'] );
}

if (isset( $_GET['s'] ))
    $_SESSION['ADMIN_SELECTED'] = $_GET['s'];
else {
    unset( $_SESSION['ADMIN_SELECTED'] );
}

$G_PUBLISH->AddContent( 'view', 'setup/main_Load' );
G::RenderPage( 'publish' );

