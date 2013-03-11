<?php

class Services_Rest_Step
{
    /**
     * Implementation for 'GET' method for Rest API
     *
     * @param  mixed $stepUid Primary key
     *
     * @return array $result Returns array within multiple records or a single record depending if
     *                       a single selection was requested passing id(s) as param
     */
    protected function get($stepUid=null)
    {
        $result = array();
        try {
            $noArguments = true;
            $argumentList = func_get_args();
            foreach ($argumentList as $arg) {
                if (!is_null($arg)) {
                    $noArguments = false;
                }
            }

            if ($noArguments) {
                $criteria = new Criteria('workflow');

                $criteria->addSelectColumn(StepPeer::STEP_UID);
                $criteria->addSelectColumn(StepPeer::PRO_UID);
                $criteria->addSelectColumn(StepPeer::TAS_UID);
                $criteria->addSelectColumn(StepPeer::STEP_TYPE_OBJ);
                $criteria->addSelectColumn(StepPeer::STEP_UID_OBJ);
                $criteria->addSelectColumn(StepPeer::STEP_CONDITION);
                $criteria->addSelectColumn(StepPeer::STEP_POSITION);
                $criteria->addSelectColumn(StepPeer::STEP_MODE);

                $dataset = AppEventPeer::doSelectRS($criteria);
                $dataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);

                while ($dataset->next()) {
                    $result[] = $dataset->getRow();
                }
            } else {
                $record = StepPeer::retrieveByPK($stepUid);
                if ($record) {
                    $result = $record->toArray(BasePeer::TYPE_FIELDNAME);
                } else {
                    $paramValues = "";
                    foreach ($argumentList as $arg) {
                        $paramValues .= (strlen($paramValues) ) ? ', ' : '';
                        if (!is_null($arg)) {
                            $paramValues .= "$arg";
                        } else {
                            $paramValues .= "NULL";
                        }
                    }
                    throw new RestException(417, "table Step ($paramValues)" );
                }
            }
        } catch (RestException $e) {
            throw new RestException($e->getCode(), $e->getMessage());
        } catch (Exception $e) {
            throw new RestException(412, $e->getMessage());
        }

        return $result;
    }

    /**
     * Implementation for 'POST' method for Rest API
     *
     * @param  mixed $stepUid Primary key
     *
     * @return array $result Returns array within multiple records or a single record depending if
     *                       a single selection was requested passing id(s) as param
     */
    protected function post($stepUid, $proUid, $tasUid, $stepTypeObj, $stepUidObj, $stepCondition, $stepPosition, $stepMode)
    {
        try {
            $result = array();
            $obj = new Step();

            $obj->setStepUid($stepUid);
            $obj->setProUid($proUid);
            $obj->setTasUid($tasUid);
            $obj->setStepTypeObj($stepTypeObj);
            $obj->setStepUidObj($stepUidObj);
            $obj->setStepCondition($stepCondition);
            $obj->setStepPosition($stepPosition);
            $obj->setStepMode($stepMode);

            $obj->save();
        } catch (Exception $e) {
            throw new RestException(412, $e->getMessage());
        }
    }

    /**
     * Implementation for 'PUT' method for Rest API
     *
     * @param  mixed $stepUid Primary key
     *
     * @return array $result Returns array within multiple records or a single record depending if
     *                       a single selection was requested passing id(s) as param
     */
    protected function put($stepUid, $proUid, $tasUid, $stepTypeObj, $stepUidObj, $stepCondition, $stepPosition, $stepMode)
    {
        try {
            $obj = StepPeer::retrieveByPK($stepUid);

            $obj->setProUid($proUid);
            $obj->setTasUid($tasUid);
            $obj->setStepTypeObj($stepTypeObj);
            $obj->setStepUidObj($stepUidObj);
            $obj->setStepCondition($stepCondition);
            $obj->setStepPosition($stepPosition);
            $obj->setStepMode($stepMode);

            $obj->save();
        } catch (Exception $e) {
            throw new RestException(412, $e->getMessage());
        }
    }

    /**
     * Implementation for 'DELETE' method for Rest API
     *
     * @param  mixed $stepUid Primary key
     *
     * @return array $result Returns array within multiple records or a single record depending if
     *                       a single selection was requested passing id(s) as param
     */
    protected function delete($stepUid)
    {
        $conn = Propel::getConnection(StepPeer::DATABASE_NAME);

        try {
            $conn->begin();

            $obj = StepPeer::retrieveByPK($stepUid);
            if (! is_object($obj)) {
                throw new RestException(412, G::LoadTranslation('ID_RECORD_DOES_NOT_EXIST'));
            }
            $obj->delete();

            $conn->commit();
        } catch (Exception $e) {
            $conn->rollback();
            throw new RestException(412, $e->getMessage());
        }
    }


}
