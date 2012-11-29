<?php

class Services_Rest_CalendarBusinessHours
{
    /**
     * Implementation for 'GET' method for Rest API
     *
     * @param  mixed $calendarUid, $calendarBusinessDay, $calendarBusinessStart, $calendarBusinessEnd Primary key
     *
     * @return array $result Returns array within multiple records or a single record depending if
     *                       a single selection was requested passing id(s) as param
     */
    protected function get($calendarUid=null, $calendarBusinessDay=null, $calendarBusinessStart=null, $calendarBusinessEnd=null)
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

                $criteria->addSelectColumn(CalendarBusinessHoursPeer::CALENDAR_UID);
                $criteria->addSelectColumn(CalendarBusinessHoursPeer::CALENDAR_BUSINESS_DAY);
                $criteria->addSelectColumn(CalendarBusinessHoursPeer::CALENDAR_BUSINESS_START);
                $criteria->addSelectColumn(CalendarBusinessHoursPeer::CALENDAR_BUSINESS_END);
                
                $dataset = AppEventPeer::doSelectRS($criteria);
                $dataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);

                while ($dataset->next()) {
                    $result[] = $dataset->getRow();
                }
            } else {
                $record = CalendarBusinessHoursPeer::retrieveByPK($calendarUid, $calendarBusinessDay, $calendarBusinessStart, $calendarBusinessEnd);
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
                    throw new RestException(417, "table CalendarBusinessHours ($paramValues)" );
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
