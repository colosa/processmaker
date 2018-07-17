<?php

/**
 * cases_New.php
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
//we're looking for the type of view
function putTypeView ()
{
    require_once 'classes/model/Configuration.php';
    $oConfiguration = new Configuration();
    $oCriteria = new Criteria( 'workflow' );
    $oCriteria->add( ConfigurationPeer::CFG_UID, 'StartNewCase' );
    $oCriteria->add( ConfigurationPeer::USR_UID, $_SESSION['USER_LOGGED'] );

    if (ConfigurationPeer::doCount( $oCriteria )) {
        $conf = ConfigurationPeer::doSelect( $oCriteria );
        return $conf[0]->getCfgValue();
    } else {
        return 'dropdown';
    }
}

$_GET['change'] = (isset( $_GET['change'] )) ? $_GET['change'] : putTypeView();

/* Permissions */
switch ($RBAC->userCanAccess( 'PM_CASES' )) {
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

/* GET , POST & $_SESSION Vars */

  /* Menues */
  $G_MAIN_MENU = 'processmaker';
$G_SUB_MENU = 'cases';
$G_ID_MENU_SELECTED = 'CASES';
$G_ID_SUB_MENU_SELECTED = 'CASES_DRAFT';

/* Prepare page before to show */
$aFields = array ();
$oCase = new Cases();
$bCanStart = $oCase->canStartCase( $_SESSION['USER_LOGGED'] );
if ($bCanStart) {
    $aFields['LANG'] = SYS_LANG;
    $aFields['USER'] = $_SESSION['USER_LOGGED'];
    $sXmlForm = 'cases/cases_New.xml';
    //$_DBArray['NewCase'] = $oCase->getStartCases( $_SESSION['USER_LOGGED'] );
    $_DBArray['NewCase'] = $oCase->getStartCasesPerType( $_SESSION['USER_LOGGED'], $_GET['change'] );

} else {
    $sXmlForm = 'cases/cases_CannotInitiateCase.xml';
}

if (isset( $_SESSION['G_MESSAGE'] ) && strlen( $_SESSION['G_MESSAGE'] ) > 0) {
    $aMessage = array ();
    $aMessage['MESSAGE'] = $_SESSION['G_MESSAGE'];
    //$_SESSION['G_MESSAGE_TYPE'];
    unset( $_SESSION['G_MESSAGE'] );
    unset( $_SESSION['G_MESSAGE_TYPE'] );
}

//get the config parameter to show in dropdown or list
require_once 'classes/model/Configuration.php';
$oConfiguration = new Configuration();
$oCriteria = new Criteria( 'workflow' );
$oCriteria->add( ConfigurationPeer::CFG_UID, 'StartNewCase' );
$oCriteria->add( ConfigurationPeer::USR_UID, $_SESSION['USER_LOGGED'] );

if (ConfigurationPeer::doCount( $oCriteria ) == 0) {
    $aData['CFG_UID'] = 'StartNewCase';
    $aData['OBJ_UID'] = '';
    $aData['CFG_VALUE'] = 'dropdown';
    $aData['PRO_UID'] = '';
    $aData['USR_UID'] = $_SESSION['USER_LOGGED'];
    $aData['APP_UID'] = '';

    $oConfig = new Configuration();
    $oConfig->create( $aData );
    $listType = 'dropdown';
} else {
    $oConfiguration = new Configuration();
    $oCriteria = new Criteria( 'workflow' );
    $oCriteria->add( ConfigurationPeer::CFG_UID, 'StartNewCase' );
    $oCriteria->add( ConfigurationPeer::USR_UID, $_SESSION['USER_LOGGED'] );
    $conf = ConfigurationPeer::doSelect( $oCriteria );

    $listType = $conf[0]->getCfgValue();
}
if (isset( $_GET['change'] )) {
    $listType = $_GET['change'];
    $aData['CFG_UID'] = 'StartNewCase';
    $aData['OBJ_UID'] = '';
    $aData['CFG_VALUE'] = $listType;
    $aData['PRO_UID'] = '';
    $aData['USR_UID'] = $_SESSION['USER_LOGGED'];
    $aData['APP_UID'] = '';

    $oConfig = new Configuration();
    $oConfig->update( $aData );
}

/* Render page */
$G_PUBLISH = new Publisher();
$aFields['CHANGE_LINK'] = G::LoadTranslation( 'ID_CHANGE_VIEW' );

if (isset( $aMessage )) {
    $G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'login/showMessage', '', $aMessage );
}
if ($listType == 'dropdown') {
    $G_PUBLISH->AddContent( 'xmlform', 'xmlform', $sXmlForm, '', $aFields, 'cases_Save' );
}

if ($listType == 'link') {
    if ($bCanStart) {
        $sXmlForm = 'cases/cases_NewRadioGroup.xml';
    }
    $G_PUBLISH->AddContent( 'xmlform', 'xmlform', $sXmlForm, '', $aFields, 'cases_Save' );
}

if ($listType == 'category') {
    if ($bCanStart) {
        $sXmlForm = 'cases/cases_NewCategory.xml';
    }
    $G_PUBLISH->AddContent( 'view', 'cases/cases_NewCategory' );
}

G::RenderPage( 'publish', 'blank' );

?>
<script>
    parent.outerLayout.hide('east');
    parent.PANEL_EAST_OPEN = false;
</script>

<?php

