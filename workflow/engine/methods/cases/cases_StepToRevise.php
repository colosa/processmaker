<?php
/**
 * cases_Step.php
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
//  die("first");
/* Permissions */
switch ($RBAC->userCanAccess( 'PM_SUPERVISOR' )) {
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

if ((int) $_SESSION['INDEX'] < 1) {
    G::SendTemporalMessage( 'ID_USER_HAVENT_RIGHTS_PAGE', 'error', 'labels' );
    G::header( 'location: ' . $_SERVER['HTTP_REFERER'] );
    die();
}
/* Includes */
G::LoadClass( 'case' );
G::LoadClass( 'derivation' );

/* GET , POST & $_SESSION Vars */
//$_SESSION['STEP_POSITION'] = (int)$_GET['POSITION'];


/* Menues */
$G_MAIN_MENU = 'processmaker';
$G_SUB_MENU = 'cases';
$G_ID_MENU_SELECTED = 'CASES';
$G_ID_SUB_MENU_SELECTED = 'CASES_TO_REVISE';

/* Prepare page before to show */
$oTemplatePower = new TemplatePower( PATH_TPL . 'cases/cases_Step.html' );
$oTemplatePower->prepare();
$G_PUBLISH = new Publisher();
$oCase = new Cases();
$Fields = $oCase->loadCase( $_SESSION['APPLICATION'] );

$oHeadPublisher = & headPublisher::getSingleton();
$oHeadPublisher->addScriptCode( "
if (typeof parent != 'undefined') {
  if (parent.showCaseNavigatorPanel) {
    parent.showCaseNavigatorPanel('{$Fields['APP_STATUS']}');
  }
}" );
// DEPRECATED this script call is marked for removal since almost all the interface is extJS based
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
    leimnud.event.add(window,"load",function(){
        ' . (isset( $_SESSION['showCasesWindow'] ) ? 'try{' . $_SESSION['showCasesWindow'] . '}catch(e){}' : '') . '
    });
' );
// DEPRECATED this script call is marked for removal
$G_PUBLISH->AddContent( 'template', '', '', '', $oTemplatePower );

if (! isset( $_GET['type'] )) {
    $_GET['type'] = 'DYNAFORM';
}
if (! isset( $_GET['position'] )) {
    $_GET['position'] = 1;
}

$_SESSION['STEP_POSITION'] = (int) $_GET['position'];

//Obtain previous and next step - Start
if (isset( $_GET['type'] )) {
    $sType = $_GET['type'];
} else {
    $sType = '';
}

$Fields['APP_DATA']['__DYNAFORM_OPTIONS']['PREVIOUS_STEP_LABEL'] = '';
$Fields['APP_DATA']['__DYNAFORM_OPTIONS']['NEXT_STEP_LABEL'] = '';

/**
 * Added By erik
 * date: 16-05-08
 * Description: this was added for the additional database connections
 */
G::LoadClass( 'dbConnections' );
$oDbConnections = new dbConnections( $_SESSION['PROCESS'] );
$oDbConnections->loadAdditionalConnections();

$G_PUBLISH = new Publisher();
if ($_GET['DYN_UID'] != '') {
    $_SESSION['CURRENT_DYN_UID'] = $_GET['DYN_UID'];
    $G_PUBLISH->AddContent( 'dynaform', 'xmlform', $_SESSION['PROCESS'] . '/' . $_GET['DYN_UID'], '', $Fields['APP_DATA'], 'cases_SaveDataSupervisor?UID=' . $_GET['DYN_UID'] );
}

G::RenderPage( 'publish', 'blank' );

if (! isset( $_GET['ex'] )) {
    $_GET['ex'] = $_GET['position'];
}
?>

<script>
/*------------------------------ To Revise Routines ---------------------------*/
// DEPRECATED this JS section is marked for removal
function setSelect()
{
  var ex=<?php echo $_GET['ex']?>;
  try {
    for(i=1; i<50; i++) {
      if (i == ex) {
        document.getElementById('focus'+i).innerHTML = '<img src="/images/bulletButton.gif" />';
      } else {
        document.getElementById('focus'+i).innerHTML = '';
      }
    }
  } catch (e){
    return 0;
  }
}
</script>

<?php

