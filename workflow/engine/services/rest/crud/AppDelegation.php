<?php

class Services_Rest_AppDelegation
{
    /**
     * Implementation for 'GET' method for Rest API
     *
     * @param  mixed $appUid, $delIndex Primary key
     *
     * @return array $result Returns array within multiple records or a single record depending if
     *                       a single selection was requested passing id(s) as param
     */
    protected function get($appUid=null, $delIndex=null)
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

                $criteria->addSelectColumn(AppDelegationPeer::APP_UID);
                $criteria->addSelectColumn(AppDelegationPeer::DEL_INDEX);
                $criteria->addSelectColumn(AppDelegationPeer::DEL_PREVIOUS);
                $criteria->addSelectColumn(AppDelegationPeer::PRO_UID);
                $criteria->addSelectColumn(AppDelegationPeer::TAS_UID);
                $criteria->addSelectColumn(AppDelegationPeer::USR_UID);
                $criteria->addSelectColumn(AppDelegationPeer::DEL_TYPE);
                $criteria->addSelectColumn(AppDelegationPeer::DEL_THREAD);
                $criteria->addSelectColumn(AppDelegationPeer::DEL_THREAD_STATUS);
                $criteria->addSelectColumn(AppDelegationPeer::DEL_PRIORITY);
                $criteria->addSelectColumn(AppDelegationPeer::DEL_DELEGATE_DATE);
                $criteria->addSelectColumn(AppDelegationPeer::DEL_INIT_DATE);
                $criteria->addSelectColumn(AppDelegationPeer::DEL_TASK_DUE_DATE);
                $criteria->addSelectColumn(AppDelegationPeer::DEL_FINISH_DATE);
                $criteria->addSelectColumn(AppDelegationPeer::DEL_DURATION);
                $criteria->addSelectColumn(AppDelegationPeer::DEL_QUEUE_DURATION);
                $criteria->addSelectColumn(AppDelegationPeer::DEL_DELAY_DURATION);
                $criteria->addSelectColumn(AppDelegationPeer::DEL_STARTED);
                $criteria->addSelectColumn(AppDelegationPeer::DEL_FINISHED);
                $criteria->addSelectColumn(AppDelegationPeer::DEL_DELAYED);
                $criteria->addSelectColumn(AppDelegationPeer::DEL_DATA);
                $criteria->addSelectColumn(AppDelegationPeer::APP_OVERDUE_PERCENTAGE);
                
                $dataset = AppEventPeer::doSelectRS($criteria);
                $dataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);

                while ($dataset->next()) {
                    $result[] = $dataset->getRow();
                }
            } else {
                $record = AppDelegationPeer::retrieveByPK($appUid, $delIndex);
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
                    throw new RestException(417, "table AppDelegation ($paramValues)" );
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
