<?php

class Services_Rest_CalendarDefinition
{
    /**
     * Implementation for 'GET' method for Rest API
     *
     * @param  mixed $calendarUid Primary key
     *
     * @return array $result Returns array within multiple records or a single record depending if
     *                       a single selection was requested passing id(s) as param
     */
    protected function get($calendarUid=null)
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

                $criteria->addSelectColumn(CalendarDefinitionPeer::CALENDAR_UID);
                $criteria->addSelectColumn(CalendarDefinitionPeer::CALENDAR_NAME);
                $criteria->addSelectColumn(CalendarDefinitionPeer::CALENDAR_CREATE_DATE);
                $criteria->addSelectColumn(CalendarDefinitionPeer::CALENDAR_UPDATE_DATE);
                $criteria->addSelectColumn(CalendarDefinitionPeer::CALENDAR_WORK_DAYS);
                $criteria->addSelectColumn(CalendarDefinitionPeer::CALENDAR_DESCRIPTION);
                $criteria->addSelectColumn(CalendarDefinitionPeer::CALENDAR_STATUS);
                
                $dataset = AppEventPeer::doSelectRS($criteria);
                $dataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);

                while ($dataset->next()) {
                    $result[] = $dataset->getRow();
                }
            } else {
                $record = CalendarDefinitionPeer::retrieveByPK($calendarUid);
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
                    throw new RestException(417, "table CalendarDefinition ($paramValues)" );
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
