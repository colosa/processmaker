<?php
/**
 * cli.php
 * @package workflow-engine-bin
 *
 * ProcessMaker Open Source Edition
 * Copyright (C) 2011 Colosa Inc.
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
 * @author Alexandre Rosenfeld <alexandre@colosa.com>
 */

  /* Windows supports both / and \ as path separators, so use the Unix separator
   * for maximum compatibility.
   */
  define('PATH_SEP', '/');

  define('PATH_HOME',     WORKFLOW_PATH );
  define('PATH_TRUNK',    PROCESSMAKER_PATH  );
  define('PATH_OUTTRUNK', realpath(PROCESSMAKER_PATH.'/..') );

/* Most definitions (including the G class) is done in paths.php
 * This mostly simulates a sysGeneric.php call.
 */
if (file_exists(PATH_HOME . "engine" . PATH_SEP . "config" . PATH_SEP . "paths_installed.php")) {
    require_once(PATH_HOME . "engine" . PATH_SEP . "config" . PATH_SEP . "paths_installed.php");
}

require_once (PATH_HOME . "engine" . PATH_SEP . "config" . PATH_SEP . "paths.php");

require_once (PATH_THIRDPARTY . "pake" . PATH_SEP . "pakeFunction.php");
require_once (PATH_THIRDPARTY . "pake" . PATH_SEP . "pakeGetopt.class.php");
require_once (PATH_CORE . "config" . PATH_SEP . "environments.php");
require_once (PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "class.system.php");

require_once (PATH_GULLIVER . "class.bootstrap.php");

spl_autoload_register(array("Bootstrap", "autoloadClass"));

Bootstrap::registerSystemClasses();

$config = System::getSystemConfiguration(PATH_HOME . "engine" . PATH_SEP . "config" . PATH_SEP . "env.ini");

  $e_all  = defined('E_DEPRECATED') ? E_ALL & ~E_DEPRECATED : E_ALL;
  $e_all  = defined('E_STRICT')     ? E_ALL & ~E_STRICT     : $e_all;
  $e_all  = $e_all & E_WARNING; // show warning
  $e_all  = $e_all & ~E_NOTICE; // don't notices

  // Do not change any of these settings directly, use env.ini instead
  ini_set('display_errors', $config['debug']);
  ini_set('error_reporting', $e_all);
  ini_set('short_open_tag', 'On');
  ini_set('default_charset', "UTF-8");
  ini_set('memory_limit', $config['memory_limit']);
  ini_set('soap.wsdl_cache_enabled', $config['wsdl_cache']);
  ini_set('date.timezone', $config['time_zone']);

  define ('DEBUG_SQL_LOG', $config['debug_sql']);
  define ('DEBUG_TIME_LOG', $config['debug_time']);
  define ('DEBUG_CALENDAR_LOG', $config['debug_calendar']);
  define ('MEMCACHED_ENABLED',  $config['memcached']);
  define ('MEMCACHED_SERVER',   $config['memcached_server']);
  define ('TIME_ZONE', $config['time_zone']);

  G::LoadClass("cli");

  // trap -V before pake
  if (in_array('-v', $argv) || in_array('-V', $argv) || in_array('--version', $argv))
  {
    printf("ProcessMaker version %s\n", pakeColor::colorize(trim(file_get_contents( PATH_GULLIVER . 'VERSION')), 'INFO'));
    exit(0);
  }

  // register tasks
  //TODO: include plugins
  $directories = array(PATH_HOME . 'engine/bin/tasks');
  $pluginsDirectories = glob(PATH_PLUGINS . "*");
  foreach ($pluginsDirectories as $dir) {
    if (!is_dir($dir))
      continue;
    if (is_dir("$dir/bin/tasks"))
      $directories[] = "$dir/bin/tasks";
  }

  foreach ($directories as $dir) {
    foreach (glob("$dir/*.php") as $filename) {
      include_once($filename);
    }
  }

  CLI::run();

  exit(0);

