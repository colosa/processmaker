<?php

use ProcessMaker\Core\System;

class EnterpriseUtils
{
    public static function getInternetConnection()
    {
        $data = array();

        $criteria = new Criteria("workflow");

        $criteria->addSelectColumn(ConfigurationPeer::CFG_VALUE);
        $criteria->add(ConfigurationPeer::CFG_UID, "EE");
        $criteria->add(ConfigurationPeer::OBJ_UID, "enterpriseConfiguration");
        $rsCriteria = ConfigurationPeer::doSelectRS($criteria);

        if ($rsCriteria->next()) {
            $row = $rsCriteria->getRow();

            $data = unserialize($row[0]);
        }

        return ((isset($data["internetConnection"]))? intval($data["internetConnection"]) : 1);
    }

    public static function checkConnectivity($url)
    {
        try {
            if (extension_loaded('curl')) {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HEADER, true);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
                curl_setopt($ch, CURLOPT_AUTOREFERER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
                curl_setopt($ch, CURLOPT_TIMEOUT, 20);
                curl_setopt($ch, CURLOPT_VERBOSE, true);

                //Apply proxy settings
                $sysConf = System::getSystemConfiguration();
                if (isset($sysConf['proxy_host'])) {
                    if ($sysConf['proxy_host'] != '') {
                        curl_setopt($ch, CURLOPT_PROXY, $sysConf['proxy_host'] . ($sysConf['proxy_port'] != '' ? ':' . $sysConf['proxy_port'] : ''));
                        if ($sysConf['proxy_port'] != '') {
                            curl_setopt($ch, CURLOPT_PROXYPORT, $sysConf['proxy_port']);
                        }
                        if ($sysConf['proxy_user'] != '') {
                            curl_setopt($ch, CURLOPT_PROXYUSERPWD, $sysConf['proxy_user'] . ($sysConf['proxy_pass'] != '' ? ':' . $sysConf['proxy_pass'] : ''));
                        }
                        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));
                    }
                }

                $content = curl_exec($ch);
                $headers = curl_getinfo($ch);
                $content = substr($content, $headers['header_size']);

                if ($headers['http_code'] === 200) {
                    return $content;
                }
            } else {
                throw (new Exception('The "CURL" extension not loaded.'));
            }
        } catch (Exception $e) {
            //Log the error
        }

        return false;
    }

    public static function checkFolderPermissions($folderPath, $result)
    {
        $directorio = opendir($folderPath);

        if (is_writable ($folderPath)) {
            while (false !== ($archivo = readdir($directorio))  && $result == true) {
                if ($archivo != '.') {
                    if ($archivo != '..') {
                        if (is_dir("$folderPath/$archivo")) {
                            $result = self::checkFolderPermissions($folderPath."/".$archivo, $result);
                        } else {
                            if (!is_writable ($folderPath."/".$archivo)) {
                                $result = false;

                                return $result;
                            }
                        }
                    }
                }
            }
        } else {
            $result = false;

            return $result;
        }

        closedir($directorio);

        return $result;
    }

    public static function pmVersion($version)
    {
        if (preg_match("/^([\d\.]+).*$/", $version, $matches)) {
            $version = $matches[1];
        }

        return $version;
    }

    public static function getUrlServerName()
    {
        $s = (G::is_https() ? "s" : null);
        $p = strtolower($_SERVER["SERVER_PROTOCOL"]);

        $protocol = substr($p, 0, strpos($p, "/")) . $s;
        $port = ($_SERVER["SERVER_PORT"] == "80")? null : ":" . $_SERVER["SERVER_PORT"];

        return ($protocol . "://" . $_SERVER["SERVER_NAME"] . $port);
    }

    public static function getUrl()
    {
        return (self::getUrlServerName() . $_SERVER["REQUEST_URI"]);
    }

    public static function getUrlPartSetup()
    {
        $setup = "setup/main";

        if (substr(SYS_SKIN, 0, 2) == "ux" && SYS_SKIN != "uxs") {
            $setup = "setup/main_init";
        }

        return $setup;
    }

    public static function skinIsUx()
    {
        $sw = 0;

        if (substr(SYS_SKIN, 0, 2) == "ux" && SYS_SKIN != "uxs") {
            $sw = 1;
        }

        return $sw;
    }
}
