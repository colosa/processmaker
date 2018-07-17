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
// $Id: params_Round2GroupC.php,v 1.4 2003/04/07 00:51:17 shane Exp $
//
require_once 'params_values.php';
require_once 'interop_test.php';
define('INTEROP_R2GROUPC','Round 2 Group C');
//***********************************************************
// echoMeStringRequest php val tests

// echoMeStringRequest with endpoint as header destination, doesn't have to understand
$test =& new SOAP_Interop_Test('echoVoid', NULL);
$test->headers[] = array('{http://soapinterop.org/echoheader/}echoMeStringRequest', &$string, 0,SOAP_TEST_ACTOR_NEXT);
$test->headers_expect['echoMeStringRequest'] = array('echoMeStringResponse'=>&$string);
$soap_tests[INTEROP_R2GROUPC][] =& $test;
unset($test);

// echoMeStringRequest with endpoint as header destination, must understand
$test =& new SOAP_Interop_Test('echoVoid', NULL);
$test->headers[] = array('{http://soapinterop.org/echoheader/}echoMeStringRequest', &$string, 1,SOAP_TEST_ACTOR_NEXT);
$test->headers_expect['echoMeStringRequest'] = array('echoMeStringResponse'=>&$string);
$soap_tests[INTEROP_R2GROUPC][] =& $test;
unset($test);

// echoMeStringRequest with endpoint NOT header destination, doesn't have to understand
$test =& new SOAP_Interop_Test('echoVoid', NULL);
$test->headers[] = array('{http://soapinterop.org/echoheader/}echoMeStringRequest', &$string, 0, SOAP_TEST_ACTOR_OTHER);
$test->headers_expect['echoMeStringRequest'] = array();
$soap_tests[INTEROP_R2GROUPC][] =& $test;
unset($test);

// echoMeStringRequest with endpoint NOT header destination, must understand
$test =& new SOAP_Interop_Test('echoVoid', NULL);
$test->headers[] = array('{http://soapinterop.org/echoheader/}echoMeStringRequest', &$string, 1, SOAP_TEST_ACTOR_OTHER);
$test->headers_expect['echoMeStringRequest'] = array();
$soap_tests[INTEROP_R2GROUPC][] =& $test;
unset($test);

//***********************************************************
// echoMeStringRequest soapval tests

// echoMeStringRequest with endpoint as header destination, doesn't have to understand
$test =& new SOAP_Interop_Test('echoVoid', NULL);
$test->type = 'soapval';
$test->headers[] =& new SOAP_Header('{http://soapinterop.org/echoheader/}echoMeStringRequest', 'string', $string);
$test->headers_expect['echoMeStringRequest'] = array('echoMeStringResponse'=>&$string);
$soap_tests[INTEROP_R2GROUPC][] =& $test;
unset($test);

// echoMeStringRequest with endpoint as header destination, must understand
$test =& new SOAP_Interop_Test('echoVoid', NULL);
$test->type = 'soapval';
$test->headers[] =& new SOAP_Header('{http://soapinterop.org/echoheader/}echoMeStringRequest', 'string', $string, 1);
$test->type = 'soapval'; // force a soapval version of this test
$test->headers_expect['echoMeStringRequest'] = array('echoMeStringResponse'=>&$string);
$soap_tests[INTEROP_R2GROUPC][] =& $test;
unset($test);

// echoMeStringRequest with endpoint NOT header destination, doesn't have to understand
$test =& new SOAP_Interop_Test('echoVoid', NULL);
$test->type = 'soapval';
$test->headers[] =& new SOAP_Header('{http://soapinterop.org/echoheader/}echoMeStringRequest', 'string', $string, 0, SOAP_TEST_ACTOR_OTHER);
$test->headers_expect['echoMeStringRequest'] = array();
$soap_tests[INTEROP_R2GROUPC][] =& $test;
unset($test);

// echoMeStringRequest with endpoint NOT header destination, must understand
$test =& new SOAP_Interop_Test('echoVoid', NULL);
$test->type = 'soapval';
$test->headers[] =& new SOAP_Header('{http://soapinterop.org/echoheader/}echoMeStringRequest', 'string', $string, 1, SOAP_TEST_ACTOR_OTHER);
$test->headers_expect['echoMeStringRequest'] = array();
$soap_tests[INTEROP_R2GROUPC][] =& $test;
unset($test);

// echoMeStringRequest with endpoint header destination, must understand,
// invalid namespace, should recieve a fault
#$test =& new SOAP_Interop_Test('echoVoid', NULL);
#$test->type = 'soapval';
#$test->headers[] =& new SOAP_Header('{http://unknown.org/echoheader/}echoMeStringRequest', 'string', 'hello world',  1);
#$test->headers_expect['echoMeStringRequest'] = array();
#$test->expect_fault = TRUE;
#$soap_tests[INTEROP_R2GROUPC][] = $test;

//***********************************************************
// php val tests
// echoMeStructRequest with endpoint as header destination, doesn't have to understand
$test =& new SOAP_Interop_Test('echoVoid', NULL);
$test->headers[] = $soapstruct->__to_soap('{http://soapinterop.org/echoheader/}echoMeStructRequest',TRUE);
$test->headers_expect['echoMeStructRequest'] =
    array('echoMeStructResponse'=> &$soapstruct);
$soap_tests[INTEROP_R2GROUPC][] =& $test;
unset($test);

// echoMeStructRequest with endpoint as header destination, must understand
$test =& new SOAP_Interop_Test('echoVoid', NULL);
$test->headers[] = $soapstruct->__to_soap('{http://soapinterop.org/echoheader/}echoMeStructRequest',TRUE,1);
$test->headers_expect['echoMeStructRequest'] =
    array('echoMeStructResponse'=> &$soapstruct);
$soap_tests[INTEROP_R2GROUPC][] =& $test;
unset($test);

// echoMeStructRequest with endpoint NOT header destination, doesn't have to understand
$test =& new SOAP_Interop_Test('echoVoid', NULL);
$test->headers[] = $soapstruct->__to_soap('{http://soapinterop.org/echoheader/}echoMeStructRequest',TRUE,0, SOAP_TEST_ACTOR_OTHER);
$test->headers_expect['echoMeStructRequest'] = array();
$soap_tests[INTEROP_R2GROUPC][] =& $test;
unset($test);

// echoMeStructRequest with endpoint NOT header destination, must understand
$test =& new SOAP_Interop_Test('echoVoid', NULL);
$test->headers[] = $soapstruct->__to_soap('{http://soapinterop.org/echoheader/}echoMeStructRequest',TRUE,1, SOAP_TEST_ACTOR_OTHER);
$test->headers_expect['echoMeStructRequest'] = array();
$soap_tests[INTEROP_R2GROUPC][] =& $test;
unset($test);

//***********************************************************
// soapval tests
// echoMeStructRequest with endpoint as header destination, doesn't have to understand
$test =& new SOAP_Interop_Test('echoVoid', NULL);
$test->type = 'soapval';
$test->headers[] = $soapstruct->__to_soap('{http://soapinterop.org/echoheader/}echoMeStructRequest',TRUE);
$test->headers_expect['echoMeStructRequest'] =
    array('echoMeStructResponse'=> &$soapstruct);
$soap_tests[INTEROP_R2GROUPC][] =& $test;
unset($test);

// echoMeStructRequest with endpoint as header destination, must understand
$test =& new SOAP_Interop_Test('echoVoid', NULL);
$test->type = 'soapval';
$test->headers[] = $soapstruct->__to_soap('{http://soapinterop.org/echoheader/}echoMeStructRequest',TRUE,1);
$test->headers_expect['echoMeStructRequest'] =
    array('echoMeStructResponse'=> &$soapstruct);
$soap_tests[INTEROP_R2GROUPC][] =& $test;
unset($test);

// echoMeStructRequest with endpoint NOT header destination, doesn't have to understand
$test =& new SOAP_Interop_Test('echoVoid', NULL);
$test->type = 'soapval';
$test->headers[] = $soapstruct->__to_soap('{http://soapinterop.org/echoheader/}echoMeStructRequest',TRUE,0, SOAP_TEST_ACTOR_OTHER);
$test->headers_expect['echoMeStructRequest'] = array();
$soap_tests[INTEROP_R2GROUPC][] =& $test;
unset($test);

// echoMeStructRequest with endpoint NOT header destination, must understand
$test =& new SOAP_Interop_Test('echoVoid', NULL);
$test->type = 'soapval';
$test->headers[] = $soapstruct->__to_soap('{http://soapinterop.org/echoheader/}echoMeStructRequest',TRUE,1, SOAP_TEST_ACTOR_OTHER);
$test->headers_expect['echoMeStructRequest'] = array();
$soap_tests[INTEROP_R2GROUPC][] =& $test;
unset($test);

//***********************************************************
// echoMeUnknown php val tests
// echoMeUnknown with endpoint as header destination, doesn't have to understand
$test =& new SOAP_Interop_Test('echoVoid', NULL);
$test->headers[] = array('{http://soapinterop.org/echoheader/}echoMeUnknown', $string,0,SOAP_TEST_ACTOR_NEXT);
$test->headers_expect['echoMeUnknown'] = array();
$soap_tests[INTEROP_R2GROUPC][] =& $test;
unset($test);

// echoMeUnknown with endpoint as header destination, must understand
$test =& new SOAP_Interop_Test('echoVoid', NULL);
$test->headers[] = array('{http://soapinterop.org/echoheader/}echoMeUnknown', $string,1,SOAP_TEST_ACTOR_NEXT);
$test->headers_expect['echoMeUnknown'] = array();
$test->expect_fault = TRUE;
$soap_tests[INTEROP_R2GROUPC][] =& $test;
unset($test);

// echoMeUnknown with endpoint NOT header destination, doesn't have to understand
$test =& new SOAP_Interop_Test('echoVoid', NULL);
$test->headers[] = array('{http://soapinterop.org/echoheader/}echoMeUnknown', $string,0, SOAP_TEST_ACTOR_OTHER);
$test->headers_expect['echoMeUnknown'] = array();
$soap_tests[INTEROP_R2GROUPC][] =& $test;
unset($test);

// echoMeUnknown with endpoint NOT header destination, must understand
$test =& new SOAP_Interop_Test('echoVoid', NULL);
$test->headers[] = array('{http://soapinterop.org/echoheader/}echoMeUnknown', $string, 1, SOAP_TEST_ACTOR_OTHER);
$test->headers_expect['echoMeUnknown'] = array();
$soap_tests[INTEROP_R2GROUPC][] =& $test;
unset($test);

//***********************************************************
// echoMeUnknown soapval tests
// echoMeUnknown with endpoint as header destination, doesn't have to understand
$test =& new SOAP_Interop_Test('echoVoid', NULL);
$test->type = 'soapval';
$test->headers[] =& new SOAP_Header('{http://soapinterop.org/echoheader/}echoMeUnknown','string',$string);
$test->headers_expect['echoMeUnknown'] = array();
$soap_tests[INTEROP_R2GROUPC][] =& $test;
unset($test);

// echoMeUnknown with endpoint as header destination, must understand
$test =& new SOAP_Interop_Test('echoVoid', NULL);
$test->type = 'soapval';
$test->headers[] =& new SOAP_Header('{http://soapinterop.org/echoheader/}echoMeUnknown','string',$string,1);
$test->headers_expect['echoMeUnknown'] = array();
$test->expect_fault = TRUE;
$soap_tests[INTEROP_R2GROUPC][] =& $test;
unset($test);

// echoMeUnknown with endpoint NOT header destination, doesn't have to understand
$test =& new SOAP_Interop_Test('echoVoid', NULL);
$test->type = 'soapval';
$test->headers[] =& new SOAP_Header('{http://soapinterop.org/echoheader/}echoMeUnknown','string',$string, 0, SOAP_TEST_ACTOR_OTHER);
$test->headers_expect['echoMeUnknown'] = array();
$soap_tests[INTEROP_R2GROUPC][] =& $test;
unset($test);

// echoMeUnknown with endpoint NOT header destination, must understand
$test =& new SOAP_Interop_Test('echoVoid', NULL);
$test->type = 'soapval';
$test->headers[] =& new SOAP_Header('{http://soapinterop.org/echoheader/}echoMeUnknown','string',$string, 1, SOAP_TEST_ACTOR_OTHER);
$test->headers_expect['echoMeUnknown'] = array();
$soap_tests[INTEROP_R2GROUPC][] =& $test;
unset($test);


?>