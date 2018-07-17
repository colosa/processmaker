<?php
/**
 * cases_Reassign.php
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
    switch ($RBAC->userCanAccess( 'PM_REASSIGNCASE' )) {
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


    $tpl = new TemplatePower( PATH_TPL . "cases/cases_Reassign.html" );
    $tpl->prepare();

    require_once 'classes/model/AppDelegation.php';
    $oCriteria = new Criteria( 'workflow' );
    $oCriteria->add( AppDelegationPeer::APP_UID, $_GET['APP_UID'] );
    $oCriteria->add( AppDelegationPeer::DEL_INDEX, $_GET['DEL_INDEX'] );
    $oCriteria->add( AppDelegationPeer::DEL_FINISH_DATE, null );
    $oDataset = AppDelegationPeer::doSelectRS( $oCriteria );
    $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
    $oDataset->next();
    $c = 0;

    $oTasks = new Tasks();

    $oGroups = new Groups();
    require_once 'classes/model/Users.php';
    $oUser = new Users();
    $name = '';
    while ($aRow = $oDataset->getRow()) {
        $c ++;

        $aUsr = array ();
        $aUsrUid = array ();
        $aAux1 = $oTasks->getGroupsOfTask( $aRow['TAS_UID'], 1 );
        foreach ($aAux1 as $value1) {
            $aAux2 = $oGroups->getUsersOfGroup( $value1['GRP_UID'] );
            foreach ($aAux2 as $value2) {
                if ($aRow['USR_UID'] != $value2['USR_UID']) {
                    if (! in_array( $value2['USR_UID'], $aUsrUid )) {
                        //var_dump($aRow['USR_UID'], $value2['USR_UID']);echo '<br /><br />';
                        $aAux = $oUser->load( $value2['USR_UID'] );
                        $aUsr[$aAux['USR_FIRSTNAME'] . ' ' . $aAux['USR_LASTNAME']] = $aAux;
                        $aUsrUid[] = $value2['USR_UID'];
                    }
                }
            }
        }

        $aUsers = $oTasks->getUsersOfTask( $aRow['TAS_UID'], 1 );
        foreach ($aUsers as $key => $value) {
            if ($aRow['USR_UID'] != $value['USR_UID']) {
                if (! in_array( $value['USR_UID'], $aUsrUid )) {
                    $aUsr[$value['USR_FIRSTNAME'] . ' ' . $value['USR_LASTNAME']] = $value;
                }
            }
        }
        ksort( $aUsr );
        //$users='';
        //$users='<select name="USERS"><option value="">Seleccione</option>';
        foreach ($aUsr as $key => $value) {
            $tpl->newBlock( "users" );
            $name = $value['USR_FIRSTNAME'] . ' ' . $value['USR_LASTNAME'] . ' (' . $value['USR_USERNAME'] . ')';
            //$users=$users."<option value='".$value['USR_UID']."'>". $name ."</option>";
            $tpl->assign( "USR_UID", $value['USR_UID'] );
            $tpl->assign( "USERS", $name );
        }
        //$users=$users.' </select>';


        //$tpl->assign( "USERS", $users );


        $oDataset->next();
    }
    $tpl->gotoBlock( '_ROOT' );
    $tpl->assign( "US", $name );
    $tpl->assign( "ID_NO_REASSIGN", '-' );
    $tpl->assign( "APP_UID", $_GET['APP_UID'] );
    $tpl->assign( "DEL_INDEX", $_GET['DEL_INDEX'] );

    $G_MAIN_MENU = 'processmaker';
    $G_SUB_MENU = 'cases';
    $G_ID_MENU_SELECTED = 'CASES';
    $G_ID_SUB_MENU_SELECTED = 'CASES_TO_REASSIGN';
    $G_PUBLISH = new Publisher();
    $G_PUBLISH->AddContent( 'template', '', '', '', $tpl );
    G::RenderPage( 'publish', 'blank' );

} catch (Exception $oException) {
    $token = strtotime("now");
    PMException::registerErrorLog($oException, $token);
    G::outRes( G::LoadTranslation("ID_EXCEPTION_LOG_INTERFAZ", array($token)) );
    die;
}

