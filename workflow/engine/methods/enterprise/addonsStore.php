<?php

use ProcessMaker\Core\System;

AddonsStore::checkLicenseStore();

$licenseManager = &PmLicenseManager::getSingleton();
$oHeadPublisher = &headPublisher::getSingleton();

if (isset($licenseManager->date) && is_array($licenseManager->date)) {
    $conf = new Configurations();
    if ( defined('SYS_SYS') && $conf->exists("ENVIRONMENT_SETTINGS")) {
        $licenseManager->date['START']  = date("Y-m-d H:i:s", strtotime($licenseManager->date['HUMAN']['START']));
        $licenseManager->date['END']    = date("Y-m-d H:i:s", strtotime($licenseManager->date['HUMAN']['END']));
        $licenseManager->date['START']  = $conf->getSystemDate($licenseManager->date['START']);
        $licenseManager->date['END']    = $conf->getSystemDate($licenseManager->date['END']);
    } else {
        $licenseManager->date['START']  = date("Y-m-d H:i:s", strtotime($licenseManager->date['HUMAN']['START']));
        $licenseManager->date['END']    = date("Y-m-d H:i:s", strtotime($licenseManager->date['HUMAN']['END']));
        $licenseManager->date['START']  = G::getformatedDate($licenseManager->date['START'], 'M d, yyyy', SYS_LANG);
        $licenseManager->date['END']    = G::getformatedDate($licenseManager->date['END'], 'M d, yyyy', SYS_LANG);
    }
}

if (isset($licenseManager->result) && $licenseManager->result == "OK") {
    $oHeadPublisher->assign("license_start_date",$licenseManager->date["START"]);
    $oHeadPublisher->assign("license_end_date", $licenseManager->expireIn!="NEVER" ? $licenseManager->date["END"]:"NA" );
    $oHeadPublisher->assign("license_user",     $licenseManager->info["FIRST_NAME"] . " " . $licenseManager->info["LAST_NAME"] . " (" . $licenseManager->info["DOMAIN_WORKSPACE"] . ")");
    $oHeadPublisher->assign("license_span",     $licenseManager->expireIn != "NEVER" ? ceil($licenseManager->date["SPAN"]/60/60/24) : "~");
    $oHeadPublisher->assign("license_name",     $licenseManager->type);
    $oHeadPublisher->assign("license_server",   $licenseManager->server);
    $oHeadPublisher->assign("license_expires",  $licenseManager->expireIn);
    $oHeadPublisher->assign("license_message",  $licenseManager->status["message"]);
    $oHeadPublisher->assign("licensed", true);
}
elseif (isset($licenseManager->info)) {
    $oHeadPublisher->assign("license_start_date", $licenseManager->date["START"]);
    $oHeadPublisher->assign("license_end_date", $licenseManager->date["END"]);
    $oHeadPublisher->assign("license_span", $licenseManager->expireIn != "NEVER" ? ceil($licenseManager->date["SPAN"]/60/60/24) : "~");
    $oHeadPublisher->assign("license_user", $licenseManager->info["FIRST_NAME"] . " " . $licenseManager->info["LAST_NAME"] . " (" . $licenseManager->info["DOMAIN_WORKSPACE"] . ")");
    $oHeadPublisher->assign("license_name", $licenseManager->type);
    $oHeadPublisher->assign("license_server",  $licenseManager->server);
    $oHeadPublisher->assign("license_expires", $licenseManager->expireIn);
    $oHeadPublisher->assign("license_message", $licenseManager->status["message"]);
    $oHeadPublisher->assign("licensed", false);
} else {
    $oHeadPublisher->assign("license_user", "");
    $oHeadPublisher->assign("license_name", "<b>Unlicensed</b>");
    $oHeadPublisher->assign("license_server",  "<b>no server</b>");
    $oHeadPublisher->assign("license_expires", "");

    $currentLicenseStatus = $licenseManager->getCurrentLicenseStatus();

    $oHeadPublisher->assign("license_message", $currentLicenseStatus["message"]);
    $oHeadPublisher->assign("license_start_date", "");
    $oHeadPublisher->assign("license_end_date", "");
    $oHeadPublisher->assign("license_span", "");
    $oHeadPublisher->assign("licensed", false);
}
$oHeadPublisher->assign("license_serial", (isset($licenseManager->licenseSerial))? $licenseManager->licenseSerial : '');
$oHeadPublisher->assign("SUPPORT_FLAG", ((isset($licenseManager->supportStartDate) && $licenseManager->supportStartDate == '') || !isset($licenseManager->supportStartDate)) ? true : false );
$oHeadPublisher->assign("supportStartDate", (isset($licenseManager->supportStartDate))? $licenseManager->supportStartDate : '');
$oHeadPublisher->assign("supportEndDate", (isset($licenseManager->supportEndDate))? $licenseManager->supportEndDate : '');

$oHeadPublisher->assign("PROCESSMAKER_VERSION", System::getVersion());
$oHeadPublisher->assign("PROCESSMAKER_URL", "/sys" . SYS_SYS . "/" . SYS_LANG . "/" . SYS_SKIN );
$oHeadPublisher->assign("SYS_SKIN", SYS_SKIN);
$oHeadPublisher->assign("URL_PART_LOGIN", ((substr(SYS_SKIN, 0, 2) == "ux" && SYS_SKIN != "uxs")? "main/login" : "login/login"));
$oHeadPublisher->assign("URL_PART_SETUP", EnterpriseUtils::getUrlPartSetup());
$oHeadPublisher->assign("PATH_PLUGINS_WRITABLE", ((is_writable(PATH_PLUGINS))? 1 : 0));
$oHeadPublisher->assign("PATH_PLUGINS_WRITABLE_MESSAGE", "The directory " . PATH_PLUGINS . " have not writable.");
$oHeadPublisher->assign("SKIN_IS_UX", EnterpriseUtils::skinIsUx());
$oHeadPublisher->assign("INTERNET_CONNECTION", EnterpriseUtils::getInternetConnection());

$oHeadPublisher->addExtJsScript("enterprise/addonsStore", true);
G::RenderPage("publish", "extJs");

