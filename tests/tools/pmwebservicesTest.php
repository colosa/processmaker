<?php

class WebservicesTest extends PHPUnit_Framework_TestCase
{
	protected $client;
		
	/*public function getResult($response){
		if($response->status_code=="0"){
			//print_r($response);
			return $response->message;
		}else{
			throw new Exception($response->message);
		}
	}*/

	protected function setUp()
	{
		$options = array(
			'soap_version'=>SOAP_1_1,
			'exceptions'=>true,
			'trace'=>1,
			'cache_wsdl'=>WSDL_CACHE_NONE
			);
		$this->client = new SoapClient('http://localhost/sysworkflow/en/classic/services/wsdl2', $options);
					
	}

	
	public function testLogin()
	{

		$parameters = array('userid'=>'admin', 'password'=>'admin');
		$result = $this->client->login($parameters);

		$sessionId = $result->message;	
			
		//print_r($sessionId);
		$this->assertEquals(0,$result->status_code);
		
		return $sessionId;	

	}

	/**
	*@depends testLogin
	*/
	public function testUserList($sessionId)
	{
		
		//$this->sessionId=$result->message;
		//print_r($sessionId);		
		$parameters = array('sessionId'=>$sessionId);
		$result2 = $this->client->userList($parameters);
		$userarray = $result2->users;
		
		$this->assertGreaterThanOrEqual(0,sizeof($userarray));

	}

	/**
	*@depends testLogin
	*/
	public function testRoleList($sessionId)
	{
		$parameters = array('sessionId'=>$sessionId);
		$result = $this->client->roleList($parameters);
		$rolesArray = $result->roles;

		$this->assertGreaterThanOrEqual(0,sizeof($rolesArray));
	}
	/** 
	*@depends testLogin
	*/
	public function testCreateUser($sessionId)
	{
		$parameters = array('sessionId'=>$sessionId, 'userId'=>'ernesto3', 'firstname'=>'Ernesto', 'lastname'=>'Vega', 'email'=>'ernesto.vega@colosa.com', 'role'=>'PROCESSMAKER_ADMIN', 'password'=>'p0o9i8u7');
		$result = $this->client->createUser($parameters);
		//print_r($result->status_code);
		$this->assertEquals(0,$result->status_code);
	}
	/**
	*@depends testLogin
	*/
	public function testCreateGroup($sessionId)
	{
		$parameters = array('sessionId'=>$sessionId, 'name'=>'Grupo1');
		$result = $this->client->createGroup($parameters);
		$this->assertEquals(0,$result->status_code);
	}
	/**
	*@depends testLogin
	*/
	public function testCreateDepartment($sessionId)
	{
		$parameters = array('sessionId'=>$sessionId, 'name'=>'Human Resources2', 'parentUID'=>'');
		$result=$this->client->createDepartment($parameters);
		$this->assertEquals(0,$result->status_code);
	}
	/**
	*@depends testLogin
	*/
	public function testProcessList($sessionId)
	{
		$parameters = array('sessionId'=>$sessionId);
		$result=$this->client->processList($parameters);
		$processarray = $result->processes;
		$this->assertGreaterThan(0,sizeof($processarray));
	}
	/**
	*@depends testLogin
	*/
	public function testTaskList($sessionId)
	{
		$parameters = array('sessionId'=>$sessionId);
		$result=$this->client->taskList($parameters);
		$taskarray = $result->tasks;
		$this->assertGreaterThan(0,sizeof($taskarray));
	}
	/**
	*@depends testLogin
	*/
	public function testCaseList($sessionId)
	{
		$parameters = array('sessionId'=>$sessionId);
		$result = $this->client->caseList($parameters);
		$casesList = $result->cases;
		$this->assertGreaterThan(0,sizeof($casesList));		
	}
	/**
	*@depends testLogin
	*/
	public function testTriggerList($sessionId)
	{
		$parameters = array('sessionId'=>$sessionId);
		$result = $this->client->triggerList($parameters);
		$triggerList = $result->triggers;
		$this->assertGreaterThan(0,sizeof($triggerList));
	}
	/**
	*@depends testLogin
	*/
	public function testDepartmentList($sessionId)
	{
		$parameters = array('sessionId'=>$sessionId);
		$result = $this->client->departmentList($parameters);
		$depsList = $result->departments;
		$this->assertGreaterThan(0,sizeof($depsList));
	}
	/**
	*@depends testLogin
	*/
	public function testExecuteTrigger($sessionId)
	{
		$parameters = array('sessionId'=>$sessionId, 'caseId'=>'7862740995036a920e24408029105125', 'triggerIndex'=>'9977315965036ae5b0a26a4038012741', 'delIndex'=>'1');
		$result = $this->client->executeTrigger($parameters);
		$this->assertNotEquals(0,$result->status_code);
	}
}


?>
