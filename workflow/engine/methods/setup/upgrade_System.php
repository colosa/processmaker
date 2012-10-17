<?php
/**
 * upgrade_System.php
 *
 * ProcessMaker Open Source Edition
 * Copyright (C) 2004 - 2008 Colosa Inc.
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
 * New System Upgrade controller
 *
 * @author Erik A. O. <erik@colosa.com>
 * @date May 12th, 2010
 */
global $RBAC;
switch ($RBAC->userCanAccess( 'PM_SETUP_ADVANCE' )) {
    case - 2:
        G::SendTemporalMessage( 'ID_USER_HAVENT_RIGHTS_SYSTEM', 'error', 'labels' );
        G::header( 'location: ../login/login' );
        die();
        break;
    case - 1:
        G::SendTemporalMessage( 'ID_USER_HAVENT_RIGHTS_PAGE', 'error', 'labels' );
        G::header( 'location: ../login/login' );
        die();
        break;
}
set_time_limit( 0 );

$G_MAIN_MENU = 'processmaker';
$G_SUB_MENU = 'setup';
$G_ID_MENU_SELECTED = 'SETUP';
$G_ID_SUB_MENU_SELECTED = 'UPGRADE';

require_once "classes/class.system.php";
$oSystem = new System();

try {
    if (! $oSystem->verifyFileForUpgrade()) {
        throw (new Exception( "There was an error uploading the file, probably the file size if greater than upload_max_filesize parameter in php.ini, please check this parameter and try again." ));
    }
    $oSystem->cleanupUpgradeDirectory();
    $oSystem->getUpgradedFilesList();

    $ver = $oSystem->upgrade();
    $G_PUBLISH = new Publisher();
    $aMessage['THEMESSAGE1'] = G::LoadTranslation( 'ID_UPGRADE_READY' ) . " <b>" . $ver[0] . "</b> " . G::LoadTranslation( 'ID_TO' ) . " <b>" . $ver[1] . "</b>";
    $aMessage['THEMESSAGE2'] = file_get_contents( $oSystem->sUpgradeFileList );
    $aMessage['THEMESSAGE3'] = '';

    if (! is_Array( $oSystem->aErrors ) || count( $oSystem->aErrors ) == 0)
        $aMessage['THEMESSAGE4'] = G::LoadTranslation( 'ID_NONE' );
    else
        $aMessage['THEMESSAGE4'] = implode( "\n", $oSystem->aErrors );

    $oHeadPublisher = & headPublisher::getSingleton();
    if (file_exists( PATH_CORE . 'js' . PATH_SEP . 'setup' . PATH_SEP . 'upgrade_System.js' )) {
        $oHeadPublisher->addScriptFile( '/jscore/setup/upgrade_System.js' );
    } else {
        $oHeadPublisher->addScriptCode( "function upgradeSystem(wsCount) {
      document.getElementById('form[THETITLE3]').innerHTML = wsCount + ' workspaces to update.';
      document.getElementById('form[SUBTITLE4]').innerHTML = '&nbsp;&nbsp;<img src='/images/alert.gif' width='13' height='13' border='0'> Please wait...';
      updateWorkspace(wsCount);
    };
    function updateWorkspace(id) {
      if(id < 0) return false;
      var oRPC = new leimnud.module.rpc.xmlhttp({
        async : true,
        method: 'POST',
        url:  '../setup/upgrade_SystemAjax',
        args  : 'id=' + id
      });
      oRPC.callback = function(rpc) {
        document.getElementById('form[SUBTITLE4]').innerHTML = rpc.xmlhttp.responseText;
        updateWorkspace(id-1);
      }.extend(this);
      oRPC.make();
    };" );
    }
    $G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'login/showInfoUpdate', '', $aMessage );

    G::RenderPage( 'publishBlank', 'blank' );
    G::evalJScript( 'upgradeSystem(' . count( $oSystem->aWorkspaces ) . ')' );
    exit( 0 );
} catch (Exception $e) {
    $G_PUBLISH = new Publisher();
    $aMessage['MESSAGE'] = $e->getMessage();
    $G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'login/showMessage', '', $aMessage );
    G::RenderPage( 'publishBlank', 'blank' );
    exit( 0 );
}

