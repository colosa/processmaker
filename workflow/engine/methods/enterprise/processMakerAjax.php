<?php

use ProcessMaker\Core\System;

ini_set("max_execution_time", 0);

if (!defined("PM_VERSION")) {
    if (file_exists(PATH_METHODS . "login/version-pmos.php")) {
        include (PATH_METHODS . "login/version-pmos.php");
    } else {
        define("PM_VERSION", "2.0.0");
    }
}

if (!defined("BUFSIZE")) {
    define("BUFSIZE", 16384);
}


function install($file)
{


    $result = array();
    $status = 1;

    try {
        //Extract
        $tar = new Archive_Tar($file);

        $swTar = $tar->extract(PATH_OUTTRUNK); //true on success, false on error. //directory for extract
        //$swTar = $tar->extract(PATH_PLUGINS);

        if (!$swTar) {
            throw (new Exception("Could not extract file."));
        }

        //Upgrade
        $option = array(
            "http" => array(
                "method" => "POST"
            )
        );

        // Proxy settings
        $sysConf = System::getSystemConfiguration();
        if (isset($sysConf['proxy_host'])) {
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
        }

        $context = stream_context_create($option);

        ///////
        $fileData = @fopen(EnterpriseUtils::getUrlServerName() . "/sys" . SYS_SYS . "/" . SYS_LANG . "/" . SYS_SKIN . "/enterprise/services/processMakerUpgrade", "rb", false, $context);

        if ($fileData === false) {
            throw (new Exception("Could not open services url."));
        }

        $resultAux = G::json_decode(stream_get_contents($fileData));

        if ($resultAux->status == "OK") {
            $result["status"] = $resultAux->status; //OK
            $result["message"] = $resultAux->message;
        } else {
            throw (new Exception($resultAux->message));
        }
    } catch (Exception $e) {
        $result["message"] = $e->getMessage();
        $status = 0;
    }

    if ($status == 0) {
        $result["status"] = "ERROR";
    }

    return $result;
}





$option = (isset($_POST["option"]))? $_POST["option"] : null;

switch ($option) {
    case "install":
        $uid         = $_POST["uid"];
        $version     = $_POST["version"];
        $versionName = $_POST["versionName"];
        $processMakerVersion = $_POST["processMakerVersion"];

        $response = array();
        $status = 1;

        try {
            if (EnterpriseUtils::getInternetConnection() == 0) {
                throw (new Exception("Enterprise Plugins Manager no connected to internet."));
            }

            ///////
            $versionName = EnterpriseUtils::pmVersion($versionName);
            $processMakerVersion = EnterpriseUtils::pmVersion($processMakerVersion);

            if (!version_compare($processMakerVersion . "", $versionName . "", "<")) {
                throw (new Exception("The system can't be upgraded to a previous version."));
            }

            ///////
            $licenseManager = &PmLicenseManager::getSingleton();
            $server = isset($licenseManager->server) ? $licenseManager->server : '';
            $workspace = (isset($licenseManager->workspace)) ? $licenseManager->workspace : 'pmLicenseSrv';

            $url = "http://$server/sys".$workspace."/en/green/services/rest";

            if (EnterpriseUtils::checkConnectivity($url) == false) {
                throw (new Exception("Server '$server' not available."));
            }

            ///////
            $boundary = "---------------------" . substr(G::encryptOld(rand(0, 32000)), 0, 10);
            $data = null;

            $data = $data . "--$boundary\n";
            $data = $data . "Content-Disposition: form-data; name=\"action\"\n\n" . "getPM" . "\n";
            $data = $data . "--$boundary\n";
            $data = $data . "Content-Disposition: form-data; name=\"OBJ_UID\"\n\n" . $uid . "\n";
            $data = $data . "--$boundary\n";
            $data = $data . "Content-Disposition: form-data; name=\"OBJ_VERSION\"\n\n" . $version . "\n";
            $data = $data . "--$boundary\n";

            $option = array(
                "http" => array(
                    "method" => "POST",
                    "header" => "Content-Type: multipart/form-data; boundary=" . $boundary,
                    "content" => $data
                )
            );

            // Proxy settings
            $sysConf = System::getSystemConfiguration();
            if (isset($sysConf['proxy_host'])) {
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
            }

            $context = stream_context_create($option);

            ///////
            $fileData = @fopen($url, "rb", false, $context);

            if ($fileData === false) {
                throw (new Exception("Could not open download url."));
            }

            //Try to get the download size and filename (ok if it fails)
            $meta = stream_get_meta_data($fileData);

            $fileName = null;
            $fileContentType = null;
            $fileLength = 0;

            if ($meta["wrapper_type"] == "http") {
                foreach ($meta["wrapper_data"] as $info) {
                    $info = explode(":", $info);
                    $infoVariable = (isset($info[0]))? trim($info[0]) : null;
                    $infoValue    = (isset($info[1]))? trim($info[1]) : null;

                    if (preg_match("/^.*Content-Type.*$/", $infoVariable)) {
                        $fileContentType = $infoValue;
                    }

                    if (strcasecmp($infoVariable, "Content-Length") == 0) {
                        $fileLength = intval($infoValue);
                    }

                    if (strcasecmp($infoVariable, "Content-Disposition") == 0) {
                        foreach (explode(";", $infoValue) as $params) {
                            $params = explode("=", $params);

                            if (count($params) <= 1) {
                                continue;
                            }

                            if (strcasecmp(trim($params[0]), "filename") == 0) {
                                $fileName = trim($params[1], "\" ");
                            }
                        }
                    }
                }
            }

            if (preg_match("/^.*json.*$/i", $fileContentType)) {
                $r = G::json_decode(stream_get_contents($fileData));

                if ($r->status == "ERROR") {
                    throw (new Exception($r->message));
                }
            }

            ///////
            $dir = PATH_DATA . "upgrade" . PATH_SEP . "processmaker";

            G::rm_dir($dir);
            G::mk_dir($dir);

            if (!file_exists($dir)) {
                throw (new Exception("Could not create destination directory."));
            }

            ///////
            $fileName = $dir . PATH_SEP . $fileName;

            $file = @fopen($fileName, "wb");

            if ($file === false) {
                throw (new Exception("Could not open destination file."));
            }

            while (!feof($fileData)) {
                $data = fread($fileData, BUFSIZE);

                //Just to be safe, check all error conditions
                if ($data === "" || $data === false) {
                    break;
                }

                if (fwrite($file, $data) === false) {
                    break;
                }
            }

            fclose($file);
            fclose($fileData);

            ///////
            $path = PATH_TRUNK;
            //$path = PATH_OUTTRUNK;

            if (EnterpriseUtils::checkFolderPermissions($path, true) == false) {
                $str = $path . " " . "directory, its sub-directories or file is not writable. Read the wiki for <a href=\"http://wiki.processmaker.com/index.php/Upgrading_ProcessMaker\" onclick=\"window.open(this.href, \'_blank\'); return (false);\">Upgrading ProcessMaker</a>.<br /> The file is downloaded in " . $fileName . "<br />";
                throw (new Exception($str));
            }

            ///////
            $result = install($fileName);

            if ($result["status"] == "OK") {
                $response["status"] = $result["status"]; //OK
                $response["message"] = $result["message"];
                G::auditLog("InstallPlugin", "Plugin Name: ".$file);
            } else {
                throw (new Exception($result["message"]));
            }
        } catch (Exception $e) {
            $response["message"] = $e->getMessage();
            $status = 0;
        }

        if ($status == 0) {
            $response["status"] = "ERROR";
        }
        echo G::json_encode($response);
        break;
    case "list":
        $status = 1;
        $response = new stdclass();
        $response->status = 'OK';
        try {
            if (EnterpriseUtils::getInternetConnection() == 0) {
                throw (new Exception("Enterprise Plugins Manager no connected to internet."));
            }

            ///////
            $licenseManager = &PmLicenseManager::getSingleton();
            $server = (isset($licenseManager->server)) ? $licenseManager->server : '';
            $workspace = (isset($licenseManager->workspace)) ? $licenseManager->workspace : 'pmLicenseSrv';

            $url = "http://$server/sys".$workspace."/en/green/services/rest";

            if (EnterpriseUtils::checkConnectivity($url) == false) {
                throw (new Exception("Server '$server' not available."));
            }

            ///////
            $option = array(
                "http" => array(
                    "method" => "POST",
                    "header" => "Content-type: application/x-www-form-urlencoded\r\n",
                    "content" => http_build_query(
                        array(
                            "action" => "getPMList"
                        )
                    )
                )
            );

            // Proxy settings
            $sysConf = System::getSystemConfiguration();
            if (isset($sysConf['proxy_host'])) {
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
            }

            $context = stream_context_create($option);

            $results = file_get_contents($url, false, $context);
            $results = G::json_decode($results);
            $results = isset($results[0]) ? $results[0] :array();

            $pmVersion = EnterpriseUtils::pmVersion(PM_VERSION);
            $versions = array();

            foreach ($results as $key => $result) {
                $version = EnterpriseUtils::pmVersion($result->OBJ_VERSION_NAME);

                if (version_compare($pmVersion . "", $version . "", "<")) {
                    $versions[] = $result;
                }
            }

            if (isset($results[0])) {
                $results[0]->OBJ_VERSION_NAME .= " (Stable)";
            }

            $response->status = "OK";
            $response->results = $versions;
        } catch (Exception $e) {
            $response->message = $e->getMessage();
            $status = 0;
        }

        if ($status == 0) {
            $response->status = "ERROR";
        }

        echo G::json_encode($response);
        break;
}

