<?php

if (!defined('PATH_SEP')) {
    define('PATH_SEP',		'/');
}

require_once PATH_HOME . 'engine/services/rest/crud/Application.php';
require_once("Rest/JsonMessage.php");
require_once("Rest/XmlMessage.php");
require_once("Rest/RestMessage.php");

class ApplicationTest extends PHPUnit_Extensions_Database_TestCase
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

        $APP_UID = array("741388075505cd6bba2e993094312973");
        $table = "APPLICATION";

        $rest = new RestMessage();
        $resp = $rest->sendGET($table,$APP_UID);
        //$rest->displayResponse();

        $APP_UID2 = array("741973");
        $rest->sendGET($table,$APP_UID2);

        $queryTable = $this->getConnection()->createQueryTable(
            'APPLICATION', 'SELECT * FROM APPLICATION WHERE APP_UID = "741388075505cd6bba2e993094312973"'
        );

        //$this->assertEquals($queryTable, $resp, "ERROR getting data");
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

        $APP_UID = array("4670755835065b7eb6a4187052654566", "2008", "", "DRAFT", "6548800755065a63d67f727063273525", "", "", "N", "00000000000000000000000000000001", "00000000000000000000000000000001", "2012-09-28 10:44:59", "2012-09-28 12:11:58", "2012-09-28 12:11:58", "2012-09-28 12:11:58", "", "00a9357205f5ea2d9325e166092b0e3f");
        $table = "APPLICATION";

        $rest = new RestMessage();
        $rest->sendPOST($table,$APP_UID);
        //$rest->displayResponse();

        $ID = array("4670755835065b7eb6a4187052654566");
        $resp = $rest->sendGET($table,$ID);

        $queryTable = $this->getConnection()->createQueryTable(
            'APPLICATION', 'SELECT * FROM APPLICATION WHERE APP_UID = "4670755835065b7eb6a4187052654566"'
        );

        $APP_UID2 = array();
        $rest->sendPOST($table,$APP_UID2);

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

        $APP_UID = array("4670755835065b7eb6a4187052654566", "2008", "", "DRAFT", "6548800755065a63d67f727063273525", "", "", "N", "00000000000000000000000000000001", "00000000000000000000000000000001", "2012-09-28 10:44:59", "2012-09-28 12:11:58", "2012-09-28 12:11:58", "2012-09-28 12:11:58", "", "00a9357205f5ea2d9325e166092b0e3f");
        $table = "APPLICATION";

        $rest = new RestMessage();
        $rest->sendPUT($table,$APP_UID);
        //$rest->displayResponse();

        $APP_UID2 = array();
        $rest->sendPOST($table,$APP_UID2);
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

        $APP_UID = array("4670755835065b7eb6a4187052654566");
        $table = "APPLICATION";

        $rest = new RestMessage();
        $rest->sendDELETE($table,$APP_UID);
        //$rest->displayResponse();

        $APP_UID2 = array("");
        $rest->sendPOST($table,$APP_UID2);
    }
}

