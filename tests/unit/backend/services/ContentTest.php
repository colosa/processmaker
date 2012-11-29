<?php

if (!defined('PATH_SEP')) {
    define('PATH_SEP',		'/');
}

require_once PATH_HOME . 'engine/services/rest/crud/Content.php';
require_once("Rest/JsonMessage.php");
require_once("Rest/XmlMessage.php");
require_once("Rest/RestMessage.php");

class ContentTest extends PHPUnit_Extensions_Database_TestCase
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

        $key = array( "PRO_TITLE", "", "6548800755065a63d67f727063273525", "en");
        $key1 = array( "PRO_DESCRIPTION", "", "4880075", "en");
        $table = "CONTENT";

        $rest = new RestMessage();
        $resp = $rest->sendGET($table,$key);
        $rest->sendGET($table,$key1);
        //$rest->displayResponse();

        $queryTable = $this->getConnection()->createQueryTable(
            'CONTENT', 'SELECT * FROM CONTENT WHERE CON_CATEGORY = "PRO_TITLE" AND CON_ID = "6548800755065a63d67f727063273525"'
        );

        //$this->assertEquals($resp, $queryTable, "ERROR getting data");

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

        $key = array( "PRO_TITLE", "", "6666000755065a63d67f727063273222", "en", "SAMPLE");
        $table = "CONTENT";

        $rest = new RestMessage();
        $rest->sendPOST($table,$key);
        //$rest->displayResponse();

        $key1 = array("PRO_TITLE", "", "6666000755065a63d67f727063273222", "en");
        $resp = $rest->sendGET($table,$key1);

        $queryTable = $this->getConnection()->createQueryTable(
            'CONTENT', 'SELECT * FROM CONTENT WHERE CON_CATEGORY = "PRO_TITLE" AND CON_ID = "6666000755065a63d67f727063273222"'
        );

        //$this->assertEquals($queryTable, $resp, "ERROR inserting data");
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

        $key = array( "PRO_TITLE", "", "6666000755065a63d67f727063273222", "en", "XAMPLE");
        $table = "CONTENT";

        $rest = new RestMessage();
        $rest->sendPUT($table,$key);
        //$rest->displayResponse();

        $key1 = array("PRO_TITLE", "", "6666000755065a63d67f727063273222", "en");
        $resp = $rest->sendGET($table,$key1);

        $queryTable = $this->getConnection()->createQueryTable(
            'CONTENT', 'SELECT * FROM CONTENT WHERE CON_CATEGORY = "PRO_TITLE" AND CON_ID = "6666000755065a63d67f727063273222"'
        );

        //$this->assertEquals($queryTable, $resp, "ERROR updating data");
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

        $key = array( "PRO_TITLE", "", "6666000755065a63d67f727063273222", "en");
        $table = "CONTENT";

        $rest = new RestMessage();
        $rest->sendDELETE($table,$key);
        //$rest->displayResponse();
        $resp = $rest->sendGET($table,$key);

        $queryTable = $this->getConnection()->createQueryTable(
            'CONTENT', 'SELECT * FROM CONTENT WHERE CON_CATEGORY = "PRO_TITLE" AND CON_ID = "6666000755065a63d67f727063273222"'
        );

        //$this->assertEquals($queryTable, $resp, "ERROR deleting data");
    }
}

