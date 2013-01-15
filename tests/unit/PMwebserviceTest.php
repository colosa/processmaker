<?php

require_once "pmwebservices.php";

class PMwebserviceTest extends PHPUnit_Framework_TestCase
{
	protected $webserv;
	
	public function setUp()
	{
		$this->webserv = new pmwebservices();
	}	

	public function testLogin()
	{
		$usr = "admin";
		$pass = "admin";
		$this->assertEquals(0,$this->webserv->Login($usr, $pass));
	}

	public function testUserList()
	{
		$this->assertGreaterThan(0,$this->webserv->UserList());
	}
}



?>
