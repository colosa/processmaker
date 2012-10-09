<?php

if (!defined('PATH_SEP')) {
    define('PATH_SEP',		'/');
}

require_once PATH_HOME . 'engine/services/rest/crud/Session.php';
require_once("Rest/JsonMessage.php");
require_once("Rest/XmlMessage.php");
require_once("Rest/RestMessage.php");

class SessionTest extends PHPUnit_Extensions_Database_TestCase
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

        $key = array( "8421281685065aec01a7643096730466");
        $key1 = array( "8421281685065a");
        $table = "SESSION";

        $rest = new RestMessage();
        $resp = $rest->sendGET($table,$key);
        $rest->sendGET($table,$key1);
        //$rest->displayResponse();

        $queryTable = $this->getConnection()->createQueryTable(
            'SESSION', 'SELECT * FROM SESSION WHERE SES_UID = "8421281685065aec01a7643096730466"'
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

        $key = array( "2252443815002e3c2422675066811111", "ACTIVE", "00000000000000000000000000000001", "192.168.11.21", "2012-10-09 10:05:52", "2012-10-09 10:20:52", "");
        $table = "SESSION";

        $rest = new RestMessage();
        $rest->sendPOST($table,$key);
        //$rest->displayResponse();

        $key1 = array("2252443815002e3c2422675066811111");
        $resp = $rest->sendGET($table,$key1);

        $queryTable = $this->getConnection()->createQueryTable(
            'SESSION', 'SELECT * FROM SESSION WHERE SES_UID = "2252443815002e3c2422675066811111"'
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

        $key = array( "2252443815002e3c2422675066811111", "XAMPLE", "00000000000000000000000000000001", "192.168.11.21", "2012-10-09 10:05:52", "2012-10-09 10:20:52", "");
        $table = "SESSION";

        $rest = new RestMessage();
        $rest->sendPUT($table,$key);
        //$rest->displayResponse();

        $key1 = array("2252443815002e3c2422675066811111");
        $resp = $rest->sendGET($table,$key1);

        $queryTable = $this->getConnection()->createQueryTable(
            'SESSION', 'SELECT * FROM SESSION WHERE SES_UID = "2252443815002e3c2422675066811111"'
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

        $key = array("2252443815002e3c2422675066811111");
        $table = "SESSION";

        $rest = new RestMessage();
        $rest->sendDELETE($table,$key);
        //$rest->displayResponse();
        $resp = $rest->sendGET($table,$key);

        $queryTable = $this->getConnection()->createQueryTable(
            'SESSION', 'SELECT * FROM SESSION WHERE SES_UID = "2252443815002e3c2422675066811111"'
        );

        //$this->assertEquals($queryTable, $resp, "ERROR deleting data");
    }
}
