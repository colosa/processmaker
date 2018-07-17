<?php

/**
 * RbacUsers.php
 * @package  rbac-classes-model
 *
 * ProcessMaker Open Source Edition
 * Copyright (C) 2004 - 2011 Colosa Inc.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * For more information, contact Colosa Inc, 2566 Le Jeune Rd.,
 * Coral Gables, FL, 33134, USA, or email info@colosa.com.
 *
 */

/**
 * @access public
 */

use ProcessMaker\Plugins\PluginRegistry;

/**
 * Skeleton subclass for representing a row from the 'USERS' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package  rbac-classes-model
 */
class RbacUsers extends BaseRbacUsers
{

    private $userUidReserved = [RBAC::GUEST_USER_UID];

    /**
     * Authentication of a user through the class RBAC_user
     *
     * verifies that a user has permission to start an application
     *
     * @access public
     * Function verifyLogin
     *
     * @param  string $userName UserId  (login) de usuario
     * @param  string $password Password
     * @return type
     *  -1: no user exists
     *  -2: wrong password
     *  -3: inactive user
     *  -4: expired user
     *  -6: role inactive
     *  n : string user uid
     * @throws Exception
     */
    public function verifyLogin($userName, $password)
    {
        //invalid user
        if ($userName == '') {
            return -1;
        }
        //invalid password
        if ($password == '') {
            return -2;
        }
        $con = Propel::getConnection(RbacUsersPeer::DATABASE_NAME);
        try {
            $c = new Criteria('rbac');
            $c->add(RbacUsersPeer::USR_USERNAME, $userName);
            /* @var $rs RbacUsers[] */
            $rs = RbacUsersPeer::doSelect($c, Propel::getDbConnection('rbac_ro'));
            if (is_array($rs) && isset($rs[0]) && is_object($rs[0]) && get_class($rs[0]) == 'RbacUsers') {
                $dataFields = $rs[0]->toArray(BasePeer::TYPE_FIELDNAME);
                //verify password with md5, and md5 format
                if (mb_strtoupper($userName, 'utf-8') === mb_strtoupper($dataFields['USR_USERNAME'], 'utf-8')) {
                    if (Bootstrap::verifyHashPassword($password, $rs[0]->getUsrPassword())) {
                        if ($dataFields['USR_DUE_DATE'] < date('Y-m-d')) {
                            return -4;
                        }
                        if ($dataFields['USR_STATUS'] != 1 && $dataFields['USR_UID'] !== RBAC::GUEST_USER_UID) {
                            return -3;
                        }
                        $role = $this->getUserRole($dataFields['USR_UID']);
                        if ($role['ROL_STATUS'] == 0) {
                            return -6;
                        }

                        return $dataFields['USR_UID'];
                    } else {
                        return -2;
                    }
                } else {
                    return -1;
                }
            } else {
                return -1;
            }
        } catch (Exception $error) {
            throw($error);
        }

        return -1;
    }

    /**
     * Verify if the userName exists
     * @param string $userName
     * @return integer
     * @throws Exception
    */
    public function verifyUser($userName)
    {
        //invalid user
        if ($userName == '') {
            return 0;
        }
        $con = Propel::getConnection(RbacUsersPeer::DATABASE_NAME);
        try {
            $c = new Criteria('rbac');
            $c->add(RbacUsersPeer::USR_USERNAME, $userName);
            $rs = RbacUsersPeer::doSelect($c, Propel::getDbConnection('rbac_ro'));
            if (is_array($rs) && isset($rs[0]) && is_object($rs[0]) && get_class($rs[0]) == 'RbacUsers') {
                //return the row for futher check of which Autentificacion method belongs this user
                $this->fields = $rs[0]->toArray(BasePeer::TYPE_FIELDNAME);

                return 1;
            } else {
                return 0;
            }
        } catch (Exception $error) {
            throw($error);
        }
    }

    /**
     * Get user info by userName
     * @param string $userName
     * @return array $dataFields if exist
     *         false if does not exist
     * @throws Exception
     */
    public function getByUsername($userName)
    {
        //invalid user
        if ($userName == '') {
            return 0;
        }
        $con = Propel::getConnection(RbacUsersPeer::DATABASE_NAME);
        try {
            $c = new Criteria('rbac');
            $c->add(RbacUsersPeer::USR_USERNAME, $userName);
            $rs = RbacUsersPeer::doSelect($c, Propel::getDbConnection('rbac_ro'));

            if (is_array($rs) && isset($rs[0]) && is_object($rs[0]) && get_class($rs[0]) == 'RbacUsers') {
                $dataFields = $rs[0]->toArray(BasePeer::TYPE_FIELDNAME);

                return $dataFields;
            } else {
                return false;
            }
        } catch (Exception $error) {
            throw($error);
        }
    }

    /**
     * Verify user by Uid
     * @param string $userUid
     * @return integer
     * @throws Exception
     */
    public function verifyUserId($userUid)
    {
        //invalid user
        if ($userUid == '') {
            return 0;
        }
        $con = Propel::getConnection(RbacUsersPeer::DATABASE_NAME);
        try {
            $c = new Criteria('rbac');
            $c->add(RbacUsersPeer::USR_UID, $userUid);
            $rs = RbacUsersPeer::doSelect($c, Propel::getDbConnection('rbac_ro'));
            if (is_array($rs) && isset($rs[0]) && is_object($rs[0]) && get_class($rs[0]) == 'RbacUsers') {
                return 1;
            } else {
                return 0;
            }
        } catch (Exception $error) {
            throw($error);
        }
    }

    /**
     * Load user information by Uid
     * @param string $userUid
     * @return array $dataFields
     * @throws Exception
     */
    public function load($userUid)
    {
        $con = Propel::getConnection(RbacUsersPeer::DATABASE_NAME);
        try {
            $c = new Criteria('rbac');
            $c->add(RbacUsersPeer::USR_UID, $userUid);
            $resultSet = RbacUsersPeer::doSelectRS($c, Propel::getDbConnection('rbac_ro'));
            if ($resultSet->next()) {
                $this->hydrate($resultSet);
                $dataFields = $this->toArray(BasePeer::TYPE_FIELDNAME);

                return $dataFields;
            }

            return false;
        } catch (Exception $error) {
            throw($error);
        }
    }

    /**
     * Create an user
     * @param string $infoData
     * @return array
     * @throws Exception
     */
    public function create($infoData)
    {
        if (class_exists('ProcessMaker\Plugins\PluginRegistry')) {
            $pluginRegistry = PluginRegistry::loadSingleton();
            if ($pluginRegistry->existsTrigger(PM_BEFORE_CREATE_USER)) {
                try {
                    $pluginRegistry->executeTriggers(PM_BEFORE_CREATE_USER, null);
                } catch (Exception $error) {
                    throw new Exception($error->getMessage());
                }
            }
        }
        $connection = Propel::getConnection(RbacUsersPeer::DATABASE_NAME);
        try {
            $rbacUsers = new RbacUsers();
            do {
                $infoData['USR_UID'] = G::generateUniqueID();
            } while ($rbacUsers->load($infoData['USR_UID']));
            $rbacUsers->fromArray($infoData, BasePeer::TYPE_FIELDNAME);
            $result = $rbacUsers->save();

            return $infoData['USR_UID'];
        } catch (Exception $error) {
            $connection->rollback();
            throw($error);
        }
    }

    /**
     * Update an user
     * @param string $infoData
     * @return boolean
     * @throws Exception
     */
    public function update($infoData)
    {
        if (in_array($infoData['USR_UID'], $this->userUidReserved)) {
            throw new Exception(G::LoadTranslation("ID_USER_CAN_NOT_UPDATE", array($infoData['USR_UID'])));
            return false;
        }
        $oConnection = Propel::getConnection(RbacUsersPeer::DATABASE_NAME);
        try {
            $this->fromArray($infoData, BasePeer::TYPE_FIELDNAME);
            $this->setNew(false);
            $result = $this->save();
        } catch (Exception $error) {
            $oConnection->rollback();
            throw($error);
        }
    }

    /**
     * Remove an user
     * @param string $userUid
     * @return void
     */
    public function remove($userUid = '')
    {
        $this->setUsrUid($userUid);
        $this->delete();
    }

    /**
     * Gets an associative array with total users by authentication sources
     * @return array $listAuth
     */
    public function getAllUsersByAuthSource()
    {
        $criteria = new Criteria('rbac');
        $criteria->addSelectColumn(RbacUsersPeer::UID_AUTH_SOURCE);
        $criteria->addSelectColumn('COUNT(*) AS CNT');
        $criteria->add(RbacUsersPeer::USR_STATUS, 'CLOSED', Criteria::NOT_EQUAL);
        $criteria->addGroupByColumn(RbacUsersPeer::UID_AUTH_SOURCE);
        $dataset = RbacUsersPeer::doSelectRS($criteria, Propel::getDbConnection('rbac_ro'));
        $dataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);

        $listAuth = [];
        while ($dataset->next()) {
            $row = $dataset->getRow();
            $listAuth[$row['UID_AUTH_SOURCE']] = $row['CNT'];
        }

        return $listAuth;
    }

    /**
     * Get users list related to an authentication source
     * @param string $authSource
     * @return array $listUsers, all users with auth_source
     */
    public function getListUsersByAuthSource($authSource)
    {
        $criteria = new Criteria('rbac');
        $criteria->addSelectColumn(RbacUsersPeer::USR_UID);

        if ($authSource == '00000000000000000000000000000000') {
            $criteria->add(
                $criteria->getNewCriterion(RbacUsersPeer::UID_AUTH_SOURCE, $authSource, Criteria::EQUAL)->addOr(
                    $criteria->getNewCriterion(RbacUsersPeer::UID_AUTH_SOURCE, '', Criteria::EQUAL)
                ));
        } else {
            $criteria->add(RbacUsersPeer::UID_AUTH_SOURCE, $authSource, Criteria::EQUAL);
        }
        $criteria->add(RbacUsersPeer::USR_STATUS, 0, Criteria::NOT_EQUAL);
        $dataset = RbacUsersPeer::doSelectRS($criteria, Propel::getDbConnection('rbac_ro'));
        $dataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $listUsers = [];
        while ($dataset->next()) {
            $row = $dataset->getRow();
            $listUsers[] = $row['USR_UID'];
        }

        return $listUsers;
    }

    /**
     * Get the user's role
     * @param string $userUid
     * @return array $row
     * @throws Exception
     */
    public function getUserRole($userUid)
    {
        $con = Propel::getConnection(UsersRolesPeer::DATABASE_NAME);
        try {
            $c = new Criteria('rbac');
            $c->clearSelectColumns();
            $c->addSelectColumn(RolesPeer::ROL_UID);
            $c->addSelectColumn(RolesPeer::ROL_CODE);
            $c->addSelectColumn(RolesPeer::ROL_STATUS);
            $c->addJoin(UsersRolesPeer::ROL_UID, RolesPeer::ROL_UID);
            $c->add(UsersRolesPeer::USR_UID, $userUid);
            $rs = UsersRolesPeer::doSelectRs($c, Propel::getDbConnection('rbac_ro'));
            $rs->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $rs->next();
            $row = $rs->getRow();

            return $row;
        } catch (Exception $error) {
            throw($error);
        }
    }

    /**
     * {@inheritdoc} except USR_PASSWORD, for security reasons.
     *
     * @param string $keyType One of the class type constants TYPE_PHPNAME,
     *                        TYPE_COLNAME, TYPE_FIELDNAME, TYPE_NUM
     * @param boolean $original If true return de original verion of fields.
     * @return an associative array containing the field names (as keys) and field values
     */
    public function toArray($keyType = BasePeer::TYPE_PHPNAME, $original = false)
    {
        if ($original) {
            return parent::toArray($keyType);
        }
        $key = RbacUsersPeer::translateFieldName(
            RbacUsersPeer::USR_PASSWORD,
            BasePeer::TYPE_COLNAME,
            $keyType
        );
        $array = parent::toArray($keyType);
        unset($array[$key]);

        return $array;
    }
}

// Users
