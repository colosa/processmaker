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
$oTemplatePower = new TemplatePower( PATH_TPL . 'services/login_getStarted.html' );
$oTemplatePower->prepare();
/*
$oTemplatePower->newBlock('users');
$oTemplatePower->assign('USR_UID', $aUser['USR_UID']);
$oTemplatePower->assign('USR_FULLNAME', $aData['USR_FIRSTNAME'] . ' ' . $aData['USR_LASTNAME'] . ' (' . $aData['USR_USERNAME'] . ')');
*/
$G_PUBLISH->AddContent( 'template', '', '', '', $oTemplatePower );

G::RenderPage( 'publish', 'raw' );

