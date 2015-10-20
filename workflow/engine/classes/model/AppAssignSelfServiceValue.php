<?php
class AppAssignSelfServiceValue extends BaseAppAssignSelfServiceValue
{
    /**
     * Create record
     *
     * @param string $applicationUid Unique id of Case
     * @param int    $delIndex       Delegation index
     * @param array  $arrayData      Data
     *
     * return void
     */
    public function create($applicationUid, $delIndex, array $arrayData)
    {
        try {
            $cnn = Propel::getConnection(AppAssignSelfServiceValuePeer::DATABASE_NAME);

            try {
                $appAssignSelfServiceValue = new AppAssignSelfServiceValue();

                $appAssignSelfServiceValue->fromArray($arrayData, BasePeer::TYPE_FIELDNAME);

                $appAssignSelfServiceValue->setAppUid($applicationUid);
                $appAssignSelfServiceValue->setDelIndex($delIndex);

                if ($appAssignSelfServiceValue->validate()) {
                    $cnn->begin();

                    $result = $appAssignSelfServiceValue->save();

                    $cnn->commit();
                } else {
                    $msg = "";

                    foreach ($appAssignSelfServiceValue->getValidationFailures() as $validationFailure) {
                        $msg .= (($msg != "")? "\n" : "") . $validationFailure->getMessage();
                    }

                    throw new Exception(G::LoadTranslation("ID_RECORD_CANNOT_BE_CREATED") . (($msg != "")? "\n" . $msg : ""));
                }
            } catch (Exception $e) {
                $cnn->rollback();

                throw $e;
            }
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Remove record
     *
     * @param string $applicationUid Unique id of Case
     * @param int    $delIndex       Delegation index
     *
     * return void
     */
    public function remove($applicationUid, $delIndex = 0)
    {
        try {
            $criteria = new Criteria("workflow");

            $criteria->add(AppAssignSelfServiceValuePeer::APP_UID, $applicationUid, Criteria::EQUAL);

            if ($delIndex != 0) {
                $criteria->add(AppAssignSelfServiceValuePeer::DEL_INDEX, $delIndex, Criteria::EQUAL);
            }

            $result = AppAssignSelfServiceValuePeer::doDelete($criteria);
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Generate data
     *
     * return void
     */
    public function generateData()
    {
        try {
            G::LoadClass("case");

            AppAssignSelfServiceValuePeer::doDeleteAll(); //Delete all records

            //Generate data
            $case = new Cases();

            $criteria = new Criteria("workflow");

            $criteria->addSelectColumn(AppDelegationPeer::APP_UID);
            $criteria->addSelectColumn(AppDelegationPeer::DEL_INDEX);
            $criteria->addSelectColumn(ApplicationPeer::APP_DATA);
            $criteria->addSelectColumn(AppDelegationPeer::PRO_UID);
            $criteria->addSelectColumn(TaskPeer::TAS_UID);
            $criteria->addSelectColumn(TaskPeer::TAS_GROUP_VARIABLE);
            $criteria->addJoin(AppDelegationPeer::APP_UID, ApplicationPeer::APP_UID, Criteria::LEFT_JOIN);
            $criteria->addJoin(AppDelegationPeer::TAS_UID, TaskPeer::TAS_UID, Criteria::LEFT_JOIN);
            $criteria->add(TaskPeer::TAS_ASSIGN_TYPE, "SELF_SERVICE", Criteria::EQUAL);
            $criteria->add(TaskPeer::TAS_GROUP_VARIABLE, "", Criteria::NOT_EQUAL);
            $criteria->add(AppDelegationPeer::USR_UID, "", Criteria::EQUAL);
            $criteria->add(AppDelegationPeer::DEL_THREAD_STATUS, "OPEN", Criteria::EQUAL);

            $rsCriteria = AppDelegationPeer::doSelectRS($criteria);
            $rsCriteria->setFetchmode(ResultSet::FETCHMODE_ASSOC);

            while ($rsCriteria->next()) {
                $row = $rsCriteria->getRow();

                $applicationData = $case->unserializeData($row["APP_DATA"]);
                $taskGroupVariable = trim($row["TAS_GROUP_VARIABLE"], " @#");

                if ($taskGroupVariable != "" && isset($applicationData[$taskGroupVariable])) {
                    $dataVariable = $applicationData[$taskGroupVariable];
                    $dataVariable = (is_array($dataVariable))? $dataVariable : trim($dataVariable);

                    if (!empty($dataVariable)) {
                        $this->create($row["APP_UID"], $row["DEL_INDEX"], array("PRO_UID" => $row["PRO_UID"], "TAS_UID" => $row["TAS_UID"], "GRP_UID" => serialize($dataVariable)));
                    }
                }
            }
        } catch (Exception $e) {
            throw $e;
        }
    }
}

