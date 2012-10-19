<?php
/**
 * AppFolder.php
 *
 * @package workflow.engine.classes.model
 */

require_once 'classes/model/om/BaseAppFolder.php';
require_once 'classes/model/Application.php';

/**
 * Skeleton subclass for representing a row from the 'APP_FOLDER' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements. This class will only be generated as
 * long as it does not already exist in the output directory.
 */
/**
 *
 * @author hugo loza
 * @package workflow.engine.classes.model
 */
class AppFolder extends BaseAppFolder
{

    /**
     *
     * @param string $folderName
     * @param strin(32) $folderParent
     * @return Ambigous <>|number
     */
    function createFolder ($folderName, $folderParent = "/", $action = "createifnotexists")
    {
        $validActions = array ("createifnotexists","create","update"
        );
        if (! in_array( $action, $validActions )) {
            $action = "createifnotexists";
        }

        //Clean Folder and Parent names (delete spaces...)
        $folderName = trim( $folderName );
        $folderParent = trim( $folderParent );
        //Try to Load the folder (Foldername+FolderParent)
        $oCriteria = new Criteria( 'workflow' );
        $oCriteria->add( AppFolderPeer::FOLDER_NAME, $folderName );
        $oCriteria->add( AppFolderPeer::FOLDER_PARENT_UID, $folderParent );
        $oDataset = AppFolderPeer::doSelectRS( $oCriteria );
        $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
        $oDataset->next();
        if ($aRow = $oDataset->getRow()) {
            //Folder exist, then return the ID
            $response['success'] = false;
            $response['message'] = $response['error'] = "Can't create folder <br /> A folder with same name already exist. <br /> $folderName";
            $response['folderUID'] = $aRow['FOLDER_UID'];
            //return ($aRow ['FOLDER_UID']);
            return ($response);
        } else {
            //Folder doesn't exist. Create and return the ID
            $folderUID = G::GenerateUniqueID();
            $tr = new AppFolder();
            $tr->setFolderUid( $folderUID );
            $tr->setFolderParentUid( $folderParent );
            $tr->setFolderName( $folderName );
            $tr->setFolderCreateDate( 'now' );
            $tr->setFolderUpdateDate( 'now' );
            if ($tr->validate()) {
                // we save it, since we get no validation errors, or do whatever else you like.
                $res = $tr->save();
                $response['success'] = true;
                $response['message'] = "Folder successfully created. <br /> $folderName";
                $response['folderUID'] = $folderUID;
                return ($response);
                //return $folderUID;
            } else {
                // Something went wrong. We can now get the validationFailures and handle them.
                $msg = '';
                $validationFailuresArray = $tr->getValidationFailures();
                foreach ($validationFailuresArray as $objValidationFailure) {
                    $msg .= $objValidationFailure->getMessage() . "<br/>";
                }
                $response['success'] = false;
                $response['message'] = $response['error'] = "Can't create folder \n A  \n " . $msg;
                return ($response);
            }
        }
    }

    /**
     *
     * @param string $folderPath
     * @param strin(32) $sessionID
     * @return string Last Folder ID generated
     */
    function createFromPath ($folderPath, $sessionID = "")
    {
        if ($sessionID == "") {
            $sessionID = $_SESSION['APPLICATION'];
            //Get current Application Fields
        }

        $oApplication = new Application();
        $appFields = $oApplication->Load( $sessionID );
        $folderPathParsed = G::replaceDataField( $folderPath, $appFields );
        $folderPathParsed = G::replaceDataField( $folderPath, unserialize( $appFields['APP_DATA'] ) );
        $folderPathParsedArray = explode( "/", $folderPathParsed );
        $folderRoot = "/"; //Always starting from Root
        foreach ($folderPathParsedArray as $folderName) {
            if (trim( $folderName ) != "") {
                $response = $this->createFolder( $folderName, $folderRoot );
                $folderRoot = $response['folderUID'];
            }
        }
        return $folderRoot != "/" ? $folderRoot : "";
    }

    /**
     *
     * @param string $fileTags
     * @param string(32) $sessionID Application ID
     * @return string
     */
    function parseTags ($fileTags, $sessionID = "")
    {

        if ($sessionID == "") {
            $sessionID = $_SESSION['APPLICATION'];
            //Get current Application Fields
        }

        $oApplication = new Application();
        $appFields = $oApplication->Load( $sessionID );
        $fileTagsParsed = G::replaceDataField( $fileTags, $appFields );
        $fileTagsParsed = G::replaceDataField( $fileTags, unserialize( $appFields['APP_DATA'] ) );
        return $fileTagsParsed;
    }

    /**
     *
     * @param string(32) $folderID
     * @return multitype:
     */
    function getFolderList ($folderID, $limit = 0, $start = 0)
    {
        $Criteria = new Criteria( 'workflow' );
        $Criteria->clearSelectColumns()->clearOrderByColumns();
        $Criteria->addSelectColumn( AppFolderPeer::FOLDER_UID );
        $Criteria->addSelectColumn( AppFolderPeer::FOLDER_PARENT_UID );
        $Criteria->addSelectColumn( AppFolderPeer::FOLDER_NAME );
        $Criteria->addSelectColumn( AppFolderPeer::FOLDER_CREATE_DATE );
        $Criteria->addSelectColumn( AppFolderPeer::FOLDER_UPDATE_DATE );
        $Criteria->add( appFolderPeer::FOLDER_PARENT_UID, $folderID, CRITERIA::EQUAL );
        $Criteria->addAscendingOrderByColumn( AppFolderPeer::FOLDER_NAME );

        $response['totalFoldersCount'] = AppFolderPeer::doCount( $Criteria );
        $response['folders'] = array ();

        if ($limit != 0) {
            $Criteria->setLimit( $limit );
            $Criteria->setOffset( $start );
        }

        $rs = appFolderPeer::doSelectRS( $Criteria );
        $rs->setFetchmode( ResultSet::FETCHMODE_ASSOC );
        $rs->next();
        $folderResult = array ();
        while (is_array( $row = $rs->getRow() )) {
            $response['folders'][] = $row;
            $rs->next();
        }
        return ($response);
    }

    /**
     *
     * @param string(32) $folderUid
     * @return array <multitype:, mixed>
     */
    function load ($folderUid)
    {
        $tr = AppFolderPeer::retrieveByPK( $folderUid );
        if ((is_object( $tr ) && get_class( $tr ) == 'AppFolder')) {
            $fields['FOLDER_UID'] = $tr->getFolderUid();
            $fields['FOLDER_PARENT_UID'] = $tr->getFolderParentUid();
            $fields['FOLDER_NAME'] = $tr->getFolderName();
            $fields['FOLDER_CREATE_DATE'] = $tr->getFolderCreateDate();
            $fields['FOLDER_UPDATE_DATE'] = $tr->getFolderUpdateDate();
        } elseif ($folderUid == "/") {
            $fields['FOLDER_UID'] = "/";
            $fields['FOLDER_PARENT_UID'] = "";
            $fields['FOLDER_NAME'] = "/";
            $fields['FOLDER_CREATE_DATE'] = "";
            $fields['FOLDER_UPDATE_DATE'] = "";
        } else {
            //      $fields = array ();
            $fields['FOLDER_UID'] = "/";
            $fields['FOLDER_PARENT_UID'] = "";
            $fields['FOLDER_NAME'] = "root";
            $fields['FOLDER_CREATE_DATE'] = "";
            $fields['FOLDER_UPDATE_DATE'] = "";
        }
        return $fields;
    }

    function getFolderStructure ($folderId)
    {
        $folderObj = $this->load( $folderId );
        $folderArray[$folderObj['FOLDER_UID']] = array ("NAME" => $folderObj['FOLDER_NAME'],"PARENT" => $folderObj['FOLDER_PARENT_UID']
        );
        $folderArray['PATH_ARRAY'][] = $folderObj['FOLDER_NAME'];

        while ($folderObj['FOLDER_PARENT_UID'] != "") {
            $folderObj = $this->load( $folderObj['FOLDER_PARENT_UID'] );
            $folderArray[$folderObj['FOLDER_UID']] = array ("NAME" => $folderObj['FOLDER_NAME'],"PARENT" => $folderObj['FOLDER_PARENT_UID']);
            $folderArray['PATH_ARRAY'][] = $folderObj['FOLDER_NAME'];
        }

        $folderArray['PATH'] = str_replace( "//", "/", implode( "/", array_reverse( $folderArray['PATH_ARRAY'] ) ) );
        return $folderArray;
    }

    function getFolderContent ($folderID, $docIdFilter = array(), $keyword = null, $searchType = null, $limit = 0, $start = 0, $user = '', $onlyActive = false)
    {
        require_once ("classes/model/AppDocument.php");
        require_once ("classes/model/InputDocument.php");
        require_once ("classes/model/OutputDocument.php");
        require_once ("classes/model/Users.php");

        G::LoadClass( 'case' );
        $oCase = new Cases();
        G::LoadClass( 'process' );
        $oProcess = new Process();

        $oAppDocument = new AppDocument();
        $oCriteria = new Criteria();

        if ((is_array( $docIdFilter )) && (count( $docIdFilter ) > 0)) {
            //Search by App Doc UID no matter what Folder it is
            $oCriteria->add( AppDocumentPeer::APP_DOC_UID, $docIdFilter, CRITERIA::IN );
        } elseif ($folderID != null) {
            if ($folderID == "/") {
                $oCriteria->add( AppDocumentPeer::FOLDER_UID, array ('root','',null), CRITERIA::IN );
            } else {
                $oCriteria->add( AppDocumentPeer::FOLDER_UID, $folderID );
            }
        } elseif ($searchType == "TAG") {
            $oCriteria->add( AppDocumentPeer::APP_DOC_TAGS, "%" . $keyword . "%", CRITERIA::LIKE );
        }

        if ($user != '') {
            require_once ("classes/model/AppDelegation.php");
            $criteria = new Criteria();
            $criteria->addSelectColumn( AppDelegationPeer::APP_UID );
            $criteria->setDistinct();

            $conditions = array ();
            $conditions[] = array (AppDelegationPeer::APP_UID,AppDocumentPeer::APP_UID);
            $conditions[] = array (AppDelegationPeer::DEL_INDEX,AppDocumentPeer::DEL_INDEX);

            $criteria->addJoinMC( $conditions, Criteria::LEFT_JOIN );

            $criteria->add( AppDelegationPeer::USR_UID, $user );

            $rs2 = AppDocumentPeer::doSelectRS( $criteria );

            $rs2->setFetchmode( ResultSet::FETCHMODE_ASSOC );
            $data = array ();
            while ($rs2->next()) {
                $row = $rs2->getRow();
                $data[] = $row['APP_UID'];
            }
            $oCriteria->add( AppDocumentPeer::APP_UID, $data, CRITERIA::IN );
        }
        if ($onlyActive) {
            $oCriteria->add( AppDocumentPeer::APP_DOC_STATUS, 'ACTIVE' );
        }

        $oCase->verifyTable();

        $oCriteria->addAscendingOrderByColumn( AppDocumentPeer::APP_DOC_INDEX );
        $oCriteria->addDescendingOrderByColumn( AppDocumentPeer::DOC_VERSION );

        $response['totalDocumentsCount'] = AppDocumentPeer::doCount( $oCriteria );
        $response['documents'] = array ();

        $oCriteria->setLimit( $limit );
        $oCriteria->setOffset( $start );

        $rs = AppDocumentPeer::doSelectRS( $oCriteria );
        $rs->setFetchmode( ResultSet::FETCHMODE_ASSOC );
        $rs->next();
        $filesResult = array ();
        while (is_array( $row = $rs->getRow() )) {
            //**** start get Doc Info
            $oApp = new Application();
            if (($oApp->exists( $row['APP_UID'] )) || ($row['APP_UID'] == "00000000000000000000000000000000")) {
                //$completeInfo = array("APP_DOC_FILENAME" => $row ["APP_DOC_UID"],"APP_DOC_UID"=>$row ['APP_UID']);
                $completeInfo = $this->getCompleteDocumentInfo( $row['APP_UID'], $row['APP_DOC_UID'], $row['DOC_VERSION'], $row['DOC_UID'], $row['USR_UID'] );
                $oAppDocument = new AppDocument();
                $lastVersion = $oAppDocument->getLastAppDocVersion( $row['APP_DOC_UID'], $row['APP_UID'] );
                //$filesResult [] = $completeInfo;
                if ($completeInfo['APP_DOC_STATUS'] != "DELETED") {
                    if ((in_array( $row['APP_DOC_UID'], $completeInfo['INPUT_DOCUMENTS'] )) || (in_array( $row['APP_DOC_UID'], $completeInfo['OUTPUT_DOCUMENTS'] )) || (in_array( $completeInfo['USR_UID'], array ($_SESSION['USER_LOGGED'],'-1') ))) {
                        if (count( $docIdFilter ) > 0) {
                            if (in_array( $row['APP_DOC_UID'], $docIdFilter )) {
                                $response['documents'][] = $completeInfo;
                            }
                        } elseif ($lastVersion == $row['DOC_VERSION']) {
                            //Only Last Document version
                            if ($searchType == "ALL") { // If search in name of docs is active then filter
                                if ((stripos( $completeInfo['APP_DOC_FILENAME'], $keyword ) !== false) || (stripos( $completeInfo['APP_DOC_TAGS'], $keyword ) !== false)) {
                                    $response['documents'][] = $completeInfo;
                                }
                            } else {
                                //No search filter active
                                $response['documents'][] = $completeInfo;
                            }
                        }
                    }
                }
            }
            $rs->next();
        }
        return ($response);
    }

    function getCompleteDocumentInfo ($appUid, $appDocUid, $docVersion, $docUid, $usrId)
    {
        require_once ("classes/model/AppDocument.php");
        require_once ("classes/model/InputDocument.php");
        require_once ("classes/model/OutputDocument.php");
        require_once ("classes/model/Users.php");
        //**** start get Doc Info
        $oApp = new Application();
        $oAppDocument = new AppDocument();
        G::LoadClass( 'case' );
        $oCase = new Cases();
        G::LoadClass( 'process' );
        $oProcess = new Process();
        if (($oApp->exists( $appUid )) || ($appUid == "00000000000000000000000000000000")) {
            if ($appUid == "00000000000000000000000000000000") { //External Files
                $row1 = $oAppDocument->load( $appDocUid, $docVersion );
                $row2 = array ('PRO_TITLE' => G::LoadTranslation( 'ID_NOT_PROCESS_RELATED' ));
                $row3 = array ('APP_TITLE' => G::LoadTranslation( 'ID_NOT_PROCESS_RELATED' ));
            } else {
                $row1 = $oAppDocument->load( $appDocUid, $docVersion );
                $row2 = $oCase->loadCase( $appUid );
                $row3 = $oProcess->Load( $row2['PRO_UID'] );
            }
            $lastVersion = $oAppDocument->getLastAppDocVersion( $appDocUid, $appUid );

            switch ($row1['APP_DOC_TYPE']) {
                case "OUTPUT":
                    $oOutputDocument = new OutputDocument();
                    $row4 = $oOutputDocument->load( $docUid );
                    $versioningEnabled = false; //$row4['OUT_DOC_VERSIONING']; //Only enabled for Input or Attached documents. Need to study the best way for Output docs.
                    switch ($row4['OUT_DOC_GENERATE']) {
                        case "PDF":
                            $downloadLink = "../cases/cases_ShowOutputDocument?a=" . $appDocUid . "&v=" . $docVersion . "&ext=pdf" . "&random=" . rand();
                            $downloadLink1 = "";
                            $downloadLabel = ".pdf";
                            $downloadLabel1 = "";
                            break;
                        case "DOC":
                            $downloadLink = "../cases/cases_ShowOutputDocument?a=" . $appDocUid . "&v=" . $docVersion . "&ext=doc" . "&random=" . rand();
                            $downloadLink1 = "";
                            $downloadLabel = ".doc";
                            $downloadLabel1 = "";
                            break;
                        case "BOTH":
                            $downloadLink = "../cases/cases_ShowOutputDocument?a=" . $appDocUid . "&v=" . $docVersion . "&ext=pdf" . "&random=" . rand();
                            $downloadLink1 = "../cases/cases_ShowOutputDocument?a=" . $appDocUid . "&v=" . $docVersion . "&ext=doc" . "&random=" . rand();
                            $downloadLabel = ".pdf";
                            $downloadLabel1 = ".doc";
                            break;
                    }

                    break;
                case "INPUT":
                    $oInputDocument = new InputDocument();
                    if ($docUid != - 1) {
                        if ($oInputDocument->InputExists( $docUid )) {
                            $row4 = $oInputDocument->load( $docUid );
                            $versioningEnabled = $row4['INP_DOC_VERSIONING'];
                        } else {
                            $row4 = array ();
                            $versioningEnabled = false;
                        }
                        $downloadLink = "../cases/cases_ShowDocument?a=" . $appDocUid . "&v=" . $docVersion;
                        $downloadLink1 = "";
                        $downloadLabel = G::LoadTranslation( 'ID_DOWNLOAD' );
                        $downloadLabel1 = "";
                    } else {
                        $row4 = array ();
                        $versioningEnabled = false;
                        $downloadLink = "../cases/cases_ShowDocument?a=" . $appDocUid . "&v=" . $docVersion;
                        $downloadLink1 = "";
                        $downloadLabel = G::LoadTranslation( 'ID_DOWNLOAD' );
                        $downloadLabel1 = "";
                    }

                    ///////
                    if (! empty( $row1["APP_DOC_PLUGIN"] )) {
                        $pluginRegistry = &PMPluginRegistry::getSingleton();
                        $pluginName = $row1["APP_DOC_PLUGIN"];
                        $fieldValue = "";

                        if (file_exists( PATH_PLUGINS . $pluginName . ".php" )) {
                            $pluginDetail = $pluginRegistry->getPluginDetails( $pluginName . ".php" );

                            if ($pluginDetail) {
                                if ($pluginDetail->enabled) {
                                    require_once (PATH_PLUGINS . $pluginName . ".php");
                                    $pluginNameClass = $pluginName . "Plugin";
                                    $objPluginClass = new $pluginNameClass( $pluginName );

                                    if (isset( $objPluginClass->sMethodGetUrlDownload ) && ! empty( $objPluginClass->sMethodGetUrlDownload )) {
                                        if (file_exists( PATH_PLUGINS . $pluginName . PATH_SEP . "class." . $pluginName . ".php" )) {
                                            require_once (PATH_PLUGINS . $pluginName . PATH_SEP . "class." . $pluginName . ".php");
                                            $pluginNameClass = $pluginName . "Class";
                                            $objClass = new $pluginNameClass();

                                            if (method_exists( $objClass, $objPluginClass->sMethodGetUrlDownload )) {
                                                eval( "\$url = \$objClass->" . $objPluginClass->sMethodGetUrlDownload . "(\"" . $row1["APP_DOC_UID"] . "\");" );
                                                $downloadLink = $url;
                                                $fieldValue = $row1["APP_DOC_PLUGIN"];
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        $row1["APP_DOC_PLUGIN"] = $fieldValue;
                    }
                    break;

                default:
                    $row4 = array ();
                    $versioningEnabled = false;
                    $downloadLink = "../cases/cases_ShowDocument?a=" . $appDocUid . "&v=" . $docVersion;
                    $downloadLink1 = "";
                    $downloadLabel = G::LoadTranslation( 'ID_DOWNLOAD' );
                    $downloadLabel1 = "";
                    break;
            }
            $oUser = new Users();
            if (($usrId != "-1") && ($oUser->userExists( $usrId ))) {
                $row5 = $oUser->load( $usrId );
            } else {
                $row5['USR_USERNAME'] = "***";
            }

            //Labels/Links
            $row6 = array ();
            $row6['DELETE_LABEL'] = G::LoadTranslation( 'ID_DELETE' );
            $row6['DOWNLOAD_LABEL'] = $downloadLabel;
            $row6['DOWNLOAD_LINK'] = $downloadLink;
            $row6['DOWNLOAD_LABEL1'] = $downloadLabel1;
            $row6['DOWNLOAD_LINK1'] = $downloadLink1;
            //if(($docVersion>1)&&($row1['APP_DOC_TYPE']!="OUTPUT")){
            if (($docVersion > 1)) {
                $row6['VERSIONHISTORY_LABEL'] = G::LoadTranslation( 'ID_VERSION_HISTORY' );
            }
            if ($versioningEnabled) {
                $row6['NEWVERSION_LABEL'] = G::LoadTranslation( 'ID_NEW_VERSION' );
            }
            $row6['APP_DOC_UID_VERSION'] = $appDocUid . "_" . $docVersion;

            if ($appUid == "00000000000000000000000000000000") {
                //External Files
                $row1['APP_DOC_TYPE'] = G::LoadTranslation( 'ID_EXTERNAL_FILE' );
            }
            //**** End get docinfo
            $infoMerged = array_merge( $row1, $row2, $row3, $row4, $row5, $row6 );
            //krumo($infoMerged);
            //****************************************************************************************************
            $sUserUID = $_SESSION['USER_LOGGED'];
            $aObjectPermissions = array ();
            if (isset( $infoMerged['PRO_UID'] )) {
                $aObjectPermissions = $oCase->getAllObjects( $infoMerged['PRO_UID'], $infoMerged['APP_UID'], '', $sUserUID );
            }

            if (! is_array( $aObjectPermissions )) {
                $aObjectPermissions = array ('DYNAFORMS' => array (- 1),
                'INPUT_DOCUMENTS' => array (- 1),
                'OUTPUT_DOCUMENTS' => array (- 1)
                );
            }
            if (! isset( $aObjectPermissions['DYNAFORMS'] )) {
                $aObjectPermissions['DYNAFORMS'] = array (- 1);
            } else {
                if (! is_array( $aObjectPermissions['DYNAFORMS'] )) {
                    $aObjectPermissions['DYNAFORMS'] = array (- 1);
                }
            }
            if (! isset( $aObjectPermissions['INPUT_DOCUMENTS'] )) {
                $aObjectPermissions['INPUT_DOCUMENTS'] = array (- 1);
            } else {
                if (! is_array( $aObjectPermissions['INPUT_DOCUMENTS'] )) {
                    $aObjectPermissions['INPUT_DOCUMENTS'] = array (- 1);
                }
            }
            if (! isset( $aObjectPermissions['OUTPUT_DOCUMENTS'] )) {
                $aObjectPermissions['OUTPUT_DOCUMENTS'] = array (- 1);
            } else {
                if (! is_array( $aObjectPermissions['OUTPUT_DOCUMENTS'] )) {
                    $aObjectPermissions['OUTPUT_DOCUMENTS'] = array (- 1);
                }
            }
            //****************************************************************************************************
            return array_merge( $infoMerged, $aObjectPermissions );
        }
    }

    function getFolderChilds ($folderID, $folderArray)
    {
        $folderList = $this->getFolderList( $folderID );
        $foldersList = array ();
        foreach ($folderList as $key => $folderObj) {
            $foldersList[$folderObj['FOLDER_UID']] = $folderObj['FOLDER_NAME'];
            $foldersList = array_merge( $foldersList, $this->getFolderChilds( $folderObj['FOLDER_UID'], $folderArray ) );
        }
        return (array_merge( $folderArray, $foldersList ));
    }

    function getFolderTags ($rootFolder)
    {
        $folderArray[$rootFolder] = $rootFolder;
        $foldersToProcess = $this->getFolderChilds( $rootFolder, $folderArray );
        $tagsInfo = array ();

        foreach ($foldersToProcess as $folderkey => $foldername) {
            $filesList = $this->getFolderContent( $folderkey );

            foreach ($filesList as $key => $fileInfo) {
                $fileTags = explode( ",", $fileInfo['APP_DOC_TAGS'] );
                foreach ($fileTags as $key1 => $tag) {
                    if (! (isset( $tagsInfo[$tag] )))
                        $tagsInfo[$tag] = 0;
                    $tagsInfo[$tag] ++;
                }
            }
        }
        return $tagsInfo;

    }

    function remove ($FolderUid, $rootfolder)
    {
        $oCriteria = new Criteria( 'workflow' );
        $oCriteria->add( AppFolderPeer::FOLDER_UID, $FolderUid );
        AppFolderPeer::doDelete( $oCriteria );
    }
}
// AppFolder

