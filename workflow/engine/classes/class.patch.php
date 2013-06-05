<?php

G::LoadClass("Task");
G::LoadClass("TaskUser");
G::LoadClass("System");

/**
 * class, helping to set some not desirable settings but necesary
 * @author reav
 *
 */

abstract class patch
{
    static protected $isPathchable = false;
    abstract static public function isApplicable();
    abstract static public function execute();
}

class p11835 extends patch 
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
        echo $count. " records where patched to use SELF_SERVICE feature.\n";
    }
}

