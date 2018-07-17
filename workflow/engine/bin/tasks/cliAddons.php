<?php
/*
//Support ProcessMaker 1.8 which doesn't have the CLI class.
define("CLI2", class_exists("CLI"));

if (CLI2) {
  CLI::taskName("addon-install");
  CLI::taskDescription(<<<EOT
    Download and install an addon
EOT
  );
  CLI::taskRun(run_addon_core_install);
} else {
  pake_desc("install addon");
  pake_task("addon-install");
}
*/
/*----------------------------------********---------------------------------*/
//function run_addon_core_install($args, $opts) {
function run_addon_core_install($args)
{
    try {
        if (!extension_loaded("mysql")) {
            if (strtoupper(substr(PHP_OS, 0, 3)) === "WIN") {
                dl("mysql.dll");
            } else {
                dl("mysql.so");
            }
        }
        ///////
        /*
        if (!CLI2) {
          $args = $opts;
        }
        */
        $workspace = $args[0];
        $storeId = $args[1];
        $addonName = $args[2];

        if (!defined("SYS_SYS")) {
            define("SYS_SYS", $workspace);
        }
        if (!defined("PATH_DATA_SITE")) {
            define("PATH_DATA_SITE", PATH_DATA . "sites/" . SYS_SYS . "/");
        }
        if (!defined("DB_ADAPTER")) {
            define("DB_ADAPTER", $args[3]);
        }

        ///////
        $ws = new WorkspaceTools($workspace);
        $ws->initPropel(false);

        require_once PATH_CORE . 'methods' . PATH_SEP . 'enterprise' . PATH_SEP . 'enterprise.php';
        require_once PATH_CORE . 'classes' . PATH_SEP . 'model' . PATH_SEP . 'AddonsManagerPeer.php';

        $addon = AddonsManagerPeer::retrieveByPK($addonName, $storeId);
        if ($addon == null) {
            throw new Exception("Id $addonName not found in store $storeId");
        }
        //echo "Downloading...\n";
        $download = $addon->download();
        //echo "Installing...\n";
        $addon->install();

        if ($addon->isCore()) {
            $ws = new WorkspaceTools($workspace);
            $ws->initPropel(false);
            $addon->setState("install-finish");
        } else {
            $addon->setState();
        }
    } catch (Exception $e) {
        $addon->setState("error");
        //fwrite(STDERR, "\n[ERROR: {$e->getMessage()}]\n");
        //fwrite(STDOUT, "\n[ERROR: {$e->getMessage()}]\n");
    }
    //echo "** Installation finished\n";
}
/*----------------------------------********---------------------------------*/
