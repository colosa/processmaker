<?php
/**
 * languages_Import.php
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
require_once "classes/model/Language.php";

global $RBAC;
$access = $RBAC->userCanAccess( 'PM_SETUP_ADVANCE' );

if ($access != 1) {
    switch ($access) {
        case - 1:
            G::SendTemporalMessage( 'ID_USER_HAVENT_RIGHTS_PAGE', 'error', 'labels' );
            break;
        case - 2:
            G::SendTemporalMessage( 'ID_USER_HAVENT_RIGHTS_SYSTEM', 'error', 'labels' );
            break;
        default:
            G::SendTemporalMessage( 'ID_USER_HAVENT_RIGHTS_PAGE', 'error', 'labels' );
            break;
    }
    G::header( 'location: ../login/login' );
    exit( 0 );
}

$result = new stdClass();

try {
    //if the xmlform path is writeable
    if (! is_writable( PATH_XMLFORM ))
        throw new Exception( G::LoadTranslation( 'IMPORT_LANGUAGE_ERR_NO_WRITABLE' ) );

        //if all xml files within the xmlform directory are writeable
    if (! G::is_rwritable( PATH_XMLFORM ))
        throw new Exception( G::LoadTranslation( 'IMPORT_LANGUAGE_ERR_NO_WRITABLE2' ) );

    $sMaxExecutionTime = ini_get( 'max_execution_time' );
    ini_set( 'max_execution_time', '0' );
    G::LoadClass( 'configuration' );

    $languageFile = $_FILES['form']['tmp_name']['LANGUAGE_FILENAME'];
    $languageFilename = $_FILES['form']['name']['LANGUAGE_FILENAME'];

    if (substr_compare( $languageFilename, ".gz", - 3, 3, true ) == 0) {
        $zp = gzopen( $languageFile, "r" );
        $languageFile = tempnam( __FILE__, '' );
        $handle = fopen( $languageFile, "w" );
        while (! gzeof( $zp )) {
            $data = gzread( $zp, 1024 );
            fwrite( $handle, $data );
        }
        gzclose( $zp );
        fclose( $handle );
    }

    $language = new Language();
    $configuration = new Configurations();
    $importResults = $language->import( $languageFile );

    G::LoadClass( "wsTools" );
    $renegerateContent = new workspaceTools( SYS_SYS );
    $messs = $renegerateContent->upgradeContent();

    $result->msg = G::LoadTranslation( 'IMPORT_LANGUAGE_SUCCESS' ) . "\n";
    $result->msg .= G::LoadTranslation("ID_FILE_NUM_RECORD") . $importResults->recordsCount . "\n";
    $result->msg .= G::LoadTranslation("ID_SUCCESS_RECORD") . $importResults->recordsCountSuccess . "\n";
    $result->msg .= G::LoadTranslation("ID_FAILED_RECORD") . ($importResults->recordsCount - $importResults->recordsCountSuccess) . "\n";

    if ($importResults->errMsg != '') {
        $result->msg .= "Errors registered: \n" . $importResults->errMsg . "\n";
    }

    //$result->msg = htmlentities($result->msg);
    $result->success = true;

    //saving metadata
    $configuration->aConfig = Array ('headers' => $importResults->headers,'language' => $importResults->lang,'import-date' => date( 'Y-m-d H:i:s' ),'user' => '','version' => '1.0'
    );
    $configuration->saveConfig( 'LANGUAGE_META', $importResults->lang );

    $dir = PATH_CORE . 'content' . PATH_SEP . 'translations' . PATH_SEP;
    if (! is_writable( $dir )) {
        throw new Exception( G::LoadTranslation( 'ID_TRANSLATIONS_FOLDER_PERMISSIONS' ) );
    }
    G::uploadFile($languageFile, $dir, $languageFilename, 0777);

    ini_set( 'max_execution_time', $sMaxExecutionTime );

} catch (Exception $oError) {
    $result->msg = $oError->getMessage();
    //print_r($oError->getTrace());
    $result->success = false;
    //G::SendTemporalMessage($oError->getMessage(), 'error', 'string');
    //G::header('location: languages_ImportForm');
}
echo G::json_encode( $result );

