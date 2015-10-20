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

    public function upgrade($first = false, $buildCacheView = false, $workSpace = SYS_SYS, $onedb = false, $lang = 'en')

    {

        $start = microtime(true);

        CLI::logging("> Updating database...\n");

        $this->upgradeDatabase($onedb);

        $stop = microtime(true);

        CLI::logging("<*>   Database Upgrade Process took " . ($stop - $start) . " seconds.\n");



        $start = microtime(true);

        CLI::logging("> Verify enterprise old...\n");

        $this->verifyFilesOldEnterprise($workSpace);

        $stop = microtime(true);

        CLI::logging("<*>   Verify took " . ($stop - $start) . " seconds.\n");



        $start = microtime(true);

        CLI::logging("> Updating translations...\n");

        $this->upgradeTranslation($first);

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

        CLI::logging("> Updating cache view...\n");

        $this->upgradeCacheView($buildCacheView, true, $lang);

        $stop = microtime(true);

        $final = $stop - $start;

        CLI::logging("<*>   Updating cache view Process took $final seconds.\n");



        $start = microtime(true);

        CLI::logging("> Backup log files...\n");

        $this->backupLogFiles();

        $stop = microtime(true);

        $final = $stop - $start;

        CLI::logging("<*>   Backup log files Process took $final seconds.\n");



        $start = microtime(true);

        CLI::logging("> Migrate new lists...\n");

        $this->migrateList($workSpace);

        $stop = microtime(true);

        $final = $stop - $start;

        CLI::logging("<*>   Migrate new lists Process took $final seconds.\n");



        $start = microtime(true);

        CLI::logging("> Updating Files Manager...\n");

        $this->processFilesUpgrade();

        $stop = microtime(true);

        CLI::logging("<*>   Updating Files Manager took " . ($stop - $start) . " seconds.\n");

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



        if($this->onedb){

        	$dbInfo = $this->getDBInfo();

            $dbPrefix = array('DB_NAME' => 'wf_', 'DB_USER' => 'wf_', 'DB_RBAC_NAME' => 'wf_', 'DB_RBAC_USER' => 'wf_', 'DB_REPORT_NAME' => 'wf_', 'DB_REPORT_USER' => 'wf_');

        	if (array_search($key, array('DB_PASS', 'DB_RBAC_PASS', 'DB_REPORT_PASS'))) {

        		$value = $dbInfo['DB_PASS'];

        	}

        } else{

            $dbPrefix = array('DB_NAME' => 'wf_', 'DB_USER' => 'wf_', 'DB_RBAC_NAME' => 'rb_', 'DB_RBAC_USER' => 'rb_', 'DB_REPORT_NAME' => 'rp_', 'DB_REPORT_USER' => 'rp_');

        }



        if (array_search($key, array('DB_HOST', 'DB_RBAC_HOST', 'DB_REPORT_HOST')) !== false) {

            /* Change the database hostname for these keys */

            $value = $this->newHost;

        } elseif (array_key_exists($key, $dbPrefix)) {

            if ($this->resetDBNames) {

                /* Change the database name to the new workspace, following the standard

                 * of prefix (either wf_, rp_, rb_) and the workspace name.

                 */

                if($this->unify){

            		$nameDb = explode("_", $value);

            		if(!isset($nameDb[1])){

            			$dbName = $value;

            		} else {

            			$dbName = $dbPrefix[$key] . $nameDb[1];

            		}

            	}else {

            		$dbName = $dbPrefix[$key] . $this->name;

            	}

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

            CLI::logging("Updating database translations with $poName\n");

            Language::import($poFile, false, true, false);

            if ($first) {

                CLI::logging("Updating XML form translations with $poName\n");

                Language::import($poFile, true, false, true);

            }

        }

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



        G::LoadSystem('database_' . strtolower($this->dbAdapter));

        if ($rbac == true){

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

     * @param bool   $flagRecreate Recreate

     * @param string $language     Language

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



        CLI::logging("-> Creating tables \n");

        //now check if table APPCACHEVIEW exists, and it have correct number of fields, etc.

        $res = $appCache->checkAppCacheView();



        CLI::logging("-> Update DEL_LAST_INDEX field in APP_DELEGATION table \n");

        //Update APP_DELEGATION.DEL_LAST_INDEX data

        $res = $appCache->updateAppDelegationDelLastIndex($lang, $flagRecreate);



        CLI::logging("-> Verifying roles permissions in RBAC \n");

        //Update table RBAC permissions

        Bootstrap::LoadSystem( 'rbac' );

        $RBAC = & RBAC::getSingleton();

        $RBAC->initRBAC();

        $result = $RBAC->verifyPermissions();

        if (count($result) > 1) {

            foreach ($result as $item) {

                CLI::logging("    $item... \n");

            }

        } else {

            CLI::logging("    All roles permissions already updated \n");

        }



        CLI::logging("-> Creating triggers\n");



        //now check if we have the triggers installed

        $this->upgradeTriggersOfTables($flagRecreate, $lang);



        if ($fill) {

            CLI::logging("-> Rebuild Cache View with language $lang...\n");

            //build using the method in AppCacheView Class

            $res = $appCache->fillAppCacheView($lang);

        }

        //set status in config table

        $confParams = Array('LANG' => $lang, 'STATUS' => 'active');

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

            $connection = Propel::getConnection( 'workflow' );



            $sql_sleep = "SELECT * FROM information_schema.processlist WHERE command = 'Sleep' and user = SUBSTRING_INDEX(USER(),'@',1) and db = DATABASE() ORDER BY id;";

            $stmt_sleep = $connection->createStatement();

            $rs_sleep = $stmt_sleep->executeQuery( $sql_sleep, ResultSet::FETCHMODE_ASSOC );



            while ($rs_sleep->next()) {

                $row_sleep = $rs_sleep->getRow();

                $oStatement_sleep = $connection->prepareStatement( "kill ". $row_sleep['ID'] );

                $oStatement_sleep->executeQuery();

            }



            $sql_query = "SELECT * FROM information_schema.processlist WHERE user = SUBSTRING_INDEX(USER(),'@',1) and db = DATABASE() and time > 0 ORDER BY id;";

            $stmt_query = $connection->createStatement();

            $rs_query = $stmt_query->executeQuery( $sql_query, ResultSet::FETCHMODE_ASSOC );



            while ($rs_query->next()) {

                $row_query = $rs_query->getRow();

                $oStatement_query = $connection->prepareStatement( "kill ". $row_query['ID'] );

                $oStatement_query->executeQuery();

            }

        }

    }



    /**

     * fix the 32K issue, by migrating /files directory structure to an uid tree structure based.

     * @param $workspace got the site(s) the manager wants to upgrade

     */

    public function upgradeCasesDirectoryStructure ($workspace)

    {

        define('PATH_DOCUMENT',  PATH_DATA . 'sites/' . $workspace . '/' . 'files/');

        $doclevel = explode('/', PATH_DOCUMENT);

        $length = sizeof(PATH_DOCUMENT);

        $filesDir = $doclevel[$length - 1];



        if (is_dir(PATH_DOCUMENT) && is_writable($filesDir)) {

            CLI::logging(CLI::error("Error:" . PATH_DOCUMENT . " is not writable... please check the su permissions.\n"));

            return;

        }



        $directory = array();

        $blackHoleDir = G::getBlackHoleDir();

        $directory = glob(PATH_DOCUMENT . "*", GLOB_ONLYDIR);

        $dirslength = sizeof($directory);



        if (! @chdir(PATH_DOCUMENT)) {

            CLI::logging(CLI::error("Cannot use Document directory. The upgrade must be done as root.\n"));

            return;

        }



        //Start migration

        for ($index = 0; $index < $dirslength; $index++) {

            $depthdirlevel = explode('/', $directory[$index]);

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

                        rmdir($UIdDir);//remove the diretory itself, G::rm_dir cannot do it

                    } else {

                        CLI::logging(CLI::error("Error: Failure at coping from $UIdDir...\n"));

                    }

                } else {

                    CLI::logging("$UIdDir is empty, removing it\n");

                    rmdir($UIdDir);//remove the diretory itself

                }

            }

        }



        //Start '0' directory migration

        $black = PATH_DOCUMENT . $blackHoleDir . '/';

        if (is_dir($black)) {

            $newpattern = array();

            $file = glob($black . '*.*');//files only

            $dirlen = count($file);



            for ($index = 0; $index < $dirlen; $index++) {

                $levelfile = explode('/', $file[$index]);

                $lastlevel = sizeof($levelfile);

                $goalFile = $levelfile[$lastlevel - 1];

                $newpattern = G::getPathFromFileUIDPlain($blackHoleDir, $goalFile);

                CLI::logging("Migrating $blackHoleDir file: $goalFile\n");

                G::mk_dir($blackHoleDir . PATH_SEP . $newpattern[0], 0777);

                //echo `cp -R $black$goalFile $black$newpattern[0]/$newpattern[1]`;



                if (copy($black . $goalFile, $black . $newpattern[0] . '/' . $newpattern[1])) {

                    unlink($file[$index]);

                } else {

                    CLI::logging(CLI::error("Error: Failure at copy $file[$index] files...\n"));

                }

            }

        }



        //Set value of 2 to the directory structure version.

        $this->initPropel(true);

        G::LoadClass("configuration");

        $conf = new Configurations();

        if (!$conf->exists("ENVIRONMENT_SETTINGS")) {

            $conf->aConfig = array ("format" => '@userName (@firstName @lastName)',

                                "dateFormat" => 'd/m/Y',

                                "startCaseHideProcessInf" => false,

                                "casesListDateFormat" => 'Y-m-d H:i:s',

                                "casesListRowNumber" => 25,

                                "casesListRefreshTime" => 120 );

            $conf->saveConfig( 'ENVIRONMENT_SETTINGS', '' );

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

    public function upgradeDatabase ($onedb = false, $checkOnly = false)

    {

        G::LoadClass("patch");

        $this->initPropel( true );

        p11835::$dbAdapter = $this->dbAdapter;

        p11835::isApplicable();

        $systemSchema = System::getSystemSchema($this->dbAdapter);

        $systemSchemaRbac = System::getSystemSchemaRbac($this->dbAdapter);// get the Rbac Schema

        $this->upgradeSchema( $systemSchema );

        $this->upgradeSchema( $systemSchemaRbac, false, true, $onedb ); // perform Upgrade to Rbac

        $this->upgradeData();



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



        p11835::execute();



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

            if($rbac){

                $rename = System::verifyRbacSchema($workspaceSchema);

                if (count($rename) > 0) {

                    foreach ($rename as $tableName) {

                        $oDataBase->executeQuery($oDataBase->generateRenameTableSQL($tableName));

                    }

                }

            }

        }



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

    public function exportDatabase($path, $onedb = false)

    {

        $dbInfo = $this->getDBInfo();



        if ($onedb) {

            $databases = array("rb", "rp");

        } else if ($dbInfo['DB_NAME'] == $dbInfo['DB_RBAC_NAME']) {

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

    public function executeSQLScript($database, $filename, $parameters)

    {

        mysql_query("CREATE DATABASE IF NOT EXISTS " . mysql_real_escape_string($database));



        //check function shell_exec

        $disabled_functions = ini_get('disable_functions');

        $flag = false;

        if ($disabled_functions!='') {

            $arr = explode(',', $disabled_functions);

            sort($arr);

            if (in_array("shell_exec", $arr)) {

                $flag = true;

            }

        }



        // Check if mysql exist on server

        $flagFunction = null;

        if ( !$flag ) {

            $flagFunction = shell_exec('mysql --version');

        }



        $arrayRegExpEngineSearch  = array("/\)\s*TYPE\s*=\s*(InnoDB)/i",       "/\)\s*TYPE\s*=\s*(MyISAM)/i");

        $arrayRegExpEngineReplace = array(") ENGINE=\\1 DEFAULT CHARSET=utf8", ") ENGINE=\\1");



        if ( !$flag && !is_null($flagFunction) ) {

            //Replace TYPE by ENGINE

            $script = preg_replace($arrayRegExpEngineSearch, $arrayRegExpEngineReplace, file_get_contents($filename));



            file_put_contents($filename,$script);



            $aHost = explode(':',$parameters['dbHost']);

            $dbHost = $aHost[0];

            if(isset($aHost[1])){

                $dbPort = $aHost[1];

                $command = 'mysql'

                . ' --host=' . $dbHost

                . ' --port=' . $dbPort

                . ' --user=' . $parameters['dbUser']

                . ' --password=' . str_replace('"', '\"', str_replace("'", "\'", quotemeta($parameters['dbPass'])))//no change! supports the type passwords: .\+*?[^]($)'"\"'

                . ' --database=' . mysql_real_escape_string($database)

                . ' --default_character_set utf8'

                . ' --execute="SOURCE '.$filename.'"';

            }else{

                $command = 'mysql'

                . ' --host=' . $dbHost

                . ' --user=' . $parameters['dbUser']

                . ' --password=' . str_replace('"', '\"', str_replace("'", "\'", quotemeta($parameters['dbPass'])))//no change! supports the type passwords: .\+*?[^]($)'"\"'

                . ' --database=' . mysql_real_escape_string($database)

                . ' --default_character_set utf8'

                . ' --execute="SOURCE '.$filename.'"';

            }

            shell_exec($command);

        } else {

            //If the safe mode of the server is actived

            try {

                mysql_select_db($database);



                //Replace TYPE by ENGINE

                $script = preg_replace($arrayRegExpEngineSearch, $arrayRegExpEngineReplace, file_get_contents($filename));



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

            if (strtoupper( substr( PHP_OS, 0, 3 ) ) === 'WIN') {

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

    static public function restore($filename, $srcWorkspace, $dstWorkspace = null, $overwrite = true, $lang = 'en', $port = '')

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



        $version = System::getVersion();

        $pmVersion = (preg_match("/^([\d\.]+).*$/", $version, $arrayMatch))? $arrayMatch[1] : ""; //Otherwise: Branch master



        CLI::logging(CLI::warning("

            Warning: A workspace from a newer version of ProcessMaker can NOT be restored in an older version of

            ProcessMaker. For example, restoring from v.3.0 to v.2.5 will not work. However, it may be possible

            to restore a workspace from an older version to an newer version of ProcessMaker, although error

            messages may be displayed during the restore process.") . "\n");



        foreach ($metaFiles as $metaFile) {

            $metadata = G::json_decode(file_get_contents($metaFile));

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

            $workspace = new workspaceTools($workspaceName);



            if ($workspace->workspaceExists()) {

                if ($overwrite) {

                    if ($workspace->dbInfo['DB_NAME'] == $workspace->dbInfo['DB_RBAC_NAME']) {

                        $newDatabases = 1;

                    } else {

                        $newDatabases = 3;

                    }



                    if ($newDatabases != $oldDatabases) {

                        throw new Exception("We can't overwrite this workspace because it has a different amount of databases. Not only the 'source' but also the 'target' must have the same amount of databases.");

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

                workspaceTools::dirPerms($workspace->path, $shared_stat['uid'], $shared_stat['gid'], $shared_stat['mode']);

            } else {

                CLI::logging(CLI::error("Could not get the shared folder permissions, not changing workspace permissions") . "\n");

            }

            list ($dbHost, $dbUser, $dbPass) = @explode(SYSTEM_HASH, G::decrypt(HASH_INSTALLATION, SYSTEM_HASH));

            if($port != ''){

               $dbHost = $dbHost.$port; //127.0.0.1:3306

            }

            $aParameters = array('dbHost'=>$dbHost,'dbUser'=>$dbUser,'dbPass'=>$dbPass);



            //Restore

            if (!defined("SYS_SYS")) {

                define("SYS_SYS", $workspaceName);

            }



            if (!defined("PATH_DATA_SITE")) {

                define("PATH_DATA_SITE", PATH_DATA . "sites" . PATH_SEP . SYS_SYS . PATH_SEP);

            }



            $pmVersionWorkspaceToRestore = (preg_match("/^([\d\.]+).*$/", $metadata->PM_VERSION, $arrayMatch))? $arrayMatch[1] : "";



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

                    $dbName = $newDBNames[$db->name];



                    if (mysql_select_db($dbName, $link)) {

                        if(!$overwrite) {

                            throw new Exception("Destination Database already exist (use -o to overwrite)");

                        }

                    }



                    CLI::logging("+> Restoring database {$db->name} to $dbName\n");

                    $workspace->executeSQLScript($dbName, "$tempDirectory/{$db->name}.sql",$aParameters);

                    $workspace->createDBUser($dbName, $db->pass, "localhost", $dbName);

                    $workspace->createDBUser($dbName, $db->pass, "%", $dbName);

                }

            }



            //CLI::logging(CLI::info("$pmVersionWorkspaceToRestore < $pmVersion") . "\n");



            if (version_compare($pmVersionWorkspaceToRestore . "", $pmVersion . "", "<") || $pmVersion == "") {

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



            if (version_compare($pmVersionWorkspaceToRestore . "", $pmVersion . "", "<") || $pmVersion == "") {

                $start = microtime(true);

                CLI::logging("> Updating cache view...\n");

                $workspace->upgradeCacheView(true, true, $lang);

                $stop = microtime(true);

                CLI::logging("<*>   Updating cache view Process took " . ($stop - $start) . " seconds.\n");

            } else {

                $workspace->upgradeTriggersOfTables(true, $lang);

            }



            /*----------------------------------********---------------------------------*/



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



        $f = ($arrayPathInfo["dirname"] == ".")? $dirHotfix . PATH_SEP . $file : $file;



        $swv  = 1;

        $msgv = "";



        if (!file_exists($dirHotfix)) {

            G::mk_dir($dirHotfix, 0777);

        }



        if (!file_exists($f)) {

            $swv  = 0;

            $msgv = $msgv . (($msgv != "")? "\n": null) . "- The file \"$f\" does not exist";

        }



        if ($arrayPathInfo["extension"] != "tar") {

            $swv  = 0;

            $msgv = $msgv . (($msgv != "")? "\n": null) . "- The file extension \"$file\" is not \"tar\"";

        }



        if ($swv == 1) {

            G::LoadThirdParty("pear/Archive", "Tar");



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



    public function checkMafeRequirements ($workspace,$lang) {

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

                if (file_exists($envFile) ) {

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



    public function changeHashPassword ($workspace, $response)

    {

        $this->initPropel( true );

        G::LoadClass("enterprise");

        $licensedFeatures = & PMLicensedFeatures::getSingleton();

        /*----------------------------------********---------------------------------*/

        return true;

    }



    public function verifyFilesOldEnterprise ($workspace)

    {

        $this->initPropel( true );

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

                workspaceTools::dirPerms($pathDirectoryEnterprise, $shared_stat['uid'], $shared_stat['gid'], $shared_stat['mode']);

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

                workspaceTools::dirPerms($pathFileEnterprise, $shared_stat['uid'], $shared_stat['gid'], $shared_stat['mode']);

            } else {

                CLI::logging(CLI::error("Could not get shared folder permissions, workspace permissions couldn't be changed") . "\n");

            }

            CLI::logging("    Removing $pathFileEnterprise...\n");

            copy($pathFileEnterprise , $pathNewFile. PATH_SEP . 'enterprise.php');

            G::rm_dir($pathFileEnterprise);

            if (file_exists($pathFileEnterprise)) {

                CLI::logging(CLI::info("    Remove manually $pathFileEnterprise...\n"));

            }

        }

    }



    public function verifyLicenseEnterprise ($workspace)

    {

        $this->initPropel( true );



        require_once ("classes/model/LicenseManager.php");

        $oCriteria = new Criteria('workflow');

        $oCriteria->add(LicenseManagerPeer::LICENSE_STATUS, 'ACTIVE');

        $oDataset = LicenseManagerPeer::doSelectRS($oCriteria);

        $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);

        $row = array();

        if ($oDataset->next()) {

            $row = $oDataset->getRow();



            $tr = LicenseManagerPeer::retrieveByPK ( $row['LICENSE_UID'] );

            $pos = strpos( $row['LICENSE_PATH'], 'license_' );

            $license = substr( $row['LICENSE_PATH'], $pos, strlen($row['LICENSE_PATH']));

            $tr->setLicensePath   ( PATH_DATA . "sites/" . $workspace . "/licenses/" . $license);

            $tr->setLicenseWorkspace ( $workspace );



            $res = $tr->save ();

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

     * return array Return array with disabled code found, array empty otherwise

     */

    public function getDisabledCode()

    {

        try {

            $this->initPropel(true);



            G::LoadClass("processes");



            $process = new Processes();



            //Return

            return $process->getDisabledCode();

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

     * return all LIST TABLES with data

     */

    public function migrateList($workSpace, $flagReinsert = false)

    {

        $this->initPropel(true);



        G::LoadClass("case");



        if (!$flagReinsert && $this->listFirstExecution("check")) {

            return 1;

        }



        if ($flagReinsert) {

            //Delete all records

            $arrayTable = array("ListInbox", "ListMyInbox", "ListCanceled", "ListParticipatedLast", "ListParticipatedHistory", "ListPaused", "ListCompleted", "ListUnassigned", "ListUnassignedGroup");



            foreach ($arrayTable as $value) {

                $tableName = $value . "Peer";



                $list = new $tableName();

                $list->doDeleteAll();

            }



            //Update //User

            $criteriaSet = new Criteria("workflow");

            $criteriaSet->add(UsersPeer::USR_TOTAL_INBOX, 0);

            $criteriaSet->add(UsersPeer::USR_TOTAL_DRAFT, 0);

            $criteriaSet->add(UsersPeer::USR_TOTAL_CANCELLED, 0);

            $criteriaSet->add(UsersPeer::USR_TOTAL_PARTICIPATED, 0);

            $criteriaSet->add(UsersPeer::USR_TOTAL_PAUSED, 0);

            $criteriaSet->add(UsersPeer::USR_TOTAL_COMPLETED, 0);

            $criteriaSet->add(UsersPeer::USR_TOTAL_UNASSIGNED, 0);



            $criteriaWhere = new Criteria("workflow");

            $criteriaWhere->add(UsersPeer::USR_UID, null, Criteria::ISNOTNULL);



            BasePeer::doUpdate($criteriaWhere, $criteriaSet, Propel::getConnection("workflow"));

        }



        $appCache = new AppCacheView();

        $users    = new Users();

        $case = new Cases();



        //Select data CANCELLED

        $canCriteria = $appCache->getSelAllColumns();

        $canCriteria->add(AppCacheViewPeer::APP_STATUS, "CANCELLED", CRITERIA::EQUAL);

        $canCriteria->add(AppCacheViewPeer::DEL_LAST_INDEX, "1", CRITERIA::EQUAL);

        $rsCriteria = AppCacheViewPeer::doSelectRS($canCriteria);

        $rsCriteria->setFetchmode(ResultSet::FETCHMODE_ASSOC);

        //Insert data LIST_CANCELED

        while ($rsCriteria->next()) {

              $row = $rsCriteria->getRow();

              $listCanceled = new ListCanceled();

              $listCanceled->remove($row["APP_UID"]);

              $listCanceled->setDeleted(false);

              $listCanceled->create($row);

        }

        CLI::logging("> Completed table LIST_CANCELED\n");



        //Select data COMPLETED

        $comCriteria = $appCache->getSelAllColumns();

        $comCriteria->add(AppCacheViewPeer::APP_STATUS, "COMPLETED", CRITERIA::EQUAL);

        $comCriteria->add(AppCacheViewPeer::DEL_LAST_INDEX, "1", CRITERIA::EQUAL);

        $rsCriteria = AppCacheViewPeer::doSelectRS($comCriteria);

        $rsCriteria->setFetchmode(ResultSet::FETCHMODE_ASSOC);

        //Insert new data LIST_COMPLETED

        while ($rsCriteria->next()) {

              $row = $rsCriteria->getRow();

              $listCompleted = new ListCompleted();

              $listCompleted->remove($row["APP_UID"]);

              $listCompleted->setDeleted(false);

              $listCompleted->create($row);

        }

        CLI::logging("> Completed table LIST_COMPLETED\n");



        //Select data TO_DO OR DRAFT

        $inbCriteria = $appCache->getSelAllColumns();

        $rsCriteria = AppCacheViewPeer::doSelectRS($inbCriteria);

        $rsCriteria->setFetchmode(ResultSet::FETCHMODE_ASSOC);



        $criteriaUser = new Criteria();

        $criteriaUser->addSelectColumn( UsersPeer::USR_UID );

        $criteriaUser->addSelectColumn( UsersPeer::USR_FIRSTNAME );

        $criteriaUser->addSelectColumn( UsersPeer::USR_LASTNAME );

        $criteriaUser->addSelectColumn( UsersPeer::USR_USERNAME );

        //Insert new data LIST_INBOX

        while ($rsCriteria->next()) {

            $row = $rsCriteria->getRow();

            $isSelfService = ($row['USR_UID'] == '') ? true : false;

            if($row["DEL_THREAD_STATUS"] == 'OPEN'){

                //Update information about the previous_user

                $row["DEL_PREVIOUS_USR_UID"] = $row["PREVIOUS_USR_UID"];

                $criteriaUser->add( UsersPeer::USR_UID, $row["PREVIOUS_USR_UID"] );

                $datasetU = UsersPeer::doSelectRS($criteriaUser);

                $datasetU->setFetchmode(ResultSet::FETCHMODE_ASSOC);

                $datasetU->next();

                $arrayUsers = $datasetU->getRow();

                $row["DEL_PREVIOUS_USR_USERNAME"] = $arrayUsers["USR_USERNAME"];

                $row["DEL_PREVIOUS_USR_FIRSTNAME"]= $arrayUsers["USR_FIRSTNAME"];

                $row["DEL_PREVIOUS_USR_LASTNAME"] = $arrayUsers["USR_LASTNAME"];

                //Update the due date

                $row["DEL_DUE_DATE"]         = $row["DEL_TASK_DUE_DATE"];

                $listInbox = new ListInbox();

                $listInbox->remove($row["APP_UID"],$row["DEL_INDEX"]);

                $listInbox->setDeleted(false);

                $listInbox->create($row, $isSelfService);

            } else {

                // create participated List when the thread is CLOSED

                $listParticipatedHistory = new ListParticipatedHistory();

                $listParticipatedHistory->remove($row['APP_UID'], $row['DEL_INDEX']);

                $listParticipatedHistory = new ListParticipatedHistory();

                $listParticipatedHistory->create($row);



                $oCriteria = new Criteria('workflow');

                $oCriteria->add(ListParticipatedLastPeer::APP_UID, $row['APP_UID']);

                $oCriteria->add(ListParticipatedLastPeer::USR_UID, $row['USR_UID']);

                ListParticipatedLastPeer::doDelete($oCriteria);



                $listParticipatedLast = new ListParticipatedLast();

                $listParticipatedLast->create($row);

                $listParticipatedLast = new ListParticipatedLast();

                $listParticipatedLast->refresh($row);

            }



        }



        CLI::logging("> Completed table LIST_INBOX\n");

        //With this List is populated the LIST_PARTICIPATED_HISTORY and LIST_PARTICIPATED_LAST

        CLI::logging("> Completed table LIST_PARTICIPATED_HISTORY\n");

        CLI::logging("> Completed table LIST_PARTICIPATED_LAST\n");



        //Select data TO_DO OR DRAFT CASES CREATED BY AN USER

        $myiCriteria = $appCache->getSelAllColumns();

        $myiCriteria->add(AppCacheViewPeer::DEL_INDEX, "1", CRITERIA::EQUAL);

        $rsCriteria = AppCacheViewPeer::doSelectRS($myiCriteria);

        $rsCriteria->setFetchmode(ResultSet::FETCHMODE_ASSOC);

        //Insert new data LIST_MY_INBOX

        while ($rsCriteria->next()) {

              $row = $rsCriteria->getRow();

              $listMyInbox = new ListMyInbox();

              $listMyInbox ->remove($row["APP_UID"],$row["USR_UID"]);

              $listMyInbox->setDeleted(false);

              $listMyInbox->create($row);

        }

        CLI::logging("> Completed table LIST_MY_INBOX\n");



        //Select data PAUSED

        $delaycriteria = new Criteria("workflow");

        $delaycriteria->addSelectColumn(AppDelayPeer::APP_UID);

        $delaycriteria->addSelectColumn(AppDelayPeer::PRO_UID);

        $delaycriteria->addSelectColumn(AppDelayPeer::APP_DEL_INDEX);

        $delaycriteria->addSelectColumn(AppCacheViewPeer::APP_NUMBER);

        $delaycriteria->addSelectColumn(AppCacheViewPeer::USR_UID);

        $delaycriteria->addSelectColumn(AppCacheViewPeer::APP_STATUS);

        $delaycriteria->addSelectColumn(AppCacheViewPeer::TAS_UID);



        $delaycriteria->addJoin( AppCacheViewPeer::APP_UID, AppDelayPeer::APP_UID . ' AND ' . AppCacheViewPeer::DEL_INDEX . ' = ' . AppDelayPeer::APP_DEL_INDEX, Criteria::INNER_JOIN );

        $delaycriteria->add(AppDelayPeer::APP_DISABLE_ACTION_USER, "0", CRITERIA::EQUAL);

        $delaycriteria->add(AppDelayPeer::APP_TYPE, "PAUSE", CRITERIA::EQUAL);

        $rsCriteria = AppDelayPeer::doSelectRS($delaycriteria);

        $rsCriteria->setFetchmode(ResultSet::FETCHMODE_ASSOC);

        //Insert new data LIST_PAUSED

        while ($rsCriteria->next()) {

              $row = $rsCriteria->getRow();

              $data = $row;

              $data["DEL_INDEX"] = $row["APP_DEL_INDEX"];

              $listPaused = new ListPaused();

              $listPaused ->remove($row["APP_UID"],$row["APP_DEL_INDEX"],$data);

              $listPaused->setDeleted(false);

              $listPaused->create($data);

        }

        CLI::logging("> Completed table LIST_PAUSED\n");



        //Select and Insert LIST_UNASSIGNED

        $unaCriteria = $appCache->getSelAllColumns();

        $unaCriteria->add(AppCacheViewPeer::USR_UID, "", CRITERIA::EQUAL);

        $rsCriteria = AppCacheViewPeer::doSelectRS($unaCriteria);

        $rsCriteria->setFetchmode(ResultSet::FETCHMODE_ASSOC);

        $del = new ListUnassignedPeer();

        $del->doDeleteAll();

        $del = new ListUnassignedGroupPeer();

        $del->doDeleteAll();

        while ($rsCriteria->next()) {

            $row = $rsCriteria->getRow();

            $listUnassigned = new ListUnassigned();

            $unaUid = $listUnassigned->generateData($row["APP_UID"],$row["PREVIOUS_USR_UID"]);

        }

        CLI::logging("> Completed table LIST_UNASSIGNED\n");

        CLI::logging("> Completed table LIST_UNASSIGNED_GROUP\n");



        // ADD LISTS COUNTS

        $aTypes = array(

            'to_do',

            'draft',

            'cancelled',

            'sent',

            'paused',

            'completed',

            'selfservice'

        );



        $users = new Users();

        $criteria = new Criteria();

        $criteria->addSelectColumn(UsersPeer::USR_UID);

        $dataset = UsersPeer::doSelectRS($criteria);

        $dataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);

        while($dataset->next()) {

            $aRow = $dataset->getRow();

            $oAppCache = new AppCacheView();

            $aCount = $oAppCache->getAllCounters( $aTypes, $aRow['USR_UID'] );

            $newData = array(

                'USR_UID'                   => $aRow['USR_UID'],

                'USR_TOTAL_INBOX'           => $aCount['to_do'],

                'USR_TOTAL_DRAFT'           => $aCount['draft'],

                'USR_TOTAL_CANCELLED'       => $aCount['cancelled'],

                'USR_TOTAL_PARTICIPATED'    => $aCount['sent'],

                'USR_TOTAL_PAUSED'          => $aCount['paused'],

                'USR_TOTAL_COMPLETED'       => $aCount['completed'],

                'USR_TOTAL_UNASSIGNED'      => $aCount['selfservice']

            );

            $users->update($newData);

        }



        if (!$flagReinsert) {

            $this->listFirstExecution("insert");

        }



        //Return

        return true;

    }



    /**

     * This function checks if List tables are going to migrated

     *

     * return boolean value

     */

    public function listFirstExecution ($action){

        $this->initPropel(true);

        switch ($action) {

           case 'insert':

                $conf  = new Configuration();

                if (!($conf->exists('MIGRATED_LIST', 'list', 'list', 'list', 'list'))) {

                    $data["CFG_UID"]  ='MIGRATED_LIST';

                    $data["OBJ_UID"]  ='list';

                    $data["CFG_VALUE"]='true';

                    $data["PRO_UID"]  ='list';

                    $data["USR_UID"]  ='list';

                    $data["APP_UID"]  ='list';

                    $conf->create($data);

                }

                return true;

                break;

           case 'check':

                $criteria = new Criteria("workflow");

                $criteria->addSelectColumn(ConfigurationPeer::CFG_UID);

                $criteria->add(ConfigurationPeer::CFG_UID, "MIGRATED_LIST", CRITERIA::EQUAL);

                $rsCriteria = AppCacheViewPeer::doSelectRS($criteria);

                $rsCriteria->setFetchmode(ResultSet::FETCHMODE_ASSOC);

                $aRows = array ();

                while ($rsCriteria->next()) {

                    $aRows[] = $rsCriteria->getRow();

                }

                if(empty($aRows)){

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

}
