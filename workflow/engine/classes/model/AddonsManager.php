<?php

use ProcessMaker\Core\System;
use ProcessMaker\Plugins\PluginRegistry;

require_once 'classes/model/om/BaseAddonsManager.php';


if (!defined("BUFSIZE")) {
    define("BUFSIZE", 16384);
}

/**
 * Skeleton subclass for representing a row from the 'UPGRADE_MANAGER' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    classes.model
 */
class AddonsManager extends BaseAddonsManager
{
    /**
     * Returns the download filename
     *
     * @return string download filename
     */

    public function getDownloadFilename()
    {
        $filename = $this->getAddonDownloadFilename();
        if (!isset($filename) || empty($filename)) {
            $filename = "download.tar";
        }
        $dir = $this->getDownloadDirectory();
        return "$dir/$filename";
    }

    public function getDownloadDirectory()
    {
        $dir = PATH_DATA . "upgrade/{$this->getStoreId()}_{$this->getAddonName()}";
        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }
        return ($dir);
    }

    /**
     * Check to see if the download file exists and has the right data.
     *
     * @return mixed true if exists and md5 is good, false otherwise. Returns null
     *               if file exists but md5 for the download is not available.
     */
    public function checkDownload()
    {
        $filename = $this->getDownloadFilename();
        if (!file_exists($filename)) {
            return false;
        }
        $download_md5 = $this->getAddonDownloadMd5();
        if ($download_md5 == null) {
            return null;
        }
        return (strcasecmp(G::encryptFileOld($filename), $download_md5) == 0);
    }

    /**
     * Returns if this addon is of type 'plugin'
     *
     * @return bool true if is of type 'plugin', false otherwise
     */
    public function isPlugin()
    {
        return ($this->getAddonType() == 'plugin');
    }

    /**
     * Returns if this addon is of type 'core'
     *
     * @return bool true if is of type 'core', false otherwise
     */
    public function isCore()
    {
        return ($this->getAddonType() == 'core');
    }

    /**
     * Returns if this addon is installed or not-
     *
     * @return bool true if installed, false otherwise
     */
    public function isInstalled()
    {
        if ($this->isCore()) {
            return ($this->getAddonVersion() == $this->getInstalledVersion());
        } elseif ($this->isPlugin()) {
            return (file_exists(PATH_PLUGINS . "{$this->getAddonName()}.php"));
        } else {
            throw new Exception("Addon type '{$this->getAddonType()}' unsupported");
        }
    }

    /**
     * Returns if this addon is enabled or not.
     *
     * @return bool true if enabled, false otherwise
     */
    public function isEnabled()
    {
        if ($this->isCore()) {
            return $this->isInstalled();
        } elseif ($this->isPlugin()) {
            if (!$this->isInstalled()) {
                return false;
            }
            $oPluginRegistry = PluginRegistry::loadSingleton();
            return $oPluginRegistry->isEnable($this->getAddonName());
        } else {
            throw new Exception("Addon type '{$this->getAddonType()}' unsupported");
        }
    }

    public function setEnabled($enable = true)
    {
        if (!$this->isInstalled() || !$this->isPlugin()) {
            return false;
        }
        if ($this->getAddonName() == "enterprise") {
            return false;
        }

        $oPluginRegistry = PluginRegistry::loadSingleton();


        $filter = new InputFilter();
        $requiredPath = PATH_PLUGINS . $this->getAddonName() . ".php";
        $requiredPath = $filter->validateInput($requiredPath, 'path');
        require_once ($requiredPath);

        if ($enable) {
            $oPluginRegistry->enablePlugin($this->getAddonName());
            $oPluginRegistry->setupPlugins(); //get and setup enabled plugins
        } else {
            $oPluginRegistry->disablePlugin($this->getAddonName());
        }
        $oPluginRegistry->savePlugin($this->getAddonName());
        return true;
    }

    /**
     * Returns the currently installed version of this addon.
     *
     * @return string the installed version or an empty string otherwise.
     */
    public function getInstalledVersion()
    {
        if ($this->isCore()) {
            return (EnterpriseUtils::pmVersion(System::getVersion()));
        } else {
            if ($this->isPlugin()) {
                if (!$this->isInstalled()) {
                    return (null);
                }

                $oPluginRegistry = PluginRegistry::loadSingleton();
                $details = $oPluginRegistry->getPluginDetails($this->getAddonName() . ".php");
                $v = (!($details == null))? $details->getVersion() : null;

                if ($v != "") {
                    return ($v);
                }

                if (file_exists(PATH_PLUGINS . $this->getAddonName() . PATH_SEP . "VERSION")) {
                    return (trim(file_get_contents(PATH_PLUGINS . $this->getAddonName() . PATH_SEP . "VERSION")));
                }
            } else {
                if ($this->getAddonType() == "core") {
                    throw new Exception("Addon type \"" . $this->getAddonType() . "\" unsupported");
                }
            }
        }
    }

    public function refresh()
    {
        /* Update our information from the db */
        $rs = $this->getPeer()->doSelectRS($this->buildPkeyCriteria());
        $rs->first();
        $this->hydrate($rs);
    }

    /**
     * Download this addon from the download url.
     *
     * @return bool true on success, false otherwise.
     */
    public function download()
    {
        $this->setState("download");

        ///////
        $aux = explode("?", $this->getAddonDownloadUrl());

        $url = $aux[0];
        $var = explode("&", $aux[1]);

        ///////
        $boundary = "---------------------" . substr(G::encryptOld(rand(0, 32000)), 0, 10);
        $data = null;

        for ($i = 0; $i <= count($var) - 1; $i++) {
            $aux = explode("=", $var[$i]);

            $data = $data . "--$boundary\n";
            $data = $data . "Content-Disposition: form-data; name=\"" . $aux[0] . "\"\n\n" . $aux[1] . "\n";
        }

        if (count($var) > 0) {
            $data = $data . "--$boundary\n";
        }

        ///////
        $licenseManager = &PmLicenseManager::getSingleton();
        $activeLicense = $licenseManager->getActiveLicense();

        $data = $data . "Content-Disposition: form-data; name=\"licenseFile\"; filename=\"" . $licenseManager->file . "\"\n";
        $data = $data . "Content-Type: text/plain\n";
        //$data = $data . "Content-Type: image/jpeg\n";
        $data = $data . "Content-Transfer-Encoding: binary\n\n";
        $data = $data . file_get_contents($activeLicense["LICENSE_PATH"]) . "\n";
        $data = $data . "--$boundary\n";

        ///////
        $option = array(
            "http" => array(
            "method" => "POST",
            //"method" => "post",
            "header" => "Content-Type: multipart/form-data; boundary=" . $boundary,
            "content" => $data
            )
        );

        // Proxy settings
        $sysConf = System::getSystemConfiguration();
        if ($sysConf['proxy_host'] != '') {
            if (!is_array($option['http'])) {
                $option['http'] = array();
            }
            $option['http']['request_fulluri'] = true;
            $option['http']['proxy'] = 'tcp://' . $sysConf['proxy_host'] . ($sysConf['proxy_port'] != '' ? ':' . $sysConf['proxy_port'] : '');
            if ($sysConf['proxy_user'] != '') {
                if (!isset($option['http']['header'])) {
                    $option['http']['header'] = '';
                }
                $option['http']['header'] .= 'Proxy-Authorization: Basic ' . base64_encode($sysConf['proxy_user'] . ($sysConf['proxy_pass'] != '' ? ':' . $sysConf['proxy_pass'] : ''));
            }
        }

        $context = stream_context_create($option);

        ///////
        $handle = fopen($url, "rb", false, $context);

        if ($handle === false) {
            throw new Exception("Could not open download url.");
        }

        $this->setAddonDownloadFilename(null);

        //Try to get the download size and filename (ok if it fails)
        $meta = stream_get_meta_data($handle);
        $totalSize = 0;

        if ($meta["wrapper_type"] == "http") {
            foreach ($meta["wrapper_data"] as $info) {
                $info = explode(":", $info);
                if (strcasecmp(trim($info[0]), "Content-Length") == 0) {
                    $totalSize = intval(trim($info[1]));
                }
                if (strcasecmp(trim($info[0]), "Content-Disposition") == 0) {
                    foreach (explode(";", $info[1]) as $params) {
                        $params = explode("=", $params);
                        if (count($params) <= 1) {
                            continue;
                        }
                        if (strcasecmp(trim($params[0]), "filename") == 0) {
                            $this->setAddonDownloadFilename(trim($params[1], "\" "));
                        }
                    }
                }
            }
        }

        //Save the filename
        $this->save();

        $units = array(" B", " KB", " MB", " GB", " TB");
        //if ($totalSize) {
        //  $bytes = $totalSize;
        //  for ($i = 0; $bytes >= 1024 && $i < 4; $i++) $bytes /= 1024;
        //  printf("Download size: %.2f%s\n", round($bytes, 2), $units[$i]);
        //}

        $downloadFile = $this->getDownloadFilename();
        $file = @fopen($downloadFile, "wb");

        if ($file === false) {
            throw new Exception("Could not open destination file.");
        }

        $start = microtime(true);
        $rate = null;
        $position = null;
        $elapsed = null;

        while (!feof($handle)) {
            $this->refresh();
            /* Check if download was cancelled from the ui */
            if ($this->getAddonState() == "cancel" || $this->getAddonState() == "") {
                $this->setState();
                break;
            }
            /* Update the database information to show we are still alive */
            $this->setState("download");
            $data = fread($handle, BUFSIZE);
            //TODO: We should use these values for something
            $elapsed = microtime(true) - $start;
            $position = ftell($handle);
            $rate = $position / $elapsed;
            if ($totalSize) {
                $progress = 100 * ($position / $totalSize);
                $this->setAddonDownloadProgress($progress);
                $this->save();
            }
            /* Just to be safe, check all error conditions */
            if ($data === "" or $data === false) {
                break;
            }
            if (fwrite($file, $data) === false) {
                break;
            }
        }
        fclose($handle);
        fclose($file);

        if ($elapsed > 60) {
            $time = sprintf("%.0f minutes and %.0f seconds", round($elapsed / 60), round($elapsed) % 60);
        } else {
            $time = sprintf("%.0f seconds", round($elapsed));
        }

        for ($i = 0; $position >= 1024 && $i < 4; $i++) {
            $position /= 1024;
        }
        for ($j = 0; $rate >= 1024 && $j < 4; $j++) {
            $rate /= 1024;
        }
        //printf("Downloaded %.2f%s in %s (rate: %.2f%s/s)\n", $position, $units[$i], $time, $rate, $units[$j]);

        return ($this->checkDownload());
    }

    /**
     * Install this addon from the downloaded file.
     */
    public function install()
    {
        $this->setState("install");

        $filename = $this->getDownloadFilename();
        //if ($this->checkDownload() === false) {
        //  throw new Exception("Download file is invalid");
        //}

        if ($this->isPlugin()) {
            if ($this->getAddonId() == "enterprise") {
                $_SESSION["__ENTERPRISE_INSTALL__"] = 1;
            }

            $oPluginRegistry = PluginRegistry::loadSingleton();
            $oPluginRegistry->installPluginArchive($filename, $this->getAddonName());

            $this->setState();
        } else {
            if ($this->getAddonType() == "core") {
                $upgrade = new Upgrade($this);

                $upgrade->install();
            } else {
                throw new Exception("Addon type {$this->getAddonType()} not supported.");
            }
        }
    }

    public function uninstall()
    {
        if ($this->isPlugin()) {
            if (!$this->isInstalled()) {
                return false;
            }

            $oPluginRegistry = PluginRegistry::loadSingleton();
            $oPluginRegistry->uninstallPlugin($this->getAddonName());

            return true;
        }
    }

    public function getInstallLog()
    {
        $logFile = $this->getDownloadDirectory() . "/download.log";
        $contents = false;
        if (file_exists($logFile)) {
            $contents = @file_get_contents($logFile);
        }
        if ($contents === false) {
            return null;
        }
        return $contents;
    }

    public function setState($state = "")
    {
        $this->setAddonState($state);
        $this->setAddonStateChanged('now');
        $this->save();
    }

    public function checkState()
    {
        if ($this->getAddonState() == 'error') {
            $this->setState();
            return false;
        }
        if ($this->getAddonState() == '' || $this->getAddonState() == 'install-finish') {
            return true;
        }
        $elapsed = time() - strtotime($this->getAddonStateChanged());
        $timeout = 3;
        if ($this->isCore()) {
            $timeout = 10;
        }
        if ($elapsed > $timeout * 60) {
            $this->setState();
            return false;
        }
        return true;
    }

    /**
     *  Exists in Addons Manager Table
     *
     * @param string $addonId
     * @param string $storeId
     * @return type
     * @throws type
     */
    public function exists($addonId, $storeId)
    {
        $oAddManager = AddonsManagerPeer::retrieveByPK($addonId, $storeId);

        return (!is_null($oAddManager));
    }

    /**
     *  Update Addons Manager Table
     *
     * @param type $data
     * @return type
     * @throws type
     */
    public function update($data)
    {
        $con = Propel::getConnection( AddonsManagerPeer::DATABASE_NAME );
        try {
            $con->begin();
            $this->setNew( false );
            $this->fromArray( $data, BasePeer::TYPE_FIELDNAME );
            if ($this->validate()) {
                $result = $this->save();
                $con->commit();
                return $result;
            } else {
                $con->rollback();
                throw (new Exception( "Failed Validation in class " . get_class( $this ) . "." ));
            }
        } catch (Exception $e) {
            $con->rollback();
            throw ($e);
        }
    }
}
