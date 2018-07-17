<?php

use ProcessMaker\Plugins\PluginRegistry;
use Illuminate\Support\Facades\Cache;

/**
 * class.pmLicenseManager.php
 *
 */
class PmLicenseManager
{
    const CACHE_KEY = 'license';

    private static $instance = null;

    public function __construct($flagActivatePlugins = true)
    {
        $oServerConf = &ServerConf::getSingleton();

        if ($oServerConf->getProperty('LOGIN_NO_WS') === null || $oServerConf->getProperty('LOGIN_NO_WS') === false) {
            $oServerConf->setProperty('LOGIN_NO_WS', true);
        }

        //searching .dat files in workspace folder
        $server_array = $_SERVER;

        $activeLicenseSetting = $oServerConf->getProperty('ACTIVE_LICENSE');

        if ((isset($activeLicenseSetting[SYS_SYS])) && (file_exists($activeLicenseSetting[SYS_SYS]))) {
            $licenseFile = $activeLicenseSetting[SYS_SYS];
        } else {
            $activeLicense = $this->getActiveLicense();
            $oServerConf->setProperty('ACTIVE_LICENSE', [SYS_SYS => $activeLicense['LICENSE_PATH']]);
            $licenseFile = $activeLicense['LICENSE_PATH'];
        }

        $application = new license_application($licenseFile, false, true, false, true);
        $application->set_server_vars($server_array);
        $application->DATE_STRING = 'Y-m-d H:i:s';
        $results = $application->validate();
        $validStatus = [
            'OK',
            'EXPIRED',
            'TMINUS'
        ];

        $this->result = $results['RESULT'];
        $this->features = [];
        $this->licensedfeatures = [];
        $this->licensedfeaturesList = [];
        if (in_array($this->result, $validStatus)) {
            $this->serial = "3ptta7Xko2prrptrZnSd356aqmPXvMrayNPFj6CLdaR1pWtrW6qPw9jV0OHjxrDGu8LVxtmSm9nP5kR23HRpdZWccpeui+bKkK°DoqCt2Kqgpq6Vg37s";
            $info = [];
            $info['FIRST_NAME'] = $results['DATA']['FIRST_NAME'];
            $info['LAST_NAME'] = $results['DATA']['LAST_NAME'];
            $info['DOMAIN_WORKSPACE'] = $results['DATA']['DOMAIN_WORKSPACE'];
            $this->date = $results ['DATE'];
            $this->info = $info;
            $this->type = $results ['DATA']['TYPE'];
            $this->plan = isset($results ['DATA']['PLAN']) ? $results ['DATA']['PLAN'] : "";
            $this->id = $results ['ID'];
            $this->expireIn = $this->getExpireIn();
            $this->features = $this->result != 'TMINUS' ? isset($results ['DATA']['CUSTOMER_PLUGIN']) ? $results ['DATA']['CUSTOMER_PLUGIN'] : $this->getActiveFeatures() : [];
            $this->licensedfeatures = $this->result != 'TMINUS' ? (isset($results ['DATA']['CUSTOMER_LICENSED_FEATURES']) && is_array($results ['DATA']['CUSTOMER_LICENSED_FEATURES'])) ? $results ['DATA']['CUSTOMER_LICENSED_FEATURES'] : [] : [];
            $this->licensedfeaturesList = isset($results ['DATA']['LICENSED_FEATURES_LIST']) ? $results ['DATA']['LICENSED_FEATURES_LIST'] : null;
            $this->status = $this->getCurrentLicenseStatus();

            if (isset ($results ['LIC'])) {
                $resultsRegister = $results['LIC'];
                $this->server = $results['LIC']['SRV'];
                $this->file = $results['LIC']['FILE'];
                $this->workspace = isset($results['LIC']['WORKSPACE']) ? $results['LIC']['WORKSPACE'] : 'pmLicenseSrv';
                $this->licenseSerial = (isset($results['LIC']['SERIAL'])) ? $results['LIC']['SERIAL'] : '';
                $this->supportStartDate = (isset($results['DATA']['SUPPORT_START_DATE'])) ? $results['DATA']['SUPPORT_START_DATE'] : '';
                $this->supportEndDate = (isset($results['DATA']['SUPPORT_END_DATE'])) ? $results['DATA']['SUPPORT_END_DATE'] : '';
                $this->supportStartDate = date("Y-m-d H:i:s", strtotime($this->supportStartDate));
                $this->supportEndDate = date("Y-m-d H:i:s", strtotime($this->supportEndDate));

                $conf = new Configurations();
                if (defined('SYS_SYS') && $conf->exists("ENVIRONMENT_SETTINGS")) {
                    $this->supportStartDate = $conf->getSystemDate($this->supportStartDate);
                    $this->supportEndDate = $conf->getSystemDate($this->supportEndDate);
                } else {
                    $this->supportStartDate = G::getformatedDate($this->supportStartDate, 'M d, yyyy', SYS_LANG);
                    $this->supportEndDate = G::getformatedDate($this->supportEndDate, 'M d, yyyy', SYS_LANG);
                }
            } else {
                $resultsRegister = [];
                $resultsRegister['ID'] = $results['DATA']['DOMAIN_WORKSPACE'];
                $this->server = null;
                $this->file = null;
            }

            // The HUMAN attribute varies according to the timezone configured in the server, therefore it does not need
            // to be considered in the comparison if the value was changed or not, it is only comparing  with te "timestamp"
            if (isset($results['DATE']['HUMAN'])) {
                unset($results['DATE']['HUMAN']);
            }
            $resultsRegister['date'] = $results ['DATE'];
            $resultsRegister['info'] = $info;
            $resultsRegister['type'] = $results ['DATA'] ['TYPE'];
            if ($oServerConf->getProperty('LICENSE_INFO')) {
                $licInfoA = $oServerConf->getProperty('LICENSE_INFO');
                // The HUMAN attribute varies according to the timezone configured in the server, therefore it does not need
                // to be considered in the comparison if the value was changed or not, it is only comparing  with te "timestamp"
                if (isset($licInfoA[SYS_SYS]['date']['HUMAN'])) {
                    unset($licInfoA[SYS_SYS]['date']['HUMAN']);
                }
            } else {
                $licInfoA = [];
            }
            if (empty($licInfoA[SYS_SYS]) || ($licInfoA[SYS_SYS] != $resultsRegister)) {
                $licInfoA[SYS_SYS] = $resultsRegister;
                $oServerConf->setProperty('LICENSE_INFO', $licInfoA);
            }
        }

        if ($flagActivatePlugins) {
            $this->activateFeatures();
        }
    }

    public static function getSingleton($flagActivatePlugins = true)
    {
        if (self::$instance == null) {
            self::$instance = new PmLicenseManager($flagActivatePlugins);
        }
        return self::$instance;
    }

    public function serializeInstance()
    {
        return serialize(self::$instance);
    }

    public function activateFeatures()
    {
        //Get a list of all Enterprise plugins and active/inactive them
        if (file_exists(PATH_PLUGINS . 'enterprise/data/default')) {
            if ($this->result == "OK") {
                //Disable
                if (file_exists(PATH_PLUGINS . 'enterprise/data/data')) {
                    $oPluginRegistry = PluginRegistry::loadSingleton();
                    $aPlugins = unserialize(trim(file_get_contents(PATH_PLUGINS . 'enterprise/data/data')));
                    foreach ($aPlugins as $aPlugin) {
                        $sClassName = substr($aPlugin ['sFilename'], 0, strpos($aPlugin ['sFilename'], '-'));
                        require_once PATH_PLUGINS . $sClassName . '.php';
                        $oDetails = $oPluginRegistry->getPluginDetails($sClassName . '.php');
                        $oPluginRegistry->disablePlugin($oDetails->getNamespace());
                        $oPluginRegistry->savePlugin($oDetails->getNamespace());
                    }
                    unlink(PATH_PLUGINS . 'enterprise/data/data');
                }

                //Enable
                $oPluginRegistry = PluginRegistry::loadSingleton();
                $aPlugins = unserialize(trim(file_get_contents(PATH_PLUGINS . "enterprise/data/default")));

                foreach ($aPlugins as $aPlugin) {
                    if ($aPlugin ["bActive"]) {
                        $sClassName = substr($aPlugin["sFilename"], 0, strpos($aPlugin["sFilename"], "-"));
                        require_once(PATH_PLUGINS . $sClassName . ".php");
                        $oDetails = $oPluginRegistry->getPluginDetails($sClassName . ".php");
                        $oPluginRegistry->enablePlugin($oDetails->getNamespace());
                        $oPluginRegistry->savePlugin($oDetails->getNamespace());
                    }
                }

                if (file_exists(PATH_DATA_SITE . "ee")) {
                    $aPlugins = unserialize(trim(file_get_contents(PATH_DATA_SITE . 'ee')));
                    $aDenied = [];
                    foreach ($aPlugins as $aPlugin) {
                        $sClassName = substr($aPlugin ['sFilename'], 0, strpos($aPlugin ['sFilename'], '-'));
                        if (!(in_array($sClassName, $this->features))) {
                            if (file_exists(PATH_PLUGINS . $sClassName . '.php')) {
                                require_once PATH_PLUGINS . $sClassName . '.php';
                                $oDetails = $oPluginRegistry->getPluginDetails($sClassName . '.php');
                                $oPluginRegistry->disablePlugin($oDetails->getNamespace());
                                $oPluginRegistry->savePlugin($oDetails->getNamespace());
                                $aDenied[] = $oDetails->getNamespace();
                            }
                        }
                    }
                    if (!(empty($aDenied))) {
                        if ((SYS_COLLECTION == "enterprise") && (SYS_TARGET == "pluginsList")) {
                            G::SendMessageText("The following plugins were restricted due to your enterprise license: " . implode(", ",
                                    $aDenied), "INFO");
                        }
                    }
                }
            } else {
                //Disable
                $oPluginRegistry = PluginRegistry::loadSingleton();
                $aPlugins = unserialize(trim(file_get_contents(PATH_PLUGINS . 'enterprise/data/default')));
                foreach ($aPlugins as $aPlugin) {
                    $sClassName = substr($aPlugin ['sFilename'], 0, strpos($aPlugin ['sFilename'], '-'));
                    //To avoid self disable
                    if (($sClassName != "pmLicenseManager") && ($sClassName != "pmTrial") && ($sClassName != "enterprise")) {
                        require_once PATH_PLUGINS . $sClassName . '.php';
                        $oDetails = $oPluginRegistry->getPluginDetails($sClassName . '.php');
                        $oPluginRegistry->disablePlugin($oDetails->getNamespace());
                    } else {
                        //Enable default and required plugins
                        require_once PATH_PLUGINS . $sClassName . '.php';
                        $oDetails = $oPluginRegistry->getPluginDetails($sClassName . '.php');
                        $oPluginRegistry->enablePlugin($oDetails->getNamespace());
                    }
                    $oPluginRegistry->savePlugin($oDetails->getNamespace());
                }

                if (file_exists(PATH_DATA_SITE . 'ee')) {
                    $aPlugins = unserialize(trim(file_get_contents(PATH_DATA_SITE . 'ee')));

                    foreach ($aPlugins as $aPlugin) {
                        $sClassName = substr($aPlugin ['sFilename'], 0, strpos($aPlugin ['sFilename'], '-'));
                        if (strlen($sClassName) > 0) {
                            if (!class_exists($sClassName)) {
                                require_once PATH_PLUGINS . $sClassName . '.php';
                            }
                            $oDetails = $oPluginRegistry->getPluginDetails($sClassName . '.php');
                            if ($oDetails) {
                                $oPluginRegistry->disablePlugin($oDetails->getNamespace());
                                $oPluginRegistry->savePlugin($oDetails->getNamespace());
                            }
                        }
                    }
                }
            }
        }
    }

    public function getCurrentLicenseStatus()
    {
        $result = [];
        switch ($this->result) {
            case 'OK':
                $result ['result'] = 'ok';
                $result ['message'] = "";
                break;
            case 'TMINUS':
                $result ['result'] = 'tminus';
                $startDateA = explode(" ", $this->date['HUMAN']['START']);
                $result ['message'] = "License will be active on " . $startDateA[0];
                break;
            case 'EXPIRED':
                $result ['result'] = 'expired';
                $result ['message'] = "License Expired";
                break;
            case 'ILLEGAL':
                $result ['result'] = 'illegal';
                $result ['message'] = "Illegal License";
                break;
            case 'ILLEGAL_LOCAL':
                $result ['result'] = 'illegal';
                $result ['message'] = "Illegal Local License";
                break;
            case 'INVALID':
                $result ['result'] = 'invalid';
                $result ['message'] = "Invalid License";
                break;
            case 'EMPTY':
                $result ['result'] = 'empty';
                $result ['message'] = "Empty License";
                if (defined('write_error')) {
                    $result ['message'] = "Write error" . $result ['message'];
                }
                break;
            default:
                break;
        }
        return $result;
    }

    public function unSerializeInstance($serialized)
    {
        if (self::$instance == null) {
            self::$instance = new PluginRegistry();
        }
        $instance = unserialize($serialized);
        self::$instance = $instance;
    }

    public function getExpireIn()
    {
        $status = $this->getCurrentLicenseStatus();
        $expireIn = 0;
        if ($status ['result'] == 'ok') {
            if ($this->date ['END'] != "NEVER") {
                $expireIn = ceil(($this->date ['END'] - time()) / 60 / 60 / 24);
            } else {
                $expireIn = "NEVER";
            }
        }
        return $expireIn;
    }

    public function getLicenseInfo()
    {
        $validStatus = [
            'ok',
            'expired'
        ];
        $status = $this->getCurrentLicenseStatus();
        $infoText = "";
        if (in_array($status ['result'], $validStatus)) {
            $start = explode(" ", $this->date ['HUMAN'] ['START']);
            $end = explode(" ", $this->date ['HUMAN'] ['END']);
            $infoText .= "<b>" . "Issued to" . ":</b> " . $this->info ['FIRST_NAME'] . " " . $this->info ['LAST_NAME'] . "<br>";
            $infoText .= "<b>" . G::LoadTranslation('ID_WORKSPACE') . ":</b> " . $this->info ['DOMAIN_WORKSPACE'] . "<br>";
            $infoText .= "<i>" . G::LoadTranslation('ID_VALID_FROM') . " " . $start [0] . " " . G::LoadTranslation('ID_TO') . " " . $end [0] . "</i>";
        }
        if ($status ['message'] != "") {
            $infoText .= "&nbsp;<font color=red><b>- " . $status ['message'] . "</b></font>";
        }
        $info ['infoText'] = $infoText;
        $info ['infoLabel'] = $status ['message'];
        return $info;
    }

    public function getExpireInLabel()
    {
        $linkText = null;

        if ($this->getExpireIn() != "NEVER" && ((int)$this->getExpireIn() <= 30) && ((int)$this->getExpireIn() > 0)) {
            $infoO = $this->getLicenseInfo();
            $infoText = $infoO['infoText'];
            $js = (EnterpriseUtils::skinIsUx() == 1) ? "Ext.MessageBox.show({title: '', msg: '$infoText', buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.INFO});" : "msgBox('$infoText');";
            $linkText = $linkText . "<span style=\"color: red;\">" . G::LoadTranslation('ID_EXPIRES_IN') . " " . $this->getExpireIn() . " " . G::LoadTranslation('ID_DAYS') . "</span>";
        } else {
            if ($this->getExpireIn() != "NEVER" && (int)$this->getExpireIn() <= 0) {
                $infoO = $this->getLicenseInfo();
                $infoText = $infoO['infoText'];
                $infoLabel = $infoO['infoLabel'];
                $js = (EnterpriseUtils::skinIsUx() == 1) ? "Ext.MessageBox.show({title: '', msg: '$infoText', buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.INFO});" : "msgBox('$infoText');";
                $linkText = $linkText . "<a href=\"javascript:;\" onclick=\"$js return (false);\"><span style=\"color: red;\">" . $infoLabel . "</span></a>";
            }
        }

        if (class_exists('pmTrialPlugin')) {
            $linkText = $linkText . "<a href='/sys" . SYS_SYS . "/" . SYS_LANG . "/" . SYS_SKIN . "/pmTrial/services/buyNow?n=true" . "'> <img align='absmiddle' src='/plugin/pmLicenseManager/btn_buy_now.gif' border='0' /></a>";
        }

        if (isset($_SESSION["__ENTERPRISE_SYSTEM_UPDATE__"]) && $_SESSION["__ENTERPRISE_SYSTEM_UPDATE__"] == 1) {
            $aOnclick = "onclick=\"this.href='" . EnterpriseUtils::getUrlServerName() . "/sys" . SYS_SYS . "/" . SYS_LANG . "/" . SYS_SKIN . "/setup/main?s=PMENTERPRISE';\"";
            if (EnterpriseUtils::skinIsUx() == 1) {
                $aOnclick = "onclick=\"Ext.ComponentMgr.get('mainTabPanel').setActiveTab('pm-option-setup'); Ext.ComponentMgr.get('pm-option-setup').setLocation(Ext.ComponentMgr.get('pm-option-setup').defaultSrc + 's=PMENTERPRISE', true); return (false);\"";
            }
            $linkText = $linkText . (($linkText != null) ? " | " : null) . "<a href=\"javascript:;\" $aOnclick style=\"color: #008000;\">" . G::LoadTranslation('ID_UPGRADE_SYSTEM') . "</a>";
        }
        $linkText = ($linkText != null) ? $linkText . ((EnterpriseUtils::skinIsUx() == 1) ? null : " |") : null;
        return ($linkText);
    }

    public function validateLicense($path)
    {
        $application = new license_application ($path, false, true, false, true, true);
        $results = $application->validate(false, false, "", "", "80", true);

        if ($results ['RESULT'] != 'OK') {
            return true;
        } else {
            return false;
        }
    }

    public function installLicense($path, $redirect = true, $includeExpired = true)
    {
        $application = new license_application ($path, false, true, false, true, true);

        $results = $application->validate(false, false, "", "", "80", true);

        //if the result is ok then it is saved into DB
        $res = $results ['RESULT'];
        if ($res == 'EMPTY') {
            return false;
        }
        if (!$includeExpired) {
            if ($res == 'EXPIRED') {
                return false;
            }
        }
        if (($res != 'OK') && ($res != 'EXPIRED') && ($res != 'TMINUS')) {
            G::SendTemporalMessage('ID_ISNT_LICENSE', 'tmp-info', 'labels');
            return false;
        } else {

            $oServerConf = &ServerConf::getSingleton();
            $oServerConf->setProperty('ACTIVE_LICENSE', [SYS_SYS => $path]);
            $this->saveDataLicense($results, $path, $redirect);
            if ($redirect) {
                G::Header('location: ../enterprise/addonsStore');
            } else {
                return true;
            }
        }
    }

    /*
      get Active License
    */
    public function getActiveLicense()
    {
        //get license from database, table LICENSE_MANAGER
        try {
            $aRow = [];
            require_once("classes/model/LicenseManager.php");
            $oCriteria = new Criteria('workflow');
            $oCriteria->addSelectColumn(LicenseManagerPeer::LICENSE_USER);
            $oCriteria->addSelectColumn(LicenseManagerPeer::LICENSE_START);
            $oCriteria->addSelectColumn(LicenseManagerPeer::LICENSE_PATH);
            $oCriteria->addSelectColumn(LicenseManagerPeer::LICENSE_DATA);
            $oCriteria->add(LicenseManagerPeer::LICENSE_STATUS, 'ACTIVE');
            $oDataset = LicenseManagerPeer::doSelectRS($oCriteria);
            $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $oDataset->next();
            $aRow = $oDataset->getRow();
        } catch (Exception $e) {
            G::pr($e);
        }
        return $aRow;
    }

    public function lookForStatusLicense()
    {
        require_once("classes/model/LicenseManager.php");
        //obtening info in a row that has ACTIVE status
        $oCtia = new Criteria ('workflow');
        $oCtia->add(LicenseManagerPeer::LICENSE_STATUS, 'ACTIVE');
        $oDataset = LicenseManagerPeer::doSelectRS($oCtia);
        $oDataset->next();
        $aRow = $oDataset->getRow();

        $oCtiaA = new Criteria ('workflow');
        $oCtiaA->add(LicenseManagerPeer::LICENSE_UID, $aRow [0]);

        $oCtiaB = new Criteria ('workflow');
        $oCtiaB->add(LicenseManagerPeer::LICENSE_STATUS, 'INACTIVE');
        BasePeer::doUpdate($oCtiaA, $oCtiaB, Propel::getConnection('workflow'));
        return 'ACTIVE';
    }

    public function saveDataLicense($results, $path)
    {
        try {
            //getting info about file
            $LicenseUid = G::generateUniqueID();
            $LicenseUser = $results ['DATA'] ['FIRST_NAME'] . ' ' . $results ['DATA'] ['LAST_NAME'];
            $LicenseStart = $results ['DATE'] ['START'];
            $LicenseEnd = $results ['DATE'] ['END'];
            $LicenseSpan = $results ['DATE'] ['SPAN'];
            $LicenseStatus = $this->lookForStatusLicense(); //we're looking for a status ACTIVE

            //getting the content from file

            $filter = new InputFilter();
            $path = $filter->xssFilterHard($path, 'path');

            $handle = fopen($path, "r");
            $contents = fread($handle, filesize($path));
            fclose($handle);
            $LicenseData = $contents;
            $LicensePath = $path;
            $LicenseWorkspace = isset($results['DATA']['DOMAIN_WORKSPACE']) ? $results['DATA']['DOMAIN_WORKSPACE'] : '';
            $LicenseType = $results['DATA']['TYPE'];

            require_once("classes/model/LicenseManager.php");

            //if exists the row in the database propel will update it, otherwise will insert.
            $tr = LicenseManagerPeer::retrieveByPK($LicenseUid);
            if (!(is_object($tr) && get_class($tr) == 'LicenseManager')) {
                $tr = new LicenseManager ();
            }
            $tr->setLicenseUid($LicenseUid);
            $tr->setLicenseUser($LicenseUser);
            $tr->setLicenseStart($LicenseStart);
            $tr->setLicenseEnd($LicenseEnd);
            $tr->setLicenseSpan($LicenseSpan);
            $tr->setLicenseStatus($LicenseStatus);
            $tr->setLicenseData($LicenseData);
            $tr->setLicensePath($LicensePath);
            $tr->setLicenseWorkspace($LicenseWorkspace);
            $tr->setLicenseType($LicenseType);

            $res = $tr->save();
            Cache::forget(PmLicenseManager::CACHE_KEY . '.' . SYS_SYS);
        } catch (Exception $e) {
            G::pr($e);
        }
    }

    public function getResultQry($sNameTable, $sfield, $sCondition)
    {
        try {
            require_once("classes/model/LicenseManager.php");
            $oCriteria = new Criteria ('workflow');
            $oCriteria->addSelectColumn(LicenseManagerPeer::LICENSE_USER);
            $oCriteria->addSelectColumn(LicenseManagerPeer::LICENSE_START);
            $oCriteria->addSelectColumn(LicenseManagerPeer::LICENSE_PATH);
            $oCriteria->addSelectColumn(LicenseManagerPeer::LICENSE_DATA);
            $oCriteria->add(LicenseManagerPeer::LICENSE_STATUS, 'ACTIVE');
            $oDataset = LicenseManagerPeer::doSelectRS($oCriteria);
            $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $oDataset->next();
            $aRow = $oDataset->getRow();
        } catch (Exception $e) {
            G::pr($e);
            $aRow = [];
        }
        return $aRow;
    }

    public function getActiveFeatures()
    {
        if (file_exists(PATH_PLUGINS . 'enterprise/data/default')) {
            return [];
        }
        return unserialize(G::decrypt($this->serial, file_get_contents(PATH_PLUGINS . 'enterprise/data/default')));
    }
}
