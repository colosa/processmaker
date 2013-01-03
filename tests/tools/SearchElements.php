<?php

class SearchElements
{
	protected $client;
	
	public function __construct()
	{
		
	}
	
	public function getResult($response){
		if($response->status_code=="0"){
			return $response->message;
		}else{
			throw new Exception($response->message);
		}
	}
	
	public function login($server,$enviroment,$user,$pass)
	{
		$options = array(
				'soap_version'=>SOAP_1_1,
				'exceptions'=>true, //Show and stop on any exception
				'trace'=>1, // Help doing traceability of errors
				'cache_wsdl'=>WSDL_CACHE_NONE  //Do not cache webservice responses
		);
		$this->client = new SoapClient("http://$server/sys$enviroment/en/uxmodern/services/wsdl2", $options);
	
		$parameters = array('userid'=>$user, 'password'=>$pass);
		$result = $this->client->login($parameters);
		$sessionId = $this->getResult($result);
		return $sessionId;
	}
	
	public function searchProcess($sessionId,$processName)
	{
		$parameters = array('sessionId'=>$sessionId);	
		$result = $this->client->processList($parameters);
		$processList = $result->processes;
		$processId = "";
		for($i=0;$i<count($processList);$i++)
		{
			//print($processList[$i]->name."\n");
			if(strtolower(trim($processList[$i]->name))==strtolower(trim($processName)))
			{
				$processId = $processList[$i]->guid;
			}
		}
		
		return $processId;
	}	
	
	function searchTask($sessionId,$taskName)
	{
		$parameters = array('sessionId'=>$sessionId);
		$result = $this->client->taskList($parameters);
		$taskList = $result->tasks;
		$taskId = "";
		for($j=0;$j<count($taskList);$j++)
		{
		//print($processList[$i]->name."\n");
			if(strtolower(trim($taskList[$j]->name))==strtolower(trim($taskName)))
			{
				$taskId = $taskList[$j]->guid;
			}
		}
		
		return $taskId;
	}
	
	function searchCase($sessionId,$caseName)
	{
		$parameters = array('sessionId'=>$sessionId);
		$result = $this->client->caseList($parameters);
		$caseList = $result->cases;
		$caseId = "";
		for($k=0;$k<count($caseList);$k++)
		{
		//print($processList[$i]->name."\n");
			if(strtolower(trim($caseList[$k]->name))==strtolower(trim($caseName)))
			{
				$caseId = $caseList[$k]->guid;
			}
		}
	
		return $caseId;
	}
	
	
	function searchTrigger($sessionId,$triggerName,$processId)
	{
		$parameters = array('sessionId'=>$sessionId);
		$result = $this->client->triggerList($parameters);
		$triggerList = $result->cases;
		$triggerId = "";
		for($l=0;$l<count($triggerList);$l++)
		{
		//print($processList[$i]->name."\n");
			if((strtolower(trim($triggerList[$l]->name))==strtolower(trim($triggerName)))&&($triggerList[$l]->processId==$processId))
			{
				$triggerId = $triggerList[$l]->guid;
			}
		}
	
		return $triggerId;
	}
	
	
}
