<?php

class Services_Rest_AppCacheView
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

                $criteria->addSelectColumn(AppCacheViewPeer::APP_UID);
                $criteria->addSelectColumn(AppCacheViewPeer::DEL_INDEX);
                $criteria->addSelectColumn(AppCacheViewPeer::APP_NUMBER);
                $criteria->addSelectColumn(AppCacheViewPeer::APP_STATUS);
                $criteria->addSelectColumn(AppCacheViewPeer::USR_UID);
                $criteria->addSelectColumn(AppCacheViewPeer::PREVIOUS_USR_UID);
                $criteria->addSelectColumn(AppCacheViewPeer::TAS_UID);
                $criteria->addSelectColumn(AppCacheViewPeer::PRO_UID);
                $criteria->addSelectColumn(AppCacheViewPeer::DEL_DELEGATE_DATE);
                $criteria->addSelectColumn(AppCacheViewPeer::DEL_INIT_DATE);
                $criteria->addSelectColumn(AppCacheViewPeer::DEL_TASK_DUE_DATE);
                $criteria->addSelectColumn(AppCacheViewPeer::DEL_FINISH_DATE);
                $criteria->addSelectColumn(AppCacheViewPeer::DEL_THREAD_STATUS);
                $criteria->addSelectColumn(AppCacheViewPeer::APP_THREAD_STATUS);
                $criteria->addSelectColumn(AppCacheViewPeer::APP_TITLE);
                $criteria->addSelectColumn(AppCacheViewPeer::APP_PRO_TITLE);
                $criteria->addSelectColumn(AppCacheViewPeer::APP_TAS_TITLE);
                $criteria->addSelectColumn(AppCacheViewPeer::APP_CURRENT_USER);
                $criteria->addSelectColumn(AppCacheViewPeer::APP_DEL_PREVIOUS_USER);
                $criteria->addSelectColumn(AppCacheViewPeer::DEL_PRIORITY);
                $criteria->addSelectColumn(AppCacheViewPeer::DEL_DURATION);
                $criteria->addSelectColumn(AppCacheViewPeer::DEL_QUEUE_DURATION);
                $criteria->addSelectColumn(AppCacheViewPeer::DEL_DELAY_DURATION);
                $criteria->addSelectColumn(AppCacheViewPeer::DEL_STARTED);
                $criteria->addSelectColumn(AppCacheViewPeer::DEL_FINISHED);
                $criteria->addSelectColumn(AppCacheViewPeer::DEL_DELAYED);
                $criteria->addSelectColumn(AppCacheViewPeer::APP_CREATE_DATE);
                $criteria->addSelectColumn(AppCacheViewPeer::APP_FINISH_DATE);
                $criteria->addSelectColumn(AppCacheViewPeer::APP_UPDATE_DATE);
                $criteria->addSelectColumn(AppCacheViewPeer::APP_OVERDUE_PERCENTAGE);
                
                $dataset = AppEventPeer::doSelectRS($criteria);
                $dataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);

                while ($dataset->next()) {
                    $result[] = $dataset->getRow();
                }
            } else {
                $record = AppCacheViewPeer::retrieveByPK($appUid, $delIndex);
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
                    throw new RestException(417, "table AppCacheView ($paramValues)" );
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
