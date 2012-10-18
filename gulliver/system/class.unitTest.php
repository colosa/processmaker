<?php

/**
 * class.unitTest.php
 *
 * @package gulliver.system
 *
 * ProcessMaker Open Source Edition
 * Copyright (C) 2004 - 2011 Colosa Inc.
 *
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

/**
 *
 * @package gulliver.system
 */

class unitTest
{
    var $dbc;
    var $times;
    var $yml;
    var $domain;
    var $testLime;

    function unitTest ($ymlFile, &$testLime, &$testDomain)
    {
        if (! isset( $testDomain )) {
            $testDomain = new ymlDomain();
        }
        $this->domain = & $testDomain;
        $this->testLime = & $testLime;
        $this->yml = new ymlTestCases( $ymlFile, $this->domain, $this->testLime );
    }
    //Load a Test (group of unitary tests) defined in the Yml file.
    function load ($testName, $fields = array())
    {
        $this->yml->load( $testName, $fields );
    }
    //Run one single unit test from the loaded Test
    function runSingle ($fields = array())
    {
        return $this->yml->runSingle( $this, $fields );
    }
    //Run a group of unit tests from the loaded Test
    function runMultiple ($fields = array(), $count = -1, $start = 0)
    {
        return $this->yml->runMultiple( $this, $fields, $count, $start );
    }
    //Run all the unit tests from the loaded Test
    function runAll ($fields = array())
    {
        return $this->yml->runMultiple( $this, $fields, - 1, 0 );
    }
    //A sample of "Function" to run a unit test.
    function sampleTestFunction ($testCase, &$Fields)
    {
        $result = ($Fields['APP_UID'] != '') ? "OK" : "FALSE";
        return $result;
    }
}

