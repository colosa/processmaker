<?php
/**
 * translationsEdit.php
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

//to do: improve the way to pass two or more parameters in the paged-table ( link )


$aux = explode( '|', $_GET['id'] );
$category = str_replace( '"', '', $aux[0] );
$id = str_replace( '"', '', $aux[1] );

require_once ("classes/model/Translation.php");
//if exists the row in the database propel will update it, otherwise will insert.
$tr = TranslationPeer::retrieveByPK( $category, $id, 'en' );

if ((is_object( $tr ) && get_class( $tr ) == 'Translation')) {
    $fields['trn_category'] = $tr->getTrnCategory();
    $fields['trn_id'] = $tr->getTrnId();
    $fields['trn_value'] = $tr->getTrnValue();
} else
    $fields = array ();

$G_MAIN_MENU = 'tools';
$G_SUB_MENU = 'toolsTranslations';
$G_ID_MENU_SELECTED = 'TOOLS';
$G_ID_SUB_MENU_SELECTED = 'ADD_TRANSLATION';

$G_PUBLISH = new Publisher();
$dbc = new DBConnection();
$ses = new DBSession( $dbc );
$G_PUBLISH = new Publisher();
$G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'tools/translationAdd', '', $fields, 'translationsSave' );
if (isset( $_SESSION['TOOLS_VIEWTYPE'] ) && $_SESSION['TOOLS_VIEWTYPE'] == 'blank') {
    G::RenderPage( 'publishBlank', 'green-submenu' );
} else {
    G::RenderPage( 'publish' );
}

