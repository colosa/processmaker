#!/usr/bin/php
<?php
  /**
    this file is used to generate test units for all files folders in processmaker class folder
  */

define('ROOT_PATH', realpath(dirname(__FILE__) . '/../..') . '/');
global $tokens;
global $dispatchIniFile;
global $fp;
global $outputDir;

$dispatchIniFile = ROOT_PATH . "configs/dispatch.ini";
$inputDir        = ROOT_PATH . 'businessLogic/modules/home/Services';
$outputDir       = ROOT_PATH . 'tests/automated/';

print "Output directory: $outputDir\n";

define ('PATH_GULLIVER',   ROOT_PATH . 'gulliver/system/');
define ('PATH_THIRDPARTY', ROOT_PATH . 'gulliver/thirdparty/');
define ('PATH_CORE', ROOT_PATH . 'workflow/engine');
define ('SYS_SKIN', 'classic');
define ('SYS_LANG', 'en');

// set include path
set_include_path(
    PATH_CORE . PATH_SEPARATOR .
    PATH_THIRDPARTY . PATH_SEPARATOR .
    PATH_THIRDPARTY . 'pear'. PATH_SEPARATOR .
    get_include_path()
);


require_once ROOT_PATH . 'gulliver/thirdparty/smarty/libs/Smarty.class.php';
require_once ROOT_PATH . 'gulliver/system/class.g.php';
require_once ROOT_PATH . 'gulliver/system/class.xmlform.php';
require_once ROOT_PATH . 'gulliver/system/class.xmlDocument.php';
require_once ROOT_PATH . 'gulliver/system/class.form.php';
require_once ROOT_PATH . 'gulliver/thirdparty/propel/Propel.php';
require_once ROOT_PATH . 'gulliver/system/class.dbtable.php';
require_once ROOT_PATH . 'gulliver/system/class.dbconnection.php';

//parsingFolder('gulliver/system', 'class.form.php class.objectTemplate.php class.tree.php class.xmlform.php class.filterForm.php');
parsingFolder('gulliver/system', 'class.database_mssql.php class.database_mysql.php class.htmlArea.php ' .
    'class.dbconnection.php class.dbrecordset.php class.dbsession.php class.soapNtlm.php class.webResource.php' );
parsingFolder('workflow/engine/classes',
    'class.xmlfield_Image.php class.groupUser.php class.group.php class.system.php ' .
    'class.ArrayPeer.php class.ArrayPeer.php class.BasePeer.php class.dbConnections.php class.webdav.php');

function parsingFolder ( $folder, $exceptionsText )
{
    $baseDir         = ROOT_PATH;
    $exceptions = explode(' ', trim($exceptionsText)) ;
  	if ($handle = opendir( $baseDir . $folder)) {
      	while (false !== ($entry = readdir($handle))) {
      		if ( is_dir($baseDir . $folder . '/' . $entry) ) {
      		}
      	  	if ( is_file($baseDir . $folder . '/' . $entry)  && substr($entry,-4) == ".php" && substr($entry,0,6) == "class." ) {
      	  		if ( !in_array($entry,$exceptions)  ) {
      			  //print "parsing $baseDir$folder/$entry \n";
      			  parsingFile ( $folder , $entry);
      	  		}
      	  	}
      	}
  	    closedir($handle);
    }
}

function parsingFile ( $folder, $entry )
{
    global $tokens;
    global $fp;
    global $outputDir;

    $baseDir = ROOT_PATH;

    $file = $baseDir . $folder . '/' . $entry;
    $content = file_get_contents ( $file );

    //get all tokens
    $tokens = token_get_all ($content);

    //remove spaces
    $temp = array();
    foreach ( $tokens as $k => $token ) {
      if ( is_array($token) && $token[0] != T_WHITESPACE ) {
        $temp[] = $tokens[$k];
      }
      if ( !is_array($token)) {
      	$temp[] = $token;
      }
    }
    $tokens = $temp;

    $className = '';
    $comments  = '';
    $path = '/' . str_replace (ROOT_PATH, '', $file);

    $atLeastOneClass = false;

    foreach ( $tokens as $k => $token ) {
      if ( is_array($token) ) {
      	//looking for classes
        if ( $token[0] == T_CLASS  ) {
          if ( $atLeastOneClass ) {
          	fprintf ( $fp, "  } \n" );
          }
          $atLeastOneClass = true;
          $className = nextToken( T_STRING, $k );
          if (strpos(strtolower($entry), strtolower($className))) {
              print "--> $className\n";
          } else {
              print "--> $className ($entry)\n";
          }
          $classFile = $folder . '/' . $entry;
          if ( !is_dir($outputDir . $folder)) {
            mkdir($outputDir . $folder, 0777, true);
          }
          $fp = fopen ( $outputDir . $folder . '/class' . $className . 'Test.php', 'w');
          fprintf ( $fp, "<?php\n" );

          fprintf ( $fp, "require_once PATH_TRUNK . 'gulliver/thirdparty/smarty/libs/Smarty.class.php';\n");
          fprintf ( $fp, "require_once PATH_TRUNK . 'gulliver/system/class.xmlform.php';\n");
          fprintf ( $fp, "require_once PATH_TRUNK . 'gulliver/system/class.xmlDocument.php';\n");
          fprintf ( $fp, "require_once PATH_TRUNK . 'gulliver/system/class.form.php';\n");
          fprintf ( $fp, "require_once PATH_TRUNK . 'gulliver/system/class.dbconnection.php';\n");
          // setup propel definitions and logging
          fprintf ( $fp, "require_once PATH_TRUNK . 'gulliver/thirdparty/propel/Propel.php';\n");
          fprintf ( $fp, "require_once PATH_TRUNK . 'gulliver/thirdparty/creole/Creole.php';\n");
          fprintf ( $fp, "require_once PATH_TRUNK . 'gulliver/thirdparty/pear/PEAR.php';\n");
          fprintf ( $fp, "require_once PATH_TRUNK . '%s/%s';\n\n", $folder, $entry);
          require_once ROOT_PATH . "$folder/$entry";
          fprintf ( $fp, "/**\n");
          fprintf ( $fp, " * Generated by ProcessMaker Test Unit Generator on %s at %s.\n", date('Y-m-d'), date('H:i:s'));
          fprintf ( $fp, "*/\n\n");
          fprintf ( $fp, "class class%sTest extends PHPUnit_Framework_TestCase\n", $className);
          fprintf ( $fp, "{\n" );
          fprintf ( $fp, "    /**\n");
          fprintf ( $fp, "     * @var %s\n",$className);
          fprintf ( $fp, "    */\n");
          fprintf ( $fp, "    protected \$object;\n\n");
          fprintf ( $fp, "    /**\n" );
          fprintf ( $fp, "     * Sets up the fixture, for example, opens a network connection.\n" );
          fprintf ( $fp, "     * This method is called before a test is executed.\n" );
          fprintf ( $fp, "    */\n" );
          fprintf ( $fp, "    protected function setUp()\n");
          fprintf ( $fp, "    {\n");
          fprintf ( $fp, "        \$this->object = new %s();\n", $className);
          fprintf ( $fp, "    }\n\n");
          fprintf ( $fp, "    /**\n" );
          fprintf ( $fp, "     * Tears down the fixture, for example, closes a network connection.\n" );
          fprintf ( $fp, "     * This method is called after a test is executed.\n" );
          fprintf ( $fp, "    */\n" );
          fprintf ( $fp, "    protected function tearDown()\n");
          fprintf ( $fp, "    {\n");
          fprintf ( $fp, "    }\n");
          fprintf ( $fp, "\n");

          $methods = get_class_methods($className);
          fprintf ( $fp, "    /**\n" );
          fprintf ( $fp, "     * This is the default method to test, if the class still having \n" );
          fprintf ( $fp, "     * the same number of methods.\n" );
          fprintf ( $fp, "    */\n" );
          fprintf ( $fp, "    public function testNumberOfMethodsInThisClass()\n");
          fprintf ( $fp, "    {\n");
          fprintf ( $fp, "        \$methods = get_class_methods('%s');", $className );
          fprintf ( $fp, "        \$this->assertTrue( count(\$methods) == %s);\n", count($methods) );
          fprintf ( $fp, "    }\n");
          fprintf ( $fp, "\n");

        }

        if ( $token[0] == T_FUNCTION ) {
            $functionName = nextToken( T_STRING, $k );
            $public       = previousToken( T_PUBLIC, $k );
            $comments     = previousToken( T_DOC_COMMENT, $k );
            parseFunction ( $k, $path, $className, $functionName, $comments);
            //if ( strtolower($public) == 'public' ) parsePublic ( $path, $className, $functionName, $comments );
        }
      }
    }
    if ( $atLeastOneClass ) {
    	fprintf ( $fp, "  } \n" );
    }
}

/*
[GetCaseInfo]
  class = BpmnEngine_Services_Case
  path = /businessLogic/modules/bpmnEngine/Services/Case.php
  gearman = false
  rest = false
  background = false
*/

  function parseFunction ( $k, $path, $className, $functionName, $comments ) {
    global $fp;
    global $tokens;
    if ( trim($className) == '' ) return;
    $comm = explode ("\n", $comments);

    $params = array();
    //print "     --> $functionName ( ";

    //search for first ( open parenthesis
    $openParenthesis = false;
    $closeParenthesis = false;
    while ( ! $openParenthesis ) {
    	if (! is_array($tokens[$k]) && $tokens[$k] == '(' )
    		$openParenthesis = true;
    	$k++;
    }
    while ( ! $closeParenthesis ) {
        if (is_array($tokens[$k]) && $tokens[$k][0] == T_VARIABLE ) {
       	  //print " " . $tokens[$k][1];
        }
        if (! is_array($tokens[$k]) && $tokens[$k] == ')' ) {
    		$closeParenthesis = true;
    		//print " \n";
        }
    	$k++;
    }

    $methods = get_class_methods($className);
    if (!in_array($functionName, $methods ) ) {
        return;
    }
    fprintf ( $fp, "    /**\n" );
    fprintf ( $fp, "    * @covers %s::%s\n", $className, $functionName );
    fprintf ( $fp, "    * @todo   Implement test%s().\n", $functionName );
    fprintf ( $fp, "    */\n" );
    fprintf ( $fp, "    public function test%s()\n", $functionName );
    fprintf ( $fp, "    {\n" );
    fprintf ( $fp, "        \$methods = get_class_methods(\$this->object);\n");
    fprintf ( $fp, "        \$this->assertTrue( in_array('%s', \$methods ), 'exists method %s' );\n",$functionName,$functionName);
    fprintf ( $fp, "        \$r = new ReflectionMethod('%s', '%s');\n", $className, $functionName );
    fprintf ( $fp, "        \$params = \$r->getParameters();\n");
    $r = new ReflectionMethod($className, $functionName);
    $params = $r->getParameters();
    foreach ( $params as $key=>$param) {
        fprintf ( $fp, "        \$this->assertTrue( \$params[$key]->getName() == '%s');\n", $param->getName());
        fprintf ( $fp, "        \$this->assertTrue( \$params[$key]->isArray() == %s);\n", $param->isArray() == true ? 'true':'false');
        fprintf ( $fp, "        \$this->assertTrue( \$params[$key]->isOptional () == %s);\n", $param->isOptional() == true ? 'true':'false');
        if ($param->isOptional()) {
            fprintf ( $fp, "        \$this->assertTrue( \$params[$key]->getDefaultValue() == '%s');\n", $param->getDefaultValue() );
        }
    }
//    fprintf ( $fp, "        \$this->markTestIncomplete('This test has not been implemented yet.');\n\n");


    fprintf ( $fp, "    } \n\n" );
  }

  function parsePublic ( $path, $className, $functionName, $comments ) {
  	global $fp;
  	$comm = explode ("\n", $comments);
  	$gearman    = false;
  	$rest       = false;
  	$background = false;
  	foreach ( $comm as $k => $line ) {
  		$line = trim(str_replace('*','',$line));
  		if (substr($line,0, 13) == '@background =') $background = strtolower(trim(substr( $line,14 )));
  		if (substr($line,0, 10) == '@gearman =')    $gearman    = strtolower(trim(substr( $line,11 )));
  		if (substr($line,0,  7) == '@rest =')       $rest       = strtolower(trim(substr( $line,7 )));
  	}
  	fprintf ( $fp, "[$functionName]\n  class = $className\n  path  = $path\n" );
  	fprintf ( $fp, "  gearman = " .    ($gearman   == 'true' ? 'true' : 'false') . "\n" );
  	fprintf ( $fp, "  background = " . ($background== 'true' ? 'true' : 'false') . "\n" );
  	fprintf ( $fp, "  rest = " .       ($rest      == 'true' ? 'true' : 'false') . "\n" );
  	fprintf ( $fp, "\n" );
  }



  function nextToken( $type, $k ) {
    global $tokens;
    do {
     $k++;
      if ($tokens[$k][0] == T_FUNCTION  || $tokens[$k][0] == T_CLASS ) {
        return '';
      }
    } while ( $k < count($tokens) && $tokens[$k][0] != $type );
    if ( isset($tokens[$k]) ) {
      return $tokens[$k][1];
    }
    else {
      return '';
    }
  }

  function previousToken( $type, $k ) {
    global $tokens;
    do {
      $k--;
      if ($tokens[$k][0] == T_FUNCTION || $tokens[$k][0] == T_CLASS ) {
        return '';
      }
    } while ( $k > 0 && $tokens[$k][0] != $type );

    if ( isset($tokens[$k]) ) {
      return $tokens[$k][1];
    }
    else {
      return '';
    }
  }


