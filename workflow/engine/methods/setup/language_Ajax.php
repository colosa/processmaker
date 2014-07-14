<?php
/**
 * language_Ajax.php
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

    G::LoadInclude( 'ajax' );
    if (isset( $_POST['form'] )) {
        $_POST = $_POST['form'];
    }
    $_POST['function'] = get_ajax_value( 'function' );
    switch ($_POST['function']) {
        case 'savePredetermined':
            require_once "classes/model/Translation.php";
            $tranlationsList = Translation::getTranslationEnvironments();
            g::pr( $tranlationsList );
            die();
            if (isset( $meta['LAN_ID'] ) && $meta['LAN_ID'] == $_POST['lang']) {
                echo 'The Setting was saved successfully!';
            } else {
                echo 'Some error occured while the setting was being save, try later please.';
            }
            break;
        case 'languagesList':
            require_once 'classes/model/Language.php';
            require_once 'classes/model/IsoCountry.php';
            require_once 'classes/model/Translation.php';
            G::loadClass( 'configuration' );

            $isoCountry = new isoCountry();
            $translationRow = new Translation();
            $response = new stdClass();
            $translationsEnvList = $translationRow->getTranslationEnvironments();
            //print_r($translationsEnvList); die;
            $i = 0;
            foreach ($translationsEnvList as $locale => $translationRow) {

                $COUNTRY_ID = $translationRow['IC_UID'];
                if ($COUNTRY_ID != '') {
                    $isoCountryRecord = $isoCountry->findById( strtoupper( $COUNTRY_ID ) );
                    $flag = strtolower( $isoCountryRecord['IC_UID'] );
                    $countryName = $translationRow['HEADERS']['X-Poedit-Country'];
                } else {
                    $flag = 'international';
                    $countryName = G::LoadTranslation( 'ID_INTERNATIONAL' );
                }

                G::LoadClass( 'configuration' );

                $conf = new Configurations();
                $confCasesList = $conf->getConfiguration( 'casesList', 'todo' );
                //echo date($confCasesList['dateformat'], '2010-01-01');


                if (isset( $confCasesList['dateformat'] )) {
                    $datetime = explode( ' ', $translationRow['DATE'] );

                    $date = explode( '-', $datetime[0] );
                    if (count( $datetime ) == 2)
                        $time = explode( ':', $datetime[1] );

                    if (count( $date ) == 3) {
                        if (count( $time ) >= 2) {
                            $DATE = date( $confCasesList['dateformat'], mktime( $time[0], $time[1], 0, $date[1], $date[2], $date[0] ) );
                        } else {
                            $DATE = date( $confCasesList['dateformat'], mktime( 0, 0, 0, $date[1], $date[2], $date[0] ) );
                        }
                    } else {
                        $DATE = $translationRow['DATE'];
                    }

                    $datetime = explode( ' ', $translationRow['HEADERS']['PO-Revision-Date'] );

                    $date = explode( '-', $datetime[0] );
                    if (count( $datetime ) == 2)
                        $time = explode( ':', $datetime[1] );

                    if (count( $date ) == 3) {
                        if (count( $time ) >= 2) {
                            $REV_DATE = date( $confCasesList['dateformat'], mktime( $time[0], substr( $time[1], 0, 2 ), 0, $date[1], $date[2], $date[0] ) );
                        } else {
                            $REV_DATE = date( $confCasesList['dateformat'], mktime( 0, 0, 0, $date[1], $date[2], $date[0] ) );
                        }
                    } else {
                        $REV_DATE = $translationRow['HEADERS']['PO-Revision-Date'];
                    }
                } else {
                    $DATE = $translationRow['DATE'];
                    $REV_DATE = $translationRow['HEADERS']['PO-Revision-Date'];
                }

                $languagesList[$i]['LAN_ID'] = $translationRow['LAN_ID'];
                $languagesList[$i]['LOCALE'] = $translationRow['LOCALE'];
                $languagesList[$i]['LAN_FLAG'] = $flag;
                $languagesList[$i]['NUM_RECORDS'] = $translationRow['NUM_RECORDS'];
                $languagesList[$i]['DATE'] = $DATE;
                $languagesList[$i]['LAN_NAME'] = $translationRow['HEADERS']['X-Poedit-Language'];
                $languagesList[$i]['COUNTRY_NAME'] = $countryName;
                $languagesList[$i]['TRANSLATOR'] = htmlentities( $translationRow['HEADERS']['Last-Translator'] );
                $languagesList[$i]['REV_DATE'] = $REV_DATE;
                $languagesList[$i]['VERSION'] = $translationRow['HEADERS']['Project-Id-Version'];

                $i ++;
            }
            $translationRow = new Translation();

            $response->data = $languagesList;

            print (G::json_encode( $response )) ;
            break;
        case 'delete':
            include_once 'classes/model/Translation.php';
            include_once 'classes/model/Content.php';
            $locale = $_POST['LOCALE'];
            $trn = new Translation();

            if (strpos( $locale, Translation::$localeSeparator ))
                list ($LAN_ID, $IC_UID) = explode( Translation::$localeSeparator, $locale );
            else {
                $LAN_ID = $locale;
                $LAN_ID = '';
            }

            $oCriteria = new Criteria( 'workflow' );
            //$oCriteria->addSelectColumn('COUNT('.ContentPeer::CON_CATEGORY.')');
            $oCriteria->addSelectColumn( ContentPeer::CON_CATEGORY );
            $oCriteria->addSelectColumn( ContentPeer::CON_VALUE );
            $oCriteria->add( ContentPeer::CON_LANG, $locale );
            $oCriteria->add( ContentPeer::CON_CATEGORY, 'APP_TITLE', Criteria::EQUAL );
            $oDataset = ContentPeer::doSelectRS( $oCriteria );

            $oDataset->next();
            $oContent = new Content();
            $aRow = $oDataset->getRow();

            $actualLanguage = $_ENV['REQUEST_URI'];
            $actualLanguage = explode("/", $actualLanguage);

            if($locale != "en"){ //Default Lengage 'en'
            	if($locale != $actualLanguage[2]){ //Current lenguage
            		//THERE IS NO ANY CASE STARTED FROM THES LANGUAGE
            		if ($aRow[0] == 0) { //so we can delete this language
            			try {
            				Content::removeLanguageContent( $locale );
            				$trn->removeTranslationEnvironment( $locale );
            				echo G::LoadTranslation( 'ID_LANGUAGE_DELETED_SUCCESSFULLY' );
            			} catch (Exception $e) {
            				echo $e->getMessage();
            			}
            		} else {
            			echo str_replace( '{0}', $aRow[0], G::LoadTranslation( 'ID_LANGUAGE_CANT_DELETE' ) );
            		}
            	} else {
            		echo str_replace( '{0}', $aRow[0], G::LoadTranslation( 'ID_LANGUAGE_CANT_DELETE_CURRENTLY' ) );
            	}
            } else {
            	echo str_replace( '{0}', $aRow[0], G::LoadTranslation( 'ID_LANGUAGE_CANT_DELETE_DEFAULT' ) );
            }
            break;
    }
} catch (Exception $oException) {
    die( $oException->getMessage() );
}

