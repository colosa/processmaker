<?php
/**
 * cliUpgrade.php
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

CLI::taskName('build-js');
CLI::taskDescription(<<<EOT
    Generate Javascript Files

    This command should be run after any modification of javascript files in
    folder gulliver/js/*.
EOT
);
//CLI::taskOpt("minify", "If the option is enabled, performs the build only with minified files", "min", "buildmin");
CLI::taskRun(minify_javascript);

function minify_javascript($command, $args)
{
    CLI::logging("BUILD-JS", PROCESSMAKER_PATH . "upgrade.log");
    CLI::logging("Checking if rake is installed...\n");
    $rakeFile = PROCESSMAKER_PATH . "workflow/engine/bin/tasks/Rakefile";
    system('rake -f ' . $rakeFile);
}

