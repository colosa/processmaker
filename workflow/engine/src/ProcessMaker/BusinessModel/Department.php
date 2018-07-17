<?php
namespace ProcessMaker\BusinessModel;

use BasePeer;
use Configurations;
use Criteria;
use Department as DepartmentModel;
use DepartmentPeer;
use Exception;
use ProcessMaker\BusinessModel\Validator;
use Propel;
use RBAC;
use ResultSet;
use Users;
use UsersPeer;
use G;

class Department
{
    /**
     * Verify if exists the title of a Department
     *
     * @param string $departmentTitle      Title
     * @param string $departmentUidExclude Unique id of Department to exclude
     * @return bool Return true if exists the title of a Department, false otherwise
     */
    public function existsTitle($departmentTitle, $departmentUidExclude = "")
    {
        try {
            $criteria = new Criteria("workflow");

            $criteria->addSelectColumn(DepartmentPeer::DEP_UID);
            $criteria->addSelectColumn(DepartmentPeer::DEP_TITLE);

            if ($departmentUidExclude != "") {
                $criteria->add(DepartmentPeer::DEP_UID, $departmentUidExclude, Criteria::NOT_EQUAL);
            }

            $criteria->add(DepartmentPeer::DEP_TITLE, $departmentTitle, Criteria::EQUAL);

            $rsCriteria = DepartmentPeer::doSelectRS($criteria);

            return ($rsCriteria->next())? true : false;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Verify if the User is not in a Department
     *
     * @param string $departmentUid
     * @param string $userUid
     * @return void Throw exception user not exists
     */
    private function throwExceptionUserNotExistsInDepartment($departmentUid, $userUid)
    {
        try {
            $user = UsersPeer::retrieveByPK($userUid);

            if (is_null($user) || $user->getDepUid() != $departmentUid) {
                throw new Exception(G::LoadTranslation('ID_USER_NOT_EXIST_DEPARTMENT', [$userUid]));
            }
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Verify if exists the title of a Department
     *
     * @param string $departmentTitle       Title
     * @param string $fieldNameForException Field name for the exception
     * @param string $departmentUidExclude  Unique id of Department to exclude
     * @return void Throw exception if exists the title of a Department
     */
    public function throwExceptionIfExistsTitle($departmentTitle, $fieldNameForException, $departmentUidExclude = "")
    {
        try {
            if ($this->existsTitle($departmentTitle, $departmentUidExclude)) {
                throw new Exception(G::LoadTranslation("ID_DEPARTMENT_TITLE_ALREADY_EXISTS", array($fieldNameForException, $departmentTitle)));
            }
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Get Department record
     *
     * @param string $departmentUid                 Unique id of Department
     * @param array  $arrayVariableNameForException Variable name for exception
     * @param bool   $throwException Flag to throw the exception if the main parameters are invalid or do not exist
     *                               (TRUE: throw the exception; FALSE: returns FALSE)
     * @return array Returns an array with Department record, ThrowTheException/FALSE otherwise
     */
    public function getDepartmentRecordByPk(
        $departmentUid,
        array $arrayVariableNameForException,
        $throwException = true
    ) {
        try {
            $obj = DepartmentPeer::retrieveByPK($departmentUid);

            if (is_null($obj)) {
                if ($throwException) {
                    throw new Exception(G::LoadTranslation(
                        'ID_DEPARTMENT_NOT_EXIST', [$arrayVariableNameForException['$departmentUid'], $departmentUid]
                    ));
                } else {
                    return false;
                }
            }

            //Return
            return $obj->toArray(BasePeer::TYPE_FIELDNAME);
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Get list for Departments
     *
     * @access public
     * @return array
     */
    public function getDepartments()
    {
        $oDepartment = new DepartmentModel();
        $aDepts = $oDepartment->getDepartments('');
        foreach ($aDepts as &$depData) {
            $depData['DEP_CHILDREN'] = $this->getChildren($depData);
            $depData = array_change_key_case($depData, CASE_LOWER);
        }
        return $aDepts;
    }

    /**
     * Assign User to Department
     *
     * @param string $departmentUid Unique id of Department
     * @param array  $arrayData     Data
     * return array Return data of the User assigned to Department
     */
    public function assignUser($departmentUid, array $arrayData)
    {
        try {
            //Verify data
            $process = new Process();
            $validator = new Validator();

            $validator->throwExceptionIfDataIsNotArray($arrayData, "\$arrayData");
            $validator->throwExceptionIfDataIsEmpty($arrayData, "\$arrayData");

            //Set data
            $arrayData = array_change_key_case($arrayData, CASE_UPPER);

            unset($arrayData["DEP_UID"]);

            //Set variables
            $arrayUserFieldDefinition = array(
                "DEP_UID" => array("type" => "string", "required" => false, "empty" => false, "defaultValues" => array(), "fieldNameAux" => "departmentUid"),
                "USR_UID" => array("type" => "string", "required" => true,  "empty" => false, "defaultValues" => array(), "fieldNameAux" => "userUid")
            );

            $arrayUserFieldNameForException = array(
                "departmentUid" => strtolower("DEP_UID"),
                "userUid"       => strtolower("USR_UID")
            );

            //Verify data
            $departmentUid = Validator::depUid($departmentUid);

            $process->throwExceptionIfDataNotMetFieldDefinition($arrayData, $arrayUserFieldDefinition, $arrayUserFieldNameForException, true);

            $process->throwExceptionIfNotExistsUser($arrayData["USR_UID"], $arrayUserFieldNameForException["userUid"]);

            //Assign User
            $department = new DepartmentModel();

            $department->load($departmentUid);

            $department->addUserToDepartment($departmentUid, $arrayData["USR_UID"], ($department->getDepManager() == "")? true : false, false);
            $department->updateDepartmentManager($departmentUid);

            //Return
            $arrayData = array_merge(array("DEP_UID" => $departmentUid), $arrayData);

            $arrayData = array_change_key_case($arrayData, CASE_LOWER);

            return $arrayData;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Post Unassign User
     *
     * @access public
     * @return void
     */
    public function unassignUser($dep_uid, $usr_uid)
    {
        $dep_uid = Validator::depUid($dep_uid);
        $usr_uid = Validator::usrUid($usr_uid);

        $this->throwExceptionUserNotExistsInDepartment($dep_uid, $usr_uid);

        $dep = new DepartmentModel();
        $dep->load( $dep_uid );
        $manager = $dep->getDepManager();
        $dep->removeUserFromDepartment( $dep_uid, $usr_uid );
        if ($usr_uid == $manager) {
            $editDepto['DEP_UID'] = $dep_uid;
            $editDepto['DEP_MANAGER'] = '';
            $dep->update( $editDepto );
            $dep->updateDepartmentManager($dep_uid);
        }
    }

    /**
     * Get custom record
     *
     * @param array $record Record
     * @return array Return an array with custom record
     */
    private function __getUserCustomRecordFromRecord(array $record)
    {
        try {
            $recordc = [
                'usr_uid'       => $record['USR_UID'],
                'usr_username'  => $record['USR_USERNAME'],
                'usr_firstname' => $record['USR_FIRSTNAME'],
                'usr_lastname'  => $record['USR_LASTNAME'],
                'usr_status'    => $record['USR_STATUS']
            ];

            if (isset($record['USR_SUPERVISOR'])) {
                $recordc['usr_supervisor'] = $record['USR_SUPERVISOR'];
            }

            return $recordc;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Get all Users of a Department (Assigned/Available)
     *
     * @param string $departmentUid   Unique id of Department
     * @param string $option          Option (ASSIGNED, AVAILABLE)
     * @param array  $arrayFilterData Data of the filters
     * @param string $sortField       Field name to sort
     * @param string $sortDir         Direction of sorting (ASC, DESC)
     * @param int    $start           Start
     * @param int    $limit           Limit
     * @param bool   $flagRecord      Flag that set the "getting" of record
     * @param bool   $throwException  Flag to throw the exception (This only if the parameters are invalid)
     *                                (TRUE: throw the exception; FALSE: returns FALSE)
     * @return array Return an array with all Users of a Department, ThrowTheException/FALSE otherwise
     */
    public function getUsers(
        $departmentUid,
        $option,
        array $arrayFilterData = null,
        $sortField = null,
        $sortDir = null,
        $start = null,
        $limit = null,
        $flagRecord = true,
        $throwException = true
    ) {
        try {
            $arrayUser = array();

            $numRecTotal = 0;

            //Verify data and Set variables
            $flagFilter = !is_null($arrayFilterData) && is_array($arrayFilterData) && isset($arrayFilterData['filter']);

            $result = Validator::validatePagerDataByPagerDefinition(
                ['$start' => $start, '$limit' => $limit],
                ['$start' => '$start', '$limit' => '$limit']
            );

            if ($result !== true) {
                if ($throwException) {
                    throw new Exception($result);
                } else {
                    return false;
                }
            }

            $arrayDepartmentData = $this->getDepartmentRecordByPk(
                $departmentUid, ['$departmentUid' => '$departmentUid'], $throwException
            );

            if ($arrayDepartmentData === false) {
                return false;
            }

            //Set variables
            $filterName = 'filter';

            if ($flagFilter) {
                $arrayAux = [
                    ''      => 'filter',
                    'LEFT'  => 'lfilter',
                    'RIGHT' => 'rfilter'
                ];

                $filterName = $arrayAux[
                    (isset($arrayFilterData['filterOption']))? $arrayFilterData['filterOption'] : ''
                ];
            }

            //Get data
            if (!is_null($limit) && (string)($limit) == '0') {
                return [
                    'total'     => $numRecTotal,
                    'start'     => (int)((!is_null($start))? $start : 0),
                    'limit'     => (int)((!is_null($limit))? $limit : 0),
                    $filterName => ($flagFilter)? $arrayFilterData['filter'] : '',
                    'data'      => $arrayUser
                ];
            }

            //Query
            $criteria = new Criteria('workflow');

            $criteria->addSelectColumn(UsersPeer::USR_UID);
            $criteria->addSelectColumn(UsersPeer::USR_USERNAME);
            $criteria->addSelectColumn(UsersPeer::USR_FIRSTNAME);
            $criteria->addSelectColumn(UsersPeer::USR_LASTNAME);
            $criteria->addSelectColumn(UsersPeer::USR_STATUS);

            $criteria->add(UsersPeer::USR_STATUS, 'CLOSED', Criteria::NOT_EQUAL);

            switch ($option) {
                case 'ASSIGNED':
                    $criteria->add(UsersPeer::DEP_UID, $departmentUid, Criteria::EQUAL);
                    break;
                case 'AVAILABLE':
                    $criteria->add(UsersPeer::DEP_UID, '', Criteria::EQUAL);
                    $criteria->add(UsersPeer::USR_UID, RBAC::GUEST_USER_UID, Criteria::NOT_EQUAL);
                    break;
            }

            if ($flagFilter && trim($arrayFilterData['filter']) != '') {
                $arraySearch = [
                    ''      => '%' . $arrayFilterData['filter'] . '%',
                    'LEFT'  => $arrayFilterData['filter'] . '%',
                    'RIGHT' => '%' . $arrayFilterData['filter']
                ];

                $search = $arraySearch[
                    (isset($arrayFilterData['filterOption']))? $arrayFilterData['filterOption'] : ''
                ];

                $criteria->add(
                    $criteria->getNewCriterion(UsersPeer::USR_USERNAME,  $search, Criteria::LIKE)->addOr(
                    $criteria->getNewCriterion(UsersPeer::USR_FIRSTNAME, $search, Criteria::LIKE)->addOr(
                    $criteria->getNewCriterion(UsersPeer::USR_LASTNAME,  $search, Criteria::LIKE)))
                );
            }

            //Number records total
            $numRecTotal = UsersPeer::doCount($criteria);

            //Query
            $conf = new Configurations();
            $sortFieldDefault = UsersPeer::TABLE_NAME . '.' . $conf->userNameFormatGetFirstFieldByUsersTable();

            if (!is_null($sortField) && trim($sortField) != '') {
                $sortField = strtoupper($sortField);

                if (in_array(UsersPeer::TABLE_NAME . '.' . $sortField, $criteria->getSelectColumns())) {
                    $sortField = UsersPeer::TABLE_NAME . '.' . $sortField;
                } else {
                    $sortField = $sortFieldDefault;
                }
            } else {
                $sortField = $sortFieldDefault;
            }

            if (!is_null($sortDir) && trim($sortDir) != '' && strtoupper($sortDir) == 'DESC') {
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

            $rsCriteria = UsersPeer::doSelectRS($criteria);
            $rsCriteria->setFetchmode(ResultSet::FETCHMODE_ASSOC);

            while ($rsCriteria->next()) {
                $record = $rsCriteria->getRow();

                switch ($option) {
                    case 'ASSIGNED':
                        $record['USR_SUPERVISOR'] = $record['USR_UID'] == $arrayDepartmentData['DEP_MANAGER'];
                        break;
                    case 'AVAILABLE':
                        break;
                }

                $arrayUser[] = ($flagRecord)? $record : $this->__getUserCustomRecordFromRecord($record);
            }

            //Return
            return [
                'total'     => $numRecTotal,
                'start'     => (int)((!is_null($start))? $start : 0),
                'limit'     => (int)((!is_null($limit))? $limit : 0),
                $filterName => ($flagFilter)? $arrayFilterData['filter'] : '',
                'data'      => $arrayUser
            ];
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Put Set Manager User
     *
     * @access public
     * @return void
     */
    public function setManagerUser($dep_uid, $usr_uid)
    {
        $dep_uid = Validator::depUid($dep_uid);
        $usr_uid = Validator::usrUid($usr_uid);

        $oCriteria = new Criteria( 'workflow' );
        $oCriteria->addSelectColumn( DepartmentPeer::DEP_UID );
        $oCriteria->add( DepartmentPeer::DEP_MANAGER, $usr_uid, Criteria::EQUAL );

        $oDataset = DepartmentPeer::doSelectRS( $oCriteria );
        $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
        if ($oDataset->next()) {
            throw (new Exception(G::LoadTranslation("ID_DEPARTMENT_MANAGER_EXIST", array('usr_uid',$usr_uid))));
        }

        $editDepartment['DEP_UID'] = $dep_uid;
        $editDepartment['DEP_MANAGER'] = $usr_uid;
        $oDept = new DepartmentModel();
        $oDept->update( $editDepartment );
        $oDept->updateDepartmentManager( $dep_uid );

        $oDept = new DepartmentModel();
        $oDept->Load($dep_uid);
        $oDept->addUserToDepartment($dep_uid, $usr_uid, ($oDept->getDepManager() == "")? true : false, false);
        $oDept->updateDepartmentManager($dep_uid);
    }

    /**
     * Get list for Departments
     *
     * @var string $dep_uid. Uid for Department
     * @access public
     * @return array
     */
    public function getDepartment($dep_uid)
    {
        $dep_uid = Validator::depUid($dep_uid);
        $criteria = new Criteria( 'workflow' );
        $criteria->add( DepartmentPeer::DEP_UID, $dep_uid, Criteria::EQUAL );
        $con = Propel::getConnection( DepartmentPeer::DATABASE_NAME );
        $objects = DepartmentPeer::doSelect( $criteria, $con );
        $oUsers = new Users();

        $node = array ();
        foreach ($objects as $oDepartment) {
            $node['DEP_UID'] = $oDepartment->getDepUid();
            $node['DEP_PARENT'] = $oDepartment->getDepParent();
            $node['DEP_TITLE'] = $oDepartment->getDepTitle();
            $node['DEP_STATUS'] = $oDepartment->getDepStatus();
            $node['DEP_MANAGER'] = $oDepartment->getDepManager();
            $node['DEP_LDAP_DN'] = $oDepartment->getDepLdapDn();
            $node['DEP_LAST'] = 0;

            $manager = $oDepartment->getDepManager();
            if ($manager != '') {
                $UserUID = $oUsers->load($manager);
                $node['DEP_MANAGER_USERNAME'] = isset( $UserUID['USR_USERNAME'] ) ? $UserUID['USR_USERNAME'] : '';
                $node['DEP_MANAGER_FIRSTNAME'] = isset( $UserUID['USR_FIRSTNAME'] ) ? $UserUID['USR_FIRSTNAME'] : '';
                $node['DEP_MANAGER_LASTNAME'] = isset( $UserUID['USR_LASTNAME'] ) ? $UserUID['USR_LASTNAME'] : '';
            } else {
                $node['DEP_MANAGER_USERNAME'] = '';
                $node['DEP_MANAGER_FIRSTNAME'] = '';
                $node['DEP_MANAGER_LASTNAME'] = '';
            }

            $criteria = new Criteria();
            $criteria->add(UsersPeer::DEP_UID, $dep_uid, Criteria::EQUAL );
            $node['DEP_MEMBERS'] = UsersPeer::doCount($criteria);

            $criteriaCount = new Criteria( 'workflow' );
            $criteriaCount->clearSelectColumns();
            $criteriaCount->addSelectColumn( 'COUNT(*)' );
            $criteriaCount->add( DepartmentPeer::DEP_PARENT, $oDepartment->getDepUid(), Criteria::EQUAL );
            $rs = DepartmentPeer::doSelectRS( $criteriaCount );
            $rs->next();
            $row = $rs->getRow();
            $node['HAS_CHILDREN'] = $row[0];
        }
        $node = array_change_key_case($node, CASE_LOWER);
        return $node;
    }

    /**
     * Save Department
     *
     * @var string $dep_data. Data for Process
     * @var string $create. Flag for create or update
     * @access public
     * @return array
     */
    public function saveDepartment($dep_data, $create = true)
    {
        Validator::isArray($dep_data, '$dep_data');
        Validator::isNotEmpty($dep_data, '$dep_data');
        Validator::isBoolean($create, '$create');

        $dep_data = array_change_key_case($dep_data, CASE_UPPER);

        if ($create) {
            unset($dep_data["DEP_UID"]);
        }

        $oDepartment = new DepartmentModel();
        if (isset($dep_data['DEP_UID']) && $dep_data['DEP_UID'] != '') {
            Validator::depUid($dep_data['DEP_UID']);
        }
        if (isset($dep_data['DEP_PARENT']) && $dep_data['DEP_PARENT'] != '') {
            Validator::depUid($dep_data['DEP_PARENT'], 'dep_parent');
        }
        if (isset($dep_data['DEP_MANAGER']) && $dep_data['DEP_MANAGER'] != '') {
            Validator::usrUid($dep_data['DEP_MANAGER'], 'dep_manager');
        }
        if (isset($dep_data['DEP_STATUS'])) {
            Validator::depStatus($dep_data['DEP_STATUS']);
        }

        if (!$create) {
            if (isset($dep_data["DEP_TITLE"])) {
                $this->throwExceptionIfExistsTitle($dep_data["DEP_TITLE"], strtolower("DEP_TITLE"), $dep_data["DEP_UID"]);

                $dep_data["DEPO_TITLE"] = $dep_data["DEP_TITLE"];
            }

            $oDepartment->update($dep_data);
            $oDepartment->updateDepartmentManager($dep_data['DEP_UID']);
        } else {
            if (isset($dep_data['DEP_TITLE'])) {
                $this->throwExceptionIfExistsTitle($dep_data["DEP_TITLE"], strtolower("DEP_TITLE"));
            } else {
                throw (new Exception(G::LoadTranslation("ID_FIELD_REQUIRED", array('dep_title'))));
            }

            $dep_uid = $oDepartment->create($dep_data);
            $response = $this->getDepartment($dep_uid);
            return $response;
        }
    }

    /**
     * Delete department
     * @var string $dep_uid. Uid for department
     *
     * @access public
     * @return array
     */
    public function deleteDepartment($dep_uid)
    {
        $dep_uid = Validator::depUid($dep_uid);
        $oDepartment = new DepartmentModel();
        $countUsers = $oDepartment->cantUsersInDepartment($dep_uid);
        if ($countUsers != 0) {
            throw (new Exception(G::LoadTranslation("ID_CANT_DELETE_DEPARTMENT_HAS_USERS")));
        }
        $dep_data = $this->getDepartment($dep_uid);
        if ($dep_data['has_children'] != 0) {
            throw (new Exception(G::LoadTranslation("ID_CANT_DELETE_DEPARTMENT_HAS_CHILDREN")));
        }
        $oDepartment->remove($dep_uid);
    }

    /**
     * Look for Children for department
     *
     * @var array $dataDep. Data for child department
     * @access public
     * @return array
     */
    protected function getChildren ($dataDep)
    {
        $children = array();
        if ((int)$dataDep['HAS_CHILDREN'] > 0) {
            $oDepartment = new DepartmentModel();
            $aDepts = $oDepartment->getDepartments($dataDep['DEP_UID']);
            foreach ($aDepts as &$depData) {
                $depData['DEP_CHILDREN'] = $this->getChildren($depData);
                $depData = array_change_key_case($depData, CASE_LOWER);
                $children[] = $depData;
            }
        }
        return $children;
    }
}
