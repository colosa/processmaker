<?php

class PMexpensereport extends PHPUnit_Framework_TestCase
{
		
	public function testFirst()
	{
		$options = array(
			'soap_version'=>SOAP_1_1,
			'exceptions'=>true,
			'trace'=>1,
			'cache_wsdl'=>WSDL_CACHE_NONE
			);
		$client = new SoapClient('http://192.168.11.130/systestcr/en/uxmodern/services/wsdl2', $options);
					
		$parameters = array('userid'=>'admin', 'password'=>'admin');
		$result = $client->login($parameters);

		$sessionId = $result->message;	
		
		$this->assertEquals(0,$result->status_code);
		
	
		$processId = '9307187034d63bc32b49551073600955';
		$taskId = '8578908424d63bc32b56826047664076';
		$variables = array(
					array('name'=>"repSubject","value"=>"Prueba 1"),
					array('name'=>"repRelatedto","value"=>"Ernesto Vega"),
					array('name'=>"repCashAdvanced","value"=>"1235"),
				  );		
		$parameters = array('sessionId'=>$sessionId,'processId'=>$processId,'taskId'=>$taskId,'variables'=>$variables);
		$response = $client->newCase($parameters);
		$caseId = $response->caseId;
		$this->assertEquals(0,$response->status_code);
			
	
		$parameters = array('sessionId'=>$sessionId, 'caseId'=>$caseId, 'delIndex'=>'1');
		$response = $client->routeCase($parameters);
		$this->assertEquals(0,$response->status_code);
	}
	
}

?>
