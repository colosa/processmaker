<?php

/**
 * @brief send new mail to the database spool
 *
 * @package Tomahawk_Mail
 * @author Ian K Armstrong <ika@[REMOVE_THESE_CAPITALS]openmail.cc>
 * @copyright Copyright (c) 2007, Ian K Armstrong
 * @license http://www.opensource.org/licenses/gpl-3.0.html GNU Public License
 * @link http://www.openmail.cc
 *
 * @category web_mail
 * @subpackage spool
 * @filesource
 *
 * @version @file tom_spool.php
 *
 */

if (isset( $_POST['form'] )) {
    $frm = "{$_POST['form']}";

    G::LoadClass( 'insert' );

    $db_spool = array ();

    $db_spool['msg_uid'] = 'msg_uid';
    $db_spool['app_uid'] = 'app_uid';
    $db_spool['del_index'] = 99; // random number
    $db_spool['app_msg_type'] = 'email message';
    $db_spool['app_msg_subject'] = 'subject field'; //"{$frm['subject']}";
    $db_spool['app_msg_from'] = '"ian armstrong" <ian@colosa.com>'; //"{$frm['from_email']}";
    $db_spool['app_msg_to'] = '<ika@openmail.cc>'; //"{$frm['to_email']}";
    $db_spool['app_msg_body'] = 'body field'; //"{$frm['body']}";
    $db_spool['app_msg_date'] = time();
    $db_spool['app_msg_cc'] = '<ian@openmail.cc>';
    $db_spool['app_msg_bcc'] = '<ian.k.armstrong@gmail.com>';
    $db_spool['app_msg_template'] = "/path/to/template";
    $db_spool['app_msg_status'] = "pending";

    $db_spool['app_msg_attach'] = serialize( array ('attachment_1','attachment_2'
    ) );

    /*
	    if(isset($frm['attachments']) && count($frm['attachments']) >0 )
        {
		    foreach($frm['attachments'] as $attchment)
		    {
		        $db_spool['app_msg_attach'][] = "$attchment";
            }
        }*/

    $insert = new insert( $db_spool );
    $status = $insert->returnStatus();
    unset( $insert );

}

$Fields['MESSAGE'] = $status;
$G_PUBLISH = new Publisher();
$G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'login/showMessage', '', $Fields, 'emailSystemSpool' );
G::RenderPage( 'publish' );

