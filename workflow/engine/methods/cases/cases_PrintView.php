<?php
/**
 * Cases_PrintPreview.php
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

    require_once 'classes/model/Dynaform.php';
    require_once 'classes/model/Process.php';

    $currentDynUid = '';
    if (isset( $_GET['DYNUIDPRINT'] ) && $_GET['DYNUIDPRINT'] != '') {
        $currentDynUid = $_GET['DYNUIDPRINT'];
    } elseif (isset( $_SESSION['DYN_UID_PRINT'] )) {
        $currentDynUid = $_SESSION['DYN_UID_PRINT'];
    } elseif (isset( $_SESSION['CURRENT_DYN_UID'] )) {
        $currentDynUid = $_SESSION['CURRENT_DYN_UID'];
    }

    $oDynaform = new Dynaform();
    $aDyn = $oDynaform->load( $currentDynUid );

    G::LoadClass( 'case' );
    $oCase = new Cases();

    if (isset( $_SESSION['APPLICATION'] )) {
        $Fields = $oCase->loadCase( $_SESSION['APPLICATION'] );
        $Fields['APP_DATA'] = array_merge( $Fields['APP_DATA'], G::getSystemConstants() );
    } else {
        $Fields['APP_DATA'] = Array ();
    }

    $G_MAIN_MENU = 'processmaker';
    $G_ID_MENU_SELECTED = 'USERS';
    $G_PUBLISH = new Publisher();

    $idProcess = (isset( $Fields['APP_DATA']['PROCESS'] )) ? $Fields['APP_DATA']['PROCESS'] : $_SESSION['PROCESS'];
    $oProcess = new Process();
    $aProcessFieds = $oProcess->load( $idProcess );
    $sProcess = $oProcess->getProTitle();

    $noShowTitle = 0;
    if (isset( $aProcessFieds['PRO_SHOW_MESSAGE'] )) {
        $noShowTitle = $aProcessFieds['PRO_SHOW_MESSAGE'];
    }
    //Add content content step - Start
    if (isset( $_SESSION['APPLICATION'] )) {
        $array['CASE'] = G::LoadTranslation( 'ID_CASE' );
        $array['USER'] = G::LoadTranslation( 'ID_USER' );
        $array['WORKSPACE'] = G::LoadTranslation( 'ID_WORKSPACE' );
        $array['APP_NUMBER'] = $Fields['APP_NUMBER'];
        $array['APP_TITLE'] = $Fields['TITLE'];
        $array['USR_USERNAME'] = $Fields['APP_DATA']['USR_USERNAME'];
        $array['USER_ENV'] = $Fields['APP_DATA']['SYS_SYS'];
        $array['DATEPRINT'] = date( 'Y-m-d H:m:s' );
    }
    $array['APP_PROCESS'] = $sProcess;

    if (isset( $Fields['TITLE'] ) && strlen( $Fields['TITLE'] ) > 0) {
        $array['TITLE'] = G::LoadTranslation( 'ID_TITLE' );
    } else {
        $array['TITLE'] = '';
    }
    $array['PROCESS'] = G::LoadTranslation( 'ID_PROCESS' );
    $array['DATELABEL'] = G::LoadTranslation( 'DATE_LABEL' );

    $aDyn['DYN_UID'] = $currentDynUid;
    if ($noShowTitle == 0) {
        $G_PUBLISH->AddContent( 'smarty', 'cases/cases_PrintViewTitle', '', '', $array );
    }
    $G_PUBLISH->AddContent( 'dynaform', 'xmlform', $aDyn['PRO_UID'] . '/' . $aDyn['DYN_UID'], '', $Fields['APP_DATA'], '', '', 'view' );
    G::RenderPage( 'publish', 'blank' );
} catch (Exception $oException) {
    die( $oException->getMessage() );
}
?>

<script>
    try{
        oFields = document.getElementsByTagName('input');
        for(i=0; i<oFields.length; i++){
            if(oFields[i].type == 'button' || oFields[i].type == 'submit')
                oFields[i].style.display="none";
            else
                oFields[i].disabled="true";
        }
        oFields = document.getElementsByTagName('textarea');
        for(i=0; i<oFields.length; i++){
            oFields[i].disabled="true";
        }
        oFields = document.getElementsByTagName('select');
        for(i=0; i<oFields.length; i++){
            oFields[i].disabled="true";
        }

        oFields = document.getElementsByTagName('td');
        for(i=0; i<oFields.length; i++){
            if(oFields[i].className == 'withoutLabel' ){
                oFields[i].style.display="none";
                break;
            }
        }

        window.print();
    } catch(e){}
</script>

<?php

