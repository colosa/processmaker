<?php


use ProcessMaker\Core\System;

class Upgrade
{
    private $addon = null;

    public function __construct($addon)
    {
        $this->addon = $addon;
    }

    public function install()
    {

        $filter = new InputFilter();
        $start = microtime(1);
        $filename = $this->addon->getDownloadFilename();
        $time = microtime(1);

        $archive = new Archive_Tar ($filename);
        $time = microtime(1);
        $extractDir = dirname($this->addon->getDownloadFilename()) . "/extract";
        $extractDir = $filter->xssFilterHard($extractDir);
        $backupDir = dirname($this->addon->getDownloadFilename()) . "/backup";
        $backupDir = $filter->xssFilterHard($backupDir);
        if (file_exists($extractDir)) {
            G::rm_dir($extractDir);
        }
        if (file_exists($backupDir)) {
            G::rm_dir($backupDir);
        }
        if (!is_dir($backupDir)) {
            mkdir($backupDir);
        }

        $time = microtime(1);
        echo "Extracting files...\n";
        $archive->extractModify($extractDir, 'processmaker');
        $checksumFile = file_get_contents("$extractDir/checksum.txt");
        $time = microtime(1);
        $checksums = array();
        foreach (explode("\n", $checksumFile) as $line) {
            $checksums[trim(substr($line, 33))] = substr($line, 0, 32);
        }

        $checksum = array();
        $changedFiles = array();
        $time = microtime(1);
        $files = $this->ls_dir($extractDir);

        echo "Updating ProcessMaker files...\n";
        $time = microtime(1);
        $checksumTime = 0;
        foreach ($checksums as $filename => $checksum) {
            if (is_dir("$extractDir/$filename")) {
                $filename = $filter->xssFilterHard($filename);
                print $filename;
                continue;
            }
            $installedFile = PATH_TRUNK . "/$filename";
            if (!file_exists($installedFile)) {
                $installedMD5 = "";
            } else {
                $time = microtime(1);
                $installedMD5 = G::encryptFileOld($installedFile);
                $checksumTime += microtime(1) - $time;
            }
            $archiveMD5 = $checksum;
            if (strcasecmp($archiveMD5, $installedMD5) != 0) {
                $changedFiles[] = $filename;
                if (!is_dir(dirname($backupDir.'/'.$filename))) {
                    mkdir(dirname($backupDir.'/'.$filename), 0777, true);
                }
                if (file_exists($installedFile) && is_file($installedFile)) {
                    copy($installedFile, $backupDir.'/'.$filename);
                }
                if (!is_dir(dirname($installedFile))) {
                    mkdir(dirname($installedFile), 0777, true);
                }
                if (!copy("$extractDir/$filename", $installedFile)) {
                    throw new Exception("Could not overwrite '$filename'");
                }
            }
        }

        printf("Updated %d files\n", count($changedFiles));
        printf("Clearing cache...\n");
        if (defined('PATH_C')) {
            G::rm_dir(PATH_C);
            mkdir(PATH_C, 0777, true);
        }
        $workspaces = System::listWorkspaces();
        $count = count($workspaces);
        $first = true;
        $num = 0;
        foreach ($workspaces as $index => $workspace) {
            try {
                $num += 1;
                printf("Upgrading workspaces ($num/$count): {$workspace->name}\n");
                $workspace->upgrade(false, SYS_SYS, false, 'en', ['updateXml' => $first, 'updateMafe' => $first]);
                $workspace->close();
                $first = false;
            } catch (Exception $e) {
                printf("Errors upgrading workspace {$workspace->name}: {$e->getMessage()}\n");
            }
        }
    }

    private function ls_dir($dir, $basename = null)
    {
        $files = array();
        if ($basename == null) {
            $basename = $dir;
        }
        foreach (glob("$dir/*") as $filename) {
            if (is_dir($filename)) {
                $files = array_merge($files, $this->ls_dir($filename, $basename));
            } else {
                $files[] = substr($filename, strlen($basename) + 1);
            }
        }
        return $files;
    }

}
