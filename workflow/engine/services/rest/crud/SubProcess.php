<?php

class Services_Rest_SubProcess
{
    /**
     * Implementation for 'GET' method for Rest API
     *
     * @param  mixed $spUid Primary key
     *
     * @return array $result Returns array within multiple records or a single record depending if
     *                       a single selection was requested passing id(s) as param
     */
    protected function get($spUid=null)
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

                $criteria->addSelectColumn(SubProcessPeer::SP_UID);
                $criteria->addSelectColumn(SubProcessPeer::PRO_UID);
                $criteria->addSelectColumn(SubProcessPeer::TAS_UID);
                $criteria->addSelectColumn(SubProcessPeer::PRO_PARENT);
                $criteria->addSelectColumn(SubProcessPeer::TAS_PARENT);
                $criteria->addSelectColumn(SubProcessPeer::SP_TYPE);
                $criteria->addSelectColumn(SubProcessPeer::SP_SYNCHRONOUS);
                $criteria->addSelectColumn(SubProcessPeer::SP_SYNCHRONOUS_TYPE);
                $criteria->addSelectColumn(SubProcessPeer::SP_SYNCHRONOUS_WAIT);
                $criteria->addSelectColumn(SubProcessPeer::SP_VARIABLES_OUT);
                $criteria->addSelectColumn(SubProcessPeer::SP_VARIABLES_IN);
                $criteria->addSelectColumn(SubProcessPeer::SP_GRID_IN);

                $dataset = AppEventPeer::doSelectRS($criteria);
                $dataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);

                while ($dataset->next()) {
                    $result[] = $dataset->getRow();
                }
            } else {
                $record = SubProcessPeer::retrieveByPK($spUid);
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
                    throw new RestException(417, "table SubProcess ($paramValues)" );
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
     * @param  mixed $spUid Primary key
     *
     * @return array $result Returns array within multiple records or a single record depending if
     *                       a single selection was requested passing id(s) as param
     */
    protected function post($spUid, $proUid, $tasUid, $proParent, $tasParent, $spType, $spSynchronous, $spSynchronousType, $spSynchronousWait, $spVariablesOut, $spVariablesIn, $spGridIn)
    {
        try {
            $result = array();
            $obj = new SubProcess();

            $obj->setSpUid($spUid);
            $obj->setProUid($proUid);
            $obj->setTasUid($tasUid);
            $obj->setProParent($proParent);
            $obj->setTasParent($tasParent);
            $obj->setSpType($spType);
            $obj->setSpSynchronous($spSynchronous);
            $obj->setSpSynchronousType($spSynchronousType);
            $obj->setSpSynchronousWait($spSynchronousWait);
            $obj->setSpVariablesOut($spVariablesOut);
            $obj->setSpVariablesIn($spVariablesIn);
            $obj->setSpGridIn($spGridIn);

            $obj->save();
        } catch (Exception $e) {
            throw new RestException(412, $e->getMessage());
        }
    }

    /**
     * Implementation for 'PUT' method for Rest API
     *
     * @param  mixed $spUid Primary key
     *
     * @return array $result Returns array within multiple records or a single record depending if
     *                       a single selection was requested passing id(s) as param
     */
    protected function put($spUid, $proUid, $tasUid, $proParent, $tasParent, $spType, $spSynchronous, $spSynchronousType, $spSynchronousWait, $spVariablesOut, $spVariablesIn, $spGridIn)
    {
        try {
            $obj = SubProcessPeer::retrieveByPK($spUid);

            $obj->setProUid($proUid);
            $obj->setTasUid($tasUid);
            $obj->setProParent($proParent);
            $obj->setTasParent($tasParent);
            $obj->setSpType($spType);
            $obj->setSpSynchronous($spSynchronous);
            $obj->setSpSynchronousType($spSynchronousType);
            $obj->setSpSynchronousWait($spSynchronousWait);
            $obj->setSpVariablesOut($spVariablesOut);
            $obj->setSpVariablesIn($spVariablesIn);
            $obj->setSpGridIn($spGridIn);

            $obj->save();
        } catch (Exception $e) {
            throw new RestException(412, $e->getMessage());
        }
    }

    /**
     * Implementation for 'DELETE' method for Rest API
     *
     * @param  mixed $spUid Primary key
     *
     * @return array $result Returns array within multiple records or a single record depending if
     *                       a single selection was requested passing id(s) as param
     */
    protected function delete($spUid)
    {
        $conn = Propel::getConnection(SubProcessPeer::DATABASE_NAME);

        try {
            $conn->begin();

            $obj = SubProcessPeer::retrieveByPK($spUid);
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
