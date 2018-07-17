<?php
/**
 * open.php Open Case main processor
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
 *
 * @author Erik Amaru Ortiz <erik@colosa.com>
 * @date Jan 3th, 2010
 */

$tBarGmail = false;
if(isset( $_GET['gmail']) && $_GET['gmail'] == 1){
    $_SESSION['gmail'] = 1;
    $tBarGmail = true;
}

//Check if we have the information for open the case
if (!isset($_GET['APP_UID']) && !isset($_GET['APP_NUMBER']) && !isset($_GET['DEL_INDEX'])) {
    throw new Exception(G::LoadTranslation('ID_APPLICATION_OR_INDEX_MISSING'));
}
//Get the APP_UID related to APP_NUMBER
if (!isset($_GET['APP_UID']) && isset($_GET['APP_NUMBER'])) {
    $oCase = new Cases();
    $appUid = $oCase->getApplicationUIDByNumber(htmlspecialchars($_GET['APP_NUMBER']));
    if (is_null( $appUid )) {
        throw new Exception(G::LoadTranslation('ID_CASE_DOES_NOT_EXISTS'));
    }
} else {
    $appUid = htmlspecialchars($_GET['APP_UID']);
}
//If we don't have the DEL_INDEX we get the current delIndex. Data reporting tool does not have this information
if (!isset($_GET['DEL_INDEX'])) {
    $oCase = new Cases();
    $delIndex = $oCase->getCurrentDelegation($appUid, $_SESSION['USER_LOGGED']);
    if (is_null( $delIndex )) {
        throw new Exception(G::LoadTranslation('ID_CASE_IS_CURRENTLY_WITH_ANOTHER_USER'));
    }
    $_GET['DEL_INDEX'] = $delIndex;
} else {
    $delIndex = htmlspecialchars($_GET['DEL_INDEX']);
}

$tasUid = (isset($_GET['TAS_UID'])) ? $tasUid = htmlspecialchars($_GET['TAS_UID']) : '';

$oCase = new Cases();
$conf = new Configurations();

$oHeadPublisher = & headPublisher::getSingleton();

$urlToRedirectAfterPause = 'casesListExtJs';

/*----------------------------------********---------------------------------*/


$oHeadPublisher->assign( 'urlToRedirectAfterPause', $urlToRedirectAfterPause );


$oHeadPublisher->addExtJsScript( 'app/main', true );
$oHeadPublisher->addExtJsScript( 'cases/open', true );
$oHeadPublisher->assign( 'FORMATS', $conf->getFormats() );
$uri = '';
foreach ($_GET as $k => $v) {
    $uri .= ($uri == '') ? "$k=$v" : "&$k=$v";
}

if( isset($_GET['action']) && ($_GET['action'] == 'jump') ) {
    $oNewCase = new \ProcessMaker\BusinessModel\Cases();
    //We need to get the last index OPEN or CLOSED (by Paused cases)
    //Set true because we need to check if the case is paused
    $delIndex = $oNewCase->getOneLastThread($appUid, true);
    $case = $oCase->loadCase( $appUid, $delIndex, $_GET['action']);
} else {
    $case = $oCase->loadCase( $appUid, $delIndex );
}

if(isset($_GET['actionFromList']) && ($_GET['actionFromList'] === 'to_revise') ){
    $oSupervisor = new \ProcessMaker\BusinessModel\ProcessSupervisor();
    $caseCanBeReview = $oSupervisor->reviewCaseStatusForSupervisor($appUid, $delIndex);
    //Check if the case has the correct status for update the information from supervisor/review
    if (!$caseCanBeReview) {
        //The supervisor can not edit the information
        $script = 'cases_Open?';
    } else {
        //The supervisor can edit the information, the case are in TO_DO
        $script = 'cases_OpenToRevise?APP_UID=' . $appUid . '&DEL_INDEX=' . $delIndex . '&TAS_UID=' . $tasUid;
        $oHeadPublisher->assign( 'treeToReviseTitle', G::loadtranslation( 'ID_STEP_LIST' ) );
        $casesPanelUrl = 'casesToReviseTreeContent?APP_UID=' . $appUid . '&DEL_INDEX=' . $delIndex;
        $oHeadPublisher->assign( 'casesPanelUrl', $casesPanelUrl ); //translations
        echo "<div id='toReviseTree'></div>";
    }
} else {
    $script = 'cases_Open?';
}

// getting bpmn projects
$c = new Criteria('workflow');
$c->addSelectColumn(BpmnProjectPeer::PRJ_UID);
$ds = ProcessPeer::doSelectRS($c);
$ds->setFetchmode(ResultSet::FETCHMODE_ASSOC);
$bpmnProjects = array();

while ($ds->next()) {
    $row = $ds->getRow();
    $bpmnProjects[] = $row['PRJ_UID'];
}
$oStep = new Step();
$oStep = $oStep->loadByProcessTaskPosition( $case['PRO_UID'], $case['TAS_UID'], 1 );
$oHeadPublisher->assign( 'uri', $script . $uri );
$oHeadPublisher->assign( '_APP_NUM', '#: ' . $case['APP_NUMBER'] );
$oHeadPublisher->assign( '_PROJECT_TYPE', in_array($case['PRO_UID'], $bpmnProjects) ? 'bpmn' : 'classic' );
$oHeadPublisher->assign( '_PRO_UID', $case['PRO_UID']);
$oHeadPublisher->assign( '_APP_UID', $appUid);
$oHeadPublisher->assign( '_ENV_CURRENT_DATE', $conf->getSystemDate( date( 'Y-m-d' ) ) );
$oHeadPublisher->assign( '_ENV_CURRENT_DATE_NO_FORMAT', date( 'Y-m-d-h-i-A' ) );
$oHeadPublisher->assign( 'idfirstform', is_null( $oStep ) ? '' : $oStep->getStepUidObj() );
$oHeadPublisher->assign( 'appStatus', $case['APP_STATUS'] );
$oHeadPublisher->assign( 'tbarGmail', $tBarGmail);

if(!isset($_SESSION['APPLICATION']) || !isset($_SESSION['TASK']) || !isset($_SESSION['INDEX'])) {
    $_SESSION['PROCESS'] = $case['PRO_UID'];
    $_SESSION['APPLICATION'] = $case['APP_UID'];
    $_SESSION['TASK'] = $case['TAS_UID'];
    $_SESSION['INDEX'] = $case['DEL_INDEX'];
}
$_SESSION['actionCaseOptions'] = (isset($_REQUEST['action'])) ? $_REQUEST['action'] : '';
G::RenderPage( 'publish', 'extJs' );

