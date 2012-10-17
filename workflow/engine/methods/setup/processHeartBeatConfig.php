<?php
/**
 * main.php Cases List main processor
 *
 * ProcessMaker Open Source Edition
 * Copyright (C) 2004 - 2010 Colosa Inc.23
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
$oHeadPublisher = & headPublisher::getSingleton();
G::LoadClass( 'serverConfiguration' );
$oServerConf = & serverConf::getSingleton();

//you can use SYS_TEMP or SYS_SYS ON HEAR_BEAT_CONF to save for each workspace
$sflag = $oServerConf->getHeartbeatProperty( 'HB_OPTION', 'HEART_BEAT_CONF' );
$heartBeatChecked = $sflag == 1 ? true : false;

$oHeadPublisher->addExtJsScript( 'setup/processHeartBeatConfig', true ); //adding a javascript file .js


$oHeadPublisher->assign( 'heartBeatChecked', $heartBeatChecked );
G::RenderPage( 'publish', 'extJs' );

