<?php

use ProcessMaker\Core\System;

/**
 * class.pmSugar.pmFunctions.php
 *
 * ProcessMaker Open Source Edition
 * Copyright (C) 2004 - 2008 Colosa Inc.
 * *
 */

////////////////////////////////////////////////////
// pmSugar PM Functions
//
// Copyright (C) 2007 COLOSA
//
// License: LGPL, see LICENSE
////////////////////////////////////////////////////


function getSoapClientOptions ()
{
    $options = array ('trace' => 1);

    //Apply proxy settings
    $sysConf = System::getSystemConfiguration();
    if ($sysConf['proxy_host'] != '') {
        $options['proxy_host'] = $sysConf['proxy_host'];
        if ($sysConf['proxy_port'] != '') {
            $options['proxy_port'] = $sysConf['proxy_port'];
        }
        if ($sysConf['proxy_user'] != '') {
            $options['proxy_login'] = $sysConf['proxy_user'];
        }
        if ($sysConf['proxy_pass'] != '') {
            $options['proxy_password'] = $sysConf['proxy_pass'];
        }
    }

    return $options;
}

/**
 * This collection of triggers allows to interact by getting and sending information to SugarCRM
 * @class pmSugar
 *
 * @name Sugar CRM Triggers
 * @icon /images/triggers/icon_SugarCRM.gif
 * @className class.pmSugar.pmFunctions.php
 */

function sugarLogin ($sugarSoap, $user, $password)
{
    $client = new SoapClient( $sugarSoap, getSoapClientOptions() );
    $auth_array = array ('user_auth' => array ('user_name' => $user,'password' => G::encryptOld( $password ),'version' => '1.0') );
    $login_results = $client->__SoapCall( 'login', $auth_array );
    $session_id = $login_results->id;
    $user_guid = $client->__SoapCall( 'get_user_id', array ($session_id) );
    return $session_id;
}

function objectToArray ($object)
{
    if (! is_object( $object ) && ! is_array( $object )) {
        return $object;
    }
    if (is_object( $object )) {
        $object = get_object_vars( $object );
    }
    return array_map( "objectToArray", $object );
}

/**
 *
 * @method
 *
 * Gets SugarCRM entry using get_entry web service.
 *
 * @name GetSugarEntry
 * @label Get SugarCRM Entry
 *
 * @param string | $sugarSoap | Sugar SOAP URL | http://www.example.com/sugar/soap.php?wsdl [^]
 * @param string | $user | User
 * @param string | $password | Password
 * @param string | $module | The name of the module from which to retrieve records.
 * @param string | $id | The SugarBean’s ID.
 * @param string | $selectedFields | Optional. The list of fields to be returned in the results.
 * @param string | $linkNameToFieldsArray | A list of link names and the fields to be returned for each link name.
 * @param string | $resultType=array | Result type (array or object)
 *
 * @return array/object | $sugarEntries | Sugar Entries (array or object) |
 *
 */

function GetSugarEntry ($sugarSoap, $user, $password, $module, $id, $selectFields, $linkNameToFieldsArray, $resultType = 'array')
{
    $sessionId = sugarLogin( $sugarSoap, $user, $password );
    $client = new SoapClient( $sugarSoap, getSoapClientOptions() );
    $request_array = array ('session' => $sessionId,'module_name' => $module,'id' => $id,'select_fields' => $select_fields,'link_name_to_fields_array' => $linkNameToFieldsArray);
    $sugarEntry = $client->__SoapCall( 'get_entry', $request_array );

    if ($resultType == 'array') {
        $sugarEntry = objectToArray( $sugarEntry );
    }

    return $sugarEntry;
}

/**
 *
 * @method
 *
 * Gets SugarCRM entries from the indicated module.
 *
 * @name GetSugarEntries
 * @label Get SugarCRM Entries
 *
 * @param string | $sugarSoap | Sugar SOAP URL | http://www.example.com/sugar/soap.php?wsdl
 * @param string | $user | User
 * @param string | $password | Password
 * @param string | $module | Module
 * @param string | $query | Query
 * @param string | $orderBy | Order By
 * @param string | $selectedFields | Selected Fields
 * @param string | $maxResults=50 | Max Results
 * @param string | $resultType=array | Result type (array or object)
 *
 * @return array/object | $sugarEntries | Sugar Entries (array or object) |
 *
 */

function GetSugarEntries ($sugarSoap, $user, $password, $module, $query, $orderBy, $selectedFields, $maxResults, $resultType = "array")
{
    $sessionId = sugarLogin( $sugarSoap, $user, $password );
    $client = new SoapClient( $sugarSoap, getSoapClientOptions() );
    $request_array = array ('session' => $sessionId,'module_name' => $module,'query' => $query,'order_by' => $orderBy,'offset' => "",'select_fields' => "",'max_result' => $maxResults);
    $sugarEntriesO = $client->__SoapCall( 'get_entry_list', $request_array );

    switch ($resultType) {
        case 'array':
            $sugarEntries = objectToArray( $sugarEntriesO );
            break;
        case 'object':
            $sugarEntries = $sugarEntriesO;
            break;
        default:
            $sugarEntries = objectToArray( $sugarEntries );
    }

    return $sugarEntries;

}

/**
 *
 * @method
 *
 * Gets SugarCRM entries from the Calls module
 *
 * @name GetSugarCalls
 * @label Gets SugarCRM entries from the Calls module
 *
 * @param string | $sugarSoap | Sugar SOAP URL | http://www.example.com/sugar/soap.php?wsdl
 * @param string | $user | User
 * @param string | $password | Password
 * @param string | $query | Query
 * @param string | $orderBy | Order By
 * @param string | $selectedFields | Selected Fields
 * @param string | $maxResults=50 | Max Results
 * @param string | $resultType=array | Result type (array or object)
 *
 * @return array/object | $sugarCalls | Sugar Calls (array or object) |
 *
 */

function GetSugarCalls ($sugarSoap, $user, $password, $query, $orderBy, $selectedFields, $maxResults, $resultType = "array")
{
    $module = "Calls";
    return GetSugarEntries( $sugarSoap, $user, $password, $module, $query, $orderBy, $selectedFields, $maxResults, $resultType );
}

/**
 *
 * @method
 *
 * Gets SugarCRM entries from the Leads module.
 *
 * @name GetSugarLeads
 * @label Gets SugarCRM entries from the Leads module.
 *
 * @param string | $sugarSoap | Sugar SOAP URL | http://www.example.com/sugar/soap.php?wsdl
 * @param string | $user | User
 * @param string | $password | Password
 * @param string | $query | Query
 * @param string | $orderBy | Order By
 * @param string | $selectedFields | Selected Fields
 * @param string | $maxResults=50 | Max Results
 * @param string | $resultType=array | Result type (array or object)
 *
 * @return array/object | $sugarLeads | Sugar Leads (array or object) |
 *
 */

function GetSugarLeads ($sugarSoap, $user, $password, $query, $orderBy, $selectedFields, $maxResults, $resultType = "array")
{
    $module = "Leads";
    return GetSugarEntries( $sugarSoap, $user, $password, $module, $query, $orderBy, $selectedFields, $maxResults, $resultType );
}

/**
 *
 * @method
 *
 * Gets SugarCRM entries from the Contacts module.
 *
 * @name GetSugarContacts
 * @label Gets SugarCRM entries from the Contacts module.
 *
 * @param string | $sugarSoap | Sugar SOAP URL | http://www.example.com/sugar/soap.php?wsdl
 * @param string | $user | User
 * @param string | $password | Password
 * @param string | $query | Query
 * @param string | $orderBy | Order By
 * @param string | $selectedFields | Selected Fields
 * @param string | $maxResults=50 | Max Results
 * @param string | $resultType=array | Result type (array or object)
 *
 * @return array/object | $sugarContacts | Sugar Contacts (array or object) |
 *
 */

function GetSugarContacts ($sugarSoap, $user, $password, $query, $orderBy, $selectedFields, $maxResults, $resultType = "array")
{
    $module = "Contacts";
    return GetSugarEntries( $sugarSoap, $user, $password, $module, $query, $orderBy, $selectedFields, $maxResults, $resultType );
}

/**
 *
 * @method
 *
 * Gets SugarCRM entries from the Opportunities module.
 *
 * @name GetSugarOpportunities
 * @label Gets SugarCRM entries from the Opportunities module.
 *
 * @param string | $sugarSoap | Sugar SOAP URL | http://www.example.com/sugar/soap.php?wsdl
 * @param string | $user | User
 * @param string | $password | Password
 * @param string | $query | Query
 * @param string | $orderBy | Order By
 * @param string | $selectedFields | Selected Fields
 * @param string | $maxResults=50 | Max Results
 * @param string | $resultType=array | Result type (array or object)
 *
 * @return array/object | $sugarAccount | Sugar Opportunities (array or object) |
 *
 */

function GetSugarOpportunities ($sugarSoap, $user, $password, $query, $orderBy, $selectedFields, $maxResults, $resultType = "array")
{
    $module = "Opportunities";
    return GetSugarEntries( $sugarSoap, $user, $password, $module, $query, $orderBy, $selectedFields, $maxResults, $resultType );
}

/**
 *
 * @method
 *
 * Gets SugarCRM entries from the Account module.
 *
 * @name GetSugarAccount
 * @label Gets SugarCRM entries from the Account module.
 *
 * @param string | $sugarSoap | Sugar SOAP URL | http://www.example.com/sugar/soap.php?wsdl
 * @param string | $user | User
 * @param string | $password | Password
 * @param string | $query | Query
 * @param string | $orderBy | Order By
 * @param string | $selectedFields | Selected Fields
 * @param string | $maxResults=50 | Max Results
 * @param string | $resultType=array | Result type (array or object)
 *
 * @return array/object | $sugarAccount | Sugar Opportunities (array or object) |
 *
 */

function GetSugarAccount ($sugarSoap, $user, $password, $query, $orderBy, $selectedFields, $maxResults, $resultType = "array")
{
    $module = "Accounts";
    return GetSugarEntries( $sugarSoap, $user, $password, $module, $query, $orderBy, $selectedFields, $maxResults, $resultType );
}

/**
 *
 * @method
 *
 * Creates SugarCRM entries from the Account module.
 *
 * @name CreateSugarAccount
 *
 * @label Creates SugarCRM entries from the Account module.
 *
 * @param string | $sugarSoap | Sugar SOAP URL | http://www.example.com/sugar/soap.php?wsdl
 * @param string | $user | User
 * @param string | $password | Password
 * @param string | $name | Account name
 * @param string | $resultType=array | Result type (array or object)
 *
 * @return array/object | $sugarAccount | Sugar Opportunities (array or object) |
 *
 */
function CreateSugarAccount ($sugarSoap, $user, $password, $name, $resultType = "array")
{

    $module = "Accounts";
    $sessionId = sugarLogin( $sugarSoap, $user, $password );
    $client = new SoapClient( $sugarSoap, getSoapClientOptions() );
    $request_array = array ('session' => $sessionId,'module_name' => $module,'name_value_list' => array (array ("name" => 'name',"value" => $name)));
    $sugarEntriesO = $client->__SoapCall( 'set_entry', $request_array );
    $account_id = $sugarEntriesO->id;

    switch ($resultType) {
        case 'array':
            $sugarEntries = objectToArray( $sugarEntriesO );
            break;
        case 'object':
            $sugarEntries = $sugarEntries;
            break;
        default:
            $sugarEntries = objectToArray( $sugarEntries );
    }
    //return $sugarEntries;
    return $account_id;
}

/**
 *
 * @method
 *
 * Creates SugarCRM entries from the Contacts module
 *
 * @name CreateSugarContact
 *
 * @label Creates SugarCRM entries from the Contacts module
 *
 * @param string | $sugarSoap | Sugar SOAP URL | http://www.example.com/sugar/soap.php?wsdl
 * @param string | $user | User
 * @param string | $password | Password
 * @param string | $first_name | First Name
 * @param string | $last_name | Last Name
 * @param string | $email | Email
 * @param string | $title | Title
 * @param string | $phone | Phone Work
 * @param string | $account_id | Valid id account
 * @param string | $resultType=array | Result type (array or object)
 *
 * @return array/object | $sugarContact | Sugar Opportunities (array or object) |
 *
 */
function CreateSugarContact ($sugarSoap, $user, $password, $first_name, $last_name, $email, $title, $phone, $account_id, $resultType = "array")
{
    $module = "Contacts";
    /*    $aValue =  array(
        array("name" => 'id',           "value" => G::generateUniqueID()),
        array("name" => 'first_name',   "value" => $first_name),
        array("name" => 'last_name',    "value" => $last_name),
    );
    */
    $sessionId = sugarLogin( $sugarSoap, $user, $password );
    $client = new SoapClient( $sugarSoap, getSoapClientOptions() );

    $request_array = array ('session' => $sessionId,'module_name' => $module,array (array ("name" => 'first_name',"value" => $first_name
    ),array ("name" => 'last_name',"value" => $last_name
    ),array ("name" => 'email1',"value" => $email
    ),array ("name" => 'title',"value" => $title
    ),array ("name" => 'phone_work',"value" => $phone
    ),
    //   array("name" => 'account_id',"value" => '8cd10a60-101f-4363-1e0b-4cfd4106bd7e')
    array ("name" => 'account_id',"value" => $account_id
    )));

    $sugarEntriesO = $client->__SoapCall( 'set_entry', $request_array );

    switch ($resultType) {
        case 'array':
            $sugarEntries = objectToArray( $sugarEntriesO );
            break;
        case 'object':
            $sugarEntries = $sugarEntries;
            break;
        default:
            $sugarEntries = objectToArray( $sugarEntries );
    }
    return $sugarEntries;
}

/**
 *
 * @method
 *
 * Creates SugarCRM entries from the Opportunities module.
 *
 * @name CreateSugarOpportunity
 *
 * @label Creates SugarCRM entries from the Opportunities module.
 *
 * @param string | $sugarSoap | Sugar SOAP URL | http://www.example.com/sugar/soap.php?wsdl
 * @param string | $user | User
 * @param string | $password | Password
 * @param string | $name | Name
 * @param string | $account_id | Valid id account
 * @param string | $amount | Amount
 * @param string | $date_closed | Date Closed
 * @param string | $sales_stage | Prospecting, Qualification, Needs Analysis, Value Proposition, Id. Decision Makers, Perception Analysis, Proposal/Price Quote, Negotiation/Review, Closed Won, Closed Lost
 * @param string | $resultType=array | Result type (array or object)
 *
 * @return array/object | $sugarOpportunity | Sugar Opportunities (array or object) |
 *
 */
function CreateSugarOpportunity ($sugarSoap, $user, $password, $name, $account_id, $amount, $date_closed, $sales_stage, $resultType = "array")
{
    // * @param string | $account_id | Account Id
    $module = "Opportunities";

    /*  $aValue =  array(
        array("name" => 'id',           "value" => G::generateUniqueID()),
        array("name" => 'name',         "value" => $name),
        array("name" => 'account_name', "value" => $account_name),
        array("name" => 'amount',       "value" => $amount),
        array("name" => 'date_closed',  "value" => $date_closed),
        array("name" => 'sales_stage',  "value" => $sales_stage)
        );*/

    $sessionId = sugarLogin( $sugarSoap, $user, $password );
    $client = new SoapClient( $sugarSoap, getSoapClientOptions() );

    $request_array = array ('session' => $sessionId,'module_name' => $module,'name_value_list' => array (array ('name' => 'name','value' => $name
    ),array ("name" => 'account_id',"value" => $account_id
    ),array ('name' => 'amount','value' => $amount
    ),array ('name' => 'date_closed','value' => $date_closed
    ),array ('name' => 'sales_stage','value' => $sales_stage
    )
    )
    );

    $sugarEntriesO = $client->__SoapCall( 'set_entry', $request_array );

    switch ($resultType) {
        case 'array':
            $sugarEntries = objectToArray( $sugarEntriesO );
            break;
        case 'object':
            $sugarEntries = $sugarEntries;
            break;
        default:
            $sugarEntries = objectToArray( $sugarEntries );
    }
    return $sugarEntries;
}

/**
 *
 * @method
 *
 * Creates SugarCRM entries from the Leads module
 *
 * @name CreateSugarLeads
 *
 * @label Creates SugarCRM entries from the Leads module
 *
 * @param string | $sugarSoap | Sugar SOAP URL | http://www.example.com/sugar/soap.php?wsdl
 * @param string | $user | User
 * @param string | $password | Password
 * @param string | $first_name | First Name
 * @param string | $last_name | Last Name
 * @param string | $email | Email
 * @param string | $title | Title
 * @param string | $phone | Phone Work
 * @param string | $account_id | Valid id account
 * @param string | $resultType=array | Result type (array or object)
 *
 * @return array/object | $sugarContact | Sugar Opportunities (array or object) |
 *
 */
function CreateSugarLeads ($sugarSoap, $user, $password, $first_name, $last_name, $email, $title, $phone, $account_id, $resultType = "array")
{

    $module = "Leads";
    $sessionId = sugarLogin( $sugarSoap, $user, $password );
    $client = new SoapClient( $sugarSoap, getSoapClientOptions() );

    $request_array = array ('session' => $sessionId,'module_name' => $module,array (array ("name" => 'first_name',"value" => $first_name
    ),array ("name" => 'last_name',"value" => $last_name
    ),array ("name" => 'email1',"value" => $email
    ),array ("name" => 'title',"value" => $title
    ),array ("name" => 'phone_work',"value" => $phone
    ),
    //   array("name" => 'account_id',"value" => '8cd10a60-101f-4363-1e0b-4cfd4106bd7e')
    array ("name" => 'account_id',"value" => $account_id
    )
    )
    );

    $sugarEntriesO = $client->__SoapCall( 'set_entry', $request_array );

    switch ($resultType) {
        case 'array':
            $sugarEntries = objectToArray( $sugarEntriesO );
            break;
        case 'object':
            $sugarEntries = $sugarEntries;
            break;
        default:
            $sugarEntries = objectToArray( $sugarEntries );
    }
    return $sugarEntries;
}

