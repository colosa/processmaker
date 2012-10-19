<?php
/**
 * cases_Resume.php
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

/* Includes */
G::LoadClass( 'case' );

$oCase = new Cases();
$Fields = $oCase->loadCase( $_SESSION['APPLICATION'], $_SESSION['INDEX'] );

/* Render page */
require_once 'classes/model/Process.php';
require_once 'classes/model/Task.php';

$objProc = new Process();
$aProc = $objProc->load( $Fields['PRO_UID'] );
$Fields['PRO_TITLE'] = $aProc['PRO_TITLE'];

$objTask = new Task();
$aTask = $objTask->load( $Fields['TAS_UID'] );
$Fields['TAS_TITLE'] = $aTask['TAS_TITLE'];

$Fields['STATUS'] .= ' ( ' . G::LoadTranslation( 'ID_UNASSIGNED' ) . ' )';

//now getting information about the PREVIOUS task. If is the first task then no preious, use 1
$oAppDel = new AppDelegation();
$oAppDel->Load( $Fields['APP_UID'], ($Fields['DEL_PREVIOUS'] == 0 ? $Fields['DEL_PREVIOUS'] = 1 : $Fields['DEL_PREVIOUS']) );

$aAppDel = $oAppDel->toArray( BasePeer::TYPE_FIELDNAME );
try {
    $oCurUser = new Users();
    $oCurUser->load( $aAppDel['USR_UID'] );
    $Fields['PREVIOUS_USER'] = $oCurUser->getUsrFirstname() . ' ' . $oCurUser->getUsrLastname();
} catch (Exception $oError) {
    $Fields['PREVIOUS_USER'] = '';
}

$objTask = new Task();
$aTask = $objTask->load( $aAppDel['TAS_UID'] );
$Fields['PREVIOUS_TASK'] = $aTask['TAS_TITLE'];

//To enable information (dynaforms, steps) before claim a case
$_SESSION['bNoShowSteps'] = true;
$G_MAIN_MENU = 'processmaker';
$G_SUB_MENU = 'caseOptions';
$G_ID_MENU_SELECTED = 'CASES';
$G_ID_SUB_MENU_SELECTED = '_';
$oHeadPublisher = & headPublisher::getSingleton();
$oHeadPublisher->addScriptCode( "
if (typeof parent != 'undefined') {
  if (parent.showCaseNavigatorPanel) {
    parent.showCaseNavigatorPanel('{$Fields['APP_STATUS']}');
  }
}" );
$oHeadPublisher->addScriptCode( '
      var Cse = {};
      Cse.panels = {};
      var leimnud = new maborak();
      leimnud.make();
      leimnud.Package.Load("rpc,drag,drop,panel,app,validator,fx,dom,abbr",{Instance:leimnud,Type:"module"});
      leimnud.Package.Load("json",{Type:"file"});
      leimnud.Package.Load("cases",{Type:"file",Absolute:true,Path:"/jscore/cases/core/cases.js"});
      leimnud.Package.Load("cases_Step",{Type:"file",Absolute:true,Path:"/jscore/cases/core/cases_Step.js"});
      leimnud.Package.Load("processmap",{Type:"file",Absolute:true,Path:"/jscore/processmap/core/processmap.js"});
      leimnud.exec(leimnud.fix.memoryLeak);
      ' );
$oHeadPublisher = & headPublisher::getSingleton();
$oHeadPublisher->addScriptFile( '/jscore/cases/core/cases_Step.js' );

$G_PUBLISH = new Publisher();
$G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'cases/cases_CatchSelfService.xml', '', $Fields, 'cases_CatchExecute' );
G::RenderPage( 'publish', 'blank' );

