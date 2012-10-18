<?php

/**
 * class.ymlTestCases.php
 *
 * @package gulliver.system
 *
 * ProcessMaker Open Source Edition
 * Copyright (C) 2004 - 2011 Colosa Inc.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 * For more information, contact Colosa Inc, 2566 Le Jeune Rd.,
 * Coral Gables, FL, 33134, USA, or email info@colosa.com.
 *
 */
/* Dependencies: class.ymlDomain.php; class.testTools.php
 * +----------------------------+
 * | STRUCTURE for ymlTestFile. |
 * +----------------------------+
 * TestCasesGroup1:
 *   -                                 Un caso de prueba.
 *     Input:
 *       FIELD_A: "Value_Field_A"
 *       FIELD_B[]: "Domain_Field_A"
 *     Output:
 *       Type: "TypeOfResult"         Type of data that should be the result.
 *       Value: "a:0{}"                This node must contain a value that will be serialized compared with the test result.
 *   -                                 Another test case.
 *     ...
 * TestCasesGroup2:
 *   ...
 */

/**
 *
 * @package gulliver.system
 */
class ymlTestCases
{
    public $testCaseFile;
    public $testCases = array ();

    /**
     * function TestCases
     *
     * @access public
     * @param string $testCaseFile
     * @param string $testDomain
     * @param string $testLime
     * @return void
     */
    public function ymlTestCases ($testCaseFile, &$testDomain, &$testLime)
    {
        $this->testDomain = & $testDomain;
        $this->testLime = & $testLime;
        if (basename( $testCaseFile ) === $testCaseFile) {
            $testCaseFile = PATH_FIXTURES . $testCaseFile;
        }
        $this->testCaseFile = $testCaseFile;
    }

    /**
     * function load
     *
     * @access public
     * @param string $inputTestCasesIndex
     * @param array $fields
     * @return array
     */
    public function load ($inputTestCasesIndex = 'TestCases', $fields = array())
    {
        $testCases = array ();
        $input = sfYAML::Load( /*PATH_FIXTURES .*/ $this->testCaseFile );
        foreach ($input[$inputTestCasesIndex] as $preTestCase) {
            $testFunctionInputs = array ();
            foreach ($preTestCase['Input'] as $inputArgument => $value) {
                if (substr( $inputArgument, - 2, 2 ) === '[]') {
                    //DOMAIN
                    $inputArgument = substr( $inputArgument, 0, strlen( $inputArgument ) - 2 );
                    if (! isset( $testFunctionInputs[$inputArgument] )) {
                        $testFunctionInputs[$inputArgument] = array ();
                    }
                    //var_dump($this->testDomain->global,$this->testDomain->get( $value ), $value );
                    ymlDomain::arrayAppend( $testFunctionInputs[$inputArgument], $this->testDomain->get( $value ) );
                } else {
                    //SPECIFIC VALUE
                    if (! isset( $testFunctionInputs[$inputArgument] )) {
                        $testFunctionInputs[$inputArgument] = array ();
                    }
                    ymlDomain::arrayAppend( $testFunctionInputs[$inputArgument], array ($value) );
                }
            }
            /* Start Block: "Explode" all the posible test cases defined in the yml
            * using domains and single values
            */
            //Initialize $index key values for the first test case (5.2 array_fill_keys(array_keys($testFunctionInputs),0))
            $index = array_combine( array_keys( $testFunctionInputs ), array_fill( 0, count( $testFunctionInputs ), 0 ) );
            //array_product()
            $prod = 1;
            foreach ($testFunctionInputs as $values) {
                $prod *= count( $values );
            }
            $lastCase = ($prod == 0);
            while (! $lastCase) {
                //foreach($index as $v) echo($v);echo("\n");
                /* Put in $aux one test case */
                $aux = array ();
                foreach ($testFunctionInputs as $key => $values) {
                    $aux[$key] = $values[$index[$key]];
                }
                /* CREATE TEST CASE: Put $aux test case in $testCases array */
                $i = count( $testCases );
                $testCases[$i] = $preTestCase;
                $testCases[$i]['Input'] = $aux;
                /* Increse the $index key values to the next test case */
                $lastCase = true;
                foreach ($testFunctionInputs as $key => $values) {
                    $index[$key] ++;
                    if ($index[$key] >= count( $values )) {
                        $index[$key] = 0;
                    } else {
                        $lastCase = false;
                        break;
                    }
                }
            }
            /*End Block */
        }
        /* Start Block: Replace @@ tags variables */
        foreach ($testCases as $key => $testCase) {
            $testCases[$key] = testTools::replaceVariables( $testCases[$key] );
            $testCases[$key]['Input'] = testTools::replaceVariables( $testCases[$key]['Input'], $fields );
            if (isset( $testCase['Output'] )) {
                if (isset( $testCase['Output']['Value'] )) {
                    /*$testCases[$key]['Output']['Value'] =
                    unserialize($testCases[$key]['Output']['Value']);*/
                }
            }
        }
        /* End Block */
        $this->testCases = $testCases;
        return $testCases;
    }

    /**
     * function load
     * Increase the number of "planned" tests.
     *
     * @access public
     * @param int $count
     * @param int $start
     * @return void
     */
    public function addToPlan ($count = -1, $start = 0)
    {
        foreach ($this->testCases as $testCase) {
            if (($start == 0) && ($count != 0)) {
                if (isset( $testCase['TODO'] )) {
                    $this->testLime->plan ++;
                } else {
                    if (isset( $testCase['Output'] )) {
                        if (isset( $testCase['Output']['Type'] ) || isset( $testCase['Output']['Value'] )) {
                            $this->testLime->plan ++;
                        }
                    }
                }
            } else {
                $start --;
                if ($count > 0) {
                    $count --;
                }
            }
        }
    }

    /**
     * function run
     *
     * @access public
     * @param object $testerObject
     * @param array $fields
     * @param int $count
     * @param int $start
     * @return array
     */
    public function run (&$testerObject, $fields = array(), $count = -1, $start = 0)
    {
        $results = array ();
        //$this->addToPlan( $count, $start );
        $functions = get_class_methods( get_class( $testerObject ) );
        foreach ($functions as $id => $fn) {
            $functions[$id] = strtolower( $fn );
        }
        foreach ($this->testCases as $index => $testCase) {
            if (($start == 0) && ($count != 0)) {
                if (isset( $testCase['TODO'] )) {
                    $this->testLime->todo( $testCase['TODO'] );
                } else {
                    if (isset( $testCase['Function'] )) {
                        if (array_search( strtolower( $testCase['Function'] ), $functions ) !== false) {
                            $testCase['Input'] = G::array_merges( $testCase['Input'], $fields );
                            $result = eval( 'return $testerObject->' . $testCase['Function'] . '($testCase, $testCase["Input"]);' );
                            $results[] = $result;
                            /* Start Block: Test the $result */
                            if (isset( $testCase['Output'] )) {
                                if (isset( $testCase['Output']['Value'] )) {
                                    //$this->testLime->is( $result, $testCase['Output']['Value'], $testCase['Title'] );
                                    $this->testLime->todo( ($testCase['Output']['Type']) );
                                    $this->testLime->diag( "/processmaker/trunk/gulliver/system/class.ymlTestCases.php at line 204" );
                                } elseif (isset( $testCase['Output']['Type'] )) {
                                    // $this->testLime->isa_ok( $result, $testCase['Output']['Type'], $testCase['Title'] );
                                    $this->testLime->todo( ($testCase['Output']['Type']) );
                                    $this->testLime->diag( "/processmaker/trunk/gulliver/system/class.ymlTestCases.php at line 204" );
                                }
                            }
                            /* End Block */
                        } else {
                            $this->testLime->fail( 'Case #' . $index . ': Test function (Function) is not present in tester object.' );
                        }
                    } else {
                        $this->testLime->fail( 'Case #' . $index . ' doesn\'t have a test function (Function) defined.' );
                    }
                }
            } else {
                $start --;
                if ($count > 0) {
                    $count --;
                }
            }
        }
        return $results;
    }

    /**
     * function runSingle
     *
     * @access public
     * @param object $testerObject
     * @param array $fields
     * @return array
     */
    public function runSingle (&$testerObject, $fields = array())
    {
        $results = $this->run( $testerObject, $fields, 1, 0 );
        return $results[0];
    }

    /**
     * function runMultiple
     *
     * @access public
     * @param object $testerObject
     * @param array $fields
     * @param int $count
     * @param int $start
     * @return array
     */
    public function runMultiple (&$testerObject, $fields = array(), $count = -1, $start = 0)
    {
        return $this->run( $testerObject, $fields, $count, $start );
    }
}

