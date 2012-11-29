<?php

class Services_Rest_CaseTrackerObject
{
    /**
     * Implementation for 'GET' method for Rest API
     *
     * @param  mixed $ctoUid Primary key
     *
     * @return array $result Returns array within multiple records or a single record depending if
     *                       a single selection was requested passing id(s) as param
     */
    protected function get($ctoUid=null)
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

                $criteria->addSelectColumn(CaseTrackerObjectPeer::CTO_UID);
                $criteria->addSelectColumn(CaseTrackerObjectPeer::PRO_UID);
                $criteria->addSelectColumn(CaseTrackerObjectPeer::CTO_TYPE_OBJ);
                $criteria->addSelectColumn(CaseTrackerObjectPeer::CTO_UID_OBJ);
                $criteria->addSelectColumn(CaseTrackerObjectPeer::CTO_CONDITION);
                $criteria->addSelectColumn(CaseTrackerObjectPeer::CTO_POSITION);
                
                $dataset = AppEventPeer::doSelectRS($criteria);
                $dataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);

                while ($dataset->next()) {
                    $result[] = $dataset->getRow();
                }
            } else {
                $record = CaseTrackerObjectPeer::retrieveByPK($ctoUid);
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
                    throw new RestException(417, "table CaseTrackerObject ($paramValues)" );
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
