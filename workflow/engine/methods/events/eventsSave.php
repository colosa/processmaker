<?php

/**
 * events_Save.php
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
global $RBAC;
if ($RBAC->userCanAccess( 'PM_SETUP' ) != 1) {
    G::SendTemporalMessage( 'ID_USER_HAVENT_RIGHTS_PAGE', 'error', 'labels' );
    G::header( 'location: ../login/login' );
    die();
}
$EVN_MESSAGE_TO_TO = isset( $_POST['form']['EVN_MESSAGE_TO_TO'] ) ? replaceQuotes( $_POST['form']['EVN_MESSAGE_TO_TO'] ) : Array ();
$EVN_MESSAGE_TO_CC = isset( $_POST['form']['EVN_MESSAGE_TO_CC'] ) ? replaceQuotes( $_POST['form']['EVN_MESSAGE_TO_CC'] ) : Array ();
$EVN_MESSAGE_TO_BCC = isset( $_POST['form']['EVN_MESSAGE_TO_BCC'] ) ? replaceQuotes( $_POST['form']['EVN_MESSAGE_TO_BCC'] ) : Array ();

if (isset( $_POST['form']['EVN_MESSAGE_SUBJECT'] )) {
    $_POST['form']['EVN_ACTION_PARAMETERS'] = array ('SUBJECT' => $_POST['form']['EVN_MESSAGE_SUBJECT'],'TO' => $EVN_MESSAGE_TO_TO,'CC' => $EVN_MESSAGE_TO_CC,'BCC' => $EVN_MESSAGE_TO_BCC,'TEMPLATE' => $_POST['form']['EVN_MESSAGE_TEMPLATE']);

    unset( $_POST['form']['EVN_MESSAGE_SUBJECT'] );
    unset( $_POST['form']['EVN_MESSAGE_TO_TO'] );
    unset( $_POST['form']['EVN_MESSAGE_TO_CC'] );
    unset( $_POST['form']['EVN_MESSAGE_TO_BCC'] );
    unset( $_POST['form']['EVN_MESSAGE_TEMPLATE'] );
}
unset( $_POST['form']['SAVE'] );

require_once 'classes/model/Event.php';
$oEvent = new Event();
if ($_POST['form']['EVN_UID'] == '') {
    //this is probably not used, because the creation of one Event is done directly in EventsNewAction
    $oEvent->create( $_POST['form'] );
} else {
    /*
    *if($_POST['form']['EVN_ACTION'] == 'SEND_MESSAGE' && $ev->getTriUid() != trim($_POST['form']['TRI_UID']) ){
    $oEvnActionParameters = unserialize($ev->getEvnActionParameters());
    prit_r($oEvnActionParameters);
    if( isset($oEvnActionParameters->TRI_UID) ){
      $_POST['form']['TRI_UID'] = $oEvnActionParameters->TRI_UID;
    }
    }
    */
    $oEvent->update( $_POST['form'] );
}

function replaceQuotes ($aData)
{
    for ($i = 0; $i < sizeof( $aData ); $i ++) {
        $aData[$i] = str_replace( "&quote;", '"', $aData[$i] );
    }
    return $aData;
}

