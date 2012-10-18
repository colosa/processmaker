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

    if (isset( $_POST['function'] )) {
        $sfunction = $_POST['function'];
    } else {
        $sfunction = $_POST['functions'];
    }

    switch ($sfunction) {
        case 'lookForNameInput':
            require_once ('classes/model/Content.php');
            require_once ("classes/model/InputDocument.php");

            $snameInput = urldecode( $_POST['NAMEINPUT'] );
            $sPRO_UID = urldecode( $_POST['proUid'] );

            $oCriteria = new Criteria( 'workflow' );
            $oCriteria->addSelectColumn( InputDocumentPeer::INP_DOC_UID );
            $oCriteria->add( InputDocumentPeer::PRO_UID, $sPRO_UID );
            $oDataset = InputDocumentPeer::doSelectRS( $oCriteria );
            $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
            $flag = true;
            while ($oDataset->next() && $flag) {
                $aRow = $oDataset->getRow();

                $oCriteria1 = new Criteria( 'workflow' );
                $oCriteria1->addSelectColumn( 'COUNT(*) AS INPUTS' );
                $oCriteria1->add( ContentPeer::CON_CATEGORY, 'INP_DOC_TITLE' );
                $oCriteria1->add( ContentPeer::CON_ID, $aRow['INP_DOC_UID'] );
                $oCriteria1->add( ContentPeer::CON_VALUE, $snameInput );
                $oCriteria1->add( ContentPeer::CON_LANG, SYS_LANG );
                $oDataset1 = ContentPeer::doSelectRS( $oCriteria1 );
                $oDataset1->setFetchmode( ResultSet::FETCHMODE_ASSOC );
                $oDataset1->next();
                $aRow1 = $oDataset1->getRow();

                if ($aRow1['INPUTS']) {
                    $flag = false;
                }
            }
            print $flag;
            break;
        default:
            require_once 'classes/model/InputDocument.php';
            G::LoadClass( 'processMap' );

            $oInputDocument = new InputDocument();
            if (isset( $_POST['form'] )) {
                $aData = $_POST['form'];
            } else {
                $aData = $_POST;
            }

            if ($aData['INP_DOC_UID'] == '') {
                unset( $aData['INP_DOC_UID'] );
                $oInputDocument->create( $aData );
            } else {
                $oInputDocument->update( $aData );
            }

            //refresh dbarray with the last change in inputDocument
            $oMap = new processMap();
            $oCriteria = $oMap->getInputDocumentsCriteria( $aData['PRO_UID'] );
            break;
    }
} catch (Exception $oException) {
    die( $oException->getMessage() );
}

