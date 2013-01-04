<?php

class Execfunctions
{
	public function RandomString($length=10,$uc=TRUE,$n=TRUE,$sc=FALSE)
	{
		$source = 'abcdefghijklmnopqrstuvwxyz';
		if($uc==1) $source .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		if($n==1) $source .= '1234567890';
		if($sc==1) $source .= '|@#~$%()=^*+[]{}-_';
		if($length>0){
			$rstr = "";
			$source = str_split($source,1);
			for($i=1; $i<=$length; $i++){
				mt_srand((double)microtime() * 1000000);
				$num = mt_rand(1,count($source));
				$rstr .= $source[$num-1];
			}
		}
		return $rstr;
	}
	
	
	
	public function valor($numb)
	{
		
		switch ($numb) 
		{
			case 1:
				{
					$val = rand(-10000, 10000);
					return $val;
					break;
				}	
			case 2:
				{
					$val = rand(-10000.00, 10000.00);
					return $val;
					break;
				}	
			case 3:
				{
					$val = $this->RandomString(12,TRUE,FALSE,TRUE);
					return $val;
					break;
				}	
			case 4:
				{
					$val = array();
					return $val;
					break;
				}	
			default:
				{
					$val = null;
					return $val;
					break;
				}	
				
		}
	}
	
	public function tipo($type)
	{
		switch ($type)
		{
			case 'num':
				{
					$value = $this->valor(1);
					return $value;
					break;
				}
			case 'float':
				{
					$value = $this->valor(2);
					return $value;
					break;
				}	
			case 'string':
				{
					$value = $this->valor(3);
					return $value;
					break;
				}	
			case 'array':
				{
					$value = $this->valor(4);
					return $value;
					break;
				}	
			default:
				{
					$value = null;
					return $value;
					break;
				}	
		}
	}
	
	public function exec_func($class,$function,$params)
	{
		$resul = call_user_func_array(array($class, $function),$params);
		return $resul;
	}
}
