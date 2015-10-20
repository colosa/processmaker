<?php
namespace ProcessMaker\BusinessModel;

class Group
{
    private $arrayFieldDefinition = array(
        "GRP_UID"    => array("type" => "string", "required" => false, "empty" => false, "defaultValues" => array(),                     "fieldNameAux" => "groupUid"),

        "GRP_TITLE"  => array("type" => "string", "required" => true,  "empty" => false, "defaultValues" => array(),                     "fieldNameAux" => "groupTitle"),
        "GRP_STATUS" => array("type" => "string", "required" => true,  "empty" => false, "defaultValues" => array("ACTIVE", "INACTIVE"), "fieldNameAux" => "groupStatus")
    );

    private $formatFieldNameInUppercase = true;

    private $arrayFieldNameForException = array(
        "filter" => "FILTER",
        "start"  => "START",
        "limit"  => "LIMIT"
    );

    /**
     * Constructor of the class
     *
     * return void
     */
    public function __construct()
    {
        try {
            foreach ($this->arrayFieldDefinition as $key => $value) {
                $this->arrayFieldNameForException[$value["fieldNameAux"]] = $key;
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Set the format of the fields name (uppercase, lowercase)
     *
     * @param bool $flag Value that set the format
     *
     * return void
     */
    public function setFormatFieldNameInUppercase($flag)
    {
        try {
            $this->formatFieldNameInUppercase = $flag;

            $this->setArrayFieldNameForException($this->arrayFieldNameForException);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Set exception messages for fields
     *
     * @param array $arrayData Data with the fields
     *
     * return void
     */
    public function setArrayFieldNameForException($arrayData)
    {
        try {
            foreach ($arrayData as $key => $value) {
                $this->arrayFieldNameForException[$key] = $this->getFieldNameByFormatFieldName($value);
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get the name of the field according to the format
     *
     * @param string $fieldName Field name
     *
     * return string Return the field name according the format
     */
    public function getFieldNameByFormatFieldName($fieldName)
    {
        try {
            return ($this->formatFieldNameInUppercase)? strtoupper($fieldName) : strtolower($fieldName);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Verify if exists the title of a Group
     *
     * @param string $groupTitle      Title
     * @param string $groupUidExclude Unique id of Group to exclude
     *
     * return bool Return true if exists the title of a Group, false otherwise
     */
    public function existsTitle($groupTitle, $groupUidExclude = "")
    {
        try {
            $delimiter = \DBAdapter::getStringDelimiter();

            $criteria = new \Criteria("workflow");

            $criteria->addSelectColumn(\GroupwfPeer::GRP_UID);

            $criteria->addAlias("CT", \ContentPeer::TABLE_NAME);

            $arrayCondition = array();
            $arrayCondition[] = array(\GroupwfPeer::GRP_UID, "CT.CON_ID", \Criteria::EQUAL);
            $arrayCondition[] = array("CT.CON_CATEGORY", $delimiter . "GRP_TITLE" . $delimiter, \Criteria::EQUAL);
            $arrayCondition[] = array("CT.CON_LANG", $delimiter . SYS_LANG . $delimiter, \Criteria::EQUAL);
            $criteria->addJoinMC($arrayCondition, \Criteria::LEFT_JOIN);

            if ($groupUidExclude != "") {
                $criteria->add(\GroupwfPeer::GRP_UID, $groupUidExclude, \Criteria::NOT_EQUAL);
            }

            $criteria->add("CT.CON_VALUE", $groupTitle, \Criteria::EQUAL);

            $rsCriteria = \GroupwfPeer::doSelectRS($criteria);

            if ($rsCriteria->next()) {
                return true;
            } else {
                return false;
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Verify if doesn't exists the Group in table GROUP
     *
     * @param string $groupUid              Unique id of Group
     * @param string $fieldNameForException Field name for the exception
     *
     * return void Throw exception if doesn't exists the Group in table GROUP
     */
    public function throwExceptionIfNotExistsGroup($groupUid, $fieldNameForException)
    {
        try {
            $group = new \Groupwf();

            if (!$group->GroupwfExists($groupUid)) {
                throw new \Exception(\G::LoadTranslation("ID_GROUP_DOES_NOT_EXIST", array($fieldNameForException, $groupUid)));
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Verify if exists the title of a Group
     *
     * @param string $groupTitle            Title
     * @param string $fieldNameForException Field name for the exception
     * @param string $groupUidExclude       Unique id of Group to exclude
     *
     * return void Throw exception if exists the title of a Group
     */
    public function throwExceptionIfExistsTitle($groupTitle, $fieldNameForException, $groupUidExclude = "")
    {
        try {
            if ($this->existsTitle($groupTitle, $groupUidExclude)) {
                throw new \Exception(\G::LoadTranslation("ID_GROUP_TITLE_ALREADY_EXISTS", array($fieldNameForException, $groupTitle)));
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Create Group
     *
     * @param array $arrayData Data
     *
     * return array Return data of the new Group created
     */
    public function create($arrayData)
    {
        try {
            $arrayData = array_change_key_case($arrayData, CASE_UPPER);

            unset($arrayData["GRP_UID"]);

            //Verify data
            $process = new \ProcessMaker\BusinessModel\Process();

            $process->throwExceptionIfDataNotMetFieldDefinition($arrayData, $this->arrayFieldDefinition, $this->arrayFieldNameForException, true);

            $this->throwExceptionIfExistsTitle($arrayData["GRP_TITLE"], $this->arrayFieldNameForException["groupTitle"]);

            //Create
            $group = new \Groupwf();

            $groupUid = $group->create($arrayData);

            //Return
            $arrayData = array_merge(array("GRP_UID" => $groupUid), $arrayData);

            if (!$this->formatFieldNameInUppercase) {
                $arrayData = array_change_key_case($arrayData, CASE_LOWER);
            }

            return $arrayData;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Update Group
     *
     * @param string $groupUid  Unique id of Group
     * @param array  $arrayData Data
     *
     * return array Return data of the Group updated
     */
    public function update($groupUid, $arrayData)
    {
        try {
            $arrayData = array_change_key_case($arrayData, CASE_UPPER);

            //Verify data
            $process = new \ProcessMaker\BusinessModel\Process();

            $this->throwExceptionIfNotExistsGroup($groupUid, $this->arrayFieldNameForException["groupUid"]);

            $process->throwExceptionIfDataNotMetFieldDefinition($arrayData, $this->arrayFieldDefinition, $this->arrayFieldNameForException, false);

            if (isset($arrayData["GRP_TITLE"])) {
                $this->throwExceptionIfExistsTitle($arrayData["GRP_TITLE"], $this->arrayFieldNameForException["groupTitle"], $groupUid);
            }

            //Update
            $group = new \Groupwf();

            $arrayData["GRP_UID"] = $groupUid;

            $result = $group->update($arrayData);

            //Return
            unset($arrayData["GRP_UID"]);

            if (!$this->formatFieldNameInUppercase) {
                $arrayData = array_change_key_case($arrayData, CASE_LOWER);
            }

            return $arrayData;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Delete Group
     *
     * @param string $groupUid Unique id of Group
     *
     * return void
     */
    public function delete($groupUid)
    {
        try {
            //Verify data
            $this->throwExceptionIfNotExistsGroup($groupUid, $this->arrayFieldNameForException["groupUid"]);

            $arrayTotalTasksByGroup = $this->getTotalTasksByGroup($groupUid);

            if (isset($arrayTotalTasksByGroup[$groupUid]) && $arrayTotalTasksByGroup[$groupUid] > 0) {
                throw new \Exception(\G::LoadTranslation("ID_GROUP_CANNOT_DELETE_WHILE_ASSIGNED_TO_TASK"));
            }

            //Delete
            $group = new \Groupwf();

            $result = $group->remove($groupUid);

            //Delete assignments of tasks
            $criteria = new \Criteria("workflow");

            $criteria->add(\TaskUserPeer::USR_UID, $groupUid);

            \TaskUserPeer::doDelete($criteria);

            //Delete permissions
            $criteria = new \Criteria("workflow");

            $criteria->add(\ObjectPermissionPeer::USR_UID, $groupUid);

            \ObjectPermissionPeer::doDelete($criteria);

            //Delete assignments of supervisors
            $criteria = new \Criteria("workflow");

            $criteria->add(\ProcessUserPeer::USR_UID, $groupUid);
            $criteria->add(\ProcessUserPeer::PU_TYPE, "GROUP_SUPERVISOR");

            \ProcessUserPeer::doDelete($criteria);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get criteria for Group
     *
     * return object
     */
    public function getGroupCriteria()
    {
        try {
            $criteria = new \Criteria("workflow");

            $criteria->addSelectColumn(\GroupwfPeer::GRP_UID);
            $criteria->addSelectColumn(\GroupwfPeer::GRP_STATUS);
            $criteria->addSelectColumn(\GroupwfPeer::GRP_LDAP_DN);
            $criteria->addSelectColumn(\GroupwfPeer::GRP_UX);
            $criteria->addAsColumn("GRP_TITLE", \ContentPeer::CON_VALUE);
            $criteria->addJoin(\GroupwfPeer::GRP_UID, \ContentPeer::CON_ID, \Criteria::LEFT_JOIN);
            $criteria->add(\ContentPeer::CON_CATEGORY, "GRP_TITLE", \Criteria::EQUAL);
            $criteria->add(\ContentPeer::CON_LANG, SYS_LANG, \Criteria::EQUAL);

            return $criteria;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get data of total Users by Group
     *
     * @param string $groupUid Unique id of Group
     *
     * return array Return an array with data of total Users by Group
     */
    public function getTotalUsersByGroup($groupUid = "")
    {
        try {
            $arrayData = array();

            //Verif data
            if ($groupUid != "") {
                $this->throwExceptionIfNotExistsGroup($groupUid, $this->arrayFieldNameForException["groupUid"]);
            }

            //Get data
            $criteria = new \Criteria("workflow");

            $criteria->addSelectColumn(\GroupUserPeer::GRP_UID);
            $criteria->addAsColumn("NUM_REC", "COUNT(" . \GroupUserPeer::GRP_UID . ")");
            $criteria->addJoin(\GroupUserPeer::USR_UID, \UsersPeer::USR_UID, \Criteria::INNER_JOIN);

            if ($groupUid != "") {
                $criteria->add(\GroupUserPeer::GRP_UID, $groupUid, \Criteria::EQUAL);
            }

            $criteria->add(\UsersPeer::USR_STATUS, "CLOSED", \Criteria::NOT_EQUAL);
            $criteria->addGroupByColumn(\GroupUserPeer::GRP_UID);

            $rsCriteria = \GroupUserPeer::doSelectRS($criteria);
            $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

            while ($rsCriteria->next()) {
                $row = $rsCriteria->getRow();

                $arrayData[$row["GRP_UID"]] = (int)($row["NUM_REC"]);
            }

            //Return
            return $arrayData;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get data of total Tasks by Group
     *
     * @param string $groupUid Unique id of Group
     *
     * return array Return an array with data of total Tasks by Group
     */
    public function getTotalTasksByGroup($groupUid = "")
    {
        try {
            $arrayData = array();

            //Verif data
            if ($groupUid != "") {
                $this->throwExceptionIfNotExistsGroup($groupUid, $this->arrayFieldNameForException["groupUid"]);
            }

            //Get data
            $criteria = new \Criteria("workflow");

            $criteria->addAsColumn("GRP_UID", \TaskUserPeer::USR_UID);
            $criteria->addAsColumn("NUM_REC", "COUNT(" . \TaskUserPeer::USR_UID . ")");

            if ($groupUid != "") {
                $criteria->add(\TaskUserPeer::USR_UID, $groupUid, \Criteria::EQUAL);
            }

            $criteria->add(\TaskUserPeer::TU_TYPE, 1, \Criteria::EQUAL);
            $criteria->add(\TaskUserPeer::TU_RELATION, 2, \Criteria::EQUAL);
            $criteria->addGroupByColumn(\TaskUserPeer::USR_UID);

            $rsCriteria = \TaskUserPeer::doSelectRS($criteria);
            $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC );

            while ($rsCriteria->next()) {
                $row = $rsCriteria->getRow();

                $arrayData[$row["GRP_UID"]] = (int)($row["NUM_REC"]);
            }

            //Return
            return $arrayData;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get data of a Group from a record
     *
     * @param array $record Record
     *
     * return array Return an array with data Group
     */
    public function getGroupDataFromRecord($record)
    {
        try {
            return array(
                $this->getFieldNameByFormatFieldName("GRP_UID")    => $record["GRP_UID"],
                $this->getFieldNameByFormatFieldName("GRP_TITLE")  => $record["GRP_TITLE"],
                $this->getFieldNameByFormatFieldName("GRP_STATUS") => $record["GRP_STATUS"],
                $this->getFieldNameByFormatFieldName("GRP_USERS")  => $record["GRP_USERS"],
                $this->getFieldNameByFormatFieldName("GRP_TASKS")  => $record["GRP_TASKS"]
            );
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get all Groups
     *
     * @param array  $arrayFilterData Data of the filters
     * @param string $sortField       Field name to sort
     * @param string $sortDir         Direction of sorting (ASC, DESC)
     * @param int    $start           Start
     * @param int    $limit           Limit
     *
     * return array Return an array with all Groups
     */
    public function getGroups($arrayFilterData = null, $sortField = null, $sortDir = null, $start = null, $limit = null)
    {
        try {
            $arrayGroup = array();

            $numRecTotal = 0;

            //Verify data
            $process = new \ProcessMaker\BusinessModel\Process();

            $process->throwExceptionIfDataNotMetPagerVarDefinition(array("start" => $start, "limit" => $limit), $this->arrayFieldNameForException);

            //Set variables
            $filterName = "filter";

            if (!is_null($arrayFilterData) && is_array($arrayFilterData) && isset($arrayFilterData["filter"])) {
                $arrayAux = array(
                    ""      => "filter",
                    "LEFT"  => "lfilter",
                    "RIGHT" => "rfilter"
                );

                $filterName = $arrayAux[(isset($arrayFilterData["filterOption"]))? $arrayFilterData["filterOption"] : ""];
            }

            //Get data
            if (!is_null($limit) && $limit . "" == "0") {
                //Return
                return array(
                    "total"     => $numRecTotal,
                    "start"     => (int)((!is_null($start))? $start : 0),
                    "limit"     => (int)((!is_null($limit))? $limit : 0),
                    $filterName => (!is_null($arrayFilterData) && is_array($arrayFilterData) && isset($arrayFilterData["filter"]))? $arrayFilterData["filter"] : "",
                    "data"      => $arrayGroup
                );
            }

            //Set variables
            $arrayTotalUsersByGroup = $this->getTotalUsersByGroup();
            $arrayTotalTasksByGroup = $this->getTotalTasksByGroup();

            //Query
            $criteria = $this->getGroupCriteria();

            if (!is_null($arrayFilterData) && is_array($arrayFilterData) && isset($arrayFilterData["filter"]) && trim($arrayFilterData["filter"]) != "") {
                $arraySearch = array(
                    ""      => "%" . $arrayFilterData["filter"] . "%",
                    "LEFT"  => $arrayFilterData["filter"] . "%",
                    "RIGHT" => "%" . $arrayFilterData["filter"]
                );

                $search = $arraySearch[(isset($arrayFilterData["filterOption"]))? $arrayFilterData["filterOption"] : ""];

                $criteria->add(\ContentPeer::CON_VALUE, $search, \Criteria::LIKE);
            }

            //Number records total
            $criteriaCount = clone $criteria;

            $criteriaCount->clearSelectColumns();
            $criteriaCount->addSelectColumn("COUNT(" . \GroupwfPeer::GRP_UID . ") AS NUM_REC");

            $rsCriteriaCount = \GroupwfPeer::doSelectRS($criteriaCount);
            $rsCriteriaCount->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

            $result = $rsCriteriaCount->next();
            $row = $rsCriteriaCount->getRow();

            $numRecTotal = (int)($row["NUM_REC"]);

            //Query
            if (!is_null($sortField) && trim($sortField) != "") {
                $sortField = strtoupper($sortField);

                if (in_array(\GroupwfPeer::TABLE_NAME . "." . $sortField, $criteria->getSelectColumns())) {
                    $sortField = \GroupwfPeer::TABLE_NAME . "." . $sortField;
                } else {
                    $sortField = "GRP_TITLE";
                }
            } else {
                $sortField = "GRP_TITLE";
            }

            if (!is_null($sortDir) && trim($sortDir) != "" && strtoupper($sortDir) == "DESC") {
                $criteria->addDescendingOrderByColumn($sortField);
            } else {
                $criteria->addAscendingOrderByColumn($sortField);
            }

            if (!is_null($start)) {
                $criteria->setOffset((int)($start));
            }

            if (!is_null($limit)) {
                $criteria->setLimit((int)($limit));
            }

            $rsCriteria = \GroupwfPeer::doSelectRS($criteria);
            $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

            while ($rsCriteria->next()) {
                $row = $rsCriteria->getRow();

                $row["GRP_USERS"] = (isset($arrayTotalUsersByGroup[$row["GRP_UID"]]))? $arrayTotalUsersByGroup[$row["GRP_UID"]] : 0;
                $row["GRP_TASKS"] = (isset($arrayTotalTasksByGroup[$row["GRP_UID"]]))? $arrayTotalTasksByGroup[$row["GRP_UID"]] : 0;

                $arrayGroup[] = $this->getGroupDataFromRecord($row);
            }

            //Return
            return array(
                "total"     => $numRecTotal,
                "start"     => (int)((!is_null($start))? $start : 0),
                "limit"     => (int)((!is_null($limit))? $limit : 0),
                $filterName => (!is_null($arrayFilterData) && is_array($arrayFilterData) && isset($arrayFilterData["filter"]))? $arrayFilterData["filter"] : "",
                "data"      => $arrayGroup
            );
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get data of a Group
     *
     * @param string $groupUid Unique id of Group
     *
     * return array Return an array with data of a Group
     */
    public function getGroup($groupUid)
    {
        try {
            //Verify data
            $this->throwExceptionIfNotExistsGroup($groupUid, $this->arrayFieldNameForException["groupUid"]);

            //Get data
            $arrayTotalUsersByGroup = $this->getTotalUsersByGroup($groupUid);
            $arrayTotalTasksByGroup = $this->getTotalTasksByGroup($groupUid);

            //SQL
            $criteria = $this->getGroupCriteria();

            $criteria->add(\GroupwfPeer::GRP_UID, $groupUid, \Criteria::EQUAL);

            $rsCriteria = \GroupwfPeer::doSelectRS($criteria);
            $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

            $rsCriteria->next();

            $row = $rsCriteria->getRow();

            $row["GRP_USERS"] = (isset($arrayTotalUsersByGroup[$groupUid]))? $arrayTotalUsersByGroup[$groupUid] : 0;
            $row["GRP_TASKS"] = (isset($arrayTotalTasksByGroup[$groupUid]))? $arrayTotalTasksByGroup[$groupUid] : 0;

            //Return
            return $this->getGroupDataFromRecord($row);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get criteria for User
     *
     * @param string $groupUid            Unique id of Group
     * @param array  $arrayFilterData     Data of the filters
     * @param array  $arrayUserUidExclude Unique id of Users to exclude
     *
     * return object
     */
    public function getUserCriteria($groupUid, $arrayFilterData = null, $arrayUserUidExclude = null)
    {
        try {
            $criteria = new \Criteria("workflow");

            $criteria->addSelectColumn(\UsersPeer::USR_UID);
            $criteria->addSelectColumn(\UsersPeer::USR_USERNAME);
            $criteria->addSelectColumn(\UsersPeer::USR_FIRSTNAME);
            $criteria->addSelectColumn(\UsersPeer::USR_LASTNAME);
            $criteria->addSelectColumn(\UsersPeer::USR_EMAIL);
            $criteria->addSelectColumn(\UsersPeer::USR_STATUS);

            if ($groupUid != "") {
                $criteria->addJoin(\GroupUserPeer::USR_UID, \UsersPeer::USR_UID, \Criteria::LEFT_JOIN);
                $criteria->add(\GroupUserPeer::GRP_UID, $groupUid, \Criteria::EQUAL);
            }

            $criteria->add(\UsersPeer::USR_STATUS, "CLOSED", \Criteria::NOT_EQUAL);

            if (!is_null($arrayUserUidExclude) && is_array($arrayUserUidExclude)) {
                $criteria->add(\UsersPeer::USR_UID, $arrayUserUidExclude, \Criteria::NOT_IN);
            }

            if (!is_null($arrayFilterData) && is_array($arrayFilterData) && isset($arrayFilterData["filter"]) && trim($arrayFilterData["filter"]) != "") {
                $criteria->add(
                    $criteria->getNewCriterion(\UsersPeer::USR_USERNAME, "%" . $arrayFilterData["filter"] . "%", \Criteria::LIKE)->addOr(
                    $criteria->getNewCriterion(\UsersPeer::USR_FIRSTNAME, "%" . $arrayFilterData["filter"] . "%", \Criteria::LIKE)->addOr(
                    $criteria->getNewCriterion(\UsersPeer::USR_LASTNAME, "%" . $arrayFilterData["filter"] . "%", \Criteria::LIKE)))
                );
            }

            return $criteria;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get data of a User from a record
     *
     * @param array $record Record
     *
     * return array Return an array with data User
     */
    public function getUserDataFromRecord($record)
    {
        try {
            return array(
                $this->getFieldNameByFormatFieldName("USR_UID")       => $record["USR_UID"],
                $this->getFieldNameByFormatFieldName("USR_USERNAME")  => $record["USR_USERNAME"],
                $this->getFieldNameByFormatFieldName("USR_FIRSTNAME") => $record["USR_FIRSTNAME"] . "",
                $this->getFieldNameByFormatFieldName("USR_LASTNAME")  => $record["USR_LASTNAME"] . "",
                $this->getFieldNameByFormatFieldName("USR_EMAIL")     => $record["USR_EMAIL"] . "",
                $this->getFieldNameByFormatFieldName("USR_STATUS")    => $record["USR_STATUS"]
            );
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * return permissions of user
     */
    public function loadUserRolePermission ($sSystem, $sUser)
    {
        require_once (PATH_RBAC_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "UsersRoles.php");
        require_once (PATH_RBAC_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "Systems.php");
        require_once (PATH_RBAC_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "RbacUsers.php");
        require_once (PATH_RBAC_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "RolesPeer.php");
        $this->sSystem = $sSystem;
        $this->usersRolesObj = new \UsersRoles();
        $this->systemObj = new \Systems();
        $fieldsSystem = $this->systemObj->loadByCode( $sSystem );
        $fieldsRoles = $this->usersRolesObj->getRolesBySystem( $fieldsSystem['SYS_UID'], $sUser );
        $fieldsPermissions = $this->usersRolesObj->getAllPermissions( $fieldsRoles['ROL_UID'], $sUser );
        $this->userObj = new \RbacUsers();
        $this->aUserInfo['USER_INFO'] = $this->userObj->load( $sUser );
        $this->aUserInfo[$sSystem]['SYS_UID'] = $fieldsSystem['SYS_UID'];
        $this->aUserInfo[$sSystem]['ROLE'] = $fieldsRoles;
        $this->aUserInfo[$sSystem]['PERMISSIONS'] = $fieldsPermissions;
        return $fieldsPermissions;
    }

    /**
     * Get all Users of a Group
     *
     * @param string $option          Option (USERS, AVAILABLE-USERS)
     * @param string $groupUid        Unique id of Group
     * @param array  $arrayFilterData Data of the filters
     * @param string $sortField       Field name to sort
     * @param string $sortDir         Direction of sorting (ASC, DESC)
     * @param int    $start           Start
     * @param int    $limit           Limit
     *
     * return array Return an array with all Users of a Group
     */
    public function getUsers($option, $groupUid, $arrayFilterData = null, $sortField = null, $sortDir = null, $start = null, $limit = null)
    {
        try {
            $arrayUser = array();

            //Verify data
            $process = new \ProcessMaker\BusinessModel\Process();

            $this->throwExceptionIfNotExistsGroup($groupUid, $this->arrayFieldNameForException["groupUid"]);

            $process->throwExceptionIfDataNotMetPagerVarDefinition(array("start" => $start, "limit" => $limit), $this->arrayFieldNameForException);

            //Get data
            if (!is_null($limit) && $limit . "" == "0") {
                return $arrayUser;
            }

            //SQL
            switch ($option) {
                case "SUPERVISOR":
                    $flagPermission = true;
                    //Criteria for Supervisor
                    $criteria = $this->getUserCriteria($groupUid, $arrayFilterData);
                    break;
                case "USERS":
                    //Criteria
                    $criteria = $this->getUserCriteria($groupUid, $arrayFilterData);
                    break;
                case "AVAILABLE-USERS":
                    //Get Uids
                    $arrayUid = array();

                    $criteria = $this->getUserCriteria($groupUid);

                    $rsCriteria = \UsersPeer::doSelectRS($criteria);
                    $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

                    while ($rsCriteria->next()) {
                        $row = $rsCriteria->getRow();

                        $arrayUid[] = $row["USR_UID"];
                    }

                    //Criteria
                    $criteria = $this->getUserCriteria("", $arrayFilterData, $arrayUid);
                    break;
            }

            //SQL
            if (!is_null($sortField) && trim($sortField) != "") {
                $sortField = strtoupper($sortField);

                if (in_array($sortField, array("USR_UID", "USR_USERNAME", "USR_FIRSTNAME", "USR_LASTNAME", "USR_EMAIL", "USR_STATUS"))) {
                    $sortField = \UsersPeer::TABLE_NAME . "." . $sortField;
                } else {
                    $sortField = \UsersPeer::USR_USERNAME;
                }
            } else {
                $sortField = \UsersPeer::USR_USERNAME;
            }

            if (!is_null($sortDir) && trim($sortDir) != "" && strtoupper($sortDir) == "DESC") {
                $criteria->addDescendingOrderByColumn($sortField);
            } else {
                $criteria->addAscendingOrderByColumn($sortField);
            }

            if (!is_null($start)) {
                $criteria->setOffset((int)($start));
            }

            if (!is_null($limit)) {
                $criteria->setLimit((int)($limit));
            }

            $rsCriteria = \UsersPeer::doSelectRS($criteria);
            $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

            if (isset($flagPermission) && $flagPermission) {
                \G::LoadSystem('rbac');
                while ($rsCriteria->next()) {
                    $row = $rsCriteria->getRow();

                    $aPermissions =   $this->loadUserRolePermission("PROCESSMAKER", $row['USR_UID']);
                    $bInclude = false;

                    foreach ($aPermissions as $aPermission) {
                        if ($aPermission['PER_CODE'] == 'PM_SUPERVISOR') {
                            $bInclude = true;
                        }
                    }
                    if ($bInclude) {
                        $arrayUser[] = $this->getUserDataFromRecord($row);
                    }
                }
            } else {
                while ($rsCriteria->next()) {
                    $row = $rsCriteria->getRow();

                    $arrayUser[] = $this->getUserDataFromRecord($row);
                }
            }
            //Return
            return $arrayUser;
        } catch (\Exception $e) {
            throw $e;
        }
    }
}

