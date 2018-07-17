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
// $Id: server_Round3GroupDImport1.php,v 1.5 2007/01/22 14:53:22 yunosh Exp $
//
require_once 'SOAP/Server.php';
require_once 'params_classes.php';

// http://www.whitemesa.com/r3/interop3.html
// http://www.whitemesa.com/r3/plan.html

class SOAP_Interop_GroupDImport1 {

    function echoString($inputString)
    {
        return new SOAP_Value('Result', 'string', $inputString);
    }
}

// http://www.whitemesa.com/r3/interop3.html
// http://www.whitemesa.com/r3/plan.html

$groupd = new SOAP_Interop_GroupDImport1();
$server = new SOAP_Server();
$server->_auto_translation = true;

$server->addObjectMap($groupd, 'http://soapinterop/echoString/');

$server->bind('http://localhost/soap_interop/wsdl/import1.wsdl.php');
if (isset($_SERVER['SERVER_NAME'])) {
    $server->service(isset($HTTP_RAW_POST_DATA) ? $HTTP_RAW_POST_DATA : null);
} else {
    // allows command line testing of specific request
    $test = '<?xml version="1.0" encoding="UTF-8"?>

<SOAP-ENV:Envelope  xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/"
 xmlns:xsd="http://www.w3.org/2001/XMLSchema"
 xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
 xmlns:SOAP-ENC="http://schemas.xmlsoap.org/soap/encoding/"
 xmlns:ns4="http://soapinterop/echoString/"
 SOAP-ENV:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/">
<SOAP-ENV:Body>

<ns4:echoString>
<x xsi:type="xsd:string">Hello World</x></ns4:echoString>
</SOAP-ENV:Body>
</SOAP-ENV:Envelope>';
    $server->service($test, '', true);
}
