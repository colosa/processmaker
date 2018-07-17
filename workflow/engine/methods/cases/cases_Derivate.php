<?php
/**
 * cases_Derivate.php
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
if (!isset($_SESSION['USER_LOGGED'])) {
	G::SendTemporalMessage( 'ID_LOGIN_AGAIN', 'warning', 'labels' );
	die( '<script type="text/javascript">
			var olink = document.location.href;
			olink = ( olink.search("gmail") == -1 ) ? parent.document.location.href : olink;
			if(olink.search("gmail") == -1){
				parent.location = "../cases/casesStartPage?action=startCase";
			} else {
				var data = olink.split("?");
				var odata = data[1].split("&");
				var appUid = odata[0].split("=");

				var dataToSend = {
					"action": "credentials",
					"operation": "refreshPmSession",
					"type": "processCall",
					"funParams": [
					appUid[1],
					""
					],
					"expectReturn": false
				};
				var x = parent.postMessage(JSON.stringify(dataToSend), "*");
				if (x == undefined){
					x = parent.parent.postMessage(JSON.stringify(dataToSend), "*");
				}
			}
			</script>');
}
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
//If no variables are submitted and the $_POST variable is empty
if (!isset($_POST['form'])) {
    $_POST['form'] = array();
}

/* GET , POST & $_SESSION Vars */
/* Process the info */
$sStatus = 'TO_DO';
foreach ($_POST['form']['TASKS'] as $aValues) {
}

try {
    //load data
    $oCase = new Cases();
    //warning: we are not using the result value of function thisIsTheCurrentUser, so I'm commenting to optimize speed.
    //$oCase->thisIsTheCurrentUser( $_SESSION['APPLICATION'], $_SESSION['INDEX'], $_SESSION['USER_LOGGED'], 'REDIRECT', 'casesListExtJs');
    $appFields = $oCase->loadCase( $_SESSION['APPLICATION'] );
    $appFields['APP_DATA'] = array_merge( $appFields['APP_DATA'], G::getSystemConstants() );
    //cleaning debug variables
    $_SESSION['TRIGGER_DEBUG']['DATA'] = Array ();
    $_SESSION['TRIGGER_DEBUG']['TRIGGERS_NAMES'] = Array ();
    $_SESSION['TRIGGER_DEBUG']['TRIGGERS_VALUES'] = Array ();
    $_SESSION['TRIGGER_DEBUG']['TRIGGERS_EXECUTION_TIME'] = [];

    $triggers = $oCase->loadTriggers( $_SESSION['TASK'], 'ASSIGN_TASK', - 2, 'BEFORE' );

    //if there are some triggers to execute
    if (sizeof( $triggers ) > 0) {
        //Execute triggers before derivation
        $appFields['APP_DATA'] = $oCase->ExecuteTriggers( $_SESSION['TASK'], 'ASSIGN_TASK', - 2, 'BEFORE', $appFields['APP_DATA'] );

        //save trigger variables for debugger
        $_SESSION['TRIGGER_DEBUG']['info'][0]['NUM_TRIGGERS'] = sizeof( $triggers );
        $_SESSION['TRIGGER_DEBUG']['info'][0]['TIME'] = G::toUpper(G::loadTranslation('ID_BEFORE'));
        $_SESSION['TRIGGER_DEBUG']['info'][0]['TRIGGERS_NAMES'] = array_column($triggers, 'TRI_TITLE');
        $_SESSION['TRIGGER_DEBUG']['info'][0]['TRIGGERS_VALUES'] = $triggers;
        $_SESSION['TRIGGER_DEBUG']['info'][0]['TRIGGERS_EXECUTION_TIME'] = $oCase->arrayTriggerExecutionTime;
    }

    unset($appFields['APP_STATUS']);
    unset($appFields['APP_PROC_STATUS']);
    unset($appFields['APP_PROC_CODE']);
    unset($appFields['APP_PIN']);

    $appFields["DEL_INDEX"] = $_SESSION["INDEX"];
    $appFields["TAS_UID"]   = $_SESSION["TASK"];
    $appFields["USER_UID"]  = $_SESSION["USER_LOGGED"];
    $appFields["CURRENT_DYNAFORM"] = "-2";
    $appFields["OBJECT_TYPE"]      = "ASSIGN_TASK";

    $oCase->updateCase($_SESSION["APPLICATION"], $appFields); //Save data

    //Prepare information for the derivation
    $oDerivation = new Derivation();
    $aCurrentDerivation = array (
        'APP_UID' => $_SESSION['APPLICATION'],
        'DEL_INDEX' => $_SESSION['INDEX'],
        'APP_STATUS' => $sStatus,
        'TAS_UID' => $_SESSION['TASK'],
        'ROU_TYPE' => $_POST['form']['ROU_TYPE']
    );
    $aDataForPrepareInfo = array (
        'USER_UID' => $_SESSION['USER_LOGGED'],
        'APP_UID' => $_SESSION['APPLICATION'],
        'DEL_INDEX' => $_SESSION['INDEX']
    );

    //We define some parameters in the before the derivation
    //Then this function will be route the case
    $arrayDerivationResult = $oDerivation->beforeDerivate(
        $aDataForPrepareInfo,
        $_POST['form']['TASKS'],
        $_POST['form']['ROU_TYPE'],
        $aCurrentDerivation
    );

    if (!empty($arrayDerivationResult)) {
        foreach ($_POST['form']['TASKS'] as $key => $value) {
            if (isset($value['TAS_UID'])) {
                foreach ($arrayDerivationResult as $value2) {
                    if ($value2['TAS_UID'] == $value['TAS_UID']) {
                        $_POST['form']['TASKS'][$key]['DEL_INDEX'] = $value2['DEL_INDEX'];
                        break;
                    }
                }
            }
        }
    }

    $appFields = $oCase->loadCase( $_SESSION['APPLICATION'] ); //refresh appFields, because in derivations should change some values
    $triggers = $oCase->loadTriggers( $_SESSION['TASK'], 'ASSIGN_TASK', - 2, 'AFTER' ); //load the triggers after derivation
    if (sizeof( $triggers ) > 0) {
        $appFields['APP_DATA'] = $oCase->ExecuteTriggers( $_SESSION['TASK'], 'ASSIGN_TASK', - 2, 'AFTER', $appFields['APP_DATA'] ); //Execute triggers after derivation


        $_SESSION['TRIGGER_DEBUG']['info'][1]['NUM_TRIGGERS'] = sizeof( $triggers );
        $_SESSION['TRIGGER_DEBUG']['info'][1]['TIME'] = G::toUpper(G::loadTranslation('ID_AFTER'));
        $_SESSION['TRIGGER_DEBUG']['info'][1]['TRIGGERS_NAMES'] = array_column($triggers, 'TRI_TITLE');
        $_SESSION['TRIGGER_DEBUG']['info'][1]['TRIGGERS_VALUES'] = $triggers;
        $_SESSION['TRIGGER_DEBUG']['info'][1]['TRIGGERS_EXECUTION_TIME'] = $oCase->arrayTriggerExecutionTime;
    }
    unset($appFields['APP_STATUS']);
    unset($appFields['APP_PROC_STATUS']);
    unset($appFields['APP_PROC_CODE']);
    unset($appFields['APP_PIN']);

    $appFields["DEL_INDEX"] = $_SESSION["INDEX"];
    $appFields["TAS_UID"]   = $_SESSION["TASK"];
    $appFields["USER_UID"]  = $_SESSION["USER_LOGGED"];
    $appFields["CURRENT_DYNAFORM"] = "-2";
    $appFields["OBJECT_TYPE"]      = "ASSIGN_TASK";

    $oCase->updateCase( $_SESSION['APPLICATION'], $appFields );

    // Send notifications - Start
    $oUser = new Users();
    $aUser = $oUser->load($_SESSION['USER_LOGGED']);
    $fromName = $aUser['USR_FIRSTNAME'] . ' ' . $aUser['USR_LASTNAME'];

    $sFromData = $fromName . ($aUser['USR_EMAIL'] != '' ? ' <' . $aUser['USR_EMAIL'] . '>' : '');

    $flagGmail = false;
    /*----------------------------------********---------------------------------*/

    try {
        $oCase->sendNotifications( $_SESSION['TASK'], $_POST['form']['TASKS'], $appFields['APP_DATA'], $_SESSION['APPLICATION'], $_SESSION['INDEX'], $sFromData );
    } catch (Exception $e) {
        G::SendTemporalMessage( G::loadTranslation( 'ID_NOTIFICATION_ERROR' ) . ' - ' . $e->getMessage(), 'warning', 'string', null, '100%' );
    }
    // Send notifications - End
    // Events - Start
    $oEvent = new Event();

    $oEvent->closeAppEvents( $_SESSION['PROCESS'], $_SESSION['APPLICATION'], $_SESSION['INDEX'], $_SESSION['TASK'] );
    $oCurrentAppDel = AppDelegationPeer::retrieveByPk( $_SESSION['APPLICATION'], $_SESSION['INDEX'] + 1 );
    $multipleDelegation = false;
    // check if there are multiple derivations
    if (count( $_POST['form']['TASKS'] ) > 1) {
        $multipleDelegation = true;
    }
    // If the case has been delegated
    if (isset( $oCurrentAppDel )) {
        // if there is just a single derivation the TASK_UID can be set by the delegation data
        if (! $multipleDelegation) {
            $aCurrentAppDel = $oCurrentAppDel->toArray( BasePeer::TYPE_FIELDNAME );
            $oEvent->createAppEvents( $aCurrentAppDel['PRO_UID'], $aCurrentAppDel['APP_UID'], $aCurrentAppDel['DEL_INDEX'], $aCurrentAppDel['TAS_UID'] );
        } else {
            // else we need to check every task and create the events if it have any
            foreach ($_POST['form']['TASKS'] as $taskDelegated) {
                $aCurrentAppDel = $oCurrentAppDel->toArray( BasePeer::TYPE_FIELDNAME );
                $oEvent->createAppEvents( $aCurrentAppDel['PRO_UID'], $aCurrentAppDel['APP_UID'], $aCurrentAppDel['DEL_INDEX'], $taskDelegated['TAS_UID'] );
            }
        }
    }
    //Events - End

    /*----------------------------------********---------------------------------*/

    $debuggerAvailable = true;

    $casesRedirector = 'casesListExtJsRedirector';
    if (isset ( $_SESSION ['user_experience'] ) && $flagGmail === false) {
        $aNextStep ['PAGE'] = $casesRedirector . '?ux=' . $_SESSION ['user_experience'];
        $debuggerAvailable = false;
    } else if ($flagGmail === true) {
        $aNextStep ['PAGE'] = $casesRedirector . '?gmail=1';
    } else {
        $aNextStep ['PAGE'] = $casesRedirector;
    }

    if (isset( $_SESSION['PMDEBUGGER'] ) && $_SESSION['PMDEBUGGER'] && $debuggerAvailable) {
        $_SESSION['TRIGGER_DEBUG']['BREAKPAGE'] = $aNextStep['PAGE'];
        $loc = 'cases_Step?' . 'breakpoint=triggerdebug';
    } else {
        $loc = $aNextStep['PAGE'];
    }
    //Triggers After
    $isIE = Bootstrap::isIE();

    if (isset( $_SESSION['TRIGGER_DEBUG']['ISSET'] ) && !$isIE) {
        if ($_SESSION['TRIGGER_DEBUG']['ISSET'] == 1) {
            $oTemplatePower = new TemplatePower( PATH_TPL . 'cases/cases_Step.html' );
            $oTemplatePower->prepare();
            $G_PUBLISH = new Publisher();
            $G_PUBLISH->AddContent( 'template', '', '', '', $oTemplatePower );
            $_POST['NextStep'] = $loc;
            $G_PUBLISH->AddContent( 'view', 'cases/showDebugFrameLoader' );
            $G_PUBLISH->AddContent( 'view', 'cases/showDebugFrameBreaker' );
            $_SESSION['TRIGGER_DEBUG']['ISSET'] == 0;
            G::RenderPage( 'publish', 'blank' );
            exit();
        } else {
            unset( $_SESSION['TRIGGER_DEBUG'] );
        }
    }

    //close tab only if IE11

    if($isIE && !isset($_SESSION['__OUTLOOK_CONNECTOR__'])) {
        $script = "<script type='text/javascript'>
                       try {
                           if(top.opener) {
                               top.opener.location.reload();
                               top.close();
                           }
                       } catch(e) {
                       }
                   </script>";
        die($script);
    }

    G::header( "location: $loc" );
} catch (Exception $e) {
    $aMessage = array ();
    $aMessage['MESSAGE'] = $e->getMessage();
    $G_PUBLISH = new Publisher();
    $G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'login/showMessage', '', $aMessage );
    G::RenderPage( 'publish', 'blank' );
}
