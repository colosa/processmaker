<?php
/**
 * languages_Delete.php
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
//print_r($_GET['LAN_ID']);


require_once 'classes/model/Language.php';
require_once 'classes/model/Content.php';

$kk = new Criteria();
$kk->add( ContentPeer::CON_LANG, $_GET['LAN_ID'] );
$oDataset = ContentPeer::doSelectRS( $kk );
$oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
$oDataset->next();

$aRow1 = $oDataset->getRow();

if (is_array( $aRow1 )) {

    $G_PUBLISH = new Publisher();
    $G_MAIN_MENU = 'processmaker';
    $G_ID_MENU_SELECTED = 'SETUP';
    $G_SUB_MENU = 'setup';
    $G_ID_SUB_MENU_SELECTED = 'LANGUAGES';

    //$aMessage['MESSAGE'] = G::LoadTranslation('CANT_DEL_LANGUAGE');//"you can't delete this language is in use";
    //$G_PUBLISH->AddContent('xmlform', 'xmlform', 'setup/showMessage', '', $aMessage );
    //G::RenderPage('publishBlank', 'blank');
    G::SendTemporalMessage( 'CANT_DEL_LANGUAGE', 'error', 'labels' );
    G::header( 'location: languages' );
} else {

    /*the reason why comment it was because when delete some language,we're losing some labels about this language*/
    $oCriteria = new Criteria( 'workflow' );
    $oCriteria->add( ContentPeer::CON_LANG, $_GET['LAN_ID'] );
    ContentPeer::doDelete( $oCriteria );

    /*
    $oCriteria1 = new Criteria('workflow');
    $oCriteria1->add(LanguagePeer::LAN_ENABLED, 0);
    $oCriteria2 = new Criteria('workflow');
    $oCriteria2->add(LanguagePeer::LAN_ID, $_GET['LAN_ID']);
    LanguagePeer::doUpdate($oCriteria2, $oCriteria1, Propel::getConnection('workflow'));
    */
    $aLanguage['LAN_ID'] = $_GET['LAN_ID'];
    $aLanguage['LAN_ENABLED'] = 0;

    $oLanguage = new Language();
    $oLanguage->update( $aLanguage );

    G::header( 'Location: languages' );
}

