<?php

class DeleteCases
{
	protected $host;
	protected $user;
	protected $pass;
	protected $dbname;
	
	function __construct($host,$usr,$pwd,$db)
	{
		$this->host = $host;
		$this->user = $usr;
		$this->pass = $pwd;
		$this->dbname = $db;
	}
	function connect()
	{
		$conn = mysql_connect($this->host,$this->user,$this->pass);
		if(!$conn)
		{
			die('Could not connect: '.mysql_error());
		}
				
		mysql_select_db($this->dbname);
		mysql_set_charset('utf8',$conn);
		
		return $conn;
	}
	
	function deleteAllCases()
	{
		$query = "DELETE FROM APP_DOCUMENT";
		mysql_query($query) or die("Error: $query");
			
		$query = "DELETE FROM APP_EVENT";
		mysql_query($query) or die("Error: $query");
			
		$query = "DELETE FROM APP_MESSAGE";
		mysql_query($query) or die("Error: $query");
			
		$query = "DELETE FROM APP_OWNER";
		mysql_query($query) or die("Error: $query");
			
		$query = "DELETE FROM APP_THREAD";
		mysql_query($query) or die("Error: $query");
			
		$query = "DELETE FROM SUB_APPLICATION";
		mysql_query($query) or die("Error: $query");
		
		$query = "DELETE FROM APP_DELAY";
		mysql_query($query) or die("Error: $query");
		
		$query = "DELETE FROM APP_DELEGATION";
		mysql_query($query) or die("Error: $query");
		
		$query = "DELETE FROM APP_CACHE_VIEW";
		mysql_query($query) or die("Error: $query");
		
		$query = "DELETE FROM APP_HISTORY";
		mysql_query($query) or die("Error: $query");
		
		$query = "DELETE FROM APPLICATION";
		mysql_query($query) or die("Error: $query");
	
		return "Deleted successfully";
	}	
	
	function deleteProcessCases($processId)
	{
		$query = "SELECT * FROM APPLICATION WHERE PRO_UID='$processId'";
		$result = mysql_query($query) or die("Error: $query");
		while($row=mysql_fetch_array($result))
		{
			$query_case = "DELETE FROM APP_DOCUMENT WHERE APP_UID='".$result['APP_UID']."'";
			mysql_query($query_case) or die("Error: $query");
			
			$query_case = "DELETE FROM APP_EVENT WHERE APP_UID='".$result['APP_UID']."'";
			mysql_query($query_case) or die("Error: $query");
			
			$query_case = "DELETE FROM APP_MESSAGE WHERE APP_UID='".$row['APP_UID']."'";
			mysql_query($query_case) or die("Error: $query");
			
			$query_case = "DELETE FROM APP_OWNER WHERE APP_UID='".$row['APP_UID']."'";
			mysql_query($query_case) or die("Error: $query");
			
			$query_case = "DELETE FROM APP_THREAD WHERE APP_UID='".$row['APP_UID']."'";
			mysql_query($query_case) or die("Error: $query");
			
			$query_case = "DELETE FROM SUB_APPLICATION WHERE APP_UID='".$row['APP_UID']."'";
			mysql_query($query_case) or die("Error: $query");
		}
		
		$query = "DELETE FROM APP_DELAY WHERE PRO_UID='$processId'";
		mysql_query($query) or die("Error: $query");
		
		$query = "DELETE FROM APP_DELEGATION WHERE PRO_UID='$processId'";
		mysql_query($query) or die("Error: $query");
		
		$query = "DELETE FROM APP_CACHE_VIEW WHERE PRO_UID='$processId'";
		mysql_query($query) or die("Error: $query");
		
		$query = "DELETE FROM APP_HISTORY WHERE PRO_UID='$processId'";
		mysql_query($query) or die("Error: $query");
		
		$query = "DELETE FROM APPLICATION WHERE PRO_UID='$processId'";
		mysql_query($query) or die("Error: $query");
		
		return "Deleted successfully";
	}
	
	function deleteCase($caseId)
	{
		$query = "DELETE FROM APP_DOCUMENT WHERE APP_UID='$caseId'";
		mysql_query($query) or die("Error: $query");
			
		$query = "DELETE FROM APP_EVENT WHERE APP_UID='$caseId'";
		mysql_query($query) or die("Error: $query");
			
		$query = "DELETE FROM APP_MESSAGE WHERE APP_UID='$caseId'";
		mysql_query($query) or die("Error: $query");
			
		$query = "DELETE FROM APP_OWNER WHERE APP_UID='$caseId'";
		mysql_query($query) or die("Error: $query");
			
		$query = "DELETE FROM APP_THREAD WHERE APP_UID='$caseId'";
		mysql_query($query) or die("Error: $query");
			
		$query = "DELETE FROM SUB_APPLICATION WHERE APP_UID='$caseId'";
		mysql_query($query) or die("Error: $query");
		
		$query = "DELETE FROM APP_DELAY WHERE APP_UID='$caseId'";
		mysql_query($query) or die("Error: $query");
		
		$query = "DELETE FROM APP_DELEGATION WHERE APP_UID='$caseId'";
		mysql_query($query) or die("Error: $query");
		
		$query = "DELETE FROM APP_CACHE_VIEW WHERE APP_UID='$caseId'";
		mysql_query($query) or die("Error: $query");
		
		$query = "DELETE FROM APP_HISTORY WHERE APP_UID='$caseId'";
		mysql_query($query) or die("Error: $query");
		
		$query = "DELETE FROM APPLICATION WHERE APP_UID='$caseId'";
		mysql_query($query) or die("Error: $query");
	
		return "Deleted successfully";
	}	
	
	function disconnect($conn)
	{
		mysql_close($conn);
	}
}
