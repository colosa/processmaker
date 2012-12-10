<?php
/**
 * trackerAjax.php
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
try {
    if (isset( $_POST['form']['action'] )) {
        $_POST['action'] = $_POST['form']['action'];
    }
    switch ($_POST['action']) {
        case 'availableCaseTrackerObjects':
            G::LoadClass( 'processMap' );
            $oProcessMap = new ProcessMap();
            $oProcessMap->availableCaseTrackerObjects( $_POST['PRO_UID'] );
            break;
        case 'assignCaseTrackerObject':
            G::LoadClass( 'processMap' );
            $oProcessMap = new ProcessMap();
            $cto_UID = $oProcessMap->assignCaseTrackerObject( $_POST['PRO_UID'], $_POST['OBJECT_TYPE'], $_POST['OBJECT_UID'] );
            $oProcessMap->getCaseTrackerObjectsCriteria( $_POST['PRO_UID'] );
            echo $cto_UID;
            break;
        case 'removeCaseTrackerObject':
            G::LoadClass( 'processMap' );
            $oProcessMap = new ProcessMap();
            $oProcessMap->removeCaseTrackerObject( $_POST['CTO_UID'], $_POST['PRO_UID'], $_POST['STEP_POSITION'] );
            $oProcessMap->getCaseTrackerObjectsCriteria( $_POST['PRO_UID'] );
            break;
        case 'upCaseTrackerObject':
            G::LoadClass( 'processMap' );
            $oProcessMap = new ProcessMap();
            $oProcessMap->upCaseTrackerObject( $_POST['CTO_UID'], $_POST['PRO_UID'], $_POST['STEP_POSITION'] );
            $oProcessMap->getCaseTrackerObjectsCriteria( $_POST['PRO_UID'] );
            break;
        case 'downCaseTrackerObject':
            G::LoadClass( 'processMap' );
            $oProcessMap = new ProcessMap();
            $oProcessMap->downCaseTrackerObject( $_POST['CTO_UID'], $_POST['PRO_UID'], $_POST['STEP_POSITION'] );
            $oProcessMap->getCaseTrackerObjectsCriteria( $_POST['PRO_UID'] );
            break;
        case 'editStagesMap':
            $oTemplatePower = new TemplatePower( PATH_TPL . 'tracker/stages_Map.html' );
            $oTemplatePower->prepare();
            $G_PUBLISH = new Publisher();
            $G_PUBLISH->AddContent( 'template', '', '', '', $oTemplatePower );
            $oHeadPublisher = & headPublisher::getSingleton();
            $oHeadPublisher->addScriptCode( '
			  var pb=leimnud.dom.capture("tag.body 0");
			  Sm=new stagesmap();
			  Sm.options = {
			  	target    : "sm_target",
			  	dataServer: "../tracker/tracker_Ajax",
			  	uid       : "' . $_POST['PRO_UID'] . '",
			  	lang      : "' . SYS_LANG . '",
			  	theme     : "processmaker",
			  	size      : {w:"780",h:"540"},
			  	images_dir: "/jscore/processmap/core/images/",
			  	rw        : true,
			  	hideMenu  : false
			  };
			  Sm.make();' );
            G::RenderPage( 'publish', 'raw' );
            break;
        case 'showUploadedDocumentTracker':
            require_once 'classes/model/AppDocument.php';
            require_once 'classes/model/AppDelegation.php';
            require_once 'classes/model/InputDocument.php';
            require_once 'classes/model/Users.php';
            $oAppDocument = new AppDocument();
            $oAppDocument->Fields = $oAppDocument->load( $_POST['APP_DOC_UID'] );

            $oInputDocument = new InputDocument();
            if ($oAppDocument->Fields['DOC_UID'] != - 1) {
                $Fields = $oInputDocument->load( $oAppDocument->Fields['DOC_UID'] );
            } else {
                $Fields = array ('INP_DOC_FORM_NEEDED' => '','FILENAME' => $oAppDocument->Fields['APP_DOC_FILENAME']);
            }
            $oCriteria = new Criteria( 'workflow' );
            $oCriteria->add( AppDelegationPeer::DEL_INDEX, $oAppDocument->Fields['DEL_INDEX'] );
            $oDataset = AppDelegationPeer::doSelectRS( $oCriteria );
            $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
            $oDataset->next();
            $aRow = $oDataset->getRow();
            $oTask = new Task();
            $aTask = $oTask->load( $aRow['TAS_UID'] );
            $Fields['ORIGIN'] = $aTask['TAS_TITLE'];
            $oUser = new Users();
            $aUser = $oUser->load( $oAppDocument->Fields['USR_UID'] );
            $Fields['CREATOR'] = $aUser['USR_FIRSTNAME'] . ' ' . $aUser['USR_LASTNAME'];
            switch ($Fields['INP_DOC_FORM_NEEDED']) {
                case 'REAL':
                    $sXmlForm = 'tracker/tracker_ViewAnyInputDocument2';
                    break;
                case 'VIRTUAL':
                    $sXmlForm = 'tracker/tracker_ViewAnyInputDocument1';
                    break;
                case 'VREAL':
                    $sXmlForm = 'tracker/tracker_ViewAnyInputDocument3';
                    break;
                default:
                    $sXmlForm = 'tracker/tracker_ViewAnyInputDocument';
                    break;
            }
            $oAppDocument->Fields['VIEW'] = G::LoadTranslation( 'ID_OPEN' );
            $oAppDocument->Fields['FILE'] = 'tracker_ShowDocument?a=' . $_POST['APP_DOC_UID'] . '&r=' . rand();

            //If plugin and trigger are defined for listing
            if ($oPluginRegistry->existsTrigger( PM_CASE_DOCUMENT_LIST_ARR )) {
                $oPluginRegistry = & PMPluginRegistry::getSingleton();
                $filesPluginArray = $oPluginRegistry->executeTriggers( PM_CASE_DOCUMENT_LIST_ARR, $_SESSION['APPLICATION'] );
                //Now search for the file, if exists the change the download URL
                foreach ($filesPluginArray as $file) {
                    if ($file->filename == $_POST['APP_DOC_UID']) {
                        $oAppDocument->Fields['FILE'] = $file->downloadScript;
                    }
                }
            }

            $G_PUBLISH = new Publisher();
            $G_PUBLISH->AddContent( 'xmlform', 'xmlform', $sXmlForm, '', G::array_merges( $Fields, $oAppDocument->Fields ), '' );
            G::RenderPage( 'publish', 'raw' );
            break;
        case 'showGeneratedDocumentTracker':
            require_once 'classes/model/AppDocument.php';
            require_once 'classes/model/AppDelegation.php';
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
            $aFields['FILE1'] = 'tracker_ShowOutputDocument?a=' . $aFields['APP_DOC_UID'] . '&ext=doc&random=' . rand();
            $aFields['FILE2'] = 'tracker_ShowOutputDocument?a=' . $aFields['APP_DOC_UID'] . '&ext=pdf&random=' . rand();

            //If plugin and trigger are defined for listing
            if ($oPluginRegistry->existsTrigger( PM_CASE_DOCUMENT_LIST_ARR )) {
                $oPluginRegistry = & PMPluginRegistry::getSingleton();
                $filesPluginArray = $oPluginRegistry->executeTriggers( PM_CASE_DOCUMENT_LIST_ARR, $aFields['APP_UID'] );
                //Now search for the file, if exists the change the download URL
                foreach ($filesPluginArray as $file) {
                    if ($file->filename == $_POST['APP_DOC_UID']) {
                        $aFields['FILE2'] = $file->downloadScript; // The PDF is the only one uploaded to KT
                    }
                }
            }

            $G_PUBLISH = new Publisher();
            $G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'tracker/tracker_ViewAnyOutputDocument', '', G::array_merges( $aOD, $aFields ), '' );
            G::RenderPage( 'publish', 'raw' );
            break;
        case 'load':
            $oConnection = Propel::getConnection( 'workflow' );
            $oStatement = $oConnection->prepareStatement( "CREATE TABLE IF NOT EXISTS `STAGE` (
                                                       `STG_UID` VARCHAR( 32 ) NOT NULL ,
                                                       `PRO_UID` VARCHAR( 32 ) NOT NULL ,
                                                       `STG_POSX` INT( 11 ) NOT NULL DEFAULT '0',
                                                       `STG_POSY` INT( 11 ) NOT NULL DEFAULT '0',
                                                       `STG_INDEX` INT( 11 ) NOT NULL DEFAULT '0',
                                                       PRIMARY KEY ( `STG_UID` )
                                                     );" );
            $oStatement->executeQuery();
            /**
             * ************************************************************************************************************
             */
            require_once 'classes/model/Stage.php';
            require_once 'classes/model/Process.php';
            require_once 'classes/model/Task.php';
            require_once 'classes/model/AppDelegation.php';
            //$oJSON = new Services_JSON();
            $oData = Bootstrap::json_decode( stripslashes( $_POST['data'] ) );
            $oProcess = new Process();
            $aRow = $oProcess->load( $oData->uid );
            $oSM->title->label = strip_tags( $aRow['PRO_TITLE'] );
            //$oSM->title->position->x = $aRow['PRO_TITLE_X'];
            //$oSM->title->position->y = $aRow['PRO_TITLE_Y'];
            $oSM->title->position->x = 10;
            $oSM->title->position->y = 10;
            $oSM->stages = array ();
            $oCriteria = new Criteria( 'workflow' );
            $oCriteria->addSelectColumn( StagePeer::STG_UID );
            $oCriteria->addSelectColumn( ContentPeer::CON_VALUE );
            $oCriteria->addSelectColumn( StagePeer::STG_POSX );
            $oCriteria->addSelectColumn( StagePeer::STG_POSY );
            $aConditions = array ();
            $aConditions[] = array (0 => StagePeer::STG_UID,1 => ContentPeer::CON_ID);
            $aConditions[] = array (0 => ContentPeer::CON_CATEGORY,1 => DBAdapter::getStringDelimiter() . 'STG_TITLE' . DBAdapter::getStringDelimiter());
            $aConditions[] = array (0 => ContentPeer::CON_LANG,1 => DBAdapter::getStringDelimiter() . SYS_LANG . DBAdapter::getStringDelimiter());
            $oCriteria->addJoinMC( $aConditions, Criteria::LEFT_JOIN );
            $oCriteria->add( StagePeer::PRO_UID, $oData->uid );
            $oCriteria->addAscendingOrderByColumn( StagePeer::STG_INDEX );
            $oDataset = StagePeer::doSelectRS( $oCriteria );
            $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
            $oDataset->next();
            while ($aRow1 = $oDataset->getRow()) {
                $oStage = null;
                $oStage->uid = $aRow1['STG_UID'];
                $oStage->label = strip_tags( $aRow1['CON_VALUE'] );
                $oStage->position->x = (int) $aRow1['STG_POSX'];
                $oStage->position->y = (int) $aRow1['STG_POSY'];
                $oStage->derivation = null;
                $oStage->derivation->to = array ();
                if (! $oData->mode) {
                    $oCriteria = new Criteria( 'workflow' );
                    $oCriteria->add( TaskPeer::STG_UID, $aRow1['STG_UID'] );
                    $oDataset1 = TaskPeer::doSelectRS( $oCriteria );
                    $oDataset1->setFetchmode( ResultSet::FETCHMODE_ASSOC );
                    $oDataset1->next();
                    $aTasks = array ();
                    while ($aRow2 = $oDataset1->getRow()) {
                        $aTasks[] = $aRow2['TAS_UID'];
                        $oDataset1->next();
                    }
                    $oCriteria = new Criteria( 'workflow' );
                    $oCriteria->add( AppDelegationPeer::APP_UID, $_SESSION['APPLICATION'] );
                    $oCriteria->add( AppDelegationPeer::TAS_UID, $aTasks, Criteria::IN );
                    $oCriteria->add( $oCriteria->getNewCriterion( AppDelegationPeer::DEL_FINISH_DATE, null, Criteria::ISNULL )->addOr( $oCriteria->getNewCriterion( AppDelegationPeer::DEL_FINISH_DATE, '' ) ) );
                    if (AppDelegationPeer::doCount( $oCriteria ) > 0) {
                        $oStage->color = '#FF0000';
                    } else {
                        $oCriteria = new Criteria( 'workflow' );
                        $oCriteria->add( AppDelegationPeer::APP_UID, $_SESSION['APPLICATION'] );
                        $oCriteria->add( AppDelegationPeer::TAS_UID, $aTasks, Criteria::IN );
                        $oCriteria->add( AppDelegationPeer::DEL_THREAD_STATUS, 'CLOSED' );
                        if (AppDelegationPeer::doCount( $oCriteria ) > 0) {
                            $oStage->color = '#006633';
                        } else {
                            $oCriteria = new Criteria( 'workflow' );
                            $oCriteria->add( AppDelegationPeer::APP_UID, $_SESSION['APPLICATION'] );
                            $oCriteria->add( AppDelegationPeer::TAS_UID, $aTasks, Criteria::IN );
                            if (AppDelegationPeer::doCount( $oCriteria ) == 0) {
                                $oStage->color = '#939598';
                            }
                        }

                    }
                }
                $oSM->stages[] = $oStage;
                $oDataset->next();
            }
            foreach ($oSM->stages as $iKey => $oStage) {
                if (isset( $oSM->stages[$iKey + 1] )) {
                    $oDerivation = new StdClass();
                    $oDerivation->stage = $oSM->stages[$iKey + 1]->uid;
                    $oSM->stages[$iKey]->derivation->to = array ($oDerivation);
                    $oSM->stages[$iKey]->derivation->type = 0;
                }
            }
            //$oJSON = new Services_JSON();
            echo Bootstrap::json_encode( $oSM );
            break;
        case 'addStage':
            require_once 'classes/model/Stage.php';
            //$oJSON = new Services_JSON();
            $oData = Bootstrap::json_decode( stripslashes( $_POST['data'] ) );
            $oCriteria = new Criteria( 'workflow' );
            $oCriteria->addSelectColumn( 'STG_UID' );
            $oCriteria->add( StagePeer::PRO_UID, $oData->uid );
            $oDataset = StagePeer::doSelectRS( $oCriteria );
            $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
            $oDataset->next();
            $aStages = array ();
            $iStageNumber = 0;
            while ($aRow = $oDataset->getRow()) {
                $aStages[] = $aRow['STG_UID'];
                $iStageNumber ++;
                $oDataset->next();
            }
            if ($iStageNumber == 0) {
                $iStageNumber = 1;
            }
            $iIndex = $iStageNumber + 1;
            $bContinue = false;
            while (! $bContinue) {
                $oCriteria = new Criteria( 'workflow' );
                $oCriteria->addSelectColumn( 'COUNT(*) AS TIMES' );
                $oCriteria->add( ContentPeer::CON_ID, $aStages, Criteria::IN );
                $oCriteria->add( ContentPeer::CON_CATEGORY, 'STG_TITLE' );
                $oCriteria->add( ContentPeer::CON_LANG, SYS_LANG );
                $oCriteria->add( ContentPeer::CON_VALUE, G::LoadTranslation( 'ID_STAGE' ) . ' ' . $iStageNumber );
                $oDataset = ContentPeer::doSelectRS( $oCriteria );
                $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
                $oDataset->next();
                $aRow = $oDataset->getRow();
                if ((int) $aRow['TIMES'] > 0) {
                    $iStageNumber += 1;
                } else {
                    $bContinue = true;
                }
            }
            $oStage = new Stage();
            $oNewStage->label = G::LoadTranslation( 'ID_STAGE' ) . ' ' . $iStageNumber;

            if ($oData->position->x < 0)
                $oData->position->x *= - 1;
            if ($oData->position->y < 0)
                $oData->position->y *= - 1;

            $oNewStage->uid = $oStage->create( array ('PRO_UID' => $oData->uid,'STG_TITLE' => $oNewStage->label,'STG_POSX' => $oData->position->x,'STG_POSY' => $oData->position->y,'STG_INDEX' => $iIndex) );
            //$oJSON = new Services_JSON();
            echo Bootstrap::json_encode( $oNewStage );
            break;
        case 'saveStagePosition':
            require_once 'classes/model/Stage.php';
            //$oJSON = new Services_JSON();
            $oData = Bootstrap::json_decode( stripslashes( $_POST['data'] ) );
            $oStage = new Stage();
            $aFields = $oStage->load( $oData->uid );
            $aFields['STG_UID'] = $oData->uid;
            $aFields['STG_POSX'] = $oData->position->x;
            $aFields['STG_POSY'] = $oData->position->y;
            $oStage->update( $aFields );
            break;
        case 'deleteStage':
            require_once 'classes/model/Stage.php';
            //$oJSON = new Services_JSON();
            $oData = Bootstrap::json_decode( stripslashes( $_POST['data'] ) );
            $oStage = new Stage();
            $aFields = $oStage->load( $oData->stg_uid );
            $oStage->remove( $oData->stg_uid );
            $oStage->reorderPositions( $aFields['PRO_UID'], $aFields['STG_INDEX'] );
            require_once 'classes/model/Task.php';
            $oCriteria1 = new Criteria( 'workflow' );
            $oCriteria1->add( TaskPeer::STG_UID, $oData->stg_uid );
            $oCriteria2 = new Criteria( 'workflow' );
            $oCriteria2->add( TaskPeer::STG_UID, '' );
            BasePeer::doUpdate( $oCriteria1, $oCriteria2, Propel::getConnection( 'workflow' ) );
            break;
        case 'editStage':
            require_once 'classes/model/Stage.php';
            //$oJSON = new Services_JSON();
            $oData = Bootstrap::json_decode( stripslashes( $_POST['data'] ) );
            $oStage = new Stage();
            $aFields = $oStage->load( $oData->stg_uid );
            $aFields['THEINDEX'] = $oData->theindex;
            $aFields['action'] = 'updateStage';
            global $G_PUBLISH;
            $G_PUBLISH = new Publisher();
            $G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'tracker/tracker_StageEdit', '', $aFields, '../tracker/tracker_Ajax' );
            G::RenderPage( 'publish', 'raw' );
            break;
        case 'updateStage':
            require_once 'classes/model/Stage.php';
            $oStage = new Stage();
            $aFields = $oStage->load( $_POST['form']['STG_UID'] );
            $aFields['STG_TITLE'] = $_POST['form']['STG_TITLE'];
            $oStage->update( $aFields );
            break;
        case 'tasksAssigned':
            require_once 'classes/model/Stage.php';
            require_once 'classes/model/Task.php';
            //$oJSON = new Services_JSON();
            $oData = Bootstrap::json_decode( stripslashes( $_POST['data'] ) );
            $oCriteria = new Criteria( 'workflow' );
            $oCriteria->addSelectColumn( TaskPeer::TAS_UID );
            $oCriteria->addAsColumn( 'TAS_TITLE', ContentPeer::CON_VALUE );
            $aConditions = array ();
            $aConditions[] = array (0 => TaskPeer::TAS_UID,1 => ContentPeer::CON_ID);
            $aConditions[] = array (0 => ContentPeer::CON_CATEGORY,1 => DBAdapter::getStringDelimiter() . 'TAS_TITLE' . DBAdapter::getStringDelimiter());
            $aConditions[] = array (0 => ContentPeer::CON_LANG,1 => DBAdapter::getStringDelimiter() . SYS_LANG . DBAdapter::getStringDelimiter());
            $oCriteria->addJoinMC( $aConditions, Criteria::LEFT_JOIN );
            $oCriteria->add( TaskPeer::STG_UID, $oData->stg_uid );
            $oCriteria->addAscendingOrderByColumn( 'TAS_TITLE' );
            global $G_PUBLISH;
            $G_PUBLISH = new Publisher();
            $G_PUBLISH->AddContent( 'propeltable', 'paged-table', 'tracker/tracker_StageTasks', $oCriteria, array ('PRO_UID' => $oData->pro_uid,'STG_UID' => $oData->stg_uid) );
            G::RenderPage( 'publish', 'raw' );
            break;
        case 'availableTasksForTheStage':
            require_once 'classes/model/Process.php';
            require_once 'classes/model/Task.php';
            $oCriteria = new Criteria( 'workflow' );
            $oCriteria->addSelectColumn( TaskPeer::TAS_UID );
            $oCriteria->addAsColumn( 'TAS_TITLE', ContentPeer::CON_VALUE );
            $aConditions = array ();
            $aConditions[] = array (0 => TaskPeer::TAS_UID,1 => ContentPeer::CON_ID );
            $aConditions[] = array (0 => ContentPeer::CON_CATEGORY,1 => DBAdapter::getStringDelimiter() . 'TAS_TITLE' . DBAdapter::getStringDelimiter());
            $aConditions[] = array (0 => ContentPeer::CON_LANG,1 => DBAdapter::getStringDelimiter() . SYS_LANG . DBAdapter::getStringDelimiter());
            $oCriteria->addJoinMC( $aConditions, Criteria::LEFT_JOIN );
            $oCriteria->add( TaskPeer::PRO_UID, $_POST['PRO_UID'] );
            $oCriteria->add( TaskPeer::STG_UID, '' );
            $oCriteria->addAscendingOrderByColumn( 'TAS_TITLE' );
            global $G_PUBLISH;
            $G_PUBLISH = new Publisher();
            $G_PUBLISH->AddContent( 'propeltable', 'paged-table', 'tracker/tracker_AvailableStageTasks', $oCriteria, array ('STG_UID' => $_POST['STG_UID']) );
            G::RenderPage( 'publish', 'raw' );
            break;
        case 'assignTaskToStage':
            require_once 'classes/model/Task.php';
            $oCriteria1 = new Criteria( 'workflow' );
            $oCriteria1->add( TaskPeer::TAS_UID, $_POST['TAS_UID'] );
            $oCriteria2 = new Criteria( 'workflow' );
            $oCriteria2->add( TaskPeer::STG_UID, $_POST['STG_UID'] );
            BasePeer::doUpdate( $oCriteria1, $oCriteria2, Propel::getConnection( 'workflow' ) );
            break;
        case 'removeTaskFromTheStage':
            require_once 'classes/model/Task.php';
            $oCriteria1 = new Criteria( 'workflow' );
            $oCriteria1->add( TaskPeer::TAS_UID, $_POST['TAS_UID'] );
            $oCriteria2 = new Criteria( 'workflow' );
            $oCriteria2->add( TaskPeer::STG_UID, '' );
            BasePeer::doUpdate( $oCriteria1, $oCriteria2, Propel::getConnection( 'workflow' ) );
            break;

        case "processMapLegend":
            $arrayField = array ();
            $arrayField["sLabel1"] = G::LoadTranslation( "ID_TASK_IN_PROGRESS" );
            $arrayField["sLabel2"] = G::LoadTranslation( "ID_COMPLETED_TASK" );
            $arrayField["sLabel3"] = G::LoadTranslation( "ID_PENDING_TASK" );
            $arrayField["sLabel4"] = G::LoadTranslation( "ID_PARALLEL_TASK" );
            $arrayField["tracker"] = 1;
            $G_PUBLISH = new Publisher();
            $G_PUBLISH->AddContent( "smarty", "cases/cases_Leyends", "", "", $arrayField );
            G::RenderPage( "publish", "raw" );
            break;
    }
} catch (Exception $oException) {
    die( $oException->getMessage() );
}

