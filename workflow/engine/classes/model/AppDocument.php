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

require_once 'classes/model/om/BaseAppDocument.php';
require_once 'classes/model/Content.php';
require_once 'classes/model/InputDocument.php';

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

    /**
     * This value goes in the content table
     *
     * @var string
     */
    protected $app_doc_title = '';

    /**
     * This value goes in the content table
     *
     * @var string
     */
    protected $app_doc_comment = '';

    /**
     * This value goes in the content table
     *
     * @var string
     */
    protected $app_doc_filename = '';

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
                //optimized for speed
                $aContentFields = $oAppDocument->getContentFields();
                $aFields['APP_DOC_TITLE'] = $aContentFields['APP_DOC_TITLE'];
                $aFields['APP_DOC_COMMENT'] = $aContentFields['APP_DOC_COMMENT'];
                $aFields['APP_DOC_FILENAME'] = $aContentFields['APP_DOC_FILENAME'];

                $this->fromArray( $aFields, BasePeer::TYPE_FIELDNAME );
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

            $oAppDocument->fromArray( $aData, BasePeer::TYPE_FIELDNAME );
            $oAppDocument->setDocVersion( $docVersion );

            $oAppDocument->setAppDocUid( $sUID );
            $oAppDocument->setAppDocIndex( $this->getLastIndex( $oAppDocument->getAppUid() ) + 1 );
            if ($oAppDocument->validate()) {
                $oConnection->begin();
                if (isset( $aData['APP_DOC_TITLE'] )) {
                    $oAppDocument->setAppDocTitle( $aData['APP_DOC_TITLE'] );
                }
                if (isset( $aData['APP_DOC_COMMENT'] )) {
                    $oAppDocument->setAppDocComment( $aData['APP_DOC_COMMENT'] );
                }
                if (isset( $aData['APP_DOC_FILENAME'] )) {
                    $oAppDocument->setAppDocFilename( $aData['APP_DOC_FILENAME'] );
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
                $oAppDocument->fromArray( $aData, BasePeer::TYPE_FIELDNAME );
                if ($oAppDocument->validate()) {
                    $oConnection->begin();
                    if (isset( $aData['APP_DOC_TITLE'] )) {
                        $oAppDocument->setAppDocTitle( $aData['APP_DOC_TITLE'] );
                    }
                    if (isset( $aData['APP_DOC_COMMENT'] )) {
                        $oAppDocument->setAppDocComment( $aData['APP_DOC_COMMENT'] );
                    }
                    if (isset( $aData['APP_DOC_FILENAME'] )) {
                        $oAppDocument->setAppDocFilename( $aData['APP_DOC_FILENAME'] );
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
    public function getAppDocTitle ()
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
    public function setAppDocTitle ($sValue)
    {
        if ($sValue !== null && ! is_string( $sValue )) {
            $sValue = (string) $sValue;
        }
        if ($this->app_doc_title !== $sValue || $sValue === '') {
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
    public function getAppDocComment ()
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
    public function setAppDocComment ($sValue)
    {
        if ($sValue !== null && ! is_string( $sValue )) {
            $sValue = (string) $sValue;
        }
        if ($this->app_doc_comment !== $sValue || $sValue === '') {
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
    public function getAppDocFilename ()
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
    public function setAppDocFilename ($sValue)
    {
        if ($sValue !== null && ! is_string( $sValue )) {
            $sValue = (string) $sValue;
        }
        if ($this->app_doc_filename !== $sValue || $sValue === '') {
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

    public function updateInsertContent ($content, $field, $value)
    {
        if (isset( $content[$field]['en'] )) {
            //update
            $con = ContentPeer::retrieveByPK( $field, $this->getDocVersion(), $this->getAppDocUid(), 'en' );
            $con->setConValue( $value );
            if ($con->validate()) {
                $res = $con->save();
            }
        } else {
            //insert
            $con = new Content();
            $con->setConCategory( $field );
            $con->setConParent( $this->getDocVersion() );
            $con->setConId( $this->getAppDocUid() );
            $con->setConLang( 'en' );
            $con->setConValue( $value );
            if ($con->validate()) {
                $res = $con->save();
            }
        }
    }

    public function normalizeContent ($content, $field, $lang)
    {
        $value = '';
        //if the lang row is not empty, update in 'en' row and continue
        if (! $this->isEmptyInContent( $content, $field, $lang )) {
            //update/insert only if this lang is != 'en', with this always we will have an en row with last value
            $value = $content[$field][$lang];
            if ($lang != 'en') {
                $this->updateInsertContent( $content, $field, $value );
            }
        } else {
            //if the lang row is empty, and 'en' row is not empty return 'en' value
            if (! $this->isEmptyInContent( $content, $field, 'en' )) {
                $value = $content[$field]['en'];
            }

            //if the lang row is empty, and 'en' row is empty get value for 'other' row and update in 'en' row and continue
            if ($this->isEmptyInContent( $content, $field, 'en' )) {
                if (isset( $content[$field] ) && is_array( $content[$field] )) {
                    foreach ($content[$field] as $lan => $val) {
                        if (trim( $val ) != '') {
                            $value = $val;
                            if ($lan != 'en') {
                                $this->updateInsertContent( $content, $field, $value );
                                continue;
                            }
                        }
                    }
                } else {
                    $this->updateInsertContent( $content, $field, '' );
                }
            }
        }
        return $value;
    }

    /**
     * Get the [app_description] , [app_title] column values.
     *
     * @return array of string
     */
    public function getContentFields ()
    {
        if ($this->getAppDocUid() == '') {
            throw (new Exception( "Error in getContentFields, the APP_DOC_UID can't be blank" ));
        }
        $lang = defined( 'SYS_LANG' ) ? SYS_LANG : 'en';
        $c = new Criteria();
        $c->clearSelectColumns();
        $c->addSelectColumn( ContentPeer::CON_CATEGORY );
        $c->addSelectColumn( ContentPeer::CON_PARENT );
        $c->addSelectColumn( ContentPeer::CON_LANG );
        $c->addSelectColumn( ContentPeer::CON_VALUE );
        $c->add( ContentPeer::CON_ID, $this->getAppDocUid() );
        $c->add( ContentPeer::CON_PARENT, $this->getDocVersion() );
        $c->addAscendingOrderByColumn( 'CON_CATEGORY' );
        $c->addAscendingOrderByColumn( 'CON_LANG' );
        $rs = ContentPeer::doSelectRS( $c );
        $rs->setFetchmode( ResultSet::FETCHMODE_ASSOC );
        $rs->next();
        $content = array ();
        while ($row = $rs->getRow()) {
            $conCategory = $row['CON_CATEGORY'];
            $conLang = $row['CON_LANG'];
            if (! isset( $content[$conCategory] )) {
                $content[$conCategory] = array ();
            }
            if (! isset( $content[$conCategory][$conLang] )) {
                $content[$conCategory][$conLang] = array ();
            }
            $content[$conCategory][$conLang] = $row['CON_VALUE'];
            $rs->next();
            $row = $rs->getRow();
        }

        $res['APP_DOC_TITLE'] = $this->normalizeContent( $content, 'APP_DOC_TITLE', $lang );
        $res['APP_DOC_COMMENT'] = $this->normalizeContent( $content, 'APP_DOC_COMMENT', $lang );
        $res['APP_DOC_FILENAME'] = $this->normalizeContent( $content, 'APP_DOC_FILENAME', $lang );
        return $res;
    }

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
}

