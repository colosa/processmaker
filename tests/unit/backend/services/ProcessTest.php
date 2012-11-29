<?php

if (!defined('PATH_SEP')) {
    define('PATH_SEP',		'/');
}

require_once PATH_HOME . 'engine/services/rest/crud/Process.php';
require_once("Rest/JsonMessage.php");
require_once("Rest/XmlMessage.php");
require_once("Rest/RestMessage.php");

class ProcessTest extends PHPUnit_Extensions_Database_TestCase
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

        $key = array( "6548800755065a63d67f727063273525");
        $key1 = array( "8421281685065a");
        $table = "PROCESS";

        $rest = new RestMessage();
        $resp = $rest->sendGET($table,$key);
        $rest->sendGET($table,$key1);
        //$rest->displayResponse();

        $queryTable = $this->getConnection()->createQueryTable(
            'PROCESS', 'SELECT * FROM PROCESS WHERE PRO_UID = "6548800755065a63d67f727063273525"'
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

        $key = array( "6548800755065a63d67f727063255555", "6548800755065a63d67f727063666665", "5", "HOURS", "ACTIVE", "0", "NORMAL", "FALSE", "1", "1", "1", "0", " ", " ", "1", "2012-09-28 09:29:33", "2012-09-28 09:29:33", "00000000000000000000000000000001", "5000", "10000", "0", "6", "0", " ", " " );
        $table = "PROCESS";

        $rest = new RestMessage();
        $rest->sendPOST($table,$key);
        //$rest->displayResponse();

        $key1 = array("6548800755065a63d67f727063255555");
        $resp = $rest->sendGET($table,$key1);

        $queryTable = $this->getConnection()->createQueryTable(
            'PROCESS', 'SELECT * FROM PROCESS WHERE PRO_UID = "6548800755065a63d67f727063255555"'
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

        $key = array( "6548800755065a63d67f727063255555", "6548800755065a63d67f727063666665", "2", "DAYS", "ACTIVE", "0", "NORMAL", "FALSE", "1", "1", "1", "0", " ", " ", "1", "2012-09-28 09:29:33", "2012-09-28 09:29:33", "00000000000000000000000000000001", "5000", "10000", "0", "6", "0", " ", " " );
        $table = "PROCESS";

        $rest = new RestMessage();
        $rest->sendPUT($table,$key);
        //$rest->displayResponse();

        $key1 = array("6548800755065a63d67f727063255555");
        $resp = $rest->sendGET($table,$key1);

        $queryTable = $this->getConnection()->createQueryTable(
            'PROCESS', 'SELECT * FROM PROCESS WHERE PRO_UID = "6548800755065a63d67f727063255555"'
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

        $key = array("6548800755065a63d67f727063255555");
        $table = "PROCESS";

        $rest = new RestMessage();
        $rest->sendDELETE($table,$key);
        //$rest->displayResponse();
        $resp = $rest->sendGET($table,$key);

        $queryTable = $this->getConnection()->createQueryTable(
            'PROCESS', 'SELECT * FROM PROCESS WHERE PRO_UID = "6548800755065a63d67f727063255555"'
        );

        //$this->assertEquals($queryTable, $resp, "ERROR deleting data");
    }
}
