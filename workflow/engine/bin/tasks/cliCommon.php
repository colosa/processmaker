<?php

/* Get the size of the terminal (only works on Linux, on Windows it's always 80) */

use ProcessMaker\Core\System;

preg_match_all("/rows.([0-9]+);.columns.([0-9]+);/", strtolower(exec('stty -a |grep columns')), $output);
if (sizeof($output) == 3 && isset($output[2]) && isset($output[2][0])) {
    define("COLUMNS", $output[2][0]);
} else {
    define("COLUMNS", 80);
}

/**
 * Returns workspace objects from an array of workspace names.
 *
 * @param  array $args an array of workspace names
 * @param  bool $includeAll if true and no workspace is specified in args,
 *                          returns all available workspaces
 * @return array of workspace objects
 */
function get_workspaces_from_args($args, $includeAll = true)
{
    return \ProcessMaker\Util\System::getWorkspacesFromArgs($args, $includeAll);
}

?>
