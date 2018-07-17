<?php
namespace ProcessMaker\BusinessModel;
use G;
use Exception;
use Bootstrap;
use SpoolRun;
use ProcessMaker\Core\System;

class EmailServer
{
    private $arrayFieldDefinition = array(
        "MESS_UID"                 => array("type" => "string", "required" => false, "empty" => false, "defaultValues" => array(),                    "fieldNameAux" => "emailServerUid"),
        "MESS_ENGINE"              => array("type" => "string", "required" => true,  "empty" => false, "defaultValues" => array("PHPMAILER", "MAIL"), "fieldNameAux" => "emailServerEngine"),
        "MESS_SERVER"              => array("type" => "string", "required" => false, "empty" => true,  "defaultValues" => array(),                    "fieldNameAux" => "emailServerServer"),
        "MESS_PORT"                => array("type" => "int",    "required" => false, "empty" => true,  "defaultValues" => array(),                    "fieldNameAux" => "emailServerPort"),
        "MESS_RAUTH"               => array("type" => "int",    "required" => false, "empty" => false, "defaultValues" => array(0, 1),                "fieldNameAux" => "emailServerRauth"),
        "MESS_ACCOUNT"             => array("type" => "string", "required" => false, "empty" => true,  "defaultValues" => array(),                    "fieldNameAux" => "emailServerUserName"),
        "MESS_PASSWORD"            => array("type" => "string", "required" => false, "empty" => true,  "defaultValues" => array(),                    "fieldNameAux" => "emailServerPassword"),
        "MESS_FROM_MAIL"           => array("type" => "string", "required" => false, "empty" => true,  "defaultValues" => array(),                    "fieldNameAux" => "emailServerFromMail"),
        "MESS_FROM_NAME"           => array("type" => "string", "required" => false, "empty" => true,  "defaultValues" => array(),                    "fieldNameAux" => "emailServerFromName"),
        "SMTPSECURE"               => array("type" => "string", "required" => false, "empty" => false, "defaultValues" => array("No", "tls", "ssl", "none"),  "fieldNameAux" => "emailServerSecureConnection"),
        "MESS_TRY_SEND_INMEDIATLY" => array("type" => "int",    "required" => false, "empty" => false, "defaultValues" => array(0, 1),                "fieldNameAux" => "emailServerSendTestMail"),
        "MAIL_TO"                  => array("type" => "string", "required" => false, "empty" => true,  "defaultValues" => array(),                    "fieldNameAux" => "emailServerMailTo"),
        "MESS_DEFAULT"             => array("type" => "int",    "required" => false, "empty" => false, "defaultValues" => array(0, 1),                "fieldNameAux" => "emailServerDefault")
    );

    private $contextLog = array();

    private $formatFieldNameInUppercase = true;

    private $arrayFieldNameForException = array(
        "start"  => "START",
        "limit"  => "LIMIT"
    );

    /**
     * Constructor of the class
     *
     * return void
     */
    public function __construct()
    {
        try {
            foreach ($this->arrayFieldDefinition as $key => $value) {
                $this->arrayFieldNameForException[$value["fieldNameAux"]] = $key;
            }
        } catch (Exception $e) {
            throw $e;
        }
    }
    
    /**
     * Get the default information from the context.
     * 
     * @global type $RBAC
     * @return void
     */
    public function getDefaultContextLog()
    {
        //Define the variables for the logging
        global $RBAC;
        if ($RBAC !== null) {
            $currentUser = $RBAC->aUserInfo['USER_INFO'];
            $info = array(
                'ip' => G::getIpAddress(),
                'workspace' => (defined("SYS_SYS")) ? SYS_SYS : "Workspace undefined",
                'usrUid' => $currentUser['USR_UID']
            );
            $this->setContextLog($info);
        }
    }

    /**
     * Get the $contextLog value.
     *
     * @return string
     */
    public function getContextLog()
    {
        return $this->contextLog;
    }

    /**
     * Set the value of $contextLog.
     *
     * @param array $k
     * @return void
     */
    public function setContextLog($k)
    {
        $this->contextLog = array_merge($this->contextLog, $k);
    }

    /**
     * Set the format of the fields name (uppercase, lowercase)
     *
     * @param bool $flag Value that set the format
     *
     * @return void
     * @throws Exception
     */
    public function setFormatFieldNameInUppercase($flag)
    {
        try {
            $this->formatFieldNameInUppercase = $flag;

            $this->setArrayFieldNameForException($this->arrayFieldNameForException);
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Set exception messages for fields
     *
     * @param array $arrayData Data with the fields
     *
     * @return void
     * @throws Exception
     */
    public function setArrayFieldNameForException(array $arrayData)
    {
        try {
            foreach ($arrayData as $key => $value) {
                $this->arrayFieldNameForException[$key] = $this->getFieldNameByFormatFieldName($value);
            }
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Get the name of the field according to the format
     *
     * @param string $fieldName Field name
     *
     * @return string, return the field name according the format
     * @throws Exception
     */
    public function getFieldNameByFormatFieldName($fieldName)
    {
        try {
            return ($this->formatFieldNameInUppercase)? strtoupper($fieldName) : strtolower($fieldName);
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Send a test email
     *
     * @param array $arrayData Data
     *
     * @return array, return array with result of send test mail
     * @throws Exception
     */
    public function sendTestMail(array $arrayData)
    {
        try {

            $aConfiguration = array(
                "MESS_ENGINE"    => $arrayData["MESS_ENGINE"],
                "MESS_SERVER"    => $arrayData["MESS_SERVER"],
                "MESS_PORT"      => (int)($arrayData["MESS_PORT"]),
                "MESS_ACCOUNT"   => $arrayData["MESS_ACCOUNT"],
                "MESS_PASSWORD"  => $arrayData["MESS_PASSWORD"],
                "MESS_FROM_NAME" => $arrayData["FROM_NAME"],
                "MESS_FROM_MAIL" => $arrayData["FROM_EMAIL"],
                "MESS_RAUTH"     => (int)($arrayData["MESS_RAUTH"]),
                "SMTPSecure"     => (isset($arrayData["SMTPSecure"]))? $arrayData["SMTPSecure"] : "none"
            );

            $sFrom = G::buildFrom($aConfiguration);

            $sSubject = G::LoadTranslation("ID_MESS_TEST_SUBJECT");
            $msg = G::LoadTranslation("ID_MESS_TEST_BODY");

            switch ($arrayData["MESS_ENGINE"]) {
                case "MAIL":
                    $engine = G::LoadTranslation("ID_MESS_ENGINE_TYPE_1");
                    break;
                case "PHPMAILER":
                    $engine = G::LoadTranslation("ID_MESS_ENGINE_TYPE_2");
                    break;
                case "OPENMAIL":
                    $engine = G::LoadTranslation("ID_MESS_ENGINE_TYPE_3");
                    break;
            }

            $sBodyPre = new \TemplatePower(PATH_TPL . "admin" . PATH_SEP . "email.tpl");

            $sBodyPre->prepare();
            $sBodyPre->assign("server", $_SERVER["SERVER_NAME"]);
            $sBodyPre->assign("date", date("H:i:s"));
            $sBodyPre->assign("ver", System::getVersion());
            $sBodyPre->assign("engine", $engine);
            $sBodyPre->assign("msg", $msg);
            $sBody = $sBodyPre->getOutputContent();

            $oSpool = new SpoolRun();

            $oSpool->setConfig($aConfiguration);

            $oSpool->create(
                array(
                    "msg_uid"          => "",
                    "app_uid"          => "",
                    "del_index"        => 0,
                    "app_msg_type"     => "TEST",
                    "app_msg_subject"  => $sSubject,
                    "app_msg_from"     => $sFrom,
                    "app_msg_to"       => $arrayData["TO"],
                    "app_msg_body"     => $sBody,
                    "app_msg_cc"       => "",
                    "app_msg_bcc"      => "",
                    "app_msg_attach"   => "",
                    "app_msg_template" => "",
                    "app_msg_status"   => "pending",
                    "app_msg_attach"   => ""
                )
            );

            $oSpool->sendMail();

            //Return
            $arrayTestMailResult = array();

            if ($oSpool->status == "sent") {
                $arrayTestMailResult["status"]  = true;
                $arrayTestMailResult["success"] = true;
                $arrayTestMailResult["msg"]     = G::LoadTranslation("ID_MAIL_TEST_SUCCESS");
            } else {
                $arrayTestMailResult["status"]  = false;
                $arrayTestMailResult["success"] = false;
                $arrayTestMailResult["msg"]     = $oSpool->error;
            }

            return $arrayTestMailResult;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Test connection by step
     *
     * @param array $arrayData Data
     * @param int   $step      Step
     *
     * @return array, return array with result of test connection by step
     * @throws Exception
     */
    public function testConnectionByStep(array $arrayData, $step = 0)
    {
        try {

            //MAIL
            if ($arrayData["MESS_ENGINE"] == "MAIL") {

                $arrayDataMail = array();

                $eregMail = "/^[0-9a-zA-Z]+(?:[._][0-9a-zA-Z]+)*@[0-9a-zA-Z]+(?:[._-][0-9a-zA-Z]+)*\.[0-9a-zA-Z]{2,3}$/";

                $arrayDataMail["FROM_EMAIL"]    = ($arrayData["MESS_FROM_MAIL"] != "" && preg_match($eregMail, $arrayData["MESS_FROM_MAIL"]))? $arrayData["MESS_FROM_MAIL"] : "";
                $arrayDataMail["FROM_NAME"]     = ($arrayData["MESS_FROM_NAME"] != "")? $arrayData["MESS_FROM_NAME"] : G::LoadTranslation("ID_MESS_TEST_BODY");
                $arrayDataMail["MESS_ENGINE"]   = "MAIL";
                $arrayDataMail["MESS_SERVER"]   = "localhost";
                $arrayDataMail["MESS_PORT"]     = 25;
                $arrayDataMail["MESS_ACCOUNT"]  = $arrayData["MAIL_TO"];
                $arrayDataMail["MESS_PASSWORD"] = "";
                $arrayDataMail["TO"]            = $arrayData["MAIL_TO"];
                $arrayDataMail["MESS_RAUTH"]    = true;

                $arrayTestMailResult = array();

                try {
                    $arrayTestMailResult = $this->sendTestMail($arrayDataMail);
                } catch (Exception $e) {
                    $arrayTestMailResult["status"] = false;
                    $arrayTestMailResult["message"] = $e->getMessage();

                }

                $arrayResult = array(
                    "result"  => $arrayTestMailResult["status"],
                    "message" => ""
                );

                if ($arrayTestMailResult["status"] == false) {
                    $arrayResult["message"] = G::LoadTranslation("ID_SENDMAIL_NOT_INSTALLED");
                }

                //Return
                return $arrayResult;
            }

            //PHPMAILER
            $server = $arrayData["MESS_SERVER"];
            $user = $arrayData["MESS_ACCOUNT"];
            $passwd = $arrayData["MESS_PASSWORD"];
            $fromMail = $arrayData["MESS_FROM_MAIL"];
            $passwdHide = $arrayData["MESS_PASSWORD"];

            if (trim($passwdHide) != "") {
                $passwd = $passwdHide;
                $passwdHide = "";
            }

            $passwdDec = G::decrypt($passwd,"EMAILENCRYPT");
            $auxPass = explode("hash:", $passwdDec);

            if (count($auxPass) > 1) {
                if (count($auxPass) == 2) {
                    $passwd = $auxPass[1];
                } else {
                    array_shift($auxPass);
                    $passwd = implode("", $auxPass);
                }
            }

            $arrayData["MESS_PASSWORD"] = $passwd;

            $port = (int)($arrayData["MESS_PORT"]);
            $auth_required = (int)($arrayData["MESS_RAUTH"]);
            $useSecureCon = $arrayData["SMTPSECURE"];
            $sendTestMail = (int)($arrayData["MESS_TRY_SEND_INMEDIATLY"]);
            $mailTo = $arrayData["MAIL_TO"];
            $smtpSecure = $arrayData["SMTPSECURE"];

            $serverNet = new \Net($server);
            $smtp = new \SMTP();

            $timeout = 10;
            $hostinfo = array();
            $srv = $arrayData["MESS_SERVER"];

            $arrayResult = array();

            switch ($step) {
                case 1:
                    $arrayResult["result"] = $serverNet->getErrno() == 0;
                    $arrayResult["message"] = $serverNet->error;
                    break;
                case 2:
                    $serverNet->scannPort($port);

                    $arrayResult["result"] = $serverNet->getErrno() == 0;
                    $arrayResult["message"] = $serverNet->error;
                    break;
                case 3:
                    //Try to connect to host
                    if (preg_match("/^(.+):([0-9]+)$/", $srv, $hostinfo)) {
                        $server = $hostinfo[1];
                        $port = $hostinfo[2];
                    } else {
                        $host = $srv;
                    }

                    $tls = (strtoupper($smtpSecure) == "tls");
                    $ssl = (strtoupper($smtpSecure) == "ssl");

                    $arrayResult["result"] = $smtp->Connect(($ssl ? "ssl://" : "") . $server, $port, $timeout);
                    $arrayResult["message"] = $serverNet->error;
                    break;
                case 4:
                    //Try login to host
                    if ($auth_required == 1) {
                        try {
                            if (preg_match("/^(.+):([0-9]+)$/", $srv, $hostinfo)) {
                                $server = $hostinfo[1];
                                $port = $hostinfo[2];
                            } else {
                                $server = $srv;
                            }
                            if (strtoupper($useSecureCon)=="TLS") {
                                $tls = "tls";
                            }

                            if (strtoupper($useSecureCon)=="SSL") {
                                $tls = "ssl";
                            }

                            $tls = (strtoupper($useSecureCon) == "tls");
                            $ssl = (strtoupper($useSecureCon) == "ssl");

                            $server = $arrayData["MESS_SERVER"];

                            if (strtoupper($useSecureCon) == "SSL") {
                                $resp = $smtp->Connect(("ssl://") . $server, $port, $timeout);
                            } else {
                                $resp = $smtp->Connect($server, $port, $timeout);
                            }

                            if ($resp) {
                                $hello = $_SERVER["SERVER_NAME"];
                                $smtp->Hello($hello);

                                if (strtoupper($useSecureCon) == "TLS") {
                                    $smtp->Hello($hello);
                                }

                                if ($smtp->Authenticate($user, $passwd) ) {
                                    $arrayResult["result"] = true;
                                } else {
                                    if (strtoupper($useSecureCon) == "TLS") {
                                        $arrayResult["result"] = true;
                                    } else {
                                        $arrayResult["result"] = false;
                                        $smtpError = $smtp->getError();
                                        $arrayResult["message"] = $smtpError["error"];
                                    }
                                }
                            } else {
                                $arrayResult["result"] = false;
                                $smtpError = $smtp->getError();
                                $arrayResult["message"] = $smtpError["error"];
                            }
                        } catch (Exception $e) {
                            $arrayResult["result"] = false;
                            $arrayResult["message"] = $e->getMessage();
                        }
                    } else {
                        $arrayResult["result"] = true;
                        $arrayResult["message"] = "No authentication required!";
                    }
                    break;
                case 5:
                    if ($sendTestMail == 1) {
                        try {
                            $arrayDataPhpMailer = array();

                            $eregMail = "/^[0-9a-zA-Z]+(?:[._][0-9a-zA-Z]+)*@[0-9a-zA-Z]+(?:[._-][0-9a-zA-Z]+)*\.[0-9a-zA-Z]{2,3}$/";

                            $arrayDataPhpMailer["FROM_EMAIL"]    = ($fromMail != "" && preg_match($eregMail, $fromMail))? $fromMail : "";
                            $arrayDataPhpMailer["FROM_NAME"]     = $arrayData["MESS_FROM_NAME"] != "" ? $arrayData["MESS_FROM_NAME"] : G::LoadTranslation("ID_MESS_TEST_BODY");
                            $arrayDataPhpMailer["MESS_ENGINE"]   = "PHPMAILER";
                            $arrayDataPhpMailer["MESS_SERVER"]   = $server;
                            $arrayDataPhpMailer["MESS_PORT"]     = $port;
                            $arrayDataPhpMailer["MESS_ACCOUNT"]  = $user;
                            $arrayDataPhpMailer["MESS_PASSWORD"] = $passwd;
                            $arrayDataPhpMailer["TO"]            = $mailTo;

                            if ($auth_required == 1) {
                                $arrayDataPhpMailer["MESS_RAUTH"] = true;
                            } else {
                                $arrayDataPhpMailer["MESS_RAUTH"] = false;
                            }
                            if (strtolower($arrayData["SMTPSECURE"]) != "no") {
                                $arrayDataPhpMailer["SMTPSecure"] = $arrayData["SMTPSECURE"];
                            }

                            $arrayTestMailResult = $this->sendTestMail($arrayDataPhpMailer);

                            if ($arrayTestMailResult["status"] . "" == "1") {
                                $arrayResult["result"] = true;
                            } else {
                                $arrayResult["result"] = false;
                                $smtpError = $smtp->getError();
                                $arrayResult["message"] = $smtpError["error"];
                            }
                        } catch (Exception $e) {
                            $arrayResult["result"] = false;
                            $arrayResult["message"] = $e->getMessage();
                        }
                    } else {
                        $arrayResult["result"] = true;
                        $arrayResult["message"] = "Jump this step";
                    }
                    break;
            }

            if (!isset($arrayResult["message"])) {
                $arrayResult["message"] = "";
            }

            //Return
            return $arrayResult;
        } catch (Exception $e) {
            $arrayResult = array();

            $arrayResult["result"] = false;
            $arrayResult["message"] = $e->getMessage();

            //Return
            return $arrayResult;
        }
    }

    /**
     * Test connection
     *
     * @param array $arrayData Data
     *
     * @return array, return array with result of test connection
     * @throws Exception
     */
    public function testConnection(array $arrayData)
    {
        try {
            $arrayData = array_change_key_case($arrayData, CASE_UPPER);

            $arrayMailTestName = array(
                1 => "verifying_mail",
                2 => "sending_email"
            );

            $arrayPhpMailerTestName = array(
                1 => "resolving_name",
                2 => "check_port",
                3 => "establishing_connection_host",
                4 => "login",
                5 => "sending_email"
            );

            $arrayResult = array();

            switch ($arrayData["MESS_ENGINE"]) {
                case "MAIL":
                    $arrayDataAux = $arrayData;

                    $arrayDataAux["MESS_TRY_SEND_INMEDIATLY"] = 1;
                    $arrayDataAux["MAIL_TO"] = "admin@processmaker.com";

                    $arrayResult[$arrayMailTestName[1]] = $this->testConnectionByStep($arrayDataAux);
                    $arrayResult[$arrayMailTestName[1]]["title"] = G::LoadTranslation("ID_EMAIL_SERVER_TEST_CONNECTION_VERIFYING_MAIL");

                    if ((int)($arrayData["MESS_TRY_SEND_INMEDIATLY"]) == 1 && $arrayData['MAIL_TO'] != '') {
                        $arrayResult[$arrayMailTestName[2]] = $this->testConnectionByStep($arrayData);
                        $arrayResult[$arrayMailTestName[2]]["title"] = G::LoadTranslation("ID_EMAIL_SERVER_TEST_CONNECTION_SENDING_EMAIL", array($arrayData["MAIL_TO"]));
                    }
                    break;
                case "PHPMAILER":
                    $numSteps = ($arrayData['MAIL_TO'] != '') ? count($arrayPhpMailerTestName) :
                        count($arrayPhpMailerTestName) - 1;
                    for ($step = 1; $step <= $numSteps; $step++) {
                        $arrayResult[$arrayPhpMailerTestName[$step]] = $this->testConnectionByStep($arrayData, $step);

                        switch ($step) {
                            case 1:
                                $arrayResult[$arrayPhpMailerTestName[$step]]["title"] = G::LoadTranslation("ID_EMAIL_SERVER_TEST_CONNECTION_RESOLVING_NAME", array($arrayData["MESS_SERVER"]));
                                break;
                            case 2:
                                $arrayResult[$arrayPhpMailerTestName[$step]]["title"] = G::LoadTranslation("ID_EMAIL_SERVER_TEST_CONNECTION_CHECK_PORT", array($arrayData["MESS_PORT"]));
                                break;
                            case 3:
                                $arrayResult[$arrayPhpMailerTestName[$step]]["title"] = G::LoadTranslation("ID_EMAIL_SERVER_TEST_CONNECTION_ESTABLISHING_CON_HOST", array($arrayData["MESS_SERVER"] . ":" . $arrayData["MESS_PORT"]));
                                break;
                            case 4:
                                $arrayResult[$arrayPhpMailerTestName[$step]]["title"] = G::LoadTranslation("ID_EMAIL_SERVER_TEST_CONNECTION_LOGIN", array($arrayData["MESS_ACCOUNT"], $arrayData["MESS_SERVER"]));
                                break;
                            case 5:
                                $arrayResult[$arrayPhpMailerTestName[$step]]["title"] = G::LoadTranslation("ID_EMAIL_SERVER_TEST_CONNECTION_SENDING_EMAIL", array($arrayData["MAIL_TO"]));
                                break;
                        }
                    }
                    break;
            }

            //Result
            return $arrayResult;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Check if is default Email Server
     *
     * @param string $emailServerUid Unique id of Email Server
     *
     * @return bool, return true if is default Email Server, false otherwise
     * @throws Exception
     */
    public function checkIfIsDefault($emailServerUid)
    {
        try {
            $criteria = $this->getEmailServerCriteria();

            $criteria->add(\EmailServerPeer::MESS_UID, $emailServerUid, \Criteria::EQUAL);
            $criteria->add(\EmailServerPeer::MESS_DEFAULT, 1, \Criteria::EQUAL);

            $rsCriteria = \EmailServerPeer::doSelectRS($criteria);

            if ($rsCriteria->next()) {
                return true;
            } else {
                return false;
            }
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Validate the data if they are invalid (INSERT and UPDATE)
     *
     * @param string $emailServerUid Unique id of Email Server
     * @param array  $arrayData      Data
     *
     * @return void Throw exception if data has an invalid value
     * @throws Exception
     */
    public function throwExceptionIfDataIsInvalid($emailServerUid, array $arrayData)
    {
        try {
            //Set variables
            $arrayEmailServerData = ($emailServerUid == "")? array() : $this->getEmailServer($emailServerUid, true);
            $flagInsert = ($emailServerUid == "")? true : false;

            $arrayFinalData = array_merge($arrayEmailServerData, $arrayData);

            //Verify data
            $process = new \ProcessMaker\BusinessModel\Process();

            $arrayFieldDefinition = $this->arrayFieldDefinition;

            switch ($arrayFinalData["MESS_ENGINE"]) {
                case "PHPMAILER":
                    $arrayFieldDefinition["MESS_SERVER"]["required"] = true;
                    $arrayFieldDefinition["MESS_SERVER"]["empty"]    = false;

                    $arrayFieldDefinition["MESS_PORT"]["required"] = true;
                    $arrayFieldDefinition["MESS_PORT"]["empty"]    = false;

                    $arrayFieldDefinition["MESS_ACCOUNT"]["required"] = true;
                    $arrayFieldDefinition["MESS_ACCOUNT"]["empty"]    = false;

                    $arrayFieldDefinition["SMTPSECURE"]["required"] = true;
                    $arrayFieldDefinition["SMTPSECURE"]["empty"]    = false;

                    if ((int)($arrayFinalData["MESS_RAUTH"]) == 1) {
                        $arrayFieldDefinition["MESS_PASSWORD"]["required"] = true;
                        $arrayFieldDefinition["MESS_PASSWORD"]["empty"] = false;
                    }
                    break;
                case "MAIL":
                    $arrayFieldDefinition["SMTPSECURE"]["empty"] = true;
                    $arrayFieldDefinition["SMTPSECURE"]["defaultValues"] = array();
                    break;
            }

            if ((int)($arrayFinalData["MESS_TRY_SEND_INMEDIATLY"]) == 1) {
                $arrayFieldDefinition["MAIL_TO"]["required"] = true;
                $arrayFieldDefinition["MAIL_TO"]["empty"]    = false;
            }

            $process->throwExceptionIfDataNotMetFieldDefinition($arrayData, $arrayFieldDefinition, $this->arrayFieldNameForException, $flagInsert);

            if ($flagInsert == false) {
                //Update
                $process->throwExceptionIfDataNotMetFieldDefinition($arrayFinalData, $arrayFieldDefinition, $this->arrayFieldNameForException, true);
            }

            //Verify data Test Connection
            if (isset($_SERVER["SERVER_NAME"])) {
                $arrayTestConnectionResult = $this->testConnection($arrayFinalData);

                $msg = "";

                foreach ($arrayTestConnectionResult as $key => $value) {
                    $arrayTest = $value;

                    if (!$arrayTest["result"]) {
                        $msg = $msg . (($msg != "")? ", " : "") . $arrayTest["title"] . " (Error: " . $arrayTest["message"] . ")";
                    }
                }

                if ($msg != "") {
                    throw new Exception($msg);
                }
            }
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Verify if does not exist the Email Server in table EMAIL_SERVER
     *
     * @param string $emailServerUid        Unique id of Email Server
     * @param string $fieldNameForException Field name for the exception
     *
     * @return void Throw exception if does not exist the Email Server in table EMAIL_SERVER
     * @throws Exception
     */
    public function throwExceptionIfNotExistsEmailServer($emailServerUid, $fieldNameForException)
    {
        try {
            $obj = \EmailServerPeer::retrieveByPK($emailServerUid);

            if (is_null($obj)) {
                throw new Exception(G::LoadTranslation("ID_EMAIL_SERVER_DOES_NOT_EXIST", array($fieldNameForException, $emailServerUid)));
            }
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Check if is default Email Server
     *
     * @param string $emailServerUid        Unique id of Email Server
     * @param string $fieldNameForException Field name for the exception
     *
     * @return void Throw exception if is default Email Server
     * @throws Exception
     */
    public function throwExceptionIfIsDefault($emailServerUid, $fieldNameForException)
    {
        try {
            if ($this->checkIfIsDefault($emailServerUid)) {
                throw new Exception(G::LoadTranslation("ID_EMAIL_SERVER_IS_DEFAULT", array($fieldNameForException, $emailServerUid)));
            }
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Set default Email Server by Unique id of Email Server
     *
     * @param string $emailServerUid Unique id of Email Server
     *
     * @return void
     * @throws Exception
     */
    public function setEmailServerDefaultByUid($emailServerUid)
    {
        try {

            $arrayEmailServerData = $this->getEmailServer($emailServerUid, true);

            //Update
            //Update - WHERE
            $criteriaWhere = new \Criteria("workflow");
            $criteriaWhere->add(\EmailServerPeer::MESS_UID, $emailServerUid, \Criteria::NOT_EQUAL);

            //Update
            $criteriaSet = new \Criteria("workflow");
            $criteriaSet->add(\EmailServerPeer::MESS_DEFAULT, 0);

            \BasePeer::doUpdate($criteriaWhere, $criteriaSet, \Propel::getConnection("workflow"));

            if ((int)($arrayEmailServerData["MESS_DEFAULT"]) == 0) {
                //Update
                //Update - WHERE
                $criteriaWhere = new \Criteria("workflow");
                $criteriaWhere->add(\EmailServerPeer::MESS_UID, $emailServerUid, \Criteria::NOT_EQUAL);

                //Update
                $criteriaSet = new \Criteria("workflow");
                $criteriaSet->add(\EmailServerPeer::MESS_DEFAULT, 1);

                \BasePeer::doUpdate($criteriaWhere, $criteriaSet, \Propel::getConnection("workflow"));
            }
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Create Email Server
     *
     * @param array $arrayData Data
     *
     * @return array, data of the new Email Server created
     * @throws Exception
     */
    public function create(array $arrayData)
    {
        try {
            //Verify data
            $process = new \ProcessMaker\BusinessModel\Process();
            $validator = new \ProcessMaker\BusinessModel\Validator();

            $validator->throwExceptionIfDataIsNotArray($arrayData, "\$arrayData");
            $validator->throwExceptionIfDataIsEmpty($arrayData, "\$arrayData");

            //Set data
            $arrayData = array_change_key_case($arrayData, CASE_UPPER);

            unset($arrayData["MESS_UID"]);

            $this->throwExceptionIfDataIsInvalid("", $arrayData);

            //Create
            $cnn = \Propel::getConnection("workflow");

            try {
                $emailServer = new \EmailServer();

                $passwd = $arrayData["MESS_PASSWORD"];
                $passwdDec = G::decrypt($passwd, "EMAILENCRYPT");
                $auxPass = explode("hash:", $passwdDec);

                if (count($auxPass) > 1) {
                    if (count($auxPass) == 2) {
                        $passwd = $auxPass[1];
                    } else {
                        array_shift($auxPass);
                        $passwd = implode("", $auxPass);
                    }
                }

                $arrayData["MESS_PASSWORD"] = $passwd;

                if ($arrayData["MESS_PASSWORD"] != "") {
                    $arrayData["MESS_PASSWORD"] = "hash:" . $arrayData["MESS_PASSWORD"];
                    $arrayData["MESS_PASSWORD"] = G::encrypt($arrayData["MESS_PASSWORD"], "EMAILENCRYPT");
                }

                $emailServer->fromArray($arrayData, \BasePeer::TYPE_FIELDNAME);

                $emailServerUid = \ProcessMaker\Util\Common::generateUID();

                $emailServer->setMessUid($emailServerUid);

                if ($emailServer->validate()) {
                    $cnn->begin();

                    $result = $emailServer->save();

                    $cnn->commit();

                    if (isset($arrayData["MESS_DEFAULT"]) && (int)($arrayData["MESS_DEFAULT"]) == 1) {
                        $this->setEmailServerDefaultByUid($emailServerUid);
                    }

                    //Logging the create action
                    $this->getDefaultContextLog();
                    $info = array(
                        'action' => 'Create email server',
                        'messUid'=> $emailServerUid,
                        'engine'=> $arrayData["MESS_ENGINE"],
                        'server' => $arrayData["MESS_SERVER"],
                        'port' => $arrayData["MESS_PORT"],
                        'requireAuthentication' => $arrayData["MESS_RAUTH"],
                        'account' => $arrayData["MESS_ACCOUNT"],
                        'senderEmail' => $arrayData["MESS_FROM_MAIL"],
                        'senderName' => $arrayData["MESS_FROM_NAME"],
                        'useSecureConnection' => $arrayData["SMTPSECURE"],
                        'sendTestEmail' => $arrayData["MESS_TRY_SEND_INMEDIATLY"],
                        'setAsDefaultConfiguration' => $arrayData["MESS_DEFAULT"]
                    );
                    $this->setContextLog($info);
                    $this->syslog(
                        'CreateEmailServer',
                        200,
                        'New email server was created',
                        $this->getContextLog()
                    );
                    return $this->getEmailServer($emailServerUid);
                } else {
                    $msg = "";

                    foreach ($emailServer->getValidationFailures() as $validationFailure) {
                        $msg = $msg . (($msg != "")? "\n" : "") . $validationFailure->getMessage();
                    }

                    throw new Exception(G::LoadTranslation("ID_RECORD_CANNOT_BE_CREATED") . (($msg != "")? "\n" . $msg : ""));
                }
            } catch (Exception $e) {
                $cnn->rollback();

                throw $e;
            }
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Create Email Server by data
     *
     * @param array $arrayData Data
     *
     * @return array, return data of the new Email Server created
     * @throws Exception
     */
    public function create2(array $arrayData)
    {
        try {
            //Create
            $cnn = \Propel::getConnection("workflow");

            try {
                $emailServer = new \EmailServer();

                $emailServer->fromArray($arrayData, \BasePeer::TYPE_FIELDNAME);

                $emailServerUid = \ProcessMaker\Util\Common::generateUID();

                $emailServer->setMessUid($emailServerUid);

                if ($emailServer->validate()) {
                    $cnn->begin();

                    $result = $emailServer->save();

                    $cnn->commit();

                    if (isset($arrayData["MESS_DEFAULT"]) && (int)($arrayData["MESS_DEFAULT"]) == 1) {
                        $this->setEmailServerDefaultByUid($emailServerUid);
                    }

                    //Return
                    return $this->getEmailServer($emailServerUid);
                } else {
                    $msg = "";

                    foreach ($emailServer->getValidationFailures() as $validationFailure) {
                        $msg = $msg . (($msg != "")? "\n" : "") . $validationFailure->getMessage();
                    }

                    throw new Exception(G::LoadTranslation("ID_RECORD_CANNOT_BE_CREATED") . (($msg != "")? "\n" . $msg : ""));
                }
            } catch (Exception $e) {
                $cnn->rollback();

                throw $e;
            }
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Update Email Server
     *
     * @param string $emailServerUid Unique id of Group
     * @param array  $arrayData      Data
     *
     * @return array Return data of the Email Server updated
     * @throws Exception
     */
    public function update($emailServerUid, $arrayData)
    {
        try {
            //Verify data
            $process = new \ProcessMaker\BusinessModel\Process();
            $validator = new \ProcessMaker\BusinessModel\Validator();

            $validator->throwExceptionIfDataIsNotArray($arrayData, "\$arrayData");
            $validator->throwExceptionIfDataIsEmpty($arrayData, "\$arrayData");

            //Set data
            $arrayData = array_change_key_case($arrayData, CASE_UPPER);

            //Verify data
            $this->throwExceptionIfNotExistsEmailServer($emailServerUid, $this->arrayFieldNameForException["emailServerUid"]);

            $this->throwExceptionIfDataIsInvalid($emailServerUid, $arrayData);

            //Update
            $cnn = \Propel::getConnection("workflow");

            try {
                $emailServer = \EmailServerPeer::retrieveByPK($emailServerUid);

                if (isset($arrayData['MESS_PASSWORD'])) {
                    $passwd = $arrayData['MESS_PASSWORD'];
                    $passwdDec = G::decrypt($passwd, 'EMAILENCRYPT');
                    $auxPass = explode('hash:', $passwdDec);

                    if (count($auxPass) > 1) {
                        if (count($auxPass) == 2) {
                            $passwd = $auxPass[1];
                        } else {
                            array_shift($auxPass);
                            $passwd = implode('', $auxPass);
                        }
                    }

                    $arrayData['MESS_PASSWORD'] = $passwd;

                    if ($arrayData['MESS_PASSWORD'] != '') {
                        $arrayData['MESS_PASSWORD'] = 'hash:' . $arrayData['MESS_PASSWORD'];
                        $arrayData['MESS_PASSWORD'] = G::encrypt($arrayData['MESS_PASSWORD'], 'EMAILENCRYPT');
                    }
                }

                $emailServer->fromArray($arrayData, \BasePeer::TYPE_FIELDNAME);

                if ($emailServer->validate()) {
                    $cnn->begin();

                    $result = $emailServer->save();

                    $cnn->commit();

                    if (isset($arrayData["MESS_DEFAULT"]) && (int)($arrayData["MESS_DEFAULT"]) == 1) {
                        $this->setEmailServerDefaultByUid($emailServerUid);
                    }

                    //Return
                    if (!$this->formatFieldNameInUppercase) {
                        $arrayData = array_change_key_case($arrayData, CASE_LOWER);
                    }

                    //Logging the update action
                    $this->getDefaultContextLog();
                    $info = array(
                        'action' => 'Update email server',
                        'messUid' => $emailServerUid,
                        'engine' => $arrayData["MESS_ENGINE"],
                        'server' => $arrayData["MESS_SERVER"],
                        'port' => $arrayData["MESS_PORT"],
                        'requireAuthentication' => $arrayData["MESS_RAUTH"],
                        'account' => $arrayData["MESS_ACCOUNT"],
                        'senderEmail' => $arrayData["MESS_FROM_MAIL"],
                        'senderName' => $arrayData["MESS_FROM_NAME"],
                        'useSecureConnection' => $arrayData["SMTPSECURE"],
                        'sendTestEmail' => $arrayData["MESS_TRY_SEND_INMEDIATLY"],
                        'setAsDefaultConfiguration' => $arrayData["MESS_DEFAULT"]
                    );
                    $this->setContextLog($info);
                    $this->syslog(
                        'UpdateEmailServer',
                        200,
                        'The email server was updated',
                        $this->getContextLog()
                    );

                    return $arrayData;
                } else {
                    $msg = "";

                    foreach ($emailServer->getValidationFailures() as $validationFailure) {
                        $msg = $msg . (($msg != "")? "\n" : "") . $validationFailure->getMessage();
                    }

                    throw new Exception(G::LoadTranslation("ID_RECORD_CANNOT_BE_CREATED") . (($msg != "")? "\n" . $msg : ""));
                }
            } catch (Exception $e) {
                $cnn->rollback();

                throw $e;
            }
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Delete Email Server
     *
     * @param string $emailServerUid Unique id of Email Server
     *
     * @return void
     * @throws Exception
     */
    public function delete($emailServerUid)
    {
        try {
            //Verify data
            $this->throwExceptionIfNotExistsEmailServer($emailServerUid, $this->arrayFieldNameForException["emailServerUid"]);
            $this->throwExceptionIfIsDefault($emailServerUid, $this->arrayFieldNameForException["emailServerUid"]);
            $criteria = $this->getEmailServerCriteria();
            $criteria->add(\EmailServerPeer::MESS_UID, $emailServerUid, \Criteria::EQUAL);
            \EmailServerPeer::doDelete($criteria);

            //Logging the delete action
            $this->getDefaultContextLog();
            $info = array(
                'action' => 'Delete email server',
                'messUid' => $emailServerUid
            );
            $this->setContextLog($info);
            $this->syslog(
                'DeleteEmailServer',
                200,
                'The email server was deleted',
                $this->getContextLog()
            );
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Get criteria for Email Server
     *
     * return object
     */
    public function getEmailServerCriteria()
    {
        try {
            $criteria = new \Criteria("workflow");

            $criteria->addSelectColumn(\EmailServerPeer::MESS_UID);
            $criteria->addSelectColumn(\EmailServerPeer::MESS_ENGINE);
            $criteria->addSelectColumn(\EmailServerPeer::MESS_SERVER);
            $criteria->addSelectColumn(\EmailServerPeer::MESS_PORT);
            $criteria->addSelectColumn(\EmailServerPeer::MESS_RAUTH);
            $criteria->addSelectColumn(\EmailServerPeer::MESS_ACCOUNT);
            $criteria->addSelectColumn(\EmailServerPeer::MESS_PASSWORD);
            $criteria->addSelectColumn(\EmailServerPeer::MESS_FROM_MAIL);
            $criteria->addSelectColumn(\EmailServerPeer::MESS_FROM_NAME);
            $criteria->addSelectColumn(\EmailServerPeer::SMTPSECURE);
            $criteria->addSelectColumn(\EmailServerPeer::MESS_TRY_SEND_INMEDIATLY);
            $criteria->addSelectColumn(\EmailServerPeer::MAIL_TO);
            $criteria->addSelectColumn(\EmailServerPeer::MESS_DEFAULT);

            return $criteria;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Get data of a from a record
     *
     * @param array $record Record
     *
     * @return array, return an array with data Email Server
     * @throws Exception
     */
    public function getEmailServerDataFromRecord(array $record)
    {
        try {
            return array(
                $this->getFieldNameByFormatFieldName("MESS_UID")                 => $record["MESS_UID"],
                $this->getFieldNameByFormatFieldName("MESS_ENGINE")              => $record["MESS_ENGINE"],
                $this->getFieldNameByFormatFieldName("MESS_SERVER")              => $record["MESS_SERVER"],
                $this->getFieldNameByFormatFieldName("MESS_PORT")                => $record["MESS_PORT"],
                $this->getFieldNameByFormatFieldName("MESS_RAUTH")               => $record["MESS_RAUTH"],
                $this->getFieldNameByFormatFieldName("MESS_ACCOUNT")             => $record["MESS_ACCOUNT"],
                $this->getFieldNameByFormatFieldName("MESS_PASSWORD")            => $record["MESS_PASSWORD"],
                $this->getFieldNameByFormatFieldName("MESS_FROM_MAIL")           => $record["MESS_FROM_MAIL"],
                $this->getFieldNameByFormatFieldName("MESS_FROM_NAME")           => $record["MESS_FROM_NAME"],
                $this->getFieldNameByFormatFieldName("SMTPSECURE")               => $record["SMTPSECURE"],
                $this->getFieldNameByFormatFieldName("MESS_TRY_SEND_INMEDIATLY") => $record["MESS_TRY_SEND_INMEDIATLY"],
                $this->getFieldNameByFormatFieldName("MAIL_TO")                  => $record["MAIL_TO"],
                $this->getFieldNameByFormatFieldName("MESS_DEFAULT")             => $record["MESS_DEFAULT"],
                $this->getFieldNameByFormatFieldName("MESS_BACKGROUND")          => '',
                $this->getFieldNameByFormatFieldName("MESS_PASSWORD_HIDDEN")     => '',
                $this->getFieldNameByFormatFieldName("MESS_EXECUTE_EVERY")       => '',
                $this->getFieldNameByFormatFieldName("MESS_SEND_MAX")            => ''
            );
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Get Default Email Server
     *
     * return array Return an array with Email Server default
     */
    public function getEmailServerDefault()
    {
        try {
            $arrayData = array();

            //SQL
            $criteria = $this->getEmailServerCriteria();

            $criteria->add(\EmailServerPeer::MESS_DEFAULT, 1, \Criteria::EQUAL);

            //QUERY
            $rsCriteria = \EmailServerPeer::doSelectRS($criteria);
            $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

            while ($rsCriteria->next()) {
                $row = $rsCriteria->getRow();

                $arrayData["MESS_UID"]                 = $row["MESS_UID"];
                $arrayData["MESS_ENGINE"]              = $row["MESS_ENGINE"];
                $arrayData["MESS_SERVER"]              = $row["MESS_SERVER"];
                $arrayData["MESS_PORT"]                = (int)($row["MESS_PORT"]);
                $arrayData["MESS_RAUTH"]               = (int)($row["MESS_RAUTH"]);
                $arrayData["MESS_ACCOUNT"]             = $row["MESS_ACCOUNT"];
                $arrayData["MESS_PASSWORD"]            = $row["MESS_PASSWORD"];
                $arrayData["MESS_FROM_MAIL"]           = $row["MESS_FROM_MAIL"];
                $arrayData["MESS_FROM_NAME"]           = $row["MESS_FROM_NAME"];
                $arrayData["SMTPSECURE"]               = $row["SMTPSECURE"];
                $arrayData["MESS_TRY_SEND_INMEDIATLY"] = (int)($row["MESS_TRY_SEND_INMEDIATLY"]);
                $arrayData["MAIL_TO"]                  = $row["MAIL_TO"];
                $arrayData["MESS_DEFAULT"]             = (int)($row["MESS_DEFAULT"]);
                $arrayData["MESS_BACKGROUND"]          = '';
                $arrayData["MESS_PASSWORD_HIDDEN"]     = '';
                $arrayData["MESS_EXECUTE_EVERY"]       = '';
                $arrayData["MESS_SEND_MAX"]            = '';
            }

            //Return
            return $arrayData;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Get all Email Servers
     *
     * @param array  $arrayFilterData Data of the filters
     * @param string $sortField       Field name to sort
     * @param string $sortDir         Direction of sorting (ASC, DESC)
     * @param int    $start           Start
     * @param int    $limit           Limit
     *
     * @return array, return an array with all Email Servers
     * @throws Exception
     */
    public function getEmailServers($arrayFilterData = null, $sortField = null, $sortDir = null, $start = null, $limit = null)
    {
        try {
            $arrayEmailServer = array();

            //Verify data
            $process = new \ProcessMaker\BusinessModel\Process();

            $process->throwExceptionIfDataNotMetPagerVarDefinition(array("start" => $start, "limit" => $limit), $this->arrayFieldNameForException);

            //Get data
            if (!is_null($limit) && $limit . "" == "0") {
                return $arrayEmailServer;
            }

            //SQL
            $criteria = $this->getEmailServerCriteria();

            if (!is_null($arrayFilterData) && is_array($arrayFilterData) && isset($arrayFilterData["filter"]) && trim($arrayFilterData["filter"]) != "") {
                $criteria->add(
                    $criteria->getNewCriterion(\EmailServerPeer::MESS_ENGINE,    "%" . $arrayFilterData["filter"] . "%", \Criteria::LIKE)->addOr(
                        $criteria->getNewCriterion(\EmailServerPeer::MESS_SERVER,    "%" . $arrayFilterData["filter"] . "%", \Criteria::LIKE))->addOr(
                        $criteria->getNewCriterion(\EmailServerPeer::MESS_ACCOUNT,   "%" . $arrayFilterData["filter"] . "%", \Criteria::LIKE))->addOr(
                        $criteria->getNewCriterion(\EmailServerPeer::MESS_FROM_NAME, "%" . $arrayFilterData["filter"] . "%", \Criteria::LIKE))->addOr(
                        $criteria->getNewCriterion(\EmailServerPeer::SMTPSECURE,     "%" . $arrayFilterData["filter"] . "%", \Criteria::LIKE))
                );
            }

            //Number records total
            $criteriaCount = clone $criteria;

            $criteriaCount->clearSelectColumns();
            $criteriaCount->addSelectColumn("COUNT(" . \EmailServerPeer::MESS_UID . ") AS NUM_REC");

            $rsCriteriaCount = \EmailServerPeer::doSelectRS($criteriaCount);
            $rsCriteriaCount->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

            $rsCriteriaCount->next();
            $row = $rsCriteriaCount->getRow();

            $numRecTotal = $row["NUM_REC"];

            //SQL
            if (!is_null($sortField) && trim($sortField) != "") {
                $sortField = strtoupper($sortField);

                if (in_array($sortField, array("MESS_ENGINE", "MESS_SERVER", "MESS_ACCOUNT", "MESS_FROM_NAME", "SMTPSECURE"))) {
                    $sortField = \EmailServerPeer::TABLE_NAME . "." . $sortField;
                } else {
                    $sortField = \EmailServerPeer::MESS_ENGINE;
                }
            } else {
                $sortField = \EmailServerPeer::MESS_ENGINE;
            }

            if (!is_null($sortDir) && trim($sortDir) != "" && strtoupper($sortDir) == "DESC") {
                $criteria->addDescendingOrderByColumn($sortField);
            } else {
                $criteria->addAscendingOrderByColumn($sortField);
            }

            if (!is_null($start)) {
                $criteria->setOffset((int)($start));
            }

            if (!is_null($limit)) {
                $criteria->setLimit((int)($limit));
            }

            $rsCriteria = \EmailServerPeer::doSelectRS($criteria);
            $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

            while ($rsCriteria->next()) {
                $row = $rsCriteria->getRow();

                $passwd = $row["MESS_PASSWORD"];
                $passwdDec = G::decrypt($passwd, "EMAILENCRYPT");
                $auxPass = explode("hash:", $passwdDec);

                if (count($auxPass) > 1) {
                    if (count($auxPass) == 2) {
                        $passwd = $auxPass[1];
                    } else {
                        array_shift($auxPass);
                        $passwd = implode("", $auxPass);
                    }
                }

                $row["MESS_PASSWORD"] = $passwd;

                $arrayEmailServer[] = $this->getEmailServerDataFromRecord($row);
            }

            //Return
            return array(
                "total"  => $numRecTotal,
                "start"  => (int)((!is_null($start))? $start : 0),
                "limit"  => (int)((!is_null($limit))? $limit : 0),
                "filter" => (!is_null($arrayFilterData) && is_array($arrayFilterData) && isset($arrayFilterData["filter"]))? $arrayFilterData["filter"] : "",
                "data"   => $arrayEmailServer
            );
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Get data of a Email Server
     *
     * @param string $emailServerUid Unique id of Email Server
     * @param bool   $flagGetRecord  Value that set the getting
     *
     * @return array, return an array with data of a Email Server
     * @throws Exception
     */
    public function getEmailServer($emailServerUid, $flagGetRecord = false)
    {
        try {
            //Verify data
            $this->throwExceptionIfNotExistsEmailServer($emailServerUid, $this->arrayFieldNameForException["emailServerUid"]);

            //Get data
            //SQL
            $criteria = $this->getEmailServerCriteria();

            $criteria->add(\EmailServerPeer::MESS_UID, $emailServerUid, \Criteria::EQUAL);

            $rsCriteria = \EmailServerPeer::doSelectRS($criteria);
            $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

            $rsCriteria->next();

            $row = $rsCriteria->getRow();

            $row["MESS_PORT"] = (int)($row["MESS_PORT"]);
            $row["MESS_RAUTH"] = (int)($row["MESS_RAUTH"]);
            $row["MESS_TRY_SEND_INMEDIATLY"] = (int)($row["MESS_TRY_SEND_INMEDIATLY"]);
            $row["MESS_DEFAULT"] = (int)($row["MESS_DEFAULT"]);
            $row["MESS_BACKGROUND"] = '';
            $row["MESS_PASSWORD_HIDDEN"] = '';
            $row["MESS_EXECUTE_EVERY"] = '';
            $row["MESS_SEND_MAX"] = '';

            //Return
            return (!$flagGetRecord)? $this->getEmailServerDataFromRecord($row) : $row;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * @param $fromAccount
     * @return array
     */
    public function getUidEmailServer($fromAccount){
        $criteria = new \Criteria("workflow");
        $criteria->addSelectColumn(\EmailServerPeer::MESS_UID);
        $criteria->add(
            $criteria->getNewCriterion(\EmailServerPeer::MESS_ACCOUNT,  $fromAccount, \Criteria::EQUAL)->addOr(
                $criteria->getNewCriterion(\EmailServerPeer::MESS_FROM_MAIL, $fromAccount, \Criteria::EQUAL))
        );
        $criteria->addAsColumn('EMAIL_SERVER_UID', 'MESS_UID');
        $rsCriteria = \EmailServerPeer::doSelectRS($criteria);
        $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);
        $rsCriteria->next();
        return $rsCriteria->getRow();
    }

    /**
     * Logging information related to the email server
     * When the user create, update, delete the email server
     *
     * @param string $channel
     * @param string $level
     * @param string $message
     * @param array $context
     *
     * @return void
     * @throws Exception
     */
    private function syslog(
        $channel,
        $level,
        $message,
        $context = array()
    )
    {
        try {
            Bootstrap::registerMonolog($channel, $level, $message, $context, $context['workspace'], 'processmaker.log');
        } catch (Exception $e) {
            throw $e;
        }
    }
}

