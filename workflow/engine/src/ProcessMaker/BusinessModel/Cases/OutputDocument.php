<?php
namespace ProcessMaker\BusinessModel\Cases;

class OutputDocument
{
    /**
     * Check if the user has permissions
     *
     * @param string $applicationUid Unique id of Case
     * @param string $delIndex       Delegation index
     * @param string $userUid        Unique id of User
     * @param string $appDocumentUid
     *
     * return void Throw exception the user does not have permission to delete
     */
    public function throwExceptionIfHaventPermissionToDelete($applicationUid, $delIndex, $userUid, $appDocumentUid)
    {
        try {
            //Verify data inbox
            $case = new \ProcessMaker\BusinessModel\Cases();
            $arrayResult = $case->getStatusInfo($applicationUid, $delIndex, $userUid);

            $flagInbox = 1;

            if (empty($arrayResult) || !preg_match("/^(?:TO_DO|DRAFT)$/", $arrayResult["APP_STATUS"])) {
                $flagInbox = 0;
            }

            //Verify data permission
            $flagPermission = 0;

            $criteria = new \Criteria("workflow");

            $criteria->addSelectColumn(\AppDocumentPeer::DOC_UID);

            $criteria->add(\AppDocumentPeer::APP_DOC_UID, $appDocumentUid, \Criteria::EQUAL);
            $criteria->add(\AppDocumentPeer::APP_UID, $applicationUid, \Criteria::EQUAL);
            $criteria->add(\AppDocumentPeer::APP_DOC_TYPE, "OUTPUT", \Criteria::EQUAL);

            $rsCriteria = \AppDocumentPeer::doSelectRS($criteria);
            $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

            if ($rsCriteria->next()) {
                $row = $rsCriteria->getRow();

                $outputDocumentUid = $row["DOC_UID"];

                $application = \ApplicationPeer::retrieveByPK($applicationUid);

                //Criteria
                $criteria2 = new \Criteria("workflow");

                $criteria2->addSelectColumn(\ObjectPermissionPeer::OP_UID);

                $criteria2->add(\ObjectPermissionPeer::PRO_UID, $application->getProUid(), \Criteria::EQUAL);
                $criteria2->add(\ObjectPermissionPeer::OP_OBJ_TYPE, "OUTPUT", \Criteria::EQUAL);
                $criteria2->add(
                    $criteria2->getNewCriterion(\ObjectPermissionPeer::OP_OBJ_UID, $outputDocumentUid, \Criteria::EQUAL)->addOr(
                    $criteria2->getNewCriterion(\ObjectPermissionPeer::OP_OBJ_UID, "0", \Criteria::EQUAL))->addOr(
                    $criteria2->getNewCriterion(\ObjectPermissionPeer::OP_OBJ_UID, "", \Criteria::EQUAL))
                );
                $criteria2->add(\ObjectPermissionPeer::OP_ACTION, "DELETE", \Criteria::EQUAL);

                //User
                $criteriaU = clone $criteria2;

                $criteriaU->add(\ObjectPermissionPeer::OP_USER_RELATION, 1, \Criteria::EQUAL);
                $criteriaU->add(\ObjectPermissionPeer::USR_UID, $userUid, \Criteria::EQUAL);

                $rsCriteriaU = \ObjectPermissionPeer::doSelectRS($criteriaU);
                $rsCriteriaU->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

                if ($rsCriteriaU->next()) {
                    $flagPermission = 1;
                }

                //Group
                if ($flagPermission == 0) {
                    $criteriaG = clone $criteria2;

                    $criteriaG->add(\ObjectPermissionPeer::OP_USER_RELATION, 2, \Criteria::EQUAL);

                    $criteriaG->addJoin(\ObjectPermissionPeer::USR_UID, \GroupUserPeer::GRP_UID, \Criteria::LEFT_JOIN);
                    $criteriaG->add(\GroupUserPeer::USR_UID, $userUid, \Criteria::EQUAL);

                    $rsCriteriaG = \ObjectPermissionPeer::doSelectRS($criteriaG);
                    $rsCriteriaG->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

                    if ($rsCriteriaG->next()) {
                        $flagPermission = 1;
                    }
                }
            }

            if ($flagInbox == 1) {
                if ($flagPermission == 0) {
                    throw new \Exception(\G::LoadTranslation("ID_USER_NOT_HAVE_PERMISSION_DELETE_OUTPUT_DOCUMENT", array($userUid)));
                }
            } else {
                if ($flagPermission == 0) {
                    throw new \Exception(\G::LoadTranslation("ID_USER_NOT_HAVE_PERMISSION_DELETE_OUTPUT_DOCUMENT", array($userUid)));
                }
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Verify if does not exists the inbox
     *
     * @param string $applicationUid Unique id of Case
     * @param string $delIndex       Delegation index
     * @param string $userUid        Unique id of User
     *
     * return void Throw exception if not exists in inbox
     */
    public function throwExceptionIfCaseNotIsInInbox($applicationUid, $delIndex, $userUid)
    {
        try {
            //Verify data
            $case = new \ProcessMaker\BusinessModel\Cases();
            $arrayResult = $case->getStatusInfo($applicationUid, $delIndex, $userUid);

            if (empty($arrayResult) || !preg_match("/^(?:TO_DO|DRAFT)$/", $arrayResult["APP_STATUS"])) {
                throw new \Exception(\G::LoadTranslation("ID_USER_NOT_HAVE_PERMISSION", array($userUid)));
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Verify if not exists OuputDocument in Steps
     *
     * @param string $applicationUid Unique id of Case
     * @param string $delIndex       Delegation index
     * @param string $outDocUuid
     *
     * return void Throw exception if not exists OuputDocument in Steps
     */
    public function throwExceptionIfOuputDocumentNotExistsInSteps($applicacionUid, $delIndex, $outputDocumentUid)
    {
        try {
            //Verify data
            $appDelegation = \AppDelegationPeer::retrieveByPK($applicacionUid, $delIndex);

            $taskUid = $appDelegation->getTasUid();

            $criteria = new \Criteria("workflow");

            $criteria->addSelectColumn(\StepPeer::STEP_UID);

            $criteria->add(\StepPeer::TAS_UID, $taskUid, \Criteria::EQUAL);
            $criteria->add(\StepPeer::STEP_TYPE_OBJ, "OUTPUT_DOCUMENT", \Criteria::EQUAL);
            $criteria->add(\StepPeer::STEP_UID_OBJ, $outputDocumentUid, \Criteria::EQUAL);

            $rsCriteria = \StepPeer::doSelectRS($criteria);
            $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

            if (!$rsCriteria->next()) {
                throw new \Exception(\G::LoadTranslation("ID_CASES_OUTPUT_DOES_NOT_EXIST", array($outputDocumentUid)));
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get data of Cases OutputDocument
     *
     * @param string $applicationUid
     * @param string $userUid
     *
     * return array Return an array with data of an OutputDocument
     */
    public function getCasesOutputDocuments($applicationUid, $userUid)
    {
        try {
            \G::LoadClass('case');
            $oCase = new \Cases();
            $fields = $oCase->loadCase( $applicationUid );
            $sProcessUID = $fields['PRO_UID'];
            $sTaskUID = '';
            $oCriteria = new \ProcessMaker\BusinessModel\Cases();
            $oCriteria->getAllGeneratedDocumentsCriteria( $sProcessUID, $applicationUid, $sTaskUID, $userUid);
            $result = array ();
            global $_DBArray;
            foreach ($_DBArray['outputDocuments'] as $key => $row) {
                if (isset( $row['DOC_VERSION'] )) {
                    $docrow = array ();
                    $docrow['app_doc_uid'] = $row['APP_DOC_UID'];
                    $docrow['app_doc_filename'] = $row['DOWNLOAD_FILE'];
                    $docrow['doc_uid'] = $row['DOC_UID'];
                    $docrow['app_doc_version'] = $row['DOC_VERSION'];
                    $docrow['app_doc_create_date'] = $row['CREATE_DATE'];
                    $docrow['app_doc_create_user'] = $row['CREATED_BY'];
                    $docrow['app_doc_type'] = $row['TYPE'];
                    $docrow['app_doc_index'] = $row['APP_DOC_INDEX'];
                    $docrow['app_doc_link'] = 'cases/' . $row['DOWNLOAD_LINK'];
                    $result[] = $docrow;
                }
            }
            return $result;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get data of Cases OutputDocument
     *
     * @param string $applicationUid
     * @param string $userUid
     * @param string $applicationDocumentUid
     *
     * return object Return an object with data of an OutputDocument
     */
    public function getCasesOutputDocument($applicationUid, $userUid, $applicationDocumentUid)
    {
        try {
            $sApplicationUID = $applicationUid;
            $sUserUID = $userUid;
            \G::LoadClass('case');
            $oCase = new \Cases();
            $fields = $oCase->loadCase( $sApplicationUID );
            $sProcessUID = $fields['PRO_UID'];
            $sTaskUID = '';
            $oCaseRest = new \ProcessMaker\BusinessModel\Cases();
            $oCaseRest->getAllGeneratedDocumentsCriteria( $sProcessUID, $sApplicationUID, $sTaskUID, $sUserUID );
            $result = array ();
            global $_DBArray;
            foreach ($_DBArray['outputDocuments'] as $key => $row) {
                if (isset( $row['DOC_VERSION'] )) {
                    $docrow = array ();
                    $docrow['app_doc_uid'] = $row['APP_DOC_UID'];
                    $docrow['app_doc_filename'] = $row['DOWNLOAD_FILE'];
                    $docrow['doc_uid'] = $row['DOC_UID'];
                    $docrow['app_doc_version'] = $row['DOC_VERSION'];
                    $docrow['app_doc_create_date'] = $row['CREATE_DATE'];
                    $docrow['app_doc_create_user'] = $row['CREATED_BY'];
                    $docrow['app_doc_type'] = $row['TYPE'];
                    $docrow['app_doc_index'] = $row['APP_DOC_INDEX'];
                    $docrow['app_doc_link'] = 'cases/' . $row['DOWNLOAD_LINK'];
                    if ($docrow['app_doc_uid'] == $applicationDocumentUid) {
                        $oAppDocument = \AppDocumentPeer::retrieveByPK( $applicationDocumentUid, $row['DOC_VERSION'] );
                        if (is_null( $oAppDocument )) {
                            throw new \Exception(\G::LoadTranslation("ID_CASES_OUTPUT_DOES_NOT_EXIST", array($applicationDocumentUid)));
                        }
                        $result = $docrow;
                    }
                }
            }
            $oResponse = json_decode(json_encode($result), false);
            return $oResponse;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Delete OutputDocument
     *
     * @param string $applicationDocumentUid
     *
     */
    public function removeOutputDocument($applicationDocumentUid)
    {
        try {
            $oAppDocumentVersion = new \AppDocument();
            $lastDocVersion = $oAppDocumentVersion->getLastAppDocVersion($applicationDocumentUid);
            $oAppDocument = \AppDocumentPeer::retrieveByPK( $applicationDocumentUid, $lastDocVersion);
            if (is_null( $oAppDocument ) || $oAppDocument->getAppDocStatus() == 'DELETED') {
                throw new \Exception(\G::LoadTranslation("ID_CASES_OUTPUT_DOES_NOT_EXIST", array($applicationDocumentUid)));
            }
            $aFields = array ('APP_DOC_UID' => $applicationDocumentUid,'DOC_VERSION' => $lastDocVersion,'APP_DOC_STATUS' => 'DELETED');
            $oAppDocument->update( $aFields );
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get data of Cases OutputDocument
     *
     * @param string $applicationUid
     * @param string $outputDocumentUid
     * @param string $userUid
     *
     * return object Return an object with data of an OutputDocument
     */
    public function addCasesOutputDocument($applicationUid, $outputDocumentUid, $userUid)
    {
        try {
            $sApplication = $applicationUid;
            $index = \AppDelegation::getCurrentIndex($applicationUid);
            $sUserLogged = $userUid;
            $outputID = $outputDocumentUid;
            $g = new \G();
            $g->sessionVarSave();
            \G::LoadClass( 'case' );
            $oCase = new \Cases();
            $oCase->thisIsTheCurrentUser( $sApplication, $index, $sUserLogged, '', 'casesListExtJs' );
            //require_once 'classes/model/OutputDocument.php';
            $oOutputDocument = new \OutputDocument();
            $aOD = $oOutputDocument->load( $outputID );
            $Fields = $oCase->loadCase( $sApplication );
            $sFilename = preg_replace( '[^A-Za-z0-9_]', '_', \G::replaceDataField( $aOD['OUT_DOC_FILENAME'], $Fields['APP_DATA'] ) );
            require_once (PATH_TRUNK . "workflow" . PATH_SEP . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "AppFolder.php");
            require_once (PATH_TRUNK . "workflow" . PATH_SEP . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "AppDocument.php");
            //Get the Custom Folder ID (create if necessary)
            $oFolder = new \AppFolder();
            $folderId = $oFolder->createFromPath( $aOD['OUT_DOC_DESTINATION_PATH'], $sApplication );
            //Tags
            $fileTags = $oFolder->parseTags( $aOD['OUT_DOC_TAGS'], $sApplication );
            //Get last Document Version and apply versioning if is enabled
            $oAppDocument = new \AppDocument();
            $lastDocVersion = $oAppDocument->getLastDocVersion( $outputID, $sApplication );
            $oCriteria = new \Criteria( 'workflow' );
            $oCriteria->add( \AppDocumentPeer::APP_UID, $sApplication );
            $oCriteria->add( \AppDocumentPeer::DOC_UID, $outputID );
            $oCriteria->add( \AppDocumentPeer::DOC_VERSION, $lastDocVersion );
            $oCriteria->add( \AppDocumentPeer::APP_DOC_TYPE, 'OUTPUT' );
            $oDataset = \AppDocumentPeer::doSelectRS( $oCriteria );
            $oDataset->setFetchmode( \ResultSet::FETCHMODE_ASSOC );
            $oDataset->next();
            if (($aOD['OUT_DOC_VERSIONING']) && ($lastDocVersion != 0)) {
                //Create new Version of current output
                $lastDocVersion ++;
                if ($aRow = $oDataset->getRow()) {
                    $aFields = array ('APP_DOC_UID' => $aRow['APP_DOC_UID'],'APP_UID' => $sApplication,'DEL_INDEX' => $index,'DOC_UID' => $outputID,'DOC_VERSION' => $lastDocVersion + 1,'USR_UID' => $sUserLogged,'APP_DOC_TYPE' => 'OUTPUT','APP_DOC_CREATE_DATE' => date( 'Y-m-d H:i:s' ),'APP_DOC_FILENAME' => $sFilename,'FOLDER_UID' => $folderId,'APP_DOC_TAGS' => $fileTags);
                    $oAppDocument = new \AppDocument();
                    $oAppDocument->create( $aFields );
                    $sDocUID = $aRow['APP_DOC_UID'];
                }
            } else {
                ////No versioning so Update a current Output or Create new if no exist
                if ($aRow = $oDataset->getRow()) {
                    //Update
                    $aFields = array ('APP_DOC_UID' => $aRow['APP_DOC_UID'],'APP_UID' => $sApplication,'DEL_INDEX' => $index,'DOC_UID' => $outputID,'DOC_VERSION' => $lastDocVersion,'USR_UID' => $sUserLogged,'APP_DOC_TYPE' => 'OUTPUT','APP_DOC_CREATE_DATE' => date( 'Y-m-d H:i:s' ),'APP_DOC_FILENAME' => $sFilename,'FOLDER_UID' => $folderId,'APP_DOC_TAGS' => $fileTags
                    );
                    $oAppDocument = new \AppDocument();
                    $oAppDocument->update( $aFields );
                    $sDocUID = $aRow['APP_DOC_UID'];
                } else {
                    //we are creating the appdocument row
                    //create
                    if ($lastDocVersion == 0) {
                        $lastDocVersion ++;
                    }
                    $aFields = array ('APP_UID' => $sApplication,'DEL_INDEX' => $index,'DOC_UID' => $outputID,'DOC_VERSION' => $lastDocVersion,'USR_UID' => $sUserLogged,'APP_DOC_TYPE' => 'OUTPUT','APP_DOC_CREATE_DATE' => date( 'Y-m-d H:i:s' ),'APP_DOC_FILENAME' => $sFilename,'FOLDER_UID' => $folderId,'APP_DOC_TAGS' => $fileTags
                    );
                    $oAppDocument = new \AppDocument();
                    $aFields['APP_DOC_UID'] = $sDocUID = $oAppDocument->create( $aFields );
                }
            }
            $sFilename = $aFields['APP_DOC_UID'] . "_" . $lastDocVersion;
            $pathOutput = PATH_DOCUMENT . \G::getPathFromUID($sApplication) . PATH_SEP . 'outdocs' . PATH_SEP; //G::pr($sFilename);die;
            \G::mk_dir( $pathOutput );
            $aProperties = array ();
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
            $aProperties['margins'] = array ('left' => $aOD['OUT_DOC_LEFT_MARGIN'],'right' => $aOD['OUT_DOC_RIGHT_MARGIN'],'top' => $aOD['OUT_DOC_TOP_MARGIN'],'bottom' => $aOD['OUT_DOC_BOTTOM_MARGIN']
            );
            if (isset($aOD['OUT_DOC_REPORT_GENERATOR'])) {
                $aProperties['report_generator'] = $aOD['OUT_DOC_REPORT_GENERATOR'];
            }
            $this->generate( $outputID, $Fields['APP_DATA'], $pathOutput, $sFilename, $aOD['OUT_DOC_TEMPLATE'], (boolean) $aOD['OUT_DOC_LANDSCAPE'], $aOD['OUT_DOC_GENERATE'], $aProperties , $applicationUid);
            //Plugin Hook PM_UPLOAD_DOCUMENT for upload document
            //G::LoadClass('plugin');
            $oPluginRegistry = & \PMPluginRegistry::getSingleton();
            if ($oPluginRegistry->existsTrigger( PM_UPLOAD_DOCUMENT ) && class_exists( 'uploadDocumentData' )) {
                $triggerDetail = $oPluginRegistry->getTriggerInfo( PM_UPLOAD_DOCUMENT );
                $aFields['APP_DOC_PLUGIN'] = $triggerDetail->sNamespace;
                $oAppDocument1 = new \AppDocument();
                $oAppDocument1->update( $aFields );
                $sPathName = PATH_DOCUMENT . \G::getPathFromUID($sApplication) . PATH_SEP;
                $oData['APP_UID'] = $sApplication;
                $oData['ATTACHMENT_FOLDER'] = true;
                switch ($aOD['OUT_DOC_GENERATE']) {
                    case "BOTH":
                        $documentData = new \uploadDocumentData( $sApplication, $sUserLogged, $pathOutput . $sFilename . '.pdf', $sFilename . '.pdf', $sDocUID, $oAppDocument->getDocVersion() );
                        $documentData->sFileType = "PDF";
                        $documentData->bUseOutputFolder = true;
                        $uploadReturn = $oPluginRegistry->executeTriggers( PM_UPLOAD_DOCUMENT, $documentData );
                        if ($uploadReturn) {
                            //Only delete if the file was saved correctly
                            unlink( $pathOutput . $sFilename . '.pdf' );
                        }
                        $documentData = new \uploadDocumentData( $sApplication, $sUserLogged, $pathOutput . $sFilename . '.doc', $sFilename . '.doc', $sDocUID, $oAppDocument->getDocVersion() );
                        $documentData->sFileType = "DOC";
                        $documentData->bUseOutputFolder = true;
                        $uploadReturn = $oPluginRegistry->executeTriggers( PM_UPLOAD_DOCUMENT, $documentData );
                        if ($uploadReturn) {
                            //Only delete if the file was saved correctly
                            unlink( $pathOutput . $sFilename . '.doc' );
                        }
                        break;
                    case "PDF":
                        $documentData = new \uploadDocumentData( $sApplication, $sUserLogged, $pathOutput . $sFilename . '.pdf', $sFilename . '.pdf', $sDocUID, $oAppDocument->getDocVersion() );
                        $documentData->sFileType = "PDF";
                        $documentData->bUseOutputFolder = true;
                        $uploadReturn = $oPluginRegistry->executeTriggers( PM_UPLOAD_DOCUMENT, $documentData );
                        if ($uploadReturn) {
                            //Only delete if the file was saved correctly
                            unlink( $pathOutput . $sFilename . '.pdf' );
                        }
                        break;
                    case "DOC":
                        $documentData = new \uploadDocumentData( $sApplication, $sUserLogged, $pathOutput . $sFilename . '.doc', $sFilename . '.doc', $sDocUID, $oAppDocument->getDocVersion() );
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
            $g->sessionVarRestore();
            $oAppDocument = \AppDocumentPeer::retrieveByPK( $aFields['APP_DOC_UID'], $lastDocVersion);
            if ($oAppDocument->getAppDocStatus() == 'DELETED') {
                $oAppDocument->setAppDocStatus('ACTIVE');
                $oAppDocument->save();
            }
            $response = $this->getCasesOutputDocument($applicationUid, $userUid, $aFields['APP_DOC_UID']);
            return $response;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /*
     * Generate the output document
     * @param string $sUID
     * @param array $aFields
     * @param string $sPath
     * @return variant
     */
    public function generate($sUID, $aFields, $sPath, $sFilename, $sContent, $sLandscape = false, $sTypeDocToGener = 'BOTH', $aProperties = array(), $sApplication)
    {
        if (($sUID != '') && is_array($aFields) && ($sPath != '')) {
            $sContent = \G::replaceDataGridField($sContent, $aFields);
            \G::verifyPath($sPath, true);
            //Start - Create .doc
            $oFile = fopen($sPath . $sFilename . '.doc', 'wb');
            $size = array();
            $size["Letter"] = "216mm  279mm";
            $size["Legal"] = "216mm  357mm";
            $size["Executive"] = "184mm  267mm";
            $size["B5"] = "182mm  257mm";
            $size["Folio"] = "216mm  330mm";
            $size["A0Oversize"] = "882mm  1247mm";
            $size["A0"] = "841mm  1189mm";
            $size["A1"] = "594mm  841mm";
            $size["A2"] = "420mm  594mm";
            $size["A3"] = "297mm  420mm";
            $size["A4"] = "210mm  297mm";
            $size["A5"] = "148mm  210mm";
            $size["A6"] = "105mm  148mm";
            $size["A7"] = "74mm   105mm";
            $size["A8"] = "52mm   74mm";
            $size["A9"] = "37mm   52mm";
            $size["A10"] = "26mm   37mm";
            $size["Screenshot640"] = "640mm  480mm";
            $size["Screenshot800"] = "800mm  600mm";
            $size["Screenshot1024"] = "1024mm 768mm";
            $sizeLandscape["Letter"] = "279mm  216mm";
            $sizeLandscape["Legal"] = "357mm  216mm";
            $sizeLandscape["Executive"] = "267mm  184mm";
            $sizeLandscape["B5"] = "257mm  182mm";
            $sizeLandscape["Folio"] = "330mm  216mm";
            $sizeLandscape["A0Oversize"] = "1247mm 882mm";
            $sizeLandscape["A0"] = "1189mm 841mm";
            $sizeLandscape["A1"] = "841mm  594mm";
            $sizeLandscape["A2"] = "594mm  420mm";
            $sizeLandscape["A3"] = "420mm  297mm";
            $sizeLandscape["A4"] = "297mm  210mm";
            $sizeLandscape["A5"] = "210mm  148mm";
            $sizeLandscape["A6"] = "148mm  105mm";
            $sizeLandscape["A7"] = "105mm  74mm";
            $sizeLandscape["A8"] = "74mm   52mm";
            $sizeLandscape["A9"] = "52mm   37mm";
            $sizeLandscape["A10"] = "37mm   26mm";
            $sizeLandscape["Screenshot640"] = "480mm  640mm";
            $sizeLandscape["Screenshot800"] = "600mm  800mm";
            $sizeLandscape["Screenshot1024"] = "768mm  1024mm";
            if (!isset($aProperties['media'])) {
                $aProperties['media'] = 'Letter';
            }
            if ($sLandscape) {
                $media = $sizeLandscape[$aProperties['media']];
            } else {
                $media = $size[$aProperties['media']];
            }
            $marginLeft = '15';
            if (isset($aProperties['margins']['left'])) {
                $marginLeft = $aProperties['margins']['left'];
            }
            $marginRight = '15';
            if (isset($aProperties['margins']['right'])) {
                $marginRight = $aProperties['margins']['right'];
            }
            $marginTop = '15';
            if (isset($aProperties['margins']['top'])) {
                $marginTop = $aProperties['margins']['top'];
            }
            $marginBottom = '15';
            if (isset($aProperties['margins']['bottom'])) {
                $marginBottom = $aProperties['margins']['bottom'];
            }
            fwrite($oFile, '<html xmlns:v="urn:schemas-microsoft-com:vml"
            xmlns:o="urn:schemas-microsoft-com:office:office"
            xmlns:w="urn:schemas-microsoft-com:office:word"
            xmlns="http://www.w3.org/TR/REC-html40">
            <head>
            <meta http-equiv=Content-Type content="text/html; charset=utf-8">
            <meta name=ProgId content=Word.Document>
            <meta name=Generator content="Microsoft Word 9">
            <meta name=Originator content="Microsoft Word 9">
            <!--[if !mso]>
            <style>
            v\:* {behavior:url(#default#VML);}
            o\:* {behavior:url(#default#VML);}
            w\:* {behavior:url(#default#VML);}
            .shape {behavior:url(#default#VML);}
            </style>
            <![endif]-->
            <!--[if gte mso 9]><xml>
             <w:WordDocument>
              <w:View>Print</w:View>
              <w:DoNotHyphenateCaps/>
              <w:PunctuationKerning/>
              <w:DrawingGridHorizontalSpacing>9.35 pt</w:DrawingGridHorizontalSpacing>
              <w:DrawingGridVerticalSpacing>9.35 pt</w:DrawingGridVerticalSpacing>
             </w:WordDocument>
            </xml><![endif]-->

            <style>
            <!--
            @page WordSection1
             {size:' . $media . ';
             margin-left:' . $marginLeft . 'mm;
             margin-right:' . $marginRight . 'mm;
             margin-bottom:' . $marginBottom . 'mm;
             margin-top:' . $marginTop . 'mm;
             mso-header-margin:35.4pt;
             mso-footer-margin:35.4pt;
             mso-paper-source:0;}
            div.WordSection1
             {page:WordSection1;}
            -->
            </style>
            </head>
            <body>
            <div class=WordSection1>');
            fwrite($oFile, $sContent);
            fwrite($oFile, "\n</div></body></html>\n\n");
            fclose($oFile);
            /* End - Create .doc */
            if ($sTypeDocToGener == 'BOTH' || $sTypeDocToGener == 'PDF') {
                $oFile = fopen($sPath . $sFilename . '.html', 'wb');
                fwrite($oFile, $sContent);
                fclose($oFile);
                /* Start - Create .pdf */
                if (isset($aProperties['report_generator'])) {
                    switch ($aProperties['report_generator']) {
                        case 'TCPDF':
                            $o = new \OutputDocument();
                            if (strlen($sContent) == 0) {
                                libxml_use_internal_errors(true);
                                $o->generateTcpdf($sUID, $aFields, $sPath, $sFilename, ' ', $sLandscape, $aProperties);
                                libxml_use_internal_errors(false);
                            } else {
                                $o->generateTcpdf($sUID, $aFields, $sPath, $sFilename, $sContent, $sLandscape, $aProperties);
                            }
                            break;
                        case 'HTML2PDF':
                        default:
                            $this->generateHtml2ps_pdf($sUID, $aFields, $sPath, $sFilename, $sContent, $sLandscape, $aProperties, $sApplication);
                            break;
                    }
                } else {
                    $this->generateHtml2ps_pdf($sUID, $aFields, $sPath, $sFilename, $sContent, $sLandscape, $aProperties);
                }
            }
            //end if $sTypeDocToGener
            /* End - Create .pdf */
        } else {
            return \PEAR::raiseError(
                null, G_ERROR_USER_UID, null, null, 'You tried to call to a generate method without send the Output Document UID, fields to use and the file path!', 'G_Error', true
            );
        }
    }

    /*
     * Generate Html2ps_pdf
     * @param string $sUID
     * @param array $aFields
     * @param string $sPath
     * @param string $sApplication
     * @return variant
     */
    public function generateHtml2ps_pdf($sUID, $aFields, $sPath, $sFilename, $sContent, $sLandscape = false, $aProperties = array(), $sApplication)
    {
        define("MAX_FREE_FRACTION", 1);
        define('PATH_OUTPUT_FILE_DIRECTORY', PATH_HTML . 'files/' . $sApplication . '/outdocs/');
        \G::verifyPath(PATH_OUTPUT_FILE_DIRECTORY, true);
        require_once (PATH_THIRDPARTY . 'html2ps_pdf/config.inc.php');
        require_once (PATH_THIRDPARTY . 'html2ps_pdf/pipeline.factory.class.php');
        parse_config_file(PATH_THIRDPARTY . 'html2ps_pdf/html2ps.config');
        $GLOBALS['g_config'] = array(
            'cssmedia' => 'screen',
            'media' => 'Letter',
            'scalepoints' => false,
            'renderimages' => true,
            'renderfields' => true,
            'renderforms' => false,
            'pslevel' => 3,
            'renderlinks' => true,
            'pagewidth' => 800,
            'landscape' => $sLandscape,
            'method' => 'fpdf',
            'margins' => array('left' => 15, 'right' => 15, 'top' => 15, 'bottom' => 15,),
            'encoding' => '',
            'ps2pdf' => false,
            'compress' => true,
            'output' => 2,
            'pdfversion' => '1.3',
            'transparency_workaround' => false,
            'imagequality_workaround' => false,
            'draw_page_border' => isset($_REQUEST['pageborder']),
            'debugbox' => false,
            'html2xhtml' => true,
            'mode' => 'html',
            'smartpagebreak' => true
        );
        $GLOBALS['g_config'] = array_merge($GLOBALS['g_config'], $aProperties);
        $g_media = \Media::predefined($GLOBALS['g_config']['media']);
        $g_media->set_landscape($GLOBALS['g_config']['landscape']);
        $g_media->set_margins($GLOBALS['g_config']['margins']);
        $g_media->set_pixels($GLOBALS['g_config']['pagewidth']);
        if (isset($GLOBALS['g_config']['pdfSecurity'])) {
            if (isset($GLOBALS['g_config']['pdfSecurity']['openPassword']) &&
                $GLOBALS['g_config']['pdfSecurity']['openPassword'] != ""
            ) {
                $GLOBALS['g_config']['pdfSecurity']['openPassword'] = G::decrypt(
                    $GLOBALS['g_config']['pdfSecurity']['openPassword'], $sUID
                );
            }
            if (isset($GLOBALS['g_config']['pdfSecurity']['ownerPassword']) &&
                $GLOBALS['g_config']['pdfSecurity']['ownerPassword'] != ""
            ) {
                $GLOBALS['g_config']['pdfSecurity']['ownerPassword'] = G::decrypt(
                    $GLOBALS['g_config']['pdfSecurity']['ownerPassword'], $sUID
                );
            }
            $g_media->set_security($GLOBALS['g_config']['pdfSecurity']);
            require_once(HTML2PS_DIR . 'pdf.fpdf.encryption.php');
        }
        $pipeline = new \Pipeline();
        if (extension_loaded('curl')) {
            require_once(HTML2PS_DIR . 'fetcher.url.curl.class.php');
            $pipeline->fetchers = array(new \FetcherURLCurl());
            if (isset($proxy)) {
                if ($proxy != '') {
                    $pipeline->fetchers[0]->set_proxy($proxy);
                }
            }
        } else {
            require_once(HTML2PS_DIR . 'fetcher.url.class.php');
            $pipeline->fetchers[] = new \FetcherURL();
        }
        $pipeline->data_filters[] = new \DataFilterDoctype();
        $pipeline->data_filters[] = new \DataFilterUTF8($GLOBALS['g_config']['encoding']);
        if ($GLOBALS['g_config']['html2xhtml']) {
            $pipeline->data_filters[] = new \DataFilterHTML2XHTML();
        } else {
            $pipeline->data_filters[] = new \DataFilterXHTML2XHTML();
        }
        $pipeline->parser = new \ParserXHTML();
        $pipeline->pre_tree_filters = array();
        $header_html = '';
        $footer_html = '';
        $filter = new \PreTreeFilterHeaderFooter($header_html, $footer_html);
        $pipeline->pre_tree_filters[] = $filter;

        if ($GLOBALS['g_config']['renderfields']) {
            $pipeline->pre_tree_filters[] = new \PreTreeFilterHTML2PSFields();
        }
        if ($GLOBALS['g_config']['method'] === 'ps') {
            $pipeline->layout_engine = new \LayoutEnginePS();
        } else {
            $pipeline->layout_engine = new \LayoutEngineDefault();
        }
        $pipeline->post_tree_filters = array();
        if ($GLOBALS['g_config']['pslevel'] == 3) {
            $image_encoder = new \PSL3ImageEncoderStream();
        } else {
            $image_encoder = new \PSL2ImageEncoderStream();
        }
        switch ($GLOBALS['g_config']['method']) {
            case 'fastps':
                if ($GLOBALS['g_config']['pslevel'] == 3) {
                    $pipeline->output_driver = new \OutputDriverFastPS($image_encoder);
                } else {
                    $pipeline->output_driver = new \OutputDriverFastPSLevel2($image_encoder);
                }
                break;
            case 'pdflib':
                $pipeline->output_driver = new \OutputDriverPDFLIB16($GLOBALS['g_config']['pdfversion']);
                break;
            case 'fpdf':
                $pipeline->output_driver = new \OutputDriverFPDF();
                break;
            case 'png':
                $pipeline->output_driver = new \OutputDriverPNG();
                break;
            case 'pcl':
                $pipeline->output_driver = new \OutputDriverPCL();
                break;
            default:
                die('Unknown output method');
        }
        if (isset($GLOBALS['g_config']['watermarkhtml'])) {
            $watermark_text = $GLOBALS['g_config']['watermarkhtml'];
        } else {
            $watermark_text = '';
        }
        $pipeline->output_driver->set_watermark($watermark_text);
        if ($watermark_text != '') {
            $dispatcher = & $pipeline->getDispatcher();
        }
        if ($GLOBALS['g_config']['debugbox']) {
            $pipeline->output_driver->set_debug_boxes(true);
        }
        if ($GLOBALS['g_config']['draw_page_border']) {
            $pipeline->output_driver->set_show_page_border(true);
        }
        if ($GLOBALS['g_config']['ps2pdf']) {
            $pipeline->output_filters[] = new \OutputFilterPS2PDF($GLOBALS['g_config']['pdfversion']);
        }
        if ($GLOBALS['g_config']['compress'] && $GLOBALS['g_config']['method'] == 'fastps') {
            $pipeline->output_filters[] = new \OutputFilterGZip();
        }
        if (!isset($GLOBALS['g_config']['process_mode'])) {
            $GLOBALS['g_config']['process_mode'] = '';
        }
        if ($GLOBALS['g_config']['process_mode'] == 'batch') {
            $filename = 'batch';
        } else {
            $filename = $sFilename;
        }
        switch ($GLOBALS['g_config']['output']) {
            case 0:
                $pipeline->destination = new \DestinationBrowser($filename);
                break;
            case 1:
                $pipeline->destination = new \DestinationDownload($filename);
                break;
            case 2:
                $pipeline->destination = new \DestinationFile($filename);
                break;
        }
        copy($sPath . $sFilename . '.html', PATH_OUTPUT_FILE_DIRECTORY . $sFilename . '.html');
        try {
            $status = $pipeline->process(((isset($_SERVER['HTTPS'])) && ($_SERVER['HTTPS'] == 'on') ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . '/files/' . $sApplication . '/outdocs/' . $sFilename . '.html', $g_media);
            copy(PATH_OUTPUT_FILE_DIRECTORY . $sFilename . '.pdf', $sPath . $sFilename . '.pdf');
            unlink(PATH_OUTPUT_FILE_DIRECTORY . $sFilename . '.pdf');
            unlink(PATH_OUTPUT_FILE_DIRECTORY . $sFilename . '.html');
        } catch (\Exception $e) {
            if ($e->getMessage() == 'ID_OUTPUT_NOT_GENERATE') {
                include_once 'classes/model/AppDocument.php';
                $dataDocument = explode('_', $sFilename);
                if (!isset($dataDocument[1])) {
                    $dataDocument[1] = 1;
                }
                $oAppDocument = new \AppDocument();
                $oAppDocument->remove($dataDocument[0], $dataDocument[1]);
                \G::SendTemporalMessage(\G::LoadTranslation('ID_OUTPUT_NOT_GENERATE'), 'Error');
            }
        }
    }
}

