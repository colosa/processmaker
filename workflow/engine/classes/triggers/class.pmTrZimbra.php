<?php

/**
 * class.pmTrZimbra.pmFunctions.php
 *
 * ProcessMaker Open Source Edition
 * Copyright (C) 2004 - 2008 Colosa Inc.
 * *
 */
////////////////////////////////////////////////////
// pmTrZimbra PM Functions
//
// Copyright (C) 2007 COLOSA
//
// License: LGPL, see LICENSE
////////////////////////////////////////////////////
use ProcessMaker\Core\System;


/**
 * Triggers that allow ProcessMaker to integrate with Zimbra
 * @class pmZimbra
 * @name Zimbra Triggers v. 0.1
 * @icon /images/triggers/zimbra.gif
 * @className class.pmZimbra.pmFunctions.php
 */
/**
 * @method
 *
 * Get Folder Name and Attributes
 *
 * @name getZimbraFolder
 * @label Get Attributes for specified FolderName
 *
 * @param string | $ServerUrl | Server name and port where Zimbra exists | zimbra.server:port
 * @param string | $username | Valid username to connect to Zimbra server
 * @param string | $preAuthKey | Server Key for SSO authentication
 * @param string | $folderName | Folder Name
 * @param string | $protocol | protpcol server http https
 *
 * @return string | $result | Response |
 *
 */

function getZimbraFolder($ServerUrl, $username, $preAuthKey, $folderName, $protocol = 'http')
{
    $zimbra = new Zimbra($username, $ServerUrl, $preAuthKey , '', $protocol);
    $connectionResult = $zimbra->connect();

    if (!$connectionResult) {
        return "Check userName or Server URL";
    }

    $aXmlArray = array();
    $aXmlArray = $zimbra->getFolder($folderName);
    $sXmlArray = @G::json_decode(@G::json_encode($aXmlArray), 1);

    $serializeResult = serialize($sXmlArray); // serializing the Array for Returning.
    return $serializeResult;
}

/**
 * @method
 *
 * Get Contact List
 *
 * @name getZimbraContactList
 * @label Get Contact Lists from Zimbra Server
 *
 * @param string | $ServerUrl | Server name and port where Zimbra exists | zimbra.server:port
 * @param string | $username| Valid username to connect to Zimbra server
 * @param string | $preAuthKey | Server Key for SSO authentication
 * @param string | $protocol | protpcol server http https
 *
 * @return string | $result | Response |
 *
 */
function getZimbraContactList($ServerUrl, $username, $preAuthKey, $protocol = 'http')
{

    $zimbra = new Zimbra($username, $ServerUrl, $preAuthKey, '', $protocol);
    $connectionResult = $zimbra->connect();

    if (!$connectionResult) {
        return "Check userName or Server URL";
    }

    $sXmlArray = array();
    $sXmlArray = $zimbra->getContacts();
    $aXmlArray = @G::json_decode(@G::json_encode($sXmlArray), 1);

    $serializeResult = serialize($aXmlArray); // serializing the Array for Returning.
    return $serializeResult;
}

/**
 * @method
 *
 * Get Task List
 *
 * @name getZimbraTaskList
 * @label Get Task Lists from Zimbra Server
 *
 * @param string | $ServerUrl | Server name and port where Zimbra exists | zimbra.server:port
 * @param string | $username| Valid username to connect to Zimbra server
 * @param string | $preAuthKey | Server Key for SSO authentication
 * @param string | $protocol | protpcol server http https
 *
 * @return string | $result | Response |
 *
 */
function getZimbraTaskList($ServerUrl, $username, $preAuthKey, $protocol = 'http')
{

    $xXmlArray = array();
    $xXmlArray1 = array();

    $zimbra = new Zimbra($username, $ServerUrl, $preAuthKey, '', $protocol = 'http');
    $connectionResult = $zimbra->connect();

    if (!$connectionResult) {
        return "Check userName or Server URL";
    }

    $sXmlArray = array();
    $sXmlArray = $zimbra->getTasks();
    $aXmlArray = @G::json_decode(@G::json_encode($sXmlArray), 1);

    $serializeResult = serialize($aXmlArray); // serializing the Array for Returning.
    return $serializeResult;
}

/**
 * @method
 *
 * Get Appointment List
 *
 * @name getZimbraAppointmentList
 * @label Get Appointment Lists from Zimbra Server
 *
 * @param string | $ServerUrl | Server name and port where Zimbra exists | zimbra.server:port
 * @param string | $username| Valid username to connect to Zimbra server
 * @param string | $preAuthKey | Server Key for SSO authentication
 * @param string | $protocol | protpcol server http https
 *
 * @return string | $result | Response |
 *
 */
function getZimbraAppointmentList($ServerUrl, $username, $preAuthKey, $protocol = 'http')
{

    $xXmlArray = array();
    $xXmlArray1 = array();

    $zimbra = new Zimbra($username, $ServerUrl, $preAuthKey, '', $protocol);
    $connectionResult = $zimbra->connect();

    if (!$connectionResult) {
        return "Check userName or Server URL";
    }

    $sXmlArray = array();
    $sXmlArray = $zimbra->getAppointments();
    $aXmlArray = @G::json_decode(@G::json_encode($sXmlArray), 1);

    $serializeResult = serialize($aXmlArray); // serializing the Array for Returning.
    return $serializeResult;
}

/**
 * @method
 *
 * Create Folder Name and Attribute
 *
 * @name createZimbraFolder
 * @label Create Specified Folder with Attributes in Briefcase Tab
 *
 * @param string | $ServerUrl | Server name and port where Zimbra exists | zimbra.server:port
 * @param string | $username | Valid username to connect to Zimbra server
 * @param string | $preAuthKey | Server Key for SSO authentication
 * @param string | $folderName | Folder Name
 * @param string | $color | Color of Folder
 * @param string | $protocol | protpcol server http https
 *
 * @return string | $result | Response |
 *
 */
function createZimbraFolder($ServerUrl, $username, $preAuthKey, $folderName, $color, $protocol = 'http')
{
    $serializeOp = array();
    $serializeOp = array('folderName' => $folderName, 'color' => $color);
    $serializeOp1 = serialize($serializeOp);

    $zimbra = new Zimbra($username, $ServerUrl, $preAuthKey, '', $protocol);
    $connectionResult = $zimbra->connect();

    if (!$connectionResult) {
        return "Check userName or Server URL";
    }
    $sXmlArray = $zimbra->addFolder($serializeOp1);
    if ($sXmlArray) {
        return "Folder Created succesfully";
    } else {
        return "A folder with name " . $folderName . " already exist.";
    }
}

/**
 * @method
 *
 * Create Contacts
 *
 * @name createZimbraContacts
 * @label Create Contacts in Address Book
 *
 * @param string | $ServerUrl | Server name and port where Zimbra exists | zimbra.server:port
 * @param string | $username | Valid username to connect to Zimbra server
 * @param string | $preAuthKey | Server Key for SSO authentication
 * @param string | $firstName | First Name
 * @param string | $lastName | Last Name
 * @param string | $email | Email Address
 * @param string | $otherData | BirthDay/Anniversary/Custom
 * @param string | $otherDataValue | Corresponding Date or Value
 * @param string | $protocol | protpcol server http https
 *
 * @return string | $result | Response |
 *
 */
function createZimbraContacts($ServerUrl, $username, $preAuthKey, $firstName, $lastName, $email, $otherData, $otherDataValue, $protocol = 'http')
{

    $serializeOp = array();
    $serializeOp = array('firstName' => $firstName, 'lastName' => $lastName, 'email' => $email, 'otherData' => $otherData, 'otherDataValue' => $otherDataValue);
    $serializeOp1 = serialize($serializeOp);

    $zimbra = new Zimbra($username, $ServerUrl, $preAuthKey, '', $protocol);
    $connectionResult = $zimbra->connect();

    if (!$connectionResult) {
        return "Check userName or Server URL";
    }

    $sXmlArray = $zimbra->addContacts($serializeOp1);
    if ($sXmlArray) {
        return "Contacts Created succesfully";
    } else {
        return "Some Error";
    }
}

/**
 * @method
 *
 * Create Tasks
 *
 * @name createZimbraTask
 * @label Create Task
 *
 * @param string | $ServerUrl | Server name and port where Zimbra exists | zimbra.server:port
 * @param string | $username | Valid username to connect to Zimbra server
 * @param string | $preAuthKey | Server Key for SSO authentication
 * @param string | $subject | Mail Subject
 * @param string | $taskName | Task Name
 * @param string | $friendlyName | Friendly Name of the User
 * @param string | $userEmail | Email Address of the User
 * @param string | $priority | Priority of the Task
 * @param string | $allDay | Is All Day Task
 * @param string | $class | Access Scope of the Class
 * @param string | $location | Location of the task
 * @param string | $dueDate | Due Date of the task
 * @param string | $status | Status of the task
 * @param string | $percent | Percentage of Task Completed
 * @param string | $protocol | protpcol server http https
 *
 * @return string | $result | Response |
 *
 */
function createZimbraTask($ServerUrl, $username, $preAuthKey, $subject, $taskName, $friendlyName, $userEmail, $priority, $allDay, $class, $location, $dueDate, $status, $percent, $protocol = 'http')
{
    $serializeOp = array();
    $serializeOp = array('subject' => $subject, 'taskName' => $taskName, 'friendlyName' => $friendlyName, 'userEmail' => $userEmail, 'priority' => $priority, 'allDay' => $allDay, 'class' => $class, 'location' => $location, 'dueDate' => $dueDate, 'status' => $status, 'percent' => $percent);
    $serializeOp1 = serialize($serializeOp);

    $zimbra = new Zimbra($username, $ServerUrl, $preAuthKey, '', $protocol);

    $connectionResult = $zimbra->connect();
    if (!$connectionResult) {
        return "Check userName or Server URL";
    }

    $sXmlArray = $zimbra->addTask($serializeOp1);
    if ($sXmlArray) {
        return "Task Created succesfully";
    } else {
        return "Error in Creating Task";
    }
}

/**
 * @method
 *
 * Create Appointment
 *
 * @name createZimbraAppointment
 * @label Create Appointment
 *
 * @param string | $ServerUrl | Server name and port where Zimbra exists | zimbra.server:port
 * @param string | $username | Valid username to connect to Zimbra server
 * @param string | $preAuthKey | Server Key for SSO authentication
 * @param string | $subject | Mail Subject
 * @param string | $appointmentName | Appointment Name
 * @param string | $friendlyName | Organizer's Friendly Name
 * @param string | $userEmail | Email Address of the Attendee(s) seperated by ';'
 * @param string | $domainName | Domain Name
 * @param string | $schedule | Schedule of the Appointment
 * @param string | $cutype | Type of Calendar User
 * @param string | $allDay | Is All Day Appointment
 * @param string | $isOrg | Is Organizer
 * @param string | $rsvp | RSVP
 * @param string | $atFriendlyName | Friendly Name of Attendee(s) seperated by ';'
 * @param string | $role | Attendee's Role
 * @param string | $location | Location
 * @param string | $ptst | Paticipation Status of the user
 * @param string | $startDate | Start Date of the Appointment
 * @param string | $endDate | End Date of the Appointment
 * @param string | $tz | Time Zone
 * @param string | $protocol | protpcol server http https
 *
 * @return string | $result | Response |
 *
 */
function createZimbraAppointment($ServerUrl, $username, $preAuthKey, $subject, $appointmentName, $friendlyName, $userEmail, $domainName, $schedule, $cutype, $allDay, $isOrg, $rsvp, $atFriendlyName, $role, $location, $ptst, $startDate, $endDate, $tz = '', $protocol = 'http')
{

    $serializeOp = array();
    $serializeOp = array('username' => $username, 'subject' => $subject, 'appointmentName' => $appointmentName, 'friendlyName' => $friendlyName, 'userEmail' => $userEmail, 'domainName' => $domainName, 'schedule' => $schedule, 'cutype' => $cutype, 'allDay' => $allDay, 'isOrg' => $isOrg, 'rsvp' => $rsvp, 'atFriendlyName' => $atFriendlyName, 'role' => $role, 'location' => $location, 'ptst' => $ptst, 'startDate' => $startDate, 'endDate' => $endDate, 'tz' => $tz);
    $serializeOp1 = serialize($serializeOp);

    $zimbra = new Zimbra($username, $ServerUrl, $preAuthKey, '', $protocol);
    $connectionResult = $zimbra->connect();

    if (!$connectionResult) {
        return "Check userName or Server URL";
    }

    $sXmlArray = $zimbra->addAppointment($serializeOp1);
    if ($sXmlArray) {
        return "Appointment Created succesfully";
    } else {
        return "Error in Creating Appointment";
    }
}

/**
 * @method
 *
 * Upload File/Document to Zimbra Server
 *
 * @name uploadZimbraFile
 * @label Upload File/Document to Zimbra Server
 *
 * @param string | $ServerUrl | Server name and port where Zimbra exists | zimbra.server:port
 * @param string | $username | Valid username to connect to Zimbra server
 * @param string | $preAuthKey | Server Key for SSO authentication
 * @param string | $folderName | Folder Name
 * @param string | $fileLocation | Absolute path of the File to be uploaded.
 * @param string | $protocol | protpcol server http https
 *
 * @return string | $result | Response |
 *
 */
function uploadZimbraFile($ServerUrl, $username, $preAuthKey, $folderName, $fileLocation, $protocol = 'http')
{

    $header_array = array("ENCTYPE" => "multipart/form-data");
    $file = $fileLocation;

    $oZimbraObj = new Zimbra($username, $ServerUrl, $preAuthKey, '', $protocol);
    $connectResult = $oZimbraObj->connect();
    $sAuthToken = $oZimbraObj->auth_token;
    $cookie = array('ZM_AUTH_TOKEN' => $sAuthToken, 'ZM_TEST' => true);
    $cookie = 'ZM_AUTH_TOKEN=' . $sAuthToken. '; ZM_TEST='. true;
    $url = "http://$ServerUrl/service/upload?fmt=raw";
    $params = array (
            'uploadFile' => "@$file"
    );
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    //curl_setopt($ch, CURLOPT_VERBOSE, 1);
    curl_setopt($ch, CURLOPT_COOKIE, $cookie);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
    curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt ($ch, CURLOPT_BINARYTRANSFER, true);
    curl_setopt ($ch, CURLOPT_COOKIESESSION, true);
    curl_setopt ($ch, CURLOPT_FRESH_CONNECT, true);
    curl_setopt ($ch, CURLOPT_HEADER, true);
    curl_setopt ($ch, CURLOPT_NOPROGRESS, false);
    curl_setopt ($ch, CURLOPT_VERBOSE, true);
    curl_setopt ($ch, CURLOPT_HTTPHEADER,$header_array);

    //Apply proxy settings
    $sysConf = System::getSystemConfiguration();
    if ($sysConf['proxy_host'] != '') {
        curl_setopt($ch, CURLOPT_PROXY, $sysConf['proxy_host'] . ($sysConf['proxy_port'] != '' ? ':' . $sysConf['proxy_port'] : ''));
        if ($sysConf['proxy_port'] != '') {
            curl_setopt($ch, CURLOPT_PROXYPORT, $sysConf['proxy_port']);
        }
        if ($sysConf['proxy_user'] != '') {
            curl_setopt($ch, CURLOPT_PROXYUSERPWD, $sysConf['proxy_user'] . ($sysConf['proxy_pass'] != '' ? ':' . $sysConf['proxy_pass'] : ''));
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));
    }

    if ( ! $response = curl_exec($ch)) {
        return "Upload error. Connection Error";
    }

    //G::pr($response);

    $header_size = curl_getinfo($ch,CURLINFO_HEADER_SIZE);
    $result['header'] = substr($response, 0, $header_size);
    $result['body'] = substr( $response, $header_size );
    $result['http_code'] = curl_getinfo($ch,CURLINFO_HTTP_CODE);
    $result['last_url'] = curl_getinfo($ch,CURLINFO_EFFECTIVE_URL);

    $aString = array();
    $aExplode = explode(",", $result['body']);
    $uploadID = substr($aExplode[2], 1, -2);

    curl_close($ch);

       // gettin FOlder ID

    $FolderResult = $oZimbraObj->getFolder($folderName);
    if (isset($FolderResult['id'])) {
        $sFolderID = $FolderResult['id'];
    } else {
        $sFolderID = $FolderResult['folder_attribute_id']['0'];
    }

    $fileNamePath = $fileLocation;
    $fileName = basename($fileNamePath);

    $docDetails = $oZimbraObj->getDocId($sFolderID, $fileName);
    if ($docDetails) {
        $docId = $docDetails['doc_attribute_id'][0];
        $docVersion = $docDetails['doc_attribute_ver'][0];
    }
    $uploadResult = $oZimbraObj->upload($sFolderID, $uploadID, $docVersion, $docId);
    if (isset($uploadResult['error'])) {
        return $uploadResult['error'];
    } else {
        return "The file has been uploaded Successfully";
    }

}

