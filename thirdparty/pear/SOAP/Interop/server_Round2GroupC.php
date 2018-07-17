<?php
//
// +----------------------------------------------------------------------+
// | PHP Version 4                                                        |
// +----------------------------------------------------------------------+
// | Copyright (c) 1997-2003 The PHP Group                                |
// +----------------------------------------------------------------------+
// | This source file is subject to version 2.02 of the PHP license,      |
// | that is bundled with this package in the file LICENSE, and is        |
// | available at through the world-wide-web at                           |
// | http://www.php.net/license/2_02.txt.                                 |
// | If you did not receive a copy of the PHP license and are unable to   |
// | obtain it through the world-wide-web, please send a note to          |
// | license@php.net so we can mail you a copy immediately.               |
// +----------------------------------------------------------------------+
// | Authors: Shane Caraveo <Shane@Caraveo.com>                           |
// +----------------------------------------------------------------------+
//
// $Id: server_Round2GroupC.php,v 1.5 2003/08/05 16:56:43 arnaud Exp $
//
require_once 'SOAP/Server.php';
require_once 'interop_Round2Base.php';
require_once 'interop_Round2GroupC.php';

$groupc_headers =& new SOAP_Interop_GroupC_Headers();
$base =& new SOAP_Interop_Base();
$server =& new SOAP_Server;
$server->_auto_translation = true;

$server->addObjectMap($groupc_headers,'http://soapinterop.org/echoheader/');
$server->addObjectMap($base,'http://soapinterop.org/');
$server->service(isset($HTTP_RAW_POST_DATA)?$HTTP_RAW_POST_DATA:NULL);
?>
