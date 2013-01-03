<?php
/**
 * cliWorkspaces.php
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
 * @package workflow-engine-bin-tasks
 */

CLI::taskName('info');
CLI::taskDescription(<<<EOT
Print information about the current system and any specified workspaces.

  If no workspace is specified, show information about all available workspaces
EOT
);
CLI::taskArg('workspace-name', true, true);
CLI::taskRun(run_info);

CLI::taskName('workspace-backup');
CLI::taskDescription(<<<EOT
  Backup the specified workspace to an archive.

  BACKUP-FILE is the backup filename. If it contains slashes, it will be
  treated as a path and filename, either absolute or relative. Otherwise, it
  will be treated as a filename inside the 'shared/backups' directory.
  If no BACKUP-FILE is specified, it will use the workspace name as the
  filename.

  A backup archive will contain all information about the specified workspace
  so that it can be restored later. The archive includes a database dump and
  all the workspace files.
EOT
);
CLI::taskArg('workspace', false);
CLI::taskArg('backup-file', true);
CLI::taskOpt("filesize", "Set the max size of the compressed splitted files, by default the max is 1000 Mb.", "s:","filesize=");
CLI::taskRun(run_workspace_backup);

CLI::taskName('workspace-restore');
CLI::taskDescription(<<<EOT
  Restore a workspace from a backup.

  BACKUP-FILE is the backup filename. If it contains slashes, it will be
  treated as a path and filename, either absolute or relative. Otherwise, it
  will be treated as a filename inside the 'shared/backups' directory.

  Specify the WORKSPACE to restore to a different workspace name. Otherwise,
  it will restore to the same workspace name as the original backup.
EOT
);
CLI::taskArg('backup-file', false);
CLI::taskArg('workspace', true);
CLI::taskOpt("overwrite", "If a workspace already exists, overwrite it.", "o", "overwrite");
CLI::taskOpt("info", "Only shows information about a backup archive.", "i");
CLI::taskOpt("multiple", "Restore from multiple compresed enumerated files.", "m");
CLI::taskOpt("workspace", "Select which workspace to restore if multiple workspaces are present in the archive.",
             "w:", "workspace=");
CLI::taskRun(run_workspace_restore);

CLI::taskName('cacheview-repair');
CLI::taskDescription(<<<EOT
  Create and populate the APP_CACHE_VIEW table

  Specify the workspaces whose cases cache should be repaired. If no workspace
  is specified, then the cases will be repaired on all available workspaces.

  In order to improve the performance, ProcessMaker includes a cache of cases
  in the table APP_CACHE_VIEW. This table must be in sync with the database
  to present the correct information in the cases inbox. This command will
  create the table and populate it with the right information. This only needs
  to be used after upgrading ProcessMaker or if the cases inbox is out of sync.
EOT
);
CLI::taskArg('workspace', true, true);
CLI::taskRun(run_cacheview_upgrade);

CLI::taskName('database-upgrade');
CLI::taskDescription(<<<EOT
  Upgrade or repair the database schema to match the latest version

  Specify the workspaces whose database schema should be upgraded or repaired.
  If no workspace is specified, then the database schema will be upgraded or
  repaired on all available workspaces.

  This command will read the system schema and attempt to modify the workspaces
  tables to match this new schema. Use this command to fix corrupted database
  schemas or after ProcessMaker has been upgraded, so the database schemas will
  changed to match the new ProcessMaker code.
EOT
);
CLI::taskArg('workspace', true, true);
CLI::taskRun(run_database_upgrade);

CLI::taskName('plugins-database-upgrade');
CLI::taskDescription(<<<EOT
  Upgrade or repair the database schema for plugins to match the latest version

  Specify the workspaces whose database schema should be upgraded or repaired
  for plugins. If no workspace is specified, then the database schema will be
  upgraded or repaired on all available workspaces.

  The same as database-upgrade but works with schemas provided by plugins.
  This is useful if there are installed plugins that include database schemas.
EOT
);
CLI::taskArg('workspace', true, true);
CLI::taskRun(run_plugins_database_upgrade);

CLI::taskName('workspace-upgrade');
CLI::taskDescription(<<<EOT
  Upgrade the workspace(s) specified.

  If no workspace is specified, the command will be run in all workspaces. More
  than one workspace can be specified.

  This command is a shortcut to execute all upgrade commands for workspaces.
  Upgrading a workspace will make it correspond to the current version of
  ProcessMaker.

  Use this command to upgrade workspaces individually, otherwise use the
  upgrade command to upgrade the entire system.
EOT
);
CLI::taskArg('workspace-name', true, true);
CLI::taskRun(run_workspace_upgrade);

CLI::taskName('translation-repair');
CLI::taskDescription(<<<EOT
  Upgrade or repair translations for the specified workspace(s).

  If no workspace is specified, the command will be run in all workspaces. More
  than one workspace can be specified.

  This command will go through each language installed in ProcessMaker and
  update this workspace translations to match the current version of
  ProcessMaker.
EOT
);
CLI::taskArg('workspace-name', true, true);
CLI::taskRun(run_translation_upgrade);

  /**
   * Function run_info
   * access public
   */
function run_info($args, $opts) {
  $workspaces = get_workspaces_from_args($args);
  workspaceTools::printSysInfo();
  foreach ($workspaces as $workspace) {
    echo "\n";
    $workspace->printMetadata(false);
  }
}

function run_workspace_upgrade($args, $opts) {
  $workspaces = get_workspaces_from_args($args);
  $first = true;
  foreach ($workspaces as $workspace) {
    try {
      $workspace->upgrade($first, false, $workspace->name);
      $first = false;
    } catch (Exception $e) {
      echo "Errors upgrading workspace " . CLI::info($workspace->name) . ": " . CLI::error($e->getMessage()) . "\n";
    }
  }
}

function run_translation_upgrade($args, $opts) {
  $workspaces = get_workspaces_from_args($args);
  $first = true;
  foreach ($workspaces as $workspace) {
    try {
      echo "Upgrading translation for " . pakeColor::colorize($workspace->name, "INFO") . "\n";
      $workspace->upgradeTranslation($first);
      $first = false;
    } catch (Exception $e) {
      echo "Errors upgrading translation of workspace " . CLI::info($workspace->name) . ": " . CLI::error($e->getMessage()) . "\n";
    }
  }
}

function run_cacheview_upgrade($args, $opts) {
  $workspaces = get_workspaces_from_args($args);
  foreach ($workspaces as $workspace) {
    try {
      echo "Upgrading cache view for " . pakeColor::colorize($workspace->name, "INFO") . "\n";
      $workspace->upgradeCacheView();
    } catch (Exception $e) {
      echo "Errors upgrading translation of workspace " . CLI::info($workspace->name) . ": " . CLI::error($e->getMessage()) . "\n";
    }
  }
}

function run_plugins_database_upgrade($args, $opts) {
  $workspaces = get_workspaces_from_args($args);
  foreach ($workspaces as $workspace) {
    try {
      CLI::logging("Upgrading plugins database for " . CLI::info($workspace->name) . "\n");
      $workspace->upgradePluginsDatabase();
    } catch (Exception $e) {
      CLI::logging("Errors upgrading plugins database: " . CLI::error($e->getMessage()));
    }
  }
}

function run_database_export($args, $opts) {
  if (count($args) < 2)
    throw new Exception ("Please provide a workspace name and a directory for export");
  $workspace = new workspaceTools($args[0]);
  $workspace->exportDatabase($args[1]);
}

function run_database_import($args, $opts) {
  throw new Exception("Not implemented");
}

function run_database_upgrade($args, $opts) {
  database_upgrade("upgrade", $args);
}

function run_database_check($args, $opts) {
  database_upgrade("check", $args);
}

function database_upgrade($command, $args) {
  $workspaces = get_workspaces_from_args($args);
  $checkOnly = (strcmp($command, "check") == 0);
  foreach ($workspaces as $workspace) {
    if ($checkOnly)
      print_r("Checking database in ".pakeColor::colorize($workspace->name, "INFO")."\n");
    else
      print_r("Upgrading database in ".pakeColor::colorize($workspace->name, "INFO")."\n");
    try {
      $changes = $workspace->upgradeDatabase($checkOnly);
      if ($changes != false) {
        if ($checkOnly) {
          echo "> ".pakeColor::colorize("Run upgrade", "INFO")."\n";
          echo "  Tables (add = " . count($changes['tablesToAdd']);
          echo ", alter = " . count($changes['tablesToAlter']) . ") ";
          echo "- Indexes (add = " . count($changes['tablesWithNewIndex'])."";
          echo ", alter = " . count($changes['tablesToAlterIndex']).")\n";
        } else {
          echo "-> Schema fixed\n";
        }
      } else {
        echo "> OK\n";
      }
    } catch (Exception $e) {
      echo "> Error: ".CLI::error($e->getMessage()) . "\n";
    }
  }
}

function delete_app_from_table($con, $tableName, $appUid, $col="APP_UID") {
  $stmt = $con->createStatement();
  $sql = "DELETE FROM " . $tableName . " WHERE " . $col . "='" . $appUid . "'";
  $rs = $stmt->executeQuery($sql, ResultSet::FETCHMODE_NUM);
}

function run_drafts_clean($args, $opts) {
  echo "Cleaning drafts\n";

  if (count($args) < 1)
    throw new Exception ("Please specify a workspace name");
  $workspace = $args[0];

  if (!file_exists(PATH_DB . $workspace . '/db.php')) {
    throw new Exception('Could not find workspace ' . $workspace);
  }

  $allDrafts = false;
  if (count($args) < 2) {
    echo "Cases older them this much days will be deleted (ENTER for all): ";
    $days = rtrim( fgets( STDIN ), "\n" );
    if ($days == "") {
      $allDrafts = true;
    }
  } else {
    $days = $args[1];
    if (strcmp($days, "all") == 0) {
      $allDrafts = true;
    }
  }

  if (!$allDrafts && (!is_numeric($days) || intval($days) <= 0)) {
    throw new Exception("Days value is not valid: " . $days);
  }

  if ($allDrafts)
    echo "Removing all drafts\n";
  else
    echo "Removing drafts older than " . $days . " days\n";

  /* Load the configuration from the workspace */
  require_once( PATH_DB . $workspace . '/db.php' );
  require_once( PATH_THIRDPARTY . 'propel/Propel.php');

  PROPEL::Init ( PATH_METHODS.'dbConnections/rootDbConnections.php' );
  $con = Propel::getConnection("root");

  $stmt = $con->createStatement();

  if (!$allDrafts)
    $dateSql = "AND DATE_SUB(CURDATE(),INTERVAL " . $days . " DAY) >= APP_CREATE_DATE";
  else
    $dateSql = "";
  /* Search for all the draft cases */
  $sql = "SELECT APP_UID FROM APPLICATION WHERE APP_STATUS='DRAFT'" . $dateSql;
  $appRows = $stmt->executeQuery($sql, ResultSet::FETCHMODE_ASSOC);

  /* Tables to remove the cases from */
  $tables = array(
      "APPLICATION",
      "APP_DELEGATION",
      "APP_CACHE_VIEW",
      "APP_THREAD",
      "APP_DOCUMENT",
      "APP_EVENT",
      "APP_HISTORY",
      "APP_MESSAGE"
  );

  echo "Found " . $appRows->getRecordCount() . " cases to remove";
  foreach ($appRows as $row) {
    echo ".";
    $appUid = $row['APP_UID'];
    foreach ($tables as $table) {
      delete_app_from_table($con, $table, $appUid);
    }
    delete_app_from_table($con, "CONTENT", $appUid, "CON_ID");
    if (file_exists(PATH_DB . $workspace . '/files/'. $appUid)) {
      echo "\nRemoving files from " . $appUid . "\n";
      G::rm_dir(PATH_DB . $workspace . '/files/'. $appUid);
    }
  }
  echo "\n";
}

function run_workspace_backup($args, $opts) {
  $workspaces = array();
  if (sizeof($args) > 2) {
    $filename = array_pop($args);
    foreach ($args as $arg) {
      $workspaces[] = new workspaceTools($arg);
    }
  } else if (sizeof($args) > 0) {
    $workspace = new workspaceTools($args[0]);
    $workspaces[] = $workspace;
    if (sizeof($args) == 2)
      $filename = $args[1];
    else
      $filename = "{$workspace->name}.tar";
  } else {
    throw new Exception("No workspace specified for backup");
  }
  foreach ($workspaces as $workspace)
    if (!$workspace->workspaceExists())
      throw new Exception("Workspace '{$workspace->name}' not found");
  //If this is a relative path, put the file in the backups directory
  if (strpos($filename, "/") === false && strpos($filename, '\\') === false){
    $filename = PATH_DATA . "backups/$filename";
  }
  CLI::logging("Backing up to $filename\n");
  
  $filesize = array_key_exists("filesize", $opts) ? $opts['filesize'] : -1;
  if($filesize >= 0)
  {
      if(!Bootstrap::isLinuxOs()){
            CLI::error("This is not a Linux enviroment, cannot use this filesize [-s] feature.\n");
            return;
      }
      $multipleBackup = new multipleFilesBackup ($filename,$filesize);//if filesize is 0 the default size will be took
      //using new method
      foreach ($workspaces as $workspace){
          $multipleBackup->addToBackup($workspace);
      }
      $multipleBackup->letsBackup();
  }
  else
  {
    //ansient method to backup into one large file
    $backup = workspaceTools::createBackup($filename);

    foreach ($workspaces as $workspace)
      $workspace->backup($backup);
  }
  CLI::logging("\n");
  workspaceTools::printSysInfo();
  foreach ($workspaces as $workspace) {
    CLI::logging("\n");
    $workspace->printMetadata(false);
  }

}

function run_workspace_restore($args, $opts) {
  $filename = $args[0];
  if (strpos($filename, "/") === false && strpos($filename, '\\') === false) {
    $filename = PATH_DATA . "backups/$filename";
    if (!file_exists($filename) && substr_compare($filename, ".tar", -4, 4, true) != 0)
      $filename .= ".tar";
  }
  $info = array_key_exists("info", $opts);
  if ($info) {
    workspaceTools::getBackupInfo($filename);
  } else {
    CLI::logging("Restoring from $filename\n");
    $workspace = array_key_exists("workspace", $opts) ? $opts['workspace'] : NULL;
    $overwrite = array_key_exists("overwrite", $opts);
    $multiple = array_key_exists("multiple", $opts);
    $dstWorkspace = $args[1];
    if(!empty($multiple)){
        if(!Bootstrap::isLinuxOs()){
            CLI::error("This is not a Linux enviroment, cannot use this multiple [-m] feature.\n");
            return;
        }
        multipleFilesBackup::letsRestore ($filename,$workspace,$dstWorkspace,$overwrite);
    }
    else{
        $anotherExtention = ".*"; //if there are files with and extra extention: e.g. <file>.tar.number
        $multiplefiles = glob($filename . $anotherExtention);// example: //shared/workflow_data/backups/myWorkspace.tar.*
        if(count($multiplefiles) > 0)
        {
            CLI::error("Processmaker found these files: .\n");
            foreach($multiplefiles as $index => $value){
                CLI::logging($value . "\n");
            }
            CLI::error("Please, you should use -m parameter to restore them.\n");
            return;
        }
        workspaceTools::restore($filename, $workspace, $dstWorkspace, $overwrite);
    }
  }
}

?>
