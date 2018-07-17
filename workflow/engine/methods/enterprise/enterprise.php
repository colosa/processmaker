<?php

use ProcessMaker\Core\System;
use ProcessMaker\Plugins\PluginRegistry;

if (!defined("PATH_PM_ENTERPRISE")) {
    define("PATH_PM_ENTERPRISE", PATH_CORE . "enterprise/");
}

if (!defined("PATH_DATA_SITE")) {
    define("PATH_DATA_SITE", PATH_DATA . "sites/" . SYS_SYS . "/");
}

set_include_path(PATH_PM_ENTERPRISE . PATH_SEPARATOR . get_include_path());

class enterprisePlugin extends PMPlugin
{
    public function enterprisePlugin($sNamespace, $sFilename = null)
    {
        $pathPluginTrunk = PATH_CORE . "enterprise";

        $VERSION = System::getVersion();

        $res = parent::PMPlugin($sNamespace, $sFilename);
        $this->sFriendlyName = "ProcessMaker Enterprise Core Edition";
        $this->sDescription  = "ProcessMaker Enterprise Core Edition $VERSION";
        $this->sPluginFolder = "enterprise";
        $this->sSetupPage    = "../enterprise/addonsStore.php";
        $this->iVersion      = $VERSION;
        $this->iPMVersion    = "2.0.31";
        $this->aDependences  = null;
        $this->aWorkspaces   = null;

        $this->database = "workflow";
        $this->table    = array("ADDONS_STORE", "ADDONS_MANAGER", "LICENSE_MANAGER");

        if (!isset($_SESSION["__EE_INSTALLATION__"])) {
            $_SESSION["__EE_INSTALLATION__"] = 0;
        }

        if (!isset($_SESSION["__EE_SW_PMLICENSEMANAGER__"])) {
            $_SESSION["__EE_SW_PMLICENSEMANAGER__"] = 1;
        }

        $sw = 1;
        $msgf = null;
        $msgd = null;

        if (file_exists(PATH_CORE . "plugins" . PATH_SEP . "pmLicenseManager.php")) {
            $_SESSION["__EE_INSTALLATION__"] = 1;
            $_SESSION["__EE_SW_PMLICENSEMANAGER__"] = 0;

            $plugin = "pmLicenseManager";
            $this->pluginUninstall($plugin);

            if (file_exists(PATH_CORE . "plugins" . PATH_SEP . $plugin . ".php") || file_exists(PATH_CORE . "plugins" . PATH_SEP . $plugin)) {
                $msgf = $msgf . (($msgf != null)? ", " : null) . $plugin . ".php";
                $msgd = $msgd . (($msgd != null)? ", " : null) . $plugin;
                $sw = 0;
            }

            $plugin = "enterprise";
            $this->pluginUninstall($plugin);

            if (file_exists(PATH_CORE . "plugins" . PATH_SEP . $plugin . ".php") || file_exists(PATH_CORE . "plugins" . PATH_SEP . $plugin)) {
                $msgf = $msgf . (($msgf != null)? ", " : null) . $plugin . ".php";
                $msgd = $msgd . (($msgd != null)? ", " : null) . $plugin;
                $sw = 0;
            }

            $this->uninstall();
        } else {
            $_SESSION["__EE_INSTALLATION__"] = $_SESSION["__EE_INSTALLATION__"] + 1;
        }

        if ($sw == 0) {
            unset($_SESSION["__EE_INSTALLATION__"]);
            unset($_SESSION["__EE_SW_PMLICENSEMANAGER__"]);

            ///////
            $js = "window.open(\"/sys" . SYS_SYS . "/" . SYS_LANG . "/" . SYS_SKIN . "/setup/main?s=PLUGINS\", \"_top\", \"\");";

            if (substr(SYS_SKIN, 0, 2) == "ux" && SYS_SKIN != "uxs") {
                //$js = "parent.window.location.href = \"/sys" . SYS_SYS . "/" . SYS_LANG . "/" . SYS_SKIN . "/setup/main_init?s=PLUGINS\";";
                //$js = "window.location.href = \"/sys" . SYS_SYS . "/" . SYS_LANG . "/" . SYS_SKIN . "/setup/pluginsImport\";";
                $js = "window.open(\"/sys" . SYS_SYS . "/" . SYS_LANG . "/" . SYS_SKIN . "/main\", \"_top\", \"\");";
            }

            ///////
            G::SendMessageText("ProcessMaker Enterprise plug-in can't delete the files \"$msgf\" and directories \"$msgd\" of \"" . (PATH_CORE . "plugins") . "\". Before proceeding with the installation of the plug-in must remove them.", "INFO");

            echo "<script type=\"text/javascript\">" . $js . "</script>";
            exit(0);
        }

        if ($_SESSION["__EE_SW_PMLICENSEMANAGER__"] == 0 && $_SESSION["__EE_INSTALLATION__"] == 2) {
            unset($_SESSION["__EE_INSTALLATION__"]);
            unset($_SESSION["__EE_SW_PMLICENSEMANAGER__"]);

            $this->install();
        }

        ///////
        return $res;
    }

    public function install()
    {
        $pluginRegistry = PluginRegistry::loadSingleton();

        $pluginDetail = $pluginRegistry->getPluginDetails("enterprise.php");
        $pluginRegistry->enablePlugin($pluginDetail->getNamespace());

        file_put_contents(PATH_DATA_SITE . "plugin.singleton", $pluginRegistry->serializeInstance());
    }

    public function uninstall()
    {
    }

    public function setup()
    {
        if (!PluginsRegistryPeer::retrieveByPK(md5('enterprise'))) {
            $pluginRegistry = PluginRegistry::loadSingleton();
            $pluginDetail = $pluginRegistry->getPluginDetails("enterprise.php");
            $pluginRegistry->enablePlugin($pluginDetail->getNamespace());
            $pluginRegistry->savePlugin($pluginDetail->getNamespace());
        }
    }

    public function enable()
    {
        $this->setConfiguration();

        require_once (PATH_CORE . 'classes/model/AddonsStore.php');
        AddonsStore::checkLicenseStore();
        $licenseManager = &PmLicenseManager::getSingleton();
        AddonsStore::updateAll(false);
    }

    public function disable()
    {
    }

    public function setConfiguration()
    {
        $confEeUid = "enterpriseConfiguration";

        $criteria = new Criteria("workflow");

        $criteria->addSelectColumn(ConfigurationPeer::CFG_VALUE);
        $criteria->add(ConfigurationPeer::CFG_UID, "EE");
        $criteria->add(ConfigurationPeer::OBJ_UID, $confEeUid);

        $rsCriteria = ConfigurationPeer::doSelectRS($criteria);

        if (!$rsCriteria->next()) {
            $conf = new Configuration();

            $data = array("internetConnection" => 1);

            $conf->create(
                array(
                    "CFG_UID"   => "EE",
                    "OBJ_UID"   => $confEeUid,
                    "CFG_VALUE" => serialize($data),
                    "PRO_UID"   => "",
                    "USR_UID"   => "",
                    "APP_UID"   => ""
                )
            );
        }
    }

    public function pluginUninstall($pluginName)
    {
        //define("PATH_PLUGINS", PATH_CORE . "plugins" . PATH_SEP);

        if (file_exists(PATH_CORE . "plugins" . PATH_SEP . $pluginName . ".php")) {
            require_once (PATH_CORE . "plugins" . PATH_SEP . $pluginName . ".php");

            $pluginRegistry = PluginRegistry::loadSingleton();

            $pluginDetail = $pluginRegistry->getPluginDetails($pluginName . ".php");

            if ($pluginDetail) {
                $pluginRegistry->enablePlugin($pluginDetail->getNamespace());
                $pluginRegistry->disablePlugin($pluginDetail->getNamespace());

                ///////
                $className = $pluginDetail->getClassName();
                $plugin = new $className($pluginDetail->getNamespace(), $pluginDetail->getFile());
                //$this->_aPlugins[$pluginDetail->sNamespace] = $plugin;

                if (method_exists($plugin, "uninstall")) {
                    $plugin->uninstall();
                }

                ///////
                $pluginRegistry->savePlugin($pluginDetail->getNamespace());
            }

            ///////
            unlink(PATH_CORE . "plugins" . PATH_SEP . $pluginName . ".php");

            if (file_exists(PATH_CORE . "plugins" . PATH_SEP . $pluginName)) {
                G::rm_dir(PATH_CORE . "plugins" . PATH_SEP . $pluginName);
            }
        }
    }

    public function registerEE($pluginFile, $pluginVersion)
    {
        if (file_exists(PATH_DATA_SITE . "ee")) {
            $this->systemAvailable = unserialize(trim(file_get_contents(PATH_DATA_SITE . "ee")));
        }

        $this->systemAvailable[$pluginFile]["sFilename"] = $pluginFile . "-" . $pluginVersion . ".tar";
        file_put_contents(PATH_DATA_SITE . "ee", serialize($this->systemAvailable));

        return true;
    }

    public function checkDependencies()
    {
    }

    public function tableBackup($tableBackup, $backupPrefix = "_", $backupSuffix = "_TEMP")
    {
        //Database Connections
        $cnn = Propel::getConnection($this->database);
        $stmt = $cnn->createStatement();

        foreach ($tableBackup as $key => $table) {
            $tablebak = $backupPrefix . $table . $backupSuffix;

            //First Search if the Table exists
            $sqlTable = "SHOW TABLES LIKE '$table'";
            $rsTable = $stmt->executeQuery($sqlTable, ResultSet::FETCHMODE_ASSOC);
            if ($rsTable->getRecordCount() > 0) {
                //Table $table exists, so we can Backup
                //If there are records in $table Backup
                $sqlSelectTable = "SELECT * FROM $table";
                $rsSelectTable = $stmt->executeQuery($sqlSelectTable, ResultSet::FETCHMODE_ASSOC);
                if ($rsSelectTable->getRecordCount() > 0) {
                    //There are records in $table!! Backup!
                    //Delete a previous Backup if exists
                    $sql = "DROP TABLE IF EXISTS $tablebak;";
                    $rs = $stmt->executeQuery($sql, ResultSet::FETCHMODE_ASSOC);

                    //Create a COPY of $table in $tablebak :: Backup
                    $sql = "CREATE TABLE $tablebak SELECT * FROM $table";
                    $rs = $stmt->executeQuery($sql, ResultSet::FETCHMODE_ASSOC);

                    //Delete a previous $table if exists
                    $sql = "DROP TABLE IF EXISTS $table;";
                    $rs = $stmt->executeQuery($sql, ResultSet::FETCHMODE_ASSOC);
                }
            }
        }
    }

    public function tableBackupRestore($tableBackup, $backupPrefix = "_", $backupSuffix = "_TEMP")
    {
        //Database Connections
        $cnn = Propel::getConnection($this->database);
        $stmt = $cnn->createStatement();

        foreach ($tableBackup as $key => $table) {
            $tablebak = $backupPrefix . $table . $backupSuffix;

            //First Search if the $tablebak exists
            $sqlTablebak = "SHOW TABLES LIKE '$tablebak'";
            $rsTablebak = $stmt->executeQuery($sqlTablebak, ResultSet::FETCHMODE_ASSOC);
            if ($rsTablebak->getRecordCount() > 0) {
                //Table $tablebak exists, so we can Restore
                $sqlSelectTablebak = "SELECT * FROM $tablebak";
                $rsSelectTablebak = $stmt->executeQuery($sqlSelectTablebak, ResultSet::FETCHMODE_ASSOC);
                if ($rsSelectTablebak->getRecordCount() > 0) {
                    $strTable = str_replace("_", " ", strtolower($table));
                    $strTable = str_replace(" ", null, ucwords($strTable));

                    require_once (PATH_PLUGINS . "enterprise" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "$strTable.php");

                    while ($rsSelectTablebak->next()) {
                        $row = $rsSelectTablebak->getRow();

                        //INSERT INTO TABLEN(FIELD1, FIELD2) VALUES('VALUE1', 'VALUE2')
                        $oTable = new $strTable();
                        $oTable->fromArray($row, BasePeer::TYPE_FIELDNAME); //Fill an object from of the array //Fill attributes
                        $oTable->save();
                    }
                }

                //Delete Backup
                $sql = "DROP TABLE IF EXISTS $tablebak;";
                $rs = $stmt->executeQuery($sql, ResultSet::FETCHMODE_ASSOC);
            }
        }
    }

    public function sqlExecute($sqlFile)
    {
        $file = fopen($sqlFile, "r");

        if ($file) {
            $line = null;

            while (!feof($file)) {
                $buffer = trim(fgets($file, 4096)); //Read a line.

                if (strlen($buffer) > 0 && $buffer[0] != "#") {
                    //Check for valid lines
                    $line = $line . $buffer;

                    if ($buffer[strlen($buffer) - 1] == ";") {
                        $cnn = Propel::getConnection($this->database);
                        $stmt = $cnn->createStatement();
                        $rs = $stmt->executeQuery($line, ResultSet::FETCHMODE_NUM);
                        $line = null;
                    }
                }
            }

            fclose($file);
        }
    }
}

$oPluginRegistry = PluginRegistry::loadSingleton();
$oPluginRegistry->registerPlugin('enterprise', __FILE__); //<- enterprise string must be in single quote, otherwise generate error

//since we are placing pmLicenseManager and EE together.. after register EE, we need to require_once the pmLicenseManager
//if( !defined("PATH_PM_LICENSE_MANAGER") ) {
//  define("PATH_PM_LICENSE_MANAGER", PATH_CORE . "/plugins/pmLicenseManager/");
//}
//set_include_path(
//  PATH_PM_LICENSE_MANAGER.PATH_SEPARATOR.
//  get_include_path()
//);

