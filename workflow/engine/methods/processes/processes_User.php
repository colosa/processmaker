<?php
/**
 * processes_User.php
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

/**
 * ription This is a callback for the View of all groups from a determinated user
 *
 * @author Everth S. Berrios Morales <everth@colosa.com>
 * @Date 16/05/2008
 * @LastModification none
 */
//G::LoadThirdParty( 'pear/json', 'class.json' );
try {
    //$oJSON = new Services_JSON();
    $stdObj = Bootstrap::json_decode( stripslashes( $_POST['data'] ) );
    if (isset( $stdObj->pro_uid ))
        $sProUid = $stdObj->pro_uid;
    else
        throw (new Exception( 'the process uid is not defined!.' ));

    G::LoadClass( 'processMap' );
    $oProcessMap = new ProcessMap();
    $c = $oProcessMap->listProcessesUser( $sProUid );

    $oHeadPublisher = & headPublisher::getSingleton();
    $oHeadPublisher->addScriptFile( '/jscore/processmap/core/processUser.js' );

    $G_PUBLISH = new Publisher();
    $G_PUBLISH->AddContent( 'propeltable', 'paged-table', 'processes/processes_User', $c, array ('PRO_UID' => $sProUid
    ) );
    G::RenderPage( 'publish', 'raw' );

} catch (Exception $e) {
    $G_PUBLISH = new Publisher();
    $aMessage['MESSAGE'] = $e->getMessage();
    $G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'login/showMessage', '', $aMessage );
    G::RenderPage( 'publish', 'raw' );
}
?>