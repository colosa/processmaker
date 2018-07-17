<?php
/**
 * ObjectPermission.php
 *
 * @package workflow.engine.classes.model
 */

//require_once 'classes/model/om/BaseObjectPermission.php';

/**
 * Skeleton subclass for representing a row from the 'OBJECT_PERMISSION' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements. This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package workflow.engine.classes.model
 */
class ObjectPermission extends BaseObjectPermission
{
    public function load ($UID)
    {
        try {
            $oRow = ObjectPermissionPeer::retrieveByPK( $UID );
            if (! is_null( $oRow )) {
                $aFields = $oRow->toArray( BasePeer::TYPE_FIELDNAME );
                $this->fromArray( $aFields, BasePeer::TYPE_FIELDNAME );
                $this->setNew( false );
                return $aFields;
            } else {
                throw (new Exception( "The row '" . $UsrUid . "' in table USER doesn't exist!" ));
            }
        } catch (Exception $oError) {
            throw ($oError);
        }
    }

    public function create ($aData)
    {
        try {
            $this->fromArray( $aData, BasePeer::TYPE_FIELDNAME );
            $result = $this->save();
            return $result;
        } catch (Exception $e) {
            throw ($e);
        }
    }

    public function Exists ($Uid)
    {
        try {
            $oPro = ObjectPermissionPeer::retrieveByPk( $Uid );
            if (is_object( $oPro ) && get_class( $oPro ) == 'ObjectPermission') {
                return true;
            } else {
                return false;
            }
        } catch (Exception $oError) {
            throw ($oError);
        }
    }

    public function remove ($Uid)
    {
        $con = Propel::getConnection( ObjectPermissionPeer::DATABASE_NAME );
        try {
            $oObjPer = ObjectPermissionPeer::retrieveByPK( $Uid );
            if (is_object( $oObjPer ) && get_class( $oObjPer ) == 'ObjectPermission') {
                $con->begin();
                $iResult = $oObjPer->delete();
                $con->commit();
                return $iResult;
            } else {
                throw (new Exception( "The row '" . $Uid . "' in table CaseTrackerObject doesn't exist!" ));
            }
        } catch (exception $e) {
            $con->rollback();
            throw ($e);
        }
    }

    public function update ($aFields)
    {
        $oConnection = Propel::getConnection( ObjectPermissionPeer::DATABASE_NAME );
        try {
            $oConnection->begin();
            $this->load( $aFields['OP_UID'] );
            $this->fromArray( $aFields, BasePeer::TYPE_FIELDNAME );
            if ($this->validate()) {
                $iResult = $this->save();
                $oConnection->commit();
                return $iResult;
            } else {
                $oConnection->rollback();
                throw (new Exception( 'Failed Validation in class ' . get_class( $this ) . '.' ));
            }
        } catch (Exception $e) {
            $oConnection->rollback();
            throw ($e);
        }
    }

    public function removeByObject ($sType, $sObjUid)
    {
        try {
            $oCriteria = new Criteria( 'workflow' );
            $oCriteria->add( ObjectPermissionPeer::OP_OBJ_TYPE, $sType );
            $oCriteria->add( ObjectPermissionPeer::OP_OBJ_UID, $sObjUid );
            ObjectPermissionPeer::doDelete( $oCriteria );
        } catch (Exception $e) {
            throw ($e);
        }
    }

    public function loadInfo ($sObjUID)
    {

        $oCriteria = new Criteria( 'workflow' );
        $oCriteria->add( ObjectPermissionPeer::OP_OBJ_UID, $sObjUID );
        $oDataset = ObjectPermissionPeer::doSelectRS( $oCriteria );
        $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
        $oDataset->next();
        $aRow = $oDataset->getRow();
        return ($aRow);
    }

    /**
     * verify if a dynaform is assigned some steps
     *
     * @param string $proUid the uid of the process
     * @param string $dynUid the uid of the dynaform
     *
     * @return array
     */
    public function verifyDynaformAssigObjectPermission ($dynUid, $proUid)
    {
        $res = array();
        $oCriteria = new Criteria();
        $oCriteria->addSelectColumn( ObjectPermissionPeer::OP_UID );
        $oCriteria->add( ObjectPermissionPeer::PRO_UID, $proUid );
        $oCriteria->add( ObjectPermissionPeer::OP_OBJ_UID, $dynUid );
        $oCriteria->add( ObjectPermissionPeer::OP_OBJ_TYPE, 'DYNAFORM' );
        $oDataset = ObjectPermissionPeer::doSelectRS( $oCriteria );
        $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
        while($oDataset->next()) {
            $res[] = $oDataset->getRow();
        }
        return $res;
    }

    /**
     * Verify if the user has a objectPermission
     *
     * @param string $usrUid the uid of the user
     * @param string $proUid the uid of the process
     * @param string $tasUid the uid of the task
     * @param string $action for the object permissions VIEW, BLOCK, RESEND
     * @param array $caseData for review the case status DRAFT, TODO, COMPLETED, PAUSED
     *
     * @return array
     */
    public function verifyObjectPermissionPerUser ($usrUid, $proUid, $tasUid = '', $action = '', $caseData = array())
    {
        $userPermissions = array();
        $oCriteria = new Criteria('workflow');
        $oCriteria->add(
                $oCriteria->getNewCriterion(ObjectPermissionPeer::USR_UID, $usrUid)->addOr(
                        $oCriteria->getNewCriterion(ObjectPermissionPeer::USR_UID, '')->addOr(
                                $oCriteria->getNewCriterion(ObjectPermissionPeer::USR_UID, '0')
                        )
                )
        );
        $oCriteria->add(ObjectPermissionPeer::PRO_UID, $proUid);
        $oCriteria->add(ObjectPermissionPeer::OP_ACTION, $action);
        $oCriteria->add(
                $oCriteria->getNewCriterion(ObjectPermissionPeer::TAS_UID, $tasUid)->addOr(
                        $oCriteria->getNewCriterion(ObjectPermissionPeer::TAS_UID, '')->addOr(
                                $oCriteria->getNewCriterion(ObjectPermissionPeer::TAS_UID, '0')
                        )
                )
        );

        $rs = ObjectPermissionPeer::doSelectRS($oCriteria);
        $rs->setFetchmode(ResultSet::FETCHMODE_ASSOC);

        while ($rs->next()) {
            $row = $rs->getRow();

            if ($row["OP_CASE_STATUS"] == "ALL" || $row["OP_CASE_STATUS"] == "" || $row["OP_CASE_STATUS"] == "0" ||
                $row["OP_CASE_STATUS"] == $caseData["APP_STATUS"]
            ) {
                array_push($userPermissions, $row);
            }
        }
        return $userPermissions;
    }

    /**
     * Verify if the user has a objectPermission
     *
     * @param string $usrUid the uid of the user
     * @param string $proUid the uid of the process
     * @param string $tasUid the uid of the task
     * @param string $action for the object permissions VIEW, BLOCK, RESEND
     * @param array $caseData for review the case status DRAFT, TODO, COMPLETED, PAUSED
     *
     * @return array
     */
    public function verifyObjectPermissionPerGroup ($usrUid, $proUid, $tasUid = '', $action = '', $caseData = array())
    {
        $gr = new Groups();
        $records = $gr->getActiveGroupsForAnUser($usrUid);
        $groupPermissions = array();

        foreach ($records as $group) {
            $oCriteria = new Criteria('workflow');
            $oCriteria->add(ObjectPermissionPeer::USR_UID, $group);
            $oCriteria->add(ObjectPermissionPeer::PRO_UID, $proUid);
            $oCriteria->add(ObjectPermissionPeer::OP_ACTION, $action);
            $oCriteria->add(
                    $oCriteria->getNewCriterion(ObjectPermissionPeer::TAS_UID, $tasUid)->addOr(
                            $oCriteria->getNewCriterion(ObjectPermissionPeer::TAS_UID, '')->addOr(
                                    $oCriteria->getNewCriterion(ObjectPermissionPeer::TAS_UID, '0')
                            )
                    )
            );

            $rs = ObjectPermissionPeer::doSelectRS($oCriteria);
            $rs->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            while ($rs->next()) {
                $row = $rs->getRow();

                if ($row["OP_CASE_STATUS"] == "ALL" || $row["OP_CASE_STATUS"] == "" || $row["OP_CASE_STATUS"] == "0" ||
                    $row["OP_CASE_STATUS"] == $caseData["APP_STATUS"]
                ) {
                    array_push($groupPermissions, $row);
                }
            }
        }
        return $groupPermissions;
    }

    /**
     * Verify if the user has the Message History access
     *
     * @param string $appUid the uid of the case
     * @param string $proUid the uid of the process
     * @param string $usrUid the uid of the user
     * @param string $opTaskSource the uid of a task selected in origin task
     * @param int $opUserRelation if the permission is by user or group
     * @param string $statusCase the status of the case COMPLETED, TO_DO
     * @param int $opParticipated the value selected in participation required
     *
     * @return array with the indexes with the messageHistory permission
     */
    public function objectPermissionMessage ($appUid, $proUid, $usrUid, $obAction, $opTaskSource, $opUserRelation, $statusCase = '', $opParticipated = 0)
    {
        $result['MSGS_HISTORY'] = array('PERMISSION' => $obAction);
        $arrayDelIndex = array();

        $oCriteria = new Criteria('workflow');
        if ($opUserRelation == 1) {
            //The relation is one is related to users
            $oCriteria->add(AppDelegationPeer::APP_UID, $appUid);
            $oCriteria->add(AppDelegationPeer::PRO_UID, $proUid);

            //If the permission Participation required = YES
            if ((int)$opParticipated === 1) {
                $oCriteria->add(AppDelegationPeer::USR_UID, $usrUid);
            }

            //If the case is COMPLETED we can not considered the Origin Task
            if ($statusCase != 'COMPLETED' && !empty($opTaskSource) && (int)$opTaskSource != 0) {
                $oCriteria->add(AppDelegationPeer::TAS_UID, $opTaskSource);
            }

            $oDataset = AppDelegationPeer::doSelectRS($oCriteria);
            $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $oDataset->next();
            while ($aRow = $oDataset->getRow()) {
                $arrayDelIndex[] = $aRow["DEL_INDEX"];
                $oDataset->next();
            }
        } else {
            //The relation is two is related to groups
            $oCriteria->add(AppDelegationPeer::APP_UID, $appUid);
            $oCriteria->add(AppDelegationPeer::PRO_UID, $proUid);

            //If the permission Participation required = YES
            if ((int)$opParticipated === 1) {
                $oCriteria->addJoin(GroupUserPeer::USR_UID, AppDelegationPeer::USR_UID, Criteria::LEFT_JOIN);
                $oCriteria->add(GroupUserPeer::GRP_UID, $usrUid);
            }

            //If the case is COMPLETED we can not considered the Origin Task
            if ($statusCase != 'COMPLETED' && !empty($opTaskSource) && (int)$opTaskSource != 0) {
                $oCriteria->add(AppDelegationPeer::TAS_UID, $opTaskSource);
            }

            $oDataset = AppDelegationPeer::doSelectRS($oCriteria);
            $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $oDataset->next();
            while ($aRow = $oDataset->getRow()) {
                $arrayDelIndex[] = $aRow["DEL_INDEX"];
                $oDataset->next();
            }
        }
        return array_merge(array("DEL_INDEX" => $arrayDelIndex), $result["MSGS_HISTORY"]);
    }

    /**
     * Verify if the user has the Dynaform access
     *
     * @param string $appUid the uid of the case
     * @param string $opTaskSource the uid of a task selected in origin task
     * @param integer $opObjUid uid of dynaform
     * @param string $statusCase the status of the case COMPLETED, TO_DO
     *
     * @return array with the uid of dynaforms
     */
    public function objectPermissionByDynaform ($appUid, $opTaskSource = 0, $opObjUid = '', $statusCase = '')
    {
        $oCriteria = new Criteria('workflow');
        $oCriteria->addSelectColumn("*");
        $oCriteria->addJoin(ApplicationPeer::PRO_UID, StepPeer::PRO_UID);
        $oCriteria->addJoin(StepPeer::STEP_UID_OBJ, DynaformPeer::DYN_UID);
        $oCriteria->add(ApplicationPeer::APP_UID, $appUid);
        $oCriteria->add(StepPeer::STEP_TYPE_OBJ, 'DYNAFORM');

        if ($statusCase != 'COMPLETED' && $opTaskSource != '' && (int)$opTaskSource != 0) {
            $oCriteria->add(StepPeer::TAS_UID, $opTaskSource);
        }

        if ($opObjUid != '' && $opObjUid != '0') {
            $oCriteria->add(DynaformPeer::DYN_UID, $opObjUid);
        }
        $oCriteria->addAscendingOrderByColumn(StepPeer::STEP_POSITION);
        $oCriteria->setDistinct();

        $oDataset = DynaformPeer::doSelectRS($oCriteria);
        $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);

        $result = array();
        $oDataset->next();
        while ($aRow = $oDataset->getRow()) {
            if (!in_array($aRow['DYN_UID'], $result)) {
                array_push($result, $aRow['DYN_UID']);
            }
            $oDataset->next();
        }
        return $result;
    }

    /**
     * Verify if the user has the Dynaform access
     *
     * @param string $appUid the uid of the case
     * @param string $proUid the uid of the process
     * @param string $opTaskSource the uid of a task selected in origin task
     * @param string $obType can be INPUT or OUTPUT
     * @param string $opObjUid uid of object [specific input or specific ouput]
     * @param string $statusCase the status of the case COMPLETED, TO_DO
     *
     * @return array with the uid of input or outputs
     */
    public function objectPermissionByOutputInput ($appUid, $proUid, $opTaskSource, $obType = 'OUTPUT', $opObjUid = '', $statusCase = '')
    {
        $oCriteria = new Criteria('workflow');
        $oCriteria->addSelectColumn(AppDocumentPeer::APP_DOC_UID);
        $oCriteria->addSelectColumn(AppDocumentPeer::APP_DOC_TYPE);
        $arrayCondition = array();
        $arrayCondition[] = array(AppDelegationPeer::APP_UID, AppDocumentPeer::APP_UID, Criteria::EQUAL);
        $arrayCondition[] = array(AppDelegationPeer::DEL_INDEX, AppDocumentPeer::DEL_INDEX, Criteria::EQUAL);
        $oCriteria->addJoinMC($arrayCondition, Criteria::LEFT_JOIN);
        $oCriteria->add(AppDelegationPeer::APP_UID, $appUid);
        $oCriteria->add(AppDelegationPeer::PRO_UID, $proUid);

        if ($statusCase != 'COMPLETED' && $opTaskSource != '' && (int)$opTaskSource != 0) {
            $oCriteria->add(AppDelegationPeer::TAS_UID, $opTaskSource);
        }
        if ($opObjUid != '' && $opObjUid != '0') {
            $oCriteria->add(AppDocumentPeer::DOC_UID, $opObjUid);
        }
        switch ($obType) {
            case 'INPUT':
                $oCriteria->add(AppDocumentPeer::APP_DOC_TYPE, 'INPUT');
                break;
            case 'ATTACHED':
                $oCriteria->add(AppDocumentPeer::APP_DOC_TYPE, 'ATTACHED');
                break;
            case 'OUTPUT':
                $oCriteria->add(AppDocumentPeer::APP_DOC_TYPE, 'OUTPUT');
                break;
        }

        $oDataset = AppDelegationPeer::doSelectRS($oCriteria);
        $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);

        $result = array();
        while ($oDataset->next()) {
            $aRow = $oDataset->getRow();
            if (!in_array($aRow['APP_DOC_UID'], $result)) {
                array_push($result, $aRow['APP_DOC_UID']);
            }
        }
        return $result;
    }

    /**
     * Verify if the user has a objectPermission for some process
     *
     * @param string $usrUid the uid of the user
     * @param int $typeRelation
     *
     * @return array
     */
    public function objectPermissionPerUser($usrUid, $typeRelation = 1)
    {
        $criteria = new Criteria("workflow");
        $criteria->addSelectColumn(ObjectPermissionPeer::USR_UID);
        $criteria->addSelectColumn(ObjectPermissionPeer::PRO_UID);
        $criteria->add(ObjectPermissionPeer::OP_USER_RELATION, $typeRelation, Criteria::EQUAL);
        $criteria->add(ObjectPermissionPeer::USR_UID, $usrUid, Criteria::EQUAL);
        $doSelectRS = ObjectPermissionPeer::doSelectRS($criteria);
        $doSelectRS->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $doSelectRS->next();
        $objectPermision = $doSelectRS->getRow();
        $data = array();
        if (isset($objectPermision["USR_UID"])) {
            $criteria = new Criteria("workflow");
            $criteria->addSelectColumn(ProcessPeer::PRO_TITLE);
            $criteria->add(ProcessPeer::PRO_UID, $objectPermision["PRO_UID"], Criteria::EQUAL);
            $doSelectRS = ProcessPeer::doSelectRS($criteria);
            $doSelectRS->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $doSelectRS->next();
            $content = $doSelectRS->getRow();
            $data['PRO_TITLE'] = $content["PRO_TITLE"];
            $data['PRO_UID'] = $objectPermision["PRO_UID"];
        }
        return $data;
    }
}

