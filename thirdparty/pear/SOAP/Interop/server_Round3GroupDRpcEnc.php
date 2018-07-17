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
// $Id: server_Round3GroupDRpcEnc.php,v 1.6 2007/01/22 14:53:22 yunosh Exp $
//
require_once 'SOAP/Server.php';
require_once 'params_classes.php';

// http://www.whitemesa.com/r3/interop3.html
// http://www.whitemesa.com/r3/plan.html

class SOAP_Interop_GroupDRpcEnc {

    // wsdlns:SoapInteropEmptySABinding
    function echoString($inputString)
    {
        return new SOAP_Value('echoStringReturn', 'string', $inputString);
    }

    function echoStringArray($inputStringArray)
    {
        $ra = array();
        if ($inputStringArray) {
            foreach($inputStringArray as $s) {
                $ra[] = new SOAP_Value('item', 'string', $s);
            }
        }
        return new SOAP_Value('echoStringArrayReturn', null, $ra);
    }

    function echoStruct($inputStruct)
    {
        if (is_object($inputStruct) &&
            strtolower(get_class($inputStruct)) == 'soapstruct') {
            return $inputStruct->__to_soap('return');
        } else {
            if (is_object($inputStruct)) {
                $inputStruct = get_object_vars($inputStruct);
            }
            $struct = new SOAPStruct($inputStruct['varString'], $inputStruct['varInt'], $inputStruct['varFloat']);
            return $struct->__to_soap('return');
        }
    }

    function echoVoid()
    {
        return null;
    }

}

// http://www.whitemesa.com/r3/interop3.html
// http://www.whitemesa.com/r3/plan.html

$groupd = new SOAP_Interop_GroupDRpcEnc();
$server = new SOAP_Server();
$server->_auto_translation = true;

$server->addObjectMap($groupd, 'http://soapinterop/');
$server->addObjectMap($groupd, 'http://soapinterop.org/xsd');
$server->addObjectMap($groupd, 'http://soapinterop.org/WSDLInteropTestRpcEnc');

$server->bind('http://localhost/soap_interop/wsdl/InteropTestRpcEnc.wsdl.php');
$server->service(isset($HTTP_RAW_POST_DATA) ? $HTTP_RAW_POST_DATA : null);
