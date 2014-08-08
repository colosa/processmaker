<?php
/**
 * adminProxy.php
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
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * For more information, contact Colosa Inc, 2566 Le Jeune Rd.,
 * Coral Gables, FL, 33134, USA, or email info@colosa.com.
 *
 */

class adminProxy extends HttpProxyController
{
    public function saveSystemConf($httpData)
    {
        G::loadClass('system');
        $envFile = PATH_CONFIG . 'env.ini';
        $updateRedirector = false;
        $restart = false;

        if (!file_exists($envFile) ) {
            if (!is_writable(PATH_CONFIG)) {
                throw new Exception('The enviroment config directory is not writable. <br/>Please give write permission to directory: /workflow/engine/config');
            }
            $content  = ";\r\n";
            $content .= "; ProcessMaker System Bootstrap Configuration\r\n";
            $content .= ";\r\n";
            file_put_contents($envFile, $content);
            //@chmod($envFile, 0777);
        } else {
            if (!is_writable($envFile)) {
                throw new Exception('The enviroment ini file file is not writable. <br/>Please give write permission to file: /workflow/engine/config/env.ini');
            }
        }

        $sysConf = System::getSystemConfiguration($envFile);

        $updatedConf = array();

        if ($sysConf['default_lang'] != $httpData->default_lang) {
            $updatedConf['default_lang'] = $sysConf['default_lang'] = $httpData->default_lang;
            $updateRedirector = true;
        }

        if ($sysConf['default_skin'] != $httpData->default_skin) {
            $updatedConf['default_skin'] = $sysConf['default_skin'] = $httpData->default_skin;
            $updateRedirector = true;
        }

        if ($sysConf['time_zone'] != $httpData->time_zone) {
            $updatedConf['time_zone'] = $httpData->time_zone;
        }

        $httpData->memory_limit .= 'M';
        if ($sysConf['memory_limit'] != $httpData->memory_limit) {
            $updatedConf['memory_limit'] = $httpData->memory_limit;
        }

        if ($sysConf['proxy_host'] != $httpData->proxy_host) {
            $updatedConf['proxy_host'] = $httpData->proxy_host;
        }

        if ($sysConf['proxy_port'] != $httpData->proxy_port) {
            $updatedConf['proxy_port'] = $httpData->proxy_port;
        }

        if ($sysConf['proxy_user'] != $httpData->proxy_user) {
            $updatedConf['proxy_user'] = $httpData->proxy_user;
        }

        if ($sysConf['proxy_pass'] != $httpData->proxy_pass) {
            $updatedConf['proxy_pass'] = G::encrypt($httpData->proxy_pass, 'proxy_pass');
        }

        $sessionGcMaxlifetime = ini_get('session.gc_maxlifetime');
        if (($httpData->max_life_time != "")  && ($sessionGcMaxlifetime != $httpData->max_life_time)) {
            if (!isset($sysConf['session.gc_maxlifetime']) || ($sysConf['session.gc_maxlifetime'] != $httpData->max_life_time)) {
                $updatedConf['session.gc_maxlifetime'] = $httpData->max_life_time;
            }
        }

        if ($updateRedirector) {
            if (!file_exists(PATH_HTML . 'index.html')) {
                throw new Exception('The index.html file is not writable on workflow/public_html directory.');
            } else {
                if (!is_writable(PATH_HTML . 'index.html')) {
                    throw new Exception(G::LoadTranslation('ID_INDEX_NOT_WRITEABLE') . ' /workflow/public_html/index.html');
                }
            }

            System::updateIndexFile(array(
              'lang' => $sysConf['default_lang'],
              'skin' => $sysConf['default_skin']
            ));

            $restart = true;
        }

        G::update_php_ini($envFile, $updatedConf);
        if (substr($sysConf['default_skin'], 0, 2) == 'ux') {
            $urlPart = '/main/login';
        } else {
            $urlPart = '/login/login';
        }

        $this->success = true;
        $this->restart = $restart;
        $this->url     = "/sys" .  SYS_SYS . "/" . (($sysConf["default_lang"] != "")? $sysConf["default_lang"] : "en") . "/" . $sysConf["default_skin"] . $urlPart;
        $this->message = 'Saved Successfully';
    }

    public function uxUserUpdate($httpData)
    {
        require_once 'classes/model/Users.php';
        $data = G::json_decode($httpData->users);
        $list = array();

        if (!is_array($data)) {
            $list[0] = (array) $data ;
        } else {
            $list =  $data;
        }

        $oRoles = new Roles();
        $rows = array();

        foreach ($list as $value) {
            $value = (array) $value;
            $user = UsersPeer::retrieveByPK($value['USR_UID']);
            $user->setUsrUx($value['USR_UX']);
            $user->save();

            $row = $user->toArray(BasePeer::TYPE_FIELDNAME);
            try {
                $uRole = $oRoles->loadByCode($row['USR_ROLE']);
            } catch (exception $oError) {
                $uRole['ROL_NAME'] = G::loadTranslation( 'ID_DELETED' );
            }
            $row['USR_ROLE_ID'] = $row['USR_ROLE'];
            $row['USR_ROLE'] = isset($uRole['ROL_NAME']) ? ($uRole['ROL_NAME'] != '' ? $uRole['ROL_NAME'] : $uRole['USR_ROLE']) : $uRole['USR_ROLE'];

            $uxList = self::getUxTypesList();
            $row['USR_UX'] = $uxList[$user->getUsrUx()];
            $rows[] = $row;
        }

        if (count($rows) == 1) {
            $retRow = $rows[0];
        } else {
            $retRow = $rows;
        }

        return array('success' => true, 'message'=>'done', 'users'=>$retRow);
    }

    public function calendarValidate($httpData)
    {
        $httpData=array_unique((array)$httpData);
        $message = '';
        $oldName = isset($_POST['oldName'])? $_POST['oldName']:'';

        switch ($_POST['action']){
            case 'calendarName':
                require_once ('classes/model/CalendarDefinition.php');
                $oCalendar  = new CalendarDefinition();
                $aCalendars = $oCalendar->getCalendarList(false,true);
                $aCalendarDefinitions = end($aCalendars);

                foreach ($aCalendarDefinitions as $aDefinitions) {
                    if (trim($_POST['name'])=='') {
                        $validated = false;
                        $message  = G::loadTranslation('ID_CALENDAR_INVALID_NAME');
                        break;
                    }
                    if ($aDefinitions['CALENDAR_NAME'] != $_POST['name']) {
                        $validated = true;
                    } else {
                        if ($aDefinitions['CALENDAR_NAME'] != $oldName) {
                            $validated = false;
                            $message  = G::loadTranslation('ID_CALENDAR_INVALID_NAME');
                            break;
                        }
                    }
                }
                break;
            case 'calendarDates':
                $validated = false;
                $message = G::loadTranslation('ID_CALENDAR_INVALID_WORK_DATES');
                break;
        }
        return $message;
    }

    public function uxGroupUpdate($httpData)
    {
        G::LoadClass('groups');
        $groups = new Groups();
        $users = $groups->getUsersOfGroup($httpData->GRP_UID);
        $success = true;
        $usersAdmin = '';
        foreach ($users as $user) {
            if ($user['USR_ROLE'] == 'PROCESSMAKER_ADMIN' && ($httpData->GRP_UX == 'SIMPLIFIED' || $httpData->GRP_UX == 'SINGLE')) {
                $success = false;
                $usersAdmin .= $user['USR_FIRSTNAME'] . ' ' . $user['USR_LASTNAME'] . ', ';
            }
        }
        if ($success) {
            $group = GroupwfPeer::retrieveByPK($httpData->GRP_UID);
            $group->setGrpUx($httpData->GRP_UX);
            $group->save();
        }
        return array('success' => $success, 'users' => $usersAdmin);
    }

    public function getUxTypesList($type = 'assoc')
    {
        $list = array();

        if ($type == 'assoc') {
            $list = array(
                'NORMAL'     => G::loadTranslation('ID_UXS_NORMAL'),
                'SIMPLIFIED' => G::loadTranslation('ID_UXS_SIMPLIFIED'),
                'SWITCHABLE' => G::loadTranslation('ID_UXS_SWITCHABLE'),
                'SINGLE'     => G::loadTranslation('ID_UXS_SINGLE')
            );
        } else {
            $list = array(
                array('NORMAL',     G::loadTranslation('ID_UXS_NORMAL') ),
                array('SIMPLIFIED', G::loadTranslation('ID_UXS_SIMPLIFIED') ),
                array('SWITCHABLE', G::loadTranslation('ID_UXS_SWITCHABLE') ),
                array('SINGLE',     G::loadTranslation('ID_UXS_SINGLE') )
            );
        }
        return $list;
    }

    public function calendarSave()
    {
        //{ $_POST['BUSINESS_DAY']
        $businessDayArray = G::json_decode($_POST['BUSINESS_DAY']);
        $businessDayFixArray = array();
        for ($i=0; $i<sizeof($businessDayArray); $i++) {
            $businessDayFixArray[$i+1]['CALENDAR_BUSINESS_DAY'] = $businessDayArray[$i]->CALENDAR_BUSINESS_DAY;
            $businessDayFixArray[$i+1]['CALENDAR_BUSINESS_START'] = $businessDayArray[$i]->CALENDAR_BUSINESS_START;
            $businessDayFixArray[$i+1]['CALENDAR_BUSINESS_END'] = $businessDayArray[$i]->CALENDAR_BUSINESS_END;
        }
        $_POST['BUSINESS_DAY'] = $businessDayFixArray;
        //}

        //{ $_POST['CALENDAR_WORK_DAYS']
        $calendarWorkDaysArray = G::json_decode($_POST['CALENDAR_WORK_DAYS']);
        $calendarWorkDaysFixArray = array();
        for ($i=0; $i<sizeof($calendarWorkDaysArray); $i++) {
            $calendarWorkDaysFixArray[$i] = $calendarWorkDaysArray[$i]."";
        }
        $_POST['CALENDAR_WORK_DAYS'] = $calendarWorkDaysFixArray;
        //}

        //{ $_POST['HOLIDAY']
        $holidayArray = G::json_decode($_POST['HOLIDAY']);
        $holidayFixArray = array();
        for ($i=0; $i<sizeof($holidayArray); $i++) {
            $holidayFixArray[$i+1]['CALENDAR_HOLIDAY_NAME'] = $holidayArray[$i]->CALENDAR_HOLIDAY_NAME;
            $holidayFixArray[$i+1]['CALENDAR_HOLIDAY_START'] = $holidayArray[$i]->CALENDAR_HOLIDAY_START;
            $holidayFixArray[$i+1]['CALENDAR_HOLIDAY_END'] = $holidayArray[$i]->CALENDAR_HOLIDAY_END;
        }
        $_POST['HOLIDAY'] = $holidayFixArray;
        //}

        //[ CALENDAR_STATUS BUSINESS_DAY_STATUS HOLIDAY_STATUS
        if ($_POST['BUSINESS_DAY_STATUS']=="INACTIVE") {
            unset($_POST['BUSINESS_DAY_STATUS']);
        }
        if ($_POST['HOLIDAY_STATUS']=="INACTIVE") {
            unset($_POST['HOLIDAY_STATUS']);
        }
        //]

        $form = $_POST;
        G::LoadClass('calendar');
        $calendarObj=new calendar();
        $calendarObj->saveCalendarInfo($form);
        echo "{success: true}";
    }

    /**
     * getting the kind of the authentication source
     * @param object $params
     * @return array $data
     */
    public function testingOption($params)
    {
        $data['success'] = true;
        $data['optionAuthS'] = $params->optionAuthS;
        return $data;

    }

    /**
     * saving the authentication source data
     * @param object $params
     * @return array $data
     */
    public function saveAuthSources($params)
    {
        global $RBAC;
        if ($RBAC->userCanAccess('PM_SETUP_ADVANCE') != 1) {
            G::SendTemporalMessage('ID_USER_HAVENT_RIGHTS_PAGE', 'error', 'labels');
            G::header('location: ../login/login');
            die;
        }
        $aCommonFields = array('AUTH_SOURCE_UID',
                               'AUTH_SOURCE_NAME',
                               'AUTH_SOURCE_PROVIDER',
                               'AUTH_SOURCE_SERVER_NAME',
                               'AUTH_SOURCE_PORT',
                               'AUTH_SOURCE_ENABLED_TLS',
                               'AUTH_ANONYMOUS',
                               'AUTH_SOURCE_SEARCH_USER',
                               'AUTH_SOURCE_PASSWORD',
                               'AUTH_SOURCE_VERSION',
                               'AUTH_SOURCE_BASE_DN',
                               'AUTH_SOURCE_OBJECT_CLASSES',
                               'AUTH_SOURCE_ATTRIBUTES');

        $aFields = $aData = array();

        unset($params->PHPSESSID);
        foreach ($params as $sField => $sValue) {
            if (in_array($sField, $aCommonFields)) {
                $aFields[$sField] = (($sField=='AUTH_SOURCE_ENABLED_TLS' || $sField=='AUTH_ANONYMOUS'))? ($sValue=='yes')?1:0 :$sValue;
            } else {
                $aData[$sField] = ($sValue=='Active Directory')?'ad':$sValue;
            }
        }
        $aFields['AUTH_SOURCE_DATA'] = $aData;
        if ($aFields['AUTH_SOURCE_UID'] == '') {
            $RBAC->createAuthSource($aFields);
        } else {
            $RBAC->updateAuthSource($aFields);
        }
        $data=array();
        $data['success'] = true;
        return $data;
    }

    /**
     * for Test email configuration
     * @autor Alvaro  <alvaro@colosa.com>
    */
    public function testConnection($params)
    {
        G::LoadClass('net');
        G::LoadThirdParty('phpmailer', 'class.smtp');

        if ($_POST['typeTest'] == 'MAIL') {
            $eregMail = "/^[0-9a-zA-Z]+(?:[._][0-9a-zA-Z]+)*@[0-9a-zA-Z]+(?:[._-][0-9a-zA-Z]+)*\.[0-9a-zA-Z]{2,3}$/";

            define("SUCCESSFUL", 'SUCCESSFUL');
            define("FAILED", 'FAILED');
            $mail_to                = $_POST['mail_to'];
            $send_test_mail         = $_POST['send_test_mail'];
            $_POST['FROM_EMAIL']    = ($_POST["from_mail"] != "" && preg_match($eregMail, $_POST["from_mail"]))? $_POST["from_mail"] : "";
            $_POST['FROM_NAME']     = $_POST["from_name"] != "" ? $_POST["from_name"] : G::LoadTranslation("ID_MESS_TEST_BODY");
            $_POST['MESS_ENGINE']   = 'MAIL';
            $_POST['MESS_SERVER']   = 'localhost';
            $_POST['MESS_PORT']     = 25;
            $_POST['MESS_ACCOUNT']  = $mail_to;
            $_POST['MESS_PASSWORD'] = '';
            $_POST['TO']            = $mail_to;
            $_POST['MESS_RAUTH']    = true;

            try {
                $resp = $this->sendTestMail();
            } catch (Exception $error) {
                $resp = new stdclass();
                $resp->status = false;
                $resp->msg = $error->getMessage();
            }

            $response = array('success' => $resp->status);

            if ($resp->status == false) {
                $response['msg'] = G::LoadTranslation('ID_SENDMAIL_NOT_INSTALLED');
            }
            echo G::json_encode($response);
            die;
        }

        $step = $_POST['step'];
        $server = $_POST['server'];
        $user = $_POST['user'];
        $passwd = $_POST['passwd'];
        $fromMail = $_POST["fromMail"];
        $passwdHide = $_POST['passwdHide'];

        if (trim($passwdHide) != '') {
            $passwd = $passwdHide;
            $passwdHide = '';
        }

        $passwdDec = G::decrypt($passwd,'EMAILENCRYPT');
        $auxPass = explode('hash:', $passwdDec);
        if (count($auxPass) > 1) {
            if (count($auxPass) == 2) {
                $passwd = $auxPass[1];
            } else {
                array_shift($auxPass);
                $passwd = implode('', $auxPass);
            }
        }
        $_POST['passwd'] = $passwd;

        $port = $_POST['port'];
        $auth_required = $_POST['req_auth'];
        $UseSecureCon = $_POST['UseSecureCon'];
        $SendaTestMail = $_POST['SendaTestMail'];
        $Mailto = $_POST['eMailto'];
        $SMTPSecure  = $_POST['UseSecureCon'];

        $Server = new NET($server);
        $smtp = new SMTP;

        $timeout = 10;
        $hostinfo = array();
        $srv=$_POST['server'];

        switch ($step) {
            case 1:
                $this->success = $Server->getErrno() == 0;
                $this->msg = $this->result ? 'success' : $Server->error;
                break;
            case 2:
                $Server->scannPort($port);

                $this->success = $Server->getErrno() == 0; //'Successfull'.$smtp->status;
                $this->msg = $this->result ? '' : $Server->error;
                break;
            case 3:   //try to connect to host
                if (preg_match('/^(.+):([0-9]+)$/', $srv, $hostinfo)) {
                    $server = $hostinfo[1];
                    $port = $hostinfo[2];
                } else {
                    $host = $srv;
                }

                $tls = (strtoupper($SMTPSecure) == 'tls');
                $ssl = (strtoupper($SMTPSecure) == 'ssl');

                $this->success = $smtp->Connect(($ssl ? 'ssl://':'').$server, $port, $timeout);
                $this->msg = $this->result ? '' : $Server->error;
                break;
            case 4:  //try login to host
                if ($auth_required == 'true') {
                    try {
                        if (preg_match('/^(.+):([0-9]+)$/', $srv, $hostinfo)) {
                            $server = $hostinfo[1];
                            $port = $hostinfo[2];
                        } else {
                            $server = $srv;
                        }
                        if (strtoupper($UseSecureCon)=='TLS') {
                            $tls = 'tls';
                        }

                        if (strtoupper($UseSecureCon)=='SSL') {
                            $tls = 'ssl';
                        }

                        $tls = (strtoupper($UseSecureCon) == 'tls');
                        $ssl = (strtoupper($UseSecureCon) == 'ssl');
                        $server = $_POST['server'];

                        if (strtoupper($UseSecureCon) == 'SSL') {
                            $resp = $smtp->Connect(('ssl://').$server, $port, $timeout);
                        } else {
                            $resp = $smtp->Connect($server, $port, $timeout);
                        }
                        if ($resp) {
                            $hello = $_SERVER['SERVER_NAME'];
                            $smtp->Hello($hello);
                            if (strtoupper($UseSecureCon) == 'TLS') {
                                $smtp->Hello($hello);
                            }
                            if ($smtp->Authenticate($user, $passwd) ) {
                                $this->success = true;
                            } else {
                                if (strtoupper($UseSecureCon) == 'TLS') {
                                    $this->success = true;
                                } else {
                                    $this->success = false;
                                    $smtpError = $smtp->getError();
                                    $this->msg = $smtpError['error'];
                                    // $this->msg = $smtp->error['error'];
                                }
                            }
                        } else {
                            $this->success = false;
                            $smtpError = $smtp->getError();
                            $this->msg = $smtpError['error'];
                            // $this->msg = $smtp->error['error'];
                        }
                    } catch (Exception $e) {
                        $this->success = false;
                        $this->msg = $e->getMessage();
                    }
                } else {
                    $this->success = true;
                    $this->msg = 'No authentication required!';
                }
                break;
            case 5:
                if ($SendaTestMail == 'true') {
                    try {
                        $eregMail = "/^[0-9a-zA-Z]+(?:[._][0-9a-zA-Z]+)*@[0-9a-zA-Z]+(?:[._-][0-9a-zA-Z]+)*\.[0-9a-zA-Z]{2,3}$/";

                        $_POST['FROM_EMAIL']    = ($fromMail != "" && preg_match($eregMail, $fromMail))? $fromMail : "";
                        $_POST['FROM_NAME']     = $_POST["fromName"] != "" ? $_POST["fromName"] : G::LoadTranslation("ID_MESS_TEST_BODY");
                        $_POST['MESS_ENGINE']   = 'PHPMAILER';
                        $_POST['MESS_SERVER']   = $server;
                        $_POST['MESS_PORT']     = $port;
                        $_POST['MESS_ACCOUNT']  = $user;
                        $_POST['MESS_PASSWORD'] = $passwd;
                        $_POST['TO'] = $Mailto;

                        if ($auth_required == 'true') {
                            $_POST['MESS_RAUTH'] = true;
                        } else {
                            $_POST['MESS_RAUTH'] = false;
                        }
                        if (strtolower($_POST["UseSecureCon"]) != "no") {
                            $_POST["SMTPSecure"] = $_POST["UseSecureCon"];
                        }
                        /*
                        if ($_POST['UseSecureCon'] == 'ssl') {
                            $_POST['MESS_SERVER'] = 'ssl://'.$_POST['MESS_SERVER'];
                        }
                        */
                        $resp = $this->sendTestMail();

                        if ($resp->status == '1') {
                            $this->success=true;
                        } else {
                            $this->success=false;
                            $smtpError = $smtp->getError();
                            $this->msg = $smtpError['error'];
                            // $this->msg = $smtp->error['error'];
                        }
                    } catch (Exception $e) {
                        $this->success = false;
                        $this->msg = $e->getMessage();
                    }
                } else {
                    $this->success=true;
                    $this->msg='jump this step';
                }
                break;
        }
    }

    /**
     * for send email configuration
     * @autor Alvaro  <alvaro@colosa.com>
     */
    public function sendTestMail()
    {
        global $G_PUBLISH;
        G::LoadClass("system");
        G::LoadClass('spool');

        $aConfiguration = array(
            'MESS_ENGINE'    => $_POST['MESS_ENGINE'],
            'MESS_SERVER'    => $_POST['MESS_SERVER'],
            'MESS_PORT'      => $_POST['MESS_PORT'],
            'MESS_ACCOUNT'   => $_POST['MESS_ACCOUNT'],
            'MESS_PASSWORD'  => $_POST['MESS_PASSWORD'],
            'MESS_FROM_NAME' => $_POST["FROM_NAME"],
            'MESS_FROM_MAIL' => $_POST["FROM_EMAIL"],
            'MESS_RAUTH'     => $_POST['MESS_RAUTH'],
            'SMTPSecure'     => isset($_POST['SMTPSecure'])?$_POST['SMTPSecure']:'none'
        );

        $sFrom = G::buildFrom($aConfiguration);

        $sSubject = G::LoadTranslation('ID_MESS_TEST_SUBJECT');
        $msg      = G::LoadTranslation('ID_MESS_TEST_BODY');

        switch ($_POST['MESS_ENGINE']) {
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

        $sBodyPre  = new TemplatePower(PATH_TPL . 'admin' . PATH_SEP . 'email.tpl');
        $sBodyPre->prepare();
        $sBodyPre->assign('server', $_SERVER['SERVER_NAME']);
        $sBodyPre->assign('date', date('H:i:s'));
        $sBodyPre->assign('ver', System::getVersion());
        $sBodyPre->assign('engine', $engine);
        $sBodyPre->assign('msg', $msg);
        $sBody = $sBodyPre->getOutputContent();

        $oSpool = new spoolRun();

        $oSpool->setConfig($aConfiguration);

        $oSpool->create(
            array(
                'msg_uid'          => '',
                'app_uid'          => '',
                'del_index'        => 0,
                'app_msg_type'     => 'TEST',
                'app_msg_subject'  => $sSubject,
                'app_msg_from'     => $sFrom,
                'app_msg_to'       => $_POST['TO'],
                'app_msg_body'     => $sBody,
                'app_msg_cc'       => '',
                'app_msg_bcc'      => '',
                'app_msg_attach'   => '',
                'app_msg_template' => '',
                'app_msg_status'   => 'pending',
                'app_msg_attach'=>'' // Added By Ankit
            )
        );

        $oSpool->sendMail();
        $G_PUBLISH = new Publisher();

        if ($oSpool->status == 'sent') {
            $o->status = true;
            $o->success = true;
            $o->msg = G::LoadTranslation('ID_MAIL_TEST_SUCCESS');
        } else {
            $o->status = false;
            $o->success = false;
            $o->msg = $oSpool->error;
        }
        return $o;
    }

    /**
     * getting Save email configuration
     * @autor Alvaro  <alvaro@colosa.com>
    */
    public function saveConfiguration()
    {
        require_once 'classes/model/Configuration.php';
        try {
            $oConfiguration = new Configuration();
            $aFields['MESS_PASSWORD']  = $_POST['passwd'];

            if ($_POST['passwdHide'] != '') {
                $aFields['MESS_PASSWORD'] = $_POST['passwdHide'];
            }

            $aFields['MESS_PASSWORD_HIDDEN'] = '';
            $passwd = $aFields['MESS_PASSWORD'];
            $passwdDec = G::decrypt($passwd,'EMAILENCRYPT');
            $auxPass = explode('hash:', $passwdDec);
            if (count($auxPass) > 1) {
                if (count($auxPass) == 2) {
                    $passwd = $auxPass[1];
                } else {
                    array_shift($auxPass);
                    $passwd = implode('', $auxPass);
                }
            }
            $aFields['MESS_PASSWORD'] = $passwd;

            if ($aFields['MESS_PASSWORD'] != '') {
                $aFields['MESS_PASSWORD'] = 'hash:'.$aFields['MESS_PASSWORD'];
                $aFields['MESS_PASSWORD'] = G::encrypt($aFields['MESS_PASSWORD'],'EMAILENCRYPT');
            }

            $aFields['MESS_ENABLED']             = isset($_POST['EnableEmailNotifications']) ? $_POST['EnableEmailNotifications'] : '';
            $aFields['MESS_ENABLED']             = ($aFields['MESS_ENABLED'] == 'true') ? '1' : $aFields['MESS_ENABLED'];
            $aFields['MESS_ENGINE']              = $_POST['EmailEngine'];
            $aFields['MESS_SERVER']              = trim($_POST['server']);
            $aFields['MESS_RAUTH']               = isset($_POST['req_auth']) ? $_POST['req_auth'] : '';
            $aFields['MESS_RAUTH']               = ($aFields['MESS_RAUTH'] == 'true') ? true : $aFields['MESS_RAUTH'];
            $aFields['MESS_PORT']                = $_POST['port'];
            $aFields['MESS_ACCOUNT']             = $_POST['from'];
            $aFields['MESS_BACKGROUND']          = '';//isset($_POST['background']) ? $_POST['background'] : '';
            $aFields['MESS_EXECUTE_EVERY']       = '';//$_POST['form']['MESS_EXECUTE_EVERY'];
            $aFields['MESS_SEND_MAX']            = '';//$_POST['form']['MESS_SEND_MAX'];
            $aFields['SMTPSecure']               = $_POST['UseSecureCon'];
            $aFields['SMTPSecure']               = ($aFields['SMTPSecure'] == 'No') ? 'none' : $aFields['SMTPSecure'];
            $aFields['MAIL_TO']                  = $_POST['eMailto'];
            $aFields['MESS_FROM_NAME']           = $_POST['FromName'];
            $aFields['MESS_TRY_SEND_INMEDIATLY'] = $_POST['SendaTestMail'];//isset($_POST['form']['MESS_TRY_SEND_INMEDIATLY']) ? $_POST['form']['MESS_TRY_SEND_INMEDIATLY'] : '';
            $aFields['MESS_TRY_SEND_INMEDIATLY'] = ($aFields['MESS_TRY_SEND_INMEDIATLY'] == 'true') ? '1' : $aFields['MESS_TRY_SEND_INMEDIATLY'];
            $aFields["MESS_FROM_MAIL"]           = $_POST["fromMail"];

            $CfgUid='Emails';
            $ObjUid='';
            $ProUid='';
            $UsrUid='';
            $AppUid='';

            if ($oConfiguration->exists($CfgUid, $ObjUid, $ProUid, $UsrUid, $AppUid)) {
                $oConfiguration->update(
                    array (
                      'CFG_UID'   => 'Emails',
                      'OBJ_UID'   => '',
                      'CFG_VALUE' => serialize($aFields),
                      'PRO_UID'   => '',
                      'USR_UID'   => '',
                      'APP_UID'   => ''
                    )
                );
                $this->success='true';
                $this->msg='Saved';
            } else {
                $oConfiguration->create(
                    array(
                      'CFG_UID'   => 'Emails',
                      'OBJ_UID'   => '',
                      'CFG_VALUE' => serialize($aFields),
                      'PRO_UID'   => '',
                      'USR_UID'   => '',
                      'APP_UID'   => ''
                    )
                );
                $this->success='true';
                $this->msg='Saved';
            }
        } catch (Exception $e) {
            $this->success= false;
            $this->msg = $e->getMessage();
        }
    }

    /**
     * loadFields for email configuration
     * @autor Alvaro  <alvaro@colosa.com>
     */
    public function loadFields()
    {
        G::loadClass('configuration');

        $oConfiguration = new Configurations();
        $oConfiguration->loadConfig($x, 'Emails','','','','');
        $fields = $oConfiguration->aConfig;
        if (count($fields) > 0) {
            $this->success = (count($fields) > 0);
            $passwd = $fields['MESS_PASSWORD'];
            $passwdDec = G::decrypt($passwd,'EMAILENCRYPT');
            $auxPass = explode('hash:', $passwdDec);
            if (count($auxPass) > 1) {
                if (count($auxPass) == 2) {
                    $passwd = $auxPass[1];
                } else {
                    array_shift($auxPass);
                    $passwd = implode('', $auxPass);
                }
            }
            $fields['MESS_PASSWORD'] = $passwd;
        }
        $this->data = $fields;
    }

    /**
     * get List Image
     * @param type $httpData
     */
    public function getListImage($httpData)
    {
        G::LoadClass('replacementLogo');
        $uplogo       = PATH_TPL . 'setup' . PATH_SEP . 'uplogo.html';
        $width        = "100%";
        $upload       = new replacementLogo();
        $aPhotoSelect = $upload->getNameLogo($_SESSION['USER_LOGGED']);
        $sPhotoSelect = trim($aPhotoSelect['DEFAULT_LOGO_NAME']);
        $check        = '';
        $ainfoSite    = explode("/",$_SERVER["REQUEST_URI"]);
        $dir          = PATH_DATA . "sites" . PATH_SEP . str_replace("sys", "", $ainfoSite[1]) . PATH_SEP . "files/logos";
        G::mk_dir ( $dir );
        $i      = 0;
        $images = array();

        /** if we have at least one image it's load  */
        if (file_exists($dir)) {
            if ($handle = opendir($dir)) {
                while (false !== ($file = readdir($handle))) {
                    if (($file != ".") && ($file != "..")) {
                        $extention      = explode(".", $file);
                        $aImageProp     = getimagesize($dir . '/' . $file, $info);
                        $sfileExtention = strtoupper($extention[count($extention)-1]);
                        if ( in_array($sfileExtention, array('JPG', 'JPEG', 'PNG', 'GIF') ) ) {
                            $check   = (!strcmp($file, $sPhotoSelect)) ? '/images/toadd.png' : '/images/delete.png';
                            $onclick = (strcmp($file, $sPhotoSelect)) ? "onclick ='deleteLogo(\" $file \");return false;'" : '';
                            if ($i == 0) {
                                $i++;
                            }
                            $i++;
                            $images[] = array(
                                'name'      => $file,
                                'size'      => '0',
                                'lastmod'   => '32',
                                'url'       => "../adminProxy/showLogoFile?id=".base64_encode($file),
                                'thumb_url' => "../adminProxy/showLogoFile?id=".base64_encode($file)
                            );
                        }
                    }
                }
                closedir($handle);
            }
        }
        $o = array('images' => $images);
        echo G::json_encode($o);
        exit();
    }

    /**
     * Change Name logo
     * @param type $snameLogo
     * @return type $snameLogo
    */
    public function changeNamelogo($snameLogo)
    {
        $snameLogo = preg_replace("/[áàâãª]/", "a", $snameLogo);
        $snameLogo = preg_replace("/[ÁÀÂÃ]/",  "A", $snameLogo);
        $snameLogo = preg_replace("/[ÍÌÎ]/",   "I", $snameLogo);
        $snameLogo = preg_replace("/[íìî]/",   "i", $snameLogo);
        $snameLogo = preg_replace("/[éèê]/",   "e", $snameLogo);
        $snameLogo = preg_replace("/[ÉÈÊ]/",   "E", $snameLogo);
        $snameLogo = preg_replace("/[óòôõº]/", "o", $snameLogo);
        $snameLogo = preg_replace("/[ÓÒÔÕ]/",  "O", $snameLogo);
        $snameLogo = preg_replace("/[úùû]/",   "u", $snameLogo);
        $snameLogo = preg_replace("/[ÚÙÛ]/",   "U", $snameLogo);
        $snameLogo = str_replace( "ç",         "c", $snameLogo);
        $snameLogo = str_replace( "Ç",         "C", $snameLogo);
        $snameLogo = str_replace( "[ñ]",       "n", $snameLogo);
        $snameLogo = str_replace( "[Ñ]",       "N", $snameLogo);
        return ($snameLogo);
    }

    /**
     * Create Thumb
     * @param type $img_file
     * @param type $ori_path
     * @param type $thumb_path
     * @param type $img_type
    */
    public function createThumb($img_file, $ori_path, $thumb_path, $img_type)
    {
        $path = $ori_path;
        $img  = $path.$img_file;
        switch ($img_type) {
            case "image/jpeg":
                $img_src = @imagecreatefromjpeg($img);
                break;
            case "image/pjpeg":
                $img_src = @imagecreatefromjpeg($img);
                break;
            case "image/png":
                $img_src = @imagecreatefrompng($img);
                break;
            case "image/x-png":
                $img_src = @imagecreatefrompng($img);
                break;
            case "image/gif":
                $img_src = @imagecreatefromgif($img);
                break;
        }
        $img_width   = imagesx($img_src);
        $img_height  = imagesy($img_src);
        $square_size = 100;
        // check width, height, or square
        if ($img_width == $img_height) {
            // square
            $tmp_width  = $square_size;
            $tmp_height = $square_size;
        } elseif ($img_height < $img_width) {
            // wide
            $tmp_height = $square_size;
            $tmp_width  = intval(($img_width / $img_height) * $square_size);
            if ($tmp_width % 2 != 0) {
                $tmp_width++;
            }
        } elseif ($img_height > $img_width) {
            $tmp_width  = $square_size;
            $tmp_height = intval(($img_height / $img_width) * $square_size);
            if (($tmp_height % 2) != 0) {
                $tmp_height++;
            }
        }
        $img_new = imagecreatetruecolor($tmp_width, $tmp_height);
        imagecopyresampled($img_new, $img_src, 0, 0, 0, 0,
                           $tmp_width, $tmp_height, $img_width, $img_height);

        // create temporary thumbnail and locate on the server
        $thumb = $thumb_path."thumb_".$img_file;
        switch ($img_type) {
            case "image/jpeg":
                imagejpeg($img_new, $thumb);
                break;
            case "image/pjpeg":
                imagejpeg($img_new, $thumb);
                break;
            case "image/png":
                imagepng($img_new, $thumb);
                break;
            case "image/x-png":
                imagepng($img_new, $thumb);
                break;
            case "image/gif":
                imagegif($img_new, $thumb);
                break;
        }

        // get tmp_image
        switch ($img_type) {
            case "image/jpeg":
                $img_thumb_square = imagecreatefromjpeg($thumb);
                break;
            case "image/pjpeg":
                $img_thumb_square = imagecreatefromjpeg($thumb);
                break;
            case "image/png":
                $img_thumb_square = imagecreatefrompng($thumb);
                break;
            case "image/x-png":
                $img_thumb_square = imagecreatefrompng($thumb);
                break;
            case "image/gif":
                $img_thumb_square = imagecreatefromgif($thumb);
                break;
        }
        $thumb_width  = imagesx($img_thumb_square);
        $thumb_height = imagesy($img_thumb_square);
        if ($thumb_height < $thumb_width) {
            // wide
            $x_src     = ($thumb_width - $square_size) / 2;
            $y_src     = 0;
            $img_final = imagecreatetruecolor($square_size, $square_size);
            imagecopy($img_final, $img_thumb_square, 0, 0,
                      $x_src, $y_src, $square_size, $square_size);
        } elseif ($thumb_height > $thumb_width) {
            // landscape
            $x_src = 0;
            $y_src = ($thumb_height - $square_size) / 2;
            $img_final = imagecreatetruecolor($square_size, $square_size);
            imagecopy($img_final, $img_thumb_square, 0, 0,
                      $x_src, $y_src, $square_size, $square_size);
        } else {
            $img_final = imagecreatetruecolor($square_size, $square_size);
            imagecopy($img_final, $img_thumb_square, 0, 0,
                    0, 0, $square_size, $square_size);
        }

        switch ($img_type) {
            case "image/jpeg":
                @imagejpeg($img_final, $thumb);
                break;
            case "image/pjpeg":
                @imagejpeg($img_final, $thumb);
                break;
            case "image/png":
                @imagepng($img_final, $thumb);
                break;
            case "image/x-png":
                @imagepng($img_final, $thumb);
                break;
            case "image/gif":
                @imagegif($img_final, $thumb);
                break;
        }
    }

    /**
     * Upload Image
     * @global type $_FILES
     */
    public function uploadImage()
    {
        //!dataSystem
        $ainfoSite = explode("/", $_SERVER["REQUEST_URI"]);
        $dir       = PATH_DATA."sites".PATH_SEP.str_replace("sys","",$ainfoSite[1]).PATH_SEP."files/logos";
        global $_FILES;

        //| 0-> non fail
        //| 1-> fail in de type of the image
        //| 2-> fail in de size of the image
        //| 3-> fail in de myme of the image
        $failed = 0;
        //!dataSystem

        $ori_dir   = $dir . '/img/ori/';
        $thumb_dir = $dir . '/img/thumbs/';

        $allowedType = array(
          'image/jpg', 'image/jpeg', 'image/pjpeg', 'image/gif', 'image/png', 'image/x-png'
        );
        $allowedTypeArray['index' . base64_encode('image/jpg')]   = IMAGETYPE_JPEG;
        $allowedTypeArray['index' . base64_encode('image/jpeg')]  = IMAGETYPE_JPEG;
        $allowedTypeArray['index' . base64_encode('image/pjpeg')] = IMAGETYPE_JPEG;
        $allowedTypeArray['index' . base64_encode('image/gif')]   = IMAGETYPE_GIF;
        $allowedTypeArray['index' . base64_encode('image/png')]   = IMAGETYPE_PNG;
        $allowedTypeArray['index' . base64_encode('image/x-png')] = IMAGETYPE_PNG;

        $uploaded = 0;
        $failed   = 0;

        if (in_array($_FILES['img']['type'], $allowedType)) {
             // max upload file is 500 KB
            if ($_FILES['img']['size'] <= 500000) {
                $formf     = $_FILES['img'];
                $namefile  = $formf['name'];
                $typefile  = $formf['type'];
                $errorfile = $formf['error'];
                $tmpFile   = $formf['tmp_name'];
                $aMessage1 = array();
                $fileName  = trim(str_replace(' ', '_', $namefile));
                $fileName  = self::changeNamelogo($fileName);
                G::uploadFile($tmpFile, $dir, 'tmp' . $fileName);
                try {
                    if (extension_loaded('exif')) {
                        $typeMime = exif_imagetype($dir . '/'. 'tmp'.$fileName);
                    } else {
                        $arrayInfo = getimagesize($dir . '/' . 'tmp' . $fileName);
                        $typeMime  = $arrayInfo[2];
                    }
                    if ($typeMime == $allowedTypeArray['index' . base64_encode($_FILES['img']['type'])]) {
                        $error = false;
                        try {
                            list($imageWidth, $imageHeight, $imageType) = @getimagesize($dir . '/' . 'tmp' . $fileName);
                            G::resizeImage($dir . '/tmp' . $fileName, $imageWidth, 49, $dir . '/' . $fileName);
                        } catch (Exception $e) {
                            $error = $e->getMessage();
                        }
                        $uploaded++;
                    } else {
                        $failed = "3";
                    }
                    unlink ($dir . '/tmp' . $fileName);
                } catch (Exception $e) {
                    $failed = "3";
                }
            } else {
                $failed = "2";
            }
        } elseif ($_FILES['img']['type'] != '') {
            $failed = "1";
        }

        echo '{success: true, failed: ' . $failed . ', uploaded: ' . $uploaded . ', type: "' . $_FILES['img']['type'] . '"}';
        exit();
    }

    /**
     * Get Name Current Logo
     * @return type
     */
    public function getNameCurrentLogo()
    {
        G::LoadClass('replacementLogo');
        $upload       = new replacementLogo();
        $aPhotoSelect = $upload->getNameLogo($_SESSION['USER_LOGGED']);
        $sPhotoSelect = trim($aPhotoSelect['DEFAULT_LOGO_NAME']);
        return $sPhotoSelect;
    }

    /**
     * compare Name Current Logo
     * @param type $selectLogo
     * @return type int value
     */
    public function isCurrentLogo()
    {
        $arrayImg   = explode(";", $_POST['selectLogo']);
        foreach ($arrayImg as $imgname) {
            if ($imgname != "") {
                if ( strcmp($imgname, self::getNameCurrentLogo()) == 0 ) {
                    echo '{success: true}';
                    exit();
                }
            }
        }
        echo '{success: false}';
        exit();
    }

    /**
     *
     * Delete Image from the list
     * @param
     * @return string '{success: true | false}'
     */
    public function deleteImage()
    {
        //!dataSystem
        $ainfoSite = explode("/", $_SERVER["REQUEST_URI"]);
        $dir       = PATH_DATA . "sites" . PATH_SEP . str_replace("sys", "", $ainfoSite[1]) . PATH_SEP . "files/logos";
        global $_FILES;
        //!dataSystem

        $dir        = $dir;
        $dir_thumbs = $dir;

        $arrayImg   = explode(";", $_POST['images']);
        foreach ($arrayImg as $imgname) {
            if ($imgname != "") {
                if ( strcmp($imgname, self::getNameCurrentLogo()) != 0 ) {
                    if (file_exists($dir . '/' . $imgname)) {
                        unlink ($dir . '/' . $imgname);
                    }
                    if (file_exists($dir . '/tmp' . $imgname)) {
                        unlink ($dir . '/tmp' . $imgname);
                    }
                } else {
                    echo '{success: false}';
                    exit();
                }
            }
        }
        echo '{success: true}';
        exit();
    }

    /**
     * Replacement Logo
     * @global type $_REQUEST
     * @global type $RBAC
     */
    public function replacementLogo()
    {
        global $_REQUEST;
        $sfunction        = $_REQUEST['nameFunction'];
        $_GET['NAMELOGO'] = $_REQUEST['NAMELOGO'];

        try {
            global $RBAC;
            switch ($RBAC->userCanAccess('PM_LOGIN')) {
                case -2:
                    G::SendTemporalMessage('ID_USER_HAVENT_RIGHTS_SYSTEM', 'error', 'labels');
                    G::header('location: ../login/login');
                    die;
                    break;
                case -1:
                    G::SendTemporalMessage('ID_USER_HAVENT_RIGHTS_PAGE', 'error', 'labels');
                    G::header('location: ../login/login');
                    die;
                    break;
            }

            switch ($sfunction) {
                case 'replacementLogo':
                    $snameLogo = urldecode($_GET['NAMELOGO']);
                    $snameLogo = trim($snameLogo);
                    $snameLogo = self::changeNamelogo($snameLogo);
                    G::loadClass('configuration');
                    $oConf = new Configurations;
                    $aConf = Array(
                        'WORKSPACE_LOGO_NAME' => SYS_SYS,
                        'DEFAULT_LOGO_NAME'   => $snameLogo
                    );

                    $oConf->aConfig = $aConf;
                    $oConf->saveConfig('USER_LOGO_REPLACEMENT', '', '', '');

                    G::SendTemporalMessage('ID_REPLACED_LOGO', 'tmp-info', 'labels');
                    break;
                case 'restoreLogo':
                    $snameLogo = $_GET['NAMELOGO'];
                    G::loadClass('configuration');
                    $oConf = new Configurations;
                    $aConf = Array(
                      'WORKSPACE_LOGO_NAME' => '',
                      'DEFAULT_LOGO_NAME'   => ''
                    );

                    $oConf->aConfig = $aConf;
                    $oConf->saveConfig('USER_LOGO_REPLACEMENT', '', '', '');

                    G::SendTemporalMessage('ID_REPLACED_LOGO', 'tmp-info', 'labels');
                    break;
            }
        } catch (Exception $oException) {
            die($oException->getMessage());
        }
        exit();
    }

    /**
     * Show Logo
     * @param type $imagen
     */
    public function showLogo($imagen)
    {
        $info = @getimagesize($imagen);
        $fp   = fopen($imagen, "rb");
        if ($info && $fp) {
            header("Content-type: {$info['mime']}");
            fpassthru($fp);
            exit;
        } else {
            throw new Exception("Image format not valid");
        }
    }

    /**
     * Copy More Logos
     * @param type $dir
     * @param type $newDir
     */
    public function cpyMoreLogos($dir, $newDir)
    {
        if (file_exists($dir)) {
            if (($handle = opendir($dir))) {
                while (false !== ($file = readdir($handle))) {
                    if (($file != ".") && ($file != "..")) {
                        $extention      = explode(".", $file);
                        $aImageProp     = getimagesize($dir . '/' . $file, $info);
                        $sfileExtention = strtoupper($extention[count($extention)-1]);
                        if ( in_array($sfileExtention, array('JPG', 'JPEG', 'PNG', 'GIF') ) ) {
                            $dir1 = $dir . PATH_SEP . $file;
                            $dir2 = $newDir . PATH_SEP . $file;
                            copy($dir1, $dir2);
                        }
                    }
                }
                closedir($handle);
            }
        }
    }

    /**
     * Show Logo File
     */
    public function showLogoFile()
    {
        $_GET['id'] = $_REQUEST['id'];

        $base64Id  = base64_decode($_GET['id']);
        $ainfoSite = explode("/", $_SERVER["REQUEST_URI"]);
        $dir       = PATH_DATA . "sites" . PATH_SEP.str_replace("sys", "", $ainfoSite[1]).PATH_SEP."files/logos";
        $imagen    = $dir . PATH_SEP . $base64Id;

        if (is_file($imagen)) {
            self::showLogo($imagen);
        } else {
            $newDir = PATH_DATA . "sites" . PATH_SEP.str_replace("sys", "", $ainfoSite[1]).PATH_SEP."files/logos";
            $dir    = PATH_HOME . "public_html/files/logos";

            if (!is_dir($newDir)) {
                G::mk_dir($newDir);
            }
            $newDir .= PATH_SEP.$base64Id;
            $dir    .= PATH_SEP.$base64Id;
            copy($dir,$newDir);
            self::showLogo($newDir);
            die;
        }
        die;
        exit();
    }
}

