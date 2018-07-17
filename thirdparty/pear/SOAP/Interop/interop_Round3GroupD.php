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
// $Id: interop_Round3GroupD.php,v 1.5 2007/01/22 14:53:21 yunosh Exp $
//
require_once 'params_classes.php';

// http://www.whitemesa.com/r3/interop3.html
// http://www.whitemesa.com/r3/plan.html

class SOAP_Interop_GroupD {

    // wsdlns:SoapInteropEmptySABinding
    function echoString($inputString)
    {
        return new SOAP_Value('outputString', 'string', $inputString);
    }

    function echoStringArray($inputStringArray)
    {
        $ra = array();
        if ($inputStringArray) {
            foreach ($inputStringArray as $s) {
                $ra[] = new SOAP_Value('item', 'string', $s);
            }
        }
        return new SOAP_Value('outputStringArray', null, $ra);
    }

    function echoStruct($inputStruct)
    {
        return $inputStruct->to_soap();
    }

    function echoStructArray($inputStructArray)
    {
        $ra = array();
        if ($inputStructArray) {
            $c = count($inputStructArray);
            for ($i = 0; $i < $c; $i++) {
                $ra[] = $inputStructArray[$i]->to_soap();
            }
        }
        return $ra;
    }

    function echoVoid()
    {
        return null;
    }

    function echoPerson()
    {
        return null;
    }

    function x_Document(&$document)
    {
        return new SOAP_Value('result_Document', '{http://soapinterop.org/xsd}x_Document', $document);
    }

    function echoEmployee()
    {
        return null;
    }

}
