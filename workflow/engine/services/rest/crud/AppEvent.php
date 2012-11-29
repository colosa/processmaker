<?php

class Services_Rest_AppEvent
{
    /**
     * Implementation for 'GET' method for Rest API
     *
     * @param  mixed $appUid, $delIndex, $evnUid Primary key
     *
     * @return array $result Returns array within multiple records or a single record depending if
     *                       a single selection was requested passing id(s) as param
     */
    protected function get($appUid=null, $delIndex=null, $evnUid=null)
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

                $criteria->addSelectColumn(AppEventPeer::APP_UID);
                $criteria->addSelectColumn(AppEventPeer::DEL_INDEX);
                $criteria->addSelectColumn(AppEventPeer::EVN_UID);
                $criteria->addSelectColumn(AppEventPeer::APP_EVN_ACTION_DATE);
                $criteria->addSelectColumn(AppEventPeer::APP_EVN_ATTEMPTS);
                $criteria->addSelectColumn(AppEventPeer::APP_EVN_LAST_EXECUTION_DATE);
                $criteria->addSelectColumn(AppEventPeer::APP_EVN_STATUS);
                
                $dataset = AppEventPeer::doSelectRS($criteria);
                $dataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);

                while ($dataset->next()) {
                    $result[] = $dataset->getRow();
                }
            } else {
                $record = AppEventPeer::retrieveByPK($appUid, $delIndex, $evnUid);
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
                    throw new RestException(417, "table AppEvent ($paramValues)" );
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
