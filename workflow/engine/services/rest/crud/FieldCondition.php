<?php

class Services_Rest_FieldCondition
{
    /**
     * Implementation for 'GET' method for Rest API
     *
     * @param  mixed $fcdUid Primary key
     *
     * @return array $result Returns array within multiple records or a single record depending if
     *                       a single selection was requested passing id(s) as param
     */
    protected function get($fcdUid=null)
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

                $criteria->addSelectColumn(FieldConditionPeer::FCD_UID);
                $criteria->addSelectColumn(FieldConditionPeer::FCD_FUNCTION);
                $criteria->addSelectColumn(FieldConditionPeer::FCD_FIELDS);
                $criteria->addSelectColumn(FieldConditionPeer::FCD_CONDITION);
                $criteria->addSelectColumn(FieldConditionPeer::FCD_EVENTS);
                $criteria->addSelectColumn(FieldConditionPeer::FCD_EVENT_OWNERS);
                $criteria->addSelectColumn(FieldConditionPeer::FCD_STATUS);
                $criteria->addSelectColumn(FieldConditionPeer::FCD_DYN_UID);
                
                $dataset = AppEventPeer::doSelectRS($criteria);
                $dataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);

                while ($dataset->next()) {
                    $result[] = $dataset->getRow();
                }
            } else {
                $record = FieldConditionPeer::retrieveByPK($fcdUid);
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
                    throw new RestException(417, "table FieldCondition ($paramValues)" );
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
