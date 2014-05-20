<?php
/**
 * login_getStarted.php
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
$G_PUBLISH = new Publisher();

$fileGetStart = PATH_SKINS . SYS_SKIN . PATH_SEP . 'login_getStarted.html';
if (!file_exists($fileGetStart)) {
    $fileGetStart = PATH_SKIN_ENGINE . SYS_SKIN . PATH_SEP . 'login_getStarted.html';
    if (!file_exists($fileGetStart)) {
        $fileGetStart = PATH_CUSTOM_SKINS . SYS_SKIN . PATH_SEP . 'login_getStarted.html';
        if (!file_exists($fileGetStart)) {
            $fileGetStart = PATH_TPL . 'services/login_getStarted.html';
        }
    }
}

$oTemplatePower = new TemplatePower( $fileGetStart );
$oTemplatePower->prepare();
/*
$oTemplatePower->newBlock('users');
$oTemplatePower->assign('USR_UID', $aUser['USR_UID']);
$oTemplatePower->assign('USR_FULLNAME', $aData['USR_FIRSTNAME'] . ' ' . $aData['USR_LASTNAME'] . ' (' . $aData['USR_USERNAME'] . ')');
*/
$userName = 'Admin';
require_once 'classes/model/Users.php';
$oCriteria = new Criteria( 'workflow' );
$oCriteria->addSelectColumn( UsersPeer::USR_USERNAME);
$oCriteria->add( UsersPeer::USR_UID, '00000000000000000000000000000001', Criteria::IN );
$oDataset = UsersPeer::doSelectRS( $oCriteria );
$oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
$aData = array ();
if ($oDataset->next()) {
    $aData = $oDataset->getRow();
    $userName = $aData['USR_USERNAME'];
}

$oTemplatePower->assign("URL_MABORAK_JS", G::browserCacheFilesUrl("/js/maborak/core/maborak.js"));
$oTemplatePower->assign("name", $userName);

$G_PUBLISH->AddContent( 'template', '', '', '', $oTemplatePower );

G::RenderPage( 'publish', 'raw' );

