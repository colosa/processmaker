<?php

require_once(__DIR__ . '/../../../bootstrap/autoload.php');

$app = new Maveriks\WebApplication();
$app->setRootDir(PROCESSMAKER_PATH);
$app->loadEnvironment();

  // trap -V before pake
  if (in_array('-v', $argv) || in_array('-V', $argv) || in_array('--version', $argv)) {
      printf("ProcessMaker version %s\n", pakeColor::colorize(trim(file_get_contents(PATH_GULLIVER . 'VERSION')), 'INFO'));
      exit(0);
  }

  // register tasks
  //TODO: include plugins
  $directories = array(PATH_HOME . 'engine/bin/tasks');
  $pluginsDirectories = glob(PATH_PLUGINS . "*");
  foreach ($pluginsDirectories as $dir) {
      if (!is_dir($dir)) {
          continue;
      }
      if (is_dir("$dir/bin/tasks")) {
          $directories[] = "$dir/bin/tasks";
      }
  }

  foreach ($directories as $dir) {
      foreach (glob("$dir/*.php") as $filename) {
          include_once($filename);
      }
  }

  CLI::run();

  exit(0);
