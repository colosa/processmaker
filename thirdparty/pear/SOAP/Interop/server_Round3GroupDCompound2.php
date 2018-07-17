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
// $Id: server_Round3GroupDCompound2.php,v 1.5 2007/01/22 14:53:22 yunosh Exp $
//
require_once 'SOAP/Server.php';
require_once 'params_classes.php';

// http://www.whitemesa.com/r3/interop3.html
// http://www.whitemesa.com/r3/plan.html

class SOAP_Interop_GroupDCompound2 {

    function echoEmployee(&$employee)
    {
        return $employee->__to_soap('result_Employee');
    }
    
}

// http://www.whitemesa.com/r3/interop3.html
// http://www.whitemesa.com/r3/plan.html

$options = array('use' => 'literal', 'style' => 'document');
$groupd = new SOAP_Interop_GroupDCompound2();
$server = new SOAP_Server($options);
$server->_auto_translation = true;

$server->addObjectMap($groupd, 'http://soapinterop.org/employee');

$server->bind('http://localhost/soap_interop/wsdl/compound2.wsdl.php');
if (isset($_SERVER['SERVER_NAME'])) {
    $server->service(isset($HTTP_RAW_POST_DATA) ? $HTTP_RAW_POST_DATA : NULL);
} else {
    $test = '<?xml version="1.0" encoding="UTF-8"?>
    
    <SOAP-ENV:Envelope  xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/"
     xmlns:xsd="http://www.w3.org/2001/XMLSchema"
     xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
     xmlns:SOAP-ENC="http://schemas.xmlsoap.org/soap/encoding/"
     xmlns:ns4="http://soapinterop.org/person"
     xmlns:ns5="http://soapinterop.org/employee"
    >
    <SOAP-ENV:Body>
    
    <ns5:x_Employee>
    <ns5:person>
    <ns4:Name>Shane</ns4:Name>
    <ns4:Male>true</ns4:Male></ns5:person>
    <ns5:salary>1000000</ns5:salary>
    <ns5:ID>12345</ns5:ID></ns5:x_Employee>
    </SOAP-ENV:Body>
    </SOAP-ENV:Envelope>';
    $server->service($test, '', true);
}
