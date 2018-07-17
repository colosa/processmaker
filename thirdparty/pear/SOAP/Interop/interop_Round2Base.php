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
// | Authors: Shane Caraveo <Shane@Caraveo.com>   Port to PEAR and more   |
// | Authors: Dietrich Ayala <dietrich@ganx4.com> Original Author         |
// +----------------------------------------------------------------------+
//
// $Id: interop_Round2Base.php,v 1.9 2007/01/26 00:06:11 yunosh Exp $
//
require_once 'params_classes.php';

function &generateFault($short, $long)
{
    $params = array(
        'faultcode' => 'Server', 
        'faultstring' => $short, 
        'detail' => $long
    );

    $faultmsg  = new SOAP_Message('Fault', $params, 'http://schemas.xmlsoap.org/soap/envelope/');
    return $faultmsg;
}

function hex2bin($data)
{
    $len = strlen($data);
    return pack('H' . $len, $data);
}


class SOAP_Interop_Base {

    function echoString($inputString)
    {
        return new SOAP_Value('outputString', 'string', $inputString);
    }

    function echoStringArray($inputStringArray)
    {
        $ra = array();
        if ($inputStringArray) {
            foreach($inputStringArray as $s) {
                $ra[] = new SOAP_Value('item', 'string', $s);
            }
        }
        return new SOAP_Value('outputStringArray', null, $ra);
    }


    function echoInteger($inputInteger)
    {
        return new SOAP_Value('outputInteger', 'int', (integer)$inputInteger);
    }

    function echoIntegerArray($inputIntegerArray)
    {
        $ra = array();
        if ($inputIntegerArray) {
            foreach ($inputIntegerArray as $i) {
                $ra[] = new SOAP_Value('item', 'int', $i);
            }
        }
        return new SOAP_Value('outputIntArray', null, $ra);
    }

    function echoFloat($inputFloat)
    {
        return new SOAP_Value('outputFloat', 'float', (float)$inputFloat);
    }

    function echoFloatArray($inputFloatArray)
    {
        $ra = array();
        if ($inputFloatArray) {
            foreach($inputFloatArray as $float) {
                $ra[] = new SOAP_Value('item', 'float', (FLOAT)$float);
            }
        }
        return new SOAP_Value('outputFloatArray', null, $ra);
    }

    function echoStruct($inputStruct)
    {
        if (is_object($inputStruct) && strtolower(get_class($inputStruct)) == 'soapstruct') {
            return $inputStruct->__to_soap();
        }
        return $inputStruct;
    }

    function echoStructArray($inputStructArray)
    {
        $ra = array();
        if ($inputStructArray) {
            $c = count($inputStructArray);
            for ($i = 0; $i < $c; $i++) {
                $ra[] = $this->echoStruct($inputStructArray[$i]);
            }
        }
        return $ra;
    }

    function echoVoid()
    {
        return NULL;
    }

    function echoBase64($b_encoded)
    {
        return new SOAP_Value('return', 'base64Binary', base64_encode(base64_decode($b_encoded)));
    }

    function echoDate($timeInstant)
    {
        require_once 'SOAP/Type/dateTime.php';
        $dt = new SOAP_Type_dateTime($timeInstant);
        if ($dt->toUnixtime() != -1) {
            $value = $dt->toSOAP();
            return new SOAP_Value('return', 'dateTime', $value);
        } else {
            return new SOAP_Fault("Value $timeInstant is not a dateTime value");
        }
    }

    function echoHexBinary($hb)
    {
        return new SOAP_Value('return', 'hexBinary', bin2hex(hex2bin($hb)));
    }

    function echoDecimal($dec)
    {
        return new SOAP_Value('return', 'decimal', (float)$dec);
    }

    function echoBoolean($boolean)
    {
        return new SOAP_Value('return', 'boolean', $boolean);
    }

    function echoMimeAttachment($stuff)
    {
        return new SOAP_Attachment('return', 'application/octet-stream', null, $stuff);
    }
}
