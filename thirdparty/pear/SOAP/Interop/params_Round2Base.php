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
// $Id: params_Round2Base.php,v 1.6 2007/01/26 14:54:51 yunosh Exp $
//

require_once 'params_values.php';
require_once 'interop_test.php';
define('INTEROP_R2BASE', 'Round 2 Base');

//***********************************************************
// Base echoString

$soap_tests[INTEROP_R2BASE][] = new SOAP_Interop_Test(
    'echoString',
    array('inputString' => $string),
    $soap_test_null);
$soap_tests[INTEROP_R2BASE][] = new SOAP_Interop_Test(
    'echoString',
    array('inputString' => $string_soapval));
$soap_tests[INTEROP_R2BASE][] = new SOAP_Interop_Test(
    'echoString(null)',
    array('inputString' => $string_null));
$soap_tests[INTEROP_R2BASE][] = new SOAP_Interop_Test(
    'echoString(null)',
    array('inputString' => $string_null_soapval));
$soap_tests[INTEROP_R2BASE][] = new SOAP_Interop_Test(
    'echoString(entities)',
    array('inputString' => $string_entities));
$soap_tests[INTEROP_R2BASE][] = new SOAP_Interop_Test(
    'echoString(entities)',
    array('inputString' => $string_entities_soapval));
$test = new SOAP_Interop_Test(
    'echoString(utf-8)',
    array('inputString' => $string_utf8));
$test->encoding = 'UTF-8';
$soap_tests[INTEROP_R2BASE][] = $test;
unset($test);

$test = new SOAP_Interop_Test(
    'echoString(utf-8)',
    array('inputString' => $string_utf8_soapval));
$test->encoding = 'UTF-8';
$soap_tests[INTEROP_R2BASE][] = $test;
unset($test);

//***********************************************************
// Base echoStringArray

$soap_tests[INTEROP_R2BASE][] = new SOAP_Interop_Test(
    'echoStringArray',
    array('inputStringArray' => $string_array));
$soap_tests[INTEROP_R2BASE][] = new SOAP_Interop_Test(
    'echoStringArray',
    array('inputStringArray' => $string_array_soapval));
$soap_tests[INTEROP_R2BASE][] = new SOAP_Interop_Test(
    'echoStringArray(one)',
    array('inputStringArray' => $string_array_one));
$soap_tests[INTEROP_R2BASE][] = new SOAP_Interop_Test(
    'echoStringArray(one)',
    array('inputStringArray' => $string_array_one_soapval));
// null array test
$soap_tests[INTEROP_R2BASE][] = new SOAP_Interop_Test(
    'echoStringArray(null)',
    array('inputStringArray' => $string_array_null));
$soap_tests[INTEROP_R2BASE][] = new SOAP_Interop_Test(
    'echoStringArray(null)',
    array('inputStringArray' => $string_array_null_soapval));

//***********************************************************
// Base echoInteger

$soap_tests[INTEROP_R2BASE][] = new SOAP_Interop_Test(
    'echoInteger',
    array('inputInteger' => $integer));
$soap_tests[INTEROP_R2BASE][] = new SOAP_Interop_Test(
    'echoInteger',
    array('inputInteger' => $integer_soapval));

//***********************************************************
// Base echoIntegerArray

$soap_tests[INTEROP_R2BASE][] = new SOAP_Interop_Test(
    'echoIntegerArray',
    array('inputIntegerArray' => $integer_array));
$soap_tests[INTEROP_R2BASE][] = new SOAP_Interop_Test(
    'echoIntegerArray',
    array('inputIntegerArray' => $integer_array_soapval));

// null array test
$soap_tests[INTEROP_R2BASE][] = new SOAP_Interop_Test(
    'echoIntegerArray(null)',
    array('inputIntegerArray' => $integer_array_null));
$soap_tests[INTEROP_R2BASE][] = new SOAP_Interop_Test(
    'echoIntegerArray(null)',
    array('inputIntegerArray' => $integer_array_null_soapval));

//***********************************************************
// Base echoFloat

$soap_tests[INTEROP_R2BASE][] = new SOAP_Interop_Test(
    'echoFloat',
    array('inputFloat' => $float));
$soap_tests[INTEROP_R2BASE][] = new SOAP_Interop_Test(
    'echoFloat',
    array('inputFloat' => $float_soapval));

//***********************************************************
// Base echoFloatArray

$soap_tests[INTEROP_R2BASE][] = new SOAP_Interop_Test(
    'echoFloatArray',
    array('inputFloatArray' => $float_array));
$soap_tests[INTEROP_R2BASE][] = new SOAP_Interop_Test(
    'echoFloatArray',
    array('inputFloatArray' => $float_array_soapval));

//***********************************************************
// Base echoStruct

$soap_tests[INTEROP_R2BASE][] = new SOAP_Interop_Test(
    'echoStruct',
    array('inputStruct' => $soapstruct));
$soap_tests[INTEROP_R2BASE][] = new SOAP_Interop_Test(
    'echoStruct',
    array('inputStruct' => $soapstruct_soapval));

//***********************************************************
// Base echoStructArray

$soap_tests[INTEROP_R2BASE][] = new SOAP_Interop_Test(
    'echoStructArray',
    array('inputStructArray' => $soapstruct_array));
$soap_tests[INTEROP_R2BASE][] = new SOAP_Interop_Test(
    'echoStructArray',
    array('inputStructArray' => $soapstruct_array_soapval));

//***********************************************************
// Base echoVoid

$soap_tests[INTEROP_R2BASE][] = new SOAP_Interop_Test('echoVoid', null);
$test = new SOAP_Interop_Test('echoVoid', null);
$test->type = 'soapval';
$soap_tests[INTEROP_R2BASE][] = $test;
unset($test);

//***********************************************************
// Base echoBase64

$soap_tests[INTEROP_R2BASE][] = new SOAP_Interop_Test(
    'echoBase64',
    array('inputBase64' => $base64),
    'Nebraska');
$soap_tests[INTEROP_R2BASE][] = new SOAP_Interop_Test(
    'echoBase64',
    array('inputBase64' => $base64_soapval),
    'Nebraska');

//***********************************************************
// Base echoHexBinary

$soap_tests[INTEROP_R2BASE][] = new SOAP_Interop_Test(
    'echoHexBinary',
    array('inputHexBinary' => $hexBin));
$soap_tests[INTEROP_R2BASE][] = new SOAP_Interop_Test(
    'echoHexBinary',
    array('inputHexBinary' => $hexBin_soapval));

//***********************************************************
// Base echoDecimal

$soap_tests[INTEROP_R2BASE][] = new SOAP_Interop_Test(
    'echoDecimal',
    array('inputDecimal' => $decimal));
$soap_tests[INTEROP_R2BASE][] = new SOAP_Interop_Test(
    'echoDecimal',
    array('inputDecimal' => $decimal_soapval));

//***********************************************************
// Base echoDate

$soap_tests[INTEROP_R2BASE][] = new SOAP_Interop_Test(
    'echoDate',
    array('inputDate' => $dateTime));
$soap_tests[INTEROP_R2BASE][] = new SOAP_Interop_Test(
    'echoDate',
    array('inputDate' => $dateTime_soapval));

//***********************************************************
// Base echoBoolean

$soap_tests[INTEROP_R2BASE][] = new SOAP_Interop_Test(
    'echoBoolean(TRUE)',
    array('inputBoolean' => $boolean_true));
$soap_tests[INTEROP_R2BASE][] = new SOAP_Interop_Test(
    'echoBoolean(TRUE)',
    array('inputBoolean' => $boolean_true_soapval));
$soap_tests[INTEROP_R2BASE][] = new SOAP_Interop_Test(
    'echoBoolean(FALSE)',
    array('inputBoolean' => $boolean_false));
$soap_tests[INTEROP_R2BASE][] = new SOAP_Interop_Test(
    'echoBoolean(FALSE)',
    array('inputBoolean' => $boolean_false_soapval));
$soap_tests[INTEROP_R2BASE][] = new SOAP_Interop_Test(
    'echoBoolean(1)',
    array('inputBoolean' => $boolean_one));
$soap_tests[INTEROP_R2BASE][] = new SOAP_Interop_Test(
    'echoBoolean(1)',
    array('inputBoolean' => $boolean_one_soapval));
$soap_tests[INTEROP_R2BASE][] = new SOAP_Interop_Test(
    'echoBoolean(0)',
    array('inputBoolean' => $boolean_zero));
$soap_tests[INTEROP_R2BASE][] = new SOAP_Interop_Test(
    'echoBoolean(0)',
    array('inputBoolean' => $boolean_zero_soapval));
