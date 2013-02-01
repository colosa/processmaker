<?php
$option = (isset($_GET["option"]))? $_GET["option"] : null;

switch ($option) {
    case "phpinfo":
        phpinfo();
        break;
    case "php":
        $oHeadPublisher = &headPublisher::getSingleton();
        $oHeadPublisher->addContent("setup/phpInfo"); //Adding a html file .html
        $oHeadPublisher->addExtJsScript("setup/phpInfo", false); //Adding a javascript file .js

        G::RenderPage("publish", "extJs");
        break;
    case "processInfo":
        $oHeadPublisher = &headPublisher::getSingleton();
        $oHeadPublisher->addContent("setup/dbInfo"); //Adding a html file .html
        $oHeadPublisher->addExtJsScript("setup/dbInfo", false); //Adding a javascript file .js

        G::RenderPage("publish", "extJs");
        break;
    default:
        require_once (PATH_CONTROLLERS . "installer.php");

        $installer = new Installer();

        $systemInfo = $installer->getSystemInfo();

        $oHeadPublisher = &headPublisher::getSingleton();
        $oHeadPublisher->addContent("setup/systemInfo"); //Adding a html file .html
        $oHeadPublisher->addExtJsScript("setup/systemInfo", false); //Adding a javascript file .js

        $oHeadPublisher->assign("SYSINFO_PHP", "\"" . $systemInfo->php->version . "\", " . (($systemInfo->php->result)? 1 : 0));
        $oHeadPublisher->assign("SYSINFO_MYSQL", "\"" . $systemInfo->mysql->version . "\", " . (($systemInfo->mysql->result)? 1 : 0));
        $oHeadPublisher->assign("SYSINFO_MSSQL", "\"" . $systemInfo->mssql->version . "\", " . (($systemInfo->mssql->result)? 1 : 0));
        $oHeadPublisher->assign("SYSINFO_CURL", "\"" . $systemInfo->curl->version . "\", " . (($systemInfo->curl->result)? 1 : 0));
        $oHeadPublisher->assign("SYSINFO_OPENSSL", "\"" . $systemInfo->openssl->version . "\", " . (($systemInfo->openssl->result)? 1 : 0));
        $oHeadPublisher->assign("SYSINFO_DOMXML", "\"" . $systemInfo->dom->version . "\", " . (($systemInfo->dom->result)? 1 : 0));
        $oHeadPublisher->assign("SYSINFO_GD", "\"" . $systemInfo->gd->version . "\", " . (($systemInfo->gd->result)? 1 : 0));
        $oHeadPublisher->assign("SYSINFO_MULTIBYTESTRING", "\"" . $systemInfo->multibyte->version . "\", " . (($systemInfo->multibyte->result)? 1 : 0));
        $oHeadPublisher->assign("SYSINFO_SOAP", "\"" . $systemInfo->soap->version . "\", " . (($systemInfo->soap->result)? 1 : 0));
        $oHeadPublisher->assign("SYSINFO_LDAP", "\"" . $systemInfo->ldap->version . "\", " . (($systemInfo->ldap->result)? 1 : 0));
        $oHeadPublisher->assign("SYSINFO_MEMORYLIMIT", "\"" . $systemInfo->memory->version . "\", " . (($systemInfo->memory->result)? 1 : 0));

        G::RenderPage("publish", "extJs");
        break;
}

