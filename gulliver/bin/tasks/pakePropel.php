<?php
/**
 * pakePropel.php
 * @package gulliver.bin.tasks
 * 
 * ProcessMaker Open Source Edition
 * Copyright (C) 2004 - 2011 Colosa Inc.23
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * For more information, contact Colosa Inc, 2566 Le Jeune Rd.,
 * Coral Gables, FL, 33134, USA, or email info@colosa.com.
 * 
 */

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

 

pake_desc('create classes for current model');
pake_task('propel-build-model', 'project_exists');

pake_desc('create sql for current model');
pake_task('propel-build-sql', 'project_exists');

pake_desc('create schema.xml from existing database');
pake_task('propel-build-schema', 'project_exists');

//pake_desc('create schema.xml from schema.yml');
//pake_task('propel-convert-yml-schema', 'project_exists');

//pake_desc('create schema.yml from schema.xml');
//pake_task('propel-convert-xml-schema', 'project_exists');

//pake_desc('load data from fixtures directory');
//pake_task('propel-load-data', 'project_exists');

pake_desc('dump data to fixtures directory');
pake_task('propel-dump-data', 'project_exists');

pake_desc('create database for current model');
pake_task('propel-build-db', 'project_exists');

pake_desc('insert sql for current model');
pake_task('propel-insert-sql', 'project_exists');

//pake_desc('generate propel model and sql and initialize database');
//pake_task('propel-build-all', 'project_exists');

//pake_desc('generate propel model and sql and initialize database, and load data');
//pake_task('propel-build-all-load', 'propel-build-all');

/**
   * Function run_propel_convert_yml_schema
   * access public
   */

function run_propel_convert_yml_schema($task, $args)
{
  _propel_convert_yml_schema(true);
}

function run_propel_convert_xml_schema($task, $args)
{
  _propel_convert_xml_schema(true);
}

function _propel_convert_yml_schema($check_schema = true, $prefix = '')
{
  $finder = pakeFinder::type('file')->name('*schema.yml');
  $dirs = array('config');
  if ($pluginDirs = glob(sfConfig::get('sf_root_dir').'/plugins/*/config'))
  {
    $dirs = array_merge($dirs, $pluginDirs);
  }
  $schemas = $finder->in($dirs);
  if ($check_schema && !count($schemas))
  {
    throw new Exception('You must create a schema.yml file.');
  }

  $db_schema = new sfPropelDatabaseSchema();
  foreach ($schemas as $schema)
  {
    $db_schema->loadYAML($schema);

    pake_echo_action('schema', 'converting "'.$schema.'"'.' to XML');

    $localprefix = $prefix;

    // change prefix for plugins
    if (preg_match('#plugins[/\\\\]([^/\\\\]+)[/\\\\]#', $schema, $match))
    {
      $localprefix = $prefix.$match[1].'-';
    }

    // save converted xml files in original directories
    $xml_file_name = str_replace('.yml', '.xml', basename($schema));

    $file = str_replace(basename($schema), $localprefix.$xml_file_name,  $schema);
    pake_echo_action('schema', 'putting '.$file);
    file_put_contents($file, $db_schema->asXML());
  }
}

function _propel_convert_xml_schema($check_schema = true, $prefix = '')
{
  $finder = pakeFinder::type('file')->name('*schema.xml');

  $schemas = array_merge($finder->in('config'), $finder->in(glob(sfConfig::get('sf_root_dir').'/plugins/*/config')));
  if ($check_schema && !count($schemas))
  {
    throw new Exception('You must create a schema.xml file.');
  }

  $db_schema = new sfPropelDatabaseSchema();
  foreach ($schemas as $schema)
  {
    $db_schema->loadXML($schema);

    $localprefix = $prefix;

    // change prefix for plugins
    if (preg_match('#plugins[/\\\\]([^/\\\\]+)[/\\\\]#', $schema, $match))
    {
      $localprefix = $prefix.$match[1].'-';
    }

    // save converted xml files in original directories
    $yml_file_name = str_replace('.xml', '.yml', basename($schema));

    $file = str_replace(basename($schema), $prefix.$yml_file_name,  $schema);
    pake_echo_action('schema', 'putting '.$file);
    file_put_contents($file, $db_schema->asYAML());
  }
}

function _propel_copy_xml_schema_from_plugins($prefix = '')
{
  $schemas = pakeFinder::type('file')->name('*schema.xml')->in(glob(sfConfig::get('sf_root_dir').'/plugins/*/config'));

  foreach ($schemas as $schema)
  {
    // reset local prefix
    $localprefix = '';

    // change prefix for plugins
    if (preg_match('#plugins[/\\\\]([^/\\\\]+)[/\\\\]#', $schema, $match))
    {
      // if the plugin name is not in the schema filename, add it
      if (!strstr(basename($schema), $match[1]))
      {
        $localprefix = $match[1].'-';
      }
    }

    // if the prefix is not in the schema filename, add it
    if (!strstr(basename($schema), $prefix))
    {
      $localprefix = $prefix.$localprefix;
    }

    pake_copy($schema, 'config'.DIRECTORY_SEPARATOR.$localprefix.basename($schema));
    if ('' === $localprefix)
    {
      pake_remove($schema, '');
    }
  }
}

function run_propel_build_all($task, $args)
{
  run_propel_build_model($task, $args);
  run_propel_build_sql($task, $args);
  run_propel_insert_sql($task, $args);
}

function run_propel_build_all_load($task, $args)
{
  run_propel_build_all($task, $args);
  run_propel_load_data($task, $args);
}

function run_propel_build_model($task, $args)
{
  if ( isset ( $args[0] ) )  {
    $propelIniFile = $args[0] . '.ini';
    if ( ! file_exists ( 'config' . PATH_SEP . $propelIniFile ) ) {
      $path = explode ( '/', $args[0] );
      if ( count($path) > 2 )
        throw new Exception('the propel.ini must be in your config directory.');
      if ( count($path) == 1 )
        $path[1] = 'propel';

      $propelIniFile = 'plugins' .PATH_SEP . $path[0] . PATH_SEP . 'config' . PATH_SEP . $path[1] . '.ini';

      pake_echo_action('propel.ini', "using the file : $propelIniFile ");
      _call_phing($task, 'om', false, $path[1] . '.ini' , PATH_PLUGINS . $path[0] . PATH_SEP );
      return;
    }

  }
  _call_phing($task, 'om');
}

function run_propel_build_sql($task, $args)
{
    $alternateDir = '';
    if (isset ($args[1])) {
        $propelIniFile = $args[1] . '.ini';
        $alternateDir = '';
        if (!file_exists('config' . PATH_SEP . $propelIniFile)) {
            $path = explode('/', $args[1]);
            if (count($path) > 2)
                throw new Exception('the propel.ini must be in your config directory.');
            if (count($path) == 1)
                $path[1] = 'propel';

            $propelIniFile = 'plugins' . PATH_SEP . $path[0] . PATH_SEP . 'config' . PATH_SEP . $path[1] . '.ini';
            if (!file_exists($propelIniFile))
                throw new Exception("the propel.ini must be in your config directory. ($propelIniFile)");

            pake_echo_action('propel.ini', "using the file : $propelIniFile ");
            $alternateDir = PATH_PLUGINS . $path[0] . PATH_SEP;
        }

    }

    $aDB = array('mysql', 'mssql', 'oracle', 'pgsql');

    if (!in_array($args[0], $aDB)) {
        throw new Exception('invalid database Adapter, available:[mysql|mssql|oracle|pgsql].');
    } else {
        switch ($args [0]) {
            case 'mysql' :
                if ($alternateDir != '')
                    _call_phing($task, 'sql', false, 'propel.mysql.ini', $alternateDir);
                else
                    _call_phing($task, 'sql', true, 'propel.mysql.ini');
                break;
            case 'mssql' :
                _call_phing($task, 'sql', true, 'propel.mssql.ini');
                break;
            case 'oracle' :
                _call_phing($task, 'sql', true, 'propel.oracle.ini');
                break;
            case 'pgsql' :
                _call_phing($task, 'sql', true, 'propel.pgsql.ini');
                break;
            default :
                throw new Exception('specify database Adapter, valid values are: mysql, mssql, oracle, pgsql.');
        }

    }
}

function run_propel_build_db($task, $args)
{
  switch ( $args [0]) {
    case 'mysql' : _call_phing($task, 'create-db', true, 'propel.mysql.ini' );
                   break;
    case 'mssql' : _call_phing($task, 'create-db', true, 'propel.mssql.ini' );
                   break;
    case 'oracle' : _call_phing($task, 'create-db', true, 'propel.oracle.ini' );
                   break;
    case 'pgsql' : _call_phing($task, 'create-db', true, 'propel.pgsql.ini' );
                   break;
    default :
      throw new Exception('specify database Adapter, valid values are: mysql, mssql, oracle, pgsql.');
  }

}

function run_propel_insert_sql($task, $args)
{
  switch ( $args [0]) {
    case 'mysql' : //_call_phing($task, 'insert-sql', true, 'propel.mysql.ini' );
                   $filename = 'config/propel.mysql.ini';
                   $fd = fopen ($filename, "r");
                   $contents = fread ($fd,filesize ($filename));
                   fclose ($fd);
                   $delimiter = "\n";
                   $splitcontents = explode($delimiter, $contents);
                   foreach ($splitcontents as $key => $line)
                     if ( strpos ( $line, 'database.url' ) > 0 ) {
                       $param = explode ( '/', $line );
                       $database = $param [ count( $param) -1 ];
                       pake_echo_action('propel-insert-sql', "using the database : $database  ");
                     }

                   //exec ( 'mysqldump --add-drop-table --compatible=mysql40 --compact --default-character-set=utf8 --no-create-info  --complete-insert --extended-insert=false > data/mysql/insert.sql ' . $database );
                   exec ( 'mysqldump --password=atopml2005 --compatible=mysql40 --compact --default-character-set=utf8 --no-create-info  --complete-insert=false --extended-insert=true > data/mysql/insert.sql ' . $database );
                   break;
    case 'mssql' : _call_phing($task, 'insert-sql', true, 'propel.mssql.ini' );
                   break;
    case 'oracle' : _call_phing($task, 'insert-sql', true, 'propel.oracle.ini' );
                   break;
    case 'pgsql' : _call_phing($task, 'insert-sql', true, 'propel.pgsql.ini' );
                   break;
    default :
      throw new Exception('specify database Adapter, valid values are: mysql, mssql, oracle, pgsql.');
  }
}

function run_propel_build_schema($task, $args)
{
  $propelIniFile = 'propel.ini';
  if ( isset ( $args[0] ) )  {

    $propelIniFile = $args[0] . '.ini';
    if ( ! file_exists ( 'config' . PATH_SEP . $propelIniFile ) ) {
      $path = explode ( '/', $args[0] );
      if ( count($path) > 2 )
        throw new Exception('the propel.ini must be in your config directory.');
      if ( count($path) == 1 )
        $path[1] = 'propel';

      $propelIniFile = 'plugins' .PATH_SEP . $path[0] . PATH_SEP . 'config' . PATH_SEP . $path[1] . '.ini';

      pake_echo_action('propel.ini', "using the file : $propelIniFile ");
      _call_phing($task, 'creole', false, $path[1] . '.ini' , PATH_PLUGINS . $path[0] . PATH_SEP );

      // fix database name
      if (file_exists(PATH_PLUGINS . $path[0] . PATH_SEP . 'config/schema.xml'))
      {
        $schema = file_get_contents(PATH_PLUGINS . $path[0] . PATH_SEP . 'config/schema.xml');
        $schema = preg_replace('/<database\s+name="[^"]+"/s', '<database name="propel"', $schema);
        file_put_contents(PATH_PLUGINS . $path[0] . PATH_SEP . 'config/schema.xml', $schema);
      }
      return;
    }

  }
  pake_echo_action('propel.ini', "using the file : $propelIniFile ");

  _call_phing($task, 'creole', false, $propelIniFile );

  // fix database name
  if (file_exists('config/schema.xml'))
  {
    $schema = file_get_contents('config/schema.xml');
    $schema = preg_replace('/<database\s+name="[^"]+"/s', '<database name="propel"', $schema);
    file_put_contents('config/schema.xml', $schema);
  }

}

/**
 * Dumps yml database data to fixtures directory.
 *
 * example symfony dump-data frontend data.yml
 * example symfony dump-data frontend data.yml dev
 *
 * @param object $task
 * @param array $args
 */
function run_propel_dump_data($task, $args)
{

  $filename = 'demo.sql';
  $app = 'app';
  $env = 'dev' ;

  // define constants
  define('SF_ROOT_DIR',    PATH_CORE . 'config' );
  define('SF_APP',         $app);
  define('SF_ENVIRONMENT', $env);
  define('SF_DEBUG',       true);

  // get configuration
  //require_once SF_ROOT_DIR.DIRECTORY_SEPARATOR.'apps'.DIRECTORY_SEPARATOR.SF_APP.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config.php';

  //$databaseManager = new sfDatabaseManager();
  //$databaseManager->initialize();

  pake_echo_action('propel', sprintf('dumping data to "%s"', $filename));

  $data = new sfPropelData();
  $data->dumpData($filename);
}

/**
 * Loads yml data from fixtures directory and inserts into database.
 *
 * example symfony load-data frontend
 * example symfony load-data frontend dev fixtures append
 *
 * @todo replace delete argument with flag -d
 *
 * @param object $task
 * @param array $args
 */
function run_propel_load_data($task, $args)
{
  if (!count($args))
  {
    throw new Exception('You must provide the app.');
  }

  $app = $args[0];

  if (!is_dir(sfConfig::get('sf_app_dir').DIRECTORY_SEPARATOR.$app))
  {
    throw new Exception('The app "'.$app.'" does not exist.');
  }

  if (count($args) > 1 && $args[count($args) - 1] == 'append')
  {
    array_pop($args);
    $delete = false;
  }
  else
  {
    $delete = true;
  }

  $env = empty($args[1]) ? 'dev' : $args[1];

  // define constants
  define('SF_ROOT_DIR',    sfConfig::get('sf_root_dir'));
  define('SF_APP',         $app);
  define('SF_ENVIRONMENT', $env);
  define('SF_DEBUG',       true);

  // get configuration
  require_once SF_ROOT_DIR.DIRECTORY_SEPARATOR.'apps'.DIRECTORY_SEPARATOR.SF_APP.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config.php';

  if (count($args) == 1)
  {
    if (!$pluginDirs = glob(sfConfig::get('sf_root_dir').'/plugins/*/data'))
    {
      $pluginDirs = array();
    }
    $fixtures_dirs = pakeFinder::type('dir')->name('fixtures')->in(array_merge($pluginDirs, array(sfConfig::get('sf_data_dir'))));
  }
  else
  {
    $fixtures_dirs = array_slice($args, 1);
  }

  $databaseManager = new sfDatabaseManager();
  $databaseManager->initialize();

  $data = new sfPropelData();
  $data->setDeleteCurrentData($delete);

  foreach ($fixtures_dirs as $fixtures_dir)
  {
    if (!is_readable($fixtures_dir))
    {
      continue;
    }

    pake_echo_action('propel', sprintf('load data from "%s"', $fixtures_dir));
    $data->loadData($fixtures_dir);
  }
}

function _call_phing($task, $task_name, $check_schema = true, $propelIni = 'propel.ini' , $propelDirectory = '' )
{
  $schemas = pakeFinder::type('file')->name('*schema.xml')->relative()->follow_link()->in('config');
  if ($check_schema && !$schemas)
  {
    throw new Exception('You must create a schema.yml or schema.xml file.');
  }

  // call phing targets
  pake_import('Phing', false);

//  if (false === strpos('propel-generator', get_include_path()))
//  {
//    set_include_path( PATH_THIRDPARTY . 'propel-generator/classes' . PATH_SEPARATOR. get_include_path());
//  }
//  set_include_path(sfConfig::get('sf_root_dir').PATH_SEPARATOR.get_include_path());

  if ( $propelDirectory == '' )
    $options = array(
      'project.dir'       => PATH_CORE . 'config',
      'build.properties'  => $propelIni,
      'propel.output.dir' => PATH_CORE ,
    );
  else
    $options = array(
      'project.dir'       => $propelDirectory . 'config',
      'build.properties'  => $propelIni,
      'propel.output.dir' => $propelDirectory ,
    );

  pakePhingTask::call_phing($task, array($task_name),
       PATH_THIRDPARTY . 'propel-generator/build.xml', $options);

  chdir( PATH_CORE );
}
