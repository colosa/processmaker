<?php

class WsCaller
{

    private $wsdlurl;
    private $soapObj;
    private $client;
    private $auth;
    private $clientStream;

    public function setAuthUser($auth)
    {
        //print "<br>- auth Setup";
        $this->auth = $auth;
    }

    public function setwsdlurl($wsdl)
    {
        //print "<br>- wsdl Setup";
        $this->wsdlurl = $wsdl;
        //var_dump($wsdl);
    }

    public function loadSOAPClient()
    {
        try {
            // we unregister the current HTTP wrapper
            stream_wrapper_unregister('http');
            // we register the new HTTP wrapper
            //$client = new PMServiceProviderNTLMStream($this->auth);
            PMServiceProviderNTLMStream::setAuthStream($this->auth);
            stream_wrapper_register('http', 'PMServiceProviderNTLMStream') or die("Failed to register protocol");

            //     $this->client = new PMServiceNTLMSoapClient($this->wsdlurl, array('trace' => 1, 'auth' => $this->auth));// Hugo's code
            $this->client = new PMServiceNTLMSoapClient($this->wsdlurl, array('trace' => 1)); // Ankit's Code
            $this->client->setAuthClient($this->auth);
            return true;
        } catch (Exception $e) {
            echo $e;
            exit();
        }
    }

    public function callWsMethod($methodName, $paramArray)
    {

        try {
            if ($methodName == 'DeleteDws' || $methodName == 'GetListCollection') {
                $strResult = "";
                $strResult = $this->client->$methodName($paramArray = "");
                return $strResult;
            } else {
                $strResult = "";
                $strResult = $this->client->$methodName($paramArray);
                return $strResult;
            }
        } catch (SoapFault $fault) {
            echo 'Fault code: ' . $fault->faultcode;
            echo 'Fault string: ' . $fault->faultstring;
        }
        stream_wrapper_restore('http');
    }
}
