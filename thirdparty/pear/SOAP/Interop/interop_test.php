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
// $Id: interop_test.php,v 1.10 2007/01/26 17:21:44 yunosh Exp $
//
require_once 'SOAP/Value.php';

define('SOAP_TEST_ACTOR_NEXT','http://schemas.xmlsoap.org/soap/actor/next');
define('SOAP_TEST_ACTOR_OTHER','http://some/other/actor');

class SOAP_Interop_Test {

    var $type = 'php';
    var $test_name = null;
    var $method_name = null;
    var $method_params = null;
    var $expect = null;
    var $expect_fault = false;
    var $headers = null;
    var $headers_expect = null;
    var $result = array();
    var $show = 1;
    var $debug = 0;
    var $encoding = SOAP_DEFAULT_ENCODING;

    /**
     * If multiple services, this sets to a specific service.
     */
    var $service = null;
    
    function SOAP_Interop_Test($methodname, $params, $expect = null)
    {
        if (strchr($methodname, '(')) {
            preg_match('/(.*)\((.*)\)/', $methodname, $matches);
            $this->test_name = $methodname;
            $this->method_name = $matches[1];
        } else {
            $this->test_name = $this->method_name = $methodname;
        }
        $this->method_params = $params;
        $this->expect = $expect;

        // determine test type
        if ($params) {
            $v = array_values($params);
            if (gettype($v[0]) == 'object' &&
                strtolower(get_class($v[0])) == 'soap_value') {
                $this->type = 'soapval';
            }
        }
    }
    
    function setResult($ok, $result, $wire, $error = '', $fault = null)
    {
        $this->result['success'] = $ok;
        $this->result['result'] = $result;
        $this->result['error'] = $error;
        $this->result['wire'] = $wire;
        $this->result['fault'] = $fault;
    }

    function reset()
    {
        $this->result = array();
    }
    
    /**
     * Prints simple output about a methods result.
     */    
    function showTestResult($debug = 0)
    {
        // Debug output
        if ($debug) {
            $this->show = 1;
            echo str_repeat('-', 50) . "\n";
        }
        
        echo "Testing $this->test_name: ";
        if ($this->headers) {
            $hc = count($this->headers);
            for ($i = 0; $i < $hc; $i++) {
                $h = $this->headers[$i];
                if (strtolower(get_class($h)) == 'soap_header') {
                    echo "\n    {$h->name},{$h->attributes['SOAP-ENV:actor']},{$h->attributes['SOAP-ENV:mustUnderstand']} : ";
                } else {
                    if (!$h[4]) {
                        $h[4] = SOAP_TEST_ACTOR_NEXT;
                    }
                    if (!$h[3]) {
                        $h[3] = 0;
                    }
                    echo "\n    $h[0],$h[4],$h[3] : ";
                }
            }
        }
        
        if ($debug) {
            echo "method params: ";
            print_r($this->params);
            echo "\n";
        }
        
        $ok = $this->result['success'];
        if ($ok) {
            echo "SUCCESS\n";
        } else {
            $fault = $this->result['fault'];
            if ($fault) {
                echo "FAILED: [{$fault->faultcode}] {$fault->faultstring}\n";
                if (!empty($fault->faultdetail)) {
                    echo $fault->faultdetail . "\n";
                }
            } else {
                echo "FAILED: " . $this->result['result'] . "\n";
            }
        }
        if ($debug) {
            echo "\n" . $this->result['wire'] . "\n";
        }
    }

}
