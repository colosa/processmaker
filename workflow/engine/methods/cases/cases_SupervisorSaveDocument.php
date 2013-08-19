<?php
/**
 * cases_SupervisorSaveDocument.php
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
    //save info
    G::LoadClass( 'case' );
    G::LoadClass( 'tasks' );
            
    $oAppDocument = new AppDocument();
    $aFields = array ('APP_UID' => $_GET['APP_UID'],'DEL_INDEX' => 100000,'USR_UID' => $_SESSION['USER_LOGGED'],'DOC_UID' => $_GET['UID'],'APP_DOC_TYPE' => $_POST['form']['APP_DOC_TYPE'],'APP_DOC_CREATE_DATE' => date( 'Y-m-d H:i:s' ),'APP_DOC_COMMENT' => isset( $_POST['form']['APP_DOC_COMMENT'] ) ? $_POST['form']['APP_DOC_COMMENT'] : '','APP_DOC_TITLE' => '','APP_DOC_FILENAME' => isset( $_FILES['form']['name']['APP_DOC_FILENAME'] ) ? $_FILES['form']['name']['APP_DOC_FILENAME'] : ''
    );
    $oAppDocument->create( $aFields );
    $sAppDocUid = $oAppDocument->getAppDocUid();
    $info = pathinfo( $oAppDocument->getAppDocFilename() );
    $ext = (isset( $info['extension'] ) ? $info['extension'] : '');
    //save the file
    if (! empty( $_FILES['form'] )) {
        if ($_FILES['form']['error']['APP_DOC_FILENAME'] == 0) {
            $sPathName = PATH_DOCUMENT . G::getPathFromUID($_GET['APP_UID']) . PATH_SEP;
            $sFileName = $sAppDocUid . '.' . $ext;
            $sOriginalName = $_FILES['form']['name']['APP_DOC_FILENAME'];
            G::uploadFile( $_FILES['form']['tmp_name']['APP_DOC_FILENAME'], $sPathName, $sFileName );

            //Plugin Hook PM_UPLOAD_DOCUMENT for upload document
            $oPluginRegistry = & PMPluginRegistry::getSingleton();
            if ($oPluginRegistry->existsTrigger( PM_UPLOAD_DOCUMENT ) && class_exists( 'uploadDocumentData' )) {
                $oData['APP_UID'] = $_GET['APP_UID'];
                $documentData = new uploadDocumentData( $_GET['APP_UID'], $_SESSION['USER_LOGGED'], $sPathName . $sFileName, $aFields['APP_DOC_FILENAME'], $sAppDocUid );
                $oPluginRegistry->executeTriggers( PM_UPLOAD_DOCUMENT, $documentData );
                unlink( $sPathName . $sFileName );
            }
            //end plugin
            
            //update AppData with the current file uploaded
            $oCase = new Cases();
            $aAppDataFields = $oCase->loadCase( $_GET['APP_UID'] );

            $oCriteria = new Criteria('workflow');
            $oCriteria->addSelectColumn(AppDelegationPeer::TAS_UID);
            $oCriteria->add(AppDelegationPeer::APP_UID, $_GET['APP_UID'], CRITERIA::EQUAL);
            $oCriteria->addAscendingOrderByColumn(AppDelegationPeer::DEL_INDEX);
            $oDataset = AppDelegationPeer::doSelectRS($oCriteria);
            $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            
            $oDataset->next();
            $oTask = new Tasks();

            $aDynaforms = array();
            while ($aRow = $oDataset->getRow()) {
                $aSteps = $oTask->getStepsOfTask($aRow['TAS_UID']);
                if (is_array($aSteps)) {
                    foreach ($aSteps as $key => $value) {
                        $oCriteriaStep = new Criteria('workflow');
                        $oCriteriaStep->addSelectColumn(StepPeer::STEP_UID_OBJ);
                        $sStepId = (isset($value['STEP_UID'])) ? $value['STEP_UID'] : 0 ;
                        $oCriteriaStep->add(StepPeer::STEP_UID, $sStepId, CRITERIA::EQUAL);
                        $oCriteriaStep->add(StepPeer::STEP_TYPE_OBJ, 'DYNAFORM', CRITERIA::EQUAL);
                        $oDataSetStep = StepPeer::doSelectRS($oCriteriaStep);
                        $oDataSetStep->setFetchmode(ResultSet::FETCHMODE_ASSOC);
                        $oDataSetStep->next();
                        $aRows = $oDataSetStep->getRow();

                        if (is_array($aRows) && !in_array($aRows['STEP_UID_OBJ'], $aDynaforms)) {
                            $aDynaforms[] = $aRows['STEP_UID_OBJ'];
                        }
                    }
                    unset($value);    
                }
                $oDataset->next();
            }

            
            if (count($aDynaforms) > 0) {
                require_once ("classes/model/Dynaform.php");
                $dynInstance = new Dynaform();
                foreach ($aDynaforms as $key => $value) {
                    $aAllFields = $dynInstance->getDynaformFields($value);
                    if (is_array($aAllFields)) {
                        foreach ($aAllFields as $kInput => $input) {
                            if (!isset($input->input)) continue;

                            if ($input->type == 'file' && $input->input == $_GET['UID'] && !empty($aAppDataFields['APP_DATA'][$kInput])) {
                                $aAppDataFields['APP_DATA'][$kInput] = $sOriginalName;
                                $oCase->updateCase( $_GET['APP_UID'], $aAppDataFields );
                            }
                        }
                        unset($input);
                    }
                }
                unset($value);
            }
            //End Update AppData with the current file uploaded
        }
    }
    //go to the next step
    if (! isset( $_POST['form']['MORE'] )) {
        $oCase = new Cases();
        $aFields = $oCase->loadCase( $_GET['APP_UID'] );
        $aNextStep = $oCase->getNextSupervisorStep( $aFields['PRO_UID'], $_GET['position'], 'INPUT_DOCUMENT' );
        G::header( 'location: ' . 'cases_StepToReviseInputs?type=INPUT_DOCUMENT&INP_DOC_UID=' . $aNextStep['UID'] . '&position=' . $aNextStep['POSITION'] . '&APP_UID=' . $_GET['APP_UID'] . '&DEL_INDEX=' );
        die();
    } else {
        G::header( 'location: ' . $_SERVER['HTTP_REFERER'] );
        die();
    }
} catch (Exception $e) {
    /* Render Error page */
    $aMessage['MESSAGE'] = $e->getMessage();
    $G_PUBLISH = new Publisher();
    $G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'login/showMessage', '', $aMessage );
    G::RenderPage( 'publish' );
}

