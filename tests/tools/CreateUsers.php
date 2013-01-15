<?php 

class CreateUsers
{
	protected $client;
	protected $groups;
	protected $departments;
	protected $users;
	
	protected $countusers;
	protected $countgroups;	
	protected $countdepartments;

	
		
	
	
	public function defineVars()
	{
			
		$this->users = array(
								'1'=>array('1'=>'Aaron', '2'=>'Laughlin','3'=>'Manager|Administrators', '4'=>'Executive President', '5'=>''),
								'2'=>array('1'=>'Adam', '2'=>'Corey','3'=>'Manager|Administrators', '4'=>'Executive President', '5'=>''),
								'3'=>array('1'=>'Alexis', '2'=>'Corleone','3'=>'Employees|Human Resources', '4'=>'International Sales', '5'=>''),
								'4'=>array('1'=>'Alyssa', '2'=>'Connolly','3'=>'Accounting|supervisors', '4'=>'Executive President', '5'=>''),
								'5'=>array('1'=>'Amy', '2'=>'Connelly','3'=>'Employees|Warehouse Users', '4'=>'Administrative Division', '5'=>''),
								'6'=>array('1'=>'Andrew', '2'=>'Cole','3'=>'Employees|Human Resources', '4'=>'Administrative Division', '5'=>''),
								'7'=>array('1'=>'Arlene', '2'=>'Cleveland','3'=>'Employees|Finance', '4'=>'Administrative Division', '5'=>''),
								'8'=>array('1'=>'Arthur', '2'=>'Cifuni','3'=>'Employees|Manager', '4'=>'Administrative Division', '5'=>''),
								'9'=>array('1'=>'Benjamin', '2'=>'Cifrino','3'=>'Employees|TI', '4'=>'Human Resources', '5'=>''),
								'10'=>array('1'=>'Brianna', '2'=>'Ciarleglio','3'=>'Employees|Manager', '4'=>'International Sales', '5'=>''),
								'11'=>array('1'=>'Carter', '2'=>'Ching','3'=>'Employees|Finance', '4'=>'International Sales', '5'=>''),
								'12'=>array('1'=>'Charlotte', '2'=>'Charette','3'=>'Accounting', '4'=>'International Sales', '5'=>''),
								'13'=>array('1'=>'Christopher', '2'=>'Champagne','3'=>'Human Resources|supervisors', '4'=>'Accounting', '5'=>''),
								'14'=>array('1'=>'Dorothy', '2'=>'Cagley','3'=>'Employees|Human Resources', '4'=>'Accounting', '5'=>''),
								'15'=>array('1'=>'Dylan', '2'=>'Burns','3'=>'Employees|Warehouse Users', '4'=>'Sales Division', '5'=>''),
								'16'=>array('1'=>'Gavin', '2'=>'Brule','3'=>'Employees|Finance', '4'=>'Operation Division', '5'=>''),
								'17'=>array('1'=>'Hunter', '2'=>'Brown','3'=>'Employees|TI', '4'=>'International Sales', '5'=>''),
								'18'=>array('1'=>'Ian', '2'=>'Broderick','3'=>'Employees|Training', '4'=>'Human Resources', '5'=>''),
								'19'=>array('1'=>'Isaiah', '2'=>'Bourgeous','3'=>'Employees|Warehouse Users', '4'=>'Accounting', '5'=>''),
								'20'=>array('1'=>'Jacob', '2'=>'Bosworth','3'=>'Employees|Human Resources', '4'=>'Sales Division', '5'=>''),
								'21'=>array('1'=>'Jason', '2'=>'Boria','3'=>'Employees|Finance', '4'=>'Human Resources', '5'=>''),
								'22'=>array('1'=>'Jeremiah', '2'=>'Blackshear','3'=>'Manager|supervisors|Administrators', '4'=>'Operation Division', '5'=>''),
								'23'=>array('1'=>'Jose', '2'=>'Black','3'=>'Accounting|supervisors', '4'=>'Human Resources', '5'=>''),
								'24'=>array('1'=>'Joseph', '2'=>'Bittner','3'=>'Employees', '4'=>'Sales Division', '5'=>''),
								'25'=>array('1'=>'Josh', '2'=>'Bittner','3'=>'Employees|Warehouse Users', '4'=>'Accounting', '5'=>''),
								'26'=>array('1'=>'Joshua', '2'=>'Bendis','3'=>'Employees|Training', '4'=>'Sales Division', '5'=>''),
								'27'=>array('1'=>'Juan', '2'=>'Bechtel','3'=>'Accounting|supervisors', '4'=>'Planning and Research', '5'=>''),
								'28'=>array('1'=>'Julian', '2'=>'Beccia','3'=>'Employees|TI', '4'=>'Operation Division', '5'=>''),
								'29'=>array('1'=>'Kevin', '2'=>'Beam','3'=>'Employees|Finance', '4'=>'Operation and Busines', '5'=>''),
								'30'=>array('1'=>'Lily', '2'=>'Basili','3'=>'Employees|TI', '4'=>'Planning and Research', '5'=>''),
								'31'=>array('1'=>'Max', '2'=>'Barrett','3'=>'Employees|Warehouse Users', '4'=>'Operation and Busines', '5'=>''),
								'32'=>array('1'=>'Michael', '2'=>'barkle','3'=>'Employees', '4'=>'Planning and Research', '5'=>''),
								'33'=>array('1'=>'Mike', '2'=>'Balisi','3'=>'Employees|TI', '4'=>'Operation and Busines', '5'=>''),
								'34'=>array('1'=>'Olivia', '2'=>'Austin','3'=>'Training', '4'=>'Operation Division', '5'=>''),
								'35'=>array('1'=>'Owen', '2'=>'Arinello','3'=>'Employees|Accounting', '4'=>'Finance Division', '5'=>''),
								'36'=>array('1'=>'ryan', '2'=>'Arbouet','3'=>'Employees', '4'=>'Finance Division', '5'=>''),
								'37'=>array('1'=>'Samantha', '2'=>'Anderson','3'=>'Training', '4'=>'Finance Division', '5'=>''),
								'38'=>array('1'=>'Sarah', '2'=>'Amadon','3'=>'Employees', '4'=>'Operation and Busines', '5'=>''),
								'39'=>array('1'=>'Ulices', '2'=>'Ahearn','3'=>'Employees|Training', '4'=>'Finance Division', '5'=>''),
								'40'=>array('1'=>'User', '2'=>'Agnes','3'=>'Employees', '4'=>'Planning and Research', '5'=>'')
							);
		
		
		$this->groups = array(
								'1'=>array('1'=>'Human Resources', '2'=>''), 
								'2'=>array('1'=>'Manager', '2'=>''),
								'3'=>array('1'=>'TI', '2'=>''),
								'4'=>array('1'=>'Training', '2'=>''), 
								'5'=>array('1'=>'Administrators', '2'=>''), 
								'6'=>array('1'=>'Employees', '2'=>''), 
				   				'7'=>array('1'=>'Accounting', '2'=>''),
				 				'8'=>array('1'=>'Finance', '2'=>''), 
				 				'9'=>array('1'=>'supervisors', '2'=>''),
								'10'=>array('1'=>'Warehouse Users', '2'=>'')
							);
		
		$this->departments = array(				
								'1'=>array('1'=>'Executive President', '2'=>''),
								'2'=>array('1'=>'Administrative Division', '2'=>''),
								'3'=>array('1'=>'Accounting', '2'=>''),
								'4'=>array('1'=>'Human Resources', '2'=>''),
								'5'=>array('1'=>'Finance Division', '2'=>''),
								'6'=>array('1'=>'Planning and Research', '2'=>''),
								'7'=>array('1'=>'Operation Division', '2'=>''),
								'8'=>array('1'=>'Operation and Busines', '2'=>''),
								'9'=>array('1'=>'Sales Division', '2'=>''),
								'10'=>array('1'=>'International Sales', '2'=>''),
								'11'=>array('1'=>'Local Sales', '2'=>''),
								'12'=>array('1'=>'Support Service Division', '2'=>''),
								'13'=>array('1'=>'Information Desk', '2'=>''),
								'14'=>array('1'=>'Tecnologica IResearch', '2'=>''),
								'15'=>array('1'=>'Marketing', '2'=>''),
								'16'=>array('1'=>'President Ejecutivo', '2'=>''),
								'17'=>array('1'=>'Recepcion', '2'=>'')
								);
			
		
		
		
		
		$this->countusers = count($this->users);
		$this->countgroups = count($this->groups);
		$this->countdepartments = count($this->departments);		
	
	
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
		$this->defineVars();
		return $sessionId; 
	}
	
	public function createUsrs($sessionId,$email)
	{
		
		for ($i=1;$i<=$this->countusers;$i++)
		{
			$parameters = array('sessionId'=>$sessionId, 'userId'=>strtolower($this->users[$i][1]), 'firstname'=>$this->users[$i][1], 'lastname'=>$this->users[$i][2], 'email'=>$email, 'role'=>'PROCESSMAKER_OPERATOR', 'password'=>'sample');
			$response = $this->client->createUser($parameters);
			if($response->status_code==0)
			{
				$this->users[$i][5]=$response->userUID;
				echo "User added successfully \n";
			}
			else 
			{
				echo "User exist's!! \n";
			}
		}
	}
	
	public function createGroups($sessionId)
	{
		for ($j=1;$j<=$this->countgroups;$j++)
		{
			$parameters = array('sessionId'=>$sessionId, 'name'=>$this->groups[$j][1]);
			$response = $this->client->createGroup($parameters);
			if($response->status_code==0)
			{
				$this->groups[$j][2]=$response->groupUID;
				echo "Group create successfully \n";
			}
			else
			{
				echo "Group exist's!! \n";
			}			
		}
	}
	
	public function createDepartments($sessionId)
	{
		for ($k=1;$k<$this->countdepartments;$k++)
		{
			$parameters = array('sessionId'=>$sessionId, 'name'=>$this->departments[$k][1], 'parentUID'=>'');
			$response = $this->client->CreateDepartment($parameters);
			if($response->status_code==0)
			{
				$this->departments[$k][2]= $response->departmentUID;
				echo "Department create successfully \n";
			}
			else
			{
				echo "Department exist's!! \n";
			}
			
		}
	}
	
	public function assignUserToDepartment($sessionId)
	{
		
		for($r=1;$r<=$this->countusers;$r++)
		{
			for($s=1;$s<=$this->countdepartments;$s++)
			{
				if($this->users[$r][4]==$this->departments[$s][1])
				{
					$parameters = array('sessionId'=>$sessionId, 'userId'=>$this->users[$r][5], 'departmentId'=>$this->departments[$s][2], 'manager'=>'');
					$reponse = $this->client->assignUserToDepartment($parameters);
					echo "User assigned to group \n";
				}
			}
		
		
		}
		
		
	}
	
	public function assignUserToGroup($sessionId)
	{
		for($o=1;$o<=$this->countusers;$o++)
		{					
			$grusr = explode('|',$this->users[$o][3]);
			for($p=0;$p<count($grusr);$p++)
			{
				for($q=1;$q<=$this->countgroups;$q++)
				{
					if($grusr[$p]==$this->groups[$q][1])
					{
						$parameters = array('sessionId'=>$sessionId, 'userId'=>$this->users[$o][5], 'groupId'=>$this->groups[$q][2]);
						$reponse = $this->client->assignUserToGroup($parameters);
						echo "User assigned to department \n";
					}
				}					
			}
				
		}	
		
	}
	
	
}


