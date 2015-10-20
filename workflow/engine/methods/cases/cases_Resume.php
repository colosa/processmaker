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



/* GET , POST & $_SESSION Vars */



/* Menues */

$_SESSION['bNoShowSteps'] = true;

$G_MAIN_MENU = 'processmaker';

$G_SUB_MENU = 'caseOptions';

$G_ID_MENU_SELECTED = 'CASES';

$G_ID_SUB_MENU_SELECTED = '_';



/* Prepare page before to show */

$oCase = new Cases();

//$Fields = $oCase->loadCase( $_SESSION['APPLICATION'], $_SESSION['INDEX'] );

if (isset($_SESSION['ACTION']) && ($_SESSION['ACTION'] == 'jump')) {

    $Fields = $oCase->loadCase( $_SESSION['APPLICATION'], $_SESSION['INDEX'], $_SESSION['ACTION']);

} else {

    $Fields = $oCase->loadCase( $_SESSION['APPLICATION'], $_SESSION['INDEX']);

}



$participated = $oCase->userParticipatedInCase( $_GET['APP_UID'], $_SESSION['USER_LOGGED'] );



if ($RBAC->userCanAccess( 'PM_ALLCASES' ) < 0 && $participated == 0) {

    /*if (strtoupper($Fields['APP_STATUS']) != 'COMPLETED') {

      $oCase->thisIsTheCurrentUser($_SESSION['APPLICATION'], $_SESSION['INDEX'], $_SESSION['USER_LOGGED'], 'SHOW_MESSAGE');

    }*/

    $aMessage['MESSAGE'] = G::LoadTranslation( 'ID_NO_PERMISSION_NO_PARTICIPATED' );

    $G_PUBLISH = new Publisher();

    $G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'login/showMessage', '', $aMessage );

    G::RenderPage( 'publishBlank', 'blank' );

    die();

}

if (isset( $aRow['APP_TYPE'] )) {

    switch ($aRow['APP_TYPE']) {

        case 'PAUSE':

            $Fields['STATUS'] = ucfirst( strtolower( G::LoadTranslation( 'ID_PAUSED' ) ) );

            break;

        case 'CANCEL':

            $Fields['STATUS'] = ucfirst( strtolower( G::LoadTranslation( 'ID_CANCELLED' ) ) );

            break;

    }



    //$Fields['STATUS'] = $aRow['APP_TYPE'];

}



$actions = 'false';

if ($_GET['action'] == 'paused') {

    $actions = 'true';

}



    /* Render page */

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



require_once 'classes/model/Process.php';



$objProc = new Process();

$aProc = $objProc->load( $Fields['PRO_UID'] );

$Fields['PRO_TITLE'] = $aProc['PRO_TITLE'];







$objTask = new Task();

$aTask = $objTask->load( $Fields['TAS_UID'] );

$Fields['TAS_TITLE'] = $aTask['TAS_TITLE'];



$objUser = new Users();



$oHeadPublisher = & headPublisher::getSingleton();

$oHeadPublisher->addScriptFile( '/jscore/cases/core/cases_Step.js' );

$G_PUBLISH = new Publisher();

$G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'cases/cases_Resume.xml', '', $Fields, '' );

if($Fields['APP_STATUS'] != 'COMPLETED'){

  $G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'cases/cases_Resume_Current_Task_Title.xml', '', $Fields, '' );

  $objDel = new AppDelegation();

  $parallel = $objDel->LoadParallel ($Fields['APP_UID'],$_GET['DEL_INDEX']);

  $FieldsPar = $Fields;

  if(empty($parallel)){

    $G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'cases/cases_Resume_Current_Task.xml', '', $Fields, '' );

  }else{

    foreach($parallel as $row){

      $FieldsPar['TAS_UID'] = $row['TAS_UID'];

      $aTask = $objTask->load( $row['TAS_UID'] );

      $FieldsPar['TAS_TITLE'] = $aTask['TAS_TITLE'];

      $FieldsPar['USR_UID'] = $row['USR_UID'];

      if(isset($row['USR_UID']) && !empty($row['USR_UID'])) {

        $aUser = $objUser->loadDetails ($row['USR_UID']);

        $FieldsPar['CURRENT_USER'] = $aUser['USR_FULLNAME'];   

      }

      $FieldsPar['DEL_DELEGATE_DATE'] = $row['DEL_DELEGATE_DATE'];

      $FieldsPar['DEL_INIT_DATE']     = $row['DEL_INIT_DATE'];

      $FieldsPar['DEL_TASK_DUE_DATE'] = $row['DEL_TASK_DUE_DATE'];

      $FieldsPar['DEL_FINISH_DATE']   = $row['DEL_FINISH_DATE'];

      $G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'cases/cases_Resume_Current_Task.xml', '', $FieldsPar, '' );

    }

  }

  

}

G::RenderPage( 'publish', 'blank' );


