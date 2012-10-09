<?php

if (!defined('PATH_SEP')) {
    define('PATH_SEP',		'/');
}

require_once PATH_HOME . 'engine/services/rest/crud/CalendarDefinition.php';
require_once("Rest/JsonMessage.php");
require_once("Rest/XmlMessage.php");
require_once("Rest/RestMessage.php");

class CalendarDefinitionTest extends PHPUnit_Extensions_Database_TestCase
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
        $table = "CalendarDefinition";

        $rest = new RestMessage();
        $resp = $rest->sendGET($table,$key);

        $key1 = array( "942663514792946220");
        $rest->sendGET($table,$key1);
        //$rest->displayResponse();

        $queryTable = $this->getConnection()->createQueryTable(
            'CALENDAR_DEFINITION', 'SELECT * FROM CALENDAR_DEFINITION WHERE CALENDAR_UID = "00000000000000000000000000000001"'
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

        $key = array( "00000000000000000000000000000002", "Calendario Diario", "2012-09-17 13:00:04", "2012-09-17 13:01:06", "1", "horarios", "ACTIVE");
        //$table = "CALENDAR_DEFINITION";
        $table = "CalendarDefinition";

        $rest = new RestMessage();
        $rest->sendPOST($table,$key);
        //$rest->displayResponse();

        $key1 = array("00000000000000000000000000000002");
        $resp = $rest->sendGET($table,$key1);

        $queryTable = $this->getConnection()->createQueryTable(
            'CALENDAR_DEFINITION', 'SELECT * FROM CALENDAR_DEFINITION WHERE CALENDAR_UID = "00000000000000000000000000000002"'
        );

        //$this->assertEquals($queryTable, $resp, "ERROR inserting data");
    }

    public function testPut()
    {
       /* $msg = array( 'user'=>'admin' , 'password'=>'admin');
        $method = "login";

        $jsonm = new JsonMessage();
        $jsonm->send($method,$msg);
        //$jsonm->displayResponse();

        $xmlm = new XmlMessage();
        $xmlm->send($method, $msg);
        //$xmlm->displayResponse();

        $key = array( "9426635155057479305aa11012946220", "Calendario Empleados", "2012-09-17 12:00:04", "2012-09-17 12:01:06", "1|2|3|4|5", "Horarios laborales", "ACTIVE");
        $table = "CALENDAR_DEFINITION";

        $rest = new RestMessage();
        $rest->sendPUT($table,$key);
        //$rest->displayResponse();

        $key1 = array("9426635155057479305aa11012946220");
        $resp = $rest->sendGET($table,$key1);

        $queryTable = $this->getConnection()->createQueryTable(
            'CALENDAR_DEFINITION', 'SELECT * FROM CALENDAR_DEFINITION WHERE CALENDAR_UID = "66666635155057479305ab1101111111"'
        );

        $this->assertEquals($queryTable, $resp, "ERROR updating data");*/
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
        $table = "CalendarDefinition";

        $rest = new RestMessage();
        $rest->sendDELETE($table,$key);
        //$rest->displayResponse();
        $resp = $rest->sendGET($table,$key);

        $queryTable = $this->getConnection()->createQueryTable(
            'CALENDAR_DEFINITION', 'SELECT * FROM CALENDAR_DEFINITION WHERE CALENDAR_UID = "00000000000000000000000000000002"'
        );

        //$this->assertEquals($queryTable, $resp, "ERROR deleting data");
    }
}

