<?php
/**
 * TaskUser.php
 *
 * @package workflow.engine.classes.model
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

require_once 'classes/model/om/BaseTaskUser.php';
require_once 'classes/model/Content.php';

/**
 * Skeleton subclass for representing a row from the 'GROUP_USER' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements. This class will only be generated as
 * long as it does not already exist in the input directory.
 *
 * @package workflow.engine.classes.model
 */
class TaskUser extends BaseTaskUser
{

    /**
     * Create the application document registry
     *
     * @param array $aData
     * @return string
     *
     */
    public function create ($aData)
    {
        $oConnection = Propel::getConnection( TaskUserPeer::DATABASE_NAME );
        try {
            $taskUser = TaskUserPeer::retrieveByPK( $aData['TAS_UID'], $aData['USR_UID'], $aData['TU_TYPE'], $aData['TU_RELATION'] );

            if (is_object( $taskUser )) {
                return - 1;
            }
            $oTaskUser = new TaskUser();
            $oTaskUser->fromArray( $aData, BasePeer::TYPE_FIELDNAME );
            if ($oTaskUser->validate()) {
                $oConnection->begin();
                $iResult = $oTaskUser->save();
                $oConnection->commit();
                return $iResult;
            } else {
                $sMessage = '';
                $aValidationFailures = $oTaskUser->getValidationFailures();
                foreach ($aValidationFailures as $oValidationFailure) {
                    $sMessage .= $oValidationFailure->getMessage() . '<br />';
                }
                throw (new Exception( 'The registry cannot be created!<br />' . $sMessage ));
            }
        } catch (Exception $oError) {
            $oConnection->rollback();
            throw ($oError);
        }
    }

    /**
     * Remove the application document registry
     *
     * @param string $sTasUid
     * @param string $sUserUid
     * @return string
     *
     */
    public function remove ($sTasUid, $sUserUid, $iType, $iRelation)
    {
        $oConnection = Propel::getConnection( TaskUserPeer::DATABASE_NAME );
        try {
            $oTaskUser = TaskUserPeer::retrieveByPK( $sTasUid, $sUserUid, $iType, $iRelation );
            if (! is_null( $oTaskUser )) {
                $oConnection->begin();
                $iResult = $oTaskUser->delete();
                $oConnection->commit();
                return $iResult;
            } else {
                throw (new Exception( 'This row does not exist!' ));
            }
        } catch (Exception $oError) {
            $oConnection->rollback();
            throw ($oError);
        }
    }

    public function TaskUserExists ($sTasUid, $sUserUid, $iType, $iRelation)
    {
        $con = Propel::getConnection( TaskUserPeer::DATABASE_NAME );
        try {
            $oTaskUser = TaskUserPeer::retrieveByPk( $sTasUid, $sUserUid, $iType, $iRelation );
            if (is_object( $oTaskUser ) && get_class( $oTaskUser ) == 'TaskUser') {
                return true;
            } else {
                return false;
            }
        } catch (Exception $oError) {
            throw ($oError);
        }
    }

    public function getCountAllTaksByGroups ()
    {
        $oCriteria = new Criteria( 'workflow' );
        $oCriteria->addAsColumn( 'GRP_UID', TaskUserPeer::USR_UID );
        $oCriteria->addSelectColumn( 'COUNT(*) AS CNT' );
        $oCriteria->add( TaskUserPeer::TU_TYPE, 1 );
        $oCriteria->add( TaskUserPeer::TU_RELATION, 2 );
        $oCriteria->addGroupByColumn( TaskUserPeer::USR_UID );
        $oDataset = TaskUserPeer::doSelectRS( $oCriteria );
        $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
        $aRows = Array ();
        while ($oDataset->next()) {
            $row = $oDataset->getRow();
            $aRows[$row['GRP_UID']] = $row['CNT'];
        }
        return $aRows;
    }
    //erik: new functions
    public function getUsersTask ($TAS_UID, $TU_TYPE = 1)
    {
        require_once 'classes/model/Users.php';

        $groupsTask = array ();
        $usersTask = array ();

        //getting task's users
        $criteria = new Criteria( 'workflow' );
        $criteria->addSelectColumn( UsersPeer::USR_FIRSTNAME );
        $criteria->addSelectColumn( UsersPeer::USR_LASTNAME );
        $criteria->addSelectColumn( UsersPeer::USR_USERNAME );
        $criteria->addSelectColumn( TaskUserPeer::TAS_UID );
        $criteria->addSelectColumn( TaskUserPeer::USR_UID );
        $criteria->addSelectColumn( TaskUserPeer::TU_TYPE );
        $criteria->addSelectColumn( TaskUserPeer::TU_RELATION );
        $criteria->addJoin( TaskUserPeer::USR_UID, UsersPeer::USR_UID, Criteria::LEFT_JOIN );
        $criteria->add( TaskUserPeer::TAS_UID, $TAS_UID );
        $criteria->add( TaskUserPeer::TU_TYPE, $TU_TYPE );
        $criteria->add( TaskUserPeer::TU_RELATION, 1 );

        $dataset = TaskUserPeer::doSelectRS( $criteria );
        $dataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );

        while ($dataset->next()) {
            $usersTask[] = $dataset->getRow();
        }
        //getting task's groups
        $delimiter = DBAdapter::getStringDelimiter();
        $criteria = new Criteria( 'workflow' );
        $criteria->addAsColumn( 'GRP_TITLE', 'CONTENT.CON_VALUE' );
        $criteria->addSelectColumn( TaskUserPeer::TAS_UID );
        $criteria->addSelectColumn( TaskUserPeer::USR_UID );
        $criteria->addSelectColumn( TaskUserPeer::TU_TYPE );
        $criteria->addSelectColumn( TaskUserPeer::TU_RELATION );
        $aConditions[] = array (TaskUserPeer::USR_UID,'CONTENT.CON_ID');
        $aConditions[] = array ('CONTENT.CON_CATEGORY',$delimiter . 'GRP_TITLE' . $delimiter);
        $aConditions[] = array ('CONTENT.CON_LANG',$delimiter . SYS_LANG . $delimiter);
        $criteria->addJoinMC( $aConditions, Criteria::LEFT_JOIN );
        $criteria->add( TaskUserPeer::TAS_UID, $TAS_UID );
        $criteria->add( TaskUserPeer::TU_TYPE, $TU_TYPE );
        $criteria->add( TaskUserPeer::TU_RELATION, 2 );
        $dataset = TaskUserPeer::doSelectRS( $criteria );
        $dataset = TaskUserPeer::doSelectRS( $criteria );
        $dataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );

        while ($dataset->next()) {
            $usersTask[] = $dataset->getRow();
        }
        $result->data = $usersTask;
        $result->totalCount = sizeof( $usersTask );

        return $result;
    }
}
// TaskUser

