<?php
if (($RBAC_Response = $RBAC->userCanAccess("PM_FACTORY")) != 1) {
    return $RBAC_Response;
}





require_once ("classes/model/Triggers.php");





$arrayField = array();
$arrayField["LANG"] = SYS_LANG;
$arrayField["PRO_UID"] = $_GET["PRO_UID"];
$arrayField["TRI_TYPE"] = "SCRIPT";

if (isset($_GET["TRI_UID"]) && !empty($_GET["TRI_UID"])) {
    $oTrigger = new Triggers();
    $arrayField = $oTrigger->load($_GET["TRI_UID"]);
}

$G_PUBLISH = new Publisher();
$G_PUBLISH->AddContent("xmlform", "xmlform", "triggers/triggerCopy", "", $arrayField, "../triggers/triggers_Save");
$oHeadPublisher =& headPublisher::getSingleton();
//$oHeadPublisher->addScriptFile('/js/codemirror/js/codemirror.js', 1);
$oHeadPublisher->addScriptFile('/js/codemirror/lib/codemirror.js', 1);
$oHeadPublisher->addScriptFile("/js/codemirror/addon/edit/matchbrackets.js",1);
$oHeadPublisher->addScriptFile("/js/codemirror/mode/htmlmixed/htmlmixed.js",1);
$oHeadPublisher->addScriptFile("/js/codemirror/mode/xml/xml.js",1);
$oHeadPublisher->addScriptFile("/js/codemirror/mode/javascript/javascript.js",1);
$oHeadPublisher->addScriptFile("/js/codemirror/mode/css/css.js",1);
$oHeadPublisher->addScriptFile("/js/codemirror/mode/clike/clike.js",1);
$oHeadPublisher->addScriptFile("/js/codemirror/mode/php/php.js",1);
G::RenderPage("publish", "raw");

