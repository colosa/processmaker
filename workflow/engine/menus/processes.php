<?php
/**
 * processes.php
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
 * RS.
 *
 * Adicioné un nuevo parámetro en la clase AddIdRawOption en class.menu.php que me ayude
 * a colocar una clase al elemento que se encuentra dentro de la lista (generada por el
 * submenu).
 *
 * En este caso seguiremos con el uso del sprite. ss_sprite ss_nombre_archivo
 */
global $G_TMP_MENU;
$G_TMP_MENU_ALIGN = "left";

$G_TMP_MENU->AddIdRawOption('DYNAFORMS',    '', G::LoadTranslation('ID_DYNAFORMS'),"",'Pm.data.render.buildingBlocks.injector(\'dynaforms\'); return false;','','ss_sprite ss_application_form');
$G_TMP_MENU->AddIdRawOption('INPUTDOCS',    '', G::LoadTranslation('ID_REQUEST_DOCUMENTS'),"",'Pm.data.render.buildingBlocks.injector(\'inputs\'); return false;','','ss_sprite ss_page_white_get');
$G_TMP_MENU->AddIdRawOption('OUTPUTDOCS',   '', G::LoadTranslation('ID_OUTPUT_DOCUMENTS'),"",'Pm.data.render.buildingBlocks.injector(\'outputs\'); return false;','','ss_sprite ss_page_white_put');
$G_TMP_MENU->AddIdRawOption('TRIGGERS',     '', G::LoadTranslation('ID_TRIGGERS'),"",'Pm.data.render.buildingBlocks.injector(\'triggers\'); return false;','','ss_sprite ss_cog');

//$G_TMP_MENU->AddIdRawOption('MESSAGES',     '', G::LoadTranslation('ID_MESSAGES'),"/images/mail.gif",'Pm.data.render.buildingBlocks.injector(\'messages\'); return false;');

if (ReportTables::tableExist()) {
  //DEPRECATED $G_TMP_MENU->AddIdRawOption('REPORT_TABLES', '', G::LoadTranslation('ID_REPORT_TABLESOLD'),"",'Pm.data.render.buildingBlocks.injector(\'reportTables2\'); return false;','','ss_sprite ss_table');
  $G_TMP_MENU->AddIdRawOption('REPORT_TABLES', '', G::LoadTranslation('ID_REPORT_TABLES'),"",'Pm.data.render.buildingBlocks.injector(\'reportTables\'); return false;','','ss_sprite ss_table');
}
$G_TMP_MENU->AddIdRawOption('DB_CONNECTIONS', '', G::LoadTranslation('ID_DB_CONNECTIONS'),"",'showDbConnectionsList(Pm.options.uid); return false;','','ss_sprite ss_database_connect');
$G_TMP_MENU->AddIdRawOption('CASE_SCHEDULER', '', G::LoadTranslation('ID_CASE_SCHEDULER'), "", 'showCaseSchedulerList(Pm.options.uid); return false;','','ss_sprite ss_calendar_view_day');

//$G_TMP_MENU->AddIdRawOption('EDIT_BPMN', '', G::LoadTranslation('ID_SWITCH_EDITOR'), "", 'showNewProcessMap(Pm.options.uid); return false;','','ss_sprite ss_arrow_switch');
