<?php
/**
 * cliMafe.php
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
 * @package workflow-engine-bin-tasks
 */


use ProcessMaker\Util\System;

CLI::taskName('flush-cache');
CLI::taskDescription(<<<EOT
    Flush cache of all workspaces of a given workspace

    If no workspace is specified, then the cache will be flushed in all available workspaces.
EOT
);

CLI::taskArg('workspace', true, true);
CLI::taskRun('run_flush_cache');

/**
 * Flush the cache files for the specified workspace.
 * If no workspace is specified, then the cache will be flushed in all available 
 * workspaces.
 * 
 * @param array $args
 * @param array $opts
 */
function run_flush_cache($args, $opts)
{
    if (!defined("PATH_C")) {
        die("ERROR: seems processmaker is not properly installed (System constants are missing)." . PHP_EOL);
    }
    $workspaces = get_workspaces_from_args($args);
    if (count($args) === 1) {
        flush_cache($workspaces[0]);
    } else {
        foreach ($workspaces as $workspace) {
            passthru("./processmaker flush-cache " . $workspace->name);
        }
    }
}

/**
 * Flush the cache files for the specified workspace.
 * 
 * @param object $workspace
 */
function flush_cache($workspace)
{
    try {
        CLI::logging("Flush " . pakeColor::colorize("system", "INFO") . " cache ... ");
        echo PHP_EOL;
        echo " Update singleton in workspace " . $workspace->name . " ... ";
        echo PHP_EOL;
        echo " Flush workspace " . pakeColor::colorize($workspace->name, "INFO") . " cache ... " . PHP_EOL;
        System::flushCache($workspace);
        echo "DONE" . PHP_EOL;
    } catch (Exception $e) {
        echo $e->getMessage() . PHP_EOL;
    }
}
