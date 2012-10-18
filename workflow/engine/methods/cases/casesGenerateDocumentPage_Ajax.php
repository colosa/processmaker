<?php
/**
 * casesGenerateDocumentPage_Ajax.php
 *
 * ProcessMaker Open Source Edition
 * Copyright (C) 2004 - 2008 Colosa Inc.
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
$actionAjax = isset( $_REQUEST['actionAjax'] ) ? $_REQUEST['actionAjax'] : null;

function casesShowOuputDocumentExist ($url)
{
    $urlArray = explode( "?", $url );
    $urlParametroString = $urlArray[1];

    parse_str( $urlParametroString, $_GET );

    require_once ("classes/model/AppDocumentPeer.php");

    $oAppDocument = new AppDocument();
    $oAppDocument->Fields = $oAppDocument->load( $_GET['a'], (isset( $_GET['v'] )) ? $_GET['v'] : NULL );

    $sAppDocUid = $oAppDocument->getAppDocUid();
    $info = pathinfo( $oAppDocument->getAppDocFilename() );
    if (! isset( $_GET['ext'] )) {
        $ext = $info['extension'];
    } else {
        if ($_GET['ext'] != '') {
            $ext = $_GET['ext'];
        } else {
            $ext = $info['extension'];
        }
    }
    $ver = (isset( $_GET['v'] ) && $_GET['v'] != '') ? '_' . $_GET['v'] : '';

    if (! $ver) //This code is in the case the outputdocument won't be versioned
        $ver = '_1';

    $realPath = PATH_DOCUMENT . $oAppDocument->Fields['APP_UID'] . '/outdocs/' . $sAppDocUid . $ver . '.' . $ext;
    $realPath1 = PATH_DOCUMENT . $oAppDocument->Fields['APP_UID'] . '/outdocs/' . $info['basename'] . $ver . '.' . $ext;
    $realPath2 = PATH_DOCUMENT . $oAppDocument->Fields['APP_UID'] . '/outdocs/' . $info['basename'] . '.' . $ext;
    $sw_file_exists = false;
    if (file_exists( $realPath )) {
        $sw_file_exists = true;
    } elseif (file_exists( $realPath1 )) {
        $sw_file_exists = true;
        $realPath = $realPath1;
    } elseif (file_exists( $realPath2 )) {
        $sw_file_exists = true;
        $realPath = $realPath2;
    }

    $swFileExist = 0;
    if ($sw_file_exists) {
        $swFileExist = 1;
    }
    return $swFileExist;
}

if ($actionAjax == 'casesGenerateDocumentPage') {
    global $G_PUBLISH;

    $oHeadPublisher = & headPublisher::getSingleton();
    G::loadClass( 'configuration' );
    $conf = new Configurations();
    $oHeadPublisher->addExtJsScript( 'cases/casesGenerateDocumentPage', true ); //adding a javascript file .js
    $oHeadPublisher->addContent( 'cases/casesGenerateDocumentPage' ); //adding a html file  .html.
    $oHeadPublisher->assign( 'pageSize', $conf->getEnvSetting( 'casesListRowNumber' ) );
    G::RenderPage( 'publish', 'extJs' );
}
if ($actionAjax == 'generateDocumentGrid_Ajax') {
    G::LoadClass( 'case' );
    G::LoadClass( "BasePeer" );

    global $G_PUBLISH;
    $oCase = new Cases();

    $aProcesses = Array ();

    $G_PUBLISH = new Publisher();
    $c = $oCase->getAllGeneratedDocumentsCriteria( $_SESSION['PROCESS'], $_SESSION['APPLICATION'], $_SESSION['TASK'], $_SESSION['USER_LOGGED'] );
    if ($c->getDbName() == 'dbarray') {
        $rs = ArrayBasePeer::doSelectRs( $c );
    } else {
        $rs = GulliverBasePeer::doSelectRs( $c );
    }

    $rs->setFetchmode( ResultSet::FETCHMODE_ASSOC );
    $rs->next();

    $totalCount = 0;
    for ($j = 0; $j < $rs->getRecordCount(); $j ++) {
        $result = $rs->getRow();

        $result["FILEDOCEXIST"] = casesShowOuputDocumentExist( $result["FILEDOC"] );
        $result["FILEPDFEXIST"] = casesShowOuputDocumentExist( $result["FILEPDF"] );

        $aProcesses[] = $result;

        $rs->next();
        $totalCount ++;
    }

    //!dateFormat
    G::LoadClass( 'configuration' );
    $conf = new Configurations();
    try {
        $generalConfCasesList = $conf->getConfiguration( 'ENVIRONMENT_SETTINGS', '' );
    } catch (Exception $e) {
        $generalConfCasesList = array ();
    }
    $dateFormat = "";
    if (isset( $generalConfCasesList['casesListDateFormat'] ) && ! empty( $generalConfCasesList['casesListDateFormat'] )) {
        $dateFormat = $generalConfCasesList['casesListDateFormat'];
    }

    $newDir = '/tmp/test/directory';
    $r = G::verifyPath( $newDir );
    $r->data = $aProcesses;
    $r->totalCount = $totalCount;
    $r->dataFormat = $dateFormat;
    echo G::json_encode( $r );
}

