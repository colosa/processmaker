<?php
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

use ProcessMaker\BusinessModel\WebEntry;
class TaskUser extends BaseTaskUser
{

    /**
     * Create the new record in the table TaskUser
     *
     * @param array $requestData
     * @return string
     * @throws Exception
     *
     */
    public function create ($requestData)
    {
        $connection = Propel::getConnection(TaskUserPeer::DATABASE_NAME);
        try {
            $bmWebEntry = new WebEntry;
            //Check the usrUid value
            if (RBAC::isGuestUserUid($requestData['USR_UID']) && !$bmWebEntry->isTaskAWebEntry($requestData['TAS_UID'])) {
                throw new Exception(G::LoadTranslation("ID_USER_CAN_NOT_UPDATE", array($requestData['USR_UID'])));
                return false;
            }

            $taskUser = TaskUserPeer::retrieveByPK(
                $requestData['TAS_UID'],
                $requestData['USR_UID'],
                $requestData['TU_TYPE'],
                $requestData['TU_RELATION']
            );

            if (is_object($taskUser)) {
                return -1;
            }

            $taskUser = new TaskUser();
            $taskUser->fromArray($requestData, BasePeer::TYPE_FIELDNAME);
            if ($taskUser->validate()) {
                $connection->begin();
                $result = $taskUser->save();
                $connection->commit();

                return $result;
            } else {
                $message = '';
                $aValidationFailures = $taskUser->getValidationFailures();
                foreach ($aValidationFailures as $oValidationFailure) {
                    $message .= $oValidationFailure->getMessage() . '<br />';
                }
                throw (new Exception('The registry cannot be created!<br />' . $message));
            }
        } catch (Exception $oError) {
            $connection->rollback();
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
        $result = new stdClass();
        $result->data = $usersTask;
        $result->totalCount = sizeof( $usersTask );

        return $result;
    }
    /**
     * Get All users assigned to task
     *
     * @param string $TAS_UID
     * @return array users info
     *
     */
    public function getAllUsersTask ($TAS_UID)
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
        $dataset = TaskUserPeer::doSelectRS( $criteria );
        $dataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
        while ($dataset->next()) {
            $row = $dataset->getRow();
            if($row["TU_RELATION"] == 2){                
                $gpr = new GroupUser();
                $array = $gpr->getAllGroupUser($row["USR_UID"]);
                foreach($array as $urow){
                  $usersTask[] = $urow;
                }
            }else{
                $usersTask[] = $row;
            }
        }
        return $usersTask;
    }
}

