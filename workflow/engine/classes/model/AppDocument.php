<?php
/**
 * AppDocument.php
 *
 * @package workflow.engine.classes.model
 *
 * ProcessMaker Open Source Edition
 * Copyright (C) 2004 - 2011 Colosa Inc.
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
 *
 */

//require_once 'classes/model/om/BaseAppDocument.php';
//require_once 'classes/model/Content.php';
//require_once 'classes/model/InputDocument.php';

/**
 * Skeleton subclass for representing a row from the 'APP_DOCUMENT' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements. This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package workflow.engine.classes.model
 */
class AppDocument extends BaseAppDocument
{

    /*----------------------------------********---------------------------------*/

    /*
     * Load the application document registry
     * @param string $sAppDocUid
     * @param integer $iVersion (Document version)
     * @return variant
     */
    public function load ($sAppDocUid, $iVersion = null)
    {
        try {
            if ($iVersion == null) {
                $iVersion = $this->getLastAppDocVersion( $sAppDocUid );
            }
            $oAppDocument = AppDocumentPeer::retrieveByPK( $sAppDocUid, $iVersion );
            if (! is_null( $oAppDocument )) {
                $aFields = $oAppDocument->toArray( BasePeer::TYPE_FIELDNAME );
                $this->fromArray( $aFields, BasePeer::TYPE_FIELDNAME );
                /*----------------------------------********---------------------------------*/
                return $aFields;
            } else {
                throw (new Exception( 'Error loading Document ' . $sAppDocUid . '/' . $iVersion . '. This row doesn\'t exist!' ));
            }
        } catch (Exception $oError) {
            throw ($oError);
        }
    }

    public function getLastIndex ($sAppUid)
    {
        try {
            $oCriteria = new Criteria();
            $oCriteria->add( AppDocumentPeer::APP_UID, $sAppUid );
            //$oCriteria->addAscendingOrderByColumn ( AppDocumentPeer::APP_DOC_INDEX );
            $oCriteria->addDescendingOrderByColumn( AppDocumentPeer::APP_DOC_INDEX );
            $lastAppDoc = AppDocumentPeer::doSelectOne( $oCriteria );
            if (! is_null( $lastAppDoc )) {
                return $lastAppDoc->getAppDocIndex();
            } else {
                return 0;
            }
        } catch (Exception $oError) {
            throw ($oError);
        }
    }

    /**
     * Get last Document Version based on Doc UID
     *
     * @param s $sAppDocUid
     * @return integer
     *
     */
    public function getLastDocVersion ($sDocUid, $appUID)
    {
        try {
            $oCriteria = new Criteria();
            $oCriteria->add( AppDocumentPeer::DOC_UID, $sDocUid );
            $oCriteria->add( AppDocumentPeer::APP_UID, $appUID );
            $oCriteria->addDescendingOrderByColumn( AppDocumentPeer::DOC_VERSION );
            $lastAppDocVersion = AppDocumentPeer::doSelectOne( $oCriteria );
            if (! is_null( $lastAppDocVersion )) {
                return $lastAppDocVersion->getDocVersion();
            } else {
                return 0;
            }
        } catch (Exception $oError) {
            throw ($oError);
        }
    }

    /**
     * Get last Document Version based on APPDoc UID
     *
     * @param s $sAppDocUid
     * @return integer
     *
     */
    public function getLastAppDocVersion ($sAppDocUid, $appUID = 0)
    {
        try {
            $oCriteria = new Criteria();
            $oCriteria->add( AppDocumentPeer::APP_DOC_UID, $sAppDocUid );
            if ($appUID != 0) {
                $oCriteria->add( AppDocumentPeer::APP_UID, $appUID );
            }
            $oCriteria->addDescendingOrderByColumn( AppDocumentPeer::DOC_VERSION );
            $lastAppDocVersion = AppDocumentPeer::doSelectOne( $oCriteria );
            if (! is_null( $lastAppDocVersion )) {
                return $lastAppDocVersion->getDocVersion();
            } else {
                return 0;
            }
        } catch (Exception $oError) {
            throw ($oError);
        }
    }

    /**
     * Create the application document registry
     *
     * @param array $aData
     * @return string
     *
     */
    public function create ($aData)
    {
        $oConnection = Propel::getConnection( AppDocumentPeer::DATABASE_NAME );
        try {
            $oAppDocument = new AppDocument();

            if (! isset( $aData['APP_DOC_UID'] )) {
                $sUID = G::generateUniqueID();
                $docVersion = 1;
            } else {
                $sUID = $aData['APP_DOC_UID'];
                $docVersion = $this->getLastAppDocVersion( $aData['APP_DOC_UID'], $oAppDocument->getAppUid() );
                $oAppDocument->load( $aData['APP_DOC_UID'], $docVersion );
                switch ($oAppDocument->getAppDocType()) {
                    case "OUTPUT": //Output versioning
                        $o = new OutputDocument();
                        $oOutputDocument = $o->load( $oAppDocument->getDocUid() );

                        if (! $oOutputDocument['OUT_DOC_VERSIONING']) {
                            throw (new Exception( 'The Output document has not versioning enabled!' ));
                        }
                        break;
                    case "INPUT": // Input versioning
                        $o = new InputDocument();
                        $oInputDocument = $o->load( $oAppDocument->getDocUid() );
                        if (! $oInputDocument['INP_DOC_VERSIONING']) {
                            throw (new Exception( 'This Input document does not have the versioning enabled, for this reason this operation cannot be completed' ));
                        }
                        break;
                    default: //Not a valid type
                        throw (new Exception( 'The document is not of a valid Type' ));
                        break;
                }

                $docVersion ++;
            }

            /*----------------------------------********---------------------------------*/
            $oAppDocument->fromArray( $aData, BasePeer::TYPE_FIELDNAME );
            $oAppDocument->setDocVersion( $docVersion );

            $oAppDocument->setAppDocUid( $sUID );
            $oAppDocument->setAppDocIndex( $this->getLastIndex( $oAppDocument->getAppUid() ) + 1 );
            if ($oAppDocument->validate()) {
                $oConnection->begin();
                if (isset( $aData['APP_DOC_TITLE'] )) {
                    $oAppDocument->setAppDocTitleContent( $aData['APP_DOC_TITLE'] );
                }
                if (isset( $aData['APP_DOC_COMMENT'] )) {
                    $oAppDocument->setAppDocCommentContent( $aData['APP_DOC_COMMENT'] );
                }
                if (isset( $aData['APP_DOC_FILENAME'] )) {
                    $oAppDocument->setAppDocFilenameContent( $aData['APP_DOC_FILENAME'] );
                }
                $iResult = $oAppDocument->save();
                $oConnection->commit();
                $this->fromArray( $oAppDocument->toArray( BasePeer::TYPE_FIELDNAME ), BasePeer::TYPE_FIELDNAME );
                return $sUID;
            } else {
                $sMessage = '';
                $aValidationFailures = $oAppDocument->getValidationFailures();
                foreach ($aValidationFailures as $oValidationFailure) {
                    $sMessage .= $oValidationFailure->getMessage() . '<br />';
                }
                throw (new Exception( 'The registry cannot be created!<br />' . $sMessage ));
            }
        } catch (Exception $oError) {
            $oConnection->rollback();
            throw ($oError);
        }
    }

    /**
     * Update the application document registry
     *
     * @param array $aData
     * @return string
     *
     */
    public function update ($aData)
    {
        $oConnection = Propel::getConnection( AppDocumentPeer::DATABASE_NAME );
        try {
            $oAppDocument = AppDocumentPeer::retrieveByPK( $aData['APP_DOC_UID'], $aData['DOC_VERSION'] );

            if (! is_null( $oAppDocument )) {
                /*----------------------------------********---------------------------------*/
                $oAppDocument->fromArray( $aData, BasePeer::TYPE_FIELDNAME );
                if ($oAppDocument->validate()) {
                    $oConnection->begin();
                    if (isset( $aData['APP_DOC_TITLE'] )) {
                        $oAppDocument->setAppDocTitleContent( $aData['APP_DOC_TITLE'] );
                    }
                    if (isset( $aData['APP_DOC_COMMENT'] )) {
                        $oAppDocument->setAppDocCommentContent( $aData['APP_DOC_COMMENT'] );
                    }
                    if (isset( $aData['APP_DOC_FILENAME'] )) {
                        $oAppDocument->setAppDocFilenameContent( $aData['APP_DOC_FILENAME'] );
                    }
                    $iResult = $oAppDocument->save();
                    $oConnection->commit();
                    return $iResult;
                } else {
                    $sMessage = '';
                    $aValidationFailures = $oAppDocument->getValidationFailures();
                    foreach ($aValidationFailures as $oValidationFailure) {
                        $sMessage .= $oValidationFailure->getMessage() . '<br />';
                    }
                    throw (new Exception( 'The registry cannot be updated!<br />' . $sMessage ));
                }
            } else {
                throw (new Exception( 'This row doesn\'t exist!' ));
            }
        } catch (Exception $oError) {
            $oConnection->rollback();
            throw ($oError);
        }
    }

    /**
     * Remove the application document registry by changing status only
     * Modified by Hugo Loza hugo@colosa.com
     *
     * @param array $aData
     * @return string
     *
     */
    public function remove ($sAppDocUid, $iVersion = 1)
    {
        $oConnection = Propel::getConnection( AppDocumentPeer::DATABASE_NAME );
        try {
            $oAppDocument = AppDocumentPeer::retrieveByPK( $sAppDocUid, $iVersion );
            if (! is_null( $oAppDocument )) {
                $arrayDocumentsToDelete = array ();
                if ($oAppDocument->getAppDocType() == "INPUT") {

                    $oCriteria = new Criteria( 'workflow' );
                    $oCriteria->add( AppDocumentPeer::APP_DOC_UID, $sAppDocUid );
                    $oDataset = AppDocumentPeer::doSelectRS( $oCriteria );
                    $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
                    $oDataset->next();
                    while ($aRow = $oDataset->getRow()) {
                        $arrayDocumentsToDelete[] = array ('sAppDocUid' => $aRow['APP_DOC_UID'],'iVersion' => $aRow['DOC_VERSION']
                        );
                        $oDataset->next();
                    }

                } else {
                    $arrayDocumentsToDelete[] = array ('sAppDocUid' => $sAppDocUid,'iVersion' => $iVersion
                    );
                }

                foreach ($arrayDocumentsToDelete as $key => $docToDelete) {
                    $aFields = array ('APP_DOC_UID' => $docToDelete['sAppDocUid'],'DOC_VERSION' => $docToDelete['iVersion'],'APP_DOC_STATUS' => 'DELETED'
                    );

                    $oAppDocument->update( $aFields );
                }

            } else {
                throw (new Exception( 'This row doesn\'t exist!' ));
            }
        } catch (Exception $oError) {
            $oConnection->rollback();
            throw ($oError);
        }
    }

    /**
     * Get the [app_doc_title] column value.
     *
     * @return string
     */
    public function getAppDocTitleContent ()
    {
        if ($this->app_doc_title == '') {
            try {
                $this->app_doc_title = Content::load( 'APP_DOC_TITLE', $this->getDocVersion(), $this->getAppDocUid(), (defined( 'SYS_LANG' ) ? SYS_LANG : 'en') );
                if ($this->app_doc_title == "") {
                    $this->app_doc_title = Content::load( 'APP_DOC_TITLE', '', $this->getAppDocUid(), (defined( 'SYS_LANG' ) ? SYS_LANG : 'en') ); //For backward compatibility
                }
            } catch (Exception $oError) {
                throw ($oError);
            }
        }
        return $this->app_doc_title;
    }

    /**
     * Set the [app_doc_title] column value.
     *
     * @param string $sValue new value
     * @return void
     */
    public function setAppDocTitleContent ($sValue)
    {
        if ($sValue !== null && ! is_string( $sValue )) {
            $sValue = (string) $sValue;
        }
        if (in_array(AppDocumentPeer::APP_DOC_TITLE, $this->modifiedColumns) || $sValue === '') {
            try {
                $this->app_doc_title = $sValue;
                $iResult = Content::addContent( 'APP_DOC_TITLE', $this->getDocVersion(), $this->getAppDocUid(), (defined( 'SYS_LANG' ) ? SYS_LANG : 'en'), $this->app_doc_title );
            } catch (Exception $oError) {
                $this->app_doc_title = '';
                throw ($oError);
            }
        }
    }

    /**
     * Get the [app_doc_comment] column value.
     *
     * @return string
     */
    public function getAppDocCommentContent ()
    {
        if ($this->app_doc_comment == '') {
            try {
                $this->app_doc_comment = Content::load( 'APP_DOC_COMMENT', $this->getDocVersion(), $this->getAppDocUid(), (defined( 'SYS_LANG' ) ? SYS_LANG : 'en') );
                if ($this->app_doc_comment == "") {
                    $this->app_doc_comment = Content::load( 'APP_DOC_COMMENT', '', $this->getAppDocUid(), (defined( 'SYS_LANG' ) ? SYS_LANG : 'en') ); //For backward compatibility
                }
            } catch (Exception $oError) {
                throw ($oError);
            }
        }
        return $this->app_doc_comment;
    }

    /**
     * Set the [app_doc_comment] column value.
     *
     * @param string $sValue new value
     * @return void
     */
    public function setAppDocCommentContent ($sValue)
    {
        if ($sValue !== null && ! is_string( $sValue )) {
            $sValue = (string) $sValue;
        }
        if (in_array(AppDocumentPeer::APP_DOC_COMMENT, $this->modifiedColumns) || $sValue === '') {
            try {
                $this->app_doc_comment = $sValue;
                $iResult = Content::addContent( 'APP_DOC_COMMENT', $this->getDocVersion(), $this->getAppDocUid(), (defined( 'SYS_LANG' ) ? SYS_LANG : 'en'), $this->app_doc_comment );
            } catch (Exception $oError) {
                $this->app_doc_comment = '';
                throw ($oError);
            }
        }
    }

    /**
     * Get the [app_doc_filename] column value.
     *
     * @return string
     */
    public function getAppDocFilenameContent ()
    {
        if ($this->app_doc_filename == '') {
            try {
                $this->app_doc_filename = Content::load( 'APP_DOC_FILENAME', $this->getDocVersion(), $this->getAppDocUid(), (defined( 'SYS_LANG' ) ? SYS_LANG : 'en') );
                if ($this->app_doc_filename == "") {
                    $this->app_doc_filename = Content::load( 'APP_DOC_FILENAME', '', $this->getAppDocUid(), (defined( 'SYS_LANG' ) ? SYS_LANG : 'en') ); //For backward compatibility
                }
            } catch (Exception $oError) {
                throw ($oError);
            }
        }
        return $this->app_doc_filename;
    }

    /**
     * Set the [app_doc_filename] column value.
     *
     * @param string $sValue new value
     * @return void
     */
    public function setAppDocFilenameContent ($sValue)
    {
        if ($sValue !== null && ! is_string( $sValue )) {
            $sValue = (string) $sValue;
        }
        if (in_array(AppDocumentPeer::APP_DOC_FILENAME, $this->modifiedColumns) || $sValue === '') {
            try {
                $this->app_doc_filename = $sValue;
                $iResult = Content::addContent( 'APP_DOC_FILENAME', $this->getDocVersion(), $this->getAppDocUid(), (defined( 'SYS_LANG' ) ? SYS_LANG : 'en'), $this->app_doc_filename );
            } catch (Exception $oError) {
                $this->app_doc_filename = '';
                throw ($oError);
            }
        }
    }

    public function isEmptyInContent ($content, $field, $lang)
    {
        if (isset( $content[$field][$lang] )) {
            if (trim( $content[$field][$lang] ) != '') {
                return false;
            }
        }
        ;
        return true;
    }

    /*----------------------------------********---------------------------------*/

    public function getObject ($APP_UID, $DEL_INDEX, $STEP_UID_OBJ, $APP_DOC_TYPE)
    {
        $oCriteria = new Criteria( 'workflow' );
        $oCriteria->add( AppDocumentPeer::APP_UID, $APP_UID );
        $oCriteria->add( AppDocumentPeer::DEL_INDEX, $DEL_INDEX );
        $oCriteria->add( AppDocumentPeer::DOC_UID, $STEP_UID_OBJ );
        $oCriteria->add( AppDocumentPeer::APP_DOC_TYPE, $APP_DOC_TYPE );
        $oDataset = AppDocumentPeer::doSelectRS( $oCriteria );
        $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
        $oDataset->next();

        return $oDataset->getRow();
    }

    /**
     * Get all docuemnts for a folder
     *
     * @param array $sFolderUid
     * @return array
     */
    public function getDocumentsinFolders ($sFolderUid)
    {
        $documents = array ();

        $oCriteria = new Criteria( 'workflow' );
        $oCriteria->add( AppDocumentPeer::FOLDER_UID, $sFolderUid );
        $oDataset = AppDocumentPeer::doSelectRS( $oCriteria );
        $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
        $oDataset->next();

        while ($aRow = $oDataset->getRow()) {
            $documents[] = array ('sAppDocUid' => $aRow['APP_DOC_UID'],'iVersion' => $aRow['DOC_VERSION']
            );
            $oDataset->next();
        }

        return $documents;
    }

    /**
     * This function check if exist a document
     * @param string $appDocUid, Uid of the document
     * @param integer $version,
     * @return object
    */
    public function exists ($appDocUid, $version = 1)
    {
        $oAppDocument = AppDocumentPeer::retrieveByPK($appDocUid, $version);
        return (is_object($oAppDocument) && get_class($oAppDocument) == 'AppDocument');
    }

    /**
     * The user that uploaded a document can download the same input file.
     * A participated user or a supervisor must have the process permission "view" to be able to download the input document.
     * If the user is a supervisor and had the input document assign, he can download the file too.
     * @param $user
     * @param $appDocUid
     * @param $version
     * @return bool
     */
    public function canDownloadInput($user, $appDocUid, $version)
    {
        //Check if the the requester is the owner in the file
        $oCriteria = new Criteria('workflow');
        $oCriteria->addSelectColumn(AppDocumentPeer::APP_UID);
        $oCriteria->addJoin(AppDocumentPeer::DOC_UID, InputDocumentPeer::INP_DOC_UID, Criteria::LEFT_JOIN);
        $oCriteria->add(AppDocumentPeer::USR_UID, $user);
        $oCriteria->add(AppDocumentPeer::APP_DOC_UID, $appDocUid);
        $oCriteria->add(AppDocumentPeer::DOC_VERSION, $version);
        $oCriteria->setLimit(1);
        $dataset = AppDocumentPeer::doSelectRS($oCriteria);
        $dataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $dataset->next();
        if ($dataset->getRow()) {
            return true;
        } else {
            //Review if is a INPUT or ATTACHED
            $oCriteria = new Criteria("workflow");
            $oCriteria->addSelectColumn(AppDocumentPeer::APP_UID);
            $oCriteria->addSelectColumn(AppDocumentPeer::DOC_UID);
            $oCriteria->addSelectColumn(AppDocumentPeer::APP_DOC_TYPE);
            $oCriteria->add(AppDocumentPeer::APP_DOC_UID, $appDocUid);
            $oCriteria->add(AppDocumentPeer::DOC_VERSION, $version);
            $oCriteria->setLimit(1);
            $dataset = AppDocumentPeer::doSelectRS($oCriteria);
            $dataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $dataset->next();
            $row = $dataset->getRow();
            if ($row['DOC_UID'] == '-1') {
                //If is an attached we only verify if is a supervisor in the process
                $appUid = $row['APP_UID'];
                $oApplication = new Application();
                $aColumns = $oApplication->Load($appUid);
                $cases = new \ProcessMaker\BusinessModel\Cases();
                $userAuthorization = $cases->userAuthorization(
                    $user,
                    $aColumns['PRO_UID'],
                    $appUid,
                    array(),
                    array('ATTACHMENTS' => 'VIEW')
                );
                //Has permissions?
                if (in_array($appDocUid, $userAuthorization['objectPermissions']['ATTACHMENTS'])) {
                    return true;
                }
                //Is supervisor?
                if ($userAuthorization['supervisor']) {
                    return true;
                }
            } else {
                //If is an file related an input document, we will check if the user is a supervisor or has permissions
                $appUid = $row['APP_UID'];
                $oInputDoc = new InputDocument();
                $aColumns = $oInputDoc->Load($row['DOC_UID']);
                $cases = new \ProcessMaker\BusinessModel\Cases();
                $userAuthorization = $cases->userAuthorization(
                    $user,
                    $aColumns['PRO_UID'],
                    $appUid,
                    array(),
                    array('INPUT_DOCUMENTS' => 'VIEW')
                );
                //Has permissions?
                if (in_array($appDocUid, $userAuthorization['objectPermissions']['INPUT_DOCUMENTS'])) {
                    return true;
                }
                //Is supervisor?
                if ($userAuthorization['supervisor']) {
                    //Review if the supervisor has assigned the object input document
                    $criteria = new Criteria("workflow");
                    $criteria->addSelectColumn(StepSupervisorPeer::STEP_UID);
                    $criteria->add(StepSupervisorPeer::STEP_TYPE_OBJ, "INPUT_DOCUMENT", \Criteria::EQUAL);
                    $criteria->add(StepSupervisorPeer::STEP_UID_OBJ, $row['DOC_UID'], \Criteria::EQUAL);
                    $rsCriteria = StepSupervisorPeer::doSelectRS($criteria);
                    if ($rsCriteria->next()) {
                        return true;
                    }
                }
            }
        }
        return false;
    }

    /**
     * Check if the user $userCanDownload can download the Output Document
     *
     * The user that generate the output document can download the same output document file
     * A participated user or a supervisor must have the process permission "view" to be able to download the output document
     * @param string $userGenerateDocument
     * @param string $userCanDownload
     * @param string $proUid
     * @param string $appUid
     * @param string $sAppDocUid
     * @return boolean
     */
    public function canDownloadOutput($userGenerateDocument, $userCanDownload, $proUid, $appUid, $sAppDocUid)
    {
        //Check if the user Logged was generate the document
        if ($userGenerateDocument !== $userCanDownload) {
            $objCase = new \ProcessMaker\BusinessModel\Cases();
            $aUserCanAccess = $objCase->userAuthorization(
                $userCanDownload,
                $proUid,
                $appUid,
                array(),
                array('OUTPUT_DOCUMENTS'=>'VIEW')
            );

            //If the user does not have the process permission can not download
            if (in_array($sAppDocUid, $aUserCanAccess['objectPermissions']['OUTPUT_DOCUMENTS'])) {
                return true;
            }
        } else {
            return true;
        }
        return false;
    }
}

