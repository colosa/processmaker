<?php
/**
 * languages.php
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
$RBAC->requirePermissions( 'PM_SETUP_ADVANCE' );

$oHeadPublisher->addExtJsScript( 'setup/languages', false ); //adding a javascript file .js
$oHeadPublisher->addContent( 'setup/languages' ); //adding a html file  .html.


$labels = G::getTranslations( Array ('ID_LAN_PREDETERMINED','ID_LANG_INSTALL_UPDATE','ID_LAN_LANGUAGE','ID_LAN_COUNTRY','ID_LAN_UPDATE_DATE','ID_LAN_REV_DATE','ID_LAN_FILE','ID_LAN_REV_DATE','ID_LAN_VERSION','ID_LAN_GRID_TITLE','ID_LAN_UPLOAD_TITLE','ID_LAN_FILE_WATER_LABEL','ID_EXPORT','ID_UPLOAD','ID_CANCEL','ID_DELETE_LANGUAGE','ID_DELETE_LANGUAGE_CONFIRM','ID_DELETE_LANGUAGE_WARNING','ID_ACTIONS','ID_LAN_LOCALE','ID_LAN_TRANSLATOR','ID_LAN_NUM_RECORDS','ID_UPLOADING_TRANSLATION_FILE'
) );

//$oHeadPublisher->assign('TRANSLATIONS', $labels);
G::RenderPage( 'publish', 'extJs' );

