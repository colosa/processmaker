<?php
/**
 * emails.php
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
$RBAC->requirePermissions( 'PM_SETUP_ADVANCE' );

$request = (isset( $_POST['action'] )) ? $_POST['action'] : $_POST['request'];

switch ($request) {
    case 'init':
        $G_PUBLISH = new Publisher();
        $G_PUBLISH->AddContent( 'view', 'setup/mailConnectiontest' );
        G::RenderPage( 'publish', 'raw' );
        break;
    case 'mailTestMail_Show':
        define( "SUCCESSFUL", 'SUCCESSFUL' );
        define( "FAILED", 'FAILED' );
        $mail_to = $_POST['mail_to'];
        $send_test_mail = $_POST['send_test_mail'];
        $_POST['FROM_NAME'] = $mail_to;
        $_POST['FROM_EMAIL'] = $mail_to;
        $_POST['MESS_ENGINE'] = 'MAIL';
        $_POST['MESS_SERVER'] = 'localhost';
        $_POST['MESS_PORT'] = 25;
        $_POST['MESS_ACCOUNT'] = $mail_to;
        $_POST['MESS_PASSWORD'] = '';
        $_POST['TO'] = $mail_to;
        $_POST['SMTPAuth'] = true;
        $resp = sendTestMail();
        if ($resp->status) {
            print (SUCCESSFUL . ',' . $resp->msg) ;
        } else {
            print (FAILED . ',' . $resp->msg) ;
        }
        break;
    case 'testConnection':
        G::LoadClass( 'net' );
        G::LoadThirdParty( 'phpmailer', 'class.smtp' );

        define( "SUCCESSFUL", 'SUCCESSFUL' );
        define( "FAILED", 'FAILED' );
        $varaux = preg_match( "([^//]*$)", $_POST['srv'], $regs );
        if ($regs)
            $srv = $regs[0];
        else
            $srv = $_POST['srv'];

        $port = ($_POST['port'] == 'default') ? 25 : $_POST['port'];
        $user = $_POST['account'];
        $passwd = $_POST['passwd'];
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
        $_POST['passwd'] = $passwd;
        $step = $_POST['step'];
        $auth_required = $_POST['auth_required'];
        $send_test_mail = $_POST['send_test_mail'];
        $mail_to = $_POST['mail_to'];
        $SMTPSecure = $_POST['SMTPSecure'];
        $timeout = 10;

        $Server = new NET( $srv );
        $smtp = new SMTP();

        switch ($step) {
            case 1:
                if ($Server->getErrno() == 0) {
                    print (SUCCESSFUL . ',') ;
                } else {
                    print (FAILED . ',' . $Server->error) ;
                }
                break;
            case 2:
                if ($port == 0) {
                    $port = $smtp->SMTP_PORT;
                }
                $Server->scannPort( $port );
                if ($Server->getErrno() == 0) {
                    print (SUCCESSFUL . ',') ;
                } else {
                    print (FAILED . ',' . $Server->error) ;
                }
                break;
            #try to connect to host
            case 3:
                $hostinfo = array ();

                if (preg_match( '/^(.+):([0-9]+)$/', $srv, $hostinfo )) {
                    $host = $hostinfo[1];
                    $port = $hostinfo[2];
                } else {
                    $host = $srv;
                }

                $tls = ($SMTPSecure == 'tls');
                $ssl = ($SMTPSecure == 'ssl');

                $resp = $smtp->Connect( ($ssl ? 'ssl://' : '') . $host, $port, $timeout );
                if ($resp) {
                    print (SUCCESSFUL . ',' . $smtp->status) ;
                } else {
                    print (FAILED . ',' . $smtp->error['error']) ;
                }
                break;
            #try login to host
            case 4:
                if ($auth_required == 'yes') {
                    try {
                        $hostinfo = array ();

                        if (preg_match( '/^(.+):([0-9]+)$/', $srv, $hostinfo )) {
                            $host = $hostinfo[1];
                            $port = $hostinfo[2];
                        } else {
                            $host = $srv;
                        }

                        $tls = ($SMTPSecure == 'tls');
                        $ssl = ($SMTPSecure == 'ssl');

                        $resp = $smtp->Connect( ($ssl ? 'ssl://' : '') . $host, $port, $timeout );
                        if ($resp) {

                            $hello = $_SERVER['SERVER_NAME'];
                            $smtp->Hello( $hello );

                            if ($tls) {
                                if (! $smtp->StartTLS()) {
                                    // problem with tls
                                }

                                //We must resend HELO after tls negotiation
                                $smtp->Hello( $hello );
                            }

                            if ($smtp->Authenticate( $user, $passwd )) {
                                print (SUCCESSFUL . ',' . $smtp->status) ;
                            } else {
                                $smtpError = $smtp->getError();
                                print (FAILED . ',' . $smtpError['error']);
                                // print (FAILED . ',' . $smtp->error['error']) ;
                            }

                        } else {
                            $smtpError = $smtp->getError();
                            print (FAILED . ',' . $smtpError['error']);
                            // print (FAILED . ',' . $smtp->error['error']) ;
                        }
                    } catch (Exception $e) {
                        print (FAILED . ',' . $e->getMessage()) ;
                    }
                } else {
                    print (SUCCESSFUL . ', No authentication required!') ;
                }
                break;
            case 5:
                if ($send_test_mail == 'yes') {
                    try {
                        //print(SUCCESSFUL.',ok');
                        $_POST['FROM_NAME'] = 'Process Maker O.S. [Test mail]';
                        $_POST['FROM_EMAIL'] = $user;

                        $_POST['MESS_ENGINE'] = 'PHPMAILER';
                        $_POST['MESS_SERVER'] = $srv;
                        $_POST['MESS_PORT'] = $port;
                        $_POST['MESS_ACCOUNT'] = $user;
                        $_POST['MESS_PASSWORD'] = $passwd;
                        $_POST['TO'] = $mail_to;
                        if ($auth_required == 'yes') {
                            $_POST['SMTPAuth'] = true;
                        } else {
                            $_POST['SMTPAuth'] = false;
                        }
                        $resp = sendTestMail();

                        if ($resp->status) {
                            print (SUCCESSFUL . ',' . $resp->msg) ;
                        } else {
                            print (FAILED . ',' . $resp->msg) ;
                        }
                    } catch (Exception $e) {
                        print (FAILED . ',' . $e->getMessage()) ;
                    }

                } else {
                    print ('jump this step') ;
                }
                break;
            default:
                print ('test finished!') ;
        }
        break;
}

function sendTestMail ()
{
    G::LoadClass( "system" );
    $sFrom = ($_POST['FROM_NAME'] != '' ? $_POST['FROM_NAME'] . ' ' : '') . '<' . $_POST['FROM_EMAIL'] . '>';
    $sSubject = G::LoadTranslation( 'ID_MESS_TEST_SUBJECT' );
    $msg = G::LoadTranslation( 'ID_MESS_TEST_BODY' );

    switch ($_POST['MESS_ENGINE']) {
        case 'MAIL':
            $engine = G::LoadTranslation( 'ID_MESS_ENGINE_TYPE_1' );
            break;
        case 'PHPMAILER':
            $engine = G::LoadTranslation( 'ID_MESS_ENGINE_TYPE_2' );
            break;
        case 'OPENMAIL':
            $engine = G::LoadTranslation( 'ID_MESS_ENGINE_TYPE_3' );
            break;
    }

    $sBody = "
  <table style=\"background-color: white; font-family: Arial,Helvetica,sans-serif; color: black; font-size: 11px; text-align: left;\" cellpadding='10' cellspacing='0' width='100%'>
  <tbody><tr><td><img id='logo' src='http://" . $_SERVER['SERVER_NAME'] . "/images/processmaker.logo.jpg' /></td></tr>
  <tr><td style='font-size: 14px;'>$msg [" . date( 'H:i:s' ) . "] - $engine</td></tr>
  <tr><td style='vertical-align:middel;'>
  <br /><hr><b>This Business Process is powered by ProcessMaker ver. " . System::getVersion() . ".<b><br />
  <a href='http://www.processmaker.com' style='color:#c40000;'>www.processmaker.com</a><br /></td>
  </tr></tbody></table>";

    G::LoadClass( 'spool' );
    $oSpool = new spoolRun();

    $passwd = $_POST['MESS_PASSWORD'];
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
    $_POST['MESS_PASSWORD'] = $passwd;

    $oSpool->setConfig( array ('MESS_ENGINE' => $_POST['MESS_ENGINE'],'MESS_SERVER' => $_POST['MESS_SERVER'],'MESS_PORT' => $_POST['MESS_PORT'],'MESS_ACCOUNT' => $_POST['MESS_ACCOUNT'],'MESS_PASSWORD' => $_POST['MESS_PASSWORD'],'SMTPAuth' => $_POST['SMTPAuth'],'SMTPSecure' => isset( $_POST['SMTPSecure'] ) ? $_POST['SMTPSecure'] : 'none'
    ) );

    $oSpool->create( array ('msg_uid' => '','app_uid' => '','del_index' => 0,'app_msg_type' => 'TEST','app_msg_subject' => $sSubject,'app_msg_from' => $sFrom,'app_msg_to' => $_POST['TO'],'app_msg_body' => $sBody,'app_msg_cc' => '','app_msg_bcc' => '','app_msg_attach' => '','app_msg_template' => '','app_msg_status' => 'pending','app_msg_attach' => ''  // Added By Ankit
    ) );

    $oSpool->sendMail();

    global $G_PUBLISH;
    $G_PUBLISH = new Publisher();
    if ($oSpool->status == 'sent') {
        $o->status = true;
        $o->msg = G::LoadTranslation( 'ID_MAIL_TEST_SUCCESS' );
    } else {
        $o->status = false;
        $o->msg = $oSpool->error;
    }
    return $o;
}

function e_utf8_encode ($input)
{
    $utftext = null;

    for ($n = 0; $n < strlen( $input ); $n ++) {

        $c = ord( $input[$n] );

        if ($c < 128) {
            $utftext .= chr( $c );
        } else if (($c > 128) && ($c < 2048)) {
            $utftext .= chr( ($c >> 6) | 192 );
            $utftext .= chr( ($c & 63) | 128 );
        } else {
            $utftext .= chr( ($c >> 12) | 224 );
            $utftext .= chr( (($c & 6) & 63) | 128 );
            $utftext .= chr( ($c & 63) | 128 );
        }
    }

    return $utftext;
}

