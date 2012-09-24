<?php

class Services_Rest_Event
{
    /**
     * Implementation for 'GET' method for Rest API
     *
     * @param  mixed $evnUid Primary key
     *
     * @return array $result Returns array within multiple records or a single record depending if
     *                       a single selection was requested passing id(s) as param
     */
    protected function get($evnUid=null)
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

                $criteria->addSelectColumn(EventPeer::EVN_UID);
                $criteria->addSelectColumn(EventPeer::PRO_UID);
                $criteria->addSelectColumn(EventPeer::EVN_STATUS);
                $criteria->addSelectColumn(EventPeer::EVN_WHEN_OCCURS);
                $criteria->addSelectColumn(EventPeer::EVN_RELATED_TO);
                $criteria->addSelectColumn(EventPeer::TAS_UID);
                $criteria->addSelectColumn(EventPeer::EVN_TAS_UID_FROM);
                $criteria->addSelectColumn(EventPeer::EVN_TAS_UID_TO);
                $criteria->addSelectColumn(EventPeer::EVN_TAS_ESTIMATED_DURATION);
                $criteria->addSelectColumn(EventPeer::EVN_TIME_UNIT);
                $criteria->addSelectColumn(EventPeer::EVN_WHEN);
                $criteria->addSelectColumn(EventPeer::EVN_MAX_ATTEMPTS);
                $criteria->addSelectColumn(EventPeer::EVN_ACTION);
                $criteria->addSelectColumn(EventPeer::EVN_CONDITIONS);
                $criteria->addSelectColumn(EventPeer::EVN_ACTION_PARAMETERS);
                $criteria->addSelectColumn(EventPeer::TRI_UID);
                $criteria->addSelectColumn(EventPeer::EVN_POSX);
                $criteria->addSelectColumn(EventPeer::EVN_POSY);
                $criteria->addSelectColumn(EventPeer::EVN_TYPE);
                $criteria->addSelectColumn(EventPeer::TAS_EVN_UID);
                
                $dataset = AppEventPeer::doSelectRS($criteria);
                $dataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);

                while ($dataset->next()) {
                    $result[] = $dataset->getRow();
                }
            } else {
                $record = EventPeer::retrieveByPK($evnUid);
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
                    throw new RestException(417, "table Event ($paramValues)" );
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
