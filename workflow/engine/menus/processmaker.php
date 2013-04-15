<?php
/**
 * processmaker.php
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


/*************************************
 *  ---= Processmaker main menu=---
 *************************************/

global $G_TMP_MENU;
global $RBAC;

// HOME MODULE
if ($RBAC->userCanAccess('PM_CASES') == 1) {
  $G_TMP_MENU->AddIdRawOption('CASES', 'cases/main', G::LoadTranslation('ID_HOME'), '', '', '', 'x-pm-home');
}

// DESIGNER MODULE
if ($RBAC->userCanAccess('PM_FACTORY') == 1 ) {
  $G_TMP_MENU->AddIdRawOption('PROCESSES', 'processes/main', G::LoadTranslation('ID_DESIGNER'), '', '', '', 'x-pm-designer');
}

// DASHBOARD MODULE
if ($RBAC->userCanAccess('PM_DASHBOARD') == 1) {
  $G_TMP_MENU->AddIdRawOption('DASHBOARD', 'dashboard/main', G::LoadTranslation('ID_DASHBOARD'), '', '', '', 'x-pm-dashboard');
}

// ADMIN MODULE
if ($RBAC->userCanAccess('PM_SETUP') == 1 || $RBAC->userCanAccess('PM_USERS') == 1) {
  $G_TMP_MENU->AddIdRawOption('SETUP', 'setup/main', G::LoadTranslation('ID_SETUP'), '', '', '', 'x-pm-setup');
}

// PLUGINS MENUS
if( file_exists(PATH_CORE . 'menus/plugin.php') ) {
  require_once(PATH_CORE . 'menus/plugin.php');
}