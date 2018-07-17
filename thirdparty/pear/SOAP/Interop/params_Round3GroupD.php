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
// $Id: params_Round3GroupD.php,v 1.7 2003/04/14 01:40:21 shane Exp $
//
require_once 'params_values.php';
require_once 'interop_test.php';
define('INTEROP_R3D_COMPOUND1','Round 3 Group D Compound 1');
define('INTEROP_R3D_COMPOUND2','Round 3 Group D Compound 2');
define('INTEROP_R3D_DOCLIT','Round 3 Group D DocLit');
define('INTEROP_R3D_DOCLIT_PARAM','Round 3 Group D DocLitParams');
define('INTEROP_R3D_IMPORT1','Round 3 Group D Import 1');
define('INTEROP_R3D_IMPORT2','Round 3 Group D Import 2');
define('INTEROP_R3D_IMPORT3','Round 3 Group D Import 3');
define('INTEROP_R3D_RPCENC','Round 3 Group D RpcEnc');
//***********************************************************
// GroupD 

# COMPOUND 1 tests
# http://www.whitemesa.net/wsdl/r3/compound1.wsdl
$test =& new SOAP_Interop_Test('echoPerson', array('x_Person'=>&$person));
$test->type = 'php'; // force to php testing
$soap_tests[INTEROP_R3D_COMPOUND1][] = &$test;
unset($test);

$test =& new SOAP_Interop_Test('echoDocument', array('x_Document'=>'Test Document Here'));
$test->type = 'php'; // force to php testing
$soap_tests[INTEROP_R3D_COMPOUND1][] = &$test;
unset($test);

# COMPOUND 2 tests
# http://www.whitemesa.net/wsdl/r3/compound2.wsdl
$test =& new SOAP_Interop_Test('echoEmployee', array('x_Employee'=>&$employee));
$test->type = 'php'; // force to php testing
$soap_tests[INTEROP_R3D_COMPOUND2][] = &$test;
unset($test);

# DOC LIT Tests
# http://www.whitemesa.net/wsdl/r3/interoptestdoclit.wsdl
$soap_tests[INTEROP_R3D_DOCLIT][] =& 
    new SOAP_Interop_Test('echoString', 
        array('echoStringParam' => &$string));
$soap_tests[INTEROP_R3D_DOCLIT][] =& 
    new SOAP_Interop_Test('echoStringArray', 
        array('echoStringArrayParam' => &$string_array));
$soap_tests[INTEROP_R3D_DOCLIT][] =& 
    new SOAP_Interop_Test('echoStruct',
        array('echoStructParam' => &$soapstruct));
#$soap_tests[INTEROP_R3D_DOCLIT][] = 
#    new SOAP_Interop_Test('echoVoid', NULL);

# DOC LIT w/Params Tests
# http://www.whitemesa.net/wsdl/r3/interoptestdoclitparameters.wsdl
$soap_tests[INTEROP_R3D_DOCLIT_PARAM][] =& 
    new SOAP_Interop_Test('echoString', 
        array('param0' => $string));
$soap_tests[INTEROP_R3D_DOCLIT_PARAM][] =& 
    new SOAP_Interop_Test('echoStringArray', 
        array('param0' => &$string_array));
$soap_tests[INTEROP_R3D_DOCLIT_PARAM][] =& 
    new SOAP_Interop_Test('echoStruct',
        array('param0' => &$soapstruct));
$soap_tests[INTEROP_R3D_DOCLIT_PARAM][] =& 
    new SOAP_Interop_Test('echoVoid', NULL);

# IMPORT 1 tests
# http://www.whitemesa.net/wsdl/r3/import1.wsdl
$soap_tests[INTEROP_R3D_IMPORT1][] =& 
    new SOAP_Interop_Test('echoString', 
        array('x' => &$string));

# IMPORT 2 tests
# http://www.whitemesa.net/wsdl/r3/import2.wsdl
$soap_tests[INTEROP_R3D_IMPORT2][] =& 
    new SOAP_Interop_Test('echoStruct',
        array('inputStruct' => &$soapstruct));

# IMPORT 2 tests
# http://www.whitemesa.net/wsdl/r3/import3.wsdl
$test =& new SOAP_Interop_Test('echoStruct',
        array('inputStruct' => &$soapstruct));
$test->service = 'Import3';
$soap_tests[INTEROP_R3D_IMPORT3][] = &$test;
unset($test);

$test =& new SOAP_Interop_Test('echoStructArray', 
        array('inputArray' =>&$soapstruct_array));
$test->service = 'Import3';
$soap_tests[INTEROP_R3D_IMPORT3][] = &$test;
unset($test);

# RPC ENCODED Tests
# http://www.whitemesa.net/wsdl/r3/interoptestdoclitparameters.wsdl
$soap_tests[INTEROP_R3D_RPCENC][] =& 
    new SOAP_Interop_Test('echoString', 
        array('param0' => &$string));
$soap_tests[INTEROP_R3D_RPCENC][] =& 
    new SOAP_Interop_Test('echoStringArray', 
        array('param0' => &$string_array));
$soap_tests[INTEROP_R3D_RPCENC][] =& 
    new SOAP_Interop_Test('echoStruct',
        array('param0' => &$soapstruct));
$soap_tests[INTEROP_R3D_RPCENC][] =& 
    new SOAP_Interop_Test('echoVoid', NULL);

?>