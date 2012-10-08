<?php

if (!defined('PATH_SEP')) {
    define('PATH_SEP',		'/');
}

require_once PATH_HOME . 'engine/services/rest/crud/Translation.php';
require_once("Rest/JsonMessage.php");
require_once("Rest/XmlMessage.php");
require_once("Rest/RestMessage.php");

class TraslationTest extends PHPUnit_Extensions_Database_TestCase
{
    public function setup()
    {
    }

    protected function getTearDownOperation()
    {
        return PHPUnit_Extensions_Database_Operation_Factory::DELETE_ALL();
    }

    /**
     * @return PHPUnit_Extensions_Database_DB_IDatabaseConnection
     */

    // only instantiate pdo once for test clean-up/fixture load
    static private $pdo = null;
    // only instantiate PHPUnit_Extensions_Database_DB_IDatabaseConnection once per test
    private $conn = null;

    public function getConnection()
    {
        if ($this->conn === null) {
            $dsn = 'mysql:dbname=' . $_SERVER['PM_UNIT_DB_NAME'] . ';host='. $_SERVER['PM_UNIT_DB_HOST'];
            if (self::$pdo == null) {
                self::$pdo = new PDO(
                    $dsn,
                    $_SERVER['PM_UNIT_DB_USER'],
                    $_SERVER['PM_UNIT_DB_PASS'] );
            }
            $this->conn = $this->createDefaultDBConnection(self::$pdo, $_SERVER['PM_UNIT_DB_NAME']);
        }
        return $this->conn;
    }

    /**
     *@return PHPUnit_Extensions_Database_DataSet_IDataSet
     */

    public function getDataSet()
    {
        return $this->createXMLDataSet('tests/unit/backend/services/Rest/fixtures/application.xml');
    }

    public function testGet()
    {
        $msg = array( 'user'=>'admin' , 'password'=>'admin');
        $method = "login";

        $jsonm = new JsonMessage();
        $jsonm->send($method,$msg);
        //$jsonm->displayResponse();

        $xmlm = new XmlMessage();
        $xmlm->send($method, $msg);
        //$xmlm->displayResponse();

        $key = array( "LABEL", "LOGIN", "en");
        $table = "TRANSLATION";

        $rest = new RestMessage();
        $resp = $rest->sendGET($table,$key);
        //$rest->displayResponse();

        $queryTable = $this->getConnection()->createQueryTable(
            'TRANSLATION', 'SELECT * FROM TRANSLATION WHERE TRN_CATEGORY  = "LABEL" AND TRN_ID = "LOGIN"'
        );

        //$this->assertEquals($resp, $key, "ERROR getting data");
    }

    public function testPost()
    {
        $msg = array( 'user'=>'admin' , 'password'=>'admin');
        $method = "login";

        $jsonm = new JsonMessage();
        $jsonm->send($method,$msg);
        //$jsonm->displayResponse();

        $xmlm = new XmlMessage();
        $xmlm->send($method, $msg);
        //$xmlm->displayResponse();

        $key = array( "HOUSE", "PUSHIN", "en", "sample", "2012-06-06" );
        $table = "TRANSLATION";

        $rest = new RestMessage();
        $rest->sendPOST($table,$key);
        //$rest->displayResponse();

        $key1 = array("HOUSE", "PUSHIN", "en");
        $resp = $rest->sendGET($table,$key1);

        $queryTable = $this->getConnection()->createQueryTable(
            'TRANSLATION', 'SELECT * FROM TRANSLATION WHERE TRN_CATEGORY  = "HOUSE" AND TRN_ID = "PUSHIN"'
        );

        //$this->assertEquals($queryTable, $resp, "ERROR getting data");
    }

    public function testPut()
    {
        $msg = array( 'user'=>'admin' , 'password'=>'admin');
        $method = "login";

        $jsonm = new JsonMessage();
        $jsonm->send($method,$msg);
        //$jsonm->displayResponse();

        $xmlm = new XmlMessage();
        $xmlm->send($method, $msg);
        //$xmlm->displayResponse();

        $key = array("HOUSE", "PUSHIN", "en", "samplemod", "2012-07-06");
        $table = "TRANSLATION";

        $rest = new RestMessage();
        $rest->sendPUT($table,$key);
        //$rest->displayResponse();

        $key1 = array("HOUSE", "PUSHIN", "en");
        $resp = $rest->sendGET($table,$key1);

        $queryTable = $this->getConnection()->createQueryTable(
            'TRANSLATION', 'SELECT * FROM TRANSLATION WHERE TRN_CATEGORY  = "HOUSE" AND TRN_ID = "PUSHIN"'
        );

        //$this->assertEquals($queryTable, $resp, "ERROR getting data");
    }

    public function testDelete()
    {
        $msg = array( 'user'=>'admin' , 'password'=>'admin');
        $method = "login";

        $jsonm = new JsonMessage();
        $jsonm->send($method,$msg);
        //$jsonm->displayResponse();

        $xmlm = new XmlMessage();
        $xmlm->send($method, $msg);
        //$xmlm->displayResponse();

        $key = array("HOUSE", "PUSHIN", "en");
        $table = "TRANSLATION";

        $rest = new RestMessage();
        $rest->sendDELETE($table,$key);
        //$rest->displayResponse();

        $queryTable = $this->getConnection()->createQueryTable(
            'TRANSLATION', 'SELECT * FROM TRANSLATION WHERE TRN_CATEGORY  = "HOUSE" AND TRN_ID = "PUSHIN"'
        );

        //$this->assertEquals($queryTable, $resp, "ERROR getting data");
    }
}
