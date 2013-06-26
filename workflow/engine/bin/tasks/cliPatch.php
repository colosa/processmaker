<?php
CLI::taskName("patch-install");

CLI::taskDescription(<<<EOT
    Install patch to system

    This command is executed when you want to update certain files, which have improvements or solution to bugs.
EOT
);

CLI::taskRun(runPatchInstall);

function runPatchInstall($command, $args)
{
    CLI::logging("PATCH", PATH_DATA . "log" . PATH_SEP . "upgrades.log");
    CLI::logging("Install patch to system\n");

    $arrayFile = $command;

    if (count($arrayFile) > 0) {
        //Install patch
        foreach ($arrayFile as $value) {
            $f = $value;

            $result = workspaceTools::patchInstall($f);

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

        CLI::logging("PATCH done\n");
    } else {
        CLI::logging("Not exist patchs to install in the command\n");
    }
}

