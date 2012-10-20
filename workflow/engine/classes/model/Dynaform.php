<?php
/**
 * Dynaform.php
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

require_once 'classes/model/om/BaseDynaform.php';
require_once 'classes/model/Content.php';
require_once ('classes/model/AdditionalTables.php');
G::LoadClass( 'dynaFormField' );

/**
 * Skeleton subclass for representing a row from the 'DYNAFORM' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements. This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package workflow.engine.classes.model
 */
class Dynaform extends BaseDynaform
{
    /**
     * This value goes in the content table
     *
     * @var string
     */
    protected $dyn_title = '';

    /**
     * Get the [Dyn_title] column value.
     *
     * @return string
     */
    public function getDynTitle ()
    {
        if ($this->getDynUid() == '') {
            throw (new Exception( "Error in getDynTitle, the DYN_UID can't be blank" ));
        }
        $lang = defined( 'SYS_LANG' ) ? SYS_LANG : 'en';
        $this->dyn_title = Content::load( 'DYN_TITLE', '', $this->getDynUid(), $lang );
        return $this->dyn_title;
    }

    /**
     * Set the [Dyn_title] column value.
     *
     * @param string $v new value
     * @return void
     */
    public function setDynTitle ($v)
    {
        if ($this->getDynUid() == '') {
            throw (new Exception( "Error in setDynTitle, the DYN_UID can't be blank" ));
        }
        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && ! is_string( $v )) {
            $v = (string) $v;
        }

        if ($this->dyn_title !== $v || $v === '') {
            $this->dyn_title = $v;
            $lang = defined( 'SYS_LANG' ) ? SYS_LANG : 'en';

            $res = Content::addContent( 'DYN_TITLE', '', $this->getDynUid(), $lang, $this->dyn_title );
        }

    } // set()


    /**
     * This value goes in the content table
     *
     * @var string
     */
    protected $dyn_description = '';

    /**
     * Get the [Dyn_description] column value.
     *
     * @return string
     */
    public function getDynDescription ()
    {
        if ($this->getDynUid() == '') {
            throw (new Exception( "Error in getDynDescription, the DYN_UID can't be blank" ));
        }
        $lang = defined( 'SYS_LANG' ) ? SYS_LANG : 'en';
        $this->dyn_description = Content::load( 'DYN_DESCRIPTION', '', $this->getDynUid(), $lang );
        return $this->dyn_description;
    }

    /**
     * Set the [Dyn_description] column value.
     *
     * @param string $v new value
     * @return void
     */
    public function setDynDescription ($v)
    {
        if ($this->getDynUid() == '') {
            throw (new Exception( "Error in setDynDescription, the DYN_UID can't be blank" ));
        }
        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && ! is_string( $v )) {
            $v = (string) $v;
        }

        if ($this->dyn_description !== $v || $v === '') {
            $this->dyn_description = $v;
            $lang = defined( 'SYS_LANG' ) ? SYS_LANG : 'en';

            $res = Content::addContent( 'DYN_DESCRIPTION', '', $this->getDynUid(), $lang, $this->dyn_description );
        }

    } // set()


    /**
     * Creates the Dynaform
     *
     * @param array $aData Fields with :
     * $aData['DYN_UID'] the dynaform id
     * $aData['USR_UID'] the userid
     * @return void
     */

    public function create ($aData)
    {
        if (! isset( $aData['PRO_UID'] )) {
            throw (new PropelException( 'The dynaform cannot be created. The PRO_UID is empty.' ));
        }
        $con = Propel::getConnection( DynaformPeer::DATABASE_NAME );
        try {
            if (isset( $aData['DYN_UID'] ) && $aData['DYN_UID'] == '') {
                unset( $aData['DYN_UID'] );
            }
            if (! isset( $aData['DYN_UID'] )) {
                $dynUid = (G::generateUniqueID());
            } else {
                $dynUid = $aData['DYN_UID'];
            }
            $this->setDynUid( $dynUid );
            $this->setProUid( $aData['PRO_UID'] );
            $this->setDynType( isset( $aData['DYN_TYPE'] ) ? $aData['DYN_TYPE'] : 'xmlform' );
            $this->setDynFilename( $aData['PRO_UID'] . PATH_SEP . $dynUid );

            if ($this->validate()) {
                $con->begin();
                $res = $this->save();

                if (isset( $aData['DYN_TITLE'] )) {
                    $this->setDynTitle( $aData['DYN_TITLE'] );
                } else {
                    $this->setDynTitle( 'Default Dynaform Title' );
                }

                if (isset( $aData['DYN_DESCRIPTION'] )) {
                    $this->setDynDescription( $aData['DYN_DESCRIPTION'] );
                } else {
                    $this->setDynDescription( 'Default Dynaform Description' );
                }

                $con->commit();
                $sXml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
                $sXml .= '<dynaForm type="' . $this->getDynType() . '" name="' . $this->getProUid() . '/' . $this->getDynUid() . '" width="500" enabletemplate="0" mode="" nextstepsave="prompt">' . "\n";
                $sXml .= '</dynaForm>';
                G::verifyPath( PATH_DYNAFORM . $this->getProUid(), true );
                $oFile = fopen( PATH_DYNAFORM . $this->getProUid() . '/' . $this->getDynUid() . '.xml', 'w' );
                fwrite( $oFile, $sXml );
                fclose( $oFile );
                return $this->getDynUid();
            } else {
                $msg = '';
                foreach ($this->getValidationFailures() as $objValidationFailure) {
                    $msg .= $objValidationFailure->getMessage() . "<br/>";
                }
                throw (new PropelException( 'The row cannot be created!', new PropelException( $msg ) ));
            }

        } catch (Exception $e) {
            $con->rollback();
            throw ($e);
        }
    }

    /**
     *
     *
     * Creates a Dynaform based on a PMTable
     *
     * @name createFromPMTable
     * @author gustavo cruz gustavo[at]colosa[dot]com
     * @param array $aData Fields with :
     * $aData['DYN_UID'] the dynaform id
     * $aData['USR_UID'] the userid
     * string $pmTableUid uid of the PMTable
     *
     */

    public function createFromPMTable ($aData, $pmTableUid)
    {
        $this->create( $aData );
        $aData['DYN_UID'] = $this->getDynUid();
        //krumo(BasePeer::getFieldnames('Content'));
        $fields = array ();
        //$oCriteria = new Criteria('workflow');
        $pmTable = AdditionalTablesPeer::retrieveByPK( $pmTableUid );
        $addTabName = $pmTable->getAddTabName();
        $keys = '';
        if (isset( $aData['FIELDS'] )) {
            foreach ($aData['FIELDS'] as $iRow => $row) {
                if ($keys != '') {
                    $keys = $keys . '|' . $row['PRO_VARIABLE'];
                } else {
                    $keys = $row['PRO_VARIABLE'];
                }
            }
        } else {
            $keys = ' ';
        }

        //      $addTabKeys = $pmTable->getAddTabDynavars();
        //      $addTabKeys = unserialize($addTabKeys);
        //      $keys = '';
        //      foreach ( $addTabKeys as $addTabKey ){
        //        if (trim($addTabKey['CASE_VARIABLE'])!=''&&$keys!=''){
        //            $keys = $keys.'|'.$addTabKey['CASE_VARIABLE'];
        //        } else {
        //            $keys = $addTabKey['CASE_VARIABLE'];
        //        }
        //
        //      }


        // Determines the engine to use
        // For a description of a table
        $sDataBase = 'database_' . strtolower( DB_ADAPTER );
        if (G::LoadSystemExist( $sDataBase )) {
            G::LoadSystem( $sDataBase );
            $oDataBase = new database();
            $sql = $oDataBase->getTableDescription( $addTabName );
        } else {
            $sql = 'DESC ' . $addTabName;
        }

        $dbh = Propel::getConnection( AdditionalTablesPeer::DATABASE_NAME );
        $sth = $dbh->createStatement();
        $res = $sth->executeQuery( $sql, ResultSet::FETCHMODE_ASSOC );

        $file = $aData['PRO_UID'] . '/' . $aData['DYN_UID'];
        $dbc = new DBConnection( PATH_DYNAFORM . $file . '.xml', '', '', '', 'myxml' );
        $ses = new DBSession( $dbc );
        $fieldXML = new DynaFormField( $dbc );

        $pmConnectionName = $addTabName . '_CONNECTION';

        if ($aData['DYN_TYPE'] == 'xmlform') {
            $labels = array ();
            $options = array ();
            $attributes = array ('XMLNODE_NAME_OLD' => '','XMLNODE_NAME' => $pmConnectionName,'TYPE' => 'pmconnection','PMTABLE' => $pmTableUid,'KEYS' => $keys
            );
            $fieldXML->Save( $attributes, $labels, $options );
        }

        $keyRequered = '';
        $countKeys = 0;
        while ($res->next()) {
            if ($res->get( 'Key' ) != '') {
                $countKeys ++;
            }
            if ($res->get( 'Extra' ) == 'auto_increment') {
                $keyRequered .= $res->get( 'Field' );
            }
        }

        $dbh = Propel::getConnection( AdditionalTablesPeer::DATABASE_NAME );
        $sth = $dbh->createStatement();
        $res = $sth->executeQuery( $sql, ResultSet::FETCHMODE_ASSOC );

        while ($res->next()) {
            // if(strtoupper($res->get('Null'))=='NO') {
            if (strtoupper( $res->get( $oDataBase->getFieldNull() ) ) == 'NO') {
                if ($countKeys == 1 && $res->get( 'Field' ) == $keyRequered) {
                    $required = '0';
                } else {
                    $required = '1';
                }
            } else {
                $required = '0';
            }
            $fieldName = $res->get( 'Field' );
            $defaultValue = $res->get( 'Default' );
            $labels = array (SYS_LANG => $fieldName
            );
            $options = array ();
            $type = explode( '(', $res->get( 'Type' ) );

            switch ($type[0]) {
                case 'text':
                    $type = 'textarea';
                    break;
                case 'date':
                    $type = 'date';
                    break;
                default:
                    $type = 'text';
                    break;
            }
            if ($aData['DYN_TYPE'] == 'xmlform') {
                $attributes = array ('XMLNODE_NAME_OLD' => '','XMLNODE_NAME' => $fieldName,'TYPE' => $type,'PMCONNECTION' => $pmConnectionName,'PMFIELD' => $fieldName,'REQUIRED' => $required,'DEFAULTVALUE' => $defaultValue
                );
            } else {
                $attributes = array ('XMLNODE_NAME_OLD' => '','XMLNODE_NAME' => $fieldName,'TYPE' => $type,'REQUIRED' => $required,'DEFAULTVALUE' => $defaultValue
                );
            }
            $fieldXML->Save( $attributes, $labels, $options );
        }
        $labels = array (SYS_LANG => 'Submit'
        );
        $attributes = array ('XMLNODE_NAME_OLD' => '','XMLNODE_NAME' => 'SUBMIT','TYPE' => 'submit'
        );
        $fieldXML->Save( $attributes, $labels, $options );
    }

    /**
     * Load the Dynaform row specified in [dyn_id] column value.
     *
     * @param string $ProUid the uid of the Prolication
     * @return array $Fields the fields
     */

    public function Load ($ProUid)
    {
        $con = Propel::getConnection( DynaformPeer::DATABASE_NAME );
        try {
            $oPro = DynaformPeer::retrieveByPk( $ProUid );
            if (is_object( $oPro ) && get_class( $oPro ) == 'Dynaform') {
                $aFields = $oPro->toArray( BasePeer::TYPE_FIELDNAME );
                $this->fromArray( $aFields, BasePeer::TYPE_FIELDNAME );
                $aFields['DYN_TITLE'] = $oPro->getDynTitle();
                $aFields['DYN_DESCRIPTION'] = $oPro->getDynDescription();
                $this->setDynTitle( $oPro->getDynTitle() );
                $this->setDynDescription( $oPro->getDynDescription() );
                return $aFields;
            } else {
                throw (new Exception( "The row '$ProUid' in table Dynaform doesn't exist!" ));
            }
        } catch (Exception $oError) {
            throw ($oError);
        }
    }

    /**
     * Update the Prolication row
     *
     * @param array $aData
     * @return variant
     *
     */

    public function update ($aData)
    {
        $con = Propel::getConnection( DynaformPeer::DATABASE_NAME );
        try {
            $con->begin();
            $oPro = DynaformPeer::retrieveByPK( $aData['DYN_UID'] );
            if (is_object( $oPro ) && get_class( $oPro ) == 'Dynaform') {
                $oPro->fromArray( $aData, BasePeer::TYPE_FIELDNAME );
                if ($oPro->validate()) {
                    if (isset( $aData['DYN_TITLE'] )) {
                        $oPro->setDynTitle( $aData['DYN_TITLE'] );
                    }
                    if (isset( $aData['DYN_DESCRIPTION'] )) {
                        $oPro->setDynDescription( $aData['DYN_DESCRIPTION'] );
                    }
                    $res = $oPro->save();
                    $con->commit();
                    return $res;
                } else {
                    foreach ($this->getValidationFailures() as $objValidationFailure) {
                        $msg .= $objValidationFailure->getMessage() . "<br/>";
                    }
                    throw (new PropelException( 'The row cannot be created!', new PropelException( $msg ) ));
                }
            } else {
                $con->rollback();
                throw (new Exception( "The row '" . $aData['DYN_UID'] . "' in table Dynaform doesn't exist!" ));
            }
        } catch (Exception $oError) {
            throw ($oError);
        }
    }

    /**
     * Remove the Prolication document registry
     *
     * @param array $aData or string $ProUid
     * @return string
     *
     */
    public function remove ($ProUid)
    {
        if (is_array( $ProUid )) {
            $ProUid = (isset( $ProUid['DYN_UID'] ) ? $ProUid['DYN_UID'] : '');
        }
        try {
            $oPro = DynaformPeer::retrieveByPK( $ProUid );
            if (! is_null( $oPro )) {
                Content::removeContent( 'DYN_TITLE', '', $oPro->getDynUid() );
                Content::removeContent( 'DYN_DESCRIPTION', '', $oPro->getDynUid() );
                $iResult = $oPro->delete();
                if (file_exists( PATH_DYNAFORM . $oPro->getProUid() . PATH_SEP . $oPro->getDynUid() . '.xml' )) {
                    unlink( PATH_DYNAFORM . $oPro->getProUid() . PATH_SEP . $oPro->getDynUid() . '.xml' );
                }
                if (file_exists( PATH_DYNAFORM . $oPro->getProUid() . PATH_SEP . $oPro->getDynUid() . '_tmp0.xml' )) {
                    unlink( PATH_DYNAFORM . $oPro->getProUid() . PATH_SEP . $oPro->getDynUid() . '_tmp0.xml' );
                }
                if (file_exists( PATH_DYNAFORM . $oPro->getProUid() . PATH_SEP . $oPro->getDynUid() . '.html' )) {
                    unlink( PATH_DYNAFORM . $oPro->getProUid() . PATH_SEP . $oPro->getDynUid() . '.html' );
                }
                if (file_exists( PATH_DYNAFORM . $oPro->getProUid() . PATH_SEP . $oPro->getDynUid() . '_tmp0.html' )) {
                    unlink( PATH_DYNAFORM . $oPro->getProUid() . PATH_SEP . $oPro->getDynUid() . '_tmp0.html' );
                }
                return $iResult;
            } else {
                throw (new Exception( "The row '$ProUid' in table Dynaform doesn't exist!" ));
            }
        } catch (Exception $oError) {
            throw ($oError);
        }
    }

    public function exists ($DynUid)
    {
        $oPro = DynaformPeer::retrieveByPk( $DynUid );
        return (is_object( $oPro ) && get_class( $oPro ) == 'Dynaform');
    }

    /**
     * verify if Dynaform row specified in [DynUid] exists.
     *
     * @param string $sProUid the uid of the Prolication
     */

    public function dynaformExists ($DynUid)
    {
        $con = Propel::getConnection( TaskPeer::DATABASE_NAME );
        try {
            $oDyn = DynaformPeer::retrieveByPk( $DynUid );
            if (is_object( $oDyn ) && get_class( $oDyn ) == 'Dynaform') {
                return true;
            } else {
                return false;
            }
        } catch (Exception $oError) {
            throw ($oError);
        }
    }

    public function getDynaformContent ($dynaformUid)
    {
        $content = '';
        $fields = $this->Load( $dynaformUid );
        $filename = PATH_DYNAFORM . $fields['PRO_UID'] . PATH_SEP . $fields['DYN_UID'] . '.xml';
        if (file_exists( $filename )) {
            $content = file_get_contents( $filename );
        }

        return $content;
    }

    public function getDynaformFields ($dynaformUid)
    {
        $content = '';
        $fields = $this->Load( $dynaformUid );
        $filename = PATH_DYNAFORM . $fields['PRO_UID'] . PATH_SEP . $fields['DYN_UID'] . '.xml';
        if (file_exists( $filename )) {
            $content = file_get_contents( $filename );
        }

        $G_FORM = new xmlform( $fields['DYN_FILENAME'], PATH_DYNAFORM );
        $G_FORM->parseFile( $filename, SYS_LANG, true );

        return $G_FORM->fields;
    }

    public function verifyExistingName ($sName, $sProUid)
    {
        $sNameDyanform = urldecode( $sName );
        $sProUid = urldecode( $sProUid );
        $oCriteria = new Criteria( 'workflow' );
        $oCriteria->addSelectColumn( DynaformPeer::DYN_UID );
        $oCriteria->add( DynaformPeer::PRO_UID, $sProUid );
        $oDataset = DynaformPeer::doSelectRS( $oCriteria );
        $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
        $flag = true;
        while ($oDataset->next() && $flag) {
            $aRow = $oDataset->getRow();
            $oCriteria1 = new Criteria( 'workflow' );
            $oCriteria1->addSelectColumn( 'COUNT(*) AS DYNAFORMS' );
            $oCriteria1->add( ContentPeer::CON_CATEGORY, 'DYN_TITLE' );
            $oCriteria1->add( ContentPeer::CON_ID, $aRow['DYN_UID'] );
            $oCriteria1->add( ContentPeer::CON_VALUE, $sNameDyanform );
            $oCriteria1->add( ContentPeer::CON_LANG, SYS_LANG );
            $oDataset1 = ContentPeer::doSelectRS( $oCriteria1 );
            $oDataset1->setFetchmode( ResultSet::FETCHMODE_ASSOC );
            $oDataset1->next();
            $aRow1 = $oDataset1->getRow();
            if ($aRow1['DYNAFORMS']) {
                $flag = false;
                break;
            }
        }
        return $flag;
    }
}

