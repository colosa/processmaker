<?php
CLI::taskName("browser-cache-files-upgrade");

CLI::taskDescription(<<<EOT
    Safe upgrade for files cached by the browser

    This command should be run after any upgrade/modification of files cached by the browser.
EOT
);

CLI::taskRun(runBrowserCacheFiles);

function runBrowserCacheFiles($command, $args)
{
    CLI::logging("Safe upgrade for files cached by the browser\n");

    G::browserCacheFilesSetUid();

    CLI::logging("Upgrade successful\n");
}

