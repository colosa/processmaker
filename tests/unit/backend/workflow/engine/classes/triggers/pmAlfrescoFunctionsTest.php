<?php
//require_once 'bootstrap.php';
require_once PATH_HOME . PATH_SEP . "engine" . PATH_SEP . "classes" . PATH_SEP . "triggers" . PATH_SEP . "class.pmAlfrescoFunctions.php";

class pmAlfrescoFunctionsTest extends PHPUnit_Framework_TestCase
{
    protected $object;

    protected function setUp()
    {
        //$this->object = new pmAlfrescoFunctions();
    }

    protected function tearDown()
    {
    }

    public function testDownloadDoc()
    {
        $alfrescoServerUrl = 'http://192.168.11.30:8080/alfresco';
        $pathFile = 'notas.txt';
        $pathFile1 = 'notas222.txt';
        $pathFolder = '/tmp';
        $pathFolder1 = '/home/jennyleekkkkk/processmaker';
        $user = 'admin';
        $pwd = 'atopml2005';

        $adownloadDoc = downloadDoc($alfrescoServerUrl, $pathFile, $pathFolder, $user, $pwd);

        try {
            $adownloadDoc1 = downloadDoc($alfrescoServerUrl, $pathFile, $pathFolder1, $user, $pwd);
        } catch (Exception $e) {
            $this->assertEquals( "Undefined index:  HTTP_REFERER", $e->getMessage(), 'ERROR!');
        }

        $this->assertEquals($adownloadDoc, true);

        try {
            $adownloadDoc1 = downloadDoc($alfrescoServerUrl, $pathFile1, $pathFolder, $user, $pwd);
        } catch (Exception $e) {
            $this->assertEquals( "simplexml_load_string(): Entity: line 1: parser error : Entity 'nbsp' not defined", $e->getMessage(), 'ERROR!');
        }
    }
}