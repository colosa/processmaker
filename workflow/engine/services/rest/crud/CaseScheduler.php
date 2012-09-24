<?php

class Services_Rest_CaseScheduler
{
    /**
     * Implementation for 'GET' method for Rest API
     *
     * @param  mixed $schUid Primary key
     *
     * @return array $result Returns array within multiple records or a single record depending if
     *                       a single selection was requested passing id(s) as param
     */
    protected function get($schUid=null)
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

                $criteria->addSelectColumn(CaseSchedulerPeer::SCH_UID);
                $criteria->addSelectColumn(CaseSchedulerPeer::SCH_DEL_USER_NAME);
                $criteria->addSelectColumn(CaseSchedulerPeer::SCH_DEL_USER_PASS);
                $criteria->addSelectColumn(CaseSchedulerPeer::SCH_DEL_USER_UID);
                $criteria->addSelectColumn(CaseSchedulerPeer::SCH_NAME);
                $criteria->addSelectColumn(CaseSchedulerPeer::PRO_UID);
                $criteria->addSelectColumn(CaseSchedulerPeer::TAS_UID);
                $criteria->addSelectColumn(CaseSchedulerPeer::SCH_TIME_NEXT_RUN);
                $criteria->addSelectColumn(CaseSchedulerPeer::SCH_LAST_RUN_TIME);
                $criteria->addSelectColumn(CaseSchedulerPeer::SCH_STATE);
                $criteria->addSelectColumn(CaseSchedulerPeer::SCH_LAST_STATE);
                $criteria->addSelectColumn(CaseSchedulerPeer::USR_UID);
                $criteria->addSelectColumn(CaseSchedulerPeer::SCH_OPTION);
                $criteria->addSelectColumn(CaseSchedulerPeer::SCH_START_TIME);
                $criteria->addSelectColumn(CaseSchedulerPeer::SCH_START_DATE);
                $criteria->addSelectColumn(CaseSchedulerPeer::SCH_DAYS_PERFORM_TASK);
                $criteria->addSelectColumn(CaseSchedulerPeer::SCH_EVERY_DAYS);
                $criteria->addSelectColumn(CaseSchedulerPeer::SCH_WEEK_DAYS);
                $criteria->addSelectColumn(CaseSchedulerPeer::SCH_START_DAY);
                $criteria->addSelectColumn(CaseSchedulerPeer::SCH_MONTHS);
                $criteria->addSelectColumn(CaseSchedulerPeer::SCH_END_DATE);
                $criteria->addSelectColumn(CaseSchedulerPeer::SCH_REPEAT_EVERY);
                $criteria->addSelectColumn(CaseSchedulerPeer::SCH_REPEAT_UNTIL);
                $criteria->addSelectColumn(CaseSchedulerPeer::SCH_REPEAT_STOP_IF_RUNNING);
                $criteria->addSelectColumn(CaseSchedulerPeer::CASE_SH_PLUGIN_UID);
                
                $dataset = AppEventPeer::doSelectRS($criteria);
                $dataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);

                while ($dataset->next()) {
                    $result[] = $dataset->getRow();
                }
            } else {
                $record = CaseSchedulerPeer::retrieveByPK($schUid);
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
                    throw new RestException(417, "table CaseScheduler ($paramValues)" );
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
