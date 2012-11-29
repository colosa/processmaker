<?php

if (!defined('PATH_SEP')) {
    define('PATH_SEP',		'/');
}

require_once PATH_HOME . 'engine/services/rest/crud/Users.php';
require_once("Rest/JsonMessage.php");
require_once("Rest/XmlMessage.php");
require_once("Rest/RestMessage.php");

class UsersTest extends PHPUnit_Extensions_Database_TestCase
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

        $key = array( "00000000000000000000000000000001" );
        $table = "USERS";

        $rest = new RestMessage();
        $resp = $rest->sendGET($table,$key);

        $key1 = array( "942663514792946220");
        $rest->sendGET($table,$key1);
        //$rest->displayResponse();

        $queryTable = $this->getConnection()->createQueryTable(
            'USERS', 'SELECT * FROM USERS WHERE USR_UID = "00000000000000000000000000000001"'
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
        $xmlm->send($method,$msg);
        //$xmlm->displayResponse();

        $key = array( "00000000000000000000000000000002", "admin", "21232f297a57a5a743894a0e4a801fc3",
                     "Administrator", "Adminn", "admin@processmaker.com", "2020-01-01", "1999-11-30 00:00:00",
                     "2008-05-23 18:36:19", "ACTIVE", "US", "FL", "MMK", "Miraflores", "2240448", "1-305-402-0282",
                     "1-305-675-1400", "", "", "Administrator", "", "1999-02-25", "PROCESSMAKER_ADMIN",
                     "", "", "NORMAL");
        $table = "Users";

        $rest = new RestMessage();
        $rest->sendPOST($table,$key);
        //$rest->displayResponse();

        $key1 = array("00000000000000000000000000000002");
        $resp = $rest->sendGET($table,$key1);

        $queryTable = $this->getConnection()->createQueryTable(
            'USERS', 'SELECT * FROM USERS WHERE USR_UID = "00000000000000000000000000000002"'
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

        $key = array( "00000000000000000000000000000002", "adminsad", "21232f297a57a5a743894a0e4a801fc3", "Administrator", "adminsad@processmaker.com", "2020-01-01", "1999-11-30 00:00:00", "2008-05-23 18:36:19", "ACTIVE", "US", "FL", "MMK", "", "", "1-305-402-0282", "1-305-675-1400", "", "", "Administrator", "", "1999-02-25", "PROCESSMAKER_ADMIN", "", "", "NORMAL");
        $table = "USERS";

        $rest = new RestMessage();
        $rest->sendPUT($table,$key);
        //$rest->displayResponse();

        $key1 = array("00000000000000000000000000000002");
        $resp = $rest->sendGET($table,$key1);

        $queryTable = $this->getConnection()->createQueryTable(
            'USERS', 'SELECT * FROM USERS WHERE USR_UID = "00000000000000000000000000000002"'
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

        $key = array("00000000000000000000000000000002");
        $table = "USERS";

        $rest = new RestMessage();
        $rest->sendDELETE($table,$key);
        //$rest->displayResponse();
        $resp = $rest->sendGET($table,$key);

        $queryTable = $this->getConnection()->createQueryTable(
            'USERS', 'SELECT * FROM USERS WHERE USR_UID = "00000000000000000000000000000002"'
        );

        //$this->assertEquals($queryTable, $resp, "ERROR deleting data");
    }
}
