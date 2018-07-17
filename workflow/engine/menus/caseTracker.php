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
 /*
   * Case Tracker Menú
   *
   * @author Everth S. Berrios Morales <everth@colosa.com>
   * 
   */
global $G_TMP_MENU;
global $RBAC;

if (file_exists ( PATH_CORE . 'menus/plugin.php')) {
  require_once (PATH_CORE . 'menus/plugin.php');
}

  $case = new Cases();
  $caseTracker = $case->caseTrackerPermissions( $_SESSION['PROCESS']);
  if ($caseTracker['CT_MAP_TYPE']) {
    $G_TMP_MENU->AddIdRawOption('MAP',      'tracker/tracker_ViewMap',    G::LoadTranslation('ID_MAP'));
  }
  if ($caseTracker['DYNADOC']) {
    $G_TMP_MENU->AddIdRawOption('DYNADOC',  'tracker/tracker_DynaDocs',   G::LoadTranslation('ID_DYNADOC'));
  }
  if ($caseTracker['CT_DERIVATION_HISTORY']) {
    $G_TMP_MENU->AddIdRawOption('HISTORY',  'tracker/tracker_History',    G::LoadTranslation('ID_HISTORY'));
  }
  if ($caseTracker['CT_MESSAGE_HISTORY']) {
    $G_TMP_MENU->AddIdRawOption('MESSAGES',  'tracker/tracker_Messages',  G::LoadTranslation('ID_HISTORY_MESSAGES'));
  }



