<?php
/**
 * This page displays a message when completed or if there is an error
 * during the execution.
 */
$G_PUBLISH = new Publisher();
$show = "login/showMessage";
$message = [];
if (isset($_GET["message"])) {
    $show = "login/showInfo";
    $message['MESSAGE'] = nl2br($_GET["message"]);
} elseif (isset($_GET["error"])) {
    $show = "login/showMessage";
    $message['MESSAGE'] = $_GET["error"];
}
$G_PUBLISH->AddContent("xmlform", "xmlform", $show, "", $message);
G::RenderPage("publish", "blank");

