<?php
/**
 * login.php
 * {projectName}
 *
 */
if (!isset($_SESSION['G_MESSAGE']))
{
	$_SESSION['G_MESSAGE'] = '';
}
if (!isset($_SESSION['G_MESSAGE_TYPE']))
{
	$_SESSION['G_MESSAGE_TYPE'] = '';
}

$msg     = $_SESSION['G_MESSAGE'];
$msgType = $_SESSION['G_MESSAGE_TYPE'];

session_destroy();
session_start();

if (strlen($msg) > 0 )
{
	$_SESSION['G_MESSAGE'] = $msg;
}
if (strlen($msgType) > 0 )
{
	$_SESSION['G_MESSAGE_TYPE'] = $msgType;
}

$G_PUBLISH = new Publisher;
$G_PUBLISH->AddContent('xmlform', 'xmlform', 'login/login', '', '', SYS_URI.'login/authentication.php');

G::RenderPage( "publish" );

