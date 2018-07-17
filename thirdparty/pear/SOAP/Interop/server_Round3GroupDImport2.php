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
// $Id: server_Round3GroupDImport2.php,v 1.6 2007/01/22 14:53:22 yunosh Exp $
//
require_once 'SOAP/Server.php';
require_once 'params_classes.php';

// http://www.whitemesa.com/r3/interop3.html
// http://www.whitemesa.com/r3/plan.html

class SOAP_Interop_GroupDImport2 {

    function echoStruct($inputStruct)
    {
        if (is_object($inputStruct) &&
            strtolower(get_class($inputStruct)) == 'soapstruct') {
            return $inputStruct->__to_soap('Result');
        } else {
            if (is_object($inputStruct)) {
                $inputStruct = get_object_vars($inputStruct);
            }
            $struct = new SOAPStruct($inputStruct['varString'], $inputStruct['varInt'], $inputStruct['varFloat']);
            return $struct->__to_soap('Result');
        }
    }
   
}

// http://www.whitemesa.com/r3/interop3.html
// http://www.whitemesa.com/r3/plan.html

$groupd = new SOAP_Interop_GroupDImport2();
$server = new SOAP_Server();
$server->_auto_translation = true;

$server->addObjectMap($groupd, 'http://soapinterop/');
$server->addObjectMap($groupd, 'http://soapinterop.org/xsd');

if (isset($_SERVER['SERVER_NAME'])) {
    $baseurl = 'http://' . $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] . '/soap_interop/';
    $server->service(isset($HTTP_RAW_POST_DATA) ? $HTTP_RAW_POST_DATA : null);
} else {
    $baseurl = 'http://localhost/soap_interop/';
    $server->bind($baseurl.'wsdl/import2.wsdl.php');
    // allows command line testing of specific request
    $test = '<?xml version="1.0" encoding="UTF-8"?>

<SOAP-ENV:Envelope  xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/"
 xmlns:xsd="http://www.w3.org/2001/XMLSchema"
 xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
 xmlns:SOAP-ENC="http://schemas.xmlsoap.org/soap/encoding/"
 xmlns:ns4="http://soapinterop.org/xsd"
 xmlns:ns5="http://soapinterop/"
 SOAP-ENV:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/">
<SOAP-ENV:Body>

<ns5:echoStruct>
<inputStruct xsi:type="ns4:SOAPStruct">
<varString xsi:type="xsd:string">arg</varString>
<varInt xsi:type="xsd:int">34</varInt>
<varFloat xsi:type="xsd:float">325.325</varFloat></inputStruct></ns5:echoStruct>
</SOAP-ENV:Body>
</SOAP-ENV:Envelope>';
    $server->service($test, '', true);
}
