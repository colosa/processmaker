<?php
/**
 * processes_List.php
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

if ($actionAjax == 'messageHistoryGridList_JXP') {

    if (!isset($_REQUEST['start'])) {
        $_REQUEST['start'] = 0;
    }

    if (!isset($_REQUEST['limit'])) {
        $_REQUEST['limit'] = 20;
    }

    G::LoadClass( 'case' );
    G::LoadClass( "BasePeer" );

    global $G_PUBLISH;
    $oCase = new Cases();

    $appMessageArray = $oCase->getHistoryMessagesTrackerExt( $_SESSION['APPLICATION'], true, $_REQUEST['start'], $_REQUEST['limit']);
    $appMessageCountArray = $oCase->getHistoryMessagesTrackerExt( $_SESSION['APPLICATION'], true);
    $result = new stdClass();
    $aProcesses = Array ();


    $proUid	= $_SESSION['PROCESS'];
    $appUid = $_SESSION['APPLICATION'];
    $tasUid	= $_SESSION['TASK'];
    $usrUid = $_SESSION['USER_LOGGED'];

    $respBlock  = $oCase->getAllObjectsFrom( $proUid, $appUid, $tasUid, $usrUid, 'BLOCK' );
    $respView   = $oCase->getAllObjectsFrom( $proUid, $appUid, $tasUid, $usrUid, 'VIEW' );
    $respResend = $oCase->getAllObjectsFrom( $proUid, $appUid, $tasUid, $usrUid, 'RESEND' );

    $delIndex = array();
    $respMess = "";

    if (count($respBlock["MSGS_HISTORY"]) > 0) {
        $respMess = $respBlock["MSGS_HISTORY"]["PERMISSION"];
        if (isset($respBlock["MSGS_HISTORY"]["DEL_INDEX"])) {
            $delIndex = $respBlock["MSGS_HISTORY"]["DEL_INDEX"];
        }
    }

    if (count($respView["MSGS_HISTORY"]) > 0) {
        $respMess = $respView["MSGS_HISTORY"]["PERMISSION"];
        if (isset($respView["MSGS_HISTORY"]["DEL_INDEX"])) {
            $delIndex = $respView["MSGS_HISTORY"]["DEL_INDEX"];
        }
    }

    if (count($respResend["MSGS_HISTORY"]) > 0) {
        $respMess = $respResend["MSGS_HISTORY"]["PERMISSION"];
        if (isset($respResend["MSGS_HISTORY"]["DEL_INDEX"])) {
            $delIndex = $respResend["MSGS_HISTORY"]["DEL_INDEX"];
        }
    }

    foreach ($appMessageArray as $index => $value) {
        if (($appMessageArray[$index]['APP_MSG_SHOW_MESSAGE'] == 1  && $respMess != 'BLOCK' ) &&
            ($appMessageArray[$index]['DEL_INDEX'] == 0 || in_array($appMessageArray[$index]['DEL_INDEX'], $delIndex ))) {

            $appMessageArray[$index]['ID_MESSAGE'] = $appMessageArray[$index]['APP_UID'] . '_' . $appMessageArray[$index]['APP_MSG_UID'];
            if ($respMess == 'BLOCK' || $respMess == '') {
                $appMessageArray[$index]['APP_MSG_BODY'] = "";
            }
            $aProcesses[] = array_merge($appMessageArray[$index], array('MSGS_HISTORY' => $respMess));
        }
    }

    $totalCount = 0;
    foreach ($appMessageCountArray as $index => $value) {
        if ($appMessageCountArray[$index]['APP_MSG_SHOW_MESSAGE'] == 1) {
            $totalCount ++;
        }
    }

    $newDir = '/tmp/test/directory';
    $r = G::verifyPath( $newDir );
    $r->data = $aProcesses;
    $r->totalCount = $totalCount;

    echo G::json_encode( $r );
}
if ($actionAjax == 'showHistoryMessage') {

    ?>
    <link rel="stylesheet" type="text/css" href="/css/classic.css" />
    <style type="text/css">
    html {
        color: black !important;
    }

    body {
        color: black !important;
    }
    </style>
    <script language="Javascript">
        //!Code that simulated reload library javascript maborak
        var leimnud = {};
        leimnud.exec = "";
        leimnud.fix = {};
        leimnud.fix.memoryLeak  = "";
        leimnud.browser = {};
        leimnud.browser.isIphone  = "";
        leimnud.iphone = {};
        leimnud.iphone.make = function(){};
        function ajax_function(ajax_server, funcion, parameters, method){
        }
        //!
      </script>
    <?php

    G::LoadClass( 'case' );
    $oCase = new Cases();

    $_POST["APP_UID"] = $_REQUEST["APP_UID"];
    $_POST['APP_MSG_UID'] = $_REQUEST["APP_MSG_UID"];

    $G_PUBLISH = new Publisher();
    $oCase = new Cases();

    $G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'cases/cases_MessagesView', '', $oCase->getHistoryMessagesTrackerView( $_POST['APP_UID'], $_POST['APP_MSG_UID'] ) );

    ?>
    <script language="javascript">
    <?php
    global $G_FORM;
    ?>
          function loadForm_<?php echo $G_FORM->id;?>(parametro1){
          }
        </script>
    <?php

    G::RenderPage( 'publish', 'raw' );
}

if ($actionAjax == 'sendMailMessage_JXP') {
    //!dataSystem
    $errorMessage = "";
    try {
        //!dataInput
        $_POST['APP_UID'] = $_REQUEST['APP_UID'];
        $_POST['APP_MSG_UID'] = $_REQUEST['APP_MSG_UID'];

        G::LoadClass( 'case' );
        G::LoadClass( 'spool' );

        $oCase = new Cases();
        $data = $oCase->getHistoryMessagesTrackerView( $_POST['APP_UID'], $_POST['APP_MSG_UID'] );

        G::LoadClass("system");

        $aSetup = System::getEmailConfiguration();

        $passwd = $aSetup['MESS_PASSWORD'];
        $passwdDec = G::decrypt( $passwd, 'EMAILENCRYPT' );
        $auxPass = explode( 'hash:', $passwdDec );
        if (count( $auxPass ) > 1) {
            if (count( $auxPass ) == 2) {
                $passwd = $auxPass[1];
            } else {
                array_shift( $auxPass );
                $passwd = implode( '', $auxPass );
            }
        }
        $aSetup['MESS_PASSWORD'] = $passwd;
        if ($aSetup['MESS_RAUTH'] == false || (is_string($aSetup['MESS_RAUTH']) && $aSetup['MESS_RAUTH'] == 'false')) {
            $aSetup['MESS_RAUTH'] = 0;
        } else {
            $aSetup['MESS_RAUTH'] = 1;
        }

        $oSpool = new spoolRun();
        $oSpool->setConfig(
            array (
                'MESS_ENGINE' => $aSetup['MESS_ENGINE'],
                'MESS_SERVER' => $aSetup['MESS_SERVER'],
                'MESS_PORT' => $aSetup['MESS_PORT'],
                'MESS_ACCOUNT' => $aSetup['MESS_ACCOUNT'],
                'MESS_PASSWORD' => $aSetup['MESS_PASSWORD'],
                'SMTPSecure' => $aSetup['SMTPSecure'],
                'SMTPAuth' => $aSetup['MESS_RAUTH']
            )
        );

        $oSpool->create( array ('msg_uid' => $data['MSG_UID'],'app_uid' => $data['APP_UID'],'del_index' => $data['DEL_INDEX'],'app_msg_type' => $data['APP_MSG_TYPE'],'app_msg_subject' => $data['APP_MSG_SUBJECT'],'app_msg_from' => $data['APP_MSG_FROM'],'app_msg_to' => $data['APP_MSG_TO'],'app_msg_body' => $data['APP_MSG_BODY'],'app_msg_cc' => $data['APP_MSG_CC'],'app_msg_bcc' => $data['APP_MSG_BCC'],'app_msg_attach' => $data['APP_MSG_ATTACH'],'app_msg_template' => $data['APP_MSG_TEMPLATE'],'app_msg_status' => 'pending'
        ) );
        $oSpool->sendMail();

    } catch (Exception $e) {

        $errorMessage = $e->getMessage();
    }

    echo $errorMessage;

}

