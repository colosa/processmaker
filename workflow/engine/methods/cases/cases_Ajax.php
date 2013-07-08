<?php
if (!isset($_SESSION['USER_LOGGED'])) {
    $response = new stdclass();
    $response->message = G::LoadTranslation('ID_LOGIN_AGAIN');
    $response->lostSession = true;
    print G::json_encode( $response );
    die();
}
/**
 * cases_Ajax.php
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

G::LoadClass( 'case' );
$oCase = new Cases();

//if($RBAC->userCanAccess('PM_ALLCASES') < 0) {
//    $oCase->thisIsTheCurrentUser( $_SESSION['APPLICATION'],
//                                  $_SESSION['INDEX'],
//                                  $_SESSION['USER_LOGGED'],
//                                  'SHOW_MESSAGE');
//}


if (($RBAC_Response = $RBAC->userCanAccess( "PM_CASES" )) != 1) {
    return $RBAC_Response;
}

if (isset( $_POST['showWindow'] )) {
    if ($_POST['showWindow'] == 'steps') {
        $fn = 'showSteps();';
    } elseif ($_POST['showWindow'] == 'information') {
        $fn = 'showInformation();';
    } elseif ($_POST['showWindow'] == 'actions') {
        $fn = 'showActions();';
    } elseif ($_POST['showWindow'] == 'false') {
        $fn = '';
    } else {
        if ($_POST['showWindow'] != '') {
            $fn = false;
        }
    }
    $_SESSION['showCasesWindow'] = $fn;
}

if (! isset( $_POST['action'] )) {
    $_POST['action'] = '';
}

switch (($_POST['action']) ? $_POST['action'] : $_REQUEST['action']) {
    case 'steps':
        global $G_PUBLISH;
        $G_PUBLISH = new Publisher();
        $G_PUBLISH->AddContent( 'view', 'cases/cases_StepsTree' );
        G::RenderPage( 'publish', 'raw' );
        break;
    case 'information':
        global $G_PUBLISH;
        $G_PUBLISH = new Publisher();
        $G_PUBLISH->AddContent( 'view', 'cases/cases_InformationTree' );
        G::RenderPage( 'publish', 'raw' );
        break;
    case 'actions':
        global $G_PUBLISH;
        $G_PUBLISH = new Publisher();
        $G_PUBLISH->AddContent( 'view', 'cases/cases_ActionsTree' );
        G::RenderPage( 'publish', 'raw' );
        break;
    case 'showProcessMap':
        G::LoadClass( 'processMap' );
        $oTemplatePower = new TemplatePower( PATH_TPL . 'processes/processes_Map.html' );
        $oTemplatePower->prepare();
        $G_PUBLISH = new Publisher();
        $G_PUBLISH->AddContent( 'template', '', '', '', $oTemplatePower );
        $oHeadPublisher = & headPublisher::getSingleton();
        $oHeadPublisher->addScriptCode( '
            var maximunX = ' . processMap::getMaximunTaskX( $_SESSION['PROCESS'] ) . ';
            var pb=leimnud.dom.capture("tag.body 0");
            Pm=new processmap();

            var params = "{\"uid\":\"' . $_SESSION['PROCESS'] . '\",\"mode\":false,\"ct\":false}";
            // maximun x and y position
            var xPos = 0;
            var yPos = 0;

            //obtaining the processmap object for the current process
            var oRPC = new leimnud.module.rpc.xmlhttp({
                url   : "../processes/processes_Ajax",
                async : false,
                method: "POST",
                args  : "action=load&data="+params
            });

            // make the ajax call
            oRPC.make();
            var response = eval(\'(\' + oRPC.xmlhttp.responseText + \')\');
            //alert(response);

            for (var i in response) {
                if (i==\'task\') {
                    elements = response[i];
                    for (var j in elements) {
                        if (elements[j].uid!=undefined) {
                            if (elements[j].position.x > xPos) {
                                xPos = elements[j].position.x;
                            }
                            if (elements[j].position.y > yPos) {
                                yPos = elements[j].position.y;
                            }
                        }
                    }
                }
            }

            Pm.options = {
                target    : "pm_target",
                dataServer: "../processes/processes_Ajax",
                uid       : "' . $_SESSION['PROCESS'] . '",
                lang      : "' . SYS_LANG . '",
                theme     : "processmaker",
                size      : {w:xPos+200,h:yPos+150},
                images_dir: "/jscore/processmap/core/images/",
                rw        : false,
                hideMenu  : false
            }
            Pm.make();' );
        G::RenderPage( 'publish', 'raw' );
        break;
    case 'showLeyends':
        $aFields = array ();
        $aFields['sLabel1'] = G::LoadTranslation( 'ID_TASK_IN_PROGRESS' );
        $aFields['sLabel2'] = G::LoadTranslation( 'ID_COMPLETED_TASK' );
        $aFields['sLabel3'] = G::LoadTranslation( 'ID_PENDING_TASK' );
        $aFields['sLabel4'] = G::LoadTranslation( 'ID_PARALLEL_TASK' );
        $G_PUBLISH = new Publisher();
        $G_PUBLISH->AddContent( 'smarty', 'cases/cases_Leyends', '', '', $aFields );
        G::RenderPage( 'publish', 'raw' );
        break;
    case 'showProcessInformation':
        //require_once 'classes/model/Process.php';
        $oProcess = new Process();
        $aFields = $oProcess->load( $_SESSION['PROCESS'] );
        require_once 'classes/model/Users.php';
        $oUser = new Users();
        try {
            $aUser = $oUser->load( $aFields['PRO_CREATE_USER'] );
            $aFields['PRO_AUTHOR'] = $aUser['USR_FIRSTNAME'] . ' ' . $aUser['USR_LASTNAME'];
        } catch (Exception $oError) {
            $aFields['PRO_AUTHOR'] = '(USER DELETED)';
        }
        $aFields['PRO_CREATE_DATE'] = date( 'F j, Y', strtotime( $aFields['PRO_CREATE_DATE'] ) );
        global $G_PUBLISH;
        $G_PUBLISH = new Publisher();
        $G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'cases/cases_ProcessInformation', '', $aFields );
        G::RenderPage( 'publish', 'raw' );
        break;
    case 'showTransferHistory':
        G::LoadClass( "case" );
        $c = Cases::getTransferHistoryCriteria( $_SESSION['APPLICATION'] );
        $G_PUBLISH = new Publisher();
        $G_PUBLISH->AddContent( 'propeltable', 'paged-table', 'cases/cases_TransferHistory', $c, array () );
        G::RenderPage( 'publish', 'raw' );
        break;
    case 'showDynaformListHistory':
        //require_once 'classes/model/AppHistory.php';
        $G_PUBLISH = new Publisher();
        $G_PUBLISH->AddContent( 'view', 'cases/cases_DynaformHistory' );
        G::RenderPage( 'publish', 'raw' );
        break;
    case 'showTaskInformation':
        //require_once 'classes/model/AppDelegation.php';
        //require_once 'classes/model/Task.php';
        $oTask = new Task();
        $aFields = $oTask->load( $_SESSION['TASK'] );
        $oCriteria = new Criteria( 'workflow' );
        $oCriteria->add( AppDelegationPeer::APP_UID, $_SESSION['APPLICATION'] );
        $oCriteria->add( AppDelegationPeer::DEL_INDEX, $_SESSION['INDEX'] );
        $oDataset = AppDelegationPeer::doSelectRS( $oCriteria );
        $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
        $oDataset->next();
        $aDelegation = $oDataset->getRow();
        $iDiff = strtotime( $aDelegation['DEL_FINISH_DATE'] ) - strtotime( $aDelegation['DEL_INIT_DATE'] );
        $aFields['INIT_DATE'] = ($aDelegation['DEL_INIT_DATE'] != null ? $aDelegation['DEL_INIT_DATE'] : G::LoadTranslation( 'ID_CASE_NOT_YET_STARTED' ));
        $aFields['DUE_DATE'] = ($aDelegation['DEL_TASK_DUE_DATE'] != null ? $aDelegation['DEL_TASK_DUE_DATE'] : G::LoadTranslation( 'ID_NOT_FINISHED' ));
        $aFields['FINISH'] = ($aDelegation['DEL_FINISH_DATE'] != null ? $aDelegation['DEL_FINISH_DATE'] : G::LoadTranslation( 'ID_NOT_FINISHED' ));
        $aFields['DURATION'] = ($aDelegation['DEL_FINISH_DATE'] != null ? (int) ($iDiff / 3600) . ' ' . ((int) ($iDiff / 3600) == 1 ? G::LoadTranslation( 'ID_HOUR' ) : G::LoadTranslation( 'ID_HOURS' )) . ' ' . (int) (($iDiff % 3600) / 60) . ' ' . ((int) (($iDiff % 3600) / 60) == 1 ? G::LoadTranslation( 'ID_MINUTE' ) : G::LoadTranslation( 'ID_MINUTES' )) . ' ' . (int) (($iDiff % 3600) % 60) . ' ' . ((int) (($iDiff % 3600) % 60) == 1 ? G::LoadTranslation( 'ID_SECOND' ) : G::LoadTranslation( 'ID_SECONDS' )) : G::LoadTranslation( 'ID_NOT_FINISHED' ));
        global $G_PUBLISH;
        $G_PUBLISH = new Publisher();
        $G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'cases/cases_TaskInformation', '', $aFields );
        G::RenderPage( 'publish', 'raw' );
        break;
    case 'showTaskDetails':
        //require_once 'classes/model/AppDelegation.php';
        //require_once 'classes/model/Task.php';
        //require_once 'classes/model/Users.php';
        $oTask = new Task();
        $aRow = $oTask->load( $_POST['sTaskUID'] );
        $sTitle = $aRow['TAS_TITLE'];
        $oCriteria = new Criteria( 'workflow' );
        $oCriteria->addSelectColumn( UsersPeer::USR_UID );
        $oCriteria->addSelectColumn( UsersPeer::USR_FIRSTNAME );
        $oCriteria->addSelectColumn( UsersPeer::USR_LASTNAME );
        $oCriteria->addSelectColumn( AppDelegationPeer::DEL_INIT_DATE );
        $oCriteria->addSelectColumn( AppDelegationPeer::DEL_TASK_DUE_DATE );
        $oCriteria->addSelectColumn( AppDelegationPeer::DEL_FINISH_DATE );
        $oCriteria->addJoin( AppDelegationPeer::USR_UID, UsersPeer::USR_UID, Criteria::LEFT_JOIN );
        $oCriteria->add( AppDelegationPeer::APP_UID, $_SESSION['APPLICATION'] );
        $oCriteria->add( AppDelegationPeer::TAS_UID, $_POST['sTaskUID'] );
        $oCriteria->addDescendingOrderByColumn( AppDelegationPeer::DEL_INDEX );
        $oDataset = AppDelegationPeer::doSelectRS( $oCriteria );
        $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
        $oDataset->next();
        $aRow = $oDataset->getRow();
        $iDiff = strtotime( $aRow['DEL_FINISH_DATE'] ) - strtotime( $aRow['DEL_INIT_DATE'] );
        $aFields = array ();
        $aFields['TASK'] = $sTitle;
        $aFields['USER'] = ($aRow['USR_UID'] != null ? $aRow['USR_FIRSTNAME'] . ' ' . $aRow['USR_LASTNAME'] : G::LoadTranslation( 'ID_NONE' ));
        $aFields['INIT_DATE'] = ($aRow['DEL_INIT_DATE'] != null ? $aRow['DEL_INIT_DATE'] : G::LoadTranslation( 'ID_CASE_NOT_YET_STARTED' ));
        $aFields['DUE_DATE'] = ($aRow['DEL_TASK_DUE_DATE'] != null ? $aRow['DEL_TASK_DUE_DATE'] : G::LoadTranslation( 'ID_CASE_NOT_YET_STARTED' ));
        $aFields['FINISH'] = ($aRow['DEL_FINISH_DATE'] != null ? $aRow['DEL_FINISH_DATE'] : G::LoadTranslation( 'ID_NOT_FINISHED' ));
        $aFields['DURATION'] = ($aRow['DEL_FINISH_DATE'] != null ? (int) ($iDiff / 3600) . ' ' . ((int) ($iDiff / 3600) == 1 ? G::LoadTranslation( 'ID_HOUR' ) : G::LoadTranslation( 'ID_HOURS' )) . ' ' . (int) (($iDiff % 3600) / 60) . ' ' . ((int) (($iDiff % 3600) / 60) == 1 ? G::LoadTranslation( 'ID_MINUTE' ) : G::LoadTranslation( 'ID_MINUTES' )) . ' ' . (int) (($iDiff % 3600) % 60) . ' ' . ((int) (($iDiff % 3600) % 60) == 1 ? G::LoadTranslation( 'ID_SECOND' ) : G::LoadTranslation( 'ID_SECONDS' )) : G::LoadTranslation( 'ID_NOT_FINISHED' ));
        global $G_PUBLISH;
        $G_PUBLISH = new Publisher();
        $G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'cases/cases_TaskDetails', '', $aFields );
        G::RenderPage( 'publish', 'raw' );
        break;
    case 'showUsers':
        switch ($_POST['TAS_ASSIGN_TYPE']) {
            // switch verify $_POST['TAS_ASSIGN_TYPE']
            case 'BALANCED':
                G::LoadClass( 'user' );
                $oUser = new User( new DBConnection() );
                $oUser->load( $_POST['USR_UID'] );
                echo $oUser->Fields['USR_FIRSTNAME'] . ' ' . $oUser->Fields['USR_LASTNAME'] . '<input type="hidden" name="form[TASKS][1][USR_UID]" id="form[TASKS][1][USR_UID]" value="' . $_POST['USR_UID'] . '">';
                break;
            case 'MANUAL':
                $sAux = '<select name="form[TASKS][1][USR_UID]" id="form[TASKS][1][USR_UID]">';
                $oSession = new DBSession( new DBConnection() );
                /*
                $oDataset = $oSession->Execute("SELECT
                                                TU.USR_UID AS USR_UID,
                                                CONCAT(U.USR_LASTNAME, ' ', U.USR_FIRSTNAME) AS USR_FULLNAME
                                            FROM
                                                TASK_USER AS TU
                                            LEFT JOIN
                                                USERS AS U
                                            ON (
                                                TU.USR_UID = U.USR_UID
                                            )
                                            WHERE
                                                TU.TAS_UID     = '" . $_POST['TAS_UID'] . "' AND
                                                TU.TU_TYPE     = 1 AND
                                                TU.TU_RELATION = 1 AND
                                                U.USR_STATUS   = 1");
                */
                $sDataBase = 'database_' . strtolower( DB_ADAPTER );
                if (G::LoadSystemExist( $sDataBase )) {
                    G::LoadSystem( $sDataBase );
                    $oDataBase = new database();
                    $sConcat = $oDataBase->concatString( "U.USR_LASTNAME", "' '", "U.USR_FIRSTNAME" );
                }
                $sSQL = "   SELECT
                                    TU.USR_UID AS USR_UID, " . $sConcat . " AS USR_FULLNAME
                                FROM
                                    TASK_USER AS TU
                                    LEFT JOIN
                                    USERS AS U
                                    ON (
                                        TU.USR_UID = U.USR_UID
                                    )
                                WHERE
                                    TU.TAS_UID     = '" . $_POST['TAS_UID'] . "' AND
                                    TU.TU_TYPE     = 1 AND
                                    TU.TU_RELATION = 1 AND
                                    U.USR_STATUS   = 1";
                $oDataset = $oSession->Execute( $sSQL );

                while ($aRow = $oDataset->Read()) {
                    $sAux .= '<option value="' . $aRow['USR_UID'] . '">' . $aRow['USR_FULLNAME'] . '</option>';
                }
                $sAux .= '</select>';
                echo $sAux;
                break;
            case 'EVALUATE':
                G::LoadClass( 'application' );
                $oApplication = new Application( new DBConnection() );
                $oApplication->load( $_SESSION['APPLICATION'] );
                $sUser = '';
                if ($_POST['TAS_ASSIGN_VARIABLE'] != '') {
                    if (isset( $oApplication->Fields['APP_DATA'][str_replace( '@@', '', $_POST['TAS_ASSIGN_VARIABLE'] )] )) {
                        $sUser = $oApplication->Fields['APP_DATA'][str_replace( '@@', '', $_POST['TAS_ASSIGN_VARIABLE'] )];
                    }
                }
                if ($sUser != '') {
                    G::LoadClass( 'user' );
                    $oUser = new User( new DBConnection() );
                    $oUser->load( $sUser );
                    echo $oUser->Fields['USR_FIRSTNAME'] . ' ' . $oUser->Fields['USR_LASTNAME'] . '<input type="hidden" name="form[TASKS][1][USR_UID]" id="form[TASKS][1][USR_UID]" value="' . $sUser . '">';
                } else {
                    echo '<strong>Error: </strong>' . $_POST['TAS_ASSIGN_VARIABLE'] . ' ' . G::LoadTranslation( 'ID_EMPTY' );
                    echo '<input type="hidden" name="_ERROR_" id="_ERROR_" value="">';
                }
                break;
            case 'SELFSERVICE':
                //Next release
                break;
        }
        break;
    case 'cancelCase':
        $oCase = new Cases();
        $multiple = false;

        if (isset( $_POST['APP_UID'] ) && isset( $_POST['DEL_INDEX'] )) {
            $APP_UID = $_POST['APP_UID'];
            $DEL_INDEX = $_POST['DEL_INDEX'];

            $appUids = explode( ',', $APP_UID );
            $delIndexes = explode( ',', $DEL_INDEX );
            if (count( $appUids ) > 1 && count( $delIndexes ) > 1) {
                $multiple = true;
            }
        } elseif (isset( $_POST['sApplicationUID'] ) && isset( $_POST['iIndex'] )) {
            $APP_UID = $_POST['sApplicationUID'];
            $DEL_INDEX = $_POST['iIndex'];
        } else {
            $APP_UID = $_SESSION['APPLICATION'];
            $DEL_INDEX = $_SESSION['INDEX'];
        }

        if ($multiple) {
            foreach ($appUids as $i => $appUid) {
                $oCase->cancelCase( $appUid, $delIndexes[$i], $_SESSION['USER_LOGGED'] );
            }
        } else {
            $oCase->cancelCase( $APP_UID, $DEL_INDEX, $_SESSION['USER_LOGGED'] );
        }
        break;
    case 'reactivateCase':
        $sApplicationUID = isset( $_POST['sApplicationUID'] ) ? $_POST['sApplicationUID'] : $_SESSION['APPLICATION'];
        $iIndex = (isset( $_POST['sApplicationUID'] )) ? $_POST['iIndex'] : $_SESSION['INDEX'];
        $oCase = new Cases();
        $oCase->reactivateCase( $sApplicationUID, $iIndex, $_SESSION['USER_LOGGED'] );
        break;
    case 'showPauseCaseInput':
        //echo '<input type=button onclick="close_pauseCase()" value="Cancel">';
        $aFields = Array ();
        $G_PUBLISH = new Publisher();
        $aFields['TIME_STAMP'] = G::getformatedDate( date( 'Y-m-d' ), 'M d, yyyy', SYS_LANG );

        $G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'cases/cases_UnpauseDateInput', '', $aFields );
        G::RenderPage( 'publish', 'raw' );
        break;
    case 'pauseCase':
        // Save the note pause reason
        if ($_POST['NOTE_REASON'] != '') {
            require_once ("classes/model/AppNotes.php");
            $appNotes = new AppNotes();
            $noteContent = addslashes( $_POST['NOTE_REASON'] );
            $result = $appNotes->postNewNote( $_POST['APP_UID'], $_SESSION['USER_LOGGED'], $noteContent, $_POST['NOTIFY_PAUSE'] );
        }
        // End save


        $unpauseDate = $_POST['unpausedate'] . ' '. $_REQUEST['unpauseTime'];
        $oCase = new Cases();
        if (isset( $_POST['APP_UID'] ) && isset( $_POST['DEL_INDEX'] )) {
            $APP_UID = $_POST['APP_UID'];
            $DEL_INDEX = $_POST['DEL_INDEX'];
        } elseif (isset( $_POST['sApplicationUID'] ) && isset( $_POST['iIndex'] )) {
            $APP_UID = $_POST['sApplicationUID'];
            $DEL_INDEX = $_POST['iIndex'];
        } else {
            $APP_UID = $_SESSION['APPLICATION'];
            $DEL_INDEX = $_SESSION['INDEX'];
        }

        $oCase->pauseCase( $APP_UID, $DEL_INDEX, $_SESSION['USER_LOGGED'], $unpauseDate );
        break;
    case 'unpauseCase':
        $sApplicationUID = (isset( $_POST['sApplicationUID'] )) ? $_POST['sApplicationUID'] : $_SESSION['APPLICATION'];
        $iIndex = (isset( $_POST['sApplicationUID'] )) ? $_POST['iIndex'] : $_SESSION['INDEX'];
        $oCase = new Cases();
        $oCase->unpauseCase( $sApplicationUID, $iIndex, $_SESSION['USER_LOGGED'] );
        break;
    case 'deleteCase':
        $oCase = new Cases();
        $sApplicationUID = (isset( $_POST['sApplicationUID'] )) ? $_POST['sApplicationUID'] : $_SESSION['APPLICATION'];
        $oCase->removeCase( $sApplicationUID );
        break;
    case 'view_reassignCase':
        G::LoadClass( 'groups' );
        G::LoadClass( 'tasks' );

        $oTasks = new Tasks();
        $aAux = $oTasks->getGroupsOfTask( $_SESSION['TASK'], 1 );
        $row = array ();

        $groups = new Groups();
        foreach ($aAux as $aGroup) {
            $aUsers = $groups->getUsersOfGroup( $aGroup['GRP_UID'] );
            foreach ($aUsers as $aUser) {
                if ($aUser['USR_UID'] != $_SESSION['USER_LOGGED']) {
                    $row[] = $aUser['USR_UID'];
                }
            }
        }

        $aAux = $oTasks->getUsersOfTask( $_SESSION['TASK'], 1 );
        foreach ($aAux as $aUser) {
            if ($aUser['USR_UID'] != $_SESSION['USER_LOGGED']) {
                $row[] = $aUser['USR_UID'];
            }
        }

        //require_once 'classes/model/Users.php';
        $c = new Criteria( 'workflow' );
        $c->addSelectColumn( UsersPeer::USR_UID );
        $c->addSelectColumn( UsersPeer::USR_FIRSTNAME );
        $c->addSelectColumn( UsersPeer::USR_LASTNAME );
        $c->add( UsersPeer::USR_UID, $row, Criteria::IN );

        global $G_PUBLISH;
        $G_PUBLISH = new Publisher();
        $G_PUBLISH->AddContent( 'propeltable', 'paged-table', 'processes/processes_viewreassignCase', $c );
        G::RenderPage( 'publish', 'raw' );
        break;
    case 'reassignCase':
        $cases = new Cases();
        $cases->reassignCase( $_SESSION['APPLICATION'], $_SESSION['INDEX'], $_SESSION['USER_LOGGED'], $_POST['USR_UID'], $_POST['THETYPE'] );
        break;
    case 'toRevisePanel':
        $_GET['APP_UID'] = $_POST['APP_UID'];
        $_GET['DEL_INDEX'] = $_POST['DEL_INDEX'];
        $G_PUBLISH = new Publisher();

        echo "<iframe scrolling='no' style='border:none;height=300px;width:240px;'" . " src='casesToRevisePanelExtJs?APP_UID={$_GET['APP_UID']}&DEL_INDEX={$_GET['DEL_INDEX']}'></iframe>";
        //  $G_PUBLISH->AddContent( 'smarty', 'cases/cases_toRevise' );
        //  $G_PUBLISH->AddContent('smarty', 'cases/cases_toReviseIn', '', '', array());
        G::RenderPage( 'publish', 'raw' );
        break;
    case 'showUploadedDocuments':
        $oCase = new Cases();
        global $G_PUBLISH;
        $G_PUBLISH = new Publisher();
        $G_PUBLISH->AddContent( 'propeltable', 'paged-table', 'cases/cases_AllInputdocsList', $oCase->getAllUploadedDocumentsCriteria( $_SESSION['PROCESS'], $_SESSION['APPLICATION'], $_SESSION['TASK'], $_SESSION['USER_LOGGED'] ) );
        G::RenderPage( 'publish', 'raw' );
        break;
    case 'showUploadedDocument':
        //require_once 'classes/model/AppDocument.php';
        //require_once 'classes/model/AppDelegation.php';
        //require_once 'classes/model/InputDocument.php';
        //require_once 'classes/model/Users.php';
        $oAppDocument = new AppDocument();
        $oAppDocument->Fields = $oAppDocument->load( $_POST['APP_DOC_UID'] );
        $oInputDocument = new InputDocument();
        if ($oAppDocument->Fields['DOC_UID'] != - 1) {
            $Fields = $oInputDocument->load( $oAppDocument->Fields['DOC_UID'] );
        } else {
            $Fields = array ('INP_DOC_FORM_NEEDED' => '','FILENAME' => $oAppDocument->Fields['APP_DOC_FILENAME']);
        }
        $oCriteria = new Criteria( 'workflow' );
        $oCriteria->add( AppDelegationPeer::APP_UID, $oAppDocument->Fields['APP_UID'] );
        $oCriteria->add( AppDelegationPeer::DEL_INDEX, $oAppDocument->Fields['DEL_INDEX'] );
        $oDataset = AppDelegationPeer::doSelectRS( $oCriteria );
        $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
        $oDataset->next();
        $aRow = $oDataset->getRow();
        $oTask = new Task();
        try {
            $aTask = $oTask->load( $aRow['TAS_UID'] );
            $Fields['ORIGIN'] = $aTask['TAS_TITLE'];
            $oAppDocument->Fields['VIEW'] = G::LoadTranslation( 'ID_OPEN' );
        } catch (Exception $oException) {
            $Fields['ORIGIN'] = '(TASK DELETED)';
        }

        try {
            $oUser = new Users();
            $aUser = $oUser->load( $oAppDocument->Fields['USR_UID'] );
            $Fields['CREATOR'] = $aUser['USR_FIRSTNAME'] . ' ' . $aUser['USR_LASTNAME'];
        } catch (Exception $e) {
            $Fields['CREATOR'] = '***';
        }
        switch ($Fields['INP_DOC_FORM_NEEDED']) {
            // switch verify $Fields['INP_DOC_FORM_NEEDED']
            case 'REAL':
                $sXmlForm = 'cases/cases_ViewAnyInputDocument2';
                break;
            case 'VIRTUAL':
                $sXmlForm = 'cases/cases_ViewAnyInputDocument1';
                break;
            case 'VREAL':
                $sXmlForm = 'cases/cases_ViewAnyInputDocument3';
                break;
            default:
                $sXmlForm = 'cases/cases_ViewAnyInputDocument';
                break;
        }
        //$oAppDocument->Fields['VIEW'] = G::LoadTranslation('ID_OPEN');
        $oAppDocument->Fields['FILE'] = 'cases_ShowDocument?a=' . $_POST['APP_DOC_UID'] . '&r=' . rand();
        $G_PUBLISH = new Publisher();
        $G_PUBLISH->AddContent( 'xmlform', 'xmlform', $sXmlForm, '', G::array_merges( $Fields, $oAppDocument->Fields ), '' );
        G::RenderPage( 'publish', 'raw' );
        break;
    case 'showGeneratedDocuments':
        global $G_PUBLISH;
        $oCase = new Cases();
        $G_PUBLISH = new Publisher();
        $G_PUBLISH->AddContent( 'propeltable', 'paged-table', 'cases/cases_AllOutputdocsList', $oCase->getAllGeneratedDocumentsCriteria( $_SESSION['PROCESS'], $_SESSION['APPLICATION'], $_SESSION['TASK'], $_SESSION['USER_LOGGED'] ) );

        G::RenderPage( 'publish', 'raw' );
        break;
    case 'uploadDocumentGrid_Ajax':
        G::LoadClass( 'case' );
        G::LoadClass( "BasePeer" );
        global $G_PUBLISH;

        $oCase = new Cases();
        $aProcesses = Array ();
        $G_PUBLISH = new Publisher();
        $c = $oCase->getAllUploadedDocumentsCriteria( $_SESSION['PROCESS'], $_SESSION['APPLICATION'], $_SESSION['TASK'], $_SESSION['USER_LOGGED'] );

        if ($c->getDbName() == 'dbarray') {
            $rs = ArrayBasePeer::doSelectRs( $c );
        } else {
            $rs = GulliverBasePeer::doSelectRs( $c );
        }

        $rs->setFetchmode( ResultSet::FETCHMODE_ASSOC );
        $rs->next();

        $totalCount = 0;

        for ($j = 0; $j < $rs->getRecordCount(); $j ++) {
            $result = $rs->getRow();
            $aProcesses[] = $result;
            $rs->next();
            $totalCount ++;
        }

        $r = new stdclass();
        $r->data = $aProcesses;
        $r->totalCount = $totalCount;

        echo Bootstrap::json_encode( $r );
        break;
    case 'generateDocumentGrid_Ajax':

        G::LoadClass( 'case' );
        G::LoadClass( "BasePeer" );
        G::LoadClass( 'configuration' );
        global $G_PUBLISH;

        $oCase = new Cases();
        $aProcesses = Array ();
        $G_PUBLISH = new Publisher();
        $c = $oCase->getAllGeneratedDocumentsCriteria( $_SESSION['PROCESS'], $_SESSION['APPLICATION'], $_SESSION['TASK'], $_SESSION['USER_LOGGED'] );

        if ($c->getDbName() == 'dbarray') {
            $rs = ArrayBasePeer::doSelectRs( $c );
        } else {
            $rs = GulliverBasePeer::doSelectRs( $c );
        }

        $rs->setFetchmode( ResultSet::FETCHMODE_ASSOC );
        $rs->next();

        $totalCount = 0;

        for ($j = 0; $j < $rs->getRecordCount(); $j ++) {
            $result = $rs->getRow();
            $result["FILEDOCEXIST"] = ($result["FILEDOC"]);
            $result["FILEPDFEXIST"] = ($result["FILEPDF"]);
            $result["DELETE_FILE"] = (isset( $result['ID_DELETE'] ) && $result['ID_DELETE'] == 'Delete') ? true : false;

            $aProcesses[] = $result;

            $rs->next();
            $totalCount ++;
        }

        //!dateFormat
        $conf = new Configurations();

        try {
            $globaleneralConfCasesList = $conf->getConfiguration( 'ENVIRONMENT_SETTINGS', '' );
        } catch (Exception $e) {
            $generalConfCasesList = array ();
        }

        $dateFormat = "";
        $varFlag = isset( $generalConfCasesList['casesListDateFormat'] );
        if ($varFlag && ! empty( $generalConfCasesList['casesListDateFormat'] )) {
            $dateFormat = $generalConfCasesList['casesListDateFormat'];
        }

        $r = new stdclass();
        $r->data = $aProcesses;
        $r->totalCount = $totalCount;
        $r->dataFormat = $dateFormat;

        echo Bootstrap::json_encode( $r );
        break;
    case 'showGeneratedDocument':
        //require_once 'classes/model/AppDocument.php';
        //require_once 'classes/model/AppDelegation.php';
        $oAppDocument = new AppDocument();
        $aFields = $oAppDocument->load( $_POST['APP_DOC_UID'] );
        require_once 'classes/model/OutputDocument.php';
        $oOutputDocument = new OutputDocument();
        $aOD = $oOutputDocument->load( $aFields['DOC_UID'] );
        $oCriteria = new Criteria( 'workflow' );
        $oCriteria->add( AppDelegationPeer::APP_UID, $aFields['APP_UID'] );
        $oCriteria->add( AppDelegationPeer::DEL_INDEX, $aFields['DEL_INDEX'] );
        $oDataset = AppDelegationPeer::doSelectRS( $oCriteria );
        $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
        $oDataset->next();
        $aRow = $oDataset->getRow();
        $oTask = new Task();
        $aTask = $oTask->load( $aRow['TAS_UID'] );
        $aFields['ORIGIN'] = $aTask['TAS_TITLE'];
        require_once 'classes/model/Users.php';
        $oUser = new Users();
        $aUser = $oUser->load( $aFields['USR_UID'] );
        $aFields['CREATOR'] = $aUser['USR_FIRSTNAME'] . ' ' . $aUser['USR_LASTNAME'];
        $aFields['VIEW'] = G::LoadTranslation( 'ID_OPEN' );
        $aFields['FILE1'] = 'cases_ShowOutputDocument?a=' . $aFields['APP_DOC_UID'] . '&ext=doc&random=' . rand();
        $aFields['FILE2'] = 'cases_ShowOutputDocument?a=' . $aFields['APP_DOC_UID'] . '&ext=pdf&random=' . rand();
        $G_PUBLISH = new Publisher();
        $G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'cases/cases_ViewAnyOutputDocument', '', G::array_merges( $aOD, $aFields ), '' );
        G::RenderPage( 'publish', 'raw' );
        break;
    case 'showDynaformList':
        $oCase = new Cases();
        global $G_PUBLISH;
        $G_PUBLISH = new Publisher();
        $G_PUBLISH->AddContent( 'propeltable', 'paged-table', 'cases/cases_AllDynaformsList', $oCase->getallDynaformsCriteria( $_SESSION['PROCESS'], $_SESSION['APPLICATION'], $_SESSION['TASK'], $_SESSION['USER_LOGGED'] ) );
        G::RenderPage( 'publish', 'raw' );
        break;
    case 'showDynaform':
        $G_PUBLISH = new Publisher();
        $oCase = new Cases();
        $Fields = $oCase->loadCase( $_SESSION['APPLICATION'] );
        $Fields['APP_DATA']['__DYNAFORM_OPTIONS']['PREVIOUS_STEP_LABEL'] = '';
        $Fields['APP_DATA']['__DYNAFORM_OPTIONS']['NEXT_STEP_LABEL'] = '';
        $Fields['APP_DATA']['__DYNAFORM_OPTIONS']['NEXT_STEP'] = '#';
        $Fields['APP_DATA']['__DYNAFORM_OPTIONS']['NEXT_ACTION'] = 'return false;';
        if (isset( $_POST['DYN_UID'] )) {
            $_SESSION['DYN_UID_PRINT'] = $_POST['DYN_UID'];
        } else {
            $_SESSION['DYN_UID_PRINT'] = $_REQUEST['DYN_UID'];
        }
        if (! isset( $_SESSION['CURRENT_DYN_UID'] )) {
            $_SESSION['CURRENT_DYN_UID'] = $_POST['DYN_UID'] ? $_POST['DYN_UID'] : $_REQUEST['DYN_UID'];
        }
        $G_PUBLISH->AddContent( 'dynaform', 'xmlform', $_SESSION['PROCESS'] . '/' . $_REQUEST['DYN_UID'], '', $Fields['APP_DATA'], '', '', 'view' );
        G::RenderPage( 'publish', 'blank' );
        break;
    case 'showDynaformHistory':
        $G_PUBLISH = new Publisher();
        $FieldsHistory = unserialize( $_SESSION['HISTORY_DATA'] );
        $Fields['APP_DATA'] = $FieldsHistory[$_POST['HISTORY_ID']];
        $Fields['APP_DATA']['__DYNAFORM_OPTIONS']['PREVIOUS_STEP_LABEL'] = '';
        $Fields['APP_DATA']['__DYNAFORM_OPTIONS']['NEXT_STEP_LABEL'] = '';
        $Fields['APP_DATA']['__DYNAFORM_OPTIONS']['NEXT_STEP'] = '#';
        $Fields['APP_DATA']['__DYNAFORM_OPTIONS']['NEXT_ACTION'] = 'return false;';
        $G_PUBLISH->AddContent( 'dynaform', 'xmlform', $_SESSION['PROCESS'] . '/' . $_POST['DYN_UID'], '', $Fields['APP_DATA'], '', '', 'view' );
        G::RenderPage( 'publish', 'raw' );
        break;
    case 'adhocAssignmentUsers':
        G::LoadClass( 'groups' );
        G::LoadClass( 'tasks' );
        $oTasks = new Tasks();
        $aAux = $oTasks->getGroupsOfTask( $_SESSION['TASK'], 2 );
        $aAdhocUsers = array ();
        $oGroups = new Groups();
        foreach ($aAux as $aGroup) {
            $aUsers = $oGroups->getUsersOfGroup( $aGroup['GRP_UID'] );
            foreach ($aUsers as $aUser) {
                if ($aUser['USR_UID'] != $_SESSION['USER_LOGGED']) {
                    $aAdhocUsers[] = $aUser['USR_UID'];
                }
            }
        }
        $aAux = $oTasks->getUsersOfTask( $_SESSION['TASK'], 2 );
        foreach ($aAux as $aUser) {
            if ($aUser['USR_UID'] != $_SESSION['USER_LOGGED']) {
                $aAdhocUsers[] = $aUser['USR_UID'];
            }
        }
        //require_once 'classes/model/Users.php';
        $oCriteria = new Criteria( 'workflow' );
        $oCriteria->addSelectColumn( UsersPeer::USR_UID );
        $oCriteria->addSelectColumn( UsersPeer::USR_FIRSTNAME );
        $oCriteria->addSelectColumn( UsersPeer::USR_LASTNAME );
        $oCriteria->add( UsersPeer::USR_UID, $aAdhocUsers, Criteria::IN );

        global $G_PUBLISH;
        $G_PUBLISH = new Publisher();
        $G_PUBLISH->AddContent( 'propeltable', 'paged-table', 'processes/processes_viewreassignCase', $oCriteria, array ('THETYPE' => 'ADHOC'
        ) );
        G::RenderPage( 'publish', 'raw' );
        break;
    case 'showHistoryMessages':
        $oCase = new Cases();
        global $G_PUBLISH;
        $G_PUBLISH = new Publisher();
        $G_PUBLISH->AddContent( 'propeltable', 'paged-table', 'cases/cases_Messages', $oCase->getHistoryMessagesTracker( $_SESSION['APPLICATION'] ) );
        G::RenderPage( 'publish', 'raw' );
        break;
    case 'showHistoryMessage':
        $G_PUBLISH = new Publisher();
        $oCase = new Cases();

        $G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'cases/cases_MessagesView', '', $oCase->getHistoryMessagesTrackerView( $_POST['APP_UID'], $_POST['APP_MSG_UID'] ) );
        G::RenderPage( 'publish', 'raw' );
        break;
    case 'deleteUploadedDocument':
        //require_once 'classes/model/AppDocument.php';
        $oAppDocument = new AppDocument();
        $oAppDocument->remove( $_POST['DOC'] );
        $oCase = new Cases();
        $oCase->getAllUploadedDocumentsCriteria( $_SESSION['PROCESS'], $_SESSION['APPLICATION'], $_SESSION['TASK'], $_SESSION['USER_LOGGED'] );
        break;
    case 'deleteGeneratedDocument':
        //require_once 'classes/model/AppDocument.php';
        $oAppDocument = new AppDocument();
        $oAppDocument->remove( $_POST['DOC'] );
        $oCase = new Cases();
        $oCase->getAllGeneratedDocumentsCriteria( $_SESSION['PROCESS'], $_SESSION['APPLICATION'], $_SESSION['TASK'], $_SESSION['USER_LOGGED'] );
        break;
    /* @Author Erik Amaru Ortiz <erik@colosa.com> */
    case 'resendMessage':
        //require_once 'classes/model/Configuration.php';
        G::LoadClass( 'spool' );

        $oCase = new Cases();
        $data = $oCase->getHistoryMessagesTrackerView( $_POST['APP_UID'], $_POST['APP_MSG_UID'] );
        //print_r($data);


        G::LoadClass("system");

        $aSetup = System::getEmailConfiguration();

        $passwd = $aSetup['MESS_PASSWORD'];
        $passwdDec = G::decrypt( $passwd, 'EMAILENCRYPT' );
        $auxPass = explode( 'hash:', $passwdDec );
        if (count( $auxPass ) > 1) {
            if (count( $auxPass ) == 2) {
                $passwd = $auxPass[1];
            } else {
                array_shift( $auxPass );
                $passwd = implode( '', $auxPass );
            }
        }
        $aSetup['MESS_PASSWORD'] = $passwd;
        if ($aSetup['MESS_RAUTH'] == false || (is_string($aSetup['MESS_RAUTH']) && $aSetup['MESS_RAUTH'] == 'false')) {
            $aSetup['MESS_RAUTH'] = 0;
        } else {
            $aSetup['MESS_RAUTH'] = 1;
        }

        $oSpool = new spoolRun();
        $oSpool->setConfig(
            array (
                'MESS_ENGINE' => $aSetup['MESS_ENGINE'],
                'MESS_SERVER' => $aSetup['MESS_SERVER'],
                'MESS_PORT' => $aSetup['MESS_PORT'],
                'MESS_ACCOUNT' => $aSetup['MESS_ACCOUNT'],
                'MESS_PASSWORD' => $aSetup['MESS_PASSWORD'],
                'SMTPSecure' => $aSetup['SMTPSecure'],
                'SMTPAuth' => $aSetup['MESS_RAUTH']
            )
        );
        $oSpool->create( array ('msg_uid' => $data['MSG_UID'],'app_uid' => $data['APP_UID'],'del_index' => $data['DEL_INDEX'],'app_msg_type' => $data['APP_MSG_TYPE'],'app_msg_subject' => $data['APP_MSG_SUBJECT'],'app_msg_from' => $data['APP_MSG_FROM'],'app_msg_to' => $data['APP_MSG_TO'],'app_msg_body' => $data['APP_MSG_BODY'],'app_msg_cc' => $data['APP_MSG_CC'],'app_msg_bcc' => $data['APP_MSG_BCC'],'app_msg_attach' => $data['APP_MSG_ATTACH'],'app_msg_template' => $data['APP_MSG_TEMPLATE'],'app_msg_status' => 'pending'
        ) );
        $oSpool->sendMail();
        break;
    /* @Author Erik Amaru Ortiz <erik@colosa.com> */
    case 'showdebug':
        $G_PUBLISH = new Publisher();
        $G_PUBLISH->AddContent( 'view', 'cases/showDebugFrame' );
        G::RenderPage( 'publish', 'raw' );
        break;
    /* @Author Erik Amaru Ortiz <erik@colosa.com> */
    case 'reassignByUserList':
        $APP_UIDS = explode( ',', $_POST['APP_UIDS'] );
        $sReassignFromUser = $_POST['FROM_USR_ID'];

        G::LoadClass( 'tasks' );
        G::LoadClass( 'groups' );
        G::LoadClass( 'case' );

        $oTasks = new Tasks();
        $oGroups = new Groups();
        $oUser = new Users();
        $oCases = new Cases();

        $aCasesList = Array ();

        foreach ($APP_UIDS as $APP_UID) {
            $aCase = $oCases->loadCaseInCurrentDelegation( $APP_UID, true );

            $aUsersInvolved = Array ();
            $aCaseGroups = $oTasks->getGroupsOfTask( $aCase['TAS_UID'], 1 );

            foreach ($aCaseGroups as $aCaseGroup) {
                $aCaseUsers = $oGroups->getUsersOfGroup( $aCaseGroup['GRP_UID'] );
                foreach ($aCaseUsers as $aCaseUser) {
                    if ($aCaseUser['USR_UID'] != $sReassignFromUser) {
                        $aCaseUserRecord = $oUser->load( $aCaseUser['USR_UID'] );
                        $aUsersInvolved[$aCaseUser['USR_UID']] = $aCaseUserRecord['USR_FIRSTNAME'] . ' ' . $aCaseUserRecord['USR_LASTNAME'];
                        // . ' (' . $aCaseUserRecord['USR_USERNAME'] . ')';
                    }
                }
            }

            $aCaseUsers = $oTasks->getUsersOfTask( $aCase['TAS_UID'], 1 );
            foreach ($aCaseUsers as $aCaseUser) {
                if ($aCaseUser['USR_UID'] != $sReassignFromUser) {
                    $aCaseUserRecord = $oUser->load( $aCaseUser['USR_UID'] );
                    $aUsersInvolved[$aCaseUser['USR_UID']] = $aCaseUserRecord['USR_FIRSTNAME'] . ' ' . $aCaseUserRecord['USR_LASTNAME'];
                    // . ' (' . $aCaseUserRecord['USR_USERNAME'] . ')';
                }
            }
            $oTmp = new stdClass();
            $oTmp->items = $aUsersInvolved;
            $oTmp->id = $aCase['APP_UID'];
            $aCase['USERS'] = $oTmp;
            array_push( $aCasesList, $aCase );
        }

        $filedNames = Array ("APP_UID","APP_NUMBER","APP_UPDATE_DATE","DEL_PRIORITY","DEL_INDEX","TAS_UID","DEL_INIT_DATE","DEL_FINISH_DATE","USR_UID","APP_STATUS","DEL_TASK_DUE_DATE","APP_CURRENT_USER","APP_TITLE","APP_PRO_TITLE","APP_TAS_TITLE","APP_DEL_PREVIOUS_USER","USERS"
        );

        $aCasesList = array_merge( Array ($filedNames
        ), $aCasesList );

        global $_DBArray;
        $_DBArray['reassign_byuser'] = $aCasesList;
        G::LoadClass( 'ArrayPeer' );
        $oCriteria = new Criteria( 'dbarray' );
        $oCriteria->setDBArrayTable( 'reassign_byuser' );
        $G_PUBLISH = new Publisher();
        $G_PUBLISH->AddContent( 'propeltable', 'cases/paged-table-reassigByUser2', 'cases/cases_ToReassignByUserList2', $oCriteria );
        G::RenderPage( 'publish', 'raw' );
        break;
    /* @Author Erik Amaru Ortiz <erik@colosa.com> */
    case 'reassignByUser':
        G::LoadClass( 'case' );

        $oCases = new Cases();
        $aCases = Array ();

        if (isset( $_POST['items'] ) && trim( $_POST['items'] ) != '') {
            $sItems = $_POST['items'];
            $aItems = explode( ',', $sItems );
            $FROM_USR_UID = $_POST['USR_UID'];

            foreach ($aItems as $item) {
                list ($APP_UID, $USR_UID) = explode( '|', $item );
                $aCase = $oCases->loadCaseInCurrentDelegation( $APP_UID, true );
                $oCase->reassignCase( $aCase['APP_UID'], $aCase['DEL_INDEX'], $FROM_USR_UID, $USR_UID );
                array_push( $aCases, $aCase );
            }
            //G::pr($aCases);


            //require_once 'classes/model/Users.php';
            $oUser = new Users();
            $sText = '';
            foreach ($aCases as $aCase) {
                $aCaseUpdated = $oCases->loadCaseInCurrentDelegation( $aCase['APP_UID'], true );
                $aUser = $oUser->load( $aCaseUpdated['USR_UID'] );
                $sText .= $aCaseUpdated['APP_PRO_TITLE'] . ' - ' . ' Case: ' . $aCaseUpdated['APP_NUMBER'] . '# (' . $aCaseUpdated['APP_TAS_TITLE'] . ') <b> => Reassigned to => </b> <font color="blue">' . $aUser['USR_FIRSTNAME'] . ' ' . $aUser['USR_LASTNAME'] . ' [' . $aUser['USR_USERNAME'] . ']' . '</font><br />';
            }

            $G_PUBLISH = new Publisher();
            $aMessage['MESSAGE'] = $sText;
            $aMessage['URL'] = 'cases_ReassignByUser?REASSIGN_USER=' . $_POST['USR_UID'];
            $G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'cases/cases_ReassignShowInfo', '', $aMessage );
            G::RenderPage( 'publish', 'raw' );
        }
        break;
    case "uploadInputDocument":
        //krumo($_POST);
        $G_PUBLISH = new Publisher();
        $Fields['DOC_UID'] = $_POST['docID'];
        $Fields['APP_DOC_UID'] = $_POST['appDocId'];
        $Fields['actionType'] = $_POST['actionType'];
        $Fields['docVersion'] = $_POST['docVersion'];
        $G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'cases/cases_AttachInputDocumentGeneral', '', $Fields, 'cases_SaveDocument?UID=' . $_POST['docID'] );
        G::RenderPage( 'publish', 'raw' );
        break;
    case "uploadToReviseInputDocument":
        //krumo($_POST);
        $G_PUBLISH = new Publisher();
        $Fields['DOC_UID'] = $_POST['docID'];
        $Fields['APP_DOC_UID'] = $_POST['appDocId'];
        $Fields['actionType'] = $_POST['actionType'];
        $Fields['docVersion'] = $_POST['docVersion'];
        $G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'cases/cases_AttachInputDocumentGeneral', '', $Fields, 'cases_SupervisorSaveDocument?UID=' . $_POST['docID'] . '&APP_UID=' . $_POST['appDocId'] );
        G::RenderPage( 'publish', 'raw' );
        break;
    case "inputDocumentVersionHistory":
        //krumo($_POST);
        $G_PUBLISH = new Publisher();
        $Fields['DOC_UID'] = $_POST['docID'];
        $Fields['APP_DOC_UID'] = $_POST['appDocId'];
        $G_PUBLISH->AddContent( 'propeltable', 'paged-table', 'cases/cases_InputdocsListHistory', $oCase->getInputDocumentsCriteria( $_SESSION['APPLICATION'], $_SESSION['INDEX'], $_POST['docID'], $_POST['appDocId'] ), array () ); //$aFields
        //$G_PUBLISH->AddContent('xmlform', 'xmlform', 'cases/cases_AttachInputDocumentGeneral',
        // '', $Fields, 'cases_SaveDocument?UID=' . $_POST['docID']);
        G::RenderPage( 'publish', 'raw' );
        break;
    case "getCountCasesFolder":
        //$json = new Services_JSON();
        $aTypes = Array ('to_do','draft','cancelled','sent','paused','completed','selfservice','to_revise','to_reassign');
        $aTypesID = Array ('to_do' => 'CASES_INBOX','draft' => 'CASES_DRAFT','cancelled' => 'CASES_CANCELLED','sent' => 'CASES_SENT','paused' => 'CASES_PAUSED','completed' => 'CASES_COMPLETED','selfservice' => 'CASES_SELFSERVICE','to_revise' => 'CASES_TO_REVISE','to_reassign' => 'CASES_TO_REASSIGN');

        if (! isset( $_POST['A'] )) {
            $oCases = new Cases();
            $aCount = $oCases->getAllConditionCasesCount( $aTypes, true );
            echo Bootstrap::json_encode( $aCount );
        } else {
            echo Bootstrap::json_encode( $aTypesID );
        }
        break;
    case "previusJump":
        //require_once 'classes/model/Application.php';

        $oCriteria = new Criteria( 'workflow' );
        $response = array ("success" => true );

        $oCriteria->add( ApplicationPeer::APP_NUMBER, $_POST['appNumber'] );
        $oDataset = ApplicationPeer::doSelectRS( $oCriteria );
        $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
        $oDataset->next();
        $aApplication = $oDataset->getRow();

        if (is_array( $aApplication )) {
            $response['exists'] = true;
        } else {
            $response['exists'] = false;
        }

        echo Bootstrap::json_encode( $response );
        break;
    default:
        echo 'default';
}

function getCasesTypeIds ()
{
    $aTypes = Array ('to_do','draft','cancelled','sent','paused','completed','selfservice','to_revise','to_reassign');
    return $aTypesID;
}

