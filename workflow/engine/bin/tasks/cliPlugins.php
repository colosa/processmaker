<?php
/**
 * cliPlugins.php
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

CLI::taskName('plugins-translation-update');
CLI::taskDescription(<<<EOT
    Update plugin translations

    LANG is the language, such as 'fr' (French) or 'zh-CN' (mainland Chinese).
  If the language is not specified, then it is 'en' (English) by default.

EOT
);
CLI::taskArg('plugin', false);
CLI::taskArg('lang', false);
CLI::taskRun("run_update");

CLI::taskName('plugins-translation-create');
CLI::taskDescription(<<<EOT
    Create .po file for the plugin

    LANG is the language, such as 'fr' (French) or 'zh-CN' (mainland Chinese).
  If the language is not specified, then it is 'en' (English) by default.

EOT
);
CLI::taskArg('plugin', true);
CLI::taskArg('lang', true);
CLI::taskRun("run_create");


function run_create($command, $args)
{
    CLI::logging("Create .po file ...\n");

    $language = new Language();
    $language->createLanguagePlugin($command[0], $command[1]);
    CLI::logging("Create successful\n");

}

function run_update($command, $args)
{
    CLI::logging("Updating...\n");

    $language = new Language();
    $language->updateLanguagePlugin($command[0], $command[1]);
    CLI::logging("Update successful\n");

}
