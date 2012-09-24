<?php

class Services_Rest_AppDelay
{
    /**
     * Implementation for 'GET' method for Rest API
     *
     * @param  mixed $appDelayUid Primary key
     *
     * @return array $result Returns array within multiple records or a single record depending if
     *                       a single selection was requested passing id(s) as param
     */
    protected function get($appDelayUid=null)
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

                $criteria->addSelectColumn(AppDelayPeer::APP_DELAY_UID);
                $criteria->addSelectColumn(AppDelayPeer::PRO_UID);
                $criteria->addSelectColumn(AppDelayPeer::APP_UID);
                $criteria->addSelectColumn(AppDelayPeer::APP_THREAD_INDEX);
                $criteria->addSelectColumn(AppDelayPeer::APP_DEL_INDEX);
                $criteria->addSelectColumn(AppDelayPeer::APP_TYPE);
                $criteria->addSelectColumn(AppDelayPeer::APP_STATUS);
                $criteria->addSelectColumn(AppDelayPeer::APP_NEXT_TASK);
                $criteria->addSelectColumn(AppDelayPeer::APP_DELEGATION_USER);
                $criteria->addSelectColumn(AppDelayPeer::APP_ENABLE_ACTION_USER);
                $criteria->addSelectColumn(AppDelayPeer::APP_ENABLE_ACTION_DATE);
                $criteria->addSelectColumn(AppDelayPeer::APP_DISABLE_ACTION_USER);
                $criteria->addSelectColumn(AppDelayPeer::APP_DISABLE_ACTION_DATE);
                $criteria->addSelectColumn(AppDelayPeer::APP_AUTOMATIC_DISABLED_DATE);
                
                $dataset = AppEventPeer::doSelectRS($criteria);
                $dataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);

                while ($dataset->next()) {
                    $result[] = $dataset->getRow();
                }
            } else {
                $record = AppDelayPeer::retrieveByPK($appDelayUid);
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
                    throw new RestException(417, "table AppDelay ($paramValues)" );
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
