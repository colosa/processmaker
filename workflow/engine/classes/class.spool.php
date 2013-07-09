<?php
/**
 * class.spool.php
 *
 * @package workflow.engine.classes
 *
 * ProcessMaker Open Source Edition
 * Copyright (C) 2004 - 2011 Colosa Inc.
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
 *
 */

/**
 * spoolRun - brief send email from the spool database, and see if we have all the addresses we send to.
 *
 * @author Ian K Armstrong <ika@[REMOVE_THESE_CAPITALS]openmail.cc>
 * @copyright Copyright (c) 2007, Ian K Armstrong
 * @license http://www.opensource.org/licenses/gpl-3.0.html GNU Public License
 * @link http://www.openmail.cc
 */

/**
 * LOG FIXES
 * =========
 *
 * 24-03-2010 Erik A.O. <erik@colosa.com>
 * class: the $ExceptionCode and $aWarnings class attributes were added
 * function handleFrom(): Validations for invalid data for {$this->fileData['from_email']} were added
 * function resendEmails(): handler for warnings was added and fixes
 * function getWarnings(): added
 * function sendMail(): now is handling the exception
 */

require_once ('classes/model/AppMessage.php');

/**
 *
 * @package workflow.engine.ProcessMaker
 */

class spoolRun
{
    public $config;
    private $fileData;
    private $spool_id;
    public $status;
    public $error;

    private $ExceptionCode = Array (); //Array to define the Expetion codes
    private $aWarnings = Array (); //Array to store the warning that were throws by the class


    private $longMailEreg;
    private $mailEreg;

    /**
     * Class constructor - iniatilize default values
     *
     * @param none
     * @return none
     */
    public function __construct ()
    {
        $this->config = array ();
        $this->fileData = array ();
        $this->spool_id = '';
        $this->status = 'pending';
        $this->error = '';

        $this->ExceptionCode['FATAL'] = 1;
        $this->ExceptionCode['WARNING'] = 2;
        $this->ExceptionCode['NOTICE'] = 3;

        $this->longMailEreg = '/(.*)(<([\w\-\.]+@[\w\-_\.]+\.\w{2,5})+>)/';
        $this->mailEreg = '/^([\w\-_\.]+@[\w\-_\.]+\.\w{2,5}+)$/';
    }

    /**
     * get all files into spool in a list
     *
     * @param none
     * @return none
     */
    public function getSpoolFilesList ()
    {
        $sql = "SELECT * FROM APP_MESSAGE WHERE APP_MSG_STATUS ='pending'";

        $con = Propel::getConnection( "workflow" );
        $stmt = $con->prepareStatement( $sql );
        $rs = $stmt->executeQuery();

        while ($rs->next()) {
            $this->spool_id = $rs->getString( 'APP_MSG_UID' );
            $this->fileData['subject'] = $rs->getString( 'APP_MSG_SUBJECT' );
            $this->fileData['from'] = $rs->getString( 'APP_MSG_FROM' );
            $this->fileData['to'] = $rs->getString( 'APP_MSG_TO' );
            $this->fileData['body'] = $rs->getString( 'APP_MSG_BODY' );
            $this->fileData['date'] = $rs->getString( 'APP_MSG_DATE' );
            $this->fileData['cc'] = $rs->getString( 'APP_MSG_CC' );
            $this->fileData['bcc'] = $rs->getString( 'APP_MSG_BCC' );
            $this->fileData['template'] = $rs->getString( 'APP_MSG_TEMPLATE' );
            $this->fileData['attachments'] = array (); //$rs->getString('APP_MSG_ATTACH');
            if ($this->config['MESS_ENGINE'] == 'OPENMAIL') {
                if ($this->config['MESS_SERVER'] != '') {
                    if (($sAux = @gethostbyaddr( $this->config['MESS_SERVER'] ))) {
                        $this->fileData['domain'] = $sAux;
                    } else {
                        $this->fileData['domain'] = $this->config['MESS_SERVER'];
                    }
                } else {
                    $this->fileData['domain'] = gethostbyaddr( '127.0.0.1' );
                }
            }
            $this->sendMail();
        }
    }

    /**
     * create a msg record for spool
     *
     * @param array $aData
     * @return none
     */
    public function create ($aData)
    {
        if (is_array($aData['app_msg_attach'])) {
            $attachment = $aData['app_msg_attach'];
        } else {
            $attachment = @unserialize($aData['app_msg_attach']);
            if ($attachment === false) {
                $attachment = explode(',', $aData['app_msg_attach']);
            }
        }
        $aData['app_msg_attach'] = serialize($attachment);
        $aData['app_msg_show_message'] = (isset($aData['app_msg_show_message'])) ? $aData['app_msg_show_message'] : 1;
        $sUID = $this->db_insert( $aData );

        $aData['app_msg_date'] = isset( $aData['app_msg_date'] ) ? $aData['app_msg_date'] : '';

        if (isset( $aData['app_msg_status'] )) {
            $this->status = strtolower( $aData['app_msg_status'] );
        }

        $aData["contentTypeIsHtml"] = (isset($aData["contentTypeIsHtml"]))? $aData["contentTypeIsHtml"] : true;

        $this->setData($sUID, $aData["app_msg_subject"], $aData["app_msg_from"], $aData["app_msg_to"], $aData["app_msg_body"], $aData["app_msg_date"], $aData["app_msg_cc"], $aData["app_msg_bcc"], $aData["app_msg_template"], $aData["app_msg_attach"], $aData["contentTypeIsHtml"]);
    }

    /**
     * set configuration
     *
     * @param array $aConfig
     * @return none
     */
    public function setConfig ($aConfig)
    {
        $this->config = $aConfig;
    }

    /**
     * set email parameters
     *
     * @param string $sAppMsgUid, $sSubject, $sFrom, $sTo, $sBody, $sDate, $sCC, $sBCC, $sTemplate
     * @return none
     */
    public function setData($sAppMsgUid, $sSubject, $sFrom, $sTo, $sBody, $sDate = "", $sCC = "", $sBCC = "", $sTemplate = "", $aAttachment = array(), $bContentTypeIsHtml = true)
    {
        $this->spool_id = $sAppMsgUid;
        $this->fileData['subject'] = $sSubject;
        $this->fileData['from'] = $sFrom;
        $this->fileData['to'] = $sTo;
        $this->fileData['body'] = $sBody;
        $this->fileData['date'] = ($sDate != '' ? $sDate : date( 'Y-m-d H:i:s' ));
        $this->fileData['cc'] = $sCC;
        $this->fileData['bcc'] = $sBCC;
        $this->fileData['template'] = $sTemplate;
        $this->fileData['attachments'] = $aAttachment;
        $this->fileData['envelope_to'] = array ();
        $this->fileData["contentTypeIsHtml"] = $bContentTypeIsHtml;

        if ($this->config['MESS_ENGINE'] == 'OPENMAIL') {
            if ($this->config['MESS_SERVER'] != '') {
                if (($sAux = @gethostbyaddr( $this->config['MESS_SERVER'] ))) {
                    $this->fileData['domain'] = $sAux;
                } else {
                    $this->fileData['domain'] = $this->config['MESS_SERVER'];
                }
            } else {
                $this->fileData['domain'] = gethostbyaddr( '127.0.0.1' );
            }
        }
    }

    /**
     * send mail
     *
     * @param none
     * @return boolean true or exception
     */
    public function sendMail ()
    {
        try {
            $this->handleFrom();
            $this->handleEnvelopeTo();
            $this->handleMail();
            $this->updateSpoolStatus();
            return true;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * update the status to spool
     *
     * @param none
     * @return none
     */
    private function updateSpoolStatus ()
    {
        $oAppMessage = AppMessagePeer::retrieveByPK( $this->spool_id );
        if (is_array( $this->fileData['attachments'] )) {
            $attachment = implode( ",", $this->fileData['attachments'] );
            $oAppMessage->setappMsgAttach( $attachment );
        }
        $oAppMessage->setappMsgstatus( $this->status );
        $oAppMessage->setappMsgsenddate( date( 'Y-m-d H:i:s' ) );
        $oAppMessage->save();
    }

    /**
     * handle the email that was set in "TO" parameter
     *
     * @param none
     * @return boolean true or exception
     */
    private function handleFrom ()
    {
        if (strpos( $this->fileData['from'], '<' ) !== false) {
            //to validate complex email address i.e. Erik A. O <erik@colosa.com>
            preg_match( $this->longMailEreg, $this->fileData['from'], $matches );
            if (isset( $matches[1] ) && $matches[1] != '') {
                //drop the " characters if they exist
                $this->fileData['from_name'] = trim( str_replace( '"', '', $matches[1] ) );
            } else {
                //if the from name was not set
                $this->fileData['from_name'] = '';
            }

            if (! isset( $matches[3] )) {
                throw new Exception( 'Invalid email address in FROM parameter (' . $this->fileData['from'] . ')', $this->ExceptionCode['WARNING'] );
            }

            $this->fileData['from_email'] = trim( $matches[3] );
        } else {
            //to validate simple email address i.e. erik@colosa.com
            preg_match( $this->mailEreg, $this->fileData['from'], $matches );

            if (! isset( $matches[0] )) {
                throw new Exception( 'Invalid email address in FROM parameter (' . $this->fileData['from'] . ')', $this->ExceptionCode['WARNING'] );
            }

            $this->fileData['from_name'] = '';
            $this->fileData['from_email'] = $matches[0];
        }
        
        // Set reply to
        preg_match( $this->longMailEreg, $this->fileData['from_name'], $matches );
        if (isset($matches[3])) {
            $this->fileData['reply_to'] = $matches[3];
            $this->fileData['reply_to_name'] = isset($matches[1]) ? $matches[1] : $this->fileData['from_name'];
        } else {
            preg_match( $this->mailEreg, $this->fileData['from_name'], $matches );
            if (isset($matches[1])) {
                $this->fileData['reply_to'] = $matches[1];
                $this->fileData['reply_to_name'] = '';
            } else {
                $this->fileData['reply_to'] = '';
                $this->fileData['reply_to_name'] = '';
            }
        }

    }

    /**
     * handle all recipients to compose the mail
     *
     * @param none
     * @return boolean true or exception
     */
    private function handleEnvelopeTo ()
    {
        $hold = array ();
        $holdcc = array ();
        $holdbcc = array ();
        $text = trim( $this->fileData['to'] );

        $textcc = '';
        $textbcc = '';
        if (isset( $this->fileData['cc'] ) && trim( $this->fileData['cc'] ) != '') {
            $textcc = trim( $this->fileData['cc'] );
        }

        if (isset( $this->fileData['bcc'] ) && trim( $this->fileData['bcc'] ) != '') {
            $textbcc = trim( $this->fileData['bcc'] );
        }

        if (false !== (strpos( $text, ',' ))) {
            $hold = explode( ',', $text );

            foreach ($hold as $val) {
                if (strlen( $val ) > 0) {
                    $this->fileData['envelope_to'][] = "$val";
                }
            }
        } elseif ($text != '') {
            $this->fileData['envelope_to'][] = "$text";
        } else {
            $this->fileData['envelope_to'] = Array ();
        }

        //CC
        if (false !== (strpos( $textcc, ',' ))) {
            $holdcc = explode( ',', $textcc );

            foreach ($holdcc as $valcc) {
                if (strlen( $valcc ) > 0) {
                    $this->fileData['envelope_cc'][] = "$valcc";
                }
            }
        } elseif ($textcc != '') {
            $this->fileData['envelope_cc'][] = "$textcc";
        } else {
            $this->fileData['envelope_cc'] = Array ();
        }

        //BCC
        if (false !== (strpos( $textbcc, ',' ))) {
            $holdbcc = explode( ',', $textbcc );

            foreach ($holdbcc as $valbcc) {
                if (strlen( $valbcc ) > 0) {
                    $this->fileData['envelope_bcc'][] = "$valbcc";
                }
            }
        } elseif ($textbcc != '') {
            $this->fileData['envelope_bcc'][] = "$textbcc";
        } else {
            $this->fileData['envelope_bcc'] = Array ();
        }

    }

    /**
     * handle and compose the email content and parameters
     *
     * @param none
     * @return none
     */
    private function handleMail ()
    {
        if (count( $this->fileData['envelope_to'] ) > 0) {
            switch ($this->config['MESS_ENGINE']) {
                case 'MAIL':
                case 'PHPMAILER':
                    G::LoadThirdParty( 'phpmailer', 'class.phpmailer' );

                    switch ($this->config['MESS_ENGINE']) {
                        case 'MAIL':
                            $oPHPMailer = new PHPMailer();
                            $oPHPMailer->Mailer = 'mail';
                            break;
                        case 'PHPMAILER':
                            $oPHPMailer = new PHPMailer( true );
                            $oPHPMailer->Mailer = 'smtp';
                            break;
                    }

                    $oPHPMailer->SMTPAuth = (isset( $this->config['SMTPAuth'] ) ? $this->config['SMTPAuth'] : '');

                    switch ($this->config['MESS_ENGINE']) {
                        case 'MAIL':
                            break;
                        case 'PHPMAILER':
                            //Posible Options for SMTPSecure are: "", "ssl" or "tls"
                            if (isset( $this->config['SMTPSecure'] ) && preg_match( '/^(ssl|tls)$/', $this->config['SMTPSecure'] )) {
                                $oPHPMailer->SMTPSecure = $this->config['SMTPSecure'];
                            }
                            break;
                    }

                    $oPHPMailer->CharSet = "UTF-8";
                    $oPHPMailer->Encoding = "8bit";
                    $oPHPMailer->Host = $this->config['MESS_SERVER'];
                    $oPHPMailer->Port = $this->config['MESS_PORT'];
                    $oPHPMailer->Username = $this->config['MESS_ACCOUNT'];
                    $passwd = $this->config['MESS_PASSWORD'];
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

                    $this->config['MESS_PASSWORD'] = $passwd;
                    $oPHPMailer->Password = $this->config['MESS_PASSWORD'];
                    $oPHPMailer->From = $this->fileData['from_email'];
                    $oPHPMailer->FromName = utf8_decode( $this->fileData['from_name'] );
                    if (isset($this->fileData['reply_to'])) {
                        if ($this->fileData['reply_to'] != '') {
                            $oPHPMailer->AddReplyTo($this->fileData['reply_to'], $this->fileData['reply_to_name']);
                        }
                    }

                    $msSubject = $this->fileData['subject'];

                    if (! (mb_detect_encoding( $msSubject, "UTF-8" ) == "UTF-8")) {
                        $msSubject = utf8_encode( $msSubject );
                    }

                    $oPHPMailer->Subject = $msSubject;

                    $msBody = $this->fileData['body'];

                    if (! (mb_detect_encoding( $msBody, "UTF-8" ) == "UTF-8")) {
                        $msBody = utf8_encode( $msBody );
                    }

                    $oPHPMailer->Body = $msBody;

                    $attachment = @unserialize($this->fileData['attachments']);
                    if ($attachment === false) {
                        $attachment = $this->fileData['attachments'];
                    }
                    if (is_array($attachment)) {
                        foreach ($attachment as $key => $fileAttach) {
                            if (file_exists( $fileAttach )) {
                                $oPHPMailer->AddAttachment( $fileAttach, is_int( $key ) ? '' : $key );
                            }
                        }
                    }

                    foreach ($this->fileData['envelope_to'] as $sEmail) {
                        if (strpos( $sEmail, '<' ) !== false) {
                            preg_match( $this->longMailEreg, $sEmail, $matches );
                            $sTo = trim( $matches[3] );
                            $sToName = trim( $matches[1] );
                            $oPHPMailer->AddAddress( $sTo, $sToName );
                        } else {
                            $oPHPMailer->AddAddress( $sEmail );
                        }
                    }

                    //CC
                    foreach ($this->fileData['envelope_cc'] as $sEmail) {
                        if (strpos( $sEmail, '<' ) !== false) {
                            preg_match( $this->longMailEreg, $sEmail, $matches );
                            $sTo = trim( $matches[3] );
                            $sToName = trim( $matches[1] );
                            $oPHPMailer->AddCC( $sTo, $sToName );
                        } else {
                            $oPHPMailer->AddCC( $sEmail );
                        }
                    }

                    //BCC
                    foreach ($this->fileData['envelope_bcc'] as $sEmail) {
                        if (strpos( $sEmail, '<' ) !== false) {
                            preg_match( $this->longMailEreg, $sEmail, $matches );
                            $sTo = trim( $matches[3] );
                            $sToName = trim( $matches[1] );
                            $oPHPMailer->AddBCC( $sTo, $sToName );
                        } else {
                            $oPHPMailer->AddBCC( $sEmail );
                        }
                    }

                    $oPHPMailer->IsHTML($this->fileData["contentTypeIsHtml"]);

                    if ($oPHPMailer->Send()) {
                        $this->error = '';
                        $this->status = 'sent';
                    } else {
                        $this->error = $oPHPMailer->ErrorInfo;
                        $this->status = 'failed';
                    }
                    break;
                case 'OPENMAIL':
                    G::LoadClass( 'package' );
                    G::LoadClass( 'smtp' );
                    $pack = new package( $this->fileData );
                    $header = $pack->returnHeader();
                    $body = $pack->returnBody();
                    $send = new smtp();
                    $send->setServer( $this->config['MESS_SERVER'] );
                    $send->setPort( $this->config['MESS_PORT'] );
                    $send->setUsername( $this->config['MESS_ACCOUNT'] );

                    $passwd = $this->config['MESS_PASSWORD'];
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

                    $this->config['MESS_PASSWORD'] = $passwd;
                    $send->setPassword( $this->config['MESS_PASSWORD'] );
                    $send->setReturnPath( $this->fileData['from_email'] );
                    $send->setHeaders( $header );
                    $send->setBody( $body );
                    $send->setEnvelopeTo( $this->fileData['envelope_to'] );
                    if ($send->sendMessage()) {
                        $this->error = '';
                        $this->status = 'sent';
                    } else {
                        $this->error = implode( ', ', $send->returnErrors() );
                        $this->status = 'failed';
                    }
                    break;
            }
        }
    }

    /**
     * try resend the emails from spool
     *
     * @param string $dateResend
     * @return none or exception
     */
    public function resendEmails ($dateResend = null, $cron = 0)
    {
        require_once ("classes/model/Configuration.php");

        $oConfiguration = new Configuration();

        $aConfiguration = $oConfiguration->load( "Emails", "", "", "", "" );

        $aConfiguration = unserialize( $aConfiguration["CFG_VALUE"] );
        $passwd = $aConfiguration["MESS_PASSWORD"];
        $passwdDec = G::decrypt( $passwd, "EMAILENCRYPT" );
        $auxPass = explode( "hash:", $passwdDec );

        if (count( $auxPass ) > 1) {
            if (count( $auxPass ) == 2) {
                $passwd = $auxPass[1];
            } else {
                array_shift( $auxPass );
                $passwd = implode( "", $auxPass );
            }
        }

        $aConfiguration["MESS_PASSWORD"] = $passwd;

        if ($aConfiguration["MESS_ENABLED"] == "1") {
            require_once ("classes/model/AppMessage.php");
            if ($aConfiguration['MESS_RAUTH'] == false || (is_string($aConfiguration['MESS_RAUTH']) && $aConfiguration['MESS_RAUTH'] == 'false')) {
                $aConfiguration['MESS_RAUTH'] = 0;
            } else {
                $aConfiguration['MESS_RAUTH'] = 1;
            }

            $this->setConfig( array ("MESS_ENGINE" => $aConfiguration["MESS_ENGINE"],"MESS_SERVER" => $aConfiguration["MESS_SERVER"],"MESS_PORT" => $aConfiguration["MESS_PORT"],"MESS_ACCOUNT" => $aConfiguration["MESS_ACCOUNT"],"MESS_PASSWORD" => $aConfiguration["MESS_PASSWORD"],"SMTPAuth" => $aConfiguration["MESS_RAUTH"],"SMTPSecure" => $aConfiguration["SMTPSecure"]
            ) );

            $criteria = new Criteria( "workflow" );
            $criteria->add( AppMessagePeer::APP_MSG_STATUS, "sent", Criteria::NOT_EQUAL );

            if ($dateResend != null) {
                $criteria->add( AppMessagePeer::APP_MSG_DATE, $dateResend, Criteria::GREATER_EQUAL );
            }

            $rsCriteria = AppMessagePeer::doSelectRS( $criteria );
            $rsCriteria->setFetchmode( ResultSet::FETCHMODE_ASSOC );

            while ($rsCriteria->next()) {
                if ($cron == 1) {
                    $arrayCron = unserialize( trim( @file_get_contents( PATH_DATA . "cron" ) ) );
                    $arrayCron["processcTimeStart"] = time();
                    @file_put_contents( PATH_DATA . "cron", serialize( $arrayCron ) );
                }

                $row = $rsCriteria->getRow();

                try {
                    $sFrom = $row["APP_MSG_FROM"];
                    $hasEmailFrom = preg_match('/(.+)@(.+)\.(.+)/', $row["APP_MSG_FROM"], $match);

                    if (! $hasEmailFrom || strpos( $row["APP_MSG_FROM"], $aConfiguration['MESS_ACCOUNT'] ) === false) {
                        $sFrom = '"' . stripslashes( $row["APP_MSG_FROM"] ) . '" <' . $aConfiguration['MESS_ACCOUNT'] . ">";
                    }
                    $this->setData( $row["APP_MSG_UID"], $row["APP_MSG_SUBJECT"], $sFrom, $row["APP_MSG_TO"], $row["APP_MSG_BODY"], date( "Y-m-d H:i:s" ), $row["APP_MSG_CC"], $row["APP_MSG_BCC"], $row["APP_MSG_TEMPLATE"], $row["APP_MSG_ATTACH"] );

                    $this->sendMail();
                } catch (Exception $e) {
                    $strAux = "Spool::resendEmails(): Using " . $aConfiguration["MESS_ENGINE"] . " for APP_MGS_UID=" . $row["APP_MSG_UID"] . " -> With message: " . $e->getMessage();

                    if ($e->getCode() == $this->ExceptionCode["WARNING"]) {
                        array_push( $this->aWarnings, $strAux );
                        continue;
                    } else {
                        throw $e;
                    }
                }
            }
        }
    }

    /**
     * gets all warnings
     *
     * @param none
     * @return string $this->aWarnings
     */
    public function getWarnings ()
    {
        if (sizeof( $this->aWarnings ) != 0) {
            return $this->aWarnings;
        }

        return false;
    }

    /**
     * db_insert
     *
     * @param array $db_spool
     * @return string $sUID;
     */
    public function db_insert ($db_spool)
    {
        $sUID = G::generateUniqueID();
        $spool = new AppMessage();
        $spool->setAppMsgUid( $sUID );
        $spool->setMsgUid( $db_spool['msg_uid'] );
        $spool->setAppUid( $db_spool['app_uid'] );
        $spool->setDelIndex( $db_spool['del_index'] );
        $spool->setAppMsgType( $db_spool['app_msg_type'] );
        $spool->setAppMsgSubject( $db_spool['app_msg_subject'] );
        $spool->setAppMsgFrom( $db_spool['app_msg_from'] );
        $spool->setAppMsgTo( $db_spool['app_msg_to'] );
        $spool->setAppMsgBody( $db_spool['app_msg_body'] );
        $spool->setAppMsgDate( date( 'Y-m-d H:i:s' ) );
        $spool->setAppMsgCc( $db_spool['app_msg_cc'] );
        $spool->setAppMsgBcc( $db_spool['app_msg_bcc'] );
        $spool->setappMsgAttach( $db_spool['app_msg_attach'] );
        $spool->setAppMsgTemplate( $db_spool['app_msg_template'] );
        $spool->setAppMsgStatus( $db_spool['app_msg_status'] );
        $spool->setAppMsgSendDate( date( 'Y-m-d H:i:s' ) ); // Add by Ankit
        $spool->setAppMsgShowMessage( $db_spool['app_msg_show_message'] ); // Add by Ankit


        if (! $spool->validate()) {
            $errors = $spool->getValidationFailures();
            $this->status = 'error';

            foreach ($errors as $key => $value) {
                echo "Validation error - " . $value->getMessage( $key ) . "\n";
            }
        } else {
            //echo "Saving - validation ok\n";
            $this->status = 'success';
            $spool->save();
        }

        return $sUID;
    }
}

