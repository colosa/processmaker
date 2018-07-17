<?php

use ProcessMaker\Core\System;

class P11835 extends Patch
{
    /*
     * Note.- Use before upgrade DB.
     * Check if the table TASK has the field TAS_GROUP_VARIABLE
     * @return boolean
     */
    static public function isApplicable()
    {
        patch::$isPathchable = false;
        $con = Propel::getConnection("workflow");
        $stmt = $con->prepareStatement("describe TASK;");
        $rs = $stmt->executeQuery();
        $rs->next();
        while($row = $rs->getRow()) {
            if ($row ['Field'] == "TAS_GROUP_VARIABLE") {
                $version = System::getVersion ();
                $version = explode('-',$version);
                if ($version[0] == '2.5.1') {
                    echo "Version " . $version[0] . " Patch\n";
                    patch::$isPathchable = true;
                }
                break;
            }
            $rs->next();
        }
        return patch::$isPathchable;
    }

    public static function pmVersion($version)
    {
        if (preg_match("/^\D*([\d\.]+)\D*$/", $version, $matches)) {
            $version = $matches[1];
        }
        
        return $version;
    }
    
    /*
     * Note.- Use after DB was upgraded.
     * Set the patch, setting all the TAS_GROUP_VARIABLE to '' 
     * if the current task has asignated users, means SELF_SERVICE only, 
     * otherwise leave TAS_GROUP_VARIABLE as it is.
     */
    static public function execute()
    {
        //Check if this is the version to apply the patch
        $count = 0;
        $task = new Task();
        if ( patch::$isPathchable && method_exists($task,'getTasGroupVariable')) {
            $con = Propel::getConnection("workflow");
            $stmt = $con->prepareStatement("select TAS_UID from TASK where TAS_ASSIGN_TYPE = 'SELF_SERVICE';");
            $recordSet = $stmt->executeQuery();
            $recordSet->next();
            $aRow = $recordSet->getRow();
            while ($aRow) {
                $tasUid = $aRow['TAS_UID'];
                $conUser = Propel::getConnection("workflow");
                $stmtUser = $conUser->prepareStatement("select * from TASK_USER where TAS_UID = '".$tasUid."';");
                $recordSetTaskUser = $stmtUser->executeQuery();
                if ($recordSetTaskUser->next()) {
                    echo "Patching uid: " . $tasUid . "\n";
                    //Set the values if they match the pattern
                    $conChange = Propel::getConnection("workflow");
                    $stmtChange = $conChange->prepareStatement("update TASK set TAS_GROUP_VARIABLE = '' where TAS_UID = '".$tasUid."';");
                    $recordResult = $stmtChange->executeQuery();
                    $count++;
                }
                $recordSet->next();
                $aRow = $recordSet->getRow();
            }
        }
        
        $conf = new Configurations();
        
        if (!$conf->exists("HOTFIX")) {
            //HOTFIX, Create empty record
            $conf->aConfig = array();
            $conf->saveConfig("HOTFIX", "");
        }
        
        $arrayHotfix = $conf->getConfiguration("HOTFIX", "");
        $arrayHotfix = (is_array($arrayHotfix))? $arrayHotfix : array($arrayHotfix);
        
        $pmVersion = self::pmVersion(System::getVersion()) . "";
        
        if (($pmVersion == "2.5.2.4" || $pmVersion == "2.8") && !in_array("15394", $arrayHotfix)) {
            $cnn = Propel::getConnection("workflow");
            $stmt = $cnn->prepareStatement("UPDATE USERS_PROPERTIES SET USR_LOGGED_NEXT_TIME = 0");
            $rs = $stmt->executeQuery();
        
            //HOTFIX, Update record (add 15394)
            $arrayHotfix[] = "15394";
        
            $conf->aConfig = $arrayHotfix;
            $conf->saveConfig("HOTFIX", "");
        }
        
        //echo
        echo $count . " records where patched to use SELF_SERVICE feature.\n";
    }
}
