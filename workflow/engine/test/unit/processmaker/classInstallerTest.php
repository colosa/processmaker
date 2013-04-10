<?php
  $unitFilename = $_SERVER['PWD'] . '/test/bootstrap/unit.php' ;
  require_once( $unitFilename );

  require_once( PATH_THIRDPARTY . '/lime/lime.php');
  require_once( PATH_THIRDPARTY.'lime/yaml.class.php');
  require_once( 'propel/Propel.php' );
  require_once ( "creole/Creole.php" );
  Propel::init(  PATH_CORE . "config/databases.php");


  G::LoadThirdParty('smarty/libs','Smarty.class');
  G::LoadSystem ( 'error');
  G::LoadSystem ( 'xmlform');
  G::LoadSystem ( 'xmlDocument');
  G::LoadSystem ( 'form');

  require_once (  PATH_CORE . "config/databases.php");  

  G::LoadClass ( 'Installer');

  $obj = new Installer ($dbc); 
  $t   = new lime_test( 35, new lime_output_color() );

  $className = Installer;
  $className = strtolower ( substr ($className, 0,1) ) . substr ($className, 1 );
  
  $reflect = new ReflectionClass( $className );
	$method = array ( );
	$testItems = 0;
 
  foreach ( $reflect->getMethods() as $reflectmethod )  { 
  	$params = '';
  	foreach ( $reflectmethod->getParameters() as $key => $row )   {  
  	  if ( $params != '' ) $params .= ', ';
  	  $params .= '$' . $row->name;  
  	}

 		$testItems++;
  	$methods[ $reflectmethod->getName() ] = $params;
  }
 
  $t->diag( '$className' );

  $t->isa_ok( $obj  , 'Installer',  'class $className created');

  $t->is( count($methods) , 16,  "class $className have " . 16 . ' methods.' );

  //checking method '__construct'
  $t->can_ok( $obj,      '__construct',   '__construct() is callable' );

  //$result = $obj->__construct ( );
  //$t->isa_ok( $result,      'NULL',   'call to method __construct ');
  $t->todo( "call to method __construct using  ");


  //checking method 'create_site'
  $t->can_ok( $obj,      'create_site',   'create_site() is callable' );

  //$result = $obj->create_site ( $config, $confirmed);
  //$t->isa_ok( $result,      'NULL',   'call to method create_site ');
  $t->todo( "call to method create_site using $config, $confirmed ");


  //checking method 'isset_site'
  $t->can_ok( $obj,      'isset_site',   'isset_site() is callable' );

  //$result = $obj->isset_site ( $name);
  //$t->isa_ok( $result,      'NULL',   'call to method isset_site ');
  $t->todo( "call to method isset_site using $name ");


  //checking method 'create_site_test'
  $t->can_ok( $obj,      'create_site_test',   'create_site_test() is callable' );

  //$result = $obj->create_site_test ( );
  //$t->isa_ok( $result,      'NULL',   'call to method create_site_test ');
  $t->todo( "call to method create_site_test using  ");


  //checking method 'make_site'
  $t->can_ok( $obj,      'make_site',   'make_site() is callable' );

  //$result = $obj->make_site ( );
  //$t->isa_ok( $result,      'NULL',   'call to method make_site ');
  $t->todo( "call to method make_site using  ");


  //checking method 'setAdmin'
  $t->can_ok( $obj,      'setAdmin',   'setAdmin() is callable' );

  //$result = $obj->setAdmin ( );
  //$t->isa_ok( $result,      'NULL',   'call to method setAdmin ');
  $t->todo( "call to method setAdmin using  ");


  //checking method 'query_sql_file'
  $t->can_ok( $obj,      'query_sql_file',   'query_sql_file() is callable' );

  //$result = $obj->query_sql_file ( $file, $connection);
  //$t->isa_ok( $result,      'NULL',   'call to method query_sql_file ');
  $t->todo( "call to method query_sql_file using $file, $connection ");


  //checking method 'check_path'
  $t->can_ok( $obj,      'check_path',   'check_path() is callable' );

  //$result = $obj->check_path ( );
  //$t->isa_ok( $result,      'NULL',   'call to method check_path ');
  $t->todo( "call to method check_path using  ");


  //checking method 'find_root_path'
  $t->can_ok( $obj,      'find_root_path',   'find_root_path() is callable' );

  //$result = $obj->find_root_path ( $path);
  //$t->isa_ok( $result,      'NULL',   'call to method find_root_path ');
  $t->todo( "call to method find_root_path using $path ");


  //checking method 'file_permisions'
  $t->can_ok( $obj,      'file_permisions',   'file_permisions() is callable' );

  //$result = $obj->file_permisions ( $file, $def);
  //$t->isa_ok( $result,      'NULL',   'call to method file_permisions ');
  $t->todo( "call to method file_permisions using $file, $def ");


  //checking method 'is_dir_writable'
  $t->can_ok( $obj,      'is_dir_writable',   'is_dir_writable() is callable' );

  //$result = $obj->is_dir_writable ( $dir);
  //$t->isa_ok( $result,      'NULL',   'call to method is_dir_writable ');
  $t->todo( "call to method is_dir_writable using $dir ");


  //checking method 'getDirectoryFiles'
  $t->can_ok( $obj,      'getDirectoryFiles',   'getDirectoryFiles() is callable' );

  //$result = $obj->getDirectoryFiles ( $dir, $extension);
  //$t->isa_ok( $result,      'NULL',   'call to method getDirectoryFiles ');
  $t->todo( "call to method getDirectoryFiles using $dir, $extension ");


  //checking method 'check_db_empty'
  $t->can_ok( $obj,      'check_db_empty',   'check_db_empty() is callable' );

  //$result = $obj->check_db_empty ( $dbName);
  //$t->isa_ok( $result,      'NULL',   'call to method check_db_empty ');
  $t->todo( "call to method check_db_empty using $dbName ");


  //checking method 'check_db'
  $t->can_ok( $obj,      'check_db',   'check_db() is callable' );

  //$result = $obj->check_db ( $dbName);
  //$t->isa_ok( $result,      'NULL',   'call to method check_db ');
  $t->todo( "call to method check_db using $dbName ");


  //checking method 'check_connection'
  $t->can_ok( $obj,      'check_connection',   'check_connection() is callable' );

  //$result = $obj->check_connection ( );
  //$t->isa_ok( $result,      'NULL',   'call to method check_connection ');
  $t->todo( "call to method check_connection using  ");


  //checking method 'log'
  $t->can_ok( $obj,      'log',   'log() is callable' );

  //$result = $obj->log ( $text);
  //$t->isa_ok( $result,      'NULL',   'call to method log ');
  $t->todo( "call to method log using $text ");



  $t->todo (  'review all pendings methods in this class');
