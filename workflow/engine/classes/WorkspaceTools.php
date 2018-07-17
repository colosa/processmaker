<?php

use ProcessMaker\Core\Installer;
use ProcessMaker\Core\System;
use ProcessMaker\Util\FixReferencePath;
use ProcessMaker\Plugins\Adapters\PluginAdapter;

/**
 * class workspaceTools.
 *
 * Utility functions to manage a workspace.
 *
 * @package workflow.engine.classes
 */
class WorkspaceTools
{
    public $name = null;
    public $path = null;
    public $db = null;
    public $dbPath = null;
    public $dbInfo = null;
    public $dbInfoRegExp = "/( *define *\( *'(?P<key>.*?)' *, *\n* *')(?P<value>.*?)(' *\) *;.*)/";
    public $initPropel = false;
    public $initPropelRoot = false;
    public static $populateIdsQueries = array(
        'UPDATE LIST_CANCELED SET
            USR_ID=(SELECT USR_ID FROM USERS WHERE USERS.USR_UID=LIST_CANCELED.USR_UID),
            TAS_ID=(SELECT TAS_ID FROM TASK WHERE TASK.TAS_UID=LIST_CANCELED.TAS_UID),
            PRO_ID=(SELECT PRO_ID FROM PROCESS WHERE PROCESS.PRO_UID=LIST_CANCELED.PRO_UID)',
        'UPDATE LIST_COMPLETED SET
            USR_ID=(SELECT USR_ID FROM USERS WHERE USERS.USR_UID=LIST_COMPLETED.USR_UID),
            TAS_ID=(SELECT TAS_ID FROM TASK WHERE TASK.TAS_UID=LIST_COMPLETED.TAS_UID),
            PRO_ID=(SELECT PRO_ID FROM PROCESS WHERE PROCESS.PRO_UID=LIST_COMPLETED.PRO_UID)',
        'UPDATE LIST_INBOX SET
            USR_ID=(SELECT USR_ID FROM USERS WHERE USERS.USR_UID=LIST_INBOX.USR_UID),
            TAS_ID=(SELECT TAS_ID FROM TASK WHERE TASK.TAS_UID=LIST_INBOX.TAS_UID),
            PRO_ID=(SELECT PRO_ID FROM PROCESS WHERE PROCESS.PRO_UID=LIST_INBOX.PRO_UID)',
        'UPDATE LIST_MY_INBOX SET
            USR_ID=(SELECT USR_ID FROM USERS WHERE USERS.USR_UID=LIST_MY_INBOX.USR_UID),
            TAS_ID=(SELECT TAS_ID FROM TASK WHERE TASK.TAS_UID=LIST_MY_INBOX.TAS_UID),
            PRO_ID=(SELECT PRO_ID FROM PROCESS WHERE PROCESS.PRO_UID=LIST_MY_INBOX.PRO_UID)',
        'UPDATE LIST_PARTICIPATED_HISTORY SET
            USR_ID=(SELECT USR_ID FROM USERS WHERE USERS.USR_UID=LIST_PARTICIPATED_HISTORY.USR_UID),
            TAS_ID=(SELECT TAS_ID FROM TASK WHERE TASK.TAS_UID=LIST_PARTICIPATED_HISTORY.TAS_UID),
            PRO_ID=(SELECT PRO_ID FROM PROCESS WHERE PROCESS.PRO_UID=LIST_PARTICIPATED_HISTORY.PRO_UID)',
        'UPDATE LIST_PARTICIPATED_LAST SET
            USR_ID=(SELECT USR_ID FROM USERS WHERE USERS.USR_UID=LIST_PARTICIPATED_LAST.USR_UID),
            TAS_ID=(SELECT TAS_ID FROM TASK WHERE TASK.TAS_UID=LIST_PARTICIPATED_LAST.TAS_UID),
            PRO_ID=(SELECT PRO_ID FROM PROCESS WHERE PROCESS.PRO_UID=LIST_PARTICIPATED_LAST.PRO_UID)',
        'UPDATE LIST_PAUSED SET
            USR_ID=(SELECT USR_ID FROM USERS WHERE USERS.USR_UID=LIST_PAUSED.USR_UID),
            TAS_ID=(SELECT TAS_ID FROM TASK WHERE TASK.TAS_UID=LIST_PAUSED.TAS_UID),
            PRO_ID=(SELECT PRO_ID FROM PROCESS WHERE PROCESS.PRO_UID=LIST_PAUSED.PRO_UID)',
        'UPDATE LIST_UNASSIGNED SET
            TAS_ID=(SELECT TAS_ID FROM TASK WHERE TASK.TAS_UID=LIST_UNASSIGNED.TAS_UID),
            PRO_ID=(SELECT PRO_ID FROM PROCESS WHERE PROCESS.PRO_UID=LIST_UNASSIGNED.PRO_UID)',
        'UPDATE LIST_UNASSIGNED_GROUP SET
            USR_ID=(SELECT USR_ID FROM USERS WHERE USERS.USR_UID=LIST_UNASSIGNED_GROUP.USR_UID)',
    );

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
     * @param bool   $buildCacheView
     * @param string $workSpace
     * @param bool   $onedb
     * @param string $lang
     * @param array  $arrayOptTranslation
     *
     * @return void
     */
    public function upgrade($buildCacheView = false, $workSpace = SYS_SYS, $onedb = false, $lang = 'en', array $arrayOptTranslation = null)
    {
        if (is_null($arrayOptTranslation)) {
            $arrayOptTranslation = ['updateXml' => true, 'updateMafe' => true];
        }

        $start = microtime(true);
        CLI::logging("> Updating database...\n");
        $this->upgradeDatabase($onedb);
        $stop = microtime(true);
        CLI::logging("<*>   Database Upgrade Process took " . ($stop - $start) . " seconds.\n");

        $start = microtime(true);
        CLI::logging("> Check Intermediate Email Event...\n");
        $this->checkIntermediateEmailEvent();
        $stop = microtime(true);
        CLI::logging("<*>   Database Upgrade Process took " . ($stop - $start) . " seconds.\n");

        $start = microtime(true);
        CLI::logging("> Verify enterprise old...\n");
        $this->verifyFilesOldEnterprise($workSpace);
        $stop = microtime(true);
        CLI::logging("<*>   Verify took " . ($stop - $start) . " seconds.\n");

        $start = microtime(true);
        CLI::logging("> Updating translations...\n");
        $this->upgradeTranslation($arrayOptTranslation['updateXml'], $arrayOptTranslation['updateMafe']);
        $stop = microtime(true);
        $final = $stop - $start;
        CLI::logging("<*>   Updating Translations Process took $final seconds.\n");

        $start = microtime(true);
        CLI::logging("> Updating Content...\n");
        $this->upgradeContent($workSpace);
        $stop = microtime(true);
        $final = $stop - $start;
        CLI::logging("<*>   Updating Content Process took $final seconds.\n");

        $start = microtime(true);
        CLI::logging("> Check Mafe Requirements...\n");
        $this->checkMafeRequirements($workSpace, $lang);
        $stop = microtime(true);
        $final = $stop - $start;
        CLI::logging("<*>   Check Mafe Requirements Process took $final seconds.\n");

        $start = microtime(true);
        CLI::logging("> Updating Triggers...\n");
        $this->updateTriggers(true, $lang);
        $stop = microtime(true);
        $final = $stop - $start;
        CLI::logging("<*>   Updating Triggers Process took $final seconds.\n");

        $start = microtime(true);
        CLI::logging("> Backup log files...\n");
        $this->backupLogFiles();
        $stop = microtime(true);
        $final = $stop - $start;
        CLI::logging("<*>   Backup log files Process took $final seconds.\n");

        $start = microtime(true);
        CLI::logging("> Optimizing content data...\n");
        $this->migrateContent($workSpace, $lang);
        $stop = microtime(true);
        CLI::logging("<*>   Optimizing content data took " . ($stop - $start) . " seconds.\n");

        /*----------------------------------********---------------------------------*/

        $start = microtime(true);
        CLI::logging("> Updating Files Manager...\n");
        $this->processFilesUpgrade();
        $stop = microtime(true);
        CLI::logging("<*>   Updating Files Manager took " . ($stop - $start) . " seconds.\n");

        $start = microtime(true);
        CLI::logging("> Clean access and refresh tokens...\n");
        $this->cleanTokens($workSpace);
        $stop = microtime(true);
        CLI::logging("<*>   Clean access and refresh tokens took " . ($stop - $start) . " seconds.\n");

        $start = microtime(true);
        CLI::logging("> Optimizing Self-Service data...\n");
        $this->migrateSelfServiceRecordsRun($workSpace);
        $stop = microtime(true);
        CLI::logging("<*>   Migrating Self-Service records Process took " . ($stop - $start) . " seconds.\n");

        $start = microtime(true);
        CLI::logging("> Migrating and populating indexing for avoiding the use of table APP_CACHE_VIEW...\n");
        $this->migratePopulateIndexingACV($workSpace);
        $stop = microtime(true);
        CLI::logging("<*>   Migrating an populating indexing for avoiding the use of table APP_CACHE_VIEW process took " . ($stop - $start) . " seconds.\n");
        
        $start = microtime(true);
        CLI::logging("> Updating rows in Web Entry table for classic processes...\n");
        $this->updatingWebEntryClassicModel($workSpace);
        $stop = microtime(true);
        CLI::logging("<*>   Updating rows in Web Entry table for classic processes took " . ($stop - $start) . " seconds.\n");

        $start = microtime(true);
        CLI::logging("> Update framework paths...\n");
        $this->updateFrameworkPaths($workSpace);
        $stop = microtime(true);
        CLI::logging("<*>   Update framework paths took " . ($stop - $start) . " seconds.\n");

        $start = microtime(true);
        CLI::logging("> Migrating and populating plugin singleton data...\n");
        $this->migrateSingleton($workSpace);
        $stop = microtime(true);
        CLI::logging("<*>   Migrating and populating plugin singleton data took " . ($stop - $start) . " seconds.\n");
    }

    /**
     * Updating cases directories structure
     *
     */
    public function updateStructureDirectories($workSpace = SYS_SYS)
    {
        $start = microtime(true);
        CLI::logging("> Updating cases directories structure...\n");
        $this->upgradeCasesDirectoryStructure($workSpace);
        $stop = microtime(true);
        $final = $stop - $start;
        CLI::logging("<*>   Database Upgrade Structure Process took $final seconds.\n");
    }

    public function checkIntermediateEmailEvent()
    {
        $oEmailEvent = new \ProcessMaker\BusinessModel\EmailEvent();
        $oEmailServer = new \ProcessMaker\BusinessModel\EmailServer();
        $oCriteria = $oEmailEvent->getEmailEventCriteriaEmailServer();
        $rsCriteria = \EmailServerPeer::doSelectRS($oCriteria);
        $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);
        while ($rsCriteria->next()) {
            $row = $rsCriteria->getRow();
            $newUidData = $oEmailServer->getUidEmailServer($row['EMAIL_EVENT_FROM']);
            if (is_array($newUidData)) {
                $oEmailEvent->update($row['EMAIL_EVENT_UID'], $newUidData);
            }
        }
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

        $this->dbRbacHost = $values["DB_RBAC_HOST"];
        $this->dbRbacName = $values["DB_RBAC_NAME"];
        $this->dbRbacUser = $values["DB_RBAC_USER"];
        $this->dbRbacPass = $values["DB_RBAC_PASS"];

        return $this->dbInfo = $values;
    }

    private function resetDBInfoCallback($matches)
    {
        /* This function changes the values of defines while keeping their formatting
         * intact.
         * $matches will contain several groups:
         * ((define('(<key>)2', ')1 (<value>)3 (');)4 )0
         */
        $key = isset($matches['key']) ? $matches['key'] : $matches[2];
        $value = isset($matches['value']) ? $matches['value'] : $matches[3];

        if ($this->onedb) {
            $dbInfo = $this->getDBInfo();
            $dbPrefix = array('DB_NAME' => 'wf_', 'DB_RBAC_NAME' => 'wf_', 'DB_REPORT_NAME' => 'wf_');
            $dbPrefixUser = array('DB_USER' => 'wf_', 'DB_RBAC_USER' => 'wf_', 'DB_REPORT_USER' => 'wf_');
        } else {
            $dbPrefix = array('DB_NAME' => 'wf_', 'DB_RBAC_NAME' => 'rb_', 'DB_REPORT_NAME' => 'rp_');
            $dbPrefixUser = array('DB_USER' => 'wf_', 'DB_RBAC_USER' => 'rb_', 'DB_REPORT_USER' => 'rp_');
        }

        if (array_search($key, array('DB_HOST', 'DB_RBAC_HOST', 'DB_REPORT_HOST')) !== false) {
            /* Change the database hostname for these keys */
            $value = $this->newHost;
        } elseif (array_key_exists($key, $dbPrefix)) {
            if ($this->resetDBNames) {
                /* Change the database name to the new workspace, following the standard
                 * of prefix (either wf_, rp_, rb_) and the workspace name.
                 */
                if ($this->unify) {
                    $nameDb = explode("_", $value);
                    if (!isset($nameDb[1])) {
                        $dbName = $value;
                    } else {
                        $dbName = $dbPrefix[$key] . $nameDb[1];
                    }
                } else {
                    $dbName = $dbPrefix[$key] . $this->name;
                }
            } else {
                $dbName = $value;
            }
            $this->resetDBDiff[$value] = $dbName;
            $value = $dbName;
        } elseif (array_key_exists($key, $dbPrefixUser)) {
            if ($this->resetDBNames) {
                $dbName = $this->dbGrantUser;
            } else {
                $dbName = $value;
            }
            $this->resetDBDiff['DB_USER'] = $dbName;
            $value = $dbName;
        }
        if (array_search($key, array('DB_PASS', 'DB_RBAC_PASS', 'DB_REPORT_PASS')) !== false && !empty($this->dbGrantUserPassword)) {
            $value = $this->dbGrantUserPassword;
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
    public function resetDBInfo($newHost, $resetDBNames = true, $onedb = false, $unify = false)
    {
        if (count(explode(":", $newHost)) < 2) {
            $newHost .= ':3306';
        }
        $this->newHost = $newHost;
        $this->resetDBNames = $resetDBNames;
        $this->resetDBDiff = array();
        $this->onedb = $onedb;
        $this->unify = $unify;
        if ($resetDBNames) {
            $this->dbGrantUser = uniqid('wf_');
            $this->dbGrantUserPassword = G::generate_password(12, "luns", ".");
        }


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

        require_once("propel/Propel.php");
        require_once("creole/Creole.php");

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
        $regenerateContent = new Content();
        $regenerateContent->regenerateContent($arrayLang, $workSpace);
    }

    /**
     * Upgrade this workspace translations from all avaliable languages.
     *
     * @param bool $flagXml  Update XML
     * @param bool $flagMafe Update MAFE
     *
     * @return void
     */
    public function upgradeTranslation($flagXml = true, $flagMafe = true)
    {
        $this->initPropel(true);
        $this->checkDataConsistenceInContentTable();


        $language = new Language();

        foreach (System::listPoFiles() as $poFile) {
            $poName = basename($poFile);
            $names = explode(".", basename($poFile));
            $extension = array_pop($names);
            $langid = array_pop($names);

            CLI::logging('Updating Database translations with ' . $poName . "\n");

            if ($flagXml) {
                CLI::logging('Updating XML form translations with ' . $poName . "\n");
            }

            if ($flagMafe) {
                CLI::logging('Updating MAFE translations with ' . $poName . "\n");
            }

            $language->import($poFile, $flagXml, true, $flagMafe);
        }
    }

    /**
     * Verification of the Content data table for column CON_ID
     * @return void
     */
    private function checkDataConsistenceInContentTable()
    {
        $criteriaSelect = new Criteria("workflow");
        $criteriaSelect->add(
            $criteriaSelect->getNewCriterion(ContentPeer::CON_ID, '%' . "'" . '%', Criteria::LIKE)->addOr(
                $criteriaSelect->getNewCriterion(ContentPeer::CON_ID, '%' . '"' . '%', Criteria::LIKE)));

        BasePeer::doDelete($criteriaSelect, Propel::getConnection("workflow"));
    }

    /**
     * Get a connection to this workspace wf database
     *
     * @return database connection
     */
    private function getDatabase($rbac = false)
    {
        if (isset($this->db) && $this->db->isConnected() && ($rbac == false && $this->db->getDatabaseName() == $this->dbName)) {
            return $this->db;
        }


        if ($rbac == true) {
            $this->db = new database($this->dbAdapter, $this->dbRbacHost, $this->dbRbacUser, $this->dbRbacPass, $this->dbRbacName);
        } else {
            $this->db = new database($this->dbAdapter, $this->dbHost, $this->dbUser, $this->dbPass, $this->dbName);
        }
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
    public function getSchema($rbac = false)
    {
        $oDataBase = $this->getDatabase($rbac);

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
     * Upgrade triggers of tables (Database)
     *
     * @param bool $flagRecreate Recreate
     * @param string $language Language
     *
     * return void
     */
    private function upgradeTriggersOfTables($flagRecreate, $language)
    {
        try {
            $appCacheView = new AppCacheView();
            $appCacheView->setPathToAppCacheFiles(PATH_METHODS . "setup" . PATH_SEP . "setupSchemas" . PATH_SEP);

            $result = $appCacheView->triggerAppDelegationInsert($language, $flagRecreate);
            $result = $appCacheView->triggerAppDelegationUpdate($language, $flagRecreate);
            $result = $appCacheView->triggerApplicationUpdate($language, $flagRecreate);
            $result = $appCacheView->triggerApplicationDelete($language, $flagRecreate);
            $result = $appCacheView->triggerSubApplicationInsert($language, $flagRecreate);
            $result = $appCacheView->triggerContentUpdate($language, $flagRecreate);
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Upgrade the AppCacheView table to the latest system version.
     *
     * This recreates the table and populates with data.
     *
     * @param bool $flagRecreate only check if the upgrade is needed if true
     * @param string $lang not currently used
     */
    public function upgradeCacheView($fill = true, $flagRecreate = false, $lang = "en")
    {
        $this->initPropel(true);

        //check the language, if no info in config about language, the default is 'en'

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

        CLI::logging("-> Creating tables \n");
        //now check if table APPCACHEVIEW exists, and it have correct number of fields, etc.
        $res = $appCache->checkAppCacheView();

        CLI::logging("-> Update DEL_LAST_INDEX field in APP_DELEGATION table \n");
        //Update APP_DELEGATION.DEL_LAST_INDEX data
        $res = $appCache->updateAppDelegationDelLastIndex($lang, $flagRecreate);



        CLI::logging("-> Creating triggers\n");

        //now check if we have the triggers installed
        $this->upgradeTriggersOfTables($flagRecreate, $lang);

        if ($fill) {
            CLI::logging("-> Rebuild Cache View with language $lang...\n");
            //build using the method in AppCacheView Class
            $res = $appCache->fillAppCacheView($lang);
        }
        //set status in config table
        $confParams = array('LANG' => $lang, 'STATUS' => 'active');
        $oConf->aConfig = $confParams;
        $oConf->saveConfig('APP_CACHE_VIEW_ENGINE', '', '', '');

        // removing casesList configuration records. TODO: removing these lines that resets all the configurations records
        $oCriteria = new Criteria();
        $oCriteria->add(ConfigurationPeer::CFG_UID, "casesList");
        $oCriteria->add(ConfigurationPeer::OBJ_UID, array("todo", "draft", "sent", "unassigned", "paused", "cancelled"), Criteria::NOT_IN);
        ConfigurationPeer::doDelete($oCriteria);
        // end of reset

        //close connection
        if (substr(PHP_OS, 0, 3) != 'WIN') {
            $connection = Propel::getConnection('workflow');

            $sql_sleep = "SELECT * FROM information_schema.processlist WHERE command = 'Sleep' and user = SUBSTRING_INDEX(USER(),'@',1) and db = DATABASE() ORDER BY id;";
            $stmt_sleep = $connection->createStatement();
            $rs_sleep = $stmt_sleep->executeQuery($sql_sleep, ResultSet::FETCHMODE_ASSOC);

            while ($rs_sleep->next()) {
                $row_sleep = $rs_sleep->getRow();
                $oStatement_sleep = $connection->prepareStatement("kill " . $row_sleep['ID']);
                $oStatement_sleep->executeQuery();
            }

            $sql_query = "SELECT * FROM information_schema.processlist WHERE user = SUBSTRING_INDEX(USER(),'@',1) and db = DATABASE() and time > 0 ORDER BY id;";
            $stmt_query = $connection->createStatement();
            $rs_query = $stmt_query->executeQuery($sql_query, ResultSet::FETCHMODE_ASSOC);

            while ($rs_query->next()) {
                $row_query = $rs_query->getRow();
                $oStatement_query = $connection->prepareStatement("kill " . $row_query['ID']);
                $oStatement_query->executeQuery();
            }
        }
    }

    /**
     * fix the 32K issue, by migrating /files directory structure to an uid tree structure based.
     * @param $workspace got the site(s) the manager wants to upgrade
     */
    public function upgradeCasesDirectoryStructure($workspace)
    {
        define('PATH_DOCUMENT', PATH_DATA . 'sites' . DIRECTORY_SEPARATOR . $workspace . DIRECTORY_SEPARATOR . 'files');
        if (!is_writable(PATH_DOCUMENT)) {
            CLI::logging(CLI::error("Error:" . PATH_DOCUMENT . " is not writable... please check the su permissions.\n"));
            return;
        }

        $directory = array();
        $blackHoleDir = G::getBlackHoleDir();
        $directory = glob(PATH_DOCUMENT . "*", GLOB_ONLYDIR);
        $dirslength = sizeof($directory);

        if (!@chdir(PATH_DOCUMENT)) {
            CLI::logging(CLI::error("Cannot use Document directory. The upgrade must be done as root.\n"));
            return;
        }

        //Start migration
        for ($index = 0; $index < $dirslength; $index++) {
            $depthdirlevel = explode(DIRECTORY_SEPARATOR, $directory[$index]);
            $lastlength = sizeof($depthdirlevel);
            $UIdDir = $depthdirlevel[$lastlength - 1];
            $lenDir = strlen($UIdDir);

            if ($lenDir == 32 && $UIdDir != $blackHoleDir) {
                $len = count(scandir($UIdDir));
                if ($len > 2) {
                    //lenght = 2, because the function check . and .. dir links
                    $newDiretory = G::getPathFromUIDPlain($UIdDir);
                    CLI::logging("Migrating $UIdDir to $newDiretory\n");
                    G::mk_dir($newDiretory, 0777);
                    //echo `cp -R $UIdDir/* $newDiretory/`;

                    if (G::recursive_copy($UIdDir, $newDiretory)) {
                        CLI::logging("Removing $UIdDir...\n");
                        G::rm_dir($UIdDir);
                        rmdir($UIdDir); //remove the diretory itself, G::rm_dir cannot do it
                    } else {
                        CLI::logging(CLI::error("Error: Failure at coping from $UIdDir...\n"));
                    }
                } else {
                    CLI::logging("$UIdDir is empty, removing it\n");
                    rmdir($UIdDir); //remove the diretory itself
                }
            }
        }

        //Start '0' directory migration
        $black = PATH_DOCUMENT . $blackHoleDir . DIRECTORY_SEPARATOR;
        if (is_dir($black)) {
            $newpattern = array();
            $file = glob($black . '*.*'); //files only
            $dirlen = count($file);

            for ($index = 0; $index < $dirlen; $index++) {
                $levelfile = explode(DIRECTORY_SEPARATOR, $file[$index]);
                $lastlevel = sizeof($levelfile);
                $goalFile = $levelfile[$lastlevel - 1];
                $newpattern = G::getPathFromFileUIDPlain($blackHoleDir, $goalFile);
                CLI::logging("Migrating $blackHoleDir file: $goalFile\n");
                G::mk_dir($blackHoleDir . PATH_SEP . $newpattern[0], 0777);
                //echo `cp -R $black$goalFile $black$newpattern[0]/$newpattern[1]`;

                if (copy($black . $goalFile, $black . $newpattern[0] . DIRECTORY_SEPARATOR . $newpattern[1])) {
                    unlink($file[$index]);
                } else {
                    CLI::logging(CLI::error("Error: Failure at copy $file[$index] files...\n"));
                }
            }
        }

        //Set value of 2 to the directory structure version.
        $this->initPropel(true);
        $conf = new Configurations();
        if (!$conf->exists("ENVIRONMENT_SETTINGS")) {
            $conf->aConfig = array("format" => '@userName (@firstName @lastName)',
                "dateFormat" => 'd/m/Y',
                "startCaseHideProcessInf" => false,
                "casesListDateFormat" => 'Y-m-d H:i:s',
                "casesListRowNumber" => 25,
                "casesListRefreshTime" => 120);
            $conf->saveConfig('ENVIRONMENT_SETTINGS', '');
        }
        $conf->setDirectoryStructureVer(2);
        CLI::logging(CLI::info("Version Directory Structure is 2 now.\n"));
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
    public function upgradeDatabase($onedb = false, $checkOnly = false)
    {
        $this->initPropel(true);
        P11835::$dbAdapter = $this->dbAdapter;
        P11835::isApplicable();
        $systemSchema = System::getSystemSchema($this->dbAdapter);
        $systemSchemaRbac = System::getSystemSchemaRbac($this->dbAdapter);// get the Rbac Schema
        $this->registerSystemTables(array_merge($systemSchema, $systemSchemaRbac));
        $this->upgradeSchema($systemSchema);
        $this->upgradeSchema($systemSchemaRbac, false, true, $onedb); // perform Upgrade to Rbac
        $this->upgradeData();
        $this->checkRbacPermissions();//check or add new permissions
        $this->checkSequenceNumber();
        $this->migrateIteeToDummytask($this->name);
        $this->upgradeConfiguration();
        /*----------------------------------********---------------------------------*/

        //There records in table "EMAIL_SERVER"
        $criteria = new Criteria("workflow");

        $criteria->addSelectColumn(EmailServerPeer::MESS_UID);
        $criteria->setOffset(0);
        $criteria->setLimit(1);

        $rsCriteria = EmailServerPeer::doSelectRS($criteria);

        if (!$rsCriteria->next()) {
            //Insert the first record
            $arrayData = array();

            $emailSever = new \ProcessMaker\BusinessModel\EmailServer();

            $emailConfiguration = System::getEmailConfiguration();

            if (!empty($emailConfiguration)) {
                $arrayData["MESS_ENGINE"] = $emailConfiguration["MESS_ENGINE"];

                switch ($emailConfiguration["MESS_ENGINE"]) {
                    case "PHPMAILER":
                        $arrayData["MESS_SERVER"]              = $emailConfiguration["MESS_SERVER"];
                        $arrayData["MESS_PORT"]                = (int)($emailConfiguration["MESS_PORT"]);
                        $arrayData["MESS_RAUTH"]               = (is_numeric($emailConfiguration["MESS_RAUTH"]))? (int)($emailConfiguration["MESS_RAUTH"]) : (($emailConfiguration["MESS_RAUTH"] . "" == "true")? 1 : 0);
                        $arrayData["MESS_ACCOUNT"]             = $emailConfiguration["MESS_ACCOUNT"];
                        $arrayData["MESS_PASSWORD"]            = $emailConfiguration["MESS_PASSWORD"];
                        $arrayData["MESS_FROM_MAIL"]           = (isset($emailConfiguration["MESS_FROM_MAIL"]))? $emailConfiguration["MESS_FROM_MAIL"] : "";
                        $arrayData["MESS_FROM_NAME"]           = (isset($emailConfiguration["MESS_FROM_NAME"]))? $emailConfiguration["MESS_FROM_NAME"] : "";
                        $arrayData["SMTPSECURE"]               = $emailConfiguration["SMTPSecure"];
                        $arrayData["MESS_TRY_SEND_INMEDIATLY"] = (isset($emailConfiguration["MESS_TRY_SEND_INMEDIATLY"]) && ($emailConfiguration["MESS_TRY_SEND_INMEDIATLY"] . "" == "true" || $emailConfiguration["MESS_TRY_SEND_INMEDIATLY"] . "" == "1"))? 1 : 0;
                        $arrayData["MAIL_TO"]                  = isset($emailConfiguration["MAIL_TO"]) ? $emailConfiguration["MAIL_TO"] : '';
                        $arrayData["MESS_DEFAULT"]             = (isset($emailConfiguration["MESS_ENABLED"]) && $emailConfiguration["MESS_ENABLED"] . "" == "1")? 1 : 0;
                        break;
                    case "MAIL":
                        $arrayData["MESS_SERVER"]              = "";
                        $arrayData["MESS_FROM_MAIL"]           = (isset($emailConfiguration["MESS_FROM_MAIL"]))? $emailConfiguration["MESS_FROM_MAIL"] : "";
                        $arrayData["MESS_FROM_NAME"]           = (isset($emailConfiguration["MESS_FROM_NAME"]))? $emailConfiguration["MESS_FROM_NAME"] : "";
                        $arrayData["MESS_TRY_SEND_INMEDIATLY"] = (isset($emailConfiguration["MESS_TRY_SEND_INMEDIATLY"]) && ($emailConfiguration["MESS_TRY_SEND_INMEDIATLY"] . "" == "true" || $emailConfiguration["MESS_TRY_SEND_INMEDIATLY"] . "" == "1"))? 1 : 0;
                        $arrayData["MESS_ACCOUNT"]             = "";
                        $arrayData["MESS_PASSWORD"]            = "";
                        $arrayData["MAIL_TO"]                  = (isset($emailConfiguration["MAIL_TO"]))? $emailConfiguration["MAIL_TO"] : "";
                        $arrayData["MESS_DEFAULT"]             = (isset($emailConfiguration["MESS_ENABLED"]) && $emailConfiguration["MESS_ENABLED"] . "" == "1")? 1 : 0;
                        break;
                }

                $arrayData = $emailSever->create($arrayData);
            } else {
                $arrayData["MESS_ENGINE"]   = "MAIL";
                $arrayData["MESS_SERVER"]   = "";
                $arrayData["MESS_ACCOUNT"]  = "";
                $arrayData["MESS_PASSWORD"] = "";
                $arrayData["MAIL_TO"]       = "";
                $arrayData["MESS_DEFAULT"]  = 1;

                $arrayData = $emailSever->create2($arrayData);
            }
        }

        P11835::execute();

        return true;
    }

    private function setFormatRows()
    {
        switch ($this->dbAdapter) {
            case 'mysql':
                $this->assoc = MYSQL_ASSOC;
                $this->num = MYSQL_NUM;
                break;
            case 'sqlsrv':
                $this->assoc = SQLSRV_FETCH_ASSOC;
                $this->num = SQLSRV_FETCH_NUMERIC;
                break;
            default:
                throw new Exception("Unknown adapter hae been set for associate fetch index row format.");
                break;
        }
    }

    /**
     * Upgrade this workspace database from a schema
     *
     * @param array $schema the schema information, such as returned from getSystemSchema
     * @param bool $checkOnly only check if the upgrade is needed if true
     * @return array bool the changes if checkOnly is true, else return
     * true on success
     */
    public function upgradeSchema($schema, $checkOnly = false, $rbac = false, $onedb = false)
    {
        $dbInfo = $this->getDBInfo();

        if ($dbInfo['DB_NAME'] == $dbInfo['DB_RBAC_NAME']) {
            $onedb = true;
        } else {
            $onedb = false;
        }

        if (strcmp($dbInfo["DB_ADAPTER"], "mysql") != 0) {
            throw new Exception("Only MySQL is supported");
        }

        $this->setFormatRows();

        $workspaceSchema = $this->getSchema($rbac);
        $oDataBase = $this->getDatabase($rbac);

        if (!$onedb) {
            if ($rbac) {
                $rename = System::verifyRbacSchema($workspaceSchema);
                if (count($rename) > 0) {
                    foreach ($rename as $tableName) {
                        $oDataBase->executeQuery($oDataBase->generateRenameTableSQL($tableName));
                    }
                }
            }
        }
        $workspaceSchema = $this->getSchema($rbac);
        $changes = System::compareSchema($workspaceSchema, $schema);

        $changed = (count($changes['tablesToAdd']) > 0 || count($changes['tablesToAlter']) > 0 || count($changes['tablesWithNewIndex']) > 0 || count($changes['tablesToAlterIndex']) > 0);

        if ($checkOnly || (!$changed)) {
            if ($changed) {
                return $changes;
            } else {
                CLI::logging("-> Nothing to change in the data base structure of " . (($rbac == true)?"RBAC":"WORKFLOW") . "\n");
                return $changed;
            }
        }

        $oDataBase->iFetchType = $this->num;

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
                            if ($oDataBase->checkPatchHor1787($sTable, $sColumn, $vData)) {
                                $oDataBase->executeQuery($oDataBase->generateCheckAddColumnSQL($sTable, $sColumn, $vData));
                                $oDataBase->executeQuery($oDataBase->deleteAllIndexesIntable($sTable, $sColumn, $vData));
                            }
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
                /*----------------------------------********---------------------------------*/
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
            $dbNetView = new Net($this->dbHost);
            $dbNetView->loginDbServer($this->dbUser, $this->dbPass);
            try {
                if (!defined('DB_ADAPTER')) {
                    require_once($this->dbPath);
                }
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

        if ($fields['DB_NAME'] == $fields['DB_RBAC_NAME']) {
            $info = array('Workspace Name' => $fields['WORKSPACE_NAME'], 'Workflow Database' => sprintf("%s://%s:%s@%s/%s", $fields['DB_ADAPTER'], $fields['DB_USER'], $fields['DB_PASS'], $fields['DB_HOST'], $fields['DB_NAME']), 'MySql Version' => $fields['DATABASE']);
        } else {
            $info = array('Workspace Name' => $fields['WORKSPACE_NAME'],
            //'Available Databases'  => $fields['AVAILABLE_DB'],
            'Workflow Database' => sprintf("%s://%s:%s@%s/%s", $fields['DB_ADAPTER'], $fields['DB_USER'], $fields['DB_PASS'], $fields['DB_HOST'], $fields['DB_NAME']), 'RBAC Database' => sprintf("%s://%s:%s@%s/%s", $fields['DB_ADAPTER'], $fields['DB_RBAC_USER'], $fields['DB_RBAC_PASS'], $fields['DB_RBAC_HOST'], $fields['DB_RBAC_NAME']), 'Report Database' => sprintf("%s://%s:%s@%s/%s", $fields['DB_ADAPTER'], $fields['DB_REPORT_USER'], $fields['DB_REPORT_PASS'], $fields['DB_REPORT_HOST'], $fields['DB_REPORT_NAME']), 'MySql Version' => $fields['DATABASE']
        );
        }

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
            WorkspaceTools::printSysInfo();
            CLI::logging("\n");
        }

        WorkspaceTools::printInfo($this->getMetadata());
    }

    /**
     * exports this workspace database to the specified path
     *
     * This function is used mainly for backup purposes.
     *
     * @param string $path the directory where to create the sql files
     */
    public function exportDatabase($path, $onedb = false)
    {
        $dbInfo = $this->getDBInfo();

        if ($onedb) {
            $databases = array("rb", "rp");
        } elseif ($dbInfo['DB_NAME'] == $dbInfo['DB_RBAC_NAME']) {
            $databases = array("wf");
        } else {
            $databases = array("wf", "rp", "rb");
        }

        $dbNames = array();

        foreach ($databases as $db) {
            $dbInfo = $this->getDBCredentials($db);
            $oDbMaintainer = new DataBaseMaintenance($dbInfo["host"], $dbInfo["user"], $dbInfo["pass"]);
            CLI::logging("Saving database {$dbInfo["name"]}\n");
            $oDbMaintainer->connect($dbInfo["name"]);
            $oDbMaintainer->setTempDir($path . "/");
            $oDbMaintainer->backupDataBase($oDbMaintainer->getTempDir() . $dbInfo["name"] . ".sql");
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
    public static function createBackup($filename, $compress = true)
    {
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
        $metadata['backupEngineVersion'] = 2;
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
    public function createDBUser($username, $password, $hostname, $database)
    {
        mysql_select_db("mysql");
        $hostname = array_shift(explode(":", $hostname));
        $sqlstmt = "SELECT * FROM user WHERE user = '$username' AND host = '$hostname'";
        $result = mysql_query($sqlstmt);
        if ($result === false) {
            throw new Exception("Unable to retrieve users: " . mysql_error());
        }
        $users = mysql_num_rows($result);
        if ($users === 0) {
            CLI::logging("Creating user $username for $hostname\n");
            $result = mysql_query("CREATE USER '$username'@'$hostname' IDENTIFIED BY '$password'");
            if ($result === false) {
                throw new Exception("Unable to create user $username: " . mysql_error());
            }
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
    public function executeSQLScript($database, $filename, $parameters, $versionBackupEngine = 1)
    {
        mysql_query("CREATE DATABASE IF NOT EXISTS " . mysql_real_escape_string($database));

        //check function shell_exec
        $disabled_functions = ini_get('disable_functions');
        $flag = false;
        if ($disabled_functions != '') {
            $arr = explode(',', $disabled_functions);
            sort($arr);
            if (in_array("shell_exec", $arr)) {
                $flag = true;
            }
        }

        // Check if mysql exist on server
        $flagFunction = null;
        if (!$flag) {
            $flagFunction = shell_exec('mysql --version');
        }

        $arrayRegExpEngineSearch = array("/\)\s*TYPE\s*=\s*(InnoDB)/i", "/\)\s*TYPE\s*=\s*(MyISAM)/i","/SET\s*FOREIGN_KEY_CHECKS\s*=\s*0\s*;/");
        $arrayRegExpEngineReplace = array(") ENGINE=\\1 DEFAULT CHARSET=utf8", ") ENGINE=\\1","SET FOREIGN_KEY_CHECKS=0;\nSET unique_checks=0;\nSET AUTOCOMMIT=0;");

        //replace DEFINER
        $script = preg_replace('/DEFINER=[^*]*/', '', file_get_contents($filename));
        file_put_contents($filename, $script);
        
        if (!$flag && !is_null($flagFunction)) {
            //Replace TYPE by ENGINE
            if ($versionBackupEngine == 1) {
                $script = preg_replace($arrayRegExpEngineSearch, $arrayRegExpEngineReplace, file_get_contents($filename));
                file_put_contents($filename, $script."\nCOMMIT;");
            } else {
                $arrayRegExpEngineSearch = array("/\)\s*TYPE\s*=\s*(InnoDB)/i", "/\)\s*TYPE\s*=\s*(MyISAM)/i");
                $arrayRegExpEngineReplace = array(") ENGINE=\\1 DEFAULT CHARSET=utf8", ") ENGINE=\\1");
                $script = preg_replace($arrayRegExpEngineSearch, $arrayRegExpEngineReplace, file_get_contents($filename));
                file_put_contents($filename, $script);
            }

            $aHost = explode(':', $parameters['dbHost']);
            $dbHost = $aHost[0];
            if (isset($aHost[1])) {
                $dbPort = $aHost[1];
                $command = 'mysql'
                    . ' --host=' . $dbHost
                    . ' --port=' . $dbPort
                    . ' --user=' . $parameters['dbUser']
                    . ' --password=' . escapeshellarg($parameters['dbPass'])
                    . ' --database=' . mysql_real_escape_string($database)
                    . ' --default_character_set utf8'
                    . ' --execute="SOURCE ' . $filename . '"';
            } else {
                $command = 'mysql'
                    . ' --host=' . $dbHost
                    . ' --user=' . $parameters['dbUser']
                    . ' --password=' . escapeshellarg($parameters['dbPass'])
                    . ' --database=' . mysql_real_escape_string($database)
                    . ' --default_character_set utf8'
                    . ' --execute="SOURCE ' . $filename . '"';
            }
            shell_exec($command);
        } else {
            //If the safe mode of the server is actived
            try {
                mysql_select_db($database);

                //Replace TYPE by ENGINE
                $script = preg_replace($arrayRegExpEngineSearch, $arrayRegExpEngineReplace, file_get_contents($filename));
                if ($versionBackupEngine == 1) {
                    $script = $script."\nCOMMIT;";
                }

                $lines = explode("\n", $script);
                $previous = null;
                $insert = false;
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

                    if (strrpos($line, "INSERT INTO") !== false) {
                        $insert = true;
                        if ($insert) {
                            $result = mysql_query("START TRANSACTION");
                            $insert = false;
                        }
                        $result = mysql_query($line);
                        continue;
                    } else {
                        if (!$insert) {
                            $result = mysql_query("COMMIT");
                            $insert = true;
                        }
                    }

                    $result = mysql_query($line);
                    if ($result === false) {
                        throw new Exception("Error when running script '$filename', line $j, query '$line': " . mysql_error());
                    }
                }
                if (!$insert) {
                    $result = mysql_query("COMMIT");
                }
            } catch (Exception $e) {
                CLI::logging(CLI::error("Error:" . "There are problems running script '$filename': " . $e));
            }
        }
    }

    public function executeScript($database, $filename, $parameters)
    {
        $this->executeSQLScript($database, $filename, $parameters);
        return true;
    }

    public static function restoreLegacy($directory)
    {
        throw new Exception("Use gulliver to restore backups from old versions");
    }

    public static function getBackupInfo($filename)
    {
        $backup = new Archive_Tar($filename);
        //Get a temporary directory in the upgrade directory
        $tempDirectory = PATH_DATA . "upgrade/" . basename(tempnam(__FILE__, ''));
        mkdir($tempDirectory);
        $metafiles = array();
        foreach ($backup->listContent() as $backupFile) {
            $filename = $backupFile["filename"];
            if (strpos($filename, "/") === false && substr_compare($filename, ".meta", -5, 5, true) === 0) {
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
            WorkspaceTools::printInfo((array)$workspaceData);
        }

        G::rm_dir($tempDirectory);
    }

    public static function dirPerms($filename, $owner, $group, $perms)
    {
        $chown = @chown($filename, $owner);
        $chgrp = @chgrp($filename, $group);
        $chmod = @chmod($filename, $perms);

        if ($chgrp === false || $chmod === false || $chown === false) {
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                exec("icacls \"" . $filename . "\" /grant Administrador:(D,WDAC) /T", $res);
            } else {
                CLI::logging(CLI::error("Failed to set permissions for $filename") . "\n");
            }
        }
        if (is_dir($filename)) {
            foreach (array_merge(glob($filename . "/*"), glob($filename . "/.*")) as $item) {
                if (basename($item) == "." || basename($item) == "..") {
                    continue;
                }
                WorkspaceTools::dirPerms($item, $owner, $group, $perms);
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
     * @param string $srcWorkspace name of the source workspace
     * @param string $dstWorkspace name of the destination workspace
     * @param string $overwrite if you need overwrite the database
     * @param string $lang for define the language
     * @param string $port of database if is empty take 3306
     */
    public static function restore($filename, $srcWorkspace, $dstWorkspace = null, $overwrite = true, $lang = 'en', $port = '')
    {
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
                return WorkspaceTools::restoreLegacy($tempDirectory);
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
        if (isset($srcWorkspace) && !in_array("$srcWorkspace.meta", array_map('basename', $metaFiles))) {
            throw new Exception("Workspace $srcWorkspace not found in backup");
        }

        $version = System::getVersion();
        $pmVersion = (preg_match("/^([\d\.]+).*$/", $version, $arrayMatch)) ? $arrayMatch[1] : ""; //Otherwise: Branch master

        CLI::logging(CLI::warning("
            Warning: A workspace from a newer version of ProcessMaker can NOT be restored in an older version of
            ProcessMaker. For example, restoring from v.3.0 to v.2.5 will not work. However, it may be possible
            to restore a workspace from an older version to an newer version of ProcessMaker, although error
            messages may be displayed during the restore process.") . "\n");

        foreach ($metaFiles as $metaFile) {
            $metadata = preg_replace('/\r|\n/', '', file_get_contents($metaFile));
            $metadata = G::json_decode(preg_replace('/\s+/', '', $metadata));
            if ($metadata->version != 1) {
                throw new Exception("Backup version {$metadata->version} not supported");
            }
            $backupWorkspace = $metadata->WORKSPACE_NAME;

            if (strpos($metadata->DB_RBAC_NAME, 'rb_') === false) {
                $onedb = true;
                $oldDatabases = 1;
            } else {
                $onedb = false;
                $oldDatabases = 3;
            }

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
            $workspace = new WorkspaceTools($workspaceName);

            if (Installer::isset_site($workspaceName)) {
                if ($overwrite) {
                    if (!$workspace->workspaceExists()) {
                        throw new Exception('We can not overwrite this workspace because the workspace ' . $workspaceName . ' does not exist please check the lower case and upper case.');
                    }
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

                if (file_exists("$tempDirectory/$dir" . "/ee")) {
                    G::rm_dir("$tempDirectory/$dir" . "/ee");
                }
                if (file_exists("$tempDirectory/$dir" . "/plugin.singleton")) {
                    G::rm_dir("$tempDirectory/$dir" . "/plugin.singleton");
                }
                if (!rename("$tempDirectory/$dir", $workspace->path)) {
                    throw new Exception("There was an error copying the backup files ($tempDirectory/$dir) to the workspace directory {$workspace->path}.");
                }
            }

            CLI::logging("> Changing file permissions\n");
            $shared_stat = stat(PATH_DATA);

            if ($shared_stat !== false) {
                WorkspaceTools::dirPerms($workspace->path, $shared_stat['uid'], $shared_stat['gid'], $shared_stat['mode']);
            } else {
                CLI::logging(CLI::error("Could not get the shared folder permissions, not changing workspace permissions") . "\n");
            }
            list($dbHost, $dbUser, $dbPass) = @explode(SYSTEM_HASH, G::decrypt(HASH_INSTALLATION, SYSTEM_HASH));
            if ($port != '') {
                $dbHost = $dbHost . $port; //127.0.0.1:3306
            }
            $aParameters = array('dbHost' => $dbHost, 'dbUser' => $dbUser, 'dbPass' => $dbPass);

            //Restore
            if (!defined("SYS_SYS")) {
                define("SYS_SYS", $workspaceName);
            }

            if (!defined("PATH_DATA_SITE")) {
                define("PATH_DATA_SITE", PATH_DATA . "sites" . PATH_SEP . SYS_SYS . PATH_SEP);
            }

            $pmVersionWorkspaceToRestore = (preg_match("/^([\d\.]+).*$/", $metadata->PM_VERSION, $arrayMatch)) ? $arrayMatch[1] : "";

            CLI::logging("> Connecting to system database in '$dbHost'\n");
            $link = mysql_connect($dbHost, $dbUser, $dbPass);
            @mysql_query("SET NAMES 'utf8';");
            @mysql_query("SET FOREIGN_KEY_CHECKS=0;");
            @mysql_query("SET GLOBAL log_bin_trust_routine_creators = 1;");

            if (!$link) {
                throw new Exception('Could not connect to system database: ' . mysql_error());
            }

            $dbName = '';
            $newDBNames = $workspace->resetDBInfo($dbHost, $createWorkspace, $onedb);

            foreach ($metadata->databases as $db) {
                if ($dbName != $newDBNames[$db->name]) {
                    $dbName = $dbUser = $newDBNames[$db->name];
                    if (isset($newDBNames['DB_USER'])) {
                        $dbUser = $newDBNames['DB_USER'];
                    }
                    if (mysql_select_db($dbName, $link)) {
                        if (!$overwrite) {
                            throw new Exception("Destination Database already exist (use -o to overwrite)");
                        }
                    }

                    CLI::logging("+> Restoring database {$db->name} to $dbName\n");
                    $versionBackupEngine = (isset($metadata->backupEngineVersion)) ? $metadata->backupEngineVersion : 1;
                    $workspace->executeSQLScript($dbName, "$tempDirectory/{$db->name}.sql", $aParameters, $versionBackupEngine);
                    $workspace->createDBUser($dbUser, ($workspace->dbGrantUserPassword != '' ? $workspace->dbGrantUserPassword : $db->pass), "localhost", $dbName);
                    $workspace->createDBUser($dbUser, ($workspace->dbGrantUserPassword != '' ? $workspace->dbGrantUserPassword : $db->pass), "%", $dbName);
                }
            }

            if (($pmVersionWorkspaceToRestore != '') && (version_compare($pmVersionWorkspaceToRestore . "",
                        $pmVersion . "", "<") || $pmVersion == "")
            ) {
                $start = microtime(true);
                CLI::logging("> Updating database...\n");
                $workspace->upgradeDatabase($onedb);
                $stop = microtime(true);
                CLI::logging("<*>   Database Upgrade Process took " . ($stop - $start) . " seconds.\n");
            }

            $start = microtime(true);
            CLI::logging("> Verify files enterprise old...\n");
            $workspace->verifyFilesOldEnterprise($workspaceName);
            $stop = microtime(true);
            CLI::logging("<*>   Verify took " . ($stop - $start) . " seconds.\n");

            $start = microtime(true);
            CLI::logging("> Verify License Enterprise...\n");
            $workspace->verifyLicenseEnterprise($workspaceName);
            $stop = microtime(true);
            CLI::logging("<*>   Verify took " . ($stop - $start) . " seconds.\n");

            $start = microtime(true);
            CLI::logging("> Check Mafe Requirements...\n");
            $workspace->checkMafeRequirements($workspaceName, $lang);
            $stop = microtime(true);
            CLI::logging("<*>   Check Mafe Requirements Process took " . ($stop - $start) . " seconds.\n");

            if (($pmVersionWorkspaceToRestore != '') && (version_compare($pmVersionWorkspaceToRestore . "",
                        $pmVersion . "", "<") || $pmVersion == "")
            ) {
                $start = microtime(true);
                CLI::logging("> Updating cache view...\n");
                $workspace->upgradeCacheView(true, true, $lang);
                $stop = microtime(true);
                CLI::logging("<*>   Updating cache view Process took " . ($stop - $start) . " seconds.\n");
            } else {
                $workspace->upgradeTriggersOfTables(true, $lang);
            }

            if ($pmVersion == '' && strpos(strtoupper($version), 'BRANCH')) {
                $pmVersion = 'dev-version-backup';
            }

            //Move the labels of content to the corresponding table
            $start = microtime(true);
            CLI::logging("> Optimizing content data...\n");
            $workspace->migrateContent($workspace->name, $lang);
            $stop = microtime(true);
            CLI::logging("<*>   Optimizing content data took " . ($stop - $start) . " seconds.\n");

            //Move the data of cases to the corresponding List
            /*----------------------------------********---------------------------------*/

            $start = microtime(true);
            CLI::logging("> Updating Files Manager...\n");
            $workspace->processFilesUpgrade();
            $stop = microtime(true);
            CLI::logging("<*>   Updating Files Manager took " . ($stop - $start) . " seconds.\n");

            //Populate the new fields for replace string UID to Interger ID
            $start = microtime(true);
            CLI::logging("> Migrating and populating indexing for APP_CACHE_VIEW...\n");
            $workspace->migratePopulateIndexingACV($workspace->name);
            $stop = microtime(true);
            CLI::logging("<*>   Migrating an populating indexing for APP_CACHE_VIEW process took " . ($stop - $start) . " seconds.\n");

            //Updating generated class files for PM Tables
            passthru('./processmaker regenerate-pmtable-classes ' . $workspace->name);

            mysql_close($link);
        }

        CLI::logging("Removing temporary files\n");

        G::rm_dir($tempDirectory);

        CLI::logging(CLI::info("Done restoring") . "\n");
    }

    public static function hotfixInstall($file)
    {
        $result = array();

        $dirHotfix = PATH_DATA . "hotfixes";

        $arrayPathInfo = pathinfo($file);

        $f = ($arrayPathInfo["dirname"] == ".") ? $dirHotfix . PATH_SEP . $file : $file;

        $swv = 1;
        $msgv = "";

        if (!file_exists($dirHotfix)) {
            G::mk_dir($dirHotfix, 0777);
        }

        if (!file_exists($f)) {
            $swv = 0;
            $msgv = $msgv . (($msgv != "") ? "\n" : null) . "- The file \"$f\" does not exist";
        }

        if ($arrayPathInfo["extension"] != "tar") {
            $swv = 0;
            $msgv = $msgv . (($msgv != "") ? "\n" : null) . "- The file extension \"$file\" is not \"tar\"";
        }

        if ($swv == 1) {
            //Extract
            $tar = new Archive_Tar($f);

            $swTar = $tar->extractModify(PATH_TRUNK, "processmaker"); //true on success, false on error

            if ($swTar) {
                $result["status"] = 1;
                $result["message"] = "- Hotfix installed successfully \"$f\"";
            } else {
                $result["status"] = 0;
                $result["message"] = "- Could not extract file \"$f\"";
            }
        } else {
            $result["status"] = 0;
            $result["message"] = $msgv;
        }

        return $result;
    }

    public function backupLogFiles()
    {
        $config = System::getSystemConfiguration();

        clearstatcache();
        $path = PATH_DATA . "log" . PATH_SEP;
        $filePath = $path . "cron.log";
        if (file_exists($filePath)) {
            $size = filesize($filePath);
            /* $config['size_log_file'] has the value 5000000 -> approximately 5 megabytes */
            if ($size > $config['size_log_file']) {
                rename($filePath, $filePath . ".bak");
            }
        }
    }

    public function checkMafeRequirements($workspace, $lang)
    {
        $this->initPropel(true);
        $pmRestClient = OauthClientsPeer::retrieveByPK('x-pm-local-client');
        if (empty($pmRestClient)) {
            if (!is_file(PATH_DATA . 'sites/' . $workspace . '/' . '.server_info')) {
                $_CSERVER = $_SERVER;
                unset($_CSERVER['REQUEST_TIME']);
                unset($_CSERVER['REMOTE_PORT']);
                $cput = serialize($_CSERVER);
                file_put_contents(PATH_DATA . 'sites/' . $workspace . '/' . '.server_info', $cput);
            }
            if (is_file(PATH_DATA . 'sites/' . $workspace . '/' . '.server_info')) {
                $SERVER_INFO = file_get_contents(PATH_DATA . 'sites/' . $workspace . '/'.'.server_info');
                $SERVER_INFO = unserialize($SERVER_INFO);

                $envFile = PATH_CONFIG . 'env.ini';
                $skin ='neoclassic';
                if (file_exists($envFile)) {
                    $sysConf = System::getSystemConfiguration($envFile);
                    $lang = $sysConf['default_lang'];
                    $skin = $sysConf['default_skin'];
                }

                $endpoint = sprintf(
                    '%s/sys%s/%s/%s/oauth2/grant',
                    isset($SERVER_INFO['HTTP_ORIGIN']) ? $SERVER_INFO['HTTP_ORIGIN'] : '',
                    $workspace,
                    $lang,
                    $skin
                );

                $oauthClients = new OauthClients();
                $oauthClients->setClientId('x-pm-local-client');
                $oauthClients->setClientSecret('179ad45c6ce2cb97cf1029e212046e81');
                $oauthClients->setClientName('PM Web Designer');
                $oauthClients->setClientDescription('ProcessMaker Web Designer App');
                $oauthClients->setClientWebsite('www.processmaker.com');
                $oauthClients->setRedirectUri($endpoint);
                $oauthClients->save();
            } else {
                eprintln("WARNING! No server info found!", 'red');
            }
        }
    }

    public function changeHashPassword($workspace, $response)
    {
        $this->initPropel(true);
        $licensedFeatures = & PMLicensedFeatures::getSingleton();
        /*----------------------------------********---------------------------------*/
        return true;
    }

    public function verifyFilesOldEnterprise($workspace)
    {
        $this->initPropel(true);
        $pathBackup = PATH_DATA . 'backups';
        if (!file_exists($pathBackup)) {
            G::mk_dir($pathBackup, 0777);
        }
        $pathNewFile = PATH_DATA . 'backups' . PATH_SEP . 'enterpriseBackup';
        $pathDirectoryEnterprise = PATH_CORE . 'plugins' . PATH_SEP . 'enterprise';
        $pathFileEnterprise = PATH_CORE . 'plugins' . PATH_SEP . 'enterprise.php';

        if (!file_exists($pathDirectoryEnterprise) && !file_exists($pathFileEnterprise)) {
            CLI::logging("    Without changes... \n");
            return true;
        }
        CLI::logging("    Migrating Enterprise Core version...\n");
        if (!file_exists($pathNewFile)) {
            CLI::logging("    Creating folder in $pathNewFile\n");
            G::mk_dir($newDiretory, 0777);
        }
        $shared_stat = stat(PATH_DATA);
        if (file_exists($pathDirectoryEnterprise)) {
            CLI::logging("    Copying Enterprise Directory to $pathNewFile...\n");

            if ($shared_stat !== false) {
                WorkspaceTools::dirPerms($pathDirectoryEnterprise, $shared_stat['uid'], $shared_stat['gid'], $shared_stat['mode']);
            } else {
                CLI::logging(CLI::error("Could not get shared folder permissions, workspace permissions couldn't be changed") . "\n");
            }
            if (G::recursive_copy($pathDirectoryEnterprise, $pathNewFile . PATH_SEP. 'enterprise')) {
                CLI::logging("    Removing $pathDirectoryEnterprise...\n");
                G::rm_dir($pathDirectoryEnterprise);
            } else {
                CLI::logging(CLI::error("    Error: Failure to copy from $pathDirectoryEnterprise...\n"));
            }
            if (file_exists($pathDirectoryEnterprise)) {
                CLI::logging(CLI::info("    Remove manually $pathDirectoryEnterprise...\n"));
            }
        }
        if (file_exists($pathFileEnterprise)) {
            CLI::logging("    Copying Enterprise.php file to $pathNewFile...\n");
            if ($shared_stat !== false) {
                WorkspaceTools::dirPerms($pathFileEnterprise, $shared_stat['uid'], $shared_stat['gid'], $shared_stat['mode']);
            } else {
                CLI::logging(CLI::error("Could not get shared folder permissions, workspace permissions couldn't be changed") . "\n");
            }
            CLI::logging("    Removing $pathFileEnterprise...\n");
            copy($pathFileEnterprise, $pathNewFile. PATH_SEP . 'enterprise.php');
            G::rm_dir($pathFileEnterprise);
            if (file_exists($pathFileEnterprise)) {
                CLI::logging(CLI::info("    Remove manually $pathFileEnterprise...\n"));
            }
        }
    }

    /**
     * @param $workspace
     */
    public function verifyLicenseEnterprise($workspace)
    {
        $this->initPropel(true);
        $oCriteria = new Criteria('workflow');
        $oCriteria->add(LicenseManagerPeer::LICENSE_STATUS, 'ACTIVE');
        $oDataset = LicenseManagerPeer::doSelectRS($oCriteria);
        $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        if ($oDataset->next()) {
            $row = $oDataset->getRow();
            $tr = LicenseManagerPeer::retrieveByPK($row['LICENSE_UID']);
            $tr->setLicensePath(PATH_DATA_SITE . basename($row['LICENSE_PATH']));
            $tr->setLicenseWorkspace($workspace);
            $res = $tr->save();
        }
    }

    /**
     * Generate data for table APP_ASSIGN_SELF_SERVICE_VALUE
     *
     * return void
     */
    public function appAssignSelfServiceValueTableGenerateData()
    {
        try {
            $this->initPropel(true);

            $appAssignSelfServiceValue = new AppAssignSelfServiceValue();
            $appAssignSelfServiceValue->generateData();
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Get disabled code
     *
     * @return array Returns an array with disabled code found, array empty otherwise
     */
    public function getDisabledCode()
    {
        try {
            $this->initPropel(true);

            $process = new Processes();

            //Return
            return $process->getDisabledCode(null, $this->name);
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Migrate all cases to New list
     *
     * @param string $workSpace    Workspace
     * @param bool   $flagReinsert Flag that specifies the re-insertion
     *
     * @return void
     */
    public function migrateList($workSpace, $flagReinsert = false, $lang='en')
    {
        $this->initPropel(true);

        $flagListAll        = $this->listFirstExecution('check');
        $flagListUnassigned = $this->listFirstExecution('check', 'unassigned');

        if (!$flagReinsert && $flagListAll && $flagListUnassigned) {
            return;
        }

        $arrayTable1 = ['ListInbox', 'ListMyInbox', 'ListCanceled', 'ListParticipatedLast', 'ListParticipatedHistory', 'ListPaused', 'ListCompleted'];
        $arrayTable2 = ['ListUnassigned', 'ListUnassignedGroup'];
        $arrayTable = array_merge($arrayTable1, $arrayTable2);

        if ($flagReinsert) {
            //Delete all records
            foreach ($arrayTable as $value) {
                $tableName = $value . 'Peer';
                $list = new $tableName();
                $list->doDeleteAll();
            }
        }

        if (!$flagReinsert && !$flagListAll) {
            foreach ($arrayTable1 as $value) {
                $tableName = $value . 'Peer';
                $list = new $tableName();

                if ((int)($list->doCount(new Criteria())) > 0) {
                    $flagListAll = true;
                    break;
                }
            }
        }

        if ($flagReinsert || !$flagListAll) {
            $this->regenerateListCompleted($lang);
            $this->regenerateListCanceled($lang);
            $this->regenerateListMyInbox(); //This list require no translation
            $this->regenerateListInbox();   //This list require no translation
            $this->regenerateListParticipatedHistory(); //This list require no translation
            $this->regenerateListParticipatedLast();    //This list require no translation
            $this->regenerateListPaused(); //This list require no translation
        }

        if ($flagReinsert || !$flagListUnassigned) {
            $this->regenerateListUnassigned(); //This list require no translation
        }

        $this->listFirstExecution('insert');
        $this->listFirstExecution('insert', 'unassigned');
    }

    public function regenerateListCanceled($lang = 'en')
    {
        $this->initPropel(true);
        $query = 'INSERT INTO '.$this->dbName.'.LIST_CANCELED
                    (APP_UID,
                    USR_UID,
                    TAS_UID,
                    PRO_UID,
                    APP_NUMBER,
                    APP_TITLE,
                    APP_PRO_TITLE,
                    APP_TAS_TITLE,
                    APP_CANCELED_DATE,
                    DEL_INDEX,
                    DEL_PREVIOUS_USR_UID,
                    DEL_CURRENT_USR_USERNAME,
                    DEL_CURRENT_USR_FIRSTNAME,
                    DEL_CURRENT_USR_LASTNAME,
                    DEL_DELEGATE_DATE,
                    DEL_INIT_DATE,
                    DEL_DUE_DATE,
                    DEL_PRIORITY)
                    SELECT
                        ACV.APP_UID,
                        ACV.USR_UID,
                        ACV.TAS_UID,
                        ACV.PRO_UID,
                        ACV.APP_NUMBER,
                        C_APP.CON_VALUE AS APP_TITLE,
                        C_PRO.CON_VALUE AS APP_PRO_TITLE,
                        C_TAS.CON_VALUE AS APP_TAS_TITLE,
                        NOW() AS APP_CANCELED_DATE,
                        ACV.DEL_INDEX,
                        PREV_AD.USR_UID AS DEL_PREVIOUS_USR_UID,
                        USR.USR_USERNAME AS DEL_CURRENT_USR_USERNAME,
                        USR.USR_FIRSTNAME AS DEL_CURRENT_USR_FIRSTNAME,
                        USR.USR_LASTNAME AS DEL_CURRENT_USR_LASTNAME,
                        AD.DEL_DELEGATE_DATE AS DEL_DELEGATE_DATE,
                        AD.DEL_INIT_DATE AS DEL_INIT_DATE,
                        AD.DEL_TASK_DUE_DATE AS DEL_DUE_DATE,
                        ACV.DEL_PRIORITY
                    FROM
                        ('.$this->dbName.'.APP_CACHE_VIEW ACV
                        LEFT JOIN '.$this->dbName.'.CONTENT C_APP ON ACV.APP_UID = C_APP.CON_ID
                            AND C_APP.CON_CATEGORY = \'APP_TITLE\'
                            AND C_APP.CON_LANG = \''.$lang.'\'
                        LEFT JOIN '.$this->dbName.'.CONTENT C_PRO ON ACV.PRO_UID = C_PRO.CON_ID
                            AND C_PRO.CON_CATEGORY = \'PRO_TITLE\'
                            AND C_PRO.CON_LANG = \''.$lang.'\'
                        LEFT JOIN '.$this->dbName.'.CONTENT C_TAS ON ACV.TAS_UID = C_TAS.CON_ID
                            AND C_TAS.CON_CATEGORY = \'TAS_TITLE\'
                            AND C_TAS.CON_LANG = \''.$lang.'\')
                            LEFT JOIN
                        ('.$this->dbName.'.APP_DELEGATION AD
                        INNER JOIN '.$this->dbName.'.APP_DELEGATION PREV_AD ON AD.APP_UID = PREV_AD.APP_UID
                            AND AD.DEL_PREVIOUS = PREV_AD.DEL_INDEX) ON ACV.APP_UID = AD.APP_UID
                            AND ACV.DEL_INDEX = AD.DEL_INDEX
                            LEFT JOIN
                        '.$this->dbName.'.USERS USR ON ACV.USR_UID = USR.USR_UID
                    WHERE
                        ACV.APP_STATUS = \'CANCELLED\'
                            AND ACV.DEL_LAST_INDEX = 1';
        $con = Propel::getConnection("workflow");
        $stmt = $con->createStatement();
        $stmt->executeQuery($query);
        CLI::logging("> Completed table LIST_CANCELED\n");
    }

    public function regenerateListCompleted($lang = 'en')
    {
        $this->initPropel(true);
        $query = 'INSERT INTO '.$this->dbName.'.LIST_COMPLETED
                    (APP_UID,
                    USR_UID,
                    TAS_UID,
                    PRO_UID,
                    APP_NUMBER,
                    APP_TITLE,
                    APP_PRO_TITLE,
                    APP_TAS_TITLE,
                    APP_CREATE_DATE,
                    APP_FINISH_DATE,
                    DEL_INDEX,
                    DEL_PREVIOUS_USR_UID,
                    DEL_CURRENT_USR_USERNAME,
                    DEL_CURRENT_USR_FIRSTNAME,
                    DEL_CURRENT_USR_LASTNAME)

                    SELECT
                        ACV.APP_UID,
                        ACV.USR_UID,
                        ACV.TAS_UID,
                        ACV.PRO_UID,
                        ACV.APP_NUMBER,
                        C_APP.CON_VALUE AS APP_TITLE,
                        C_PRO.CON_VALUE AS APP_PRO_TITLE,
                        C_TAS.CON_VALUE AS APP_TAS_TITLE,
                        ACV.APP_CREATE_DATE,
                        ACV.APP_FINISH_DATE,
                        ACV.DEL_INDEX,
                        PREV_AD.USR_UID AS DEL_PREVIOUS_USR_UID,
                        USR.USR_USERNAME AS DEL_CURRENT_USR_USERNAME,
                        USR.USR_FIRSTNAME AS DEL_CURRENT_USR_FIRSTNAME,
                        USR.USR_LASTNAME AS DEL_CURRENT_USR_LASTNAME
                    FROM
                        ('.$this->dbName.'.APP_CACHE_VIEW ACV
                        LEFT JOIN '.$this->dbName.'.CONTENT C_APP ON ACV.APP_UID = C_APP.CON_ID
                            AND C_APP.CON_CATEGORY = \'APP_TITLE\'
                            AND C_APP.CON_LANG = \''.$lang.'\'
                        LEFT JOIN '.$this->dbName.'.CONTENT C_PRO ON ACV.PRO_UID = C_PRO.CON_ID
                            AND C_PRO.CON_CATEGORY = \'PRO_TITLE\'
                            AND C_PRO.CON_LANG = \''.$lang.'\'
                        LEFT JOIN '.$this->dbName.'.CONTENT C_TAS ON ACV.TAS_UID = C_TAS.CON_ID
                            AND C_TAS.CON_CATEGORY = \'TAS_TITLE\'
                            AND C_TAS.CON_LANG = \''.$lang.'\')
                            LEFT JOIN
                        ('.$this->dbName.'.APP_DELEGATION AD
                        INNER JOIN '.$this->dbName.'.APP_DELEGATION PREV_AD ON AD.APP_UID = PREV_AD.APP_UID
                            AND AD.DEL_PREVIOUS = PREV_AD.DEL_INDEX) ON ACV.APP_UID = AD.APP_UID
                            AND ACV.DEL_INDEX = AD.DEL_INDEX
                            LEFT JOIN
                        '.$this->dbName.'.USERS USR ON ACV.USR_UID = USR.USR_UID
                    WHERE
                        ACV.APP_STATUS = \'COMPLETED\'
                            AND ACV.DEL_LAST_INDEX = 1';
        $con = Propel::getConnection("workflow");
        $stmt = $con->createStatement();
        $stmt->executeQuery($query);
        CLI::logging("> Completed table LIST_COMPLETED\n");
    }

    public function regenerateListMyInbox()
    {
        $this->initPropel(true);
        $query = 'INSERT INTO '.$this->dbName.'.LIST_MY_INBOX
                    (APP_UID,
                    USR_UID,
                    TAS_UID,
                    PRO_UID,
                    APP_NUMBER,
                    APP_TITLE,
                    APP_PRO_TITLE,
                    APP_TAS_TITLE,
                    APP_CREATE_DATE,
                    APP_UPDATE_DATE,
                    APP_FINISH_DATE,
                    APP_STATUS,
                    DEL_INDEX,
                    DEL_PREVIOUS_USR_UID,
                    DEL_PREVIOUS_USR_USERNAME,
                    DEL_PREVIOUS_USR_FIRSTNAME,
                    DEL_PREVIOUS_USR_LASTNAME,
                    DEL_CURRENT_USR_UID,
                    DEL_CURRENT_USR_USERNAME,
                    DEL_CURRENT_USR_FIRSTNAME,
                    DEL_CURRENT_USR_LASTNAME,
                    DEL_DELEGATE_DATE,
                    DEL_INIT_DATE,
                    DEL_DUE_DATE,
                    DEL_PRIORITY)

                    SELECT
                        ACV.APP_UID,
                        ACV.USR_UID,
                        ACV.TAS_UID,
                        ACV.PRO_UID,
                        ACV.APP_NUMBER,
                        ACV.APP_TITLE,
                        ACV.APP_PRO_TITLE,
                        ACV.APP_TAS_TITLE,
                        ACV.APP_CREATE_DATE,
                        ACV.APP_UPDATE_DATE,
                        ACV.APP_FINISH_DATE,
                        ACV.APP_STATUS,
                        ACV.DEL_INDEX,
                        ACV.PREVIOUS_USR_UID AS DEL_PREVIOUS_USR_UID,
                        PRE_USR.USR_USERNAME AS DEL_PREVIOUS_USR_USERNAME,
                        PRE_USR.USR_FIRSTNAME AS DEL_PREVIOUS_USR_FIRSTNAME,
                        PRE_USR.USR_LASTNAME AS DEL_PREVIOUS_USR_LASTNAME,
                        ACV.USR_UID AS DEL_CURRENT_USR_UID,
                        CUR_USR.USR_USERNAME AS DEL_CURRENT_USR_USERNAME,
                        CUR_USR.USR_FIRSTNAME AS DEL_CURRENT_USR_FIRSTNAME,
                        CUR_USR.USR_LASTNAME AS DEL_CURRENT_USR_LASTNAME,
                        ACV.DEL_DELEGATE_DATE AS DEL_DELEGATE_DATE,
                        ACV.DEL_INIT_DATE AS DEL_INIT_DATE,
                        ACV.DEL_TASK_DUE_DATE AS DEL_DUE_DATE,
                        ACV.DEL_PRIORITY
                    FROM
                        '.$this->dbName.'.APP_CACHE_VIEW ACV
                            LEFT JOIN
                        '.$this->dbName.'.USERS CUR_USR ON ACV.USR_UID = CUR_USR.USR_UID
                            LEFT JOIN
                        '.$this->dbName.'.USERS PRE_USR ON ACV.PREVIOUS_USR_UID = PRE_USR.USR_UID
                    WHERE ACV.DEL_INDEX=1';

        $con = Propel::getConnection("workflow");
        $stmt = $con->createStatement();
        $stmt->executeQuery($query);
        CLI::logging("> Completed table LIST_MY_INBOX\n");
    }

    public function regenerateListInbox()
    {
        $this->initPropel(true);
        $query = 'INSERT INTO '.$this->dbName.'.LIST_INBOX
                    (APP_UID,
                    DEL_INDEX,
                    USR_UID,
                    TAS_UID,
                    PRO_UID,
                    APP_NUMBER,
                    APP_STATUS,
                    APP_TITLE,
                    APP_PRO_TITLE,
                    APP_TAS_TITLE,
                    APP_UPDATE_DATE,
                    DEL_PREVIOUS_USR_UID,
                    DEL_PREVIOUS_USR_USERNAME,
                    DEL_PREVIOUS_USR_FIRSTNAME,
                    DEL_PREVIOUS_USR_LASTNAME,
                    DEL_DELEGATE_DATE,
                    DEL_INIT_DATE,
                    DEL_DUE_DATE,
                    DEL_RISK_DATE,
                    DEL_PRIORITY)

                    SELECT
                        ACV.APP_UID,
                        ACV.DEL_INDEX,
                        ACV.USR_UID,
                        ACV.TAS_UID,
                        ACV.PRO_UID,
                        ACV.APP_NUMBER,
                        ACV.APP_STATUS,
                        ACV.APP_TITLE,
                        ACV.APP_PRO_TITLE,
                        ACV.APP_TAS_TITLE,
                        ACV.APP_UPDATE_DATE,
                        ACV.PREVIOUS_USR_UID AS DEL_PREVIOUS_USR_UID,
                        USR.USR_USERNAME AS DEL_PREVIOUS_USR_USERNAME,
                        USR.USR_FIRSTNAME AS DEL_PREVIOUS_USR_FIRSTNAME,
                        USR.USR_LASTNAME AS DEL_PREVIOUS_USR_LASTNAME,
                        ACV.DEL_DELEGATE_DATE AS DEL_DELEGATE_DATE,
                        ACV.DEL_INIT_DATE AS DEL_INIT_DATE,
                        ACV.DEL_TASK_DUE_DATE AS DEL_DUE_DATE,
                        ACV.DEL_RISK_DATE AS DEL_RISK_DATE,
                        ACV.DEL_PRIORITY
                    FROM
                        '.$this->dbName.'.APP_CACHE_VIEW ACV
                            LEFT JOIN
                        '.$this->dbName.'.USERS USR ON ACV.PREVIOUS_USR_UID = USR.USR_UID
                    WHERE
                        ACV.DEL_THREAD_STATUS = \'OPEN\'';
        $con = Propel::getConnection("workflow");
        $stmt = $con->createStatement();
        $stmt->executeQuery($query);
        CLI::logging("> Completed table LIST_INBOX\n");
    }

    public function regenerateListParticipatedHistory()
    {
        $this->initPropel(true);
        $query = 'INSERT INTO '.$this->dbName.'.LIST_PARTICIPATED_HISTORY
                    (APP_UID,
                    DEL_INDEX,
                    USR_UID,
                    TAS_UID,
                    PRO_UID,
                    APP_NUMBER,
                    APP_TITLE,
                    APP_PRO_TITLE,
                    APP_TAS_TITLE,
                    DEL_PREVIOUS_USR_UID,
                    DEL_PREVIOUS_USR_USERNAME,
                    DEL_PREVIOUS_USR_FIRSTNAME,
                    DEL_PREVIOUS_USR_LASTNAME,
                    DEL_CURRENT_USR_USERNAME,
                    DEL_CURRENT_USR_FIRSTNAME,
                    DEL_CURRENT_USR_LASTNAME,
                    DEL_DELEGATE_DATE,
                    DEL_INIT_DATE,
                    DEL_DUE_DATE,
                    DEL_PRIORITY)

                    SELECT
                        ACV.APP_UID,
                        ACV.DEL_INDEX,
                        ACV.USR_UID,
                        ACV.TAS_UID,
                        ACV.PRO_UID,
                        ACV.APP_NUMBER,
                        ACV.APP_TITLE,
                        ACV.APP_PRO_TITLE,
                        ACV.APP_TAS_TITLE,
                        ACV.PREVIOUS_USR_UID AS DEL_PREVIOUS_USR_UID,
                        PRE_USR.USR_USERNAME AS DEL_PREVIOUS_USR_USERNAME,
                        PRE_USR.USR_FIRSTNAME AS DEL_PREVIOUS_USR_FIRSTNAME,
                        PRE_USR.USR_LASTNAME AS DEL_PREVIOUS_USR_LASTNAME,
                        CUR_USR.USR_USERNAME AS DEL_CURRENT_USR_USERNAME,
                        CUR_USR.USR_FIRSTNAME AS DEL_CURRENT_USR_FIRSTNAME,
                        CUR_USR.USR_LASTNAME AS DEL_CURRENT_USR_LASTNAME,
                        ACV.DEL_DELEGATE_DATE AS DEL_DELEGATE_DATE,
                        ACV.DEL_INIT_DATE AS DEL_INIT_DATE,
                        ACV.DEL_TASK_DUE_DATE AS DEL_DUE_DATE,
                        ACV.DEL_PRIORITY
                    FROM
                        '.$this->dbName.'.APP_CACHE_VIEW ACV
                            LEFT JOIN
                        '.$this->dbName.'.USERS CUR_USR ON ACV.USR_UID = CUR_USR.USR_UID
                            LEFT JOIN
                        '.$this->dbName.'.USERS PRE_USR ON ACV.PREVIOUS_USR_UID = PRE_USR.USR_UID';
        $con = Propel::getConnection("workflow");
        $stmt = $con->createStatement();
        $stmt->executeQuery($query);
        CLI::logging("> Completed table LIST_PARTICIPATED_HISTORY\n");
    }

    public function regenerateListParticipatedLast()
    {
        $this->initPropel(true);
        $query = 'INSERT INTO ' . $this->dbName . '.LIST_PARTICIPATED_LAST
                    (
                      APP_UID,
                      USR_UID,
                      DEL_INDEX,
                      TAS_UID,
                      PRO_UID,
                      APP_NUMBER,
                      APP_TITLE,
                      APP_PRO_TITLE,
                      APP_TAS_TITLE,
                      APP_STATUS,
                      DEL_PREVIOUS_USR_UID,
                      DEL_PREVIOUS_USR_USERNAME,
                      DEL_PREVIOUS_USR_FIRSTNAME,
                      DEL_PREVIOUS_USR_LASTNAME,
                      DEL_CURRENT_USR_USERNAME,
                      DEL_CURRENT_USR_FIRSTNAME,
                      DEL_CURRENT_USR_LASTNAME,
                      DEL_DELEGATE_DATE,
                      DEL_INIT_DATE,
                      DEL_DUE_DATE,
                      DEL_CURRENT_TAS_TITLE,
                      DEL_PRIORITY,
                      DEL_THREAD_STATUS)
                    
                      SELECT
                        ACV.APP_UID,
                        IF(ACV.USR_UID=\'\', \'SELF_SERVICES\', ACV.USR_UID),
                        ACV.DEL_INDEX,
                        ACV.TAS_UID,
                        ACV.PRO_UID,
                        ACV.APP_NUMBER,
                        ACV.APP_TITLE,
                        ACV.APP_PRO_TITLE,
                        ACV.APP_TAS_TITLE,
                        ACV.APP_STATUS,
                        DEL_PREVIOUS_USR_UID,
                        IFNULL(PRE_USR.USR_USERNAME, CUR_USR.USR_USERNAME)   AS DEL_PREVIOUS_USR_USERNAME,
                        IFNULL(PRE_USR.USR_FIRSTNAME, CUR_USR.USR_FIRSTNAME) AS DEL_PREVIOUS_USR_USERNAME,
                        IFNULL(PRE_USR.USR_LASTNAME, CUR_USR.USR_LASTNAME)   AS DEL_PREVIOUS_USR_USERNAME,
                        CUR_USR.USR_USERNAME                                 AS DEL_CURRENT_USR_USERNAME,
                        CUR_USR.USR_FIRSTNAME                                AS DEL_CURRENT_USR_FIRSTNAME,
                        CUR_USR.USR_LASTNAME                                 AS DEL_CURRENT_USR_LASTNAME,
                        ACV.DEL_DELEGATE_DATE                                AS DEL_DELEGATE_DATE,
                        ACV.DEL_INIT_DATE                                    AS DEL_INIT_DATE,
                        ACV.DEL_TASK_DUE_DATE                                AS DEL_DUE_DATE,
                        ACV.APP_TAS_TITLE                                    AS DEL_CURRENT_TAS_TITLE,
                        ACV.DEL_PRIORITY,
                        ACV.DEL_THREAD_STATUS
                      FROM
                        (
                          SELECT
                            CASE WHEN ACV1.PREVIOUS_USR_UID = \'\' AND ACV1.DEL_INDEX = 1
                              THEN ACV1.USR_UID
                            ELSE ACV1.PREVIOUS_USR_UID END AS DEL_PREVIOUS_USR_UID,
                            ACV1.*
                          FROM ' . $this->dbName . '.APP_CACHE_VIEW ACV1
                            JOIN
                            (SELECT
                               ACV_INT.APP_UID,
                               MAX(ACV_INT.DEL_INDEX) MAX_DEL_INDEX
                             FROM
                               ' . $this->dbName . '.APP_CACHE_VIEW ACV_INT
                             GROUP BY
                               ACV_INT.USR_UID,
                               ACV_INT.APP_UID
                            ) ACV2
                              ON ACV2.APP_UID = ACV1.APP_UID AND ACV2.MAX_DEL_INDEX = ACV1.DEL_INDEX
                        ) ACV
                        LEFT JOIN ' . $this->dbName . '.USERS PRE_USR ON ACV.PREVIOUS_USR_UID = PRE_USR.USR_UID
                        LEFT JOIN ' . $this->dbName . '.USERS CUR_USR ON ACV.USR_UID = CUR_USR.USR_UID';
        $con = Propel::getConnection("workflow");
        $stmt = $con->createStatement();
        $stmt->executeQuery($query);
        CLI::logging(">  Inserted data into table LIST_PARTICIPATED_LAST\n");
        $query = 'UPDATE ' . $this->dbName . '.LIST_PARTICIPATED_LAST LPL, (
                       SELECT
                         TASK.TAS_TITLE,
                         CUR_USER.APP_UID,
                         USERS.USR_UID,
                         USERS.USR_USERNAME,
                         USERS.USR_FIRSTNAME,
                         USERS.USR_LASTNAME
                       FROM (
                              SELECT
                                APP_UID,
                                TAS_UID,
                                DEL_INDEX,
                                USR_UID
                              FROM ' . $this->dbName . '.APP_DELEGATION
                              WHERE DEL_LAST_INDEX = 1
                            ) CUR_USER
                         LEFT JOIN ' . $this->dbName . '.USERS ON CUR_USER.USR_UID = USERS.USR_UID
                         LEFT JOIN ' . $this->dbName . '.TASK ON CUR_USER.TAS_UID = TASK.TAS_UID) USERS_VALUES
                    SET
                      LPL.DEL_CURRENT_USR_USERNAME  = IFNULL(USERS_VALUES.USR_USERNAME, \'\'),
                      LPL.DEL_CURRENT_USR_FIRSTNAME = IFNULL(USERS_VALUES.USR_FIRSTNAME, \'\'),
                      LPL.DEL_CURRENT_USR_LASTNAME  = IFNULL(USERS_VALUES.USR_LASTNAME, \'\'),
                      LPL.DEL_CURRENT_TAS_TITLE     = IFNULL(USERS_VALUES.TAS_TITLE, \'\')
                    WHERE LPL.APP_UID = USERS_VALUES.APP_UID';
        $con = Propel::getConnection("workflow");
        $stmt = $con->createStatement();
        CLI::logging("> Updating the current users data on table LIST_PARTICIPATED_LAST\n");
        $stmt->executeQuery($query);
        CLI::logging("> Completed table LIST_PARTICIPATED_LAST\n");
    }

    /**
     * This function overwrite the table LIST_PAUSED
     * Get the principal information in the tables appDelay, appDelegation
     * For the labels we use the tables user, process, task and application
     * @return void
     */
    public function regenerateListPaused()
    {
        $this->initPropel(true);
        $query =  'INSERT INTO '.$this->dbName.'.LIST_PAUSED
                  (
                  APP_UID,
                  DEL_INDEX,
                  USR_UID,
                  TAS_UID,
                  PRO_UID,
                  APP_NUMBER,
                  APP_TITLE,
                  APP_PRO_TITLE,
                  APP_TAS_TITLE,
                  APP_PAUSED_DATE,
                  APP_RESTART_DATE,
                  DEL_PREVIOUS_USR_UID,
                  DEL_PREVIOUS_USR_USERNAME,
                  DEL_PREVIOUS_USR_FIRSTNAME,
                  DEL_PREVIOUS_USR_LASTNAME,
                  DEL_CURRENT_USR_USERNAME,
                  DEL_CURRENT_USR_FIRSTNAME,
                  DEL_CURRENT_USR_LASTNAME,
                  DEL_DELEGATE_DATE,
                  DEL_INIT_DATE,
                  DEL_DUE_DATE,
                  DEL_PRIORITY,
                  PRO_ID,
                  USR_ID,
                  TAS_ID
                  )
                  SELECT
                      AD1.APP_UID,
                      AD1.DEL_INDEX,
                      AD1.USR_UID,
                      AD1.TAS_UID,
                      AD1.PRO_UID,
                      AD1.APP_NUMBER,
                      APPLICATION.APP_TITLE,
                      PROCESS.PRO_TITLE,
                      TASK.TAS_TITLE,
                      APP_DELAY.APP_ENABLE_ACTION_DATE AS APP_PAUSED_DATE ,
                      APP_DELAY.APP_DISABLE_ACTION_DATE AS APP_RESTART_DATE,
                      AD2.USR_UID AS DEL_PREVIOUS_USR_UID,
                      PREVIOUS.USR_USERNAME AS DEL_PREVIOUS_USR_USERNAME,
                      PREVIOUS.USR_FIRSTNAME AS DEL_CURRENT_USR_FIRSTNAME,
                      PREVIOUS.USR_LASTNAME AS DEL_PREVIOUS_USR_LASTNAME,
                      USERS.USR_USERNAME AS DEL_CURRENT_USR_USERNAME,
                      USERS.USR_FIRSTNAME AS DEL_CURRENT_USR_FIRSTNAME,
                      USERS.USR_LASTNAME AS DEL_CURRENT_USR_LASTNAME,
                      AD1.DEL_DELEGATE_DATE AS DEL_DELEGATE_DATE,
                      AD1.DEL_INIT_DATE AS DEL_INIT_DATE,
                      AD1.DEL_TASK_DUE_DATE AS DEL_DUE_DATE,
                      AD1.DEL_PRIORITY AS DEL_PRIORITY,
                      PROCESS.PRO_ID,
                      USERS.USR_ID,
                      TASK.TAS_ID
                  FROM
                        '.$this->dbName.'.APP_DELAY
                  LEFT JOIN
                        '.$this->dbName.'.APP_DELEGATION AS AD1 ON (APP_DELAY.APP_NUMBER = AD1.APP_NUMBER AND AD1.DEL_INDEX = APP_DELAY.APP_DEL_INDEX)
                  LEFT JOIN
                        '.$this->dbName.'.APP_DELEGATION AS AD2 ON (AD1.APP_NUMBER = AD2.APP_NUMBER AND AD1.DEL_PREVIOUS = AD2.DEL_INDEX)
                  LEFT JOIN
                        '.$this->dbName.'.USERS ON (APP_DELAY.APP_DELEGATION_USER_ID = USERS.USR_ID)
                  LEFT JOIN
                        '.$this->dbName.'.USERS PREVIOUS ON (AD2.USR_ID = PREVIOUS.USR_ID)
                  LEFT JOIN
                        '.$this->dbName.'.APPLICATION ON (AD1.APP_NUMBER = APPLICATION.APP_NUMBER)
                  LEFT JOIN
                        '.$this->dbName.'.PROCESS ON (AD1.PRO_ID = PROCESS.PRO_ID)
                  LEFT JOIN
                        '.$this->dbName.'.TASK ON (AD1.TAS_ID = TASK.TAS_ID)
                  WHERE
                       APP_DELAY.APP_DISABLE_ACTION_USER = "0" AND
                       APP_DELAY.APP_TYPE = "PAUSE"
               ';
        $con = Propel::getConnection("workflow");
        $stmt = $con->createStatement();
        $stmt->executeQuery($query);
        CLI::logging("> Completed table LIST_PAUSED\n");
    }

    /*----------------------------------********---------------------------------*/

    /**
     * This function checks if List tables are going to migrated
     *
     * return boolean value
     */
    public function listFirstExecution($action, $list='all')
    {
        $this->initPropel(true);
        switch ($action) {
            case 'insert':
                $conf  = new Configuration();
                if ($list==='all') {
                    if (!($conf->exists('MIGRATED_LIST', 'list', 'list', 'list', 'list'))) {
                        $data["CFG_UID"]  ='MIGRATED_LIST';
                        $data["OBJ_UID"]  ='list';
                        $data["CFG_VALUE"]='true';
                        $data["PRO_UID"]  ='list';
                        $data["USR_UID"]  ='list';
                        $data["APP_UID"]  ='list';
                        $conf->create($data);
                    }
                }
                if ($list==='unassigned') {
                    if (!($conf->exists('MIGRATED_LIST_UNASSIGNED', 'list', 'list', 'list', 'list'))) {
                        $data["CFG_UID"]  ='MIGRATED_LIST_UNASSIGNED';
                        $data["OBJ_UID"]  ='list';
                        $data["CFG_VALUE"]='true';
                        $data["PRO_UID"]  ='list';
                        $data["USR_UID"]  ='list';
                        $data["APP_UID"]  ='list';
                        $conf->create($data);
                    }
                }
                return true;
                break;
            case 'check':
                $criteria = new Criteria("workflow");
                $criteria->addSelectColumn(ConfigurationPeer::CFG_UID);
                if ($list==='all') {
                    $criteria->add(ConfigurationPeer::CFG_UID, "MIGRATED_LIST", CRITERIA::EQUAL);
                }
                if ($list==='unassigned') {
                    $criteria->add(ConfigurationPeer::CFG_UID, "MIGRATED_LIST_UNASSIGNED", CRITERIA::EQUAL);
                }
                $rsCriteria = AppCacheViewPeer::doSelectRS($criteria);
                $rsCriteria->setFetchmode(ResultSet::FETCHMODE_ASSOC);
                $aRows = array();
                while ($rsCriteria->next()) {
                    $aRows[] = $rsCriteria->getRow();
                }
                if (empty($aRows)) {
                    return false; //If is false continue with the migrated
                } else {
                    return true; //Stop
                }
                break;
            default:
                return true;
        }
    }

    /**
     * Verify feature
     *
     * @param string $featureName Feature name
     *
     * return bool Return true if is valid the feature, false otherwise
     */
    public function pmLicensedFeaturesVerifyFeature($featureName)
    {
        try {
            $this->initPropel(true);

            $flag = PMLicensedFeatures::getSingleton()->verifyfeature($featureName);

            $this->close();

            //Return
            return $flag;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Process-Files upgrade
     *
     * return void
     */
    public function processFilesUpgrade()
    {
        try {
            if (!defined("PATH_DATA_MAILTEMPLATES")) {
                define("PATH_DATA_MAILTEMPLATES", PATH_DATA_SITE . "mailTemplates" . PATH_SEP);
            }

            if (!defined("PATH_DATA_PUBLIC")) {
                define("PATH_DATA_PUBLIC", PATH_DATA_SITE . "public" . PATH_SEP);
            }

            $this->initPropel(true);

            $filesManager = new \ProcessMaker\BusinessModel\FilesManager();

            $filesManager->processFilesUpgrade();
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Register system tables in a file
     *
     * return void
     */
    public static function registerSystemTables($aSquema)
    {
        //Register all tables
        $sListTables = '';
        foreach ($aSquema as $key => $value) {
            $sListTables .= $key .'|';
        }

        $sysTablesIniFile= PATH_CONFIG . 'system-tables.ini';
        $contents = file_put_contents($sysTablesIniFile, sprintf("%s '%s'\n", "tables = ", $sListTables));
        if ($contents === null) {
            throw (new Exception(G::LoadTranslation('ID_FILE_NOT_WRITEABLE', SYS_LANG, array($sysTablesIniFile))));
        }
    }

    /**
     *return void
     */
    public function checkRbacPermissions()
    {
        CLI::logging("-> Verifying roles permissions in RBAC \n");
        //Update table RBAC permissions
        $RBAC = &RBAC::getSingleton();
        $RBAC->initRBAC();
        $result = $RBAC->verifyPermissions();
        if (count($result) > 1) {
            foreach ($result as $item) {
                CLI::logging("    $item... \n");
            }
        } else {
            CLI::logging("    All roles permissions already updated \n");
        }
    }

    public function checkSequenceNumber()
    {
        $criteria = new Criteria("workflow");
        $rsCriteria = AppSequencePeer::doSelectRS($criteria);
        $rsCriteria->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $rsCriteria->next();
        $appSequenceRow = $rsCriteria->getRow();
        if (empty($appSequenceRow)) {
            $sequenceInstance = SequencesPeer::retrieveByPK("APP_NUMBER");
            $appSequenceInstance = new AppSequence();
            if (!is_null($sequenceInstance)) {
                $sequenceFields = $sequenceInstance->toArray(BasePeer::TYPE_FIELDNAME);
                $appSequenceInstance->updateSequenceNumber($sequenceFields['SEQ_VALUE']);
            } else {
                $appSequenceInstance->updateSequenceNumber(0);
            }
        }
    }

    public function hasMissingUsers()
    {
        $this->initPropel(true);
        file_put_contents(
            PATH_DATA."/missing-users-".$this->name.".txt",
            "Missing Processes List.\n"
        );

        $criteria = new Criteria("workflow");
        $criteria->addSelectColumn(AppCacheViewPeer::APP_UID);
        $criteria->addSelectColumn(AppCacheViewPeer::DEL_INDEX);
        $criteria->addSelectColumn(AppCacheViewPeer::USR_UID);
        $criteria->addJoinMC(array(
            array(AppCacheViewPeer::USR_UID, UsersPeer::USR_UID)),
            Criteria::LEFT_JOIN
        );
        $criteria->add(UsersPeer::USR_UID, null, Criteria::ISNULL);
        $rsCriteria = AppCacheViewPeer::doSelectRS($criteria);
        $rsCriteria->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $counter = 0;
        while ($rsCriteria->next()) {
            $item = $rsCriteria->getRow();
            $counter++;
            file_put_contents(
                PATH_DATA."/missing-users-".$this->name.".txt",
                "APP_UID:[".$item['APP_UID']."] - DEL_INDEX[".$item['DEL_INDEX']."] have relation " .
                "with invalid or non-existent user user with ".
                "id [".$item['USR_UID']."]"
            );
        }
        CLI::logging("> Number of user related inconsistencies for workspace " . CLI::info($this->name) . ": " . CLI::info($counter) . "\n");
        return ($counter > 0);
    }

    public function hasMissingTasks()
    {
        $this->initPropel(true);
        file_put_contents(
            PATH_DATA."/missing-tasks-".$this->name.".txt",
            "Missing Processes List\n"
        );

        $criteria = new Criteria("workflow");
        $criteria->addSelectColumn(AppCacheViewPeer::APP_UID);
        $criteria->addSelectColumn(AppCacheViewPeer::DEL_INDEX);
        $criteria->addSelectColumn(AppCacheViewPeer::TAS_UID);
        $criteria->addJoinMC(array(
            array(AppCacheViewPeer::USR_UID, TaskPeer::TAS_UID)),
            Criteria::LEFT_JOIN
        );

        $criteria->add(TaskPeer::TAS_UID, null, Criteria::ISNULL);
        $rsCriteria = AppCacheViewPeer::doSelectRS($criteria);
        $rsCriteria->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $counter = 0;
        while ($rsCriteria->next()) {
            $item = $rsCriteria->getRow();
            file_put_contents(
                PATH_DATA."/missing-tasks-".$this->name.".txt",
                "APP_UID:[".$item['APP_UID']."] - DEL_INDEX[".$item['DEL_INDEX']."] have relation " .
                "with invalid or non-existent task with ".
                "id [".$item['TAS_UID']."]"
            );
        }

        CLI::logging("> Number of task related inconsistencies for workspace " . CLI::info($this->name) . ": " . CLI::info($counter) . "\n");
        return ($counter > 0);
    }

    public function hasMissingProcesses()
    {
        $this->initPropel(true);
        file_put_contents(
            PATH_DATA."/missing-processes-".$this->name.".txt",
            "Missing Processes List\n"
        );

        $criteria = new Criteria("workflow");
        $criteria->addSelectColumn(AppCacheViewPeer::APP_UID);
        $criteria->addSelectColumn(AppCacheViewPeer::DEL_INDEX);
        $criteria->addSelectColumn(AppCacheViewPeer::PRO_UID);
        $criteria->addJoinMC(array(
            array(AppCacheViewPeer::USR_UID, ProcessPeer::PRO_UID)),
            Criteria::LEFT_JOIN
        );

        $criteria->add(ProcessPeer::PRO_UID, null, Criteria::ISNULL);
        $rsCriteria = AppCacheViewPeer::doSelectRS($criteria);
        $rsCriteria->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $counter = 0;
        while ($rsCriteria->next()) {
            $item = $rsCriteria->getRow();
            file_put_contents(
                PATH_DATA."/missing-processes-".$this->name.".txt",
                "APP_UID:[".$item['APP_UID']."] - DEL_INDEX[".$item['DEL_INDEX']."] have relation " .
                "with invalid or non-existent process with ".
                "id [".$item['PRO_UID']."]"
            );
        }
        CLI::logging("> Number of processes related data inconsistencies for workspace " . CLI::info($this->name) . ": " . CLI::info($counter) . "\n");
        return ($counter > 0);
    }

    public function hasMissingAppDelegations()
    {
        $this->initPropel(true);
        file_put_contents(
            PATH_DATA."/missing-app-delegation-".$this->name.".txt",
            "Missing AppDelegation List.\n"
        );

        $criteria = new Criteria("workflow");
        $criteria->addSelectColumn(AppCacheViewPeer::APP_UID);
        $criteria->addSelectColumn(AppCacheViewPeer::DEL_INDEX);
        $criteria->addJoinMC(array(
            array(AppCacheViewPeer::APP_UID, AppCacheViewPeer::DEL_INDEX),
            array(AppDelegationPeer::APP_UID, AppDelegationPeer::DEL_INDEX)),
            Criteria::LEFT_JOIN
        );
        $criteria->add(AppDelegationPeer::APP_UID, null, Criteria::ISNULL);
        $rsCriteria = AppCacheViewPeer::doSelectRS($criteria);
        $rsCriteria->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $counter = 0;
        while ($rsCriteria->next()) {
            $item = $rsCriteria->getRow();
            $counter++;
            file_put_contents(
                PATH_DATA."/missing-app-delegation-".$this->name.".txt",
                "APP_UID:[".$item['APP_UID']."] - DEL_INDEX[".$item['DEL_INDEX']."] have relation " .
                "with invalid or non-existent process with ".
                "id [".$item['PRO_UID']."]"
            );
        }
        CLI::logging("> Number of delegations related data inconsistencies for workspace " . CLI::info($this->name) . ": " . CLI::info($counter) . "\n");
        return ($counter > 0);
    }


    public function verifyMissingCancelled()
    {
        $this->initPropel(true);
        file_put_contents(
            PATH_DATA."/post-missing-cancelled-".$this->name.".txt",
            "Missing Cancelled List.\n"
        );
        $criteria = new Criteria("workflow");
        $criteria->addSelectColumn(AppCacheViewPeer::APP_UID);
        $criteria->addSelectColumn(AppCacheViewPeer::DEL_INDEX);
        $criteria->addJoinMC(array(
            array(AppCacheViewPeer::APP_UID, ListCanceledPeer::APP_UID),
            array(AppCacheViewPeer::DEL_INDEX, ListCanceledPeer::DEL_INDEX)),
            Criteria::LEFT_JOIN
        );

        $criteria->add(AppCacheViewPeer::APP_STATUS, 'CANCELLED', Criteria::EQUAL);
        $criteria->add(AppCacheViewPeer::DEL_LAST_INDEX, 1, Criteria::EQUAL);
        $criteria->add(ListCanceledPeer::APP_UID, null, Criteria::ISNULL);

        $rsCriteria = AppCacheViewPeer::doSelectRS($criteria);
        $rsCriteria->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $counter = 0;
        while ($rsCriteria->next()) {
            $item = $rsCriteria->getRow();
            $counter++;

            file_put_contents(
                PATH_DATA."/post-missing-cancelled-".$this->name.".txt",
                "[".$item['APP_UID']."] has not been found"
            );
        }

        CLI::logging("> Number of missing cancelled cases for workspace " . CLI::info($this->name) . ": " . CLI::info($counter) . "\n");
    }

    public function verifyMissingCompleted()
    {
        $this->initPropel(true);
        file_put_contents(
            PATH_DATA."/post-missing-completed-".$this->name.".txt",
            "Missing Completed List.\n"
        );
        $criteria = new Criteria("workflow");
        $criteria->addSelectColumn(AppCacheViewPeer::APP_UID);
        $criteria->addSelectColumn(AppCacheViewPeer::DEL_INDEX);
        $criteria->addJoinMC(array(
            array(AppCacheViewPeer::APP_UID, ListCompletedPeer::APP_UID),
            array(AppCacheViewPeer::DEL_INDEX, ListCompletedPeer::DEL_INDEX)),
            Criteria::LEFT_JOIN
        );

        $criteria->add(AppCacheViewPeer::APP_STATUS, 'COMPLETED', Criteria::EQUAL);
        $criteria->add(AppCacheViewPeer::DEL_LAST_INDEX, 1, Criteria::EQUAL);
        $criteria->add(ListCompletedPeer::APP_UID, null, Criteria::ISNULL);

        $rsCriteria = AppCacheViewPeer::doSelectRS($criteria);
        $rsCriteria->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $counter = 0;
        while ($rsCriteria->next()) {
            $item = $rsCriteria->getRow();
            $counter++;
            file_put_contents(
                PATH_DATA."/post-missing-completed-".$this->name.".txt",
                "[".$item['APP_UID']."] has not been found"
            );
        }

        CLI::logging("> Number of missing completed cases for workspace " . CLI::info($this->name) . ": " . CLI::info($counter) . "\n");
    }

    public function verifyMissingInbox()
    {
        $this->initPropel(true);
        file_put_contents(
            PATH_DATA."/post-missing-inbox-".$this->name.".txt",
            "Missing Inbox List.\n"
        );
        $criteria = new Criteria("workflow");
        $criteria->addSelectColumn(AppCacheViewPeer::APP_UID);
        $criteria->addSelectColumn(AppCacheViewPeer::DEL_INDEX);
        $criteria->addJoinMC(array(
            array(AppCacheViewPeer::APP_UID, ListInboxPeer::APP_UID),
            array(AppCacheViewPeer::DEL_INDEX, ListInboxPeer::DEL_INDEX)),
            Criteria::LEFT_JOIN
        );

        $criteria->add(AppCacheViewPeer::DEL_THREAD_STATUS, 'OPEN', Criteria::EQUAL);
        $criteria->add(ListInboxPeer::APP_UID, null, Criteria::ISNULL);

        $rsCriteria = AppCacheViewPeer::doSelectRS($criteria);
        $rsCriteria->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $counter = 0;
        while ($rsCriteria->next()) {
            $item = $rsCriteria->getRow();
            $counter++;
            file_put_contents(
                PATH_DATA."/post-missing-inbox-".$this->name.".txt",
                "[".$item['APP_UID']."] has not been found"
            );
        }

        CLI::logging("> Number of missing inbox cases for workspace " . CLI::info($this->name) . ": " . CLI::info($counter) . "\n");
    }

    public function verifyMissingParticipatedHistory()
    {
        $this->initPropel(true);
        file_put_contents(
            PATH_DATA."/post-missing-participated-history-".$this->name.".txt",
            "Missing ParticipatedHistory List.\n"
        );
        $criteria = new Criteria("workflow");
        $criteria->addSelectColumn(AppCacheViewPeer::APP_UID);
        $criteria->addSelectColumn(AppCacheViewPeer::DEL_INDEX);
        $criteria->addJoinMC(array(
            array(AppCacheViewPeer::APP_UID, ListParticipatedHistoryPeer::APP_UID),
            array(AppCacheViewPeer::DEL_INDEX, ListParticipatedHistoryPeer::DEL_INDEX)
        ),
            Criteria::LEFT_JOIN
        );

        $criteria->add(AppCacheViewPeer::DEL_THREAD_STATUS, 'OPEN', Criteria::NOT_EQUAL);
        $criteria->add(ListParticipatedHistoryPeer::APP_UID, null, Criteria::ISNULL);

        $rsCriteria = AppCacheViewPeer::doSelectRS($criteria);
        $rsCriteria->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $counter = 0;
        while ($rsCriteria->next()) {
            $item = $rsCriteria->getRow();
            $counter++;
            file_put_contents(
                PATH_DATA."/post-missing-participated-history-".$this->name.".txt",
                "[".$item['APP_UID']."] has not been found"
            );
        }

        CLI::logging("> Number of missing participated history entries for workspace " . CLI::info($this->name) . ": " . CLI::info($counter) . "\n");
    }

    public function verifyMissingParticipatedLast()
    {
        $this->initPropel(true);
        file_put_contents(
            PATH_DATA."/post-missing-participated-last-".$this->name.".txt",
            "Missing ParticipatedLast List.\n"
        );
        $criteria = new Criteria("workflow");
        $criteria->addSelectColumn(AppCacheViewPeer::APP_UID);
        $criteria->addSelectColumn(AppCacheViewPeer::DEL_INDEX);
        $criteria->addJoinMC(array(
            array(AppCacheViewPeer::APP_UID, ListParticipatedLastPeer::APP_UID),
            array(AppCacheViewPeer::DEL_INDEX, ListParticipatedLastPeer::DEL_INDEX)
        ),
            Criteria::LEFT_JOIN
        );

        $criteria->add(AppCacheViewPeer::DEL_THREAD_STATUS, 'OPEN', Criteria::NOT_EQUAL);
        $criteria->add(ListParticipatedLastPeer::APP_UID, null, Criteria::ISNULL);

        $rsCriteria = AppCacheViewPeer::doSelectRS($criteria);
        $rsCriteria->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $counter = 0;
        while ($rsCriteria->next()) {
            $item = $rsCriteria->getRow();
            $counter++;
            file_put_contents(
                PATH_DATA."/post-missing-participated-last-".$this->name.".txt",
                "[".$item['APP_UID']."] has not been found"
            );
        }

        CLI::logging("> Number of missing participated last entries for workspace " . CLI::info($this->name) . ": " . CLI::info($counter) . "\n");
    }

    public function verifyMissingMyInbox()
    {
        $this->initPropel(true);
        file_put_contents(
            PATH_DATA."/post-missing-my-inbox-".$this->name.".txt",
            "Missing MyInbox List.\n"
        );
        $criteria = new Criteria("workflow");
        $criteria->addSelectColumn(AppCacheViewPeer::APP_UID);
        $criteria->addSelectColumn(AppCacheViewPeer::DEL_INDEX);
        $criteria->addJoinMC(array(
            array(AppCacheViewPeer::APP_UID, ListMyInboxPeer::APP_UID),
            array(AppCacheViewPeer::DEL_INDEX, ListMyInboxPeer::DEL_INDEX)
        ),
            Criteria::LEFT_JOIN
        );

        $criteria->add(AppCacheViewPeer::DEL_INDEX, 1, Criteria::EQUAL);
        $criteria->add(ListMyInboxPeer::APP_UID, null, Criteria::ISNULL);

        $rsCriteria = AppCacheViewPeer::doSelectRS($criteria);
        $rsCriteria->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $counter = 0;
        while ($rsCriteria->next()) {
            $item = $rsCriteria->getRow();
            $counter++;
            file_put_contents(
                PATH_DATA."/post-missing-my-inbox-".$this->name.".txt",
                "[".$item['APP_UID']."] has not been found"
            );
        }

        CLI::logging("> Number of missing my inbox entries for workspace " . CLI::info($this->name) . ": " . CLI::info($counter) . "\n");
    }

    public function verifyMissingUnassigned()
    {
        $this->initPropel(true);
        file_put_contents(
            PATH_DATA."/post-missing-unassigned-".$this->name.".txt",
            "Missing MissingUnassigned List.\n"
        );
        $criteria = new Criteria("workflow");
        $criteria->addSelectColumn(AppCacheViewPeer::APP_UID);
        $criteria->addSelectColumn(AppCacheViewPeer::DEL_INDEX);
        $criteria->addJoinMC(array(
            array(AppCacheViewPeer::APP_UID, ListUnassignedPeer::APP_UID),
            array(AppCacheViewPeer::DEL_INDEX, ListUnassignedPeer::DEL_INDEX)
        ),
            Criteria::LEFT_JOIN
        );

        $criteria->add(AppCacheViewPeer::USR_UID, '', Criteria::EQUAL);
        $criteria->add(ListUnassignedPeer::APP_UID, null, Criteria::ISNULL);

        $rsCriteria = AppCacheViewPeer::doSelectRS($criteria);
        $rsCriteria->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $counter = 0;
        while ($rsCriteria->next()) {
            $item = $rsCriteria->getRow();
            $counter++;
            file_put_contents(
                PATH_DATA."/post-missing-unassigned-".$this->name.".txt",
                "[".$item['APP_UID']."] has not been found"
            );
        }
        CLI::logging("> Number of unassigned cases for workspace " . CLI::info($this->name) . ": " . CLI::info($counter) . "\n");
    }

    public function verifyListData($type)
    {
        switch ($type) {
            case 'LIST_CANCELLED':
                $response = $this->verifyMissingCancelled();
                break;
            case 'LIST_COMPLETED':
                $response = $this->verifyMissingCompleted();
                break;
            case 'LIST_INBOX':
                $response = $this->verifyMissingInbox();
                break;
            case 'LIST_PARTICIPATED_HISTORY':
                $response = $this->verifyMissingParticipatedHistory();
                break;
            case 'LIST_PARTICIPATED_LAST':
                $response = $this->verifyMissingParticipatedLast();
                break;
            case 'LIST_MY_INBOX':
                $response = $this->verifyMissingMyInbox();
                break;
            case 'LIST_PAUSED':
                // The list implementation needs to be reestructured in order to
                // properly validate the list consistency, currently we are maintaining the
                // current LIST_PAUSED implementation.
                $response = '';
                break;
            case 'LIST_UNASSIGNED':
                $response = $this->verifyMissingUnassigned();
                break;
            case 'LIST_UNASSIGNED_GROUP':
                // There is still no need to validate this list since is not being
                // populated until the logic has been defined
                $response = '';
                break;
            default:
                $response = '';
                break;
        }
        return $response;
    }


    public function migrateContent($workspace, $lang = SYS_LANG)
    {
        if ((!class_exists('Memcache') || !class_exists('Memcached')) && !defined('MEMCACHED_ENABLED')) {
            define('MEMCACHED_ENABLED', false);
        }
        $this->initPropel(true);
        $conf  = new Configuration();
        $blackList = array();
        if ($bExist = $conf->exists('MIGRATED_CONTENT', 'content')) {
            $oConfig = $conf->load('MIGRATED_CONTENT', 'content');
            $blackList = $oConfig['CFG_VALUE'] == 'true' ? array('Groupwf', 'Process', 'Department', 'Task', 'InputDocument', 'Application') : unserialize($oConfig['CFG_VALUE']);
        }

        $blackList = $this->migrateContentRun($workspace, $lang, $blackList);
        $data["CFG_UID"] = 'MIGRATED_CONTENT';
        $data["OBJ_UID"] = 'content';
        $data["CFG_VALUE"] = serialize($blackList);
        $data["PRO_UID"] = '';
        $data["USR_UID"] = '';
        $data["APP_UID"] = '';
        $conf->create($data);
    }

    /**
     * Migrate this workspace table Content.
     *
     * @param $className
     * @param $fields
     * @param mixed|string $lang
     * @throws Exception
     */
    public function migrateContentWorkspace($className, $fields, $lang = SYS_LANG)
    {
        try {
            $this->initPropel(true);
            $fieldUidName = $fields['uid'];
            $oCriteria = new Criteria();
            $oCriteria->clearSelectColumns();
            $oCriteria->addAsColumn($fieldUidName, ContentPeer::CON_ID);
            $oCriteria->addSelectColumn(ContentPeer::CON_PARENT);
            $oCriteria->addSelectColumn(ContentPeer::CON_CATEGORY);
            $oCriteria->addSelectColumn(ContentPeer::CON_VALUE);
            $oCriteria->add(ContentPeer::CON_CATEGORY, $fields['fields'], Criteria::IN);
            $oCriteria->add(ContentPeer::CON_LANG, $lang);
            $oDataset = ContentPeer::doSelectRS($oCriteria);
            $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $methods = $fields['methods'];
            while ($oDataset->next()) {
                $row = $oDataset->getRow();
                $fieldName = $row['CON_CATEGORY'];
                $fieldName = isset($fields['alias']) && isset($fields['alias'][$fieldName]) ? $fields['alias'][$fieldName] : $fieldName;
                unset($row['CON_CATEGORY']);
                $row[$fieldName] = $row['CON_VALUE'];
                unset($row['CON_VALUE']);
                $oTable = new $className();
                $that = array($oTable, $methods['exists']);
                $params = array($row[$fieldUidName]);
                if (isset($row['CON_PARENT']) && $row['CON_PARENT'] != '') {
                    array_push($params, $row['CON_PARENT']);
                    $fieldName = isset($fields['alias']) && isset($fields['alias']['CON_PARENT']) ? $fields['alias']['CON_PARENT'] : 'CON_PARENT';
                    $row[$fieldName] = $row['CON_PARENT'];
                }
                unset($row['CON_PARENT']);
                if (call_user_func_array($that, $params)) {
                    if (isset($methods['update'])) {
                        $fn = $methods['update'];
                        $fn($row);
                    } else {
                        $oTable->update($row);
                    }
                }
            }
            $classNamePeer = class_exists($className . 'Peer') ? $className . 'Peer' : $fields['peer'];
            CLI::logging("|--> Add content data in table " . $classNamePeer::TABLE_NAME . "\n");
        } catch (Exception $e) {
            throw ($e);
        }
    }

    /**
     * Migration
     *
     * @param $workspace
     * @param mixed|string $lang
     * @return array
     */
    public function migrateContentRun($workspace, $lang = SYS_LANG, $blackList = array())
    {
        if ((!class_exists('Memcache') || !class_exists('Memcached')) && !defined('MEMCACHED_ENABLED')) {
            define('MEMCACHED_ENABLED', false);
        }
        $content = array(
            'Groupwf' => array(
                'uid' => 'GRP_UID',
                'fields' => array('GRP_TITLE'),
                'methods' => array('exists' => 'GroupwfExists')
            ),
            'Process' => array(
                'uid' => 'PRO_UID',
                'fields' => array('PRO_TITLE', 'PRO_DESCRIPTION'),
                'methods' => array('exists' => 'exists')
            ),
            'Department' => array(
                'uid' => 'DEP_UID',
                'fields' => array('DEPO_TITLE'),
                'alias' => array('DEPO_TITLE' => 'DEP_TITLE'),
                'methods' => array('exists' => 'existsDepartment')
            ),
            'Task' => array(
                'uid' => 'TAS_UID',
                'fields' => array('TAS_TITLE', 'TAS_DESCRIPTION', 'TAS_DEF_TITLE', 'TAS_DEF_SUBJECT_MESSAGE', 'TAS_DEF_PROC_CODE', 'TAS_DEF_MESSAGE', 'TAS_DEF_DESCRIPTION'),
                'methods' => array('exists' => 'taskExists')
            ),
            'InputDocument' => array(
                'uid' => 'INP_DOC_UID',
                'fields' => array('INP_DOC_TITLE', 'INP_DOC_DESCRIPTION'),
                'methods' => array('exists' => 'InputExists')
            ),
            'Application' => array(
                'uid' => 'APP_UID',
                'fields' => array('APP_TITLE', 'APP_DESCRIPTION'),
                'methods' => array('exists' => 'exists')
            ),
            'AppDocument' => array(
                'uid' => 'APP_DOC_UID',
                'alias' => array('CON_PARENT' => 'DOC_VERSION'),
                'fields' => array('APP_DOC_TITLE', 'APP_DOC_COMMENT', 'APP_DOC_FILENAME'),
                'methods' => array('exists' => 'exists')
            ),
            'Dynaform' => array(
                'uid' => 'DYN_UID',
                'fields' => array('DYN_TITLE', 'DYN_DESCRIPTION'),
                'methods' => array('exists' => 'exists')
            ),
            'OutputDocument' => array(
                'uid' => 'OUT_DOC_UID',
                'fields' => array('OUT_DOC_TITLE', 'OUT_DOC_DESCRIPTION', 'OUT_DOC_FILENAME', 'OUT_DOC_TEMPLATE'),
                'methods' => array('exists' => 'OutputExists')
            ),
            'ReportTable' => array(
                'uid' => 'REP_TAB_UID',
                'fields' => array('REP_TAB_TITLE'),
                'methods' => array('exists' => 'reportTableExists', 'update' => function ($row) {
                    $oRepTab = \ReportTablePeer::retrieveByPK($row['REP_TAB_UID']);
                    $oRepTab->fromArray($row, BasePeer::TYPE_FIELDNAME);
                    if ($oRepTab->validate()) {
                        $result = $oRepTab->save();
                    }
                })
            ),
            'Triggers' => array(
                'uid' => 'TRI_UID',
                'fields' => array('TRI_TITLE', 'TRI_DESCRIPTION'),
                'methods' => array('exists' => 'TriggerExists')
            ),
            '\ProcessMaker\BusinessModel\WebEntryEvent' => array(
                'uid' => 'WEE_UID',
                'fields' => array('WEE_TITLE', 'WEE_DESCRIPTION'),
                'methods' => array('exists' => 'exists', 'update' => function ($row) {
                    $webEntry = \WebEntryEventPeer::retrieveByPK($row['WEE_UID']);
                    $webEntry->fromArray($row, BasePeer::TYPE_FIELDNAME);
                    if ($webEntry->validate()) {
                        $result = $webEntry->save();
                    }
                }),
                'peer' => 'WebEntryEventPeer'
            )
        );

        foreach ($content as $className => $fields) {
            if (!in_array($className, $blackList)) {
                $this->migrateContentWorkspace($className, $fields, $lang);
                $blackList[] = $className;
            }
        }
        return $blackList;
    }

    public function cleanTokens($workspace, $lang = SYS_LANG)
    {
        $this->initPropel(true);
        $oCriteria = new Criteria();
        $oCriteria->add(OauthAccessTokensPeer::ACCESS_TOKEN, 0, Criteria::NOT_EQUAL);
        $accessToken = OauthAccessTokensPeer::doDelete($oCriteria);
        CLI::logging("|--> Clean data in table " . OauthAccessTokensPeer::TABLE_NAME . " rows ".$accessToken."\n");
        $oCriteria = new Criteria();
        $oCriteria->add(OauthRefreshTokensPeer::REFRESH_TOKEN, 0, Criteria::NOT_EQUAL);
        $refreshToken = OauthRefreshTokensPeer::doDelete($oCriteria);
        CLI::logging("|--> Clean data in table " . OauthRefreshTokensPeer::TABLE_NAME . " rows ".$refreshToken."\n");
    }

    public function migrateIteeToDummytask($workspaceName)
    {
        $this->initPropel(true);
        $arraySystemConfiguration = System::getSystemConfiguration('', '', $workspaceName);
        $conf = new Configurations();
        \G::$sysSys = $workspaceName;
        \G::$pathDataSite = PATH_DATA . "sites" . PATH_SEP . \G::$sysSys . PATH_SEP;
        \G::$pathDocument = PATH_DATA . 'sites' . DIRECTORY_SEPARATOR . $workspaceName . DIRECTORY_SEPARATOR . 'files';
        \G::$memcachedEnabled = $arraySystemConfiguration['memcached'];
        \G::$pathDataPublic = \G::$pathDataSite . "public" . PATH_SEP;
        \G::$sysSkin = $conf->getConfiguration('SKIN_CRON', '');
        if (is_file(\G::$pathDataSite . PATH_SEP . ".server_info")) {
            $serverInfo = file_get_contents(\G::$pathDataSite . PATH_SEP . ".server_info");
            $serverInfo = unserialize($serverInfo);
            $envHost = $serverInfo["SERVER_NAME"];
            $envPort = ($serverInfo["SERVER_PORT"] . "" != "80") ? ":" . $serverInfo["SERVER_PORT"] : "";
            if (!empty($envPort) && strpos($envHost, $envPort) === false) {
                $envHost = $envHost . $envPort;
            }
            \G::$httpHost = $envHost;
        }

        //Search All process
        $oCriteria = new Criteria("workflow");
        $oCriteria->addSelectColumn(ProcessPeer::PRO_UID);
        $oCriteria->addSelectColumn(ProcessPeer::PRO_ITEE);
        $oCriteria->add(ProcessPeer::PRO_ITEE, '0', Criteria::EQUAL);
        $rsCriteria = ProcessPeer::doSelectRS($oCriteria);
        $rsCriteria->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $message = "-> Migrating the Intermediate Email Event \n";
        CLI::logging($message);
        while ($rsCriteria->next()) {
            $row = $rsCriteria->getRow();
            $prj_uid = $row['PRO_UID'];
            $bpmnProcess = new Process();
            if ($bpmnProcess->isBpmnProcess($prj_uid)) {
                $project = new \ProcessMaker\Project\Adapter\BpmnWorkflow();
                $diagram = $project->getStruct($prj_uid);
                $res = $project->updateFromStruct($prj_uid, $diagram);
                $bpmnProcess->setProUid($prj_uid);
                $oProcess = new Process();
                $aProcess['PRO_UID'] = $prj_uid;
                $aProcess['PRO_ITEE'] = '1';
                if ($oProcess->processExists($prj_uid)) {
                    $oProcess->update($aProcess);
                }
                $message = "    Process updated ".$bpmnProcess->getProTitle(). "\n";
                CLI::logging($message);
            }
        }
        $message = "   Migrating Itee Done \n";
        CLI::logging($message);
    }

    public function upgradeConfiguration()
    {
        $conf = new Configurations();
        $conf->aConfig = 'neoclassic';
        $conf->saveConfig('SKIN_CRON', '');
    }

    public function upgradeAuditLog($workspace)
    {
        $conf = new Configurations();
        if (!$conf->exists('AUDIT_LOG', 'log')) {
            CLI::logging("> Updating Auditlog Config \n");
            $oServerConf = &ServerConf::getSingleton();
            $sAudit = $oServerConf->getAuditLogProperty('AL_OPTION', $workspace);
            $conf->aConfig = ($sAudit == 1) ? 'true' : 'false';
            $conf->saveConfig('AUDIT_LOG', 'log');
        }
    }

    public function migrateSelfServiceRecordsRun($workspace)
    {
        // Initializing
        $this->initPropel(true);

        // Get datat to migrate
        $criteria = new Criteria("workflow");
        $criteria->addSelectColumn(AppAssignSelfServiceValuePeer::ID);
        $criteria->addSelectColumn(AppAssignSelfServiceValuePeer::GRP_UID);
        $criteria->add(AppAssignSelfServiceValuePeer::GRP_UID, '', Criteria::NOT_EQUAL);
        $rsCriteria = AppAssignSelfServiceValuePeer::doSelectRS($criteria);
        $rsCriteria->setFetchmode(ResultSet::FETCHMODE_ASSOC);

        // Migrating data
        CLI::logging("-> Migrating Self-Service by Value Cases \n");
        while ($rsCriteria->next()) {
            $row = $rsCriteria->getRow();
            $temp = @unserialize($row['GRP_UID']);
            if (is_array($temp)) {
                foreach ($temp as $groupUid) {
                    if ($groupUid != '') {
                        $appAssignSelfServiceValueGroup = new AppAssignSelfServiceValueGroup();
                        $appAssignSelfServiceValueGroup->setId($row['ID']);
                        $appAssignSelfServiceValueGroup->setGrpUid($groupUid);
                        $appAssignSelfServiceValueGroup->save();
                    }
                }
            } else {
                if ($temp != '') {
                    $appAssignSelfServiceValueGroup = new AppAssignSelfServiceValueGroup();
                    $appAssignSelfServiceValueGroup->setId($row['ID']);
                    $appAssignSelfServiceValueGroup->setGrpUid($temp);
                    $appAssignSelfServiceValueGroup->save();
                }
            }
            CLI::logging("    Migrating Record ".$row['ID']. "\n");
        }

        // Updating processed records to empty
        $con = Propel::getConnection('workflow');
        $criteriaSet = new Criteria("workflow");
        $criteriaSet->add(AppAssignSelfServiceValuePeer::GRP_UID, '');
        BasePeer::doUpdate($criteria, $criteriaSet, $con);

        CLI::logging("   Migrating Self-Service by Value Cases Done \n");
    }

    public function migratePopulateIndexingACV($workspace)
    {
        // Migrating and populating new indexes
        CLI::logging("-> Migrating an populating indexing for avoiding the use of table APP_CACHE_VIEW Start \n");

        // Initializing
        $this->initPropel(true);
        $con = Propel::getConnection(AppDelegationPeer::DATABASE_NAME);

        // Populating APP_DELEGATION.APP_NUMBER
        CLI::logging("->   Populating APP_DELEGATION.APP_NUMBER \n");
        $con->begin();
        $stmt = $con->createStatement();
        $rs = $stmt->executeQuery("UPDATE APP_DELEGATION AS AD
                                   INNER JOIN (
                                       SELECT APPLICATION.APP_UID, APPLICATION.APP_NUMBER
                                       FROM APPLICATION
                                   ) AS APP
                                   ON (AD.APP_UID = APP.APP_UID)
                                   SET AD.APP_NUMBER = APP.APP_NUMBER
                                   WHERE AD.APP_NUMBER = 0");
        $con->commit();

        // Populating APP_DELEGATION.USR_ID
        CLI::logging("->   Populating APP_DELEGATION.USR_ID \n");
        $con->begin();
        $stmt = $con->createStatement();
        $rs = $stmt->executeQuery("UPDATE APP_DELEGATION AS AD
                                   INNER JOIN (
                                       SELECT USERS.USR_UID, USERS.USR_ID
                                       FROM USERS
                                   ) AS USR
                                   ON (AD.USR_UID = USR.USR_UID)
                                   SET AD.USR_ID = USR.USR_ID
                                   WHERE AD.USR_ID = 0");
        $con->commit();

        // Populating APP_DELEGATION.PRO_ID
        CLI::logging("->   Populating APP_DELEGATION.PRO_ID \n");
        $con->begin();
        $stmt = $con->createStatement();
        $rs = $stmt->executeQuery("UPDATE APP_DELEGATION AS AD
                                   INNER JOIN (
                                       SELECT PROCESS.PRO_UID, PROCESS.PRO_ID
                                       FROM PROCESS
                                   ) AS PRO
                                   ON (AD.PRO_UID = PRO.PRO_UID)
                                   SET AD.PRO_ID = PRO.PRO_ID
                                   WHERE AD.PRO_ID = 0");
        $con->commit();

        // Populating APP_DELEGATION.TAS_ID
        CLI::logging("->   Populating APP_DELEGATION.TAS_ID \n");
        $con->begin();
        $stmt = $con->createStatement();
        $rs = $stmt->executeQuery("UPDATE APP_DELEGATION AS AD
                                   INNER JOIN (
                                       SELECT TASK.TAS_UID, TASK.TAS_ID
                                       FROM TASK
                                   ) AS TAS
                                   ON (AD.TAS_UID = TAS.TAS_UID)
                                   SET AD.TAS_ID = TAS.TAS_ID
                                   WHERE AD.TAS_ID = 0");
        $con->commit();

        // Populating APPLICATION.APP_STATUS_ID
        CLI::logging("->   Populating APPLICATION.APP_STATUS_ID \n");
        $con->begin();
        $stmt = $con->createStatement();
        $rs = $stmt->executeQuery("UPDATE APPLICATION
                                    SET APP_STATUS_ID = (case
                                        when APP_STATUS = 'DRAFT' then 1
                                        when APP_STATUS = 'TO_DO' then 2
                                        when APP_STATUS = 'COMPLETED' then 3
                                        when APP_STATUS = 'CANCELLED' then 4
                                    end)
                                    WHERE APP_STATUS in ('DRAFT', 'TO_DO', 'COMPLETED', 'CANCELLED') AND
                                    APP_STATUS_ID = 0");
        $con->commit();

        // Populating APP_DELAY.USR_ID
        CLI::logging("->   Populating APP_DELAY.USR_ID \n");
        $con->begin();
        $stmt = $con->createStatement();
        $rs = $stmt->executeQuery("UPDATE APP_DELAY AS AD
                                   INNER JOIN (
                                       SELECT USERS.USR_UID, USERS.USR_ID
                                       FROM USERS
                                   ) AS USR
                                   ON (AD.APP_DELEGATION_USER = USR.USR_UID)
                                   SET AD.APP_DELEGATION_USER_ID = USR.USR_ID
                                   WHERE AD.APP_DELEGATION_USER_ID = 0");
        $con->commit();

        // Populating APP_DELAY.PRO_ID
        CLI::logging("->   Populating APP_DELAY.PRO_ID \n");
        $con->begin();
        $stmt = $con->createStatement();
        $rs = $stmt->executeQuery("UPDATE APP_DELAY AS AD
                                   INNER JOIN (
                                       SELECT PROCESS.PRO_UID, PROCESS.PRO_ID
                                       FROM PROCESS
                                   ) AS PRO
                                   ON (AD.PRO_UID = PRO.PRO_UID)
                                   SET AD.PRO_ID = PRO.PRO_ID
                                   WHERE AD.PRO_ID = 0");
        $con->commit();

        // Populating APP_DELAY.APP_NUMBER
        CLI::logging("->   Populating APP_DELAY.APP_NUMBER \n");
        $con->begin();
        $stmt = $con->createStatement();
        $rs = $stmt->executeQuery("UPDATE APP_DELAY AS AD
                                   INNER JOIN (
                                       SELECT APPLICATION.APP_UID, APPLICATION.APP_NUMBER
                                       FROM APPLICATION
                                   ) AS APP
                                   ON (AD.APP_UID = APP.APP_UID)
                                   SET AD.APP_NUMBER = APP.APP_NUMBER
                                   WHERE AD.APP_NUMBER = 0");
        $con->commit();

        CLI::logging("-> Migrating And Populating Indexing for avoiding the use of table APP_CACHE_VIEW Done \n");

        // Populating PRO_ID, USR_ID
        CLI::logging("->   Populating PRO_ID, USR_ID at LIST_* \n");
        $con->begin();
        $stmt = $con->createStatement();
        foreach (WorkspaceTools::$populateIdsQueries as $query) {
            $stmt->executeQuery($query);
        }
        $con->commit();

        CLI::logging("-> Populating PRO_ID, USR_ID at LIST_*  Done \n");
    }
    
    /**
     * It populates the WEB_ENTRY table for the classic processes, this procedure
     * is done to verify the execution of php files generated when the WebEntry
     * is configured.
     * @param type $workSpace
     */
    public function updatingWebEntryClassicModel($workSpace, $force = false)
    {
        //We obtain from the configuration the list of proUids obtained so that
        //we do not go through again.
        $cfgUid = 'UPDATING_ROWS_WEB_ENTRY';
        $objUid = 'blackList';
        $blackList = [];
        $conf = new Configuration();
        $ifExists = $conf->exists($cfgUid, $objUid);
        if ($ifExists) {
            $oConfig = $conf->load($cfgUid, $objUid);
            $blackList = unserialize($oConfig['CFG_VALUE']);
        }

        //The following query returns all the classic processes that do not have
        //a record in the WEB_ENTRY table.
        $oCriteria = new Criteria("workflow");
        $oCriteria->addSelectColumn(ProcessPeer::PRO_UID);
        $oCriteria->addSelectColumn(BpmnProcessPeer::PRJ_UID);
        $oCriteria->addJoin(ProcessPeer::PRO_UID, BpmnProcessPeer::PRJ_UID, Criteria::LEFT_JOIN);
        $oCriteria->addJoin(ProcessPeer::PRO_UID, WebEntryPeer::PRO_UID, Criteria::LEFT_JOIN);
        $oCriteria->add(BpmnProcessPeer::PRJ_UID, null, Criteria::EQUAL);
        $oCriteria->add(WebEntryPeer::PRO_UID, null, Criteria::EQUAL);
        if ($force === false) {
            $oCriteria->add(ProcessPeer::PRO_UID, $blackList, Criteria::NOT_IN);
        }
        $rsCriteria = ProcessPeer::doSelectRS($oCriteria);
        $rsCriteria->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $process = new Process();
        while ($rsCriteria->next()) {
            $row = $rsCriteria->getRow();
            $proUid = $row['PRO_UID'];
            if (!in_array($proUid, $blackList)) {
                $blackList[] = $proUid;
            }
            $path = PATH_DATA . "sites" . PATH_SEP . $this->name . PATH_SEP . "public" . PATH_SEP . $proUid;
            if (is_dir($path)) {
                $dir = opendir($path);
                while ($fileName = readdir($dir)) {
                    if ($fileName !== "." && $fileName !== ".." && strpos($fileName, "wsClient.php") === false && strpos($fileName, "Post.php") === false
                    ) {
                        CLI::logging("Verifying if file: " . $fileName . " is a web entry\n");
                        $step = new Criteria("workflow");
                        $step->addSelectColumn(StepPeer::PRO_UID);
                        $step->addSelectColumn(StepPeer::TAS_UID);
                        $step->addSelectColumn(StepPeer::STEP_TYPE_OBJ);
                        $step->addSelectColumn(StepPeer::STEP_UID_OBJ);
                        $step->add(StepPeer::STEP_TYPE_OBJ, "DYNAFORM", Criteria::EQUAL);
                        $step->add(StepPeer::PRO_UID, $proUid, Criteria::EQUAL);
                        $stepRs = StepPeer::doSelectRS($step);
                        $stepRs->setFetchmode(ResultSet::FETCHMODE_ASSOC);
                        while ($stepRs->next()) {
                            $row1 = $stepRs->getRow();
                            $content = file_get_contents($path . "/" . $fileName);
                            if (strpos($content, $proUid . "/" . $row1["STEP_UID_OBJ"]) !== false) {
                                //The default user admin is set. This task is
                                //carried out by the system administrator.
                                $userUid = "00000000000000000000000000000001";
                                //save data in table WEB_ENTRY
                                $arrayData = [
                                    "PRO_UID" => $proUid,
                                    "DYN_UID" => $row1["STEP_UID_OBJ"],
                                    "TAS_UID" => $row1["TAS_UID"],
                                    "WE_DATA" => $fileName,
                                    "USR_UID" => $userUid,
                                    "WE_CREATE_USR_UID" => $userUid,
                                    "WE_UPDATE_USR_UID" => $userUid
                                ];
                                $webEntry = new \ProcessMaker\BusinessModel\WebEntry();
                                $webEntry->createClassic($arrayData);
                            }
                        }
                    }
                }
            }
        }

        //The list of proUids obtained is saved in the configuration so that it
        //does not go through again.
        $data = [
            "CFG_UID" => $cfgUid,
            "OBJ_UID" => $objUid,
            "CFG_VALUE" => serialize($blackList),
            "PRO_UID" => '',
            "USR_UID" => '',
            "APP_UID" => ''
        ];
        if ($ifExists) {
            $conf->update($data);
        } else {
            $conf->create($data);
        }
    }

    /**
     * Updating triggers
     * @param $flagRecreate
     * @param $lang
     */
    public function updateTriggers($flagRecreate, $lang)
    {
        $this->initPropel(true);
        $this->upgradeTriggersOfTables($flagRecreate, $lang);
    }

    /**
     * @param $workspace
     */
    public function migrateSingleton($workspace)
    {
        if ((!class_exists('Memcache') || !class_exists('Memcached')) && !defined('MEMCACHED_ENABLED')) {
            define('MEMCACHED_ENABLED', false);
        }
        $this->initPropel(true);
        $conf  = new Configuration();
        $pathSingleton = PATH_DATA . 'sites' . PATH_SEP . $workspace . PATH_SEP . 'plugin.singleton';
        if ((!$bExist = $conf->exists('MIGRATED_PLUGIN', 'singleton')) && file_exists($pathSingleton)) {
            $oPluginRegistry = unserialize(file_get_contents($pathSingleton));
            $pluginAdapter = new PluginAdapter();
            $pluginAdapter->migrate($oPluginRegistry);
            $data["CFG_UID"] = 'MIGRATED_PLUGIN';
            $data["OBJ_UID"] = 'singleton';
            $data["CFG_VALUE"] = 'true';
            $data["PRO_UID"] = '';
            $data["USR_UID"] = '';
            $data["APP_UID"] = '';
            $conf->create($data);
        }
    }

    /**
     * This method finds all recursively PHP files that have the path PATH_DATA,
     * poorly referenced, this is caused by the import of processes where the data
     * directory of ProcessMaker has different routes. Modified files are backed
     * up with the extension '.backup' in the same directory.
     *
     * @return void
     */
    public function fixReferencePathFiles($pathClasses, $pathData)
    {
        try {
            $this->initPropel(true);
            $fixReferencePath = new FixReferencePath();
            $fixReferencePath->runProcess($pathClasses, $pathData);
            CLI::logging($fixReferencePath->getResumeDebug());
        } catch (Exception $e) {
            CLI::logging(CLI::error("Error:" . "Error updating generated class files for PM Tables, proceed to regenerate manually: " . $e));
        }
    }

    /**
     * Updating framework directory structure
     *
     */
    private function updateFrameworkPaths($workSpace = SYS_SYS)
    {
        $paths = [
            PATH_DATA.'framework' => 0770,
            PATH_DATA.'framework' . DIRECTORY_SEPARATOR . 'cache' => 0770,
        ];
        foreach ($paths as $path => $permission) {
            if (!file_exists($path)) {
                G::mk_dir($path, $permission);
            }
            CLI::logging("    $path [" . (file_exists($path) ? 'OK' : 'MISSING') . "]\n");
        }
    }
}
