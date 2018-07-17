<?php
/**
 * class.pmFunctions.php
 *
 * ProcessMaker Open Source Edition
 * Copyright (C) 2004 - 2008 Colosa Inc.23
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
 */
////////////////////////////////////////////////////
// PM Functions
//
// Copyright (C) 2007 COLOSA
//
// License: LGPL, see LICENSE
////////////////////////////////////////////////////
use ProcessMaker\Core\System;
use ProcessMaker\Plugins\PluginRegistry;
use ProcessMaker\Util\ElementTranslation;


/**
 * ProcessMaker has made a number of its PHP functions available be used in triggers and conditions.
 * Most of these functions are wrappers for internal functions used in Gulliver, which is the development framework
 * used by ProcessMaker.
 * @class pmFunctions
 *
 * @name ProcessMaker Functions
 * @icon /images/pm.gif
 * @className class.pmFunctions.php
 */

/**
 * @method
 *
 * Retrieves the current date formated in the format "yyyy-mm-dd", with leading zeros in the
 * month and day if less than 10. This function is equivalent to PHP's date("Y-m-d").
 *
 * @name getCurrentDate
 * @label Get Current Date
 * @link http://wiki.processmaker.com/index.php/ProcessMaker_Functions#getCurrentDate.28.29
 *
 * @return date | $date | Current Date (Y-m-d) | It returns the current date as a string value.
 *
 */
function getCurrentDate ()
{
    return G::CurDate( 'Y-m-d' );
}

/**
 *
 * @method
 *
 * Returns the current time in the format "hh:mm:ss" with leading zeros when the hours,
 * minutes or seconds are less than 10.
 *
 * @name getCurrentTime
 * @label Get Current Time
 * @link http://wiki.processmaker.com/index.php/ProcessMaker_Functions#getCurrentTime.28.29
 *
 * @return time | $time | Current Time (H:i:s)| The function returns the current time as a string.
 *
 */
function getCurrentTime ()
{
    return G::CurDate( 'H:i:s' );
}

/**
 *
 * @method
 *
 * Retrieves information about a user with a given ID.
 *
 * @name userInfo
 * @label User Info
 * @link http://wiki.processmaker.com/index.php/ProcessMaker_Functions#userInfo.28.29
 *
 * @param string(32) | $userUid | User ID | The user unique ID
 * @return array | $info | User Info | An associative array with Information
 *
 */
function userInfo($userUid)
{
    return PMFInformationUser($userUid);
}

/**
 *
 * @method
 *
 * Returns a string converted into all UPPERCASE letters.
 *
 * @name upperCase
 * @label Upper Case
 * @link http://wiki.processmaker.com/index.php/ProcessMaker_Functions#upperCase.28.29
 *
 * @param string(32) | $sText | Text To Convert | A string to convert to UPPERCASE letters.
 * @return string | $TextC | Text Converted | Returns a string with the text converted into upper case letters.
 *
 */
function upperCase ($sText)
{
    return G::toUpper( $sText );
}

/**
 *
 * @method
 *
 * Returns a string with all the letters converted into lower case letters.
 *
 * @name lowerCase
 * @label Lower Case
 * @link http://wiki.processmaker.com/index.php/ProcessMaker_Functions#lowerCase.28.29
 *
 * @param string(32) | $sText | Text To Convert | A string to convert to lower case letters.
 * @return string | $TextC | Text Converted | Returns a string with the text converted into lower case letters.
 *
 */
function lowerCase ($sText)
{
    return G::toLower( $sText );
}

/**
 *
 * @method
 *
 * Converts the first letter in each word into an uppercase letter.
 * Subsequent letters in each word are changed into lowercase letters.
 *
 * @name capitalize
 * @label Capitalize
 * @link http://wiki.processmaker.com/index.php/ProcessMaker_Functions#capitalize.28.29
 *
 * @param string(32) | $sText | Text To Convert | The string to capitalize.
 * @return string | $TextC | Text Converted | It returns the introduced text with the first letter capitalized in each word and the subsequent letters into lowercase letters
 *
 */
function capitalize ($sText)
{
    return G::capitalizeWords( $sText );
}

/**
 *
 * @method
 *
 * Returns a string formatted according to the given date format and given language
 *
 * @name formatDate
 * @label Format Date
 * @link http://wiki.processmaker.com/index.php/ProcessMaker_Functions#formatDate.28.29
 *
 * @param string(32) | $date | Date | The input date to be reformatted. The input date must be a string in the format 'yyyy-mm-dd'.
 * @param string(32) | $format="" | format | The format of the date which will be returned. It can have the following definitions:
 * @param string(32) | $lang="en"| Language | The language in which to reformat the date. It can be 'en' (English), 'es' (Spanish) or 'fa' (Persian).
 * @return string | $formatDate | Date whit format | It returns the passed date according to the given date format.
 *
 */
function formatDate ($date, $format = '', $lang = 'en')
{
    if (! isset( $date ) or $date == '') {
        throw new Exception( 'function:formatDate::Bad param' );
    }
    try {
        return G::getformatedDate( $date, $format, $lang );
    } catch (Exception $oException) {
        throw $oException;
    }
}

/**
 *
 * @method
 *
 * Returns a specified date written out in a given language, with full month names.
 *
 * @name literalDate
 * @label Literal Date
 * @link http://wiki.processmaker.com/index.php/ProcessMaker_Functions#literalDate.28.29
 *
 * @param string(32) | $date | date | The input date in standard format (yyyy-mm-dd) that is a string.
 * @param string(32) | $lang="en" | Language | The language to display, which can be 'en' (English) or 'es' (Spanish). If not included, then it will be English by default.
 * @return string | $literaDate | Literal date | It returns the literal date as a string value.
 *
 */
function literalDate ($date, $lang = 'en')
{
    if (! isset( $date ) or $date == '') {
        throw new Exception( 'function:formatDate::Bad param' );
    }
    try {
        switch ($lang) {
            case 'en':
                $ret = G::getformatedDate( $date, 'M d,yyyy', $lang );
                break;
            case 'es':
                $ret = G::getformatedDate( $date, 'd de M de yyyy', $lang );
                break;
        }
        return $ret;
    } catch (Exception $oException) {
        throw $oException;
    }
}

/**
 *
 * @method
 *
 * Executes a SQL statement in a database connection or in one of ProcessMaker's
 * internal databases.
 *
 * @name executeQuery
 * @label execute Query
 * @link http://wiki.processmaker.com/index.php/ProcessMaker_Functions#executeQuery.28.29
 *
 * @param string(32) | $SqlStatement | Sql query | The SQL statement to be executed. Do NOT include the database name in the SQL statement.
 * @param string(32) | $DBConnectionUID="workflow"| UID database | The UID of the database connection where the SQL statement will be executed.
 *
 * @return array or string | $Resultquery | Result | Result of the query | If executing a SELECT statement, it returns an array of associative arrays
 * @throws SQLException
 *
 */
function executeQuery ($SqlStatement, $DBConnectionUID = 'workflow', $aParameter = array())
{
    $sysSys = (defined("SYS_SYS"))? SYS_SYS : "Undefined";
    $aContext = \Bootstrap::getDefaultContextLog();
    $con = Propel::getConnection( $DBConnectionUID );
    $con->begin();
    $blackList = System::getQueryBlackList();
    $listQueries = explode('|', isset($blackList['queries']) ? $blackList['queries'] : '');
    $aListAllTables = explode(
        '|',
        ((isset($blackList['tables']))? $blackList['tables'] : '') .
        ((isset($blackList['pmtables']))? $blackList['pmtables'] : '')
    );
    $parseSqlStm = new PHPSQLParser($SqlStatement);
    try {
        //Parsing queries and check the blacklist
        foreach ($parseSqlStm as $key => $value) {
            if($key === 'parsed'){
                $aParseSqlStm = $value;
                continue;
            }
        }
        $nameOfTable = '';
        $arrayOfTables = array();
        foreach ($aParseSqlStm as $key => $value) {
            if(in_array($key, $listQueries)){
                if(isset($value['table'])){
                    $nameOfTable = $value['table'];
                } else {
                    foreach ($value as $valueTab) {
                        if(is_array($valueTab)){
                            $arrayOfTables = $valueTab;
                        } else {
                            $nameOfTable = $valueTab;
                        }
                    }
                }
                if(isset($nameOfTable) && $nameOfTable !== ''){
                    if(in_array($nameOfTable,$aListAllTables)){
                        G::SendTemporalMessage( G::loadTranslation('ID_NOT_EXECUTE_QUERY', array($nameOfTable)), 'error', 'labels' );
                        throw new SQLException(G::loadTranslation('ID_NOT_EXECUTE_QUERY', array($nameOfTable)));
                    }
                }
                if (is_array($arrayOfTables)){
                    foreach ($arrayOfTables as $row){
                        if(!empty($row)){
                            if(in_array($row, $aListAllTables)){
                                G::SendTemporalMessage(G::loadTranslation('ID_NOT_EXECUTE_QUERY', array($nameOfTable)), 'error', 'labels' );
                                throw new SQLException(G::loadTranslation('ID_NOT_EXECUTE_QUERY', array($nameOfTable)));
                            }
                        }
                    }
                }
            }
        }

        $statement = trim( $SqlStatement );
        $statement = str_replace( '(', '', $statement );

        $result = false;
        if (getEngineDataBaseName( $con ) != 'oracle') {
            switch (true) {
                case preg_match( "/^(SELECT|EXECUTE|EXEC|SHOW|DESCRIBE|EXPLAIN|BEGIN)\s/i", $statement ):
                    $rs = $con->executeQuery( $SqlStatement );
                    $con->commit();

                    $result = Array ();
                    $i = 1;
                    while ($rs->next()) {
                        $result[$i ++] = $rs->getRow();
                    }
                    break;
                case preg_match( "/^INSERT\s/i", $statement ):
                    $rs = $con->executeUpdate( $SqlStatement );
                    $result = $con->getUpdateCount();
                    $con->commit();
                    break;
                case preg_match( "/^REPLACE\s/i", $statement ):
                    $rs = $con->executeUpdate( $SqlStatement );
                    $result = $con->getUpdateCount();
                    $con->commit();
                    break;
                case preg_match( "/^UPDATE\s/i", $statement ):
                    $rs = $con->executeUpdate( $SqlStatement );
                    $result = $con->getUpdateCount();
                    $con->commit();
                    break;
                case preg_match( "/^DELETE\s/i", $statement ):
                    $rs = $con->executeUpdate( $SqlStatement );
                    $result = $con->getUpdateCount();
                    $con->commit();
                    break;
            }
        } else {
            $dataEncode = $con->getDSN();

            if (isset($dataEncode["encoding"]) && $dataEncode["encoding"] != "") {
                $result = executeQueryOci($SqlStatement, $con, $aParameter, $dataEncode["encoding"]);
            } else {
                $result = executeQueryOci($SqlStatement, $con, $aParameter);
            }
        }
        //Logger
        $aContext['action'] = 'execute-query';
        $aContext['sql'] = $SqlStatement;
        \Bootstrap::registerMonolog('sqlExecution', 200, 'Sql Execution', $aContext, $sysSys, 'processmaker.log');

        return $result;
    } catch (SQLException $sqle) {
        //Logger
        $aContext['action'] = 'execute-query';
        $aContext['exception'] = (array)$sqle;
        \Bootstrap::registerMonolog('sqlExecution', 400, 'Sql Execution', $aContext, $sysSys, 'processmaker.log');

        if (isset($sqle->xdebug_message)) {
            error_log(print_r($sqle->xdebug_message, true));
        } else {
            error_log(print_r($sqle, true));
        }
        $con->rollback();
        throw $sqle;
    }
}

/**
 *
 * @method
 *
 * Sorts a grid according to a specified field in ascending or descending order.
 *
 * @name orderGrid
 * @label order Grid
 * @link http://wiki.processmaker.com/index.php/ProcessMaker_Functions#orderGrid.28.29
 *
 * @param array | $dataM | Grid Name | A grid, which is a numbered array containing associative arrays with field names and their values, it has to be set like this "@=".
 * @param string(32) | $field | Name of field | The name of the field by which the grid will be sorted.
 * @param string(32) | $ord = "ASC"| Optional parameter (Criteria) | Optional parameter. The order which can either be 'ASC' (ascending) or 'DESC' (descending). If not included, 'ASC' will be used by default.
 * @return array | $dataM | Grid Sorted | Grid sorted
 *
 */
function orderGrid ($dataM, $field, $ord = 'ASC')
{
    if (! is_array( $dataM ) or ! isset( $field ) or $field == '') {
        throw new Exception( 'function:orderGrid Error!, bad parameters found!' );
    }
    for ($i = 1; $i <= count( $dataM ) - 1; $i ++) {
        for ($j = $i + 1; $j <= count( $dataM ); $j ++) {
            if (strtoupper( $ord ) == 'ASC') {
                if (strtolower( $dataM[$j][$field] ) < strtolower( $dataM[$i][$field] )) {
                    $swap = $dataM[$i];
                    $dataM[$i] = $dataM[$j];
                    $dataM[$j] = $swap;
                }
            } else {
                if ($dataM[$j][$field] > $dataM[$i][$field]) {
                    $swap = $dataM[$i];
                    $dataM[$i] = $dataM[$j];
                    $dataM[$j] = $swap;
                }
            }
        }
    }
    return $dataM;
}

/**
 *
 * @method
 *
 * Executes operations among the grid fields, such as addition, substraction, etc
 *
 * @name evaluateFunction
 * @label evaluate Function
 * @link http://wiki.processmaker.com/index.php/ProcessMaker_Functions#evaluateFunction.28.29
 *
 * @param array | $aGrid | Grid | The input grid.
 * @param string(32) | $sExpresion | Expression for the operation | The input expression for the operation among grid fields. The expression must always be within double quotes, otherwise a fatal error will occur.
 * @return array | $aGrid | Grid | Grid with executed operation
 *
 */
function evaluateFunction ($aGrid, $sExpresion)
{
    $sExpresion = str_replace( 'Array', '$this->aFields', $sExpresion );
    $sExpresion .= ';';

    $pmScript = new PMScript();
    $pmScript->setScript( $sExpresion );

    for ($i = 1; $i <= count( $aGrid ); $i ++) {
        $aFields = $aGrid[$i];

        $pmScript->setFields( $aFields );

        $pmScript->execute();

        $aGrid[$i] = $pmScript->aFields;

        //compatibility for var_label
        foreach ($aFields as $j => $val) {
            if (isset($aGrid[$i][$j . "_label"]) && empty($aGrid[$i][$j . "_label"]) && !empty($aGrid[$i][$j])) {
                $aGrid[$i][$j . "_label"] = $aGrid[$i][$j];
            }
            if (substr($j, -6) !== "_label" && ($val !== $aGrid[$i][$j])) {
                $aGrid[$i][$j . "_label"] = $aGrid[$i][$j];
            }
        }
        //end
    }
    return $aGrid;
}

/**
 * Web Services Functions *
 */
/**
 *
 * @method
 *
 * Logs in a user to initiate a web services session in a ProcessMaker server.
 *
 * @name WSLogin
 * @label WS Login
 * @link http://wiki.processmaker.com/index.php/ProcessMaker_Functions#WSLogin.28.29
 *
 * @param string(32) | $user | Username of the user | The username of the user who will login to ProcessMaker. All subsequent actions will be limited to the permissions of that user.
 * @param string(32) | $pass | Password encrypted | The user's password encrypted as an MD5 or SHA256 hash with '{hashType}:' prepended.
 * @param string(32) | $endpoint="" | URI of the WSDL | The URI (address) of the WSDL definition of the ProcessMaker web services.
 * @return string | $unique ID | Unique Id |The unique ID for the initiated session.
 *
 */
function WSLogin ($user, $pass, $endpoint = "")
{
    $client = WSOpen( true );

    $params = array ("userid" => $user,"password" => $pass
    );

    $result = $client->__soapCall( "login", array ($params
    ) );

    if ($result->status_code == 0) {
        if ($endpoint != "") {
            if (isset( $_SESSION["WS_SESSION_ID"] )) {
                $_SESSION["WS_END_POINT"] = $endpoint;
            }
        }
        /*
        if (isset($_SESSION["WS_SESSION_ID"]))
        return $_SESSION["WS_SESSION_ID"] = $result->message;
        else
        return $result->message;
        */

        $_SESSION["WS_SESSION_ID"] = $result->message;

        return $result->message;
    } else {
        if (isset( $_SESSION["WS_SESSION_ID"] )) {
            unset( $_SESSION["WS_SESSION_ID"] );
        }

        $wp = (trim( $pass ) != "") ? "YES" : "NO";

        throw new Exception( "WSAccess denied! for user $user with password $wp" );
    }
}

/**
 *
 * @method
 *
 * Opens a connection for web services and returns a SOAP client object which is
 * used by all subsequent other WS function calls
 *
 * @name WSOpen
 * @label WS Open
 * @link http://wiki.processmaker.com/index.php/ProcessMaker_Functions#WSOpen.28.29
 *
 * @param boolean | $force=false | Optional Parameter | Optional parameter. Set to true to force a new connection to be created even if a valid connection already exists.
 * @return Object Client | $client | SoapClient object | A SoapClient object. If unable to establish a connection, returns NULL.
 *
 */
function WSOpen ($force = false)
{
    if (isset( $_SESSION["WS_SESSION_ID"] ) || $force) {
        $optionsHeaders = array(
            "cache_wsdl" => WSDL_CACHE_NONE,
            "soap_version" => SOAP_1_1,
            "trace" => 1,
            "stream_context" => stream_context_create(
                array(
                    'ssl' => array(
                        'verify_peer' => 0,
                        'verify_peer_name' => 0
                    )
                )
            )
        );

        if (! isset( $_SESSION["WS_END_POINT"] )) {
            $defaultEndpoint = $_SERVER["REQUEST_SCHEME"] . "://" . $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . "/sys" . SYS_SYS . "/en/classic/services/wsdl2";
        }

        $endpoint = isset( $_SESSION["WS_END_POINT"] ) ? $_SESSION["WS_END_POINT"] : $defaultEndpoint;

        $client = new SoapClient( $endpoint, $optionsHeaders);

        return $client;
    } else {
        throw new Exception( "WS session is not open" );
    }
}

/**
 *
 * @method
 *
 * Returns all the tasks which has open delegations for the indicated case.
 *
 * @name WSTaskCase
 * @label WS Task Case
 * @link http://wiki.processmaker.com/index.php/ProcessMaker_Functions#WSTaskCase.28.29
 *
 * @param string(32) | $caseId | Case ID | The unique ID for the case. Case UIDs can be found with WSCaseList() and are stored in the field wf_<WORKSPACE>.APPLICATION.APP_UID.
 * @return array | $rows | Array of tasks open | An array of tasks in the indicated case which have open delegations.
 *
 */
function WSTaskCase ($caseId)
{
    $client = WSOpen();

    $sessionId = $_SESSION["WS_SESSION_ID"];
    $params = array ("sessionId" => $sessionId,"caseId" => $caseId
    );

    $result = $client->__soapCall( "taskCase", array ($params
    ) );

    $rows = array ();
    $i = 0;

    if (isset( $result->taskCases )) {
        //Processing when it is an array
        if (is_array( $result->taskCases )) {
            foreach ($result->taskCases as $key => $obj) {
                $rows[$i] = array ("guid" => $obj->guid,"name" => $obj->name
                );
                $i = $i + 1;
            }
        } else {
            //Processing when it is an object //1 row
            if (is_object( $result->taskCases )) {
                $rows[$i] = array ("guid" => $result->taskCases->guid,"name" => $result->taskCases->name
                );
                $i = $i + 1;
            }
        }
    }

    return $rows;
}

/**
 *
 * @method
 *
 * Returns a list of tasks in which the logged-in user can initiate cases or is
 * assigned to these cases.
 *
 * @name WSTaskList
 * @label WS Task List
 * @link http://wiki.processmaker.com/index.php/ProcessMaker_Functions#WSTaskList.28.29
 *
 * @return array | $rows |List of tasks | This function returns a list of tasks
 *
 */
function WSTaskList ()
{
    $client = WSOpen();

    $sessionId = $_SESSION["WS_SESSION_ID"];
    $params = array ("sessionId" => $sessionId
    );

    $result = $client->__soapCall( "TaskList", array ($params
    ) );

    $rows = array ();
    $i = 0;

    if (isset( $result->tasks )) {
        //Processing when it is an array
        if (is_array( $result->tasks )) {
            foreach ($result->tasks as $key => $obj) {
                $rows[$i] = array ("guid" => $obj->guid,"name" => $obj->name
                );
                $i = $i + 1;
            }
        } else {
            //Processing when it is an object //1 row
            if (is_object( $result->tasks )) {
                $rows[$i] = array ("guid" => $result->tasks->guid,"name" => $result->tasks->name
                );
                $i = $i + 1;
            }
        }
    }

    return $rows;
}

/**
 *
 * @method
 *
 * Returns a list of users whose status is "ACTIVE" in the current workspace.
 *
 * @name WSUserList
 * @label WS User List
 * @link http://wiki.processmaker.com/index.php/ProcessMaker_Functions#WSUserList.28.29
 *
 * @return array | $rows | List | List of Active users in the workspace
 *
 */
function WSUserList ()
{
    $client = WSOpen();

    $sessionId = $_SESSION["WS_SESSION_ID"];
    $params = array ("sessionId" => $sessionId
    );

    $result = $client->__soapCall( "UserList", array ($params
    ) );

    $rows = array ();
    $i = 0;

    if (isset( $result->users )) {
        //Processing when it is an array
        if (is_array( $result->users )) {
            foreach ($result->users as $key => $obj) {
                $rows[$i] = array ("guid" => $obj->guid,"name" => $obj->name
                );
                $i = $i + 1;
            }
        } else {
            //Processing when it is an object //1 row
            if (is_object( $result->users )) {
                $rows[$i] = array ("guid" => $result->users->guid,"name" => $result->users->name
                );
                $i = $i + 1;
            }
        }
    }

    return $rows;
}

/**
 *
 * @method
 *
 * Returns a list of active groups in a workspace.
 *
 * @name WSGroupList
 * @label WS Group List
 * @link http://wiki.processmaker.com/index.php/ProcessMaker_Functions#WSGroupList.28.29
 *
 * @return array | $rows | List | List of active groups in the workspace
 *
 */
function WSGroupList ()
{
    $client = WSOpen();

    $sessionId = $_SESSION["WS_SESSION_ID"];
    $params = array ("sessionId" => $sessionId
    );

    $result = $client->__soapCall( "GroupList", array ($params
    ) );

    $rows = array ();
    $i = 0;

    if (isset( $result->groups )) {
        //Processing when it is an array
        if (is_array( $result->groups )) {
            foreach ($result->groups as $key => $obj) {
                $rows[$i] = array ("guid" => $obj->guid,"name" => $obj->name
                );
                $i = $i + 1;
            }
        } else {
            //Processing when it is an object //1 row
            if (is_object( $result->groups )) {
                $rows[$i] = array ("guid" => $result->groups->guid,"name" => $result->groups->name
                );
                $i = $i + 1;
            }
        }
    }

    return $rows;
}

/**
 *
 * @method
 *
 * Returns a list of roles in the current workspace.
 *
 * @name WSRoleList
 * @label WS Role List
 * @link http://wiki.processmaker.com/index.php/ProcessMaker_Functions#WSRoleList.28.29
 *
 * @return array | $rows | List | List of roles in the workspace
 *
 */
function WSRoleList ()
{
    $client = WSOpen();

    $sessionId = $_SESSION["WS_SESSION_ID"];
    $params = array ("sessionId" => $sessionId
    );

    $result = $client->__soapCall( "RoleList", array ($params
    ) );

    $rows = array ();
    $i = 0;

    if (isset( $result->roles )) {
        //Processing when it is an array
        if (is_array( $result->roles )) {
            foreach ($result->roles as $key => $obj) {
                $rows[$i] = array ("guid" => $obj->guid,"name" => $obj->name
                );
                $i = $i + 1;
            }
        } else {
            //Processing when it is an object //1 row
            if (is_object( $result->roles )) {
                $rows[$i] = array ("guid" => $result->roles->guid,"name" => $result->roles->name
                );
                $i = $i + 1;
            }
        }
    }

    return $rows;
}

/**
 *
 * @method
 *
 * Returns a list of the cases which the current logged-in user has privileges to
 * open.
 *
 * @name WSCaseList
 * @label WS Case List
 * @link http://wiki.processmaker.com/index.php/ProcessMaker_Functions#WSCaseList.28.29
 *
 * @return array | $rows | List of the cases |It returns a list of cases
 *
 */
function WSCaseList ()
{
    $client = WSOpen();

    $sessionId = $_SESSION["WS_SESSION_ID"];
    $params = array ("sessionId" => $sessionId
    );

    $result = $client->__soapCall( "CaseList", array ($params
    ) );

    $rows = array ();
    $i = 0;

    if (isset( $result->cases )) {
        //Processing when it is an array
        if (is_array( $result->cases )) {
            foreach ($result->cases as $key => $obj) {
                $rows[$i] = array ("guid" => $obj->guid,"name" => $obj->name
                );
                $i = $i + 1;
            }
        } else {
            //Processing when it is an object //1 row
            if (is_object( $result->cases )) {
                $rows[$i] = array ("guid" => $result->cases->guid,"name" => $result->cases->name
                );
                $i = $i + 1;
            }
        }
    }

    return $rows;
}

/**
 *
 * @method
 *
 * Returns a list of processes in the current workspace.
 *
 * @name WSProcessList
 * @label WS Process List
 * @link http://wiki.processmaker.com/index.php/ProcessMaker_Functions#WSProcessList.28.29
 *
 * @return array | $rows | List of processes | A list of processes
 *
 */
function WSProcessList ()
{
    $client = WSOpen();

    $sessionId = $_SESSION["WS_SESSION_ID"];
    $params = array ("sessionId" => $sessionId
    );

    $result = $client->__soapCall( "ProcessList", array ($params
    ) );

    $rows = array ();
    $i = 0;

    if (isset( $result->processes )) {
        //Processing when it is an array
        if (is_array( $result->processes )) {
            foreach ($result->processes as $key => $obj) {
                $rows[$i] = array ("guid" => $obj->guid,"name" => $obj->name
                );
                $i = $i + 1;
            }
        } else {
            //Processing when it is an object //1 row
            if (is_object( $result->processes )) {
                $rows[$i] = array ("guid" => $result->processes->guid,"name" => $result->processes->name
                );
                $i = $i + 1;
            }
        }
    }

    return $rows;
}

/**
 *
 * @method
 *
 * Returns Email configuration.
 *
 * @name getEmailConfiguration
 * @label Get Email Configuration
 *
 * @return array | $aFields | Array |Get current email configuration
 *
 */
//private function to get current email configuration
function getEmailConfiguration ()
{
    return System::getEmailConfiguration();
}

/**
 *
 * @method
 *
 * Sends an email using a template file.
 *
 * @name PMFSendMessage
 * @label PMF Send Message
 * @link http://wiki.processmaker.com/index.php/ProcessMaker_Functions#PMFSendMessage.28.29
 *
 * @param string(32) | $caseId | UID for case | The UID (unique identification) for a case, which is a string of 32 hexadecimal characters to identify the case.
 * @param string(32) | $sFrom | Sender | The email address of the person who sends out the email.
 * @param string(100) | $sTo | Recipient | The email address(es) to whom the email is sent. If multiple recipients, separate each email address with a comma.
 * @param string(100) | $sCc = '' | Carbon copy recipient | The email address(es) of people who will receive carbon copies of the email.
 * @param string(100) | $sBcc = ''| Carbon copy recipient | The email address(es) of people who will receive blind carbon copies of the email.
 * @param string(50) | $sSubject | Subject of the email | The subject (title) of the email.
 * @param string(50) | $sTemplate | Name of the template | The name of the template file in plain text or HTML format which will produce the body of the email.
 * @param array | $aFields = array() | Variables for email template | Optional parameter. An associative array where the keys are the variable names and the values are the variables' values.
 * @param array | $aAttachment = array() | Attachment | An Optional arrray. An array of files (full paths) to be attached to the email.
 * @param boolean | $showMessage = true | Show message | Optional parameter. Set to TRUE to show the message in the case's message history.
 * @param int | $delIndex = 0 | Delegation index of the case | Optional parameter. The delegation index of the current task in the case.
 * @param string(100) | $config = '' | Email server configuration | An optional array: An array of parameters to be used in the Email sent (MESS_ENGINE, MESS_SERVER, MESS_PORT, MESS_FROM_MAIL, MESS_RAUTH, MESS_ACCOUNT, MESS_PASSWORD, and SMTPSecure) Or String: UID of Email server .
 * @return int | | result | Result of sending email
 *
 */
//@param array | $aFields=array() | An associative array optional | Optional parameter. An associative array where the keys are the variable name and the values are the variable's value.
function PMFSendMessage(
    $caseId,
    $sFrom,
    $sTo,
    $sCc,
    $sBcc,
    $sSubject,
    $sTemplate,
    $aFields = array(),
    $aAttachment = array(),
    $showMessage = true,
    $delIndex = 0,
    $config = array()
) {
    ini_set ( "pcre.backtrack_limit", 1000000 );
    ini_set ( 'memory_limit', '-1' );
    @set_time_limit ( 100000 );

    global $oPMScript;

    if (isset( $oPMScript->aFields ) && is_array( $oPMScript->aFields )) {
        if (is_array( $aFields )) {
            $aFields = array_merge( $oPMScript->aFields, $aFields );
        } else {
            $aFields = $oPMScript->aFields;
        }
    }

    $ws = new WsBase();
    $result = $ws->sendMessage(
        $caseId,
        $sFrom,
        $sTo,
        $sCc,
        $sBcc,
        $sSubject,
        $sTemplate,
        $aFields,
        $aAttachment,
        $showMessage,
        $delIndex,
        $config
    );

    if ($result->status_code == 0) {
        return 1;
    } else {
        error_log($result->message);
        return 0;
    }
}

/**
 *
 * @method
 *
 * Sends two variables to the specified case.
 * It will create new case variables if they don't already exist
 *
 * @name WSSendVariables
 * @label WS Send Variables
 * @link http://wiki.processmaker.com/index.php/ProcessMaker_Functions#WSSendVariables.28.29
 *
 * @param string(32) | $caseId | UID for case | The unique ID of the case which will receive the variables.
 * @param string(32) | $name1 | Name of the first variable | The name of the first variable to be sent to the created case.
 * @param string(32) | $value1 | Value of the first variable | The value of the first variable to be sent to the created case.
 * @param string(32) | $name2 | Name of the second variable | The name of the second variable to be sent to the created case.
 * @param string(32) | $value2 | Value of the second variable | The value of the second variable to be sent to the created case.
 * @return array | $fields | WS Response Associative Array: | The function returns a WS Response associative array.
 *
 */
function WSSendVariables ($caseId, $name1, $value1, $name2, $value2)
{
    $client = WSOpen();

    $sessionId = $_SESSION["WS_SESSION_ID"];

    $v1 = new stdClass();
    $v1->name = $name1;
    $v1->value = $value1;

    $v2 = new stdClass();
    $v2->name = $name2;
    $v2->value = $value2;

    $variables = array ($v1,$v2
    );

    $params = array ("sessionId" => $sessionId,"caseId" => $caseId,"variables" => $variables
    );

    $result = $client->__soapCall( "SendVariables", array ($params
    ) );

    $fields["status_code"] = $result->status_code;
    $fields["message"] = $result->message;
    $fields["time_stamp"] = $result->timestamp;

    return $fields;
}

/**
 *
 * @method
 *
 * Routes (derivates) a case, moving the case to the next task in the process
 * according its routing rules.
 *
 * @name WSDerivateCase
 * @label WS Derivate Case
 * @link http://wiki.processmaker.com/index.php/ProcessMaker_Functions#WSDerivateCase.28.29
 *
 * @param string(32) | $CaseId | Case ID |The unique ID for a case, which can be found with WSCaseList() or by examining the field wf_<WORKSPACE>.APPLICATION.APP_UID.
 * @param string(32) | $delIndex | Delegation index for the task | The delegation index for the task, which can be found by examining the field wf_<WORKSPACE>.APP_DELEGATION.DEL_INDEX.
 * @return array | $fields | WS Response Associative Array | A WS Response associative array.
 *
 */
function WSDerivateCase ($caseId, $delIndex)
{
    $client = WSOpen();

    $sessionId = $_SESSION["WS_SESSION_ID"];

    $params = array ("sessionId" => $sessionId,"caseId" => $caseId,"delIndex" => $delIndex
    );

    $result = $client->__soapCall( "DerivateCase", array ($params
    ) );

    $fields["status_code"] = $result->status_code;
    $fields["message"] = $result->message;
    $fields["time_stamp"] = $result->timestamp;

    return $fields;
}

/**
 *
 * @method
 *
 * Creates a case with any user with two initial case variables.
 *
 * @name WSNewCaseImpersonate
 * @label WS New Case Impersonate
 * @link http://wiki.processmaker.com/index.php/ProcessMaker_Functions#WSNewCaseImpersonate.28.29
 *
 * @param string(32) | $processId | Process ID | The unique ID for the process.
 * @param string(32) | $userId | User ID | The unique ID for the user.
 * @param string(32) | $name1 | Name of the first variable | The name of the first variable to be sent to the created case.
 * @param string(32) | $value1 | Value of the first variable | The value of the first variable to be sent to the created case.
 * @param string(32) | $name2 | Name of the second variable | The name of the second variable to be sent to the created case.
 * @param string(32) | $value2 | Value of the second variable | The value of the second variable to be sent to the created case.
 * @return array | $fields | WS Response Associative Array | A WS Response associative array.
 *
 */
function WSNewCaseImpersonate ($processId, $userId, $name1, $value1, $name2, $value2)
{
    $client = WSOpen();

    $sessionId = $_SESSION["WS_SESSION_ID"];

    $v1 = new stdClass();
    $v1->name = $name1;
    $v1->value = $value1;

    $v2 = new stdClass();
    $v2->name = $name2;
    $v2->value = $value2;

    $variables = array ($v1,$v2
    );

    $params = array ("sessionId" => $sessionId,"processId" => $processId,"userId" => $userId,"variables" => $variables
    );

    $result = $client->__soapCall( "NewCaseImpersonate", array ($params
    ) );

    $fields["status_code"] = $result->status_code;
    $fields["message"] = $result->message;
    $fields["time_stamp"] = $result->timestamp;
    $fields["case_id"] = $result->caseId;
    $fields["case_number"] = $result->caseNumber;

    return $fields;
}

/**
 *
 * @method
 *
 * Creates a new case starting with a specified task and using two initial case
 * variables.
 *
 * @name WSNewCase
 * @label WS New Case
 * @link http://wiki.processmaker.com/index.php/ProcessMaker_Functions#WSNewCase.28.29
 *
 * @param string(32) | $processId | Process ID | The unique ID for the process. To use the current process, use the system variable @@PROCESS.
 * @param string(32) | $userId | User ID | The unique ID for the user. To use the currently logged-in user, use the system variable @@USER_LOGGED.
 * @param string(32) | $name1 | Name of the first variable | The name of the first variable to be sent to the created case.
 * @param string(32) | $value1 | Value of the first variable | The value of the first variable to be sent to the created case.
 * @param string(32) | $name2 | Name of the second variable | The name of the second variable to be sent to the created case.
 * @param string(32) | $value2 | Value of the second variable | The value of the second variable to be sent to the created case.
 * @return array | $fields | WS array | A WS Response associative array.
 *
 */
function WSNewCase ($processId, $taskId, $name1, $value1, $name2, $value2)
{
    $client = WSOpen();
    $sessionId = $_SESSION["WS_SESSION_ID"];

    $v1 = new stdClass();
    $v1->name = $name1;
    $v1->value = $value1;

    $v2 = new stdClass();
    $v2->name = $name2;
    $v2->value = $value2;

    $variables = array ($v1,$v2
    );

    $params = array ("sessionId" => $sessionId,"processId" => $processId,"taskId" => $taskId,"variables" => $variables
    );

    $result = $client->__soapCall( "NewCase", array ($params
    ) );

    $fields["status_code"] = $result->status_code;
    $fields["message"] = $result->message;
    $fields["time_stamp"] = $result->timestamp;
    $fields["case_id"] = $result->caseId;
    $fields["case_number"] = $result->caseNumber;

    return $fields;
}

/**
 *
 * @method
 *
 * Assigns a user to a group (as long as the logged in user has the PM_USERS
 * permission in their role).
 *
 * @name WSAssignUserToGroup
 * @label WS Assign User To Group
 * @link http://wiki.processmaker.com/index.php/ProcessMaker_Functions#WSAssignUserToGroup.28.29
 *
 * @param string(32) | $userId | User ID | The unique ID for a user.
 * @param string(32) | $groupId | Group ID | The unique ID for a group.
 * @return array | $fields | WS array |A WS Response associative array.
 *
 */
function WSAssignUserToGroup ($userId, $groupId)
{
    $client = WSOpen();

    $sessionId = $_SESSION["WS_SESSION_ID"];

    $params = array ("sessionId" => $sessionId,"userId" => $userId,"groupId" => $groupId
    );

    $result = $client->__soapCall( "AssignUserToGroup", array ($params
    ) );

    $fields["status_code"] = $result->status_code;
    $fields["message"] = $result->message;
    $fields["time_stamp"] = $result->timestamp;

    return $fields;
}

/**
 *
 * @method
 *
 * Creates a new user in ProcessMaker.
 *
 * @name WSCreateUser
 * @label WS Create User
 * @link http://wiki.processmaker.com/index.php/ProcessMaker_Functions#WSCreateUser.28.29
 *
 * @param string(32) | $userId | User ID | The username of the new user, which can be up to 32 characters long.
 * @param string(32) | $password | Password of the new user | El password of the new user, which can be up to 32 characters long.
 * @param string(32) | $firstname | Firstname of the new user | The first name(s) of the new user, which can be up to 50 characters long.
 * @param string(32) | $lastname | Lastname of the new user | The last name(s) of the new user, which can be up to 50 characters long.
 * @param string(32) | $email | Email the new user | The e-mail of the new user, which can be up to 100 characters long.
 * @param string(32) | $role | Rol of the new user | The role of the new user, such as "PROCESSMAKER_ADMIN" and "PROCESSMAKER_OPERATOR".
 * @param string(32) | $dueDate=null | Expiration date | Optional parameter. The expiration date must be a string in the format "yyyy-mm-dd".
 * @param string(32) | $status=null | Status of the new user | Optional parameter. The user's status, such as "ACTIVE", "INACTIVE" or "VACATION".
 * @return array | $fields | WS array | A WS Response associative array.
 *
 */
function WSCreateUser ($userId, $password, $firstname, $lastname, $email, $role, $dueDate = null, $status = null)
{
    $client = WSOpen();

    $sessionId = $_SESSION["WS_SESSION_ID"];

    $params = array ("sessionId" => $sessionId,"userId" => $userId,"firstname" => $firstname,"lastname" => $lastname,"email" => $email,"role" => $role,"password" => $password,"dueDate" => $dueDate,"status" => $status
    );

    try {
        $result = $client->__soapCall( "CreateUser", array ($params) );
    } catch(Exception $oError) {
        return $oError->getMessage();
    }

    $fields["status_code"] = $result->status_code;
    $fields["message"] = $result->message;
    $fields["time_stamp"] = $result->timestamp;

    return $fields;
}

/**
 *
 * @method
 *
 * Update an user in ProcessMaker.
 *
 * @name WSUpdateUser
 * @label WS Update User
 * @link http://wiki.processmaker.com/index.php/ProcessMaker_Functions#WSUpdateUser.28.29
 *
 * @param string(32) | $userUid | User UID | The user UID.
 * @param string(32) | $userName | User ID | The username for the user.
 * @param string(32) | $firstName=null | Firstname of the user | Optional parameter. The first name of the user, which can be up to 50 characters long.
 * @param string(32) | $lastName=null | Lastname of the user | Optional parameter. The last name of the user, which can be up to 50 characters long.
 * @param string(32) | $email=null | Email the user | Optional parameter. The email of the user, which can be up to 100 characters long.
 * @param string(32) | $dueDate=null | Expiration date | Optional parameter. The expiration date must be a string in the format "yyyy-mm-dd".
 * @param string(32) | $status=null | Status of the user | Optional parameter. The user's status, such as "ACTIVE", "INACTIVE" or "VACATION".
 * @param string(32) | $role=null | Rol of the user | The role of the user such as "PROCESSMAKER_ADMIN" or "PROCESSMAKER_OPERATOR".
 * @param string(32) | $password=null | Password of the user | The password of the user, which can be up to 32 characters long.
 * @return array | $fields | WS array | A WS Response associative array.
 *
 */
function WSUpdateUser ($userUid, $userName, $firstName = null, $lastName = null, $email = null, $dueDate = null, $status = null, $role = null, $password = null)
{
    $client = WSOpen();

    $sessionId = $_SESSION["WS_SESSION_ID"];

    $params = array ("sessionId" => $sessionId,"userUid" => $userUid,"userName" => $userName,"firstName" => $firstName,"lastName" => $lastName,"email" => $email,"dueDate" => $dueDate,"status" => $status,"role" => $role,"password" => $password
    );

    $result = $client->__soapCall( "updateUser", array ($params
    ) );

    $fields["status_code"] = $result->status_code;
    $fields["message"] = $result->message;
    $fields["time_stamp"] = $result->timestamp;

    return $fields;
}

/**
 *
 * @method
 *
 * Retrieves information about a user with a given ID.
 *
 * @name WSInformationUser
 * @label WS Information User
 * @link http://wiki.processmaker.com/index.php/ProcessMaker_Functions#WSInformationUser.28.29
 *
 * @param string(32) | $userUid | User UID | The user UID.
 * @return array | $response | WS array | A WS Response associative array.
 *
 */
function WSInformationUser($userUid)
{
    $client = WSOpen();

    $sessionId = $_SESSION["WS_SESSION_ID"];

    $params = array(
        "sessionId" => $sessionId,
        "userUid"   => $userUid
    );

    $result = $client->__soapCall("informationUser", array($params));

    $response = array();
    $response["status_code"] = $result->status_code;
    $response["message"]     = $result->message;
    $response["time_stamp"]  = $result->timestamp;
    $response["info"] = (isset($result->info))? $result->info : null;

    return $response;
}

/**
 *
 * @method
 *
 * Returns the unique ID for the current login session.
 *
 * @name WSGetSession
 * @label WS Get Session
 * @link http://wiki.processmaker.com/index.php/ProcessMaker_Functions#WSGetSession.28.29
 *
 * @return string | $userId | Sesion ID | The unique ID for the current active session.
 *
 */
function WSGetSession ()
{
    if (isset( $_SESSION["WS_SESSION_ID"] )) {
        return $_SESSION["WS_SESSION_ID"];
    } else {
        throw new Exception( "SW session is not open!" );
    }
}

/**
 *
 * @method
 *
 * Delete a specified case.
 *
 * @name WSDeleteCase
 * @label WS Delete Case
 * @link http://wiki.processmaker.com/index.php/ProcessMaker_Functions#WSDeleteCase.28.29
 *
 * @param string(32) | $caseUid | ID of the case | The unique ID of the case.
 * @return array | $response | WS array | A WS Response associative array.
 *
 */
function WSDeleteCase ($caseUid)
{
    $client = WSOpen();

    $sessionId = $_SESSION["WS_SESSION_ID"];

    $params = array ("sessionId" => $sessionId,"caseUid" => $caseUid
    );

    $result = $client->__soapCall( "deleteCase", array ($params
    ) );

    $response = array ();
    $response["status_code"] = $result->status_code;
    $response["message"] = $result->message;
    $response["time_stamp"] = $result->timestamp;

    return $response;
}

/**
 *
 * @method
 *
 * Cancel a specified case.
 *
 * @name WSCancelCase
 * @label WS Cancel Case
 * @link http://wiki.processmaker.com/index.php/ProcessMaker_Functions#WSCancelCase.28.29
 *
 * @param string(32) | $caseUid | ID of the case | The unique ID of the case.
 * @param int | $delIndex | Delegation index of the case | The delegation index of the current task in the case.
 * @param string(32) | $userUid | ID user | The unique ID of the user who will cancel the case.
 * @return array | $response | WS array | A WS Response associative array.
 *
 */
function WSCancelCase ($caseUid, $delIndex, $userUid)
{
    $client = WSOpen();

    $sessionId = $_SESSION["WS_SESSION_ID"];

    $params = array ("sessionId" => $sessionId,"caseUid" => $caseUid,"delIndex" => $delIndex,"userUid" => $userUid
    );

    $result = $client->__soapCall( "cancelCase", array ($params
    ) );

    $response = array ();
    $response["status_code"] = $result->status_code;
    $response["message"] = $result->message;
    $response["time_stamp"] = $result->timestamp;

    return $response;
}

/**
 *
 * @method
 *
 * Pauses a specified case.
 *
 * @name WSPauseCase
 * @label WS Pause Case
 * @link http://wiki.processmaker.com/index.php/ProcessMaker_Functions#WSPauseCase.28.29
 *
 * @param string(32) | $caseUid | ID of the case | The unique ID of the case.
 * @param int | $delIndex | Delegation index of the case | The delegation index of the current task in the case.
 * @param string(32) | $userUid | ID user | The unique ID of the user who will pause the case.
 * @param string(32) | $unpauseDate=null | Date | Optional parameter. The date in the format "yyyy-mm-dd" indicating when to unpause the case.
 * @return array | $response | WS array | A WS Response associative array.
 *
 */
function WSPauseCase ($caseUid, $delIndex, $userUid, $unpauseDate = null)
{
    $client = WSOpen();

    $sessionId = $_SESSION["WS_SESSION_ID"];

    $params = array ("sessionId" => $sessionId,"caseUid" => $caseUid,"delIndex" => $delIndex,"userUid" => $userUid,"unpauseDate" => $unpauseDate
    );

    $result = $client->__soapCall( "pauseCase", array ($params
    ) );

    $response = array ();
    $response["status_code"] = $result->status_code;
    $response["message"] = $result->message;
    $response["time_stamp"] = $result->timestamp;

    return $response;
}

/**
 *
 * @method
 *
 * Unpause a specified case.
 *
 * @name WSUnpauseCase
 * @label WS Unpause Case
 * @link http://wiki.processmaker.com/index.php/ProcessMaker_Functions#WSUnpauseCase.28.29
 *
 * @param string(32) | $caseUid | ID of the case | The unique ID of the case.
 * @param int | $delIndex | Delegation index of the case | The delegation index of the current task in the case.
 * @param string(32) | $userUid | ID user | The unique ID of the user who will unpause the case.
 * @return array | $response | WS array | A WS Response associative array.
 *
 */
function WSUnpauseCase ($caseUid, $delIndex, $userUid)
{
    $client = WSOpen();

    $sessionId = $_SESSION["WS_SESSION_ID"];

    $params = array ("sessionId" => $sessionId,"caseUid" => $caseUid,"delIndex" => $delIndex,"userUid" => $userUid
    );

    $result = $client->__soapCall( "unpauseCase", array ($params
    ) );

    $response = array ();
    $response["status_code"] = $result->status_code;
    $response["message"] = $result->message;
    $response["time_stamp"] = $result->timestamp;

    return $response;
}

/**
 *
 * @method
 *
 * Add a case note.
 *
 * @name WSAddCaseNote
 * @label WS Add a case note
 * @link http://wiki.processmaker.com/index.php/ProcessMaker_Functions#WSAddCaseNote.28.29
 *
 * @param string(32) | $caseUid | ID of the case | The unique ID of the case.
 * @param string(32) | $processUid | ID of the process | The unique ID of the process.
 * @param string(32) | $taskUid | ID of the task | The unique ID of the task.
 * @param string(32) | $userUid | ID user | The unique ID of the user who will add note case.
 * @param string | $note | Note of the case | Note of the case.
 * @param int | $sendMail = 1 | Send mail | Optional parameter. If set to 1, will send an email to all participants in the case.
 * @return array | $response | WS array | A WS Response associative array.
 *
 */
function WSAddCaseNote($caseUid, $processUid, $taskUid, $userUid, $note, $sendMail = 1)
{
    $client = WSOpen();

    $sessionId = $_SESSION["WS_SESSION_ID"];

    $params = array(
        "sessionId"  => $sessionId,
        "caseUid"    => $caseUid,
        "processUid" => $processUid,
        "taskUid"    => $taskUid,
        "userUid"    => $userUid,
        "note"       => $note,
        "sendMail"   => $sendMail
    );

    $result = $client->__soapCall("addCaseNote", array($params));

    $response = array();
    $response["status_code"] = $result->status_code;
    $response["message"]     = $result->message;
    $response["time_stamp"]  = $result->timestamp;

    return $response;
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


/**
 * Local Services Functions *
 */

/**
 *
 * @method
 *
 * Returns all the tasks for the specified case which have open delegations.
 *
 * @name PMFTaskCase
 * @label PMF Task Case
 * @link http://wiki.processmaker.com/index.php/ProcessMaker_Functions#PMFTaskCase.28.29
 *
 * @param string(32) | $caseId | Case ID | The unique ID for a case.
 * @return array | $rows | List of tasks | A list of tasks
 *
 */
function PMFTaskCase ($caseId) //its test was successfull
{
    $ws = new WsBase();
    $result = $ws->taskCase( $caseId );
    $rows = Array ();
    $i = 1;
    if (isset( $result )) {
        foreach ($result as $item) {
            $rows[$i ++] = $item;
        }
    }
    return $rows;
}

/**
 *
 * @method
 *
 * Returns a list of tasks which the specified user has initiated.
 *
 * @name PMFTaskList
 * @label PMF Task List
 * @link http://wiki.processmaker.com/index.php/ProcessMaker_Functions#PMFTaskList.28.29
 *
 * @param string(32) | $userid | User ID | The unique ID of a user.
 * @return array | $rows | List of tasks | An array of tasks
 *
 */
function PMFTaskList ($userId) //its test was successfull
{
    $ws = new WsBase();
    $result = $ws->taskList( $userId );
    $rows = Array ();
    $i = 1;
    if (isset( $result )) {
        foreach ($result as $item) {
            $rows[$i ++] = $item;
        }
    }
    return $rows;
}

/**
 *
 * @method
 *
 * Returns a list of users whose status is set to "ACTIVE" for the current workspace.
 *
 * @name PMFUserList
 * @label PMF User List
 * @link http://wiki.processmaker.com/index.php/ProcessMaker_Functions#PMFUserList.28.29
 *
 * @return array | $rows | List of users | An array of users
 *
 */
function PMFUserList () //its test was successfull
{
    $ws = new WsBase();
    $result = $ws->userList();
    $rows = Array ();
    $i = 1;
    if (isset( $result )) {
        foreach ($result as $item) {
            $rows[$i ++] = $item;
        }
    }
    return $rows;
}

/**
 * @method
 *
 * Add an Input Document.
 *
 * @name PMFAddInputDocument
 * @label PMF Add an input document
 * @link http://wiki.processmaker.com/index.php/ProcessMaker_Functions#PMFAddInputDocument.28.29
 *
 * @param string(32) | $inputDocumentUid | ID of the input document | The unique ID of the input document.
 * @param string(32) | $appDocUid | ID of the application document | The unique ID of the application document; if action is set to null or empty (Add), then this parameter it set to null.
 * @param int | $docVersion | Document version | Document version.
 * @param string | $appDocType = "INPUT" | Document type | Document type.
 * @param string | $appDocComment | Document comment | Document comment.
 * @param string | $inputDocumentAction | Action | Action, posible values: null or empty (Add), "R" (Replace), "NV" (New Version).
 * @param string(32) | $caseUid | ID of the case | The unique ID of the case.
 * @param int | $delIndex | Delegation index of the case | The delegation index of the current task in the case.
 * @param string(32) | $taskUid | ID of the task | The unique ID of the task.
 * @param string(32) | $userUid | ID user | The unique ID of the user who will add an input document.
 * @param string | $option = "file" | Option | Option, value: "file".
 * @param string | $file = "path_to_file/myfile.txt" | File, path to file | File, path to file.
 * @return string | $appDocUid | ID of the application document | Returns ID if it has added the input document successfully; otherwise, returns null or empty if an error occurred.
 *
 */
function PMFAddInputDocument(
    $inputDocumentUid,
    $appDocUid,
    $docVersion,
    $appDocType = "INPUT",
    $appDocComment,
    $inputDocumentAction,
    $caseUid,
    $delIndex,
    $taskUid,
    $userUid,
    $option = "file",
    $file = "path_to_file/myfile.txt"
) {

    $g = new G();

    $g->sessionVarSave();

    $_SESSION["APPLICATION"] = $caseUid;
    $_SESSION["INDEX"] = $delIndex;
    $_SESSION["TASK"] = $taskUid;
    $_SESSION["USER_LOGGED"] = $userUid;

    $case = new Cases();

    $appDocUid = $case->addInputDocument(
        $inputDocumentUid,
        $appDocUid,
        $docVersion,
        $appDocType,
        $appDocComment,
        $inputDocumentAction,
        $caseUid,
        $delIndex,
        $taskUid,
        $userUid,
        $option,
        $file
    );

    $g->sessionVarRestore();

    return $appDocUid;
}

/**
 *
 * @method
 *
 * Generates an Output Document
 *
 * @name PMFGenerateOutputDocument
 * @label PMF Generate Output Document
 *
 * @param string(32) | $outputID | Output ID | Output Document ID
 * @param string(32) | $sApplication = null | Case ID | The unique ID for a case
 * @param string(32) | $index = null | Index | Value for Index
 * @param string(32) | $sUserLogged = null | User UID | User Logged UID
 * @return none | $none | None | None
 *
 */
function PMFGenerateOutputDocument ($outputID, $sApplication = null, $index = null, $sUserLogged = null)
{
    $g = new G();

    $g->sessionVarSave();

    if ($sApplication) {
        $_SESSION["APPLICATION"] = $sApplication;
    } else {
        $sApplication = $_SESSION["APPLICATION"];
    }

    if ($index) {
        $_SESSION["INDEX"] = $index;
    } else {
        $index = $_SESSION["INDEX"];
    }

    if ($sUserLogged) {
        $_SESSION["USER_LOGGED"] = $sUserLogged;
    } else {
        $sUserLogged = $_SESSION["USER_LOGGED"];
    }

    $oCase = new Cases();
    $oCase->thisIsTheCurrentUser( $sApplication, $index, $sUserLogged, '', 'casesListExtJs' );

    //require_once 'classes/model/OutputDocument.php';
    $oOutputDocument = new OutputDocument();
    $aOD = $oOutputDocument->load( $outputID );
    $Fields = $oCase->loadCase( $sApplication );
    //The $_GET['UID'] variable is used when a process executes.
    //$_GET['UID']=($aOD['OUT_DOC_VERSIONING'])?$_GET['UID']:$aOD['OUT_DOC_UID'];
    //$sUID = ($aOD['OUT_DOC_VERSIONING'])?$_GET['UID']:$aOD['OUT_DOC_UID'];
    $sFilename = preg_replace( '[^A-Za-z0-9_]', '_', G::replaceDataField( $aOD['OUT_DOC_FILENAME'], $Fields['APP_DATA'] ) );
    require_once 'classes/model/AppFolder.php';
    require_once 'classes/model/AppDocument.php';

    //Get the Custom Folder ID (create if necessary)
    $oFolder = new AppFolder();
    //$aOD['OUT_DOC_DESTINATION_PATH'] = ($aOD['OUT_DOC_DESTINATION_PATH']=='')?PATH_DOCUMENT
    //      . $_SESSION['APPLICATION'] . PATH_SEP . 'outdocs'. PATH_SEP:$aOD['OUT_DOC_DESTINATION_PATH'];
    $folderId = $oFolder->createFromPath( $aOD['OUT_DOC_DESTINATION_PATH'], $sApplication );
    //Tags
    $fileTags = $oFolder->parseTags( $aOD['OUT_DOC_TAGS'], $sApplication );

    //Get last Document Version and apply versioning if is enabled
    $oAppDocument = new AppDocument();
    $lastDocVersion = $oAppDocument->getLastDocVersion( $outputID, $sApplication );

    $oCriteria = new Criteria( 'workflow' );
    $oCriteria->add( AppDocumentPeer::APP_UID, $sApplication );
    //$oCriteria->add(AppDocumentPeer::DEL_INDEX,    $index);
    $oCriteria->add( AppDocumentPeer::DOC_UID, $outputID );
    $oCriteria->add( AppDocumentPeer::DOC_VERSION, $lastDocVersion );
    $oCriteria->add( AppDocumentPeer::APP_DOC_TYPE, 'OUTPUT' );
    $oDataset = AppDocumentPeer::doSelectRS( $oCriteria );
    $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
    $oDataset->next();

    if (($aOD['OUT_DOC_VERSIONING']) && ($lastDocVersion != 0)) {
        //Create new Version of current output
        $lastDocVersion ++;
        if ($aRow = $oDataset->getRow()) {
            $aFields = array ('APP_DOC_UID' => $aRow['APP_DOC_UID'],'APP_UID' => $sApplication,'DEL_INDEX' => $index,'DOC_UID' => $outputID,'DOC_VERSION' => $lastDocVersion + 1,'USR_UID' => $sUserLogged,'APP_DOC_TYPE' => 'OUTPUT','APP_DOC_CREATE_DATE' => date( 'Y-m-d H:i:s' ),'APP_DOC_FILENAME' => $sFilename,'FOLDER_UID' => $folderId,'APP_DOC_TAGS' => $fileTags
            );
            $oAppDocument = new AppDocument();
            $oAppDocument->create( $aFields );
            $sDocUID = $aRow['APP_DOC_UID'];
        }
    } else {
        ////No versioning so Update a current Output or Create new if no exist
        if ($aRow = $oDataset->getRow()) {
            //Update
            $aFields = array ('APP_DOC_UID' => $aRow['APP_DOC_UID'],'APP_UID' => $sApplication,'DEL_INDEX' => $index,'DOC_UID' => $outputID,'DOC_VERSION' => $lastDocVersion,'USR_UID' => $sUserLogged,'APP_DOC_TYPE' => 'OUTPUT','APP_DOC_CREATE_DATE' => date( 'Y-m-d H:i:s' ),'APP_DOC_FILENAME' => $sFilename,'FOLDER_UID' => $folderId,'APP_DOC_TAGS' => $fileTags
            );
            $oAppDocument = new AppDocument();
            $oAppDocument->update( $aFields );
            $sDocUID = $aRow['APP_DOC_UID'];
        } else {
            //we are creating the appdocument row
            //create
            if ($lastDocVersion == 0) {
                $lastDocVersion ++;
            }
            $aFields = array ('APP_UID' => $sApplication,'DEL_INDEX' => $index,'DOC_UID' => $outputID,'DOC_VERSION' => $lastDocVersion,'USR_UID' => $sUserLogged,'APP_DOC_TYPE' => 'OUTPUT','APP_DOC_CREATE_DATE' => date( 'Y-m-d H:i:s' ),'APP_DOC_FILENAME' => $sFilename,'FOLDER_UID' => $folderId,'APP_DOC_TAGS' => $fileTags
            );
            $oAppDocument = new AppDocument();
            $aFields['APP_DOC_UID'] = $sDocUID = $oAppDocument->create( $aFields );
        }
    }
    $sFilename = $aFields['APP_DOC_UID'] . "_" . $lastDocVersion;

    $pathOutput = PATH_DOCUMENT . G::getPathFromUID($sApplication) . PATH_SEP . 'outdocs' . PATH_SEP; //G::pr($sFilename);die;
    G::mk_dir( $pathOutput );

    $aProperties = array ();

    if (! isset( $aOD['OUT_DOC_MEDIA'] )) {
        $aOD['OUT_DOC_MEDIA'] = 'Letter';
    }
    if (! isset( $aOD['OUT_DOC_LEFT_MARGIN'] )) {
        $aOD['OUT_DOC_LEFT_MARGIN'] = '15';
    }
    if (! isset( $aOD['OUT_DOC_RIGHT_MARGIN'] )) {
        $aOD['OUT_DOC_RIGHT_MARGIN'] = '15';
    }
    if (! isset( $aOD['OUT_DOC_TOP_MARGIN'] )) {
        $aOD['OUT_DOC_TOP_MARGIN'] = '15';
    }
    if (! isset( $aOD['OUT_DOC_BOTTOM_MARGIN'] )) {
        $aOD['OUT_DOC_BOTTOM_MARGIN'] = '15';
    }

    $aProperties['media'] = $aOD['OUT_DOC_MEDIA'];
    $aProperties['margins'] = array ('left' => $aOD['OUT_DOC_LEFT_MARGIN'],'right' => $aOD['OUT_DOC_RIGHT_MARGIN'],'top' => $aOD['OUT_DOC_TOP_MARGIN'],'bottom' => $aOD['OUT_DOC_BOTTOM_MARGIN']
    );
    if ($aOD['OUT_DOC_PDF_SECURITY_ENABLED'] == '1') {
        $aProperties['pdfSecurity'] = array('openPassword' => $aOD['OUT_DOC_PDF_SECURITY_OPEN_PASSWORD'], 'ownerPassword' => $aOD['OUT_DOC_PDF_SECURITY_OWNER_PASSWORD'], 'permissions' => $aOD['OUT_DOC_PDF_SECURITY_PERMISSIONS']);
    }
    if (isset($aOD['OUT_DOC_REPORT_GENERATOR'])) {
        $aProperties['report_generator'] = $aOD['OUT_DOC_REPORT_GENERATOR'];
    }
    $oOutputDocument->generate( $outputID, $Fields['APP_DATA'], $pathOutput, $sFilename, $aOD['OUT_DOC_TEMPLATE'], (boolean) $aOD['OUT_DOC_LANDSCAPE'], $aOD['OUT_DOC_GENERATE'], $aProperties );

    //Plugin Hook PM_UPLOAD_DOCUMENT for upload document

    $oPluginRegistry = PluginRegistry::loadSingleton();
    if ($oPluginRegistry->existsTrigger( PM_UPLOAD_DOCUMENT ) && class_exists( 'uploadDocumentData' )) {
        /** @var \ProcessMaker\Plugins\Interfaces\TriggerDetail $triggerDetail */
        $triggerDetail = $oPluginRegistry->getTriggerInfo( PM_UPLOAD_DOCUMENT );
        $aFields['APP_DOC_PLUGIN'] = $triggerDetail->getNamespace();

        $oAppDocument1 = new AppDocument();
        $oAppDocument1->update( $aFields );

        $sPathName = PATH_DOCUMENT . G::getPathFromUID($sApplication) . PATH_SEP;

        $oData['APP_UID'] = $sApplication;
        $oData['ATTACHMENT_FOLDER'] = true;
        switch ($aOD['OUT_DOC_GENERATE']) {
            case "BOTH":
                $documentData = new uploadDocumentData( $sApplication, $sUserLogged, $pathOutput . $sFilename . '.pdf', $sFilename . '.pdf', $sDocUID, $oAppDocument->getDocVersion() );

                $documentData->sFileType = "PDF";
                $documentData->bUseOutputFolder = true;
                $uploadReturn = $oPluginRegistry->executeTriggers( PM_UPLOAD_DOCUMENT, $documentData );
                if ($uploadReturn) {
                    //Only delete if the file was saved correctly
                    unlink( $pathOutput . $sFilename . '.pdf' );
                }

                $documentData = new uploadDocumentData( $sApplication, $sUserLogged, $pathOutput . $sFilename . '.doc', $sFilename . '.doc', $sDocUID, $oAppDocument->getDocVersion() );

                $documentData->sFileType = "DOC";
                $documentData->bUseOutputFolder = true;
                $uploadReturn = $oPluginRegistry->executeTriggers( PM_UPLOAD_DOCUMENT, $documentData );
                if ($uploadReturn) {
                    //Only delete if the file was saved correctly
                    unlink( $pathOutput . $sFilename . '.doc' );
                }

                break;
            case "PDF":
                $documentData = new uploadDocumentData( $sApplication, $sUserLogged, $pathOutput . $sFilename . '.pdf', $sFilename . '.pdf', $sDocUID, $oAppDocument->getDocVersion() );

                $documentData->sFileType = "PDF";
                $documentData->bUseOutputFolder = true;
                $uploadReturn = $oPluginRegistry->executeTriggers( PM_UPLOAD_DOCUMENT, $documentData );
                if ($uploadReturn) {
                    //Only delete if the file was saved correctly
                    unlink( $pathOutput . $sFilename . '.pdf' );
                }
                break;
            case "DOC":
                $documentData = new uploadDocumentData( $sApplication, $sUserLogged, $pathOutput . $sFilename . '.doc', $sFilename . '.doc', $sDocUID, $oAppDocument->getDocVersion() );

                $documentData->sFileType = "DOC";
                $documentData->bUseOutputFolder = true;
                $uploadReturn = $oPluginRegistry->executeTriggers( PM_UPLOAD_DOCUMENT, $documentData );
                if ($uploadReturn) {
                    //Only delete if the file was saved correctly
                    unlink( $pathOutput . $sFilename . '.doc' );
                }
                break;
        }
    }

    $g->sessionVarRestore();
}

/**
 *
 * @method
 *
 * Returns a list of groups from the current workspace
 *
 * @name PMFGroupList
 * @label PMF Group List
 * @link http://wiki.processmaker.com/index.php/ProcessMaker_Functions#PMFGroupList.28.29
 *
 * @param string | $regex = null | String to search | Optional parameter.
 * @param int | $start = null | Start | Optional parameter.
 * @param int | $limit = null | Limit | Optional parameter.
 * @return array | $rows | List of groups | An array of groups
 *
 */
function PMFGroupList ($regex = null, $start = null, $limit = null) //its test was successfull
{
    $ws = new WsBase();
    $result = $ws->groupList($regex, $start, $limit);
    $rows = array();
    if ($result) {
        $rows = array_combine(range(1, count($result)), array_values($result));
    }
    return $rows;
}

/**
 *
 * @method
 *
 * Returns a list of roles whose status is "ACTIVE" for the current workspace.
 *
 * @name PMFRoleList
 * @label PMF Role List
 * @link http://wiki.processmaker.com/index.php/ProcessMaker_Functions#PMFRoleList.28.29
 *
 * @return array | $rows | List of roles | This function returns an array of roles
 *
 */
function PMFRoleList () //its test was successfull
{
    $ws = new WsBase();
    $result = $ws->roleList();
    $rows = Array ();
    $i = 1;
    if (isset( $result )) {
        foreach ($result as $item) {
            $rows[$i ++] = $item;
        }
    }
    return $rows;
}

/**
 *
 * @method Returns a list of the pending cases for a specified user
 *
 * returns a list of the pending cases for a specified user. Note that the specified user must be designated to work on the current task for these cases.
 *
 * @name PMFCaseList
 * @label PMF Case List
 * @link http://wiki.processmaker.com/index.php/ProcessMaker_Functions#PMFCaseList.28.29
 *
 * @param string(32) | $userId | User ID | The unique ID of a user who is assigned to work on the cases.
 * @return array | $rows | List of cases | A list of cases
 *
 */
function PMFCaseList ($userId) //its test was successfull
{
    $ws = new WsBase();
    $result = $ws->caseList( $userId );
    $rows = Array ();
    $i = 1;
    if (isset( $result )) {
        foreach ($result as $item) {
            $rows[$i ++] = $item;
        }
    }
    return $rows;
}

/**
 *
 * @method
 *
 * Returns a list of processes for the current workspace
 *
 * @name PMFProcessList
 * @label PMF Process List
 * @link http://wiki.processmaker.com/index.php/ProcessMaker_Functions#PMFProcessList.28.29
 *
 * @return array | $rows | Lis ot Processes | An array of tasks in the indicated case which have open delegations
 *
 */
function PMFProcessList () //its test was successfull
{
    $ws = new WsBase();
    $result = $ws->processList();
    $rows = Array ();
    $i = 1;
    if (isset( $result )) {
        foreach ($result as $item) {
            $rows[$i ++] = $item;
        }
    }
    return $rows;
}

/**
 *
 * @method
 *
 * Sends an array of case variables to a specified case.
 *
 * @name PMFSendVariables
 * @label PMF Send Variables
 * @link http://wiki.processmaker.com/index.php/ProcessMaker_Functions#PMFSendVariables.28.29
 *
 * @param string(32) | $caseId | Case ID | The unique ID of the case to receive the variable.
 * @param array | $variables | Array of variables | An associative array to hold the case variables to send to the case.
 * @return int | $result | Result of send variables | Returns 1 if the variables were sent successfully to the case; otherwise, returns 0 if an error occurred.
 *
 */
function PMFSendVariables ($caseId, $variables)
{
    $ws = new WsBase();

    $result = $ws->sendVariables( $caseId, $variables );
    if ($result->status_code == 0) {
        if (isset($_SESSION['APPLICATION'])) {
            if ($caseId == $_SESSION['APPLICATION']) {
                global $oPMScript;
                if (isset($oPMScript->aFields) && is_array($oPMScript->aFields)) {
                    if (is_array($variables)) {
                        $oPMScript->aFields = array_merge($oPMScript->aFields, $variables);
                    }
                }
            }
        }
        return 1;
    } else {
        return 0;
    }
}

/**
 *
 * @method
 *
 * Derivates (routes) a case to the next task in the process.
 *
 * @name PMFDerivateCase
 * @label PMF Derivate Case
 * @link http://wiki.processmaker.com/index.php/ProcessMaker_Functions#PMFDerivateCase.28.29
 *
 * @param string(32) | $caseId | Case ID | The unique ID for the case to be derivated (routed)
 * @param int | $delIndex | delegation index for the case | The delegation index for the case to derivated (routed).
 * @param boolean | $bExecuteTriggersBeforeAssignment = false | Trigger | Optional parameter. If set to true, any triggers which are assigned to pending steps in the current task will be executed before the case is assigned to the next user.
 * @param boolean | $sUserLogged = null | User ID | Optional parameter. The unique ID of the user who will route the case. This should be set to the user who is currently designated to work on the case. If omitted or set to NULL, then the currently logged-in user will route the case.
 * @return int | $result | Result of Derivate case | Returns 1 if new case was derivated (routed) successfully; otherwise, returns 0 if an error occurred.
 *
 */
function PMFDerivateCase ($caseId, $delIndex, $bExecuteTriggersBeforeAssignment = false, $sUserLogged = null)
{
    if (! $sUserLogged) {
        $sUserLogged = $_SESSION['USER_LOGGED'];
    }

    $ws = new WsBase();
    $result = $ws->derivateCase( $sUserLogged, $caseId, $delIndex, $bExecuteTriggersBeforeAssignment );
    if (is_array($result)) {
        $result = (object)$result;
    }
    if ($result->status_code == 0) {
        return 1;
    } else {
        return 0;
    }
}

/**
 *
 * @method
 *
 * Creates a new case with a user who can impersonate a user with the proper
 * privileges.
 *
 * @name PMFNewCaseImpersonate
 * @label PMF New Case Impersonate
 * @link http://wiki.processmaker.com/index.php/ProcessMaker_Functions#PMFNewCaseImpersonate.28.29
 *
 * @param string(32) | $processId | Process ID | The unique ID of the process.
 * @param string(32) | $userId | User ID | The unique ID of the user.
 * @param array | $variables | Array of variables | An associative array of the variables which will be sent to the case.
 * @param string(32) | $taskId | The unique ID of the task taha is in the starting group.
 * @return int | $result | Result | Returns 1 if new case was created successfully; otherwise, returns 0 if an error occurred.
 *
 */
function PMFNewCaseImpersonate ($processId, $userId, $variables, $taskId = '')
{
    $ws = new WsBase();
    $result = $ws->newCaseImpersonate( $processId, $userId, $variables, $taskId);

    if ($result->status_code == 0) {
        return $result->caseId;
    } else {
        return 0;
    }
}

/**
 *
 * @method
 *
 * Creates a new case starting with the specified task
 *
 * @name PMFNewCase
 * @label PMF New Case
 * @link http://wiki.processmaker.com/index.php/ProcessMaker_Functions#PMFNewCase.28.29
 *
 * @param string(32) | $processId | Process ID | The unique ID of the process.
 * @param string(32) | $userId | User ID | The unique ID of the user.
 * @param string(32) | $taskId | Task ID | The unique ID of the task.
 * @param array | $variables | Array of variables | An associative array of the variables which will be sent to the case.
 * @param string(32) | $status=null | Status | Status of the case DRAFT or TO_DO.
 * @return string | $idNewCase | Case ID | If an error occured, it returns the integer zero. Otherwise, it returns a string with the case UID of the new case.
 *
 */
function PMFNewCase ($processId, $userId, $taskId, $variables, $status = null)
{
    $ws = new WsBase();

    $result = $ws->newCase($processId, $userId, $taskId, $variables, 0, $status);

    if ($result->status_code == 0) {
        return $result->caseId;
    } else {
        return 0;
    }
}

/**
 *
 * @method
 *
 *
 *
 * Assigns a user to a group. Note that the logged-in user must have the PM_USERS permission in his/her role to be able to assign a user to a group.
 *
 * @name PMFAssignUserToGroup
 * @label PMF Assign User To Group
 * @link http://wiki.processmaker.com/index.php/ProcessMaker_Functions#PMFNewCase.28.29
 *
 * @param string(32) | $userId | User ID | The unique ID of the user.
 * @param string(32) | $groupId | Group ID | The unique ID of the group.
 * @return int | $result | Result of the assignment | Returns 1 if the user was successfully assigned to the group; otherwise, returns 0.
 *
 */
function PMFAssignUserToGroup ($userId, $groupId)
{
    $ws = new WsBase();
    $result = $ws->assignUserToGroup( $userId, $groupId );

    if ($result->status_code == 0) {
        return 1;
    } else {
        return 0;
    }
}

/**
 *
 * @method
 *
 * Creates a new user with the given data.
 *
 * @name PMFCreateUser
 * @label PMF Create User
 * @link http://wiki.processmaker.com/index.php/ProcessMaker_Functions#PMFCreateUser.28.29
 *
 * @param string(32) | $userId | User Name | The username for the new user.
 * @param string(32) | $password | Password of the new user | The password of the new user, which can be up to 32 characters long.
 * @param string(32) | $firstname | Firstname of the new user | The first name of the user, which can be up to 50 characters long.
 * @param string(32) | $lastname | Lastname of the new user | The last name of the user, which can be up to 50 characters long.
 * @param string(32) | $email | Email the new user | The email of the new user, which can be up to 100 characters long.
 * @param string(32) | $role | Rol of the new user | The role of the new user such as "PROCESSMAKER_ADMIN" or "PROCESSMAKER_OPERATOR".
 * @param string(32) | $dueDate=null | Expiration date | Optional parameter. The expiration date must be a string in the format "yyyy-mm-dd".
 * @param string(32) | $status=null | Status of the new user | Optional parameter. The user's status, such as "ACTIVE", "INACTIVE" or "VACATION".
 * @return int | $result | Result of the creation | Returns 1 if the new user was created successfully; otherwise, returns 0 if an error occurred.
 *
 */
function PMFCreateUser ($userId, $password, $firstname, $lastname, $email, $role, $dueDate = null, $status = null)
{
    $ws = new WsBase();
    $result = $ws->createUser( $userId, $firstname, $lastname, $email, $role, $password, $dueDate, $status );

    //When the user is created the $result parameter is an array, in other case is a object exception
    if (!is_object($result)) {
        $result = (object)$result;
    }

    if ($result->status_code == 0) {
        return 1;
    } else {
        return 0;
    }
}

/**
 *
 * @method
 *
 * Update a user with the given data.
 *
 * @name PMFUpdateUser
 * @label PMF Update User
 * @link http://wiki.processmaker.com/index.php/ProcessMaker_Functions#PMFUpdateUser.28.29
 *
 * @param string(32) | $userUid | User UID | The user UID.
 * @param string(32) | $userName | Username | The username for the user.
 * @param string(32) | $firstName=null | Firstname of the user | Optional parameter. The first name of the user, which can be up to 50 characters long.
 * @param string(32) | $lastName=null | Lastname of the user | Optional parameter. The last name of the user, which can be up to 50 characters long.
 * @param string(32) | $email=null | Email the user | Optional parameter. The email of the user, which can be up to 100 characters long.
 * @param string(32) | $dueDate=null | Expiration date | Optional parameter. The expiration date must be a string in the format "yyyy-mm-dd".
 * @param string(32) | $status=null | Status of the user | Optional parameter. The user's status, such as "ACTIVE", "INACTIVE" or "VACATION".
 * @param string(32) | $role=null | Rol of the user | The role of the user such as "PROCESSMAKER_ADMIN" or "PROCESSMAKER_OPERATOR".
 * @param string(32) | $password=null | Password of the user | The password of the user, which can be up to 32 characters long.
 * @return int | $result | Result of the update | Returns 1 if the user is updated successfully; otherwise, returns 0 if an error occurred.
 *
 */
function PMFUpdateUser ($userUid, $userName, $firstName = null, $lastName = null, $email = null, $dueDate = null, $status = null, $role = null, $password = null)
{
    $ws = new WsBase();
    $result = $ws->updateUser( $userUid, $userName, $firstName, $lastName, $email, $dueDate, $status, $role, $password );

    if ($result->status_code == 0) {
        return 1;
    } else {
        return 0;
    }
}

/**
 *
 * @method
 *
 * Retrieves information about a user with a given ID.
 *
 * @name PMFInformationUser
 * @label PMF Information User
 * @link http://wiki.processmaker.com/index.php/ProcessMaker_Functions#PMFInformationUser.28.29
 *
 * @param string(32) | $userUid | User UID | The user UID.
 * @return array | $info | Information of user | An associative array with Information.
 *
 */
function PMFInformationUser($userUid)
{
    $ws = new WsBase();
    $result = $ws->informationUser($userUid);

    $info = array();

    if ($result->status_code == 0 && isset($result->info)) {
        $info = $result->info;
    }

    return $info;
}

/**
 *
 * @method
 *
 * Creates a random string of letters and/or numbers of a specified length,which
 * can be used as the PINs (public identification numbers) and codes for cases.
 *
 * @name generateCode
 * @label generate Code
 * @link http://wiki.processmaker.com/index.php/ProcessMaker_Functions#generateCode.28.29
 *
 * @param int | $iDigits = 4 | Number of characters | The number of characters to be generated.
 * @param string(32) | $sType="NUMERIC" | Type of characters | The type of of characters to be generated
 * @return string | $generateString | Generated string | The generated string of random characters.
 *
 */
function generateCode ($iDigits = 4, $sType = 'NUMERIC')
{
    return G::generateCode( $iDigits, $sType );
}

/**
 *
 * @method
 *
 * Sets the code and PIN for a case.
 *
 * @name setCaseTrackerCode
 * @label set Case Tracker Code
 * @link http://wiki.processmaker.com/index.php/ProcessMaker_Functions#setCaseTrackerCode.28.29
 *
 * @param string(32) | $sApplicationUID | Case ID | The unique ID for a case (which can be found with WSCaseList()
 * @param string(32) | $sCode | New Code for case | The new code for a case, which will be stored in the field wf_<WORKSPACE>.APPLICATION.APP_CODE
 * @param string(32) | $sPIN = "" | New Code PIN for case |The new code for a case.
 * @return int | $result | Result | If successful, returns one, otherwise zero or error number.
 *
 */
function setCaseTrackerCode ($sApplicationUID, $sCode, $sPIN = '')
{
    if ($sCode != '' || $sPIN != '') {
        $oCase = new Cases();
        $aFields = $oCase->loadCase( $sApplicationUID );
        $aFields['APP_PROC_CODE'] = $sCode;
        if ($sPIN != '') {
            $aFields['APP_DATA']['PIN'] = $sPIN;
            $aFields['APP_PIN'] = G::encryptOld( $sPIN );
        }
        $oCase->updateCase( $sApplicationUID, $aFields );
        if (isset($_SESSION['APPLICATION'])) {
            if ($sApplicationUID == $_SESSION['APPLICATION']) {
                global $oPMScript;
                if (isset($oPMScript->aFields) && is_array($oPMScript->aFields)) {
                    $oPMScript->aFields['PIN'] = $aFields['APP_DATA']['PIN'];
                }
            }
        }
        return 1;
    } else {
        return 0;
    }
}

/**
 *
 * @method
 *
 * Routes (derivates) a case and then displays the case list.
 *
 * @name jumping
 * @label jumping
 * @link http://wiki.processmaker.com/index.php/ProcessMaker_Functions#jumping.28.29
 *
 * @param string(32) | $caseId | Case ID | The unique ID for the case to be routed (derivated).
 * @param int | $delIndex | delegation Index of case | The delegation index of the task to be routed (derivated). Counting starts from 1.
 * @return none | $none | None | None
 *
 */
function jumping ($caseId, $delIndex)
{
    try {
        $response = PMFDerivateCase($caseId, $delIndex);
        if ($response) {
            G::header( 'Location: casesListExtJsRedirector' );
            die(); // After routing and jumping the case, the thread execution will end
        } else {
            G::SendTemporalMessage( 'ID_NOT_DERIVATED', 'error', 'labels' );
        }
    } catch (Exception $oException) {
        G::SendTemporalMessage( 'ID_NOT_DERIVATED', 'error', 'labels' );
    }
}

/**
 *
 * @method
 *
 * Returns the label of a specified option from a dropdown box, listbox,
 * checkgroup or radiogroup.
 *
 * @name PMFgetLabelOption
 * @label PMF get Label Option
 * @link http://wiki.processmaker.com/index.php/ProcessMaker_Functions#PMFgetLabelOption.28.29
 *
 * @param string(32) | $PROCESS | Process ID | The unique ID of the process which contains the field.
 * @param string(32) | $DYNAFORM_UID | Dynaform ID | The unique ID of the DynaForm where the field is located.
 * @param string(32) | $FIELD_NAME | Fiel Name | The field name of the dropdown box, listbox, checkgroup or radiogroup from the specified DynaForm.
 * @param string(32) | $FIELD_SELECTED_ID | ID selected | The value (i.e., ID) of the option from the fieldName.
 * @return string | $label | Label of the specified option | A string holding the label of the specified option or NULL if the specified option does not exist.
 *
 */
function PMFgetLabelOption ($PROCESS, $DYNAFORM_UID, $FIELD_NAME, $FIELD_SELECTED_ID)
{
    $data = array();
    $data["CURRENT_DYNAFORM"] = $DYNAFORM_UID;
    $dynaform = new PmDynaform($data);
    if ($dynaform->isResponsive()) {
        $json = $dynaform->searchFieldByName($DYNAFORM_UID, $FIELD_NAME);
        $options = $json->options + $json->optionsSql;
        foreach ($options as $key => $value) {
            if ((string) $value->value === (string) $FIELD_SELECTED_ID) {
                return $value->label;
            }
        }
        return null;
    }

    $G_FORM = new Form( "{$PROCESS}/{$DYNAFORM_UID}", PATH_DYNAFORM, SYS_LANG, false );
    if (isset( $G_FORM->fields[$FIELD_NAME]->option[$FIELD_SELECTED_ID] )) {
        return $G_FORM->fields[$FIELD_NAME]->option[$FIELD_SELECTED_ID];
    } else {
        return null;
    }
}

/**
 *
 * @method
 *
 * Redirects a case to any step in the current task. In order for the step to
 * be executed, the specified step much exist and if it contains a condition,
 * it must evaluate to true.
 *
 * @name PMFRedirectToStep
 * @label PMF Redirect To Step
 * @link http://wiki.processmaker.com/index.php/ProcessMaker_Functions#PMFRedirectToStep.28.29
 *
 * @param string(32) | $sApplicationUID | Case ID | The unique ID for a case,
 * @param int | $iDelegation | Delegation index | The delegation index of a case.
 * @param string(32) | $sStepType | Type of Step | The type of step, which can be "DYNAFORM", "INPUT_DOCUMENT" or "OUTPUT_DOCUMENT".
 * @param string(32) | $sStepUid | Step ID | The unique ID for the step.
 * @return none | $none | None | None
 *
 */
function PMFRedirectToStep ($sApplicationUID, $iDelegation, $sStepType, $sStepUid)
{
    $g = new G();

    $g->sessionVarSave();

    $iDelegation = intval($iDelegation);

    $_SESSION["APPLICATION"] = $sApplicationUID;
    $_SESSION["INDEX"] = $iDelegation;

    require_once 'classes/model/AppDelegation.php';
    $oCriteria = new Criteria( 'workflow' );
    $oCriteria->addSelectColumn( AppDelegationPeer::TAS_UID );
    $oCriteria->add( AppDelegationPeer::APP_UID, $sApplicationUID );
    $oCriteria->add( AppDelegationPeer::DEL_INDEX, $iDelegation );
    $oDataset = AppDelegationPeer::doSelectRS( $oCriteria );
    $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
    $oDataset->next();
    global $oPMScript;
    $aRow = $oDataset->getRow();
    if ($aRow) {
        require_once 'classes/model/Step.php';
        $oStep = new Step();
        $oTheStep = $oStep->loadByType( $aRow['TAS_UID'], $sStepType, $sStepUid );
        $bContinue = true;
        $oCase = new Cases();
        $aFields = $oCase->loadCase( $sApplicationUID );
        if ($oTheStep->getStepCondition() != '') {
            $pmScript = new PMScript();
            $pmScript->setFields( $aFields['APP_DATA'] );
            $pmScript->setScript( $oTheStep->getStepCondition() );
            $bContinue = $pmScript->evaluate();
        }
        if ($bContinue) {
            switch ($oTheStep->getStepTypeObj()) {
                case 'DYNAFORM':
                    $sAction = 'EDIT';
                    break;
                case 'OUTPUT_DOCUMENT':
                    $sAction = 'GENERATE';
                    break;
                case 'INPUT_DOCUMENT':
                    $sAction = 'ATTACH';
                    break;
                case 'EXTERNAL':
                    $sAction = 'EDIT';
                    break;
                case 'MESSAGE':
                    $sAction = '';
                    break;
            }
            // save data
            if (! is_null( $oPMScript )) {
                $aFields['APP_DATA'] = $oPMScript->aFields;
                unset($aFields['APP_STATUS']);
                unset($aFields['APP_PROC_STATUS']);
                unset($aFields['APP_PROC_CODE']);
                unset($aFields['APP_PIN']);
                $oCase->updateCase( $sApplicationUID, $aFields );
            }

            $g->sessionVarRestore();

            G::header( 'Location: ' . 'cases_Step?TYPE=' . $sStepType . '&UID=' . $sStepUid . '&POSITION=' . $oTheStep->getStepPosition() . '&ACTION=' . $sAction );
            die();
        }
    }

    $g->sessionVarRestore();
}

/**
 *
 * @method
 *
 * Returns a list of the next assigned users to a case.
 *
 * @name PMFGetNextAssignedUser
 * @label PMF  Get Next Assigned User
 *
 * @param string(32) | $application | Case ID | Id of the case
 * @param string(32) | $task | Task ID | Id of the task
 * @return array | $array | List of users | Return a list of users
 *
 */
function PMFGetNextAssignedUser($application, $task, $delIndex = null, $userUid = null)
{

    require_once 'classes/model/AppDelegation.php';
    require_once 'classes/model/Task.php';
    require_once 'classes/model/TaskUser.php';
    require_once 'classes/model/Users.php';
    require_once 'classes/model/Groupwf.php';
    require_once 'classes/model/GroupUser.php';

    $oTask = new Task();
    $TaskFields = $oTask->load($task);
    $typeTask = $TaskFields['TAS_ASSIGN_TYPE'];

    $g = new G();

    $g->sessionVarSave();

    $_SESSION['INDEX'] = (!empty($delIndex) ? $delIndex : (isset($_SESSION['INDEX']) ? $_SESSION['INDEX'] : null));
    $_SESSION['USER_LOGGED'] = (!empty($userUid) ? $userUid : (isset($_SESSION['USER_LOGGED']) ? $_SESSION['USER_LOGGED']
        : null));

    if ($typeTask == 'BALANCED' && !is_null($_SESSION['INDEX']) && !is_null($_SESSION['USER_LOGGED'])) {
        $oDerivation = new Derivation();
        $aDeriv = $oDerivation->prepareInformation(array('USER_UID' => $_SESSION['USER_LOGGED'], 'APP_UID' => $application, 'DEL_INDEX' => $_SESSION['INDEX']
        ));

        foreach ($aDeriv as $derivation) {

            $aUser = array('USR_UID' => $derivation['NEXT_TASK']['USER_ASSIGNED']['USR_UID'], 'USR_USERNAME' => $derivation['NEXT_TASK']['USER_ASSIGNED']['USR_USERNAME'], 'USR_FIRSTNAME' => $derivation['NEXT_TASK']['USER_ASSIGNED']['USR_FIRSTNAME'], 'USR_LASTNAME' => $derivation['NEXT_TASK']['USER_ASSIGNED']['USR_LASTNAME'], 'USR_EMAIL' => $derivation['NEXT_TASK']['USER_ASSIGNED']['USR_EMAIL']
            );
            $aUsers[] = $aUser;
        }

        $g->sessionVarRestore();

        if (count($aUsers) == 1) {
            return $aUser;
        } else {
            return $aUsers;
        }

    } else {
        $g->sessionVarRestore();
        return false;
    }
}

/**
 * @method
 *
 * Returns the email address of the specified user.
 *
 * @name PMFGetUserEmailAddress
 * @label PMF Get User Email Address
 * @link http://wiki.processmaker.com/index.php/ProcessMaker_Functions#PMFGetUserEmailAddress.28.29
 *
 * @param string(32) or Array | $id | List of Recipients | which can be a mixture of user IDs, group IDs, variable names or email addresses
 * @param string(32) | $APP_UID = null | Application ID | Id of the Application.
 * @param string(32) | $prefix = "usr" | prefix | Id of the task.
 * @return array | $aRecipient | Array of the Recipient | Return an Array of the Recipient.
 *
 */
function PMFGetUserEmailAddress ($id, $APP_UID = null, $prefix = 'usr')
{

    if (is_string( $id ) && trim( $id ) == "") {
        return false;
    }
    if (is_array( $id ) && count( $id ) == 0) {
        return false;
    }

    //recipient to store the email addresses
    $aRecipient = Array ();
    $aItems = Array ();

    /*
    * First at all the $id user input can be by example erik@colosa.com
    * 2.this $id param can be a array by example Array('000000000001','000000000002') in this case $prefix is necessary
    * 3.this same param can be a array by example Array('usr|000000000001', 'usr|-1', 'grp|2245141479413131441')
    */

    /*
    * The second thing is that the return type will be configurated depend of the input type (using $retType)
    */
    if (is_array( $id )) {
        $aItems = $id;
        $retType = 'array';
    } else {
        $retType = 'string';
        if (strpos( $id, "," ) !== false) {
            $aItems = explode( ',', $id );
        } else {
            array_push( $aItems, $id );
        }
    }

    foreach ($aItems as $sItem) {
        //cleaning for blank spaces into each array item
        $sItem = trim( $sItem );
        if (strpos( $sItem, "|" ) !== false) {
            // explode the parameter because  always will be compose with pipe separator to indicate
            // the type (user or group) and the target mai
            list ($sType, $sID) = explode( '|', $sItem );
            $sType = trim( $sType );
            $sID = trim( $sID );
        } else {
            $sType = $prefix;
            $sID = $sItem;
        }

        switch ($sType) {
            case 'ext':
                if (G::emailAddress( $sID )) {
                    array_push( $aRecipient, $sID );
                }
                break;
            case 'usr':
                if ($sID == '-1') {
                    // -1: Curent user, load from user record
                    if (isset( $APP_UID )) {
                        $oAppDelegation = new AppDelegation();
                        $aAppDel = $oAppDelegation->getLastDeleration( $APP_UID );
                        if (isset( $aAppDel )) {
                            $oUserRow = UsersPeer::retrieveByPK( $aAppDel['USR_UID'] );
                            if (isset( $oUserRow )) {
                                $sID = $oUserRow->getUsrEmail();
                            } else {
                                throw new Exception( 'User with ID ' . $oAppDelegation->getUsrUid() . 'doesn\'t exist' );
                            }
                            if (G::emailAddress( $sID )) {
                                array_push( $aRecipient, $sID );
                            }
                        }
                    }
                } else {
                    $oUserRow = UsersPeer::retrieveByPK( $sID );
                    if ($oUserRow != null) {
                        $sID = $oUserRow->getUsrEmail();
                        if (G::emailAddress( $sID )) {
                            array_push( $aRecipient, $sID );
                        }
                    }
                }

                break;
            case 'grp':
                $oGroups = new Groups();
                $oCriteria = $oGroups->getUsersGroupCriteria( $sID );
                $oDataset = GroupwfPeer::doSelectRS( $oCriteria );
                $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
                while ($oDataset->next()) {
                    $aGroup = $oDataset->getRow();
                    //to validate email address
                    if (G::emailAddress( $aGroup['USR_EMAIL'] )) {
                        array_push( $aRecipient, $aGroup['USR_EMAIL'] );
                    }
                }

                break;
            case 'dyn':
                $oCase = new Cases();
                $aFields = $oCase->loadCase( $APP_UID );
                $aFields['APP_DATA'] = array_merge( $aFields['APP_DATA'], G::getSystemConstants() );

                //to validate email address
                if (isset( $aFields['APP_DATA'][$sID] ) && G::emailAddress( $aFields['APP_DATA'][$sID] )) {
                    array_push( $aRecipient, $aFields['APP_DATA'][$sID] );
                }
                break;
        }
    }

    switch ($retType) {
        case 'array':
            return $aRecipient;
            break;
        case 'string':
            return implode( ',', $aRecipient );
            break;
        default:
            return $aRecipient;
    }
}

/**
 * @method
 *
 * Get of the cases notes an application.
 *
 * @name PMFGetCaseNotes
 * @label PMF Get of the cases notes an application
 * @link http://wiki.processmaker.com/index.php/ProcessMaker_Functions#PMFGetCaseNotes.28.29
 *
 * @param string(32) | $applicationID | Application ID | ID of the Application.
 * @param string(32) | $type = "array" | type of the return value | type of the return value (array, object, string).
 * @param string(32) | $userUid = "" | User ID | Id of the User.
 * @return array, object or string | $response | Array of the response | Return an Array or Object or String.
 *
 */
function PMFGetCaseNotes ($applicationID, $type = 'array', $userUid = '')
{
    $response = Cases::getCaseNotes( $applicationID, $type, $userUid );
    return $response;
}

/**
 *
 * @method
 *
 * Delete a specified case.
 *
 * @name PMFDeleteCase
 * @label PMF Delete a specified case.
 * @link http://wiki.processmaker.com/index.php/ProcessMaker_Functions#PMFDeleteCase.28.29
 *
 * @param string(32) | $caseUid | ID of the case | The unique ID of the case.
 * @return int | $result | Result of the elimination | Returns 1 if the case is delete successfully; otherwise, returns 0 if an error occurred.
 *
 */
function PMFDeleteCase ($caseUid)
{
    $ws = new WsBase();
    $result = $ws->deleteCase( $caseUid );

    if ($result->status_code == 0) {
        return 1;
    } else {
        return 0;
    }
}

/**
 *
 * @method
 *
 * Cancel a specified case.
 *
 * @name PMFCancelCase
 * @label PMF Cancel a specified case.
 * @link http://wiki.processmaker.com/index.php/ProcessMaker_Functions#PMFCancelCase.28.29
 *
 * @param string(32) | $caseUid | ID of the case | The unique ID of the case.
 * @param int | $delIndex | Delegation index of the case | The delegation index of the current task in the case.
 * @param string(32) | $userUid | ID user | The unique ID of the user who will cancel the case.
 * @return int | $result | Result of the cancelation | Returns 1 if the case is cancel successfully; otherwise, returns 0 if an error occurred.
 *
 */
function PMFCancelCase ($caseUid, $delIndex, $userUid)
{
    $ws = new WsBase();
    $result = $ws->cancelCase( $caseUid, $delIndex, $userUid );

    if ($result->status_code == 0) {
        if (isset($_SESSION['APPLICATION']) && isset($_SESSION['INDEX'])) {
            if ($_SESSION['APPLICATION'] == $caseUid && $_SESSION['INDEX'] == $delIndex) {
                if (!defined('WEB_SERVICE_VERSION')) {
                    G::header('Location: ../cases/casesListExtJsRedirector');
                    die();
                } else {
                    die(G::LoadTranslation('ID_PM_FUNCTION_CHANGE_CASE', SYS_LANG, array('PMFCancelCase', G::LoadTranslation('ID_CANCELLED'))));
                }
            }
        }
        return 1;
    } else {
        return 0;
    }
}

/**
 *
 * @method
 *
 * Pauses a specified case.
 *
 * @name PMFPauseCase
 * @label PMF Pauses a specified case.
 * @link http://wiki.processmaker.com/index.php/ProcessMaker_Functions#PMFPauseCase.28.29
 *
 * @param string(32) | $caseUid | Case UID | The unique ID of the case.
 * @param int | $delIndex | Delegation index of the case | The delegation index of the current task in the case.
 * @param string(32) | $userUid | User UID | The unique ID of the user who will pause the case.
 * @param string(32) | $unpauseDate=null | Unpaused date | The date in the format "yyyy-mm-dd" indicating when to unpause the case.
 * @return int | $result | Result of the pause | Returns 1 if the case is paused successfully; otherwise, returns 0 if an error occurred.
 *
 */
function PMFPauseCase ($caseUid, $delIndex, $userUid, $unpauseDate = null)
{
    $ws = new WsBase();
    $result = $ws->pauseCase($caseUid, $delIndex, $userUid, $unpauseDate);

    if ($result->status_code == 0) {
        if (isset($_SESSION['APPLICATION']) && isset($_SESSION['INDEX'])) {
            if ($_SESSION['APPLICATION'] == $caseUid && $_SESSION['INDEX'] == $delIndex) {
                if (!defined('WEB_SERVICE_VERSION')) {
                    G::header('Location: ../cases/casesListExtJsRedirector');
                    die();
                } else {
                    die(G::LoadTranslation('ID_PM_FUNCTION_CHANGE_CASE', SYS_LANG, array('PMFPauseCase', G::LoadTranslation('ID_PAUSED'))));
                }
            }
        }
        return 1;
    } else {
        return 0;
    }
}

/**
 *
 * @method
 *
 * Unpause a specified case.
 *
 * @name PMFUnpauseCase
 * @label PMF Unpause a specified case.
 * @link http://wiki.processmaker.com/index.php/ProcessMaker_Functions#PMFUnpauseCase.28.29
 *
 * @param string(32) | $caseUid | ID of the case | The unique ID of the case.
 * @param int | $delIndex | Delegation index of the case | The delegation index of the current task in the case.
 * @param string(32) | $userUid | ID user | The unique ID of the user who will unpause the case.
 * @return int | $result | Result of the unpause | Returns 1 if the case is unpause successfully; otherwise, returns 0 if an error occurred.
 *
 */
function PMFUnpauseCase ($caseUid, $delIndex, $userUid)
{
    $ws = new WsBase();
    $result = $ws->unpauseCase( $caseUid, $delIndex, $userUid );

    if ($result->status_code == 0) {
        return 1;
    } else {
        return 0;
    }
}

/**
 *
 * @method
 *
 * Add a case note.
 *
 * @name PMFAddCaseNote
 * @label PMF Add a case note
 * @link http://wiki.processmaker.com/index.php/ProcessMaker_Functions#PMFAddCaseNote.28.29
 *
 * @param string(32) | $caseUid | ID of the case | The unique ID of the case.
 * @param string(32) | $processUid | ID of the process | The unique ID of the process.
 * @param string(32) | $taskUid | ID of the task | The unique ID of the task.
 * @param string(32) | $userUid | ID user | The unique ID of the user who will add note case.
 * @param string | $note | Note of the case | Note of the case.
 * @param int | $sendMail = 1 | Send mail | Optional parameter. If set to 1, will send an email to all participants in the case.
 * @return int | $result | Result of the add a case note | Returns 1 if the note has been added to the case.; otherwise, returns 0 if an error occurred.
 *
 */
function PMFAddCaseNote($caseUid, $processUid, $taskUid, $userUid, $note, $sendMail = 1)
{
    $ws = new WsBase();
    $result = $ws->addCaseNote($caseUid, $processUid, $taskUid, $userUid, $note, $sendMail);

    if ($result->status_code == 0) {
        return 1;
    } else {
        return 0;
    }
}

/**
 *@method
 *
 * Adds a filename and file path to an associative array of files which can be passed to the PMFSendMessage() to send emails with attachments. It renames files with the same filename so existing files will not be replaced in the array.
 *
 * @name PMFAddAttachmentToArray
 * @label Add File to Array
 * @link http://wiki.processmaker.com/index.php/ProcessMaker_Functions#PMFAddAttachmentToArray.28.29
 *
 * @param array | $arrayData | Array of files | Associative array where the index of each element is its new filename and its value is the path to the file or its web address.
 * @param string(32) | $index | Filename | New filename which will be added as the index in the array
 * @param string(32) | $value | File location | The web address or path on the ProcessMaker server for the file
 * @param string | $suffix = " Copy({i})" | Filename suffix | A suffix to add to the filename if the filename already exists in the array
 * @return array | $arrayData | Array with new data | The array with the added file
 *
 */

function PMFAddAttachmentToArray($arrayData, $index, $value, $suffix = " Copy({i})")
{
    if (isset($suffix) && $suffix == "") {
        $suffix = " Copy({i})";
    }

    $newIndex = $index;
    $count = 2;

    $newIndexFormat = $index . $suffix;

    if (preg_match("/^(.+)\.(.+)$/", $index, $arrayMatch)) {
        $newIndexFormat = $arrayMatch[1] . $suffix . "." . $arrayMatch[2];
    }

    while (isset($arrayData[$newIndex])) {
        $newIndex = str_replace("{i}", $count, $newIndexFormat);
        $count = $count + 1;
    }

    $arrayData[$newIndex] = $value;

    return $arrayData;
}

/**
 *@method
 *
 * Removes the currency symbol and thousands separator inserted by a currency mask.
 *
 * @name PMFRemoveMask
 * @label PMF Remove Mask
 *
 * @param string | $field | Value the field
 * @param string | $separator | Separator of thousands (, or .)
 * @param string | $currency | symbol of currency
 * @return $field | value without mask
 *
 */

function PMFRemoveMask ($field, $separator = '.', $currency = '')
{
    $thousandSeparator = $separator;
    $decimalSeparator = ($thousandSeparator == ".") ? "," : ".";

    $field = str_replace($thousandSeparator, "", $field);
    $field = str_replace($decimalSeparator, ".", $field);
    $field = str_replace($currency, "", $field);
    if(strpos($decimalSeparator, $field) !== false){
        $field = (float)(trim($field));
    }
    return $field;
}

/**
 *@method
 *
 * Sends an array of case variables to a specified case.
 *
 * @name PMFSaveCurrentData
 * @label PMF Save Current Data
 *
 * @return int | $result | Result of send variables | Returns 1 if the variables were sent successfully to the case; otherwise, returns 0 if an error occurred.
 *
 */

function PMFSaveCurrentData ()
{
    global $oPMScript;
    $response = 0;

    if (isset($_SESSION['APPLICATION']) && isset($oPMScript->aFields)) {
        $ws = new WsBase();
        $result = $ws->sendVariables($_SESSION['APPLICATION'], $oPMScript->aFields);
        $response = $result->status_code == 0 ? 1 : 0;
    }
    return $response;
}

/**
 * @method
 * Return an array of associative arrays which contain the unique task ID and title.
 * @name PMFTasksListByProcessId
 * @label PMF Tasks List By Process Id
 * @param string | $processId | ID Process | To get the current process id, use the system variable @@PROCESS
 * @param string | $lang | Language | This parameter actually is not used, the same is kept by backward compatibility.Is the language of the text, that must be the same to the column: "CON_LANG" of the CONTENT table
 * @return array | $result | Array result | Array of associative arrays which contain the unique task ID and title
 */
function PMFTasksListByProcessId($processId, $lang = 'en')
{
    $result = array();
    $criteria = new Criteria("workflow");
    $criteria->addSelectColumn(TaskPeer::TAS_UID);
    $criteria->addSelectColumn(TaskPeer::TAS_TITLE);
    $criteria->add(TaskPeer::PRO_UID, $processId, Criteria::EQUAL);
    $ds = TaskPeer::doSelectRS($criteria);
    $ds->setFetchmode(ResultSet::FETCHMODE_ASSOC);
    while ($ds->next()) {
        $result[] = $ds->getRow();
    }
    return $result;
}

/**
 *
 * @method
 *
 * Get the Unique id of Process by name
 *
 * @name PMFGetProcessUidByName
 * @label PMF Get the Unique id of Process by name
 * @link http://wiki.processmaker.com/index.php/ProcessMaker_Functions#PMFGetProcessUidByName.28.29
 *
 * @param  string | $processName = '' | Name of Process | Name of Process
 * @return string(32) | $processUid | Unique id of Process | Returns the Unique id of Process, FALSE otherwise
 *
 */
function PMFGetProcessUidByName($processName = '')
{
    try {
        $processUid = '';

        if ($processName == '') {
            //Return
            return (isset($_SESSION['PROCESS']))? $_SESSION['PROCESS'] : false;
        }

        $criteria = new Criteria('workflow');

        $criteria->addSelectColumn(ProcessPeer::PRO_UID);
        $criteria->add(ProcessPeer::PRO_TITLE, $processName, Criteria::EQUAL);
        $rsCriteria = ProcessPeer::doSelectRS($criteria);
        $rsCriteria->setFetchmode(ResultSet::FETCHMODE_ASSOC);

        if ($rsCriteria->next()) {
            $row = $rsCriteria->getRow();
            $processUid = $row['PRO_UID'];
        } else {
            //Return
            return false;
        }

        //Return
        return $processUid;
    } catch (Exception $e) {
        throw $e;
    }
}

/**
 * @method
 * The requested text in the specified language | If not found returns false
 * @name PMFGeti18nText
 * @label PMF Get i18n Text
 * @param string | $id | ID Text | Is the identifier of text, that must be the same to the column: "CON_ID" of the CONTENT table
 * @param string | $category | Category  | Is the category of the text, that must be the same to the column: "CON_CATEGORY" of the CONTENT table
 * @param string | $lang | Language | Is the language of the text, that must be the same to the column: "CON_LANG" of the CONTENT table
 * @return string | $text | Translated text | the translated text of a string in Content
 */
function PMFGeti18nText($id, $category, $lang = "en")
{
    $text = false;
    $criteria = new Criteria("workflow");
    $criteria->addSelectColumn(ContentPeer::CON_VALUE);
    $criteria->add(ContentPeer::CON_ID, $id, Criteria::EQUAL);
    $criteria->add(ContentPeer::CON_CATEGORY, $category, Criteria::EQUAL);
    $criteria->add(ContentPeer::CON_LANG, $lang, Criteria::EQUAL);
    $ds = ContentPeer::doSelectRS($criteria);
    $ds->setFetchmode(ResultSet::FETCHMODE_ASSOC);
    $ds->next();
    $row = $ds->getRow();
    if (isset($row["CON_VALUE"])) {
        $text = $row["CON_VALUE"];
    }
    return $text;
}

/**
 * @method
 * The requested text in the specified language | If not found returns false
 * @name PMFUnCancelCase
 * @label PMF Restore Case
 * @param string | $caseUID | ID Case | Is the unique UID of the case
 * @param string | $userUID | ID User  | Is the unique ID of the user who will uncancel the case
 * @return int | $value | Return | Returns 1 if the case was successfully uncancelled, otherwise returns 0 if an error ocurred
 */
function PMFUnCancelCase($caseUID, $userUID)
{
    try {
        $cases = new Cases();
        $cases->unCancelCase($caseUID, $userUID);
        return 1;
    } catch (Exception $oException) {
        return 0;
    }
}

/**
 * @method
 * Function to return an array of objects containing the properties of the fields
 * in a specified DynaForm.
 * It also inserts the "value" and "value_label" as properties in the fields' objects,
 * if the case is specified.
 * @name PMFDynaFormFields
 * @label PMF DynaForm Fields
 * @param string | $dynUid | Dynaform ID | Id of the dynaform
 * @param string | $appUid | Case ID | Id of the case
 * @param int | $delIndex | Delegation index | Delegation index for case
 * @return array | $fields | List of fields | Return a list of fields
 */
function PMFDynaFormFields($dynUid, $appUid = false, $delIndex = 0)
{
    $fields = array();
    $data = array();

    if ($appUid !== false) {
        if ($delIndex < 0) {
            throw new Exception(G::LoadTranslation('ID_INVALID_DELEGATION_INDEX_FOR_CASE') . "'" . $appUid . "'.");
        }
        $cases = new Cases();
        $data = $cases->loadCase($appUid, $delIndex);
    } else {
        global $oPMScript;
        if (isset($oPMScript->aFields) && is_array($oPMScript->aFields)) {
            $data['APP_DATA'] = $oPMScript->aFields;
        }
    }
    $data["CURRENT_DYNAFORM"] = $dynUid;

    $dynaform = new PmDynaform(\ProcessMaker\Util\DateTime::convertUtcToTimeZone($data));
    $dynaform->onPropertyRead = function(&$json, $key, $value) {
        if (isset($json->data) && !isset($json->value)) {
            $json->value = $json->data->value;
            $json->value_label = $json->data->label;
        }
    };

    if ($dynaform->isResponsive()) {
        $json = G::json_decode($dynaform->record["DYN_CONTENT"]);
        $dynaform->jsonr($json);

        $rows = $json->items[0]->items;
        foreach ($rows as $items) {
            foreach ($items as $item) {
                $fields[] = $item;
            }
        }
    } else {
        $oldDynaform = new Dynaform();
        $aFields = $oldDynaform->getDynaformFields($dynUid);
        foreach ($aFields as $value) {
            if (isset($data["APP_DATA"]) && isset($data["APP_DATA"][$value->name])) {
                $value->value = $data["APP_DATA"][$value->name];
            }
            $fields[] = $value;
        }
    }
    return $fields;
}

/**
 * @method
 * Return the task title of the specified task uid | If not found returns false
 * @name PMFGetTaskName
 * @label PMF Get Task Title Text
 * @param string | $taskUid | ID Task | Is the identifier of task, that must be the same to the column: "TAS_UID" of the TASK table
 * @param string | $lang | Language | This parameter actually is not used, the same is kept by backward compatibility. Is the language of the text, that must be the same to the column: "CON_LANG"
 * of the CONTENT table
 * @return string | $text | Translated text | the translated text of a string in Content
 */
function PMFGetTaskName($taskUid, $lang = SYS_LANG) {
    if (empty($taskUid)) {
        return false;
    }
    $oTask = new \Task();
    $aTasks = $oTask->load($taskUid);
    $text = isset($aTasks["TAS_TITLE"]) ? $aTasks["TAS_TITLE"] : false;
    return $text;
}

/**
 * @method
 * Return the group title of the specified group uid | If not found returns false
 * @name PMFGetGroupName
 * @label PMF Get Group Title Text
 * @param string | $grpUid | ID Group | Is the identifier of group, that must be the same to the column: "GPR_UID" of the GROUPWF table
 * @param string | $lang | Language | Is the language of the text, that must be the same to the column: "CON_LANG" of the CONTENT table
 * @return string | $text | Translated text | the translated text of a string in Content
 */
function PMFGetGroupName($grpUid, $lang = SYS_LANG) {
    if (empty($grpUid)) {
        return false;
    }
    return PMFGeti18nText($grpUid, 'GRP_TITLE', $lang);
}

/**
 * @method
 * The identifier of the element found using the text.
 * @name PMFGetUidFromText
 * @label PMF Get Uid From Text
 * @param string | $text | Text
 * @param string | $category | Category
 * @param string | $proUid | ProcessUid
 * @param string | $lang | Languaje
 * @return array
 */
function PMFGetUidFromText($text, $category, $proUid = null, $lang = SYS_LANG)
{
    $obj = new ElementTranslation();
    $uids = $obj->getUidFromTextI18n($text, $category, $proUid, $lang);
    return $uids;
}

/**
 * @method
 *
 * Get the unique ID of dynaform
 *
 * @name PMFGetDynaformUID
 * @label PMF Get Dynafrom UID
 * @link http://wiki.processmaker.com/index.php/ProcessMaker_Functions#PMFGetDynaformUID.28.29
 *
 * @param string | $dynaFormName | Name dynaform | The name of dynaform
 * @param string | $processUid = null| ID of the process | The unique ID of process. If not set, the current process ID is used
 *
 * @return string | $dynaFormUid | The unique ID of dynaform | Returns the unique ID of dynaform, FALSE otherwise
 */
function PMFGetDynaformUID($dynaFormName, $processUid = null)
{
    if (is_null($processUid) && !(isset($_SESSION) && array_key_exists('PROCESS', $_SESSION))) {
        return false;
    }

    $arrayResult = PMFGetUidFromText($dynaFormName, 'DYN_TITLE', (!empty($processUid)) ? $processUid : $_SESSION['PROCESS']);

    //Return
    return (!empty($arrayResult)) ? array_shift($arrayResult) : false;
}

/**
 * @method
 *
 * Get the unique ID of group
 *
 * @name PMFGetGroupUID
 * @label PMF Get Group UID
 * @link http://wiki.processmaker.com/index.php/ProcessMaker_Functions#PMFGetGroupUID.28.29

 * @param string | $groupName | Name group | The name of group
 *
 * @return string | $groupUid | The unique ID of group | Returns the unique ID of group, FALSE otherwise
 */
function PMFGetGroupUID($groupName)
{
    $groupUid = '';

    $criteria = new Criteria('workflow');

    $criteria->addSelectColumn(GroupwfPeer::GRP_UID);
    $criteria->add(GroupwfPeer::GRP_TITLE, $groupName, Criteria::EQUAL);

    $rsCriteria = GroupwfPeer::doSelectRS($criteria);
    $rsCriteria->setFetchmode(ResultSet::FETCHMODE_ASSOC);

    if ($rsCriteria->next()) {
        $record = $rsCriteria->getRow();

        $groupUid = $record['GRP_UID'];
    }

    //Return
    return ($groupUid != '')? $groupUid : false;
}

/**
 * @method
 *
 * Get the unique ID of task
 *
 * @name PMFGetTaskUID
 * @label PMF Get Task UID
 * @link http://wiki.processmaker.com/index.php/ProcessMaker_Functions#PMFGetTaskUID.28.29
 *
 * @param string | $taskName | Name task | The name of task
 * @param string | $processUid = null| ID of the process | The unique ID of process. If not set, the current process ID is used
 *
 * @return string | $taskUid | The unique ID of task | Returns the unique ID of task, FALSE otherwise
 */
function PMFGetTaskUID($taskName, $processUid = null)
{
    if (is_null($processUid) && !(isset($_SESSION) && array_key_exists('PROCESS', $_SESSION))) {
        return false;
    }

    $taskUid = '';

    $criteria = new Criteria('workflow');

    $criteria->addSelectColumn(TaskPeer::TAS_UID);
    $criteria->add(TaskPeer::TAS_TITLE, $taskName, Criteria::EQUAL);

    $criteria->add(TaskPeer::PRO_UID, (!empty($processUid)) ? $processUid : $_SESSION['PROCESS'],
        Criteria::EQUAL);

    $rsCriteria = TaskPeer::doSelectRS($criteria);
    $rsCriteria->setFetchmode(ResultSet::FETCHMODE_ASSOC);

    if ($rsCriteria->next()) {
        $record = $rsCriteria->getRow();

        $taskUid = $record['TAS_UID'];
    }

    //Return
    return ($taskUid != '') ? $taskUid : false;
}

/**
 * @method
 * Get Group Users
 * @name PMFGetGroupUsers
 * @label PMF Group Users
 * @param string | $GroupUID | Group UID
 * @return  array | $result | array
 */
function PMFGetGroupUsers($GroupUID)
{
    $groups = new Groups();
    $usersGroup = $groups->getUsersOfGroup($GroupUID, 'ALL');
    return $usersGroup;

}

/**
 * @method
 *
 * Get next derivation info
 *
 * @name PMFGetNextDerivationInfo
 * @label PMF Get next derivation info
 * @link http://wiki.processmaker.com/index.php/ProcessMaker_Functions#PMFGetNextDerivationInfo.28.29
 *
 * @param string(32) | $caseUid | ID of the case | The unique ID of the case
 * @param int | $delIndex | Delegation index of the case | The delegation index of the current task in the case
 *
 * @return array | $arrayNextDerivationInfo | Next derivation info | Returns the next derivation info, FALSE otherwise
 */
function PMFGetNextDerivationInfo($caseUid, $delIndex)
{
    try {
        $arrayNextDerivationInfo = [];

        //Verify data and Set variables
        $case = new \ProcessMaker\BusinessModel\Cases();

        $arrayAppDelegationData = $case->getAppDelegationRecordByPk(
            $caseUid,
            $delIndex,
            ['$applicationUid' => '$caseUid', '$delIndex' => '$delIndex'],
            false
        );

        if ($arrayAppDelegationData === false) {
            return false;
        }

        //Set variables
        $processUid = $arrayAppDelegationData['PRO_UID'];
        $userUid = $arrayAppDelegationData['USR_UID'];

        //Get next derivation
        $derivation = new Derivation();

        $arrayData = $derivation->prepareInformation([
            'APP_UID'   => $caseUid,
            'DEL_INDEX' => $delIndex,
            'USER_UID'  => $userUid //User logged
        ]);

        $task = new \ProcessMaker\BusinessModel\Task();

        foreach ($arrayData as $value) {
            $arrayInfo = $value;

            $nextTaskUid = $arrayInfo['NEXT_TASK']['TAS_UID'];

            $arrayUserUid = [];
            $arrayGroupUid = [];

            if ($nextTaskUid != '-1') {
                $arrayResult = $task->getTaskAssignees($processUid, $nextTaskUid, 'ASSIGNEE', 1);

                foreach ($arrayResult['data'] as $value2) {
                    $arrayAssigneeData = $value2;

                    switch ($arrayAssigneeData['aas_type']) {
                        case 'user':
                            $arrayUserUid[] = $arrayAssigneeData['aas_uid'];
                            break;
                        case 'group':
                            $arrayGroupUid[] = $arrayAssigneeData['aas_uid'];
                            break;
                    }
                }

                $assignmentType = $arrayInfo['NEXT_TASK']['TAS_ASSIGN_TYPE'];

                if ($arrayInfo['NEXT_TASK']['TAS_ASSIGN_TYPE'] == 'SELF_SERVICE' &&
                    trim($arrayInfo['NEXT_TASK']['TAS_GROUP_VARIABLE']) != ''
                ) {
                    $assignmentType = 'SELF_SERVICE_VALUE';
                }

                $arrayNextDerivationInfo[] = [
                    'taskUid'        => $nextTaskUid,
                    'assignmentType' => $assignmentType,
                    'users'  => $arrayUserUid,
                    'groups' => $arrayGroupUid,
                ];
            }
        }

        //Return
        return $arrayNextDerivationInfo;
    } catch (Exception $e) {
        throw $e;
    }
}

/**
 * @method
 *
 * Direct case link
 *
 * @name PMFCaseLink
 * @label PMF Direct case link
 * @link http://wiki.processmaker.com/index.php/ProcessMaker_Functions#PMFCaseLink.28.29
 *
 * @param string(32) | $caseUid | ID of the case | The unique ID of the case
 * @param string | $workspace = null | Workspace | The workspace
 * @param string | $language = null | Language | The language
 * @param string | $skin = null | Skin | The skin
 *
 * @return string | $url | Direct case link | Returns the direct case link, FALSE otherwise
 */
function PMFCaseLink($caseUid, $workspace = null, $language = null, $skin = null)
{
    try {
        $case = new \ProcessMaker\BusinessModel\Cases();

        $arrayApplicationData = $case->getApplicationRecordByPk($caseUid, [], false);

        if ($arrayApplicationData === false) {
            return false;
        }
        $workspace = (!empty($workspace)) ? $workspace : SYS_SYS;
        $language = (!empty($language)) ? $language : SYS_LANG;
        $skin = (!empty($skin)) ? $skin : SYS_SKIN;

        $uri = '/sys' . $workspace . '/' . $language . '/' . $skin . '/cases/opencase/' . $caseUid;

        //Return
        return ((G::is_https()) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $uri;
    } catch (Exception $e) {
        throw $e;
    }
}

/**
 *
 * @method
 *
 * This function Associates the uploaded files inside a grid with an Input File of the process
 *
 * @name PMFAssociateUploadedFilesWithInputFile
 * @label PMF Associates the uploaded files inside a grid with an Input File of the process
 * @link http://wiki.processmaker.com/index.php/ProcessMaker_Functions#PMFAssociateUploadedFilesWithInputFile.28.29
 *
 * @param string(32) | $inputDocumentUid | Unique id of Input Document | The unique id of the Input Document we want to associate with
 * @param string(32) | $gridVariableName | Variable name of the grid | Variable name of the grid, that contains the uploaded files
 * @param string(32) | $fileVariableName | Variable name file | Variable name file , of the field in the grid that contains the files
 * @param string(32) | $caseUid | Unique id of the case | The unique id of the case with the documents (variable application)
 * @param string(32) | $userUid | Unique id of the user | The unique id of the user that's logged in for doing the upload
 * @param int | $currentDelIndex | Current Index of the application | Current Index of the application
 *
 * @return none | $none | None | None
 */

function PMFAssociateUploadedFilesWithInputFile($inputDocumentUid, $gridVariableName, $fileVariableName, $caseUid, $userUid, $currentDelIndex)
{
    try {
        require_once 'classes/model/AppDocument.php';

        $appDocument = new AppDocument();

        //First step: get all the documents from this case and this grid that are not associated
        $criteria = new Criteria('workflow');

        $criteria->add(AppDocumentPeer::APP_UID, $caseUid, Criteria::EQUAL);
        $criteria->add(AppDocumentPeer::APP_DOC_TYPE, 'ATTACHED', Criteria::EQUAL);
        $criteria->add(AppDocumentPeer::APP_DOC_FIELDNAME, $gridVariableName . '_%_' . $fileVariableName, Criteria::LIKE);
        $criteria->add(AppDocumentPeer::APP_DOC_STATUS, 'ACTIVE', Criteria::EQUAL);
        $criteria->add(AppDocumentPeer::DOC_UID, '-1', Criteria::EQUAL);
        $criteria->addAscendingOrderByColumn(AppDocumentPeer::APP_DOC_INDEX);

        $numRecTotal = AppDocumentPeer::doCount($criteria);

        //If we have an error in the result set, do not continue
        if ($numRecTotal == 0) {
            //Return
            return false;
        }

        //Query
        $rsCriteria = AppDocumentPeer::doSelectRS($criteria);
        $rsCriteria->setFetchmode(ResultSet::FETCHMODE_ASSOC);

        //Search file in the process, associate with the Input document
        while ($rsCriteria->next()) {
            $row = $rsCriteria->getRow();

            //Load the file by its unique identifier, its name, its extension and the path where it's stored
            $file = $appDocument->load($row['APP_DOC_UID']);
            $ext = pathinfo($file['APP_DOC_FILENAME'], PATHINFO_EXTENSION);
            $fileName = $file['APP_DOC_UID'] . '_' . $file['DOC_VERSION'] . '.' . $ext;
            $pathFile = PATH_DOCUMENT . G::getPathFromUID($caseUid) . PATH_SEP . $fileName;
            $comment = '';

            //Delete this file so it can't be uploaded again (Includes Mark Database Record)
            $appDocument->remove($file['APP_DOC_UID'], $file['DOC_VERSION']);

            $fields = array (
                'APP_UID' => $caseUid,
                'DEL_INDEX' => $currentDelIndex,
                'USR_UID' => $userUid,
                'DOC_UID' => $inputDocumentUid,
                'APP_DOC_TYPE' => 'INPUT',
                'APP_DOC_CREATE_DATE' => date('Y-m-d H:i:s'),
                'APP_DOC_COMMENT' => $comment,
                'APP_DOC_TITLE' => $file['APP_DOC_TITLE'],
                'APP_DOC_FILENAME' => $file['APP_DOC_FILENAME'],
                'APP_DOC_FIELDNAME' => ''
            );

            $result = $appDocument->create($fields);

            //Copy the file
            $appUid = $appDocument->getAppUid();
            $appDocUid = $appDocument->getAppDocUid();
            $docVersion = $appDocument->getDocVersion();
            $info = pathinfo( $appDocument->getAppDocFilename() );
            $extAux = (isset( $info['extension'] )) ? $info['extension'] : '';

            //Save the file
            $pathName = PATH_DOCUMENT . G::getPathFromUID($appUid) . PATH_SEP;
            $pathFileName = $appDocUid . '_' . $docVersion . '.' . $extAux;

            copy($pathFile, $pathName . $pathFileName);

            //Update
            $result = $appDocument->update([
                'APP_DOC_UID' => $appDocUid,
                'DOC_VERSION' => $docVersion,
                'APP_DOC_TAGS' => 'INPUT',
                'APP_DOC_FIELDNAME' => $file['APP_DOC_FIELDNAME']
            ]);

            //Remove the old submitted file completely from file system
            unlink($pathFile);
        }
    } catch (Exception $e) {
        throw $e;
    }
}

/**
 * @method
 *
 * Remove users from a group
 *
 * @name PMFRemoveUsersToGroup
 * @label PMF Remove users from a group (deprecated)
 * @link http://wiki.processmaker.com/index.php/ProcessMaker_Functions#PMFRemoveUsersToGroup.28.29
 *
 * @deprecated we corrected the name
 * @param string | $groupId | Group Uid | The unique Uid of the group.
 * @param array | $users | Array of users | Array of users to remove.
 *
 * @return  array | $result | array
 */
function PMFRemoveUsersToGroup($groupUid, array $users)
{
    try {
        $user = new \ProcessMaker\BusinessModel\Group\User();

        $result = $user->unassignUsers($groupUid, $users);

        //Return
        return $result;
    } catch (Exception $e) {
        throw $e;
    }
}

/**
 * @method
 *
 * Remove users from a group
 *
 * @name PMFRemoveUsersFromGroup
 * @label PMF Remove users from a group
 * @link http://wiki.processmaker.com/index.php/ProcessMaker_Functions#PMFRemoveUsersFromGroup.28.29
 *
 * @param string | $groupId | Group Uid | The unique Uid of the group.
 * @param array | $users | Array of users | Array of users to remove.
 *
 * @return  array | $result | array
 */
function PMFRemoveUsersFromGroup($groupUid, array $users)
{
    try {
        $user = new \ProcessMaker\BusinessModel\Group\User();

        $result = $user->unassignUsers($groupUid, $users);

        //Return
        return $result;
    } catch (Exception $e) {
        throw $e;
    }
}

/**
 * @method
 *
 * Copy or attach a file to a Case
 *
 * @name PMFCopyDocumentCase
 * @label PMF Copy Document Case
 *
 * @param string | $appDocUid | Document Application ID | The unique Uid of the Document.
 * @param int | $versionNumber | Version Number | Is the document version.
 * @param string | $targetCaseUid | Case ID | Is the target case uid where we want to copy the document to.
 * @param string | $inputDocumentUid =null | InputDocument ID | Optional parameter. Is the input document that we want to associate with in the target case. If is not specified then the file is uploaded as attachment in the case (not associated to any input document).
 *
 * @return string | $newUidAppDocUid | ID of the document | Returns ID if it has copied the input document successfully; otherwise, returns exception if an error occurred.
 */
function PMFCopyDocumentCase($appDocUid, $versionNumber, $targetCaseUid, $inputDocumentUid = null)
{
    try {
        $messageError = 'function:PMFCopyDocumentCase Error!, ';
        $appDocument = new AppDocument();
        $dataFields = $appDocument->load($appDocUid, $versionNumber);
        if (!$dataFields) {
            throw new Exception($messageError . 'The AppDocUid does not exist');
        }
        $arrayFieldData = array(
            "APP_UID" => $targetCaseUid,
            "DEL_INDEX" => $dataFields['DEL_INDEX'],
            "USR_UID" => $dataFields['USR_UID'],
            "DOC_UID" => (!empty($inputDocumentUid)) ? $inputDocumentUid : $dataFields['DOC_UID'],
            "APP_DOC_TYPE" => $dataFields['APP_DOC_TYPE'],
            "APP_DOC_CREATE_DATE" => date("Y-m-d H:i:s"),
            "APP_DOC_COMMENT" => $dataFields['APP_DOC_COMMENT'],
            "APP_DOC_TITLE" => $dataFields['APP_DOC_TITLE'],
            "APP_DOC_FILENAME" => $dataFields['APP_DOC_FILENAME'],
            "FOLDER_UID" => $dataFields['FOLDER_UID'],
            "APP_DOC_TAGS" => $dataFields['APP_DOC_TAGS']
        );

        $arrayInfo = pathinfo($appDocument->getAppDocFilename());
        $ext = (isset($arrayInfo['extension']) ? $arrayInfo['extension'] : '');
        $parcialPath = G::getPathFromUID($dataFields['APP_UID']);
        $file = G::getPathFromFileUID($dataFields['APP_UID'], $dataFields['APP_DOC_UID']);
        $realPath = PATH_DOCUMENT . $parcialPath . '/' . $file[0] . $file[1] . '_' . $versionNumber . '.' . $ext;
        $strFileName = $dataFields['APP_DOC_UID'] . '_' . $versionNumber . '.' . $ext;
        $newUidAppDocUid = null;
        if ($dataFields['APP_DOC_TYPE'] == 'INPUT' || $dataFields['APP_DOC_TYPE'] == 'ATTACHED') {
            if (file_exists($realPath)) {
                $strPathName = PATH_DOCUMENT . G::getPathFromUID($targetCaseUid) . PATH_SEP;
                if (!is_dir($strPathName)) {
                    G::mk_dir($strPathName);
                }
                $appNewDocument = new AppDocument();
                $newUidAppDocUid = $appNewDocument->create($arrayFieldData);
                $appNewDocument->setAppDocTitle($dataFields['APP_DOC_TITLE']);
                $appNewDocument->setAppDocComment($dataFields['APP_DOC_COMMENT']);
                $appNewDocument->setAppDocFilename($dataFields['APP_DOC_FILENAME']);
                $newStrFileName = $newUidAppDocUid . '_' . $versionNumber . '.' . $ext;
                $resultCopy = copy($realPath, $strPathName . $newStrFileName);
                if (!$resultCopy) {
                    throw new Exception($messageError, 'Could not copy the document');
                }
            } else {
                throw new Exception($messageError, 'The document for copy does not exist');
            }
        } else {
            $pathOutput = PATH_DOCUMENT . G::getPathFromUID($dataFields['APP_UID']) . PATH_SEP . 'outdocs' . PATH_SEP;
            if (is_dir($pathOutput)) {
                @chmod($pathOutput, 0755);
                $strPathName = PATH_DOCUMENT . G::getPathFromUID($targetCaseUid) . PATH_SEP . 'outdocs' . PATH_SEP;
                if (!is_dir($strPathName)) {
                    G::mk_dir($strPathName);
                }
                @chmod($strPathName, 0755);
                $oAppDocument = new AppDocument();
                $newUidAppDocUid = $oAppDocument->create($arrayFieldData);
                $arrayExtension = array('doc', 'html', 'pdf');
                $newStrFilename = $newUidAppDocUid . '_' . $versionNumber;
                foreach ($arrayExtension as $item) {
                    $resultCopy = copy($pathOutput . $strFileName . $item, $strPathName . $newStrFilename . '.' . $item);
                    if (!$resultCopy) {
                        throw new Exception($messageError, 'Could not copy the document');
                    }
                }
            } else {
                throw new Exception($messageError, 'The document for copy does not exist');
            }
        }
        return $newUidAppDocUid;
    } catch (Exception $e) {
        throw $e;
    }
}

/**
 * @method
 *
 * Add user or group to Task
 *
 * @name PMFAddUserGroupToTask
 * @label PMF Add user or group to Task
 * @link http://wiki.processmaker.com/index.php/ProcessMaker_Functions#PMFAddUserGroupToTask.28.29
 *
 * @param string | $taskUid | Task Uid | The unique Id of the Task.
 * @param string | $userGroupUid | Uid from User or Group | The unique Uid from User or Group.
 *
 * @return int | $result | Result | Returns 1 when is assigned
 */

function PMFAddUserGroupToTask($taskUid, $userGroupUid)
{
    //Verify data and Set variables
    $task = new \ProcessMaker\BusinessModel\Task();
    $taskwf = TaskPeer::retrieveByPK($taskUid);

    if (is_null($taskwf)) {
        throw new Exception(G::LoadTranslation('ID_TASK_NOT_EXIST', ['tas_uid', $taskUid]));
    }

    $uid = '';
    $userType = '';

    $objUser = UsersPeer::retrieveByPK($userGroupUid);

    if (!is_null($objUser)) {
        $uid = $userGroupUid;
        $userType = 'user';
    } else {
        $groupUid = GroupwfPeer::retrieveByPK($userGroupUid);

        if (!is_null($groupUid)) {
            $uid = $userGroupUid;
            $userType = 'group';
        } else {
            throw new Exception(G::LoadTranslation(
                'ID_USER_GROUP_NOT_CORRESPOND', [$userGroupUid, G::LoadTranslation('ID_USER') . '/' . G::LoadTranslation('ID_GROUP')]
            ));
        }
    }

    //Assignee User/Group
    $task->addTaskAssignee($taskwf->getProUid(), $taskUid, $uid, $userType);

    //Return
    return 1;
}

/**
 * @method
 *
 * Remove a user or group from the list of assignees of the Task.
 *
 * @name PMFRemoveUserGroupFromTask
 * @label PMF Remove user or group from a Task
 * @link http://wiki.processmaker.com/index.php/ProcessMaker_Functions#PMFRemoveUserGroupFromTask.28.29
 *
 * @param string | $taskUid | Task Uid | The unique Id of the Task.
 * @param string | $userGroupUid | Uid from User or Group | The unique Id from User or Group.
 *
 * @return int | $result | Result | Returns 1 when is remove
 */
function PMFRemoveUserGroupFromTask($taskUid, $userGroupUid)
{
    //Verify data and Set variables
    $task = new \ProcessMaker\BusinessModel\Task();
    $taskwf = TaskPeer::retrieveByPK($taskUid);

    if (is_null($taskwf)) {
        throw new Exception(G::LoadTranslation('ID_TASK_NOT_EXIST', ['tas_uid', $taskUid]));
    }

    $uid = '';

    $objUser = UsersPeer::retrieveByPK($userGroupUid);

    if (!is_null($objUser)) {
        $uid = $userGroupUid;
    } else {
        $groupUid = GroupwfPeer::retrieveByPK($userGroupUid);

        if (!is_null($groupUid)) {
            $uid = $userGroupUid;
        } else {
            throw new Exception(G::LoadTranslation(
                'ID_USER_GROUP_NOT_CORRESPOND', [$userGroupUid, G::LoadTranslation('ID_USER') . '/' . G::LoadTranslation('ID_GROUP')]
            ));
        }
    }

    //Remove User/Group
    $task->removeTaskAssignee($taskwf->getProUid(), $taskUid, $uid);

    //Return
    return 1;
}

/**
 * @method
 *
 * Sends emails to user's group using a template file
 *
 * @name PMFSendMessageToGroup
 * @label PMF Send Message To Group
 * @link http://wiki.processmaker.com/index.php/ProcessMaker_Functions#PMFSendMessageToGroup.28.29
 *
 * @param string(32) | $groupId | Group ID | Unique id of Group.
 * @param string(32) | $caseId | Case ID | The UID (unique identification) for a case, which is a string of 32 hexadecimal characters to identify the case.
 * @param string | $from | Sender | The email address of the person who sends out the email.
 * @param string | $subject | Subject of the email | The subject (title) of the email.
 * @param string | $template | Name of the template | The name of the template file in plain text or HTML format which will produce the body of the email.
 * @param array | $arrayField = [] | Variables for email template | Optional parameter. An associative array where the keys are the variable names and the values are the variables' values.
 * @param array | $arrayAttachment = [] | Attachment | An Optional arrray. An array of files (full paths) to be attached to the email.
 * @param boolean | $showMessage = true | Show message | Optional parameter. Set to TRUE to show the message in the case's message history.
 * @param int | $delIndex = 0 | Delegation index of the case | Optional parameter. The delegation index of the current task in the case.
 * @param mixed | $config = [] | Email server configuration | An optional array: An array of parameters to be used in the Email sent (MESS_ENGINE, MESS_SERVER, MESS_PORT, MESS_FROM_MAIL, MESS_RAUTH, MESS_ACCOUNT, MESS_PASSWORD, and SMTPSecure) Or String: UID of Email server.
 * @param int | $limit = 100 | Limit | Limit of mails to send in each bach.
 *
 * @return int | $result | Result | Returns 1 when is send message to group
 */
function PMFSendMessageToGroup(
    $groupId,
    $caseId,
    $from,
    $subject,
    $template,
    $arrayField = [],
    $arrayAttachment = [],
    $showMessage = true,
    $delIndex = 0,
    $config = [],
    $limit = 100
) {
    //Verify data and Set variables
    $group = new \ProcessMaker\BusinessModel\Group();
    $case = new \ProcessMaker\BusinessModel\Cases();

    $group->throwExceptionIfNotExistsGroup($groupId, '$groupId');

    if ($limit <= 0) {
        throw new Exception(G::LoadTranslation('ID_INVALID_LIMIT'));
    }

    $arrayApplicationData = $case->getApplicationRecordByPk($caseId, ['$applicationUid' => '$caseId'], true);

    //Send mails
    $criteriaGroupUser = $group->getUserCriteria($groupId, ['condition' => [[UsersPeer::USR_STATUS, 'ACTIVE', Criteria::EQUAL]]]);

    $start = 0;

    do {
        $flagNextRecord = false;

        $to = '';

        $criteria = clone $criteriaGroupUser;

        $criteria->setOffset($start);
        $criteria->setLimit($limit);

        $rsCriteria = GroupUserPeer::doSelectRS($criteria);
        $rsCriteria->setFetchmode(ResultSet::FETCHMODE_ASSOC);

        while ($rsCriteria->next()) {
            $record = $rsCriteria->getRow();

            $to .= (($to != '')? ', ' : '') . $record['USR_EMAIL'];

            $flagNextRecord = true;
        }

        if ($flagNextRecord) {
            $result = PMFSendMessage(
                $caseId, $from, $to, null, null, $subject, $template, $arrayField, $arrayAttachment, $showMessage, $delIndex, $config
            );

            if ($result == 0) {
                return 0;
            }
        }

        $start += $limit;
    } while ($flagNextRecord);

    //Return
    return 1;
}

//Start - Private functions


/**
 * Convert to string
 *
 * @param variant $vValue
 * @return string
 */
function pmToString($vValue)
{
    return (string)$vValue;
}

/**
 * Convert to integer
 *
 * @param variant $vValue
 * @return integer
 */
function pmToInteger($vValue)
{
    return (int)$vValue;
}

/**
 * Convert to float
 *
 * @param variant $vValue
 * @return float
 */
function pmToFloat($vValue)
{
    return (float)$vValue;
}

/**
 * Convert to Url
 *
 * @param variant $vValue
 * @return url
 */
function pmToUrl($vValue)
{
    return urlencode($vValue);
}

/**
 * Convert to data base escaped string
 *
 * @param variant $vValue
 * @return string
 */
function pmSqlEscape($vValue)
{
    return G::sqlEscape($vValue);
}

//End - Private functions


/**
 * Error handler
 *
 * @param $errno
 * @param $errstr
 * @param $errfile
 * @param $errline
 */
function handleErrors($errno, $errstr, $errfile, $errline)
{
    if ($errno != 2048 && isset($_SESSION['_DATA_TRIGGER_']['_EXECUTION_TIME_'])) {
        G::logTriggerExecution($_SESSION, $errstr, '', round(microtime(true) -
            $_SESSION['_DATA_TRIGGER_']['_EXECUTION_TIME_'], 5));
    }

    if ($errno != '' && ($errno != 8) && ($errno != 2048)) {
        if (isset($_SESSION['_CODE_'])) {
            $sCode = $_SESSION['_CODE_'];
            unset($_SESSION['_CODE_']);
            global $oPMScript;
            if (isset($oPMScript) && isset($_SESSION['APPLICATION'])) {
                $oCase = new Cases();
                $oPMScript->aFields['__ERROR__'] = $errstr;
                $oCase->updateCase($_SESSION['APPLICATION'], array('APP_DATA' => $oPMScript->aFields));
            }
            registerError(1, $errstr, $errline - 1, $sCode);
        }
    }
}

/*
 * Handle Fatal Errors
 * @param variant $buffer
 * @return buffer
 */

function handleFatalErrors($buffer)
{
    if (!empty($buffer)) {
        G::logTriggerExecution($_SESSION, $buffer, 'FATAL_ERROR');
    }

    if (preg_match('/(error<\/b>:)(.+)(<br)/', $buffer, $regs)) {
        $oCase = new Cases();
        $err = preg_replace('/<.*?>/', '', $regs[2]);
        $aAux = explode(' in ', $err);
        $sCode = isset($_SESSION['_CODE_']) ? $_SESSION['_CODE_'] : null;
        unset($_SESSION['_CODE_']);
        registerError(2, $aAux[0], 0, $sCode);
        if (strpos($_SERVER['REQUEST_URI'], '/cases/cases_Step') !== false) {
            if (strpos($_SERVER['REQUEST_URI'], '&ACTION=GENERATE') !== false) {
                $aNextStep = $oCase->getNextStep($_SESSION['PROCESS'], $_SESSION['APPLICATION'], $_SESSION['INDEX'], $_SESSION['STEP_POSITION']);
                if ($_SESSION['TRIGGER_DEBUG']['ISSET']) {
                    $_SESSION['TRIGGER_DEBUG']['TIME'] = G::toUpper(G::loadTranslation('ID_AFTER'));
                    $_SESSION['TRIGGER_DEBUG']['BREAKPAGE'] = $aNextStep['PAGE'];
                    $aNextStep['PAGE'] = $aNextStep['PAGE'] . '&breakpoint=triggerdebug';
                }
                global $oPMScript;
                if (isset($oPMScript) && isset($_SESSION['APPLICATION'])) {
                    $oPMScript->aFields['__ERROR__'] = $aAux[0];
                    $oCase->updateCase($_SESSION['APPLICATION'], array('APP_DATA' => $oPMScript->aFields));
                }
                G::header('Location: ' . $aNextStep['PAGE']);
                die();
            }
            $_SESSION['_NO_EXECUTE_TRIGGERS_'] = 1;
            global $oPMScript;
            if (isset($oPMScript) && isset($_SESSION['APPLICATION'])) {
                $oPMScript->aFields['__ERROR__'] = $aAux[0];
                $oCase->updateCase($_SESSION['APPLICATION'], array('APP_DATA' => $oPMScript->aFields));
            }
            G::header('Location: ' . $_SERVER['REQUEST_URI']);
            die();
        } else {
            $aNextStep = $oCase->getNextStep($_SESSION['PROCESS'], $_SESSION['APPLICATION'], $_SESSION['INDEX'], $_SESSION['STEP_POSITION']);
            if (isset($_SESSION['TRIGGER_DEBUG']['ISSET']) && $_SESSION['TRIGGER_DEBUG']['ISSET']) {
                $_SESSION['TRIGGER_DEBUG']['TIME'] = G::toUpper(G::loadTranslation('ID_AFTER'));
                $_SESSION['TRIGGER_DEBUG']['BREAKPAGE'] = $aNextStep['PAGE'];
                $aNextStep['PAGE'] = $aNextStep['PAGE'] . '&breakpoint=triggerdebug';
            }
            if (strpos($aNextStep['PAGE'], 'TYPE=ASSIGN_TASK&UID=-1') !== false) {
                G::SendMessageText('Fatal error in trigger', 'error');
            }
            global $oPMScript;
            if (isset($oPMScript) && isset($_SESSION['APPLICATION'])) {
                $oPMScript->aFields['__ERROR__'] = $aAux[0];
                $oCase->updateCase($_SESSION['APPLICATION'], array('APP_DATA' => $oPMScript->aFields));
            }
            G::header('Location: ' . $aNextStep['PAGE']);
            die();
        }
    }
    return $buffer;
}

/*
 * Register Error
 * @param string $iType
 * @param string $sError
 * @param string $iLine
 * @param string $sCode
 * @return void
 */

function registerError($iType, $sError, $iLine, $sCode)
{
    $sType = ($iType == 1 ? 'ERROR' : 'FATAL');
    $_SESSION['TRIGGER_DEBUG']['ERRORS'][][$sType] = $sError . ($iLine > 0 ? ' (line ' . $iLine . ')' : '') . ':<br /><br />' . $sCode;
}

/**
 * Obtain engine Data Base name
 *
 * @param type $connection
 * @return type
 */
function getEngineDataBaseName($connection)
{
    $aDNS = $connection->getDSN();
    return $aDNS["phptype"];
}

/**
 * Execute Queries for Oracle Database
 *
 * @param type $sql
 * @param type $connection
 */
function executeQueryOci($sql, $connection, $aParameter = array(), $dbsEncode = "")
{
    $aDNS = $connection->getDSN();

    $sUsername = $aDNS["username"];
    $sPassword = $aDNS["password"];
    $sHostspec = $aDNS["hostspec"];
    $sDatabse = $aDNS["database"];
    $sPort = $aDNS["port"];

    if ($sPort != "1521") {
        $flagTns = ($sDatabse == "" && ($sPort . "" == "" || $sPort . "" == "0")) ? 1 : 0;

        if ($flagTns == 0) {
            // if not default port
            $conn = oci_connect($sUsername, $sPassword, $sHostspec . ":" . $sPort . "/" . $sDatabse, $dbsEncode);
        } else {
            $conn = oci_connect($sUsername, $sPassword, $sHostspec, $dbsEncode);
        }
    } else {
        $conn = oci_connect($sUsername, $sPassword, $sHostspec . "/" . $sDatabse, $dbsEncode);
    }

    if (!$conn) {
        $e = oci_error();
        trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
        return $e;
    }

    switch (true) {
        case preg_match("/^(SELECT|SHOW|DESCRIBE|DESC|WITH)\s/i", $sql):
            $stid = oci_parse($conn, $sql);

            if (count($aParameter) > 0) {
                foreach ($aParameter as $key => $val) {
                    oci_bind_by_name($stid, $key, $val);
                }
            }
            oci_execute($stid, OCI_DEFAULT);

            $result = Array();
            $i = 1;
            while ($row = oci_fetch_array($stid, OCI_ASSOC + OCI_RETURN_NULLS)) {
                $result[$i++] = $row;
            }
            oci_free_statement($stid);
            oci_close($conn);
            return $result;
            break;
        case preg_match("/^(INSERT|UPDATE|DELETE)\s/i", $sql):
            $stid = oci_parse($conn, $sql);
            $isValid = true;
            if (count($aParameter) > 0) {
                foreach ($aParameter as $key => $val) {
                    oci_bind_by_name($stid, $key, $val);
                }
            }
            $objExecute = oci_execute($stid, OCI_DEFAULT);
            $result = oci_num_rows($stid);
            if ($objExecute) {
                oci_commit($conn);
            } else {
                oci_rollback($conn);
                $isValid = false;
            }
            oci_free_statement($stid);
            oci_close($conn);
            if ($isValid) {
                return $result;
            } else {
                return oci_error();
            }
            break;
        default:
            // Stored procedures
            $stid = oci_parse($conn, $sql);
            $aParameterRet = array();
            if (count($aParameter) > 0) {
                foreach ($aParameter as $key => $val) {
                    $aParameterRet[$key] = $val;
                    // The third parameter ($aParameterRet[$key]) returned a value by reference.
                    oci_bind_by_name($stid, $key, $aParameterRet[$key]);
                }
            }
            $objExecute = oci_execute($stid, OCI_DEFAULT);
            oci_free_statement($stid);
            oci_close($conn);
            return $aParameterRet;
            break;
    }
}
