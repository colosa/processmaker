<?php
/**
 * class.groups.php
 *
 * @package workflow.engine.ProcessMaker
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
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 * For more information, contact Colosa Inc, 2566 Le Jeune Rd.,
 * Coral Gables, FL, 33134, USA, or email info@colosa.com.
 *
 */
require_once 'classes/model/Groupwf.php';
require_once 'classes/model/GroupUser.php';
require_once 'classes/model/Users.php';

/**
 * Groups - Groups class
 *
 * @package workflow.engine.ProcessMaker
 * @copyright 2007 COLOSA
 */

class Groups
{

    /**
     * Get the assigned users of a group
     *
     * @param string $sGroupUID
     * @return array
     */
    function getUsersOfGroup ($sGroupUID)
    {
        try {
            $aUsers = array ();
            $oCriteria = new Criteria();
            $oCriteria->addJoin( UsersPeer::USR_UID, GroupUserPeer::USR_UID, Criteria::LEFT_JOIN );
            $oCriteria->add( GroupUserPeer::GRP_UID, $sGroupUID );
            $oCriteria->add( UsersPeer::USR_STATUS, 'ACTIVE' );
            $oDataset = UsersPeer::doSelectRS( $oCriteria );
            $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
            $oDataset->next();

            while ($aRow = $oDataset->getRow()) {
                $aUsers[] = $aRow;
                $oDataset->next();
            }
            return $aUsers;
        } catch (exception $oError) {
            throw ($oError);
        }
    }

    /**
     * Get the active groups for an user
     *
     * @param string $sUserUID
     * @return array
     */
    function getActiveGroupsForAnUser ($sUserUID)
    {
        try {
            $oCriteria = new Criteria();
            $oCriteria->addSelectColumn( GroupUserPeer::GRP_UID );
            $oCriteria->addSelectColumn( GroupwfPeer::GRP_STATUS );
            $oCriteria->add( GroupUserPeer::USR_UID, $sUserUID );
            $oCriteria->add( GroupwfPeer::GRP_STATUS, 'ACTIVE' );
            $oCriteria->addJoin( GroupUserPeer::GRP_UID, GroupwfPeer::GRP_UID, Criteria::LEFT_JOIN );
            $oDataset = GroupUserPeer::doSelectRS( $oCriteria );
            $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
            $oDataset->next();

            $aGroups = array ();
            $aRow = $oDataset->getRow();
            while (is_array( $aRow )) {
                $aGroups[] = $aRow['GRP_UID'];
                $oDataset->next();
                $aRow = $oDataset->getRow();
            }
            return $aGroups;
        } catch (exception $oError) {
            throw ($oError);
        }
    }

    /**
     * Set a user to group
     *
     * @param string $GrpUid, $UsrUid
     * @return array
     */
    function addUserToGroup ($GrpUid, $UsrUid)
    {
        try {
            $oGrp = GroupUserPeer::retrieveByPk( $GrpUid, $UsrUid );
            if (is_object( $oGrp ) && get_class( $oGrp ) == 'GroupUser') {
                return true;
            } else {
                $oGrp = new GroupUser();
                $oGrp->setGrpUid( $GrpUid );
                $oGrp->setUsrUid( $UsrUid );
                $oGrp->Save();
            }
        } catch (exception $oError) {
            throw ($oError);
        }
    }

    /*
  * Remove a user from group
  * @param string $GrpUid, $UsrUid
  * @return array
  */
    function removeUserOfGroup ($GrpUid, $UsrUid)
    {
        $gu = new GroupUser();
        $gu->remove( $GrpUid, $UsrUid );
    }

    /**
     * get all groups
     *
     * @param none
     * @return $objects
     */
    function getAllGroups ()
    {
        try {
            $criteria = new Criteria();
            $criteria->add( GroupwfPeer::GRP_UID, "", Criteria::NOT_EQUAL );
            $con = Propel::getConnection( GroupwfPeer::DATABASE_NAME );
            $objects = GroupwfPeer::doSelect( $criteria, $con );
            return $objects;
        } catch (exception $e) {
            throw $e;
        }
    }

    /**
     * get all the groups from a single user
     *
     * @param $sUserUid user uid
     * @return an array of group objects
     */
    function getUserGroups ($sUserUID)
    {
        try {
            $criteria = new Criteria();
            $criteria->add( GroupwfPeer::GRP_UID, "", Criteria::NOT_EQUAL );
            $criteria->add( GroupUserPeer::USR_UID, $sUserUID );
            $criteria->add( GroupwfPeer::GRP_STATUS, 'ACTIVE' );
            $criteria->addJoin( GroupUserPeer::GRP_UID, GroupwfPeer::GRP_UID, Criteria::LEFT_JOIN );
            $con = Propel::getConnection( GroupwfPeer::DATABASE_NAME );
            $objects = GroupwfPeer::doSelect( $criteria, $con );
            return $objects;
        } catch (exception $e) {
            throw $e;
        }
    }

    /**
     * Get Available Groups for a single user
     *
     * @author Qennix
     * @param string $sUserUid
     * @return object
     */

    function getAvailableGroupsCriteria ($sUserUid, $filter = '')
    {
        try {
            $oCriteria = new Criteria( 'workflow' );
            $oCriteria->addSelectColumn( GroupUserPeer::GRP_UID );
            $oCriteria->add( GroupUserPeer::USR_UID, $sUserUid );
            $oCriteria->add( GroupUserPeer::GRP_UID, '', Criteria::NOT_EQUAL );
            $oDataset = GroupUserPeer::doSelectRS( $oCriteria );
            $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
            $oDataset->next();
            $gUIDs = array ();
            while ($aRow = $oDataset->getRow()) {
                $gUIDs[] = $aRow['GRP_UID'];
                $oDataset->next();
            }
            $oCriteria = new Criteria( 'workflow' );
            $oCriteria->addSelectColumn( GroupwfPeer::GRP_UID );
            $oCriteria->addSelectColumn( GroupwfPeer::GRP_STATUS );
            $oCriteria->addSelectColumn( ContentPeer::CON_VALUE );
            $oCriteria->addJoin( GroupwfPeer::GRP_UID, ContentPeer::CON_ID, Criteria::LEFT_JOIN );
            $oCriteria->add( GroupwfPeer::GRP_UID, $gUIDs, Criteria::NOT_IN );
            $oCriteria->add( GroupwfPeer::GRP_STATUS, 'ACTIVE' );
            $oCriteria->add( ContentPeer::CON_CATEGORY, 'GRP_TITLE' );
            $oCriteria->add( ContentPeer::CON_LANG, SYS_LANG );

            if ($filter != '') {
                $oCriteria->add( ContentPeer::CON_VALUE, '%' . $filter . '%', Criteria::LIKE );
            }

            return $oCriteria;
        } catch (exception $e) {
            throw $e;
        }
    }

    /**
     * Get Assigned Groups for a single user
     *
     * @author Qennix
     * @param string $sUserUid
     * @return object
     */

    function getAssignedGroupsCriteria ($sUserUid, $filter = '')
    {
        try {
            $oCriteria = new Criteria( 'workflow' );
            $oCriteria->addSelectColumn( GroupwfPeer::GRP_UID );
            $oCriteria->addSelectColumn( GroupwfPeer::GRP_STATUS );
            $oCriteria->addSelectColumn( GroupwfPeer::GRP_LDAP_DN );
            $oCriteria->addSelectColumn( ContentPeer::CON_VALUE );
            $oCriteria->addJoin( GroupUserPeer::GRP_UID, GroupwfPeer::GRP_UID, Criteria::LEFT_JOIN );
            $oCriteria->addJoin( GroupwfPeer::GRP_UID, ContentPeer::CON_ID, Criteria::LEFT_JOIN );
            $oCriteria->add( GroupUserPeer::USR_UID, $sUserUid, Criteria::EQUAL );
            $oCriteria->add( GroupwfPeer::GRP_STATUS, 'ACTIVE' );
            $oCriteria->add( ContentPeer::CON_CATEGORY, 'GRP_TITLE' );
            $oCriteria->add( ContentPeer::CON_LANG, SYS_LANG );

            if ($filter != '') {
                $oCriteria->add( ContentPeer::CON_VALUE, '%' . $filter . '%', Criteria::LIKE );
            }

            return $oCriteria;
        } catch (exception $e) {
            throw $e;
        }
    }

    function getGroupsForUser ($usrUid)
    {
        $criteria = $this->getAssignedGroupsCriteria( $usrUid );
        $criteria->addAscendingOrderByColumn( ContentPeer::CON_VALUE );
        $dataset = GroupwfPeer::doSelectRS( $criteria );
        $dataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
        $dataset->next();
        $groups = array ();
        while ($row = $dataset->getRow()) {
            if (! isset( $groups[$row['GRP_UID']] )) {
                $groups[$row['GRP_UID']] = $row;
            }
            $dataset->next();
        }
        return $groups;
    }

    /**
     * Remove a user from all groups
     *
     * @param string $sUsrUid
     * @return void
     */
    public function removeUserOfAllGroups ($sUserUID = '')
    {
        try {
            $oCriteria = new Criteria( 'workflow' );
            $oCriteria->add( GroupUserPeer::USR_UID, $sUserUID );
            GroupUserPeer::doDelete( $oCriteria );
        } catch (exception $oError) {
            throw ($oError);
        }
    }

    /**
     * Get a criteria object of all users from group
     *
     * @param string $sGroupUID
     * @return array
     */
    function getUsersGroupCriteria ($sGroupUID = '')
    {
        require_once 'classes/model/GroupUser.php';
        require_once 'classes/model/Users.php';
        try {
            $oCriteria = new Criteria( 'workflow' );
            $oCriteria->addSelectColumn( GroupUserPeer::GRP_UID );
            $oCriteria->addSelectColumn( UsersPeer::USR_UID );
            $oCriteria->addSelectColumn( UsersPeer::USR_USERNAME );
            $oCriteria->addSelectColumn( UsersPeer::USR_FIRSTNAME );
            $oCriteria->addSelectColumn( UsersPeer::USR_LASTNAME );
            $oCriteria->addSelectColumn( UsersPeer::USR_EMAIL );
            $oCriteria->addJoin( GroupUserPeer::USR_UID, UsersPeer::USR_UID, Criteria::LEFT_JOIN );
            $oCriteria->add( GroupUserPeer::GRP_UID, $sGroupUID );
            $oCriteria->add( UsersPeer::USR_STATUS, 'ACTIVE' );
            return $oCriteria;
        } catch (exception $oError) {
            throw ($oError);
        }
    }

    /**
     * Get a criteria object of all groups from user
     *
     * @param string $sGroupUID
     * @return array
     */
    function getUserGroupsCriteria ($sUserUID = '')
    {
        require_once 'classes/model/GroupUser.php';
        require_once 'classes/model/Users.php';
        try {
            $oCriteria = new Criteria( 'workflow' );
            $oCriteria->addSelectColumn( UsersPeer::USR_UID );
            $oCriteria->addSelectColumn( GroupUserPeer::GRP_UID );
            $oCriteria->addSelectColumn( UsersPeer::USR_FIRSTNAME );
            $oCriteria->addSelectColumn( UsersPeer::USR_LASTNAME );
            $oCriteria->addJoin( GroupUserPeer::USR_UID, UsersPeer::USR_UID, Criteria::LEFT_JOIN );
            $oCriteria->add( GroupUserPeer::GRP_UID, $sUserUID );
            $oCriteria->add( UsersPeer::USR_STATUS, 'ACTIVE' );
            return $oCriteria;
        } catch (exception $oError) {
            throw ($oError);
        }
    }

    /**
     * Get the number of groups
     *
     * @param string $sGroupUid
     * @return integer $cnt
     */
    function getNumberGroups ($sUserUID)
    {
        try {
            $allGroups = $this->getUserGroups( $sUserUID );
            $cnt = 0;
            foreach ($allGroups as $group) {
                $cnt ++;
            }
            return $cnt;
        } catch (exception $oError) {
            print_r( $oError );
        }
    }

    /**
     * Return the available users list criteria object
     *
     * @param string $sGroupUID
     * @return object
     */
    function getAvailableUsersCriteria ($sGroupUID = '')
    {
        try {
            $oCriteria = new Criteria( 'workflow' );
            $oCriteria->addSelectColumn( UsersPeer::USR_UID );
            $oCriteria->addJoin( GroupUserPeer::USR_UID, UsersPeer::USR_UID, Criteria::LEFT_JOIN );
            $oCriteria->add( GroupUserPeer::GRP_UID, $sGroupUID );
            $oCriteria->add( UsersPeer::USR_STATUS, 'ACTIVE' );
            $oDataset = UsersPeer::doSelectRS( $oCriteria );
            $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
            $oDataset->next();
            $aUIDs = array ();
            while ($aRow = $oDataset->getRow()) {
                $aUIDs[] = $aRow['USR_UID'];
                $oDataset->next();
            }
            $oCriteria = new Criteria( 'workflow' );
            $oCriteria->addSelectColumn( UsersPeer::USR_UID );
            $oCriteria->addSelectColumn( UsersPeer::USR_FIRSTNAME );
            $oCriteria->addSelectColumn( UsersPeer::USR_LASTNAME );
            $oCriteria->add( UsersPeer::USR_UID, $aUIDs, Criteria::NOT_IN );
            $oCriteria->add( UsersPeer::USR_STATUS, 'ACTIVE' );
            return $oCriteria;
        } catch (exception $oError) {
            throw ($oError);
        }
    }

    /**
     * Verify if a user is assigned to a group
     *
     * @param $GrpUid group Uid
     * @param $UsrUid user Uid
     * @return 1/0 if it's or not assigned
     */
    function verifyUsertoGroup ($GrpUid, $UsrUid)
    {
        try {
            $oGrp = GroupUserPeer::retrieveByPk( $GrpUid, $UsrUid );
            if (is_object( $oGrp ) && get_class( $oGrp ) == 'GroupUser') {
                return 1;
            } else {
                return 0;
            }
        } catch (exception $oError) {
            throw ($oError);
        }
    }

    /**
     * Verify the existence of a Group
     *
     * @param $sGroupUid group Uid
     * @return 1/0 if exist or not
     */
    function verifyGroup ($sGroupUID)
    {
        try {
            $aUsers = array ();
            $oCriteria = new Criteria();
            //$oCriteria->addJoin(UsersPeer::USR_UID, GroupUserPeer::USR_UID, Criteria::LEFT_JOIN);
            $oCriteria->add( GroupwfPeer::GRP_UID, $sGroupUID );
            //$oCriteria->add(UsersPeer::USR_STATUS, 'ACTIVE');
            $oDataset = GroupwfPeer::doSelectRS( $oCriteria );
            $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
            $oDataset->next();
            $aRow = $oDataset->getRow();
            if (is_array( $aRow ))
                return 1;
            else
                return 0;
        } catch (exception $oError) {
            throw ($oError);
        }
    }

    /**
     * Load all the data of a group with a search based on it uid
     *
     * @param $GrpUid group uid
     * @return an array of objects/false/exception object
     *
     */
    public function load ($GrpUid)
    {
        try {
            $criteria = new Criteria();
            $criteria->add( GroupwfPeer::GRP_UID, $GrpUid, Criteria::EQUAL );
            $con = Propel::getConnection( GroupwfPeer::DATABASE_NAME );
            $objects = GroupwfPeer::doSelect( $criteria, $con );
            if (is_array( $objects ) && count( $objects ) > 0) {
                return $objects[0];
            } else {
                return false;
            }
        } catch (exception $e) {
            throw $e;
        }
    }
}
