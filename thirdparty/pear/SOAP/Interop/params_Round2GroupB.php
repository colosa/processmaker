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
// $Id: params_Round2GroupB.php,v 1.4 2003/04/07 00:51:17 shane Exp $
//
require_once 'params_values.php';
require_once 'interop_test.php';
define('INTEROP_R2GROUPB','Round 2 Group B');
//***********************************************************
// GroupB echoStructAsSimpleTypes

$expect = array(
        'outputString'=>'arg',
        'outputInteger'=>34,
        'outputFloat'=>325.325
    );
$soap_tests[INTEROP_R2GROUPB][] = 
    new SOAP_Interop_Test('echoStructAsSimpleTypes',
        array('inputStruct' => &$soapstruct), $expect);
$soap_tests[INTEROP_R2GROUPB][] = 
    new SOAP_Interop_Test('echoStructAsSimpleTypes',
        array('inputStruct' => &$soapstruct_soapval), $expect);

//***********************************************************
// GroupB echoSimpleTypesAsStruct

$soap_tests[INTEROP_R2GROUPB][] =& new SOAP_Interop_Test('echoSimpleTypesAsStruct',
    $simpletypes, $soapstruct);
$soap_tests[INTEROP_R2GROUPB][] =& new SOAP_Interop_Test('echoSimpleTypesAsStruct',
    $simpletypes_soapval, $soapstruct);    

//***********************************************************
// GroupB echo2DStringArray

$soap_tests[INTEROP_R2GROUPB][] =& new SOAP_Interop_Test('echo2DStringArray',
    array('input2DStringArray' => &$multidimarray));
$soap_tests[INTEROP_R2GROUPB][] =& new SOAP_Interop_Test('echo2DStringArray',
    array('input2DStringArray' => &$multidimarray_soapval));

//***********************************************************
// GroupB echoNestedStruct

$soap_tests[INTEROP_R2GROUPB][] =& new SOAP_Interop_Test('echoNestedStruct',
    array('inputStruct' => &$soapstructstruct));
$soap_tests[INTEROP_R2GROUPB][] =& new SOAP_Interop_Test('echoNestedStruct',
    array('inputStruct' => &$soapstructstruct_soapval));

//***********************************************************
// GroupB echoNestedArray

$soap_tests[INTEROP_R2GROUPB][] =& new SOAP_Interop_Test('echoNestedArray',
    array('inputStruct' => &$soaparraystruct));
$soap_tests[INTEROP_R2GROUPB][] =& new SOAP_Interop_Test('echoNestedArray',
    array('inputStruct' => &$soaparraystruct_soapval));
        

?>