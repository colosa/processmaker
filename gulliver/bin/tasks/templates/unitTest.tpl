<?php
  $unitFilename = $_SERVER['PWD'] . '/test/bootstrap/unit.php' ;
  require_once( $unitFilename );

  require_once( PATH_THIRDPARTY . '/lime/lime.php');
  require_once( PATH_THIRDPARTY.'lime/yaml.class.php');
 

  G::LoadSystem ( 'xmlform');
  G::LoadSystem ( 'xmlDocument');
  G::LoadSystem ( 'form');
  require_once( 'propel/Propel.php' );
  require_once ( "creole/Creole.php" );
  require_once (  PATH_CORE . "config/databases.php");  

  $obj = new {$className} ($dbc); 
  $t   = new lime_test( {$testItems}, new lime_output_color() );

  $className = {$className};
  $className = strtolower ( substr ($className, 0,1) ) . substr ($className, 1 );
  
  $reflect = new ReflectionClass( $className );
	$method = array ( );
	$testItems = 0;
 
  foreach ( $reflect->getMethods() as $reflectmethod ) {literal} { {/literal} 
  	$params = '';
  	foreach ( $reflectmethod->getParameters() as $key => $row )  {literal} { {/literal} 
  	  if ( $params != '' ) $params .= ', ';
  	  $params .= '$' . $row->name;  
  	}

 		$testItems++;
  	$methods[ $reflectmethod->getName() ] = $params;
  }
  //To change the case only the first letter of each word, TIA
  $className = ucwords($className);
  $t->diag("class $className" );

  $t->isa_ok( $obj  , $className,  "class $className created");

  $t->is( count($methods) , {$cantMethods},  "class $className have " . {$cantMethods} . ' methods.' );

 {foreach from=$methods key=methodName item=parameters}
  //checking method '{$methodName}'
  $t->can_ok( $obj,      '{$methodName}',   '{$methodName}() is callable' );

  //$result = $obj->{$methodName} ( {$parameters});
  //$t->isa_ok( $result,      'NULL',   'call to method {$methodName} ');
  $t->todo( "call to method {$methodName} using {$parameters} ");


{/foreach}

  $t->todo (  'review all pendings methods in this class');
