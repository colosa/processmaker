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
    protected $dyn_title_content = '';

    /**
     * Get the [Dyn_title_content] column value.
     *
     * @return string
     */
    public function getDynTitleContent ()
    {
        if ($this->getDynUid() == '') {
            throw (new Exception( "Error in getDynTitle, the DYN_UID can't be blank" ));
        }
        $lang = defined( 'SYS_LANG' ) ? SYS_LANG : 'en';
        $this->dyn_title_content = Content::load( 'DYN_TITLE', '', $this->getDynUid(), $lang );
        return $this->dyn_title_content;
    }

    /**
     * Set the [Dyn_title] column value.
     *
     * @param string $v new value
     * @return void
     */
    public function setDynTitleContent ($v)
    {
        if ($this->getDynUid() == '') {
            throw (new Exception( "Error in setDynTitle, the DYN_UID can't be blank" ));
        }
        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && ! is_string( $v )) {
            $v = (string) $v;
        }

        if (in_array(DynaformPeer::DYN_TITLE, $this->modifiedColumns) || $v === '') {
            $this->dyn_title_content = $v;
            $lang = defined( 'SYS_LANG' ) ? SYS_LANG : 'en';

            $res = Content::addContent( 'DYN_TITLE', '', $this->getDynUid(), $lang, $this->dyn_title_content );
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
    public function getDynDescriptionContent ()
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
    public function setDynDescriptionContent ($v)
    {
        if ($this->getDynUid() == '') {
            throw (new Exception( "Error in setDynDescription, the DYN_UID can't be blank" ));
        }
        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && ! is_string( $v )) {
            $v = (string) $v;
        }

        if (in_array(DynaformPeer::DYN_DESCRIPTION, $this->modifiedColumns) || $v === '') {
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

    public function create ($aData, $pmTableUid='')
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
            $dynTitle = isset($aData['DYN_TITLE']) ? $aData['DYN_TITLE'] : 'Default Dynaform Title';
            $this->setDynTitle($dynTitle);
            $dynDescription = isset($aData['DYN_DESCRIPTION']) ? $aData['DYN_DESCRIPTION'] : 'Default Dynaform Description';
            $this->setDynDescription($dynDescription);
            $this->setProUid( $aData['PRO_UID'] );
            $this->setDynType( isset( $aData['DYN_TYPE'] ) ? $aData['DYN_TYPE'] : 'xmlform' );
            $this->setDynFilename( $aData['PRO_UID'] . PATH_SEP . $dynUid );
            $this->setDynUpdateDate( date("Y-m-d H:i:s"));

            if (isset($aData["DYN_CONTENT"])) {
                $this->setDynContent($aData["DYN_CONTENT"]);
            } else {
                $this->setDynContent(G::json_encode(array(
                            "name" => $aData["DYN_TITLE"],
                            "description" => $aData["DYN_DESCRIPTION"],
                            "items" => array(array(
                                    "type" => "form",
                                    "variable" => "",
                                    "var_uid" => "",
                                    "dataType" => "",
                                    "id" => $this->getDynUid(),
                                    "name" => $aData["DYN_TITLE"],
                                    "description" => $aData["DYN_DESCRIPTION"],
                                    "mode" => "edit",
                                    "script" => "",
                                    "language" => "en",
                                    "externalLibs" => "",
                                    "printable" => false,
                                    "items" => array(),
                                    "variables" => array()
                                )
                            )
                )));
            }
            if (isset($aData["DYN_LABEL"])) {
                $this->setDynLabel($aData["DYN_LABEL"]);
            }
            if (!isset($aData['DYN_VERSION'])) {
                $aData['DYN_VERSION'] = 0;
            }
            $this->setDynVersion( $aData['DYN_VERSION'] );
            if ($this->validate()) {
                $con->begin();
                $this->setDynTitleContent( $dynTitle );
                $this->setDynDescriptionContent( $dynDescription );
                $res = $this->save();
                $con->commit();
                
                //Add Audit Log
                $mode    = isset($aData['MODE'])? $aData['MODE'] : 'Determined by Fields';
                $description = "";
                if($pmTableUid!=''){
                  $pmTable = AdditionalTablesPeer::retrieveByPK( $pmTableUid );
                  $addTabName = $pmTable->getAddTabName();
                  $description = "Create from a PM Table: ".$addTabName.", ";
                }
                G::auditLog("CreateDynaform", $description."Dynaform Title: ".$aData['DYN_TITLE'].", Type: ".$aData['DYN_TYPE'].", Description: ".$aData['DYN_DESCRIPTION'].", Mode: ".$mode);
                
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
        $this->create( $aData , $pmTableUid);
        $aData['DYN_UID'] = $this->getDynUid();
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

        // Determines the engine to use
        // For a description of a table
        $sDataBase = 'database_' . strtolower( DB_ADAPTER );
        if (G::LoadSystemExist( $sDataBase )) {

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
        
        //update content if version is 2
        if ($this->getDynVersion() === 2) {
            $items = array();
            $variables = array();
            $res = $sth->executeQuery($sql, ResultSet::FETCHMODE_ASSOC);
            while ($res->next()) {
                //data type
                $type = "text";
                $dataType = explode('(', $res->get('Type'));
                error_log(print_r($dataType, true));
                switch ($dataType[0]) {
                    case 'bigint':
                        $type = 'text';
                        $dataType = 'integer';
                        break;
                    case 'int':
                        $type = 'text';
                        $dataType = 'integer';
                        break;
                    case 'smallint':
                        $type = 'text';
                        $dataType = 'integer';
                        break;
                    case 'tinyint':
                        $type = 'text';
                        $dataType = 'integer';
                        break;
                    case 'decimal':
                        $type = 'text';
                        $dataType = 'float';
                        break;
                    case 'double':
                        $type = 'text';
                        $dataType = 'float';
                        break;
                    case 'float':
                        $type = 'text';
                        $dataType = 'float';
                        break;
                    case 'datetime':
                        $type = 'datetime';
                        $dataType = 'datetime';
                        break;
                    case 'date':
                        $type = 'datetime';
                        $dataType = 'datetime';
                        break;
                    case 'time':
                        $type = 'datetime';
                        $dataType = 'datetime';
                        break;
                    case 'char':
                        $type = 'text';
                        $dataType = 'string';
                        break;
                    case 'varchar':
                        $type = 'text';
                        $dataType = 'string';
                        break;
                    case 'mediumtext':
                        $type = 'textarea';
                        $dataType = 'string';
                        break;
                    default:
                        $type = "text";
                        $dataType = 'string';
                        break;
                }
                
                //variables
                $arrayData = array(
                    "var_name" => $res->get('Field'),
                    "var_label" => $res->get('Field'),
                    "var_field_type" => $dataType,
                    "var_field_size" => 10,
                    "var_null" => 1,
                    "var_dbconnection" => "none",
                    "var_sql" => "",
                    "var_options_control" => "",
                    "var_default" => "",
                    "var_accepted_values" => Array()
                );
                $objVariable = new \ProcessMaker\BusinessModel\Variable();
                try {
                    $objVariable->existsName($this->getProUid(), $res->get('Field'));
                    $variable = $objVariable->create($this->getProUid(), $arrayData);
                } catch (\Exception $e) {
                    $data = $objVariable->getVariables($this->getProUid());
                    foreach ($data as $datavariable) {
                        if ($datavariable["var_name"] === $res->get('Field')) {
                            $variable = $datavariable;
                            break;
                        }
                    }
                }
                array_push($variables, $variable);

                array_push($items, array(
                    array(
                        "type" => $type,
                        "dataType" => $dataType, //$res->get('Type'),
                        "id" => $res->get('Field'),
                        "name" => $res->get('Field'),
                        "label" => $res->get('Field'),
                        "hint" => "",
                        "required" => false,
                        "defaultValue" => "",
                        "dependentFields" => array(),
                        "textTransform" => "none",
                        "validate" => "any",
                        "mask" => "",
                        "maxLength" => 1000,
                        "formula" => "",
                        "mode" => "parent",
                        "var_uid" => $variable["var_uid"],
                        "var_name" => $variable["var_name"],
                        "colSpan" => 12
                    )
                ));
            }
            //submit button
            array_push($items, array(
                array(
                    "type" => "submit",
                    "id" => "FormDesigner-" . \ProcessMaker\Util\Common::generateUID(),
                    "name" => "submit",
                    "label" => "submit",
                    "colSpan" => 12
                )
            ));
            $json = array(
                "name" => $this->getDynTitle(),
                "description" => $this->getDynDescription(),
                "items" => array(
                    array(
                        "type" => "form",
                        "id" => $this->getDynUid(),
                        "name" => $this->getDynTitle(),
                        "description" => $this->getDynDescription(),
                        "mode" => "edit",
                        "script" => "",
                        "items" => $items,
                        "variables" => $variables
                    )
                )
            );

            $aData = $this->Load($this->getDynUid());
            $aData["DYN_CONTENT"] = G::json_encode($json);
            $this->update($aData);
        }
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
                $oPro->setDynUpdateDate(date("Y-m-d H:i:s"));
                if ($oPro->validate()) {
                    if (isset( $aData['DYN_TITLE'] )) {
                        $oPro->setDynTitleContent( $aData['DYN_TITLE'] );
                    }
                    if (isset( $aData['DYN_DESCRIPTION'] )) {
                        $oPro->setDynDescriptionContent( $aData['DYN_DESCRIPTION'] );
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
                $title = $oPro->getDynTitle();
                $type  = $oPro->getDynType(); 
                $description = $oPro->getDynDescription();

                Content::removeContent( 'DYN_TITLE', '', $oPro->getDynUid() );
                Content::removeContent( 'DYN_DESCRIPTION', '', $oPro->getDynUid() );
                $iResult = $oPro->delete();
                
                //Add Audit Log
                G::auditLog("DeleteDynaform", "Dynaform Title: ".$title.", Type: ".$type.", Description: ".$description);

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

    public function verifyExistingName ($sName, $sProUid, $sDynUid = null)
    {
        $sNameDyanform = urldecode( $sName );
        $sProUid = urldecode( $sProUid );
        $oCriteria = new Criteria( 'workflow' );
        $oCriteria->addSelectColumn( DynaformPeer::DYN_UID );
        $oCriteria->add( DynaformPeer::PRO_UID, $sProUid );
        if (!is_null($sDynUid)) {
            $oCriteria->add(DynaformPeer::DYN_UID, $sDynUid);
        }
        $oCriteria->add( DynaformPeer::DYN_TITLE, $sNameDyanform );
        $oDataset = DynaformPeer::doSelectRS( $oCriteria );
        $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
        $oDataset->next();
        $aRow = $oDataset->getRow();
        return (!$aRow) ? true : false;
    }
    
    /**
     * verify if a dynaform is assigned some dynaform
     *
     * @param string $proUid the uid of the process
     * @param string $dynUid the uid of the dynaform
     *
     * @return array
     */
    public function verifyDynaformAssignDynaform ($dynUid, $proUid) 
    {
        $res = array();
        $oCriteria = new Criteria();
        $oCriteria->addSelectColumn(DynaformPeer::DYN_UID);
        $oCriteria->add(DynaformPeer::PRO_UID, $proUid);
        $oCriteria->add(DynaformPeer::DYN_UID, $dynUid, Criteria::NOT_EQUAL);
        $oCriteria->add(DynaformPeer::DYN_CONTENT, "%" . $dynUid . "%", Criteria::LIKE);
        $oDataset = DynaformPeer::doSelectRS($oCriteria);
        $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        while ($oDataset->next()) {
            $res[] = $oDataset->getRow();
        }
        return $res;
    }
}
