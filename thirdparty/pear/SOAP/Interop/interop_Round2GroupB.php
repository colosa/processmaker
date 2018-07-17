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
// $Id: interop_Round2GroupB.php,v 1.9 2007/01/22 14:53:21 yunosh Exp $
//
require_once 'params_classes.php';

class SOAP_Interop_GroupB {

    var $__dispatch_map = array();
    
    function SOAP_Interop_GroupB()
    {
        $this->__dispatch_map['echoStructAsSimpleTypes'] =
            array('in' => array('inputStruct' => 'SOAPStruct'),
                  'out' => array('outputString' => 'string', 'outputInteger' => 'int', 'outputFloat' => 'float'));
        $this->__dispatch_map['echoSimpleTypesAsStruct'] =
            array('in' => array('inputString' => 'string', 'inputInteger' => 'int', 'inputFloat' => 'float'),
                  'out' => array('return' => 'SOAPStruct'));
        $this->__dispatch_map['echoNestedStruct'] =
            array('in' => array('inputStruct' => 'SOAPStructStruct'),
                  'out' => array('return' => 'SOAPStructStruct'));
        $this->__dispatch_map['echo2DStringArray'] =
            array('in' => array('input2DStringArray' => 'ArrayOfString2D'),
                  'out' => array('return' => 'ArrayOfString2D'));
        $this->__dispatch_map['echoNestedArray'] =
            array('in' => array('inputString' => 'SOAPArrayStruct'),
                  'out' => array('return' => 'SOAPArrayStruct'));
    }
    
    /* this private function is called on by SOAP_Server to determine any
     * special dispatch information that might be necessary. This, for
     * example, can be used to set up a dispatch map for functions that return
     * multiple OUT parameters. */
    function __dispatch($methodname)
    {
        if (array_key_exists($methodname,$this->__dispatch_map)) {
            return $this->__dispatch_map[$methodname];
        }
        return null;
    }
    
    function echoStructAsSimpleTypes ($struct)
    {
        // Convert a SOAPStruct to an array.
        return array(
            new SOAP_Value('outputString','string',$struct->varString),
            new SOAP_Value('outputInteger','int',$struct->varInt),
            new SOAP_Value('outputFloat','float',$struct->varFloat));
    }

    function echoSimpleTypesAsStruct($string, $int, $float)
    {
        // Convert a input into struct.
        $v = new SOAPStruct($string, $int, $float);
        return new SOAP_Value('return', '{http://soapinterop.org/xsd}SOAPStruct', $v);
    }

    function echoNestedStruct($struct)
    {
        $separator = "\n";
        $methods = get_class_methods($struct);
        $arr_str = $separator . strtolower(implode($separator, $methods));
        $string = $separator . '__to_soap' . $separator;
        if (strpos($arr_str, $string) !== false) {
            return $struct->__to_soap();
        }
        return $struct;
    }

    function echo2DStringArray($array)
    {
        $ret = new SOAP_Value('return', 'Array', $array);
        $ret->options['flatten'] = true;
        return $ret;
    }

    function echoNestedArray($array)
    {
        return $array;
    }

}
