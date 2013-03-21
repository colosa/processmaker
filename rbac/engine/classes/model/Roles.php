<?php
/**
 * Roles.php
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
require_once 'classes/model/Permissions.php';
require_once 'classes/model/Systems.php';
require_once 'classes/model/RolesPermissions.php';

require_once 'classes/model/om/BaseRoles.php';
require_once 'classes/model/om/BaseRbacUsers.php';
require_once 'classes/model/om/BaseUsersRoles.php';

require_once 'classes/model/Content.php';

/**
 * Skeleton subclass for representing a row from the 'ROLES' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package  rbac-classes-model
 */
class Roles extends BaseRoles {

    public $rol_name;

   /**
    * Function load
    * access public
   */

    public function load($Uid) {
        try {
            $oRow = RolesPeer::retrieveByPK($Uid);
            if (! is_null($oRow)) {
                $aFields = $oRow->toArray(BasePeer::TYPE_FIELDNAME);
                $this->fromArray($aFields, BasePeer::TYPE_FIELDNAME);
                $this->setNew(false);

                $this->getRolName();
                $aFields['ROL_NAME'] = $this->rol_name;

                return $aFields;
            } else {
                throw (new Exception("The '$Uid' row doesn't exist!"));
            }
        } catch( exception $oError ) {
            throw ($oError);
        }
    }

    function loadByCode($sRolCode = '') {
        try {
            $oCriteria = new Criteria('rbac');
            $oCriteria->add(RolesPeer::ROL_CODE, $sRolCode);
            $oDataset = RolesPeer::doSelectRS($oCriteria);
            $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $oDataset->next();
            $aRow = $oDataset->getRow();
            if (is_array($aRow)) {
                return $aRow;
            } else {
                throw (new Exception("The role '$sRolCode' doesn\'t exist!"));
            }
        } catch( exception $oError ) {
            throw ($oError);
        }
    }

    function listAllRoles($systemCode = 'PROCESSMAKER', $filter = '') {
        try {

            $oCriteria = new Criteria('rbac');
            $oCriteria->addSelectColumn(RolesPeer::ROL_UID);
            $oCriteria->addSelectColumn(RolesPeer::ROL_PARENT);
            $oCriteria->addSelectColumn(RolesPeer::ROL_SYSTEM);
            $oCriteria->addSelectColumn(SystemsPeer::SYS_CODE);
            $oCriteria->addSelectColumn(RolesPeer::ROL_CODE);
            $oCriteria->addSelectColumn(RolesPeer::ROL_CREATE_DATE);
            $oCriteria->addSelectColumn(RolesPeer::ROL_UPDATE_DATE);
            $oCriteria->addSelectColumn(RolesPeer::ROL_STATUS);
            $oCriteria->add(RolesPeer::ROL_UID, '', Criteria::NOT_EQUAL);
            $oCriteria->add(SystemsPeer::SYS_CODE, $systemCode);
            $oCriteria->add(RolesPeer::ROL_CREATE_DATE, '', Criteria::NOT_EQUAL);
            $oCriteria->add(RolesPeer::ROL_UPDATE_DATE, '', Criteria::NOT_EQUAL);
            //Added by QENNIX Jan 21th, 2011
            if ($filter != ''){
              $oCriteria->add(RolesPeer::ROL_CODE, '%'.$filter.'%', Criteria::LIKE);
            }
            $oCriteria->addJoin(RolesPeer::ROL_SYSTEM, SystemsPeer::SYS_UID);

            return $oCriteria;

        } catch( exception $oError ) {
            throw (new Exception("Class ROLES::FATAL ERROR. Criteria with rbac Can't initialized "));
        }
    }

    //Added by QENNIX
	function getAllRolesFilter($start, $limit, $filter='') {
		//echo $start.'<<<<'.$limit;
		$systemCode = 'PROCESSMAKER';
		$oCriteria2 = new Criteria('rbac');
		$result = Array();

		$oCriteria2->addSelectColumn('COUNT(*) AS CNT');
		$oCriteria2->add(RolesPeer::ROL_UID, '', Criteria::NOT_EQUAL);
    $oCriteria2->add(SystemsPeer::SYS_CODE, $systemCode);
    $oCriteria2->add(RolesPeer::ROL_CREATE_DATE, '', Criteria::NOT_EQUAL);
    $oCriteria2->add(RolesPeer::ROL_UPDATE_DATE, '', Criteria::NOT_EQUAL);
    $oCriteria2->addJoin(RolesPeer::ROL_SYSTEM, SystemsPeer::SYS_UID);
	  if ($filter != ''){
      $oCriteria2->add(RolesPeer::ROL_CODE, '%'.$filter.'%', Criteria::LIKE);
    }
    $result['COUNTER'] = $oCriteria2;
    $oCriteria = new Criteria('rbac');
    $oCriteria->clear();
    $oCriteria->addSelectColumn(RolesPeer::ROL_UID);
    $oCriteria->addSelectColumn(RolesPeer::ROL_PARENT);
    $oCriteria->addSelectColumn(RolesPeer::ROL_SYSTEM);
    $oCriteria->addSelectColumn(SystemsPeer::SYS_CODE);
    $oCriteria->addSelectColumn(RolesPeer::ROL_CODE);
    $oCriteria->addSelectColumn(RolesPeer::ROL_CREATE_DATE);
    $oCriteria->addSelectColumn(RolesPeer::ROL_UPDATE_DATE);
    $oCriteria->addSelectColumn(RolesPeer::ROL_STATUS);
    $oCriteria->add(RolesPeer::ROL_UID, '', Criteria::NOT_EQUAL);
    $oCriteria->add(SystemsPeer::SYS_CODE, $systemCode);
    $oCriteria->add(RolesPeer::ROL_CREATE_DATE, '', Criteria::NOT_EQUAL);
    $oCriteria->add(RolesPeer::ROL_UPDATE_DATE, '', Criteria::NOT_EQUAL);
    $oCriteria->addJoin(RolesPeer::ROL_SYSTEM, SystemsPeer::SYS_UID);

    if ($filter != ''){
      $oCriteria->add(RolesPeer::ROL_CODE, '%'.$filter.'%', Criteria::LIKE);
    }

    $oCriteria->setOffset($start);
    $oCriteria->setLimit($limit);

    $result['LIST'] = $oCriteria;

    return $result;
	}

    function getAllRoles($systemCode = 'PROCESSMAKER') {
        $c = $this->listAllRoles($systemCode);
		    $rs = RolesPeer::DoSelectRs($c);
        $rs->setFetchmode(ResultSet::FETCHMODE_ASSOC);

        $aRows = Array();
        while($rs->next()){
        	$row = $rs->getRow();
        	$o = new Roles();
        	$o->load($row['ROL_UID']);
        	$row['ROL_NAME'] = $o->getRolName();
        	$aRows[] = $row;
        }
        return $aRows;
    }

    function listAllPermissions($systemCode = 'PROCESSMAKER') {
        try {
            $oCriteria = new Criteria('rbac');
            $oCriteria->addSelectColumn(PermissionsPeer::PER_UID);
            $oCriteria->addSelectColumn(PermissionsPeer::PER_CODE);
            $oCriteria->addSelectColumn(PermissionsPeer::PER_CREATE_DATE);
            $oCriteria->addSelectColumn(PermissionsPeer::PER_UPDATE_DATE);
            $oCriteria->addSelectColumn(PermissionsPeer::PER_STATUS);
            $oCriteria->add(PermissionsPeer::PER_CODE, substr($systemCode, 0, 3) . '_%', Criteria::LIKE);

            return $oCriteria;

        } catch( exception $oError ) {
            throw (new Exception("Class ROLES::FATAL ERROR. Criteria with rbac Can't initialized "));
        }
    }

    function createRole($aData) {
        $con = Propel::getConnection(RolesPeer::DATABASE_NAME);
        try {
            $con->begin();
            $sRolCode = $aData['ROL_CODE'];
            $sRolSystem = $aData['ROL_SYSTEM'];
            $oCriteria = new Criteria('rbac');
            $oCriteria->add(RolesPeer::ROL_CODE, $sRolCode);
            $oCriteria->add(RolesPeer::ROL_SYSTEM, $sRolSystem);
            $oDataset = RolesPeer::doSelectRS($oCriteria);
            $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $oDataset->next();
            $aRow = $oDataset->getRow();
            if (is_array($aRow)) {
                return $aRow;
            }

            $rol_name = $aData['ROL_NAME'];
            unset($aData['ROL_NAME']);

            $obj = new Roles();
            $obj->fromArray($aData, BasePeer::TYPE_FIELDNAME);
            if ($obj->validate()) {
                $result = $obj->save();
                $con->commit();

                $obj->setRolName($rol_name);

            } else {
                $e = new Exception("Failed Validation in class " . get_class($this) . ".");
                $e->aValidationFailures = $this->getValidationFailures();
                throw ($e);
            }
            return $result;
        } catch( exception $e ) {
            $con->rollback();
            throw ($e);
        }
    }

    public function updateRole($fields) {
        $con = Propel::getConnection(RolesPeer::DATABASE_NAME);
        try {
            $con->begin();
            $this->load($fields['ROL_UID']);
            $rol_name = $fields['ROL_NAME'];
            unset($fields['ROL_NAME']);

            $this->fromArray($fields, BasePeer::TYPE_FIELDNAME);
            if ($this->validate()) {
                $result = $this->save();
                $con->commit();

                $this->setRolName($rol_name);
                return $result;
            } else {
                $con->rollback();
                throw (new Exception("Failed Validation in class " . get_class($this) . "."));
            }
        } catch( exception $e ) {
            $con->rollback();
            throw ($e);
        }
    }

    function removeRole($ROL_UID) {
        $con = Propel::getConnection(RolesPeer::DATABASE_NAME);
        try {
            $con->begin();
            $this->setRolUid($ROL_UID);
            Content::removeContent('ROL_NAME', '', $this->getRolUid());
            $result = $this->delete();

            $con->commit();
            return $result;
        } catch( exception $e ) {
            $con->rollback();
            throw ($e);
        }
    }

    function verifyNewRole($code) {
        $code = trim($code);
        $oCriteria = new Criteria('rbac');
        $oCriteria->addSelectColumn(RolesPeer::ROL_UID);
        $oCriteria->add(RolesPeer::ROL_CODE, $code);
        $count = RolesPeer::doCount($oCriteria);

        if ($count == 0) {
            return true;
        } else {
            return false;
        }
    }

    function loadById($ROL_UID) {

        $oCriteria = new Criteria('rbac');
        $oCriteria->addSelectColumn(RolesPeer::ROL_UID);
        $oCriteria->addSelectColumn(RolesPeer::ROL_PARENT);
        $oCriteria->addSelectColumn(RolesPeer::ROL_SYSTEM);
        $oCriteria->addSelectColumn(RolesPeer::ROL_CODE);
        $oCriteria->addSelectColumn(RolesPeer::ROL_CREATE_DATE);
        $oCriteria->addSelectColumn(RolesPeer::ROL_UPDATE_DATE);
        $oCriteria->addSelectColumn(RolesPeer::ROL_STATUS);
        $oCriteria->add(RolesPeer::ROL_UID, $ROL_UID);

        $result = RolesPeer::doSelectRS($oCriteria);
        $result->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $result->next();

        $row = $result->getRow();
        if (is_array($row)) {
            $o = RolesPeer::retrieveByPK($row['ROL_UID']);
            $row['ROL_NAME'] = $o->getRolName();
            return $row;
        } else {
            return null;
        }
    }

    function getRoleCode($ROL_UID) {
        $oCriteria = new Criteria('rbac');
        $oCriteria->addSelectColumn(RolesPeer::ROL_UID);
        $oCriteria->addSelectColumn(RolesPeer::ROL_CODE);
        $oCriteria->add(RolesPeer::ROL_UID, $ROL_UID);

        $result = RolesPeer::doSelectRS($oCriteria);
        $result->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $result->next();
        $row = $result->getRow();
        $ret = $row['ROL_CODE'];

        return $ret;
    }

    //Added by Enrique at Feb 9th, 2011
    //Gets number of users by role
    function getAllUsersByRole(){
    	 $oCriteria = new Criteria('rbac');
    	 $oCriteria->addSelectColumn(UsersRolesPeer::ROL_UID);
    	 $oCriteria->addSelectColumn('COUNT(*) AS CNT');
    	 $oCriteria->addJoin(RbacUsersPeer::USR_UID,UsersRolesPeer::USR_UID,Criteria::INNER_JOIN);
    	 $oCriteria->add(RbacUsersPeer::USR_STATUS,0,Criteria::NOT_EQUAL);
    	 $oCriteria->addGroupByColumn(UsersRolesPeer::ROL_UID);
    	 $oDataset = UsersRolesPeer::doSelectRS($oCriteria);
    	 $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
    	 $aRoles = array();
    	 while ($oDataset->next()){
    	 	 $row = $oDataset->getRow();
    	 	 $aRoles[$row['ROL_UID']] = $row['CNT'];
    	 }
    	 return $aRoles;
    }

    //Added by Enrique at Feb 10th, 2011
    //Gets number of users by department
    function getAllUsersByDepartment(){
    	 $oCriteria = new Criteria('rbac');
    	 $oCriteria->addSelectColumn(UsersPeer::DEP_UID);
    	 $oCriteria->addSelectColumn('COUNT(*) AS CNT');
    	 $oCriteria->add(UsersPeer::USR_STATUS, 'CLOSED', Criteria::NOT_EQUAL);
    	 $oCriteria->addGroupByColumn(UsersPeer::DEP_UID);
    	 $oDataset = UsersPeer::doSelectRS($oCriteria);
    	 $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
    	 $aDepts = array();
    	 while ($oDataset->next()){
    	 	 $row = $oDataset->getRow();
    	 	 $aDepts[$row['DEP_UID']] = $row['CNT'];
    	 }
    	 return $aDepts;
    }

    function getRoleUsers($ROL_UID, $filter='') {
        try {
            $criteria = new Criteria();
            $criteria->addSelectColumn(RolesPeer::ROL_UID);
            $criteria->addSelectColumn(RolesPeer::ROL_PARENT);
            $criteria->addSelectColumn(RolesPeer::ROL_SYSTEM);
            $criteria->addSelectColumn(RolesPeer::ROL_CODE);
            $criteria->addSelectColumn(RolesPeer::ROL_CREATE_DATE);
            $criteria->addSelectColumn(RolesPeer::ROL_UPDATE_DATE);
            $criteria->addSelectColumn(RolesPeer::ROL_STATUS);
            $criteria->addSelectColumn(RbacUsersPeer::USR_UID);
            $criteria->addSelectColumn(RbacUsersPeer::USR_USERNAME);
            $criteria->addSelectColumn(RbacUsersPeer::USR_FIRSTNAME);
            $criteria->addSelectColumn(RbacUsersPeer::USR_LASTNAME);
            $criteria->add(RolesPeer::ROL_UID, "", Criteria::NOT_EQUAL);
            $criteria->add(RolesPeer::ROL_UID, $ROL_UID);

            $criteria->add(RbacUsersPeer::USR_STATUS, 0, Criteria::NOT_EQUAL);

            $criteria->addJoin(RolesPeer::ROL_UID, UsersRolesPeer::ROL_UID);
            $criteria->addJoin(UsersRolesPeer::USR_UID, RbacUsersPeer::USR_UID);

            if ($filter != ''){
            	$criteria->add(
            	  $criteria->getNewCriterion(RbacUsersPeer::USR_USERNAME,'%'.$filter.'%',Criteria::LIKE)->addOr(
            	  $criteria->getNewCriterion(RbacUsersPeer::USR_FIRSTNAME,'%'.$filter.'%',Criteria::LIKE)->addOr(
            	  $criteria->getNewCriterion(RbacUsersPeer::USR_LASTNAME,'%'.$filter.'%',Criteria::LIKE)))
            	);
            }

            $oDataset = RolesPeer::doSelectRS($criteria);
            $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            return $oDataset;

        } catch( exception $e ) {
            throw $e;
        }
    }

    function getAllUsers($ROL_UID, $filter='') {
        try {
            $c = new Criteria();
            $c->addSelectColumn(RbacUsersPeer::USR_UID);
            $c->add(RolesPeer::ROL_UID, $ROL_UID);
            $c->addJoin(RolesPeer::ROL_UID, UsersRolesPeer::ROL_UID);
            $c->addJoin(UsersRolesPeer::USR_UID, RbacUsersPeer::USR_UID);

            $result = RolesPeer::doSelectRS($c);
            $result->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $result->next();

            $a = Array();
            while( $row = $result->getRow() ) {
                $a[] = $row['USR_UID'];
                $result->next();
            }

            $criteria = new Criteria();

            $criteria->addSelectColumn(RbacUsersPeer::USR_UID);
            $criteria->addSelectColumn(RbacUsersPeer::USR_USERNAME);
            $criteria->addSelectColumn(RbacUsersPeer::USR_FIRSTNAME);
            $criteria->addSelectColumn(RbacUsersPeer::USR_LASTNAME);
            $criteria->add(RbacUsersPeer::USR_STATUS, 1, Criteria::EQUAL);
            $criteria->add(RbacUsersPeer::USR_UID, $a, Criteria::NOT_IN);

            if ($filter != ''){
            	$criteria->add(
            	  $criteria->getNewCriterion(RbacUsersPeer::USR_USERNAME,'%'.$filter.'%',Criteria::LIKE)->addOr(
            	  $criteria->getNewCriterion(RbacUsersPeer::USR_FIRSTNAME,'%'.$filter.'%',Criteria::LIKE)->addOr(
            	  $criteria->getNewCriterion(RbacUsersPeer::USR_LASTNAME,'%'.$filter.'%',Criteria::LIKE)))
            	);
            }

            $oDataset = RbacUsersPeer::doSelectRS($criteria);
            $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            return $oDataset;
        } catch( exception $e ) {
            throw $e;
        }
    }

    function assignUserToRole($aData) {
        /*find the system uid for this role */
        require_once 'classes/model/Users.php';
        $c = new Criteria();
        $c->add(RolesPeer::ROL_UID, $aData['ROL_UID']);
        $result = RolesPeer::doSelectRS($c);
        $result->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $result->next();
        $row = $result->getRow();
        $sSystemId = $row['ROL_SYSTEM'];

        //updating the role into users table
        $oCriteria1 = new Criteria('workflow');
        $oCriteria1->add(UsersPeer::USR_UID , $aData['USR_UID'], Criteria::EQUAL);
        $oCriteria2 = new Criteria('workflow');
        $oCriteria2->add(UsersPeer::USR_ROLE , $row['ROL_CODE']);
        BasePeer::doUpdate($oCriteria1, $oCriteria2, Propel::getConnection('workflow'));

        //delete roles for the same System
        $c = new Criteria();
        $c->addSelectColumn(UsersRolesPeer::USR_UID);
        $c->addSelectColumn(RolesPeer::ROL_UID);
        $c->addSelectColumn(RolesPeer::ROL_CODE);
        $c->addSelectColumn(RolesPeer::ROL_SYSTEM);
        $c->add(UsersRolesPeer::USR_UID, $aData['USR_UID']);
        $c->add(RolesPeer::ROL_SYSTEM, $sSystemId);
        $c->addJoin(RolesPeer::ROL_UID, UsersRolesPeer::ROL_UID);
        $result = RolesPeer::doSelectRS($c);
        $result->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $result->next();

        while( $row = $result->getRow() ) {
            $crit = new Criteria();
            $crit->add(UsersRolesPeer::USR_UID, $row['USR_UID']);
            $crit->add(UsersRolesPeer::ROL_UID, $row['ROL_UID']);
            UsersRolesPeer::doDelete($crit);
            $result->next();
        }

        //save the unique role for this system
        $oUsersRoles = new UsersRoles();
        $oUsersRoles->setUsrUid($aData['USR_UID']);
        $oUsersRoles->setRolUid($aData['ROL_UID']);
        $oUsersRoles->save();

    }

    function deleteUserRole($ROL_UID, $USR_UID) {
        $crit = new Criteria();
        $crit->add(UsersRolesPeer::USR_UID, $USR_UID);

        if ($ROL_UID != '%') {
            $crit->add(UsersRolesPeer::ROL_UID, $ROL_UID);
        }
        UsersRolesPeer::doDelete($crit);
    }

    function getRolePermissions($ROL_UID, $filter='', $status=null) {
        try {
            $criteria = new Criteria();
            $criteria->addSelectColumn(RolesPeer::ROL_UID);
            $criteria->addSelectColumn(RolesPeer::ROL_PARENT);
            $criteria->addSelectColumn(RolesPeer::ROL_SYSTEM);
            $criteria->addSelectColumn(RolesPeer::ROL_CODE);
            $criteria->addSelectColumn(RolesPeer::ROL_CREATE_DATE);
            $criteria->addSelectColumn(RolesPeer::ROL_UPDATE_DATE);
            $criteria->addSelectColumn(RolesPeer::ROL_STATUS);
            $criteria->addSelectColumn(PermissionsPeer::PER_UID);
            $criteria->addSelectColumn(PermissionsPeer::PER_CODE);
            $criteria->addSelectColumn(PermissionsPeer::PER_CREATE_DATE);
            $criteria->addSelectColumn(PermissionsPeer::PER_UPDATE_DATE);
            $criteria->addSelectColumn(PermissionsPeer::PER_STATUS);
            $criteria->add(RolesPeer::ROL_UID, "", Criteria::NOT_EQUAL);
            $criteria->add(RolesPeer::ROL_UID, $ROL_UID);
            $criteria->addJoin(RolesPeer::ROL_UID, RolesPermissionsPeer::ROL_UID);
            $criteria->addJoin(RolesPermissionsPeer::PER_UID, PermissionsPeer::PER_UID);

            if ($filter != '') {
            	$criteria->add(PermissionsPeer::PER_CODE, '%'.$filter.'%',Criteria::LIKE);
            }

            if (!is_null($status) && ($status == 1 || $status == 0)) {
                $criteria->add(PermissionsPeer::PER_STATUS, $status);
            }

            $oDataset = RolesPeer::doSelectRS($criteria);
            $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            return $oDataset;

        } catch( exception $e ) {
            throw $e;
        }
    }

    function getAllPermissions($ROL_UID, $PER_SYSTEM = "", $filter='', $status=null) {
        try {
            $c = new Criteria();
            $c->addSelectColumn(PermissionsPeer::PER_UID);
            $c->add(RolesPeer::ROL_UID, $ROL_UID);
            $c->addJoin(RolesPeer::ROL_UID, RolesPermissionsPeer::ROL_UID);
            $c->addJoin(RolesPermissionsPeer::PER_UID, PermissionsPeer::PER_UID);

            $result = PermissionsPeer::doSelectRS($c);
            $result->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $result->next();

            $a = Array();
            while( $row = $result->getRow() ) {
                $a[] = $row['PER_UID'];
                $result->next();
            }

            $criteria = new Criteria();
            $criteria->addSelectColumn(PermissionsPeer::PER_UID);
            $criteria->addSelectColumn(PermissionsPeer::PER_CODE);
            $criteria->addSelectColumn(PermissionsPeer::PER_CREATE_DATE);
            $criteria->addSelectColumn(PermissionsPeer::PER_UPDATE_DATE);
            $criteria->addSelectColumn(PermissionsPeer::PER_STATUS);
            $criteria->addSelectColumn(PermissionsPeer::PER_SYSTEM);
            $criteria->addSelectColumn(SystemsPeer::SYS_CODE);
            $criteria->add(PermissionsPeer::PER_UID, $a, Criteria::NOT_IN);
            if ($PER_SYSTEM != "") {
                $criteria->add(SystemsPeer::SYS_CODE, $PER_SYSTEM);
            }
            $criteria->addJoin(PermissionsPeer::PER_SYSTEM, SystemsPeer::SYS_UID);

        	if ($filter != ''){
            	$criteria->add(PermissionsPeer::PER_CODE, '%'.$filter.'%',Criteria::LIKE);
            }

            if (!is_null($status) && ($status == 1 || $status == 0)) {
                $criteria->add(PermissionsPeer::PER_STATUS, $status);
            }

            $oDataset = PermissionsPeer::doSelectRS($criteria);
            $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            return $oDataset;
        } catch( exception $e ) {
            throw $e;
        }
    }

    function assignPermissionRole($sData) {
        $o = new RolesPermissions();
        $o->setPerUid($sData['PER_UID']);
        $o->setRolUid($sData['ROL_UID']);
        $o->save();
    }

    function deletePermissionRole($ROL_UID, $PER_UID) {
        $crit = new Criteria();
        $crit->add(RolesPermissionsPeer::ROL_UID, $ROL_UID);
        $crit->add(RolesPermissionsPeer::PER_UID, $PER_UID);
        RolesPermissionsPeer::doDelete($crit);
    }

    function numUsersWithRole($ROL_UID) {
        $criteria = new Criteria();
        $criteria->addSelectColumn(RbacUsersPeer::USR_UID);
        $criteria->add(RolesPeer::ROL_UID, "", Criteria::NOT_EQUAL);
        $criteria->add(RolesPeer::ROL_UID, $ROL_UID);
        $criteria->addJoin(RolesPeer::ROL_UID, UsersRolesPeer::ROL_UID);
        $criteria->addJoin(UsersRolesPeer::USR_UID, RbacUsersPeer::USR_UID);

        return RolesPeer::doCount($criteria);
    }

    function verifyByCode($sRolCode = '') {
        try {
            $oCriteria = new Criteria('rbac');
            $oCriteria->add(RolesPeer::ROL_CODE, $sRolCode);
            $oDataset = RolesPeer::doSelectRS($oCriteria);
            $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $oDataset->next();
            $aRow = $oDataset->getRow();
            if (is_array($aRow)) {
                return 1;
            } else {
                return 0;
            }
        } catch( exception $oError ) {
            throw ($oError);
        }
    }

    public function getRolName() {
        if ($this->getRolUid() == '') {
            throw (new Exception("Error in getRolName, the ROL_UID can't be blank"));
        }
        $lang = defined('SYS_LANG') ? SYS_LANG : 'en';
        $this->rol_name = Content::load('ROL_NAME', '', $this->getRolUid(), $lang);
        return $this->rol_name;
    }

    public function setRolName($v) {
        if ($this->getRolUid() == '') {
            throw (new Exception("Error in setProTitle, the PRO_UID can't be blank"));
        }
        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && ! is_string($v)) {
            $v = (string)$v;
        }

        if ($this->rol_name !== $v || $v === '') {
            $this->rol_name = $v;
            $lang = defined('SYS_LANG') ? SYS_LANG : 'en';
            $res = Content::addContent('ROL_NAME', '', $this->getRolUid(), $lang, $this->rol_name);
        }

    }
} // Roles
