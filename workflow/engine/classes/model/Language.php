<?php
/**
 * Language.php
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

//require_once 'classes/model/om/BaseLanguage.php';

//require_once 'classes/model/Content.php';
//require_once 'classes/model/IsoCountry.php';
//require_once 'classes/model/Translation.php';

/**
 * Skeleton subclass for representing a row from the 'LANGUAGE' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements. This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package workflow.engine.classes.model
 */
class Language extends BaseLanguage
{

    public function load ($sLanUid)
    {
        try {
            $oRow = LanguagePeer::retrieveByPK( $sLanUid );
            if (! is_null( $oRow )) {
                $aFields = $oRow->toArray( BasePeer::TYPE_FIELDNAME );
                $this->fromArray( $aFields, BasePeer::TYPE_FIELDNAME );
                $this->setNew( false );
                return $aFields;
            } else {
                throw (new Exception( 'This row doesn\'t exist!' ));
            }
        } catch (Exception $oError) {
            throw ($oError);
        }
    }

    public function update ($aFields)
    {
        $oConnection = Propel::getConnection( LanguagePeer::DATABASE_NAME );
        try {
            $oConnection->begin();
            $this->load( $aFields['LAN_ID'] );
            $this->fromArray( $aFields, BasePeer::TYPE_FIELDNAME );
            if ($this->validate()) {
                $iResult = $this->save();
                $oConnection->commit();
                return $iResult;
            } else {
                $oConnection->rollback();
                throw (new Exception( 'Failed Validation in class ' . get_class( $this ) . '.' ));
            }
        } catch (Exception $e) {
            $oConnection->rollback();
            throw ($e);
        }
    }

    //SELECT LAN_ID, LAN_NAME FROM LANGUAGE WHERE LAN_ENABLED = '1' ORDER BY LAN_WEIGHT DESC
    public function getActiveLanguages ()
    {
        $oCriteria = new Criteria( 'workflow' );
        $oCriteria->addSelectColumn( LanguagePeer::LAN_ID );
        $oCriteria->addSelectColumn( LanguagePeer::LAN_NAME );
        $oCriteria->add( LanguagePeer::LAN_ENABLED, '1' );
        $oCriteria->addDescendingOrderByColumn( LanguagePeer::LAN_WEIGHT );

        $oDataset = ContentPeer::doSelectRS( $oCriteria );
        $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );

        $oContent = new Content();
        $rows = Array ();
        while ($oDataset->next()) {
            array_push( $rows, $oDataset->getRow() );
        }

        return $rows;
    }

    public function findById ($LAN_ID)
    {
        $oCriteria = new Criteria( 'workflow' );
        $oCriteria->addSelectColumn( LanguagePeer::LAN_NAME );
        $oCriteria->add( LanguagePeer::LAN_ID, $LAN_ID );
        $oDataset = LanguagePeer::doSelectRS( $oCriteria );
        $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
        $oDataset->next();
        return $oDataset->getRow();
    }

    public function findByLanName ($LAN_NAME)
    {
        $oCriteria = new Criteria( 'workflow' );
        $oCriteria->addSelectColumn( LanguagePeer::LAN_ID );
        $oCriteria->addSelectColumn( LanguagePeer::LAN_NAME );
        $oCriteria->add( LanguagePeer::LAN_NAME, $LAN_NAME, Criteria::LIKE );
        $oDataset = LanguagePeer::doSelectRS( $oCriteria );
        $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
        $oDataset->next();
        return $oDataset->getRow();
    }

    /*
     * Import a language file
     *
     * @author Erik Amaru Ortiz <erik@colosa.com, aortiz.erik@gmail>
     * @param string $sLanguageFile
     * @param string $bXml
     * @return void
     */
    public function import ($sLanguageFile, $updateXml = true, $updateDB = true)
    {
        try {
            G::LoadSystem( 'i18n_po' );
            $POFile = new i18n_PO( $sLanguageFile );
            $POFile->readInit();
            $POHeaders = $POFile->getHeaders();

            /*getting the PO Language definition*/
            $langName = $POHeaders['X-Poedit-Language'];
            //find the lang id
            $language = new Language();
            $langRecord = $language->findByLanName( $langName );

            if (! isset( $langRecord['LAN_ID'] )) {
                //if the language doesn't exist abort
                throw new Exception( 'The .po file has a invalid X-Poedit-Language definition!' );
            }

            $languageID = $langRecord['LAN_ID'];

            /*getting the PO Language definition*/
            $countryName = $POHeaders['X-Poedit-Country'];
            if ($countryName != '.') {
                $isoCountry = new IsoCountry();
                $countryRecord = $isoCountry->findByIcName( $countryName );

                if (! isset( $countryRecord['IC_UID'] )) {
                    //if the language doesn't exist abort
                    throw new Exception( 'The .po file has a invalid X-Poedit-Country definition!' );
                }

                $countryID = $countryRecord['IC_UID'];
                //define locale
                $LOCALE = "$languageID-$countryID";
            } else {
                $LOCALE = $languageID;
            }

            $oTranslation = new Translation();
            $countItems = 0;
            $countItemsSuccess = 0;
            $errorMsg = '';

            while ($rowTranslation = $POFile->getTranslation()) {
                $countItems ++;
                if (! isset( $POFile->translatorComments[0] ) || ! isset( $POFile->translatorComments[1] ) || ! isset( $POFile->references[0] )) {
                    throw new Exception( 'The .po file doesn\'t have valid directives for Processmaker!' );
                }

                foreach ($POFile->translatorComments as $a => $aux) {
                    $aux = trim( $aux );
                    if ($aux == 'TRANSLATION') {
                        $identifier = $aux;
                    } else {
                        $var = explode( '/', $aux );
                        if ($var[0] == 'LABEL') {
                            $context = $aux;
                        }
                        if ($var[0] == 'JAVASCRIPT') {
                            $context = $aux;
                        }
                    }
                    if (preg_match( '/^([\w-]+)\/([\w-]+\/*[\w-]*\.xml\?)/', $aux, $match )) {
                        $identifier = $aux;
                    } else {
                        if (preg_match( '/^([\w-]+)\/([\w-]+\/*[\w-]*\.xml$)/', $aux, $match )) {
                            $context = $aux;
                        }
                    }
                }

                $reference = $POFile->references[0];

                // it is a Sql insert on TRANSLATIONS TAble
                if ($identifier == 'TRANSLATION') {
                    if ($updateDB) {
                        list ($category, $id) = explode( '/', $context );
                        $result = $oTranslation->addTranslation( $category, $id, $LOCALE, trim( stripcslashes( str_replace( chr( 10 ), '', $rowTranslation['msgstr'] ) ) ) );
                        if ($result['codError'] == 0) {
                            $countItemsSuccess ++;
                        } else {
                            $errorMsg .= $id . ': ' . $result['message'] . "\n";
                        }
                    }
                }                 // is a Xml update
                elseif ($updateXml) {
                    $xmlForm = $context;
                    //erik: expresion to prevent and hable correctly dropdown values like -1, -2 etc.
                    preg_match( '/^([\w_]+)\s-\s([\w_]+)\s*-*\s*([\w\W]*)$/', $reference, $match );

                    if (! file_exists( PATH_XMLFORM . $xmlForm )) {
                        $errorMsg .= 'file doesn\'t exist: ' . PATH_XMLFORM . $xmlForm . "\n";
                        continue;
                    }

                    if (count( $match ) < 4) {
                        $near = isset( $rowTranslation['msgid'] ) ? $rowTranslation['msgid'] : (isset( $rowTranslation['msgstr'] ) ? $rowTranslation['msgstr'] : '');
                        $errorMsg .= "Invalid Translation reference: \"$reference\",  near -> " . strip_tags($near) . "\n";
                        continue;
                    }

                    G::LoadSystem( 'dynaformhandler' );
                    $dynaform = new dynaFormHandler( PATH_XMLFORM . $xmlForm );
                    $fieldName = $match[2];

                    $codes = explode( '-', $reference );

                    if (sizeof( $codes ) == 2) {
                        //is a normal node
                        $dynaform->addChilds( $fieldName, Array ($LOCALE => stripcslashes( str_replace( chr( 10 ), '', $rowTranslation['msgstr'] ) )
                        ) );
                    } elseif (sizeof( $codes ) > 2) {
                        //is a node child for a language node
                        $name = $match[3] == "''" ? '' : $match[3];
                        $childNode = Array (Array ('name' => 'option','value' => $rowTranslation['msgstr'],'attributes' => Array ('name' => $name
                        )
                        )
                        );

                        $dynaform->addChilds( $fieldName, Array ($LOCALE => null
                        ), $childNode );
                    }
                    $countItemsSuccess ++;
                }
            }

            $oLanguage = new Language();
            $oLanguage->update( array ('LAN_ID' => $languageID,'LAN_ENABLED' => '1'
            ) );

            $trn = new Translation();
            $trn->generateFileTranslation( $LOCALE );
            $trn->addTranslationEnvironment( $LOCALE, $POHeaders, $countItemsSuccess );

            //fill the results
            $results = new stdClass();
            $results->recordsCount = $countItems;
            $results->recordsCountSuccess = $countItemsSuccess;
            $results->lang = $languageID;
            $results->headers = $POHeaders;
            $results->errMsg = $errorMsg;

            return $results;
        } catch (Exception $oError) {
            throw ($oError);
        }
    }

    //export
    public function export ()
    {
        G::LoadSystem( 'i18n_po' );
        G::LoadClass( "system" );

        //creating the .po file
        $sPOFile = PATH_CORE . 'content' . PATH_SEP . 'translations' . PATH_SEP . MAIN_POFILE . '.' . $_GET['LOCALE'] . '.po';

        $poFile = new i18n_PO( $sPOFile );
        $poFile->buildInit();

        $language = new Language();

        $locale = $_GET['LOCALE'];
        $_TARGET_LANG = $_GET['LOCALE'];
        $_BASE_LANG = 'en';

        if (strpos( $locale, Translation::$localeSeparator ) !== false) {
            list ($LAN_ID, $IC_UID) = explode( Translation::$localeSeparator, $_GET['LOCALE'] );
            $iCountry = new IsoCountry();
            $iCountryRecord = $iCountry->findById( $IC_UID );

            if (! isset( $iCountryRecord['IC_UID'] )) {
                throw new Exception( "Country Target ID '{$_GET['LAN_ID']}' doesn't exist!" );
            }

            $sCountry = $iCountryRecord['IC_NAME'];
        } else {
            $LAN_ID = $locale;
            $sCountry = $IC_UID = '';
        }

        $langRecord = $language->findById( $LAN_ID );

        if (! isset( $langRecord['LAN_NAME'] )) {
            throw new Exception( "Language Target ID \"{$LAN_ID}\" doesn't exist!" );
        }

        $sLanguage = $langRecord['LAN_NAME'];

        //setting headers
        $poFile->addHeader( 'Project-Id-Version', 'ProcessMaker ' . System::getVersion() );
        $poFile->addHeader( 'POT-Creation-Date', '' );
        $poFile->addHeader( 'PO-Revision-Date', date( 'Y-m-d H:i:s' ) );
        $poFile->addHeader( 'Last-Translator', '' );
        $poFile->addHeader( 'Language-Team', 'Colosa Developers Team <developers@colosa.com>' );
        $poFile->addHeader( 'MIME-Version', '1.0' );
        $poFile->addHeader( 'Content-Type', 'text/plain; charset=utf-8' );
        $poFile->addHeader( 'Content-Transfer_Encoding', '8bit' );
        $poFile->addHeader( 'X-Poedit-Language', ucwords( $sLanguage ) );
        $poFile->addHeader( 'X-Poedit-Country', ucwords( $sCountry ) );
        $poFile->addHeader( 'X-Poedit-SourceCharset', 'utf-8' );
        $poFile->addHeader( 'Content-Transfer-Encoding', '8bit' );

        $aLabels = array ();
        $aMsgids = array ('' => true
        );

        // selecting all translations records of base language 'en' on TRANSLATIONS table
        $oCriteria = new Criteria( 'workflow' );
        $oCriteria->addSelectColumn( TranslationPeer::TRN_CATEGORY );
        $oCriteria->addSelectColumn( TranslationPeer::TRN_ID );
        $oCriteria->addSelectColumn( TranslationPeer::TRN_VALUE );
        $oCriteria->add( TranslationPeer::TRN_LANG, 'en' );
        $oDataset = TranslationPeer::doSelectRS( $oCriteria );
        $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );

        $targetLangRecords = array ();
        // retrieve the translation for the target language
        if ($LAN_ID != 'en') {
            // only if it is different language than base language 'en'
            $c = new Criteria( 'workflow' );
            $c->addSelectColumn( TranslationPeer::TRN_CATEGORY );
            $c->addSelectColumn( TranslationPeer::TRN_ID );
            $c->addSelectColumn( TranslationPeer::TRN_VALUE );
            $c->add( TranslationPeer::TRN_LANG, $_GET['LOCALE'] );
            $ds = TranslationPeer::doSelectRS( $c );
            $ds->setFetchmode( ResultSet::FETCHMODE_ASSOC );

            while ($ds->next()) {
                $row = $ds->getRow();
                $targetLangRecords[$row['TRN_CATEGORY'] . '/' . $row['TRN_ID']] = $row['TRN_VALUE'];
            }
        }

        // get the respective translation for each english label
        while ($oDataset->next()) {
            $aRow1 = $oDataset->getRow();
            $trnCategory = trim( $aRow1['TRN_CATEGORY'] );

            # Validation, validate that the TRN_CATEGORY contains valid characteres
            preg_match( "/^[0-9a-zA-Z_-]+/", $trnCategory, $sTestResult );

            // IF the translations id "TRN_ID" has invalid characteres or has not accepted categories
            if ($sTestResult[0] !== $trnCategory || ($trnCategory != 'LABEL' && $trnCategory != 'JAVASCRIPT')) {
                $oTranslation = new Translation();
                $oTranslation->remove( $aRow1['TRN_CATEGORY'], $aRow1['TRN_ID'], 'en' ); //remove not accepted translations
                continue; //jump to next iteration
            }

            // retrieve the translation for the target language
            if ($LAN_ID != 'en') {
                // only if it is different language than base language 'en'
                if (isset( $targetLangRecords[$aRow1['TRN_CATEGORY'] . '/' . $aRow1['TRN_ID']] )) {
                    $msgstr = $targetLangRecords[$aRow1['TRN_CATEGORY'] . '/' . $aRow1['TRN_ID']] != '' ? $targetLangRecords[$aRow1['TRN_CATEGORY'] . '/' . $aRow1['TRN_ID']] : $aRow1['TRN_VALUE'];
                } else {
                    $msgstr = $aRow1['TRN_VALUE'];
                }
            } else {
                //if not just copy the same
                $msgstr = $aRow1['TRN_VALUE'];
            }

            $msgid = trim( $aRow1['TRN_VALUE'] );
            $msgstr = trim( $msgstr );

            if (isset( $aMsgids[$msgid] )) {
                $msgid = '[' . $aRow1['TRN_CATEGORY'] . '/' . $aRow1['TRN_ID'] . '] ' . $msgid;
            }

            $poFile->addTranslatorComment( 'TRANSLATION' );
            $poFile->addTranslatorComment( $aRow1['TRN_CATEGORY'] . '/' . $aRow1['TRN_ID'] );
            $poFile->addReference( $aRow1['TRN_CATEGORY'] . '/' . $aRow1['TRN_ID'] );

            $poFile->addTranslation( stripcslashes( $msgid ), stripcslashes( $msgstr ) );
            $aMsgids[$msgid] = true;
        }

        //$timer->setMarker('end making 1th .po from db');


        //now find labels in xmlforms
        $aExceptionFields = array ('','javascript','hidden','phpvariable','private','toolbar','xmlmenu','toolbutton','cellmark','grid','CheckboxTable'
        );

        //find all xml files into PATH_XMLFORM
        $aXMLForms = glob( PATH_XMLFORM . '*/*.xml' );
        //from a sublevel to
        $aXMLForms2 = glob( PATH_XMLFORM . '*/*/*.xml' );
        $aXMLForms = array_merge( $aXMLForms, $aXMLForms2 );

        $aEnglishLabel = array ();
        $aOptions = array ();
        $nodesNames = Array ();

        G::loadSystem( 'dynaformhandler' );

        foreach ($aXMLForms as $xmlFormPath) {
            $xmlFormFile = str_replace( chr( 92 ), '/', $xmlFormPath );
            $xmlFormFile = str_replace( PATH_XMLFORM, '', $xmlFormPath );

            $dynaForm = new dynaFormHandler( $xmlFormPath );

            $dynaNodes = $dynaForm->getFields();

            //get all fields of each xmlform
            foreach ($dynaNodes as $oNode) {

                $sNodeName = $oNode->nodeName;
                //$arrayNode = $dynaForm->getArray($oNode, Array('type', $_BASE_LANG, $_BASE_LANG));
                $arrayNode = $dynaForm->getArray( $oNode );
                //if has not native language translation
                if (! isset( $arrayNode[$_BASE_LANG] ) || ! isset( $arrayNode['type'] ) || (isset( $arrayNode['type'] ) && in_array( $arrayNode['type'], $aExceptionFields ))) {
                    continue; //just continue with the next node
                }

                // Getting the Base Origin Text
                if (! is_array( $arrayNode[$_BASE_LANG] )) {
                    $originNodeText = trim( $arrayNode[$_BASE_LANG] );
                } else {
                    $langNode = $arrayNode[$_BASE_LANG][0];
                    $originNodeText = $langNode['__nodeText__'];
                }

                // Getting the Base Target Text
                if (isset( $arrayNode[$_TARGET_LANG] )) {
                    if (! is_array( $arrayNode[$_TARGET_LANG] )) {
                        $targetNodeText = trim( $arrayNode[$_TARGET_LANG] );
                    } else {
                        $langNode = $arrayNode[$_TARGET_LANG][0];
                        $targetNodeText = $langNode['__nodeText__'];
                    }
                } else {
                    $targetNodeText = $originNodeText;
                }

                $nodeName = $arrayNode['__nodeName__'];
                $nodeType = $arrayNode['type'];

                $msgid = $originNodeText;

                // if the nodeName already exists in the po file, we need to create other msgid
                if (isset( $aMsgids[$msgid] )) {
                    $msgid = '[' . $xmlFormFile . '?' . $nodeName . '] ' . $originNodeText;
                }
                $poFile->addTranslatorComment( $xmlFormFile . '?' . $nodeName );
                $poFile->addTranslatorComment( $xmlFormFile );
                $poFile->addReference( $nodeType . ' - ' . $nodeName );
                $poFile->addTranslation( stripslashes( $msgid ), stripslashes( $targetNodeText ) );

                $aMsgids[$msgid] = true;

                //if this node has options child nodes
                if (isset( $arrayNode[$_BASE_LANG] ) && isset( $arrayNode[$_BASE_LANG][0] ) && isset( $arrayNode[$_BASE_LANG][0]['option'] )) {

                    $originOptionNode = $arrayNode[$_BASE_LANG][0]['option']; //get the options


                    $targetOptionExists = false;
                    if (isset( $arrayNode[$_TARGET_LANG] ) && isset( $arrayNode[$_TARGET_LANG][0] ) && isset( $arrayNode[$_TARGET_LANG][0]['option'] )) {
                        $targetOptionNode = $arrayNode[$_TARGET_LANG][0]['option'];
                        $targetOptionExists = true;
                    }

                    if (! is_array( $originOptionNode )) {
                        if (is_string( $originOptionNode )) {
                            $poFile->addTranslatorComment( $xmlFormFile . '?' . $nodeName . '-' . $originOptionNode );
                            $poFile->addTranslatorComment( $xmlFormFile );
                            $poFile->addReference( $nodeType . ' - ' . $nodeName . ' - ' . $originOptionNode );
                            $poFile->addTranslation( stripslashes( $msgid ), stripslashes( $originOptionNode ) );
                        }
                    } else {
                        foreach ($originOptionNode as $optionNode) {
                            $optionName = $optionNode['name'];
                            $originOptionValue = $optionNode['__nodeText__'];

                            if ($targetOptionExists) {

                                $targetOptionValue = getMatchDropdownOptionValue( $optionName, $targetOptionNode );
                                if ($targetOptionValue === false) {
                                    $targetOptionValue = $originOptionValue;
                                }
                            } else {
                                $targetOptionValue = $originOptionValue;
                            }

                            $msgid = '[' . $xmlFormFile . '?' . $nodeName . '-' . $optionName . ']';
                            $poFile->addTranslatorComment( $xmlFormFile . '?' . $nodeName . '-' . $optionName );
                            $poFile->addTranslatorComment( $xmlFormFile );
                            $poFile->addReference( $nodeType . ' - ' . $nodeName . ' - ' . $optionName );
                            $poFile->addTranslation( $msgid, stripslashes( $targetOptionValue ) );
                        }
                    }
                }
            } //end foreach
        }
        G::streamFile( $sPOFile, true );
    }
}
// Language

function getMatchDropdownOptionValue ($name, $options)
{
    foreach ($options as $option) {
        if ($name == $option['name']) {
            return $option['__nodeText__'];
        }
    }
    return false;
}

