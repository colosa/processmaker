<?php
require_once 'externalclass.php';





/*$num = sizeof($funcname);
for($i=0;$i<$num;$i++)
{
	$getfunc = new ReflectionMethod($nameclass, $funcname[$i]);
	echo $funcname[$i]."\n";
	$params = array();
	foreach ($getfunc->getParameters() as $param) {
		echo $param->getName()."\n";
		//echo $param->isOptional();		
	}
	
}*/	


try {
	$class = new externalclass();
	
	$nameclass = get_class($class);
	
	$class_methods = get_class_methods($nameclass);
	
	$funcname[] = array();
	$count = 0;
	
	foreach($class_methods as $method_name)
	{
		$funcname[$count] = $method_name;
		echo $funcname[$count]."\n";
		$count++;
	}
	echo $funcname[0]."\n";
	
	for($i=0;$i<$num;$i++)
	{
		$getfunc = new ReflectionMethod($nameclass, $funcname[$i]);
		echo $funcname[$i]."\n";
		$params = array();
		foreach ($getfunc->getParameters() as $param) {
			$params[] = $param->getName();
			//echo $param->isOptional();
		}
		
		$count2 = sizeof($params);
	
	}
	
	
}
catch (Exception $e){
	
}


/*$reflection = new ReflectionClass($nameclass);
 $methods= $reflection->getMethod($nameclass);
$funcname = array();
$count = 0;

foreach($methods as $methodname)
{
$funcname[$count] = $methodname;
echo $funcname[$count]."\n";
$count++;

}*/

/*
 $docComment = $constructor->getDocComment();
$params = $constructor->getParameters();
foreach ($params AS $param) {
$name = $param->getName();
echo $name ." is ";
//echo $param->getHint()."\n"; // in PHP 5.4
echo getHint($docComment, $name)."\n"; // work around
}*/