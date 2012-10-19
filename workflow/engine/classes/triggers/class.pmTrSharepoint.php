<?php

/**
 * class.pmTrSharepoint.php
 */
G::LoadSystem( "soapNtlm" );

class wscaller
{

    private $wsdlurl;
    private $soapObj;
    private $client;
    private $auth;
    private $clientStream;

    function setAuthUser ($auth)
    {
        //print "<br>- auth Setup";
        $this->auth = $auth;
    }

    function setwsdlurl ($wsdl)
    {
        //print "<br>- wsdl Setup";
        $this->wsdlurl = $wsdl;
        //var_dump($wsdl);
    }

    function loadSOAPClient ()
    {
        try {
            // we unregister the current HTTP wrapper
            stream_wrapper_unregister( 'http' );
            // we register the new HTTP wrapper
            //$client = new PMServiceProviderNTLMStream($this->auth);
            PMServiceProviderNTLMStream::setAuthStream( $this->auth );
            stream_wrapper_register( 'http', 'PMServiceProviderNTLMStream' ) or die( "Failed to register protocol" );

            //     $this->client = new PMServiceNTLMSoapClient($this->wsdlurl, array('trace' => 1, 'auth' => $this->auth));// Hugo's code
            $this->client = new PMServiceNTLMSoapClient( $this->wsdlurl, array ('trace' => 1
            ) ); // Ankit's Code
            $this->client->setAuthClient( $this->auth );
            return true;
        } catch (Exception $e) {
            echo $e;
            exit();
        }
    }

    function callWsMethod ($methodName, $paramArray)
    {

        try {
            if ($methodName == 'DeleteDws' || $methodName == 'GetListCollection') {
                $strResult = "";
                $strResult = $this->client->$methodName( $paramArray = "" );
                return $strResult;
            } else {
                $strResult = "";
                $strResult = $this->client->$methodName( $paramArray );
                return $strResult;
            }
        } catch (SoapFault $fault) {
            echo 'Fault code: ' . $fault->faultcode;
            echo 'Fault string: ' . $fault->faultstring;
        }
        stream_wrapper_restore( 'http' );
    }
}

class DestinationUrlCollection
{

    public $string;
}

;

class FieldInformation
{
}

class FieldInformationCollection
{
    public $FieldInformation;
}

class pmTrSharepointClass
{
    function __construct ($server, $auth)
    {
        set_include_path( PATH_PLUGINS . 'pmTrSharepoint' . PATH_SEPARATOR . get_include_path() );
        $this->server = $server;
        $this->auth = $auth;
        $this->dwsObj = new wscaller();
        $this->dwsObj->setAuthUser( $this->auth );
    }

    function createDWS ($name, $users, $title, $documents)
    {
        //print "<br>- Method createDWS";
        $this->dwsObj->setwsdlurl( $this->server . "/_vti_bin/Dws.asmx?WSDL" );

        $this->dwsObj->loadSOAPClient();

        $paramArray = array ('name' => '','users' => '','title' => $name,'documents' => ''
        );

        $methodName = 'CreateDws';

        $result = $this->dwsObj->callWsMethod( $methodName, $paramArray );
        $xml = $result->CreateDwsResult; // in Result we get string in Xml format
        $xmlNew = simplexml_load_string( $xml ); // used to parse string to xml
        $xmlArray = @G::json_decode( @G::json_encode( $xmlNew ), 1 ); // used to convert Objects to array
        $dwsUrl = $xmlArray['Url'];
        return "Dws with following Url is created:$dwsUrl";

        /* $newResult = $result->CreateDwsResult;
           $needleStart='<Url>';
           $urlStartPos = strpos($newResult, $needleStart);
           $urlStart = $urlStartPos + 5;
           $needleEnd='</Url>';
           $urlEndPos = strpos($newResult, $needleEnd);
           $length = $urlEndPos - $urlStart;
           $result = substr($newResult, $urlStart, $length);
           return $result; */
    }

    function deleteDWS ($dwsname)
    {
        //print "<br>- Method createDWS";
        $url = $this->server . "/" . $dwsname . "/_vti_bin/Dws.asmx?WSDL";
        $this->dwsObj->setwsdlurl( $url );

        $this->dwsObj->loadSOAPClient();
        $paramArray = null;
        $methodName = 'DeleteDws';
        $result = $this->dwsObj->callWsMethod( $methodName, $paramArray = null );
        var_dump( $result );
        return $result;

    }

    function createFolderDWS ($dwsname, $dwsFolderName)
    {
        //print "<br>- Method createDWS";
        $this->dwsObj->setwsdlurl( $this->server . "/" . $dwsname . "/_vti_bin/Dws.asmx?WSDL" );

        $this->dwsObj->loadSOAPClient();

        $url = "Shared Documents/$dwsFolderName";
        $paramArray = array ('url' => $url
        );

        # $paramArray = array('name' => '', 'users' => '', 'title' => $name, 'documents' => '');


        $methodName = 'CreateFolder';

        $result = $this->dwsObj->callWsMethod( $methodName, $paramArray );
        var_dump( $result );
        return $result;
    }

    function deleteFolderDWS ($dwsname, $folderName)
    {
        //print "<br>- Method createDWS";
        $this->dwsObj->setwsdlurl( $this->server . "/" . $dwsname . "/_vti_bin/Dws.asmx?WSDL" );

        $this->dwsObj->loadSOAPClient();

        $url = "Shared Documents/$folderName";
        $paramArray = array ('url' => $url
        );

        # $paramArray = array('name' => '', 'users' => '', 'title' => $name, 'documents' => '');


        $methodName = 'DeleteFolder';

        $result = $this->dwsObj->callWsMethod( $methodName, $paramArray );
        var_dump( $result );
        return $result;
    }

    function findDWSdoc ($dwsname, $guid)
    {
        //print "<br>- Method createDWS";
        $this->dwsObj->setwsdlurl( $this->server . $dwsName . "/_vti_bin/Dws.asmx?WSDL" );

        $this->dwsObj->loadSOAPClient();

        $paramArray = array ('id' => '$guid'
        );

        $methodName = 'FindDwsDoc';

        $result = $this->dwsObj->callWsMethod( $methodName, $paramArray );
        var_dump( $result );
    }

    function getDWSData ($newFileName, $dwsname, $lastUpdate)
    {
        //print "<br>- Method getDWSData<br />";
        $url = $this->server . "/" . $dwsname . "/_vti_bin/Dws.asmx?WSDL";
        $this->dwsObj->setwsdlurl( $url );
        if ($this->dwsObj->loadSOAPClient()) {
            $doc = "Shared Documents";
            $paramArray = array ('document' => '','lastUpdate' => ''
            );
            $methodName = 'GetDwsData';
            $result = $this->dwsObj->callWsMethod( $methodName, $paramArray );
            var_dump( $result );
            $sResult = $result->GetDwsDataResult;
            /*            $xmlNew = simplexml_load_string($sResult);// used to parse string to xml
              $xmlArray = @G::json_decode(@G::json_encode($xmlNew),1);// used to convert Objects to array */
            $serializeResult = serialize( $sResult ); // serializing the Array for Returning.
            var_dump( $serializeResult );
            return $serializeResult;
        } else {
            return "The enter the Correct Dws Name";
        }
    }

    function uploadDocumentDWS ($dwsname, $folderName, $sourceUrl, $filename)
    {
        //print "<br>- Method createDWS";
        $url = $this->server . "/" . $dwsname . "/_vti_bin/Copy.asmx?WSDL";
        $this->dwsObj->setwsdlurl( $url );
        $this->dwsObj->loadSOAPClient();

        $destUrlObj = new DestinationUrlCollection();
        if ($folderName != '') {
            $destUrl = $this->server . "/$dwsname/Shared%20Documents/$folderName/$filename";
        } else {
            $destUrl = $this->server . "/$dwsname/Shared%20Documents/$filename";
        }
        $destUrlObj->string = $destUrl;

        $fieldInfoObj = new FieldInformation();

        $fieldInfoCollObj = new FieldInformationCollection();
        $fieldInfoCollObj->FieldInformation = $fieldInfoObj;

        $imgfile = $sourceUrl . "/" . $filename;
        $filep = fopen( $imgfile, "r" );
        $fileLength = filesize( $imgfile );
        $content = fread( $filep, $fileLength );
        //$content = base64_encode($content);


        $paramArray = array ('SourceUrl' => $imgfile,'DestinationUrls' => $destUrlObj,'Fields' => $fieldInfoCollObj,'Stream' => $content
        );
        $methodName = 'CopyIntoItems';
        $result = $this->dwsObj->callWsMethod( $methodName, $paramArray );
        var_dump( $result );
        $newResult = $result->Results->CopyResult->ErrorCode;
        if ($newResult == 'Success') {
            return "The document has been uploaded Successfully";
        } else {
            return "Could not Upload the Document due to some Error";
        }
    }

    function getDWSMetaData ($newFileName, $dwsname, $id)
    {
        //print "<br>- Method createDWS";
        $url = $this->server . "/" . $dwsname . "/_vti_bin/Dws.asmx?WSDL";
        $this->dwsObj->setwsdlurl( $url );

        $this->dwsObj->loadSOAPClient();

        $doc = "Shared Documents/$newFileName";
        $paramArray = array ('document' => $doc,'id' => '','minimal' => false
        );

        $methodName = 'GetDwsMetaData';

        $result = $this->dwsObj->callWsMethod( $methodName, $paramArray );
        $sResult = $result->GetDwsMetaDataResult;
        $errorReturn = strpos( $sResult, "Error" );
        if (isset( $sResult ) && ! $errorReturn) {
            $serializeResult = serialize( $sResult ); // serializing the Array for Returning.
            var_dump( $serializeResult );
            return $serializeResult;
        } else {
            return $sResult;
        }
    }

    function getDWSDocumentVersions ($newFileName, $dwsname)
    {
        //print "<br>- Method createDWS";
        $this->dwsObj->setwsdlurl( $this->server . "/" . $dwsname . "/_vti_bin/Versions.asmx?WSDL" );

        $this->dwsObj->loadSOAPClient();

        $doc = "Shared Documents/$newFileName";
        $paramArray = array ('fileName' => $doc
        );

        $methodName = 'GetVersions';

        $result = $this->dwsObj->callWsMethod( $methodName, $paramArray );
        var_dump( $result );
        return $result;
    }

    function deleteDWSDocVersion ($newFileName, $dwsname, $versionNum)
    {
        //print "<br>- Method createDWS";
        $this->dwsObj->setwsdlurl( $this->server . "/" . $dwsname . "/_vti_bin/Versions.asmx?WSDL" );

        $this->dwsObj->loadSOAPClient();

        $doc = "Shared Documents/$newFileName";
        $paramArray = array ('fileName' => $doc,'fileVersion' => $versionNum
        );

        $methodName = 'DeleteVersion';

        $result = $this->dwsObj->callWsMethod( $methodName, $paramArray );
        if ($result) {
            $sResult = $result->DeleteVersionResult->any;
            $xmlNew = simplexml_load_string( $sResult ); // used to parse string to xml
            $xmlArray = @G::json_decode( @G::json_encode( $xmlNew ), 1 ); // used to convert Objects to array
            $versionCount = count( $xmlArray['result'] );

            if ($versionCount > 1) {
                for ($i = 0; $i < $versionCount; $i ++) {
                    $version[] = $xmlArray['result'][$i]['@attributes']['version'];
                }
            } else {
                $version[] = $xmlArray['result']['@attributes']['version'];
            }

            $serializeResult = serialize( $version ); // serializing the Array for Returning.
            var_dump( $serializeResult );
            return $serializeResult;
        } else {
            return "The given Version could not be deleted.";
        }
    }

    function deleteAllDWSDocVersion ($newFileName, $dwsname)
    {
        //print "<br>- Method createDWS";
        $this->dwsObj->setwsdlurl( $this->server . "/" . $dwsname . "/_vti_bin/Versions.asmx?WSDL" );

        $this->dwsObj->loadSOAPClient();

        $doc = "Shared Documents/$newFileName";
        $paramArray = array ('fileName' => $doc
        );

        $methodName = 'DeleteAllVersions';

        $result = $this->dwsObj->callWsMethod( $methodName, $paramArray );
        if ($result) {
            $xml = $result->DeleteAllVersionsResult->any; // in Result we get string in Xml format
            $xmlNew = simplexml_load_string( $xml ); // used to parse string to xml
            $xmlArray = @G::json_decode( @G::json_encode( $xmlNew ), 1 ); // used to convert Objects to array
            $latestVersion = $xmlArray['result']['@attributes']['version'];
            return "All Versions are Deleted, except the latest i.e $latestVersion";
        } else {
            return "The Version/ File name/ Dws Name  is incorrect";
        }
    }

    function getDWSFolderItems ($dwsname, $strFolderUrl)
    {
        $pmTrSharepointClassObj = new pmTrSharepointClass();
        //print "<br>- Method getDWSFolderItems";
        $url = $this->server . "/" . $dwsname . "/_vti_bin/SiteData.asmx?WSDL";
        $this->dwsObj->setwsdlurl( $this->server . "/" . $dwsname . "/_vti_bin/SiteData.asmx?WSDL" );

        $this->dwsObj->loadSOAPClient();

        #$doc = "Shared Documents/$newFileName";
        $paramArray = array ('strFolderUrl' => $strFolderUrl
        );

        $methodName = 'EnumerateFolder';

        $result = $this->dwsObj->callWsMethod( $methodName, $paramArray );
        //$newResult = $result->vUrls->_sFPUrl->Url;
        if (isset( $result->vUrls->_sFPUrl->Url )) {
            $returnContent = $pmTrSharepointClassObj->getFolderUrlContent( $result->vUrls->_sFPUrl->Url );
            $serializeResult = serialize( $returnContent );
            return $serializeResult;
        } elseif (isset( $result->vUrls->_sFPUrl )) {
            $itemCount = count( $result->vUrls->_sFPUrl );
            for ($i = 0; $i < $itemCount; $i ++) {
                $aObjects = $result->vUrls->_sFPUrl[$i]->IsFolder;
                //$booleanStatus = $aObjects[$i]->IsFolder;
                if ($aObjects) {
                    $listArr = $result->vUrls->_sFPUrl[$i]->Url;
                    $returnContent[] = $pmTrSharepointClassObj->getFolderUrlContent( $listArr ) . "(Is a Folder)";
                } else {
                    $listArr = $result->vUrls->_sFPUrl[$i]->Url;
                    $returnContent[] = $pmTrSharepointClassObj->getFolderUrlContent( $listArr ) . "(Is a File)";
                }
            }
            $serializeResult = serialize( $returnContent );
            return $serializeResult;
        }
        return "There is some error";
    }

    function downloadDocumentDWS ($dwsname, $fileName, $fileLocation)
    {
        //print "<br>- Method createDWS";
        $url = $this->server . "/" . $dwsname . "/_vti_bin/Copy.asmx?WSDL";
        $this->dwsObj->setwsdlurl( $url );

        $this->dwsObj->loadSOAPClient();

        $CompleteUrl = $this->server . "/" . $dwsname . "/Shared Documents/" . $fileName;
        $paramArray = array ('Url' => $CompleteUrl
        );

        $methodName = 'GetItem';

        $result = $this->dwsObj->callWsMethod( $methodName, $paramArray );
        $newResult = $result->Stream;

        //$latestResult = base64_decode($newResult);

        /**
         * In the Below line of code, we are coping the files at our local Directory using the php file methods.
         */
        $imgfile = $fileLocation . "/" . $fileName;
        $filep = fopen( $imgfile, 'w' );
        //$content = fwrite($filep, $latestResult);
        $content = fwrite( $filep, $newResult );
        return $content;
    }

    function getFolderUrlContent ($newResult)
    {
        $needleStart = '/';
        $needleCount = substr_count( $newResult, $needleStart );

        $urlStartPos = strpos( $newResult, $needleStart );
        $urlStartPos ++;

        if ($needleCount == '2') {
            $newResultPos = strpos( $newResult, $needleStart, $urlStartPos );
            $newResultPos ++;
            $actualResult = substr( $newResult, $newResultPos );
            return $actualResult;
        } else {
            $actualResult = substr( $newResult, $urlStartPos );
            return $actualResult;
        }
    }
}

