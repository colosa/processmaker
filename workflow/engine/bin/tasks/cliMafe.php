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

CLI::taskName('mafe-translation');
CLI::taskDescription(<<<EOT
    Creates labels of mafe for the database

    If no workspace is specified, then the translation labels will be created in all available workspaces.
EOT
);

CLI::taskArg('workspace', true, true);
CLI::taskOpt("lang", "Specify the language to create the translation labels. If not specified then the language will be 'en' (English) by default.
        Ex: -lfr (French)
        Ex: --lang=zh-CN (Mainland Chinese)", "lLANG","lang=LANG");
CLI::taskRun('run_create_translation');

function run_create_translation($args, $opts)
{

    $filter = new InputFilter();
    $opts = $filter->xssFilterHard($opts);
    $args = $filter->xssFilterHard($args);
    $rootDir = realpath(__DIR__."/../../../../");
    $app = new Maveriks\WebApplication();
    $app->setRootDir($rootDir);
    $loadConstants = false;

    $workspaces = get_workspaces_from_args($args);
    $lang = array_key_exists("lang", $opts) ? $opts['lang'] : 'en';

    $translation = new Translation();
    CLI::logging("Updating labels Mafe ...\n");
    foreach ($workspaces as $workspace) {
        try {
            G::outRes( "Updating labels for workspace " . pakeColor::colorize($workspace->name, "INFO") . "\n" );
            $translation->generateTransaltionMafe($lang);
        } catch (Exception $e) {
            G::outRes( "Errors upgrading labels for workspace " . CLI::info($workspace->name) . ": " . CLI::error(G::getErrorMessage($e)) . "\n" );
        }
    }

    CLI::logging("Create successful\n");

}
