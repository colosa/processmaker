<?php
class ldapadvancedClassCron
{
    public $deletedRemoved = 0; //Users in the removed OU
    public $deletedRemovedUsers = "";

    public $dAlready    = 0; //Count for already existing users
    public $dMoved      = 0; //Users moved from a Department to another Department
    public $dImpossible = 0; //Users already created using another Authentication source
    public $dCreated    = 0; //Users created
    public $dRemoved    = 0; //Users removed
    public $dAlreadyUsers    = "";
    public $dMovedUsers      = "";
    public $dImpossibleUsers = "";
    public $dCreatedUsers    = "";
    public $dRemovedUsers    = "";

    public $gAlready    = 0;
    public $gMoved      = 0;
    public $gImpossible = 0;
    public $gCreated    = 0;
    public $gRemoved    = 0;
    public $gAlreadyUsers    = "";
    public $gMovedUsers      = "";
    public $gImpossibleUsers = "";
    public $gCreatedUsers    = "";
    public $gRemovedUsers    = "";

    public $managersHierarchy    = array();
    public $oldManagersHierarchy = array();
    public $managersToClear      = array();
    public $deletedManager       = 0;

    public function __construct()
    {
    }

    /**
      function executed by the cron
      this function will synchronize users from ldap/active directory to PM users tables
      @return void
    */
    public function executeCron($debug)
    {
        $rbac = &RBAC::getSingleton();

        if (is_null($rbac->authSourcesObj)) {
            $rbac->authSourcesObj = new AuthenticationSource();
        }

        $plugin = new LdapAdvanced();
        $plugin->sSystem = $rbac->sSystem;

        $plugin->setFrontEnd(true);
        $plugin->setDebug($debug);

        //Get all authsource for this plugin ( ldapAdvanced plugin, because other authsources are not needed )
        $arrayAuthenticationSource = $plugin->getAuthSources();

        $aDepartments = $plugin->getDepartments("");
        $aGroups = $plugin->getGroups();

        //$arrayDepartmentUserAd = array(); //(D) Update Users
        //$arrayGroupUserAd = array(); //(G) Update Users

        //echo "\n";

        $plugin->frontEndShow("START");

        $plugin->debugLog("START");

        foreach ($arrayAuthenticationSource as $value) {
            $arrayAuthenticationSourceData = $value;

            $plugin->debugLog("ldapadvanced.php > function executeCron() > foreach > \$arrayAuthenticationSourceData ---->\n" . print_r($arrayAuthenticationSourceData, true));

            $plugin->sAuthSource = $arrayAuthenticationSourceData["AUTH_SOURCE_UID"];
            $plugin->ldapcnn = null;

            $plugin->setArrayDepartmentUserSynchronizedChecked(array());
            $plugin->setArrayUserUpdateChecked(array());

            //Get all User (USR_UID, USR_USERNAME, USR_AUTH_USER_DN) registered in RBAC with this Authentication Source
            $plugin->setArrayAuthenticationSourceUsers($arrayAuthenticationSourceData["AUTH_SOURCE_UID"]); //INITIALIZE DATA

            $plugin->frontEndShow("TEXT", "Authentication Source: " . $arrayAuthenticationSourceData["AUTH_SOURCE_NAME"]);

            $plugin->log(null, "Executing cron for Authentication Source: " . $arrayAuthenticationSourceData["AUTH_SOURCE_NAME"]);

            //Get all departments from Ldap/ActiveDirectory and build a hierarchy using dn (ou->ou parent)
            $aLdapDepts = $plugin->searchDepartments();

            //Obtain all departments from PM with a valid department in LDAP/ActiveDirectory
            $aRegisteredDepts = $plugin->getRegisteredDepartments($aLdapDepts, $aDepartments);

            $plugin->debugLog("ldapadvanced.php > function executeCron() > foreach > \$aRegisteredDepts ---->\n" . print_r($aRegisteredDepts, true));

            //Get all group from Ldap/ActiveDirectory
            $aLdapGroups = $plugin->searchGroups();

            //Obtain all groups from PM with a valid group in LDAP/ActiveDirectory
            $aRegisteredGroups = $plugin->getRegisteredGroups($aLdapGroups, $aGroups);

            $plugin->debugLog("ldapadvanced.php > function executeCron() > foreach > \$aRegisteredGroups ---->\n" . print_r($aRegisteredGroups, true));

            //Get all users from Removed OU
            $this->usersRemovedOu = $plugin->getUsersFromRemovedOu($arrayAuthenticationSourceData);
            $plugin->deactiveArrayOfUsers($this->usersRemovedOu);

            //Variables
            $this->deletedRemoved = count($this->usersRemovedOu);
            $this->deletedRemovedUsers = "";

            $this->dAlready    = 0;
            $this->dMoved      = 0;
            $this->dImpossible = 0;
            $this->dCreated    = 0;
            $this->dRemoved    = 0;
            $this->dAlreadyUsers    = "";
            $this->dMovedUsers      = "";
            $this->dImpossibleUsers = "";
            $this->dCreatedUsers    = "";
            $this->dRemovedUsers    = "";

            $this->gAlready    = 0;
            $this->gMoved      = 0;
            $this->gImpossible = 0;
            $this->gCreated    = 0;
            $this->gRemoved    = 0;
            $this->gAlreadyUsers    = "";
            $this->gMovedUsers      = "";
            $this->gImpossibleUsers = "";
            $this->gCreatedUsers    = "";
            $this->gRemovedUsers    = "";

            //Department - Synchronize Users
            $numDepartments = count($aRegisteredDepts);
            $count = 0;

            $plugin->debugLog("ldapadvanced.php > function executeCron() > foreach > \$numDepartments ----> $numDepartments");

            foreach ($aRegisteredDepts as $registeredDept) {
                $count++;

                //(D) Update Users
                //if (!isset($arrayDepartmentUserAd[$registeredDept["DEP_UID"]])) {
                //    $arrayDepartmentUserAd[$registeredDept["DEP_UID"]] = array(); //Current users in department based in Active Directory
                //}
                //
                //$arrayAux = $this->departmentSynchronizeUsers($plugin, $numDepartments, $count, $registeredDept);
                //$arrayAux = array_merge($arrayDepartmentUserAd[$registeredDept["DEP_UID"]], $arrayAux);
                //
                //$arrayDepartmentUserAd[$registeredDept["DEP_UID"]] = array_unique($arrayAux);

                $arrayAux = $this->departmentSynchronizeUsers($plugin, $numDepartments, $count, $registeredDept);
            }

            //Department - Print log
            $logResults = sprintf(
                "- Departments -> Existing users: %d, moved: %d, impossible: %d, created: %d, removed: %d",
                $this->dAlready,
                $this->dMoved,
                $this->dImpossible,
                $this->dCreated,
                $this->dRemoved
            );

            $plugin->frontEndShow("TEXT", $logResults);

            $plugin->log(null, $logResults);

            //Group - Synchronize Users
            $numGroups = count($aRegisteredGroups);
            $count = 0;

            $plugin->debugLog("ldapadvanced.php > function executeCron() > foreach > \$numGroups ----> $numGroups");

            foreach ($aRegisteredGroups as $registeredGroup) {
                $count++;

                //(G) Update Users
                //if (!isset($arrayGroupUserAd[$registeredGroup["GRP_UID"]])) {
                //    $arrayGroupUserAd[$registeredGroup["GRP_UID"]] = array(); //Current users in group based in Active Directory
                //}
                //
                //$arrayAux = $this->groupSynchronizeUsers($plugin, $numGroups, $count, $registeredGroup);
                //$arrayAux = array_merge($arrayGroupUserAd[$registeredGroup["GRP_UID"]], $arrayAux);
                //
                //$arrayGroupUserAd[$registeredGroup["GRP_UID"]] = array_unique($arrayAux);

                $arrayAux = $this->groupSynchronizeUsers($plugin, $numGroups, $count, $registeredGroup);
            }

            //Group - Print log
            $logResults = sprintf(
                "- Groups -> Existing users: %d, moved: %d, impossible: %d, created: %d, removed: %d",
                $this->gAlready,
                $this->gMoved,
                $this->gImpossible,
                $this->gCreated,
                $this->gRemoved
            );

            $plugin->frontEndShow("TEXT", $logResults);

            $plugin->log(null, $logResults);

            //Manager
            $plugin->clearManager($this->managersToClear);

            if (isset($arrayAuthenticationSourceData["AUTH_SOURCE_DATA"]["DEPARTMENTS_TO_UNASSIGN"])) {
                if (is_array($arrayAuthenticationSourceData["AUTH_SOURCE_DATA"]["DEPARTMENTS_TO_UNASSIGN"])) {
                    foreach ($arrayAuthenticationSourceData["AUTH_SOURCE_DATA"]["DEPARTMENTS_TO_UNASSIGN"] as $departmentUID) {
                        // Delete manager assignments
                        $criteriaSet = new Criteria("workflow");
                        $criteriaSet->add(UsersPeer::USR_REPORTS_TO, "");
                        $criteriaWhere = new Criteria("workflow");
                        $criteriaWhere->add(UsersPeer::DEP_UID, $departmentUID);
                        $criteriaWhere->add(UsersPeer::USR_REPORTS_TO, "", Criteria::NOT_EQUAL);
                        $this->deletedManager = BasePeer::doUpdate($criteriaWhere, $criteriaSet, Propel::getConnection("workflow"));
                        // Delete department assignments
                        $criteriaSet = new Criteria("workflow");
                        $criteriaSet->add(UsersPeer::DEP_UID, "");
                        $criteriaWhere = new Criteria("workflow");
                        $criteriaWhere->add(UsersPeer::DEP_UID, $departmentUID);
                        $this->dMoved += UsersPeer::doCount($criteriaWhere);
                        BasePeer::doUpdate($criteriaWhere, $criteriaSet, Propel::getConnection("workflow"));
                    }
                }

                unset($arrayAuthenticationSourceData["AUTH_SOURCE_DATA"]["DEPARTMENTS_TO_UNASSIGN"]);

                $rbac = &RBAC::getSingleton();
                $rbac->authSourcesObj->update($arrayAuthenticationSourceData);
            }

            if (isset($arrayAuthenticationSourceData["AUTH_SOURCE_DATA"]["GROUPS_TO_UNASSIGN"])) {
                if (is_array($arrayAuthenticationSourceData["AUTH_SOURCE_DATA"]["GROUPS_TO_UNASSIGN"])) {
                    foreach ($arrayAuthenticationSourceData["AUTH_SOURCE_DATA"]["GROUPS_TO_UNASSIGN"] as $groupUID) {
                        // Delete manager assignments
                        $groupsInstance = new Groups();
                        $criteria = $groupsInstance->getUsersGroupCriteria($groupUID);
                        $dataset = UsersPeer::doSelectRS($criteria);
                        $dataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
                        $dataset->next();
                        $users = array();

                        while ($row = $dataset->getRow()) {
                            $users[] = $row["USR_UID"];
                            $dataset->next();
                        }

                        $criteriaSet = new Criteria("workflow");
                        $criteriaSet->add(UsersPeer::USR_REPORTS_TO, "");
                        $criteriaWhere = new Criteria("workflow");
                        $criteriaWhere->add(UsersPeer::USR_UID, $users, Criteria::IN);
                        $criteriaWhere->add(UsersPeer::USR_REPORTS_TO, "", Criteria::NOT_EQUAL);
                        $this->deletedManager = BasePeer::doUpdate($criteriaWhere, $criteriaSet, Propel::getConnection("workflow"));

                        // Delete group assignments
                        $criteria = new Criteria("workflow");
                        $criteria->add(GroupUserPeer::GRP_UID, $groupUID);
                        $this->gMoved += GroupUserPeer::doCount($criteria);
                        BasePeer::doDelete($criteria, Propel::getConnection("workflow"));
                    }
                }

                unset($arrayAuthenticationSourceData["AUTH_SOURCE_DATA"]["GROUPS_TO_UNASSIGN"]);

                $rbac = &RBAC::getSingleton();
                $rbac->authSourcesObj->update($arrayAuthenticationSourceData);
            }

            // Delete the managers that not exists in PM
            $criteria = new Criteria("rbac");
            $criteria->addSelectColumn(RbacUsersPeer::USR_AUTH_USER_DN);
            $criteria->add(RbacUsersPeer::USR_AUTH_USER_DN, "", Criteria::NOT_EQUAL);
            $dataset = RbacUsersPeer::doSelectRS($criteria);
            $dataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $dataset->next();
            $existingUsers = array();

            while ($row = $dataset->getRow()) {
                $existingUsers[] = $row["USR_AUTH_USER_DN"];
                $dataset->next();
            }

            foreach ($this->managersHierarchy as $managerDN => $subordinates) {
                if (!in_array($managerDN, $existingUsers)) {
                    unset($this->managersHierarchy[$managerDN]);
                }
            }

            // Get the managers assigments counters
            $plugin->synchronizeManagers($this->managersHierarchy);

            $deletedManagersAssignments = self::array_diff_assoc_recursive($this->oldManagersHierarchy, $this->managersHierarchy);
            $newManagersAssignments = self::array_diff_assoc_recursive($this->managersHierarchy, $this->oldManagersHierarchy);
            $deletedManagers = array();
            $newManagers = array();
            $movedManagers = array();

            if (is_array($deletedManagersAssignments)) {
                foreach ($deletedManagersAssignments as $dn1 => $subordinates1) {
                    foreach ($subordinates1 as $subordinate) {
                        if (!in_array($subordinate, $deletedManagers)) {
                            $deletedManagers[] = $subordinate;
                        }

                        foreach ($newManagersAssignments as $dn2 => $subordinates2) {
                            if (isset($subordinates2[$subordinate])) {
                                $movedManagers[] = $subordinate;
                            }
                        }
                    }
                }
            }

            if (is_array($newManagersAssignments)) {
                foreach ($newManagersAssignments as $dn1 => $subordinates1) {
                    foreach ($subordinates1 as $subordinate) {
                        if (!in_array($subordinate, $newManagers)) {
                            $newManagers[] = $subordinate;
                        }

                        foreach ($deletedManagersAssignments as $dn2 => $subordinates2) {
                            if (isset($subordinates2[$subordinate])) {
                                if (!in_array($subordinate, $movedManagers)) {
                                    $movedManagers[] = $subordinate;
                                }
                            }
                        }
                    }
                }
            }

            //Print and log the users's information
            //Deleted/Removed Users
            $logResults = sprintf("- Deleted/Removed Users: %d", $this->deletedRemoved);

            $plugin->frontEndShow("TEXT", $logResults);

            $plugin->log(null, $logResults);

            if ($this->deletedRemoved > 0) {
                 $plugin->log(null, "Deleted/Removed Users: ");
                 $plugin->log(null, $this->deletedRemovedUsers);
            }

            if ($this->dAlready + $this->gAlready > 0) {
                 $plugin->log(null, "Existing Users: ");
                 $plugin->log(null, $this->dAlreadyUsers . " " . $this->gAlreadyUsers);
            }

            if ($this->dMoved + $this->gMoved > 0) {
                 $plugin->log(null, "Moved Users: ");
                 $plugin->log(null, $this->dMovedUsers . " " . $this->gMovedUsers);
            }

            if ($this->dImpossible + $this->gImpossible > 0) {
                 $plugin->log(null, "Impossible Users: ");
                 $plugin->log(null, $this->dImpossibleUsers . " " . $this->gImpossibleUsers);
            }

            if ($this->dCreated + $this->gCreated > 0) {
                 $plugin->log(null, "Created Users: ");
                 $plugin->log(null, $this->dCreatedUsers . " " . $this->gCreatedUsers);
            }

            if ($this->dRemoved + $this->gRemoved > 0) {
                $plugin->log(null, "Removed Users: ");
                $plugin->log(null, $this->dRemovedUsers . " " . $this->gRemovedUsers);
            }

            //Print and log the managers assignments"s information
            $logResults = sprintf(
                "- Managers assignments: created %d, moved %d, removed %d",
                count($newManagers) - count($movedManagers),
                count($movedManagers),
                count($deletedManagers) - count($movedManagers) + $this->deletedManager
            );

            $plugin->frontEndShow("TEXT", $logResults);

            $plugin->log(null, $logResults);

            //Update Users data based on the LDAP Server
            $plugin->usersUpdateData($arrayAuthenticationSourceData["AUTH_SOURCE_UID"]);
        }

        $plugin->frontEndShow("END");

        //(D) Update Users
        ////Department //Upgrade users in departments
        //foreach ($arrayDepartmentUserAd as $departmentUid => $arrayUserAd) {
        //    $plugin->setArrayDepartmentUsers($departmentUid); //INITIALIZE DATA
        //
        //    $arrayAux = array_diff(array_keys($plugin->arrayDepartmentUsersByUid), $arrayUserAd);
        //
        //    $this->departmentRemoveUsers($departmentUid, $arrayAux);
        //}

        //(G) Update Users
        ////Group //Upgrade users in groups
        //foreach ($arrayGroupUserAd as $groupUid => $arrayUserAd) {
        //    $plugin->setArrayGroupUsers($groupUid); //INITIALIZE DATA
        //
        //    $arrayAux = array_diff(array_keys($plugin->arrayGroupUsersByUid), $arrayUserAd);
        //
        //    $this->groupRemoveUsers($groupUid, $arrayAux);
        //}

        //// Developed by Gary and Ronald
        //$usersInfo = $plugin->ASUpdateInfo('');
        //if (isset($usersInfo) && $usersInfo > 0) {
        //    $this->dMoved = $usersInfo;
        //}
        //// End Developed by Gary and Ronald

        $plugin->debugLog("END");
    }

    public function array_diff_assoc_recursive($array1, $array2)
    {
        foreach ($array1 as $key => $value) {
            if (is_array($value)) {
                if (!isset($array2[$key])) {
                    $difference[$key] = $value;
                } else {
                    if (!is_array($array2[$key])) {
                        $difference[$key] = $value;
                    } else {
                        $new_diff = self::array_diff_assoc_recursive($value, $array2[$key]);

                        if ($new_diff != false) {
                            $difference[$key] = $new_diff;
                        }
                    }
                }
            } else {
                if (!isset($array2[$key]) || $array2[$key] != $value) {
                    $difference[$key] = $value;
                }
            }
        }

        return (!isset($difference))? array() : $difference;
    }

    public function departmentRemoveUsers($departmentUid, array $arrayUserUid)
    {
        try {
            $department = new Department();
            $department->Load($departmentUid);

            $departmentManagerUid = $department->getDepManager();

            foreach ($arrayUserUid as $value) {
                $userUid = $value;

                $department->removeUserFromDepartment($departmentUid, $userUid);

                if ($userUid == $departmentManagerUid) {
                    $department->update(array("DEP_UID" => $departmentUid, "DEP_MANAGER" => ""));

                    $department->updateDepartmentManager($departmentUid);
                }
            }
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function groupRemoveUsers($groupUid, array $arrayUserUid)
    {
        try {
            $group = new Groups();

            foreach ($arrayUserUid as $value) {
                $userUid = $value;

                $group->removeUserOfGroup($groupUid, $userUid);
            }
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function departmentSynchronizeUsers($ldapAdvanced, $numDepartments, $count, array $arrayDepartmentData)
    {
        try {
            $ldapAdvanced->debugLog("ldapadvanced.php > function departmentSynchronizeUsers() > START");
            $ldapAdvanced->debugLog("ldapadvanced.php > function departmentSynchronizeUsers() > \$arrayDepartmentData ---->\n" . print_r($arrayDepartmentData, true));

            //Get users from ProcessMaker tables (for this Department)
            $ldapAdvanced->setArrayDepartmentUsers($arrayDepartmentData["DEP_UID"]); //INITIALIZE DATA

            //Clear the manager assignments
            $arrayUserUid = array();

            foreach ($ldapAdvanced->arrayDepartmentUsersByUid as $key => $user) {
                $arrayUserUid[] = $user["USR_UID"];

                if (isset($user["USR_REPORTS_TO"]) && $user["USR_REPORTS_TO"] != "") {
                    $dn = (isset($ldapAdvanced->arrayAuthenticationSourceUsersByUid[$user["USR_REPORTS_TO"]]["USR_AUTH_USER_DN"]))? $ldapAdvanced->arrayAuthenticationSourceUsersByUid[$user["USR_REPORTS_TO"]]["USR_AUTH_USER_DN"] : "";

                    if ($dn != "") {
                        if (!isset($this->oldManagersHierarchy[$dn])) {
                            $this->oldManagersHierarchy[$dn] = array();
                        }

                        $this->oldManagersHierarchy[$dn][$user["USR_UID"]] = $user["USR_UID"];
                    }
                }
            }

            $this->managersToClear = $arrayUserUid;

            //Synchronize Users from Department
            //Now we need to go over ldapusers and check if the user exists in ldap but not in PM, then we need to create it
            $arrayData = array(
                "already"         => $this->dAlready,
                "moved"           => $this->dMoved,
                "impossible"      => $this->dImpossible,
                "created"         => $this->dCreated,
                "alreadyUsers"    => $this->dAlreadyUsers,
                "movedUsers"      => $this->dMovedUsers,
                "impossibleUsers" => $this->dImpossibleUsers,
                "createdUsers"    => $this->dCreatedUsers,

                "managersHierarchy" => $this->managersHierarchy,
                "arrayUserUid"      => array(),

                "n" => $numDepartments,
                "i" => $count
            );

            //Get Users from LDAP (for this Department)
            $arrayData = $ldapAdvanced->ldapGetUsersFromDepartment("SYNCHRONIZE", $arrayDepartmentData["DEP_LDAP_DN"], $arrayData);

            $this->dAlready         = $arrayData["already"];
            $this->dMoved           = $arrayData["moved"];
            $this->dImpossible      = $arrayData["impossible"];
            $this->dCreated         = $arrayData["created"];
            $this->dAlreadyUsers    = $arrayData["alreadyUsers"];
            $this->dMovedUsers      = $arrayData["movedUsers"];
            $this->dImpossibleUsers = $arrayData["impossibleUsers"];
            $this->dCreatedUsers    = $arrayData["createdUsers"];

            $this->managersHierarchy = $arrayData["managersHierarchy"];
            $arrayUserUid            = $arrayData["arrayUserUid"];

            //(D) Update Users
            $arrayAux = array_diff(array_keys($ldapAdvanced->arrayDepartmentUsersByUid), $arrayUserUid);

            $this->departmentRemoveUsers($arrayDepartmentData["DEP_UID"], $arrayAux);

            $this->dRemoved += count($arrayAux);
            $this->dRemovedUsers = "";

            $ldapAdvanced->debugLog("ldapadvanced.php > function departmentSynchronizeUsers() > END");

            //Return all UID of Users synchronized in the Department (Return all UID of Users of this Department)
            return $arrayUserUid;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function groupSynchronizeUsers($ldapAdvanced, $numGroups, $count, array $arrayGroupData)
    {
        try {
            $ldapAdvanced->debugLog("ldapadvanced.php > function groupSynchronizeUsers() > START");
            $ldapAdvanced->debugLog("ldapadvanced.php > function groupSynchronizeUsers() > \$arrayGroupData ---->\n" . print_r($arrayGroupData, true));

            //Get users from ProcessMaker tables (for this Group)
            $ldapAdvanced->setArrayGroupUsers($arrayGroupData["GRP_UID"]); //INITIALIZE DATA

            //Clear the manager assignments
            $arrayUserUid = array();

            foreach ($ldapAdvanced->arrayGroupUsersByUid as $key => $user) {
                $arrayUserUid[] = $user["USR_UID"];

                if (isset($user["USR_REPORTS_TO"]) && $user["USR_REPORTS_TO"] != "") {
                    $dn = (isset($ldapAdvanced->arrayAuthenticationSourceUsersByUid[$user["USR_REPORTS_TO"]]["USR_AUTH_USER_DN"]))? $ldapAdvanced->arrayAuthenticationSourceUsersByUid[$user["USR_REPORTS_TO"]]["USR_AUTH_USER_DN"] : "";

                    if ($dn != "") {
                        if (!isset($this->oldManagersHierarchy[$dn])) {
                            $this->oldManagersHierarchy[$dn] = array();
                        }

                        $this->oldManagersHierarchy[$dn][$user["USR_UID"]] = $user["USR_UID"];
                    }
                }
            }

            $this->managersToClear = array_merge($this->managersToClear, $arrayUserUid);

            //Synchronize Users from Group
            //Now we need to go over ldapusers and check if the user exists in ldap but not in PM, then we need to create it
            $arrayData = array(
                "already"         => $this->gAlready,
                "moved"           => $this->gMoved,
                "impossible"      => $this->gImpossible,
                "created"         => $this->gCreated,
                "alreadyUsers"    => $this->gAlreadyUsers,
                "movedUsers"      => $this->gMovedUsers,
                "impossibleUsers" => $this->gImpossibleUsers,
                "createdUsers"    => $this->gCreatedUsers,

                "managersHierarchy" => $this->managersHierarchy,
                "arrayUserUid"      => array(),

                "n" => $numGroups,
                "i" => $count
            );

            //Get Users from LDAP (for this Group)
            $arrayData = $ldapAdvanced->ldapGetUsersFromGroup("SYNCHRONIZE", $arrayGroupData, $arrayData);

            $this->gAlready         = $arrayData["already"];
            $this->gMoved           = $arrayData["moved"];
            $this->gImpossible      = $arrayData["impossible"];
            $this->gCreated         = $arrayData["created"];
            $this->gAlreadyUsers    = $arrayData["alreadyUsers"];
            $this->gMovedUsers      = $arrayData["movedUsers"];
            $this->gImpossibleUsers = $arrayData["impossibleUsers"];
            $this->gCreatedUsers    = $arrayData["createdUsers"];

            $this->managersHierarchy = $arrayData["managersHierarchy"];
            $arrayUserUid            = $arrayData["arrayUserUid"];

            //(G) Update Users
            $arrayAux = array_diff(array_keys($ldapAdvanced->arrayGroupUsersByUid), $arrayUserUid);

            $this->groupRemoveUsers($arrayGroupData["GRP_UID"], $arrayAux);

            $this->gRemoved += count($arrayAux);
            $this->gRemovedUsers = "";

            $ldapAdvanced->debugLog("ldapadvanced.php > function groupSynchronizeUsers() > END");

            //Return all UID of Users synchronized in the Group (Return all UID of Users of this Group)
            return $arrayUserUid;
        } catch (Exception $e) {
            throw $e;
        }
    }
}

