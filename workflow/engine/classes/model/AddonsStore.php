<?php

use ProcessMaker\Core\System;
use ProcessMaker\Plugins\PluginRegistry;

require_once 'classes/model/om/BaseAddonsStore.php';

define("STORE_VERSION", 1);
class AddonsStore extends BaseAddonsStore
{
    /**
     * Add a store to the database
     *
     * @param string $storeId 32-character long store id
     * @param string $storeLocation URL to obtain the store info
     * @param string $storeType type of store (only "license" supported now)
     * @param string $storeVersion version of the data store
     */
    public static function addStore($storeId, $storeLocation, $storeType = "license", $storeVersion = STORE_VERSION)
    {
        $store = new AddonsStore();
        $store->setStoreId($storeId);
        $store->setStoreLocation($storeLocation);
        $store->setStoreVersion($storeVersion);
        $store->setStoreType($storeType);

        return AddonsStorePeer::doInsert($store);
    }

    /**
     * Check if the current license has a store and removes unwanted stores.
     *
     * @return bool true if a store was added, false otherwise.
     */
    public static function checkLicenseStore()
    {
        //getting the licenseManager....
        $licenseManager = &PmLicenseManager::getSingleton();

        if (isset($licenseManager->id)) {
            //Remove any license store that is not the active license
            $criteria = new Criteria(AddonsStorePeer::DATABASE_NAME);
            $criteria->addSelectColumn("*");
            $criteria->add(AddonsStorePeer::STORE_TYPE, "license", Criteria::EQUAL);
            $criteria->add(AddonsStorePeer::STORE_ID, $licenseManager->id, Criteria::NOT_EQUAL);

            foreach (AddonsStorePeer::doSelect($criteria) as $store) {
                $store->clear();
            }

            AddonsStorePeer::doDelete($criteria);

            //If the active license doesn't have a store, add one for it
            if (AddonsStorePeer::retrieveByPK($licenseManager->id) === null) {
                preg_match("/^license_(.*).dat$/", $licenseManager->file, $matches);
                $realId = urlencode($matches[1]);
                $workspace = (isset($licenseManager->workspace)) ? $licenseManager->workspace : 'pmLicenseSrv';
                $addonLocation = "http://{$licenseManager->server}/sys".$workspace."/en/green/services/addonsStore?action=getInfo&licId=$realId";

                self::addStore($licenseManager->id, $addonLocation);

                return true;
            }
        }

        return false;
    }

    public static function addonList($type = 'plugin')
    {
        $result = array();

        AddonsStore::checkLicenseStore();

        $licenseManager = &PmLicenseManager::getSingleton(); //Getting the licenseManager

        $result["store_errors"] = array();
        list($stores, $errors)  = AddonsStore::updateAll(false, $type);

        foreach ($errors as $store_id => $store_error) {
            $result["store_errors"][] = array("id" => $store_id, "msg" => $store_error);
        }

        $result["addons"] = array();
        $result["errors"] = array();

        $criteria = new Criteria();
        $criteria->addAscendingOrderByColumn(AddonsManagerPeer::ADDON_TYPE);
        $criteria->addAscendingOrderByColumn(AddonsManagerPeer::ADDON_ID);
        $criteria->add(AddonsManagerPeer::ADDON_TYPE, $type, Criteria::EQUAL);
        $addons = AddonsManagerPeer::doSelect($criteria);

        foreach ($addons as $addon) {
            if ($addon->getAddonState() != '' && $addon->isInstalled()) {
                $addon->setState();
                $addon->refresh();
            }

            $status  = $addon->getAddonStatus();
            $version = $addon->getAddonVersion();
            $enabled = null;

            if (!$addon->checkState()) {
                $result["errors"][] = array("addonId" => $addon->getAddonId(), "storeId" => $addon->getStoreId());
            }

            $sw = 1;
            if ($type == 'plugin') {
                $addonInLicense = false;
                if (!empty($addon->getAddonId()) && !empty($licenseManager->features)) {
                    $addonInLicense = in_array($addon->getAddonId(), $licenseManager->features);
                }

                if ($sw == 1 && $addon->getAddonId() != "enterprise" && !$addonInLicense) {
                    $sw = 0;
                }
                if ($sw == 1 && $addon->isInstalled()) {
                    if ($addon->isEnabled()) {
                        $status = "installed";
                    } else {
                        $status = "disabled";
                    }

                    $version = $addon->getInstalledVersion();

                    if (version_compare($version . "", $addon->getAddonVersion() . "", "<")) {
                        $status = "upgrade";
                    }

                    $enabled = (bool)$addon->isEnabled();
                    $sw = 0;
                }
            } else {
                $status = "available";
                $enabled = false;
                $addonInLicense = in_array($addon->getAddonId(), $licenseManager->licensedfeatures);
                if (in_array($addon->getAddonName(), $licenseManager->licensedfeatures) == 1) {
                    $status = "installed";
                    $enabled = true;
                }
            }

            if ($sw == 1 && $addonInLicense) {
                $status = "ready";
                $sw = 0;
            }

            $state = $addon->getAddonState();
            $log   = null;

            if ($state != null) {
                $status = $state;
                $log    = $addon->getInstallLog();
            }
            if ($addon->getAddonId() == "enterprise" && $status== 'ready') {
                $status = 'installed';
            }
            if ($status == 'minus-circle' ) {
                $status = "available";
            }

            $result["addons"][$addon->getAddonId()] = array(
                "id"             => $addon->getAddonId(),
                "store"          => $addon->getStoreId(),
                "name"           => $addon->getAddonName(),
                "nick"           => $addon->getAddonNick(),
                "version"        => $version,
                "enabled"        => $enabled,
                "latest_version" => $addon->getAddonVersion(),
                "type"           => $addon->getAddonType(),
                "release_type"   => $addon->getAddonReleaseType(),
                "url"            => $addon->getAddonDownloadUrl(),
                "publisher"      => $addon->getAddonPublisher(),
                "description"    => $addon->getAddonDescription(),
                "status"         => $status,
                "log"            => $log,
                "progress"       => round($addon->getAddonDownloadProgress())
            );
        }

        return $result;
    }

    public static function addonFeatureList()
    {
        $result = array();

        AddonsStore::checkLicenseStore();

        $licenseManager = &PmLicenseManager::getSingleton(); //Getting the licenseManager

        $result["store_errors"] = array();
        list($stores, $errors)  = AddonsStore::updateAll(false);

        foreach ($errors as $store_id => $store_error) {
            $result["store_errors"][] = array("id" => $store_id, "msg" => $store_error);
        }

        $result["addons"] = array();
        $result["errors"] = array();

        $criteria = new Criteria();
        $criteria->addAscendingOrderByColumn(AddonsManagerPeer::ADDON_TYPE);
        $criteria->addAscendingOrderByColumn(AddonsManagerPeer::ADDON_ID);
        $addons = AddonsManagerPeer::doSelect($criteria);

        foreach ($addons as $addon) {
            $status  = $addon->getAddonStatus();
            $version = $addon->getAddonVersion();
            $enabled = null;

            if (!$addon->checkState()) {
                $result["errors"][] = array("addonId" => $addon->getAddonId(), "storeId" => $addon->getStoreId());
            }

            $sw = 1;

            $addonInLicense = false;
            if (!empty($addon->getAddonId()) && !empty($licenseManager->features)) {
                $addonInLicense = in_array($addon->getAddonId(), $licenseManager->features);
            }

            if ($sw == 1 && $addon->getAddonId() != "enterprise" && !$addonInLicense) {
                $sw = 0;
            }

            if ($sw == 1 && $addon->isInstalled()) {
                if ($addon->isEnabled()) {
                    $status = "installed";
                } else {
                    $status = "disabled";
                }

                $version = $addon->getInstalledVersion();

                if (version_compare($version . "", $addon->getAddonVersion() . "", "<")) {
                    $status = "upgrade";
                }

                $enabled = $addon->isEnabled();
                $sw = 0;
            }

            if ($sw == 1 && $addonInLicense) {
                $status = "ready";
                $sw = 0;
            }

            $state = $addon->getAddonState();
            $log   = null;

            if ($state != null) {
                $status = $state;
                $log    = $addon->getInstallLog();
            }
            if ($addon->getAddonId() == "enterprise" && $status== 'ready') {
                $status = 'installed';
            }
            if ($status == 'minus-circle' ) {
                $status = "available";
            }

            $result["addons"][$addon->getAddonId()] = array(
                "id"             => $addon->getAddonId(),
                "store"          => $addon->getStoreId(),
                "name"           => $addon->getAddonName(),
                "nick"           => $addon->getAddonNick(),
                "version"        => $version,
                "enabled"        => $enabled,
                "latest_version" => $addon->getAddonVersion(),
                "type"           => $addon->getAddonType(),
                "release_type"   => $addon->getAddonReleaseType(),
                "url"            => $addon->getAddonDownloadUrl(),
                "publisher"      => $addon->getAddonPublisher(),
                "description"    => $addon->getAddonDescription(),
                "status"         => $status,
                "log"            => $log,
                "progress"       => round($addon->getAddonDownloadProgress())
            );
        }

        return $result;
    }

    /**
     * Returns all stores as AddonsStore objects.
     *
     * @return array of AddonsStore objects
     */
    public static function listStores()
    {
        $criteria = new Criteria(AddonsStorePeer::DATABASE_NAME);

        return AddonsStorePeer::doSelect($criteria);
    }

    /**
     * Updates all stores
     *
     * @return array containing a 'stores' array and a 'errors' array
     */
    public static function updateAll($force = false, $type = 'plugin')
    {
        $stores = array();
        $errors = array();

        foreach (self::listStores() as $store) {
            try {
                $stores[$store->getStoreId()] = $store->update($force, $type);
            } catch (Exception $e) {
                $errors[$store->getStoreId()] = $e->getMessage();
            }
        }

        return array($stores, $errors);
    }

    /**
     * Clear this store addons
     *
     * @return int number of addons removed
     */
    public function clear($type = 'plugin')
    {
        /* Remove old items from this store */
        $criteria = new Criteria(AddonsManagerPeer::DATABASE_NAME);
        $criteria->add(AddonsManagerPeer::STORE_ID, $this->getStoreId(), Criteria::EQUAL);
        $criteria->add(AddonsManagerPeer::ADDON_TYPE, $type, Criteria::EQUAL);

        return AddonsManagerPeer::doDelete($criteria);
    }

    /**
     * Update this store information from the store location.
     *
     * @return bool true if updated, false otherwise
     */
    public function update($force = false, $type = 'plugin')
    {
        //If we have any addon that is installing or updating, don't update store
        $criteria = new Criteria(AddonsManagerPeer::DATABASE_NAME);
        $criteria->add(AddonsManagerPeer::ADDON_STATE, '', Criteria::NOT_EQUAL);
        $criteria->add(AddonsManagerPeer::ADDON_TYPE, $type);

        if (AddonsManagerPeer::doCount($criteria) > 0) {
            return false;
        }

        $this->clear($type);

        //Fill with local information

        //List all plugins installed
        $oPluginRegistry = PluginRegistry::loadSingleton();
        $aPluginsPP = array();

        if (file_exists(PATH_DATA_SITE . 'ee')) {
            $aPluginsPP = unserialize(trim(file_get_contents(PATH_DATA_SITE . 'ee')));
        }

        $pmLicenseManagerO = &PmLicenseManager::getSingleton();
        $localPlugins = array();

        if ($type == 'plugin') {
            foreach ($aPluginsPP as $aPlugin) {
                $sClassName = substr($aPlugin['sFilename'], 0, strpos($aPlugin['sFilename'], '-'));

                if (file_exists(PATH_PLUGINS . $sClassName . '.php')) {
                    require_once PATH_PLUGINS . $sClassName . '.php';

                    $oDetails = $oPluginRegistry->getPluginDetails($sClassName . '.php');

                    if ($oDetails) {
                        $sStatus = $oDetails->isEnabled() ? G::LoadTranslation('ID_ENABLED') : G::LoadTranslation('ID_DISABLED');

                        if ($oDetails->getWorkspaces()) {
                            if (!in_array(SYS_SYS, $oDetails->getWorkspaces())) {
                                continue;
                            }
                        }

                        if ($sClassName == "pmLicenseManager" || $sClassName == "pmTrial") {
                            continue;
                        }

                        $sEdit = (($oDetails->getSetupPage() != '') && ($oDetails->isEnabled())? G::LoadTranslation('ID_SETUP') : ' ');
                        $aPlugin = array();
                        $aPluginId = $sClassName;
                        $aPluginTitle = $oDetails->getFriendlyName();
                        $aPluginDescription = $oDetails->getDescription();
                        $aPluginVersion = $oDetails->getVersion();

                        if (@in_array($sClassName, $pmLicenseManagerO->features)) {
                            $aPluginStatus = $sStatus;
                            $aPluginLinkStatus = 'pluginsChange?id=' . $sClassName . '.php&status=' . $oDetails->isEnabled();
                            $aPluginEdit = $sEdit;
                            $aPluginLinkEdit = 'pluginsSetup?id=' . $sClassName . '.php';
                            $aPluginStatusA = $sStatus == "Enabled" ? "installed" : 'disabled';
                            $enabledStatus = true;
                        } else {
                            $aPluginStatus = "";
                            $aPluginLinkStatus = '';
                            $aPluginEdit = '';
                            $aPluginLinkEdit = '';
                            $aPluginStatusA = 'minus-circle';
                            $enabledStatus = false;
                        }

                        $addon = new AddonsManager();
                        //G::pr($addon);
                        $addon->setAddonId($aPluginId);
                        $addon->setStoreId($this->getStoreId());
                        //Don't trust external data
                        $addon->setAddonName($aPluginId);
                        $addon->setAddonDescription($aPluginDescription);
                        $addon->setAddonNick($aPluginTitle);
                        $addon->setAddonVersion("");
                        $addon->setAddonStatus($aPluginStatusA);
                        $addon->setAddonType("plugin");
                        $addon->setAddonPublisher("Colosa");
                        $addon->setAddonDownloadUrl("");
                        $addon->setAddonDownloadMd5("");
                        $addon->setAddonReleaseDate(null);
                        $addon->setAddonReleaseType('localRegistry');
                        $addon->setAddonReleaseNotes("");
                        $addon->setAddonState("");

                        $addon->save();

                        $localPlugins[$aPluginId] = $addon;
                    }
                }
            }
        } else {
            $list = unserialize($pmLicenseManagerO->licensedfeaturesList);
            if (is_array($list)) {
                foreach ($list['addons'] as $key => $feature) {
                    $addon = new AddonsManager();
                    if($addon->exists($feature['name'],$feature['guid'])){
                        $arrayData['ADDON_ID']   = $feature['name'];
                        $arrayData['STORE_ID']   = $feature['guid'];
                        $arrayData['ADDON_NAME'] = $feature['name'];
                        $arrayData['ADDON_NICK'] = $feature['nick'];
                        $arrayData['ADDON_DESCRIPTION'] = $feature['description'];
                        $arrayData['ADDON_STATE']     = '';
                        $arrayData['ADDON_STATUS']    = $feature['status'];
                        $arrayData['ADDON_VERSION']   = '';
                        $arrayData['ADDON_TYPE']      = 'features';
                        $arrayData['ADDON_PUBLISHER'] = 'Colosa';
                        $arrayData['ADDON_RELEASE_DATE']  = null;
                        $arrayData['ADDON_RELEASE_TYPE']  = 'localRegistry';
                        $arrayData['ADDON_RELEASE_NOTES'] = '';
                        $arrayData['ADDON_DOWNLOAD_URL']  = '';
                        $arrayData['ADDON_DOWNLOAD_MD5']  = '';

                        $addon->update($arrayData);
                    } else {
                        $addon->setAddonId($feature['name']);
                        $addon->setStoreId($feature['guid']);
                        $addon->setAddonName($feature['name']);
                        $addon->setAddonDescription($feature['description']);
                        $addon->setAddonNick($feature['nick']);
                        $addon->setAddonVersion("");
                        $addon->setAddonStatus($feature['status']);
                        $addon->setAddonType("features");
                        $addon->setAddonPublisher("Colosa");
                        $addon->setAddonDownloadUrl("");
                        $addon->setAddonDownloadMd5("");
                        $addon->setAddonReleaseDate(null);
                        $addon->setAddonReleaseType('localRegistry');
                        $addon->setAddonReleaseNotes("");
                        $addon->setAddonState("");

                        $addon->save();
                    }
                }
            }
        }

        $this->setStoreLastUpdated(time());
        $this->save();

        $url = $this->getStoreLocation();

        //Validate url
        $licenseInfo = $pmLicenseManagerO->getActiveLicense();
        $licenseId = str_replace('.dat', '', str_replace('license_', '', basename($licenseInfo['LICENSE_PATH'])));

        $url = explode('&', $url);
        $url[count($url) - 1] = 'licId=' . urlencode($licenseId);
        $url = implode('&', $url);

        if (EnterpriseUtils::getInternetConnection() == 1 && EnterpriseUtils::checkConnectivity($url) == true) {
            $option = array(
                "http" => array(
                    "method" => "POST",
                    "header" => "Content-type: application/x-www-form-urlencoded\r\n",
                    "content" => http_build_query(
                        array(
                            "pmVersion" => System::getVersion(),
                            "version" => STORE_VERSION
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

            //This may block for a while, always use AJAX to call this method
            $url = $url . '&type=' . strtoupper($type);
            $data = file_get_contents($url, false, $context);

            if ($data === false) {
                throw new Exception("Could not contact store");
            }

            $serverData = G::json_decode($data);

            //Don't trust external data
            if (empty($serverData)) {
                throw (new Exception("Store data invalid ('$data')"));
            }

            if (isset($serverData->error)) {
                throw (new Exception("Store sent us an error: {$serverData->error}"));
            }

            if (!isset($serverData->version)) {
                throw (new Exception("Store version not found"));
            }

            if ($serverData->version != STORE_VERSION) {
                throw (new Exception("Store version '{$serverData->version}' unsupported"));
            }

            if (!isset($serverData->addons)) {
                throw (new Exception("Addons not found on store data"));
            }

            $this->clear($type);

            try {
                //Add each item to this stores addons
                $addons = @get_object_vars($serverData->addons);

                if (!empty($addons)) {
                    foreach (get_object_vars($serverData->addons) as $addonId => $addonInfo) {
                        $addon = new AddonsManager();
                        $addon->setAddonId($addonId);
                        $addon->setStoreId($this->getStoreId());
                        //Don't trust external data
                        $addon->setAddonName(isset($addonInfo->name)? $addonInfo->name : $addonId);
                        $addon->setAddonDescription(isset($addonInfo->description)? $addonInfo->description : "");
                        $addon->setAddonNick(isset($addonInfo->nick)? $addonInfo->nick : "");
                        $addon->setAddonVersion(isset($addonInfo->version)? $addonInfo->version : "");
                        $addon->setAddonStatus(isset($addonInfo->status)? $addonInfo->status : "");
                        $addon->setAddonType(isset($addonInfo->type)? $addonInfo->type : "");
                        $addon->setAddonPublisher(isset($addonInfo->publisher)? $addonInfo->publisher : "");
                        $workspace = (isset($pmLicenseManagerO->workspace)) ? $pmLicenseManagerO->workspace : 'pmLicenseSrv';
                        $addon->setAddonDownloadUrl(isset($addonInfo->download_url)? $addonInfo->download_url : "http://" . $pmLicenseManagerO->server . "/sys".$workspace."/en/green/services/rest?action=getPlugin&OBJ_UID=" . $addonInfo->guid);
                        $addon->setAddonDownloadMd5(isset($addonInfo->download_md5)? $addonInfo->download_md5 : "");
                        $addon->setAddonReleaseDate(isset($addonInfo->release_date)? $addonInfo->release_date : "");
                        $addon->setAddonReleaseType(isset($addonInfo->release_type)? $addonInfo->release_type : '');
                        $addon->setAddonReleaseNotes(isset($addonInfo->release_notes)? $addonInfo->release_notes : "");
                        $addon->setAddonState("");

                        $addon->save();

                        if (isset($localPlugins[$addonId])) {
                            unset($localPlugins[$addonId]);
                        }
                    }

                    foreach ($localPlugins as $keyPlugin => $addonA) {
                        //G::pr($addonA );
                        //$addonA->save();
                        $addon = new AddonsManager();
                        //G::pr($addon);
                        $addon->setAddonId($addonA->getAddonId());
                        $addon->setStoreId($addonA->getStoreId());
                        //Don't trust external data
                        $addon->setAddonName($addonA->getAddonName());
                        $addon->setAddonDescription($addonA->getAddonDescription());
                        $addon->setAddonNick($addonA->getAddonNick());
                        $addon->setAddonVersion("");
                        $addon->setAddonStatus($addonA->getAddonStatus());
                        $addon->setAddonType($addonA->getAddonType());
                        $addon->setAddonPublisher($addonA->getAddonPublisher());
                        $addon->setAddonDownloadUrl($addonA->getAddonDownloadUrl());
                        $addon->setAddonDownloadMd5($addonA->getAddonDownloadMd5());
                        $addon->setAddonReleaseDate(null);
                        $addon->setAddonReleaseType('localRegistry');
                        $addon->setAddonReleaseNotes("");
                        $addon->setAddonState("");

                        $addon->save();
                    }
                }

                $this->setStoreLastUpdated(time());
                $this->save();
            } catch (Exception $e) {
                //If we had issues, don't keep only a part of the items
                $this->clear($type);

                throw $e;
            }
        }

        return true;
    }
}
