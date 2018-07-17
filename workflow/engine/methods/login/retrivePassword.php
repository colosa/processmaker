<?php

use ProcessMaker\Core\System;

$data = $_POST['form'];
global $RBAC;

$rbacUser = new RbacUsers();
$user = new Users();
$data['USR_USERNAME'] = strip_tags($data['USR_USERNAME']);
$userData = $rbacUser->getByUsername($data['USR_USERNAME']);

if ($userData['USR_EMAIL'] != '' && $userData['USR_EMAIL'] === $data['USR_EMAIL'] && ($userData['USR_AUTH_TYPE'] === '' || $userData['USR_AUTH_TYPE'] == 'MYSQL') ) {
    $aSetup = System::getEmailConfiguration();
    if (count($aSetup) == 0 || !isset($aSetup['MESS_ENGINE'])) {
        G::SendTemporalMessage ('ID_EMAIL_ENGINE_IS_NOT_ENABLED', "warning");
        G::header('location: forgotPassword');
        die;
    }

    $newPass = G::generate_password();

    $aData['USR_UID'] = $userData['USR_UID'];
    $aData['USR_PASSWORD'] = Bootstrap::hashPassword($newPass);
    $oUserProperty = new UsersProperties();
    $aUserPropertyData = $oUserProperty->load($aData['USR_UID']);
    if (is_array($aUserPropertyData)) {
        $aUserPropertyData['USR_LOGGED_NEXT_TIME'] = 1;
        $oUserProperty = $oUserProperty->update($aUserPropertyData);
    }
    /* **Save after sending the mail
      $rbacUser->update($aData);
      $user->update($aData);
    */

    $sFrom = G::buildFrom($aSetup, $sFrom);

    $sSubject = G::LoadTranslation('ID_RESET_PASSWORD').' - ProcessMaker' ;
    $msg = '<h3>ProcessMaker Forgot password Service</h3>';
    $msg .='<p>'.G::LoadTranslation('ID_YOUR_USERMANE_IS').' :  <strong>'.$userData['USR_USERNAME'].'</strong></p>';
    $msg .='<p>'.G::LoadTranslation('ID_YOUR_PASSWORD_IS').' :  <strong>'.$newPass.'</strong></p>';
    switch ($aSetup['MESS_ENGINE']) {
        case 'MAIL':
            $engine = G::LoadTranslation('ID_MESS_ENGINE_TYPE_1');
            break;
        case 'PHPMAILER':
            $engine = G::LoadTranslation('ID_MESS_ENGINE_TYPE_2');
            break;
        case 'OPENMAIL':
            $engine = G::LoadTranslation('ID_MESS_ENGINE_TYPE_3');
            break;
    }

    $sBody = "
  <table style=\"background-color: white; font-family: Arial,Helvetica,sans-serif; color: black; font-size: 11px; text-align: left;\" cellpadding='10' cellspacing='0' width='100%'>
  <tbody><tr><td><img id='logo' src='http://".$_SERVER['SERVER_NAME']."/images/processmaker.logo.jpg' /></td></tr>
  <tr><td style='font-size: 14px;'>$msg</td></tr>
  <tr><td style='vertical-align:middel;'>
  <br /><hr><b>This Business Process is powered by ProcessMaker ver. ".System::getVersion().".<b><br />
  <a href='http://www.processmaker.com' style='color:#c40000;'>www.processmaker.com</a><br /></td>
  </tr></tbody></table>";

    $oSpool = new SpoolRun();

    $oSpool->setConfig($aSetup);
    $oSpool->create(array(
        'msg_uid'          => '',
        'app_uid'          => '',
        'del_index'        => 0,
        'app_msg_type'     => 'TEST',
        'app_msg_subject'  => $sSubject,
        'app_msg_from'     => $sFrom,
        'app_msg_to'       => $data['USR_EMAIL'],
        'app_msg_body'     => $sBody,
        'app_msg_cc'       => '',
        'app_msg_bcc'      => '',
        'app_msg_attach'   => '',
        'app_msg_template' => '',
        'app_msg_status'   => 'pending',
        'app_msg_attach'=>''
    ));

    try {
        $oSpool->sendMail();
        $rbacUser->update($aData);
        $user->update($aData);
        G::header("location: login");
        G::SendTemporalMessage('ID_NEW_PASSWORD_SENT', "info");
    } catch (phpmailerException $e) {
        G::header("location: login");
        G::SendTemporalMessage(G::LoadTranslation('MISSING_OR_NOT_CONFIGURED_SMTP'), "warning", 'string');
    } catch (Exception $e) {
        G::header("location: login");
        G::SendTemporalMessage($e->getMessage(), "warning", 'string');
    }
} else {
    if ($userData['USR_AUTH_TYPE'] === '' || $userData['USR_AUTH_TYPE'] === 'MYSQL') {
        $msg = G::LoadTranslation('ID_USER') . ' ' . htmlentities($data['USR_USERNAME'], ENT_QUOTES, 'UTF-8') . ' '. G::LoadTranslation('ID_IS_NOT_REGISTERED');
    } else {
        $msg = G::LoadTranslation('ID_USER_NOT_FUNCTIONALITY');
    }
    G::SendTemporalMessage ($msg, "warning", 'string');
    G::header('location: forgotPassword');
}

