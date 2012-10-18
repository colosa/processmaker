<?php
/**
 * dynaforms_Save_as.php
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

/*
  * dynaforms_Save_as.php
  * script that handles the save-as functionality of a dynaform
  * replicates the dynaform structure and content physical and in DB
  * also handles the complete copy of field-events related and
  * the html template if its required too.
  */
require_once ('classes/model/Dynaform.php');
if (! class_exists( "FieldCondition" )) {
    require_once "classes/model/FieldCondition.php";
}

try {
    $con = Propel::getConnection( DynaformPeer::DATABASE_NAME );
    $frm = $_POST['form'];
    $PRO_UID = $frm['PRO_UID'];
    $DYN_UID = $frm['DYN_UID'];
    $DYN_TYPE = $frm['DYN_TYPE'];

    // checks if there are conditions attached to the dynaform
    $oFieldCondition = new FieldCondition();
    $aConditions = $oFieldCondition->getAllByDynUid( $DYN_UID );

    $dynaform = new dynaform();
    /*Save Register*/

    $dynUid = (G::generateUniqueID());

    $dynaform->setDynUid( $dynUid );
    $dynaform->setProUid( $PRO_UID );
    $dynaform->setDynType( $DYN_TYPE );
    $dynaform->setDynFilename( $PRO_UID . PATH_SEP . $dynUid );

    $con->begin();
    $res = $dynaform->save();
    $dynaform->setDynTitle( $frm['DYN_TITLENEW'] );
    $dynaform->setDynDescription( (! $frm['DYN_DESCRIPTIONNEW']) ? 'Default Dynaform Description' : $frm['DYN_DESCRIPTIONNEW'] );

    //$con->commit();


    $hd = fopen( PATH_DYNAFORM . $PRO_UID . '/' . $DYN_UID . '.xml', "r" );
    $hd1 = fopen( PATH_DYNAFORM . $PRO_UID . '/' . $dynUid . '.xml', "w" );
    $templateFilename = PATH_DYNAFORM . $PRO_UID . '/' . $DYN_UID . '.html';

    // also make a copy of the template file in case that the html edition is enabled
    if (file_exists( $templateFilename )) {
        $templateHd = fopen( $templateFilename, "r" );
        $templateHd1 = fopen( PATH_DYNAFORM . $PRO_UID . '/' . $dynUid . '.html', "w" );
    }

    // also copy all the necessarily conditions if there are any
    foreach ($aConditions as $condition) {
        $condition['FCD_UID'] = (G::generateUniqueID());
        $condition['FCD_DYN_UID'] = $dynUid;
        $oFieldCondition->quickSave( $condition );
    }
    // checks if the physical dynaform file exists and copy the contents
    if ($hd) {
        while (! feof( $hd )) {
            $line = fgets( $hd, 4096 );
            fwrite( $hd1, str_replace( $DYN_UID, $dynUid, $line ) );
        }
    }

    fclose( $hd );
    fclose( $hd1 );

    // check if the template file also exists
    if (isset( $templateHd )) {
        while (! feof( $templateHd )) {
            $line = fgets( $templateHd, 4096 );
            fwrite( $templateHd1, str_replace( $DYN_UID, $dynUid, $line ) );
        }
        fclose( $templateHd );
        fclose( $templateHd1 );
    }

} catch (Exception $e) {
    return (array) $e;
}

