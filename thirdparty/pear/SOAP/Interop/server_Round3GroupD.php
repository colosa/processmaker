<?php
//
// +----------------------------------------------------------------------+
// | PHP Version 4                                                        |
// +----------------------------------------------------------------------+
// | Copyright (c) 1997-2002 The PHP Group                                |
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
// $Id: server_Round3GroupD.php,v 1.4 2005/04/01 16:18:00 chagenbu Exp $
//
require_once 'SOAP/Server.php';
require_once 'interop_Round3GroupD.php';

// http://www.whitemesa.com/r3/interop3.html
// http://www.whitemesa.com/r3/plan.html

$groupd = new SOAP_Interop_GroupD();
$server = new SOAP_Server;
$server->_auto_translation = true;

$server->addObjectMap($groupd, 'http://soapinterop/');
$server->addObjectMap($groupd, 'http://soapinterop.org/xsd');

$server->service(isset($HTTP_RAW_POST_DATA) ? $HTTP_RAW_POST_DATA : NULL);

$test = '<?xml version="1.0" encoding="UTF-8"?>

<SOAP-ENV:Envelope  xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/"
 xmlns:xsd="http://www.w3.org/2001/XMLSchema"
 xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
 xmlns:SOAP-ENC="http://schemas.xmlsoap.org/soap/encoding/"
 xmlns:ns4="http://soapinterop.org/xsd"
>
<SOAP-ENV:Body>

<ns4:x_Document>Test Document Here</ns4:x_Document>
</SOAP-ENV:Body>
</SOAP-ENV:Envelope>';
#$server->service($test,'',TRUE);
