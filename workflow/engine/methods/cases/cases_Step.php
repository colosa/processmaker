<?php
if (!isset($_SESSION['USER_LOGGED'])) {
      G::SendTemporalMessage( 'ID_LOGIN_AGAIN', 'warning', 'labels' );
      die( '<script type="text/javascript">
                try 
                  {
                     prnt = parent.parent;
                     top.location = top.location;
                  }
                catch (err) 
                  {
                     parent.location = parent.location;
                  }
            </script>');
}
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

if ((int) $_SESSION['INDEX'] < 1) {
    G::SendTemporalMessage( 'ID_USER_HAVENT_RIGHTS_PAGE', 'error', 'labels' );
    G::header( 'location: ' . $_SERVER['HTTP_REFERER'] );
    die();
}
global $_DBArray;
if (! isset( $_DBArray )) {
    $_DBArray = array ();
}

/* Includes */
G::LoadClass( 'case' );
G::LoadClass( 'derivation' );

/* GET , POST & $_SESSION Vars */
if (isset( $_GET['POSITION'] )) {
    $_SESSION['STEP_POSITION'] = (int) $_GET['POSITION'];
}

if (isset( $_SESSION['CASES_REFRESH'] )) {
    unset( $_SESSION['CASES_REFRESH'] );
    G::evalJScript( "if(typeof parent != 'undefined' && parent.refreshCountFolders) parent.refreshCountFolders();" );
}

/* Menues */
$G_MAIN_MENU = 'processmaker';
$G_ID_MENU_SELECTED = 'CASES';
$G_SUB_MENU = 'caseOptions';
$G_ID_SUB_MENU_SELECTED = '_';

/* Prepare page before to show */
$oTemplatePower = new TemplatePower( PATH_TPL . 'cases/cases_Step.html' );
$oTemplatePower->prepare();
$G_PUBLISH = new Publisher();
$oHeadPublisher = & headPublisher::getSingleton();
$oHeadPublisher->addScriptCode( '
  var Cse = {};
  Cse.panels = {};
  var leimnud = new maborak();
  leimnud.make();
  leimnud.Package.Load("rpc,drag,drop,panel,app,validator,fx,dom,abbr",{Instance:leimnud,Type:"module"});
  leimnud.exec(leimnud.fix.memoryLeak);
  leimnud.event.add(window,"load",function(){
    ' . (isset( $_SESSION['showCasesWindow'] ) ? 'try{' . $_SESSION['showCasesWindow'] . '}catch(e){}' : '') . '
  });
  ' );
$G_PUBLISH->AddContent( 'template', '', '', '', $oTemplatePower );

$oCase = new Cases();

$Fields = $oCase->loadCase( $_SESSION['APPLICATION'] );
$Fields['APP_DATA'] = array_merge( $Fields['APP_DATA'], G::getSystemConstants() );
$sStatus = $Fields['APP_STATUS'];

$APP_NUMBER = $Fields['APP_NUMBER'];
$APP_TITLE = $Fields['TITLE'];

$oProcess = new Process();
$oProcessFieds = $oProcess->Load( $_SESSION['PROCESS'] );

#trigger debug routines...


if (isset( $oProcessFieds['PRO_DEBUG'] ) && $oProcessFieds['PRO_DEBUG']) {
    #here we must verify if is a debugg session
    $_SESSION['TRIGGER_DEBUG']['ISSET'] = 1;
    $_SESSION['PMDEBUGGER'] = true;
} else {
    $_SESSION['TRIGGER_DEBUG']['ISSET'] = 0;
    $_SESSION['PMDEBUGGER'] = false;
}

//cleaning debug variables
if (! isset( $_GET['breakpoint'] )) {
    if (isset( $_SESSION['TRIGGER_DEBUG']['info'] )) {
        unset( $_SESSION['TRIGGER_DEBUG']['info'] );
    }

    if (! isset( $_SESSION['_NO_EXECUTE_TRIGGERS_'] )) {
        $_SESSION['TRIGGER_DEBUG']['ERRORS'] = Array ();
    }
    $_SESSION['TRIGGER_DEBUG']['DATA'] = Array ();
    $_SESSION['TRIGGER_DEBUG']['TRIGGERS_NAMES'] = Array ();
    $_SESSION['TRIGGER_DEBUG']['TRIGGERS_VALUES'] = Array ();

    $triggers = $oCase->loadTriggers( $_SESSION['TASK'], $_GET['TYPE'], $_GET['UID'], 'BEFORE' );

    $_SESSION['TRIGGER_DEBUG']['NUM_TRIGGERS'] = count( $triggers );
    $_SESSION['TRIGGER_DEBUG']['TIME'] = 'BEFORE';
    if ($_SESSION['TRIGGER_DEBUG']['NUM_TRIGGERS'] != 0) {
        $_SESSION['TRIGGER_DEBUG']['TRIGGERS_NAMES'] = $oCase->getTriggerNames( $triggers );
        $_SESSION['TRIGGER_DEBUG']['TRIGGERS_VALUES'] = $triggers;
    }

    if (! isset( $_SESSION['_NO_EXECUTE_TRIGGERS_'] )) {
        //Execute before triggers - Start
        $Fields['APP_DATA'] = $oCase->ExecuteTriggers( $_SESSION['TASK'], $_GET['TYPE'], $_GET['UID'], 'BEFORE', $Fields['APP_DATA'] );
        $Fields['DEL_INDEX'] = $_SESSION['INDEX'];
        $Fields['TAS_UID'] = $_SESSION['TASK'];
        //Execute before triggers - End
    } else {
        unset( $_SESSION['_NO_EXECUTE_TRIGGERS_'] );
        $Fields['DEL_INDEX'] = $_SESSION['INDEX'];
        $Fields['TAS_UID'] = $_SESSION['TASK'];
    }
}

if (isset( $_GET['breakpoint'] )) {
    $_POST['NextStep'] = $_SESSION['TRIGGER_DEBUG']['BREAKPAGE'];
}

/**
 * Here we throw the debug view
 */
if (isset( $_GET['breakpoint'] )) {

    $G_PUBLISH->AddContent( 'view', 'cases/showDebugFrameLoader' );
    $G_PUBLISH->AddContent( 'view', 'cases/showDebugFrameBreaker' );
    G::RenderPage( 'publish', 'blank' );
    exit();
}
#end trigger debug session.......


//$oCase->thisIsTheCurrentUser($_SESSION['APPLICATION'], $_SESSION['INDEX'], $_SESSION['USER_LOGGED'], 'REDIRECT', 'casesListExtJs');


//Save data - Start
unset($Fields['APP_STATUS']);
unset($Fields['APP_PROC_STATUS']);
unset($Fields['APP_PROC_CODE']);
unset($Fields['APP_PIN']);
$oCase->updateCase( $_SESSION['APPLICATION'], $Fields );
//Save data - End


//Obtain previous and next step - Start
try {
    $oCase = new Cases();
    $aNextStep = $oCase->getNextStep( $_SESSION['PROCESS'], $_SESSION['APPLICATION'], $_SESSION['INDEX'], $_SESSION['STEP_POSITION'] );
    $aPreviousStep = $oCase->getPreviousStep( $_SESSION['PROCESS'], $_SESSION['APPLICATION'], $_SESSION['INDEX'], $_SESSION['STEP_POSITION'] );
} catch (Exception $e) {
}
//Obtain previous and next step - End

$aRequiredFields = array(
    'APPLICATION'   => $Fields['APP_DATA']['APPLICATION'],
    'PROCESS'       => $Fields['APP_DATA']['PROCESS'],
    'TASK'          => $Fields['APP_DATA']['TASK'],
    'INDEX'         => $Fields['APP_DATA']['INDEX'],
    'TRIGGER_DEBUG' => isset($Fields['APP_DATA']['TRIGGER_DEBUG']) ? $Fields['APP_DATA']['TRIGGER_DEBUG']:array()
);

$oHeadPublisher->addScriptCode('var __dynaformSVal__ = \'' . base64_encode(serialize($aRequiredFields)) . '\'; ');
try {
    //Add content content step - Start
    $oApp = ApplicationPeer::retrieveByPK( $_SESSION['APPLICATION'] );
    $array['APP_NUMBER'] = $APP_NUMBER;
    $sTitleCase = $oApp->getAppTitle();
    $array['APP_TITLE'] = $sTitleCase;
    $array['CASE'] = G::LoadTranslation( 'ID_CASE' );
    $array['TITLE'] = G::LoadTranslation( 'ID_TITLE' );

    $noShowTitle = 0;
    if (isset( $oProcessFieds['PRO_SHOW_MESSAGE'] )) {
        $noShowTitle = $oProcessFieds['PRO_SHOW_MESSAGE'];
    }

    switch ($_GET['TYPE']) {
        case 'DYNAFORM':
            if ($noShowTitle == 0) {
                $G_PUBLISH->AddContent( 'smarty', 'cases/cases_title', '', '', $array );
            }
            if (! $aPreviousStep) {
                $Fields['APP_DATA']['__DYNAFORM_OPTIONS']['PREVIOUS_STEP_LABEL'] = '';
            } else {
                $Fields['APP_DATA']['__DYNAFORM_OPTIONS']['PREVIOUS_STEP'] = $aPreviousStep['PAGE'];
                $Fields['APP_DATA']['__DYNAFORM_OPTIONS']['PREVIOUS_STEP_LABEL'] = G::loadTranslation( "ID_PREVIOUS_STEP" );
            }
            $Fields['APP_DATA']['__DYNAFORM_OPTIONS']['NEXT_STEP'] = $aNextStep['PAGE'];
            $Fields['APP_DATA']['__DYNAFORM_OPTIONS']['NEXT_STEP_LABEL'] = G::loadTranslation( 'ID_NEXT_STEP' );
            $Fields['APP_DATA']['__DYNAFORM_OPTIONS']['PHPSESSID'] = @session_id();
            $Fields['APP_DATA']['__DYNAFORM_OPTIONS']['DYNUIDPRINT'] = $_GET['UID'];

            $oHeadPublisher = & headPublisher::getSingleton();

            if (!isset($_SESSION["PM_RUN_OUTSIDE_MAIN_APP"])) {
                $oHeadPublisher->addScriptCode( "
                                                    if (typeof parent != 'undefined') {
                                                        if (parent.setNode) {
                                                            parent.setNode('" . $_GET['UID'] . "');
                                                        }
                                                    }" );
            }

            $oStep = new Step();
            $oStep = $oStep->loadByProcessTaskPosition( $_SESSION['PROCESS'], $_SESSION['TASK'], $_GET['POSITION'] );

            /**
             * Added By erik 16-05-08
             * Description: this was added for the additional database connections
             */
            G::LoadClass( 'dbConnections' );
            $oDbConnections = new dbConnections( $_SESSION['PROCESS'] );
            $oDbConnections->loadAdditionalConnections();
            $_SESSION['CURRENT_DYN_UID'] = $_GET['UID'];

            $G_PUBLISH->AddContent( 'dynaform', 'xmlform', $_SESSION['PROCESS'] . '/' . $_GET['UID'], '', $Fields['APP_DATA'], 'cases_SaveData?UID=' . $_GET['UID'] . '&APP_UID=' . $_SESSION['APPLICATION'], '', (strtolower( $oStep->getStepMode() ) != 'edit' ? strtolower( $oStep->getStepMode() ) : '') );
            break;
        case 'INPUT_DOCUMENT':
            if ($noShowTitle == 0) {
                $G_PUBLISH->AddContent( 'smarty', 'cases/cases_title', '', '', $array );
            }
            $oInputDocument = new InputDocument();
            $Fields = $oInputDocument->load( $_GET['UID'] );
            if (! $aPreviousStep) {
                $Fields['__DYNAFORM_OPTIONS']['PREVIOUS_STEP_LABEL'] = '';
                $Fields['PREVIOUS_STEP_LABEL'] = '';
            } else {
                $Fields['__DYNAFORM_OPTIONS']['PREVIOUS_STEP'] = $aPreviousStep['PAGE'];
                $Fields['__DYNAFORM_OPTIONS']['PREVIOUS_STEP_LABEL'] = G::loadTranslation( "ID_PREVIOUS_STEP" );

                $Fields['PREVIOUS_STEP'] = $aPreviousStep['PAGE'];
                $Fields['PREVIOUS_STEP_LABEL'] = G::loadTranslation( "ID_PREVIOUS_STEP" );
            }
            $Fields['NEXT_STEP'] = $aNextStep['PAGE'];
            $Fields['NEXT_STEP_LABEL'] = G::loadTranslation( "ID_NEXT_STEP" );
            switch ($_GET['ACTION']) {
                case 'ATTACH':
                    switch ($Fields['INP_DOC_FORM_NEEDED']) {
                        case 'REAL':
                            $Fields['TYPE_LABEL'] = G::LoadTranslation( 'ID_NEW' );
                            $sXmlForm = 'cases/cases_AttachInputDocument2';
                            break;
                        case 'VIRTUAL':
                            $Fields['TYPE_LABEL'] = G::LoadTranslation( 'ID_ATTACH' );
                            $sXmlForm = 'cases/cases_AttachInputDocument1';
                            break;
                        case 'VREAL':
                            $Fields['TYPE_LABEL'] = G::LoadTranslation( 'ID_ATTACH' );
                            $sXmlForm = 'cases/cases_AttachInputDocument3';
                            break;
                    }
                    $Fields['MESSAGE1'] = G::LoadTranslation( 'ID_PLEASE_ENTER_COMMENTS' );
                    $Fields['MESSAGE2'] = G::LoadTranslation( 'ID_PLEASE_SELECT_FILE' );
                    //START: If there is a Break Step registered from Plugin Similar as a Trigger debug
                    $oPluginRegistry = & PMPluginRegistry::getSingleton();
                    if ($oPluginRegistry->existsTrigger( PM_UPLOAD_DOCUMENT_BEFORE )) {
                        //If a Plugin has registered a Break Page Evaluator
                        $oPluginRegistry->executeTriggers( PM_UPLOAD_DOCUMENT_BEFORE, array ('USR_UID' => $_SESSION['USER_LOGGED']) );
                    }
                    //END: If there is a Break Step registered from Plugin
                    $G_PUBLISH->AddContent( 'propeltable', 'cases/paged-table-inputDocuments', 'cases/cases_InputdocsList', $oCase->getInputDocumentsCriteria( $_SESSION['APPLICATION'], $_SESSION['INDEX'], $_GET['UID'] ), array_merge( array ('DOC_UID' => $_GET['UID']
                    ), $Fields ) ); //$aFields


                    //call plugin
                    //if ( $oPluginRegistry->existsTrigger ( PM_CASE_DOCUMENT_LIST ) ) {
                    //  $folderData = new folderData (null, null, $_SESSION['APPLICATION'], null, $_SESSION['USER_LOGGED'] );
                    //  $oPluginRegistry =& PMPluginRegistry::getSingleton();
                    //  $oPluginRegistry->executeTriggers ( PM_CASE_DOCUMENT_LIST , $folderData );
                    //  //end plugin
                    //}
                    //else
                    //  $G_PUBLISH->AddContent('propeltable', 'cases/paged-table-inputDocuments', 'cases/cases_InputdocsList', $oCase->getInputDocumentsCriteria($_SESSION['APPLICATION'], $_SESSION['INDEX'], $_GET['UID']), array_merge(array('DOC_UID'=>$_GET['UID']),$Fields));//$aFields


                    $oHeadPublisher = & headPublisher::getSingleton();
                    $titleDocument = "<h3>" . $Fields['INP_DOC_TITLE'] . "<br><small>" . G::LoadTranslation( 'ID_INPUT_DOCUMENT' ) . "</small></h3>";
                    if ($Fields['INP_DOC_DESCRIPTION']) {
                        $titleDocument .= " " . str_replace( "\n", "", str_replace( "'", "\'", nl2br( htmlentities( utf8_decode( $Fields['INP_DOC_DESCRIPTION'] ) ) ) ) ) . "";
                    }

                    $oHeadPublisher->addScriptCode( "documentName='{$titleDocument}';" );
                    break;
                case 'VIEW':
                    //require_once 'classes/model/AppDocument.php';
                    //require_once 'classes/model/Users.php';
                    $oAppDocument = new AppDocument();
                    $oAppDocument->Fields = $oAppDocument->load( $_GET['DOC'], $_GET['VERSION'] );
                    $Fields['POSITION'] = $_SESSION['STEP_POSITION'];
                    $oUser = new Users();
                    $aUser = $oUser->load( $oAppDocument->Fields['USR_UID'] );
                    $Fields['CREATOR'] = $aUser['USR_FIRSTNAME'] . ' ' . $aUser['USR_LASTNAME'];
                    switch ($Fields['INP_DOC_FORM_NEEDED']) {
                        case 'REAL':
                            $sXmlForm = 'cases/cases_ViewInputDocument2';
                            break;
                        case 'VIRTUAL':
                            $sXmlForm = 'cases/cases_ViewInputDocument1';
                            break;
                        case 'VREAL':
                            $sXmlForm = 'cases/cases_ViewInputDocument3';
                            break;
                    }
                    $oAppDocument->Fields['VIEW'] = G::LoadTranslation( 'ID_OPEN' );
                    $oAppDocument->Fields['FILE'] = 'cases_ShowDocument?a=' . $_GET['DOC'] . '&r=' . rand();
                    $G_PUBLISH->AddContent( 'xmlform', 'xmlform', $sXmlForm, '', G::array_merges( $Fields, $oAppDocument->Fields ), '' );
                    break;
            }
            break;
        case 'OUTPUT_DOCUMENT':
            //$G_PUBLISH->AddContent('smarty', 'cases/cases_title', '', '', $array);

            //require_once 'classes/model/OutputDocument.php';
            $oOutputDocument = new OutputDocument();
            $aOD = $oOutputDocument->load( $_GET['UID'] );
            if (! $aPreviousStep) {
                $aOD['__DYNAFORM_OPTIONS']['PREVIOUS_STEP_LABEL'] = '';
            } else {
                $aOD['__DYNAFORM_OPTIONS']['PREVIOUS_STEP'] = $aPreviousStep['PAGE'];
                $aOD['__DYNAFORM_OPTIONS']['PREVIOUS_STEP_LABEL'] = G::loadTranslation( "ID_PREVIOUS_STEP" );
            }
            $aOD['__DYNAFORM_OPTIONS']['NEXT_STEP'] = $aNextStep['PAGE'];
            $aOD['__DYNAFORM_OPTIONS']['NEXT_STEP_LABEL'] = G::loadTranslation( "ID_NEXT_STEP" );
            switch ($_GET['ACTION']) {
                case 'GENERATE':
                    //START: If there is a Break Step registered from Plugin Similar as a Trigger debug
                    $oPluginRegistry = & PMPluginRegistry::getSingleton();
                    if ($oPluginRegistry->existsTrigger( PM_UPLOAD_DOCUMENT_BEFORE )) {
                        //If a Plugin has registered a Break Page Evaluator
                        $oPluginRegistry->executeTriggers( PM_UPLOAD_DOCUMENT_BEFORE, array ('USR_UID' => $_SESSION['USER_LOGGED']) );
                    }
                    //END: If there is a Break Step registered from Plugin

                    $sFilenameOriginal = $sFilename = preg_replace( '[^A-Za-z0-9_]', '_', G::replaceDataField( $aOD['OUT_DOC_FILENAME'], $Fields['APP_DATA'] ) );
                    //require_once 'classes/model/AppFolder.php';
                    //require_once 'classes/model/AppDocument.php';

                    //Get the Custom Folder ID (create if necessary)
                    $oFolder = new AppFolder();
                    $folderId = $oFolder->createFromPath( $aOD['OUT_DOC_DESTINATION_PATH'] );

                    //Tags
                    $fileTags = $oFolder->parseTags( $aOD['OUT_DOC_TAGS'] );

                    //Get last Document Version and apply versioning if is enabled


                    $oAppDocument = new AppDocument();
                    $lastDocVersion = $oAppDocument->getLastDocVersion( $_GET['UID'], $_SESSION['APPLICATION'] );

                    //if(($aOD['OUT_DOC_VERSIONING'])||($lastDocVersion==0)){
                    //  $lastDocVersion++;
                    //}

                    $oCriteria = new Criteria( 'workflow' );
                    $oCriteria->add( AppDocumentPeer::APP_UID, $_SESSION['APPLICATION'] );
                    //$oCriteria->add(AppDocumentPeer::DEL_INDEX,    $_SESSION['INDEX']);
                    $oCriteria->add( AppDocumentPeer::DOC_UID, $_GET['UID'] );
                    $oCriteria->add( AppDocumentPeer::DOC_VERSION, $lastDocVersion );
                    $oCriteria->add( AppDocumentPeer::APP_DOC_TYPE, 'OUTPUT' );
                    $oDataset = AppDocumentPeer::doSelectRS( $oCriteria );
                    $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
                    $oDataset->next();
                    if (($aOD['OUT_DOC_VERSIONING']) && ($lastDocVersion != 0)) {
                        //Create new Version of current output
                        $lastDocVersion ++;
                        if ($aRow = $oDataset->getRow()) {
                            $aFields = array ('APP_DOC_UID' => $aRow['APP_DOC_UID'],'APP_UID' => $_SESSION['APPLICATION'],'DEL_INDEX' => $_SESSION['INDEX'],'DOC_UID' => $_GET['UID'],'DOC_VERSION' => $lastDocVersion + 1,'USR_UID' => $_SESSION['USER_LOGGED'],'APP_DOC_TYPE' => 'OUTPUT','APP_DOC_CREATE_DATE' => date( 'Y-m-d H:i:s' ),'APP_DOC_FILENAME' => $sFilename,'FOLDER_UID' => $folderId,'APP_DOC_TAGS' => $fileTags
                            );
                            $oAppDocument = new AppDocument();
                            $oAppDocument->create( $aFields );
                            $sDocUID = $aRow['APP_DOC_UID'];
                        }
                    } else {
                        //No versioning so Update a current Output or Create new if no exist
                        if ($aRow = $oDataset->getRow()) {
                            //Update
                            $aFields = array ('APP_DOC_UID' => $aRow['APP_DOC_UID'],'APP_UID' => $_SESSION['APPLICATION'],'DEL_INDEX' => $_SESSION['INDEX'],'DOC_UID' => $_GET['UID'],'DOC_VERSION' => $lastDocVersion,'USR_UID' => $_SESSION['USER_LOGGED'],'APP_DOC_TYPE' => 'OUTPUT','APP_DOC_CREATE_DATE' => date( 'Y-m-d H:i:s' ),'APP_DOC_FILENAME' => $sFilename,'FOLDER_UID' => $folderId,'APP_DOC_TAGS' => $fileTags );
                            $oAppDocument = new AppDocument();
                            $oAppDocument->update( $aFields );
                            $sDocUID = $aRow['APP_DOC_UID'];
                        } else {
                            //create
                            if ($lastDocVersion == 0) {
                                $lastDocVersion ++;
                            }
                            $aFields = array ('APP_UID' => $_SESSION['APPLICATION'],'DEL_INDEX' => $_SESSION['INDEX'],'DOC_UID' => $_GET['UID'],'DOC_VERSION' => $lastDocVersion,'USR_UID' => $_SESSION['USER_LOGGED'],'APP_DOC_TYPE' => 'OUTPUT','APP_DOC_CREATE_DATE' => date( 'Y-m-d H:i:s' ),'APP_DOC_FILENAME' => $sFilename,'FOLDER_UID' => $folderId,'APP_DOC_TAGS' => $fileTags);
                            $oAppDocument = new AppDocument();
                            $aFields['APP_DOC_UID'] = $sDocUID = $oAppDocument->create( $aFields );

                        }
                    }

                    //$sFilename = ereg_replace('[^A-Za-z0-9_]', '_', G::replaceDataField($aOD['OUT_DOC_FILENAME'], $Fields['APP_DATA']));
                    //if ( $sFilename == '' ) $sFilename='_';

                    $sFilename = $aFields['APP_DOC_UID'] . "_" . $lastDocVersion;

                    $pathOutput = PATH_DOCUMENT . G::getPathFromUID($_SESSION['APPLICATION']) . PATH_SEP . 'outdocs' . PATH_SEP;
                    G::mk_dir( $pathOutput );
                    switch ($aOD['OUT_DOC_TYPE']) {
                        case 'HTML':

                            $aProperties = array (); //maui

                            if (! isset( $aOD['OUT_DOC_MEDIA'] )) {
                                $aOD['OUT_DOC_MEDIA'] = 'Letter';
                            }
                            if (! isset( $aOD['OUT_DOC_LEFT_MARGIN'] )) {
                                $aOD['OUT_DOC_LEFT_MARGIN'] = '15';
                            }
                            if (! isset( $aOD['OUT_DOC_RIGHT_MARGIN'] )) {
                                $aOD['OUT_DOC_RIGHT_MARGIN'] = '15';
                            }
                            if (! isset( $aOD['OUT_DOC_TOP_MARGIN'] )) {
                                $aOD['OUT_DOC_TOP_MARGIN'] = '15';
                            }
                            if (! isset( $aOD['OUT_DOC_BOTTOM_MARGIN'] )) {
                                $aOD['OUT_DOC_BOTTOM_MARGIN'] = '15';
                            }

                            $aProperties['media'] = $aOD['OUT_DOC_MEDIA'];
                            $aProperties['margins'] = array ('left' => $aOD['OUT_DOC_LEFT_MARGIN'],'right' => $aOD['OUT_DOC_RIGHT_MARGIN'],'top' => $aOD['OUT_DOC_TOP_MARGIN'],'bottom' => $aOD['OUT_DOC_BOTTOM_MARGIN']);
                            if ($aOD['OUT_DOC_PDF_SECURITY_ENABLED'] == '1') {
                                $aProperties['pdfSecurity'] = array ('openPassword' => $aOD['OUT_DOC_PDF_SECURITY_OPEN_PASSWORD'],'ownerPassword' => $aOD['OUT_DOC_PDF_SECURITY_OWNER_PASSWORD'],'permissions' => $aOD['OUT_DOC_PDF_SECURITY_PERMISSIONS']);
                            }
                            if (isset($aOD['OUT_DOC_REPORT_GENERATOR'])) {
                                $aProperties['report_generator'] = $aOD['OUT_DOC_REPORT_GENERATOR'];
                            }
                            $oOutputDocument->generate( $_GET['UID'], $Fields['APP_DATA'], $pathOutput, $sFilename, $aOD['OUT_DOC_TEMPLATE'], (boolean) $aOD['OUT_DOC_LANDSCAPE'], $aOD['OUT_DOC_GENERATE'], $aProperties );
                            //$sFilename, $aOD['OUT_DOC_TEMPLATE'], (boolean)$aOD['OUT_DOC_LANDSCAPE'], $aOD['OUT_DOC_GENERATE'] );
                            break;
                        case 'JRXML':
                            //creating the xml with the application data;
                            $xmlData = "<dynaform>\n";
                            foreach ($Fields['APP_DATA'] as $key => $val) {
                                $xmlData .= "  <$key>$val</$key>\n";
                            }
                            $xmlData .= "</dynaform>\n";
                            $iSize = file_put_contents( $javaOutput . 'addressBook.xml', $xmlData );

                            G::LoadClass( 'javaBridgePM' );
                            $JBPM = new JavaBridgePM();
                            $JBPM->checkJavaExtension();

                            $util = new Java( "com.processmaker.util.pmutils" );
                            $util->setInputPath( $javaInput );
                            $util->setOutputPath( $javaOutput );

                            //$content = file_get_contents ( PATH_DYNAFORM . $aOD['PRO_UID'] . PATH_SEP . $aOD['OUT_DOC_UID'] . '.jrxml' );
                            //$iSize = file_put_contents ( $javaInput .  $aOD['OUT_DOC_UID'] . '.jrxml', $content );
                            copy( PATH_DYNAFORM . $aOD['PRO_UID'] . PATH_SEP . $aOD['OUT_DOC_UID'] . '.jrxml', $javaInput . $aOD['OUT_DOC_UID'] . '.jrxml' );

                            $outputFile = $javaOutput . $sFilename . '.pdf';
                            print $util->jrxml2pdf( $aOD['OUT_DOC_UID'] . '.jrxml', basename( $outputFile ) );

                            //$content = file_get_contents ( $outputFile );
                            //$iSize = file_put_contents ( $pathOutput .  $sFilename . '.pdf' , $content );
                            copy( $outputFile, $pathOutput . $sFilename . '.pdf' );
                            //die;
                            break;
                        case 'ACROFORM':
                            //creating the xml with the application data;
                            $xmlData = "<dynaform>\n";
                            foreach ($Fields['APP_DATA'] as $key => $val) {
                                $xmlData .= "  <$key>$val</$key>\n";
                            }
                            $xmlData .= "</dynaform>\n";
                            //$iSize = file_put_contents ( $javaOutput .  'addressBook.xml' , $xmlData );

                            G::LoadClass( 'javaBridgePM' );
                            $JBPM = new JavaBridgePM();
                            $JBPM->checkJavaExtension();

                            $util = new Java( "com.processmaker.util.pmutils" );
                            $util->setInputPath( $javaInput );
                            $util->setOutputPath( $javaOutput );

                            copy( PATH_DYNAFORM . $aOD['PRO_UID'] . PATH_SEP . $aOD['OUT_DOC_UID'] . '.pdf', $javaInput . $aOD['OUT_DOC_UID'] . '.pdf' );

                            $outputFile = $javaOutput . $sFilename . '.pdf';
                            print $util->writeVarsToAcroFields( $aOD['OUT_DOC_UID'] . '.pdf', $xmlData );

                            copy( $javaOutput . $aOD['OUT_DOC_UID'] . '.pdf', $pathOutput . $sFilename . '.pdf' );

                            break;
                        default:
                            throw (new Exception( 'invalid output document' ));
                    }

                    //Execute after triggers - Start
                    $Fields['APP_DATA'] = $oCase->ExecuteTriggers( $_SESSION['TASK'], 'OUTPUT_DOCUMENT', $_GET['UID'], 'AFTER', $Fields['APP_DATA'] );
                    $Fields['DEL_INDEX'] = $_SESSION['INDEX'];
                    $Fields['TAS_UID'] = $_SESSION['TASK'];
                    //Execute after triggers - End

                    //Save data - Start
                    unset($Fields['APP_STATUS']);
                    unset($Fields['APP_PROC_STATUS']);
                    unset($Fields['APP_PROC_CODE']);
                    unset($Fields['APP_PIN']);
                    $oCase->updateCase( $_SESSION['APPLICATION'], $Fields );
                    //Save data - End

                    //Plugin Hook PM_UPLOAD_DOCUMENT for upload document
                    $oPluginRegistry = & PMPluginRegistry::getSingleton();
                    if ($oPluginRegistry->existsTrigger( PM_UPLOAD_DOCUMENT ) && class_exists( 'uploadDocumentData' )) {
                        $triggerDetail = $oPluginRegistry->getTriggerInfo( PM_UPLOAD_DOCUMENT );

                        $sPathName = PATH_DOCUMENT . G::getPathFromUID($_SESSION['APPLICATION']) . PATH_SEP;

                        $oData['APP_UID'] = $_SESSION['APPLICATION'];
                        $oData['ATTACHMENT_FOLDER'] = true;
                        switch ($aOD['OUT_DOC_GENERATE']) {
                            case "BOTH":
                                $documentData = new uploadDocumentData( $_SESSION['APPLICATION'], $_SESSION['USER_LOGGED'], $pathOutput . $sFilename . '.pdf', $sFilenameOriginal . '.pdf', $sDocUID, $oAppDocument->getDocVersion() );

                                $documentData->sFileType = "PDF";
                                $documentData->bUseOutputFolder = true;
                                $uploadReturn = $oPluginRegistry->executeTriggers( PM_UPLOAD_DOCUMENT, $documentData );
                                if ($uploadReturn) {
                                    //Only delete if the file was saved correctly
                                    $aFields['APP_DOC_PLUGIN'] = $triggerDetail->sNamespace;
                                    //$oAppDocument = new AppDocument();
                                    //$oAppDocument->update($aFields);
                                    unlink( $pathOutput . $sFilename . '.pdf' );
                                }

                                $documentData = new uploadDocumentData( $_SESSION['APPLICATION'], $_SESSION['USER_LOGGED'], $pathOutput . $sFilename . '.doc', $sFilenameOriginal . '.doc', $sDocUID, $oAppDocument->getDocVersion() );

                                $documentData->sFileType = "DOC";
                                $documentData->bUseOutputFolder = true;
                                $uploadReturn = $oPluginRegistry->executeTriggers( PM_UPLOAD_DOCUMENT, $documentData );
                                if ($uploadReturn) {
                                    //Only delete if the file was saved correctly
                                    unlink( $pathOutput . $sFilename . '.doc' );
                                }
                                break;
                            case "PDF":
                                $documentData = new uploadDocumentData( $_SESSION['APPLICATION'], $_SESSION['USER_LOGGED'], $pathOutput . $sFilename . '.pdf', $sFilenameOriginal . '.pdf', $sDocUID, $oAppDocument->getDocVersion() );

                                $documentData->sFileType = "PDF";
                                $documentData->bUseOutputFolder = true;
                                $uploadReturn = $oPluginRegistry->executeTriggers( PM_UPLOAD_DOCUMENT, $documentData );
                                if ($uploadReturn) {
                                    //Only delete if the file was saved correctly
                                    unlink( $pathOutput . $sFilename . '.pdf' );
                                }
                                break;
                            case "DOC":
                                $documentData = new uploadDocumentData( $_SESSION['APPLICATION'], $_SESSION['USER_LOGGED'], $pathOutput . $sFilename . '.doc', $sFilenameOriginal . '.doc', $sDocUID, $oAppDocument->getDocVersion() );

                                $documentData->sFileType = "DOC";
                                $documentData->bUseOutputFolder = true;
                                $uploadReturn = $oPluginRegistry->executeTriggers( PM_UPLOAD_DOCUMENT, $documentData );
                                if ($uploadReturn) {
                                    //Only delete if the file was saved correctly
                                    unlink( $pathOutput . $sFilename . '.doc' );
                                }
                                break;
                        }

                    }

                    $outputNextStep = 'cases_Step?TYPE=OUTPUT_DOCUMENT&UID=' . $_GET['UID'] . '&POSITION=' . $_SESSION['STEP_POSITION'] . '&ACTION=VIEW&DOC=' . $sDocUID;
                    G::header( 'location: ' . $outputNextStep );
                    die();
                    break;
                case 'VIEW':
                    if ($noShowTitle == 0) {
                        $G_PUBLISH->AddContent( 'smarty', 'cases/cases_title', '', '', $array );
                    }
                    //require_once 'classes/model/AppDocument.php';
                    $oAppDocument = new AppDocument();
                    $lastVersion = $oAppDocument->getLastAppDocVersion( $_GET['DOC'], $_SESSION['APPLICATION'] );
                    $aFields = $oAppDocument->load( $_GET['DOC'], $lastVersion );
                    $listing = false;
                    $oPluginRegistry = & PMPluginRegistry::getSingleton();
                    if ($oPluginRegistry->existsTrigger( PM_CASE_DOCUMENT_LIST )) {
                        $folderData = new folderData( null, null, $_SESSION['APPLICATION'], null, $_SESSION['USER_LOGGED'] );
                        $folderData->PMType = "OUTPUT";
                        $folderData->returnList = true;
                        $listing = $oPluginRegistry->executeTriggers( PM_CASE_DOCUMENT_LIST, $folderData );
                    }

                    //require_once 'classes/model/OutputDocument.php';
                    $oOutputDocument = new OutputDocument();
                    $aGields = $oOutputDocument->load( $aFields['DOC_UID'] );

                    if (isset( $aGields['OUT_DOC_VERSIONING'] ) && $aGields['OUT_DOC_VERSIONING'] != 0) {
                        $oAppDocument = new AppDocument();
                        $lastDocVersion = $oAppDocument->getLastDocVersion( $_GET['UID'], $_SESSION['APPLICATION'] );
                    } else {
                        $lastDocVersion = '';
                    }
                    $aFields['VIEW1'] = G::LoadTranslation( 'ID_OPEN' );

                    $aFields['VIEW2'] = G::LoadTranslation( 'ID_OPEN' );

                    $aFields['FILE1'] = 'cases_ShowOutputDocument?a=' . $aFields['APP_DOC_UID'] . '&v=' . $lastDocVersion . '&ext=doc&random=' . rand() . '&PHPSESSID=' . @session_id();

                    $aFields['FILE2'] = 'cases_ShowOutputDocument?a=' . $aFields['APP_DOC_UID'] . '&v=' . $lastDocVersion . '&ext=pdf&random=' . rand() . '&PHPSESSID=' . @session_id();

                    if (is_array( $listing )) {
                        //If exist in Plugin Document List
                        foreach ($listing as $folderitem) {
                            if (($folderitem->filename == $aFields['APP_DOC_UID']) && ($folderitem->type == 'DOC')) {
                                $aFields['VIEW1'] = G::LoadTranslation( 'ID_GET_EXTERNAL_FILE' );
                                $aFields['FILE1'] = $folderitem->downloadScript;
                                continue;
                            }
                            if (($folderitem->filename == $aFields['APP_DOC_UID']) && ($folderitem->type == 'PDF')) {
                                $aFields['VIEW2'] = G::LoadTranslation( 'ID_GET_EXTERNAL_FILE' );
                                $aFields['FILE2'] = $folderitem->downloadScript;
                                continue;
                            }
                        }
                    }

                    if (($aGields['OUT_DOC_GENERATE'] == 'BOTH') || ($aGields['OUT_DOC_GENERATE'] == '')) {
                        $G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'cases/cases_ViewOutputDocument1', '', G::array_merges( $aOD, $aFields ), '' );
                    }

                    if ($aGields['OUT_DOC_GENERATE'] == 'DOC') {
                        $G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'cases/cases_ViewOutputDocument2', '', G::array_merges( $aOD, $aFields ), '' );
                    }

                    if ($aGields['OUT_DOC_GENERATE'] == 'PDF') {
                        $G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'cases/cases_ViewOutputDocument3', '', G::array_merges( $aOD, $aFields ), '' );
                    }
                    break;
            }
            break;
        case 'ASSIGN_TASK':
            $oDerivation = new Derivation();
            $oProcess = new Process();
            $aData = $oCase->loadCase( $_SESSION['APPLICATION'] );

            $aFields['PROCESS'] = $oProcess->load( $_SESSION['PROCESS'] );
            $aFields['PREVIOUS_PAGE'] = $aPreviousStep['PAGE'];
            $aFields['PREVIOUS_PAGE_LABEL'] = G::LoadTranslation( 'ID_PREVIOUS_STEP' );
            $aFields['ASSIGN_TASK'] = G::LoadTranslation( 'ID_ASSIGN_TASK' );
            $aFields['END_OF_PROCESS'] = G::LoadTranslation( 'ID_END_OF_PROCESS' );
            $aFields['NEXT_TASK_LABEL'] = G::LoadTranslation( 'ID_NEXT_TASK' );
            $aFields['EMPLOYEE'] = G::LoadTranslation( 'ID_EMPLOYEE' );
            $aFields['LAST_EMPLOYEE'] = G::LoadTranslation( 'ID_LAST_EMPLOYEE' );
            $aFields['OPTION_LABEL'] = G::LoadTranslation( 'ID_OPTION' );
            $aFields['CONTINUE'] = G::LoadTranslation( 'ID_CONTINUE' );
            $aFields['FINISH'] = G::LoadTranslation( 'ID_FINISH' );
            $aFields['CONTINUE_WITH_OPTION'] = G::LoadTranslation( 'ID_CONTINUE_WITH_OPTION' );
            $aFields['FINISH_WITH_OPTION'] = G::LoadTranslation( 'ID_FINISH_WITH_OPTION' );
            $aFields['TAS_TIMING_TITLE'] = G::LoadTranslation( 'ID_TIMING_CONTROL' );
            $aFields['TAS_DURATION'] = G::LoadTranslation( 'ID_TASK_DURATION' );
            $aFields['TAS_TIMEUNIT'] = G::LoadTranslation( 'ID_TIME_UNIT' );
            $aFields['TAS_TYPE_DAY'] = G::LoadTranslation( 'ID_COUNT_DAYS' );
            $aFields['TAS_CALENDAR'] = G::LoadTranslation( 'ID_CALENDAR' );

            $aFields['TASK'] = $oDerivation->prepareInformation( array ('USER_UID' => $_SESSION['USER_LOGGED'],'APP_UID' => $_SESSION['APPLICATION'],'DEL_INDEX' => $_SESSION['INDEX']
            ) );

            if (empty( $aFields['TASK'] )) {
                throw (new Exception( G::LoadTranslation( 'ID_NO_DERIVATION_RULE' ) ));
            }

            //take the first derivation rule as the task derivation rule type.
            $aFields['PROCESS']['ROU_TYPE'] = $aFields['TASK'][1]['ROU_TYPE'];
            $aFields['PROCESS']['ROU_FINISH_FLAG'] = false;

            foreach ($aFields['TASK'] as $sKey => &$aValues) {
                $sPriority = ''; //set priority value
                if ($aFields['TASK'][$sKey]['NEXT_TASK']['TAS_PRIORITY_VARIABLE'] != '') {
                    //TO DO: review this type of assignment
                    if (isset( $aData['APP_DATA'][str_replace( '@@', '', $aFields['TASK'][$sKey]['NEXT_TASK']['TAS_PRIORITY_VARIABLE'] )] )) {
                        $sPriority = $aData['APP_DATA'][str_replace( '@@', '', $aFields['TASK'][$sKey]['NEXT_TASK']['TAS_PRIORITY_VARIABLE'] )];
                    }
                } //set priority value


                $sTask = $aFields['TASK'][$sKey]['NEXT_TASK']['TAS_UID'];

                //TAS_UID has a hidden field to store the TAS_UID
                $hiddenName = "form[TASKS][" . $sKey . "][TAS_UID]";
                $hiddenField = '<input type="hidden" name="' . $hiddenName . '" id="' . $hiddenName . '" value="' . $aValues['NEXT_TASK']['TAS_UID'] . '">';
                $aFields['TASK'][$sKey]['NEXT_TASK']['TAS_HIDDEN_FIELD'] = $hiddenField;
                //print "<hr>".$aValues['NEXT_TASK']['TAS_ASSIGN_TYPE']."<hr>";
                switch ($aValues['NEXT_TASK']['TAS_ASSIGN_TYPE']) {
                    case 'EVALUATE':
                    case 'REPORT_TO':
                    case 'BALANCED':
                    case 'SELF_SERVICE':
                        $hiddenName = "form[TASKS][" . $sKey . "][USR_UID]";
                        $aFields['TASK'][$sKey]['NEXT_TASK']['USR_UID'] = $aFields['TASK'][$sKey]['NEXT_TASK']['USER_ASSIGNED']['USR_FULLNAME'];
                        $aFields['TASK'][$sKey]['NEXT_TASK']['USR_HIDDEN_FIELD'] = '<input type="hidden" name="' . $hiddenName . '" id="' . $hiddenName . '" value="' . $aValues['NEXT_TASK']['USER_ASSIGNED']['USR_UID'] . '">';
                        //var_dump($aFields);
                        //there is a error with reportsTo, when the USR_UID is empty means there are no manager for this user, so we are disabling buttons
                        //but this validation is not for SELF_SERVICE
                        if ($aValues['NEXT_TASK']['TAS_ASSIGN_TYPE'] != 'SELF_SERVICE') {
                            if ($aFields['TASK'][$sKey]['NEXT_TASK']['USER_ASSIGNED']['USR_UID'] == '') {
                                $aFields['PROCESS']['ERROR'] = $aFields['TASK'][$sKey]['NEXT_TASK']['USER_ASSIGNED']['USR_FULLNAME'];
                            }
                        }
                        break;
                    case 'MANUAL':
                        $Aux = array ();
                        foreach ($aValues['NEXT_TASK']['USER_ASSIGNED'] as $aUser) {
                            $Aux[$aUser['USR_UID']] = $aUser['USR_FULLNAME'];
                        }
                        asort( $Aux );
                        $sAux = '<select name="form[TASKS][' . $sKey . '][USR_UID]" id="form[TASKS][' . $sKey . '][USR_UID]">';
                        $sAux .= '<option value="" enabled>' . G::LoadTranslation( 'ID_SELECT' ) . '</option>';
                        foreach ($Aux as $key => $value) {
                            $sAux .= '<option value="' . $key . '">' . $value . '</option>';
                        }
                        $sAux .= '</select>';

                        $aFields['TASK'][$sKey]['NEXT_TASK']['USR_UID'] = $sAux;
                        break;
                    case 'CANCEL_MI':
                    case 'STATIC_MI':
                        //count the Users in the group
                        $cntInstanceUsers = count( $aValues['NEXT_TASK']['USER_ASSIGNED'] );

                        //set TAS_MI_INSTANCE_VARIABLE value
                        $sMIinstanceVar = '';
                        if ($aFields['TASK'][$sKey]['NEXT_TASK']['TAS_MI_INSTANCE_VARIABLE'] != '') {
                            if (isset( $aData['APP_DATA'][str_replace( '@@', '', $aFields['TASK'][$sKey]['NEXT_TASK']['TAS_MI_INSTANCE_VARIABLE'] )] )) {
                                $sMIinstanceVar = $aData['APP_DATA'][str_replace( '@@', '', $aFields['TASK'][$sKey]['NEXT_TASK']['TAS_MI_INSTANCE_VARIABLE'] )];
                                if ($sMIinstanceVar > $cntInstanceUsers) {
                                    throw (new Exception( "Total Multiple Instance Task cannot be greater than number of users in the group." ));
                                } elseif ($sMIinstanceVar == 0) {
                                    throw (new Exception( "Total Multiple Instance Task cannot be zero." ));
                                }
                            } elseif (is_int( (int) $aFields['TASK'][$sKey]['NEXT_TASK']['TAS_MI_INSTANCE_VARIABLE'] )) {
                                $sMIinstanceVar = $aFields['TASK'][$sKey]['NEXT_TASK']['TAS_MI_INSTANCE_VARIABLE'];
                                if ($sMIinstanceVar > $cntInstanceUsers) {
                                    throw (new Exception( "Total Multiple Instance Task cannot be greater than number of users in the group." ));
                                }
                            } else {
                                throw (new Exception( "Total Multiple Instance Task variable doesn't have valid value." ));
                            }
                        } else {
                            throw (new Exception( "Total Multiple Instance Task variable doesn't have valid value." ));
                            ////set TAS_MI_INSTANCE_VARIABLE value
                        }


                        //set TAS_MI_COMPLETE_VARIABLE value
                        $sMIcompleteVar = '';
                        if ($aFields['TASK'][$sKey]['NEXT_TASK']['TAS_MI_COMPLETE_VARIABLE'] != '') {
                            if (isset( $aData['APP_DATA'][str_replace( '@@', '', $aFields['TASK'][$sKey]['NEXT_TASK']['TAS_MI_COMPLETE_VARIABLE'] )] )) {
                                $sMIcompleteVar = $aData['APP_DATA'][str_replace( '@@', '', $aFields['TASK'][$sKey]['NEXT_TASK']['TAS_MI_COMPLETE_VARIABLE'] )];
                                //
                                if ($sMIcompleteVar > $sMIinstanceVar) {
                                    throw (new Exception( "Total Multiple Instance Task to complete cannot be greater than Total number of Instances." ));
                                }
                            } elseif (is_int( (int) $aFields['TASK'][$sKey]['NEXT_TASK']['TAS_MI_COMPLETE_VARIABLE'] )) {
                                $sMIcompleteVar = $aFields['TASK'][$sKey]['NEXT_TASK']['TAS_MI_COMPLETE_VARIABLE'];
                                if ($sMIcompleteVar > $sMIinstanceVar) {
                                    throw (new Exception( "Total Multiple Instance Task to complete cannot be greater than Total number of Instances." ));
                                }
                            } else {
                                throw (new Exception( "Total Multiple Instance Task to complete variable doesn't have valid value." ));
                            }

                        } else {
                            throw (new Exception( "Total Multiple Instance Task to complete variable doesn't have valid value." ));
                        }
                            //set TAS_MI_COMPLETE_VARIABLE value
                        $aFields['TASK'][$sKey]['NEXT_TASK']['TAS_NEXT'] = $aValues['NEXT_TASK']['TAS_ASSIGN_TYPE'];

                        //If the Users in the group is equal to the MI Instance variable then Show all the users without Dropdown
                        if ($sMIinstanceVar == $cntInstanceUsers) {
                            foreach ($aValues['NEXT_TASK']['USER_ASSIGNED'] as $key => $aUser) {
                                $hiddenName = "form[TASKS][" . $sKey . "][NEXT_TASK][USER_ASSIGNED][" . $key . "][USR_UID]";
                                $aFields['TASK'][$sKey]['NEXT_TASK']['USER_ASSIGNED'][$key]['USR_UID'] = $aUser['USR_FULLNAME'];
                                $aFields['TASK'][$sKey]['NEXT_TASK']['USER_ASSIGNED'][$key]['USR_HIDDEN_FIELD'] = '<input type="hidden" name="' . $hiddenName . '" id="' . $hiddenName . '" value="' . $aUser['USR_UID'] . '">';
                            }
                        }                         //If the Users in the group is not equal to the MI Instance variable then Show Only count users in dropdown
                        else {
                            $Aux = array ();
                            foreach ($aValues['NEXT_TASK']['USER_ASSIGNED'] as $aUser) {
                                $Aux[$aUser['USR_UID']] = $aUser['USR_FULLNAME'];
                            }
                            asort( $Aux );
                            $aAux = '<option value="" enabled>' . G::LoadTranslation( 'ID_SELECT' ) . '</option>';
                            foreach ($Aux as $akey => $value) {
                                $aAux .= '<option value="' . $akey . '">' . $value . '</option>';
                            }

                            for ($key = 0; $key < $sMIinstanceVar; $key ++) {
                                $hiddenName = "form[TASKS][" . $sKey . "][NEXT_TASK][USER_ASSIGNED][" . $key . "][USR_UID]";
                                $sAux = "<select name=$hiddenName id=$hiddenName";
                                $sAux .= $aAux;
                                $sAux .= '</select>';
                                $aFields['TASK'][$sKey]['NEXT_TASK']['USER_ASSIGNED'][$key]['USR_HIDDEN_FIELD'] = "<input type='hidden' name='hidden' id='hidden' value=''>";
                                $aFields['TASK'][$sKey]['NEXT_TASK']['USER_ASSIGNED'][$key]['USR_UID'] = $sAux;
                            }
                        }
                        break;
                    case '': //when this task is the Finish process
                        $userFields = $oDerivation->getUsersFullNameFromArray( $aFields['TASK'][$sKey]['USER_UID'] );
                        $aFields['TASK'][$sKey]['NEXT_TASK']['USR_UID'] = $userFields['USR_FULLNAME'];
                        $aFields['TASK'][$sKey]['NEXT_TASK']['ROU_FINISH_FLAG'] = true;
                        $aFields['PROCESS']['ROU_FINISH_FLAG'] = true;
                        break;
                }
                $hiddenName = 'form[TASKS][' . $sKey . ']';

                /* Allow user defined Timing Control
                * Values in the dropdown will be populated from the Table TASK.
                */
                if ($aValues['NEXT_TASK']['TAS_ASSIGN_TYPE'] != '') {
                    //Check for End of Process
                    $aFields['TASK'][$sKey]['NEXT_TASK']['TAS_TRANSFER_FLY'] = strtolower( $aValues['NEXT_TASK']['TAS_TRANSFER_FLY'] );
                    $aFields['TASK'][$sKey]['NEXT_TASK']['TAS_TRANSFER_HIDDEN_FLY'] = "<input type=hidden name='" . $hiddenName . "[NEXT_TASK][TAS_TRANSFER_HIDDEN_FLY]' id='" . $hiddenName . "[NEXT_TASK][TAS_TRANSFER_HIDDEN_FLY]' value=" . $aValues['NEXT_TASK']['TAS_TRANSFER_FLY'] . ">";
                    if ($aValues['NEXT_TASK']['TAS_TRANSFER_FLY'] == 'true') {
                        $aFields['TASK'][$sKey]['NEXT_TASK']['TAS_DURATION'] = '<input type="text" size="5" name="' . $hiddenName . '[NEXT_TASK][TAS_DURATION]" id="' . $hiddenName . '[NEXT_TASK][TAS_DURATION]" value="' . $aValues['NEXT_TASK']['TAS_DURATION'] . '">';
                        $hoursSelected = $daysSelected = '';
                        if ($aFields['TASK'][$sKey]['NEXT_TASK']['TAS_TIMEUNIT'] == 'HOURS') {
                            $hoursSelected = "selected = 'selected'";
                        } else {
                            $daysSelected = "selected = 'selected'";
                        }

                        $sAux = '<select name=' . $hiddenName . '[NEXT_TASK][TAS_TIMEUNIT] id= ' . $hiddenName . '[NEXT_TASK][TAS_TIMEUNIT] ';
                        $sAux .= "<option " . $hoursSelected . " value='HOURS'>Hours</option> ";
                        $sAux .= "<option " . $daysSelected . " value='DAYS'>Days</option> ";
                        $sAux .= '</select>';
                        $aFields['TASK'][$sKey]['NEXT_TASK']['TAS_TIMEUNIT'] = $sAux;

                        $workSelected = $calendarSelected = '';
                        if ($aFields['TASK'][$sKey]['NEXT_TASK']['TAS_TYPE_DAY'] == '1') {
                            $workSelected = "selected = 'selected'";
                        } else {
                            $calendarSelected = "selected = 'selected'";
                        }

                        $sAux = '<select name=' . $hiddenName . '[NEXT_TASK][TAS_TYPE_DAY] id= ' . $hiddenName . '[NEXT_TASK][TAS_TYPE_DAY] ';
                        $sAux .= "<option " . $workSelected . " value='1'>Work Days</option> ";
                        $sAux .= "<option " . $calendarSelected . " value='2'>Calendar Days</option> ";
                        $sAux .= '</select>';
                        $aFields['TASK'][$sKey]['NEXT_TASK']['TAS_TYPE_DAY'] = $sAux;

                        //Check for
                        G::LoadClass( 'calendar' );
                        $calendar = new Calendar();
                        $calendarObj = $calendar->getCalendarList( true, true );
                        $availableCalendar = $calendarObj['array'];
                        $aCalendar['CALENDAR_UID'] = '00000000000000000000000000000001';
                        $aCalendar['CALENDAR_NAME'] = 'DEFAULT';
                        $sAux = '<select name=' . $hiddenName . '[NEXT_TASK][TAS_CALENDAR] id= ' . $hiddenName . '[NEXT_TASK][TAS_CALENDAR] ';
                        $sAux .= "<option value='none'>-None-</option> ";
                        foreach ($availableCalendar as $aCalendar) {
                            if (is_array( $aCalendar )) {
                                $sAux .= "<option value='" . $aCalendar['CALENDAR_UID'] . "'>" . $aCalendar['CALENDAR_NAME'] . "</option> ";
                            }
                        }
                        $sAux .= '</select>';
                        $aFields['TASK'][$sKey]['NEXT_TASK']['TAS_CALENDAR'] = $sAux;

                    }

                    $aFields['TASK'][$sKey]['NEXT_TASK']['TAS_ASSIGN_TYPE'] = '<input type="hidden" name="' . $hiddenName . '[TAS_ASSIGN_TYPE]"   id="' . $hiddenName . '[TAS_ASSIGN_TYPE]"   value="' . $aValues['NEXT_TASK']['TAS_ASSIGN_TYPE'] . '">';
                    if (isset( $aValues['NEXT_TASK']['TAS_DEF_PROC_CODE'] )) {
                        $aFields['TASK'][$sKey]['NEXT_TASK']['TAS_DEF_PROC_CODE'] = '<input type="hidden" name="' . $hiddenName . '[TAS_DEF_PROC_CODE]" id="' . $hiddenName . '[TAS_DEF_PROC_CODE]" value="' . $aValues['NEXT_TASK']['TAS_DEF_PROC_CODE'] . '">';
                    } else {
                        $aFields['TASK'][$sKey]['NEXT_TASK']['TAS_DEF_PROC_CODE'] = '<input type="hidden" name="' . $hiddenName . '[TAS_DEF_PROC_CODE]" id="' . $hiddenName . '[TAS_DEF_PROC_CODE]" value="">';
                    }
                    $aFields['TASK'][$sKey]['NEXT_TASK']['DEL_PRIORITY'] = '<input type="hidden" name="' . $hiddenName . '[DEL_PRIORITY]"      id="' . $hiddenName . '[DEL_PRIORITY]"      value="' . $sPriority . '">';
                    $aFields['TASK'][$sKey]['NEXT_TASK']['TAS_PARENT'] = '<input type="hidden" name="' . $hiddenName . '[TAS_PARENT]"        id="' . $hiddenName . '[TAS_PARENT]"        value="' . $aValues['NEXT_TASK']['TAS_PARENT'] . '">';
                }
            }
            $aFields['PROCESSING_MESSAGE'] = G::loadTranslation( 'ID_PROCESSING' );

            /**
             * New Feature: Derivation Screen can be personalized
             *
             * @author Erik Amaru Ortiz <erik@colosa.com>
             */
            $tplFile = 'cases/cases_ScreenDerivation';
            $task = TaskPeer::retrieveByPk( $_SESSION['TASK'] );

            $tasDerivationScreenTpl = $task->getTasDerivationScreenTpl();

            if (! empty( $tasDerivationScreenTpl )) {
                //erik: first, verify if the task has a personalized template (for derivation screen)
                $tplFile = $tasDerivationScreenTpl;
                $tplFile = PATH_DATA_MAILTEMPLATES . $aFields['PROCESS']['PRO_UID'] . PATH_SEP . $tplFile;
            } else {
                //erik: verify if the process has a personalized template (for derivation screen)
                if (! empty( $aFields['PROCESS']['PRO_DERIVATION_SCREEN_TPL'] )) {
                    $tplFile = $aFields['PROCESS']['PRO_DERIVATION_SCREEN_TPL'];
                    $tplFile = PATH_DATA_MAILTEMPLATES . $aFields['PROCESS']['PRO_UID'] . PATH_SEP . $tplFile;
                }
            }

            $title = htmlentities($aFields['TASK'][$sKey]['NEXT_TASK']['TAS_TITLE'], ENT_QUOTES, 'UTF-8');
            $aFields['TASK'][$sKey]['NEXT_TASK']['TAS_TITLE'] = $title;

            $G_PUBLISH->AddContent( 'smarty', $tplFile, '', '', $aFields );
            /*
            if (isset( $aFields['TASK'][1]['NEXT_TASK']['USER_ASSIGNED'])){
                if($aFields['TASK'][1]['NEXT_TASK']['USER_ASSIGNED']!="ERROR" && is_array($aFields['TASK'][1]['NEXT_TASK']['USER_ASSIGNED'])){
                $G_PUBLISH->AddContent('smarty', 'cases/cases_ScreenDerivation', '', '', $aFields);
            } else {
                $sMessageError = "The current user does not have a valid Reports To user.  Please contact administrator.";
                //$aFields['TASK'][$sKey]['NEXT_TASK']['USR_HIDDEN_FIELD'] = '<input type="hidden" name="' . $hiddenName . '" id="' . $hiddenName . '" value="' . $sMessageError . '">';
                G::SendTemporalMessage ('UID_UNDEFINED_USER', "Error");
                $aFields['ERROR_REPORTSTO']= "Error";
                $aFields['MESSAGE_ERROR_REPORTSTO']=G::loadTranslation("ID_MSJ_REPORSTO");;
                $G_PUBLISH->AddContent('smarty', 'cases/cases_ShowE_Reportsto', '', '', $aFields);
            }
            } else {
                $G_PUBLISH->AddContent('smarty', 'cases/cases_ScreenDerivation', '', '', $aFields);
            }
            */
            break;
        case 'EXTERNAL':
            if ($noShowTitle == 0) {
                $G_PUBLISH->AddContent( 'smarty', 'cases/cases_title', '', '', $array );
            }
            $oPluginRegistry = &PMPluginRegistry::getSingleton();
            $externalSteps = $oPluginRegistry->getSteps();

            $sNamespace = '';
            $sStepName = '';
            foreach ($externalSteps as $key => $val) {
                if ($val->sStepId == $_GET['UID']) {
                    $sNamespace = $val->sNamespace;
                    $sStepName = $val->sStepName;

                }
            }
            if (class_exists( $sNamespace . "plugin" )) {
                if (! $aPreviousStep) {
                    $Fields['APP_DATA']['__DYNAFORM_OPTIONS']['PREVIOUS_STEP_LABEL'] = '';
                } else {
                    $Fields['APP_DATA']['__DYNAFORM_OPTIONS']['PREVIOUS_STEP'] = $aPreviousStep['PAGE'];
                    $Fields['APP_DATA']['__DYNAFORM_OPTIONS']['PREVIOUS_STEP_LABEL'] = G::loadTranslation( "ID_PREVIOUS_STEP" );
                }
                $Fields['APP_DATA']['__DYNAFORM_OPTIONS']['NEXT_STEP'] = $aNextStep['PAGE'];
                $Fields['APP_DATA']['__DYNAFORM_OPTIONS']['NEXT_STEP_LABEL'] = G::loadTranslation( "ID_NEXT_STEP" );

                /**
                 * Added By erik date: 16-05-08
                 * Description: this was added for the additional database connections
                 */
                G::LoadClass( 'dbConnections' );
                $oDbConnections = new dbConnections( $_SESSION['PROCESS'] );
                $oDbConnections->loadAdditionalConnections();
                $stepFilename = "$sNamespace/$sStepName";
                G::evalJScript( "
        if (parent.setCurrent) {
          parent.setCurrent('" . $_GET['UID'] . "');
        }" );

                $G_PUBLISH->AddContent( 'content', $stepFilename );
            } else {
                $aMessage['MESSAGE'] = G::loadTranslation( 'ID_EXTERNAL_STEP_MISSING', SYS_LANG, array ("plugin" => $sNamespace
                ) );
                $G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'login/showMessage', '', $aMessage );
            }
            break;
    }
    //Add content content step - End
} catch (Exception $e) {
    G::SendTemporalMessage( G::LoadTranslation( 'ID_PROCESS_DEF_PROBLEM' ), 'error', 'string', 3, 100 );
    $aMessage = array ();
    $aMessage['MESSAGE'] = $e->getMessage();
    $G_PUBLISH = new Publisher();
    $G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'login/showMessage', '', $aMessage );
    G::RenderPage( 'publish', 'blank' );
    die();
}

$oHeadPublisher = & headPublisher::getSingleton();
$oHeadPublisher->addScriptFile( "/jscore/cases/core/cases_Step.js" );

if (!isset($_SESSION["PM_RUN_OUTSIDE_MAIN_APP"])) {
    $oHeadPublisher->addScriptCode( "
                                        if (typeof parent != 'undefined') {
                                            if (parent.showCaseNavigatorPanel) {
                                                parent.showCaseNavigatorPanel('$sStatus');
                                            }

                                            if (parent.setCurrent) {
                                                parent.setCurrent('" . $_GET['UID'] . "');
                                            }
                                        }" );

}

G::RenderPage( 'publish', 'blank' );

if ($_SESSION['TRIGGER_DEBUG']['ISSET']) {
    G::evalJScript( '
    if (typeof showdebug != \'undefined\') {
      showdebug();
    }' );
}

