<?php
$option = (isset($_POST["option"]))? $_POST["option"] : "";
$response = array();

$RBAC->allows(basename(__FILE__), $option);
switch ($option) {
    case "INS":
        $arrayData = array();

        $server = "";
        $port = "";
        $reqAuthentication = 0;
        $password = "";
        $smtpSecure = "";

        $cboEmailEngine = $_POST["cboEmailEngine"];
        $accountFrom = (isset($_POST["accountFrom"]))? $_POST["accountFrom"] : "";
        $fromName = $_POST["fromName"];
        $fromMail = $_POST["fromMail"];
        $sendTestMail = (int)($_POST["sendTestMail"]);
        $mailTo = ($sendTestMail == 1)? $_POST["mailTo"] : "";
        $emailServerDefault = (int)($_POST["emailServerDefault"]);

        if ($cboEmailEngine == "PHPMAILER") {
            $server = $_POST["server"];
            $port = (int)($_POST["port"]);
            $reqAuthentication = (int)($_POST["reqAuthentication"]);
            $password = ($reqAuthentication == 1)? $_POST["password"] : "";
            $smtpSecure = $_POST["smtpSecure"];
        }

        try {
            $arrayData = array(
                "MESS_ENGINE"              => $cboEmailEngine,
                "MESS_SERVER"              => $server,
                "MESS_PORT"                => $port,
                "MESS_RAUTH"               => $reqAuthentication,
                "MESS_ACCOUNT"             => $accountFrom,
                "MESS_PASSWORD"            => $password,
                "MESS_FROM_MAIL"           => $fromMail,
                "MESS_FROM_NAME"           => $fromName,
                "SMTPSECURE"               => $smtpSecure,
                "MESS_TRY_SEND_INMEDIATLY" => $sendTestMail,
                "MAIL_TO"                  => $mailTo,
                "MESS_DEFAULT"             => $emailServerDefault
            );

            $emailSever = new \ProcessMaker\BusinessModel\EmailServer();

            $arrayEmailServerData = $emailSever->create($arrayData);

            $response["status"] = "OK";
            $response["data"]   = $arrayEmailServerData;
        } catch (Exception $e) {
            $response["status"]  = "ERROR";
            $response["message"] = $e->getMessage();
        }
        break;
    case "UPD":
        $arrayData = array();

        $emailServerUid = $_POST["emailServerUid"];

        $server = "";
        $port = "";
        $reqAuthentication = 0;
        $password = "";
        $smtpSecure = "";

        $cboEmailEngine = $_POST["cboEmailEngine"];
        $accountFrom = (isset($_POST["accountFrom"]))? $_POST["accountFrom"] : "";
        $fromName = $_POST["fromName"];
        $fromMail = $_POST["fromMail"];
        $sendTestMail = (int)($_POST["sendTestMail"]);
        $mailTo = ($sendTestMail == 1)? $_POST["mailTo"] : "";
        $emailServerDefault = (int)($_POST["emailServerDefault"]);

        if ($cboEmailEngine == "PHPMAILER") {
            $server = $_POST["server"];
            $port = (int)($_POST["port"]);
            $reqAuthentication = (int)($_POST["reqAuthentication"]);
            $password = ($reqAuthentication == 1)? $_POST["password"] : "";
            $smtpSecure = $_POST["smtpSecure"];
        }

        try {
            $arrayData = array(
                "MESS_ENGINE"              => $cboEmailEngine,
                "MESS_SERVER"              => $server,
                "MESS_PORT"                => $port,
                "MESS_RAUTH"               => $reqAuthentication,
                "MESS_ACCOUNT"             => $accountFrom,
                "MESS_PASSWORD"            => $password,
                "MESS_FROM_MAIL"           => $fromMail,
                "MESS_FROM_NAME"           => $fromName,
                "SMTPSECURE"               => $smtpSecure,
                "MESS_TRY_SEND_INMEDIATLY" => $sendTestMail,
                "MAIL_TO"                  => $mailTo,
                "MESS_DEFAULT"             => $emailServerDefault
            );

            $emailSever = new \ProcessMaker\BusinessModel\EmailServer();

            $arrayEmailServerData = $emailSever->update($emailServerUid, $arrayData);

            $response["status"] = "OK";
            $response["data"]   = $arrayEmailServerData;
        } catch (Exception $e) {
            $response["status"]  = "ERROR";
            $response["message"] = $e->getMessage();
        }

        break;
    case "DEL":
        $emailServerUid = $_POST["emailServerUid"];

        try {
            $emailSever = new \ProcessMaker\BusinessModel\EmailServer();

            $result = $emailSever->delete($emailServerUid);

            $response["status"] = "OK";
        } catch (Exception $e) {
            $response["status"]  = "ERROR";
            $response["message"] = $e->getMessage();
        }
        break;
    case "LST":
        $pageSize = $_POST["pageSize"];
        $search = $_POST["search"];

        $sortField = (isset($_POST["sort"]))? $_POST["sort"]: "";
        $sortDir   = (isset($_POST["dir"]))? $_POST["dir"]: "";
        $start = (isset($_POST["start"]))? $_POST["start"]: 0;
        $limit = (isset($_POST["limit"]))? $_POST["limit"]: $pageSize;

        try {
            $emailSever = new \ProcessMaker\BusinessModel\EmailServer();

            $result = $emailSever->getEmailServers(array("filter" => $search), $sortField, $sortDir, $start, $limit);

            $response["status"]  = "OK";
            $response["success"] = true;
            $response["resultTotal"] = $result["total"];
            $response["resultRoot"]  = $result["data"];
        } catch (Exception $e) {
            $response["status"]  = "ERROR";
            $response["message"] = $e->getMessage();
        }
        break;
    case "TEST":
        $arrayData = array();

        $server = "";
        $port = "";
        $reqAuthentication = 0;
        $password = "";
        $smtpSecure = "";

        $cboEmailEngine = $_POST["cboEmailEngine"];
        $accountFrom = (isset($_POST["accountFrom"]))? $_POST["accountFrom"] : "";
        $fromName = $_POST["fromName"];
        $fromMail = $_POST["fromMail"];
        $sendTestMail = (int)($_POST["sendTestMail"]);
        $mailTo = ($sendTestMail == 1)? $_POST["mailTo"] : "";
        $emailServerDefault = (int)($_POST["emailServerDefault"]);

        if ($cboEmailEngine == "PHPMAILER") {
            $server = $_POST["server"];
            $port = (int)($_POST["port"]);
            $reqAuthentication = (int)($_POST["reqAuthentication"]);
            $password = ($reqAuthentication == 1)? $_POST["password"] : "";
            $smtpSecure = $_POST["smtpSecure"];
        }

        try {
            $arrayData = array(
                "MESS_ENGINE"              => $cboEmailEngine,
                "MESS_SERVER"              => $server,
                "MESS_PORT"                => $port,
                "MESS_RAUTH"               => $reqAuthentication,
                "MESS_ACCOUNT"             => $accountFrom,
                "MESS_PASSWORD"            => $password,
                "MESS_FROM_MAIL"           => $fromMail,
                "MESS_FROM_NAME"           => $fromName,
                "SMTPSECURE"               => $smtpSecure,
                "MESS_TRY_SEND_INMEDIATLY" => $sendTestMail,
                "MAIL_TO"                  => $mailTo,
                "MESS_DEFAULT"             => $emailServerDefault
            );

            $emailSever = new \ProcessMaker\BusinessModel\EmailServer();

            $arrayEmailServerData = $emailSever->testConnection($arrayData);

            $response["data"]   = $arrayEmailServerData;
        } catch (Exception $e) {
            $response["status"]  = "ERROR";
            $response["message"] = $e->getMessage();
        }
        break;
}

echo G::json_encode($response);

