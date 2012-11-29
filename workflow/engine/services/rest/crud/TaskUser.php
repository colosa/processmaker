<?php

class Services_Rest_TaskUser
{
    /**
     * Implementation for 'GET' method for Rest API
     *
     * @param  mixed $tasUid, $usrUid, $tuType, $tuRelation Primary key
     *
     * @return array $result Returns array within multiple records or a single record depending if
     *                       a single selection was requested passing id(s) as param
     */
    protected function get($tasUid=null, $usrUid=null, $tuType=null, $tuRelation=null)
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

                $criteria->addSelectColumn(TaskUserPeer::TAS_UID);
                $criteria->addSelectColumn(TaskUserPeer::USR_UID);
                $criteria->addSelectColumn(TaskUserPeer::TU_TYPE);
                $criteria->addSelectColumn(TaskUserPeer::TU_RELATION);
                
                $dataset = AppEventPeer::doSelectRS($criteria);
                $dataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);

                while ($dataset->next()) {
                    $result[] = $dataset->getRow();
                }
            } else {
                $record = TaskUserPeer::retrieveByPK($tasUid, $usrUid, $tuType, $tuRelation);
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
                    throw new RestException(417, "table TaskUser ($paramValues)" );
                }
            }
        } catch (RestException $e) {
            throw new RestException($e->getCode(), $e->getMessage());
        } catch (Exception $e) {
            throw new RestException(412, $e->getMessage());
        }

        return $result;
    }


}
