<?php

/**
 * class.pmTrSharepoint.pmFunctions.php
 *
 * ProcessMaker Open Source Edition
 * Copyright (C) 2004 - 2008 Colosa Inc.
 * *
 */
////////////////////////////////////////////////////
// pmTrSharepoint PM Functions
//
// Copyright (C) 2007 COLOSA
//
// License: LGPL, see LICENSE
////////////////////////////////////////////////////


/**
 * Sharepoint Triggers that allow ProcessMaker to perform common DWS actions
 * @class pmTrSharepoint
 *
 * @name Sharepoint DWS Triggers v. 0.1
 * @icon /images/triggers/sharepoint.gif
 * @className class.pmTrSharepoint.pmFunctions.php
 */

/**
 *
 * @method Create a DWS in Sharepoint server
 *
 * @name createDWS
 * @label Create a DWS in Sharepoint server
 *
 * @param string | $sharepointServer | Server name and port whre DWS wsdl exists, including protocol | http://server:port
 * @param string | $auth | Valid Auth string to connect to server | user:password
 * @param string | $name | Name of DWS
 * @param string | $users | Relevent User of DWS
 * @param string | $title | Title of DWS
 * @param string | $documents | Document that need to be attached to DWS
 *
 * @return string | $result | Response
 *
 */
function createDWS ($sharepointServer, $auth, $name = "", $users = "", $title = "", $documents = "", $result = "")
{
    //print "Trigger: createDWS";
    require_once (PATH_CORE . 'classes' . PATH_SEP . 'triggers' . PATH_SEP . 'class.pmTrSharepoint.php');
    $pmTrSharepoint = new pmTrSharepointClass( $sharepointServer, $auth );
    $beforeResult = $pmTrSharepoint->createDWS( $name, $users, $title, $documents );
    return $beforeResult;
}

/**
 *
 * @method Delete a DWS in Sharepoint server
 *
 * @name deleteDWS
 * @label Delete a DWS in Sharepoint server
 *
 * @param string | $sharepointServer | Server name and port whre DWS wsdl exists, including protocol | http://server:port/_vti_bin/Dws.asmx?WSDL
 * @param string | $auth | Valid Auth string to connect to server | user:password
 * @param string | $dwsname | Name of DWS to be deleted
 *
 * @return string | $result | Response
 *
 */
function deleteDWS ($sharepointServer, $auth, $dwsname)
{
    require_once (PATH_CORE . 'classes' . PATH_SEP . 'triggers' . PATH_SEP . 'class.pmTrSharepoint.php');
    $pmTrSharepoint = new pmTrSharepointClass( $sharepointServer, $auth );
    $beforeResult = $pmTrSharepoint->deleteDWS( $dwsname );
    $newResult = $beforeResult->DeleteDwsResult;
    if ($newResult == '<Result/>') {
        return "Successfully deleted the DWS";
    } else {
        return "Error in deleting the DWS";
    }

}

/**
 *
 * @method Create a folder in a DWS
 *
 * @name createFolderDWS
 * @label Create a folder in a DWS
 *
 * @param string | $sharepointServer | Server name and port whre DWS wsdl exists, including protocol | http://server:port/_vti_bin/Dws.asmx?WSDL
 * @param string | $auth | Valid Auth string to connect to server | user:password
 * @param string | $dwsname | Name of DWS
 * @param string | $dwsFolderName | Name of DWS folder
 *
 * @return string | $result | Response
 *
 */
function createFolderDWS ($sharepointServer, $auth, $dwsname, $dwsFolderName)
{
    require_once (PATH_CORE . 'classes' . PATH_SEP . 'triggers' . PATH_SEP . 'class.pmTrSharepoint.php');
    $pmTrSharepoint = new pmTrSharepointClass( $sharepointServer, $auth );
    $result = $pmTrSharepoint->createFolderDWS( $dwsname, $dwsFolderName );
    var_dump( $result );
    $newResult = $result->CreateFolderResult;
    var_dump( $newResult );
    if (isset( $newResult )) {
        if ($newResult == '<Result/>') {
            return "Folder is created";
        } else {
            return "Folder could not be created";
        }
    }
}

/**
 *
 * @method Delete a DWS folder
 *
 * @name deleteFolderDWS
 * @label Delete a DWS folder
 *
 * @param string | $sharepointServer | Server name and port whre DWS wsdl exists, including protocol | http://server:port/_vti_bin/Dws.asmx?WSDL
 * @param string | $auth | Valid Auth string to connect to server | user:password
 * @param string | $dwsname | Name of DWS
 * @param string | $delDwsFolderName | Name of DWS folder to be deleted
 *
 * @return string | $result | Response
 *
 */

function deleteFolderDWS ($sharepointServer, $auth, $dwsname, $folderName)
{
    require_once (PATH_CORE . 'classes' . PATH_SEP . 'triggers' . PATH_SEP . 'class.pmTrSharepoint.php');
    $pmTrSharepoint = new pmTrSharepointClass( $sharepointServer, $auth );
    $result = $pmTrSharepoint->deleteFolderDWS( $dwsname, $folderName );
    $newResult = $result->DeleteFolderResult;
    if (isset( $newResult )) {
        if ($newResult == '<Result/>') {
            return "Folder is deleted";
        } else {
            return "Folder could not be deleted";
        }
    }
}

/**
 *
 * @method Get DWS data
 *
 * @name getDWSData
 * @label Get DWS data
 *
 * @param string | $sharepointServer | Server name and port whre DWS wsdl exists, including protocol | http://server:port/_vti_bin/Dws.asmx?WSDL
 * @param string | $auth | Valid Auth string to connect to server | user:password
 * @param string | $newFileName | File Name
 * @param string | $dwsname | Name of DWS
 * @param string | $lastUpdate | LastUpdate
 *
 * @return string | $result | Response
 *
 */
function getDWSData ($sharepointServer, $auth, $newFileName, $dwsname, $lastUpdate)
{
    require_once (PATH_CORE . 'classes' . PATH_SEP . 'triggers' . PATH_SEP . 'class.pmTrSharepoint.php');
    $pmTrSharepoint = new pmTrSharepointClass( $sharepointServer, $auth );
    $resultDWSData = $pmTrSharepoint->getDWSData( $newFileName, $dwsname, $lastUpdate );
    if ($resultDWSData) {
        return $resultDWSData;
    } else {
        return "There was some error while getting the DWS Data";
    }
}

/**
 *
 * @method Get DWS meta data
 *
 * @name getDWSMetaData
 * @label Get DWS meta data
 *
 * @param string | $sharepointServer | Server name and port whre DWS wsdl exists, including protocol | http://server:port/_vti_bin/Dws.asmx?WSDL
 * @param string | $auth | Valid Auth string to connect to server | user:password
 * @param string | $newFileName | File Name
 * @param string | $dwsname | Name of DWS
 * @param string | $id | ID
 *
 *
 * @return string | $result | Response
 *
 */
function getDWSMetaData ($sharepointServer, $auth, $newFileName, $dwsname, $id)
{
    require_once (PATH_CORE . 'classes' . PATH_SEP . 'triggers' . PATH_SEP . 'class.pmTrSharepoint.php');
    $pmTrSharepoint = new pmTrSharepointClass( $sharepointServer, $auth );
    $result = $pmTrSharepoint->getDWSMetaData( $newFileName, $dwsname, $id );
    //$newResult = $result->GetDwsMetaDataResult;
    if (isset( $result )) {
        return $result;
    } else {
        return "Document workspace Meta-data not found";
    }

}

/**
 *
 * @method Copy/Upload Documents to DWS folder
 *
 * @name uploadDocumentDWS
 * @label Copy/Upload Documents to DWS folder
 *
 * @param string | $sharepointServer | Server name and port whre DWS wsdl exists, including protocol | http://server:port/_vti_bin/Dws.asmx?WSDL
 * @param string | $auth | Valid Auth string to connect to server | user:password
 * @param string | $dwsname | DWS name in which you want to Upload the Folder
 * @param string | $folderName | Folder Name, dont provide folder name in case upload is in "Shared Directory"
 * @param string | $sourceUrl | Absolute path of the file to upload
 * @param string | $filename | Name of the File to Upload
 *
 * @return string | $result | Response
 *
 */
function uploadDocumentDWS ($sharepointServer, $auth, $dwsname, $folderName, $sourceUrl, $filename)
{
    require_once (PATH_CORE . 'classes' . PATH_SEP . 'triggers' . PATH_SEP . 'class.pmTrSharepoint.php');
    $pmTrSharepoint = new pmTrSharepointClass( $sharepointServer, $auth );
    $beforeResult = $pmTrSharepoint->uploadDocumentDWS( $dwsname, $folderName, $sourceUrl, $filename );

    return $beforeResult;
}

/**
 *
 * @method Download documents from DWS folder
 *
 * @name downloadDocumentDWS
 * @label Download DWS Documents
 *
 * @param string | $sharepointServer | Server name and port whre DWS wsdl exists, including protocol | http://server:port/_vti_bin/Dws.asmx?WSDL
 * @param string | $auth | Valid Auth string to connect to server | user:password
 * @param string | $dwsname | Name of DWS
 * @param string | $fileName | File to be downloaded
 * @param string | $fileLocation | Location to be downloaded into
 *
 * @return string | $result | Response
 *
 */
function downloadDocumentDWS ($sharepointServer, $auth, $dwsname, $fileName, $fileLocation)
{
    require_once (PATH_CORE . 'classes' . PATH_SEP . 'triggers' . PATH_SEP . 'class.pmTrSharepoint.php');
    $pmTrSharepoint = new pmTrSharepointClass( $sharepointServer, $auth );
    $result = $pmTrSharepoint->downloadDocumentDWS( $dwsname, $fileName, $fileLocation );
    if (isset( $result )) {
        return "Document downloaded";
    } else {
        return "Document cannot be downloaded";
    }
}

/**
 *
 * @method Get DWS Folder items
 *
 * @name getDWSFolderItems
 * @label Get DWS Folder items
 *
 * @param string | $sharepointServer | Server name and port whre DWS wsdl exists, including protocol | http://server:port/_vti_bin/Dws.asmx?WSDL
 * @param string | $auth | Valid Auth string to connect to server | user:password
 * @param string | $dwsname | Name of the DWS
 * @param string | $strFolderUrl | Folder URL
 *
 * @return string | $result | Response
 *
 */
function getDWSFolderItems ($sharepointServer, $auth, $dwsname, $strFolderUrl)
{
    require_once (PATH_CORE . 'classes' . PATH_SEP . 'triggers' . PATH_SEP . 'class.pmTrSharepoint.php');
    $pmTrSharepoint = new pmTrSharepointClass( $sharepointServer, $auth );
    $result = $pmTrSharepoint->getDWSFolderItems( $dwsname, $strFolderUrl );
    if (isset( $result )) {
        return $result;
    } else {
        return "Folder does not exist";
    }
}

/**
 *
 * @method Get DWS Document Versions
 *
 * @name getDWSDocumentVersions
 * @label Get DWS Document Versions
 *
 * @param string | $sharepointServer | Server name and port whre DWS wsdl exists, including protocol | http://server:port/_vti_bin/Dws.asmx?WSDL
 * @param string | $auth | Valid Auth string to connect to server | user:password
 * @param string | $newFileName | Name of New File
 * @param string | $dwsname | Name of DWS
 *
 * @return string | $result | Response
 *
 */
function getDWSDocumentVersions ($sharepointServer, $auth, $newFileName, $dwsname)
{
    require_once (PATH_CORE . 'classes' . PATH_SEP . 'triggers' . PATH_SEP . 'class.pmTrSharepoint.php');
    $pmTrSharepoint = new pmTrSharepointClass( $sharepointServer, $auth );
    $result = $pmTrSharepoint->getDWSDocumentVersions( $newFileName, $dwsname );
    if (isset( $result->GetVersionsResult )) {
        /*
                         * Code to get the Document's Version/s
                         */
        $xml = $result->GetVersionsResult->any; // in Result we get string in Xml format
        $xmlNew = simplexml_load_string( $xml ); // used to parse string to xml
        $xmlArray = @G::json_decode( @G::json_encode( $xmlNew ), 1 ); // used to convert Objects to array
        $resultCount = count( $xmlArray['result'] );
        for ($i = 0; $i < $resultCount; $i ++) {
            $version[] = $xmlArray['result'][$i]['@attributes']['version'];
        }
        $serializeResult = serialize( $version ); // serializing the Array for Returning.
        return $serializeResult;
    } else {
        return "No version found";
    }
}

/**
 *
 * @method Delete DWS Document Version
 *
 * @name deleteDWSDocumentVersion
 * @label Delete DWS Document Version
 *
 * @param string | $sharepointServer | Server name and port whre DWS wsdl exists, including protocol | http://server:port/_vti_bin/Dws.asmx?WSDL
 * @param string | $auth | Valid Auth string to connect to server | user:password
 * @param string | $newFileName | Name of the file
 * @param string | $dwsname | Name of DWS
 * @param string | $versionNum | Version No.
 *
 * @return string | $result | Response
 *
 */
function deleteDWSDocumentVersion ($sharepointServer, $auth, $newFileName, $dwsname, $versionNum)
{
    require_once (PATH_CORE . 'classes' . PATH_SEP . 'triggers' . PATH_SEP . 'class.pmTrSharepoint.php');
    $pmTrSharepoint = new pmTrSharepointClass( $sharepointServer, $auth );
    $result = $pmTrSharepoint->deleteDWSDocVersion( $newFileName, $dwsname, $versionNum );
    return $result;
}

/**
 *
 * @method Delete all DWS Document Versions
 *
 * @name deleteDWSAllDocumentVersion
 * @label Delete all DWS Document Versions
 *
 * @param string | $sharepointServer | Server name and port whre DWS wsdl exists, including protocol | http://server:port/_vti_bin/Dws.asmx?WSDL
 * @param string | $auth | Valid Auth string to connect to server | user:password
 * @param string | $newFileName | Name of File
 * @param string | $dwsname | Name of DWS
 *
 * @return string | $result | Response
 *
 */
function deleteDWSAllDocumentVersion ($sharepointServer, $auth, $newFileName, $dwsname)
{
    require_once (PATH_CORE . 'classes' . PATH_SEP . 'triggers' . PATH_SEP . 'class.pmTrSharepoint.php');
    $pmTrSharepoint = new pmTrSharepointClass( $sharepointServer, $auth );
    $result = $pmTrSharepoint->deleteAllDWSDocVersion( $newFileName, $dwsname );
    return $result;
}

