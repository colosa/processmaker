<?php
/**
 * inputdocs_Save.php
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
    global $RBAC;
    switch ($RBAC->userCanAccess( 'PM_FACTORY' )) {
        case - 2:
            G::SendTemporalMessage( 'ID_USER_HAVENT_RIGHTS_SYSTEM', 'error', 'labels' );
            G::header( 'location: ../login/login' );
            die();
            break;
        case - 1:
            G::SendTemporalMessage( 'ID_USER_HAVENT_RIGHTS_PAGE', 'error', 'labels' );
            G::header( 'location: ../login/login' );
            die();
            break;
    }

    $sfunction = '';
    if (isset( $_POST['function'] )) {
        $sfunction = $_POST['function'];
    } elseif (isset( $_POST['functions'] )){
        $sfunction = $_POST['functions'];
    } 
    // Bootstrap::mylog("post:".$_POST['function']);
    switch ($sfunction) {
        case 'lookForNameInput':
            $snameInput = urldecode( $_POST['NAMEINPUT'] );
            $sPRO_UID = urldecode( $_POST['proUid'] );

            $oCriteria = new Criteria( 'workflow' );
            $oCriteria->addSelectColumn( InputDocumentPeer::INP_DOC_UID );
            $oCriteria->add( InputDocumentPeer::PRO_UID, $sPRO_UID );
            $oCriteria->add( InputDocumentPeer::INP_DOC_TITLE, $snameInput );
            $oDataset = InputDocumentPeer::doSelectRS( $oCriteria );
            $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
            $flag = true;
            $oDataset->next();
            $aRow = $oDataset->getRow();
            if ($aRow) {
                $flag = false;
            }
            print $flag;
            break;
        default:

            $oInputDocument = new InputDocument();
            if (isset( $_POST['form'] )) {
                $aData = $_POST['form'];
            } else {
                $aData = $_POST;
            }

            //Validating the format of the allowed extentions
            //Allowed Types has to have this format -> *.pdf, .xls, *.docx, *.*  or .* to all.
            $allowedTypes = explode(", ", $aData['INP_DOC_TYPE_FILE']);
            foreach ($allowedTypes as $types => $val) {
            	if ((preg_match("/^.*\.?[a-zA-Z0-9]{2,15}$/", $val)) || ($val == "*.*") || ($val == ".*")) {
            	}else {
            		$message = G::LoadTranslation( 'ID_UPLOAD_ERR_WRONG_ALLOWED_EXTENSION_FORMAT' );
            		G::SendMessageText( $message, "ERROR" );
            		die();
            	}
            }

            if ($aData['INP_DOC_UID'] == '') {
                unset( $aData['INP_DOC_UID'] );
                $oInputDocument->create( $aData );
            } else {
                $oInputDocument->update( $aData );
            }

            //refresh dbarray with the last change in inputDocument
            $oMap = new ProcessMap();
            $oCriteria = $oMap->getInputDocumentsCriteria( $aData['PRO_UID'] );
            break;
    }
} catch (Exception $oException) {
    $token = strtotime("now");
    PMException::registerErrorLog($oException, $token);
    G::outRes( G::LoadTranslation("ID_EXCEPTION_LOG_INTERFAZ", array($token)) );
    die;
}

