<?php
CLI::taskName("hotfix-install");

CLI::taskDescription(<<<EOT
    Install hotfix to system

    This command is executed when you want to update certain files, which have improvements or bugs solutions.
EOT
);

CLI::taskRun(runHotfixInstall);

function runHotfixInstall($command, $args)
{
    CLI::logging("HOTFIX", PATH_DATA . "log" . PATH_SEP . "upgrades.log");
    CLI::logging("Install hotfix to system\n");

    $arrayFile = $command;

    if (count($arrayFile) > 0) {
        //Install hotfix
        foreach ($arrayFile as $value) {
            $f = $value;

            $result = workspaceTools::hotfixInstall($f);

            CLI::logging($result["message"] . "\n");
        }

        //Clear server's cache
        CLI::logging("\nClearing cache...\n");

        if (defined("PATH_C")) {
            G::rm_dir(PATH_C);
            G::mk_dir(PATH_C, 0777);
        }

        //Safe upgrade for JavaScript files
        CLI::logging("\nSafe upgrade for files cached by the browser\n\n");

        G::browserCacheFilesSetUid();

        CLI::logging("HOTFIX done\n");
    } else {
        CLI::logging("Please specify the hotfix to install\n");
    }
}

