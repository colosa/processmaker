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
// $Id: interop_client.php,v 1.16 2007/01/26 17:21:26 yunosh Exp $
//
require_once 'DB.php'; // PEAR/DB
require_once 'SOAP/Client.php';

require_once 'config.php';
require_once 'interop_test_functions.php';
require_once 'interop_test.php';
require_once 'params_Round2Base.php';
require_once 'params_Round2GroupB.php';
require_once 'params_Round2GroupC.php';
require_once 'params_Round3GroupD.php';
require_once 'registrationAndNotification.php';

error_reporting(E_ALL ^ E_NOTICE);
$INTEROP_LOCAL_SERVER = false;

class Interop_Client
{
    // database DNS
    var $DSN;

    // our central interop server, where we can get the list of endpoints
    var $registrationDB;
    
    // our local endpoint, will always get added to the database for all tests
    var $localEndpoint;
    
    // specify testing
    var $currentTest = '';      // see $tests above
    var $paramType = 'php';     // 'php' or 'soapval'
    var $useWSDL = false;       // true: do wsdl tests
    var $numServers = 0;        // 0: all
    var $specificEndpoint = ''; // test only this endpoint
    var $testMethod = '';       // test only this method
    var $skipEndpointList = array(); // endpoints to skip
    var $nosave = false;
    var $client_type = 'pear'; //  name of client
    
    // debug output
    var $show = 1;
    var $debug = 0;
    var $showFaults = 0; // used in result table output
    
    // PRIVATE VARIABLES
    var $dbc = null;
    var $totals = array();
    var $tests = array('Round 2 Base',
                       'Round 2 Group B', 
                       'Round 2 Group C', 
                       'Round 3 Group D Compound 1',
                       'Round 3 Group D Compound 2',
                       'Round 3 Group D DocLit',
                       'Round 3 Group D DocLitParams',
                       'Round 3 Group D Import 1',
                       'Round 3 Group D Import 2',
                       'Round 3 Group D Import 3',
                       'Round 3 Group D RpcEnc'
            );
    var $paramTypes = array('php', 'soapval');
    var $endpoints = array();
    
    function Interop_Client() {
        global $interopConfig;
        $this->DSN = $interopConfig['DSN'];
        $this->registrationDB =& new SOAP_Interop_registrationDB();
        
        // XXX for now, share the database for results also
        $this->dbc =& $this->registrationDB->dbc;
    }
    
    /**
    *  fetchEndpoints
    * retreive endpoints interop server
    *
    * @return boolean result
    * @access private
    */    
    function fetchEndpoints($name = 'Round 2 Base') {
        $service =& $this->registrationDB->findService($name);
        $this->endpoints =& $this->registrationDB->getServerList($service->id,true);
        return true;
    }
    
    /**
    *  getEndpoints
    * retreive endpoints from either database or interop server
    *
    * @param string name (see local var $tests)
    * @param boolean all (if false, only get valid endpoints, status=1)
    * @return boolean result
    * @access private
    */    
    function getEndpoints($name = 'Round 2 Base', $all = 0) {
        $service =& $this->registrationDB->findService($name);
        $this->endpoints =& $this->registrationDB->getServerList($service->id);
        return true;
    }

    /**
     * Retreives results from the database and stuffs them into the endpoint
     * array.
     *
     * @access private
     */
    function getResults($test = 'Round 2 Base', $type = 'php', $wsdl = 0)
    {
        // Be sure we have the right endpoints for this test result.
        $this->getEndpoints($test);
        $c = count($this->endpoints);

        // Retreive the results and put them into the endpoint info.
        $sql = "SELECT * FROM results WHERE class='$test' AND type='$type' AND wsdl=$wsdl";
        $results = $this->dbc->getAll($sql, null, DB_FETCHMODE_ASSOC);
        for ($j = 0, $rc = count($results); $j < $rc; ++$j) {
            $result = $results[$j];
            // Find the endpoint.
            for ($i = 0; $i < $c; $i++) {
                if ($this->endpoints[$i]->id == $result['endpoint']) {
                    // Store the info.
                    if (!isset($this->endpoints[$i]->methods)) {
                        $this->endpoints[$i]->methods = array();
                    }
                    $this->endpoints[$i]->methods[$result['function']] = $result;
                    break;
                }
            }
        }
    }
    
    /**
     * Saves the results of a method test into the database.
     *
     * @access private
     */
    function _saveResults($endpoint_id, &$soap_test)
    {
        if ($this->nosave) {
            return;
        }
        
        $result =& $soap_test->result;
        $wire = $result['wire'];
        if ($result['success']) {
            $success = 'OK';
            $error = '';
        } else {
            $success = $result['fault']->faultcode;
            $error = $result['fault']->faultstring;
            if (!$wire) {
                $wire = $result['fault']->faultdetail;
            }
            if (!$wire) {
                $wire = $result['fault']->faultstring;
            }
        }
        
        $test_name = $soap_test->test_name;
        // add header info to the test name
        if ($soap_test->headers) {
            foreach ($soap_test->headers as $h) {
                $destination = 0;
                if (is_object($h) && strtolower(get_class($h)) == 'soap_header') {
                    if ($h->attributes['SOAP-ENV:actor'] == 'http://schemas.xmlsoap.org/soap/actor/next') {
                        $destination = 1;
                    }
                    $test_name .= ":{$h->name},$destination,{$h->attributes['SOAP-ENV:mustUnderstand']}";
                } else {
                    if (!$h[3] ||
                        $h[3] == 'http://schemas.xmlsoap.org/soap/actor/next') {
                        $destination = 1;
                    }
                    if (!$h[2]) {
                        $h[2] = 0;
                    }
                    $qn = new QName($h[0]);
                    $test_name .= ":{$qn->name},$destination," . (int)$h[2];
                }
            }
        }
        
        $sql = 'DELETE FROM results WHERE endpoint = ? AND class = ? AND type = ? AND wsdl = ? AND client = ? AND function = ?';
        $values = array($endpoint_id, $this->currentTest, $this->paramType,
                        $this->useWSDL, $this->client_type, $test_name);
        $res = $this->dbc->query($sql, $values);
        if (DB::isError($res)) {
            die($res->getMessage());
        }
        if (is_object($res)) {
            $res->free();
        }
        
        $sql = 'INSERT INTO results (client, endpoint, stamp, class, type, wsdl, function, result, error, wire) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
        $values = array($this->client_type, $endpoint_id, time(),
                        $this->currentTest, $this->paramType, $this->useWSDL,
                        $test_name, $success, $error,
                        $wire ? $wire : '');
        //echo "\n".$sql;
        $res = $this->dbc->query($sql, $values);
        if (DB::isError($res)) {
            die($res->getMessage());
        }
        if (is_object($res)) {
            $res->free();
        }
    }

    /**
     * Compares two PHP types for a match.
     *
     * @param mixed $expect
     * @param mixed $test_result
     *
     * @return boolean
    */    
    function compareResult(&$expect, &$result, $type = null)
    {
        $expect_type = gettype($expect);
        $result_type = gettype($result);
        if ($expect_type == 'array' && $result_type == 'array') {
            // compare arrays
            return array_compare($expect, $result);
        }
        if ($type == 'float') {
            // We'll only compare to 3 digits of precision.
            return number_compare($expect, $result);
        }
        if ($type == 'boolean') {
            return boolean_compare($expect, $result);
        }
        return string_compare($expect, $result);
    }


    /**
     * Runs a method on an endpoint and stores its results to the database.
     *
     * @param array $endpoint_info
     * @param SOAP_Test $soap_test
     *
     * @return boolean result
     */    
    function doEndpointMethod(&$endpoint_info, &$soap_test)
    {
        $ok = false;
        
        // Prepare a holder for the test results.
        $soap_test->result['class'] = $this->currentTest;
        $soap_test->result['type'] = $this->paramType;
        $soap_test->result['wsdl'] = $this->useWSDL;
        $opdata = null;
        //global $soap_value_total;
        //echo "SOAP VALUES TEST-START: $soap_value_total\n";
        
        if ($this->useWSDL) {
            if ($endpoint_info->wsdlURL) {
                if (!$endpoint_info->client) {
                    if (0 /* dynamic client */) {
                    $endpoint_info->wsdl = new SOAP_WSDL($endpoint_info->wsdlURL);
                    $endpoint_info->wsdl->trace=1;
                    $endpoint_info->client = $endpoint_info->wsdl->getProxy('', $endpoint_info->name);
                    } else {
                    $endpoint_info->client = new SOAP_Client($endpoint_info->wsdlURL, 1);
                    }
                    $endpoint_info->client->_auto_translation = true;
                }
                if ($endpoint_info->client->_wsdl->_isfault()) {
                    $fault = $endpoint_info->client->_wsdl->fault->getFault();
                    $detail = $fault->faultstring . "\n\n" . $fault->faultdetail;
                    $soap_test->setResult(0,
                                          'WSDL',
                                          $detail,
                                          $fault->faultstring,
                                          $fault);
                    return false;
                }
                if ($soap_test->service) {
                    $endpoint_info->client->_wsdl->setService($soap_test->service);
                }
                $soap =& $endpoint_info->client;
                //$port = $soap->_wsdl->getPortName($soap_test->method_name);
                //$opdata = $soap->_wsdl->getOperationData($port, $soap_test->method_name);
            } else {
                $fault = array('faultcode' => 'WSDL',
                               'faultstring' => "no WSDL defined for $endpoint");
                $soap_test->setResult(0,
                                      'WSDL',
                                      $fault->faultstring,
                                      $fault->faultstring,
                                      $fault);
                return false;
            }
            $options = array('trace' => 1);
        } else {
            $namespace = $soapaction = 'http://soapinterop.org/';
            // Hack to make tests work with MS SoapToolkit.
            // It's the only one that uses this soapaction, and breaks if
            // it isn't right. Can't wait for soapaction to be fully deprecated
            // 8/25/2002, seems this is fixed now
            //if ($this->currentTest == 'Round 2 Base' &&
            //    strstr($endpoint_info->name,'MS SOAP ToolKit 2.0')) {
            //    $soapaction = 'urn:soapinterop';
            //}
            if (!$endpoint_info->client) {
                $endpoint_info->client = new SOAP_Client($endpoint_info->endpointURL);
                $endpoint_info->client->_auto_translation = true;
            }
            $soap = &$endpoint_info->client;
            $options = array('namespace' => $namespace, 
                             'soapaction' => $soapaction,
                             'trace' => 1);
        }
        
        // Add headers to the test.
        if ($soap_test->headers) {
            // $header is already a SOAP_Header class
            $soap->headersOut = array();
            $soap->headersIn = array();
            for ($i = 0, $hc = count($soap_test->headers); $i < $hc; $i++) {
                $soap->addHeader($soap_test->headers[$i]);
            }
        }
        $soap->setEncoding($soap_test->encoding);

        //if ($opdata) {
        //    if (isset($opdata['style'])) 
        //        $options['style'] = $opdata['style'];
        //    if (isset($opdata['soapAction'])) 
        //        $options['soapaction'] = $opdata['soapAction'];
        //    if (isset($opdata['input']) &&
        //        isset($opdata['input']['use']))
        //        $options['use'] = $opdata['input']['use'];
        //    if (isset($opdata['input']) &&
        //        isset($opdata['input']['namespace']))
        //        $options['namespace'] = $soap->_wsdl->namespaces[$opdata['input']['namespace']];
        //}
        //if ($this->useWSDL) {
        //    $wsdlcall = '$return = $soap->'.$soap_test->method_name.'(';
        //    $args = '';
        //    if ($soap_test->method_params) {
        //    $pnames = array_keys($soap_test->method_params);
        //    foreach ($pnames as $argname) {
        //        if ($args) $args .=',';
        //        $args .= '$soap_test->method_params[\''.$argname.'\']';
        //    }
        //    }
        //    $wsdlcall = $wsdlcall.$args.');';
        //    eval($wsdlcall);
        //} else {
            $return =& $soap->call($soap_test->method_name, $soap_test->method_params, $options);
        //}
        
        if (!PEAR::isError($return)) {
            if (is_array($soap_test->method_params) &&
                count($soap_test->method_params) == 1) {
                $sent = array_shift(array_values($soap_test->method_params));
            } else {
                $sent = $soap_test->method_params;
            }

            // Compare header results.
            $header_result = array();
            $headers_ok = true;
            if ($soap_test->headers) {
                // $header is already a SOAP_Header class
                for ($i = 0, $hc = count($soap_test->headers); $i < $hc; $i++) {
                    $header = $soap_test->headers[$i];
                    if (is_object($header) && strtolower(get_class($header)) != 'soap_header') {
                        // Assume it's an array.
                        $header = new SOAP_Header($header[0], null, $header[1], $header[2], $header[3], $header[4]);
                    }
                    $expect = $soap_test->headers_expect[$header->name];
                    $header_result[$header->name] = array();
                    // XXX need to fix need_result to identify the actor correctly
                    $need_result = $hresult ||
                        ($header->attributes['SOAP-ENV:actor'] == 'http://schemas.xmlsoap.org/soap/actor/next'
                         && $header->attributes['SOAP-ENV:mustUnderstand']);
                    if ($expect) {
                        $hresult = $soap->headersIn[key($expect)];
                        $ok = !$need_result || $this->compareResult($hresult ,$expect[key($expect)]);
                    } else {
                        $hresult = $soap->headersIn[$header->name];
                        $expect =& $soap->_decode($header);
                        $ok = !$need_result || $this->compareResult($hresult ,$expect);
                    }
                    $header_result[$header->name]['ok'] = $ok;
                    if (!$ok) {
                        $headers_ok = false;
                    }
                }
            }

            // We need to decode what we sent so we can compare!
            if (gettype($sent) == 'object' &&
                (strtolower(get_class($sent)) == 'soap_value' ||
                 is_subclass_of($sent, 'soap_value'))) {
                $sent_d =& $soap->_decode($sent);
            } else {
                $sent_d =& $sent;
            }
            
            // compare the results with what we sent
            $ok = $this->compareResult($sent_d, $return, $sent->type);
            $expected = $sent_d;
            unset($sent_d);
            unset($sent);
            if (!$ok && $soap_test->expect) {
                $ok = $this->compareResult($soap_test->expect, $return);
                $expected = $soap_test->expect;
            }
            
            if ($ok) {
                if (!$headers_ok) {
                    $fault = new stdClass();
                    $fault->faultcode = 'HEADER';
                    $fault->faultstring = 'The returned result did not match what we expected to receive';
                    $soap_test->setResult(0,
                                          $fault->faultcode,
                                          $soap->getWire(),
                                          $fault->faultstring,
                                          $fault);
                } else {
                    $soap_test->setResult(1, 'OK', $soap->getWire());
                    $success = true;
                }
            } else {
                $fault = new stdClass();
                $fault->faultcode = 'RESULT';
                $fault->faultstring = 'The returned result did not match what we expected to receive';
                $fault->faultdetail = "RETURNED:\n" . var_export($return, true) . "\n\nEXPECTED:\n" . var_export($expected, true);
                $soap_test->setResult(0,
                                      $fault->faultcode,
                                      $soap->getWire(),
                                      $fault->faultstring,
                                      $fault);
            }
        } else {
            $fault = $return->getFault();
            if ($soap_test->expect_fault) {
                $ok = 1;
                $res = 'OK';
            } else {
                $ok = 0;
                $res = $fault->faultcode;
            }
            $soap_test->setResult($ok,
                                  $res,
                                  $soap->getWire(),
                                  $fault->faultstring,
                                  $fault);
        }
        $soap->_reset();
        unset($return);

        return $ok;
    }

    /**
     * Runs a single round of tests.
     */    
    function doTest()
    {
        global $soap_tests;

        $empty_string = '';
        // Get endpoints for this test.
        if (!$this->currentTest) {
            die("Asked to run a test, but no testname!\n");
        }
        $this->getEndpoints($this->currentTest);
        // Clear totals.
        $this->totals = array();
        
        for ($i = 0, $c = count($this->endpoints); $i < $c; ++$i) {
            $endpoint_info = $this->endpoints[$i];
            // If we specify an endpoint, skip until we find it.
            if (($this->specificEndpoint &&
                 $endpoint_info->name != $this->specificEndpoint) ||
                ($this->useWSDL && !$endpoint_info->wsdlURL)) {
                continue;
            }
            
            $skipendpoint = false;
            $this->totals['servers']++;
            //$endpoint_info['tests'] = array();
            
            if ($this->show) {
                echo "Processing {$endpoint_info->name} at {$endpoint_info->endpointURL}\n";
            }
            
            for ($ti = 0, $tc = count($soap_tests[$this->currentTest]); $ti < $tc; ++$ti) {
                $soap_test = $soap_tests[$this->currentTest][$ti];
            
                // Only run the type of test we're looking for (php or
                // soapval).
                if ($soap_test->type != $this->paramType) {
                    continue;
                }
            
                // If this is in our skip list, skip it.
                if (in_array($endpoint_info->name, $this->skipEndpointList)) {
                    $skipendpoint = true;
                    $skipfault = new stdClass();
                    $skipfault->faultcode = 'SKIP';
                    $skipfault->faultstring = 'endpoint skipped';
                    $soap_test->setResult(0,
                                          $skipfault->faultcode,
                                          $empty_string,
                                          $skipfault->faultstring,
                                          $skipfault);
                    //$endpoint_info['tests'][] = &$soap_test;
                    //$soap_test->showTestResult($this->debug);
                    //$this->_saveResults($endpoint_info['id'], $soap_test->method_name);
                    $soap_test->result = null;
                    continue;
                }
                
                // If we're looking for a specific method, skip unless we have
                // it.
                if ($this->testMethod &&
                    strcmp($this->testMethod, $soap_test->test_name) != 0) {
                    continue;
                }
                if ($this->testMethod &&
                    $this->currentTest == 'Round 2 Group C') {
                    // We have to figure things out now.
                    if (!preg_match('/(.*):(.*),(\d),(\d)/', $this->testMethod, $m)) {
                        continue;
                    }
                    
                    // Is the header in the headers list?
                    $gotit = false;
                    $thc = count($soap_test->headers);
                    for ($thi = 0; $thi < $thc; $thi++) {
                        $header = $soap_test->headers[$thi];
                        if (is_object($header) && strtolower(get_class($header)) == 'soap_header') {
                            if ($header->name == $m[2]) {
                                $gotit = $header->attributes['SOAP-ENV:actor'] == ($m[3] ? SOAP_TEST_ACTOR_NEXT : SOAP_TEST_ACTOR_OTHER);
                                $gotit = $gotit && $header->attributes['SOAP-ENV:mustUnderstand'] == $m[4];
                            }
                        } elseif ($header[0] == $m[2]) {
                            $gotit = $gotit && $header[3] == ($m[3] ? SOAP_TEST_ACTOR_NEXT : SOAP_TEST_ACTOR_OTHER);
                            $gotit = $gotit && $header[4] == $m[4];
                        }
                    }
                    if (!$gotit) {
                        continue;
                    }
                }
            
                // If we are skipping the rest of the tests (due to error)
                // note a fault.
                if ($skipendpoint) {
                    $soap_test->setResult(0,
                                          $skipfault->faultcode,
                                          $empty_string,
                                          $skipfault->faultstring,
                                          $skipfault);
                    //$endpoint_info['tests'][] = &$soap_test;
                    $this->totals['fail']++;
                } else {
                    // Run the endpoint test.
                    unset($soap_test->result);
                    if ($this->doEndpointMethod($endpoint_info, $soap_test)) {
                        $this->totals['success']++;
                    } else {
                        $skipendpoint = $soap_test->result['fault']->faultcode == 'HTTP';
                        $skipfault = $skipendpoint ? $soap_test->result['fault'] : null;
                        $this->totals['fail']++;
                    }
                    //$endpoint_info['tests'][] = &$soap_test;
                }
                $soap_test->showTestResult($this->debug);
                $this->_saveResults($endpoint_info->id, $soap_test);
                $soap_test->reset();
                $this->totals['calls']++;
            }
            unset($endpoint_info->client);
            if ($this->numservers && ++$i >= $this->numservers) {
                break;
            }
        }
    }
    
    function doGroupTests() {
        $dowsdl = array(0,1);
        foreach($dowsdl as $usewsdl) {
            $this->useWSDL = $usewsdl;
            foreach($this->paramTypes as $ptype) {
                // skip a pointless test
                if ($usewsdl && $ptype == 'soapval') break;
                if (stristr($this->currentTest, 'Round 3') && !$usewsdl) break;
                $this->paramType = $ptype;
                $this->doTest();
            }
        }
    }
    
    /**
     * Go all out. This takes time.
     */    
    function doTests()
    {
        // The mother of all interop tests.
        $dowsdl = array(0, 1);
        foreach ($this->tests as $test) {
            $this->currentTest = $test;
            foreach ($dowsdl as $usewsdl) {
                $this->useWSDL = $usewsdl;
                foreach ($this->paramTypes as $ptype) {
                    // Skip a pointless test.
                    if ($usewsdl && $ptype == 'soapval') {
                        break;
                    }
                    if (stristr($this->currentTest, 'Round 3') && !$usewsdl) {
                        break;
                    }
                    $this->paramType = $ptype;
                    $this->doTest();
                }
            }
        }
    }
    
    /**
     * @access private
     */
    function getMethodList($test = 'base')
    {
        $this->dbc->setFetchMode(DB_FETCHMODE_ORDERED);
        // Retreive the results and put them into the endpoint info.
        $sql = "SELECT DISTINCT(function) FROM results WHERE client='$this->client_type' AND class='$test' ORDER BY function";
        $results = $this->dbc->getAll($sql);
        $ar = array();
        foreach($results as $result) {
            $ar[] = $result[0];
        }
        return $ar;
    }
    
    function outputTable()
    {
        $methods = $this->getMethodList($this->currentTest);
        if (!$methods) {
            return;
        }
        $this->getResults($this->currentTest,$this->paramType,$this->useWSDL);
        
        echo "<b>Testing $this->currentTest ";
        if ($this->useWSDL) {
            echo "using WSDL ";
        } else {
            echo "using Direct calls ";
        }
        echo "with $this->paramType values</b><br>\n";
        
        // Calculate totals for this table.
        $this->totals['success'] = 0;
        $this->totals['fail'] = 0;
        $this->totals['result'] = 0;
        $this->totals['wsdl'] = 0;
        $this->totals['connect'] = 0;
        $this->totals['servers'] = 0; //count($this->endpoints);
        for ($i = 0, $c = count($this->endpoints); $i < $c; ++$i) {
            $endpoint_info = $this->endpoints[$i];
            if (!$endpoint_info->name) {
                continue;
            }
            if (count($endpoint_info->methods) > 0) {
                $this->totals['servers']++;
                foreach ($methods as $method) {
                    $r = $endpoint_info->methods[$method]['result'];
                    if ($r == 'OK') {
                        $this->totals['success']++;
                    } elseif (stristr($r, 'result')) {
                        $this->totals['result']++;
                    } elseif (stristr($r, 'wsdlcache')) {
                        $this->totals['connect']++;
                    } elseif (stristr($r, 'wsdl')) {
                        $this->totals['wsdl']++;
                    } elseif (stristr($r, 'http')) {
                        $this->totals['connect']++;
                    } else {
                        $this->totals['fail']++;
                    }
                }
            } else {
                //unset($this->endpoints[$i]);
            }
        }
        $this->totals['calls'] = count($methods) * $this->totals['servers'];

        //if ($this->totals['fail'] == $this->totals['calls']) {
        //    // assume tests have not run, skip outputing table
        //    echo "No Data Available<br>\n";
        //    return;
        //}
        
        echo "\n\n<b>Servers: {$this->totals['servers']} Calls: {$this->totals['calls']} Success: {$this->totals['success']} <br>\n"
            . "System-Fail: {$this->totals['fail']} Result-Failure: {$this->totals['result']} Connect-Failure: {$this->totals['connect']} WSDL-Failure: {$this->totals['wsdl']} </b><br>\n"
       
            . "<table border=\"1\" cellspacing=\"0\" cellpadding=\"2\">\n"
            . "<tr><td class=\"BLANK\">Endpoint</td>\n";
        foreach ($methods as $method) {
            $info = explode(':', $method);
            echo "<td class='BLANK' valign='top'>";
            foreach ($info as $m) {
                $hi = explode(',', $m);
                echo '<b>'. $hi[0] . "</b><br>\n";
                if (count($hi) > 1) {
                    echo "&nbsp;&nbsp;Actor="
                        . ($hi[1] ? 'Target' : 'Not Target')
                        . "<br>\n&nbsp;&nbsp;MustUnderstand=$hi[2]<br>\n";
                }
            }
            echo "</td>\n";
        }
        echo "</tr>\n";
        $faults = array();
        $fi = 0;
        for ($i = 0, $c = count($this->endpoints); $i < $c; ++$i) {
            $endpoint_info = $this->endpoints[$i];
            if (!$endpoint_info->name) {
                continue;
            }
            if ($endpoint_info->wsdlURL) {
                echo "<tr><td class=\"BLANK\"><a href=\"{$endpoint_info->wsdlURL}\">{$endpoint_info->name}</a></td>\n";
            } else {
                echo "<tr><td class=\"BLANK\">{$endpoint_info->name}</td>\n";
            }
            foreach ($methods as $method) {
                $id = $endpoint_info->methods[$method]['id'];
                $r = $endpoint_info->methods[$method]['result'];
                $e = $endpoint_info->methods[$method]['error'];
                if ($e) {
                    $faults[$fi++] = $e;
                }
                if ($r) {
                    echo "<td class='$r'><a href='$PHP_SELF?wire=$id'>$r</a></td>\n";
                } else {
                    echo "<td class='untested'>untested</td>\n";
                }
            }
            echo "</tr>\n";
        }
        echo "</table><br>\n";
        if ($this->showFaults && count($faults) > 0) {
            echo "<b>ERROR Details:</b><br>\n<ul>\n";
            // output more error detail
            foreach ($faults as $fault) {
                echo '<li>' . htmlspecialchars($fault) . "</li>\n";
            }
        }
        echo "</ul><br><br>\n";
    }
    
    function outputTables()
    {
        $dowsdl = array(0, 1);
        foreach($this->tests as $test) {
            $this->currentTest = $test;
            foreach ($dowsdl as $usewsdl) {
                $this->useWSDL = $usewsdl;
                foreach ($this->paramTypes as $ptype) {
                    // Skip a pointless test.
                    if ($usewsdl && $ptype == 'soapval') {
                        break;
                    }
                    if (stristr($this->currentTest, 'Round 3') && !$usewsdl) {
                        break;
                    }
                    $this->paramType = $ptype;
                    $this->outputTable();
                }
            }
        }
    }
    
    function showWire($id)
    {
        $results = $this->dbc->getAll("SELECT * FROM results WHERE id=$id", null, DB_FETCHMODE_ASSOC );
        //$wire = preg_replace("/>/",">\n",$results[0]['wire']);
        $wire = $results[0]['wire'];
        echo "<pre>\n" . htmlspecialchars($wire) . "</pre>\n";
    }

}
