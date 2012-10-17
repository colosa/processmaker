<?php
/**
 * main.php Cases List main processor
 *
 * ProcessMaker Open Source Edition
 * Copyright (C) 2004 - 2011 Colosa Inc.23
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
G::loadClass( 'configuration' );
$oConf = new Configurations();

$oHeadPublisher = & headPublisher::getSingleton();
$oServerConf = & serverConf::getSingleton();

$oHeadPublisher->addExtJsScript( 'setup/loginSettings', true ); //adding a javascript file .js
$oHeadPublisher->addContent( 'setup/loginSettings' ); //adding a html file  .html.


$oConf->loadConfig( $obj, 'ENVIRONMENT_SETTINGS', '' );

$forgotPasswd = isset( $oConf->aConfig['login_enableForgotPassword'] ) ? $oConf->aConfig['login_enableForgotPassword'] : false;
$virtualKeyboad = isset( $oConf->aConfig['login_enableVirtualKeyboard'] ) ? $oConf->aConfig['login_enableVirtualKeyboard'] : false;
$defaultLanguaje = isset( $oConf->aConfig['login_defaultLanguage'] ) ? $oConf->aConfig['login_defaultLanguage'] : 'en';

$oHeadPublisher->assign( 'currentLang', $defaultLanguaje );
$oHeadPublisher->assign( 'forgotPasswd', $forgotPasswd );
$oHeadPublisher->assign( 'virtualKeyboad', $virtualKeyboad );

G::RenderPage( 'publish', 'extJs' );

