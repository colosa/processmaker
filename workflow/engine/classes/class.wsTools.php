<?php

/**
 * Utility functions to manage a workspace.
 *
 * @author Alexandre Rosenfeld
 */
G::LoadSystem('dbMaintenance');
G::LoadClass("cli");
G::LoadClass("multipleFilesBackup");

/**
 * class workspaceTools
 *
 * @package workflow.engine.classes
 */
class workspaceTools
{
    public $name = null;
    public $path = null;
    public $db = null;
    public $dbPath = null;
    public $dbInfo = null;
    public $dbInfoRegExp = "/( *define *\( *'(?P<key>.*?)' *, *\n* *')(?P<value>.*?)(' *\) *;.*)/";
    public $initPropel = false;
    public $initPropelRoot = false;

    /**
     * Create a workspace tools object.
     * Note that workspace might not exist when
     * this object is created, however most methods requires that a workspace with
     * this name does exists.
     *
     * @author Alexandre Rosenfeld <alexandre@colosa.com>
     * @access public
     * @param string $workspaceName name of the workspace
     */
    public function __construct($workspaceName)
    {
        $this->name = $workspaceName;
        $this->path = PATH_DB . $this->name;
        $this->dbPath = $this->path . '/db.php';
        if ($this->workspaceExists()) {
            $this->getDBInfo();
        }
    }

    /**
     * Returns true if the workspace already exists
     *
     * @return bool
     */
    public function workspaceExists()
    {
        return (file_exists($this->path) && file_exists($this->dbPath));
    }

    /**
     * Upgrade this workspace to the latest system version
     *
     * @param bool $first true if this is the first workspace to be upgrade
     */
    public function upgrade($first = false, $buildCacheView = false, $workSpace = SYS_SYS)
    {
        $start = microtime(true);
        CLI::logging("> Updating database...\n");
        $this->upgradeDatabase();
        $stop = microtime(true);
        $final = $stop - $start;
        CLI::logging("<*>   Process Updating database carried out in $final seconds.\n");

        $start = microtime(true);
        CLI::logging("> Updating translations...\n");
        $this->upgradeTranslation($first);
        $stop = microtime(true);
        $final = $stop - $start;
        CLI::logging("<*>   Process Updating translations carried out in $final seconds.\n");

        $start = microtime(true);
        CLI::logging("> Updating Content...\n");
        $this->upgradeContent($workSpace);
        $stop = microtime(true);
        $final = $stop - $start;
        CLI::logging("<*>   Process Updating Content carried out in $final seconds.\n");

        $start = microtime(true);
        CLI::logging("> Updating cache view...\n");
        $this->upgradeCacheView($buildCacheView, true);
        $stop = microtime(true);
        $final = $stop - $start;
        CLI::logging("<*>   Process Updating cache view carried out in $final seconds.\n");
    }

    /**
     * Scan the db.php file for database information and return it as an array
     *
     * @return array with database information
     */
    public function getDBInfo()
    {
        if (!$this->workspaceExists()) {
            throw new Exception("Could not get db.php in workspace " . $this->name);
        }
        if (isset($this->dbInfo)) {
            return $this->dbInfo;
        }
        $sDbFile = file_get_contents($this->dbPath);
        /* This regular expression will match any "define ('<key>', '<value>');"
         * with any combination of whitespace between words.
         * Each match will have these groups:
         * ((define('(<key>)2', ')1 (<value>)3 (');)4 )0
         */
        preg_match_all($this->dbInfoRegExp, $sDbFile, $matches, PREG_SET_ORDER);
        $values = array();
        foreach ($matches as $match) {
            $values[$match['key']] = $match['value'];
        }
        $this->dbAdapter = $values["DB_ADAPTER"];
        $this->dbName = $values["DB_NAME"];
        $this->dbHost = $values["DB_HOST"];
        $this->dbUser = $values["DB_USER"];
        $this->dbPass = $values["DB_PASS"];
        return $this->dbInfo = $values;
    }

    private function resetDBInfoCallback($matches)
    {
        /* This function changes the values of defines while keeping their formatting
         * intact.
         * $matches will contain several groups:
         * ((define('(<key>)2', ')1 (<value>)3 (');)4 )0
         */
        $dbPrefix = array('DB_NAME' => 'wf_', 'DB_USER' => 'wf_', 'DB_RBAC_NAME' => 'rb_', 'DB_RBAC_USER' => 'rb_', 'DB_REPORT_NAME' => 'rp_', 'DB_REPORT_USER' => 'rp_');
        $key = $matches['key'];
        $value = $matches['value'];
        if (array_search($key, array('DB_HOST', 'DB_RBAC_HOST', 'DB_REPORT_HOST')) !== false) {
            /* Change the database hostname for these keys */
            $value = $this->newHost;
        } elseif (array_key_exists($key, $dbPrefix)) {
            if ($this->resetDBNames) {
                /* Change the database name to the new workspace, following the standard
                 * of prefix (either wf_, rp_, rb_) and the workspace name.
                 */
                $dbName = $dbPrefix[$key] . $this->name;
            } else {
                $dbName = $value;
            }
            $this->resetDBDiff[$value] = $dbName;
            $value = $dbName;
        }
        return $matches[1] . $value . $matches[4];
    }

    /**
     * Reset the database information to that of a newly created workspace.
     *
     * This assumes this workspace already has a db.php file, which will be changed
     * to contain the new information.
     * This function will reset the database hostname to the system database.
     * If reseting database names, it will also use the the prefixes rp_,
     * rb_ and wf_, with the workspace name as database names.
     *
     * @param string $newHost the new hostname for the database
     * @param bool $resetDBNames if true, also reset all database names
     * @return array contains the new database names as values
     */
    public function resetDBInfo($newHost, $resetDBNames = true)
    {
        if (count(explode(":", $newHost)) < 2) {
            $newHost .= ':3306';
        }
        $this->newHost = $newHost;
        $this->resetDBNames = $resetDBNames;
        $this->resetDBDiff = array();

        if (!$this->workspaceExists()) {
            throw new Exception("Could not find db.php in the workspace");
        }
        $sDbFile = file_get_contents($this->dbPath);
        if ($sDbFile === false) {
            throw new Exception("Could not read database information from db.php");
        }
        /* Match all defines in the config file. Check updateDBCallback to know what
         * keys are changed and what groups are matched.
         * This regular expression will match any "define ('<key>', '<value>');"
         * with any combination of whitespace between words.
         */
        $sNewDbFile = preg_replace_callback("/( *define *\( *'(?P<key>.*?)' *, *\n* *')(?P<value>.*?)(' *\) *;.*)/", array(&$this, 'resetDBInfoCallback'), $sDbFile);
        if (file_put_contents($this->dbPath, $sNewDbFile) === false) {
            throw new Exception("Could not write database information to db.php");
        }
        $newDBNames = $this->resetDBDiff;
        unset($this->resetDBDiff);
        unset($this->resetDBNames);
        //Clear the cached information about db.php
        unset($this->dbInfo);
        return $newDBNames;
    }

    /**
     * Get DB information for this workspace, such as hostname, username and password.
     *
     * @param string $dbName a db name, such as wf, rp and rb
     * @return array with all the database information.
     */
    public function getDBCredentials($dbName)
    {
        $prefixes = array("wf" => "", "rp" => "REPORT_", "rb" => "RBAC_");
        $prefix = $prefixes[$dbName];
        $dbInfo = $this->getDBInfo();
        return array('adapter' => $dbInfo["DB_ADAPTER"], 'name' => $dbInfo["DB_" . $prefix . "NAME"], 'host' => $dbInfo["DB_" . $prefix . "HOST"], 'user' => $dbInfo["DB_" . $prefix . "USER"], 'pass' => $dbInfo["DB_" . $prefix . "PASS"], 'dsn' => sprintf("%s://%s:%s@%s/%s?encoding=utf8", $dbInfo['DB_ADAPTER'], $dbInfo["DB_" . $prefix . "USER"], $dbInfo["DB_" . $prefix . "PASS"], $dbInfo["DB_" . $prefix . "HOST"], $dbInfo["DB_" . $prefix . "NAME"]));
    }

    /**
     * Initialize a Propel connection to the database
     *
     * @param bool $root wheter to also initialize a root connection
     * @return the Propel connection
     */
    public function initPropel($root = false)
    {
        if (($this->initPropel && !$root) || ($this->initPropelRoot && $root)) {
            return;
        }
        $wfDetails = $this->getDBCredentials("wf");
        $rbDetails = $this->getDBCredentials("rb");
        $rpDetails = $this->getDBCredentials("rp");

        $config = array('datasources' => array('workflow' => array('connection' => $wfDetails["dsn"], 'adapter' => $wfDetails["adapter"]
                ), 'rbac' => array('connection' => $rbDetails["dsn"], 'adapter' => $rbDetails["adapter"]
                ), 'rp' => array('connection' => $rpDetails["dsn"], 'adapter' => $rpDetails["adapter"]
                )
            )
        );

        if ($root) {
            $dbHash = @explode(SYSTEM_HASH, G::decrypt(HASH_INSTALLATION, SYSTEM_HASH));

            $dbInfo = $this->getDBInfo();
            $host = $dbHash[0];
            $user = $dbHash[1];
            $pass = $dbHash[2];
            $dbName = $dbInfo["DB_NAME"];

            $rootConfig = array(
                'datasources' => array('root' => array('connection' => "mysql://$user:$pass@$host/$dbName?encoding=utf8", 'adapter' => "mysql"))
            );

            $config["datasources"] = array_merge($config["datasources"], $rootConfig["datasources"]);

            $this->initPropelRoot = true;
        }

        $this->initPropel = true;

        require_once ("propel/Propel.php");
        require_once ("creole/Creole.php");

        Propel::initConfiguration($config);
    }

    /**
     * Close the propel connection from initPropel
     */
    private function closePropel()
    {
        Propel::close();
        $this->initPropel = false;
        $this->initPropelRoot = false;
    }

    /**
     * Upgrade this workspace Content.
     */
    public function upgradeContent($workSpace = SYS_SYS)
    {
        $this->initPropel(true);
        //require_once 'classes/model/Translation.php';
        $translation = new Translation();
        $information = $translation->getTranslationEnvironments();
        $arrayLang = array();
        foreach ($information as $key => $value) {
            $arrayLang[] = trim($value['LOCALE']);
        }
        //require_once ('classes/model/Content.php');
        $regenerateContent = new Content();
        $regenerateContent->regenerateContent($arrayLang, $workSpace);
    }

    /**
     * Upgrade this workspace translations from all avaliable languages.
     *
     * @param bool $first if updating a series of workspace, true if the first
     */
    public function upgradeTranslation($first = true)
    {
        $this->initPropel(true);
        //require_once ('classes/model/Language.php');
        G::LoadThirdParty('pear/json', 'class.json');
        foreach (System::listPoFiles() as $poFile) {
            $poName = basename($poFile);
            $names = explode(".", basename($poFile));
            $extension = array_pop($names);
            $langid = array_pop($names);
            if (strcasecmp($langid, "en") == 0) {
                CLI::logging("Updating database translations with $poName\n");
                Language::import($poFile, false, true);
            } elseif ($first) {
                CLI::logging("Updating XML form translations with $poName\n");
                Language::import($poFile, true, false);
                CLI::logging("Updating database translations with $poName\n");
                Language::import($poFile, false, true);
            }
        }
    }

    /**
     * Get a connection to this workspace wf database
     *
     * @return database connection
     */
    private function getDatabase()
    {
        if (isset($this->db) && $this->db->isConnected()) {
            return $this->db;
        }
        G::LoadSystem('database_' . strtolower($this->dbAdapter));
        $this->db = new database($this->dbAdapter, $this->dbHost, $this->dbUser, $this->dbPass, $this->dbName);
        if (!$this->db->isConnected()) {
            $this->db->logQuery('No available connection to database!');
            throw new Exception("Could not connect to database");
        }
        return $this->db;
    }

    /**
     * Close any database opened with getDatabase
     */
    private function closeDatabase()
    {
        if (!isset($this->db)) {
            return;
        }
        $this->db->close();
        $this->db = null;
    }

    /**
     * Close all currently opened databases
     */
    public function close()
    {
        $this->closePropel();
        $this->closeDatabase();
    }

    /**
     * Get the current workspace database schema
     *
     * @return array with the database schema
     */
    public function getSchema()
    {
        $oDataBase = $this->getDatabase();

        $aOldSchema = array();

        try {
            $oDataBase->iFetchType = MYSQL_NUM;
            $oDataset1 = $oDataBase->executeQuery($oDataBase->generateShowTablesSQL());
        } catch (Exception $e) {
            $oDataBase->logQuery($e->getmessage());
            return null;
        }

        //going thru all tables in current WF_ database
        while ($aRow1 = $oDataBase->getRegistry($oDataset1)) {
            $aPrimaryKeys = array();
            $sTable = strtoupper($aRow1[0]);

            //get description of each table, ( column and primary keys )
            //$oDataset2 = $oDataBase->executeQuery( $oDataBase->generateDescTableSQL($aRow1[0]) );
            $oDataset2 = $oDataBase->executeQuery($oDataBase->generateDescTableSQL($sTable));
            $aOldSchema[$sTable] = array();
            $oDataBase->iFetchType = MYSQL_ASSOC;
            while ($aRow2 = $oDataBase->getRegistry($oDataset2)) {
                $aOldSchema[$sTable][$aRow2['Field']]['Field'] = $aRow2['Field'];
                $aOldSchema[$sTable][$aRow2['Field']]['Type'] = $aRow2['Type'];
                $aOldSchema[$sTable][$aRow2['Field']]['Null'] = $aRow2['Null'];
                $aOldSchema[$sTable][$aRow2['Field']]['Default'] = $aRow2['Default'];
            }

            //get indexes of each table  SHOW INDEX FROM `ADDITIONAL_TABLES`;   -- WHERE Key_name <> 'PRIMARY'
            $oDataset2 = $oDataBase->executeQuery($oDataBase->generateTableIndexSQL($aRow1[0]));
            $oDataBase->iFetchType = MYSQL_ASSOC;
            while ($aRow2 = $oDataBase->getRegistry($oDataset2)) {
                if (!isset($aOldSchema[$sTable]['INDEXES'])) {
                    $aOldSchema[$sTable]['INDEXES'] = array();
                }
                if (!isset($aOldSchema[$sTable]['INDEXES'][$aRow2['Key_name']])) {
                    $aOldSchema[$sTable]['INDEXES'][$aRow2['Key_name']] = array();
                }
                $aOldSchema[$sTable]['INDEXES'][$aRow2['Key_name']][] = $aRow2['Column_name'];
            }

            $oDataBase->iFetchType = MYSQL_NUM; //this line is neccesary because the next fetch needs to be with MYSQL_NUM
        }
        //finally return the array with old schema obtained from the Database
        if (count($aOldSchema) == 0) {
            $aOldSchema = null;
        }
        return $aOldSchema;
    }

    /**
     * Upgrade the AppCacheView table to the latest system version.
     *
     * This recreates the table and populates with data.
     *
     * @param bool $checkOnly only check if the upgrade is needed if true
     * @param string $lang not currently used
     */
    public function upgradeCacheView($fill = true, $checkOnly = false)
    {
        $this->initPropel(true);

        $lang = "en";

        //require_once ('classes/model/AppCacheView.php');
        //check the language, if no info in config about language, the default is 'en'
        G::LoadClass("configuration");

        $oConf = new Configurations();
        $oConf->loadConfig($x, 'APP_CACHE_VIEW_ENGINE', '', '', '', '');
        $appCacheViewEngine = $oConf->aConfig;

        //setup the appcacheview object, and the path for the sql files
        $appCache = new AppCacheView();
        $appCache->setPathToAppCacheFiles(PATH_METHODS . 'setup' . PATH_SEP . 'setupSchemas' . PATH_SEP);

        $userGrants = $appCache->checkGrantsForUser(false);

        $currentUser = $userGrants['user'];
        $currentUserIsSuper = $userGrants['super'];

        //if user does not have the SUPER privilege we need to use the root user and grant the SUPER priv. to normal user.
        if (!$currentUserIsSuper) {
            $appCache->checkGrantsForUser(true);
            $appCache->setSuperForUser($currentUser);
            $currentUserIsSuper = true;
        }

        CLI::logging("-> Creating table\n");
        //now check if table APPCACHEVIEW exists, and it have correct number of fields, etc.
        $res = $appCache->checkAppCacheView();

        CLI::logging("-> Update DEL_LAST_INDEX field in APP_DELEGATION table\n");
        //Update APP_DELEGATION.DEL_LAST_INDEX data
        $res = $appCache->updateAppDelegationDelLastIndex($lang, $checkOnly);

        CLI::logging("-> Creating triggers\n");
        //now check if we have the triggers installed
        $triggers = array();
        $triggers[] = $appCache->triggerAppDelegationInsert($lang, $checkOnly);
        $triggers[] = $appCache->triggerAppDelegationUpdate($lang, $checkOnly);
        $triggers[] = $appCache->triggerApplicationUpdate($lang, $checkOnly);
        $triggers[] = $appCache->triggerApplicationDelete($lang, $checkOnly);
        $triggers[] = $appCache->triggerContentUpdate($lang, $checkOnly);

        if ($fill) {
            CLI::logging("-> Rebuild Cache View\n");
            //build using the method in AppCacheView Class
            $res = $appCache->fillAppCacheView($lang);
            //set status in config table
            $confParams = Array('LANG' => $lang, 'STATUS' => 'active');
        }
        $oConf->aConfig = $confParams;
        $oConf->saveConfig('APP_CACHE_VIEW_ENGINE', '', '', '');

        // removing casesList configuration records. TODO: removing these lines that resets all the configurations records
        $oCriteria = new Criteria();
        $oCriteria->add(ConfigurationPeer::CFG_UID, "casesList");
        $oCriteria->add(ConfigurationPeer::OBJ_UID, array("todo", "draft", "sent", "unassigned", "paused", "cancelled"), Criteria::NOT_IN);
        ConfigurationPeer::doDelete($oCriteria);
        // end of reset
    }

    /**
     * Upgrade this workspace database to the latest plugins schema
     */
    public function upgradePluginsDatabase()
    {
        foreach (System::getPlugins() as $pluginName) {
            $pluginSchema = System::getPluginSchema($pluginName);
            if ($pluginSchema !== false) {
                CLI::logging("Updating plugin " . CLI::info($pluginName) . "\n");
                $this->upgradeSchema($pluginSchema);
            }
        }
    }

    /**
     * Upgrade this workspace database to the latest system schema
     *
     * @param bool $checkOnly only check if the upgrade is needed if true
     * @return array bool upgradeSchema for more information
     */
    public function upgradeDatabase($checkOnly = false)
    {
        $systemSchema = System::getSystemSchema();
        $this->upgradeSchema($systemSchema);
        $this->upgradeData();
        return true;
    }

    /**
     * Upgrade this workspace database from a schema
     *
     * @param array $schema the schema information, such as returned from getSystemSchema
     * @param bool $checkOnly only check if the upgrade is needed if true
     * @return array bool the changes if checkOnly is true, else return
     * true on success
     */
    public function upgradeSchema($schema, $checkOnly = false)
    {
        $dbInfo = $this->getDBInfo();

        if (strcmp($dbInfo["DB_ADAPTER"], "mysql") != 0) {
            throw new Exception("Only MySQL is supported");
        }

        $workspaceSchema = $this->getSchema();
        $changes = System::compareSchema($workspaceSchema, $schema);
        $changed = (count($changes['tablesToAdd']) > 0 || count($changes['tablesToAlter']) > 0 || count($changes['tablesWithNewIndex']) > 0 || count($changes['tablesToAlterIndex']) > 0);
        if ($checkOnly || (!$changed)) {
            if ($changed) {
                return $changes;
            } else {
                CLI::logging("-> Nothing to change in the data base structure\n");
                return $changed;
            }
        }

        $oDataBase = $this->getDatabase();
        $oDataBase->iFetchType = MYSQL_NUM;

        $oDataBase->logQuery(count($changes));

        if (!empty($changes['tablesToAdd'])) {
            CLI::logging("-> " . count($changes['tablesToAdd']) . " tables to add\n");
        }
        foreach ($changes['tablesToAdd'] as $sTable => $aColumns) {
            $oDataBase->executeQuery($oDataBase->generateCreateTableSQL($sTable, $aColumns));
            if (isset($changes['tablesToAdd'][$sTable]['INDEXES'])) {
                foreach ($changes['tablesToAdd'][$sTable]['INDEXES'] as $indexName => $aIndex) {
                    $oDataBase->executeQuery($oDataBase->generateAddKeysSQL($sTable, $indexName, $aIndex));
                }
            }
        }

        if (!empty($changes['tablesToAlter'])) {
            CLI::logging("-> " . count($changes['tablesToAlter']) . " tables to alter\n");
        }
        foreach ($changes['tablesToAlter'] as $sTable => $aActions) {
            foreach ($aActions as $sAction => $aAction) {
                foreach ($aAction as $sColumn => $vData) {
                    switch ($sAction) {
                        case 'DROP':
                            $oDataBase->executeQuery($oDataBase->generateDropColumnSQL($sTable, $vData));
                            break;
                        case 'ADD':
                            $oDataBase->executeQuery($oDataBase->generateAddColumnSQL($sTable, $sColumn, $vData));
                            break;
                        case 'CHANGE':
                            $oDataBase->executeQuery($oDataBase->generateChangeColumnSQL($sTable, $sColumn, $vData));
                            break;
                    }
                }
            }
        }

        if (!empty($changes['tablesWithNewIndex'])) {
            CLI::logging("-> " . count($changes['tablesWithNewIndex']) . " indexes to add\n");
        }
        foreach ($changes['tablesWithNewIndex'] as $sTable => $aIndexes) {
            foreach ($aIndexes as $sIndexName => $aIndexFields) {
                $oDataBase->executeQuery($oDataBase->generateAddKeysSQL($sTable, $sIndexName, $aIndexFields));
            }
        }

        if (!empty($changes['tablesToAlterIndex'])) {
            CLI::logging("-> " . count($changes['tablesToAlterIndex']) . " indexes to alter\n");
        }
        foreach ($changes['tablesToAlterIndex'] as $sTable => $aIndexes) {
            foreach ($aIndexes as $sIndexName => $aIndexFields) {
                $oDataBase->executeQuery($oDataBase->generateDropKeySQL($sTable, $sIndexName));
                $oDataBase->executeQuery($oDataBase->generateAddKeysSQL($sTable, $sIndexName, $aIndexFields));
            }
        }
        $this->closeDatabase();
        return true;
    }

    public function upgradeData()
    {
        if (file_exists(PATH_CORE . 'data' . PATH_SEP . 'check.data')) {
            $checkData = unserialize(file_get_contents(PATH_CORE . 'data' . PATH_SEP . 'check.data'));
            if (is_array($checkData)) {
                foreach ($checkData as $checkThis) {
                    $this->updateThisRegistry($checkThis);
                }
            }
        }
    }

    public function updateThisRegistry($data)
    {
        $dataBase = $this->getDatabase();
        $sql = '';
        switch ($data['action']) {
            case 1:
                $sql = $dataBase->generateInsertSQL($data['table'], $data['data']);
                $message = "-> Row added in {$data['table']}\n";
                break;
            case 2:
                $sql = $dataBase->generateUpdateSQL($data['table'], $data['keys'], $data['data']);
                $message = "-> Row updated in {$data['table']}\n";
                break;
            case 3:
                $sql = $dataBase->generateDeleteSQL($data['table'], $data['keys'], $data['data']);
                $message = "-> Row deleted in {$data['table']}\n";
                break;
            case 4:
                $sql = $dataBase->generateSelectSQL($data['table'], $data['keys'], $data['data']);
                $dataset = $dataBase->executeQuery($sql);
                if ($dataBase->getRegistry($dataset)) {
                    $sql = $dataBase->generateDeleteSQL($data['table'], $data['keys'], $data['data']);
                    $dataBase->executeQuery($sql);
                }
                $sql = $dataBase->generateInsertSQL($data['table'], $data['data']);
                $message = "-> Row updated in {$data['table']}\n";
                break;
        }
        if ($sql != '') {
            $dataBase->executeQuery($sql);
            CLI::logging($message);
        }
    }

    /**
     * Get metadata from this workspace
     *
     * @param string $path the directory where to create the sql files
     * @return array information about this workspace
     */
    public function getMetadata()
    {
        $Fields = array_merge(System::getSysInfo(), $this->getDBInfo());
        $Fields['WORKSPACE_NAME'] = $this->name;

        if (isset($this->dbHost)) {

            //TODO: This code stopped working with the refactoring
            //require_once ("propel/Propel.php");
            //G::LoadClass('dbConnections');
            //$dbConns = new dbConnections('');
            //$availdb = '';
            //foreach( $dbConns->getDbServicesAvailables() as $key => $val ) {
            //if(!empty($availdb))
            //  $availdb .= ', ';
            //  $availdb .= $val['name'];
            //}


            G::LoadClass('net');
            $dbNetView = new NET($this->dbHost);
            $dbNetView->loginDbServer($this->dbUser, $this->dbPass);
            try {
                $sMySQLVersion = $dbNetView->getDbServerVersion('mysql');
            } catch (Exception $oException) {
                $sMySQLVersion = 'Unknown';
            }

            $Fields['DATABASE'] = $dbNetView->dbName($this->dbAdapter) . ' (Version ' . $sMySQLVersion . ')';
            $Fields['DATABASE_SERVER'] = $this->dbHost;
            $Fields['DATABASE_NAME'] = $this->dbName;
            $Fields['AVAILABLE_DB'] = "Not defined";
            //$Fields['AVAILABLE_DB'] = $availdb;
        } else {
            $Fields['DATABASE'] = "Not defined";
            $Fields['DATABASE_SERVER'] = "Not defined";
            $Fields['DATABASE_NAME'] = "Not defined";
            $Fields['AVAILABLE_DB'] = "Not defined";
        }

        return $Fields;
    }

    /**
     * Print the system information gathered from getSysInfo
     */
    public static function printSysInfo()
    {
        $fields = System::getSysInfo();

        $info = array(
            'ProcessMaker Version' => $fields['PM_VERSION'],
            'System' => $fields['SYSTEM'],
            'PHP Version' => $fields['PHP'],
            'Server Address' => $fields['SERVER_ADDR'],
            'Client IP Address' => $fields['IP'],
            'Plugins' => (count($fields['PLUGINS_LIST']) > 0) ? $fields['PLUGINS_LIST'][0] : 'None'
        );

        foreach ($fields['PLUGINS_LIST'] as $k => $v) {
            if ($k == 0) {
                continue;
            }
            $info[] = $v;
        }

        foreach ($info as $k => $v) {
            if (is_numeric($k)) {
                $k = "";
            }
            CLI::logging(sprintf("%20s %s\n", $k, pakeColor::colorize($v, 'INFO')));
        }
    }

    public function printInfo($fields = null)
    {
        if (!$fields) {
            $fields = $this->getMetadata();
        }

        $wfDsn = $fields['DB_ADAPTER'] . '://' . $fields['DB_USER'] . ':' . $fields['DB_PASS'] . '@' . $fields['DB_HOST'] . '/' . $fields['DB_NAME'];
        $rbDsn = $fields['DB_ADAPTER'] . '://' . $fields['DB_RBAC_USER'] . ':' . $fields['DB_RBAC_PASS'] . '@' . $fields['DB_RBAC_HOST'] . '/' . $fields['DB_RBAC_NAME'];
        $rpDsn = $fields['DB_ADAPTER'] . '://' . $fields['DB_REPORT_USER'] . ':' . $fields['DB_REPORT_PASS'] . '@' . $fields['DB_REPORT_HOST'] . '/' . $fields['DB_REPORT_NAME'];

        $info = array('Workspace Name' => $fields['WORKSPACE_NAME'],
            //'Available Databases'  => $fields['AVAILABLE_DB'],
            'Workflow Database' => sprintf("%s://%s:%s@%s/%s", $fields['DB_ADAPTER'], $fields['DB_USER'], $fields['DB_PASS'], $fields['DB_HOST'], $fields['DB_NAME']), 'RBAC Database' => sprintf("%s://%s:%s@%s/%s", $fields['DB_ADAPTER'], $fields['DB_RBAC_USER'], $fields['DB_RBAC_PASS'], $fields['DB_RBAC_HOST'], $fields['DB_RBAC_NAME']), 'Report Database' => sprintf("%s://%s:%s@%s/%s", $fields['DB_ADAPTER'], $fields['DB_REPORT_USER'], $fields['DB_REPORT_PASS'], $fields['DB_REPORT_HOST'], $fields['DB_REPORT_NAME']), 'MySql Version' => $fields['DATABASE']
        );

        foreach ($info as $k => $v) {
            if (is_numeric($k)) {
                $k = "";
            }
            CLI::logging(sprintf("%20s %s\n", $k, pakeColor::colorize($v, 'INFO')));
        }
    }

    /**
     * Print workspace information
     *
     * @param bool $printSysInfo include sys info as well or not
     */
    public function printMetadata($printSysInfo = true)
    {
        if ($printSysInfo) {
            workspaceTools::printSysInfo();
            CLI::logging("\n");
        }

        workspaceTools::printInfo($this->getMetadata());
    }

    /**
     * exports this workspace database to the specified path
     *
     * This function is used mainly for backup purposes.
     *
     * @param string $path the directory where to create the sql files
     */
    public function exportDatabase($path)
    {
        $dbInfo = $this->getDBInfo();
        $databases = array("wf", "rp", "rb");
        $dbNames = array();
        foreach ($databases as $db) {
            $dbInfo = $this->getDBCredentials($db);
            $oDbMaintainer = new DataBaseMaintenance($dbInfo["host"], $dbInfo["user"], $dbInfo["pass"]);
            CLI::logging("Saving database {$dbInfo["name"]}\n");
            $oDbMaintainer->connect($dbInfo["name"]);
            $oDbMaintainer->lockTables();
            $oDbMaintainer->setTempDir($path . "/");
            $oDbMaintainer->backupDataBase($oDbMaintainer->getTempDir() . $dbInfo["name"] . ".sql");
            $oDbMaintainer->unlockTables();
            $dbNames[] = $dbInfo;
        }
        return $dbNames;
    }

    /**
     * adds files to the backup archive
     */
    private function addToBackup($backup, $filename, $pathRoot, $archiveRoot = "")
    {
        if (is_file($filename)) {
            CLI::logging("-> $filename\n");
            $backup->addModify($filename, $archiveRoot, $pathRoot);
        } else {
            CLI::logging(" + $filename\n");
            $backup->addModify($filename, $archiveRoot, $pathRoot);
            //foreach (glob($filename . "/*") as $item) {
            //  $this->addToBackup($backup, $item, $pathRoot, $archiveRoot);
            //}
        }
    }

    /**
     * Creates a backup archive, which can be used instead of a filename to backup
     *
     * @param string $filename the backup filename
     * @param bool $compress wheter to compress or not
     */
    static public function createBackup($filename, $compress = true)
    {
        G::LoadThirdParty('pear/Archive', 'Tar');
        if (!file_exists(dirname($filename))) {
            mkdir(dirname($filename));
        }
        if (file_exists($filename)) {
            unlink($filename);
        }
        $backup = new Archive_Tar($filename);
        return $backup;
    }

    /**
     * create a backup of this workspace
     *
     * Exports the database and copies the files to an archive specified, so this
     * workspace can later be restored.
     *
     * @param string|archive $filename archive filename to use as backup or
     * archive object created by createBackup
     * @param bool $compress specifies wheter the backup is compressed or not
     */
    public function backup($backupFile, $compress = true)
    {
        /* $filename can be a string, in which case it's used as the filename of
         * the backup, or it can be a previously created tar, which allows for
         * multiple workspaces in one backup.
         */
        if (!$this->workspaceExists()) {
            throw new Exception("Workspace '{$this->name}' not found");
        }
        if (is_string($backupFile)) {
            $backup = $this->createBackup($backupFile);
            $filename = $backupFile;
        } else {
            $backup = $backupFile;
            $filename = $backup->_tarname;
        }
        if (!file_exists(PATH_DATA . "upgrade/")) {
            mkdir(PATH_DATA . "upgrade/");
        }
        $tempDirectory = PATH_DATA . "upgrade/" . basename(tempnam(__FILE__, ''));
        mkdir($tempDirectory);
        $metadata = $this->getMetadata();
        CLI::logging("Backing up database...\n");
        $metadata["databases"] = $this->exportDatabase($tempDirectory);
        $metadata["directories"] = array("{$this->name}.files");
        $metadata["version"] = 1;
        $metaFilename = "$tempDirectory/{$this->name}.meta";
        /* Write metadata to file, but make it prettier before. The metadata is just
         * a JSON codified array.
         */
        if (!file_put_contents($metaFilename, str_replace(array(",", "{", "}"
                                ), array(",\n  ", "{\n  ", "\n}\n"
                                ), G::json_encode($metadata)))) {
            throw new Exception("Could not create backup metadata");
        }
        CLI::logging("Copying database to backup...\n");
        $this->addToBackup($backup, $tempDirectory, $tempDirectory);
        CLI::logging("Copying files to backup...\n");

        $this->addToBackup($backup, $this->path, $this->path, "{$this->name}.files");
        //Remove leftovers.
        G::rm_dir($tempDirectory);
    }

    //TODO: Move to class.dbMaintenance.php
    /**
     * create a user in the database
     *
     * Create a user specified by the parameters and grant all priviledges for
     * the database specified, when the user connects from the hostname.
     * Drops the user if it already exists.
     * This function only supports MySQL.
     *
     * @param string $username username
     * @param string $password password
     * @param string $hostname the hostname the user will be connecting from
     * @param string $database the database to grant permissions
     */
    private function createDBUser($username, $password, $hostname, $database)
    {
        mysql_select_db("mysql");
        $hostname = array_shift(explode(":", $hostname));
        $sqlstmt = "SELECT * FROM user WHERE user = '$username' AND host = '$hostname'";
        $result = mysql_query($sqlstmt);
        if ($result === false) {
            throw new Exception("Unable to retrieve users: " . mysql_error());
        }
        $users = mysql_num_rows($result);
        if ($users != 0) {
            $result = mysql_query("DROP USER '$username'@'$hostname'");
            if ($result === false) {
                throw new Exception("Unable to drop user: " . mysql_error());
            }
        }
        CLI::logging("Creating user $username for $hostname\n");
        $result = mysql_query("CREATE USER '$username'@'$hostname' IDENTIFIED BY '$password'");
        if ($result === false) {
            throw new Exception("Unable to create user $username: " . mysql_error());
        }
        $result = mysql_query("GRANT ALL ON $database.* TO '$username'@'$hostname'");
        if ($result === false) {
            throw new Exception("Unable to grant priviledges to user $username: " . mysql_error());
        }
    }

    //TODO: Move to class.dbMaintenance.php
    /**
     * executes a mysql script
     *
     * This function supports scripts with -- comments in the beginning of a line
     * and multi-line statements.
     * It does not support other forms of comments (such as /*... or {{...}}).
     *
     * @param string $filename the script filename
     * @param string $database the database to execute this script into
     */
    private function executeSQLScript($database, $filename)
    {
        mysql_query("CREATE DATABASE IF NOT EXISTS " . mysql_real_escape_string($database));
        mysql_select_db($database);
        $script = file_get_contents($filename);
        $lines = explode("\n", $script);
        $previous = null;
        foreach ($lines as $j => $line) {
            // Remove comments from the script
            $line = trim($line);
            if (strpos($line, "--") === 0) {
                $line = substr($line, 0, strpos($line, "--"));
            }
            if (empty($line)) {
                continue;
            }
            // Concatenate the previous line, if any, with the current
            if ($previous) {
                $line = $previous . " " . $line;
            }
            $previous = null;
            // If the current line doesnt end with ; then put this line together
            // with the next one, thus supporting multi-line statements.
            if (strrpos($line, ";") != strlen($line) - 1) {
                $previous = $line;
                continue;
            }
            $line = substr($line, 0, strrpos($line, ";"));
            $result = mysql_query($line);
            if ($result === false) {
                throw new Exception("Error when running script '$filename', line $j, query '$line': " . mysql_error());
            }
        }
    }

    static public function restoreLegacy($directory)
    {
        throw new Exception("Use gulliver to restore backups from old versions");
    }

    static public function getBackupInfo($filename)
    {
        G::LoadThirdParty('pear/Archive', 'Tar');
        $backup = new Archive_Tar($filename);
        //Get a temporary directory in the upgrade directory
        $tempDirectory = PATH_DATA . "upgrade/" . basename(tempnam(__FILE__, ''));
        mkdir($tempDirectory);
        $metafiles = array();
        foreach ($backup->listContent() as $backupFile) {
            $filename = $backupFile["filename"];
            if (strpos($filename, "/") === false && substr_compare($filename, ".meta", - 5, 5, true) === 0) {
                if (!$backup->extractList(array($filename), $tempDirectory)) {
                    throw new Exception("Could not extract backup");
                }
                $metafiles[] = "$tempDirectory/$filename";
            }
        }

        CLI::logging("Found " . count($metafiles) . " workspace(s) in backup\n");

        foreach ($metafiles as $metafile) {
            $data = file_get_contents($metafile);
            $workspaceData = G::json_decode($data);
            CLI::logging("\n");
            workspaceTools::printInfo((array) $workspaceData);
        }

        G::rm_dir($tempDirectory);
    }

    static public function dirPerms($filename, $owner, $group, $perms)
    {
        $chown = @chown($filename, $owner);
        $chgrp = @chgrp($filename, $group);
        $chmod = @chmod($filename, $perms);
        if ($chgrp === false || $chmod === false || $chown === false) {
            CLI::logging(CLI::error("Failed to set permissions for $filename") . "\n");
        }
        if (is_dir($filename)) {
            foreach (array_merge(glob($filename . "/*"), glob($filename . "/.*")) as $item) {
                if (basename($item) == "." || basename($item) == "..") {
                    continue;
                }
                workspaceTools::dirPerms($item, $owner, $group, $perms);
            }
        }
    }

    /**
     * restore an archive into a workspace
     *
     * Restores any database and files included in the backup, either as a new
     * workspace, or overwriting a previous one
     *
     * @param string $filename the backup filename
     * @param string $newWorkspaceName if defined, supplies the name for the
     * workspace to restore to
     */
    static public function restore($filename, $srcWorkspace, $dstWorkspace = null, $overwrite = true)
    {
        G::LoadThirdParty('pear/Archive', 'Tar');
        $backup = new Archive_Tar($filename);
        //Get a temporary directory in the upgrade directory
        $tempDirectory = PATH_DATA . "upgrade/" . basename(tempnam(__FILE__, ''));
        $parentDirectory = PATH_DATA . "upgrade";
        if (is_writable($parentDirectory)) {
            mkdir($tempDirectory);
        } else {
            throw new Exception("Could not create directory:" . $parentDirectory);
        }
        //Extract all backup files, including database scripts and workspace files
        if (!$backup->extract($tempDirectory)) {
            throw new Exception("Could not extract backup");
        }
        //Search for metafiles in the new standard (the old standard would contain
        //txt files).
        $metaFiles = glob($tempDirectory . "/*.meta");
        if (empty($metaFiles)) {
            $metaFiles = glob($tempDirectory . "/*.txt");
            if (!empty($metaFiles)) {
                return workspaceTools::restoreLegacy($tempDirectory);
            } else {
                throw new Exception("No metadata found in backup");
            }
        } else {
            CLI::logging("Found " . count($metaFiles) . " workspaces in backup:\n");
            foreach ($metaFiles as $metafile) {
                CLI::logging("-> " . basename($metafile) . "\n");
            }
        }
        if (count($metaFiles) > 1 && (!isset($srcWorkspace))) {
            throw new Exception("Multiple workspaces in backup but no workspace specified to restore");
        }
        if (isset($srcWorkspace) && !in_array("$srcWorkspace.meta", array_map(BASENAME, $metaFiles))) {
            throw new Exception("Workspace $srcWorkspace not found in backup");
        }
        foreach ($metaFiles as $metaFile) {
            $metadata = G::json_decode(file_get_contents($metaFile));
            if ($metadata->version != 1) {
                throw new Exception("Backup version {$metadata->version} not supported");
            }
            $backupWorkspace = $metadata->WORKSPACE_NAME;
            if (isset($dstWorkspace)) {
                $workspaceName = $dstWorkspace;
                $createWorkspace = true;
            } else {
                $workspaceName = $metadata->WORKSPACE_NAME;
                $createWorkspace = false;
            }
            if (isset($srcWorkspace) && strcmp($metadata->WORKSPACE_NAME, $srcWorkspace) != 0) {
                CLI::logging(CLI::warning("> Workspace $backupWorkspace found, but not restoring.") . "\n");
                continue;
            } else {
                CLI::logging("> Restoring " . CLI::info($backupWorkspace) . " to " . CLI::info($workspaceName) . "\n");
            }
            $workspace = new workspaceTools($workspaceName);
            if ($workspace->workspaceExists()) {
                if ($overwrite) {
                    CLI::logging(CLI::warning("> Workspace $workspaceName already exist, overwriting!") . "\n");
                } else {
                    throw new Exception("Destination workspace already exist (use -o to overwrite)");
                }
            }
            if (file_exists($workspace->path)) {
                G::rm_dir($workspace->path);
            }
            foreach ($metadata->directories as $dir) {
                CLI::logging("+> Restoring directory '$dir'\n");

                if (!rename("$tempDirectory/$dir", $workspace->path)) {
                    throw new Exception("There was an error copying the backup files ($tempDirectory/$dir) to the workspace directory {$workspace->path}.");
                }
            }

            CLI::logging("> Changing file permissions\n");
            $shared_stat = stat(PATH_DATA);
            if ($shared_stat !== false) {
                workspaceTools::dirPerms($workspace->path, $shared_stat['uid'], $shared_stat['gid'], $shared_stat['mode']);
            } else {
                CLI::logging(CLI::error("Could not get the shared folder permissions, not changing workspace permissions") . "\n");
            }
            list ($dbHost, $dbUser, $dbPass) = @explode(SYSTEM_HASH, G::decrypt(HASH_INSTALLATION, SYSTEM_HASH));

            CLI::logging("> Connecting to system database in '$dbHost'\n");
            $link = mysql_connect($dbHost, $dbUser, $dbPass);
            @mysql_query("SET NAMES 'utf8';");
            @mysql_query("SET FOREIGN_KEY_CHECKS=0;");
            if (!$link) {
                throw new Exception('Could not connect to system database: ' . mysql_error());
            }

            $newDBNames = $workspace->resetDBInfo($dbHost, $createWorkspace);

            foreach ($metadata->databases as $db) {
                $dbName = $newDBNames[$db->name];
                CLI::logging("+> Restoring database {$db->name} to $dbName\n");
                $workspace->executeSQLScript($dbName, "$tempDirectory/{$db->name}.sql");
                $workspace->createDBUser($dbName, $db->pass, "localhost", $dbName);
                $workspace->createDBUser($dbName, $db->pass, "%", $dbName);
            }

            $workspace->upgradeCacheView(false);

            mysql_close($link);
        }

        CLI::logging("Removing temporary files\n");

        G::rm_dir($tempDirectory);

        CLI::logging(CLI::info("Done restoring") . "\n");
    }
}

